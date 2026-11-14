"""
Hand-written domain glossary for the Track A/B translation pipeline,
per docs/TRANSLATION_PLAYBOOK.md section 4 ("Glossary written by
hand... used in every prompt and exported to the theme. Same names
everywhere.") and docs/TRANSLATION_PIPELINE_ARCHITECTURE.md section 4.

Scope: the 7 target languages -- es (Spanish), de (German), ar
(Arabic), fr (French), pl (Polish), it (Italian), pt (Portuguese).

IMPORTANT -- this is a best-effort glossary of standard financial
terminology, not a native-speaker-reviewed one. Per the playbook's own
approval gate (section 0.3: a 5-7 page sample reviewed by the owner
before the full run), get native-speaker sign-off on this glossary
specifically before the full run -- it's reused in every single
translation request, so an error here repeats everywhere rather than
being a one-off mistake.

PT_VARIANT note: "pt" is ambiguous between European and Brazilian
Portuguese (different enough in register and some vocabulary that a
native speaker should pick one explicitly before launch). The entries
below default to a broadly neutral/European-leaning form; set
PT_VARIANT = "pt-BR" and have a native speaker adjust the handful of
terms that actually differ if the target audience is Brazil-heavy.
"""

PT_VARIANT = "pt-PT"

LANGUAGES = ["es", "de", "ar", "fr", "pl", "it", "pt"]

# Terms the model must NEVER translate -- brand names, regulator
# acronyms, platform names, currency pairs. Passed to every prompt
# as an explicit "leave these exactly as-is" list, since a cheap
# model under instruction to "translate everything" will otherwise
# occasionally translate a proper noun it doesn't recognize as one.
DO_NOT_TRANSLATE = [
    # Platforms
    "MetaTrader 4", "MetaTrader 5", "MT4", "MT5", "cTrader", "TradingView",
    "DupliTrade", "ZuluTrade",
    # Regulators (acronym form always kept; full name may be translated
    # the first time it's spelled out, acronym never is)
    "CySEC", "FCA", "ASIC", "FSCA", "FSA", "BaFin", "CFTC", "NFA", "CNMV",
    "CONSOB", "AMF", "KNF", "DFSA", "FINMA", "FSC", "SCB",
    # Execution-model terms kept as industry-standard acronyms
    "ECN", "STP", "DMA", "NDD",
    # Currency pairs / instruments -- never translated
    "EUR/USD", "GBP/USD", "USD/JPY", "CFD", "CFDs",
]

