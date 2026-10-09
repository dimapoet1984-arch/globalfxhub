<?php
/**
 * "Best broker in [country]" pages for all 27 EU member states.
 *
 * globalfxhub_get_brokers() now also includes brokers whose only licence
 * is offshore (Seychelles FSA, cysec === null) -- this file's ranking
 * excludes those (see globalfxhub_country_broker_ranking() below), since
 * a CySEC licence is the precondition for MiFID passporting into any
 * EU/EEA state at all. That's a necessary condition, not a sufficient
 * one: passporting itself works via a notification the home regulator
 * files per host country, so it isn't automatic or uniform across every
 * broker and every member state, and specific products/account types can
 * still vary by entity and country even where notification is in place
 * -- see each country page's own nuance on this rather than treating
 * "CySEC-licensed" and "actively available here" as the same claim. No
 * live traffic/market-share dataset exists anywhere in this codebase or
 * environment to measure country-level popularity with.
 *
 * Rather than invent country-specific rankings we can't back up, the
 * ranking here uses one real, already-present signal -- a broker's own
 * disclosed headquarters (the 'hq' field already on every broker record)
 * -- to surface brokers actually based in that country ahead of the rest,
 * then fills remaining slots with the same disclosed overall-score
 * ranking used everywhere else on the site. Every country page says so
 * plainly. This intentionally means most countries (the ones with no
 * broker headquartered there) show the same top-10 list; that's the
 * honest result of the data actually available, not a bug.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * All 27 EU member states. 'iso' is the ISO 3166-1 alpha-2 code, used to
 * render a flag emoji (see globalfxhub_country_flag_emoji() below).
 * 'hq_match' is the plain-English country name as it actually appears in
 * broker 'hq' strings (see globalfxhub_get_brokers()), used to detect a
 * local headquarters -- confirmed present in the data for Cyprus, Poland,
 * Ireland, Germany, and Estonia; every other country currently has no HQ
 * match and falls back to the global ranking.
 *
 * 'paragraphs' are hand-written, fact-checked content: currency,
 * regulator, EU/eurozone status, and -- where a genuinely verifiable,
 * non-fabricated detail exists (a broker's real HQ, a documented
 * regulatory precedent) -- that detail. Nothing here is invented traffic
 * or market-share data.
 */
