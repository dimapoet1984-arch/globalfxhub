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
          <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php globalfxhub_te( 'footer_about' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/how-we-test/' ) ); ?>"><?php globalfxhub_te( 'footer_how_we_test' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/why-trust-us/' ) ); ?>"><?php globalfxhub_te( 'footer_why_trust_us' ); ?></a></li>
        </ul>
        <?php endif; ?>
      </div>
      <div>
        <h4><?php globalfxhub_te( 'footer_research_heading' ); ?></h4>
        <?php if ( has_nav_menu( 'footer_research' ) ) : wp_nav_menu( array( 'theme_location' => 'footer_research', 'container' => false, 'menu_class' => '', 'items_wrap' => '<ul>%3$s</ul>' ) ); else : ?>
        <ul>
          <li><a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>"><?php globalfxhub_te( 'footer_all_reviews' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/broker-finder/' ) ); ?>"><?php globalfxhub_te( 'nav_finder' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/learn/' ) ); ?>"><?php globalfxhub_te( 'nav_learn' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/news/markets/' ) ); ?>">FX Market News</a></li>
          <li><a href="<?php echo esc_url( home_url( '/news/brokers/' ) ); ?>">Broker News</a></li>
          <li><a href="<?php echo esc_url( home_url( '/compare/' ) ); ?>"><?php globalfxhub_te( 'footer_compare_brokers' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/best/' ) ); ?>"><?php globalfxhub_te( 'nav_best' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/regulation/' ) ); ?>"><?php globalfxhub_te( 'nav_regulation' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/countries/' ) ); ?>"><?php globalfxhub_te( 'nav_countries' ); ?></a></li>
        </ul>
        <?php endif; ?>
      </div>
      <div>
        <h4><?php globalfxhub_te( 'footer_legal_heading' ); ?></h4>
        <?php if ( has_nav_menu( 'footer_legal' ) ) : wp_nav_menu( array( 'theme_location' => 'footer_legal', 'container' => false, 'menu_class' => '', 'items_wrap' => '<ul>%3$s</ul>' ) ); else : ?>
        <ul>
          <li><a href="<?php echo esc_url( home_url( '/advertiser-disclosure/' ) ); ?>"><?php globalfxhub_te( 'footer_advertiser_disclosure' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php globalfxhub_te( 'footer_terms' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php globalfxhub_te( 'footer_privacy' ); ?></a></li>
          <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php globalfxhub_te( 'footer_contact' ); ?></a></li>
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
