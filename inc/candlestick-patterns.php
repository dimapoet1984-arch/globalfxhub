<?php
/**
 * The 45 candlestick patterns, each as its own "How to Read..." post
 * (cluster pages), linking up to the pillar guide at
 * /how-to-read-candlestick-patterns/ and cross-linking to related
 * patterns. Kept in their own file since functions.php was already
 * large before this.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Keyed by short slug; the real post slug is "how-to-read-{key}".
 * 'related' entries reference other keys in this same array.
 */
function globalfxhub_get_candlestick_patterns() {
    return array(
        'doji' => array(
            'title'   => 'How to Read a Doji Candlestick',
            'excerpt' => 'A doji forms when the open and close are almost identical -- here is what that indecision actually means and when it matters.',
            'candles' => 1,
            'signal'  => 'Indecision',
            'body'    => "<p>A doji forms when a period's open and close are virtually identical, leaving a body so thin it often looks like a plus sign, with wicks extending above and/or below. It shows that neither buyers nor sellers gained meaningful control during that period -- price moved, but ended up right back where it started.</p>\n<p>A doji in the middle of a quiet, range-bound market usually means little; the same shape appearing after a strong, extended trend is watched far more closely, since it can mark the point where the prevailing side is finally running out of conviction. On its own, though, a doji is a reason to pay attention, not a signal to act -- most traders wait for the next candle to confirm which way the balance actually tips.</p>",
            'related' => array( 'long-legged-doji', 'dragonfly-doji', 'gravestone-doji', 'spinning-top' ),
        ),
        'long-legged-doji' => array(
            'title'   => 'How to Read a Long-Legged Doji',
            'excerpt' => 'A doji with unusually long wicks on both sides -- a more extreme version of market indecision than a standard doji.',
            'candles' => 1,
            'signal'  => 'Indecision (extreme)',
            'body'    => "<p>A long-legged doji is a doji variant with unusually long upper <em>and</em> lower wicks relative to its tiny body, showing that price swung sharply in both directions during the period before settling almost exactly where it opened. Where a standard doji can reflect a fairly quiet stalemate, a long-legged doji reflects real volatility -- both sides pushed hard and neither one held the advantage.</p>\n<p>Because the range is so wide, a long-legged doji is often read as a sign that the market is unusually undecided about value at that price level, which can happen at turning points but also simply during high-volatility news events. Context (what happened just before it, and on what kind of volume) matters more here than for most single-candle patterns.</p>",
            'related' => array( 'doji', 'high-wave-candle', 'tri-star' ),
        ),
        'dragonfly-doji' => array(
            'title'   => 'How to Read a Dragonfly Doji',
            'excerpt' => 'A doji with a long lower wick and almost no upper wick -- a common bullish reversal signal after a downtrend.',
            'candles' => 1,
            'signal'  => 'Bullish reversal',
            'body'    => "<p>A dragonfly doji has a long lower wick and little to no upper wick, with the open, high, and close all sitting at or near the top of the period's range. It shows sellers pushed price sharply lower during the session, but buyers recovered essentially all of that ground by the close.</p>\n<p>Appearing after a downtrend, a dragonfly doji is read as a potential bullish reversal -- the long lower wick suggests a level where demand stepped in hard. As with any single-candle signal, it's generally treated as more meaningful when it lines up with an existing support level, and traders typically wait for the following candle to confirm the recovery holds before acting on it.</p>",
            'related' => array( 'doji', 'hammer', 'gravestone-doji' ),
        ),
        'gravestone-doji' => array(
            'title'   => 'How to Read a Gravestone Doji',
            'excerpt' => 'The mirror image of a dragonfly doji -- a long upper wick with almost no lower wick, often a bearish warning after a rally.',
            'candles' => 1,
            'signal'  => 'Bearish reversal',
            'body'    => "<p>A gravestone doji is the mirror image of a dragonfly: a long upper wick with little to no lower wick, and the open, low, and close all sitting near the bottom of the period's range. Buyers pushed price up during the session, but sellers took essentially all of it back by the close.</p>\n<p>Appearing after an uptrend, it's read as a potential bearish reversal signal -- the long upper wick shows a level where supply overwhelmed demand. Like most single-candle patterns, it carries more weight near an existing resistance level or round number, and it's usually treated as a warning to watch closely rather than an automatic sell signal on its own.</p>",
            'related' => array( 'doji', 'shooting-star', 'dragonfly-doji' ),
        ),
        'hammer' => array(
            'title'   => 'How to Read a Hammer Candlestick',
            'excerpt' => 'A small body near the top of the range with a long lower wick -- one of the most recognized bullish reversal signals.',
            'candles' => 1,
            'signal'  => 'Bullish reversal',
            'body'    => "<p>A hammer has a small body near the top of the candle's range, a lower wick at least roughly twice the length of the body, and little or no upper wick, appearing after a downtrend. It shows sellers pushed price well below the open during the session, but buyers stepped in hard enough to close the period back near where it started.</p>\n<p>The shape matters more than the color of the body -- a bullish or bearish hammer both send the same message. What separates a reliable hammer from noise is usually the wick-to-body ratio (the longer the lower wick relative to the body, the more decisive the rejection looks) and whether the next candle actually confirms the move higher; a hammer that isn't followed through often just gets revisited and broken.</p>",
            'related' => array( 'hanging-man', 'inverted-hammer', 'dragonfly-doji', 'bullish-engulfing' ),
        ),
        'inverted-hammer' => array(
            'title'   => 'How to Read an Inverted Hammer',
            'excerpt' => 'A small body near the bottom of the range with a long upper wick -- a tentative bullish signal that usually needs confirmation.',
            'candles' => 1,
            'signal'  => 'Bullish reversal',
            'body'    => "<p>An inverted hammer has a small body near the bottom of the range, a long upper wick, and little or no lower wick, appearing after a downtrend. It shows buyers tested meaningfully higher prices during the period, even though the close ended up back near the open rather than holding those gains.</p>\n<p>Because the close doesn't confirm the buying pressure the way a standard hammer's close does, an inverted hammer is generally treated as a weaker, more tentative version of a bullish reversal signal. Most traders wait for the following candle to close above the inverted hammer's body before treating the reversal as real, rather than acting on the inverted hammer alone.</p>",
            'related' => array( 'hammer', 'shooting-star', 'morning-star' ),
        ),
        'hanging-man' => array(
            'title'   => 'How to Read a Hanging Man Candlestick',
            'excerpt' => 'Structurally identical to a hammer, but appearing after an uptrend -- an early warning that buying momentum may be fading.',
            'candles' => 1,
            'signal'  => 'Bearish reversal',
            'body'    => "<p>A hanging man is structurally identical to a hammer -- a small body near the top of the range, a long lower wick, little upper wick -- but it appears after an <em>uptrend</em> instead of a downtrend. The same shape means something different depending on what came before it: here, it shows sellers were able to push price sharply lower intraday even while the broader trend was still up.</p>\n<p>That's exactly what makes it a warning sign rather than a straightforward bearish signal -- the close still finished near the high, so the bears haven't won yet, only shown they can push back. As with a hammer, confirmation from the next candle (a close below the hanging man's body) is what most traders look for before treating it as a genuine reversal.</p>",
            'related' => array( 'hammer', 'shooting-star', 'bearish-engulfing' ),
        ),
        'shooting-star' => array(
            'title'   => 'How to Read a Shooting Star Candlestick',
            'excerpt' => 'A small body near the bottom of the range with a long upper wick -- a common early warning near the top of a rally.',
            'candles' => 1,
            'signal'  => 'Bearish reversal',
            'body'    => "<p>A shooting star has a small body near the bottom of the range, a long upper wick, and little or no lower wick, appearing after an uptrend. It shows buyers pushed price higher during the period, but sellers took control and dragged it back down to close near the open.</p>\n<p>Like a hammer, the strength of a shooting star comes largely from the wick-to-body ratio -- a longer upper wick relative to the body suggests a more decisive rejection of higher prices. It's most meaningful near an existing resistance level or after an extended run higher, and, as with most single-candle signals, is usually paired with a wait for the next candle's confirmation before acting on it.</p>",
            'related' => array( 'hanging-man', 'inverted-hammer', 'gravestone-doji', 'evening-star' ),
        ),
        'spinning-top' => array(
            'title'   => 'How to Read a Spinning Top Candlestick',
            'excerpt' => 'A small body with wicks of similar length on both sides -- a milder cousin of the doji, showing a balanced tug-of-war.',
            'candles' => 1,
            'signal'  => 'Indecision',
            'body'    => "<p>A spinning top has a small body (bullish or bearish) with upper and lower wicks of roughly similar, moderate length. It's conceptually close to a doji but with a slightly larger body, showing a session where price moved meaningfully in both directions without either side gaining a decisive advantage.</p>\n<p>Spinning tops show up often in normal market activity and, in isolation, aren't a strong signal either way. Where they matter more is in sequence -- a string of spinning tops after a strong trend can reflect a market genuinely losing conviction, which is a different (and often more reliable) read than any single spinning top on its own.</p>",
            'related' => array( 'doji', 'high-wave-candle' ),
        ),
        'marubozu' => array(
            'title'   => 'How to Read a Marubozu Candle',
            'excerpt' => 'A candle with no wicks at all -- open-to-close in one continuous move, showing one side was in full control.',
            'candles' => 1,
            'signal'  => 'Strong continuation / conviction',
            'body'    => "<p>A marubozu has little or no wick at all -- just a long body, moving from open to close in one continuous direction. A bullish marubozu opens at the low and closes at the high of the period; a bearish marubozu opens at the high and closes at the low. Either way, one side was in complete control for the entire session, with no meaningful pushback from the other.</p>\n<p>Because there's no wick to suggest hesitation, a marubozu is generally read as a strong conviction signal -- appearing at the start of a move, it often signals strong follow-through in that direction; appearing after an extended trend, it can instead be a sign of capitulation or exhaustion. The surrounding context matters more here than for almost any other single-candle pattern.</p>",
            'related' => array( 'belt-hold', 'three-white-soldiers', 'three-black-crows' ),
        ),
        'high-wave-candle' => array(
            'title'   => 'How to Read a High Wave Candle',
            'excerpt' => 'A small body with unusually long wicks on both sides -- extreme volatility and disagreement within a single period.',
            'candles' => 1,
            'signal'  => 'Indecision',
            'body'    => "<p>A high wave candle has a very small body with unusually long wicks on both sides -- similar to a long-legged doji, but with a slightly more visible body rather than a near-flat line. It reflects extreme volatility and real disagreement between buyers and sellers within a single period.</p>\n<p>High wave candles often show up before a trend changes character, particularly after a strong, one-directional run -- the sudden appearance of a wide, indecisive range can mean the move has stopped attracting new participants in the same direction. As with other indecision patterns, it's a cue to watch the next few candles closely rather than a standalone trade signal.</p>",
            'related' => array( 'doji', 'long-legged-doji', 'spinning-top' ),
        ),
        'belt-hold' => array(
            'title'   => 'How to Read a Belt Hold Candlestick',
            'excerpt' => 'A candle that opens at its high or low and then runs hard the other way for the rest of the session.',
            'candles' => 1,
            'signal'  => 'Reversal',
            'body'    => "<p>A belt hold opens at (or very near) its high or low for the period and then moves strongly in one direction for the rest of the session, leaving little or no wick on the opening side. A bullish belt hold opens at the low and rallies from there; a bearish belt hold opens at the high and sells off from there.</p>\n<p>The pattern signals a decisive shift in control right from the opening bell -- there's no back-and-forth, just sustained pressure in one direction for the whole period. It's read the same way whether it appears after a downtrend (bullish belt hold) or an uptrend (bearish belt hold): a sudden, one-sided session that can mark the start of a reversal.</p>",
            'related' => array( 'marubozu', 'kicker-pattern' ),
        ),
        'bullish-engulfing' => array(
            'title'   => 'How to Read a Bullish Engulfing Pattern',
            'excerpt' => 'A small bearish candle followed by a larger bullish candle that swallows it whole -- one of the most watched reversal signals.',
            'candles' => 2,
            'signal'  => 'Bullish reversal',
            'body'    => "<p>A bullish engulfing pattern is a small bearish candle followed by a larger bullish candle whose body completely covers, or \"engulfs,\" the first candle's body. Appearing after a downtrend, it shows buyers didn't just stop the decline -- they reversed it decisively within a single session.</p>\n<p>It's one of the more widely followed two-candle reversal signals precisely because the second candle actively erases the first rather than merely pausing it. It tends to carry more weight when the engulfing candle trades on noticeably higher volume than the candle it swallows, and when it forms at an existing support level rather than in the middle of open range.</p>",
            'related' => array( 'bearish-engulfing', 'piercing-line', 'bullish-harami', 'three-outside-up' ),
        ),
        'bearish-engulfing' => array(
            'title'   => 'How to Read a Bearish Engulfing Pattern',
            'excerpt' => 'A small bullish candle followed by a larger bearish candle that swallows it whole -- often an early sign a rally has lost control.',
            'candles' => 2,
            'signal'  => 'Bearish reversal',
            'body'    => "<p>A bearish engulfing pattern is a small bullish candle followed by a larger bearish candle that completely covers its body. Appearing after an uptrend, it shows sellers overwhelmed the prior session's gains in one move, which is why it's often read as an early sign a rally has lost control to sellers.</p>\n<p>As with its bullish counterpart, a bearish engulfing pattern is generally treated as more significant near an existing resistance level and when the engulfing (bearish) candle trades on heavier volume than the one it swallows. A bearish engulfing candle that forms on unusually thin volume is more often just noise inside a still-intact uptrend.</p>",
            'related' => array( 'bullish-engulfing', 'dark-cloud-cover', 'bearish-harami', 'three-outside-down' ),
        ),
        'piercing-line' => array(
            'title'   => 'How to Read a Piercing Line Pattern',
            'excerpt' => 'A bearish candle followed by a bullish candle that closes above its midpoint -- a somewhat less decisive cousin of bullish engulfing.',
            'candles' => 2,
            'signal'  => 'Bullish reversal',
            'body'    => "<p>A piercing line is a bearish candle followed by a bullish candle that opens below the first candle's low but closes above the midpoint of the first candle's body -- without fully engulfing it (that would make it a bullish engulfing pattern instead). It shows a sharp rejection of lower prices partway through what looks like a developing recovery.</p>\n<p>Because the second candle recovers only part of the first candle's decline rather than all of it, a piercing line is generally read as a somewhat less forceful signal than a full bullish engulfing pattern -- real, but calling for more confirmation before assuming the trend has actually turned.</p>",
            'related' => array( 'bullish-engulfing', 'dark-cloud-cover', 'morning-star' ),
        ),
        'dark-cloud-cover' => array(
            'title'   => 'How to Read a Dark Cloud Cover Pattern',
            'excerpt' => 'The bearish mirror of a piercing line -- a bullish candle followed by a bearish candle that erases more than half its gain.',
            'candles' => 2,
            'signal'  => 'Bearish reversal',
            'body'    => "<p>Dark cloud cover is the mirror of a piercing line: a bullish candle followed by a bearish candle that opens above the first candle's high but closes below the midpoint of its body. It signals sellers took back more than half of the prior session's gains without fully engulfing the candle.</p>\n<p>Like a piercing line, it's read as a partial, warning-stage reversal signal rather than a decisive one -- real selling pressure showed up, but the bulls haven't been fully overrun yet. Traders typically watch for a follow-through bearish candle before treating it as confirmation that the uptrend has actually broken.</p>",
            'related' => array( 'bearish-engulfing', 'piercing-line', 'evening-star' ),
        ),
        'bullish-harami' => array(
            'title'   => 'How to Read a Bullish Harami',
            'excerpt' => 'A large bearish candle followed by a small bullish candle contained inside it -- momentum contracting after a decline.',
            'candles' => 2,
            'signal'  => 'Bullish reversal',
            'body'    => "<p>A bullish harami is a large bearish candle followed by a small bullish candle whose entire body sits inside the first candle's body -- the opposite arrangement of an engulfing pattern, where the second candle is smaller rather than larger. It shows momentum sharply contracting after a decline.</p>\n<p>A harami is generally treated as a more tentative signal than an engulfing pattern -- it shows selling pressure running out of steam rather than buyers actively taking control. Many traders wait for a third candle to close above the first candle's high before acting on it (see three inside up), rather than trading the two-candle harami alone.</p>",
            'related' => array( 'bearish-harami', 'harami-cross', 'three-inside-up', 'bullish-engulfing' ),
        ),
        'bearish-harami' => array(
            'title'   => 'How to Read a Bearish Harami',
            'excerpt' => 'A large bullish candle followed by a small bearish candle contained inside it -- a rally suddenly losing steam.',
            'candles' => 2,
            'signal'  => 'Bearish reversal',
            'body'    => "<p>A bearish harami is a large bullish candle followed by a small bearish candle contained entirely within its body. It shows a rally suddenly losing steam, with buyers unable to keep extending price the way they had in the prior session.</p>\n<p>As with a bullish harami, this is generally read as an early warning rather than a confirmed reversal -- momentum has stalled, but sellers haven't yet proven they can push price lower. A third candle closing below the first candle's low (see three inside down) is the more decisive confirmation most traders look for.</p>",
            'related' => array( 'bullish-harami', 'harami-cross', 'three-inside-down' ),
        ),
        'harami-cross' => array(
            'title'   => 'How to Read a Harami Cross',
            'excerpt' => 'A harami where the second candle is a doji -- generally read as a stronger version of the standard harami pattern.',
            'candles' => 2,
            'signal'  => 'Reversal (stronger indecision variant)',
            'body'    => "<p>A harami cross is a harami where the contained second candle is specifically a doji rather than just any small colored body. Because a doji on its own signals indecision, a harami cross combines that with the harami's \"momentum sharply contracting\" message.</p>\n<p>The result is generally read as a stronger version of the standard harami -- momentum hasn't just slowed, it's stalled outright at that level. It carries the same caveat as a standard harami, though: most traders treat it as an early warning that calls for confirmation from the next candle, not a standalone signal to reverse a position.</p>",
            'related' => array( 'bullish-harami', 'bearish-harami', 'doji' ),
        ),
        'tweezer-bottom' => array(
            'title'   => 'How to Read a Tweezer Bottom',
            'excerpt' => 'Two candles with matching lows after a downtrend -- a level that has now rejected selling twice in a row.',
            'candles' => 2,
            'signal'  => 'Bullish reversal',
            'body'    => "<p>A tweezer bottom is two consecutive candles (their colors can vary) whose lows land at almost exactly the same price, appearing after a downtrend. The matching lows show that a specific price level has now rejected further selling twice in a row.</p>\n<p>The pattern is essentially a very short-term support test: the market tried the same level twice and failed to break it both times. It tends to be more convincing when the second candle is bullish and closes well off its low, and less convincing in a fast, high-volatility market where matching lows can simply be coincidence.</p>",
            'related' => array( 'tweezer-top', 'hammer', 'bullish-engulfing' ),
        ),
        'tweezer-top' => array(
            'title'   => 'How to Read a Tweezer Top',
            'excerpt' => 'Two candles with matching highs after an uptrend -- a level that has now rejected buying twice in a row.',
            'candles' => 2,
            'signal'  => 'Bearish reversal',
            'body'    => "<p>A tweezer top is the mirror image of a tweezer bottom: two consecutive candles whose highs land at almost exactly the same price, appearing after an uptrend. It shows a specific level has now twice rejected further buying, and may be acting as resistance.</p>\n<p>As with a tweezer bottom, the signal is stronger when the second candle is bearish and closes well off its high, and weaker in fast-moving conditions where two similar highs could just as easily be coincidence as a genuine rejection of the level.</p>",
            'related' => array( 'tweezer-bottom', 'shooting-star', 'bearish-engulfing' ),
        ),
        'kicker-pattern' => array(
            'title'   => 'How to Read a Kicker Pattern',
            'excerpt' => 'A candle followed by one that gaps sharply the other way with no overlap at all -- one of the more forceful reversal signals.',
            'candles' => 2,
            'signal'  => 'Strong reversal',
            'body'    => "<p>A kicker pattern is a candle followed by one that gaps sharply in the opposite direction and never overlaps the first candle's range at all -- the two candles look like they belong to entirely different price zones. A bullish kicker gaps up from a bearish candle; a bearish kicker gaps down from a bullish one.</p>\n<p>Because there's a genuine gap rather than just an overlapping engulf, a kicker is generally read as one of the more forceful reversal signals, often tied to a sudden shift in news or sentiment rather than gradual technical exhaustion. The size of the gap and whether it holds (rather than being immediately filled) both matter to how much weight traders put on it.</p>",
            'related' => array( 'belt-hold', 'marubozu', 'bullish-engulfing', 'bearish-engulfing' ),
        ),
        'on-neck-pattern' => array(
            'title'   => 'How to Read an On-Neck Pattern',
            'excerpt' => 'A weak bounce within a downtrend that barely reaches the prior candle\'s low -- usually read as continuation, not reversal.',
            'candles' => 2,
            'signal'  => 'Bearish continuation',
            'body'    => "<p>An on-neck pattern is a bearish candle followed by a small bullish candle that closes at, or just above, the prior candle's low, within an existing downtrend. Because the \"recovery\" barely makes it back to the previous close, it's read as a weak, unconvincing bounce.</p>\n<p>Unlike the reversal patterns on this list, an on-neck pattern is a continuation signal -- it suggests the downtrend is likely to resume rather than turn. The key detail is just how little ground the second candle recovers; a bounce that closes meaningfully higher would no longer qualify as this pattern.</p>",
            'related' => array( 'in-neck-pattern', 'thrusting-pattern' ),
        ),
        'in-neck-pattern' => array(
            'title'   => 'How to Read an In-Neck Pattern',
            'excerpt' => 'Very similar to an on-neck pattern -- a small bounce that closes just inside the prior candle\'s close, within a downtrend.',
            'candles' => 2,
            'signal'  => 'Bearish continuation',
            'body'    => "<p>An in-neck pattern closely resembles an on-neck pattern: a bearish candle followed by a small bullish candle that closes just inside (slightly above) the prior candle's close, within a downtrend. The bounce is marginally stronger than an on-neck pattern's, but still modest.</p>\n<p>Like an on-neck pattern, it's read as a continuation signal rather than a reversal -- the attempted recovery falls well short of reclaiming meaningful ground, and the existing downtrend is expected to resume. The distinction between an in-neck and on-neck pattern mostly matters for precise classification; the trading implication is essentially the same.</p>",
            'related' => array( 'on-neck-pattern', 'thrusting-pattern' ),
        ),
        'thrusting-pattern' => array(
            'title'   => 'How to Read a Thrusting Pattern',
            'excerpt' => 'A slightly stronger bounce than on-neck or in-neck, but still one that usually falls short of a genuine reversal.',
            'candles' => 2,
            'signal'  => 'Bearish continuation',
            'body'    => "<p>A thrusting pattern is a bearish candle followed by a bullish candle that closes above the midpoint of the prior candle's body but still below its open, within a downtrend. It's a somewhat stronger bounce than an on-neck or in-neck pattern, since it recovers more than half of the prior decline.</p>\n<p>Even so, because the close doesn't reach the first candle's open, a thrusting pattern generally still falls short of signaling a genuine reversal and is treated as continuation until proven otherwise -- the key threshold to watch is whether the next candle actually closes above that open.</p>",
            'related' => array( 'on-neck-pattern', 'in-neck-pattern', 'piercing-line' ),
        ),
        'meeting-lines' => array(
            'title'   => 'How to Read Meeting Lines',
            'excerpt' => 'Two candles of opposite color whose closes land at the same price -- the two sides fighting to a standstill at one level.',
            'candles' => 2,
            'signal'  => 'Reversal',
            'body'    => "<p>Meeting lines are two candles of opposite color whose closes land at almost the same price, even though they open at different levels and move in different directions throughout their sessions. The matching closes show the two sides fought to a standstill at a specific price.</p>\n<p>A bullish meeting line forms after a downtrend (bearish candle, then a bullish candle closing at the same level); a bearish meeting line forms after an uptrend. Either way, the pattern is read similarly to a tweezer -- a level being defended -- but with the added detail that it's the <em>close</em>, not the high or low, that's matching.</p>",
            'related' => array( 'tweezer-bottom', 'tweezer-top', 'harami-cross' ),
        ),
        'morning-star' => array(
            'title'   => 'How to Read a Morning Star Pattern',
            'excerpt' => 'Decline, pause, strong recovery -- one of the most recognized three-candle bottoming patterns.',
            'candles' => 3,
            'signal'  => 'Bullish reversal',
            'body'    => "<p>A morning star is a bearish candle, followed by a small-bodied candle that gaps down (the \"star\"), followed by a bullish candle that closes well back into the first candle's body. The shape -- decline, pause, strong recovery -- is one of the most widely recognized three-candle bottoming patterns.</p>\n<p>The star candle is the key: it shows the decline momentarily stalling before the third candle confirms buyers have taken control. The pattern is generally read as more reliable the further the third candle closes back into the first candle's body, and especially when the star candle itself is a doji (see morning doji star).</p>",
            'related' => array( 'evening-star', 'morning-doji-star', 'three-inside-up', 'bullish-engulfing' ),
        ),
        'evening-star' => array(
            'title'   => 'How to Read an Evening Star Pattern',
            'excerpt' => 'The bearish mirror of a morning star -- rally, pause, sharp reversal, appearing at the top of an uptrend.',
            'candles' => 3,
            'signal'  => 'Bearish reversal',
            'body'    => "<p>An evening star is the mirror image of a morning star: a bullish candle, a small gapping-up star candle, then a bearish candle that closes back into the first candle's body. It marks the same decline-pause-recovery shape as a morning star, just inverted at the top of an uptrend.</p>\n<p>As with a morning star, the middle star candle is the pivot -- it shows the rally stalling before the third candle confirms sellers have taken over. The further the third candle closes back into the first candle's body, the more decisive the reversal is generally considered.</p>",
            'related' => array( 'morning-star', 'evening-doji-star', 'three-inside-down', 'bearish-engulfing' ),
        ),
        'morning-doji-star' => array(
            'title'   => 'How to Read a Morning Doji Star',
            'excerpt' => 'A morning star whose middle candle is specifically a doji -- generally read as a stronger bullish signal.',
            'candles' => 3,
            'signal'  => 'Bullish reversal (stronger)',
            'body'    => "<p>A morning doji star is a morning star where the middle \"star\" candle is specifically a doji rather than just any small-bodied candle. The added indecision of a doji sitting right at the pattern's pivot point is generally read as a stronger version of the standard morning star.</p>\n<p>The logic is straightforward: a doji already signals a stalemate on its own, so finding one at exactly the point where a downtrend is supposedly turning adds extra weight to the reversal case. As with a standard morning star, the size and conviction of the third (bullish) candle still matters for confirming the signal.</p>",
            'related' => array( 'morning-star', 'evening-doji-star', 'doji' ),
        ),
        'evening-doji-star' => array(
            'title'   => 'How to Read an Evening Doji Star',
            'excerpt' => 'An evening star whose middle candle is a doji -- the stronger bearish counterpart to the morning doji star.',
            'candles' => 3,
            'signal'  => 'Bearish reversal (stronger)',
            'body'    => "<p>An evening doji star is an evening star whose middle candle is a doji rather than a generic small body, making it the bearish counterpart to a morning doji star. The doji at the pivot point adds extra weight to the reversal signal compared to a standard evening star.</p>\n<p>As with the morning doji star, the added indecision of a true doji at the exact turning point is what separates this from a standard evening star -- but the strength of the reversal still depends on how convincingly the third (bearish) candle closes back into the first candle's body.</p>",
            'related' => array( 'evening-star', 'morning-doji-star', 'doji' ),
        ),
        'three-white-soldiers' => array(
            'title'   => 'How to Read Three White Soldiers',
            'excerpt' => 'Three consecutive bullish candles, each closing at a new high -- steady, sustained buying rather than one sharp move.',
            'candles' => 3,
            'signal'  => 'Bullish continuation / reversal',
            'body'    => "<p>Three white soldiers is three consecutive bullish candles, each opening within the prior candle's body and closing at a new high with a relatively small wick. It shows steady, sustained buying spread across three full sessions rather than one sharp spike.</p>\n<p>Because the buying is spread out and each candle closes near its high, the pattern is generally read as a strong signal of genuine demand. The one caveat worth knowing: three unusually long, fast candles in a row can also indicate a short-term overextension, so some traders watch for the bodies to stay a reasonably consistent size rather than accelerating sharply.</p>",
            'related' => array( 'three-black-crows', 'marubozu', 'rising-three-methods' ),
        ),
        'three-black-crows' => array(
            'title'   => 'How to Read Three Black Crows',
            'excerpt' => 'The bearish mirror of three white soldiers -- three consecutive candles closing at new lows, showing sustained selling.',
            'candles' => 3,
            'signal'  => 'Bearish continuation / reversal',
            'body'    => "<p>Three black crows is the mirror image of three white soldiers: three consecutive bearish candles, each opening within the prior candle's body and closing at a new low. It reflects the same sustained, steady character as three white soldiers, just to the downside.</p>\n<p>As with three white soldiers, the pattern's strength comes from the buying (or here, selling) being spread evenly across three sessions rather than compressed into one sharp move, which is generally read as more sustainable pressure than a single large candle -- though, again, a very fast run of three can also warn of a short-term oversold bounce.</p>",
            'related' => array( 'three-white-soldiers', 'identical-three-crows', 'falling-three-methods' ),
        ),
        'three-inside-up' => array(
            'title'   => 'How to Read a Three Inside Up Pattern',
            'excerpt' => 'A bullish harami plus a third confirming candle -- the extra step that turns a tentative signal into a decisive one.',
            'candles' => 3,
            'signal'  => 'Bullish reversal (confirmed)',
            'body'    => "<p>A three inside up pattern is a bullish harami (a large bearish candle, then a small bullish candle inside it) followed by a third bullish candle that closes above the first candle's high. That third candle is the confirmation that turns a harami from a tentative signal into a more decisive reversal call.</p>\n<p>Because a standalone harami only shows momentum contracting, not reversing, many traders specifically wait for this third-candle confirmation before acting -- which is exactly what this pattern represents. It's essentially the harami pattern plus the proof that buyers actually followed through.</p>",
            'related' => array( 'bullish-harami', 'three-outside-up', 'morning-star' ),
        ),
        'three-inside-down' => array(
            'title'   => 'How to Read a Three Inside Down Pattern',
            'excerpt' => 'The bearish equivalent of three inside up -- a bearish harami confirmed by a third candle closing below the first candle\'s low.',
            'candles' => 3,
            'signal'  => 'Bearish reversal (confirmed)',
            'body'    => "<p>A three inside down pattern is a bearish harami followed by a third bearish candle that closes below the first candle's low, confirming the reversal. It's the direct bearish equivalent of three inside up.</p>\n<p>As with its bullish counterpart, this pattern exists specifically to address the main weakness of a standalone harami -- that it only shows momentum stalling, not actually reversing. The third candle's close below the first candle's low is what turns that stall into confirmed follow-through selling.</p>",
            'related' => array( 'bearish-harami', 'three-outside-down', 'evening-star' ),
        ),
        'three-outside-up' => array(
            'title'   => 'How to Read a Three Outside Up Pattern',
            'excerpt' => 'A bullish engulfing pattern followed by a third bullish candle closing higher still -- confirmation rather than a single-session event.',
            'candles' => 3,
            'signal'  => 'Bullish reversal (confirmed)',
            'body'    => "<p>A three outside up pattern is a bullish engulfing pattern followed by a third bullish candle that closes higher still. The extra candle confirms the engulfing signal rather than leaving it as a single-session event that could easily be reversed the next day.</p>\n<p>Bullish engulfing patterns are already a fairly strong signal on their own, so this confirmed version is generally treated as one of the more reliable multi-candle bullish setups on this list -- two full sessions of sustained buying following a decisive reversal candle.</p>",
            'related' => array( 'bullish-engulfing', 'three-inside-up' ),
        ),
        'three-outside-down' => array(
            'title'   => 'How to Read a Three Outside Down Pattern',
            'excerpt' => 'A bearish engulfing pattern followed by a third bearish candle closing lower still, confirming the reversal.',
            'candles' => 3,
            'signal'  => 'Bearish reversal (confirmed)',
            'body'    => "<p>A three outside down pattern is a bearish engulfing pattern followed by a third bearish candle that closes lower still, confirming the reversal rather than leaving it as a single session's move. It's the direct bearish equivalent of three outside up.</p>\n<p>Because it builds on an already-strong bearish engulfing signal with a full session of follow-through selling, it's generally treated as one of the more reliable multi-candle bearish setups covered here.</p>",
            'related' => array( 'bearish-engulfing', 'three-inside-down' ),
        ),
        'abandoned-baby' => array(
            'title'   => 'How to Read an Abandoned Baby Pattern',
            'excerpt' => 'A doji isolated by gaps on both sides -- rare in practice, but one of the more decisive reversal signals when it appears.',
            'candles' => 3,
            'signal'  => 'Rare, strong reversal',
            'body'    => "<p>An abandoned baby is a candle, followed by a doji that gaps completely away from it (no overlap in range at all), followed by a third candle that gaps back in the opposite direction from the doji. The doji effectively sits isolated between two gaps.</p>\n<p>The two clean gaps on either side of an isolated doji make this a genuinely rare pattern in real price data, but that rarity is part of why it's taken seriously when it does appear -- it takes a real shift in sentiment (often around news) to produce gaps on both sides of a single session.</p>",
            'related' => array( 'tri-star', 'morning-doji-star', 'evening-doji-star' ),
        ),
        'tri-star' => array(
            'title'   => 'How to Read a Tri-Star Pattern',
            'excerpt' => 'Three consecutive doji candles with a gap at the middle one -- three periods of indecision in a row, often marking exhaustion.',
            'candles' => 3,
            'signal'  => 'Rare reversal',
            'body'    => "<p>A tri-star pattern is three consecutive doji candles, with the middle one gapping away from the other two. Like the abandoned baby, its rarity is part of what makes it notable -- three separate periods of indecision in a row, with a gap at the pivot point.</p>\n<p>The pattern generally shows up after an extended trend and is read as a sign the trend has become fully exhausted -- not one session of hesitation, but three straight. Because true tri-stars are uncommon, most traders won't see one in a given instrument for a long stretch of time.</p>",
            'related' => array( 'abandoned-baby', 'doji', 'long-legged-doji' ),
        ),
        'upside-gap-two-crows' => array(
            'title'   => 'How to Read an Upside Gap Two Crows Pattern',
            'excerpt' => 'A gap higher followed by two bearish candles that don\'t fully give back the gap -- an uptrend running into trouble.',
            'candles' => 3,
            'signal'  => 'Bearish',
            'body'    => "<p>An upside gap two crows pattern is a bullish candle, followed by a bearish candle that gaps up and doesn't fill that gap, followed by a second bearish candle that closes lower still but remains above the first candle's close. It shows an uptrend running into trouble right after a gap higher.</p>\n<p>Because the gap isn't fully filled by the two bearish candles, the pattern is read as an early warning rather than a full reversal -- two sessions of selling pressure have shown up, but the bulls haven't yet given back all of their prior gains. Whether the gap eventually gets filled is what most traders watch for next.</p>",
            'related' => array( 'identical-three-crows', 'three-black-crows', 'evening-star' ),
        ),
        'identical-three-crows' => array(
            'title'   => 'How to Read Identical Three Crows',
            'excerpt' => 'Three similar bearish candles opening at the prior close with almost no wick -- a steadier, more mechanical version of three black crows.',
            'candles' => 3,
            'signal'  => 'Bearish',
            'body'    => "<p>Identical three crows is three consecutive bearish candles of similar size, each opening at or very near the prior candle's close, with little to no wick. It's essentially a steadier, more mechanical version of three black crows.</p>\n<p>The lack of any real wick on each candle, and the fact that each one opens right where the last one closed, shows consistent selling with no real attempt at a bounce between sessions -- generally read as a sign of persistent, low-drama distribution rather than panic selling.</p>",
            'related' => array( 'three-black-crows', 'upside-gap-two-crows' ),
        ),
        'three-stars-in-the-south' => array(
            'title'   => 'How to Read Three Stars in the South',
            'excerpt' => 'Three candles within a downtrend showing shrinking ranges and rising lows -- selling pressure drying up gradually rather than reversing sharply.',
            'candles' => 3,
            'signal'  => 'Rare bullish',
            'body'    => "<p>Three stars in the south is three candles within a downtrend, each showing a progressively smaller range and a higher low than the one before it, with the last candle a small, marubozu-like body. It suggests selling pressure steadily drying up session by session rather than reversing all at once.</p>\n<p>Unlike sharper reversal patterns like a morning star or a bullish engulfing candle, this one is a gradual signal -- there's no single dramatic candle, just a quiet contraction in range that can precede a bottom. It's one of the rarer named patterns and takes some practice to spot reliably.</p>",
            'related' => array( 'hammer', 'morning-star', 'ladder-bottom' ),
        ),
        'rising-three-methods' => array(
            'title'   => 'How to Read Rising Three Methods',
            'excerpt' => 'A strong bullish candle, a brief pause, then another strong bullish candle -- an uptrend consolidating rather than reversing.',
            'candles' => 5,
            'signal'  => 'Bullish continuation',
            'body'    => "<p>Rising three methods is a long bullish candle, followed by three small bearish (or mixed) candles that stay within the first candle's range, followed by another long bullish candle that closes at a new high. It shows an uptrend pausing to consolidate for a few sessions without actually reversing, then resuming.</p>\n<p>The key detail is that the three small middle candles stay contained within the first candle's range -- if they break below it, the setup no longer qualifies and the bullish continuation case weakens considerably. It's a pattern about patience: three quiet sessions that don't undo the prior move, followed by a resumption of it.</p>",
            'related' => array( 'falling-three-methods', 'mat-hold', 'three-white-soldiers' ),
        ),
        'falling-three-methods' => array(
            'title'   => 'How to Read Falling Three Methods',
            'excerpt' => 'The bearish mirror of rising three methods -- a downtrend pausing to consolidate before continuing lower.',
            'candles' => 5,
            'signal'  => 'Bearish continuation',
            'body'    => "<p>Falling three methods is the mirror image of rising three methods: a long bearish candle, three small consolidating candles contained within its range, then another long bearish candle to a new low. It shows a downtrend pausing and then continuing rather than reversing.</p>\n<p>As with the bullish version, the three middle candles staying inside the first candle's range is what defines the pattern -- if the consolidation pushes back above that range, it starts to look more like an actual reversal attempt than a pause within the downtrend.</p>",
            'related' => array( 'rising-three-methods', 'three-black-crows' ),
        ),
        'mat-hold' => array(
            'title'   => 'How to Read a Mat Hold Pattern',
            'excerpt' => 'Similar to rising three methods, but the pullback is allowed to drift a bit further before the uptrend resumes.',
            'candles' => 5,
            'signal'  => 'Bullish continuation',
            'body'    => "<p>A mat hold pattern is similar to rising three methods, but the small consolidating candles are allowed to drift slightly below the first candle's close (not just stay within its full range) before the final bullish candle pushes to a new high.</p>\n<p>It's read the same way as rising three methods -- a pause within an uptrend rather than a reversal -- just with a bit more give in how far the pullback is allowed to go before the pattern still counts. In practice, many traders treat mat hold and rising three methods as close enough in meaning that the distinction mostly matters for precise classification.</p>",
            'related' => array( 'rising-three-methods', 'three-white-soldiers' ),
        ),
        'ladder-bottom' => array(
            'title'   => 'How to Read a Ladder Bottom Pattern',
            'excerpt' => 'Steady selling, a rejected low, then a confirmed rally -- a multi-stage bottoming pattern rather than a single reversal candle.',
            'candles' => 5,
            'signal'  => 'Bullish reversal',
            'body'    => "<p>A ladder bottom pattern is three consecutive bearish candles with progressively lower closes (similar to the start of three black crows), followed by a small-bodied candle with a long lower wick, followed by a bullish candle that gaps up and closes higher.</p>\n<p>The shift across the sequence -- from steady selling, to a rejected low (the small-bodied candle with the long wick), to a confirmed gap-up rally -- makes this a multi-stage bottoming pattern rather than a single reversal candle. Because it takes five full sessions to complete, it's generally read as a more deliberate, confirmed bottom than faster patterns like a hammer or a morning star.</p>",
            'related' => array( 'three-stars-in-the-south', 'hammer', 'morning-star' ),
        ),
    );
}

