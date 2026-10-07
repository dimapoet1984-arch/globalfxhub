<?php get_header(); ?>
<?php
$globalfxhub_market = globalfxhub_get_market_snapshot();
$globalfxhub_market_symbols = $globalfxhub_market['symbols'];
$globalfxhub_biggest_mover = null;
foreach ( $globalfxhub_market_symbols as $s ) {
    if ( null === $s['percent_change'] ) {
        continue;
    }
    if ( ! $globalfxhub_biggest_mover || abs( $s['percent_change'] ) > abs( $globalfxhub_biggest_mover['percent_change'] ) ) {
        $globalfxhub_biggest_mover = $s;
    }
}
$globalfxhub_movers_sorted = $globalfxhub_market_symbols;
usort( $globalfxhub_movers_sorted, function( $a, $b ) { return $b['percent_change'] <=> $a['percent_change']; } );
$globalfxhub_gainers = array_slice( $globalfxhub_movers_sorted, 0, 5 );
$globalfxhub_losers  = array_reverse( array_slice( $globalfxhub_movers_sorted, -5 ) );

$globalfxhub_broker_count = count( globalfxhub_get_brokers() );
$globalfxhub_article_count = 0;
foreach ( array( 'guides', 'candlestick-patterns' ) as $globalfxhub_cat_slug ) {
    $globalfxhub_cat_term = get_term_by( 'slug', $globalfxhub_cat_slug, 'category' );
    if ( $globalfxhub_cat_term ) {
        $globalfxhub_article_count += (int) $globalfxhub_cat_term->count;
    }
}
?>

<section class="hero banner" style="padding-top:0;">
  <div class="banner__track" id="bannerTrack">

    <div class="banner__slide" style="background:linear-gradient(120deg, #0e1c30, #16283f);">
      <div class="wrap banner__grid">
        <div>
          <div class="banner__eyebrow"><?php globalfxhub_te( 'hero1_eyebrow' ); ?></div>
          <h1><?php globalfxhub_te( 'hero1_h1' ); ?></h1>
          <p><?php globalfxhub_te( 'hero1_p' ); ?></p>
          <div class="banner__search-label"><?php globalfxhub_te( 'hero_search_label' ); ?></div>
          <div class="searchbar-wrap">
            <form class="searchbar" id="brokerSearchForm">
              <input type="text" id="brokerSearchInput" autocomplete="off" placeholder="<?php echo esc_attr( globalfxhub_t( 'hero_search_placeholder' ) ); ?>">
              <button type="submit"><?php globalfxhub_te( 'hero_search_button' ); ?></button>
            </form>
            <div class="search-results" id="brokerSearchResults" hidden></div>
          </div>
        </div>
        <div class="snapshot">
          <div class="snapshot__head"><span>Top rated this quarter</span><span>Score</span></div>
          <div class="snapshot__row"><span class="snapshot__rank">01</span><span><span class="snapshot__name">IG</span><br><span class="snapshot__meta">CySEC 309/16 · est. 1974</span></span><span class="snapshot__score">4.13</span></div>
          <div class="snapshot__row"><span class="snapshot__rank">02</span><span><span class="snapshot__name">AvaTrade</span><br><span class="snapshot__meta">est. 2006</span></span><span class="snapshot__score">4.12</span></div>
          <div class="snapshot__row"><span class="snapshot__rank">03</span><span><span class="snapshot__name">FOREX.com</span><br><span class="snapshot__meta">CySEC 400/21 · est. 1999</span></span><span class="snapshot__score">3.83</span></div>
        </div>
      </div>
    </div>

    <div class="banner__slide" style="background:linear-gradient(120deg, #16283f, #2f6f5e);">
      <div class="wrap banner__grid">
        <div>
          <div class="banner__eyebrow"><?php globalfxhub_te( 'hero2_eyebrow' ); ?></div>
          <h1><?php globalfxhub_te( 'hero2_h1' ); ?></h1>
          <p><?php globalfxhub_te( 'hero2_p' ); ?></p>
          <a href="#overview" class="btn btn--gold"><?php globalfxhub_te( 'hero2_button' ); ?></a>
        </div>
        <div class="snapshot">
          <div class="snapshot__head"><span>Today's biggest mover</span><span>Change</span></div>
          <?php if ( $globalfxhub_biggest_mover ) : ?>
          <div class="snapshot__row"><span class="snapshot__rank">🔥</span><span><span class="snapshot__name"><?php echo esc_html( $globalfxhub_biggest_mover['label'] ); ?></span><br><span class="snapshot__meta"><?php echo esc_html( $globalfxhub_biggest_mover['category'] ); ?></span></span><span class="snapshot__score"><?php echo esc_html( globalfxhub_format_change( $globalfxhub_biggest_mover['percent_change'] ) ); ?></span></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="banner__slide" style="background:linear-gradient(120deg, #7a5a2e, #b8862f);">
      <div class="wrap banner__grid">
        <div>
          <div class="banner__eyebrow"><?php globalfxhub_te( 'hero3_eyebrow' ); ?></div>
          <h1><?php globalfxhub_te( 'hero3_h1' ); ?></h1>
          <p><?php globalfxhub_te( 'hero3_p' ); ?></p>
          <a href="#latest-articles" class="btn btn--gold"><?php globalfxhub_te( 'hero3_button' ); ?></a>
        </div>
        <div class="snapshot">
          <div class="snapshot__head"><span>Most read this week</span><span></span></div>
          <div class="snapshot__row"><span class="snapshot__rank">01</span><span><span class="snapshot__name">What Is Forex?</span></span><span></span></div>
          <div class="snapshot__row"><span class="snapshot__rank">02</span><span><span class="snapshot__name">How Gold Trading Works</span></span><span></span></div>
        </div>
      </div>
    </div>

  </div>

  <button class="banner__arrow banner__arrow--prev" id="bannerPrev" aria-label="Previous">‹</button>
  <button class="banner__arrow banner__arrow--next" id="bannerNext" aria-label="Next">›</button>
  <div class="banner__dots" id="bannerDots">
    <button class="banner__dot active" data-i="0" aria-label="Slide 1"></button>
    <button class="banner__dot" data-i="1" aria-label="Slide 2"></button>
    <button class="banner__dot" data-i="2" aria-label="Slide 3"></button>
  </div>
