<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Untitled Document</title>
	<link href="https://<?php echo $_SERVER['HTTP_HOST'];?>/css/style.css" rel="stylesheet" type="text/css" />

	<script src="https://<?php echo $_SERVER['HTTP_HOST'];?>/js/jquery-1.9.0.js" type="text/javascript"></script>
	<script src="https://<?php echo $_SERVER['HTTP_HOST'];?>/js/mashov.js" type="text/javascript"></script>
</head>
<body>
<span id="TextData" runat="server" style="display: none;"><?php echo $result;?></span>
<div class="container">

	<table cellpadding="0" cellspacing="0" width="850">
		<tr>

			<td><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics/company-logos/<?php echo $companyId;?>.jpg"  style="max-height:71px;max-width:420px; display:none"/><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics/logo-small.jpg" /></td>
		</tr>
	</table>

	<div class="content">
		<h1>סודי - אישי<br />
			דו"ח התאמה לתפקיד עמוד 1 מתוך 3</h1>

		<table  border="0" cellpadding="2" cellspacing="0" id="PersonalD_Table" class="PersonalData">
		</table>
	</div>
	<h2>הערכת יושרה וערכי עבודה </h2>

	<p> מערכת הערכת יושרה וערכי עבודה בודקת את נטייתו של העובד להתנהגות אתית בתחום העבודה.

		התוצאות מבוססות על דיווחי הנבדק ותשובותיו לשאלונים פסיכולוגיים הבודקים את רמת מצפוניותו ואת ערכי העבודה שלו.

		התוצאות מבוססות על נורמות המותאמות לאוכלוסיית המועמדים לתפקיד הנתון ונתקבלו על סמך מחקרים מדעיים שבדקו את תוקף המבחן. </p>
	<!-- end .content -->

	<hr />

	<div style="height:850px">&nbsp;</div>


	<h1>סודי - אישי<br />
		דו"ח התאמה לתפקיד עמוד 2 מתוך 3</h1>
	<table  border="0" cellpadding="2" cellspacing="0" class="PersonalData">
		<tr><td>מספר נבדק:</td><td id="PD_candidateID">[F_NAME] [LAST_NAME]</td></tr></table><br />
	<div style="text-align: left; margin-left: 50px;">
		<strong id="debug_txt_res"></strong>
	</div>
	<div style="width:850px; position:relative;">
		<div class="ParamContainer" id="dim_mnipulation">
			<h3>מניפולטיביות </h3>
			<div class="ParamRightText"><p class="ParamText">ישיר ואמיתי מסוגל להזדהות עם רגשות הזולת ולהתחשב בהם.</p></div>
			<div class="ParamLeftText"><p class="ParamText">מרוכז בעצמו, חסר אמפטיה</p> </div>
			<div class="SliderContainer"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\SliderBg.jpg" width="390" height="50" /></div>
			<div class="Slider_Arrow"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\arrow.png" /></div>
		</div>
	</div>
	<div style="width:850px; position:relative;">
		<div class="ParamContainer" id="dim_adishut">
			<h3>אדישות:</h3>
			<div class="ParamRightText"><p class="ParamText">רגיש למצבים מעוררי חרדה, חש במתח כאשר פועל באופן לא מקובל, "לוקח ללב".</p></div>
			<div class="ParamLeftText"><p class="ParamText">אינו חווה מתח וחרדה, שומר על דימוי של אדם שליו ורגוע, לא חש במתח כאשר מתנהג בחוסר כנות, אדיש לנוכח מצבים מעוררי חרדה.</p></div>
			<div class="SliderContainer"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\SliderBg.jpg" width="390" height="50" /></div>
			<div class="Slider_Arrow"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\arrow.png" /></div>
		</div>
	</div>
	<div style="width:850px; position:relative;">
		<div class="ParamContainer" id="dim_impulsive">
			<h3>אימפולסיביות:</h3>
			<div class="ParamRightText"><p class="ParamText">מחושב, שקול, זהיר, דייקן, מתכנן לטווח ארוך </p></div>
			<div class="ParamLeftText"><p class="ParamText">אימפולסיבי, שאנן, מתקשה בראיה לטווח ארוך, "נהנה לחיות את הרגע" </p></div>
			<div class="SliderContainer"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\SliderBg.jpg" width="390" height="50" /></div>
			<div class="Slider_Arrow"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\arrow.png" /></div>
		</div>
	</div>
	<div style="width:850px; position:relative;">
		<div class="ParamContainer" id="dim_unResponsible">
			<h3>אי לקיחת אחריות:</h3>
			<div class="ParamRightText"><p class="ParamText">נותן אמון בזולת, אינו מאשים אחרים בטעויות, מתייחס לאחרים באופן חיובי ואופטימי. </p></div>
			<div class="ParamLeftText"><p class="ParamText">מאשים אחרים בטעויות או בהתנהות לא מקובלת, נוטה לחוש שמתנכלים לו, נוטה לחוות את עצמו כקורבן, ביקורתי. </p></div>
			<div class="SliderContainer"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\SliderBg.jpg" width="390" height="50" /></div>
			<div class="Slider_Arrow"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\arrow.png" /></div>
		</div>
	</div>
	<div style="width:850px; position:relative;">
		<div class="ParamContainer" id="dim_halaklak">
			<h3>חלקלקות בין אישית:</h3>
			<div class="ParamRightText"><p class="ParamText">מחוספס, ישיר, נתפש כאמיתי על ידי אחרים, קל לזולת לאבחן את רגשותיו ומחשבותיו, מסוגל להיות נזפני ובוטה. </p></div>
			<div class="ParamLeftText"><p class="ParamText">בעל קסם אישי, בעל יכולת שכנוע בקשר בין אישי, חלקלק, "שומר על הקלפים". </p></div>
			<div class="SliderContainer"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\SliderBg.jpg" width="390" height="50" /></div>
			<div class="Slider_Arrow"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\arrow.png" /></div>
		</div>
	</div>
	<hr />
	<div style="width:850px; position:relative;">
		<div class="ParamContainer" id="dim_mzaSum">
			<h3>סגנון קבלת החלטות בפתרון דילמות אתיות:</h3>
			<div class="ParamRightText"><p class="ParamText">מביע רמת מצפוניות נאותה, המעידה על הפנמה של ערכים ונורמות מקובלות.</p></div>
			<div class="ParamLeftText"><p class="ParamText">מביע רמת מצפוניות נמוכה, המעידה על קושי בהפנמה של ערכים ונורמות מקובלות. לפיכך, פועל בעיקר מתוך שיקולים תועלתניים.</p></div>
			<div class="SliderContainer"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\SliderBg.jpg" width="390" height="50" /></div>
			<div class="Slider_Arrow"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\arrow.png" /></div>
		</div>
	</div>
	<div style="width:850px; position:relative;">
		<div class="ParamContainer" id="dim_sumTotal">
			<h3>סיכום והמלצה: רמת יושרה ואמינות</h3>
			<div class="ParamRightText"><p class="ParamText">רמת יושרה מקובלת</p></div>
			<div class="ParamLeftText"><p class="ParamText">רמת יושרה נמוכה</p></div>
			<div class="SliderContainer"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\SliderBg.jpg" width="390" height="50" /></div>
			<div class="Slider_Arrow"><img src="https://<?php echo $_SERVER['HTTP_HOST'];?>/pics\arrow.png" /></div>
		</div>
	</div>
	<div style="height:250px">&nbsp;</div>
	<hr />
	<h1>סודי - אישי<br />
		דו"ח התאמה לתפקיד עמוד 3 מתוך 3</h1>
	<h2 style="display:none">מאפיינים</h2>
	<div style="padding-right:15px; display:none">
		<div id="ParamText_dim_mnipulation-1.499-0.999">

			<!-- מניפולטיביות  -->
			<div id="ParamText_M_H">
				<!--Z>1.499 -->
				- מרוכז בעצמו, לא מתחשב בזולת, מוכן "לעגל פינות" על מנת להשיג את מבוקשו, מניפולטיבי כדי להשיג את מטרותיו.
			</div>
			<div id="ParamText_M_M">
				<!--1.5<Z>.999 -->
				- נוטה להיות מניפולטיבי כדי להשיג את מטרותיו אך יש מצבים בהם הוא יכול גם להתנהג בצורה הנראית כמתחשבת באחרים.
			</div>
			<div id="ParamText_M_L">
				<!--Z<1  -->
				- ישיר ואמיתי, מסוגל להיות אמפטי כלפי הזולת ומתחשב באנשים.
			</div>
		</div>
		<div id="ParamText_dim_halaklak-1.499-0.999">
			<!-- חלקלקות בין אישית  -->
			<div id="ParamText_S_H">
				<!--Z>1.499 -->
				- בעל בטחון עצמי חברתי גבוה, יודע לשכנע ומציג מסיכה של נעימות בין אישית כדי להשיג את מטרותיו.
			</div>
			<div id="ParamText_S_M">
				<!--1.5<Z>.999 -->
				- בדרך כלל בעל בטחון עצמי חברתי גבוה, יודע לשכנע ומציג מסיכה של נעימות בין אישית כדי להשיג את מטרותיו. במצבים מסוימים יכול גם להתנהג גם באופן ישיר ומחוספס.
			</div>
			<div id="ParamText_S_L">
				<!--Z<1  -->
				- מחוספס, ישיר, נתפש כאמיתי על ידי אחרים, קל לזולת לזהות  את רגשותיו ומחשבותיו.
			</div>
		</div>
		<div id="ParamText_dim_impulsive-1.499-0.999">
			<!-- אימפולסיביות    -->
			<div id="ParamText_P_H">
				<!--Z>1.499 -->
				- מתקשה לתכנן לטווח ארוך, יכול להגיב  ללא מחשבה מוקדמת, מתקשה לדחות ספוקים.
			</div>
			<div id="ParamText_P_M">
				<!--1.5<Z>.999 -->
				- בדרך כלל מתקשה לתכנן לטווח ארוך, יכול להגיב  ללא מחשבה מוקדמת, מתקשה לדחות ספוקים.  לעתים הוא גם מסוגל לחשוב ולתכנן מראש.
			</div>
			<div id="ParamText_P_L">
				<!--Z<1  -->
				- מתכנן לטווח ארוך מחושב ושקול, מסוגל לדחות ספוקים.
			</div>
		</div>
		<div id="ParamText_dim_unResponsible-1.499-0.999">
			<!--אי לקיחת אחריות    -->
			<div id="ParamText_B_H">
				<!--Z>1.499 -->
				- נוטה לא לקחת אחריות על מחדליו, מאשים אחרים בטעויות או בהתנהגות לא מקובלת, חשדן ונוטה לחוש שמתנכלים לו., נוטה לחוות את עצמו כקורבן, ביקורתי.
			</div>
			<div id="ParamText_B_M">
				<!--1.5<Z>.999 -->
				- בדרך כלל נוטה לא לקחת אחריות על מחדליו, מאשים אחרים בטעויות או בהתנהגות לא מקובלת, חשדן ונוטה לחוש שמתנכלים לו., אולם, בקשרים ארוכי טווח מסוגל לסמוך יותר על אחרים.
			</div>
			<div id="ParamText_B_L">
				<!--Z<1  -->
				- נוטה לקחת אחריות על מעשיו, אינו מאשים אחרים בטעויות, חושב שאחרים מתייחסים אליו בכבוד ובצורה חיובית.
			</div>
		</div>
		<div id="ParamText_dim_adishut-0.999">
			<!--אדישות    -->
			<div id="ParamText_A_H">
				<!--Z>1 -->
				- אדיש למצבים בהם הוא מתנהג באופן לא מקובל, לא חושש מלקיחת סכון, חש בנוח גם כאשר הוא אינו דובר אמת או עובר על החוק.
			</div>
			<div id="ParamText_A_L">
				<!--Z<1  -->
				- רגיש למצבים מעוררי חרדה, חש במתח כאשר פועל באופן לא מקובל, "לוקח ללב".
			</div>

		</div>
	</div>
	<hr />

	<h2>בשקלול כל הנתונים המתייחסים למצפוניות וערכי עבודה של המועמד התקבלה המסקנה הבאה:</h2>
	<div id="Group1">
		<p id="GrupText1">
			<strong>הנבדק שייך לקבוצה: 1</strong><br />
			המועמד שייך לקבוצה שאחוז גבוה של האנשים הכלולים בה <strong>אינם עונים בכנות על השאלונים</strong>, ולכן לא ניתן להעריך את רמת היושרה שלהם. <br />

		<h3>על מנת לקבל החלטה לגבי המועמד מומלץ לבחון את רמת היושרה באמצעים נוספים.</h3>

		</p>
	</div>
	<div id="Group2">
		<p id="GrupText2"><strong>הנבדק שייך לקבוצה: 2</strong><br />

			המועמד שייך לקבוצה שאחוז גבוה של האנשים הכלולים בה הנם בעלי <span class="BlueText">רמת יושרה גבוהה.</span> הם נוהגים לפי הנורמות המקובלות, שומרים על נהלים וחוקים, לוקחים אחריות על פעולותיהם ואינם מנצלים אחרים לתועלתם.
		<h3><span class="BlueText">בהתייחס לרמת היושרה שלו מומלץ לקבלו.</span></h3>

		</p>
	</div>
	<div id="Group3">
		<p id="GrupText3"><strong>הנבדק שייך לקבוצה: 3</strong><br />

			המועמד שייך לקבוצה שאחוז גבוה של האנשים הכלולים בה הנם בעלי <span class="RedText">רמת יושרה נמוכה.</span> התנהגותם מושפעת משיקולים תועלתניים. הם נוטים לעבור על החוק, ולהפר נורמות וכללים. הם אינם לוקחים אחריות ל פעולותיהם ועלולים לנצל אחרים לתועלתם האישית.</p>
		<h3><span class="RedText">בהתייחס לרמת היושרה שלו מומלץ לא לקבלו.</span></h3>
		</p>
	</div>
	<div id="Social_desirability_ReRun" class="">
		<!--יש להציג חלק זה במקרה ומשתנה Social_desirability_ReRun=yes -->
		<!--המועמד ענה על מבחן המצפוניות ברצייה חברתית גבוהה ולמרות זאת,
		<h3><span class="RedText"> יש לנבדק נטיה להציג את עצמו באור חיובי מדי.</span></h3> -->

	</div>
	<hr />
	<!-- <h1>  סודי – אישי  <br />
    תוצאות מבחן היושרה  </h1>
     <table  border="0" cellpadding="2" cellspacing="0" class="PersonalData">
                <tr><td>מספר נבדק:</td><td id="PD_candidateID">[F_NAME] [LAST_NAME]</td></tr>
                <tr><td>שם פרטי:</td><td id="PD_firstName">[F_NAME] [LAST_NAME]</td></tr>
                <tr><td>שם משפחה:</td><td id="PD_lastName">[F_NAME] [LAST_NAME]</td></tr>
                <tr><td>ת.ז:</td><td id="PD_IDNumber">[F_NAME] [LAST_NAME]</td></tr>
      </table><br /> -->
	<h2>דברי הסבר </h2>
	<h4>המבחן מסווג את הנבחנים לפי תגובותיהם ל- 3 קבוצות:</h4>
	<div style="padding:10px; margin-right:20px"><ul><li><strong>קבוצה 1:</strong> קבוצה שאחוז גבוה של האנשים הכלולים בה אינם עונים בכנות על השאלונים, ולכן לא ניתן להעריך את רמת היושרה שלהם.</li>
			<li><strong>קבוצה 2:</strong> קבוצה שאחוז גבוה של האנשים הכלולים בה הנם בעלי רמת יושרה נאותה. הם נוהגים לפי הנורמות המקובלות, שומרים על נהלים וחוקים, לוקחים אחריות על פעולותיהם ואינם מנצלים אחרים לתועלתם.</li>
			<li><strong>קבוצה 3:</strong> קבוצה שאחוז גבוה של האנשים הכלולים בה הנם בעלי רמת יושרה נמוכה. התנהגותם מושפעת משיקולים תועלתניים, הם נוטים לעבור על החוק ולהפר נורמות וכללים, הם אינם לוקחים אחריות לפעולותיהם ועלולים לנצל אחרים לתועלתם האישית.</li></ul>

