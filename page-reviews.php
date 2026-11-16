<?php
/**
 * Template Name: Broker Reviews
 * Description: Single review page per broker at /reviews/{slug}/, a
 * review index at /reviews/, and "alternatives to" pages at
 * /reviews/{slug}/alternatives/ for a bounded set of reference brokers
 * (top 40 by rank) -- see inc/broker-alternatives.php.
 */
get_header();

$slug = get_query_var( 'broker' );
if ( ! $slug && isset( $_GET['broker'] ) ) {
    $slug = sanitize_title( wp_unslash( $_GET['broker'] ) );
}
$broker = $slug ? globalfxhub_get_broker_by_slug( $slug ) : null;

$alts_slug = get_query_var( 'broker_alts' );
$alts_data = $alts_slug ? globalfxhub_broker_alternatives( $alts_slug ) : null;
if ( $alts_slug && ! $alts_data ) {
    status_header( 404 );
}

$research_slug = get_query_var( 'broker_research' );
$research_kind = get_query_var( 'broker_research_kind' );
$research_entry = ( $research_slug && $research_kind ) ? globalfxhub_get_localized_broker_research_article( $research_slug, $research_kind ) : null;
if ( $research_slug && $research_kind && ! $research_entry ) {
    status_header( 404 );
}
?>

<?php if ( $research_slug && $research_kind ) :
    if ( ! $research_entry ) :
?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'rev_research_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'rev_research_not_available_h1' ); ?></h1>
  <p><?php
  $see_all_link = '<a href="' . esc_url( home_url( '/reviews/' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'rev_see_all_reviews_link' ) ) . '</a>';
  echo sprintf( esc_html( globalfxhub_t( 'rev_research_not_available_body' ) ), $see_all_link );
  ?></p>
</div>

    <?php else :
        $rb = $research_entry['broker'];
    ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> /
  <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>"><?php globalfxhub_te( 'nav_reviews' ); ?></a> /
  <a href="<?php echo esc_url( home_url( '/reviews/' . $rb['slug'] . '/' ) ); ?>"><?php echo esc_html( $rb['name'] ); ?></a> /
  <?php echo esc_html( $research_entry['title'] ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'fees' === $research_entry['kind'] ? 'rev_research_eyebrow_fees' : 'rev_research_eyebrow_regulation' ); ?></div>
  <h1><?php echo esc_html( $research_entry['title'] ); ?></h1>
  <p><?php echo esc_html( $research_entry['intro'] ); ?></p>
</div>

<div class="wrap" style="padding-bottom:20px;">
  <?php foreach ( $research_entry['sections'] as $i => $section ) : ?>
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:28px 0 10px;"><?php echo esc_html( $section['heading'] ); ?></h2>
  <p style="color:var(--ink-soft);line-height:1.7;"><?php echo esc_html( $section['body'] ); ?></p>
  <?php if ( 0 === $i ) : ?>
  <p style="margin:10px 0 0;font-size:13.5px;"><a href="<?php echo esc_url( home_url( '/cost-calculator/' ) . '?broker=' . rawurlencode( $rb['slug'] ) ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'rev_research_try_calculator_link' ); ?></a></p>
  <?php endif; ?>
  <?php if ( 'fees' === $research_entry['kind'] && 4 === $i ) : ?>
  <p style="margin:10px 0 0;font-size:13.5px;"><a href="<?php echo esc_url( home_url( '/research/forex-trading-platform-adoption-report/' ) ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'rev_research_full_report_link' ); ?></a></p>
  <?php endif; ?>
  <?php endforeach; ?>
</div>

