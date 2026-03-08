<html lang="en">



<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">



    <title>Kumon</title>



    <meta name="description"

        content="Dashmix - Bootstrap 4 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">

    <meta name="author" content="pixelcave">

    <meta name="robots" content="noindex, nofollow">



    <!-- Open Graph Meta -->

    <meta property="og:title" content="Dashmix - Bootstrap 4 Admin Template &amp; UI Framework">

    <meta property="og:site_name" content="Dashmix">

    <meta property="og:description"

        content="Dashmix - Bootstrap 4 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">

    <meta property="og:type" content="website">

    <meta property="og:url" content="">

    <meta property="og:image" content="">



    <!-- Icons -->

    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->

    <link rel="shortcut icon" href="{{ asset('public/img/fav1.png') }}">

    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('public/img/fav1.png') }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/img/fav1.png') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">



    <!-- END Icons -->

    <link rel="stylesheet"

        href="{{ asset('public/dashboard_assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">

    <link rel="stylesheet"

        href="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />



    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link

        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Source+Sans+Pro:wght@200;300;400;600;700;900&display=swap"

        rel="stylesheet">



    {{-- <link rel="stylesheet" href="{{ asset('public/dashboard_assets/js/plugins/select2/css/select2.min.css') }}"> --}}

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />



    <link rel="stylesheet"

        href="{{ asset('public/dashboard_assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">

    <link rel="stylesheet"

        href="{{ asset('public/dashboard_assets/js/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">



    <!-- Stylesheets -->

    <!-- Fonts and Dashmix framework -->

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <link rel="stylesheet" id="css-main" href="{{ asset('public/dashboard_assets/css/dashmix.min.css') }}">

    <link rel="stylesheet"

        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

    <link rel="stylesheet"

        href="{{ asset('public/dashboard_assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">

    <link rel="stylesheet" href="{{ asset('public/dashboard_assets/js/plugins/flatpickr/flatpickr.min.css') }}">

    <link rel="stylesheet" href="{{ asset('public/css/filepond.css') }}">


    <link rel="stylesheet"

        href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css">

    <link rel="stylesheet" href="{{ asset('public/css/dropify.css') }}">

    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->

    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/xwork.min.css"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />

    <!-- END Stylesheets -->



    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/whiteboard-semibold.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/thumbprint-light.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/slab-press-regular.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/slab-regular.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/sharp-duotone-thin.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/sharp-duotone-solid.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/sharp-duotone-regular.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/sharp-duotone-light.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/sharp-thin.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/sharp-solid.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/sharp-regular.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/sharp-light.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/notdog-duo-solid.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/notdog-solid.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/jelly-fill-regular.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/jelly-duo-regular.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/jelly-regular.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/etch-solid.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/duotone-thin.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/duotone.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/duotone-regular.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/duotone-light.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/thin.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/solid.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/regular.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/light.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/brands.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontawsome_assets/css/chisel-regular.css') }}">

    <style type="text/css">

        body {
            overflow-x: hidden;
            font-family: "Segoe UI", Inter, -apple-system, BlinkMacSystemFont, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";

        }



        .page-item:first-child .page-link {



            border-top-left-radius: 10px !important;

            border-bottom-left-radius: 10px !important;

        }



        .page-item:last-child .page-link {



            border-top-right-radius: 8px !important;

            border-bottom-right-radius: 8px !important;

        }





        .table-block-new {

            border-radius: 10px !important;

            border: 1px solid #ECEFF4 !important;



            box-shadow: 3px 3px 6px #d0d3d8 !important;

        }



        .table-block-new:hover {

            border-radius: 10px !important;

            border: 1px solid #BFBFBF !important;

            background: #F2F2F2 !important;

            box-shadow: 3px 3px 4px #40404057 !important;

        }



        .table-block-new.c-active:hover {

            border-radius: 10px !important;

            background: #C2DBFF !important;

            box-shadow: 3px 3px 4px #40404057 !important;

            border: 1px solid #7F7F7F !important;

            background-color: #91BDFF !important;

            transition: background-color 0.3s ease;

        }



        .table-block-new.c-active .ActionIcon:hover {

            background: #C2DBFF !important;

        }



        .bg-new-grey {

            background-color: #D9D9D9;



        }



        .bg-new-blue {

            background-color: #4194F6;

            border: 1px solid #D9D9D9;

        }



        .bg-new-green {

            background-color: #4EA833;

            border: 1px solid #D9D9D9;

        }



        .bg-new-dark {

            background-color: #21263B;

            border: 1px solid #D9D9D9;

        }



        .bg-new-yellow {

            background-color: #FFCC00;

            border: 1px solid #D9D9D9;

        }



        .text-orange {

            color: #FFCC00 !important;

        }



        .text-grey {

            color: lightgrey !important;

        }



        .HostActive {

            font-family: Calibri;

            font-size: 9pt;

            font-weight: bold;

            color: #1EFF00;



        }



        .contract_type_button_manage label {





            border: 1px solid #D9D9D9;

            color: #BFBFBF;

            background: white;

            font-size: 11pt !important;

            width: 160px !important;

            padding-top: 7px;

            padding-bottom: 7px;



        }



        .contract_type_button_manage input[type="radio"]:checked+label,

        .contract_type_button_manage input[type="checkbox"]:checked+label {

            background-color: white;

            color: #4194F6;

            border: 1px solid #4194F6;

        }



        .contract_type_button_manage input[type="radio"]:checked:hover+label,

        .contract_type_button_manage input[type="checkbox"]:checked:hover+label {

            background-color: #F0F8FF;

            box-shadow: 0px 0px 5pt 5pt rgba(65, 148, 246, 0.6) !important;

            font-weight: bold;

            color: #4194F6;

            border: 1px solid #4194F6;

        }



        .contract_type_button_manage label:hover {

            background-color: #F9F9F9;

            color: #7F7F7F;

            font-weight: bold;

            border: 1px solid #D9D9D9;

            /* box-shadow: 0px 0px 1pt 3pt #D9D9D9 !important; */

            box-shadow: 0px 0px 5pt 5pt rgba(65, 148, 246, 0.6) !important;



        }







        .contract_type_button_manage_sm label {

            width: 120px !important;

            font-size: 10pt !important;

            padding-top: 5px;

            padding-bottom: 5px;



            border: 1px solid #D9D9D9;

            color: #BFBFBF;

            background: white;



        }





        /* .contract_type_button_manage_sm label:hover {

    background-color: #F9F9F9;

            color: #7F7F7F;

             font-weight: bold;

                border: 1px solid #D9D9D9;

                    box-shadow:0px 0px 1pt 3pt #D9D9D9 !important;



        } */



        .contract_type_button_manage_sm label:hover {

            background-color: #F9F9F9;

            color: #7F7F7F;

            font-weight: bold;

            border: 1px solid #D9D9D9;

            /* box-shadow: 0px 0px 5pt 5pt rgba(217, 217, 217, 0.6) !important; */

        }



        .contract_type_button_og input[type="checkbox"]:checked+label:hover {

            /* box-shadow: 0px 0px 5pt 5pt rgba(0, 158, 4, 0.6) !important; */

        }



        .location-tag:hover {

            background-color: #F9F9F9 !important;

        }





        .contract_type_button_og0 input[type="radio"]:checked+label {

            background-color: #E6F0FA;

            color: #4A7AAB;

            font-weight: bold;

            border: 1px solid #B0C4DE;

        }



        .contract_type_button_og1 input[type="radio"]:checked+label {

            background-color: #FFF4E5;

            color: #FFA500;

            font-weight: bold;

            border: 1px solid #FFCF75;

        }



        .contract_type_button_og2 input[type="radio"]:checked+label {

            background-color: #F2F2F2;

            color: #3F3F3F;

            font-weight: bold;

            border: 1px solid #D9D9D9;

        }





        .contract_type_button_og input[type="checkbox"]:checked+label {

            background: white;

            border: 1px solid #009E04;

            color: #009E04;



        }



        .contract_type_button_og input[type="checkbox"]:checked:hover+label {

            background: #F2FFF3;

            color: #009E04;

            /* box-shadow: 0px 0px 1pt 1pt #009E04 !important; */



        }









        .HostInActive {

            font-family: Calibri;

            font-size: 1pt;

            font-weight: bold;

            color: #BFBFBF;

        }

