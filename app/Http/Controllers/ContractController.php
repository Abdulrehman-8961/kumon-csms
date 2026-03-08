<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Auth;

use DB;

use Mail;

use Hash;

use PDF;



use Excel;

use Response;

use App\Exports\ExportContract;

use App\Exports\ExportExpiringContract;

use DateTime;



use Validator;
use Carbon\Carbon;



class ContractController extends Controller

{

    //

    public function __construct() {}





    public function Contract(Request $request)

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

                $contract = DB::table('contracts')->whereNotNull('client_id')->where('is_deleted', 0)->where('id', $qry->contract_id)->first();

                if ($contract == '') {



                    return view('error')->with(['message' => "Contract Not Found"]);
                }
            }

            return view('Contract', ['type' => $request->type, 'hash' => $request->key, 'id' => $qry->contract_id]);
        } else {

            if (isset($request->key) && $request->key != '') {

                $qry = DB::table('contract_sharable_links')->where('hash', $request->key)->first();

                if ($qry == '') {



                    return view('error')->with(['message' => "Invalid Link / Link Expired"]);
                } else {

                    $expiry_date = $qry->expiry_date;

                    if (date('Y-m-d') > $expiry_date) {

                        return view('error')->with(['message' => "Link Expired"]);
                    }

                    $contract = DB::table('contracts')->whereNotNull('client_id')->where('is_deleted', 0)->where('id', $qry->contract_id)->first();

                    if ($contract == '') {



                        return view('error')->with(['message' => "Contract Not Found"]);
                    }
                }

                return view('Contract', ['type' => $request->type, 'hash' => $request->key, 'id' => $qry->contract_id]);
            } else {

                return view('Contract', ['type' => $request->type]);
            }
        }
    }
    public function Contract_new(Request $request)

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

                $contract = DB::table('contracts')->whereNotNull('client_id')->where('is_deleted', 0)->where('id', $qry->contract_id)->first();

                if ($contract == '') {



                    return view('error')->with(['message' => "Contract Not Found"]);
                }
            }

            return view('ContractNewDesign', ['type' => $request->type, 'hash' => $request->key, 'id' => $qry->contract_id]);
        } else {

            if (isset($request->key) && $request->key != '') {

                $qry = DB::table('contract_sharable_links')->where('hash', $request->key)->first();

                if ($qry == '') {



                    return view('error')->with(['message' => "Invalid Link / Link Expired"]);
                } else {

                    $expiry_date = $qry->expiry_date;

                    if (date('Y-m-d') > $expiry_date) {

                        return view('error')->with(['message' => "Link Expired"]);
                    }

                    $contract = DB::table('contracts')->whereNotNull('client_id')->where('is_deleted', 0)->where('id', $qry->contract_id)->first();

                    if ($contract == '') {



                        return view('error')->with(['message' => "Contract Not Found"]);
                    }
                }

                return view('ContractNewDesign', ['type' => $request->type, 'hash' => $request->key, 'id' => $qry->contract_id]);
            } else {

                return view('ContractNewDesign', ['type' => $request->type]);
            }
        }
    }





    // public function AddContract($type)
    // {
    //     return view('AddContract', ['type' => $type]);
    // }
    // public function AddContract(Request $request)
    // {
    //     return view('AddContractNew');
    // }
    public function AddContract(Request $request)
    {
        return view('AddContract');
    }



    public function uploadContractAttachment(Request $request)

    {



        $attachment = $_FILES['attachment']['name'];

        $file_tmp = $_FILES['attachment']['tmp_name'];



        $fileExt = explode('.', $attachment);

        $fileActualExt = strtolower(end($fileExt));

        $key = $fileExt[0] . uniqid() . '.' . $fileActualExt;



        $request->file('attachment')->move(public_path('temp_uploads'), $key);



        return response()->json($key);
    }









    public function LoadContractAttachment(Request $request)

    {



        $request->header('Access-Control-Allow-Origin: *');



        // Allow the following methods to access this file

        $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');



        // Allow the following headers in preflight

        $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');



        // Allow the following headers in response

        $request->header('Access-Control-Expose-Headers: upload-offset');



        // Load our configuration for this server









        $uniqueFileID = $_GET["key"];



        $imagePointer = public_path("contract_attachment/" .  $uniqueFileID);

        if (!file_exists('..temp_uploads/' . $uniqueFileID)) {



            copy(public_path("contract_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID));
        }









        $imageName = $uniqueFileID;













        // if imageName was found in the DB, get file with imageName and return file object or blob

        $imagePointer = public_path("contract_attachment/" . $uniqueFileID);





        $fileObject = null;



        if ($imageName != '' && file_exists($imagePointer)) {



            $fileObject = file_get_contents($imagePointer);
        }







        // trigger load local image

        $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];



        if ($fileBlob) {

            $imagePointer = public_path("contract_attachment/" .  $imageName);

            $fileContextType = mime_content_type($imagePointer);

            $fileSize = filesize($imagePointer);



            $handle = fopen($imagePointer, 'r');

            if (!$handle) return false;

            $content = fread($handle, filesize($imagePointer));





            $response = Response::make($content);

            $response->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Length, X-Content-Transfer-Id');

            $response->header('Content-Type', $fileContextType);

            $response->header('Content-Length', $fileSize);

            $response->header('Content-Disposition', "inline; filename=$imageName");





            return $response;
        } else {

            http_response_code(500);
        }
    }





    public function revertContractAttachment(Request $request)

    {

        $key = str_replace('"', "", $request->key);



        unlink(public_path('temp_uploads/' . $key));



        echo json_encode(1);
    }







    public function GetAssetsByType(Request $request)

    {



        $userAccess = explode(',', @Auth::user()->access_to_client);

        $client_id = $request->client_id;



        $affliates = $request->affliates == '' ? '' : implode(',', $request->affliates);

        $where = '';

        if ($request->affliates != '') {

            $where .= " or a.client_id in ($affliates)";
        }

        if ($request->id != '') {

            $id = $request->id;

            $qry = DB::select("select * from assets as a where a.is_deleted=0 and HasWarranty=1 and (a.client_id='$client_id' $where) and  AssetStatus=1    order by a.sn asc ");
        } else {

            $qry = DB::select("select * from assets as a where a.is_deleted=0 and HasWarranty=1 and (a.client_id='$client_id'  $where) and  AssetStatus=1   order by a.sn asc");
        }





        return response()->json($qry);
    }

    public function Edit0Contract()
    {
        return view('EditContractOld');
    }
    public function EditContract(Request $request)
    {
        $id = $request->id;

        // Get main contract record
        $contract = DB::table('contracts')->where('id', $id)->first();

        // Fetch all related renewal emails
        $emails = DB::table('contract_emails')
            ->where('contract_id', $id)
            ->pluck('renewal_email')
            ->toArray();

        return view('EditContract', compact('contract', 'emails'));
    }
    public function CloneContract(Request $request)
    {
        $id = $request->id;

        // Get main contract record
        $contract = DB::table('contracts')->where('id', $id)->first();

        // Fetch all related renewal emails
        $emails = DB::table('contract_emails')
            ->where('contract_id', $id)
            ->pluck('renewal_email')
            ->toArray();

        return view('CloneContract', compact('contract', 'emails'));
    }
    public function RenewContract(Request $request)
    {
        $id = $request->id;

        // Get main contract record
        $contract = DB::table('contracts')->where('id', $id)->first();

        // Fetch all related renewal emails
        $emails = DB::table('contract_emails')
            ->where('contract_id', $id)
            ->pluck('renewal_email')
            ->toArray();

        return view('RenewContract', compact('contract', 'emails'));
    }


    public function SharinglinkContract(Request $request)

    {

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

            $contract = DB::table('contracts')->whereNotNull('client_id')->where('is_deleted', 0)->where('id', $qry->contract_id)->first();

            if ($contract == '') {



                return view('error')->with(['message' => "Contract Not Found"]);
            }
        }





        return view('exports/ExportPrintContract', ['id' => $qry->contract_id]);
    }







    public function ExportPrintContract()

    {





        return view('exports/ExportPrintContract');
    }



    public function Expiring30Days()

    {





        return view('Expiring30Days');
    }









    // public function Renew0Contract()

    // {
    //     return view('RenewContract');
    // }





    public function ExportPdfContract()

    {





        $pdf = PDF::loadView('exports/ExportPdfContract');



        return $pdf->stream('Contract.pdf');
    }





    public function ShowContractDetails(Request $request)

    {



        $details = DB::Table('contract_details')->where('contract_id', $request->id)->where('is_deleted', 0)->get();

        $count = 0;





        $html = '';

        if (count($details) > 0) {



            $html .= '<table class="table table-bordered">

                                        <thead class="thead thead-dark">

                                            <tr>

                                                <th class="text-center" style="width: 60px;">#</th>

                                                <th>PN #</th>

                                                <th class="text-center" >Qty</th>

                                                <th class="text-right" >MSRP</th>

                                                <th class="text-right"   >Comments</th>

                                            </tr>

                                        </thead>

                                        <tbody>';

            foreach ($details as $key => $d) {

                $count++;

                $asset = DB::Table('contract_assets as ca')->select('*', 'a.hostname as asset_name')->leftjoin('assets as  a', 'a.id', '=', 'ca.hostname')->where('ca.contract_detail_id', $d->contract_detail_id)->where('ca.is_deleted', 0)->get();



                $html .= '<tr><td class="text-center">' . ($key + 1) . '</td>

                                         

                                                     <td class="px-0 pb-0">

                                                        <p class="mb-3 px-2">' . $d->pn_no . ' </p>';

                foreach ($asset as $a) {



                    $html .= '<div  class="font-w600 t  border-top " style="font-weight: bold; ">

                    <p class="px-2 mb-0" style="text-transform:uppercase;">' . $a->asset_name . ' ' . ($a->sn != '' ? '[' . $a->sn . ']' : '') . '</p></div>';
                }





                $html .= '</td><td class="text-center"><p class="mb-0  ">' . $d->qty . '</p></td>

                                                <td   class="text-right"><p class="mb-0 ">$' . number_format($d->msrp, 2) . '</p> </td>

                                                <td class="text- ">' . $d->detail_comments . '</td>



                                              

                                            </tr>';
            }

            $html .= '</tbody></table>';
        }

        echo $html;
    }



    public function ShowContracts(Request $request)

    {

        $qry = DB::table('contracts as a')->select('a.*', 'c.client_display_name', 'c.client_address', 's.site_name', 'd.distributor_name', 'v.vendor_name', 'd.distributor_image', 'c.logo', 'v.vendor_image', 's.address', 's.city', 's.country', 's.phone', 's.zip_code', 's.province', 'usr.firstname as created_firstname', 'usr.lastname as created_lastname', 'upd.firstname as updated_firstname', 'upd.lastname as updated_lastname', 'a.ended_reason', 'a.ended_on', 'ued.firstname as ended_firstname', 'ued.lastname as ended_lastname', 'ued.email as ended_email')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('distributors as d', 'd.id', '=', 'a.distributor_id')->leftjoin('vendors as v', 'v.id', '=', 'a.vendor_id')->leftjoin('users as usr', 'usr.id', '=', 'a.created_by')->leftjoin('users as upd', 'upd.id', '=', 'a.updated_by')->leftjoin('users as ued', 'ued.id', '=', 'a.ended_by')->where('a.id', $request->id)->first();

        return response()->json($qry);
    }



    public function getEmailContracts(Request $request)

    {

        $qry = DB::table('contract_emails')->where('contract_id', $request->id)->get();

        return response()->json($qry);
    }

    public function saveEmaolContracts(Request $request)

    {

        $qry = DB::table('contract_emails')->insert([

            'renewal_email' => $request->email_address,

            'contract_id' => $request->contract_id

        ]);

        return redirect()->back()->with('success', 'Email Added Successfully');
    }





    public function getAttachmentContracts(Request $request)

    {

        $qry = DB::table('contract_attachments as c')

            ->select('c.*', 'u.user_image')

            ->join('users as u', function ($j) {

                $j->on('u.id', '=', 'c.added_by');
            })

            ->where('c.contract_id', $request->id)->get();

        return response()->json($qry);
    }



    public function getCommentsContracts(Request $request)

    {

        $qry = DB::table('contract_comments as c')

            ->select('c.*', 'u.user_image')

            ->join('users as u', function ($j) {

                $j->on('u.id', '=', 'c.added_by');
            })

            ->where('c.contract_id', $request->id)->get();

        return response()->json($qry);
    }



    public function getContractDetails(Request $request)
    {
        $id = $request->id;
        $qry = DB::select("SELECT *,(select GROUP_CONCAT(hostname) from contract_assets  where contract_detail_id=contract_details.contract_detail_id and is_deleted=0) as asset,(select GROUP_CONCAT(a.hostname) from contract_assets as ca left join assets as a on a.id=ca.hostname     where contract_detail_id=contract_details.contract_detail_id and ca.is_deleted=0) as hostname FROM contract_details where contract_id='$id' and is_deleted=0");
        return response()->json($qry);
    }







    public function getVendorOfContract(Request $request)

    {

        $client_id = $request->client_id;

        $site_id = $request->site_id;



        if ($site_id != '' && @$site_id[0] != '') {



            $qry = DB::Table('vendors as v')->select('v.*')->join('contracts as c', 'c.vendor_id', '=', 'v.id')->where('c.client_id', $client_id)->whereIn('c.site_id', $site_id)->groupBy('c.vendor_id')->orderby('v.vendor_name', 'asc')->get();
        } else {

            $qry = DB::Table('vendors as v')->select('v.*')->join('contracts as c', 'c.vendor_id', '=', 'v.id')->where('client_id', $client_id)->groupBy('c.vendor_id')->orderby('v.vendor_name', 'asc')->get();
        }

        return response()->json($qry);
    }



    public function getVendorOfSSL(Request $request)

    {

        $client_id = $request->client_id;

        $site_id = $request->site_id;



        if ($site_id != '' && @$site_id[0] != '') {



            $qry = DB::Table('vendors as v')->select('v.*')->join('ssl_certificate as c', 'c.cert_issuer', '=', 'v.id')->where('c.client_id', $client_id)->whereIn('c.site_id', $site_id)->groupBy('c.cert_issuer')->orderby('v.vendor_name', 'asc')->get();
        } else {

            $qry = DB::Table('vendors as v')->select('v.*')->join('ssl_certificate as c', 'c.cert_issuer', '=', 'v.id')->where('client_id', $client_id)->groupBy('c.cert_issuer')->orderby('v.vendor_name', 'asc')->get();
        }

        return response()->json($qry);
    }







    public function getDistributorOfContract(Request $request)

    {

        $client_id = $request->client_id;

        $site_id = $request->site_id;

        $vendor_id = $request->vendor_id;

        if ($vendor_id != ''  && @$vendor_id[0] != '') {



            $qry = DB::Table('distributors as v')->select('v.*')->join('contracts as c', 'c.vendor_id', '=', 'v.id')->where('c.client_id', $client_id)->whereIn('c.site_id', $site_id)->whereIn('c.vendor_id', $vendor_id)->groupBy('c.distributor_id')->orderby('v.distributor_name', 'asc')->get();
        } else if ($site_id != '' && @$site_id[0] != '') {



            $qry = DB::Table('distributors as v')->select('v.*')->join('contracts as c', 'c.distributor_id', '=', 'v.id')->where('c.client_id', $client_id)->whereIn('c.site_id', $site_id)->groupBy('c.distributor_id')->orderby('v.distributor_name', 'asc')->get();
        } else {

            $qry = DB::Table('distributors as v')->select('v.*')->join('contracts as c', 'c.distributor_id', '=', 'v.id')->where('client_id', $client_id)->groupBy('c.distributor_id')->orderby('v.distributor_name', 'asc')->get();
        }

        return response()->json($qry);
    }



    public function GenerateContractSharableLink(Request $request)

    {



        $hash = uniqid() . Hash::make(time() . rand(1, 100000000000000));

        DB::table('contract_sharable_links')->insert([

            'contract_id' => $request->id,

            'hash' => $hash,

            'expiry_date' => $request->expiry_date

        ]);



        return response()->json($hash);
    }



    public function RemoveActiveContractLinks(Request $request)

    {

        DB::table('contract_sharable_links')->where('contract_id', $request->id)->delete();

        return redirect()->back()->with('success', 'Active Links Removed Successfully');
    }
    public function ShowContract(Request $request)

    {











        $qry = DB::table('Contract as a')->select('a.*', 's.site_name', 'd.domain_name', 'c.client_display_name', 'o.operating_system_name', 'm.vendor_name')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->where('a.id', $request->id)->first();

        return response()->json($qry);
    }









    public function ExportExcelContract(Request $request)

    {



        return Excel::download(new ExportContract($request), 'Contract.xlsx');
    }

    public function ExportExpiringExcelContract(Request $request)

    {



        return Excel::download(new ExportExpiringContract($request), 'ExpiringContract.xlsx');
    }















    //      public function InsertContract(Request $request){

    //         // dd($request->all())

    //                       $data=array(

    //                                 'contract_status'=>'Active',



    //                            'client_id'=>$request->client_id,

    //                                 'site_id'=>$request->site_id,

    //                                 'contract_notification'=>$request->contract_notification,

    //                                 'estimate_no'=>$request->estimate_no,

    //                                 'sales_order_no'=>$request->sales_order_no,

    //                                 'invoice_no'=>$request->invoice_no,

    //                                 'contract_description'=>$request->contract_description,

    //                                 'invoice_date'=>date('Y-m-d',strtotime($request->invoice_date)),

    //                                 'registered_email'=>$request->registered_email,

    //                                 'po_no'=>$request->po_no,

    //                                 'po_date'=>date('Y-m-d',strtotime($request->po_date)),



    //                                 'distributor_id'=>$request->distributor_id,

    //                                 'reference_no'=>$request->reference_no,

    //                                 'distrubutor_sales_order_no'=>$request->distrubutor_sales_order_no,

    //                                 'vendor_id'=>$request->vendor_id,

    //                                 'contract_type'=>$request->contract_type,



    //                                 'contract_no'=>$request->contract_no,

    //                                 'contract_start_date'=>date('Y-m-d',strtotime($request->contract_start_date)),

    //                                 'contract_end_date'=>date('Y-m-d',strtotime($request->contract_end_date)) ,

    //                                 'created_by'=>Auth::id(),





    //                         );



    //                      DB::Table('contracts')->insert($data);

    //                     $id=DB::getPdo()->lastInsertId();



    //                      $attachment_array= $request->attachmentArray;

    //                         if(isset($request->attachmentArray)){

    //                         foreach($attachment_array as $a){

    //                             $a=json_decode($a);



    //                                    copy( public_path('temp_uploads/'.$a->attachment), public_path('contract_attachment/'.$a->attachment) );

    //                                              DB::table('contract_attachments')->insert([

    //                                                  'contract_id'=>$id,

    //                                                  'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),

    //                                                  'attachment'=>$a->attachment,

    //                                                  'name'=>$a->name,

    //                                                  'added_by'=>Auth::id(),

    //                                              ]);

    //                         }   

    //                     }



    //                          $emailArray= $request->emailArray;

    //                               if(isset($request->emailArray)){

    //                         foreach($emailArray as $a){

    //                             $a=json_decode($a);





    //                                              DB::table('contract_emails')->insert([

    //                                                  'contract_id'=>$id,



    //                                                  'renewal_email'=>$a->email,



    //                                                  'added_by'=>Auth::id(),

    //                                              ]);

    //                         }   

    //                         }



    //                                 $commentArray= $request->commentArray;

    //                          if(isset($request->commentArray)){

    //                         foreach($commentArray as $a){

    //                             $a=json_decode($a);





    //                                              DB::table('contract_comments')->insert([

    //                                                  'contract_id'=>$id,

    //                                                  'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),

    //                                                  'comment'=>$a->comment,

    //                                                  'name'=>$a->name,

    //                                                  'added_by'=>Auth::id(),

    //                                              ]);

    //                         }   

    //                         }



    //             $contractDetailsArray= $request->contractDetailsArray;

    //                          if(isset($request->contractDetailsArray)){

    //                         foreach($contractDetailsArray as $a){

    //                             $a=json_decode($a);





    //                                              DB::table('contract_details')->insert([

    //                                                  'contract_id'=>$id,

    //                                                     'pn_no'=>$a->pn_no,

    //                                                     'qty'=>$a->qty,

    //                                                     'msrp'=>$a->msrp,

    //                                                     'contract_type_line'=>$a->contract_type_line,

    //                                                     'detail_comments'=>$a->asset_description,

    //                                                  'added_by'=>Auth::id(),

    //                                              ]);

    //                                              $detail_id=DB::getPdo()->lastInsertId();

    //                                                 $asset_array=$a->hostname_modal;

    //                                                 foreach($asset_array as $b){

    //    $assetQry=DB::table('assets')->where('id',$b)->first();

    //                                                 iF($assetQry!=''){

    //                                             DB::table('contract_assets')->insert([

    //                                                     'contract_id'=>$id,

    //                                                     'contract_detail_id'=>$detail_id,

    //                                                     'asset_type'=>@$assetQry->asset_type,

    //                                                     'hostname'=>$b,



    //                                             ]);



    //                                             DB::table('assets')->where('id',$b)->update(['warranty_status'=>'Active','warranty_end_date'=>$request->contract_end_date,'SupportStatus'=>'Supported']);

    //                                                         }

    //                                                     }

    //                         }   

    //                     }







    // DB::table('contract_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Contract added','contract_id'=>$id]);



    // if($request->contract_notification==1){



    //                  $settings=DB::Table('notification_settings')->first();



    //     $client=DB::Table('clients')->where('id',$request->client_id)->first();

    //        $vendor=DB::Table('vendors')->where('id',$request->vendor_id)->first();

    // $recipients=[$client->email_address];

    //    if(isset($request->emailArray)){

    //                         foreach($emailArray as $a){

    //                                $a=json_decode($a);





    //                                 $recipients[]=$a->email;

    //                         }

    //                     }



    //     //                  $data = array( 'emails' => $recipients, 'contract_no' =>$request->contract_no,'end_date'=>$request->contract_end_date , 'subject' => 'New Contract '.$request->contract_no.' Created','vendor_name'=>$vendor->vendor_name,'contract_id'=>$id,'from_name'=>$settings->from_name);

    //     //                 //  dd($data);

    //     //                   Mail::send('emails.renewal_email', ['data' => $data], function ($message) use ($data) {

    //     //         $message->to($data['emails']);

    //     //         $message->subject($data['subject']);

    //     // $message->from('support@consultationamaltitek.com',$data['from_name']);

    //     //     });

    //     //             DB::Table('notifications')->insert(['type'=>'Contract','from_email'=>$settings->from_name,'to_email'=>implode(',',$data['emails']),'subject'=>$data['subject']]);

    // $data = array(

    //     'emails' => $recipients, 

    //     'contract_description' => $request->contract_description, 

    //     'contract_no' => $request->contract_no,

    //     'start_date' => date('Y-m-d',strtotime($request->contract_start_date)), 

    //     'end_date' => $request->contract_end_date, 

    //     'subject' => 'New Contract ' . $request->contract_no .' Created',

    //     'vendor_name' => $vendor->vendor_name,

    //     'contract_id' => $id,

    //     'from_name' => $settings->from_name

    // );

    // Mail::send('emails.client_notification_email', ['data' => $data], function ($message) use ($data) {

    //     $message->to($data['emails']);

    //     $message->subject($data['subject']);

    //     $message->from('support@consultationamaltitek.com', $data['from_name']);

    // });

    // DB::Table('notifications')->insert([

    //     'type' => 'Contract',

    //     'from_email' => 'support@consultationamaltitek.com',

    //     'to_email' => implode(',', $data['emails']),

    //     'subject' => $data['subject']

    // ]);



    // Mail::send('emails.settings_notification_email', ['data' => $data], function ($message) use ($data, $settings) {

    //     $message->to($settings->notification_email);

    //     $message->subject($data['subject']);

    //     $message->from('support@consultationamaltitek.com', $data['from_name']);

    // });

    // DB::Table('notifications')->insert([

    //     'type' => 'Contract',

    //     'from_email' => 'support@consultationamaltitek.com',

    //     'to_email' => $settings->notification_email,

    //     'subject' => $data['subject']

    // ]);



    // }

    //                 return response()->json('success');





    //     }





    public function InsertContractss(Request $request)
    {

        $data = array(

            'contract_status' => 'Active',

            'client_id' => $request->client_id,

            'managed_by' => $request->managed,

            'currency' => $request->currency,

            'site_id' => $request->site_id,

            'contract_notification' => $request->contract_notification,

            'estimate_no' => $request->estimate_no,

            'sales_order_no' => $request->sales_order_no,

            'invoice_no' => $request->invoice_no,

            'contract_description' => $request->contract_description,

            'invoice_date' => date('Y-m-d', strtotime($request->invoice_date)),

            'registered_email' => $request->registered_email,

            'po_no' => $request->po_no,

            'po_date' => date('Y-m-d', strtotime($request->po_date)),

            'distributor_id' => $request->distributor_id,

            'reference_no' => $request->reference_no,

            'distrubutor_sales_order_no' => $request->distrubutor_sales_order_no,

            'vendor_id' => $request->vendor_id,

            'contract_type' => $request->contract_type,

            'contract_no' => $request->contract_no,

            'total_amount' => $request->total_amount,

            'affliates' => $request->selected_clients ? implode(',', $request->selected_clients) : '',

            'contract_start_date' => date('Y-m-d', strtotime($request->contract_start_date)),

            'contract_end_date' => date('Y-m-d', strtotime($request->contract_end_date)),

            'created_by' => Auth::id()

        );



        DB::Table('contracts')->insert($data);

        $id = DB::getPdo()->lastInsertId();



        $attachment_array = $request->attachmentArray;

        if (isset($request->attachmentArray)) {

            foreach ($attachment_array as $a) {

                $a = json_decode($a);



                // copy(public_path('temp_uploads/' . $a->attachment), public_path('contract_attachment/' . $a->attachment));

                DB::table('contract_attachments')->insert([

                    'contract_id' => $id,

                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),

                    'attachment' => $a->attachment,

                    'name' => $a->name,

                    'added_by' => Auth::id(),

                ]);
            }
        }



        $emailArray = $request->emailArray;

        if (isset($request->emailArray)) {

            foreach ($emailArray as $a) {

                $a = json_decode($a);





                DB::table('contract_emails')->insert([

                    'contract_id' => $id,



                    'renewal_email' => $a->email,



                    'added_by' => Auth::id(),

                ]);
            }
        }



        $commentArray = $request->commentArray;

        if (isset($request->commentArray)) {

            foreach ($commentArray as $a) {

                $a = json_decode($a);





                DB::table('contract_comments')->insert([

                    'contract_id' => $id,

                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),

                    'comment' => $a->comment,

                    'name' => $a->name,

                    'added_by' => Auth::id(),

                ]);
            }
        }



        $contractDetailsArray = $request->contractDetailsArray;

        if (isset($request->contractDetailsArray)) {

            foreach ($contractDetailsArray as $a) {

                $a = json_decode($a);





                DB::table('contract_details')->insert([

                    'contract_id' => $id,

                    'pn_no' => $a->pn_no,

                    'qty' => $a->qty,

                    'msrp' => $a->msrp,

                    'contract_type_line' => $a->contract_type_line,

                    'detail_comments' => $a->asset_description,

                    'added_by' => Auth::id(),

                ]);

                $detail_id = DB::getPdo()->lastInsertId();

                $asset_array = $a->hostname_modal;

                foreach ($asset_array as $b) {

                    $assetQry = DB::table('assets')->where('id', $b)->first();

                    if ($assetQry != '') {

                        DB::table('contract_assets')->insert([

                            'contract_id' => $id,

                            'contract_detail_id' => $detail_id,

                            'asset_type' => @$assetQry->asset_type,

                            'hostname' => $b,



                        ]);



                        DB::table('assets')->where('id', $b)->update(['warranty_status' => 'Active', 'warranty_end_date' => $request->contract_end_date, 'SupportStatus' => 'Supported']);
                    }
                }
            }
        }







        DB::table('contract_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Contract added', 'contract_id' => $id]);



        if ($request->contract_notification == 1) {



            $settings = DB::Table('notification_settings')->first();

            $client = DB::Table('clients')->where('id', $request->client_id)->first();

            $vendor = DB::Table('vendors')->where('id', $request->vendor_id)->first();

            $recipients = [$settings->asset_emails];

            // if(isset($request->emailArray)){

            // foreach($emailArray as $a){

            //   $a=json_decode($a);





            //     $recipients[]=$a->email;

            // }

            // }





            $data = array('emails' => $recipients, 'contract_no' => $request->contract_no, 'end_date' => $request->contract_end_date, 'start_date' => $request->contract_start_date, 'subject' => 'New Contract ' . $request->contract_no . ' Created', 'vendor_name' => $vendor->vendor_name, 'contract_id' => $id, 'from_name' => $settings->from_name, 'contract_description' => $request->contract_description);

            try {

                //code...

                Mail::send('emails.renewal_email', ['data' => $data], function ($message) use ($data) {

                    $message->to($data['emails']);

                    $message->subject($data['subject']);

                    $message->from('support@consultationamaltitek.com', $data['from_name']);
                });
            } catch (\Throwable $th) {

                //throw $th;

            }

            DB::Table('notifications')->insert(['type' => 'Contract', 'from_email' => $settings->from_name, 'to_email' => implode(',', $data['emails']), 'subject' => $data['subject']]);
        }





        return response()->json('success');
    }













    public function RenewContractUpdate(Request $request)

    {

        $data = array(

            'contract_status' => 'Active',

            'client_id' => $request->client_id,

            'site_id' => $request->site_id,

            'managed_by' => $request->managed,

            'currency' => $request->currency,

            'contract_notification' => $request->contract_notification,

            'estimate_no' => $request->estimate_no,

            'sales_order_no' => $request->sales_order_no,

            'invoice_no' => $request->invoice_no,

            'contract_description' => $request->contract_description,

            'invoice_date' => date('Y-m-d', strtotime($request->invoice_date)),

            'registered_email' => $request->registered_email,

            'po_no' => $request->po_no,

            'po_date' => date('Y-m-d', strtotime($request->po_date)),

            'distributor_id' => $request->distributor_id,

            'reference_no' => $request->reference_no,

            'distrubutor_sales_order_no' => $request->distrubutor_sales_order_no,

            'vendor_id' => $request->vendor_id,

            'contract_type' => $request->contract_type,

            'contract_no' => $request->contract_no,

            'affliates' => $request->selected_clients ? implode(',', $request->selected_clients) : '',
            'total_amount' => $request->total_amount,



            'contract_start_date' => date('Y-m-d', strtotime($request->contract_start_date)),

            'contract_end_date' => date('Y-m-d', strtotime($request->contract_end_date)),

            'updated_by' => Auth::id(),

            'updated_at' => date('Y-m-d H:i:s'),

        );







        DB::Table('contracts')->insert($data);

        $id = DB::getPdo()->lastInsertId();



        $contract_id = $request->id;



        $qry = DB::Table('contracts')->whereNotNull('client_id')->where('id', $contract_id)->first();





        $asset = DB::table('contract_assets')->where('is_deleted', 0)->where('contract_id', $contract_id)->get();

        foreach ($asset as $a) {



            DB::table('assets')->where('id', $a->hostname)->update(['warranty_status' => 'Inactive', 'warranty_end_date' => 'No contract Found', 'SupportStatus' => 'Unassigned']);
        }



        DB::table('contract_assets')->where('contract_id', $contract_id)->where('is_deleted', 0)->update(['status' => 'Inactive']);



        DB::table('contracts')->where('id', $contract_id)->update(['contract_status' => 'Inactive', 'renewed_on' => date('Y-m-d H:i:s'), 'renewed_by' => Auth::id()]);

 






        $attachment_array = $request->attachmentArray;

        if (isset($request->attachmentArray)) {

            foreach ($attachment_array as $a) {

                $a = json_decode($a);



                // copy(public_path('temp_uploads/' . $a->attachment), public_path('contract_attachment/' . $a->attachment));

                DB::table('contract_attachments')->insert([

                    'contract_id' => $id,

                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),

                    'attachment' => $a->attachment,

                    'name' => $a->name,

                    'added_by' => Auth::id(),

                ]);
            }
        }



        $emailArray = $request->emailArray;

        if (isset($request->emailArray)) {

            foreach ($emailArray as $a) {

                $a = json_decode($a);





                DB::table('contract_emails')->insert([

                    'contract_id' => $id,



                    'renewal_email' => $a->email,



                    'added_by' => Auth::id(),

                ]);
            }
        }



        $commentArray = $request->commentArray;

        if (isset($request->commentArray)) {

            foreach ($commentArray as $a) {

                $a = json_decode($a);





                DB::table('contract_comments')->insert([

                    'contract_id' => $id,

                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),

                    'comment' => $a->comment,

                    'name' => $a->name,

                    'added_by' => Auth::id(),

                ]);
            }
        }



        $contractDetailsArray = $request->contractDetailsArray;

        if (isset($request->contractDetailsArray)) {

            foreach ($contractDetailsArray as $a) {

                $a = json_decode($a);





                DB::table('contract_details')->insert([

                    'contract_id' => $id,

                    'pn_no' => $a->pn_no,

                    'qty' => $a->qty,

                    'contract_type_line' => $a->contract_type_line,

                    'msrp' => $a->msrp,

                    'detail_comments' => $a->asset_description,

                    'added_by' => Auth::id(),

                ]);

                $detail_id = DB::getPdo()->lastInsertId();

                $asset_array = $a->hostname_modal;

                foreach ($asset_array as $b) {

                    $assetQry = DB::table('assets')->where('id', $b)->first();

                    if ($assetQry != '') {

                        DB::table('contract_assets')->insert([

                            'contract_id' => $id,

                            'contract_detail_id' => $detail_id,

                            'asset_type' => @$assetQry->asset_type,

                            'hostname' => $b,



                        ]);













                        DB::table('assets')->where('id', $b)->update(['warranty_status' => 'Active', 'warranty_end_date' => $request->contract_end_date, 'SupportStatus' => 'Supported']);
                    }
                }
            }
        }





        DB::table('contract_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => @Auth::user()->firstname . ' ' . @Auth::user()->lastname, 'contract_id' => $request->id, 'comment' => 'Contract successfully renewed.']);



        DB::table('contract_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Contract successfully renewed.', 'contract_id' => $request->id]);

        if ($request->contract_notification == 1) {



            $settings = DB::Table('notification_settings')->first();

            $client = DB::Table('clients')->where('id', $request->client_id)->first();

            $vendor = DB::Table('vendors')->where('id', $request->vendor_id)->first();

            $recipients = [$settings->asset_emails];





            $data = array('emails' => $recipients, 'contract_no' => $request->contract_no, 'end_date' => $request->contract_end_date, 'start_date' => $request->contract_start_date, 'subject' => 'Renew Contract ' . $request->contract_no . ' Created', 'vendor_name' => $vendor->vendor_name, 'contract_id' => $id, 'from_name' => $settings->from_name, 'contract_description' => $request->contract_description);

            try {

                //code...

                Mail::send('emails.renewal_email', ['data' => $data], function ($message) use ($data) {

                    $message->to($data['emails']);

                    $message->subject($data['subject']);

                    $message->from('support@consultationamaltitek.com', $data['from_name']);
                });
            } catch (\Throwable $th) {

                //throw $th;

            }

            DB::Table('notifications')->insert(['type' => 'Contract', 'from_email' => $settings->from_name, 'to_email' => implode(',', $data['emails']), 'subject' => $data['subject']]);
        }

        return response()->json('success');
    }









    public function GetContractNotifications(Request $request)

    {

        $clients = DB::table('notification_settings')->first();



        $email_qry = DB::table('client_emails')->where('client_id', $request->id)->get();

        $email_qry[] = ['renewal_email' => $clients->contract_email];



        return response()->json($email_qry);
    }

    public function GetSSLNotifications(Request $request)

    {

        $clients = DB::table('notification_settings')->first();



        $email_qry = DB::table('client_ssl_emails')->where('client_id', $request->id)->get();

        $email_qry[] = ['renewal_email' => $clients->certificate_email];



        return response()->json($email_qry);
    }

    public function InsertCloneContract(Request $request)
    {

        $contract_id = DB::table('contracts')->insertGetId([
            'contract_status' => $request->contract_status,
            'client_id' => $request->client_id,
            'site_id' => $request->site_id,
            'vendor_id' => $request->vendor_id,
            'distributor_id' => $request->distributor_id,
            'currency' => $request->currency,
            'total_amount' => $request->total_amount,
            'contract_no' => $request->contract_no,
            'contract_type' => $request->contract_type,
            'contract_start_date' => $request->contract_start_date ? date('Y-m-d', strtotime($request->contract_start_date)) : null,
            'contract_end_date' => $request->contract_end_date ? date('Y-m-d', strtotime($request->contract_end_date)) : null,
            'contract_description' => $request->contract_description,
            'managed_by' => $request->managed_by,
            'registered_email' => $request->registered_email,
            'reference_no' => $request->reference_no,
            'distrubutor_sales_order_no' => $request->distrubutor_sales_order_no,
            'estimate_no' => $request->estimate_no,
            'sales_order_no' => $request->sales_order_no,
            'invoice_no' => $request->invoice_no,
            'invoice_date' => $request->invoice_date ? date('Y-m-d', strtotime($request->invoice_date)) : null,
            'po_no' => $request->po_no,
            'po_date' => $request->po_date ? date('Y-m-d', strtotime($request->po_date)) : null,
            'contract_notification' => $request->contract_notification,
            'affliates' => $request->affiliate_ids, // comma-separated
            'created_at' => now(),
            'created_by' => auth()->id(),
        ]);

        if (!empty($request->email_ids) && is_array($request->email_ids)) {
            foreach ($request->email_ids as $email) {
                if (!empty($email)) {
                    DB::table('contract_emails')->insert([
                        'contract_id' => $contract_id,
                        'renewal_email' => $email,
                    ]);
                }
            }
        }

        // Get contract info for status check
        $qry = DB::table('contracts')->where('id', $contract_id)->first();

        // Decode and insert new contract details + assets
        $distributor_array = json_decode($request->distributor_array, true);
        if (!empty($distributor_array) && is_array($distributor_array)) {
            foreach ($distributor_array as $item) {
                DB::table('contract_distribution')->insert([
                    'contract_id' => $contract_id,
                    'distributor' => $item['distributer'],
                    'reference_no' => $item['reference'],
                    'sales_order_no' => $item['salesorder'],
                    'added_by' => Auth::user()->id,
                ]);
            }
        }
        $purchasing_array = json_decode($request->purchasing_array, true);
        if (!empty($purchasing_array) && is_array($purchasing_array)) {
            foreach ($purchasing_array as $item) {
                DB::table('contract_purchasing')->insert([
                    'contract_id' => $contract_id,
                    'estimate_no' => $item['estimate_no'],
                    'sales_order_no' => $item['sales_order_no'],
                    'invoice_no' => $item['invoice_no'],
                    'invoice_date' => date('Y-m-d', strtotime($item['invoice_date'])),
                    'po_no' => $item['po_no'],
                    'po_date' => date('Y-m-d', strtotime($item['po_date'])),
                    'added_by' => Auth::user()->id,
                ]);
            }
        }

        // Decode and insert new contract details + assets
        $lineItems = json_decode($request->lineItems, true);

        if (!is_array($lineItems)) {
            return response()->json(['message' => 'Invalid line items data'], 400);
        }

        foreach ($lineItems as $item) {

            $typeMap = [
                'SFT' => 'Software Support',
                'HDW' => 'Hardware Support',
                'SUB' => 'Subscription',
                'MSP' => 'Other',
            ];
            if ($item['type'] != "") {

                $contract_type_line = $typeMap[$item['type']] ?? $item['type'];

                // Insert each contract detail
                $contractDetailId = DB::table('contract_details')->insertGetId([
                    'contract_id' => $contract_id,
                    'qty' => $item['qty'] ?? 0,
                    'pn_no' => $item['pn_no'] ?? '',
                    'contract_type_line' => $contract_type_line,
                    'detail_comments' => $item['desc'] ?? '',
                    'msrp' => $item['cost'] ?? 0,
                    'is_deleted' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'added_by' => auth()->id(),
                ]);

                // Insert contract assets for this detail
                if (!empty($item['assets'])) {
                    foreach ($item['assets'] as $asset) {
                        DB::table('contract_assets')->insert([
                            'contract_id' => $contract_id,
                            'contract_detail_id' => $contractDetailId,
                            'hostname' => $asset['id'],
                            'is_deleted' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Update asset warranty/support based on contract status
                        DB::table('assets')->where('id', $asset['id'])->update([
                            'warranty_status' => $qry->contract_status === 'Active' ? 'Active' : 'Inactive',
                            'warranty_end_date' => $request->contract_end_date,
                            'SupportStatus' => $qry->contract_status === 'Active' ? 'Supported' : 'Expired',
                        ]);
                    }
                }
            }
        }

        DB::table('contract_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Contract Clonned', 'contract_id' => $contract_id]);

        return response()->json(['message' => 'Contract clonned successfully!']);
    }
    public function InsertContract(Request $request)
    {

        $contract_id = DB::table('contracts')->insertGetId([
            'contract_status' => 'Active',
            'client_id' => $request->client_id,
            'site_id' => $request->site_id,
            'vendor_id' => $request->vendor_id,
            'distributor_id' => $request->distributor_id,
            'currency' => $request->currency,
            'total_amount' => $request->total_amount,
            'contract_no' => $request->contract_no,
            'contract_type' => $request->contract_type,
            'contract_start_date' => $request->contract_start_date ? date('Y-m-d', strtotime($request->contract_start_date)) : null,
            'contract_end_date' => $request->contract_end_date ? date('Y-m-d', strtotime($request->contract_end_date)) : null,
            'contract_description' => $request->contract_description,
            'managed_by' => $request->managed_by,
            'registered_email' => $request->registered_email,
            'reference_no' => $request->reference_no,
            'distrubutor_sales_order_no' => $request->distrubutor_sales_order_no,
            'estimate_no' => $request->estimate_no,
            'sales_order_no' => $request->sales_order_no,
            'invoice_no' => $request->invoice_no,
            'invoice_date' => $request->invoice_date ? date('Y-m-d', strtotime($request->invoice_date)) : null,
            'po_no' => $request->po_no,
            'po_date' => $request->po_date ? date('Y-m-d', strtotime($request->po_date)) : null,
            'contract_notification' => $request->contract_notification,
            'affliates' => $request->affiliate_ids, // comma-separated
            'created_at' => now(),
            'created_by' => auth()->id(),
        ]);

        if (!empty($request->email_ids) && is_array($request->email_ids)) {
            foreach ($request->email_ids as $email) {
                if (!empty($email)) {
                    DB::table('contract_emails')->insert([
                        'contract_id' => $contract_id,
                        'renewal_email' => $email,
                    ]);
                }
            }
        }
        $attachment_array = json_decode($request->attachmentArray, true);
        if (!empty($attachment_array) && is_array($attachment_array)) {
            foreach ($attachment_array as $a) {
                copy(public_path('temp_uploads/' . $a['attachment']), public_path('network_attachment/' . $a['attachment']));
                DB::table('contract_attachments')->insert([
                    'contract_id' => $contract_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a['date'] . ' ' . $a['time'])),
                    'attachment' => $a['attachment'],
                    'name' => $a['name'],
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = json_decode($request->commentArray, true);
        if (!empty($commentArray) && is_array($commentArray)) {
            foreach ($commentArray as $a) {
                DB::table('contract_comments')->insert([
                    'contract_id' => $contract_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a['date'] . ' ' . $a['time'])),
                    'comment' => $a['comment'],
                    'name' => $a['name'],
                    'added_by' => Auth::id(),
                ]);
            }
        }

        // Get contract info for status check
        $qry = DB::table('contracts')->where('id', $contract_id)->first();

        // Decode and insert new contract details + assets
        $distributor_array = json_decode($request->distributor_array, true);
        if (!empty($distributor_array) && is_array($distributor_array)) {
            foreach ($distributor_array as $item) {
                DB::table('contract_distribution')->insert([
                    'contract_id' => $contract_id,
                    'distributor' => $item['distributer'],
                    'reference_no' => $item['reference'],
                    'sales_order_no' => $item['salesorder'],
                    'added_by' => Auth::user()->id,
                ]);
            }
        }
        $purchasing_array = json_decode($request->purchasing_array, true);
        if (!empty($purchasing_array) && is_array($purchasing_array)) {
            foreach ($purchasing_array as $item) {
                DB::table('contract_purchasing')->insert([
                    'contract_id' => $contract_id,
                    'estimate_no' => $item['estimate_no'],
                    'sales_order_no' => $item['sales_order_no'],
                    'invoice_no' => $item['invoice_no'],
                    'invoice_date' => date('Y-m-d', strtotime($item['invoice_date'])),
                    'po_no' => $item['po_no'],
                    'po_date' => date('Y-m-d', strtotime($item['po_date'])),
                    'added_by' => Auth::user()->id,
                ]);
            }
        }
        $lineItems = json_decode($request->lineItems, true);

        if (!is_array($lineItems)) {
            return response()->json(['message' => 'Invalid line items data'], 400);
        }
        // dd($lineItems);
        foreach ($lineItems as $item) {

            $typeMap = [
                'SFT' => 'Software Support',
                'HDW' => 'Hardware Support',
                'SUB' => 'Subscription',
                'MSP' => 'Other',
            ];
            if ($item['type'] != "") {
                $contract_type_line = $typeMap[$item['type']] ?? $item['type'];

                // Insert each contract detail
                $contractDetailId = DB::table('contract_details')->insertGetId([
                    'contract_id' => $contract_id,
                    'qty' => $item['qty'] ?? 0,
                    'pn_no' => $item['pn_no'] ?? '',
                    'contract_type_line' => $contract_type_line,
                    'detail_comments' => $item['desc'] ?? '',
                    'msrp' => $item['cost'] ?? 0,
                    'is_deleted' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'added_by' => auth()->id(),
                ]);

                // Insert contract assets for this detail
                if (!empty($item['assets'])) {
                    foreach ($item['assets'] as $asset) {
                        DB::table('contract_assets')->insert([
                            'contract_id' => $contract_id,
                            'contract_detail_id' => $contractDetailId,
                            'hostname' => $asset['id'],
                            'is_deleted' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Update asset warranty/support based on contract status
                        DB::table('assets')->where('id', $asset['id'])->update([
                            'warranty_status' => 'Active',
                            'warranty_end_date' => $request->contract_end_date,
                            'SupportStatus' => 'Supported',
                        ]);
                    }
                }
            }
        }

        DB::table('contract_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Contract Added', 'contract_id' => $contract_id]);

        return response()->json(['message' => 'Contract added successfully!']);
    }

    public function UpdateRenewContract(Request $request)
    {
        // $contract_id = $request->contract_id;

        DB::table('contracts')->where('id', $request->contract_id)->update([
            'contract_status' => 'Inactive',
         'renewed_on' => date('Y-m-d H:i:s'), 'renewed_by' => Auth::id()
        ]);

        $msg = "This contract was renewed by " . ucfirst(Auth::user()->firstname) . " " . ucfirst(Auth::user()->lastname) . " on " . date('d-M-Y g:i a', strtotime(now()));

        DB::table('pinned_messages')->insert([
            'message' => $msg,
            'linked_id' => $request->contract_id,
            'page' => 'contract',
            'added_by' => Auth::user()->id,
            'is_deleteable' => 0,
            'status' => 'renewed',
        ]);

        // ✅ 1. Update the main contract record
        $contract_id = DB::table('contracts')->insertGetId([
            'contract_status' => 'Active',
            'client_id' => $request->client_id,
            'site_id' => $request->site_id,
            'vendor_id' => $request->vendor_id,
            'distributor_id' => $request->distributor_id,
            'currency' => $request->currency,
            'total_amount' => $request->total_amount,
            'contract_no' => $request->contract_no,
            'contract_type' => $request->contract_type,
            'contract_start_date' => $request->contract_start_date ? date('Y-m-d', strtotime($request->contract_start_date)) : null,
            'contract_end_date' => $request->contract_end_date ? date('Y-m-d', strtotime($request->contract_end_date)) : null,
            'contract_description' => $request->contract_description,
            'managed_by' => $request->managed_by,
            'registered_email' => $request->registered_email,
            'reference_no' => $request->reference_no,
            'distrubutor_sales_order_no' => $request->distrubutor_sales_order_no,
            'estimate_no' => $request->estimate_no,
            'sales_order_no' => $request->sales_order_no,
            'invoice_no' => $request->invoice_no,
            'invoice_date' => $request->invoice_date ? date('Y-m-d', strtotime($request->invoice_date)) : null,
            'po_no' => $request->po_no,
            'po_date' => $request->po_date ? date('Y-m-d', strtotime($request->po_date)) : null,
            'contract_notification' => $request->contract_notification,
            'affliates' => $request->affiliate_ids, // comma-separated
            'created_at' => now(),
            'created_by' => auth()->id(),
        ]);

        $attachment_array = json_decode($request->attachmentArray, true);
        if (!empty($attachment_array) && is_array($attachment_array)) {
            foreach ($attachment_array as $a) {
                copy(public_path('temp_uploads/' . $a['attachment']), public_path('network_attachment/' . $a['attachment']));
                DB::table('contract_attachments')->insert([
                    'contract_id' => $contract_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a['date'] . ' ' . $a['time'])),
                    'attachment' => $a['attachment'],
                    'name' => $a['name'],
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = json_decode($request->commentArray, true);
        if (!empty($commentArray) && is_array($commentArray)) {
            foreach ($commentArray as $a) {
                DB::table('contract_comments')->insert([
                    'contract_id' => $contract_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a['date'] . ' ' . $a['time'])),
                    'comment' => $a['comment'],
                    'name' => $a['name'],
                    'added_by' => Auth::id(),
                ]);
            }
        }

        // ✅ 2. Refresh contract emails
        DB::table('contract_emails')->where('contract_id', $contract_id)->delete();

        if (!empty($request->email_ids) && is_array($request->email_ids)) {
            foreach ($request->email_ids as $email) {
                if (!empty($email)) {
                    DB::table('contract_emails')->insert([
                        'contract_id' => $contract_id,
                        'renewal_email' => $email,
                    ]);
                }
            }
        }

        // ✅ 3. Reset asset status for this contract
        $oldAssets = DB::table('contract_assets')
            ->where('is_deleted', 0)
            ->where('contract_id', $contract_id)
            ->get();

        foreach ($oldAssets as $a) {
            DB::table('assets')
                ->where('id', $a->hostname)
                ->update([
                    'warranty_status' => 'Inactive',
                    'warranty_end_date' => 'No contract Found',
                    'SupportStatus' => 'Unassigned'
                ]);
        }

        // ✅ 4. Soft delete old contract details & assets
        DB::table('contract_details')
            ->where('contract_id', $contract_id)
            ->where('is_deleted', 0)
            ->update([
                'is_deleted' => 1,
                'deleted_at' => now(),
            ]);

        DB::table('contract_assets')
            ->where('contract_id', $contract_id)
            ->where('is_deleted', 0)
            ->update([
                'is_deleted' => 1,
                'deleted_at' => now(),
            ]);
        DB::table('contract_distribution')
            ->where('contract_id', $contract_id)
            ->delete();
        DB::table('contract_purchasing')
            ->where('contract_id', $contract_id)
            ->delete();

        // ✅ 5. Get contract info for status check
        $qry = DB::table('contracts')->where('id', $contract_id)->first();

        // Decode and insert new contract details + assets
        $distributor_array = json_decode($request->distributor_array, true);
        if (!empty($distributor_array) && is_array($distributor_array)) {
            foreach ($distributor_array as $item) {
                DB::table('contract_distribution')->insert([
                    'contract_id' => $contract_id,
                    'distributor' => $item['distributer'],
                    'reference_no' => $item['reference'],
                    'sales_order_no' => $item['salesorder'],
                    'added_by' => Auth::user()->id,
                ]);
            }
        }
        $purchasing_array = json_decode($request->purchasing_array, true);
        if (!empty($purchasing_array) && is_array($purchasing_array)) {
            foreach ($purchasing_array as $item) {
                DB::table('contract_purchasing')->insert([
                    'contract_id' => $contract_id,
                    'estimate_no' => $item['estimate_no'],
                    'sales_order_no' => $item['sales_order_no'],
                    'invoice_no' => $item['invoice_no'],
                    'invoice_date' => date('Y-m-d', strtotime($item['invoice_date'])),
                    'po_no' => $item['po_no'],
                    'po_date' => date('Y-m-d', strtotime($item['po_date'])),
                    'added_by' => Auth::user()->id,
                ]);
            }
        }

        // ✅ 6. Decode and insert new contract details + assets
        $lineItems = json_decode($request->lineItems, true);

        if (!is_array($lineItems)) {
            return response()->json(['message' => 'Invalid line items data'], 400);
        }

        foreach ($lineItems as $item) {

            $typeMap = [
                'SFT' => 'Software Support',
                'HDW' => 'Hardware Support',
                'SUB' => 'Subscription',
                'MSP' => 'Other',
            ];
            if ($item['type'] != "") {

                $contract_type_line = $typeMap[$item['type']] ?? $item['type'];

                // Insert each contract detail
                $contractDetailId = DB::table('contract_details')->insertGetId([
                    'contract_id' => $contract_id,
                    'qty' => $item['qty'] ?? 0,
                    'pn_no' => $item['pn_no'] ?? '',
                    'contract_type_line' => $contract_type_line,
                    'detail_comments' => $item['desc'] ?? '',
                    'msrp' => $item['cost'] ?? 0,
                    'is_deleted' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'added_by' => auth()->id(),
                ]);

                // Insert contract assets for this detail
                if (!empty($item['assets'])) {
                    foreach ($item['assets'] as $asset) {
                        DB::table('contract_assets')->insert([
                            'contract_id' => $contract_id,
                            'contract_detail_id' => $contractDetailId,
                            'hostname' => $asset['id'],
                            'is_deleted' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Update asset warranty/support based on contract status
                        DB::table('assets')->where('id', $asset['id'])->update([
                            'warranty_status' => $qry->contract_status === 'Active' ? 'Active' : 'Inactive',
                            'warranty_end_date' => $request->contract_end_date,
                            'SupportStatus' => $qry->contract_status === 'Active' ? 'Supported' : 'Expired',
                        ]);
                    }
                }
            }
        }

        DB::table('contract_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Contract renewed', 'contract_id' => $contract_id]);

        return response()->json(['message' => 'Contract renewed successfully!']);
    }

    public function UpdateContract(Request $request)
    {
        $contract_id = $request->contract_id;

        // ✅ 1. Update the main contract record
        DB::table('contracts')
            ->where('id', $contract_id)
            ->update([
                'client_id' => $request->client_id,
                'site_id' => $request->site_id,
                'vendor_id' => $request->vendor_id,
                'distributor_id' => $request->distributor_id,
                'currency' => $request->currency,
                'total_amount' => $request->total_amount,
                'contract_no' => $request->contract_no,
                'contract_type' => $request->contract_type,
                'contract_start_date' => $request->contract_start_date ? date('Y-m-d', strtotime($request->contract_start_date)) : null,
                'contract_end_date' => $request->contract_end_date ? date('Y-m-d', strtotime($request->contract_end_date)) : null,
                'contract_description' => $request->contract_description,
                'managed_by' => $request->managed_by,
                'registered_email' => $request->registered_email,
                'reference_no' => $request->reference_no,
                'distrubutor_sales_order_no' => $request->distrubutor_sales_order_no,
                'estimate_no' => $request->estimate_no,
                'sales_order_no' => $request->sales_order_no,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date ? date('Y-m-d', strtotime($request->invoice_date)) : null,
                'po_no' => $request->po_no,
                'po_date' => $request->po_date ? date('Y-m-d', strtotime($request->po_date)) : null,
                'contract_notification' => $request->contract_notification,
                'affliates' => $request->affiliate_ids, // comma-separated
                'updated_at' => now(),
                'updated_by' => auth()->id(),
            ]);

        // ✅ 2. Refresh contract emails
        DB::table('contract_emails')->where('contract_id', $contract_id)->delete();

        if (!empty($request->email_ids) && is_array($request->email_ids)) {
            foreach ($request->email_ids as $email) {
                if (!empty($email)) {
                    DB::table('contract_emails')->insert([
                        'contract_id' => $contract_id,
                        'renewal_email' => $email,
                    ]);
                }
            }
        }

        // ✅ 3. Reset asset status for this contract
        $oldAssets = DB::table('contract_assets')
            ->where('is_deleted', 0)
            ->where('contract_id', $contract_id)
            ->get();

        foreach ($oldAssets as $a) {
            DB::table('assets')
                ->where('id', $a->hostname)
                ->update([
                    'warranty_status' => 'Inactive',
                    'warranty_end_date' => 'No contract Found',
                    'SupportStatus' => 'Unassigned'
                ]);
        }

        // ✅ 4. Soft delete old contract details & assets
        DB::table('contract_details')
            ->where('contract_id', $contract_id)
            ->where('is_deleted', 0)
            ->update([
                'is_deleted' => 1,
                'deleted_at' => now(),
            ]);

        DB::table('contract_assets')
            ->where('contract_id', $contract_id)
            ->where('is_deleted', 0)
            ->update([
                'is_deleted' => 1,
                'deleted_at' => now(),
            ]);

        // ✅ 5. Get contract info for status check
        $qry = DB::table('contracts')->where('id', $contract_id)->first();

        DB::table('contract_purchasing')
            ->where('contract_id', $contract_id)
            ->delete();
        DB::table('contract_distribution')
            ->where('contract_id', $contract_id)
            ->delete();
        DB::table('contract_attachments')
            ->where('contract_id', $contract_id)
            ->delete();
        DB::table('contract_comments')
            ->where('contract_id', $contract_id)
            ->delete();

        $attachment_array = json_decode($request->attachmentArray, true);
        if (!empty($attachment_array) && is_array($attachment_array)) {
            foreach ($attachment_array as $a) {
                copy(public_path('temp_uploads/' . $a['attachment']), public_path('network_attachment/' . $a['attachment']));
                DB::table('contract_attachments')->insert([
                    'contract_id' => $contract_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a['date'] . ' ' . $a['time'])),
                    'attachment' => $a['attachment'],
                    'name' => $a['name'],
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = json_decode($request->commentArray, true);
        if (!empty($commentArray) && is_array($commentArray)) {
            foreach ($commentArray as $a) {
                DB::table('contract_comments')->insert([
                    'contract_id' => $contract_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a['date'] . ' ' . $a['time'])),
                    'comment' => $a['comment'],
                    'name' => $a['name'],
                    'added_by' => Auth::id(),
                ]);
            }
        }

        // ✅ 6. Decode and insert new contract details + assets
        $distributor_array = json_decode($request->distributor_array, true);
        if (!empty($distributor_array) && is_array($distributor_array)) {
            foreach ($distributor_array as $item) {
                DB::table('contract_distribution')->insert([
                    'contract_id' => $contract_id,
                    'distributor' => $item['distributer'],
                    'reference_no' => $item['reference'],
                    'sales_order_no' => $item['salesorder'],
                    'added_by' => Auth::user()->id,
                ]);
            }
        }
        $purchasing_array = json_decode($request->purchasing_array, true);
        if (!empty($purchasing_array) && is_array($purchasing_array)) {
            foreach ($purchasing_array as $item) {
                DB::table('contract_purchasing')->insert([
                    'contract_id' => $contract_id,
                    'estimate_no' => $item['estimate_no'],
                    'sales_order_no' => $item['sales_order_no'],
                    'invoice_no' => $item['invoice_no'],
                    'invoice_date' => date('Y-m-d', strtotime($item['invoice_date'])),
                    'po_no' => $item['po_no'],
                    'po_date' => date('Y-m-d', strtotime($item['po_date'])),
                    'added_by' => Auth::user()->id,
                ]);
            }
        }
        $lineItems = json_decode($request->lineItems, true);

        if (!is_array($lineItems)) {
            return response()->json(['message' => 'Invalid line items data'], 400);
        }

        foreach ($lineItems as $item) {

            $typeMap = [
                'SFT' => 'Software Support',
                'HDW' => 'Hardware Support',
                'SUB' => 'Subscription',
                'MSP' => 'Other',
            ];
            if ($item['type'] != "") {

                $contract_type_line = $typeMap[$item['type']] ?? $item['type'];

                // Insert each contract detail
                $contractDetailId = DB::table('contract_details')->insertGetId([
                    'contract_id' => $contract_id,
                    'qty' => $item['qty'] ?? 0,
                    'pn_no' => $item['pn_no'] ?? '',
                    'contract_type_line' => $contract_type_line,
                    'detail_comments' => $item['desc'] ?? '',
                    'msrp' => $item['cost'] ?? 0,
                    'is_deleted' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'added_by' => auth()->id(),
                ]);

                // Insert contract assets for this detail
                if (!empty($item['assets'])) {
                    foreach ($item['assets'] as $asset) {
                        DB::table('contract_assets')->insert([
                            'contract_id' => $contract_id,
                            'contract_detail_id' => $contractDetailId,
                            'hostname' => $asset['id'],
                            'is_deleted' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Update asset warranty/support based on contract status
                        DB::table('assets')->where('id', $asset['id'])->update([
                            'warranty_status' => $qry->contract_status === 'Active' ? 'Active' : 'Inactive',
                            'warranty_end_date' => $request->contract_end_date,
                            'SupportStatus' => $qry->contract_status === 'Active' ? 'Supported' : 'Expired',
                        ]);
                    }
                }
            }
        }

        DB::table('contract_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Contract updated', 'contract_id' => $contract_id]);

        return response()->json(['message' => 'Contract updated successfully!']);
    }


    public function Update0Contract(Request $request)

    {



        $data = array(





            'site_id' => $request->site_id,

            'managed_by' => $request->managed,

            'currency' => $request->currency,

            'contract_notification' => $request->contract_notification,

            'estimate_no' => $request->estimate_no,

            'sales_order_no' => $request->sales_order_no,

            'invoice_no' => $request->invoice_no,

            'contract_description' => $request->contract_description,

            'invoice_date' => date('Y-m-d', strtotime($request->invoice_date)),

            'registered_email' => $request->registered_email,

            'po_no' => $request->po_no,

            'po_date' => date('Y-m-d', strtotime($request->po_date)),



            'distributor_id' => $request->distributor_id,

            'reference_no' => $request->reference_no,

            'distrubutor_sales_order_no' => $request->distrubutor_sales_order_no,

            'vendor_id' => $request->vendor_id,

            'contract_type' => $request->contract_type,

            'total_amount' => $request->total_amount,

            'affliates' => $request->selected_clients ? implode(',', $request->selected_clients) : '',

            'contract_no' => $request->contract_no,

            'contract_start_date' => date('Y-m-d', strtotime($request->contract_start_date)),

            'contract_end_date' => date('Y-m-d', strtotime($request->contract_end_date)),

            'updated_by' => Auth::id(),

            'updated_at' => date('Y-m-d H:i:s'),





        );





        $id = $request->id;

        DB::Table('contracts')->where('id', $id)->update($data);

        $qry = DB::Table('contracts')->whereNotNull('client_id')->where('id', $id)->first();





        $asset = DB::table('contract_assets')->where('is_deleted', 0)->where('contract_id', $id)->get();

        foreach ($asset as $a) {



            DB::table('assets')->where('id', $a->hostname)->update(['warranty_status' => 'Inactive', 'warranty_end_date' => 'No contract Found', 'SupportStatus' => 'Unassigned']);
        }

        DB::table('contract_details')->where('contract_id', $id)->where('is_deleted', 0)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);

        DB::table('contract_assets')->where('contract_id', $id)->where('is_deleted', 0)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);







        DB::table('contract_emails')->where('contract_id', $request->id)->delete();



        DB::table('contract_attachments')->where('contract_id', $request->id)->delete();

        DB::table('contract_comments')->where('contract_id', $request->id)->delete();









        $attachment_array = $request->attachmentArray;

        if (isset($request->attachmentArray)) {

            foreach ($attachment_array as $a) {

                $a = json_decode($a);



                // copy(public_path('temp_uploads/' . $a->attachment), public_path('contract_attachment/' . $a->attachment));

                DB::table('contract_attachments')->insert([

                    'contract_id' => $id,

                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),

                    'attachment' => $a->attachment,

                    'name' => $a->name,

                    'added_by' => Auth::id(),

                ]);
            }
        }



        $emailArray = $request->emailArray;

        if (isset($request->emailArray)) {

            foreach ($emailArray as $a) {

                $a = json_decode($a);





                DB::table('contract_emails')->insert([

                    'contract_id' => $id,



                    'renewal_email' => $a->email,



                    'added_by' => Auth::id(),

                ]);
            }
        }



        $commentArray = $request->commentArray;

        if (isset($request->commentArray)) {

            foreach ($commentArray as $a) {

                $a = json_decode($a);





                DB::table('contract_comments')->insert([

                    'contract_id' => $id,

                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),

                    'comment' => $a->comment,

                    'name' => $a->name,

                    'added_by' => Auth::id(),

                ]);
            }
        }



        $contractDetailsArray = $request->contractDetailsArray;

        if (isset($request->contractDetailsArray)) {

            foreach ($contractDetailsArray as $a) {

                $a = json_decode($a);





                DB::table('contract_details')->insert([

                    'contract_id' => $id,

                    'pn_no' => $a->pn_no,

                    'qty' => $a->qty,

                    'msrp' => $a->msrp,

                    'detail_comments' => $a->asset_description,

                    'contract_type_line' => $a->contract_type_line,

                    'added_by' => Auth::id(),

                ]);

                $detail_id = DB::getPdo()->lastInsertId();

                $asset_array = $a->hostname_modal;

                foreach ($asset_array as $b) {

                    $assetQry = DB::table('assets')->where('id', $b)->first();

                    if ($assetQry != '') {

                        DB::table('contract_assets')->insert([

                            'contract_id' => $id,

                            'contract_detail_id' => $detail_id,

                            'asset_type' => @$assetQry->asset_type,

                            'hostname' => $b,

                        ]);


                        if ($qry->contract_status == 'Active') {

                            DB::table('assets')->where('id', $b)->update(['warranty_status' => 'Active', 'warranty_end_date' => $request->contract_end_date, 'SupportStatus' => 'Supported']);
                        } else {

                            DB::table('assets')->where('id', $b)->update(['warranty_status' => 'Inactive', 'warranty_end_date' => $request->contract_end_date, 'SupportStatus' => 'Expired']);
                        }
                    }
                }
            }
        }





        DB::table('contract_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Contract updated', 'contract_id' => $id]);

        return response()->json('success');
    }


    public function getContractContent(Request $request)

    {

        $id = $request->id;

        $html = '';



        $q = DB::table('contracts as a')->select('a.*', 'c.client_display_name', 'c.client_address', 's.site_name', 'd.distributor_name', 'v.vendor_name', 'd.distributor_image', 'c.logo', 'v.vendor_image', 's.address', 's.city', 's.country', 's.phone', 's.zip_code', 's.province', 'usr.firstname as created_firstname', 'usr.lastname as created_lastname', 'upd.firstname as updated_firstname', 'upd.lastname as updated_lastname', 'a.ended_reason', 'a.ended_on', 'ued.firstname as ended_firstname', 'ued.lastname as ended_lastname', 'ued.email as ended_email')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('distributors as d', 'd.id', '=', 'a.distributor_id')->leftjoin('vendors as v', 'v.id', '=', 'a.vendor_id')->leftjoin('users as usr', 'usr.id', '=', 'a.created_by')->leftjoin('users as upd', 'upd.id', '=', 'a.updated_by')->leftjoin('users as ued', 'ued.id', '=', 'a.ended_by')->where('a.id', $id)->first();

        $contract_end_date = date('Y-M-d', strtotime($q->contract_end_date));

        $today = date('Y-m-d');

        $earlier = new DateTime($contract_end_date);

        $later = new DateTime($today);



        $abs_diff = $later->diff($earlier)->format("%a"); //3

        $ended_qry = DB::Table('users')->Where('id', $q->ended_by)->first();

        $renewed_qry = DB::Table('users')->Where('id', $q->renewed_by)->first();







        if ($q->contract_status == 'Active') {



            if ($abs_diff <= 30) {

                $html .= '<div class="block card-round   bg-new-yellow new-nav" >

                                <div class="block-header   py-new-header" >

                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">



                                

                                      <div class="d-flex">

                          <img src="' . ('public/img/icon-upcoming-removebg-preview.png') . '" width="40px">

                                <div class="ml-4">

                                <h4  class="mb-0 header-new-text text-dark" style="line-height:25px">Upcoming</h4>

                                <p class="mb-0  header-new-subtext text-dark" style="line-height:20px">In ' . $abs_diff . ' days</p>

                                    </div>

                                </div>';
            } else {

                $html .= '<div class="block card-round   bg-new-green new-nav" >

                                <div class="block-header   py-new-header" >

                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">



                                

                                <div class="d-flex">

                                <img src="' . ('public/img/icon-active-removebg-preview.png') . '" width="40px">

                                <div class="ml-4">

                                <h4  class="mb-0 header-new-text" style="line-height:25px">Contract Active</h4>

                                <p class="mb-0  header-new-subtext" style="line-height:15px">Until ' . $contract_end_date . ' (' . $abs_diff . ' days remaining)</p>

                                    </div>

                                </div>';
            }
        } elseif ($q->contract_status == 'Inactive') {

            $html .= '<div class="block card-round   bg-new-blue new-nav" >

                                <div class="block-header   py-new-header" >

                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">



                                

                                <div class="d-flex">

                                <img src="' . ('public/img/icon-renewed-removebg-preview.png') . '" width="40px">

                                <div class="ml-4">

                                <h4  class="mb-0 header-new-text" style="line-height:25px">Contract Renewed

                                </h4>

                                       <p class="mb-0  header-new-subtext" style="line-height:15px">On ' . date('Y-M-d H:i:s A', strtotime($q->renewed_on)) . ' by   ' . @$renewed_qry->firstname . ' ' . @$renewed_qry->lastname . '</p>

                                    </div>

                                </div>';
        } elseif ($q->contract_status == 'Expired/Ended') {

            $html .= '<div class="block card-round   bg-new-red new-nav" >

                                <div class="block-header   py-new-header" >

                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">



                                

                                <div class="d-flex">

                                <img src="' . ('public/img/icon-ended-removebg-preview.png') . '" width="40px">

                                <div class="ml-4">

                                <h4  class="mb-0 header-new-text" style="line-height:25px">Contract Ended

</h4>

                                <p class="mb-0  header-new-subtext" style="line-height:15px">On ' . date('Y-M-d', strtotime($q->ended_on)) . ' at  ' . date('H:i:s A', strtotime($q->ended_on)) . ' By ' . @$ended_qry->firstname . ' ' . @$ended_qry->lastname . ' </p>

                                    </div>

                                </div>';
        } elseif ($q->contract_status == 'Ended') {

            $html .= '<div class="block card-round   bg-new-red new-nav" >

                                

                                <div class="block-header   py-new-header" >

                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">



                                

                                <div class="d-flex">

                                <img src="' . ('public/img/icon-expired-removebg-preview.png') . '" width="40px">

                                <div class="ml-4">

                                <h4  class="mb-0 header-new-text" style="line-height:25px">Contract Ended

</h4>   

                                <p class="mb-0  header-new-subtext" style="line-height:15px">On ' . date('Y-M-d', strtotime($q->ended_on)) . ' at  ' . date('H:i:s A', strtotime($q->ended_on)) . ' By ' . @$ended_qry->firstname . ' ' . @$ended_qry->lastname . ' </p>

                                    </div>

                                </div>';
        } elseif ($q->contract_status == 'Expired') {

            $html .= '<div class="block card-round   bg-new-red new-nav" >



                                <div class="block-header   py-new-header" >

                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">



                                

                                <div class="d-flex">

                                <img src="' . ('public/img/icon-expired-removebg-preview.png') . '" width="40px">

                                <div class="ml-4">

                                <h4  class="mb-0 header-new-text" style="line-height:25px">Contract Expired

</h4>

                                <p class="mb-0  header-new-subtext" style="line-height:15px">On ' . $contract_end_date . '</p>

                                    </div>

                                </div>';
        }









        $html .= '<div class="new-header-icon-div d-flex align-items-center no-print"> <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" > 

                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="' . asset('public/img/paper-clip-white.png') . '" width="20px"></a>

                                         </span>

                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 

                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="' . asset('public/img/comment-white.png') . '" width="20px"></a></span>';

        if (Auth::check()) {

            if (@Auth::user()->role != 'read') {



                if ($q->contract_status != 'Inactive') {

                    $html .= '<a href="' . url('renew-contract') . '?id=' . $q->id . '" i  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Renew Contract" class=" ">

                                                <img src="public/img/action-white-renew.png?cache=1" width="20px">

                                            </a>';
                }

                if ($q->contract_status != 'Inactive' && $q->contract_status != 'Expired/Ended') {





                    $html .= '<span  > 

                                             <a href="javascript:;" class="btnEnd" data-status="' . $q->contract_status . '"   data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="End Contract" class=" "><img src="public/img/action-white-end-revoke.png?cache=1" width="20px"></a>

                                         </span>';
                }
            }
        }

        $html .= '

        <a href="' . url("add-contract/support") . '?id=' . $q->id . '" data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Clone"  style="padding:5px 7px">

                                                <img src="' . asset("public/icons/icon-white-clone.png?cache=1") . '" width="22px"  >

        <a  target="_blank" href="' . url('pdf-contract') . '?id=' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Pdf" class="  " style="padding:5px 7px">

                                                <img src="public/img/action-white-pdf.png?cache=1" width="24px">

                                            </a>

     <a  href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">

                                                <img src="public/img/action-white-print.png?cache=1" width="20px">

                                            </a>';





        if (@Auth::user()->role != 'read' && Auth::check()) {



            $html .= '<a   href="' . url('edit-contract') . '?id=' . $q->id . '" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png?cache=1" width="20px">  </a>



                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png?cache=1" width="17px"></a>';
        }



        $affliates_qry = DB::Table('clients')->select('client_display_name')->whereIn('id', explode(',', $q->affliates))->get();

        $affliate_html = [];

        foreach ($affliates_qry as $p) {

            $affliate_html[] = $p->client_display_name;
        }



        $html .= '</div>





                                </div>

                            </div>

                        </div>



                        <div class="block new-block position-relative mt-3" >

                                                <div class="top-div text-capitalize">' . $q->contract_type . ' Contract</div>

                            

                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">

                             

                                <div class="row justify-content- position-relative inner-body-content push" >

 <div class="top-right-div top-right-div-yellow text-capitalize">Client</div>

                            

                            <div class="col-sm-12 m-

                            " >

                                     

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

                                           <div class="bubble-new">Affilations</div> 

                                       </div>

                                                                                  

                                            <div class="col-sm-8">

                                                

                                           <div class="bubble-white-new bubble-text-first text-truncate"><b>' . implode(', ', $affliate_html) . '</b></div> 

                                     

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

                             

                                <div class="row justify-content- position-relative inner-body-content push" >

 <div class="top-right-div top-right-div-red text-capitalize">Vendor</div>

                            

                            <div class="col-sm-12 m-

                            " >

                                     

                        <input type="hidden" name="attachment_array" id="attachment_array" >

                                <div class="row">

 

                                    <div class="col-sm-10">

                                        <div class="form-group row">

                                                        <div class="col-sm-4">

                                           <div class="bubble-new">Managed By</div> 

                                       </div>

                                                                                  

                                            <div class="col-sm-8">

                                                

                                           <div class="bubble-white-new bubble-text-first"><b>' . $q->managed_by . '</b></div> 

                                     

                                            </div>



                                         </div>

                                        <div class="form-group row">

                                                        <div class="col-sm-4">

                                           <div class="bubble-new">Vendor</div> 

                                       </div>

                                                                                  

                                            <div class="col-sm-8">

                                                

                                           <div class="bubble-white-new bubble-text-sec">' . $q->vendor_name . '</div> 

                                     

                                            </div>



                                         </div>

                                         

                                        <div class="form-group row">

                                         <div class="col-sm-4">

                                           <div class="bubble-new">Contract #</div> 

                                       </div>

                                            <div class="col-sm-8">

                                                  <div class="bubble-white-new bubble-text-sec">

                                                    ' . $q->contract_no . '

                                                  

                                                </div> 

                                     

                                            </div>

                                          

                                        </div>

                                         <div class="form-group row">

                                         <div class="col-sm-4">

                                           <div class="bubble-new">Description</div> 

                                       </div>

                                            <div class="col-sm-8">

                                                  <div class="bubble-white-new bubble-text-sec">

                                                ' . $q->contract_description . '

                                                  

                                                </div> 

                                     

                                            </div>

                                          

                                        </div>

                                         <div class="form-group row">

                                         <div class="col-sm-4">

                                           <div class="bubble-new">End User Email</div> 

                                       </div>

                                            <div class="col-sm-8">

                                                  <div class="bubble-white-new bubble-text-sec">

                                                ' . $q->registered_email . '

                                                  

                                                </div> 

                                     

                                            </div>

                                          

                                        </div>

                                    </div>

                                    <div class="col-sm-2">

                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">

                                                <!--  $q->vendor_logos  -->

                                                      <img src="public/vendor_logos' . '/' . $q->vendor_image . '" style="width: 100%;">

                                                </div> 



                                    </div>



                                      

                                               </div>      



                                             





                                   



                                                     

                 

                 </div>

             </div>

         </div>







                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">

                             

                                <div class="row justify-content- position-relative inner-body-content push" >

 <div class="top-right-div top-right-div-blue text-capitalize">Distribution</div>

                            

                            <div class="col-sm-12 m-

                            " >

                                     

                        <input type="hidden" name="attachment_array" id="attachment_array" >

                                <div class="row">

 

                                    <div class="col-sm-10">

                                        <div class="form-group row">

                                                        <div class="col-sm-4">

                                           <div class="bubble-new">Distributor</div> 

                                       </div>

                                                                                  

                                            <div class="col-sm-8">

                                                

                                           <div class="bubble-white-new bubble-text-first"><b>' . $q->distributor_name . '</b></div> 

                                     

                                            </div>



                                         </div>

                                         

                                        <div class="form-group row">

                                         <div class="col-sm-4">

                                           <div class="bubble-new">Reference #</div> 

                                       </div>

                                            <div class="col-sm-8">

                                                  <div class="bubble-white-new bubble-text-sec">

                                                    ' . $q->reference_no . '

                                                

                                                </div> 

                                     

                                            </div>

                                          

                                        </div>

                                         <div class="form-group row">

                                         <div class="col-sm-4">

                                           <div class="bubble-new">Sales Order #</div> 

                                       </div>

                                            <div class="col-sm-8">

                                                  <div class="bubble-white-new bubble-text-sec">

                                                ' . $q->distrubutor_sales_order_no . '

                                                  

                                                </div> 

                                     

                                            </div>

                                          

                                        </div>

                                         

                                    </div>

                                    <div class="col-sm-2">';

        if ($q->distributor_image != '') {





            $html .= '<div class="bubble-white-new bubble-text-sec" style="padding:10px">

 

                                                      <img src="public/distributor_logos/' . $q->distributor_image . '" style="width: 100%;">

                                                </div> ';
        }

        $html .= '</div>



                                      

                                               </div>      



                          

                 </div>

             </div>

         </div>

















                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">

                             

                                <div class="row justify-content- position-relative inner-body-content push" >

 <div class="top-right-div top-right-div-green text-capitalize">Purchasing</div>

                            

                            <div class="col-sm-12">

                                     

                        <input type="hidden" name="attachment_array" id="attachment_array" >

                                <div class="row">

 

                                    <div class="col-sm-6">

                                        <div class="form-group row">

                                                        <div class="col-sm-6">

                                           <div class="bubble-new">Currency

                                           </div> 

                                       </div>

                                                                                  

                                            <div class="col-sm-6 form-group ">

                                                

                                           <div class="bubble-white-new bubble-text-first">

                                                ' . $q->currency . '

                                                  

                                                </div>

                                     

                                            </div>

                                                        <div class="col-sm-6">

                                           <div class="bubble-new">Total Cost

                                           </div> 

                                       </div>

                                                                                  

                                            <div class="col-sm-6 form-group ">

                                                

                                           <div class="bubble-white-new bubble-text-sec">

                                                ' . $q->total_amount . '

                                                  

                                                </div>

                                     

                                            </div>

                                                        <div class="col-sm-6">

                                           <div class="bubble-new">Estimate #

                                           </div> 

                                       </div>

                                                                                  

                                            <div class="col-sm-6 form-group ">

                                                

                                           <div target="_blank" class="bubble-white-new bubble-text-sec"><a href="' . url('GetZohoInvoicesAuth?estimate_number=') . '' . $q->estimate_no . '">' . $q->estimate_no . '</a></div> 

                                     

                                            </div>



    





                                         <div class="col-sm-6 form-group ">

                                           <div class="bubble-new">Sales Order #</div> 

                                       </div>

                                            <div class="col-sm-6 form-group ">

                                                  <div class="bubble-white-new bubble-text-sec">

                                                    <a  target="_blank" href="' . url('GetZohoInvoicesAuth?sales_number=') . '' . $q->sales_order_no . '">' . $q->sales_order_no . '</a> 

                                                  

                                                </div> 

                                     

                                            </div>

                                          

                                      

                                         <div class="col-sm-6 form-group ">

                                           <div class="bubble-new">Invoice #</div> 

                                       </div>

                                            <div class="col-sm-6 form-group ">

                                                  <div class="bubble-white-new bubble-text-sec">

                                           

                                                        <a target="_blank" href="' . url('GetZohoInvoicesAuth?invoice_number=') . '' . $q->invoice_no . '">' . $q->invoice_no . '</a> 

                                                </div> 

                                     

                                            </div>

                                              <div class="col-sm-6 form-group ">

                                           <div class="bubble-new">Invoice Date</div> 

                                       </div>

                                            <div class="col-sm-6 form-group " >

                                                  <div class="bubble-white-new bubble-text-sec">

                                                    ' . date('Y-M-d', strtotime($q->invoice_date)) . '

                                                  

                                                </div> 

                                     

                                            </div>



                                              <div class="col-sm-6 form-group ">

                                           <div class="bubble-new">PO #</div> 

                                       </div>

                                            <div class="col-sm-6 form-group ">

                                                  <div class="bubble-white-new bubble-text-sec">

                                                

                                                      <a href="' . url('GetZohoInvoicesAuth?po_number=') . '' . $q->po_no . '" target="_blank">' . $q->po_no . '</a> 

                                                  

                                                </div> 

                                     

                                            </div>

                                            <div class="col-sm-6 form-group ">

                                           <div class="bubble-new">PO Date</div> 

                                       </div>

                                            <div class="col-sm-6 form-group ">

                                                  <div class="bubble-white-new bubble-text-sec">

                                                     ' . date('Y-M-d', strtotime($q->po_date)) . ' 

                                                  

                                                </div> 

                                     

                                            </div>

                                        </div>

                                        </div>

                                        <div class="col-lg-4">

                                        </div>

                                        <div class="col-sm-2">

                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">

                                                <!--  $q->vendor_logos  -->

                                                      <img src="public/img/static-amaltitek.png?cache=1" style="width: 100%;">

                                                </div> 



                                    

                                         

                                    </div>

                                    



                                      

                                               </div>      



                                             





                                   



                                                     

                 

                 </div>

             </div>

         </div>





     </div>';







        $line_items = DB::Table('contract_details as ca')->select('ca.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'ca.added_by')->where('ca.contract_id', $q->id)->orderBy('ca.contract_detail_id', 'asc')->where('ca.is_deleted', 0)->get();



        if (sizeof($line_items) > 0) {

            // Determine the maximum string length of the 'qty' field

            $maxLength = $line_items->max(function ($item) {

                return strlen((string)$item->qty);
            });



            // Calculate the tag width based on the maximum length

            $baseWidth = 30; // Base width for a single digit

            $tagWidth = $baseWidth * $maxLength;

            $html .= '  <div class="block new-block position-relative mt-5" >

                                                <div class="top-div text-capitalize">Contract Details</div>

                            

                                                          <div class="block-content new-block-content" id="commentBlock"> ';

            foreach ($line_items as $c) {

                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">

                                        <table class="table table-borderless table-vcenter mb-0">

                                            <tbody>

                                                <tr>

                                                    <td class="text-center pr-0" style="width: 38px;">

                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style="width: ' . $tagWidth . 'px;"><b>

                                                          ' . $c->qty . '</b></h1>

                                                    </td>

                                            <td class="js-task-content pl-0">

  <h2 class="mb-0 comments-text">

    <div class="d-flex align-ite ms-center position-rel ative">

      <span class="pn-no  " style=" 

  padding-right: 10px; ">' . $c->pn_no . '</span>';

                if ($c->contract_type_line != '') {

                    $html .= '

    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@300;700&display=swap" rel="stylesheet">

    <div class="contract_type_button px-1 ml-2" style="flex-shrink: 0;" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="">

        <input type="checkbox" class="custom-control-input" id="patched" name="patched" value="1" disabled checked>

        <label class="btn btn-new w-100 py-1" style="font-size: 8pt !important; font-family: \'Jura\', sans-serif !important; border-color: #595959; color: #595959;" for="patched">

            ' . htmlspecialchars($c->contract_type_line) . '

        </label>

    </div>';
                }

                $html .= '</div>

    <span class="comments-subtext">' . $c->detail_comments . '</span>

  </h2>

</td>



                                                    <td class="text-right position-relative" style="width: 130px;">

                                                        

                                                            <h3 class="mb-0" style="    position: absolute;

    top: 6px;

    right: 10px;

}">$' . number_format($c->msrp, 2) . '</h3>

                                                    </td>

                                                </tr>

                                                <tr >

                                                    <td colspan="2"  class="pt-0" style="vertical-align: top;" >

                                                       <div class="pl-2  mb-0 row  "> ';

                $line_item = DB::Table('contract_assets as ca')->selectRaw('a.hostname,a.AssetStatus,o.operating_system_name,a.fqdn,a.id,at.asset_icon,at.asset_type_description,a.sn,a.asset_type,( SELECT row_number FROM (

    SELECT   id,@curRow := @curRow + 1 AS row_number 

    FROM (

        SELECT * FROM assets  where is_deleted=0  

        ORDER BY id ASC

    ) l 

    JOIN (

        SELECT @curRow := 0 

    ) r

) t where t.id=ca.hostname limit 1) as rownumber ')->where('ca.contract_id', $q->id)->where('ca.contract_detail_id', $c->contract_detail_id)->join('assets as a', 'a.id', '=', 'ca.hostname')->join('operating_systems as o', 'a.os', '=', 'o.id')->leftjoin('asset_type as at', 'a.asset_type_id', '=', 'at.asset_type_id')->groupBy('ca.hostname')->where('ca.is_deleted', 0)->orderBy('a.hostname', 'asc')->get();

                $cvm = '';

                foreach ($line_item as $l) {





                    $html .= '<div class="col-lg-4 px-1"><div class="block block-rounded table-block-new ">





<div class="d-flex block-content align-items-center px-2 py-2"><p class="font-11pt mr-1   mb-0  ' . ($l->asset_type == 'physical' ? 'c4-p' : 'c4-v') . ' " style="max-width:20px; " data="262">' . ($l->asset_type == 'physical' ? 'P' : 'V') . '</p><p class="font-12pt w-100 mb-0 text-truncate   c4" ' . ($l->asset_type == 'physical' ? 'data-toggle="tooltip"' : '') . ' data-trigger="hover" data-placement="top" data-title="' . $l->fqdn . '" style="  background-color: rgb(151, 192, 255); color: rgb(89, 89, 89); border-color: rgb(89, 89, 89);' . ($l->asset_type == 'physical' ? 'text-transform: uppercase;' : 'text-transform: lowercase;') . '" data="262">' . ($l->asset_type == 'physical' ? $l->sn : $l->fqdn) . '</p>';



                    if ($l->asset_type == 'physical') {

                        $html .= "<img src='public/img/icon-p-asset-d-grey.png' class='toggle pl-2' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title=''   width='35px' style='object-fit:contain'  >";
                    } else {

                        $html .= "<img src='public/img/icon-vm-grey-darker.png' class='toggle pl-2' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title=''   width='30px' style='object-fit:contain'  >";
                    }





                    $html .= ' <a  class="dropdown-toggle ml-2"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>

                                <img src="public/img/dots.png?cache=1"   >

                                                                        </a>

                                         <div class="dropdown-menu py-0 pt-1 " aria-labelledby="dropdown-dropright-primary">

      

                  <a class="dropdown-item d-flex align-items-center px-0" target="_blank" href="' . url('assets') . '/' . ($l->asset_type) . '?id=' . $l->id . '&page=' . ceil($l->rownumber / 10) . '" >   <div style="width: 32;  padding-left: 2px"><img src="public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > &nbsp;&nbsp;View Asset</div></a>  

                 

                </div>





</div>



 

           </div></div>';
                }





                $html .= '</div>

                                                    </td>

                                                    

                                                </tr>



                                        </tbody>

                                    </table>



                                    </div>';
            }



            $html .= '</div>



                            </div>';
        }

        $contract = DB::table('contract_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('contract_id', $q->id)->get();

        // dd($contract);

        if (sizeof($contract) > 0) {

            $html .= '<div class="block new-block position-relative mt-5" >

            <div class="top-div text-capitalize">Comments</div>

            

            <div class="block-content new-block-content" id="commentBlock"> ';

            foreach ($contract as $c) {

                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">

                                        <table class="table table-borderless table-vcenter mb-0">

                                            <tbody>

                                                <tr>

                                                    <td class="text-center pr-0" style="width: 38px;">

                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>

                                                          <img width="40px" class="bg-dark" height="40" style="border-radius: 50%;" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b></h1>

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









        $contract = DB::table('contract_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('contract_id', $q->id)->get();

        if (sizeof($contract) > 0) {

            $html .= '<div class="block new-block position-relative mt-5" >

                                                <div class="top-div text-capitalize">Attachments</div>

                            

                                                          <div class="block-content new-block-content  px-4 row" id="attachmentBlock"> ';

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

                                                          <img width="40px" class="bg-dark" height="40" style="border-radius: 50%;" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b></h1>

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

                                                        <a href="public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text--"><img src="public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>

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





        $contract = DB::table('contract_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.contract_id', $q->id)->get();



        if (sizeof($contract) > 0) {

            $html .= '<div class="block new-block position-relative mt-5" >

                                                <div class="top-div text-capitalize">Audit Trial</div>

                            

                                                          <div class="block-content new-block-content" id="commentBlock">';

            foreach ($contract as $c) {

                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">

                                        <table class="table table-borderless table-vcenter mb-0">

                                            <tbody>

                                                <tr>

                                                    <td class="text-center pr-0" style="width: 38px;">

                                                         <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>

                                                          <img width="40px" class="bg-dark" height="40" style="border-radius: 50%;" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b></h1>

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



    public function getContractContentNew(Request $request)
    {
        $id = $request->id;


        $contract_distribution = DB::table('contract_distribution')
            ->where('contract_id', $id)
            ->get();
        $contract_purchasing = DB::table('contract_purchasing')
            ->where('contract_id', $id)
            ->get();
        $html = '';
        $q = DB::table('contracts as a')
            ->select('a.*', 'c.client_display_name', 'c.client_address', 's.site_name', 'd.distributor_name', 'v.vendor_name', 'd.distributor_image', 'c.logo', 'v.vendor_image', 's.address', 's.city', 's.country', 's.phone', 's.zip_code', 's.province', 'usr.firstname as created_firstname', 'usr.lastname as created_lastname', 'upd.firstname as updated_firstname', 'upd.lastname as updated_lastname', 'a.ended_reason', 'a.ended_on', 'ued.firstname as ended_firstname', 'ued.lastname as ended_lastname', 'ued.email as ended_email')
            ->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')
            ->leftjoin('distributors as d', 'd.id', '=', 'a.distributor_id')
            ->leftjoin('vendors as v', 'v.id', '=', 'a.vendor_id')
            ->leftjoin('users as usr', 'usr.id', '=', 'a.created_by')
            ->leftjoin('users as upd', 'upd.id', '=', 'a.updated_by')
            ->leftjoin('users as ued', 'ued.id', '=', 'a.ended_by')
            ->where('a.id', $id)->first();
        $message = DB::table('pinned_messages')
            ->where('linked_id', $id)
            ->where('page', 'contract')
            ->get();
        $line_items = DB::Table('contract_details as ca')
            ->select('ca.*', 'u.user_image')
            ->leftjoin('users as u', 'u.id', '=', 'ca.added_by')
            ->where('ca.contract_id', $q->id)
            ->where('ca.is_deleted', 0)
            ->orderBy('ca.contract_detail_id', 'asc')
            ->get();
        $contract_no = @$q->contract_no;
        $header_desc = @$q->contract_description;
        $contract_end_date = date('Y-M-d', strtotime(@$q->contract_end_date));
        $today = date('Y-m-d');
        $earlier = new DateTime($q->contract_end_date);
        $later = new DateTime($today);
        $contract_s_date = date('d-M-Y', strtotime(@$q->contract_start_date));
        $contract_e_date = date('d-M-Y', strtotime(@$q->contract_end_date));

        $end_date = @$q->contract_end_date;

        // Calculate the difference in days
        // $diff = (strtotime($end_date) - strtotime($today)) / (60 * 60 * 24);
        $diff = abs((strtotime($end_date) - strtotime($today)) / (60 * 60 * 24));


        // Convert to integer (in case of decimal)
        $total_days = (int)$diff;


        $abs_diff = $later->diff($earlier)->format('%a'); //3
        $day_text = ($abs_diff == 1) ? 'Day' : 'Days';
        $ended_qry = DB::Table('users')->Where('id', $q->ended_by)->first();
        $renewed_qry = DB::Table('users')->Where('id', $q->renewed_by)->first();

        if ($q->contract_status == 'Active') {

            if ($abs_diff <= 30) {
                $headerImg = asset('public/img/icon-upcoming-removebg-preview.png');
                $headerText = 'Upcoming';
                $headerSubText = 'In ' . $abs_diff . ' days';
            } else {
                $headerImg = asset('public/img/icon-active-removebg-preview.png');
                $headerText = 'Contract Active';
                $headerSubText = 'Until ' . $contract_end_date . ' (' . $abs_diff . ' days remaining)';
            }
        } elseif ($q->contract_status == 'Inactive') {
            $headerImg = asset('public/img/icon-renewed-removebg-preview.png');
            $headerText = 'Contract Renewed';
            $headerSubText = 'On ' . date('Y-M-d H:i:s A', strtotime($q->renewed_on)) . ' by   ' . @$renewed_qry->firstname . ' ' . @$renewed_qry->lastname;
        } elseif ($q->contract_status == 'Expired/Ended') {

            $headerImg = asset('public/img/icon-ended-removebg-preview.png');
            $headerText = 'Contract Ended';
            $headerSubText = 'On ' . date('Y-M-d', strtotime($q->ended_on)) . ' at  ' . date('H:i:s A', strtotime($q->ended_on)) . ' By ' . @$ended_qry->firstname . ' ' . @$ended_qry->lastname;
        } elseif ($q->contract_status == 'Ended') {
            $headerImg = asset('public/img/icon-expired-removebg-preview.png');
            $headerText = 'Contract Ended';
            $headerSubText = 'On ' . date('Y-M-d', strtotime($q->ended_on)) . ' at  ' . date('H:i:s A', strtotime($q->ended_on)) . ' By ' . @$ended_qry->firstname . ' ' . @$ended_qry->lastname;
        } elseif ($q->contract_status == 'Expired') {

            $headerImg = asset('public/img/icon-expired-removebg-preview.png');
            $headerText = 'Contract Expired';
            $headerSubText = 'On ' . $contract_end_date;
        }

        if (Auth::check()) {

            if (@Auth::user()->role != 'read') {
            }
        }

        if (@Auth::user()->role != 'read' && Auth::check()) {
        }

        $affliates_qry = DB::Table('clients')->select('client_display_name')->whereIn('id', explode(',', $q->affliates))->get();

        $affliate_html = [];

        foreach ($affliates_qry as $p) {
            $affliate_html[] = $p->client_display_name;
        }



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
  <div class="tab-pane fade show active" id="nav-main-contract" role="tabpanel" aria-labelledby="nav-main-tab-contract">

<div class="block new-block position-relative mt-2" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="titillium-web-black mb-3 text-darkgrey">General Information</h5>
            </div>
            <div class="col-sm-12">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">Client</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-user-tie text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . $q->client_display_name . '</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">Site</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-buildings text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2" style="line-height: 1.5;">
                        <b>' . $q->site_name . '</b><br>
                        ' . $q->address . '<br>
                        ' . $q->city . ', ' . $q->province . '<br>
                        ' . $q->zip_code . '</h6>
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
                <h5 class="titillium-web-black mb-3 text-darkgrey">Affiliates</h5>
            </div>
            <div class="col-sm-12">
                              <div class="border py-2 pl-2 pr-1 mb-3 border-style">
                                  <div class="d-flex align-items-center justify-content-between my-2">
                                      <h6 class="font-titillium text-grey mb-0 fw-700 pl-2">Affiliate</h6>
                                  </div>';
        if (count($affliates_qry) > 0) {
            $html .= '<div class="table-responsive pr-2 small-box small-box-no-arrow">
                                      <table class="table table-sm table-striped </table>align-middle mb-0 added-affiliates">
                                          <tbody>';
            foreach ($affliates_qry as $p) {
                $html .= '<tr class="affiliate-item banner-icon" data-client-id="16">
                <td class="py-2 border-0 align-middle" width="20" style="border-radius: 13px 0 0 13px;">
                </td>
                <td class="py-2 border-0 ">
                    <button type="button" class="btn mr-1 p-0">
                        <i class="fa-thin fa-people-arrows text-grey fs-18 regular-icon"></i>
                        <i class="fa-solid fa-people-arrows text-darkgrey fs-18 header-solid-icon"></i>
                    </button>
                    <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">' . $p->client_display_name . '</span>
                </td>
                <td class="py-2 border-0 text-right align-middle" width="50" style="border-radius: 0 13px 13px 0;">
                    
                </td>
            </tr>';
            }

            $html .= '</tbody>
                                      </table>
                                  </div>';
        } else {
            $html .= '<div class="font-titillium text-darkgrey fw-400 mb-0 text-center py-3">No affiliates assigned</div>';
        }
        $html .= '</div>
                          </div>
        </div>
    </div> 
</div> ';

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
        if ($q->contract_notification == 1) {
            $emails = DB::table('contract_emails')
                ->where('contract_id', $id)
                ->pluck('renewal_email')
                ->toArray();
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

        $html .= '<div class="block new-block position-relative mt-3" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Contract Information</h5>
            </div>
            <div class="col-sm-6">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">Vendor</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-industry-windows text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . $q->vendor_name . '</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">Contract/Reference #</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-file-contract text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . $q->contract_no . '</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">Contract Type</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-puzzle text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . ($q->contract_type=='Other'?'MSP':$q->contract_type) . '</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">Start Date</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-calendar-range text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . date('d-M-Y', strtotime($q->contract_start_date)) . '</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">End Date</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-calendar-range text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . date('d-M-Y', strtotime($q->contract_end_date)) . '</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">Description</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-pencil-line text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . $q->contract_description . '</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">End User Email</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-id-card text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . $q->registered_email . '</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">Managed By</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-crosshairs-simple text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2">' . $q->managed_by . '</h6>
                    </div>
                </div>
            </div>
            
        </div>
    </div> 
</div>';

        $html .= '</div>
  <div class="tab-pane fade" id="nav-purchasing-contract" role="tabpanel" aria-labelledby="nav-purchasing-tab-contract">


<div class="block new-block position-relative mt-3" style="margin-bottom: 1rem;">
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-11">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Distribution</h5>
            </div>
            <div class="col-sm-1 text-right">';
        if (count($contract_distribution) > 0) {
            $html .= '<div class="download-icon"  id="downloadDistributerCSV"
                    style="right: 25px; top: 10px; cursor: pointer;">
                    <i class="fa-light fa-arrow-down-to-line text-grey fs-18" style="font-size:20px;"></i>
                </div>';
        }
        $html .= '</div>
            <div class="col-sm-12">
                <div class="border p-2 mb-3 border-style pl-3">
                    <div class="distributer-download-toast" role="status" aria-live="polite">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fa-light fa-circle-check mr-2"></i>
                                <span class="font-titillium fs-14 text-darkgrey">Details downloaded successfully!</span>
                            </div>
                            <button type="button" data-section="distributer-download-toast"
                                class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                <i class="fa-light fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive distributer-details-container small-box-no-arrow">
                    <table class="table table-sm table-striped table-borderless distributer-table">
                        <thead>
                            <tr>
                                <th><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head pl-2">Distributor</h6></th>
                                <th><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head">Reference No.</h6></th>
                                <th><h6 class="border-secondary font-titillium fw-700 mb-0 text-table-head">Sales Order No.</h6></th>
                            </tr>
                        </thead>
                        <tbody class="scrollable-tbody">';
        // if(!empty($q->reference_no) && !empty($q->distrubutor_sales_order_no)) {
        if (count($contract_distribution) > 0) {
            foreach ($contract_distribution as $c) {
                $name = DB::table('distributors')->where('id', $c->distributor)->first();
                $html .= '<tr>
                                    <td class="font-titillium text-darkgrey fw-400 mb-0 " id="distributor_name"><span class="ml-2">' . $name->distributor_name . '</span></td>
                                    <td class="font-titillium text-darkgrey fw-400 mb-0" id="reference_no">' . $c->reference_no . '</td>
                                    <td class="font-titillium text-darkgrey fw-400 mb-0" id="sales_order_no">' . $c->sales_order_no . '</td>
                                </tr>';
            }
        }
        $html .= '</tbody>
                    </table></div>';
        if (count($contract_distribution) == 0) {
            $html .= '<h6 class="font-titillium text-darkgrey mb-0 text-center">No Details Found</h6>';
        }
        $html .= '</div>
            </div>
        </div>
    </div> 
</div> 

<div class="block new-block position-relative mt-3" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-11">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Purchasing</h5>
            </div>
            <div class="col-sm-1 text-right">';
        if (count($contract_purchasing) > 0) {
            $html .= '<div class="download-icon"  id="downloadPurchasingCSV"
                    style="right: 25px; top: 10px; cursor: pointer;">
                    <i class="fa-light fa-arrow-down-to-line text-grey fs-18" style="font-size:20px;"></i>
                </div>';
        }
        $html .= '</div>
            <div class="col-sm-12">
                <div class="border p-2 mb-3 border-style pl-3">
                <div class="purchase-download-toast" role="status" aria-live="polite">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fa-light fa-circle-check mr-2"></i>
                                <span class="font-titillium fs-14 text-darkgrey">Details downloaded successfully!</span>
                            </div>
                            <button type="button" data-section="purchase-download-toast"
                                class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                <i class="fa-light fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive purchasing-details-container small-box-no-arrow">
                    <table class="table table-sm table-striped table-borderless purchasing-table">
                        <thead>
                            <tr>
                                <th><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head pl-2">Estimate No.</h6></th>
                                <th><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head">Sales Order No.</h6></th>
                                <th><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head">Invoice No.</h6></th>
                                <th><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head">Invoice Date</h6></th>
                                <th><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head">PO No.</h6></th>
                                <th><h6 class="border-secondary font-titillium fw-700 mb-0 text-table-head">PO Date</h6></th>
                            </tr>
                        </thead>
                        <tbody class="scrollable-tbody">';
        if (count($contract_purchasing) > 0) {
            // if(!empty($q->estimate_no) && !empty($q->sales_order_no) && !empty($q->invoice_no) && !empty($q->invoice_date) && !empty($q->po_no) && !empty($q->po_date)) {
            foreach ($contract_purchasing as $c) {
                $html .=
                    '<tr>
                                    <td class="font-titillium text-darkgrey fw-400 mb-0" id="estimate_no"><span class="ml-2">' . $c->estimate_no . '</span></td>
                                    <td class="font-titillium text-darkgrey fw-400 mb-0" id="sale_order_no">' . $c->sales_order_no . '</td>
                                    <td class="font-titillium text-darkgrey fw-400 mb-0" id="invoice_no">' . $c->invoice_no . '</td>
                                    <td class="font-titillium text-darkgrey fw-400 mb-0" id="invoive_date">' . date('d-M-Y', strtotime($c->invoice_date)) . '</td>
                                    <td class="font-titillium text-darkgrey fw-400 mb-0" id="po_no">' . $c->po_no . '</td>
                                    <td class="font-titillium text-darkgrey fw-400 mb-0" id="po_date">' . date('d-M-Y', strtotime($c->po_date)) . '</td>
                                </tr>';
            }
        }
        $html .= '</tbody>
                    </table></div>';
        if (count($contract_purchasing) == 0) {
            $html .= '<h6 class="font-titillium text-darkgrey mb-0 text-center">No Details Found</h6>';
        }
        $html .= '
                </div>
            </div>
        </div>
    </div> 
</div> 


  </div>
  <div class="tab-pane fade" id="nav-details-contract" role="tabpanel" aria-labelledby="nav-details-tab-contract">


<div class="block new-block position-relative mt-3" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12 d-flex justify-content-between">
                <h5 class="titillium-web-black mb-3 content-title">Contract Details</h5>
                
            </div>
            <div class="col-sm-2">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">Currency</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-calendar-range text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2" id="contract_currency">' . $q->currency . '</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="border p-2 mb-3 border-style pl-3">
                    <h6 class="font-titillium content-title mb-1 fw-700">Total Cost</h6>
                    <div class="d-flex pt-1 mb-1">
                        <i class="fa-light fa-money-check-dollar text-grey fs-18"></i>
                        <h6 class="font-titillium text-grey fw-300 mb-0 ml-2" id="contract_total_amount">$' . $q->total_amount . '</h6>
                    </div>
                </div>
            </div>
            <div class="align-items-end col-sm-6 d-flex justify-content-end py-3">';
        if (count($line_items) > 0) {
            $html .= '<div class="download-icon" id="downloadContractCSV" 
                    style="right: 25px; top: 10px; cursor: pointer;">
                    <i class="fa-light fa-arrow-down-to-line text-grey fs-18" style="font-size:20px;"></i>
                </div>';
        }
        $html .= '</div>';

        $html .= '<div class="col-sm-12">
                <div class="border p-2 mb-3 border-style pl-3 contract-details-container">
                <div class="contract-details-download-toast" role="status" aria-live="polite">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fa-light fa-circle-check mr-2"></i>
                                <span class="font-titillium fs-14 text-darkgrey">Details downloaded successfully!</span>
                            </div>
                            <button type="button" data-section="contract-details-download-toast"
                                class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                <i class="fa-light fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                    <table class="table table-sm ' . (count($line_items) > 0 ? 'table-striped' : '') . ' table-borderless contract-table">
    <thead>
        <tr>
            <th width="3%"><h6 class="border-secondary font-titillium fw-700 mb-0 text-table-head"></h6></th>
            <th class=""><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head text-center">Qty</h6></th>
            <th class=""><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head">PN #</h6></th>
            <th class="" width="6%"><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head text-center">Type</h6></th>
            <th class="w-50 "><h6 class="border-right border-secondary font-titillium fw-700 mb-0 text-table-head">Description</h6></th>
            <th><h6 class="border-secondary font-titillium fw-700 mb-0 text-table-head text-right">Cost</h6></th>
        </tr>
    </thead>';
        if (count($line_items) > 0) {
            $html .= '
    <tbody class="scrollable-tbody">
';

            foreach ($line_items as $c) {

                // Contract detail type
                if ($c->contract_type_line == 'Subscription') {
                    $contract_detail_type = "SUB";
                } elseif ($c->contract_type_line == 'Hardware Support') {
                    $contract_detail_type = "HDW";
                } elseif ($c->contract_type_line == 'Software Support') {
                    $contract_detail_type = "SFT";
                } else {
                    $contract_detail_type = "MSP";
                }

                $typeClass = '';
                switch ($contract_detail_type) {
                    case 'SUB': // Subscription
                        $typeClass = 'tag-subscription';
                        break;
                    case 'HDW': // Hardware
                        $typeClass = 'tag-hardware';
                        break;
                    case 'SFT': // Software
                        $typeClass = 'tag-software';
                        break;
                    case 'MSP': // Other (MSP)
                        $typeClass = 'tag-msp';
                        break;
                    default:
                        $typeClass = '';
                        break;
                }

                // Fetch assets
                $line_item3 = DB::table('contract_assets as ca')
                    ->selectRaw("
                        a.hostname,
                        a.AssetStatus,
                        o.operating_system_name,
                        a.fqdn,
                        a.id,
                        at.asset_icon,
                        at.asset_type_description,
                        a.sn,
                        a.asset_type,
                        (
                            SELECT row_number
                            FROM (
                                SELECT id, @curRow := @curRow + 1 AS row_number
                                FROM (
                                    SELECT * FROM assets WHERE is_deleted = 0 ORDER BY id ASC
                                ) l
                                JOIN (SELECT @curRow := 0) r
                            ) t 
                            WHERE t.id = ca.hostname 
                            LIMIT 1
                        ) as rownumber
                    ")
                    ->where('ca.contract_id', $q->id)
                    ->where('ca.contract_detail_id', $c->contract_detail_id)
                    ->join('assets as a', 'a.id', '=', 'ca.hostname')
                    ->join('operating_systems as o', 'a.os', '=', 'o.id')
                    ->leftJoin('asset_type as at', 'a.asset_type_id', '=', 'at.asset_type_id')
                    ->where('ca.is_deleted', 0)
                    ->groupBy('ca.hostname')
                    ->orderBy('a.hostname', 'asc')
                    ->get();


                // Build popover content (max 5 assets + "n more")
                $cvm2 = '<div class="text-center" style="font-size:11pt;">Assigned Assets</div>';
                $totalAssets = count($line_item3);
                if ($totalAssets == 0) {
                    $cvm2 .= '<div class="fs-15 fw-500 font-titillium text-center text-truncate  mt-1">None</div>';
                } else {
                    foreach ($line_item3 as $index => $l) {
                        if ($index >= 5) break;

                        if ($l->AssetStatus == 1) {
                            $cvm2 .= '<div class="text-truncate text-success fw-500 font-titillium fs-14 mt-1 d-flex align-items-center"><i class="fa-thin fa-network-wired mr-2 fs-14"></i>'
                                . ($l->asset_type == 'physical' ? e($l->sn) : e($l->fqdn))
                                . '</div>';
                        } else {
                            $statusClass = 'text-danger';
                            $cvm2 .= '<div class="text-truncate text-red fw-500 font-titillium fs-14 mt-1 d-flex align-items-center"><i class="fa-thin fa-server mr-2 fs-14"></i>'
                                . ($l->asset_type == 'physical' ? e($l->sn) : e($l->fqdn))
                                . '</div>';
                        }
                    }

                    if ($totalAssets > 5) {
                        $remaining = $totalAssets - 5;
                        $dataAssets = htmlspecialchars(json_encode($line_item3->toArray()), ENT_QUOTES, 'UTF-8');

                        $cvm2 .= '<div class="text-muted font-italic mt-2 mb-1">'
                            . $remaining . ' more assets</div><a href="javascript:;" class="showAssetsModal mt-0" data-assets="' . $dataAssets . '">'
                            . '<i class="fa-thin fa-ellipsis-stroke text-muted fs-22"></i>'
                            . '</a>';
                    }
                }



                // Modal content with all assets
                $allAssetsHtml = '';
                foreach ($line_item3 as $l) {
                    $allAssetsHtml .= '<div class="mb-1">'
                        . ($l->asset_type == 'physical' ? e($l->sn) : e($l->fqdn))
                        . '</div>';
                }

                // Hidden inline popover div
                $popoverHtml = '<div class="asset-popover-read d-none" id="popover-' . $c->contract_detail_id . '" id="first_value">'
                    . $cvm2
                    . '</div>';

                $html .= '                               
<tr>
    <td class="mb-0" style="color:#0D0D0D!important">
        <div class="asset-trigger-read">
            <i class="fa-thin fa-server fa-server fs-20"
                style="--fa-primary-color:#36454f;--fa-secondary-color:#36454f;--fa-secondary-opacity:.2;">
            </i>
            ' . $popoverHtml . '
        </div>
    </td>
    <td class="position-relative font-titillium text-darkgrey fw-400 mb-0 qty text-center">' . $c->qty . '</td>
    <td class="position-relative font-titillium text-darkgrey fw-400 mb-0 pn_no">' . $c->pn_no . '<a href="javascript:void();" class="copy-detail-line position-absolute" style="right: 10px; display:none;" data-text="' . $c->pn_no . '"><i class="fa-copy fs-18 fa-thin" style="color: #263050;"></i></a></td>
    <td class="position-relative font-titillium text-darkgrey fw-400 mb-0 contract_detail_type text-center"><span class="' . $typeClass . ' type-tag" style="color:#333!important">' . $contract_detail_type . '</span></td>
    <td class="position-relative font-titillium text-darkgrey fw-400 mb-0 detail_comments">
    
    <span class="truncate-text cell-text font-titillium text-darkgrey" style>
        ' . $c->detail_comments . '
    </span>

    <a href="javascript:void();" 
       class="copy-detail-line position-absolute" 
       style="right: 10px; display:none;" 
       data-text="' . $c->detail_comments . '">
        <i class="fa-copy fs-18 fa-thin" style="color: #263050;"></i>
    </a>

</td>

    <td class="position-relative font-titillium text-darkgrey fw-400 mb-0 msrp text-right">$' . number_format($c->msrp, 2) . '</td>
</tr>

    ';
            }

            $html .= '
    </tbody>';
        }
        $html .= '
</table>';
        if (count($line_items) == 0) {
            $html .= '<h6 class="font-titillium text-darkgrey mb-0 text-center">No Details Found</h6>';
        }
        $html .= '</div>
            </div>
        </div>
    </div> 
</div> 


  </div>
  <div class="tab-pane fade" id="nav-comments-contract" role="tabpanel" aria-labelledby="nav-comments-tab-contract">


<div class="block new-block position-relative mt-3" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Comments</h5>
            </div>
';
        $contract = DB::table('contract_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('contract_id', $q->id)->where('v.is_deleted', 0)->get();

        if (sizeof($contract) > 0) {
            $html .= '<div class="col-sm-12"><button type="button" data-toggle="modal" data-target="#CommentModal" class="btn font-titillium fw-500 py-1 px-3 ml-3 new-ok-btn mb-3" style="width: fit-content;">Add Comment</button></div>';
            foreach ($contract as $c) {
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
<a class="float-right edit-comment-contract mr-2" data-id="' . $c->id . '" data-contract-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
<i class="fa-thin fa-pen text-darkgrey fs-18"></i>
</a> 
<a class="float-right delete-comment-contract" data-id="' . $c->id . '" data-contract-id="' . $id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
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
            <div class="font-titillium text-darkgrey mb-0 contractDetailsBody-empty pb-2 pt-0">No comments. Add a comment by using the Add Comment button.</div>
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
  <div class="tab-pane fade" id="nav-attachments-contract" role="tabpanel" aria-labelledby="nav-attachments-tab-contract">


<div class="block new-block position-relative mt-3" >
    <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="titillium-web-black mb-3 text-darkgrey">Attachments</h5>
            </div>
';

        $contract = DB::table('contract_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('contract_id', $q->id)->where('v.is_deleted', 0)->get();

        if (sizeof($contract) > 0) {
            $html .= '<div class="col-sm-12"><button type="button" data-toggle="modal" data-target="#AttachmentModal" class="btn font-titillium fw-500 py-1 px-3 ml-3 new-ok-btn mb-3" style="width: fit-content;">Add Attachemnt</button></div>';
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
                                    <a class="float-right delete-attachment" data-id="' . $c->id . '" data-contract-id="' . $id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
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
            <div class="font-titillium text-darkgrey mb-0 contractDetailsBody-empty pb-2 pt-0">No attachments. Add an Attachment by using the Add Attachment button.</div>
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
  <div class="tab-pane fade" id="nav-audit-contract" role="tabpanel" aria-labelledby="nav-audit-tab-contract">


  
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

        $contract_audit = DB::table('contract_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.contract_id', $q->id)->get();

        if (sizeof($contract_audit) > 0) {
            foreach ($contract_audit as $c) {
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
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-3"></i>
                                        <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Select recipients to send e-mail notification for this contract upcoming renewal.</span>
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
        $notify_emails = DB::table('contract_emails')
            ->where('contract_id', $id)
            ->pluck('renewal_email')
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
            $html .= '<button type="button" class="btn ok-btn cancel-btn btn-notify-email" data-contract-id="' . $id . '" disabled>Notify</button>';
        } else {
            $html .= '<button type="button" class="btn ok-btn btn-primary btn-notify-email" data-contract-id="' . $id . '">Notify</button>';
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

        if (count($message) > 0) {
            foreach ($message as $m) {
                $html .= '<tr class="pinned-message-item banner-icon" data-contract-id="' . $id . '">
                                                <td class="py-2 border-0 " style="border-radius: 13px 0 0 13px;">
                                                    <span class="fw-300 text-darkgrey font-titillium fs-15 pinned-message">' . $m->message . '</span>
                                                </td>
                                                <td class="py-2 border-0 text-right align-middle" width="50" style="border-radius: 0 13px 13px 0;">
                                                    
                                                    <a href="javascript:;" class="align-items-center d-flex drag-handle justify-content-center mb-0 remove-pinned-message" data-contract-id="' . $id . '" data-message="' . $m->message . '">
                                                            <i class="fa-light fa-circle-xmark mr-0 text-grey fs-18"></i>
                                                        </a>
                                                </td>
                                            </tr>';
            }
        }
        $html .= '</tbody>
                                      </table>';
        if (count($message) == 0) {
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
                                <button type="button" class="btn ok-btn btn-primary btn-add-pin-messages" data-contract-id="' . $id . '">Ok</button>
                                    <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
        $iconHtml = '';
        if (@Auth::user()->role != 'read') {



            if ($q->contract_status != 'Inactive') {

                if ($q->contract_status == 'Expired/Ended') {

                    $iconHtml .= '<a class="text-white banner-icon btnEnd mr-0" href="javascript:;" data-ended="1" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reinstate Contract" data="' . $id . '" data-id="' . $id . '">
                    <i class="fa-light fa-arrow-up-to-arc regular-icon"></i>
                    <i class="fa-solid fa-arrow-up-to-arc solid-icon" style="padding-right: 3px; padding-left: 1.5px;"></i></a>';
                } else {

                    $iconHtml .= '<a class="text-white banner-icon btn-renew mr-0" href="javascript:;" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Renew Contract" data="' . $id . '" data-id="' . $id . '">
                    <i class="fa-light fa-arrows-rotate regular-icon"></i>
                    <i class="fa-solid fa-arrows-rotate solid-icon" style="padding-right: 3px; padding-left: 1.5px;"></i>
                    
                                                </a>';
                }
            }

            if ($q->contract_status != 'Inactive' && $q->contract_status != 'Expired/Ended') {





                $iconHtml .= '<span> 
                                 <a href="javascript:;" class="btnEnd text-white banner-icon ml-0" data-status="' . $q->contract_status . '"  data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="End Contract" class=" ">
                                 <i class="fa-light fa-circle-chevron-down regular-icon"></i>
                                 <i class="fa-solid fa-circle-chevron-down solid-icon" style="padding-right: 3px; padding-left: 1.5px;"></i>
                             </span>';
            }
        }

        $editUrl = url('edit-contract') . '?id=' . $q->id;
        $cloneUrl = url("add-contract/support") . '?id=' . $q->id;
        $pdfUrl = url('pdf-contract') . '?id=' . $q->id;

        // $cards = ``;

        $cards = '<div class="container px-0 mt-0" style="overflow: hidden;">
    <div class="d-flex">
    <div class="cards-container" style="display: none;">
        <div class="mr-3 status-card">
';
        if ($q->contract_status == 'Active') {
            if ($abs_diff <= 30) {
                $cards .= '        
            <div class="rounded-10px  pl-2 pr-4 pt-2 bg-white h-100" style="width: fit-content; border: 2px solid #e5e50e;">
                <h6 class="font-titillium fw-800 text-yellow fs-18 mb-0 text-uppercase">Upcoming</h6>
                <div class="d-flex align-items-center">
                    <i class="fa-light fa-circle-exclamation text-yellow fs-16 mr-2"></i>
                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0">' . $abs_diff . ' ' . $day_text . '</p>
                </div>
            </div>
';
            } else {
                $cards .= '        
            <div class="rounded-10px  pl-2 pr-4 pt-2 bg-white h-100" style="width: fit-content; border: 2px solid #4EA833;">
                <h6 class="font-titillium fw-800 text-green fs-18 mb-0 text-uppercase">' . $q->contract_status . '</h6>
                <div class="d-flex align-items-center">
                    <i class="fa-light fa-circle-check text-green fs-16 mr-2"></i>
                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0">' . $abs_diff . ' ' . $day_text . '</p>
                </div>
            </div>
';
            }
        }
        if ($q->contract_status == 'Inactive') {
            $cards .= '        
            <div class="rounded-10px  pl-2 pr-4 pt-2 bg-white h-100" style="width: fit-content; border: 2px solid #0070C0;">
                <h6 class="font-titillium fw-800 text-darkblue fs-18 mb-0 text-uppercase">Renewed</h6>
                <div class="d-flex align-items-center">
                    <i class="fa-light fa-rotate text-darkblue fs-16 mr-2"></i>
                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0">'.date('Y-m-d',strtotime($q->renewed_on)).'</p>
                </div>
            </div>
';
        }
        if ($q->contract_status == 'Expired/Ended') {
            $cards .= '        
            <div class="rounded-10px  pl-2 pr-4 pt-2 bg-white h-100" style="width: fit-content; border: 2px solid #C41E3A;">
                <h6 class="font-titillium fw-800 text-red fs-18 mb-0 text-uppercase">Ended</h6>
                <div class="d-flex align-items-center">
                    <i class="fa-light fa-octagon-exclamation text-red fs-16 mr-2"></i>
                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0">' . $abs_diff . ' ' . $day_text . ' ago</p>
                </div>
            </div>
';
        }
        if ($q->contract_status == 'Expired') {
            $cards .= '        
            <div class="rounded-10px  pl-2 pr-4 pt-2 bg-white h-100" style="width: fit-content; border: 2px solid orange;">
                <h6 class="font-titillium fw-800 text-orange fs-18 mb-0 text-uppercase">Expired</h6>
                <div class="d-flex align-items-center">
                    <i class="fa-light fa-diamond-exclamation text-orange fs-16 mr-2"></i>
                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0">' . $abs_diff . ' ' . $day_text . '</p>
                </div>
            </div>
';
        }
        $cards .= '        
        </div>
        <div class="mr-3 pl-0 info-card">       
            <div class="short-cards rounded-10px  pl-2 pr-2 pt-1 bg-white border d-flex align-items-center h-100 position-relative" style="width: fit-content;">
                <img src="' . ('public/client_logos/' . $q->logo) . '" width="40px" class="rounded-circle">
                <div class="ml-2 w-100">
                    <h6 class="font-titillium fw-800 text-darkblue fs-16 mb-0 short-card-header text-uppercase">' . $q->client_display_name . '</h6>
                    <div class="d-flex justify-content-between align-items-center" style=style=style="width: 190px; max-width: 190px;">
                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0 short-card-details">' . $q->site_name . '</p>
                    <a href="javascript:void()" data-text="' . $q->site_name . '" class="copy-info" style="width: 15%;display: none;"><i class="fa-thin fa-copy fs-12" style="color: #263050;"></i></a>
                    </div>
                </div>
                <div class="clipboard-toast"><div class="d-flex align-items-center"><i class="fa-light fa-circle-check mr-2"></i><span class="font-titillium fs-14 text-darkgrey">Copied to clipboard!</span></div></div>
            </div>        
        </div>
        <div class="mr-3 pl-0 info-card">       
            <div class="short-cards rounded-10px  pl-2 pr-2 pt-1 bg-white border d-flex align-items-center h-100 position-relative">
                <img src="' . ('public/vendor_logos/' . $q->vendor_image) . '" width="40px" class="rounded-circle">
                <div class="ml-2 w-100">
                    <h6 class="font-titillium fw-800 text-darkblue fs-16 mb-0 short-card-header text-uppercase">' . $q->vendor_name . '</h6>
                    <div class="d-flex justify-content-between align-items-center"     max-width: 190px    max-width: 190pxstyle="width: 190px; max-width: 190px;">
                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0 short-card-details">' . $q->contract_no . '</p>
                    <a href="javascript:void()" data-text="' . $q->contract_no . '" class="copy-info" style="width: 15%;display: none;"><i class="fa-thin fa-copy fs-12" style="color: #263050;"></i></a>
                    </div>
                </div>
                <div class="clipboard-toast"><div class="d-flex align-items-center"><i class="fa-light fa-circle-check mr-2"></i><span class="font-titillium fs-14 text-darkgrey">Copied to clipboard!</span></div></div>
            </div>        
        </div>
        
        <div class=" pl-0 info-card mr-3">       
            <div class="short-cards rounded-10px  pl-2 pr-2 pt-1 bg-white border d-flex align-items-center h-100 position-relative">
                <i class="fa-light fa-calendar-range text-darkblue fs-35"></i>
                <div class="ml-2 w-100">
                    <h6 class="font-titillium fw-800 text-darkblue fs-16 mb-0 text-uppercase">Validity</h6>
                    <div class="d-flex justify-content-between align-items-center" style="width: 190px; max-width: 190px;">
                    <p class="font-titillium fw-300 text-darkgrey fs-12 mb-0 short-card-details validity">' . $contract_s_date . ' to ' . $contract_e_date . '</p>
                    <a href="javascript:void()" data-text="' . $contract_s_date . ' to ' . $contract_e_date . '" class="copy-info" style="width: 15%;display: none;"><i class="fa-thin fa-copy fs-12" style="color: #263050;"></i></a>
                    </div>
                </div>
                <div class="clipboard-toast"><div class="d-flex align-items-center"><i class="fa-light fa-circle-check mr-2"></i><span class="font-titillium fs-14 text-darkgrey">Copied to clipboard!</span></div></div>
            </div>        
        </div>
        </div>
    </div>
</div>
';
        $today = Carbon::today();
        $endDate_ = Carbon::parse($q->contract_end_date);
        $daysLeft_ = $today->diffInDays($endDate_, false);
        if (count($message) > 0 || ($daysLeft_ >= 0 && $daysLeft_ <= 50 && $q->contract_status == 'Active') || ($daysLeft_ < 0 || $q->contract_status === 'Expired/Ended')) {
            $cards .= '<div class="container" style="padding-right: 10px;">';

            // Upcoming (≤ 50 days remaining)
            if ($daysLeft_ >= 0 && $daysLeft_ <= 30 && $q->contract_status == 'Active') {
                $cards .= '<div class="align-items-center container d-flex mb-0 px-2 py-1 mt-2 message-banner-upcoming">
                    <i class="fa-solid fa-circle-exclamation text-black fs-16 mr-3"></i>
                    <p class="font-titillium fs-16 fw-500 mb-0 text-black">
                    Contract will expire in ' . $daysLeft_ . ' days. Please Renew before end date to avoid service interruptions.
                    </p>
                  </div>';
            }

            // Expired
            if ($daysLeft_ < 0 && $q->contract_status === 'Expired/Ended') {
                $cards .= '<div class="align-items-center container d-flex mb-0 px-2 py-1 mt-2 message-banner-expired">
                    <i class="fa-solid fa-diamond-exclamation text-black fs-16 mr-3"></i>
                    <p class="font-titillium fs-16 fw-500 mb-0 text-black">
                    Contract Expired on ' . $endDate_->format('d-M-Y') . '. Please Renew or End this contract to finalize its status.
                    </p>
                  </div>';
            }
            foreach ($message as $msg) {
                if ($msg->status == "renewed") {
                    $cards .= '<div class="align-items-center container d-flex mb-0 px-2 py-1 mt-2 text-white message-banner-renew">
            <i class="fa-solid fa-circle-info text-white fs-16 mr-3"></i>
            <p class="font-titillium fs-16 fw-500 mb-0 text-white">' . $msg->message . '</p></div>';
                } elseif ($msg->status == "ended") {
                    $cards .= '<div class="align-items-center container d-flex mb-0 px-2 py-1 mt-2 text-white message-banner-ended">
            <i class="fa-solid fa-octagon-exclamation text-white fs-16 mr-3"></i>
            <p class="font-titillium fs-16 fw-500 mb-0 text-white">' . $msg->message . '</p></div>';
                } else {
                    $cards .= '<div class="align-items-center container d-flex mb-0 px-2 py-1 mt-2 text-darkgrey message-banner">
            <i class="fa-solid fa-message-exclamation fs-16 mr-3"></i>
            <p class="font-titillium fs-16 fw-500 mb-0 text-darkgrey">' . $msg->message . '</p></div>';
                }
            }
            $cards .= '</div>';
        }

        return response()->json([
            'cards' => $cards,
            'content' => $html,
            'header_img' => $headerImg,
            'header_text' => $headerText,
            'header_sub_text' => $headerSubText,
            'contract_no' => $contract_no,
            'header_desc' => $header_desc,
            'editUrl' => $editUrl,
            'id' => $q->id,
            'cloneUrl' => $cloneUrl,
            'pdfUrl' => $pdfUrl,
            'iconHtml' => $iconHtml,
        ]);
    }

    public function UpdateCommentContract(Request $request)
    {
        try {
            $contract_id = $request->contract_id;
            $comment_id = $request->comment_id;
            $comment = $request->comment_text;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('contract_comments')
                ->where('id', $comment_id)
                ->where('is_deleted', 0)
                ->where('contract_id', $contract_id)
                ->update([
                    'comment' => $comment,
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            DB::table('contract_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Comment Updated | ' . $comment_id,
                'contract_id' => $contract_id,
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

    public function DeleteCommentContract(Request $request)
    {
        try {
            $contract_id = $request->contract_id;
            $comment_id = $request->comment_id;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('contract_comments')
                ->where('id', $comment_id)
                ->where('is_deleted', 0)
                ->where('contract_id', $contract_id)
                ->update([
                    'is_deleted' => 1
                ]);

            DB::table('contract_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Comment Deleted | ' . $comment_id,
                'contract_id' => $contract_id,
            ]);

            return response()->json([
                'status' => 'success',
                'comment_id' => $comment_id,
                'contract_id' => $contract_id,
                'message' => 'Comment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // public function checkCloseToExpireContracts() {
    //     $contracts = DB::table('contracts')->where('is_deleted', 0)->get(); // i have contract_end_date column eg "2031-12-26"
    // }

    public function checkCloseToExpireContracts()
    {
        $today = Carbon::today();

        $contracts = DB::table('contracts')
            ->where('is_deleted', 0)
            ->whereNotIn('contract_status', ['Ended'])
            ->get();

        foreach ($contracts as $contract) {

            $endDate = Carbon::parse($contract->contract_end_date);
            $daysLeft = $today->diffInDays($endDate, false); // negative if expired

            /**
             * 1️⃣ EXPIRED CONTRACT
             */
            if ($daysLeft < 0 && $contract->contract_status === 'Expired/Ended') {

                $alreadyExists = DB::table('pinned_messages')
                    ->where('linked_id', $contract->id)
                    ->where('page', 'contract')
                    ->where('status', 'expired')
                    ->exists();

                if (!$alreadyExists) {

                    $msg = "Contract Expired on " . $endDate->format('d-M-Y') . ". Please Renew or End this contract to finalize its status.";

                    DB::table('pinned_messages')->insert([
                        'message' => $msg,
                        'linked_id' => $contract->id,
                        'page' => 'contract',
                        'is_deleteable' => 0,
                        'status' => 'expired',
                        'created_at' => now(),
                    ]);
                }
            }

            /**
             * 2️⃣ UPCOMING CONTRACT (≤ 50 days)
             */
            if ($daysLeft >= 0 && $daysLeft <= 50 && $contract->contract_status == 'Active') {

                $alreadyExists = DB::table('pinned_messages')
                    ->where('linked_id', $contract->id)
                    ->where('page', 'contract')
                    ->where('status', 'upcoming')
                    ->exists();

                if (!$alreadyExists) {

                    $msg = "Contract will expire in {$daysLeft} days. Please Renew before end date to avoid service interruptions.";

                    DB::table('pinned_messages')->insert([
                        'message' => $msg,
                        'linked_id' => $contract->id,
                        'page' => 'contract',
                        'is_deleteable' => 0,
                        'status' => 'upcoming',
                        'created_at' => now(),
                    ]);
                }
            }
        }
    }


    public function UndoDeleteCommentContract(Request $request)
    {
        try {
            $contract_id = $request->contract_id;
            $comment_id = $request->comment_id;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('contract_comments')
                ->where('id', $comment_id)
                ->where('is_deleted', 1)
                ->where('contract_id', $contract_id)
                ->update([
                    'is_deleted' => 0
                ]);

            DB::table('contract_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Comment Recovered | ' . $comment_id,
                'contract_id' => $contract_id,
            ]);

            return response()->json([
                'status' => 'success',
                'comment_id' => $comment_id,
                'contract_id' => $contract_id,
                'message' => 'Comment recovered successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function delete_attachment_contract(Request $request)
    {
        try {
            $contract_id = $request->contract_id;
            $attachment_id = $request->attachment_id;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('contract_attachments')
                ->where('id', $attachment_id)
                ->where('is_deleted', 0)
                ->where('contract_id', $contract_id)
                ->update([
                    'is_deleted' => 1
                ]);

            DB::table('contract_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Attachment Deleted | ' . $attachment_id,
                'contract_id' => $contract_id,
            ]);

            return response()->json([
                'status' => 'success',
                'attachment_id' => $attachment_id,
                'contract_id' => $contract_id,
                'message' => 'Attachment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function undo_delete_attachment_contract(Request $request)
    {
        try {
            $contract_id = $request->contract_id;
            $attachment_id = $request->attachment_id;

            if (Auth::user()->role == 'read') {
                return response()->json(['status' => 'error', 'message' => 'You don’t have access.']);
            }

            DB::table('contract_attachments')
                ->where('id', $attachment_id)
                ->where('is_deleted', 1)
                ->where('contract_id', $contract_id)
                ->update([
                    'is_deleted' => 0
                ]);

            DB::table('contract_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Attachment Recovered | ' . $attachment_id,
                'contract_id' => $contract_id,
            ]);

            return response()->json([
                'status' => 'success',
                'attachment_id' => $attachment_id,
                'contract_id' => $contract_id,
                'message' => 'Attachment recovered successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function storeManagedBy(Request $request)
    {
        $id = DB::table('contracts')->insertGetId([
            'managed_by_option' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'id' => $id,
            'name' => $request->name
        ]);
    }
    public function storeCurrency(Request $request)
    {
        $id = DB::table('contracts')->insertGetId([
            'currency_option' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'id' => $id,
            'name' => $request->name
        ]);
    }

    public function storePnNo(Request $request)
    {
        $id = DB::table('contracts')->insertGetId([
            'currency_option' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'id' => $id,
            'name' => $request->name
        ]);
    }

    public function getContractDistributor(Request $request)
    {
        $contract_id = $request->id;

        $data = DB::table('contract_distribution as cd')
            ->leftJoin('distributors as d', 'cd.distributor', '=', 'd.id')
            ->where('cd.contract_id', $contract_id)
            ->select(
                'cd.distributor as distributor_id',
                'd.distributor_name',
                'cd.reference_no',
                'cd.sales_order_no'
            )
            ->get();

        return response()->json($data);
    }

    public function getContractPurchasing(Request $request)
    {
        $contract_id = $request->id;

        $data = DB::table('contract_purchasing')
            ->where('contract_id', $contract_id)
            ->select(
                'estimate_no',
                'sales_order_no',
                'invoice_no',
                'invoice_date',
                'po_no',
                'po_date'
            )
            ->get();

        return response()->json($data);
    }
    function add_notify_email(Request $request)
    {
        $contract_id = $request->contract_id;
        $email = $request->email;

        DB::table('contract_emails')->insert([
            'contract_id' => $contract_id,
            'renewal_email' => $email,
            'added_by' => Auth::user()->id,
            'created_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Email added successfully.',
        ]);
    }

    function remove_notify_email(Request $request)
    {
        $contract_id = $request->contract_id;
        $email = $request->email;

        DB::table('contract_emails')->where('contract_id', $contract_id)
            ->where('renewal_email', $email)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Email deleted successfully.',
        ]);
    }
    function add_pinned_message(Request $request)
    {
        $contract_id = $request->contract_id;
        $messages = $request->messages;

        // Delete old pinned messages
        DB::table('pinned_messages')
            ->where('linked_id', $contract_id)
            ->where('page', 'contract')
            ->where('is_deleteable', 1)
            ->delete();

        // Insert new messages
        if (!empty($messages)) {
            foreach ($messages as $msg) {
                DB::table('pinned_messages')->insert([
                    'linked_id' => $contract_id,
                    'page' => 'contract',
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

    function remove_pinned_message(Request $request)
    {
        $contract_id = $request->contract_id;
        $message = $request->message;

        DB::table('pinned_messages')
            ->where('linked_id', $contract_id)
            ->where('page', 'contract')
            ->where('message', $message)
            ->where('is_deleteable', 1)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pinned message deleted successfully.',
        ]);
    }

    public function sendRenewalNotification(Request $request)
    {
        $request->validate([
            'contract_id' => 'required',
            'emails' => 'required|array',
        ]);

        $contract = DB::table('contracts as c')->where('c.id', $request->contract_id)
            ->select('c.*', 'v.vendor_name as vendor_name', 'cl.company_name as client_name')
            ->leftJoin('vendors as v', function ($join) {
                $join->on('c.vendor_id', '=', 'v.id');
            })
            ->leftJoin('clients as cl', function ($join) {
                $join->on('c.client_id', '=', 'c.id');
            })
            ->first();
        // dd($contract);
        if (!$contract) {
            return response()->json(['status' => 'error', 'message' => 'Contract not found']);
        }

        $daysRemaining = now()->diffInDays($contract->contract_end_date);


        foreach ($request->emails as $email) {
            Mail::to($email)->send(new \App\Mail\ContractRenewalMail(
                $contract,
                $daysRemaining
            ));
        }

        return response()->json(['status' => 'success']);
    }

    
}
