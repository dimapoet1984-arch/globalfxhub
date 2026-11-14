# Translation pipeline architecture — globalfxhub.net

How this site translates into 7 languages (Spanish, German, Arabic,
French, Polish, Italian, Portuguese), adapted from
`docs/TRANSLATION_PLAYBOOK.md` (the sunmoontarot playbook) to what's
actually true here. Read that file first — this one only covers where
globalfxhub diverges and the concrete plan for each divergence.

## 0. What's already done (as of 2026-10-29)

- Polylang installed, 7 languages added (owner-confirmed).
- `GLOBALFXHUB_LANG_SWITCHER_ENABLED` kill switch (off) and
  `GLOBALFXHUB_LIVE_LANGUAGES = ['en']` (`inc/i18n-live-languages.php`)
  already gate noindex/hreflang/core-sitemap exposure — section 2.2 of
  the playbook is **done**, ahead of creating any real translations.
  Nothing here should add a language to that constant before step 9.
- `globalfxhub_sync_templated_pages_across_languages()` (functions.php)
  auto-creates/repairs a correctly-templated page per language for the
  11 "container" slugs, now behind a real `add_option()`-based lock
  (fixed a live race condition on 2026-10-29 — see git history).
- The 7 `home-<language>` front-page objects exist with their original
  descriptive slugs restored.
- `i18n/cleanup_duplicate_pages.py` and `i18n/fix_home_stub_slugs.py`
  are one-off emergency scripts, not part of the ongoing pipeline —
  the real `cleanup.py`/`audit.py` below replace their job going
  forward.
- Credentials: `WP_USERNAME`/`WP_APP_PASSWORD` and `TRANSLATION_API_KEY`
  (a plain Anthropic key, `sk-ant-...`) are GitHub Actions repo secrets.
  Model/budget and approval-gate decisions from playbook section 0 are
  adopted as proposed there: Haiku translate + Haiku review
  (`claude-haiku-5-5`), Sonnet review offered as a pricier option only;
  a 5-7 page sample reviewed by the owner before the full run.

## 1. The two tracks, concretely

Unlike sunmoontarot, globalfxhub has **no page builder** — templates
are plain PHP (`page-*.php`) rendering either real WP post content or
PHP-array data with no `wp_insert_post()` backing at all. That data
split, not a builder-markup split, is the real architectural fork
here.

### Track A — real WP posts/pages (Polylang-applicable)

Everything Polylang can natively duplicate-and-link:

| What | Count | Post type | Notes |
|---|---:|---|---|
| 11 container pages (reviews, learn, compare, news, countries, best, regulation, broker-finder, cost-calculator, regulation-checker, broker-changelog) | 11 | page | Already auto-synced per language by `globalfxhub_sync_templated_pages_across_languages()`; content lives in theme PHP, title the job needs to translate is minimal (mostly handled by Track B's companion UI strings). |
| 2 container children (news/markets, news/brokers) | 2 | page | Same mechanism. |
| 7 service pages (about, how-we-test, why-trust-us, advertiser-disclosure, terms, privacy, contact) | 7 | page | Real HTML content in `inc/service-pages.php`, needs full translation. |
| home + 7 home-`<language>` stubs | 8 | page | Front page is `is_front_page()`-driven; content is theme PHP (front-page.php), not post content — nothing to translate here beyond the hero banner's UI strings (Track A-adjacent, see §5). |
| Learn category posts | 69 | post | Full post content, real translation target. |
| News category posts | 100 | post | Includes FX Market News + Broker News sub-feeds. |
| Candlestick Patterns category posts | 45 | post | Real translation target. |

Totals confirmed live via REST (`X-WP-Total`): 32 pages, 214 posts.

### Track B — virtual/data-driven content (no WP post exists)

Rendered via rewrite-rule + query-var routes from PHP arrays, the
same data every live page already reads (`globalfxhub_top_ranked_broker_slugs()`,
`globalfxhub_vs_pair()`, `globalfxhub_broker_alternatives()`, etc.):
broker review pages, country pages, best-lists, regulation pages,
compare pages, broker-news items — confirmed scope: **everything, all
fields, all 139 brokers × 7 languages** (owner-confirmed, not a
prioritized subset). ~900 URLs today in English; ×7 once translated.

Polylang literally cannot help here — `pll_save_post_translations()`
needs a post ID on both sides, and there is no post. This needs its
own storage and its own lookup layer.

### What's explicitly NOT a third track

UI strings (nav, footer, hero banner, section headings — everything in
`inc/translatable-strings.php`) already have a working mechanism:
`pll_register_string()` + Polylang's own Translations admin screen,
served through `globalfxhub_t()`/`globalfxhub_te()`. The pipeline
should **populate** these via Polylang's string-translation REST/DB
API, not invent a parallel system — see §5.

Also NOT applicable: the playbook's `menus.py` (§4 "Menus: rebuild per
language... assign through Polylang's `nav_menus` setting"). This
theme doesn't use `wp_nav_menu()` — navigation is hardcoded PHP reading
`globalfxhub_t()` strings — so there is no WP menu object to duplicate
or assign. Nothing to build here.

## 2. Track A architecture

```
GitHub Actions (manual + scheduled)
  i18n/translate.py ──REST (app password)──> WordPress + Polylang
     │  post/page content split into pieces (HTML block-level, no
     │  builder markup to worry about -- just well-formed post HTML),
     │  Claude translates pieces, rebuilt byte-exact
     └─ Anthropic API (Haiku) via i18n/translate_common.py
  i18n/cleanup.py   (dry-run first) parents, categories, slugs, unlinked duplicates
  i18n/audit.py     every live English original has a live translation in every language
Theme (inc/i18n-rest-connector.php)
  REST fields on post + page: gfh_lang, gfh_translations, gfh_seo_title, gfh_seo_desc
  Same noindex/hreflang/sitemap gating as today (inc/i18n-live-languages.php) -- no change needed, it's already keyed off GLOBALFXHUB_LIVE_LANGUAGES, not post type.
```

### 2.1 REST connector (`inc/i18n-rest-connector.php`, to build)

Free Polylang has no REST fields for posts, same gap the playbook
hits. `register_rest_field()` on `post` and `page`:

- `gfh_lang`: get `pll_get_post_language()`, set `pll_set_post_language()`.
- `gfh_translations`: get `pll_get_post_translations()`, set
  `pll_save_post_translations()` (merge into the group, never replace
  it from a stale snapshot — see §2.3), then
  `PLL()->model->post->clean_languages_cache()`.
- `gfh_seo_title` / `gfh_seo_desc`: plain post meta,
  `show_in_rest => true`. This theme has no SEO plugin (no Yoast/RankMath
  found) — confirm whether these are consumed anywhere before building
  a reader for them; if nothing reads SEO meta today, store them
  anyway for when the SEO layer exists, but don't block the pipeline
  on wiring up consumption.

No `gfh_copy_meta_from` field: this theme's pages have no page-builder
layout meta to copy (plain `page-*.php` templates keyed by
`page_template`, which the pipeline sets directly via the standard
`template` REST field on creation).

### 2.2 Splitting content

No builder markup — Track A content is either hand-written post HTML
(Learn/News/candlestick-patterns posts, service pages) or empty
(container pages, whose real content is theme PHP, not post_content).
Split by block-level HTML tags (`<p>`, `<h2>`-`<h4>`, `<li>`, `<blockquote>`),
translate inner text + `alt`/`title`/`aria-label` attributes, preserve
every tag/attribute/href exactly. Much simpler than the playbook's
Divi-block case — no `<!-- wp:name {json} -->` comments, no module
attribute serialization. Still needs a byte-exact round-trip test on
real dumps before the first real translation call (playbook §3.1's
rule applies regardless of markup complexity: prove the rebuild is
lossless on unchanged pieces before trusting it on changed ones).

### 2.3 Linking translations

Same rule as the playbook, same reason it's rule #1: the exact race
this site already hit once this session (concurrent requests racing
`wp_insert_post()`/translation-group writes) is the generalized form of
the exact bug `globalfxhub_sync_templated_pages_across_languages()` had.
Reuse the now-proven `add_option()` lock pattern
(`globalfxhub_acquire_i18n_sync_lock()`/`globalfxhub_release_i18n_sync_lock()`)
rather than inventing a second locking mechanism — either literally
call those functions from the REST connector's translation-linking
code path, or copy the identical pattern into a pipeline-specific lock
option if calling theme PHP functions from the connector isn't
feasible at that call site.

## 3. Track B architecture (new)

```
GitHub Actions
  i18n/translate_track_b.py ──REST (app password, custom endpoint)──> WordPress
     │  one Claude request per broker per language (all fields together:
     │  review prose, scores commentary if any free text exists, meta
     │  descriptions, etc.) via i18n/translate_common.py
     └─ writes rows into wp_globalfxhub_translations
  i18n/audit_track_b.py   every English broker/country/etc. item has a row for every field, every language
Theme (inc/i18n-track-b-translations.php, to build)
  wp_globalfxhub_translations table: content_type, content_id, field, lang, value
  globalfxhub_tf( $type, $id, $field, $english_value, $lang = null )
    -- same fallback contract as globalfxhub_t(): returns the translation
       if one exists for the current (or given) language, else $english_value
       unchanged. Every Track B template (page-countries.php, the broker
       review template, page-best.php, broker-vs.php, broker-alternatives.php,
       regulation.php, broker-news.php, ...) wraps its English strings in
       this call the same way templates already call globalfxhub_t() for
       UI strings.
  A dedicated REST endpoint (or REST field on a lightweight proxy) for
  the pipeline to write rows -- there's no post for register_rest_field
  to attach to, so this needs its own route under wp-json, authenticated
  the same way (Application Password), rather than register_rest_field.
```

### 3.1 Why a DB table instead of options/post meta

~900 items × ~10-20 fields × 7 languages is 60,000-125,000+ rows.
`wp_options`/postmeta autoloading at that volume would bloat every
request's options cache. A dedicated table with
`(content_type, content_id, field, lang)` as a unique key and indexed
lookup is the standard WordPress answer at this scale, and keeps Track
B fully decoupled from Track A's post-based storage — correct, since
Track B items aren't posts.

### 3.2 Table shape

```sql
CREATE TABLE wp_globalfxhub_translations (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  content_type VARCHAR(40) NOT NULL,   -- 'broker', 'country', 'best_list', 'regulation', 'compare', 'broker_news', ...
  content_id VARCHAR(80) NOT NULL,     -- broker slug, country code, etc. -- varies by content_type, always a stable existing identifier
  field VARCHAR(60) NOT NULL,          -- 'review_summary', 'seo_title', 'meta_description', ...
  lang VARCHAR(10) NOT NULL,
  value LONGTEXT NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY content_field_lang (content_type, content_id, field, lang),
  KEY content_lookup (content_type, content_id, lang)
) {charset_collate};
```

Created/upgraded via `dbDelta()` behind a version-option check
(`globalfxhub_track_b_schema_version`), the same self-healing pattern
this theme already uses for its templated pages (`globalfxhub_ensure_templated_page()`)
— run on `init`, cheap no-op after the first successful run.

### 3.3 Field inventory per content_type

To be enumerated exactly against the live data shape before the first
translation call (not guessed): read one real broker record, one
country record, one best-list entry, one regulation-page record, one
compare-pair record, one broker-news item from their respective PHP
data sources, and list every free-text field each actually has (not
every numeric score, which never needs translation). This is a
pre-flight step in the pipeline build, not something to hardcode here
speculatively.

## 4. Shared Claude API layer (`i18n/translate_common.py`, to build)

Used by both tracks. Per the `claude-api` skill's current guidance
(2026-10 snapshot):

- Model: `claude-haiku-5-5` for both translate and review passes
  (playbook's cost choice). A `--reviewer sonnet` override maps to
  `claude-sonnet-5-5`, offered per-run, never the default.
- Structured output via `output_config: {"format": {"type": "json_schema", "schema": {...}}}`
  — guarantees the response's first text block is valid JSON matching
  the schema; no `output_format` (deprecated name).
- Effort: `output_config.effort = "low"` for translation, `"medium"`
  for review (playbook §3.2's explicit split).
- Prompt caching: the system prompt (glossary + style/register
  instructions, shared across every request in a run) gets a
  `cache_control: {"type": "ephemeral"}` marker on its last block.
  Tools render before system but this pipeline uses no tools, so the
  breakpoint is simply the system block. Per-item content (the actual
  piece being translated) goes in the user message, after the cached
  prefix, so it never invalidates the cache.
- Retry: SDK default `max_retries=2` isn't enough for a long batch run
  against a shared-across-languages API key; set `max_retries=8` per
  playbook §3.5, matching the parallelism cap (2 languages at once) to
  avoid the 80,000 output-tokens/minute rate limit mentioned there —
  verify against this account's actual limit before relying on the
  number, since limits are account-specific.
- Verify caching is actually working on the first real run by checking
  `response.usage.cache_read_input_tokens > 0` on the second and later
  requests in a run — don't assume the marker placement is correct
  without checking the usage field once.

## 5. UI strings (hero banner, nav, footer)

Populate via Polylang's string-translation storage directly (each
`pll_register_string()`-registered key has a per-language value
stored by Polylang itself, readable/writable through
`pll_translate_string()` for reads — the pipeline needs a write path.
Free Polylang stores these in an options-backed single translation
group per language, editable at `/wp-admin/admin.php?page=mlang_strings`;
there is no REST route for it in free Polylang, so writing this from
`i18n/translate.py` needs either a small custom REST endpoint (same
approach as Track B's write path, §3) or a WP-CLI-style direct DB
write run once via an Action, not per-item). Given this is a small,
fixed set of strings (not hundreds of items), a one-time translation
pass with manual review is proportionate — this doesn't need the same
automated-pipeline treatment as Track A/B's hundreds of items.

**Per CLAUDE.md**: once the hero banner's copy is translated, re-check
it against the rankings exactly as CLAUDE.md already requires for the
English copy — a translated claim can go stale the same way the
English one can.

## 6. Build order

1. ~~Decisions, owner setup, Polylang install~~ — done.
2. ~~Hide unlaunched languages~~ — done, ahead of schedule.
3. Track A REST connector (`inc/i18n-rest-connector.php`) + Track B
   table/helper (`inc/i18n-track-b-translations.php`) + Track B write
   endpoint. Deploy, dump a few real items, inspect raw content.
4. Glossary (`i18n/glossary.py`) + shared Claude layer
   (`i18n/translate_common.py`), tested against real dumps.
5. Track A splitter with byte-exact round-trip test.
6. Track A pipeline (`i18n/translate.py`) with all playbook §3.3
   checks, serialized linking, id-ordered pagination.
7. Track B field inventory (§3.3 above) + `i18n/translate_track_b.py`.
8. Sample run: one language, 5-7 representative items across both
   tracks → fix → owner review → all 7 languages, all items.
9. `cleanup.py`/`audit.py` for Track A; `audit_track_b.py` for Track B.
10. UI strings (§5) — one-time pass + manual review.
11. Verify per playbook §5 (adapted: no builder layout/CLS concern
    from a page builder, but still check `<html lang>`, noindex while
    hidden, hreflang, broker-review/country/compare pages render
    correctly in each language, GTM fires once).
12. Add the 7 languages to `GLOBALFXHUB_LIVE_LANGUAGES`, resubmit
    `/sitemap.xml` to GSC (the custom sitemap at `inc/sitemap.php`
    currently emits English-only URLs via bare `home_url()` calls —
    confirm whether to extend it with per-language URLs at this step,
    a decision to make at launch time, not now).
13. Schedule new-content translation + audit (new Learn/News posts,
    new broker/country data) the way the playbook does in its §3.7.
