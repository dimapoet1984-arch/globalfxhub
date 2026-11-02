<?php
/**
 * Every recurring UI/homepage string, registered with Polylang (if active)
 * so translations entered under Languages > Translations actually get
 * served on the front end. The theme never hard-depends on Polylang: if
 * it's inactive, globalfxhub_t()/globalfxhub_te() just pass the original
 * English straight through.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_translatable_strings() {
    return array(
        'nav_reviews', 'nav_learn', 'nav_news', 'nav_compare', 'nav_best', 'nav_regulation', 'nav_countries', 'nav_finder', 'nav_cost_calculator', 'nav_regulation_checker', 'nav_changelog', 'nav_tools', 'nav_research', 'nav_cta',

        'footer_company_heading', 'footer_about', 'footer_how_we_test', 'footer_why_trust_us',
        'footer_research_heading', 'footer_all_reviews', 'footer_compare_brokers',
        'footer_legal_heading', 'footer_advertiser_disclosure', 'footer_terms', 'footer_privacy',
        'footer_contact', 'footer_tagline', 'footer_legal_line', 'footer_disclosure_full',
        'footer_fx_market_news', 'footer_broker_news',

        'hero1_eyebrow', 'hero1_h1', 'hero1_p', 'hero_search_label', 'hero_search_placeholder',
        'hero_search_button', 'hero2_eyebrow', 'hero2_h1', 'hero2_p', 'hero2_button',
        'hero3_eyebrow', 'hero3_h1', 'hero3_p', 'hero3_button',

        'trust1', 'trust2', 'trust3', 'trust4',

        'sec_overview_h2', 'sec_overview_p', 'sec_heatmap_h3', 'sec_movers_h3',
        'sec_blog_h2', 'sec_blog_p', 'sec_news_h2', 'sec_news_p',
        'sec_rankings_h2', 'sec_rankings_p', 'sec_method_h2', 'sec_method_p',
        'sec_guides_h2', 'sec_guides_p', 'sec_compare_h2', 'sec_compare_p',
        'sec_countries_h2', 'sec_countries_p',

        'method1_h3', 'method1_p', 'method2_h3', 'method2_p',
        'method3_h3', 'method3_p', 'method4_h3', 'method4_p',
        'method5_h3', 'method5_p', 'method6_h3', 'method6_p',
        'method7_h3', 'method7_p', 'method8_h3', 'method8_p',
        'method9_h3', 'method9_p',

        'link_how_we_score', 'link_all_articles', 'link_all_news', 'link_all_guides', 'link_open_compare',

        'table_broker', 'table_spread', 'table_platforms', 'table_score', 'btn_read_review',
    );
}

/**
 * The canonical English text for each key -- this is both the source
 * registered with Polylang and the fallback shown when no translation
 * exists yet (or Polylang isn't active).
 */
