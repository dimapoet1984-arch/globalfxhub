<?php
/**
 * Template Name: Countries Index
 * Description: "Best broker in [country]" pages at /countries/{slug}/
 * for all 27 EU member states plus four non-EU jurisdictions (Australia,
 * South Africa, the UAE, Singapore -- see inc/countries.php), and a
 * country index at /countries/. $is_eu_country below branches every
 * section that differs between the two: the EU pages infer availability
 * from CySEC passporting, while the non-EU pages require a confirmed
 * licence from that country's own regulator.
 */
get_header();

$country_slug = get_query_var( 'country' );
if ( ! $country_slug && isset( $_GET['country'] ) ) {
    $country_slug = sanitize_title( wp_unslash( $_GET['country'] ) );
}
$country = $country_slug ? globalfxhub_get_country_by_slug( $country_slug ) : null;
?>

<?php if ( $country ) :
    $is_eu_country = empty( $country['region_type'] ) || 'other' !== $country['region_type'];
    $ranked = $is_eu_country
        ? globalfxhub_country_broker_ranking( $country, 10 )
        : globalfxhub_country_broker_ranking_other_reg( $country, 10 );
    $has_local = ! empty( array_filter( $ranked, function( $b ) { return ! empty( $b['is_local_hq'] ); } ) );
?>

<?php $country_name = globalfxhub_tf( 'country', $country_slug, 'name', $country['name'] ); ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> /
  <a href="<?php echo esc_url( globalfxhub_templated_page_url( 'countries' ) ); ?>"><?php globalfxhub_te( 'nav_countries' ); ?></a> /
  <?php echo esc_html( $country_name ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php echo esc_html( sprintf( globalfxhub_t( 'country_eyebrow' ), mb_strtoupper( $country_name, 'UTF-8' ) ) ); ?></div>
  <h1><span class="country-flag" aria-hidden="true"><?php echo esc_html( globalfxhub_country_flag_emoji( $country['iso'] ) ); ?></span> <?php echo sprintf( esc_html( globalfxhub_t( 'country_h1' ) ), esc_html( $country_name ) ); ?></h1>
  <?php
  $country_full_reviews_link = '<a href="' . esc_url( globalfxhub_templated_page_url( 'reviews' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'country_link_text_full_reviews' ) ) . '</a>';
  $country_suffix = $has_local ? globalfxhub_t( 'country_has_local_suffix' ) : '';
  if ( $is_eu_country ) :
  ?>
  <p><?php echo sprintf( globalfxhub_t( 'country_intro_eu' ), $country_suffix, esc_html( $country_name ), $country_full_reviews_link ); ?></p>
  <?php else : ?>
  <p><?php echo sprintf( globalfxhub_t( 'country_intro_noneu' ), esc_html( $country['regulator'] ), $country_suffix, esc_html( $country['regulator_short'] ), esc_html( $country_name ), $country_full_reviews_link ); ?></p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:10px;">
  <?php foreach ( $country['paragraphs'] as $p_i => $p ) : ?>
  <p style="color:var(--ink-soft);line-height:1.7;margin:0 0 16px;"><?php echo esc_html( globalfxhub_tf( 'country', $country_slug, "paragraphs[$p_i]", $p ) ); ?></p>
  <?php endforeach; ?>
  <p style="color:var(--ink-soft);line-height:1.7;margin:0 0 16px;font-size:14px;">
    <strong><?php globalfxhub_te( 'country_label_currency' ); ?></strong> <?php echo esc_html( $country['currency'] ); ?><?php echo ! empty( $country['eurozone'] ) ? esc_html( globalfxhub_t( 'country_eurozone_suffix' ) ) : ''; ?> &middot;
    <strong><?php globalfxhub_te( 'country_label_regulator' ); ?></strong> <?php echo esc_html( $country['regulator'] ); ?>
    <?php if ( $is_eu_country ) : ?>
    &middot; <strong><?php globalfxhub_te( 'country_label_eu_since' ); ?></strong> <?php echo esc_html( $country['eu_since'] ); ?>
    <?php endif; ?>
  </p>
</div>

<?php
$country_coverage_link = '<a href="' . globalfxhub_templated_page_url( 'countries' ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'country_link_text_coverage' ) ) . '</a>';
$cc_not_researched = sprintf( globalfxhub_t( 'country_not_researched' ), $country_coverage_link );
?>