<div class="wrap" style="padding-bottom:40px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 12px;"><?php globalfxhub_te( 'rev_faq_h2' ); ?></h2>
  <div>
    <?php foreach ( $research_entry['faqs'] as $faq ) : ?>
    <div style="margin-bottom:18px;">
      <p style="font-weight:600;margin:0 0 4px;color:var(--navy);"><?php echo esc_html( $faq['q'] ); ?></p>
      <p style="margin:0;color:var(--ink-soft);line-height:1.6;"><?php echo esc_html( $faq['a'] ); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
  <p style="margin-top:4px;"><a href="<?php echo esc_url( home_url( '/reviews/' . $rb['slug'] . '/' ) ); ?>" style="color:var(--teal);">&larr; <?php echo esc_html( sprintf( globalfxhub_t( 'rev_back_to_full_review' ), $rb['name'] ) ); ?></a></p>
</div>

    <?php endif;
elseif ( $alts_slug ) :
    if ( ! $alts_data ) :
?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'rev_alt_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'rev_alt_not_available_h1' ); ?></h1>
  <p><?php echo esc_html( globalfxhub_t( 'rev_alt_not_available_body' ) ); ?> <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'rev_see_all_reviews_link' ); ?></a>.</p>
</div>

    <?php else :
        $ref = $alts_data['broker'];
        $alt_brokers = $alts_data['alternatives'];
    ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> /
  <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>"><?php globalfxhub_te( 'nav_reviews' ); ?></a> /
  <a href="<?php echo esc_url( home_url( '/reviews/' . $ref['slug'] . '/' ) ); ?>"><?php echo esc_html( $ref['name'] ); ?></a> /
  <?php globalfxhub_te( 'rev_breadcrumb_alternatives' ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'rev_alt_eyebrow' ); ?></div>
  <h1><?php echo esc_html( sprintf( globalfxhub_t( 'rev_alt_title' ), $ref['name'] ) ); ?></h1>
  <p><?php echo esc_html( sprintf( globalfxhub_t( 'rev_alt_intro' ), $ref['name'] ) ); ?></p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <?php if ( empty( $alt_brokers ) ) : ?>
  <p style="padding:24px;color:var(--ink-soft);"><?php globalfxhub_te( 'rev_no_similar_broker' ); ?></p>
  <?php else : ?>
  <div class="alt-cards">
    <?php foreach ( $alt_brokers as $i => $alt ) :
        $vs_pair_check = globalfxhub_vs_pair( $ref['slug'], $alt['slug'] );
    ?>
    <div class="alt-card">
      <div class="alt-card__head">
        <span class="alt-card__rank"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
        <span class="alt-card__name"><?php echo esc_html( $alt['name'] ); ?></span>
        <span class="alt-card__tag"><?php echo esc_html( globalfxhub_broker_regulation_label( $alt ) ); ?></span>
        <span class="alt-card__score"><?php echo esc_html( $alt['scores']['overall'] ?? globalfxhub_t( 'rev_na_abbr' ) ); ?> / 5</span>
      </div>
      <?php if ( ! empty( $alt['reasons'] ) ) : ?>
      <ul class="alt-card__reasons">
        <?php foreach ( $alt['reasons'] as $reason ) : ?>
        <li><?php echo esc_html( $reason ); ?></li>
        <?php endforeach; ?>
      </ul>
      <?php else : ?>
      <p class="alt-card__reasons alt-card__reasons--neutral"><?php echo esc_html( sprintf( globalfxhub_t( 'rev_alt_no_clear_favor' ), $alt['name'], $ref['name'] ) ); ?></p>
      <?php endif; ?>
      <p class="alt-card__meta"><?php echo esc_html( $alt['min_deposit_display'] ? sprintf( globalfxhub_t( 'rev_alt_min_deposit' ), $alt['min_deposit_display'] ) : globalfxhub_t( 'rev_alt_min_deposit_not_confirmed' ) ); ?> &middot; <?php echo esc_html( $alt['platforms'] ? globalfxhub_render_platforms_list( $alt['platforms'] ) : globalfxhub_t( 'rev_alt_platforms_not_confirmed' ) ); ?></p>
      <div class="alt-card__ctas">
        <a href="<?php echo esc_url( home_url( '/reviews/' . $alt['slug'] . '/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
        <?php if ( $vs_pair_check ) : ?>
        <a href="<?php echo esc_url( home_url( '/compare/' . $ref['slug'] . '-vs-' . $alt['slug'] . '/' ) ); ?>" class="rank-cta"><?php echo esc_html( sprintf( globalfxhub_t( 'rev_compare_vs' ), $ref['name'] ) ); ?></a>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <p style="margin-top:20px;"><a href="<?php echo esc_url( home_url( '/reviews/' . $ref['slug'] . '/' ) ); ?>" style="color:var(--teal);">&larr; <?php echo esc_html( sprintf( globalfxhub_t( 'rev_back_to_full_review' ), $ref['name'] ) ); ?></a></p>
