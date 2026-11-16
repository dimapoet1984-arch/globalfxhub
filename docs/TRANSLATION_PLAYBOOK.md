# Translating a WordPress site with Claude: playbook

How sunmoontarot.com was translated into 5 languages (October 2026),
written as instructions for Claude Code on the next site. It covers
the order of work, the architecture, and every mistake made the first
time with the rule that prevents it. Reference implementation: `i18n/`
and `wp-content/themes/Divi-child/i18n/` in the sunmoontarot repo.

Result on sunmoontarot: about 210 pages and posts × 5 languages,
Claude Haiku for translation and review, about $8 in total. Running
cost: about $5/month for new posts and daily generated content.

## 0. Decisions to settle before writing code

Ask the owner, then don't revisit:

1. Languages. Start with 3–5, never 30 at once: unreviewed machine
   translation at scale is a Google spam risk.
2. Model and budget. Cheapest that holds quality: Haiku translates and
   Haiku reviews (≈ $0.007 per page per language). Sonnet review costs
   about 7× more; offer it as an option only.
3. Approval gate. A sample of 5–7 representative pages (home, a
   content page, a tool or interactive page, a blog post, a dynamic
   page) in all languages, reviewed by the owner, before the full run.

## 1. Owner's setup steps (give them as one checklist)

- Full backup (hosting panel).
- Install Polylang (free). Settings, all at once:
  - default language = the site's language; assign existing content
    to it;
  - URL: language from the directory (`/es/`); hide the default
    language in URLs on;
  - "front page URL contains the language code" on (`redirect_lang`):
    otherwise `/es/` redirects to `/es/inicio/`;
  - detect browser language off;
  - media translation off.
- Check the menus immediately after activating Polylang. Polylang
  keeps a menu per language and location; on activation the default
  language often has none, and WordPress falls back to listing every
  page (Cart, Checkout…) in the live header. Fix it the same day (see
  3.4).
- Anthropic API key in a separate workspace with a monthly spend limit
  (Console → Workspaces → Spend limits; the Default workspace can't
  have one; a key can't be moved between workspaces, so create it in
  the new one). Store it as a GitHub Actions secret, never in chat.
- Account credits: turn on auto-reload. Workspace limits don't protect
  the account balance. On sunmoontarot the translation run used up the
  account's credits, and the site's live AI tools (same account) went
  down until the owner topped up.

## 2. Architecture

```
GitHub Actions (manual + scheduled)
  i18n/translate.py ──REST (app password)──> WordPress + Polylang
     │  content split into pieces, Claude translates pieces, rebuilt byte-exact
     └─ Anthropic API (Haiku)
  i18n/cleanup.py   (dry run first) unlinked duplicates, parents, categories, slugs
  i18n/audit.py     every live original has a live translation in every language; fails the job if not
  i18n/menus.py     translated menus per language, assigned through Polylang settings
Theme (child theme) i18n/i18n.php
  REST fields: smt_lang, smt_translations, smt_copy_meta_from; SEO title/description meta
  live-language switch: noindex + no hreflang + out of sitemap until launched
  language switcher, per-language texts (i18n/<lang>.json), og images per language
```

### 2.1 Polylang REST connector (free Polylang has no REST fields for posts)

In the child theme, `register_rest_field` on post, page and category:

- `smt_lang`: get `pll_get_post_language`, set `pll_set_post_language`;
- `smt_translations`: get `pll_get_post_translations`, set
  `pll_save_post_translations` (add the item itself to the group),
  then clear Polylang's language cache
  (`PLL()->model->clean_languages_cache()`) or language home pages
  keep stale URLs;
- `smt_copy_meta_from`: copies the original's custom fields (page
  builder layout, template) except language-specific ones (`_edit_*`,
  `_wp_old_*`, `_pll*`, own SEO meta, `_thumbnail_id`, cache meta).
  Without it, translated page-builder pages render without their
  layout;
- register `smt_seo_title` / `smt_seo_desc` post meta with
  `show_in_rest`, set by the pipeline, read by the SEO layer.

### 2.2 Languages hidden until launch

