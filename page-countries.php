<?php
/**
 * Template Name: Countries Index
 * Description: "Best broker in [country]" pages for all 27 EU member
 * states at /countries/{slug}/, and a country index at /countries/.
 */
get_header();

$country_slug = get_query_var( 'country' );
if ( ! $country_slug && isset( $_GET['country'] ) ) {
    $country_slug = sanitize_title( wp_unslash( $_GET['country'] ) );
}
$country = $country_slug ? globalfxhub_get_country_by_slug( $country_slug ) : null;
?>

<?php if ( $country ) :
    $ranked = globalfxhub_country_broker_ranking( $country, 10 );
    $has_local = ! empty( array_filter( $ranked, function( $b ) { return ! empty( $b['is_local_hq'] ); } ) );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/countries/' ) ); ?>">Countries</a> /
  <?php echo esc_html( $country['name'] ); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BEST BROKER IN <?php echo esc_html( strtoupper( $country['name'] ) ); ?></div>
  <h1><span class="country-flag" aria-hidden="true"><?php echo esc_html( globalfxhub_country_flag_emoji( $country['iso'] ) ); ?></span> Best Forex &amp; CFD Brokers in <?php echo esc_html( $country['name'] ); ?></h1>
  <p>Top CySEC-regulated brokers, ranked by our disclosed methodology<?php echo $has_local ? ' -- with brokers actually headquartered here called out below' : ''; ?>. A CySEC licence carries the <em>right</em> to passport services into <?php echo esc_html( $country['name'] ); ?> under MiFID II, but passporting works via a per-country notification the firm's home regulator files on its behalf -- it isn't automatic, and which specific products or account types are actually offered can vary by entity and by country even where notification is in place. Treat "CySEC-regulated" as confirmation of the licence, not a guarantee that every broker below is actively onboarding <?php echo esc_html( $country['name'] ); ?> retail clients today -- confirm directly with the broker. Brokers we cover whose only licence is offshore (e.g. Seychelles FSA) aren't EU-passportable at all and are left out of this page for that reason -- see our <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" style="color:var(--teal);">full reviews</a> for those.</p>
</div>

<div class="wrap" style="padding-bottom:10px;">
  <?php foreach ( $country['paragraphs'] as $p ) : ?>
  <p style="max-width:74ch;color:var(--ink-soft);line-height:1.7;margin:0 0 16px;"><?php echo esc_html( $p ); ?></p>
  <?php endforeach; ?>
  <p style="max-width:74ch;color:var(--ink-soft);line-height:1.7;margin:0 0 16px;font-size:14px;">
    <strong>Base currency:</strong> <?php echo esc_html( $country['currency'] ); ?><?php echo $country['eurozone'] ? ' (eurozone)' : ''; ?> &middot;
    <strong>Local regulator:</strong> <?php echo esc_html( $country['regulator'] ); ?> &middot;
    <strong>EU member since:</strong> <?php echo esc_html( $country['eu_since'] ); ?>
  </p>
</div>

<?php
$cc_not_researched = 'Not yet independently researched for this country -- see our ongoing <a href="' . home_url( '/countries/' ) . '" style="color:var(--teal);">country coverage</a> for updates, and verify directly with a local advisor in the meantime.';
?>