</div>

    <?php endif;
elseif ( $broker ) :
    $pc = globalfxhub_broker_pros_cons( $broker );
    $who = globalfxhub_broker_who_for( $broker );
    $investor_protection = globalfxhub_broker_investor_protection( $broker );
    $deep_dive_na = globalfxhub_t( 'rev_deep_dive_na' );
    $deep_dive_fields = array(
        'account_types'          => globalfxhub_t( 'rev_field_account_types' ),
        'execution_model'        => globalfxhub_t( 'rev_field_execution_model' ),
        'withdrawal_deposit_note' => globalfxhub_t( 'rev_field_withdrawal_deposit' ),
        'overnight_financing'    => globalfxhub_t( 'rev_field_overnight_financing' ),
        'currency_conversion_fee' => globalfxhub_t( 'rev_field_currency_conversion' ),
        'inactivity_fee'         => globalfxhub_t( 'rev_field_inactivity_fee' ),
        'customer_service_note'  => globalfxhub_t( 'rev_field_customer_service' ),
        'mobile_note'            => globalfxhub_t( 'rev_field_mobile' ),
        'education_note'         => globalfxhub_t( 'rev_field_education' ),
    );
    $sub_labels = array(
        'regulation'            => globalfxhub_t( 'method1_h3' ),
        'cost'                  => globalfxhub_t( 'method2_h3' ),
        'non_trading_fees'      => globalfxhub_t( 'method3_h3' ),
        'platforms'             => globalfxhub_t( 'method4_h3' ),
        'execution'              => globalfxhub_t( 'method5_h3' ),
        'product_range'         => globalfxhub_t( 'method6_h3' ),
        'deposits_withdrawals'  => globalfxhub_t( 'method7_h3' ),
        'transparency'          => globalfxhub_t( 'method8_h3' ),
        'track_record'          => globalfxhub_t( 'method9_h3' ),
    );
    $reg_label = globalfxhub_broker_regulation_label( $broker );
    $verify_links = globalfxhub_broker_verify_links( $broker );
    $not_confirmed = globalfxhub_t( 'tb_not_independently_confirmed' );
    $review_dates = globalfxhub_broker_review_dates( $broker );
    $section_learn_links = array_map( 'globalfxhub_resolve_learn_link', globalfxhub_review_section_learn_links() );
    $broker_faqs = globalfxhub_broker_faqs( $broker );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> /
  <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>"><?php globalfxhub_te( 'nav_reviews' ); ?></a> /
  <?php echo esc_html( $broker['name'] ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head review-head">
  <div class="eyebrow"><?php echo esc_html( sprintf( globalfxhub_t( 'rev_eyebrow_rank' ), str_pad( (string) $broker['rank'], 2, '0', STR_PAD_LEFT ), count( globalfxhub_get_brokers() ) ) ); ?></div>
  <h1><?php echo esc_html( sprintf( globalfxhub_t( 'rev_title_review' ), $broker['name'] ) ); ?></h1>
  <p><?php echo esc_html( $broker['blurb'] ); ?></p>
  <div class="review-head__meta">
    <span class="review-head__score"><span class="score-num"><?php echo esc_html( $broker['scores']['overall'] ); ?></span> / 5 <?php globalfxhub_te( 'rev_score_overall' ); ?></span>
    <span class="review-head__tag"><?php echo esc_html( $reg_label ); ?><?php echo $broker['founded'] ? ' &middot; ' . esc_html( sprintf( globalfxhub_t( 'rev_founded_inline' ), $broker['founded'] ) ) : ''; ?></span>
  </div>
  <div class="review-head__dates" style="font-size:12.5px;color:var(--ink-soft);margin-top:6px;display:flex;flex-wrap:wrap;gap:14px;">
    <span><?php globalfxhub_te( 'rev_researched_factchecked_by' ); ?> <a href="<?php echo esc_url( home_url( '/editorial-policy/' ) ); ?>" style="color:var(--ink-soft);text-decoration:underline;">GlobalFXHub Research Team</a></span>
    <span><?php globalfxhub_te( 'rev_data_last_reviewed' ); ?> <?php echo esc_html( $review_dates['data_reviewed'] ); ?></span>
    <span><?php globalfxhub_te( 'rev_regulation_last_reviewed' ); ?> <?php echo esc_html( $review_dates['regulation_reviewed'] ); ?></span>
    <span><?php globalfxhub_te( 'rev_pricing_last_checked' ); ?> <?php echo esc_html( $review_dates['pricing_checked'] ); ?></span>
    <span><?php globalfxhub_te( 'rev_next_scheduled_review' ); ?> <?php echo esc_html( $review_dates['next_review'] ); ?></span>
  </div>
  <?php
  $notice_parts = array_filter( array(
      isset( $broker['cysec_note'] ) ? $broker['cysec_note'] : null,
      isset( $broker['fca_note'] ) ? $broker['fca_note'] : null,
      isset( $broker['seychelles_note'] ) ? $broker['seychelles_note'] : null,
  ) );
  if ( ! empty( $notice_parts ) ) :
  ?>
  <div class="review-notice"><strong><?php globalfxhub_te( 'rev_worth_knowing' ); ?></strong> <?php echo esc_html( implode( ' ', $notice_parts ) ); ?></div>
  <?php endif; ?>
  <div class="hero__actions" style="margin-top:22px;">
    <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=' . rawurlencode( $broker['slug'] ) ); ?>" class="btn btn--gold"><?php globalfxhub_te( 'rev_compare_vs_another' ); ?></a>
    <?php foreach ( globalfxhub_editorial_comparisons_for_broker( $broker['slug'] ) as $editorial_vs ) : ?>
    <a href="<?php echo esc_url( $editorial_vs['url'] ); ?>" class="btn btn--ghost" style="color:var(--navy);border-color:var(--rule);"><?php echo esc_html( $broker['name'] . ' vs ' . $editorial_vs['other']['name'] ); ?>: <?php globalfxhub_te( 'rev_vs_our_verdict' ); ?></a>
    <?php endforeach; ?>
    <?php foreach ( globalfxhub_broker_research_articles_for_broker( $broker['slug'] ) as $research_link ) : ?>
    <a href="<?php echo esc_url( $research_link['url'] ); ?>" class="btn btn--ghost" style="color:var(--navy);border-color:var(--rule);"><?php echo esc_html( $research_link['title'] ); ?></a>
    <?php endforeach; ?>
    <a href="<?php echo esc_url( home_url( '/broker-finder/' ) ); ?>" class="btn btn--ghost" style="color:var(--navy);border-color:var(--rule);"><?php globalfxhub_te( 'rev_find_similar' ); ?></a>
    <?php if ( globalfxhub_broker_alternatives( $broker['slug'] ) ) : ?>
    <a href="<?php echo esc_url( home_url( '/reviews/' . $broker['slug'] . '/alternatives/' ) ); ?>" class="btn btn--ghost" style="color:var(--navy);border-color:var(--rule);"><?php globalfxhub_te( 'rev_see_alternatives' ); ?></a>
    <?php endif; ?>
    <?php foreach ( $verify_links as $link ) : ?>
    <a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener" class="btn btn--ghost" style="color:var(--navy);border-color:var(--rule);"><?php echo esc_html( $link['label'] ); ?></a>
    <?php endforeach; ?>
    <a href="#" class="btn btn--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;"><?php globalfxhub_te( 'tb_visit_broker' ); ?></a>
  </div>
</div>

<div class="wrap">
<div class="review-grid">
  <div>
    <table class="compare-table review-facts">
      <tbody>
        <tr><th><?php globalfxhub_te( 'rev_regulated_entity' ); ?></th><td><?php echo esc_html( $broker['entity'] ); ?></td></tr>
        <tr><th><?php globalfxhub_te( 'nav_regulation' ); ?></th><td><?php echo esc_html( $reg_label ); ?></td></tr>
        <tr><th><?php globalfxhub_te( 'rev_other_tier1' ); ?></th><td><?php echo esc_html( $broker['other_reg'] ? implode( ', ', $broker['other_reg'] ) : globalfxhub_t( 'tb_none_confirmed' ) ); ?></td></tr>
        <tr><th><?php globalfxhub_te( 'rev_founded' ); ?></th><td><?php echo esc_html( $broker['founded'] ? $broker['founded'] : $not_confirmed ); ?></td></tr>
        <tr><th><?php globalfxhub_te( 'rev_headquarters' ); ?></th><td><?php echo esc_html( $broker['hq'] ? $broker['hq'] : $not_confirmed ); ?></td></tr>
        <tr><th><?php globalfxhub_te( 'rev_minimum_deposit' ); ?></th><td><?php echo esc_html( $broker['min_deposit_display'] ? $broker['min_deposit_display'] : $not_confirmed ); ?></td></tr>
        <tr><th><?php globalfxhub_te( 'rev_avg_spread_eurusd' ); ?></th><td><?php echo null !== $broker['spread_eurusd'] ? esc_html( $broker['spread_eurusd'] ) . ' pips' : esc_html( $not_confirmed ); ?><?php echo globalfxhub_data_confidence_badge_html( $broker, 'spread_eurusd' ); ?></td></tr>
        <tr><th><?php globalfxhub_te( 'table_platforms' ); ?></th><td><?php echo esc_html( $broker['platforms'] ? globalfxhub_render_platforms_list( $broker['platforms'] ) : $not_confirmed ); ?></td></tr>
        <tr><th><?php globalfxhub_te( 'rev_instruments' ); ?></th><td><?php echo esc_html( $broker['instruments'] ? $broker['instruments'] : $not_confirmed ); ?></td></tr>
      </tbody>
    </table>
    <p style="margin:10px 0 0;font-size:13.5px;"><a href="<?php echo esc_url( home_url( '/regulation-checker/' ) . '?broker=' . rawurlencode( $broker['slug'] ) ); ?>" style="color:var(--teal);"><?php echo esc_html( sprintf( globalfxhub_t( 'rev_see_full_dossier' ), $broker['name'] ) ); ?></a><?php if ( $section_learn_links['regulation'] ) : ?> &middot; <a href="<?php echo esc_url( $section_learn_links['regulation']['url'] ); ?>" style="color:var(--teal);"><?php echo esc_html( $section_learn_links['regulation']['label'] ); ?></a><?php endif; ?></p>

    <div class="review-proscons">
      <div class="review-proscons__col review-proscons__pros">
        <h3><?php globalfxhub_te( 'rev_strengths' ); ?></h3>
        <ul>
          <?php foreach ( $pc['pros'] as $pro ) : ?><li><?php echo esc_html( $pro ); ?></li><?php endforeach; ?>
        </ul>
      </div>
      <div class="review-proscons__col review-proscons__cons">
        <h3><?php globalfxhub_te( 'rev_weaknesses' ); ?></h3>
        <ul>
          <?php foreach ( $pc['cons'] as $con ) : ?><li><?php echo esc_html( $con ); ?></li><?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <div class="review-scorebox">
    <h3><?php globalfxhub_te( 'tb_score_breakdown' ); ?></h3>
    <?php foreach ( $sub_labels as $key => $label ) : ?>
    <div class="subscore-row">
      <?php echo esc_html( $label ); ?>
      <?php if ( null === $broker['scores'][ $key ] ) : ?>
      <span class="review-scorebox__na"><?php globalfxhub_te( 'tb_not_enough_data_score' ); ?></span>
      <?php else : ?>
      <div class="bar-track"><div class="bar-fill" style="width:<?php echo esc_attr( $broker['scores'][ $key ] / 5 * 100 ); ?>%;"></div></div>
      <span class="review-scorebox__num"><?php echo esc_html( $broker['scores'][ $key ] ); ?> / 5</span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</div>

<div class="wrap" style="padding-bottom:20px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 20px;"><?php echo esc_html( sprintf( globalfxhub_t( 'rev_who_fit_title' ), $broker['name'] ) ); ?></h2>
  <div class="review-proscons">
    <div class="review-proscons__col review-proscons__pros">
      <h3><?php globalfxhub_te( 'rev_good_fit_if' ); ?></h3>
      <ul>
        <?php foreach ( $who['for'] as $reason ) : ?><li><?php echo esc_html( $reason ); ?></li><?php endforeach; ?>
      </ul>
    </div>
    <div class="review-proscons__col review-proscons__cons">
      <h3><?php globalfxhub_te( 'rev_look_elsewhere_if' ); ?></h3>
      <ul>
        <?php foreach ( $who['against'] as $reason ) : ?><li><?php echo esc_html( $reason ); ?></li><?php endforeach; ?>
      </ul>
    </div>
  </div>
  <p style="color:var(--ink-soft);font-size:13px;margin-top:10px;"><?php globalfxhub_te( 'rev_who_fit_note' ); ?></p>
</div>

<div class="wrap" style="padding-bottom:20px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 10px;"><?php globalfxhub_te( 'rev_investor_protection_h2' ); ?></h2>
  <p><?php echo esc_html( $investor_protection ); ?></p>
  <?php if ( $section_learn_links['investor_protection'] ) : ?>
  <p style="margin:10px 0 0;font-size:13.5px;"><a href="<?php echo esc_url( $section_learn_links['investor_protection']['url'] ); ?>" style="color:var(--teal);"><?php echo esc_html( $section_learn_links['investor_protection']['label'] ); ?> &rarr;</a></p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:20px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 6px;"><?php globalfxhub_te( 'rev_deep_dive_h2' ); ?></h2>
  <p style="color:var(--ink-soft);font-size:14.5px;margin:0 0 20px;"><?php globalfxhub_te( 'rev_deep_dive_intro' ); ?></p>
  <table class="compare-table review-facts">
    <tbody>
      <?php foreach ( $deep_dive_fields as $field_key => $field_label ) : ?>
      <tr><th><?php echo esc_html( $field_label ); ?></th><td><?php echo esc_html( ! empty( $broker[ $field_key ] ) ? $broker[ $field_key ] : $deep_dive_na ); ?><?php echo globalfxhub_data_confidence_badge_html( $broker, $field_key ); ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p style="margin:10px 0 0;font-size:13.5px;"><a href="<?php echo esc_url( home_url( '/cost-calculator/' ) ); ?>" style="color:var(--teal);"><?php echo esc_html( sprintf( globalfxhub_t( 'rev_see_cost_compare' ), $broker['name'] ) ); ?></a><?php if ( $section_learn_links['fees'] ) : ?> &middot; <a href="<?php echo esc_url( $section_learn_links['fees']['url'] ); ?>" style="color:var(--teal);"><?php echo esc_html( $section_learn_links['fees']['label'] ); ?></a><?php endif; ?></p>
</div>

<div class="wrap" style="padding-bottom:40px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 12px;"><?php globalfxhub_te( 'rev_faq_h2' ); ?></h2>
  <div>
    <?php foreach ( $broker_faqs as $faq ) : ?>
    <div style="margin-bottom:18px;">
      <p style="font-weight:600;margin:0 0 4px;color:var(--navy);"><?php echo esc_html( $faq['q'] ); ?></p>
      <p style="margin:0;color:var(--ink-soft);line-height:1.6;"><?php echo esc_html( $faq['a'] ); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="wrap">
  <div class="methodology-note">
    <strong><?php globalfxhub_te( 'tb_methodology_how_calculated' ); ?></strong> <?php globalfxhub_te( 'tb_methodology_note_body' ); ?> <?php globalfxhub_te( 'tb_methodology_see_the' ); ?> <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'tb_methodology_full_link' ); ?></a>. <?php globalfxhub_te( 'tb_methodology_verify' ); ?>
  </div>