function globalfxhub_string_text( $key ) {
    $strings = array(
        'nav_reviews' => 'Reviews',
        'nav_learn' => 'Learn',
        'nav_news' => 'News',
        'nav_compare' => 'Compare',
        'nav_best' => 'Best Brokers',
        'nav_regulation' => 'Regulation',
        'nav_countries' => 'Countries',
        'nav_finder' => 'Broker Finder',
        'nav_cost_calculator' => 'Cost Calculator',
        'nav_regulation_checker' => 'Regulation Checker',
        'nav_changelog' => 'Change Log',
        'nav_tools' => 'Tools',
        'nav_research' => 'Research',
        'nav_cta' => 'See rankings',

        'footer_company_heading' => 'Company',
        'footer_about' => 'About',
        'footer_how_we_test' => 'How we test',
        'footer_why_trust_us' => 'Why trust us',
        'footer_research_heading' => 'Research',
        'footer_all_reviews' => 'All reviews',
        'footer_compare_brokers' => 'Compare brokers',
        'footer_legal_heading' => 'Legal',
        'footer_advertiser_disclosure' => 'Advertiser disclosure',
        'footer_terms' => 'Terms of use',
        'footer_privacy' => 'Privacy policy',
        'footer_contact' => 'Contact',
        'footer_tagline' => 'Independent forex & CFD broker research based on regulatory records, broker disclosures and comparative data.',
        'footer_legal_line' => 'Independent forex & CFD broker research',
        'footer_disclosure_full' => 'Advertiser disclosure & risk warning: Broker names, regulatory licence numbers (CySEC, the UK\'s FCA, Seychelles FSA, and others), and trading conditions referenced on this site describe real, independently operating companies. Entity and pricing figures were originally compiled March 2026; regulatory status and broker-specific disclosures were most recently reviewed October 2026, with another full review scheduled January 2027 -- see each broker\'s own review page for its specific dates -- and figures change, so verify directly with the broker and on the relevant regulator\'s register before you rely on them. Scores are calculated using a disclosed methodology, not hands-on account testing. GlobalFXHub does not currently have referral or advertising relationships with any broker listed; if that changes, this section will disclose it. CFDs are complex instruments carrying a high risk of losing money rapidly due to leverage -- most retail investor accounts lose money trading CFDs. This is not financial advice.',
        'footer_fx_market_news' => 'FX Market News',
        'footer_broker_news' => 'Broker News',

        'hero1_eyebrow' => '2026 ANNUAL RANKINGS',
        'hero1_h1' => 'Find a forex broker you can actually trust.',
        'hero1_p' => 'We score every broker across nine weighted categories -- regulation, costs, fees, platforms, execution, product range, deposits/withdrawals, transparency, and track record -- against a fixed, disclosed rubric built from regulatory records and broker disclosures, not hands-on account testing.',
        'hero_search_label' => 'QUICK BROKER SEARCH',
        'hero_search_placeholder' => 'Search a broker by name…',
        'hero_search_button' => 'Search',
        'hero2_eyebrow' => 'NEW THIS MONTH',
        'hero2_h1' => 'Track the market with our live heatmap.',
        'hero2_p' => "See which currency pairs, metals, and energy markets are moving right now, and how brokers' spreads compare across each.",
        'hero2_button' => 'View market overview',
        'hero3_eyebrow' => 'FREE EDUCATION',
        'hero3_h1' => 'New to trading? Start with the basics.',
        'hero3_p' => 'Plain-English guides on forex, commodities, oil, and gold -- no jargon, no sales pitch.',
        'hero3_button' => 'Browse the guides',

        'trust1' => 'Regulated brokers researched',
        'trust2' => 'Scoring factors, fully disclosed',
        'trust3' => 'Free educational guides & articles',
        'trust4' => 'Paid for by broker rankings',

        'sec_overview_h2' => 'Market overview',
        'sec_overview_p' => "A snapshot of today's forex, metals, and energy markets. Updated throughout the trading day.",
        'sec_heatmap_h3' => 'Market heatmap',
        'sec_movers_h3' => "Today's movers",
        'sec_blog_h2' => 'From the blog',
        'sec_blog_p' => 'Plain-English explainers on how the forex and commodities markets actually work.',
        'sec_news_h2' => 'Latest news',
        'sec_news_p' => 'Broker-specific developments, and original FX market analysis -- not a bulk reproduction of wire-service headlines.',
        'sec_rankings_h2' => 'Top 15 regulated forex brokers',
        'sec_rankings_p' => 'Real, currently CySEC-, FCA-, or Seychelles FSA-licensed brokers, scored with a disclosed methodology based on public data -- not hands-on testing. See "How we score" below.',
        'sec_method_h2' => 'How we score brokers',
        'sec_method_p' => "This is a disclosed, data-based methodology -- not a claim of hands-on account testing. Here's exactly how each score is built.",
        'sec_guides_h2' => 'Guides for your trading style',
        'sec_guides_p' => 'Start wherever you are -- new to forex, switching platforms, or optimizing for cost.',
        'sec_compare_h2' => 'Compare brokers head-to-head',
        'sec_compare_p' => 'Put two brokers side by side on cost, platforms, and regulation.',
        'sec_countries_h2' => 'Best brokers by country',
        'sec_countries_p' => 'Regulation and available brokers vary a lot by where you live -- start here.',

        'method1_h3' => 'Regulation & client protection',
        'method1_p' => 'A fixed point value for each independently-verifiable regulator held (CySEC, the FCA, ASIC, and similar Tier-1/Tier-2 bodies), against a disclosed scale -- not a comparison to other brokers. An offshore-only licence, such as the Seychelles FSA, adds nothing on its own: it reflects materially lighter oversight and no statutory investor compensation scheme, though we still disclose that licence and its number on the broker\'s review page.',
        'method2_h3' => 'Trading costs',
        'method2_p' => 'The broker\'s EUR/USD spread against fixed pip thresholds. We deliberately don\'t weight minimum deposit here -- a broker letting you start with $1,000 isn\'t charging you more to trade than one that lets you start with $10.',
        'method3_h3' => 'Non-trading fees',
        'method3_p' => 'Confirmed inactivity fees and currency-conversion charges. A broker that\'s confirmed not to charge either scores well; one with a confirmed fee scores worse; where we couldn\'t independently confirm either way, this factor sits at a neutral midpoint rather than being guessed.',
        'method4_h3' => 'Platforms & tools',
        'method4_p' => 'Number of distinct trading platforms supported (MT4, MT5, cTrader, TradingView, proprietary). This is a breadth count, not a quality judgment of any one platform -- which is also why it carries less weight than it used to.',
        'method5_h3' => 'Execution / trading conditions',
        'method5_p' => 'Whether the broker discloses a no-dealing-desk execution model (ECN/STP/DMA), discounted for any independently-reported pattern of withdrawal refusals, fund manipulation, or similar conduct red flags found in our research.',
        'method6_h3' => 'Product range',
        'method6_p' => 'How many distinct asset classes (forex, indices, shares, commodities, crypto, bonds, ETFs, and more) we could independently confirm the broker offers, against a fixed scale.',
        'method7_h3' => 'Deposits & withdrawals',
        'method7_p' => 'Confirmed deposit/withdrawal fees and processing speed, discounted for any independently-reported pattern of delayed, blocked, or disputed withdrawals.',
        'method8_h3' => 'Transparency',
        'method8_p' => 'Starts clean and is discounted only for specific, named findings from our research: a regulator warning or blacklisting, a confirmed clone/impersonation risk, confirmed review manipulation, or a defunct/wound-down status.',
        'method9_h3' => 'Track record',
        'method9_p' => 'Years in operation, against a fixed scale from founding year. Longer-operating brokers score higher.',

        'link_how_we_score' => 'How we score →',
        'link_all_articles' => 'All articles →',
        'link_all_news' => 'All news →',
        'link_all_guides' => 'All guides →',
        'link_open_compare' => 'Open the compare tool →',

        'table_broker' => 'Broker',
        'table_spread' => 'Avg. spread',
        'table_platforms' => 'Platforms',
        'table_score' => 'Score',
        'btn_read_review' => 'Read review',
    );

    return isset( $strings[ $key ] ) ? $strings[ $key ] : $key;
}

