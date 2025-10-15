<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Distribution Report</title>
    <style>
        :root { --brand:#29a6a8; }
        body { direction: rtl; font-family: Arial, sans-serif; margin: 16px; background:#fff; }
        .container { max-width: 1200px; margin: 0 auto; }
        .toolbar { display: flex; gap: 8px; align-items: center; margin-bottom: 12px; }
        .mr-header { height: 80px; border:2px dashed #e0f2f1; border-radius:8px; background:linear-gradient(90deg, #f7fffe, #ffffff); display:flex; align-items:center; justify-content:center; color:#5aa; margin-bottom:16px; }
        .section { margin: 14px 0; }
        .card { border:2px solid var(--brand); border-radius:12px; padding:12px; background:linear-gradient(180deg,#f7fffe,#ffffff); }
        .card h3 { margin:0 0 8px; color:#0b6; }
        .chart { border:1px solid #eee; border-radius:8px; padding:10px; margin-bottom:8px; background:#fff; }
        .chart h4 { margin:0 0 6px; }
        .grid { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:16px; align-items:start; }
        table { border-collapse: collapse; width: 100%; font-size: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: right; }
        th { background:#f0fdfa; }
        .warn { color:#b45309; }
        .scale { display:flex; justify-content:space-between; font-size:11px; color:#555; margin-top:4px; }
        @media print { .toolbar { display:none; } }
    </style>
</head>
<body>
<div class="container" id="dist-root">
    <div class="toolbar">
        <button onclick="window.print()">הדפסה</button>
        <a id="exportLink" class="btn" href="#">ייצוא לאקסל</a>
        <span id="metaWarn" class="warn" style="display:none;"></span>
        <span style="margin-right:auto"></span>
        <label>דימנשיה: <select id="dimSelect"></select></label>
    </div>

    <div class="mr-header">כותרת גרפית / לוגו</div>

    <div class="section card" id="filtersCard">
        <h3>פרטי סינון</h3>
        <div id="filtersBody" style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px 16px;"></div>
    </div>

    <div class="section card">
        <h3>Overall</h3>
        <div id="overallPanel" class="grid"></div>
    </div>
    <div class="section card">
        <h3>Final Group</h3>
        <div id="fgPanel" class="grid"></div>
    </div>
    <div class="section card">
        <h3>Company</h3>
        <div id="companyPanel" class="grid"></div>
    </div>
    <div class="section card">
        <h3>Division</h3>
        <div id="divisionPanel" class="grid"></div>
    </div>
</div>

<script type="application/json" id="dist-data"><?php echo $data; ?></script>
<script>
(function(){
    var payload = {};
    try { payload = JSON.parse(document.getElementById('dist-data').textContent || '{}'); } catch(e){ payload = {}; }
    var dims = payload.dims || [];
    var meta = payload.meta || {}; var bins = meta.bins || 12; var range = meta.range || [-3,3];
    var qs = location.search || '';
    var exportUrl = '<?php echo fix_link(site_url('welcome/distribution_export')); ?>' + qs;
    document.getElementById('exportLink').setAttribute('href', exportUrl);

    if (meta.truncated) {
        var w = document.getElementById('metaWarn');
        w.style.display = ''; w.textContent = 'שים לב: התוצאה קוצצה ל-' + (meta.rowsUsed||0) + ' מתוך ' + (meta.total||0);
    }

    // Fill filters card
    var fb = document.getElementById('filtersBody');
    var params = new URLSearchParams(location.search);
    var kv = [
        ['טווח תאריכים', params.get('daterange')||'-'],
        ['חיפוש חופשי', params.get('freetext')||'-'],
        ['SocialDes', params.get('socialDes')||'-'],
        ['Company', params.get('company')||'-'],
        ['Division', params.get('division')||'-'],
        ['FinalGroup', params.get('finalGroup')||'-'],
        ['Rows used', (meta.rowsUsed||0) + ' / ' + (meta.total||0)],
        ['Truncated', meta.truncated ? 'כן' : 'לא']
    ];
    kv.forEach(function(p){ var d=document.createElement('div'); d.innerHTML='<strong>'+p[0]+':</strong> '+p[1]; fb.appendChild(d); });

    // Dimension selector
    var dimSel = document.getElementById('dimSelect');
    dims.forEach(function(d){ var o=document.createElement('option'); o.value=d; o.textContent=d; dimSel.appendChild(o); });
    dimSel.onchange = renderAll; if (dims.length) dimSel.value = dims[0];

    function renderLineChartWithTable(container, counts, title) {
        var wrap = document.createElement('div'); wrap.className='chart';
        var h4 = document.createElement('h4'); h4.textContent = title; wrap.appendChild(h4);
        var max = 0, sum=0; for (var i=0;i<bins;i++){ var v=(counts[i]||0); if (v>max) max=v; sum+=v; }
        var width = 980, height = 220, pad = 28;
        var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('viewBox', '0 0 '+width+' '+height);
        svg.setAttribute('width', '100%'); svg.setAttribute('height', height);
        var innerW = width - pad*2, innerH = height - pad*2;
        // grid lines
        for (var g=0; g<=4; g++){
            var gy = pad + (innerH * g/4);
            var gl = document.createElementNS('http://www.w3.org/2000/svg','line');
            gl.setAttribute('x1', pad); gl.setAttribute('y1', gy);
            gl.setAttribute('x2', pad+innerW); gl.setAttribute('y2', gy);
            gl.setAttribute('stroke', '#eee'); svg.appendChild(gl);
        }
        // polyline points
        var pts=[];
        for (var i=0;i<bins;i++){
            var x = pad + (i * (innerW / (bins-1)));
            var val = counts[i]||0;
            var y = pad + (innerH - (max>0 ? (val/max)*innerH : 0));
            pts.push(x+","+y);
            // point circle
            var c = document.createElementNS('http://www.w3.org/2000/svg','circle');
            c.setAttribute('cx', x); c.setAttribute('cy', y); c.setAttribute('r', 2.5);
            c.setAttribute('fill', '#29a6a8'); svg.appendChild(c);
        }
        var pl = document.createElementNS('http://www.w3.org/2000/svg','polyline');
        pl.setAttribute('points', pts.join(' ')); pl.setAttribute('fill','none'); pl.setAttribute('stroke','#29a6a8'); pl.setAttribute('stroke-width','2');
        svg.appendChild(pl);
        // axis labels as scale
        var scale = document.createElement('div'); scale.className='scale';
        var ticks = [-3,-2,-1,0,1,2,3];
        ticks.forEach(function(t){ var s=document.createElement('span'); s.textContent = (t>0? '+'+t : t); scale.appendChild(s); });
        wrap.appendChild(svg); wrap.appendChild(scale);

        // numeric table
        var tbl = document.createElement('table');
        var thead = document.createElement('thead'); var thr = document.createElement('tr');
        ['#','Bin start','Bin end','Count','Percent'].forEach(function(h){ var th=document.createElement('th'); th.textContent=h; thr.appendChild(th); });
        thead.appendChild(thr); tbl.appendChild(thead);
        var tb = document.createElement('tbody');
        var binWidth = (range[1]-range[0]) / bins;
        for (var i=0;i<bins;i++){
            var tr = document.createElement('tr');
            var cval = counts[i]||0; var pct = sum>0 ? ((cval/sum)*100).toFixed(2) : '0.00';
            var cells = [i, (range[0]+i*binWidth).toFixed(3), (range[0]+(i+1)*binWidth).toFixed(3), cval, pct+'%'];
            cells.forEach(function(v){ var td=document.createElement('td'); td.textContent=v; tr.appendChild(td); });
            tb.appendChild(tr);
        }
        tbl.appendChild(tb); wrap.appendChild(tbl);
        container.appendChild(wrap);
    }

    function renderOverall(dim){
        var cont = document.getElementById('overallPanel'); cont.innerHTML='';
        var map = (payload.overall||{})[dim] || []; renderLineChartWithTable(cont, map, 'Overall: ' + dim);
    }
    function renderFG(dim){
        var cont = document.getElementById('fgPanel'); cont.innerHTML='';
        ['1','2','3'].forEach(function(fg){
            var map = ((payload.byFinalGroup||{})[fg]||{})[dim] || [];
            var div = document.createElement('div');
            renderLineChartWithTable(div, map, 'Final Group ' + fg + ': ' + dim);
            cont.appendChild(div);
        });
    }
    function renderCompany(dim){
        var cont = document.getElementById('companyPanel'); cont.innerHTML='';
        var cmp = payload.byCompany || {}; Object.keys(cmp).sort().forEach(function(name){
            var map = (cmp[name]||{})[dim] || [];
            var div = document.createElement('div'); renderLineChartWithTable(div, map, 'Company ' + name + ': ' + dim); cont.appendChild(div);
        });
    }
    function renderDivision(dim){
        var cont = document.getElementById('divisionPanel'); cont.innerHTML='';
        var dv = payload.byDivision || {}; Object.keys(dv).sort().forEach(function(name){
            var map = (dv[name]||{})[dim] || [];
            var div = document.createElement('div'); renderLineChartWithTable(div, map, 'Division ' + name + ': ' + dim); cont.appendChild(div);
        });
    }

    function renderAll(){ var d = dimSel.value; if (!d) return; renderOverall(d); renderFG(d); renderCompany(d); renderDivision(d); }
    renderAll();

    // no tabs – all sections are stacked
})();
</script>
</body>
</html>