# term -> {lang: translation}. English is the key itself (the source
# value passed through globalfxhub_t()/globalfxhub_tf()), so there's
# no separate "en" column.
GLOSSARY = {
    "broker": {
        "es": "bróker", "de": "Broker", "ar": "وسيط", "fr": "courtier",
        "pl": "broker", "it": "broker", "pt": "corretora" if PT_VARIANT == "pt-BR" else "corretora",
    },
    "regulated broker": {
        "es": "bróker regulado", "de": "regulierter Broker", "ar": "وسيط منظم",
        "fr": "courtier réglementé", "pl": "broker regulowany",
        "it": "broker regolamentato", "pt": "corretora regulamentada",
    },
    "regulation": {
        "es": "regulación", "de": "Regulierung", "ar": "التنظيم",
        "fr": "réglementation", "pl": "regulacja", "it": "regolamentazione",
        "pt": "regulação",
    },
    "licence": {
        "es": "licencia", "de": "Lizenz", "ar": "الترخيص", "fr": "licence",
        "pl": "licencja", "it": "licenza", "pt": "licença",
    },
    "spread": {
        "es": "spread", "de": "Spread", "ar": "الفرق السعري (سبريد)",
        "fr": "spread", "pl": "spread", "it": "spread", "pt": "spread",
    },
    "leverage": {
        "es": "apalancamiento", "de": "Hebel", "ar": "الرافعة المالية",
        "fr": "effet de levier", "pl": "lewarowanie (lewar)",
        "it": "leva finanziaria", "pt": "alavancagem",
    },
    "forex": {
        "es": "forex / divisas", "de": "Forex / Devisenhandel", "ar": "الفوركس",
        "fr": "forex / change", "pl": "forex / rynek walutowy",
        "it": "forex / valute", "pt": "forex / câmbio",
    },
    "trading account": {
        "es": "cuenta de trading", "de": "Handelskonto", "ar": "حساب تداول",
        "fr": "compte de trading", "pl": "konto handlowe",
        "it": "conto di trading", "pt": "conta de negociação",
    },
    "demo account": {
        "es": "cuenta demo", "de": "Demokonto", "ar": "حساب تجريبي",
        "fr": "compte démo", "pl": "konto demo", "it": "conto demo",
        "pt": "conta demo",
    },
    "minimum deposit": {
        "es": "depósito mínimo", "de": "Mindesteinzahlung", "ar": "الحد الأدنى للإيداع",
        "fr": "dépôt minimum", "pl": "minimalny depozyt",
        "it": "deposito minimo", "pt": "depósito mínimo",
    },
    "withdrawal": {
        "es": "retirada de fondos", "de": "Auszahlung", "ar": "السحب",
        "fr": "retrait", "pl": "wypłata", "it": "prelievo",
        "pt": "retirada" if PT_VARIANT == "pt-BR" else "levantamento",
    },
    "inactivity fee": {
        "es": "comisión por inactividad", "de": "Inaktivitätsgebühr",
        "ar": "رسوم عدم النشاط", "fr": "frais d'inactivité",
        "pl": "opłata za nieaktywność", "it": "commissione di inattività",
        "pt": "taxa de inatividade",
    },
    "currency conversion fee": {
        "es": "comisión por conversión de divisas", "de": "Währungsumrechnungsgebühr",
        "ar": "رسوم تحويل العملات", "fr": "frais de conversion de devises",
        "pl": "opłata za przewalutowanie", "it": "commissione di conversione valutaria",
        "pt": "taxa de conversão cambial",
    },
    "trading platform": {
        "es": "plataforma de trading", "de": "Handelsplattform", "ar": "منصة التداول",
        "fr": "plateforme de trading", "pl": "platforma handlowa",
        "it": "piattaforma di trading", "pt": "plataforma de negociação",
    },
    "execution": {
        "es": "ejecución de órdenes", "de": "Orderausführung", "ar": "تنفيذ الصفقات",
        "fr": "exécution des ordres", "pl": "realizacja zleceń",
        "it": "esecuzione degli ordini", "pt": "execução de ordens",
    },
    "slippage": {
        "es": "deslizamiento (slippage)", "de": "Slippage (Kursabweichung)",
        "ar": "الانزلاق السعري", "fr": "glissement (slippage)",
        "pl": "poślizg cenowy (slippage)", "it": "slittamento (slippage)",
        "pt": "deslizamento (slippage)",
    },
    "track record": {
        "es": "trayectoria", "de": "Erfolgsbilanz / Historie", "ar": "السجل التاريخي",
        "fr": "historique / antécédents", "pl": "historia działalności",
        "it": "track record / storico", "pt": "histórico de atuação",
    },
    "trading conditions": {
        "es": "condiciones de trading", "de": "Handelsbedingungen",
        "ar": "شروط التداول", "fr": "conditions de trading",
        "pl": "warunki handlowe", "it": "condizioni di trading",
        "pt": "condições de negociação",
    },
    "score": {
        "es": "puntuación", "de": "Bewertung", "ar": "التقييم", "fr": "note / score",
        "pl": "ocena / wynik", "it": "punteggio", "pt": "pontuação",
    },
    "review": {
        "es": "análisis / reseña", "de": "Testbericht / Review", "ar": "مراجعة",
        "fr": "analyse / avis", "pl": "recenzja / analiza", "it": "recensione",
        "pt": "análise / avaliação",
    },
    "compare brokers": {
        "es": "comparar brókers", "de": "Broker vergleichen", "ar": "مقارنة الوسطاء",
        "fr": "comparer les courtiers", "pl": "porównaj brokerów",
        "it": "confronta i broker", "pt": "comparar corretoras",
    },
    "best brokers": {
        "es": "mejores brókers", "de": "beste Broker", "ar": "أفضل الوسطاء",
        "fr": "meilleurs courtiers", "pl": "najlepsi brokerzy",
        "it": "migliori broker", "pt": "melhores corretoras",
    },
    "scam": {
        "es": "estafa / fraude", "de": "Betrug / Abzocke", "ar": "عملية نصب / احتيال",
        "fr": "arnaque / fraude", "pl": "oszustwo", "it": "truffa",
        "pt": "fraude / esquema fraudulento",
    },
    "client protection": {
        "es": "protección al cliente", "de": "Kundenschutz", "ar": "حماية العميل",
        "fr": "protection du client", "pl": "ochrona klienta",
        "it": "protezione del cliente", "pt": "proteção do cliente",
    },
    "non-trading fees": {
        "es": "comisiones no asociadas al trading", "de": "handelsunabhängige Gebühren",
        "ar": "رسوم غير متعلقة بالتداول", "fr": "frais hors trading",
        "pl": "opłaty niezwiązane z handlem", "it": "commissioni non di trading",
        "pt": "taxas não relacionadas à negociação",
    },
    "product range": {
        "es": "gama de productos", "de": "Produktangebot", "ar": "نطاق المنتجات",
        "fr": "gamme de produits", "pl": "zakres produktów", "it": "gamma di prodotti",
        "pt": "gama de produtos",
    },
    "transparency": {
        "es": "transparencia", "de": "Transparenz", "ar": "الشفافية",
        "fr": "transparence", "pl": "przejrzystość", "it": "trasparenza",
        "pt": "transparência",
    },
}