function globalfxhub_get_countries() {
    return array(
        'austria' => array(
            'name' => 'Austria', 'iso' => 'AT', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1995,
            'regulator' => 'FMA (Finanzmarktaufsicht)', 'hq_match' => 'Austria',
            'taxation_overview' => 'Non-securitised derivatives (the category forex/CFDs are generally understood to fall into) are, since Austria\'s 2022 eco-social tax reform, eligible for the flat 27.5% special capital-gains rate if the broker voluntarily withholds Austrian KESt (tax at source); otherwise the gain must be self-declared, though the special rate can still often be applied on request. Losses on non-securitised derivatives can only be offset against same-category private capital income in the same year -- no carryforward. No primary Austrian tax-law source was found explicitly naming spot forex/CFDs as "non-securitised derivatives" -- this is the standard interpretation in secondary/tax-advisory sources, not confirmed verbatim against the EStG text. Consult an Austrian Steuerberater for your specific position.',
            'local_payment_methods' => 'EPS (eps-Ueberweisung) is Austria\'s dominant domestic bank-transfer rail generally (an estimated 15-20% of Austrian e-commerce volume), but no evidence was found that forex/CFD brokers commonly offer it as a deposit method -- broker comparison sites for Austria list cards, SEPA wire, and e-wallets instead.',
            'country_specific_restrictions' => 'The FMA\'s Product Intervention Measures regulation (in force since 15 May 2019) made ESMA\'s then-temporary CFD/binary-options measures permanent under Austrian law, with minor Austria-specific tweaks to risk-warning wording -- in substance the EU baseline made permanent nationally, not a materially different extra layer.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in Austria?', 'a' => 'Yes, if offered by an FMA-authorized firm or an EU firm passporting in; binary options are banned for retail clients under a permanent FMA rule since 2019.' ), array( 'q' => 'Do I have to declare my CFD/forex profits to the Finanzamt?', 'a' => 'Generally yes, unless your broker voluntarily withholds Austrian KESt on your behalf. This is a general overview only -- confirm your specific situation with a Steuerberater.' ) ),
            'regulatory_source_url' => 'https://www.fma.gv.at/en/',
            'paragraphs' => array(
                'Austria joined the EU in 1995 and has used the euro since the currency\'s launch. Retail CFD and forex trading is supervised domestically by the FMA (Finanzmarktaufsicht), which enforces the same EU-wide leverage caps and risk-warning rules that apply to every CySEC-licensed broker passporting into the country.',
                'Austrian retail investors are historically more exposed to traditional bank-distributed savings and investment products than to leveraged CFD trading, so the market is smaller and more niche than in several larger EU economies -- worth knowing before comparing marketing claims from any provider.',
            ),
        ),
        'belgium' => array(
            'name' => 'Belgium', 'iso' => 'BE', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'FSMA (Financial Services and Markets Authority)', 'hq_match' => 'Belgium',
            'additional_eu_note' => 'Belgium has its own, earlier restriction beyond the EU baseline: the FSMA banned the distribution of binary options and certain highly leveraged OTC derivatives to retail clients in 2016, two years ahead of ESMA\'s EU-wide leverage caps and marketing rules in 2018. Any broker still marketing CFDs to Belgian retail clients must satisfy both the domestic FSMA rules and the bloc-wide ESMA framework.',
            'taxation_overview' => 'Gains from forex/CFD trading, where classified as "speculation" or abnormal management of private assets (the standard classification used for leveraged, short-term trading), are taxed as miscellaneous income at a flat 33%. A separate new general 10% capital gains tax on financial assets (with a EUR10,000 annual exemption), approved in April 2026 retroactive to 1 January 2026, explicitly excludes speculative transactions -- so it likely doesn\'t apply to active forex/CFD trading, though classification is fact-specific. Mandatory broker withholding is reported to start 1 June 2026 (opt-out available); gains via a foreign broker must be self-reported. Consult a Belgian tax adviser for your specific classification.',
            'local_payment_methods' => 'Bancontact, Belgium\'s dominant card/app payment rail (tied to a Belgian bank account), is confirmed as a deposit method at least at one online trading platform, though broader broker-level adoption specifically for forex/CFD deposits wasn\'t confirmed.',
            'country_specific_restrictions' => 'Belgium\'s FSMA, by Royal Decree of 21 July 2016, imposed what multiple sources describe as a full ban -- not just a marketing restriction -- on offering or distributing OTC binary options, certain leveraged CFDs, and rolling spot forex to retail consumers, including via electronic platforms, regardless of whether the provider is EU-licensed. It also banned referral/incentive schemes and credit-card payment for these products -- a genuinely stricter, earlier measure than the later EU-wide ESMA rules. Whether it remains unchanged today wasn\'t independently confirmed from a 2026 source.',
            'faqs' => array( array( 'q' => 'Can I legally trade CFDs/forex through a broker as a Belgian retail resident?', 'a' => 'Belgium has had, since 2016, one of the EU\'s strictest national regimes: a Royal Decree banned distribution and marketing of OTC binary options, leveraged CFDs, and rolling spot forex to Belgian retail consumers -- going beyond the later EU-wide ESMA rules. Verify current status directly with the FSMA before opening an account.' ), array( 'q' => 'Do I need to declare CFD/forex gains to the Belgian tax authorities?', 'a' => 'Generally yes; such gains are typically treated as "speculative" miscellaneous income taxed at 33%, separate from the new 2026 general capital gains tax, which appears to exclude speculative trading. Consult a Belgian tax adviser for your specific case.' ) ),
            'regulatory_source_url' => 'https://www.fsma.be/en',
            'paragraphs' => array(
                'A founding EU and eurozone member, Belgium is supervised domestically by the FSMA. Belgium was notably ahead of the curve on retail derivatives protection: the FSMA restricted the distribution of binary options and certain highly leveraged OTC derivatives to retail clients back in 2016, two years before ESMA\'s EU-wide leverage caps and marketing rules took effect in 2018.',
                'That early, stricter stance is part of why Belgian regulators are generally viewed as conservative on retail leveraged trading -- any broker marketing into Belgium is bound by both the domestic FSMA rules and the EU-wide ESMA framework.',
            ),
        ),
        'bulgaria' => array(
            'name' => 'Bulgaria', 'iso' => 'BG', 'currency' => 'BGN', 'eurozone' => false, 'eu_since' => 2007,
            'regulator' => 'FSC (Financial Supervision Commission)', 'hq_match' => 'Bulgaria',
            'taxation_overview' => 'Gains from spot forex, CFDs, and OTC derivatives are generally described as taxed under Bulgaria\'s flat 10% personal income tax rate (net gain = sale price minus purchase price). Some sources claim a standard expense deduction effectively lowers this to 9%, but this is disputed between sources. The EU/EEA-regulated-market capital gains exemption that applies to shares/ETFs does not extend to CFDs or forex. Very active, frequent trading risks reclassification by the tax authority (NRA) as a business activity, changing the treatment, though the exact threshold wasn\'t found. Consult a Bulgarian tax adviser and the NRA directly.',
            'local_payment_methods' => 'ePay.bg, Bulgaria\'s leading domestic online payment system, is explicitly listed as a supported deposit option on at least one major e-wallet used by Bulgarian traders, though no forex/CFD broker was confirmed to support it directly.',
            'country_specific_restrictions' => 'The FSC maintains a publicly updated blacklist of unauthorized investment/forex/CFD platforms, and Bulgarian courts have ordered ISPs to block access to unlicensed brokers (40 sites blocked via a 2018 Sofia Regional Court order) -- a genuine additional consumer-protection layer. On product rules specifically, the FSC\'s 2019 CFD measures largely mirror the EU/ESMA framework rather than add materially beyond it.',
            'faqs' => array( array( 'q' => 'Is a forex/CFD broker regulated if it says it\'s "FSC-licensed"?', 'a' => 'Only if it appears on the FSC\'s own register of licensed investment intermediaries or the EEA-passporting notification list. The FSC publishes and regularly updates a blacklist of unauthorized platforms, and Bulgarian courts have ordered ISP blocks on unlicensed sites -- verify independently on fsc.bg rather than relying on a broker\'s own claim.' ), array( 'q' => 'How are my forex/CFD trading gains taxed in Bulgaria?', 'a' => 'Commonly described as taxed at a flat 10% (some sources say an effective 9% after a standard deduction, though this is disputed) under the Personal Income Tax Act. Confirm with the NRA or a Bulgarian tax adviser, especially regarding loss offsets and possible reclassification as business income for frequent traders.' ) ),
            'regulatory_source_url' => 'https://www.fsc.bg/en/',
            'paragraphs' => array(
                'Bulgaria joined the EU in 2007 and still uses its own currency, the lev (BGN), which is pegged to the euro; the country has targeted future eurozone entry. Domestic oversight of financial markets sits with the FSC, though in practice most retail forex and CFD trading in Bulgaria happens through brokers passporting in from elsewhere in the EU, CySEC-licensed firms among the most common.',
                'No broker in our current CySEC-licensed dataset is headquartered in Bulgaria, so the ranking below reflects our overall EU-wide scoring rather than any Bulgaria-specific presence.',
            ),
        ),
        'croatia' => array(
            'name' => 'Croatia', 'iso' => 'HR', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2013,
            'regulator' => 'HANFA (Croatian Financial Services Supervisory Agency)', 'hq_match' => 'Croatia',
            'taxation_overview' => 'Capital gains from financial assets, a category that includes derivatives, are taxed at a flat 12% for individuals (raised from 10% plus surtax, effective 1 January 2024). A two-year holding-period exemption exists for qualifying financial assets, but no source confirmed whether short-term leveraged CFD/forex positions would ever qualify in practice. No Croatian source was found addressing forex or CFDs by name specifically -- this overview is extrapolated from general financial-asset capital gains rules and should be treated as unconfirmed for forex/CFDs specifically until checked with Porezna uprava (the Croatian tax authority) or a local adviser.',
            'local_payment_methods' => 'Not found as broker-specific. Croatia has notable domestic payment apps (Keks Pay, Aircash), but no source connects any of these to forex/CFD broker deposits.',
            'country_specific_restrictions' => 'HANFA\'s 2019 decision made CFD retail restrictions permanent under Croatian law and additionally imposed a full, permanent ban on binary options for retail investors -- including an explicit prohibition on offering binary options from Croatian territory to investors outside Croatia, a notable extra-territorial element not typical of the EU baseline. Note that spot currency exchange itself falls outside HANFA\'s jurisdiction -- only forex CFDs are covered.',
            'faqs' => array( array( 'q' => 'Is forex trading legal in Croatia?', 'a' => 'Spot currency exchange isn\'t something HANFA regulates; CFDs on currency pairs (and other CFDs) are regulated, and Croatia has had a permanent national ban on binary options for retail investors since 2019, alongside leverage and negative-balance-protection rules on CFDs similar to the EU-wide ESMA framework.' ), array( 'q' => 'Do I need to declare CFD trading gains to the Croatian tax authorities?', 'a' => 'General financial-asset capital gains are taxed at a flat 12% for individuals, but no source specifically addressed forex/CFD gains -- this is a general overview only; confirm directly with Porezna uprava or a Croatian tax adviser.' ) ),
            'regulatory_source_url' => 'https://www.hanfa.hr/',
            'paragraphs' => array(
                'Croatia is the EU\'s newest member state, joining in 2013, and the most recent to adopt the euro, switching from the kuna in January 2023. HANFA supervises domestic financial markets, working alongside the EU-wide ESMA framework that governs every CySEC-passported broker operating there.',
                'As with several smaller EU markets, no broker in our dataset is headquartered in Croatia, so rankings below reflect overall broker quality rather than a confirmed local market footprint.',
            ),
        ),
        'cyprus' => array(
            'name' => 'Cyprus', 'iso' => 'CY', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'CySEC (Cyprus Securities and Exchange Commission)', 'hq_match' => 'Cyprus',
            'local_entity_note' => 'Cyprus is the one country on this list where "home regulator" and "local regulator" are the same thing: the large majority of brokers reviewed here are directly licensed and headquartered in Cyprus (Limassol or Nicosia specifically), so for Cypriot residents, CySEC\'s Investor Compensation Fund (up to &euro;20,000) isn\'t a cross-border passporting mechanic -- it\'s the direct, domestic regulatory relationship.',
            'taxation_overview' => 'Cyprus has no general capital gains tax applicable to forex/CFDs (the main CGT exception, at 20%, applies only to Cyprus real estate or shares in companies holding it). Occasional, investor-type forex/CFD gains for individuals are generally not taxed as capital gains. However, if trading is conducted regularly and in a business-like manner, profits may instead be taxed as ordinary income under Cyprus\'s progressive personal income tax schedule. The frequent-vs-occasional-trading line is fact-specific (frequency, holding periods, intent) with no clear bright-line rule found -- a Cyprus tax adviser\'s input matters more than usual here.',
            'local_payment_methods' => 'Not found beyond the generic set. Cyprus payment infrastructure is dominated by SEPA bank transfers and cards processed via JCC Payment Systems, the main domestic acquirer -- no Cyprus-specific consumer payment method comparable to BLIK or iDEAL was found used for broker deposits.',
            'country_specific_restrictions' => 'CySEC\'s 2019 national CFD/binary-options measures made the ESMA-style framework permanent, with a notable extra layer: a later CySEC amending directive introduced a 10% notional-value cap specifically for CFDs on certain previously-unlisted commodities and stock indices -- a genuine restriction beyond the EU baseline. CySEC also directs its licensed firms to comply with other member states\' stricter national rules when marketing cross-border (e.g. Spain\'s enhanced risk-acknowledgment requirements, Germany\'s negative-balance-protection rules).',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading taxed in Cyprus?', 'a' => 'Generally not taxed as a capital gain for an occasional retail individual trader (Cyprus has no general CGT applicable to CFDs/forex), but frequent, business-like trading may instead be taxed as progressive personal income -- this classification is genuinely fact-specific. This is a general overview only, not advice.' ), array( 'q' => 'Beyond the EU-wide leverage limits, does CySEC impose any extra CFD rules?', 'a' => 'Yes: CySEC applies a 10% notional-value cap on CFDs tied to certain previously-unlisted commodities/indices, on top of the standard ESMA-style framework it made permanent in 2019.' ) ),
            'regulatory_source_url' => 'https://www.cysec.gov.cy/en-GB/home/',
            'paragraphs' => array(
                'Cyprus is the regulatory home base for this entire site: CySEC is the licensing authority behind every broker in our rankings, and the large majority of them run their EU-regulated entity directly out of Limassol or Nicosia. For Cypriot residents, that means many of the brokers reviewed here aren\'t just passporting in from elsewhere -- they\'re headquartered locally.',
                'Cyprus joined the EU in 2004 and the eurozone in 2008. Because so much of the CFD/forex brokerage industry is physically based there, CySEC has built specific supervisory expertise in this sector that most national regulators, overseeing far fewer such firms, haven\'t needed to develop to the same degree.',
            ),
        ),
        'czechia' => array(
            'name' => 'Czechia', 'iso' => 'CZ', 'currency' => 'CZK', 'eurozone' => false, 'eu_since' => 2004,
            'regulator' => 'ČNB (Czech National Bank)', 'hq_match' => 'Czech',
            'taxation_overview' => 'CFD and forex gains are generally described (in secondary, broker/education sources rather than the Income Tax Act itself) as taxed as personal income under Czechia\'s progressive schedule: 15% up to a threshold, 23% above it. No source explicitly cites the specific statutory provision covering forex/CFDs by name -- whether gains count as capital income, other income, or business income can change the result and isn\'t settled by sources found. Consult the Financni sprava (Czech tax authority) or a local tax adviser.',
            'local_payment_methods' => 'Not found as a single standout rail. Cards are the leading Czech online payment method generally, with instant bank-transfer gateways (GoPay, Comgate, PayU) also widely used by merchants -- but no confirmation that forex/CFD brokers specifically offer these for deposits.',
            'country_specific_restrictions' => 'The CNB issued a national CFD retail-trading restriction effective 9 August 2019, following on from ESMA\'s 2018 EU-wide temporary measure -- described in trade-press coverage as substantively the same as the ESMA rules but implemented permanently at national level, rather than adding a materially different extra layer. The CNB separately publishes ongoing public warnings naming specific unauthorized forex/CFD-style firms operating in Czechia.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in Czechia?', 'a' => 'Yes, through a CNB-authorized or EU-passported firm; the CNB has restricted retail CFD trading under a permanent national measure since August 2019, and separately issues public warnings naming specific unauthorized providers -- always check a broker against the CNB\'s own register before trading.' ), array( 'q' => 'Do I need to declare my forex/CFD profits to the Financni sprava?', 'a' => 'Likely yes, generally taxed as personal income at 15% (23% above a higher threshold), but the precise statutory classification for forex/CFDs specifically wasn\'t confirmed -- this is a general overview only; consult a Czech tax adviser.' ) ),
            'regulatory_source_url' => 'https://www.cnb.cz/en/',
            'paragraphs' => array(
                'Czechia joined the EU in 2004 and has not adopted the euro, retaining the koruna (CZK). The Czech National Bank (ČNB) acts as both central bank and financial supervisor, and enforces the same EU-wide leverage limits and risk disclosures on any broker serving Czech retail clients.',
                'No broker in our current dataset is headquartered in Czechia, so the ranking below uses our overall EU-wide scoring.',
            ),
        ),
        'denmark' => array(
            'name' => 'Denmark', 'iso' => 'DK', 'currency' => 'DKK', 'eurozone' => false, 'eu_since' => 1973,
            'regulator' => 'Finanstilsynet (Danish FSA)', 'hq_match' => 'Denmark',
            'taxation_overview' => 'Danish guidance (secondary sources) treats CFD and forex-contract gains as capital income ("kapitalindkomst") under a mark-to-market principle (lagerprincip) -- both realized and unrealized gains/losses are taxed annually, not just on closing a position, under the Kursgevinstloven. Sources disagree on the exact resulting rate: one widely-cited figure (27%/42%, with a ~DKK 61,000 threshold) actually describes a different regime (share income, "aktieindkomst"), not capital income, which instead folds into progressive personal income tax (reaching the low-to-mid 40s% at the margin). This is a genuine, unresolved area in the sources found; a professional/business trading classification changes the treatment again.',
            'local_payment_methods' => 'No forex/CFD broker funding page reviewed lists MobilePay, Denmark\'s dominant domestic mobile wallet, as a deposit option -- broker deposit methods for Danish clients appear to be the generic set (cards, bank transfer, PayPal, Skrill/Neteller, crypto).',
            'country_specific_restrictions' => 'None found beyond the EU baseline: Finanstilsynet made the 2018 ESMA temporary CFD/binary-options restrictions permanent for Denmark from 1 August 2019 (leverage caps, 50% margin close-out, negative balance protection, incentive ban, standardized risk warnings, binary options banned outright) -- in substance the EU-wide measures made permanent nationally, not an additional Danish-specific layer.',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Denmark?', 'a' => 'Yes, through a firm authorized under MiFID II (either Finanstilsynet-licensed or EU-passported). Finanstilsynet enforces the ESMA-aligned leverage caps, margin close-out, and negative-balance-protection rules as permanent national rules since August 2019.' ), array( 'q' => 'Do I have to declare CFD/forex gains to SKAT even if I haven\'t closed the position?', 'a' => 'Danish guidance suggests CFDs are taxed under a mark-to-market principle, meaning gains/losses may be assessed annually regardless of whether a position is closed -- but how this interacts with the capital-income vs. share-income classification isn\'t fully settled in available sources. Consult SKAT or a Danish tax adviser for your specific situation.' ) ),
            'regulatory_source_url' => 'https://www.finanstilsynet.dk',
            'paragraphs' => array(
                'Denmark joined the EU in 1973 and negotiated a formal opt-out from the euro, keeping the krone (DKK), which is tightly pegged to the euro via the ERM II exchange rate mechanism. Finanstilsynet is the domestic financial supervisor, working within the same EU-wide ESMA rules on leverage and marketing that apply across the bloc.',
                'No broker in our current dataset is headquartered in Denmark, so the ranking below reflects our overall EU-wide scoring rather than a confirmed local presence.',
            ),
        ),
        'estonia' => array(
            'name' => 'Estonia', 'iso' => 'EE', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'Estonian Financial Supervision Authority (Finantsinspektsioon)', 'hq_match' => 'Estonia',
            'local_entity_note' => 'Admirals (formerly Admiral Markets) is genuinely headquartered in Tallinn, but its EU retail clients -- including Estonian residents -- are served through its CySEC-regulated Cyprus subsidiary, not a locally-licensed Estonian entity. That means the Cyprus ICF (&euro;20,000 cap), not a separate Estonian scheme, is what actually applies to Estonian clients of that broker.',
            'taxation_overview' => 'Estonia taxes resident individuals\' worldwide income; the personal income tax rate rose from 20% to 22% from 1 January 2025. EMTA (the Estonian Tax and Customs Board) publishes guidance for securities gains (sale price minus acquisition cost, FIFO or weighted-average, losses offsettable against other securities gains if declared) -- but whether forex/CFD trading specifically falls under this securities regime or a different category could not be confirmed. Treat as genuinely unresolved and consult EMTA or a local tax adviser directly.',
            'local_payment_methods' => 'Not found -- no Estonia-specific payment rail was found evidenced as broker-supported; generic cards and bank transfer appear to be the norm.',
            'country_specific_restrictions' => 'None found beyond the EU baseline. Finantsinspektsioon (FI) is active in publishing public warnings about unauthorized providers (863 alerts in 2024 alone, per FI\'s own reporting), but this research found no FI-specific leverage or marketing restriction beyond the EU-wide ESMA measures.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in Estonia?', 'a' => 'Yes, via firms authorized by Finantsinspektsioon or EU-passported under MiFID II; the ESMA leverage caps and marketing rules apply. FI maintains a public alerts list for unauthorized providers (fi.ee/en/alerts) worth checking before depositing with an unfamiliar broker.' ), array( 'q' => 'Do I need to declare CFD/forex trading gains to EMTA?', 'a' => 'Not independently confirmed how CFDs specifically are classified for tax purposes in Estonia (securities vs. another category). EMTA requires declaration of securities gains using a FIFO or weighted-average cost method -- consult EMTA directly or a local tax adviser for your specific instrument.' ) ),
            'regulatory_source_url' => 'https://www.fi.ee',
            'paragraphs' => array(
                'Estonia joined the EU in 2004 and adopted the euro in 2011. It\'s also one of the few countries on this list that\'s genuinely home to one of our reviewed brokers: Admirals (formerly Admiral Markets) was founded in Tallinn and still lists Estonia as its group headquarters, even though its EU retail clients are served through its CySEC-regulated Cyprus subsidiary.',
                'Estonia\'s broader reputation for e-government and digital-first business services (e-Residency, fully digital company formation) is part of why a number of fintech and brokerage firms have chosen to establish a presence there, even when their regulated EU retail entity sits elsewhere.',
            ),
        ),
        'finland' => array(
            'name' => 'Finland', 'iso' => 'FI', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1995,
            'regulator' => 'FIN-FSA (Finanssivalvonta)', 'hq_match' => 'Finland',
            'taxation_overview' => 'Finland has the best-sourced tax treatment found in this research, direct from Vero (the Finnish Tax Administration)\'s own published guidance: CFD (including forex CFD) profits are taxed as capital income at 30% on the first EUR30,000 and 34% above that. Unusually, Vero\'s own FAQ states CFD losses cannot be deducted at all -- not against CFD profits, not as a capital loss -- so a trader who is net lossmaking overall can still owe tax on individually profitable trades. Still a general overview only; confirm application to your specific broker/instrument with a Finnish tax adviser.',
            'local_payment_methods' => 'MobilePay and Siirto are genuinely popular Finnish payment rails generally, but no forex/CFD broker funding page reviewed actually listed either as a supported deposit method -- broker deposit options for Finland appear to be the generic set (cards, bank transfer, e-wallets).',
            'country_specific_restrictions' => 'FIN-FSA issued its own 2019 decision (Public Notice 3/2019) restricting CFD marketing/distribution/sale to retail clients, plus a separate notice banning binary options outright -- both modeled on and continuing the EU-wide ESMA framework rather than clearly going beyond it. The retrieved text didn\'t confirm whether Finland\'s standing leverage caps differ from the ESMA tiers, so that specific point needs verification against FIN-FSA\'s primary text.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in Finland?', 'a' => 'Yes, through firms licensed by FIN-FSA or passported under MiFID II. Binary options are banned to retail clients, and FIN-FSA has its own 2019 decision restricting CFD marketing alongside the EU-wide ESMA framework.' ), array( 'q' => 'Can I deduct my CFD trading losses on my Finnish tax return?', 'a' => 'According to Vero\'s own FAQ, no -- CFD losses cannot be deducted in any way under current guidance, even against CFD profits. This is unusually strict and worth confirming with Vero or a Finnish tax adviser for your specific situation.' ) ),
            'regulatory_source_url' => 'https://www.finanssivalvonta.fi',
            'paragraphs' => array(
                'Finland joined the EU in 1995 and was one of the original eurozone members. FIN-FSA supervises domestic financial markets and enforces the same EU-wide ESMA leverage caps and marketing restrictions that apply to every broker passporting into the country.',
                'No broker in our current dataset is headquartered in Finland, so the ranking below reflects our overall EU-wide scoring rather than a confirmed local footprint.',
            ),
        ),
        'france' => array(
            'name' => 'France', 'iso' => 'FR', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'AMF (Autorité des marchés financiers)', 'hq_match' => 'France',
            'additional_eu_note' => 'France has its own restriction beyond the EU baseline: the AMF banned electronic advertising (online ads, email, etc.) for high-risk CFDs and binary options to retail investors in 2016-2017, ahead of the EU-wide ESMA restrictions that followed in 2018. That precedent is part of why French retail investors have long seen prominent risk warnings on CFD marketing specifically.',
            'taxation_overview' => 'Gains are generally taxed under France\'s flat tax (Prelevement Forfaitaire Unique, PFU) -- CFDs and forex get no distinct regime, they\'re treated as ordinary securities capital gains. The PFU rate rose from 30% to 31.4% from 1 January 2026 (12.8% income tax plus rising social charges), though sources disagree on exactly which tax year\'s gains the new rate first applies to. Losses can generally only offset same-type gains within the same year, with no carry-forward. Taxpayers can elect into the progressive income-tax scale instead (an irrevocable annual choice), and frequent/large-scale trading risks reclassification as business income taxed at progressive rates up to 45%. Foreign-broker accounts must be declared via form 3916-bis regardless of profit or loss. Given the 2026 rate change, confirm your specific filing with a French tax adviser.',
            'local_payment_methods' => 'Not found -- no France-specific payment rail beyond generic cards (Carte Bancaire network) and SEPA bank transfer was confirmed as broker-supported.',
            'faqs' => array( array( 'q' => 'Is forex/CFD advertising to individuals legal in France?', 'a' => 'No -- since the 2016 "Loi Sapin II," the AMF bans electronic advertising of binary options, CFDs, and forex contracts targeted at retail individuals, on top of the EU-wide ESMA marketing rules. Advertising aimed only at professional investors isn\'t covered by this specific ban.' ), array( 'q' => 'How do I check if a forex broker is authorized in France?', 'a' => 'The AMF and ACPR jointly publish and regularly update a public blacklist of unauthorized forex and crypto-derivative websites -- check it, and verify the firm\'s licence/passporting status on the AMF\'s own register, before depositing funds.' ) ),
            'regulatory_source_url' => 'https://www.amf-france.org',
            'paragraphs' => array(
                'A founding EU and eurozone member, France is supervised by the AMF, one of the more assertive regulators in Europe on retail derivatives. France restricted the advertising of high-risk CFDs and binary options to retail investors via electronic communications back in 2016-2017, ahead of the EU-wide ESMA restrictions that followed in 2018.',
                'That precedent is part of why French retail investors have long been shown prominent risk warnings on CFD marketing -- a pattern the rest of the EU later adopted. Any broker in our rankings serving French clients must still comply with both AMF rules and the bloc-wide ESMA framework.',
            ),
        ),
        'germany' => array(
            'name' => 'Germany', 'iso' => 'DE', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'BaFin (Bundesanstalt für Finanzdienstleistungsaufsicht)', 'hq_match' => 'Germany',
            'local_entity_note' => 'NAGA is genuinely headquartered in Hamburg and its parent, The NAGA Group AG, is listed on the Frankfurt Stock Exchange -- but its EU retail CFD business runs through a CySEC-regulated Cyprus entity, not a BaFin-licensed German one. German clients of NAGA are therefore covered by Cyprus\'s ICF (&euro;20,000 cap), not a German compensation scheme, despite the company\'s genuine German roots.',
            'taxation_overview' => 'CFD/forex gains for German private investors generally fall under the flat withholding tax (Abgeltungsteuer), commonly cited together with the solidarity surcharge at roughly 26.375% (church tax, where applicable, adds more). A notable 2021 rule capped loss offsetting from derivatives (including CFDs) at EUR20,000/year, widely reported as extremely punitive for active traders; Germany\'s Federal Fiscal Court found this cap problematic around mid-2022 and it was reportedly repealed retroactive to 2020 -- but the exact current legal status could not be fully confirmed against primary tax-authority guidance, so verify directly with a Steuerberater (German tax adviser).',
            'local_payment_methods' => 'Giropay and Sofort are genuine German-rooted bank-transfer rails that some brokers do list on their funding pages -- real local relevance, unlike most other EU countries researched. Note that Sofort was discontinued as a standalone service in March 2025 and folded into Klarna, so a broker page still showing "Sofort" branding may now route through Klarna; and not all brokers offer these rails (several favor cards/PayPal/e-wallets instead).',
            'country_specific_restrictions' => 'Germany has the clearest documented restriction found in this research, and it actually preceded EU-wide rules: BaFin\'s May 2017 administrative order banned marketing CFDs with an additional payment obligation (i.e. without negative balance protection) to retail clients -- Germany\'s first use of national product-intervention powers, over a year ahead of the EU-wide ESMA measure. Germany\'s rule was later made permanent in 2019, converging with the ESMA standard; a possible 2022 extension to futures contracts was proposed but its final status wasn\'t confirmed in this research.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in Germany, and are there extra German rules beyond the EU baseline?', 'a' => 'Yes, via BaFin-licensed or EU-passported firms. Germany was the first EU country to restrict CFDs nationally (a 2017 ban on CFDs without negative balance protection), predating the EU-wide ESMA measures; the current rule has since converged with the ESMA standard.' ), array( 'q' => 'Can I use Giropay or Sofort to fund a German broker account?', 'a' => 'Some brokers list these German bank-transfer rails, though not all do -- and Sofort was discontinued as a standalone service in 2025, folded into Klarna, so a broker page still showing "Sofort" may route through Klarna now. Check the specific broker\'s current funding page.' ) ),
            'regulatory_source_url' => 'https://www.bafin.de',
            'paragraphs' => array(
                'Germany, a founding EU and eurozone member, has one of the largest retail trading populations in the EU and is supervised by BaFin. It\'s also genuinely home to one of our reviewed brokers: NAGA is headquartered in Hamburg, and its parent, The NAGA Group AG, is listed on the Frankfurt Stock Exchange, even though its EU retail CFD business runs through a CySEC-regulated Cyprus entity.',
                'Germany\'s size and its well-developed retail brokerage culture mean most large CySEC-licensed brokers maintain German-language sites and support specifically for this market, on top of the EU-wide leverage and marketing rules that apply everywhere.',
            ),
        ),
        'greece' => array(
            'name' => 'Greece', 'iso' => 'GR', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1981,
            'regulator' => 'HCMC (Hellenic Capital Market Commission)', 'hq_match' => 'Greece',
            'taxation_overview' => 'Greek sources describe a flat 15% capital gains tax on derivative gains for resident individuals, with CFDs commonly treated as derivatives for this purpose (residency test: present over 183 days/year, or "center of vital interests" in Greece). A genuine area of uncertainty: a 2014-era rule suggests frequent, pattern-like trading could instead be deemed a business activity and taxed at ordinary income rates (reportedly up to ~33%) rather than the flat 15% -- the current precise test for this reclassification wasn\'t confirmed. Consult a Greek tax adviser for your specific position.',
            'local_payment_methods' => 'Not found -- no Greece-specific payment rail was found; deposit methods cited are the generic set (SEPA/local bank transfer, cards, Skrill, increasingly crypto).',
            'country_specific_restrictions' => 'None found beyond the EU baseline. The HCMC\'s additional activity in this space consists of individual public warnings naming specific unauthorized platforms, rather than a published blacklist or an extra regulatory regime comparable to some other EU states.',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Greece?', 'a' => 'Yes. Retail forex/CFD trading is legal through a firm authorized by the HCMC or passported from another EU/EEA regulator. The HCMC publishes warnings against specific unauthorized platforms -- verify any broker\'s licence on the HCMC\'s register before depositing.' ), array( 'q' => 'Do I need to declare CFD/forex trading gains to the Greek tax authority?', 'a' => 'General sources point to a flat 15% capital gains tax on derivative gains for residents, but whether frequent trading could instead be classed as a taxable business activity (at a higher rate) isn\'t clearly settled in available sources. This isn\'t tax advice -- consult a Greek tax professional about your specific situation.' ) ),
            'regulatory_source_url' => 'https://www.hcmc.gr',
            'paragraphs' => array(
                'Greece joined the EU in 1981 and the eurozone in 2001. The HCMC supervises domestic financial markets, and -- being geographically and culturally close to Cyprus -- Greek retail traders are especially likely to already be familiar with CySEC-licensed brokers, many of which run Greek-language sites and support.',
                'No broker in our current dataset is headquartered in Greece itself, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'hungary' => array(
            'name' => 'Hungary', 'iso' => 'HU', 'currency' => 'HUF', 'eurozone' => false, 'eu_since' => 2004,
            'regulator' => 'MNB (Magyar Nemzeti Bank)', 'hq_match' => 'Hungary',
            'taxation_overview' => 'Non-official sources state a flat 15% personal income tax applies to short-term trading/capital gains, administered by NAV -- but this could not be confirmed against an official NAV source. A material complication: actual treatment may depend on whether the transaction qualifies as a "controlled capital market transaction," use of a TBSZ long-term investment account (which can reduce or eliminate tax on investment returns after 3-5 years), and potentially an additional social contribution tax on top of the 15% rate that wasn\'t independently confirmed. Genuinely unclear how these interact -- consult NAV or a Hungarian tax adviser directly.',
            'local_payment_methods' => 'Not found -- no Hungary-specific broker payment rail was confirmed; deposits cited are the generic set (bank transfer, Skrill, cards, crypto).',
            'country_specific_restrictions' => 'Yes -- a documented additional measure: after the EU-wide ESMA temporary measures lapsed, the MNB independently restricted CFD marketing to retail clients from August 2019, then issued a permanent national product-intervention decision (No. H-JE-III-10/2020, effective 10 April 2020) covering margin requirements, leverage, margin close-out, negative balance protection, and a ban on trading inducements. ESMA\'s 2019 opinion had criticized Hungary\'s initial, narrower 2019 approach as insufficiently protective before the fuller 2020 decision was judged proportionate.',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Hungary?', 'a' => 'Yes, through MNB-licensed or EU-passported firms. Hungary has its own national CFD product-intervention decision (No. H-JE-III-10/2020) on top of the general EU framework.' ), array( 'q' => 'Do I need to declare CFD/forex gains to NAV?', 'a' => 'Non-official sources describe a flat 15% rate, but how this interacts with social contribution tax and TBSZ long-term investment account rules isn\'t settled in available sources. This isn\'t tax advice -- check with NAV or a Hungarian tax adviser.' ) ),
            'regulatory_source_url' => 'https://www.mnb.hu/en/supervision',
            'paragraphs' => array(
                'Hungary joined the EU in 2004 and has not adopted the euro, retaining the forint (HUF). The Magyar Nemzeti Bank (MNB) acts as both central bank and financial regulator, enforcing the same EU-wide ESMA leverage caps and risk-disclosure rules on any broker serving Hungarian retail clients.',
                'No broker in our current dataset is headquartered in Hungary, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'ireland' => array(
            'name' => 'Ireland', 'iso' => 'IE', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1973,
            'regulator' => 'Central Bank of Ireland', 'hq_match' => 'Ireland',
            'local_entity_note' => 'AvaTrade is a genuine exception to the "passported CySEC entity" pattern on this site: it is headquartered in Dublin and its EU clients, including Irish residents, are typically served directly under the Central Bank of Ireland\'s own licence rather than a CySEC one. Ireland\'s investor compensation scheme (ICCL) may apply to that entity specifically -- confirm current coverage and terms directly with AvaTrade and the Central Bank of Ireland, since this review did not independently verify ICCL\'s exact current cap.',
            'taxation_overview' => 'The default position is that CFDs are capital assets subject to Capital Gains Tax (CGT) -- commonly cited at 33%, with a personal annual exemption on the first EUR1,270 of gains -- unless the activity is deemed to be carried on "in the course of a financial trade," in which case profits are instead taxed as ordinary income under Case I, Schedule D. Which applies depends on facts specific to you (frequency, organization, skill involved) and isn\'t something that can be stated definitively in general terms. Note spread betting is reportedly taxed differently from CFDs -- don\'t assume the two are treated the same. Consult Revenue.ie or an Irish tax adviser for your specific position.',
            'local_payment_methods' => 'Not found -- no Ireland-specific payment rail was confirmed; deposit methods cited are the generic set (SEPA bank transfer, cards, e-wallets, crypto). Sources disagreed on whether Skrill/Neteller are even available to Irish clients at some brokers -- an unresolved conflict, not a confirmed fact either way.',
            'country_specific_restrictions' => 'The Central Bank of Ireland considered an outright ban on CFDs in a 2017 consultation (citing average client losses around EUR6,900), but in 2019 did not adopt a full ban for CFDs (it did ban binary options outright). Instead it made the EU-wide ESMA restrictions (leverage caps, margin close-out, negative balance protection, incentive ban, risk warnings) permanent and national from 1 August 2019, rather than materially stricter -- a nuance worth stating precisely rather than overclaiming a unique Irish restriction.',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Ireland?', 'a' => 'Yes, via brokers authorized by the Central Bank of Ireland or passported from another EU/EEA regulator. Ireland made the ESMA-era retail CFD restrictions permanent under national law in August 2019, having considered but not adopted an outright ban.' ), array( 'q' => 'Do I need to declare CFD trading gains to Revenue?', 'a' => 'Generally, CFD gains are taxed as capital gains unless your trading pattern is frequent/organized enough to be treated as a trade, in which case income tax applies instead. Which applies depends on your specific facts -- this isn\'t advice; confirm your position with Revenue.ie or a tax adviser.' ) ),
            'regulatory_source_url' => 'https://www.centralbank.ie',
            'paragraphs' => array(
                'Ireland joined the EU in 1973 and the eurozone at its launch. It\'s genuinely home to one of the longest-established brokers in our rankings: AvaTrade is headquartered in Dublin, regulated there by the Central Bank of Ireland, with EU clients typically served through MiFID passporting rather than a separate CySEC licence.',
                'Ireland\'s an unusual case among the countries here in that respect -- most of our dataset is CySEC-licensed via Cyprus specifically, while Ireland has its own well-regarded domestic financial regulator that some brokers use as their primary EU base instead.',
            ),
        ),
        'italy' => array(
            'name' => 'Italy', 'iso' => 'IT', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'CONSOB (Commissione Nazionale per le Società e la Borsa)', 'hq_match' => 'Italy',
            'additional_eu_note' => 'CONSOB has taken an active enforcement role beyond the EU baseline: since 2019 it has published and periodically updated a public blacklist of websites offering financial services in Italy without the required authorization. That doesn\'t affect properly CySEC-licensed, EU-passported brokers like the ones reviewed here, but it\'s a reminder to verify a broker\'s actual licence status directly rather than relying on marketing claims.',
            'taxation_overview' => 'Multiple sources describe a flat 26% substitute tax ("imposta sostitutiva") on the net annual result (gains minus losses) of forex, CFD, and other OTC-derivative trading for individuals, categorized as miscellaneous financial income. Losses can reportedly be carried forward against similar future financial gains for up to 4 years. Two reporting regimes exist for Italian investors generally (administered vs. declarative), with gains typically declared via Modello 730 or Modello Redditi PF; the practical difference between the two regimes specifically for CFD/forex wasn\'t detailed in sources found. Consult the Agenzia delle Entrate or an Italian tax adviser for your specific filing.',
            'local_payment_methods' => 'Italy is the one country in this research pass with confidently-sourced local payment rails beyond the generic set: MyBank (an Italy-focused bank-transfer scheme) and PostePay (Poste Italiane\'s prepaid card, sometimes with a lower minimum deposit) both appear on broker/payment-processor documentation as supported Italy-specific deposit options.',
            'country_specific_restrictions' => 'Yes -- CONSOB has gone measurably beyond the EU baseline: under a 2019 "Growth Decree," CONSOB has statutory power to order Italian ISPs to block websites offering financial services without proper Italian authorization, and has used this power continuously since July 2019 -- cumulative blocked sites reportedly exceed 1,700 as of mid-2026. This website-blocking power is a genuinely distinctive Italian enforcement tool beyond what most other EU regulators do.',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Italy?', 'a' => 'Yes, through CONSOB-authorized or EU-passported brokers. Unlike in some other EU states, CONSOB actively orders Italian ISPs to block websites of unauthorized providers -- cumulative blocked sites have exceeded 1,700 since 2019. Check CONSOB\'s current warning list before depositing with an unfamiliar broker.' ), array( 'q' => 'Can I fund a broker account using PostePay or MyBank?', 'a' => 'Several brokers serving Italian clients reportedly support PostePay and MyBank as deposit methods, sometimes with lower minimum deposits. Availability varies by broker -- confirm directly with your chosen provider.' ) ),
            'regulatory_source_url' => 'https://www.consob.it',
            'paragraphs' => array(
                'A founding EU and eurozone member, Italy is supervised by CONSOB, which has taken an active enforcement role against unauthorized forex/CFD providers: since 2019 it has published and periodically updated a public blacklist of websites offering financial services in Italy without the required authorization.',
                'That doesn\'t affect properly CySEC-licensed, EU-passported brokers like the ones reviewed on this site, but it\'s a useful reminder to always verify a broker\'s actual licence status before depositing funds, rather than relying on marketing claims alone.',
            ),
        ),
        'latvia' => array(
            'name' => 'Latvia', 'iso' => 'LV', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'Latvijas Banka', 'hq_match' => 'Latvia',
            'taxation_overview' => 'Latvia\'s tax authority (VID) states a 25.5% personal income tax rate currently applies to capital gains for individuals (a transitional 20% rate applies to some capital-asset transactions started before 31 December 2024, through 2027). It could not be confirmed whether retail forex/CFD positions actually fall within VID\'s defined "capital asset" category for this tax, or a different category -- a genuine open question. Where income comes via a foreign broker (true for most brokers serving Latvian clients), the individual is responsible for self-declaring; no automatic withholding by a foreign broker should be assumed. Consult VID directly or a Latvian tax adviser.',
            'local_payment_methods' => 'Not found as broker-confirmed -- deposit methods cited for Latvian traders are generic (bank transfer/SEPA, commonly from Swedbank, SEB, or Citadele reflecting Latvia\'s Scandinavian-dominated retail banking sector, plus cards and e-wallets).',
            'country_specific_restrictions' => 'Latvia\'s national CFD/binary-options rules are set by Latvijas Banka\'s Regulation No. 362 (2024), which replaced the former FCMC\'s 2020 regulation and applies to both domestic and foreign firms serving Latvian residents -- a notable cross-border reach, though it largely mirrors the EU/ESMA baseline (leverage limits, margin close-out, negative balance protection). Whether it adds anything materially beyond the EU baseline, versus simply codifying it nationally, wasn\'t confirmed either way.',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Latvia?', 'a' => 'Yes, through a Latvijas Banka-authorized firm or one properly passported from another EU/EEA state. Latvijas Banka regulates retail CFD/binary-options marketing and sale under its own Regulation No. 362 (2024), which applies to both domestic and foreign firms serving Latvian residents.' ), array( 'q' => 'Do I need to declare CFD/forex trading gains to the VID?', 'a' => 'Latvia applies a 25.5% personal income tax on capital gains generally, and if your gains come via a foreign broker you\'re responsible for self-declaring them -- but whether CFD/forex gains specifically fall under this category isn\'t independently confirmed. Consult the VID directly or a Latvian tax adviser.' ) ),
            'regulatory_source_url' => 'https://www.bank.lv/en',
            'paragraphs' => array(
                'Latvia joined the EU in 2004 and the eurozone in 2014. Financial supervision, previously handled by a standalone regulator (the FCMC), was folded into the central bank, Latvijas Banka, in 2023. The same EU-wide ESMA leverage and marketing rules apply to any broker serving Latvian retail clients.',
                'No broker in our current dataset is headquartered in Latvia, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'lithuania' => array(
            'name' => 'Lithuania', 'iso' => 'LT', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'Bank of Lithuania', 'hq_match' => 'Lithuania',
            'taxation_overview' => 'Treatment is genuinely unclear and in flux: Lithuania is moving to a progressive personal income tax system from 2026 for most aggregated income, while certain items (e.g. qualifying long-term share gains) stay outside aggregation at a flat 15%. No source specifically addresses how the State Tax Inspectorate (VMI) classifies forex/CFD gains under the new regime -- whether aggregated progressive income or a flat-rate capital gain likely depends on trading frequency and classification as business activity versus occasional investment, which isn\'t resolved in available sources. Consult a Lithuanian tax adviser or VMI directly, especially given the 2026 transition.',
            'local_payment_methods' => 'Not found as broker-specific. Lithuania has bank-redirect "Bank Link" and newer payment-initiation methods via providers like Paysera, but no source confirms these are used for broker deposits specifically.',
            'country_specific_restrictions' => 'The Bank of Lithuania applied the EU-wide ESMA-aligned retail CFD measures around 2019, plus a binary options ban, largely mirroring the EU baseline rather than adding something materially beyond it. One distinct national tool: the Bank of Lithuania maintains a public list (reportedly 300+ entities) of firms offering investment services without authorization -- a consumer-protection blacklist separate from the EU rules.',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Lithuania?', 'a' => 'Yes -- trading via an authorized or EU-passported investment firm is legal; the Bank of Lithuania enforces the EU-wide retail protections and separately maintains a public warning list of unauthorized providers, worth checking before funding an account.' ), array( 'q' => 'Do I need to declare CFD gains to VMI?', 'a' => 'Likely yes in some form, but the exact tax treatment (flat rate vs. progressive) isn\'t settled in available sources, especially given Lithuania\'s 2026 tax changes -- consult a Lithuanian tax adviser or VMI directly.' ) ),
            'regulatory_source_url' => 'https://www.lb.lt/en',
            'paragraphs' => array(
                'Lithuania joined the EU in 2004 and the eurozone in 2015. The Bank of Lithuania handles financial supervision alongside its central-banking role, and has also positioned the country as a notable fintech licensing hub in the Baltics -- though for CFD/forex brokers specifically, CySEC (via Cyprus) remains the far more common EU licence of choice among the firms reviewed here.',
                'No broker in our current dataset is headquartered in Lithuania itself, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'luxembourg' => array(
            'name' => 'Luxembourg', 'iso' => 'LU', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'CSSF (Commission de Surveillance du Secteur Financier)', 'hq_match' => 'Luxembourg',
            'taxation_overview' => 'Luxembourg has progressive personal income tax up to 42% plus a solidarity surcharge, giving a maximum combined marginal rate around 45.8%. For gains on movable assets held as private property: generally tax-exempt if held longer than 6 months; fully taxable as miscellaneous income at progressive rates if held 6 months or less. If trading activity amounts to a "business" (assessed via trader-vs-investor tests), gains are instead taxed as business income. No source specifically confirms how forex/CFD derivatives are classified, though their typically short-term, leveraged nature suggests the short-term/trader rules are more likely to apply than the 6-month exemption -- this is inference, not a confirmed rule. Consult a Luxembourg tax adviser or the Administration des contributions directes.',
            'local_payment_methods' => 'Payconiq was Luxembourg\'s most-used digital payment method by some measures, but a December 2025 changelog entry indicates it was being decommissioned in favor of Bancontact, with the Benelux iDEAL network separately rebranding to "Wero" through 2026-2027. No source confirms any broker accepts these for CFD/forex deposits specifically -- not found at the broker level.',
            'country_specific_restrictions' => 'CSSF Regulation No. 19-06 (effective 1 August 2019) restricts CFD marketing, distribution, and sale to retail clients, and a parallel regulation bans binary options -- both explicitly described by the CSSF as in substance the same as ESMA\'s EU-wide measures, largely mirroring rather than exceeding the EU baseline. The CSSF separately issues ad hoc public warnings against unauthorized FX and crypto entities.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in Luxembourg?', 'a' => 'Yes, via CSSF-authorized or EU-passported firms, subject to the same ESMA-aligned retail protections applied nationally through CSSF Regulation 19-06.' ), array( 'q' => 'Do I need to declare my CFD/forex trading gains to the Luxembourg tax authorities?', 'a' => 'General securities-gains rules distinguish short-term (taxable) from long-term (often exempt) holdings and a trader-vs-investor test for business-income treatment, but no source specifically confirms how forex/CFD derivatives are classified -- consult a Luxembourg tax adviser.' ) ),
            'regulatory_source_url' => 'https://www.cssf.lu',
            'paragraphs' => array(
                'A founding EU and eurozone member, Luxembourg is supervised by the CSSF. Its financial sector is heavily weighted toward fund administration, private banking, and cross-border wealth management rather than retail CFD/forex trading, which is a comparatively small niche in the domestic market.',
                'No broker in our current dataset is headquartered in Luxembourg, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'malta' => array(
            'name' => 'Malta', 'iso' => 'MT', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'MFSA (Malta Financial Services Authority)', 'hq_match' => 'Malta',
            'taxation_overview' => 'Malta has progressive personal income tax up to 35% for Malta-domiciled residents, applied to worldwide capital gains including gains classified as "trading" income. Maltese tax advisers commonly cite the "Badges of Trade" test (borrowed from UK case law) to distinguish passive investment from active trading based on frequency, profit motive, and similar factors -- active trading "carried out from Malta" can make gains taxable up to 35% even via a foreign broker. No official MFSA or Commissioner for Revenue text specifically addressing forex/CFDs was found; figures here come from private tax-advisory firms. Consult the Commissioner for Revenue or a Maltese tax adviser for your specific case.',
            'local_payment_methods' => 'Not found. No Malta-specific local payment rail comparable to BLIK or iDEAL was found in connection with retail trading deposits -- generic cards, bank wire, and e-wallets appear standard.',
            'country_specific_restrictions' => 'The MFSA amended its Conduct of Business Rulebook to impose permanent national restrictions on CFD (including rolling spot forex) marketing to retail clients, built on the same substance as ESMA\'s temporary measures -- ESMA itself reviewed and found Malta\'s national measures justified and proportionate. Firms offering CFDs/forex to retail clients need an MFSA Category 2 or 3 investment-services licence. The MFSA also issues frequent public warnings against specific unauthorized or clone brokers, sometimes jointly with other regulators.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in Malta?', 'a' => 'Yes, through an MFSA-licensed (Category 2/3) or EU-passported firm; Malta has made ESMA-style retail protections permanent rather than just following temporary EU renewals.' ), array( 'q' => 'How do I check if a broker claiming to be "Malta-licensed" is genuine?', 'a' => 'The MFSA has repeatedly warned about clone/impersonation brokers falsely claiming Maltese registration -- check the MFSA\'s official register and warning list directly before depositing funds.' ) ),
            'regulatory_source_url' => 'https://www.mfsa.mt',
            'paragraphs' => array(
                'Malta joined the EU in 2004 and the eurozone in 2008. The MFSA has built its own reputation as a licensing hub for online financial and gaming businesses, in some ways a smaller parallel to Cyprus\'s role for CFD brokers specifically -- though CySEC remains the dominant EU licence among the brokers reviewed on this site.',
                'No broker in our current dataset is headquartered in Malta itself, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'netherlands' => array(
            'name' => 'Netherlands', 'iso' => 'NL', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'AFM (Autoriteit Financiële Markten)', 'hq_match' => 'Netherlands',
            'taxation_overview' => 'The Netherlands taxes most private investment assets under Box 3, a deemed/fictional-return wealth tax rather than a tax on actual realized trading gains -- meaning CFD/forex losses don\'t automatically reduce the Box 3 tax base the way they would under a capital-gains system. The Box 3 tax rate is 36%, with an annual tax-free allowance and a deemed investment return that changes each year. No source confirms specifically how a margin/CFD trading account balance is classified within Box 3, versus the possibility that frequent or leveraged trading could instead be treated as taxable business income under a different category -- a genuine open question. Consult the Belastingdienst or a Dutch tax adviser.',
            'local_payment_methods' => 'iDEAL is the clearly dominant Dutch online payment method, run by major Dutch banks via direct bank-redirect, and several brokers serving Dutch clients are reported to accept it for deposits. Note iDEAL is reportedly being rebranded to "Wero" during 2026-2027 following an acquisition, and iDEAL/Wero is often deposit-only (withdrawals may go via standard bank transfer instead) -- confirm per broker.',
            'country_specific_restrictions' => 'The AFM was one of the first EU regulators to make its national CFD/binary-options restrictions permanent (effective 19 April 2019) rather than relying on ESMA\'s temporary renewals. In October 2021, the AFM extended similar leverage/marketing restrictions to Turbo warrants (a leveraged product popular in the Netherlands, treated as having a CFD-like risk profile) -- a genuinely Netherlands-specific extension beyond the EU/ESMA CFD baseline. The AFM also maintains a public warning list of unauthorized firms.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in the Netherlands?', 'a' => 'Yes, through an AFM-licensed or properly EU-passported firm; the Netherlands was an early adopter of permanent (not just temporary) CFD restrictions, and separately extended similar protections to Turbo warrants in 2021.' ), array( 'q' => 'Can I use iDEAL to fund a broker account?', 'a' => 'Yes, iDEAL is widely supported by several brokers serving Dutch clients for deposits, though it\'s being rebranded to "Wero" in 2026-2027 and may be deposit-only -- check your specific broker\'s payment page.' ) ),
            'regulatory_source_url' => 'https://www.afm.nl',
            'paragraphs' => array(
                'A founding EU and eurozone member, the Netherlands is supervised by the AFM, which actively enforces the EU-wide requirement that CFD marketing display a clear percentage-of-retail-accounts-lose-money risk warning, along with the standard ESMA leverage caps.',
                'No broker in our current dataset is headquartered in the Netherlands, so the ranking below reflects our overall EU-wide scoring rather than a confirmed local presence.',
            ),
        ),
        'poland' => array(
            'name' => 'Poland', 'iso' => 'PL', 'currency' => 'PLN', 'eurozone' => false, 'eu_since' => 2004,
            'regulator' => 'KNF (Komisja Nadzoru Finansowego)', 'hq_match' => 'Poland',
            'local_entity_note' => 'XTB is a genuine Polish success story in this industry: founded in Warsaw and still listed on the Warsaw Stock Exchange, it\'s Poland\'s own home-grown CFD/forex brokerage. Even so, its EU retail clients -- including Polish residents -- are served through its CySEC-licensed entity, so the Cyprus ICF (&euro;20,000 cap) is what applies, not a Polish-specific scheme, despite XTB\'s Polish origins and listing.',
            'taxation_overview' => 'Retail forex/CFD gains fall under Poland\'s flat 19% capital gains tax (the "Belka tax") on financial instruments, reported on form PIT-38 by 30 April of the following year. Only realized (closed-position) gains are taxed. Polish tax residents using foreign/offshore brokers must self-report, since foreign brokers typically don\'t issue the PIT-8C statement Polish brokers provide. Sources conflict on loss treatment -- whether losses can only offset gains within the same year, or carry forward -- so confirm this specific point with a Polish tax adviser or the tax authority (Krajowa Administracja Skarbowa).',
            'local_payment_methods' => 'BLIK, Poland\'s widely used mobile one-time-code payment system, is reported as a commonly supported deposit method at some brokers serving Polish clients. Important caveat: some BLIK-offering brokers aren\'t KNF-licensed and may onboard Polish clients under offshore entities with non-EU-compliant leverage -- BLIK availability itself isn\'t a sign of regulatory compliance.',
            'country_specific_restrictions' => 'Beyond the EU/ESMA baseline, the KNF created a distinctive "experienced retail client" carve-out (effective 2019): clients meeting KNF-defined experience criteria can trade major FX pairs, gold, and a defined list of major indices at up to 1:100 leverage, versus the standard 1:30 EU retail cap -- a genuine Poland-specific deviation. This rule applies to all firms offering CFDs into Poland, including EU-passported ones. The KNF also maintains a long-running public warning list naming unlicensed forex/CFD operators.',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Poland?', 'a' => 'Yes, through a KNF-licensed or EU-passported firm. Poland is notable for its "experienced retail client" rule allowing leverage up to 1:100 on specified instruments (versus the standard 1:30 EU retail cap) for clients meeting KNF-defined experience criteria.' ), array( 'q' => 'Can I use BLIK to fund a broker account?', 'a' => 'Yes, several brokers accept BLIK deposits from Polish clients, but BLIK support alone doesn\'t confirm KNF licensing -- always separately verify the broker against the KNF\'s official register and public warning list before depositing funds.' ) ),
            'regulatory_source_url' => 'https://www.knf.gov.pl/en',
            'paragraphs' => array(
                'Poland joined the EU in 2004 and has not adopted the euro, retaining the złoty (PLN). It\'s genuinely home to one of the largest brokers in our entire rankings: XTB was founded in Warsaw and remains listed on the Warsaw Stock Exchange, making it Poland\'s own home-grown entry in the CFD/forex brokerage industry, even though EU retail clients are served through its CySEC-licensed entity.',
                'The KNF regulates domestic financial markets and, alongside the EU-wide ESMA framework, applies its own scrutiny to leveraged retail products -- worth knowing given how prominent Polish-founded brokers are in this space.',
            ),
        ),
        'portugal' => array(
            'name' => 'Portugal', 'iso' => 'PT', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1986,
            'regulator' => 'CMVM (Comissão do Mercado de Valores Mobiliários)', 'hq_match' => 'Portugal',
            'taxation_overview' => 'Retail individuals\' CFD gains are generally treated as capital gains, taxed under IRS at a flat autonomous rate of 28%, or via the taxpayer\'s progressive IRS bracket if "aggregation" (englobamento) is elected. Only the net balance of gains/losses for the period is taxed; CFD losses can reportedly only be offset against gains from other derivative instruments, not other investment income. Whether spot forex specifically sits on Portugal\'s taxable asset-category list (as opposed to CFDs) is a genuine open question in sources found. Consult a Portuguese certified accountant (contabilista certificado) or the Autoridade Tributaria.',
            'local_payment_methods' => 'MB WAY, a mobile payment app built on Portugal\'s Multibanco/SIBS interbank network, is very widely used domestically and has been offered as a deposit rail by at least one e-wallet provider used by traders -- though broker-level support should be verified broker by broker.',
            'country_specific_restrictions' => 'CMVM Regulation No. 5/2019 implemented the EU/ESMA CFD and binary-options restrictions into permanent Portuguese law -- substantially mirroring the EU-wide baseline rather than adding materially new restrictions beyond it, though it made the restriction permanent in Portuguese law specifically. No additional Portugal-specific rule (e.g. an extra registration regime for foreign firms beyond standard MiFID II passporting) was confirmed.',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Portugal?', 'a' => 'Yes -- trading through a CMVM-authorised firm, or an EU/EEA firm passporting into Portugal under MiFID II, is legal. CFD marketing to retail clients is restricted under CMVM Regulation 5/2019, and binary options marketing to retail clients is banned.' ), array( 'q' => 'Can I fund a broker account using MB WAY?', 'a' => 'MB WAY is a widely used Portuguese mobile payment method, and some payment providers support it, but not all forex/CFD brokers do -- check your specific broker\'s deposit page.' ) ),
            'regulatory_source_url' => 'https://www.cmvm.pt',
            'paragraphs' => array(
                'Portugal joined the EU in 1986 and the eurozone at its launch. The CMVM supervises domestic securities markets and enforces the same EU-wide ESMA leverage caps and marketing restrictions that apply to any broker serving Portuguese retail clients.',
                'No broker in our current dataset is headquartered in Portugal, so the ranking below reflects our overall EU-wide scoring rather than a confirmed local footprint.',
            ),
        ),
        'romania' => array(
            'name' => 'Romania', 'iso' => 'RO', 'currency' => 'RON', 'eurozone' => false, 'eu_since' => 2007,
            'regulator' => 'ASF (Autoritatea de Supraveghere Financiară)', 'hq_match' => 'Romania',
            'taxation_overview' => 'Romanian tax rules reportedly changed as of 2026: gains via a Romania-resident intermediary are taxed at 3% (positions held 365+ days) or 6% (held under 365 days), withheld by the intermediary; gains via a non-Romanian (foreign) broker are instead self-assessed at a reported 16% on net annual gains, filed directly with ANAF. This is a recent change and sources show some disagreement on exact figures -- confirm current rates with ANAF (anaf.ro) or a licensed Romanian accountant (contabil autorizat) before relying on any specific number.',
            'local_payment_methods' => 'Not found. Romanian retail payment habits skew toward cash-on-delivery for e-commerce generally, with bank cards and transfers as the main digital rails -- no evidence of a Romania-specific rail used for broker deposits.',
            'country_specific_restrictions' => 'The ASF requires that third-country (non-EU/EEA) firms serve Romanian clients only through an ASF-authorised branch, unless the client initiated contact entirely unprompted ("reverse solicitation") -- a genuine Romania-specific layer beyond the EU baseline for non-EU firms. Using an unauthorized entity means no protection under Romania\'s Investor Compensation Fund. The ASF also actively publishes investor alerts on unauthorized entities (172 entities, ~199 related websites, per a 2023 report).',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Romania?', 'a' => 'Yes, through an ASF-authorised firm or an EU/EEA firm passporting in -- check ASF\'s public register first, since ASF has repeatedly warned about unauthorised entities and fraudulent "authorisation" documents.' ), array( 'q' => 'Can a firm based outside the EU legally offer me CFD trading in Romania?', 'a' => 'Only through an ASF-authorised branch, unless you contacted them entirely on your own initiative (reverse solicitation) -- otherwise the firm is operating without authorisation and you have no Investor Compensation Fund protection.' ) ),
            'regulatory_source_url' => 'https://asfromania.ro',
            'paragraphs' => array(
                'Romania joined the EU in 2007 and has not adopted the euro, retaining the leu (RON). The ASF supervises domestic non-banking financial markets, working alongside the EU-wide ESMA framework that governs any CySEC-passported broker serving Romanian retail clients.',
                'No broker in our current dataset is headquartered in Romania, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'slovakia' => array(
            'name' => 'Slovakia', 'iso' => 'SK', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'NBS (Národná banka Slovenska)', 'hq_match' => 'Slovakia',
            'taxation_overview' => 'No authoritative Slovakia-specific forex/CFD tax source was found. General individual income tax is progressive (commonly cited bands around 19%/25%, though thresholds appear to have changed between 2025 and 2026 and sources conflict). Whether CFD gains qualify for Slovakia\'s known securities exemption (for shares/ETFs held over 12 months on a regulated market) is unconfirmed, since CFDs aren\'t shares or ETF units. Whether gains count as "other income" or capital income, and whether losses offset other income, is unresolved -- consult Financna sprava (the Slovak tax authority) or a local tax adviser.',
            'local_payment_methods' => 'Not found as broker-specific. Slovakia has several locally popular bank-transfer schemes (TatraPay, ePlatby VUB, SporoPay) used in e-commerce generally, but no source confirms any forex/CFD broker actually supports these specifically.',
            'country_specific_restrictions' => 'No Slovakia-specific CFD/forex restriction beyond the EU/ESMA baseline was found as a general rule. The NBS has issued numerous individual warnings against specific unauthorized providers over the years (a case-by-case enforcement pattern, not a standing additional regulation) and maintains a public register of authorised entities.',
            'faqs' => array( array( 'q' => 'Is forex/CFD trading legal in Slovakia?', 'a' => 'Yes, via an NBS-authorised firm or EU/EEA-passported firm. NBS has issued multiple warnings about specific unlicensed providers over the years -- check NBS\'s public register (subjekty.nbs.sk) before using a platform.' ), array( 'q' => 'How are CFD/forex gains taxed in Slovakia?', 'a' => 'Not independently confirmed -- sources disagree, and Slovakia\'s general progressive income tax bands appear to have changed for 2026. Consult Financna sprava or a local tax adviser rather than relying on any single online figure.' ) ),
            'regulatory_source_url' => 'https://www.nbs.sk',
            'paragraphs' => array(
                'Slovakia joined the EU in 2004 and the eurozone in 2009. The National Bank of Slovakia (NBS) handles financial supervision alongside its central-banking role, applying the same EU-wide ESMA leverage caps and marketing rules as the rest of the bloc.',
                'No broker in our current dataset is headquartered in Slovakia, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'slovenia' => array(
            'name' => 'Slovenia', 'iso' => 'SI', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'ATVP (Agencija za trg vrednostnih papirjev)', 'hq_match' => 'Slovenia',
            'taxation_overview' => 'Pre-2026, gains on derivative financial instruments were reportedly taxed on a sliding scale by holding period (as high as 40% under one year, down to 10% after 15-20 years). From tax year 2026, secondary sources indicate this moved to a flat 25% rate on derivative gains regardless of holding period. Whether spot forex is itself classified as a "derivative financial instrument" under Slovenian law (determining which regime applies) wasn\'t confirmed. Consult FURS (Financna uprava RS) or a Slovenian tax adviser for your specific position.',
            'local_payment_methods' => 'Not found. Cards are the dominant online payment method in Slovenia with bank transfer as a secondary option; no Slovenia-specific rail analogous to BLIK or Swish was identified as broker-relevant.',
            'country_specific_restrictions' => 'In 2019, ESMA gave a positive assessment of Slovenia\'s national CFD/binary-options measures: a permanent restriction on CFD marketing to retail clients mirroring the expired EU-wide temporary measures, plus an outright ban on binary options marketing to retail clients. Whether this 2019 measure remains unchanged today wasn\'t confirmed from current sources. ATVP separately issues individual warnings against unauthorized providers.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in Slovenia?', 'a' => 'Yes, through an ATVP-authorised firm or an EU/EEA passported firm -- but Slovenia made its CFD marketing restriction to retail clients permanent (2019), and binary options marketing to retail clients is banned outright. Check ATVP\'s authorised-firm register before using any platform.' ), array( 'q' => 'Do I need to declare CFD/forex gains to FURS?', 'a' => 'Likely yes, but the exact classification and rate aren\'t fully settled in available sources -- secondary sources point to a flat 25% rate on derivative gains from 2026, but whether spot forex counts as a "derivative instrument" under Slovenian law isn\'t confirmed. Consult FURS or a Slovenian tax adviser.' ) ),
            'regulatory_source_url' => 'https://www.a-tvp.si',
            'paragraphs' => array(
                'Slovenia joined the EU in 2004 and the eurozone in 2007. The ATVP supervises domestic securities markets, working within the same EU-wide ESMA leverage and marketing framework that applies to every CySEC-licensed broker passporting into the country.',
                'No broker in our current dataset is headquartered in Slovenia, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'spain' => array(
            'name' => 'Spain', 'iso' => 'ES', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1986,
            'regulator' => 'CNMV (Comisión Nacional del Mercado de Valores)', 'hq_match' => 'Spain',
            'additional_eu_note' => 'Like Italy\'s CONSOB, the CNMV maintains and regularly updates a public warning list of unauthorized firms offering forex and CFD services to Spanish residents without the required licence -- a real enforcement effort beyond the EU baseline, though it doesn\'t affect properly CySEC-licensed, EU-passported brokers like the ones reviewed here.',
            'taxation_overview' => 'Retail CFD/forex gains are generally treated as capital gains falling in the IRPF "savings income" category, taxed on Spain\'s progressive savings scale (commonly cited: 19% up to EUR6,000, rising through bands to 30% above EUR300,000) rather than a flat rate -- though sources disagree on the exact current top-bracket figure. Brokers generally don\'t withhold tax on CFD/forex profits in Spain, so the trader self-declares via the annual Modelo 100 return. Losses can reportedly be offset against gains from other investment types and carried forward up to 4 years. Confirm current bracket thresholds with the Agencia Tributaria (AEAT) or a Spanish asesor fiscal rather than relying on a single secondary source.',
            'local_payment_methods' => 'Bizum, a Spanish bank-linked mobile payment system, is very widely used domestically, but it requires a Spanish-bank acquiring agreement on the merchant side -- no source directly confirmed a specific forex/CFD broker currently accepting Bizum deposits.',
            'country_specific_restrictions' => 'CNMV adopted permanent, EU/ESMA-aligned CFD restrictions from August 2019, plus a permanent binary options marketing ban. Notably, in July 2023 CNMV went further than the EU baseline: a new Resolution added restrictions specifically banning certain aggressive CFD marketing practices aimed at retail clients -- including use of sales agents, call centres, or software providers to recruit retail investors -- stating the 2019 measures had shown "limited effectiveness" (roughly 75% of retail CFD accounts were still losing money). This 2023 expansion is a genuine Spain-specific restriction beyond the EU/ESMA baseline.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in Spain?', 'a' => 'Yes, through a CNMV-authorised firm or an EU/EEA passported firm. Spain has gone further than the EU baseline: CNMV made the 2019 CFD retail-marketing restrictions and binary-options ban permanent, and in 2023 added further restrictions specifically banning certain aggressive CFD marketing practices (e.g. cold-calling, sales-agent recruitment) aimed at retail clients.' ), array( 'q' => 'Do I need to declare CFD/forex trading gains to the Agencia Tributaria?', 'a' => 'Yes -- brokers generally don\'t withhold tax on these gains in Spain, so you self-declare them as savings-category capital gains on your annual Modelo 100 return. Confirm current bracket thresholds with AEAT or a Spanish tax adviser, not with any single online summary.' ) ),
            'regulatory_source_url' => 'https://www.cnmv.es',
            'paragraphs' => array(
                'Spain joined the EU in 1986 and the eurozone at its launch. Like Italy\'s CONSOB, the CNMV maintains and regularly updates a public warning list of unauthorized firms offering forex and CFD services to Spanish residents without the required licence.',
                'That enforcement stance doesn\'t affect properly CySEC-licensed, EU-passported brokers like the ones reviewed on this site, but it underlines why checking a broker\'s actual regulatory status -- not just its marketing -- matters before depositing funds anywhere.',
            ),
        ),
        'sweden' => array(
            'name' => 'Sweden', 'iso' => 'SE', 'currency' => 'SEK', 'eurozone' => false, 'eu_since' => 1995,
            'regulator' => 'Finansinspektionen', 'hq_match' => 'Sweden',
            'taxation_overview' => 'Sweden\'s tax authority (Skatteverket) treats CFD contracts as a futures-like instrument rather than ordinary shares -- gains/losses are reported the same way as ordinary futures contracts, implying losses are generally deductible under the current approach. However, an older Skatteverket position reportedly held that CFD losses were not deductible, creating a genuine conflict between an older and a more current source that shouldn\'t be resolved by guessing. Brokers are reportedly required to issue a control statement (kontrolluppgift) to Skatteverket for each CFD contract they helped close. Consult Skatteverket\'s own CFD guidance or a Swedish tax adviser given this unresolved point.',
            'local_payment_methods' => 'Swish, Sweden\'s dominant mobile/bank-linked payment system, is extremely widely used domestically, but no forex/CFD broker was confirmed in this research to actually accept Swish deposits -- it would require the broker to hold a Swedish bank merchant agreement, which wasn\'t confirmed for any specific broker.',
            'country_specific_restrictions' => 'Finansinspektionen issued its own national product-intervention regulation for CFDs (FFFS 2019:7, in force from August 2019) after the EU-wide temporary measure wasn\'t renewed, making Sweden\'s CFD restrictions permanent under Swedish law -- plus a parallel permanent binary-options prohibition (FFFS 2019:8). This is essentially the ESMA framework adopted permanently and nationally, similar to several other EU states, rather than a materially stricter extra layer.',
            'faqs' => array( array( 'q' => 'Is CFD/forex trading legal in Sweden?', 'a' => 'Yes, through an FI-authorised firm or an EU/EEA passported firm. Sweden made the ESMA-style CFD retail-marketing restrictions and a binary-options ban permanent under its own regulations (FFFS 2019:7 and 2019:8) rather than relying on the original temporary EU-wide measure.' ), array( 'q' => 'Do I need to declare CFD/forex gains to Skatteverket?', 'a' => 'Yes. Skatteverket currently treats CFDs similarly to futures contracts for tax purposes, and brokers are generally required to send a control statement for each contract. Whether CFD losses are deductible isn\'t entirely clear-cut -- consult Skatteverket\'s own CFD guidance or a Swedish tax adviser rather than assuming either answer.' ) ),
            'regulatory_source_url' => 'https://www.fi.se',
            'paragraphs' => array(
                'Sweden joined the EU in 1995 and, like Denmark, has not adopted the euro, retaining the krona (SEK) following a 2003 referendum. Finansinspektionen supervises domestic financial markets and enforces the same EU-wide ESMA leverage caps and marketing restrictions as the rest of the bloc.',
                'No broker in our current dataset is headquartered in Sweden, so the ranking below reflects our overall EU-wide scoring rather than a confirmed local presence.',
            ),
        ),
    );
}

