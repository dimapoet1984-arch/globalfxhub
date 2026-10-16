<?php
/**
 * Shared horizontal "banner space available" placement, linking to the
 * Contact page -- one function so all three placements (homepage
 * below the hero, homepage above "How we score brokers", and below
 * the breadcrumb on every News/Broker-News/article page) render
 * identical markup instead of drifting out of sync.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_promo_banner() {
    ?>
<div class="wrap promo-banner-wrap">
  <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="promo-banner">
    <span class="promo-banner__text">
      <span class="promo-banner__eyebrow">AD SPACE AVAILABLE</span>
      <span class="promo-banner__headline">Your banner can be here.</span>
      <span class="promo-banner__body">Contact us for placement terms and conditions.</span>
    </span>
    <span class="promo-banner__cta">Contact us &rarr;</span>
  </a>
</div>
    <?php
}
