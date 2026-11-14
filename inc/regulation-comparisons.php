<?php
/**
 * Head-to-head regulator comparisons at /regulation/{slug}/ -- same
 * URL space and query var ('regpage') as inc/regulation.php's single-
 * regulator knowledge-base pages, not a new rewrite rule. Distinct
 * from those pages: each single-regulator page (e.g. /regulation/fca/)
 * explains ONE regulator on its own; this compares TWO head-to-head
 * (compensation caps, market access, leverage/conduct rules), the
 * genuinely new angle neither a single-regulator page nor either
 * regulator's Learn-cluster explainer (fca-forex-regulation-explained,
 * cysec-forex-regulation-explained) already covers -- same reasoning
 * that kept the Pepperstone vs IC Markets editorial comparison from
 * duplicating either broker's own review.
 *
 * Every fact restated here (compensation caps, passporting, register
 * URLs) already exists in inc/regulation.php's single-regulator pages
 * -- nothing new is sourced for this comparison, only synthesized.
 *
 * Structured and flattened for translation exactly like
 * inc/broker-comparison-editorial.php and
 * inc/broker-research-articles.php (section_N_heading/body,
 * faq_N_q/faq_N_a) -- unlike inc/regulation.php's existing 5 pages,
 * which store 'content' as one opaque English-only HTML blob with no
 * translation layer at all. Deliberately not retrofitting those 5
 * existing pages to this structure right now -- that's a bigger,
 * separate optimization-queue change, not part of adding this one new
 * comparison.
 *
 * Added one regulator pair at a time, same precedent as the broker
 * comparisons/research articles -- never a template mechanically
 * applied to every regulator pairing.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_get_regulation_comparisons() {
    return array(
        'fca-vs-cysec' => array(
            'title'            => "FCA vs CySEC: What's the Difference for Forex Traders?",
            'meta_title'       => 'FCA vs CySEC (2026): Which Forex Regulator Protects Traders Better?',
            'meta_description' => 'FCA and CySEC compared on investor compensation, market access, and leverage rules -- using our regulation knowledge base, not marketing claims. See which actually matters more for your situation.',
            'intro'            => "FCA and CySEC are the two Tier-1 regulators that show up most often across the brokers we've researched -- 33 of the 139 brokers in our dataset hold both licences at once, usually through separate UK and EU entities under the same brand. They're often treated as interchangeable \"proper regulation,\" but they differ in ways that matter if you're actually choosing between two entities of the same broker, or deciding whether a broker's EU-only or UK-only credential is enough for you.",
            'sections' => array(
                array(
                    'heading' => '£85,000 vs €20,000: investor compensation',
                    'body'    => "An FCA-authorised firm's eligible retail clients are covered by the Financial Services Compensation Scheme (FSCS) up to £85,000 per person if the firm fails. A CySEC-licensed firm's eligible retail clients are covered by the Investor Compensation Fund (ICF) up to €20,000 per person instead. Both are real, statutory schemes with a hard cap -- neither is marketing language -- but they're denominated in different currencies, set at very different levels, and each applies only to the specific entity licensed by that regulator, never to an unrelated sister brand that merely shares a brand name.",
                ),
                array(
                    'heading' => 'Market access: UK-only vs EU-wide passporting',
                    'body'    => "Since Brexit, an FCA licence covers UK clients only -- it no longer carries automatic EU passporting. A CySEC licence, by contrast, lets a Cyprus Investment Firm passport its services across the entire EU/EEA under MiFID II with that one licence, without needing a separate authorisation in every member state. That's why a broker serving both UK and EU clients typically runs two separately-licensed, separately-capitalised entities rather than one licence covering both regions.",
                ),
                array(
                    'heading' => 'Leverage and conduct rules',
                    'body'    => "Both regulators apply similar ESMA-style retail leverage caps -- typically 30:1 on major currency pairs, down to 2:1 on crypto CFDs -- since the UK retained these rules after Brexit rather than diverging from them. Where they differ more is conduct: the FCA's Consumer Duty rules require firms to actively deliver good outcomes for retail customers, not merely avoid harming them, while CySEC-licensed firms follow MiFID II's conduct-of-business rules instead. In practice, this doesn't translate into a large difference in how much leverage a retail client is offered, but it is a distinct additional obligation on the FCA side.",
                ),
                array(
                    'heading' => 'Which one should you actually care about?',
                    'body'    => "Whichever entity your own account is actually held with -- not the brand's overall reputation. A UK resident's compensation protection comes from the FCA entity and the FSCS; an EU resident's comes from the CySEC entity and the ICF. If a broker holds both licences, confirm in writing exactly which legal entity your account sits under, then check that entity's specific licence number against the corresponding regulator's own register -- not the brand's general claim to be \"FCA and CySEC regulated.\"",
                ),
                array(
                    'heading' => 'How common is dual FCA/CySEC regulation?',
                    'body'    => "Of the 139 brokers in our research set, 95 hold a CySEC licence, 53 hold an FCA licence, and 33 hold both at once -- typically via separate UK and EU entities rather than one licence covering both regions. 24 of the 139 hold neither as a primary credential, though many of those still hold another Tier-1 or offshore licence elsewhere in our dataset. Holding both is common among larger, more established brokers, but it never means a single compensation scheme covers every client -- each entity's scheme only ever covers its own entity's clients.",
                ),
            ),
            'faqs' => array(
                array(
                    'q' => 'Is CySEC regulation as good as FCA regulation?',
                    'a' => "Different, not simply better or worse: CySEC covers eligible clients up to €20,000 under the ICF and carries EU-wide MiFID II passporting, while FCA covers up to £85,000 under the FSCS but applies only to UK clients post-Brexit. Which one offers more relevant protection for you depends on where you live and which entity you're actually signed up with.",
                ),
                array(
                    'q' => 'If a broker has both FCA and CySEC entities, which one protects me?',
                    'a' => "Whichever entity you actually opened your account with -- not the brand as a whole. Confirm in writing from the broker exactly which legal entity holds your account, then check that entity's specific licence number against the corresponding regulator's own register.",
                ),
                array(
                    'q' => 'How many brokers hold both an FCA and a CySEC licence?',
                    'a' => '33 of the 139 brokers in our research set hold both at once, typically through separate UK and EU entities under one brand -- see the full breakdown above.',
                ),
            ),
        ),
    );
}

/**
 * Per-language translations, same shape/purpose as
 * globalfxhub_editorial_comparison_translations() and
 * globalfxhub_broker_research_translations() -- filled in once
 * i18n/translate_regulation_comparisons.py's real translate+review
 * run completes for a given key.
 */
