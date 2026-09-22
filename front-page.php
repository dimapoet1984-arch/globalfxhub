<?php get_header(); ?>

<section class="hero banner" style="padding-top:0;">
  <div class="banner__track" id="bannerTrack">

    <div class="banner__slide" style="background:linear-gradient(120deg, #0e1c30, #16283f);">
      <div class="wrap banner__grid">
        <div>
          <div class="banner__eyebrow"><?php globalfxhub_te( 'hero1_eyebrow' ); ?></div>
          <h1><?php globalfxhub_te( 'hero1_h1' ); ?></h1>
          <p><?php globalfxhub_te( 'hero1_p' ); ?></p>
          <div class="banner__search-label"><?php globalfxhub_te( 'hero_search_label' ); ?></div>
          <form class="searchbar" onsubmit="return false;">
            <input type="text" placeholder="<?php echo esc_attr( globalfxhub_t( 'hero_search_placeholder' ) ); ?>">
            <button type="submit"><?php globalfxhub_te( 'hero_search_button' ); ?></button>
          </form>
        </div>
        <div class="snapshot">
          <div class="snapshot__head"><span>Top rated this quarter</span><span>Score</span></div>
          <div class="snapshot__row"><span class="snapshot__rank">01</span><span><span class="snapshot__name">IG</span><br><span class="snapshot__meta">CySEC 309/16 · est. 1974</span></span><span class="snapshot__score">4.7</span></div>
          <div class="snapshot__row"><span class="snapshot__rank">02</span><span><span class="snapshot__name">FOREX.com</span><br><span class="snapshot__meta">CySEC 400/21 · est. 1999</span></span><span class="snapshot__score">4.1</span></div>
          <div class="snapshot__row"><span class="snapshot__rank">03</span><span><span class="snapshot__name">AvaTrade</span><br><span class="snapshot__meta">est. 2006</span></span><span class="snapshot__score">4.0</span></div>
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
          <div class="snapshot__row"><span class="snapshot__rank">🔥</span><span><span class="snapshot__name">XAU/USD</span><br><span class="snapshot__meta">Gold</span></span><span class="snapshot__score">+1.42%</span></div>
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
      <div class="trustbar__num">3.2M</div>
      <div class="trustbar__label"><?php globalfxhub_te( 'trust1' ); ?></div>
    </div>
    <div>
      <div class="trustbar__num">180+</div>
      <div class="trustbar__label"><?php globalfxhub_te( 'trust2' ); ?></div>
    </div>
    <div>
      <div class="trustbar__num">27</div>
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
      <span class="section__link" style="cursor:default;border-bottom:none;color:var(--ink-soft);">Illustrative data · demo</span>
    </div>

    <div class="overview__grid">
      <div class="overview__panel">
        <h3><?php globalfxhub_te( 'sec_heatmap_h3' ); ?></h3>
        <p class="sub">Green = gaining, red = losing. Deeper color means a bigger move today.</p>
        <div class="heatmap">
          <div class="heat-cell" style="background:#2f6f5e;"><span class="sym">EUR/USD</span><span class="chg">+0.12%</span></div>
          <div class="heat-cell" style="background:#a44432;"><span class="sym">GBP/USD</span><span class="chg">−0.08%</span></div>
          <div class="heat-cell" style="background:#3f8a70;"><span class="sym">USD/JPY</span><span class="chg">+0.21%</span></div>
          <div class="heat-cell" style="background:#c15a44;"><span class="sym">AUD/USD</span><span class="chg">−0.04%</span></div>
          <div class="heat-cell" style="background:#2f6f5e;"><span class="sym">USD/CAD</span><span class="chg">+0.06%</span></div>
          <div class="heat-cell" style="background:#1f5a48;"><span class="sym">XAU/USD</span><span class="chg">+1.42%</span></div>
          <div class="heat-cell" style="background:#8a3f2e;"><span class="sym">WTI Crude</span><span class="chg">−0.95%</span></div>
          <div class="heat-cell" style="background:#a44432;"><span class="sym">Brent</span><span class="chg">−0.71%</span></div>
          <div class="heat-cell" style="background:#3f8a70;"><span class="sym">USD/CHF</span><span class="chg">+0.18%</span></div>
          <div class="heat-cell" style="background:#c15a44;"><span class="sym">NZD/USD</span><span class="chg">−0.22%</span></div>
          <div class="heat-cell" style="background:#2f6f5e;"><span class="sym">XAG/USD</span><span class="chg">+0.64%</span></div>
          <div class="heat-cell" style="background:#8a3f2e;"><span class="sym">Nat Gas</span><span class="chg">−1.18%</span></div>
        </div>
      </div>

      <div class="overview__panel">
        <h3><?php globalfxhub_te( 'sec_movers_h3' ); ?></h3>
        <p class="sub">Biggest gainers and losers across major instruments.</p>
        <div class="movers-tabs">
          <button class="movers-tab active">Gainers</button>
          <button class="movers-tab">Losers</button>
        </div>
        <ul class="movers-list">
          <li><span class="sym">XAU/USD <span class="sub">Gold</span></span><span class="chg chg--up">+1.42%</span></li>
          <li><span class="sym">USD/JPY <span class="sub">Major</span></span><span class="chg chg--up">+0.21%</span></li>
          <li><span class="sym">XAG/USD <span class="sub">Silver</span></span><span class="chg chg--up">+0.64%</span></li>
          <li><span class="sym">EUR/USD <span class="sub">Major</span></span><span class="chg chg--up">+0.12%</span></li>
          <li><span class="sym">USD/CAD <span class="sub">Major</span></span><span class="chg chg--up">+0.06%</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

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

