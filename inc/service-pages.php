<?php
/**
 * The standard informational/legal pages linked from the footer (About,
 * How we test, Why trust us, Advertiser disclosure, Terms, Privacy,
 * Contact). None of these had real pages behind them yet -- visiting any
 * of these footer links 404'd.
 *
 * Content here sticks to what's actually true about this site and its
 * methodology (already established and used elsewhere in the theme) --
 * nothing about company history, team size/credentials, or years of
 * experience is invented. Two things use sensible defaults you should
 * confirm rather than verified facts: the contact email
 * (hello@globalfxhub.net -- make sure that inbox actually exists and is
 * monitored) and the Terms of Use page, which is a general informational
 * framework, not something reviewed by a lawyer for your jurisdiction.
 *
 * Self-heals like the rest of the theme's provisioning: creates a page
 * if the slug doesn't exist yet, and fills in content only if an
 * existing page's content is empty -- it never overwrites a page you've
 * since edited by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_get_service_pages() {
    return array(
        'about' => array(
            'title'   => 'About GlobalFXHub',
            'content' => "<p>GlobalFXHub is an independent research site covering forex and CFD brokers licensed by CySEC, the UK's FCA, the Seychelles FSA, and other regulators worldwide. We built it because comparing brokers is harder than it should be: marketing pages rarely state regulatory status clearly, \"top 10\" lists are often paid placements dressed up as rankings, and the details that actually matter -- licence status, cost, platform choice, how long a broker has actually been operating -- are scattered across dozens of sources.</p>\n<p>What we do is straightforward: we cross-reference brokers against the official public register for whichever regulator(s) license them, compile their regulatory footprint, cost structure, platform offering, and operating history from public disclosures, and score them using one disclosed, consistent methodology across the whole set. See <a href=\"/how-we-test/\">How we test</a> for exactly how that works.</p>\n<p>We do not test broker accounts hands-on, and we say so plainly wherever a score appears -- see <a href=\"/why-trust-us/\">Why trust us</a> for more on that, our current advertiser relationships (currently none), and what to double-check yourself before acting on anything here.</p>\n<p>This site is a work in progress. Brokers get added, existing entries get corrected when we find something wrong, and the methodology itself may be refined over time -- when it changes, we'll update this page and the methodology page together.</p>",
        ),
        'how-we-test' => array(
            'title'   => 'How We Test',
            'content' => "<p>Every score on GlobalFXHub comes from one disclosed methodology, applied identically to every broker in our researched set -- whether it's licensed by CySEC, the FCA, the Seychelles FSA, or another regulator. This is desk research compiled from public regulatory registers and broker disclosures -- not hands-on account testing. We say that plainly because plenty of broker-review sites imply otherwise.</p>\n<h2>The nine categories, and their weight</h2>\n<ul>\n<li><strong>Regulation & client protection -- 30%.</strong> A fixed point value for each independently-verifiable regulator held (CySEC, the FCA, ASIC, FSCA, and similar), checked against each regulator's own public register where possible. Offshore-only regulators, such as the Seychelles FSA, add nothing on their own -- a broker whose sole licence is offshore scores at the bottom of this factor, reflecting materially lighter oversight and no statutory investor compensation scheme, though we still disclose that licence and its number on the broker's review page. More independent oversight scores higher.</li>\n<li><strong>Trading costs -- 20%.</strong> The broker's EUR/USD spread, scored against fixed pip thresholds (tighter spreads score higher). We deliberately exclude minimum deposit from this factor: a low minimum makes a broker more accessible, but it isn't a measure of what it actually costs you to trade.</li>\n<li><strong>Non-trading fees -- 10%.</strong> Confirmed inactivity fees and currency-conversion charges. A broker confirmed not to charge either scores well; a confirmed fee scores worse; where we couldn't independently confirm either way, this sits at a neutral midpoint rather than being guessed.</li>\n<li><strong>Platforms & tools -- 10%.</strong> Number of distinct trading platforms supported (MT4, MT5, cTrader, TradingView, a broker's own proprietary platform, and so on). This is a breadth count, not a quality judgment of any one platform -- a broker with one excellent proprietary platform isn't automatically worse than one offering four mediocre ones, which is also why this factor now carries less weight than it used to.</li>\n<li><strong>Execution / trading conditions -- 10%.</strong> Whether the broker discloses a no-dealing-desk execution model (ECN/STP/DMA), discounted for any independently-reported pattern of withdrawal refusals, fund manipulation, or similar conduct red flags surfaced in our research.</li>\n<li><strong>Product range -- 5%.</strong> How many distinct asset classes (forex, indices, shares, commodities, crypto, bonds, ETFs, and more) we could independently confirm the broker offers, against a fixed scale.</li>\n<li><strong>Deposits & withdrawals -- 5%.</strong> Confirmed deposit/withdrawal fees and processing speed, discounted for any independently-reported pattern of delayed, blocked, or disputed withdrawals.</li>\n<li><strong>Transparency -- 5%.</strong> Starts clean and is discounted only for specific, named findings from our research: a regulator warning or blacklisting, a confirmed clone/impersonation risk, confirmed fake-review manipulation, or a defunct/wound-down status.</li>\n<li><strong>Track record -- 5%.</strong> Years in operation, against a fixed scale from founding year. Longer-operating brokers score higher.</li>\n</ul>\n<h2>How the score is actually calculated</h2>\n<p>Each factor is scored against a fixed, disclosed threshold -- an absolute rubric, not a ranking relative to other brokers in our set. A broker is scored on what it actually is, not on how it stacks up against whoever else happens to be in our dataset that week. That means adding, removing, or re-researching a broker never changes anyone else's score, and a broker can only improve its own score by actually changing something about itself (a new licence, a lower spread, a resolved complaint pattern) -- never just because we added a worse broker to the list.</p>\n<p>Where we couldn't independently confirm a specific figure -- a spread or a founding year -- we don't guess or estimate. That factor is left out of that broker's average and the remaining factors are reweighted to fill the gap, rather than showing a number we can't stand behind. For the newer categories above (non-trading fees, execution, product range, deposits/withdrawals, transparency), an unconfirmed fact instead scores a neutral midpoint, since these are derived from our own research notes and \"we don't know\" shouldn't silently swing a broker's score up or down.</p>\n<h2>What we don't do</h2>\n<p>We don't open live accounts and trade through them. We don't award scores based on affiliate revenue, and we don't have any current advertiser relationships to affect our scoring in the first place -- see <a href=\"/why-trust-us/\">Why trust us</a>. And we don't treat a \"What other reviewers say\" figure, shown on individual review pages, as our own assessment -- those are compiled from what other publicly indexed sites report, clearly labeled as such, and kept separate from our own score.</p>\n<p>Regulatory status, licence numbers, and trading conditions change. Always verify current terms directly with the broker and against the relevant regulator's own public register -- for example <a href=\"https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/\" target=\"_blank\" rel=\"noopener\">CySEC's</a> or the <a href=\"https://fsaseychelles.sc/\" target=\"_blank\" rel=\"noopener\">Seychelles FSA's</a> -- before making a decision. Nothing on this site is personalized financial advice.</p>",
        ),
        'why-trust-us' => array(
            'title'   => 'Why Trust Us',
            'content' => "<p>A site that scores financial brokers only matters if you can see how those scores were built. Here's exactly where we stand.</p>\n<h2>The methodology is public</h2>\n<p>Every score is built from one disclosed formula, applied the same way to every broker -- see <a href=\"/how-we-test/\">How we test</a> for the full breakdown. We're not asking you to take a black-box \"expert rating\" on faith.</p>\n<h2>We don't claim hands-on testing</h2>\n<p>We don't open live accounts with every broker we cover, and we never imply otherwise. Our scores come from public regulatory data and broker disclosures, cross-referenced against each broker's regulator of record -- CySEC, the FCA, the Seychelles FSA, or another -- not from trading through each platform ourselves.</p>\n<h2>Advertiser relationships</h2>\n<p>GlobalFXHub does not currently have referral or advertising relationships with any broker listed on this site. If that changes, we'll disclose it clearly on this page and in the site footer, and it won't change how a score is calculated.</p>\n<h2>We correct mistakes</h2>\n<p>Broker details change, and we make mistakes. When we find an error -- a wrong licence number, an outdated regulatory status, a stale figure -- we correct it and keep researching, rather than leaving a page wrong because it's already published.</p>\n<h2>What we'd ask you to do anyway</h2>\n<p>Regardless of how much you trust this site, verify anything that matters -- current licence status, trading conditions, fees -- directly with the broker and on its regulator's own public register (for example, <a href=\"https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/\" target=\"_blank\" rel=\"noopener\">CySEC's</a> or the <a href=\"https://fsaseychelles.sc/\" target=\"_blank\" rel=\"noopener\">Seychelles FSA's</a>) before depositing money. Nothing here is personalized financial advice, and CFDs carry a high risk of losing money rapidly due to leverage.</p>",
        ),
        'advertiser-disclosure' => array(
            'title'   => 'Advertiser Disclosure',
            'content' => "<p><strong>Broker information.</strong> Broker names, regulatory licence numbers (CySEC, the FCA, Seychelles FSA, and others), and trading conditions referenced on this site describe real, independently operating companies, compiled from public regulatory registers and broker disclosures as of March 2026. These figures change -- verify them directly with the broker and on the relevant regulator's own register, such as <a href=\"https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/\" target=\"_blank\" rel=\"noopener\">CySEC's</a> or the <a href=\"https://fsaseychelles.sc/\" target=\"_blank\" rel=\"noopener\">Seychelles FSA's</a>, before relying on them.</p>\n<p><strong>How scores are calculated.</strong> Scores are calculated using a disclosed methodology (see <a href=\"/how-we-test/\">How we test</a>), not hands-on account testing.</p>\n<p><strong>Advertiser relationships.</strong> GlobalFXHub does not currently have referral or advertising relationships with any broker listed. If that changes, this page will disclose it.</p>\n<p><strong>Risk warning.</strong> CFDs are complex instruments carrying a high risk of losing money rapidly due to leverage -- most retail investor accounts lose money trading CFDs. Nothing on this site is personalized financial advice.</p>",
        ),
        'terms' => array(
            'title'   => 'Terms of Use',
            'content' => "<p>These terms govern your use of GlobalFXHub (the \"site\"). By using the site, you agree to them. If you don't agree, please don't use the site.</p>\n<h2>Informational purposes only</h2>\n<p>Everything on this site -- broker reviews, scores, comparisons, guides, and market data -- is provided for general informational and educational purposes only. It is not personalized financial, investment, legal, or tax advice, and shouldn't be treated as a recommendation to use any specific broker or trade any specific instrument.</p>\n<h2>No guarantee of accuracy</h2>\n<p>We research broker information carefully, but regulatory status, fees, spreads, and trading conditions change, and we can make mistakes. We make no warranty, express or implied, that any information on this site is complete, current, or error-free. Verify anything that matters directly with the broker and the relevant regulator before acting on it.</p>\n<h2>Market data</h2>\n<p>Live market figures shown on this site (prices, spreads, percentage changes) are supplied by a third-party data provider and may be delayed. They're shown for general market-overview purposes and shouldn't be used as the basis for a trading decision.</p>\n<h2>Third-party links</h2>\n<p>This site links to broker websites and other third-party resources. We don't control, and aren't responsible for, the content, accuracy, or practices of any external site.</p>\n<h2>Intellectual property</h2>\n<p>The text, design, and original graphics on this site belong to GlobalFXHub unless otherwise noted. You're welcome to link to our pages; please don't republish our content wholesale without asking first.</p>\n<h2>Limitation of liability</h2>\n<p>To the fullest extent permitted by law, GlobalFXHub is not liable for any loss or damage arising from your use of this site or reliance on its content, including trading losses.</p>\n<h2>Changes to these terms</h2>\n<p>We may update these terms from time to time. Continued use of the site after a change means you accept the updated terms.</p>\n<p><em>This page is a general informational framework and has not been reviewed by a lawyer for any specific jurisdiction -- get it properly reviewed before relying on it for real legal protection, especially if the site starts handling user accounts, payments, or user-submitted content.</em></p>",
        ),
        'privacy' => array(
            'title'   => 'Privacy Policy',
            'content' => "<p>This page explains what happens with your data when you visit GlobalFXHub.</p>\n<h2>What we collect</h2>\n<p>We don't currently run advertising trackers or analytics software on this site. The information we do have access to is limited to:</p>\n<ul>\n<li>Standard server logs kept by our hosting provider (e.g. IP address, browser type, pages requested) for security and technical operation -- this is normal for any website, and we don't separately analyze it.</li>\n<li>Anything you send us directly, such as by emailing us via the <a href=\"/contact/\">contact page</a>.</li>\n</ul>\n<h2>Fonts</h2>\n<p>This site loads typefaces from Google Fonts' servers. Loading a font means your browser makes a request to Google, which can see that the request came from your IP address, the same as for any externally hosted resource. We don't use this for tracking, but we can't fully control what Google itself does with that request.</p>\n<h2>Cookies</h2>\n<p>We don't set marketing or tracking cookies. WordPress, the software this site runs on, may set basic functional cookies for features like commenting, if enabled.</p>\n<h2>Third-party links</h2>\n<p>Pages on this site link to broker websites and other external resources with their own, separate privacy practices. We're not responsible for how those sites handle your data.</p>\n<h2>Your rights</h2>\n<p>If you're in the EU, UK, or another jurisdiction with similar data protection law, you have rights over any personal data we hold about you -- primarily, for this site, whatever you've sent us directly via email. These include the right to:</p>\n<ul>\n<li><strong>Access</strong> a copy of the personal data we hold about you.</li>\n<li><strong>Correct</strong> inaccurate or incomplete data.</li>\n<li><strong>Erase</strong> your data (the \"right to be forgotten\"), where it no longer needs to be kept.</li>\n<li><strong>Object to or restrict</strong> how we process your data.</li>\n<li><strong>Lodge a complaint</strong> with your local data protection supervisory authority if you believe we've mishandled your data.</li>\n</ul>\n<p>To exercise any of these rights, contact us via the <a href=\"/contact/\">contact page</a>. Since we don't maintain user accounts or a database of visitor profiles, most requests will simply mean deleting an email thread -- we'll confirm once that's done.</p>\n<h2>Changes</h2>\n<p>If we add analytics, advertising, or other data collection in the future, we'll update this page to reflect it before turning it on.</p>\n<h2>Questions</h2>\n<p>Contact us via the <a href=\"/contact/\">contact page</a> with any privacy questions.</p>",
        ),
        'contact' => array(
            'title'   => 'Contact',
            'content' => "<p>The best way to reach us is by email: <a href=\"mailto:hello@globalfxhub.net\">hello@globalfxhub.net</a>.</p>\n<h2>What to contact us about</h2>\n<ul>\n<li>Something on a broker's review page looks outdated or wrong (a licence number, a fee, a regulatory status).</li>\n<li>A broker or press contact wanting to get in touch.</li>\n<li>General questions about our methodology or this site.</li>\n</ul>\n<p>We read everything that comes in, though as an independent research site we can't guarantee a reply to every message.</p>",
        ),
    );
}

/**
 * Creates each page if its slug doesn't exist, fills in content if an
 * existing page's content is empty, and -- the one exception to "never
 * touch existing content" -- resyncs a page whose content still matches
 * exactly what this function itself last wrote (tracked via a stored
 * hash per slug), so a wording correction to the text above (like
 * broadening "CySEC-regulated" to cover other regulators) actually
 * reaches an already-published page instead of leaving it stale forever.
 * The moment a human edits a page by hand, its content no longer matches
 * the last-written hash, and this function permanently leaves it alone
 * from then on -- exactly like the rest of the theme's self-healing
 * provisioning treats manual changes.
 *
 * $globalfxhub_service_pages_legacy_hashes seeds the very first run after
 * this resync mechanism was introduced, so the four pages that already
 * shipped with CySEC-only wording (about, how-we-test, why-trust-us,
 * advertiser-disclosure) are recognized as "still exactly what we wrote"
 * and get corrected once, rather than requiring a manual wp-admin edit.
 */