</section>

<script>
// Broker index + matching logic live in header.php (GlobalFXHubBrokerSearch)
// so the nav search bar and this hero search bar share one implementation.
GlobalFXHubBrokerSearch.init(
  document.getElementById('brokerSearchForm'),
  document.getElementById('brokerSearchInput'),
  document.getElementById('brokerSearchResults')
);
</script>

<script>
(function(){
  var track = document.getElementById('bannerTrack');
  var prev = document.getElementById('bannerPrev');
  var next = document.getElementById('bannerNext');
  var dots = document.querySelectorAll('#bannerDots .banner__dot');
  if (!track || !dots.length) return;

  var count = dots.length;
  var index = 0;

  function show(i){
    index = (i + count) % count;
    track.style.transform = 'translateX(-' + (index * (100 / count)) + '%)';
    dots.forEach(function(dot, di){ dot.classList.toggle('active', di === index); });
  }

  prev && prev.addEventListener('click', function(){ show(index - 1); });
  next && next.addEventListener('click', function(){ show(index + 1); });
  dots.forEach(function(dot, di){ dot.addEventListener('click', function(){ show(di); }); });

  var timer = setInterval(function(){ show(index + 1); }, 7000);
  track.closest('.hero.banner').addEventListener('mouseenter', function(){ clearInterval(timer); });
  track.closest('.hero.banner').addEventListener('mouseleave', function(){ timer = setInterval(function(){ show(index + 1); }, 7000); });
})();
</script>

<div class="trustbar">
  <div class="wrap trustbar__grid">
    <div>
      <div class="trustbar__num"><?php echo esc_html( $globalfxhub_broker_count ); ?></div>
      <div class="trustbar__label"><?php globalfxhub_te( 'trust1' ); ?></div>
    </div>
    <div>
      <div class="trustbar__num">4</div>
      <div class="trustbar__label"><?php globalfxhub_te( 'trust2' ); ?></div>
    </div>
    <div>
      <div class="trustbar__num"><?php echo esc_html( $globalfxhub_article_count ); ?></div>
      <div class="trustbar__label"><?php globalfxhub_te( 'trust3' ); ?></div>
    </div>
    <div>
      <div class="trustbar__num">$0</div>
      <div class="trustbar__label"><?php globalfxhub_te( 'trust4' ); ?></div>
    </div>
  </div>
