#!/usr/bin/env python3
"""
Read-only: for each of the 22 French duplicate-title clusters found by
i18n/classify_fr_orphans.py (210 fr orphans -> 51 of them in tight
same-title clusters), fetches every member's OWN gfh_translations
group so a defensible "which copy is the real one" decision can be
made before trashing anything -- same approach as
i18n/inspect_pl_duplicate_clusters.py / i18n/inspect_ar_duplicate_clusters.py.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/inspect_fr_duplicate_clusters.py
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# (title, [ids]) from i18n/classify_fr_orphans.py's "likely genuine
# duplicate groups" output.
CLUSTERS = [
    ("15 signaux d'alerte à vérifier avant de déposer des fonds chez un courtier forex", [3960, 3962, 4352]),
    ("Quels sont les principaux événements du jour ?", [3982, 4108, 4229]),
    ("Comment vérifier si un courtier forex est réglementé", [4010, 4136, 4257]),
    ("Comment lire une bougie Belt Hold", [4038, 4164, 4286]),
    ("Comment lire les trois soldats blancs", [4046, 4172, 4294]),
    ("Comment lire le motif Three Outside Down", [4050, 4176, 4298]),
    ("Comment lire le motif des trois corbeaux identiques", [4054, 4180, 4302]),
    ("Les frais des courtiers forex expliqués", [3968, 4346]),
    ("cTrader expliqué : le guide complet", [3970, 4096]),
    ("Comment Nvidia, Micron et un rapport sur l'emploi surprenant ont fait bouger les actions la semaine dernière", [3996, 4243]),
    ("Explication de la réglementation du forex par la FCA", [4012, 4138]),
    ("Explication de la réglementation ASIC pour le forex", [4014, 4140]),
    ("Explication de la réglementation forex de la DFSA", [4016, 4264]),
    ("Comment lire le marteau inversé", [4032, 4280]),
    ("La RBA relève son taux directeur de 25 points de base à 4,60 %, son plus haut niveau depuis 2011", [4062, 4310]),
    ("Perspectives du marché pour la semaine du 28 septembre au 2 octobre", [4106, 4227]),
    ("Pourquoi les actions ne chutent-elles pas ?", [4128, 4249]),
    ("Frais de conversion de devises chez les courtiers forex", [4148, 4270]),
    ("Comment lire une bougie Marubozu", [4162, 4284]),
    ("Comment lire le motif Three Inside Up", [4174, 4296]),
    ("Comment lire la figure Tri-Star", [4178, 4300]),
    ("Explication de la réglementation forex par la FSCA", [4266, 4342]),
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
        for m in members:
            trans = m.get("gfh_translations") or {}
            self_consistent = trans.get("fr") == m["id"]
            print(
                f"  id={m['id']} date={m['date']} slug={m['slug']} "
                f"categories={m.get('categories')} self_consistent={self_consistent} translations={trans}"
            )
        print()


if __name__ == "__main__":
    main()
