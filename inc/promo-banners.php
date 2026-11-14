<?php
/**
 * Shared "ad space available" placements, both linking to the Contact
 * page: the horizontal banner (homepage, "How we score brokers", and
 * below the breadcrumb on every content page) and the 300px-square
 * sidebar used only on News/Broker-News/article pages. One function
 * per shape so every placement of that shape renders identical markup
 * instead of drifting out of sync.
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

/**
 * Three stacked 300px-square ad placements, each linking to Contact.
 * Echoed inside a .content-with-sidebar grid's second column -- see
 * the template call sites for the wrapping markup.
 */
function globalfxhub_ad_sidebar() {
    $contact_url = home_url( '/contact/' );
    ?>
<aside class="ad-sidebar">
  <a href="<?php echo esc_url( $contact_url ); ?>" class="ad-sidebar__banner">
    <span class="ad-sidebar__eyebrow">AD SPACE AVAILABLE</span>
    <span class="ad-sidebar__text">Banner 1 ad placement text</span>
    <span class="ad-sidebar__cta">Contact us &rarr;</span>
  </a>
  <a href="<?php echo esc_url( $contact_url ); ?>" class="ad-sidebar__banner">
    <span class="ad-sidebar__eyebrow">AD SPACE AVAILABLE</span>
    <span class="ad-sidebar__text">Banner 2 ad placement text</span>
    <span class="ad-sidebar__cta">Contact us &rarr;</span>
  </a>
  <a href="<?php echo esc_url( $contact_url ); ?>" class="ad-sidebar__banner">
    <span class="ad-sidebar__eyebrow">AD SPACE AVAILABLE</span>
    <span class="ad-sidebar__text">Banner 3 ad placement text</span>
    <span class="ad-sidebar__cta">Contact us &rarr;</span>
  </a>
</aside>
    <?php
}
