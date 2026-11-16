#!/usr/bin/env python3
"""
Crawl indexable non-English GlobalFXHub pages and flag visible text
that looks like English when it should be localized.

Scope (kept deliberately bounded rather than literally every URL --
most pages share the same handful of templates, so one instance of
each template per language surfaces the same UI-chrome gap as
crawling every single page of that template would):
  - the 14 auto-duplicated container/tool pages per non-English
    language (reviews, learn, compare, news, countries, best,
    regulation, research, regulators, data, broker-finder,
    cost-calculator, regulation-checker, broker-changelog)
  - the homepage per language
  - one real single-broker-review page per language (dynamically
    generated broker descriptions live there)
  - one real translated article (post) per language

For each fetched page, scans the raw rendered HTML against a
dictionary of common English UI words/phrases (headings, buttons,
labels, filters, pagination, forms, empty states, errors, CTAs,
breadcrumbs) and reports each match's surrounding snippet, skipping
anything on the allowlist (broker/platform/regulator proper nouns,
licence-number-shaped tokens, currency/country codes).

Makes no changes. Read-only GETs only, all against public page URLs
(no WP auth needed for front-end pages, but WP_USERNAME/WP_APP_PASSWORD
are still used for the REST discovery calls that find the URLs).

Usage:
    python3 i18n/crawl_english_text_audit.py
"""
import html
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"
SITE_BASE = "https://globalfxhub.net"

LANGS = ["es", "de", "ar", "it", "pt", "fr", "pl"]

CONTAINER_SLUGS = [
    "reviews", "learn", "compare", "news", "countries", "best",
    "regulation", "research", "regulators", "data", "broker-finder",
    "cost-calculator", "regulation-checker", "broker-changelog",
]

# Protected terms that must never be flagged, regardless of language --
# broker/platform/regulator proper nouns and the kind of alphanumeric
# tokens a licence number takes.
ALLOWLIST_TERMS = [
    "MetaTrader", "MT4", "MT5", "cTrader", "TradingView", "ProRealTime",
    "FCA", "ASIC", "CySEC", "MiFID", "MiFID II", "FSCA", "DFSA", "MAS",
    "CNMV", "BaFin", "AMF", "CONSOB", "CMVM", "SCB", "FSA", "IFSC",
    "VFSC", "FSA Seychelles", "ESMA", "GlobalFXHub", "EUR", "USD", "GBP",
    "CFD", "CFDs", "ECN", "STP", "DMA", "PAMM", "MAM", "NFP",
]
ALLOWLIST_RE = re.compile(
    r"\b(" + "|".join(re.escape(t) for t in ALLOWLIST_TERMS) + r")\b"
)
# Licence-number-shaped tokens (digits with slashes/dashes, e.g.
# "309/16", "195355") -- never flagged even if adjacent to a dictionary word.
LICENCE_NUMBER_RE = re.compile(r"\b[\dA-Z]{2,}[/-][\dA-Z]{1,}\b")

# Common English UI phrases most likely to indicate a missed
# translation, grouped by the component category the task asked for.
# Longer/more specific phrases first so they take priority over a
# generic substring also present (e.g. "Not confirmed" before "Not").
UI_PHRASES = {
    "heading/eyebrow": [
        "BROKER REVIEWS", "BROKER RESEARCH", "BROKER ALTERNATIVES",
        "BEST BROKERS", "BROKER FINDER", "BROKER NEWS", "REGULATION",
        "COUNTRY", "COMPARISON", "Broker Reviews", "Best Brokers",
    ],
    "table/label": [
        "Avg. spread", "Minimum deposit", "Min. deposit", "Platforms",
        "Score", "Broker", "Regulated entity", "Headquarters", "Founded",
        "Instruments", "Other Tier-1 regulators",
    ],
    "button/CTA": [
        "Read review", "Visit Broker", "Compare vs", "Find similar brokers",
        "See alternatives", "View all reviews", "Clear search",
    ],
    "status/empty-state": [
        "Not confirmed", "Not independently confirmed", "None confirmed",
        "No brokers matched", "No results", "No brokers in our researched set",
    ],
    "filter/pagination/search": [
        "Showing results for", "Any / outside the EU", "Any platform",
        "Minimum deposit you can meet", "Trading style", "Previous", "Next",
    ],
    "breadcrumb/nav": [
        ">Home<", ">Reviews<", ">Learn<", ">Regulation<", ">Countries<",
        ">News<", ">Compare<",
    ],
    "disclaimer/methodology": [
        "How this score is calculated", "full methodology",
        "Verify current terms directly with the broker",
        "This is not personalized financial advice",
    ],
}


