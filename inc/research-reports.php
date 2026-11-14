<?php
/**
 * Original data reports at /research/{slug}/ -- the "original
 * research/data (unique, citable reports built from this site's own
 * broker dataset)" 15% content bucket from CLAUDE.md's 2026-11-09
 * content strategy, explicitly called out there as worth
 * disproportionate effort since these are the assets other sites
 * cite.
 *
 * New top-level section (like /learn/, /best/, /regulation/), not
 * nested under an existing one, because unlike this week's two prior
 * additions (/reviews/{slug}/fees/ reusing the review's own URL
 * space, /regulation/fca-vs-cysec/ reusing the regulator knowledge
 * base's URL space) a cross-broker statistical report isn't about any
 * one broker or regulator -- there's no existing page whose URL space
 * it belongs under. The primary nav's "Research" dropdown
 * (inc/i18n-nav-menu.php's globalfxhub_primary_nav_label_map(),
 * already translated into all 7 languages as 'nav_research') already
 * anticipates exactly this section; it had no link target until now.
 *
 * Every figure in this report is computed directly from the live
 * globalfxhub_get_brokers() dataset (see the verification harness run
 * before this content was written) and frozen into hand-written prose
 * at authoring time -- same precedent as every other hand-curated
 * editorial piece on this site. Re-verify and refresh the numbers
 * whenever broker platform data changes materially.
 *
 * Added one report at a time, same precedent as the broker/regulation
 * comparisons this week -- never a template mechanically generated
 * for every possible cross-section of the dataset.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_research_reports_rewrite_rules() {
    add_rewrite_rule( '^research/([^/]+)/?$', 'index.php?pagename=research&report=$matches[1]', 'top' );
}
add_action( 'init', 'globalfxhub_research_reports_rewrite_rules' );

function globalfxhub_research_reports_query_vars( $vars ) {
    $vars[] = 'report';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_research_reports_query_vars' );

function globalfxhub_get_research_reports() {
    return array(
        'forex-trading-platform-adoption-report' => array(
            'title'            => 'Forex Trading Platform Adoption Report',
            'meta_title'       => 'Forex Trading Platform Adoption Report (2026): MT4, MT5, cTrader and TradingView Across 139 Brokers',
            'meta_description' => 'How many forex brokers still support MT4, how many have moved to MT5, and how rare is cTrader or TradingView support -- an original data study across our full 139-broker research set.',
            'intro'            => "Which trading platforms a broker actually supports rarely gets the same scrutiny as its spread or its regulator -- so we pulled it directly from our own 139-broker research set and counted it. This report covers every confirmed platform across that full set: how common MT4 and MT5 still are, how many brokers run them side by side versus having moved on, and how rare cTrader and TradingView support actually is. Every figure below comes from the same platforms field used throughout this site's reviews, best-broker lists, and comparisons -- nothing here is a separate, newly-sourced claim.",
            'sections' => array(
                array(
                    'heading' => 'MT4 is still the most common platform, but MT5 has nearly caught up',
                    'body'    => "103 of the 139 brokers in our research set (74%) are confirmed to support MT4, and 97 (70%) are confirmed to support MT5. Despite MetaTrader 4 being the older of the two platforms, and despite MetaQuotes having stopped actively developing it in favor of MT5, it remains the more widely supported platform by a modest margin -- brokers have not abandoned MT4 at anywhere near the rate its \"legacy platform\" reputation might suggest.",
                ),
                array(
                    'heading' => 'Most brokers run MT4 and MT5 side by side -- few have actually dropped MT4',
                    'body'    => "84 of the 139 brokers support both MT4 and MT5 at once -- by far the most common setup. Only 13 of the 97 brokers that support MT5 (13%) have dropped MT4 entirely; the other 87% keep both running in parallel. 19 brokers still offer MT4 without having added MT5 at all, and 23 brokers (17% of the full set) offer neither platform, relying instead on cTrader, TradingView, or a proprietary platform of their own.",
                ),
                array(
                    'heading' => 'cTrader and TradingView remain minority platforms',
                    'body'    => "18 of the 139 brokers (13%) are confirmed to support cTrader, and 16 (12%) are confirmed to support TradingView -- both still a clear minority next to MT4/MT5, though far from negligible. The two overlap more than they compete: 7 brokers support both cTrader and TradingView, and only 1 of the 16 TradingView-supporting brokers does so without also offering MT4, meaning TradingView support is almost always additive to a broker's existing MetaTrader offering rather than a replacement for it.",
                ),
                array(
                    'heading' => 'TradingView support correlates strongly with our top-ranked brokers',
                    'body'    => "Splitting the dataset at our top 40 by overall rank shows a real gap: 30% of our top-40 brokers (12 of 40) support TradingView, against just 4% of the remaining 99 (4 brokers). MT4 shows a smaller version of the same pattern -- 83% of the top 40 support it, against 71% of the rest. This is a correlation, not a causal claim: supporting TradingView doesn't itself make a broker better-regulated or cheaper, and our ranking formula doesn't reward platform count directly (platforms is a flat function of how many platforms are confirmed, weighted at 10% of the overall score) -- but the two clearly tend to travel together in this dataset.",
                ),
                array(
                    'heading' => 'The five brokers confirmed to support all four major platforms',
                    'body'    => "Only 5 of the 139 brokers in our research set -- Pepperstone, FP Markets, GO Markets, Fusion Markets, and ICM Capital -- are confirmed to support all four of MT4, MT5, cTrader, and TradingView at once. That's a genuinely rare combination: offering every major third-party platform means supporting four separate pieces of trading infrastructure rather than concentrating on one or two, and all five of these brokers happen to also run an ECN/no-dealing-desk-style execution model, though our dataset doesn't let us say whether that's a cause, a correlation, or a coincidence at this sample size.",
                ),
                array(
                    'heading' => 'Proprietary platforms are common individually, but fragmented as a category',
                    'body'    => "Beyond the big four, 22 of the 139 brokers confirm exactly one platform, and 5 of those are MT4-only. The rest of the dataset is a long tail of broker-specific proprietary platforms -- each one typically confirmed for exactly one broker, with no single proprietary platform used by more than a handful of brokers -- so \"proprietary\" is common as a category but fragmented in practice, unlike the real cross-broker standardisation MT4, MT5, cTrader, and TradingView each represent. 12 of the 139 brokers have no platform confirmed in our research at all; we're treating that as an honest data gap rather than guessing, consistent with how every other unconfirmed field on this site is handled.",
                ),
            ),
            'faqs' => array(
                array(
                    'q' => 'Is MT4 being phased out by forex brokers?',
                    'a' => 'Not yet, at least not among the brokers we\'ve researched: 74% still support it, and 87% of the brokers that have added MT5 keep MT4 running alongside it rather than dropping it. Only 13% of MT5-supporting brokers in our dataset have moved on from MT4 entirely.',
                ),
                array(
                    'q' => 'Which forex brokers support MT4, MT5, cTrader, and TradingView?',
                    'a' => 'Five brokers in our 139-broker research set are confirmed to support all four: Pepperstone, FP Markets, GO Markets, Fusion Markets, and ICM Capital.',
                ),
                array(
                    'q' => 'Does TradingView support mean a broker is better?',
                    'a' => "Not directly -- our scoring formula doesn't reward platform count on its own. But there's a real correlation in our data: 30% of our top-40-ranked brokers support TradingView, against just 4% of the remaining 99, so platform breadth and overall ranking clearly tend to move together even though one doesn't mechanically cause the other.",
                ),
                array(
                    'q' => 'How was this data collected?',
                    'a' => "From the platforms field already used throughout this site's broker reviews, best-broker lists, and comparisons -- the same 139-broker research set, not a separate survey. Where we couldn't independently confirm a broker's platform support, we counted it as unconfirmed rather than assuming either way; 12 of the 139 brokers fall into that category.",
                ),
            ),
        ),
    );
}

/**
 * Per-language translations, same shape/purpose as the other two
 * hand-curated-content translation overlays added this week --
 * filled in once i18n/translate_research_reports.py's real
 * translate+review run completes for a given key.
 */