</div>

<section id="overview">
  <div class="wrap">
    <div class="section__head">
      <div>
        <h2><?php globalfxhub_te( 'sec_overview_h2' ); ?></h2>
        <p><?php globalfxhub_te( 'sec_overview_p' ); ?></p>
      </div>
      <?php if ( $globalfxhub_market['is_live'] && $globalfxhub_market['last_updated'] ) : ?>
      <span class="section__link" style="cursor:default;border-bottom:none;color:var(--ink-soft);">Updated <?php echo esc_html( human_time_diff( $globalfxhub_market['last_updated'] ) ); ?> ago</span>
      <?php else : ?>
      <span class="section__link" style="cursor:default;border-bottom:none;color:var(--ink-soft);">Illustrative data · demo</span>
      <?php endif; ?>
    </div>

    <div class="overview__grid">
      <div class="overview__panel">
        <h3><?php globalfxhub_te( 'sec_heatmap_h3' ); ?></h3>
        <p class="sub">Green = gaining, red = losing. Deeper color means a bigger move today.</p>
        <div class="heatmap">
          <?php foreach ( $globalfxhub_market_symbols as $s ) : ?>
          <div class="heat-cell" style="background:<?php echo esc_attr( globalfxhub_heat_color( $s['percent_change'] ) ); ?>;"><span class="sym"><?php echo esc_html( $s['label'] ); ?></span><span class="chg"><?php echo esc_html( globalfxhub_format_change( $s['percent_change'] ) ); ?></span></div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="overview__panel">
        <h3><?php globalfxhub_te( 'sec_movers_h3' ); ?></h3>
        <p class="sub">Biggest gainers and losers across major instruments.</p>
        <div class="movers-tabs">
          <button class="movers-tab active" data-target="movers-gainers">Gainers</button>
          <button class="movers-tab" data-target="movers-losers">Losers</button>
        </div>
        <ul class="movers-list" id="movers-gainers">
          <?php foreach ( $globalfxhub_gainers as $s ) :
              $up = null === $s['percent_change'] || $s['percent_change'] >= 0;
          ?>
          <li><span class="sym"><?php echo esc_html( $s['label'] ); ?> <span class="sub"><?php echo esc_html( $s['category'] ); ?></span></span><span class="chg <?php echo $up ? 'chg--up' : 'chg--down'; ?>"><?php echo esc_html( globalfxhub_format_change( $s['percent_change'] ) ); ?></span></li>
          <?php endforeach; ?>
        </ul>
        <ul class="movers-list" id="movers-losers" style="display:none;">
          <?php foreach ( $globalfxhub_losers as $s ) :
              $up = null === $s['percent_change'] || $s['percent_change'] >= 0;
          ?>
          <li><span class="sym"><?php echo esc_html( $s['label'] ); ?> <span class="sub"><?php echo esc_html( $s['category'] ); ?></span></span><span class="chg <?php echo $up ? 'chg--up' : 'chg--down'; ?>"><?php echo esc_html( globalfxhub_format_change( $s['percent_change'] ) ); ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <p style="font-size:12px;color:var(--ink-soft);margin-top:10px;">Market data via Twelve Data<?php echo $globalfxhub_market['is_live'] ? '' : ' -- illustrative until live data is configured'; ?>. For information only, not investment advice.</p>
  </div>
</section>

<script>
(function(){
  var tabs = document.querySelectorAll('.movers-tab');
  if (!tabs.length) return;
  tabs.forEach(function(tab){
    tab.addEventListener('click', function(){
      tabs.forEach(function(t){ t.classList.remove('active'); });
      tab.classList.add('active');
      var gainers = document.getElementById('movers-gainers');
      var losers = document.getElementById('movers-losers');
      var showGainers = tab.getAttribute('data-target') === 'movers-gainers';
      if (gainers) gainers.style.display = showGainers ? '' : 'none';
      if (losers) losers.style.display = showGainers ? 'none' : '';
    });
  });
})();
</script>