One constant, `SMT_LIVE_LANGUAGES = ['en']`. For languages not in it:
`wpseo_robots` → `noindex, nofollow`; `pll_rel_hreflang_attributes` →
drop them (and drop all if only one remains);
`wpseo_exclude_from_sitemap_by_post_ids` → their posts. Polylang
starts advertising empty `/es/` home pages in hreflang the moment
languages exist, so add this before creating languages if possible, or
straight after.

## 3. The translation pipeline

### 3.1 Splitting content (page builders)

Never send whole page-builder markup to the model. Parse block
comments (`<!-- wp:name {json} -->`) and the HTML between them, and
translate only:

- HTML between block comments that contains words;
- builder module values holding text (Divi 5: every `innerContent`
  string; image `alt`/`aria-label`/`placeholder` attributes);
- code modules: only the plain text runs between tags, never inside
  `<script>`/`<style>` (buttons like "Draw Again" live there; skipping
  code modules entirely left them in English).

Rebuild changed blocks with WordPress's own attribute serialization
(`json` with `--`, `<`, `>`, `&`, `"` escaped as `-- < > & "`). Test
the round trip on real dumps first: unchanged pieces must reproduce
the stored content byte for byte.

### 3.2 One request per item per language

Structured output (JSON schema): title, slug, excerpt, SEO title
(≤ 60), SEO description (≤ 155), category names and slugs, pieces by
id. System prompt: native-speaker register, form of address per
language, keep every tag and attribute, translate only visible text
and alt/title/aria-label/placeholder, the glossary. Prompt-cache the
system prompt. Effort `low` for translation, `medium` for review.

### 3.3 Checks and repairs (each one fixed a real failure)

1. Markup check per piece: same tags and attributes (ignoring
   alt/title/aria-label/placeholder/href) as the original. Failing
   pieces are retried with a note, then with the reviewer model.