.btn-share[aria-describedby] ~ .tooltip .tooltip-inner {
    text-align: left !important;
    display: flex !important;
    align-items: center !important;
}

        .tooltip-inner {

            text-align: left;

        }



        /*    .block-content{

            overflow-x: hidden!important;

        }*/

        .badge-new {

            display: flex;

            align-items: center;

            justify-content: center;



            font-family: Calibri !important;

            font-size: 11pt !important;

            border-radius: 6px;

            padding-top: 2px;

            padding-bottom: 2px;

            padding-left: 15px;

            padding-right: 15px;

            width: fit-content;

        }



        .js-task {

            border: 2px solid #ECEFF4 !important;

        }



        .js-task.maintenance-tags {

            border: 2px solid #ECEFF4 !important;

        }



        .js-task:hover {

            border: 2px solid #AACEFB !important;

            /* box-shadow: 0px 0px 1pt 3pt #7F7F7F !important; */

            box-shadow: 0px 0px 5pt 5pt rgba(170, 206, 251, 0.6) !important;

        }



        .js-task.maintenance-tags:hover {

            border: 2px solid #ECEFF4 !important;

            box-shadow: 0px 3px 5px #ECEFF4 !important;

            margin-bottom: 20px !important;

            border-radius: 10px !important;

        }

        }



        label {

            color: #3F3F3F;

            font-family: Calibri !important;

            font-size: 14pt !important;

            font-weight: normal;



        }



        .tooltip-inner {

            text-align: center;

        }



        .form-control,

        .select2,

        .select2-container--default .select2-selection--single,

        .select2-container--default .select2-selection--single .select2-selection__rendered,

        .selectpicker,

        .bootstrap-select .btn {

            border-color: #D4DCEC !important;

            color: #7F7F7F !important;

            font-family: Calibri !important;

            font-weight: normal !important;

            font-size: 14pt !important;

            height: 40px;

            border-radius: 10px;

        }



        .bootstrap-select .btn {

            background-color: white;

        }



        .MainTags {

            font-size: 18pt;

            width: 140px !important;

            font-weight: bold;

            border: 1px solid #595959;

            border-radius: 5px !important;

            color: #595959

        }



        .MainTags:hover {



            background: #F2F2F2;

            /* box-shadow: 0px 0px 5pt 5pt #7F7F7F; */

        }



        /* .c-active:hover {

            background-color: #91BDFF !important;

        } */



        .LineTags {

            font-size: 10pt;

            width: 100px !important;

            padding-top: 5px;

            padding-bottom: 5px;

            border: 1px solid #7F7F7F;

            border-radius: 5px !important;

            color: #595959

        }



        .LineTags:hover {



            background: #F2F2F2;

            box-shadow: 0px 0px 5pt 5pt #D9D9D9;

        }



        .dropdown-item {

            cursor: pointer;

        }



        .ThreeDots {

            color: #595959;

        }



        .ThreeDots:hover {

            color: #69A9F8;

        }



        .bootstrap-select .dropdown-toggle:focus {

            outline: none !important;

        }



        .comments-subtext {

            color: #7F7F7F !important;

            font-family: Calibri !important;

            font-size: 10pt !important;

            font-weight: normal;

        }



        .comments-text {



            color: #595959 !important;

            font-family: Calibri !important;

            font-size: 13pt !important;

            font-weight: bold;

        }



        .comments-section-text {

            color: #595959 !important;

            font-family: Calibri !important;

            line-height: 1.3;

            font-size: 12pt !important;

            /* white-space: pre-wrap; */

            overflow-wrap: anywhere;

        }



        .btn-new,

        .btn-new-secondary {

            border: 1px solid #D9D9D9;

            background: white;

            color: #2080F4;

            font-weight: normal;

            font-family: Calibri !important;

            font-size: 12pt !important;

            border-radius: 10px;

            padding-top: 5px;

            padding-bottom: 5px;

            min-width: 90px;

        }



        .font-10pt {

            font-size: 10pt;

        }



        .font-105pt {

            font-size: 10.5pt;

        }



        .font-11pt {

            font-size: 11pt !important;

        }



        .font-12pt {

            font-size: 12pt !important;

        }



        .attachmentDivNew {



            color: #7F7F7F;

            box-shadow: 0px 0px 10px #ECEFF4 !important;

            padding: 7px 10px;

            width: 100%;

            font-size: 14pt !important;

            border-radius: 10px !important;

            border: 1px solid #ECEFF4;

        }



        .block-content .push {

            margin-bottom: 0px;

        }



        .form-group {

            margin-bottom: 4.25mm !important;

        }



        .btn-new:hover {

            background: #2080F4;

            color: white;

        }



        .btn-new-secondary:hover {



            background-color: #A6A6A6;

            color: #FFFFFF !important;

        }



        .new-block-content {

            padding-left: 9mm;

            padding-top: 5mm;

            padding-right: 9mm;

        }



        .mandatory {

            color: #E54643 !important;

        }



        .bg-new-red {

            background-color: #E54643 !important;

            border: 1px solid #D9D9D9 !important;

        }



        .text-yellow {

            color: #FFFF00 !important;

        }



        .top-div {

            position: absolute;

            top: 0;

            left: 50%;

            background: #262626;

            color: white;

            border: 1px solid #D9D9D9;

            width: 270px;

            padding-top: 3px;

            padding-bottom: 3px;

            border-radius: 10px;

            font-weight: bold;

            text-align: center;

            font-family: Calibri;

            font-size: 12pt;

            transform: translate(-50%, -50%);



        }



        .inner-body-content {

            border: 1px solid #ECEFF4;

            border-radius: 10px;

            padding-top: 30px;

            padding-bottom: 10px;

            box-shadow: 5px 5px 10px #d0d3d8

        }



        .bubble-new {

            color: #7F7F7F;

            font-family: Calibri;

            font-size: 12pt;

            padding-top: 5px;

            padding-bottom: 5px;

            border: 1px solid #ECEFF4;

            background: #F2F2F2;

            border-radius: 10px;

            padding-left: 20px;

            padding-right: 20px;

            box-shadow: 0px 0px 5px #d0d3d8;

            min-height: 36px;

        }



        .bubble-text-first {

            color: #3F3F3F;

        }



        .bubble-text-sec {

            color: #7F7F7F;

        }



        .bubble-white-new {



            font-family: Calibri;

            font-size: 12pt;

            padding-top: 5px;

            padding-bottom: 5px;

            border: 1px solid #ECEFF4;

            background: white;

            width: 100%;

            border-radius: 10px;

            padding-left: 20px;

            padding-right: 20px;

            box-shadow: 3px 3px 5px #d0d3d8;

            min-height: 36px;

        }



        .top-right-div {

            position: absolute;

            top: -15px;

            left: 20px;



            color: #3F3F3F;

            border: 1px solid #D9D9D9;

            width: 200px;

            padding-top: 3px;

            padding-bottom: 3px;

            border-radius: 10px;

            font-weight: bold;

            text-align: center;

            font-family: Calibri;

            font-size: 12pt;



        }



        .top-right-div-yellow {

            background: #FFCC00;

        }



        .top-right-div-red {

            background: #E54643;

            color: white;

        }



        .top-right-div-blue {

            background: #4194F6;

            color: white;

        }



        .top-right-div-green {

            background: #4EA833;

            color: white;

        }



        .AssetActive {

            min-height: 7mm;

            width: auto;

            border: 1px solid #ECEFF4;

            background-color: #404040;

            color: #1EFF00;

            font-family: Calibri;

            font-size: 12pt;

            font-weight: bold;

            border-radius: 10px;

            text-align: center;

            margin-bottom: 10px;

            padding-left: 20px;

            padding-right: 20px;

            display: flex;

            justify-content: center;

            align-items: center;

            word-break: break-all;

        }



        .AssetInactive {

            min-height: 7mm;

            word-break: break-all;

            border: 1px solid #ECEFF4;

            background-color: #404040;

            color: #7F7F7F;

            padding-left: 10px;

            padding-right: 10px;

            display: flex;

            justify-content: center;

            align-items: center;

            margin-bottom: 10px;

            border-radius: 10px;

            text-align: center;

            font-family: Calibri;

            font-size: 12pt;

            font-weight: bold;

        }



        .new-nav {

            position: sticky;

            top: 0px;

            z-index: 100;

        }



        .js-task {

            border: 2px solid #ECEFF4;

            box-shadow: 0px 3px 5px #ECEFF4 !important;

            margin-bottom: 20px !important;

            border-radius: 10px !important;

        }



        .drag-handle:hover>svg {

            color: #AACEFB !important;

        }



        .tooltip-inner {

            background: #3A3B42;

            border-color: #3A3B42;

            font-family: Calibri !important;

            font-size: 10pt !important;

            font-weight: bold !important;

            opacity: 1 !important;

            border-radius: 7px;

            padding: 7px 15px;

        }



        .section-header {

            color: #595959;

            font-size: 18pt;

            font-family: Calibri;

        }



        .new-block {

            border-radius: 10px;

            border: 1px solid lightgrey;

            padding-top: 5mm !important;

            padding-bottom: 5mm !important;

        }



        .tooltip.show {

            opacity: 1 !important;



        }



        .tooltip .arrow {

            border-color: #595959 !important;

        }



        .card-round {

            border-radius: 10px;

        }



        .header-new-text {

            color: white;

            font-family: Calibri;

            font-size: 18pt;

            font-weight: bold;

        }



        .header-new-subtext {

            color: #EEEEEE;

            font-family: Calibri;

            font-size: 12pt;

            font-weight: normal;

        }



        .py-new-header {

            padding-top: 8px;

            padding-bottom: 8px;



        }



        .new-header-icon-div a {

            padding-left: 10px;

            margin-left: 3px;

            margin-right: 3px;

            padding-right: 10px;



            padding-top: 7px;

            margin-top: 2px;

            padding-bottom: 7px;

            /*border: 1px solid transparent;*/

        }





        .new-header-icon-div a:hover {
            color: white!important;

/*            background: #3A3B42 !important;

            border: 1px solid transparent !important;

            border-radius: 10px;*/

        }



        .tooltip .arrow::before {

            border-bottom-color: #ffffffe6 !important;

            border-top-color: #ffffffe6 !important;

        }



        .error {

            color: #d73838;

        }



        .select2 {

            width: 100% !important;

        }



        .filepond--root {

            min-height: 220px;



        }



        .btn-dual {



            background: #21263C;

            border: none;

        }



        .d1 {

            padding-right: 12px;

            padding-left: 12px;

        }



        .d2 {

            padding: 7px 12px;

        }



        .d3 {

            color: white !important;

            padding: 7px 12px 5px 12px !important;

            border-radius: 5px

        }



        .d3:hover {

            background: #333850;



        }



        .btn-dual:not(:disabled):not(.disabled).active,

        .btn-dual:not(:disabled):not(.disabled):active,

        .show>.btn-dual.dropdown-toggle {

            color: #16181a;

            background-color: #408DFB;

            border-color: #333850;

        }



        .btn-dual:hover,

        .btn-dual:focus {

            border-color: #4194F6 !important;

            background: #4194F6 !important

        }



        .nav-main-submenu {



            padding-left: 0px;

        }



        .nav-main-submenu .nav-main-item a {

            padding-left: 40px;

        }



        .nav-main-item .active {

            border: 2px solid grey;



        }



        ::-webkit-scrollbar {

            width: 5px;

            height: 10px;

            background-color: #F5F5F5;

        }



        .col-lg-8::-webkit-scrollbar {

            /*width: 0px;*/

            height: 10px;

            background-color: #F5F5F5;

        }



        ::-webkit-scrollbar-thumb {

            border-radius: 10px;

            background-image: -webkit-gradient(linear, left bottom, left top, color-stop(0.44, #9e9e9e), color-stop(0.72, #9e9e9e), color-stop(0.86, #9e9e9e));



        }



        .bg-orange {

            background-color: orange;

        }



        .thead-dark {

            background-color: #BFBFBF !important;

        }



        .thead-dark th {

            font-size: 9.5pt;

            background-color: #BFBFBF !important;

            border: none !important;

            font-family: Source Sans Pro;

            color: #262626 !important;

            text-transform: uppercase;



        }



        .font-size-h2 {

            font-size: 1.5rem !important;

        }



        .block-title {

            font-size: 0.9rem;

        }



        .TopArea input,

        .TopArea select {

            font-size: 0.9rem !important;

        }



        .TopArea a,

        .TopArea .btn {

            font-size: 0.9rem;

        }



        tbody .btn-group .btn {

            font-size: 0.7rem;

        }



        .content-side {

            padding-left: 0px;

            padding-right: 0px;

        }



        #accordion2_q1 {

            font-size: 0.8rem;

        }



        .c-active {

            background-color: #C2DBFF;

            box-shadow: 3px 3px 4px #40404057 !important;

            border: 1px solid #7F7F7F !important;

        }



        #accordion2_q1 .btn,

        #accordion2_q1 input,

        #accordion2_q1 select {

            font-size: 0.8rem;

        }



        .breadcrumb-item {

            font-size: 0.8rem;

        }



        #accordion2_q1 .bootstrap-select .dropdown-item {

            font-size: 0.8rem !important;

        }




        .dropdown-menu {

            z-index: 1040;

        }



        .floatThead-container {

            z-index: 999 !important;

        }



        .thead-dark th a {

            color: #262626 !important;

        }



        td {

            color: #0D0D0D !important;

            font-size: 10pt;

            font-family: Source Sans Pro;

        }
 .NewNavTab{
     border-bottom:none!important;
 }
 .NewNavTab .active{
                background: white;
    border: 2px solid #4ea833!important;
    color: #009E04!important;
    
        }
         .NewNavTab a:hover{
              background: #F2FFF3!important;
        color: #009E04!important;
     }
        .NewNavTab a{
                background: white;
    border: 2px solid #D9D9D9!important;
    color: #BFBFBF!important;
    width: 70px !important;
    font-size: 9pt !important;
    padding-top: 2px;
    padding-bottom: 2px;
    padding-left:0px;
    padding-right:0px;
    border-radius: 10px!important;
    font-weight: normal;
    text-align: center;
        }
        .bg-primary-dark {

            background-color: #BFBFBF !important;

            color: #262626 !important;

            font-family: 15px;

            font-weight: bold;

        }



        .bg-primary-dark * {

            color: #262626 !important;

            font-size: 12pt;

            font-weight: bold;



        }



        .tablemodal td,

        .tablemodal th {

            color: #0D0D0D !important;

            font-family: Source Sans Pro;

            font-size: 9.5pt !important

        }



        #assetdiv th,

        #assetdiv td {

            font-size: 8pt !important;

            color: #0D0D0D;

            font-family: Open Sans

        }



        #page-container {

            min-height: 75vh !important;

        }



        @media (min-width: 1200px) {

            #page-container.main-content-narrow>#main-container .content {

                width: 100%;

            }



            .badge {

                font-size: 13px !important;

            }



            @media (min-width: 768px) {

                .content {

                    width: 100%;

                    margin: 0 auto;

                    padding: 0.5rem 0.5rem 1px;



                }

            }



            .tooltip1 {

                position: relative;

                display: inline-block;

                border-bottom: 1px dotted black;

            }



            .tooltip1 .tooltiptext {

                visibility: hidden;

                width: 230px;



                background-color: #555;

                color: #fff;

                text-align: center;

                border-radius: 6px;

                padding: 5px 0;

                position: absolute;

                z-index: 400000000000000000000;

                bottom: 0%;

                left: 50%;

                margin-left: -60px;

                opacity: 0;

                transition: opacity 0.3s;

            }



            .c4 {

                background-color: #D9D9D9;

                border: 1px solid #7F7F7F;

                color: black !important;

                padding-left: 5px;

                padding-right: 5px;

                font-family: Calibri;

                border-style: dashed;

                border-radius: 2px;

                width: fit-content;

                padding-top: 2px;

                padding-bottom: 2px;

            }



            .dropdown-toggle::after {

                display: none !important;

            }



            .c4-p {

                background-color: #343A40;

                border: 1px solid #7F7F7F;

                color: white !important;

                padding-left: 5px;

                padding-right: 5px;

                font-family: Calibri;

                border-style: dashed;

                border-radius: 2px;

                width: fit-content;

                padding-top: 2px;

                padding-bottom: 2px;

            }



            .c4-s {

                background-color: #7F659F;

                border: 1px solid #7F659F;

                color: white !important;

                padding-left: 5px;

                padding-right: 5px;

                font-family: Calibri;

                border-style: dashed;

                border-radius: 2px;

                width: fit-content;

                padding-top: 2px;

                padding-bottom: 2px;

            }



            .c4-v {

                background-color: #4194F6;

                border: 1px solid #7F7F7F;

                color: white !important;

                padding-left: 5px;

                padding-right: 5px;

                font-family: Calibri;

                border-style: dashed;

                border-radius: 2px;

                width: fit-content;

                padding-top: 2px;

                padding-bottom: 2px;

            }



            .c2 {

                font-size: 11pt !important;

                color: black !important;

            }



            .tooltip1:hover .tooltiptext {

                visibility: visible;

                opacity: 1;

            }



            .sidebar-o .nav-main-link {

                padding-left: 20px;

            }



            .sidebar-o .nav-main-link:hover {

                background: #2b3048 !important;

            }



            .sidebar-dark #sidebar .nav-main-link.active {

                background-color: #578CB7 !important;

                border: #578CB7 !important;

            }



            .breadcrumb-item a {

                color: white

            }



            .headerSetting {

                color: white !important;

                padding-top: 8px;

                padding-bottom: 12px;

                padding-left: 13px;

                padding-right: 13px;

                border-radius: 5px

            }



            .headerSetting:hover {

                background: #333850;



            }



            .imgAvatar {

                border: 2px solid #9e9e9e;

                width: 40px !important;

                height: 40px !important;

            }



            .imgAvatar:hover {

                border: 2px solid #408DFB

            }



            .page-header {

                background: #21263C !important;

            }



            .searchNew {

                color: #F0F0F0 !important;

                border-color: #6C7184 !important;

                background: #21263C !important;

                border-radius: 8px

            }



            .page-header select {

                background: #333850 !important;

                padding: 3px 0px !important;

                border-color: #6C7184 !important;

                border-radius: 7px !important;

                height: 37px;

                color: #F0F0F0 !important;



            }



            .page-header select:hover {

                border-color: #408DFB !important;

            }



            .searchNew:hover+.input-group-append .input-group-text {

                border-top-color: #408DFB !important;

                border-bottom-color: #408DFB !important;

                border-right-color: #408DFB !important;



            }



            .searchNew:hover {

                border-top-color: #408DFB !important;

                border-bottom-color: #408DFB !important;

                border-left-color: #408DFB !important;



            }



            .input-group-text {

                background: transparent;

                padding-left: 7px;

                padding-right: 7px;

                border-color: #6C7184;

                border-top-right-radius: 8px;

                border-bottom-right-radius: 8px;

            }



            .pagination {



                border-color: #6C7184 !important;

                font-family: Calibri;

                border-radius: 0px !important;



            }



            .pagination a {

                background: #333850 !important;

                color: #F0F0F0 !important;

                border-color: #6C7184 !important;

                border-radius: 0px !important;

                border-right: none;

                border-left: none;

            }



            .pagination a:hover {

                background: #171D29 !important;

            }



            .pagination .active a {



                background: #408DFB !important;



                border-color: #6C7184 !important;



            }







            .input-group {

                max-width: 60%;

                transition: max-width 0.3s ease-in-out;

            }



            .input-group:hover,

            .input-group:focus {

                max-width: 90%;

            }



            .searchNew {

                width: 100%;

            }



            .input-group-append {

                width: 50px;

                overflow: hidden;

                transition: width 0.3s ease-in-out;

            }



            .input-group:hover .input-group-append,

            .input-group:focus .input-group-append {

                width: auto;

            }



            #ajax-overlay {

                position: fixed;

                top: 0;

                left: 0;

                width: 100%;

                height: 100%;

                background: rgba(0, 0, 0, 0.5); /* Semi-transparent dark overlay */

                z-index: 9999; /* Ensure it's on top */

                display: none; /* Initially hidden */

            }

            
    </style>