/**
 * Country pages beyond the 27 EU member states above. These do NOT use
 * the EU/MiFID-II/ESMA framework the functions above assume -- each one
 * has its own regulator, licensing regime, and (where it exists at all)
 * leverage cap, so they're kept in a separate array rather than forced
 * into the EU shape. Every entry below carries 'region_type' => 'other'
 * so templates can branch instead of silently reusing EU-only copy.
 *
 * Chosen using a real, already-present signal -- which Tier-1 regulators
 * actually show up in globalfxhub_get_brokers()'s 'other_reg' field --
 * rather than guessing at search demand: ASIC (Australia, confirmed for
 * 31 of our reviewed brokers), FSCA (South Africa, 30), DFSA (UAE, 9),
 * and MAS (Singapore, 3) are the only four with enough confirmed
 * same-regulator coverage to support a real ranking. 'regulator_match'
 * is the exact string used in that field, used by
 * globalfxhub_country_broker_ranking_other_reg() below -- a stronger
 * claim than the EU pages' passporting inference, since it only ranks
 * brokers with a *confirmed* licence from that specific regulator, not
 * every CySEC broker that merely has the right to passport in.
 *
 * Several fields below are deliberately left as an honest "not
 * independently confirmed" rather than guessed -- consistent with every
 * other per-country field in this file -- where the research done for
 * this page didn't turn up a authoritative, current primary source.
 */
