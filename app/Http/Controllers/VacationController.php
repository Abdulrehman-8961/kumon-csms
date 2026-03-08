<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Mail;
use Hash;
use PDF;

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
use Illuminate\Support\Facades\Auth as FacadesAuth;

class VacationController extends Controller
{
    //
    public function __construct() {}

    public function index()
    {
        $title = "Vacation";
        return view('vacation.read', compact('title'));
    }
    public function getVacationContent(Request $request)
    {
        $id = $request->input('id');
        if (isset($id)) {
            // $q = DB::table('users')->where('id', $id)->first();
            $row = DB::table('student_vacations')
                ->select(
                    'student_vacations.*',
                    'users.firstname',
                    'users.lastname',
                    DB::raw("COALESCE(NULLIF(client_students.student_name, ''), student_vacations.student_name) as student_name")
                )
                ->leftJoin('users', function($join) {
                    $join->on('users.id', 'student_vacations.added_by');
                })
                ->leftJoin('client_students', function($join) {
                    $join->on('client_students.id', 'student_vacations.student_id');
                })
                ->where('student_vacations.id', $id)
                ->first();

            if (!$row) {
                return response()->json([
                    'cards' => '',
                    'content' => '<div class="block new-block mt-2"><div class="block-content py-3 px-3"><h6 class="font-titillium text-grey fw-400 mb-0">Vacation record was not found.</h6></div></div>',
                    'header_img' => asset('public/img/menu-icon-contracts-white.png'),
                    'header_sub_text' => '',
                    'header_desc' => '',
                    'headerText' => '',
                    'take_work_home' => 0,
                    'planned' => 0,
                    'id' => 0,
                    'message' => 'Vacation record not found.',
                ], 404);
            }

            // decode subjects first
            $subjectsArray = !empty($row->subjects) ? json_decode($row->subjects, true) : [];
            if (!is_array($subjectsArray)) {
                $subjectsArray = [];
            }

            $subjectNames = [];
            if (!empty($subjectsArray)) {
                $subjectNames = DB::table('subjects')
                    ->whereIn('id', $subjectsArray) // ✅ now it's an array
                    ->pluck('name')
                    ->toArray();
            }

            $row->subject_names = implode(', ', $subjectNames);
            $row->subjects = $subjectsArray;


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
                            <div class="tab-pane fade show active" id="nav-main-vacation" role="tabpanel" aria-labelledby="nav-main-tab-vacation">
                                ';

            // if (!empty($row->reduced_workload)) {
            //     $html .= '
            //                     <div class="border mt-1 mb-3" style="background:#FDE9C9;border-color:#F5B041;border-radius:10px;padding:10px 14px;">
            //                         <div class="d-flex align-items-center">
            //                             <i class="fa-light fa-message-exclamation text-orange fs-18 mr-2"></i>
            //                             <span class="font-titillium text-darkgrey fw-600">Student is requesting a reduced workload during vacation time</span>
            //                         </div>
            //                     </div>
            //                     ';
            // }

            $html .= '
                                <div class="block new-block position-relative mt-2">
                                    <div class="block-content py-0" style="padding-left: 30px; padding-right: 30px">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <h5 class="titillium-web-black mb-3 text-darkgrey">
                                                General Information
                                                </h5>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="border p-2 mb-3 border-style pl-3">
                                                    <h6 class="font-titillium content-title mb-1 fw-700">Student name</h6>
                                                    <div class="d-flex pt-1 mb-1">
                                                        <i class="fa-light fa-graduation-cap text-grey fs-18"></i>
                                                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
                                                        ' . $row->student_name . '
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="border p-2 mb-3 border-style pl-3">
                                                    <h6 class="font-titillium content-title mb-1 fw-700">Date Range</h6>
                                                    <div class="d-flex pt-1 mb-1">
                                                        <i class="fa-light fa-calendar-range text-grey fs-18"></i>
                                                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
                                                        ' . $row->date_range . '
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="border p-2 mb-3 border-style pl-3">
                                                    <h6 class="font-titillium content-title mb-1 fw-700">Subject</h6>
                                                    <div class="d-flex pt-1 mb-1">
                                                        <i class="fa-light fa-seal text-grey fs-18"></i>
                                                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
                                                        ' . $row->subject_names . '
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="border p-2 mb-3 border-style pl-3">
                                                    <h6 class="font-titillium content-title mb-1 fw-700">Take Work Home</h6>
                                                    <div class="d-flex pt-1 mb-1">
                                                        <i class="fa-light fa-books text-grey fs-18"></i>
                                                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
                                                        ' . ($row->take_work_home ? "Yes" : "No") . '
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="border p-2 mb-3 border-style pl-3">
                                                    <h6 class="font-titillium content-title mb-1 fw-700">Reduced Workload</h6>
                                                    <div class="d-flex pt-1 mb-1">
                                                        <i class="fa-light fa-books text-grey fs-18"></i>
                                                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
                                                        ' . ($row->reduced_workload ? "Yes" : "No") . '
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12">
                                                <div class="border p-2 mb-3 border-style pl-3">
                                                    <h6 class="font-titillium content-title mb-1 fw-700">Comment</h6>
                                                    <div class="d-flex pt-1 mb-1">
                                                        <i class="fa-light fa-comment text-grey fs-18"></i>
                                                    <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
                                                        ' . nl2br(e($row->comment ?? '')) . '
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-audit-vacation" role="tabpanel" aria-labelledby="nav-audit-tab-vacation">



    <div class="block new-block position-relative mt-3" style="max-height: 600px; overflow-y: auto;">
        <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
            <div class="row">
                <div class="col-sm-11">
                    <h5 class="titillium-web-black mb-3 text-darkgrey">Audit Trail</h5>
                </div>
                <div class="col-sm-1 text-right d-flex justify-content-end align-items-center">
                    <div class="download-icon mr-2" id="downloadAuditLogCSV" style="right: 25px; top: 10px; cursor: pointer;">
                        <i class="fa-light fa-arrow-down-to-line text-grey fs-18" style="font-size:20px;"></i>
                    </div>
                    <div class="audit-sort-toggle" data-order="desc" title="Sort audit trail" style="cursor: pointer;">
                        <i class="fa-light fa-circle-sort-up fs-18" style="font-size:20px; color:#36454F;"></i>
                    </div>
                </div>
                ';

                $vacation_audit = DB::table('vacation_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.vacation_id', $row->id)->orderBy('c.id', 'desc')->get();

                if (sizeof($vacation_audit) > 0) {
                foreach ($vacation_audit as $c) {
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
                                        <h6 class="font-titillium text-darkgrey mb-1 fw-500 adit-message">' . $c->description . '</h6>
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
            $headerImg = asset('public/img/menu-icon-contracts-white.png');
            // $cards = ``;

            $cards = '<div class="container px-0 mt-0" style="overflow: hidden;">
    <div class="d-flex">
    <div class="cards-container">
        <div class="mr-3 status-card">
';
            if ($row->planned) {
                $planned_at = date('d-M-Y',strtotime($row->planned_at));
                $cards .= '        
            <div class="rounded-10px status-card-container  pl-2 pr-4 pt-2 bg-white h-100" style="width: fit-content; border: 1px solid #74A639; --status-color:#74A639;">
                                <h6 class="font-titillium fw-800 text-darkgreen fs-16 mb-0" style="line-height: 0.75;padding-top: 6px;">PLANNED</h6>
                                <div class="d-flex align-items-center">
                                    <i class="fa-light fa-sparkles text-darkgreen fs-16 mr-2"></i>
                                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0">' . $planned_at . '</p>
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

                $cards .= '<div class="container" style="padding-right: 10px;">';
                $pinnedBy = trim(($row->firstname ?? '') . ' ' . ($row->lastname ?? ''));
                    $pinnedDate = !empty($row->created_at) ? Carbon::parse($row->created_at)->format('d-M-Y') : '';
                    $pinnedMeta = trim($pinnedBy . (strlen($pinnedBy) && strlen($pinnedDate) ? ', ' : '') . $pinnedDate);
            if (!empty($row->reduced_workload)) {
                // $cards .= '<div class="align-items-center container d-flex justify-content-between mb-0 px-2 py-1 mt-2 text-darkgrey message-banner" style="background: #E67E22 !important;">
                //                 <div class="d-flex align-items-center">
                //                     <i class="fa-solid fa-message-exclamation fs-16 mr-3"></i>
                //                     <p class="font-titillium fs-16 fw-500 mb-0 text-darkgrey">Student is requesting a reduced workload during vacation time</p>
                //                 </div>
                //             </div>';

                $cards .= '
                            <div class="align-items-center container d-flex justify-content-between mb-0 px-2 py-1 mt-2  alert-banner">
                                <div class="d-flex align-items-center justify-content-between msg-left">
                                    <div class="align-items-center d-flex">
                                        <i class="fa-solid fa-alarm-exclamation text-dark-yellow fs-16 mr-2"></i>
                                        <p class="font-titillium font-12pt fw-500 mb-0 text-darkgrey msg-text text-ellipsis">
                                            <span class="alert-text">ALERT:</span> Reduced workload requested
                                        </p>
                                    </div>
                                </div>

                                <span class="font-titillium fw-500 text-darkgrey msg-meta font-8pt">
                                    ' . e($pinnedMeta) . '
                                </span>
                            </div>';
                }
            if (!empty($row->take_work_home) && $row->take_work_home == 1 && $row->planned == 0) {
                // $cards .= '<div class="align-items-center container d-flex justify-content-between mb-0 px-2 py-1 mt-2 text-darkgrey message-banner" style="background: #E67E22 !important;">
                //                 <div class="d-flex align-items-center">
                //                     <i class="fa-solid fa-message-exclamation fs-16 mr-3"></i>
                //                     <p class="font-titillium fs-16 fw-500 mb-0 text-darkgrey">Student is requesting a reduced workload during vacation time</p>
                //                 </div>
                //             </div>';

                $cards .= '
                            <div class="align-items-center container d-flex justify-content-between mb-0 px-2 py-1 mt-2  alert-banner">
                                <div class="d-flex align-items-center justify-content-between msg-left">
                                    <div class="align-items-center d-flex">
                                        <i class="fa-solid fa-alarm-exclamation text-dark-yellow fs-16 mr-2"></i>
                                        <p class="font-titillium font-12pt fw-500 mb-0 text-darkgrey msg-text text-ellipsis">
                                            <span class="alert-text">ALERT:</span> '. $row->student_name .' Vacation '. $row->date_range .' requires planning
                                        </p>
                                    </div>
                                </div>

                                <span class="font-titillium fw-500 text-darkgrey msg-meta font-8pt">
                                    ' . e($pinnedMeta) . '
                                </span>
                            </div>';
                }
        $cards .= '</div>';
            return response()->json([
                'cards' => $cards,
                'content' => $html,
                'header_img' => $headerImg,
                // 'header_text' => $headerText,
                'header_sub_text' => $row->subject_names,
                'header_desc' => $row->subject_names,
                // 'editUrl' => $editUrl,
                'headerText' => $row->student_name,
                'take_work_home' => (int) $row->take_work_home,
                'planned' => (int) ($row->planned ?? 0),
                'id' => (int) $row->id,
                // 'id' => $q->id,
                // 'cloneUrl' => $cloneUrl,
                // 'pdfUrl' => $pdfUrl,
                // 'iconHtml' => $iconHtml,
            ]);
        }
    }
    public function AddVacation()
    {
        return view('vacation.add');
    }
    public function editVacation()
    {
        $id = request()->get('id');
        if (isset($id)) {
            $row = DB::table('student_vacations')
                ->where('id', $id)
                ->first();
            if (!$row) {
                abort(404);
            }

            // decode subjects first
            $subjectsArray = !empty($row->subjects) ? json_decode($row->subjects, true) : [];
            if (!is_array($subjectsArray)) {
                $subjectsArray = [];
            }

            $subjectNames = [];
            if (!empty($subjectsArray)) {
                $subjectNames = DB::table('subjects')
                    ->whereIn('id', $subjectsArray) // ✅ now it's an array
                    ->pluck('name')
                    ->toArray();
            }

            $row->subject_names = implode(', ', $subjectNames);
            $row->subjects = $subjectsArray;
            return view('vacation.edit', compact('row'));
        }
    }
    public function cloneVacation()
    {
        $id = request()->get('id');
        if (isset($id)) {
            $row = DB::table('student_vacations')
                ->where('id', $id)
                ->first();
            if (!$row) {
                abort(404);
            }

            // decode subjects first
            $subjectsArray = !empty($row->subjects) ? json_decode($row->subjects, true) : [];
            if (!is_array($subjectsArray)) {
                $subjectsArray = [];
            }

            $subjectNames = [];
            if (!empty($subjectsArray)) {
                $subjectNames = DB::table('subjects')
                    ->whereIn('id', $subjectsArray) // ✅ now it's an array
                    ->pluck('name')
                    ->toArray();
            }

            $row->subject_names = implode(', ', $subjectNames);
            $row->subjects = $subjectsArray;
            return view('vacation.clone', compact('row'));
        }
    }
    public function insertVacation(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'date_range' => 'required',
            'subjects' => 'required'
        ]);

        $takeWorkHome = $request->boolean('take_work_home') ? 1 : 0;

        $vacationId = DB::table('student_vacations')->insertGetId([
            'client_id' => Auth::user()->role == 'parent' ? Auth::user()->id : $request->client_id,
            'student_id' => $request->student_id,
            'student_name' => $request->student_name,
            'subjects' => json_encode($request->subjects),
            'date_range' => $request->date_range,
            'take_work_home' => $takeWorkHome,
            'reduced_workload' => $request->boolean('reduced_workload') ? 1 : 0,
            'comment' => $request->comments ?? null,
            'added_by' => Auth::id(),
        ]);

        if ($takeWorkHome === 1) {
            DB::table('vacation_audit_trail')->insert([
                'user_id' => Auth::id(),
                'vacation_id' => $vacationId,
                'description' => 'Student vacation requires planning.',
                'created_at' => now(),
            ]);

            $centerSettings = center_settings();
            $adminEmail = trim((string) ($centerSettings->administrator_email ?? ''));
            $centerName = trim((string) ($centerSettings->center_name ?? 'Kumon csms'));

            if ($adminEmail !== '' && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                $planStudentUrl = url('/vacations') . '?id=' . $vacationId;
                $vacationEmailSent = false;

                try {
                    Mail::send('emails.vacation_requires_planning', [
                        'student_name' => $request->student_name,
                        'date_range' => $request->date_range,
                        'plan_student_url' => $planStudentUrl,
                        'center_name' => $centerName,
                    ], function ($message) use ($adminEmail, $request) {
                        $message->to($adminEmail)
                            ->from('noreply@kumon-csms.com', 'Kumon-noreply')
                            ->subject('Vacation Requires Planning - ' . $request->student_name);
                    });
                    $vacationEmailSent = true;
                } catch (\Throwable $e) {
                    // Keep vacation creation successful even if email fails.
                }

                if ($vacationEmailSent) {
                    DB::table('vacation_audit_trail')->insert([
                        'user_id' => Auth::id(),
                        'vacation_id' => $vacationId,
                        'description' => 'Vacation request email notification sent to ' . $adminEmail . '.',
                        'created_at' => now(),
                    ]);
                }
            }
        }

        return response()->json('success');
    }
    public function updateVacation(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'date_range' => 'required',
            'subjects' => 'required'
        ]);
        DB::table('student_vacations')->where('id', $request->vacation_id)->update([
            'client_id' => Auth::user()->role == 'parent' ? Auth::user()->id : $request->client_id,
            'student_id' => $request->student_id,
            'student_name' => $request->student_name,
            'subjects' => json_encode($request->subjects),
            'date_range' => $request->date_range,
            'take_work_home' => $request->boolean('take_work_home') ? 1 : 0,
            'reduced_workload' => $request->boolean('reduced_workload') ? 1 : 0,
            'comment' => $request->comments ?? null,
            'added_by' => Auth::id(),
        ]);