<section id="market-news">
  <div class="wrap">
    <div class="section__head">
      <div>
        <h2><?php globalfxhub_te( 'sec_news_h2' ); ?></h2>
        <p><?php globalfxhub_te( 'sec_news_p' ); ?></p>
      </div>
      <a href="#" class="section__link"><?php globalfxhub_te( 'link_all_news' ); ?></a>
    </div>
    <div class="news__grid">
      <a href="#" class="news-item">
        <div class="tag">BROKER NEWS</div>
        <h4>Meridian FX expands regulated entities into two new markets</h4>
        <p>The move adds oversight in two additional jurisdictions, extending client protections to more regions.</p>
        <time>March 14, 2026</time>
      </a>
      <a href="#" class="news-item">
        <div class="tag market">MARKET NEWS</div>
        <h4>Gold holds near multi-month highs as rate-cut bets firm up</h4>
        <p>XAU/USD extended gains for a third session as traders priced in a more dovish path for interest rates.</p>
        <time>March 13, 2026</time>
      </a>
      <a href="#" class="news-item">
        <div class="tag">BROKER NEWS</div>
        <h4>Northbridge Markets cuts minimum deposit to $50</h4>
        <p>The change lowers the barrier to entry on its standard account tier, aimed at newer traders.</p>
        <time>March 12, 2026</time>
      </a>
      <a href="#" class="news-item">
        <div class="tag market">MARKET NEWS</div>
        <h4>Oil slips as inventory data surprises to the upside</h4>
        <p>WTI crude fell after weekly stockpile figures came in well above analyst expectations.</p>
        <time>March 11, 2026</time>
      </a>
      <a href="#" class="news-item">
        <div class="tag">BROKER NEWS</div>
        <h4>Almanac FX launches a redesigned education hub</h4>
        <p>The broker's new learning center adds structured courses aimed at first-time traders.</p>
        <time>March 10, 2026</time>
      </a>
      <a href="#" class="news-item">
        <div class="tag market">MARKET NEWS</div>
        <h4>Dollar steadies ahead of upcoming inflation data</h4>
        <p>Major currency pairs traded in tight ranges as markets awaited the week's key data release.</p>
        <time>March 10, 2026</time>
      </a>
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
      <div class="rank-row">
        <span class="rank-num">01</span>
        <span class="rank-broker">
          <span class="rank-broker__name">IG</span>
          <span class="rank-broker__tag">CySEC 309/16 · est. 1974</span>
        </span>
        <span class="rank-detail col-fees">0.6 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">Proprietary, MT4, ProRealTime</span>
        <span class="rank-score">4.13 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/ig/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=ig' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">02</span>
        <span class="rank-broker">
          <span class="rank-broker__name">AvaTrade</span>
          <span class="rank-broker__tag">EU-regulated (MiFID passporting) · est. 2006</span>
        </span>
        <span class="rank-detail col-fees">0.93 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">Proprietary (AvaTradeGO), MT4, MT5…</span>
        <span class="rank-score">4.12 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/avatrade/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=avatrade' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">03</span>
        <span class="rank-broker">
          <span class="rank-broker__name">FOREX.com</span>
          <span class="rank-broker__tag">CySEC 400/21 · est. 1999</span>
        </span>
        <span class="rank-detail col-fees">1.0 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">Proprietary, MT4, MT5…</span>
        <span class="rank-score">3.83 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/forex-com/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=forex-com' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">04</span>
        <span class="rank-broker">
          <span class="rank-broker__name">FXCM</span>
          <span class="rank-broker__tag">CySEC 392/20 · est. 1999</span>
        </span>
        <span class="rank-detail col-fees">1.3 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">Trading Station (proprietary), MT4, ZuluTrade…</span>
        <span class="rank-score">3.79 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/fxcm/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=fxcm' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">05</span>
        <span class="rank-broker">
          <span class="rank-broker__name">Plus500</span>
          <span class="rank-broker__tag">CySEC 250/14 · est. 2008</span>
        </span>
        <span class="rank-detail col-fees">1.3 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">WebTrader (proprietary)</span>
        <span class="rank-score">3.65 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/plus500/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=plus500' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">06</span>
        <span class="rank-broker">
          <span class="rank-broker__name">Pepperstone</span>
          <span class="rank-broker__tag">CySEC 388/20 · est. 2010</span>
        </span>
        <span class="rank-detail col-fees">1.1 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">MT4, MT5, cTrader…</span>
        <span class="rank-score">3.64 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/pepperstone/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=pepperstone' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">07</span>
        <span class="rank-broker">
          <span class="rank-broker__name">Eightcap</span>
          <span class="rank-broker__tag">CySEC 246/14 · est. 2009</span>
        </span>
        <span class="rank-detail col-fees">1.0 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">MT4, MT5, TradingView</span>
        <span class="rank-score">3.5 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/eightcap/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=eightcap' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">08</span>
        <span class="rank-broker">
          <span class="rank-broker__name">FxPro</span>
          <span class="rank-broker__tag">CySEC 078/07 · est. 2006</span>
        </span>
        <span class="rank-detail col-fees">1.44 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">MT4, MT5, cTrader</span>
        <span class="rank-score">3.5 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/fxpro/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=fxpro' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">09</span>
        <span class="rank-broker">
          <span class="rank-broker__name">Capital.com</span>
          <span class="rank-broker__tag">CySEC 319/17 · est. 2016</span>
        </span>
        <span class="rank-detail col-fees">0.6 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">Proprietary, MT4, TradingView</span>
        <span class="rank-score">3.44 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/capital-com/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=capital-com' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">10</span>
        <span class="rank-broker">
          <span class="rank-broker__name">XM</span>
          <span class="rank-broker__tag">CySEC 120/10 · est. 2009</span>
        </span>
        <span class="rank-detail col-fees">1.1 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">MT4, MT5</span>
        <span class="rank-score">3.34 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/xm/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=xm' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">11</span>
        <span class="rank-broker">
          <span class="rank-broker__name">eToro</span>
          <span class="rank-broker__tag">CySEC 109/10 · est. 2007</span>
        </span>
        <span class="rank-detail col-fees">1.0 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">Proprietary (CopyTrader)</span>
        <span class="rank-score">3.22 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/etoro/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=etoro' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">12</span>
        <span class="rank-broker">
          <span class="rank-broker__name">XTB</span>
          <span class="rank-broker__tag">CySEC 169/12 · est. 2002</span>
        </span>
        <span class="rank-detail col-fees">0.7 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">xStation 5 (proprietary)</span>
        <span class="rank-score">3.1 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/xtb/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=xtb' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">13</span>
        <span class="rank-broker">
          <span class="rank-broker__name">IC Markets</span>
          <span class="rank-broker__tag">CySEC 362/18 · est. 2007</span>
        </span>
        <span class="rank-detail col-fees">0.8 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">MT4, MT5, cTrader</span>
        <span class="rank-score">3.07 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/ic-markets/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=ic-markets' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">14</span>
        <span class="rank-broker">
          <span class="rank-broker__name">Tickmill</span>
          <span class="rank-broker__tag">CySEC 278/15 · est. 2014</span>
        </span>
        <span class="rank-detail col-fees">1.7 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">MT4, MT5</span>
        <span class="rank-score">2.94 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/tickmill/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=tickmill' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
      <div class="rank-row">
        <span class="rank-num">15</span>
        <span class="rank-broker">
          <span class="rank-broker__name">Trading 212</span>
          <span class="rank-broker__tag">CySEC 398/21 · est. 2004</span>
        </span>
        <span class="rank-detail col-fees">2.7 pips (EUR/USD)</span>
        <span class="rank-detail col-plat">Proprietary</span>
        <span class="rank-score">2.59 / 5</span>
        <span class="rank-ctas">
          <a href="<?php echo esc_url( home_url( '/reviews/trading-212/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
          <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=trading-212' ); ?>" class="rank-cta"><?php globalfxhub_te( 'nav_compare' ); ?></a>
        </span>
      </div>
    </div>
    <p style="font-size:12.5px;color:var(--ink-soft);margin-top:14px;">Figures shown are standard-account averages compiled from broker disclosures and third-party research as of March 2026. Spreads, minimum deposits and licence status change — verify current terms directly with the broker and on the <a href="https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/" target="_blank" rel="noopener" style="color:var(--teal);">CySEC public register</a> before making a decision.</p>
  </div>
