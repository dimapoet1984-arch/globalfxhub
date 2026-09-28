<?php
/**
 * Daily market-news digest: scans a fixed list of finance/markets RSS
 * feeds, scores each article by a keyword+recency heuristic (no LLM/API
 * calls, so this costs nothing to run), and publishes the 10
 * highest-impact articles as normal posts in the "News" category --
 * summarized in our own words length-wise (a trimmed excerpt of the
 * source's own description, never the full article), always attributed
 * and linked back to the original source.
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
 * Candidate sources. Ten general finance/markets outlets, favoring ones
 * that plausibly still run a public RSS feed. Unverified from this
 * sandbox -- see the file header.
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
 * Keyword -> featured-image topic. Picks whichever topic has the most
 * keyword hits in the article; 'general' is the fallback when nothing
 * matches.
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

function globalfxhub_news_classify_topic( $text ) {
    $text_lower = strtolower( $text );
    $best_topic = 'general';
    $best_hits  = 0;
    foreach ( globalfxhub_news_topic_keywords() as $topic => $keywords ) {
        $hits = 0;
        foreach ( $keywords as $kw ) {
            if ( false !== strpos( $text_lower, $kw ) ) {
                $hits++;
            }
        }
        if ( $hits > $best_hits ) {
            $best_hits  = $hits;
            $best_topic = $topic;
        }
    }
    return $best_topic;
}

/**
 * Names shorter than 3 characters (e.g. "IG", "XM") are skipped -- too
 * likely to false-positive inside ordinary text -- so those brokers just
 * never get tagged as the subject of a news item, which is a fine
 * trade-off for avoiding wrong tags.
 */
function globalfxhub_news_detect_broker( $text ) {
    $text_lower = strtolower( $text );
    foreach ( globalfxhub_get_brokers() as $broker ) {
        $name_lower = strtolower( $broker['name'] );
        if ( strlen( $name_lower ) < 3 ) {
            continue;
        }
        if ( false !== strpos( $text_lower, $name_lower ) ) {
            return $broker;
        }
    }
    return null;
}

/**
 * Injects up to $max_links contextual links into an already-esc_html'd
 * text: a mentioned broker's own review page first, then whichever
 * existing guide the text is about. Caps total links so a short summary
 * doesn't turn into a wall of anchors.
 */
function globalfxhub_news_inject_internal_links( $escaped_text, $max_links = 2 ) {
    $links_added = 0;

    foreach ( globalfxhub_get_brokers() as $broker ) {
        if ( $links_added >= $max_links ) {
            break;
        }
        if ( strlen( $broker['name'] ) < 3 ) {
            continue;
        }
        $pattern = '/\b(' . preg_quote( $broker['name'], '/' ) . ')\b/i';
        if ( preg_match( $pattern, $escaped_text ) ) {
            $url = home_url( '/reviews/' . $broker['slug'] . '/' );
            $escaped_text = preg_replace( $pattern, '<a href="' . esc_url( $url ) . '">$1</a>', $escaped_text, 1 );
            $links_added++;
        }
    }

    $guide_map = array(
        'leverage'                                => home_url( '/guides/understanding-leverage-in-forex-trading/' ),
        'candlestick(?:s|\spatterns?)?'            => home_url( '/guides/how-to-read-candlestick-patterns/' ),
        '(?:compare brokers|broker comparison)'    => home_url( '/compare/' ),
        '(?:start trading|trading forex|forex trading)' => home_url( '/guides/how-to-start-trading-forex/' ),
    );
    foreach ( $guide_map as $pattern_words => $url ) {
        if ( $links_added >= $max_links ) {
            break;
        }
        $pattern = '/\b(' . $pattern_words . ')\b/i';
        if ( preg_match( $pattern, $escaped_text ) ) {
            $escaped_text = preg_replace( $pattern, '<a href="' . esc_url( $url ) . '">$1</a>', $escaped_text, 1 );
            $links_added++;
        }
    }

    return $escaped_text;
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

    $cache[ $topic ] = $attach_id;
    update_option( GLOBALFXHUB_NEWS_TOPIC_ATTACHMENTS_OPTION, $cache, false );

    return $attach_id;
}

/**
 * Creates one published post for a ranked candidate article: a category
 * of "News", a short excerpt of the source's own description (never the
 * full article text) plus an attributed, linked-back source line,
 * contextual internal links woven into that excerpt, a topic-classified
 * featured image, and the postmeta the templates and dedupe logic rely
 * on (_news_source_url, _news_type, _byline).
 */
