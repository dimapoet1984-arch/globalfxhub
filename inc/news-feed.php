<?php
/**
 * FX Market News: a low-volume, high-depth weekly digest, not a daily
 * feed. Scans a fixed list of finance/markets RSS feeds purely to spot
 * genuinely high-impact triggers (Fed/ECB decisions, CPI/NFP releases,
 * major geopolitical/market shocks), then has the Claude API write an
 * original 300-700 word analysis piece for each one -- never a
 * reproduction or close paraphrase of the source's own article, and
 * never published at all if the source excerpt is too thin to support
 * genuine analysis without inventing detail.
 *
 * This deliberately replaces an earlier version of this file that
 * published up to 10 short (2-3 sentence) items per day from the same
 * feeds. That pattern -- high volume, thin per-item substance, excerpted
 * from wire sources -- is close to what Google's own guidance on scaled
 * content abuse describes: mass-generated or lightly-reworked content
 * published primarily to build indexed volume rather than for its own
 * sake. The fix isn't to do the same thing more carefully; it's to
 * publish far less, and make every published item substantive enough
 * to stand on its own. At most 3 items/week now, and only for stories
 * that clear a real significance bar.
 *
 * Broker-specific news (fines, licence actions, acquisitions, etc.) is
 * handled entirely separately in inc/broker-news.php, as a curated,
 * non-syndicated feed -- it never flows through this RSS pipeline.
 *
 * Mirrors the Twelve Data integration's shape on purpose: fetch on a
 * schedule (never per-pageview), a minimum-gap guard independent of the
 * schedule itself, graceful per-item failure (one dead/blocked feed never
 * breaks the run), and an admin-only debug endpoint for live verification.
 *
 * The feed URLs below could not be verified from this environment --
 * outbound HTTPS to every candidate finance-news domain is blocked by
 * this sandbox's own egress policy, independent of the live production
 * server. Confirm which ones actually resolve via the debug endpoint
 * (?globalfxhub_news_debug=1, while logged in as an administrator) once
 * this is deployed, and prune/replace any that 404 or redirect.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GLOBALFXHUB_NEWS_SEEN_OPTION', 'globalfxhub_news_seen_urls' );
define( 'GLOBALFXHUB_NEWS_TOPIC_ATTACHMENTS_OPTION', 'globalfxhub_news_topic_attachments' );

/**
 * Candidate sources, used only to spot "something happened" triggers
 * (headline, short excerpt, link, timestamp) -- never as content to
 * reproduce. Unverified from this sandbox -- see the file header.
 */
function globalfxhub_news_feed_sources() {
    return array(
        array( 'name' => 'MarketWatch — Top Stories',  'url' => 'https://feeds.content.dowjones.io/public/rss/mw_topstories' ),
        array( 'name' => 'MarketWatch — Market Pulse', 'url' => 'https://feeds.content.dowjones.io/public/rss/mw_marketpulse' ),
        array( 'name' => 'CNBC — Markets',              'url' => 'https://www.cnbc.com/id/20910258/device/rss/rss.html' ),
        array( 'name' => 'CNBC — Top News',             'url' => 'https://www.cnbc.com/id/100003114/device/rss/rss.html' ),
        array( 'name' => 'Investing.com — Forex News',  'url' => 'https://www.investing.com/rss/news_1.rss' ),
        array( 'name' => 'ForexLive',                   'url' => 'https://www.forexlive.com/feed/' ),
        array( 'name' => 'DailyFX',                      'url' => 'https://www.dailyfx.com/feeds/all' ),
        array( 'name' => 'FXStreet — News',             'url' => 'https://www.fxstreet.com/rss/news' ),
        array( 'name' => 'Kitco News',                  'url' => 'https://www.kitco.com/rss/KitcoNews.xml' ),
        array( 'name' => 'Yahoo Finance',               'url' => 'https://finance.yahoo.com/news/rssindex' ),
    );
}

/**
 * Weighted keywords for the "biggest market impact" ranking. Higher
 * weight = more market-moving. Combined with a recency bonus so a
 * high-impact story from just now beats a high-impact story from three
 * days ago.
 */