function globalfxhub_get_non_eu_countries() {
    return array(
        'australia' => array(
            'name' => 'Australia', 'iso' => 'AU', 'currency' => 'AUD', 'region_type' => 'other',
            'regulator' => 'ASIC (Australian Securities & Investments Commission)', 'regulator_short' => 'ASIC',
            'regulator_match' => 'ASIC', 'hq_match' => 'Australia',
            'framework_overview' => 'Any firm offering CFDs to Australian retail clients needs an Australian Financial Services (AFS) licence from ASIC authorising it to deal in and/or advise on CFDs. Since 29 March 2021, ASIC\'s CFD product intervention order has applied on top of the licence: leverage caps by asset class (below), standardised margin close-out rules designed to automatically close a losing position before a client\'s account is wiped out, and a ban on trading inducements such as account-opening or deposit rebates. The order followed near-identical UK and EU measures, prompted partly by heavy retail CFD losses during 2020\'s COVID-19 volatility. Whether the order (originally time-limited) has since been made permanent or renewed wasn\'t independently confirmed from the sources checked for this page -- see ASIC\'s own CFD product intervention page for its current status.',
            'leverage_overview' => 'ASIC\'s order caps retail CFD leverage by underlying asset: 30:1 on major currency pairs, 20:1 on minor currency pairs, gold, and major stock indices, 10:1 on other commodities and minor stock indices, 5:1 on shares, and 2:1 on crypto-asset CFDs -- a steep cut from the leverage of up to 500:1 some retail accounts reportedly reached beforehand. A client who qualifies as a "wholesale" investor (Australia\'s rough equivalent of "professional") can apply for higher leverage, but gives up the retail protections that come with the cap.',
            'compensation_overview' => 'Australia has no CFD/forex-specific investor compensation scheme comparable to Cyprus\'s ICF or the UK\'s FSCS. The Australian Financial Complaints Authority (AFCA) can hear complaints against AFS licensees and order compensation, but only case-by-case on a complaint it upholds -- it isn\'t a standing fund that automatically pays out if a broker becomes insolvent. Not independently confirmed here: whether any indemnity-insurance requirement attached to an AFS licence would itself cover client losses from a broker failure.',
            'taxation_overview' => 'Not independently confirmed in the depth this page would need -- in general, the ATO tends to treat gains from speculative CFD/forex trading as assessable income (and losses as deductible) under ordinary income-tax rules rather than the capital-gains discount that applies to long-term investment assets, but your own treatment depends on whether the ATO views your activity as a "business" of trading. Consult a registered Australian tax agent for your specific position.',
            'local_payment_methods' => 'Not independently confirmed broker-by-broker for this page -- Australian clients are typically offered bank transfer (including BPAY/PayID where a broker supports it), debit/credit card, and e-wallets such as Skrill/Neteller, but confirmed support varies by broker.',
            'country_specific_restrictions' => 'Beyond the leverage caps above, ASIC\'s order restricts specific sales practices, including a ban on inducements to trade. Contravention carries penalties of up to 5 years\' imprisonment for individuals and substantial civil penalties for corporations, and a client harmed by a breach may be able to recover losses through AFCA or the courts.',
            'faqs' => array(
                array( 'q' => 'Is CFD trading legal in Australia?', 'a' => 'Yes, if offered by an ASIC-licensed AFS holder complying with ASIC\'s CFD product intervention order -- leverage caps and standardised margin close-out rules in force since March 2021.' ),
                array( 'q' => 'What leverage can I get as a retail CFD trader in Australia?', 'a' => 'ASIC caps it by asset class: 30:1 on major FX pairs down to 2:1 on crypto-asset CFDs. Classifying as a "wholesale" investor can unlock higher leverage but removes the retail protections that come with the cap.' ),
            ),
            'regulatory_source_url' => 'https://asic.gov.au/',
            'paragraphs' => array(
                'Australia regulates CFDs and margin forex directly through ASIC rather than via any EU framework -- a firm needs its own Australian Financial Services licence, not a passported EU one, to serve Australian retail clients.',
                'ASIC\'s 2021 CFD product intervention order brought Australian retail leverage caps roughly in line with the EU\'s own ESMA limits, after years of Australian retail accounts being able to reach far higher leverage than their EU counterparts.',
            ),
        ),
        'south-africa' => array(
            'name' => 'South Africa', 'iso' => 'ZA', 'currency' => 'ZAR', 'region_type' => 'other',
            'regulator' => 'FSCA (Financial Sector Conduct Authority)', 'regulator_short' => 'FSCA',
            'regulator_match' => 'FSCA', 'hq_match' => 'South Africa',
            'framework_overview' => 'A firm providing advice or intermediary services on CFDs or forex to South African clients -- including marketing and sales -- needs an FSCA licence under the Financial Advisory and Intermediary Services (FAIS) Act; forex-specific intermediation generally requires a Category I Financial Services Provider (FSP) licence. A FAIS/FSP licence alone doesn\'t let a firm issue CFDs as principal (take the other side of client trades) -- that additionally requires an OTC Derivative Provider (ODP) licence under the Financial Markets Act. Applicants must appoint a South-Africa-resident Key Individual, a local representative, and a compliance officer, each individually registered with the FSCA. A long-pending Conduct of Financial Institutions (COFI) Act would eventually replace FAIS and consolidate several financial-sector statutes, but its current parliamentary stage and effective date weren\'t independently confirmed here -- check the FSCA directly.',
            'leverage_overview' => 'No FSCA-mandated retail leverage cap for forex/CFDs comparable to ESMA\'s or ASIC\'s tiered limits was independently confirmed from the sources checked for this page. Leverage offered to South African retail clients varies by broker and by which entity actually governs the account -- an FSCA-licensed one versus an offshore entity merely accepting South African clients -- so confirm the specific cap on your account directly with the broker and the FSCA\'s public register rather than assuming an EU-style limit applies.',
            'compensation_overview' => 'South Africa has no FSCA-administered investor compensation fund for CFD/forex broker failure comparable to Cyprus\'s ICF. The FSCA\'s role is licensing and conduct supervision -- including public warnings about unlicensed offshore platforms -- not a standing payout scheme for client losses if a licensed broker becomes insolvent. Not independently confirmed here: any client-asset segregation or indemnity-insurance requirement attached to the relevant FSP/ODP licence category.',
            'taxation_overview' => 'Not independently confirmed in the depth this page would need -- SARS generally taxes trading profits as ordinary income rather than capital gains when the activity is frequent/speculative enough to be viewed as a "scheme of profit-making," but the line is fact-specific. Consult a South African tax practitioner for your own position.',
            'local_payment_methods' => 'Not independently confirmed broker-by-broker for this page -- South African clients are typically offered EFT/bank transfer, debit/credit card, and e-wallets, but confirmed support (and any Rand-specific settlement arrangement) varies by broker.',
            'country_specific_restrictions' => 'The FSCA has repeatedly warned the public about unlicensed offshore platforms marketing forex/CFDs to South Africans without any FSCA authorisation at all -- verifying a broker\'s FAIS/FSP (and, if it trades as principal, ODP) licence on the FSCA\'s own register before funding an account is the single most load-bearing check for a South African trader, more so than for an EU resident covered by MiFID passporting.',
            'faqs' => array(
                array( 'q' => 'Is CFD/forex trading legal in South Africa?', 'a' => 'Yes, through a firm properly licensed by the FSCA (FAIS/FSP, plus an ODP licence if it trades as principal). The FSCA has repeatedly warned against unlicensed offshore platforms, so checking its public register before funding an account matters more here than in many other jurisdictions.' ),
                array( 'q' => 'Is there a compensation fund if my South African CFD broker fails?', 'a' => 'No FSCA-administered compensation fund for CFD/forex broker insolvency was confirmed for this page -- the FSCA licenses and supervises conduct but doesn\'t run a standing payout scheme comparable to the EU\'s ICF.' ),
            ),
            'regulatory_source_url' => 'https://www.fsca.co.za/',
            'paragraphs' => array(
                'South Africa regulates CFD/forex intermediation directly through the FSCA under the FAIS Act, with a separate OTC Derivative Provider licence required for any firm trading as principal against its clients -- there is no EU-style passporting involved at all.',
                'Because the FSCA has flagged unlicensed offshore platforms targeting South African traders on multiple occasions, confirming a broker\'s actual FSCA licence status is a more consequential check here than the EU-passporting nuance that dominates this site\'s EU country pages.',
            ),
        ),
        'united-arab-emirates' => array(
            'name' => 'United Arab Emirates', 'iso' => 'AE', 'currency' => 'AED', 'region_type' => 'other',
            'regulator' => 'DFSA (Dubai Financial Services Authority)', 'regulator_short' => 'DFSA',
            'regulator_match' => 'DFSA', 'hq_match' => 'UAE',
            'framework_overview' => 'The DFSA is the independent regulator of the Dubai International Financial Centre (DIFC), a financial free zone with its own common-law-based framework distinct from UAE federal law -- a DFSA licence covers firms operating within the DIFC specifically, not the UAE mainland. Mainland UAE CFD/forex activity instead falls under the federal Securities and Commodities Authority (SCA), a separate regulator with its own rulebook not covered by this page. DFSA-licensed firms must meet minimum capital, client-money segregation, and AML/CTF requirements, and at least one confirmed DIFC-based brand\'s DFSA entity does not offer CFDs or rolling spot FX to retail clients at all, serving Professional clients only -- confirm a specific broker\'s DIFC entity actually serves retail accounts before assuming a DFSA licence means retail access.',
            'leverage_overview' => 'The DFSA raised minimum margin requirements for retail clients effective 6 December 2021: 3.33% margin (30:1 leverage) on major forex pairs, 5% (20:1) on gold, and 10% (10:1) on oil. A 2025 industry report instead described a 50:1 retail cap on majors, which conflicts with the 2021 margin rule -- treat that higher figure as unconfirmed or possibly outdated, and check the current DFSA Rulebook directly. These limits don\'t apply to clients who qualify for and elect Professional classification.',
            'compensation_overview' => 'No DFSA-administered investor compensation scheme for retail CFD/forex client losses was confirmed in the sources checked for this page -- the DFSA\'s framework centres on licensing, capital, and client-money segregation requirements rather than a standing compensation fund. Not independently confirmed either way; check directly with the DFSA before assuming compensation-fund protection comparable to the EU\'s ICF or the UK\'s FSCS.',
            'taxation_overview' => 'The UAE levies no personal income tax, so individual trading gains aren\'t subject to UAE personal income tax -- a well-established, general feature of UAE tax law, not specific to forex/CFD trading. This says nothing about your home-country obligations: most jurisdictions tax residents on worldwide trading income regardless of where the broker or account is based, so confirm your own reporting duties with a tax adviser in your actual country of tax residence.',
            'local_payment_methods' => 'Not independently confirmed broker-by-broker for this page -- UAE clients are typically offered local bank transfer, card, and e-wallet options, but confirmed broker-level support wasn\'t verified here.',
            'country_specific_restrictions' => 'The DIFC/mainland split above is the single most important nuance for this jurisdiction: a DFSA licence only covers DIFC-based activity, and some DFSA entities choose not to serve retail clients with CFDs/rolling spot FX at all. Always confirm which specific entity (DIFC-DFSA, mainland-SCA, or an offshore entity with neither) actually holds your account.',
            'faqs' => array(
                array( 'q' => 'Is CFD/forex trading legal in the UAE?', 'a' => 'Yes, through a firm licensed either by the DFSA (within the Dubai International Financial Centre free zone) or the federal SCA (UAE mainland) -- these are two separate regulators with separate rulebooks, so confirm which one actually licenses your broker\'s entity.' ),
                array( 'q' => 'What leverage is available to retail clients in the UAE?', 'a' => 'DFSA-regulated retail accounts are capped at 30:1 on major FX pairs under margin rules effective December 2021 (20:1 on gold, 10:1 on oil); a conflicting 50:1 figure appears in at least one 2025 industry report and should be treated as unconfirmed. Professional-classified clients can access higher leverage.' ),
            ),
            'regulatory_source_url' => 'https://www.dfsa.ae/',
            'paragraphs' => array(
                'The UAE has two entirely separate regulatory regimes relevant to CFD/forex trading: the DFSA, covering only the Dubai International Financial Centre free zone, and the federal SCA, covering the UAE mainland -- a broker\'s "UAE licence" means one or the other, never both automatically.',
                'The DFSA tightened its retail margin requirements in December 2021, bringing DIFC-regulated leverage much closer to the EU\'s own ESMA caps than some older marketing claims about UAE leverage would suggest.',
            ),
        ),
        'singapore' => array(
            'name' => 'Singapore', 'iso' => 'SG', 'currency' => 'SGD', 'region_type' => 'other',
            'regulator' => 'MAS (Monetary Authority of Singapore)', 'regulator_short' => 'MAS',
            'regulator_match' => 'MAS', 'hq_match' => 'Singapore',
            'framework_overview' => 'A firm offering leveraged forex or CFDs to Singapore retail clients needs a Capital Markets Services (CMS) licence from MAS under the Securities and Futures Act, and retail clients must generally pass a Customer Knowledge Assessment (CKA) before trading leveraged products. Some brokers instead serve Singapore-resident clients through an offshore entity holding no MAS licence at all -- those accounts fall entirely outside MAS\'s retail protections, including its leverage cap below, so confirm which entity (MAS-licensed or offshore) actually holds your account.',
            'leverage_overview' => 'MAS cut the retail forex leverage cap from 50:1 to 20:1 in 2019 (roughly a 5% margin requirement); broker-comparison guides from 2025-2026 still describe 20:1 as the current retail ceiling, though this wasn\'t independently confirmed against the MAS rulebook itself for this page. A Singapore "accredited investor" (meeting the jurisdiction\'s wealth/experience criteria) can access higher leverage, reportedly up to 50:1, with correspondingly reduced regulatory protection. Offshore brokers serving Singapore residents outside MAS\'s licensing regime aren\'t bound by this cap at all and may offer far higher leverage -- a materially different risk profile from a MAS-licensed account.',
            'compensation_overview' => 'No MAS-administered investor compensation scheme specific to CFD/forex broker insolvency was confirmed in the sources checked for this page. MAS\'s framework for CMS licensees centres on licensing conditions, segregated client accounts, and the Customer Knowledge Assessment rather than a standing compensation fund; not independently confirmed either way -- check directly with MAS before assuming fund-backed protection.',
            'taxation_overview' => 'Not independently confirmed in the depth this page would need -- Singapore generally doesn\'t tax capital gains, but trading income classified by IRAS as a "trade or business" (rather than a one-off capital transaction) can instead be taxed as ordinary income; the classification is fact-specific. Consult a Singapore tax adviser for your own position.',
            'local_payment_methods' => 'Not independently confirmed broker-by-broker for this page -- Singapore clients are typically offered bank transfer (including PayNow where a broker supports it), card, and e-wallet options, but confirmed broker-level support wasn\'t verified here.',
            'country_specific_restrictions' => 'The mandatory Customer Knowledge Assessment for MAS-licensed CMS holders, and the existence of unlicensed-offshore alternatives entirely outside MAS\'s leverage cap and protections, are the two points most worth confirming directly with any specific broker before funding a Singapore account.',
            'faqs' => array(
                array( 'q' => 'Is leveraged forex/CFD trading legal in Singapore?', 'a' => 'Yes, through a firm holding a Capital Markets Services licence from MAS, with retail clients generally required to pass a Customer Knowledge Assessment first. Some brokers instead serve Singapore residents through an unlicensed offshore entity, which falls outside MAS\'s protections entirely.' ),
                array( 'q' => 'What leverage can retail traders get in Singapore?', 'a' => 'MAS-licensed accounts are generally capped at 20:1 for forex (cut from 50:1 in 2019), though this wasn\'t independently re-confirmed against the current MAS rulebook for this page. Accredited investors can reportedly access up to 50:1; offshore, non-MAS-licensed brokers aren\'t bound by the cap at all.' ),
            ),
            'regulatory_source_url' => 'https://www.mas.gov.sg/',
            'paragraphs' => array(
                'Singapore regulates leveraged forex/CFD trading directly through MAS under the Securities and Futures Act, with a mandatory knowledge test for retail clients rather than the EU\'s passporting mechanic.',
                'A meaningful share of brokers serving Singapore residents reportedly do so through offshore entities outside MAS\'s licence regime entirely -- worth confirming before assuming MAS\'s 20:1 retail leverage cap and protections apply to your specific account.',
            ),
        ),
    );
}

