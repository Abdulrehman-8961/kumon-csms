<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Auth;
use DB;
use Mail;
use Hash;
use PDF;
 
  use Excel;
  
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
use Cache;

 use Validator;
class MainController extends Controller
{
    //
public function __construct(){ 

}
   
    public function Clients(){
    
     return view('clients');
 
    }
    public function AddClients(){
 
     return view('AddClients');
 
    }
 
    public function EditClients(){
 

     return view('EditClients');
 
    }
       public function Notifications(){
 

     return view('Notifications');
 
    }

    public function Settings(){
 

     return view('Settings');
 
    }
  public function UpdateSettings(Request $request){
 
     
        DB::Table('notification_settings')->update(['interval_1'=>$request->interval_1,'interval_2'=>$request->interval_2,'interval_3'=>$request->interval_3,'interval_4'=>$request->interval_4,'interval_5'=>$request->interval_5,'interval_6'=>$request->interval_6,'interval_7'=>$request->interval_7,'from_name'=>$request->from_name]);
      return redirect()->back()->with('success','Settings Updated Successfully');
           

    }
     public function ExportPrintClients(){
 

     return view('exports/ExportPrintClients');
 
    }

    public function ExportPdfClients(){
 

    $pdf = PDF::loadView('exports/ExportPdfClients');
   
    return $pdf->stream('Clients.pdf');

 
    }
     
    
    
    public function DeleteClients(Request $request){

                        
          DB::Table('clients')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Client Deleted Successfully');
           

    }
    public function ShowClients(Request $request){

                        
          $qry=DB::Table('clients')->where('id',$request->id)->first();
          return response()->json($qry);
           

    }
        public function getVlanId(Request $request){

                        
          $qry=DB::Table('network')->where('client_id',$request->client_id)->where('site_id',$request->site_id)->where('is_deleted',0)->orderby('vlan_id','asc')->get();
          return response()->json($qry);
           

    }

       public function getVlanIdAll(Request $request){

                        
          $qry=DB::Table('network')->where('client_id',$request->client_id)->whereIn('site_id',$request->site_id)->where('is_deleted',0)->orderby('vlan_id','asc')->get();
          return response()->json($qry);
 
    }
       
       public function getVlanIdInfo(Request $request){

                        
          $qry=DB::Table('network')->where('id',$request->id)->first();
          return response()->json($qry);
           

    }


        public function ExportExcelClients(Request $request) 
        {
          
            return Excel::download(new ExportClients($request), 'Clients.xlsx');
        } 
   public function CheckUniqueNetwork(Request $request){

    if(isset($request->id)){
   $qry=DB::Table('network')->where('client_id',$request->client_id)->where('id','!=',$request->id)->where('site_id',$request->site_id)->where('vlan_id',$request->vlan_id)->count();
    }   
    else{


          $qry=DB::Table('network')->where('client_id',$request->client_id)->where('site_id',$request->site_id)->where('vlan_id',$request->vlan_id)->count();
    }
          return response()->json($qry);
           

    }



     public function InsertClients(Request $request){
          $image='';

                    if($request->logo!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('logo')->getClientOriginalExtension();
                               $request->file('logo')->move(public_path('client_logos'), $image);  
                    }

                     $data=array(
                                'salutation'=>$request->salutation,
                                'firstname'=>$request->firstname,
                                'lastname'=>$request->lastname,
                                'client_display_name'=>$request->client_display_name,
                                'company_name'=>$request->company_name,
                                'email_address'=>$request->email,
                                'client_address'=>$request->client_address,
                                'work_phone'=>$request->work_phone,
                                'client_status'=>$request->client_status,
                                'mobile'=>$request->mobile,
                                'website'=>$request->website,
                                'logo'=>$image,
                                'renewal_notification'=>$request->renewal_notification,
                                'renewal_notification_email'=>$request->notification_renewal_email_base,
                                'created_by'=>Auth::id(),

                        );
                     DB::Table('clients')->insert($data);
                     $last_id=DB::getPdo()->lastInsertId();

                     if(isset($request->notification_renewal_email)){
                            foreach($request->notification_renewal_email as $r){
                                        if($r!=''){
                                    DB::table('client_emails')->insert([
                                            'client_id'=>$last_id,
                                            'renewal_email'=>$r,
                                    ]);
                                }

                            }
                              }
 

 if(isset($request->ssl_certificate_email)){
                            foreach($request->ssl_certificate_email as $r){
                                        if($r!=''){
                                    DB::table('client_ssl_emails')->insert([
                                            'client_id'=>$last_id,
                                            'renewal_email'=>$r,
                                    ]);
                                }

                            }
                              }

if(isset($request->saveAndClose)){
     return redirect('clients')->with('success','Clients Added Successfully');
}

    return redirect()->back()->with('success','Clients Added Successfully');

    
 
    }

    public function UpdateClients(Request $request){
          $image='';
                    if($request->logo!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('logo')->getClientOriginalExtension();
                               $request->file('logo')->move(public_path('client_logos'), $image);  
                    }
                    else{
                     $image=$request->hidden_img;   
                    }


                     $data=array(
                                'salutation'=>$request->salutation,
                                'firstname'=>$request->firstname,
                                'lastname'=>$request->lastname,
                                'client_display_name'=>$request->client_display_name,
                                'company_name'=>$request->company_name,
                                'client_address'=>$request->client_address,
                                'email_address'=>$request->email,
                                'work_phone'=>$request->work_phone,
                                'mobile'=>$request->mobile,
                                'website'=>$request->website,
                                'client_status'=>$request->client_status,
                                
                                'logo'=>$image,
                                'renewal_notification'=>$request->renewal_notification,

                                'renewal_notification_email'=>$request->notification_renewal_email_base,
                                'updated_at'=>date('Y-m-d H:i:s'),
                                'updated_by'=>Auth::id(),
                        );
                     DB::Table('clients')->where('id',$request->id)->update($data);
                        DB::table('client_emails')->where('client_id',$request->id)->delete();
                     if(isset($request->notification_renewal_email)){
                      
                            foreach($request->notification_renewal_email as $r){
                                        if($r!=''){
                                    DB::table('client_emails')->insert([
                                            'client_id'=>$request->id,
                                            'renewal_email'=>$r,
                                    ]);
                                }

                            }
                      }

                        DB::table('client_ssl_emails')->where('client_id',$request->id)->delete();
                     if(isset($request->ssl_certificate_email)){
                      
                            foreach($request->ssl_certificate_email as $r){
                                        if($r!=''){
                                    DB::table('client_ssl_emails')->insert([
                                            'client_id'=>$request->id,
                                            'renewal_email'=>$r,
                                    ]);
                                }

                            }
                      }




if(isset($request->saveAndClose)){
     return redirect('clients')->with('success','Clients Updated Successfully');
}


    return redirect()->back()->with('success','Clients Updated Successfully');

    
 
    }

    
    
    
 





















