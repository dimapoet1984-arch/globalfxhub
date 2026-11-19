<?php
/**
 * Template Name: Regulation
 * Description: /regulation/ index of the regulatory knowledge base, and
 * /regulation/{slug}/ for each individual page. See inc/regulation.php
 * for the content -- every compensation figure and register URL there
 * matches exactly what globalfxhub_broker_verify_links() and
 * globalfxhub_broker_investor_protection() use per-broker, so this and
 * every review page stay consistent with each other by construction.
 */
get_header();

$regpage_slug = get_query_var( 'regpage' );
$reg_pages = globalfxhub_get_regulation_pages();
$reg_comparison = $regpage_slug ? globalfxhub_get_localized_regulation_comparison( $regpage_slug ) : null;
?>

<?php if ( $reg_comparison ) : ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> /
  <a href="<?php echo esc_url( globalfxhub_templated_page_url( 'regulation' ) ); ?>"><?php globalfxhub_te( 'nav_regulation' ); ?></a> /
  <?php echo esc_html( $reg_comparison['title'] ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'regulation_eyebrow' ); ?></div>
  <h1><?php echo esc_html( $reg_comparison['title'] ); ?></h1>
  <p><?php echo esc_html( $reg_comparison['intro'] ); ?></p>
</div>

<div class="wrap art-body" style="padding-bottom:20px;">
  <?php foreach ( $reg_comparison['sections'] as $section ) : ?>
  <h2><?php echo esc_html( $section['heading'] ); ?></h2>
  <p><?php echo esc_html( $section['body'] ); ?></p>
  <?php endforeach; ?>
</div>

<div class="wrap" style="padding-bottom:40px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 12px;"><?php globalfxhub_te( 'rev_faq_h2' ); ?></h2>
  <div>
    <?php foreach ( $reg_comparison['faqs'] as $faq ) : ?>
    <div style="margin-bottom:18px;">
      <p style="font-weight:600;margin:0 0 4px;color:var(--navy);"><?php echo esc_html( $faq['q'] ); ?></p>
      <p style="margin:0;color:var(--ink-soft);line-height:1.6;"><?php echo esc_html( $faq['a'] ); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:4px;"><?php
  $regulation_all_pages_link = '<a href="' . esc_url( globalfxhub_templated_page_url( 'regulation' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'regulation_link_text_all_pages' ) ) . '</a>';
  echo sprintf( esc_html( globalfxhub_t( 'regulation_see_all_pages_simple' ) ), $regulation_all_pages_link );
  ?></p>
</div>

<?php elseif ( $regpage_slug && isset( $reg_pages[ $regpage_slug ] ) ) :
    $page_data = $reg_pages[ $regpage_slug ];
    $reg_title = globalfxhub_tf( 'regulation', $regpage_slug, 'title', $page_data['title'] );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> /
  <a href="<?php echo esc_url( globalfxhub_templated_page_url( 'regulation' ) ); ?>"><?php globalfxhub_te( 'nav_regulation' ); ?></a> /
  <?php echo esc_html( $reg_title ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'regulation_eyebrow' ); ?></div>
  <h1><?php echo esc_html( $reg_title ); ?></h1>
  <p><?php echo esc_html( globalfxhub_tf( 'regulation', $regpage_slug, 'summary', $page_data['summary'] ) ); ?></p>
</div>

<div class="wrap art-body" style="padding-bottom:60px;">
  <?php echo wp_kses( globalfxhub_tf( 'regulation', $regpage_slug, 'content', $page_data['content'] ), array(
      'p' => array(), 'h2' => array(), 'h3' => array(), 'strong' => array(), 'em' => array(),
      'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ),
      'ul' => array(), 'li' => array(),
  ) ); ?>
  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:24px;"><?php
  $regulation_all_pages_link = '<a href="' . esc_url( globalfxhub_templated_page_url( 'regulation' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'regulation_link_text_all_pages' ) ) . '</a>';
  $regulation_best_link = '<a href="' . esc_url( globalfxhub_templated_page_url( 'best' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'nav_best' ) ) . '</a>';
  echo sprintf( esc_html( globalfxhub_t( 'regulation_see_all_pages_and_best' ) ), $regulation_all_pages_link, $regulation_best_link );
  ?></p>
</div>

<?php else : ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> / <?php globalfxhub_te( 'nav_regulation' ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'regulation_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'nav_regulation' ); ?></h1>
  <p><?php globalfxhub_te( 'regulation_index_intro' ); ?></p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="guides__grid">
    <?php foreach ( $reg_pages as $slug => $page_data ) : ?>
    <a href="<?php echo esc_url( globalfxhub_track_b_url( 'regulation/' . $slug . '/' ) ); ?>" class="guide">
      <h3><?php echo esc_html( globalfxhub_tf( 'regulation', $slug, 'title', $page_data['title'] ) ); ?></h3>
      <p><?php echo esc_html( globalfxhub_tf( 'regulation', $slug, 'summary', $page_data['summary'] ) ); ?></p>
    </a>
    <?php endforeach; ?>
    <?php foreach ( globalfxhub_get_regulation_comparisons() as $cmp_slug => $cmp_data ) :
        $cmp_localized = globalfxhub_get_localized_regulation_comparison( $cmp_slug );
    ?>
    <a href="<?php echo esc_url( globalfxhub_track_b_url( 'regulation/' . $cmp_slug . '/' ) ); ?>" class="guide">
      <h3><?php echo esc_html( $cmp_localized['title'] ); ?></h3>
      <p><?php echo esc_html( $cmp_localized['meta_description'] ); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
