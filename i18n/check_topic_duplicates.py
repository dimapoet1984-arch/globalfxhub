#!/usr/bin/env python3
"""
Checks a proposed new content topic against every title already on
the site (Learn articles, broker-research articles, regulation
comparisons, research reports, and live gfh_comparison posts) and
flags close overlaps before any drafting work starts.

Built after two content-calendar items ("Order Types", "Negative
Balance Protection") turned out to substantially duplicate existing
Learn articles -- caught by hand mid-session, not by any process.
This is the process: run it BEFORE drafting, as step 10 of CLAUDE.md's
pre-publish checklist ("whether this is a genuine duplicate").

Sources checked (title text only, not full content -- a thorough
duplicate check still means reading the 3-5 relevant existing pages
CLAUDE.md's checklist calls for, this just narrows which pages those
likely are):
  - inc/learn-articles.php's globalfxhub_get_learn_articles() roster
    (slug + title + cluster, 109 entries at last count)
  - inc/broker-research-articles.php's
    globalfxhub_get_broker_research_articles() (title per entry)
  - inc/regulation-comparisons.php's
    globalfxhub_get_regulation_comparisons() (title per entry)
  - inc/research-reports.php's globalfxhub_get_research_reports()
    (title per entry)
  - Live gfh_comparison posts via the public, unauthenticated REST
    read endpoint (GET /wp-json/wp/v2/gfh-comparisons) -- best-effort;
    skipped with a warning if the site isn't reachable, since this
    script is meant to run before any drafting starts, with no other
    network dependency and no credentials required.

Scoring is deliberately simple: lowercase, strip a short stopword
list and punctuation, compare the resulting significant-word sets by
Jaccard similarity AND raw shared-word count. Order Types vs "Forex
Order Types Explained" shares {order, types} -- a small but highly
specific overlap that a pure Jaccard score can under-rank next to
longer titles sharing more common words, so both measures are shown
and either one tripping its threshold is enough to flag a result.

This is a title-overlap heuristic, not a content-similarity model --
it will miss a genuine duplicate that uses completely different
wording, and it will sometimes flag unrelated titles that happen to
share a common word. Treat its output as a shortlist of pages worth
actually reading, not a verdict.

Usage:
    python3 i18n/check_topic_duplicates.py "Order Types Every New Trader Actually Needs"
    python3 i18n/check_topic_duplicates.py "DFSA vs FCA" --keywords "Dubai,regulator,leverage"
    python3 i18n/check_topic_duplicates.py "Some New Report" --no-network
"""
import argparse
import json
import re
import sys
import urllib.error
import urllib.request

REPO_ROOT = __file__.rsplit("/i18n/", 1)[0] if "/i18n/" in __file__ else "."

STOPWORDS = {
    "a", "an", "the", "is", "are", "was", "were", "be", "been", "being",
    "of", "for", "in", "on", "at", "to", "from", "by", "with", "without",
    "and", "or", "but", "if", "than", "then", "that", "this", "these", "those",
    "what", "which", "who", "whom", "how", "why", "when", "where",
    "do", "does", "did", "doing", "can", "could", "should", "would", "will",
    "vs", "versus", "compared", "comparing", "comparison", "compare",
    "explained", "explaining", "explain", "guide", "report", "actually",
    "really", "your", "you", "our", "we", "it", "its", "their", "his", "her",
    "about", "into", "over", "under", "across", "between", "against",
    "us", "not", "no", "yes", "all", "every", "each", "any", "some",
    "2026", "2025", "2024",
}

PHP_SOURCES = [
    ("inc/learn-articles.php", "globalfxhub_get_learn_articles", "learn"),
    ("inc/broker-research-articles.php", "globalfxhub_get_broker_research_articles", "broker-research"),
    ("inc/regulation-comparisons.php", "globalfxhub_get_regulation_comparisons", "regulation-comparison"),
    ("inc/research-reports.php", "globalfxhub_get_research_reports", "research-report"),
]

TITLE_RE = re.compile(r"""'title'\s*=>\s*(?:"((?:[^"\\]|\\.)*)"|'((?:[^'\\]|\\.)*)')""")


def unescape_php_single(s):
    return s.replace("\\'", "'").replace("\\\\", "\\")


def extract_function_body(php_text, function_name):
    """Returns the text from `function {function_name}(` up to the next
    top-level `function ` declaration (or end of file)."""
    start_match = re.search(r"^function " + re.escape(function_name) + r"\s*\(", php_text, re.M)
    if not start_match:
        return None
    start = start_match.start()
    next_match = re.search(r"^function ", php_text[start_match.end():], re.M)
    end = start_match.end() + next_match.start() if next_match else len(php_text)
    return php_text[start:end]