    public function Vendors(){
 
     return view('vendors');
 
    }
    public function AddVendors(){
 
     return view('AddVendors');
 
    }
 
    public function EditVendors(){
 

     return view('EditVendors');
 
    }

     public function ExportPrintVendors(){
 

     return view('exports/ExportPrintVendors');
 
    }

    public function ExportPdfVendors(){
 

    $pdf = PDF::loadView('exports/ExportPdfVendors');
   
    return $pdf->stream('Vendors.pdf');

 
    }
     
    
    
    public function DeleteVendors(Request $request){

                        
          DB::Table('vendors')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Vendors Deleted Successfully');
           

    }
    public function ShowVendors(Request $request){

                        
          $qry=DB::Table('vendors')->where('id',$request->id)->first();
          return response()->json($qry);
           

    }
      
       


        public function ExportExcelVendors(Request $request) 
        {
          
            return Excel::download(new ExportVendors($request), 'Vendors.xlsx');
        } 


 public function ExportExcelNetwork(Request $request) 
        {
          
            return Excel::download(new ExportExcelNetwork($request), 'Network.xlsx');
        } 



     public function InsertVendors(Request $request){
          $image='';
                    if($request->vendor_image!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('vendor_image')->getClientOriginalExtension();
                               $request->file('vendor_image')->move(public_path('vendor_logos'), $image);  
                    }
                     $data=array(
                                'vendor_name'=>$request->vendor_name,
                                'vendor_image'=>$image, 
                                'created_by'=>Auth::id(),
                        );
                     DB::Table('vendors')->insert($data);
                     $last_id=DB::getPdo()->lastInsertId();
                      
 
    

if(isset($request->saveAndClose)){
     return redirect('vendors')->with('success','Vendors Added Successfully');
}


    return redirect()->back()->with('success','Vendors Added Successfully');

    
 
    }

