<?php
/**
 * Template Name: GlobalFXHub Data
 * Description: /data/ -- a landing page for the research behind this
 * site: the broker dataset, regulator rosters, original reports,
 * comparisons, and the tools built on top of them. Every number here
 * is computed live from the same functions every other page on this
 * site calls (globalfxhub_get_brokers(), globalfxhub_get_regulator_entities(),
 * etc.) -- never a count frozen at publish time, so this page can't
 * quietly drift out of date the way a hand-written stat could.
 */
get_header();

$data_brokers      = globalfxhub_get_brokers();
$data_broker_count = count( $data_brokers );
$data_regulators   = globalfxhub_get_regulator_entities();
$data_regulator_count = count( $data_regulators );
$data_licence_holdings = 0;
foreach ( array_keys( $data_regulators ) as $data_reg_slug ) {
    $data_licence_holdings += globalfxhub_regulator_entity_stats( $data_reg_slug )['held'];
}
$data_country_count     = count( globalfxhub_get_all_countries() );
$data_report_count      = count( globalfxhub_get_research_reports() );
$data_reg_comparison_count = count( globalfxhub_get_regulation_comparisons() );
$data_broker_comparison_count = count( globalfxhub_get_editorial_comparisons() );
$data_best_list_count   = count( globalfxhub_get_best_lists() );
$data_vs_bound          = count( globalfxhub_top_ranked_broker_slugs( 40 ) );
$data_vs_pair_count     = (int) ( $data_vs_bound * ( $data_vs_bound - 1 ) / 2 );

$data_plural = function( $count, $singular, $plural = null ) {
    return $count . ' ' . ( 1 === $count ? $singular : ( $plural ?? $singular . 's' ) );
};
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow">DATA</div>
  <h1><?php globalfxhub_te( 'ptitle_data' ); ?></h1>
  <p>GlobalFXHub is a research operation that happens to publish a website. Every broker review, ranking, comparison, and tool on this site is a different view of the same underlying dataset -- this page is where that dataset itself is the subject. Every figure below is computed live when this page loads, from the same functions every other page on this site calls, not a number written once and left to go stale.</p>
</div>

<div class="trustbar">
  <div class="wrap trustbar__grid">
    <div>
      <div class="trustbar__num"><?php echo esc_html( $data_broker_count ); ?></div>
      <div class="trustbar__label">Brokers researched</div>
    </div>
    <div>
      <div class="trustbar__num"><?php echo esc_html( $data_regulator_count ); ?></div>
      <div class="trustbar__label">Regulators tracked</div>
    </div>
    <div>
      <div class="trustbar__num"><?php echo esc_html( $data_licence_holdings ); ?></div>
      <div class="trustbar__label">Confirmed regulatory licence holdings</div>
    </div>
    <div>
      <div class="trustbar__num"><?php echo esc_html( $data_country_count ); ?></div>
      <div class="trustbar__label">Countries covered</div>
    </div>
  </div>
</div>