<div class="wrap" style="padding-bottom:10px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;">Applicable EU regulatory framework</h2>
  <div style="max-width:74ch;color:var(--ink-soft);line-height:1.7;"><?php echo wp_kses( globalfxhub_country_eu_framework_html( $country ), array( 'p' => array(), 'em' => array(), 'strong' => array() ) ); ?></div>

  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;">Investor compensation arrangements</h2>
  <div style="max-width:74ch;color:var(--ink-soft);line-height:1.7;"><?php echo wp_kses( globalfxhub_country_investor_compensation_html( $country ), array( 'p' => array(), 'em' => array(), 'strong' => array(), 'a' => array( 'href' => array() ) ) ); ?></div>

  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;">Leverage rules</h2>
  <div style="max-width:74ch;color:var(--ink-soft);line-height:1.7;"><?php echo wp_kses( globalfxhub_country_leverage_rules_html( $country ), array( 'p' => array(), 'em' => array(), 'strong' => array() ) ); ?></div>

  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;">Taxation overview</h2>
  <div style="max-width:74ch;color:var(--ink-soft);line-height:1.7;">
    <p><?php echo ! empty( $country['taxation_overview'] ) ? esc_html( $country['taxation_overview'] ) : wp_kses( $cc_not_researched, array( 'a' => array( 'href' => array() ) ) ); ?></p>
    <p style="font-size:13px;"><em>General overview only, not tax advice -- rules change and depend on your personal circumstances (including whether you're classified as trading as a business). Confirm your specific position with a local tax advisor.</em></p>
  </div>

  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;">Local deposit methods</h2>
  <div style="max-width:74ch;color:var(--ink-soft);line-height:1.7;">
    <p><?php echo ! empty( $country['local_payment_methods'] ) ? esc_html( $country['local_payment_methods'] ) : wp_kses( $cc_not_researched, array( 'a' => array( 'href' => array() ) ) ); ?></p>
  </div>

  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;">Local restrictions beyond the EU baseline</h2>
  <div style="max-width:74ch;color:var(--ink-soft);line-height:1.7;">
    <p><?php
    if ( ! empty( $country['country_specific_restrictions'] ) ) {
        echo esc_html( $country['country_specific_restrictions'] );
    } elseif ( ! empty( $country['additional_eu_note'] ) ) {
        echo esc_html( $country['additional_eu_note'] );
    } else {
        echo wp_kses( $cc_not_researched, array( 'a' => array( 'href' => array() ) ) );
    }
    ?></p>
  </div>
</div>

<div class="wrap" style="padding-bottom:16px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:0 0 8px;">Brokers with an actual local entity or office</h2>
  <p style="max-width:74ch;color:var(--ink-soft);line-height:1.7;">
  <?php if ( $has_local ) : ?>
    Confirmed below (marked "Headquartered in <?php echo esc_html( $country['name'] ); ?>") based on each broker's own disclosed headquarters -- not a branch, not a marketing claim, and not the same as where its EU-regulated entity happens to be licensed. Every other broker in this ranking passports in from elsewhere with no confirmed local office.
  <?php else : ?>
    None of the brokers in our researched set are confirmed to be headquartered in <?php echo esc_html( $country['name'] ); ?>, or to hold a <?php echo esc_html( $country['name'] ); ?>-licensed entity specifically -- every broker ranked below passports in under its EU licence from elsewhere (typically Cyprus). We don't have comprehensive data on which brokers have filed a branch-level or local-office notification short of full headquarters, so we don't claim one here rather than guess.
  <?php endif; ?>
  </p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="rankings">
    <div class="rank-row head">
      <span></span><span>Broker</span><span class="col-fees">Avg. spread</span><span class="col-plat">Platforms</span><span>Score</span><span></span>
    </div>
    <?php foreach ( $ranked as $i => $b ) :
        $tag = globalfxhub_broker_regulation_label( $b );
        if ( $b['founded'] ) {
            $tag .= ' &middot; est. ' . $b['founded'];
        }
        if ( ! empty( $b['is_local_hq'] ) ) {
            $tag .= ' &middot; <strong style="color:var(--teal);">Headquartered in ' . esc_html( $country['name'] ) . '</strong>';
        }
    ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker">
        <span class="rank-broker__name"><?php echo esc_html( $b['name'] ); ?></span>
        <span class="rank-broker__tag"><?php echo wp_kses( $tag, array( 'strong' => array( 'style' => array() ) ) ); ?></span>
      </span>
      <span class="rank-detail col-fees"><?php echo null !== $b['spread_eurusd'] ? esc_html( $b['spread_eurusd'] ) . ' pips (EUR/USD)' : 'Not confirmed'; ?></span>
      <span class="rank-detail col-plat"><?php echo esc_html( $b['platforms'] ? implode( ', ', $b['platforms'] ) : 'Not confirmed' ); ?></span>
      <span class="rank-score"><?php echo esc_html( $b['scores']['overall'] ); ?> / 5</span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( home_url( '/reviews/' . $b['slug'] . '/' ) ); ?>" class="rank-cta">Read review</a>
        <a href="#" class="rank-cta rank-cta--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">Visit Broker</a>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:18px;max-width:74ch;">
    Ranked using our <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);">disclosed overall broker score</a> (nine weighted categories, led by regulation & client protection and trading costs) -- every broker here holds a CySEC licence capable of being passported into <?php echo esc_html( $country['name'] ); ?>, not a confirmed, currently-active local notification for each one (see the nuance above). We don't have independent country-level trading-volume or market-share data, so this reflects broker quality and, where applicable, a confirmed local headquarters -- not measured local popularity. See our <a href="<?php echo esc_url( home_url( '/compare/' ) ); ?>" style="color:var(--teal);">comparison tool</a> to compare any two directly, or the <a href="<?php echo esc_url( home_url( '/learn/understanding-leverage-in-forex-trading/' ) ); ?>" style="color:var(--teal);">leverage guide</a> for what the EU-wide ESMA rules mean for your account.
  </p>