/**
 * All country pages this site has, EU and non-EU combined. Templates
 * that need the full index (e.g. the /countries/ directory page) use
 * this; globalfxhub_get_countries() alone remains available for any
 * EU-specific logic that must not see the non-EU entries.
 */
function globalfxhub_get_all_countries() {
    return array_merge( globalfxhub_get_countries(), globalfxhub_get_non_eu_countries() );
}

function globalfxhub_get_country_by_slug( $slug ) {
    $countries = globalfxhub_get_all_countries();
    return isset( $countries[ $slug ] ) ? array_merge( array( 'slug' => $slug ), $countries[ $slug ] ) : null;
}

/**
 * The EU framework, leverage caps, and investor-compensation mechanic
 * are genuinely uniform EU/MiFID-II law across all 27 member states --
 * computed here once rather than repeated as near-identical boilerplate
 * in 27 data entries. Precision here is itself differentiating content:
 * the compensation mechanic specifically (which scheme actually applies
 * to a passported broker) is a point most broker-comparison sites get
 * vague about. Each function still takes $country so it can call out a
 * genuine, real per-country wrinkle (non-eurozone currency conversion,
 * a locally-headquartered entity) rather than being pure copy-paste.
 */
function globalfxhub_country_eu_framework_html( $country ) {
    $html = '<p>Any broker serving ' . esc_html( $country['name'] ) . ' retail clients operates under MiFID II, the EU-wide framework covering investment firms, plus the retail-facing product intervention rules ESMA introduced in 2018 (leverage caps, standardized risk warnings, negative balance protection, and a ban on bonus incentives). A CySEC (or other EU member-state) licence is the precondition for legally offering these services anywhere in the EU/EEA, but the firm must also complete a MiFID passporting notification for ' . esc_html( $country['name'] ) . ' specifically -- it is not automatic just because the firm is licensed somewhere in the EU.</p>';
    if ( ! empty( $country['additional_eu_note'] ) ) {
        $html .= '<p>' . esc_html( $country['additional_eu_note'] ) . '</p>';
    }
    return $html;
}