</div>

<?php
$ext = globalfxhub_broker_external_reviews( $broker['slug'] );
if ( ! empty( $ext['reviews'] ) ) :
?>
<div class="wrap" style="padding-bottom:20px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 6px;"><?php globalfxhub_te( 'rev_external_reviews_h2' ); ?></h2>
  <p style="color:var(--ink-soft);font-size:14.5px;margin:0 0 20px;"><?php globalfxhub_te( 'rev_external_reviews_intro' ); ?></p>
  <div class="external-reviews">
    <?php foreach ( $ext['reviews'] as $r ) : ?>
    <a href="<?php echo esc_url( $r['url'] ); ?>" target="_blank" rel="noopener nofollow" class="external-review-card">
      <div class="external-review-card__source"><?php echo esc_html( $r['source'] ); ?></div>
      <?php if ( null !== $r['rating'] ) : ?>
      <div class="external-review-card__score"><?php echo esc_html( $r['rating'] ); ?> <span>/ <?php echo esc_html( $r['scale'] ); ?><?php echo ! empty( $r['label'] ) ? ' ' . esc_html( $r['label'] ) : ''; ?></span></div>
      <?php else : ?>
      <div class="external-review-card__score external-review-card__score--link"><?php globalfxhub_te( 'rev_read_the_review' ); ?></div>
      <?php endif; ?>
      <?php if ( ! empty( $r['note'] ) ) : ?><div class="external-review-card__note"><?php echo esc_html( $r['note'] ); ?></div><?php endif; ?>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php
