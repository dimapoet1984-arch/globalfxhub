<div class="wrap" style="padding:56px 0;">
  <div class="section__head">
    <div>
      <h2>Blog</h2>
      <p>Plain-English guides to how the forex and commodities markets work.</p>
    </div>
  </div>

  <div class="guides__grid">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
        $cats     = get_the_category();
        $cat_name = ! empty( $cats ) ? strtoupper( $cats[0]->name ) : 'ARTICLE';
    ?>
    <a href="<?php the_permalink(); ?>" class="guide">
      <div class="guide__time"><?php echo esc_html( $cat_name ); ?> · <?php echo esc_html( globalfxhub_reading_time( get_the_content() ) ); ?> MIN READ</div>
      <h3><?php the_title(); ?></h3>
      <p><?php echo esc_html( globalfxhub_trim_excerpt( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 20 ) ); ?></p>
    </a>
    <?php endwhile; else : ?>
    <p style="padding:24px;color:var(--ink-soft);">No articles published yet.</p>
    <?php endif; ?>
  </div>

  <?php if ( have_posts() ) : ?>
  <div style="margin-top:32px;">
    <?php the_posts_pagination(); ?>
  </div>
  <?php endif; ?>
</div>