function globalfxhub_country_leverage_rules_html( $country ) {
    return '<p>Retail leverage is capped under the same ESMA-derived limits across every EU member state, ' . esc_html( $country['name'] ) . ' included: 30:1 on major currency pairs, 20:1 on non-major pairs/gold/major indices, 10:1 on other commodities and non-major indices, 5:1 on individual equities, and 2:1 on crypto-asset CFDs. Clients who qualify as "professional" under MiFID II\'s elective-professional criteria (meeting at least two of: a sufficiently large portfolio, relevant trading frequency/experience, or relevant industry employment) can apply for higher leverage, but lose the retail-specific protections -- including, notably, negative balance protection -- that come with those caps.</p>';
}

function globalfxhub_country_investor_compensation_html( $country ) {
    $html = '<p>This is the detail most broker-comparison content glosses over: investor compensation for a <em>passported</em> broker (the large majority of brokers on this site, relative to ' . esc_html( $country['name'] ) . ') comes from the broker\'s <strong>home</strong>-country scheme, not a ' . esc_html( $country['name'] ) . '-specific one. For a CySEC-licensed entity, that means Cyprus\'s Investor Compensation Fund (ICF), covering eligible retail clients up to &euro;20,000 per person if the firm fails -- the same cover regardless of which EU country the client lives in. A UK FCA entity instead carries the UK\'s FSCS cover, up to &pound;85,000 per person, via a separate licence that doesn\'t passport from the EU side at all post-Brexit.</p>';
    if ( ! empty( $country['local_entity_note'] ) ) {
        $html .= '<p>' . esc_html( $country['local_entity_note'] ) . '</p>';
    } else {
        $html .= '<p>No broker in our researched set is confirmed to hold a ' . esc_html( $country['name'] ) . '-licensed entity specifically (as opposed to a passported CySEC or other EU entity), so no ' . esc_html( $country['name'] ) . '-specific compensation scheme applies to any broker reviewed here -- it\'s the home regulator\'s scheme in every case. See our <a href="' . home_url( '/regulation/investor-compensation-schemes-explained/' ) . '">investor compensation schemes</a> page for the general mechanics.</p>';
    }
    return $html;
}

