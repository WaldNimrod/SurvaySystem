# מדריך למפתח

- דרישות: PHP תואם CI3, MySQL, הרחבות mysqli/mbstring/json.
- הרצה לוקאלית: הגדרת DB מקומי, עדכון config/database.php, גלישה ל-index.
- סטייל קוד: להימנע ממספרים קסומים; קוד קריא ובדיקות.
- Git: סניפים לפי פיצ'רים; PR עם ריוויו; תגיות גרסה.
 
## דוח התפלגות (Distribution)

- קונפיג: `application/config/config.php`
  - `distribution_max_rows` (ברירת מחדל 3000, ניתן לעדכון עם `DIST_MAX_ROWS`).
  - `distribution_bins` (ברירת מחדל 12, ניתן לעדכון עם `DISTRIBUTION_BINS`).
- קונטרולר: `Welcome::distribution()` מייצר JSON לתצוגה ו־`Welcome::distribution_export()` מייצר CSV.
- תצוגה: `application/views/distribution.php` (RTL, tabs, Export/Print).
- הנתונים מחושבים לפי המסננים של מסך `admin` באמצעות `_getQuery()`.