<?php
/**
 * Shared horizontal promo banners -- internal cross-promotion (never
 * third-party ad code) for real, already-built site features: the
 * Broker Change Log, the Broker Finder tool, and the Best Brokers
 * rankings. One function + one markup block so the three placements
 * (homepage below the hero, homepage above "How we score brokers",
 * and below the breadcrumb on every News/Broker-News/article page)
 * can't drift out of sync with each other.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_promo_banners() {
    $broker_count = count( globalfxhub_get_brokers() );
    return array(
        'changelog'     => array(
            'eyebrow'   => 'FREE · UPDATED LIVE',
            'headline'  => 'We track every broker fee & policy change -- free',
            'body'      => 'Get notified the moment a broker on our list changes its fees, leverage, or regulatory status. No noise, no sales pitch -- just the disclosed Broker Change Log.',
            'cta_label' => 'See the Change Log',
            'cta_url'   => home_url( '/broker-changelog/' ),
        ),
        'broker-finder' => array(
            'eyebrow'   => 'INTERACTIVE TOOL',
            'headline'  => 'Find your ideal broker in 60 seconds',
            'body'      => 'Filter by regulation, trading style, platform, and country -- scored with the same disclosed nine-category methodology as every review on this site.',
            'cta_label' => 'Try the Broker Finder',
            'cta_url'   => home_url( '/broker-finder/' ),
        ),
        'rankings'      => array(
            'eyebrow'   => 'RESEARCHED & RANKED',
            'headline'  => 'See every broker, ranked by our disclosed methodology',
            'body'      => $broker_count . ' brokers, scored on the same nine weighted categories -- regulation, costs, platforms, and more. No pay-to-rank placements.',
            'cta_label' => 'See the rankings',
            'cta_url'   => home_url( '/best/' ),
        ),
    );
}

/**
 * Echoes one of the banners above. $key is one of 'changelog',
 * 'broker-finder', 'rankings' -- an unknown key renders nothing rather
 * than a broken block.
 */
function globalfxhub_promo_banner( $key ) {
    $banners = globalfxhub_promo_banners();
    if ( ! isset( $banners[ $key ] ) ) {
        return;
    }
    $b = $banners[ $key ];
    ?>
<div class="wrap promo-banner-wrap">
  <a href="<?php echo esc_url( $b['cta_url'] ); ?>" class="promo-banner">
    <span class="promo-banner__text">
      <span class="promo-banner__eyebrow"><?php echo esc_html( $b['eyebrow'] ); ?></span>
      <span class="promo-banner__headline"><?php echo esc_html( $b['headline'] ); ?></span>
      <span class="promo-banner__body"><?php echo esc_html( $b['body'] ); ?></span>
    </span>
    <span class="promo-banner__cta"><?php echo esc_html( $b['cta_label'] ); ?> &rarr;</span>
  </a>
</div>
    <?php
}
