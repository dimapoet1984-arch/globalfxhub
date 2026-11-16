#!/usr/bin/env python3
"""
Read-only registry-freshness checker for the FCA (UK Financial Conduct
Authority) Firm Reference Numbers already on file for brokers reviewed
on this site (functions.php's broker data -- see
tools/extract_broker_registry_ids.php). For each broker that claims an
FCA licence, looks that FRN up on the FCA's own public Financial
Services Register API and flags anything that doesn't look like a
currently-authorised firm, for a human to then verify and (if needed)
write up as a broker-news item or correct the broker's profile.

This NEVER writes anything -- not to WordPress, not to functions.php,
not anywhere. It only prints a report. See inc/broker-news.php's own
header for why broker-news items stay fully hand-curated.

Why only FCA, not CySEC too: CySEC has no public API, just a
JS-rendered (Kentico CMS, ASP.NET WebForms) register page with no
discoverable data endpoint -- a real check would need browser
automation, and a real headless-browser request to that page got
blocked outright (a WAF-style block page, independent of which network
it came from -- a plain curl succeeded from the exact same IP that a
headless Chrome request got blocked from, confirmed 2026-10-30).
Working around a regulator's own anti-bot defenses isn't something
this tool does. CySEC license drift stays a fully manual check.
Seychelles FSA has no reliable per-license lookup of any kind (name-only
browsing, no status field) -- also manual-only, not attempted here.

Setup required before this can run for real:
  1. Free signup at https://register.fca.org.uk/Developer/s/ (self-service,
     no approval step reported) to get an API key.
  2. Set FCA_API_EMAIL (the email you registered with) and FCA_API_KEY
     (the key from your developer portal profile) as GitHub Actions
     secrets (see .github/workflows/check-fca-registry.yml) or local
     environment variables.
Until those are set, this script exits with a clear message rather
than failing confusingly.

Usage: python3 tools/check_fca_registry.py
"""
import json
import os
import subprocess
import sys
import time

import requests

FCA_API_BASE = "https://register.fca.org.uk/services/V0.1"

# Per community-documented usage of this API: roughly 10 requests per
# 10 seconds. Pausing between calls keeps this comfortably under that
# without needing real throttling logic for what's at most ~40-50 calls.
REQUEST_PAUSE_SECONDS = 1.0

# Statuses observed/documented as "this firm is currently fine" --
# anything else gets flagged for a human to look at. Deliberately a
# narrow allow-list rather than a deny-list: an unrecognized status
# value should be surfaced for review, not silently treated as OK.
OK_STATUSES = {"Authorised", "Registered"}

NO_LICENSE_PLACEHOLDERS = {None, "", "—", "-"}


def load_brokers_with_fca_numbers():
    script_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), "extract_broker_registry_ids.php")
    result = subprocess.run(["php", script_path], capture_output=True, text=True, check=True)
    brokers = json.loads(result.stdout)
    return [b for b in brokers if b.get("fca") not in NO_LICENSE_PLACEHOLDERS]


def check_firm(session, frn):
    """Returns (status_string_or_None, raw_response_dict_or_None, error_or_None)."""
    try:
        resp = session.get(f"{FCA_API_BASE}/Firm/{frn}", timeout=20)
    except requests.exceptions.RequestException as exc:
        return None, None, f"request failed: {exc}"

    if 404 == resp.status_code:
        return None, None, "FRN not found on the FCA register at all"
    if 200 != resp.status_code:
        return None, None, f"HTTP {resp.status_code}: {resp.text[:300]}"

    try:
        data = resp.json()
    except ValueError:
        return None, None, f"response wasn't valid JSON: {resp.text[:300]}"

    # The FCA API's exact field names weren't independently confirmed
    # before this was written (see this file's header) -- try the
    # documented common shape, but fall back to returning the raw
    # record so a human can see what's actually there rather than this
    # silently reporting "OK" or crashing on a shape mismatch.
    records = data.get("Data") if isinstance(data, dict) else None
    if not records or not isinstance(records, list):
        return None, data, "response JSON didn't have the expected 'Data' array -- see raw response"

    record = records[0]
    status = record.get("Status")
    if status is None:
        return None, data, "found a record but no 'Status' field -- see raw response"

    return status, data, None


def main():
    email = os.environ.get("FCA_API_EMAIL")
    key = os.environ.get("FCA_API_KEY")
    if not email or not key:
        print(
            "FCA_API_EMAIL and FCA_API_KEY must both be set (as GitHub Actions secrets or "
            "local env vars) before this can run. Get a free key at "
            "https://register.fca.org.uk/Developer/s/ -- see this file's module docstring.",
            file=sys.stderr,
        )
        sys.exit(1)

    brokers = load_brokers_with_fca_numbers()
    print(f"Checking {len(brokers)} broker(s) with an FCA number on file against the live FCA register...\n")

    session = requests.Session()
    session.headers.update({"X-Auth-Email": email, "X-Auth-Key": key})

    flagged = []
    unresolved = []

    for i, broker in enumerate(brokers):
        frn = broker["fca"]
        status, raw, error = check_firm(session, frn)

        if error:
            print(f"  ? {broker['name']} (FRN {frn}): could not determine status -- {error}")
            unresolved.append({"broker": broker["name"], "frn": frn, "error": error, "raw": raw})
        elif status in OK_STATUSES:
            print(f"  OK {broker['name']} (FRN {frn}): {status}")
        else:
            print(f"  ! FLAG {broker['name']} (FRN {frn}): status is {status!r} -- not in the known-OK list, needs a human look")
            flagged.append({"broker": broker["name"], "frn": frn, "status": status})

        if i < len(brokers) - 1:
            time.sleep(REQUEST_PAUSE_SECONDS)

    print("\n=== Summary ===")
    print(f"Checked: {len(brokers)}")
    print(f"Flagged for review: {len(flagged)}")
    for f in flagged:
        print(f"  - {f['broker']} (FRN {f['frn']}): {f['status']}")
    print(f"Unresolved (couldn't check): {len(unresolved)}")
    for u in unresolved:
        print(f"  - {u['broker']} (FRN {u['frn']}): {u['error']}")


if __name__ == "__main__":
    main()
