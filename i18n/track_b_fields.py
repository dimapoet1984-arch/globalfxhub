"""
Track B field inventory -- every free-text field actually worth
translating per content_type, enumerated against the REAL PHP data
sources (not guessed), per docs/TRANSLATION_PIPELINE_ARCHITECTURE.md
section 3.3's explicit instruction not to hardcode this speculatively.

Checked directly against: functions.php (globalfxhub_get_brokers()),
inc/countries.php (globalfxhub_get_countries()), inc/best.php
(globalfxhub_get_best_lists()), inc/regulation.php
(globalfxhub_get_regulation_pages()), inc/broker-news.php. Numeric/
structural fields (scores, ranks, booleans, years, URLs, PHP closures)
are deliberately excluded -- nothing there is ever translated.

inc/broker-vs.php (compare) and inc/regulation-checker.php are pure
reshapings of the SAME broker/country/regulation data already covered
below -- they carry no text of their own, so there is no separate
"compare" or "regulation_checker" content_type here. The UI chrome
around them (labels, headings) is a Track A/UI-string concern
(inc/translatable-strings.php), not Track B.

Fields marked html=True are block-level content -- the whole field is
wrapped in leaf tags (<p>, <h2>, ...) the way a real post body is --
and must go through the SAME splitter/markup-check machinery as
Track A content (i18n/splitter.py, i18n/markup_check.py), since a
broken <a href> here is exactly as real a markup bug as one in a blog
post. That splitter only recognizes block-level leaf tags, so it is
NOT for a field that is merely bare inline text that happens to
contain a short tag like <a href> with no <p>/<h2> wrapper at all --
marking one of those html=True gives it zero leaf-tag matches, so the
whole field becomes one untranslated passthrough segment (confirmed
bug: best_list.intro, fixed by marking it html=False instead -- see
that entry's own comment). Fields marked html=False are plain text,
safe to translate as a single string with no piece splitting needed;
translate_track_b.py's plain-field prompt also preserves any such bare
inline tag while translating the surrounding text, so html=False is
correct whether or not the field happens to contain one.

FIELDS[content_type] is a list of (field_path, html) tuples. A
field_path with a '[]' segment denotes a list of items sharing that
shape (e.g. "faqs[].q" means every item in the faqs array has a 'q'
field) -- the actual iteration happens in translate_track_b.py, not
here; this module is purely the inventory.
"""

FIELDS = {
    "broker": [
        ("instruments", False),
        ("account_types", False),
        ("execution_model", False),
        ("withdrawal_deposit_note", False),
        ("overnight_financing", False),
        ("currency_conversion_fee", False),
        ("inactivity_fee", False),
        ("customer_service_note", False),
        ("mobile_note", False),
        ("education_note", False),
        ("blurb", False),
        # 'hq' (e.g. "London, UK") is a short place-name phrase --
        # translatable in principle (many languages do localize country
        # names), but low priority and easy to get wrong without a
        # native speaker; left out of the first pass deliberately
        # rather than guessed. 'name'/'entity' (company/legal names)
        # and 'min_deposit_display' (a currency-formatted string) are
        # never translated -- proper nouns and formatted numbers.
    ],
    "country": [
        ("name", False),  # country names DO need translation (e.g. "Germany" -> "Alemania")
        ("taxation_overview", False),
        ("local_payment_methods", False),
        ("country_specific_restrictions", False),
        ("additional_eu_note", False),  # present on some countries only
        ("local_entity_note", False),  # present on some countries only
        ("faqs[].q", False),
        ("faqs[].a", False),
        ("paragraphs[]", False),
        # 'regulator' (e.g. "FMA (Finanzmarktaufsicht)") is a proper
        # noun + its own translated expansion in parens already --
        # left untranslated, same reasoning as broker entity names.
    ],
    "best_list": [
        ("title", False),
        # NOT True: this contains an inline <a href> link, but it is bare
        # text, never wrapped in a block-level leaf tag (<p>, <h2>, ...).
        # Marking it True routed it through split_html() (splitter.py),
        # which only recognizes LEAF_TAGS block wrappers -- against bare
        # inline text it finds zero leaf-tag matches, so the entire field
        # becomes ONE passthrough "text" segment with ZERO pieces sent for
        # translation. rebuild() then reassembles that single untouched
        # segment, byte-identical to the English source, and writes that
        # back as the "translation" -- no piece failed, so no warning is
        # ever logged either. Confirmed live: every intro for every
        # best_list item, in every language, silently round-tripped back
        # to raw English this way (discovered by checking the real
        # rendered page after a translate run reported "0 warnings").
        # False routes it through translate_plain_fields() instead, whose
        # prompt explicitly preserves inline HTML tags while translating
        # the surrounding text -- correct for bare inline content like
        # this, with no leaf-tag assumption at all.
        ("intro", False),
        ("sort_label", False),
    ],
    "regulation": [
        ("title", False),
        ("summary", False),
        ("content", True),  # confirmed live: full rich HTML (h2/p/a), same shape as Track A posts
    ],
    "broker_news": [
        ("headline", False),
        ("body", False),
        ("article.paragraphs[]", False),
        # 'article.sources[].label' is a citation of another
        # publication's own article title -- left in English
        # deliberately; translating a citation's title doesn't help
        # a reader find the source, and risks misrepresenting what
        # the cited publication actually called it. The URL is never
        # translated either way.
    ],
}


def iter_field_paths(content_type):
    """Yields (field_path, is_html) for a content_type, or raises
    KeyError for an unrecognized one -- never silently returns an
    empty list for a typo'd content_type name."""
    return list(FIELDS[content_type])


def resolve_field_values(fields, field_path):
    """Applies one field_path spec (as used in FIELDS above) against
    one item's "fields" dict from the track-b-source REST endpoint.

    A plain path ("blurb") resolves to at most one (concrete_name, value)
    pair, concrete_name equal to the path itself. A path with an
    "[]" segment ("paragraphs[]", "faqs[].q") expands to one pair per
    array element, with the index baked into a concrete field name
    ("paragraphs[0]", "faqs[0].q") -- that concrete name is exactly
    what gets stored as the `field` column in wp_globalfxhub_translations
    and looked up again by globalfxhub_tf() at render time, so it must
    be stable across runs (it is: built from the array's own order,
    never regenerated differently for the same source data).

    Returns [] for a field that doesn't exist on this particular item
    (e.g. "additional_eu_note" on a country that doesn't have one) or
    is empty -- never raises, since that's expected, normal variation
    across items of the same content_type, not an error."""

    def walk(obj, segments, prefix):
        if not segments:
            if obj in (None, "", []):
                return []
            return [(prefix, obj)]
        seg = segments[0]
        rest = segments[1:]
        if seg.endswith("[]"):
            key = seg[:-2]
            lst = obj.get(key) if isinstance(obj, dict) else None
            if not isinstance(lst, list):
                return []
            results = []
            for i, elem in enumerate(lst):
                sub_prefix = f"{prefix}.{key}[{i}]" if prefix else f"{key}[{i}]"
                results.extend(walk(elem, rest, sub_prefix))
            return results
        if not isinstance(obj, dict) or seg not in obj:
            return []
        sub_prefix = f"{prefix}.{seg}" if prefix else seg
        return walk(obj[seg], rest, sub_prefix)

    return walk(fields, field_path.split("."), "")
