<?php
/**
 * Template Name: Countries Index
 * Description: "Best broker in [country]" pages for all 27 EU member
 * states at /countries/{slug}/, and a country index at /countries/.
 */
get_header();

$country_slug = get_query_var( 'country' );
if ( ! $country_slug && isset( $_GET['country'] ) ) {
    $country_slug = sanitize_title( wp_unslash( $_GET['country'] ) );
}
$country = $country_slug ? globalfxhub_get_country_by_slug( $country_slug ) : null;
?>

<?php if ( $country ) :
    $ranked = globalfxhub_country_broker_ranking( $country, 10 );
    $has_local = ! empty( array_filter( $ranked, function( $b ) { return ! empty( $b['is_local_hq'] ); } ) );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/countries/' ) ); ?>">Countries</a> /
  <?php echo esc_html( $country['name'] ); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BEST BROKER IN <?php echo esc_html( strtoupper( $country['name'] ) ); ?></div>
  <h1>Best Forex &amp; CFD Brokers in <?php echo esc_html( $country['name'] ); ?></h1>
  <p>Top CySEC-regulated brokers available to retail traders in <?php echo esc_html( $country['name'] ); ?>, ranked by our disclosed methodology<?php echo $has_local ? ' -- with brokers actually headquartered here called out below' : ''; ?>.</p>
</div>

<div class="wrap" style="padding-bottom:10px;">
  <?php foreach ( $country['paragraphs'] as $p ) : ?>
  <p style="max-width:74ch;color:var(--ink-soft);line-height:1.7;margin:0 0 16px;"><?php echo esc_html( $p ); ?></p>
  <?php endforeach; ?>
  <p style="max-width:74ch;color:var(--ink-soft);line-height:1.7;margin:0 0 16px;font-size:14px;">
    <strong>Currency:</strong> <?php echo esc_html( $country['currency'] ); ?> &middot;
    <strong>Regulator:</strong> <?php echo esc_html( $country['regulator'] ); ?> &middot;
    <strong>EU member since:</strong> <?php echo esc_html( $country['eu_since'] ); ?>
  </p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="rankings">
    <div class="rank-row head">
      <span></span><span>Broker</span><span class="col-fees">Avg. spread</span><span class="col-plat">Platforms</span><span>Score</span><span></span>
    </div>
    <?php foreach ( $ranked as $i => $b ) :
        $tag = '—' === $b['cysec'] ? 'EU-regulated (MiFID passporting)' : 'CySEC ' . $b['cysec'];
        if ( $b['founded'] ) {
            $tag .= ' &middot; est. ' . $b['founded'];
        }
        if ( ! empty( $b['is_local_hq'] ) ) {
            $tag .= ' &middot; <strong style="color:var(--teal);">Headquartered in ' . esc_html( $country['name'] ) . '</strong>';
        }
    ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker">
        <span class="rank-broker__name"><?php echo esc_html( $b['name'] ); ?></span>
        <span class="rank-broker__tag"><?php echo wp_kses( $tag, array( 'strong' => array( 'style' => array() ) ) ); ?></span>
      </span>
      <span class="rank-detail col-fees"><?php echo null !== $b['spread_eurusd'] ? esc_html( $b['spread_eurusd'] ) . ' pips (EUR/USD)' : 'Not confirmed'; ?></span>
      <span class="rank-detail col-plat"><?php echo esc_html( $b['platforms'] ? implode( ', ', $b['platforms'] ) : 'Not confirmed' ); ?></span>
      <span class="rank-score"><?php echo esc_html( $b['scores']['overall'] ); ?> / 5</span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( home_url( '/reviews/' . $b['slug'] . '/' ) ); ?>" class="rank-cta">Read review</a>
        <a href="#" class="rank-cta rank-cta--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">Visit Broker</a>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:18px;max-width:74ch;">
    Ranked using our <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);">disclosed overall broker score</a> (regulation, cost, platforms, track record) -- every broker here is CySEC-licensed and legally entitled to serve <?php echo esc_html( $country['name'] ); ?> under EU passporting. We don't have independent country-level trading-volume or market-share data, so this reflects broker quality and, where applicable, a confirmed local headquarters -- not measured local popularity. See our <a href="<?php echo esc_url( home_url( '/compare/' ) ); ?>" style="color:var(--teal);">comparison tool</a> to compare any two directly, or the <a href="<?php echo esc_url( home_url( '/guides/understanding-leverage-in-forex-trading/' ) ); ?>" style="color:var(--teal);">leverage guide</a> for what the EU-wide ESMA rules mean for your account.
  </p>
</div>

<?php else :
    $countries = globalfxhub_get_countries();
    ksort( $countries );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BY COUNTRY</div>
  <h1><?php the_title(); ?></h1>
  <p>Every broker we review is CySEC-licensed and, under EU passporting, legally available across all 27 EU member states. Pick a country for its top-ranked brokers plus what's actually different there -- currency, regulator, and any broker genuinely headquartered locally.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="guides__grid">
    <?php foreach ( $countries as $slug => $c ) : ?>
    <a href="<?php echo esc_url( home_url( '/countries/' . $slug . '/' ) ); ?>" class="guide">
      <div class="guide__time"><?php echo esc_html( $c['currency'] ); ?> &middot; EU since <?php echo esc_html( $c['eu_since'] ); ?></div>
      <h3>Best Broker in <?php echo esc_html( $c['name'] ); ?></h3>
      <p><?php echo esc_html( $c['regulator'] ); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
