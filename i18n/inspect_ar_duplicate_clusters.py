#!/usr/bin/env python3
"""
Read-only: for each of the 29 Arabic duplicate-title clusters found by
i18n/classify_ar_orphans.py (393 ar orphans -> 64 of them in tight
same-title clusters), fetches every member's OWN gfh_translations
group so a defensible "which copy is the real one" decision can be
made before trashing anything -- same approach as
i18n/inspect_pl_duplicate_clusters.py, which found that a post
belonging to a populated group (specifically: the group's own 'ar'
slot naming that exact post id) is the live, currently-linked member,
not necessarily the earliest-published copy.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/inspect_ar_duplicate_clusters.py
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# (title, [ids]) from i18n/classify_ar_orphans.py's "likely genuine
# duplicate groups" output.
CLUSTERS = [
    ("ما هي الأحداث الرئيسية لليوم؟", [1999, 2009, 2029, 2073, 2151, 2169, 2433, 2585]),
    ("فهم الرافعة المالية في تداول الفوركس", [1895, 2331]),
    ("كيف تقرأ دوجي اليعسوب", [1901, 2337]),
    ("كيفية قراءة شمعة المطرقة", [1905, 2341]),
    ("كيفية قراءة شمعة ماروبوزو", [1915, 2351]),
    ("كيفية قراءة شمعة الموجة العالية", [1917, 2353]),
    ("كيفية قراءة نمط الكيكر", [1939, 2373]),
    ("كيفية قراءة خطوط الالتقاء", [1947, 2381]),
    ("كيفية قراءة نمط نجمة الصباح", [1949, 2383]),
    ("كيفية قراءة نمط نجمة المساء", [1951, 2385]),
    ("كيفية قراءة نمط الغربان الثلاثة المتطابقة", [1975, 2409]),
    ("كيفية قراءة نمط الطرق الثلاثة الهابطة", [1981, 2415]),
    ("لماذا تهم تعليقات وخطابات أعضاء البنوك المركزية للأسواق", [2027, 2461]),
    ("قراءات مؤشر أسعار المستهلك في الولايات الألمانية تشير إلى عودة التضخم فوق 3% في سبتمبر", [2045, 2479]),
    ("الأسهم الأمريكية تفتتح على ارتفاع بعد تباطؤ التوظيف", [2099, 2533]),
    ("لماذا لا تنخفض الأسهم؟", [2175, 2609]),
    ("ما هي أزواج العملات؟ الأزواج الرئيسية والثانوية والغريبة موضحة", [2191, 2623]),
    ("ما هو اللوت في الفوركس؟", [2195, 2627]),
    ("ما هو الانزلاق السعري في تداول الفوركس؟", [2201, 2633]),
    ("ما هي الرافعة المالية في الفوركس؟", [2207, 2639]),
    ("كم يجب أن تخاطر به في كل صفقة فوركس؟", [2223, 2655]),
    ("كيفية اختيار وسيط الفوركس", [2227, 2659]),
    ("وسطاء الفوركس المنظمون مقابل غير المنظمين", [2231, 2663]),
    ("ماذا يحدث إذا أفلس وسيط الفوركس الخاص بك؟", [2233, 2665]),
    ("ما هو وسيط الفوركس ECN؟", [2241, 2673]),
    ("ما هو وسيط الفوركس STP؟", [2243, 2675]),
    ("شرح تنظيم الفوركس من هيئة FSCA", [2255, 3473]),
    ("ما هي أبرز أحداث اليوم؟", [2443, 2603]),
    ("ما هي أهم أحداث اليوم؟", [2463, 2507]),
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
            self_consistent = trans.get("ar") == m["id"]
            print(
                f"  id={m['id']} date={m['date']} slug={m['slug']} "
                f"categories={m.get('categories')} self_consistent={self_consistent} translations={trans}"
            )
        print()


if __name__ == "__main__":
    main()
