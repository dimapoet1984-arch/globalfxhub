<?php
/**
 * Broker-specific "deep dive" research articles -- fees and (later)
 * regulation breakdowns for ONE named broker, grounded entirely in
 * that broker's own confirmed fields in globalfxhub_get_brokers() plus
 * cross-broker stats computed from the same dataset. Distinct from:
 *  - the broker's own /reviews/{slug}/ page, which already surfaces
 *    the raw fee fields (account_types, overnight_financing, etc.) as
 *    a plain fact table -- this synthesizes them into worked
 *    explanations, a cross-broker cost comparison, and FAQs, the way
 *    CLAUDE.md's "broker-specific research (fees/regulation
 *    deep-dives)" content bucket calls for, rather than repeating the
 *    same table under a new URL.
 *  - inc/broker-comparison-editorial.php's two-broker comparisons,
 *    which overlay onto the existing /compare/{a}-vs-{b}/ page; a
 *    single-broker deep-dive has no existing page to overlay onto, so
 *    it gets its own URL at /reviews/{slug}/fees/ (and, once drafted,
 *    /reviews/{slug}/regulation/) -- nested under the review's own URL
 *    space rather than a new top-level section, same convention as
 *    the existing /reviews/{slug}/alternatives/ pages
 *    (inc/broker-alternatives.php).
 *
 * Cross-broker stats quoted in the prose below (cost-score rank,
 * minimum-deposit percentile, platform-breadth count) are frozen,
 * hand-verified numbers computed once against the live dataset at
 * authoring time -- same precedent as every other hand-curated
 * editorial piece on this site (e.g. broker-comparison-editorial.php),
 * not a live recompute on every page load. Re-verify them whenever
 * this article is revisited for a data refresh.
 *
 * Added one broker/kind at a time as the editorial calendar reaches
 * it, same precedent as broker-comparison-editorial.php -- never a
 * template mechanically applied to all 139 brokers.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_research_rewrite_rules() {
    add_rewrite_rule( '^reviews/([^/]+)/fees/?$', 'index.php?pagename=reviews&broker_research=$matches[1]&broker_research_kind=fees', 'top' );
    add_rewrite_rule( '^reviews/([^/]+)/regulation/?$', 'index.php?pagename=reviews&broker_research=$matches[1]&broker_research_kind=regulation', 'top' );
}
add_action( 'init', 'globalfxhub_research_rewrite_rules' );

function globalfxhub_research_query_vars( $vars ) {
    $vars[] = 'broker_research';
    $vars[] = 'broker_research_kind';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_research_query_vars' );

/**
 * English content, keyed "{broker_slug}-{kind}". Flattened the same
 * way inc/broker-comparison-editorial.php flattens its content for
 * the real translate+review pipeline (i18n/translate_broker_research.py):
 * section_N_heading/section_N_body, faq_N_q/faq_N_a.
 */
function globalfxhub_get_broker_research_articles() {
    return array(
        'pepperstone-fees' => array(
            'broker_slug'      => 'pepperstone',
            'kind'             => 'fees',
            'title'            => 'Pepperstone Fees Explained',
            'meta_title'       => 'Pepperstone Fees Explained (2026): Spreads, Commissions, Swap and Hidden Costs',
            'meta_description' => 'A full breakdown of every fee Pepperstone can charge -- spreads, Razor commission, swap, inactivity and currency conversion -- grounded in our own researched broker dataset, not marketing claims.',
            'intro'            => 'Pepperstone markets "raw spreads from 0.0 pips," but that\'s only one line item in what actually determines your cost to trade with it. This breaks down every fee Pepperstone can charge -- spread, commission, overnight financing, inactivity, currency conversion, and deposits/withdrawals -- using the same researched data we score it against the other 138 brokers in our dataset, not Pepperstone\'s own promotional framing. Every figure below is for Pepperstone EU Limited, the CySEC- and FCA-regulated entity reviewed on this site; a different Pepperstone entity elsewhere in the world may set different terms.',
            'sections' => array(
                array(
                    'heading' => 'Spreads and commissions: Standard vs Razor',
                    'body'    => 'Pepperstone\'s EU entity offers three account types -- Standard, Razor, and Professional (eligibility-gated, with higher leverage) -- plus an Islamic/swap-free option on both Standard and Razor. Standard charges no separate commission: spreads start from around 1.0 pip, and our own on-file headline EUR/USD spread for the account is 1.1 pips. Razor instead charges a raw spread from 0.0 pips plus a round-turn commission of roughly $6-7 per lot, which varies by the platform you trade on. Execution is no-dealing-desk on both tiers -- Standard runs STP-style, Razor runs ECN-style -- and direct market access (DMA) CFDs are available on either tier via cTrader and MT4/MT5. Which account is actually cheaper depends on your own trade size and frequency: a trader placing a few, larger trades often pays less in pure spread on Standard than in spread-plus-commission on Razor, while an active or algorithmic trader running higher volume at the raw Razor spread typically comes out ahead once the commission is netted against the tighter spread. There\'s no substitute for running your own numbers against your actual account size, trade size, and trading frequency.',
                ),
                array(
                    'heading' => 'Overnight financing (swap) costs',
                    'body'    => 'Hold a position open overnight and Pepperstone charges a swap fee based on the tom-next rate from its liquidity providers, plus its own admin/funding fee on top of that. The Standard account doesn\'t break this out as a separate line: instead, Pepperstone bakes roughly a 1-pip markup into the spread rather than charging a distinct overnight fee. Switch to the Islamic/swap-free variant of either account and the daily swap itself disappears, but a reported $100-per-lot administration fee applies for every five-day period a position stays open -- worth modelling carefully if you intend to hold positions for weeks rather than days, since that flat fee doesn\'t scale down the way a per-night swap would for a smaller position.',
                ),
                array(
                    'heading' => 'Non-trading fees: inactivity and currency conversion',
                    'body'    => 'Multiple independent sources we checked report that Pepperstone charges no inactivity fee at all, though we weren\'t able to independently verify that directly against the EU entity\'s own client agreement -- treat it as well-corroborated rather than contractually confirmed. On currency conversion, Pepperstone itself doesn\'t appear to charge a deposit fee when your funding currency differs from your account\'s base currency, but the sending bank or card network may apply its own conversion markup on that transfer, and we couldn\'t independently confirm a specific percentage for that pass-through cost.',
                ),
                array(
                    'heading' => 'Deposit and withdrawal fees',
                    'body'    => 'Pepperstone charges no fee on deposits or withdrawals except for international bank-transfer withdrawals, which carry a fee of roughly $20 (or £15). There\'s no minimum via bank transfer; card, Apple Pay, and Google Pay deposits carry a minimum of around £10. Processing time matters as much as the fee itself here: card deposits land instantly and bank-transfer deposits take up to two business days, while withdrawals range from under one business day (card, Apple Pay, Google Pay) to up to two business days (domestic bank transfer) to as long as seven business days (international bank transfer).',
                ),
                array(
                    'heading' => 'How Pepperstone\'s costs actually rank against the brokers we\'ve researched',
                    'body'    => 'Running Pepperstone\'s 1.1-pip EUR/USD spread through our disclosed scoring formula gives it a trading-costs score of 3.57/5 -- 31st of the 65 brokers in our researched set with a confirmed EUR/USD spread, almost exactly the dataset\'s median rather than meaningfully cheap or expensive on that one measure. That\'s worth knowing because Pepperstone\'s $10 minimum deposit undercuts 59 of the 78 brokers (76%) we have a confirmed minimum deposit for, and its overall score of 3.75/5 sits well above the dataset\'s midpoint -- "accessible and well-regulated" and "cheapest spread" are two different claims, and only the first one holds up for Pepperstone on the numbers. One place it is genuinely rare: Pepperstone is one of just 5 of the 139 brokers we\'ve researched confirmed to support all four of MT4, MT5, cTrader, and TradingView -- platform breadth that\'s a real differentiator even where the headline spread itself is unremarkable.',
                ),
            ),
            'faqs' => array(
                array(
                    'q' => 'Does Pepperstone charge an inactivity fee?',
                    'a' => 'Multiple independent sources we checked report that Pepperstone charges no inactivity fee, though we weren\'t able to verify that directly against the EU entity\'s own client agreement.',
                ),
                array(
                    'q' => 'What\'s the difference between Pepperstone\'s Standard and Razor accounts?',
                    'a' => 'Standard charges no separate commission, with spreads from around 1.0 pip (our on-file EUR/USD figure is 1.1 pips); Razor charges a raw spread from 0.0 pips plus a round-turn commission of roughly $6-7 per lot that varies by platform. Both have an Islamic/swap-free variant, and execution is no-dealing-desk on each -- Standard runs STP-style, Razor runs ECN-style.',
                ),
                array(
                    'q' => 'Does Pepperstone charge a fee to convert currency on deposit?',
                    'a' => 'Not from Pepperstone itself, as far as we could confirm -- but the bank or card network handling the transfer may apply its own conversion markup if your funding currency differs from your account\'s base currency, and we couldn\'t independently confirm a specific percentage for that pass-through cost.',
                ),
            ),
        ),
        'avatrade-fees' => array(
            'broker_slug'      => 'avatrade',
            'kind'             => 'fees',
            'title'            => 'AvaTrade Fees Explained',
            'meta_title'       => 'AvaTrade Fees Explained (2026): Spreads, Swap, and Hidden Costs',
            'meta_description' => 'A full breakdown of every fee AvaTrade can charge -- spread, overnight financing, inactivity, and currency conversion -- grounded in our own researched broker dataset, not marketing claims.',
            'intro'            => 'AvaTrade doesn\'t market itself on raw pricing the way a commission-based ECN broker does -- its cost structure is simpler but less transparent on paper, since everything is built into one dealing-desk spread rather than split into spread-plus-commission line items. This breaks down every fee AvaTrade can charge using the same researched data we score it against the other 138 brokers in our dataset. Every figure below is for Ava Trade EU Ltd, the entity reviewed on this site; a different AvaTrade entity elsewhere in the world may set different terms.',
            'sections' => array(
                array(
                    'heading' => 'Spreads and the dealing-desk model',
                    'body'    => 'AvaTrade runs a single pricing model across its account types rather than a Standard/Razor-style split: it operates as a dealing desk / market maker, meaning AvaTrade itself is the counterparty to your trade and sets its own spread, rather than passing through a raw interbank price plus a separate commission the way an ECN broker does. Our on-file headline EUR/USD spread is 0.93 pips. Because the spread is the whole cost -- there\'s no separate commission line to add or subtract -- what you see quoted is close to the full trading cost, unlike a Razor- or Raw-style account where the headline spread understates the true cost until commission is added back in.',
                ),
                array(
                    'heading' => 'Overnight financing (swap) costs',
                    'body'    => 'Standard accounts are charged normal overnight swap rates, calculated the way most brokers do it -- a benchmark rate differential plus AvaTrade\'s own markup, though the exact markup percentage wasn\'t confirmed in our research. The Islamic/swap-free account has an unusual structure worth flagging prominently: it\'s genuinely swap-free only for the first 5 days a position is held, after which an administration fee applies instead -- not the indefinite swap-free status some other brokers\' Islamic accounts offer. It also excludes certain currencies (RUB, MXN, TRY, ZAR) and crypto from swap-free treatment entirely, and AvaTrade reserves the right to revoke swap-free status altogether if it suspects abuse of the feature.',
                ),
                array(
                    'heading' => 'Non-trading fees: inactivity and currency conversion',
                    'body'    => 'Both of these are genuinely unconfirmed in our research, and we\'re not going to guess at numbers. We found no inactivity-fee figure for AvaTrade at all in this research pass -- if one exists, confirm it directly with AvaTrade before assuming your account is exempt. Currency conversion is the same: no specific percentage or fee schedule was found, so if your funding currency differs from your account\'s base currency, check AvaTrade\'s current terms directly rather than assuming either a specific cost or that it\'s free.',
                ),
                array(
                    'heading' => 'Deposit and withdrawal fees',
                    'body'    => 'No AvaTrade-side fee was confirmed on deposits or withdrawals, though third-party processor or bank fees may still apply depending on your method. The $100 minimum deposit applies across Retail and Islamic accounts alike. Processing time varies by method: deposits are near-instant by card, within 24 hours by e-wallet, and 7-10 business days by wire transfer; withdrawals process in 24-48 hours by card or e-wallet once verified, but can take up to 10 business days by wire.',
                ),
                array(
                    'heading' => 'How AvaTrade\'s costs actually rank against the brokers we\'ve researched',
                    'body'    => 'Running AvaTrade\'s 0.93-pip EUR/USD spread through our disclosed scoring formula gives it a trading-costs score of 3.79/5 -- 23rd of the 67 brokers in our researched set with a confirmed EUR/USD spread, meaning it\'s cheaper than roughly two-thirds of the dataset on this one measure. Minimum deposit tells a different story: AvaTrade\'s $100 only undercuts 24 of the 78 brokers (31%) we have a confirmed minimum deposit for -- on this measure it\'s pricier to get started than most of the dataset, not cheaper. Where AvaTrade is genuinely rare: it supports 5 trading platforms (its own AvaTradeGO, MT4, MT5, DupliTrade, and ZuluTrade) -- more than any other single broker in our full 139-broker dataset. No other broker we\'ve researched matches or exceeds that platform count.',
                ),
            ),
            'faqs' => array(
                array(
                    'q' => 'Does AvaTrade charge an inactivity fee?',
                    'a' => 'We found no confirmed inactivity-fee figure for AvaTrade in this research pass -- confirm directly with AvaTrade before assuming either way.',
                ),
                array(
                    'q' => 'What execution model does AvaTrade use?',
                    'a' => 'Dealing desk / market maker -- AvaTrade is the counterparty to your trade and sets its own spread, rather than operating as a true STP/ECN broker.',
                ),
                array(
                    'q' => 'Does AvaTrade charge a fee to convert currency on deposit?',
                    'a' => 'Not independently confirmed -- no specific percentage or fee schedule was found in our research, so check AvaTrade\'s current terms directly if your funding currency differs from your account\'s base currency.',
                ),
            ),
        ),
    );
}

/**
 * Per-language translations, mirrors
 * globalfxhub_editorial_comparison_translations()'s shape and purpose
 * exactly -- filled in once i18n/translate_broker_research.py's real
 * translate+review run completes for a given key.
 */
