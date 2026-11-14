"""
Slug transliteration per docs/TRANSLATION_PLAYBOOK.md section 3.3
point 6: "transliterate (NFKD -> ASCII), never strip accented letters
('tarô' became 'tar')." unicodedata's NFKD decomposition separates a
base letter from its combining accent mark (e.g. 'o' + combining
circumflex), so encoding to ASCII with errors="ignore" drops only the
accent mark byte, keeping the base letter -- "tarô" -> "taro", not
"tar". Non-Latin scripts (Arabic) have no Latin base-letter
decomposition at all, so NFKD does nothing useful for them -- see
transliterate_slug()'s fallback below.
"""
import hashlib
import re
import unicodedata


def transliterate_slug(text, fallback_prefix="item"):
    """Converts a (possibly translated, possibly accented or
    non-Latin) title into a URL-safe ASCII slug. Arabic (and any
    other non-Latin script) has no ASCII transliteration via NFKD --
    normalizing it strips every character, leaving an empty string --
    so in that case this falls back to fallback_prefix plus a short
    digest of the original text, rather than returning an empty slug
    (which WordPress would silently turn into "post-N"). Uses
    hashlib, not Python's built-in hash(): that's randomized per
    process (PYTHONHASHSEED) by default, which would give the SAME
    Arabic title a DIFFERENT fallback slug on every pipeline run --
    exactly the kind of non-idempotent write that created the
    duplicate-page incident this session already fixed once
    (functions.php's i18n sync lock). A content-derived digest is
    stable across runs, so re-running on the same item reuses the
    same slug instead of creating a new page under a new one."""
    normalized = unicodedata.normalize("NFKD", text)
    ascii_text = normalized.encode("ascii", "ignore").decode("ascii")
    slug = re.sub(r"[^a-zA-Z0-9]+", "-", ascii_text).strip("-").lower()
    if slug:
        return slug
    digest = hashlib.sha1(text.encode("utf-8")).hexdigest()[:10]
    return f"{fallback_prefix}-{digest}"