    public function UpdateVendors(Request $request){
          $image='';
                    if($request->vendor_image!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('vendor_image')->getClientOriginalExtension();
                               $request->file('vendor_image')->move(public_path('vendor_logos'), $image);  
                    }
                    else{
                     $image=$request->hidden_img;   
                    }
                   $data=array(
                                'vendor_name'=>$request->vendor_name,
                                'vendor_image'=>$image, 
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              
                     DB::Table('vendors')->where('id',$request->id)->update($data);
                  
 

if(isset($request->saveAndClose)){
     return redirect('vendors')->with('success','Vendors Updated Successfully');
}


    return redirect()->back()->with('success','Vendors Updated Successfully');

    
 
    }

    
    
    



















    public function SLA(){
 
     return view('Sla');
 
    }
    public function AddSla(){
 
     return view('AddSla');
 
    }
 
    public function EditSla(){
 

     return view('EditSla');
 
    }
   
    public function DeleteSla(Request $request){

                        
          DB::Table('sla')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Sla Deleted Successfully');
           

    }
   public function InsertSla(Request $request){
          $image='';
                   
                     $data=array(
                                   'sla_description'=>$request->sla_description,
                                'tag_back_color'=>$request->tag_back_color, 
                                'tag_text_color'=>$request->tag_text_color, 
                                'created_by'=>Auth::id(),
                        );
                     DB::Table('sla')->insert($data);
                     $last_id=DB::getPdo()->lastInsertId();
                      
 
  return response()->json('success'); 
 
    }

    public function UpdateSla(Request $request){
       
                   $data=array(
                                'sla_description'=>$request->sla_description,
                                'tag_back_color'=>$request->tag_back_color, 
                                'tag_text_color'=>$request->tag_text_color, 
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              
                     DB::Table('sla')->where('id',$request->id)->update($data);
                  
 
  return response()->json('success');
           
 
    }

    







    public function NetworkZone(){
 
     return view('NetworkZone');
 
    }
    public function AddNetworkZone(){
 
     return view('AddNetworkZone');
 
    }
 
    public function EditNetworkZone(){
 

     return view('EditNetworkZone');
 
    }
   
    public function DeleteNetworkZone(Request $request){

                        
          DB::Table('network_zone')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Network Zone Deleted Successfully');
           

    }
   public function InsertNetworkZone(Request $request){
          $image='';
                   
                     $data=array(
                                   'network_zone_description'=>$request->network_zone_description,
                                'tag_back_color'=>$request->tag_back_color, 
                                'tag_text_color'=>$request->tag_text_color, 
                                'created_by'=>Auth::id(),
                        );
                     DB::Table('network_zone')->insert($data);
                     $last_id=DB::getPdo()->lastInsertId();
                      
 
  return response()->json('success'); 
 
    }

    public function UpdateNetworkZone(Request $request){
       
                   $data=array(
                                'network_zone_description'=>$request->network_zone_description,
                                'tag_back_color'=>$request->tag_back_color, 
                                'tag_text_color'=>$request->tag_text_color, 
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              
                     DB::Table('network_zone')->where('id',$request->id)->update($data);
                      

                      
 
  return response()->json('success');
           
 
    }

    
    
    










    public function Sites(){
 
     return view('sites');
 
    }
    public function AddSites(){
 
     return view('AddSites');
 
    }
 
    public function EditSites(){
 

     return view('EditSites');
 
    }

     public function ExportPrintSites(){
 

     return view('exports/ExportPrintSites');
 
    }

    public function ExportPdfSites(){
 

    $pdf = PDF::loadView('exports/ExportPdfSites');
   
    return $pdf->stream('Sites.pdf');

 
    }
     
    
    
    public function DeleteSites(Request $request){

                        
          DB::Table('sites')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Sites Deleted Successfully');
           

    }
    public function ShowSites(Request $request){

                        
          $qry=DB::Table('sites as s')->select('s.*','c.client_display_name')->join('clients as c','c.id','=','s.client_id')->where('s.id',$request->id)->first();
          return response()->json($qry);
           

    }
    
       


        public function ExportExcelSites(Request $request) 
        {
          
            return Excel::download(new ExportSites($request), 'Sites.xlsx');
        } 



     public function InsertSites(Request $request){
 
                     $data=array(
                                'client_id'=>$request->client_id,
                                'site_name'=>$request->site_name,
                                'country'=>$request->country,
                                'address'=>$request->address,
                                'city'=>$request->city,
                                'province'=>$request->province,
                                'zip_code'=>$request->zip,
                                'phone'=>$request->phone,
                                'created_by'=>Auth::id(),
                                'fax'=>$request->fax,

                            
                        );
                     DB::Table('sites')->insert($data); 
                      
   $id=DB::getPdo()->lastInsertId();
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('network_attachment/'.$a->attachment) );
                                             DB::table('site_attachments')->insert([
                                                 'site_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'attachment'=>$a->attachment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
  $commentArray= $request->commentArray;
                         if(isset($request->commentArray)){
                        foreach($commentArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('site_comments')->insert([
                                                 'site_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }


                  
    return response()->json('success');
    
 
    }

    

  public function getAttachmentSites(Request $request){
         $qry=DB::table('site_attachments')->where('site_id',$request->id)->get();
          return response()->json($qry);
     }

     public function getCommentsSites(Request $request){
         $qry=DB::table('site_comments')->where('site_id',$request->id)->get();
          return response()->json($qry);
     }
    public function UpdateSites(Request $request){
                
                   $data=array(
                                 'client_id'=>$request->client_id,
                                'site_name'=>$request->site_name,
                                'country'=>$request->country,
                                'address'=>$request->address,
                                'city'=>$request->city,
                                'province'=>$request->province,
                                'zip_code'=>$request->zip,
                                'phone'=>$request->phone,
                                 'fax'=>$request->fax,
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              
                     DB::Table('sites')->where('id',$request->id)->update($data);
                  
 
 
   DB::table('site_attachments')->where('site_id',$request->id)->delete();    
  DB::table('site_comments')->where('site_id',$request->id)->delete();    
      $id=$request->id;
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('network_attachment/'.$a->attachment) );
                                             DB::table('site_attachments')->insert([
                                                 'site_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'attachment'=>$a->attachment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
                  $commentArray= $request->commentArray;
                         if(isset($request->commentArray)){
                        foreach($commentArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('site_comments')->insert([
                                                 'site_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

return response()->json('success');    
    
 
    }

    
    
    



     public function uploadNetworkAttachment(Request $request){

             $attachment = $_FILES['attachment']['name'];
  $file_tmp = $_FILES['attachment']['tmp_name'];

       $fileExt = explode('.', $attachment);
    $fileActualExt = strtolower(end($fileExt));
        $key=$fileExt[0].uniqid().'.'.$fileActualExt; 

      $request->file('attachment')->move(public_path('temp_uploads'), $key);  

return response()->json($key);
        }




     public function LoadNetworkAttachment(Request $request){
        
        $request->header('Access-Control-Allow-Origin: *');

  // Allow the following methods to access this file
   $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');

  // Allow the following headers in preflight
   $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');

  // Allow the following headers in response
   $request->header('Access-Control-Expose-Headers: upload-offset');

  // Load our configuration for this server
 
 
 
   
    $uniqueFileID =$_GET["key"];
 
          $imagePointer = public_path("network_attachment/" .  $uniqueFileID);
        if(!file_exists('..temp_uploads/'.$uniqueFileID)){
             
                copy( public_path("network_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID ));
         }

  

 
      $imageName = $uniqueFileID;

  



 
      // if imageName was found in the DB, get file with imageName and return file object or blob
      $imagePointer = public_path("network_attachment/" . $uniqueFileID);
 
      
      $fileObject = null;
       
      if ($imageName!='' && file_exists($imagePointer)) {
     
        $fileObject = file_get_contents($imagePointer);
     
      }

 

    // trigger load local image
    $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];
 
    if ($fileBlob) {
      $imagePointer = public_path("network_attachment/" .  $imageName);
      $fileContextType = mime_content_type($imagePointer);
      $fileSize = filesize($imagePointer);

       $handle = fopen($imagePointer, 'r');
   if (!$handle) return false;
 $content = fread($handle, filesize($imagePointer));

    
           $response = Response::make($content);
       $response->header('Access-Control-Expose-Headers','Content-Disposition, Content-Length, X-Content-Transfer-Id');
       $response->header('Content-Type',$fileContextType);
       $response->header('Content-Length', $fileSize);
      $response->header('Content-Disposition', "inline; filename=$imageName");

 
         return $response;
       
    } else {
     http_response_code(500);
    }
 
  
}


             public function revertNetworkAttachment(Request $request){
                $key=str_replace('"',"", $request->key);

               unlink(public_path('temp_uploads/'.$key));
    
            echo json_encode(1);

             }
           










    public function Network(){
 
     return view('Network');
 
    }
    public function AddNetwork(){
 
     return view('AddNetwork');
 
    }
 
    public function EditNetwork(){
 

     return view('EditNetwork');
 
    }

  public function DeleteNetwork(Request $request){

                        
          DB::Table('network')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Network Deleted Successfully');
           

    }

     public function InsertNetwork(Request $request){
 
                     $data=array(
                                'client_id'=>$request->client_id,
                                'site_id'=>$request->site_id, 
                                'vlan_id'=>$request->vlan_id, 
                                'description'=>$request->description, 
                                'zone'=>$request->network_zone, 
                                'subnet_ip'=>$request->subnet_ip, 
                                'mask'=>$request->mask, 
                                'gateway_ip'=>$request->gateway_ip, 
                                 'ssid_name'=>$request->ssid_name,
                                 'internet_facing'=>$request->internet_facing, 
                                    'encryption'=>$request->encryption, 
                                       'sign_in_method'=>$request->sign_in_method, 
                                          'certificate'=>$request->certificate, 
                                             'wifi_enabled'=>$request->wifi_enabled, 
                                 'created_by'=>Auth::id(),
 
                            
                        );
                     DB::Table('network')->insert($data); 
                      
 
        $id=DB::getPdo()->lastInsertId();
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('network_attachment/'.$a->attachment) );
                                             DB::table('network_attachments')->insert([
                                                 'network_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'attachment'=>$a->attachment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
  $commentArray= $request->commentArray;
                         if(isset($request->commentArray)){
                        foreach($commentArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('network_comments')->insert([
                                                 'network_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }


                  
    return response()->json('success');

    
 
    }

    public function UpdateNetwork(Request $request){
                
                   $data=array(
                                'client_id'=>$request->client_id,
                                'site_id'=>$request->site_id, 
                                'vlan_id'=>$request->vlan_id, 
                                'description'=>$request->description, 
                                'zone'=>$request->network_zone, 
                                'subnet_ip'=>$request->subnet_ip, 
                                'mask'=>$request->mask, 
                                'gateway_ip'=>$request->gateway_ip, 
                                 'ssid_name'=>$request->ssid_name, 
                                 'internet_facing'=>$request->internet_facing, 
 
                                  'encryption'=>$request->encryption, 
                                       'sign_in_method'=>$request->sign_in_method, 
                                          'certificate'=>$request->certificate, 
                                             'wifi_enabled'=>$request->wifi_enabled, 
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              

                     DB::Table('network')->where('id',$request->id)->update($data);
                  
 
      
 
   DB::table('network_attachments')->where('network_id',$request->id)->delete();    
                                    DB::table('network_comments')->where('network_id',$request->id)->delete();    
                                        $id=$request->id;
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('network_attachment/'.$a->attachment) );
                                             DB::table('network_attachments')->insert([
                                                 'network_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'attachment'=>$a->attachment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
  $commentArray= $request->commentArray;
                         if(isset($request->commentArray)){
                        foreach($commentArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('network_comments')->insert([
                                                 'network_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

return response()->json('success');    
 
    }

    
    
    


    public function Domains(){
 
     return view('domains');
 
    }
    public function AddDomains(){
 
     return view('AddDomains');
 
    }
 
    public function EditDomains(){
 

     return view('EditDomains');
 
    }

     public function ExportPrintDomains(){
 

     return view('exports/ExportPrintDomains');
 
    }
     public function PrintSite(){
 

     return view('exports/PrintSite');
 
    }
     public function PrintNetwork(){
 

     return view('exports/PrintNetwork');
 
    }


    public function ExportPdfDomains(){
 

    $pdf = PDF::loadView('exports/ExportPdfDomains');
   
    return $pdf->stream('Domains.pdf');

 
    }
     
    
    
    public function DeleteDomains(Request $request){

                        
          DB::Table('domains')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Domains Deleted Successfully');
           

    }
    public function ShowDomains(Request $request){

                        
          $qry=DB::Table('domains as s')->select('s.*','c.client_display_name')->join('clients as c','c.id','=','s.client_id')->where('s.id',$request->id)->first();
          return response()->json($qry);
           

    }
    
       


        public function ExportExcelDomains(Request $request) 
        {
          
            return Excel::download(new ExportDomains($request), 'Domains.xlsx');
        } 



     public function InsertDomains(Request $request){
 
                     $data=array(
                                'client_id'=>$request->client_id,
                                'domain_name'=>$request->domain_name, 

                            'created_by'=>Auth::id(),
                            
                        );
                     DB::Table('domains')->insert($data); 
                      
 
    if(isset($request->saveAndClose)){
     return redirect('domains')->with('success','Domain Added Successfully');
}

    return redirect()->back()->with('success','Domain Added Successfully');

    
 
    }

    public function UpdateDomains(Request $request){
                
                   $data=array(
                                 'client_id'=>$request->client_id,
                                'domain_name'=>$request->domain_name,
                                 
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              
                     DB::Table('domains')->where('id',$request->id)->update($data);
                  
 
    if(isset($request->saveAndClose)){
     return redirect('domains')->with('success','Domain Updated Successfully');
}



    return redirect()->back()->with('success','Domain Updated Successfully');

    
 
    }

    
      public function UpdateContractEmail(Request $request){
                    DB::Table('contracts')->where('client_id',$request->id)->update(['registered_email'=>$request->email]);
        return response()->json(1);
       }    
    













    public function AssetType(){
 
     return view('AssetType');
 
    }
    public function AddAssetType(){
 
     return view('AddAssetType');
 
    }
 
    public function EditAssetType(){
 

     return view('EditAssetType');
 
    }

     public function ExportPrintAssetType(){
 

     return view('exports/ExportPrintAssetType');
 
    }
 
    
    public function DeleteAssetType(Request $request){

                        
          DB::Table('asset_type')->where('asset_type_id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Asset Type Deleted Successfully');
           

    }
    public function ShowAssetType(Request $request){

                        
          $qry=DB::Table('asset_type')->where('id',$request->id)->first();
          return response()->json($qry);
           

    }
    
       


        public function ExportExcelAssetType(Request $request) 
        {
          
            return Excel::download(new ExportAssetType($request), 'AssetType.xlsx');
        } 



     public function InsertAssetType(Request $request){
          $image='';
                    if($request->asset_icon!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('asset_icon')->getClientOriginalExtension();
                               $request->file('asset_icon')->move(public_path('asset_icon'), $image);  
                    }
                     $data=array(
                                'asset_type_description'=>$request->asset_type_description,
                                'asset_icon'=>$image, 
                                'created_by'=>Auth::id(),
                        );
                     DB::Table('asset_type')->insert($data);
                     $last_id=DB::getPdo()->lastInsertId();
            

              if(isset($request->saveAndClose)){
     return redirect('asset-type')->with('success','Asset Type Added Successfully');
}

    return redirect()->back()->with('success','Asset Type Added Successfully');
 
    }

    public function UpdateAssetType(Request $request){
          $image='';
                    if($request->asset_icon!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('asset_icon')->getClientOriginalExtension();
                               $request->file('asset_icon')->move(public_path('asset_icon'), $image);  
                    }
                    else{
                     $image=$request->hidden_img;   
                    }
                   $data=array(
                                       'asset_type_description'=>$request->asset_type_description,
                                'asset_icon'=>$image, 
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              
                     DB::Table('asset_type')->where('asset_type_id',$request->id)->update($data);
                  
 
  if(isset($request->saveAndClose)){
     return redirect('asset-type')->with('success','Asset Type Updated Successfully');
}


    return redirect()->back()->with('success','Asset Type  Updated Successfully');

    
 
    }

    
    
    
 









    public function Distributors(){
 
     return view('distributors');
 
    }
    public function AddDistributors(){
 
     return view('AddDistributors');
 
    }
 
    public function EditDistributors(){
 

     return view('EditDistributors');
 
    }

     public function ExportPrintDistributors(){
 

     return view('exports/ExportPrintDistributors');
 
    }

    public function ExportPdfDistributors(){
 

    $pdf = PDF::loadView('exports/ExportPdfDistributors');
   
    return $pdf->stream('Distributors.pdf');

 
    }
     
    
    
    public function DeleteDistributors(Request $request){

                        
          DB::Table('distributors')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Distributors Deleted Successfully');
           

    }
    public function ShowDistributors(Request $request){

                        
          $qry=DB::Table('distributors')->where('id',$request->id)->first();
          return response()->json($qry);
           

    }
    
       


        public function ExportExcelDistributors(Request $request) 
        {
          
            return Excel::download(new ExportDistributors($request), 'Distributors.xlsx');
        } 



     public function InsertDistributors(Request $request){
          $image='';
                    if($request->distributor_image!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('distributor_image')->getClientOriginalExtension();
                               $request->file('distributor_image')->move(public_path('distributor_logos'), $image);  
                    }
                     $data=array(
                                'distributor_name'=>$request->distributor_name,
                                'distributor_image'=>$image, 
                                'created_by'=>Auth::id(),
                        );
                     DB::Table('distributors')->insert($data);
                     $last_id=DB::getPdo()->lastInsertId();
                      
 
      if(isset($request->saveAndClose)){
     return redirect('distributors')->with('success','Distributors Added Successfully');
}

    return redirect()->back()->with('success','Distributors Added Successfully');

    
 
    }

    public function UpdateDistributors(Request $request){
          $image='';
                    if($request->distributor_image!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('distributor_image')->getClientOriginalExtension();
                               $request->file('distributor_image')->move(public_path('distributor_logos'), $image);  
                    }
                    else{
                     $image=$request->hidden_img;   
                    }
                   $data=array(
                                'distributor_name'=>$request->distributor_name,
                                'distributor_image'=>$image, 
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              
                     DB::Table('distributors')->where('id',$request->id)->update($data);
                  
                    
 
      if(isset($request->saveAndClose)){
     return redirect('distributors')->with('success','Distributors Updated Successfully');
}


    return redirect()->back()->with('success','Distributor Updated Successfully');

    
 
    }

    
    
     
    
















    public function OperatingSystems(){
 
     return view('OperatingSystems');
 
    }
    public function AddOperatingSystems(){
 
     return view('AddOperatingSystems');
 
    }
 
    public function EditOperatingSystems(){
 

     return view('EditOperatingSystems');
 
    }

     public function ExportPrintOperatingSystems(){
 

     return view('exports/ExportPrintOperatingSystems');
 
    }

    public function ExportPdfOperatingSystems(){
 

    $pdf = PDF::loadView('exports/ExportPdfOperatingSystems');
   
    return $pdf->stream('OperatingSystems.pdf');

 
    }
     
    
    
    public function DeleteOperatingSystems(Request $request){

                        
          DB::Table('operating_systems')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Operating Systems Deleted Successfully');
           

    }
    public function ShowOperatingSystems(Request $request){

                        
          $qry=DB::Table('operating_systems')->where('id',$request->id)->first();
          return response()->json($qry);
           

    }
    
       


        public function ExportExcelOperatingSystems(Request $request) 
        {
          
            return Excel::download(new ExportOperatingSystems($request), 'OperatingSystems.xlsx');
        } 



     public function InsertOperatingSystems(Request $request){
          $image='';
                    if($request->operating_system_image!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('operating_system_image')->getClientOriginalExtension();
                               $request->file('operating_system_image')->move(public_path('operating_system_logos'), $image);  
                    }
                     $data=array(
                                'operating_system_name'=>$request->operating_system_name,
                                'operating_system_image'=>$image, 
                                'created_by'=>Auth::id(),
                        );
                     DB::Table('operating_systems')->insert($data);
                     $last_id=DB::getPdo()->lastInsertId();
                      
 
    

    return redirect()->back()->with('success','Operating Systems Added Successfully');

    
 
    }

    public function UpdateOperatingSystems(Request $request){
          $image='';
                    if($request->operating_system_image!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('operating_system_image')->getClientOriginalExtension();
                               $request->file('operating_system_image')->move(public_path('operating_system_logos'), $image);  
                    }
                    else{
                     $image=$request->hidden_img;   
                    }
                   $data=array(
                                'operating_system_name'=>$request->operating_system_name,
                                'operating_system_image'=>$image, 
                                'updated_by'=>Auth::id(),
                                 'updated_at'=>date('Y-m-d H:i:s'),
                        );

              
                     DB::Table('operating_systems')->where('id',$request->id)->update($data);
                  
 


    return redirect()->back()->with('success','Operating System Updated Successfully');

    
 
    }

    
    
    
 
    public function changeAssetColumns(Request $request){
        $type=$request->type;    
        $check=DB::table('settings')->where('user_id',Auth::id())->first();
        if($type=='') {             
                        if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['virtual_all_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'virtual_all_columns'=>implode(',',$request->array)]);
                        }

 


  }
 else if($type=='managed'){
 

    
    if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['virtual_managed_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'virtual_managed_columns'=>implode(',',$request->array)]);
                        }
 }
 else if($type=='support-contracts'){

       if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['virtual_support_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'virtual_support_columns'=>implode(',',$request->array)]);
                        }
 }
  else if($type=='ssl-certificate'){
 
               if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['virtual_ssl_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'virtual_ssl_columns'=>implode(',',$request->array)]);
                        }
 }
  else if($type=='inactive'){
 
    
               if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['virtual_inactive_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'virtual_inactive_columns'=>implode(',',$request->array)]);
                        }
 }

    }



   
    public function changeContractColumns(Request $request){
        $type=$request->type;    
        $check=DB::table('settings')->where('user_id',Auth::id())->first();
            if($type=='support') {             
                        if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['support_contract'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'support_contract'=>implode(',',$request->array)]);
                        }
 

  }
 else if($type=='subscription'){
 

    
    if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['subscription_contract'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'subscription_contract'=>implode(',',$request->array)]);
                        }
 }
       }

   public function changeExpiringColumns(Request $request){
        $type=$request->type;    
        $check=DB::table('settings')->where('user_id',Auth::id())->first();
                   
                        if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['expiring_contract'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'expiring_contract'=>implode(',',$request->array)]);
                        }
 

  
       }




        public function changePhysicalAssetColumns(Request $request){
        $type=$request->type;    
        $check=DB::table('settings')->where('user_id',Auth::id())->first();
       
        if($type=='') {             
                        if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['physical_all_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'physical_all_columns'=>implode(',',$request->array)]);
                        }
 

  }
 else if($type=='servers'){
 

    
    if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['physical_servers_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'physical_servers_columns'=>implode(',',$request->array)]);
                        }
 }
 else if($type=='other'){

       if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['physical_other_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'physical_other_columns'=>implode(',',$request->array)]);
                        }
 }
  else if($type=='managed'){
 
               if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['physical_managed_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'physical_managed_columns'=>implode(',',$request->array)]);
                        }
 }
  else if($type=='support-contracts'){
 
    
               if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['physical_support_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'physical_support_columns'=>implode(',',$request->array)]);
                        }
 }
   else if($type=='ssl-certificate'){
 
    
               if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['physical_ssl_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'physical_ssl_columns'=>implode(',',$request->array)]);
                        }
 }
   else if($type=='inactive'){
 
    
               if($check!=''){
                                DB::Table('settings')->where('user_id',Auth::id())->update(['physical_inactive_columns'=>implode(',',$request->array)]);
                        }
                        else{
                                DB::Table('settings')->insert(['user_id'=>Auth::id(),'physical_inactive_columns'=>implode(',',$request->array)]);
                        }
 }

    }



   
    public function Users(){
 
     return view('users');
 
    }
    public function AddUsers(){
 
     return view('AddUsers');
 
    }
 
    public function EditUsers(){
 

     return view('EditUsers');
 
    }

     public function ExportPrintUsers(){
 

     return view('exports/ExportPrintUsers');
 
    }

    public function ExportPdfUsers(){
 

    $pdf = PDF::loadView('exports/ExportPdfUsers');
   
    return $pdf->stream('Users.pdf');

 
    }
     
    
    
    public function DeleteUsers(Request $request){

                        
          DB::Table('users')->where('id',$request->id)->delete();
          return redirect()->back()->with('success','Users Deleted Successfully');
           

    }
    public function ShowUsers(Request $request){

                        
          $qry=DB::Table('users')->where('id',$request->id)->first();
          return response()->json($qry);
           

    }
    public function ShowUsersClients(Request $request){

                        
          $qry=DB::Table('users')->where('id',$request->id)->first();
            $arr=explode(',',$qry->access_to_client);
          $data=DB::Table('clients')->whereIn('id',$arr)->get();
          return response()->json($data);
           

    }
     
      public function UpdateUserPassword(Request $request){

                        
          DB::Table('users')->where('id',$request->id)->update(
                ['password'=>Hash::make($request->password),'password_verified'=>date('Y-m-d H:i:s')]
            );
          return redirect()->back()->with('success','Password Changed Successfully');
           

    }
       


        public function ExportExcelUsers(Request $request) 
        {
          
            return Excel::download(new ExportUsers($request), 'Users.xlsx');
        } 



     public function InsertUsers(Request $request){

        $access_to_client=  $request->access_to_client!=''?implode(',',$request->access_to_client) :'';

    $check=DB::table('users')->where('email',$request->email)->first();
   

            if($check!=''){
                            return redirect()->back()->withInput();     
            }
                    $password=uniqid();
           $data=array(
                                'salutation'=>$request->salutation,
                                'firstname'=>$request->firstname,
                                'lastname'=>$request->lastname,
                                'email'=>$request->email,
                                'password'=>Hash::make($password),
                                'mobile'=>$request->mobile,
                                'work_phone'=>$request->work_phone,
                                'role'=>$request->access_type,
                                'mobile'=>$request->mobile,
                                'portal_access'=>1,
                       
                                'access_to_client'=>$access_to_client,
                        
                                   'created_by'=>Auth::id(),
                        );



                          
                 $settings=DB::Table('notification_settings')->first();
    
     DB::Table('users')->insert($data);
                      
   $data = array( 'email' => $request->email, 'password' =>$password,'name'=>$request->firstname.' '.$request->lastname,'subject'=>'Access your Contracts and Assets','from_name'=>$settings->from_name);
              
 
 
  Mail::to($request->email)->send(new UserMail( $data));
      
 
 
if(isset($request->saveAndClose)){
     return redirect('users')->with('success','Users Added Successfully');
}


    return redirect()->back()->with('success','Users Added Successfully');

    
 
    }

    public function UpdateUsers(Request $request){
 


                 $access_to_client=  $request->access_to_client!=''?implode(',',$request->access_to_client) :'';
    $check=DB::table('users')->where('id','!=',$request->id)->where('email',$request->email)->first();
            if($check!=''){
                       return redirect()->back()->with('success','Email Already Exist'); 
            }
           $data=array(
                                'salutation'=>$request->salutation,
                                'firstname'=>$request->firstname,
                                'lastname'=>$request->lastname,
                                'email'=>$request->email,
 
                                'mobile'=>$request->mobile,
                                'work_phone'=>$request->work_phone,
                                'role'=>$request->access_type,
                                'mobile'=>$request->mobile,
                                'portal_access'=>$request->portal_access,
                       
                                'access_to_client'=>$access_to_client,
                           'updated_at'=>date('Y-m-d H:i:s'),
                           'updated_by'=>Auth::id(),

                        );

    $settings=DB::Table('notification_settings')->first();
  
  DB::Table('users')->where('id',$request->id)->update($data);
       
    
             
     return redirect()->back()->with('success','Users Updated Successfully');

    
 

    }

 

 

public function getSiteContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('sites as a')->select('a.*' ,'c.client_display_name','c.logo' ,'usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','c.logo' )->join('clients as c','c.id','=','a.client_id') ->leftjoin('users as usr','usr.id','=','a.created_by')->leftjoin('users as upd','upd.id','=','a.updated_by')->where('a.id',$id)->first();
   
                   
 
                           $html.='<div class="block card-round   bg-new-dark new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-network-segment-white.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Site</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' <a  target="_blank" href="pdf-site?id='.$q->id.'"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pdf" class=" ">
                                                <img src="public/img/action-white-pdf.png" width="25px">
                                            </a>
     <a  target="_blank" href="print-site?id='.$q->id.'"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-sites?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
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
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->client_display_name.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Site</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    <b>'.$q->site_name.'</b><br>
                                                    <span>'.$q->address.'</span><br>
                                                    <span>'.$q->city.','.$q->province.'</span><br>
                                                    <span>'.$q->zip_code.'</span><br>
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                                  <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Telephone</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-sec"><b>'.$q->phone.'</b></div> 
                                     
                                            </div>

                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-sec"><b>'.$q->fax.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                         
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                         

                                                      <img src="public/client_logos/'.$q->logo.'" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>

 

                                                     
                 
                 </div>
             </div>


         </div>';
   
 

   $contract=DB::table('site_comments')->where('site_id',$q->id) ->get();  
if(sizeof($contract)>0){
     $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Comments</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock"> ';
                                                                         foreach($contract as $c){
                                                             $html.='<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  '.nl2br($c->comment).'
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
                                    }
                                $html.='</div>

                            </div>';

}




 $contract=DB::table('site_attachments')->where('site_id',$q->id) ->get();  
 if(sizeof($contract)>0){
    $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Attachments</div>
                            
                                                          <div class="block-content new-block-content  px-4 row" id="attachmentBlock"> ';
                                                            foreach($contract as $c){

                                                                                $f=explode('.',$c->attachment);
                                                                            $fileExtension = end($f);
                                         $icon='attachment.png';
                                          if($fileExtension=='pdf'){
                                                $icon='attch-Icon-pdf.png';
                                            }
                                            else if($fileExtension=='doc' || $fileExtension=='docx'){
                                                $icon='attch-word.png';
                                            }
                                            else if($fileExtension=='txt'){
                                                $icon='attch-word.png';

                                            }
                                            else if($fileExtension=='csv' || $fileExtension=='xlsx' || $fileExtension=='xlsm' || $fileExtension=='xlsb' || $fileExtension=='xltx'){
                                                    $icon='attch-excel.png';
                                            }
                                            else if($fileExtension=='png'  || $fileExtension=='gif' || $fileExtension=='webp' || $fileExtension=='svg' ){
                                                $icon='attch-png icon.png';
                                            }
                                              else if(  $fileExtension=='jpeg' || $fileExtension=='jpg'  ){
                                                $icon='attch-jpg-icon.png';
                                            }
                                               else if(  $fileExtension=='potx' || $fileExtension=='pptx' || $fileExtension=='ppsx' || $fileExtension=='thmx'  ){
                                                $icon='attch-powerpoint.png';
                                            }
  
 

                                                        $html.='<div class="col-lg-4  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
</a></p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
                                }
                                $html.='</div>

                            </div>';
                        }


   $contract=DB::table('site_audit_trail as c')->select('c.*','u.firstname','u.lastname')->leftjoin('users as u','u.id','=','c.user_id')->where('c.site_id',$q->id)->get(); 
  
if(sizeof($contract)>0){
     $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Audit Trial</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock">'; 
                                                                        foreach($contract as $c){
                                                             $html.='<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">'.$c->firstname.' '.$c->lastname.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->created_at)).' at '.date('h:i:s A',strtotime($c->created_at)).' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  '.$c->description.'
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
}
                                $html.='</div>

                            </div>';
                        }




$html.='
    </div>


                    </div>



                </div>
               </div>
       </div>';



 
     return response()->json($html);
 
    }

 
public function getNetworkContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('network as a')->select('a.*','s.site_name','c.client_display_name','c.logo' ,'s.address','s.city','s.country','s.phone','s.zip_code','s.province' ,'usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','c.logo' )->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id') ->leftjoin('users as usr','usr.id','=','a.created_by')->leftjoin('users as upd','upd.id','=','a.updated_by')->where('a.id',$id)->first();
   
                   
 
                           $html.='<div class="block card-round   bg-new-dark new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-network-segment-white.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">  Network Segment</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' <a  target="_blank" href="pdf-network?id='.$q->id.'"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pdf" class=" ">
                                                <img src="public/img/action-white-pdf.png" width="25px">
                                            </a>
     <a  target="_blank" href="print-network?id='.$q->id.'"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-network?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
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
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->client_display_name.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Site</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    <b>'.$q->site_name.'</b><br>
                                                    <span>'.$q->address.'</span><br>
                                                    <span>'.$q->city.','.$q->province.'</span><br>
                                                    <span>'.$q->zip_code.'</span><br>
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                           
                                         
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                         

                                                      <img src="public/client_logos/'.$q->logo.'" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>




                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-red text-capitalize">Network
</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Network Zone</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-3">
                                            ';
                                                $net_zone=DB::Table('network_zone')->where('network_zone_description',$q->zone)->first(); 
                                            $html.='
                                      <div class=" text-center border-none   font-size-lg   bubble-white-new bubble-text-sec"  style="border: none;border-radius:13px;color:'.@$net_zone->tag_text_color.';background-color: '.@$net_zone->tag_back_color.'" ><b>'.$q->zone.'</b></div>
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">vLAN ID</div> 
                                       </div>
                                            <div class="col-sm-4">
                                                  <div class="bubble-white-new   bubble-text-first">
                                                   <b> '.$q->vlan_id.'</b>
                                                  
                                                </div> 
                                     
                                            </div>
                                            <div class="col-sm-3">
                                                                      <div class="contract_type_button  w-100 mr-4 mb-3">
  <input type="checkbox" class="custom-control-input" id="wifi_enabled" name="wifi_enabled" value="1" disabled '.($q->wifi_enabled==1?'checked':'').'>
  <label class="btn btn-new w-75 WifiDiv " for="wifi_enabled">'.($q->wifi_enabled==1?'Yes':'No').'</label>
</div>
</div>
                                          
                                        </div>
                                         
                                        
                                 
  
                                            <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Description</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->description.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

 
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                           
                                                      <img src="public/img/static-networking.png" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>



                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-blue text-capitalize">Wifi</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                    
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">SSID Name</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-first">
                                          <b>'.$q->ssid_name.'  </b>
                                                  
                                                </div> 
                                     
                                            </div>
      </div>
                                          <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Encryption</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          '.$q->encryption.'  
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                           <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Sign-On Method</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          '.$q->sign_in_method.'  
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                           <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Certificate</div> 
                                       </div>
                                           <div class="col-sm-3">
                                                                      <div class="contract_type_button  w-100 mr-4 mb-3">
  <input type="checkbox" class="custom-control-input" id="wifi_enabled" name="wifi_enabled" value="1" disabled '.($q->certificate==1?'checked':'').'>
  <label class="btn btn-new w-75 WifiDiv " for="wifi_enabled">'.($q->certificate==1?'Yes':'No').'</label>
</div>
</div>
                                          
                                        </div>
                                         
                                    </div>
                                    <div class="col-sm-2">
                                                <div class="bubble-white-new bubble-text-sec" style="padding:10px">
 

                                                      <img src="public/img/static-wifi.png" style="width: 100%;">
                                                </div> </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>








                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-green text-capitalize">Subnet</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
      <div class="col-sm-10">
        
                                    
                                            <div class="row">
                                          
    


                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">Subnet IP
</div> 
                                       </div>
                                            <div class="col-sm-4 form-group ">
                                                  <div class="bubble-white-new bubble-text-first">
                                                   '.$q->subnet_ip.' 
                                                  
                                                </div>
                                                 </div>
                                                  <div class="col-sm-1 form-group ">
                                                  <div class="bubble-white-new px-1 bubble-text-first">
                                                   '.$q->mask.' 
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                      
                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">Gateway IP</div> 
                                       </div>
                                            <div class="col-sm-4 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           
                                                       '.$q->gateway_ip.' 
                                                </div> 
                                     
                                            </div>
                                        

</div>
                                    </div>
                              
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/img/static-ip.png" style="width: 100%;">
                                                </div> 

                                    </div>';
                                         

                          $html.='</div>
                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>



                    

                                                     
                 
                 </div>
             </div>


         </div>';
   
 

   $contract=DB::table('network_comments')->where('network_id',$q->id) ->get();  
if(sizeof($contract)>0){
     $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Comments</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock"> ';
                                                                         foreach($contract as $c){
                                                             $html.='<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  '.nl2br($c->comment).'
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
                                    }
                                $html.='</div>

                            </div>';

}




 $contract=DB::table('network_attachments')->where('network_id',$q->id) ->get();  
 if(sizeof($contract)>0){
    $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Attachments</div>
                            
                                                          <div class="block-content new-block-content  px-4 row" id="attachmentBlock"> ';
                                                            foreach($contract as $c){

                                                                                $f=explode('.',$c->attachment);
                                                                            $fileExtension = end($f);
                                         $icon='attachment.png';
                                          if($fileExtension=='pdf'){
                                                $icon='attch-Icon-pdf.png';
                                            }
                                            else if($fileExtension=='doc' || $fileExtension=='docx'){
                                                $icon='attch-word.png';
                                            }
                                            else if($fileExtension=='txt'){
                                                $icon='attch-word.png';

                                            }
                                            else if($fileExtension=='csv' || $fileExtension=='xlsx' || $fileExtension=='xlsm' || $fileExtension=='xlsb' || $fileExtension=='xltx'){
                                                    $icon='attch-excel.png';
                                            }
                                            else if($fileExtension=='png'  || $fileExtension=='gif' || $fileExtension=='webp' || $fileExtension=='svg' ){
                                                $icon='attch-png icon.png';
                                            }
                                              else if(  $fileExtension=='jpeg' || $fileExtension=='jpg'  ){
                                                $icon='attch-jpg-icon.png';
                                            }
                                               else if(  $fileExtension=='potx' || $fileExtension=='pptx' || $fileExtension=='ppsx' || $fileExtension=='thmx'  ){
                                                $icon='attch-powerpoint.png';
                                            }
  
 

                                                        $html.='<div class="col-lg-4  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
</a></p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
                                }
                                $html.='</div>

                            </div>';
                        }


   $contract=DB::table('network_audit_trail as c')->select('c.*','u.firstname','u.lastname')->leftjoin('users as u','u.id','=','c.user_id')->where('c.network_id',$q->id)->get(); 
  
if(sizeof($contract)>0){
     $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Audit Trial</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock">'; 
                                                                        foreach($contract as $c){
                                                             $html.='<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">'.$c->firstname.' '.$c->lastname.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->created_at)).' at '.date('h:i:s A',strtotime($c->created_at)).' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  '.$c->description.'
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
}
                                $html.='</div>

                            </div>';
                        }




$html.='
    </div>


                    </div>



                </div>
               </div>
       </div>';



 
     return response()->json($html);
 
    }


    
}