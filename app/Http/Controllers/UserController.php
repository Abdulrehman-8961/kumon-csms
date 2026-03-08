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

class UserController extends Controller
{
    //
    public function __construct() {}

    public function index()
    {
        $title = "Users";
        return view('user.read', compact('title'));
    }
    public function getUserContent(Request $request)
    {
        $id = $request->input('id');
        if (isset($id)) {
            $q = DB::table('users')->where('id', $id)->first();
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
                                            <div class="tab-pane fade show active" id="nav-main-user" role="tabpanel" aria-labelledby="nav-main-tab-user">

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
      <div class="col-9">
      <div class="row">
      <div class="col-sm-12">
        <div class="border p-2 mb-3 border-style pl-3">
          <h6 class="font-titillium content-title mb-1 fw-700">Email</h6>
          <div class="d-flex pt-1 mb-1">
            <i class="fa-light fa-envelope text-grey fs-18"></i>
            <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
              ' . $q->email . '
            </h6>
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="border p-2 mb-3 border-style pl-3">
          <h6 class="font-titillium content-title mb-1 fw-700">First Name</h6>
          <div class="d-flex pt-1 mb-1">
            <i class="fa-light fa-user-tie text-grey fs-18"></i>
            <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
              ' . $q->firstname . '
            </h6>
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="border p-2 mb-3 border-style pl-3">
          <h6 class="font-titillium content-title mb-1 fw-700">Last Name</h6>
          <div class="d-flex pt-1 mb-1">
            <i class="fa-light fa-user-tie text-grey fs-18"></i>
            <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
              ' . $q->lastname . '
            </h6>
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="border p-2 mb-3 border-style pl-3">
          <h6 class="font-titillium content-title mb-1 fw-700">Role</h6>
          <div class="d-flex pt-1 mb-1">
            <i class="fa-light fa-puzzle text-grey fs-18"></i>
            <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">
              ' . ucfirst($q->role) . '
            </h6>
          </div>
        </div>
      </div>
      </div>
      </div>
      <div class="col-3">';
            if ($q->user_image && $q->user_image != "" && $q->user_image != null) {
                $html .= '<img src="public/client_logos/' . $q->user_image . '" width="100%" class="rounded">';
            }
            $html .= '</div>
  </div>
</div></div>
';
            $html .= '</div>
  
  <div class="tab-pane fade" id="nav-comments-user" role="tabpanel" aria-labelledby="nav-comments-tab-user">


<div class="block new-block position-relative mt-3" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Comments</h5>
            </div>
';
            $user_comments = DB::table('user_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.is_deleted', 0)->where('user_id', $q->id)->get();

            if (sizeof($user_comments) > 0) {
                $html .= '<div class="col-sm-12"><button type="button" data-toggle="modal" data-target="#CommentModal" class="btn font-titillium fw-500 py-1 px-3 ml-3 new-ok-btn mb-3" style="width: fit-content;">Add Comment</button></div>';
                foreach ($user_comments as $c) {
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
<a class="float-right edit-comment-client mr-2" data-id="' . $c->id . '" data-user-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
<i class="fa-thin fa-pen text-darkgrey fs-18"></i>
</a> 
<a class="float-right delete-comment-user" data-id="' . $c->id . '" data-user-id="' . $id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
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
  <div class="tab-pane fade" id="nav-attachments-user" role="tabpanel" aria-labelledby="nav-attachments-tab-user">


<div class="block new-block position-relative mt-3" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Attachments</h5>
            </div>
';

            $user_attachments = DB::table('user_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.is_deleted', 0)->where('user_id', $q->id)->get();

            if (sizeof($user_attachments) > 0) {
                $html .= '<div class="col-sm-12"><button type="button" data-toggle="modal" data-target="#AttachmentModal" class="btn font-titillium fw-500 py-1 px-3 ml-3 new-ok-btn mb-3" style="width: fit-content;">Add Attachemnt</button></div>';
                foreach ($user_attachments as $c) {

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
                                    <a class="float-right delete-attachment" data-id="' . $c->id . '" data-user-id="' . $id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
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
  <div class="tab-pane fade" id="nav-audit-user" role="tabpanel" aria-labelledby="nav-audit-tab-user">


  
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

            $user_audit = DB::table('user_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.user_id', $q->id)->get();
            if (sizeof($user_audit) > 0) {
                foreach ($user_audit as $c) {
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

       </div>';
            $iconHtml = '';
            if (@Auth::user()->role != 'read') {



                if ($q->portal_access != 1) {
                        $iconHtml .= '<a class="text-white banner-icon btnEnd mr-0" href="javascript:;" data-ended="1" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reinstate User" data="' . $id . '" data-id="' . $id . '">
                        <i class="fa-light fa-arrow-up-to-arc regular-icon"></i>
                        <i class="fa-solid fa-arrow-up-to-arc solid-icon" style="padding-right: 3px; padding-left: 1.5px;"></i></a>';
                    
                } else {

                    $iconHtml .= '<span> 
                                     <a href="javascript:;" class="btnEnd text-white banner-icon ml-0" data-status="' . $q->portal_access . '"  data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="End User" class=" ">
                                     <i class="fa-light fa-circle-chevron-down regular-icon"></i>
                                     <i class="fa-solid fa-circle-chevron-down solid-icon" style="padding-right: 3px; padding-left: 1.5px;"></i>
                                 </span>';
                }
            }

            $editUrl = url('edit-user') . '?id=' . $q->id;
            $cloneUrl = url("add-user/support") . '?id=' . $q->id;
            $pdfUrl = url('pdf-user') . '?id=' . $q->id;
$headerImg = asset('public/img/menu-icon-contracts-white.png');
            // $cards = ``;

            return response()->json([
                // 'cards' => $cards,
                'content' => $html,
                'header_img' => $headerImg,
                // 'header_text' => $headerText,
                // 'header_sub_text' => $result,
                'header_desc' => $q->email,
                'editUrl' => $editUrl,
                'headerText' => $q->firstname .' '. $q->lastname,
                'id' => $q->id,
                'mustChange' => (int)($q->must_change ?? 0),
                'must_change' => (int)($q->must_change ?? 0),
                'cloneUrl' => $cloneUrl,
                'pdfUrl' => $pdfUrl,
                'iconHtml' => $iconHtml,
            ]);
        }
    }
    public function AddUser()
    {
        return view('user.add');
    }
    public function editUser()
    {
        $id = request()->get('id');
        if (isset($id)) {
            $user = DB::table('users')->where('id', $id)->first();
            return view('user.edit', compact('user'));
        }
    }




    public function InsertUser(Request $request)
    {
        DB::beginTransaction();

        $check = DB::table('users')->where('is_deleted', 0)->where('email', $request->email)->first();
        if ($check != '') {
            return response()->json(['message' => 'Email Already Exist'], 422);
        }
        $image = '';
        if ($request->profile_image != '') {
            $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $request->file('profile_image')->move(public_path('client_logos'), $image);
        }

        $password = uniqid();

        try {
            DB::commit();
            // Insert item category
            $UserId = DB::table('users')->insertGetId([
                'role' => $request->role,
                'name' => $password,
                'firstname' => $request->first_name,
                'lastname' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'portal_access' => 1,
                'user_image' => $image,
                'must_change' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add to audit trail
            DB::table('user_audit_trail')->insert([
                'user_id' => $UserId,
                'description' => 'User added successfully',
                'client_id' => $UserId,
                'created_at' => now()
            ]);

            
            $data2 = array(
                'email' => $request->email,
                'password' => $password,
                'name' => $request->firstname . ' ' . $request->lastname,
                'subject' => 'Password Reset Notification'
            );
            try {
                Mail::send('emails.password', ["data" => $data2], function ($message) use ($data2) {
                    $message->to($data2['email'])
                        ->subject('Create Your Password');
                });
            } catch (\Throwable $th) {
                //throw $th;
            }
            return response()->json([
                'message' => 'New user added and notified by e-mail'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create user. Please try again.'. $e
            ], 500);
        }
    }
    public function updateUser(Request $request)
    {

        try {
            DB::beginTransaction();

            $check = DB::table('users')->where('is_deleted', 0)->where('email', $request->email)->where('id', '!=', $request->edit_form_id)->first();

            if ($check != '') {
                return response()->json(['message' => 'Email Already Exist'], 422);
            }

            $check_img = DB::table('users')->where('id', $request->edit_form_id)->where('is_deleted', 0)->first();

            $image = $check_img->user_image;
            if ($request->profile_image != '') {
                $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('profile_image')->getClientOriginalExtension();
                $request->file('profile_image')->move(public_path('client_logos'), $image);
            }

            // 1. Soft delete all existing modules
            DB::table('user_modules')
                ->where('user_id', $request->edit_form_id)
                ->update([
                    'is_deleted' => 1
                ]);

            DB::table('users')
                ->where('id', $request->edit_form_id)
                ->where('is_deleted', 0)
                ->update([
                    'firstname' => $request->first_name,
                    'lastname' => $request->last_name,
                    'email' => $request->email,
                    'user_image' => $image,
                    'updated_at' => now(),
                    'updated_by' => Auth::id()
                ]);

            $user_id = $request->edit_form_id;

            DB::table('user_audit_trail')->insert(['user_id' => $request->edit_form_id, 'description' => 'User updated successfully']);

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resetUserPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#^(){}\[\]<>~+=|\/.,:;\'"-]/',
                'confirmed',
            ],
            'must_change' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = DB::table('users')
            ->where('id', $request->id)
            ->where('is_deleted', 0)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        $mustChange = $request->boolean('must_change');

        try {
            DB::beginTransaction();

            DB::table('users')
                ->where('id', $request->id)
                ->update([
                    'password' => Hash::make($request->password),
                    'must_change' => $mustChange ? 1 : 0,
                    'password_verified' => $mustChange ? null : now(),
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            DB::table('user_audit_trail')->insert([
                'user_id' => $request->id,
                'description' => 'Password reset for ' . $user->email,
                'client_id' => $request->id,
                'created_at' => now(),
            ]);

            DB::commit();

            try {
                $data2 = [
                    'email' => $user->email,
                    'password' => $request->password,
                    'name' => trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')),
                    'subject' => 'Password Reset Notification'
                ];

                Mail::send('emails.password', ["data" => $data2], function ($message) use ($data2) {
                    $message->to($data2['email'])
                        ->subject('Create Your Password');
                });
            } catch (\Throwable $th) {
            }

            return response()->json([
                'message' => 'Password updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to reset password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resendUserInvite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = DB::table('users')
            ->where('id', $request->id)
            ->where('is_deleted', 0)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        if ((int)($user->must_change ?? 0) !== 1) {
            return response()->json([
                'message' => 'Resend invite is available only for invited users.'
            ], 422);
        }

        $tempPassword = Str::random(10);

        try {
            DB::beginTransaction();

            DB::table('users')
                ->where('id', $request->id)
                ->update([
                    'password' => Hash::make($tempPassword),
                    'must_change' => 1,
                    'password_verified' => null,
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            DB::table('user_audit_trail')->insert([
                'user_id' => $request->id,
                'description' => 'Invitation to access Kumon CSMS Portal sent to ' . $user->email,
                'client_id' => $request->id,
                'created_at' => now(),
            ]);

            DB::commit();

            try {
                $data2 = [
                    'email' => $user->email,
                    'password' => $tempPassword,
                    'name' => trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')),
                    'subject' => 'Password Reset Notification'
                ];

                Mail::send('emails.password', ["data" => $data2], function ($message) use ($data2) {
                    $message->to($data2['email'])
                        ->subject('Create Your Password');
                });
            } catch (\Throwable $th) {
            }

            return response()->json([
                'message' => 'Successfully sent email notification',
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to resend invite.',
                'error' => $e->getMessage()
            ], 500);
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

    public function InsertCommentUser(Request $request)
    {
        DB::table('user_comments')->insert([
            'user_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        return redirect()->back()->with('success-comment', 'Comment Added Successfully');
    }
    public function InsertAttachmentUser(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {
                copy(public_path('temp_uploads/' . $a), public_path('network_attachment/' . $a));
                DB::table('user_attachments')->insert([
                    'user_id' => $request->id,
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

    public function UndoDeleteCommentUser(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $comment_id = $request->comment_id;

            // if (Auth::user()->role == 'read') {
            //     return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            // }

            DB::table('user_comments')
                ->where('id', $comment_id)
                ->where('is_deleted', 1)
                ->where('user_id', $user_id)
                ->update([
                    'is_deleted' => 0
                ]);

            DB::table('user_audit_trail')->insert([
                'user_id' => $user_id,
                'description' => 'Comment Recovered | ' . $comment_id
            ]);

            return response()->json([
                'status' => 'success',
                'comment_id' => $comment_id,
                'user_id' => $user_id,
                'message' => 'Comment recovered successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function delete_attachment_user(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $attachment_id = $request->attachment_id;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('user_attachments')
                ->where('id', $attachment_id)
                ->where('is_deleted', 0)
                ->where('user_id', $user_id)
                ->update([
                    'is_deleted' => 1
                ]);

            DB::table('user_audit_trail')->insert([
                'user_id' => $user_id,
                'description' => 'Attachment Deleted | ' . $attachment_id
            ]);

            return response()->json([
                'status' => 'success',
                'attachment_id' => $attachment_id,
                'user_id' => $user_id,
                'message' => 'Attachment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function undo_delete_attachment_user(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $attachment_id = $request->attachment_id;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('user_attachments')
                ->where('id', $attachment_id)
                ->where('is_deleted', 1)
                ->where('user_id', $user_id)
                ->update([
                    'is_deleted' => 0
                ]);

            DB::table('user_audit_trail')->insert([
                // 'user_id' => Auth::id(),
                'description' => 'Attachment Recovered | ' . $attachment_id,
                'user_id' => $user_id,
            ]);

            return response()->json([
                'status' => 'success',
                'attachment_id' => $attachment_id,
                'user_id' => $user_id,
                'message' => 'Attachment recovered successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function UpdateCommentUser(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $comment_id = $request->comment_id;
            $comment = $request->comment_text;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('user_comments')
                ->where('id', $comment_id)
                ->where('is_deleted', 0)
                ->where('user_id', $user_id)
                ->update([
                    'comment' => $comment,
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            DB::table('user_audit_trail')->insert([
                // 'user_id' => Auth::id(),
                'description' => 'Comment Updated | ' . $comment_id,
                'user_id' => $user_id,
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

    public function DeleteCommentUser(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $comment_id = $request->comment_id;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('user_comments')
                ->where('id', $comment_id)
                ->where('is_deleted', 0)
                ->where('user_id', $user_id)
                ->update([
                    'is_deleted' => 1
                ]);

            DB::table('user_audit_trail')->insert([
                // 'user_id' => Auth::id(),
                'description' => 'Comment Deleted | ' . $comment_id,
                'user_id' => $user_id,
            ]);

            return response()->json([
                'status' => 'success',
                'comment_id' => $comment_id,
                'user_id' => $user_id,
                'message' => 'Comment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function EndUser(Request $request)
    {
        // $user = DB::table('users')->where('id', $request->id)
        //     ->first();
        // $newStatus = $request->end == 1 ? 'Active' : 'Inactive';
        // if ($user) {
        //     try {
        //         Mail::to('support@amaltitek.com')->send(new \App\Mail\StatusChangeMail(
        //             $user,
        //             $newStatus
        //         ));
        //     } catch (\Throwable $th) {
        //         //throw $th;
        //     }
        // }
        if ($request->end == 1) {
            DB::Table('users')->where('id', $request->id)->update(['portal_access' => '1']);
            DB::table('user_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'comment' => 'User Reactivated.<br>' . $request->reason]);
            DB::table('user_audit_trail')->insert(['user_id' => $request->id, 'description' => 'User successfully Reactivated.', 'client_id' => $request->id]);
            return redirect()->back()->with('success', 'User Reactivated');
        } else {
            DB::Table('users')->where('id', $request->id)->update(['portal_access' => '0']);
            DB::table('user_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'comment' => 'User successfully Deactivated.<br>' . $request->reason]);
            DB::table('user_audit_trail')->insert(['user_id' => $request->id, 'description' => 'User successfully Deactivated.', 'client_id' => $request->id]);
            return redirect()->back()->with('success', 'User Deactivated Successfully');
        }
    }

    public function DeleteUser(Request $request)
    {
        try {
            $id = $request->id;

            $qry = DB::table('users')->where('id', $id)->first();

            if (!$qry) {
                return response()->json(['status' => 'error', 'message' => 'User not found.']);
            }

            // Soft delete related data
            $timestamp = date('Y-m-d H:i:s');

            DB::table('users')
                ->where('id', $id)
                ->update(['is_deleted' => 1, 'deleted_at' => $timestamp]);

            // Add audit trail
            DB::table('user_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'User Deleted | ' . $id,
                'user_id' => $id,
                'created_at' => $timestamp,
            ]);

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
    public function DeleteuserUndo(Request $request)
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

            // Add audit trail
            DB::table('user_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'User Recovered | ' . $id,
                'user_id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

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
}