<section id="from-the-blog">
  <div class="wrap">
    <div class="section__head">
      <div>
        <h2><?php globalfxhub_te( 'sec_blog_h2' ); ?></h2>
        <p><?php globalfxhub_te( 'sec_blog_p' ); ?></p>
      </div>
      <?php $blog_page = get_option( 'page_for_posts' ); ?>
      <a href="<?php echo esc_url( $blog_page ? get_permalink( $blog_page ) : home_url( '/blog/' ) ); ?>" class="section__link"><?php globalfxhub_te( 'link_all_articles' ); ?></a>
    </div>
    <div class="guides__grid">
      <?php
      $recent = new WP_Query( array(
          'post_type'      => 'post',
          'posts_per_page' => 6,
          'ignore_sticky_posts' => true,
      ) );
      if ( $recent->have_posts() ) :
          while ( $recent->have_posts() ) : $recent->the_post();
      ?>
      <a href="<?php the_permalink(); ?>" class="guide">
        <div class="guide__time"><?php echo esc_html( globalfxhub_reading_time( get_the_content() ) ); ?> MIN READ</div>
        <h3><?php the_title(); ?></h3>
        <p><?php echo esc_html( globalfxhub_trim_excerpt( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 18 ) ); ?></p>
      </a>
      <?php
          endwhile;
          wp_reset_postdata();
      else :
      ?>
      <p style="padding:24px;color:var(--ink-soft);">No articles published yet — add your first post in wp-admin and it will show up here automatically.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php
$globalfxhub_news_query = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 5,
    'category_name'  => 'news',
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
?>
<section id="market-news">
  <div class="wrap">
    <div class="section__head">
      <div>
        <h2><?php globalfxhub_te( 'sec_news_h2' ); ?></h2>
        <p><?php globalfxhub_te( 'sec_news_p' ); ?></p>
      </div>
      <a href="<?php echo esc_url( home_url( '/news/' ) ); ?>" class="section__link"><?php globalfxhub_te( 'link_all_news' ); ?></a>
    </div>
    <div class="news__grid">
      <?php if ( $globalfxhub_news_query->have_posts() ) : while ( $globalfxhub_news_query->have_posts() ) : $globalfxhub_news_query->the_post();
          $globalfxhub_news_type = get_post_meta( get_the_ID(), '_news_type', true );
      ?>
      <a href="<?php the_permalink(); ?>" class="news-item">
        <?php if ( has_post_thumbnail() ) : ?>
        <?php the_post_thumbnail( 'medium_large', array( 'class' => 'news-item__thumb' ) ); ?>
        <?php endif; ?>
        <div class="tag<?php echo 'market' === $globalfxhub_news_type ? ' market' : ''; ?>"><?php echo 'market' === $globalfxhub_news_type ? 'MARKET NEWS' : 'BROKER NEWS'; ?></div>
        <h4><?php the_title(); ?></h4>
        <p><?php echo esc_html( globalfxhub_trim_excerpt( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 20 ) ); ?></p>
        <time><?php echo esc_html( get_the_date() ); ?></time>
      </a>
      <?php endwhile; wp_reset_postdata(); else : ?>
      <p style="padding:24px;color:var(--ink-soft);">No news published yet.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<section id="rankings">
  <div class="wrap">
    <div class="section__head">
      <div>
        <h2><?php globalfxhub_te( 'sec_rankings_h2' ); ?></h2>
        <p><?php globalfxhub_te( 'sec_rankings_p' ); ?></p>
      </div>
      <a href="#method" class="section__link"><?php globalfxhub_te( 'link_how_we_score' ); ?></a>
    </div>

    <div class="rankings">
      <div class="rank-row head">
        <span></span>
        <span><?php globalfxhub_te( 'table_broker' ); ?></span>
        <span class="col-fees"><?php globalfxhub_te( 'table_spread' ); ?></span>
        <span class="col-plat"><?php globalfxhub_te( 'table_platforms' ); ?></span>
        <span><?php globalfxhub_te( 'table_score' ); ?></span>
        <span></span>
      </div>
      <?php
      $fp_brokers = globalfxhub_get_brokers();
      usort( $fp_brokers, function( $a, $b ) { return $a['rank'] <=> $b['rank']; } );
      $fp_top15 = array_slice( $fp_brokers, 0, 15 );
      foreach ( $fp_top15 as $fp_b ) :
          $fp_tag = globalfxhub_broker_regulation_label( $fp_b );
          if ( $fp_b['founded'] ) {
              $fp_tag .= ' &middot; est. ' . $fp_b['founded'];
          }
      ?>
      <div class="rank-row">
        <span class="rank-num"><?php echo esc_html( str_pad( (string) $fp_b['rank'], 2, '0', STR_PAD_LEFT ) ); ?></span>
        <span class="rank-broker">
          <span class="rank-broker__name"><?php echo esc_html( $fp_b['name'] ); ?></span>
          <span class="rank-broker__tag"><?php echo esc_html( $fp_tag ); ?></span>
        </span>
        <span class="rank-detail col-fees"><?php echo null !== $fp_b['spread_eurusd'] ? esc_html( $fp_b['spread_eurusd'] ) . ' pips (EUR/USD)' : 'Not confirmed'; ?></span>
        <span class="rank-detail col-plat"><?php echo esc_html( $fp_b['platforms'] ? implode( ', ', $fp_b['platforms'] ) : 'Not confirmed' ); ?></span>
        <span class="rank-score"><?php echo esc_html( $fp_b['scores']['overall'] ); ?> / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/' . $fp_b['slug'] . '/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=' . $fp_b['slug'] ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <?php endforeach; ?>
    </div>
    <p style="font-size:12.5px;color:var(--ink-soft);margin-top:14px;">Figures shown are standard-account averages compiled from broker disclosures and third-party research as of March 2026. Spreads, minimum deposits and licence status change — verify current terms directly with the broker and on the <a href="https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/" target="_blank" rel="noopener" style="color:var(--teal);">CySEC public register</a> before making a decision.</p>
  </div>
