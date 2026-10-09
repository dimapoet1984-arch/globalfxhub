<?php
/**
 * Template Name: Compare Brokers
 * Description: Interactive side-by-side broker comparison tool.
 */
get_header();
$a_param = isset($_GET['a']) ? sanitize_title($_GET['a']) : '';
$b_param = isset($_GET['b']) ? sanitize_title($_GET['b']) : '';
?>

<div class="wrap page-head">
  <div class="eyebrow">BROKER COMPARISON TOOL</div>
  <h1><?php the_title(); ?></h1>
  <p>Pick any two of our researched brokers to compare regulation, cost, platforms, and score — generated instantly from the same data behind our rankings.</p>
  <?php $tool_learn_link = globalfxhub_resolve_learn_link( globalfxhub_tool_learn_links()['compare'] ); if ( $tool_learn_link ) : ?>
  <p style="font-size:13.5px;margin-top:8px;"><a href="<?php echo esc_url( $tool_learn_link['url'] ); ?>" style="color:var(--teal);">New to this? <?php echo esc_html( $tool_learn_link['label'] ); ?> &rarr;</a></p>
  <?php endif; ?>
</div>

<div class="wrap">
  <div class="picker">
    <div>
      <label for="brokerA">Broker A</label>
      <select id="brokerA"></select>
    </div>
    <div class="vs">vs</div>
    <div>
      <label for="brokerB">Broker B</label>
      <select id="brokerB"></select>
    </div>
    <button id="compareBtn" type="button">Compare</button>
  </div>

  <div class="presets" id="presets"></div>

  <div id="empty-state">Choose two brokers above (or pick a popular comparison) to see the full side-by-side breakdown.</div>

  <div id="results">
    <table class="compare-table" id="resultsTable"></table>
    <div class="methodology-note">
      <strong>How this score is calculated:</strong> Nine weighted categories against a fixed, disclosed rubric -- regulation & client protection 30%, trading costs 20%, non-trading fees 10%, platforms & tools 10%, execution/trading conditions 10%, product range 5%, deposits/withdrawals 5%, transparency 5%, track record 5% -- not a ranking relative to other brokers, and not hands-on tested. See the <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);">full methodology</a>. Verify current terms directly with the broker and its regulator's public register before depositing funds. This is not personalized financial advice.
    </div>
  </div>
</div>

<script>
const BROKERS = <?php echo wp_json_encode( globalfxhub_get_brokers() ); ?>;
const PRESET_A = <?php echo wp_json_encode( $a_param ); ?>;
const PRESET_B = <?php echo wp_json_encode( $b_param ); ?>;

