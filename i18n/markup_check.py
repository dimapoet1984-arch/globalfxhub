"""
Per-piece markup check, per docs/TRANSLATION_PLAYBOOK.md section 3.3
point 1: "same tags and attributes (ignoring alt/title/aria-label/
placeholder/href) as the original. Failing pieces are retried with a
note, then with the reviewer model." Point 3: "Last resort: same tags
in a different order."

Point 2 of that section (automatically re-wrapping a dropped <a> link
around its translated text by finding the glossary form) is NOT
implemented here -- it's a genuinely fuzzy text-matching problem, and
a wrong auto-repair (wrapping the wrong word) is worse than leaving a
piece flagged as failed for a human to look at. Known gap, not a
silent one: a piece that drops a link fails check_strict() and
check_order_relaxed() the same as any other markup mismatch, so it
still gets retried and, if it never recovers, reported rather than
shipped silently wrong (see translate.py's retry escalation).
"""
import re
from collections import Counter

_TAG_RE = re.compile(r"<(/?)([a-zA-Z][a-zA-Z0-9]*)\b([^>]*)>")
_ATTR_RE = re.compile(r"""([a-zA-Z][a-zA-Z0-9-]*)\s*=\s*("[^"]*"|'[^']*')""")

IGNORED_ATTRS = {"alt", "title", "aria-label", "placeholder", "href"}


def extract_tag_signature(html):
    """Returns an ordered list of (is_closing, tag_name, frozenset_of_(name,value)_pairs)
    for every tag in html. Attribute VALUES are compared too -- not
    just names -- for every attribute except IGNORED_ATTRS, so a tag
    kept but with its src/class/data-* VALUE silently altered is
    still caught; IGNORED_ATTRS are expected to change (translated
    alt/title text, or an href repointed in a later relink pass) and
    are dropped entirely from the signature, value and all."""
    signature = []
    for match in _TAG_RE.finditer(html):
        is_closing = bool(match.group(1))
        tag_name = match.group(2).lower()
        attrs = frozenset(
            (name.lower(), value[1:-1])
            for name, value in _ATTR_RE.findall(match.group(3))
            if name.lower() not in IGNORED_ATTRS
        )
        signature.append((is_closing, tag_name, attrs))
    return signature


def check_strict(original_html, translated_html):
    """Same tags, same attributes (minus IGNORED_ATTRS), same order."""
    return extract_tag_signature(original_html) == extract_tag_signature(translated_html)


def check_order_relaxed(original_html, translated_html):
    """Same tags and attributes as a multiset, order-independent --
    the playbook's explicit "last resort" (adjectives/links moving
    around each other is normal across languages with different word
    order)."""
    return Counter(extract_tag_signature(original_html)) == Counter(extract_tag_signature(translated_html))