function globalfxhub_research_report_translations() {
    return array(
        'forex-trading-platform-adoption-report' => array(
            'es' => array(
                'title' => 'Informe sobre la adopción de plataformas de trading de forex',
                'meta_title' => 'Informe sobre la adopción de plataformas de trading de forex (2026): MT4, MT5, cTrader y TradingView en 139 brókers',
                'meta_description' => '¿Cuántos brókers de forex siguen ofreciendo MT4, cuántos han pasado a MT5 y qué tan poco frecuente es el soporte de cTrader o TradingView? Un estudio de datos original sobre nuestro conjunto completo de 139 brókers analizados.',
                'intro' => 'Las plataformas de trading que admite realmente un bróker rara vez reciben el mismo escrutinio que su spread o su regulador, así que las extrajimos directamente de nuestro propio conjunto de 139 brókers analizados y las contamos. Este informe cubre todas las plataformas confirmadas en ese conjunto completo: qué tan comunes siguen siendo MT4 y MT5, cuántos brókers las ofrecen en paralelo frente a los que ya las dejaron atrás y qué tan poco frecuente es realmente el soporte de cTrader y TradingView. Todas las cifras siguientes provienen del mismo campo de plataformas que se utiliza en los análisis, listas de mejores brókers y comparativas de este sitio: ninguna constituye una afirmación nueva ni procede de una fuente separada.',
                'section_1_heading' => 'MT4 sigue siendo la plataforma más común, pero MT5 casi la ha alcanzado',
                'section_1_body' => '103 de los 139 brókers de nuestro conjunto de análisis (74%) tienen confirmado el soporte de MT4, y 97 (70%) tienen confirmado el soporte de MT5. Aunque MetaTrader 4 sea la más antigua de las dos plataformas, y aunque MetaQuotes haya dejado de desarrollarla activamente para centrarse en MT5, sigue siendo la más ampliamente admitida, por un margen modesto: los brókers no han abandonado MT4 ni de lejos al ritmo que podría sugerir su reputación de «plataforma heredada».',
                'section_2_heading' => 'La mayoría de los brókers ejecutan MT4 y MT5 en paralelo; pocos han eliminado realmente MT4',
                'section_2_body' => '84 de los 139 brókers admiten MT4 y MT5 al mismo tiempo, con diferencia la configuración más habitual. Solo 13 de los 97 brókers que admiten MT5 (13%) han eliminado por completo MT4; el 87% restante mantiene ambas plataformas en funcionamiento paralelo. 19 brókers siguen ofreciendo MT4 sin haber añadido nunca MT5, y 23 brókers (17% del conjunto completo) no ofrecen ninguna de las dos, recurriendo a cTrader, TradingView o una plataforma propia.',
                'section_3_heading' => 'cTrader y TradingView siguen siendo plataformas minoritarias',
                'section_3_body' => '18 de los 139 brókers (13%) tienen confirmado el soporte de cTrader, y 16 (12%) tienen confirmado el soporte de TradingView; ambas siguen siendo una clara minoría frente a MT4/MT5, aunque están lejos de ser insignificantes. Se solapan más de lo que compiten: 7 brókers admiten tanto cTrader como TradingView, y solo 1 de los 16 brókers que admiten TradingView lo hace sin ofrecer también MT4. Lo que significa que el soporte de TradingView casi siempre se suma a la oferta de MetaTrader existente de un bróker, en lugar de sustituirla.',
                'section_4_heading' => 'El soporte de TradingView se correlaciona fuertemente con nuestros brókers mejor clasificados',
                'section_4_body' => 'Al dividir el conjunto de datos entre nuestros 40 brókers mejor clasificados según el ranking general, se observa una diferencia real: el 30% de nuestros 40 mejores brókers (12 de 40) admite TradingView, frente a solo el 4% de los 99 restantes (4 brókers). MT4 muestra una versión más pequeña del mismo patrón: el 83% de los 40 primeros lo admite, frente al 71% del resto. Se trata de una correlación, no de una afirmación causal: el soporte de TradingView no convierte por sí mismo a un bróker en uno mejor regulado o más barato, y nuestra fórmula de puntuación no premia directamente el número de plataformas (el campo de plataformas es una función sencilla de cuántas plataformas están confirmadas, con un peso del 10% en la puntuación general). Aun así, en este conjunto de datos, ambas variables tienden claramente a ir de la mano.',
                'section_5_heading' => 'Los cinco brókers con confirmación de soporte para las cuatro plataformas principales',
                'section_5_body' => 'Solo 5 de los 139 brókers de nuestro conjunto de análisis (Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital) tienen confirmado el soporte simultáneo de MT4, MT5, cTrader y TradingView. Es una combinación genuinamente poco frecuente: ofrecer todas las grandes plataformas de terceros significa dar soporte a cuatro infraestructuras de trading distintas, en lugar de concentrarse en una o dos. Además, los cinco brókers también utilizan un modelo de ejecución de tipo ECN/sin dealing desk, aunque nuestro conjunto de datos no nos permite determinar si eso es una causa, una correlación o una coincidencia con este tamaño de muestra.',
                'section_6_heading' => 'Las plataformas propias son comunes de forma individual, pero fragmentadas como categoría',
                'section_6_body' => 'Más allá de las cuatro grandes, 22 de los 139 brókers confirman exactamente una plataforma, y 5 de ellos solo ofrecen MT4. El resto del conjunto de datos es una larga cola de plataformas propias de cada bróker: cada una suele estar confirmada para un único bróker, sin que ninguna plataforma propia sea utilizada por más de un puñado de brókers. Por eso, «propia» es una categoría frecuente, pero fragmentada en la práctica, a diferencia de la verdadera estandarización entre brókers que representan MT4, MT5, cTrader y TradingView. 12 de los 139 brókers no tienen ninguna plataforma confirmada en nuestro análisis; tratamos esto como una laguna de datos honesta en lugar de adivinar, en línea con el modo en que se gestiona cualquier otro campo no confirmado de este sitio.',
                'faq_1_q' => '¿Están los brókers de forex dejando de ofrecer MT4?',
                'faq_1_a' => 'Todavía no, al menos entre los brókers que hemos analizado: el 74% sigue admitiéndola, y el 87% de los brókers que han añadido MT5 mantiene MT4 en funcionamiento junto a ella en lugar de eliminarla. Solo el 13% de los brókers de nuestro conjunto de datos que admiten MT5 han dejado MT4 por completo.',
                'faq_2_q' => '¿Qué brókers de forex admiten MT4, MT5, cTrader y TradingView?',
                'faq_2_a' => 'Cinco brókers de nuestro conjunto de 139 analizados tienen confirmado el soporte de las cuatro: Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital.',
                'faq_3_q' => '¿Significa el soporte de TradingView que un bróker es mejor?',
                'faq_3_a' => 'No directamente: nuestra fórmula de puntuación no premia por sí sola el número de plataformas. Pero en nuestros datos existe una correlación real: el 30% de nuestros 40 brókers mejor clasificados admite TradingView, frente a solo el 4% de los 99 restantes. Así, la variedad de plataformas y el ranking general claramente tienden a moverse juntos, aunque una cosa no cause mecánicamente la otra.',
                'faq_4_q' => '¿Cómo se recopilaron estos datos?',
                'faq_4_a' => 'A partir del campo de plataformas que ya se utiliza en los análisis de brókers, listas de mejores brókers y comparativas de este sitio: el mismo conjunto de 139 brókers analizados, no una encuesta independiente. Cuando no pudimos confirmar de forma independiente el soporte de plataformas de un bróker, lo contamos como no confirmado en lugar de suponer en ningún sentido; 12 de los 139 brókers entran en esa categoría.',
            ),
            'de' => array(
                'title' => 'Bericht zur Verbreitung von Forex-Handelsplattformen',
                'meta_title' => 'Bericht zur Verbreitung von Forex-Handelsplattformen (2026): MT4, MT5, cTrader und TradingView bei 139 Brokern',
                'meta_description' => 'Wie viele Forex-Broker noch MT4 unterstützen, wie viele auf MT5 umgestiegen sind und wie selten die Unterstützung von cTrader oder TradingView ist -- eine eigenständige Datenstudie über unser gesamtes Recherche-Set mit 139 Brokern.',
                'intro' => 'Welche Handelsplattformen ein Broker tatsächlich unterstützt, wird selten so genau geprüft wie sein Spread oder seine Regulierung -- deshalb haben wir diese Angaben direkt aus unserem eigenen Recherche-Set mit 139 Brokern erhoben und ausgezählt. Dieser Bericht erfasst jede bestätigte Plattform in diesem gesamten Set: wie verbreitet MT4 und MT5 noch sind, wie viele Broker beide parallel betreiben und wie viele bereits darauf verzichtet haben, und wie selten die Unterstützung von cTrader und TradingView tatsächlich ist. Alle folgenden Zahlen stammen aus demselben Feld zu Plattformen, das auf dieser Website in allen Testberichten, Listen der besten Broker und Vergleichen verwendet wird -- hier wird also nichts Neues oder separat Belegtes behauptet.',
                'section_1_heading' => 'MT4 ist nach wie vor die verbreitetste Plattform, doch MT5 hat fast aufgeholt',
                'section_1_body' => '103 der 139 Broker in unserem Recherche-Set (74%) unterstützen nachweislich MT4, und 97 (70%) unterstützen nachweislich MT5. Obwohl MetaTrader 4 die ältere der beiden Plattformen ist und MetaQuotes die Weiterentwicklung zugunsten von MT5 eingestellt hat, wird MT4 mit einem knappen Vorsprung weiterhin häufiger unterstützt -- die Broker haben MT4 bei Weitem nicht in dem Maße aufgegeben, wie es der Ruf als "veraltete Plattform" vermuten ließe.',
                'section_2_heading' => 'Die meisten Broker betreiben MT4 und MT5 parallel -- nur wenige haben MT4 tatsächlich abgeschafft',
                'section_2_body' => '84 der 139 Broker unterstützen MT4 und MT5 gleichzeitig -- mit Abstand die häufigste Konstellation. Nur 13 der 97 Broker, die MT5 unterstützen (13%), haben MT4 vollständig eingestellt; die übrigen 87% betreiben beide Plattformen parallel. 19 Broker bieten weiterhin MT4 an, ohne MT5 hinzugefügt zu haben, und 23 Broker (17% des gesamten Sets) bieten keine der beiden Plattformen an und setzen stattdessen auf cTrader, TradingView oder eine eigene proprietäre Plattform.',
                'section_3_heading' => 'cTrader und TradingView bleiben Minderheitenplattformen',
                'section_3_body' => '18 der 139 Broker (13%) unterstützen nachweislich cTrader, und 16 (12%) unterstützen nachweislich TradingView -- beide bleiben neben MT4/MT5 eindeutig in der Minderheit, sind aber alles andere als unbedeutend. Die beiden überschneiden sich stärker, als sie miteinander konkurrieren: 7 Broker unterstützen sowohl cTrader als auch TradingView, und nur 1 der 16 Broker mit TradingView-Unterstützung tut dies, ohne zugleich MT4 anzubieten. Die TradingView-Unterstützung ergänzt also fast immer das bestehende MetaTrader-Angebot eines Brokers, anstatt es zu ersetzen.',
                'section_4_heading' => 'Die Unterstützung von TradingView korreliert stark mit unseren bestplatzierten Brokern',
                'section_4_body' => 'Teilt man den Datensatz anhand unserer Top 40 nach Gesamtrang auf, zeigt sich eine deutliche Lücke: 30% unserer Top-40-Broker (12 von 40) unterstützen TradingView, gegenüber nur 4% der übrigen 99 (4 Broker). MT4 zeigt ein kleineres Abbild desselben Musters -- 83% der Top 40 unterstützen es, gegenüber 71% der übrigen. Dies ist eine Korrelation, keine kausale Aussage: Die Unterstützung von TradingView macht einen Broker nicht selbst besser reguliert oder günstiger, und unsere Bewertungsformel belohnt die Anzahl der Plattformen nicht direkt (das Kriterium Plattformen ist eine einfache Funktion der Anzahl bestätigter Plattformen und fließt mit 10% in die Gesamtbewertung ein) -- doch in diesem Datensatz tendieren beide Merkmale klar dazu, gemeinsam aufzutreten.',
                'section_5_heading' => 'Die fünf Broker, deren Unterstützung aller vier großen Plattformen bestätigt ist',
                'section_5_body' => 'Nur 5 der 139 Broker in unserem Recherche-Set -- Pepperstone, FP Markets, GO Markets, Fusion Markets und ICM Capital -- unterstützen nachweislich alle vier Plattformen MT4, MT5, cTrader und TradingView gleichzeitig. Das ist eine tatsächlich seltene Kombination: Jede große Drittanbieter-Plattform anzubieten bedeutet, vier getrennte Handelsinfrastrukturen zu unterstützen, statt sich auf ein oder zwei zu konzentrieren. Alle fünf dieser Broker betreiben zudem ein Ausführungsmodell im Stil eines ECN bzw. ohne Dealing Desk. Ob dies eine Ursache, eine Korrelation oder ein Zufall ist, lässt sich bei dieser Stichprobengröße mit unserem Datensatz jedoch nicht sagen.',
                'section_6_heading' => 'Proprietäre Plattformen sind einzeln verbreitet, als Kategorie jedoch zersplittert',
                'section_6_body' => 'Abseits der großen vier bestätigen 22 der 139 Broker genau eine Plattform, und 5 davon bieten ausschließlich MT4. Der Rest des Datensatzes besteht aus einer langen Reihe brokerspezifischer proprietärer Plattformen -- jede davon typischerweise nur bei einem einzigen Broker bestätigt, wobei keine einzelne proprietäre Plattform von mehr als einigen wenigen Brokern genutzt wird. "Proprietär" ist als Kategorie also weit verbreitet, in der Praxis aber zersplittert, anders als die echte brokerübergreifende Standardisierung, die MT4, MT5, cTrader und TradingView jeweils darstellen. 12 der 139 Broker haben in unserer Recherche gar keine Plattform bestätigt; wir werten dies als ehrliche Datenlücke statt zu raten, im Einklang damit, wie wir jedes andere unbestätigte Feld auf dieser Website behandeln.',
                'faq_1_q' => 'Wird MT4 von Forex-Brokern allmählich abgeschafft?',
                'faq_1_a' => 'Noch nicht, zumindest nicht bei den von uns recherchierten Brokern: 74% unterstützen es weiterhin, und 87% der Broker, die MT5 ergänzt haben, betreiben MT4 weiterhin parallel, anstatt es abzuschalten. Nur 13% der MT5-unterstützenden Broker in unserem Datensatz haben MT4 vollständig hinter sich gelassen.',
                'faq_2_q' => 'Welche Forex-Broker unterstützen MT4, MT5, cTrader und TradingView?',
                'faq_2_a' => 'Fünf Broker in unserem Recherche-Set mit 139 Brokern unterstützen nachweislich alle vier: Pepperstone, FP Markets, GO Markets, Fusion Markets und ICM Capital.',
                'faq_3_q' => 'Bedeutet die Unterstützung von TradingView, dass ein Broker besser ist?',
                'faq_3_a' => 'Nicht direkt -- unsere Bewertungsformel belohnt die Anzahl der Plattformen nicht für sich allein. Doch in unseren Daten gibt es eine echte Korrelation: 30% unserer Top-40-Broker unterstützen TradingView, gegenüber nur 4% der übrigen 99. Die Breite des Plattformangebots und der Gesamtrang tendieren also klar gemeinsam dazu, obwohl das eine das andere nicht mechanisch verursacht.',
                'faq_4_q' => 'Wie wurden diese Daten erhoben?',
                'faq_4_a' => 'Aus dem Feld zu Plattformen, das auf dieser Website in allen Testberichten, Listen der besten Broker und Vergleichen verwendet wird -- also aus demselben Recherche-Set mit 139 Brokern, nicht aus einer separaten Umfrage. Konnten wir die Plattformunterstützung eines Brokers nicht unabhängig bestätigen, haben wir ihn als unbestätigt gezählt, statt in die eine oder andere Richtung zu annehmen; 12 der 139 Broker fallen in diese Kategorie.',
            ),
            'ar' => array(
                'title' => 'تقرير اعتماد منصات تداول الفوركس',
                'meta_title' => 'تقرير اعتماد منصات تداول الفوركس (2026): MT4 وMT5 وcTrader وTradingView عبر 139 وسيطًا',
                'meta_description' => 'كم عدد وسطاء الفوركس الذين ما زالوا يدعمون MT4، وكم منهم انتقل إلى MT5، ومدى ندرة دعم cTrader أو TradingView -- دراسة بيانات أصلية تشمل مجموعة أبحاثنا الكاملة المكونة من 139 وسيطًا.',
                'intro' => 'نادرًا ما تحظى المنصات التي يدعمها الوسيط بالقدر نفسه من التدقيق الذي يحظى به الفرق السعري (سبريد) أو الجهة المنظمة له، لذلك استخرجنا هذه البيانات مباشرة من مجموعة أبحاثنا المكونة من 139 وسيطًا وأحصيناها. يغطي هذا التقرير كل منصة مؤكدة في المجموعة الكاملة: مدى انتشار MT4 وMT5 حتى الآن، وعدد الوسطاء الذين يشغّلونهما جنبًا إلى جنب مقابل من تخلّوا عنهما، ومدى ندرة دعم cTrader وTradingView فعليًا. وتأتي جميع الأرقام الواردة أدناه من حقل المنصات نفسه المستخدم في جميع مراجعات الموقع وقوائم أفضل الوسطاء والمقارنات، ولا يوجد هنا أي ادعاء جديد أو مصدر منفصل.',
                'section_1_heading' => 'يبقى MT4 المنصة الأكثر شيوعًا، لكن MT5 قارب اللحاق به',
                'section_1_body' => 'يُؤكَّد دعم 103 من أصل 139 وسيطًا في مجموعة أبحاثنا (74%) لمنصة MT4، ويُؤكَّد دعم 97 وسيطًا (70%) لمنصة MT5. وعلى الرغم من أن MetaTrader 4 هي الأقدم بين المنصتين، وعلى الرغم من توقف MetaQuotes عن التطوير النشط لها لصالح MT5، فإنها ما زالت الأوسع دعمًا بفارق طفيف، إذ لم يتخلَّ الوسطاء عن MT4 بالمعدل الذي قد يوحي به وصفها بـ«المنصة القديمة».',
                'section_2_heading' => 'يشغّل معظم الوسطاء MT4 وMT5 جنبًا إلى جنب، وقلة منهم تخلّت فعليًا عن MT4',
                'section_2_body' => 'يدعم 84 وسيطًا من أصل 139 المنصتين MT4 وMT5 معًا، وهو الترتيب الأكثر شيوعًا بفارق كبير. ولم يتخلَّ عن MT4 تمامًا سوى 13 وسيطًا من أصل 97 وسيطًا يدعمون MT5 (13%)، بينما يحتفظ الباقون بالمنصتين تعملان بالتوازي (87%). ويقدّم 19 وسيطًا منصة MT4 دون أن يضيفوا MT5 مطلقًا، ولا يقدّم 23 وسيطًا (17% من المجموعة الكاملة) أيًّا من المنصتين، بل يعتمدون بدلًا من ذلك على cTrader أو TradingView أو منصة خاصة بهم.',
                'section_3_heading' => 'تبقى cTrader وTradingView منصتين للأقلية من الوسطاء',
                'section_3_body' => 'يُؤكَّد دعم 18 وسيطًا من أصل 139 (13%) لمنصة cTrader، ويُؤكَّد دعم 16 وسيطًا (12%) لمنصة TradingView، وهما ما زالتا أقلية واضحة مقارنة بـ MT4/MT5، رغم أنهما ليستا هامشيتين. والمنصتان تتداخلان أكثر مما تتنافسان: يدعم 7 وسطاء المنصتين cTrader وTradingView معًا، ولا يقدّم سوى وسيط واحد من أصل 16 وسيطًا يدعمون TradingView، دون أن يقدّم MT4 أيضًا، ما يعني أن دعم TradingView يكاد يكون دائمًا إضافة إلى عرض MetaTrader القائم لدى الوسيط، لا بديلًا عنه.',
                'section_4_heading' => 'يرتبط دعم TradingView ارتباطًا قويًا بأفضل وسطائنا تصنيفًا',
                'section_4_body' => 'عند تقسيم مجموعة البيانات بين أفضل 40 وسيطًا وفق الترتيب العام وبقية المجموعة، يظهر فرق حقيقي: 30% من أفضل 40 وسيطًا لدينا (12 من 40) يدعمون TradingView، مقابل 4% فقط من الـ 99 المتبقين (4 وسطاء). ويظهر MT4 النمط نفسه بصورة أضعف، إذ يدعمه 83% من أفضل 40 مقابل 71% من الباقين. وهذا ارتباط وليس علاقة سببية: فدعم TradingView لا يجعل الوسيط أفضل تنظيمًا أو أرخص بذاته، كما أن معادلة الترتيب لدينا لا تكافئ عدد المنصات مباشرة (إذ يُحتسب حقل المنصات بوصفه دالة ثابتة لعدد المنصات المؤكدة، بوزن 10% من التقييم الإجمالي)، لكن الأمرين يميلان بوضوح إلى الظهور معًا في هذه البيانات.',
                'section_5_heading' => 'الوسطاء الخمسة المؤكد دعمهم للمنصات الأربع الرئيسية',
                'section_5_body' => 'من بين 139 وسيطًا في مجموعة أبحاثنا، لا يؤكد دعم جميع المنصات الأربع MT4 وMT5 وcTrader وTradingView معًا سوى 5 وسطاء، هم Pepperstone وFP Markets وGO Markets وFusion Markets وICM Capital. وهذه تركيبة نادرة فعلًا: فتقديم كل منصة رئيسية من أطراف ثالثة يعني دعم أربع بنى تحتية مستقلة للتداول بدلًا من التركيز على واحدة أو اثنتين، ويعمل جميع هؤلاء الوسطاء الخمسة أيضًا بنموذج تنفيذ من نوع ECN/بلا مكتب تعامل، غير أن بياناتنا لا تتيح لنا تحديد ما إذا كان ذلك سببًا أو ارتباطًا أو مصادفة عند حجم العينة الحالي.',
                'section_6_heading' => 'المنصات الخاصة شائعة بشكل فردي، لكنها مجزّأة كفئة',
                'section_6_body' => 'بعيدًا عن المنصات الأربع الكبرى، يؤكد 22 وسيطًا من أصل 139 منصة واحدة فقط، ومن بينهم 5 وسطاء يقدّمون MT4 فقط. أما بقية المجموعة فهي ذيل طويل من المنصات الخاصة بكل وسيط، إذ تُؤكَّد كل منصة منها عادةً لوسيط واحد، ولا تستخدم أي منصة خاصة أكثر من حفنة من الوسطاء، لذلك فإن «الخاصة» شائعة كفئة لكنها مجزّأة عمليًا، على عكس التوحيد الحقيقي بين الوسطاء الذي تمثله كل من MT4 وMT5 وcTrader وTradingView. ولا تتوفر معلومات عن المنصات لدى 12 وسيطًا من أصل 139 في أبحاثنا؛ ونعتبر ذلك فجوة بيانات صريحة بدلًا من التخمين، بما يتسق مع طريقة التعامل مع أي حقل غير مؤكد على هذا الموقع.',
                'faq_1_q' => 'هل يجري التخلص التدريجي من MT4 لدى وسطاء الفوركس؟',
                'faq_1_a' => 'ليس بعد، على الأقل بين الوسطاء الذين درسناهم: ما زال 74% منهم يدعمونها، ويحتفظ 87% من الوسطاء الذين أضافوا MT5 بـ MT4 تعمل إلى جانبها بدلًا من التخلي عنها. ولم يتخلَّ عن MT4 تمامًا سوى 13% من الوسطاء الداعمين لـ MT5 في مجموعة بياناتنا.',
                'faq_2_q' => 'أي وسطاء الفوركس يدعمون MT4 وMT5 وcTrader وTradingView؟',
                'faq_2_a' => 'يؤكد دعم جميع المنصات الأربع خمسة وسطاء في مجموعة أبحاثنا المكونة من 139 وسيطًا، وهم Pepperstone وFP Markets وGO Markets وFusion Markets وICM Capital.',
                'faq_3_q' => 'هل يعني دعم TradingView أن الوسيط أفضل؟',
                'faq_3_a' => 'ليس بشكل مباشر، فمعادلة التقييم لدينا لا تكافئ عدد المنصات بذاته. لكن هناك ارتباط حقيقي في بياناتنا: يدعم 30% من وسطائنا الـ 40 الأعلى تصنيفًا منصة TradingView، مقابل 4% فقط من الـ 99 المتبقين، لذلك يميل تنوع المنصات والترتيب العام بوضوح إلى التحرك معًا، رغم أن أحدهما لا يسبب الآخر بشكل آلي.',
                'faq_4_q' => 'كيف جُمعت هذه البيانات؟',
                'faq_4_a' => 'من حقل المنصات المستخدم أصلًا في جميع مراجعات وسطاء الموقع وقوائم أفضل الوسطاء والمقارنات، أي مجموعة الأبحاث نفسها المكونة من 139 وسيطًا، وليست استبيانًا منفصلًا. وحين تعذّر علينا التحقق بشكل مستقل من دعم منصة لدى وسيط ما، احتسبناه غير مؤكد بدلًا من افتراض أي شيء؛ ويقع 12 وسيطًا من أصل 139 ضمن هذه الفئة.',
            ),
            'fr' => array(
                'title' => 'Rapport sur l\'adoption des plateformes de trading forex',
                'meta_title' => 'Rapport sur l\'adoption des plateformes de trading forex (2026) : MT4, MT5, cTrader et TradingView chez 139 courtiers',
                'meta_description' => 'Combien de courtiers forex prennent encore en charge MT4, combien sont passés à MT5, et à quel point le support de cTrader ou de TradingView reste rare : une étude de données originale portant sur l\'ensemble de notre base de recherche de 139 courtiers.',
                'intro' => 'Les plateformes de trading qu\'un courtier prend réellement en charge font rarement l\'objet du même examen que son spread ou son régulateur. Nous avons donc extrait ces informations directement de notre propre base de recherche de 139 courtiers et les avons comptabilisées. Ce rapport couvre chaque plateforme confirmée dans l\'ensemble de cette base : la fréquence de MT4 et de MT5, le nombre de courtiers qui les exploitent en parallèle par rapport à ceux qui sont passés à autre chose, ainsi que la rareté réelle du support de cTrader et de TradingView. Tous les chiffres ci-dessous proviennent du même champ « plateformes » utilisé dans l\'ensemble des analyses, des listes de meilleurs courtiers et des comparatifs de ce site. Rien ici ne constitue une affirmation distincte ou nouvellement sourcée.',
                'section_1_heading' => 'MT4 reste la plateforme la plus répandue, mais MT5 a presque rattrapé son retard',
                'section_1_body' => '103 des 139 courtiers de notre base de recherche (74 %) sont confirmés comme prenant en charge MT4, et 97 (70 %) sont confirmés comme prenant en charge MT5. Bien que MetaTrader 4 soit la plus ancienne des deux plateformes, et bien que MetaQuotes ait cessé de la développer activement au profit de MT5, elle reste la plus largement prise en charge, avec une marge modeste. Les courtiers n\'ont pas abandonné MT4 au rythme que sa réputation de « plateforme héritée » pourrait laisser supposer.',
                'section_2_heading' => 'La plupart des courtiers exploitent MT4 et MT5 en parallèle, rares sont ceux qui ont réellement abandonné MT4',
                'section_2_body' => '84 des 139 courtiers prennent en charge MT4 et MT5 simultanément, ce qui constitue de loin la configuration la plus courante. Seuls 13 des 97 courtiers prenant en charge MT5 (13 %) ont entièrement abandonné MT4 ; les 87 % restants conservent les deux plateformes en parallèle. 19 courtiers proposent encore MT4 sans avoir ajouté MT5, et 23 courtiers (17 % de l\'ensemble) ne proposent aucune de ces deux plateformes, s\'appuyant plutôt sur cTrader, TradingView ou une plateforme propriétaire.',
                'section_3_heading' => 'cTrader et TradingView restent des plateformes minoritaires',
                'section_3_body' => '18 des 139 courtiers (13 %) sont confirmés comme prenant en charge cTrader, et 16 (12 %) sont confirmés comme prenant en charge TradingView. Ces deux plateformes restent nettement minoritaires face à MT4 et MT5, sans pour autant être négligeables. Elles se recoupent davantage qu\'elles ne se concurrencent : 7 courtiers prennent en charge à la fois cTrader et TradingView, et seul 1 des 16 courtiers proposant TradingView le fait sans proposer également MT4. Le support de TradingView vient donc presque toujours en complément de l\'offre MetaTrader existante d\'un courtier, et non en remplacement.',
                'section_4_heading' => 'Le support de TradingView est fortement corrélé à nos courtiers les mieux classés',
                'section_4_body' => 'En scindant la base selon nos 40 premiers courtiers au classement général, on observe un écart réel : 30 % de nos 40 meilleurs courtiers (12 sur 40) prennent en charge TradingView, contre seulement 4 % des 99 autres (4 courtiers). MT4 présente une version plus modeste du même schéma : 83 % des 40 premiers le prennent en charge, contre 71 % des autres. Il s\'agit d\'une corrélation et non d\'un lien de causalité : prendre en charge TradingView ne rend pas, en soi, un courtier mieux régulé ni moins cher, et notre formule de classement ne récompense pas directement le nombre de plateformes (le champ « plateformes » est une fonction simple du nombre de plateformes confirmées, pondérée à 10 % de la note globale). Ces deux éléments tendent néanmoins clairement à aller de pair dans cette base.',
                'section_5_heading' => 'Les cinq courtiers confirmés comme prenant en charge les quatre grandes plateformes',
                'section_5_body' => 'Seuls 5 des 139 courtiers de notre base de recherche (Pepperstone, FP Markets, GO Markets, Fusion Markets et ICM Capital) sont confirmés comme prenant en charge simultanément les quatre plateformes MT4, MT5, cTrader et TradingView. Il s\'agit d\'une combinaison véritablement rare : proposer chaque grande plateforme tierce implique de faire fonctionner quatre infrastructures de trading distinctes plutôt que de se concentrer sur une ou deux. Par ailleurs, ces cinq courtiers exploitent tous un modèle d\'exécution de type ECN/sans dealing desk. Notre base ne permet toutefois pas de dire s\'il s\'agit d\'une cause, d\'une corrélation ou d\'une coïncidence, compte tenu de la taille de l\'échantillon.',
                'section_6_heading' => 'Les plateformes propriétaires sont courantes individuellement, mais fragmentées en tant que catégorie',
                'section_6_body' => 'Au-delà des quatre grandes, 22 des 139 courtiers ne confirment qu\'une seule plateforme, et 5 d\'entre eux ne proposent que MT4. Le reste de la base se compose d\'une longue traîne de plateformes propriétaires propres à chaque courtier, chacune n\'étant généralement confirmée que pour un seul courtier, aucune plateforme propriétaire n\'étant utilisée par plus d\'une poignée de courtiers. Le terme « propriétaire » désigne donc une catégorie courante, mais fragmentée dans les faits, contrairement à la véritable standardisation inter-courtiers que représentent MT4, MT5, cTrader et TradingView. 12 des 139 courtiers n\'ont aucune plateforme confirmée dans notre recherche. Nous considérons cela comme une lacune honnête des données plutôt que de deviner, conformément à la manière dont tout autre champ non confirmé est traité sur ce site.',
                'faq_1_q' => 'Les courtiers forex abandonnent-ils progressivement MT4 ?',
                'faq_1_a' => 'Pas encore, du moins parmi les courtiers que nous avons étudiés : 74 % prennent encore en charge MT4, et 87 % des courtiers ayant ajouté MT5 conservent MT4 en parallèle plutôt que de l\'abandonner. Seuls 13 % des courtiers prenant en charge MT5 dans notre base ont entièrement abandonné MT4.',
                'faq_2_q' => 'Quels courtiers forex prennent en charge MT4, MT5, cTrader et TradingView ?',
                'faq_2_a' => 'Cinq courtiers de notre base de 139 courtiers sont confirmés comme prenant en charge les quatre : Pepperstone, FP Markets, GO Markets, Fusion Markets et ICM Capital.',
                'faq_3_q' => 'Le support de TradingView signifie-t-il qu\'un courtier est meilleur ?',
                'faq_3_a' => 'Pas directement : notre formule de notation ne récompense pas le nombre de plateformes en soi. Mais nos données révèlent une corrélation réelle : 30 % de nos courtiers les mieux classés (top 40) prennent en charge TradingView, contre seulement 4 % des 99 autres. La diversité des plateformes et le classement général tendent donc clairement à aller de pair, même si l\'un ne cause pas mécaniquement l\'autre.',
                'faq_4_q' => 'Comment ces données ont-elles été collectées ?',
                'faq_4_a' => 'À partir du champ « plateformes » déjà utilisé dans l\'ensemble des analyses de courtiers, des listes de meilleurs courtiers et des comparatifs de ce site, sur la même base de recherche de 139 courtiers, et non à partir d\'une enquête distincte. Lorsque nous n\'avons pas pu confirmer de manière indépendante la prise en charge de plateformes par un courtier, nous l\'avons comptabilisé comme non confirmé plutôt que de supposer dans un sens ou dans l\'autre. 12 des 139 courtiers entrent dans cette catégorie.',
            ),
            'pl' => array(
                'title' => 'Raport o popularności platform handlowych w forex',
                'meta_title' => 'Raport o popularności platform handlowych w forex (2026): MT4, MT5, cTrader i TradingView u 139 brokerów',
                'meta_description' => 'Ilu brokerów forex nadal obsługuje MT4, ilu przeszło na MT5, a jak rzadkie jest wsparcie dla cTrader lub TradingView -- oryginalne badanie danych obejmujące pełny zbiór 139 brokerów.',
                'intro' => 'To, jakie platformy handlowe faktycznie obsługuje dany broker, rzadko podlega takiej samej kontroli jak jego spread czy regulator -- dlatego zebraliśmy te dane bezpośrednio z naszego własnego zbioru 139 brokerów i je policzyliśmy. Raport obejmuje wszystkie potwierdzone platformy w całym tym zbiorze: jak powszechne są nadal MT4 i MT5, ilu brokerów oferuje je równolegle, a ilu już z nich zrezygnowało, oraz jak rzadkie jest faktycznie wsparcie dla cTrader i TradingView. Wszystkie dane poniżej pochodzą z tego samego pola platforms, którego używamy w recenzjach, zestawieniach najlepszych brokerów i porównaniach na tej stronie -- nie stanowią odrębnych, nowo pozyskanych twierdzeń.',
                'section_1_heading' => 'MT4 nadal jest najpopularniejszą platformą, ale MT5 niemal ją dogonił',
                'section_1_body' => '103 spośród 139 brokerów w naszym zbiorze badawczym (74%) ma potwierdzone wsparcie dla MT4, a 97 (70%) -- dla MT5. Mimo że MetaTrader 4 jest starszą z tych dwóch platform i mimo że firma MetaQuotes przestała aktywnie rozwijać ją na rzecz MT5, wciąż jest ona szerzej obsługiwana, choć jedynie niewielką przewagą -- brokerzy nie porzucili MT4 w tempie, jakiego mogłaby sugerować jej reputacja „platformy przestarzałej”.',
                'section_2_heading' => 'Większość brokerów oferuje MT4 i MT5 równolegle -- niewielu całkowicie zrezygnowało z MT4',
                'section_2_body' => '84 spośród 139 brokerów obsługuje jednocześnie MT4 i MT5 -- zdecydowanie najczęstszy układ. Tylko 13 z 97 brokerów wspierających MT5 (13%) całkowicie zrezygnowało z MT4; pozostałe 87% utrzymuje obie platformy równolegle. 19 brokerów nadal oferuje MT4 bez dodania MT5, a 23 brokerów (17% całego zbioru) nie oferuje żadnej z tych platform, polegając zamiast tego na cTrader, TradingView lub własnej, zastrzeżonej platformie.',
                'section_3_heading' => 'cTrader i TradingView pozostają platformami mniejszościowymi',
                'section_3_body' => '18 spośród 139 brokerów (13%) ma potwierdzone wsparcie dla cTrader, a 16 (12%) -- dla TradingView. Obie platformy wciąż stanowią wyraźną mniejszość obok MT4/MT5, choć nie są znikome. Częściej się uzupełniają, niż konkurują: 7 brokerów obsługuje zarówno cTrader, jak i TradingView, a tylko 1 spośród 16 brokerów wspierających TradingView robi to bez oferowania MT4. Oznacza to, że wsparcie dla TradingView niemal zawsze stanowi dodatek do istniejącej oferty MetaTrader, a nie jej zamiennik.',
                'section_4_heading' => 'Wsparcie dla TradingView silnie koreluje z naszymi najwyżej ocenianymi brokerami',
                'section_4_body' => 'Podział zbioru na 40 najwyżej sklasyfikowanych brokerów według ogólnego rankingu pokazuje wyraźną różnicę: 30% z naszej czołowej czterdziestki (12 z 40) wspiera TradingView, wobec zaledwie 4% pozostałych 99 brokerów (4 brokerów). MT4 wykazuje mniejszą wersję tego samego wzorca -- 83% czołowej czterdziestki je obsługuje, wobec 71% pozostałych. Jest to korelacja, a nie twierdzenie o związku przyczynowym: wsparcie dla TradingView nie czyni samo w sobie brokera lepiej regulowanym ani tańszym, a nasza formuła rankingowa nie nagradza bezpośrednio liczby platform (platforms to funkcja liczby potwierdzonych platform, z wagą 10% ogólnej oceny) -- jednak w tym zbiorze obie cechy wyraźnie mają tendencję do współwystępowania.',
                'section_5_heading' => 'Pięciu brokerów, u których potwierdzono wsparcie dla wszystkich czterech głównych platform',
                'section_5_body' => 'Tylko 5 spośród 139 brokerów w naszym zbiorze badawczym -- Pepperstone, FP Markets, GO Markets, Fusion Markets i ICM Capital -- ma potwierdzone wsparcie dla wszystkich czterech platform: MT4, MT5, cTrader i TradingView jednocześnie. To naprawdę rzadkie połączenie: oferowanie każdej z głównych zewnętrznych platform oznacza obsługę czterech odrębnych rozwiązań infrastrukturalnych zamiast koncentracji na jednej lub dwóch. Wszyscy pięciu ci brokerzy działają też w modelu realizacji zleceń w stylu ECN/no-dealing-desk, jednak nasz zbiór nie pozwala stwierdzić, czy jest to przyczyna, korelacja, czy przypadek przy tak małej próbie.',
                'section_6_heading' => 'Platformy zastrzeżone są powszechne pojedynczo, ale w skali kategorii rozdrobnione',
                'section_6_body' => 'Poza czterema głównymi platformami 22 spośród 139 brokerów potwierdza dokładnie jedną platformę, a 5 z nich to wyłącznie MT4. Reszta zbioru to długi ogon zastrzeżonych platform poszczególnych brokerów -- każda zazwyczaj potwierdzona dla jednego brokera, a żadna zastrzeżona platforma nie jest używana przez więcej niż kilku brokerów. „Zastrzeżona” jest więc w tym sensie kategorią powszechną, ale w praktyce rozdrobnioną, w przeciwieństwie do rzeczywistej standaryzacji między brokerami, jaką reprezentują MT4, MT5, cTrader i TradingView. 12 spośród 139 brokerów nie ma w naszych badaniach potwierdzonej żadnej platformy; traktujemy to jako uczciwą lukę w danych, zamiast zgadywać, zgodnie z tym, jak obsługujemy każde inne niepotwierdzone pole na tej stronie.',
                'faq_1_q' => 'Czy brokerzy forex wycofują MT4?',
                'faq_1_a' => 'Jeszcze nie, przynajmniej nie wśród brokerów, których przebadaliśmy: 74% nadal ją obsługuje, a 87% brokerów, którzy dodali MT5, utrzymuje MT4 obok niej, zamiast z niej rezygnować. Tylko 13% brokerów wspierających MT5 w naszym zbiorze całkowicie porzuciło MT4.',
                'faq_2_q' => 'Którzy brokerzy forex obsługują MT4, MT5, cTrader i TradingView?',
                'faq_2_a' => 'Pięciu z 139 brokerów w naszym zbiorze badawczym ma potwierdzone wsparcie dla wszystkich czterech platform: Pepperstone, FP Markets, GO Markets, Fusion Markets i ICM Capital.',
                'faq_3_q' => 'Czy wsparcie dla TradingView oznacza, że broker jest lepszy?',
                'faq_3_a' => 'Nie bezpośrednio -- nasza formuła oceny nie nagradza samej liczby platform. Jest jednak wyraźna korelacja w naszych danych: 30% brokerów z czołowej czterdziestki w naszym rankingu wspiera TradingView, wobec zaledwie 4% pozostałych 99, więc szeroki wybór platform i ogólny ranking wyraźnie idą w parze, choć jedno nie powoduje mechanicznie drugiego.',
                'faq_4_q' => 'Jak zebrano te dane?',
                'faq_4_a' => 'Z pola platforms, które już wykorzystujemy w recenzjach brokerów, zestawieniach najlepszych brokerów i porównaniach na tej stronie -- ten sam zbiór 139 brokerów, a nie odrębne badanie ankietowe. Gdy nie mogliśmy niezależnie potwierdzić wsparcia platform przez brokera, traktowaliśmy je jako niepotwierdzone, zamiast zakładać cokolwiek; 12 spośród 139 brokerów należy do tej kategorii.',
            ),
            'it' => array(
                'title' => 'Report sull\'adozione delle piattaforme di trading forex',
                'meta_title' => 'Report sull\'adozione delle piattaforme di trading forex (2026): MT4, MT5, cTrader e TradingView su 139 broker',
                'meta_description' => 'Quanti broker forex supportano ancora MT4, quanti sono passati a MT5 e quanto è raro il supporto a cTrader o TradingView -- uno studio di dati originale sull\'intero campione di ricerca di 139 broker.',
                'intro' => 'Quali piattaforme di trading supporta effettivamente un broker raramente riceve la stessa attenzione dello spread o del regolatore -- così le abbiamo estratte direttamente dal nostro campione di ricerca di 139 broker e le abbiamo contate. Questo report copre ogni piattaforma confermata nell\'intero campione: quanto sono ancora diffuse MT4 e MT5, quanti broker le gestiscono in parallelo e quanti invece hanno voltato pagina, e quanto sia davvero raro il supporto a cTrader e TradingView. Ogni dato qui riportato proviene dallo stesso campo delle piattaforme usato in tutte le recensioni, nelle liste dei migliori broker e nei confronti di questo sito -- non si tratta di un\'affermazione separata né di dati di nuova raccolta.',
                'section_1_heading' => 'MT4 è ancora la piattaforma più diffusa, ma MT5 è quasi al pari',
                'section_1_body' => '103 dei 139 broker del nostro campione di ricerca (74%) risultano confermati come supportati per MT4, e 97 (70%) per MT5. Nonostante MetaTrader 4 sia la più vecchia delle due piattaforme, e nonostante MetaQuotes abbia smesso di svilupparla attivamente a favore di MT5, resta la piattaforma più supportata con un margine contenuto -- i broker non hanno abbandonato MT4 nemmeno lontanamente al ritmo che la sua reputazione di «piattaforma legacy» potrebbe far pensare.',
                'section_2_heading' => 'La maggior parte dei broker gestisce MT4 e MT5 in parallelo -- pochi hanno davvero abbandonato MT4',
                'section_2_body' => '84 dei 139 broker supportano contemporaneamente MT4 e MT5 -- di gran lunga la configurazione più comune. Solo 13 dei 97 broker che supportano MT5 (13%) hanno eliminato del tutto MT4; gli altri, l\'87%, le mantengono entrambe in funzione in parallelo. 19 broker offrono ancora MT4 senza aver mai aggiunto MT5, e 23 broker (17% dell\'intero campione) non offrono nessuna delle due, affidandosi invece a cTrader, TradingView o a una piattaforma proprietaria.',
                'section_3_heading' => 'cTrader e TradingView restano piattaforme minoritarie',
                'section_3_body' => '18 dei 139 broker (13%) risultano confermati come supportati per cTrader, e 16 (12%) per TradingView -- entrambe ancora nettamente minoritarie rispetto a MT4/MT5, sebbene tutt\'altro che trascurabili. Le due si sovrappongono più di quanto competano: 7 broker supportano sia cTrader sia TradingView, e solo 1 dei 16 broker che supportano TradingView non offre anche MT4. Ciò significa che il supporto a TradingView si aggiunge quasi sempre all\'offerta MetaTrader già esistente di un broker, anziché sostituirla.',
                'section_4_heading' => 'Il supporto a TradingView è fortemente correlato ai nostri broker meglio classificati',
                'section_4_body' => 'Dividendo il campione in base ai primi 40 broker per classifica complessiva emerge un divario reale: il 30% dei nostri primi 40 (12 su 40) supporta TradingView, contro appena il 4% dei restanti 99 (4 broker). MT4 mostra una versione più contenuta dello stesso schema -- l\'83% dei primi 40 lo supporta, contro il 71% degli altri. Si tratta di una correlazione, non di un nesso causale: supportare TradingView non rende di per sé un broker meglio regolamentato o più economico, e la nostra formula di classifica non premia direttamente il numero di piattaforme (che è una semplice funzione del numero di piattaforme confermate, con un peso del 10% sul punteggio complessivo) -- ma in questo campione le due caratteristiche tendono chiaramente a presentarsi insieme.',
                'section_5_heading' => 'I cinque broker confermati come supportati da tutte e quattro le piattaforme principali',
                'section_5_body' => 'Solo 5 dei 139 broker del nostro campione di ricerca -- Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital -- risultano confermati come supportati contemporaneamente per tutte e quattro le piattaforme MT4, MT5, cTrader e TradingView. Si tratta di una combinazione davvero rara: offrire ogni principale piattaforma di terze parti significa supportare quattro infrastrutture di trading distinte invece di concentrarsi su una o due, e tutti e cinque questi broker adottano anche un modello di esecuzione in stile ECN/no-dealing-desk. Il nostro campione, però, non ci permette di dire se si tratti di una causa, di una correlazione o di una coincidenza, data la dimensione ridotta del campione.',
                'section_6_heading' => 'Le piattaforme proprietarie sono comuni singolarmente, ma frammentate come categoria',
                'section_6_body' => 'Oltre alle quattro grandi, 22 dei 139 broker confermano esattamente una piattaforma, e 5 di questi offrono solo MT4. Il resto del campione è una lunga coda di piattaforme proprietarie specifiche di ciascun broker -- ognuna tipicamente confermata per un solo broker, senza alcuna piattaforma proprietaria usata da più di qualche broker -- quindi «proprietaria» è una categoria comune ma frammentata nella pratica, a differenza della reale standardizzazione tra broker rappresentata da MT4, MT5, cTrader e TradingView. 12 dei 139 broker non hanno alcuna piattaforma confermata nella nostra ricerca; trattiamo questo dato come una lacuna onesta anziché indovinare, coerentemente con come gestiamo ogni campo non confermato su questo sito.',
                'faq_1_q' => 'I broker forex stanno abbandonando MT4?',
                'faq_1_a' => 'Non ancora, almeno non tra i broker che abbiamo analizzato: il 74% lo supporta ancora, e l\'87% dei broker che hanno aggiunto MT5 mantiene MT4 in funzione accanto a MT5, anziché abbandonarlo. Solo il 13% dei broker che supportano MT5 nel nostro campione ha abbandonato del tutto MT4.',
                'faq_2_q' => 'Quali broker forex supportano MT4, MT5, cTrader e TradingView?',
                'faq_2_a' => 'Cinque broker del nostro campione di 139 risultano confermati come supportati per tutte e quattro: Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital.',
                'faq_3_q' => 'Il supporto a TradingView significa che un broker è migliore?',
                'faq_3_a' => 'Non direttamente -- la nostra formula di punteggio non premia da sola il numero di piattaforme. Però nei nostri dati esiste una correlazione reale: il 30% dei nostri broker classificati tra i primi 40 supporta TradingView, contro appena il 4% dei restanti 99, quindi l\'ampiezza delle piattaforme e la classifica complessiva tendono chiaramente a muoversi insieme, anche se nessuna delle due causa meccanicamente l\'altra.',
                'faq_4_q' => 'Come sono stati raccolti questi dati?',
                'faq_4_a' => 'Dal campo delle piattaforme già utilizzato in tutte le recensioni dei broker, nelle liste dei migliori broker e nei confronti di questo sito -- lo stesso campione di 139 broker, non un sondaggio separato. Quando non siamo riusciti a confermare in modo indipendente il supporto alle piattaforme di un broker, lo abbiamo contato come non confermato anziché presumere in un senso o nell\'altro; 12 dei 139 broker rientrano in questa categoria.',
            ),
            'pt' => array(
                'title' => 'Relatório de Adoção de Plataformas de Negociação de Forex',
                'meta_title' => 'Relatório de Adoção de Plataformas de Forex (2026): MT4, MT5, cTrader e TradingView em 139 corretoras',
                'meta_description' => 'Quantas corretoras de forex ainda suportam o MT4, quantas já passaram para o MT5 e quão rara é a oferta de cTrader ou TradingView -- um estudo de dados original sobre todo o nosso conjunto de investigação de 139 corretoras.',
                'intro' => 'As plataformas de negociação que uma corretora suporta raramente merecem o mesmo escrutínio que o seu spread ou o seu regulador -- por isso extraímos esses dados diretamente do nosso próprio conjunto de investigação de 139 corretoras e contámo-los. Este relatório abrange todas as plataformas confirmadas nesse conjunto completo: a frequência com que o MT4 e o MT5 continuam a ser utilizados, quantas corretoras os executam em simultâneo face às que já avançaram, e quão rara é, na prática, a oferta de cTrader e TradingView. Todos os valores abaixo provêm do mesmo campo de plataformas utilizado em todas as análises, listas de melhores corretoras e comparações deste site -- nada aqui constitui uma afirmação separada e recém-obtida.',
                'section_1_heading' => 'O MT4 continua a ser a plataforma mais comum, mas o MT5 quase o alcançou',
                'section_1_body' => '103 das 139 corretoras do nosso conjunto de investigação (74%) têm confirmado o suporte ao MT4, e 97 (70%) têm confirmado o suporte ao MT5. Apesar de o MetaTrader 4 ser a mais antiga das duas plataformas, e apesar de a MetaQuotes ter deixado de desenvolver ativamente a versão em favor do MT5, continua a ser a plataforma com maior suporte, por uma margem modesta -- as corretoras não abandonaram o MT4 sequer perto da taxa que a reputação de «plataforma legada» poderia sugerir.',
                'section_2_heading' => 'A maioria das corretoras executa o MT4 e o MT5 em paralelo -- poucas abandonaram realmente o MT4',
                'section_2_body' => '84 das 139 corretoras suportam o MT4 e o MT5 em simultâneo -- de longe a configuração mais comum. Apenas 13 das 97 corretoras que suportam o MT5 (13%) abandonaram totalmente o MT4; as restantes 87% mantêm ambas as plataformas em funcionamento em paralelo. 19 corretoras continuam a oferecer o MT4 sem terem acrescentado o MT5, e 23 corretoras (17% do conjunto completo) não oferecem nenhuma das duas plataformas, recorrendo antes ao cTrader, ao TradingView ou a uma plataforma proprietária de que dispõem.',
                'section_3_heading' => 'O cTrader e o TradingView continuam a ser plataformas minoritárias',
                'section_3_body' => '18 das 139 corretoras (13%) têm confirmado o suporte ao cTrader, e 16 (12%) têm confirmado o suporte ao TradingView -- ambas ainda claramente minoritárias face ao MT4/MT5, embora longe de irrelevantes. As duas sobrepõem-se mais do que competem: 7 corretoras suportam simultaneamente o cTrader e o TradingView, e apenas 1 das 16 corretoras que suportam o TradingView o faz sem oferecer também o MT4, o que significa que o suporte ao TradingView é quase sempre um complemento da oferta MetaTrader já existente de uma corretora, e não um substituto.',
                'section_4_heading' => 'O suporte ao TradingView está fortemente correlacionado com as nossas corretoras mais bem classificadas',
                'section_4_body' => 'Dividindo o conjunto de dados pelas nossas 40 principais corretoras segundo a classificação global, surge uma diferença real: 30% das nossas 40 principais corretoras (12 em 40) suportam o TradingView, contra apenas 4% das restantes 99 (4 corretoras). O MT4 mostra uma versão mais ligeira do mesmo padrão -- 83% das 40 principais suportam-no, contra 71% das restantes. Trata-se de uma correlação, e não de uma afirmação de causalidade: suportar o TradingView não torna, por si só, uma corretora mais bem regulada ou mais barata, e a nossa fórmula de classificação não recompensa diretamente o número de plataformas (o campo de plataformas é uma função simples do número de plataformas confirmadas, ponderada em 10% da pontuação global) -- mas as duas tendem claramente a andar juntas neste conjunto de dados.',
                'section_5_heading' => 'As cinco corretoras com suporte confirmado às quatro principais plataformas',
                'section_5_body' => 'Apenas 5 das 139 corretoras do nosso conjunto de investigação -- Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital -- têm confirmado o suporte simultâneo às quatro plataformas MT4, MT5, cTrader e TradingView. Trata-se de uma combinação genuinamente rara: oferecer todas as principais plataformas de terceiros significa suportar quatro infraestruturas de negociação distintas, em vez de concentrar-se numa ou duas, e todas estas cinco corretoras executam também um modelo de execução de ordens do tipo ECN/sem mesa de dealing, embora o nosso conjunto de dados não permita afirmar se se trata de causa, correlação ou coincidência, dada a dimensão reduzida da amostra.',
                'section_6_heading' => 'As plataformas proprietárias são comuns individualmente, mas fragmentadas enquanto categoria',
                'section_6_body' => 'Para além das quatro principais, 22 das 139 corretoras confirmam exatamente uma plataforma, e 5 delas são apenas MT4. O restante conjunto de dados é uma longa cauda de plataformas proprietárias específicas de cada corretora -- cada uma normalmente confirmada para uma única corretora, sem nenhuma plataforma proprietária utilizada por mais do que um punhado de corretoras -- pelo que «proprietária» é comum como categoria, mas fragmentada na prática, ao contrário da verdadeira padronização transversal que o MT4, o MT5, o cTrader e o TradingView representam. 12 das 139 corretoras não têm qualquer plataforma confirmada na nossa investigação; tratamos isso como uma lacuna honesta nos dados, em vez de adivinhar, em consonância com a forma como qualquer outro campo não confirmado deste site é tratado.',
                'faq_1_q' => 'O MT4 está a ser descontinuado pelas corretoras de forex?',
                'faq_1_a' => 'Ainda não, pelo menos entre as corretoras que investigámos: 74% continuam a suportá-lo, e 87% das corretoras que acrescentaram o MT5 mantêm o MT4 em funcionamento em paralelo, em vez de o abandonarem. Apenas 13% das corretoras que suportam o MT5 no nosso conjunto de dados abandonaram totalmente o MT4.',
                'faq_2_q' => 'Que corretoras de forex suportam o MT4, o MT5, o cTrader e o TradingView?',
                'faq_2_a' => 'Cinco corretoras do nosso conjunto de investigação de 139 têm confirmado o suporte às quatro: Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital.',
                'faq_3_q' => 'O suporte ao TradingView significa que uma corretora é melhor?',
                'faq_3_a' => 'Não diretamente -- a nossa fórmula de pontuação não recompensa o número de plataformas por si só. Mas existe uma correlação real nos nossos dados: 30% das nossas 40 principais corretoras suportam o TradingView, contra apenas 4% das restantes 99, pelo que a amplitude de plataformas e a classificação global tendem claramente a acompanhar-se, embora uma não cause mecanicamente a outra.',
                'faq_4_q' => 'Como foram recolhidos estes dados?',
                'faq_4_a' => 'A partir do campo de plataformas já utilizado em todas as análises, listas de melhores corretoras e comparações deste site -- o mesmo conjunto de investigação de 139 corretoras, e não um inquérito separado. Quando não conseguimos confirmar de forma independente o suporte de plataformas de uma corretora, contabilizámo-lo como não confirmado, em vez de presumir qualquer dos dois cenários; 12 das 139 corretoras enquadram-se nessa categoria.',
            ),
        ),
    );
}

