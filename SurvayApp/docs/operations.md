# הפעלה וכיבוי סביבת פיתוח מקומית

המסמך הזה מרכז את ההוראות המדויקות להפעלה/כיבוי של השרת המקומי ושל ה‑DB (Docker), כדי שנזכור בדיוק מה לעשות בכל פעם.

## הקדמות חד‑פעמיות
- Docker מותקן ופועל.
- PHP CLI זמין (brew php).

## הפעלה
1) הרם MySQL בדוקר (רץ רק כשעובדים):
```bash
cd SurvayApp/docker
docker compose up -d
```
2) הגדר משתני סביבה לחיבור DB (אפשר להוסיף ל‑~/.zshrc או להריץ בכל סשן):
```bash
export MEZOO_DB_HOST=127.0.0.1
export MEZOO_DB_PORT=3307
export MEZOO_DB_USER=root
export MEZOO_DB_PASS=root
export MEZOO_DB_NAME=mezoo
```
3) הרם שרת PHP מקומי:
```bash
SurvayApp/tools/start_local.sh
```
4) גלישה לעמוד כניסה:
- http://localhost:8000/welcome/login

## בדיקת Smoke (אופציונלי)
```bash
BASE_URL="http://localhost:8000" SurvayApp/tests/smoke/smoke.sh
```

## כיבוי בסיום עבודה
1) כיבוי שרת PHP:
```bash
SurvayApp/tools/stop_local.sh
```
2) כיבוי DB Docker:
```bash
cd SurvayApp/docker
docker compose down
```

## הערות
- קבצי דוגמה להגדרות נמצאים ב‑SurvayApp/.env.local.example. ניתן ליצור SurvayApp/.env.local ולשים בו ערכים קבועים.
- ייבוא סכימה ראשוני מתבצע אוטומטית ב‑Docker דרך fresh.sql. אם יש צורך לייבא ידנית — צרו קשר.
