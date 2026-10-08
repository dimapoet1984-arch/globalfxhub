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