</div>

<div class="wrap" style="padding-bottom:40px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:0 0 12px;"><?php echo esc_html( $country['name'] ); ?>-specific FAQs</h2>
  <?php if ( ! empty( $country['faqs'] ) ) : ?>
  <div style="max-width:74ch;">
    <?php foreach ( $country['faqs'] as $faq ) : ?>
    <div style="margin-bottom:18px;">
      <p style="font-weight:600;margin:0 0 4px;color:var(--navy);"><?php echo esc_html( $faq['q'] ); ?></p>
      <p style="margin:0;color:var(--ink-soft);line-height:1.6;"><?php echo esc_html( $faq['a'] ); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else : ?>
  <p style="max-width:74ch;color:var(--ink-soft);"><?php echo wp_kses( $cc_not_researched, array( 'a' => array( 'href' => array() ) ) ); ?></p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:0 0 8px;">Regulatory sources</h2>
  <p style="max-width:74ch;color:var(--ink-soft);line-height:1.7;font-size:14px;">
    <?php echo esc_html( $country['name'] ); ?>: <a href="<?php echo esc_url( ! empty( $country['regulatory_source_url'] ) ? $country['regulatory_source_url'] : 'https://ec.europa.eu/' ); ?>" target="_blank" rel="noopener" style="color:var(--teal);"><?php echo esc_html( $country['regulator'] ); ?> (official site)</a>
    &middot; EU-wide: <a href="https://www.esma.europa.eu/" target="_blank" rel="noopener" style="color:var(--teal);">ESMA</a>
    &middot; Broker licensing: <a href="<?php echo esc_url( home_url( '/regulation/cysec/' ) ); ?>" style="color:var(--teal);">our CySEC regulation page</a>
  </p>
</div>

<?php else :
    $countries = globalfxhub_get_countries();
    ksort( $countries );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BY COUNTRY</div>
  <h1><?php the_title(); ?></h1>
  <p>Every broker shown on these country pages holds a CySEC licence, which carries the <em>right</em> to passport into any EU/EEA member state under MiFID II -- but passporting is a per-country notification process, not automatic blanket access, and which products or account types are actually offered can vary by entity and by country. We also review brokers licensed elsewhere, such as the Seychelles FSA, but those aren't EU-passportable at all so they're not ranked here. Pick a country for its top-ranked CySEC brokers plus what's actually different there -- regulator, investor compensation, leverage rules, tax treatment, local payment methods, and any broker genuinely headquartered locally.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="guides__grid">
    <?php foreach ( $countries as $slug => $c ) : ?>
    <a href="<?php echo esc_url( home_url( '/countries/' . $slug . '/' ) ); ?>" class="guide country-card">
      <div class="guide__time"><?php echo esc_html( $c['currency'] ); ?> &middot; EU since <?php echo esc_html( $c['eu_since'] ); ?></div>
      <h3><span class="country-flag" aria-hidden="true"><?php echo esc_html( globalfxhub_country_flag_emoji( $c['iso'] ) ); ?></span> Best Broker in <?php echo esc_html( $c['name'] ); ?></h3>
      <p><?php echo esc_html( $c['regulator'] ); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