def load_php_titles():
    results = []
    for rel_path, func_name, category in PHP_SOURCES:
        path = f"{REPO_ROOT}/{rel_path}"
        try:
            with open(path, encoding="utf-8") as f:
                text = f.read()
        except OSError as exc:
            print(f"  warning: could not read {rel_path}: {exc}", file=sys.stderr)
            continue
        body = extract_function_body(text, func_name)
        if body is None:
            print(f"  warning: could not find {func_name}() in {rel_path}", file=sys.stderr)
            continue
        for m in TITLE_RE.finditer(body):
            raw = m.group(1) if m.group(1) is not None else m.group(2)
            title = unescape_php_single(raw)
            results.append((category, title))
    return results


def load_live_comparison_titles(timeout=10):
    url = "https://globalfxhub.net/wp-json/wp/v2/gfh-comparisons?per_page=100&_fields=slug,title"
    try:
        with urllib.request.urlopen(url, timeout=timeout) as resp:
            data = json.loads(resp.read().decode("utf-8"))
        return [("comparison", item["title"]["rendered"]) for item in data]
    except (urllib.error.URLError, OSError, ValueError, KeyError) as exc:
        print(f"  warning: could not fetch live gfh_comparison posts ({exc}) -- "
              f"continuing without them", file=sys.stderr)
        return []


def tokenize(text):
    words = re.findall(r"[a-z0-9]+", text.lower())
    return {w for w in words if w not in STOPWORDS and len(w) > 1}


def score(candidate_tokens, existing_tokens):
    if not candidate_tokens or not existing_tokens:
        return 0.0, 0
    shared = candidate_tokens & existing_tokens
    union = candidate_tokens | existing_tokens
    jaccard = len(shared) / len(union) if union else 0.0
    return jaccard, len(shared)


def main():
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("title", help="The proposed new content's working title")
    parser.add_argument("--keywords", default="", help="Comma-separated extra keywords to fold into the check")
    parser.add_argument("--no-network", action="store_true", help="Skip the live gfh_comparison REST lookup")
    parser.add_argument("--top", type=int, default=10, help="How many top matches to print (default 10)")
    args = parser.parse_args()

    candidate_text = args.title
    if args.keywords:
        candidate_text += " " + args.keywords.replace(",", " ")
    candidate_tokens = tokenize(candidate_text)

    if not candidate_tokens:
        print("No significant words found in the title/keywords given -- nothing to check against.")
        sys.exit(1)

    print(f"Checking: \"{args.title}\"" + (f" (+ keywords: {args.keywords})" if args.keywords else ""))
    print(f"Significant words: {', '.join(sorted(candidate_tokens))}\n")

    existing = load_php_titles()
    if not args.no_network:
        existing += load_live_comparison_titles()

    scored = []
    for category, title in existing:
        existing_tokens = tokenize(title)
        jaccard, shared_count = score(candidate_tokens, existing_tokens)
        if shared_count > 0:
            scored.append((jaccard, shared_count, category, title))

    scored.sort(key=lambda r: (r[1], r[0]), reverse=True)

    JACCARD_FLAG = 0.30
    SHARED_FLAG = 2

    flagged = [r for r in scored if r[0] >= JACCARD_FLAG or r[1] >= SHARED_FLAG]

    if not flagged:
        print(f"No close title overlaps found across {len(existing)} existing titles checked.")
        print("Still worth reading CLAUDE.md's 3-5 relevant existing pages by hand before publishing --")
        print("this only checks title-level overlap, not full content.")
    else:
        print(f"POTENTIAL OVERLAP -- {len(flagged)} existing title(s) share significant words "
              f"(checked against {len(existing)} total):\n")
        for jaccard, shared_count, category, title in scored[: args.top]:
            marker = "  !! " if (jaccard >= JACCARD_FLAG or shared_count >= SHARED_FLAG) else "     "
            print(f"{marker}[{category}] \"{title}\"  (shared words: {shared_count}, jaccard: {jaccard:.2f})")
        print("\nRead the flagged page(s) before drafting. A shared word or two doesn't always mean a real")
        print("duplicate (e.g. two pieces both mentioning \"broker\" or \"regulation\"), but every title above")
        print("the threshold is worth a deliberate look, not a skip.")

    if scored and not flagged:
        print(f"\n(Weakest overlaps below the flag threshold, for reference:)")
        for jaccard, shared_count, category, title in scored[: min(5, len(scored))]:
            print(f"      [{category}] \"{title}\"  (shared words: {shared_count}, jaccard: {jaccard:.2f})")


if __name__ == "__main__":
    main()