/**
 * Converts an ISO 3166-1 alpha-2 code (e.g. "DE") to its Unicode flag
 * emoji by combining two "regional indicator symbol" code points -- the
 * standard technique, no image assets needed. Returns '' for anything
 * that isn't exactly 2 letters, so a bad/missing code just omits the flag
 * rather than rendering mojibake.
 */
function globalfxhub_country_flag_emoji( $iso ) {
    $iso = strtoupper( trim( (string) $iso ) );
    if ( 1 !== preg_match( '/^[A-Z]{2}$/', $iso ) ) {
        return '';
    }
    $flag = '';
    for ( $i = 0; $i < 2; $i++ ) {
        $flag .= mb_chr( 0x1F1E6 + ( ord( $iso[ $i ] ) - 65 ), 'UTF-8' );
    }
    return $flag;
}

/**
 * Ranks brokers for a given country: any broker actually HEADQUARTERED
 * there is surfaced first (sorted by their own overall rank), then the
 * list is filled up to $limit with the next-best brokers from the
 * site-wide ranking. Each returned broker carries an 'is_local_hq' flag
 * so the template can label it distinctly.
 *
 * Matching only looks at the primary clause of the 'hq' field (the part
 * before the first "(" or ";"), not the whole string -- several brokers'
 * hq text reads like "Melbourne, Australia (group); ... Cyprus entity
 * based in Limassol" or "Limassol, Cyprus (with a branch office in
 * Hamburg, Germany)", where the country named later is a *branch* or a
 * *regulated entity*, not the actual headquarters. Matching the full
 * string would wrongly label a dozen-plus Australia/UK/Israel/US-based
 * brokers as "headquartered in Cyprus" just because their CySEC entity
 * is mentioned there.
 *
 * When no broker is headquartered in the country (the common case), this
 * simply returns the global top $limit -- an honest reflection of having
 * no country-specific signal, not a placeholder to be replaced later.
 *
 * Excludes any broker with no CySEC/EU regulation at all (cysec === null
 * -- a Seychelles-FSA-only broker, for example): this whole section's
 * premise, stated plainly on every country page, is "every broker here
 * is CySEC-licensed and legally entitled to serve you under EU
 * passporting." Once the site's roster includes brokers with no EU
 * presence, leaving them in this ranking would make that claim false for
 * whichever one turned up.
 */
