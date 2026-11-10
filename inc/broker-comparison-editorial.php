<?php
/**
 * Editorial comparison content for specific, hand-curated broker pairs --
 * layered on top of the existing data-driven /compare/{a}-vs-{b}/ table
 * (inc/broker-vs.php), not a replacement for it. The table already
 * covers the mechanical side-by-side facts; this adds the "which
 * broker should YOU actually pick, and why" narrative a reader
 * searching "X vs Y" is really looking for -- see CLAUDE.md's content
 * strategy ("editorially curated comparisons, not generic database
 * dumps").
 *
 * Deliberately NOT a template applied to all ~780 eligible pairs --
 * each entry here is a hand-written piece grounded in that specific
 * pair's actual confirmed data (see globalfxhub_get_brokers()), added
 * one at a time as the editorial calendar reaches that pair. A pair
 * with no entry here just shows the existing data table, unchanged.
 *
 * Every claim below traces back to a field already in this broker's
 * own array -- nothing here is a new, separately-sourced fact.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Per-language translations of each editorial comparison's content,
 * keyed by pair then by language, flattened the same way
 * i18n/translate_comparison_editorial.py flattens it for the real
 * translate+review pipeline (point-list items as choose_a_point_1..N,
 * FAQs as faq_N_q/faq_N_a) -- globalfxhub_get_localized_editorial_comparison()
 * reassembles the structure. Populated via that script's output, same
 * as every other hand-curated translation table in this theme; empty
 * for a language/pair until that translation run has actually been
 * inserted here.
 */