</section>

<section>
  <div class="wrap">
    <div class="award">
      <div class="award__badge">2026<b>#1</b>TOP-SCORED CYSEC BROKER</div>
      <div>
        <h2>IG tops our CySEC broker list for 2026</h2>
        <p>IG scores highest in our editorial ranking (4.7/5), based on regulatory breadth, cost, platform variety, and years in operation. Long-established, publicly listed (LSE: IGG), one of the widest regulatory footprints of any broker on this list.</p>
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
        <div class="num">30%</div>
        <h3><?php globalfxhub_te( 'method2_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method2_p' ); ?></p>
      </div>
      <div class="method__cell">
        <div class="num">20%</div>
        <h3><?php globalfxhub_te( 'method3_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method3_p' ); ?></p>
      </div>
      <div class="method__cell">
        <div class="num">20%</div>
        <h3><?php globalfxhub_te( 'method4_h3' ); ?></h3>
        <p><?php globalfxhub_te( 'method4_p' ); ?></p>
      </div>
    </div>
    <p style="font-size:13px;color:#aab6c6;margin-top:24px;max-width:70ch;">Every factor is ranked relative to the other 14 brokers in this list, not against an absolute external benchmark. Source data comes from CySEC's public register, broker legal disclosures, and third-party broker research, compiled March 2026. This methodology does not involve opening or funding live accounts, and it isn't personalized financial advice — always verify current licence status, fees, and terms directly with the broker before depositing funds.</p>
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
