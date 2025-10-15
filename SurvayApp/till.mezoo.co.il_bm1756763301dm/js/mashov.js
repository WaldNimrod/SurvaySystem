//This JS Responsible to create the mashov after getting the final data from the sever

var ParamTextName = 'ParamText';
var Data; //Containes the data returned from the server For creating the mashov
var RectSize; // the size of 1 squere of Slider (represent 1 standard deviation unit)

$(document).ready(function () {
    RectSize = $($('.SliderContainer')[0]).width() / 6;

    //Taking the data from the server side (converting the String to a Javascript object that containes all the data from the server)
    try {
        Data = JSON.parse($('#TextData').text());
    } catch (e) {
        console && console.error && console.error('Failed parsing result JSON', e);
        return;
    }

    Data = Data || {};
    Data.PD = Data.PD || {};
    Data.Dims = Data.Dims || {};

    // New modern report builder (cards + gauges) — will run after legacy ParamText logic

    //Creating the Personal Details of the responder
    $PDTable = $('#PersonalD_Table');
    $.each(Data.PD, function (key, value) {
        $PDTable.append(
            $('<tr>').append(
                $('<td>').text(String(value).split(';')[0] || '')
            ).append(
                $('<td>').text(String(value).split(';')[1] || '')
            )
        );
    });

    //filling all the texts with an ID of PD_XXX
    $("[id^='PD']").each(function () {
        var IDval = Data.PD && Data.PD[this.id];
        if (IDval) {
            $(this).text((String(IDval)).split(';')[1] || '');
        }
    });

    //Configuring the sliders positions:
    $.each(Data.Dims, function (key, value) {
        var $DimDiv = $('#' + key);
        if (!$DimDiv.length) return;
        var moveArrow = 18;
        var res = parseFloat(value && value.res);
        if (isNaN(res)) return;
        if (res > 3) res = 3;
        if (res < -3) res = -3;
        moveArrow += res * -1 * RectSize;
        $DimDiv.find('.Slider_Arrow').find('img')
            .css('position', 'relative')
            .css('left', moveArrow + 'px');
        if (value && value.threshold != null) {
            var $dimThreshold = $('<div style="position:absolute; z-index:3; top:25px;">');
            var tleft = 320;
            tleft += parseFloat(value.threshold) * -1 * RectSize;
            $dimThreshold.css('left', tleft + 'px');
            $dimThreshold.append($('<img src="data:image/png;base64,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" width="54" height="75">'));
            $DimDiv.append($dimThreshold);
        }
    });

    $.each($("div[id*='ParamText_dim']"), function () {
        var PTextID = this.id;
        var delimiters = PTextID.split('-');
        var dimID = delimiters.splice(0, 1)[0];
        dimID = dimID.substring(ParamTextName.length + 1);
        var DimRes = Data.Dims && Data.Dims[dimID] && Data.Dims[dimID].res;
        if (DimRes == null) return;
        var $PTexts = $(this).find('div');
        for (var dindx = 0; dindx < $PTexts.length; dindx++) {
            if (dindx == 0) {
                if (DimRes <= delimiters[dindx]) {
                    $($PTexts[dindx]).hide();
                }
            }
            else if (dindx + 1 == $PTexts.length) {
                if (DimRes >= delimiters[dindx - 1]) {
                    $($PTexts[dindx]).hide();
                }
            }
            else {
                if (DimRes >= delimiters[dindx - 1] || DimRes <= delimiters[dindx]) {
                    $($PTexts[dindx]).hide();
                }
            }
        }
    });

    var sdr = (Data.Social_desirability_ReRun || '').toString();
    if (sdr && (sdr.toLowerCase() == 'false' || sdr.toLowerCase() == 'yes')) {
        if (sdr.toLowerCase() != 'yes') {
            $('#Social_desirability_ReRun').hide();
        }
        $('[id=Group1]').hide();
        var sumObj = Data.Dims && Data.Dims['dim_sumTotal'];
        if (sumObj && parseFloat(sumObj.res) <= parseFloat(sumObj.threshold)) {
            $('[id=Group3]').hide();
        }
        else {
            $('[id=Group2]').hide();
        }
    }
    else if (sdr && sdr.toLowerCase() == 'double') {
            $('[id=Group2]').hide();
            $('[id=Group3]').hide();
        }

    // Build modern report now (ParamText logic applied above)
    buildModernReport(Data);

    // Optional debug table
    if (window.DEBUG_MODE) {
        var $tbl = $('#DebugDimsTable tbody');
        $.each(Data.Dims, function (k, v) {
            var tr = $('<tr>');
            tr.append($('<td>').text(k));
            tr.append($('<td>').text(v && v.res));
            tr.append($('<td>').text(v && v.threshold));
            $tbl.append(tr);
        });
        $('#DebugPanel').show();
    }
});

