# ממשק מנהל

- התחברות: welcome/login (כרגע השוואת טקסט; יעודכן לגיבוב).
- מסך admin: סינון לפי תאריך/חברה/חטיבה/קבוצה, מיון, הערות, ייצוא CSV.
  - כפתור Distribution report: פותח דוח התפלגות (welcome/distribution) על בסיס המסננים הפעילים.
  - כפתור Distribution CSV: מייצא את חישוב ההתפלגות לקובץ CSV (welcome/distribution_export) עם אותם פרמטרים.
  - הערה: אם כמות הרשומות גדולה מהמגבלה (`distribution_max_rows`) יוצג מסר קיצוץ בדוח.
