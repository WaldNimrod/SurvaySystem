# אלגוריתם חישוב ציונים

שלבים:
1. איסוף PD_* ושמירתם (rowData + responderextradatas + result.PD).
2. חישוב Z לכל ממד: ממוצע תשובות נענות לממד ÷ ספירה; Z = (avg - average) / std.
3. SIG: סכימה של ממדים [1,3,4,5,6] → sigTotalAverage = sigTotalVal / 56 → Z-sig לפי ממד 7.
4. MAZ: ממד 9; Total: (sigDimRes + mazDimRes)/2; threshold ממד 8.
5. finalGroup לפי Social_desirability_ReRun ו-threshold.

הערה: המספרים הקסומים (1,3,4,5,6,7,8,9,56) יתועדו/יועברו לקבועים/קונפיג.