function globalfxhub_editorial_comparison_translations() {
    return array(
        'pepperstone-ic-markets' => array(
                'es' => array(
                    'meta_title' => 'Pepperstone vs IC Markets (2026): ¿Qué bróker deberías elegir?',
                    'meta_description' => 'Pepperstone vs IC Markets comparados en regulación, costes, plataformas y trayectoria -- con nuestra metodología publicada, no con afirmaciones de marketing. Descubre cuál se ajusta a tu estilo de trading.',
                    'intro' => 'Pepperstone e IC Markets se basan en la misma propuesta: precios ajustados de estilo ECN dirigidos a traders activos y algorítmicos, no a un producto masivo para el minorista. Esa similitud es precisamente la razón por la que se comparan tan a menudo, pero los dos brókers en realidad difieren en aspectos que importan más de lo que sugiere su posicionamiento compartido de «diseñado para traders serios».',
                    'verdict' => 'Pepperstone cuenta con una presencia regulatoria Tier-1 más amplia (CySEC, FCA, ASIC y DFSA, frente a CySEC y ASIC de IC Markets), un depósito mínimo mucho menor ($10 frente a $200) y una plataforma compatible adicional (TradingView, junto a MT4/MT5/cTrader). IC Markets se adelanta en el spread principal de EUR/USD que tenemos registrado (0.8 pips frente a 1.1 pips de Pepperstone) y tiene una trayectoria operativa más larga (fundada en 2007 frente a 2010). Sin embargo, ambos brókers también ofrecen un nivel separado de spread bruto más comisión que no queda reflejado en esa cifra principal, así que revisa las condiciones actuales de la cuenta de cada bróker para tu caso concreto. Según nuestra metodología publicada, Pepperstone obtiene 3.64/5 en la puntuación global frente a 3.07/5 de IC Markets, impulsada principalmente por la categoría de regulación (30% del total y la mayor ventaja de Pepperstone).',
                    'choose_a_heading' => 'Elige Pepperstone si...',
                    'choose_a_point_1' => 'Quieres una presencia regulatoria más amplia: Pepperstone cuenta con autorización de CySEC, FCA, ASIC y DFSA, frente a CySEC y ASIC de IC Markets.',
                    'choose_a_point_2' => 'Empiezas con una cuenta pequeña: el depósito mínimo de $10 de Pepperstone es una fracción de los $200 de IC Markets.',
                    'choose_a_point_3' => 'Operas en TradingView: Pepperstone lo admite directamente; IC Markets no.',
                    'choose_a_point_4' => 'Te importa una política explícita de no cobrar comisión por inactividad: múltiples fuentes independientes confirman que Pepperstone no cobra ninguna, sin conflicto pendiente de resolver en nuestra investigación.',
                    'choose_b_heading' => 'Elige IC Markets si...',
                    'choose_b_point_1' => 'El spread principal es tu prioridad y puedes cumplir el mínimo de $200: el spread de 0.8 pips en EUR/USD de IC Markets es más ajustado que los 1.1 pips de Pepperstone en el nivel comparable.',
                    'choose_b_point_2' => 'Te importa una trayectoria operativa más larga: IC Markets opera desde 2007, tres años más que Pepperstone.',
                    'choose_b_point_3' => 'No necesitas cobertura de DFSA ni TradingView, y ASIC y CySEC bastan como respaldo regulatorio para tus necesidades.',
                    'worth_knowing_heading' => 'Lo que conviene saber antes de decidir',
                    'worth_knowing' => 'La entidad europea de IC Markets publica una Política formal de comisiones por inactividad (una cuenta se considera inactiva tras 6 meses continuos sin ninguna operación ejecutada), pero varios sitios de comparación independientes informan de que, en la práctica, no se cobra ninguna comisión; se trata de un conflicto real y sin resolver entre la política publicada por el bróker y los informes de terceros, que no pudimos resolver en nuestra investigación. Si el riesgo de inactividad te preocupa, confirma la situación actual directamente con IC Markets antes de basarte en cualquiera de las dos fuentes. La afirmación de Pepperstone de no cobrar comisión por inactividad, en cambio, está corroborada de forma consistente en las fuentes que revisamos.',
                    'faq_1_q' => '¿Es Pepperstone o IC Markets mejor para principiantes?',
                    'faq_1_a' => 'El depósito mínimo de $10 de Pepperstone lo hace mucho más accesible para empezar que los $200 de IC Markets, y su presencia regulatoria más amplia (con FCA y DFSA además de CySEC y ASIC) ofrece a un principiante más cobertura regulatoria a la que recurrir. Sin embargo, ninguna de las estructuras de cuenta de los dos brókers está diseñada explícitamente para principiantes antes que para traders activos.',
                    'faq_2_q' => '¿Cuál tiene el coste de trading más bajo, Pepperstone o IC Markets?',
                    'faq_2_a' => 'Según el spread principal de EUR/USD que tenemos registrado, IC Markets es más ajustado con 0.8 pips frente a los 1.1 pips de Pepperstone. Ambos ofrecen además un nivel separado de spread bruto más comisión (Razor de Pepperstone, Raw Spread de IC Markets) con niveles de comisión bastante similares; consulta el calendario de comisiones vigente de cada bróker para tu tipo de cuenta antes de basarte en cualquiera de las dos cifras principales.',
                ),
                'de' => array(
                    'meta_title' => 'Pepperstone vs IC Markets (2026): Welchen Broker sollten Sie wählen?',
                    'meta_description' => 'Pepperstone und IC Markets im Vergleich: Regulierung, Kosten, Handelsplattformen und Erfolgsbilanz -- auf Basis unserer offengelegten Methodik statt Marketingversprechen. Finden Sie heraus, welcher Broker zu Ihrem Handelsstil passt.',
                    'intro' => 'Pepperstone und IC Markets werben beide mit demselben Versprechen: enge Preise im ECN-Stil für aktive und algorithmische Trader, kein Massenprodukt für Privatkunden. Genau diese Ähnlichkeit ist der Grund, warum die beiden so häufig verglichen werden -- doch die Broker unterscheiden sich in Punkten, die schwerer wiegen, als ihre gemeinsame Positionierung als „für ernsthafte Trader gemacht“ vermuten lässt.',
                    'verdict' => 'Pepperstone verfügt über die breitere Tier-1-Regulierungsbasis (CySEC, FCA, ASIC und DFSA, gegenüber CySEC und ASIC bei IC Markets), eine deutlich niedrigere Mindesteinzahlung ($10 gegenüber $200) und eine weitere unterstützte Handelsplattform (TradingView, neben MT4/MT5/cTrader). IC Markets liegt bei unserem vorliegenden Kernwert für den EUR/USD-Spread vorn (0.8 Pips gegenüber 1.1 Pips bei Pepperstone) und ist länger am Markt (gegründet 2007 gegenüber 2010) -- allerdings bieten beide Broker zusätzlich ein separates Modell mit Rohspread plus Provision an, das von diesem einzelnen Kennwert nicht erfasst wird. Prüfen Sie daher die aktuellen Kontobedingungen jedes Brokers für Ihre konkrete Situation. Nach unserer offengelegten Methodik erreicht Pepperstone insgesamt 3.64/5, IC Markets 3.07/5. Maßgeblich dafür ist vor allem die Kategorie Regulierung (30% der Gesamtbewertung und der größte Vorteil von Pepperstone).',
                    'choose_a_heading' => 'Pepperstone wählen, wenn ...',
                    'choose_a_point_1' => 'Sie eine breitere regulatorische Abdeckung wünschen -- Pepperstone verfügt über die Zulassungen von CySEC, FCA, ASIC und DFSA, gegenüber CySEC und ASIC bei IC Markets.',
                    'choose_a_point_2' => 'Sie mit einem kleinen Konto starten -- die Mindesteinzahlung von $10 bei Pepperstone ist nur ein Bruchteil der $200 bei IC Markets.',
                    'choose_a_point_3' => 'Sie über TradingView handeln -- Pepperstone unterstützt es direkt, IC Markets nicht.',
                    'choose_a_point_4' => 'Ihnen ist eine ausdrückliche Richtlinie ohne Inaktivitätsgebühren wichtig -- mehrere unabhängige Quellen bestätigen, dass Pepperstone keine erhebt, und in unserer Recherche gibt es dazu keinen ungelösten Widerspruch.',
                    'choose_b_heading' => 'IC Markets wählen, wenn ...',
                    'choose_b_point_1' => 'der reine Headline-Spread Ihre Priorität ist und Sie die Mindesteinzahlung von $200 aufbringen können -- der 0.8-Pips-Spread von IC Markets beim EUR/USD ist im vergleichbaren Segment enger als die 1.1 Pips bei Pepperstone.',
                    'choose_b_point_2' => 'Ihnen eine längere Marktpräsenz wichtig ist -- IC Markets ist seit 2007 aktiv, also drei Jahre länger als Pepperstone.',
                    'choose_b_point_3' => 'Sie keine DFSA-Abdeckung oder TradingView benötigen und CySEC plus ASIC für Ihre Anforderungen ausreichend sind.',
                    'worth_knowing_heading' => 'Wissenswert vor Ihrer Entscheidung',
                    'worth_knowing' => 'Die EU-Gesellschaft von IC Markets veröffentlicht eine formelle Richtlinie zu Inaktivitätsgebühren (ein Konto gilt nach 6 durchgehenden Monaten ohne ausgeführten Handel als inaktiv), doch mehrere unabhängige Vergleichsseiten berichten, dass in der Praxis tatsächlich keine Gebühr erhoben wird -- ein echter, ungelöster Widerspruch zwischen der veröffentlichten Richtlinie des Brokers und Berichten Dritter, den wir in unserer Recherche nicht auflösen konnten. Falls Ihnen das Risiko einer Kontoinaktivität wichtig ist, bestätigen Sie den aktuellen Stand direkt bei IC Markets, bevor Sie sich auf eine der beiden Quellen verlassen. Der Anspruch von Pepperstone, keine Inaktivitätsgebühr zu erheben, wird dagegen in den von uns geprüften Quellen durchgehend bestätigt.',
                    'faq_1_q' => 'Ist Pepperstone oder IC Markets besser für Einsteiger?',
                    'faq_1_a' => 'Die Mindesteinzahlung von $10 macht Pepperstone für den Einstieg deutlich zugänglicher als die $200 bei IC Markets, und die breitere regulatorische Abdeckung (mit FCA und DFSA zusätzlich zu CySEC und ASIC) bietet Einsteigern mehr regulatorische Absicherung. Keiner der beiden Broker hat seine Kontostruktur allerdings ausdrücklich für Einsteiger statt für aktive Trader konzipiert.',
                    'faq_2_q' => 'Welcher Broker hat die niedrigeren Handelskosten, Pepperstone oder IC Markets?',
                    'faq_2_a' => 'Beim vorliegenden Kernwert für den EUR/USD-Spread ist IC Markets mit 0.8 Pips gegenüber 1.1 Pips bei Pepperstone enger. Beide Broker bieten außerdem ein separates Modell mit Rohspread plus Provision an (Pepperstones Razor, Raw Spread von IC Markets) mit weitgehend ähnlichen Provisionsstufen. Verlassen Sie sich nicht allein auf den Kernwert, sondern prüfen Sie vor einer Entscheidung die aktuelle Gebührenübersicht des jeweiligen Brokers für Ihren Kontotyp.',
                ),
                'ar' => array(
                    'meta_title' => 'Pepperstone مقابل IC Markets (2026): أي وسيط يجب أن تختار؟',
                    'meta_description' => 'مقارنة بين Pepperstone وIC Markets من حيث التنظيم والتكلفة والمنصات والسجل التاريخي -- وفق منهجيتنا المعلنة، لا وفق ادعاءات تسويقية. اعرف أيهما يناسب أسلوب تداولك.',
                    'intro' => 'يقوم Pepperstone وIC Markets على الطرح نفسه تقريبًا: تسعير محكم على غرار ECN يستهدف المتداولين النشطين والمتداولين الآليين، وليس منتجًا موجهًا إلى السوق الجماهيرية للأفراد. هذا التشابه هو ما يفسر كثرة مقارنتهما ببعض، غير أن الوسيطين يختلفان فعليًا في جوانب أكثر أهمية مما يوحي به تموضعهما المشترك بوصفهما "مصممين للمتداولين الجادين".',
                    'verdict' => 'يمتلك Pepperstone نطاقًا تنظيميًا أوسع من الفئة الأولى (CySEC وFCA وASIC وDFSA، مقابل CySEC وASIC لدى IC Markets)، وحدًا أدنى للإيداع أقل بكثير ($10 مقابل $200)، ومنصة إضافية مدعومة (TradingView، إلى جانب MT4 وMT5 وcTrader). ويتفوق IC Markets في فرق السعر الرئيسي المسجل لدينا لزوج EUR/USD (0.8 pips مقابل 1.1 pips لدى Pepperstone)، كما يتمتع بتاريخ تشغيل أطول (التأسيس عام 2007 مقابل 2010) -- مع الإشارة إلى أن كلا الوسيطين يقدمان أيضًا فئة منفصلة تجمع بين السبريد الخام والعمولة، لا يعكسها هذا الرقم الرئيسي وحده، لذا تحقق من شروط الحساب الحالية لكل وسيط بما يناسب إعدادك. ووفق منهجيتنا المعلنة، يحصل Pepperstone على تقييم إجمالي قدره 3.64/5 مقابل 3.07/5 لدى IC Markets، ويعود ذلك أساسًا إلى فئة التنظيم (30% من إجمالي التقييم، وهي الميزة الأكبر لدى Pepperstone).',
                    'choose_a_heading' => 'اختر Pepperstone إذا...',
                    'choose_a_point_1' => 'كنت تريد نطاقًا تنظيميًا أوسع -- إذ يحمل Pepperstone ترخيصًا من CySEC وFCA وASIC وDFSA، مقابل CySEC وASIC لدى IC Markets.',
                    'choose_a_point_2' => 'كنت تبدأ بحساب صغير -- فالحد الأدنى للإيداع لدى Pepperstone البالغ $10 لا يمثل سوى جزء بسيط من $200 لدى IC Markets.',
                    'choose_a_point_3' => 'كنت تتداول عبر TradingView -- يدعمه Pepperstone مباشرة، بينما لا يدعمه IC Markets.',
                    'choose_a_point_4' => 'تهمك سياسة صريحة بعدم وجود رسوم عدم نشاط -- فقد أكدت مصادر مستقلة متعددة أن Pepperstone لا يفرض أي رسوم من هذا النوع، ولا يوجد تضارب غير محسوم في أبحاثنا.',
                    'choose_b_heading' => 'اختر IC Markets إذا...',
                    'choose_b_point_1' => 'كانت أولويتك فرق السعر الرئيسي الخام وكنت قادرًا على تلبية الحد الأدنى البالغ $200 -- ففرق السعر البالغ 0.8 pips لزوج EUR/USD لدى IC Markets أضيق من 1.1 pips لدى Pepperstone ضمن الفئة المقارنة.',
                    'choose_b_point_2' => 'يهمك تاريخ تشغيل أطول -- فقد عمل IC Markets منذ عام 2007، أي قبل Pepperstone بثلاث سنوات.',
                    'choose_b_point_3' => 'لا تحتاج إلى تغطية DFSA أو TradingView، وتكفيك تغطية ASIC وCySEC التنظيمية لاحتياجاتك.',
                    'worth_knowing_heading' => 'ما يستحق معرفته قبل اتخاذ القرار',
                    'worth_knowing' => 'تنشر الكيان الأوروبي لدى IC Markets سياسة رسمية لرسوم عدم النشاط (يُعد الحساب غير نشط بعد 6 أشهر متتالية دون أي صفقة منفذة)، لكن مواقع مقارنة مستقلة متعددة تفيد بأنه لا تُفرض فعليًا أي رسوم في الممارسة -- وهو تضارب حقيقي وغير محسوم بين السياسة المنشورة من الوسيط والتقارير الصادرة عن أطراف ثالثة، ولم نتمكن من حله في بحثنا. فإذا كانت مخاطر الخمول تهمك، فتحقق من الوضع الحالي مباشرة لدى IC Markets قبل الاعتماد على أي من المصدرين. أما ادعاء Pepperstone بعدم وجود رسوم عدم نشاط، فهو على النقيض من ذلك مدعوم باستمرار في المصادر التي راجعناها.',
                    'faq_1_q' => 'أيهما أفضل للمبتدئين، Pepperstone أم IC Markets؟',
                    'faq_1_a' => 'يجعل الحد الأدنى للإيداع لدى Pepperstone البالغ $10 البدء به أيسر بكثير من IC Markets الذي يتطلب $200، كما يمنح نطاقه التنظيمي الأوسع (بإضافة FCA وDFSA إلى CySEC وASIC) المبتدئ تغطية تنظيمية أكبر يلجأ إليها عند الحاجة. ومع ذلك، لا يُصمم هيكل حساب أي من الوسيطين صراحةً للمبتدئين بقدر ما يُصمم للمتداولين النشطين.',
                    'faq_2_q' => 'أيهما تكلفة التداول فيه أقل، Pepperstone أم IC Markets؟',
                    'faq_2_a' => 'وفق فرق السعر الرئيسي المسجل لدينا لزوج EUR/USD، يكون IC Markets أضيق عند 0.8 pips مقابل 1.1 pips لدى Pepperstone. ويقدم الوسيطان كذلك فئة منفصلة تجمع بين السبريد الخام والعمولة (Razor لدى Pepperstone، وRaw Spread لدى IC Markets) بمستويات عمولة متقاربة إلى حد كبير -- تحقق من جدول الرسوم الحالي لكل وسيط بما يناسب نوع حسابك قبل الاعتماد على أي رقم رئيسي.',
                ),
                'fr' => array(
                    'meta_title' => 'Pepperstone vs IC Markets (2026) : quel courtier choisir ?',
                    'meta_description' => 'Pepperstone et IC Markets comparés sur la réglementation, les coûts, les plateformes et l\'historique, selon notre méthodologie publiée et non sur la base d\'arguments marketing. Découvrez lequel correspond à votre style de trading.',
                    'intro' => 'Pepperstone et IC Markets reposent tous deux sur la même promesse : une tarification serrée de type ECN, destinée aux traders actifs et algorithmiques, et non à un produit grand public. Cette similitude explique pourquoi ils sont si souvent comparés, mais les deux courtiers diffèrent sur des points plus importants que ne le laisse supposer leur positionnement commun « conçu pour les traders exigeants ».',
                    'verdict' => 'Pepperstone bénéficie d\'une présence réglementaire de premier rang plus étendue (CySEC, FCA, ASIC et DFSA, contre CySEC et ASIC pour IC Markets), d\'un dépôt minimum bien plus bas ($10 contre $200) et d\'une plateforme de trading supplémentaire (TradingView, aux côtés de MT4/MT5/cTrader). IC Markets prend l\'avantage sur le spread EUR/USD de référence figurant dans nos données (0.8 pips contre 1.1 pips pour Pepperstone) et dispose d\'un historique plus long (fondé en 2007 contre 2010). Toutefois, les deux courtiers proposent aussi une formule distincte à spread brut plus commission, que ce chiffre de référence unique ne prend pas en compte. Vérifiez donc les conditions de compte actuelles de chaque courtier pour votre situation. Selon notre méthodologie publiée, Pepperstone obtient une note globale de 3.64/5 contre 3.07/5 pour IC Markets, principalement grâce à la catégorie réglementation (30% du score total, et principal atout de Pepperstone).',
                    'choose_a_heading' => 'Choisissez Pepperstone si...',
                    'choose_a_point_1' => 'Vous recherchez une présence réglementaire plus étendue : Pepperstone détient les agréments CySEC, FCA, ASIC et DFSA, contre CySEC et ASIC pour IC Markets.',
                    'choose_a_point_2' => 'Vous débutez avec un petit compte : le dépôt minimum de $10 de Pepperstone représente une fraction des $200 exigés par IC Markets.',
                    'choose_a_point_3' => 'Vous tradez sur TradingView : Pepperstone le prend directement en charge, ce qui n\'est pas le cas d\'IC Markets.',
                    'choose_a_point_4' => 'Une absence explicite de frais d\'inactivité compte pour vous : plusieurs sources indépendantes confirment que Pepperstone n\'en applique aucun, sans conflit non résolu dans nos recherches.',
                    'choose_b_heading' => 'Choisissez IC Markets si...',
                    'choose_b_point_1' => 'Le spread brut de référence est votre priorité et vous pouvez respecter le dépôt minimum de $200 : le spread EUR/USD de 0.8 pip d\'IC Markets est plus serré que les 1.1 pips de Pepperstone sur la formule comparable.',
                    'choose_b_point_2' => 'Un historique d\'exploitation plus long compte pour vous : IC Markets est en activité depuis 2007, soit trois ans de plus que Pepperstone.',
                    'choose_b_point_3' => 'Vous n\'avez pas besoin de la couverture DFSA ni de TradingView, et ASIC et CySEC suffisent à couvrir vos besoins réglementaires.',
                    'worth_knowing_heading' => 'À savoir avant de vous décider',
                    'worth_knowing' => 'La filiale européenne d\'IC Markets publie une politique formelle relative aux frais d\'inactivité (un compte est considéré comme inactif après 6 mois consécutifs sans transaction exécutée), mais plusieurs comparateurs indépendants indiquent qu\'aucun frais n\'est réellement appliqué en pratique. Il existe un conflit réel et non résolu entre la politique publiée par le courtier et les informations de tiers, que nous n\'avons pas pu trancher dans nos recherches. Si le risque d\'inactivité du compte vous préoccupe, confirmez directement auprès d\'IC Markets la situation actuelle avant de vous fier à l\'une ou l\'autre source. L\'absence annoncée de frais d\'inactivité chez Pepperstone, en revanche, est systématiquement corroborée par les sources que nous avons consultées.',
                    'faq_1_q' => 'Pepperstone ou IC Markets, lequel est le meilleur pour les débutants ?',
                    'faq_1_a' => 'Le dépôt minimum de $10 de Pepperstone le rend bien plus accessible pour débuter que les $200 d\'IC Markets, et sa présence réglementaire plus étendue (avec FCA et DFSA en plus de CySEC et ASIC) offre à un débutant davantage de couverture réglementaire sur laquelle se reposer. Aucun des deux courtiers ne conçoit toutefois sa structure de compte spécifiquement pour les débutants plutôt que pour les traders actifs.',
                    'faq_2_q' => 'Lequel a le coût de trading le plus bas, Pepperstone ou IC Markets ?',
                    'faq_2_a' => 'Sur le spread EUR/USD de référence figurant dans nos données, IC Markets est plus serré avec 0.8 pip contre 1.1 pips pour Pepperstone. Les deux proposent également une formule distincte à spread brut plus commission (Razor chez Pepperstone, Raw Spread chez IC Markets) avec des niveaux de commission assez proches. Vérifiez la grille tarifaire actuelle de chaque courtier pour votre type de compte avant de vous fier à l\'un ou l\'autre chiffre de référence.',
                ),
                'pl' => array(
                    'meta_title' => 'Pepperstone vs IC Markets (2026): którego brokera wybrać?',
                    'meta_description' => 'Pepperstone i IC Markets porównane pod kątem regulacji, kosztów, platform i historii działalności -- według naszej ujawnionej metodologii, a nie deklaracji marketingowych. Sprawdź, który z nich pasuje do Twojego stylu handlu.',
                    'intro' => 'Pepperstone i IC Markets opierają się na tej samej koncepcji: wąskie spready w modelu ECN, skierowane do aktywnych i algorytmicznych traderów, a nie do masowego produktu detalicznego. Właśnie to podobieństwo sprawia, że obie firmy tak często są zestawiane ze sobą -- ale różnią się w sposób, który ma większe znaczenie, niż sugeruje ich wspólne pozycjonowanie jako firm „stworzonych dla poważnych traderów”.',
                    'verdict' => 'Pepperstone ma szerszy zasięg regulacyjny Tier-1 (CySEC, FCA, ASIC i DFSA, wobec CySEC i ASIC w przypadku IC Markets), znacznie niższy minimalny depozyt ($10 wobec $200) oraz jedną dodatkową obsługiwaną platformę handlową (TradingView, obok MT4/MT5/cTrader). IC Markets nieznacznie wyprzedza w naszym zarejestrowanym wskaźniku spreadu EUR/USD (0.8 pipsa wobec 1.1 pipsa u Pepperstone) i ma dłuższą historię działalności (założona w 2007 r. wobec 2010 r.) -- choć obie firmy oferują także osobny poziom spreadu surowego plus prowizja, którego nie obejmuje ta jedna wartość, więc sprawdź aktualne warunki konta handlowego u każdego brokera pod kątem swojej konfiguracji. Zgodnie z naszą ujawnioną metodologią Pepperstone uzyskuje łącznie ocenę 3.64/5, a IC Markets 3.07/5, głównie dzięki kategorii regulacji (30% całkowitej oceny i największa przewaga Pepperstone).',
                    'choose_a_heading' => 'Wybierz Pepperstone, jeśli...',
                    'choose_a_point_1' => 'Zależy Ci na szerszym zasięgu regulacyjnym -- Pepperstone posiada autoryzację CySEC, FCA, ASIC i DFSA, wobec CySEC i ASIC w przypadku IC Markets.',
                    'choose_a_point_2' => 'Zaczynasz z niewielkim kontem handlowym -- minimalny depozyt Pepperstone wynoszący $10 stanowi ułamek kwoty $200 wymaganej przez IC Markets.',
                    'choose_a_point_3' => 'Handlujesz na TradingView -- Pepperstone obsługuje tę platformę bezpośrednio, IC Markets nie.',
                    'choose_a_point_4' => 'Istotna jest dla Ciebie wyraźna polityka braku opłat za nieaktywność -- wielu niezależnych źródeł potwierdza, że Pepperstone ich nie pobiera, a w naszych badaniach nie ma nierozstrzygniętych sprzeczności.',
                    'choose_b_heading' => 'Wybierz IC Markets, jeśli...',
                    'choose_b_point_1' => 'Priorytetem jest dla Ciebie nominalny spread surowy, a możesz spełnić minimum $200 -- spread EUR/USD IC Markets wynoszący 0.8 pipsa jest węższy niż 1.1 pipsa Pepperstone na porównywalnym poziomie.',
                    'choose_b_point_2' => 'Ważna jest dla Ciebie dłuższa historia działalności -- IC Markets działa od 2007 r., czyli o trzy lata dłużej niż Pepperstone.',
                    'choose_b_point_3' => 'Nie potrzebujesz zasięgu DFSA ani TradingView, a dla Twoich potrzeb wystarczy regulacja ASIC i CySEC.',
                    'worth_knowing_heading' => 'Warto wiedzieć, zanim podejmiesz decyzję',
                    'worth_knowing' => 'Unijny podmiot IC Markets publikuje formalną politykę opłat za nieaktywność (konto uznaje się za nieaktywne po 6 kolejnych miesiącach bez zrealizowanej transakcji), jednak wiele niezależnych serwisów porównawczych podaje, że w praktyce żadna opłata nie jest pobierana -- to rzeczywista, nierozstrzygnięta sprzeczność między własną publikowaną polityką brokera a raportami stron trzecich, której nie udało nam się rozwiązać w toku badań. Jeśli ryzyko uśpienia konta ma dla Ciebie znaczenie, przed podjęciem decyzji potwierdź aktualną sytuację bezpośrednio u IC Markets. Twierdzenie Pepperstone dotyczące braku opłat za nieaktywność jest natomiast konsekwentnie potwierdzane w sprawdzonych przez nas źródłach.',
                    'faq_1_q' => 'Czy Pepperstone, czy IC Markets jest lepszym wyborem dla początkujących?',
                    'faq_1_a' => 'Minimalny depozyt Pepperstone wynoszący $10 sprawia, że firma jest znacznie łatwiej dostępna na start niż IC Markets z kwotą $200, a jej szerszy zasięg regulacyjny (obok CySEC i ASIC dodaje FCA i DFSA) daje początkującemu więcej zabezpieczeń regulacyjnych. Żadna z tych firm nie projektuje jednak struktury konta z myślą o początkujących bardziej niż o aktywnych traderach.',
                    'faq_2_q' => 'Który z brokerów, Pepperstone czy IC Markets, ma niższe koszty handlu?',
                    'faq_2_a' => 'Według naszego zarejestrowanego wskaźnika spreadu EUR/USD IC Markets jest węższy: 0.8 pipsa wobec 1.1 pipsa u Pepperstone. Obie firmy oferują także osobny poziom spreadu surowego plus prowizja (Razor u Pepperstone, Raw Spread u IC Markets) z zbliżonymi poziomami prowizji -- przed podjęciem decyzji sprawdź aktualny cennik każdego brokera dla swojego typu konta, zamiast polegać wyłącznie na wskaźniku nominalnym.',
                ),
                'it' => array(
                    'meta_title' => 'Pepperstone vs IC Markets (2026): quale broker scegliere?',
                    'meta_description' => 'Pepperstone vs IC Markets a confronto su regolamentazione, costi, piattaforme e track record -- con la nostra metodologia dichiarata, non affermazioni promozionali. Scopri quale si adatta al proprio stile di trading.',
                    'intro' => 'Pepperstone e IC Markets si presentano con la stessa proposta: prezzi stretti in stile ECN, pensati per trader attivi e algoritmici, non per un prodotto retail di massa. Proprio questa somiglianza spiega perché vengono confrontati così spesso, ma i due broker differiscono in aspetti più rilevanti di quanto suggerisca il comune posizionamento «pensato per trader seri».',
                    'verdict' => 'Pepperstone ha una copertura normativa Tier-1 più ampia (CySEC, FCA, ASIC e DFSA, contro CySEC e ASIC di IC Markets), un deposito minimo molto più basso ($10 contro $200) e una piattaforma supportata in più (TradingView, oltre a MT4/MT5/cTrader). IC Markets è in vantaggio sullo spread EUR/USD di riferimento rilevato nei nostri dati (0.8 pips contro 1.1 pips di Pepperstone) e ha una storia operativa più lunga (fondata nel 2007 contro il 2010) -- anche se entrambi i broker offrono un tier separato con spread grezzo più commissione, che non è catturato da quel singolo dato di riferimento, quindi è opportuno verificare le condizioni di trading attuali di ciascun broker per la propria situazione specifica. Secondo la nostra metodologia dichiarata, Pepperstone ottiene un punteggio complessivo di 3.64/5 contro il 3.07/5 di IC Markets, trainato soprattutto dalla categoria regolamentazione (30% del punteggio totale, e il vantaggio più forte di Pepperstone).',
                    'choose_a_heading' => 'Scegli Pepperstone se...',
                    'choose_a_point_1' => 'Serve una copertura normativa più ampia -- Pepperstone detiene l\'autorizzazione CySEC, FCA, ASIC e DFSA, contro CySEC e ASIC di IC Markets.',
                    'choose_a_point_2' => 'Parti con un conto piccolo -- il deposito minimo di Pepperstone da $10 è una frazione dei $200 di IC Markets.',
                    'choose_a_point_3' => 'Operi su TradingView -- Pepperstone lo supporta direttamente; IC Markets non lo supporta.',
                    'choose_a_point_4' => 'Una politica esplicita di assenza di commissioni di inattività è importante per te -- più fonti indipendenti confermano che Pepperstone non ne applica alcuna, senza conflitti irrisolti nella nostra ricerca.',
                    'choose_b_heading' => 'Scegli IC Markets se...',
                    'choose_b_point_1' => 'La priorità è lo spread grezzo di riferimento e si può soddisfare il deposito minimo di $200 -- lo spread EUR/USD di 0.8 pip di IC Markets è più stretto dei 1.1 pip di Pepperstone sul tier comparabile.',
                    'choose_b_point_2' => 'Una storia operativa più lunga è importante per te -- IC Markets opera dal 2007, tre anni in più di Pepperstone.',
                    'choose_b_point_3' => 'Non servono né la copertura DFSA né TradingView, e ASIC e CySEC bastano per le esigenze normative.',
                    'worth_knowing_heading' => 'Da sapere prima di decidere',
                    'worth_knowing' => 'L\'entità UE di IC Markets pubblica una politica formale sulle commissioni di inattività (un conto è considerato inattivo dopo 6 mesi consecutivi senza operazioni eseguite), ma diversi siti di confronto indipendenti riferiscono che in pratica non viene addebitata alcuna commissione -- un conflitto reale e irrisolto tra la politica pubblicata dal broker e le segnalazioni di terzi, che non siamo riusciti a risolvere nella nostra ricerca. Se il rischio di inattività è rilevante, è opportuno verificare la situazione attuale direttamente con IC Markets prima di fare affidamento su una delle due fonti. L\'affermazione di Pepperstone sull\'assenza di commissioni di inattività, invece, è costantemente confermata dalle fonti verificate.',
                    'faq_1_q' => 'Pepperstone o IC Markets è meglio per i principianti?',
                    'faq_1_a' => 'Il deposito minimo di $10 di Pepperstone lo rende molto più accessibile per iniziare rispetto ai $200 di IC Markets, e la sua copertura normativa più ampia (con FCA e DFSA oltre a CySEC e ASIC) offre a un principiante più copertura normativa su cui contare. Nessuno dei due broker, però, ha una struttura del conto di trading pensata esplicitamente per i principianti anziché per i trader attivi.',
                    'faq_2_q' => 'Quale dei due ha il costo di trading più basso, Pepperstone o IC Markets?',
                    'faq_2_a' => 'Sullo spread EUR/USD di riferimento rilevato nei nostri dati, IC Markets è più stretto con 0.8 pips contro gli 1.1 pips di Pepperstone. Entrambi offrono anche un tier separato con spread grezzo più commissione (Razor di Pepperstone, Raw Spread di IC Markets) con livelli di commissione simili -- è opportuno verificare il listino commissioni attuale di ciascun broker per il tipo di conto di interesse, prima di basarsi su uno dei due dati di riferimento.',
                ),
                'pt' => array(
                    'meta_title' => 'Pepperstone vs IC Markets (2026): Qual Corretora Deve Escolher?',
                    'meta_description' => 'Pepperstone vs IC Markets comparadas em regulação, custos, plataformas e histórico de atuação -- com base na nossa metodologia divulgada, e não em afirmações de marketing. Veja qual se adapta ao seu estilo de negociação.',
                    'intro' => 'A Pepperstone e a IC Markets assentam na mesma proposta: preços apertados em estilo ECN, concebidos para traders ativos e algorítmicos, e não como um produto de massas para o investidor de retalho. Essa semelhança é precisamente a razão pela qual são tão frequentemente comparadas -- mas as duas corretoras diferem em aspetos que pesam mais do que sugere o seu posicionamento comum de "concebidas para traders sérios".',
                    'verdict' => 'A Pepperstone tem a presença regulatória Tier-1 mais alargada (CySEC, FCA, ASIC e DFSA, contra a CySEC e a ASIC da IC Markets), um depósito mínimo muito mais baixo ($10 contra $200) e mais uma plataforma suportada (TradingView, além do MT4/MT5/cTrader). A IC Markets fica à frente no spread principal de EUR/USD que registámos (0.8 pips contra 1.1 pips da Pepperstone) e tem um histórico de operação mais longo (fundada em 2007 contra 2010) -- embora ambas as corretoras também ofereçam um escalão separado de spread bruto mais comissão, que não é captado por esse valor principal único, por isso confirme os termos atuais da conta de cada corretora para a sua configuração específica. Segundo a nossa metodologia divulgada, a Pepperstone obtém 3.64/5 no total contra 3.07/5 da IC Markets, impulsionada sobretudo pela categoria de regulação (30% da pontuação total e a maior vantagem da Pepperstone).',
                    'choose_a_heading' => 'Escolha a Pepperstone se...',
                    'choose_a_point_1' => 'Pretende uma presença regulatória mais ampla -- a Pepperstone tem autorização da CySEC, FCA, ASIC e DFSA, contra a CySEC e a ASIC da IC Markets.',
                    'choose_a_point_2' => 'Está a começar com uma conta pequena -- o depósito mínimo de $10 da Pepperstone representa uma fração dos $200 da IC Markets.',
                    'choose_a_point_3' => 'Negoceia no TradingView -- a Pepperstone suporta-o diretamente; a IC Markets não.',
                    'choose_a_point_4' => 'Uma política explícita de ausência de taxas de inatividade é importante para si -- várias fontes independentes confirmam que a Pepperstone não cobra nenhuma, sem qualquer conflito por resolver na nossa pesquisa.',
                    'choose_b_heading' => 'Escolha a IC Markets se...',
                    'choose_b_point_1' => 'O spread bruto principal é a sua prioridade e pode cumprir o mínimo de $200 -- o spread de EUR/USD de 0.8 pips da IC Markets é mais apertado do que os 1.1 pips da Pepperstone no escalão comparável.',
                    'choose_b_point_2' => 'Um histórico de operação mais longo é importante para si -- a IC Markets opera desde 2007, três anos a mais do que a Pepperstone.',
                    'choose_b_point_3' => 'Não precisa de cobertura DFSA nem de TradingView, e a ASIC mais a CySEC bastam para as suas necessidades regulatórias.',
                    'worth_knowing_heading' => 'Vale a pena saber antes de decidir',
                    'worth_knowing' => 'A entidade europeia da IC Markets publica uma Política de Taxas de Inatividade formal (uma conta é considerada inativa após 6 meses consecutivos sem qualquer negociação executada), mas várias comparações independentes indicam que, na prática, não é cobrada nenhuma taxa -- um conflito genuíno e por resolver entre a política publicada pela própria corretora e as informações de terceiros, que não conseguimos resolver na nossa pesquisa. Se o risco de inatividade for relevante para si, confirme a situação atual diretamente com a IC Markets antes de confiar em qualquer uma das fontes. A alegação de ausência de taxas de inatividade da Pepperstone, em contrapartida, é consistentemente corroborada pelas fontes que verificámos.',
                    'faq_1_q' => 'A Pepperstone ou a IC Markets são melhores para principiantes?',
                    'faq_1_a' => 'O depósito mínimo de $10 da Pepperstone torna-a muito mais acessível para começar do que os $200 da IC Markets, e a sua presença regulatória mais ampla (acrescentando FCA e DFSA às CySEC e ASIC) dá a um principiante mais cobertura regulatória à qual recorrer. Nenhuma das estruturas de conta das corretoras foi, contudo, concebida explicitamente para principiantes em vez de traders ativos.',
                    'faq_2_q' => 'Qual tem o custo de negociação mais baixo, a Pepperstone ou a IC Markets?',
                    'faq_2_a' => 'Com base no spread principal de EUR/USD que registámos, a IC Markets é mais apertada, com 0.8 pips contra 1.1 pips da Pepperstone. Ambas oferecem também um escalão separado de spread bruto mais comissão (o Razor da Pepperstone e o Raw Spread da IC Markets), com níveis de comissão em grande parte semelhantes -- consulte a tabela de taxas atual de cada corretora para o seu tipo de conta específico antes de confiar em qualquer um dos valores principais.',
                ),
        ),
    );
}