function globalfxhub_broker_research_translations() {
    return array(
        'pepperstone-fees' => array(
            'es' => array(
                'title' => 'Comisiones de Pepperstone explicadas',
                'meta_title' => 'Comisiones de Pepperstone explicadas (2026): spreads, comisiones, swap y costos ocultos',
                'meta_description' => 'Un desglose completo de cada comisión que Pepperstone puede cobrar -- spreads, comisión Razor, swap, inactividad y conversión de divisas -- basado en nuestro propio conjunto de datos de brókers que hemos investigado, y no en afirmaciones de marketing.',
                'intro' => 'Pepperstone promociona "spreads brutos desde 0.0 pips", pero eso es solo una partida dentro de lo que realmente determina el costo de operar con el bróker. Este análisis desglosa cada comisión que Pepperstone puede cobrar -- spread, comisión, financiación nocturna, inactividad, conversión de divisas y depósitos y retiradas de fondos -- utilizando los mismos datos investigados con los que lo puntuamos frente a los otros 138 brókers de nuestro conjunto de datos, en lugar del encuadre promocional de Pepperstone. Todas las cifras que aparecen a continuación corresponden a Pepperstone EU Limited, la entidad regulada por CySEC y FCA revisada en este sitio; una entidad distinta de Pepperstone en otra parte del mundo podría establecer condiciones diferentes.',
                'section_1_heading' => 'Spreads y comisiones: Standard vs Razor',
                'section_1_body' => 'La entidad europea de Pepperstone ofrece tres tipos de cuenta -- Standard, Razor y Professional (sujeta a requisitos de elegibilidad, con mayor apalancamiento) -- además de una variante islámica/sin swap de Standard y Razor. Standard no cobra comisión independiente: los spreads parten de unos 1.0 pip, y nuestra cifra de referencia para el spread EUR/USD de la cuenta es 1.1 pips. Razor, en cambio, cobra un spread bruto desde 0.0 pips más una comisión de ida y vuelta de aproximadamente $6-7 por lote, que varía según la plataforma de trading que se utilice. La ejecución de órdenes es no-dealing-desk en ambos niveles -- Standard funciona al estilo STP y Razor al estilo ECN -- y el acceso directo al mercado (DMA) con CFDs está disponible en cualquiera de los dos niveles a través de cTrader y MT4/MT5. Qué cuenta resulta realmente más barata depende del tamaño y la frecuencia de las operaciones: un trader que realiza pocas operaciones de mayor tamaño suele pagar menos en spread puro con Standard que en spread más comisión con Razor, mientras que un trader activo o algorítmico que opera con más volumen al spread bruto de Razor suele salir ganando una vez descontada la comisión del spread más ajustado. No hay sustituto para calcular las cifras propias con el tamaño real de la cuenta, el tamaño de las operaciones y la frecuencia de trading.',
                'section_2_heading' => 'Costos de financiación nocturna (swap)',
                'section_2_body' => 'Si se mantiene una posición abierta de un día para otro, Pepperstone cobra una comisión de swap basada en el tipo tom-next de sus proveedores de liquidez, más su propia comisión administrativa/de financiación. La cuenta Standard no desglosa esto como una partida separada: en su lugar, Pepperstone incorpora un recargo de aproximadamente 1-pip en el spread en lugar de cobrar una comisión nocturna diferenciada. Si se pasa a la variante islámica/sin swap de cualquiera de las dos cuentas, desaparece el swap diario, pero se aplica una comisión administrativa de $100 por lote, según se informa, por cada periodo de cinco días que la posición permanece abierta. Conviene analizarlo con cuidado si se piensa mantener posiciones durante semanas en lugar de días, ya que esa comisión fija no se reduce con el tamaño de la posición como sí lo haría un swap nocturno en una posición más pequeña.',
                'section_3_heading' => 'Comisiones no asociadas al trading: inactividad y conversión de divisas',
                'section_3_body' => 'Varias fuentes independientes que revisamos indican que Pepperstone no cobra ninguna comisión por inactividad, aunque no pudimos verificar esto de forma independiente con el contrato de cliente de la entidad europea; conviene considerarlo bien corroborado, pero no confirmado contractualmente. En cuanto a la conversión de divisas, Pepperstone en sí no parece cobrar una comisión por depósito cuando la divisa de financiación difiere de la divisa base de la cuenta, pero el banco remitente o la red de tarjetas puede aplicar su propio recargo de conversión en esa transferencia, y no pudimos confirmar de forma independiente un porcentaje concreto para ese costo intermedio.',
                'section_4_heading' => 'Comisiones de depósito y retirada de fondos',
                'section_4_body' => 'Pepperstone no cobra comisión por depósitos ni por retiradas de fondos, salvo en las retiradas por transferencia bancaria internacional, que conllevan una comisión de aproximadamente $20 (o £15). No hay un depósito mínimo por transferencia bancaria; los depósitos con tarjeta, Apple Pay y Google Pay tienen un mínimo de alrededor de £10. El tiempo de procesamiento importa tanto como la comisión en sí: los depósitos con tarjeta se reflejan de inmediato y los depósitos por transferencia bancaria tardan hasta dos días hábiles, mientras que las retiradas de fondos van desde menos de un día hábil (tarjeta, Apple Pay, Google Pay) hasta un máximo de dos días hábiles (transferencia bancaria nacional) y hasta siete días hábiles (transferencia bancaria internacional).',
                'section_5_heading' => 'Cómo se comparan los costos de Pepperstone con los brókers que hemos analizado',
                'section_5_body' => 'Aplicar el spread EUR/USD de 1.1 pips de Pepperstone a nuestra fórmula de puntuación publicada le asigna una puntuación de costos de trading de 3.57/5 -- el puesto 31 de los 65 brókers de nuestro conjunto investigado con un spread EUR/USD confirmado, prácticamente en la mediana del conjunto de datos, ni especialmente barato ni caro en esa medida. Conviene saberlo porque el depósito mínimo de $10 de Pepperstone queda por debajo del de 59 de los 78 brókers (76%) de los que tenemos un depósito mínimo confirmado, y su puntuación general de 3.75/5 se sitúa muy por encima del punto medio del conjunto de datos. "Accesible y bien regulado" y "el spread más barato" son dos afirmaciones distintas, y según las cifras solo la primera se sostiene para Pepperstone. Hay un aspecto en el que es realmente poco común: Pepperstone es uno de los 5 de los 139 brókers que hemos analizado con compatibilidad confirmada con los cuatro de MT4, MT5, cTrader y TradingView, una amplitud de plataformas que supone un diferenciador real incluso cuando el spread principal no resulta especialmente llamativo.',
                'faq_1_q' => '¿Pepperstone cobra una comisión por inactividad?',
                'faq_1_a' => 'Varias fuentes independientes que revisamos indican que Pepperstone no cobra comisión por inactividad, aunque no pudimos verificarlo directamente con el contrato de cliente de la entidad europea.',
                'faq_2_q' => '¿Cuál es la diferencia entre las cuentas Standard y Razor de Pepperstone?',
                'faq_2_a' => 'Standard no cobra comisión independiente, con spreads desde unos 1.0 pip (nuestra cifra de referencia para EUR/USD es 1.1 pips); Razor cobra un spread bruto desde 0.0 pips más una comisión de ida y vuelta de aproximadamente $6-7 por lote, que varía según la plataforma. Ambas tienen una variante islámica/sin swap, y la ejecución de órdenes es no-dealing-desk en cada una: Standard funciona al estilo STP y Razor al estilo ECN.',
                'faq_3_q' => '¿Pepperstone cobra una comisión por convertir divisas al depositar?',
                'faq_3_a' => 'No por parte de Pepperstone, según pudimos confirmar, pero el banco o la red de tarjetas que gestiona la transferencia puede aplicar su propio recargo de conversión si la divisa de financiación difiere de la divisa base de la cuenta, y no pudimos confirmar de forma independiente un porcentaje concreto para ese costo intermedio.',
            ),
            'de' => array(
                'title' => 'Pepperstone-Gebühren erklärt',
                'meta_title' => 'Pepperstone-Gebühren erklärt (2026): Spreads, Provisionen, Swap und versteckte Kosten',
                'meta_description' => 'Eine vollständige Aufschlüsselung aller Gebühren, die Pepperstone erheben kann -- Spreads, Razor-Provision, Swap, Inaktivitätsgebühr und Währungsumrechnungsgebühr -- basierend auf unserem eigenen recherchierten Broker-Datensatz und nicht auf Marketingaussagen.',
                'intro' => 'Pepperstone wirbt mit „Raw-Spreads ab 0.0 pips“, doch das ist nur ein Posten unter mehreren, die Ihre tatsächlichen Handelskosten bei diesem Broker bestimmen. Dieser Testbericht schlüsselt jede Gebühr auf, die Pepperstone erheben kann -- Spread, Provision, Übernachtfinanzierung, Inaktivität, Währungsumrechnung sowie Ein- und Auszahlungen. Grundlage ist dieselbe recherchierte Datenbasis, mit der wir Pepperstone gegenüber den anderen 138 Brokern in unserem Datensatz bewerten, und nicht die werblichen Darstellungen von Pepperstone selbst. Alle Angaben unten beziehen sich auf Pepperstone EU Limited, die auf dieser Seite getestete, von CySEC und FCA regulierte Gesellschaft. Eine andere Pepperstone-Gesellschaft in einer anderen Region kann abweichende Konditionen festlegen.',
                'section_1_heading' => 'Spreads und Provisionen: Standard vs. Razor',
                'section_1_body' => 'Die EU-Gesellschaft von Pepperstone bietet drei Kontotypen an -- Standard, Razor und Professional (mit Zugangsvoraussetzungen und höherem Hebel) -- sowie eine islamkonforme, swapfreie Option für Standard und Razor. Das Standard-Konto erhebt keine gesonderte Provision: Die Spreads beginnen bei etwa 1.0 pip, und unser eigener, archivierter Richtwert für den EUR/USD-Spread dieses Kontos liegt bei 1.1 pips. Razor berechnet dagegen einen Raw-Spread ab 0.0 pips zuzüglich einer Round-Turn-Provision von etwa $6-7 pro Lot, die je nach gewählter Handelsplattform variiert. Die Orderausführung erfolgt auf beiden Kontostufen ohne Dealing Desk: Standard arbeitet STP-ähnlich, Razor ECN-ähnlich. Direkten Marktzugang (DMA) bei CFDs gibt es auf beiden Stufen über cTrader sowie MT4/MT5. Welches Konto tatsächlich günstiger ist, hängt von Ihrer eigenen Ordergröße und Handelshäufigkeit ab. Wer wenige, aber größere Trades platziert, zahlt auf Standard oft weniger reinen Spread als auf Razor mit Spread plus Provision. Wer aktiv oder algorithmisch mit hohem Volumen zum Raw-Spread von Razor handelt, schneidet in der Regel besser ab, sobald die Provision gegen den engeren Spread gegengerechnet wird. Es gibt keine Alternative dazu, Ihre eigenen Zahlen mit Ihrer tatsächlichen Kontogröße, Ordergröße und Handelsfrequenz durchzurechnen.',
                'section_2_heading' => 'Kosten für Übernachtfinanzierung (Swap)',
                'section_2_body' => 'Wenn Sie eine Position über Nacht offen halten, berechnet Pepperstone eine Swap-Gebühr auf Basis des Tom-Next-Satzes seiner Liquiditätsanbieter, zuzüglich einer eigenen Verwaltungs- und Finanzierungsgebühr. Beim Standard-Konto wird dies nicht als eigener Posten ausgewiesen: Stattdessen schlägt Pepperstone etwa 1 pip Aufschlag in den Spread ein, anstatt eine gesonderte Übernachtgebühr zu erheben. Wechseln Sie zur islamkonformen, swapfreien Variante eines der beiden Konten, entfällt der tägliche Swap, dafür fällt eine berichtete Verwaltungsgebühr von $100 pro Lot für jede Fünf-Tage-Periode an, in der eine Position offen bleibt. Das sollten Sie sorgfältig einkalkulieren, wenn Sie Positionen eher über Wochen als über Tage halten wollen, da diese feste Gebühr bei kleineren Positionen nicht sinkt, wie es bei einem täglichen Swap der Fall wäre.',
                'section_3_heading' => 'Handelsunabhängige Gebühren: Inaktivität und Währungsumrechnung',
                'section_3_body' => 'Mehrere unabhängige Quellen, die wir geprüft haben, berichten, dass Pepperstone keine Inaktivitätsgebühr erhebt. Wir konnten dies jedoch nicht unabhängig anhand des Kundenvertrags der EU-Gesellschaft selbst überprüfen. Betrachten Sie es daher als gut belegt, nicht aber als vertraglich bestätigt. Bei der Währungsumrechnung scheint Pepperstone selbst keine Einzahlungsgebühr zu erheben, wenn Ihre Einzahlungswährung von der Basiswährung Ihres Kontos abweicht. Die sendende Bank oder das Kartennetzwerk kann bei dieser Überweisung jedoch einen eigenen Umrechnungsaufschlag erheben. Einen konkreten Prozentsatz für diese durchgereichte Kostenposition konnten wir nicht unabhängig bestätigen.',
                'section_4_heading' => 'Ein- und Auszahlungsgebühren',
                'section_4_body' => 'Pepperstone erhebt keine Gebühr auf Ein- oder Auszahlungen, mit Ausnahme internationaler Banküberweisungen bei Auszahlungen. Für diese fällt eine Gebühr von etwa $20 (oder £15) an. Bei Banküberweisungen gibt es kein Mindestbetrag. Für Einzahlungen per Karte, Apple Pay und Google Pay gilt ein Mindestbetrag von etwa £10. Die Bearbeitungszeit ist hier ebenso wichtig wie die Gebühr selbst: Kartenzahlungen werden sofort gutgeschrieben, Banküberweisungen benötigen bis zu zwei Geschäftstage. Auszahlungen dauern je nach Methode unter einem Geschäftstag (Karte, Apple Pay, Google Pay), bis zu zwei Geschäftstagen (inländische Banküberweisung) oder bis zu sieben Geschäftstagen (internationale Banküberweisung).',
                'section_5_heading' => 'Wie die Kosten von Pepperstone im Vergleich zu den von uns getesteten Brokern abschneiden',
                'section_5_body' => 'Wenn wir Pepperstones EUR/USD-Spread von 1.1 pips durch unsere offengelegte Bewertungsformel laufen lassen, ergibt sich eine Bewertung der Handelskosten von 3.57/5. Damit liegt Pepperstone auf Platz 31 von 65 Brokern in unserem Datensatz mit bestätigtem EUR/USD-Spread, nahezu exakt beim Median des Datensatzes und bei diesem einen Kriterium weder ausgesprochen günstig noch teuer. Das ist relevant, weil die Mindesteinzahlung von Pepperstone in Höhe von $10 59 der 78 Broker (76%) unterbietet, für die wir eine bestätigte Mindesteinzahlung haben. Die Gesamtbewertung von 3.75/5 liegt zudem deutlich über dem Mittelwert des Datensatzes. „Zugänglich und gut reguliert“ und „günstigster Spread“ sind zwei verschiedene Aussagen, und laut den Zahlen trifft nur die erste auf Pepperstone zu. Eine Eigenschaft ist tatsächlich selten: Pepperstone gehört zu den nur 5 von 139 Brokern, die wir untersucht haben und bei denen bestätigt ist, dass sie alle vier Plattformen MT4, MT5, cTrader und TradingView unterstützen. Diese Breite an Handelsplattformen ist ein echtes Unterscheidungsmerkmal, auch wenn der eigentliche Spread nicht besonders auffällt.',
                'faq_1_q' => 'Erhebt Pepperstone eine Inaktivitätsgebühr?',
                'faq_1_a' => 'Mehrere unabhängige Quellen, die wir geprüft haben, berichten, dass Pepperstone keine Inaktivitätsgebühr erhebt. Wir konnten dies jedoch nicht direkt anhand des Kundenvertrags der EU-Gesellschaft überprüfen.',
                'faq_2_q' => 'Worin unterscheiden sich die Konten Standard und Razor von Pepperstone?',
                'faq_2_a' => 'Das Standard-Konto erhebt keine gesonderte Provision, die Spreads beginnen bei etwa 1.0 pip (unser archivierter EUR/USD-Richtwert liegt bei 1.1 pips). Razor berechnet einen Raw-Spread ab 0.0 pips zuzüglich einer Round-Turn-Provision von etwa $6-7 pro Lot, die je nach Plattform variiert. Beide Konten gibt es in einer islamkonformen, swapfreien Variante, und die Orderausführung erfolgt bei beiden ohne Dealing Desk: Standard arbeitet STP-ähnlich, Razor ECN-ähnlich.',
                'faq_3_q' => 'Erhebt Pepperstone eine Gebühr für die Währungsumrechnung bei Einzahlungen?',
                'faq_3_a' => 'Nicht von Pepperstone selbst, soweit wir bestätigen konnten. Die Bank oder das Kartennetzwerk, über die die Überweisung läuft, kann jedoch einen eigenen Umrechnungsaufschlag erheben, wenn Ihre Einzahlungswährung von der Basiswährung Ihres Kontos abweicht. Einen konkreten Prozentsatz für diese durchgereichte Kostenposition konnten wir nicht unabhängig bestätigen.',
            ),
            'ar' => array(
                'title' => 'شرح رسوم Pepperstone',
                'meta_title' => 'شرح رسوم Pepperstone (2026): الفرق السعري والعمولات والسواب والتكاليف الخفية',
                'meta_description' => 'تفصيل كامل لكل رسم يمكن أن يفرضه Pepperstone -- الفرق السعري وعمولة Razor والسواب ورسوم عدم النشاط وتحويل العملات -- استنادًا إلى مجموعة بيانات الوسطاء التي أعددناها بأنفسنا، لا إلى ادعاءات تسويقية.',
                'intro' => 'يُسوّق Pepperstone «فروقات سعرية خام تبدأ من 0.0 pips»، لكن هذا بند واحد فقط من بين البنود التي تحدد فعليًا تكلفة التداول معه. تستعرض هذه المقالة كل رسم يمكن أن يفرضه Pepperstone -- الفرق السعري والعمولة والتمويل لليلة واحدة والسواب ورسوم عدم النشاط وتحويل العملات والإيداع والسحب -- مستندةً إلى البيانات البحثية نفسها التي نقيّم بها الوسيط مقارنةً بالوسطاء الـ138 الآخرين في مجموعة بياناتنا، لا إلى الصياغة الترويجية لـ Pepperstone. وتنطبق جميع الأرقام الواردة أدناه على Pepperstone EU Limited، وهي الجهة الخاضعة لتنظيم CySEC وFCA والتي تُجرى عليها المراجعة في هذا الموقع؛ وقد تطبّق جهة Pepperstone أخرى في مكان آخر من العالم شروطًا مختلفة.',
                'section_1_heading' => 'الفرق السعري والعمولات: Standard مقابل Razor',
                'section_1_body' => 'تقدّم الجهة الأوروبية لـ Pepperstone ثلاثة أنواع من الحسابات -- Standard وRazor وProfessional (مشروط بالأهلية، مع رافعة مالية أعلى) -- إضافةً إلى خيار إسلامي/خالٍ من السواب متاح على كل من Standard وRazor. لا يفرض حساب Standard عمولة منفصلة: تبدأ الفروق السعرية من نحو 1.0 pip، وقد بلغ الفرق السعري الرئيسي لزوج EUR/USD المسجَّل لدينا لهذا الحساب 1.1 pips. أما Razor فيفرض فرقًا سعريًا خامًا يبدأ من 0.0 pips إضافةً إلى عمولة الدورة الكاملة التي تبلغ نحو $6-7 لكل لوت، وتختلف بحسب المنصة التي تتداول عليها. يتم تنفيذ الصفقات دون طاولة تداول (no-dealing-desk) في الفئتين، إذ يعمل Standard وفق نموذج STP بينما يعمل Razor وفق نموذج ECN، ويتوفر الوصول المباشر إلى السوق (DMA) في عقود CFDs على أي من الفئتين عبر cTrader وMT4/MT5. وأيّ الحسابين أرخص فعليًا يعتمد على حجم صفقاتك وتكرارها: فالمتداول الذي يُجري عدة صفقات كبيرة يدفع في الغالب تكلفة فرق سعري أقل على Standard مما يدفعه من فرق سعري وعمولة على Razor، بينما يحقق المتداول النشط أو الخوارزمي الذي يُجري حجمًا أكبر بالفرق السعري الخام لـ Razor مزيّةً عادةً بعد احتساب العمولة مقابل الفرق السعري الأضيق. لا يوجد بديل عن احتساب أرقامك الخاصة وفق حجم حسابك وحجم صفقاتك وتكرار تداولك.',
                'section_2_heading' => 'تكاليف التمويل لليلة واحدة (السواب)',
                'section_2_body' => 'إذا أبقيت مركزًا مفتوحًا طوال الليل، يفرض Pepperstone رسم سواب يستند إلى سعر tom-next من مزوّدي السيولة لديه، إضافةً إلى رسم الإدارة/التمويل الخاص به فوق ذلك. لا يُظهر حساب Standard هذا البند كسطر منفصل، بل يُدرج Pepperstone هامشًا يقارب 1 pip ضمن الفرق السعري بدلًا من فرض رسم تمويل ليلي منفصل. أما إذا انتقلت إلى النسخة الإسلامية/الخالية من السواب من أي من الحسابين، فيختفي السواب اليومي نفسه، لكن يُطبَّق رسم إدارة مُعلن قدره $100 لكل لوت عن كل فترة من خمسة أيام تبقى فيها الصفقة مفتوحة. وينبغي دراسة ذلك بعناية إذا كنت تعتزم الاحتفاظ بالمراكز لأسابيع لا لأيام، إذ إن هذا الرسم الثابت لا يتناقص مع حجم مركز أصغر كما يتناقص السواب اليومي.',
                'section_3_heading' => 'رسوم غير متعلقة بالتداول: عدم النشاط وتحويل العملات',
                'section_3_body' => 'تفيد مصادر مستقلة متعددة راجعناها بأن Pepperstone لا يفرض أي رسم على عدم النشاط إطلاقًا، لكننا لم نتمكن من التحقق من ذلك بشكل مستقل مباشرةً مقابل اتفاقية العميل الخاصة بالجهة الأوروبية، لذا ينبغي اعتباره مدعومًا بأدلة متعددة لا مؤكدًا تعاقديًا. أما فيما يخص تحويل العملات، فلا يبدو أن Pepperstone نفسه يفرض رسم إيداع عندما تختلف عملة التمويل لديك عن العملة الأساسية لحسابك، لكن البنك المُرسِل أو شبكة البطاقات قد تطبّق هامش تحويل خاصًا بها على هذه العملية، ولم نتمكن من تأكيد نسبة محددة لتكلفة العبور هذه بشكل مستقل.',
                'section_4_heading' => 'رسوم الإيداع والسحب',
                'section_4_body' => 'لا يفرض Pepperstone أي رسم على الإيداعات أو عمليات السحب، باستثناء السحب عبر التحويل البنكي الدولي الذي يحمل رسمًا يبلغ نحو $20 (أو £15). لا يوجد حد أدنى للإيداع عبر التحويل البنكي، بينما تحمل الإيداعات عبر البطاقات وApple Pay وGoogle Pay حدًا أدنى يبلغ نحو £10. ولمدة المعالجة أهمية لا تقل عن الرسم نفسه هنا: تصل إيداعات البطاقات فورًا، بينما تستغرق إيداعات التحويل البنكي حتى يومَي عمل، وتتراوح مدة السحب من أقل من يوم عمل واحد (البطاقات وApple Pay وGoogle Pay) إلى يومَي عمل كحد أقصى (التحويل البنكي المحلي)، وصولًا إلى سبعة أيام عمل كحد أقصى (التحويل البنكي الدولي).',
                'section_5_heading' => 'موقع تكاليف Pepperstone مقارنةً بالوسطاء الذين راجعناهم',
                'section_5_body' => 'عند تطبيق فرق EUR/USD البالغ 1.1 pip الخاص بـ Pepperstone على معادلة التقييم المعلنة لدينا، يحصل على تقييم لتكاليف التداول قدره 3.57/5، أي في المرتبة 31 من أصل 65 وسيطًا في مجموعتنا البحثية الذين لديهم فرق EUR/USD مؤكد، وهي مرتبة قريبة تقريبًا من الوسيط الإحصائي لمجموعة البيانات، أي لا رخيصة ولا مكلفة بشكل ملحوظ على هذا المقياس وحده. وهذا جدير بالمعرفة لأن الحد الأدنى للإيداع لدى Pepperstone البالغ $10 أقل من الحد الأدنى لدى 59 من أصل 78 وسيطًا (76%) لدينا حد أدنى مؤكد للإيداع لهم، كما أن تقييمه الإجمالي البالغ 3.75/5 يتجاوز نقطة المنتصف لمجموعة البيانات بوضوح. فعبارتا «سهل الوصول ومنظم جيدًا» و«أرخص فرق سعري» ادعاءان مختلفان، ولا يصمد وفق الأرقام إلا الادعاء الأول بالنسبة لـ Pepperstone. ومن المواضع التي يتميز فيها فعليًا أنه واحد من 5 فقط من أصل 139 وسيطًا راجعناهم تأكد دعمه لجميع المنصات الأربع: MT4 وMT5 وcTrader وTradingView، وهو اتساع في المنصات يمثّل ميزة حقيقية حتى حين لا يكون الفرق السعري الرئيسي استثنائيًا.',
                'faq_1_q' => 'هل يفرض Pepperstone رسوم عدم النشاط؟',
                'faq_1_a' => 'تفيد مصادر مستقلة متعددة راجعناها بأن Pepperstone لا يفرض أي رسم على عدم النشاط، لكننا لم نتمكن من التحقق من ذلك مباشرةً مقابل اتفاقية العميل الخاصة بالجهة الأوروبية.',
                'faq_2_q' => 'ما الفرق بين حسابي Standard وRazor لدى Pepperstone؟',
                'faq_2_a' => 'لا يفرض حساب Standard عمولة منفصلة، وتبدأ فروقاته السعرية من نحو 1.0 pip (ورقم EUR/USD المسجَّل لدينا هو 1.1 pips)؛ أما Razor فيفرض فرقًا سعريًا خامًا يبدأ من 0.0 pips إضافةً إلى عمولة دورة كاملة تبلغ نحو $6-7 لكل لوت تختلف بحسب المنصة. ويتوفر لكلا الحسابين خيار إسلامي/خالٍ من السواب، ويتم تنفيذ الصفقات دون طاولة تداول في كل منهما، إذ يعمل Standard وفق نموذج STP ويعمل Razor وفق نموذج ECN.',
                'faq_3_q' => 'هل يفرض Pepperstone رسومًا لتحويل العملات عند الإيداع؟',
                'faq_3_a' => 'ليس من Pepperstone نفسه، على حد ما تمكنا من التأكد منه، لكن البنك أو شبكة البطاقات التي تتولى التحويل قد تطبّق هامش تحويل خاصًا بها إذا اختلفت عملة التمويل لديك عن العملة الأساسية لحسابك، ولم نتمكن من تأكيد نسبة محددة لتكلفة العبور هذه بشكل مستقل.',
            ),
            'fr' => array(
                'title' => 'Les frais de Pepperstone expliqués',
                'meta_title' => 'Les frais de Pepperstone expliqués (2026) : spreads, commissions, swap et coûts cachés',
                'meta_description' => 'Une analyse complète de chaque frais que Pepperstone peut appliquer (spreads, commission Razor, swap, frais d\'inactivité et de conversion de devises), fondée sur notre propre base de données de courtiers analysés, et non sur des affirmations commerciales.',
                'intro' => 'Pepperstone met en avant des « spreads bruts à partir de 0.0 pips », mais ce n\'est qu\'un poste parmi ceux qui déterminent réellement le coût de vos transactions avec ce courtier. Cette analyse détaille chaque frais que Pepperstone peut appliquer (spread, commission, financement overnight, inactivité, conversion de devises et dépôts/retraits) en s\'appuyant sur les mêmes données que celles qui nous servent à évaluer ce courtier par rapport aux 138 autres courtiers de notre base, et non sur le discours promotionnel de Pepperstone. Tous les chiffres ci-dessous concernent Pepperstone EU Limited, l\'entité régulée par la CySEC et la FCA analysée sur ce site ; une autre entité Pepperstone dans le monde peut appliquer des conditions différentes.',
                'section_1_heading' => 'Spreads et commissions : Standard contre Razor',
                'section_1_body' => 'L\'entité européenne de Pepperstone propose trois types de comptes de trading : Standard, Razor et Professional (sous condition d\'éligibilité, avec un effet de levier plus élevé), ainsi qu\'une option islamique/sans swap sur les comptes Standard et Razor. Le compte Standard ne facture aucune commission distincte : les spreads démarrent à environ 1.0 pip, et le spread EUR/USD de référence que nous avons relevé pour ce compte est de 1.1 pips. Le compte Razor applique en revanche un spread brut à partir de 0.0 pips, plus une commission aller-retour d\'environ $6-7 par lot, qui varie selon la plateforme de trading utilisée. L\'exécution est sans dealing desk pour les deux niveaux (Standard fonctionne en mode STP, Razor en mode ECN), et l\'accès direct au marché (DMA) sur les CFD est disponible sur l\'un ou l\'autre niveau via cTrader et MT4/MT5. Le compte le plus avantageux dépend de la taille et de la fréquence de vos transactions : un trader qui place quelques transactions de montant élevé paie souvent moins en spread pur sur Standard qu\'en spread plus commission sur Razor, tandis qu\'un trader actif ou algorithmique, réalisant des volumes importants au spread brut Razor, s\'en sort généralement mieux une fois la commission déduite du spread plus serré. Rien ne remplace le calcul de vos propres coûts en fonction de la taille de votre compte, de la taille de vos transactions et de votre fréquence de trading.',
                'section_2_heading' => 'Coûts de financement overnight (swap)',
                'section_2_body' => 'Si vous conservez une position ouverte pendant la nuit, Pepperstone facture des frais de swap basés sur le taux tom-next de ses fournisseurs de liquidité, auxquels s\'ajoutent ses propres frais d\'administration et de financement. Le compte Standard ne détaille pas ce coût comme une ligne distincte : Pepperstone intègre plutôt une majoration d\'environ 1 pip dans le spread au lieu de facturer des frais overnight distincts. Si vous passez à la variante islamique/sans swap de l\'un ou l\'autre compte, le swap quotidien disparaît, mais des frais d\'administration forfaitaires de $100 par lot s\'appliquent pour chaque période de cinq jours pendant laquelle une position reste ouverte. Il convient de modéliser ce coût avec soin si vous comptez conserver vos positions pendant des semaines plutôt que des jours, car ces frais forfaitaires ne diminuent pas proportionnellement à la taille de la position, contrairement à un swap quotidien.',
                'section_3_heading' => 'Frais hors trading : inactivité et conversion de devises',
                'section_3_body' => 'Plusieurs sources indépendantes que nous avons consultées indiquent que Pepperstone ne facture aucun frais d\'inactivité, mais nous n\'avons pas pu le vérifier directement dans le contrat client de l\'entité européenne. Considérez-la donc comme bien corroborée plutôt que comme confirmée contractuellement. En matière de conversion de devises, Pepperstone ne semble pas facturer de frais de dépôt lorsque votre devise de financement diffère de la devise de base de votre compte, mais la banque émettrice ou le réseau de la carte peut appliquer sa propre majoration de conversion sur ce transfert. Nous n\'avons pas pu confirmer de pourcentage précis pour ce coût intermédiaire.',
                'section_4_heading' => 'Frais de dépôt et de retrait',
                'section_4_body' => 'Pepperstone ne facture aucun frais sur les dépôts ou les retraits, à l\'exception des retraits par virement bancaire international, qui coûtent environ $20 (ou £15). Il n\'existe aucun minimum pour les virements bancaires ; les dépôts par carte, Apple Pay et Google Pay sont soumis à un minimum d\'environ £10. Le délai de traitement compte autant que les frais eux-mêmes : les dépôts par carte sont instantanés, tandis que les virements bancaires prennent jusqu\'à deux jours ouvrés. Quant aux retraits, leur délai varie de moins d\'un jour ouvré (carte, Apple Pay, Google Pay) à jusqu\'à deux jours ouvrés (virement bancaire national), et jusqu\'à sept jours ouvrés (virement bancaire international).',
                'section_5_heading' => 'Comment les coûts de Pepperstone se situent par rapport aux courtiers que nous avons analysés',
                'section_5_body' => 'En appliquant le spread EUR/USD de 1.1 pip de Pepperstone à notre formule de notation publiée, on obtient une note de coûts de trading de 3.57/5, soit la 31e place parmi les 65 courtiers de notre échantillon dont le spread EUR/USD est confirmé. Ce résultat se situe pratiquement à la médiane de la base, sans être véritablement bon marché ni coûteux sur ce seul critère. C\'est un point à retenir, car le dépôt minimum de $10 de Pepperstone est inférieur à celui de 59 des 78 courtiers (76 %) dont nous connaissons un dépôt minimum confirmé, et sa note globale de 3.75/5 se situe nettement au-dessus du point médian de la base. « Accessible et bien réglementé » et « spread le moins cher » sont deux affirmations différentes, et seule la première se vérifie pour Pepperstone au vu des chiffres. Un point le distingue vraiment : Pepperstone est l\'un des 5 seuls courtiers, sur les 139 que nous avons analysés, dont la prise en charge de MT4, MT5, cTrader et TradingView est confirmée. Cette diversité de plateformes constitue un véritable atout, même lorsque le spread affiché n\'a rien d\'exceptionnel.',
                'faq_1_q' => 'Pepperstone facture-t-il des frais d\'inactivité ?',
                'faq_1_a' => 'Plusieurs sources indépendantes que nous avons consultées indiquent que Pepperstone ne facture aucun frais d\'inactivité, mais nous n\'avons pas pu le vérifier directement dans le contrat client de l\'entité européenne.',
                'faq_2_q' => 'Quelle est la différence entre les comptes Standard et Razor de Pepperstone ?',
                'faq_2_a' => 'Le compte Standard ne facture aucune commission distincte, avec des spreads à partir d\'environ 1.0 pip (notre chiffre EUR/USD de référence est de 1.1 pips). Le compte Razor applique un spread brut à partir de 0.0 pips, plus une commission aller-retour d\'environ $6-7 par lot qui varie selon la plateforme. Les deux comptes existent en variante islamique/sans swap, et l\'exécution est sans dealing desk pour chacun : Standard fonctionne en mode STP, Razor en mode ECN.',
                'faq_3_q' => 'Pepperstone facture-t-il des frais de conversion de devises lors du dépôt ?',
                'faq_3_a' => 'Pas de la part de Pepperstone lui-même, d\'après nos vérifications. En revanche, la banque ou le réseau de carte qui traite le transfert peut appliquer sa propre majoration de conversion si votre devise de financement diffère de la devise de base de votre compte. Nous n\'avons pas pu confirmer de pourcentage précis pour ce coût intermédiaire.',
            ),
            'pl' => array(
                'title' => 'Opłaty Pepperstone wyjaśnione',
                'meta_title' => 'Opłaty Pepperstone wyjaśnione (2026): spready, prowizje, swap i ukryte koszty',
                'meta_description' => 'Pełne zestawienie wszystkich opłat, jakie Pepperstone może pobierać – spready, prowizja Razor, swap, opłata za nieaktywność i przewalutowanie – oparte na naszym własnym, zbadanym zbiorze danych o brokerach, a nie na deklaracjach marketingowych.',
                'intro' => 'Pepperstone reklamuje „surowe spready od 0.0 pips”, ale to tylko jedna pozycja w tym, co faktycznie determinuje koszt handlu u tego brokera. Poniżej omawiamy każdą opłatę, jaką Pepperstone może pobierać – spread, prowizję, koszt finansowania overnight, opłatę za nieaktywność, przewalutowanie oraz wpłaty i wypłaty – korzystając z tych samych zbadanych danych, na podstawie których oceniamy go wobec pozostałych 138 brokerów w naszym zbiorze, a nie z promocyjnego przedstawienia Pepperstone. Wszystkie podane tu wartości dotyczą Pepperstone EU Limited, podmiotu regulowanego przez CySEC i FCA, który recenzujemy na tej stronie; inny podmiot Pepperstone działający w innym miejscu świata może stosować inne warunki.',
                'section_1_heading' => 'Spready i prowizje: Standard kontra Razor',
                'section_1_body' => 'Europejski podmiot Pepperstone oferuje trzy typy kont – Standard, Razor oraz Professional (dostępne po spełnieniu warunków kwalifikacyjnych, z wyższym lewarowaniem) – a także wariant islamski/wolny od swapu dla kont Standard i Razor. Konto Standard nie pobiera osobnej prowizji: spready zaczynają się od około 1.0 pip, a według naszych danych orientacyjny spread EUR/USD dla tego konta wynosi 1.1 pips. Konto Razor pobiera natomiast surowy spread od 0.0 pips oraz prowizję round-turn w wysokości około $6-7 za lot, która zależy od wybranej platformy handlowej. Realizacja zleceń jest bez desku dealerskiego w obu wariantach – Standard działa w modelu zbliżonym do STP, a Razor w modelu zbliżonym do ECN – a bezpośredni dostęp do rynku (DMA) w przypadku CFD jest dostępny w obu wariantach przez cTrader oraz MT4/MT5. To, które konto jest faktycznie tańsze, zależy od wielkości i częstotliwości transakcji inwestora: osoba zawierająca kilka większych transakcji często płaci mniej w samym spreadzie na koncie Standard niż w spreadzie plus prowizji na koncie Razor, natomiast aktywny lub algorytmiczny trader realizujący większy wolumen przy surowym spreadzie Razor zwykle wypada korzystniej, gdy prowizję odejmie się od węższego spreadu. Nie ma zastępstwa dla własnych obliczeń opartych na rzeczywistej wielkości konta, wielkości transakcji i częstotliwości handlu.',
                'section_2_heading' => 'Koszty finansowania overnight (swap)',
                'section_2_body' => 'Utrzymanie pozycji przez noc skutkuje opłatą swap opartą na stawce tom-next od dostawców płynności, a do tego dochodzi własna opłata administracyjna/finansowa Pepperstone. Konto Standard nie wydziela tego jako osobnej pozycji: zamiast tego Pepperstone wbudowuje w spread narzut rzędu około 1-pip, zamiast pobierać odrębną opłatę overnight. Przejście na wariant islamski/wolny od swapu dowolnego konta eliminuje dzienny swap, ale obowiązuje wtedy opłata administracyjna w wysokości $100 za lot za każdy pięciodniowy okres utrzymywania pozycji – warto to dokładnie przeliczyć, jeśli zamierzasz trzymać pozycje przez tygodnie zamiast dni, ponieważ ta stała opłata nie maleje proporcjonalnie przy mniejszej pozycji, tak jak zrobiłby to dzienny swap.',
                'section_3_heading' => 'Opłaty niezwiązane z handlem: nieaktywność i przewalutowanie',
                'section_3_body' => 'Kilka niezależnych źródeł, które sprawdziliśmy, podaje, że Pepperstone nie pobiera żadnej opłaty za nieaktywność, choć nie udało nam się niezależnie zweryfikować tego bezpośrednio w umowie z klientem podmiotu europejskiego – traktujemy to jako dobrze potwierdzone, lecz nie potwierdzone umownie. Jeśli chodzi o przewalutowanie, Pepperstone sam nie wydaje się pobierać opłaty za wpłatę, gdy waluta środków różni się od waluty bazowej konta, jednak bank nadawcy lub sieć kartowa może naliczyć własny narzut przewalutowania przy tym przelewie, a nie udało nam się niezależnie potwierdzić konkretnego procentu za ten koszt przenoszony dalej.',
                'section_4_heading' => 'Opłaty za wpłaty i wypłaty',
                'section_4_body' => 'Pepperstone nie pobiera opłat za wpłaty ani wypłaty, z wyjątkiem międzynarodowych wypłat przelewem bankowym, które kosztują około $20 (lub £15). Przelewem bankowym nie obowiązuje żadne minimum; przy wpłatach kartą, Apple Pay i Google Pay minimum wynosi około £10. Czas realizacji ma tu znaczenie równie duże jak sama opłata: wpłaty kartą są księgowane natychmiast, a wpłaty przelewem bankowym trwają do dwóch dni roboczych, natomiast wypłaty trwają od mniej niż jednego dnia roboczego (karta, Apple Pay, Google Pay) przez do dwóch dni roboczych (krajowy przelew bankowy) aż do siedmiu dni roboczych (międzynarodowy przelew bankowy).',
                'section_5_heading' => 'Jak koszty Pepperstone wypadają na tle zbadanych przez nas brokerów',
                'section_5_body' => 'Po zastosowaniu naszego opublikowanego wzoru oceny do spreadu EUR/USD wynoszącego 1.1 pipa Pepperstone otrzymuje ocenę kosztów handlu 3.57/5 – 31. miejsce spośród 65 brokerów w naszym zbiorze z potwierdzonym spreadem EUR/USD, niemal dokładnie na medianie zbioru, a nie wyraźnie tani ani drogi w tej jednej mierze. Warto o tym wiedzieć, ponieważ minimalny depozyt Pepperstone wynoszący $10 jest niższy niż u 59 spośród 78 brokerów (76%), dla których mamy potwierdzony minimalny depozyt, a jego ogólna ocena 3.75/5 wyraźnie przewyższa środek zbioru – „dostępny i dobrze regulowany” oraz „najtańszy spread” to dwa różne twierdzenia, a na podstawie liczb potwierdza się tylko pierwsze z nich w przypadku Pepperstone. Pod jednym względem Pepperstone jest naprawdę rzadkim przypadkiem: to jeden z zaledwie 5 spośród 139 zbadanych przez nas brokerów, u których potwierdzono obsługę wszystkich czterech platform: MT4, MT5, cTrader i TradingView – szeroki wybór platform to realna przewaga nawet tam, gdzie sam spread nie wyróżnia się.',
                'faq_1_q' => 'Czy Pepperstone pobiera opłatę za nieaktywność?',
                'faq_1_a' => 'Kilka niezależnych źródeł, które sprawdziliśmy, podaje, że Pepperstone nie pobiera opłaty za nieaktywność, choć nie udało nam się zweryfikować tego bezpośrednio w umowie z klientem podmiotu europejskiego.',
                'faq_2_q' => 'Czym różnią się konta Standard i Razor w Pepperstone?',
                'faq_2_a' => 'Konto Standard nie pobiera osobnej prowizji, a spready zaczynają się od około 1.0 pip (nasze dane EUR/USD wynoszą 1.1 pips); konto Razor pobiera surowy spread od 0.0 pips oraz prowizję round-turn w wysokości około $6-7 za lot, zależną od platformy. Oba warianty mają wersję islamską/wolną od swapu, a realizacja zleceń jest bez desku dealerskiego w każdym z nich – Standard działa w modelu zbliżonym do STP, Razor w modelu zbliżonym do ECN.',
                'faq_3_q' => 'Czy Pepperstone pobiera opłatę za przewalutowanie przy wpłacie?',
                'faq_3_a' => 'Nie przez sam Pepperstone, o ile udało nam się to potwierdzić – jednak bank lub sieć kartowa obsługująca przelew może naliczyć własny narzut przewalutowania, jeśli waluta środków różni się od waluty bazowej konta, a nie udało nam się niezależnie potwierdzić konkretnego procentu za ten koszt przenoszony dalej.',
            ),
            'it' => array(
                'title' => 'Pepperstone: le commissioni spiegate',
                'meta_title' => 'Pepperstone: le commissioni spiegate (2026): spread, commissioni, swap e costi nascosti',
                'meta_description' => 'Un\'analisi completa di ogni commissione che Pepperstone può applicare -- spread, commissione Razor, swap, commissione di inattività e commissione di conversione valutaria -- basata sui dati raccolti in proprio nel nostro database di broker, non su affermazioni di marketing.',
                'intro' => 'Pepperstone pubblicizza “spread grezzi da 0.0 pip”, ma questa è solo una delle voci che determinano davvero quanto costa operare con il broker. Questo articolo analizza ogni commissione che Pepperstone può applicare -- spread, commissione, costi di overnight financing, commissione di inattività, commissione di conversione valutaria e depositi/prelievi -- utilizzando gli stessi dati raccolti con cui la valutiamo rispetto agli altri 138 broker del nostro database, e non sulla presentazione promozionale di Pepperstone. Tutte le cifre riportate di seguito si riferiscono a Pepperstone EU Limited, l\'entità regolamentata da CySEC e FCA recensita su questo sito; un\'altra entità Pepperstone in un altro Paese potrebbe applicare condizioni diverse.',
                'section_1_heading' => 'Spread e commissioni: Standard vs Razor',
                'section_1_body' => 'L\'entità europea di Pepperstone offre tre tipologie di conto -- Standard, Razor e Professional (riservato a chi ha i requisiti, con leva finanziaria più alta) -- oltre a un\'opzione islamica/swap-free disponibile sia per Standard sia per Razor. Il conto Standard non prevede commissioni separate: gli spread partono da circa 1.0 pip e, secondo i nostri dati, lo spread EUR/USD indicativo per questo conto è di 1.1 pip. Il conto Razor applica invece uno spread grezzo da 0.0 pip più una commissione round-turn di circa $6-7 per lotto, che varia in base alla piattaforma utilizzata. L\'esecuzione degli ordini è senza dealing desk su entrambe le tipologie: Standard opera in modalità simile a STP, Razor in modalità simile a ECN. Le operazioni CFD con accesso diretto al mercato (DMA) sono disponibili su entrambe le tipologie tramite cTrader e MT4/MT5. Quale conto risulti effettivamente più conveniente dipende dalla dimensione e dalla frequenza delle proprie operazioni: chi effettua poche operazioni di importo elevato spesso paga meno in spread puro su Standard rispetto a spread più commissione su Razor, mentre chi opera in modo attivo o algoritmico con volumi elevati, sfruttando lo spread grezzo di Razor, di solito risulta avvantaggiato una volta sottratta la commissione dallo spread più stretto. Non c\'è nulla che sostituisca il calcolo dei propri costi reali, in base alla dimensione del conto, a quella delle operazioni e alla frequenza di trading.',
                'section_2_heading' => 'Costi di overnight financing (swap)',
                'section_2_body' => 'Se si mantiene una posizione aperta durante la notte, Pepperstone applica una commissione di swap basata sul tasso tom-next dei propri liquidity provider, a cui si aggiunge una propria commissione amministrativa/di finanziamento. Il conto Standard non riporta questo costo come voce separata: Pepperstone incorpora invece un markup di circa 1 pip nello spread, anziché addebitare una commissione overnight distinta. Passando alla variante islamica/swap-free di uno dei due conti, lo swap giornaliero scompare, ma si applica una commissione amministrativa fissa di $100 per lotto per ogni periodo di cinque giorni in cui la posizione resta aperta. Vale la pena valutarla con attenzione se si intende mantenere posizioni per settimane anziché per giorni, poiché questa commissione fissa non si riduce proporzionalmente per posizioni più piccole, come invece accadrebbe con uno swap notturno.',
                'section_3_heading' => 'Commissioni non di trading: inattività e conversione valutaria',
                'section_3_body' => 'Diverse fonti indipendenti da noi consultate riportano che Pepperstone non applica alcuna commissione di inattività, anche se non siamo riusciti a verificarlo in modo indipendente direttamente nel contratto con i clienti dell\'entità europea. L\'informazione va quindi considerata ben corroborata, ma non confermata contrattualmente. Per quanto riguarda la conversione valutaria, Pepperstone stessa non sembra applicare una commissione sul deposito quando la valuta di finanziamento è diversa da quella base del conto; tuttavia, la banca o il circuito della carta utilizzato per il trasferimento potrebbe applicare un proprio markup di conversione, e non siamo riusciti a confermare in modo indipendente una percentuale specifica per questo costo intermedio.',
                'section_4_heading' => 'Commissioni su depositi e prelievi',
                'section_4_body' => 'Pepperstone non applica commissioni su depositi o prelievi, fatta eccezione per i prelievi internazionali via bonifico bancario, che prevedono una commissione di circa $20 (o £15). Per i bonifici bancari non è previsto alcun deposito minimo; per depositi con carta, Apple Pay e Google Pay il deposito minimo è di circa £10. I tempi di elaborazione contano quanto la commissione stessa: i depositi con carta vengono accreditati immediatamente, mentre quelli tramite bonifico bancario richiedono fino a due giorni lavorativi. I prelievi richiedono da meno di un giorno lavorativo (carta, Apple Pay, Google Pay) fino a due giorni lavorativi (bonifico bancario nazionale), e fino a sette giorni lavorativi (bonifico bancario internazionale).',
                'section_5_heading' => 'Come si collocano i costi di Pepperstone rispetto ai broker che abbiamo analizzato',
                'section_5_body' => 'Applicando lo spread EUR/USD di 1.1 pip di Pepperstone alla formula di punteggio che pubblichiamo, si ottiene un punteggio dei costi di trading di 3.57/5: il 31° posto tra i 65 broker del nostro campione con uno spread EUR/USD confermato, quasi esattamente la mediana del database, né particolarmente economico né particolarmente caro su questo parametro. Questo dato è utile, perché il deposito minimo di $10 di Pepperstone è inferiore a quello di 59 dei 78 broker (76%) per i quali abbiamo un deposito minimo confermato, e il suo punteggio complessivo di 3.75/5 si colloca ben al di sopra del punto medio del database. “Accessibile e ben regolamentato” e “lo spread più basso” sono due affermazioni diverse, e per Pepperstone, sulla base dei numeri, solo la prima regge. C\'è però un aspetto in cui Pepperstone è davvero rara: è uno dei soli 5 broker su 139 da noi analizzati con supporto confermato per tutte e quattro le piattaforme MT4, MT5, cTrader e TradingView. Questa ampiezza di piattaforme è un vero elemento di differenziazione, anche quando lo spread dichiarato non ha nulla di straordinario.',
                'faq_1_q' => 'Pepperstone applica una commissione di inattività?',
                'faq_1_a' => 'Diverse fonti indipendenti da noi consultate riportano che Pepperstone non applica alcuna commissione di inattività, anche se non siamo riusciti a verificarlo direttamente nel contratto con i clienti dell\'entità europea.',
                'faq_2_q' => 'Qual è la differenza tra i conti Standard e Razor di Pepperstone?',
                'faq_2_a' => 'Il conto Standard non prevede commissioni separate, con spread a partire da circa 1.0 pip (secondo i nostri dati, lo spread EUR/USD è di 1.1 pip); il conto Razor applica uno spread grezzo da 0.0 pip più una commissione round-turn di circa $6-7 per lotto, che varia in base alla piattaforma. Entrambi hanno una variante islamica/swap-free e l\'esecuzione degli ordini è senza dealing desk su entrambi: Standard opera in modalità simile a STP, Razor in modalità simile a ECN.',
                'faq_3_q' => 'Pepperstone applica una commissione per convertire la valuta al momento del deposito?',
                'faq_3_a' => 'Non da parte di Pepperstone, per quanto abbiamo potuto verificare. Tuttavia, la banca o il circuito della carta che gestisce il trasferimento può applicare un proprio markup di conversione se la valuta di finanziamento è diversa da quella base del conto, e non siamo riusciti a confermare in modo indipendente una percentuale specifica per questo costo intermedio.',
            ),
            'pt' => array(
                'title' => 'Taxas da Pepperstone explicadas',
                'meta_title' => 'Taxas da Pepperstone explicadas (2026): spreads, comissões, swap e custos ocultos',
                'meta_description' => 'Análise completa de todas as taxas que a Pepperstone pode cobrar -- spreads, comissão Razor, swap, taxa de inatividade e conversão cambial -- baseada no nosso próprio conjunto de dados de corretoras, pesquisado de forma independente, e não em alegações de marketing.',
                'intro' => 'A Pepperstone promove «spreads brutos a partir de 0.0 pips», mas isso é apenas uma linha na estrutura de custos que efetivamente determina o custo de negociar com a corretora. Este artigo detalha todas as taxas que a Pepperstone pode cobrar -- spread, comissão, financiamento overnight, inatividade, conversão cambial e depósitos/levantamentos -- com base nos mesmos dados pesquisados com que a avaliamos face às outras 138 corretoras do nosso conjunto, e não no enquadramento promocional da própria Pepperstone. Todos os valores abaixo referem-se à Pepperstone EU Limited, entidade regulada pela CySEC e pela FCA e analisada neste site; outra entidade Pepperstone noutra parte do mundo poderá definir condições diferentes.',
                'section_1_heading' => 'Spreads e comissões: Standard vs Razor',
                'section_1_body' => 'A entidade europeia da Pepperstone oferece três tipos de conta -- Standard, Razor e Professional (sujeita a elegibilidade, com maior alavancagem) -- além de uma opção islâmica/sem swap nas contas Standard e Razor. A conta Standard não cobra comissão separada: os spreads começam em cerca de 1.0 pip, e o spread EUR/USD de referência que registámos para esta conta é de 1.1 pips. A Razor cobra, em contrapartida, um spread bruto a partir de 0.0 pips, mais uma comissão ida e volta de cerca de $6-7 por lote, que varia consoante a plataforma utilizada. A execução de ordens é sem dealing desk nos dois níveis -- a Standard funciona em modelo tipo STP e a Razor em modelo tipo ECN -- e o acesso direto ao mercado (DMA) em CFDs está disponível em qualquer dos níveis através de cTrader e MT4/MT5. Qual das contas é efetivamente mais barata depende da dimensão e da frequência das suas operações: quem realiza poucas operações, de maior dimensão, paga frequentemente menos em spread puro na Standard do que em spread mais comissão na Razor, enquanto quem negocia com maior frequência ou volume, ou recorre a algoritmos, com o spread bruto da Razor, tende a sair beneficiado quando a comissão é descontada ao spread mais apertado. Não há substituto para fazer os seus próprios cálculos, com base na dimensão real da conta, no tamanho das operações e na frequência de negociação.',
                'section_2_heading' => 'Custos de financiamento overnight (swap)',
                'section_2_body' => 'Ao manter uma posição aberta durante a noite, a Pepperstone cobra uma taxa de swap baseada na taxa tom-next dos seus prestadores de liquidez, acrescida da sua própria taxa administrativa/de financiamento. A conta Standard não discrimina este custo como linha separada: em vez disso, a Pepperstone incorpora cerca de 1 pip de markup no spread, em vez de cobrar uma taxa overnight distinta. Se optar pela variante islâmica/sem swap de qualquer das contas, o swap diário desaparece, mas é aplicada uma taxa de administração reportada de $100 por lote por cada período de cinco dias em que a posição permaneça aberta -- vale a pena modelar com cuidado se pretender manter posições durante semanas em vez de dias, uma vez que esta taxa fixa não diminui proporcionalmente, como aconteceria com um swap noturno, numa posição mais pequena.',
                'section_3_heading' => 'Taxas não relacionadas à negociação: inatividade e conversão cambial',
                'section_3_body' => 'Várias fontes independentes que verificámos indicam que a Pepperstone não cobra qualquer taxa de inatividade, embora não tenhamos conseguido confirmar isso de forma independente diretamente no contrato de cliente da entidade europeia -- deve tratá-lo como bem corroborado, e não como confirmado contratualmente. Quanto à conversão cambial, a própria Pepperstone não parece cobrar taxa de depósito quando a moeda de financiamento difere da moeda base da conta, mas o banco remetente ou a rede do cartão podem aplicar a sua própria margem de conversão nessa transferência, e não conseguimos confirmar de forma independente uma percentagem específica para este custo intermédio.',
                'section_4_heading' => 'Taxas de depósito e levantamento',
                'section_4_body' => 'A Pepperstone não cobra taxas sobre depósitos ou levantamentos, exceto nos levantamentos por transferência bancária internacional, que têm uma taxa de cerca de $20 (ou £15). Não existe depósito mínimo por transferência bancária; os depósitos com cartão, Apple Pay e Google Pay têm um mínimo de cerca de £10. O prazo de processamento é tão importante como a própria taxa: os depósitos com cartão são creditados de imediato e as transferências bancárias demoram até dois dias úteis, enquanto os levantamentos variam entre menos de um dia útil (cartão, Apple Pay, Google Pay) e até dois dias úteis (transferência bancária nacional), podendo chegar a sete dias úteis (transferência bancária internacional).',
                'section_5_heading' => 'Como os custos da Pepperstone se comparam com as corretoras que analisámos',
                'section_5_body' => 'Aplicando o spread EUR/USD de 1.1 pips da Pepperstone à nossa fórmula de pontuação divulgada, obtém-se uma pontuação de custos de negociação de 3.57/5 -- 31.ª entre as 65 corretoras do nosso conjunto com spread EUR/USD confirmado, praticamente igual à mediana do conjunto, e não significativamente barata nem cara nessa medida isolada. Isto é relevante porque o depósito mínimo de $10 da Pepperstone fica abaixo do de 59 das 78 corretoras (76%) para as quais temos um depósito mínimo confirmado, e a sua pontuação global de 3.75/5 situa-se bastante acima do ponto médio do conjunto -- «acessível e bem regulada» e «spread mais baixo» são duas afirmações diferentes, e só a primeira se sustenta para a Pepperstone à luz dos números. Num aspeto, a Pepperstone é verdadeiramente rara: é uma das apenas 5 das 139 corretoras que analisámos com suporte confirmado para as quatro plataformas MT4, MT5, cTrader e TradingView -- uma amplitude de plataformas que é um diferenciador real, mesmo quando o spread principal não se destaca.',
                'faq_1_q' => 'A Pepperstone cobra uma taxa de inatividade?',
                'faq_1_a' => 'Várias fontes independentes que verificámos indicam que a Pepperstone não cobra taxa de inatividade, embora não tenhamos conseguido confirmar isso diretamente no contrato de cliente da entidade europeia.',
                'faq_2_q' => 'Qual é a diferença entre as contas Standard e Razor da Pepperstone?',
                'faq_2_a' => 'A Standard não cobra comissão separada, com spreads a partir de cerca de 1.0 pip (o nosso valor de referência para EUR/USD é de 1.1 pips); a Razor cobra um spread bruto a partir de 0.0 pips, mais uma comissão ida e volta de cerca de $6-7 por lote, que varia consoante a plataforma. Ambas têm uma variante islâmica/sem swap, e a execução de ordens é sem dealing desk em cada uma -- a Standard funciona em modelo tipo STP e a Razor em modelo tipo ECN.',
                'faq_3_q' => 'A Pepperstone cobra uma taxa para converter moeda no depósito?',
                'faq_3_a' => 'Não pela própria Pepperstone, tanto quanto conseguimos confirmar -- mas o banco ou a rede de cartões que processa a transferência pode aplicar a sua própria margem de conversão se a moeda de financiamento for diferente da moeda base da conta, e não conseguimos confirmar de forma independente uma percentagem específica para este custo intermédio.',
            ),
        ),
        'avatrade-fees' => array(
            'es' => array(
                'title' => 'Comisiones de AvaTrade explicadas',
                'meta_title' => 'Comisiones de AvaTrade explicadas (2026): spreads, swap y costos ocultos',
                'meta_description' => 'Un desglose completo de cada comisión que AvaTrade puede cobrar -- spread, financiación nocturna, inactividad y conversión de divisas -- basado en nuestro propio conjunto de datos de brókers investigado, y no en afirmaciones publicitarias.',
                'intro' => 'AvaTrade no se promociona por sus precios brutos como sí lo hace un bróker ECN basado en comisiones: su estructura de costos es más sencilla, pero menos transparente sobre el papel, ya que todo está integrado en un único spread de mesa de dealing en lugar de dividirse en líneas separadas de spread más comisión. Este análisis desglosa cada comisión que AvaTrade puede cobrar, utilizando los mismos datos investigados con los que lo puntuamos frente a los otros 138 brókers de nuestro conjunto de datos. Todas las cifras que aparecen a continuación corresponden a Ava Trade EU Ltd, la entidad analizada en este sitio; otra entidad de AvaTrade en otra parte del mundo puede establecer condiciones distintas.',
                'section_1_heading' => 'Spreads y el modelo de mesa de dealing',
                'section_1_body' => 'AvaTrade aplica un único modelo de precios en todos sus tipos de cuenta, en lugar de una división al estilo Standard/Razor: opera como mesa de dealing / creador de mercado, lo que significa que la propia AvaTrade es la contraparte de la operación y fija su propio spread, en lugar de trasladar un precio interbancario bruto más una comisión aparte, como hace un bróker ECN. El spread principal que tenemos registrado para EUR/USD es de 0.93 pips. Como el spread constituye todo el costo -- no hay una línea de comisión separada que sumar o restar --, lo que se muestra cotizado se acerca al costo total de la operación, a diferencia de una cuenta tipo Razor o Raw, donde el spread principal subestima el costo real hasta que se le suma la comisión.',
                'section_2_heading' => 'Costos de financiación nocturna (swap)',
                'section_2_body' => 'Las cuentas Standard aplican las tasas swap nocturnas habituales, calculadas como lo hace la mayoría de los brókers: un diferencial de tasas de referencia más el margen propio de AvaTrade, aunque el porcentaje exacto de ese margen no se confirmó en nuestra investigación. La cuenta islámica o libre de swap tiene una estructura inusual que conviene señalar con claridad: solo está realmente libre de swap durante los primeros 5 días de mantenimiento de una posición, tras los cuales se aplica en su lugar una comisión de administración, y no el estatus libre de swap indefinido que ofrecen las cuentas islámicas de otros brókers. Además, excluye de este tratamiento libre de swap ciertas divisas (RUB, MXN, TRY, ZAR) y las criptomonedas, y AvaTrade se reserva el derecho de revocar por completo el estatus libre de swap si sospecha un uso abusivo de esta función.',
                'section_3_heading' => 'Comisiones no asociadas al trading: inactividad y conversión de divisas',
                'section_3_body' => 'Ambas cuestiones están sin confirmar en nuestra investigación, y no vamos a adivinar cifras. No encontramos ninguna cifra de comisión por inactividad para AvaTrade en esta revisión; si existe, conviene confirmarla directamente con AvaTrade antes de dar por hecho que la cuenta está exenta. Con la conversión de divisas ocurre lo mismo: no se encontró ningún porcentaje específico ni tabla de comisiones, así que, si la divisa de fondeo difiere de la divisa base de la cuenta, conviene consultar directamente las condiciones actuales de AvaTrade, en lugar de asumir un costo concreto o que sea gratuita.',
                'section_4_heading' => 'Comisiones de depósito y retirada de fondos',
                'section_4_body' => 'No se confirmó ninguna comisión por parte de AvaTrade en depósitos ni en retiradas de fondos, aunque las comisiones de procesadores de pago externos o de bancos pueden seguir aplicándose según el método utilizado. El depósito mínimo de $100 se aplica por igual a las cuentas Retail e islámicas. El tiempo de procesamiento varía según el método: los depósitos con tarjeta son casi inmediatos, los de monedero electrónico se realizan en 24 horas y las transferencias bancarias tardan de 7 a 10 días hábiles; las retiradas de fondos con tarjeta o monedero electrónico se procesan en 24-48 horas una vez verificada la cuenta, pero las transferencias bancarias pueden tardar hasta 10 días hábiles.',
                'section_5_heading' => 'Cómo se sitúan los costos de AvaTrade frente a los brókers que hemos investigado',
                'section_5_body' => 'Aplicar el spread de 0.93 pips en EUR/USD de AvaTrade a nuestra fórmula de puntuación publicada da como resultado una puntuación de costos de trading de 3.79/5 -- en el puesto 23 de los 67 brókers de nuestro conjunto investigado con un spread EUR/USD confirmado --, lo que significa que es más barato que aproximadamente dos tercios del conjunto de datos en este único criterio. El depósito mínimo cuenta una historia distinta: los $100 de AvaTrade solo son más bajos que los de 24 de los 78 brókers (31%) para los que tenemos un depósito mínimo confirmado; en este criterio, empezar a operar resulta más caro que en la mayoría del conjunto de datos, no más barato. Donde AvaTrade sí es realmente poco común: admite 5 plataformas de trading (su propia AvaTradeGO, MT4, MT5, DupliTrade y ZuluTrade), más que cualquier otro bróker individual de nuestro conjunto de datos completo de 139 brókers. Ningún otro bróker que hayamos investigado iguala o supera ese número de plataformas.',
                'faq_1_q' => '¿AvaTrade cobra una comisión por inactividad?',
                'faq_1_a' => 'No encontramos ninguna cifra confirmada de comisión por inactividad para AvaTrade en esta revisión; conviene confirmarlo directamente con AvaTrade antes de asumir cualquiera de las dos opciones.',
                'faq_2_q' => '¿Qué modelo de ejecución de órdenes utiliza AvaTrade?',
                'faq_2_a' => 'Mesa de dealing / creador de mercado: AvaTrade es la contraparte de la operación y fija su propio spread, en lugar de operar como un bróker STP/ECN auténtico.',
                'faq_3_q' => '¿AvaTrade cobra una comisión por conversión de divisas al depositar?',
                'faq_3_a' => 'No confirmada de forma independiente: no se encontró ningún porcentaje específico ni tabla de comisiones en nuestra investigación, así que conviene consultar directamente las condiciones actuales de AvaTrade si la divisa de fondeo difiere de la divisa base de la cuenta.',
            ),
            'de' => array(
                'title' => 'AvaTrade Gebühren erklärt',
                'meta_title' => 'AvaTrade Gebühren erklärt (2026): Spreads, Swap und versteckte Kosten',
                'meta_description' => 'Eine vollständige Aufschlüsselung jeder Gebühr, die AvaTrade erheben kann -- Spread, Übernachtfinanzierung, Inaktivität und Währungsumrechnung -- basierend auf unserem eigenen recherchierten Broker-Datensatz und nicht auf Marketingaussagen.',
                'intro' => 'AvaTrade wirbt nicht mit reinen Preisen, wie es ein provisionsbasierter ECN-Broker tut. Die Kostenstruktur ist einfacher, auf dem Papier aber weniger transparent, da alles in einen einzigen Dealing-Desk-Spread eingerechnet ist und nicht in Spread plus separate Provisionspositionen aufgeteilt wird. Hier wird jede Gebühr erläutert, die AvaTrade erheben kann, auf Grundlage derselben recherchierten Daten, mit denen wir es gegenüber den anderen 138 Brokern in unserem Datensatz bewerten. Alle folgenden Angaben beziehen sich auf Ava Trade EU Ltd, das auf dieser Website geprüfte Unternehmen. Eine andere AvaTrade-Gesellschaft an anderen Standorten kann abweichende Bedingungen festlegen.',
                'section_1_heading' => 'Spreads und das Dealing-Desk-Modell',
                'section_1_body' => 'AvaTrade setzt bei seinen Kontotypen ein einheitliches Preismodell ein statt einer Aufteilung im Stil von Standard/Razor. Das Unternehmen arbeitet als Dealing Desk / Market Maker, d. h. AvaTrade selbst ist Gegenpartei Ihres Handels und legt den Spread eigenständig fest, anstatt einen rohen Interbankenkurs plus eine separate Provision weiterzugeben, wie es ein ECN-Broker tut. Unser hinterlegter Hauptwert für den EUR/USD-Spread beträgt 0,93 Pips. Da der Spread die gesamten Kosten darstellt -- es gibt keine separate Provisionsposition, die addiert oder abgezogen werden müsste --, entspricht der angezeigte Kurs nahezu den vollständigen Handelskosten. Anders als bei einem Razor- oder Raw-Konto, bei dem der angegebene Spread die tatsächlichen Kosten so lange unterschätzt, bis die Provision hinzugerechnet wird.',
                'section_2_heading' => 'Übernachtfinanzierung (Swap-Kosten)',
                'section_2_body' => 'Standardkonten werden mit den üblichen Overnight-Swap-Sätzen belastet, berechnet wie bei den meisten Brokern: eine Benchmark-Zinsdifferenz plus einen eigenen Aufschlag von AvaTrade. Der genaue Aufschlagsprozentsatz wurde in unserer Recherche jedoch nicht bestätigt. Das islamische/swapfreie Konto hat eine ungewöhnliche Struktur, auf die Sie unbedingt achten sollten: Es ist nur für die ersten 5 Tage, die eine Position gehalten wird, tatsächlich swapfrei. Danach fällt stattdessen eine Verwaltungsgebühr an. Es handelt sich also nicht um den unbefristeten swapfreien Status, den die islamischen Konten anderer Broker bieten. Außerdem sind bestimmte Währungen (RUB, MXN, TRY, ZAR) und Kryptowerte vollständig von der swapfreien Behandlung ausgenommen. AvaTrade behält sich zudem vor, den swapfreien Status vollständig zu widerrufen, falls der Verdacht eines Missbrauchs dieser Funktion besteht.',
                'section_3_heading' => 'Handelsunabhängige Gebühren: Inaktivität und Währungsumrechnung',
                'section_3_body' => 'Beide Punkte sind in unserer Recherche tatsächlich nicht bestätigt, und wir werden keine Zahlen erraten. In dieser Recherchephase haben wir für AvaTrade überhaupt keine Angabe zur Inaktivitätsgebühr gefunden. Falls es eine gibt, bestätigen Sie diese direkt bei AvaTrade, bevor Sie davon ausgehen, dass Ihr Konto befreit ist. Bei der Währungsumrechnung verhält es sich genauso: Es wurde kein konkreter Prozentsatz und kein Gebührenverzeichnis gefunden. Wenn Ihre Einzahlungswährung von der Basiswährung Ihres Kontos abweicht, prüfen Sie daher die aktuellen Bedingungen von AvaTrade direkt, anstatt von einer bestimmten Kostenhöhe oder davon auszugehen, dass die Umrechnung kostenlos ist.',
                'section_4_heading' => 'Gebühren für Ein- und Auszahlungen',
                'section_4_body' => 'Für Ein- und Auszahlungen wurde keine Gebühr auf Seiten von AvaTrade bestätigt, wobei je nach gewählter Methode dennoch Gebühren von Drittanbietern oder Banken anfallen können. Die Mindesteinzahlung von 100 $ gilt sowohl für Retail- als auch für islamische Konten. Die Bearbeitungszeit hängt von der Methode ab: Einzahlungen per Karte erfolgen nahezu sofort, per E-Wallet innerhalb von 24 Stunden und per Banküberweisung in 7-10 Geschäftstagen. Auszahlungen per Karte oder E-Wallet werden nach erfolgter Verifizierung innerhalb von 24-48 Stunden bearbeitet. Bei Banküberweisung können jedoch bis zu 10 Geschäftstage vergehen.',
                'section_5_heading' => 'Wie die Kosten von AvaTrade im Vergleich zu den von uns recherchierten Brokern abschneiden',
                'section_5_body' => 'Wenn der 0,93-Pip-EUR/USD-Spread von AvaTrade durch unsere offengelegte Bewertungsformel läuft, ergibt sich eine Bewertung der Handelskosten von 3,79/5. Das ist Platz 23 von den 67 Brokern in unserem recherchierten Datensatz mit bestätigtem EUR/USD-Spread. Das bedeutet, dass es bei diesem Kriterium günstiger ist als etwa zwei Drittel des Datensatzes. Bei der Mindesteinzahlung zeigt sich ein anderes Bild: Die 100 $ von AvaTrade unterbieten nur 24 der 78 Broker (31%), für die wir eine bestätigte Mindesteinzahlung haben. Bei diesem Kriterium ist der Einstieg bei AvaTrade also teurer als bei den meisten Brokern des Datensatzes, nicht günstiger. Eine echte Ausnahme bildet AvaTrade bei den Handelsplattformen: Es unterstützt 5 Handelsplattformen (die eigene AvaTradeGO, MT4, MT5, DupliTrade und ZuluTrade) -- mehr als jeder andere einzelne Broker in unserem gesamten Datensatz mit 139 Brokern. Kein anderer von uns recherchierter Broker erreicht diese Anzahl an Plattformen oder übertrifft sie.',
                'faq_1_q' => 'Erhebt AvaTrade eine Inaktivitätsgebühr?',
                'faq_1_a' => 'In dieser Recherchephase haben wir für AvaTrade keine bestätigte Angabe zur Inaktivitätsgebühr gefunden -- bestätigen Sie dies direkt bei AvaTrade, bevor Sie in keine der beiden Richtungen davon ausgehen.',
                'faq_2_q' => 'Welches Orderausführungsmodell verwendet AvaTrade?',
                'faq_2_a' => 'Dealing Desk / Market Maker -- AvaTrade ist Gegenpartei Ihres Handels und legt den Spread eigenständig fest, anstatt als echter STP-/ECN-Broker zu arbeiten.',
                'faq_3_q' => 'Erhebt AvaTrade eine Gebühr für die Währungsumrechnung bei Einzahlungen?',
                'faq_3_a' => 'Nicht unabhängig bestätigt -- in unserer Recherche wurde kein konkreter Prozentsatz und kein Gebührenverzeichnis gefunden. Prüfen Sie daher die aktuellen Bedingungen von AvaTrade direkt, falls Ihre Einzahlungswährung von der Basiswährung Ihres Kontos abweicht.',
            ),
            'ar' => array(
                'title' => 'رسوم AvaTrade موضحة',
                'meta_title' => 'رسوم AvaTrade موضحة (2026): الفرق السعري والتمويل الليلي والتكاليف الخفية',
                'meta_description' => 'تفصيل كامل لكل رسم قد تفرضه AvaTrade، ومنها الفرق السعري والتمويل الليلي وعدم النشاط وتحويل العملات، استنادًا إلى قاعدة بيانات الوسطاء التي أعددناها بأنفسنا لا إلى ادعاءات تسويقية.',
                'intro' => 'لا تروّج AvaTrade لأسعارها الخام كما يفعل الوسيط ECN القائم على العمولات، فبنية تكاليفها أبسط لكنها أقل شفافية على الورق، إذ تُدمج كل التكاليف في فرق سعري واحد يحدده مكتب التعامل بدلًا من تقسيمها إلى فرق سعري وبنود عمولة منفصلة. يفصّل هذا المقال كل رسم قد تفرضه AvaTrade مستندًا إلى البيانات البحثية نفسها التي نقيّم بها AvaTrade مقارنةً بالوسطاء الـ 138 الآخرين في قاعدة بياناتنا. كل الأرقام الواردة أدناه تخص Ava Trade EU Ltd، الكيان الذي تناولناه في هذا الموقع، وقد تفرض كيانات AvaTrade الأخرى حول العالم شروطًا مختلفة.',
                'section_1_heading' => 'الفروق السعرية ونموذج مكتب التعامل',
                'section_1_body' => 'تطبّق AvaTrade نموذج تسعير واحدًا على جميع أنواع حساباتها بدلًا من التقسيم على غرار Standard وRazor، إذ تعمل كمكتب تعامل وصانع سوق، أي أن AvaTrade نفسها هي الطرف المقابل لصفقتك وتحدد فرقها السعري بنفسها، بدلًا من تمرير سعر البنوك البينية الخام مع عمولة منفصلة كما يفعل الوسيط ECN. يبلغ الفرق السعري الرئيسي لزوج EUR/USD المسجل لدينا 0.93 pips. ولأن الفرق السعري هو التكلفة الكاملة، إذ لا توجد عمولة منفصلة تُضاف أو تُطرح، فإن السعر المعروض يقارب تكلفة التداول الكاملة، على عكس حسابات على غرار Razor أو Raw حيث يقلل الفرق السعري المعلن من التكلفة الحقيقية إلى أن تُضاف العمولة.',
                'section_2_heading' => 'تكاليف التمويل الليلي (السواب)',
                'section_2_body' => 'تُحتسب على الحسابات العادية أسعار السواب الليلية المعتادة، بالطريقة التي تتبعها معظم الوسطاء، أي فرق سعر مرجعي إضافة إلى هامش ربح خاص بـ AvaTrade، مع أن نسبة الهامش الدقيقة لم تُؤكَّد في بحثنا. أما الحساب الإسلامي أو الخالي من السواب فله بنية غير معتادة تستحق التنبيه إليها بوضوح: فهو خالٍ من السواب فعليًا لأول 5 أيام فقط من الاحتفاظ بالمركز، وبعدها تُطبّق رسوم إدارية بدلًا منه، وهذا يختلف عن الوضع الخالي من السواب بلا حدود الذي تقدمه حسابات إسلامية لدى وسطاء آخرين. كما تستثني الشركة عملات معينة (RUB وMXN وTRY وZAR) والعملات الرقمية من المعاملة الخالية من السواب بالكامل، وتحتفظ AvaTrade بحقها في إلغاء وضع الخلو من السواب تمامًا إذا اشتبهت في إساءة استخدام هذه الميزة.',
                'section_3_heading' => 'رسوم غير متعلقة بالتداول: عدم النشاط وتحويل العملات',
                'section_3_body' => 'هذان البندان غير مؤكدين في بحثنا، ولن نخمّن أرقامًا. لم نجد أي رقم لرسوم عدم النشاط لدى AvaTrade في جولة البحث هذه، وإن وُجد مثل هذا الرسم فتحقق منه مباشرةً لدى AvaTrade قبل أن تفترض أن حسابك معفى. ينطبق الأمر نفسه على تحويل العملات، إذ لم نعثر على نسبة محددة ولا جدول رسوم، لذا إذا اختلفت عملة التمويل لديك عن عملة الحساب الأساسية فراجع الشروط الحالية لدى AvaTrade مباشرةً، ولا تفترض تكلفة محددة أو أن الخدمة مجانية.',
                'section_4_heading' => 'رسوم الإيداع والسحب',
                'section_4_body' => 'لم تُؤكَّد أي رسوم من جانب AvaTrade على الإيداعات أو عمليات السحب، لكن قد تنطبق رسوم معالجة من أطراف ثالثة أو رسوم بنكية تبعًا لوسيلة الدفع لديك. ينطبق الحد الأدنى للإيداع البالغ 100$ على الحسابات العادية والإسلامية على حد سواء. وتختلف مدة المعالجة باختلاف الوسيلة: تتم الإيداعات فوريًا تقريبًا بالبطاقة، وخلال 24 ساعة عبر المحافظ الإلكترونية، وخلال 7 إلى 10 أيام عمل عبر التحويل البنكي، أما السحب فيُنفَّذ خلال 24 إلى 48 ساعة بالبطاقة أو المحفظة الإلكترونية بعد التحقق، لكنه قد يستغرق حتى 10 أيام عمل عبر التحويل البنكي.',
                'section_5_heading' => 'كيف تقارن تكاليف AvaTrade بالوسطاء الذين درسناهم',
                'section_5_body' => 'عند تطبيق الفرق السعري البالغ 0.93 pips لزوج EUR/USD لدى AvaTrade على معادلة التقييم المعلنة لدينا، يحصل الوسيط على تقييم تكاليف التداول البالغ 3.79/5، أي المرتبة 23 من أصل 67 وسيطًا في مجموعتنا التي لديها فرق سعري مؤكد لزوج EUR/USD، مما يعني أنه أرخص من نحو ثلثي قاعدة البيانات وفق هذا المعيار الواحد. أما الحد الأدنى للإيداع فيروي قصة مختلفة، إذ لا يتفوق مبلغ AvaTrade البالغ 100$ إلا على 24 وسيطًا من أصل 78 (31%) لدينا حد أدنى مؤكد للإيداع لديهم، وبهذا المعيار يكون الدخول إليها أغلى من معظم قاعدة البيانات لا أرخص. أما ما ينفرد به الوسيط حقًا فهو أنه يدعم 5 منصات تداول (منصته الخاصة AvaTradeGO وMT4 وMT5 وDupliTrade وZuluTrade)، أي أكثر من أي وسيط آخر في قاعدة بياناتنا الكاملة البالغة 139 وسيطًا. ولا يوجد وسيط آخر درسناه يساوي هذا العدد من المنصات أو يتجاوزه.',
                'faq_1_q' => 'هل تفرض AvaTrade رسوم عدم نشاط؟',
                'faq_1_a' => 'لم نجد أي رقم مؤكد لرسوم عدم النشاط لدى AvaTrade في جولة البحث هذه، لذا تحقق منها مباشرةً لدى AvaTrade قبل افتراض أي شيء في أي اتجاه.',
                'faq_2_q' => 'ما نموذج تنفيذ الصفقات الذي تعتمده AvaTrade؟',
                'faq_2_a' => 'مكتب التعامل وصانع السوق، إذ تكون AvaTrade الطرف المقابل لصفقتك وتحدد فرقها السعري بنفسها، ولا تعمل كوسيط STP أو ECN حقيقي.',
                'faq_3_q' => 'هل تفرض AvaTrade رسومًا لتحويل العملة عند الإيداع؟',
                'faq_3_a' => 'غير مؤكد بشكل مستقل، فلم نجد أي نسبة محددة أو جدول رسوم في بحثنا، لذا راجع الشروط الحالية لدى AvaTrade مباشرةً إذا اختلفت عملة التمويل لديك عن عملة حسابك الأساسية.',
            ),
            'fr' => array(
                'title' => 'Les frais d\'AvaTrade expliqués',
                'meta_title' => 'Les frais d\'AvaTrade expliqués (2026) : spreads, swap et coûts cachés',
                'meta_description' => 'Une analyse complète de chaque frais qu\'AvaTrade peut appliquer -- spread, financement de nuit, inactivité et conversion de devises -- fondée sur notre propre jeu de données de courtiers, et non sur des affirmations marketing.',
                'intro' => 'AvaTrade ne met pas en avant ses prix bruts comme le fait un courtier ECN à commissions. Sa structure de coûts est plus simple, mais moins transparente sur le papier, puisque tout est intégré dans un seul spread de dealing desk plutôt que réparti entre un spread et des lignes de commission distinctes. Cet article détaille chaque frais qu\'AvaTrade peut appliquer, en s\'appuyant sur les mêmes données de recherche que celles qui nous servent à le noter par rapport aux 138 autres courtiers de notre jeu de données. Tous les chiffres ci-dessous concernent Ava Trade EU Ltd, l\'entité analysée sur ce site ; une autre entité AvaTrade dans le monde peut appliquer des conditions différentes.',
                'section_1_heading' => 'Spreads et modèle de dealing desk',
                'section_1_body' => 'AvaTrade applique un modèle de tarification unique à l\'ensemble de ses types de comptes, sans la répartition de type Standard/Razor : il fonctionne comme un dealing desk / teneur de marché, ce qui signifie qu\'AvaTrade est lui-même la contrepartie de votre transaction et fixe son propre spread, au lieu de transmettre un prix interbancaire brut auquel s\'ajoute une commission distincte, comme le fait un courtier ECN. Le spread EUR/USD principal figurant dans notre dossier est de 0.93 pip. Comme le spread représente à lui seul la totalité du coût -- il n\'existe aucune ligne de commission distincte à ajouter ou à soustraire -- le spread affiché est proche du coût total de trading, contrairement à un compte de type Razor ou Raw, où le spread affiché sous-estime le coût réel jusqu\'à ce que la commission soit rajoutée.',
                'section_2_heading' => 'Frais de financement de nuit (swap)',
                'section_2_body' => 'Les comptes Standard sont soumis aux taux de swap de nuit habituels, calculés comme le font la plupart des courtiers : un différentiel de taux de référence auquel s\'ajoute la marge propre d\'AvaTrade, bien que le pourcentage exact de cette marge n\'ait pas été confirmé dans nos recherches. Le compte islamique / sans swap présente une structure inhabituelle qu\'il convient de signaler clairement : il n\'est réellement sans swap que pour les 5 premiers jours de détention d\'une position, après quoi des frais d\'administration s\'appliquent à la place, et non le statut sans swap illimité que proposent les comptes islamiques d\'autres courtiers. Il exclut également certaines devises (RUB, MXN, TRY, ZAR) ainsi que les cryptomonnaies du traitement sans swap, et AvaTrade se réserve le droit de révoquer entièrement ce statut s\'il soupçonne un usage abusif de cette fonctionnalité.',
                'section_3_heading' => 'Frais hors trading : inactivité et conversion de devises',
                'section_3_body' => 'Ces deux points n\'ont pas été confirmés par nos recherches, et nous ne voulons pas avancer de chiffres sans fondement. Nous n\'avons trouvé aucun montant de frais d\'inactivité pour AvaTrade lors de cette étude ; si un tel frais existe, vérifiez-le directement auprès d\'AvaTrade avant de considérer votre compte comme exonéré. Il en va de même pour la conversion de devises : aucun pourcentage précis ni grille tarifaire n\'a été trouvé. Si la devise de financement de votre compte diffère de sa devise de base, consultez directement les conditions actuelles d\'AvaTrade plutôt que de supposer un coût précis ou une gratuité.',
                'section_4_heading' => 'Frais de dépôt et de retrait',
                'section_4_body' => 'Aucun frais côté AvaTrade n\'a été confirmé pour les dépôts ou les retraits, même si des frais de prestataire de paiement ou de banque tiers peuvent s\'appliquer selon votre mode de paiement. Le dépôt minimum de $100 s\'applique de la même manière aux comptes Retail et islamiques. Les délais de traitement varient selon le mode : les dépôts sont quasi instantanés par carte, sous 24 heures par portefeuille électronique, et de 7-10 jours ouvrés par virement bancaire ; les retraits sont traités en 24-48 heures par carte ou portefeuille électronique une fois le compte vérifié, mais peuvent prendre jusqu\'à 10 jours ouvrés par virement.',
                'section_5_heading' => 'Le positionnement des coûts d\'AvaTrade par rapport aux courtiers que nous avons analysés',
                'section_5_body' => 'En appliquant le spread EUR/USD de 0.93 pip d\'AvaTrade à notre formule de notation publiée, on obtient une note de coût de trading de 3.79/5 -- 23e parmi les 67 courtiers de notre échantillon disposant d\'un spread EUR/USD confirmé, ce qui signifie qu\'il est moins cher qu\'environ deux tiers du jeu de données sur ce critère. Le dépôt minimum raconte une autre histoire : le dépôt minimum de $100 d\'AvaTrade n\'est inférieur à celui de que 24 des 78 courtiers (31 %) pour lesquels nous disposons d\'un dépôt minimum confirmé ; sur ce critère, ouvrir un compte chez AvaTrade revient plus cher que pour la plupart de l\'échantillon, et non moins cher. Là où AvaTrade est véritablement rare : il prend en charge 5 plateformes de trading (sa propre AvaTradeGO, MT4, MT5, DupliTrade et ZuluTrade), soit davantage que tout autre courtier de notre jeu de données complet de 139 courtiers. Aucun autre courtier analysé n\'atteint ou ne dépasse ce nombre de plateformes.',
                'faq_1_q' => 'AvaTrade facture-t-il des frais d\'inactivité ?',
                'faq_1_a' => 'Nous n\'avons trouvé aucun montant confirmé de frais d\'inactivité pour AvaTrade lors de cette étude -- vérifiez directement auprès d\'AvaTrade avant de conclure dans un sens ou dans l\'autre.',
                'faq_2_q' => 'Quel modèle d\'exécution AvaTrade utilise-t-il ?',
                'faq_2_a' => 'Dealing desk / teneur de marché -- AvaTrade est la contrepartie de votre transaction et fixe son propre spread, plutôt que de fonctionner comme un courtier STP/ECN véritable.',
                'faq_3_q' => 'AvaTrade facture-t-il des frais pour convertir une devise lors d\'un dépôt ?',
                'faq_3_a' => 'Non confirmé de manière indépendante -- aucun pourcentage précis ni grille tarifaire n\'a été trouvé dans nos recherches ; consultez directement les conditions actuelles d\'AvaTrade si la devise de financement diffère de la devise de base de votre compte.',
            ),
            'pl' => array(
                'title' => 'Opłaty AvaTrade wyjaśnione',
                'meta_title' => 'Opłaty AvaTrade wyjaśnione (2026): spread, swap i ukryte koszty',
                'meta_description' => 'Pełne zestawienie wszystkich opłat, jakie AvaTrade może pobierać -- spread, finansowanie overnight, nieaktywność i przewalutowanie -- oparte na naszym własnym, zbadanym zbiorze danych o brokerach, a nie na deklaracjach marketingowych.',
                'intro' => 'AvaTrade nie promuje się cenami surowymi tak jak broker ECN pobierający prowizję -- jego struktura kosztów jest prostsza, ale na papierze mniej przejrzysta, ponieważ wszystko jest wbudowane w jeden spread ustalany przez dealing desk, zamiast być podzielone na odrębne pozycje: spread i prowizja. Poniżej opisujemy każdą opłatę, jaką AvaTrade może pobierać, na podstawie tych samych zbadanych danych, według których oceniamy go względem pozostałych 138 brokerów w naszym zbiorze. Wszystkie podane tu wartości dotyczą Ava Trade EU Ltd, podmiotu recenzowanego na tej stronie; inny podmiot AvaTrade na świecie może stosować odmienne warunki.',
                'section_1_heading' => 'Spready i model dealing desk',
                'section_1_body' => 'AvaTrade stosuje jeden model wyceny dla wszystkich typów kont, zamiast podziału w stylu Standard/Razor: działa jako dealing desk / market maker, co oznacza, że to sam AvaTrade jest kontrahentem Twojej transakcji i ustala własny spread, zamiast przekazywać surową cenę międzybankową plus osobną prowizję, jak robi broker ECN. Nasz wskaźnikowy spread EUR/USD, zapisany w bazie danych, wynosi 0.93 pipa. Ponieważ spread stanowi cały koszt -- nie ma osobnej pozycji prowizji do dodania lub odjęcia -- kwotowana wartość jest zbliżona do pełnego kosztu handlu, w przeciwieństwie do kont typu Razor lub Raw, gdzie wskazany spread zaniża rzeczywisty koszt, dopóki nie doliczy się prowizji.',
                'section_2_heading' => 'Koszty finansowania overnight (swap)',
                'section_2_body' => 'Konta Standard są obciążane standardowymi stawkami swap overnight, obliczanymi w sposób typowy dla większości brokerów -- jako różnica stóp referencyjnych plus własna marża AvaTrade, choć dokładny procent marży nie został potwierdzony w naszych badaniach. Konto islamskie/bez swapu ma nietypową strukturę, na którą warto zwrócić szczególną uwagę: jest rzeczywiście wolne od swapu tylko przez pierwsze 5 dni utrzymywania pozycji, po czym zamiast tego nalicza się opłatę administracyjną -- w przeciwieństwie do nieograniczonego statusu wolnego od swapu, jaki oferują konta islamskie niektórych innych brokerów. Wyłącza ono także z traktowania bez swapu określone waluty (RUB, MXN, TRY, ZAR) oraz kryptowaluty, a AvaTrade zastrzega sobie prawo do całkowitego cofnięcia statusu bez swapu, jeśli podejrzewa nadużycie tej funkcji.',
                'section_3_heading' => 'Opłaty niezwiązane z handlem: nieaktywność i przewalutowanie',
                'section_3_body' => 'Obie te opłaty pozostają w naszych badaniach niepotwierdzone, dlatego nie zamierzamy zgadywać wartości. Nie znaleźliśmy na tym etapie badań żadnej kwoty opłaty za nieaktywność dla AvaTrade -- jeśli taka istnieje, potwierdź ją bezpośrednio u AvaTrade, zanim założysz, że Twoje konto jest zwolnione. Podobnie jest z przewalutowaniem: nie znaleziono konkretnego procentu ani cennika, więc jeśli waluta Twojego depozytu różni się od waluty bazowej konta, sprawdź aktualne warunki AvaTrade bezpośrednio, zamiast zakładać albo konkretny koszt, albo że jest on bezpłatny.',
                'section_4_heading' => 'Opłaty za wpłaty i wypłaty',
                'section_4_body' => 'Nie potwierdzono żadnej opłaty po stronie AvaTrade za wpłaty ani wypłaty, choć opłaty pośredników płatności lub banków mogą nadal obowiązywać, w zależności od wybranej metody. Minimalny depozyt wynoszący $100 obowiązuje zarówno na kontach Retail, jak i islamskich. Czas realizacji zależy od metody: wpłaty kartą są niemal natychmiastowe, w ciągu 24 godzin przez e-portfel, a 7-10 dni roboczych przy przelewie bankowym; wypłaty kartą lub e-portfelem są realizowane w ciągu 24-48 godzin po weryfikacji, ale przelew bankowy może zająć do 10 dni roboczych.',
                'section_5_heading' => 'Jak koszty AvaTrade wypadają na tle zbadanych brokerów',
                'section_5_body' => 'Po zastosowaniu naszego opublikowanego wzoru oceny do spreadu EUR/USD wynoszącego 0.93 pipa AvaTrade otrzymuje ocenę kosztów handlu 3.79/5 -- 23. miejsce spośród 67 brokerów w naszym zbiorze, dla których potwierdzono spread EUR/USD, co oznacza, że jest tańszy niż około dwie trzecie zbioru w tym jednym wskaźniku. Minimalny depozyt opowiada inną historię: minimalny depozyt AvaTrade wynoszący $100 jest niższy tylko od 24 z 78 brokerów (31%), dla których mamy potwierdzony minimalny depozyt -- pod tym względem start u AvaTrade jest droższy niż u większości zbioru, a nie tańszy. Tym, co naprawdę wyróżnia AvaTrade, jest to, że obsługuje 5 platform handlowych (własną AvaTradeGO, MT4, MT5, DupliTrade i ZuluTrade) -- więcej niż jakikolwiek inny pojedynczy broker w naszym pełnym zbiorze 139 brokerów. Żaden inny broker, którego badaliśmy, nie dorównuje tej liczbie platform ani jej nie przewyższa.',
                'faq_1_q' => 'Czy AvaTrade pobiera opłatę za nieaktywność?',
                'faq_1_a' => 'Nie znaleźliśmy potwierdzonej kwoty opłaty za nieaktywność dla AvaTrade na tym etapie badań -- potwierdź ją bezpośrednio u AvaTrade, zanim przyjmiesz którekolwiek z tych założeń.',
                'faq_2_q' => 'Jakiego modelu realizacji zleceń używa AvaTrade?',
                'faq_2_a' => 'Dealing desk / market maker -- AvaTrade jest kontrahentem Twojej transakcji i ustala własny spread, zamiast działać jako prawdziwy broker STP/ECN.',
                'faq_3_q' => 'Czy AvaTrade pobiera opłatę za przewalutowanie przy wpłacie?',
                'faq_3_a' => 'Nie potwierdzono tego niezależnie -- w naszych badaniach nie znaleziono konkretnego procentu ani cennika, dlatego jeśli waluta Twojego depozytu różni się od waluty bazowej konta, sprawdź aktualne warunki AvaTrade bezpośrednio.',
            ),
            'it' => array(
                'title' => 'Commissioni di AvaTrade spiegate',
                'meta_title' => 'Commissioni di AvaTrade spiegate (2026): spread, swap e costi nascosti',
                'meta_description' => 'Un\'analisi completa di ogni commissione che AvaTrade può applicare -- spread, finanziamento overnight, inattività e conversione valutaria -- basata sui dati raccolti in proprio dal nostro dataset di broker, non su affermazioni di marketing.',
                'intro' => 'AvaTrade non punta sul prezzo grezzo come fa un broker ECN basato su commissioni: la sua struttura dei costi è più semplice ma meno trasparente sulla carta, perché tutto è incluso in un unico spread da dealing desk anziché essere suddiviso in spread più commissione. Questo articolo illustra ogni commissione che AvaTrade può applicare, usando gli stessi dati di ricerca con cui la valutiamo rispetto agli altri 138 broker del nostro dataset. Tutti i valori riportati di seguito si riferiscono ad Ava Trade EU Ltd, l\'entità recensita su questo sito; un\'altra entità AvaTrade in un altro paese potrebbe applicare condizioni diverse.',
                'section_1_heading' => 'Spread e modello dealing desk',
                'section_1_body' => 'AvaTrade adotta un unico modello di prezzo per tutti i suoi tipi di conto, invece di una suddivisione in stile Standard/Razor: opera come dealing desk / market maker, il che significa che AvaTrade stessa è la controparte della tua operazione e fissa il proprio spread, anziché trasferire un prezzo interbancario grezzo più una commissione separata come fa un broker ECN. Lo spread EUR/USD principale che abbiamo registrato è 0.93 pips. Poiché lo spread rappresenta l\'intero costo -- non ci sono commissioni separate da aggiungere o sottrarre -- il valore quotato è vicino al costo di trading complessivo, a differenza di un conto in stile Razor o Raw, dove lo spread dichiarato sottostima il costo reale finché la commissione non viene aggiunta.',
                'section_2_heading' => 'Costi di finanziamento overnight (swap)',
                'section_2_body' => 'I conti Standard sono soggetti ai normali tassi swap overnight, calcolati come fa la maggior parte dei broker: un differenziale di tasso di riferimento più il markup di AvaTrade, anche se la percentuale esatta del markup non è stata confermata nella nostra ricerca. Il conto islamico/swap-free ha una struttura insolita che merita particolare attenzione: è effettivamente privo di swap solo per i primi 5 giorni in cui una posizione viene mantenuta, dopodiché si applica invece una commissione di amministrazione -- non lo status swap-free illimitato offerto dai conti islamici di altri broker. Esclude inoltre del tutto dal regime swap-free alcune valute (RUB, MXN, TRY, ZAR) e le criptovalute, e AvaTrade si riserva il diritto di revocare completamente lo status swap-free in caso di sospetto abuso della funzione.',
                'section_3_heading' => 'Commissioni non di trading: inattività e conversione valutaria',
                'section_3_body' => 'Entrambe queste voci non sono confermate nella nostra ricerca, e non intendiamo azzardare cifre. In questa fase di ricerca non abbiamo trovato alcun importo di commissione di inattività per AvaTrade -- se ne esiste una, verificala direttamente con AvaTrade prima di presumere che il tuo conto ne sia esente. Lo stesso vale per la conversione valutaria: non è stata trovata alcuna percentuale specifica né un listino delle commissioni, quindi se la valuta con cui depositi è diversa dalla valuta base del conto, controlla direttamente le condizioni attuali di AvaTrade, senza presumere né un costo specifico né che il servizio sia gratuito.',
                'section_4_heading' => 'Commissioni su depositi e prelievi',
                'section_4_body' => 'Non è stata confermata alcuna commissione a carico di AvaTrade su depositi o prelievi, anche se commissioni di processori terzi o della banca possono comunque applicarsi a seconda del metodo utilizzato. Il deposito minimo di 100 $ vale sia per i conti Retail sia per quelli islamici. I tempi di elaborazione variano in base al metodo: i depositi con carta sono quasi istantanei, quelli con e-wallet avvengono entro 24 ore, e quelli con bonifico bancario richiedono 7-10 giorni lavorativi; i prelievi con carta o e-wallet vengono elaborati in 24-48 ore una volta verificati, mentre con bonifico possono richiedere fino a 10 giorni lavorativi.',
                'section_5_heading' => 'Come si collocano i costi di AvaTrade rispetto ai broker che abbiamo analizzato',
                'section_5_body' => 'Applicando lo spread EUR/USD di 0.93 pips di AvaTrade alla nostra formula di punteggio dichiarata, si ottiene un punteggio dei costi di trading di 3.79/5 -- 23° su 67 broker del nostro campione con uno spread EUR/USD confermato, il che significa che è più economica di circa due terzi del dataset per questo parametro. Il deposito minimo racconta una storia diversa: i 100 $ di AvaTrade sono inferiori a quelli di solo 24 dei 78 broker (31%) per cui abbiamo un deposito minimo confermato -- per questo parametro iniziare a operare costa più della maggior parte del dataset, non meno. Un aspetto in cui AvaTrade è davvero rara: supporta 5 piattaforme di trading (la propria AvaTradeGO, MT4, MT5, DupliTrade e ZuluTrade) -- più di qualsiasi altro singolo broker nel nostro dataset completo di 139 broker. Nessun altro broker che abbiamo analizzato eguaglia o supera questo numero di piattaforme.',
                'faq_1_q' => 'AvaTrade applica una commissione di inattività?',
                'faq_1_a' => 'In questa fase di ricerca non abbiamo trovato alcun importo confermato di commissione di inattività per AvaTrade -- verifica direttamente con AvaTrade prima di presumere in un senso o nell\'altro.',
                'faq_2_q' => 'Quale modello di esecuzione degli ordini usa AvaTrade?',
                'faq_2_a' => 'Dealing desk / market maker -- AvaTrade è la controparte della tua operazione e fissa il proprio spread, anziché operare come vero broker STP/ECN.',
                'faq_3_q' => 'AvaTrade applica una commissione di conversione valutaria al momento del deposito?',
                'faq_3_a' => 'Non confermato in modo indipendente -- nella nostra ricerca non è stata trovata alcuna percentuale specifica né un listino delle commissioni, quindi verifica direttamente le condizioni attuali di AvaTrade se la valuta con cui depositi è diversa dalla valuta base del tuo conto.',
            ),
            'pt' => array(
                'title' => 'Taxas da AvaTrade explicadas',
                'meta_title' => 'Taxas da AvaTrade explicadas (2026): spreads, swap e custos ocultos',
                'meta_description' => 'Uma análise completa de todas as taxas que a AvaTrade pode cobrar -- spread, financiamento overnight, inatividade e conversão cambial -- baseada no nosso próprio conjunto de dados de corretoras analisado, e não em alegações de marketing.',
                'intro' => 'A AvaTrade não se promove com base em preços brutos, como faz uma corretora ECN baseada em comissões -- a sua estrutura de custos é mais simples, mas menos transparente no papel, uma vez que tudo está incluído num único spread da mesa de negociação, em vez de ser dividido em spread mais comissão. Este artigo detalha todas as taxas que a AvaTrade pode cobrar, usando os mesmos dados analisados com que a avaliamos face às outras 138 corretoras do nosso conjunto de dados. Todos os valores abaixo referem-se à Ava Trade EU Ltd, a entidade analisada neste site; outra entidade da AvaTrade noutro ponto do mundo poderá aplicar condições diferentes.',
                'section_1_heading' => 'Spreads e o modelo de mesa de negociação',
                'section_1_body' => 'A AvaTrade aplica um único modelo de preços em todos os seus tipos de conta, em vez de uma divisão ao estilo Standard/Razor: funciona como mesa de negociação (dealing desk) / market maker, o que significa que a própria AvaTrade é a contraparte da sua operação e define o seu próprio spread, em vez de transmitir um preço interbancário bruto acrescido de uma comissão separada, como faz uma corretora ECN. O spread principal que registámos para o EUR/USD é de 0.93 pips. Como o spread representa todo o custo -- não existe uma linha de comissão separada a somar ou subtrair --, o valor apresentado está próximo do custo total de negociação, ao contrário de uma conta do tipo Razor ou Raw, em que o spread anunciado subestima o custo real até se acrescentar a comissão.',
                'section_2_heading' => 'Custos de financiamento overnight (swap)',
                'section_2_body' => 'As contas Standard são cobradas com as taxas de swap overnight normais, calculadas da forma como a maioria das corretoras o faz -- um diferencial de taxas de referência mais a margem própria da AvaTrade, embora a percentagem exata dessa margem não tenha sido confirmada na nossa investigação. A conta islâmica/sem swap tem uma estrutura pouco habitual que merece destaque: é efetivamente sem swap apenas durante os primeiros 5 dias em que uma posição se mantém aberta, após os quais passa a aplicar-se, em sua substituição, uma taxa administrativa -- e não o estatuto de isenção de swap por tempo indeterminado que outras corretoras oferecem nas suas contas islâmicas. Também exclui do tratamento sem swap determinadas moedas (RUB, MXN, TRY, ZAR) e criptomoedas, e a AvaTrade reserva-se o direito de revogar por completo o estatuto sem swap caso suspeite de abuso desta funcionalidade.',
                'section_3_heading' => 'Taxas não relacionadas à negociação: inatividade e conversão cambial',
                'section_3_body' => 'Ambas estas taxas não foram confirmadas na nossa investigação, e não vamos arriscar números. Não encontrámos nesta análise qualquer valor de taxa de inatividade para a AvaTrade -- se existir, confirme-o diretamente com a AvaTrade antes de presumir que a sua conta está isenta. O mesmo se aplica à conversão cambial: não foi encontrada nenhuma percentagem específica nem tabela de taxas, pelo que, se a moeda de financiamento for diferente da moeda base da sua conta, consulte diretamente as condições atuais da AvaTrade, em vez de presumir um custo específico ou que o serviço é gratuito.',
                'section_4_heading' => 'Taxas de depósito e levantamento',
                'section_4_body' => 'Não foi confirmada qualquer taxa por parte da AvaTrade em depósitos ou levantamentos, embora possam ainda aplicar-se taxas de processadores terceiros ou bancos, consoante o método utilizado. O depósito mínimo de $100 aplica-se de igual forma às contas Retail e islâmicas. O tempo de processamento varia consoante o método: os depósitos são quase instantâneos com cartão, em até 24 horas por carteira eletrónica e em 7 a 10 dias úteis por transferência bancária; os levantamentos são processados em 24 a 48 horas com cartão ou carteira eletrónica, após verificação, mas podem demorar até 10 dias úteis por transferência bancária.',
                'section_5_heading' => 'Como os custos da AvaTrade se comparam com as corretoras que analisámos',
                'section_5_body' => 'Aplicando o spread de 0.93 pips no EUR/USD da AvaTrade à nossa fórmula de pontuação divulgada, obtém-se uma pontuação de custos de negociação de 3.79/5 -- 23.ª entre as 67 corretoras do nosso conjunto analisado com um spread EUR/USD confirmado, o que significa que é mais barata do que cerca de dois terços do conjunto de dados neste critério. O depósito mínimo conta uma história diferente: os $100 da AvaTrade só são inferiores aos de 24 das 78 corretoras (31%) para as quais temos um depósito mínimo confirmado -- neste critério, começar é mais caro do que na maioria do conjunto, e não mais barato. Aspeto em que a AvaTrade é verdadeiramente rara: suporta 5 plataformas de negociação (a sua própria AvaTradeGO, MT4, MT5, DupliTrade e ZuluTrade) -- mais do que qualquer outra corretora no nosso conjunto completo de 139. Nenhuma outra corretora analisada iguala ou supera esse número de plataformas.',
                'faq_1_q' => 'A AvaTrade cobra uma taxa de inatividade?',
                'faq_1_a' => 'Não encontrámos nesta análise qualquer valor confirmado de taxa de inatividade para a AvaTrade -- confirme diretamente com a AvaTrade antes de presumir uma ou outra situação.',
                'faq_2_q' => 'Que modelo de execução utiliza a AvaTrade?',
                'faq_2_a' => 'Mesa de negociação (dealing desk) / market maker -- a AvaTrade é a contraparte da sua operação e define o seu próprio spread, em vez de funcionar como uma verdadeira corretora STP/ECN.',
                'faq_3_q' => 'A AvaTrade cobra uma taxa para converter moeda no depósito?',
                'faq_3_a' => 'Não confirmada de forma independente -- não foi encontrada nenhuma percentagem específica nem tabela de taxas na nossa investigação, por isso consulte diretamente as condições atuais da AvaTrade se a moeda de financiamento for diferente da moeda base da sua conta.',
            ),
        ),
    );
}

