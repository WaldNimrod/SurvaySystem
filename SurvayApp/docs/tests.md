# בדיקות — מדריך

## מה רץ אצלי (אוטומטי/חצי-אוטומטי)
- אוסף Postman מוכן תחת docs/postman/SurvayApp.postman_collection.json.
- סקריפט smoke (curl) תחת tests/smoke/smoke.sh.

## איך להריץ (לוקאל/סטייג'/פרוד)
- הגדירו BASE_URL (למשל https://till.mezoo.co.il_bm1756763301dm).
- הריצו tests/smoke/smoke.sh (קריאת GET בסיסית, יצוא).
- ב-Postman: ייבאו את האוסף, הגדירו משתנה baseUrl, והריצו בקשות לפי הסדר.

## מה לבדוק ידנית
1) GET חיצוני — תקבלו JSON 200/שגיאות בהתאם לפרמטרים.
2) התחברות — ניסיון כושל (ננעל ל-rate-limit אחרי 5) ואז הצלחה.
3) דשבורד — סינון/מיון, הערות, ייצוא CSV (נפתח באקסל ללא אזהרות נוסחה).
4) דוח — ב-localhost נפתח HTML; בפרוד נשאר JSON בלבד (צפוי).
