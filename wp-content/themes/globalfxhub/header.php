<!DOCTYPE html>
<html lang="<?php language_attributes(); ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="ticker" aria-hidden="true">
  <div class="ticker__track">
    <span><b>EUR/USD</b> 1.0834 <span class="ticker__up">▲ 0.12%</span></span>
    <span><b>GBP/USD</b> 1.2651 <span class="ticker__down">▼ 0.08%</span></span>
    <span><b>USD/JPY</b> 149.32 <span class="ticker__up">▲ 0.21%</span></span>
    <span><b>AUD/USD</b> 0.6598 <span class="ticker__down">▼ 0.04%</span></span>
    <span><b>USD/CAD</b> 1.3572 <span class="ticker__up">▲ 0.06%</span></span>
    <span><b>XAU/USD</b> 2,412.80 <span class="ticker__up">▲ 0.34%</span></span>
    <span><b>EUR/USD</b> 1.0834 <span class="ticker__up">▲ 0.12%</span></span>
    <span><b>GBP/USD</b> 1.2651 <span class="ticker__down">▼ 0.08%</span></span>
    <span><b>USD/JPY</b> 149.32 <span class="ticker__up">▲ 0.21%</span></span>
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
    <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" class="nav__cta">See rankings</a>
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