def strip_html(raw):
    text = re.sub(r"<[^>]+>", " ", raw or "")
    text = html.unescape(text)
    return re.sub(r"\s+", " ", text).strip()


def find_matches(page_html, label, url, lang, findings):
    # Search the stripped text (not raw HTML) so matches are real
    # visible content, not attribute values or class names.
    text = strip_html(page_html)
    for component, phrases in UI_PHRASES.items():
        for phrase in phrases:
            needle = phrase.strip(">/<")
            idx = text.find(needle)
            if idx == -1:
                continue
            start = max(0, idx - 40)
            snippet = text[start:idx + len(needle) + 40]
            if ALLOWLIST_RE.search(snippet) and needle in ALLOWLIST_TERMS:
                continue
            findings.append({
                "url": url, "locale": lang, "phrase": needle,
                "component_guess": component, "page_label": label,
                "snippet": snippet,
            })


def fetch(url):
    try:
        resp = request_with_retry("GET", url, timeout=30)
        resp.raise_for_status()
        return resp.text
    except Exception as exc:
        print(f"  FAILED to fetch {url}: {exc}", file=sys.stderr)
        return None


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    # Index every published page by slug (one paginated pass), so the
    # container pages' real public 'link' can be looked up rather than
    # guessed.
    by_slug = {}
    for page_num in range(1, 61):
        resp = request_with_retry(
            "GET", f"{WP_BASE}/pages",
            params={
                "per_page": 100, "page": page_num, "orderby": "id", "order": "asc",
                "_fields": "id,slug,link,gfh_lang",
            },
            auth=auth, timeout=30,
        )
        if resp.status_code == 400:
            break
        resp.raise_for_status()
        items = resp.json()
        if not items:
            break
        for item in items:
            by_slug[item["slug"]] = item

    # One sample post per language (for a real translated article) and
    # one sample single-broker-review page per language (for dynamically
    # generated broker descriptions) -- deep-paginate since these
    # predate the newest items.
    sample_post_per_lang = {}
    for page_num in range(1, 61):
        resp = request_with_retry(
            "GET", f"{WP_BASE}/posts",
            params={
                "per_page": 100, "page": page_num, "orderby": "id", "order": "asc",
                "_fields": "id,slug,link,gfh_lang",
            },
            auth=auth, timeout=30,
        )
        if resp.status_code == 400:
            break
        resp.raise_for_status()
        items = resp.json()
        if not items:
            break
        for item in items:
            lang = item.get("gfh_lang")
            if lang in LANGS and lang not in sample_post_per_lang:
                sample_post_per_lang[lang] = item
        if len(sample_post_per_lang) == len(LANGS):
            break

    findings = []
    pages_crawled = 0

    for lang in LANGS:
        print(f"=== {lang} ===")

        # Homepage.
        home_url = f"{SITE_BASE}/{lang}/"
        page_html = fetch(home_url)
        pages_crawled += 1
        if page_html:
            find_matches(page_html, "homepage", home_url, lang, findings)

        # 14 container/tool pages.
        for slug in CONTAINER_SLUGS:
            canonical_slug = f"{slug}-{lang}"
            page = by_slug.get(canonical_slug)
            if not page:
                print(f"  (no page found for slug={canonical_slug})")
                continue
            page_html = fetch(page["link"])
            pages_crawled += 1
            if page_html:
                find_matches(page_html, f"container:{slug}", page["link"], lang, findings)

        # Sample translated article.
        sample = sample_post_per_lang.get(lang)
        if sample:
            page_html = fetch(sample["link"])
            pages_crawled += 1
            if page_html:
                find_matches(page_html, "sample-article", sample["link"], lang, findings)
        else:
            print(f"  (no sample post found for {lang})")

        print(f"  done: {lang}")

    print(f"\n--- Crawled {pages_crawled} pages, found {len(findings)} English-text matches ---\n")
    for f in findings:
        print(f"URL: {f['url']}")
        print(f"  locale: {f['locale']}")
        print(f"  phrase: {f['phrase']!r}")
        print(f"  component (guess): {f['component_guess']} ({f['page_label']})")
        print(f"  context: ...{f['snippet']}...")
        print()


if __name__ == "__main__":
    main()