(function(){
  const selA = document.getElementById('brokerA');
  const selB = document.getElementById('brokerB');
  const btn = document.getElementById('compareBtn');
  const results = document.getElementById('results');
  const empty = document.getElementById('empty-state');
  const table = document.getElementById('resultsTable');
  const presetsEl = document.getElementById('presets');

  const bySlug = {};
  BROKERS.forEach(b => bySlug[b.slug] = b);

  const sorted = [...BROKERS].sort((a,b) => a.name.localeCompare(b.name));
  sorted.forEach(b => {
    selA.add(new Option(b.name, b.slug));
    selB.add(new Option(b.name, b.slug));
  });

  const presets = [
    ['ig','xtb'], ['etoro','plus500'], ['xm','pepperstone'],
    ['ic-markets','fxpro'], ['fxcm','capital-com'], ['avatrade','tickmill']
  ];
  presets.forEach(([a,b]) => {
    if(!bySlug[a] || !bySlug[b]) return;
    const btnEl = document.createElement('button');
    btnEl.className = 'preset';
    btnEl.type = 'button';
    btnEl.textContent = bySlug[a].name + ' vs ' + bySlug[b].name;
    btnEl.addEventListener('click', () => { selA.value = a; selB.value = b; render(); });
    presetsEl.appendChild(btnEl);
  });

  function render(){
    const a = bySlug[selA.value];
    const b = bySlug[selB.value];
    if(!a || !b || a.slug === b.slug){
      empty.textContent = a && b && a.slug === b.slug
        ? 'Choose two different brokers to compare.'
        : 'Choose two brokers above to see the full side-by-side breakdown.';
      empty.style.display = 'block';
      results.classList.remove('show');
      return;
    }
    empty.style.display = 'none';
    results.classList.add('show');

    const NA = 'Not independently confirmed';
    const orNA = (v, suffix) => (v === null || v === undefined || v === '') ? NA : (v + (suffix || ''));

    const regulationLabel = (x) => {
      const parts = [];
      if (x.cysec && x.cysec !== '—') parts.push(`CySEC No. ${x.cysec}`);
      else if (x.cysec === '—') parts.push('EU-regulated (MiFID passporting)');
      if (x.fca) parts.push(`FCA No. ${x.fca}`);
      else if (x.fca_note && x.fca === null) parts.push('FCA-licensed (FRN unconfirmed)');
      if (x.seychelles) parts.push(`FSA Seychelles No. ${x.seychelles}`);
      else if (x.seychelles_note && x.seychelles === null) parts.push('FSA Seychelles-licensed (licence number unconfirmed)');
      return parts.length ? parts.join(' + ') : 'Regulatory status not independently confirmed';
    };

    const rows = [];
    rows.push(['Overall score',
      `<span class="score-num">${a.scores.overall}</span> / 5`,
      `<span class="score-num">${b.scores.overall}</span> / 5`,
      a.scores.overall, b.scores.overall]);
    const regNote = (x) => [x.cysec_note, x.fca_note, x.seychelles_note].filter(Boolean).join(' ');
    rows.push(['Regulation',
      regulationLabel(a) + (regNote(a) ? `<div class="compare-note">${regNote(a)}</div>` : ''),
      regulationLabel(b) + (regNote(b) ? `<div class="compare-note">${regNote(b)}</div>` : '')]);
    rows.push(['Entity', a.entity, b.entity]);
    rows.push(['Founded', orNA(a.founded), orNA(b.founded), a.founded ? (2026-a.founded) : undefined, b.founded ? (2026-b.founded) : undefined]);
    rows.push(['Headquarters', orNA(a.hq), orNA(b.hq)]);
    rows.push(['Minimum deposit', orNA(a.min_deposit_display), orNA(b.min_deposit_display),
      (typeof a.min_deposit_usd === 'number') ? -a.min_deposit_usd : undefined,
      (typeof b.min_deposit_usd === 'number') ? -b.min_deposit_usd : undefined]);
    rows.push(['Avg. spread (EUR/USD)', orNA(a.spread_eurusd, ' pips'), orNA(b.spread_eurusd, ' pips'),
      (typeof a.spread_eurusd === 'number') ? -a.spread_eurusd : undefined,
      (typeof b.spread_eurusd === 'number') ? -b.spread_eurusd : undefined]);
    rows.push(['Platforms', a.platforms.length ? a.platforms.join(', ') : NA, b.platforms.length ? b.platforms.join(', ') : NA, a.platforms.length, b.platforms.length]);
    rows.push(['Other Tier-1 regulators', a.other_reg.length ? a.other_reg.join(', ') : 'None confirmed', b.other_reg.length ? b.other_reg.join(', ') : 'None confirmed', a.other_reg_count, b.other_reg_count]);
    rows.push(['Instruments', orNA(a.instruments), orNA(b.instruments)]);

    let html = `<thead><tr><th class="label-col"></th><th>${a.name}</th><th>${b.name}</th></tr></thead><tbody>`;
    rows.forEach(([label, valA, valB, numA, numB]) => {
      let clsA = '', clsB = '';
      if(numA != null && numB != null && numA !== numB){
        if(numA > numB) clsA = ' class="better"'; else clsB = ' class="better"';
      }
      html += `<tr><th>${label}</th><td${clsA}>${valA}</td><td${clsB}>${valB}</td></tr>`;
    });

    const subLabels = [
      ['regulation','Regulation & client protection'],
      ['cost','Trading costs'],
      ['non_trading_fees','Non-trading fees'],
      ['platforms','Platforms & tools'],
      ['execution','Execution / trading conditions'],
      ['product_range','Product range'],
      ['deposits_withdrawals','Deposits & withdrawals'],
      ['transparency','Transparency'],
      ['track_record','Track record'],
    ];
    const bar = (scores, key, label) => {
      if(scores[key] === null || scores[key] === undefined){
        return `<div class="subscore-row">${label}<span class="review-scorebox__na">Not enough confirmed data to score</span></div>`;
      }
      return `<div class="subscore-row">${label}<div class="bar-track"><div class="bar-fill" style="width:${scores[key]/5*100}%;"></div></div></div>`;
    };
    let barsA = '', barsB = '';
    subLabels.forEach(([key, label]) => {
      barsA += bar(a.scores, key, label);
      barsB += bar(b.scores, key, label);
    });
    html += `<tr><th>Score breakdown</th><td>${barsA}</td><td>${barsB}</td></tr>`;
    html += `<tr><th>Visit</th>
      <td><a href="#" class="btn btn--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">Visit ${a.name}</a></td>
      <td><a href="#" class="btn btn--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">Visit ${b.name}</a></td>
    </tr>`;
    html += '</tbody>';
    table.innerHTML = html;

    const params = new URLSearchParams(window.location.search);
    params.set('a', a.slug); params.set('b', b.slug);
    history.replaceState(null, '', '?' + params.toString());
  }

  btn.addEventListener('click', render);

  let pa = PRESET_A, pb = PRESET_B;
  if(!pa){
    const params = new URLSearchParams(window.location.search);
    pa = params.get('a') || ''; pb = params.get('b') || '';
  }
  if(pa && bySlug[pa]) selA.value = pa;
  if(pb && bySlug[pb]) selB.value = pb;
  if(pa && bySlug[pa] && !pb){
    const other = sorted.find(x => x.slug !== pa);
    if(other) selB.value = other.slug;
  }
  if(pa && bySlug[pa]) render();
})();
</script>

<?php get_footer(); ?>