<div class="wrap art-body" style="padding-top:36px;padding-bottom:10px;">

  <h2>The broker dataset</h2>
  <p><?php echo esc_html( $data_broker_count ); ?> brokers, each scored against the same disclosed nine-category rubric -- regulation, cost, non-trading fees, platforms, execution, product range, deposits/withdrawals, transparency, and track record -- so a score means the same thing on every page it appears on. Where we couldn't independently confirm a field, we say so rather than guess; that's reflected directly in the <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);">scoring methodology</a> and in the confidence rating shown next to individual facts on <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" style="color:var(--teal);">broker review pages</a>.</p>
  <p>The same dataset also drives <?php echo esc_html( $data_best_list_count ); ?> curated <a href="<?php echo esc_url( home_url( '/best/' ) ); ?>" style="color:var(--teal);">best-broker lists</a> and <?php echo esc_html( $data_broker_comparison_count ); ?> hand-written <a href="<?php echo esc_url( home_url( '/compare/' ) ); ?>" style="color:var(--teal);">broker-vs-broker comparisons</a>. The interactive compare tool itself can match up any two of the <?php echo esc_html( $data_broker_count ); ?> researched brokers on demand; <?php echo esc_html( $data_vs_pair_count ); ?> of those pairings -- every combination among our top <?php echo esc_html( $data_vs_bound ); ?> by rank -- also get their own indexed, linkable page rather than existing only inside the tool.</p>

  <h2>Regulator data</h2>
  <p><?php echo esc_html( $data_regulator_count ); ?> regulators -- CySEC, FCA, Seychelles FSA, ASIC, FSCA, DFSA, New Zealand's FMA, Singapore's MAS, and Kenya's CMA -- each with its own <a href="<?php echo esc_url( home_url( '/regulators/' ) ); ?>" style="color:var(--teal);">entity page</a>: jurisdiction, leverage rules, compensation scheme (or the honest absence of one), complaint process, the official register to check a firm against, and a live roster of which of our researched brokers currently hold that licence. Summed across all nine, our researched brokers hold <?php echo esc_html( $data_licence_holdings ); ?> confirmed regulatory licences between them -- a broker holding three regulators' licences counts three times in that total, since the point is to show the real footprint, not a brokers-vs-regulators ratio. Where two regulators are commonly confused with each other -- a broker's legal entity, a Tier-1-vs-offshore distinction, a specific compensation cap -- our <a href="<?php echo esc_url( home_url( '/regulation/' ) ); ?>" style="color:var(--teal);">regulation knowledge base</a> covers <?php echo esc_html( $data_plural( $data_reg_comparison_count, 'head-to-head regulator comparison' ) ); ?> directly.</p>

  <h2>Original research</h2>
  <p><?php echo esc_html( $data_plural( $data_report_count, 'original data report' ) ); ?> so far, each built entirely from this site's own 139-broker dataset -- not a third-party survey, not a press release, a direct count of something already in our research. Every report states its own methodology up front: sample size, the date the underlying data was last reviewed, how we define a "confirmed" fact, how missing data is handled, and the source hierarchy used to confirm each figure. See the <a href="<?php echo esc_url( home_url( '/research/' ) ); ?>" style="color:var(--teal);">full research index</a>. This is a deliberately small, growing library -- a handful of excellent, citable reports, not a mechanically generated one for every possible cross-section of the dataset.</p>

  <h2>Data confidence</h2>
  <p>Not every fact in this dataset is equally well-sourced, and we don't present them as if they were. Individual facts on broker review pages carry a HIGH, MEDIUM, or LOW confidence badge computed from the same hedge language our own research notes already use -- "confirmed via the broker's own published page" rates differently from "sources disagree" or "not independently confirmed." A fact with no such signal either way is left unrated rather than assigned a guessed middle tier. See any <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" style="color:var(--teal);">broker review</a>'s deep-dive section for this in practice.</p>

  <h2>Tools built on this data</h2>
  <p>Three interactive tools run directly against the same dataset rather than a separate copy of it: the <a href="<?php echo esc_url( home_url( '/broker-finder/' ) ); ?>" style="color:var(--teal);">Broker Finder</a> (filters our full researched set down to brokers matching your own answers), the <a href="<?php echo esc_url( home_url( '/cost-calculator/' ) ); ?>" style="color:var(--teal);">Cost Calculator</a> (estimates your own annual spread cost at every broker with a confirmed spread), and the <a href="<?php echo esc_url( home_url( '/regulation-checker/' ) ); ?>" style="color:var(--teal);">Regulation Checker</a> (the full regulation dossier for any one researched broker). None of the three hold their own separate data -- a change to a broker's record updates all three automatically.</p>

  <h2>Keeping this current</h2>
  <p>Data changes; we try not to let the site quietly fall out of step with it. The <a href="<?php echo esc_url( home_url( '/broker-changelog/' ) ); ?>" style="color:var(--teal);">Broker Change Log</a> is a dated, broker-by-broker record of when a published fact actually changed -- a spread update, a new licence number, a platform added or dropped -- not a marketing announcement. Material errors we catch after publishing are logged, not quietly edited away, at <a href="<?php echo esc_url( home_url( '/corrections/' ) ); ?>" style="color:var(--teal);">Corrections</a>. Our full sourcing, AI-use, and scoring policy is at <a href="<?php echo esc_url( home_url( '/editorial-policy/' ) ); ?>" style="color:var(--teal);">Editorial Policy</a>.</p>

</div>

<?php get_footer(); ?>