# Per-language register/style notes, given verbatim to the model in
# the system prompt (playbook section 3.2: "native-speaker register,
# form of address per language"). Finance/trading content on a
# review-and-comparison site reads as professional but direct, not
# stuffy -- addressing an individual trader, not a corporate client.
STYLE_NOTES = {
    "es": "Neutral, international Spanish (not Spain-specific slang, not Mexico-specific slang) -- suitable for a pan-Latin-American and Spanish readership. Use 'tú' is too informal for financial content; use the neutral/formal register without 'usted' feeling stiff -- the tone of a well-written financial news site, not a legal document.",
    "de": "Formal 'Sie' register throughout, standard for German financial/business content. Compound nouns follow standard German orthography (no artificial hyphenation to ease translation).",
    "ar": "Modern Standard Arabic (MSA), right-to-left, formal register appropriate for financial journalism -- not a regional dialect. Numerals: keep Western Arabic numerals (0-9) as used in financial contexts across the region, not Eastern Arabic-Indic digits.",
    "fr": "Formal 'vous' register, standard international French (not Québécois or regional). Standard French financial-press tone.",
    "pl": "Formal register appropriate for Polish financial journalism. Avoid overly literal calques from English finance terms where a standard Polish financial-press term already exists.",
    "it": "Formal 'Lei' register is overly stiff for a financial comparison site read by individual traders -- use the neutral, direct register of Italian financial journalism (impersonal constructions over heavy 'Lei' conjugation).",
    "pt": f"{PT_VARIANT} register -- " + (
        "Brazilian Portuguese, using Brazilian financial-press vocabulary and the 'você' address form."
        if PT_VARIANT == "pt-BR" else
        "European Portuguese, using Portugal financial-press vocabulary and formal address."
    ),
}


def glossary_block_for_prompt(lang):
    """Renders the glossary + do-not-translate list as plain text for
    the system prompt. Only entries that have a translation for this
    language are included -- keeps the prompt shorter for languages
    we haven't filled in every term for."""
    lines = [f"Glossary (English term -> required {lang} translation):"]
    for term, translations in GLOSSARY.items():
        if lang in translations:
            lines.append(f"- {term} -> {translations[lang]}")
    lines.append("")
    lines.append("Never translate these -- copy exactly as written: " + ", ".join(DO_NOT_TRANSLATE))
    lines.append("")
    lines.append(f"Style: {STYLE_NOTES.get(lang, 'Formal, professional financial-journalism register.')}")
    return "\n".join(lines)
