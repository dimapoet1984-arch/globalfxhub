# Project notes for Claude

## Keep the homepage hero banner in sync with the rankings

Whenever broker data, scores, or ranks change in a way that moves who's
in the top positions (new research, a scoring-formula change, a broker
correction), check `front-page.php`'s hero banner carousel (`hero1_h1`/
`hero1_p`/etc. in `inc/translatable-strings.php`) for any broker-specific
claim -- a name, a score, a "#1" claim -- and update it if it no longer
matches reality. The "#1 broker" award section right below the rankings
table already pulls the real #1 broker live (`$fp_top15[0]` in
`front-page.php`), so it self-updates; the hero banner's copy does not,
since it's plain translatable text, so it needs a manual check each time.

## Content strategy (set 2026-11-09)

No more large batches of generic "What is X?" articles. New content mix,
by proportion of new pieces: 30% commercial research (broker comparisons),
25% broker-specific research (fees/regulation deep-dives), 20%
regulation/safety, 15% original research/data (unique, citable reports
built from this site's own broker dataset -- put disproportionate effort
into these, they're the assets other sites cite), 10% evergreen education.

**Pace:** ~4 substantial new articles/week, not 10-20/day -- a sensible
monthly target is ~16 new articles, 10-15 existing-page updates, and
10-20 broker data refreshes. Publish when a piece is actually ready, not
on a mechanical schedule (no "exactly 09:00 every Monday" just to look
human) -- some weeks 3 articles + 7 updates, others 5 + 2. During a
regulatory event or major broker change, update the review/Change
Log/comparison instead of forcing an unrelated new article. The site
should behave like a maintained publication, not simulate one.

**Three publishing queues**, not one blog queue -- think of work this way
rather than a single backlog: Editorial (comparisons, regulation, broker
research), Data (pricing, licences, fees, platforms, Change Log), and
Optimization (existing-page updates, internal links, merging weak pages).
For the next 3-6 months, split ongoing effort roughly 40% new content /
60% improving and verifying what already exists -- with ~599 existing
guides/articles, that balance grows authority faster than just growing
the page count.

**Before publishing any new article**, work through: (1) primary search
intent, (2) parent topical hub, (3) 3-5 relevant existing informational
pages, (4) relevant GlobalFXHub tool, (5) relevant Best Brokers page,
(6) relevant broker review(s), (7) relevant comparison page, (8) at
least one authoritative external source, (9) whether this site's own
broker data can add an original table/statistic/finding, (10) which
existing pages should link back to the new article. Never publish
something that substantially duplicates an existing page. Never
fabricate a statistic, broker data point, or search-volume claim. After
publishing, add contextual internal links from at least 3 existing
relevant pages. Translate new content into all of this site's live
languages as part of publishing it, not as a separate deferred step.

**Weekly maintenance audit** (report-only, never auto-applies changes --
get explicit sign-off before touching anything it finds): pricing data
older than 60 days; regulation data older than 90 days; newly broken
internal/external links; orphan pages; pages with declining Search
Console clicks/impressions; pages ranking 5-20 with optimization
potential; high-impression/weak-CTR pages; keyword cannibalization;
stale year/date references; outdated broker claims; newly changed
broker data; pages missing useful internal links; indexation problems;
factual contradictions between structured broker data and editorial
copy. Return at most 10 recommended actions, ranked by expected impact.
Several of these checks need Search Console/GA data this environment
doesn't always have access to -- say so explicitly per check rather than
guessing or silently skipping it.
