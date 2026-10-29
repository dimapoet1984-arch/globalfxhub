"""
Shared Claude API layer for the translation pipeline (Track A and
Track B both import this -- see
docs/TRANSLATION_PIPELINE_ARCHITECTURE.md section 4).

Model/effort/caching choices here follow the playbook (section 0.2,
3.2) and the current claude-api skill guidance (2026-10 snapshot):
Haiku for both translate and review passes, low effort for
translation, medium for review, a cached system prompt (glossary +
style), structured JSON output via output_config.format so the
response is always parseable without free-text stripping.

The API key comes from TRANSLATION_API_KEY, not the SDK's default
ANTHROPIC_API_KEY -- that's the name the repo secret was created
under (see docs/TRANSLATION_PIPELINE_ARCHITECTURE.md section 0), so
every entry point here reads it explicitly rather than relying on
anthropic.Anthropic()'s automatic env lookup.
"""
import json
import os

import anthropic

HAIKU_MODEL = "claude-haiku-5-5"
SONNET_MODEL = "claude-sonnet-5-5"  # offered as a pricier review option only -- never the default.

TRANSLATE_EFFORT = "low"
REVIEW_EFFORT = "medium"

# Matches playbook section 3.5 ("Parallelism: 2 languages at a time,
# SDK max_retries=8. Five at once hit the 80,000 output tokens/minute
# limit.") -- the exact number is account-specific; re-verify against
# this account's actual rate limit before the first full run rather
# than trusting this unchanged.
MAX_RETRIES = 8
MAX_PARALLEL_LANGUAGES = 2


def get_client():
    api_key = os.environ.get("TRANSLATION_API_KEY")
    if not api_key:
        raise RuntimeError(
            "TRANSLATION_API_KEY is not set. This pipeline reads the "
            "Anthropic key from that env var (the GitHub Actions "
            "secret name), not ANTHROPIC_API_KEY."
        )
    return anthropic.Anthropic(api_key=api_key, max_retries=MAX_RETRIES)


def build_system_prompt(glossary_text, role_instructions):
    """Returns the `system` param shape with the cache breakpoint on
    the (only, so also last) block -- this is the large, byte-stable
    content shared across every request in a run: glossary + style +
    role instructions never change mid-run, so every item after the
    first one in a run reads this from cache instead of paying for it
    again (prompt-caching.md's "Large system prompt shared across
    many requests" pattern)."""
    text = role_instructions.strip() + "\n\n" + glossary_text.strip()
    return [
        {
            "type": "text",
            "text": text,
            "cache_control": {"type": "ephemeral"},
        }
    ]


TRANSLATE_ROLE_INSTRUCTIONS = """\
You are translating content for GlobalFXHub, an independent forex/CFD
broker research site, from English into the target language given in
each request. Translate only visible text and alt/title/aria-label
attributes. Keep every HTML tag and attribute exactly as given --
never add, remove, or reorder tags. Never translate anything in the
glossary's "never translate" list. Use the glossary's required
translation for every term it covers. Match the style notes for the
target language exactly (register/form of address). Output must be
valid JSON matching the given schema -- no commentary outside the
JSON."""

REVIEW_ROLE_INSTRUCTIONS = """\
You are reviewing a machine translation for GlobalFXHub, an
independent forex/CFD broker research site. Fix grammar, gender and
article agreement, and word order -- a native speaker should not be
able to tell this was machine-translated. Do not change meaning, do
not add or remove information, do not touch any HTML tag or attribute.
Keep every glossary term as given. Output must be valid JSON matching
the given schema -- no commentary outside the JSON."""


def _extract_json_text(response):
    """output_config.format guarantees the first content block is
    text containing valid JSON (tool-use.md's Raw Schema example) --
    this still defends against an empty/missing content list rather
    than assuming index 0 exists, since a max_tokens cutoff or a
    refusal can produce a response with no text block at all."""
    for block in response.content:
        if block.type == "text":
            return block.text
    raise ValueError(
        f"No text block in response (stop_reason={response.stop_reason!r}) -- "
        "likely truncated by max_tokens or a refusal; nothing to parse."
    )


def structured_request(client, model, system, user_content, json_schema, effort, max_tokens=8000):
    """One structured-output request. Returns (parsed_dict, response)
    so callers can inspect response.usage for cache-hit verification
    (see prompt-caching.md "Verifying cache hits") without this
    function hiding that from them."""
    response = client.messages.create(
        model=model,
        max_tokens=max_tokens,
        system=system,
        messages=[{"role": "user", "content": user_content}],
        output_config={
            "effort": effort,
            "format": {
                "type": "json_schema",
                "schema": json_schema,
            },
        },
    )
    text = _extract_json_text(response)
    try:
        parsed = json.loads(text)
    except json.JSONDecodeError as exc:
        raise ValueError(f"Model returned invalid JSON despite output_config.format: {text[:500]!r}") from exc
    return parsed, response


def translate_piece(client, system, item_description, pieces, lang, json_schema, model=HAIKU_MODEL):
    """pieces: list of {"id": ..., "html": ...} -- the already-split
    content units (see the Track A splitter, to be built). Returns the
    parsed structured response: translated pieces plus title/slug/SEO
    fields per the schema the caller passes in (schema shape differs
    between Track A posts/pages and Track B field-by-field items, so
    it's a parameter here, not hardcoded)."""
    user_content = (
        f"Target language: {lang}\n\n"
        f"Item: {item_description}\n\n"
        f"Pieces to translate (JSON array, translate each by id, preserve all HTML exactly):\n"
        f"{json.dumps(pieces, ensure_ascii=False)}"
    )
    return structured_request(client, model, system, user_content, json_schema, TRANSLATE_EFFORT)


def review_piece(client, system, item_description, translated_pieces, lang, json_schema, model=HAIKU_MODEL):
    user_content = (
        f"Target language: {lang}\n\n"
        f"Item: {item_description}\n\n"
        f"Machine-translated pieces to review and correct (JSON array):\n"
        f"{json.dumps(translated_pieces, ensure_ascii=False)}"
    )
    return structured_request(client, model, system, user_content, json_schema, REVIEW_EFFORT)