function globalfxhub_news_impact_keywords() {
    return array(
        3 => array( 'fed', 'federal reserve', 'fomc', 'rate decision', 'rate hike', 'rate cut', 'ecb', 'interest rate', 'cpi', 'inflation', 'non-farm payrolls', 'nfp', 'jobs report', 'recession' ),
        2 => array( 'central bank', 'gdp', 'unemployment', 'treasury yield', 'opec', 'crude oil', 'war', 'sanctions', 'tariff', 'trade deal', 'geopolitical' ),
        1 => array( 'dollar', 'euro', 'pound', 'yen', 'gold', 'stock market', 'wall street', 'earnings', 'bitcoin', 'crypto', 'forex', 'currency' ),
    );
}

function globalfxhub_news_score_item( $text, $age_hours ) {
    $text_lower = strtolower( $text );
    $score = 0.0;
    foreach ( globalfxhub_news_impact_keywords() as $weight => $keywords ) {
        foreach ( $keywords as $kw ) {
            if ( false !== strpos( $text_lower, $kw ) ) {
                $score += $weight;
            }
        }
    }
    // Decays to 0 by 96h old; worth up to 3 points for a brand-new story.
    $recency_bonus = max( 0, ( 96 - $age_hours ) / 96 ) * 3;
    return $score + $recency_bonus;
}

/**
 * Minimum score a candidate must clear before we even attempt to
 * generate analysis for it. This is the "actually significant" bar --
 * a lone low-weight keyword plus recency alone (max 1 + 3 = 4) isn't
 * enough; it takes a real impact-keyword hit (a weight-2 or weight-3
 * term) layered on top. Deliberately high: this is a weekly digest of
 * genuinely market-moving stories, not a quota to fill.
 */
define( 'GLOBALFXHUB_NEWS_MIN_SCORE', 5.0 );

/**
 * Keyword -> featured-image topic, and keyword -> the specific
 * instrument whose "why this matters" angle the analysis prompt should
 * anchor on. Picks whichever topic has the most keyword hits in the
 * article; 'general' is the fallback when nothing matches.
 */
function globalfxhub_news_topic_keywords() {
    return array(
        'rates'    => array( 'fed', 'federal reserve', 'fomc', 'rate decision', 'rate hike', 'rate cut', 'ecb', 'european central bank', 'bank of england', 'boe', 'bank of japan', 'boj', 'central bank', 'interest rate', 'monetary policy' ),
        'gold'     => array( 'gold', 'xau', 'bullion', 'precious metal', 'silver' ),
        'oil'      => array( 'oil', 'crude', 'opec', 'brent', 'wti', 'natural gas', 'energy price' ),
        'crypto'   => array( 'bitcoin', 'crypto', 'ethereum', 'blockchain', 'btc', 'eth' ),
        'stocks'   => array( 'stock market', 'wall street', 's&p 500', 'nasdaq', 'dow jones', 'equities', 'shares', 'earnings' ),
        'econdata' => array( 'cpi', 'inflation', 'gdp', 'unemployment', 'non-farm payrolls', 'nfp', 'jobs report', 'retail sales', 'pmi', 'economic data' ),
        'forex'    => array( 'forex', 'currency', 'currencies', 'eur/usd', 'gbp/usd', 'usd/jpy', 'dollar index', 'exchange rate', 'dollar', 'euro', 'pound', 'yen' ),
    );
}

/**
 * The instrument/angle each topic's analysis should explicitly address
 * under the "Why this matters for ___" subheading the generation prompt
 * requires.
 */
function globalfxhub_news_topic_instrument_label( $topic ) {
    $map = array(
        'rates'    => 'EUR/USD and the broader US-dollar majors',
        'gold'     => 'gold (XAU/USD)',
        'oil'      => 'oil (WTI and Brent) and commodity-linked currencies like CAD and NOK',
        'crypto'   => 'Bitcoin and broader risk-asset sentiment',
        'stocks'   => 'broad risk sentiment and safe-haven FX flows',
        'econdata' => 'EUR/USD and whichever currency is most directly tied to the data released',
        'forex'    => 'EUR/USD and other major currency pairs',
        'general'  => 'broader FX market sentiment',
    );
    return isset( $map[ $topic ] ) ? $map[ $topic ] : $map['general'];
}