// Build modern report UI from Data JSON
function buildModernReport(Data) {
    var $root = $('#ModernReport');
    if (!$root.length) return;

    // Final group card
    try {
        var sum = Data.Dims && Data.Dims['dim_sumTotal'] || {};
        var sdr = (Data.Social_desirability_ReRun || '').toString().toLowerCase();
        var finalHtml = '';
        finalHtml += '<div class="mr-badge">Final Group: ' + decideGroup(sum, sdr) + '</div>';
        finalHtml += '<div class="mr-meta">totalDimRes: ' + safeNum(sum.res) + ' | threshold: ' + (sum.threshold != null ? sum.threshold : '-') + ' | Social: ' + (sdr || '-') + '</div>';
        $('#mr-final').html(finalHtml);
    } catch(e){}

    // Personal details (two columns)
    try {
        var vm = buildViewMeta(Data);
        var right = '';
        right += colItem('שם פרטי', vm.firstName) + colItem('שם משפחה', vm.lastName) + colItem('ת.ז', vm.idNumber) + colItem('תאריך', vm.date);
        var left = '';
        left += colItem('חברה', vm.companyName) + colItem('מחלקה', vm.divisionName) + colItem('קבוצת שיוך', vm.routeName);
        $('#mr-details').html('<div>' + right + '</div><div>' + left + '</div>');
    } catch(e){}

    // Summary cards
    try {
        var cards = [
            { key:'dim_sigSum', title:'SigTotal — ציון סגנונות' },
            { key:'dim_MAZ', title:'MAZ — ציון מצבים' },
            { key:'dim_sumTotal', title:'SumTotal — ציון סופי' }
        ];
        var html = '';
        for (var i=0;i<cards.length;i++) {
            var c = cards[i]; var dim = Data.Dims[c.key] || {}; var meta = findMeta(Data, c.key);
            html += summaryCard(c.title, dim, meta);
        }
        $('#mr-summaries').html(html);
    } catch(e){}

    // Dimension cards
    try {
        var exclude = { 'dim_sigSum':1, 'dim_MAZ':1, 'dim_sumTotal':1 };
        var orderPref = ['dim_mnipulation','dim_adishut','dim_impulsive','dim_unResponsible','dim_halaklak'];
        var keys = [];
        for (var dk in Data.Dims) { if (!exclude[dk]) keys.push(dk); }
        // reorder by preferred list first
        keys.sort(function(a,b){
            var ia = orderPref.indexOf(a), ib = orderPref.indexOf(b);
            if (ia === -1 && ib === -1) return a.localeCompare(b);
            if (ia === -1) return 1;
            if (ib === -1) return -1;
            return ia - ib;
        });
        var grid = '';
        for (var j=0;j<keys.length;j++){
            var k = keys[j]; var d = Data.Dims[k] || {}; var m = findMeta(Data, k);
            grid += dimCard(nameFromKey(k), d, m);
        }
        $('#mr-grid').html(grid);
    } catch(e){}

    // Dev-only panel
    try {
        if (window.DEBUG_MODE) {
            buildDevPanel(Data);
            $('#mr-dev').show();
        }
    } catch(e){}
}

function nameFromKey(k){
    var map = {
        'dim_mnipulation':'מניפולטיביות',
        'dim_adishut':'אדישות',
        'dim_impulsive':'אימפולסיביות',
        'dim_unResponsible':'חוסר אחריות',
        'dim_halaklak':'חלקלקות בין‑אישית',
        'dim_sigSum':'SigTotal',
        'dim_MAZ':'MAZ',
        'dim_sumTotal':'SumTotal'
    };
    return map[k] || k;
}

function findMeta(Data, key){
    var meta = Data.DimMeta || {};
    if (meta[key]) return meta[key];
    var parts = key.split('_');
    for (var mk in meta){ if (mk.endsWith(parts[1])) return meta[mk]; }
    return {};
}

function buildViewMeta(Data){
    var vm = Data.ViewMeta || {};
    if (!vm.firstName) vm.firstName = getPD(Data, 'PD_firstName');
    if (!vm.lastName) vm.lastName = getPD(Data, 'PD_lastName');
    if (!vm.idNumber) vm.idNumber = getPD(Data, 'PD_IDNumber');
    if (!vm.date) vm.date = getPD(Data, 'PD_date');
    if (!vm.routeName) vm.routeName = getPD(Data, 'PD_routeName');
    // Optional fallbacks for org names if available in PD
    if (!vm.companyName) vm.companyName = getPD(Data, 'PD_companyName') || getPD(Data, 'PD_company');
    if (!vm.divisionName) vm.divisionName = getPD(Data, 'PD_divisionName') || getPD(Data, 'PD_division');
    return vm;
}

function getPD(Data, key){
    try {
        var v = Data.PD && Data.PD[key];
        if (!v) return '';
        return String(v).split(';')[1] || '';
    } catch(e){ return ''; }
}