</head>



<body>



    @yield('sidebar')



    <!-- Header -->

    <header id="page-header" class="page-header" style="">

        <!-- Header Content -->

        <div class="content-header w-100 px-3">

            <!-- Left Section -->

            <div>



                <!--   <button type="button" class="btn btn-dual mt-2" data-toggle="layout" data-action="sidebar_toggle">

                            <i class="fa fa-fw fa-bars"></i>

                        </button>

                      -->



                <nav class="flex-sm-00-auto ml-sm-3 float-right" aria-label="breadcrumb">



                    <ol class="breadcrumb">





                        @if (Request::is('clients'))

                            <li class="breadcrumb-item active text-dark"> <b>Clients</b> </li>



                        @endif

                        @if (Request::is('add-clients'))

                            <li class="breadcrumb-item  "><a href="{{ url('clients') }}"> Clients</a> </li>

                            <li class="breadcrumb-item active text-dark "><b>Add Clients</b></li>

                        @endif



                        @if (Request::is('edit-clients'))

                            <li class="breadcrumb-item  "><a href="{{ url('clients') }}"> Clients</a> </li>

                            <li class="breadcrumb-item active text-dark "><b>Edit Clients</b></li>

                        @endif



                        @if (Request::is('users'))

                            <li class="breadcrumb-item active text-dark"><b> Users</b> </li>

                        @endif



                        @if (Request::is('add-users'))

                            <li class="breadcrumb-item  "><a href="{{ url('users') }}"> Users</a> </li>

                            <li class="breadcrumb-item active text-dark"><b> Add User</b> </li>

                        @endif

                        @if (Request::is('edit-users'))

                            <li class="breadcrumb-item  "><a href="{{ url('users') }}"> Users</a> </li>

                            <li class="breadcrumb-item active text-dark"><b> Edit User</b> </li>

                        @endif

                        @if (Request::is('vendors'))



                            <li class="breadcrumb-item active text-dark"><b> Vendors</b> </li>

                        @endif

                        @if (Request::is('add-vendors'))

                            <li class="breadcrumb-item  "><a href="{{ url('vendors') }}"> Vendors</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Add Vendors</b></li>

                        @endif

                        @if (Request::is('edit-vendors'))

                            <li class="breadcrumb-item  "><a href="{{ url('vendors') }}"> Vendors</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Edit Vendors</b></li>

                        @endif

                        @if (Request::is('distributors'))

                            <li class="breadcrumb-item active text-dark"><b> Distributors</b> </li>

                        @endif

                        @if (Request::is('add-distributors'))

                            <li class="breadcrumb-item  "><a href="{{ url('distributors') }}"> Distributors</a> </li>

                            <li class="breadcrumb-item active text-dark"><b> Add Distributors</b></li>

                        @endif



                        @if (Request::is('edit-distributors'))

                            <li class="breadcrumb-item  "><a href="{{ url('distributors') }}"> Distributors</a> </li>

                            <li class="breadcrumb-item active text-dark"><b> Edit Distributors</b></li>

                        @endif



                        @if (Request::is('sites'))

                            <li class="breadcrumb-item active text-dark"><b> Sites</b> </li>

                        @endif

                        @if (Request::is('add-sites'))

                            <li class="breadcrumb-item  "><a href="{{ url('sites') }}"> Sites</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Add Sites</b></li>

                        @endif



                        @if (Request::is('edit-sites'))

                            <li class="breadcrumb-item  "><a href="{{ url('sites') }}"> Sites</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Edit Sites</b></li>

                        @endif



                        @if (Request::is('operating-systems'))

                            <li class="breadcrumb-item active text-dark"><b> Operating Systems</b> </li>

                        @endif

                        @if (Request::is('add-operating-systems'))

                            <li class="breadcrumb-item  "><a href="{{ url('/operating-systems') }}"> Operating

                                    Systems</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Add Operating Systems<b></li>

                        @endif



                        @if (Request::is('edit-operating-systems'))

                            <li class="breadcrumb-item  "><a href="{{ url('/operating-systems') }}"> Operating

                                    Systems</a> </li>

                            <li class="breadcrumb-item active text-dark"><b> Edit Operating Systems</b></li>

                        @endif





                        @if (Request::is('domains'))

                            <li class="breadcrumb-item active text-dark"><b> Domains</b> </li>

                        @endif

                        @if (Request::is('add-domains'))

                            <li class="breadcrumb-item  "><a href="{{ url('/domains') }}">Domains</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Add Domains<b></li>

                        @endif



                        @if (Request::is('edit-domains'))

                            <li class="breadcrumb-item  "><a href="{{ url('/domains') }}"> Domains</a> </li>

                            <li class="breadcrumb-item active text-dark"><b> Edit Domains</b></li>

                        @endif





                        @if (Request::is('asset-type'))

                            <li class="breadcrumb-item active text-dark"><b> Asset Type</b> </li>

                        @endif

                        @if (Request::is('add-asset-type'))

                            <li class="breadcrumb-item  "><a href="{{ url('/asset-type') }}">Asset Type</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Add Asset Type<b></li>

                        @endif



                        @if (Request::is('edit-asset-type'))

                            <li class="breadcrumb-item  "><a href="{{ url('/asset-type') }}"> Asset Type</a> </li>

                            <li class="breadcrumb-item active text-dark"><b> Edit Asset Type</b></li>

                        @endif







                        @if (Request::is('network'))

                            <li class="breadcrumb-item active text-dark"><b> Network</b> </li>

                        @endif

                        @if (Request::is('add-network'))

                            <li class="breadcrumb-item  "><a href="{{ url('/network') }}">Network</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Add Network<b></li>

                        @endif



                        @if (Request::is('edit-network'))

                            <li class="breadcrumb-item  "><a href="{{ url('/network') }}"> Network</a> </li>

                            <li class="breadcrumb-item active text-dark"><b> Edit Network</b></li>

                        @endif





                        @if (Request::is('virtual'))

                            <li class="breadcrumb-item active text-dark"><b> Virtual </b> </li>

                        @endif

                        @if (Request::is('virtual/*'))

                            <li class="breadcrumb-item active text-dark"><b> Virtual / <span

                                        class="text-capitalize">{{ $page_type }}</span></b> </li>

                        @endif



                        @if (Request::is('add-assets/*'))

                            @if ($type == 'virtual')

                                <li class="breadcrumb-item  "><a href="{{ url('/virtual') }}">Virtual</a> </li>

                            @else

                                <li class="breadcrumb-item  "><a href="{{ url('/physical') }}">Physical</a> </li>

                            @endif

                            <li class="breadcrumb-item active text-dark"> <b>Add {{ $type }} Asset<b></li>

                        @endif



                        @if (Request::is('edit-assets'))

                            @if ($type == 'virtual')

                                <li class="breadcrumb-item  "><a href="{{ url('/virtual') }}">Virtual</a> </li>

                            @else

                                <li class="breadcrumb-item  "><a href="{{ url('/physical') }}">Physical</a> </li>

                            @endif

                            <li class="breadcrumb-item active text-dark"><b> Edit {{ $type }} Asset</b></li>

                        @endif







                        @if (Request::is('physical'))

                            <li class="breadcrumb-item active text-dark"><b> Physical </b> </li>

                        @endif



                        @if (Request::is('physical/*'))

                            <li class="breadcrumb-item active text-dark"><b> Physical / <span

                                        class="text-capitalize">{{ $page_type }}</span></b> </li>

                        @endif











                        @if (Request::is('contract/*'))

                            <li class="breadcrumb-item active text-dark"><b> Contract / <span

                                        class="text-capitalize">{{ $type }}</span></b> </li>

                        @endif

                        @if (Request::is('contract'))

                            <li class="breadcrumb-item active text-dark"><b> Contract / <span

                                        class="text-capitalize">All</span></b> </li>

                        @endif



                        @if (Request::is('add-contract/*'))



                            <li class="breadcrumb-item  "><a href="{{ url('/contract/') }}/{{ $type }}"

                                    class="text-capitalize">{{ $type }}</a> </li>



                            <li class="breadcrumb-item active text-dark"> <b>Add {{ $type }} Contract<b></li>

                        @endif



                        @if (Request::is('edit-contract'))



                            <li class="breadcrumb-item active text-dark"><b> Edit Contract</b></li>

                        @endif



                        @if (Request::is('renew-contract'))



                            <li class="breadcrumb-item active text-dark"><b>Renew Contract</b></li>

                        @endif

                        @if (Request::is('ssl-certificate'))



                            <li class="breadcrumb-item active text-dark"><b>SSL Certificate</b></li>

                        @endif



                        @if (Request::is('add-ssl-certificate'))



                            <li class="breadcrumb-item  "><a href="{{ url('/ssl-certificate') }}"

                                    class="text-capitalize">SSL Certificate</a> </li>



                            <li class="breadcrumb-item active text-dark"> <b>Add SSL Certificate<b></li>

                        @endif



                        @if (Request::is('edit-ssl-certificate'))



                            <li class="breadcrumb-item  "><a href="{{ url('/ssl-certificate') }}"

                                    class="text-capitalize">SSL Certificate</a> </li>



                            <li class="breadcrumb-item active text-dark"> <b>Edit SSL Certificate<b></li>

                        @endif



                        @if (Request::is('renew-ssl-certificate'))



                            <li class="breadcrumb-item  "><a href="{{ url('/ssl-certificate') }}"

                                    class="text-capitalize">SSL Certificate</a> </li>



                            <li class="breadcrumb-item active text-dark"> <b>Renew SSL Certificate<b></li>

                        @endif



                        @if (Request::is('notifications'))





                        @endif





                    </ol>





                </nav>







            </div>

            <!-- END Left Section -->



            <!-- Right Section -->

            <div>



                @if (@Auth::user()->role == 'admin')



                    <a href="{{ url('settings') }}" data-toggle="tooltip"

                        data-title="Settings"class="mr-2 text-dark headerSetting"><img

                            src="{{ asset('public/img/ui-icon-settings.png') }}" width="23px"></a>



                @endif

                <!-- User Dropdown -->

                <div class="dropdown d-inline-block">

                    <a type="button" class="  " id="page-header-user-dropdown" data-toggle="dropdown"

                        aria-haspopup="true" aria-expanded="false">

                        @if (@Auth::user()->user_image == '')

                            <img class="img-avatar imgAvatar img-avatar48"

                                src="{{ asset('public') }}/dashboard_assets/media/avatars/avatar2.jpg"

                                alt="">

                        @else

                            <img class="img-avatar imgAvatar img-avatar48"

                                src="{{ asset('public/client_logos/') }}/{{ Auth::user()->user_image }}"

                                alt="">



                        @endif



                    </a>

                    <div class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="page-header-user-dropdown">



                        <div class="p-2">

                            @auth

                                <a class="dropdown-item" href="{{ url('change-password') }}">

                                    <i class="far fa-fw fa-user mr-1"></i> My Profile

                                </a>











                                <!-- END Side Overlay -->

                                <form id="logout-form" method="post" action="{{ url('logout') }}">

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

                    <!-- END User Dropdown -->



                    <!-- Notifications Dropdown -->



                    <!-- Toggle Side Overlay -->

                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->



                    <!-- END Toggle Side Overlay -->

                </div>

                <!-- END Right Section -->

            </div>

            <!-- END Header Content -->





            <!-- Header Loader -->

            <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->

            <div id="page-header-loader" class="overlay-header bg-header-dark">

                <div class="bg-white-10">

                    <div class="content-header">

                        <div class="w-100 text-center">

                            <i class="fa fa-fw fa-sun fa-spin text-white"></i>

                        </div>

                    </div>

                </div>

            </div>

            <!-- END Header Loader -->

        </header>

        @yield('content')

        @yield('footer')

