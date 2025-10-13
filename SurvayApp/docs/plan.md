<!-- 7c164917-7aed-4e81-a803-5609c8d8e96c af51545d-52b1-40a9-bfae-d3b814a25460 -->
## אפיון שלב א' + השוואת אופציות טכנולוגיות

נשמור בשלב זה את ממשק ה‑GET החיצוני כמות‑שהוא (כולל שמות פרמטרים), נבצע הקשחה וריפקטור נקודתי, ונכין תשתית להוספת ממשק השאלון בהמשך.

### שלב 0: בדיקת סביבה והרצה (לפני כל שינוי)

- אימות סביבת שרת: גרסת PHP תואמת CI3, הרחבות (mysqli, mbstring, json), ו‑`index.php` (ENVIRONMENT, `application`/`system`).
- הרשאות כתיבה: `application/logs` ו‑`sys_get_temp_dir()` (sessions).
- בדיקת קונפיג: `application/config/{config.php, database.php, routes.php, autoload.php}` ומיפוי `HTTP_HOST` למסדי נתונים.
- בדיקות עשן: קריאת GET ל‑`welcome/index`, התחברות מנהל, סינון/ייצוא, `welcome/generate` (localhost).

### טבלת השוואה: חלופות טכנולוגיות

| אפשרות | פשטות/זמן ל‑MVP | תחזוקה | אבטחה | עלות הגירה | סיכונים | ממשק שאלון |
|---|---|---|---|---|---|---|
| 1) הקשחה ב‑CI3 | גבוהה | בינונית | דורש חיזוקים | נמוכה | לגאסי | טובה |
| 2) Laravel | בינונית | גבוהה | מצוינת | בינונית | מיגרציה | מצוינת |
| 3) Node+SPA | נמוכה | גבוהה | מצוינת | גבוהה | שינוי ערימה | מצוינת |

### חישוב ציונים — לימוד והפרדה לאלגוריתם שירות

- מיפוי סכימה: `dimensiondatagroups`, `dimensiondata`, `dimensiontypes`, `questions`, `feedbacks`, `feedbackdims`, `responderextradatas`.
- שלבים קיימים: איסוף PD_*; Z לכל ממד; SIG לממדים [1,3,4,5,6] עם `sigTotalAverage = sigTotalVal / 56`; `mazDimRes`=ממד 9; `totalDimRes = (sigDimRes + mazDimRes)/2`; threshold מממד 8; `finalGroup` לפי `Social_desirability_ReRun` ו‑threshold.
- הפרדה לשירות (למשל `ScoreCalculator`) ללא שינוי לוגיקה; תיעוד קבועים; בדיקות אי‑רגרסיה מול המימוש הנוכחי.

### בדיקת סכימה והיררכיה + הצעות שיפור

