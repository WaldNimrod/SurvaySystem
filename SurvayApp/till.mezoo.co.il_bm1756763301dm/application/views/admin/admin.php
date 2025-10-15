<div class="row" id="search">
    <div class="text-center"><h2>Grahpic Area</h2></div>

    <a href="<?php echo fix_link(site_url('welcome/logout')); ?>" class="pull-right">Logout</a>

    <!--<a href="javascript:void(0);" onclick="$('#changePasswordModal').modal('show');" class="pull-right"
       style="margin-right:20px;">Change Password</a>-->

    <br/><br/>

    <form action="" method="GET">
        <div class="col-sm-3">
            <input type="text" name="daterange" value="<?php echo @$_GET['daterange']; ?>" class="form-control"/>
        </div>
        <div class="col-sm-3">
            <input type="text" name="freetext" placeholder="Freetext" value="<?php echo @$_GET['freetext']; ?>"
                   name="text" class="form-control"/>
        </div>
        <div class="col-sm-3">
            <select name="socialDes" class="form-control">
                <option value="">All Social Desirability</option>
                <option value="false" <?php if (@$_GET['socialDes'] === 'false'){ ?>selected<?php } ?>>False</option>
                <option value="yes" <?php if (@$_GET['socialDes'] === 'yes'){ ?>selected<?php } ?>>Yes</option>
                <option value="double" <?php if (@$_GET['socialDes'] === 'double'){ ?>selected<?php } ?>>Double</option>
            </select>
        </div>

        <div class="col-sm-3">
            <input type="submit" value="Search" class="btn btn-success form-control"/>
        </div>
        <br/><br/>
        <?php
        if ($data['admin']) {
            ?>
            <div class="col-sm-3">
                <select name="company" class="form-control">
                    <option value="">All Companies</option>
                    <?php foreach ($data['companies'] as $company) { ?>
                        <option <?php if (@$_GET['company'] === $company['id']){ ?>selected<?php } ?>
                        value="<?php echo $company['id']; ?>"><?php echo $company['login']; ?></option><?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-6">
                        <select name="division" class="form-control">
                            <option value="">All Divisions</option>
                            <?php foreach ($data['divisions'] as $division) { ?>
                                <option  <?php if (@$_GET['division'] === $division['id']){ ?>selected<?php } ?>
                                value="<?php echo $division['id']; ?>"><?php echo $division['name']; ?></option><?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <select name="dataGroup" class="form-control">
                            <option value="">All Data Groups</option>
                            <?php foreach ($data['dataGroups'] as $dataGroup) { ?>
                                <option <?php if (@$_GET['dataGroup'] === $dataGroup['id']){ ?>selected<?php } ?>
                                value="<?php echo $dataGroup['id']; ?>"><?php echo $dataGroup['attrGroupId']; ?></option><?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="col-sm-3">
                <select name="division" class="form-control">
                    <option value="">All Divisions</option>
                    <?php foreach ($data['divisions'] as $division) { ?>
                        <option  <?php if (@$_GET['division'] === $division['id']){ ?>selected<?php } ?>
                        value="<?php echo $division['id']; ?>"><?php echo $division['name']; ?></option><?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <select name="dataGroup" class="form-control">
                    <option value="">All Data Groups</option>
                    <?php foreach ($data['dataGroups'] as $dataGroup) { ?>
                        <option <?php if (@$_GET['dataGroup'] === $dataGroup['id']){ ?>selected<?php } ?>
                        value="<?php echo $dataGroup['id']; ?>"><?php echo $dataGroup['attrGroupId']; ?></option><?php } ?>
                </select>
            </div>
            <?php
        }
        ?>
        <div class="col-sm-3">
            <select name="finalGroup" class="form-control">
                <option value="">All Final Group</option>
                <option value="1" <?php if (@$_GET['finalGroup'] === '1'){ ?>selected<?php } ?>>1</option>
                <option value="2" <?php if (@$_GET['finalGroup'] === '2'){ ?>selected<?php } ?>>2</option>
                <option value="3" <?php if (@$_GET['finalGroup'] === '3'){ ?>selected<?php } ?>>3</option>
            </select>
        </div>

        <div class="col-sm-3">
            <div class="row">
                <div class="col-sm-6">
                    <input type="number" min="1" max="1000" name="perPage" placeholder="Rows/page"
                           value="<?php echo @$_GET['perPage'] ?: 100; ?>" class="form-control"/>
                </div>
                <div class="col-sm-6">
                    <input type="number" min="1" name="page" placeholder="Page"
                           value="<?php echo @$_GET['page'] ?: 1; ?>" class="form-control"/>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="row">
                <div class="col-sm-6">
                    <a href="<?php echo fix_link(site_url('welcome/export')); ?>?daterange=<?php echo $this->input->get('daterange'); ?>&freetext=<?php echo @$_GET["freetext"];?>&socialDes=<?php echo @$_GET["socialDes"];?>&company=<?php echo @$_GET["company"];?>&division=<?php echo @$_GET["division"];?>&dataGroup=<?php echo @$_GET["dataGroup"];?>&finalGroup=<?php echo @$_GET["finalGroup"];?>&sortKey=<?php echo @$_GET["sortKey"];?>&sortOrder=<?php echo @$_GET["sortOrder"];?>"
                       class="btn btn-danger form-control">Export
                  </a>
                </div>
                <div class="col-sm-6">
                    <?php $distUrlBase = fix_link(site_url('welcome/distribution')); ?>
                    <a href="<?php echo $distUrlBase; ?>?<?php echo http_build_query($_GET); ?>"
                       class="btn btn-warning form-control">Distribution report</a>
                </div>
            </div>
            <div class="row" style="margin-top:6px;">
                <div class="col-sm-6">
                    <a href="<?php echo fix_link(site_url('welcome/admin')); ?>"
                       class="btn btn-info form-control">Clear</a>
                </div>
                <div class="col-sm-6">
                    <?php $distExportBase = fix_link(site_url('welcome/distribution_export')); ?>
                    <a href="<?php echo $distExportBase; ?>?<?php echo http_build_query($_GET); ?>"
                       class="btn btn-default form-control">Distribution CSV</a>
                </div>
            </div>
        </div>
        <input type="hidden" name="sortKey" value="<?php echo @$_GET['sortKey']; ?>"/>
        <input type="hidden" name="sortOrder" value="<?php echo @$_GET['sortOrder']; ?>"/>
    </form>
</div>
<table class="table table-striped table-hover header-fixed <?php if ($data['admin']) { ?>admin<?php } ?>">
    <thead id="thead" style="min-height:37px;">
    <tr>
        <th onclick="sort('feedbackId')" class="sortable">Id <?php if ($_GET['sortKey'] === 'feedbackId') { ?><span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <?php if ($data['admin']) { ?>
            <th>Company</th><?php } ?>
        <th onclick="sort('divisionName')" class="sortable">Division <?php if ($_GET['sortKey'] === 'divisionName') { ?>
            <span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th onclick="sort('firstName')" class="sortable">First name <?php if ($_GET['sortKey'] === 'firstName') { ?>
            <span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th onclick="sort('lastName')" class="sortable">Last name <?php if ($_GET['sortKey'] === 'lastName') { ?><span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th onclick="sort('candidateId')" class="sortable">
            CandidateID <?php if ($_GET['sortKey'] === 'candidateId') { ?><span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th onclick="sort('idNumber')" class="sortable">ID Number <?php if ($_GET['sortKey'] === 'idNumber') { ?><span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th onclick="sort('created')" class="sortable">Created At <?php if ($_GET['sortKey'] === 'created') { ?><span
                class="glyphicon glyphicon-triangle-<?php echo $_GET['sortOrder'] === 'desc' ? 'bottom' : 'top'; ?>"></span><?php } ?>
        </th>
        <th>Remarks</th>
    </tr>
    </thead>
    <tbody class="initial">
    <?php
    foreach ($data['results'] as $result) {
        ?>
        <tr id="row<?php echo $result['feedbackId']; ?>"
            data-data="<?php echo fix_link(site_url('welcome/generate/'.$result['feedbackId']) . '?json=' . urlencode($result['json'])); ?>">
            <td class="clickable">
                <?php echo $result['feedbackId']; ?>
                <a class="downloadable"
                   href="<?php echo fix_link(site_url('welcome/generate/'.$result['feedbackId']) . '?d=1&fileName='.urlencode($result['fileName']).'&json=' . urlencode($result['json'])); ?>"><span
                        class="glyphicon glyphicon-download"></span></a>
                <?php if (!empty($data['appDev'])) { ?>
                    <a class="btn btn-xs btn-default" target="_blank" style="margin-right:6px;"
                       href="<?php echo fix_link(site_url('welcome/recalc/'.$result['feedbackId'])); ?>" onclick="event.stopPropagation();">Recalc (dev)</a>
                <?php } ?>
            </td>
            <?php if ($data['admin']) { ?>
                <td class="clickable"><?php echo $result['companyName']; ?></td><?php } ?>
            <td class="clickable"><?php echo $result['divisionName']; ?></td>
            <td class="clickable"><?php echo $result['firstName']; ?></td>
            <td class="clickable"><?php echo $result['lastName']; ?></td>
            <td class="clickable"><?php echo $result['candidateId']; ?></td>
            <td class="clickable"><?php echo $result['idNumber']; ?></td>
            <td class="clickable"><?php echo @date('d/m/Y', $result['created']); ?></td>
            <td>
                <?php if (!isset($result['remarks'])) { ?>
                    <button type="button" class="btn btn-default"
                            onclick="editRemarks(<?php echo $result['feedbackId']; ?>)">Add Remarks
                    </button>
                <?php } ?>
                <section id="remarks<?php echo $result['feedbackId']; ?>"
                         class="<?php if (!isset($result['remarks'])) { ?>hidden<?php } ?>" data-container="body"
                         data-toggle="popover" data-trigger="hover" data-placement="top"
                         data-content="<?php echo @$result['remarks']; ?>"
                         onclick="editRemarks(<?php echo $result['feedbackId']; ?>)">
                    <?php echo mb_substr($result['remarks'], 0, 80); ?>...
                </section>
                <section class="hidden"
                         id="remarksOriginal<?php echo $result['feedbackId']; ?>"><?php echo mb_substr($result['remarks'],
                        0, 80); ?></section>
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