function globalfxhub_regulation_comparison_translations() {
    return array(
        'fca-vs-cysec' => array(
            'es' => array(
                'title' => 'FCA vs CySEC: ¿cuál es la diferencia para los traders de forex?',
                'meta_title' => 'FCA vs CySEC (2026): ¿qué regulador protege mejor a los traders de forex?',
                'meta_description' => 'Comparación de la FCA y la CySEC en compensación al inversor, acceso al mercado y normas de apalancamiento, basada en nuestra base de conocimiento sobre regulación y no en afirmaciones comerciales. Descubra cuál importa más para su situación.',
                'intro' => 'La FCA y la CySEC son los dos reguladores de Nivel 1 que más aparecen entre los brókers que hemos analizado: 33 de los 139 brókers de nuestro conjunto de datos poseen ambas licencias a la vez, normalmente a través de entidades separadas en el Reino Unido y en la UE bajo la misma marca. Con frecuencia se tratan como intercambiables como "regulación adecuada", pero se diferencian en aspectos que importan si se está eligiendo entre dos entidades del mismo bróker, o si hay que decidir si una credencial solo de la UE o solo del Reino Unido es suficiente.',
                'section_1_heading' => '£85.000 frente a €20.000: compensación al inversor',
                'section_1_body' => 'Los clientes minoristas elegibles de una empresa autorizada por la FCA están cubiertos por el Financial Services Compensation Scheme (FSCS) hasta £85.000 por persona si la empresa quiebra. Los clientes minoristas elegibles de una empresa con licencia de la CySEC están cubiertos, en cambio, por el Investor Compensation Fund (ICF) hasta €20.000 por persona. Ambos son esquemas legales reales con un límite máximo fijo, y ninguno es lenguaje publicitario. Sin embargo, están denominados en monedas distintas, fijados en niveles muy diferentes, y cada uno se aplica solo a la entidad concreta autorizada por ese regulador, nunca a una marca hermana no relacionada que simplemente comparta el nombre.',
                'section_2_heading' => 'Acceso al mercado: solo Reino Unido frente a pasaporte comunitario de la UE',
                'section_2_body' => 'Desde el Brexit, una licencia de la FCA cubre solo a los clientes del Reino Unido; ya no conlleva un pasaporte comunitario automático. En cambio, una licencia de la CySEC permite a una Cyprus Investment Firm prestar sus servicios en toda la UE y el EEE en virtud de MiFID II con esa única licencia, sin necesidad de una autorización separada en cada Estado miembro. Por eso, un bróker que atiende a clientes del Reino Unido y de la UE suele operar con dos entidades separadas, con licencias y capital independientes, en lugar de una sola licencia que cubra ambas regiones.',
                'section_3_heading' => 'Apalancamiento y normas de conducta',
                'section_3_body' => 'Ambos reguladores aplican límites de apalancamiento minorista similares, de estilo ESMA: normalmente 30:1 en los principales pares de divisas, y hasta 2:1 en CFD de criptomonedas, ya que el Reino Unido mantuvo estas normas tras el Brexit en lugar de apartarse de ellas. Donde más se diferencian es en la conducta: las normas Consumer Duty de la FCA exigen que las empresas logren activamente buenos resultados para los clientes minoristas, y no solo que eviten perjudicarlos, mientras que las empresas con licencia de la CySEC, en cambio, siguen las normas de conducta empresarial de MiFID II. En la práctica, esto no se traduce en una gran diferencia en el apalancamiento que se ofrece a un cliente minorista, pero sí supone una obligación adicional y distinta por parte de la FCA.',
                'section_4_heading' => '¿Cuál le importa realmente?',
                'section_4_body' => 'Aquella entidad con la que tenga realmente abierta su cuenta, y no la reputación general de la marca. La protección en caso de compensación de un residente del Reino Unido proviene de la entidad de la FCA y del FSCS; la de un residente de la UE, de la entidad de la CySEC y del ICF. Si un bróker posee ambas licencias, confirme por escrito a qué entidad jurídica pertenece exactamente su cuenta y, a continuación, compruebe el número de licencia de esa entidad en el registro del regulador correspondiente, y no la afirmación general de la marca de estar "regulada por la FCA y la CySEC".',
                'section_5_heading' => '¿Qué tan común es la regulación dual FCA/CySEC?',
                'section_5_body' => 'De los 139 brókers de nuestro conjunto de investigación, 95 poseen una licencia de la CySEC, 53 tienen una licencia de la FCA y 33 poseen ambas a la vez, normalmente a través de entidades separadas en el Reino Unido y en la UE, en lugar de una sola licencia que cubra ambas regiones. 24 de los 139 no tienen ninguna de estas dos como credencial principal, aunque muchos de ellos sí poseen otra licencia de Nivel 1 u offshore en otra parte de nuestros datos. Poseer ambas es habitual entre brókers más grandes y consolidados, pero nunca significa que un único esquema de compensación cubra a todos los clientes: el esquema de cada entidad solo cubre a los clientes de esa misma entidad.',
                'faq_1_q' => '¿Es la regulación de la CySEC tan buena como la de la FCA?',
                'faq_1_a' => 'Son diferentes, no simplemente mejores o peores: la CySEC cubre a los clientes elegibles hasta €20.000 a través del ICF y cuenta con pasaporte comunitario en la UE en virtud de MiFID II, mientras que la FCA cubre hasta £85.000 a través del FSCS, pero solo se aplica a los clientes del Reino Unido tras el Brexit. Cuál ofrece una protección más relevante depende del lugar de residencia y de la entidad con la que esté realmente registrado.',
                'faq_2_q' => 'Si un bróker tiene entidades de la FCA y de la CySEC, ¿cuál me protege?',
                'faq_2_a' => 'Aquella entidad con la que realmente abrió su cuenta, y no la marca en su conjunto. Confirme por escrito con el bróker qué entidad jurídica tiene su cuenta y, a continuación, compruebe el número de licencia de esa entidad en el registro del regulador correspondiente.',
                'faq_3_q' => '¿Cuántos brókers poseen tanto una licencia de la FCA como una de la CySEC?',
                'faq_3_a' => '33 de los 139 brókers de nuestro conjunto de investigación poseen ambas a la vez, normalmente a través de entidades separadas en el Reino Unido y en la UE bajo una misma marca; consulte el desglose completo más arriba.',
            ),
            'de' => array(
                'title' => 'FCA vs. CySEC: Was ist der Unterschied für Forex-Trader?',
                'meta_title' => 'FCA vs. CySEC (2026): Welche Forex-Regulierung schützt Trader besser?',
                'meta_description' => 'FCA und CySEC im Vergleich: Anlegerentschädigung, Marktzugang und Hebelregeln -- auf Basis unserer Regulierungsdatenbank, nicht auf Marketingversprechen. Erfahren Sie, was für Ihre Situation tatsächlich wichtiger ist.',
                'intro' => 'FCA und CySEC sind die beiden Tier-1-Regulierungsbehörden, die bei den von uns geprüften Brokern am häufigsten vorkommen -- 33 der 139 Broker in unserem Datensatz besitzen beide Lizenzen gleichzeitig, meist über getrennte britische und EU-Gesellschaften unter derselben Marke. Sie werden oft als austauschbare "richtige Regulierung" behandelt, unterscheiden sich jedoch in Punkten, die wichtig sind, wenn Sie zwischen zwei Gesellschaften desselben Brokers wählen oder entscheiden müssen, ob die rein EU- oder rein britische Zulassung eines Brokers für Sie ausreicht.',
                'section_1_heading' => '£85.000 vs. €20.000: Anlegerentschädigung',
                'section_1_body' => 'Berechtigte Privatkunden einer von der FCA zugelassenen Firma sind im Insolvenzfall bis zu £85.000 pro Person durch das Financial Services Compensation Scheme (FSCS) abgesichert. Berechtigte Privatkunden einer bei CySEC lizenzierten Firma sind dagegen bis zu €20.000 pro Person durch den Investor Compensation Fund (ICF) abgesichert. Beide sind reale, gesetzlich verankerte Systeme mit einer festen Obergrenze -- keines davon ist Marketingsprache -- doch sie sind in unterschiedlichen Währungen ausgewiesen und auf sehr unterschiedlichem Niveau festgelegt, und jedes gilt nur für die jeweils von dieser Behörde lizenzierte Gesellschaft, niemals für eine unabhängige Schwestermarke, die lediglich denselben Markennamen trägt.',
                'section_2_heading' => 'Marktzugang: nur Großbritannien vs. EU-weites Passporting',
                'section_2_body' => 'Seit dem Brexit gilt eine FCA-Lizenz nur für britische Kunden -- sie umfasst kein automatisches EU-Passporting mehr. Eine CySEC-Lizenz dagegen erlaubt es einer zyprischen Wertpapierfirma (Cyprus Investment Firm), ihre Dienstleistungen mit dieser einen Lizenz unter MiFID II im gesamten EU/EWR-Raum anzubieten, ohne in jedem Mitgliedstaat eine eigene Zulassung zu benötigen. Deshalb betreibt ein Broker, der sowohl britische als auch EU-Kunden bedient, in der Regel zwei getrennt lizenzierte und getrennt kapitalisierte Gesellschaften, statt einer einzigen Lizenz für beide Regionen.',
                'section_3_heading' => 'Hebel und Verhaltensregeln',
                'section_3_body' => 'Beide Regulierungsbehörden wenden ähnliche, an ESMA angelehnte Hebelobergrenzen für Privatkunden an -- typischerweise 30:1 bei Hauptwährungspaaren und bis zu 2:1 bei Krypto-CFDs --, da das Vereinigte Königreich diese Regeln nach dem Brexit beibehalten hat, statt sich von ihnen zu entfernen. Deutlicher unterscheiden sie sich beim Verhalten: Die Consumer-Duty-Regeln der FCA verpflichten Firmen, für Privatkunden aktiv gute Ergebnisse zu erzielen und nicht nur Schaden zu vermeiden, während CySEC-lizenzierte Firmen stattdessen die Wohlverhaltensregeln von MiFID II befolgen. In der Praxis führt dies nicht zu einem großen Unterschied beim Hebel, der einem Privatkunden angeboten wird, wohl aber zu einer zusätzlichen, eigenständigen Pflicht auf Seiten der FCA.',
                'section_4_heading' => 'Worauf Sie tatsächlich achten sollten',
                'section_4_body' => 'Darauf, bei welcher Gesellschaft Ihr eigenes Konto tatsächlich geführt wird -- nicht auf den allgemeinen Ruf der Marke. Der Entschädigungsschutz eines Kunden mit Wohnsitz im Vereinigten Königreich stammt von der FCA-Gesellschaft und dem FSCS; der eines EU-Kunden von der CySEC-Gesellschaft und dem ICF. Wenn ein Broker beide Lizenzen besitzt, bestätigen Sie schriftlich, welche juristische Person Ihr Konto führt, und prüfen Sie dann die spezifische Lizenznummer dieser Gesellschaft im Register der jeweiligen Behörde -- nicht die allgemeine Behauptung der Marke, "FCA- und CySEC-reguliert" zu sein.',
                'section_5_heading' => 'Wie verbreitet ist die duale FCA/CySEC-Regulierung?',
                'section_5_body' => 'Von den 139 Brokern in unserem Recherchesatz besitzen 95 eine CySEC-Lizenz, 53 eine FCA-Lizenz und 33 beide gleichzeitig -- typischerweise über getrennte britische und EU-Gesellschaften statt über eine Lizenz für beide Regionen. 24 der 139 besitzen keine dieser Lizenzen als primären Nachweis, auch wenn viele davon an anderer Stelle in unserem Datensatz eine andere Tier-1- oder Offshore-Lizenz halten. Dass beide Lizenzen vorliegen, ist bei größeren, etablierteren Brokern üblich, bedeutet aber nie, dass ein einziges Entschädigungssystem alle Kunden abdeckt -- das System jeder Gesellschaft gilt immer nur für die Kunden eben dieser Gesellschaft.',
                'faq_1_q' => 'Ist die CySEC-Regulierung so gut wie die FCA-Regulierung?',
                'faq_1_a' => 'Unterschiedlich, nicht einfach besser oder schlechter: CySEC deckt berechtigte Kunden bis €20.000 über den ICF ab und bietet EU-weites MiFID-II-Passporting, während die FCA bis £85.000 über den FSCS absichert, aber nach dem Brexit nur für britische Kunden gilt. Welcher Schutz für Sie relevanter ist, hängt davon ab, wo Sie leben und bei welcher Gesellschaft Sie tatsächlich angemeldet sind.',
                'faq_2_q' => 'Wenn ein Broker sowohl FCA- als auch CySEC-Gesellschaften hat, welche schützt mich?',
                'faq_2_a' => 'Diejenige Gesellschaft, bei der Sie Ihr Konto tatsächlich eröffnet haben -- nicht die Marke als Ganzes. Lassen Sie sich vom Broker schriftlich bestätigen, welche juristische Person Ihr Konto führt, und prüfen Sie dann die spezifische Lizenznummer dieser Gesellschaft im Register der jeweiligen Behörde.',
                'faq_3_q' => 'Wie viele Broker besitzen sowohl eine FCA- als auch eine CySEC-Lizenz?',
                'faq_3_a' => '33 der 139 Broker in unserem Recherchesatz besitzen beide gleichzeitig, meist über getrennte britische und EU-Gesellschaften unter einer Marke -- die vollständige Aufschlüsselung finden Sie oben.',
            ),
            'ar' => array(
                'title' => 'FCA مقابل CySEC: ما الفرق بالنسبة لمتداولي الفوركس؟',
                'meta_title' => 'FCA مقابل CySEC (2026): أي جهة تنظيم للفوركس تحمي المتداولين بشكل أفضل؟',
                'meta_description' => 'مقارنة بين FCA وCySEC من حيث تعويض المستثمرين وإمكانية الوصول إلى الأسواق وقواعد الرافعة المالية -- استنادًا إلى قاعدة معرفتنا بالتنظيم، لا إلى ادعاءات تسويقية. اكتشف أيهما أكثر أهمية لوضعك.',
                'intro' => 'FCA وCySEC هما الجهتان التنظيميتان من الدرجة الأولى الأكثر تكرارًا بين الوسطاء الذين درسناهم -- إذ يحمل 33 من أصل 139 وسيطًا في قاعدة بياناتنا الترخيصين معًا، عادةً عبر كيانين منفصلين في المملكة المتحدة والاتحاد الأوروبي تحت العلامة التجارية نفسها. وغالبًا ما يُنظر إليهما على أنهما «تنظيم سليم» قابل للتبادل، لكنهما يختلفان في جوانب مهمة إذا كنت تختار بين كيانين تابعين للوسيط نفسه، أو تتساءل عما إذا كان اعتماد الوسيط على الاتحاد الأوروبي فقط أو المملكة المتحدة فقط كافيًا بالنسبة لك.',
                'section_1_heading' => '£85,000 مقابل €20,000: تعويض المستثمرين',
                'section_1_body' => 'يحصل العملاء الأفراد المؤهلون لدى الشركات المرخصة من FCA على تغطية من مخطط تعويض الخدمات المالية (FSCS) حتى £85,000 لكل شخص إذا تعثرت الشركة. أما العملاء الأفراد المؤهلون لدى الشركات المرخصة من CySEC فتغطيهم بدلًا من ذلك صندوق تعويض المستثمرين (ICF) حتى €20,000 لكل شخص. كلاهما مخططان قانونيان حقيقيان بسقف محدد -- وليس أيٌّ منهما لغة تسويقية -- لكنهما مقوّمان بعملات مختلفة، ومحددان بمستويات مختلفة تمامًا، ويُطبّق كل منهما فقط على الكيان المحدد المرخّص من تلك الجهة التنظيمية، لا على شركة شقيقة غير ذات صلة تشارك الاسم التجاري فحسب.',
                'section_2_heading' => 'الوصول إلى الأسواق: المملكة المتحدة فقط مقابل جواز المرور التنظيمي في الاتحاد الأوروبي',
                'section_2_body' => 'منذ بريكست، لم يعد ترخيص FCA يشمل إلا العملاء في المملكة المتحدة، ولم يعد يتيح جواز المرور التنظيمي التلقائي إلى الاتحاد الأوروبي. أما ترخيص CySEC فيسمح، على العكس من ذلك، لشركة استثمار قبرصية بتقديم خدماتها عبر الاتحاد الأوروبي/المنطقة الاقتصادية الأوروبية بالكامل بموجب MiFID II بهذا الترخيص الواحد، دون الحاجة إلى تفويض منفصل في كل دولة عضو. ولهذا السبب يدير الوسيط الذي يخدم عملاء في المملكة المتحدة والاتحاد الأوروبي عادةً كيانين مرخصين بشكل منفصل ومُمولين برأسمال منفصل، بدلًا من ترخيص واحد يغطي المنطقتين.',
                'section_3_heading' => 'الرافعة المالية وقواعد السلوك',
                'section_3_body' => 'تطبّق الجهتان حدودًا مماثلة للرافعة المالية للتجزئة على غرار ESMA -- عادةً 30:1 على أزواج العملات الرئيسية، وصولًا إلى 2:1 على CFDs للعملات المشفرة -- إذ أبقت المملكة المتحدة على هذه القواعد بعد بريكست بدلًا من الابتعاد عنها. أما الاختلاف الأكبر فيكمن في السلوك: إذ تُلزم قواعد الواجب تجاه المستهلك لدى FCA الشركات بتحقيق نتائج جيدة فعليًا للعملاء الأفراد، لا مجرد تجنب الإضرار بهم، بينما تتبع الشركات المرخصة من CySEC بدلًا من ذلك قواعد سلوك ممارسة الأعمال المنصوص عليها في MiFID II. وعمليًا، لا يترجم هذا إلى فرق كبير في مقدار الرافعة المالية المتاحة للعميل الفرد، لكنه يمثل التزامًا إضافيًا مميزًا على عاتق FCA.',
                'section_4_heading' => 'أيهما ينبغي أن يهمّك فعلًا؟',
                'section_4_body' => 'الكيان الذي يُحتفظ فيه بحسابك فعليًا -- وليس السمعة العامة للعلامة التجارية. تأتي حماية تعويض المقيم في المملكة المتحدة من كيان FCA ومن FSCS؛ أما تعويض المقيم في الاتحاد الأوروبي فيأتي من كيان CySEC ومن ICF. إذا كان الوسيط يحمل الترخيصين معًا، فتحقق كتابيًا من الكيان القانوني الذي يندرج حسابك تحته تحديدًا، ثم تحقق من رقم ترخيص ذلك الكيان المحدد في سجل الجهة التنظيمية المقابلة له -- لا من ادعاء العلامة التجارية العام بأنها «منظمة من FCA وCySEC».',
                'section_5_heading' => 'ما مدى شيوع التنظيم المزدوج من FCA وCySEC؟',
                'section_5_body' => 'من بين 139 وسيطًا في مجموعة بحثنا، يحمل 95 ترخيص CySEC، ويحمل 53 ترخيص FCA، ويحمل 33 الترخيصين معًا -- عادةً عبر كيانات منفصلة في المملكة المتحدة والاتحاد الأوروبي، لا عبر ترخيص واحد يغطي المنطقتين. ولا يحمل 24 من أصل 139 أيًّا من الترخيصين كاعتماد أساسي، رغم أن كثيرًا منها يحمل ترخيصًا آخر من الدرجة الأولى أو ترخيصًا أوفشور في موضع آخر من قاعدة بياناتنا. ويُعد الجمع بين الترخيصين شائعًا لدى الوسطاء الأكبر والأكثر رسوخًا، لكنه لا يعني أبدًا أن مخططًا تعويضيًا واحدًا يغطي كل العملاء -- إذ لا يغطي مخطط كل كيان إلا عملاء ذلك الكيان نفسه.',
                'faq_1_q' => 'هل تنظيم CySEC بنفس جودة تنظيم FCA؟',
                'faq_1_a' => 'مختلفان، وليس أحدهما أفضل أو أسوأ من الآخر ببساطة: يغطي CySEC العملاء المؤهلين حتى €20,000 بموجب ICF، ويتمتع بجواز المرور التنظيمي عبر الاتحاد الأوروبي بموجب MiFID II، بينما تغطي FCA حتى £85,000 بموجب FSCS لكنها تنطبق فقط على عملاء المملكة المتحدة بعد بريكست. ويعتمد أيّ الخيارين يوفر لك حماية أوفى على مكان إقامتك والكيان الذي سجّلت فيه فعليًا.',
                'faq_2_q' => 'إذا كان لدى وسيط كيانات تابعة لـ FCA وCySEC معًا، فأيهما يحميني؟',
                'faq_2_a' => 'الكيان الذي فتحت حسابك لديه فعليًا -- وليس العلامة التجارية ككل. تحقق كتابيًا من الوسيط من الكيان القانوني الذي يحتفظ بحسابك تحديدًا، ثم تحقق من رقم ترخيص ذلك الكيان في سجل الجهة التنظيمية المقابلة له.',
                'faq_3_q' => 'كم عدد الوسطاء الذين يحملون ترخيصي FCA وCySEC معًا؟',
                'faq_3_a' => 'يحمل 33 من أصل 139 وسيطًا في مجموعة بحثنا الترخيصين معًا، عادةً عبر كيانات منفصلة في المملكة المتحدة والاتحاد الأوروبي تحت علامة تجارية واحدة -- راجع التفصيل الكامل أعلاه.',
            ),
            'fr' => array(
                'title' => 'FCA vs CySEC : quelle est la différence pour les traders forex ?',
                'meta_title' => 'FCA vs CySEC (2026) : quel régulateur forex protège mieux les traders ?',
                'meta_description' => 'FCA et CySEC comparés sur l\'indemnisation des investisseurs, l\'accès au marché et les règles relatives à l\'effet de levier, à partir de notre base de connaissances sur la réglementation et non sur des affirmations commerciales. Découvrez lequel compte vraiment davantage dans votre situation.',
                'intro' => 'La FCA et la CySEC sont les deux régulateurs de premier rang (Tier-1) que l\'on retrouve le plus souvent parmi les courtiers que nous avons analysés : 33 des 139 courtiers de notre base de données détiennent les deux licences à la fois, généralement par l\'intermédiaire d\'entités distinctes au Royaume-Uni et dans l\'UE sous la même marque. On les présente souvent comme interchangeables, sous l\'étiquette de « véritable réglementation », mais elles diffèrent sur des points importants si vous devez choisir entre deux entités d\'un même courtier, ou si vous vous demandez si l\'agrément limité à l\'UE ou au seul Royaume-Uni d\'un courtier vous suffit.',
                'section_1_heading' => '85 000 £ contre 20 000 € : indemnisation des investisseurs',
                'section_1_body' => 'Les clients particuliers éligibles d\'une société agréée par la FCA sont couverts par le Financial Services Compensation Scheme (FSCS) à hauteur de 85 000 £ par personne si la société fait défaut. Les clients particuliers éligibles d\'une société agréée par la CySEC sont en revanche couverts par le Fonds d\'indemnisation des investisseurs (ICF) à hauteur de 20 000 € par personne. Ces deux dispositifs sont réels et légaux, avec un plafond strict : il ne s\'agit pas d\'un argument commercial. Ils sont toutefois libellés dans des devises différentes, fixés à des niveaux très différents, et chacun ne s\'applique qu\'à l\'entité précise agréée par ce régulateur, jamais à une marque sœur sans lien qui partage simplement le même nom.',
                'section_2_heading' => 'Accès au marché : Royaume-Uni uniquement contre passeport européen',
                'section_2_body' => 'Depuis le Brexit, une licence FCA ne couvre que les clients britanniques : elle n\'ouvre plus automatiquement de passeport européen. En revanche, une licence CySEC permet à une société d\'investissement chypriote de proposer ses services dans l\'ensemble de l\'UE et de l\'EEE au titre de MiFID II, avec cette seule licence, sans autorisation distincte dans chaque État membre. C\'est pourquoi un courtier qui sert à la fois des clients britanniques et européens exploite généralement deux entités distinctes, agréées et capitalisées séparément, plutôt qu\'une seule licence couvrant les deux régions.',
                'section_3_heading' => 'Effet de levier et règles de conduite',
                'section_3_body' => 'Les deux régulateurs appliquent des plafonds d\'effet de levier pour les particuliers similaires à ceux de l\'ESMA, généralement de 30:1 sur les principales paires de devises, jusqu\'à 2:1 sur les CFD sur cryptomonnaies, car le Royaume-Uni a conservé ces règles après le Brexit plutôt que de s\'en écarter. Leurs différences portent davantage sur la conduite : les règles Consumer Duty de la FCA obligent les sociétés à produire activement de bons résultats pour les clients particuliers, et non pas seulement à éviter de leur nuire, tandis que les sociétés agréées par la CySEC suivent à la place les règles de conduite de MiFID II. En pratique, cela ne se traduit pas par un écart important du niveau d\'effet de levier proposé à un client particulier, mais il s\'agit d\'une obligation supplémentaire distincte du côté de la FCA.',
                'section_4_heading' => 'Lequel devez-vous vraiment prendre en compte ?',
                'section_4_body' => 'C\'est l\'entité auprès de laquelle votre compte est effectivement détenu, et non la réputation générale de la marque. La protection en cas d\'indemnisation d\'un résident britannique provient de l\'entité FCA et du FSCS ; celle d\'un résident de l\'UE provient de l\'entité CySEC et de l\'ICF. Si un courtier détient les deux licences, demandez-lui par écrit quelle est l\'entité juridique exacte sous laquelle votre compte est ouvert, puis vérifiez le numéro de licence de cette entité dans le registre du régulateur correspondant, et non pas la simple affirmation de la marque d\'être « régulée par la FCA et la CySEC ».',
                'section_5_heading' => 'Quelle est la fréquence de la double réglementation FCA/CySEC ?',
                'section_5_body' => 'Parmi les 139 courtiers de notre échantillon de recherche, 95 détiennent une licence CySEC, 53 une licence FCA, et 33 les deux à la fois, généralement par l\'intermédiaire d\'entités distinctes au Royaume-Uni et dans l\'UE plutôt qu\'une licence unique couvrant les deux régions. 24 des 139 ne détiennent aucune de ces deux licences comme agrément principal, même si beaucoup d\'entre eux disposent d\'une autre licence de premier rang ou offshore ailleurs dans notre base de données. Détenir les deux licences est courant chez les courtiers les plus importants et les plus établis, mais cela ne signifie jamais qu\'un seul dispositif d\'indemnisation couvre tous les clients : le dispositif de chaque entité ne couvre que les clients de cette même entité.',
                'faq_1_q' => 'La réglementation de la CySEC est-elle aussi solide que celle de la FCA ?',
                'faq_1_a' => 'Elles sont différentes, sans être simplement meilleures ou moins bonnes : la CySEC couvre les clients éligibles à hauteur de 20 000 € au titre de l\'ICF et bénéficie du passeport MiFID II à l\'échelle de l\'UE, tandis que la FCA couvre jusqu\'à 85 000 £ au titre du FSCS mais ne s\'applique qu\'aux clients britanniques depuis le Brexit. Celle qui offre la protection la plus pertinente pour vous dépend du lieu où vous résidez et de l\'entité auprès de laquelle vous êtes effectivement inscrit.',
                'faq_2_q' => 'Si un courtier dispose d\'entités FCA et CySEC, laquelle me protège ?',
                'faq_2_a' => 'Celle auprès de laquelle vous avez réellement ouvert votre compte, et non la marque dans son ensemble. Demandez par écrit au courtier l\'entité juridique exacte qui détient votre compte, puis vérifiez le numéro de licence de cette entité dans le registre du régulateur correspondant.',
                'faq_3_q' => 'Combien de courtiers détiennent à la fois une licence FCA et une licence CySEC ?',
                'faq_3_a' => '33 des 139 courtiers de notre échantillon de recherche détiennent les deux à la fois, généralement par l\'intermédiaire d\'entités distinctes au Royaume-Uni et dans l\'UE sous une même marque : voir la ventilation complète ci-dessus.',
            ),
            'pl' => array(
                'title' => 'FCA vs CySEC: Jaka jest różnica dla traderów forex?',
                'meta_title' => 'FCA vs CySEC (2026): który regulator lepiej chroni traderów forex?',
                'meta_description' => 'Porównanie FCA i CySEC pod kątem kompensacji dla inwestorów, dostępu do rynku oraz zasad dotyczących lewarowania -- na podstawie naszej bazy wiedzy o regulacjach, a nie deklaracji marketingowych. Sprawdź, co faktycznie ma większe znaczenie w Twojej sytuacji.',
                'intro' => 'FCA i CySEC to dwaj regulatorzy Tier-1, którzy najczęściej pojawiają się wśród badanych przez nas brokerów -- 33 spośród 139 brokerów w naszym zbiorze danych posiada jednocześnie obie licencje, zwykle za pośrednictwem odrębnych podmiotów brytyjskich i unijnych działających pod tą samą marką. Często traktuje się je jako wymienne "odpowiednie regulacje", jednak różnią się w sposób istotny, jeśli wybierasz między dwoma podmiotami tego samego brokera lub zastanawiasz się, czy wyłącznie unijna albo wyłącznie brytyjska licencja brokera jest dla Ciebie wystarczająca.',
                'section_1_heading' => '£85,000 vs €20,000: kompensacja dla inwestorów',
                'section_1_body' => 'Uprawnieni klienci detaliczni firmy autoryzowanej przez FCA są objęci ochroną Financial Services Compensation Scheme (FSCS) do £85,000 na osobę, jeśli firma upadnie. Uprawnieni klienci detaliczni firmy posiadającej licencję CySEC są natomiast objęci ochroną Investor Compensation Fund (ICF) do €20,000 na osobę. Oba systemy są rzeczywiste i ustawowe, mają sztywny limit -- żadne z tych określeń nie jest sformułowaniem marketingowym -- ale są wyrażone w różnych walutach, ustalone na zupełnie różnych poziomach, a każdy z nich dotyczy wyłącznie podmiotu objętego licencją danego regulatora, nigdy zaś niezwiązanej z nim spółki siostrzanej, która jedynie dzieli nazwę marki.',
                'section_2_heading' => 'Dostęp do rynku: ograniczony do Wielkiej Brytanii vs paszport UE',
                'section_2_body' => 'Od czasu Brexitu licencja FCA obejmuje wyłącznie klientów z Wielkiej Brytanii -- nie daje już automatycznego paszportu UE. Licencja CySEC natomiast pozwala cypryjskiej firmie inwestycyjnej świadczyć usługi na terenie całej UE/EOG na podstawie MiFID II, dzięki jednej licencji, bez konieczności uzyskiwania odrębnej autoryzacji w każdym państwie członkowskim. Dlatego broker obsługujący zarówno klientów brytyjskich, jak i unijnych zwykle prowadzi dwa odrębnie licencjonowane podmioty o odrębnym kapitale, a nie jedną licencję obejmującą oba regiony.',
                'section_3_heading' => 'Lewarowanie i zasady postępowania wobec klientów',
                'section_3_body' => 'Obaj regulatorzy stosują podobne limity lewarowania dla klientów detalicznych, zbliżone do wytycznych ESMA -- zazwyczaj 30:1 dla głównych par walutowych, spadając do 2:1 w przypadku CFD na kryptowaluty -- ponieważ po Brexicie Wielka Brytania zachowała te zasady, zamiast od nich odstępować. Istotniejsza różnica dotyczy postępowania: zasady Consumer Duty FCA wymagają od firm aktywnego zapewniania dobrych rezultatów dla klientów detalicznych, a nie wyłącznie unikania szkodzenia im, podczas gdy firmy objęte licencją CySEC podlegają natomiast zasadom prowadzenia działalności wynikającym z MiFID II. W praktyce nie przekłada się to na dużą różnicę w wysokości lewarowania oferowanego klientowi detalicznemu, lecz po stronie FCA jest to wyraźnie dodatkowy obowiązek.',
                'section_4_heading' => 'Na którym z nich naprawdę powinno Ci zależeć?',
                'section_4_body' => 'Na tym podmiocie, w którym faktycznie prowadzone jest Twoje konto -- a nie na ogólnej reputacji marki. Rezydent Wielkiej Brytanii jest chroniony przez podmiot FCA i system FSCS; rezydent UE -- przez podmiot CySEC i ICF. Jeśli broker posiada obie licencje, potwierdź na piśmie, który dokładnie podmiot prawny obsługuje Twoje konto, a następnie sprawdź numer licencji tego podmiotu w rejestrze odpowiedniego regulatora -- a nie ogólne twierdzenie marki o regulacji przez "FCA i CySEC".',
                'section_5_heading' => 'Jak powszechna jest podwójna regulacja FCA/CySEC?',
                'section_5_body' => 'Spośród 139 brokerów w naszym zbiorze badawczym 95 posiada licencję CySEC, 53 licencję FCA, a 33 obie jednocześnie -- zwykle za pośrednictwem odrębnych podmiotów brytyjskich i unijnych, a nie jednej licencji obejmującej oba regiony. 24 spośród 139 nie posiada żadnej z tych licencji jako podstawowego uprawnienia, choć wielu z nich posiada gdzie indziej inną licencję Tier-1 lub offshore w naszym zbiorze danych. Posiadanie obu jest powszechne wśród większych, bardziej ugruntowanych brokerów, ale nigdy nie oznacza, że jeden system kompensacji obejmuje wszystkich klientów -- system każdego podmiotu obejmuje wyłącznie klientów tego podmiotu.',
                'faq_1_q' => 'Czy regulacja CySEC jest równie dobra jak regulacja FCA?',
                'faq_1_a' => 'Różnią się, a nie są po prostu lepsze lub gorsze: CySEC obejmuje uprawnionych klientów do €20,000 w ramach ICF i zapewnia paszport MiFID II na terenie całej UE, natomiast FCA obejmuje do £85,000 w ramach FSCS, ale dotyczy wyłącznie klientów z Wielkiej Brytanii po Brexicie. Która z nich zapewnia Ci bardziej adekwatną ochronę, zależy od miejsca zamieszkania i od tego, z którym podmiotem faktycznie podpisałeś umowę.',
                'faq_2_q' => 'Jeśli broker ma podmioty FCA i CySEC, który z nich mnie chroni?',
                'faq_2_a' => 'Ten podmiot, w którym faktycznie otworzyłeś konto -- a nie marka jako całość. Potwierdź na piśmie u brokera, który dokładnie podmiot prawny prowadzi Twoje konto, a następnie sprawdź numer licencji tego podmiotu w rejestrze odpowiedniego regulatora.',
                'faq_3_q' => 'Ilu brokerów posiada zarówno licencję FCA, jak i CySEC?',
                'faq_3_a' => '33 spośród 139 brokerów w naszym zbiorze badawczym posiada obie licencje jednocześnie, zwykle za pośrednictwem odrębnych podmiotów brytyjskich i unijnych działających pod jedną marką -- pełne zestawienie znajduje się powyżej.',
            ),
            'it' => array(
                'title' => 'FCA vs CySEC: qual è la differenza per i trader forex?',
                'meta_title' => 'FCA vs CySEC (2026): quale regolatore forex tutela meglio i trader?',
                'meta_description' => 'FCA e CySEC a confronto su compensazione degli investitori, accesso al mercato e regole sulla leva finanziaria, basandoci sulla nostra base di conoscenze sulla regolamentazione e non sulle promesse di marketing. Scopri quale conta davvero di più nella tua situazione.',
                'intro' => 'FCA e CySEC sono i due regolatori di Tier-1 che compaiono più spesso tra i broker che abbiamo analizzato: 33 dei 139 broker del nostro dataset detengono entrambe le licenze contemporaneamente, di solito tramite entità separate nel Regno Unito e nell\'UE sotto lo stesso marchio. Vengono spesso trattati come equivalenti, come «regolamentazione seria», ma differiscono in aspetti importanti se stai scegliendo tra due entità dello stesso broker, o se ti chiedi se la credenziale solo UE o solo UK di un broker sia sufficiente per te.',
                'section_1_heading' => '£85.000 vs €20.000: compensazione degli investitori',
                'section_1_body' => 'I clienti retail idonei di un\'impresa autorizzata dalla FCA sono coperti dal Financial Services Compensation Scheme (FSCS) fino a £85.000 per persona in caso di fallimento dell\'impresa. I clienti retail idonei di un\'impresa con licenza CySEC sono invece coperti dall\'Investor Compensation Fund (ICF) fino a €20.000 per persona. Entrambi sono schemi previsti dalla legge, reali e con un limite massimo rigido, non linguaggio di marketing, ma sono espressi in valute diverse, fissati a livelli molto diversi e ciascuno si applica solo all\'entità specifica autorizzata da quel regolatore, e mai a un marchio sorella non collegato che condivide semplicemente il nome.',
                'section_2_heading' => 'Accesso al mercato: solo Regno Unito vs passaporto UE',
                'section_2_body' => 'Dopo la Brexit, una licenza FCA copre solo i clienti del Regno Unito e non prevede più il passaporto automatico nell\'UE. Una licenza CySEC, al contrario, consente a un\'impresa di investimento cipriota di offrire i propri servizi in tutta l\'UE/SEE ai sensi della MiFID II con un\'unica licenza, senza dover ottenere un\'autorizzazione separata in ogni Stato membro. Per questo un broker che serve clienti sia del Regno Unito sia dell\'UE di solito opera con due entità separatamente autorizzate e separatamente capitalizzate, anziché con un\'unica licenza che copre entrambe le aree.',
                'section_3_heading' => 'Leva finanziaria e regole di condotta',
                'section_3_body' => 'Entrambi i regolatori applicano limiti simili alla leva finanziaria al dettaglio in stile ESMA, tipicamente 30:1 sulle principali coppie di valute, fino a 2:1 sui CFD su criptovalute, perché il Regno Unito ha mantenuto queste regole dopo la Brexit invece di discostarsene. La differenza maggiore riguarda la condotta: le regole Consumer Duty della FCA impongono alle imprese di garantire attivamente buoni risultati ai clienti retail, non solo di evitare di danneggiarli, mentre le imprese con licenza CySEC seguono invece le regole di condotta commerciale della MiFID II. Nella pratica questo non si traduce in una grande differenza nella leva offerta a un cliente retail, ma da parte della FCA costituisce un obbligo aggiuntivo distinto.',
                'section_4_heading' => 'Quale dovrebbe davvero interessarti?',
                'section_4_body' => 'Conta l\'entità presso cui è effettivamente aperto il tuo conto, non la reputazione complessiva del marchio. Un residente nel Regno Unito ottiene la protezione della compensazione dall\'entità FCA e dal FSCS; un residente nell\'UE la ottiene dall\'entità CySEC e dall\'ICF. Se un broker detiene entrambe le licenze, verifica per iscritto quale entità giuridica gestisce il tuo conto, quindi controlla il numero di licenza specifico di quell\'entità nel registro del regolatore corrispondente, non l\'affermazione generica del marchio di essere «regolamentato da FCA e CySEC».',
                'section_5_heading' => 'Quanto è diffusa la doppia regolamentazione FCA/CySEC?',
                'section_5_body' => 'Dei 139 broker del nostro campione di ricerca, 95 detengono una licenza CySEC, 53 una licenza FCA e 33 entrambe contemporaneamente, di solito tramite entità separate nel Regno Unito e nell\'UE anziché con un\'unica licenza che copra entrambe le aree. 24 dei 139 non hanno nessuna delle due come credenziale principale, anche se molti di loro detengono comunque un\'altra licenza di Tier-1 o offshore altrove nel nostro dataset. Detenere entrambe è comune tra i broker più grandi e consolidati, ma non significa mai che un unico schema di compensazione copra tutti i clienti: lo schema di ogni entità copre solo i clienti di quella stessa entità.',
                'faq_1_q' => 'La regolamentazione CySEC è buona quanto quella FCA?',
                'faq_1_a' => 'Si tratta di tutele diverse, non semplicemente migliori o peggiori: CySEC copre i clienti idonei fino a €20.000 tramite l\'ICF e prevede il passaporto MiFID II in tutta l\'UE, mentre la FCA copre fino a £85.000 tramite il FSCS ma si applica solo ai clienti del Regno Unito dopo la Brexit. Quale offra una protezione più pertinente per te dipende da dove vivi e da con quale entità hai effettivamente aperto il conto.',
                'faq_2_q' => 'Se un broker ha entità sia FCA sia CySEC, quale mi tutela?',
                'faq_2_a' => 'Quella presso cui hai effettivamente aperto il conto, non il marchio nel suo insieme. Chiedi al broker di confermare per iscritto quale entità giuridica detiene il tuo conto, quindi verifica il numero di licenza specifico di quell\'entità nel registro del regolatore corrispondente.',
                'faq_3_q' => 'Quanti broker detengono sia una licenza FCA sia una licenza CySEC?',
                'faq_3_a' => '33 dei 139 broker del nostro campione di ricerca detengono entrambe contemporaneamente, di solito tramite entità separate nel Regno Unito e nell\'UE sotto un unico marchio: vedi la ripartizione completa sopra.',
            ),
            'pt' => array(
                'title' => 'FCA vs CySEC: Qual é a diferença para os traders de forex?',
                'meta_title' => 'FCA vs CySEC (2026): Qual dos reguladores de forex protege melhor os traders?',
                'meta_description' => 'FCA e CySEC comparados em termos de compensação de investidores, acesso ao mercado e regras de alavancagem, com base na nossa base de conhecimento sobre regulação e não em alegações comerciais. Veja qual deles importa mais na sua situação.',
                'intro' => 'A FCA e a CySEC são os dois reguladores de Nível 1 que mais aparecem entre as corretoras que analisámos: 33 das 139 corretoras do nosso conjunto de dados detêm as duas licenças em simultâneo, normalmente através de entidades separadas no Reino Unido e na UE sob a mesma marca. Costumam ser tratados como equivalentes, como uma «regulação adequada», mas diferenciam-se em aspetos importantes se estiver a escolher entre duas entidades da mesma corretora, ou a decidir se a credenciação apenas na UE ou apenas no Reino Unido lhe basta.',
                'section_1_heading' => '£85.000 vs €20.000: compensação de investidores',
                'section_1_body' => 'Os clientes de retalho elegíveis de uma empresa autorizada pela FCA estão cobertos pelo Financial Services Compensation Scheme (FSCS) até £85.000 por pessoa, caso a empresa entre em incumprimento. Os clientes de retalho elegíveis de uma empresa licenciada pela CySEC estão cobertos, em vez disso, pelo Investor Compensation Fund (ICF) até €20.000 por pessoa. Ambos são regimes legais reais com um limite máximo definido, não são linguagem publicitária, mas estão expressos em moedas diferentes, fixados em níveis muito distintos e aplicam-se apenas à entidade concreta licenciada por cada regulador, nunca a uma marca irmã sem ligação que apenas partilhe o mesmo nome.',
                'section_2_heading' => 'Acesso ao mercado: apenas Reino Unido vs passaporte em toda a UE',
                'section_2_body' => 'Desde o Brexit, uma licença da FCA abrange apenas os clientes do Reino Unido e já não confere passaporte automático na UE. Uma licença da CySEC, pelo contrário, permite que uma empresa de investimento cipriota preste os seus serviços em toda a UE/EEE ao abrigo da MiFID II com essa única licença, sem necessidade de autorização separada em cada Estado-Membro. Por isso, uma corretora que atende clientes do Reino Unido e da UE funciona normalmente com duas entidades licenciadas separadamente e com capital próprio separado, em vez de uma única licença a cobrir as duas regiões.',
                'section_3_heading' => 'Alavancagem e regras de conduta',
                'section_3_body' => 'Ambos os reguladores aplicam limites de alavancagem de retalho semelhantes aos da ESMA, normalmente 30:1 em pares de moedas principais e até 2:1 em CFD de criptomoedas, uma vez que o Reino Unido manteve estas regras após o Brexit em vez de se afastar delas. As diferenças são mais marcadas na conduta: as regras do Consumer Duty da FCA exigem que as empresas garantam ativamente bons resultados para os clientes de retalho, e não apenas que evitem prejudicá-los, ao passo que as empresas licenciadas pela CySEC seguem, em vez disso, as regras de conduta de negócio da MiFID II. Na prática, isto não se traduz numa grande diferença no nível de alavancagem oferecido a um cliente de retalho, mas constitui uma obrigação adicional distinta do lado da FCA.',
                'section_4_heading' => 'Qual deve realmente preocupá-lo?',
                'section_4_body' => 'A entidade onde a sua conta está efetivamente sediada, e não a reputação global da marca. A proteção por compensação de um residente no Reino Unido provém da entidade da FCA e do FSCS; a de um residente na UE provém da entidade da CySEC e do ICF. Se uma corretora detiver as duas licenças, confirme por escrito a entidade jurídica exata à qual a sua conta pertence e, em seguida, verifique o número de licença dessa entidade no registo do regulador correspondente, e não a alegação geral da marca de ser «regulada pela FCA e pela CySEC».',
                'section_5_heading' => 'Com que frequência existe dupla regulação FCA/CySEC?',
                'section_5_body' => 'Das 139 corretoras do nosso conjunto de análise, 95 detêm uma licença da CySEC, 53 detêm uma licença da FCA e 33 detêm ambas em simultâneo, normalmente através de entidades separadas no Reino Unido e na UE, e não de uma licença única a cobrir as duas regiões. 24 das 139 não têm nenhuma das duas como credencial principal, embora muitas delas detenham outra licença de Nível 1 ou offshore noutro ponto do nosso conjunto de dados. A detenção de ambas é comum entre corretoras maiores e mais consolidadas, mas nunca significa que um único regime de compensação cubra todos os clientes: o regime de cada entidade abrange apenas os clientes dessa mesma entidade.',
                'faq_1_q' => 'A regulação da CySEC é tão boa como a da FCA?',
                'faq_1_a' => 'Diferente, e não simplesmente melhor ou pior: a CySEC cobre os clientes elegíveis até €20.000 ao abrigo do ICF e beneficia do passaporte da MiFID II em toda a UE, ao passo que a FCA cobre até £85.000 ao abrigo do FSCS, mas aplica-se apenas aos clientes do Reino Unido após o Brexit. Qual das duas oferece uma proteção mais relevante para si depende do país onde vive e da entidade à qual está efetivamente inscrito.',
                'faq_2_q' => 'Se uma corretora tiver entidades da FCA e da CySEC, qual delas me protege?',
                'faq_2_a' => 'A entidade onde abriu efetivamente a sua conta, e não a marca no seu conjunto. Confirme por escrito junto da corretora qual a entidade jurídica que detém a sua conta e, em seguida, verifique o número de licença dessa entidade no registo do regulador correspondente.',
                'faq_3_q' => 'Quantas corretoras detêm simultaneamente uma licença da FCA e uma licença da CySEC?',
                'faq_3_a' => '33 das 139 corretoras do nosso conjunto de análise detêm ambas em simultâneo, normalmente através de entidades separadas no Reino Unido e na UE sob uma mesma marca. Consulte a análise completa acima.',
            ),
        ),
    );
}