function globalfxhub_get_research_report( $slug ) {
    $reports = globalfxhub_get_research_reports();
    return $reports[ $slug ] ?? null;
}

function globalfxhub_get_localized_research_report( $slug ) {
    $entry = globalfxhub_get_research_report( $slug );
    if ( ! $entry ) {
        return null;
    }
    $lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
    $translations = globalfxhub_research_report_translations();
    $flat = $translations[ $slug ][ $lang ] ?? array();
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

function globalfxhub_research_reports_seo_title( $title_parts ) {
    $slug = get_query_var( 'report' );
    if ( $slug ) {
        $entry = globalfxhub_get_localized_research_report( $slug );
        if ( $entry ) {
            $title_parts['title'] = $entry['meta_title'];
        }
    } elseif ( is_page( 'research' ) ) {
        $title_parts['title'] = 'Original Forex Broker Research ' . date( 'Y' ) . ': Data Studies and Reports';
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_research_reports_seo_title' );

function globalfxhub_research_reports_canonical( $canonical_url ) {
    $slug = get_query_var( 'report' );
    if ( $slug && globalfxhub_get_research_report( $slug ) ) {
        return home_url( '/research/' . $slug . '/' );
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_research_reports_canonical' );

function globalfxhub_research_reports_seo_head() {
    $slug = get_query_var( 'report' );
    if ( ! $slug ) {
        if ( is_page( 'research' ) ) {
            echo '<meta name="description" content="' . esc_attr( 'Original data studies and reports built directly from our own 139-broker research set -- platform adoption, regulation, fees, and more, computed from the same dataset behind every review and ranking on this site.' ) . '">' . "\n";
        }
        return;
    }
    $entry = globalfxhub_get_localized_research_report( $slug );
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
add_action( 'wp_head', 'globalfxhub_research_reports_seo_head', 5 );