<span class="RedText">על פי התשובות של המועמד/ת והנורמות של האוכלוסייה, המועמד/ת
    שייך/ת לקבוצה <strong id="Group1">1: אינם עונים בכנות על השאלונים</strong>
    <strong id="Group2"><span class="BlueText">2: רמת יושרה גבוהה.</span></strong>
    <strong id="Group3">3: רמת יושרה נמוכה</strong>
</span>
	</div>
	<!--
    <h4>דוגמאות לתשובות שנתן הנבדק לשאלות קריטיות: </h4>
    <div style="padding:10px; margin-right:20px"><ol>
    <li>אני משתמש די הרבה ב'שקרים לבנים'.<strong> תשובה: מסכים</strong></li>
    <li>אני מאוד מתרגז כשאני לא מקבל את הזכויות וההטבות המיוחדות שמגיעות לי. <strong>תשובה: מסכים</strong></li>
    <li>אם מישהו מתייחס אלי לא יפה אני מעדיף לסלוח לו מאשר לכעוס עליו. <strong>תשובה: מתנגד</strong></li>
    <li>אני קפדן מאוד כשעלי לעשות עבודה הכרוכה בפרטים רבים. <strong>תשובה: מתנגד</strong></li>
    <li>לעיתים קרובות אני עושה דברים מתוך דחף פתאומי. <strong>תשובה: מסכים מאוד</strong></li>

    </ol>
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    -->
	<!-- end .content --></div>








<!-- end .container --></div>
</body>
</html>
