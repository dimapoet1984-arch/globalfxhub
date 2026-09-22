<!DOCTYPE html>
<html lang="<?php language_attributes(); ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/favicon.ico' ); ?>" sizes="any">
<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( get_template_directory_uri() . '/assets/favicon.svg' ); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( get_template_directory_uri() . '/assets/favicon-32x32.png' ); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( get_template_directory_uri() . '/assets/favicon-16x16.png' ); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( get_template_directory_uri() . '/assets/apple-touch-icon.png' ); ?>">
<link rel="manifest" href="<?php echo esc_url( get_template_directory_uri() . '/assets/site.webmanifest' ); ?>">
<meta name="theme-color" content="#0e1c30">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
$globalfxhub_ticker_snapshot = globalfxhub_get_market_snapshot();
$globalfxhub_ticker_items = array_filter( $globalfxhub_ticker_snapshot['symbols'], function( $s ) { return ! empty( $s['in_ticker'] ); } );
// Repeat the sequence once so the CSS scroll animation loops seamlessly.
$globalfxhub_ticker_render = array_merge( $globalfxhub_ticker_items, $globalfxhub_ticker_items );
?>
<div class="ticker" aria-hidden="true">
  <div class="ticker__track">
    <?php foreach ( $globalfxhub_ticker_render as $s ) :
        $up = $s['percent_change'] >= 0;
    ?>
    <span><b><?php echo esc_html( $s['label'] ); ?></b> <?php echo esc_html( globalfxhub_format_price( $s['close'], $s ) ); ?> <span class="ticker__<?php echo $up ? 'up' : 'down'; ?>"><?php echo $up ? '▲' : '▼'; ?> <?php echo esc_html( number_format( abs( $s['percent_change'] ), 2 ) ); ?>%</span></span>
    <?php endforeach; ?>
  </div>
</div>

<header>
  <div class="nav">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand">Global<em>FXHub</em></a>
    <button class="nav__toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">☰</button>
    <?php
    if ( has_nav_menu( 'primary' ) ) {
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_id'        => 'navLinks',
            'menu_class'     => 'nav__links',
        ) );
    } else {
        globalfxhub_fallback_menu();
    }
    ?>
    <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" class="nav__cta"><?php globalfxhub_te( 'nav_cta' ); ?></a>
    <?php if ( GLOBALFXHUB_LANG_SWITCHER_ENABLED && function_exists( 'pll_the_languages' ) ) : ?>
    <div class="langsel">
      <?php
      $lang_links = pll_the_languages( array(
          'raw'               => 1,
          'hide_if_empty'     => 0,
          'show_flags'        => 0,
          'show_names'        => 1,
          'display_names_as'  => 'name',
      ) );
      if ( $lang_links ) :
      ?>
      <select onchange="if(this.value) window.location.href=this.value;">
        <?php foreach ( $lang_links as $lang ) : ?>
        <option value="<?php echo esc_url( $lang['url'] ); ?>" <?php selected( ! empty( $lang['current_lang'] ) ); ?>>
          <?php echo esc_html( $lang['name'] ); ?>
        </option>
        <?php endforeach; ?>
      </select>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</header>

<script>
  (function(){
    var toggle = document.getElementById('navToggle');
    var links = document.getElementById('navLinks');
    if (!toggle || !links) return;
    toggle.addEventListener('click', function () {
      var open = links.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  })();
</script>
