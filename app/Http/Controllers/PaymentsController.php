<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class PaymentsController extends Controller
{
    public function index()
    {
        $title = "Payments";
        return view('payments.read', compact('title'));
    }

    public function getPaymentContent(Request $request)
    {
        $id = $request->input('id');
        if (!isset($id)) {
            return response()->json(['content' => '']);
        }

        $row = DB::table('client_payments as cp')
            ->join('clients as c', 'c.id', '=', 'cp.client_id')
            ->select('cp.*', 'c.client_display_name', 'c.payment_method')
            ->where('cp.id', $id)
            ->first();

        if (!$row) {
            return response()->json(['content' => '']);
        }

        $paymentRef = $row->reference_no ?? $row->payment_ref ?? '';

        $html = '
        <div class="block new-block position-relative mt-2">
            <div class="block-content py-0" style="padding-left: 30px; padding-right: 30px">
                <div class="row">
                    <div class="col-sm-12">
                        <h5 class="titillium-web-black mb-3 text-darkgrey">Payment Details</h5>
                    </div>
                    <div class="col-sm-6">
                        <div class="border p-2 mb-3 border-style pl-3">
                            <h6 class="font-titillium content-title mb-1 fw-700">Client</h6>
                            <div class="d-flex pt-1 mb-1">
                                <i class="fa-light fa-user-tie text-grey fs-18"></i>
                                <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . $row->client_display_name . '</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="border p-2 mb-3 border-style pl-3">
                            <h6 class="font-titillium content-title mb-1 fw-700">Payment Method</h6>
                            <div class="d-flex pt-1 mb-1">
                                <i class="fa-light fa-credit-card text-grey fs-18"></i>
                                <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . ($row->payment_method ?? '-') . '</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border p-2 mb-3 border-style pl-3">
                            <h6 class="font-titillium content-title mb-1 fw-700">Month</h6>
                            <div class="d-flex pt-1 mb-1">
                                <i class="fa-light fa-calendar text-grey fs-18"></i>
                                <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . ($row->kumon_month ?? '-') . '</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border p-2 mb-3 border-style pl-3">
                            <h6 class="font-titillium content-title mb-1 fw-700">Payment Type</h6>
                            <div class="d-flex pt-1 mb-1">
                                <i class="fa-light fa-money-bill text-grey fs-18"></i>
                                <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . ($row->payment_type ?? '-') . '</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border p-2 mb-3 border-style pl-3">
                            <h6 class="font-titillium content-title mb-1 fw-700">Amount</h6>
                            <div class="d-flex pt-1 mb-1">
                                <i class="fa-light fa-dollar-sign text-grey fs-18"></i>
                                <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">$' . number_format((float) $row->amount, 2) . '</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border p-2 mb-3 border-style pl-3">
                            <h6 class="font-titillium content-title mb-1 fw-700">Payment Date</h6>
                            <div class="d-flex pt-1 mb-1">
                                <i class="fa-light fa-calendar-day text-grey fs-18"></i>
                                <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . Carbon::parse($row->payment_date)->format('d-M-Y') . '</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border p-2 mb-3 border-style pl-3">
                            <h6 class="font-titillium content-title mb-1 fw-700">Reference No.</h6>
                            <div class="d-flex pt-1 mb-1">
                                <i class="fa-light fa-hashtag text-grey fs-18"></i>
                                <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . ($paymentRef !== '' ? $paymentRef : '-') . '</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return response()->json([
            'content' => $html,
        ]);
    }
}
