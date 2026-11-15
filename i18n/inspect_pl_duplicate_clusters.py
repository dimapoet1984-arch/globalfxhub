#!/usr/bin/env python3
"""
Read-only: for each of the 56 Polish duplicate-title clusters found by
i18n/classify_pl_orphans.py (563 pl orphans -> 179 of them in tight
same-title clusters), fetches every member's OWN gfh_translations
group so a defensible "which copy is the real one" decision can be
made before trashing anything.

Why this matters: a post's gfh_translations getter returns the
translation GROUP that post itself is currently a member of. If one
copy in a cluster already belongs to a populated group (ideally with
an 'en' entry pointing at a live English post, and other language
members matching), it may be the genuinely intended translation whose
link-back to the English source simply never completed -- the same
failure mode i18n/link_orphaned_translation.py fixed before (a
correct translation, published, but the REST write linking it back
hit the lock-timeout bug). Trashing that one and keeping an unlinked
copy would be the wrong choice. If every copy's own group is empty or
self-only, there's no such signal and the earliest-published copy is
the reasonable one to keep.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/inspect_pl_duplicate_clusters.py
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# (title, [ids]) from i18n/classify_pl_orphans.py's "likely genuine
# duplicate groups" output.
CLUSTERS = [
    ("Jak czytać formację trzech białych żołnierzy", [5552, 5862, 5882, 5952, 6081, 6129, 6201]),
    ("Jak sprawdzić, czy broker forex jest regulowany", [4969, 5516, 5760, 5916, 6043, 6163]),
    ("Opłaty brokerów forex wyjaśnione", [5008, 5474, 5600, 5848, 5874, 6249]),
    ("Wyjaśnienie regulacji forex przez FCA", [5518, 5644, 5762, 5918, 6045, 6167]),
    ("Wyjaśnienie regulacji forex przez FSCA", [5524, 5650, 5768, 6051, 6173, 6245]),
    ("Jak czytać świecę Belt Hold", [5544, 5670, 5788, 5944, 6071, 6193]),
    ("Jak czytać formację Three Inside Up", [5554, 5680, 5798, 5954, 6083, 6203]),
    ("Jak czytać formację Three Outside Down", [5556, 5682, 5800, 5956, 6085, 6205]),
    ("Jak czytać formację Tri-Star", [5558, 5684, 5802, 5958, 6087, 6207]),
    ("Dlaczego akcje nie spadają?", [4913, 5508, 5634, 5752, 5908]),
    ("Jak czytać formację Dark Cloud Cover", [5546, 5672, 5790, 5946, 6073]),
    ("Jak czytać formację trzech identycznych kruków", [5560, 5804, 5960, 6089, 6209]),
    ("Jakie są dziś najważniejsze wydarzenia?", [4745, 4766, 5488, 5614]),
    ("Opłaty za przewalutowanie u brokerów forex", [5020, 5528, 5772, 6177]),
    ("Jak czytać odwrócony młot", [5538, 5664, 6065, 6187]),
    ("Jak czytać formację trzech czarnych kruków", [5606, 5880, 6006, 6127]),
    ("Jak czytać formację młota", [5780, 5936, 6063, 6185]),
    ("Jakie są główne wydarzenia na dziś?", [4786, 4810, 4888]),
    ("Wyjaśnienie stosunku ryzyka do zysku w forex", [5514, 5640, 6041]),
    ("Jak czytać formację Mat Hold", [5562, 5688, 6091]),
    ("Raport o zatrudnieniu w USA może określić, jak wysoko mogą wzrosnąć rentowności obligacji skarbowych", [5564, 5964, 6093]),
    ("Czym jest rozkład prognoz dla amerykańskiego raportu o zatrudnieniu poza rolnictwem (NFP)?", [5576, 5976, 6105]),
    ("Jak czytać formację linii przenikania", [5604, 5878, 6004]),
    ("Perspektywy rynkowe na tydzień od 28 września do 2 października", [5612, 6012, 6133]),
    ("Wyjaśnienie regulacji forex DFSA", [5648, 5922, 6171]),
    ("Jak czytać świecę Marubozu", [5668, 6069, 6191]),
    ("Analiza złota (XAU/USD): wzrosty ograniczone impasem między USA a Iranem i ryzykiem silnego raportu NFP", [5698, 5972, 6101]),
    ("Jakie są główne wydarzenia dnia?", [5888, 6014, 6135]),
    ("RBA podnosi stopę gotówkową o 25 pb do 4,60%, najwyżej od 2011 roku", [4760, 5124]),
    ("Dlaczego komentarze i wystąpienia członków banków centralnych mają znaczenie dla rynków", [4764, 5128]),
    ("Inflacja we Francji gwałtownie rośnie we wrześniu, HICP wzrasta do 3,4%", [4774, 5138]),
    ("Regulacja forex FSCA wyjaśniona", [5470, 5870]),
    ("cTrader wyjaśniony: kompletny przewodnik", [5476, 5876]),
    ("Rentowności spadają. Kontrakty terminowe na akcje wskazują na wyższe otwarcie. Dolar traci po raporcie o zatrudnieniu", [5498, 6024]),
    ("Wyjaśnienie regulacji ASIC dla forex", [5520, 5920]),
    ("cTrader kontra MetaTrader 5", [5532, 5932]),
    ("Jak czytać świecę marubozu", [5542, 5942]),
    ("Jak czytać formację harami niedźwiedzia", [5548, 5948]),
    ("Wiadomości z rynku Azji i Pacyfiku od investingLive: jen słabnie, Nikkei rośnie", [5570, 5696]),
    ("Kryzys na rynku oleju napędowego przeradza się w spór o rezerwy strategiczne, podczas gdy USA wywierają presję na Europę", [5578, 5822]),
    ("Rentowności spadają. Kontrakty terminowe na akcje wskazują na wyższe otwarcie. Dolar amerykański traci po raporcie o zatrudnieniu", [5584, 6145]),
    ("Rentowności spadają. Kontrakty terminowe na akcje wskazują na wzrost przy otwarciu. Dolar traci po raporcie o zatrudnieniu", [5624, 5898]),
    ("Bank centralny Indii podnosi stopy procentowe po raz pierwszy od 2023 roku w obliczu rosnącego ryzyka inflacyjnego", [5638, 6039]),
    ("Regulacja forex przez FSA Seszeli: wyjaśnienie", [5652, 6053]),
    ("cTrader a MetaTrader 5", [5658, 6059]),
    ("Jak czytać gwiazdę spadającą", [5666, 6067]),
    ("Jak czytać formację Piercing Line", [5722, 6125]),
    ("Rentowności spadają. Kontrakty terminowe na akcje wskazują na wyższe otwarcie. Dolar USA traci po raporcie o rynku pracy", [5742, 5828]),
    ("Regulacja forex przez Seychelles FSA: wyjaśnienie", [5770, 6175]),
    ("Opłaty za wpłaty i wypłaty w handlu forex – wyjaśnienie", [5774, 6179]),
    ("cTrader vs. MetaTrader 5", [5776, 6181]),
    ("Jak czytać odwróconego młota", [5782, 5938]),
    ("Wiadomości z rynków Azji i Pacyfiku od investingLive: jen słabnie, Nikkei rośnie", [5814, 5970]),
    ("Jak czytać formację Tweezer Top (szczyt pincetowy)", [5950, 6079]),
    ("investingLive Rynki Azji i Pacyfiku: złoto mocno traci i ponownie spada poniżej 4 200 dolarów", [6010, 6131]),
    ("Dlaczego akcje nie tracą na wartości?", [6035, 6155]),
]


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    total_ids = sum(len(ids) for _, ids in CLUSTERS)
    print(f"Inspecting {total_ids} posts across {len(CLUSTERS)} duplicate clusters\n")

    recommend_keep = {}
    recommend_trash = []

    for title, ids in CLUSTERS:
        members = []
        for post_id in ids:
            resp = request_with_retry(
                "GET", f"{WP_BASE}/posts/{post_id}",
                params={"_fields": "id,slug,date,categories,gfh_translations"},
                auth=auth, timeout=30,
            )
            resp.raise_for_status()
            members.append(resp.json())

        print(f"--- {title!r} ({len(members)} copies) ---")
        best = None
        for m in members:
            trans = m.get("gfh_translations") or {}
            group_size = len(trans)
            has_en = "en" in trans
            print(
                f"  id={m['id']} date={m['date']} slug={m['slug']} "
                f"categories={m.get('categories')} translations={trans}"
            )
            score = (1 if has_en else 0, group_size, -m["id"])  # prefer has-en, then bigger group, then earliest id
            if best is None or score > best[0]:
                best = (score, m)

        keep_id = best[1]["id"]
        trash_ids = [m["id"] for m in members if m["id"] != keep_id]
        reason = "has an 'en' link" if "en" in (best[1].get("gfh_translations") or {}) else "earliest/most populated group" if (best[1].get("gfh_translations") or {}) else "earliest copy (no group on any copy)"
        print(f"  -> keep {keep_id} ({reason}), trash {trash_ids}\n")

        recommend_keep[title] = keep_id
        recommend_trash.extend(trash_ids)

    print(f"=== Summary: keep {len(recommend_keep)}, trash {len(recommend_trash)} ===")
    print(f"TRASH_IDS = {sorted(recommend_trash)}")


if __name__ == "__main__":
    main()
