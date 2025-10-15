<div class="row" id="search" style="direction: rtl; text-align: right;">
    <div class="text-center"><h2>אזור גרפי</h2></div>

    <a href="<?php echo fix_link(site_url('welcome/logout')); ?>" class="pull-left">יציאה</a>

    <!--<a href="javascript:void(0);" onclick="$('#changePasswordModal').modal('show');" class="pull-right"
       style="margin-right:20px;">Change Password</a>-->

    <br/><br/>

    <form action="" method="GET">
        <!-- Row 1: ארבעה דרופדאונים: מחלקות, קבוצות נתונים, קבוצות סיווג, רמות כנות -->
        <div class="row">
            <div class="col-sm-3">
                <label>מחלקות</label>
                <select name="division" class="form-control">
                    <option value="">כל המחלקות</option>
                    <?php foreach ($data['divisions'] as $division) { ?>
                        <option  <?php if (@$_GET['division'] === $division['id']){ ?>selected<?php } ?>
                        value="<?php echo $division['id']; ?>"><?php echo $division['name']; ?></option><?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <label>קבוצות נתונים</label>
                <select name="dataGroup" class="form-control">
                    <option value="">כל קבוצות הנתונים</option>
                    <?php foreach ($data['dataGroups'] as $dataGroup) { ?>
                        <option <?php if (@$_GET['dataGroup'] === $dataGroup['id']){ ?>selected<?php } ?>
                        value="<?php echo $dataGroup['id']; ?>"><?php echo $dataGroup['attrGroupId']; ?></option><?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <label>קבוצות סיווג</label>
                <select name="finalGroup" class="form-control">
                    <option value="">כל קבוצות הסיווג</option>
                    <option value="1" <?php if (@$_GET['finalGroup'] === '1'){ ?>selected<?php } ?>>1</option>
                    <option value="2" <?php if (@$_GET['finalGroup'] === '2'){ ?>selected<?php } ?>>2</option>
                    <option value="3" <?php if (@$_GET['finalGroup'] === '3'){ ?>selected<?php } ?>>3</option>
                </select>
            </div>
            <div class="col-sm-3">
                <label>רמות כנות</label>
                <select name="socialDes" class="form-control">
                    <option value="">כל רמות הכנות</option>
                    <option value="false" <?php if (@$_GET['socialDes'] === 'false'){ ?>selected<?php } ?>>False</option>
                    <option value="yes" <?php if (@$_GET['socialDes'] === 'yes'){ ?>selected<?php } ?>>Yes</option>
                    <option value="double" <?php if (@$_GET['socialDes'] === 'double'){ ?>selected<?php } ?>>Double</option>
                </select>
            </div>
        </div>

        <!-- Row 2: תאריכים, חיפוש חופשי, ניקוי, חיפוש -->
        <div class="row" style="margin-top:8px;">
            <div class="col-sm-3">
                <label>טווח תאריכים</label>
                <input type="text" name="daterange" value="<?php echo @$_GET['daterange']; ?>" class="form-control" placeholder="טווח תאריכים"/>
            </div>
            <div class="col-sm-3">
                <label>חיפוש חופשי</label>
                <input type="text" name="freetext" placeholder="חיפוש חופשי" value="<?php echo @$_GET['freetext']; ?>" class="form-control"/>
            </div>
            <div class="col-sm-3">
                <label>&nbsp;</label>
                <a href="<?php echo fix_link(site_url('welcome/admin')); ?>" class="btn btn-info form-control">ניקוי</a>
            </div>
            <div class="col-sm-3">
                <label>&nbsp;</label>
                <input type="submit" value="חיפוש" class="btn btn-success form-control"/>
            </div>
        </div>

        <!-- Separator between filter rows and controls -->
        <div style="border-top: 1px solid #e5e7eb; margin: 8px 0;"></div>

        <!-- Row 3: פקדים -->
        <div class="row" style="margin-top:8px;">
            <div class="col-sm-3">
                <label>שורות לעמוד</label>
                <select name="perPage" class="form-control">
                    <?php foreach ([50,100,200,500,1000] as $opt) { ?>
                        <option value="<?php echo $opt; ?>" <?php if ((int)@$_GET['perPage'] === $opt) { ?>selected<?php } ?>><?php echo $opt; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <label>עמוד</label>
                <select name="page" class="form-control">
                    <?php for ($p=1; $p<=$data['pagination']['pages']; $p++) { ?>
                        <option value="<?php echo $p; ?>" <?php if ((int)@$_GET['page'] === $p) { ?>selected<?php } ?>>עמוד <?php echo $p; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-2">
                <label>&nbsp;</label>
                <?php $distUrlBase = fix_link(site_url('welcome/distribution')); ?>
                <a href="<?php echo $distUrlBase; ?>?<?php echo http_build_query($_GET); ?>" class="btn btn-warning form-control">דוח התפלגות</a>
            </div>
            <div class="col-sm-2">
                <label>&nbsp;</label>
                <?php $distExportBase = fix_link(site_url('welcome/distribution_export')); ?>
                <a href="<?php echo $distExportBase; ?>?<?php echo http_build_query($_GET); ?>" class="btn btn-default form-control">דוח התפלגות CSV</a>
            </div>
            <div class="col-sm-2">
                <label>&nbsp;</label>
                <a href="<?php echo fix_link(site_url('welcome/export')); ?>?daterange=<?php echo $this->input->get('daterange'); ?>&freetext=<?php echo @$_GET["freetext"];?>&socialDes=<?php echo @$_GET["socialDes"];?>&company=<?php echo @$_GET["company"];?>&division=<?php echo @$_GET["division"];?>&dataGroup=<?php echo @$_GET["dataGroup"];?>&finalGroup=<?php echo @$_GET["finalGroup"];?>&sortKey=<?php echo @$_GET["sortKey"];?>&sortOrder=<?php echo @$_GET["sortOrder"];?>" class="btn btn-danger form-control">ייצוא CSV</a>
            </div>
        </div>

        <input type="hidden" name="sortKey" value="<?php echo @$_GET['sortKey']; ?>"/>
        <input type="hidden" name="sortOrder" value="<?php echo @$_GET['sortOrder']; ?>"/>
    </form>
</div>
<table class="table table-striped table-hover header-fixed <?php if ($data['admin']) { ?>admin<?php } ?>" style="direction: rtl; text-align: right;">
    <thead id="thead" style="min-height:37px;">
    <tr>
        <th onclick="sort('feedbackId')" class="sortable">מזהה <?php if ($_GET['sortKey'] === 'feedbackId') { ?><span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <?php if ($data['admin']) { ?>
            <th>חברה</th><?php } ?>
        <th onclick="sort('divisionName')" class="sortable">מחלקה <?php if ($_GET['sortKey'] === 'divisionName') { ?>
            <span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th onclick="sort('firstName')" class="sortable">שם פרטי <?php if ($_GET['sortKey'] === 'firstName') { ?>
            <span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th onclick="sort('lastName')" class="sortable">שם משפחה <?php if ($_GET['sortKey'] === 'lastName') { ?><span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th onclick="sort('candidateId')" class="sortable">
            מזהה מועמד <?php if ($_GET['sortKey'] === 'candidateId') { ?><span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th onclick="sort('idNumber')" class="sortable">תעודת זהות <?php if ($_GET['sortKey'] === 'idNumber') { ?><span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th onclick="sort('created')" class="sortable">נוצר בתאריך <?php if ($_GET['sortKey'] === 'created') { ?><span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th>פעולות / הערות</th>
    </tr>
    </thead>
    <tbody class="initial">
    <?php
    foreach ($data['results'] as $result) {
        ?>
        <tr id="row<?php echo $result['feedbackId']; ?>"
            data-data="<?php echo fix_link(site_url('welcome/generate/'.$result['feedbackId']) . '?json=' . urlencode($result['json'])); ?>">
            <td class="clickable"><?php echo $result['feedbackId']; ?></td>
            <?php if ($data['admin']) { ?>
                <td class="clickable"><?php echo $result['companyName']; ?></td><?php } ?>
            <td class="clickable"><?php echo $result['divisionName']; ?></td>
            <td class="clickable"><?php echo $result['firstName']; ?></td>
            <td class="clickable"><?php echo $result['lastName']; ?></td>
            <td class="clickable"><?php echo $result['candidateId']; ?></td>
            <td class="clickable"><?php echo $result['idNumber']; ?></td>
            <td class="clickable"><?php echo @date('d/m/Y', $result['created']); ?></td>
            <td class="actions-cell">
                <div class="btn-group actions-group" role="group" aria-label="פעולות">
                    <a class="btn btn-xs btn-success" title="הורדה"
                       href="<?php echo fix_link(site_url('welcome/generate/'.$result['feedbackId']) . '?d=1&fileName='.urlencode($result['fileName']).'&json=' . urlencode($result['json'])); ?>" onclick="event.stopPropagation();">
                        <span class="glyphicon glyphicon-download"></span> הורדה
                    </a>
                    <a class="btn btn-xs btn-primary" title="צפייה"
                       href="<?php echo fix_link(site_url('welcome/generate/'.$result['feedbackId']) . '?json=' . urlencode($result['json'])); ?>" onclick="event.stopPropagation();">
                        <span class="glyphicon glyphicon-eye-open"></span> צפייה
                    </a>
                    <?php if (!empty($data['appDev'])) { ?>
                        <a class="btn btn-xs btn-default" target="_blank" title="חישוב"
                           href="<?php echo fix_link(site_url('welcome/recalc/'.$result['feedbackId'])); ?>" onclick="event.stopPropagation();">
                            <span class="glyphicon glyphicon-refresh"></span> חישוב
                        </a>
                    <?php } ?>
                    <button type="button" class="btn btn-xs btn-warning" title="הערות" onclick="event.stopPropagation(); editRemarks(<?php echo $result['feedbackId']; ?>)">
                        <span class="glyphicon glyphicon-comment"></span> הערות
                    </button>
                </div>
                <section id="remarks<?php echo $result['feedbackId']; ?>" class="hidden" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?php echo @$result['remarks']; ?>">
                    <?php echo mb_substr($result['remarks'], 0, 80); ?>...
                </section>
                <section class="hidden" id="remarksOriginal<?php echo $result['feedbackId']; ?>"><?php echo mb_substr($result['remarks'], 0, 80); ?></section>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<div id="footer">
    Total: <?php echo (int)$data['pagination']['total']; ?> | Page <?php echo (int)$data['pagination']['page']; ?> / <?php echo (int)$data['pagination']['pages']; ?>
    <div class="pull-right">
        <?php
        $base = fix_link(site_url('welcome/admin')) . '?';
        $params = $_GET; $params['perPage'] = $data['pagination']['perPage'];
        $prev = $data['pagination']['page'] > 1 ? $data['pagination']['page'] - 1 : 1;
        $next = $data['pagination']['page'] < $data['pagination']['pages'] ? $data['pagination']['page'] + 1 : $data['pagination']['pages'];
        $params['page'] = $prev; $prevUrl = $base . http_build_query($params);
        $params['page'] = $next; $nextUrl = $base . http_build_query($params);
        ?>
        <a class="btn btn-default btn-xs" href="<?php echo $prevUrl; ?>">Prev</a>
        <a class="btn btn-default btn-xs" href="<?php echo $nextUrl; ?>">Next</a>
    </div>
</div>