function globalfxhub_country_broker_ranking( $country, $limit = 10 ) {
    $all = array_filter( globalfxhub_get_brokers(), function( $b ) {
        return null !== $b['cysec'];
    } );
    usort( $all, function( $a, $b ) {
        return $a['rank'] <=> $b['rank'];
    } );

    $local = array();
    $rest  = array();
    foreach ( $all as $broker ) {
        $primary_hq = ! empty( $broker['hq'] ) ? preg_split( '/[(;]/', $broker['hq'], 2 )[0] : '';
        $broker['is_local_hq'] = '' !== $primary_hq && false !== stripos( $primary_hq, $country['hq_match'] );
        if ( $broker['is_local_hq'] ) {
            $local[] = $broker;
        } else {
            $rest[] = $broker;
        }
    }

    return array_slice( array_merge( $local, $rest ), 0, $limit );
}

/**
 * The non-EU equivalent of globalfxhub_country_broker_ranking() above --
 * used for the four non-EU country pages (globalfxhub_get_non_eu_
 * countries()). Rather than the EU pages' "every CySEC broker can
 * passport in" inference, this only includes a broker if its own
 * 'other_reg' field confirms a licence from that exact country's
 * regulator (e.g. 'ASIC' for Australia) -- a stronger, directly
 * evidenced claim than passporting, at the cost of a shorter list for
 * jurisdictions where fewer of our reviewed brokers hold that specific
 * licence (Singapore's MAS, for example, currently has only 3). A short
 * but accurate list is preferred here to padding it out with brokers
 * that aren't actually licensed in that jurisdiction.
 */
function globalfxhub_country_broker_ranking_other_reg( $country, $limit = 10 ) {
    $all = array_filter( globalfxhub_get_brokers(), function( $b ) use ( $country ) {
        return ! empty( $b['other_reg'] ) && in_array( $country['regulator_match'], $b['other_reg'], true );
    } );
    usort( $all, function( $a, $b ) {
        return $a['rank'] <=> $b['rank'];
    } );

    $local = array();
    $rest  = array();
    foreach ( $all as $broker ) {
        $primary_hq = ! empty( $broker['hq'] ) ? preg_split( '/[(;]/', $broker['hq'], 2 )[0] : '';
        $broker['is_local_hq'] = '' !== $primary_hq && false !== stripos( $primary_hq, $country['hq_match'] );
        if ( $broker['is_local_hq'] ) {
            $local[] = $broker;
        } else {
            $rest[] = $broker;
        }
    }

    return array_slice( array_merge( $local, $rest ), 0, $limit );
}

/**
 * Short meta line for a country's card on the /countries/ index grid:
 * "EU since 1995" for an EU member state, or "ASIC-regulated" etc. for
 * one of the non-EU pages -- avoids the index template needing to know
 * which fields exist on which kind of country entry.
 */
function globalfxhub_country_index_badge( $country ) {
    if ( ! empty( $country['eu_since'] ) ) {
        return 'EU since ' . $country['eu_since'];
    }
    return ! empty( $country['regulator_short'] ) ? $country['regulator_short'] . '-regulated' : 'Non-EU';
}

/**
 * /countries/{slug}/ needs a "Countries" page on the "Countries Index"
 * template; self-creates/self-repairs on every load like the other
 * templated pages.
 */
function globalfxhub_ensure_countries_page() {
    globalfxhub_ensure_templated_page( 'countries', 'Countries', 'page-countries.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_countries_page' );

/**
 * Pretty URLs for individual country pages: /countries/{slug}/ -- same
 * rewrite-rule + query-var pattern as /reviews/{slug}/.
 */
function globalfxhub_country_rewrite_rules() {
    add_rewrite_rule( '^countries/([^/]+)/?$', 'index.php?pagename=countries&country=$matches[1]', 'top' );
}
add_action( 'init', 'globalfxhub_country_rewrite_rules' );

function globalfxhub_country_query_vars( $vars ) {
    $vars[] = 'country';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_country_query_vars' );

/**
 * SEO for /countries/{slug}/ pages: distinct title, canonical, and meta
 * description per country, mirroring how /reviews/{slug}/ is handled --
 * otherwise every country URL would share the "Countries" page's single
 * default title and description.
 */
function globalfxhub_country_seo_title( $title_parts ) {
    $slug = get_query_var( 'country' );
    if ( $slug ) {
        $country = globalfxhub_get_country_by_slug( $slug );
        if ( $country ) {
            $title_parts['title'] = 'Best Forex & CFD Brokers in ' . $country['name'] . ' ' . date( 'Y' );
        }
    } elseif ( is_page( 'countries' ) ) {
        $title_parts['title'] = 'Best Forex & CFD Brokers by Country ' . date( 'Y' );
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_country_seo_title' );

function globalfxhub_country_canonical( $canonical_url ) {
    $slug = get_query_var( 'country' );
    if ( $slug ) {
        $country = globalfxhub_get_country_by_slug( $slug );
        if ( $country ) {
            return home_url( '/countries/' . $slug . '/' );
        }
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_country_canonical' );

function globalfxhub_country_seo_head() {
    $slug = get_query_var( 'country' );
    if ( ! $slug ) {
        if ( is_page( 'countries' ) ) {
            echo '<meta name="description" content="' . esc_attr( 'Top-ranked, confirmed-licensed forex and CFD brokers for every EU member state plus Australia, South Africa, the UAE, and Singapore, with what\'s actually different -- regulator, currency, and local broker presence -- about trading in each one.' ) . '">' . "\n";
        }
        return;
    }
    $country = globalfxhub_get_country_by_slug( $slug );
    if ( ! $country ) {
        return;
    }
    $is_eu_country = empty( $country['region_type'] ) || 'other' !== $country['region_type'];
    $description   = $is_eu_country
        ? sprintf(
            'The top CySEC-regulated forex and CFD brokers available to retail traders in %s, ranked by our disclosed methodology -- plus what\'s specific to %s: currency, regulator, and EU rules.',
            $country['name'],
            $country['name']
        )
        : sprintf(
            'The top %s-regulated forex and CFD brokers available to retail traders in %s, ranked by our disclosed methodology -- plus what\'s specific to %s: regulator, leverage rules, and investor protection.',
            $country['regulator_short'],
            $country['name'],
            $country['name']
        );
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'globalfxhub_country_seo_head', 5 );