/**
 * Reassembles a flat {id: text} translation map (see
 * globalfxhub_editorial_comparison_translations() above) back into
 * the same nested structure globalfxhub_get_editorial_comparisons()
 * uses, overlaid onto the English entry as a fallback for any key the
 * translation doesn't cover (e.g. a_slug/b_slug, which never need
 * translating).
 */
function globalfxhub_get_localized_editorial_comparison( $key ) {
    $all = globalfxhub_get_editorial_comparisons();
    if ( ! isset( $all[ $key ] ) ) {
        return null;
    }
    $entry = $all[ $key ];

    $lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
    $translations = globalfxhub_editorial_comparison_translations();
    $flat = $translations[ $key ][ $lang ] ?? array();
    if ( empty( $flat ) ) {
        return $entry;
    }

    $get = function( $id, $fallback ) use ( $flat ) {
        return $flat[ $id ] ?? $fallback;
    };

    $entry['meta_title']            = $get( 'meta_title', $entry['meta_title'] );
    $entry['meta_description']      = $get( 'meta_description', $entry['meta_description'] );
    $entry['intro']                 = $get( 'intro', $entry['intro'] );
    $entry['verdict']               = $get( 'verdict', $entry['verdict'] );
    $entry['choose_a_heading']      = $get( 'choose_a_heading', $entry['choose_a_heading'] );
    $entry['choose_b_heading']      = $get( 'choose_b_heading', $entry['choose_b_heading'] );
    $entry['worth_knowing_heading'] = $get( 'worth_knowing_heading', $entry['worth_knowing_heading'] );
    $entry['worth_knowing']         = $get( 'worth_knowing', $entry['worth_knowing'] );

    foreach ( array( 'choose_a_points' => 'choose_a_point_', 'choose_b_points' => 'choose_b_point_' ) as $list_key => $prefix ) {
        $entry[ $list_key ] = array_values( array_map(
            function( $i, $point ) use ( $get, $prefix ) {
                return $get( $prefix . ( $i + 1 ), $point );
            },
            array_keys( $entry[ $list_key ] ),
            $entry[ $list_key ]
        ) );
    }

    $entry['faqs'] = array_values( array_map(
        function( $i, $faq ) use ( $get ) {
            $n = $i + 1;
            return array(
                'q' => $get( "faq_{$n}_q", $faq['q'] ),
                'a' => $get( "faq_{$n}_a", $faq['a'] ),
            );
        },
        array_keys( $entry['faqs'] ),
        $entry['faqs']
    ) );

    return $entry;
}

