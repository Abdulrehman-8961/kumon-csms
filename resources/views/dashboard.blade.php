@extends('layouts.header2')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')

<?php
$userAccess=explode(',',Auth::user()->access_to_client);
 
 ?>

<style type="text/css">
    .dropdown-menu {
        z-index: 100000 !important;
    }

    .pagination {
        margin-bottom: 0px;
    }

    .ActionIcon {

        border-radius: 50%;
        padding: 6px;
    }

    .ActionIcon:hover {

        background: #dadada;
    }

    body {
        overflow: -moz-scrollbars-vertical;
        overflow-x: hidden;
    }

    .blockDivs .block-header-default {
        background-color: #f1f3f8;
        padding: 7px 1.25rem;
    }

    .blockDivs {
        border: 1px solid lightgrey;
        margin-bottom: 10px !important;
    }

    .contract_type_button label,
    .contract_type_button input {}

    .contract_type_button {
        float: left;
    }

    .contract_type_button input[type="radio"] {
        opacity: 0.011;
        z-index: 100;

        position: absolute;
    }

    .contract_type_button input[type="radio"]:checked+label {
        background: #4194F6;
        font-weight: bold;
        color: white;
    }

    .contract_type_button label:hover {



        background-color: #EEEEEE;
        color: #7F7F7F;


    }

    .contract_type_button label {

        width: 150px;

        border-color: #D9D9D9;
        color: #7F7F7F;
        font-size: 12pt;


    }

    .modal-backdrop {
        background-color: #00000080 !important;
    }


    .attachmentDivNew:hover {
        color: #FFFFFF !important;
        background-color: #4194F6;
    }

    .alert-info .close {
        color: #BDBDBE !important;
        font-size: 30px !important;
        top: 10px !important;
        right: 15px !important;
        opacity: 1 !important;
        font-weight: 200 !important;
        width: 33px;
        padding-bottom: 3px;
    }

    .alert-info .close:hover {
        background-color: white !important;
        border-radius: 50%;
    }

    .modal-lg,
    .modal-xl {
        max-width: 950px;
    }

    .alert-info .btn-tooltip {
        color: #00B0F0 !important;
        font-family: Calibri !important;
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

    .bs-select-all,
    .bs-deselect-all,
    .bs-actionsbox .btn-light {
        border: 1px solid #D9D9D9 !important;
        background: white !important;

        color: #2080F4 !important;
        font-weight: normal !important;
        font-family: Calibri !important;
        font-size: 12pt !important;
        border-radius: 15px !important;
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        margin-top: 10px !important;
        margin-bottom: 10px !important;
        margin-left: 10px;
        margin-right: 10px;
        height: 35px !important;
        padding-left: 10px;
        padding-right: 10px;
        min-width: 90px !important;
    }


    .bs-deselect-all:hover {
        background-color: #EEEEEE !important;
        color: #7F7F7F !important;
    }

    .bs-select-all:hover {
        background-color: #EEEEEE !important;
        color: #7F7F7F !important;
    }

    .c1 {
        color: #3F3F3F;
        font-family: 'Calibri';
    }

    .c2 {
        color: #7F7F7F;
        font-family: 'Calibri';
    }

    .c3 {
        color: #595959;
        font-family: 'Calibri';
    }

    .contract_type_button label,
    .contract_type_button input {}

    .contract_type_button {
        float: left;
    }

    .contract_type_button input[type="radio"] {
        opacity: 0.011;
        z-index: 100;

        position: absolute;
    }

    .contract_type_button input[type="radio"]:checked+label {
        background: #4194F6;
        font-weight: bold;
        color: white;
    }

    .contract_type_button label:hover {



        background-color: #EEEEEE;
        color: #7F7F7F;


    }

    .contract_type_button label {

        width: 150px;

        border-color: #D9D9D9;
        color: #7F7F7F;
        font-size: 12pt;


    }

    .modal-backdrop {
        background-color: #00000080 !important;
    }

    .alert-info,
    .alert {

        width: auto !important;
        padding-right: 70px;
        background-color: #262626 !important;

        color: #FFFFFF !important;
        font-family: Calibri !important;
        font-size: 14pt !important;
        padding-top: 14px;
        padding-bottom: 14px;
        z-index: 11000 !important;
    }

    .attachmentDivNew:hover {
        color: #FFFFFF !important;
        background-color: #4194F6;
    }

    .alert-info .close {
        color: #BDBDBE !important;
        font-size: 30px !important;
        top: 10px !important;
        right: 15px !important;
        opacity: 1 !important;
        font-weight: 200 !important;
        width: 33px;
        padding-bottom: 3px;
    }

    .alert-info .close:hover {
        background-color: white !important;
        border-radius: 50%;
    }

    .alert-info .btn-tooltip {
        color: #00B0F0 !important;
        font-family: Calibri !important;
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
        color: #1EFF00;
        letter-spacing: 0px;

    }

    .HostInActive {
        font-family: Calibri;
        font-size: 9pt;
        font-weight: bold;
        color: #E54643;
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


    .headerSetting {
        padding: 5px 10px !important;
    }
</style>

<style type="text/css">
    .cardText {
        font-size: 20px;
        font-weight: bold
    }

    .columnblock {
        box-shadow: 5px 4px 10px lightgrey;
        border-radius: 8px !important;
    }
</style>
<!-- Main Container -->
<main id="main-container">
    <!-- Hero -->
    <div class="content ">


    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content px-4">

    </div>
    </div>

    </div>
    </div>
    <!-- END Latest Orders + Stats -->
    </div>
    <!-- END Page Content -->
</main>
<!-- END Main Container -->

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
    crossorigin="anonymous"></script>
<script src="{{asset('public/dashboard_assets/js/dashmix.app.min.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
    $(function(){
   @if(Session::has('success'))
       
         Dashmix.helpers('notify', {align: 'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> {{Session::get('success')}}', delay: 5000});

  @endif
})
</script>
@endsection('content')