/**
 * Resolves "{broker_slug}-{kind}" to its article plus the live broker
 * record, or null if either the article isn't drafted yet or the
 * broker slug it names doesn't resolve -- so a bad/future URL 404s
 * rather than rendering with missing data.
 */
function globalfxhub_get_broker_research_article( $broker_slug, $kind ) {
    $key = $broker_slug . '-' . $kind;
    $articles = globalfxhub_get_broker_research_articles();
    if ( ! isset( $articles[ $key ] ) || $articles[ $key ]['broker_slug'] !== $broker_slug || $articles[ $key ]['kind'] !== $kind ) {
        return null;
    }
    $broker = globalfxhub_get_broker_by_slug( $broker_slug );
    if ( ! $broker ) {
        return null;
    }
    $entry = $articles[ $key ];
    $entry['key'] = $key;
    $entry['broker'] = $broker;
    return $entry;
}

/**
 * Same overlay pattern as
 * globalfxhub_get_localized_editorial_comparison(): flattens this
 * language's translation map onto the English entry field by field,
 * falling back to English for any key the translation doesn't cover.
 */
function globalfxhub_get_localized_broker_research_article( $broker_slug, $kind ) {
    $entry = globalfxhub_get_broker_research_article( $broker_slug, $kind );
    if ( ! $entry ) {
        return null;
    }
    $lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
    $translations = globalfxhub_broker_research_translations();
    $flat = $translations[ $entry['key'] ][ $lang ] ?? array();
    if ( empty( $flat ) ) {
        return $entry;
    }
    $get = function( $id, $fallback ) use ( $flat ) {
        return $flat[ $id ] ?? $fallback;
    };

    $entry['title']            = $get( 'title', $entry['title'] );
    $entry['meta_title']       = $get( 'meta_title', $entry['meta_title'] );
    $entry['meta_description'] = $get( 'meta_description', $entry['meta_description'] );
    $entry['intro']            = $get( 'intro', $entry['intro'] );

    $entry['sections'] = array_values( array_map(
        function( $i, $section ) use ( $get ) {
            $n = $i + 1;
            return array(
                'heading' => $get( "section_{$n}_heading", $section['heading'] ),
                'body'    => $get( "section_{$n}_body", $section['body'] ),
            );
        },
        array_keys( $entry['sections'] ), $entry['sections']
    ) );

    $entry['faqs'] = array_values( array_map(
        function( $i, $faq ) use ( $get ) {
            $n = $i + 1;
            return array(
                'q' => $get( "faq_{$n}_q", $faq['q'] ),
                'a' => $get( "faq_{$n}_a", $faq['a'] ),
            );
        },
        array_keys( $entry['faqs'] ), $entry['faqs']
    ) );

    return $entry;
}