function globalfxhub_register_strings() {
    if ( ! function_exists( 'pll_register_string' ) ) {
        return;
    }
    foreach ( globalfxhub_translatable_strings() as $key ) {
        $text = globalfxhub_string_text( $key );
        pll_register_string( $key, $text, 'GlobalFXHub Theme' );
    }
}
add_action( 'init', 'globalfxhub_register_strings' );

/**
 * Hand-translated nav/footer UI strings, hardcoded per language
 * rather than relying on Polylang's own Languages > Translations
 * admin screen (confirmed live 2026-11-02: no translation had ever
 * been entered there for any of these keys, so every one of them
 * was silently falling back to English on every non-English page --
 * the exact gap globalfxhub_t() below now closes). Short, unambiguous
 * UI words -- same bar as i18n/setup_category_translations.py's
 * CATEGORY_TRANSLATIONS -- reusing the EXACT wording
 * i18n/glossary.py already established for "broker"/"regulation"/etc.
 * so site-wide terminology stays consistent with Track A's own
 * translated content.
 *
 * Deliberately excludes 'footer_disclosure_full': a financial
 * risk-warning/compliance paragraph deserves the same two-pass
 * translate+review quality bar as real page content
 * (docs/TRANSLATION_PLAYBOOK.md section 3.3), not a quick hand
 * translation -- left on its English fallback until it's run through
 * that pipeline properly.
 */