/**
 * Null if the slug isn't a drafted comparison -- callers fall through
 * to inc/regulation.php's single-regulator lookup, then to the plain
 * /regulation/ index, exactly like globalfxhub_vs_pair()'s null-then-404
 * convention elsewhere in this theme.
 */
function globalfxhub_get_regulation_comparison( $slug ) {
    $comparisons = globalfxhub_get_regulation_comparisons();
    return $comparisons[ $slug ] ?? null;
}

function globalfxhub_get_localized_regulation_comparison( $slug ) {
    $entry = globalfxhub_get_regulation_comparison( $slug );
    if ( ! $entry ) {
        return null;
    }
    $lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
    $translations = globalfxhub_regulation_comparison_translations();
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

function globalfxhub_regulation_comparison_seo_title( $title_parts ) {
    $slug = get_query_var( 'regpage' );
    if ( $slug ) {
        $entry = globalfxhub_get_localized_regulation_comparison( $slug );
        if ( $entry ) {
            $title_parts['title'] = $entry['meta_title'];
        }
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_regulation_comparison_seo_title' );

function globalfxhub_regulation_comparison_seo_head() {
    $slug = get_query_var( 'regpage' );
    if ( ! $slug ) {
        return;
    }
    $entry = globalfxhub_get_localized_regulation_comparison( $slug );
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
add_action( 'wp_head', 'globalfxhub_regulation_comparison_seo_head', 5 );