2. Repair dropped links before checking: the model sometimes drops
   `<a>` around a name ("A reversed Eight of Wands" → "Un Ocho de
   Bastos invertido"). Find the glossary form of the link text in the
   translation and wrap it in the original `<a>` tag.
3. Last resort: same tags in a different order (adjectives move
   around links).
4. A piece that still fails stays in the original language and is
   reported with the model's last attempt, so the cause is visible
   without re-running.
5. Editor review pass (same cheap model): title, SEO title/description,
   excerpt and all h1–h4 headings, with the originals, "fix grammar,
   gender and article agreement, word order". This fixed "Die Narr
   Tarotkarte".
6. Slugs: transliterate (`NFKD` → ASCII), never strip accented letters
   ("tarô" became "tar"). Polylang free doesn't share slugs between
   languages: if WordPress returns a different slug than requested,
   set `<slug>-<lang>`. Put domain words in the glossary including for
   slugs ("tarot card" = "carta", never "tarjeta").

### 3.4 Linking translations: one at a time, from the current state

The biggest mistake. Languages ran in parallel and each save sent the
original's translation group from a snapshot taken at the start, so
one language overwrote another's link. Unlinked translations stayed
published; the next run saw no translation and created duplicates with
`-es` slugs (238 duplicates to clean up). Rule:

- create or update the translation without the translations field;
- then, inside a process-wide lock: GET the original's current group,
  add this language, POST it to the translation;
- same lock for creating translated categories.

### 3.5 Everything else in the pipeline

- List posts with `orderby=id&order=asc` in every paginated REST
  call. Date order with equal dates shifts items between pages; the
  cleanup missed two originals and trashed 9 valid translations.
- Parents before children (pages sorted by depth); a child's parent =
  the parent's translation.
- Copy status, date (posts), featured image, comment status, template,
  menu order; categories via their translations; SEO title/description
  into the translation's meta.
- Relink internal links to the translations that exist (`href` and
  builder link fields); run a relink pass again after the full run.
- Parallelism: 2 languages at a time, SDK `max_retries=8`. Five at once
  hit the 80,000 output tokens/minute limit.
- `--max-minutes` stops before GitHub's 6-hour job limit; re-running
  skips finished items.
- Modes: `translate-dry` (counts, no API calls), `translate`,
  `--force` for specific items, `--reviewer`.
- Output (reports, dumps) to an orphan branch pushed by the workflow:
  artifact downloads may be blocked from the Claude Code environment.

### 3.6 Cleanup and audit

- `cleanup.py --dry-run` first, always. It: sets translated pages'
  parents and posts' categories to the original's translations;
  trashes non-original items that aren't in any original's group;
  restores plain slugs where free. Never trash an item whose own group
  contains the original language, even if its original wasn't listed.
- `audit.py` (public REST, no login): every live original has a live,
  correctly-languaged translation in every language. Run after every
  translation run; exit non-zero so the job fails and GitHub emails
  the owner.
- WordPress's per-language counts in the admin include the trash:
  compare live counts from the REST API instead.

### 3.7 New content

Scheduled workflow after each publish time (both possible UTC hours if
the site's time zone has daylight saving), running
`translate.py --items all` then `audit.py`. GitHub starts scheduled
jobs 10 minutes to 3 hours late: the audit and the next run cover
gaps, and the job can be started by hand.

## 4. Beyond page content (the parts machine translation plugins miss)

- Glossary written by hand (domain names: tarot cards, zodiac signs,
  months, product terms) in `terms.py`, used in every prompt and
  exported to the theme. Same names everywhere.
- Interactive tools: JS gets `window.smtTools = {tool, lang, texts}`
  and `smtT(english, {vars})` from the theme; tool detection by the
  original's slug (translated URLs break `location.pathname` checks);
  requests carry `?lang=`; the backend adds "write in " to the model
  prompt and caches generated texts per language. Grammar of composed
  strings: give adjectives a noun to agree with ("(carta invertida)",
  not "(invertida)").
- Generated daily content (horoscopes): language parameter, cache per
  language, server-rendered in the page, one warm-up job per language
  (the endpoint's per-IP rate limit).
- SEO layer: related-link blocks and automatic links computed from the
  original's path, then mapped to translated paths and translated
  labels; untranslated targets left out. SEO titles from the
  translation's meta, never the original's map. Social share images
  regenerated per language (check the font covers all characters).
- Menus: rebuild per language from the original menus once content
  exists; assign through Polylang's `nav_menus` setting
  (`/wp-json/pll/v1/settings`). Until then, a theme fallback to the
  original menu.
- Language switcher in the main menu: only live languages, and only
  when there is a choice.
- Legal pages: third-party embeds (e.g. Ezoic's privacy section) may
  exist in one language only: link to it.

## 5. Testing (what to check, and the traps)

- Browser tests with Playwright: simulate interaction (mouse move,
  wheel, key) before checking scripts. LiteSpeed and similar plugins
  delay JS until the first interaction.
- The first visit after a cache purge can fail or shift (optimized
  files not built yet): re-test before investigating.
- For each language: page renders with the builder layout, `<html
  lang>`, title, noindex while hidden, the switcher, related links, a
  full tool run (draw → meanings → chat reply in the language), layout
  shift (CLS).
- Check the original-language site after every theme change (menus,
  horoscopes, related links, CLS).
- Analytics/Tag Manager: one copy only, high in `<head>`, excluded
  from JS delay (`data-no-optimize="1" data-no-defer="1"`), and not
  also pasted into the theme options.

## 6. Order of work

1. Decisions (section 0), owner's setup (section 1), check menus.
2. Hide unlaunched languages; REST connector; deploy; dump 5–8 real
   items and inspect their raw content.
3. Splitter with a byte-exact round-trip test on the dumps.
4. Glossary and UI strings (hand-written).
5. Pipeline with all checks (section 3) before the first real run,
   including serialized linking and id ordering.
6. Sample run, one language → fix → all languages → owner review.
   Report cost per page.
7. Tools, dynamic content, SEO layer, og images, switcher.
8. Full run (2 workers) → re-translate items that need it → cleanup
   dry run → cleanup → audit → menus.
9. Verify (section 5), then add the languages to `SMT_LIVE_LANGUAGES`;
   owner purges caches and resubmits the sitemap.
10. Schedule new-content translation + audit; tell the owner about
    credits auto-reload.