</section>

<section>
  <div class="wrap">
    <?php
    $fp_no1 = $fp_top15[0];
    $fp_no1_tag = globalfxhub_broker_regulation_label( $fp_no1 );
    ?>
    <div class="award">
      <div class="award__badge">2026<b>#1</b>TOP-SCORED BROKER</div>
      <div>
        <h2><?php echo esc_html( $fp_no1['name'] ); ?> tops our broker rankings for 2026</h2>
        <p><?php echo esc_html( $fp_no1['name'] ); ?> scores highest under our disclosed, nine-category methodology (<?php echo esc_html( $fp_no1['scores']['overall'] ); ?>/5)<?php echo $fp_no1['founded'] ? ', operating since ' . esc_html( $fp_no1['founded'] ) : ''; ?>. Regulatory status: <?php echo esc_html( $fp_no1_tag ); ?>.</p>
      </div>
    </div>
  </div>
</section>

<section class="method" id="method">
  <div class="wrap">
    <div class="section__head">
      <div>
        <h2><?php globalfxhub_te( 'sec_method_h2' ); ?></h2>
        <p><?php globalfxhub_te( 'sec_method_p' ); ?></p>
      </div>
    </div>
    <div class="method__grid">
      <div class="method__cell">
        <div class="num">30%</div>
        <h3><?php globalfxhub_te( 'method1_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method1_p' ); ?></p>
      </div>
      <div class="method__cell">
        <div class="num">20%</div>
        <h3><?php globalfxhub_te( 'method2_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method2_p' ); ?></p>
      </div>
      <div class="method__cell">
        <div class="num">10%</div>
        <h3><?php globalfxhub_te( 'method3_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method3_p' ); ?></p>
      </div>
      <div class="method__cell">
        <div class="num">10%</div>
        <h3><?php globalfxhub_te( 'method4_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method4_p' ); ?></p>
      </div>
      <div class="method__cell">
        <div class="num">10%</div>
        <h3><?php globalfxhub_te( 'method5_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method5_p' ); ?></p>
      </div>
      <div class="method__cell">
        <div class="num">5%</div>
        <h3><?php globalfxhub_te( 'method6_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method6_p' ); ?></p>
      </div>
      <div class="method__cell">
        <div class="num">5%</div>
        <h3><?php globalfxhub_te( 'method7_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method7_p' ); ?></p>
      </div>
      <div class="method__cell">
        <div class="num">5%</div>
        <h3><?php globalfxhub_te( 'method8_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method8_p' ); ?></p>
      </div>
      <div class="method__cell">
        <div class="num">5%</div>
        <h3><?php globalfxhub_te( 'method9_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method9_p' ); ?></p>
      </div>
    </div>
    <p style="font-size:13px;color:#aab6c6;margin-top:24px;max-width:70ch;">Every factor is scored against a fixed, disclosed threshold -- an absolute rubric, not a ranking relative to other brokers in our set. Adding, removing, or re-researching a broker never changes anyone else's score. Source data comes from each broker's regulator(s) of record (CySEC, the UK's FCA, the Seychelles FSA, and others), broker legal disclosures, and third-party broker research, compiled on a rolling basis. Where we can't independently confirm a fact, that category either scores a neutral midpoint or is left out of that broker's average, rather than guessed at. This methodology does not involve opening or funding live accounts, and it isn't personalized financial advice — always verify current licence status, fees, and terms directly with the broker before depositing funds.</p>
  </div>
</section>

<span id="latest-articles"></span>
<section id="guides" style="scroll-margin-top:20px;">
  <div class="wrap">
    <div class="section__head">
      <div>
        <h2><?php globalfxhub_te( 'sec_guides_h2' ); ?></h2>
        <p><?php globalfxhub_te( 'sec_guides_p' ); ?></p>
      </div>
      <a href="<?php echo esc_url( home_url( '/guides/' ) ); ?>" class="section__link"><?php globalfxhub_te( 'link_all_guides' ); ?></a>
    </div>
    <div class="guides__grid">
      <?php
      $home_guides = new WP_Query( array(
          'post_type'      => 'post',
          'posts_per_page' => 6,
          'category_name'  => 'guides',
          'orderby'        => 'date',
          'order'          => 'ASC',
      ) );
      if ( $home_guides->have_posts() ) :
          while ( $home_guides->have_posts() ) : $home_guides->the_post();
      ?>
      <a href="<?php the_permalink(); ?>" class="guide">
        <div class="guide__time"><?php echo esc_html( globalfxhub_reading_time( get_the_content() ) ); ?> MIN READ</div>
        <h3><?php the_title(); ?></h3>
        <p><?php echo esc_html( globalfxhub_trim_excerpt( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 20 ) ); ?></p>
      </a>
      <?php
          endwhile;
          wp_reset_postdata();
      else :
      ?>
      <p style="padding:24px;color:var(--ink-soft);">No guides published yet.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<section id="compare">
  <div class="wrap">
    <div class="section__head">
      <div>
        <h2><?php globalfxhub_te( 'sec_compare_h2' ); ?></h2>
        <p><?php globalfxhub_te( 'sec_compare_p' ); ?></p>
      </div>
      <a href="<?php echo esc_url( home_url( '/compare/' ) ); ?>" class="section__link"><?php globalfxhub_te( 'link_open_compare' ); ?></a>
    </div>
    <div class="countries__row">
      <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=ig&b=xtb' ); ?>" class="country-pill">IG vs XTB</a>
      <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=etoro&b=plus500' ); ?>" class="country-pill">eToro vs Plus500</a>
      <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=xm&b=pepperstone' ); ?>" class="country-pill">XM vs Pepperstone</a>
      <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=ic-markets&b=fxpro' ); ?>" class="country-pill">IC Markets vs FxPro</a>
      <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=fxcm&b=capital-com' ); ?>" class="country-pill">FXCM vs Capital.com</a>
      <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=avatrade&b=tickmill' ); ?>" class="country-pill">AvaTrade vs Tickmill</a>
      <a href="<?php echo esc_url( home_url( '/compare/' ) ); ?>" class="country-pill">Pick your own →</a>
    </div>
  </div>
</section>

<section id="countries">
  <div class="wrap">
    <div class="section__head">
      <div>
        <h2><?php globalfxhub_te( 'sec_countries_h2' ); ?></h2>
        <p><?php globalfxhub_te( 'sec_countries_p' ); ?></p>
      </div>
    </div>
    <div class="countries__row">
      <a href="#" class="country-pill">🇺🇸 United States</a>
      <a href="#" class="country-pill">🇬🇧 United Kingdom</a>
      <a href="#" class="country-pill">🇨🇦 Canada</a>
      <a href="#" class="country-pill">🇦🇺 Australia</a>
      <a href="#" class="country-pill">🇮🇳 India</a>
      <a href="#" class="country-pill">🇿🇦 South Africa</a>
      <a href="#" class="country-pill">🇳🇬 Nigeria</a>
      <a href="#" class="country-pill">🇵🇭 Philippines</a>
      <a href="#" class="country-pill">🇦🇪 UAE</a>
      <a href="#" class="country-pill">All countries →</a>
    </div>
  </div>
</section>

<?php get_footer(); ?>