        return response()->json('success');
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





    public function getAttachmentUser(Request $request)
    {
        $qry = DB::table('user_attachments')->where('user_id', $request->id)->get();
        return response()->json($qry);
    }

    public function getCommentsUser(Request $request)
    {
        $qry = DB::table('user_comments')->where('user_id', $request->id)->get();
        return response()->json($qry);
    }

    public function DeleteVacation(Request $request)
    {
        try {
            $id = $request->id;

            $qry = DB::table('student_vacations')->where('id', $id)->first();

            if (!$qry) {
                return response()->json(['status' => 'error', 'message' => 'User not found.']);
            }

            // Soft delete related data
            $timestamp = date('Y-m-d H:i:s');

            DB::table('student_vacations')
                ->where('id', $id)
                ->delete();

            return response()->json([
                'status' => 'success',
                'id' => $id,
                'message' => 'User deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function DeleteVacationUndo(Request $request)
    {
        try {
            $id = $request->id;
            $qry = DB::table('users')->where('id', $id)->first();
            if (!$qry) {
                return response()->json(['status' => 'error', 'message' => 'User not found.']);
            }
            DB::table('users')
                ->where('id', $id)
                ->update(['is_deleted' => 0, 'deleted_at' => null]);


            return response()->json([
                'status' => 'success',
                'user_id' => $id,
                'message' => 'User recovered successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function markPlanned(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $vacation = DB::table('student_vacations')
            ->where('id', $request->id)
            ->first();

        if (!$vacation) {
            return response()->json(['status' => 'error', 'message' => 'Vacation not found'], 404);
        }

        DB::table('student_vacations')
            ->where('id', $request->id)
            ->update([
                'planned' => 1,
                'planned_at' => now(),
                'updated_at' => now(),
            ]);

        if ((int) ($vacation->planned ?? 0) === 0) {
            DB::table('vacation_audit_trail')->insert([
                'user_id' => Auth::id(),
                'vacation_id' => $vacation->id,
                'description' => 'Vacation planning during ' . $vacation->date_range . ' for ' . $vacation->student_name . ' completed.',
                'created_at' => now(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