<div class="wrap" style="padding-bottom:10px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;"><?php globalfxhub_te( $is_eu_country ? 'country_h2_eu_framework' : 'country_h2_framework_other' ); ?></h2>
  <div style="color:var(--ink-soft);line-height:1.7;">
  <?php if ( $is_eu_country ) : ?>
    <?php echo wp_kses( globalfxhub_country_eu_framework_html( $country ), array( 'p' => array(), 'em' => array(), 'strong' => array() ) ); ?>
  <?php else : ?>
    <p><?php echo esc_html( globalfxhub_tf( 'country', $country_slug, 'framework_overview', $country['framework_overview'] ) ); ?></p>
  <?php endif; ?>
  </div>

  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;"><?php globalfxhub_te( 'country_h2_investor_compensation' ); ?></h2>
  <div style="color:var(--ink-soft);line-height:1.7;">
  <?php if ( $is_eu_country ) : ?>
    <?php echo wp_kses( globalfxhub_country_investor_compensation_html( $country ), array( 'p' => array(), 'em' => array(), 'strong' => array(), 'a' => array( 'href' => array() ) ) ); ?>
  <?php else : ?>
    <p><?php echo esc_html( globalfxhub_tf( 'country', $country_slug, 'compensation_overview', $country['compensation_overview'] ) ); ?></p>
  <?php endif; ?>
  </div>

  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;"><?php globalfxhub_te( 'country_h2_leverage_rules' ); ?></h2>
  <div style="color:var(--ink-soft);line-height:1.7;">
  <?php if ( $is_eu_country ) : ?>
    <?php echo wp_kses( globalfxhub_country_leverage_rules_html( $country ), array( 'p' => array(), 'em' => array(), 'strong' => array() ) ); ?>
  <?php else : ?>
    <p><?php echo esc_html( globalfxhub_tf( 'country', $country_slug, 'leverage_overview', $country['leverage_overview'] ) ); ?></p>
  <?php endif; ?>
  </div>

  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;"><?php globalfxhub_te( 'country_h2_taxation' ); ?></h2>
  <div style="color:var(--ink-soft);line-height:1.7;">
    <p><?php echo ! empty( $country['taxation_overview'] ) ? esc_html( globalfxhub_tf( 'country', $country_slug, 'taxation_overview', $country['taxation_overview'] ) ) : wp_kses( $cc_not_researched, array( 'a' => array( 'href' => array() ) ) ); ?></p>
    <p style="font-size:13px;"><em><?php globalfxhub_te( 'country_tax_disclaimer' ); ?></em></p>
  </div>

  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;"><?php globalfxhub_te( 'country_h2_deposit_methods' ); ?></h2>
  <div style="color:var(--ink-soft);line-height:1.7;">
    <p><?php echo ! empty( $country['local_payment_methods'] ) ? esc_html( globalfxhub_tf( 'country', $country_slug, 'local_payment_methods', $country['local_payment_methods'] ) ) : wp_kses( $cc_not_researched, array( 'a' => array( 'href' => array() ) ) ); ?></p>
  </div>

  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 8px;"><?php globalfxhub_te( $is_eu_country ? 'country_h2_restrictions_eu' : 'country_h2_restrictions_other' ); ?></h2>
  <div style="color:var(--ink-soft);line-height:1.7;">
    <p><?php
    if ( ! empty( $country['country_specific_restrictions'] ) ) {
        echo esc_html( globalfxhub_tf( 'country', $country_slug, 'country_specific_restrictions', $country['country_specific_restrictions'] ) );
    } elseif ( ! empty( $country['additional_eu_note'] ) ) {
        echo esc_html( globalfxhub_tf( 'country', $country_slug, 'additional_eu_note', $country['additional_eu_note'] ) );
    } else {
        echo wp_kses( $cc_not_researched, array( 'a' => array( 'href' => array() ) ) );
    }
    ?></p>
  </div>
</div>

