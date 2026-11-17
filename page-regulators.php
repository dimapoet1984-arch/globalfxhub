<?php
/**
 * Template Name: Regulators
 * Description: /regulators/ index, and /regulators/{slug}/ for each
 * individual regulator -- a live roster of which of our researched
 * brokers hold that licence, plus the regulator's own key facts. See
 * inc/regulator-entities.php for the data; this is the structured
 * data companion to the prose explainer at /regulation/{slug}/
 * (inc/regulation.php), cross-linked both ways rather than merged
 * into one page.
 */
get_header();

$regulator_slug = get_query_var( 'regulator' );
$entities = globalfxhub_get_regulator_entities();
$entity = ( $regulator_slug && isset( $entities[ $regulator_slug ] ) ) ? $entities[ $regulator_slug ] : null;
if ( $regulator_slug && ! $entity ) {
    status_header( 404 );
}
?>

<?php if ( $regulator_slug ) :
    if ( ! $entity ) :
?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'regulators_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'regulator_not_available_h1' ); ?></h1>
  <p><?php
  $regulator_see_all_link = '<a href="' . esc_url( globalfxhub_templated_page_url( 'regulators' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'regulator_see_all_link' ) ) . '</a>';
  echo sprintf( esc_html( globalfxhub_t( 'regulator_not_available_body' ) ), $regulator_see_all_link );
  ?></p>
</div>

    <?php else :
        $stats = globalfxhub_regulator_entity_stats( $regulator_slug );
        $brokers = globalfxhub_regulator_entity_brokers( $regulator_slug );
        $reg_pages = globalfxhub_get_regulation_pages();
    ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> /
  <a href="<?php echo esc_url( globalfxhub_templated_page_url( 'regulators' ) ); ?>"><?php globalfxhub_te( 'nav_regulators' ); ?></a> /
  <?php echo esc_html( $entity['name'] ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'regulator_entity_eyebrow' ); ?></div>
  <h1><?php echo sprintf( globalfxhub_t( 'regulator_entity_h1' ), esc_html( $entity['name'] ) ); ?></h1>
  <p><?php echo esc_html( sprintf( globalfxhub_t( 'regulator_entity_stats_para' ), $entity['full_name'], $stats['held'], $stats['total'], $stats['percent'] ) ); ?></p>
</div>

<div class="wrap art-body" style="padding-bottom:10px;">
  <table class="compare-table review-facts">
    <tbody>
      <tr><th><?php globalfxhub_te( 'regulator_th_jurisdiction' ); ?></th><td><?php echo esc_html( $entity['jurisdiction'] ); ?></td></tr>
      <tr><th><?php globalfxhub_te( 'regulator_th_leverage' ); ?></th><td><?php echo esc_html( $entity['leverage_note'] ); ?></td></tr>
      <tr><th><?php globalfxhub_te( 'regulator_th_compensation' ); ?></th><td><?php echo $entity['compensation_scheme'] ? esc_html( $entity['compensation_scheme'] ) . ': ' . wp_kses( $entity['compensation_cap'], array() ) : esc_html( globalfxhub_t( 'regulator_no_compensation_scheme' ) ); ?></td></tr>
      <tr><th><?php globalfxhub_te( 'regulator_th_complaint_process' ); ?></th><td><?php echo esc_html( $entity['complaint_process'] ); ?></td></tr>
      <tr><th><?php globalfxhub_te( 'regulator_th_official_register' ); ?></th><td><a href="<?php echo esc_url( $entity['register_url'] ); ?>" target="_blank" rel="noopener" style="color:var(--teal);"><?php echo esc_html( sprintf( globalfxhub_t( 'regulator_search_register_link' ), $entity['register_label'] ) ); ?> &rarr;</a></td></tr>
    </tbody>
  </table>
  <p style="color:var(--ink-soft);font-size:13.5px;margin:0 0 20px;">
    <?php if ( $entity['related_regulation_slug'] && isset( $reg_pages[ $entity['related_regulation_slug'] ] ) ) :
        $regulator_related_link = '<a href="' . esc_url( globalfxhub_track_b_url( 'regulation/' . $entity['related_regulation_slug'] . '/' ) ) . '" style="color:var(--teal);">' . esc_html( sprintf( globalfxhub_t( 'regulator_link_text_explainer' ), $entity['name'] ) ) . '</a>';
        echo sprintf( esc_html( globalfxhub_t( 'regulator_see_explainer' ) ), $regulator_related_link );
    elseif ( ! empty( $entity['related_learn_slug'] ) ) :
        $regulator_related_link = '<a href="' . esc_url( globalfxhub_learn_article_url( $entity['related_learn_slug'] ) ) . '" style="color:var(--teal);">' . esc_html( sprintf( globalfxhub_t( 'regulator_link_text_guide' ), $entity['name'] ) ) . '</a>';
        echo sprintf( esc_html( globalfxhub_t( 'regulator_see_explainer' ) ), $regulator_related_link );
    endif; ?>
    <?php if ( $entity['related_best_slug'] ) :
        $regulator_best_link = '<a href="' . esc_url( globalfxhub_track_b_url( 'best/' . $entity['related_best_slug'] . '/' ) ) . '" style="color:var(--teal);">' . esc_html( sprintf( globalfxhub_t( 'regulator_link_text_best' ), $entity['name'] ) ) . '</a>';
        echo sprintf( esc_html( globalfxhub_t( 'regulator_see_best_list' ) ), $regulator_best_link );
    endif; ?>
  </p>
