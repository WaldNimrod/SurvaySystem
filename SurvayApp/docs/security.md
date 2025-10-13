# אבטחה והקשחה

ממצאים:
- SQL concatenation ב-_getQuery(): סיכון הזרקה.
- eval ב-mashov.js.
- סיסמאות גלוי; ללא CSRF/XSS גלובליים; MyISAM.

פעולות שלב א':
- מעבר ל-Query Builder + binding בכל השאילתות.
- JSON.parse במקום eval; הקשחת Views.
- גיבוב סיסמאות (bcrypt) + rate-limit; הפעלת CSRF בטפסים.