/**
 * The "Every pattern, explained" index section appended to the pillar
 * guide (how-to-read-candlestick-patterns). Built from the same data as
 * the individual posts, so it never drifts out of sync with what
 * actually exists.
 */
function globalfxhub_candlestick_pattern_index_html() {
    $patterns = globalfxhub_get_candlestick_patterns();
    $by_candles = array();
    foreach ( $patterns as $key => $p ) {
        $by_candles[ $p['candles'] ][ $key ] = $p;
    }
    ksort( $by_candles );
    $labels = array(
        1 => 'Single-candle patterns',
        2 => 'Two-candle patterns',
        3 => 'Three-candle patterns',
        5 => 'Four- and five-candle patterns',
    );

    $html = '<h2 id="candlestick-pattern-index">Every pattern, explained on its own page</h2>' . "\n";
    $html .= "<p>The eight patterns above cover the essentials. Each of the 45 named patterns below has its own in-depth page covering exactly how to identify it, what it signals, and how reliable it tends to be.</p>\n";
    foreach ( $by_candles as $count => $group ) {
        $label = isset( $labels[ $count ] ) ? $labels[ $count ] : ( $count . '-candle patterns' );
        $html .= '<h3>' . esc_html( $label ) . "</h3>\n<ul>\n";
        foreach ( $group as $key => $p ) {
            $html .= '<li><a href="' . esc_url( home_url( '/how-to-read-' . $key . '/' ) ) . '">' . esc_html( $p['title'] ) . '</a></li>' . "\n";
        }
        $html .= "</ul>\n";
    }
    return $html;
}

