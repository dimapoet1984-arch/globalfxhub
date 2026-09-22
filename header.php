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
    <div class="nav__right">
      <div class="nav-search">
        <form id="navSearchForm" class="nav-search__form" autocomplete="off">
          <input type="search" id="navSearchInput" placeholder="Search a broker…" aria-label="Search a broker">
        </form>
        <div class="search-results" id="navSearchResults" hidden></div>
      </div>
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
  </div>
</header>

<script>
var globalfxhubBrokerSearchIndex = <?php echo wp_json_encode( array_map( function( $b ) {
    $reg = ( '—' === $b['cysec'] ) ? 'EU-regulated' : ( 'CySEC ' . $b['cysec'] );
    return array(
        'name' => $b['name'],
        'slug' => $b['slug'],
        'meta' => $reg . ( $b['founded'] ? ' · est. ' . $b['founded'] : '' ),
    );
}, globalfxhub_get_brokers() ) ); ?>;

/**
 * Shared broker typeahead, used by both this nav search bar and the
 * homepage hero's search bar -- one broker index, one matching/rendering
 * implementation, wired up per instance via init().
 */
var GlobalFXHubBrokerSearch = {
  normalize: function(s) { return (s || '').toLowerCase().trim(); },
  findMatches: function(query) {
    var self = this;
    query = this.normalize(query);
    if (!query) return [];
    var starts = [], contains = [];
    globalfxhubBrokerSearchIndex.forEach(function(b){
      var name = self.normalize(b.name);
      if (name.indexOf(query) === 0) starts.push(b);
      else if (name.indexOf(query) !== -1) contains.push(b);
    });
    return starts.concat(contains).slice(0, 6);
  },
  init: function(formEl, inputEl, resultsEl) {
    var self = this;
    if (!formEl || !inputEl || !resultsEl) return;

    function render(list) {
      if (!list.length) { resultsEl.hidden = true; resultsEl.innerHTML = ''; return; }
      resultsEl.innerHTML = list.map(function(b){
        return '<a href="/reviews/' + b.slug + '/" class="search-results__item"><span>' + b.name + '</span><span class="search-results__meta">' + b.meta + '</span></a>';
      }).join('');
      resultsEl.hidden = false;
    }

    inputEl.addEventListener('input', function(){ render(self.findMatches(inputEl.value)); });
    inputEl.addEventListener('focus', function(){ if (inputEl.value) render(self.findMatches(inputEl.value)); });
    inputEl.addEventListener('blur', function(){ setTimeout(function(){ resultsEl.hidden = true; }, 150); });

    formEl.addEventListener('submit', function(e){
      e.preventDefault();
      var list = self.findMatches(inputEl.value);
      if (list.length) {
        window.location.href = '/reviews/' + list[0].slug + '/';
      } else if (self.normalize(inputEl.value)) {
        window.location.href = '/reviews/?search=' + encodeURIComponent(inputEl.value.trim());
      }
    });
  }
};

GlobalFXHubBrokerSearch.init(
  document.getElementById('navSearchForm'),
  document.getElementById('navSearchInput'),
  document.getElementById('navSearchResults')
);
</script>

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