function colItem(label, value){
    return '<div class="mr-item"><div class="mr-label">'+(label||'')+'</div><div class="mr-value">'+(value||'')+'</div></div>';
}

function summaryCard(title, dim, meta){
    var z = parseFloat(dim.res); if (isNaN(z)) z = 0;
    var t = meta && meta.threshold != null ? parseFloat(meta.threshold) : 1.0;
    return '<div class="mr-summary">'
        + '<div class="title">'+title+'</div>'
        + gaugeHtml(z, t)
        + '<div class="mr-meta">Z: '+safeNum(dim.res)+' | נורמה: avg '+safeNum(meta.average)+' σ '+safeNum(meta.standardDeviation)+' | threshold: '+(meta.threshold!=null?meta.threshold:'-')+'</div>'
        + '</div>';
}

function dimCard(name, dim, meta){
    var z = parseFloat(dim.res); if (isNaN(z)) z = 0;
    var t = meta && meta.threshold != null ? parseFloat(meta.threshold) : 1.0;
    var status = Math.abs(z) <= t ? 'mr-ok' : 'mr-warn';
    return '<div class="mr-card">'
        + '<div class="name">'+name+'</div>'
        + gaugeHtml(z, t)
        + '<div class="meta">Z: '+safeNum(dim.res)+' | נורמה: avg '+safeNum(meta.average)+' σ '+safeNum(meta.standardDeviation)+' | threshold: '+(meta.threshold!=null?meta.threshold:'-')+' | מצב: <span class="'+status+'">'+(status==='mr-ok'?'OK':'WARN')+'</span></div>'
        + '</div>';
}

function gaugeHtml(z, t){
    if (z < -3) z = -3; if (z > 3) z = 3;
    var pos = (z + 3) / 6 * 100;
    var bar = '<div class="mr-bar" style="background:linear-gradient(90deg,#f8d7da 0% 16.667%, #fff3cd 16.667% 33.333%, #e8f5e9 33.333% 66.667%, #fff3cd 66.667% 83.333%, #f8d7da 83.333% 100%);">'
        + '<div class="mr-tick" style="left:16.667%"></div>'
        + '<div class="mr-tick" style="left:33.333%"></div>'
        + '<div class="mr-tick" style="left:66.667%"></div>'
        + '<div class="mr-tick" style="left:83.333%"></div>'
        + '<div class="mr-marker" style="left:'+pos+'%"></div>'
        + '<div class="mr-z" style="left:'+pos+'%">|Z|='+(Math.abs(z)).toFixed(2)+'σ</div>'
        + '</div>'
        + '<div class="mr-scale"><span>-3</span><span>-2</span><span>-1</span><span>0</span><span>+1</span><span>+2</span><span>+3</span></div>';
    return bar;
}

function safeNum(n){ return (n==null || n==='') ? '-' : parseFloat(n).toFixed(2); }

function decideGroup(sum, sdr){
    try {
        var z = parseFloat(sum.res); var thr = parseFloat(sum.threshold); var sd = (sdr||'').toLowerCase();
        if (sd && (sd==='false' || sd==='yes')) {
            if (isNaN(z) || isNaN(thr)) return '-';
            return z <= thr ? 2 : 3;
        } else { return 1; }
    } catch(e){ return '-'; }
}

function buildDevPanel(Data){
    var meta = Data.DimMeta || {};
    // Meta table
    var metaHtml = '<table class="table table-bordered"><thead><tr><th>Dim</th><th>avg</th><th>σ</th><th>threshold</th></tr></thead><tbody>';
    for (var k in meta){
        var m = meta[k]||{};
        metaHtml += '<tr><td>'+k+'</td><td>'+safeNum(m.average)+'</td><td>'+safeNum(m.standardDeviation)+'</td><td>'+(m.threshold!=null?m.threshold:'-')+'</td></tr>';
    }
    metaHtml += '</tbody></table>';
    $('#mr-dev-meta').html(metaHtml);

    // Per-dimension values
    var dimsHtml = '<table class="table table-bordered"><thead><tr><th>Dim</th><th>Z</th><th>|Z|σ</th><th>threshold</th><th>status</th></tr></thead><tbody>';
    for (var dk in Data.Dims){
        var d = Data.Dims[dk]||{}; var m2 = findMeta(Data, dk);
        var z = parseFloat(d.res); if (isNaN(z)) z = 0;
        var t = m2 && m2.threshold != null ? parseFloat(m2.threshold) : 1.0;
        var status = Math.abs(z) <= t ? 'OK' : 'WARN';
        dimsHtml += '<tr><td>'+dk+'</td><td>'+safeNum(d.res)+'</td><td>'+Math.abs(z).toFixed(2)+'</td><td>'+(m2.threshold!=null?m2.threshold:'-')+'</td><td>'+status+'</td></tr>';
    }
    dimsHtml += '</tbody></table>';
    $('#mr-dev-dims').html(dimsHtml);
}