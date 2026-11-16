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
            $matches[] = array(
                'kind'  => $entry['kind'],
                'title' => $entry['title'],
                'url'   => home_url( '/reviews/' . $slug . '/' . $entry['kind'] . '/' ),
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
        return home_url( '/reviews/' . $slug . '/' . $kind . '/' );
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
