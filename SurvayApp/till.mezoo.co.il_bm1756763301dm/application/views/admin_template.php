<html dir="rtl" lang="he">
<head>
    <title>מערכת סקרים - ממשק ניהול</title>
    <!-- Include Required Prerequisites (pinned versions) -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"/>

    <!-- Include Date Range Picker -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap-daterangepicker@2.1.27/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-daterangepicker@2.1.27/daterangepicker.css"/>
    <script>
        $(function () {
            $('input[name="daterange"]').daterangepicker({
                "startDate": "<?php $e1 = explode(' - ', @$_GET['daterange']); echo(@$_GET['daterange'] ? $e1[0] : @date("d-m-y", strtotime("-7 day"))); ?>",
                "endDate": "<?php echo(@$_GET['daterange'] ? $e1[1] : @date("d-m-y")); ?>",
                locale: {
                    format: 'DD.MM.YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'מתחילת השנה': [moment().startOf('year'), moment()],
                    'שנה שעברה': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                }
            });

            var calc = $(document).outerHeight() - ($('#thead').height() + $('#footer').height() + $('#search').height()) - 1;
            $('.header-fixed > tbody').removeClass('initial');

            $('.header-fixed > tbody').css({'max-height': calc + 'px', 'overflow-y': 'auto'});


            $(document).ready(function () {
                $('[data-toggle="popover"]').popover({html: true});
                $('.clickable').click(function (element) {
                    var url = $(element.target).parent().attr('data-data');
                    console.log(url)
                    if(!url){

                        url = $(element.target).parent().attr('href')

                    }

                    // Avoid popup blocks: navigate in same tab
                    window.location.href = url;

                })
                $('.downloadable').click(function (element) {
                    event.preventDefault();

                })
                <?php

       if (isset($lastPasswordChange) && $lastPasswordChange !== 0 && $lastPasswordChange + (60*60*24*30*6) < time()) {
           ?>$('#changePasswordModal').modal('show');<?php
                }
                ?>
            });
        });

        function sort(sortKey) {
            var oldSortKey = $('input[name=sortKey]').val();
            console.log(oldSortKey, sortKey)
            $('input[name=sortKey]').val(sortKey);
            if (sortKey === oldSortKey) {
                if ($('input[name=sortOrder]').val() === 'desc') {
                    $('input[name=sortOrder]').val('asc');
                } else {
                    if ($('input[name=sortOrder]').val() === 'asc') {
                        $('input[name=sortOrder]').val('desc');
                    }
                }
            }
            $('#search form').submit();
        }

        function editRemarks(feedbackId) {
            window.feedbackId = feedbackId;
            $('[data-toggle="popover"]').popover('hide');

            $('#myModal textarea').val($('#remarksOriginal' + window.feedbackId).html());

            $('#myModal').modal('show');
        }

        function saveRemarks() {

            $.ajax({
                url: "<?php echo fix_link(site_url('welcome/setRemarks'));?>/" + window.feedbackId,
                type: "post",
                data: $('#myModal textarea').val(),
                success: function (response) {


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }


            });
            $('#remarksOriginal' + window.feedbackId).html($('#myModal textarea').val());
            $('#remarks' + window.feedbackId).html($('#myModal textarea').val().substring(0, 80) + '...');
            $('#remarks' + window.feedbackId).removeClass('hidden');
            $('#remarks' + window.feedbackId).attr('data-content', $('#myModal textarea').val());
            $('#remarks' + window.feedbackId).parent().find('button').remove()
        }

        function saveChangePassword() {
            $.ajax({
                url: "<?php echo fix_link(site_url('welcome/changePassword'));?>",
                type: "post",
                data: $('#changePasswordModal input').val(),
                success: function (response) {


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }


            });
        }

    </script>
    <style>
        body { direction: rtl; text-align: right; }
        .header-fixed {
            width: 100%
        }

        /* RTL: הפוך סדר עמודות באזור החיפוש */
        #search .row > [class^="col-"] { float: right; }

        .header-fixed > thead,
        .header-fixed > tbody,
        .header-fixed > thead > tr,
        .header-fixed > tbody > tr,
        .header-fixed > thead > tr > th,
        .header-fixed > tbody > tr > td {
            display: block;
        }

        .header-fixed > tbody > tr:after,
        .header-fixed > thead > tr:after {
            content: ' ';
            display: block;
            visibility: hidden;
            clear: both;
        }

        .initial {
            overflow-y: auto;
            height: 1px;
        }

        .header-fixed > tbody > tr > td,
        .header-fixed > thead > tr > th {
            font-size: 10pt !important;
            float: right;
            text-align: right;
        }

        .header-fixed > thead > tr > th.sortable {
            color: darkblue;
            cursor: pointer;
        }

        /* חלוקת רוחב מדויקת כדי לפנות מקום לפעולות */
        .header-fixed > thead > tr > th,
        .header-fixed > tbody > tr > td {
            width: 10.857%; /* 7 עמודות * 10.857% + 24% (פעולות) = 100% */
        }
        .header-fixed.admin > thead > tr > th,
        .header-fixed.admin > tbody > tr > td {
            width: 9.0% !important; /* 8 עמודות * 9% + 28% (פעולות) = 100% */
        }
        /* עמודת פעולות רחבה יותר */
        .header-fixed > thead > tr > th:last-child,
        .header-fixed > tbody > tr > td:last-child {
            width: 24% !important;
        }
        .header-fixed.admin > thead > tr > th:last-child,
        .header-fixed.admin > tbody > tr > td:last-child {
            width: 28% !important;
        }

        table {
            margin: 0 !important;
        }

        .clickable {
            cursor: pointer;
        }

        /* Actions inline spacing */
        .actions-group { display: inline-flex; gap: 6px; }
        .actions-group .btn { padding-left: 8px; padding-right: 8px; }
        .actions-cell { white-space: nowrap; }
    </style>
</head>
<body>
<div class="container">
    <?php $this->load->view('partials/top_header'); ?>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <textarea class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="saveRemarks()">Save
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    New password:
                    <input type="text" name="password" id="password" class="form-control"></input>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="saveChangePassword()">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
    $this->load->view($view);
    ?>

</div>
</body>
</html>