/**
 * Title mentions count double, since the headline is by far the
 * strongest signal of what an article is actually about -- without this,
 * a crypto headline ("Bitcoin dips as $400M in crypto longs are
 * liquidated") could lose to 'rates' on a tie just because 'rates' is
 * listed first in globalfxhub_news_topic_keywords() and a routine
 * "central bank"/"monetary policy" mention in the body (common boilerplate
 * in almost any macro story) reached the same hit count -- confirmed live:
 * exactly this Bitcoin headline got tagged with the Central Banks & Rates
 * image instead of the crypto one.
 */
function globalfxhub_news_classify_topic( $title, $summary = '' ) {
    $title_lower   = strtolower( $title );
    $summary_lower = strtolower( $summary );
    $best_topic = 'general';
    $best_score = 0;
    foreach ( globalfxhub_news_topic_keywords() as $topic => $keywords ) {
        $score = 0;
        foreach ( $keywords as $kw ) {
            if ( false !== strpos( $title_lower, $kw ) ) {
                $score += 2;
            } elseif ( false !== strpos( $summary_lower, $kw ) ) {
                $score += 1;
            }
        }
        if ( $score > $best_score ) {
            $best_score = $score;
            $best_topic = $topic;
        }
    }
    return $best_topic;
}

/**
 * Injects up to $max_links contextual links into already-sanitized HTML:
 * a mentioned broker's own review page first, then whichever existing
 * guide the text is about. Caps total links so a short summary doesn't
 * turn into a wall of anchors. Names shorter than 3 characters (e.g.
 * "IG", "XM") are skipped -- too likely to false-positive inside
 * ordinary text.
 */
function globalfxhub_news_inject_internal_links( $html, $max_links = 2 ) {
    $links_added = 0;

    foreach ( globalfxhub_get_brokers() as $broker ) {
        if ( $links_added >= $max_links ) {
            break;
        }
        if ( strlen( $broker['name'] ) < 3 ) {
            continue;
        }
        $pattern = '/\b(' . preg_quote( $broker['name'], '/' ) . ')\b/i';
        if ( preg_match( $pattern, $html ) ) {
            $url = home_url( '/reviews/' . $broker['slug'] . '/' );
            $html = preg_replace( $pattern, '<a href="' . esc_url( $url ) . '">$1</a>', $html, 1 );
            $links_added++;
        }
    }

    $guide_map = array(
        'leverage'                                => home_url( '/learn/understanding-leverage-in-forex-trading/' ),
        'candlestick(?:s|\spatterns?)?'            => home_url( '/learn/how-to-read-candlestick-patterns/' ),
        '(?:compare brokers|broker comparison)'    => home_url( '/compare/' ),
        '(?:start trading|trading forex|forex trading)' => home_url( '/learn/how-to-start-trading-forex/' ),
    );
    foreach ( $guide_map as $pattern_words => $url ) {
        if ( $links_added >= $max_links ) {
            break;
        }
        $pattern = '/\b(' . $pattern_words . ')\b/i';
        if ( preg_match( $pattern, $html ) ) {
            $html = preg_replace( $pattern, '<a href="' . esc_url( $url ) . '">$1</a>', $html, 1 );
            $links_added++;
        }
    }

    return $html;
}

function globalfxhub_news_topic_image_path( $topic ) {
    $map = array(
        'rates'    => 'rates.png',
        'forex'    => 'forex.png',
        'gold'     => 'gold.png',
        'oil'      => 'oil.png',
        'stocks'   => 'stocks.png',
        'crypto'   => 'crypto.png',
        'econdata' => 'econdata.png',
        'general'  => 'general.png',
    );
    $file = isset( $map[ $topic ] ) ? $map[ $topic ] : $map['general'];
    return get_template_directory() . '/assets/news/' . $file;
}

/**
 * Accessible alt text for each topic image -- the_post_thumbnail() calls
 * in front-page.php and page-news.php don't pass an alt override, so
 * this is the only thing standing between a screen reader and an empty
 * alt="" on every news card.
 */
function globalfxhub_news_topic_image_alt( $topic ) {
    $map = array(
        'rates'    => 'Illustration representing central bank and interest rate news',
        'forex'    => 'Illustration representing forex market news',
        'gold'     => 'Illustration representing gold and precious metals news',
        'oil'      => 'Illustration representing oil and energy market news',
        'stocks'   => 'Illustration representing stock market news',
        'crypto'   => 'Illustration representing cryptocurrency market news',
        'econdata' => 'Illustration representing economic data news',
        'general'  => 'Illustration representing general market news',
    );
    return isset( $map[ $topic ] ) ? $map[ $topic ] : $map['general'];
}

