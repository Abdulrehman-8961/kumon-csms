<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Hash;
use PDF;
use App\Imports\AssetImport;
use App\Imports\AssetDetailLinesImport;
use App\Imports\AssetWorkstationImport;
use Excel;
use Illuminate\Support\Facades\Storage;

use App\Exports\ExportExcelPhysical;
use App\Exports\ExportVirtualAssets;
use App\Exports\ExportAssets;
use App\Exports\ExportAssetsDetaiLine;
use App\Mail\AssetNotificationMail;
use DateTime;
use Validator;

class AssetsController extends Controller
{
    //
    public function __construct() {}




    public function getSiteByClientId(Request $request)
    {

        $qry = DB::Table('sites')->where('is_deleted', 0)->where('client_id', $request->id)->orderby('site_name', 'asc')->get();
        return response()->json($qry);
    }
    public function getAssetsList(Request $request)
    {
        $type = $request->input('assetType');
        $qry = DB::Table('assets')->where('is_deleted', 0)->where('asset_type', $type)->orderby('hostname', 'asc')->get();
        return response()->json($qry);
    }

    public function getDomainByClientId(Request $request)
    {


        $qry = DB::Table('domains')->where('is_deleted', 0)->where('client_id', $request->id)->get();
        return response()->json($qry);
    }


