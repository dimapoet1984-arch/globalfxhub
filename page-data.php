<?php
/**
 * Template Name: GlobalFXHub Data
 * Description: /data/ -- a landing page for the research behind this
 * site: the broker dataset, regulator rosters, original reports,
 * comparisons, and the tools built on top of them. Every number here
 * is computed live from the same functions every other page on this
 * site calls (globalfxhub_get_brokers(), globalfxhub_get_regulator_entities(),
 * etc.) -- never a count frozen at publish time, so this page can't
 * quietly drift out of date the way a hand-written stat could.
 */
get_header();

$data_brokers      = globalfxhub_get_brokers();
$data_broker_count = count( $data_brokers );
$data_regulators   = globalfxhub_get_regulator_entities();
$data_regulator_count = count( $data_regulators );
$data_licence_holdings = 0;
foreach ( array_keys( $data_regulators ) as $data_reg_slug ) {
    $data_licence_holdings += globalfxhub_regulator_entity_stats( $data_reg_slug )['held'];
}
$data_country_count     = count( globalfxhub_get_all_countries() );
$data_report_count      = count( globalfxhub_get_research_reports() );
$data_reg_comparison_count = count( globalfxhub_get_regulation_comparisons() );
$data_broker_comparison_count = count( globalfxhub_get_editorial_comparisons() );
$data_best_list_count   = count( globalfxhub_get_best_lists() );
$data_vs_bound          = count( globalfxhub_top_ranked_broker_slugs( 40 ) );
$data_vs_pair_count     = (int) ( $data_vs_bound * ( $data_vs_bound - 1 ) / 2 );

$data_plural_t = function( $count, $singular_key, $plural_key ) {
    return sprintf( globalfxhub_t( 1 === $count ? $singular_key : $plural_key ), $count );
};
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> / <?php globalfxhub_te( 'ptitle_data' ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'data_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'ptitle_data' ); ?></h1>
  <p><?php globalfxhub_te( 'data_intro' ); ?></p>
</div>

<div class="trustbar">
  <div class="wrap trustbar__grid">
    <div>
      <div class="trustbar__num"><?php echo esc_html( $data_broker_count ); ?></div>
      <div class="trustbar__label"><?php globalfxhub_te( 'data_stat_brokers' ); ?></div>
    </div>
    <div>
      <div class="trustbar__num"><?php echo esc_html( $data_regulator_count ); ?></div>
      <div class="trustbar__label"><?php globalfxhub_te( 'data_stat_regulators' ); ?></div>
    </div>
    <div>
      <div class="trustbar__num"><?php echo esc_html( $data_licence_holdings ); ?></div>
      <div class="trustbar__label"><?php globalfxhub_te( 'data_stat_licence_holdings' ); ?></div>
    </div>
    <div>
      <div class="trustbar__num"><?php echo esc_html( $data_country_count ); ?></div>
      <div class="trustbar__label"><?php globalfxhub_te( 'data_stat_countries' ); ?></div>
    </div>
  </div>
</div>

<?php
$data_link = function( $url, $text_key ) {
    return '<a href="' . esc_url( $url ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( $text_key ) ) . '</a>';
};
$data_link_methodology       = $data_link( home_url( '/#method' ), 'data_link_text_methodology' );
$data_link_review_pages      = $data_link( globalfxhub_templated_page_url( 'reviews' ), 'data_link_text_review_pages' );
$data_link_best_lists        = $data_link( globalfxhub_templated_page_url( 'best' ), 'data_link_text_best_lists' );
$data_link_comparisons       = $data_link( globalfxhub_templated_page_url( 'compare' ), 'data_link_text_comparisons' );
$data_link_entity_page       = $data_link( globalfxhub_templated_page_url( 'regulators' ), 'data_link_text_entity_page' );
$data_link_regulation_kb     = $data_link( globalfxhub_templated_page_url( 'regulation' ), 'data_link_text_regulation_kb' );
$data_link_research_index    = $data_link( globalfxhub_templated_page_url( 'research' ), 'data_link_text_research_index' );
$data_link_broker_review     = $data_link( globalfxhub_templated_page_url( 'reviews' ), 'data_link_text_broker_review' );
$data_link_broker_finder     = $data_link( globalfxhub_templated_page_url( 'broker-finder' ), 'ptitle_broker_finder' );
$data_link_cost_calculator   = $data_link( globalfxhub_templated_page_url( 'cost-calculator' ), 'data_link_text_cost_calculator' );
$data_link_regulation_checker = $data_link( globalfxhub_templated_page_url( 'regulation-checker' ), 'data_link_text_regulation_checker' );
$data_link_broker_changelog  = $data_link( globalfxhub_templated_page_url( 'broker-changelog' ), 'ptitle_broker_changelog' );
$data_link_corrections       = $data_link( home_url( '/corrections/' ), 'footer_corrections' );
$data_link_editorial_policy  = $data_link( home_url( '/editorial-policy/' ), 'data_link_text_editorial_policy' );

$data_reg_comparison_phrase = $data_plural_t( $data_reg_comparison_count, 'data_reg_comparison_singular', 'data_reg_comparison_plural' );
$data_report_phrase         = $data_plural_t( $data_report_count, 'data_report_singular', 'data_report_plural' );
?>
<div class="wrap art-body" style="padding-top:36px;padding-bottom:10px;">

  <h2><?php globalfxhub_te( 'data_h2_dataset' ); ?></h2>
  <p><?php echo sprintf( esc_html( globalfxhub_t( 'data_p_dataset_1' ) ), esc_html( $data_broker_count ), $data_link_methodology, $data_link_review_pages ); ?></p>
  <p><?php echo sprintf( esc_html( globalfxhub_t( 'data_p_dataset_2' ) ), esc_html( $data_best_list_count ), $data_link_best_lists, esc_html( $data_broker_comparison_count ), $data_link_comparisons, esc_html( $data_broker_count ), esc_html( $data_vs_pair_count ), esc_html( $data_vs_bound ) ); ?></p>

  <h2><?php globalfxhub_te( 'data_h2_regulator' ); ?></h2>
  <p><?php echo sprintf( esc_html( globalfxhub_t( 'data_p_regulator' ) ), esc_html( $data_regulator_count ), $data_link_entity_page, esc_html( $data_licence_holdings ), $data_link_regulation_kb, esc_html( $data_reg_comparison_phrase ) ); ?></p>

  <h2><?php globalfxhub_te( 'data_h2_research' ); ?></h2>
  <p><?php echo sprintf( esc_html( globalfxhub_t( 'data_p_research' ) ), esc_html( $data_report_phrase ), esc_html( $data_broker_count ), $data_link_research_index ); ?></p>

  <h2><?php globalfxhub_te( 'data_h2_confidence' ); ?></h2>
  <p><?php echo sprintf( esc_html( globalfxhub_t( 'data_p_confidence' ) ), $data_link_broker_review ); ?></p>

  <h2><?php globalfxhub_te( 'data_h2_tools' ); ?></h2>
  <p><?php echo sprintf( esc_html( globalfxhub_t( 'data_p_tools' ) ), $data_link_broker_finder, $data_link_cost_calculator, $data_link_regulation_checker ); ?></p>

  <h2><?php globalfxhub_te( 'data_h2_keeping_current' ); ?></h2>
  <p><?php echo sprintf( esc_html( globalfxhub_t( 'data_p_keeping_current' ) ), $data_link_broker_changelog, $data_link_corrections, $data_link_editorial_policy ); ?></p>

</div>

<?php get_footer(); ?>