/**
 * One real media attachment per topic, created once and reused across
 * every article tagged with that topic (WordPress featured images don't
 * need a unique attachment per post). Re-checks the cached ID still
 * resolves to a real post before trusting it, so a manually-deleted
 * attachment self-heals instead of leaving every future news post
 * without a featured image.
 */
function globalfxhub_news_get_topic_attachment_id( $topic ) {
    $cache = get_option( GLOBALFXHUB_NEWS_TOPIC_ATTACHMENTS_OPTION, array() );
    if ( ! is_array( $cache ) ) {
        $cache = array();
    }
    if ( ! empty( $cache[ $topic ] ) && get_post( $cache[ $topic ] ) ) {
        // Backfill alt text on an attachment created before this check
        // existed -- cheap no-op once set.
        if ( '' === get_post_meta( $cache[ $topic ], '_wp_attachment_image_alt', true ) ) {
            update_post_meta( $cache[ $topic ], '_wp_attachment_image_alt', globalfxhub_news_topic_image_alt( $topic ) );
        }
        return (int) $cache[ $topic ];
    }

    $path = globalfxhub_news_topic_image_path( $topic );
    if ( ! file_exists( $path ) ) {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $filename = 'news-' . sanitize_key( $topic ) . '.png';
    $contents = file_get_contents( $path );
    if ( false === $contents ) {
        return 0;
    }

    $upload = wp_upload_bits( $filename, null, $contents );
    if ( ! empty( $upload['error'] ) ) {
        return 0;
    }

    $attach_id = wp_insert_attachment( array(
        'post_mime_type' => 'image/png',
        'post_title'     => 'GlobalFXHub News — ' . ucfirst( $topic ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ), $upload['file'] );

    if ( is_wp_error( $attach_id ) || ! $attach_id ) {
        return 0;
    }

    $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
    wp_update_attachment_metadata( $attach_id, $attach_data );
    update_post_meta( $attach_id, '_wp_attachment_image_alt', globalfxhub_news_topic_image_alt( $topic ) );

    $cache[ $topic ] = $attach_id;
    update_option( GLOBALFXHUB_NEWS_TOPIC_ATTACHMENTS_OPTION, $cache, false );

    return $attach_id;
}

/**
 * Generates an original 300-700 word analysis article for one trigger
 * story via the Claude API -- genuinely substantive and specific to
 * that headline/excerpt, never a reworded reproduction of the source,
 * and never fabricating facts the excerpt doesn't contain. Requires a
 * "Why this matters for {instrument}" section addressing the specific
 * pair/asset most relevant to the story's topic.
 *
 * Raw HTTP via wp_remote_post(), matching this theme's existing
 * external-API pattern (see inc/market-data.php) rather than the
 * Anthropic PHP SDK -- this is a plain WordPress theme with no Composer
 * dependency management, so pulling in the SDK would be a heavier,
 * inconsistent addition versus a single wp_remote_post() call.
 *
 * Requires the site's Anthropic API key to be defined in wp-config.php
 * (never in this repo, since it's a public theme):
 *   define( 'GLOBALFXHUB_ANTHROPIC_API_KEY', 'sk-ant-xxxxxxxxxxxx' );
 * Until that constant exists, or if the call ever fails for any reason
 * (network error, rate limit, malformed response), or if the model
 * itself says the excerpt is too thin to analyze honestly, this returns
 * null and the candidate is skipped entirely -- no post gets published
 * for it. A failure or thin-material verdict here never produces a
 * stub post and never fabricates analysis by inventing specifics.
 */
function globalfxhub_news_generate_analysis( $title, $summary, $source, $instrument_label ) {
    if ( ! defined( 'GLOBALFXHUB_ANTHROPIC_API_KEY' ) || ! GLOBALFXHUB_ANTHROPIC_API_KEY ) {
        return null;
    }

    $system_prompt = 'You are a financial-markets analyst writing an original FX-market analysis article for a forex/CFD broker review website, based only on a headline and short excerpt from a wire source -- you do not have access to the full source article.'
        . ' Write 300-700 words of ORIGINAL prose (never the source\'s own wording, never a close paraphrase of it) as plain HTML using only <p> and <strong>/<em> tags, structured as:'
        . ' (1) an opening paragraph giving context -- what happened and the immediate market backdrop;'
        . ' (2) one or two paragraphs on the likely mechanism and what traders are watching for next;'
        . ' (3) a paragraph that starts with exactly this subheading as its own tag, <h3>Why this matters for ' . $instrument_label . '</h3>, followed by a <p> explaining the specific, clearly-hedged ("could", "may", "if confirmed") implications for that instrument;'
        . ' (4) a short closing paragraph on what would change the picture (an upcoming data release, a central bank meeting, etc).'
        . ' Rules: never give direct trading advice (no "buy", "sell", "you should"); never restate the headline verbatim; never invent specific numbers, prices, dates, or facts beyond what the excerpt actually provides; hedge explicitly around anything not stated outright in the excerpt.'
        . ' If the excerpt is too thin to support genuine, specific 300-700 word analysis without inventing detail, respond with exactly the single line NOT_ENOUGH_MATERIAL and nothing else -- do not pad with filler or generic restatement to hit the length.';

    $user_prompt = "Headline: {$title}\nSource: {$source}\nExcerpt: {$summary}\n\nWrite the analysis article now.";

    $response = wp_remote_post( 'https://api.anthropic.com/v1/messages', array(
        'timeout' => 30,
        'headers' => array(
            'x-api-key'         => GLOBALFXHUB_ANTHROPIC_API_KEY,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ),
        'body' => wp_json_encode( array(
            'model'      => 'claude-sonnet-4-5',
            'max_tokens' => 1400,
            'system'     => $system_prompt,
            'messages'   => array(
                array( 'role' => 'user', 'content' => $user_prompt ),
            ),
        ) ),
    ) );

    if ( is_wp_error( $response ) ) {
        error_log( 'GlobalFXHub news analysis: request failed -- ' . $response->get_error_message() );
        return null;
    }

    $code = (int) wp_remote_retrieve_response_code( $response );
    if ( 200 !== $code ) {
        error_log( 'GlobalFXHub news analysis: HTTP ' . $code . ' -- ' . substr( wp_remote_retrieve_body( $response ), 0, 300 ) );
        return null;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $body ) || empty( $body['content'] ) || ! is_array( $body['content'] ) ) {
        return null;
    }

    $text = null;
    foreach ( $body['content'] as $block ) {
        if ( isset( $block['type'] ) && 'text' === $block['type'] && ! empty( $block['text'] ) ) {
            $text = trim( $block['text'] );
            break;
        }
    }
    if ( ! $text ) {
        return null;
    }
    if ( 'NOT_ENOUGH_MATERIAL' === $text ) {
        return null;
    }

    // Sanitize to the small allowed tag set regardless of what the model
    // returned, then enforce a real minimum word count -- a model that
    // ignored the length instruction without using the sentinel string
    // still shouldn't result in a thin post.
    $clean = wp_kses( $text, array(
        'p'      => array(),
        'h3'     => array(),
        'strong' => array(),
        'em'     => array(),
    ) );
    $word_count = count( preg_split( '/\s+/', trim( wp_strip_all_tags( $clean ) ) ) );
    if ( $word_count < 180 ) {
        return null;
    }

    return $clean;
}

/**
 * Creates one published post for a ranked, already-analyzed candidate:
 * a category of "FX Market News", the Claude-generated analysis article
 * as the body (with contextual internal links woven in), an attributed
 * linked-back source line, a topic-classified featured image, and the
 * postmeta the templates and dedupe logic rely on (_news_source_url).
 */
function globalfxhub_create_news_post( array $candidate, $analysis ) {
    $term = term_exists( 'FX Market News', 'category' );
    if ( ! $term ) {
        $term = wp_insert_term( 'FX Market News', 'category', array( 'slug' => 'fx-market-news' ) );
    }
    if ( is_wp_error( $term ) || empty( $term['term_id'] ) ) {
        return 0;
    }
    $category_id = (int) $term['term_id'];

    $topic = globalfxhub_news_classify_topic( $candidate['title'], $candidate['summary'] );

    $summary_trimmed = globalfxhub_trim_excerpt( $candidate['summary'], 40 );
    if ( ! $summary_trimmed ) {
        $summary_trimmed = 'Original analysis of a market-moving development -- see the source below for the underlying report.';
    }

    $content = globalfxhub_news_inject_internal_links( $analysis );
    $content .= "\n\n" . '<p>Source: <a href="' . esc_url( $candidate['link'] ) . '" rel="nofollow noopener" target="_blank">' . esc_html( $candidate['source'] ) . '</a>.</p>';

    $post_id = wp_insert_post( array(
        'post_title'    => wp_strip_all_tags( $candidate['title'] ),
        'post_content'  => $content,
        'post_excerpt'  => $summary_trimmed,
        'post_status'   => 'publish',
        'post_type'     => 'post',
        'post_category' => array( $category_id ),
    ) );

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        return 0;
    }

    update_post_meta( $post_id, '_news_source_url', $candidate['link'] );
    update_post_meta( $post_id, '_news_source_name', $candidate['source'] );
    update_post_meta( $post_id, '_byline', 'markets-editor' );

    $attach_id = globalfxhub_news_get_topic_attachment_id( $topic );
    if ( $attach_id ) {
        set_post_thumbnail( $post_id, $attach_id );
    }

    return $post_id;
}