function globalfxhub_create_news_post( array $candidate ) {
    $term = term_exists( 'News', 'category' );
    if ( ! $term ) {
        $term = wp_insert_term( 'News', 'category', array( 'slug' => 'news' ) );
    }
    if ( is_wp_error( $term ) || empty( $term['term_id'] ) ) {
        return 0;
    }
    $category_id = (int) $term['term_id'];

    $text_blob = $candidate['title'] . ' ' . $candidate['summary'];
    $topic     = globalfxhub_news_classify_topic( $text_blob );
    $broker    = globalfxhub_news_detect_broker( $text_blob );
    $news_type = $broker ? 'broker' : 'market';

    $summary_trimmed = globalfxhub_trim_excerpt( $candidate['summary'], 55 );
    if ( ! $summary_trimmed ) {
        $summary_trimmed = 'Read the full story at the source below.';
    }
    $summary_linked = globalfxhub_news_inject_internal_links( esc_html( $summary_trimmed ) );

    $content = '<p>' . $summary_linked . '</p>' . "\n\n"
        . '<p>Source: <a href="' . esc_url( $candidate['link'] ) . '" rel="nofollow noopener" target="_blank">' . esc_html( $candidate['source'] ) . '</a>.</p>';

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
    update_post_meta( $post_id, '_news_type', $news_type );
    update_post_meta( $post_id, '_byline', 'markets-editor' );
    if ( $broker ) {
        update_post_meta( $post_id, '_news_broker_slug', $broker['slug'] );
    }

    $attach_id = globalfxhub_news_get_topic_attachment_id( $topic );
    if ( $attach_id ) {
        set_post_thumbnail( $post_id, $attach_id );
    }

    return $post_id;
}

/**
 * The pipeline: pull every feed (one failing/blocked feed never stops
 * the rest), score every item found across all of them together, then
 * publish the top 10 that aren't already-published duplicates (deduped
 * by source URL, both against a running "seen" list and a direct
 * postmeta lookup so a manually-added article is respected too).
 *
 * Guards against running too often independent of the cron schedule --
 * at most once every 12 hours, no matter what triggers it.
 */
function globalfxhub_fetch_and_publish_news( $force = false ) {
    $last_run = (int) get_option( 'globalfxhub_news_last_run_at', 0 );
    if ( ! $force && ( time() - $last_run ) < 12 * HOUR_IN_SECONDS ) {
        return array( 'ok' => false, 'reason' => 'Skipped: ran ' . ( time() - $last_run ) . 's ago, under the 12-hour minimum gap.' );
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
            if ( $age_hours > 96 || $age_hours < 0 ) {
                continue; // Older than 4 days, or clock-skewed into the future.
            }

            $title = wp_strip_all_tags( (string) $item->get_title() );
            if ( ! $title ) {
                continue;
            }
            $summary = wp_strip_all_tags( (string) $item->get_description() );

            $candidates[] = array(
                'title'     => $title,
                'summary'   => $summary,
                'link'      => $link,
                'source'    => $source['name'],
                'published' => $published,
                'score'     => globalfxhub_news_score_item( $title . ' ' . $summary, $age_hours ),
            );
        }
    }

    if ( empty( $candidates ) ) {
        return array( 'ok' => false, 'reason' => 'No candidate articles parsed from any feed.', 'feed_results' => $feed_results );
    }

    usort( $candidates, function( $a, $b ) {
        return $b['score'] <=> $a['score'];
    } );

    $seen = get_option( GLOBALFXHUB_NEWS_SEEN_OPTION, array() );
    if ( ! is_array( $seen ) ) {
        $seen = array();
    }

    $created      = array();
    $skipped_dupe = 0;

    foreach ( $candidates as $candidate ) {
        if ( count( $created ) >= 10 ) {
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

        $post_id = globalfxhub_create_news_post( $candidate );
        if ( $post_id ) {
            $created[] = array( 'id' => $post_id, 'title' => $candidate['title'], 'score' => round( $candidate['score'], 2 ) );
            $seen[] = $hash;
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
        'candidates_considered' => count( $candidates ),
        'feed_results'          => $feed_results,
    );
}

/**
 * Once a day is plenty for a "scan overnight, publish a digest" job --
 * loosen via the 'daily' -> a custom schedule if faster turnaround is
 * ever wanted; the 12-hour guard inside the fetch function itself stops
 * that from ever running too often regardless.
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
 * published or skipped. Remove once the feed list is confirmed working
 * live (matching how the Twelve Data debug endpoint was handled).
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
