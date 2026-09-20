<footer>
  <div class="wrap">
    <?php if ( is_front_page() ) : ?>
    <div class="footer__grid">
      <div>
        <div class="footer__brand">Global<em>FXHub</em></div>
        <p style="max-width:32ch;color:#8695a8;">Independent forex and CFD broker research. We test with real accounts and real money.</p>
      </div>
      <div>
        <h4>Company</h4>
        <?php if ( has_nav_menu( 'footer_company' ) ) : wp_nav_menu( array( 'theme_location' => 'footer_company', 'container' => false, 'menu_class' => '', 'items_wrap' => '<ul>%3$s</ul>' ) ); else : ?>
        <ul>
          <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
          <li><a href="<?php echo esc_url( home_url( '/how-we-test/' ) ); ?>">How we test</a></li>
          <li><a href="<?php echo esc_url( home_url( '/why-trust-us/' ) ); ?>">Why trust us</a></li>
        </ul>
        <?php endif; ?>
      </div>
      <div>
        <h4>Research</h4>
        <?php if ( has_nav_menu( 'footer_research' ) ) : wp_nav_menu( array( 'theme_location' => 'footer_research', 'container' => false, 'menu_class' => '', 'items_wrap' => '<ul>%3$s</ul>' ) ); else : ?>
        <ul>
          <li><a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>">All reviews</a></li>
          <li><a href="<?php $bp = get_option('page_for_posts'); echo esc_url( $bp ? get_permalink( $bp ) : home_url('/blog/') ); ?>">Blog</a></li>
          <li><a href="<?php echo esc_url( home_url( '/compare/' ) ); ?>">Compare brokers</a></li>
          <li><a href="<?php echo esc_url( home_url( '/countries/' ) ); ?>">Countries</a></li>
        </ul>
        <?php endif; ?>
      </div>
      <div>
        <h4>Legal</h4>
        <?php if ( has_nav_menu( 'footer_legal' ) ) : wp_nav_menu( array( 'theme_location' => 'footer_legal', 'container' => false, 'menu_class' => '', 'items_wrap' => '<ul>%3$s</ul>' ) ); else : ?>
        <ul>
          <li><a href="<?php echo esc_url( home_url( '/advertiser-disclosure/' ) ); ?>">Advertiser disclosure</a></li>
          <li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>">Terms of use</a></li>
          <li><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>">Privacy policy</a></li>
          <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
        </ul>
        <?php endif; ?>
      </div>
    </div>
    <div class="disclosure">
      <strong>Advertiser disclosure &amp; risk warning:</strong> Broker names, CySEC licence numbers, and trading conditions referenced on this site describe real, independently operating companies, compiled from public regulatory registers and broker disclosures as of March 2026 — figures change, and should be verified directly with the broker and on the CySEC register before you rely on them. Scores are calculated using a disclosed methodology, not hands-on account testing. GlobalFXHub does not currently have referral or advertising relationships with any broker listed; if that changes, this section will disclose it. CFDs are complex instruments carrying a high risk of losing money rapidly due to leverage — most retail investor accounts lose money trading CFDs. This is not financial advice.
    </div>
    <?php endif; ?>
    <div class="legal">
      <span>© <?php echo date( 'Y' ); ?> GlobalFXHub</span>
      <span>Independent forex &amp; CFD broker research</span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
