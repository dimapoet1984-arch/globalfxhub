"""
Track A content splitter -- turns one post/page's post_content into a
list of translatable "pieces" (leaf block-level elements) plus the
exact passthrough text between them, so only real content gets sent
to the model and the untouched structure survives byte-for-byte.

Per docs/TRANSLATION_PIPELINE_ARCHITECTURE.md section 2.2: this theme
has no page builder, so unlike the sunmoontarot playbook's Divi-block
splitter, there are no `<!-- wp:name {json} -->` block comments to
parse -- post content here is plain classic-editor HTML. Confirmed
live across 200 real posts (Learn/News/candlestick-patterns): the only
tags that ever appear are a, em, strong, h2, h3, img, figure,
figcaption, li, ol, p, ul. LEAF_TAGS below also covers h1/h4-h6 and
blockquote defensively, since the architecture doc commits to that
set even though nothing live uses them yet.

Design: scan the raw HTML for the leaf tags below, matched
non-greedily against their own closing tag (so a leaf tag's own
inner content -- including any inline tags like <a>/<strong>/<em>, or
for <figure>, a nested <img>/<figcaption> -- travels as ONE piece's
HTML, verbatim). Everything NOT inside a matched leaf span (the
structural <ul>/<ol> wrapper tags, whitespace between blocks) is
passthrough, copied through unchanged. This is correct for this
theme's real content because no leaf tag nests inside another leaf
tag of the same name here (confirmed live) -- it is NOT a general
HTML parser, and verify_round_trip() below exists specifically to
catch the case where that assumption doesn't hold on some future
piece of content, rather than silently mistranslating it.

Never trust this on unseen content without first calling
verify_round_trip() on it -- see the "byte-exact round-trip test on
real dumps" rule in docs/TRANSLATION_PLAYBOOK.md section 3.1 and
docs/TRANSLATION_PIPELINE_ARCHITECTURE.md section 2.2.
"""
import re

LEAF_TAGS = ["p", "h1", "h2", "h3", "h4", "h5", "h6", "li", "blockquote", "figure"]

_LEAF_PATTERN = re.compile(
    r"<(" + "|".join(LEAF_TAGS) + r")\b[^>]*>.*?</\1>",
    re.IGNORECASE | re.DOTALL,
)


def split_html(html):
    """Returns an ordered list of segments:
    {"type": "text", "html": "..."} for passthrough content, or
    {"type": "piece", "id": N, "html": "..."} for one leaf block.
    Concatenating every segment's "html" in order always reproduces
    the input exactly, by construction (every byte of the input is
    assigned to exactly one segment) -- that alone does NOT prove the
    split is semantically correct, since a nested same-name leaf tag
    (e.g. a <blockquote> inside another <blockquote>) would still
    concatenate back losslessly while matching the WRONG span (the
    inner closing tag instead of the outer one). Guard against that
    directly: raise rather than silently return a mis-split piece --
    see verify_round_trip()'s docstring for why the round-trip check
    alone can't catch this."""
    segments = []
    pos = 0
    piece_id = 0
    for match in _LEAF_PATTERN.finditer(html):
        tag_name = match.group(1)
        inner = match.group(0)[len(tag_name) + 2 : -(len(tag_name) + 3)]
        if re.search(r"<" + tag_name + r"\b", inner, re.IGNORECASE):
            raise ValueError(
                f"Nested <{tag_name}> found inside another <{tag_name}> "
                f"(around byte {match.start()}) -- this splitter assumes "
                "leaf tags never nest inside themselves (true for every "
                "live item checked); refusing to silently mis-split. "
                "Needs a human look at this specific content before it "
                "can go through this pipeline."
            )
        if match.start() > pos:
            segments.append({"type": "text", "html": html[pos:match.start()]})
        segments.append({"type": "piece", "id": piece_id, "html": match.group(0)})
        piece_id += 1
        pos = match.end()
    if pos < len(html):
        segments.append({"type": "text", "html": html[pos:]})
    return segments


def pieces_for_translation(segments):
    """The subset of segments the model actually needs to see, in the
    shape translate_common.translate_piece()'s pieces parameter
    expects."""
    return [{"id": s["id"], "html": s["html"]} for s in segments if s["type"] == "piece"]


def rebuild(segments, translated_pieces_by_id):
    """Reassembles the full HTML, substituting each piece's HTML with
    its translation where one was returned, falling back to the
    original piece HTML for any id missing from translated_pieces_by_id
    (a piece that failed every retry stays in English rather than
    vanishing -- see docs/TRANSLATION_PLAYBOOK.md section 3.3 point 4:
    "A piece that still fails stays in the original language... so
    the cause is visible without re-running.")."""
    parts = []
    for segment in segments:
        if segment["type"] == "text":
            parts.append(segment["html"])
        else:
            parts.append(translated_pieces_by_id.get(segment["id"], segment["html"]))
    return "".join(parts)


def verify_round_trip(html):
    """Splits html and rebuilds it with every piece UNCHANGED -- the
    result must equal the input byte-for-byte. This mainly catches a
    bug in the segment bookkeeping itself (a skipped or duplicated
    byte range); it can NOT by itself catch a semantically-wrong split
    where the pieces still happen to concatenate back losslessly --
    split_html()'s own nested-tag guard (raises ValueError) is what
    catches that case. Call this on every real dump before trusting
    the splitter on it, per docs/TRANSLATION_PLAYBOOK.md section 3.1.
    Returns (ok, rebuilt) rather than raising for a round-trip
    mismatch, so a caller can log/report on content that fails this
    specific check across a whole dump rather than stopping at the
    first one -- but a ValueError from split_html() itself (the
    nested-tag case) still propagates up unchanged, since that's not
    something to just note and continue past."""
    segments = split_html(html)
    rebuilt = rebuild(segments, {s["id"]: s["html"] for s in segments if s["type"] == "piece"})
    return rebuilt == html, rebuilt