</div>

<div class="wrap" style="padding-bottom:40px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 6px;"><?php echo esc_html( sprintf( globalfxhub_t( 'regulator_roster_h2' ), $entity['name'] ) ); ?></h2>
  <?php if ( $entity['tracks_licence_numbers'] ) : ?>
  <p style="color:var(--ink-soft);font-size:14.5px;margin:0 0 20px;"><?php globalfxhub_te( 'regulator_entity_disclosure_tracked' ); ?></p>
  <?php else : ?>
  <p style="color:var(--ink-soft);font-size:14.5px;margin:0 0 20px;"><?php echo esc_html( sprintf( globalfxhub_t( 'regulator_entity_disclosure_untracked' ), $entity['name'] ) ); ?></p>
  <?php endif; ?>
  <?php if ( empty( $brokers ) ) : ?>
  <p><?php globalfxhub_te( 'regulator_no_brokers_hold_licence' ); ?></p>
  <?php else : ?>
  <div class="rankings regulator-roster">
    <div class="rank-row head">
      <span></span><span><?php globalfxhub_te( 'table_broker' ); ?></span><span class="col-fees"><?php echo esc_html( $entity['tracks_licence_numbers'] ? globalfxhub_t( 'regulator_th_licence_number' ) : globalfxhub_t( 'regulator_th_status' ) ); ?></span><span></span>
    </div>
    <?php foreach ( $brokers as $i => $row ) : ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker"><span class="rank-broker__name"><?php echo esc_html( $row['name'] ); ?></span><?php if ( $row['entity'] ) : ?><span class="rank-broker__tag"><?php echo esc_html( $row['entity'] ); ?></span><?php endif; ?></span>
      <span class="rank-detail col-fees"><?php
        if ( $entity['tracks_licence_numbers'] ) {
            echo esc_html( $row['licence_number'] ? $row['licence_number'] : globalfxhub_t( 'tb_not_independently_confirmed' ) );
        } else {
            echo esc_html( globalfxhub_t( 'regulator_licence_held_untracked' ) );
        }
      ?></span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( $row['review_url'] ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

    <?php endif; ?>

<?php else : ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> / <?php globalfxhub_te( 'nav_regulators' ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'regulators_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'nav_regulators' ); ?></h1>
  <p><?php
  $regulators_index_regulation_link = '<a href="' . esc_url( globalfxhub_templated_page_url( 'regulation' ) ) . '">' . esc_html( globalfxhub_t( 'regulators_index_link_regulation' ) ) . '</a>';
  $regulators_index_data_link = '<a href="' . esc_url( globalfxhub_templated_page_url( 'data' ) ) . '">' . esc_html( globalfxhub_t( 'ptitle_data' ) ) . '</a>';
  echo sprintf( esc_html( globalfxhub_t( 'regulators_index_intro' ) ), count( globalfxhub_get_brokers() ), $regulators_index_regulation_link, $regulators_index_data_link );
  ?></p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="guides__grid">
    <?php foreach ( $entities as $slug => $entity_data ) :
        $entity_stats = globalfxhub_regulator_entity_stats( $slug );
    ?>
    <a href="<?php echo esc_url( globalfxhub_track_b_url( 'regulators/' . $slug . '/' ) ); ?>" class="guide">
      <h3><?php echo esc_html( $entity_data['name'] ); ?></h3>
      <p><?php echo esc_html( $entity_data['full_name'] ); ?> &mdash; <?php echo esc_html( sprintf( globalfxhub_t( 'regulators_card_stat' ), $entity_stats['held'], $entity_stats['total'] ) ); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
