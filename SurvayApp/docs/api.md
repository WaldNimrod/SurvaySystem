# API חיצוני (GET)

- נקודת כניסה: /welcome/index
- פרמטרים חיוניים: PD_IDNumber, CompanyId, divisionId, SurveyId, ResponseId, ועוד PD_*/SIG_*/MZA_*.
- תגובה בפרוד: JSON סטטוס/שגיאות; בלוקאל: דוח HTML (report view).
- אבטחה מוצעת: חתימת HMAC על פרמטרים/Allowlist IPs.
