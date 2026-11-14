<?php
/**
 * Template Name: Regulators
 * Description: /regulators/ index, and /regulators/{slug}/ for each
 * individual regulator -- a live roster of which of our researched
 * brokers hold that licence, plus the regulator's own key facts. See
 * inc/regulator-entities.php for the data; this is the structured
 * data companion to the prose explainer at /regulation/{slug}/
 * (inc/regulation.php), cross-linked both ways rather than merged
 * into one page.
 */
get_header();

$regulator_slug = get_query_var( 'regulator' );
$entities = globalfxhub_get_regulator_entities();
$entity = ( $regulator_slug && isset( $entities[ $regulator_slug ] ) ) ? $entities[ $regulator_slug ] : null;
if ( $regulator_slug && ! $entity ) {
    status_header( 404 );
}
?>

<?php if ( $regulator_slug ) :
    if ( ! $entity ) :
?>

<div class="wrap page-head">
  <div class="eyebrow">REGULATORS</div>
  <h1>Regulator not available</h1>
  <p>We don't cover that regulator as a structured entity page yet. <a href="<?php echo esc_url( home_url( '/regulators/' ) ); ?>" style="color:var(--teal);">See all regulators</a>.</p>
</div>

    <?php else :
        $stats = globalfxhub_regulator_entity_stats( $regulator_slug );
        $brokers = globalfxhub_regulator_entity_brokers( $regulator_slug );
        $reg_pages = globalfxhub_get_regulation_pages();
    ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/regulators/' ) ); ?>">Regulators</a> /
  <?php echo esc_html( $entity['name'] ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow">REGULATOR</div>
  <h1><?php echo esc_html( $entity['name'] ); ?> &mdash; Regulated Brokers &amp; Licence Register</h1>
  <p><?php echo esc_html( $entity['full_name'] ); ?>. <?php echo esc_html( $stats['held'] ); ?> of the <?php echo esc_html( $stats['total'] ); ?> brokers in our researched set (<?php echo esc_html( $stats['percent'] ); ?>%) currently hold a licence from this regulator, computed live from the same dataset behind every review and ranking on this site.</p>
</div>

<div class="wrap art-body" style="padding-bottom:10px;">
  <table class="compare-table review-facts">
    <tbody>
      <tr><th>Jurisdiction</th><td><?php echo esc_html( $entity['jurisdiction'] ); ?></td></tr>
      <tr><th>Leverage rules</th><td><?php echo esc_html( $entity['leverage_note'] ); ?></td></tr>
      <tr><th>Investor compensation</th><td><?php echo $entity['compensation_scheme'] ? esc_html( $entity['compensation_scheme'] ) . ': ' . wp_kses( $entity['compensation_cap'], array() ) : esc_html( 'No statutory compensation scheme.' ); ?></td></tr>
      <tr><th>Complaint process</th><td><?php echo esc_html( $entity['complaint_process'] ); ?></td></tr>
      <tr><th>Official register</th><td><a href="<?php echo esc_url( $entity['register_url'] ); ?>" target="_blank" rel="noopener" style="color:var(--teal);">Search <?php echo esc_html( $entity['register_label'] ); ?> &rarr;</a></td></tr>
    </tbody>
  </table>
  <p style="color:var(--ink-soft);font-size:13.5px;margin:0 0 20px;">
    <?php if ( isset( $reg_pages[ $entity['related_regulation_slug'] ] ) ) : ?>
    See our <a href="<?php echo esc_url( home_url( '/regulation/' . $entity['related_regulation_slug'] . '/' ) ); ?>" style="color:var(--teal);">full <?php echo esc_html( $entity['name'] ); ?> explainer</a> for what this licence does and doesn't guarantee.
    <?php endif; ?>
    <?php if ( $entity['related_best_slug'] ) : ?>
    See our ranked <a href="<?php echo esc_url( home_url( '/best/' . $entity['related_best_slug'] . '/' ) ); ?>" style="color:var(--teal);">best <?php echo esc_html( $entity['name'] ); ?>-regulated brokers</a> list.
    <?php endif; ?>
  </p>
</div>

<div class="wrap" style="padding-bottom:40px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 6px;"><?php echo esc_html( $entity['name'] ); ?>-licensed brokers in our research set</h2>
  <p style="color:var(--ink-soft);font-size:14.5px;margin:0 0 20px;">Legal entity is shown only where this broker holds exactly this one of the regulators we track per-entity (CySEC, FCA, Seychelles FSA) -- a broker holding two of these has one disclosed entity name that reliably belongs to only one of them, so we don't guess which. See the broker's own review for its full multi-entity breakdown where it holds more than one.</p>
  <?php if ( empty( $brokers ) ) : ?>
  <p>No broker in our researched set currently holds this licence.</p>
  <?php else : ?>
  <div class="rankings regulator-roster">
    <div class="rank-row head">
      <span></span><span>Broker</span><span class="col-fees">Licence number</span><span></span>
    </div>
    <?php foreach ( $brokers as $i => $row ) : ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker"><span class="rank-broker__name"><?php echo esc_html( $row['name'] ); ?></span><?php if ( $row['entity'] ) : ?><span class="rank-broker__tag"><?php echo esc_html( $row['entity'] ); ?></span><?php endif; ?></span>
      <span class="rank-detail col-fees"><?php echo esc_html( $row['licence_number'] ? $row['licence_number'] : 'Not independently confirmed' ); ?></span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( $row['review_url'] ); ?>" class="rank-cta">Read review</a>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

    <?php endif; ?>

<?php else : ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow">REGULATORS</div>
  <h1><?php the_title(); ?></h1>
  <p>Which of our 139 researched brokers currently hold a licence from each regulator, with jurisdiction, compensation cover, and the official register for each -- the structured data companion to our <a href="<?php echo esc_url( home_url( '/regulation/' ) ); ?>">regulation explainers</a>.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="guides__grid">
    <?php foreach ( $entities as $slug => $entity_data ) :
        $entity_stats = globalfxhub_regulator_entity_stats( $slug );
    ?>
    <a href="<?php echo esc_url( home_url( '/regulators/' . $slug . '/' ) ); ?>" class="guide">
      <h3><?php echo esc_html( $entity_data['name'] ); ?></h3>
      <p><?php echo esc_html( $entity_data['full_name'] ); ?> &mdash; <?php echo esc_html( $entity_stats['held'] ); ?> of <?php echo esc_html( $entity_stats['total'] ); ?> researched brokers hold this licence.</p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