function globalfxhub_ensure_service_pages() {
    // Legacy seed hashes, keyed by slug: the content hash of each page
    // as it shipped BEFORE this resync mechanism (or before that specific
    // page was added to it) ever ran live. Seeded in via array_merge()
    // below rather than as get_option()'s $default -- the default only
    // ever applies the very first time an option is read before it's
    // been saved, and this option has already been saved live since the
    // mechanism's first deploy. Without this merge, adding a NEW slug
    // here later would silently do nothing: get_option() would return
    // the already-stored array, which has no entry for that slug, and
    // the resync condition below would never fire for it.
    $globalfxhub_service_pages_legacy_hashes = array(
        'about'                 => '433f10356390f475170faa77479ac628',
        'how-we-test'           => '493a63801eb2c79910d00e108c0863e8',
        'why-trust-us'          => '0f0c0447585309594fd1cfcbdcf084c5',
        'advertiser-disclosure' => '32c6a0799f8c7a37168457fbf87aad47',
        'privacy'               => '9b7e88793a30c6db624c582d3c9dd5a7',
    );

    $known_hashes = get_option( 'globalfxhub_service_pages_hashes', array() );
    if ( ! is_array( $known_hashes ) ) {
        $known_hashes = array();
    }
    foreach ( $globalfxhub_service_pages_legacy_hashes as $slug => $legacy_hash ) {
        if ( ! isset( $known_hashes[ $slug ] ) ) {
            $known_hashes[ $slug ] = $legacy_hash;
        }
    }
    $updated_hashes = $known_hashes;

    foreach ( globalfxhub_get_service_pages() as $slug => $data ) {
        $page = get_page_by_path( $slug );
        $new_hash = md5( $data['content'] );

        if ( ! $page ) {
            $new_id = wp_insert_post( array(
                'post_title'   => $data['title'],
                'post_name'    => $slug,
                'post_content' => $data['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ) );
            if ( $new_id && ! is_wp_error( $new_id ) ) {
                $updated_hashes[ $slug ] = $new_hash;
            }
            continue;
        }

        if ( '' === trim( wp_strip_all_tags( $page->post_content ) ) ) {
            wp_update_post( array( 'ID' => $page->ID, 'post_content' => $data['content'] ) );
            $updated_hashes[ $slug ] = $new_hash;
            continue;
        }

        $current_hash = md5( $page->post_content );
        if ( $current_hash === $new_hash ) {
            $updated_hashes[ $slug ] = $new_hash; // already correct
            continue;
        }
        if ( isset( $known_hashes[ $slug ] ) && $known_hashes[ $slug ] === $current_hash ) {
            wp_update_post( array( 'ID' => $page->ID, 'post_content' => $data['content'] ) );
            $updated_hashes[ $slug ] = $new_hash;
        }
    }

    update_option( 'globalfxhub_service_pages_hashes', $updated_hashes, false );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_service_pages' );