    public function getSystemTypeByClientId(Request $request)
    {


        $qry = DB::Table('system_types')->where('is_deleted', 0)->where('client_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getAssetTypeList(Request $request)
    {


        $qry = DB::Table('asset_type')->where('is_deleted', 0)->orderBy('asset_type_description', 'asc')->get();
        return response()->json($qry);
    }


    public function getSystemCategoryByClientId(Request $request)
    {


        $qry = DB::Table('system_category')->where('is_deleted', 0)->where('client_id', $request->id)->get();
        return response()->json($qry);
    }


    public function showAssetIp(Request $request)
    {


        $qry = DB::Table('asset_ip_addresses')->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }


    public function Virtual($page_type = '')
    {

        return view('virtual', ['page_type' => $page_type]);
    }
    public function Assets($type)
    {



        return view('Assets', compact('type'));
    }
    public function workstation()
    {
        return view('AssetsWorkstation');
    }
    public function addWorkstation()
    {
        return view('AddAssetsWorkstation');
    }
    public function mobileDevice()
    {
        return view('AssetsMobile');
    }
    public function addMobileDevice()
    {
        return view('AddAssetsMobile');
    }



    public function Physical($page_type = '')
    {
        return view('Physical', ['page_type' => $page_type]);
    }

    public function Insertchecklist(Request $request)
    {
        if ($request->sub_type == 'Workstation') {
            $asset_field = 'workstation_asset_type';
            $asset = $request->asset_type_2;
        } else {
            $asset_field = 'asset_id';
            $asset = $request->asset_type;
        }
        $data = array(
            'client_id' => $request->client_id,
            'sub_type' => $request->sub_type,
            'device_type' => $request->sub_type == 'Device' ? $request->device_type : '',
            'checklist_status' => $request->checklist_type,
            $asset_field => $asset,
            // 'operating_system_id' => $request->operating_system,
            'platform' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ?  $request->platform : '',
            'created_by' => Auth::id(),
        );

        DB::Table('checklist')->insert($data);
        $last_id = DB::getPdo()->lastInsertId();


        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('client_attachment/' . $a->attachment));
                DB::table('checklist_attachments')->insert([
                    'checklist_id' => $last_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('checklist_comments')->insert([
                    'checklist_id' => $last_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }
        $taskDetailsArray = $request->taskDetailsArray;
        if (isset($request->taskDetailsArray)) {
            foreach ($taskDetailsArray as $a) {
                $a = json_decode($a);
                DB::table('checklist_tasks')->insert([
                    'checklist_id' => $last_id,
                    'responsible' => $a->responsible,
                    'description' => $a->task_description,
                    'details' => $a->task_details,
                    'added_by' => Auth::id(),
                ]);
                $last_task_id = DB::getPdo()->lastInsertId();
                if (isset($a->images) && is_array($a->images)) {
                    foreach ($a->images as $image) {
                        DB::table('task_screenshots')->insert([
                            'checklist_id' => $last_id,
                            'task_id' => $last_task_id,
                            'path' => $image->path,
                            'name' => $image->name,
                            'added_by' => Auth::id(),
                        ]);
                    }
                }
            }
        }
        return response()->json('success');
    }
    public function Updatechecklist(Request $request)
    {
        $update_id = $request->update_id;
        if ($request->sub_type == 'Workstation') {
            $asset_field = 'workstation_asset_type';
            $asset = $request->asset_type_2;
        } else {
            $asset_field = 'asset_id';
            $asset = $request->asset_type;
        }
        $data = array(
            'client_id' => $request->client_id,
            'sub_type' => $request->sub_type,
            'device_type' => $request->sub_type == 'Device' ? $request->device_type : '',
            'checklist_status' => $request->checklist_type,
            $asset_field => $asset,
            // 'operating_system_id' => $request->operating_system,
            'platform' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ?  $request->platform : '',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id(),
        );

        DB::Table('checklist')->where('id', $update_id)->update($data);
        $last_id = DB::getPdo()->lastInsertId();


        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            DB::table('checklist_attachments')->where('checklist_id', $update_id)->delete();
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('client_attachment/' . $a->attachment));
                DB::table('checklist_attachments')->insert([
                    'checklist_id' => $update_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            DB::table('checklist_comments')->where('checklist_id', $update_id)->delete();
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('checklist_comments')->insert([
                    'checklist_id' => $update_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }
        $taskDetailsArray = $request->taskDetailsArray;
        if (isset($request->taskDetailsArray)) {
            DB::table('checklist_tasks')->where('checklist_id', $update_id)->delete();
            foreach ($taskDetailsArray as $a) {
                $a = json_decode($a);
                DB::table('checklist_tasks')->insert([
                    'task_id' => $a->task_id,
                    'checklist_id' => $update_id,
                    'responsible' => @$a->responsible,
                    'description' => @$a->task_description,
                    'details' => @$a->task_details,
                    'added_by' => Auth::id(),
                ]);
                $last_task_id = DB::getPdo()->lastInsertId();
                if (isset($a->images) && is_array($a->images)) {
                    foreach ($a->images as $image) {
                        DB::table('task_screenshots')->insert([
                            'checklist_id' => $last_id,
                            'task_id' => $last_task_id,
                            'path' => $image->path,
                            'name' => $image->name,
                            'added_by' => Auth::id(),
                        ]);
                    }
                }
            }
        }
        return response()->json('success');
    }

    public function getCommentsChecklist(Request $request)
    {
        $qry = DB::table('checklist_comments as c')
            ->select('c.*', 'u.user_image')
            ->join('users as u', function ($j) {
                $j->on('u.id', '=', 'c.added_by');
            })
            ->where('c.checklist_id', $request->id)->get();
        return response()->json($qry);
    }

    public function getAttachmentChecklist(Request $request)
    {
        $qry = DB::table('checklist_attachments as c')
            ->select('c.*', 'u.user_image')
            ->join('users as u', function ($j) {
                $j->on('u.id', '=', 'c.added_by');
            })
            ->where('c.checklist_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getTaskChecklist(Request $request)
    {
        $qry = DB::table('checklist_tasks as c')
            ->select(
                'c.*',
                'u.user_image',
                DB::raw('CONCAT(u.firstname, " ", u.lastname) as username'),
                DB::raw('GROUP_CONCAT(CONCAT(ts.path, "/", ts.name) SEPARATOR ",") as screenshots')
            )
            ->join('users as u', 'u.id', '=', 'c.added_by')
            ->leftJoin('task_screenshots as ts', 'ts.task_id', '=', 'c.id') // Adjust join condition
            ->where('c.checklist_id', $request->id)
            ->groupBy('c.id') // Group by primary key
            ->get();
        return response()->json($qry);
    }
    public function getTaskContent(Request $request)
    {
        $qry = DB::table('checklist_tasks as c')
            ->select(
                'c.*',
                'u.user_image',
                DB::raw('CONCAT(u.firstname, " ", u.lastname) as username'),
                DB::raw('GROUP_CONCAT(CONCAT(ts.path, "/", ts.name) SEPARATOR ",") as screenshots')
            )
            ->join('users as u', 'u.id', '=', 'c.added_by')
            ->leftJoin('task_screenshots as ts', 'ts.task_id', '=', 'c.id')
            ->where('c.id', $request->id) // Adjust the where clause to filter by c.id
            ->groupBy('c.id') // Group by the primary key
            ->get();
        return response()->json($qry);
    }

    public function AddAssets($type)
    {

        $systemTypes = DB::table('system_types')
            ->where('is_deleted', 0)
            ->orderBy('domain_name', 'asc')
            ->get();

        $systemCategories = DB::table('system_category')
            ->where('is_deleted', 0)
            ->orderBy('domain_name', 'asc')
            ->get();
        $sla = DB::table('sla')
            ->where('is_deleted', 0)
            ->get();
        return view('AddAssets', [
            'type' => $type,
            'systemTypes' => $systemTypes,
            'systemCategories' => $systemCategories,
            'sla' => $sla
        ]);
    }

    public function EditAssets()
    {

        $systemTypes = DB::table('system_types')
            ->where('is_deleted', 0)
            ->orderBy('domain_name', 'asc')
            ->get();
        // dd($systemTypes);
        $systemCategories = DB::table('system_category')
            ->where('is_deleted', 0)
            ->orderBy('domain_name', 'asc')
            ->get();

        $sla = DB::table('sla')
            ->where('is_deleted', 0)
            ->get();

        return view('EditAssets', [
            'systemTypes' => $systemTypes,
            'systemCategories' => $systemCategories,
            'sla' => $sla
        ]);
    }
    public function editWorkstation()
    {
        return view('editAssetsWorkstation');
    }
    public function editMobileAsset()
    {
        return view('editAssetsMobile');
    }





    public function getAttachmentAssets(Request $request)
    {
        $qry = DB::table('asset_attachments as a')
            ->select('a.*', 'u.user_image')
            ->join('users as u', function ($j) {
                $j->on('u.id', '=', 'a.added_by');
            })
            ->where('a.asset_id', $request->id)->get();
        return response()->json($qry);
    }

    public function getCommentsAssets(Request $request)
    {
        $qry = DB::table('asset_comments as ac')
            ->select('ac.*', 'u.user_image')
            ->join('users as u', function ($join) {
                $join->on('u.id', '=', 'ac.added_by');
            })
            ->where('ac.asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getMaintenanceAssets(Request $request)
    {
        $qry = DB::table('asset_maintenance')->where('is_deleted', 0)->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getPowerAssets(Request $request)
    {
        $qry = DB::table('assets_power_connection')->where('is_deleted', 0)->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getLocationAssets(Request $request)
    {
        $qry = DB::table('assets_location')->where('is_deleted', 0)->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getDrsAssets(Request $request)
    {
        $qry = DB::table('assets_drs_rule')->where('is_deleted', 0)->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getIpDnsAssets(Request $request)
    {
        $qry = DB::table('asset_ip_dns')->where('is_deleted', 0)->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getPortMapAssets(Request $request)
    {
        $qry = DB::table('asset_port_map')->where('is_deleted', 0)->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getNetworkAdapterAssets(Request $request)
    {
        $qry = DB::table('asset_network_adapter')->where('is_deleted', 0)->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getVirtualDiskAssets(Request $request)
    {
        $qry = DB::table('asset_virtual_disks')->where('is_deleted', 0)->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getRaidVolumeAssets(Request $request)
    {
        $qry = DB::table('asset_raid_volume')->where('is_deleted', 0)->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getLogicalVolumeAssets(Request $request)
    {
        $qry = DB::table('asset_logical_volume')->where('is_deleted', 0)->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }
    public function getIpAssets(Request $request)
    {
        $qry = DB::table('asset_ip_addresses')->where('asset_id', $request->id)->get();
        return response()->json($qry);
    }


    public function ExportPrintAsset(Request $request)
    {
        if (!Auth::check()) {
            if (!isset($request->key) || $request->key == '') {
                return view('error')->with(['message' => "Unauthorized Access"]);
            }

            $qry = DB::table('contract_sharable_links')->where('hash', $request->key)->first();

            if ($qry == '') {
                return view('error')->with(['message' => "Invalid Link / Link Expired"]);
            } else {
                $expiry_date = $qry->expiry_date;
                if (date('Y-m-d') > $expiry_date) {
                    return view('error')->with(['message' => "Link Expired"]);
                }
                $contract = DB::table('assets')->where('is_deleted', 0)->where('id', $qry->contract_id)->first();
                if ($contract == '') {

                    return view('error')->with(['message' => "Asset Not Found"]);
                }
            }


            return view('exports/ExportPrintSSL', ['id' => $qry->contract_id]);
        } else {

            if ($request->key != '') {
                $qry = DB::table('contract_sharable_links')->where('hash', $request->key)->first();

                if ($qry == '') {

                    return view('error')->with(['message' => "Invalid Link / Link Expired"]);
                } else {
                    $expiry_date = $qry->expiry_date;
                    if (date('Y-m-d') > $expiry_date) {
                        return view('error')->with(['message' => "Link Expired"]);
                    }
                    $contract = DB::table('assets')->where('is_deleted', 0)->where('id', $qry->contract_id)->first();
                    if ($contract == '') {

                        return view('error')->with(['message' => "Asset Not Found"]);
                    }
                }
            } else {
                $qry = DB::table('assets')->where('is_deleted', 0)->where('id', $request->id)->first();
            }

            return view('exports/ExportPrintAsset', ['id' => $qry->id]);
        }
        return view('exports/ExportPrintAsset');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('checklist_upload'), $imageName);
            $imagePath = asset('public/checklist_upload/' . $imageName);
            return response()->json(['success' => true, 'image_path' => $imagePath, 'image_name' => $imageName]);
        } else {
            return response()->json(['success' => false, 'error' => 'No image file found']);
        }
    }


    public function EndContract(Request $request)
    {
        $contract = DB::table('contracts as c')->where('c.id', $request->id)
            ->select('c.*', 'v.vendor_name as vendor_name', 'cl.company_name as client_name', 's.site_name as site_name')
            ->leftJoin('vendors as v', function ($join) {
                $join->on('c.vendor_id', '=', 'v.id');
            })
            ->leftJoin('clients as cl', function ($join) {
                $join->on('c.client_id', '=', 'c.id');
            })
            ->leftJoin('sites as s', function ($join) {
                $join->on('c.site_id', '=', 's.id');
            })
            ->first();
        $newStatus = $request->end == 1 ? 'Active' : 'Expired/Ended';
        if ($contract) {
            try {
                Mail::to('support@amaltitek.com')->send(new \App\Mail\StatusChangeMail(
                    $contract,
                    $newStatus
                ));
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        if ($request->end == 1) {
            DB::table('pinned_messages')->where(['linked_id' => $request->id,'page' => 'contract','status' => 'ended'])->delete();
            DB::Table('contracts')->where('id', $request->id)->update(['contract_status' => 'Active']);
            DB::table('contract_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'contract_id' => $request->id, 'comment' => 'Contract successfully Reninstated.<br>' . $request->reason]);

            DB::table('contract_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Contract successfully Reinstated.', 'contract_id' => $request->id]);
            return redirect()->back()->with('success-status', 'Active');
        } else {
            $msg = "This contract was ended by ". ucfirst(Auth::user()->firstname) ." ". ucfirst(Auth::user()->lastname) ." on " . date('d-M-Y g:i a', strtotime(now()));

            DB::table('pinned_messages')->insert([
                'message' => $msg,
                'linked_id' => $request->id,
                'page' => 'contract',
                'added_by' => Auth::user()->id,
                'is_deleteable' => 0,
                'status' => 'ended',
            ]);
            DB::Table('contracts')->where('id', $request->id)->update(['contract_status' => 'Expired/Ended', 'ended_reason' => $request->reason, 'ended_by' => Auth::id(), 'ended_on' => date('Y-m-d H:i:s')]);
            DB::table('contract_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'contract_id' => $request->id, 'comment' => 'Contract successfully Ended.<br>' . $request->reason]);

            DB::table('contract_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Contract successfully Ended.', 'contract_id' => $request->id]);
            return redirect()->back()->with('success-status', 'Expired/Ended');
        }
    }

    public function DecommisionAsset(Request $request)
    {
        $qry = DB::Table('assets')->where('id', $request->id)->first();
        $status = 0;
        if ($qry->AssetStatus == 1) {
            $detail = 'Asset successfully decommissioned.';
            $status = 0;
            $InactiveDate = date('Y-m-d');
        } else {
            $detail = 'Asset successfully Re-activated.';
            $status = 1;
            $InactiveDate = '';
        }
        DB::Table('assets')->where('id', $request->id)->update(['AssetStatus' => $status, 'InactiveDate' => $InactiveDate, 'InactiveBy' => Auth::id()]);
        DB::table('asset_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'asset_id' => $request->id, 'comment' => $detail . '<br>' . $request->reason]);

        DB::table('asset_audit_trail')->insert(['user_id' => Auth::id(), 'description' => $detail, 'asset_id' => $request->id]);


        return redirect()->back()->with('success', $detail);
    }

    public function UploadAssetCsv(Request $request)
    {
        $import = new AssetImport($request->page_type);
        Excel::import($import, $request->file('file')->store('temp'));

        return redirect()->back()->with('response', $import->data);
    }
    public function UploadAssetDetailLineCsv(Request $request)
    {
        $import = new AssetDetailLinesImport($request->detail_line, $request->pageType);
        Excel::import($import, $request->file('file')->store('temp'));

        return redirect()->back()->with('success', str_replace("\n", "<br>", $import->data));
    }

    public function ExportPdfAsset()
    {


        $pdf = PDF::loadView('exports/ExportPdfAsset');

        return $pdf->stream('Assets.pdf');
    }


    public function showContractAsset(Request $request)
    {

        $contract = DB::table('contract_assets as a')->select('c.contract_status', 'c.contract_type', 'm.vendor_name', 'c.contract_no', 'c.contract_end_date', 'c.contract_description')->join('contracts as c', 'c.id', '=', 'a.contract_id')->leftjoin('vendors as m', 'm.id', '=', 'c.vendor_id')->where('a.hostname', $request->id)->where('a.is_deleted', 0)->where('ca.is_deleted', 0)->get();

        return response()->json($contract);
    }

    public function showSSLAsset(Request $request)
    {

        $contract = DB::table('ssl_certificate as c')->select('c.cert_status', 'c.cert_type', 'm.vendor_name', 'c.cert_edate', 'c.cert_name')->leftjoin('vendors as m', 'm.id', '=', 'c.cert_issuer')->whereRaw('FIND_IN_SET(?, c.cert_hostname)', [$request->id])->where('c.is_deleted', 0)->get();

        return response()->json($contract);
    }











    public function DeleteVirtualAssets(Request $request)
    {

        $qry = DB::table('assets')->where('id', $request->id)->first();

        $userAccess = explode(',', Auth::user()->access_to_client);
        if (Auth::user()->role != 'admin') {

            if (!in_array($qry->client_id, $userAccess)) {
                echo "You dont have access";
                exit;
            }
        }
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }


        DB::Table('assets')->where('id', $request->id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s'), 'position' => null]);
        DB::table('assets')->where('position', '>', $qry->position)->where('asset_type', 'virtual')->decrement('position');

        return redirect()->back()->with('success', 'Virtual Asset Deleted Successfully');
    }


    public function DeletePhysicalAssets(Request $request)
    {

        $qry = DB::table('assets')->where('id', $request->id)->first();
        $userAccess = explode(',', Auth::user()->access_to_client);
        if (Auth::user()->role != 'admin') {

            if (!in_array($qry->client_id, $userAccess)) {
                echo "You dont have access";
                exit;
            }
        }
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }

        DB::Table('assets')->where('id', $request->id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s'), 'position' => null]);
        DB::table('assets')->where('position', '>', $qry->position)->where('asset_type', 'physical')->decrement('position');

        // return redirect()->back()->with('success', 'Physical Asset Deleted Successfully');
        return redirect()->back()->with('success', 'Asset Deleted Successfully  <a href="javascript:;" class="  btn-notify btnUndo ml-4" data-id=' . $request->id . ' data-position="' . $qry->position . '">Undo</a>');
    }
    public function DeletePhysicalAssetsUndo(Request $request)
    {

        $qry = DB::table('assets')->where('id', $request->id)->first();
        $userAccess = explode(',', Auth::user()->access_to_client);
        if (Auth::user()->role != 'admin') {

            if (!in_array($qry->client_id, $userAccess)) {
                echo "You dont have access";
                exit;
            }
        }
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }

        DB::Table('assets')->where('id', $request->id)->update(['is_deleted' => 0, 'deleted_at' => null, 'position' => $request->position]);
        DB::table('assets')->where('position', '>', $request->position)->where('asset_type', 'physical')->increment('position');

        return redirect()->back()->with('success', 'Asset Restored Successfully');
    }


    public function ShowAssets(Request $request)
    {


        $qry = DB::table('assets as a')->select('a.*', 's.site_name', 'd.domain_name', 'c.client_display_name', 'o.operating_system_name', 'm.vendor_name', 'at.asset_icon', 'at.asset_type_description', 'at.asset_type_description as asset_type_name', 'n.vlan_id as vlanId', 'usr.firstname as created_firstname', 'usr.lastname as created_lastname', 'upd.firstname as updated_firstname', 'upd.lastname as updated_lastname', 'c.logo')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->leftjoin('network as n', 'a.vlan_id', '=', 'n.id')->leftjoin('users as usr', 'usr.id', '=', 'a.created_by')->leftjoin('users as upd', 'upd.id', '=', 'a.updated_by')->where('a.id', $request->id)->first();
        return response()->json($qry);
    }

    public function getParentAsset(Request $request)
    {


        $asset = DB::Table('assets as a')->leftjoin('asset_type as t', 'a.asset_type_id', '=', 't.asset_type_id')->where('t.asset_type_description', 'Storage Controller')->where('a.is_deleted', 0)->where('a.client_id', $request->id)->orderBy('a.hostname', 'asc')->get();


        return response()->json($asset);
    }
    public function ExportExcelPhysical(Request $request)
    {
        return Excel::download(new ExportExcelPhysical($request), 'PhysicalAsset.xlsx');
    }
    public function ExportExcelAssets(Request $request)
    {
        return Excel::download(new ExportAssets($request), 'Asset.xlsx');
    }
    public function ExportExcelAssetsDetailLine(Request $request)
    {
        return Excel::download(new ExportAssetsDetaiLine($request), 'Asset.xlsx');
    }

    public function exportExcelVirtual(Request $request)
    {
        return Excel::download(new ExportVirtualAssets($request), 'VirtualAsset.xlsx');
    }
    public function PrintVirtual()
    {


        return view('exports/PrintVirtual');
    }
    public function PrintPhysical()
    {


        return view('exports/PrintPhysical');
    }

    public function getVendorOfPhysical(Request $request)
    {
        $client_id = $request->client_id;
        $site_id = $request->site_id;

        if ($site_id != '' && @$site_id[0] != '') {

            $qry = DB::Table('vendors as v')->select('v.*')->join('assets as c', 'c.manufacturer', '=', 'v.id')->where('c.client_id', $client_id)->whereIn('c.site_id', $site_id)->groupBy('c.manufacturer')->orderby('v.vendor_name', 'asc')->get();
        } else {
            $qry = DB::Table('vendors as v')->select('v.*')->join('assets as c', 'c.manufacturer', '=', 'v.id')->where('client_id', $client_id)->groupBy('c.manufacturer')->orderby('v.vendor_name', 'asc')->get();
        }
        return response()->json($qry);
    }


    public function SwapVirtualRows(Request $request)
    {
        $qry = '';
        $sno = 0;
        $page = 1;
        $limit = $request->limit != '' ? $request->limit : 10;

        if ($request->page == '') {
            $offset = 0;
        } else {
            $offset = ($request->page * $limit) - $limit;
        }

        // $qry=DB::table('assets')->where('asset_type','Virtual')->where('is_deleted',0)->orderBy('position','asc')->offset($offset)->limit(20)->get();

        foreach ($request->id as $key => $q) {
            $sno++;


            DB::table('assets')->where('id', $q)->where('is_deleted', 0)->where('asset_type', 'virtual')->update(['position' => ($key + 1) + $offset]);
        }
        return response()->json('success');
    }


    public function SwapPhysicalRows(Request $request)
    {
        $qry = '';
        $sno = 0;
        $page = 1;
        $limit = $request->limit != '' ? $request->limit : 10;

        if ($request->page == '') {
            $offset = 0;
        } else {
            $offset = ($request->page * $limit) - $limit;
        }

        // $qry=DB::table('assets')->where('asset_type','Virtual')->where('is_deleted',0)->orderBy('position','asc')->offset($offset)->limit(20)->get();

        foreach ($request->id as $key => $q) {
            $sno++;


            DB::table('assets')->where('id', $q)->where('is_deleted', 0)->where('asset_type', 'physical')->update(['position' => ($key + 1) + $offset]);
        }
        return response()->json('success');
    }








    public function InsertAssets(Request $request)
    {
        $position = DB::Table('assets')->where('asset_type', $request->asset_type)->max('position');
        if ($request->HasWarranty == 1) {
            $warranty_status = 'Unassigned';
        } else {
            $warranty_status = 'N/A';
        }



        if ($request->ntp == 1) {
            $ssl_status = 'Unassigned';
        } else {
            $ssl_status = 'N/A';
        }







        $app_owner = $request->app_owner;
        $sla = $request->sla;
        $internet_facing = $request->internet_facing ?? 0;
        $clustered = $request->clustered ?? 0;
        $disaster_recovery = $request->disaster_recovery ?? 0;
        $monitored = $request->monitored ?? 0;
        $load_balancing = $request->load_balancing ?? 0;
        $patched = $request->patched ?? 0;
        $antivirus = $request->antivirus ?? 0;
        $smtp = $request->smtp ?? 0;
        $replicated = $request->replicated ?? 0;
        $ntp = $request->ntp ?? 0;
        $backup = $request->backup ?? 0;
        $syslog = $request->syslog ?? 0;




        if ($request->managed != 1) {
            $app_owner = 'N/A';
            $sla = 'N/A';

            $disaster_recovery = 2;
            $monitored = 2;

            $patched = 2;
            $antivirus = 2;
            $smtp = 2;
            $replicated = 2;

            $backup = 2;
            $syslog = 2;
        }






        $domain = DB::table('domains')->where('id', $request->domain)->first();

        $systemTypeName = '';
        if (isset($request->system_type)) {
            $systemType = DB::table('system_types')->where('id', $request->system_type)->first();
            $systemTypeName = $systemType->domain_name;
        }

        $data = array(
            'asset_type' => @$request->asset_type,
            'client_id' => @$request->client_id,
            'site_id' => @$request->site_id,
            'platform' => @$request->platform,
            'warranty_status' => 'Inactive',
            'warranty_end_date' => 'No Contract Found',
            'hostname' => @$request->hostname,
            'domain' => @$request->domain,
            'system_type' => @$request->system_type,
            'system_category' => @$request->system_category,
            'device_info_ad_domain' => @$request->device_info_addomain,
            'device_info_ou' => @$request->device_info_ou,
            'fqdn' => @$request->hostname . '.' . @$domain->domain_name,
            'role' => @$request->role,
            'SupportStatus' => $warranty_status,
            'use_' => $systemTypeName,
            'os' => @$request->os,
            'HasWarranty' => @$request->HasWarranty,
            'AssetStatus' => 1,
            'disaster_recovery1' => @$request->disaster_recovery1,
            'app_owner' => $app_owner,
            'ip_address' => @$request->ip_address,
            'vlan_id' => @$request->vlan_id,
            'network_zone' => @$request->network_zone,
            'internet_facing' => $internet_facing,
            'disaster_recovery' => $disaster_recovery,
            'load_balancing' => $load_balancing,
            'clustered' => $clustered,
            'managed' => @$request->managed == 1 ? 1 : 0,
            'monitored' => $monitored,
            'patched' => $patched,
            'antivirus' => $antivirus,
            'backup' => $backup,
            'replicated' => $replicated,
            'edr' => $request->edr,
            'twofa' => $request->twofa,
            'smtp' => $smtp,
            'ntp' => $ntp,
            'syslog' => $syslog,
            'sla' => $sla,
            'vcpu' => @$request->vcpu,
            'memory' => @$request->memory,
            'comments' => @$request->comments,
            'location' => @$request->location,
            'manufacturer' => @$request->manufacturer,
            'model' => @$request->model,
            'type' => @$request->type,
            'sn' => @$request->sn,
            'asset_tag' => @$request->asset_tag,
            'parent_asset' => @$request->parent_asset,
            'ssl_certificate_status' => $ssl_status,
            'cpu_model' => @$request->cpu_model,
            'cpu_sockets' => @$request->cpu_sockets,
            'cpu_cores' => @$request->cpu_cores,
            'cpu_freq' => @$request->cpu_freq,
            'cpu_hyperthreadings' => @$request->cpu_hyperthreadings,
            'cpu_total_cores' => @$request->cpu_total_cores,
            'NotSupportedReason' => @$request->NotSupportedReason,
            'asset_type_id' => @$request->asset_type_id,
            'position' => $position + 1,
            'center_server' => @$request->center_server,
            'cluster_host' => @$request->cluster_host,
            'vm_folder' => @$request->vm_folder,
            'vm_datastore' => @$request->vm_datastore,
            'vm_retart_priority' => @$request->vm_retart_priority,
            'end_of_life' => isset($request->end_of_life) ? date('Y-m-d', strtotime($request->end_of_life)) : null,
            'created_by' => Auth::id(),
            'network_connected' => @$request->network_connected,


        );
        // dd($request->all());
        DB::Table('assets')->insert($data);

        $id = DB::getPdo()->lastInsertId();
        $ipArray = @$request->ipArray;
        if (isset($request->ipArray)) {
            foreach ($ipArray as $a) {
                $a = json_decode($a);
                DB::table('asset_ip_addresses')->insert([
                    'asset_id' => $id,
                    'ip_address_value' => $a->ip_address_value,
                    'ip_address_name' => $a->ip_address_name,
                ]);
            }
        }


        $attachment_array = @$request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('asset_attachment/' . $a->attachment));
                DB::table('asset_attachments')->insert([
                    'asset_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = @$request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('asset_comments')->insert([
                    'asset_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }


        $maintenanceArray = @$request->maintenanceArray;
        if (isset($request->maintenanceArray)) {
            foreach ($maintenanceArray as $a) {
                $a = json_decode($a);
                DB::table('asset_maintenance')->insert([
                    'asset_id' => $id,
                    'frequency' => $a->frequency,
                    'day' => $a->day,
                    'occurance' => $a->occurance,
                    'month' => $a->month,
                    'start_time' => $a->start_time,
                    'time_zone' => $a->time_zone,
                    'duration_hours' => $a->duration_hours,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $ipDns_array = @$request->ipDnsArray;
        if (isset($request->ipDnsArray)) {
            foreach ($ipDns_array as $a) {
                $a = json_decode($a);
                DB::table('asset_ip_dns')->insert([
                    'asset_id' => $id,
                    'dns_type' => @$a->type,
                    'alias' => @$a->alias,
                    'vlan_id_no' => @$a->vlan_id,
                    'vlan_id' => @$a->vlan_text,
                    'host_name' => @$a->host_name,
                    'ip_address' => @$a->address,
                    'gateway' => @$a->dns_gateway,
                    'description' => @$a->description,
                    'primary_dns' => @$a->primary_dns,
                    'secondary_dns' => @$a->secondary_dns,
                    'primary_ntp' => @$a->primary_ntp,
                    'secondary_ntp' => @$a->secondary_ntp,
                    'subnet_ip' => @$a->subnet_ip,
                    'mask' => @$a->mask,
                    'gateway_ip' => @$a->gateway,
                    'dhcp_toggle' => @$a->dhcp_toggle,
                    'zone' => @$a->zone,
                    'background' => @$a->background,
                    'color' => @$a->color,
                ]);
            }
        }

        $networkAdapter_array = @$request->networkAdapter;
        if (isset($request->networkAdapter)) {
            foreach ($networkAdapter_array as $a) {
                $a = json_decode($a);
                DB::table('asset_network_adapter')->insert([
                    'asset_id' => $id,
                    'connection_type' => @$a->type,
                    'vmic' => @$a->vmic,
                    'adapter_name' => @$a->adapter_name,
                    'port_group' => @$a->port_group,
                    'adapter_type' => @$a->adapter_type,
                    'slot' => @$a->slot,
                    'port' => @$a->port,
                    'port_media' => @$a->port_media,
                    'mac_address' => @$a->mac_address
                ]);
            }
        }

        $portMap = @$request->portMap;
        if (isset($request->portMap)) {
            foreach ($portMap as $a) {
                $a = json_decode($a);
                DB::table('asset_port_map')->insert([
                    'asset_id' => $id,
                    'mapping_type' => @$a->mappingType,
                    'network_adapter' => @$a->networkAdapter,
                    'media_type' => @$a->mediaType,
                    'switch' => @$a->switch,
                    'port' => @$a->port,
                    'port_mode' => @$a->portMode,
                    'selectedIds' => isset($a->selectedIds) ? (is_array($a->selectedIds) ? implode(', ', @$a->selectedIds) : @$a->selectedIds) : null,
                    'vlan_ids' => isset($a->vlanIds) ? (is_array($a->vlanIds) ? implode(', ', @$a->vlanIds) : @$a->vlanIds) : null,
                    'sub_text' => @$a->sub_text,
                    'comments' => @$a->comments,
                    'ssid' => @$a->ssid,
                    'ssid_text' => @$a->ssid_text,
                ]);
            }
        }

        $virtualDiskArray = @$request->virtualDisk;
        if (isset($request->virtualDisk)) {
            foreach ($virtualDiskArray as $a) {
                $a = json_decode($a);
                DB::table('asset_virtual_disks')->insert([
                    'asset_id' => $id,
                    'vdisk_no' => @$a->vDiskNo,
                    'datastore' => @$a->dataStore,
                    'scsi_id_a' => @$a->scsi_a,
                    'scsi_id_b' => @$a->scsi_b,
                    'device_type' => @$a->drive_type,
                    'drive_size' => @$a->drive_size,
                    'drive_size_unit' => @$a->drive_size_unit
                ]);
            }
        }

        $raidVolumeArray = @$request->raidVolume;
        if (isset($request->raidVolume)) {
            foreach ($raidVolumeArray as $a) {
                $a = json_decode($a);
                DB::table('asset_raid_volume')->insert([
                    'asset_id' => $id,
                    'name' => @$a->volume_name,
                    'volume_description' => @$a->volume_description,
                    'controller' => @$a->controller,
                    'drive_type' => @$a->drive_type,
                    'raid_level' => @$a->raid_level,
                    'no_of_sets' => @$a->no_of_sets,
                    'no_of_drives' => @$a->no_of_drives,
                    'drive_size' => @$a->drive_size,
                    'drive_size_unit' => @$a->drive_size_unit,
                    'volume_size' => @$a->volume_size,
                ]);
            }
        }

        $logicalVolumeArray = @$request->logicalVolume;
        if (isset($request->logicalVolume)) {

            foreach ($logicalVolumeArray as $a) {
                $a = json_decode($a);
                DB::table('asset_logical_volume')->insert([
                    'asset_id' => $id,
                    'source_disk' => @$a->source_disk,
                    'tooltip' => @$a->tooltip,
                    'volume' => @$a->volume,
                    'volume1' => @$a->volume1,
                    'volume_name' => @$a->volume_name,
                    'size' => @$a->size,
                    'size_unit' => @$a->size_unit,
                    'format' => @$a->format,
                    'block_size' => @$a->block_size
                ]);
            }
        }
        $locationArray = @$request->locationArray;
        if (isset($request->locationArray)) {
            foreach ($locationArray as $a) {
                $a = json_decode($a);
                DB::table('assets_location')->insert([
                    'asset_id' => $id,
                    'contact_name' => @$a->contact_name,
                    'floor' => @$a->floor,
                    'cabinet_rack' => @$a->cabinet_rack,
                    'u' => @$a->u,
                    'u_size' => @$a->u_size,
                    'racked' => @$a->racked,
                    'u_location' => @$a->u_location,
                    'room' => @$a->room
                ]);
            }
        }
        $powerArray = @$request->powerArray;
        if (isset($request->powerArray)) {
            foreach ($powerArray as $a) {
                $a = json_decode($a);
                DB::table('assets_power_connection')->insert([
                    'asset_id' => $id,
                    'host_psu_no' => @$a->host_psu_no,
                    'host_psu' => @$a->host_psu,
                    'host_pdu_no' => @$a->host_pdu_no,
                    'host_pdu' => @$a->host_pdu,
                    'cable_length' => @$a->cable_length
                ]);
            }
        }
        $drsArray = @$request->drsArray;
        if (isset($request->drsArray)) {
            foreach ($drsArray as $a) {
                $a = json_decode($a);
                DB::table('assets_drs_rule')->insert([
                    'asset_id' => $id,
                    'rule_type' => @$a->rule_type,
                    'vm_host' => @$a->vm_host
                ]);
            }
        }

        DB::table('asset_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Asset added', 'asset_id' => $id]);


        $settings = DB::Table('notification_settings')->first();

        $client = DB::Table('clients')->where('id', @$request->client_id)->first();

        $asset_type = DB::Table('asset_type')->where('asset_type_id', @$request->asset_type_id)->first();
        $system_type = DB::Table('system_types')->where('id', @$request->system_type)->first();

        $recipients = [$settings->asset_emails];

        // if ($settings->asset_emails != '') {
        //     $recipients[] = $settings->asset_emails;
        // }


        // $data = array('emails' => $recipients, 'hostname' => @$request->hostname, 'description' => @$request->role, 'subject' => 'New Asset ' . $asset_type->asset_type_description . ' Created', 'asset_type' => $asset_type->asset_type_description, 'contract_id' => $id, 'from_name' => $settings->from_name);
        $data = array('emails' => $recipients, 'type' => @$request->asset_type, 'client_name' => $client->client_display_name, 'hostname' => @$request->hostname, 'description' => @$request->role, 'subject' => 'New Asset ' . ($request->asset_type == 'workstation' ? @$request->asset_type : @$asset_type->asset_type_description) . ' Created', 'asset_type' => ($request->asset_type == 'workstation' ? @$request->asset_type_id :  @$asset_type->asset_type_description), 'contract_id' => $id, 'from_name' => $settings->from_name, 'system_type' => @$system_type->domain_name, 'role' => @$request->role, 'sn' => @$request->sn);
        Mail::send('emails.renewal_email_asset', ['data' => $data], function ($message) use ($data) {
            $message->to($data['emails']);
            $message->subject($data['subject']);
            $message->from('support@consultationamaltitek.com', $data['from_name']);
        });
        // Mail::queue('emails.renewal_email_asset', ['data' => $data], function ($message) use ($data) {
        //     $message->to($data['emails']);
        //     $message->subject($data['subject']);
        //     $message->from('support@consultationamaltitek.com', $data['from_name']);
        // });
        // $data = array(
        //     // 'emails' => $recipients,
        //     'emails' => 'danimughal8961@gmail.com',
        //     'type' => $request->asset_type,
        //     'client_name' => $client->client_display_name,
        //     'hostname' => $request->hostname,
        //     'description' => $request->role,
        //     'subject' => 'New Asset ' . ($request->asset_type == 'workstation' ? $request->asset_type : $asset_type->asset_type_description) . ' Created',
        //     'asset_type' => ($request->asset_type == 'workstation' ? $request->asset_type_id : $asset_type->asset_type_description),
        //     'contract_id' => $id,
        //     'from_name' => $settings->from_name,
        //     'system_type' => $system_type->domain_name,
        //     'role' => $request->role,
        //     'sn' => $request->sn,
        // );

        // // Queue the email for background sending
        // Mail::to($data['emails'])->queue(new AssetNotificationMail($data));

        DB::Table('notifications')->insert(['type' => 'Asset', 'from_email' => $settings->from_name, 'to_email' => implode(',', $data['emails']), 'subject' => $data['subject']]);

        return response()->json('success');
    }





    public function UpdateAssets(Request $request)
    {
        $qry = DB::table('contract_assets as ca')->join('contracts as c', 'c.id', '=', 'ca.contract_id')->join('assets as a', 'ca.hostname', '=', 'a.id')->where('a.id', $request->id)->where('c.is_deleted', 0)->orderBy('c.id', 'desc')->first();
        if (!$request->HasWarranty == 1 || !$request->AssetStatus == 1) {
            $warranty_end_date = 'No Contract Found';
            $warranty_status = 'Inactive';
            $support_status = 'N/A';
        } else {
            if ($qry == '') {
                $warranty_end_date = 'No Contract Found';
                $warranty_status = 'Inactive';
                $support_status = 'Unassigned';
            } else {

                if ($qry->contract_status == 'Active') {

                    $warranty_end_date = $qry->warranty_end_date;
                    $warranty_status = 'Active';
                    $support_status = 'Supported';
                } else {

                    $warranty_end_date = $qry->warranty_end_date;
                    $warranty_status = 'Inactive';
                    $support_status = 'Expired';
                }
            }
        }




        $app_owner = $request->app_owner;
        $sla = $request->sla;
        $internet_facing = $request->internet_facing ?? 0;
        $clustered = $request->clustered ?? 0;
        $disaster_recovery = $request->disaster_recovery ?? 0;
        $monitored = $request->monitored ?? 0;
        $load_balancing = $request->load_balancing ?? 0;
        $patched = $request->patched ?? 0;
        $antivirus = $request->antivirus ?? 0;
        $smtp = $request->smtp ?? 0;
        $replicated = $request->replicated ?? 0;
        $ntp = $request->ntp ?? 0;
        $backup = $request->backup ?? 0;
        $syslog = $request->syslog ?? 0;


        if ($request->ntp == 1) {
            $ssl_status = 'Unassigned';
        } else {
            $ssl_status = 'N/A';
        }

        if ($request->managed != 1) {
            $app_owner = 'N/A';
            $sla = 'N/A';

            $disaster_recovery = 2;
            $monitored = 2;

            $patched = 2;
            $antivirus = 2;
            $smtp = 2;
            $replicated = 2;

            $backup = 2;
            $syslog = 2;
        }

        $domain = DB::table('domains')->where('id', $request->domain)->first();

        $systemTypeName = '';
        if (isset($request->system_type)) {
            $systemType = DB::table('system_types')->where('id', $request->system_type)->first();
            $systemTypeName = $systemType->domain_name;
        }


        $data = array(
            'client_id' => $request->client_id,
            'site_id' => $request->site_id,
            'platform' => $request->platform,
            'hostname' => $request->hostname,
            'domain' => $request->domain,
            'system_type' => $request->system_type,
            'system_category' => $request->system_category,
            'device_info_ad_domain' => $request->device_info_addomain,
            'device_info_ou' => $request->device_info_ou,
            'fqdn' => $request->hostname . '.' . @$domain->domain_name,
            'role' => $request->role,
            'warranty_status' => $warranty_status,
            'SupportStatus' => $support_status,
            'warranty_end_date' => $warranty_end_date,
            'use_' => $systemTypeName,
            'os' => $request->os,
            'asset_type_id' => $request->asset_type_id,
            'managed' => $request->managed == 1 ? 1 : 0,

            'app_owner' => $app_owner,
            'ip_address' => $request->ip_address,
            'vlan_id' => $request->vlan_id,
            'network_zone' => $request->network_zone,
            'internet_facing' => $internet_facing,
            'disaster_recovery' => $disaster_recovery,
            'disaster_recovery1' => $request->disaster_recovery1,
            'load_balancing' => $load_balancing,
            'clustered' => $clustered,
            'HasWarranty' => $request->HasWarranty,
            'network_connected' => @$request->network_connected,

            'monitored' => $monitored,
            'patched' => $patched,
            'antivirus' => $antivirus,
            'backup' => $backup,
            'replicated' => $replicated,
            'edr' => $request->edr,
            'twofa' => $request->twofa,
            'location' => $request->location,
            'smtp' => $smtp,
            'ntp' => $ntp,
            'syslog' => $syslog,
            'sla' => $sla,
            'vcpu' => $request->vcpu,
            'memory' => $request->memory,
            'comments' => $request->comments,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'type' => $request->type,
            'sn' => $request->sn,
            'asset_tag' => @$request->asset_tag,
            'parent_asset' => $request->parent_asset,
            'cpu_model' => $request->cpu_model,
            'cpu_sockets' => $request->cpu_sockets,
            'cpu_cores' => $request->cpu_cores,
            'ssl_certificate_status' => $ssl_status,
            'NotSupportedReason' => $request->NotSupportedReason,
            'cpu_freq' => $request->cpu_freq,
            'cpu_hyperthreadings' => $request->cpu_hyperthreadings,
            'cpu_total_cores' => $request->cpu_total_cores,
            'center_server' => $request->center_server,
            'cluster_host' => $request->cluster_host,
            'vm_folder' => $request->vm_folder,
            'vm_datastore' => $request->vm_datastore,
            'vm_retart_priority' => $request->vm_retart_priority,
            'end_of_life' => isset($request->end_of_life) ? date('Y-m-d', strtotime($request->end_of_life)) : null,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id(),
        );
        DB::Table('assets')->where('id', $request->id)->update($data);
        DB::table('asset_ip_addresses')->where('asset_id', $request->id)->delete();

        DB::table('asset_attachments')->where('asset_id', $request->id)->delete();
        DB::table('asset_comments')->where('asset_id', $request->id)->delete();
        $id = $request->id;

        $ipArray = $request->ipArray;
        if (isset($request->ipArray)) {
            foreach ($ipArray as $a) {
                $a = json_decode($a);
                DB::table('asset_ip_addresses')->insert([
                    'asset_id' => $id,
                    'ip_address_value' => $a->ip_address_value,
                    'ip_address_name' => $a->ip_address_name,
                ]);
            }
        }


        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('asset_attachment/' . $a->attachment));
                DB::table('asset_attachments')->insert([
                    'asset_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('asset_comments')->insert([
                    'asset_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }
        $maintenanceArray = $request->maintenanceArray;
        if (isset($request->maintenanceArray)) {
            DB::table('asset_maintenance')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
            foreach ($maintenanceArray as $a) {
                $a = json_decode($a);


                DB::table('asset_maintenance')->insert([
                    'asset_id' => $id,
                    'frequency' => $a->frequency,
                    'day' => $a->day,
                    'occurance' => $a->occurance,
                    'month' => $a->month,
                    'start_time' => $a->start_time,
                    'time_zone' => $a->time_zone,
                    'duration_hours' => $a->duration_hours,
                    'added_by' => Auth::id(),
                ]);
            }
        } else {
            DB::table('asset_maintenance')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
        }

        $ipDns_array = $request->ipDnsArray;
        // dd($ipDns_array);
        if (isset($request->ipDnsArray)) {
            DB::table('asset_ip_dns')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
            foreach ($ipDns_array as $a) {
                $a = json_decode($a);
                DB::table('asset_ip_dns')->insert([
                    'asset_id' => $id,
                    'dns_type' => @$a->type,
                    'alias' => @$a->alias,
                    'vlan_id_no' => @$a->vlan_id,
                    'vlan_id' => @$a->vlan_text,
                    'host_name' => @$a->host_name,
                    'ip_address' => @$a->address,
                    'gateway' => @$a->dns_gateway,
                    'description' => @$a->description,
                    'primary_dns' => @$a->primary_dns,
                    'secondary_dns' => @$a->secondary_dns,
                    'primary_ntp' => @$a->primary_ntp,
                    'secondary_ntp' => @$a->secondary_ntp,
                    'subnet_ip' => @$a->subnet_ip,
                    'mask' => @$a->mask,
                    'gateway_ip' => @$a->gateway,
                    'dhcp_toggle' => @$a->dhcp_toggle,
                    'zone' => @$a->zone,
                    'background' => @$a->background,
                    'color' => @$a->color,
                ]);
            }
        } else {
            DB::table('asset_ip_dns')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
        }
        $networkAdapter_array = $request->networkAdapter;
        if (isset($request->networkAdapter)) {
            DB::table('asset_network_adapter')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
            foreach ($networkAdapter_array as $a) {
                $a = json_decode($a);
                DB::table('asset_network_adapter')->insert([
                    'asset_id' => $id,
                    'connection_type' => @$a->type,
                    'vmic' => @$a->vmic,
                    'adapter_name' => @$a->adapter_name,
                    'port_group' => @$a->port_group,
                    'adapter_type' => @$a->adapter_type,
                    'slot' => @$a->slot,
                    'port' => @$a->port,
                    'port_media' => @$a->port_media,
                    'mac_address' => @$a->mac_address
                ]);
            }
        } else {
            DB::table('asset_network_adapter')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
        }
        $portMap = $request->portMap;
        if (isset($request->portMap)) {
            DB::table('asset_port_map')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
            foreach ($portMap as $a) {
                $a = json_decode($a);
                DB::table('asset_port_map')->insert([
                    'asset_id' => $id,
                    'mapping_type' => @$a->mappingType,
                    'network_adapter' => @$a->networkAdapter,
                    'media_type' => @$a->mediaType,
                    'switch' => @$a->switch,
                    'port' => @$a->port,
                    'port_mode' => @$a->portMode,
                    'selectedIds' => isset($a->selectedIds) ? (is_array($a->selectedIds) ? implode(', ', @$a->selectedIds) : @$a->selectedIds) : null,
                    'vlan_ids' => isset($a->vlanIds) ? (is_array($a->vlanIds) ? implode(', ', @$a->vlanIds) : @$a->vlanIds) : null,
                    'sub_text' => @$a->sub_text,
                    'comments' => @$a->comments,
                    'ssid' => @$a->ssid,
                    'ssid_text' => @$a->ssid_text,
                ]);
            }
        } else {
            DB::table('asset_port_map')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
        }
        $virtualDiskArray = $request->virtualDisk;
        if (isset($request->virtualDisk)) {
            DB::table('asset_virtual_disks')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
            foreach ($virtualDiskArray as $a) {
                $a = json_decode($a);
                DB::table('asset_virtual_disks')->insert([
                    'asset_id' => $id,
                    'vdisk_no' => @$a->vDiskNo,
                    'datastore' => @$a->dataStore,
                    'scsi_id_a' => @$a->scsi_a,
                    'scsi_id_b' => @$a->scsi_b,
                    'device_type' => @$a->drive_type,
                    'drive_size' => @$a->drive_size,
                    'drive_size_unit' => @$a->drive_size_unit
                ]);
            }
        } else {
            DB::table('asset_virtual_disks')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
        }

        $raidVolumeArray = $request->raidVolume;
        if (isset($request->raidVolume)) {
            DB::table('asset_raid_volume')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
            foreach ($raidVolumeArray as $a) {
                $a = json_decode($a);
                DB::table('asset_raid_volume')->insert([
                    'asset_id' => $id,
                    'name' => @$a->volume_name,
                    'volume_description' => @$a->volume_description,
                    'controller' => @$a->controller,
                    'drive_type' => @$a->drive_type,
                    'raid_level' => @$a->raid_level,
                    'no_of_sets' => @$a->no_of_sets,
                    'no_of_drives' => @$a->no_of_drives,
                    'drive_size' => @$a->drive_size,
                    'drive_size_unit' => @$a->drive_size_unit,
                    'volume_size' => @$a->volume_size,
                ]);
            }
        } else {
            DB::table('asset_raid_volume')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
        }

        $logicalVolumeArray = $request->logicalVolume;
        if (isset($request->logicalVolume)) {
            DB::table('asset_logical_volume')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
            foreach ($logicalVolumeArray as $a) {
                $a = json_decode($a);
                DB::table('asset_logical_volume')->insert([
                    'asset_id' => $id,
                    'source_disk' => @$a->source_disk,
                    'tooltip' => @$a->tooltip,
                    'volume' => @$a->volume,
                    'volume1' => @$a->volume1,
                    'volume_name' => @$a->volume_name,
                    'size' => @$a->size,
                    'size_unit' => @$a->size_unit,
                    'format' => @$a->format,
                    'block_size' => @$a->block_size
                ]);
            }
        } else {
            DB::table('asset_logical_volume')->where('asset_id', $request->id)->update([
                'is_deleted' => 1
            ]);
        }
        $locationArray = @$request->locationArray;
        if (isset($request->locationArray)) {
            DB::table('assets_location')->where('asset_id', $id)->delete();
            foreach ($locationArray as $a) {
                $a = json_decode($a);
                DB::table('assets_location')->insert([
                    'asset_id' => $id,
                    'contact_name' => @$a->contact_name,
                    'floor' => @$a->floor,
                    'cabinet_rack' => @$a->cabinet_rack,
                    'u' => @$a->u,
                    'racked' => @$a->racked,
                    'u_size' => @$a->u_size,
                    'u_location' => @$a->u_location,
                    'room' => @$a->room
                ]);
            }
        }
        $powerArray = @$request->powerArray;
        if (isset($request->powerArray)) {
            DB::table('assets_power_connection')->where('asset_id', $id)->delete();
            foreach ($powerArray as $a) {
                $a = json_decode($a);
                DB::table('assets_power_connection')->insert([
                    'asset_id' => $id,
                    'host_psu_no' => @$a->host_psu_no,
                    'host_psu' => @$a->host_psu,
                    'host_pdu_no' => @$a->host_pdu_no,
                    'host_pdu' => @$a->host_pdu,
                    'cable_length' => @$a->cable_length
                ]);
            }
        }

        $drsArray = @$request->drsArray;
        if (isset($request->drsArray)) {
            DB::table('assets_drs_rule')->where('asset_id', $id)->delete();
            foreach ($drsArray as $a) {
                $a = json_decode($a);
                DB::table('assets_drs_rule')->insert([
                    'asset_id' => $id,
                    'rule_type' => @$a->rule_type,
                    'vm_host' => @$a->vm_host
                ]);
            }
        }
        DB::table('asset_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Asset updated', 'asset_id' => $id]);
        return response()->json('success');
    }







    //     public function getPhysicalContent(Request $request)
    //     {
    //         $id = $request->id;
    //         $html = '';

    //         $q = DB::table('assets as a')->select('a.*','st.domain_name as system_type','sc.domain_name as system_category', 's.site_name', 'd.domain_name', 'c.client_display_name', 'o.operating_system_name', 'o.operating_system_image', 'm.vendor_name', 's.address', 's.city', 's.country', 's.phone', 's.zip_code', 's.province', 'at.asset_icon', 'at.asset_type_description', 'at.asset_type_description as asset_type_name', 'n.vlan_id as vlanId', 'n.subnet_ip', 'n.mask', 'usr.firstname as created_firstname', 'usr.lastname as created_lastname', 'upd.firstname as updated_firstname', 'upd.lastname as updated_lastname', 'c.logo', 'm.vendor_image', 'nz.network_zone_description', 'nz.tag_back_color', 'nz.tag_text_color')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->leftjoin('network as n', 'a.vlan_id', '=', 'n.id')->leftjoin('network_zone as nz', 'nz.network_zone_description', '=', 'n.zone')->leftjoin('users as usr', 'usr.id', '=', 'a.created_by')->leftjoin('users as upd', 'upd.id', '=', 'a.updated_by')->leftjoin('system_category as sc', 'a.system_category', '=', 'sc.id')->leftjoin('system_types as st', 'a.system_type', '=', 'st.id')->where('a.id', $id)->first();


    //         $contract_ssl_line_items = DB::Table('contract_assets as ca')->selectRaw('a.contract_no,c.client_display_name,a.contract_status,a.contract_start_date,a.contract_end_date,v.vendor_image,a.contract_description,a.contract_type,a.id,( SELECT row_number FROM (
    //     SELECT   id,@curRow := @curRow + 1 AS row_number 
    //     FROM (
    //         SELECT * FROM contracts  where is_deleted=0  
    //         ORDER BY id desc
    //     ) l 
    //     JOIN (
    //         SELECT @curRow := 0 
    //     ) r order by id desc
    // ) t where t.id=ca.contract_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('contracts  as a', 'a.id', '=', 'ca.contract_id')->join('clients as c', 'c.id', '=', 'a.client_id')->join('vendors as v', 'v.id', '=', 'a.vendor_id')->groupBy('a.id')->where('a.is_deleted', 0)->where('ca.is_deleted', 0)->orderBy('a.contract_no', 'asc')->get();


    //         $ssl_line_items_2 = DB::Table('ssl_host as ca')->selectRaw(' a.cert_name , a.cert_status , a.cert_edate,a.cert_rdate , a.cert_type , a.id , c.logo , v.vendor_image , a.description , c.client_display_name ,( SELECT row_number FROM (
    //     SELECT   id,@curRow := @curRow + 1 AS row_number 
    //     FROM (
    //         SELECT * FROM ssl_certificate  where is_deleted=0  
    //         ORDER BY id desc
    //     ) l 
    //     JOIN (
    //         SELECT @curRow := 0 
    //     ) r order by id desc
    // ) t where t.id=ca.ssl_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('ssl_certificate  as a', 'a.id', '=', 'ca.ssl_id')->join('clients as c', 'c.id', '=', 'a.client_id')->leftjoin('vendors as v', 'v.id', '=', 'a.cert_issuer')->where('a.is_deleted', 0)->orderBy('a.cert_name', 'asc')->get();


    //         if ($q->AssetStatus == 1) {
    //             $html .= '<div class="block card-round   bg-new-green new-nav" >
    //                                 <div class="block-header   py-new-header" >
    //                                <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
    //                                 <div class="d-flex">
    //                                 <img src="public/img/icon-active-removebg-preview.png" width="40px">
    //                                 <div class="ml-4">
    //                                 <h4  class="mb-0 header-new-text " style="line-height:27px">  Asset Active</h4>
    //                                 <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
    //                                     </div>
    //                                 </div>';
    //         } else {
    //             $html .= '<div class="block card-round   bg-new-red new-nav" >
    //                                 <div class="block-header   py-new-header" >
    //                                <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
    //                                 <div class="d-flex">
    //                                 <img src="public/img/action-white-end-revoke.png" width="40px">
    //                                 <div class="ml-4">
    //                                 <h4  class="mb-0 header-new-text " style="line-height:27px">Asset Decomissioned</h4>';
    //             $renewed_qry = DB::Table('users')->Where('id', $q->InactiveBy)->first();



    //             $html .= '<p class="mb-0  header-new-subtext" style="line-height:17px">On ' . date('Y-M-d', strtotime($q->InactiveDate)) . ' by ' . @$renewed_qry->firstname . ' ' . @$renewed_qry->lastname . '</p>
    //                                     </div>
    //                                 </div>';
    //         }


    //         $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print"> <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" > 
    //                                              <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="' . asset('public/img/paper-clip-white.png') . '" width="20px"></a>
    //                                          </span>
    //                                              <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
    //                                              <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="' . asset('public/img/comment-white.png') . '" width="20px"></a></span>';

    //         if (Auth::user()->role != 'read') {



    //             if ($q->AssetStatus == 1) {
    //                 $html .= '<span  > 
    //                                              <a href="javascript:;" class="btnEnd"  data="' . $q->AssetStatus . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Decomission" class=" "><img src="public/img/action-white-end-revoke.png?cache=1" width="22px"></a>
    //                                          </span>';
    //             } else {
    //                 $html .= '    <span  > 
    //                                              <a href="javascript:;" class="btnEnd"  data="' . $q->AssetStatus . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="public/img/icon-header-white-reactivate.png?cache=1" width="22px"></a>
    //                                          </span>';
    //             }
    //         }

    //         $html .= ' <a  target="_blank" href="pdf-asset?id=' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Pdf"  style="padding:5px 7px">
    //                                                 <img src="public/img/action-white-pdf.png?cache=1" width="24px"  >
    //                                             </a>
    //      <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
    //                                                 <img src="public/img/action-white-print.png?cache=1" width="20px">
    //                                             </a>';


    //         if (Auth::user()->role != 'read') {

    //             $html .= '<a   href="edit-assets?id=' . $q->id . '" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png?cache=1" width="20px">  </a>

    //                                             <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png?cache=1" width="17px"></a>';
    //         }

    //         $html .= '</div></div>
    //                             </div>
    //                         </div>

    //                         <div class="block new-block position-relative mt-3" >
    //                                                 <div class="top-div text-capitalize">' . $q->asset_type_description . ' </div>

    //                             <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">

    //                                 <div class="row justify-content- position-relative inner-body-content push" >
    //                             <div class="col-sm-12">

    //                         <input type="hidden" name="attachment_array" id="attachment_array" >
    //                                 <div class="row">

    // <div class="col-sm-10">
    // <div class="form-group row">
    // <div class="col-sm-4">
    // <div class="bubble-new">Role</div> 
    //  </div>
    // <div class="col-sm-8">
    //       <div class="bubble-white-new bubble-text-sec">
    //     ' . $q->role . '

    //     </div> 

    // </div>
    // </div>
    //                                         <div class="form-group row">
    //                                                         <div class="col-sm-4">
    //                                            <div class="bubble-new">Operating System</div> 
    //                                        </div>

    //                                             <div class="col-sm-8">

    //                                            <div class="bubble-white-new bubble-text-first"><b>' . $q->operating_system_name.'</b></div> 

    //                                             </div>

    //                                          </div>

    //                                         <div class="form-group row">
    //                                          <div class="col-sm-4">
    //                                            <div class="bubble-new">FQDN</div> 
    //                                        </div>
    //                                             <div class="col-sm-8">
    //                                                   <div class="bubble-white-new bubble-text-sec">
    //                                                     ' . $q->fqdn . '

    //                                                 </div> 

    //                                             </div>

    //                                         </div>
    //                                          <div class="form-group row">
    //                                          <div class="col-sm-4">
    //                                            <div class="bubble-new">System Type</div> 
    //                                        </div>
    //                                             <div class="col-sm-8">
    //                                                   <div class="bubble-white-new bubble-text-sec" style="display: flex; justify-content: space-between;"><span>
    //                                                 ' . ($q->system_type != null && $q->system_type != "" ? $q->system_type : '-') .'</span><span>'. ($q->system_category != null && $q->system_category != "" ? $q->system_category : '-') . '
    //                                                   </span>
    //                                                 </div> 

    //                                             </div>

    //                                         </div>
    //                                         <div class="row form-group mt-5">
    //                                                        <div class="col-sm-4 t er">

    // <div class="contract_type_button  w-100 mr-4 mb-3">
    //   <input type="checkbox" class="custom-control-input" id="disaster_recovery1" name="disaster_recovery" disabled="" value="1" ' . ($q->disaster_recovery1 == 1 ? 'checked' : '') . '>
    //   <label class="btn btn-new w-100  py-1 font-11pt " for="disaster_recovery1">D/R Plan</label>
    // </div>
    // </div>





    //  <div class="col-sm-4  ">

    // <div class="contract_type_button  w-100 mr-4  ">
    //           <input type="checkbox" class="custom-control-input" id="clustered" name="clustered"  disabled="" value="1"  ' . ($q->clustered == 1 ? 'checked' : '') . '>
    //   <label class="btn btn-new w-100  py-1 font-11pt " for="clustered"> Clustered</label>
    // </div>
    // </div>

    //  <div class="col-sm-4 text-center">

    // <div class="contract_type_button  w-100 mr-4  ">
    //      <input type="checkbox" class="custom-control-input" id="internet_facing" name="internet_facing" value="1" disabled="" ' . ($q->internet_facing == 1 ? 'checked' : '') . '>

    //        <label class="btn btn-new w-100  py-1 font-11pt " for="internet_facing"> Internet Facing</label>
    // </div>
    // </div>




    //  <div class="col-sm-4  text-right">

    // <div class="contract_type_button  w-100 mr-4 ">
    //               <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" ' . ($q->load_balancing == 1 ? 'checked' : '') . '>
    //   <label class="btn btn-new w-100  py-1 font-11pt " for="load_balancing"> Load Balanced</label>
    // </div>
    // </div>




    //  <div class="col-sm-4 text-right">

    // <div class="contract_type_button  w-100 mr-4 ">
    //               <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" ' . ($q->ntp == 1 ? 'checked' : '') . '>
    //   <label class="btn btn-new w-100 py-1 font-11pt" for="load_balancing"> SSL Certificate</label>
    // </div>
    // </div>


    //  <div class="col-sm-4   text-right">

    // <div class="contract_type_button  w-100 mr-4 ">
    //               <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" ' . ($q->HasWarranty == 1 > 0 ? 'checked' : '') . '>
    //   <label class="btn btn-new w-100 py-1 font-11pt" for="load_balancing"> ' . ($q->HasWarranty == 1 ? 'Supported' : 'Unsupported') . '</label>
    // </div>
    // </div>
    // </div>
    //                                     </div>
    //                                     <div class="col-sm-2">
    //                                                <div class="bubble-white-new bubble-text-sec" style="padding:10px">

    //                                                       <img src="public/operating_system_logos/' . $q->operating_system_image . '" style="width: 100%;">
    //                                                 </div> 

    //                                     </div>


    //                                                </div>      








    //                  </div>
    //              </div>
    //          </div> ';

    //         //          <div class="row">

    //         //          <div class="col-sm-10">
    //         //              <div class="form-group row">
    //         //                              <div class="col-sm-4">
    //         //                 <div class="bubble-new">Client</div> 
    //         //             </div>

    //         //                  <div class="col-sm-8">

    //         //                 <div class="bubble-white-new bubble-text-first"><b>'.$q->client_display_name.'</b></div> 

    //         //                  </div>

    //         //               </div>

    //         //              <div class="form-group row">
    //         //               <div class="col-sm-4">
    //         //                 <div class="bubble-new">Site</div> 
    //         //             </div>
    //         //                  <div class="col-sm-8">
    //         //                        <div class="bubble-white-new bubble-text-sec">
    //         //                          <b>'.$q->site_name.'</b><br>
    //         //                          <span>'.$q->address.'</span><br>
    //         //                          <span>'.$q->city.','.$q->province.'</span><br>
    //         //                          <span>'.$q->zip_code.'</span><br>
    //         //                      </div> 

    //         //                  </div>

    //         //              </div>



    //         //          </div>
    //         //          <div class="col-sm-2">
    //         //                     <div class="bubble-white-new bubble-text-sec" style="padding:10px">


    //         //                            <img src="public/client_logos/'.$q->logo.'" style="width: 100%;">
    //         //                      </div> 

    //         //          </div>


    //         //                     </div>      








    //         // </div>
    //         if ($q->asset_type == 'physical') {

    //             $html .= '

    //                             <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">

    //                                 <div class="row justify-content- position-relative inner-body-content push" >
    //  <div class="top-right-div top-right-div-blue text-capitalize">Hardware</div>

    //                             <div class="col-sm-12 m-
    //                             " >

    //                         <input type="hidden" name="attachment_array" id="attachment_array" >
    //                                 <div class="row">

    //                                     <div class="col-sm-10">
    //                                         <div class="form-group row">
    //                                                         <div class="col-sm-4">
    //                                            <div class="bubble-new">Manufacturer</div> 
    //                                        </div>

    //                                             <div class="col-sm-8">

    //                                            <div class="bubble-white-new bubble-text-first"><b>' . $q->vendor_name . '</b></div> 

    //                                             </div>

    //                                          </div>

    //                                         <div class="form-group row">
    //                                          <div class="col-sm-4">
    //                                            <div class="bubble-new">Model</div> 
    //                                        </div>
    //                                             <div class="col-sm-8">
    //                                                   <div class="bubble-white-new bubble-text-sec">
    //                                                     ' . $q->model . '

    //                                                 </div> 

    //                                             </div>

    //                                         </div>
    //                                          <div class="form-group row">
    //                                          <div class="col-sm-4">
    //                                            <div class="bubble-new">Type</div> 
    //                                        </div>
    //                                             <div class="col-sm-8">
    //                                                   <div class="bubble-white-new bubble-text-sec">
    //                                                 ' . $q->type . '

    //                                                 </div> 

    //                                             </div>

    //                                         </div>

    //                                          <div class="form-group row">
    //                                          <div class="col-sm-4">
    //                                            <div class="bubble-new">Serial Number</div> 
    //                                        </div>
    //                                             <div class="col-sm-8">
    //                                                   <div class="bubble-white-new bubble-text-sec">
    //                                                 ' . $q->sn . '

    //                                                 </div> 

    //                                             </div>

    //                                         </div>
    //                                         ';
    //             if ($q->asset_type_description == 'Physical Server') {
    //                 $html .= '<div class="form-group row">
    //                                          <div class="col-sm-4">
    //                                            <div class="bubble-new">CPU</div> 
    //                                        </div>
    //                                             <div class="col-sm-8">
    //                                                   <div class="bubble-white-new bubble-text-sec">
    //                                           ' . $q->cpu_sockets . ' ' . $q->cpu_model . ' ' . $q->cpu_cores . ' C @ ' . $q->cpu_freq . ' GHz

    //                                                 </div> 

    //                                             </div>

    //                                         </div>
    //                                         ';
    //             }

    //             $html .= '<div class="form-group row">
    //                                          <div class="col-sm-4">
    //                                            <div class="bubble-new">Memory</div> 
    //                                        </div>
    //                                             <div class="col-sm-8">
    //                                                   <div class="bubble-white-new bubble-text-sec">
    //                                           ' . $q->memory . '  

    //                                                 </div> 

    //                                             </div>

    //                                         </div>

    //                                     </div>
    //                                     <div class="col-sm-2">
    //                                                ';
    //             if ($q->vendor_image != '') {
    //                 $html .= '<div class="bubble-white-new bubble-text-sec" style="padding:10px">


    //                                                       <img src="public/vendor_logos/' . $q->vendor_image . '" style="width: 100%;">
    //                                                 </div> ';
    //             }

    //             $html .= '</div>


    //                                                </div>      








    //                  </div>
    //              </div>
    //          </div>';
    //         } else {
    //             $html .= '

    //                             <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">

    //                                 <div class="row justify-content- position-relative inner-body-content push" >
    //  <div class="top-right-div top-right-div-blue text-capitalize">Resources</div>

    //                             <div class="col-sm-12 m-
    //                             " >

    //                         <input type="hidden" name="attachment_array" id="attachment_array" >
    //                                 <div class="row">

    //                                     <div class="col-sm-10">

    //                                          <div class="form-group row">
    //                                          <div class="col-sm-4">
    //                                            <div class="bubble-new">vCPUs</div> 
    //                                        </div>
    //                                             <div class="col-sm-8">
    //                                                   <div class="bubble-white-new bubble-text-sec">
    //                                           ' . $q->vcpu . '  

    //                                                 </div> 

    //                                             </div>
    //       </div>
    //                                           <div class="form-group row">
    //                                          <div class="col-sm-4">
    //                                            <div class="bubble-new">Memory</div> 
    //                                        </div>
    //                                             <div class="col-sm-8">
    //                                                   <div class="bubble-white-new bubble-text-sec">
    //                                           ' . $q->memory . '  

    //                                                 </div> 

    //                                             </div>

    //                                         </div>

    //                                     </div>
    //                                     <div class="col-sm-2">
    //                                                 <div class="bubble-white-new bubble-text-sec" style="padding:10px">


    //                                                       <img src="public/img/static-vm.png?cache=1" style="width: 100%;">
    //                                                 </div> </div>


    //                                                </div>      








    //                  </div>
    //              </div>
    //          </div>






    // ';
    //         }







    //         $html .= '
    //                             <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">

    //                                 <div class="row justify-content- position-relative inner-body-content push" >
    //  <div class="top-right-div top-right-div-green text-capitalize">Networking</div>

    //                             <div class="col-sm-12 m-
    //                             " >

    //                         <input type="hidden" name="attachment_array" id="attachment_array" >
    //                                 <div class="row">
    //       <div class="col-sm-10">
    //         <div class="inner-body-content position-relative px-3">
    //                                    <div class="top-div text-capitalize w-25 font-size-sm" >Primary IP
    // </div>                               

    //                                         <div class="   row">
    //                                                              <div class="col-sm-4 form-group ">
    //                                            <div class="bubble-new">Network Zone
    // </div> 
    //                                        </div>                           
    //                                             <div class="col-sm-3 form-group ">

    //                                           ';


    //         $html .= '<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px;width:fit-content!important;border-radius:5px;background:' . $q->tag_back_color . ';color: ' . $q->tag_text_color . '" class=" text-center px-2 border-none  font-size-md  bubble-white-new bubble-text-sec"  ><b>' . $q->network_zone_description . '</b></div>';

    //         //     if($q->network_zone=='Internal'){
    //         //                $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px" class=" text-center border-none text-white font-size-md bg-secondary bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone_description.'</b></div>';
    //         // }elseif($q->network_zone=='Secure'){
    //         //     $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center border-none font-size-md text-white bg-info bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone.'</b></div>';
    //         // }
    //         //     elseif($q->network_zone=='Greenzone'){
    //         //     $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center border-none font-size-md text-white bg-success bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone.'</b></div>';
    //         //     }elseif($q->network_zone=='Guest'){
    //         //     $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center font-size-md border-none text-white bg-warning"  ><b>'.$q->network_zone.'</b></div>';
    //         //     }elseif($q->network_zone=='Semi-Trusted'){
    //         //     $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white border-none bubble-white-new bubble-text-sec  " style="background:#FFFF11;color: black"  ><b>'.$q->network_zone.'</b></div>';
    //         //     }elseif($q->network_zone=='Public DMZ' || $q->network_zone=='Public' || $q->network_zone=='Servers Public DMZ' ){
    //         //     $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white bubble-white-new border-none bubble-text-sec bg-danger"  ><b>'.$q->network_zone.'</b></div>
    //         //    ';
    //         //     }else{
    //         //       $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white bubble-white-new border-none bubble-text-sec  "  ><b>'.$q->network_zone.'</b></div>';
    //         //     }
    //         $html .= '</div> 
    //                                             </div>
    //                                             <div class="row">




    //                                          <div class="col-sm-4 form-group ">
    //                                            <div class="bubble-new">vLAN ID
    // </div> 
    //                                        </div>
    //                                             <div class="col-sm-8 form-group ">
    //                                                   <div class="bubble-white-new bubble-text-first">
    //                                                    ' . $q->vlanId . ' 

    //                                                 </div> 

    //                                             </div>


    //                                          <div class="col-sm-4 form-group ">
    //                                            <div class="bubble-new">IP Address</div> 
    //                                        </div>
    //                                             <div class="col-sm-8 form-group ">
    //                                                   <div class="bubble-white-new bubble-text-sec">

    //                                                        ' . $q->ip_address . '' . $q->mask . ' 
    //                                                 </div> 

    //                                             </div>
    //                                           <div class="col-sm-4 form-group ">
    //                                            <div class="bubble-new">Gateway Ip</div> 
    //                                        </div>
    //                                             <div class="col-sm-8 form-group ">
    //                                                   <div class="bubble-white-new bubble-text-sec">

    //                                                        ' . $q->subnet_ip . '   
    //                                                 </div> 

    //                                             </div>


    // </div>
    //                                     </div>
    //                                     </div>
    //                                     <div class="col-sm-2">
    //                                                <div class="bubble-white-new bubble-text-sec" style="padding:10px">
    //                                                 <!--  $q->vendor_logos  -->
    //                                                       <img src="public/img/static-networking.png?cache=1" style="width: 100%;">
    //                                                 </div> 

    //                                     </div>';
    //         $asset_ip = DB::Table('asset_ip_addresses')->where('asset_id', $q->id)->orderby('ip_address_name', 'asc')->get();
    //         if (sizeof($asset_ip) > 0) {
    //             $html .= '     <div class="col-sm-12 mt-4">
    //         <div class="inner-body-content position-relative px-3">
    //                 <div class="top-div text-capitalize w-25 font-size-sm" >Additional IPs </div>                               

    //                                         <div class="row form-group">

    //                                                 ';
    //             foreach ($asset_ip as $i) {
    //                 $html .= '<div class="col-sm-6">
    //                                                     <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
    //                                         <table class="table table-borderless table-vcenter mb-0">
    //                                             <tbody>
    //                                                 <tr>
    //                                                     <td class="text-center pr-0" style="width: 50%">
    //                                                         <p class="mb-0 mr-3 mx-auto  text-white text-center  px-2 " style="max-width: 150px;border-radius: 10px;border: 1px solid grey;padding:5px 5px;background: #595959;"><b>' . $i->ip_address_name . '</b></p> 
    //                                                     </td>
    //                                                     <td class="js-task-content  text-center">
    //                                                         <label class="mb-0 bubble-text-sec font-12pt">' . $i->ip_address_value . '</label>
    //                                                     </td>

    //                                                 </tr>

    //                                         </tbody>
    //                                     </table>

    //                                     </div>
    //                                 </div>';
    //             }
    //             $html .= '</div>                         

    //                                     </div>
    //                                     </div>';
    //         }

    //         $html .= '</div>

    //                                                </div>      








    //                  </div>
    //              </div>


    // ';
    //         if ($q->managed == 1) {

    //             $html .= '<div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">

    //                                 <div class="row justify-content- position-relative inner-body-content push" >
    //  <div class="top-right-div top-right-div-yellow text-capitalize">Managed Services</div>

    //                             <div class="col-sm-12 m-
    //                             " >

    //                         <input type="hidden" name="attachment_array" id="attachment_array" >
    //                                 <div class="row">
    //       <div class="col-sm-10">

    //                                         <div class="form-group    row">

    //                                                <div class="col-sm-4 form-group ">
    //                                            <div class="bubble-new">App Owner
    // </div> 
    //                                        </div>
    //                                             <div class="col-sm-8 form-group ">
    //                                                   <div class="bubble-white-new bubble-text-first">
    //                                                    ' . $q->app_owner . ' 

    //                                                 </div> 

    //                                             </div>



    //                                                              <div class="col-sm-4 form-group ">
    //                                            <div class="bubble-new">SLA
    // </div> 
    //                                        </div>                           
    //                                             <div class="col-sm-3 form-group ">



    //                                                         ';

    //             $sla = DB::Table('sla')->Where('sla_description', $q->sla)->first();
    //             $html .= '
    //                                                 <div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;width:fit-content!important;border-radius:5px;min-height:29px;background:' . @$sla->tag_back_color . ';color:' . @$sla->tag_text_color . '" class=" text-center font-size-md bubble-white-new border-none bubble-text-sec px-2"  ><b>' . $q->sla . '</b></div>';
    //             $html .= '
    //                                             </div> 
    //                                             </div>
    //                                             <div class="row">

    //  <div class="col-sm-3 mb-3 ">

    // <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
    //           <input type="checkbox" class="custom-control-input" id="patched" name="patched" value="1" disabled="" ' . ($q->patched == 1 ? 'checked' : '') . '>
    //   <label class="btn btn-new w-100  py-1 font-11pt" for="patched"> Patched</label>
    // </div>
    // </div>

    //  <div class="col-sm-3   mb-3">

    // <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
    //        <input type="checkbox" class="custom-control-input" id="monitored" name="monitored" value="1" disabled="" ' . ($q->monitored == 1 ? 'checked' : '') . '>
    //        <label class="btn btn-new w-100  py-1 font-11pt " for="monitored">Monitored</label>
    // </div>
    // </div>

    //  <div class="col-sm-3  mb-3">

    // <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
    //         <input type="checkbox" class="custom-control-input" id="backup" name="backup" value="1" disabled="" ' . ($q->backup == 1 ? 'checked' : '') . '>
    //        <label class="btn btn-new w-100  py-1 font-11pt " for="backup">Backup</label>
    // </div>
    // </div>


    //  <div class="col-sm-3  mb-3">

    // <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
    //            <input type="checkbox" class="custom-control-input" id="antivirus" disabled="" name="antivirus" value="1"   ' . ($q->antivirus == 1 ? 'checked' : '') . '>
    //        <label class="btn btn-new w-100  py-1 font-11pt" for="antivirus">Anti-Virus
    // </label>
    // </div>
    // </div>


    //  <div class="col-sm-3  mb-3">

    // <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
    //           <input type="checkbox" class="custom-control-input" id="replicated"  disabled="" name="replicated" value="1" ' . ($q->replicated == 1 ? 'checked' : '') . '>
    //        <label class="btn btn-new w-100  py-1 font-11pt" for="replicated">Replicated
    // </label>
    // </div>
    // </div>

    //  <div class="col-sm-3 ">

    // <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
    //           <input type="checkbox" class="custom-control-input" id="disaster_recovery"  disabled="" name="disaster_recovery" value="1" ' . ($q->disaster_recovery == 1 ? 'checked' : '') . '>
    //        <label class="btn btn-new w-100  py-1 font-11pt " for="disaster_recovery">Vulnerability Scan</label>
    // </div>
    // </div>

    //  <div class="col-sm-3  ">


    // <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
    //                      <input type="checkbox" class="custom-control-input" id="syslog" disabled="" name="syslog" value="1" ' . ($q->syslog == 1 ? 'checked' : '') . ' >
    //        <label class="btn btn-new w-100  py-1 font-11pt" for="syslog">SIEM</label>
    // </div>
    // </div>

    //  <div class="col-sm-3  ">

    // <div class="contract_type_button w-100 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
    //       <input type="checkbox" class="custom-control-input" id="smtp" name="smtp" value="1" disabled="" ' . ($q->smtp == 1 ? 'checked' : '') . '>
    //        <label class="btn btn-new w-100  py-1 font-11pt" for="smtp">SMTP</label>
    // </div>
    // </div>


    // </div>

    //                                     </div>
    //                                     <div class="col-sm-2">
    //                                                <div class="bubble-white-new bubble-text-sec" style="padding:10px">
    //                                                 <!--  $q->vendor_logos  -->
    //                                                       <img src="public/img/static-amaltitek.png?cache=1" style="width: 100%;">
    //                                                 </div> 

    //                                     </div>

    //      <div class="col-sm-12 mt-4">

    //                                     </div>


    //                           </div>

    //                                                </div>      



    // ';
    //         }

    //         $html .= '




    //                  </div>
    //              </div>


    //          </div>';


    //         if (sizeof($contract_ssl_line_items) > 0) {

    //             $html .= '<div class="block new-block position-relative mt-4" >
    //                                                 <div class="top-div text-capitalize">Support Contracts</div>

    //        <div class="block-content row new-block-content " id="commentBlock"><div class="col-lg-10">
    //  ';

    //             foreach ($contract_ssl_line_items as $l) {
    //                 $rownumber = ceil($l->rownumber / 10);
    //                 $contract_end_date = date('Y-M-d', strtotime($l->contract_end_date));
    //                 $today = date('Y-m-d');
    //                 $earlier = new DateTime($l->contract_end_date);
    //                 $later = new DateTime($today);

    //                 $abs_diff = $later->diff($earlier)->format("%a"); //3

    //                 $html .= '
    // <div class="block block-rounded align-items-center  table-block-new mb-2 pb-0 " data="' . $l->id . '" style="cursor:pointer;">


    //                         <div class="block-content pt-1 pb-1 d-flex align-items-center pl-1 position-relative">


    //                                          <div class="mr-1   p-2  justify-content-center align-items-center  d-flex" style="width:15%">
    //                                             <img src="public/vendor_logos/' . $l->vendor_image . '"  class="rounded-circle  "  width="100%" style=" object-fit: cover;">
    //                                         </div>


    //                                      <div class="  " style="width:50%">
    //                                              <p class="font-12pt mb-0 text-truncate font-w600 c1">' . $l->client_display_name . '</p>

    //                                                <div class="d-flex">';
    //                 if ($l->contract_type == 'Hardware Support') {
    //                     $html .= '<p class="font-11pt mr-1   mb-0  c4-p  "  style="max-width:12%; " data-toggle="tooltip" data-title="Hardware Support" data="' . $l->id . '">H</p>';
    //                 } elseif ($l->contract_type == 'Software Support') {
    //                     $html .= ' <p class="font-11pt mr-1   mb-0  c4-s  "  style="max-width:12%; " data-toggle="tooltip" data-title="Software Support" data="' . $l->id . '">S</p>';
    //                 } else {

    //                     $html .= '<p class="font-11pt mr-1   mb-0   c4-v  "  style="max-width:12%; " data-toggle="tooltip" data-title="Subscription" data="' . $l->id . '">C</p>';
    //                 }
    //                 $html .= '<p class="font-12pt mb-0 text-truncate   c4"  style="max-width:90%" data="' . $q->id . '">' . $l->contract_no . '</p></div>


    //                                                     <p class="font-12pt mb-0 text-truncate c2">' . $l->contract_description . '</p> 
    //                                         </div>
    //                                         <div class=" text-right" style="width:25%;;">
    //                                                                             <div style="position: absolute;width: 100%; top: 10px;right: 10px;">


    // ';

    //                 $contract_end_date = date('Y-M-d', strtotime($l->contract_end_date));
    //                 $today = date('Y-m-d');
    //                 $earlier = new DateTime($l->contract_end_date);
    //                 $later = new DateTime($today);

    //                 $abs_diff = $later->diff($earlier)->format("%a"); //3




    //                 if ($l->contract_status == 'Active') {

    //                     if ($abs_diff <= 30) {
    //                         $html .= '<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-weight-bold   text-dark"  >
    //                                                                                 <span class=" ">Upcoming</span>
    //                                                                     </div> ';
    //                     } else {
    //                         $html .= ' <div class=" bg-new-green ml-auto  badge-new  text-center font-weight-bold   text-white"  >
    //                                                                                  <span class=" ">Active</span>
    //                                                                     </div>  
    //                                                                     ';
    //                     }
    //                 } elseif ($l->contract_status == 'Inactive') {

    //                     $html .= '<div class=" bg-new-blue ml-auto  badge-new  font-weight-bold    text-center  font-w600 text-white"  >
    //                                                                                   <span class=" ">Renewed</span>

    //                                                                     </div>  ';
    //                 } elseif ($l->contract_status == 'Expired/Ended') {

    //                     $html .= '   <div class=" bg-new-red ml-auto  font-weight-bold    badge-new  text-center  font-w600 text-white"  >
    //                                                                                   <span class=" ">Ended</span>

    //                                                                     </div>';
    //                 } elseif ($l->contract_status == 'Ended') {
    //                     $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center  font-weight-bold   0 text-white"  >
    //                                                                                   <span class=" ">Ended</span>
    //                                                                     </div>';
    //                 } elseif ($l->contract_status == 'Expired') {
    //                     $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   text-white"  >
    //                                                                                   <span class=" ">Expired</span>
    //                                                                     </div>';
    //                 }





    //                 $html .= '    </div>';
    //                 $ssl_line_items = DB::Table('contract_assets as ca')->select('a.hostname', 'a.AssetStatus')->where('ca.contract_id', $l->id)->join('assets as a', 'a.id', '=', 'ca.hostname')->where('ca.is_deleted', 0)->orderBy('a.hostname', 'asc')->get();
    //                 $cvm = '<b class="HostActive text-white">Assigned Assets</b><br>';
    //                 foreach ($ssl_line_items as $v) {
    //                     if ($v->AssetStatus != '1') {
    //                         $cvm .= '<span class="HostInactive text-uppercase">' . $v->hostname . '</span><br>';
    //                     } else {
    //                         $cvm .= '<span class="HostActive text-uppercase">' . $v->hostname . '</span><br>';
    //                     }
    //                 }

    //                 $contract_end_date = date('Y-M-d', strtotime($l->contract_end_date));
    //                 $today = date('Y-m-d');
    //                 $earlier = new DateTime($l->contract_end_date);
    //                 $later = new DateTime($today);

    //                 $abs_diff = $later->diff($earlier)->format("%a"); //3

    //                 $cvm = '<p class="HostActive text-white  my-0">Validity Range</p><p class="HostActive my-n1 text-orange"  >' . date('d-M-Y', strtotime($l->contract_start_date)) . '-' . date('d-M-Y', strtotime($l->contract_end_date)) . '</p><p class="font-10pt mb-0 text-grey text-truncate mt-0"> <small><i>' . $abs_diff . ' days remaining</i></small></p>';
    //                 $html .= "<div    style='position: absolute;width: 100%; bottom: 2px;right: 10px;display: flex;align-items: center;justify-content: end;'>


    //     <div class='ActionIcon'  data-src='public/img/calendar-grey-removebg-preview.png?cache=1' data-original-src='public/img/calendar-grey-removebg-preview.png'>
    //  <a href='javascript:;' class='toggle '' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title='' >
    //                              <img  src='public/img/calendar-grey-removebg-preview.png' width='24px'  class='' >
    //                         </a>
    //                                                                     </div>


    //  ";
    //                 if (Auth::check()) {
    //                     if (@Auth::user()->role != 'read') {
    //                         $html .= '<div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
    //                                                                          <a  class="dropdown-toggle"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>

    //                                                                         <img src="public/img/dots.png?cache=1"   >
    //                                                                         </a>
    //                                          <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary">
    //                                          ';

    //                         $html .= '<a class="dropdown-item d-flex align-items-center px-0" href="contract?id=' . $l->id . '&page=' . ceil($l->rownumber / 10) . '" target="_blank">   <div style="  padding-left: 2px"><img src="public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > View Support Contract</div></a>  

    //                 </div>
    //                                                                    </div>';
    //                     }
    //                 }
    //                 $html .= '         </div>

    //                                         </div>    
    //                                 </div>
    //                             </div>
    //                             ';
    //             }

    //             $html .= '</div>
    //                                 <div class="col-sm-2">
    //                                                <div class="bubble-white-new bubble-text-sec" style="padding:10px">
    //                                                   <img src="public/img/signing-contract-icon-business-concept-flat-vector-6269121-removebg-preview.png?cache=1" style="width: 100%;">
    //                                                 </div> 

    //                                     </div>
    // </div> </div>';
    //         }



    //         if (sizeof($ssl_line_items_2) > 0) {
    //             $html .= '<div class="block new-block position-relative mt-4" >
    //                                                 <div class="top-div text-capitalize">SSL Certificates</div>

    //        <div class="block-content row new-block-content " id="commentBlock"><div class="col-lg-10">
    //  ';

    //             foreach ($ssl_line_items_2 as  $ssl_row) {

    //                 $html .= ' <div class="block block-rounded   table-block-new mb-2 pb-0  -   " data="' . $ssl_row->id . '" style="cursor:pointer;">

    //                          <div class="block-content align-items-center pt-1 pb-1 d-flex  pl-1 position-relative">

    //                                                                                  <div class="mr-1   p-2  justify-content-center align-items-center  d-flex" style="width:15%">';

    //                 if ($ssl_row->cert_type == 'internal') {
    //                     $html .= '<img src="public/client_logos/' . $ssl_row->logo . '" class="rounded-circle"  width="100%" style=" object-fit: cover;">';
    //                 } else {
    //                     $html .= '<img src="public/vendor_logos/' . $ssl_row->vendor_image . '" class="rounded-circle"  width="100%" style=" object-fit: cover;">';
    //                 }

    //                 $html .= '</div>
    //                                         <div class="  " style="width:45%">
    //                             <p class="font-12pt mb-0 text-truncate c1"><b>' . $ssl_row->client_display_name . '
    //                                                          </b></p>

    //                                                       <div class="d-flex">';
    //                 if ($ssl_row->cert_type == 'internal') {
    //                     $html .= '<p class="font-11pt mr-1   mb-0  c4-p  "  style="max-width:12%; " data-toggle="tooltip" data-title="Internal Certificate" data="' . $ssl_row->id . '">I</p>';
    //                 } else {

    //                     $html .= '   <p class="font-11pt mr-1   mb-0   c4-v  "  style="max-width:12%; " data-toggle="tooltip" data-title="Public Certificate" data="' . $ssl_row->id . '">P</p>';
    //                 }
    //                 $html .= '<p class="font-12pt mb-0 text-truncate   c4"  style="max-width:88%" data="' . $ssl_row->id . '">' . $ssl_row->cert_name . '</p>
    //                                                                        </div>



    //                                           <p class="font-12pt mb-0 text-truncate  c2">' . $ssl_row->description . '</p>

    //                                         </div>
    //                                         <div class=" text-right" style="width:25%;;">
    //                                                                             <div style="position: absolute;width: 100%; top: 10px;right: 10px;">';
    //                 $cert_edate = date('Y-M-d', strtotime($ssl_row->cert_edate));
    //                 $today = date('Y-m-d');
    //                 $earlier = new DateTime($ssl_row->cert_edate);
    //                 $later = new DateTime($today);

    //                 $abs_diff = $later->diff($earlier)->format("%a");





    //                 if ($ssl_row->cert_status == 'Active') {

    //                     if ($abs_diff <= 30) {
    //                         $html .= '<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-weight-bold text-dark"  >
    //                                                                                Upcoming</span>
    //                                                                     </div> ';
    //                     } else {
    //                         $html .= '<div class=" bg-new-green ml-auto  badge-new  text-center  font-weight-bold   text-white"  >
    //                                                                                  Active</span>
    //                                                                     </div>  ';
    //                     }
    //                 } elseif ($ssl_row->cert_status == 'Inactive') {

    //                     $html .= '<div class=" bg-new-blue ml-auto  badge-new   font-weight-bold  text-center  font-w600 text-white"  >
    //  <span class=" ">Renewed</span>
    //                                                                     </div>  ';
    //                 } elseif ($ssl_row->cert_status == 'Expired/Ended') {

    //                     $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
    //                                                                                Ended</span>

    //                                                                     </div>';
    //                 } elseif ($ssl_row->cert_status == 'Ended') {
    //                     $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
    //                                                                                Ended</span>
    //                                                                     </div>';
    //                 } elseif ($ssl_row->cert_status == 'Expired') {
    //                     $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
    //                                                                                <span class=" ">Expired</span>
    //                                                                     </div>';
    //                 }




    //                 $html .= '    </div>';
    //                 $san_items = DB::Table('ssl_san')->select('san')->where('ssl_id', $ssl_row->id)->groupBy('san')->orderBy('san', 'asc')->get();
    //                 $cvm1 = '<b class="HostActive text-white ">SANs</b><br>';
    //                 foreach ($san_items as $li) {

    //                     $cvm1 .= '<span class="SSLActive">' . $li->san . '</span><br>';
    //                 }

    //                 $html .= '     <div  class="" style="position: absolute;width: 100%; bottom:2px;right: 10px;display: flex;align-items: center;justify-content: end;">

    //                                                                       <div  class="ActionIcon" style="margin-left:2px;" data-src="public/img/icon-san-grey-darker.png?cache=1" data-original-src="public/img/icon-san-grey-darker.png?cache=1">
    //                                                                 ';
    //                 $html .= "<a href='javascript:;' class='toggle' data-toggle='tooltip' data-trigger='hover' data-placement='top'  data-html='true'   data-original-title='$cvm1'>";

    //                 $html .= '<img  src="public/img/icon-san-grey-darker.png?cache=1"  height="24px">
    //                                                                         </a>
    //                                                                     </div>
    //                                                                         <div class="ActionIcon"  data-src="public/img/calendar-grey-removebg-preview.png?cache=1" data-original-src="public/img/calendar-grey-removebg-preview.png?cache=1">';

    //                 $cvm = '<p class="HostActive text-white  my-0">Validity Range</p><p class="HostActive my-n1 text-orange"  >' . date('d-M-Y', strtotime($ssl_row->cert_rdate)) . '-' . date('d-M-Y', strtotime($ssl_row->cert_edate)) . '</p><p class="font-10pt mb-0 text-grey text-truncate mt-0"> <small><i>' . $abs_diff . ' days remaining</i></small></p>';

    //                 $html .= "
    //  <a href='javascript:;' class='toggle ' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title='' >
    //                              <img  src='public/img/calendar-grey-removebg-preview.png' width='24px'  class='' >
    //                         </a>
    //                                                                     </div>


    //  ";

    //                 if (Auth::check()) {
    //                     if (@Auth::user()->role != 'read') {
    //                         $html .= '<div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
    //                                                                          <a  class="dropdown-toggle"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>

    //                                                                         <img src="public/img/dots.png?cache=1"   >
    //                                                                         </a>
    //                                          <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary">';



    //                         $html .= '<a class="dropdown-item d-flex align-items-center px-0" target="_blank" href="ssl-certificate?id=' . $ssl_row->id . '&page=' . ceil($ssl_row->rownumber/10) . '">   <div style="  padding-left: 2px"><img src="public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > View SSL Certificate</div></a>  
    //                 </div>
    //                                                                    </div>';
    //                     }
    //                 }
    //                 $html .= '         </div>
    //                                    </div>    
    //                                 </div>
    //                             </div>
    //                             ';
    //             }

    //             $html .= '</div>
    //                                 <div class="col-sm-2">
    //                                                <div class="bubble-white-new bubble-text-sec" style="padding:10px">
    //                                                   <img src="public/img/internal-ssl.png?cache=1" style="width: 100%;">
    //                                                 </div> 

    //                                     </div>
    // </div> </div>';
    //         }


    //         $contract = DB::table('asset_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('asset_id', $q->id)->get();
    //         if (sizeof($contract) > 0) {
    //             $html .= '<div class="block new-block position-relative mt-4" >
    //                                                 <div class="top-div text-capitalize">Comments</div>

    //                                                           <div class="block-content new-block-content" id="commentBlock"> ';
    //             foreach ($contract as $c) {
    //                 $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
    //                                         <table class="table table-borderless table-vcenter mb-0">
    //                                             <tbody>
    //                                                 <tr>
    //                                                     <td class="text-center pr-0" style="width: 38px;">
    //                                                          <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
    //                                                           <img width="40px" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b></h1>
    //                                                     </td>
    //                                                     <td class="js-task-content  pl-0">
    //                                                         <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
    // </span></h2>
    //                                                     </td>

    //                                                 </tr>
    //                                                 <tr>
    //                                                     <td colspan="2" class="pt-0">
    //                                                        <p class="px-4 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
    // </p>
    //                                                     </td>

    //                                                 </tr>

    //                                         </tbody>
    //                                     </table>

    //                                     </div>';
    //             }
    //             $html .= '</div>

    //                             </div>';
    //         }




    //         $contract = DB::table('asset_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('asset_id', $q->id)->get();
    //         if (sizeof($contract) > 0) {
    //             $html .= '<div class="block new-block position-relative mt-4" >
    //                                                 <div class="top-div text-capitalize">Attachments</div>

    //                                                           <div class="block-content new-block-content  px-4 row" id="attachmentBlock"> ';
    //             foreach ($contract as $c) {

    //                 $f = explode('.', $c->attachment);
    //                 $fileExtension = end($f);
    //                 $icon = 'attachment.png';
    //                 if ($fileExtension == 'pdf') {
    //                     $icon = 'attch-Icon-pdf.png';
    //                 } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
    //                     $icon = 'attch-word.png';
    //                 } else if ($fileExtension == 'txt') {
    //                     $icon = 'attch-word.png';
    //                 } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
    //                     $icon = 'attch-excel.png';
    //                 } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
    //                     $icon = 'attch-png icon.png';
    //                 } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
    //                     $icon = 'attch-jpg-icon.png';
    //                 } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
    //                     $icon = 'attch-powerpoint.png';
    //                 }



    //                 $html .= '<div class="col-sm-12  ">
    //                                               <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
    //                                         <table class="table table-borderless table-vcenter mb-0">
    //                                             <tbody>
    //                                                 <tr>
    //                                                     <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
    //                                                          <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
    //                                                           <img width="40px" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b></h1>
    //                                                     </td>
    //                                                     <td class="js-task-content  pl-0">
    //                                                           <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
    // </span></h2>
    //                                                     </td>
    //                                                     <td class="text-right position-relative" style="width: auto;">


    //                                                        <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
    //                                                          <img src="public/img/trash--v1.png?cache=1" width="24px">
    //                                                         </a>  -->
    //                                                     </td>
    //                                                 </tr>
    //                                                 <tr>
    //                                                     <td colspan="3" class="pt-2">
    //                                                         <p class=" pb-0 mb-0">
    //  <a href="public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
    // </a></p>
    //                                                     </td>

    //                                                 </tr>

    //                                         </tbody>
    //                                     </table>
    //                                         </div>
    //                                     </div>';
    //             }
    //             $html .= '</div>

    //                             </div>';
    //         }


    //         $contract = DB::table('asset_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.asset_id', $q->id)->get();

    //         if (sizeof($contract) > 0) {
    //             $html .= '<div class="block new-block position-relative mt-4" >
    //                                                 <div class="top-div text-capitalize">Audit Trail</div>

    //                                                           <div class="block-content new-block-content" id="commentBlock">';
    //             foreach ($contract as $c) {
    //                 $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
    //                                         <table class="table table-borderless table-vcenter mb-0">
    //                                             <tbody>
    //                                                 <tr>
    //                                                     <td class="text-center pr-0" style="width: 38px;">
    //                                                          <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
    //                                                           <img width="40px" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b></h1>
    //                                                     </td>
    //                                                     <td class="js-task-content  pl-0">
    //                                                         <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
    // </span></h2>
    //                                                     </td>

    //                                                 </tr>
    //                                                 <tr>
    //                                                     <td colspan="2" class="pt-0">
    //                                                        <p class="px-4 mb-0 comments-section-text">  ' . $c->description . '
    // </p>
    //                                                     </td>

    //                                                 </tr>

    //                                         </tbody>
    //                                     </table>

    //                                     </div>';
    //             }
    //             $html .= '</div>

    //                             </div>';
    //         }




    //         $html .= '
    //     </div>


    //                     </div>



    //                 </div>
    //                </div>
    //        </div>';




    //         return response()->json($html);
    //     }


    public function getPhysicalContent(Request $request)
    {
        $id = $request->id;
        $html = '';
        $platform_name = '';

        $q = DB::table('assets as a')->select('a.*', 'sc.domain_name as system_category', 's.site_name', 'd.domain_name', 'c.client_display_name', 'o.operating_system_name', 'o.operating_system_image', 'm.vendor_name', 's.address', 's.city', 's.country', 's.phone', 's.zip_code', 's.province', 'at.asset_icon', 'at.asset_type_description', 'at.asset_type_description as asset_type_name', 'n.vlan_id as vlanId', 'n.subnet_ip', 'n.mask', 'usr.firstname as created_firstname', 'usr.lastname as created_lastname', 'upd.firstname as updated_firstname', 'upd.lastname as updated_lastname', 'c.logo', 'm.vendor_image', 'nz.network_zone_description', 'nz.tag_back_color', 'nz.tag_text_color')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->leftjoin('network as n', 'a.vlan_id', '=', 'n.id')->leftjoin('network_zone as nz', 'nz.network_zone_description', '=', 'n.zone')->leftjoin('users as usr', 'usr.id', '=', 'a.created_by')->leftjoin('users as upd', 'upd.id', '=', 'a.updated_by')->leftjoin('system_category as sc', 'a.system_category', '=', 'sc.id')->where('a.id', $id)->first();

        $asset_data = DB::table('asset_type')->where('asset_type_id', $q->asset_type_id)->first();
        if (strpos($q->operating_system_name, 'Windows') !== false) {
            $platform_name = 'Windows';
        } else if (strpos($q->operating_system_name, 'ESXi') !== false) {
            $platform_name = 'ESXi';
        } else {
            $platform_name = 'Linux';
        }
        $contract_ssl_line_items = DB::Table('contract_assets as ca')->selectRaw('a.contract_no,c.client_display_name,a.contract_status,a.contract_start_date,a.contract_end_date,v.vendor_image,a.contract_description,a.contract_type,a.id,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM contracts  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.contract_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('contracts  as a', 'a.id', '=', 'ca.contract_id')->join('clients as c', 'c.id', '=', 'a.client_id')->join('vendors as v', 'v.id', '=', 'a.vendor_id')->groupBy('a.id')->where('a.is_deleted', 0)->where('ca.is_deleted', 0)->orderBy('a.contract_start_date', 'desc')->get();
        $contract_ssl_line_items_active = DB::Table('contract_assets as ca')->selectRaw('a.contract_no,c.client_display_name,a.contract_status,a.contract_start_date,a.contract_end_date,v.vendor_image,a.contract_description,a.contract_type,a.id,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM contracts  where is_deleted=0  and contract_status="active"
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.contract_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('contracts  as a', 'a.id', '=', 'ca.contract_id')->where('a.contract_status', 'active')->join('clients as c', 'c.id', '=', 'a.client_id')->join('vendors as v', 'v.id', '=', 'a.vendor_id')->groupBy('a.id')->where('a.is_deleted', 0)->where('ca.is_deleted', 0)->orderBy('a.contract_start_date', 'desc')->get();

        $ssl_line_items_2_active = DB::Table('ssl_host as ca')->selectRaw(' a.cert_name , a.cert_status , a.cert_edate,a.cert_rdate,a.cert_sdate , a.cert_type , a.id , c.logo , v.vendor_image , a.description , c.client_display_name ,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM ssl_certificate  where is_deleted=0     and cert_status="active"
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.ssl_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('ssl_certificate  as a', 'a.id', '=', 'ca.ssl_id')->join('clients as c', 'c.id', '=', 'a.client_id')->leftjoin('vendors as v', 'v.id', '=', 'a.cert_issuer')->where('a.cert_status', 'active')->where('a.is_deleted', 0)->orderBy('a.cert_sdate', 'desc')->get();


        $ssl_line_items_2 = DB::Table('ssl_host as ca')->selectRaw(' a.cert_name , a.cert_status , a.cert_edate,a.cert_rdate,a.cert_sdate , a.cert_type , a.id , c.logo , v.vendor_image , a.description , c.client_display_name ,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM ssl_certificate  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.ssl_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('ssl_certificate  as a', 'a.id', '=', 'ca.ssl_id')->join('clients as c', 'c.id', '=', 'a.client_id')->leftjoin('vendors as v', 'v.id', '=', 'a.cert_issuer')->where('a.is_deleted', 0)->orderBy('a.cert_sdate', 'desc')->get();


        if ($q->AssetStatus == 1) {
            $html .= '<div class="block card-round   bg-new-green new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="' . asset("public/img/icon-active-removebg-preview.png") . '" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px;text-transform:capitalize">' . $platform_name . '  ' . $asset_data->asset_type_description . '</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>';
        } else {
            $html .= '<div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="' . asset("public/img/action-white-end-revoke.png") . '" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px;text-transform:capitalize">' . $platform_name . '  ' . $asset_data->asset_type_description . '</h4>';
            $renewed_qry = DB::Table('users')->Where('id', $q->InactiveBy)->first();



            $html .= '<p class="mb-0  header-new-subtext" style="line-height:17px">On ' . date('Y-M-d', strtotime($q->InactiveDate)) . ' by ' . @$renewed_qry->firstname . ' ' . @$renewed_qry->lastname . '</p>
                                    </div>
                                </div>';
        }


        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print"> <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" style="margin-right: -3px;"> 
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="' . asset('public/img/paper-clip-white.png') . '" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="' . asset('public/img/comment-white.png') . '" width="20px"></a></span>';

        if (Auth::user()->role != 'read') {



            if ($q->AssetStatus == 1) {
                $html .= '<span  > 
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->AssetStatus . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Decomission" class=" "><img src="' . asset("public/img/action-white-end-revoke.png?cache=1") . '" width="22px"></a>
                                         </span>';
            } else {
                $html .= '    <span  > 
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->AssetStatus . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="' . asset("public/img/icon-header-white-reactivate.png?cache=1") . '" width="22px"></a>
                                         </span>';
            }
        }

        $html .= ' <a href="javascript:;" data="' . $q->id . '" class="btn-clone" data-type="' . ucfirst($q->asset_type) . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Clone"  style="padding:5px 7px">
                                                <img src="' . asset("public/icons/icon-white-clone.png?cache=1") . '" width="22px"  >
        <a  target="_blank" href="' . url("pdf-asset") . '?id=' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Pdf"  style="padding:5px 7px">
                                                <img src="' . asset("public/img/action-white-pdf.png?cache=1") . '" width="26px"  >
                                            </a>
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="' . asset("public/img/action-white-print.png?cache=1") . '" width="20px">
                                            </a>';


        if (Auth::user()->role != 'read') {

            $html .= '<a   href="' . url("edit-assets") . '?id=' . $q->id . '" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="' . asset("public/img/action-white-edit.png?cache=1") . '" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="' . asset("public/img/action-white-delete.png?cache=1") . '" width="17px"></a>';
        }


        $html .= '</div></div>
                            </div>
                        </div>

                            
                            <div class="block new-block pb-0 mt-4 " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content- push mb-0" jid >
                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">General Info</div>
                            <div class="col-sm-12">
                        <input type="hidden" name="attachment_array" id="attachment_array">
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Client</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->client_display_name . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Site</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->site_name . '
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Sub-Type</div> 
                                       </div>
                                            <div class="col-sm-4">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    Device
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Device Type</div> 
                                       </div>
                                            <div class="col-sm-4">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . ucfirst($q->asset_type) . '
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Asset Type</div> 
                                       </div>
                                            <div class="col-sm-4">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $asset_data->asset_type_description . '
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Platform</div> 
                                       </div>
                                            <div class="col-sm-4">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . ($q->platform ? $q->platform : $platform_name) . '
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                     <div class="form-group row">
                                                    <div class="col-sm-4">
                                                    <div class="bubble-new">Description</div> 
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->role . '
                                                    
                                                    </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         
                                    </div>
                                    
                                    <div class="col-sm-2">
                                               <div class="bubble-text-sec" style="padding:10px">
                                         

                                                      <img src="' . asset("public") . '/client_logos/' . $q->logo . '" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>';






        if ($q->asset_type == 'physical') {

            $html .= '
                            <div class="block new-block pb-0 mt-4 " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content-  push mb-0" >
 <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Hardware Information</div>
                            
                            <div class="col-sm-12" >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Manufacturer</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->vendor_name . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Model</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->model . '
                                                
                                                </div> 
                                     
                                            </div>
                                            <div class="col-sm-3">
                                           <div class="bubble-new">Type</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->type . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                         <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Serial Number</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec" style="color: #4194F6;">
                                               <b>' . $q->sn . '</b>
                                                  
                                                </div> 
                                     
                                            </div>
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Asset Tag</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->asset_tag . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        ';
            if ($q->asset_type_description == 'Physical Server') {
                $html .= '<div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">CPU Model</div> 
                                       </div>
                                            <div class="col-sm-9">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->cpu_model . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                        <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">No. of Sockets</div> 
                                       </div>
                                            <div class="col-sm-1">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->cpu_sockets . '
                                                    </div> 
                                     
                                            </div>
                                         <div class="col-sm-2">
                                           <div class="bubble-new">No. of Cores</div> 
                                       </div>
                                            <div class="col-sm-1">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->cpu_cores . '
                                                    </div> 
                                     
                                            </div>
                                         <div class="col-sm-2">
                                           <div class="bubble-new">Frequency</div> 
                                       </div>
                                            <div class="col-sm-2">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->cpu_freq . '-GHz
                                                    </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        ';
            }

            $html .= '<div class="form-group row">
                                 <div class="col-sm-3">
                                           <div class="bubble-new">Memory (GB)</div> 
                                       </div>
                                            <div class="col-sm-2">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->memory . '  
                                                  
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
       ';
        }


        if ($q->asset_type == 'physical') {
            $assets_location = DB::table('assets_location')->where('asset_id', $id)->where('is_deleted', 0)->get();
            if (count($assets_location) > 0) {
                $html .= '<div class="block new-block mt-4" style="padding-top: 0mm !important;">
                                    <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
                                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Location Information</div>
                                                                        
                                            <div class="col-sm-12" >';
                foreach ($assets_location as $al) {
                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false" data="${i}">
                                <table class="table table-borderless table-vcenter mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-center pr-0" style="width: 50px;">
                                                 <img src="' . asset("public") . '/icons/database.png" alt="" style="width: 50px;">
                                            </td>
                                            <td class="js-task-content  pl-3">
                                                <h2 class="mb-0 comments-text">
                                                    <div style="display: flex;align-items: center;">' . (isset($al->room) ? $al->room : '') . ' &nbsp;&nbsp</div><span class="comments-subtext ml-0 mb-0 d-flex" style="display: block;margin: .3rem;"><div class="mr-2 location-tag" style="width: fit-content;padding: 1px 16px;border: 1px solid lightgrey;border-radius: 5px;">Floor: ' . (isset($al->floor) ? $al->floor : '') . '</div><div class="mr-2 location-tag ' . ($al->racked == 1 ? '' : 'd-none') . '" style="width: fit-content;padding: 1px 16px;border: 1px solid lightgrey;border-radius: 5px;">Cabinet/Rack: ' . (isset($al->cabinet_rack) ? $al->cabinet_rack : '') . '</div><div class="mr-2 location-tag ' . ($al->racked == 1 ? '' : 'd-none') . '" style="width: fit-content;padding: 1px 16px;border: 1px solid lightgrey;border-radius: 5px;">U-Location: ' . (isset($al->u_location) ? $al->u_location : '') . '</div></span></h2>
                                            </td>
                                            <td style="width: 7%;" class="text-right  "><div class="d-flex  align-items-center">
                                                <div class="mr-2 location-tag" style="width: fit-content;padding: 5px 20px;border: 1px solid lightgrey;border-radius: 5px;white-space: nowrap;">' . (isset($al->contact_name) ? $al->contact_name : '') . '</div>
                                            </div></td>
                                        </tr>
                                </tbody>
                            </table>    
                            </div>';
                }
                $html .= '</div> 
                                    </div>
                                    
                                    </div>      

                                </div>
                                </div>
                                    ';
            }
        }



        $html .= '<div class="block new-block pb-0 mt-4 ' . ($q->network_connected == 1 || $q->asset_type == 'virtual' ? '' : 'd-none') . ' " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content-  push mb-0" >
                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Device Information</div>
                            <div class="col-sm-12">
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-12">
                                    <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Operating System</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->operating_system_name . '</b></div> 
                                     
                                            </div>

                                         </div>';

        $domain_data = DB::table('domains')->where('id', $q->domain)->first();
        $html .= '
                                        
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Hostname</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec" style="color: #4194F6; text-transform: uppercase;"><b>
                                                    ' . $q->hostname . '</b>
                                                  
                                                </div> 
                                     
                                            </div>
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Domain</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . @$domain_data->domain_name . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                                <div class="col-sm-3">
                                                     <div class="bubble-new">System Type</div> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->use_ . '
                                                    </div> 
                                                </div> 
                                                <div class="col-sm-3">
                                                    <div class="bubble-new">System Category</div> 
                                                </div>
                                                <div class="col-sm-3">
                                                        <div class="bubble-white-new bubble-text-sec">
                                                        ' . $q->system_category . '
                                                        
                                                        </div> 
                                                </div> 
                                                </div>';
        if ($q->platform == 'Windows') {
            $ad_domain_data = DB::table('domains')->where('id', @$q->device_info_ad_domain)->first();
            $html .= '<div class="form-group row">
                                                 <div class="col-sm-3">
                                                   <div class="bubble-new">A/D Domain</div> 
                                               </div>
                                                    <div class="col-sm-3">
                                                          <div class="bubble-white-new bubble-text-sec">
                                                            ' . @$ad_domain_data->domain_name . '
                                                          
                                                        </div> 
                                             
                                                    </div>
                                                 <div class="col-sm-3">
                                                   <div class="bubble-new">A/D OU</div> 
                                               </div>
                                                    <div class="col-sm-3">
                                                          <div class="bubble-white-new bubble-text-sec">
                                                            ' . $q->device_info_ou . '
                                                          
                                                        </div> 
                                             
                                                    </div>
                                                  
                                                </div>';
        }
        $html .= '</div>
                                        </div>
        
                                    </div>
                                    

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
         

         ';
        $sla = DB::table('sla')
            ->where('is_deleted', 0)
            ->get();
        $html .= '<div class="block new-block pb-0 mt-4 ' . ($q->network_connected == 1 || $q->asset_type == 'virtual' ? '' : 'd-none') . ' " style="padding-left: 20px;padding-right: 20px;">
                             
         <div class="justify-content-  push" >
<div class="mb-3" style="font-size: 20px; font-weight: 600;">Critical Information</div>
<div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">End of Life</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . (isset($q->end_of_life) ? date('d-M-Y', strtotime($q->end_of_life)) : "Current") . '
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>';
        $maintenance = DB::table('asset_maintenance')->where('asset_id', $id)->where('is_deleted', 0)->first();
        if ($maintenance) {
            $detailText = '';
            if ($maintenance->frequency == 'Daily' || $maintenance->frequency == 'Weekly') {
                $detailText = $maintenance->day . ' @ ' . $maintenance->start_time . ' ' .
                    $maintenance->time_zone . ' for ' . $maintenance->duration_hours . '-hours';
            } else if ($maintenance->frequency == 'Monthly') {
                $detailText = $maintenance->occurance . ' ' . $maintenance->day . ' @ ' .
                    $maintenance->start_time . ' ' .
                    $maintenance->time_zone . ' for ' . $maintenance->duration_hours . '-hours';
            } else if ($maintenance->frequency == 'Yearly') {
                $detailText = $maintenance->occurance . ' ' . $maintenance->day . ' of ' .
                    $maintenance->month . ' @ ' . $maintenance->start_time . ' ' .
                    $maintenance->time_zone . ' for ' . $maintenance->duration_hours . '-hours';
            }

            $html .= '
 
              <div class="js-task block block-rounded mb-2  maintenance-tags" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                      <table class="table table-borderless table-vcenter mb-0">
                                          <tbody>
                                              <tr>
                                                  <td class="text-center pr-0" style="width: 50px;">
                                                      

                                                          <div class="d-flex align-items-center " style="width: 180px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags" data-toggle="tooltip"  data-original-title="" title="">

                                                    ' . $maintenance->frequency . '</p>
                                                    
                                                       
                                      </div>



                                                  </td>
                                                  <td class="js-task-content  pl-0">
                                                      
                                                          <h2 class="mb-0 comments-text">
                                                        <div style="display: flex;align-items: center;color:#595959!important;font-size:13pt!important">
                                                        ' . $detailText . '  </div><span class="comments-subtext ml-0" style="display: block;color:#009E04!important;font-size:10pt!important">Maintenance Window</span>
                                                    </h2>


                                                  </td>
                                                 
                                                 <td class="text-right">';
            // if ($q->managed == 1) {

            //     foreach ($sla as $key => $s) {
            //         if ($q->sla == $s->id) {
            //             $html .= ' <div class=" contract_type_button_manage_sm contract_type_button_og' . $key . ' mr-4  ">

            //                         <input type="radio" class="custom-control-input" id="sla1' . $key . '" name="sla1"   checked >
            //                         <label class="btn btn-new w-75 mb-0" data-toggle="tooltip" data-title="SLA"
            //                             for="sla1' . $key . '">' . $s->sla_description . ' </label>
            //                     </div>';
            //         }
            //     }
            // }

            $html .= '

</td>
                                              </tr>
                                      </tbody>
                                  </table>

                                  </div>

             ';
        } else {



            $html .= '
 
              <div class="js-task block block-rounded mb-2  maintenance-tags" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                      <table class="table table-borderless table-vcenter mb-0">
                                          <tbody>
                                              <tr>
                                                  <td class="text-center pr-0" style="width: 50px;">
                                                      

                                                          <div class="d-flex align-items-center " style="width: 180px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags" data-toggle="tooltip"  data-original-title="" title="">

                                                    NOT SET</p>
                                                    
                                                       
                                      </div>



                                                  </td>
                                                  <td class="js-task-content  pl-0">
                                                      
                                                          <h2 class="mb-0 comments-text">
                                                        <div style="display: flex;align-items: center;color:#595959!important;font-size:13pt!important">
                                                        Undefined  </div><span class="comments-subtext ml-0" style="display: block;color:#3F3F3F!important;font-size:10pt!important">Maintenance Window</span>
                                                    </h2>


                                                  </td>
                                                 
                                                 <td class="text-right">';
            if ($q->managed == 1) {

                foreach ($sla as $key => $s) {
                    if ($q->sla == $s->id) {
                        $html .= ' <div class=" contract_type_button_manage_sm contract_type_button_og' . $key . ' mr-4  ">
                                        
                                    <input type="radio" class="custom-control-input" id="sla1' . $key . '" name="sla1"   checked >
                                    <label class="btn btn-new w-75 mb-0" data-toggle="tooltip" data-title="SLA"
                                        for="sla1' . $key . '">' . $s->sla_description . ' </label>
                                </div>';
                    }
                }
            }

            $html .= '

</td>
                                              </tr>
                                      </tbody>
                                  </table>

                                  </div>

             ';
        }





        $html .= '<div class="js-task block block-rounded mb-2 maintenance-tags " data-task-id="9" data-task-completed="false" data-task-starred="false">
                                      <table class="table table-borderless table-vcenter mb-0">
                                          <tbody>
                                              <tr>
                                                  <td class="text-center pr-0" style="width: 40px;">
                                                      

                                                          <div class="d-flex align-items-center " style="width: 150px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags" data-toggle="tooltip" data-original-title="" title="">

                                                      TAGS </p>
                                                    
                                                       
                                      </div>



                                                  </td>
                                                  <td class="pl-4 pr-0">
                                                       
<div class="contract_type_button_manage_sm contract_type_button_og   js-tooltip-enabled" >
                                      <input type="checkbox" class="custom-control-input"  
                                        name="" value="1"
                                        ' . ($q->disaster_recovery1 == 1 ? 'checked' : '') . '>
                                    <label class="btn btn-new w-75 mb-0 px-0"   style="width:100px!important" for=" ">D/R Plan</label>
                                    </div>

                                                  </td>

                                                   <td class="px-0">
                                                       
<div class="contract_type_button_manage_sm contract_type_button_og     js-tooltip-enabled" >
                             <input type="checkbox" class="custom-control-input" 
                                        value="1" ' . ($q->ntp == 1 ? 'checked' : '') . '>
                                    <label class="btn btn-new w-75 mb-0 px-0"  style="width:100px!important" for="ntp" data-toggle="tooltip"
                                        data-trigger="hover" data-html="true"
                                        title="Allows you to assign SSL Certs to asset"> SSL Certificate</label>
                                    </div>

                                                  </td>


<td class="px-0">
                                                       
<div class="contract_type_button_manage_sm contract_type_button_og    js-tooltip-enabled" >
                            <input type="checkbox" class="custom-control-input" 
                                         value="1" ' . ($q->HasWarranty == 1 ? 'checked' : '') . '>
                                    <label class="btn btn-new w-75 px-0 mb-0   "  style="width:100px!important" for=" " data-toggle="tooltip"
                                        data-trigger="hover" data-html="true"
                                        title="' . ($q->HasWarranty == 1 ? 'Allows you to assign contracts to asset' : $q->NotSupportedReason) . '">
                                        ' . ($q->HasWarranty == 1 ? 'Supported' : 'Unsupported') . '</label>
                                    </div>

                                                  </td>


<td class="px-0">
                                                       
<div class="contract_type_button_manage_sm contract_type_button_og  js-tooltip-enabled" >
                          <input type="checkbox" class="custom-control-input"  
                                        value="1" ' . ($q->clustered == 1 ? 'checked' : '') . '>
                                    <label class="btn btn-new w-75 mb-0 px-0"  for=" "  style="width:100px!important"> Clustered</label>
                                    </div>

                                                  </td>

<td class="px-0">
                                                       
<div class="contract_type_button_manage_sm contract_type_button_og     js-tooltip-enabled" >
                          <input type="checkbox" class="custom-control-input"  value="1"
                                        ' . ($q->internet_facing == 1 ? 'checked' : '') . '>

                                    <label class="btn btn-new w-75 px-0 mb-0" for=" "  style="width:100px!important"> Internet Facing</label>
                                    </div>

                                                  </td>


<td class="px-0">
                                                       
<div class="contract_type_button_manage_sm contract_type_button_og    js-tooltip-enabled" >
                    <input type="checkbox" class="custom-control-input"   value="1"
                                        ' . ($q->load_balancing == 1 ? 'checked' : '') . '>
                                    <label class="btn btn-new  mb-0 px-0" style="width:100px!important"  for=" "> Load Balanced</label>
                                    </div>

                                                  </td>


                                                 
                                              </tr>
                                      </tbody>
                                  </table>

                                  </div></div>';


        // if ($q->managed == 1) {

        $html .= '<div class="block new-block pb-0 mt-4  ' . ($q->network_connected == 1 || $q->asset_type == 'virtual' ? '' : 'd-none') . '" style="padding-left: 20px;padding-right: 20px;">
                     
                        <div class="row justify-content-  push mb-0">
                    <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Managed Services</div>
                    <div class="col-sm-12 m-
                    " >
                             
                <input type="hidden" name="attachment_array" id="attachment_array" >
                        <div class="row">
<div class="col-sm-12">
<div class="form-group row">
                                                        <div class="col-sm-3">
                                                        <div class="bubble-new">Status</div> 
                                                    </div>
                                                        <div class="col-sm-3">
                                                                <div class="bubble-white-new bubble-text-sec"><b style="color: #4194F6;">
                                                                    ' . ($q->managed == 1 ? 'Managed' : 'Unmanaged') . '  </b>
                                                                
                                                                </div> 
                                                
                                                        </div>';
        if ($q->managed == 1) {
            $html .= '<div class="col-sm-3  d-flex align-items-center ">';
            foreach ($sla as $key => $s) {
                if ($q->sla == $s->id) {
                    $html .= ' <div class=" contract_type_button_manage_sm contract_type_button_og' . $key . ' mr-4  ">
                                        
                                    <input type="radio" class="custom-control-input" id="sla' . $key . '" name="sla"   checked >
                                    <label class="btn btn-new w-75 " data-toggle="tooltip" data-title="SLA"
                                        for="sla' . $key . '">' . $s->sla_description . ' </label>
                                </div>';
                }
            }

            $html .= '</div>';
        }



        $html .= '</div>
                                                        ';
        if ($q->managed == 1) {
            $html .= '
                                <div class="form-group    row">

                                       <div class="col-sm-3 form-group ">
                                   <div class="bubble-new">App Owner
</div> 
                               </div><div class="col-sm-9 form-group ">
                                          <div class="bubble-white-new bubble-text-first">
                                           ' . $q->app_owner . ' 
                                          
                                        </div> 
                             
                                    </div>
                                  


                                                
                               </div>                           
                                   
                                      
                                                ';


            $html .= '
                                    </div> 
                                    </div>
                        <div class="row">
  
                                       <div class="col-sm-3 form-group ">
                                   <div class="bubble-new">Services</div>
                                   </div> 
                                 <div class="col-sm-9 pr-3  ">
<div class="row">


                                <div class=" mb-3 " style="width:20%" data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="System is patched automatically or manually">

                                    <div class="contract_type_button_manage_sm  contract_type_button_og mr-4  px-3">
                                        <input type="checkbox" class="custom-control-input"  
                                         value="1" ' . ($q->patched == 1 ? 'checked' : '') . '>
                                        <label class="btn btn-new w-100 " > Patched</label>
                                    </div>
                                </div>

                                <div class=" mb-3"  style="width:20%" data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="System is monitored">

                                    <div class="contract_type_button_manage_sm  contract_type_button_og  mr-4  px-3">
                                        <input type="checkbox" class="custom-control-input"   value="1" ' . ($q->monitored == 1 ? 'checked' : '') . '>
                                        <label class="btn btn-new w-100 "  >Monitored</label>
                                    </div>
                                </div>

                                <div class="mb-3"  style="width:20%" data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="System data is protected">

                                    <div class="contract_type_button_manage_sm contract_type_button_og    mr-4 px-3 ">
                                        <input type="checkbox" class="custom-control-input"  value="1" ' . ($q->backup == 1 ? 'checked' : '') . '>
                                        <label class="btn btn-new w-100 " >Backup</label>
                                    </div>
                                </div>

 <div class="  mb-3"  style="width:20%"  data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="System is replicated">

                                    <div class="contract_type_button_manage_sm  contract_type_button_og  mr-4  px-3">
                                        <input type="checkbox" class="custom-control-input"  value="1"
                                            ' . ($q->replicated == 1 ? 'checked' : '') . '>
                                        <label class="btn btn-new w-100 "  >Replicated
                                        </label>
                                    </div>
                                </div>
                                   <div class="   "  style="width:20%" data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="System requires SMTP Relay Access"> 

                                    <div class="contract_type_button_manage_sm    contract_type_button_og  px-3 ">
                                        <input type="checkbox" class="custom-control-input"  value="1" ' . ($q->smtp == 1 ? 'checked' : '') . '>
                                        <label class="btn btn-new w-100 "  >SMTP</label>
                                    </div>
                                </div>

                                <div class="   mb-3"  style="width:20%" data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="System has Anti-Virus installed">

                                    <div class="contract_type_button_manage_sm  contract_type_button_og mr-4 px-3">
                                        <input type="checkbox" class="custom-control-input"  value="1" ' . ($q->antivirus == 1 ? 'checked' : '') . '>
                                        <label class="btn btn-new w-100 " >Anti-Virus
                                        </label>
                                    </div>
                                </div>

                                   <div class=""  style="width:20%"  data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="EDR">

                                    <div class="contract_type_button_manage_sm  contract_type_button_og   px-3">
                                        <input type="checkbox" class="custom-control-input"  value="1" ' . ($q->edr == 1 ? 'checked' : '') . '>
                                        <label class="btn btn-new w-100 "  >EDR</label>
                                    </div>
                                </div>
                               
                                <div class="  "  style="width:20%"  data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="System is scanned by Drawbridge">

                                    <div class="contract_type_button_manage_sm contract_type_button_og  mr-4 px-3">
                                        <input type="checkbox" class="custom-control-input"  value="1"
                                            ' . ($q->disaster_recovery == 1 ? 'checked' : '') . '>
                                        <label class="btn btn-new w-100 " >Vuln Scan</label>
                                    </div>
                                </div>

                                <div class="  "  style="width:20%" data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="System sends info to SIEM/Syslog">


                                    <div class="contract_type_button_manage_sm contract_type_button_og mr-4  px-3">
                                        <input type="checkbox" class="custom-control-input"  value="1" ' . ($q->syslog == 1 ? 'checked' : '') . '>
                                        <label class="btn btn-new w-100 "  >SIEM</label>
                                    </div>
                                </div>

                                     <div class=""  style="width:20%"  data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="2FA">


                                    <div class="contract_type_button_manage_sm mr-4  contract_type_button_og  px-3">
                                        <input type="checkbox" class="custom-control-input"   value="1" ' . ($q->twofa == 1 ? 'checked' : '') . '>
                                        <label class="btn btn-new w-100 "  >2FA</label>
                                    </div>
                                </div>
                            </div>


';
        }
        $html .= '</div>
                           
                           

                  </div>
                              
                                       </div>      

                                     

';

        $html .= '




</div>
</div>

';
        // }

        $html .= '<div class="row"><div class="col-6"><div class="block new-block position-relative mt-0 mb-0"  style="padding-left: 16px;padding-right: 16px;">
                                        <div class="mb-3" style="font-size: 20px; font-weight: 600;">Support Contracts
                                        <ul class="nav nav-tabs NewNavtab justify-content-end float-right" id="contractTab" role="tablist">
  <li class="nav-item mr-3" role="presentation">
    <a class="nav-link active" id="Active-tab"   data-toggle="tab" href="#Active" role="tab" aria-controls="showAll" aria-selected="true">Active</a>
   
  </li>
   <li class="nav-item " role="presentation">
  
    <a class="nav-link  " id="showAll-tab" data-toggle="tab" href="#showAll" role="tab" aria-controls="showAll"  >Show All</a>

  </li>
</ul>
                                        </div>
                     

<div class="block-content row new-block-content  pt-0 px-0" id="commentBlock" style="max-height: 470px; overflow-y: auto;">';
        if ($q->HasWarranty == 1) {

            if (sizeof($contract_ssl_line_items) > 0) {
                $html .= '
<div class="tab-content mt-3" id="contractTabContent">
  <div class="tab-pane fade show active" id="Active" role="tabpanel" aria-labelledby="showAll-tab">
  
';



                $html .= '
            <div class="col-lg-12 px-0" style="min-height:200px; ;max-height: 312px; overflow-y: auto; overflow-x: hidden;">';
                foreach ($contract_ssl_line_items_active as $l) {
                    $rownumber = ceil($l->rownumber / 10);
                    $contract_end_date = date('Y-M-d', strtotime($l->contract_end_date));
                    $today = date('Y-m-d');
                    $earlier = new DateTime($l->contract_end_date);
                    $later = new DateTime($today);

                    $abs_diff = $later->diff($earlier)->format("%a"); //3

                    $html .= '
<div class="block block-rounded align-items-center  table-block-new mb-2 pb-0 " data="' . $l->id . '" style="cursor:pointer;">
            
         
                <div class="block-content pt-1 pb-1 d-flex align-items-center pl-1 position-relative">
                            

                                 <div class="mr-1  justify-content-center align-items-center  d-flex" style="width:20%; padding: 7px;">
                                    <img src="' . asset("public") . '/vendor_logos/' . $l->vendor_image . '"  class="rounded-circle  "  width="100%" style=" object-fit: cover;">
                                </div>


                             <div class="  " style="width:59%">
                                     <p class="font-12pt mb-0 text-truncate font-w600 c1">' . $l->client_display_name . '</p>

                                       <div class="d-flex">';
                    if ($l->contract_type == 'Hardware Support') {
                        $html .= '<p class="font-11pt mr-1   mb-0  c4-p  "  style="max-width:12%; " data-toggle="tooltip" data-title="Hardware Support" data="' . $l->id . '">H</p>';
                    } elseif ($l->contract_type == 'Software Support') {
                        $html .= ' <p class="font-11pt mr-1   mb-0  c4-s  "  style="max-width:12%; " data-toggle="tooltip" data-title="Software Support" data="' . $l->id . '">S</p>';
                    } else {

                        $html .= '<p class="font-11pt mr-1   mb-0   c4-v  "  style="max-width:12%; " data-toggle="tooltip" data-title="Subscription" data="' . $l->id . '">C</p>';
                    }
                    $html .= '<p class="font-12pt mb-0 text-truncate   c4"  style="width:90%" data="' . $q->id . '">' . $l->contract_no . '</p></div>

                      
                                            <p class="font-12pt mb-0 text-truncate c2">' . $l->contract_description . '</p> 
                                </div>
                                <div class=" text-right" style="width:25%;;">
                                                                    <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                       

';

                    $contract_end_date = date('Y-M-d', strtotime($l->contract_end_date));
                    $today = date('Y-m-d');
                    $earlier = new DateTime($l->contract_end_date);
                    $later = new DateTime($today);

                    $abs_diff = $later->diff($earlier)->format("%a"); //3




                    if ($l->contract_status == 'Active') {

                        if ($abs_diff <= 30) {
                            $html .= '<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-weight-bold   text-dark"  >
                                                                        <span class=" ">Upcoming</span>
                                                            </div> ';
                        } else {
                            $html .= ' <div class=" bg-new-green ml-auto  badge-new  text-center font-weight-bold   text-white"  >
                                                                         <span class=" ">Active</span>
                                                            </div>  
                                                            ';
                        }
                    } elseif ($l->contract_status == 'Inactive') {

                        $html .= '<div class=" bg-new-blue ml-auto  badge-new  font-weight-bold    text-center  font-w600 text-white"  >
                                                                          <span class=" ">Renewed</span>
                        
                                                            </div>  ';
                    } elseif ($l->contract_status == 'Expired/Ended') {

                        $html .= '   <div class=" bg-new-red ml-auto  font-weight-bold    badge-new  text-center  font-w600 text-white"  >
                                                                          <span class=" ">Ended</span>
                        
                                                            </div>';
                    } elseif ($l->contract_status == 'Ended') {
                        $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center  font-weight-bold   0 text-white"  >
                                                                          <span class=" ">Ended</span>
                                                            </div>';
                    } elseif ($l->contract_status == 'Expired') {
                        $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   text-white"  >
                                                                          <span class=" ">Expired</span>
                                                            </div>';
                    }





                    $html .= '    </div>';
                    $ssl_line_items = DB::Table('contract_assets as ca')->select('a.hostname', 'a.AssetStatus')->where('ca.contract_id', $l->id)->join('assets as a', 'a.id', '=', 'ca.hostname')->where('ca.is_deleted', 0)->orderBy('a.hostname', 'asc')->get();
                    $cvm = '<b class="HostActive text-white">Assigned Assets</b><br>';
                    foreach ($ssl_line_items as $v) {
                        if ($v->AssetStatus != '1') {
                            $cvm .= '<span class="HostInactive text-uppercase">' . $v->hostname . '</span><br>';
                        } else {
                            $cvm .= '<span class="HostActive text-uppercase">' . $v->hostname . '</span><br>';
                        }
                    }

                    $contract_end_date = date('Y-M-d', strtotime($l->contract_end_date));
                    $today = date('Y-m-d');
                    $earlier = new DateTime($l->contract_end_date);
                    $later = new DateTime($today);

                    $abs_diff = $later->diff($earlier)->format("%a"); //3

                    $cvm = '<p class="HostActive text-white  my-0">Validity Range</p><p class="HostActive my-n1 text-orange"  >' . date('d-M-Y', strtotime($l->contract_start_date)) . '-' . date('d-M-Y', strtotime($l->contract_end_date)) . '</p><p class="font-10pt mb-0 text-grey text-truncate mt-0"> <small><i>' . $abs_diff . ' days remaining</i></small></p>';
                    $html .= "<div    style='position: absolute;width: 100%; bottom: 2px;right: 10px;display: flex;align-items: center;justify-content: end;'>
                                                                

<div class='ActionIcon'  data-src='../public/img/calendar-grey-removebg-preview.png?cache=1' data-original-src='../public/img/calendar-grey-removebg-preview.png'>
<a href='javascript:;' class='toggle '' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title='' >
                     <img  src='../public/img/calendar-grey-removebg-preview.png' width='24px'  class='' >
                </a>
                                                            </div>


";
                    if (Auth::check()) {
                        if (@Auth::user()->role != 'read') {
                            $html .= '<div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
                                                                 <a  class="dropdown-toggle"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>
                                                               
                                                                <img src="../public/img/dots.png?cache=1"   >
                                                                </a>
                                 <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary">
                                 ';

                            $html .= '<a class="dropdown-item d-flex align-items-center px-0" href="' . url("contract") . '?id=' . $l->id . '&page=' . ceil($l->rownumber / 10) . '" target="_blank">   <div style="  padding-left: 2px"><img src="../public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > View Support Contract</div></a>  
           
        </div>
                                                           </div>';
                        }
                    }

                    $html .= '      
                                                 
                                </div>    
                           </div>     
                    </div> </div>
                    ';
                }

                $html .= ' </div>   </div>   
                 <div class="tab-pane fade    " id="showAll" role="tabpanel" aria-labelledby="showAll-tab">';


                $html .= '
            <div class="col-lg-12 px-0" style="min-height:200px; ;max-height: 312px; overflow-y: auto; overflow-x: hidden;">';
                foreach ($contract_ssl_line_items as $l) {
                    $rownumber = ceil($l->rownumber / 10);
                    $contract_end_date = date('Y-M-d', strtotime($l->contract_end_date));
                    $today = date('Y-m-d');
                    $earlier = new DateTime($l->contract_end_date);
                    $later = new DateTime($today);

                    $abs_diff = $later->diff($earlier)->format("%a"); //3

                    $html .= '
<div class="block block-rounded align-items-center  table-block-new mb-2 pb-0 " data="' . $l->id . '" style="cursor:pointer;">
            
         
                <div class="block-content pt-1 pb-1 d-flex align-items-center pl-1 position-relative">
                            

                                 <div class="mr-1  justify-content-center align-items-center  d-flex" style="width:20%; padding: 7px;">
                                    <img src="' . asset("public") . '/vendor_logos/' . $l->vendor_image . '"  class="rounded-circle  "  width="100%" style=" object-fit: cover;">
                                </div>


                             <div class="  " style="width:59%">
                                     <p class="font-12pt mb-0 text-truncate font-w600 c1">' . $l->client_display_name . '</p>

                                       <div class="d-flex">';
                    if ($l->contract_type == 'Hardware Support') {
                        $html .= '<p class="font-11pt mr-1   mb-0  c4-p  "  style="max-width:12%; " data-toggle="tooltip" data-title="Hardware Support" data="' . $l->id . '">H</p>';
                    } elseif ($l->contract_type == 'Software Support') {
                        $html .= ' <p class="font-11pt mr-1   mb-0  c4-s  "  style="max-width:12%; " data-toggle="tooltip" data-title="Software Support" data="' . $l->id . '">S</p>';
                    } else {

                        $html .= '<p class="font-11pt mr-1   mb-0   c4-v  "  style="max-width:12%; " data-toggle="tooltip" data-title="Subscription" data="' . $l->id . '">C</p>';
                    }
                    $html .= '<p class="font-12pt mb-0 text-truncate   c4"  style="width:90%" data="' . $q->id . '">' . $l->contract_no . '</p></div>

                      
                                            <p class="font-12pt mb-0 text-truncate c2">' . $l->contract_description . '</p> 
                                </div>
                                <div class=" text-right" style="width:25%;;">
                                                                    <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                       

';

                    $contract_end_date = date('Y-M-d', strtotime($l->contract_end_date));
                    $today = date('Y-m-d');
                    $earlier = new DateTime($l->contract_end_date);
                    $later = new DateTime($today);

                    $abs_diff = $later->diff($earlier)->format("%a"); //3




                    if ($l->contract_status == 'Active') {

                        if ($abs_diff <= 30) {
                            $html .= '<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-weight-bold   text-dark"  >
                                                                        <span class=" ">Upcoming</span>
                                                            </div> ';
                        } else {
                            $html .= ' <div class=" bg-new-green ml-auto  badge-new  text-center font-weight-bold   text-white"  >
                                                                         <span class=" ">Active</span>
                                                            </div>  
                                                            ';
                        }
                    } elseif ($l->contract_status == 'Inactive') {

                        $html .= '<div class=" bg-new-blue ml-auto  badge-new  font-weight-bold    text-center  font-w600 text-white"  >
                                                                          <span class=" ">Renewed</span>
                        
                                                            </div>  ';
                    } elseif ($l->contract_status == 'Expired/Ended') {

                        $html .= '   <div class=" bg-new-red ml-auto  font-weight-bold    badge-new  text-center  font-w600 text-white"  >
                                                                          <span class=" ">Ended</span>
                        
                                                            </div>';
                    } elseif ($l->contract_status == 'Ended') {
                        $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center  font-weight-bold   0 text-white"  >
                                                                          <span class=" ">Ended</span>
                                                            </div>';
                    } elseif ($l->contract_status == 'Expired') {
                        $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   text-white"  >
                                                                          <span class=" ">Expired</span>
                                                            </div>';
                    }





                    $html .= '    </div>';
                    $ssl_line_items = DB::Table('contract_assets as ca')->select('a.hostname', 'a.AssetStatus')->where('ca.contract_id', $l->id)->join('assets as a', 'a.id', '=', 'ca.hostname')->where('ca.is_deleted', 0)->orderBy('a.hostname', 'asc')->get();
                    $cvm = '<b class="HostActive text-white">Assigned Assets</b><br>';
                    foreach ($ssl_line_items as $v) {
                        if ($v->AssetStatus != '1') {
                            $cvm .= '<span class="HostInactive text-uppercase">' . $v->hostname . '</span><br>';
                        } else {
                            $cvm .= '<span class="HostActive text-uppercase">' . $v->hostname . '</span><br>';
                        }
                    }

                    $contract_end_date = date('Y-M-d', strtotime($l->contract_end_date));
                    $today = date('Y-m-d');
                    $earlier = new DateTime($l->contract_end_date);
                    $later = new DateTime($today);

                    $abs_diff = $later->diff($earlier)->format("%a"); //3

                    $cvm = '<p class="HostActive text-white  my-0">Validity Range</p><p class="HostActive my-n1 text-orange"  >' . date('d-M-Y', strtotime($l->contract_start_date)) . '-' . date('d-M-Y', strtotime($l->contract_end_date)) . '</p><p class="font-10pt mb-0 text-grey text-truncate mt-0"> <small><i>' . $abs_diff . ' days remaining</i></small></p>';
                    $html .= "<div    style='position: absolute;width: 100%; bottom: 2px;right: 10px;display: flex;align-items: center;justify-content: end;'>
                                                                

<div class='ActionIcon'  data-src='../public/img/calendar-grey-removebg-preview.png?cache=1' data-original-src='../public/img/calendar-grey-removebg-preview.png'>
<a href='javascript:;' class='toggle '' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title='' >
                     <img  src='../public/img/calendar-grey-removebg-preview.png' width='24px'  class='' >
                </a>
                                                            </div>


";
                    if (Auth::check()) {
                        if (@Auth::user()->role != 'read') {
                            $html .= '<div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
                                                                 <a  class="dropdown-toggle"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>
                                                               
                                                                <img src="../public/img/dots.png?cache=1"   >
                                                                </a>
                                 <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary">
                                 ';

                            $html .= '<a class="dropdown-item d-flex align-items-center px-0" href="' . url("contract") . '?id=' . $l->id . '&page=' . ceil($l->rownumber / 10) . '" target="_blank">   <div style="  padding-left: 2px"><img src="../public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > View Support Contract</div></a>  
           
        </div>
                                                           </div>';
                        }
                    }
                    $html .= '         </div>
                                                 
                                   
                         </div>   
                        </div>     
                          </div> 
                    ';
                }
                $html .= '
   </div>
             </div>
                </div>
               ';
            } else {
                $html .= '
                <div class="col-lg-12 px-0" style="max-height: 295px; overflow-y: auto; overflow-x: hidden;">
                <div class="row">
                <div class="col-8 d-flex align-items-center">
                <img src="' . asset('public/icons/cross.png') . '" width="50" class="ml-1 mr-3">
                <p class="font-12pt mb-0 text-truncate c1">Unassigned
                                                 </p>
                </div>
                </div>
                </div>';
            }
        } else {
            $html .= '
            <div class="col-lg-12 px-0" style="max-height: 295px; overflow-y: auto; overflow-x: hidden;">
            <div class="row">
                <div class="col-8 d-flex align-items-center">
                <img src="' . asset('public/icons/icon-checklist-na.png') . '" width="50" class="ml-1 mr-3">
                <p class="font-12pt mb-0 text-truncate c1">Not Applicable
                                                 </p>
                </div>
                </div>
                </div>';
        }

        $html .= '</div>
                        
 </div> </div>';
        // }



        // if (sizeof($ssl_line_items_2) > 0) {
        $html .= '<div class="col-6 pl-0"><div class="block new-block mt-0 mb-0" style="padding-left: 20px;padding-right: 20px;">
                                        <div class="mb-3" style="font-size: 20px; font-weight: 600;">SSL Certificates
                                        
                                                                      <ul class="nav nav-tabs NewNavtab justify-content-end float-right" id="contractTab" role="tablist">
  <li class="nav-item mr-3" role="presentation">
    <a class="nav-link active" id="Active-tab1"   data-toggle="tab" href="#Active1" role="tab" aria-controls="showAll" aria-selected="true">Active</a>
   
  </li>
   <li class="nav-item " role="presentation">
  
    <a class="nav-link  " id="showAll-tab1" data-toggle="tab" href="#showAll1" role="tab" aria-controls="showAll"  >Show All</a>

  </li>
</ul>
</div>
                     
<div class="block-content row new-block-content  pt-0 px-0" id="commentBlock" style="
    max-height: 470px;
    overflow-y: auto;
">
';
        if ($q->ntp == 1) {
            if (sizeof($ssl_line_items_2) > 0) {


                $html .= '
<div class="tab-content mt-3" id="contractTabContent1">
  <div class="tab-pane fade show active" id="Active1" role="tabpanel" aria-labelledby="showAll-tab">
  
';




                $html .= '
            <div class="col-lg-12 px-0" style="min-height:200px; ;max-height: 312px; overflow-y: auto; overflow-x: hidden;">';
                foreach ($ssl_line_items_2_active as  $ssl_row) {

                    $html .= '
                    <div class="block block-rounded table-block-new mb-2 pb-0  -   " data="' . $ssl_row->id . '" style="cursor:pointer;">
            
                 <div class="block-content align-items-center pt-1 pb-1 d-flex  pl-1 position-relative">
                            
                                                                         <div class="mr-1 justify-content-center align-items-center  d-flex" style="width:20%; padding: 7px;">';

                    if ($ssl_row->cert_type == 'internal') {
                        $html .= '<img src="../public/client_logos/' . $ssl_row->logo . '" class="rounded-circle"  width="100%" style=" object-fit: cover;">';
                    } else {
                        $html .= '<img src="../public/vendor_logos/' . $ssl_row->vendor_image . '" class="rounded-circle"  width="100%" style=" object-fit: cover;">';
                    }

                    $html .= '</div>
                                <div class="  " style="width:59%">
                    <p class="font-12pt mb-0 text-truncate c1"><b>' . $ssl_row->client_display_name . '
                                                 </b></p>
                                     
                                              <div class="d-flex">';
                    if ($ssl_row->cert_type == 'internal') {
                        $html .= '<p class="font-11pt mr-1   mb-0  c4-p  "  style="max-width:12%; " data-toggle="tooltip" data-title="Internal Certificate" data="' . $ssl_row->id . '">I</p>';
                    } else {

                        $html .= '   <p class="font-11pt mr-1   mb-0   c4-v  "  style="max-width:12%; " data-toggle="tooltip" data-title="Public Certificate" data="' . $ssl_row->id . '">P</p>';
                    }
                    $html .= '<p class="font-12pt mb-0 text-truncate   c4"  style="width:93%" data="' . $ssl_row->id . '">' . $ssl_row->cert_name . '</p>
                                                               </div>

                      

                                  <p class="font-12pt mb-0 text-truncate  c2">' . $ssl_row->description . '</p>
                                             
                                </div>
                                <div class=" text-right" style="width:25%;;">
                                                                    <div style="position: absolute;width: 100%; top: 10px;right: 10px;">';
                    $cert_edate = date('Y-M-d', strtotime($ssl_row->cert_edate));
                    $today = date('Y-m-d');
                    $earlier = new DateTime($ssl_row->cert_edate);
                    $later = new DateTime($today);

                    $abs_diff = $later->diff($earlier)->format("%a");





                    if ($ssl_row->cert_status == 'Active') {

                        if ($abs_diff <= 30) {
                            $html .= '<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-weight-bold text-dark"  >
                                                                       Upcoming</span>
                                                            </div> ';
                        } else {
                            $html .= '<div class=" bg-new-green ml-auto  badge-new  text-center  font-weight-bold   text-white"  >
                                                                         Active</span>
                                                            </div>  ';
                        }
                    } elseif ($ssl_row->cert_status == 'Inactive') {

                        $html .= '<div class=" bg-new-blue ml-auto  badge-new   font-weight-bold  text-center  font-w600 text-white"  >
<span class=" ">Renewed</span>
                                                            </div>  ';
                    } elseif ($ssl_row->cert_status == 'Expired/Ended') {

                        $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
                                                                       Ended</span>
                        
                                                            </div>';
                    } elseif ($ssl_row->cert_status == 'Ended') {
                        $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
                                                                       Ended</span>
                                                            </div>';
                    } elseif ($ssl_row->cert_status == 'Expired') {
                        $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
                                                                       <span class=" ">Expired</span>
                                                            </div>';
                    }




                    $html .= '    </div>';
                    $san_items = DB::Table('ssl_san')->select('san')->where('ssl_id', $ssl_row->id)->groupBy('san')->orderBy('san', 'asc')->get();
                    $cvm1 = '<b class="HostActive text-white ">SANs</b><br>';
                    foreach ($san_items as $li) {

                        $cvm1 .= '<span class="SSLActive">' . $li->san . '</span><br>';
                    }

                    $html .= '     <div  class="" style="position: absolute;width: 100%; bottom:2px;right: 10px;display: flex;align-items: center;justify-content: end;">
                                                                    
                                                              <div  class="ActionIcon" style="margin-left:2px;" data-src="../public/img/icon-san-grey-darker.png?cache=1" data-original-src="../public/img/icon-san-grey-darker.png?cache=1">
                                                        ';
                    $html .= "<a href='javascript:;' class='toggle' data-toggle='tooltip' data-trigger='hover' data-placement='top'  data-html='true'   data-original-title='$cvm1'>";

                    $html .= '<img  src="../public/img/icon-san-grey-darker.png?cache=1"  height="24px">
                                                                </a>
                                                            </div>
                                                                <div class="ActionIcon"  data-src="../public/img/calendar-grey-removebg-preview.png?cache=1" data-original-src="../public/img/calendar-grey-removebg-preview.png?cache=1">';

                    $cvm = '<p class="HostActive text-white  my-0">Validity Range</p><p class="HostActive my-n1 text-orange"  >' . date('d-M-Y', strtotime($ssl_row->cert_sdate)) . '-' . date('d-M-Y', strtotime($ssl_row->cert_edate)) . '</p><p class="font-10pt mb-0 text-grey text-truncate mt-0"> <small><i>' . $abs_diff . ' days remaining</i></small></p>';

                    $html .= "
<a href='javascript:;' class='toggle ' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title='' >
                     <img  src='../public/img/calendar-grey-removebg-preview.png' width='24px'  class='' >
                </a>
                                                            </div>


";

                    if (Auth::check()) {
                        if (@Auth::user()->role != 'read') {
                            $html .= '<div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
                                                                 <a  class="dropdown-toggle"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>
                                                               
                                                                <img src="../public/img/dots.png?cache=1"   >
                                                                </a>
                                 <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary">';



                            $html .= '<a class="dropdown-item d-flex align-items-center px-0" target="_blank" href="' . url("ssl-certificate") . '?id=' . $ssl_row->id . '&page=' . ceil($ssl_row->rownumber / 10) . '">   <div style="  padding-left: 2px"><img src="../public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > View SSL Certificate</div></a>  
        </div>
                                                           </div>';
                        }
                    }



                    $html .= '      
                                                 
                                </div>    
                           </div>     
                    </div> </div>
                    ';
                }

                $html .= ' </div>   </div>   
                 <div class="tab-pane fade    " id="showAll1" role="tabpanel" aria-labelledby="showAll-tab">';
                $html .= '
            <div class="col-lg-12 px-0" style="min-height:200px; ;max-height: 312px; overflow-y: auto; overflow-x: hidden;">';
                foreach ($ssl_line_items_2 as  $ssl_row) {

                    $html .= '
                    <div class="block block-rounded table-block-new mb-2 pb-0  -   " data="' . $ssl_row->id . '" style="cursor:pointer;">
            
                 <div class="block-content align-items-center pt-1 pb-1 d-flex  pl-1 position-relative">
                            
                                                                         <div class="mr-1 justify-content-center align-items-center  d-flex" style="width:20%; padding: 7px;">';

                    if ($ssl_row->cert_type == 'internal') {
                        $html .= '<img src="../public/client_logos/' . $ssl_row->logo . '" class="rounded-circle"  width="100%" style=" object-fit: cover;">';
                    } else {
                        $html .= '<img src="../public/vendor_logos/' . $ssl_row->vendor_image . '" class="rounded-circle"  width="100%" style=" object-fit: cover;">';
                    }

                    $html .= '</div>
                                <div class="  " style="width:59%">
                    <p class="font-12pt mb-0 text-truncate c1"><b>' . $ssl_row->client_display_name . '
                                                 </b></p>
                                     
                                              <div class="d-flex">';
                    if ($ssl_row->cert_type == 'internal') {
                        $html .= '<p class="font-11pt mr-1   mb-0  c4-p  "  style="max-width:12%; " data-toggle="tooltip" data-title="Internal Certificate" data="' . $ssl_row->id . '">I</p>';
                    } else {

                        $html .= '   <p class="font-11pt mr-1   mb-0   c4-v  "  style="max-width:12%; " data-toggle="tooltip" data-title="Public Certificate" data="' . $ssl_row->id . '">P</p>';
                    }
                    $html .= '<p class="font-12pt mb-0 text-truncate   c4"  style="width:93%" data="' . $ssl_row->id . '">' . $ssl_row->cert_name . '</p>
                                                               </div>

                      

                                  <p class="font-12pt mb-0 text-truncate  c2">' . $ssl_row->description . '</p>
                                             
                                </div>
                                <div class=" text-right" style="width:25%;;">
                                                                    <div style="position: absolute;width: 100%; top: 10px;right: 10px;">';
                    $cert_edate = date('Y-M-d', strtotime($ssl_row->cert_edate));
                    $today = date('Y-m-d');
                    $earlier = new DateTime($ssl_row->cert_edate);
                    $later = new DateTime($today);

                    $abs_diff = $later->diff($earlier)->format("%a");





                    if ($ssl_row->cert_status == 'Active') {

                        if ($abs_diff <= 30) {
                            $html .= '<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-weight-bold text-dark"  >
                                                                       Upcoming</span>
                                                            </div> ';
                        } else {
                            $html .= '<div class=" bg-new-green ml-auto  badge-new  text-center  font-weight-bold   text-white"  >
                                                                         Active</span>
                                                            </div>  ';
                        }
                    } elseif ($ssl_row->cert_status == 'Inactive') {

                        $html .= '<div class=" bg-new-blue ml-auto  badge-new   font-weight-bold  text-center  font-w600 text-white"  >
<span class=" ">Renewed</span>
                                                            </div>  ';
                    } elseif ($ssl_row->cert_status == 'Expired/Ended') {

                        $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
                                                                       Ended</span>
                        
                                                            </div>';
                    } elseif ($ssl_row->cert_status == 'Ended') {
                        $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
                                                                       Ended</span>
                                                            </div>';
                    } elseif ($ssl_row->cert_status == 'Expired') {
                        $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
                                                                       <span class=" ">Expired</span>
                                                            </div>';
                    }




                    $html .= '    </div>';
                    $san_items = DB::Table('ssl_san')->select('san')->where('ssl_id', $ssl_row->id)->groupBy('san')->orderBy('san', 'asc')->get();
                    $cvm1 = '<b class="HostActive text-white ">SANs</b><br>';
                    foreach ($san_items as $li) {

                        $cvm1 .= '<span class="SSLActive">' . $li->san . '</span><br>';
                    }

                    $html .= '     <div  class="" style="position: absolute;width: 100%; bottom:2px;right: 10px;display: flex;align-items: center;justify-content: end;">
                                                                    
                                                              <div  class="ActionIcon" style="margin-left:2px;" data-src="../public/img/icon-san-grey-darker.png?cache=1" data-original-src="../public/img/icon-san-grey-darker.png?cache=1">
                                                        ';
                    $html .= "<a href='javascript:;' class='toggle' data-toggle='tooltip' data-trigger='hover' data-placement='top'  data-html='true'   data-original-title='$cvm1'>";

                    $html .= '<img  src="../public/img/icon-san-grey-darker.png?cache=1"  height="24px">
                                                                </a>
                                                            </div>
                                                                <div class="ActionIcon"  data-src="../public/img/calendar-grey-removebg-preview.png?cache=1" data-original-src="../public/img/calendar-grey-removebg-preview.png?cache=1">';

                    $cvm = '<p class="HostActive text-white  my-0">Validity Range</p><p class="HostActive my-n1 text-orange"  >' . date('d-M-Y', strtotime($ssl_row->cert_sdate)) . '-' . date('d-M-Y', strtotime($ssl_row->cert_edate)) . '</p><p class="font-10pt mb-0 text-grey text-truncate mt-0"> <small><i>' . $abs_diff . ' days remaining</i></small></p>';

                    $html .= "
<a href='javascript:;' class='toggle ' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title='' >
                     <img  src='../public/img/calendar-grey-removebg-preview.png' width='24px'  class='' >
                </a>
                                                            </div>


";

                    if (Auth::check()) {
                        if (@Auth::user()->role != 'read') {
                            $html .= '<div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
                                                                 <a  class="dropdown-toggle"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>
                                                               
                                                                <img src="../public/img/dots.png?cache=1"   >
                                                                </a>
                                 <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary">';



                            $html .= '<a class="dropdown-item d-flex align-items-center px-0" target="_blank" href="' . url("ssl-certificate") . '?id=' . $ssl_row->id . '&page=' . ceil($ssl_row->rownumber / 10) . '">   <div style="  padding-left: 2px"><img src="../public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > View SSL Certificate</div></a>  
        </div>
                                                           </div>';
                        }
                    }



                    $html .= '         </div>
                                                 
                                   
                         </div>   
                        </div>     
                          </div> 
                    ';
                }
                $html .= '
   </div>
             </div>
                </div>
               ';
            } else {
                $html .= '
                <div class="col-lg-12 px-0" style="max-height: 295px; overflow-y: auto; overflow-x: hidden;">
                <div class="row">
                <div class="col-8 d-flex align-items-center">
                <img src="' . asset('public/icons/cross.png') . '" width="50" class="ml-1 mr-3">
                <p class="font-12pt mb-0 text-truncate c1">Unassigned
                                                 </p>
                </div>
                </div>
                </div>';
            }
        } else {
            $html .= '
            <div class="col-lg-12 px-0" style="max-height: 295px; overflow-y: auto; overflow-x: hidden;">
            <div class="row">
                <div class="col-8 d-flex align-items-center">
                <img src="' . asset('public/icons/icon-checklist-na.png') . '" width="50" class="ml-1 mr-3">
                <p class="font-12pt mb-0 text-truncate c1">Not Applicable
                                                 </p>
                </div>
                </div>
                </div>';
        }

        $html .= '
</div> </div></div> </div></div>';




        if ($q->asset_type == 'virtual') {
            $html .= '<div class="block new-block pb-0 mt-4 " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content-   push mb-0" >
                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">VM Information</div>
                            <div class="col-sm-12">
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-12">
                                    <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">vCenter Server</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->center_server . '</b></div> 
                                     
                                            </div>

                                         </div>
                                    <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Cluster/Host</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first">' . $q->cluster_host . '</div> 
                                     
                                            </div>

                                         </div>
                                    <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">VM Folder</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first">' . $q->vm_folder . '</div> 
                                     
                                            </div>

                                         </div>
                                    <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">VM Datastore</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first">' . $q->vm_datastore . '</div> 
                                     
                                            </div>

                                         </div>
                                         <div class="form-group row">
                                                    <div class="col-sm-3">
                                                    <div class="bubble-new">VM Restart Priority</div> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->vm_retart_priority . '
                                                    
                                                    </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        </div>
                                        </div>
        
                                    </div>
                                    

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
         

         ';

            $drs_rule = DB::table('assets_drs_rule')->where('asset_id', $id)->where('is_deleted', 0)->get();
            if (count($drs_rule) > 0) {
                $html .= '<div class="block new-block mt-4" style="padding-top: 0mm !important;">
                                    <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
                                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">DRS Rules</div>
                                                                        
                                            <div class="col-sm-12" >';
                foreach ($drs_rule as $al) {
                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false" data="${i}">
                                <table class="table table-borderless table-vcenter mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-center pr-0" style="width: 50px;">
                                                 <div class="contract_type_button mr-2">
                                        <input type="radio" id="keep_separated1" name=""
                                            value="' . (isset($al->rule_type) ? $al->rule_type : '') . '" checked>
                                        <label class="btn btn-new label1" for="keep_separated1"
                                            style="min-width: 140px;">' . (isset($al->rule_type) ? $al->rule_type : '') . '</label>
                                    </div>
                                            </td>
                                            <td class="js-task-content  pl-3">
                                                <h2 class="mb-0 comments-text" style="font-weight: 500;">
                                                    <div style="display: flex;align-items: center;">' . (isset($al->vm_host) ? $al->vm_host : '') . ' &nbsp;&nbsp</div></h2>
                                            </td>
                                        </tr>
                                </tbody>
                            </table>    
                            </div>';
                }
                $html .= '</div> 
                                    </div>
                                    
                                    </div>      

                                </div>
                                </div>
                                    ';
            }
        }

        if ($q->asset_type == 'virtual') {

            $html .= '
    <div class="block new-block pb-0 mt-4" style="padding-left: 20px;padding-right: 20px;">
            <div class="row justify-content-  push mb-0" >
                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">VM Resources</div>
                            <div class="col-sm-12 gg333" >            
                                <input type="hidden" name="attachment_array" id="attachment_array" >
                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group row">
                                                                <div class="col-sm-3">
                                                                <div class="bubble-new">vCPUs</div> 
                                                            </div>
                                                                    <div class="col-sm-2">
                                                                        <div class="bubble-white-new bubble-text-sec">
                                                                            ' . $q->vcpu . '  
                                                                            </div> 
                                                            
                                                                    </div>
                                                            </div>
                                                                <div class="form-group row">
                                                                <div class="col-sm-3">
                                                                <div class="bubble-new">Memory (Gb)</div> 
                                                            </div>
                                                                <div class="col-sm-2">
                                                                        <div class="bubble-white-new bubble-text-sec">
                                                                            ' . $q->memory . '                                                                          
                                                                        </div>                                                         
                                                                </div>                                                            
                                                        </div>                                
                                            </div>
                    </div>
                </div>
        </div></div>';
        }

        // if (sizeof($contract_ssl_line_items) > 0) {


        // }



        if ($q->asset_type == 'physical') {
            $assets_power_connection = DB::table('assets_power_connection')->where('asset_id', $id)->where('is_deleted', 0)->get();
            if (count($assets_power_connection) > 0) {
                $html .= '<div class="block new-block mt-4" style="padding-top: 0mm !important;">
                                    <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
                                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Power Connection</div>
                                                                        
                                            <div class="col-sm-12" >';
                foreach ($assets_power_connection as $p) {

                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" >
                                      <div class="d-flex" style="padding: 0.5rem;">
                                       
                                       <div class="d-flex align-items-center " style="width: 130px;text-align: center;">
                                          <p class="ml-3 mb-0 mr-2 rounded  LineTags" data-toggle="tooltip" data-title="Host PSU" data-original-title="" title="">

                                                          PSU# ' . $p->host_psu_no . '</p>
                                                    
                                                       
                                      </div>


                                         
                                          <div class="d-flex align-items-center justify-content-left"
                                              style="width: 20%;">
                                           <div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="PSU Type" data-original-title="" title="">

                                                        <span class=" ">' . $p->host_psu . '</span>
                                                    </div>


                                            
                                          </div>
                                          <div class="d-flex align-items-center justify-content-center"
                                              style="width: 37%;">
                                              <p class="mb-0 pb-0 text-muted" style="font-size: 12pt;color: black !important;"><img src="' . asset('public/img/arrow-left-1.png') . '" width="70px">' . $p->cable_length . ' foot cable <img src="' . asset('public/img/arrow-right-1.png') . '" width="70px"></p>
                                          </div>
                                          <div class="d-flex align-items-center justify-content-end"
                                              style="width: 30%;">
                                            

                                                   <p class="ml-3 mb-0 mr-3 rounded text-center  LineTags" data-toggle="tooltip" data-title="PDU#" data-original-title="" title="">

                                                          PDU# ' . $p->host_pdu_no . '</p>
                                                    
                                                     

 
                                                 <div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="PDU Type" data-original-title="" title="">

                                                        <span class=" ">' . $p->host_pdu . '</span>
                                                    </div>
                                          </div>
                                      

                                      </div>


                                  </div>';
                }
                $html .= '</div> 
                                    </div>
                                    
                                    </div>      

                                </div>
                                </div>
                                    ';
            }
        }

        $ipDns = DB::table('asset_ip_dns')->where('asset_id', $id)->where('is_deleted', 0)->get();
        if (count($ipDns) > 0) {
            $html .= '<div class="block new-block mt-4  ' . ($q->network_connected == 1 || $q->asset_type == 'virtual' ? '' : 'd-none') . '" style="padding-top: 0mm !important;">
                                    <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
                                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">IP and DNS</div>
                                                                        
                                            <div class="col-sm-12" >';
            foreach ($ipDns as $ip) {


                if ($ip->dns_type == "A") {
                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9"
                                    data-task-completed="false" data-task-starred="false"   >
                                    <div class="d-flex" style="padding: 0.5rem;">
                                     
                                      <div class="d-flex align-items-center " style="width: 180px;text-align: center;"  >
                                          <p class="ml-3 mb-0 mr-3 rounded  MainTags" data-toggle="tooltip" data-title="VLANID">

                                                        ' . ($ip->vlan_id == '' ? 'NONE' : $ip->vlan_id) . '</p>
                                                    
                                                       
                                      </div> 
                                      <div class="d-flex align-items-center" style="width: 34%;margin-left: 20px;">
                                          <h2 class="mb-0 comments-text">
                                                        <div style="display: flex;align-items: center;">
                                                           ' . $ip->ip_address . ' ' . ($ip->mask != '' ? $ip->mask : '') . ' </div><span
                                                            class="comments-subtext ml-0"
                                                            style="display: block;">' . $ip->description . '</span>
                                                    </h2>
                                      </div>
                                      <div class="d-flex justify-content-end align-items-center" style="width: 34%;">
                                          <h2 class="mb-0 comments-text pr-3 w-100">
                                                        <div class="truncate-text" style="/* display: flex; */justify-content: end;text-align: end;">
                                                            ' . $ip->host_name . '</div><span
                                                            class="comments-subtext mr-0 "
                                                            style="display: block;text-align: end;">' . ($ip->gateway == "on" ? $ip->gateway_ip : 'N/A') . '</span>
                                                    </h2>
                                                 
                                                    
                                      </div>
                                       <div class="d-flex justify-content-end align-items-center" style="width: fit-content;padding-right:10px">
                                     
                                          <div class="text-center LineTags mr-2"  data-toggle="tooltip" data-title="Zone"
                                                        >
                                                        <span class=" ">' . ($ip->zone == '' ? 'Default' : $ip->zone) . '</span>
                                                    </div> 
                                                    </div>
                                      <div class="d-flex justify-content-end align-items-center" style="width: fit-content;padding-right:10px">
                                          <a type="button"
                                                        data="${i}"
                                                        class="js-   btn btn-sm  text-warning">
                                                        <img src="' . url('public/icons/icon-dl-dns.png') . '" style="width: 25px;"
                                                            data-toggle="tooltip" data-trigger="hover"
                                                            data-placement="top" title="" data-html="true" ' . ($ip->primary_dns != '' || $ip->secondary_dns != '' ? 'data-original-title="<span class=\'HostActive text-yellow\' >Primary DNS </span><span class=\'HostActive text-white \' >' . $ip->primary_dns . '</span><br><span class=\'HostActive text-yellow \' >Secondary DNS </span><span class=\'HostActive text-white\'  >' . $ip->secondary_dns . '</span>"' : 'data-original-title="N/A"') . '>
                                                    </a>
                                                  <a type="button"
                                                      
                                                        class="js-   btn btn-sm  text-warning">
                                                        <img src="' . url('public/icons/icon-dl-ntp.png') . '" style="width: 25px;"
                                                            data-toggle="tooltip" data-trigger="hover"
                                                            data-placement="top" title="" data-html="true"
                                                            ' . ($ip->primary_ntp != '' || $ip->secondary_ntp != '' ? 'data-original-title="<span class=\'HostActive text-yellow \' >Primary NTP </span><span class=\' HostActive text-white \'>' . $ip->primary_ntp . '</span><br><span class=\' HostActive text-yellow \' >Secondary NTP </span><span class=\' HostActive text-white \' >' . $ip->secondary_ntp . '</span>"' : 'data-original-title="N/A"') . '
                                                                          >
                                                    </a>
                                                
                              
 
                                      </div>
                                      </div>
                                    

                                </div>';
                } else {

                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn"  >
                                  <div class="d-flex" style="padding: 0.5rem;">
                                    
                                      <div class="d-flex align-items-center"  style="width: 180px;text-align: center;">
                                           <p class="ml-3 mb-0 mr-3 rounded MainTags"  data-toggle="tooltip" data-title="Alias">

                                                        ' . ($ip->dns_type == 'NONE(0)' ? 'None' : $ip->dns_type) . '</p>
                                      </div>
                                      <div class="d-flex align-items-center justify-content-between" style="width: 20%;margin-left: 20px;">
                                          <h2 class="mb-0 comments-text" style="text-align: left;">
                                                            <div style="display: flex;">
                                                                ' . $ip->alias . ' </div><span
                                                                class="comments-subtext ml-0"
                                                                style="display: block;">
                                                                ' . $ip->description . '</span>
                                                        </h2>
                                                      
                                      </div>
                                      <div style="width:20%;">
                                    
                                        <img src="' . asset('public') . '/icons/arrow-right-new.png" style="width: 130px;height: 50px;">
                                        </div>
                                      <div class="d-flex align-items-center justify-content-center" style="width: 30%;">
                                          <h2 class="mb-0 comments-text pr-2">
                                                        <div style="display: flex;justify-content: end;">
                                                            <p class="my-0">' . $ip->host_name . '' . (@$domain_data->domain_name != 'Select Domain' && @$domain_data->domain_name != '' ? '.' . @$domain_data->domain_name : '') . '<p></div>
                                                    </h2>
                                      </div>
                                   </div>

                                </div>';
                }










                // if ($ip->dns_type == "A") {
                //     $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9"
                //                       data-task-completed="false" data-task-starred="false">
                //                       <div class="d-flex" style="padding: 0.75rem;">
                //                         <div class="d-flex align-items-center" style="width: 20%;">
                //                             <p class="ml-3 mb-0 mr-3 rounded px-3"
                //                                           style="font-size: 27px;font-weight: 600;border: 1px solid #303030;border-radius: 20% !important;">

                //                                           ' . $ip->dns_type . '</p>
                //                                           <h2 class="mb-0 comments-text">
                //                                       <div style="display: flex;align-items: center;margin-top: 2px;font-size: 35px;font-weight: 700;color: #4194f6;font-family: calibri;height: 29px;">
                //                                           ' . $ip->vlan_id . ' </div><span
                //                                           class="comments-subtext ml-0 mt-0 mb-0"
                //                                           style="display: block;margin: .3rem;">VLANID</span>
                //                                   </h2>
                //                         </div>
                //                         <div class="d-flex align-items-center" style="width: 30%;">
                //                             <h2 class="mb-0 comments-text">
                //                                           <div style="display: flex;align-items: center;">
                //                                               ' . $ip->ip_address . $ip->mask . '</div><span
                //                                               class="comments-subtext ml-0"
                //                                               style="display: block;margin: .3rem;">' . $ip->description . '</span>
                //                                       </h2>
                //                         </div>
                //                         <div class="d-flex justify-content-end align-items-center" style="width: 42%;">
                //                             <h2 class="mb-0 comments-text pr-3">
                //                                           <div style="display: flex;justify-content: end;">
                //                                               ' . $ip->host_name . '</div><span
                //                                               class="comments-subtext mr-0"
                //                                               style="display: block;margin: .3rem;display: flex;justify-content: end;">' . ($ip->gateway == 'on' ? $ip->gateway_ip : 'N/A') . '</span>
                //                                       </h2>
                //                                       <div class="text-center font-size-md bubble-white-new border-none bubble-text-sec px-2"
                //                                           style="background:' . $ip->background . ';color:' . $ip->color . ';width:fit-content!important;border-radius:5px;min-height:32px!important;border:none;">
                //                                           <span class=" ">' . $ip->zone . '</span>
                //                                       </div>
                //                         </div>
                //                         <div class="d-flex justify-content-end align-items-center" style="width: fit-content;">
                //                             <a type="button"
                //                                           data="${i}"
                //                                           class="js-   btn btn-sm  text-warning">
                //                                           <img src="../public/icons/icon-details-ip-dns.png" style="width: 25px;"
                //                                               data-toggle="tooltip" data-trigger="hover"
                //                                               data-placement="top" title="" data-html="true" data-original-title="<span class=\' HostActive text-yellow \' >Primary DNS </span><span class=\' HostActive text-white \' >' . $ip->primary_dns . '</span><br><span class=\' HostActive text-yellow \' >Secondary DNS </span><span class=\' HostActive text-white \' >' . $ip->secondary_dns . '</span>">
                //                                       </a>
                //                                     <a type="button"
                //                                           data="${i}"
                //                                           class="js-   btn btn-sm  text-warning">
                //                                           <img src="../public/icons/icon-details-ntp.png" style="width: 25px;"
                //                                               data-toggle="tooltip" data-trigger="hover"
                //                                               data-placement="top" title="" data-html="true" data-original-title="<span class=\' HostActive text-yellow \' >Primary NTP </span><span class=\' HostActive text-white \' >' . $ip->primary_ntp . '</span><br><span class=\' HostActive text-yellow \' >Secondary NTP </span><span class=\' HostActive text-white \' >' . $ip->secondary_ntp . '</span>"}
                //                                                             >
                //                                       </a>
                //                         </div>
                //                         </div>


                //                   </div>';
                // } else {
                //     $html .= '


                //               <div class="js-task block block-rounded mb-2 animated fadeIn">
                //                     <div class="d-flex" style="padding: 0.75rem;">
                //                         <div class="d-flex align-items-center" style="width: 20%;">
                //                             <p class="ml-3 mb-0 mr-3 rounded px-3"
                //                                           style="/* margin-top: -5px; *//* background-color: #FFCC00; *//* width: 169px; */font-size: 24px;font-weight: 600;border: 1px solid #303030;border-radius: 15px !important;">

                //                                           ' . $ip->dns_type . '</p>
                //                         </div>
                //                         <div class="d-flex align-items-center justify-content-between" style="width: 40%;">
                //                             <h2 class="mb-0 comments-text" style="text-align: left;">
                //                                               <div style="display: flex;">
                //                                                   ' . $ip->alias . ' </div><span
                //                                                   class="comments-subtext ml-0"
                //                                                   style="display: block;margin: .3rem;">
                //                                                   ' . $ip->description . '</span>
                //                                           </h2>
                //                                           <img src="../public/img/arrow-right.png" style="width: 130px;height: 50px;">
                //                         </div>
                //                         <div class="d-flex align-items-center justify-content-end" style="width: 40%;">
                //                             <h2 class="mb-0 comments-text pr-2">
                //                                           <div style="display: flex;justify-content: end;">
                //                                               ' . $ip->host_name . '</div>
                //                                       </h2>
                //                         </div>

                //                     </div>


                //                   </div>';
                // }
            }
            $html .= '</div> 
                                    </div>
                                    
                                    </div>      

                                </div>
                                </div>
                                    ';
        }

        $networkAdapter = DB::table('asset_network_adapter')->where('asset_id', $id)->where('is_deleted', 0)->get();
        if (count($networkAdapter) > 0) {
            $html .= '<div class="block new-block mt-4  ' . ($q->network_connected == 1 || $q->asset_type == 'virtual' ? '' : 'd-none') . '" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Network Adapters</div>
                                                                            
                                                <div class="col-sm-12" >';
            foreach ($networkAdapter as $row) {

                if ($row->connection_type == "Virtual") {



                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false" >
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                   
                                                    <td class="text-center pr-0" style="width: 40px;    padding-top: 0.5rem;
    padding-bottom: 0.5rem;    padding-left: 20px;">
                                                      
                                                              <div class="d-flex align-items-center " style="width: 180px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags" data-toggle="tooltip" data-title="Host Port" data-original-title="" title="">

                                                         vmnic' . $row->vmic . '</p>
                                                    
                                                       
                                      </div>


                                                    </td>
                                                    <td class="js-task-content  pl-0" style="padding-top: 0.5rem;padding-bottom: 0.5rem;">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="display: flex;align-items: center;">
                                                            ' . $row->port_group . ' &nbsp;&nbsp</div>
                                                            <span class="comments-subtext ml-0" style="display: block;">' . $row->mac_address . '</span></h2>
                                                    </td>



                                                    <td style="width: 20%;padding-right:18px;padding-top: 0.5rem;padding-bottom: 0.5rem;vertical-align:center;" class="text-right  ">
                                                      <div class="d-flex" style="    justify-content: space-between;">
                                                   
 
<div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="Type" data-original-title="" title="">

                                                        <span class=" ">' . ($row->adapter_type != '' ? $row->adapter_type : 'Null') . '</span>
                                                    </div>
 
                                                  

  
   

</div>


</td>
                                                </tr>
                                        </tbody>
                                    </table>
  
                                    </div>';
                } else if ($row->connection_type == 'Wifi') {
                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false"  >
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                     
                                                    <td class="text-center pr-0" style="width: 40px;padding-top: 0.5rem;padding-bottom: 0.5rem;padding-left: 20px;">
                                                        <div class="d-flex align-items-center " style="width: 180px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags" data-toggle="tooltip" data-title="Host Port" data-original-title="" title="">

                                                         ' . $row->connection_type . '</p>
                                                    
                                                       
                                      </div>


                                                    </td>
                                                    <td class="js-task-content  pl-0" style="padding-top: 0.5rem;padding-bottom: 0.5rem;vertical-align:center">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="display: flex;align-items: center;">


                                                            ' . $row->adapter_name . ' &nbsp;&nbsp</div><span class="comments-subtext ml-0 mt-0 mb-0" style="display: block;">' . $row->mac_address . '</span></h2>

                                                    </td>
                                                    <td style="width: 20%;padding-right:18px;" class="text-right;padding-top: 0.5rem;padding-bottom: 0.5rem;  "><div class="d-flex align-items-center justify-content-end">     


</div></td>
                                                </tr>
                                        </tbody>
                                    </table>    
                                    </div>';
                } else {
                    $tag_details = '';
                    if ($row->adapter_type == "MGMT") {
                        $tag_details = $row->adapter_type;
                    } else if ($row->adapter_type == 'EMB' || $row->adapter_type == 'MEZ' || $row->adapter_type == 'SwPort') {
                        $tag_details = $row->adapter_type . ' ' . $row->port;
                    } else {
                        $tag_details = $row->adapter_type . ' ' . $row->slot . ':' . $row->port;
                    }
                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false" >
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                   
                                                    <td class="text-center pr-0" style="width: 40px;padding-top: 0.5rem;padding-bottom: 0.5rem;padding-left: 20px;">
                                                     

                                                              <div class="d-flex align-items-center " style="width: 180px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags" data-toggle="tooltip" data-title="Host Port" data-original-title="" title="">' . $tag_details . '</p>
                                                    
                                                       
                                      </div>

                                                    </td>
                                                    <td class="js-task-content  pl-0" style="padding-top: 0.5rem;padding-bottom: 0.5rem;">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="display: flex;align-items: center;">
                                                            ' . $row->adapter_name . ' &nbsp;&nbsp</div><span class="comments-subtext ml-0 mt-0 mb-0" style="display: block;">' . $row->mac_address . '</span></h2>
                                                    </td>
                                                    <td style="width: 20%;padding-right:18px;padding-top: 0.5rem;padding-bottom: 0.5rem;vertical-align:center" class="text-right  ">


                                                    <div class="d-flex  align-items-center" style="justify-content: space-between;">
 
<div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="Port Media" data-original-title="" title="">

                                                        <span class=" ">' . $row->port_media . '</span>
                                                    </div>
 
                                                    </div>

                                                    </td>
                                                </tr>
                                        </tbody>
                                    </table>    
                                    </div>';
                }


                // if ($row->connection_type == "Virtual") {
                //     $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                //                           <table class="table table-borderless table-vcenter mb-0">
                //                               <tbody>
                //                                   <tr>
                //                                       <td class="text-center pr-0" style="width: 50px;">
                //                                            <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">vmnic' . $row->vmic . '</p>
                //                                       </td>
                //                                       <td class="js-task-content  pl-0">
                //                                           <h2 class="mb-0 comments-text">
                //                                               <div style="display: flex;align-items: center;">
                //                                               ' . $row->port_group . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">' . $row->mac_address . '</span></h2>
                //                                       </td>
                //                                       <td style="width: 20%;" class="text-right  0">
                //                                         <div class="d-flex">
                //                                         <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important;border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                                           <span class=" ">' . $row->adapter_type . '</span>
                //                                       </div>  </div></td>
                //                                   </tr>
                //                           </tbody>
                //                       </table>

                //                       </div>';
                // } else if ($row->connection_type == "Wifi") {
                //     $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                //                           <table class="table table-borderless table-vcenter mb-0">
                //                               <tbody>
                //                                   <tr>
                //                                       <td class="text-center pr-0" style="width: 50px;">
                //                                            <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size:24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">' . $row->connection_type . '</p>
                //                                       </td>
                //                                       <td class="js-task-content  pl-0">
                //                                           <h2 class="mb-0 comments-text">
                //                                               <div style="display: flex;align-items: center;">
                //                                               ' . $row->adapter_name . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">' . $row->mac_address . '</span></h2>
                //                                       </td>
                //                                       <td style="width: 20%;" class="text-right  "><div class="d-flex">
                //                                            </div></td>
                //                                   </tr>
                //                           </tbody>
                //                       </table>

                //                       </div>';
                // } else {

                //     $tag_details = '';
                //     if($row->adapter_type == "MGMT") {
                //         $tag_details = $row->adapter_type;
                //     } else if($row->adapter_type == 'EMB' || $row->adapter_type == 'MEZ' || $row->adapter_type == 'SwPort'){
                //         $tag_details = $row->adapter_type . ' ' . $row->port;
                //     } else {
                //         $tag_details = $row->adapter_type . ' ' . $row->slot.':'.$row->port;
                //     }
                //     $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                //                           <table class="table table-borderless table-vcenter mb-0">
                //                               <tbody>
                //                                   <tr>
                //                                       <td class="text-center pr-0" style="width: 50px;">
                //                                            <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size:24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">' . $row->connection_type . '</p>
                //                                       </td>
                //                                       <td class="js-task-content  pl-0">
                //                                           <h2 class="mb-0 comments-text">
                //                                               <div style="display: flex;align-items: center;">
                //                                               ' . $row->adapter_name . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">' . $row->mac_address . '</span></h2>
                //                                       </td>
                //                                       <td style="width: 20%;" class="text-right  "><div class="d-flex align-items-center justify-content-end"><div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important;border: none;" class=" bg-new-blue ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                                           <span class=" ">' . $tag_details . '</span>
                //                                       </div>  
                //                                       <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                                           <span class=" ">' . $row->port_media . '</span>
                //                                       </div>
                //                                            </div></td>
                //                                   </tr>
                //                           </tbody>
                //                       </table>

                //                       </div>';








                // }
            }
            $html .= '</div> 
                                        </div>
                                        
                                        </div>      

                                    </div>
                                    </div>
                                        ';
        }
        $portMap = DB::table('asset_port_map')->where('asset_id', $id)->where('is_deleted', 0)->get();
        if (count($portMap) > 0) {
            $html .= '<div class="block new-block  ' . ($q->network_connected == 1 || $q->asset_type == 'virtual' ? '' : 'd-none') . ' mt-4" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Port Mapping</div>
                                                                            
                                                <div class="col-sm-12" >';
            foreach ($portMap as $row) {




                if ($row->mapping_type == "Wired") {
                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false"  >
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                
                                                    <td class="text-center pr-0" style="width: 50px;padding-top: 0.5rem;padding-bottom: 0.5rem;padding-left: 20px;">
                                                       

                                                          <div class="d-flex align-items-center " style="width: 180px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags" data-toggle="tooltip" data-title="Host Port" data-original-title="" title="">

                                                      ' . $row->network_adapter . ' </p>
                                                    
                                                       
                                      </div>
                                                    </td>
                                                    <td class="js-task-content  pl-0" style="padding-bottom: 0.5rem;padding-top: 0.5rem;">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="display: flex;align-items: center;">
                                                            ' . $row->switch . ' ' . $row->port . ' &nbsp;&nbsp</div><span class="comments-subtext ml-0 mb-0 mt-0" style="display: block;">' . $row->sub_text . '</span></h2>
                                                    </td>
                                                    <td style="width: 20%;padding-right:18px;padding-bottom: 0.5rem;padding-top: 0.5rem;" class="text-right  ">
                                                      <div class="d-flex align-items-center">


                                                      
<div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="Port Mode" data-original-title="" title="">

                                                        <span class=" ">' . ($row->port_mode != '' ? $row->port_mode : 'Null') . '</span>
                                                    </div>

                                          
<div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="Media Type" data-original-title="" title="">

                                                        <span class=" ">' . ($row->media_type != '' ? $row->media_type : 'Null') . '</span>
                                                    </div>



                                              

                                                    <a type="button" class="js-  btn btn-sm  text-warning">
                                                         <img src="' . url('public/icons/icon-details-vlanid.png') . '" style="width: 30px;"  data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                                          data-title="" data-html="true"
                                                                          data-original-title="<div class=\'text-center pb-0 mb-0 HostActive text-yellow\'>vLAN IDs</div><span class=\' HostActive text-white \' >' . $row->vlan_ids . '</span>">
                                                        </a>
                    
  </div>




                                                    </td>
                                                </tr>
                                        </tbody>
                                    </table>
  
                                    </div>';
                } else {
                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false" >
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                
                                                    <td class="text-center pr-0" style="width: 50px;padding-bottom: 0.5rem;padding-top: 0.5rem;padding-left: 20px;">
                                                       
                                                            <div class="d-flex align-items-center " style="width: 180px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags" data-toggle="tooltip" data-title="Host Port" data-original-title="" title="">

                                                     WIFI </p>
                                                    
                                                       
                                      </div>
                                                    </td>
                                                    <td class="js-task-content  pl-0" style="padding-bottom: 0.5rem;padding-top: 0.5rem;">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="display: flex;align-items: center;">
                                                            ' . $row->ssid . ' &nbsp;&nbsp</div><span class="comments-subtext ml-0 mb-0 mt-0" style="display: block;">Comments</span></h2>
                                                    </td>
                                                    <td style="width: 20%;padding-right:18px; padding-bottom: 0.5rem;padding-top: 0.5rem;" class="text-right; ">


                                                      <div class="d-flex align-items-center" style="justify-content-space-between">
                                                    

<div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="SSID" data-original-title="" title="">

                                                        <span class=" ">' . ($row->ssid != '' ? $row->ssid : 'Null') . '</span>
                                                    </div>


     


                                                    </div>



                                                    </td>
                                                </tr>
                                        </tbody>
                                    </table>
  
                                    </div>';
                }







                // if ($row->mapping_type == "Wired") {
                //     $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                //                           <table class="table table-borderless table-vcenter mb-0">
                //                               <tbody>
                //                                   <tr>
                //                                       <td class="text-center pr-0" style="width: 50px;">
                //                                            <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">                                                          
                //                                             ' . @$row->network_adapter . '</p>
                //                                       </td>
                //                                       <td class="js-task-content  pl-0">
                //                                           <h2 class="mb-0 comments-text">
                //                                               <div style="display: flex;align-items: center;">
                //                                               ' . @$row->switch . ' ' . @$row->port . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">' . @$row->sub_text . '</span></h2>
                //                                       </td>
                //                                       <td style="width: 20%;" class="text-right ">
                //                                         <div class="d-flex align-items-center">
                //                                         <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-blue ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                                           <span class=" ">' . @$row->port_mode . '</span>
                //                                       </div>
                //                                       <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                                           <span class=" ">' . $row->media_type . '</span>
                //                                       </div>
                //                                       <a type="button" class="js-  btn btn-sm  text-warning">
                //                                            <img src="../public/icons/icon-details-vlanid.png" style="width: 30px;"  data-toggle="tooltip" data-trigger="hover" data-placement="top"
                //                                                             data-title="" data-html="true"
                //                                                             data-original-title="<div class=\' HostActive text-center text-yellow \' >vLAN IDs</div><span class=\' HostActive text-white \' >' . $row->vlan_ids . '</span>">
                //                                           </a>
                //                                        </div></td>
                //                                   </tr>
                //                           </tbody>
                //                       </table>

                //                       </div>';
                // } else {
                //     $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                //                           <table class="table table-borderless table-vcenter mb-0">
                //                               <tbody>
                //                                   <tr>
                //                                       <td class="text-center pr-0" style="width: 50px;">
                //                                            <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">                                                          
                //                                             WIFI</p>
                //                                       </td>
                //                                       <td class="js-task-content  pl-0">
                //                                           <h2 class="mb-0 comments-text">
                //                                               <div style="display: flex;align-items: center;">
                //                                               ' . @$row->ssid . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">Comments</span></h2>
                //                                       </td>
                //                                       <td style="width: 20%;" class="text-right  ">
                //                                         <div class="d-flex">
                //                                         <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                                           <span class=" ">' . @$row->ssid . '</span>
                //                                       </div>  </div></td>
                //                                   </tr>
                //                           </tbody>
                //                       </table>

                //                       </div>';
                // }
            }
            $html .= '</div> 
                                        </div>
                                        
                                        </div>      

                                    </div>
                                    </div>
                                        ';
        }

        $virtualDisks = DB::table('asset_virtual_disks')->where('asset_id', $id)->where('is_deleted', 0)->get();
        if (count($virtualDisks) > 0) {
            $html .= '<div class="block new-block mt-4" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Virtual Disks</div>
                                                                            
                                                <div class="col-sm-12" >';
            foreach ($virtualDisks as $row) {

                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false"  >
                            <table class="table table-borderless table-vcenter mb-0">
                                <tbody>
                                    <tr>
                                     
                                        

                                           <td class="text-center pr-0" style="width: 50px;padding-bottom: 0.5rem;padding-top: 0.5rem;padding-left: 20px;">
                                                       

                                                          <div class="d-flex align-items-center " style="width: 180px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags"  data-original-title="" title="">

                                                      vDisk' . $row->vdisk_no . ' </p>
                                                    
                                                       
                                      </div>
                                                    </td>

                     <td class="js-task-content  pl-0" style="padding-bottom: 0.5rem;padding-top: 0.5rem;">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="display: flex;align-items: center;">
                                                            ' . $row->datastore . '   &nbsp;&nbsp</div><span class="comments-subtext ml-0 mb-0 mt-0" style="display: block;">DATASTORE</span></h2>
                                                    </td>
   <td style="width: 20%;padding-right:18px;padding-bottom: 0.5rem;padding-top: 0.5rem" class="text-right  ">
                                                      <div class="d-flex align-items-center">


                                                      
<div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="Disk Size" data-original-title="" title="">

                                                        <span class=" ">' . $row->drive_size . ' ' . $row->drive_size_unit . '</span>
                                                    </div>

                                          
<div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="Provisioning" data-original-title="" title="">

                                                        <span class=" ">' . $row->device_type . ' </span>
                                                    </div>



                                              

                                                    <a type="button" class="js-  btn btn-sm  text-warning">
                                                         <img src="' . url('public/icons/icon-detail-raid-disk.png') . '" style="width: 25px;"  data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                                          data-title="" data-html="true"
                                                                          data-original-title="<div class=\'text-center pb-0 mb-0 HostActive text-yellow\'>' . $row->device_type . '</div><span class=\' HostActive text-white \' >SCIS ' . $row->scsi_id_a . ':' . $row->scsi_id_b . '</span>">
                                                        </a>
                                                  
 
  </div>




                                                    </td>

                                
                                  
                                    </tr>
                            </tbody>
                        </table>

                        </div>';



                // $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                //                           <table class="table table-borderless table-vcenter mb-0">
                //                               <tbody>
                //                                   <tr>
                //                                       <td class="text-center pr-0" style="width: 50px;">
                //                                            <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">' . $row->drive_size . ' ' . $row->drive_size_unit . '</p>
                //                                       </td>
                //                                       <td class="js-task-content  pl-0">
                //                                           <h2 class="mb-0 comments-text">
                //                                               <div style="display: flex;align-items: center;">
                //                                               ' . $row->datastore . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">vDisk' . $row->vdisk_no . '</span></h2>
                //                                       </td>
                //                                       <td style="width: 20%;" class="text-right  ">
                //                                         <div class="d-flex">
                //                                         <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-blue ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                                           <span class=" ">SCSI ' . $row->scsi_id_a . ':' . $row->scsi_id_b . '</span>
                //                                       </div> <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-dark ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                                           <span class=" ">' . $row->device_type . '</span>
                //                                       </div>  </div></td>
                //                                   </tr>
                //                           </tbody>
                //                       </table>

                //                       </div>';
            }
            $html .= '</div> 
                                        </div>
                                        
                                        </div>      

                                    </div>
                                    </div>
                                        ';
        }
        $raidVolume = DB::table('asset_raid_volume')->where('asset_id', $id)->where('is_deleted', 0)->get();
        if (count($raidVolume) > 0) {
            $html .= '<div class="block new-block mt-4  ' . ($q->network_connected == 1 || $q->asset_type == 'virtual' ? '' : 'd-none') . '" style="padding-top: 0mm !important;">
            <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Raid Volume</div>
            
            <div class="col-sm-12" >';
            foreach ($raidVolume as $row) {

                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false" >
        <table class="table table-borderless table-vcenter mb-0">
            <tbody>
                <tr>
                 
                   
          <td class="text-center pr-0" style="width: 50px;padding-bottom: 0.5rem;padding-top: 0.5rem;padding-left: 20px;">
                                                       
                                                            <div class="d-flex align-items-center " style="width: 180px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags"  data-original-title="" title="">

                                                     RV' . str_pad($row->name, 3, '0', STR_PAD_LEFT) . ' </p>
                                                    
                                                       
                                      </div>
                                                    </td>

              
                     <td class="js-task-content  pl-0" style="padding-bottom: 0.5rem;padding-top: 0.5rem;">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="display: flex;align-items: center;">
                                                            ' . $row->controller . ' &nbsp;&nbsp</div><span class="comments-subtext ml-0 mb-0 mt-0" style="display: block;">' . $row->volume_description . '</span></h2>
                                                    </td>

  <td style="width: 20%;padding-bottom: 0.5rem;padding-top: 0.5rem;padding-right:18px;" class="text-right;  " >


                                                      <div class="d-flex align-items-center" style="justify-content-space-between">
                                                    

<div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="Usable Size" data-original-title="" title="">

                                                        <span class=" ">' . $row->volume_size . '</span>
                                                    </div>

<div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="Raid Type" data-original-title="" title="">

                                                        <span class=" ">RAID' . $row->raid_level . '</span>
                                                    </div>

<a type="button" class="js-  btn btn-sm  text-warning">
                                                         <img src="' . asset('public/icons/icon-detail-raid-disk.png') . '" style="width: 25px;" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="" data-html="true" data-original-title="<div class=\'text-center pb-0 mb-0 HostActive text-yellow\'>' . $row->drive_size . ' ' . $row->drive_size_unit . ' ' . $row->drive_type . '</div><span class=\' HostActive text-white \' >RAID' . $row->raid_level . ' (' . $row->no_of_sets . 'x' . $row->no_of_drives . ')</span>">
                                                        </a>
     

                                                    </div>



                                                    </td>

           
                </tr>
        </tbody>
    </table>

    </div>';



                // $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                //         <table class="table table-borderless table-vcenter mb-0">
                //             <tbody>
                //                 <tr>
                //                     <td class="text-center pr-0" style="width: 50px;">
                //                          <p class="ml-3 mb-0 mr-3  px-1" style=" width: 170px;font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">                                                          
                //                           ' . $row->volume_size . '</p>
                //                     </td>
                //                     <td class="js-task-content  pl-0">
                //                         <h2 class="mb-0 comments-text">
                //                             <div style="display: flex;align-items: center;">
                //                             ' . $row->controller . ' &nbsp;&nbsp</div><span class="comments-subtext ml-0 mt-0 mb-0" style="display: block;margin: .3rem;">'. ($q->asset_type == 'virtual' ? 'vDisk'.' ' : '') . $row->name . '</span></h2>
                //                     </td>
                //                     <td style="width: 20%;" class="text-right ">
                //                       <div class="d-flex">
                //                       <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-blue ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                         <span class=" ">' . $row->drive_size . ' ' . $row->drive_size_unit . '</span>
                //                     </div><div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                         <span class=" ">' . $row->drive_type . '</span>
                //                     </div>
                //                     <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none; color: #262626;" class=" bg-new-yellow ml-auto mr-3  badge-new  text-center  font-weight-bold ">
                //                         <span class=" ">RAID' . $row->raid_level . '</span>
                //                     </div> <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-dark ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                         <span class=" ">' . $row->no_of_sets . 'x' . $row->no_of_drives . '</span>
                //                     </div>  </div></td>
                //                 </tr>
                //         </tbody>
                //     </table>

                //     </div>';
            }
            $html .= '</div> 
                                        </div>
                                        
                                        </div>      

                                    </div>
                                    </div>
                                        ';
        }

        $logicalVolume = DB::table('asset_logical_volume')->where('asset_id', $id)->where('is_deleted', 0)->get();
        if (count($logicalVolume) > 0) {
            $html .= '<div class="block new-block mt-4  ' . ($q->network_connected == 1 || $q->asset_type == 'virtual' ? '' : 'd-none') . '" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Logical Volume</div>
                                                                            
                                                <div class="col-sm-12" >';
            foreach ($logicalVolume as $row) {

                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                  <table class="table table-borderless table-vcenter mb-0">
                      <tbody>
                          <tr>
                    

                                 <td class="text-center pr-0" style="width: 50px;padding-bottom: 0.5rem;padding-top: 0.5rem;padding-left: 20px;">
                                                       

                                                          <div class="d-flex align-items-center " style="width: 180px;text-align: center;">
                                          <p class="ml-1 mb-0 mr-1 rounded  MainTags" data-toggle="tooltip"  data-original-title="" title="">

                                                    ' . ($q->platform == 'Windows' ? $row->volume : 'LV' . str_pad($row->volume1, 3, '0', STR_PAD_LEFT)) . ' </p>
                                                    
                                                       
                                      </div>
                                                    </td>
                              <td class="js-task-content  pl-0" padding-bottom: 0.5rem;padding-top: 0.5rem;>
                                  <h2 class="mb-0 comments-text">
                                      <div style="display: flex;align-items: center;">
                              ' . $row->volume_name . '  &nbsp;&nbsp</div><span class="comments-subtext ml-0 mt-0 mb-0 ml-0" style="display: block;">' . $row->source_disk . '</span></h2>
                              </td>



   <td style="width: 20%;padding-right:12px;padding-bottom: 0.5rem;padding-top: 0.5rem;padding-right:18px;" class="text-right  ">
                                                      <div class="d-flex align-items-center justify-content-between">


                                                      
<div class="text-center LineTags mr-2" data-toggle="tooltip"    data-html="true"  data-original-title="<div class=\'text-center pb-0 mb-0 HostActive text-yellow\'>' . $row->format . '</div><span class=\' HostActive text-white \'>' . $row->block_size . ' Block</span>" >

                                                        <span class=" ">' . $row->size . ' ' . $row->size_unit . '</span>
                                                    </div>
 
<div>
                                                    <a type="button" class="js-  btn btn-sm  text-warning">
                                                         <img src="' . url('public/icons/icon-detail-raid-disk.png') . '" style="width: 25px;"  data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                                          data-title="" data-html="true"
                                                                           data-original-title="' . ($row->tooltip ? $row->tooltip : '')  . '">
                                                        </a>
                                                  
 
</div>
  </div>




                                                    </td>





 
                          </tr>
                          </tbody>
                          </table>
                          </div>';


                // $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                //     <table class="table table-borderless table-vcenter mb-0">
                //         <tbody>
                //             <tr>
                //                 <td class="text-center pr-0" style="width: 50px;">
                //                     <p class="ml-3 mb-0 mr-3  px-3" style=" font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px">' . $row->volume . '</p>
                //                 </td>
                //                 <td class="js-task-content  pl-0">
                //                     <h2 class="mb-0 comments-text">
                //                         <div style="display: flex;align-items: center;">
                //                         ' . $row->source_disk . ' &nbsp;&nbsp</div><span class="comments-subtext ml-0 mt-0 mb-0" style="display: block;margin: .3rem;">'. ($q->asset_type == 'virtual' ? 'vDisk'.' ' : '') . $row->volume_name . '</span></h2>
                //                 </td>
                //                 <td style="width: 20%;" class="text-right  ">
                //                     <div class="d-flex">
                //                     <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-blue ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                     <span class=" ">' . $row->size . ' ' . $row->size_unit . '</span>
                //                 </div><div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                //                     <span class=" ">' . $row->format . '</span>
                //                 </div>
                //                 <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none; color: #262626;" class=" bg-new-yellow ml-auto mr-3  badge-new  text-center  font-weight-bold">
                //                     <span class=" ">' . $row->block_size . '</span>
                //                 </div>
                //                 <a type="button" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-html="true" data-original-title="' . ($row->tooltip ? $row->tooltip : '')  . '">
                //                     <img src="../public/icons/icon-details-raid-disk.png" style="margin-top: 2px; width: 26px;">
                //                     </a>
                //                      </div></td>
                //             </tr>
                //     </tbody>
                //     </table>
                //     </div>';
            }
            $html .= '</div> 
                                        </div>
                                        
                                        </div>      

                                    </div>
                                    </div>
                                        ';
        }
        $contract = DB::table('asset_comments as v')->select('v.*', 'u.user_image', 'u.id as user_id', 'u.firstname', 'u.lastname')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('asset_id', $q->id)->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Comments</div>
                            
                                                          <div class="block-content new-block-content pt-0" id="commentBlock"> ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                                                          <img width="40px" height="40" style="border-radius: 50%;"  class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? url('public') . '/img/profile-white.png' : url('public') . '/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->firstname . ' ' .  $c->lastname . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $contract = DB::table('asset_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('asset_id', $q->id)->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Attachments</div>
                            
                                                          <div class="block-content new-block-content  px-4 row pt-0" id="attachmentBlock"> ';
            foreach ($contract as $c) {

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



                $html .= '<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                                                          <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? url('public') . '/img/profile-white.png' : url('public') . '/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="../public/img/trash--v1.png?cache=1" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="../public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="../public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
</a></p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }


        $contract = DB::table('asset_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.asset_id', $q->id)->get();

        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Audit Trail</div>
                            
                                                          <div class="block-content new-block-content pt-0" id="commentBlock">';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                                                          <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? url('public') . '/img/profile-white.png' : url('public') . '/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ' . $c->description . '
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';




        return response()->json($html);
    }
    public function getWorkstationContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('assets as a')->select('a.*', 'sc.domain_name as system_category', 's.site_name', 'd.domain_name', 'c.client_display_name', 'o.operating_system_name', 'o.operating_system_image', 'm.vendor_name', 's.address', 's.city', 's.country', 's.phone', 's.zip_code', 's.province', 'at.asset_icon', 'at.asset_type_description', 'at.asset_type_description as asset_type_name', 'n.vlan_id as vlanId', 'n.subnet_ip', 'n.mask', 'usr.firstname as created_firstname', 'usr.lastname as created_lastname', 'upd.firstname as updated_firstname', 'upd.lastname as updated_lastname', 'c.logo', 'm.vendor_image', 'nz.network_zone_description', 'nz.tag_back_color', 'nz.tag_text_color')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->leftjoin('network as n', 'a.vlan_id', '=', 'n.id')->leftjoin('network_zone as nz', 'nz.network_zone_description', '=', 'n.zone')->leftjoin('users as usr', 'usr.id', '=', 'a.created_by')->leftjoin('users as upd', 'upd.id', '=', 'a.updated_by')->leftjoin('system_category as sc', 'a.system_category', '=', 'sc.id')->where('a.id', $id)->first();


        $contract_ssl_line_items = DB::Table('contract_assets as ca')->selectRaw('a.contract_no,c.client_display_name,a.contract_status,a.contract_start_date,a.contract_end_date,v.vendor_image,a.contract_description,a.contract_type,a.id,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM contracts  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.contract_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('contracts  as a', 'a.id', '=', 'ca.contract_id')->join('clients as c', 'c.id', '=', 'a.client_id')->join('vendors as v', 'v.id', '=', 'a.vendor_id')->groupBy('a.id')->where('a.is_deleted', 0)->where('ca.is_deleted', 0)->orderBy('a.contract_no', 'asc')->get();


        $ssl_line_items_2 = DB::Table('ssl_host as ca')->selectRaw(' a.cert_name , a.cert_status , a.cert_edate,a.cert_rdate,a.cert_sdate , a.cert_type , a.id , c.logo , v.vendor_image , a.description , c.client_display_name ,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM ssl_certificate  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.ssl_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('ssl_certificate  as a', 'a.id', '=', 'ca.ssl_id')->join('clients as c', 'c.id', '=', 'a.client_id')->leftjoin('vendors as v', 'v.id', '=', 'a.cert_issuer')->where('a.is_deleted', 0)->orderBy('a.cert_name', 'asc')->get();


        if ($q->AssetStatus == 1) {
            $html .= '<div class="block card-round   bg-new-green new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="../public/img/icon-active-removebg-preview.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">  ' . ($q->AssetStatus == '1' ? 'Active' : 'Inactive') . ' Workstation</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>';
        } else {
            $html .= '<div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="../public/img/action-white-end-revoke.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Asset Decomissioned</h4>';
            $renewed_qry = DB::Table('users')->Where('id', $q->InactiveBy)->first();



            $html .= '<p class="mb-0  header-new-subtext" style="line-height:17px">On ' . date('Y-M-d', strtotime($q->InactiveDate)) . ' by ' . @$renewed_qry->firstname . ' ' . @$renewed_qry->lastname . '</p>
                                    </div>
                                </div>';
        }


        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print"> <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" style="margin-right: -3px;> 
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="' . asset('public/img/paper-clip-white.png') . '" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="' . asset('public/img/comment-white.png') . '" width="20px"></a></span>';

        if (Auth::user()->role != 'read') {



            if ($q->AssetStatus == 1) {
                $html .= '<span  > 
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->AssetStatus . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Decomission" class=" "><img src="../public/img/action-white-end-revoke.png?cache=1" width="22px"></a>
                                         </span>';
            } else {
                $html .= '    <span  > 
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->AssetStatus . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="../public/img/icon-header-white-reactivate.png?cache=1" width="22px"></a>
                                         </span>';
            }
        }

        $html .= ' <a href="javascript:;" class="btn-clone" data-type="' . ucfirst($q->asset_type) . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Clone"  style="padding:5px 7px">
                                                <img src="../public/icons/icon-white-clone.png?cache=1" width="22px"  >
        <a  target="_blank" href="' . url("pdf-asset/") . '?id=' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Pdf"  style="padding:5px 7px">
                                                <img src="../public/img/action-white-pdf.png?cache=1" width="26px"  >
                                            </a>
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="../public/img/action-white-print.png?cache=1" width="20px">
                                            </a>';


        if (Auth::user()->role != 'read') {

            $html .= '<a   href="' . url('edit-assets-worksation') . '?id=' . $q->id . '" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="../public/img/action-white-edit.png?cache=1" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="../public/img/action-white-delete.png?cache=1" width="17px"></a>';
        }
        $asset_data = DB::table('asset_type')->where('asset_type_id', $q->asset_type_id)->first();
        $html .= '</div></div>
                            </div>
                        </div>

                            
                            <div class="block new-block pb-0 mt-4 " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content- push mb-0" jid >
                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">General Info</div>
                            <div class="col-sm-12">
                        <input type="hidden" name="attachment_array" id="attachment_array">
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Client</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->client_display_name . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Site</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->site_name . '
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Sub-Type</div> 
                                       </div>
                                            <div class="col-sm-4">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    Workstation
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Asset Type</div> 
                                       </div>
                                            <div class="col-sm-4">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->asset_type_id . '
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Platform</div> 
                                       </div>
                                            <div class="col-sm-4">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->platform . '
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                     
                                         
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-text-sec" style="padding:10px">
                                         

                                                      <img src="../public/client_logos/' . $q->logo . '" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>';



        if ($q->asset_type == 'physical') {

            $html .= '
                            <div class="block new-block pb-0 mt-4 " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content-  push mb-0" >
 <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Hardware Information</div>
                            
                            <div class="col-sm-12" >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Manufacturer</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->vendor_name . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Model</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->model . '
                                                
                                                </div> 
                                     
                                            </div>
                                            <div class="col-sm-3">
                                           <div class="bubble-new">Type</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->type . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                         <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Serial Number</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec" style="color: #4194F6;">
                                               <b>' . $q->sn . '</b>
                                                  
                                                </div> 
                                     
                                            </div>
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Asset Tag</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->asset_tag . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        ';
            if ($q->asset_type_description == 'Physical Server') {
                $html .= '<div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">CPU Model</div> 
                                       </div>
                                            <div class="col-sm-9">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->cpu_model . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                        <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">No. of Sockets</div> 
                                       </div>
                                            <div class="col-sm-1">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->cpu_sockets . '
                                                    </div> 
                                     
                                            </div>
                                         <div class="col-sm-2">
                                           <div class="bubble-new">No. of Cores</div> 
                                       </div>
                                            <div class="col-sm-1">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->cpu_cores . '
                                                    </div> 
                                     
                                            </div>
                                         <div class="col-sm-2">
                                           <div class="bubble-new">Frequency</div> 
                                       </div>
                                            <div class="col-sm-2">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->cpu_freq . '-GHz
                                                    </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        ';
            }

            $html .= '<div class="form-group row">
                      https://github.com/ajaxorg/ace/wiki/Default-Keyboard-Shortcuts                   <div class="col-sm-3">
                                           <div class="bubble-new">Memory (GB)</div> 
                                       </div>
                                            <div class="col-sm-2">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->memory . '  
                                                  
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
       ';
        }
        $html .= ' <div class="block new-block pb-0 mt-4 " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content-   push mb-0" >
                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Workstation Information</div>
                            <div class="col-sm-12">
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-12">
                                    <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Operating System</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->operating_system_name . '</b></div> 
                                     
                                            </div>

                                         </div>';
        $domain_data = DB::table('domains')->where('id', $q->domain)->first();
        $html .= '<div class="form-group row">
                                                    <div class="col-sm-3">
                                                    <div class="bubble-new">Role/Description</div> 
                                                </div>
                                                <div class="col-sm-9">
                                                    <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->role . '
                                                    
                                                    </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Hostname</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec" style="color: #4194F6; text-transform: uppercase;"><b>
                                                    ' . $q->hostname . '</b>
                                                  
                                                </div> 
                                     
                                            </div>
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Domain</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . @$domain_data->domain_name . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>';
        if ($q->platform == 'Windows') {
            $ad_domain_data = DB::table('domains')->where('id', @$q->device_info_ad_domain)->first();
            $html .= '<div class="form-group row">
                                                 <div class="col-sm-3">
                                                   <div class="bubble-new">A/D Domain</div> 
                                               </div>
                                                    <div class="col-sm-3">
                                                          <div class="bubble-white-new bubble-text-sec">
                                                            ' . @$ad_domain_data->domain_name . '
                                                          
                                                        </div> 
                                             
                                                    </div>
                                                 <div class="col-sm-3">
                                                   <div class="bubble-new">A/D OU</div> 
                                               </div>
                                                    <div class="col-sm-3">
                                                          <div class="bubble-white-new bubble-text-sec">
                                                            ' . $q->device_info_ou . '
                                                          
                                                        </div> 
                                             
                                                    </div>
                                                  
                                                </div>';
        }
        $html .= '</div>
                                        </div>
        
                                    </div>
                                    

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
         

         ';

        $html .= '
                            <div class="block new-block pb-0 mt-4 " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content-  push mb-0" >
 <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Hardware Information</div>
                            
                            <div class="col-sm-12" >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Manufacturer</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->vendor_name . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Model</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->model . '
                                                
                                                </div> 
                                     
                                            </div>
                                            <div class="col-sm-3">
                                           <div class="bubble-new">Type</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->type . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                         <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Serial Number</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec" style="color: #4194F6;">
                                                <b>' . $q->sn . '</b>
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        ';
        $html .= '<div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">CPU Model</div> 
                                       </div>
                                            <div class="col-sm-9">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->cpu_model . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        ';

        $html .= '<div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Memory (GB)</div> 
                                       </div>
                                            <div class="col-sm-2">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->memory . '  
                                                  
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
       ';



        $html .= '
                                   

                                                     
                 
                 </div>
             </div>


         </div>';


        $contract = DB::table('asset_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('asset_id', $q->id)->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Comments</div>
                            
                                                          <div class="block-content new-block-content pt-0" id="commentBlock"> ';
            foreach ($contract as $index => $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                                                          <img width="40px" height="40" style="border-radius: 50%;"  class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? url('public') . '/img/profile-white.png' : url('public') . '/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $contract = DB::table('asset_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('asset_id', $q->id)->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Attachments</div>
                            
                                                          <div class="block-content new-block-content  px-4 row pt-0" id="attachmentBlock"> ';
            foreach ($contract as $c) {

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



                $html .= '<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                                                          <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? url('public') . '/img/profile-white.png' : url('public') . '/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="../public/img/trash--v1.png?cache=1" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="../public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="' . url('public') . '/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
</a></p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }





        $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';




        return response()->json($html);
    }
    public function getMobileContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('assets as a')->select('a.*', 'sc.domain_name as system_category', 's.site_name', 'd.domain_name', 'c.client_display_name', 'o.operating_system_name', 'o.operating_system_image', 'm.vendor_name', 's.address', 's.city', 's.country', 's.phone', 's.zip_code', 's.province', 'at.asset_icon', 'at.asset_type_description', 'at.asset_type_description as asset_type_name', 'n.vlan_id as vlanId', 'n.subnet_ip', 'n.mask', 'usr.firstname as created_firstname', 'usr.lastname as created_lastname', 'upd.firstname as updated_firstname', 'upd.lastname as updated_lastname', 'c.logo', 'm.vendor_image', 'nz.network_zone_description', 'nz.tag_back_color', 'nz.tag_text_color')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->leftjoin('network as n', 'a.vlan_id', '=', 'n.id')->leftjoin('network_zone as nz', 'nz.network_zone_description', '=', 'n.zone')->leftjoin('users as usr', 'usr.id', '=', 'a.created_by')->leftjoin('users as upd', 'upd.id', '=', 'a.updated_by')->leftjoin('system_category as sc', 'a.system_category', '=', 'sc.id')->where('a.id', $id)->first();


        $contract_ssl_line_items = DB::Table('contract_assets as ca')->selectRaw('a.contract_no,c.client_display_name,a.contract_status,a.contract_start_date,a.contract_end_date,v.vendor_image,a.contract_description,a.contract_type,a.id,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM contracts  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.contract_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('contracts  as a', 'a.id', '=', 'ca.contract_id')->join('clients as c', 'c.id', '=', 'a.client_id')->join('vendors as v', 'v.id', '=', 'a.vendor_id')->groupBy('a.id')->where('a.is_deleted', 0)->where('ca.is_deleted', 0)->orderBy('a.contract_no', 'asc')->get();


        $ssl_line_items_2 = DB::Table('ssl_host as ca')->selectRaw(' a.cert_name , a.cert_status , a.cert_edate,a.cert_rdate,a.cert_sdate , a.cert_type , a.id , c.logo , v.vendor_image , a.description , c.client_display_name ,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM ssl_certificate  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.ssl_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('ssl_certificate  as a', 'a.id', '=', 'ca.ssl_id')->join('clients as c', 'c.id', '=', 'a.client_id')->leftjoin('vendors as v', 'v.id', '=', 'a.cert_issuer')->where('a.is_deleted', 0)->orderBy('a.cert_name', 'asc')->get();


        if ($q->AssetStatus == 1) {
            $html .= '<div class="block card-round   bg-new-green new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="../public/img/icon-active-removebg-preview.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">  ' . ($q->AssetStatus == '1' ? 'Active' : 'Inactive') . ' Mobile Device</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>';
        } else {
            $html .= '<div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="../public/img/action-white-end-revoke.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Asset Decomissioned</h4>';
            $renewed_qry = DB::Table('users')->Where('id', $q->InactiveBy)->first();



            $html .= '<p class="mb-0  header-new-subtext" style="line-height:17px">On ' . date('Y-M-d', strtotime($q->InactiveDate)) . ' by ' . @$renewed_qry->firstname . ' ' . @$renewed_qry->lastname . '</p>
                                    </div>
                                </div>';
        }


        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print"> <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" style="margin-right: -3px;> 
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="' . asset('public/img/paper-clip-white.png') . '" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="' . asset('public/img/comment-white.png') . '" width="20px"></a></span>';

        if (Auth::user()->role != 'read') {



            if ($q->AssetStatus == 1) {
                $html .= '<span  > <a href="javascript:;" class="btnEnd"  data="' . $q->AssetStatus . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Decomission" class=" "><img src="../public/img/action-white-end-revoke.png?cache=1" width="22px"></a>
                                         </span>';
            } else {
                $html .= '    <span  > 
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->AssetStatus . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="../public/img/icon-header-white-reactivate.png?cache=1" width="22px"></a>
                                         </span>';
            }
        }

        $html .= ' <a href="javascript:;" class="btn-clone" data-type="' . ucfirst($q->asset_type) . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Clone"  style="padding:5px 7px">
                                                <img src="../public/icons/icon-white-clone.png?cache=1" width="22px"  >
        <a  target="_blank" href="' . url("pdf-asset/") . '?id=' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Pdf"  style="padding:5px 7px">
                                                <img src="../public/img/action-white-pdf.png?cache=1" width="26px"  >
                                            </a>
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="../public/img/action-white-print.png?cache=1" width="20px">
                                            </a>';


        if (Auth::user()->role != 'read') {

            $html .= '<a   href="' . url('edit-assets-mobile-device') . '?id=' . $q->id . '" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="../public/img/action-white-edit.png?cache=1" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="../public/img/action-white-delete.png?cache=1" width="17px"></a>';
        }
        $asset_data = DB::table('asset_type')->where('asset_type_id', $q->asset_type_id)->first();
        $html .= '</div></div>
                            </div>
                        </div>

                            
                            <div class="block new-block pb-0 mt-4 " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content- push mb-0" jid >
                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">General Info</div>
                            <div class="col-sm-12">
                        <input type="hidden" name="attachment_array" id="attachment_array">
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Client</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->client_display_name . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Site</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->site_name . '
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Sub-Type</div> 
                                       </div>
                                            <div class="col-sm-4">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    Mobile Device
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Platform</div> 
                                       </div>
                                            <div class="col-sm-4">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->platform . '
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                     
                                         
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-text-sec" style="padding:10px">
                                         

                                                      <img src="../public/client_logos/' . $q->logo . '" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>




                            <div class="block new-block pb-0 mt-4 " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content-   push mb-0" >
                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Mobile Device Information</div>
                            <div class="col-sm-12">
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-12">
                                    <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Operating System</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->operating_system_name . '</b></div> 
                                     
                                            </div>

                                         </div>';
        $domain_data = DB::table('domains')->where('id', $q->domain)->first();
        $html .= '<div class="form-group row">
                                                    <div class="col-sm-3">
                                                    <div class="bubble-new">Role/Description</div> 
                                                </div>
                                                <div class="col-sm-9">
                                                    <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->role . '
                                                    
                                                    </div> 
                                     
                                            </div>
                                          
                                        </div>';
        $html .= '</div>
                                        </div>
        
                                    </div>
                                    

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
         

         ';

        $html .= '
                            <div class="block new-block pb-0 mt-4 " style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content-  push mb-0" >
 <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Hardware Information</div>
                            
                            <div class="col-sm-12" >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Manufacturer</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b style="color: #4194F6;">' . $q->vendor_name . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Model</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->model . '
                                                
                                                </div> 
                                     
                                            </div>
                                            <div class="col-sm-3">
                                           <div class="bubble-new">Type</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->type . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                         <div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Serial Number</div> 
                                       </div>
                                            <div class="col-sm-3">
                                                  <div class="bubble-white-new bubble-text-sec" style="color: #4194F6;">
                                                <b>' . $q->sn . '</b>
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        ';
        $html .= '<div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">CPU Model</div> 
                                       </div>
                                            <div class="col-sm-9">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->cpu_model . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        ';

        $html .= '<div class="form-group row">
                                         <div class="col-sm-3">
                                           <div class="bubble-new">Memory (GB)</div> 
                                       </div>
                                            <div class="col-sm-2">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->memory . '  
                                                  
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
       ';



        $html .= '
                                   

                                                     
                 
                 </div>
             </div>


         </div>';


        $contract = DB::table('asset_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('asset_id', $q->id)->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Comments</div>
                            
                                                          <div class="block-content new-block-content pt-0" id="commentBlock"> ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                                                          <img width="40px" height="40" style="border-radius: 50%;"  class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? url('public') . '/img/profile-white.png' : url('public') . '/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $contract = DB::table('asset_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('asset_id', $q->id)->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Attachments</div>
                            
                                                          <div class="block-content new-block-content  px-4 row pt-0" id="attachmentBlock"> ';
            foreach ($contract as $c) {

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



                $html .= '<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                                                          <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? url('public') . '/img/profile-white.png' : url('public') . '/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="../public/img/trash--v1.png?cache=1" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="../public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="../public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
</a></p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }





        $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';




        return response()->json($html);
    }


    public function getVirtualContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('assets as a')->select('a.*', 's.site_name', 'd.domain_name', 'c.client_display_name', 'o.operating_system_name', 'o.operating_system_image', 'm.vendor_name', 's.address', 's.city', 's.country', 's.phone', 's.zip_code', 's.province', 'at.asset_icon', 'at.asset_type_description', 'at.asset_type_description as asset_type_name', 'n.vlan_id as vlanId', 'n.subnet_ip', 'n.mask', 'usr.firstname as created_firstname', 'usr.lastname as created_lastname', 'upd.firstname as updated_firstname', 'upd.lastname as updated_lastname', 'c.logo', 'm.vendor_image')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->leftjoin('network as n', 'a.vlan_id', '=', 'n.id')->leftjoin('users as usr', 'usr.id', '=', 'a.created_by')->leftjoin('users as upd', 'upd.id', '=', 'a.updated_by')->where('a.id', $id)->first();



        if ($q->AssetStatus == 1) {
            $html .= '<div class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-asset-white.png?cache=1" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">  Asset Active</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>';
        } else {
            $html .= '<div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-asset-white.png?cache=1" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Asset Decomissioned</h4>';
            $renewed_qry = DB::Table('users')->Where('id', $q->InactiveBy)->first();



            $html .= '<p class="mb-0  header-new-subtext" style="line-height:17px">On ' . date('Y-M-d', strtotime($q->InactiveDate)) . ' by ' . @$renewed_qry->firstname . ' ' . @$renewed_qry->lastname . '</p>
                                    </div>
                                </div>';
        }


        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print">';

        if (Auth::user()->role != 'read') {



            if ($q->AssetStatus == 1) {
                $html .= '<span  > 
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->AssetStatus . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Decomission" class=" "><img src="public/img/action-white-end-revoke.png?cache=1" width="22px"></a>
                                         </span>';
            } else {
                $html .= '    <span  > 
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->AssetStatus . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="public/img/icon-header-white-renew.png?cache=1" width="20px"></a>
                                         </span>';
            }
        }

        $html .= ' <a  target="_blank" href="pdf-asset?id=' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="PDF" class=" ">
                                                <img src="public/img/action-white-pdf.png?cache=1" width="26px"  style="padding:5px 7px">
                                            </a>
     <a href="javascript:;" onclick="window.print()" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png?cache=1" width="20px">
                                            </a>';


        if (Auth::user()->role != 'read') {

            $html .= '<a   href="edit-assets?id=' . $q->id . '" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png?cache=1" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png?cache=1" width="17px"></a>';
        }

        $html .= '</div></div>
                            </div>

                        <div class="block new-block position-relative mt-3" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">' . $q->asset_type_description . '</div>
                            
                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                     <div class="row justify-content- position-relative inner-body-content push mb-0" >
                            <div class="top-right-div top-right-div-yellow text-capitalize">Client</div>  
                            <div class="col-sm-12>
                                     
                                    <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Client</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>' . $q->client_display_name . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Site</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    <b>' . $q->site_name . '</b><br>
                                                    <span>' . $q->address . '</span><br>
                                                    <span>' . $q->city . ',' . $q->province . '</span><br>
                                                    <span>' . $q->zip_code . '</span><br>
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                           
                                         
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                         

                                                      <img src="public/client_logos/' . $q->logo . '" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>




                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push mb-0" >
 <div class="top-right-div top-right-div-red text-capitalize">Host Information
</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Operating System</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>' . $q->operating_system_name . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">FQDN</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->fqdn . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Environment</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->use_ . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                                    <div class="col-sm-4">
                                                    <div class="bubble-new">Role</div> 
                                                </div>
                                                    <div class="col-sm-8">
                                                        <div class="bubble-white-new bubble-text-sec">
                                                        ' . $q->role . '
                                                        
                                                        </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="row form-group mt-5">
                                                       <div class="col-sm-3 t er">

<div class="contract_type_button  w-100 mr-4 mb-3">
  <input type="checkbox" class="custom-control-input " id="disaster_recovery1" name="disaster_recovery" disabled="" value="1" ' . ($q->disaster_recovery == 1 ? 'checked' : '') . '>
  <label class="btn btn-new w-100 py-1 font-11pt" for="disaster_recovery1">D/R Plan</label>
</div>
</div>
 

 


 <div class="col-sm-3  ">

<div class="contract_type_button  w-100 mr-4  ">
          <input type="checkbox" class="custom-control-input" id="clustered" name="clustered"  disabled="" value="1"  ' . ($q->clustered == 1 ? 'checked' : '') . '>
  <label class="btn btn-new w-100  py-1 font-11pt" for="clustered"> Clustered</label>
</div>
</div>

 <div class="col-sm-3 text-center">

<div class="contract_type_button  w-100 mr-4  ">
     <input type="checkbox" class="custom-control-input" id="internet_facing" name="internet_facing" value="1" disabled="" ' . ($q->internet_facing == 1 ? 'checked' : '') . '>

       <label class="btn btn-new w-100  py-1 font-11pt" for="internet_facing"> Internet Facing</label>
</div>
</div>




 <div class="col-sm-3  text-right">

<div class="contract_type_button  w-100 mr-4 ">
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" ' . ($q->load_balancing == 1 ? 'checked' : '') . '>
  <label class="btn btn-new w-100 py-1 font-11pt" for="load_balancing"> Load Balanced</label>
</div>
</div>


 <div class="col-sm-3  text-right">

<div class="contract_type_button  w-100 mr-4 ">
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" ' . ($q->ntp == 1 ? 'checked' : '') . '>
  <label class="btn btn-new w-100 py-1 font-11pt" for="load_balancing"> SSL Certificate</label>
</div>
</div>


 <div class="col-sm-3  text-right">

<div class="contract_type_button  w-100 mr-4 ">
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" ' . ($q->HasWarranty == 1 ? 'checked' : '') . '>
  <label class="btn btn-new w-100 py-1 font-11pt" for="load_balancing"> Supprted/Unsupported</label>
</div>
</div>


</div>
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                           
                                                      <img src="public/operating_system_logos/' . $q->operating_system_image . '" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>



                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push mb-0" >
 <div class="top-right-div top-right-div-blue text-capitalize">Virtual Hardware</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                    
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">vCPUs</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->vcpu . '  
                                                  
                                                </div> 
                                     
                                            </div>
      </div>
                                          <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Memory</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          ' . $q->memory . '  
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         
                                    </div>
                                    <div class="col-sm-2">
                                                <div class="bubble-white-new bubble-text-sec" style="padding:10px">
 

                                                      <img src="public/img/static-vm.png?cache=1" style="width: 100%;">
                                                </div> </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>








                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push mb-0" >
 <div class="top-right-div top-right-div-green text-capitalize">Networking</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
      <div class="col-sm-10">
        <div class="inner-body-content position-relative px-3">
                                   <div class="top-div text-capitalize w-25 font-size-sm" >Primary IP
</div>                               

                                        <div class="form-group    row">
                                                             <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">Network Zone
</div> 
                                       </div>                           
                                            <div class="col-sm-3 form-group ">
                                                
                                          ';
        if ($q->network_zone == 'Internal') {
            $html .= '<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px" class=" text-center border-none text-white font-size-md bg-secondary bubble-white-new bubble-text-sec"  ><b>' . $q->network_zone . '</b></div>';
        } elseif ($q->network_zone == 'Secure') {
            $html .= '<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center border-none font-size-md text-white bg-info bubble-white-new bubble-text-sec"  ><b>' . $q->network_zone . '</b></div>';
        } elseif ($q->network_zone == 'Greenzone') {
            $html .= '<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center border-none font-size-md text-white bg-success bubble-white-new bubble-text-sec"  ><b>' . $q->network_zone . '</b></div>';
        } elseif ($q->network_zone == 'Guest') {
            $html .= '<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center font-size-md border-none text-white bg-warning"  ><b>' . $q->network_zone . '</b></div>';
        } elseif ($q->network_zone == 'Semi-Trusted') {
            $html .= '<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white border-none bubble-white-new bubble-text-sec  " style="background:#FFFF11;color: black"  ><b>' . $q->network_zone . '</b></div>';
        } elseif ($q->network_zone == 'Public DMZ' || $q->network_zone == 'Public' || $q->network_zone == 'Servers Public DMZ') {
            $html .= '<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white bubble-white-new border-none bubble-text-sec bg-danger"  ><b>' . $q->network_zone . '</b></div>
                                               ';
        } else {
            $html .= '<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white bubble-white-new border-none bubble-text-sec  "  ><b>' . $q->network_zone . '</b></div>';
        }
        $html .= '</div> 
                                            </div>
                                            <div class="row">
                                          
    


                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">vLAN ID
</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-first">
                                                   ' . $q->vlanId . ' 
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                      
                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">IP Address</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           
                                                       ' . $q->ip_address . '' . $q->mask . '  
                                                </div> 
                                     
                                            </div>

                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">Gateway Ip</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           
                                                          ' . $q->subnet_ip . '
                                                </div> 
                                     
                                            </div>
                                       

</div>
                                    </div>
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/img/static-networking.png?cache=1" style="width: 100%;">
                                                </div> 

                                    </div>';
        $asset_ip = DB::Table('asset_ip_addresses')->where('asset_id', $q->id)->orderby('ip_address_name', 'asc')->get();
        if (sizeof($asset_ip) > 0) {
            $html .= '     <div class="col-sm-12 mt-4">
        <div class="inner-body-content position-relative px-3">
                <div class="top-div text-capitalize w-25 font-size-sm" >Additional IPs </div>                               

                                        <div class="row form-group">
                                       
                                                ';
            foreach ($asset_ip as $i) {
                $html .= '<div class="col-sm-6">
                                                    <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 50%">
                                                        <p class="mb-0 mr-3 mx-auto  text-white text-center  px-2 " style="max-width: 150px;border-radius: 10px;border: 1px solid grey;padding:5px 5px;background: #595959;"><b>' . $i->ip_address_name . '</b></p> 
                                                    </td>
                                                    <td class="js-task-content  text-center">
                                                        <label class="mb-0 bubble-text-sec font-12pt">' . $i->ip_address_value . '</label>
                                                    </td>
                                                   
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>
                                </div>';
            }
            $html .= '</div>                         
                                  
                                    </div>
                                    </div>';
        }

        $html .= '</div>
                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>



                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push mb-0" >
 <div class="top-right-div top-right-div-yellow text-capitalize">Managed Services</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
      <div class="col-sm-10">
     
                                        <div class="form-group row">

                                               <div class="col-sm-4 form-group ">
                                                    <div class="bubble-new">App Owner</div> 
                                                </div>
                                                <div class="col-sm-8 form-group ">
                                                    <div class="bubble-white-new bubble-text-first">
                                                    ' . $q->app_owner . '                                                   
                                                    </div>                                      
                                                </div>
                                          


                                                <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">SLA
</div> 
                                       </div>                           
                                            <div class="col-sm-3 form-group ">
                                                
                                                        ';

        $sla = DB::Table('sla')->Where('id', $q->sla)->first();
        $html .= '
                                                <div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px;background:{{@$sla->tag_back_color}};color:{{@$sla->tag_text_color}}" class=" text-center font-size-md bubble-white-new border-none bubble-text-sec"  ><b>' . $q->sla . '</b></div>';
        $html .= '
                                            </div> 
                                            </div>
                                            <div class="row">

 <div class="col-sm-3 mb-3 ">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
          <input type="checkbox" class="custom-control-input" id="patched" name="patched" value="1" disabled="" ' . ($q->patched == 1 ? 'checked' : '') . '>
  <label class="btn btn-new w-100  py-1 font-11pt " for="patched"> Patched</label>
</div>
</div>

 <div class="col-sm-3   mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
       <input type="checkbox" class="custom-control-input" id="monitored" name="monitored" value="1" disabled="" ' . ($q->monitored == 1 ? 'checked' : '') . '>
       <label class="btn btn-new w-100  py-1 font-11pt " for="monitored">Monitored</label>
</div>
</div>

 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
        <input type="checkbox" class="custom-control-input" id="backup" name="backup" value="1" disabled="" ' . ($q->backup == 1 ? 'checked' : '') . '>
       <label class="btn btn-new w-100   py-1 font-11pt" for="backup">Backup</label>
</div>
</div>


 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
           <input type="checkbox" class="custom-control-input" id="antivirus" disabled="" name="antivirus" value="1"   ' . ($q->antivirus == 1 ? 'checked' : '') . '>
       <label class="btn btn-new w-100   py-1 font-11pt" for="antivirus">Anti-Virus
</label>
</div>
</div>


 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""  >
          <input type="checkbox" class="custom-control-input" id="replicated"  disabled="" name="replicated" value="1" ' . ($q->replicated == 1 ? 'checked' : '') . '>
       <label class="btn btn-new w-100  py-1 font-11pt" for="replicated">Replicated
</label>
</div>
</div>

 <div class="col-sm-3 ">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
          <input type="checkbox" class="custom-control-input" id="disaster_recovery"  disabled="" name="disaster_recovery" value="1" ' . ($q->disaster_recovery == 1 ? 'checked' : '') . '>
       <label class="btn btn-new w-100  py-1 font-11pt" for="disaster_recovery">Vulnerability Scan</label>
</div>
</div>

 <div class="col-sm-3  ">


<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
                     <input type="checkbox" class="custom-control-input" id="syslog" disabled="" name="syslog" value="1" ' . ($q->syslog == 1 ? 'checked' : '') . ' >
       <label class="btn btn-new w-100  py-1 font-11pt" for="syslog">SIEM</label>
</div>
</div>

 <div class="col-sm-3  ">

<div class="contract_type_button w-100 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
      <input type="checkbox" class="custom-control-input" id="smtp" name="smtp" value="1" disabled="" ' . ($q->smtp == 1 ? 'checked' : '') . '>
       <label class="btn btn-new w-100  py-1 font-11pt" for="smtp">SMTP</label>
</div>
</div>

 
</div>
                                            
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/img/static-amaltitek.png?cache=1" style="width: 100%;">
                                                </div> 

                                    </div>
                                   
     <div class="col-sm-12 mt-4">
       
                                    </div>
                                   

                          </div>
                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>


         </div>';

        $ssl_line_items = DB::Table('contract_assets as ca')->selectRaw('a.contract_no,c.client_display_name,a.contract_status,a.contract_start_date,a.contract_end_date,v.vendor_image,a.contract_description,a.contract_type,a.id,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM contracts  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.contract_id order by id desc limit 1 ) as rownumber ')

            ->where('ca.hostname', $q->id)->join('contracts  as a', 'a.id', '=', 'ca.contract_id')->join('clients as c', 'c.id', '=', 'a.client_id')->join('vendors as v', 'v.id', '=', 'a.vendor_id')->groupBy('a.id')->where('a.is_deleted', 0)->where('ca.is_deleted', 0)->orderBy('a.contract_no', 'asc')->get();

        if (sizeof($ssl_line_items) > 0) {

            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Support Contracts</div>
                            
                                                          <div class="block-content new-block-content pt-0" id="commentBlock">
 ';

            foreach ($ssl_line_items as $l) {

                $contract_end_date = date('Y-M-d', strtotime($l->contract_end_date));
                $today = date('Y-m-d');
                $earlier = new DateTime($l->contract_end_date);
                $later = new DateTime($today);

                $abs_diff = $later->diff($earlier)->format("%a"); //3

                $html .= '<div class="block block-rounded   table-block-new mb-2 pb-0  -  ">
                    
                        <div class="block-content d-flex py-3 mt-0 position-relative">
                                        <div class="mr-3   align-items-center  d-flex" style="width:100px">
                                            <img src="public/vendor_logos/' . $l->vendor_image . '" class="rounded-circle" width="75px" style="max-width:100px;object-fit: cover;">
                                        </div>
                                        <div class="  " style="width:80%">
                                                  <p class="font-10pt mb-0 text-truncate c1">' . $l->contract_type . '</p>
                                                                <p class="font-10pt mb-0 text-truncate  c4" >' . $l->contract_no . '</p>
                                                    <p class="font-10pt mb-0 text-truncate c2">' . $l->contract_description . '</p>
                                                    <p class="font-10pt mb-0 text-truncate c3"><b>' . $l->client_display_name . '</b></p>
                                        </div>
                                        <div class=" text-right" style="width:35%;;">
                                   <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                                        
                                                                        <div class="d-inline-flex justify-content-end align-items-center">
                                                                            <span class=" mr-2 font-10pt"><b>' . date('Y-M-d', strtotime($l->contract_end_date)) . '</b></span>';

                if ($l->contract_status == 'Active') {

                    if ($abs_diff <= 30) {
                        $html .= '<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-w600 text-dark"  >
                                                                                 <img src="public/img/status-upcoming-.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Upcoming</span>
                                                                    </div> ';
                    } else {
                        $html .= ' <div class=" bg-new-green ml-auto  badge-new  text-center font-w600   text-white"  >
                                                                                 <img src="public/img/status-white-active.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Active</span>
                                                                    </div>  
                                                  ';
                    }
                } elseif ($l->contract_status == 'Inactive') {

                    $html .= '<div class=" bg-new-blue ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-renewed.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Renewed</span>
                                
                                                                    </div>  ';
                } elseif ($l->contract_status == 'Expired/Ended') {

                    $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/action-white-end-revoke.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                
                                                                    </div>';
                } elseif ($l->contract_status == 'Ended') {
                    $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/action-white-end-revoke.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                                                    </div>';
                } elseif ($l->contract_status == 'Expired') {
                    $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-expired.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Expired</span>
                                                                    </div>';
                }

                $html .= '                            </div>

                                                                    <div >
                                                                                   <p class="font-10pt mb-0 text-truncate c2"> <small><i>' . $abs_diff . ' days remaining</i></small></p>

                                                                    </div>
                                                                   
                                                                </div>
                                                                 
 <div style="position: absolute;width: 100%; bottom: 5px;right: 10px;">
                                       <a href="' . url('print-contract') . '?id=' . $l->id . '&page=' . ceil($l->rownumber / 10) . '" target="_blank" class="toggle led" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Open" data-html="true" data-original-title="Open">
          

          <img src="public/img/icon-eye-grey.png?cache=1" class="mr-2 " width="23px" height="23px">
                                                                        </a>
                                                                           
                                                                    
                                                                    </div>
                                        </div>
                                </div>
                            </div>


';
            }

            $html .= '</div>
</div>';
        }

        $ssl_line_items = DB::Table('ssl_host as ca')->selectRaw(' a.cert_name , a.cert_status , a.cert_edate , a.cert_type , a.id , c.logo , v.vendor_image , a.description , c.client_display_name ,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM ssl_certificate  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.ssl_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname', $q->id)->join('ssl_certificate  as a', 'a.id', '=', 'ca.ssl_id')->leftjoin('clients as c', 'c.id', '=', 'a.client_id')->leftjoin('vendors as v', 'v.id', '=', 'a.cert_issuer')->where('a.is_deleted', 0)->orderBy('a.cert_name', 'asc')->get();

        if (sizeof($ssl_line_items) > 0) {

            $html .= '  <div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">SSL Certificates</div>
                            
                                                          <div class="block-content new-block-content pt-0" id="commentBlock">
';

            foreach ($ssl_line_items as $l) {

                $contract_end_date = date('Y-M-d', strtotime($l->cert_edate));
                $today = date('Y-m-d');
                $earlier = new DateTime($l->cert_edate);
                $later = new DateTime($today);

                $abs_diff = $later->diff($earlier)->format("%a"); //3

                $html .= '<div class="block block-rounded   table-block-new mb-2 pb-0  -   ">
                    
                        <div class="block-content d-flex py-3 mt-0 position-relative">
                                        <div class="mr-3   align-items-center  d-flex" style="width:100px">';

                if ($l->cert_type == 'public') {
                    $html .= '<img src="public/vendor_logos/' . $l->vendor_image . '" class="rounded-circle" width="75px" style="max-width:100px;object-fit: cover;">';
                } else {
                    $html .= '<img src="public/client_logos/' . $l->logo . '" class="rounded-circle" width="75px" style="max-width:100px;object-fit: cover;">';
                }
                $html .= '
                                        </div>
                                        <div class="  " style="width:80%">
                                                     <p class="font-10pt mb-0 text-truncate c1">';
                if ($l->cert_type == 'internal') {
                    $html .= 'Internal SSL Certificate';
                } else {
                    $html .= 'Public SSL Certificate';
                }

                $html .= ' </p>
                                                               <p class="font-10pt mb-0 text-truncate  c4" >' . $l->cert_name . '</p>
                                                    <p class="font-10pt mb-0 text-truncate c2">' . $l->description . '</p>
                                                    <p class="font-10pt mb-0 text-truncate c3">' . $l->client_display_name . '<b>
                                                        ';

                $html .= 'SSl Certificate

                                                    </b></p>
                                        </div>
                                        <div class=" text-right" style="width:35%;;">
                                   <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                                        
                                                                        <div class="d-inline-flex justify-content-end align-items-center">
                                                                            <span class=" mr-2 font-10pt"><b>' . date('Y-M-d', strtotime($l->cert_edate)) . '</b></span>';

                if ($l->cert_status == 'Active') {

                    if ($abs_diff <= 30) {
                        $html .= '<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-w600 text-dark"  >
                                                                                 <img src="public/img/status-upcoming-.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Upcoming</span>
                                                                    </div> ';
                    } else {
                        $html .= ' <div class=" bg-new-green ml-auto  badge-new  text-center font-w600   text-white"  >
                                                                                 <img src="public/img/status-white-active.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Active</span>
                                                                    </div>  ';
                    }
                } elseif ($l->cert_status == 'Inactive') {

                    $html .= '<div class=" bg-new-blue ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-renewed.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Renewed</span>
 
                                                                    </div>  ';
                } elseif ($l->cert_status == 'Expired/Ended') {

                    $html .= '   <div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/action-white-end-revoke.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                 
                                                                    </div>';
                } elseif ($l->cert_status == 'Ended') {
                    $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/action-white-end-revoke.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                                                    </div>';
                } elseif ($l->cert_status == 'Expired') {
                    $html .= '<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-expired.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Expired</span>
                                                                    </div>';
                }

                $html .= '</div>

                                                                    <div >
                                                                                   <p class="font-10pt mb-0 text-truncate c2"> <small><i>' . $abs_diff . ' days remaining</i></small></p>

                                                                    </div>
                                                                   
                                                                </div>
                                                                 
 <div style="position: absolute;width: 100%; bottom: 5px;right: 10px;">
                                       <a href="' . url('ssl-certificate') . '?id=' . $l->id . '&page=' . ceil($l->rownumber / 10) . '" target="_blank" class="toggle led" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Open" data-html="true" data-original-title="Open">
          

          <img src="public/img/icon-eye-grey.png?cache=1" class="mr-2 " width="23px" height="23px">
                                                                        </a>
                                                                           
                                                                    
                                                                    </div>
                                        </div>
                                </div>
                            </div>';
            }
            $html .= '</div>
</div>';
        }

        $contract = DB::table('asset_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('asset_id', $q->id)->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Comments</div>
                            
                                                          <div class="block-content new-block-content pt-0" id="commentBlock"> ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                                                          <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? url('public') . '/img/profile-white.png' : url('public') . '/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $contract = DB::table('asset_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('asset_id', $q->id)->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Attachments</div>
                            
                                                          <div class="block-content new-block-content  px-4 row pt-0" id="attachmentBlock"> ';
            foreach ($contract as $c) {

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



                $html .= '<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                                                          <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? url('public') . '/img/profile-white.png' : url('public') . '/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png?cache=1" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
</a></p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }


        $contract = DB::table('asset_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.asset_id', $q->id)->get();

        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative mt-4" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Audit Trail</div>
                            
                                                          <div class="block-content new-block-content pt-0" id="commentBlock">';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                                                          <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? url('public') . '/img/profile-white.png' : url('public') . '/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ' . $c->description . '
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';




        return response()->json($html);
    }


    public function removeOption(Request $request)
    {
        DB::table($request->table)->where('id', $request->id)->update([
            'is_deleted' => 1
        ]);
        return response()->json('success');
    }
    public function removeOption_(Request $request)
    {
        DB::table($request->table)->where($request->column, $request->value)->update([
            $request->column => null
        ]);
        if ($request->table == "asset_network_adapter") {
            if ($request->column != 'adapter_type_') {
                DB::table('tech_spec_network_adapter')->where($request->column, $request->value)->update([
                    $request->column => null
                ]);
            }
        } else if ($request->table == "asset_port_map") {
            DB::table('tech_spec_port_map')->where($request->column, $request->value)->update([
                $request->column => null
            ]);
        } else if ($request->table == "asset_raid_volume") {
            DB::table('tech_spec_raid_volume')->where($request->column, $request->value)->update([
                $request->column => null
            ]);
        } else if ($request->table == "asset_virtual_disks") {
            DB::table('tech_spec_virtual_disks')->where($request->column, $request->value)->update([
                $request->column => null
            ]);
        } else if ($request->table == "assets") {
            DB::table('tech_spec')->where($request->column, $request->value)->update([
                $request->column => null
            ]);
        } else if ($request->table == "asset_logical_volume") {
            DB::table('tech_spec_logical_volume')->where($request->column, $request->value)->update([
                $request->column => null
            ]);
        }
        return response()->json('success');
    }


    // public function getAvailableAssets(Request $request)
    // {
    //     $excludeIds = $request->input('exclude_ids', []);
    //     $search = $request->input('search', '');

    //     // Convert exclude_ids to array if it's a string
    //     if (is_string($excludeIds)) {
    //         $excludeIds = explode(',', $excludeIds);
    //     }

    //     $query = DB::table('assets as a')
    //         ->select(
    //             'a.id',
    //             'a.hostname',
    //             'a.fqdn',
    //             'a.sn',
    //             'a.asset_type',
    //             'a.AssetStatus',
    //             'o.operating_system_name',
    //             'at.asset_icon',
    //             'at.asset_type_description'
    //         )
    //         ->leftJoin('operating_systems as o', 'a.os', '=', 'o.id')
    //         ->leftJoin('asset_type as at', 'a.asset_type_id', '=', 'at.asset_type_id')
    //         ->where('a.is_deleted', 0);

    //     // Exclude already assigned assets
    //     if (!empty($excludeIds)) {
    //         $query->whereNotIn('a.id', $excludeIds);
    //     }

    //     // Add search functionality
    //     if (!empty($search)) {
    //         $query->where(function($q) use ($search) {
    //             $q->where('a.hostname', 'like', "%{$search}%")
    //               ->orWhere('a.sn', 'like', "%{$search}%");
    //         });
    //     }

    //     // You might want to add more conditions here based on your business logic
    //     // For example, only show assets that are not already assigned to other contracts

    //     $assets = $query->orderBy('a.hostname', 'asc')->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $assets,
    //         'count' => $assets->count()
    //     ]);
    // }

    public function getAvailableAssets(Request $request)
    {
        $excludeIds   = $request->input('exclude_ids', []);
        $search       = $request->input('search', '');
        $clientId     = $request->input('client_id');
        $affiliateIds = $request->input('affiliate_ids', []); // 🔹 also client_ids

        // Convert exclude_ids to array if string
        if (is_string($excludeIds)) {
            $excludeIds = explode(',', $excludeIds);
        }

        $query = DB::table('assets as a')
            ->select(
                'a.id',
                'a.hostname',
                'a.fqdn',
                'a.sn',
                'a.asset_type',
                'a.AssetStatus',
                'o.operating_system_name',
                'at.asset_icon',
                'at.asset_type_description'
            )
            ->leftJoin('operating_systems as o', 'a.os', '=', 'o.id')
            ->leftJoin('asset_type as at', 'a.asset_type_id', '=', 'at.asset_type_id')
            ->where('a.is_deleted', 0);

        // 🔹 Client + Affiliate client filtering
        if (!empty($clientId) || !empty($affiliateIds)) {
            $query->where(function ($q) use ($clientId, $affiliateIds) {

                if (!empty($clientId)) {
                    $q->where('a.client_id', $clientId);
                }

                if (!empty($affiliateIds)) {
                    $q->orWhereIn('a.client_id', $affiliateIds);
                }
            });
        }

        // Exclude already assigned assets
        if (!empty($excludeIds)) {
            $query->whereNotIn('a.id', $excludeIds);
        }

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('a.hostname', 'like', "%{$search}%")
                    ->orWhere('a.sn', 'like', "%{$search}%");
            });
        }

        $assets = $query
            ->orderBy('a.hostname', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $assets,
            'count'   => $assets->count()
        ]);
    }
}