<div class="wrap" style="padding-bottom:16px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:0 0 8px;"><?php globalfxhub_te( 'country_h2_local_entity' ); ?></h2>
  <p style="color:var(--ink-soft);line-height:1.7;">
  <?php
  if ( $has_local ) :
      $licence_clause = $is_eu_country
          ? globalfxhub_t( 'country_local_entity_eu_licence_clause' )
          : sprintf( globalfxhub_t( 'country_local_entity_other_licence_clause' ), esc_html( $country['regulator_short'] ) );
      echo sprintf( globalfxhub_t( 'country_local_entity_has_local' ), esc_html( $country_name ), $licence_clause );
  elseif ( $is_eu_country ) :
      echo sprintf( globalfxhub_t( 'country_local_entity_none_eu' ), esc_html( $country_name ) );
  else :
      echo sprintf( globalfxhub_t( 'country_local_entity_none_other' ), esc_html( $country_name ), esc_html( $country['regulator_short'] ) );
  endif;
  ?>
  </p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="rankings">
    <div class="rank-row head">
      <span></span><span><?php globalfxhub_te( 'table_broker' ); ?></span><span class="col-fees"><?php globalfxhub_te( 'table_spread' ); ?></span><span class="col-plat"><?php globalfxhub_te( 'table_platforms' ); ?></span><span><?php globalfxhub_te( 'table_score' ); ?></span><span></span>
    </div>
    <?php foreach ( $ranked as $i => $b ) :
        $tag = globalfxhub_broker_regulation_label( $b );
        if ( $b['founded'] ) {
            $tag .= ' &middot; ' . esc_html( sprintf( globalfxhub_t( 'rev_founded_inline' ), $b['founded'] ) );
        }
        if ( ! empty( $b['is_local_hq'] ) ) {
            $tag .= ' &middot; <strong style="color:var(--teal);">' . esc_html( sprintf( globalfxhub_t( 'cty_headquartered_in' ), $country_name ) ) . '</strong>';
        } elseif ( ! empty( $b['has_direct_national_licence'] ) ) {
            $tag .= ' &middot; <strong style="color:var(--teal);">' . esc_html( sprintf( globalfxhub_t( 'cty_direct_licence_tag' ), $country['regulator'] ?? $country_name ) ) . '</strong>';
        }
        $availability_badges = array(
            'confirmed' => array( 'label' => globalfxhub_t( 'cty_badge_confirmed_label' ), 'title' => globalfxhub_t( 'cty_badge_confirmed_title' ), 'color' => 'var(--teal)' ),
            'inferred'  => array( 'label' => globalfxhub_t( 'cty_badge_inferred_label' ), 'title' => globalfxhub_t( 'cty_badge_inferred_title' ), 'color' => '#b8860b' ),
            'unverified' => array( 'label' => globalfxhub_t( 'cty_badge_unverified_label' ), 'title' => globalfxhub_t( 'cty_badge_unverified_title' ), 'color' => 'var(--ink-soft)' ),
        );
        $availability_badge = $availability_badges[ $b['availability_status'] ?? 'unverified' ];
    ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker">
        <span class="rank-broker__name"><?php echo esc_html( $b['name'] ); ?> <span title="<?php echo esc_attr( $availability_badge['title'] ); ?>" style="font-size:10px;font-weight:700;letter-spacing:0.03em;color:<?php echo esc_attr( $availability_badge['color'] ); ?>;border:1px solid currentColor;border-radius:3px;padding:1px 5px;vertical-align:middle;cursor:help;"><?php echo esc_html( $availability_badge['label'] ); ?></span></span>
        <span class="rank-broker__tag"><?php echo wp_kses( $tag, array( 'strong' => array( 'style' => array() ) ) ); ?></span>
      </span>
      <span class="rank-detail col-fees"><?php echo null !== $b['spread_eurusd'] ? esc_html( $b['spread_eurusd'] ) . ' pips (EUR/USD)' : esc_html( globalfxhub_t( 'tb_not_confirmed' ) ); ?></span>
      <span class="rank-detail col-plat"><?php echo esc_html( $b['platforms'] ? globalfxhub_render_platforms_list( $b['platforms'] ) : globalfxhub_t( 'tb_not_confirmed' ) ); ?></span>
      <span class="rank-score"><?php echo esc_html( $b['scores']['overall'] ); ?> / 5</span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( globalfxhub_track_b_url( 'reviews/' . $b['slug'] . '/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
        <a href="#" class="rank-cta rank-cta--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;"><?php globalfxhub_te( 'tb_visit_broker' ); ?></a>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:18px;">
  <?php
  $country_score_link = '<a href="' . esc_url( home_url( '/#method' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'country_link_text_score' ) ) . '</a>';
  $country_compare_link = '<a href="' . esc_url( globalfxhub_templated_page_url( 'compare' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'country_link_text_comparison_tool' ) ) . '</a>';
  if ( $is_eu_country ) :
      $country_leverage_guide_link = '<a href="' . esc_url( home_url( '/learn/understanding-leverage-in-forex-trading/' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'country_link_text_leverage_guide' ) ) . '</a>';
      echo sprintf( globalfxhub_t( 'country_methodology_eu' ), $country_score_link, esc_html( $country_name ), $country_compare_link, $country_leverage_guide_link );
  else :
      $shortlist_note = ( count( $ranked ) < 10 )
          ? sprintf( globalfxhub_t( 'country_methodology_shortlist_note' ), count( $ranked ), esc_html( $country['regulator_short'] ) ) . ' '
          : '';
      echo sprintf( globalfxhub_t( 'country_methodology_other' ), $country_score_link, esc_html( $country['regulator_short'] ), $shortlist_note, $country_compare_link );
  endif;
  ?>
  </p>
</div>

<div class="wrap" style="padding-bottom:40px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:0 0 12px;"><?php echo sprintf( esc_html( globalfxhub_t( 'country_h2_faqs' ) ), esc_html( $country_name ) ); ?></h2>
  <?php if ( ! empty( $country['faqs'] ) ) : ?>
  <div>
    <?php foreach ( $country['faqs'] as $faq_i => $faq ) : ?>
    <div style="margin-bottom:18px;">
      <p style="font-weight:600;margin:0 0 4px;color:var(--navy);"><?php echo esc_html( globalfxhub_tf( 'country', $country_slug, "faqs[$faq_i].q", $faq['q'] ) ); ?></p>
      <p style="margin:0;color:var(--ink-soft);line-height:1.6;"><?php echo esc_html( globalfxhub_tf( 'country', $country_slug, "faqs[$faq_i].a", $faq['a'] ) ); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else : ?>
  <p style="color:var(--ink-soft);"><?php echo wp_kses( $cc_not_researched, array( 'a' => array( 'href' => array() ) ) ); ?></p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:0 0 8px;"><?php globalfxhub_te( 'country_h2_regulatory_sources' ); ?></h2>
  <p style="color:var(--ink-soft);line-height:1.7;font-size:14px;">
    <?php echo esc_html( $country_name ); ?>: <a href="<?php echo esc_url( ! empty( $country['regulatory_source_url'] ) ? $country['regulatory_source_url'] : 'https://ec.europa.eu/' ); ?>" target="_blank" rel="noopener" style="color:var(--teal);"><?php echo esc_html( sprintf( globalfxhub_t( 'country_regsrc_official_site_suffix' ), $country['regulator'] ) ); ?></a>
    <?php if ( $is_eu_country ) : ?>
    &middot; <?php globalfxhub_te( 'country_regsrc_eu_wide_label' ); ?> <a href="https://www.esma.europa.eu/" target="_blank" rel="noopener" style="color:var(--teal);">ESMA</a>
    &middot; <?php globalfxhub_te( 'country_regsrc_broker_licensing_label' ); ?> <a href="<?php echo esc_url( home_url( '/regulation/cysec/' ) ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'country_link_text_cysec_page' ); ?></a>
    <?php endif; ?>
  </p>
</div>

<?php else :
    $countries = globalfxhub_get_all_countries();
    ksort( $countries );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> / <?php globalfxhub_te( 'nav_countries' ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'country_index_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'nav_countries' ); ?></h1>
  <p><?php echo wp_kses( globalfxhub_t( 'country_index_intro' ), array( 'em' => array() ) ); ?></p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="guides__grid">
    <?php foreach ( $countries as $slug => $c ) :
        $c_name = globalfxhub_tf( 'country', $slug, 'name', $c['name'] );
    ?>
    <a href="<?php echo esc_url( globalfxhub_track_b_url( 'countries/' . $slug . '/' ) ); ?>" class="guide country-card">
      <div class="guide__time"><?php echo esc_html( $c['currency'] ); ?> &middot; <?php echo esc_html( globalfxhub_country_index_badge( $c ) ); ?></div>
      <h3><span class="country-flag" aria-hidden="true"><?php echo esc_html( globalfxhub_country_flag_emoji( $c['iso'] ) ); ?></span> <?php echo sprintf( esc_html( globalfxhub_t( 'country_index_card_title' ) ), esc_html( $c_name ) ); ?></h3>
      <p><?php echo esc_html( $c['regulator'] ); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