/**
 * Editorial comparisons involving a given broker (either side), for
 * contextual linking from that broker's own review page -- a reader
 * already looking at this broker is exactly who an editorially-curated
 * "X vs Y" verdict is for, unlike a sitewide link to the bare /compare/
 * tool. Relies on each entry's explicit 'a_slug'/'b_slug' rather than
 * parsing them back out of the array key, since a broker slug can
 * itself contain a hyphen (e.g. "ic-markets"), making the key
 * ambiguous to split.
 */
function globalfxhub_editorial_comparisons_for_broker( $slug ) {
    $matches = array();
    foreach ( globalfxhub_get_editorial_comparisons() as $key => $entry ) {
        if ( ! in_array( $slug, array( $entry['a_slug'], $entry['b_slug'] ), true ) ) {
            continue;
        }
        $other_slug = ( $entry['a_slug'] === $slug ) ? $entry['b_slug'] : $entry['a_slug'];
        $other = globalfxhub_get_broker_by_slug( $other_slug );
        if ( ! $other ) {
            continue;
        }
        $matches[] = array(
            'url'   => home_url( '/compare/' . $key . '/' ),
            'other' => $other,
        );
    }
    return $matches;
}

function globalfxhub_get_editorial_comparisons() {
    return array(
        'pepperstone-ic-markets' => array(
            'a_slug'           => 'pepperstone',
            'b_slug'           => 'ic-markets',
            'meta_title'       => 'Pepperstone vs IC Markets (2026): Which Broker Should You Choose?',
            'meta_description' => 'Pepperstone vs IC Markets compared on regulation, cost, platforms, and track record -- using our disclosed methodology, not marketing claims. See which fits your trading style.',
            'intro' => 'Pepperstone and IC Markets are both built around the same pitch: tight ECN-style pricing aimed at active and algorithmic traders, not a mass-market retail product. That similarity is exactly why they get compared so often -- but the two brokers actually differ in ways that matter more than their shared "built for serious traders" positioning suggests.',
            'verdict' => 'Pepperstone holds the wider Tier-1 regulatory footprint (CySEC, FCA, ASIC, and DFSA, against IC Markets\' CySEC and ASIC), a far lower minimum deposit ($10 vs $200), and one more supported platform (TradingView, alongside MT4/MT5/cTrader). IC Markets edges ahead on our on-file headline EUR/USD spread (0.8 pips against Pepperstone\'s 1.1), and has a longer operating history (founded 2007 vs 2010) -- though both brokers also offer a separate raw-spread-plus-commission tier not captured by that single headline figure, so check each broker\'s current account terms for your specific setup. Under our disclosed methodology, Pepperstone scores 3.64/5 overall against IC Markets\' 3.07/5, driven mainly by the regulation category (30% of the total score, and Pepperstone\'s biggest advantage).',
            'choose_a_heading' => 'Choose Pepperstone if...',
            'choose_a_points' => array(
                'You want the broader regulatory footprint -- Pepperstone holds CySEC, FCA, ASIC, and DFSA authorisation, against IC Markets\' CySEC and ASIC.',
                'You\'re starting with a small account -- Pepperstone\'s $10 minimum deposit is a fraction of IC Markets\' $200.',
                'You trade on TradingView -- Pepperstone supports it directly; IC Markets does not.',
                'An explicit no-inactivity-fee policy matters to you -- multiple independent sources confirm Pepperstone charges none, with no unresolved conflict in our research.',
            ),
            'choose_b_heading' => 'Choose IC Markets if...',
            'choose_b_points' => array(
                'Raw headline spread is your priority and you can meet the $200 minimum -- IC Markets\' 0.8-pip EUR/USD spread is tighter than Pepperstone\'s 1.1 pips on the comparable tier.',
                'A longer operating history matters to you -- IC Markets has operated since 2007, three years longer than Pepperstone.',
                'You don\'t need DFSA coverage or TradingView, and ASIC plus CySEC is enough regulatory footprint for your needs.',
            ),
            'worth_knowing_heading' => 'Worth knowing before you decide',
            'worth_knowing' => 'IC Markets\' EU entity publishes a formal Inactivity Fees Policy (an account is defined as inactive after 6 continuous months with no executed trade), but multiple independent comparison sites report that no fee is actually charged in practice -- a genuine, unresolved conflict between the broker\'s own published policy and third-party reporting that we could not resolve in our research. If dormancy risk matters to you, confirm the current position directly with IC Markets before relying on either source. Pepperstone\'s no-inactivity-fee claim, by contrast, is consistently corroborated across the sources we checked.',
            'faqs' => array(
                array(
                    'q' => 'Is Pepperstone or IC Markets better for beginners?',
                    'a' => 'Pepperstone\'s $10 minimum deposit makes it far more accessible to start with than IC Markets\' $200, and its wider regulatory footprint (adding FCA and DFSA alongside CySEC and ASIC) gives a beginner more regulatory coverage to fall back on. Neither broker\'s account structure is explicitly designed for beginners over active traders, though.',
                ),
                array(
                    'q' => 'Which has the lower trading cost, Pepperstone or IC Markets?',
                    'a' => 'On our on-file headline EUR/USD spread, IC Markets is tighter at 0.8 pips against Pepperstone\'s 1.1 pips. Both also offer a separate raw-spread-plus-commission tier (Pepperstone\'s Razor, IC Markets\' Raw Spread) with broadly similar commission levels -- check each broker\'s own current fee schedule for your specific account type before relying on either headline figure.',
                ),
            ),
        ),
    );
}
