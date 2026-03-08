@extends('layouts.header2')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
    <link
        href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/css/style_new.css') }}?v={{ rand(1, 999999) }}">
    <style>
        .titillium-web-extralight {
            font-family: "Titillium Web", sans-serif;
            font-weight: 200;
            font-style: normal;
        }

        .titillium-web-light {
            font-family: "Titillium Web", sans-serif;
            font-weight: 300;
            font-style: normal;
        }

        .titillium-web-regular {
            font-family: "Titillium Web", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .titillium-web-semibold {
            font-family: "Titillium Web", sans-serif;
            font-weight: 600;
            font-style: normal;
        }

        .titillium-web-bold {
            font-family: "Titillium Web", sans-serif;
            font-weight: 700;
            font-style: normal;
        }

        .titillium-web-black {
            font-family: "Titillium Web", sans-serif;
            font-weight: 900;
            font-style: normal;
        }

        .titillium-web-extralight-italic {
            font-family: "Titillium Web", sans-serif;
            font-weight: 200;
            font-style: italic;
        }

        .titillium-web-light-italic {
            font-family: "Titillium Web", sans-serif;
            font-weight: 300;
            font-style: italic;
        }

        .titillium-web-regular-italic {
            font-family: "Titillium Web", sans-serif;
            font-weight: 400;
            font-style: italic;
        }

        .titillium-web-semibold-italic {
            font-family: "Titillium Web", sans-serif;
            font-weight: 600;
            font-style: italic;
        }

        .titillium-web-bold-italic {
            font-family: "Titillium Web", sans-serif;
            font-weight: 700;
            font-style: italic;
        }

        .asset-checkbox:checked {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .asset-checkbox:checked {
            accent-color: black;
        }
    </style>
    <?php
    $userAccess = explode(',', @Auth::user()->access_to_client);
    $limit = 10;
    
    $no_check = DB::Table('settings')->where('user_id', Auth::id())->first();
    if (isset($_GET['limit']) && $_GET['limit'] != '') {
        $limit = $_GET['limit'];
    
        if ($no_check != '') {
            DB::table('settings')
                ->where('user_id', Auth::id())
                ->update(['clients' => $limit]);
        } else {
            DB::table('settings')->insert(['user_id' => Auth::id(), 'clients' => $limit]);
        }
    } else {
        if ($no_check != '') {
            if ($no_check->contract != '') {
                $limit = $no_check->contract;
            }
        }
    }
    
    $orderby = 'desc';
    $field = 'id';
    if (sizeof($_GET) > 0) {
        if (isset($_GET['orderBy'])) {
            $orderby = $_GET['orderBy'];
            $field = $_GET['field'];
        }
    
        $cond = '';
    
        if (isset($_GET['advance_search'])) {
            if (isset($_GET['client_id']) && $_GET['client_id'] != '') {
                $client_id = $_GET['client_id'];
                $cond .= " and a.client_id ='$client_id'";
            }
    
            if (isset($_GET['has_attachment']) && $_GET['has_attachment'] != '') {
                $attachment = $_GET['has_attachment'];
                if ($attachment == 1) {
                    $cond .= " and a.attachment!='' ";
                } elseif ($attachment == 0) {
                    $cond .= ' and a.attachment is null ';
                }
            }
    
            if (isset($_GET['comments']) && $_GET['comments'] != '') {
                $comments = $_GET['comments'];
                $cond .= " and a.comments like '%$comments%'";
            }
            if (isset($_GET['contract_type']) && $_GET['contract_type'] != '') {
                $contract_type = $_GET['contract_type'];
                $cond .= " and a.contract_type like '%$contract_type%'";
            }
        }
    
        $sear = @$_GET['search'];
        $statusFilter = request('status');
        $monthFilter = request('vacation_month');
        $yearFilter = request('vacation_year');
        $requiresPlanning = request('requires_planning');
        $plannedStatus = request('planned_status');
        $monthStart = null;
        $monthEnd = null;

        if (!empty($monthFilter) && !empty($yearFilter)) {
            $monthValue = str_pad($monthFilter, 2, '0', STR_PAD_LEFT);
            $monthStart = $yearFilter . '-' . $monthValue . '-01';
            $monthEnd = date('Y-m-t', strtotime($monthStart));
        }

        $startExpr = "STR_TO_DATE(SUBSTRING_INDEX(date_range, ' to ', 1), '%d-%b-%Y')";
        $endExpr = "STR_TO_DATE(SUBSTRING_INDEX(date_range, ' to ', -1), '%d-%b-%Y')";
        $today = date('Y-m-d');
    
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'staff') {
            $qry = DB::table('student_vacations')
                ->where(function ($query) {
                    $query->Orwhere('student_name', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('date_range', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('comment', 'like', '%' . @$_GET['search'] . '%');
                })
                ->when(!empty($statusFilter) && strtolower($statusFilter) !== 'all', function ($q) use ($statusFilter, $startExpr, $endExpr, $today) {
                    $status = strtolower($statusFilter);
                    if ($status === 'active') {
                        $q->whereRaw("($startExpr <= ? AND $endExpr >= ?)", [$today, $today]);
                    } elseif ($status === 'upcoming') {
                        $q->whereRaw("$startExpr > ?", [$today]);
                    } elseif ($status === 'inactive') {
                        $q->whereRaw("($endExpr < ? OR date_range IS NULL OR date_range = '')", [$today]);
                    }
                })
                ->when(!empty($monthStart) && !empty($monthEnd), function ($q) use ($startExpr, $endExpr, $monthStart, $monthEnd) {
                    $q->whereRaw("($startExpr <= ? AND $endExpr >= ?)", [$monthEnd, $monthStart]);
                })
                ->when(!empty($requiresPlanning) && strtolower($requiresPlanning) !== 'all', function ($q) use ($requiresPlanning) {
                    $q->where('take_work_home', strtolower($requiresPlanning) === 'true' ? 1 : 0);
                })
                ->when(!empty($plannedStatus) && strtolower($plannedStatus) !== 'all', function ($q) use ($plannedStatus) {
                    $q->where('planned', strtolower($plannedStatus) === 'true' ? 1 : 0);
                })
                ->orderBy($field, $orderby);
            $totalQuery = clone $qry;
            $totalRows = $totalQuery->select(DB::raw('COUNT(DISTINCT id) as aggregate'))->first()->aggregate;
            $qry = $qry->orderBy($field, $orderby)->paginate($limit);
        } else {
            $qry = DB::table('student_vacations')
                ->where('client_id', Auth::user()->id)
                ->when(!empty($statusFilter) && strtolower($statusFilter) !== 'all', function ($q) use ($statusFilter, $startExpr, $endExpr, $today) {
                    $status = strtolower($statusFilter);
                    if ($status === 'active') {
                        $q->whereRaw("($startExpr <= ? AND $endExpr >= ?)", [$today, $today]);
                    } elseif ($status === 'upcoming') {
                        $q->whereRaw("$startExpr > ?", [$today]);
                    } elseif ($status === 'inactive') {
                        $q->whereRaw("($endExpr < ? OR date_range IS NULL OR date_range = '')", [$today]);
                    }
                })
                ->when(!empty($monthStart) && !empty($monthEnd), function ($q) use ($startExpr, $endExpr, $monthStart, $monthEnd) {
                    $q->whereRaw("($startExpr <= ? AND $endExpr >= ?)", [$monthEnd, $monthStart]);
                })
                ->when(!empty($requiresPlanning) && strtolower($requiresPlanning) !== 'all', function ($q) use ($requiresPlanning) {
                    $q->where('take_work_home', strtolower($requiresPlanning) === 'true' ? 1 : 0);
                })
                ->when(!empty($plannedStatus) && strtolower($plannedStatus) !== 'all', function ($q) use ($plannedStatus) {
                    $q->where('planned', strtolower($plannedStatus) === 'true' ? 1 : 0);
                })
                ->orderBy('id', 'desc');
            $totalQuery = clone $qry;
            $totalRows = $totalQuery->select(DB::raw('COUNT(DISTINCT id) as aggregate'))->first()->aggregate;
            $qry = $qry->orderBy($field, $orderby)->paginate($limit);
        }
    } else {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'staff') {
            $qry = DB::table('student_vacations')->orderBy('id', 'desc');
            $totalQuery = clone $qry;
            $totalRows = $totalQuery->select(DB::raw('COUNT(DISTINCT id) as aggregate'))->first()->aggregate;
            $qry = $qry->orderBy($field, $orderby)->paginate($limit);
        } else {
            $qry = DB::table('student_vacations')
                ->where('client_id', Auth::user()->id)
                ->orderBy('id', 'desc');
            $totalQuery = clone $qry;
            $totalRows = $totalQuery->select(DB::raw('COUNT(DISTINCT id) as aggregate'))->first()->aggregate;
            $qry = $qry->orderBy($field, $orderby)->paginate($limit);
        }
    }
    
    if (isset($_GET['id']) || isset($id)) {
        $GETID = $_GET['id'] ?? $id;
    } else {
        $GETID = @$qry[0]->id;
    }
    ?>
    <style type="text/css">
        #page-header {
            display: none;
        }
    </style>
    <!-- Main Container -->
    <main id="main-container pt-0">
        <!-- Hero -->
        <style type="text/css">
            .dropdown-menu {
                z-index: 100000 !important;
            }

            .pagination {
                margin-bottom: 0px;
            }

            .ActionIcon {
                /*border-radius: 50%;*/
                padding: 6px;
            }

            .ActionIcon:hover {
                /*background: #dadada;*/
            }

            .blockDivs .block-header-default {
                background-color: #f1f3f8;
                padding: 7px 1.25rem;
            }

            .blockDivs {
                border: 1px solid lightgrey;
                margin-bottom: 10px !important;
            }

            .radio_button label,
            .radio_button input {}

            .radio_button {
                float: left;
            }

            .radio_button input[type="radio"] {
                opacity: 0.011;
                z-index: 100;
                position: absolute;
            }

            .radio_button input[type="checkbox"] {
                opacity: 0.011;
                z-index: 100;
                position: absolute;
            }

            .radio_button label {
                width: 110px;
                border-color: #D9D9D9;
                color: #7F7F7F;
                /*font-size: 12pt;*/
            }

            #contractDetailModal .modal-lg {
                max-width: 660px !important;
            }

            .modal-lg,
            .modal-xl {
                max-width: 950px;
            }

            .btn-notify {
                color: #00B0F0;
                font-family: Calibri;
                font-size: 14pt;
                font-weight: bold;
                padding: 5px 13px;
                font-weight: bold;
                border-radius: 7px;
            }

            .btn-link {
                padding: 0px;
                margin: .25rem .5rem;
            }

            .btn-link:hover {
                box-shadow: -1px 2px 4px 3px #99dff9;
                background: #99dff9;
            }

            .btn-notify:hover {
                color: #00B0F0;
                background: #386875;
            }

            .btnDeleteAttachment {
                position: absolute;
                right: 2px;
                top: 6px;
            }

            .attachmentDiv {
                border: 1px solid lightgrey;
                padding: 7px;
                font-size: 10px;
                border-radius: 32px;
                color: grey;
                width: 50px;
            }

            .dropdown-menu {
                border: 1px solid #D4DCEC !important;
                box-sizing: 1px 1px 1pxo #D4DCEC;
                box-shadow: 6px 6px 8px #8f8f8f5e;
                border-radius: 11px;
            }

            .c1 {
                color: #3F3F3F;
                /* font-family: 'Calibri'; */
            }

            .c2 {
                color: #7F7F7F;
                font-family: 'Calibri';
            }

            .c3 {
                color: #595959;
                font-family: 'Calibri';
            }



            .alert-danger {
                width: auto !important;
                padding-right: 70px;
                background-color: #FDF5EE !important;
                border: 2px solid orange !important;
                border-radius: 10px !important;
                color: #36454F !important;
                font-family: 'Titillium Web' !important;
                font-size: 14pt !important;
                padding-top: 14px;
                padding-bottom: 14px;
                z-index: 11000 !important;
            }

            .alert-danger button.close {
                color: #7F7F7F !important;
                top: 16px !important;
            }

            .alert-info,
            .alert {
                width: auto !important;
                padding-right: 70px;
                background-color: #ffffff !important;
                color: #36454F !important;
                font-family: 'Titillium Web' !important;
                font-size: 14pt !important;
                padding-top: 14px;
                padding-bottom: 14px;
                z-index: 11000 !important;
                border: 1px solid #D9D9D9 !important;
            }

            .alert-info .close,
            .alert .close {
                color: #36454F !important;
                font-size: 30px !important;
                opacity: 1 !important;
                font-weight: 200 !important;
                width: 33px;
                top: 11px !important;
            }

            .alert .close:hover {
                font-weight: 500 !important;
            }

            .alert-info .close:hover {
                background-color: #BFBFBF !important;
                border-radius: 50%;
            }

            .alert-info .btn-tooltip {
                color: #00B0F0 !important;
                font-family: Signika !important;
                font-size: 14pt !important;
                font-weight: bold !important;
            }

            .btn-notify {
                color: #00B0F0;
                font-family: Calibri;
                font-size: 14pt;
                font-weight: bold;
                padding: 5px 13px;
                font-weight: bold;
                border-radius: 7px;
            }

            .btn-link {
                padding: 0px;
                margin: .25rem .5rem;
            }

            .btn-link:hover {
                box-shadow: -1px 2px 4px 3px #99dff9;
                background: #99dff9;
            }

            .btn-notify:hover {
                color: #00B0F0;
                background: #386875;
            }

            .btnDeleteAttachment {
                position: absolute;
                right: 2px;
                top: 6px;
            }

            .btnNewAction:hover,
            .btnNewAction1:hover,
            .btnNewAction2:hover {
                background: #59595930;
                border-radius: 50%;
            }

            .HostActive {
                font-family: Calibri;
                font-size: 9pt;
                font-weight: bold;
                color: #74A639;
                letter-spacing: 0px;
            }

            .HostInActive {
                font-family: Calibri;
                font-size: 9pt;
                font-weight: bold;
                color: #C41E3A;
                letter-spacing: 0px;
            }

            @media only print {
                .no-print {
                    display: none !important;
                }

                #showData {
                    height: 100% !important;
                }

                .content {
                    background: #F0F3F8;
                }
            }

            .mainser:hover,
            .mainser:focus {
                max-width: 102%;
            }

            .spinner {
                display: inline-block;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            /* Class to expand the button */
            .expanded {
                padding-right: 40px;
                width: auto;
                transition: width 0.3s ease-in-out;
            }

            .c4 {
                background-color: transparent;
                border: 1px solid #263050;
                color: #3F3F3F !important;
                padding-left: 7px;
                padding-right: 7px;
                font-family: "Titillium Web", sans-serif;
                font-weight: 600;
                font-style: normal;
                border-style: solid;
                border-radius: 5px;
                width: fit-content;
                padding-top: 1px;
                padding-bottom: 1px;
            }

            .font-8pt {
                font-size: 8pt !important;
            }

            .font-9pt {
                font-size: 9pt !important;
            }

            .font-10pt {
                font-size: 10pt !important;
            }

            .tooltip {
                opacity: 1 !important;
                /* ensure your transparency shows */
            }

            .dropdown-menu.show .dropdown-item:hover,
            .dropdown-menu.show .dropdown-item:focus {
                background: rgba(63, 63, 63, 0.08);
                color: #3F3F3F;
            }

            [data-notify-position="bottom-left"].alert-notify-desktop,
            [data-notify-position="bottom-left"].alert-info {
                left: 110px !important;
                padding-top: 12px !important;
                padding-bottom: 12px !important;
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            }

            .vendor-card {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .vendor-img {
                flex: 0 0 16%;
                aspect-ratio: 1 / 1;
                padding: 3px;
            }

            .vendor-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
            }

            .vendor-info {
                flex: 1 1 auto;
                padding: 3px;
                min-width: 0;
                /* allows text truncation */
                overflow: hidden;
            }

            @media (max-width: 1650px) {
                .vendor-img {
                    flex: 0 0 20%;
                    min-width: 50px;
                }
            }

            .tooltip .arrow {
                display: none !important;
            }

            .cards-container {
                /* display: flex; */
            }

            .info-card {
                width: 250px !important;
            }

            .short-cards {
                width: 100% !important;
            }

            .copy-info i {
                transition: opacity 0.2s ease, transform 0.2s ease;
                opacity: 0.8;
                transform: scale(1);
            }

            .copy-info:hover i {
                opacity: 1;
                transform: scale(1.15);
            }

            .clipboard-toast {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                bottom: -16px;
                background: #ffffff;
                font-family: font Titillium;
                right: -12px;
                z-index: 99;
            }

            .distributer-download-toast {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 300px;
            }

            .audit-log-download-toast {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: 4px;
                background: #ffffff;
                font-family: font Titillium;
                right: 6px;
                z-index: 99;
                width: 300px;
            }

            .purchase-download-toast {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 300px;
            }

            .contract-details-download-toast {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 300px;
            }

            .email-toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -17px;
                background: #ffffff;
                font-family: font Titillium;
                right: -17px;
                z-index: 99;
                width: 270px;
            }

            .email-toast-updated {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .email-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .email-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .asset-array-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 0px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -12px;
                z-index: 99;
                width: 270px;
            }

            .asset-array-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 0px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -12px;
                z-index: 99;
                width: 270px;
            }

            .affiliate-toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .affiliate-toast-updated {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .affiliate-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .affiliate-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .distribution-toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .distribution-toast-updated {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .distribution-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .distribution-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .Student-toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .Student-toast-updated {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .Student-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .Student-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .payment-toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .payment-toast-updated {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .payment-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .payment-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .vacation-toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .vacation-toast-updated {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .vacation-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .vacation-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .purchasing-toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .purchasing-toast-updated {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .purchasing-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .purchasing-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .notify-email-toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .notify-email-toast-updated {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .notify-email-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .notify-email-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .contract-toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .contract-assets-toast {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                left: 55px;
                z-index: 99;
                width: 270px;
            }

            .contract-toast-updated {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .contract-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .contract-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .pinned-message-toast-added {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .pinned-message-toast-updated {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .pinned-message-toast-recovered {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .pinned-message-toast-deleted {
                display: none;
                position: absolute;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
                top: -14px;
                background: #ffffff;
                font-family: font Titillium;
                right: -6px;
                z-index: 99;
                width: 270px;
            }

            .btn-undo {
                border-radius: 30px;
                padding: 2px 15px
            }

            .btn-undo:hover {
                background-color: #d8d8d8;
            }



            .contract-table {
                border-collapse: separate !important;
                border-spacing: 0 !important;
            }



            .contract-table tbody tr:hover td,
            .contract-table tbody tr:hover span {
                font-weight: 600 !important;
                color: #0665d0 !important;
                /*border-top: 1px solid #ccccccad !important;*/
                /*border-bottom: 1px solid #ccccccad !important;*/
            }




            .distributer-table {
                border-collapse: separate !important;
                border-spacing: 0 !important;
            }

            /*.distributer-table td {*/
            /*    border-top: 1px solid transparent;*/
            /*    border-bottom: 1px solid transparent;*/
            /*    border-left: 1px solid transparent;*/
            /*    border-right: 1px solid transparent;*/
            /*}*/


            .distributer-table tbody tr:hover td {
                font-weight: 600 !important;
                color: #0665d0 !important;
                /*border-top: 1px solid #ccccccad !important;*/
                /*border-bottom: 1px solid #ccccccad !important;*/
            }

            /* Round corner cells */
            .distributer-table tbody tr:hover td:first-child {
                /*border-top-left-radius: 7px !important;*/
                /*border-bottom-left-radius: 7px !important;*/
                /*border-left: 1px solid #ccccccad !important;*/
            }

            .distributer-table tbody tr:hover td:last-child {
                /*border-top-right-radius: 7px !important;*/
                /*border-bottom-right-radius: 7px !important;*/
                /*border-right: 1px solid #ccccccad !important;*/
            }

            .purchasing-table {
                border-collapse: separate !important;
                border-spacing: 0 !important;
            }

            .purchasing-table td {
                /*border-top: 1px solid transparent;*/
                /*border-bottom: 1px solid transparent;*/
                /*border-left: 1px solid transparent;*/
                /*border-right: 1px solid transparent;*/
            }


            .purchasing-table tbody tr:hover td {
                font-weight: 600 !important;
                color: #0665d0 !important;
                /*border-top: 1px solid #ccccccad !important;*/
                /*border-bottom: 1px solid #ccccccad !important;*/
            }

            /* Round corner cells */
            /*.purchasing-table tbody tr:hover td:first-child {*/
            /*    border-top-left-radius: 7px !important;*/
            /*    border-bottom-left-radius: 7px !important;*/
            /*    border-left: 1px solid #ccccccad !important;*/
            /*}*/

            /*.purchasing-table tbody tr:hover td:last-child {*/
            /*    border-top-right-radius: 7px !important;*/
            /*    border-bottom-right-radius: 7px !important;*/
            /*    border-right: 1px solid #ccccccad !important;*/
            /*}*/
            .distributer-table,
            .purchasing-table {
                table-layout: fixed;
            }

            .distributer-table th,
            .purchasing-table th,
            .distributer-table h6,
            .purchasing-table h6 {
                font-size: 10pt;
            }

            .added-pinned-message {
                border-collapse: separate !important;
                border-spacing: 0 !important;
            }

            .pinned-message-item:hover td {
                border-top: 1px solid #ccccccad !important;
                border-bottom: 1px solid #ccccccad !important;
            }

            .pinned-message-item:hover td:first-child {
                border-top-left-radius: 10px !important;
                border-bottom-left-radius: 10px !important;
                border-left: 1px solid #ccccccad !important;
            }

            .pinned-message-item:hover td:last-child {
                border-top-right-radius: 10px !important;
                border-bottom-right-radius: 10px !important;
                border-right: 1px solid #ccccccad !important;
            }

            .added-notify-email {
                border-collapse: separate !important;
                border-spacing: 0 !important;
            }

            .notify-email-item:hover td {
                border-top: 1px solid #ccccccad !important;
                border-bottom: 1px solid #ccccccad !important;
            }

            .notify-email-item:hover td:first-child {
                border-top-left-radius: 10px !important;
                border-bottom-left-radius: 10px !important;
                border-left: 1px solid #ccccccad !important;
            }

            .notify-email-item:hover td:last-child {
                border-top-right-radius: 10px !important;
                border-bottom-right-radius: 10px !important;
                border-right: 1px solid #ccccccad !important;
            }


            .hidden-section {
                display: none;
            }

            .cards-container {
                display: flex;
                overflow-x: auto;
                scroll-behavior: smooth;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 2px;
                position: relative;
                overflow: hidden;
                margin-top: -6px;
                /* z-index: 1; */
            }

            .cards-container::-webkit-scrollbar {
                display: none;
            }

            .cards-container {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .status-card {
                flex-shrink: 0;
                position: sticky;
                left: 0;
                background: transparent;
                z-index: 10;
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            .info-card {
                flex-shrink: 0;
            }

            .cards-container {
                cursor: grab;
            }

            .cards-container:active {
                cursor: grabbing;
            }

            #showEditData .border-style {
                border-radius: 10px;
                border-color: #BFBFBF !important;
            }

            #showData .border-style {
                border-radius: 10px;
                border-color: #595959;
            }

            #showEditData .border-style.border-hover-comment:hover {
                border: 1px solid !important;
                border-color: #2485E8 !important;
                box-shadow: none !important;
            }

            /* thin vertical divider shown at the right edge of left column */
            .left-vertical-divider {
                /* draw divider on right side */
                box-shadow: inset -1px 0 0 0 #595959;
                /* keep layout consistent (optional) */
                /* make sure it doesn't add width */
                box-sizing: border-box;
            }

            /* alternative using border if you prefer - but box-shadow avoids reflow */
            .left-vertical-divider-border {
                border-right: 1px solid #595959;
            }

            .clipboard-toast-detailline {
                position: absolute;
                background: #ffffff;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                z-index: 9999;
                opacity: 0;
                transform: translateY(-5px);
                transition: all 0.18s ease-in-out;
                pointer-events: none;

                border-radius: 8px;
                padding: 3px 15px;
                white-space: nowrap;
            }

            .clipboard-toast-detailline.show {
                opacity: 1;
                transform: translateY(-10px);
            }

            .dropdown-spacer {
                width: 100%;
                pointer-events: none;
            }

            .truncate-text {
                max-width: 450px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                display: inline-block;
                vertical-align: middle;
            }

            .truncate-desc {
                max-width: 420px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                display: inline-block;
                vertical-align: middle;
            }

            .truncate-pn-no {
                max-width: 165px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                display: inline-block;
                vertical-align: middle;
            }

            /* Tooltip bubble */
            .tooltip-box {
                position: absolute;
                top: 15px;
                left: 50%;
                transform: translateX(-50%);
                background: #fff;
                border-radius: 12px;
                padding: 8px 14px;
                box-shadow: 0 3px 15px rgba(0, 0, 0, 0.2);
                z-index: 200;
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
                width: 500px;
            }

            /* Copy icon inside tooltip */
            .tooltip-copy {
                cursor: pointer;
                /* font-size: 16px;
                                                                                color: #263050; */
            }

            /* Copied bubble */
            .copied-msg {
                position: absolute;
                top: -30px;
                left: 50%;
                transform: translateX(-50%);
                font-size: 12px;
                white-space: nowrap;
                display: none;

                background: #ffffff;
                color: #4EA833 !important;
                border: 1px solid #4EA833;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                z-index: 9999;
                /* transform: translateY(-5px); */
                transition: all 0.18s ease-in-out;
                pointer-events: none;

                border-radius: 8px;
                padding: 3px 15px;
            }

            /* For the main container */
            .flex-grow-1 {
                flex-grow: 1;
            }

            /* For the title */
            .header-item-code.text-ellipsis {
                white-space: nowrap;
                text-overflow: ellipsis;
                overflow: hidden;
                flex-grow: 1;
                min-width: 0;
            }

            /* For the description */
            .text-ellipsis-desc {
                white-space: nowrap;
                text-overflow: ellipsis;
                overflow: hidden;
                width: 100%;
            }

            /* Ensure the parent containers allow text truncation */
            [style*="min-width: 0"] {
                min-width: 0 !important;
            }

            /* Optional: Add a hover tooltip for full text */
            /* .header-item-code.text-ellipsis:hover::after,
                                                .text-ellipsis-desc:hover::after {
                                                    content: attr(title);
                                                    position: absolute;
                                                    background: rgba(0,0,0,0.8);
                                                    color: white;
                                                    padding: 4px 8px;
                                                    border-radius: 4px;
                                                    font-size: 12px;
                                                    white-space: normal;
                                                    z-index: 1000;
                                                    max-width: 300px;
                                                } */

            /* Base tag style */
            .type-tag {
                font-family: 'Titillium Web', sans-serif;
                font-weight: 300;
                /* Light */
                font-size: 8pt !important;
                /* Required by client */
                padding: 3px 8px;
                border-radius: 6px;
                display: inline-block;
            }

            /* Subscription */
            .tag-subscription {
                background: #007BFF;
                color: white !important;
            }

            /* Hardware */
            .tag-hardware {
                background: #B0B0B0;
                color: #333;
                /* charcoal */
            }

            /* Software */
            .tag-software {
                background: #E1F2FF;
                color: #333;
                /* charcoal */
            }

            /* MSP (Other) */
            .tag-msp {
                background: #F1F8E9;
                color: #333;
                /* charcoal */
            }

            .short-card-header {
                width: 175px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .short-card-details {
                width: 140px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .short-card-details.validity {
                width: 158px !important;
            }

            .read-header,
            .edit-header {
                box-shadow: 0 3pt 4pt rgba(127, 127, 127, 0.6);
            }

            .cards-container .status-card div,
            .short-cards {
                min-height: 30px;
            }

            .capped-field {
                text-transform: uppercase;
            }

            /* ===============================
                                                    COMMON TABLE FIX (BOTH TABLES)
                                                    =============================== */

            .purchasing-details-container {}

            .distributer-details-container {}

            .contract-details-container {}

            .distributor-table td {
                border: 1px solid transparent;

            }

            /* .students-box {
                                    border: 1px solid #ddd;
                                    border-radius: 8px;
                                    padding: 6px;
                                } */

            .student-row {
                display: flex;
                align-items: center;
                gap: 6px;
                margin-bottom: 4px;
            }

            .student-icon {
                width: 14px;
                text-align: center;
            }

            .student-icon i {
                font-size: 12px;
                color: #444;
            }

            .student-name {
                flex: 1;
                font-size: 8pt;
                color: #7F7F7F;
                text-align: left;
            }

            .student-amount {
                font-size: 8pt;
                color: #7F7F7F;
                text-align: right;
                min-width: 40px;
            }

            .dropdown-menu .inner {
                max-height: 110px !important;
            }
        </style>
        <div class="con   no-print page-header py-1" id="">
            <!-- Full Table -->
            <div class="b   mb-0  ">
                <div class="block-content pt-0 mt-0">
                    <div class="TopArea" style="position: sticky;padding-top: 12px;z-index: 1000;padding-bottom: 7px;max-height: 62px;">
                        <div class="row align-items-center">
                            <?php
                            $filter =
                                (isset($_GET['advance_search']) ? 'advance_search=' . $_GET['advance_search'] : '') .
                                (isset($_GET['client_id']) ? '&client_id=' . $_GET['client_id'] : '') .
                                (isset($_GET['site_id']) ? '&' . http_build_query(['site_id' => $_GET['site_id']]) : '') .
                                (isset($_GET['distributor_id']) ? '&' . http_build_query(['distributor_id' => $_GET['distributor_id']]) : '') .
                                (isset($_GET['contract_status']) ? '&contract_status=' . $_GET['contract_status'] : '') .
                                (isset($_GET['estimate_no']) ? '&estimate_no=' . $_GET['estimate_no'] : '') .
                                (isset($_GET['sales_order_no']) ? '&sales_order_no=' . $_GET['sales_order_no'] : '') .
                                (isset($_GET['vendor_id']) ? '&' . http_build_query(['vendor_id' => $_GET['vendor_id']]) : '') .
                                (isset($_GET['invoice_no']) ? '&invoice_no=' . $_GET['invoice_no'] : '') .
                                (isset($_GET['invoice_date']) ? '&invoice_date=' . $_GET['invoice_date'] : '') .
                                (isset($_GET['po_no']) ? '&po_no=' . $_GET['po_no'] : '') .
                                (isset($_GET['po_date']) ? '&po_date=' . $_GET['po_date'] : '') .
                                (isset($_GET['reference_no']) ? '&reference_no=' . $_GET['reference_no'] : '') .
                                (isset($_GET['distrubutor_sales_order_no']) ? '&distrubutor_sales_order_no=' . $_GET['distrubutor_sales_order_no'] : '') .
                                (isset($_GET['contract_no']) ? '&contract_no=' . $_GET['contract_no'] : '') .
                                (isset($_GET['contract_start_date']) ? '&contract_start_date=' . $_GET['contract_start_date'] : '') .
                                (isset($_GET['has_attachment']) ? '&has_attachment=' . $_GET['has_attachment'] : '') .
                                (isset($_GET['daterange']) ? '&daterange=' . $_GET['daterange'] : '') .
                                (isset($_GET['renewal_within']) ? '&renewal_within=' . $_GET['renewal_within'] : '') .
                                (isset($_GET['contract_end_date']) ? '&contract_end_date=' . $_GET['contract_end_date'] : '') .
                                (isset($_GET['contract_description']) ? '&contract_description=' . $_GET['contract_description'] : '') .
                                (isset($_GET['comments']) ? '&comments=' . $_GET['comments'] : '') .
                                (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') .
                                (isset($_GET['vacation_month']) ? '&vacation_month=' . $_GET['vacation_month'] : '') .
                                (isset($_GET['vacation_year']) ? '&vacation_year=' . $_GET['vacation_year'] : '') .
                                (isset($_GET['requires_planning']) ? '&requires_planning=' . $_GET['requires_planning'] : '') .
                                (isset($_GET['planned_status']) ? '&planned_status=' . $_GET['planned_status'] : '') .
                                (isset($_GET['limit']) ? '&limit=' . $_GET['limit'] : '') .
                                (isset($_GET['id']) ? '&id=' . $_GET['id'] : '');
                            $filter_2 =
                                (isset($_GET['advance_search']) ? 'advance_search=' . $_GET['advance_search'] : '') .
                                (isset($_GET['client_id']) ? '&client_id=' . $_GET['client_id'] : '') .
                                (isset($_GET['site_id']) ? '&' . http_build_query(['site_id' => $_GET['site_id']]) : '') .
                                (isset($_GET['distributor_id']) ? '&' . http_build_query(['distributor_id' => $_GET['distributor_id']]) : '') .
                                (isset($_GET['contract_status']) ? '&contract_status=' . $_GET['contract_status'] : '') .
                                (isset($_GET['estimate_no']) ? '&estimate_no=' . $_GET['estimate_no'] : '') .
                                (isset($_GET['sales_order_no']) ? '&sales_order_no=' . $_GET['sales_order_no'] : '') .
                                (isset($_GET['vendor_id']) ? '&' . http_build_query(['vendor_id' => $_GET['vendor_id']]) : '') .
                                (isset($_GET['invoice_no']) ? '&invoice_no=' . $_GET['invoice_no'] : '') .
                                (isset($_GET['invoice_date']) ? '&invoice_date=' . $_GET['invoice_date'] : '') .
                                (isset($_GET['po_no']) ? '&po_no=' . $_GET['po_no'] : '') .
                                (isset($_GET['po_date']) ? '&po_date=' . $_GET['po_date'] : '') .
                                (isset($_GET['reference_no']) ? '&reference_no=' . $_GET['reference_no'] : '') .
                                (isset($_GET['distrubutor_sales_order_no']) ? '&distrubutor_sales_order_no=' . $_GET['distrubutor_sales_order_no'] : '') .
                                (isset($_GET['contract_no']) ? '&contract_no=' . $_GET['contract_no'] : '') .
                                (isset($_GET['contract_start_date']) ? '&contract_start_date=' . $_GET['contract_start_date'] : '') .
                                (isset($_GET['has_attachment']) ? '&has_attachment=' . $_GET['has_attachment'] : '') .
                                (isset($_GET['daterange']) ? '&daterange=' . $_GET['daterange'] : '') .
                                (isset($_GET['renewal_within']) ? '&renewal_within=' . $_GET['renewal_within'] : '') .
                                (isset($_GET['contract_end_date']) ? '&contract_end_date=' . $_GET['contract_end_date'] : '') .
                                (isset($_GET['contract_description']) ? '&contract_description=' . $_GET['contract_description'] : '') .
                                (isset($_GET['comments']) ? '&comments=' . $_GET['comments'] : '') .
                                (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') .
                                (isset($_GET['vacation_month']) ? '&vacation_month=' . $_GET['vacation_month'] : '') .
                                (isset($_GET['vacation_year']) ? '&vacation_year=' . $_GET['vacation_year'] : '') .
                                (isset($_GET['requires_planning']) ? '&requires_planning=' . $_GET['requires_planning'] : '') .
                                (isset($_GET['planned_status']) ? '&planned_status=' . $_GET['planned_status'] : '') .
                                (isset($_GET['limit']) ? '&limit=' . $_GET['limit'] : '');
                            
                            ?>
                            <div class="col-sm-4">
                                @Auth
                                    <div class="row align-items-center">
                                        <div class="mb-0 col-sm-2 pr-0">
                                            <span>
                                                <a class="btn filterSampleTestModal btn-dual banner-icon d1 {{ isset($_GET['advance_search']) ? 'active' : '' }} "
                                                    data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                    title="" data-original-title="Filters">
                                                    <i class="fa-thin fa-filter-list regular-icon text-white fs-25"></i>
                                                    <i class="fa-solid fa-filter-list solid-icon text-white fs-25 fa-fade"
                                                        style="padding-left: 2px;"></i>
                                                </a>
                                                {{-- 
                    <a class="btn btn-dual filterSampleTestModal d2 {{ (@$_GET['filter_test_name'] && $_GET['filter_test_name'] !== '') || !empty($_GET['filter_item_cat']) || !empty($_GET['filter_asset_no']) || !empty($_GET['filter_workorder_no']) || !empty($_GET['filter_bosubi']) || !empty($_GET['filter_sample_date']) || !empty($_GET['filter_production_date']) || !empty($_GET['filter_user']) ? 'filter-active' : '' }} "
                      data-custom-class="header-tooltip" data-toggle="tooltip"
                      data-trigger="hover" data-placement="top" title=""
                      data-original-title="Filters" href="javascript:;" id="GeneralFilters">
                      <img src="{{ asset('public/img/cf-menu-icons/header-filter.png') }}"
                        width="20"> --}}
                                            </span>
                                        </div>
                                        <div class="mb-0 col-sm-10 pl-0">
                                            <form class="push mb-0" method="get" id="form-search"
                                                action="{{ url('vacations/') }}?{{ $filter }}">
                                                <div class="input-group mainser banner-icon">
                                                    <input type="text" value="{{ @$_GET['search'] }}"
                                                        class="form-control searchNew font-titillium py-3" name="search"
                                                        placeholder="Search Vacations" style="font-weight: 300!important;">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text ">
                                                            {{-- <img src="{{ asset('public/img/ui-icon-search.png') }}" width="23px"> --}}
                                                            <i class="fa-thin fa-magnifying-glass-waveform regular-icon fs-20"
                                                                style="color: #ffffff"></i>
                                                            <i class="fa-solid fa-magnifying-glass-waveform solid-icon fs-20"
                                                                style="color: #ffffff"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="float-left " role="tab" id="accordion2_h1">
                                                    <!--     <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Modify Filters</a> -->
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-sm-2" style="margin-top: 2px;">
                                    @Auth
                                        <a class="btn btn-dual d2 banner-icon p-2 btn-add" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Add Vacation" href="javascript:;">
                                            <i class="fa-light fa-square-plus text-white regular-icon fs-25"></i>
                                            <i class="fa-solid fa-square-plus text-white header-solid-icon fs-25 fa-beat"
                                                style="padding-left: 4px; padding-right: 4.5px;"></i>
                                        </a>
                                        <span>
                                            <button class="btn btn-dual d2 banner-icon p-2" data-toggle="tooltip"
                                                data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Scroll Bubble" href="javascript:;" id="jumpToActiveBubble">
                                                <i class="fa-light fa-arrow-to-top regular-icon text-white fs-25"></i>
                                                <i class="fa-solid fa-arrow-to-top header-solid-icon text-white fs-25"
                                                    style="padding-left: 4px; padding-right: 4px;"></i>
                                            </button>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 d-flex align-items-center justify-content-end">
                                        <div class="mr-3 d-flex">
                                            {{ $qry->appends($_GET)->onEachSide(0)->links('pagination::bootstrap-4') }}
                                        </div>
                                        <div class="d-flex text-right  w-  justify-content-end">
                                            <form id="limit_form" class="ml-2 mb-0"
                                                action="{{ url('vacations') }}?{{ $_SERVER['QUERY_STRING'] }}">
                                                <select name="limit" class="float-right form-control mr-3 font-titillium  px-0"
                                                    style="width:auto">
                                                    <option value="10" {{ @$limit == 10 ? 'selected' : '' }}>10</option>
                                                    <option value="25" {{ @$limit == 25 ? 'selected' : '' }}>25</option>
                                                    <option value="50" {{ @$limit == 50 ? 'selected' : '' }}>50</option>
                                                    <option value="100" {{ @$limit == 100 ? 'selected' : '' }}>100</option>
                                                </select>
                                            </form>
                                            @if (@Auth::user()->role == 'admin')
                                                {{-- <a href="{{ url('settings') }}" data-toggle="tooltip"
                                                    data-title="Settings"class="mr-3 text-dark d3 banner-icon p-2 btn btn-dual align-content-center">
                                                    <i class="fa-thin fa-gear regular-icon fs-23 text-white"></i>
                                                    <i class="fa-solid fa-gear header-solid-icon fs-23 text-white fa-spin-pulse"
                                                        style="padding-left: 1.8px; padding-right: 4px;"></i>
                                                </a> --}}
                                            @endif
                                            <!-- User Dropdown -->
                                            <div class="dropdown d-inline-block align-content-center">
                                                <a type="button" class="banner-icon" id="page-header-user-dropdown"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    @if (Auth::user()->user_image != '')
                                                        <!--                     <img class="img-avatar imgAvatar img-avatar48"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      src="{{ asset('public') }}/dashboard_assets/media/avatars/avatar2.jpg"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      alt=""> -->
                                                        <i class="fa-thin fa-circle-user text-white fs-30 regular-icon"></i>
                                                        <i class="fa-solid fa-circle-user text-white fs-30 solid-icon"
                                                            style="padding-left: 3.5px; padding-right: 4px;"></i>
                                                    @else
                                                        <img class="img-avatar imgAvatar img-avatar48"
                                                            src="{{ asset('public/client_logos/') }}/{{ Auth::user()->user_image }}"
                                                            alt="">
                                                    @endif
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right p-0"
                                                        aria-labelledby="page-header-user-dropdown">
                                                        <div class="p-2">
                                                            @auth
                                                                <a class="dropdown-item" href="{{ url('change-password') }}">
                                                                    <i class="far fa-fw fa-user mr-1"></i> My Profile
                                                                </a>
                                                                <!-- END Side Overlay -->
                                                                <form id="logout-form" class="mb-0" method="post"
                                                                    action="{{ url('logout') }}">
                                                                    @csrf
                                                                </form>
                                                                <div role="separator" class="dropdown-divider"></div>
                                                                <a class="dropdown-item" href="javascript:;"
                                                                    onclick="document.getElementById('logout-form').submit()">
                                                                    <i class="far fa-fw fa-arrow-alt-circle-left mr-1"></i> Sign Out
                                                                </a>
                                                            @else
                                                                <a class="dropdown-item" href="{{ url('/login') }}">
                                                                    <i class="far fa-fw fa-user mr-1"></i> Login
                                                                </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid pb-0">
                        <div class="row">
                            <div class="col-md-4 bubble-header">
                                <div class="row align-items-center">
                                    <?php
                                    $search = isset($_GET['search']) && trim($_GET['search']) !== '';
                                    
                                    $class = 'bubble-header-grey';
                                    $get_text = '';
                                    $get_text_before = '';
                                    $get_text_display = 'd-none';
                                    
                                    if ($search && $filter_2) {
                                        $class = 'bubble-header-green';
                                        $get_text = 'Filtered and Search Results:';
                                        $get_text_before = '';
                                        $get_text_display = 'd-block';
                                    } elseif ($search) {
                                        $class = 'bubble-header-yellow';
                                        $get_text = '';
                                        $get_text_before = 'Search Results';
                                        $get_text_display = 'd-block';
                                    } elseif ($filter_2) {
                                        $class = 'bubble-header-blue';
                                        $get_text = 'Filters Applied:';
                                        $get_text_before = '';
                                        $get_text_display = 'd-block';
                                    }
                                    ?>
                                    <div class="col-1 {{ $class }}"></div>
                                    <p class="col-11 bubble-header-text d-flex justify-content-between align-items-center">Vacations
                                        <span class="{{ $get_text_display }} text-right" style="line-height: 1.3;">
                                            <a class="clear-link" href="{{ url('/vacations') }}"
                                                data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover"
                                                data-placement="top" title="" data-original-title="Clear"
                                                style="color: #595959;">
                                                <i class="fa-regular fa-circle-chevron-down"></i>
                                                <!-- <img class="nav-main-link-icon"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              src="{{ asset('public/img/cf-menu-icons/menu-icon-deactivate-grey.png') }}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              data-default="{{ asset('public/img/cf-menu-icons/menu-icon-deactivate-grey.png') }}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              data-hover="{{ asset('public/img/cf-menu-icons/3dot-deactivate.png') }}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              width="16"> -->
                                            </a>
                                            <a type="button" class="filterSampleTestModal bubble-a-tag"
                                                href="javascript:;">{{ $get_text }}</a>
                                            <br>
                                            <span class="bubble-filter-count">{{ $get_text_before }} {{ $totalRows }}
                                                Documents</span>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-8 detail-header detail-header-blue read-header">
                                <div class="h-100 d-flex align-items-center">
                                    <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                        {{-- <div class="d-flex justify-content-center align-items-center">
                                            <img src="{{ asset('public/img/menu-icon-contracts-white.png') }}" class="header-image"
                                                style="width: 36px; height: 36px;">
                                            <div class="" style="margin-left: 0.91rem;">
                                                <div class="d-flex align-items-center">
                                                    <div class="header-badge">CON</div>
                                                    <h4
                                                        class="mb-0 header-item-code text-ellipsis font-titillium text-white fw-800 fs-25">
                                                        Support
                                                        Contract</h4>
                                                </div>
                                                <p class="mb-0 header-desc text-ellipsis-desc">Pending Description</p>
                                            </div>
                                        </div> --}}
                                        <div class="d-flex justify-content-center align-items-center flex-grow-1"
                                            style="min-width: 0; overflow: hidden;">
                                            <img src="{{ asset('public/logo/kumonlogo-white.png') }}" class="header-image"
                                                style="width: 36px; height: 36px;">
                                            <div class="ms-3 flex-grow-1" style="min-width: 0; margin-left: 0.91rem;">
                                                <div class="d-flex align-items-center">
                                                    <div class="header-badge flex-shrink-0">VAC</div>
                                                    <h4
                                                        class="mb-0 header-item-code text-ellipsis font-titillium text-white fw-800 fs-25 ms-2">
                                                        Vacations
                                                    </h4>
                                                </div>
                                                <p class="mb-0 header-desc text-ellipsis-desc">Pending Description</p>
                                            </div>
                                        </div>

                                        <div class="new-header-icon-div d-flex align-items-center flex-shrink-0 no-print">
                                            @if ($totalRows != 0)
                                                <span class="icon-html"></span>
                                                <a data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                    title="" href="javascript:;" data-original-title="Mark as Planned"
                                                    class="text-white btn-mark-planned banner-icon sub-nav-icons d-none"
                                                    data-item-id="{{ @$GETID }}" data-id="{{ @$GETID }}">
                                                    <i class="fa-light fa-sparkles fs-20 regular-icon"></i>
                                                    <i class="fa-solid fa-sparkles fs-20 solid-icon"
                                                        style="padding-right: 2px; padding-left: 2.5px;"></i>
                                                </a>
                                                <a data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                                    href="javascript:;" data-original-title="Clone"
                                                    class="text-white btn-clone banner-icon sub-nav-icons"
                                                    data-item-id="{{ @$GETID }}" data-id="{{ @$GETID }}">
                                                    <i class="fa-light fa-clone fs-20 regular-icon"></i>
                                                    <i class="fa-solid fa-clone fs-20 solid-icon"
                                                        style="padding-right: 2px; padding-left: 2.5px;"></i>
                                                </a>
                                                <a href="javascript:;" type="button" data-toggle="tooltip" data-trigger="hover"
                                                    data-placement="top" data-html="true" title="" data-original-title="Share"
                                                    class="btn text-white btn-share banner-icon sub-nav-icons">
                                                    <i class="fa-light fa-circle-share-nodes regular-icon" style="font-size: 21px;"></i>
                                                    <i class="fa-solid fa-circle-share-nodes solid-icon"
                                                        style="padding-right: 2px; padding-left: 2.5px;font-size: 21px;"></i>
                                                </a>
                                                <a href="javascript:;" onclick="window.print()" d=""
                                                    class="text-white print-icon banner-icon sub-nav-icons"
                                                    data-item-id="{{ @$GETID }}" data-item-code="" data-toggle="tooltip"
                                                    data-trigger="hover" data-placement="top" title=""
                                                    data-original-title="Print">
                                                    <i class="fa-light fa-print fs-20 regular-icon"></i>
                                                    <i class="fa-solid fa-print fs-20 solid-icon"
                                                        style="padding-right: 2px; padding-left: 2.5px;"></i>
                                                </a>
                                                <a href="javascript:;" class="text-white edit-icon btn-edit banner-icon sub-nav-icons"
                                                    data-item-id="{{ @$GETID }}" data-id="{{ @$GETID }}"
                                                    data="{{ @$GETID }}" data-toggle="tooltip" data-trigger="hover"
                                                    data-placement="top" title="" data-original-title="Edit">

                                                    <i class="fa-light fa-file-pen fs-20 regular-icon"></i>
                                                    <i class="fa-solid fa-file-pen fs-20 solid-icon"></i>
                                                </a>
                                                <a href="javascript:;" d=""
                                                    class="text-white delete-icon btnDelete banner-icon sub-nav-icons"
                                                    data-item-id="{{ @$GETID }}" data="{{ @$GETID }}" data-item-code=""
                                                    data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                                    data-original-title="Delete">
                                                    <i class="fa-regular fa-trash fs-20 regular-icon"></i>
                                                    <i class="fa-solid fa-trash fs-20 solid-icon"
                                                        style="padding-right: 2.855px; padding-left: 4px;"></i>
                                                </a>

                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 detail-header detail-header-edit edit-header d-none">
                                <div class="h-100 d-flex align-items-center">
                                    <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                        <div class="d-flex justify-content-center align-items-center flex-grow-1"
                                            style="min-width: 0; overflow: hidden;">
                                            <i class="dynamic-icon fa-solid fa-file-pen text-white fs-35"></i>

                                            <div class="ms-3 flex-grow-1" style="min-width: 0; margin-left: 0.91rem;">
                                                <div class="d-flex align-items-center">
                                                    <div class="header-badge flex-shrink-0">VAC</div>
                                                    <h4
                                                        class="mb-0 header-item-code text-ellipsis font-titillium text-white fw-800 fs-25 ms-2">
                                                        Clients
                                                    </h4>
                                                </div>
                                                <p class="mb-0 header-desc text-ellipsis-desc">Pending Description</p>
                                            </div>
                                        </div>
                                        {{-- edit,new,clone,renew --}}
                                        <div class="new-header-icon-div d-flex align-items-center  no-print">
                                            <a href="javascript:;" d="" class="text-white update-continue-icon updateBtn banner-icon"
                                                data-item-id="{{ @$GETID }}" data="{{ @$GETID }}" data-item-code=""
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Add + Continue" data-type="continue">
                                                <i class="fa-light fa-file-circle-plus fs-23 regular-icon"></i>
                                                <i class="fa-solid fa-file-circle-plus fs-23 solid-icon"
                                                    style="padding-right: 1px; padding-left: 2px;"></i>
                                            </a>
                                            <a href="javascript:;" d="" class="text-white update-icon updateBtn banner-icon"
                                                data-item-id="{{ @$GETID }}" data="{{ @$GETID }}" data-item-code=""
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Save">
                                                <i class="fa-light fa-circle-check fs-23 regular-icon"></i>
                                                <i class="fa-solid fa-circle-check fs-23 solid-icon"
                                                    style="padding-right: 2.855px; padding-left: 3.0px;"></i>
                                            </a>
                                            <a href="javascript:;" d="" class="text-white close-icon closeEditBtn banner-icon"
                                                data-item-id="{{ @$GETID }}" data="{{ @$GETID }}" data-item-code=""
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Close">
                                                <i class="fa-light fa-circle-xmark fs-23 regular-icon"></i>
                                                <i class="fa-solid fa-circle-xmark fs-23 solid-icon"
                                                    style="padding-right: 2.855px; padding-left: 3.0px;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid pb-0">
                        <div class="row">
                            <div class="col-md-4 bubble-header">
                                <!-- Filter dropdown container - initially hidden -->
                                <div class="filter-dropdown-container roll-down">
                                    <div class="row align-items-center">
                                        <div class="col-1 bubble-fil ter-ht {{ $class }}" style="height: 336px;"></div>
                                        <form id="filterForm" method="GET" action=""
                                            class="mb-0 col-11 py-3 px-3 small-box small-box-400">
                                            <div class="d-flex justify-content-between align-items-center pl-2 mb-3">
                                                <span class="font-filter">Filters</span>
                                                <button type="button" class="close close-cross close-filter" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="block block-transparent mb-0">
                                                <div class="pl-3 pt-0 pb-0">
                                                    <div class="align-items-baseline row">
                                                        <label class="col-sm-4 modal-label">Status</label>
                                                        <div class="col-sm-7 px-sm-0 form-group">
                                                            <select id="status" name="status" class="form-control" title="All">
                                                                <option value="All"
                                                                    {{ request('status', 'All') === 'All' ? 'selected' : '' }}>
                                                                    All
                                                                </option>
                                                                <option value="Active"
                                                                    {{ request('status') === 'Active' ? 'selected' : '' }}>
                                                                    Active
                                                                </option>
                                                                <option value="Upcoming"
                                                                    {{ request('status') === 'Upcoming' ? 'selected' : '' }}>
                                                                    Upcoming
                                                                </option>
                                                                <option value="Inactive"
                                                                    {{ request('status') === 'Inactive' ? 'selected' : '' }}>
                                                                    Inactive
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="align-items-baseline row">
                                                        <label class="col-sm-4 modal-label">Month/Year</label>
                                                        <div class="col-sm-7 px-sm-0 form-group">
                                                            <div class="d-flex">
                                                                <select id="vacation_month" name="vacation_month"
                                                                    class="form-control mr-2" title="Month">
                                                                    @foreach (range(1, 12) as $m)
                                                                        @php
                                                                            $label = date('M', mktime(0, 0, 0, $m, 1));
                                                                        @endphp
                                                                        <option value="{{ $m }}"
                                                                            {{ (string) request('vacation_month') === (string) $m ? 'selected' : '' }}>
                                                                            {{ $label }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <select id="vacation_year" name="vacation_year"
                                                                    class="form-control" title="Year">
                                                                    @foreach (range(date('Y') - 2, date('Y') + 2) as $y)
                                                                        <option value="{{ $y }}"
                                                                            {{ (string) request('vacation_year') === (string) $y ? 'selected' : '' }}>
                                                                            {{ $y }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="align-items-baseline row">
                                                        <label class="col-sm-4 modal-label">Requires Planning</label>
                                                        <div class="col-sm-7 px-sm-0 form-group">
                                                            <select id="requires_planning" name="requires_planning"
                                                                class="form-control" title="All">
                                                                <option value="All"
                                                                    {{ request('requires_planning', 'All') === 'All' ? 'selected' : '' }}>
                                                                    All
                                                                </option>
                                                                <option value="true"
                                                                    {{ request('requires_planning') === 'true' ? 'selected' : '' }}>
                                                                    True
                                                                </option>
                                                                <option value="false"
                                                                    {{ request('requires_planning') === 'false' ? 'selected' : '' }}>
                                                                    False
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="align-items-baseline row">
                                                        <label class="col-sm-4 modal-label">Planned Status</label>
                                                        <div class="col-sm-7 px-sm-0 form-group">
                                                            <select id="planned_status" name="planned_status"
                                                                class="form-control" title="All">
                                                                <option value="All"
                                                                    {{ request('planned_status', 'All') === 'All' ? 'selected' : '' }}>
                                                                    All
                                                                </option>
                                                                <option value="true"
                                                                    {{ request('planned_status') === 'true' ? 'selected' : '' }}>
                                                                    True
                                                                </option>
                                                                <option value="false"
                                                                    {{ request('planned_status') === 'false' ? 'selected' : '' }}>
                                                                    False
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $userAccess = explode(',', @Auth::user()->access_to_client);
                                                    $c_check = 0;
                                                    if (@Auth::user()->role == 'admin') {
                                                        $client = DB::Table('clients')->where('is_deleted', 0)->where('is_active', 1)->orderBy('client_display_name', 'asc')->get();
                                                    } else {
                                                        if (sizeof($userAccess) == 1) {
                                                            $c_check = 1;
                                                        }
                                                        $client = DB::Table('clients')->whereIn('id', $userAccess)->where('is_deleted', 0)->where('is_active', 1)->orderBy('client_display_name', 'asc')->get();
                                                    }
                                                    
                                                    ?>
                                                </div>
                                                <input type="hidden" name="search" value="{{ @$_GET['search'] }}">
                                                <div class="block-content text-right pt-1 pr-0" style="padding-left: 9mm;">
                                                    <a href="{{ url('/vacations') }}" class="btn btn-action mr-3">Clear</a>
                                                    <button type="submit" class="btn btn-action" name="advance_search">
                                                        <span class="btn-action-gear d-none mr-2"><img
                                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                                        Apply
                                                        <span class="btn-action-gear d-none ml-2"><img
                                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid pb-0">
                        <!-- Page Content -->
                        <div class="row px-0">
                            @if (!isset($hash))
                                <div id="leftCol" class="col-lg-4   LeftDi no-print bubble-div"
                                    style="overflow-y: auto;height: 85vh;">
                                    <div style="padding-top: 15px;">
                                        @foreach ($qry as $q)
                                            <div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent"
                                                data="{{ $q->id }}" style="cursor:pointer;">
                                                <div class="block-content p-2 d-flex">
                                                    <div class="mr-2" style="width:16%;padding:3px">
                                                        <img src="{{ asset('public/logo/kumon-logo.png') }}" class="rounded-circle  "
                                                            width="100%" style="object-fit: cover;">
                                                    </div>
                                                    <div class="align-content-center" style="width:82%;">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <p class="c1 font-12pt mb-0 text-truncate titillium-web-black mb-0">
                                                                {{ $q->student_name }}
                                                            </p>
                                                            @php
    // date_range format: "09-Feb-2026 to 26-Feb-2026"
    [$start, $end] = array_map('trim', explode('to', $q->date_range));

    $startDate = \Carbon\Carbon::createFromFormat('d-M-Y', $start)->startOfDay();
    $endDate   = \Carbon\Carbon::createFromFormat('d-M-Y', $end)->endOfDay();

    $today = now()->startOfDay();

    $isActive   = $today->between($startDate, $endDate);
    $isUpcoming = $today->lt($startDate); // start is in future
@endphp
                                                            @php
                                                            $reducedIcon = $q->reduced_workload
                                                                ? '&nbsp;<i class="fa-duotone fa-arrow-down-to-line text-darkgrey fs-16 mr-1" data-toggle="tooltip" data-original-title="Reduced Workload"></i>'
                                                                : '';
                                                            $plannedIcon = $q->planned
                                                                ? '<i class="fa-light fa-sparkles text-darkgrey fs-16 mr-1" data-toggle="tooltip" data-original-title="Planned"></i>'
                                                                : '';
                                                        @endphp
                                                            <div>
                                                                <div>
        {!! $plannedIcon !!}

        @if ($isActive)
            <i class="fa-light fa-circle-check text-green fs-20"
               data-toggle="tooltip" data-original-title="Active"></i>
        @elseif ($isUpcoming)
            <i class="fa-light fa-triangle-exclamation text-warning fs-16"
               data-toggle="tooltip" data-original-title="Upcoming"></i>
        @else
            <i class="fa-light fa-circle-xmark text-red fs-20"
               data-toggle="tooltip" data-original-title="Inactive"></i>
        @endif
    </div>
                                                            </div>
                                                        </div>
                                                        <div class="w-100">
                                                            <div class="d-flex align-items-center mb-0">
                                                                @if (@$q->take_work_home)
                                                                    <i class="fa-duotone fa-solid fa-books fs-18"></i>
                                                                @else
                                                                    <i class="fa-duotone fa-solid fa-island-tropical fs-18"></i>
                                                                @endif
                                                                <p class="font-12pt mb-0 text-truncate titillium-web-light ml-1">
                                                                    {{ @$q->date_range }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $subjectIds = is_array($q->subjects)
                                                                ? $q->subjects
                                                                : (json_decode($q->subjects, true) ?:
                                                                []);
                                                            $subjectNames = DB::table('subjects')
                                                                ->whereIn('id', $subjectIds)
                                                                ->pluck('name')
                                                                ->implode(', ');
                                                        @endphp
                                                        
                                                        <div class="d-flex align-items-center justify-content-between w-100">
                                                            <div class="d-flex align-items-center" style="width: 75%;">
                                                                <p class="font-9pt mb-0 text-truncate c2 titillium-web-light"
                                                                    style="line-height: 1;">
                                                                    {{ $subjectNames }}
                                                                </p>
                                                                {!! $reducedIcon !!}
                                                            </div>

                                                            <div class="d-flex align-items-center">
                                                                
                                                                <?php if(Auth::check()) { ?>
                                                                <a class="dropdown-toggle banner-icon" data-toggle="dropdown"
                                                                    aria-haspopup="true" aria-expanded="false" href="javascript:;">
                                                                   <i
                                                                        class="fa-thin fa-ellipsis-stroke-vertical bubbles-icon-color fs-18 regular-icon"></i>
                                                                    <i class="fa-solid fa-ellipsis-vertical bubbles-icon-color fs-18 solid-icon"
                                                                        style="padding-right: 9px; padding-left: 9px;"></i>
                                                                </a>
                                                                <div class="dropdown-menu"
                                                                    aria-labelledby="dropdown-dropright-primary">
                                                                    <a href="javascript:;"
                                                                        class="dropdown-item d-flex align-items-center btn-edit-3dot pl-0"
                                                                        data="{{ $q->id }}" data-id="{{ $q->id }}">
                                                                        <i class="fa-light fa-file-pen mx-2 icons-3dot"></i>
                                                                        Edit
                                                                    </a>
                                                                    <a class="dropdown-item d-flex align-items-center btn-clone-3dot pl-0"
                                                                        data="{{ $q->id }}" data-id="{{ $q->id }}"
                                                                        href="javascript:;">
                                                                        <i class="fa-light fa-clone mx-2 icons-3dot"></i>
                                                                        Clone
                                                                    </a>

                                                                    <a class="dropdown-item d-flex align-items-center  pl-0 btnDelete"
                                                                        data="{{ $q->id }}" href="javascript:void(0)">
                                                                        <i class="fa-light fa-trash mx-2 icons-3dot"></i>
                                                                        Delete
                                                                    </a>
                                                                </div>
                                                                <?php  } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if (!isset($hash))

                                <div id="rightCol" class="  col-lg-8  pr-0">
                                    <div class="row bg-nav-tab read-header">
                                        <div class="col-md-12 border-top rounded ml-3 mt-2"
                                            style="max-width: 95%; border-color: #d9d9d996 !important; border-width: 2px!important;">
                                        </div>
                                        <div class="col-md-12 bg-nav-tab position-relative">
                                            <nav class="align-items-center d-flex justify-content-between read-nav-tabs sub-nav-tabs">
                                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                    <button class="nav-link active" id="nav-main-tab-vacation"
                                                        data-toggle="tab" data-target="#nav-main-vacation" type="button"
                                                        role="tab" aria-controls="nav-main-vacation"
                                                        aria-selected="true">Vacation</button>
                                                    <button class="nav-link border-right-0" id="nav-audit-tab-vacation"
                                                        data-toggle="tab" data-target="#nav-audit-vacation" type="button"
                                                        role="tab" aria-controls="nav-audit-vacation" aria-selected="false">Audit
                                                        Trail</button>
                                                    {{-- <button class="nav-link" id="nav-student-tab-client" data-toggle="tab"
                                                        data-target="#nav-student-client" type="button" role="tab"
                                                        aria-controls="nav-student-client" aria-selected="false">Student</button>
                                                    <button class="nav-link" id="nav-vacation-tab-client" data-toggle="tab"
                                                        data-target="#nav-vacation-client" type="button" role="tab"
                                                        aria-controls="nav-vacation-client" aria-selected="false">Vacation</button>
                                                    <button class="nav-link" id="nav-comments-tab-client" data-toggle="tab"
                                                        data-target="#nav-comments-client" type="button" role="tab"
                                                        aria-controls="nav-comments-client" aria-selected="true">Comments</button>
                                                    <button class="nav-link" id="nav-attachments-tab-client" data-toggle="tab"
                                                        data-target="#nav-attachments-client" type="button" role="tab"
                                                        aria-controls="nav-attachments-client"
                                                        aria-selected="false">Attachments</button>
                                                    <button class="nav-link border-right-0" id="nav-audit-tab-client"
                                                        data-toggle="tab" data-target="#nav-audit-client" type="button"
                                                        role="tab" aria-controls="nav-audit-client" aria-selected="false">Audit
                                                        Trail</button> --}}
                                                </div>
                                                {{-- <a href="javascript:void();" class="card-toggle mr-4" data-toggle="tooltip"
                                                    data-trigger="hover" data-placement="top" title=""
                                                    data-original-title="Show Cards"><i
                                                        class="fa-address-card fa-solid fs-18 text-white"></i></a> --}}
                                            </nav>
                                            {{-- <a href="javascript:void();" class="card-toggle position-absolute" style="right: 45px; top: 13px;"><i class="fa-address-card fa-thin fs-18 text-white"></i></a> --}}
                                        </div>
                                    </div>
                                    <div class="row bg-edit-tab edit-header d-none">
                                        <div class="col-md-12 border-top rounded ml-3 mt-2"
                                            style="max-width: 95%; border-color: #D9D9D9 !important; border-width: 1px!important;">
                                        </div>
                                        <div class="col-md-12 bg-edit-tab edit-sub-header">
                                            <nav class="sub-nav-tabs edit-nav-tabs d-no ne">
                                                <div class="nav nav-tabs" id="nav-tab-edit" role="tablist">
                                                    <button class="nav-link border-right-0 active" id="nav-main-tab-vacation-edit"
                                                        data-toggle="tab" data-target="#nav-main-vacation-edit" type="button"
                                                        role="tab" aria-controls="nav-main-vacation-edit"
                                                        aria-selected="true">Vacation</button>
                                                </div>
                                            </nav>
                                        </div>
                                    </div>
                                    <div class="pr-3 d-none" id="showEditData" style="overflow-y: auto;height:77vh;"></div>
                                    <div class="d-flex flex-column" style="height: 78vh;">
                                        <div class="pr-3" id="showCards"></div>
                                        <div class="container pr-3 flex-grow-1 position-relative" id="showData"
                                            style="overflow-y: auto;">
                                        </div>
                                    @else
                                        <div class="  col-lg-12  position-relative" id="showData"
                                            style="overflow-y: auto;height:77vh;">
                                            <div class="col-lg-12 d-none" id="showEditData" style="overflow-y: auto;height:77vh;">
                                            </div>
                            @endif
                        </div>
                        <div class="modal fade" id="viewData" tabindex="-1" role="dialog" aria-labelledby="modal-block-large"
                            aria-hidden="true" data-bs-backdrop="static">
                            <div class="modal-dialog modal-lg " role="document">
                                <div class="modal-content">
                                    <div class="block block-themed block-transparent mb-0">
                                        <div class="block-header  py-1" style="background:#4194F6">
                                            <h3 class="block-title" id="hostnameDisplay">All Info</h3>
                                            <div id="clientLogo" class="block-options">
                                            </div>
                                        </div>
                                        <div class="block-content" id="accordion2" role="tablist" aria-multiselectable="true">
                                            <div class="block block-rounded blockDivs mb-1">
                                                <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                    <a class="font-w600 text-secondary" data-toggle="collapse"
                                                        data-parent="#accordion2" href="#accordion2_q1" aria-expanded="true" aria
                                                        -controls="accordion2_q1"><img src="{{ asset('public/img/contract.jpg') }}"
                                                            width="20" class="mr-1" height="20"> Contract Info</a>
                                                </div>
                                                <div id="accordion2_q1" class="collapse  " role="tabpanel"
                                                    aria-labelledby="accordion2_h1">
                                                    <div class="block-content">
                                                        <table class="table tablemodal">
                                                            <tbody>
                                                                <tr>
                                                                    <th>Contract Type</th>
                                                                    <td id="contract_typeDisplay"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Site</th>
                                                                    <td id="site_name"></td>
                                                                </tr>
                                                                <tr class="   ">
                                                                    <th>Contract Ended By</th>
                                                                    <td id="ended_by" class="ContractEndDiv"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Start Date/End Date</th>
                                                                    <td><span id="contract_start_dateDisplay"></span> / <span
                                                                            id="contract_end_dateDisplay"></span></td>
                                                                </tr>
                                                                <tr class="   ">
                                                                    <th>Contract Ended On</th>
                                                                    <td id="ended_on" class="ContractEndDiv"></td>
                                                                </tr>
                                                                <tr class="   ">
                                                                    <th>Days Remaining</th>
                                                                    <td id="days_remaining"></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="block block-rounded mb-1 blockDivs">
                                                <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                    <a class="font-w600 text-secondary" data-toggle="collapse"
                                                        data-parent="#accordion2" href="#accordion2_q2" aria-expanded="true" aria
                                                        -controls="accordion2_q2"><img
                                                            src="{{ asset('public/img/contract-details.png') }}" width="20"
                                                            class="mr-1" height="20"> Contract Details</a>
                                                </div>
                                                <div id="accordion2_q2" class="collapse  " role="tabpanel"
                                                    aria-labelledby="accordion2_h1">
                                                    <div class="block-content">
                                                        <div id="assetdiv" class="table-responsive">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="block block-rounded blockDivs NetworkDiv mb-1">
                                                <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                    <a class="font-w600 text-secondary" data-toggle="collapse"
                                                        data-parent="#accordion2" href="#accordion2_q3" aria-expanded="true" aria
                                                        -controls="accordion2_q3"><img
                                                            src="{{ asset('public/img/distribution.png') }}" width="20"
                                                            class="mr-1" height="20"> Distribution</a>
                                                </div>
                                                <div id="accordion2_q3" class="collapse  " role="tabpanel"
                                                    aria-labelledby="accordion2_h1">
                                                    <div class="block-content">
                                                        <table class="table tablemodal">
                                                            <tbody>
                                                                <tr>
                                                                    <th>Distribution</th>
                                                                    <td id="distributor_nameDisplay"></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Reference #</th>
                                                                    <td id="reference_noDisplay"></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Sales Order #</th>
                                                                    <td id="sales_order_noDisplay"></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="block block-rounded ManagedDiv blockDivs mb-1">
                                                <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                    <a class="font-w600 text-secondary" data-toggle="collapse"
                                                        data-parent="#accordion2" href="#accordion2_q4" aria-expanded="true" aria
                                                        -controls="accordion2_q4"><img src="{{ asset('public/img/purchasing.png') }}"
                                                            width="20" class="mr-1" height="20"> Purchasing</a>
                                                </div>
                                                <div id="accordion2_q4" class="collapse  " role="tabpanel"
                                                    aria-labelledby="accordion2_h1">
                                                    <div class="block-content">
                                                        <table class="table tablemodal">
                                                            <tbody>
                                                                <tr>
                                                                    <th>Estimate #</th>
                                                                    <td id="estimate_noDisplay"></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Sales #</th>
                                                                    <td id="sales_no_Display"></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Invoice #</th>
                                                                    <td id="invoice_noDisplay"></td>
                                                                    <th>Invoice Date</th>
                                                                    <td id="invoice_dateDisplay"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>PO #</th>
                                                                    <td id="po_noDisplay"></td>
                                                                    <th>PO Date</th>
                                                                    <td id="po_dateDisplay"></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (@Auth::user()->role == 'admin')
                                                <div class="block block-rounded commentsDiv blockDivs mb-1">
                                                    <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                        <a class="font-w600 text-secondary" data-toggle="collapse"
                                                            data-parent="#accordion2" href="#accordion2_q5" aria-expanded="true" aria
                                                            -controls="accordion2_q5"><img
                                                                src="{{ asset('public/img/comments.jpg') }}" width="20"
                                                                class="mr-1" height="20"> Comments</a>
                                                    </div>
                                                    <div id="accordion2_q5" class="collapse  " role="tabpanel"
                                                        aria-labelledby="accordion2_h1">
                                                        <div class="block-content">
                                                            <div id="commentDisplay"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="block block-rounded attachmentsDiv blockDivs mb-1">
                                                <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                    <a class="font-w600 text-secondary" data-toggle="collapse"
                                                        data-parent="#accordion2" href="#accordion2_q6" aria-expanded="true" aria
                                                        -controls="accordion2_q6"><img src="{{ asset('public/img/attachment.png') }}"
                                                            width="20" class="mr-1" height="20"> Attachments</a>
                                                </div>
                                                <div id="accordion2_q6" class="collapse  " role="tabpanel"
                                                    aria-labelledby="accordion2_h1">
                                                    <div class="block-content py-4">
                                                        <div id="attachmentDisplay"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="block block-rounded blockDivs mb-1">
                                                <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                    <a class="font-w600 text-secondary" data-toggle="collapse"
                                                        data-parent="#accordion2" href="#accordion2_q8" aria-expanded="true" aria
                                                        -controls="accordion2_q8"><img src="{{ asset('public/img/audit.png') }}"
                                                            width="20" class="mr-1" height="20"> Audit Trail</a>
                                                </div>
                                                <div id="accordion2_q8" class="collapse  " role="tabpanel"
                                                    aria-labelledby="accordion2_h1">
                                                    <div class="block-content">
                                                        <table class="table tablemodal">
                                                            <tbody>
                                                                <tr>
                                                                    <th>Created By</th>
                                                                    <td id="created_by"></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Created On</th>
                                                                    <td id="created_at"></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Last Modified By</th>
                                                                    <td id="updated_by"></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Last Modified On</th>
                                                                    <td id="updated_at"></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            </tbody>
                                            </table>
                                            <hr>
                                        </div>
                                        <div class="block-content block-content-full   bg-light">
                                            <a class=" mr-4" href="javascript:;" data-dismiss="modal"><img
                                                    src="{{ asset('public/img/back icon.png') }}" width="40px" height="40px"
                                                    style="object-fit:contain;"></a>
                                            <a class="  printDiv" target="_blank"><img src="{{ asset('public/img/print.png') }}"
                                                    width="40px" height="40px" style="object-fit:contain;"></a>
                                            <a class="  pdfDiv" target="_blank"><img src="{{ asset('public/img/pdf.jpg') }}"
                                                    width="40px" height="40px" style="object-fit:contain;"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END Page Content -->
                        <!--                              <div class="modal fade" id="viewData" tabindex="-1" role="dialog" aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="modal-dialog modal-lg " role="document">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="modal-content">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="block block-themed block-transparent mb-0">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="block-header bg-primary-dark">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <h3 class="block-title"  id="contract_noDisplay"> </h3>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="block-options">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="block-content">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <table class="table tablemodal">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <tbody>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <tr class="ContractEndDiv  ">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Reason</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td id="ended_reason"></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td></td><td></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Client</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td id="client_display_name"></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td style="width: 25%;"></td><td style="width: 25%;"></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Vendor</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td id="vendor_nameDisplay"></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td></td><td></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Registered Email</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td id="registered_emailDisplay"></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td></td><td></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Type</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td id="contract_typeDisplay"></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td></td><td></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Attachment</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td id="attachmentDisplay"></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td></td><td></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            @if (@Auth::user()->role == 'admin')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Comments</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td id="commentDisplay"></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td></td><td></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            @endif
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Description</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td id="descriptionDisplay"></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td></td><td></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </tbody>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   </table>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div id="assetdiv" class="table-responsive">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <div class="block-content block-content-full   bg-light">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <a class="btn btn-primary printDiv"  target="_blank">Print</a>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           <a class="btn btn-primary pdfDiv" target="_blank">PDF</a>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-sm float-right btn-light" data-dismiss="modal">Close</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    -->
                        <form action="" class="mb-0 pb-0">
                            <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" data-backdrop="static"
                                aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
                                <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                                    <div class="modal-content">
                                        <div class="block  block-transparent mb-0">
                                            <div class="block-header pb-0  ">
                                                <span class="b e section-header">Filter Contracts</span>
                                                <div class="block-options">
                                                    <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </button> -->
                                                </div>
                                            </div>
                                            <div class="block-content new-block-content pt-0 pb-0 ">
                                                <div class="row">
                                                    <div class="col-sm-4 form-group">
                                                        <label class="   " for="example-hf-email">Contract Status</label>
                                                        <select type="text" class="form-control"
                                                            value="{{ @$_GET['contract_status'] }}" id="contract_status"
                                                            name="contract_status" placeholder="All ">
                                                            <option value="">All</option>
                                                            <option value="Active"
                                                                {{ @$_GET['contract_status'] == 'Active' ? 'selected' : '' }}>Active
                                                            </option>
                                                            <option value="Upcoming"
                                                                {{ @$_GET['contract_status'] == 'Upcoming' ? 'selected' : '' }}>
                                                                Upcoming
                                                            </option>
                                                            <option value="Expired"
                                                                {{ @$_GET['contract_status'] == 'Expired' ? 'selected' : '' }}>
                                                                Expired
                                                            </option>
                                                            <option value="Expired/Ended"
                                                                {{ @$_GET['contract_status'] == 'Expired/Ended' ? 'selected' : '' }}>
                                                                Ended
                                                            </option>
                                                            <option value="Inactive"
                                                                {{ @$_GET['contract_status'] == 'Inactive' ? 'selected' : '' }}>
                                                                Renewed
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <?php
                                                    $userAccess = explode(',', @Auth::user()->access_to_client);
                                                    $c_check = 0;
                                                    if (@Auth::user()->role == 'admin') {
                                                        $client = DB::Table('clients')->where('is_deleted', 0)->where('is_active', 1)->orderBy('client_display_name', 'asc')->get();
                                                    } else {
                                                        if (sizeof($userAccess) == 1) {
                                                            $c_check = 1;
                                                        }
                                                        $client = DB::Table('clients')->whereIn('id', $userAccess)->where('is_deleted', 0)->where('is_active', 1)->orderBy('client_display_name', 'asc')->get();
                                                    }
                                                    
                                                    ?>
                                                    @if ($c_check == 0)
                                                        <div class="col-sm-4  form-group">
                                                            <label class="   " for="example-hf-client_id">Client</label>
                                                            <select type="client_id" class="form-control selectpicker"
                                                                data-style="btn-outline-light border text-dark"
                                                                data-live-search="true" id="client_id" title="All" value=""
                                                                name="client_id" placeholder="Client">
                                                                @foreach ($client as $c)
                                                                    <option value="{{ $c->id }}"
                                                                        {{ @$_GET['client_id'] == $c->id ? 'selected' : '' }}>
                                                                        {{ $c->client_display_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif
                                                    <div class="col-sm-4  form-group">
                                                        <label class="   " for="example-hf-client_id">Renewal Within
                                                            (Days)</label>
                                                        <input type="number" class="form-control  " id="renewal_within"
                                                            value="{{ @$_GET['renewal_within'] }}" name="renewal_within">
                                                    </div>
                                                    <div class="col-sm-8  form-group">
                                                        <label class="   " for="example-hf-client_id">Date Range</label>
                                                        <input type="text" class="js-flatpickr form-control bg-white"
                                                            id="example-flatpickr-range" name="daterange"
                                                            placeholder="Select Date Range" data-mode="range"
                                                            value="{{ @$_GET['daterange'] }}" data-alt-input="true"
                                                            data-date-format="Y-m-d" data-alt-format="d-M-Y">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="block-content block-content-full   pt-4"
                                                style="padding-left: 9mm;padding-right: 9mm">
                                                <button type="submit" class="btn mr-3 btn-new" name="advance_search">Apply</button>
                                                <button type="button" class="btn     btn-new-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                @if (isset($_GET['advance_search']))
                                                    <a href="{{ url('vacations') }}" class="btn     btn-new-secondary float-right"
                                                        style="background: black;
                color: goldenrod;">Clear Filters</a>
                                                @else
                                                    <a href="{{ url('vacations') }}" class="btn     btn-new-secondary float-right"
                                                        style="">Clear
                                                        Filters</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!--  <form class="mb-0 pb-0" id="export form" action="{{ url('export-excel-contract') }}?{{ $filter }}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    method="get">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="modal fade" id="Expor tModal" tabindex="-1" role="dialog" data-backdrop="static"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      <div class="modal-dialog modal- -centered  modal-md modal-bac " role="document">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="modal-content">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          <div class="block  block-transparent mb-0">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="block-header pb-0  ">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <span class="b e section-header">Export Contract</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <div class="block-options">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="block-content new-block-content pt-0 pb-0 ">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <div class="row">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="col-sm-12">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <label>Fields to Export</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <select class="form-control selectpicker"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    data-style="btn-outline-light border columns text-dark" id="columns"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    data-actions-box="true" data-live-search="true" data- multiple=""
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    required="" name="columns[]">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="1">Status</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="2">Client</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="3">Site</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="25">Managed By</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="4">Contract Type</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="5">Vendor</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="6">Start Date</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="7">End Date</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="26">Currency</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="27">Total Amount</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="8">Distributor</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="9">Contract #</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="10">Description</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="11">End User Email</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="12">Distro Reference #</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="13">Distro SO#</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="14">Estimate #</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="15">Sales Order #</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="16">Invoice #</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="17">Invoice Date</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="18">PO #</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="19">Line PN#</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="20">Line Assets</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="21">Line Quantity</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="22">Line Description</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="23">Line MSRP</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="24">Line Type</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </select>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="block-content block-content-full   pt-4"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              style="padding-left: 9mm;padding-right: 9mm">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <button type="button" class="btn mr-3 btn-new " id="btnExport">Export</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <button type="button" class="btn     btn-new-secondary"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                data-dismiss="modal">Cancel</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </form> -->
                        <form id="exportform" action="{{ url('export-excel-contract') }}?{{ $filter }}" method="get">
                            @csrf
                            <div class="modal fade" id="ExportModal" tabindex="-1" role="dialog" data-backdrop="static"
                                aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
                                <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
                                    <div class="modal-content">
                                        <div class="block  block-transparent mb-0">
                                            <div class="block-header   ">
                                                <span
                                                    class="b e section-header font-titillium fw-600 fs-20 text-darkgrey endTitle">Export
                                                    Contract</span>
                                                <div class="block-options">
                                                </div>
                                            </div>
                                            <div class="block-content pt-0 row">
                                                <div class="col-sm-12 px-0">
                                                    <select class="form-control selectpicker export-select export-step1"
                                                        data-style="btn-outline-light border columns text-dark" id="columns"
                                                        data-actions-box="true" data-live-search="true" data- multiple=""
                                                        required="" name="columns[]">
                                                        <option value="1">Status</option>
                                                        <option value="2">Client</option>
                                                        <option value="3">Site</option>
                                                        <option value="25">Managed By</option>
                                                        <option value="4">Contract Type</option>
                                                        <option value="5">Vendor</option>
                                                        <option value="6">Start Date</option>
                                                        <option value="7">End Date</option>
                                                        <option value="26">Currency</option>
                                                        <option value="27">Total Amount</option>
                                                        <option value="8">Distributor</option>
                                                        <option value="9">Contract #</option>
                                                        <option value="10">Description</option>
                                                        <option value="11">End User Email</option>
                                                        <option value="12">Distro Reference #</option>
                                                        <option value="13">Distro SO#</option>
                                                        <option value="14">Estimate #</option>
                                                        <option value="15">Sales Order #</option>
                                                        <option value="16">Invoice #</option>
                                                        <option value="17">Invoice Date</option>
                                                        <option value="18">PO #</option>
                                                        <option value="19">Line PN#</option>
                                                        <option value="20">Line Assets</option>
                                                        <option value="21">Line Quantity</option>
                                                        <option value="22">Line Description</option>
                                                        <option value="23">Line MSRP</option>
                                                        <option value="24">Line Type</option>
                                                    </select>
                                                    <center><span class="font-titillium text-darkgrey export-step2 d-none fw-300"
                                                            style="font-size: 14pt;">Are you sure you wish to export the current view
                                                            results?</span></center>
                                                    <hr class="mb-1 mt-4">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                                                <button type="button" class="btn ok-btn btn-primary export-step1">OK</button>
                                                <button type="submit" class="btn ok-btn btn-primary export-step2 d-none"
                                                    id="btnExport">OK</button>
                                                <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form id='end-client' action="{{ url('end-client') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="">
                            <input type="hidden" name="end" value="">
                            <div class="modal fade" id="EndModal" tabindex="-1" role="dialog" data-backdrop="static"
                                aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
                                <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
                                    <div class="modal-content">
                                        <div class="block  block-transparent mb-0">
                                            <div class="block-header   ">
                                                <span class="b e section-header font-titillium fw-600 fs-20 text-renue-blue">Change
                                                    Status</span>
                                                <div class="block-options">
                                                </div>
                                            </div>

                                            <div class="block-content pt-0 row">
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-2"></i>
                                                    <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Are you sure
                                                        you wish to change the status of this Client from <span
                                                            id="currentStatus"></span> to <span id="toStatus"></span></span>
                                                </div>
                                                <div class="col-sm-12 px-0">
                                                    <textarea class="form-control" rows="5" id="reason" name="reason" placeholder="Enter Comment"></textarea>
                                                    <hr class="mb-1 mt-4">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                                                <button type="submit" class="btn ok-btn btn-primary end-btn endTitle"
                                                    id="end-btn">Deactivate</button>
                                                <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form action="{{ url('insert-comments-client') }}" id="insertComment" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $GETID }}">
                            <div class="modal fade" id="CommentModal" tabindex="-1" role="dialog" data-backdrop="static"
                                aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
                                <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
                                    <div class="modal-content">
                                        <div class="block  block-transparent mb-0">
                                            <div class="block-header   ">
                                                <span
                                                    class="b e section-header font-titillium fw-600 fs-20 text-darkgrey">Comment</span>
                                                <div class="block-options">
                                                    <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </button> -->
                                                </div>
                                            </div>
                                            <div class="block-content pt-0 row">
                                                <div class="col-sm-12 px-0">
                                                    <textarea class="form-control  " rows="5" required="" name="comment"></textarea>
                                                    <hr class="mb-1 mt-4">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                                                <button type="submit" class="btn ok-btn btn-primary" id="CommentSave">Save</button>
                                                <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form action="{{ url('insert-attachment-client') }}" id="insertAttachment" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $GETID }}">
                            <input type="hidden" name="attachment_array" id="attachment_array">
                            <div class="modal fade" id="AttachmentModal" tabindex="-1" role="dialog" data-backdrop="static"
                                aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
                                <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
                                    <div class="modal-content">
                                        <div class="block  block-transparent mb-0">
                                            <div class="block-header   ">
                                                <span
                                                    class="b e section-header font-titillium text-darkgrey fs-20 fw-600">Attachments</span>
                                                <div class="block-options">
                                                    <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </button> -->
                                                </div>
                                            </div>
                                            <div class="block-content pt-0 row">
                                                <div class="col-sm-12    px-0">
                                                    <input type="file" class="  attachment" multiple="" style=""
                                                        id="attachment" name="attachment" placeholder="">
                                                    <hr class="mb-1 mt-4">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                                                <button type="submit" class="btn ok-btn btn-primary"
                                                    id="AttachmentSave">Save</button>
                                                <button type="button" class="btn cancel-btn" id="AttachmentClose"
                                                    data-dismiss="modal">Cancel</button>
                                            </div>
                                            <!-- <div class="modal-footer border-0 pt-0" style="justify-content: space-evenly;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <button type="button" id="updateCommentBtn" class="btn ok-btn btn-primary">Update</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!--   <form class="mb-0 pb-0" action="{{ url('delete-attachment-client') }}" method="post">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    @csrf
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="modal fade" id="DelAttachmentModal" tabindex="-1" role="dialog"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="modal-content">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          <div class="block  block-transparent mb-0">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="block-header pb-0  ">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <span class="b e section-header"><span class="revokeText">Delete Attachment
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <div class="block-options">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="block-content new-block-content pt-0 pb-0 ">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <input type="hidden" id="del_client_id" name="client_id">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <input type="hidden" id="del_attachment_id" name="id">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <div class="row">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="col-sm-12 text-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <p class="fw-300">Are you sure you wish to delete this attachment?</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="block-content block-content-full text-center pt-3"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              style="padding-left: 9mm;padding-right: 9mm">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <button type="submit" class="btn mx-2 btn-yes  ">Yes</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <button type="button" class="btn mx-2 btn-no" data-dismiss="modal">No</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </form> -->
                </main>
                <!-- END Main Container -->




                <div class="modal fade" id="assetsModal" tabindex="-1" role="dialog" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header align-items-center border-0">
                                <h5 class="modal-title font-titillium fw-900 text-header-blue fs-30">Assigned Assets</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                </button>
                            </div>
                            <div class="modal-body pt-0">
                                <!-- Filled by JS -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="assetsModal_2" tabindex="-1" role="dialog" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header align-items-center border-0">
                                <h5 class="modal-title font-titillium fw-900 text-header-blue" style="font-size: 22px;">Assigned
                                    Assets</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                </button>
                            </div>
                            <div class="modal-body pt-0">
                                <!-- Filled by JS -->
                            </div>
                            <div class="modal-footer border-0 pt-0" style="justify-content: end;">
                                <button type="button" id="addAssetsModal"
                                    class="btn font-titillium fw-500 py-1 new-ok-btn float-right" style="width: 120px;">Add
                                    Assets</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="allAssetsModal" tabindex="-1" role="dialog" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header align-items-center border-0">
                                <h5 class="modal-title font-titillium fw-900 text-header-blue" style="font-size: 22px;">Assign
                                    Assets</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                </button>
                            </div>
                            <div class="modal-body pt-0">
                                <!-- Filled by JS -->
                            </div>
                            <div class="modal-footer border-0 pt-0" style="justify-content: end;">
                                <button type="button" id="selectAssets"
                                    class="btn font-titillium fw-500 py-1 new-ok-btn float-right"
                                    style="width: 120px;">Assign</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Comment Modal -->
                <div class="modal fade" id="editCommentModal" tabindex="-1" role="dialog"
                    aria-labelledby="editCommentModalLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form id="editCommentForm" class="mb-0" method="POST" action="update-comment-contract">
                                @csrf
                                <div class="modal-header align-items-center border-0">
                                    <h5 class="modal-title font-titillium text-darkgrey fs-20">Edit Comment</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                    </button>
                                </div>
                                <div class="modal-body py-0">
                                    <input type="hidden" id="comment_id" name="comment_id">
                                    <input type="hidden" id="client-id" name="client-id">
                                    <div class="form-group">
                                        <textarea class="form-control" id="comment_text" name="comment_text" rows="5"></textarea>
                                    </div>
                                    <hr class="mb-3 mt-4">
                                </div>
                                <div class="modal-footer border-0 pt-0" style="justify-content: space-evenly;">
                                    <button type="button" id="updateCommentBtn" class="btn ok-btn btn-primary">Update</button>
                                    <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Comment Modal -->
                <div class="modal fade" id="deleteCommentModal" tabindex="-1" role="dialog"
                    aria-labelledby="deleteCommentModalLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form id="deleteCommentForm" class="mb-0" method="POST" action="delete-comment-client">
                                @csrf
                                <div class="modal-header align-items-center border-0">
                                    <h5 class="modal-title font-titillium fw-800 text-header-blue" style="font-size: 18pt;">Delete
                                    </h5>
                                </div>
                                <div class="modal-body py-0">
                                    <input type="hidden" id="comment_id" name="comment_id">
                                    <input type="hidden" id="client_id" name="client_id">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-2"></i>
                                        <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Are you sure you wish to
                                            delete this comment?</span>
                                    </div>
                                    <hr>
                                </div>
                                <div class="modal-footer border-0 pt-0" style="justify-content: space-evenly;">
                                    <button type="button" id="deleteCommentBtn" class="btn del-btn btn-danger">Delete</button>
                                    <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Attachment Modal -->
                <div class="modal fade" id="DelAttachmentModal" tabindex="-1" role="dialog"
                    aria-labelledby="DelAttachmentModalLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form id="deleteAttachmentForm" class="mb-0" method="POST"
                                action="{{ url('delete-attachment-client') }}">
                                @csrf
                                <div class="modal-header align-items-center border-0">
                                    <h5 class="modal-title font-titillium fw-800 text-header-blue" style="font-size: 18pt;">Delete
                                    </h5>
                                </div>
                                <div class="modal-body py-0">
                                    <input type="hidden" id="del_attachment_id" name="attachment_id">
                                    <input type="hidden" id="del_client_id" name="client_id">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-2"></i>
                                        <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Are you sure you wish to
                                            delete this attachment?</span>
                                    </div>
                                    <hr>
                                </div>
                                <div class="modal-footer border-0 pt-0" style="justify-content: space-evenly;">
                                    <button type="button" id="deleteAttachmentBtn" class="btn del-btn btn-danger">Delete</button>
                                    <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- Delete Contract Modal -->
                <div class="modal fade" id="deleteVacationModal" tabindex="-1" role="dialog"
                    aria-labelledby="deleteVacationModalLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form id="deleteVacationForm" class="mb-0" method="POST" action="{{ url('delete-contract') }}">
                                @csrf
                                <div class="modal-header align-items-center border-0">
                                    <h5 class="modal-title font-titillium fw-800 text-header-blue" style="font-size: 18pt;">Delete
                                    </h5>
                                </div>
                                <div class="modal-body py-0">
                                    <input type="hidden" id="id" name="id">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-2"></i>
                                        <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Are you sure you wish to
                                            delete this Vacation?</span>
                                    </div>
                                    <hr>
                                </div>
                                <div class="modal-footer border-0 pt-0" style="justify-content: space-evenly;">
                                    <button type="button" id="deleteVacationBtn" class="btn del-btn btn-danger">Delete</button>
                                    <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="confirmCloneModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header font-titillium fw-700 fs-20 text-header-blue">Clone</span>
                                    <div class="block-options">
                                    </div>
                                </div>
                                <div class="modal-body py-0">
                                    <input type="hidden" id="id" name="id">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-2"></i>
                                        <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Are you sure you wish to
                                            clone this Vacation?</span>
                                    </div>
                                    <hr>
                                </div>
                                <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                                    <button type="button" class="btn ok-btn btn-primary btn-clone-confirm">Clone</button>
                                    <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="confirmPlannedModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="confirmPlannedModalLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header">
                                    <span class="b e section-header font-titillium fw-700 fs-20 text-header-blue">Mark as Planned</span>
                                    <div class="block-options"></div>
                                </div>
                                <div class="modal-body py-0">
                                    <input type="hidden" id="planned_vacation_id" name="planned_vacation_id">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-2"></i>
                                        <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Are you sure you wish to
                                            mark this Student’s vacation as Planned?</span>
                                    </div>
                                    <hr>
                                </div>
                                <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                                    <button type="button" class="btn ok-btn btn-primary btn-mark-planned-confirm">Yes</button>
                                    <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="plannedProcessingModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="plannedProcessingModalLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header align-items-center border-0 px-4">
                                <h5 class="modal-title font-titillium fw-800 text-header-blue" style="font-size: 18pt;">Processing</h5>
                            </div>
                            <div class="modal-body pt-0 pb-4 px-4">
                                <div class="d-flex align-items-center">
                                    <i class="fa-light fa-gear-complex fa-spin text-darkgrey fs-30 mr-2"></i>
                                    <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Please wait while marking planned ...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="confirmRenewModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header font-titillium fw-700 fs-20 text-header-blue">Renew</span>
                                    <div class="block-options">
                                    </div>
                                </div>
                                <div class="modal-body py-0">
                                    <input type="hidden" id="id" name="id">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-2"></i>
                                        <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Are you sure you wish to
                                            deactivate this Client?</span>
                                    </div>
                                    <hr>
                                </div>
                                <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                                    <button type="button" class="btn ok-btn btn-primary btn-renew-confirm">Renew</button>
                                    <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="changeStatus" tabindex="-1" role="dialog" aria-labelledby="changeStatusLabel"
                    aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header align-items-center border-0 px-4">
                                <h5 class="modal-title font-titillium fw-800 text-header-blue" style="font-size: 18pt;">Change
                                    Status
                                </h5>
                            </div>
                            <div class="modal-body pt-0 pb-4 px-4">
                                <div class="d-flex align-items-center">
                                    <i class="fa-light fa-gear-complex fa-spin text-darkgrey fs-30 mr-2"></i>
                                    <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Please wait while updating
                                        status ...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="notifyEmailsModal" tabindex="-1" role="dialog"
                    aria-labelledby="changeStatusLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header align-items-center border-0 px-4">
                                <h5 class="modal-title font-titillium fw-800 text-header-blue" style="font-size: 18pt;">Change
                                    Status
                                </h5>
                            </div>
                            <div class="modal-body pt-0 pb-4 px-4">
                                <div class="d-flex align-items-center">
                                    <i class="fa-light fa-gear-complex fa-spin text-darkgrey fs-30 mr-2"></i>
                                    <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Please wait while sending
                                        out e-mail notifications...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="pinnedMessageModal" tabindex="-1" role="dialog"
                    aria-labelledby="changeStatusLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header align-items-center border-0 px-4">
                                <h5 class="modal-title font-titillium fw-800 text-header-blue" style="font-size: 18pt;">Pinned
                                    Messages

                                </h5>
                            </div>
                            <div class="modal-body pt-0 pb-4 px-4">
                                <div class="d-flex align-items-center">
                                    <i class="fa-light fa-gear-complex fa-spin text-darkgrey fs-30 mr-2"></i>
                                    <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Please wait while setting
                                        pinned messages...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="unsavedChangesModal" tabindex="-1" role="dialog"
                    aria-labelledby="unsavedChangesModalLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form id="leaveForm" class="mb-0" method="POST" action="delete-comment-contract">
                                @csrf
                                <div class="modal-header align-items-center border-0">
                                    <h5 class="modal-title font-titillium fw-800 text-header-blue" style="font-size: 18pt;">Leave
                                        this page?</h5>
                                </div>
                                <div class="modal-body py-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-2"></i>
                                        <span class="font-titillium text-darkgrey" style="font-size: 14pt;">If you leave, your
                                            unsaved changes will be discarded.</span>
                                    </div>
                                    <hr>
                                </div>
                                <div class="modal-footer border-0 pt-0" style="justify-content: space-evenly;">
                                    <button type="button" class="btn ok-btn btn-primary" data-dismiss="modal">Stay Here</button>
                                    <button type="button" id="confirmExit" data="" class="btn cancel-btn">Leave</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script src="{{ asset('public/js') }}/xlsx.full.min.js"></script>

                <script>
                    $(function() {
                        $(document).on('mouseenter', '.mandatory-icon', function() {
                        $(this).find('i').addClass('fa-solid text-red').removeClass('fa-light');
                    });
                    $(document).on('mouseleave', '.mandatory-icon', function() {
                        $(this).find('i').addClass('fa-light').removeClass('fa-solid text-red');
                    });
                    $('#jumpToActiveBubble').on('click', function() {
                        const container = $('#leftCol');
                        const activeBubble = container.find('.c-active').first();

                        if (!activeBubble.length) {
                            alert('No bubble is currently selected.');
                            return;
                        }

                        const scrollTop =
                            container.scrollTop() +
                            activeBubble.position().top -
                            10; // small padding from top

                        container.animate({
                                scrollTop: scrollTop
                            },
                            400
                        );
                    });
                        const messageTitle = sessionStorage.getItem('successTitle');
                        const message = sessionStorage.getItem('successMessage');
                        if (message && messageTitle) {
                            showSuccessNotification(messageTitle, message);
                            sessionStorage.removeItem('successMessage'); // Clear the flag
                            sessionStorage.removeItem('successTitle'); // Clear the flag
                        }

                        function showSuccessNotification(title, message) {
                            Dashmix.helpers('notify', {
                                type: 'success',
                                message: `
                            <div>
                                <div class="font-titillium" style="font-weight: 800; color: #4EA833; font-size: 15pt;">${title}</div>
                                <div class="d-flex align-items-center">
                                    <div style="font-size: 14pt; margin-right: 8px;"><i class="fa-thin fa-circle-check"></i></div>
                                    <div>${message}</div>
                                </div>
                            </div>
                        `,
                                allow_dismiss: true,
                                delay: 3000,
                                align: 'center',
                            });
                        }
                        // Enable tooltips globally with HTML support
                        $('[data-toggle="tooltip"]').tooltip({
                            html: true,
                        });
                        $('.new-header-icon-div .banner-icon[data-toggle="tooltip"]').tooltip('dispose').tooltip({
                            html: true,
                            placement: 'top',
                            boundary: 'window',
                            popperConfig: {
                                modifiers: {
                                    offset: {
                                        enabled: true,
                                        offset: '0,-8'
                                    },
                                    flip: {
                                        enabled: false // ❗ stops up/down jumping
                                    },
                                    preventOverflow: {
                                        enabled: false // ❗ stops Popper from moving it
                                    },
                                    computeStyle: {
                                        adaptive: false // ❗ CRITICAL for consistency
                                    }
                                }
                            }
                        });
                    });

                    $(document).on('click', '.read-nav-tabs .nav-link', function() {
                        var currentTab = $(this).attr('id');
                        var target = $(this).attr('data-target');
                        localStorage.setItem('vacation_active_tab', currentTab);
                        localStorage.setItem('vacation_active_tab_target', target);
                    })
                    $(document).on('click', '#addAssetsModal', function() {
                        // Get excluded IDs and pn_no from the button's data attributes
                        let excludeIds = $(this).data('exclude-ids') || [];
                        let pn_no = $(this).data('pn_no');

                        // Close the current modal
                        $('#assetsModal_2').modal('hide');

                        // Open the all assets modal
                        $('#allAssetsModal').modal('show');

                        // Load assets excluding the ones already assigned
                        loadAllAssetsModal(excludeIds, pn_no);
                    });

                    function loadAllAssetsModal(excludeIds, pn_no) {
                        // Show loading indicator
                        $('#allAssetsModal .modal-body').html(`
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Loading assets...</p>
                            </div>
                        `);
                        // 🔹 Get affiliate IDs (multiple)
                        let affiliateIds = $('input[name="affiliate_ids[]"]')
                            .map(function() {
                                return $(this).val();
                            }).get();

                        // 🔹 Get client ID (single)
                        let clientId = $('input[name="client_id"]').val();

                        console.log('clientId-- ', clientId);
                        console.log('affiliateIds-- ', affiliateIds);


                        // Prepare parameters
                        let params = {
                            exclude_ids: Array.isArray(excludeIds) ? excludeIds.join(',') : excludeIds,
                            affiliate_ids: affiliateIds, // array
                            client_id: clientId // single value
                        };

                        // Make AJAX call
                        $.ajax({
                            url: '{{ url('/available-assets') }}', // Your Laravel route
                            method: 'GET',
                            data: params,
                            success: function(response) {
                                if (response.success) {
                                    renderAssetsModal(response.data, pn_no);
                                } else {
                                    $('#allAssetsModal .modal-body').html(`
                                        <div class="alert alert-danger">
                                            Failed to load assets. Please try again.
                                        </div>
                                    `);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error loading assets:', error);
                                $('#allAssetsModal .modal-body').html(`
                                    <div class="alert alert-danger">
                                        Error loading assets: ${error}
                                    </div>
                                `);
                            }
                        });
                    }

                    function renderAssetsModal(assets, pn_no) {
                        let html = `
                        <div class="form-group">
            <input type="text" id="assetSearch" class="form-control" placeholder="Search assets..." style="margin-bottom: 15px;">
        </div>
                            <div class="border p-2 rounded-10px">
                            <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                <table class="table table-sm table-striped table-borderless mb-0 font-titillium">
                                    <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="5%"></th>
                                            <th width="45%">Hostname</th>
                                            <th width="45%">Serial No.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="assetsListBody">`;

                        if (assets.length === 0) {
                            html += `<tr><td colspan="4" class="text-center text-muted py-4">No available assets found</td></tr>`;
                        } else {
                            assets.forEach(asset => {
                                const displayName = asset.fqdn || asset.hostname || 'N/A';
                                const sn = asset.sn || 'N/A';
                                const assetType = asset.asset_type || 'virtual';

                                // Escape HTML to prevent XSS
                                const escapeHtml = (text) => {
                                    if (!text) return '';
                                    const div = document.createElement('div');
                                    div.textContent = text;
                                    return div.innerHTML;
                                };

                                html += `<tr class="asset-item">
                                    <td style="vertical-align: middle;" class="text-center">
                                        <input type="checkbox" style="opacity:0; visibility:hidden;" class="asset-checkbox" value="${asset.id}" 
                                            data-asset='${JSON.stringify(asset).replace(/'/g, "&#39;")}'>
                                    </td>
                                    <td class="py-2"><i class="fa-light fa-${getAssetIcon(asset.asset_type)} text-grey fs-18"></i></td>
                                    <td class="py-2 fw-300 fs-16">${escapeHtml(displayName)}</td>
                                    <td class="py-2 fw-300 fs-16">${escapeHtml(sn)}</td>
                                </tr>`;
                            });
                        }

                        html += `</tbody></table></div></div>`;

                        $('#allAssetsModal .modal-body').html(html);

                        // Add search functionality
                        $('#assetSearch').on('keyup', function() {
                            const searchTerm = $(this).val().toLowerCase();
                            $('#assetsListBody tr').each(function() {
                                const rowText = $(this).text().toLowerCase();
                                $(this).toggle(rowText.includes(searchTerm));
                            });
                        });

                        // Select all functionality
                        $('#selectAllAssets').on('change', function() {
                            $('.asset-checkbox').prop('checked', $(this).prop('checked'));
                        });

                        // Store pn_no in the Assign button for later use
                        $('#allAssetsModal .modal-footer button').attr('data-pn_no', pn_no);
                    }

                    function getAssetIcon(assetType) {
                        const map = {
                            "virtual": "box",
                            "physical": "server",
                            "workstation": "computer",
                            "firewall": "black-brick-wall",
                            "switch": "ethernet",
                            "dsitribution switch": "network-wired",
                            "isp-router": "router",
                            "accesspoint": "circle-wifi",
                            "voip-phone": "phone-office",
                            "printer": "print",
                            "router": "arrows-to-circle",
                            "projector": "projector",
                            "ups": "plug-circle-plus",
                            "laptop": "laptop",
                            "pc": "desktop",
                            "scanner": "scanner-gun"
                        };

                        return map[assetType?.toLowerCase()] || "box"; // default icon
                    }

                    $(document).on('click', '.btn-clone-3dot, .btn-clone', function(event) {
                        hideDropdown(event);
                        var data = $(this).attr('data');
                        var dataId = $(this).attr('data-id');
                        $('.btn-clone-confirm').attr({
                            'data': data,
                            'data-id': dataId
                        });
                        $('#confirmCloneModal').modal('show');
                    });
                    $(document).on('click', '.btn-renew-3dot, .btn-renew', function(event) {
                        hideDropdown(event);
                        var data = $(this).attr('data');
                        var dataId = $(this).attr('data-id');
                        $('.btn-renew-confirm').attr({
                            'data': data,
                            'data-id': dataId
                        });
                        $('#confirmRenewModal').modal('show');
                    });

                    function hideDropdown(event) {
                        var menu = $(event.target).closest('.dropdown-menu');
                        if (menu.length && menu.hasClass('show')) {
                            menu.removeClass('show');
                        }
                    }

                    $(document).on("click", ".btn-share", function() {
                        const shareBtn = $(this);

                        // Copy link
                        const link = window.location.href;
                        navigator.clipboard.writeText(link).then(() => {
                            // Change tooltip content
                            shareBtn.attr("data-original-title",
                                "<img src='{{ asset('public/check.png') }}' width='15' class='mr-1'> Link copied to clipboard!"
                            );

                            // Show tooltip with HTML
                            shareBtn.tooltip('show');

                            // Reset after 2s
                            setTimeout(() => {
                                shareBtn.tooltip('hide')
                                    .attr("data-original-title", "Share");
                            }, 3000);
                        });
                    });
                    $(document).on("click", ".btn-share-2", function(e) {
                        const shareBtn = $(this);
                        const id = $(this).attr('data-url');

                        // Copy link
                        const appUrl = "{{ config('app.url') }}";
                        const link = appUrl + "/vacations" + id;

                        navigator.clipboard.writeText(link).then(() => {
                            // // Change tooltip content
                            // shareBtn.attr("data-original-title",
                            //     "<img src='{{ asset('public/check.png') }}' width='15' class='mr-1'> Link copied to clipboard!"
                            // );

                            // // Show tooltip with HTML
                            // shareBtn.tooltip('show');

                            // // Reset after 2s
                            // setTimeout(() => {
                            //     shareBtn.tooltip('hide')
                            //         .attr("data-original-title", "Share");
                            // }, 3000);

                            const toast = $(`
                                <div class="clipboard-toast" style="
                                    position: absolute;
                                    top: ${e.pageY + 10}px;
                                    left: ${e.pageX - 132}px;
                                    z-index: 9999;
                                    display: none;
                                    background: #fff;
                                    padding: 8px 12px;
                                    border-radius: 6px;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                                    width: fit-content;
                                    height: fit-content;
                                ">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-circle-check mr-2"></i>
                                        <span class="font-titillium fs-14 text-darkgrey">Copied to clipboard!</span>
                                    </div>
                                </div>
                            `);

                            // Append to body
                            $('body').append(toast);

                            // Show toast
                            toast.fadeIn(200);

                            // Auto-hide after 2 seconds and remove from DOM
                            setTimeout(() => toast.fadeOut(200, function() {
                                $(this).remove();
                            }), 2000);

                        });
                    });
                </script>
            @endsection('content')
            <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
                crossorigin="anonymous"></script>
            <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>

            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>


            <script>
                $(function() {



                    $(document).on('input', '.capped-field', function() {
                        this.value = this.value.toUpperCase();
                    });

                    const $left = $('#leftCol');
                    const $right = $('#rightCol');

                    function hasVerticalScrollbar(el) {
                        // returns true if element's content overflow vertically (scrollbar present)
                        return el.scrollHeight > el.clientHeight;
                    }

                    function updateDivider() {
                        // show divider on left when left DOES NOT have a vertical scrollbar
                        if (!hasVerticalScrollbar($left[0])) {
                            $left.addClass('left-vertical-divider');
                        } else {
                            $left.removeClass('left-vertical-divider');
                        }

                        // optionally: if you want divider when right has no scrollbar instead,
                        // adapt logic accordingly
                    }

                    // run on load and resize
                    $(window).on('load resize', updateDivider);
                    // run once on DOM ready
                    updateDivider();

                    // re-check when content changes in either column
                    const observerConfig = {
                        childList: true,
                        subtree: true,
                        characterData: true
                    };
                    const observer = new MutationObserver(function() {
                        // throttle: run after microtask to avoid many repeated calls
                        requestAnimationFrame(updateDivider);
                    });

                    if ($left.length) observer.observe($left[0], observerConfig);
                    if ($right.length) observer.observe($right[0], observerConfig);

                    // optional: also re-check after images load inside columns
                    $left.find('img').on('load', updateDivider);
                    $right.find('img').on('load', updateDivider);
                });
            </script>

            <script type="text/javascript">
                $(function() {
                    $(document).on('click', '.btn-pin-message', function(event) {
                        $('#pinMessageModal').modal('show');
                    });

                    $(document).on('click', '.btn-mark-planned', function() {
                        const id = $(this).attr('data-id') || $(this).attr('data-item-id');
                        if (!id) {
                            showError('Unable to mark planned. Missing vacation id.');
                            return;
                        }

                        $('#planned_vacation_id').val(id);
                        $('#confirmPlannedModal').modal('show');
                    });

                    $(document).on('click', '.btn-mark-planned-confirm', function () {

                        const id = $('#planned_vacation_id').val();
                        if (!id) {
                            showError('Unable to mark planned. Missing vacation id.');
                            return;
                        }

                        // Close confirm modal
                        $('#confirmPlannedModal').modal('hide');

                        // Show processing modal AFTER confirm modal fully closes
                        $('#confirmPlannedModal').one('hidden.bs.modal', function () {
                            $('#plannedProcessingModal').modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                        });

                        $.ajax({
                            url: '{{ url('mark-vacation-planned') }}',
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                id: id
                            },
                            success: function () {
                                // showSuccessNotify(
                                //     'Student marked as planned successfully',
                                //     'Student marked as planned successfully.'
                                // );
                                sessionStorage.setItem('successTitle', "Planned Successfully");
                                        sessionStorage.setItem('successMessage',
                                            "Student marked as planned successfully");
                                // showData(id);
                                location.reload();
                            },
                            error: function (xhr) {
                                showError(xhr.responseText || 'Failed to mark planned');
                            },
                            complete: function () {
                                // Hide processing modal ONCE
                                $('#plannedProcessingModal').modal('hide');
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                            }
                        });
                    });

                    $(document).on('click', '.btn-email', function(event) {
                        $('#EmailModal').modal('show');
                    });

                    // add notify email
                    $(document).on('click', '.add-email-notify', function(event) {
                        var email = $('#email_notify').val().trim();
                        var client_id = $(this).attr('data-id');

                        if (email === '') {
                            showError('Please enter an email address.');
                            return;
                        }

                        // Simple email format validation
                        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailPattern.test(email)) {
                            showError('Please enter a valid email address.');
                            return;
                        }

                        // Check uniqueness (case-insensitive) in the current list
                        var exists = false;
                        $('.added-notify-email tbody .email-notify').each(function() {
                            if ($(this).text().trim().toLowerCase() === email.toLowerCase()) {
                                exists = true;
                                return false;
                            }
                        });

                        if (exists) {
                            showError('This email has already been added.');
                            return;
                        }

                        // disable button to prevent double-clicks
                        var $btn = $(this);
                        $btn.prop('disabled', true);

                        $('.no-notify-email-row').remove(); // Remove "no emails" row if present



                        // AJAX request to add email (append only on success)
                        // $.ajax({
                        //     url: '{{ url('add-notify-email') }}',
                        //     method: 'POST',
                        //     data: {
                        //         _token: '{{ csrf_token() }}',
                        //         email: email,
                        //         client_id: client_id
                        //     },
                        //     success: function(response) {
                        //         if (response.status === 'success') {
                        $('.added-notify-email tbody').append(`
                                            <tr class="email-notify-item banner-icon" data-client-id="${client_id}">
                                                <td class="py-2 border-0 " style="border-radius: 13px 0 0 13px;">
                                                    <span class="fw-300 text-darkgrey font-titillium fs-15 email-notify">${email}</span>
                                                </td>
                                                <td class="py-2 border-0 text-right align-middle" width="50" style="border-radius: 0 13px 13px 0;">
                                                    <a href="javascript:;" class="align-items-center d-flex drag-handle justify-content-center mb-0 remove-notification" data-client-id="${client_id}" data-email="${email}">
                                                            <i class="fa-light fa-circle-xmark mr-0 text-grey fs-18"></i>
                                                        </a>
                                                </td>
                                            </tr>
                                        `);

                        // Clear input field
                        $('#email_notify').val('');
                        if ($('.added-notify-email tbody tr').length === 0) {
                            $('.btn-notify-email').prop('disabled', true).removeClass(
                                    'btn-primary')
                                .addClass('cancel-btn');
                        } else {
                            $('.btn-notify-email').prop('disabled', false).removeClass(
                                    'cancel-btn')
                                .addClass('btn-primary');
                        }
                        showToast('notify-email-toast-added');
                        // } else {
                        //     showError(response.message || 'An error occurred.');
                        // }
                        //         },
                        //         error: function() {
                        //             showError('An error occurred while adding the email.');
                        //         },
                        //         complete: function() {
                        //             $btn.prop('disabled', false);
                        //         }
                        //     });
                    });

                    // remove notify email
                    $(document).on('click', '.remove-notification', function(e) {

                        e.preventDefault();

                        let email = $(this).data('email');
                        let client_id = $(this).data('client-id');
                        let $row = $(this).closest('tr');

                        // store removed row HTML for undo action
                        let removedRowHTML = $row.prop('outerHTML');

                        // fade out for UI effect
                        $row.fadeOut(200, function() {
                            $(this).remove();

                            // If table becomes empty, add "no emails" placeholder row
                            if ($('.added-notify-email tbody tr').length === 0) {
                                $('.btn-notify-email').prop('disabled', true).removeClass('btn-primary')
                                    .addClass(
                                        'cancel-btn');
                                $('.added-notify-email tbody').append(`
                                <tr class="no-notify-email-row" style="border-radius: 13px;">
                                                                    <td colspan="2" class="font-titillium text-darkgrey fw-400 fs-14 text-center">No email addresses added</td></tr>
                            `);
                            } else {
                                $('.btn-notify-email').prop('disabled', false).removeClass('cancel-btn')
                                    .addClass(
                                        'btn-primary');
                            }
                        });
                        showToast('notify-email-toast-deleted');
                        window.lastRemovedNotify = {
                            html: removedRowHTML,
                            email: email,
                            client_id: client_id
                        };
                        // // AJAX request to delete email
                        // $.ajax({
                        //     url: '{{ url('remove-notify-email') }}',
                        //     method: 'POST',
                        //     data: {
                        //         _token: '{{ csrf_token() }}',
                        //         email: email,
                        //         client_id: client_id
                        //     },
                        //     success: function(response) {
                        //         if (response.status === 'success') {
                        //             showToast('notify-email-toast-deleted');

                        //             // Store undo data
                        //             window.lastRemovedNotify = {
                        //                 html: removedRowHTML,
                        //                 email: email,
                        //                 client_id: client_id
                        //             };
                        //         } else {
                        //             showError(response.message || 'Error removing email.');
                        //         }
                        //     },
                        //     error: function() {
                        //         showError('Server error while removing the email.');
                        //     }
                        // });
                    });

                    // undo remove notify email
                    $(document).on('click', '.undo-delete-notify-email', function() {

                        if (!window.lastRemovedNotify) return;

                        let restoredHTML = window.lastRemovedNotify.html;
                        let email = window.lastRemovedNotify.email;
                        let client_id = window.lastRemovedNotify.client_id;

                        // remove placeholder "no emails" row
                        $('.no-notify-email-row').remove();

                        // restore deleted row
                        $('.added-notify-email tbody').append(restoredHTML);

                        // hide undo button
                        $('.undo-notify-wrapper').empty();

                        // AJAX to restore in backend
                        // $.ajax({
                        //     url: '{{ url('add-notify-email') }}',
                        //     method: 'POST',
                        //     data: {
                        //         _token: '{{ csrf_token() }}',
                        //         email: email,
                        //         client_id: client_id
                        //     },
                        //     success: function(response) {
                        //         if (response.status === 'success') {
                        //             showToast('notify-email-toast-restored');
                        //             if ($('.added-notify-email tbody tr').length === 0) {
                        //                 $('.btn-notify-email').prop('disabled', true).removeClass(
                        //                         'btn-primary')
                        //                     .addClass('cancel-btn');
                        //             } else {
                        //                 $('.btn-notify-email').prop('disabled', false).removeClass(
                        //                         'cancel-btn')
                        //                     .addClass('btn-primary');
                        //             }
                        //         }
                        //     }
                        // });

                        // clear memory
                        window.lastRemovedNotify = null;
                    });

                    // pinned message
                    // ADD pinned message (HTML only)
                    $(document).on('click', '.add-pinned-message', function(event) {

                        var message = $('#pin_message').val().trim();
                        var client_id = $(this).attr('data-id');

                        if (message === '') {
                            showError('Please enter a pinned message.');
                            return;
                        }

                        // max 5 limit check
                        let currentCount = $('.added-pinned-message tbody .pinned-message-item').length;
                        if (currentCount >= 5) {
                            showError('You can add a maximum of 5 pinned messages.');
                            return;
                        }

                        // Check uniqueness
                        var exists = false;
                        $('.added-pinned-message tbody .pinned-message').each(function() {
                            if ($(this).text().trim().toLowerCase() === message.toLowerCase()) {
                                exists = true;
                                return false;
                            }
                        });

                        if (exists) {
                            showError('This message is already added.');
                            return;
                        }

                        // Disable button for safety
                        var $btn = $(this);
                        $btn.prop('disabled', true);

                        $('.no-pinned-message-row').hide(); // remove placeholder row

                        // ONLY add HTML — no Ajax
                        $('.added-pinned-message tbody').append(`
                            <tr class="pinned-message-item" data-client-id="${client_id}">
                                <td class="py-2 border-0" style="border-radius: 13px 0 0 13px;">
                                    <span class="fw-300 text-darkgrey font-titillium fs-15 pinned-message">${message}</span>
                                </td>
                                <td class="py-2 border-0 text-right align-middle" width="50" style="border-radius: 0 13px 13px 0;">
                                    <a href="javascript:;" class="align-items-center d-flex drag-handle justify-content-center mb-0 remove-pinned-message"
                                    data-message="${message}" data-client-id="${client_id}">
                                        <i class="fa-light fa-circle-xmark text-grey fs-18"></i>
                                    </a>
                                </td>
                            </tr>
                        `);

                        $('#pin_message').val('');
                        showToast('pinned-message-toast-added');

                        $btn.prop('disabled', false);
                    });
                    // REMOVE pinned message (HTML only)
                    $(document).on('click', '.remove-pinned-message', function(e) {
                        e.preventDefault();

                        let message = $(this).data('message');
                        let client_id = $(this).data('client-id');
                        let $row = $(this).closest('tr');

                        let removedRowHTML = $row.prop('outerHTML');

                        // Remove row visually
                        $row.fadeOut(200, function() {
                            $(this).remove();

                            if ($('.added-pinned-message tbody tr').length === 0) {
                                //                     $('.added-pinned-message tbody').append(`
                    //     <tr class="no-pinned-message-row">
                    //         <td colspan="2" class="font-titillium text-darkgrey fw-400 fs-14 text-center">
                    //             No messages added
                    //         </td>
                    //     </tr>
                    // `);
                                $('.no-pinned-message-row').show();
                            }
                        });

                        // Show toast (HTML only)
                        showToast('pinned-message-toast-deleted');

                        // Save last removed message for Undo
                        window.lastRemovedPinnedMessage = {
                            html: removedRowHTML,
                            message: message,
                            client_id: client_id
                        };
                    });
                    // UNDO delete pinned message (HTML only)
                    $(document).on('click', '.undo-delete-pinned-message', function() {

                        if (!window.lastRemovedPinnedMessage) return;

                        let restoredHTML = window.lastRemovedPinnedMessage.html;

                        $('.no-pinned-message-row').hide(); // remove placeholder

                        // Restore deleted row
                        $('.added-pinned-message tbody').append(restoredHTML);

                        showToast('pinned-message-toast-recovered');

                        // Clear memory
                        window.lastRemovedPinnedMessage = null;
                    });


                    // Save pinned messages on OK button click
                    $(document).on('click', '.btn-add-pin-messages', function() {

                        let client_id = $(this).attr('data-client-id');

                        // collect all messages
                        let messages = [];
                        $('.added-pinned-message tbody .pinned-message').each(function() {
                            let msg = $(this).text().trim();
                            if (msg !== "") messages.push(msg);
                        });

                        $.ajax({
                            url: '{{ url('save-pinned-messages') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                client_id: client_id,
                                messages: messages
                            },

                            beforeSend: function() {
                                $('.btn-add-pin-messages').prop('disabled', true);

                                // Hide main modal first
                                $('#pinMessageModal').modal('hide');

                                // Wait until the first modal fully hides (VERY IMPORTANT)
                                $('#pinMessageModal').on('hidden.bs.modal', function() {
                                    $('#pinnedMessageModal').modal('show');
                                });
                            },

                            success: function(res) {
                                if (res.status === 'success') {

                                    // hide the modal AFTER showing success
                                    setTimeout(() => {
                                        $('#pinnedMessageModal').modal('hide');
                                    }, 1000);
                                    setTimeout(() => {
                                        Dashmix.helpers('notify', {
                                            type: 'success',
                                            message: `
                            <div>
                                <div class="font-titillium" style="font-weight: 800; color: #4EA833; font-size: 15pt;">Pinned Messages</div>
                                <div class="d-flex align-items-center">
                                    <div style="font-size: 14pt; margin-right: 8px;"><i class="fa-thin fa-message-exclamation"></i></div>
                                    <div>Pinned messages set successfully.</div>
                                </div>
                            </div>
                        `,
                                            allow_dismiss: true,
                                            delay: 3000,
                                            align: 'center',
                                        });
                                    }, 1100);
                                } else {
                                    setTimeout(() => {
                                        $('#pinnedMessageModal').modal('hide');
                                    }, 1000);
                                    showError(res.message || "Error adding pin messages.");
                                }
                            },

                            error: function() {
                                setTimeout(() => {
                                    $('#pinnedMessageModal').modal('hide');
                                }, 1000);
                                showError("Server error sending email.");
                            },

                            complete: function() {
                                $('.btn-add-pin-messages').prop('disabled', false);
                                setTimeout(() => {
                                    showData(client_id);
                                }, 1000);
                            }
                        });

                    });



                    // End pinned message

                    $(document).on("click", ".copy-detail-line", function(e) {
                        e.stopPropagation();

                        const text = $(this).data("text").trim();

                        // Copy to clipboard
                        navigator.clipboard.writeText(text).then(() => {

                            // Remove old toast if exists
                            $(".clipboard-toast").remove();

                            // Create toast element
                            const toast = $(`
                                <div class="clipboard-toast-detailline">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-circle-check mr-2"></i>
                                        <span class="font-titillium fs-14 text-darkgrey">
                                            Copied to clipboard!
                                        </span>
                                    </div>
                                </div>
                            `);

                            // Append to body
                            $("body").append(toast);

                            // Get button position
                            const btnOffset = $(this).offset();
                            const btnWidth = $(this).outerWidth();

                            // Position toast above button
                            toast.css({
                                top: btnOffset.top - 35, // 35px above button
                                left: btnOffset.left + (btnWidth / 2) -
                                    80 // center toast (160px width adjust)
                            });

                            // Show animation
                            setTimeout(() => toast.addClass("show"), 10);

                            // Hide after 1.3s
                            setTimeout(() => {
                                toast.removeClass("show");
                                setTimeout(() => toast.remove(), 200);
                            }, 1300);

                        });
                    });




                    // SEND NOTIFICATION EMAIL TO SELECTED RECIPIENTS
                    $(document).on('click', '.btn-notify-email', function() {
                        let client_id = $(this).data('client-id');

                        // collect all email rows
                        let emails = [];
                        $('.added-notify-email .email-notify').each(function() {
                            emails.push($(this).text().trim());
                        });

                        if (emails.length === 0) {
                            showError("No recipient emails found!");
                            return;
                        }

                        $.ajax({
                            url: '{{ url('send-renewal-notification') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                client_id: client_id,
                                emails: emails
                            },
                            beforeSend: function() {
                                $('.btn-notify-email').prop('disabled', true);
                                $('#EmailModal').modal('hide');
                                $('#notifyEmailsModal').modal('show');
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Dashmix.helpers('notify', {
                                        type: 'success',
                                        message: `
            <div>
                <div class="font-titillium" style="font-weight: 800; color: #4EA833; font-size: 15pt;">Email Notify</div>
                <div class="d-flex align-items-center">
                    <div style="font-size: 14pt; margin-right: 8px;"><i class="fa-thin fa-envelope"></i></div>
                    <div>Email notifications sent to selected recipients.</div>
                </div>
            </div>
        `,
                                        allow_dismiss: true,
                                        delay: 3000,
                                        align: 'center',
                                    });
                                } else {
                                    showError(res.message || "Error sending email");
                                }
                            },
                            error: function() {
                                $('#notifyEmailsModal').modal('hide');
                                $('.btn-notify-email').prop('disabled', false);
                                showError("Server error sending email.");
                            },
                            complete: function() {
                                $('.btn-notify-email').prop('disabled', false);
                                $('#notifyEmailsModal').modal('hide');
                            }
                        });
                    });

                    function showError(message) {
                        Dashmix.helpers('notify', {
                            type: 'danger',
                            message: `
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div style="font-size: 30pt; margin-right: 8px;"><i class="fa-light fa-triangle-exclamation text-orange"></i></div>
                                        <div class="mx-auto text-grey fw-300 fs-18">${message}</div>
                                    </div>
                                </div>
                            `,
                            allow_dismiss: true,
                            delay: 3000,
                            align: 'center',
                        });
                    }

                    function showToast(cls, timeout = 3000) {
                        const $el = $('.' + cls);
                        $el.stop(true, true);
                        const prev = $el.data('toastTimeout');
                        if (prev) clearTimeout(prev);

                        $el.fadeIn(200);
                        const t = setTimeout(() => {
                            $el.fadeOut(200);
                            $el.removeData('toastTimeout');
                        }, timeout);
                        $el.data('toastTimeout', t);
                    }
                    $(document).on('mouseenter', '.email-notify-item', function() {
                        $(this).find('.drag-handle').css('opacity', '1');
                    }).on('mouseleave', '.email-notify-item', function() {
                        $(this).find('.drag-handle').css('opacity', '0');
                    });
                    $(document).on('mouseenter', '.asset-item', function() {
                        $(this)
                            .find('input.asset-checkbox:not(:checked)')
                            .css('visibility', 'visible') // allow fading
                            .stop(true)
                            .animate({
                                opacity: 1
                            }, 150);
                    });

                    $(document).on('mouseleave', '.asset-item', function() {
                        $(this)
                            .find('input.asset-checkbox:not(:checked)')
                            .stop(true)
                            .animate({
                                opacity: 0
                            }, 150, function() {
                                $(this).css('visibility', 'hidden'); // hide again so it's not focusable
                            });
                    });

                    $(document).on('mouseenter', '.pinned-message-item', function() {
                        $(this).find('.drag-handle').css('opacity', '1');
                    }).on('mouseleave', '.pinned-message-item', function() {
                        $(this).find('.drag-handle').css('opacity', '0');
                    });
                    $(document).on('mouseenter', '.showAssetsModal_2', function() {
                        $(this).find('i').removeClass('fa-thin').addClass('fa-solid');
                    }).on('mouseleave', '.showAssetsModal_2', function() {
                        $(this).find('i').removeClass('fa-solid').addClass('fa-thin');
                    });
                    $(document).on('mouseenter', '.dropdown-toggle', function() {
                        $(this).find('i').removeClass('fa-thin').addClass('fa-solid');
                    }).on('mouseleave', '.dropdown-toggle', function() {
                        $(this).find('i').removeClass('fa-solid').addClass('fa-thin');
                    });
                    $(document).on('mouseenter', '.bubble-server-icon', function() {
                        $(this).find('i').removeClass('fa-thin').addClass('fa-solid');
                    }).on('mouseleave', '.bubble-server-icon', function() {
                        $(this).find('i').removeClass('fa-solid').addClass('fa-thin');
                    });
                    $(document).on('mouseenter', '.asset-trigger', function() {
                        $(this).find('i').removeClass('fa-thin').addClass('fa-duotone fa-solid');
                    }).on('mouseleave', '.asset-trigger', function() {
                        $(this).find('i').removeClass('fa-duotone fa-solid').addClass('fa-thin');
                    });
                    $(document).on('mouseenter', '.asset-trigger-read', function() {
                        $(this).find('i').removeClass('fa-thin').addClass('fa-duotone fa-solid');
                    }).on('mouseleave', '.asset-trigger-read', function() {
                        $(this).find('i').removeClass('fa-duotone fa-solid').addClass('fa-thin');
                    });

                    $(document).on('click', '#downloadStudentCSV', function() {
                        var table = $('.student-table');
                        let rows = [
                            ["Distributor Name", "Reference No.", "Sales Order No."]
                        ];
                        table.find("tbody tr").each(function() {
                            let distributor_name = $(this).find('#distributor_name').text(),
                                reference_no = $(this).find('#reference_no').text(),
                                sales_order_no = $(this).find('#sales_order_no').text();


                            rows.push([
                                distributor_name,
                                reference_no,
                                sales_order_no
                            ]);
                        });

                        let csv = rows.map(e => e.join(",")).join("\n");

                        let blob = new Blob([csv], {
                            type: "text/csv"
                        });

                        let url = window.URL.createObjectURL(blob);

                        let a = document.createElement("a");
                        a.href = url;
                        let timestamp = new Date().toISOString().replace(/[:.-]/g, "");
                        a.download = "distributer-details-" + timestamp + ".csv";
                        a.click();
                        showToast('distributer-download-toast')
                    })

                    $(document).on('click', '#downloadPurchasingCSV', function() {
                        var table = $('.purchasing-table');
                        let rows = [
                            ["Estimate No.", "Sales Order No.", "Invoice No.", "Invoice Date", "PO No.",
                                "PO Date"
                            ]
                        ];
                        table.find("tbody tr").each(function() {
                            let estimate_no = $(this).find('#estimate_no').text(),
                                sale_order_no = $(this).find('#sale_order_no').text(),
                                invoice_no = $(this).find('#invoice_no').text(),
                                invoice_date = $(this).find('#invoive_date').text(),
                                po_no = $(this).find('#po_no').text(),
                                po_date = $(this).find('#po_date').text();


                            rows.push([
                                estimate_no,
                                sale_order_no,
                                invoice_no,
                                invoice_date,
                                po_no,
                                po_date
                            ]);
                        });

                        let csv = rows.map(e => e.join(",")).join("\n");

                        let blob = new Blob([csv], {
                            type: "text/csv"
                        });

                        let url = window.URL.createObjectURL(blob);

                        let a = document.createElement("a");
                        a.href = url;
                        let timestamp = new Date().toISOString().replace(/[:.-]/g, "");
                        a.download = "purchasing-details-" + timestamp + ".csv";
                        a.click();
                        showToast('purchase-download-toast')
                    })
                    $(document).on('click', '#downloadContractCSV', function() {
                        var table = $('.contract-table');

                        // Contract values
                        let contract_currency = $('#contract_currency').text().trim();
                        let contract_total_amount = $('#contract_total_amount').text().trim();

                        // CSV rows array
                        let rows = [];

                        // Contract summary rows
                        // rows.push(["Currency", contract_currency]);
                        // rows.push(["Total Amount", contract_total_amount]);
                        // rows.push([]); // empty row separator

                        // Table header
                        rows.push(["Qty", "PN #", "Type", "Description", "Cost"]);

                        // Loop through ALL line items
                        table.find("tbody tr").each(function() {
                            let qty = $(this).find(".qty").text().trim();
                            let pn_no = $(this).find(".pn_no").text().trim();
                            let type = $(this).find(".contract_detail_type").text().trim();
                            let desc = $(this).find(".detail_comments").text().trim();
                            let cost = $(this).find(".msrp").text().trim();

                            rows.push([qty, pn_no, type, desc, cost]);
                        });

                        // Convert to CSV
                        let csv = rows.map(r => r.join(",")).join("\n");
                        let blob = new Blob([csv], {
                            type: "text/csv"
                        });
                        let url = window.URL.createObjectURL(blob);

                        let a = document.createElement("a");
                        a.href = url;

                        let timestamp = new Date().toISOString().replace(/[:.-]/g, "");
                        a.download = "Contract-details-" + timestamp + ".csv";

                        a.click();
                        showToast('contract-details-download-toast')
                    })
                    $(document).on('click', '#downloadAuditLogCSV', function() {
                        var table = $('.contract-table');

                        // Contract values
                        let contract_currency = $('#contract_currency').text().trim();
                        let contract_total_amount = $('#contract_total_amount').text().trim();

                        // CSV rows array
                        let rows = [];

                        // Contract summary rows
                        // rows.push(["Currency", contract_currency]);
                        // rows.push(["Total Amount", contract_total_amount]);
                        // rows.push([]); // empty row separator

                        // Table header
                        rows.push(["User", "Date/Time", "Message"]);

                        // Loop through ALL line items
                        $(".audit-log-item").each(function() {
                            let user = $(this).find(".user_name").val().trim();
                            let message = $(this).find(".adit-message").text().trim();
                            let date_time = $(this).find("#date_time").val().trim();

                            rows.push([user, date_time, message]);
                        });

                        // Convert to CSV
                        let csv = rows.map(r => r.join(",")).join("\n");
                        let blob = new Blob([csv], {
                            type: "text/csv"
                        });
                        let url = window.URL.createObjectURL(blob);

                        let a = document.createElement("a");
                        a.href = url;

                        let timestamp = new Date().toISOString().replace(/[:.-]/g, "");
                        a.download = "Audit-Log-" + timestamp + ".csv";

                        a.click();
                        showToast('audit-log-download-toast')
                    })

                    $(document).on('mouseenter', '.audit-sort-toggle', function() {
                        $(this).find('i').removeClass('fa-light').addClass('fa-solid');
                    });

                    $(document).on('mouseleave', '.audit-sort-toggle', function() {
                        $(this).find('i').removeClass('fa-solid').addClass('fa-light');
                    });

                    $(document).on('click', '.audit-sort-toggle', function() {
                        const $toggle = $(this);
                        const $tabPane = $toggle.closest('.tab-pane');
                        const $items = $tabPane.find('.audit-log-item');

                        if ($items.length < 2) {
                            return;
                        }

                        $($items.get().reverse()).appendTo($items.first().parent());

                        const currentOrder = ($toggle.attr('data-order') || 'desc').toLowerCase();
                        const nextOrder = currentOrder === 'desc' ? 'asc' : 'desc';
                        const $icon = $toggle.find('i');

                        $toggle.attr('data-order', nextOrder);
                        $icon.removeClass('fa-circle-sort-up fa-circle-sort-down')
                            .addClass(nextOrder === 'desc' ? 'fa-circle-sort-up' : 'fa-circle-sort-down');
                    });

                    // SHOW TOOLTIP
                    $(document).on('mouseenter', '.text-cell', function() {

                        let el = $(this)[0];
                        let fullText = $(this).text().trim();

                        // check overflow: scrollWidth > clientWidth
                        if (el.scrollWidth > el.clientWidth) {
                            console.log('herere 2');

                            let tooltip = $('<div class="custom-tooltip"></div>');
                            tooltip.text(fullText);

                            $("body").append(tooltip);

                            // Position tooltip
                            let pos = $(this).offset();
                            let width = $(this).outerWidth();

                            tooltip.css({
                                top: pos.top - tooltip.outerHeight() - 8,
                                left: pos.left + (width / 2) - (tooltip.outerWidth() / 2)
                            });

                            $(this).data("tooltip", tooltip);
                        }
                    });


                    // HIDE TOOLTIP
                    $(document).on('mouseleave', '.text-cell', function() {
                        let tooltip = $(this).data("tooltip");
                        if (tooltip) tooltip.remove();
                    });

                    $(document).on('mouseenter', '.short-cards', function() {
                        var copy_button = $(this).find('.copy-info');
                        if (copy_button) {
                            copy_button.fadeIn()
                        }
                    })

                    $(document).on('mouseleave', '.short-cards', function() {
                        var copy_button = $(this).find('.copy-info');
                        if (copy_button) {
                            copy_button.fadeOut()
                        }
                    })
                    $(document).on('mouseenter', '.contract-table tbody tr td', function() {
                        var copy_button = $(this).find('.copy-detail-line');
                        if (copy_button) {
                            copy_button.fadeIn()
                        }
                    })

                    $(document).on('mouseleave', '.contract-table tbody tr td', function() {
                        var copy_button = $(this).find('.copy-detail-line');
                        if (copy_button) {
                            copy_button.fadeOut()
                        }
                    })

                    $(document).on('mouseenter', '.copy-info', function() {
                        $(this).find('i').addClass('fa-solid').removeClass('fa-thin');
                    });

                    $(document).on('mouseleave', '.copy-info', function() {
                        $(this).find('i').addClass('fa-thin').removeClass('fa-solid');
                    });
                    $(document).on('mouseenter', '.copy-detail-line', function() {
                        $(this).find('i').addClass('fa-solid').removeClass('fa-thin');
                    });

                    $(document).on('mouseleave', '.copy-detail-line', function() {
                        $(this).find('i').addClass('fa-thin').removeClass('fa-solid');
                    });
                    $(document).on('mouseenter', '.clear-icon', function() {
                        $(this).addClass('fa-solid').removeClass('fa-light');
                    });

                    $(document).on('mouseleave', '.clear-icon', function() {
                        $(this).addClass('fa-light').removeClass('fa-solid');
                    });
                    $(document).on('mouseenter', '.delete-comment-client', function() {
                        $(this).find('i').addClass('fa-solid').removeClass('fa-thin');
                    });

                    $(document).on('mouseleave', '.delete-comment-client', function() {
                        $(this).find('i').addClass('fa-thin').removeClass('fa-solid');
                    });
                    $(document).on('mouseenter', '.edit-comment-contract', function() {
                        $(this).find('i').addClass('fa-solid').removeClass('fa-thin');
                    });

                    $(document).on('mouseleave', '.edit-comment-contract', function() {
                        $(this).find('i').addClass('fa-thin').removeClass('fa-solid');
                    });
                    $(document).on('mouseenter', '.tooltip-copy', function() {
                        $(this).addClass('fa-solid').removeClass('fa-thin');
                    });

                    $(document).on('mouseleave', '.tooltip-copy', function() {
                        $(this).addClass('fa-thin').removeClass('fa-solid');
                    });
                    // $(document).on('keyup change', 'input[name=contract_start_date], input[name=contract_end_date]', function () {
                    //     var contract_start_date = $('input[name=contract_start_date]');
                    //     var contract_end_date = $('input[name=contract_end_date]');
                    //     console.log(contract_start_date.val());

                    //     if (!contract_end_date.val()) {
                    //         contract_end_date.addClass('text-grey').removeClass('text-placeholder');
                    //     } else {
                    //         contract_end_date.addClass('text-placeholder').removeClass('text-grey');
                    //     }

                    //     if (!contract_start_date.val()) {
                    //         contract_start_date.addClass('text-grey').removeClass('text-placeholder');
                    //     } else {
                    //         contract_start_date.addClass('text-placeholder').removeClass('text-grey');
                    //     }
                    // });


                    // $(document).on('click', '.copy-info', function() {
                    //     const text = $(this).attr('data-text');
                    //     const toast = $(this).closest('.short-cards').find('.clipboard-toast');

                    //     // 2. Copy to clipboard
                    //     navigator.clipboard.writeText(text).then(() => {
                    //         // 3. Show toast
                    //         toast.fadeIn();

                    //         // 4. Auto-hide after 2 seconds
                    //         setTimeout(() => toast.fadeOut(), 2000);
                    //     }).catch(err => {
                    //         console.error('Copy failed', err);
                    //         showError('Copy failed – check console');
                    //     });
                    // })
                    $(document).on('click', '.copy-info', function(e) {
                        const text = $(this).attr('data-text');

                        // Copy to clipboard
                        navigator.clipboard.writeText(text).then(() => {

                            // Create toast HTML dynamically
                            const toast = $(`
                                <div class="clipboard-toast" style="
                                    position: absolute;
                                    top: ${e.pageY + 10}px;
                                    left: ${e.pageX - 132}px;
                                    z-index: 9999;
                                    display: none;
                                    background: #fff;
                                    padding: 8px 12px;
                                    border-radius: 6px;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                                    width: fit-content;
                                    height: fit-content;
                                ">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-circle-check mr-2"></i>
                                        <span class="font-titillium fs-14 text-darkgrey">Copied to clipboard!</span>
                                    </div>
                                </div>
                            `);

                            // Append to body
                            $('body').append(toast);

                            // Show toast
                            toast.fadeIn(200);

                            // Auto-hide after 2 seconds and remove from DOM
                            setTimeout(() => toast.fadeOut(200, function() {
                                $(this).remove();
                            }), 2000);

                        }).catch(err => {
                            console.error('Copy failed', err);
                            alert('Copy failed – check console');
                        });
                    });

                    $(document).on('click', '.btn-edit, .btn-edit-3dot', function(e) {
                        e.stopPropagation();
                        var id = $(this).attr('data');
                        var url = "{{ url('edit-vacation') }}?id=" + id;

                        $(this).closest('.dropdown-menu')?.removeClass('show');

                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                // Hide the old section
                                $('#showData').addClass('d-none');
                                $('#showCards').addClass('d-none');
                                $('.read-nav-tabs, .read-header').addClass('d-none');
                                $('.edit-nav-tabs').removeClass('d-none');
                                $('.edit-sub-header, .edit-header').removeClass(
                                    'd-none bg-add-tab bg-clone-tab bg-renew-tab');
                                $('.edit-sub-header, .edit-header').addClass('bg-edit-tab');
                                $('.img-icon').addClass('d-none');
                                $('.dynamic-icon').removeClass('d-none fa-clone fa-rotate');
                                $('.dynamic-icon').addClass('fa-file-pen');
                                $('.update-icon').attr('data-original-title', 'Save');
                                $('.update-continue-icon').addClass('d-none');

                                // 🔹 Insert the new HTML first
                                $('#showEditData').removeClass('d-none').html(response);

                                // Reinitialize selectpicker for new content
                                $('.selectpicker').selectpicker();

                                // 🔹 Reinitialize Dashmix helpers AFTER content is inserted
                                if (typeof Dashmix !== 'undefined') {
                                    if (typeof Dashmix.helpersOnLoad === 'function') {
                                        Dashmix.helpersOnLoad(['js-flatpickr']);
                                    } else if (typeof Dashmix.helpers === 'function') {
                                        Dashmix.helpers(['flatpickr']);
                                    }
                                }


                                // ✅ Fallback: direct Flatpickr init
                                if (typeof flatpickr !== 'undefined') {
                                    $('#showEditData .js-flatpickr').flatpickr({
                                        altInput: true,
                                        dateFormat: 'Y-m-d',
                                        allowInput: true
                                    });
                                } else {
                                    console.warn('⚠️ flatpickr is not loaded.');
                                }
                                // 🔹 Reset tabs to first (Main)
                                $('.sub-nav-tabs.edit-nav-tabs .nav-link').removeClass('active');
                                $('.tab-pane').removeClass('show active');
                                $('#nav-main-tab-vacation-edit').addClass('active');
                                $('#nav-main-vacation-edit').addClass('show active');
                            },
                            error: function() {
                                showError('Failed to load edit page.');
                            }
                        });
                    });

                    const editDiv = document.getElementById('showEditData');

                    const observer = new MutationObserver(() => {
                        if ($(editDiv).is(':visible')) {

                            console.log("Edit section is now visible — loading data...");

                            setTimeout(() => {

                                // getEditDetailLines();
                            }, 500);

                            observer.disconnect(); // run only once
                        }
                    });

                    observer.observe(editDiv, {
                        attributes: true,
                        attributeFilter: ['class', 'style']
                    });

                    // function getEditDetailLines() {
                    //     // get data 
                    //     $.ajax({
                    //         type: 'get',
                    //         url: "{{ url('get-contract-distributor') }}",
                    //         data: {
                    //             id: '{{ @$_GET['id'] }}'
                    //         },
                    //         success: function(res) {
                    //             console.log(res);

                    //             distributor_array_edit = []; // clear old values

                    //             for (var i = 0; i < res.length; i++) {

                    //                 distributor_array_edit.push({
                    //                     key: distributorKeyEdit,
                    //                     distributer: res[i].distributor_id, // id
                    //                     distributername: res[i].distributor_name, // name
                    //                     reference: res[i].reference_no,
                    //                     salesorder: res[i].sales_order_no
                    //                 });

                    //                 distributorKeyEdit++;
                    //             }

                    //             showDistributer();
                    //         }
                    //     });

                    //     $.ajax({

                    //         type: 'get',
                    //         url: "{{ url('get-contract-purchasing') }}",
                    //         data: {
                    //             id: '{{ @$_GET['id'] }}'
                    //         },
                    //         success: function(res) {

                    //             purchasing_array_edit = []; // clear old values

                    //             for (var i = 0; i < res.length; i++) {

                    //                 purchasing_array_edit.push({
                    //                     key: purchasingKeyEdit,
                    //                     estimate_no: res[i].estimate_no,
                    //                     sales_order_no: res[i].sales_order_no,
                    //                     invoice_no: res[i].invoice_no,
                    //                     invoice_date: res[i].invoice_date,
                    //                     po_no: res[i].po_no,
                    //                     po_date: res[i].po_date
                    //                 });

                    //                 purchasingKeyEdit++;
                    //             }

                    //             showPurchasing();
                    //         }
                    //     });
                    // }
                    $(document).on('click', '.btn-clone-confirm', function(e) {
                        e.stopPropagation();
                        var id = $(this).attr('data-id');
                        var url = "{{ url('clone-vacation') }}?id=" + id;
                        $(this).closest('.dropdown-menu')?.removeClass('show');
                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                $('#confirmCloneModal').modal('hide');
                                $(this).attr({
                                    'data': '',
                                    'data-id': ''
                                });
                                // Hide the old section
                                $('#showData').addClass('d-none');
                                $('#showCards').addClass('d-none');
                                $('.read-nav-tabs, .read-header').addClass('d-none');
                                $('.edit-nav-tabs').removeClass('d-none');
                                $('.edit-sub-header, .edit-header').removeClass(
                                    'd-none bg-add-tab bg-edit-tab bg-renew-tab');
                                $('.edit-sub-header, .edit-header').addClass('bg-clone-tab');
                                $('.img-icon').addClass('d-none');
                                $('.dynamic-icon').removeClass('d-none fa-file-pen fa-rotate');
                                $('.dynamic-icon').addClass('fa-clone');
                                $('.update-icon').attr('data-original-title', 'Clone');
                                $('.update-continue-icon').addClass('d-none');

                                // 🔹 Insert the new HTML first
                                $('#showEditData').removeClass('d-none').html(response);

                                // Reinitialize selectpicker for new content
                                $('.selectpicker').selectpicker();

                                // 🔹 Reinitialize Dashmix helpers AFTER content is inserted
                                if (typeof Dashmix !== 'undefined') {
                                    if (typeof Dashmix.helpersOnLoad === 'function') {
                                        Dashmix.helpersOnLoad(['js-flatpickr']);
                                    } else if (typeof Dashmix.helpers === 'function') {
                                        Dashmix.helpers(['flatpickr']);
                                    }
                                }


                                // ✅ Fallback: direct Flatpickr init
                                if (typeof flatpickr !== 'undefined') {
                                    $('#showEditData .js-flatpickr').flatpickr({
                                        altInput: true,
                                        dateFormat: 'Y-m-d',
                                        allowInput: true
                                    });
                                } else {
                                    console.warn('⚠️ flatpickr is not loaded.');
                                }
                                // 🔹 Reset tabs to first (Main)
                                $('.sub-nav-tabs.edit-nav-tabs .nav-link').removeClass('active');
                                $('.tab-pane').removeClass('show active');
                                $('#nav-main-tab-vacation-edit').addClass('active');
                                $('#nav-main-vacation-edit').addClass('show active');
                            },
                            error: function() {
                                showError('Failed to load edit page.');
                            }
                        });
                    });
                    $(document).on('click', '.btn-add', function(e) {
                        e.stopPropagation();

                        var url = "{{ url('add-vacation') }}";

                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                // Hide the old section
                                $('#showData').addClass('d-none');
                                $('#showCards').addClass('d-none');
                                $('.read-nav-tabs, .read-header').addClass('d-none');
                                $('.edit-nav-tabs').removeClass('d-none');

                                $('.edit-sub-header, .edit-header').removeClass(
                                    'd-none bg-edit-tab bg-clone-tab bg-renew-tab');
                                $('.edit-sub-header, .edit-header').addClass('bg-add-tab');
                                $('.dynamic-icon').addClass('d-none');
                                $('.img-icon').removeClass('d-none');
                                $('.update-icon').attr('data-original-title', 'Save');
                                $('.update-continue-icon').removeClass('d-none');
                                $('.update-continue-icon').attr('data-original-title',
                                'Add + Continue');

                                // 🔹 Insert the new HTML first
                                $('#showEditData').removeClass('d-none').html(response);

                                // Reinitialize selectpicker for new content
                                $('.selectpicker').selectpicker();

                                // 🔹 Reinitialize Dashmix helpers AFTER content is inserted
                                if (typeof Dashmix !== 'undefined') {
                                    if (typeof Dashmix.helpersOnLoad === 'function') {
                                        Dashmix.helpersOnLoad(['js-flatpickr']);
                                    } else if (typeof Dashmix.helpers === 'function') {
                                        Dashmix.helpers(['flatpickr']);
                                    }
                                }


                                // ✅ Fallback: direct Flatpickr init
                                if (typeof flatpickr !== 'undefined') {
                                    $('#showEditData .js-flatpickr').flatpickr({
                                        altInput: true,
                                        dateFormat: 'Y-m-d',
                                        allowInput: true
                                    });
                                } else {
                                    console.warn('⚠️ flatpickr is not loaded.');
                                }
                                // 🔹 Reset tabs to first (Main)
                                $('.sub-nav-tabs.edit-nav-tabs .nav-link').removeClass('active');
                                $('.tab-pane').removeClass('show active');
                                $('#nav-main-tab-vacation-edit').addClass('active');
                                $('#nav-main-vacation-edit').addClass('show active');
                            },
                            error: function() {
                                showError('Failed to load edit page.');
                            }
                        });
                    });



                    function showDeleteNotify(title, message, type_id, client_id, type) {
                        Dashmix.helpers('notify', {
                            type: 'success',
                            message: `
                                <div>
                                    <div class="font-titillium" style="font-weight: 800; color: #c41e39; font-size: 15pt;">${title}</div>
                                    <div class="d-flex align-items-center my-2">
                                        <div style="font-size: 14pt; margin-right: 8px;"><i class="fa-light fa-trash"></i></div>
                                        <div>${message}</div>
                                    </div>
                                    <div>
                                        <a href="javascript:void(0);" class="text-secondary fw-600 undo-deleted-${type} float-right fs-18 my-1" data-${type}-id="${type_id}" data-client-id="${client_id}">Undo</a>
                                    </div>
                                </div>
                            `,
                            allow_dismiss: true,
                            delay: 3000,
                            align: 'center',
                        });
                    }

                    function showSuccessNotify(title, message) {
                        Dashmix.helpers('notify', {
                            type: 'success',
                            message: `
                                    <div>
                                        <div class="font-titillium" style="font-weight: 800; color: #4EA833; font-size: 15pt;">${title}</div>
                                        <div class="d-flex align-items-center">
                                            <div style="font-size: 14pt; margin-right: 8px;"><i class="fa-thin fa-circle-check"></i></div>
                                            <div>${message}</div>
                                        </div>
                                    </div>
                                `,
                            allow_dismiss: true,
                            delay: 3000,
                            align: 'center',
                        });
                    }



                    $(document).on('click', '.delete-comment-client', function() {
                        const commentId = $(this).data('id');
                        const clientId = $(this).data('client-id');

                        // Populate modal fields
                        $('#deleteCommentModal #comment_id').val(commentId);
                        $('#deleteCommentModal #client_id').val(clientId);

                        // Show modal
                        $('#deleteCommentModal').modal('show');
                    });
                    $(document).on('click', '.edit-comment-client', function() {
                        const commentId = $(this).data('id');
                        const clientId = $(this).data('client-id');
                        const comment = $(this).data('comment').replace(/<br\s*\/?>/g,
                            "\n"); // convert nl2br back to newlines

                        // Populate modal fields
                        $('#comment_id').val(commentId);
                        $('#client_id').val(clientId);
                        $('#comment_text').val(comment);

                        // Show modal
                        $('#editCommentModal').modal('show');
                    });

                    // Handle Update button click
                    $('#updateCommentBtn').on('click', function() {
                        const formData = $('#editCommentForm').serialize();

                        $.ajax({
                            url: '{{ url('update-comment-contract') }}',
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                $('#editCommentModal').modal('hide');
                                showSuccessNotify('Comment Updated Successfully',
                                    'Comment has been updated successfully.');
                                localStorage.setItem('vacation_active_tab',
                                    'nav-comments-tab-contract');
                                setTimeout(function() {
                                    location.reload();
                                }, 4000);
                            },
                            error: function() {
                                showError('Error updating comment.');
                            }
                        });
                    });
                    $('#insertComment').on('submit', function() {
                        localStorage.setItem('vacation_active_tab_target', 'nav-comments-client');
                        localStorage.setItem('vacation_active_tab', 'nav-comments-tab-client');
                    });
                    $('#insertAttachment').on('submit', function() {
                        localStorage.setItem('vacation_active_tab_target', 'nav-attachments-client');
                        localStorage.setItem('vacation_active_tab', 'nav-attachments-tab-client');
                    });
                    $('#deleteCommentBtn').on('click', function() {
                        const formData = $('#deleteCommentForm').serialize();

                        $.ajax({
                            url: '{{ url('delete-comment-client') }}',
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                $('#deleteCommentModal').modal('hide');
                                showDeleteNotify('Deleted Successfully',
                                    'Comment has been deleted successfully.', response.comment_id,
                                    response.client_id, 'comment');
                                localStorage.setItem('vacation_active_tab_target',
                                    'nav-comments-client');
                                localStorage.setItem('vacation_active_tab',
                                    'nav-comments-tab-client');
                                setTimeout(function() {
                                    location.reload();
                                }, 4000);
                            },
                            error: function() {
                                showError('Error deleting comment.');
                            }
                        });
                    });
                    // Handle undo click on dynamically added notifications
                    $(document).on('click', '.undo-deleted-comment', function() {
                        const commentId = $(this).data('comment-id');
                        const contractId = $(this).data('client-id');

                        $.ajax({
                            url: '{{ url('undo-delete-comment-client') }}',
                            type: 'POST',
                            data: {
                                comment_id: commentId,
                                client_id: contractId,
                                _token: '{{ csrf_token() }}' // ✅ important for Laravel
                            },
                            success: function(response) {
                                showSuccessNotify(
                                    'Recovered Successfully',
                                    'Comment has been recovered successfully.'
                                );
                                localStorage.setItem('vacation_active_tab_target',
                                    'nav-comments-client');
                                localStorage.setItem('vacation_active_tab',
                                    'nav-comments-tab-client');
                                setTimeout(function() {
                                    location.reload();
                                }, 4000);
                            },
                            error: function() {
                                Dashmix.helpers('notify', {
                                    type: 'danger',
                                    message: `
                    <div>
                        <div class="font-titillium" style="font-weight: 800; color: #c41e39; font-size: 15pt;">Error</div>
                        <div class="d-flex align-items-center">
                            <div style="font-size: 14pt; margin-right: 8px;"><i class="fa-light fa-circle-xmark"></i></div>
                            <div>There was a problem recovering the comment.</div>
                        </div>
                    </div>
                `,
                                    allow_dismiss: true,
                                    delay: 3000,
                                    align: 'center',
                                });
                            }
                        });
                    });



                    $('#deleteAttachmentBtn').on('click', function() {
                        const formData = $('#deleteAttachmentForm').serialize();

                        $.ajax({
                            url: '{{ url('delete-attachment-client') }}',
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                $('#DelAttachmentModal').modal('hide');
                                showDeleteNotify('Deleted Successfully',
                                    'Attachment has been deleted successfully.', response
                                    .attachment_id, response.client_id, 'attachment');
                                localStorage.setItem('vacation_active_tab_target',
                                    'nav-attachments-client');
                                localStorage.setItem('vacation_active_tab',
                                    'nav-attachments-tab-contract');
                                setTimeout(function() {
                                    location.reload();
                                }, 4000);
                            },
                            error: function() {
                                showError('Error deleting attachment.');
                            }
                        });
                    });
                    // Handle undo click on dynamically added notifications
                    $(document).on('click', '.undo-deleted-attachment', function() {
                        const attachmentId = $(this).data('attachment-id');
                        const contractId = $(this).data('client-id');

                        $.ajax({
                            url: '{{ url('undo-delete-attachment-client') }}',
                            type: 'POST',
                            data: {
                                attachment_id: attachmentId,
                                client_id: contractId,
                                _token: '{{ csrf_token() }}' // ✅ important for Laravel
                            },
                            success: function(response) {
                                showSuccessNotify(
                                    'Recovered Successfully',
                                    'Attachment has been recovered successfully.'
                                );
                                setTimeout(function() {
                                    location.reload();
                                }, 4000);
                            },
                            error: function() {
                                Dashmix.helpers('notify', {
                                    type: 'danger',
                                    message: `
                    <div>
                        <div class="font-titillium" style="font-weight: 800; color: #c41e39; font-size: 15pt;">Error</div>
                        <div class="d-flex align-items-center">
                            <div style="font-size: 14pt; margin-right: 8px;"><i class="fa-light fa-circle-xmark"></i></div>
                            <div>There was a problem recovering the attachment.</div>
                        </div>
                    </div>
                `,
                                    allow_dismiss: true,
                                    delay: 3000,
                                    align: 'center',
                                });
                            }
                        });
                    });
                    $(document).on('click', '.btnDelete', function() {
                        var id = $(this).attr('data');

                        $('#deleteVacationModal #id').val(id);

                        // Show modal
                        $('#deleteVacationModal').modal('show');
                    });
                    $(document).on('click', '#deleteVacationBtn', function() {
                        const formData = $('#deleteVacationForm').serialize();

                        $.ajax({
                            url: '{{ url('delete-vacation') }}',
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                $('#deleteVacationModal').modal('hide');
                                showSuccessNotify(
                                    'Deleted Successfully',
                                    'Vacation has been deleted successfully.'
                                );
                                setTimeout(function() {
                                    location.reload();
                                }, 4000);
                            },
                            error: function() {
                                showError('Error deleting client.');
                            }
                        });
                    })
                    $(document).on('click', '.undo-deleted-client', function() {
                        const clientId = $(this).data('client-id');

                        $.ajax({
                            url: '{{ url('delete-client-undo') }}',
                            type: 'POST',
                            data: {
                                id: clientId,
                                _token: '{{ csrf_token() }}' // ✅ important for Laravel
                            },
                            success: function(response) {
                                showSuccessNotify(
                                    'Recovered Successfully',
                                    'Client has been recovered successfully.'
                                );
                                setTimeout(function() {
                                    location.reload();
                                }, 4000);
                            },
                            error: function() {
                                Dashmix.helpers('notify', {
                                    type: 'danger',
                                    message: `
                    <div>
                        <div class="font-titillium" style="font-weight: 800; color: #c41e39; font-size: 15pt;">Error</div>
                        <div class="d-flex align-items-center">
                            <div style="font-size: 14pt; margin-right: 8px;"><i class="fa-light fa-circle-xmark"></i></div>
                            <div>There was a problem recovering the client.</div>
                        </div>
                    </div>
                `,
                                    allow_dismiss: true,
                                    delay: 3000,
                                    align: 'center',
                                });
                            }
                        });
                    });

                    $(document).on('click', '.btnUndo', function() {
                        var id = $(this).attr('data-id');
                        window.location.href = "{{ url('delete-contract-undo') }}?id=" + id;
                    })




                    $(document).on('mouseenter', '.viewContent', function() {
                        var element = $(this);
                        element.find('.status-text').show()
                    });
                    $(document).on('mouseleave', '.viewContent', function() {
                        var element = $(this);
                        element.find('.status-text').hide()
                    });
                    $(document).on('click', '.delete-attachment', function() {
                        var client_id = $(this).attr('data-client-id');
                        var id = $(this).attr('data-id');
                        $('#del_attachment_id').val(id);
                        $('#del_client_id').val(client_id);
                        $('#DelAttachmentModal').modal('show');
                    });

                    $('.end-btn').on('click', function() {
                        var $btn = $(this); // Save the button reference
                        if ($btn.hasClass('disabled')) {
                            return; // Prevent multiple clicks
                        }
                        // Add spinner and expand button
                        $btn.addClass('disabled'); // Disable the button and expand it
                        // $btn.html(
                        //     '<i class="fa fa-cog spinner text-white"></i> Please wait while updating status...'
                        // ); // Add spinner icon and text
                        // $btn.css('background', '#2080F4')
                        // $btn.css('color', '#fff')
                        $('#EndModal').modal('hide');
                        $('#changeStatus').modal('show');
                        setTimeout(() => {
                            $('#end-client').submit();
                        }, 1000);
                    })

                    FilePond.registerPlugin(

                        FilePondPluginImagePreview,
                        FilePondPluginImageExifOrientation,
                        FilePondPluginFileValidateSize,
                        FilePondPluginImageEdit,
                        FilePondPluginFileValidateType
                    );

                    var attachments_file = [];
                    var content3_image = []
                    let filePond = FilePond.create(
                        document.querySelector('.attachment'), {
                            name: 'attachment',
                            allowMultiple: true,
                            allowImagePreview: true,

                            imagePreviewFilterItem: false,
                            imagePreviewMarkupFilter: false,

                            dataMaxFileSize: "2MB",



                            // server
                            server: {
                                process: {
                                    url: '{{ url('uploadNetworkAttachment') }}',
                                    method: 'POST',
                                    headers: {
                                        'x-customheader': 'Processing File'
                                    },
                                    onload: (response) => {

                                        response = response.replaceAll('"', '');
                                        content3_image.push(response);

                                        var attachemnts = $('input[name=attachment_array]').val()
                                        var attachment_array = attachemnts.split(',');
                                        attachment_array.push(response);
                                        $('input[name=attachment_array]').val(content3_image.join(','));

                                        return response;

                                    },
                                    onerror: (response) => {



                                        return response
                                    },
                                    ondata: (formData) => {
                                        window.h = formData;

                                        return formData;
                                    }
                                },
                                revert: (uniqueFileId, load, error) => {

                                    const formData = new FormData();
                                    formData.append("key", uniqueFileId);

                                    content3_image = content3_image.filter(function(ele) {
                                        return ele != uniqueFileId;
                                    });

                                    var attachemnts = $('input[name=attachment_array]').val()
                                    var attachment_array = attachemnts.split(',');
                                    attachment_array = attachment_array.filter(function(ele) {
                                        return ele != uniqueFileId;
                                    });

                                    $('input[name=attachment_array]').val(content3_image.join(','));


                                    fetch(`{{ url('revertContractAttachment') }}?key=${uniqueFileId}`, {
                                            method: "DELETE",
                                            body: formData,
                                        })
                                        .then(res => res.json())
                                        .then(json => {
                                            console.log(json);


                                            // Should call the load method when done, no parameters required

                                            load();

                                        })
                                        .catch(err => {
                                            console.log(err)
                                            // Can call the error method if something is wrong, should exit after
                                            error(err.message);
                                        })
                                },



                                remove: (uniqueFileId, load, error) => {
                                    // Should somehow send `source` to server so server can remove the file with this source
                                    content3_image = content3_image.filter(function(ele) {
                                        return ele != uniqueFileId;
                                    });


                                    // Should call the load method when done, no parameters required
                                    load();
                                },

                            }
                        }
                    );

                    $(document).on('click', '.filterSampleTestModal', function(e) {
                        e.stopPropagation();
                        var dropdown = $('.filter-dropdown-container');

                        populateFilterFromURL();

                        // Close all other dropdowns
                        $('.filter-dropdown-container').not(dropdown).removeClass('open');

                        // Toggle this dropdown
                        dropdown.toggleClass('open');
                    });

                    $(document).on('click', '.close-filter', function(e) {
                        e.stopPropagation();
                        populateFilterFromURL();
                        $(this).closest('.filter-dropdown-container').removeClass('open');
                    });

                    $(document).on('click', function(e) {
                        if (!$(e.target).closest('.filter-dropdown-container, .filterSampleTestModal').length) {
                            populateFilterFromURL();
                            $('.filter-dropdown-container').removeClass('open');
                        }
                    });

                    function populateFilterFromURL() {
                        const params = new URLSearchParams(window.location.search);

                        $('#contract_status').val(params.get('contract_status') || '');

                        $('#client_id')
                            .val(params.get('client_id') || '')
                            .selectpicker('refresh');

                        $('#contract_type').val(params.get('contract_type') || '');

                        $('#renewal_within').val(params.get('renewal_within') || '');

                        $('#site_id')
                            .val(params.getAll('site_id[]'))
                            .selectpicker('refresh');

                        $('#vendor_id')
                            .val(params.getAll('vendor_id[]'))
                            .selectpicker('refresh');

                        $('#distributor_id')
                            .val(params.getAll('distributor_id[]'))
                            .selectpicker('refresh');

                        // flatpickr
                        const fp = $('#example-flatpickr-range')[0]?._flatpickr;
                        const dateRange = params.get('daterange');

                        if (fp) {
                            dateRange ? fp.setDate(dateRange.split(' to '), true) : fp.clear();
                        }
                    }




                    @if (Session::has('success-attachment'))

                        showSuccessNotify('Attachments Added Successfully', 'Attachments have been added successfully.');
                    @endif

                    @if (Session::has('success-comment'))

                        showSuccessNotify('Comment Added Successfully', 'Comment has been added successfully.');
                    @endif
                    @if (Session::has('success-status'))
                        Dashmix.helpers('notify', {
                            align: 'center',
                            message: '<div class="p-2" style="min-width:320px;"><div class="font-titillium" style="font-weight: 800; color: #4EA833; font-size: 15pt;">Status Updated Successfully</div><div class="d-flex align-items-start mb-1"><i class="fa-thin fa-circle-check mr-2 fs-18" style="color:#333;"></i><span class="fs-15">Status has been changed to <strong>{!! Session::get('success-status') !!}</strong></span></div><div class="d-flex align-items-start"><i class="fa-thin fa-envelope mr-2 fs-18" style="color:#333;"></i><span class="fs-15">Email notification sent to support@amaltitek.com.</span></div></div>',
                            delay: 6000
                        });
                    @endif
                    @if (Session::has('success'))

                        Dashmix.helpers('notify', {
                            align: 'center',
                            message: '<div class="d-flex align-items-center justify-content-between"><div><img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1 mr-2"> {!! Session::get('success') !!}</div>',
                            delay: 5000
                        });
                    @endif


                    showData('{{ @$GETID }}');

                    function showData(id) {
                        $('.c-active').removeClass('c-active');
                        $('.viewContent[data=' + id + ']').addClass('c-active');
                        $.ajax({
                            type: 'get',
                            data: {
                                id: id
                            },
                            url: '{{ url('get-vacation-content') }}',
                            dataType: 'json',
                            beforeSend() {
                                Dashmix.layout('header_loader_on');

                            },

                            success: function(res) {
                                Dashmix.layout('header_loader_off');
                                $('#showData').removeClass('d-none').html(res.content);
                                $('#showCards').removeClass('d-none').html(res.cards);
                                const headerText = res.headerText ?? res.header_text ?? '';
                                const headerSubText = res.header_sub_text ?? '';
                                const headerDesc = res.header_desc ?? '';

                                $('.header-new-text').html(headerText);
                                $('.header-new-subtext').html(headerSubText);
                                $('.read-header .header-item-code').text(headerText);
                                $('.read-header .header-desc').text(headerDesc);
                                // $('.header-image').attr('src', res.header_img);
                                // $('.btn-edit').attr('data', res.id);
                                // $('.btnDelete').attr('data', res.id);
                                // $('.btn-clone').attr('data', res.id);
                                $('.sub-nav-icons').attr('data', res.id);
                                $('.sub-nav-icons').attr('data-id', res.id);
                                $('.sub-nav-icons').attr('data-item-id', res.id);
                                $('.closeEditBtn').attr('data-item-id', res.id);
                                $('.closeEditBtn').attr('data', res.id);
                                if (res.take_work_home && !res.planned) {
                                    $('.btn-mark-planned').removeClass('d-none');
                                } else {
                                    $('.btn-mark-planned').addClass('d-none');
                                }
                                $('.btn-pdf').attr('href', res.pdfUrl);
                                $('.icon-html').html(res.iconHtml);
                                $('[data-toggle=tooltip]').tooltip();
                                $('.new-header-icon-div .banner-icon[data-toggle="tooltip"]').tooltip('dispose')
                                    .tooltip({
                                        html: true,
                                        placement: 'top',
                                        boundary: 'window',
                                        popperConfig: {
                                            modifiers: {
                                                offset: {
                                                    enabled: true,
                                                    offset: '0,-8'
                                                },
                                                flip: {
                                                    enabled: false // ❗ stops up/down jumping
                                                },
                                                preventOverflow: {
                                                    enabled: false // ❗ stops Popper from moving it
                                                },
                                                computeStyle: {
                                                    adaptive: false // ❗ CRITICAL for consistency
                                                }
                                            }
                                        }
                                    });
                                setTimeout(() => {
                                    document.querySelectorAll(
                                        '.distributer-table, .purchasing-table'
                                    ).forEach(table => {
                                        const tbody = table.querySelector(
                                            'tbody.scrollable-tbody');
                                        const thead = table.querySelector('thead');

                                        function syncHeader() {
                                            const hasScrollbar = tbody.scrollHeight > tbody
                                                .clientHeight;

                                            thead.classList.toggle('has-scrollbar',
                                                hasScrollbar);
                                        }

                                        syncHeader();
                                        window.addEventListener('resize', syncHeader);
                                    });
                                }, 200);

                                // 🔹 Reset tabs to first (Main)
                                // $('.sub-nav-tabs .nav-link').removeClass('active');
                                // $('.tab-pane').removeClass('show active');
                                // $('#nav-main-tab-contract').addClass('active');
                                // $('#nav-main-contract').addClass('show active');
                                const savedTab = localStorage.getItem('vacation_active_tab');
                                const targetSection = localStorage.getItem('vacation_active_tab_target');
                                if (savedTab && targetSection) {
                                    $('.sub-nav-tabs .nav-link').removeClass('active');
                                    $('.tab-pane').removeClass('show active');
                                    $('#' + savedTab).addClass('active');
                                    $(targetSection).addClass('show active');
                                } else {
                                    $('.sub-nav-tabs .nav-link').removeClass('active');
                                    $('.tab-pane').removeClass('show active');
                                    $('#nav-main-tab-vacation').addClass('active');
                                    $('#nav-main-vacation').addClass('show active');
                                }
                                $('body').append(
                                    '<div id="vacation-comment-tooltip" class="font-titillium text-grey fw-300" ' +
                                    'style="font-family:\'Titillium Web\', sans-serif; display:none; position:absolute; ' +
                                    'border:1px solid #ccc; padding:10px 12px; border-radius:6px; width:270px; ' +
                                    'line-height:1.3; z-index:1000; background:#f9f9f9; ' +
                                    'box-shadow:0 2px 10px rgba(0,0,0,0.1);">' +
                                    '</div>'
                                );
                                const tooltip_read = $('#vacation-comment-tooltip');

                                // 2. Use event delegation for hover events on dynamically created buttons.
                                $('.vacation-table').on('mouseenter', '.vacation-info', function() {
                                    console.log('herre');

                                    const button = $(this);
                                    const comment = button.data('comment');
                                    button.find('i').removeClass('fa-light').addClass('fa-solid')
                                    if (comment) {
                                        // 3. Create the tooltip's inner HTML and populate it.
                                        const tooltipContent =
                                            `<strong class="titillium-web-black fs-18 text-primary" style="line-height:1.6;">Parent Comment</strong><br>${comment}`;
                                        tooltip_read.html(tooltipContent);

                                        // 4. Calculate the correct position.
                                        const iconPosition = button.offset();
                                        const iconWidth = button.outerWidth();

                                        // Position and show the tooltip
                                        tooltip_read.css({
                                            top: iconPosition.top, // Align top with the button
                                            left: iconPosition.left + iconWidth +
                                                10 // 10px to the right
                                        }).show();
                                    }
                                }).on('mouseleave', '.vacation-info', function() {
                                    // 5. Hide the tooltip on mouse out.
                                    $(this).find('i').removeClass('fa-solid').addClass('fa-light')
                                    tooltip_read.hide();
                                });

                                // new functions
                                let activeTooltip = null;

                                setTimeout(() => {
                                    // Check each cell on page load
                                    $(".detail_comments").each(function() {
                                        const td = $(this);
                                        const text = td.find(".cell-text").text().trim();

                                        // Create invisible measuring element
                                        const measurer = $("<span>")
                                            .text(text)
                                            .css({
                                                position: "absolute",
                                                visibility: "hidden",
                                                whiteSpace: "nowrap",
                                                fontSize: td.css("font-size"),
                                                fontFamily: td.css("font-family"),
                                                fontWeight: td.css("font-weight"),
                                                letterSpacing: td.css("letter-spacing")
                                            })
                                            .appendTo("body");

                                        const textWidth = measurer
                                            .width(); // REAL WIDTH of the text

                                        measurer.remove(); // cleanup

                                        // Apply logic
                                        if (textWidth > 450) {
                                            td.find(".copy-detail-line")
                                                .remove(); // too long → hide
                                        }
                                    });

                                    // Hover handler
                                    $(".detail_comments").hover(function() {
                                        const td = $(this);
                                        const fullText = td.find(".cell-text").text();
                                        const textEl = td.find(".cell-text")[0];

                                        const fits = textEl.scrollWidth <= textEl.clientWidth;

                                        // If fits → no tooltip
                                        if (fits) return;

                                        // Hide inline copy icon
                                        td.find(".copy-detail-line").hide();

                                        // Remove old tooltip
                                        if (activeTooltip) activeTooltip.remove();

                                        // Build tooltip
                                        const tooltip = $(`
                                <div class="tooltip-box">
                                    <span class="font-titillium text-darkgrey" style="color:#0D0D0D!important;font-weight:normal!important">${fullText}</span>
                                    <i class="fa-copy fs-18 fa-thin tooltip-copy text-darkgrey"></i>
                                    <div class="copied-msg">
                                        <div class="d-flex align-items-center">
                                        <i class="fa-light fa-circle-check mr-2"></i>
                                        <span class="font-titillium fs-14 text-darkgrey">
                                            Copied to clipboard!
                                        </span>
                                    </div>
                                        </div>
                                </div>
                            `);

                                        td.append(tooltip);
                                        activeTooltip = tooltip;

                                        // Copy inside tooltip
                                        tooltip.find(".tooltip-copy").on("click", function() {
                                            navigator.clipboard.writeText(fullText);

                                            const msg = tooltip.find(".copied-msg");
                                            msg.fadeIn(150);

                                            setTimeout(() => msg.fadeOut(150), 2000);
                                        });

                                    }, function() {
                                        if (activeTooltip) {
                                            activeTooltip.remove();
                                            activeTooltip = null;
                                        }
                                    });

                                    let activeTooltip = null;

                                    $(document).on({
                                        mouseenter: function() {

                                            const el = $(this);
                                            const fullText = el.text().trim();
                                            const textNode = el[0];

                                            // Check if truncated
                                            const isTruncated = textNode.scrollWidth >
                                                textNode.clientWidth;
                                            if (!isTruncated) return;

                                            // Remove previous tooltip
                                            if (activeTooltip) {
                                                activeTooltip.remove();
                                                activeTooltip = null;
                                            }

                                            // Create tooltip
                                            const tooltip = $(`
            <div class="tooltip-box">
                <span class="font-titillium text-darkgrey">${fullText}</span>
            </div>
        `);

                                            // Append and position relative to the hovered element
                                            el.parent().append(tooltip);

                                            // Positioning (center & below the text)
                                            const offset = el.position();
                                            tooltip.css({
                                                position: "absolute",
                                                top: offset.top + el.outerHeight() +
                                                    4, // 4px spacing under the text
                                                left: offset
                                                    .left, // aligned with text start
                                                width: "max-content",
                                                "max-width": "260px",
                                                "z-index": 9999
                                            });

                                            activeTooltip = tooltip;
                                        },

                                        mouseleave: function() {
                                            if (activeTooltip) {
                                                activeTooltip.remove();
                                                activeTooltip = null;
                                            }
                                        }

                                    }, ".short-card-details");

                                    $(".header-item-code.text-ellipsis").hover(function() {

                                        const el = $(this);
                                        const fullText = el.text().trim();
                                        const textNode = el[0];

                                        // Check if text is truncated (scrollWidth > clientWidth)
                                        const isTruncated = textNode.scrollWidth > textNode
                                            .clientWidth;
                                        if (!isTruncated) return;

                                        // Remove existing tooltip
                                        if (activeTooltip) activeTooltip.remove();

                                        const tooltip = $(`
                                                <div class="tooltip-box" style="width: 450px;top: 35px;left: 335px;">
                                                    <span class="font-titillium text-darkgrey">${fullText}</span>
                                                </div>
                                            `);

                                        // Append to parent container (better positioning)
                                        el.parent().append(tooltip);

                                        activeTooltip = tooltip;

                                    }, function() {
                                        if (activeTooltip) {
                                            activeTooltip.remove();
                                            activeTooltip = null;
                                        }
                                    });

                                    // Inline copy (when text fits)
                                    // $(".copy-detail-line").on("click", function() {
                                    //     const text = $(this).attr("data-text");
                                    //     navigator.clipboard.writeText(text);

                                    //     const td = $(this).closest("td");
                                    //     const msg = $('<div class="copied-msg">Copied!</div>');
                                    //     td.append(msg);

                                    //     msg.fadeIn(150);

                                    //     setTimeout(() => msg.fadeOut(150, () => msg.remove()), 2000);
                                    // });
                                }, 100);
                            }
                        })
                    }

                    function updateQueryStringParameter(uri, key, value) {
                        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
                        if (uri.match(re)) {
                            return uri.replace(re, '$1' + key + "=" + value + '$2');
                        } else {
                            return uri + separator + key + "=" + value;
                        }
                    }

                    $(document).on('click', '.viewContent', function() {
                        var id = $(this).attr('data');
                        var oldURL = window.location.href;
                        var type = id;

                        $('#showEditData').addClass('d-none');
                        $('.edit-nav-tabs, .edit-header').addClass('d-none');
                        $('.read-nav-tabs, .read-header').removeClass('d-none');

                        if (history.pushState) {

                            var newUrl = updateQueryStringParameter(oldURL, 'id', id)
                            window.history.pushState({
                                path: newUrl
                            }, '', newUrl);
                        }

                        showData(id);
                    })

                    // $('.ActionIcon').mouseover(function() {
                    //     var data = $(this).attr('data-src');
                    //     $(this).find('img').attr('src', data);
                    // })
                    // $('.ActionIcon').mouseout(function() {
                    //     var data = $(this).attr('data-original-src');
                    //     $(this).find('img').attr('src', data);
                    // })

                    $('.changeSelect').change(function() {


                        var array = [];
                        $('.changeSelect:checked').each(function() {
                            array.push($(this).val());
                        })
                        console.log(array);
                        $('td[data-index],th[data-index]').addClass('d-none')

                        for (var i = 0; i < array.length; i++) {
                            $('td[data-index=' + array[i] + ']').removeClass('d-none')
                            $('th[data-index=' + array[i] + ']').removeClass('d-none')
                        }


                        $.ajax({
                            type: 'get',
                            data: {
                                array: array,
                                type: type
                            },
                            url: "{{ url('change-contract-columns') }}",
                            success: function(res) {

                            }
                        })


                    })



                    $('#showdata').on('click', '.btnEdit', function() {
                        var id = $(this).attr('data');
                        $.ajax({
                            type: 'get',
                            data: {
                                id: id
                            },
                            url: '{{ url('show-contract') }}',
                            success: function(res) {


                                $('#viewData').modal('show');
                                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",
                                    "July", "Aug", "Sep", "Oct", "Nov", "Dec"
                                ];
                                var start_dateObject = new Date(res.contract_start_date);
                                var contract_start_date = start_dateObject.getFullYear() + '-' +
                                    monthNames[start_dateObject.getMonth()] + '-' + start_dateObject
                                    .getDate();

                                var end_dateObject = new Date(res.contract_end_date);
                                var contract_end_date = end_dateObject.getFullYear() + '-' + monthNames[
                                    end_dateObject.getMonth()] + '-' + end_dateObject.getDate();

                                var po_dateObject = new Date(res.po_date);
                                var po_date = po_dateObject.getFullYear() + '-' + monthNames[
                                    po_dateObject.getMonth()] + '-' + po_dateObject.getDate();


                                var invoice_dateObject = new Date(res.invoice_date);
                                var invoice_date = invoice_dateObject.getFullYear() + '-' + monthNames[
                                        invoice_dateObject.getMonth()] + '-' + invoice_dateObject
                                    .getDate();

                                var ended_onObject = new Date(res.ended_on);
                                var ended_on = ended_onObject.getFullYear() + '-' + monthNames[
                                    ended_onObject.getMonth()] + '-' + ended_onObject.getDate();
                                var MyDate = new Date('<?php echo date('m/d/Y'); ?>');

                                var expiry_dateObj = new Date(res.contract_end_date);


                                var status = '';

                                const diffTime = Math.abs(expiry_dateObj - MyDate);
                                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                                if (res.contract_status == 'Active') {

                                    if (diffDays <= 30) {
                                        status = 'upcoming.png';

                                    } else {
                                        status = 'active.png';
                                    }


                                } else if (res.contract_status == 'Inactive') {
                                    status = 'renewed.png';

                                } else if (res.contract_status == 'Expired/Ended') {
                                    status = 'ended.png';
                                } else if (res.contract_status == 'ended') {
                                    status = 'ended.png';
                                } else if (res.contract_status == 'Expired') {
                                    status = 'expired.png';
                                } else {
                                    status = 'active.png';
                                }

                                // $('#hostnameDisplay').html('<div style="display:flex;align-items:center"><img class="img-avatar  mr-3 atar48" style="object-fit: cover" src="{{ asset('public/vendor_logos/') }}/'+res.vendor_image+'" alt=""> <p class="text-uppercase mt-3"><img class="  mr-3 atar48" width="30px"  src="{{ asset('public/img/') }}/'+status+'" alt="">'+res.contract_no+' <br><span style="color:grey!important">'+res.contract_description+'</span></p></div>')
                                $('#hostnameDisplay').html(
                                    '<div style="display:flex;align-items:center"><img class="  mr-2 atar48" style="object-fit: cover" src="{{ asset('public/vendor_logos/') }}/' +
                                    res.vendor_image +
                                    '"  alt="" width="60px" height="40px"> <div><p class="text-uppercase mb-0 mt-2" style="color:#0D0D0D!important;font-size:15pt;line-height:20px"><img class="  mr-3 atar48" width="30px"  src="{{ asset('public/img/') }}/' +
                                    status + '" alt=""><b>' + res.contract_no +
                                    '</b></p><p class="my-0"><span style="color:#D3D3D3!important;font-size:10pt">' +
                                    res.contract_description + '</span></p></div></div>')



                                $('#clientLogo').html(
                                    '<img class="img-avatar  mr-3 atar48" style="object-fit: cover" src="{{ asset('public/client_logos/') }}/' +
                                    res.logo + '" alt="">');



                                $('#client_display_name').html(res.client_display_name)
                                $('#site_name').html(res.site_name)

                                $('#contract_noDisplay').html(res.contract_no + ' ' + status)
                                $('#contract_start_dateDisplay').html(contract_start_date)
                                $('#contract_end_dateDisplay').html(contract_end_date)

                                $('#ended_by').html(res.ended_email)
                                $('#ended_on').html(ended_on)
                                $('#ended_reason').html(res.ended_reason)
                                if (res.contract_status == 'Expired/Ended') {
                                    $('.ContractEndDiv').removeClass('invisible');
                                } else {
                                    $('.ContractEndDiv').addClass('invisible');
                                }
                                $('#estimate_noDisplay').html(
                                    '<a target="_blank" href="{{ url('GetZohoInvoicesAuth?estimate_number=') }}' +
                                    res.estimate_no + '">' + res.estimate_no + '</a>')
                                $('#sales_no_Display').html(
                                    '<a target="_blank" href="{{ url('GetZohoInvoicesAuth?sales_number=') }}' +
                                    res.sales_order_no + '">' + res.sales_order_no + '</a>')
                                $('#invoice_noDisplay').html(
                                    '<a target="_blank" href="{{ url('GetZohoInvoicesAuth?invoice_number=') }}' +
                                    res.invoice_no + '">' + res.invoice_no + '</a>')

                                @if (@Auth::user()->role == 'admin')

                                    $('#po_noDisplay').html(
                                        '<a target="_blank" href="{{ url('GetZohoInvoicesAuth?po_number=') }}' +
                                        res.po_no + '">' + res.po_no + '</a>')
                                @else


                                    $('#po_noDisplay').html(res.po_no)
                                @endif
                                $('#invoice_dateDisplay').html(invoice_date)
                                $('#po_dateDisplay').html(po_date)
                                $('#distributor_nameDisplay').html(res.distributor_name)
                                $('#reference_noDisplay').html(res.reference_no)
                                $('#registered_emailDisplay').html(res.registered_email)

                                $('#descriptionDisplay').html(res.contract_description)


                                if (res.comments == '' || res.comments == null) {
                                    $('.commentsDiv').addClass('d-none')
                                } else {
                                    $('.commentsDiv').removeClass('d-none')
                                }


                                if (res.attachment == '' || res.attachment == null) {
                                    $('.attachmentsDiv').addClass('d-none')
                                } else {
                                    $('.attachmentsDiv').removeClass('d-none')
                                }

                                $('#sales_order_noDisplay').html(res.distrubutor_sales_order_no)
                                $('#vendor_nameDisplay').html(res.vendor_name)
                                $('#contract_typeDisplay').html(res.contract_type)
                                $('#commentDisplay').html(res.comments)
                                $('#created_at').html(res.created_at)
                                $('#created_by').html(res.created_by != null ? res.created_firstname +
                                    ' ' + res.created_lastname : '')
                                $('#updated_by').html(res.updated_by != null ? res.updated_firstname +
                                    ' ' + res.updated_lastname : '')
                                $('#updated_at').html(res.updated_at)

                                if (res.attachment != '' && res.attachment != null) {
                                    ht = '';
                                    var attachments = res.attachment.split(',');
                                    for (var i = 0; i < attachments.length; i++) {
                                        var icon = 'fa-file';

                                        var fileExtension = attachments[i].split('.').pop();
                                        console.log(fileExtension)
                                        if (fileExtension == 'pdf') {
                                            icon = 'fa-file-pdf';
                                        } else if (fileExtension == 'doc' || fileExtension == 'docx') {
                                            icon = 'fa-file-word'
                                        } else if (fileExtension == 'txt') {
                                            icon = 'fa-file-alt';

                                        } else if (fileExtension == 'csv' || fileExtension == 'xlsx' ||
                                            fileExtension == 'xlsm' || fileExtension == 'xlsb' ||
                                            fileExtension == 'xltx') {
                                            icon = 'fa-file-excel'
                                        } else if (fileExtension == 'png' || fileExtension == 'jpeg' ||
                                            fileExtension == 'jpg' || fileExtension == 'gif' ||
                                            fileExtension == 'webp' || fileExtension == 'svg') {
                                            icon = 'fa-image'
                                        }
                                        ht += '<span class="attachmentDiv mr-2"><i class="fa ' + icon +
                                            ' text-danger"></i><a class="text-dark"  href="{{ asset('public/contract_attachment') }}/' +
                                            attachments[i] + '" target="_blank"> ' + attachments[i] +
                                            '</a></span>';
                                    }
                                    $('#attachmentDisplay').html(ht)
                                } else {
                                    $('#attachmentDisplay').html('')
                                }


                                $('.printDiv').attr('href', '{{ url('print-contract') }}?id=' + id)
                                $('.pdfDiv').attr('href', '{{ url('pdf-contract') }}?id=' + id)




                                $.ajax({
                                    type: 'get',
                                    data: {
                                        id: id
                                    },
                                    url: '{{ url('show-contract-details') }}',
                                    success: function(res) {
                                        var html = '';


                                        $('#assetdiv').html(res)

                                    }
                                })





                            }
                        })

                    })
                    $('#form-search').submit(function(e) {
                        e.preventDefault();
                    })
                    $('input[name=search]').keyup(function(e) {

                        var val = $(this).val();
                        if (e.which == 13) {
                            var form = $('#form-search');

                            let url = form.attr("action");
                            url += '&search=' + val;
                            window.location.href = url
                        }
                    })

                    $('select[name=limit]').change(function() {
                        var form = $('#limit_form');
                        if (form.attr("action") === undefined) {
                            throw "form does not have action attribute"
                        }


                        let url = form.attr("action");
                        if (url.includes("?") === false) return false;

                        let index = url.indexOf("?");
                        let action = url.slice(0, index)
                        let params = url.slice(index);
                        url = new URLSearchParams(params);
                        for (param of url.keys()) {
                            if (param != 'limit') {
                                let paramValue = url.get(param);

                                let attrObject = {
                                    "type": "hidden",
                                    "name": param,
                                    "value": paramValue
                                };
                                let hidden = $("<input>").attr(attrObject);
                                form.append(hidden);
                            }
                        }
                        form.attr("action", action)

                        form.submit();
                    })

                    $(document).on('click', '.export-step1', function() {
                        var col = $('#columns').val();

                        if (col && col.length > 0) {
                            $('.export-step2').removeClass('d-none');
                            $('.export-step1').addClass('d-none');
                        } else {
                            Dashmix.helpers('notify', {
                                align: 'center',
                                type: 'danger',
                                message: '<i class="fa fa-exclamation-circle"></i> Please select at least one option to export.',
                                delay: 4000
                            });
                        }
                    });


                    $(document).on('click', '.btn.export-step2', function(e) {
                        e.preventDefault(); // prevent immediate page reload

                        var col = $('#columns').val();

                        if (col && col.length > 0) {
                            var form = $('#exportform');

                            if (!form.attr("action")) {
                                throw "Form does not have an action attribute";
                            }

                            let url = form.attr("action");
                            let action = '';
                            if (url.includes("?")) {
                                let index = url.indexOf("?");
                                action = url.slice(0, index);
                                let params = url.slice(index);
                                url = new URLSearchParams(params);
                                for (const param of url.keys()) {
                                    if (param != 'limit') {
                                        let paramValue = url.get(param);
                                        form.append($('<input>').attr({
                                            type: "hidden",
                                            name: param,
                                            value: paramValue
                                        }));
                                    }
                                }
                            } else {
                                action = url;
                            }

                            form.attr("action", action);

                            // Hide modal before form submission
                            $('#ExportModal').modal('hide');

                            // Optional: small delay to ensure modal animation completes before redirect
                            setTimeout(function() {
                                form.submit();
                            }, 300);

                            // Reset modal state
                            $('.export-step1').removeClass('d-none');
                            $('.export-step2').addClass('d-none');

                            showSuccessNotify('Export Successful', 'Export has been completed successfully.');
                        }
                    });





                    function getVendor(client_id, site_id, on) {
                        $.ajax({
                            type: 'get',
                            data: {
                                client_id: client_id,
                                site_id: site_id
                            },
                            url: '{{ url('getVendorOfContract') }}',
                            async: false,
                            success: function(res) {
                                var html = '';
                                var check = '<?php echo @$_GET['vendor_id'] ? implode(',', $_GET['vendor_id']) : ''; ?>';;
                                check = check.split(',');
                                for (var i = 0; i < res.length; i++) {
                                    if (on) {
                                        if (check.includes(String(res[i].id))) {
                                            html += '<option value="' + res[i].id + '" selected>' + res[i]
                                                .vendor_name + '</option>';
                                        } else {
                                            html += '<option value="' + res[i].id + '" >' + res[i].vendor_name +
                                                '</option>';
                                        }
                                    } else {
                                        html += '<option value="' + res[i].id + '" >' + res[i].vendor_name +
                                            '</option>';
                                    }
                                }

                                $('#vendor_id').html(html);
                                $('#vendor_id').selectpicker('refresh');
                            }
                        })
                    }

                    function getDistributor(client_id, site_id, vendor_id, on) {
                        $.ajax({
                            type: 'get',
                            data: {
                                client_id: client_id,
                                site_id: site_id,
                                vendor_id: vendor_id
                            },
                            url: '{{ url('getDistributorOfContract') }}',
                            async: false,
                            success: function(res) {
                                var html = '';

                                var check = '<?php echo @$_GET['distributor_id'] ? implode(',', $_GET['distributor_id']) : ''; ?>';;
                                check = check.split(',');
                                for (var i = 0; i < res.length; i++) {
                                    if (on) {
                                        if (check.includes(String(res[i].id))) {
                                            html += '<option value="' + res[i].id + '" selected>' + res[i]
                                                .distributor_name + '</option>';
                                        } else {
                                            html += '<option value="' + res[i].id + '" >' + res[i]
                                                .distributor_name + '</option>';
                                        }
                                    } else {
                                        html += '<option value="' + res[i].id + '" >' + res[i]
                                            .distributor_name + '</option>';
                                    }
                                }

                                $('#distributor_id').html(html);
                                $('#distributor_id').selectpicker('refresh');
                            }
                        })
                    }

                    $('#site_id').change(function() {
                        var site_id = $(this).val();
                        var client_id = $('#client_id').val()

                        getVendor(client_id, site_id)
                    })
                    $('#vendor_id').change(function() {
                        var vendor_id = $(this).val();
                        var client_id = $('#client_id').val()
                        var site_id = $('#site_id').val()
                        getDistributor(client_id, site_id, vendor_id)
                    })

                    function run(id, on) {
                        $.ajax({
                            type: 'get',
                            data: {
                                id: id
                            },
                            url: '{{ url('getSiteByClientId') }}',
                            async: false,
                            success: function(res) {
                                var html = '';
                                var check = '<?php echo @$_GET['site_id'] ? implode(',', $_GET['site_id']) : ''; ?>';;
                                check = check.split(',');
                                for (var i = 0; i < res.length; i++) {
                                    if (on) {
                                        if (check.includes(String(res[i].id))) {
                                            html += '<option value="' + res[i].id + '" selected>' + res[i]
                                                .site_name + '</option>';
                                        } else {
                                            html += '<option value="' + res[i].id + '" >' + res[i].site_name +
                                                '</option>';
                                        }
                                    } else {
                                        html += '<option value="' + res[i].id + '" >' + res[i].site_name +
                                            '</option>';
                                    }
                                }

                                $('#site_id').html(html);
                                $('#site_id').selectpicker('refresh');
                            }
                        })




                        $.ajax({
                            type: 'get',
                            data: {
                                id: id
                            },
                            url: '{{ url('getDomainByClientId') }}',
                            success: function(res) {
                                var html = '';

                                var check = '{{ @$domain }}';
                                check = check.split(',');

                                for (var i = 0; i < res.length; i++) {
                                    if (on) {
                                        if (check.includes(String(res[i].id))) {
                                            html += '<option value="' + res[i].id + '" selected>' + res[i]
                                                .domain_name + '</option>';
                                        } else {
                                            html += '<option value="' + res[i].id + '" >' + res[i].domain_name +
                                                '</option>';
                                        }
                                    } else {
                                        html += '<option value="' + res[i].id + '" >' + res[i].domain_name +
                                            '</option>';
                                    }
                                }

                                $('#domain').html(html);
                                $('#domain').selectpicker('refresh');
                            }
                        })
                    }

                    $('#client_id').change(function() {
                        var id = $(this).val()

                        run(id)
                        getVendor(id);

                    })


                    $(document).on('click', '.btnEnd', function() {
                        var id = $(this).attr('data');
                        var ended = $(this).attr('data-ended');
                        var currentStatus = $(this).attr('data-status');
                        var newStatus = '';

                        if (ended == 1) {
                            newStatus = 'Active';
                            $('input[name=end]').val(1);
                            $('.endTitle').html('Reinstate Contract')
                            $('#end-btn').html('Activate')
                        } else {
                            newStatus = 'Inactive';
                            $('input[name=end]').val(0);
                            $('.endTitle').html('End Contract')
                            $('#end-btn').html('Deactivate')
                        }

                        $('input[name=id]').val(id);
                        $('#toStatus').text(newStatus);
                        $('#currentStatus').text(currentStatus);
                        $('#EndModal').modal('show')
                    })



                    $(document).on('click', '.undo-delete-attachment', function() {
                        var client_id = $(this).attr('data-client-id');
                        var id = $(this).attr('data');
                        window.location.href = "{{ url('undo-delete-attachment-client') }}?id=" + id +
                            "&client_id=" + client_id;
                    });

                    @if (Session::has('alert-delete-attachment'))
                        const alertStr = {!! json_encode(Session::get('alert-delete-attachment')) !!}; // ensures it's a proper string
                        const parts = alertStr.split("|");
                        const message = parts[0];
                        const id = parts[1];
                        const client_id = parts[2];

                        Dashmix.helpers('notify', {
                            from: 'bottom',
                            align: 'left',
                            message: message + ' <a href="javascript:;" data="' + id + '" data-client-id="' +
                                client_id +
                                '" data-notify="dismiss" class="btn-notify undo-delete-attachment ml-4">Undo</a>',
                            delay: 125000,
                            type: 'info alert-notify-desktop'
                        });
                    @endif
                })



                $(document).on('mouseenter', '.asset-trigger', function() {
                    $(this).find('.asset-popover').removeClass('d-none').stop(true, true).fadeIn(150);
                }).on('mouseleave', '.asset-trigger', function() {
                    $(this).find('.asset-popover').stop(true, true).fadeOut(150, function() {
                        $(this).addClass('d-none');
                    });
                });

                let activePopover = null;
                let hideTimeout = null;

                function showPopover($trigger) {
                    const $popover = $trigger.find('.asset-popover-read');
                    if (!$popover.length) return;

                    if (activePopover) {
                        activePopover.remove();
                        activePopover = null;
                    }

                    const $clone = $popover.clone()
                        .removeClass('d-none')
                        .addClass('active-asset-popover')
                        .appendTo('body')
                        .fadeIn(150);

                    const offset = $trigger.offset();

                    $clone.css({
                        top: offset.top + $trigger.outerHeight() / 2,
                        left: offset.left + $trigger.outerWidth() + 8,
                        transform: 'translateY(-50%)'
                    });

                    activePopover = $clone;

                    // cancel hide when mouse enters popover
                    $clone.on('mouseenter', () => {
                        if (hideTimeout) clearTimeout(hideTimeout);
                    });

                    // hide when mouse leaves popover
                    $clone.on('mouseleave', hidePopover);
                }

                function hidePopover() {
                    hideTimeout = setTimeout(() => {
                        if (activePopover) {
                            activePopover.fadeOut(150, function() {
                                $(this).remove();
                            });
                            activePopover = null;
                        }
                    }, 150);
                }

                // Hover on trigger
                $(document).on('mouseenter', '.asset-trigger-read', function() {
                    if (hideTimeout) clearTimeout(hideTimeout);
                    showPopover($(this));
                });

                $(document).on('mouseleave', '.asset-trigger-read', function() {
                    hidePopover();
                });



                // Modal open with assets
                $(document).on('click', '.showAssetsModal', function(e) {
                    e.preventDefault();

                    let assets = $(this).data('assets'); // JSON list
                    let html = `<div class="border p-2 rounded-10px">
                  <table class="table table-sm table-striped table-borderless mb-0 font-titillium">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Hostname</th>
                        <th>Serial No.</th>
                      </tr>
                    </thead>
                  <tbody>`;

                    if (Array.isArray(assets)) {
                        assets.forEach(a => {
                            html += `<tr>
                        <td class="py-2"><i class="fa-light fa-server text-grey fs-18"></i></td>

                        <!-- Hostname -->
                        <td class="py-2 fw-300 fs-16 asset-cell">
                            <span class="asset-hostname-copy">${a.fqdn || ''}</span>
                            <i class="fa-light fa-copy fs-14 text-grey asset-copy-icon hostname-icon mr-2 mt-1 float-right" style="cursor:pointer;" data-toggle="tooltip" 
   data-html="true" 
   title="Copy Hostname"></i>
                        </td>

                        <!-- Serial -->
                        <td class="py-2 fw-300 fs-16 asset-cell">
                            <span class="asset-sr-copy">${a.asset_type === 'physical' ? a.sn : ''}</span>
                            <i class="fa-light fa-copy fs-14 text-grey asset-copy-icon sr-icon mr-2 mt-1 float-right" style="cursor:pointer;" data-toggle="tooltip" 
   data-html="true" 
   title="Copy SN#">
   </i>
                        </td>
                     </tr>`;
                        });
                    }

                    html += '</tbody></table><div>';

                    $('#assetsModal .modal-body').html(html);
                    $('#assetsModal').modal('show');

                    if (activePopover) {
                        activePopover.fadeOut(150, function() {
                            $(this).remove();
                        });
                        activePopover = null;
                    }

                    $('[data-toggle="tooltip"]').tooltip();

                });

                // Hostname copy
                // $(document).on("click", ".hostname-icon", function() {
                //     let icon = $(this);
                //     let text = icon.siblings(".asset-hostname-copy").text().trim();

                //     navigator.clipboard.writeText(text).then(() => {
                //         // Change tooltip content to success
                //         icon.attr("data-original-title",
                //             '<i class="fa-light fa-circle-check mr-2"></i><span class="font-titillium fs-14 text-darkgrey">Copied to clipboard!</span>');
                //         icon.tooltip("show");

                //         // Reset after 2s
                //         setTimeout(() => {
                //             icon.tooltip("hide")
                //                 .attr("data-original-title", "Copy Hostname");
                //         }, 2000);
                //     });
                // });
                $(document).on("click", ".hostname-icon", function() {
                    let icon = $(this);
                    let text = icon.siblings(".asset-hostname-copy").text().trim();

                    navigator.clipboard.writeText(text).then(() => {

                        // 🔴 Hide & temporarily disable tooltip
                        icon.tooltip('hide').tooltip('disable');

                        // Create toast element
                        const toast = $(`
                            <div class="clipboard-toast-detailline">
                                <div class="d-flex align-items-center">
                                    <i class="fa-light fa-circle-check mr-2"></i>
                                    <span class="font-titillium fs-14 text-darkgrey">
                                        Copied to clipboard!
                                    </span>
                                </div>
                            </div>
                        `);

                        // Append toast
                        $("body").append(toast);

                        // Button position
                        const btnOffset = icon.offset();
                        const btnWidth = icon.outerWidth();

                        // Position toast above icon
                        toast.css({
                            top: btnOffset.top - 35,
                            left: btnOffset.left + (btnWidth / 2) - 80
                        });

                        // Show animation
                        setTimeout(() => toast.addClass("show"), 10);

                        // Hide toast + re-enable tooltip
                        setTimeout(() => {
                            toast.removeClass("show");

                            setTimeout(() => {
                                toast.remove();
                                icon.tooltip('enable'); // ✅ tooltip works again
                            }, 200);

                        }, 1300);
                    });
                });


                // Serial copy
                // $(document).on("click", ".sr-icon", function() {
                //     let icon = $(this);
                //     let text = icon.siblings(".asset-sr-copy").text().trim();

                //     navigator.clipboard.writeText(text).then(() => {
                //         icon.attr("data-original-title",
                //             '<i class="fa-light fa-circle-check mr-2"></i><span class="font-titillium fs-14 text-darkgrey">Copied to clipboard!</span>');
                //         icon.tooltip("show");

                //         setTimeout(() => {
                //             icon.tooltip("hide").attr("data-original-title", "Copy SN#");
                //         }, 2000);
                //     });
                // });
                $(document).on("click", ".sr-icon", function() {
                    let icon = $(this);
                    let text = icon.siblings(".asset-sr-copy").text().trim();

                    navigator.clipboard.writeText(text).then(() => {

                        // 🔴 Hide & temporarily disable tooltip
                        icon.tooltip('hide').tooltip('disable');

                        // Create toast element
                        const toast = $(`
                            <div class="clipboard-toast-detailline">
                                <div class="d-flex align-items-center">
                                    <i class="fa-light fa-circle-check mr-2"></i>
                                    <span class="font-titillium fs-14 text-darkgrey">
                                        Copied to clipboard!
                                    </span>
                                </div>
                            </div>
                        `);

                        // Append toast
                        $("body").append(toast);

                        // Button position
                        const btnOffset = icon.offset();
                        const btnWidth = icon.outerWidth();

                        // Position toast above icon
                        toast.css({
                            top: btnOffset.top - 35,
                            left: btnOffset.left + (btnWidth / 2) - 80
                        });

                        // Show animation
                        setTimeout(() => toast.addClass("show"), 10);

                        // Hide toast + re-enable tooltip
                        setTimeout(() => {
                            toast.removeClass("show");

                            setTimeout(() => {
                                toast.remove();
                                icon.tooltip('enable'); // ✅ tooltip works again
                            }, 200);

                        }, 1300);
                    });
                });

                // close toast
                $(document).on("click", ".btn-close-toast", function() {
                    var toast = $(this).attr('data-section');
                    $('.' + toast).fadeOut();
                });

                $(document).on('click', '.dropdown-options li', function() {
                    const text = $(this).text().trim();
                    const box = $(this).closest('.custom-dropdown');
                    if (text) {
                        box.siblings('.validation-error').remove();
                        box.find('.constant-icon').css('color', '');
                        box.find('h6').css('color', '');
                        setTimeout(() => {
                            box.css({
                                position: '',
                                width: '',
                                zIndex: '',
                                'border-color': '',
                                'box-shadow': ''
                            });
                            box.next('.dropdown-spacer').remove();
                        }, 350);
                    }
                })

                // $(document).on('click', '.selected-value', function() {
                //     console.log('dropdown new');
                //     let el = $(this).closest('.custom-dropdown');

                //     if (el.hasClass('open')) {
                //         // closing
                //         el.removeClass('open');

                //         // delay before returning to relative
                //         setTimeout(() => {
                //             el.css('position', 'relative');
                //         }, 300); // delay in ms (match your transition)
                //     } else {
                //         // opening
                //         el.css('position', 'absolute');
                //         el.addClass('open');
                //     }
                // });
            </script>