/*
 * "Baseline recorded" entries (field === null) mark when tracking
 * started for this broker, not a real change -- showing one under a
 * "Recent changes" heading would read as if something had actually
 * changed when nothing has. Only genuine field-diff entries belong
 * here; see inc/broker-changelog.php.
 */
$changelog_entries = array_values( array_filter( globalfxhub_get_broker_changelog( $broker['slug'] ), function( $entry ) {
    return null !== $entry['field'];
} ) );
$changelog_entries = array_slice( $changelog_entries, 0, 5 );
?>
<div class="wrap" style="padding-bottom:40px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 6px;"><?php globalfxhub_te( 'rev_recent_changes_h2' ); ?></h2>
  <p style="color:var(--ink-soft);font-size:14.5px;margin:0 0 20px;"><?php globalfxhub_te( 'rev_recent_changes_intro' ); ?> <?php
  $changelog_link = '<a href="' . esc_url( home_url( '/broker-changelog/' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'rev_change_log_tool_link' ) ) . '</a>';
  echo sprintf( esc_html( globalfxhub_t( 'rev_recent_changes_cta' ) ), $changelog_link );
  ?></p>
  <?php if ( ! empty( $changelog_entries ) ) : ?>
  <div class="rankings">
    <?php foreach ( $changelog_entries as $i => $entry ) : ?>
    <div class="rank-row" style="grid-template-columns:44px 160px 1fr;">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-detail"><?php echo esc_html( $entry['date'] ); ?></span>
      <span class="rank-detail"><?php echo esc_html( $entry['summary'] ); ?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else : ?>
  <p style="color:var(--ink-soft);font-size:14.5px;"><?php globalfxhub_te( 'rev_no_changelog_entries' ); ?></p>
  <?php endif; ?>