/**
 * Ensures the pillar guide links down to all 45 pattern pages. Idempotent
 * (checks for the index heading's id before appending), and works whether
 * the pillar post was just created or already existed on a live site
 * from before this feature shipped.
 */
function globalfxhub_ensure_pattern_index_in_pillar() {
    $post = get_page_by_path( 'how-to-read-candlestick-patterns', OBJECT, 'post' );
    if ( ! $post ) {
        return;
    }
    if ( false !== strpos( $post->post_content, 'id="candlestick-pattern-index"' ) ) {
        return;
    }
    wp_update_post( array(
        'ID'           => $post->ID,
        'post_content' => $post->post_content . "\n\n" . globalfxhub_candlestick_pattern_index_html(),
    ) );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_pattern_index_in_pillar', 20 );

/**
 * Auto-create the 45 pattern posts (in their own "Candlestick Patterns"
 * category, kept separate from "Guides" so they don't clutter the main
 * /guides/ listing -- they're reachable via the pillar guide's index
 * above and each other's "Related patterns" links instead).
 */
function globalfxhub_ensure_candlestick_pattern_posts() {
    $term = term_exists( 'Candlestick Patterns', 'category' );
    if ( ! $term ) {
        $term = wp_insert_term( 'Candlestick Patterns', 'category', array( 'slug' => 'candlestick-patterns' ) );
    }
    if ( is_wp_error( $term ) || empty( $term['term_id'] ) ) {
        return;
    }
    $category_id = (int) $term['term_id'];
    $patterns = globalfxhub_get_candlestick_patterns();
    $parent_url = home_url( '/how-to-read-candlestick-patterns/' );

    foreach ( $patterns as $key => $pattern ) {
        $slug = 'how-to-read-' . $key;
        if ( get_page_by_path( $slug, OBJECT, 'post' ) ) {
            continue;
        }

        $content  = '<p><a href="' . esc_url( $parent_url ) . '">&larr; Back to the full guide: How to Read Candlestick Patterns</a></p>' . "\n\n";
        $content .= '<p><strong>' . esc_html( $pattern['candles'] ) . '-candle pattern &middot; ' . esc_html( $pattern['signal'] ) . '</strong></p>' . "\n\n";
        $content .= $pattern['body'] . "\n\n";

        if ( ! empty( $pattern['related'] ) ) {
            $content .= "<h2>Related patterns</h2>\n<ul>\n";
            foreach ( $pattern['related'] as $rel_key ) {
                if ( isset( $patterns[ $rel_key ] ) ) {
                    $content .= '<li><a href="' . esc_url( home_url( '/how-to-read-' . $rel_key . '/' ) ) . '">' . esc_html( $patterns[ $rel_key ]['title'] ) . '</a></li>' . "\n";
                }
            }
            $content .= "</ul>\n\n";
        }

        $content .= '<p>This article is educational only and isn\'t a recommendation to trade any particular instrument or pattern -- no candlestick pattern works in isolation. See the <a href="' . esc_url( $parent_url ) . '">full candlestick guide</a> for how patterns like this one fit into a broader approach.</p>';

        wp_insert_post( array(
            'post_title'    => $pattern['title'],
            'post_name'     => $slug,
            'post_excerpt'  => $pattern['excerpt'],
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_category' => array( $category_id ),
        ) );
    }
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_candlestick_pattern_posts', 21 );