function globalfxhub_live_string_translations() {
    return array(
        'es' => array(
            'nav_reviews' => 'Reseñas', 'nav_learn' => 'Aprende', 'nav_news' => 'Noticias',
            'nav_compare' => 'Comparar', 'nav_best' => 'Mejores Brókers', 'nav_regulation' => 'Regulación',
            'nav_countries' => 'Países', 'nav_finder' => 'Buscador de Brókers',
            'nav_cost_calculator' => 'Calculadora de Costos', 'nav_regulation_checker' => 'Verificador de Regulación',
            'nav_changelog' => 'Registro de Cambios', 'nav_tools' => 'Herramientas', 'nav_research' => 'Investigación',
            'nav_cta' => 'Ver clasificación',
            'footer_company_heading' => 'Empresa', 'footer_about' => 'Acerca de', 'footer_how_we_test' => 'Cómo evaluamos',
            'footer_why_trust_us' => 'Por qué confiar en nosotros', 'footer_research_heading' => 'Investigación',
            'footer_all_reviews' => 'Todas las reseñas', 'footer_compare_brokers' => 'Comparar brókers',
            'footer_legal_heading' => 'Legal', 'footer_advertiser_disclosure' => 'Divulgación publicitaria',
            'footer_terms' => 'Términos de uso', 'footer_privacy' => 'Política de privacidad', 'footer_contact' => 'Contacto',
            'footer_tagline' => 'Investigación independiente de brókers de forex y CFD basada en registros regulatorios, divulgaciones de brókers y datos comparativos.',
            'footer_legal_line' => 'Investigación independiente de brókers de forex y CFD',
            'footer_fx_market_news' => 'Noticias del Mercado Forex', 'footer_broker_news' => 'Noticias de Brókers',
        ),
        'de' => array(
            'nav_reviews' => 'Testberichte', 'nav_learn' => 'Lernen', 'nav_news' => 'Nachrichten',
            'nav_compare' => 'Vergleichen', 'nav_best' => 'Beste Broker', 'nav_regulation' => 'Regulierung',
            'nav_countries' => 'Länder', 'nav_finder' => 'Broker-Finder',
            'nav_cost_calculator' => 'Kostenrechner', 'nav_regulation_checker' => 'Regulierungsprüfer',
            'nav_changelog' => 'Änderungsprotokoll', 'nav_tools' => 'Werkzeuge', 'nav_research' => 'Recherche',
            'nav_cta' => 'Rangliste ansehen',
            'footer_company_heading' => 'Unternehmen', 'footer_about' => 'Über uns', 'footer_how_we_test' => 'Wie wir testen',
            'footer_why_trust_us' => 'Warum uns vertrauen', 'footer_research_heading' => 'Recherche',
            'footer_all_reviews' => 'Alle Testberichte', 'footer_compare_brokers' => 'Broker vergleichen',
            'footer_legal_heading' => 'Rechtliches', 'footer_advertiser_disclosure' => 'Werbeoffenlegung',
            'footer_terms' => 'Nutzungsbedingungen', 'footer_privacy' => 'Datenschutzerklärung', 'footer_contact' => 'Kontakt',
            'footer_tagline' => 'Unabhängige Forex- und CFD-Broker-Recherche auf Basis von Regulierungsunterlagen, Broker-Offenlegungen und Vergleichsdaten.',
            'footer_legal_line' => 'Unabhängige Forex- und CFD-Broker-Recherche',
            'footer_fx_market_news' => 'FX-Marktnachrichten', 'footer_broker_news' => 'Broker-Nachrichten',
        ),
        'ar' => array(
            'nav_reviews' => 'المراجعات', 'nav_learn' => 'تعلم', 'nav_news' => 'أخبار',
            'nav_compare' => 'قارن', 'nav_best' => 'أفضل الوسطاء', 'nav_regulation' => 'التنظيم',
            'nav_countries' => 'الدول', 'nav_finder' => 'أداة البحث عن وسيط',
            'nav_cost_calculator' => 'حاسبة التكلفة', 'nav_regulation_checker' => 'فاحص التنظيم',
            'nav_changelog' => 'سجل التغييرات', 'nav_tools' => 'الأدوات', 'nav_research' => 'البحث',
            'nav_cta' => 'عرض التصنيف',
            'footer_company_heading' => 'الشركة', 'footer_about' => 'من نحن', 'footer_how_we_test' => 'كيف نختبر',
            'footer_why_trust_us' => 'لماذا تثق بنا', 'footer_research_heading' => 'البحث',
            'footer_all_reviews' => 'جميع المراجعات', 'footer_compare_brokers' => 'مقارنة الوسطاء',
            'footer_legal_heading' => 'القانونية', 'footer_advertiser_disclosure' => 'إفشاء الإعلانات',
            'footer_terms' => 'شروط الاستخدام', 'footer_privacy' => 'سياسة الخصوصية', 'footer_contact' => 'اتصل بنا',
            'footer_tagline' => 'أبحاث مستقلة حول وسطاء الفوركس والعقود مقابل الفروقات، مبنية على السجلات التنظيمية وإفشاءات الوسطاء والبيانات المقارنة.',
            'footer_legal_line' => 'أبحاث مستقلة حول وسطاء الفوركس والعقود مقابل الفروقات',
            'footer_fx_market_news' => 'أخبار سوق الفوركس', 'footer_broker_news' => 'أخبار الوسطاء',
        ),
        'fr' => array(
            'nav_reviews' => 'Avis', 'nav_learn' => 'Apprendre', 'nav_news' => 'Actualités',
            'nav_compare' => 'Comparer', 'nav_best' => 'Meilleurs Courtiers', 'nav_regulation' => 'Réglementation',
            'nav_countries' => 'Pays', 'nav_finder' => 'Recherche de Courtier',
            'nav_cost_calculator' => 'Calculateur de Coûts', 'nav_regulation_checker' => 'Vérificateur de Réglementation',
            'nav_changelog' => 'Journal des Modifications', 'nav_tools' => 'Outils', 'nav_research' => 'Recherche',
            'nav_cta' => 'Voir le classement',
            'footer_company_heading' => 'Entreprise', 'footer_about' => 'À propos', 'footer_how_we_test' => 'Comment nous testons',
            'footer_why_trust_us' => 'Pourquoi nous faire confiance', 'footer_research_heading' => 'Recherche',
            'footer_all_reviews' => 'Tous les avis', 'footer_compare_brokers' => 'Comparer les courtiers',
            'footer_legal_heading' => 'Mentions légales', 'footer_advertiser_disclosure' => 'Divulgation publicitaire',
            'footer_terms' => "Conditions d'utilisation", 'footer_privacy' => 'Politique de confidentialité', 'footer_contact' => 'Contact',
            'footer_tagline' => 'Recherche indépendante sur les courtiers forex et CFD basée sur des dossiers réglementaires, des divulgations de courtiers et des données comparatives.',
            'footer_legal_line' => 'Recherche indépendante sur les courtiers forex et CFD',
            'footer_fx_market_news' => 'Actualités du Marché Forex', 'footer_broker_news' => 'Actualités des Courtiers',
        ),
        'pl' => array(
            'nav_reviews' => 'Recenzje', 'nav_learn' => 'Nauka', 'nav_news' => 'Wiadomości',
            'nav_compare' => 'Porównaj', 'nav_best' => 'Najlepsi Brokerzy', 'nav_regulation' => 'Regulacja',
            'nav_countries' => 'Kraje', 'nav_finder' => 'Wyszukiwarka Brokerów',
            'nav_cost_calculator' => 'Kalkulator Kosztów', 'nav_regulation_checker' => 'Sprawdzanie Regulacji',
            'nav_changelog' => 'Rejestr Zmian', 'nav_tools' => 'Narzędzia', 'nav_research' => 'Analizy',
            'nav_cta' => 'Zobacz ranking',
            'footer_company_heading' => 'Firma', 'footer_about' => 'O nas', 'footer_how_we_test' => 'Jak testujemy',
            'footer_why_trust_us' => 'Dlaczego nam ufać', 'footer_research_heading' => 'Analizy',
            'footer_all_reviews' => 'Wszystkie recenzje', 'footer_compare_brokers' => 'Porównaj brokerów',
            'footer_legal_heading' => 'Informacje prawne', 'footer_advertiser_disclosure' => 'Informacja o reklamach',
            'footer_terms' => 'Warunki użytkowania', 'footer_privacy' => 'Polityka prywatności', 'footer_contact' => 'Kontakt',
            'footer_tagline' => 'Niezależne badania brokerów forex i CFD oparte na dokumentach regulacyjnych, informacjach od brokerów i danych porównawczych.',
            'footer_legal_line' => 'Niezależne badania brokerów forex i CFD',
            'footer_fx_market_news' => 'Wiadomości z Rynku Forex', 'footer_broker_news' => 'Wiadomości o Brokerach',
        ),
        'it' => array(
            'nav_reviews' => 'Recensioni', 'nav_learn' => 'Impara', 'nav_news' => 'Notizie',
            'nav_compare' => 'Confronta', 'nav_best' => 'Migliori Broker', 'nav_regulation' => 'Regolamentazione',
            'nav_countries' => 'Paesi', 'nav_finder' => 'Trova Broker',
            'nav_cost_calculator' => 'Calcolatore dei Costi', 'nav_regulation_checker' => 'Verifica Regolamentazione',
            'nav_changelog' => 'Registro Modifiche', 'nav_tools' => 'Strumenti', 'nav_research' => 'Ricerca',
            'nav_cta' => 'Vedi la classifica',
            'footer_company_heading' => 'Azienda', 'footer_about' => 'Chi siamo', 'footer_how_we_test' => 'Come testiamo',
            'footer_why_trust_us' => 'Perché fidarsi di noi', 'footer_research_heading' => 'Ricerca',
            'footer_all_reviews' => 'Tutte le recensioni', 'footer_compare_brokers' => 'Confronta broker',
            'footer_legal_heading' => 'Legale', 'footer_advertiser_disclosure' => 'Informativa pubblicitaria',
            'footer_terms' => 'Termini di utilizzo', 'footer_privacy' => 'Informativa sulla privacy', 'footer_contact' => 'Contatti',
            'footer_tagline' => 'Ricerca indipendente sui broker forex e CFD basata su documenti normativi, informative dei broker e dati comparativi.',
            'footer_legal_line' => 'Ricerca indipendente sui broker forex e CFD',
            'footer_fx_market_news' => 'Notizie del Mercato Forex', 'footer_broker_news' => 'Notizie sui Broker',
        ),
        'pt' => array(
            'nav_reviews' => 'Avaliações', 'nav_learn' => 'Aprenda', 'nav_news' => 'Notícias',
            'nav_compare' => 'Comparar', 'nav_best' => 'Melhores Corretoras', 'nav_regulation' => 'Regulação',
            'nav_countries' => 'Países', 'nav_finder' => 'Localizador de Corretoras',
            'nav_cost_calculator' => 'Calculadora de Custos', 'nav_regulation_checker' => 'Verificador de Regulação',
            'nav_changelog' => 'Registro de Alterações', 'nav_tools' => 'Ferramentas', 'nav_research' => 'Pesquisa',
            'nav_cta' => 'Ver classificação',
            'footer_company_heading' => 'Empresa', 'footer_about' => 'Sobre nós', 'footer_how_we_test' => 'Como testamos',
            'footer_why_trust_us' => 'Por que confiar em nós', 'footer_research_heading' => 'Pesquisa',
            'footer_all_reviews' => 'Todas as avaliações', 'footer_compare_brokers' => 'Comparar corretoras',
            'footer_legal_heading' => 'Jurídico', 'footer_advertiser_disclosure' => 'Divulgação publicitária',
            'footer_terms' => 'Termos de uso', 'footer_privacy' => 'Política de privacidade', 'footer_contact' => 'Contato',
            'footer_tagline' => 'Pesquisa independente sobre corretoras de forex e CFD baseada em registros regulatórios, divulgações de corretoras e dados comparativos.',
            'footer_legal_line' => 'Pesquisa independente sobre corretoras de forex e CFD',
            'footer_fx_market_news' => 'Notícias do Mercado Forex', 'footer_broker_news' => 'Notícias de Corretoras',
        ),
    );
}

/**
 * Get/echo a registered string translated into the current language.
 * Checks the hardcoded table above first, then falls back to
 * Polylang's own Languages > Translations admin entry (if any), then
 * the original English.
 */
function globalfxhub_t( $key ) {
    $text = globalfxhub_string_text( $key );

    if ( function_exists( 'pll_current_language' ) ) {
        $lang = pll_current_language();
        $live = globalfxhub_live_string_translations();
        if ( $lang && isset( $live[ $lang ][ $key ] ) ) {
            return $live[ $lang ][ $key ];
        }
    }

    return function_exists( 'pll__' ) ? pll__( $text ) : $text;
}
function globalfxhub_te( $key ) {
    echo esc_html( globalfxhub_t( $key ) );
}