</div>

<?php
$others = array_filter( globalfxhub_get_brokers(), function( $b ) use ( $broker ) {
    return $b['slug'] !== $broker['slug'];
} );
usort( $others, function( $a, $b ) { return $a['rank'] <=> $b['rank']; } );
$others = array_slice( $others, 0, 3 );
?>
<div class="wrap" style="padding-bottom:60px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 20px;"><?php globalfxhub_te( 'rev_other_reviewed_h2' ); ?></h2>
  <div class="guides__grid">
    <?php foreach ( $others as $other ) : ?>
    <a href="<?php echo esc_url( home_url( '/reviews/' . $other['slug'] . '/' ) ); ?>" class="guide">
      <div class="guide__time">#<?php echo esc_html( $other['rank'] ); ?> &middot; <?php echo esc_html( $other['scores']['overall'] ); ?> / 5</div>
      <h3><?php echo esc_html( $other['name'] ); ?></h3>
      <p><?php echo esc_html( $other['blurb'] ); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php else :
    $brokers = globalfxhub_get_brokers();
    usort( $brokers, function( $a, $b ) { return $a['rank'] <=> $b['rank']; } );

    $search_query = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
    if ( $search_query ) {
        $needle = strtolower( $search_query );
        $brokers = array_values( array_filter( $brokers, function( $b ) use ( $needle ) {
            return false !== strpos( strtolower( $b['name'] ), $needle );
        } ) );
    }