- רוורס‑הנדסה ל‑ERD מהמודלים וה‑SQL (`fresh.sql`, `upgrade.sql`, `refresh.sql`).
- איתור כפילויות/Redundancy:
- `feedbacks.rowData` מול `responderextradatas` (PD_* נשמר גם גולמי וגם מפורק).
- חפיפות בהגדרות ממדים/קבוצות בין `dimensiondata`, `dimensiontypes`, `dimensiondatagroups`.
- שדות זהים ב‑`companies`/`divisions` המציינים שיוכים.
- מיפוי היררכיה/לוגיקה:
- חברות → חטיבות → משיבים → שיוך ל‑`dimensiondatagroup` ולסט נורמות/thresholds.
- כללי ירושה (parentId בחברה), והרשאות מנהל‑על מול מנהל לקוח.
- שיפורים מוצעים (ללא שבירת API בשלב א'):
- נרמול שיוך לקבוצות ציון: טבלת Mapping מפורשת `division_to_attr_group` או `company_division_norm_set` עם תארוך־גרסאות.
- קונסולידציה של PD: שמירת JSON נורמלי ב‑`feedbacks.json` בלבד + המשך `responderextradatas` כמבנה נגיש לשאילתות (מניעת כפילויות), או להפך — החלטה אחת עקבית.
- וורסיונינג של פרמטרי נורמות (`dimensiondata`) כדי לאפשר עדכונים מבוקרים והיסטוריה.
- הסרת "מספרים קסומים" (1,3,4,5,6,7,8,9,56) לקבועים מתועדים/טבלת קונפיג.
- תוכנית מיגרציה בטוחה:
- יצירת טבלאות/מפתחות חדשים Additive.
- Backfill נתונים וכלי אימות דטרמיניסטי (השוואת סכומים/ספירות).
- שלב Dual‑read/dual‑write קצר, ואז Cutover.

### עיקרי העבודות לשלב א' (ללא שבירת ה‑API החיצוני)

- אבטחה ו‑Input: מעבר ל‑Query Builder ב‑`_getQuery()`; אימות מקור (Allowlist/HMAC); החלפת `eval` ב‑`JSON.parse`; הקשחת Views.
- זהויות והרשאות: גיבוב סיסמאות (bcrypt); rate‑limit; הקשחת login.
- ריפקטור: הוצאת החישוב לשירות; שימוש ב‑`autoload` ללודרים הנחוצים.
- נכסים: קיבוע גרסאות CDN או נכסים מקומיים.
- דוחות/ייצוא: הקשחת CSV ושדות.
- לוגים/ניטור: יישור רמות לוג והתראות אימייל בטוחות.

### קבצים עיקריים שיושפעו

- `till.mezoo.co.il_bm.../application/controllers/Welcome.php`
- `till.mezoo.co.il_bm.../application/helpers/mezoo_helper.php`
- `till.mezoo.co.il_bm.../application/config/{config.php, database.php, autoload.php, routes.php}`
- `till.mezoo.co.il_bm.../application/views/{admin_template.php, report.php, _report.php}`
- `till.mezoo.co.il_bm.../js/mashov.js`

### תוצרים לשלב א'

- סביבה רצה ומדויקת מאומתת (פרוד/סטייג'/לוקאל) עם בדיקות עשן.
- אלגוריתם ציונים מופרד כשירות עם בדיקות אי‑רגרסיה.
- תוכנית נרמול/מיגרציה לסכימה והיררכיה ללא שבירת API (בינתיים).
- דשבורד מנהל מוקשח וייצוא בטוח; דוח HTML יציב ללא `eval`.
- חבילת דוקומנטציה מלאה בעברית תחת `SurvayApp/docs`.

### חבילת הדוקומנטציה (docs/) – מבנה ותכולה

- README.md – קובץ ראשי: סקירה, איך להתחיל, קישורים למסמכים, מפת ניווט.
- index.md – אינדקס ניווט מסודר לפי נושאים.
- plan.md – תכנית עבודה (ייבוא ועדכון מתוכן `/survey.plan.md`).
- architecture.md – ארכיטקטורה כללית, זרימות, רכיבים עיקריים, נקודות אינטגרציה.
- data-model.md – ERD טקסטואלי, פירוט טבלאות, קשרים, אינדקסים, הערות MyISAM.
- scoring-algorithm.md – פירוט שלבי האלגוריתם, קבועים, מיפוי ממדים (SIG/MAZ/TOTAL), מקרי קצה.
- api.md – חוזה ה‑GET החיצוני: פרמטרים, דוגמאות בקשה/תגובה, קודי שגיאה, אבטחה (HMAC/Allowlist – הצעה).
- admin-ui.md – מסכי מנהל, פילטרים, מיון, ייצוא, דוח HTML ב‑localhost.
- security.md – ממצאי אבטחה (SQLi, eval, סיסמאות גלוי, CSRF/XSS), תכנית הקשחה שלב‑א'.
- operations.md – סביבה וקונפיג (ENV/DB), בדיקות עשן, לוגים, ניטור, דרישות הרשאות.
- developer-guide.md – מדריך למפתח: הקמה לוקאלית, סקריפטים שימושיים, סגנון קוד, בדיקות.
- roadmap.md – מפת דרכים לשדרוגים (נרמול/וורסיונינג/CI→Laravel/Front‑end מודרני), שלבים.
- changelog.md – יומן שינויים (יתחיל ריק, יתעדכן עם כל שינוי).

### To-dos

- [ ] הקשחת קלט/אבטחה ושמירת API חיצוני
- [ ] פירוק לוגיקת Welcome לשכבת שירות
- [ ] הצפנת סיסמאות, rate-limit והקשחת login
- [ ] הסרת eval והקשחת mashov.js ו‑Views
- [ ] הקשחת ייצוא CSV ובקרת שדות
- [ ] קיבוע גרסאות CDN או מעבר לנכסים מקומיים
- [ ] יישור רמות לוג, ניטור והתראות אימייל