/**
 * The pipeline: pull every feed (one failing/blocked feed never stops
 * the rest), score every item found across all of them together,
 * discard anything under the significance bar, then attempt real
 * analysis for the remainder in score order, publishing only the ones
 * that clear the length/substance bar in globalfxhub_news_generate_analysis()
 * -- up to 3 per run, skipping (not stubbing) anything too thin.
 * Deduped by source URL, both against a running "seen" list and a
 * direct postmeta lookup so a manually-added article is respected too.
 *
 * Guards against running too often independent of the cron schedule --
 * at most once every 7 days, no matter what triggers it. This is a
 * weekly digest by design, not a daily feed: see the file header for why.
 */
function globalfxhub_fetch_and_publish_news( $force = false ) {
    $last_run = (int) get_option( 'globalfxhub_news_last_run_at', 0 );
    if ( ! $force && ( time() - $last_run ) < 7 * DAY_IN_SECONDS ) {
        return array( 'ok' => false, 'reason' => 'Skipped: ran ' . ( time() - $last_run ) . 's ago, under the 7-day minimum gap.' );
    }
    update_option( 'globalfxhub_news_last_run_at', time(), false );

    $candidates    = array();
    $feed_results  = array();

    foreach ( globalfxhub_news_feed_sources() as $source ) {
        $feed = fetch_feed( $source['url'] );
        if ( is_wp_error( $feed ) ) {
            $feed_results[ $source['name'] ] = 'error: ' . $feed->get_error_message();
            continue;
        }

        $items = $feed->get_items( 0, 15 );
        $feed_results[ $source['name'] ] = count( $items ) . ' item(s)';

        foreach ( $items as $item ) {
            $link = $item->get_permalink();
            if ( ! $link ) {
                continue;
            }
            $published = (int) $item->get_date( 'U' );
            if ( ! $published ) {
                $published = time();
            }
            $age_hours = ( time() - $published ) / HOUR_IN_SECONDS;
            if ( $age_hours > 168 || $age_hours < 0 ) {
                continue; // Older than this digest's own 7-day window, or clock-skewed into the future.
            }

            $title = wp_strip_all_tags( (string) $item->get_title() );
            if ( ! $title ) {
                continue;
            }
            $summary = wp_strip_all_tags( (string) $item->get_description() );

            $score = globalfxhub_news_score_item( $title . ' ' . $summary, $age_hours );
            if ( $score < GLOBALFXHUB_NEWS_MIN_SCORE ) {
                continue; // Doesn't clear the "genuinely significant" bar -- not a candidate at all.
            }

            $candidates[] = array(
                'title'     => $title,
                'summary'   => $summary,
                'link'      => $link,
                'source'    => $source['name'],
                'published' => $published,
                'score'     => $score,
            );
        }
    }

    if ( empty( $candidates ) ) {
        return array( 'ok' => false, 'reason' => 'No candidate articles cleared the significance bar across any feed this week.', 'feed_results' => $feed_results );
    }

    usort( $candidates, function( $a, $b ) {
        return $b['score'] <=> $a['score'];
    } );

    $seen = get_option( GLOBALFXHUB_NEWS_SEEN_OPTION, array() );
    if ( ! is_array( $seen ) ) {
        $seen = array();
    }

    $created       = array();
    $skipped_dupe  = 0;
    $skipped_thin  = 0;

    foreach ( $candidates as $candidate ) {
        if ( count( $created ) >= 3 ) {
            break;
        }
        $hash = md5( $candidate['link'] );
        if ( in_array( $hash, $seen, true ) ) {
            $skipped_dupe++;
            continue;
        }
        $existing = get_posts( array(
            'post_type'      => 'post',
            'meta_key'       => '_news_source_url',
            'meta_value'     => $candidate['link'],
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ) );
        if ( ! empty( $existing ) ) {
            $seen[] = $hash;
            $skipped_dupe++;
            continue;
        }

        $topic = globalfxhub_news_classify_topic( $candidate['title'], $candidate['summary'] );
        $instrument_label = globalfxhub_news_topic_instrument_label( $topic );
        $analysis = globalfxhub_news_generate_analysis( $candidate['title'], $candidate['summary'], $candidate['source'], $instrument_label );

        // Always mark seen once we've genuinely evaluated it, win or lose
        // -- a thin-material verdict is a real answer about this specific
        // story, not a transient failure worth retrying next week.
        $seen[] = $hash;

        if ( ! $analysis ) {
            $skipped_thin++;
            continue;
        }

        $post_id = globalfxhub_create_news_post( $candidate, $analysis );
        if ( $post_id ) {
            $created[] = array( 'id' => $post_id, 'title' => $candidate['title'], 'score' => round( $candidate['score'], 2 ) );
        }
    }

    if ( count( $seen ) > 500 ) {
        $seen = array_slice( $seen, -500 );
    }
    update_option( GLOBALFXHUB_NEWS_SEEN_OPTION, $seen, false );

    return array(
        'ok'                    => true,
        'created'               => $created,
        'skipped_dupe'          => $skipped_dupe,
        'skipped_thin'          => $skipped_thin,
        'candidates_considered' => count( $candidates ),
        'feed_results'          => $feed_results,
    );
}