?>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'rev_index_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'rev_index_h1' ); ?></h1>
  <?php if ( $search_query ) : ?>
  <p><?php echo esc_html( sprintf( globalfxhub_t( 'rev_showing_results_for' ), $search_query ) ); ?> &middot; <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'rev_clear_search' ); ?></a></p>
  <?php else : ?>
  <p><?php globalfxhub_te( 'rev_index_intro' ); ?></p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <?php if ( $search_query && empty( $brokers ) ) : ?>
  <p style="padding:24px;color:var(--ink-soft);"><?php echo esc_html( sprintf( globalfxhub_t( 'rev_no_brokers_matched' ), $search_query ) ); ?> <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'rev_view_all_reviews' ); ?></a></p>
  <?php else : ?>
  <div class="rankings">
    <div class="rank-row head">
      <span></span><span><?php globalfxhub_te( 'table_broker' ); ?></span><span class="col-fees"><?php globalfxhub_te( 'table_spread' ); ?></span><span class="col-plat"><?php globalfxhub_te( 'table_platforms' ); ?></span><span><?php globalfxhub_te( 'table_score' ); ?></span><span></span>
    </div>
    <?php foreach ( $brokers as $b ) : ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) $b['rank'], 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker">
        <span class="rank-broker__name"><?php echo esc_html( $b['name'] ); ?></span>
        <span class="rank-broker__tag"><?php echo esc_html( globalfxhub_broker_regulation_label( $b ) ); ?><?php echo $b['founded'] ? ' &middot; ' . esc_html( sprintf( globalfxhub_t( 'rev_founded_inline' ), $b['founded'] ) ) : ''; ?></span>
      </span>
      <span class="rank-detail col-fees"><?php echo null !== $b['spread_eurusd'] ? esc_html( $b['spread_eurusd'] ) . ' pips (EUR/USD)' : esc_html( globalfxhub_t( 'tb_not_confirmed' ) ); ?></span>
      <span class="rank-detail col-plat"><?php echo esc_html( $b['platforms'] ? globalfxhub_render_platforms_list( $b['platforms'] ) : globalfxhub_t( 'tb_not_confirmed' ) ); ?></span>
      <span class="rank-score"><?php echo esc_html( $b['scores']['overall'] ); ?> / 5</span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( home_url( '/reviews/' . $b['slug'] . '/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
        <a href="#" class="rank-cta rank-cta--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;"><?php globalfxhub_te( 'tb_visit_broker' ); ?></a>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php endif; ?>

<?php get_footer(); ?>
