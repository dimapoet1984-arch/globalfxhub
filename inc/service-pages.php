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
            'content' => "<p>GlobalFXHub is an independent research site covering CySEC-regulated forex and CFD brokers. We built it because comparing brokers is harder than it should be: marketing pages rarely state regulatory status clearly, \"top 10\" lists are often paid placements dressed up as rankings, and the details that actually matter -- licence status, cost, platform choice, how long a broker has actually been operating -- are scattered across dozens of sources.</p>\n<p>What we do is straightforward: we cross-reference brokers against the official CySEC investment-firms register, compile their regulatory footprint, cost structure, platform offering, and operating history from public disclosures, and score them using one disclosed, consistent methodology across the whole set. See <a href=\"/how-we-test/\">How we test</a> for exactly how that works.</p>\n<p>We do not test broker accounts hands-on, and we say so plainly wherever a score appears -- see <a href=\"/why-trust-us/\">Why trust us</a> for more on that, our current advertiser relationships (currently none), and what to double-check yourself before acting on anything here.</p>\n<p>This site is a work in progress. Brokers get added, existing entries get corrected when we find something wrong, and the methodology itself may be refined over time -- when it changes, we'll update this page and the methodology page together.</p>",
        ),
        'how-we-test' => array(
            'title'   => 'How We Test',
            'content' => "<p>Every score on GlobalFXHub comes from one disclosed methodology, applied identically to every broker in our researched set. This is desk research compiled from public regulatory registers and broker disclosures -- not hands-on account testing. We say that plainly because plenty of broker-review sites imply otherwise.</p>\n<h2>The four factors, and their weight</h2>\n<ul>\n<li><strong>Regulatory footprint -- 30%.</strong> How many Tier-1 regulators (CySEC plus others, such as the FCA, ASIC, or FSCA) a broker holds, checked against each regulator's own public register where possible. More independent oversight scores higher.</li>\n<li><strong>Cost -- 30%.</strong> Average EUR/USD spread and minimum deposit, each compared against every other broker in the set. Lower cost and lower barrier to entry score higher.</li>\n<li><strong>Platform breadth -- 20%.</strong> Number of distinct trading platforms supported (MT4, MT5, cTrader, TradingView, a broker's own proprietary platform, and so on). More choice scores higher.</li>\n<li><strong>Track record -- 20%.</strong> Years in operation, compared against the rest of the set. Longer-operating brokers score higher.</li>\n</ul>\n<h2>How the score is actually calculated</h2>\n<p>For each factor, every broker is ranked relative to every other broker we've researched -- not against a fixed, absolute scale. That means adding more brokers to the set, or an existing broker changing its spread or regulatory status, can shift everyone's relative score, not just the one that changed. We recompute the full set when that happens, rather than leaving old scores stale.</p>\n<p>Where we couldn't independently confirm a specific figure -- a spread, a minimum deposit, a founding year -- we don't guess or estimate. That factor is left out of that broker's score, and the page says so, rather than showing a number we can't stand behind.</p>\n<h2>What we don't do</h2>\n<p>We don't open live accounts and trade through them. We don't award scores based on affiliate revenue, and we don't have any current advertiser relationships to affect our scoring in the first place -- see <a href=\"/why-trust-us/\">Why trust us</a>. And we don't treat a \"What other reviewers say\" figure, shown on individual review pages, as our own assessment -- those are compiled from what other publicly indexed sites report, clearly labeled as such, and kept separate from our own score.</p>\n<p>Regulatory status, licence numbers, and trading conditions change. Always verify current terms directly with the broker and against the <a href=\"https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/\" target=\"_blank\" rel=\"noopener\">CySEC public register</a> before making a decision. Nothing on this site is personalized financial advice.</p>",
        ),
        'why-trust-us' => array(
            'title'   => 'Why Trust Us',
            'content' => "<p>A site that scores financial brokers only matters if you can see how those scores were built. Here's exactly where we stand.</p>\n<h2>The methodology is public</h2>\n<p>Every score is built from one disclosed formula, applied the same way to every broker -- see <a href=\"/how-we-test/\">How we test</a> for the full breakdown. We're not asking you to take a black-box \"expert rating\" on faith.</p>\n<h2>We don't claim hands-on testing</h2>\n<p>We don't open live accounts with every broker we cover, and we never imply otherwise. Our scores come from public regulatory data and broker disclosures, cross-referenced against the official CySEC register -- not from trading through each platform ourselves.</p>\n<h2>Advertiser relationships</h2>\n<p>GlobalFXHub does not currently have referral or advertising relationships with any broker listed on this site. If that changes, we'll disclose it clearly on this page and in the site footer, and it won't change how a score is calculated.</p>\n<h2>We correct mistakes</h2>\n<p>Broker details change, and we make mistakes. When we find an error -- a wrong licence number, an outdated regulatory status, a stale figure -- we correct it and keep researching, rather than leaving a page wrong because it's already published.</p>\n<h2>What we'd ask you to do anyway</h2>\n<p>Regardless of how much you trust this site, verify anything that matters -- current licence status, trading conditions, fees -- directly with the broker and on the <a href=\"https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/\" target=\"_blank\" rel=\"noopener\">CySEC public register</a> before depositing money. Nothing here is personalized financial advice, and CFDs carry a high risk of losing money rapidly due to leverage.</p>",
        ),
        'advertiser-disclosure' => array(
            'title'   => 'Advertiser Disclosure',
            'content' => "<p><strong>Broker information.</strong> Broker names, CySEC licence numbers, and trading conditions referenced on this site describe real, independently operating companies, compiled from public regulatory registers and broker disclosures as of March 2026. These figures change -- verify them directly with the broker and on the <a href=\"https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/\" target=\"_blank\" rel=\"noopener\">CySEC register</a> before relying on them.</p>\n<p><strong>How scores are calculated.</strong> Scores are calculated using a disclosed methodology (see <a href=\"/how-we-test/\">How we test</a>), not hands-on account testing.</p>\n<p><strong>Advertiser relationships.</strong> GlobalFXHub does not currently have referral or advertising relationships with any broker listed. If that changes, this page will disclose it.</p>\n<p><strong>Risk warning.</strong> CFDs are complex instruments carrying a high risk of losing money rapidly due to leverage -- most retail investor accounts lose money trading CFDs. Nothing on this site is personalized financial advice.</p>",
        ),
        'terms' => array(
            'title'   => 'Terms of Use',
            'content' => "<p>These terms govern your use of GlobalFXHub (the \"site\"). By using the site, you agree to them. If you don't agree, please don't use the site.</p>\n<h2>Informational purposes only</h2>\n<p>Everything on this site -- broker reviews, scores, comparisons, guides, and market data -- is provided for general informational and educational purposes only. It is not personalized financial, investment, legal, or tax advice, and shouldn't be treated as a recommendation to use any specific broker or trade any specific instrument.</p>\n<h2>No guarantee of accuracy</h2>\n<p>We research broker information carefully, but regulatory status, fees, spreads, and trading conditions change, and we can make mistakes. We make no warranty, express or implied, that any information on this site is complete, current, or error-free. Verify anything that matters directly with the broker and the relevant regulator before acting on it.</p>\n<h2>Market data</h2>\n<p>Live market figures shown on this site (prices, spreads, percentage changes) are supplied by a third-party data provider and may be delayed. They're shown for general market-overview purposes and shouldn't be used as the basis for a trading decision.</p>\n<h2>Third-party links</h2>\n<p>This site links to broker websites and other third-party resources. We don't control, and aren't responsible for, the content, accuracy, or practices of any external site.</p>\n<h2>Intellectual property</h2>\n<p>The text, design, and original graphics on this site belong to GlobalFXHub unless otherwise noted. You're welcome to link to our pages; please don't republish our content wholesale without asking first.</p>\n<h2>Limitation of liability</h2>\n<p>To the fullest extent permitted by law, GlobalFXHub is not liable for any loss or damage arising from your use of this site or reliance on its content, including trading losses.</p>\n<h2>Changes to these terms</h2>\n<p>We may update these terms from time to time. Continued use of the site after a change means you accept the updated terms.</p>\n<p><em>This page is a general informational framework and has not been reviewed by a lawyer for any specific jurisdiction -- get it properly reviewed before relying on it for real legal protection, especially if the site starts handling user accounts, payments, or user-submitted content.</em></p>",
        ),
        'privacy' => array(
            'title'   => 'Privacy Policy',
            'content' => "<p>This page explains what happens with your data when you visit GlobalFXHub.</p>\n<h2>What we collect</h2>\n<p>We don't currently run advertising trackers or analytics software on this site. The information we do have access to is limited to:</p>\n<ul>\n<li>Standard server logs kept by our hosting provider (e.g. IP address, browser type, pages requested) for security and technical operation -- this is normal for any website, and we don't separately analyze it.</li>\n<li>Anything you send us directly, such as by emailing us via the <a href=\"/contact/\">contact page</a>.</li>\n</ul>\n<h2>Fonts</h2>\n<p>This site loads typefaces from Google Fonts' servers. Loading a font means your browser makes a request to Google, which can see that the request came from your IP address, the same as for any externally hosted resource. We don't use this for tracking, but we can't fully control what Google itself does with that request.</p>\n<h2>Cookies</h2>\n<p>We don't set marketing or tracking cookies. WordPress, the software this site runs on, may set basic functional cookies for features like commenting, if enabled.</p>\n<h2>Third-party links</h2>\n<p>Pages on this site link to broker websites and other external resources with their own, separate privacy practices. We're not responsible for how those sites handle your data.</p>\n<h2>Changes</h2>\n<p>If we add analytics, advertising, or other data collection in the future, we'll update this page to reflect it before turning it on.</p>\n<h2>Questions</h2>\n<p>Contact us via the <a href=\"/contact/\">contact page</a> with any privacy questions.</p>",
        ),
        'contact' => array(
            'title'   => 'Contact',
            'content' => "<p>The best way to reach us is by email: <a href=\"mailto:hello@globalfxhub.net\">hello@globalfxhub.net</a>.</p>\n<h2>What to contact us about</h2>\n<ul>\n<li>Something on a broker's review page looks outdated or wrong (a licence number, a fee, a regulatory status).</li>\n<li>A broker or press contact wanting to get in touch.</li>\n<li>General questions about our methodology or this site.</li>\n</ul>\n<p>We read everything that comes in, though as an independent research site we can't guarantee a reply to every message.</p>",
        ),
    );
}

/**
 * Creates each page if its slug doesn't exist, and fills in content only
 * if an existing page's content is empty -- never overwrites a page
 * that's already been edited (matching how the rest of the theme's
 * self-healing provisioning treats manual changes).
 */
function globalfxhub_ensure_service_pages() {
    foreach ( globalfxhub_get_service_pages() as $slug => $data ) {
        $page = get_page_by_path( $slug );

        if ( ! $page ) {
            wp_insert_post( array(
                'post_title'   => $data['title'],
                'post_name'    => $slug,
                'post_content' => $data['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ) );
            continue;
        }

        if ( '' === trim( wp_strip_all_tags( $page->post_content ) ) ) {
            wp_update_post( array(
                'ID'           => $page->ID,
                'post_content' => $data['content'],
            ) );
        }
    }
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_service_pages' );