/**
 * Weekly is the actual cadence (enforced by the 7-day minimum-gap guard
 * inside the fetch function itself, independent of this schedule) --
 * scheduled daily only so a missed/delayed cron tick still catches up
 * promptly rather than silently waiting for next week's exact slot.
 */
function globalfxhub_schedule_news_cron() {
    if ( ! wp_next_scheduled( 'globalfxhub_fetch_news_event' ) ) {
        wp_schedule_event( time(), 'daily', 'globalfxhub_fetch_news_event' );
    }
}
add_action( 'wp', 'globalfxhub_schedule_news_cron' );
add_action( 'globalfxhub_fetch_news_event', 'globalfxhub_fetch_and_publish_news' );

function globalfxhub_unschedule_news_cron() {
    wp_clear_scheduled_hook( 'globalfxhub_fetch_news_event' );
}
add_action( 'switch_theme', 'globalfxhub_unschedule_news_cron' );

/**
 * Admin-only diagnostic: visit ?globalfxhub_news_debug=1 while logged in
 * as an administrator to force an immediate run and see exactly which
 * feeds resolved, how many articles each returned, and what got
 * published, skipped as a duplicate, or skipped as too thin to analyze
 * honestly. Remove once the feed list is confirmed working live
 * (matching how the Twelve Data debug endpoint was handled).
 */
function globalfxhub_news_debug() {
    if ( ! isset( $_GET['globalfxhub_news_debug'] ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    header( 'Content-Type: text/plain; charset=utf-8' );
    echo "GlobalFXHub News Pipeline Debug\n================================\n\n";
    echo print_r( globalfxhub_fetch_and_publish_news( true ), true );
    exit;
}
add_action( 'init', 'globalfxhub_news_debug' );

/**
 * Lets any post be marked noindex via postmeta (_news_noindex) -- the
 * mechanism a lower-quality legacy post could be flagged with without
 * unpublishing it outright, and that nothing this pipeline currently
 * creates needs, since every post it makes now has to clear the
 * substance bar above before it's ever published.
 */
function globalfxhub_maybe_noindex_head() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }
    if ( get_post_meta( get_the_ID(), '_news_noindex', true ) ) {
        echo '<meta name="robots" content="noindex,follow">' . "\n";
    }
}
add_action( 'wp_head', 'globalfxhub_maybe_noindex_head', 1 );
