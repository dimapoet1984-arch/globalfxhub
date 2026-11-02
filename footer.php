<footer>
  <div class="wrap">
    <div class="footer__grid">
      <div>
        <div class="footer__brand">Global<em>FXHub</em></div>
        <p style="max-width:32ch;color:#8695a8;"><?php globalfxhub_te( 'footer_tagline' ); ?></p>
      </div>
      <div>
        <h4><?php globalfxhub_te( 'footer_company_heading' ); ?></h4>
        <?php if ( has_nav_menu( 'footer_company' ) ) : wp_nav_menu( array( 'theme_location' => 'footer_company', 'container' => false, 'menu_class' => '', 'items_wrap' => '<ul>%3$s</ul>' ) ); else : ?>
        <ul>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/about/', 'about' ) ); ?>"><?php globalfxhub_te( 'footer_about' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/how-we-test/', 'how-we-test' ) ); ?>"><?php globalfxhub_te( 'footer_how_we_test' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/why-trust-us/', 'why-trust-us' ) ); ?>"><?php globalfxhub_te( 'footer_why_trust_us' ); ?></a></li>
        </ul>
        <?php endif; ?>
      </div>
      <div>
        <h4><?php globalfxhub_te( 'footer_research_heading' ); ?></h4>
        <?php if ( has_nav_menu( 'footer_research' ) ) : wp_nav_menu( array( 'theme_location' => 'footer_research', 'container' => false, 'menu_class' => '', 'items_wrap' => '<ul>%3$s</ul>' ) ); else : ?>
        <ul>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/reviews/', 'reviews' ) ); ?>"><?php globalfxhub_te( 'footer_all_reviews' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/broker-finder/', 'broker-finder' ) ); ?>"><?php globalfxhub_te( 'nav_finder' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/cost-calculator/', 'cost-calculator' ) ); ?>"><?php globalfxhub_te( 'nav_cost_calculator' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/regulation-checker/', 'regulation-checker' ) ); ?>"><?php globalfxhub_te( 'nav_regulation_checker' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/broker-changelog/', 'broker-changelog' ) ); ?>"><?php globalfxhub_te( 'nav_changelog' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/learn/', 'learn' ) ); ?>"><?php globalfxhub_te( 'nav_learn' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/news/markets/' ) ); ?>"><?php globalfxhub_te( 'footer_fx_market_news' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/news/brokers/' ) ); ?>"><?php globalfxhub_te( 'footer_broker_news' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/compare/', 'compare' ) ); ?>"><?php globalfxhub_te( 'footer_compare_brokers' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/best/', 'best' ) ); ?>"><?php globalfxhub_te( 'nav_best' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/regulation/', 'regulation' ) ); ?>"><?php globalfxhub_te( 'nav_regulation' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/countries/', 'countries' ) ); ?>"><?php globalfxhub_te( 'nav_countries' ); ?></a></li>
        </ul>
        <?php endif; ?>
      </div>
      <div>
        <h4><?php globalfxhub_te( 'footer_legal_heading' ); ?></h4>
        <?php if ( has_nav_menu( 'footer_legal' ) ) : wp_nav_menu( array( 'theme_location' => 'footer_legal', 'container' => false, 'menu_class' => '', 'items_wrap' => '<ul>%3$s</ul>' ) ); else : ?>
        <ul>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/advertiser-disclosure/', 'advertiser-disclosure' ) ); ?>"><?php globalfxhub_te( 'footer_advertiser_disclosure' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/terms/', 'terms' ) ); ?>"><?php globalfxhub_te( 'footer_terms' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/privacy/', 'privacy' ) ); ?>"><?php globalfxhub_te( 'footer_privacy' ); ?></a></li>
          <li><a href="<?php echo esc_url( globalfxhub_nav_url( '/contact/', 'contact' ) ); ?>"><?php globalfxhub_te( 'footer_contact' ); ?></a></li>
        </ul>
        <?php endif; ?>
      </div>
    </div>
    <div class="disclosure">
      <?php globalfxhub_te( 'footer_disclosure_full' ); ?>
    </div>
    <div class="legal">
      <span>© <?php echo date( 'Y' ); ?> GlobalFXHub</span>
      <span><?php globalfxhub_te( 'footer_legal_line' ); ?></span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
