<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Mail as Mailer;
use Mail;
use Hash;
use PDF;
use Illuminate\Support\Facades\Schema;

use Excel;
use Illuminate\Support\Str;

use App\Exports\ExportClients;
use App\Exports\ExportUsers;
use App\Exports\ExportVendors;
use App\Exports\ExportSites;
use App\Exports\ExportDistributors;
use App\Exports\ExportAssetType;
use App\Exports\ExportExcelNetwork;
use App\Mail\UserMail;
use App\Exports\ExportOperatingSystems;
use App\Exports\ExportDomains;
use App\Exports\ExportSystemCategory;
use App\Exports\ExportSystemTypes;
use Cache;
use Illuminate\Support\Facades\DB as FacadesDB;
use Validator;
use Carbon\Carbon;

class ClientsController extends Controller
{
    //
    public function __construct() {}

    public function Clients()
    {
        $title = "Clients";
        return view('clients.clientsView', compact('title'));
    }
    public function getClientContent(Request $request)
    {
        $id = $request->input('id');
        if (isset($id)) {
            $q = DB::table('clients')->where('id', $id)->first();
            $message = DB::table('pinned_messages')
                ->leftJoin('users', 'users.id', '=', 'pinned_messages.added_by')
                ->where('linked_id', $id)
                ->where('page', 'client')
                ->where(function ($query) {
                    $query->where('is_deleteable', 1)
                        ->orWhere('pinned_messages.created_at', '>=', Carbon::now());
                })
                ->select('pinned_messages.*', 'users.firstname', 'users.lastname')
                ->get();
            $regular_messages = DB::table('pinned_messages')
                ->leftJoin('users', 'users.id', '=', 'pinned_messages.added_by')
                ->where('linked_id', $id)
                ->where('page', 'client')
                ->where('status', 'regular')
                ->where(function ($query) {
                    $query->where('is_deleteable', 1)
                        ->orWhere('pinned_messages.created_at', '>=', Carbon::now());
                })
                ->select('pinned_messages.*', 'users.firstname', 'users.lastname')
                ->get();
            $html = '';

            $html .= '</div> 

                                </div>

                            </div>

                        </div>



                        <div class="audit-log-download-toast" role="status" aria-live="polite">
                                                  <div class="d-flex align-items-center justify-content-between">
                                                      <div class="d-flex align-items-center">
                                                          <i class="fa-light fa-circle-check mr-2"></i>
                                                          <span class="font-titillium fs-14 text-darkgrey">Audit Log downloaded successfully!</span>
                                                      </div>
                                                      <button type="button" data-section="audit-log-download-toast"
                                                          class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                                          <i class="fa-light fa-xmark"></i>
                                                      </button>
                                                  </div>
                                              </div>
                                            <div class="tab-content" id="nav-tabContent">
                                            <div class="tab-pane fade show active" id="nav-main-client" role="tabpanel" aria-labelledby="nav-main-tab-client">

                                            <div class="block new-block position-relative mt-2">
  <div
    class="block-content py-0"
    style="padding-left: 30px; padding-right: 30px"
  >
    <div class="row">
      <div class="col-sm-12">
        <h5 class="titillium-web-black mb-3 text-darkgrey">
          General Information
        </h5>
      </div>
      <div class="col-sm-12">
        <div class="border p-2 mb-3 border-style pl-3">
          <h6 class="font-titillium content-title mb-1 fw-700">Client Name</h6>
          <div class="d-flex pt-1 mb-1">
            <i class="fa-light fa-user-tie text-grey fs-18"></i>
            <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
              ' . $q->salutation . ' ' . $q->firstname . ' ' . $q->lastname . '
            </h6>
          </div>
        </div>
      </div>
      <div class="col-sm-6 mb-3">
        <div class="border border-style h-100 mb-3 p-2 pl-3">
          <h6 class="font-titillium content-title mb-1 fw-700">Address</h6>
          <div class="d-flex pt-1 mb-1">
            <i class="fa-light fa-buildings text-grey fs-18"></i>
            <h6
              class="font-titillium text-grey fw-300 mb-0 ml-2"
              style="line-height: 1.5"
            >
              <b>' . $q->client_address . '</b><br />
              ' . $q->city . ', ' . $q->state . '<br />
              ' . $q->zip . '
            </h6>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-6">
          <div class="border p-2 mb-3 border-style pl-3 position-relative field-action-wrapper">
            <button type="button" class="btn field-action-btn client-read-phone-btn" title="Call" aria-label="Call"
                data-phone="' . $q->work_phone . '">
              <i class="fa-light fa-phone-arrow-up-right"></i>
            </button>
            <h6 class="font-titillium content-title mb-1 fw-700">
              Telephone No.
            </h6>
            <div class="d-flex pt-1 mb-1">
              <i class="fa-light fa-buildings text-grey fs-18"></i>
              <h6
                class="font-titillium text-grey fw-300 mb-0 ml-2"
                style="line-height: 1.5"
              >
                ' . $q->work_phone . '
              </h6>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="border p-2 mb-3 border-style pl-3">
            <h6 class="font-titillium content-title mb-1 fw-700">
              Payment Method
            </h6>
            <div class="d-flex pt-1 mb-1">
              <i class="fa-light fa-buildings text-grey fs-18"></i>
              <h6
                class="font-titillium text-grey fw-300 mb-0 ml-2"
                style="line-height: 1.5"
              >
                ' . $q->payment_method . '
              </h6>
            </div>
          </div>
        </div>
        <div class="col-sm-12">
          <div class="border p-2 mb-3 border-style pl-3 position-relative field-action-wrapper">
            <button type="button" class="btn field-action-btn client-read-email-btn" title="Copy Email" aria-label="Copy Email"
                data-email="' . $q->email_address . '">
              <i class="fa-light fa-copy"></i>
            </button>
            <h6 class="font-titillium content-title mb-1 fw-700">
              Primary Email Address
            </h6>
            <div class="d-flex pt-1 mb-1">
              <i class="fa-light fa-buildings text-grey fs-18"></i>
              <h6
                class="font-titillium text-grey fw-300 mb-0 ml-2"
                style="line-height: 1.5"
              >
                ' . $q->email_address . '
              </h6>
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>';

            $html .= '<div class="block new-block position-relative mt-3" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Email Notifications</h5>
            </div>
            <div class="col-sm-12">
                              <div class="border py-2 pl-2 pr-1 mb-3 border-style">
                                  <div class="d-flex align-items-center justify-content-between my-2">
                                      <h6 class="font-titillium text-grey mb-0 fw-700 pl-2">Email Addresses</h6>
                                  </div>';
            $emails = DB::table('client_emails')
                ->where('client_id', $id)
                ->pluck('email')
                ->toArray();
            if (count($emails) > 0) {
                $html .= '<div class="table-responsive pr-2 small-box small-box-no-arrow">
                                      <table class="table table-sm table-striped align-middle mb-0">
                                          <tbody>';
                foreach ($emails as $e) {
                    $html .= '<tr class="affiliate-item banner-icon" data-email-id="${email}">
                <td class="py-2 border-0 align-middle" width="20" style="border-radius: 13px 0 0 13px;">
                    
                </td>
                <td class="py-2 border-0 ">
                    <button type="button" class="btn mr-1 p-0">
                        <i class="fa-thin fa-envelope text-grey fs-18 regular-icon"></i>
                        <i class="fa-solid fa-envelope text-darkgrey fs-18 header-solid-icon"></i>
                    </button>
                    <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">' . $e . '</span>
                </td>
                <td class="py-2 border-0 text-right align-middle" width="50" style="border-radius: 0 13px 13px 0;">
                    
                </td>
            </tr>';
                }
                $html .= '</tbody>
                                      </table>
                                  </div>';
            } else {
                $html .= '<div class="font-titillium text-darkgrey fw-400 mb-0 text-center py-3">No email addresses found</div>';
            }
            $html .= '</div>
                          </div>
        </div>
    </div> 
</div> ';
            $students = DB::table('client_students')->where('client_id', $id)->get();
            $subjectsMap = DB::table('subjects')->pluck('name', 'id');
            $studentSubjects = collect();

            if ($students->count() > 0) {
                $studentSubjects = DB::table('student_subjects')
                    ->whereIn('student_subjects.student_id', $students->pluck('id'))
                    ->select('student_id', 'subject_id')
                    ->get()
                    ->groupBy('student_id');
            }
            $iconMap = [
                1 => ['icon' => 'fa-calculator', 'title' => 'Math'],
                2 => ['icon' => 'fa-book', 'title' => 'Reading'],
                3 => ['icon' => 'fa-language', 'title' => 'EFL'],
            ];
            $html .= '</div>
  <div class="tab-pane fade" id="nav-students-client" role="tabpanel" aria-labelledby="nav-students-tab-client">
  <div class="block new-block position-relative mt-3" style="margin-bottom: 1rem;">
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-11">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Students</h5>
            </div>
            <div class="col-sm-1 text-right">';
            if (count($students) > 0) {
                $html .= '<div class="download-icon"  id="downloadStudentCSV"
                    style="right: 25px; top: 10px; cursor: pointer;">
                    <i class="fa-light fa-arrow-down-to-line text-grey fs-18" style="font-size:20px;"></i>
                </div>';
            }
            $html .= '</div>
            <div class="col-sm-12">
                <div class="border p-2 mb-3 border-style pl-3">
                    <div class="student-download-toast" role="status" aria-live="polite">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fa-light fa-circle-check mr-2"></i>
                                <span class="font-titillium fs-14 text-darkgrey">Details downloaded successfully!</span>
                            </div>
                            <button type="button" data-section="student-download-toast"
                                class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                <i class="fa-light fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive student-details-container small-box-no-arrow">
                    <table class="table table-sm table-striped table-borderless student-table">
                        <thead>
                            <tr>
                                <th><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head pl-2">Student Name</h6></th>
                            <th><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head text-right pr-2">Subjects</h6></th>
                            <th><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head">Start Date</h6></th>
                            <th><h6 class="border-secondary font-titillium fw-700 mb-0 text-table-head text-right">Amount</h6></th>
                            </tr>
                        </thead>
                        <tbody class="scrollable-tbody">';
            // if(!empty($q->reference_no) && !empty($q->distrubutor_sales_order_no)) {
            if (count($students) > 0) {
                foreach ($students as $c) {
                    $icons = '';
                    $subjects = $studentSubjects[$c->id] ?? collect();
                    $subjectNames = [];
                    // 2️⃣ Generate icons + tooltip
                    foreach ($subjects as $sub) {
                        if (isset($iconMap[$sub->subject_id])) {
                            $subjectName = $subjectsMap[$sub->subject_id] ?? '';
                            $icons .= '<span data-toggle="tooltip" data-placement="top" title="' . e($subjectName) . '">
                                            <i class="fa-light ' . $iconMap[$sub->subject_id]['icon'] . ' text-grey fs-16 ml-1"></i>
                                    </span>';
                            $subjectNames[] = $subjectName;
                        }
                    }
                    $html .= '<tr>
                    <td class="font-titillium fw-300 text-darkgrey fs-15 fw-400 student-name">
                        <span class="ml-2">' . e($c->student_name) . '</span>
                    </td>
                    <td class="font-titillium fw-300 text-darkgrey fs-15 fw-400 text-right pr-2 subjects">
                        ' . $icons
                        . '<span class="subject-text" style="display:none">' . e(implode(", ", $subjectNames)) . '</span>'
                        . '</td>
       </td>
                  <td class="font-titillium fw-300 text-darkgrey fs-15 fw-400 start-date">
                        ' . date('d-M-Y', strtotime($c->start_date)) . '
                  </td>
                  <td class="font-titillium fw-300 text-darkgrey fs-15 text-right amount">
                        ' . number_format($c->amount, 2, '.', ',') . '
                  </td>
                 </tr>';
                }
            }

            $html .= '</tbody>
          </table>
        </div>';
            if (count($students) == 0) {
                $html .= '<div class="font-titillium text-darkgrey fw-400 mb-0 text-center py-2">No details found</div>';
            }
            $html .= '</div>
            </div>
        </div>
    </div> 
</div> ';
            $client_payments = DB::table('client_payments')->where('client_id', $id)->get();
            //  $student_vacations = DB::table('student_vacations')->where('client_id', $id)->get();
            $student_vacations = DB::table('student_vacations as sv')
                ->where('sv.client_id', $id)
                ->leftJoin('subjects as s', function ($join) {
                    $join->whereRaw(
                        'JSON_VALID(sv.subjects)
             AND sv.subjects LIKE CONCAT(\'%"\', s.id, \'\"%\')'
                    );
                })
                ->select(
                    'sv.*',
                    DB::raw('GROUP_CONCAT(s.name SEPARATOR ", ") as subject_names')
                )
                ->groupBy('sv.id')
                ->get();




            $html .= '</div>
  <div class="tab-pane fade" id="nav-payments-client" role="tabpanel" aria-labelledby="nav-payments-tab-client">


<div class="block new-block position-relative mt-3" style="margin-bottom: 1rem;">
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-11">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Payments</h5>
            </div>
            <div class="col-sm-1 text-right">';
            if (count($client_payments) > 0) {
                $html .= '<div class="download-icon"  id="downloadPaymentCSV"
                    style="right: 25px; top: 10px; cursor: pointer;">
                    <i class="fa-light fa-arrow-down-to-line text-grey fs-18" style="font-size:20px;"></i>
                </div>';
            }
            $html .= '</div>
            <div class="col-sm-12">
                <div class="border p-2 mb-3 border-style pl-3">
                    <div class="payment-download-toast" role="status" aria-live="polite">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fa-light fa-circle-check mr-2"></i>
                                <span class="font-titillium fs-14 text-darkgrey">Details downloaded successfully!</span>
                            </div>
                            <button type="button" data-section="payment-download-toast"
                                class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                <i class="fa-light fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive payment-details-container small-box-no-arrow">
                    <table class="table table-sm table-striped table-borderless payment-table">
                        <thead>
                            <tr>
                                <th class="py-2 border-0 pl-2" width="25%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">
                                                      Month</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-2" width="20%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary text-center">
                                                      Payment Type</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-2" width="20%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">
                                                      Reference No.</h6>
                                              </th>
                                              <th class="py-2 border-0" width="20%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary text-right pr-2">
                                                      Amount</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-2" width="30%">
                                                  <h6 class="font-titillium text-table-head mb-0 fw-700">Date</h6>
                                              </th>
                            </tr>
                        </thead>
                        <tbody class="scrollable-tbody">';
            // if(!empty($q->reference_no) && !empty($q->distrubutor_sales_order_no)) {
            if (count($client_payments) > 0) {
                foreach ($client_payments as $c) {
                    $html .= '<tr><td class="py-2 border-0 pl-2 month">
                <span class="fw-300 text-darkgrey fs-15">
                    ' . $c->kumon_month . '
                </span>
            </td>
                <td class="py-2 border-0 text-center pr-2 payment-type" width="10%">
                    <span class="fw-300 text-darkgrey fs-15">
                        ' . $c->payment_type . '
                </span>
            </td>
            <td class="py-2 border-0 pl-2 reference-no">
                <span class="fw-300 text-darkgrey fs-15">
                    ' . (!empty($c->reference_no) ? $c->reference_no : '-') . '
                </span>
            </td>
            <td class="py-2 border-0 pl-2 text-right pr-2 amount">
                <span class="fw-300 text-darkgrey fs-15 pr-1">
                    $ ' . number_format($c->amount, 2, '.', ',') . '
                </span>
            </td>
            <td class="py-2 border-0 pl-2 payment-date">
                <span class="fw-300 text-darkgrey fs-15">
                    ' . date('d-M-Y', strtotime($c->payment_date)) . '
                </span>
            </td></tr>';
                }
            }
            $html .= '</tbody>
                    </table></div>';
            if (count($client_payments) == 0) {
                $html .= '<div class="font-titillium text-darkgrey fw-400 mb-0 text-center py-2">No details found</div>';
            }
            $html .= '</div>
            </div>
        </div>
    </div> 
</div>
  </div>
  <div class="tab-pane fade" id="nav-vacation-client" role="tabpanel" aria-labelledby="nav-vacation-tab-client">
  <div class="block new-block position-relative mt-3" style="margin-bottom: 1rem;">
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-11">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Vacations</h5>
            </div>
            <div class="col-sm-1 text-right">';
            if (count($student_vacations) > 0) {
                $html .= '<div class="download-icon"  id="downloadVacationCSV"
                    style="right: 25px; top: 10px; cursor: pointer;">
                    <i class="fa-light fa-arrow-down-to-line text-grey fs-18" style="font-size:20px;"></i>
                </div>';
            }
            $html .= '</div>
            <div class="col-sm-12">
                <div class="border p-2 mb-3 border-style pl-3">
                    <div class="vacation-download-toast" role="status" aria-live="polite">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fa-light fa-circle-check mr-2"></i>
                                <span class="font-titillium fs-14 text-darkgrey">Details downloaded successfully!</span>
                            </div>
                            <button type="button" data-section="vacation-download-toast"
                                class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                <i class="fa-light fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive vacation-details-container small-box-no-arrow">
                    <table class="table table-sm table-striped table-borderless vacation-table">
                        <thead>
                            <th class="py-2 border-0 pl-2" width="3%"></th>
                            <th class="py-2 border-0 pl-2" width="20%">
                                <h6
                                    class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">
                                    Student</h6>
                            </th>
                            <th class="py-2 border-0 pl-2" width="25%">
                                <h6
                                    class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">
                                    Subject</h6>
                            </th>
                            <th class="py-2 border-0 pl-2" width="30%">
                                <h6 class="font-titillium text-table-head mb-0 fw-700">Date Range
                                </h6>
                            </th>
                            <th class="py-2 border-0 pl-2" width="3%"></th>
                        </thead>
                        <tbody class="scrollable-tbody">';
            // if(!empty($q->reference_no) && !empty($q->distrubutor_sales_order_no)) {
            if (count($student_vacations) > 0) {
                foreach ($student_vacations as $c) {

                    // Status icon logic
                    $iconHtml = '<i class="fa-light fa-circle-xmark text-red fs-20"></i>';
                    $isActive = false;
                    $isUpcoming = false;

                    if (!empty($c->date_range)) {
                        $parts = explode(' to ', $c->date_range);

                        if (count($parts) === 2) {
                            try {
                                $start = Carbon::parse($parts[0])->startOfDay();
                                $end   = Carbon::parse($parts[1])->endOfDay();
                                $today = Carbon::today();

                                if ($today->between($start, $end)) {
                                    $iconHtml = '<i class="fa-light fa-circle-check text-green fs-20"></i>';
                                    $isActive = true;
                                } elseif ($start->greaterThan($today)) {
                                    $isUpcoming = true;
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    }
                    $tooltip = "";
                    if ($c->comment) {
                        $tooltip = '<button type="button" data-comment="' . $c->comment . '" class="btn vacation-info ml-auto p-0 cursor-help">
                <i class="fa-light fa-circle-info text-grey fs-18"></i></button>';
                    }

                    // Build HTML row
                    $takeWorkText = $c->take_work_home ? 'True' : 'False';
                    $reducedText = $c->reduced_workload ? 'True' : 'False';
                    $plannedText = $c->planned ? 'True' : 'False';
                    $reducedIcon = '';
                if($c->reduced_workload){
                    $reducedIcon = '<span data-toggle="tooltip" data-placement="left" title="Reduced Workload">
                                    <i class="fa-duotone fa-solid fa-arrow-down-to-line"></i>
                            </span>';
                }
                $takeWorkIcon = '';
                if($c->take_work_home){
                    $takeWorkIcon = '<span data-toggle="tooltip" data-placement="right" title="Take Work Home">
                                    <i class="fa-duotone fa-solid fa-books"></i>
                            </span>';
                }else{
                    $takeWorkIcon = '<span data-toggle="tooltip" data-placement="right" title="No Take Work Home">
                                    <i class="fa-duotone fa-solid fa-island-tropical"></i>
                            </span>';
                }
                $plannedIcon = '';
                if($c->planned){
                    $plannedIcon = '<span data-toggle="tooltip" data-placement="right" title="Planned">
                                    <i class="fa-duotone fa-solid fa-sparkles"></i>
                            </span>';
                }
                $statusIcon = $isActive ? '<i class="fa-light fa-circle-check text-green fs-20" data-toggle="tooltip" data-placement="top" title="Active"></i>' : ($isUpcoming ? '<i class="fa-light fa-triangle-exclamation text-warning fs-16" data-toggle="tooltip" data-placement="top" title="Upcoming"></i>' : '<i class="fa-light fa-circle-xmark text-red fs-20" data-toggle="tooltip" data-placement="top" title="Inactive"></i>');
                $statusClass = $isActive ? 'active' : ($isUpcoming ? 'upcoming' : 'inactive');

                    $html .= '
        <tr class="' . $statusClass . '">
        <input type="hidden" class="comment" value="' . e($c->comment) . '">
        <input type="hidden" class="takework" value="' . ($c->take_work_home ? 'Yes' : 'No') . '">

            <td class="py-2 border-0 text-center" width="3%">' . $statusIcon . '</td>

            <td width="20%" class="py-2 border-0 pl-2 student_name">
                <span class="fw-300 text-darkgrey fs-15">' . $c->student_name . '</span>
            </td>

            <td width="25%" class="py-2 border-0 pl-2">
                <div class="d-flex justify-content-between w-100 mb-1">
                    <div class="left-content">
                        ' . $tooltip . '<span class="fw-300 text-darkgrey fs-15">' . $c->subject_names . '</span>
                    </div>
                    <div class="right-content">
                        <span class="d-flex gap-3" style="margin-right: 10px;"><div class="d-flex justify-content-between">' . $reducedIcon.$takeWorkIcon . '</div></span>
                    </div>
                </div>
            </td>

            <td width="30%" class="py-2 border-0 pl-2">
                <span class="fw-300 text-darkgrey fs-15 date-range">' . $c->date_range . '</span>
            </td>
            <td class="py-2 border-0 text-center" width="3%">'.$plannedIcon.'</td>
        </tr>';
                }
            }
            $html .= '</tbody>
                    </table></div>';
            if (count($student_vacations) == 0) {
                $html .= '<div class="font-titillium text-darkgrey fw-400 mb-0 text-center py-2">No details found</div>';
            }
            $html .= '</div>
            </div>
        </div>
        <div class=" text-right">
        <div class="d-flex d-flex justify-content-end">
                                          <div class="radio_button ">
                                              <input type="radio" id="active"
                                                  name="vacationToogle" value="active" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="active">Active</label>
                                          </div>
                                          <div class="radio_button">
                                              <input type="radio" id="all" name="vacationToogle"
                                                  value="all" checked/>
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="all">All</label>
                                          </div>
                                      </div>
        </div>
    </div> 
</div>
  </div>
  <div class="tab-pane fade" id="nav-comments-client" role="tabpanel" aria-labelledby="nav-comments-tab-client">


<div class="block new-block position-relative mt-3" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Comments</h5>
            </div>
';
            $client = DB::table('client_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.is_deleted', 0)->where('v.client_id', $q->id)->get();

            if (sizeof($client) > 0) {
                $html .= '<div class="col-sm-12"><button type="button" data-toggle="modal" data-target="#CommentModal" class="btn font-titillium fw-500 py-1 px-3 ml-3 new-ok-btn mb-3" style="width: fit-content;">Add Comment</button></div>';
                foreach ($client as $c) {
                    $html .= '                          
            <div class="col-sm-12">
                <div class="border p-2 mb-3 border-style border-style border-hover-comment">
                    <table class="table table-borderless table-vcenter mb-0">
                        <tbody>
                            <tr>
                                <td class="text-center pr-0 pl-2" style="width: 38px;">
                                    <h1 class="mb-0 mr-1 text-white rounded">
';
                    if ($c->user_image == '') {
                        $html .= '
<i class="fa-solid fa-circle-user text-darkgrey"></i>
';
                    } else {
                        $html .= '
<img width="40px" class="bg-dark mr-2 ml-1" height="40" style="border-radius: 50%;" src="' . ('public/client_logos/' . $c->user_image) . '">
';
                    }
                    $html .= '
                                    </h1>
                                </td>
                                <td class="js-task-content  pl-0">
<h6 class="font-titillium text-grey mb-1 fw-700">' . $c->name . '</h6>
<h6 class="font-titillium text-grey mb-0 fw-300 fs-14">On ' . date('d-M-Y', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT</h6>
                                </td>
                                <td class="align-content-start">
<div class="d-flex justify-content-end">
<a class="float-right edit-comment-client mr-2" data-id="' . $c->id . '" data-client-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
<i class="fa-thin fa-pen text-darkgrey fs-18"></i>
</a> 
<a class="float-right delete-comment-client" data-id="' . $c->id . '" data-client-id="' . $id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
<i class="fa-thin fa-circle-xmark text-darkgrey fs-18"></i>
</a>                                    
</div>                                    
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="pt-0">
<h6 class="font-titillium text-darkgrey mb-1 fw-500">' . nl2br($c->comment) . '</h6>
                                </td>
                            </tr>
                        </tbody>
                    </table>   
                </div>
            </div>
';
                }
            } else {
                $html .= '<div class="col-sm-12">
            <div class="font-titillium text-darkgrey mb-0 clientDetailsBody-empty pb-2 pt-0">No comments. Add a comment by using the Add Comment button.</div>
            </div>
            <div class="col-sm-12">
            <button type="button" data-toggle="modal" data-target="#CommentModal" class="btn font-titillium fw-500 py-1 px-3 new-ok-btn d-flex" style="width: fit-content;">Add Comment</button>
</div>';
            }
            $html .= '                                                   
        </div>
    </div> 
</div> 


  </div>
  <div class="tab-pane fade" id="nav-attachments-client" role="tabpanel" aria-labelledby="nav-attachments-tab-client">


<div class="block new-block position-relative mt-3" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Attachments</h5>
            </div>
';

            $client = DB::table('client_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.is_deleted', 0)->where('v.client_id', $q->id)->get();

            if (sizeof($client) > 0) {
                $html .= '<div class="col-sm-12"><button type="button" data-toggle="modal" data-target="#AttachmentModal" class="btn font-titillium fw-500 py-1 px-3 ml-3 new-ok-btn mb-3" style="width: fit-content;">Add Attachemnt</button></div>';
                foreach ($client as $c) {

                    $f = explode('.', $c->attachment);

                    $fileExtension = end($f);

                    $icon = 'attachment.png';

                    if ($fileExtension == 'pdf') {

                        $icon = 'attch-Icon-pdf.png';
                    } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {

                        $icon = 'attch-word.png';
                    } else if ($fileExtension == 'txt') {

                        $icon = 'attch-word.png';
                    } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {

                        $icon = 'attch-excel.png';
                    } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {

                        $icon = 'attch-png icon.png';
                    } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {

                        $icon = 'attch-jpg-icon.png';
                    } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {

                        $icon = 'attch-powerpoint.png';
                    }

                    $html .= '                          
            <div class="col-sm-6">
                <div class="border py-2 mb-3 border-style border-style-hover border-hover-comment">
                    <table class="table table-borderless table-vcenter mb-0">
                        <tbody>
                            <tr>
                                <td class="text-center pr-0 pl-2" style="width: 38px;">
                                    <h1 class="mb-0 mr-1 text-white rounded">
';
                    if ($c->user_image == '') {
                        $html .= '
<i class="fa-solid fa-circle-user text-darkgrey"></i>
';
                    } else {
                        $html .= '
<img width="40px" class="bg-dark mr-2 ml-1" height="40" style="border-radius: 50%;" src="' . ('public/client_logos/' . $c->user_image) . '">
';
                    }
                    $html .= '
                                    </h1>
                                </td>
                                <td class="js-task-content px-0">
                                    <h6 class="font-titillium text-grey mb-1 fw-700">' . $c->name . '</h6>
                                    <h6 class="font-titillium text-grey mb-0 fw-300 fs-14">On ' . date('d-M-Y', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT</h6>
                                </td>
                                <td class="align-content-start">
                                    <a class="float-right delete-attachment" data-id="' . $c->id . '" data-client-id="' . $id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                        <i class="fa-solid fa-circle-xmark text-darkgrey fs-25 attachment-cross"></i>
                                    </a>                                    
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-0">
                                    <a href="public/network_attachment/' . nl2br($c->attachment) . '" download="" target="_blank" class="">
                                        <h6 class="font-titillium text-darkgrey mb-1 fw-500 d-flex align-items-center attachment-name">
                                            <img src="public/img/' . $icon . '" width="25px" class="mr-2">' . substr($c->attachment, 0, 25) . '
                                        </h6>
                                    </a>
                                </td>
                                                                                  
                            </tr>
                        </tbody>
                    </table>   
                </div>
            </div>
';
                }
            } else {
                $html .= '<div class="col-sm-12">
            <div class="font-titillium text-darkgrey mb-0 clientDetailsBody-empty pb-2 pt-0">No attachments. Add an Attachment by using the Add Attachment button.</div>
            </div>
            <div class="col-sm-12">
            <button type="button" data-toggle="modal" data-target="#AttachmentModal" class="btn font-titillium fw-500 py-1 px-3 new-ok-btn d-flex" style="width: fit-content;">Add Attachment</button>
</div>';
            }
            $html .= '                                                   
        </div>
    </div> 
</div> 


  </div>
  <div class="tab-pane fade" id="nav-audit-client" role="tabpanel" aria-labelledby="nav-audit-tab-client">


  
<div class="block new-block position-relative mt-3" style="max-height: 600px; overflow-y: auto;">
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-11">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Audit Trail</h5>
            </div>
            <div class="col-sm-1 text-right"><div class="download-icon" id="downloadAuditLogCSV" style="right: 25px; top: 10px; cursor: pointer;">
                    <i class="fa-light fa-arrow-down-to-line text-grey fs-18" style="font-size:20px;"></i>
                </div></div>
';

            $client_audit = DB::table('client_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.client_id', $q->id)->get();

            if (sizeof($client_audit) > 0) {
                foreach ($client_audit as $c) {
                    $html .= '                          
            <div class="col-sm-12 audit-log-item">
                <div class="border p-2 mb-3 border-style">
                    <table class="table table-borderless table-vcenter mb-0">
                        <tbody>
                            <tr>
                                <td class="text-center pr-0 pl-2" style="width: 38px;">
                                    <h1 class="mb-0 mr-1 text-white rounded">
';
                    if ($c->user_image == '') {
                        $html .= '
                                            <i class="fa-solid fa-circle-user text-darkgrey"></i>
                                            ';
                    } else {
                        $html .= '
                                        <img width="40px" class="bg-dark mr-2 ml-1" height="40" style="border-radius: 50%;" src="' . ('public/client_logos/' . $c->user_image) . '">
                                        ';
                    }
                    $html .= '
                                    </h1>
                                </td>
                                <td class="js-task-content  pl-0">
                                    <h6 class="font-titillium text-grey mb-1 fw-700">' . $c->firstname . ' ' . $c->lastname . '</h6>
                                    <input type="hidden" value="' . ucfirst($c->firstname) . ' ' . ucfirst($c->lastname) . '" class="user_name">
                                    <h6 class="font-titillium text-grey mb-0 fw-300 fs-14">On ' . date('d-M-Y', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT</h6>
                                    <input type="hidden" value="' . date('d-M-Y', strtotime($c->created_at)) . ' ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT" id="date_time">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="py-0">
                                    <h6 class="font-titillium text-darkgrey mb-1 fw-500 adit-message">' .     $c->description . '</h6>
                                </td>
                            </tr>
                        </tbody>
                    </table>   
                </div>
            </div>
';
                }
            }
            $html .= '                                                   
        </div>
    </div> 
</div> 


  </div>
</div>                        


';
            $html .= '

                    </div>
                </div>

               </div>

       </div>
       <div class="modal fade" id="EmailModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header font-titillium fw-700 fs-20 text-header-blue">Email Notification</span>
                                    <div class="block-options">
                                    </div>
                                </div>
                                <div class="modal-body py-0">
                                    <input type="hidden" id="id" name="id">
                                    <div class="row mb-3">
                                        <div class="col-md-1 text-center">
                                            <i class="fa-light fa-triangle-exclamation text-warning fs-30"></i>
                                        </div>
                                        <div class="col-md-11">
                                            <p class="text-center font-titillium text-darkgrey mb-0" style="font-size: 14pt;">
                                                Select recipients to send an e-mail notification for this form upcoming payments or vacations.
                                            </p>
                                            <div class="d-flex justify-content-center mt-3">
                                                <div class="radio_button">
                                                    <input type="radio" id="email_notification_type_payment"
                                                        name="email_notification_type" value="payment" checked>
                                                    <label
                                                        class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                        for="email_notification_type_payment">Payments</label>
                                                </div>
                                                <div class="radio_button">
                                                    <input type="radio" id="email_notification_type_vacation"
                                                        name="email_notification_type" value="vacation">
                                                    <label
                                                        class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                        for="email_notification_type_vacation">Vacations</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                    <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700">Email Address</h6>
                              <div class="d-flex pt-1">
                                  <i class="fa-light fa-envelope text-grey fs-18 constant-icon"></i>
                                  <input type="email"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                      id="email_notify" placeholder="Enter Email Address" value="">
                                      <a class="banner-icon add-new-btn add-email-notify" data-id="' . $id . '" href="javascript:;">
                                              <i class="fa-light fa-square-plus text-grey regular-icon fs-23"></i>
                                              <i class="fa-solid fa-square-plus text-primary header-solid-icon fs-23"
                                                  style="padding-left: 4px; padding-right: 4.5px;"></i>
                                          </a>
                              </div>
                          </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border py-2 pl-2 pr-1 mb-3 border-style">
                                  <div class="d-flex align-items-center justify-content-between mb-3">
                                      <h6 class="font-titillium text-grey mb-0 fw-700 pl-2">Email Address</h6>
                                  </div>
                                  <div class="table-responsive pr-2 small-box small-box-no-arrow" style="overflow-y: auto; max-height: 195px;">
                                      <table class="table table-sm table-striped align-middle mb-0 added-notify-email">
                                          <tbody>';
            $notify_emails = DB::table('client_emails')
                ->where('client_id', $id)
                ->pluck('email')
                ->toArray();
            if (count($notify_emails) > 0) {
                foreach ($notify_emails as $e) {
                    $html .= '<tr class="email-notify-item banner-icon" data-contract-id="' . $id . '">
                                                <td class="py-2 border-0 " style="border-radius: 13px 0 0 13px;">
                                                    <span class="fw-300 text-darkgrey font-titillium fs-15 email-notify">' . $e . '</span>
                                                </td>
                                                <td class="py-2 border-0 text-right align-middle" width="50" style="border-radius: 0 13px 13px 0;">
                                                    
                                                    <a href="javascript:;" class="align-items-center d-flex drag-handle justify-content-center mb-0 remove-notification" data-contract-id="' . $id . '" data-email="' . $e . '">
                                                            <i class="fa-light fa-circle-xmark mr-0 text-grey fs-18"></i>
                                                        </a>
                                                </td>
                                            </tr>';
                }
            } else {
                $html .= '<tr class="no-notify-email-row" style="border-radius: 13px;">
                                                                                    <td colspan="2" class="font-titillium text-darkgrey fw-400 fs-14 text-center">No email addresses added</td></tr>';
            }
            $html .= '</tbody>
                                      </table>
                                  </div>
                                  <div class="notify-email-toast-added" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line added
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="notify-email-toast-added"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="notify-email-toast-updated" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line updated
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="notify-email-toast-updated"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="notify-email-toast-recovered" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line recovered
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="notify-email-toast-recovered"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="notify-email-toast-deleted" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line deleted</span>
                                          </div>
                                          <div class="d-flex align-items-center">
                                              <button type="button"
                                                  class="btn text-darkgrey btn-undo undo-delete-notify-email font-titillium fs-14 mr-2"
                                                  data-action="undo">
                                                  Undo
                                              </button>
                                              <button type="button" data-section="notify-email-toast-deleted"
                                                  class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                                  <i class="fa-light fa-xmark"></i>
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                                    <hr>
                                </div>
                                <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">';
            if (count($notify_emails) == 0) {
                $html .= '<button type="button" class="btn ok-btn cancel-btn btn-notify-email" data-client-id="' . $id . '" disabled>Notify</button>';
            } else {
                $html .= '<button type="button" class="btn ok-btn btn-primary btn-notify-email" data-client-id="' . $id . '">Notify</button>';
            }
            $html .= '
                                    <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              <div class="modal fade" id="pinMessageModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header font-titillium fw-700 fs-20 text-header-blue">Pinned Messages</span>
                                    <div class="block-options">
                                    </div>
                                </div>
                                <div class="modal-body py-0">
                                    <input type="hidden" id="id" name="id">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-3"></i>
                                        <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Enter a pinned messages that you want to all users to see for this document.</span>
                                    </div>
                                    <div class="col-sm-12">
                                    <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700">Message</h6>
                              <div class="d-flex pt-1">
                                  <i class="fa-light fa-message-exclamation text-grey fs-18 constant-icon"></i>
                                  <input type="email"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                      id="pin_message" placeholder="Enter pinned message" value="">
                                      <a class="banner-icon add-new-btn add-pinned-message" data-id="' . $id . '" href="javascript:;">
                                              <i class="fa-light fa-square-plus text-grey regular-icon fs-23"></i>
                                              <i class="fa-solid fa-square-plus text-primary header-solid-icon fs-23"
                                                  style="padding-left: 4px; padding-right: 4.5px;"></i>
                                          </a>
                              </div>
                          </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border py-2 pl-2 pr-1 mb-3 border-style">
                                  <div class="d-flex align-items-center justify-content-between mb-3">
                                      <h6 class="font-titillium text-grey mb-0 fw-700 pl-2">Pinned Message</h6>
                                  </div>
                                  <div class="table-responsive pr-2 small-box small-box-no-arrow" style="overflow-y: auto; max-height: 195px;">
                                      <table class="table table-sm table-striped align-middle mb-0 added-pinned-message">
                                          <tbody>';

            if (count($regular_messages) > 0) {
                foreach ($regular_messages as $m) {
                    $html .= '<tr class="pinned-message-item banner-icon" data-client-id="' . $id . '">
                                                <td class="py-2 border-0 " style="border-radius: 13px 0 0 13px;">
                                                    <span class="fw-300 text-darkgrey font-titillium fs-15 pinned-message">' . $m->message . '</span>
                                                </td>
                                                <td class="py-2 border-0 text-right align-middle" width="50" style="border-radius: 0 13px 13px 0;">
                                                    
                                                    <a href="javascript:;" class="align-items-center d-flex drag-handle justify-content-center mb-0 remove-pinned-message" data-client-id="' . $id . '" data-message="' . $m->message . '">
                                                            <i class="fa-light fa-circle-xmark mr-0 text-grey fs-18"></i>
                                                        </a>
                                                </td>
                                            </tr>';
                }
            }
            $html .= '</tbody>
                                      </table>';
            if (count($regular_messages) == 0) {
                $html .= '<h6 class="font-titillium text-darkgrey mb-0 text-center no-pinned-message-row py-2">No pinned message</h6>';
            }
            $html .= '</div>
                                  <div class="pinned-message-toast-added" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line added
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="pinned-message-toast-added"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="pinned-message-toast-updated" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line updated
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="pinned-message-toast-updated"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="pinned-message-toast-recovered" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line recovered
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="pinned-message-toast-recovered"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="pinned-message-toast-deleted" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line deleted</span>
                                          </div>
                                          <div class="d-flex align-items-center">
                                              <button type="button"
                                                  class="btn text-darkgrey btn-undo undo-delete-pinned-message font-titillium fs-14 mr-2"
                                                  data-action="undo">
                                                  Undo
                                              </button>
                                              <button type="button" data-section="pinned-message-toast-deleted"
                                                  class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                                  <i class="fa-light fa-xmark"></i>
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                                    <hr>
                                </div>
                                <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                                <button type="button" class="btn ok-btn btn-primary btn-add-pin-messages" data-client-id="' . $id . '">Ok</button>
                                    <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                ';
            $iconHtml = '';
            if (@Auth::user()->role != 'read') {



                if ($q->is_active) {

                        
                    $iconHtml .= '<span> 
                                     <a href="javascript:;" class="btnEnd text-white banner-icon ml-0" data-status="' . $q->is_active . '"  data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="End Client" class=" ">
                                     <i class="fa-light fa-circle-chevron-down regular-icon"></i>
                                     <i class="fa-solid fa-circle-chevron-down solid-icon" style="padding-right: 3px; padding-left: 1.5px;"></i>
                                 </span>';
                } else {
$iconHtml .= '<a class="text-white banner-icon btnEnd mr-0" href="javascript:;" data-ended="1" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reinstate Client" data="' . $id . '" data-id="' . $id . '">
                        <i class="fa-light fa-arrow-up-to-arc regular-icon"></i>
                        <i class="fa-solid fa-arrow-up-to-arc solid-icon" style="padding-right: 3px; padding-left: 1.5px;"></i></a>';
                }
            }

            $editUrl = url('edit-client') . '?id=' . $q->id;
            $cloneUrl = url("add-client/support") . '?id=' . $q->id;
            $pdfUrl = url('pdf-client') . '?id=' . $q->id;

            // $cards = ``;

            $cards = '<div class="container px-0 mt-0" style="overflow: hidden;">
    <div class="d-flex">
    <div class="cards-container">
        <div class="mr-3 status-card">
';
            if ($q->is_active) {
                $createdTimestamp = strtotime($q->created_at);
                $currentTimestamp = time();
                $activeDays = floor(($currentTimestamp - $createdTimestamp) / (60 * 60 * 24));
                $cards .= '        
            <div class="rounded-10px  pl-2 pr-4 pt-2 bg-white h-100" style="width: fit-content; border: 1px solid #4EA833;">
                <div class="d-flex"><i class="fa-light fa-circle-check text-green fs-16 mr-2" style="margin-top: 7px;"></i><h6 class="font-titillium fw-800 text-green fs-18 mb-0">Active</h6></div>
                <!--<div class="d-flex align-items-center">
                    <i class="fa-light fa-circle-check text-green fs-16 mr-2"></i>
                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0">' . $activeDays . ' days</p>
                </div> -->
            </div>
';
            } else {
                $cards .= '        
            <div class="rounded-10px  pl-2 pr-4 pt-2 bg-white h-100" style="width: fit-content; border: 1px solid #C41E3A;">
                <h6 class="font-titillium fw-800 text-red fs-18 mb-0">Ended</h6>
                <div class="d-flex align-items-center">
                    <i class="fa-light fa-octagon-exclamation text-red fs-16 mr-2"></i>
                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0">' . date('d-M-Y', strtotime($q->ended_on)) . '</p>
                </div>
            </div>
';
            }
            $cards .= '        
        </div>        
        </div>
    </div>
</div>
';
            $today = Carbon::today();

            $activeVacationPins = [];
            $priceAlerts = DB::table('student_price_changes')
                ->where('client_id', $q->id)
                ->where('confirmed', 0)
                ->get();

            $vacations = DB::table('student_vacations')
                ->whereNotNull('date_range')
                ->where('client_id', $q->id)
                ->get();

            foreach ($vacations as $v) {

                $parts = explode(' to ', $v->date_range);

                if (count($parts) !== 2) {
                    continue;
                }

                try {
                    $start = Carbon::parse(trim($parts[0]))->startOfDay();
                    $end   = Carbon::parse(trim($parts[1]))->endOfDay();

                    if ($today->between($start, $end)) {
                        $activeVacationPins[] = [
                            'student_name' => $v->student_name,
                            'requires_planning' => (bool) $v->take_work_home
                        ];
                    }
                } catch (\Exception $e) {
                    // silently skip invalid date ranges
                }
            }

            if (count($message) > 0 || count($activeVacationPins) > 0 || count($priceAlerts) > 0) {
                $cards .= '<div class="container" style="padding-right: 10px;">';

                // // Upcoming (≤ 50 days remaining)
                // if ($daysLeft_ >= 0 && $daysLeft_ <= 30 && $q->contract_status == 'Active') {
                //     $cards .= '<div class="align-items-center container d-flex mb-0 px-2 py-1 mt-2 message-banner-upcoming">
                //         <i class="fa-solid fa-circle-exclamation text-black fs-16 mr-3"></i>
                //         <p class="font-titillium fs-16 fw-500 mb-0 text-black">
                //         Contract will expire in ' . $daysLeft_ . ' days. Please Renew before end date to avoid service interruptions.
                //         </p>
                //       </div>';
                // }

                // // Expired
                // if ($daysLeft_ < 0 && $q->contract_status === 'Expired/Ended') {
                //     $cards .= '<div class="align-items-center container d-flex mb-0 px-2 py-1 mt-2 message-banner-expired">
                //         <i class="fa-solid fa-diamond-exclamation text-black fs-16 mr-3"></i>
                //         <p class="font-titillium fs-16 fw-500 mb-0 text-black">
                //         Contract Expired on ' . $endDate_->format('d-M-Y') . '. Please Renew or End this contract to finalize its status.
                //         </p>
                //       </div>';
                // }
                if (Auth::user()->role == 'admin') {
                    foreach ($priceAlerts as $alert) {
                        $cards .= '
                            <div class="align-items-center justify-content-between container d-flex mb-0 px-2 py-1 mt-2 message-banner">
                            <div class="d-flex align-items-center">
                            <i class="fa-solid fa-circle-exclamation fs-16 mr-3"></i>
                            <p class="font-titillium fs-16 fw-500 mb-0">
                                Price changed for <b>' . $alert->student_name . '</b>. Please acknowledge this alert once it’s been treated.
                            </p>
                            </div>
                                <a href="' . url('/confirm-price-change') . '/ ' . $alert->id . '" class="confirm-price-change text-darkgrey"
                                        data-id="' . $alert->id . '">Confirm</a>
                            </div>';
                    }
                }
                foreach ($activeVacationPins as $vac) {

                    if ($vac['requires_planning']) {
                        // 🟡 Planning REQUIRED
                        $cards .= '
            <div class="align-items-center container d-flex mb-0 px-2 py-1 mt-2 message-banner-planning-required">
                <i class="fa-solid fa-message-exclamation fs-16 mr-3"></i>
                <p class="font-titillium fs-16 fw-500 mb-0">
                    ' . $vac['student_name'] . ' requires planning for upcoming vacation
                </p>
            </div>';
                    } else {
                        // 🔵 Planning NOT required
                        $cards .= '
            <div class="align-items-center container d-flex mb-0 px-2 py-1 mt-2 message-banner-planning-not-required">
                <i class="fa-solid fa-message-exclamation fs-16 mr-3"></i>
                <p class="font-titillium fs-16 fw-500 mb-0">
                    No planning required for ' . $vac['student_name'] . ' upcoming vacation
                </p>
            </div>';
                    }
                }
                foreach ($message as $msg) {
                    $pinnedBy = trim(($msg->firstname ?? '') . ' ' . ($msg->lastname ?? ''));
                    $pinnedDate = !empty($msg->created_at) ? Carbon::parse($msg->created_at)->format('d-M-Y') : '';
                    $pinnedMeta = trim($pinnedBy . (strlen($pinnedBy) && strlen($pinnedDate) ? ', ' : '') . $pinnedDate);

                    if ($msg->status == "renewed") {
                        $cards .= '<div class="align-items-center container d-flex justify-content-between mb-0 px-2 py-1 mt-2 text-white message-banner-renew">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-info text-white fs-16 mr-3"></i>
                <p class="font-titillium fs-16 fw-500 mb-0 text-white">' . $msg->message . '</p>
            </div>
            <span class="font-titillium fw-300 text-white" style="font-size: 10pt; opacity: 0.7;">' . $pinnedMeta . '</span>
        </div>';
                    } elseif ($msg->status == "ended") {
                        $cards .= '<div class="align-items-center container d-flex justify-content-between mb-0 px-2 py-1 mt-2 text-white message-banner-ended">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-octagon-exclamation text-white fs-16 mr-3"></i>
                <p class="font-titillium fs-16 fw-500 mb-0 text-white">' . $msg->message . '</p>
            </div>
            <span class="font-titillium fw-300 text-white" style="font-size: 10pt; opacity: 0.7;">' . $pinnedMeta . '</span>
        </div>';
                    } else {
                        $cards .= '<div class="align-items-center container d-flex justify-content-between mb-0 px-2 py-1 mt-2 text-darkgrey message-banner">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-message-exclamation fs-16 mr-3"></i>
                <p class="font-titillium fs-16 fw-500 mb-0 text-darkgrey">' . $msg->message . '</p>
            </div>
            <span class="font-titillium fw-300 text-darkgrey" style="font-size: 12pt;">' . $pinnedMeta . '</span>
        </div>';
                    }
                }
                $cards .= '</div>';
            }
            // $rows = DB::table('client_students as cs')
            //     ->join('student_subjects as ss', 'ss.student_id', '=', 'cs.id')
            //     ->join('subjects as s', 's.id', '=', 'ss.subject_id')
            //     ->where('cs.client_id', $id)
            //     ->select(
            //         'cs.student_name',
            //         DB::raw('GROUP_CONCAT(CONCAT("[", s.name, "]") SEPARATOR " ") as subjects')
            //     )
            //     ->groupBy('cs.id')
            //     ->get();

            // $list = [];

            // foreach ($rows as $row) {
            //     $list[] = $row->student_name . ' [' . $row->subjects . ']';
            // }

            // $result = implode(', ', $list);
            $rows = DB::table('client_students as cs')
                ->join('student_subjects as ss', 'ss.student_id', '=', 'cs.id')
                ->join('subjects as s', 's.id', '=', 'ss.subject_id')
                ->where('cs.client_id', $id)
                ->select(
                    'cs.student_name',
                    DB::raw('GROUP_CONCAT(CONCAT("[", s.name, "]") SEPARATOR " ") as subjects')
                )
                ->groupBy('cs.id', 'cs.student_name')
                ->get();

            $list = [];

            foreach ($rows as $row) {
                // ✅ Do NOT wrap again — subjects already contain [ ]
                $list[] = $row->student_name . ' ' . $row->subjects;
            }

            $result = implode(', ', $list);


            return response()->json([
                'cards' => $cards,
                'content' => $html,
                // 'header_img' => $headerImg,
                // 'header_text' => $headerText,
                // 'header_sub_text' => $result,
                'header_desc' => $result,
                'editUrl' => $editUrl,
                'headerText' => $q->lastname . ', ' . $q->firstname,
                'id' => $q->id,
                'cloneUrl' => $cloneUrl,
                'pdfUrl' => $pdfUrl,
                'iconHtml' => $iconHtml,
                'is_active' => $q->is_active ? 1 : 0,
                'client' => [
                    'salutation' => $q->salutation,
                    'firstname' => $q->firstname,
                    'lastname' => $q->lastname,
                    'address' => $q->client_address,
                    'city' => $q->city,
                    'province' => $q->state,
                    'postal_code' => $q->zip,
                ],
                'latest_payment' => DB::table('client_payments')
                    ->where('client_id', $q->id)
                    ->orderBy('id', 'desc')
                    ->orderBy('payment_date', 'desc')
                    ->select('payment_type', 'payment_date', 'kumon_month', 'amount')
                    ->first(),
            ]);
        }
    }
    public function sendVacationPlanningReminders()
    {
        $today = Carbon::today();

        // Admin email (adjust as needed)
        // $adminEmail = "abdulrehman8961@gmail.com";
        $adminEmail = "support@amaltitek.com";
        // OR fetch from users table
        // $adminEmail = User::where('role', 'admin')->value('email');

        $vacations = DB::table('student_vacations')
            ->where('take_work_home', 1)
            ->whereNotNull('date_range')
            ->get();

        foreach ($vacations as $v) {

            $parts = explode(' to ', $v->date_range);
            if (count($parts) !== 2) {
                continue;
            }

            try {
                $startDate = Carbon::parse(trim($parts[0]))->startOfDay();
                $endDate   = Carbon::parse(trim($parts[1]))->endOfDay();
            } catch (\Exception $e) {
                continue;
            }

            // Only upcoming or active vacations
            if ($today->gt($endDate)) {
                continue;
            }

            $daysBefore = $today->diffInDays($startDate, false);

            /*
        |--------------------------------------------------------------------------
        | Determine reminder type
        |--------------------------------------------------------------------------
        */
            $reminderMap = [
                3 => 'reminder_3_sent',
                2 => 'reminder_2_sent',
                1 => 'reminder_1_sent',
                0 => 'reminder_0_sent',
            ];

            if (!array_key_exists($daysBefore, $reminderMap)) {
                continue;
            }

            $column = $reminderMap[$daysBefore];

            // Skip if already sent
            if ($v->$column == 1) {
                continue;
            }
            echo '1';
            /*
        |--------------------------------------------------------------------------
        | SEND EMAIL
        |--------------------------------------------------------------------------
        */
            Mail::send('emails.vacation_planning', [
                'student' => $v->student_name,
                'subjects' => $v->subjects,
                'dateRange' => $v->date_range,
                'daysBefore' => $daysBefore
            ], function ($mail) use ($adminEmail, $v, $daysBefore) {

                $subjectPrefix = match ($daysBefore) {
                    3 => '3 Days Reminder',
                    2 => '2 Days Reminder',
                    1 => 'Tomorrow Reminder',
                    0 => 'Starts Today',
                };

                $mail->to($adminEmail)
                    ->subject("Vacation Planning Required – {$subjectPrefix} – {$v->student_name}");
            });

            /*
        |--------------------------------------------------------------------------
        | MARK REMINDER AS SENT
        |--------------------------------------------------------------------------
        */
            DB::table('student_vacations')
                ->where('id', $v->id)
                ->update([$column => 1]);
        }
    }
    public function AddClient()
    {
        return view('clients.add');
    }
    public function cloneClient()
    {
        $id = request()->get('id');
        if (isset($id)) {
            $client = DB::table('clients')->where('id', $id)->first();
            $emails = DB::table('client_emails')
                ->where('client_id', $id)
                ->pluck('email')
                ->toArray();
            $rows = DB::table('client_students as cs')
                ->join('student_subjects as ss', 'ss.student_id', '=', 'cs.id')
                ->join('subjects as s', 's.id', '=', 'ss.subject_id')
                ->where('cs.client_id', $id)
                ->select(
                    'cs.student_name',
                    DB::raw('GROUP_CONCAT(CONCAT("[", s.name, "]") SEPARATOR " ") as subjects')
                )
                ->groupBy('cs.id')
                ->get();

            $list = [];

            foreach ($rows as $row) {
                $list[] = $row->student_name . ' [' . $row->subjects . ']';
            }

            $desc = implode(', ', $list);
            return view('clients.clone', compact('client', 'emails', 'desc'));
        }
    }
    public function editClient()
    {
        $id = request()->get('id');
        if (isset($id)) {
            $client = DB::table('clients')->where('id', $id)->first();
            $emails = DB::table('client_emails')
                ->where('client_id', $id)
                ->pluck('email')
                ->toArray();
            $rows = DB::table('client_students as cs')
                ->join('student_subjects as ss', 'ss.student_id', '=', 'cs.id')
                ->join('subjects as s', 's.id', '=', 'ss.subject_id')
                ->where('cs.client_id', $id)
                ->select(
                    'cs.student_name',
                    DB::raw('GROUP_CONCAT(CONCAT("[", s.name, "]") SEPARATOR " ") as subjects')
                )
                ->groupBy('cs.id')
                ->get();

            $list = [];

            foreach ($rows as $row) {
                $list[] = $row->student_name . ' [' . $row->subjects . ']';
            }

            $desc = implode(', ', $list);
            return view('clients.edit', compact('client', 'emails', 'desc'));
        }
    }

    public function ExportPrintClients()
    {
        return view('exports/ExportPrintClients');
    }

    public function ExportPdfClients()
    {
        $pdf = PDF::loadView('exports/ExportPdfClients');
        return $pdf->stream('Clients.pdf');
    }

    public function DeleteClients(Request $request)
    {
        DB::Table('clients')->where('id', $request->id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::Table('client_payments')->where('client_id', $request->id)->update(['is_deleted' => 1]);
        return redirect()->back()->with('success', 'Client Deleted Successfully');
    }
    public function ShowClients(Request $request)
    {


        $qry = DB::Table('clients')->where('id', $request->id)->first();
        return response()->json($qry);
    }




    public function InsertClients(Request $request)
    {
        // dd($request->all());
        // Convert JSON arrays to PHP arrays
        $students_array = json_decode($request->students_array ?? '[]', true);
        $payments_array = json_decode($request->payments_array ?? '[]', true);
        $vacation_array = json_decode($request->vacation_array ?? '[]', true);
        $commentArray = json_decode($request->commentArray ?? '[]', true);
        $attachment_array = json_decode($request->attachmentArray ?? '[]', true);
        $email_ids = $request->email_ids ?? [];
        $portalAccess = $request->has('portal_access') ? 1 : 0;

        // Map form fields to DB columns
        $data = [
            'salutation' => $request->salutation,
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'client_display_name' => $request->first_name . ' ' . $request->last_name,
            'company_name' => $request->company_name ?? null,
            'email_address' => $request->primary_email_address,
            'client_address' => $request->client_address,
            'work_phone' => $request->telephone_no,
            'payment_method' => $request->payment_method,
            'is_active' => 1,
            'mobile' => $request->mobile ?? null,
            'website' => $request->website ?? null,
            'logo' => $request->logo ?? null,
            'country' => $request->country ?? null,
            'zip' => $request->postal_code,
            'state' => $request->province,
            'city' => $request->city,
            'ssl_notification' => $request->cert_notification ?? 0,
            'renewal_notification' => $request->client_notification ?? 0,
            'portal_access' => $portalAccess,
            'created_by' => Auth::id(),
        ];


        $last_id = DB::table('clients')->insertGetId($data);

        $inviteSent = false;

        if ($portalAccess === 1) {

            // prevent duplicate users
            $existingUser = DB::table('users')
                ->where('email', $request->primary_email_address)
                ->where('is_deleted', 0)
                ->first();

            if (!$existingUser) {

                $tempPassword = Str::random(10);

                $userId = DB::table('users')->insertGetId([
                    'client_id' => $last_id,          // ✅ REQUIRED
                    'role' => 'parent',               // use consistent role
                    'firstname' => $request->first_name,
                    'lastname' => $request->last_name,
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->primary_email_address,
                    'password' => Hash::make($tempPassword),
                    'portal_access' => 1,
                    'must_change' => 1,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                // mark client invited
                DB::table('clients')->where('id', $last_id)->update([
                    'portal_invited_at' => now()
                ]);

                // send invite email
                try {
                    $data2 = array(
                        'email' => $request->primary_email_address,
                        'password' => $tempPassword,
                        'name' => $request->first_name . ' ' . $request->last_name,
                        'subject' => 'Password Reset Notification'
                    );

                    Mail::send('emails.password', ["data" => $data2], function ($message) use ($data2) {
                        $message->to($data2['email'])
                            ->subject('Create Your Password');
                    });
                } catch (\Throwable $e) {
                }

                $inviteSent = true;
            }
        }


        // Insert renewal emails
        foreach ($email_ids as $email) {
            if (!empty($email)) {
                DB::table('client_emails')->insert([
                    'client_id' => $last_id,
                    'email' => $email,
                ]);
            }
        }
        $student_map = [];

        // Insert students
        foreach ($students_array as $student) {
            $student_id = DB::table('client_students')->insertGetId([
                'client_id' => $last_id,
                'student_name' => $student['student_name'],
                'start_date' => date('Y-m-d', strtotime($student['start_date'])),
                'amount' => $student['amount'],
                'added_by' => Auth::id(),
            ]);

            $student_map[$student['student_name']] = $student_id;

            // Link subjects
            if (!empty($student['subjects'])) {
                foreach ($student['subjects'] as $subject_id) {
                    DB::table('student_subjects')->insert([
                        'student_id' => $student_id,
                        'subject_id' => $subject_id,
                    ]);
                }
            }
        }

        // Insert payments
        foreach ($payments_array as $payment) {
            DB::table('client_payments')->insert([
                'client_id' => $last_id,
                'payment_date' => date('Y-m-d', strtotime($payment['payment_date'])),
                'kumon_month' => $payment['kumon_month'],
                'payment_type' => $payment['payment_type'],
                'reference_no' => $payment['reference_no'] ?? null,
                'amount' => $payment['amount'],
                'added_by' => Auth::id(),
            ]);
        }

        // Insert vacations
        foreach ($vacation_array as $vac) {
            $student_id = $student_map[$vac['student']] ?? null;
            DB::table('student_vacations')->insert([
                'client_id' => $last_id,
                'student_id' => $student_id,        // <-- add student_id here
                'student_name' => $vac['student'],  // optional, you can keep the name
                'subjects' => json_encode($vac['subjects']),
                'date_range' => $vac['date_range'],
                'take_work_home' => $vac['take_work_home'],
                'reduced_workload' => $vac['reduced_workload'] ?? 0,
                'comment' => $vac['comment'] ?? null,
                'added_by' => Auth::id(),
            ]);
        }

        // Insert comments
        foreach ($commentArray as $c) {
            DB::table('client_comments')->insert([
                'client_id' => $last_id,
                'date' => date('Y-m-d H:i:s', strtotime($c['date'] . ' ' . $c['time'] ?? '00:00:00')),
                'comment' => $c['comment'],
                'name' => $c['name'],
                'added_by' => Auth::id(),
            ]);
        }

        // Insert attachments
        foreach ($attachment_array as $a) {
            copy(public_path('temp_uploads/' . $a['attachment']), public_path('client_attachment/' . $a['attachment']));
            DB::table('client_attachments')->insert([
                'client_id' => $last_id,
                'date' => date('Y-m-d H:i:s', strtotime($a['date'] . ' ' . $a['time'] ?? '00:00:00')),
                'attachment' => $a['attachment'],
                'name' => $a['name'],
                'added_by' => Auth::id(),
            ]);
        }

        DB::table('client_audit_trail')->insert([
            'user_id' => Auth::id(),
            'description' => 'Client added',
            'client_id' => $last_id,
        ]);

        return response()->json('success');
    }
    public function updateClient(Request $request)
    {
        $last_id = $request->client_id;
        if (!$last_id) return;
        DB::beginTransaction();
        $portalAccess = $request->has('portal_access') ? 1 : 0;


        // get old client state
        $oldClient = DB::table('clients')->where('id', $last_id)->first();
        $wasPortalEnabled = (int) ($oldClient->portal_access ?? 0);
        $oldPaymentMethod = $oldClient->payment_method ?? null;

        $inviteSent = false;

        $oldStudents = DB::table('client_students')
            ->where('client_id', $last_id)
            ->get()
            ->keyBy('student_name'); // key by name for comparison

        // Convert JSON arrays to PHP arrays
        $students_array = json_decode($request->students_array ?? '[]', true);
        $payments_array = json_decode($request->payments_array ?? '[]', true);
        $vacation_array = json_decode($request->vacation_array ?? '[]', true);
        $commentArray = json_decode($request->commentArray ?? '[]', true);
        $attachment_array = json_decode($request->attachmentArray ?? '[]', true);
        $email_ids = $request->email_ids ?? [];

        // Map form fields to DB columns
        $data = [
            'salutation' => $request->salutation,
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'client_display_name' => $request->first_name . ' ' . $request->last_name, // or customize
            'company_name' => $request->company_name ?? null,
            'email_address' => $request->primary_email_address,
            'client_address' => $request->client_address,
            'work_phone' => $request->telephone_no,
            'payment_method' => $request->payment_method,
            'mobile' => $request->mobile ?? null,
            'website' => $request->website ?? null,
            'logo' => $request->logo ?? null,
            'country' => $request->country ?? null,
            'zip' => $request->postal_code,
            'state' => $request->province,
            'city' => $request->city,
            'ssl_notification' => $request->cert_notification ?? 0,
            'renewal_notification' => $request->client_notification ?? 0,
            'portal_access' => $portalAccess,
            'updated_at' => now(),
            'updated_by' => Auth::id()
        ];

        DB::table('clients')->where('id', $last_id)->update($data);
        $newPaymentMethod = $request->payment_method;
        $paymentMethodChanged = $request->payment_method_changed == '1'
            && !empty($newPaymentMethod)
            && $oldPaymentMethod
            && $oldPaymentMethod !== $newPaymentMethod;

        if ($paymentMethodChanged) {
            $changedAt = now();
            $angieEmail = $oldClient->email_address ?: config('mail.from.address');
            $changedBy = Auth::user()->firstname . ' ' . Auth::user()->lastname;
            $emailMessage = "PAYMENT METHOD FOR CLIENT HAS CHANGED FROM {$oldPaymentMethod} TO {$newPaymentMethod} ON {$changedAt->format('Y-m-d H:i')} BY {$changedBy}.";

            if (!empty($angieEmail)) {
                try {
                    Mail::raw($emailMessage, function ($message) use ($angieEmail) {
                        $message->to($angieEmail)
                            ->subject('Payment Method Change');
                    });
                } catch (\Throwable $e) {
                }
            }

            DB::table('pinned_messages')->insert([
                'message' => $emailMessage,
                'linked_id' => $last_id,
                'page' => 'client',
                'added_by' => Auth::id(),
                'is_deleteable' => 0,
                'status' => 'payment_method_changed',
                'created_at' => $changedAt->copy()->addDays(30),
            ]);

            DB::table('client_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => $emailMessage,
                'client_id' => $last_id,
            ]);
        }

        // 🔐 PORTAL ACCESS LOGIC (INVITE ONLY ON FIRST ENABLE)
        if ($wasPortalEnabled === 0 && $portalAccess === 1) {

            // email must be unique
            $existingUser = DB::table('users')
                ->where('email', $request->primary_email_address)
                ->where('is_deleted', 0)
                ->first();

            if ($existingUser) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'This email already has an account.'
                ], 422);
            }

            $tempPassword = Str::random(10);

            DB::table('users')->insert([
                'client_id' => $last_id,          // ✅ REQUIRED
                'role' => 'parent',
                'firstname' => $request->first_name,
                'lastname' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->primary_email_address,
                'password' => Hash::make($tempPassword),
                'portal_access' => 1,
                'must_change' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('clients')->where('id', $last_id)->update([
                'portal_invited_at' => now()
            ]);

            try {
                Mail::send('emails.password', [
                    'data' => [
                        'email' => $request->primary_email_address,
                        'password' => $tempPassword,
                        'name' => $request->first_name . ' ' . $request->last_name,
                    ]
                ], function ($message) use ($request) {
                    $message->to($request->primary_email_address)
                        ->subject('Create Your Password');
                });
            } catch (\Throwable $e) {
            }

            $inviteSent = true;
        }


        DB::table('client_emails')->where('client_id', $last_id)->delete();

        // Insert renewal emails
        foreach ($email_ids as $email) {
            if (!empty($email)) {
                DB::table('client_emails')->insert([
                    'client_id' => $last_id,
                    'email' => $email,
                ]);
            }
        }

        $prev_students = DB::table('client_students')->where('client_id', $last_id)->get();
        foreach ($prev_students as $student) {
            DB::table('student_subjects')->where('student_id', $student->id)->delete();
        }
        DB::table('client_students')->where('client_id', $last_id)->delete();

        // Insert students
        if (Auth::user()->role == 'admin') {
            foreach ($students_array as $student) {
                $student_id = DB::table('client_students')->insertGetId([
                    'client_id' => $last_id,
                    'student_name' => $student['student_name'],
                    'start_date' => date('Y-m-d', strtotime($student['start_date'])),
                    'amount' => $student['amount'],
                    'added_by' => Auth::id(),
                ]);

                // Link subjects
                if (!empty($student['subjects'])) {
                    foreach ($student['subjects'] as $subject_id) {
                        DB::table('student_subjects')->insert([
                            'student_id' => $student_id,
                            'subject_id' => $subject_id,
                        ]);
                    }
                }
            }
        } else {
            foreach ($students_array as $student) {

                $oldAmount = null;
                if (isset($oldStudents[$student['student_name']])) {
                    $oldAmount = $oldStudents[$student['student_name']]->amount;
                }

                $student_id = DB::table('client_students')->insertGetId([
                    'client_id' => $last_id,
                    'student_name' => $student['student_name'],
                    'start_date' => date('Y-m-d', strtotime($student['start_date'])),
                    'amount' => $oldAmount,
                    'added_by' => Auth::id(),
                ]);

                // 🔴 PRICE CHANGE DETECTED
                if ($oldAmount !== null && bccomp($oldAmount, $student['amount'], 2) !== 0) {

                    DB::table('student_price_changes')->insert([
                        'client_id'   => $last_id,
                        'student_id'   => $student_id,
                        'student_name' => $student['student_name'],
                        'old_amount'  => $oldAmount,
                        'new_amount'  => $student['amount'],
                    ]);

                    // send email immediately
                    $this->sendPriceChangeEmail(
                        $last_id,
                        $student['student_name'],
                        $oldAmount,
                        $student['amount']
                    );
                }

                // subjects
                if (!empty($student['subjects'])) {
                    foreach ($student['subjects'] as $subject_id) {
                        DB::table('student_subjects')->insert([
                            'student_id' => $student_id,
                            'subject_id' => $subject_id,
                        ]);
                    }
                }
            }
        }

        DB::table('client_payments')->where('client_id', $last_id)->delete();

        // Insert payments
        foreach ($payments_array as $payment) {
            DB::table('client_payments')->insert([
                'client_id' => $last_id,
                'payment_date' => date('Y-m-d', strtotime($payment['payment_date'])),
                'kumon_month' => $payment['kumon_month'],
                'payment_type' => $payment['payment_type'],
                'reference_no' => $payment['reference_no'] ?? null,
                'amount' => $payment['amount'],
                'added_by' => Auth::id(),
            ]);
        }
        DB::table('student_vacations')->where('client_id', $last_id)->delete();
        // Insert vacations
        foreach ($vacation_array as $vac) {
            DB::table('student_vacations')->insert([
                'client_id' => $last_id,
                'student_name' => $vac['student'],
                'subjects' => json_encode($vac['subjects']),
                'date_range' => $vac['date_range'],
                'take_work_home' => $vac['take_work_home'],
                'reduced_workload' => $vac['reduced_workload'] ?? 0,
                'comment' => $vac['comment'] ?? null,
                'added_by' => Auth::id(),
            ]);
        }
        DB::table('client_comments')->where('client_id', $last_id)->delete();
        // Insert comments
        foreach ($commentArray as $c) {
            DB::table('client_comments')->insert([
                'client_id' => $last_id,
                'date' => date('Y-m-d H:i:s', strtotime($c['date'] . ' ' . $c['time'] ?? '00:00:00')),
                'comment' => $c['comment'],
                'name' => $c['name'],
                'added_by' => Auth::id(),
            ]);
        }
        DB::table('client_attachments')->where('client_id', $last_id)->delete();
        // Insert attachments
        foreach ($attachment_array as $a) {
            copy(public_path('temp_uploads/' . $a['attachment']), public_path('client_attachment/' . $a['attachment']));
            DB::table('client_attachments')->insert([
                'client_id' => $last_id,
                'date' => date('Y-m-d H:i:s', strtotime($a['date'] . ' ' . $a['time'] ?? '00:00:00')),
                'attachment' => $a['attachment'],
                'name' => $a['name'],
                'added_by' => Auth::id(),
            ]);
        }

        DB::table('client_audit_trail')->insert([
            'user_id' => Auth::id(),
            'description' => 'Client updated',
            'client_id' => $last_id,
        ]);

        DB::commit();

        return response()->json('success');
    }

    private function sendPriceChangeEmail($clientId, $studentName, $oldAmount, $newAmount)
    {
        // $adminEmails = DB::table('users')
        //     ->where('role', 'admin')
        //     ->pluck('email')
        //     ->toArray();
        $adminEmails = "abdulrehman8961@gmail.com";
        $client = DB::table('clients')->where('id', $clientId)->first();

        try {
            Mail::raw(
                "Price changed for student: {$studentName}\n
                Client: {$client->client_display_name}\n
                Old Amount: {$oldAmount}\n
                New Amount: {$newAmount}\n
                Please review and confirm.",
                function ($message) use ($adminEmails) {
                    $message->to($adminEmails)
                        ->subject('Student Price Change Alert');
                }
            );
        } catch (\Throwable $th) {
            //throw $th;
        }
    }



    public function uploadNetworkAttachment(Request $request)
    {

        $attachment = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];

        $fileExt = explode('.', $attachment);
        $fileActualExt = strtolower(end($fileExt));
        $key = $fileExt[0] . uniqid() . '.' . $fileActualExt;

        $request->file('attachment')->move(public_path('temp_uploads'), $key);

        return response()->json($key);
    }





    public function getAttachmentClients(Request $request)
    {
        $qry = DB::table('client_attachments')->where('client_id', $request->id)->get();
        return response()->json($qry);
    }

    public function getCommentsClients(Request $request)
    {
        $qry = DB::table('client_comments')->where('client_id', $request->id)->get();
        return response()->json($qry);
    }

    public function getClientStudents(Request $request)
    {
        $client_id = $request->id;

        $rows = DB::table('client_students as cs')
            ->leftJoin('student_subjects as ss', 'ss.student_id', '=', 'cs.id')
            ->leftJoin('subjects as s', 's.id', '=', 'ss.subject_id')
            ->where('cs.client_id', $client_id)
            ->select(
                'cs.id',
                'cs.student_name',
                'cs.start_date',
                'cs.amount',
                's.id as subject_id',
                's.name as subject_name'
            )
            ->get();

        $students = $rows->groupBy('id')->map(function ($items) {

            $student = $items->first();

            return [
                'id'           => $student->id,
                'student_name' => $student->student_name,
                'start_date'   => date('d-M-Y', strtotime($student->start_date)),
                'amount'       => $student->amount,
                'subjects'     => $items->filter(fn($i) => $i->subject_id)
                    ->map(fn($i) => [
                        'id'   => $i->subject_id,
                        'name' => $i->subject_name
                    ])
                    ->values()
            ];
        })->values();
        return response()->json($students);
    }
    public function getClientPayments(Request $request)
    {
        $client_id = $request->id;

        $rows = DB::table('client_payments')
            ->where('client_id', $client_id)
            ->get();

        $payments = $rows->groupBy('id')->map(function ($items) {

            $payment = $items->first();

            return [
                'id'           => $payment->id,
                'kumon_month' => $payment->kumon_month,
                'payment_type' => $payment->payment_type,
                'payment_date'   => date('d-M-Y', strtotime($payment->payment_date)),
                'amount'       => $payment->amount,
                'reference_no' => $payment->reference_no ?? null

            ];
        })->values();
        return response()->json($payments);
    }
    public function sendPaymentReceipt(Request $request)
    {
        $email = trim($request->email ?? '');
        if ($email === '') {
            return response()->json(['success' => false, 'message' => 'Email address is required.'], 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => 'Please enter a valid email address.'], 422);
        }

        $data = [
            'email' => $email,
            'client_name' => $request->client_name ?? 'Client',
            'payment_type' => $request->payment_type ?? '-',
            'payment_date' => $request->payment_date ? date('F d, Y', strtotime($request->payment_date)) : '-',
            'kumon_month' => $request->kumon_month ?? '-',
            'amount' => $request->amount ?? '-',
            'reference_no' => $request->reference_no ?? '-',
            'subject' => 'Payment Receipt',
        ];

        try {
            Mailer::send('emails.payment_receipt', ['data' => $data], function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject($data['subject']);
            });
        } catch (\Throwable $e) {
            // show error message
            return response()->json(['success' => false, 'message' => 'Unable to send email. Error: ' . $e->getMessage()], 500);
            // return response()->json(['success' => false, 'message' => 'Unable to send email.'], 500);
        }

        return response()->json(['success' => true]);
    }
    public function getCenterSettings(Request $request)
    {
        $row = DB::table('center_settings')->where('user_id', Auth::id())->first();

        return response()->json([
            'center_name' => $row->center_name ?? '',
            'address' => $row->address ?? '',
            'city' => $row->city ?? '',
            'province' => $row->province ?? '',
            'postal_code' => $row->postal_code ?? '',
            'telephone' => $row->telephone ?? '',
        ]);
    }

    public function saveCenterSettings(Request $request)
    {
        $data = [
            'center_name' => $request->center_name ?? '',
            'address' => $request->address ?? '',
            'city' => $request->city ?? '',
            'province' => $request->province ?? '',
            'postal_code' => $request->postal_code ?? '',
            'telephone' => $request->telephone ?? '',
            'updated_at' => now(),
        ];

        DB::table('center_settings')->updateOrInsert(
            ['user_id' => Auth::id()],
            $data + ['created_at' => now()]
        );

        return response()->json(['success' => true]);
    }
    public function getClientVacation(Request $request)
    {
        $client_id = $request->id;

        $rows = DB::table('student_vacations as sv')
            ->where('sv.client_id', $client_id)
            ->leftJoin('subjects as s', function ($join) {
                $join->whereRaw(
                    'JSON_VALID(sv.subjects)
                 AND sv.subjects LIKE CONCAT(\'%"\', s.id, \'\"%\')'
                );
            })
            ->select(
                'sv.id',
                'sv.client_id',
                'sv.student_name',
                'sv.subjects',
                'sv.date_range',
                'sv.take_work_home',
                'sv.reduced_workload',
                'sv.planned',
                'sv.comment',
                DB::raw('GROUP_CONCAT(s.name SEPARATOR ", ") as subject_names')
            )
            ->groupBy('sv.id')
            ->get()
            ->map(function ($row) {
                $row->subjects = json_decode($row->subjects, true) ?? [];
                return $row;
            });
        return response()->json($rows);
    }

    public function InsertCommentClient(Request $request)
    {
        DB::table('client_comments')->insert([
            'client_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        return redirect()->back()->with('success-comment', 'Comment Added Successfully');
    }
    public function InsertAttachmentClient(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {
                copy(public_path('temp_uploads/' . $a), public_path('network_attachment/' . $a));
                DB::table('client_attachments')->insert([
                    'client_id' => $request->id,
                    'date' => date('Y-m-d H:i:s'),
                    'attachment' => $a,
                    'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->back()->with('success-attachment', 'Attachment Added Successfully');
    }

    function add_pinned_message(Request $request)
    {
        $client_id = $request->client_id;
        $messages = $request->messages;

        // Delete old pinned messages
        DB::table('pinned_messages')
            ->where('linked_id', $client_id)
            ->where('page', 'client')
            ->where('is_deleteable', 1)
            ->delete();

        // Insert new messages
        if (!empty($messages)) {
            foreach ($messages as $msg) {
                DB::table('pinned_messages')->insert([
                    'linked_id' => $client_id,
                    'page' => 'client',
                    'message' => $msg,
                    'added_by' => Auth::user()->id,
                    'created_at' => now()
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pinned messages saved successfully.'
        ]);
    }

    public function UndoDeleteCommentclient(Request $request)
    {
        try {
            $client_id = $request->client_id;
            $comment_id = $request->comment_id;

            // if (Auth::user()->role == 'read') {
            //     return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            // }

            DB::table('client_comments')
                ->where('id', $comment_id)
                ->where('is_deleted', 1)
                ->where('client_id', $client_id)
                ->update([
                    'is_deleted' => 0
                ]);

            DB::table('client_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Comment Recovered | ' . $comment_id,
                'client_id' => $client_id,
            ]);

            return response()->json([
                'status' => 'success',
                'comment_id' => $comment_id,
                'client_id' => $client_id,
                'message' => 'Comment recovered successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function delete_attachment_client(Request $request)
    {
        try {
            $client_id = $request->client_id;
            $attachment_id = $request->attachment_id;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('client_attachments')
                ->where('id', $attachment_id)
                ->where('is_deleted', 0)
                ->where('client_id', $client_id)
                ->update([
                    'is_deleted' => 1
                ]);

            DB::table('client_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Attachment Deleted | ' . $attachment_id,
                'client_id' => $client_id,
            ]);

            return response()->json([
                'status' => 'success',
                'attachment_id' => $attachment_id,
                'client_id' => $client_id,
                'message' => 'Attachment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function undo_delete_attachment_client(Request $request)
    {
        try {
            $client_id = $request->client_id;
            $attachment_id = $request->attachment_id;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('client_attachments')
                ->where('id', $attachment_id)
                ->where('is_deleted', 1)
                ->where('client_id', $client_id)
                ->update([
                    'is_deleted' => 0
                ]);

            DB::table('client_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Attachment Recovered | ' . $attachment_id,
                'client_id' => $client_id,
            ]);

            return response()->json([
                'status' => 'success',
                'attachment_id' => $attachment_id,
                'client_id' => $client_id,
                'message' => 'Attachment recovered successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function UpdateCommentClient(Request $request)
    {
        try {
            $client_id = $request->client_id;
            $comment_id = $request->comment_id;
            $comment = $request->comment_text;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('client_comments')
                ->where('id', $comment_id)
                ->where('is_deleted', 0)
                ->where('client_id', $client_id)
                ->update([
                    'comment' => $comment,
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            DB::table('client_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Comment Updated | ' . $comment_id,
                'client_id' => $client_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Comment updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function DeleteCommentClient(Request $request)
    {
        try {
            $client_id = $request->client_id;
            $comment_id = $request->comment_id;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('client_comments')
                ->where('id', $comment_id)
                ->where('is_deleted', 0)
                ->where('client_id', $client_id)
                ->update([
                    'is_deleted' => 1
                ]);

            DB::table('client_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Comment Deleted | ' . $comment_id,
                'client_id' => $client_id,
            ]);

            return response()->json([
                'status' => 'success',
                'comment_id' => $comment_id,
                'client_id' => $client_id,
                'message' => 'Comment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function EndClient(Request $request)
    {
        $client = DB::table('clients')->where('id', $request->id)
            ->first();
        $newStatus = $request->end == 1 ? 'Active' : 'Inactive';
        if ($client) {
            try {
                Mail::to('support@amaltitek.com')->send(new \App\Mail\StatusChangeMail(
                    $client,
                    $newStatus
                ));
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        if ($request->end == 1) {
            DB::table('pinned_messages')->where(['linked_id' => $request->id, 'page' => 'client', 'status' => 'ended'])->delete();
            DB::Table('clients')->where('id', $request->id)->update(['is_active' => 1]);
            DB::table('client_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'client_id' => $request->id, 'comment' => 'Client successfully Reninstated.<br>' . $request->reason]);

            DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Client successfully Reinstated.', 'client_id' => $request->id]);
            return redirect()->back()->with('success-status', 'Active');
        } else {
            $msg = "This client was Deactivated by " . ucfirst(Auth::user()->firstname) . " " . ucfirst(Auth::user()->lastname) . " on " . date('d-M-Y g:i a', strtotime(now()));
DB::table('pinned_messages')->where([
                'linked_id' => $request->id,
                'page' => 'client',
                'status' => 'ended'
            ])->delete();
            DB::table('pinned_messages')->insert([
                'message' => $msg,
                'linked_id' => $request->id,
                'page' => 'client',
                'added_by' => Auth::user()->id,
                'is_deleteable' => 0,
                'status' => 'ended',
            ]);
            DB::Table('clients')->where('id', $request->id)->update(['is_active' => 0, 'ended_reason' => $request->reason, 'ended_by' => Auth::id(), 'ended_on' => date('Y-m-d H:i:s')]);
            DB::table('client_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'client_id' => $request->id, 'comment' => 'Client successfully Ended.<br>' . $request->reason]);

            DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Client successfully Ended.', 'client_id' => $request->id]);
            return redirect()->back()->with('success-status', 'Ended');
        }
    }

    public function DeleteClient(Request $request)
    {
        try {
            $id = $request->id;

            $qry = DB::table('clients')->where('id', $id)->first();

            if (!$qry) {
                return response()->json(['status' => 'error', 'message' => 'Client not found.']);
            }

            // Soft delete related data
            $timestamp = date('Y-m-d H:i:s');

            DB::table('clients')
                ->where('id', $id)
                ->update(['is_deleted' => 1, 'deleted_at' => $timestamp]);

            // Add audit trail
            DB::table('client_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Client Deleted | ' . $id,
                'client_id' => $id,
                'created_at' => $timestamp,
            ]);

            return response()->json([
                'status' => 'success',
                'id' => $id,
                'message' => 'Client deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function DeleteClientUndo(Request $request)
    {
        try {
            $id = $request->id;

            $qry = DB::table('clients')->where('id', $id)->first();

            if (!$qry) {
                return response()->json(['status' => 'error', 'message' => 'Client not found.']);
            }



            DB::table('clients')
                ->where('id', $id)
                ->update(['is_deleted' => 0, 'deleted_at' => null]);

            // Add audit trail
            DB::table('client_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Client Recovered | ' . $id,
                'client_id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status' => 'success',
                'client_id' => $id,
                'message' => 'Client recovered successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function confirmPriceChange($id)
    {
        $priceChange = DB::table('student_price_changes')
            ->where('confirmed', 0)
            ->where('id', $id)
            ->first();

        if ($priceChange) {
            DB::table('client_students')
                ->where('id', $priceChange->student_id)
                ->update(['amount' => $priceChange->new_amount]);

            DB::table('student_price_changes')
                ->where('id', $priceChange->id)
                ->update(['confirmed' => 1]);
        }

        return redirect()->back()->with('success', 'Change Confirmed');
    }

    public function sendPaymentReminder(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'emails'    => 'required|array'
        ]);

        $notificationType = $request->input('notification_type', 'payment');
        if (!in_array($notificationType, ['payment', 'vacation'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid notification type'
            ]);
        }

        /* =========================
       Fetch client
    ==========================*/
        $client = DB::table('clients')
            ->where('id', $request->client_id)
            ->select('id', 'client_display_name', 'email_address')
            ->first();

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client not found'
            ]);
        }

        foreach ($request->emails as $email) {

            $exists = DB::table('client_emails')
                ->where('client_id', $client->id)
                ->where('email', $email)
                ->exists();

            if (!$exists) {
                DB::table('client_emails')->insert([
                    'client_id' => $client->id,
                    'email'     => $email,
                    'created_at' => now()
                ]);
            }
        }

        /* =========================
       Merge main + additional emails
    ==========================*/
        $emails = array_unique(array_merge(
            [$client->email_address],
            $request->emails
        ));

        if ($notificationType === 'vacation') {
            $today = Carbon::today();

            $vacations = DB::table('student_vacations')
                ->where('client_id', $client->id)
                ->where('take_work_home', 1)
                ->whereNotNull('date_range')
                ->get();

            $vacationPayloads = [];
            foreach ($vacations as $vacation) {
                $parts = explode(' to ', $vacation->date_range);
                if (count($parts) != 2) {
                    continue;
                }

                try {
                    $startDate = Carbon::parse(trim($parts[0]))->startOfDay();
                    $endDate = Carbon::parse(trim($parts[1]))->endOfDay();
                } catch (\Exception $e) {
                    continue;
                }

                if ($today->gt($endDate)) {
                    continue;
                }

                $daysBefore = $today->diffInDays($startDate, false);
                if ($daysBefore < 0) {
                    $daysBefore = 0;
                }

                $vacationPayloads[] = [
                    'vacation' => $vacation,
                    'daysBefore' => $daysBefore,
                ];
            }

            if (count($vacationPayloads) == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No upcoming vacations found'
                ]);
            }

            foreach ($emails as $email) {
                foreach ($vacationPayloads as $payload) {
                    $vacation = $payload['vacation'];
                    $daysBefore = $payload['daysBefore'];

                    $subjectPrefix = match (true) {
                        $daysBefore === 0 => 'Starts Today',
                        $daysBefore === 1 => 'Tomorrow Reminder',
                        $daysBefore === 2 => '2 Days Reminder',
                        $daysBefore === 3 => '3 Days Reminder',
                        default => 'Upcoming Vacation'
                    };

                    try {
                        Mail::send('emails.vacation_planning', [
                            'student' => $vacation->student_name,
                            'subjects' => $vacation->subjects,
                            'dateRange' => $vacation->date_range,
                            'daysBefore' => $daysBefore,
                            'client' => $client,
                        ], function ($mail) use ($email, $vacation, $subjectPrefix) {
                            $mail->to($email)
                                ->subject("Vacation Planning Required - {$subjectPrefix} - {$vacation->student_name}");
                        });
                    } catch (\Throwable $th) {
                        // ignore mail errors to continue sending to other recipients
                    }
                }
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Vacation notification emails sent successfully'
            ]);
        }

        /* =========================
       Fetch students with upcoming payments
       (example: next 7 days - adjust as needed)
    ==========================*/
        $students = DB::table('client_students')
            ->where('client_id', $client->id)
            ->whereBetween('start_date', [
                now()->toDateString(),
                now()->addDays(7)->toDateString()
            ])
            ->orderBy('start_date')
            ->get();

        if ($students->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No upcoming payments found'
            ]);
        }

        /* =========================
       Send emails
    ==========================*/
        foreach ($emails as $email) {
            try {
                //code...
                Mail::to($email)->send(
                    new \App\Mail\PaymentReminderMail(
                        $client,
                        $students
                    )
                );
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment reminder emails sent successfully'
        ]);
    }

    public function exportClientData(Request $request)
    {
        $type = strtolower($request->query('type', ''));
        if (!in_array($type, ['clients', 'students'], true)) {
            return response()->json(['success' => false, 'message' => 'Invalid export type.'], 400);
        }

        $filename = $type . '-export-' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'clients') {
                fputcsv($handle, [
                    'Salutation',
                    'Fname',
                    'Lname',
                    'Address',
                    'City',
                    'Province',
                    'PostalCode',
                    'Tel',
                    'Prim-Email',
                    'Paymethod'
                ]);

                $clients = DB::table('clients')
                    ->where('is_deleted', 0)
                    ->orderBy('id')
                    ->get([
                        'salutation',
                        'firstname',
                        'lastname',
                        'client_address',
                        'city',
                        'state',
                        'zip',
                        'work_phone',
                        'email_address',
                        'payment_method'
                    ]);

                foreach ($clients as $row) {
                    fputcsv($handle, [
                        $row->salutation ?? '',
                        $row->firstname ?? '',
                        $row->lastname ?? '',
                        $row->client_address ?? '',
                        $row->city ?? '',
                        $row->state ?? '',
                        $row->zip ?? '',
                        $row->work_phone ?? '',
                        $row->email_address ?? '',
                        $row->payment_method ?? ''
                    ]);
                }
            }

            if ($type === 'students') {
                fputcsv($handle, [
                    'Client Name',
                    'Student Name',
                    'Subject 1',
                    'Subject 2',
                    'Subject 3',
                    'Start Date',
                    'Amount'
                ]);

                $students = DB::table('client_students as cs')
                    ->leftJoin('clients as c', 'c.id', '=', 'cs.client_id')
                    ->leftJoin('student_subjects as ss', 'ss.student_id', '=', 'cs.id')
                    ->leftJoin('subjects as s', 's.id', '=', 'ss.subject_id')
                    ->select(
                        'cs.student_name',
                        'cs.start_date',
                        'cs.amount',
                        'c.firstname',
                        'c.lastname',
                        DB::raw('GROUP_CONCAT(s.name ORDER BY s.id SEPARATOR ",") as subject_names')
                    )
                    ->groupBy('cs.id')
                    ->orderBy('cs.id')
                    ->get();

                foreach ($students as $row) {
                    $clientName = trim(($row->firstname ?? '') . ' ' . ($row->lastname ?? ''));
                    $subjects = $row->subject_names ? array_slice(explode(',', $row->subject_names), 0, 3) : [];
                    $subjects = array_pad($subjects, 3, '');

                    fputcsv($handle, [
                        $clientName,
                        $row->student_name ?? '',
                        $subjects[0],
                        $subjects[1],
                        $subjects[2],
                        $row->start_date ?? '',
                        $row->amount ?? ''
                    ]);
                }
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function importClientData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:clients,students',
            'file' => 'required|file|mimes:csv,txt'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $type = strtolower($request->input('type'));
        $file = $request->file('file');
        $normalizeHeader = function ($value) {
            $value = strtolower(trim($value));
            return preg_replace('/[^a-z0-9]/', '', $value);
        };

        $parseDate = function ($value) {
            $timestamp = strtotime($value);
            if ($timestamp === false) {
                throw new \Exception('Invalid date format.');
            }
            return date('Y-m-d', $timestamp);
        };
        $normalizePhone = function ($value) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
            }
            if (preg_match('/e/i', $value)) {
                $value = sprintf('%.0f', (float) $value);
            }
            return $value;
        };

        try {
            DB::transaction(function () use ($type, $file, $normalizeHeader, $parseDate) {
                $handle = fopen($file->getRealPath(), 'r');
                if (!$handle) {
                    throw new \Exception('Unable to read uploaded file.');
                }

                $headerRow = fgetcsv($handle);
                if (!$headerRow) {
                    fclose($handle);
                    throw new \Exception('CSV header is missing.');
                }

                $headers = array_map($normalizeHeader, $headerRow);
                $requiredHeaders = $type === 'students'
                    ? ['clientname', 'studentname', 'startdate', 'amount']
                    : ['fname', 'lname', 'primemail'];
                $missingHeaders = array_diff($requiredHeaders, $headers);
                if (!empty($missingHeaders)) {
                    fclose($handle);
                    throw new \Exception('Missing required columns: ' . implode(', ', $missingHeaders));
                }
                $clients = DB::table('clients')
                    ->select('id', 'firstname', 'lastname', 'client_display_name', 'company_name')
                    ->where('is_deleted', 0)
                    ->get();

                $clientMap = [];
                foreach ($clients as $client) {
                    $fullName = trim(($client->firstname ?? '') . ' ' . ($client->lastname ?? ''));
                    foreach ([$fullName, $client->client_display_name, $client->company_name] as $name) {
                        $key = strtolower(trim((string) $name));
                        if ($key !== '') {
                            $clientMap[$key] = $client->id;
                        }
                    }
                }

                while (($row = fgetcsv($handle)) !== false) {
                    if (count(array_filter($row, function ($value) {
                        return trim((string) $value) !== '';
                    })) === 0) {
                        continue;
                    }

                    $data = [];
                    foreach ($headers as $index => $header) {
                        $data[$header] = $row[$index] ?? '';
                    }

                    if ($type === 'clients') {
                        $salutation = trim($data['salutation'] ?? '');
                        $firstName = trim($data['fname'] ?? '');
                        $lastName = trim($data['lname'] ?? '');
                        $address = trim($data['address'] ?? '');
                        $city = trim($data['city'] ?? '');
                        $province = trim($data['province'] ?? '');
                        $postalCode = trim($data['postalcode'] ?? '');
                        $telephone = $normalizePhone($data['tel'] ?? '');
                        $primaryEmail = trim($data['primemail'] ?? '');
                        $paymethod = trim($data['paymethod'] ?? '');

                        if ($firstName === '' || $lastName === '' || $primaryEmail === '') {
                            throw new \Exception('Missing required client fields.');
                        }

                        DB::table('clients')->insert([
                            'salutation' => $salutation,
                            'firstname' => $firstName,
                            'lastname' => $lastName,
                            'client_address' => $address,
                            'city' => $city,
                            'state' => $province,
                            'zip' => $postalCode,
                            'work_phone' => $telephone,
                            'email_address' => $primaryEmail,
                            'payment_method' => $paymethod !== '' ? $paymethod : 'Credit Card',
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                    if ($type === 'students') {
                        $clientName = strtolower(trim($data['clientname'] ?? ''));
                        $studentName = trim($data['studentname'] ?? '');
                        $startDate = trim($data['startdate'] ?? '');
                        $amount = trim($data['amount'] ?? '');

                        if ($clientName === '' || $studentName === '' || $startDate === '' || $amount === '') {
                            throw new \Exception('Missing required student fields.');
                        }

                        if (!isset($clientMap[$clientName])) {
                            throw new \Exception('Client not found: ' . $clientName);
                        }

                        $studentId = DB::table('client_students')->insertGetId([
                            'client_id' => $clientMap[$clientName],
                            'student_name' => $studentName,
                            'start_date' => $parseDate($startDate),
                            'amount' => $amount,
                            'added_by' => Auth::id()
                        ]);

                        $subjectNames = [
                            trim($data['subject1'] ?? ''),
                            trim($data['subject2'] ?? ''),
                            trim($data['subject3'] ?? '')
                        ];

                        foreach ($subjectNames as $subjectName) {
                            if ($subjectName === '') {
                                continue;
                            }
                            $subject = DB::table('subjects')
                                ->whereRaw('LOWER(name) = ?', [strtolower($subjectName)])
                                ->first();

                            if (!$subject) {
                                throw new \Exception('Subject not found: ' . $subjectName);
                            }

                            DB::table('student_subjects')->insert([
                                'student_id' => $studentId,
                                'subject_id' => $subject->id,
                                'created_at' => now()
                            ]);
                        }
                    }

                }

                fclose($handle);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Import completed successfully.']);
    }

}
