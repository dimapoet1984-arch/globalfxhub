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

Fields marked html=True contain inline HTML (links, headings) and
must go through the SAME splitter/markup-check machinery as Track A
content (i18n/splitter.py, i18n/markup_check.py) -- not a naive
whole-string translation -- since a broken <a href> here is exactly
as real a markup bug as one in a blog post. Fields marked html=False
are plain text, safe to translate as a single string with no piece
splitting needed.

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
        ("intro", True),  # confirmed live: contains inline <a href> links
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