/**
 * All drafted research articles for a given broker (either kind) --
 * used for the review page's own contextual links, same idea as
 * globalfxhub_editorial_comparisons_for_broker().
 */
function globalfxhub_broker_research_articles_for_broker( $slug ) {
    $matches = array();
    foreach ( globalfxhub_get_broker_research_articles() as $key => $entry ) {
        if ( $entry['broker_slug'] === $slug ) {
            // The title must come from the SAME localized lookup the
            // article's own page uses (globalfxhub_get_localized_broker_research_article()),
            // not the raw English $entry['title'] -- otherwise this
            // link's visible text stays English forever while its href
            // (already language-aware via globalfxhub_track_b_url())
            // correctly lands on a translated article page, the same
            // bug class fixed earlier for Learn article cross-links.
            $localized = globalfxhub_get_localized_broker_research_article( $slug, $entry['kind'] );
            $matches[] = array(
                'kind'  => $entry['kind'],
                'title' => $localized ? $localized['title'] : $entry['title'],
                'url'   => globalfxhub_track_b_url( 'reviews/' . $slug . '/' . $entry['kind'] . '/' ),
            );
        }
    }
    return $matches;
}

function globalfxhub_research_seo_title( $title_parts ) {
    $slug = get_query_var( 'broker_research' );
    $kind = get_query_var( 'broker_research_kind' );
    if ( $slug && $kind ) {
        $entry = globalfxhub_get_localized_broker_research_article( $slug, $kind );
        if ( $entry ) {
            $title_parts['title'] = $entry['meta_title'];
        }
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_research_seo_title' );

function globalfxhub_research_canonical( $canonical_url ) {
    $slug = get_query_var( 'broker_research' );
    $kind = get_query_var( 'broker_research_kind' );
    if ( $slug && $kind && globalfxhub_get_broker_research_article( $slug, $kind ) ) {
        return globalfxhub_track_b_url( 'reviews/' . $slug . '/' . $kind . '/' );
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_research_canonical' );

function globalfxhub_research_seo_head() {
    $slug = get_query_var( 'broker_research' );
    $kind = get_query_var( 'broker_research_kind' );
    if ( ! $slug || ! $kind ) {
        return;
    }
    $entry = globalfxhub_get_localized_broker_research_article( $slug, $kind );
    if ( ! $entry ) {
        return;
    }
    echo '<meta name="description" content="' . esc_attr( $entry['meta_description'] ) . '">' . "\n";

    if ( ! empty( $entry['faqs'] ) ) {
        $faq_schema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_map( function( $faq ) {
                return array(
                    '@type'          => 'Question',
                    'name'           => $faq['q'],
                    'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $faq['a'] ),
                );
            }, $entry['faqs'] ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $faq_schema ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'globalfxhub_research_seo_head', 5 );
