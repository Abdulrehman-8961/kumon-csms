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
use App\Exports\ExportSystemCategory;
use App\Exports\ExportSystemTypes;
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
 
     
        DB::Table('notification_settings')->update(['interval_1'=>$request->interval_1,'interval_2'=>$request->interval_2,'interval_3'=>$request->interval_3,'interval_4'=>$request->interval_4,'interval_5'=>$request->interval_5,'interval_6'=>$request->interval_6,'interval_7'=>$request->interval_7,'from_name'=>$request->from_name,'asset_emails'=>$request->asset_emails]);
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

                        
          $qry=DB::Table('network')->select('network.*','nz.network_zone_description','nz.tag_back_color','nz.tag_text_color')->where('network.client_id',$request->client_id)->leftjoin('network_zone as nz','nz.network_zone_description','=','network.zone')->where('network.site_id',$request->site_id)->where('network.is_deleted',0)->orderby('network.vlan_id','asc')->get();
          return response()->json($qry);
           

    }

       public function getVlanIdAll(Request $request){

                        
       
              $qry=DB::Table('network')->select('network.*','nz.network_zone_description','nz.tag_back_color','nz.tag_text_color')->where('network.client_id',$request->client_id)->leftjoin('network_zone as nz','nz.network_zone_description','=','network.zone')->whereIn('network.site_id',$request->site_id)->where('network.is_deleted',0)->orderby('network.vlan_id','asc')->get();
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
                                'client_status'=>1,
                                'mobile'=>$request->mobile,
                                'website'=>$request->website,
                                'logo'=>$image,
                                'country'=>$request->country,
                                'zip'=>$request->zip,
                                'state'=>$request->state,
                                'city'=>$request->city,
                                'ssl_notification'=>$request->cert_notification ?? 0,
                                'renewal_notification'=>$request->contract_notification ?? 0,
                            
                                'created_by'=>Auth::id(),

                        );
                     DB::Table('clients')->insert($data);
                     $last_id=DB::getPdo()->lastInsertId();

                     if(isset($request->notification_renewal_email)){
                            foreach($request->notification_renewal_email as $r){
                                        if($r!=''){
                                             $r=json_decode($r);
                                    DB::table('client_emails')->insert([
                                            'client_id'=>$last_id,
                                            'renewal_email'=>$r->email,
                                    ]);
                                }

                            }
                              }
 

 if(isset($request->ssl_certificate_email)){
                            foreach($request->ssl_certificate_email as $r){
                                        if($r!=''){
                                             $r=json_decode($r);
                                    DB::table('client_ssl_emails')->insert([
                                            'client_id'=>$last_id,
                                            'renewal_email'=>$r->email,
                                    ]);
                                }

                            }
                              }






 
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('client_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('client_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('client_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Client added','client_id'=>$last_id]);
                  




 return response()->json('success');
    
    
 
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
                                
                                'logo'=>$image,
                             'country'=>$request->country,
                                'zip'=>$request->zip,
                                'state'=>$request->state,
                                'city'=>$request->city,
                                'ssl_notification'=>$request->cert_notification ?? 0,
                                'renewal_notification'=>$request->contract_notification ?? 0,
                            
                                'updated_at'=>date('Y-m-d H:i:s'),
                                'updated_by'=>Auth::id(),
                        );
                     DB::Table('clients')->where('id',$request->id)->update($data);
                        DB::table('client_emails')->where('client_id',$request->id)->delete();
                    
                        DB::table('client_ssl_emails')->where('client_id',$request->id)->delete();
                


 $last_id=$request->id;
                     if(isset($request->notification_renewal_email)){
                            foreach($request->notification_renewal_email as $r){
                                        if($r!=''){
                                             $r=json_decode($r);
                                    DB::table('client_emails')->insert([
                                            'client_id'=>$last_id,
                                            'renewal_email'=>$r->email,
                                    ]);
                                }

                            }
                              }
 

 if(isset($request->ssl_certificate_email)){
                            foreach($request->ssl_certificate_email as $r){
                                        if($r!=''){
                                             $r=json_decode($r);
                                    DB::table('client_ssl_emails')->insert([
                                            'client_id'=>$last_id,
                                            'renewal_email'=>$r->email,
                                    ]);
                                }

                            }
                              }





 
                        DB::table('client_attachments')->where('client_id',$request->id)->delete();
                  DB::table('client_comments')->where('client_id',$request->id)->delete();
                
 
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('client_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('client_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('client_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Client updated','client_id'=>$last_id]);
                  


 
 return response()->json('success');
    
    
    }

    
    
    
 

 



  public function getAttachmentClients(Request $request){
         $qry=DB::table('client_attachments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }

     public function getCommentsClients(Request $request){
         $qry=DB::table('client_comments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }


  public function getAttachmentUsers(Request $request){
         $qry=DB::table('user_attachments')->where('user_id',$request->id)->get();
          return response()->json($qry);
     }

     public function getCommentsUsers(Request $request){
         $qry=DB::table('user_comments')->where('user_id',$request->id)->get();
          return response()->json($qry);
     }


     public function getEmailClients(Request $request){
        $qry=DB::table('client_ssl_emails')->where('client_id',$request->id)->get();

           return response()->json($qry);
     }

     public function getEmailContractClients(Request $request){
     $qry=DB::table('client_emails')->where('client_id',$request->id)->get();

         return response()->json($qry);
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
                      
 
 
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('vendor_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('vendor_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('vendor_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Vendor added','client_id'=>$last_id]);

 return response()->json(1);
    
 
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
                  
 
 $last_id=$request->id;
    
        DB::table('vendor_attachments')->where('client_id',$request->id)->delete();
                  DB::table('vendor_comments')->where('client_id',$request->id)->delete();
                
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('vendor_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('vendor_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('vendor_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Vendor updated','client_id'=>$last_id]);
                  
             
     return response()->json('Users Updated Successfully');

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
 
   
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('sla_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('sla_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('sla_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Sla added','client_id'=>$last_id]);

return response()->json(1);
 
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
                  
  $last_id=$request->id;
    
        DB::table('sla_attachments')->where('client_id',$request->id)->delete();
                  DB::table('sla_comments')->where('client_id',$request->id)->delete();
                
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('sla_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('sla_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('sla_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Sla updated','client_id'=>$last_id]);
                  
             
     return response()->json('Users Updated Successfully');

 
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
 
   
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('network_zone_attachment/'.$a->attachment) );
                                             DB::table('network_zone_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('network_zone_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('network_zone_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Network Zone added','client_id'=>$last_id]);

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
                      

                      
  $last_id=$request->id;
    
        DB::table('network_zone_attachments')->where('client_id',$request->id)->delete();
                  DB::table('network_zone_comments')->where('client_id',$request->id)->delete();
                
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('network_zone_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('network_zone_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('network_zone_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Network Zone updated','client_id'=>$last_id]);
                  
             
     return response()->json('Users Updated Successfully');

 
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

DB::table('site_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Site added','site_id'=>$id]);
                  
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

     
 



 



 
  public function getCommentsVendors(Request $request){
         $qry=DB::table('vendor_comments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }

     public function getAttachmentVendors(Request $request){
         $qry=DB::table('vendor_attachments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }

  public function getCommentsDistributors(Request $request){
         $qry=DB::table('distributor_comments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }

     public function getAttachmentDistributors(Request $request){
         $qry=DB::table('distributor_attachments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }



     public function getCommentsDomains(Request $request){
         $qry=DB::table('domain_comments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }
  public function getAttachmentDomains(Request $request){
         $qry=DB::table('domain_attachments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }
     
     public function getCommentsSystemCatgory(Request $request){
         $qry=DB::table('system_category_comments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }
  public function getAttachmentSystemCategory(Request $request){
         $qry=DB::table('system_category_attachments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }
     
     public function getCommentsSystemTypes(Request $request){
         $qry=DB::table('system_type_comments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }
  public function getAttachmentSystemTypes(Request $request){
         $qry=DB::table('system_type_attachments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }

     public function getCommentsOperatingSystems(Request $request){
         $qry=DB::table('operating_system_comments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }
  public function getAttachmentOperatingSystems(Request $request){
         $qry=DB::table('operating_system_attachments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }
         public function getCommentsAssetType(Request $request){
         $qry=DB::table('asset_type_comments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }
  public function getAttachmentAssetType(Request $request){
         $qry=DB::table('asset_type_attachments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }

     public function getCommentsSla(Request $request){
         $qry=DB::table('sla_comments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }
  public function getAttachmentSla(Request $request){
         $qry=DB::table('sla_attachments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }

     public function getCommentsNetworkZone(Request $request){
         $qry=DB::table('network_zone_comments')->where('client_id',$request->id)->get();
          return response()->json($qry);
     }
  public function getAttachmentNetworkZone(Request $request){
         $qry=DB::table('network_zone_attachments')->where('client_id',$request->id)->get();
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

 DB::table('site_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Site updated','site_id'=>$id]);

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


  public function UpdateUserProfile(Request $request){

   $image='';
                    if($request->logo!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('logo')->getClientOriginalExtension();
                               $request->file('logo')->move(public_path('client_logos'), $image);  
                    }
                    DB::table('users')->where('id',Auth::id())->update(['user_image'=>$image]);
  return redirect()->back()->with('success','Profile update Successfully');
         
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

DB::table('network_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Network added' ,'network_id'=>$id]);
                  
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
DB::table('network_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Network updated','network_id'=>$id]);
                  
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
    
     public function SystemCategory(){
 
     return view('SystemCategory');
 
    }
    public function AddSystemCategory(){
 
     return view('AddSystemCategory');
 
    }
 
    public function EditSystemCategory(){
 

     return view('EditSystemCategory');
 
    }

     public function ExportPrintSystemCategory(){
 

     return view('exports/ExportPrintSystemCategory');
 
    }
    
    public function SystemTypes(){
 
     return view('SystemTypes');
 
    }
    public function AddSystemTypes(){
 
     return view('AddSystemTypes');
 
    }
 
    public function EditSystemTypes(){
 

     return view('EditSystemTypes');
 
    }

     public function ExportPrintSystemTypes(){
 

     return view('exports/ExportPrintSystemTypes');
 
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
    
     public function ExportPdfSystemCategory(){
 

    $pdf = PDF::loadView('exports/ExportSystemCategory');
   
    return $pdf->stream('SystemCategory.pdf');

 
    }
    
        public function ExportPdfSystemTypes(){
 

    $pdf = PDF::loadView('exports/ExportPdfSystemTypes');
   
    return $pdf->stream('SystemTypes.pdf');

 
    }
     
    
    
    public function DeleteDomains(Request $request){

                        
          DB::Table('domains')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','Domains Deleted Successfully');
           

    }
    
    public function DeleteSystemCategory(Request $request){

                        
          DB::Table('system_category')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','System Category Deleted Successfully');
           

    }
    
    public function DeleteSystemTypes(Request $request){

                        
          DB::Table('system_types')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
          return redirect()->back()->with('success','System types Deleted Successfully');
           

    }
    
    public function ShowDomains(Request $request){

                        
          $qry=DB::Table('domains as s')->select('s.*','c.client_display_name')->join('clients as c','c.id','=','s.client_id')->where('s.id',$request->id)->first();
          return response()->json($qry);
           

    }
    
      public function ShowSystemCategory(Request $request){

                        
          $qry=DB::Table('system_category as s')->select('s.*','c.client_display_name')->join('clients as c','c.id','=','s.client_id')->where('s.id',$request->id)->first();
          return response()->json($qry);

    }
    
    
    public function ShowSystemTypes(Request $request){

                        
          $qry=DB::Table('system_types as s')->select('s.*','c.client_display_name')->join('clients as c','c.id','=','s.client_id')->where('s.id',$request->id)->first();
          return response()->json($qry);
           

    }
    
       


        public function ExportExcelDomains(Request $request) 
        {
          
            return Excel::download(new ExportDomains($request), 'Domains.xlsx');
        } 
        
         public function ExportExcelSystemCategory(Request $request) 
        {
          
            return Excel::download(new ExportSystemCategory($request), 'SystemCategory.xlsx');
        } 
        
        public function ExportExcelSystemTypes(Request $request) 
        {
          
            return Excel::download(new ExportSystemTypes($request), 'SystemTypes.xlsx');
        } 



     public function InsertDomains(Request $request){
 
                     $data=array(
                                'client_id'=>$request->client_id,
                                'domain_name'=>$request->domain_name, 

                            'created_by'=>Auth::id(),
                            
                        );
                     DB::Table('domains')->insert($data); 
    $last_id=DB::getPdo()->lastInsertId();                  
 
   
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('domain_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('domain_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('domain_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Domain added','client_id'=>$last_id]);

return response()->json(1);
 
    }
    
     public function InsertSystemCategory(Request $request){
 
                     $data=array(
                                'client_id'=>$request->client_id,
                                'domain_name'=>$request->domain_name, 

                            'created_by'=>Auth::id(),
                            
                        );
                     DB::Table('system_category')->insert($data); 
    $last_id=DB::getPdo()->lastInsertId();                  
 
   
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('system_category_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('system_category_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('system_category_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'System Category added','client_id'=>$last_id]);

return response()->json(1);
 
    }
    
    public function InsertSystemTypes(Request $request){
 
                     $data=array(
                                'client_id'=>$request->client_id,
                                'domain_name'=>$request->domain_name, 

                            'created_by'=>Auth::id(),
                            
                        );
                     DB::Table('system_types')->insert($data); 
    $last_id=DB::getPdo()->lastInsertId();                  
 
   
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('system_type_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('system_type_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('system_type_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'System type added','client_id'=>$last_id]);

return response()->json(1);
 
    }

    public function UpdateDomains(Request $request){
                
                   $data=array(
                                 'client_id'=>$request->client_id,
                                'domain_name'=>$request->domain_name,
                                 
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              
                     DB::Table('domains')->where('id',$request->id)->update($data);
                  
 $last_id=$request->id;
    
        DB::table('domain_attachments')->where('client_id',$request->id)->delete();
                  DB::table('domain_comments')->where('client_id',$request->id)->delete();
                
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('domain_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('domain_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('domain_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Domain updated','client_id'=>$last_id]);
                  
             
     return response()->json('Users Updated Successfully');

    
 
    }
    
    
    
    
      public function UpdateSystemCategory(Request $request){
                
                   $data=array(
                                 'client_id'=>$request->client_id,
                                'domain_name'=>$request->domain_name,
                                 
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              
                     DB::Table('system_category')->where('id',$request->id)->update($data);
                  
 $last_id=$request->id;
    
        DB::table('system_category_attachments')->where('client_id',$request->id)->delete();
                  DB::table('system_category_comments')->where('client_id',$request->id)->delete();
                
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('system_category_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('system_category_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('system_category_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'System Category updated','client_id'=>$last_id]);
                  
             
     return response()->json('Users Updated Successfully');

    
 
    }
    
    
    public function UpdateSystemTypes(Request $request){
                
                   $data=array(
                                 'client_id'=>$request->client_id,
                                'domain_name'=>$request->domain_name,
                                 
                                 'updated_at'=>date('Y-m-d H:i:s'),
                                 'updated_by'=>Auth::id(),
                        );

              
                     DB::Table('system_types')->where('id',$request->id)->update($data);
                  
 $last_id=$request->id;
    
        DB::table('system_type_attachments')->where('client_id',$request->id)->delete();
                  DB::table('system_type_comments')->where('client_id',$request->id)->delete();
                
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('system_type_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('system_type_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('system_type_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'System type updated','client_id'=>$last_id]);
                  
             
     return response()->json('Users Updated Successfully');

    
 
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
 
   
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('asset_type_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('asset_type_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('asset_type_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Asset Type added','client_id'=>$last_id]);

return response()->json(1);
 
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
                  
  $last_id=$request->id;
    
        DB::table('asset_type_attachments')->where('client_id',$request->id)->delete();
                  DB::table('asset_type_comments')->where('client_id',$request->id)->delete();
                
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('asset_type_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('asset_type_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('asset_type_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Asset Type updated','client_id'=>$last_id]);
                  
             
     return response()->json('Users Updated Successfully');

 
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
                      
 
 
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('distributor_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('distributor_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('distributor_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Distributor added','client_id'=>$last_id]);

return response()->json(1);
 
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
                  
     $last_id=$request->id;
    
        DB::table('distributor_attachments')->where('client_id',$request->id)->delete();
                  DB::table('distributor_comments')->where('client_id',$request->id)->delete();
                
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('distributor_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('distributor_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('distributor_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Distributor updated','client_id'=>$last_id]);
                  
             
     return response()->json('Users Updated Successfully');

    
 
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
 
   
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('operating_system_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('operating_system_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('operating_system_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Operating System added','client_id'=>$last_id]);

return response()->json(1);
 
    
 
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
                  
 

  $last_id=$request->id;
    
        DB::table('operating_system_attachments')->where('client_id',$request->id)->delete();
                  DB::table('operating_system_comments')->where('client_id',$request->id)->delete();
                
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('operating_system_attachments')->insert([
                                                 'client_id'=>$last_id,
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
                             
                              
                                             DB::table('operating_system_comments')->insert([
                                                 'client_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('operating_system_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Operating System updated','client_id'=>$last_id]);
                  
             
     return response()->json('Users Updated Successfully');

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
                            return response()->json('Email Already Exist');     
            }
            $image='';
                    if($request->logo!=''){
                         $image = mt_rand(1,1000).''.time() . '.' . $request->file('logo')->getClientOriginalExtension();
                               $request->file('logo')->move(public_path('client_logos'), $image);  
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
                                'user_image'=>$image,
                                'portal_access'=>$request->portal_access,
                              
                                'access_to_client'=>$request->access_to_client!=''?implode(',',$request->access_to_client):'' ,
                        
                                   'created_by'=>Auth::id(),
                        );



                          
                 $settings=DB::Table('notification_settings')->first();
    
     DB::Table('users')->insert($data);
                 $last_id=DB::getPdo()->lastInsertId();     
   $data = array( 'email' => $request->email, 'password' =>$password,'name'=>$request->firstname.' '.$request->lastname,'subject'=>'Access your Contracts and Assets','from_name'=>$settings->from_name);
              
 
  


 
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('user_attachments')->insert([
                                                 'user_id'=>$last_id,
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
                             
                              
                                             DB::table('user_comments')->insert([
                                                 'user_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('user_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'User added','client_id'=>$last_id]);
                  





  Mail::to($request->email)->send(new UserMail( $data));
      
  
 return response()->json('success');

 
    }

    public function UpdateUsers(Request $request){
 


                 $access_to_client=  $request->access_to_client!=''?implode(',',$request->access_to_client) :'';
    $check=DB::table('users')->where('id','!=',$request->id)->where('email',$request->email)->first();
            if($check!=''){
                       return redirect()->back()->with('success','Email Already Exist'); 
            }

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
                                'email'=>$request->email,
 
                                'mobile'=>$request->mobile,
                                'work_phone'=>$request->work_phone,
                                'role'=>$request->access_type,
                                'mobile'=>$request->mobile,
 'user_image'=>$image,
                                'portal_access'=>$request->portal_access,
                              
                                'access_to_client'=>$request->access_to_client!=''?implode(',',$request->access_to_client):'' ,
                           'updated_at'=>date('Y-m-d H:i:s'),
                           'updated_by'=>Auth::id(),

                        );
 
  
  DB::Table('users')->where('id',$request->id)->update($data);
       $last_id=$request->id;
    
        DB::table('user_attachments')->where('user_id',$request->id)->delete();
                  DB::table('user_comments')->where('user_id',$request->id)->delete();
                
       $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('client_attachment/'.$a->attachment) );
                                             DB::table('user_attachments')->insert([
                                                 'user_id'=>$last_id,
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
                             
                              
                                             DB::table('user_comments')->insert([
                                                 'user_id'=>$last_id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('user_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'User updated','client_id'=>$last_id]);
                  
             
     return response()->json('Users Updated Successfully');

    
 

    }

 




public function getVendorContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('vendors as a') ->where('a.id',$id)->first();
   
             
                           $html.='<div class="block card-round   bg-new-green  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/white-vendor-icon.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Vendors</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-vendors?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

                                          
// $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->vendor_name.' </b></div> 
                                       </div>      </div>
                                            </div>
      <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                         
                                                        ';
                                                        if($q->vendor_image!=''){

                                                      $html.='<img src="public/vendor_logos/'.$q->vendor_image.'" style="width: 100%;">';
                                                      }else{
                                                            $html.='<img src="public/img/image-default.png" style="width: 100%;">';
                                                      }
                                                $html.='</div> 

                                    </div>

                                         </div>

                                         
                                         
                                      
 
                                           </div>       
                              
                                      
                                               </div>      

                         </div>
 </div>

             </div>


         </div>   ';

   $contract=DB::table('vendor_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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




 $contract=DB::table('vendor_attachments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('vendor_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
 






public function getDomainContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('domains as a')->select('a.*','c.client_display_name')->leftjoin('clients as c','c.id','=','a.client_id') ->where('a.id',$id)->first();
   
             
                           $html.='<div class="block card-round   bg-new-green  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-white-domains.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Domains</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-domains?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

   
                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                             <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Client</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->client_display_name.' </b></div> 
                                       </div>      </div>
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->domain_name.' </b></div> 
                                       </div>      </div>
                                       

                                            </div>
    
                                         </div>

                                         
                                         
                                      
 
                                           </div>       
                              
                                      
                                               </div>      

                         </div>
 </div>

             </div>


         </div>   ';

   $contract=DB::table('domain_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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




 $contract=DB::table('domain_attachments')->where('client_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('domain_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
    
    
    
    public function getSystemCategoryContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('system_category as a')->select('a.*','c.client_display_name')->leftjoin('clients as c','c.id','=','a.client_id') ->where('a.id',$id)->first();
   
             
                           $html.='<div class="block card-round   bg-new-green  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-white-domains.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">System Category</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-system-category?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

   
                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                             <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Client</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->client_display_name.' </b></div> 
                                       </div>      </div>
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->domain_name.' </b></div> 
                                       </div>      </div>
                                       

                                            </div>
    
                                         </div>

                                         
                                         
                                      
 
                                           </div>       
                              
                                      
                                               </div>      

                         </div>
 </div>

             </div>


         </div>   ';

   $contract=DB::table('system_category_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.(@$c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.@$c->user_image).'"> </b></h1>
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




 $contract=DB::table('system_category_attachments')->where('client_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.(@$c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.@$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('system_category_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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



 
     return response()->json($html) ;
 
    }
    
    
    
    public function getSystemTypeContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('system_types as a')->select('a.*','c.client_display_name')->leftjoin('clients as c','c.id','=','a.client_id') ->where('a.id',$id)->first();
   
             
                           $html.='<div class="block card-round   bg-new-green  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-white-domains.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">System Types</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-system-types?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

   
                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                             <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Client</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->client_display_name.' </b></div> 
                                       </div>      </div>
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->domain_name.' </b></div> 
                                       </div>      </div>
                                       

                                            </div>
    
                                         </div>

                                         
                                         
                                      
 
                                           </div>       
                              
                                      
                                               </div>      

                         </div>
 </div>

             </div>


         </div>   ';

   $contract=DB::table('system_type_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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




 $contract=DB::table('system_type_attachments')->where('client_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.(@$c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.@$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('system_type_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
 


public function getNetworkZoneContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('network_zone as a') ->where('a.id',$id)->first();
   
             
                           $html.='<div class="block card-round   bg-new-green  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-network-segment-white.png" width="50px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Network Zone</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-network-zone?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

   
                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                           
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->network_zone_description.' </b></div> 
                                       </div>      </div>
                                       

                                            </div>
    
                                         </div>

                                         
                                         
                                      
 
                                           </div>       
                              
                                      
                                               </div>      

                         </div>
 </div>

             </div>


         </div>   ';

   $contract=DB::table('network_zone_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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




 $contract=DB::table('network_zone_attachments')->where('client_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('network_zone_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
 





public function getSlaContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('sla as a') ->where('a.id',$id)->first();
   
             
                           $html.='<div class="block card-round   bg-new-green  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-white-sla.png" width="50px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">SLA</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-sla?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

   
                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                           
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->sla_description.' </b></div> 
                                       </div>      </div>
                                       

                                            </div>
    
                                         </div>

                                         
                                         
                                      
 
                                           </div>       
                              
                                      
                                               </div>      

                         </div>
 </div>

             </div>


         </div>   ';

   $contract=DB::table('sla_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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




 $contract=DB::table('sla_attachments')->where('client_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('sla_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
 



public function getDistributorContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('distributors as a') ->where('a.id',$id)->first();
   
             
                           $html.='<div class="block card-round   bg-new-green  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-white-distributor.png" width="50px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Distributor</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-distributors?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

                                          
// $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->distributor_name.' </b></div> 
                                       </div>      </div>
                                            </div>
      <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                         
                                                        ';
                                                        if($q->distributor_image!=''){

                                                      $html.='<img src="public/distributor_logos/'.$q->distributor_image.'" style="width: 100%;">';
                                                      }else{
                                                            $html.='<img src="public/img/image-default.png" style="width: 100%;">';
                                                      }
                                                $html.='</div> 

                                    </div>

                                         </div>

                                         
                                         
                                      
 
                                           </div>       
                              
                                      
                                               </div>      

                         </div>
 </div>

             </div>


         </div>   ';

   $contract=DB::table('distributor_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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




 $contract=DB::table('distributor_attachments')->where('client_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('distributor_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
 

 

public function getAssetTypeContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('asset_type as a') ->where('a.asset_type_id',$id)->first();
   
             
                           $html.='<div class="block card-round   bg-new-green  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-white-asset-type.png" width="50px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Asset Type</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-asset-type?id='.$q->asset_type_id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->asset_type_id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

                                          
// $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->asset_type_description.' </b></div> 
                                       </div>      </div>
                                            </div>
      <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                         
                                                        ';
                                                        if($q->asset_icon!=''){

                                                      $html.='<img src="public/asset_icon/'.$q->asset_icon.'" style="width: 100%;">';
                                                      }else{
                                                            $html.='<img src="public/img/image-default.png" style="width: 100%;">';
                                                      }
                                                $html.='</div> 

                                    </div>

                                         </div>

                                         
                                         
                                      
 
                                           </div>       
                              
                                      
                                               </div>      

                         </div>
 </div>

             </div>


         </div>   ';

   $contract=DB::table('asset_type_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->asset_type_id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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




 $contract=DB::table('asset_type_attachments')->where('client_id',$q->asset_type_id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('asset_type_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->asset_type_id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
 





public function getOperatingContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('operating_systems as a') ->where('a.id',$id)->first();
   
             
                           $html.='<div class="block card-round   bg-new-green  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-white-os.png" width="50px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Operating System</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-operating-systems?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

                                          
// $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-7">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->operating_system_name.' </b></div> 
                                       </div>      </div>
                                            </div>
      <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                         
                                                        ';
                                                        if($q->operating_system_image!=''){

                                                      $html.='<img src="public/operating_system_logos/'.$q->operating_system_image.'" style="width: 100%;">';
                                                      }else{
                                                            $html.='<img src="public/img/image-default.png" style="width: 100%;">';
                                                      }
                                                $html.='</div> 

                                    </div>

                                         </div>

                                         
                                         
                                      
 
                                           </div>       
                              
                                      
                                               </div>      

                         </div>
 </div>

             </div>


         </div>   ';

   $contract=DB::table('operating_system_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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




 $contract=DB::table('operating_system_attachments')->where('client_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('operating_system_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
 



public function getUsersContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('users as a') ->where('a.id',$id)->first();
   
             
                           $html.='<div class="block card-round   '.($q->portal_access==1?'bg-new-green':'bg-new-red').' new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-white-user.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Users</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-users?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

                                            $id_array=explode(',',$q->access_to_client);

 $client_array=DB::table('clients')
    ->select('client_display_name', 'id', DB::raw('(@row_number:=@row_number+1) AS rownumber'))
    ->from(DB::raw('(SELECT @row_number:=0) AS rn, clients'))
    ->whereIn('id', $id_array)
    ->orderByDesc('id')
    ->get();


// $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Primary Contact</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->salutation.' '.$q->firstname.' '.$q->lastname.'</b></div> 
                                     
                                            </div>

                                         </div>

                                         
                                          <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Email Address</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->email.'</b></div> 
                                     
                                            </div>

                                         </div>
                                      

                                                  <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Telephone</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-sec"><b>'.$q->work_phone.'</b></div> 
                                     
                                            </div>

                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-sec"><b>'.$q->mobile.'</b></div> 
                                     
                                            </div>

                                         </div>
                                           </div>       
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                         ';
                                                        if($q->user_image!=''){

                                                      $html.='<img src="public/client_logos/'.$q->user_image.'" style="width: 100%;">';
                                                      }else{
                                                            $html.='<img src="public/img/image-default.png" style="width: 100%;">';
                                                      }
                                                $html.='
 
                                                </div> 

                                    </div>

                                      
                                               </div>      

                         </div>

             </div>


         </div>  <div class="block-content pb-0  mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">Portal Access</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                             <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                              <div class="col-lg-4">
                                        <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="">
       <input type="checkbox" class="custom-control-input" id="monitored1" name="monitored" value="1" disabled=""'.($q->portal_access==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt " for="monitored1">Enabled</label>
</div>
                                          </div>
                                           

                                         </div>

                                         
                                          <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Access Type</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first text-capitalize"><b>'.$q->role.'</b></div> 
                                     
                                            </div>
     </div>
                                      <div class="row form-group pl-2 pt-2">
                                        ';

                                            foreach($client_array as $c){
                                        $html.='
                                        <div class=" col-lg-3 px-1 ">
                                      <div class="block block-rounded ml-2  table-block-new ">
<div class="d-flex block-content  align-items-center px-2 py-2">
 <p class="font-12pt mb-0  w-100 text-truncate   c4 - " style="  background-color: rgb(151, 192, 255);  ; color: rgb(89, 89, 89); border-color: rgb(89, 89, 89);" data="262">'.$c->client_display_name.'</p> <a class="dropdown-toggle ml-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:;" c="">
                                <img src="public/img/dots.png?cache=1">
                                                                        </a>
                                         <div class="dropdown-menu py-0 pt-1 " aria-labelledby="dropdown-dropright-primary">
      
                  <a class="dropdown-item d-flex align-items-center px-0" target="_blank" href="clients?id='.$c->id.'&page='.(ceil($c->rownumber/10)).'">   <div style="width: 32;  padding-left: 2px"><img src="public/img/open-icon-removebg-preview.png?cache=1" width="22px"> &nbsp;&nbsp;View Client</div></a>  
                 
                </div>
</div>

</div>
</div>
';
}
$html.='
 
         

           </div></div>

                                                 
                                           </div>       
                                    

                                      
                                               </div>      

                         </div>

             </div>
               </div> 
                  </div>
                    </div>
                      </div>
 
'; 

   $contract=DB::table('user_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('user_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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




 $contract=DB::table('user_attachments')->where('user_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('user_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
 













 
    public function InsertCommentSsl(Request $request){
               
                            
                                             DB::table('site_comments')->insert([
                                                 'site_id'=>$request->id,
                                                 'date'=>date('Y-m-d H:i:s'),
                                                 'comment'=>$request->comment ,
                                                 'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,
                                                 'added_by'=>Auth::id(),
                                             ]);
           return redirect()->back()->with('success','Comment Added Successfully');          

    }
    public function InsertAttachmentSsl(Request $request){
       $attachment_array= explode(',',$request->attachment_array);

                        if(isset($request->attachment_array)){
                        foreach($attachment_array as $a){
                        
                             
                                   copy( public_path('temp_uploads/'.$a), public_path('network_attachment/'.$a) );
                                             DB::table('site_attachments')->insert([
                                                 'site_id'=>$request->id,
                                                 'date'=>date('Y-m-d H:i:s'),
                                                 'attachment'=>$a ,
                                                'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
                     return redirect()->back()->with('success','Attachment Added Successfully');
 

    }


 
    public function InsertCommentNetwork(Request $request){
               
                            
                                             DB::table('network_comments')->insert([
                                                 'network_id'=>$request->id,
                                                 'date'=>date('Y-m-d H:i:s'),
                                                 'comment'=>$request->comment ,
                                                 'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,
                                                 'added_by'=>Auth::id(),
                                             ]);
           return redirect()->back()->with('success','Comment Added Successfully');          

    }
    public function InsertAttachmentNetwork(Request $request){
       $attachment_array= explode(',',$request->attachment_array);

                        if(isset($request->attachment_array)){
                        foreach($attachment_array as $a){
                        
                             
                                   copy( public_path('temp_uploads/'.$a), public_path('network_attachment/'.$a) );
                                             DB::table('network_attachments')->insert([
                                                 'network_id'=>$request->id,
                                                 'date'=>date('Y-m-d H:i:s'),
                                                 'attachment'=>$a ,
                                                'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
                     return redirect()->back()->with('success','Attachment Added Successfully');
 

    }


    public function InsertCommentAssets(Request $request){
               
                            
                                             DB::table('asset_comments')->insert([
                                                 'asset_id'=>$request->id,
                                                 'date'=>date('Y-m-d H:i:s'),
                                                 'comment'=>$request->comment ,
                                                 'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,
                                                 'added_by'=>Auth::id(),
                                             ]);
           return redirect()->back()->with('success','Comment Added Successfully');          

    }
    public function InsertAttachmentAssets(Request $request){
       $attachment_array= explode(',',$request->attachment_array);

                        if(isset($request->attachment_array)){
                        foreach($attachment_array as $a){
                        
                             
                                   copy( public_path('temp_uploads/'.$a), public_path('network_attachment/'.$a) );
                                             DB::table('asset_attachments')->insert([
                                                 'asset_id'=>$request->id,
                                                 'date'=>date('Y-m-d H:i:s'),
                                                 'attachment'=>$a ,
                                                'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
                     return redirect()->back()->with('success','Attachment Added Successfully');
 

    }


    public function InsertCommentContracts(Request $request){
               
                            
                                             DB::table('contract_comments')->insert([
                                                 'contract_id'=>$request->id,
                                                 'date'=>date('Y-m-d H:i:s'),
                                                 'comment'=>$request->comment ,
                                                 'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,
                                                 'added_by'=>Auth::id(),
                                             ]);
           return redirect()->back()->with('success','Comment Added Successfully');          

    }
    public function InsertAttachmentContracts(Request $request){
       $attachment_array= explode(',',$request->attachment_array);

                        if(isset($request->attachment_array)){
                        foreach($attachment_array as $a){
                        
                             
                                   copy( public_path('temp_uploads/'.$a), public_path('network_attachment/'.$a) );
                                             DB::table('contract_attachments')->insert([
                                                 'contract_id'=>$request->id,
                                                 'date'=>date('Y-m-d H:i:s'),
                                                 'attachment'=>$a ,
                                                'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
                     return redirect()->back()->with('success','Attachment Added Successfully');
 

    }

    public function InsertCommentSSlCertificate(Request $request){
               
                            
                                             DB::table('ssl_comments')->insert([
                                                 'ssl_id'=>$request->id,
                                                 'date'=>date('Y-m-d H:i:s'),
                                                 'comment'=>$request->comment ,
                                                 'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,
                                                 'added_by'=>Auth::id(),
                                             ]);
           return redirect()->back()->with('success','Comment Added Successfully');          

    }
    public function InsertAttachmentSSlCertificate(Request $request){
       $attachment_array= explode(',',$request->attachment_array);

                        if(isset($request->attachment_array)){
                        foreach($attachment_array as $a){
                        
                             
                                   copy( public_path('temp_uploads/'.$a), public_path('network_attachment/'.$a) );
                                             DB::table('ssl_attachments')->insert([
                                                 'ssl_id'=>$request->id,
                                                 'date'=>date('Y-m-d H:i:s'),
                                                 'attachment'=>$a ,
                                                'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
                     return redirect()->back()->with('success','Attachment Added Successfully');
 

    }
    public function EndClients(Request $request){
            if($request->end==1){



                       DB::Table('clients')->where('id',$request->id)->update(['client_status'=>'1']);       
                DB::table('client_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'client_id'=>$request->id,'comment'=>'Client Reactivated.<br>'.$request->reason]);

               DB::table('client_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Client successfully Reactivated.','client_id'=>$request->id]);
    return redirect()->back()->with('success','Client Reactivated');
            }
            else{
             DB::Table('clients')->where('id',$request->id)->update(['client_status'=>'0']);       
                DB::table('client_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'client_id'=>$request->id,'comment'=>'Client successfully Deactivated.<br>'.$request->reason]);

               DB::table('client_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Client successfully Deactivated.','client_id'=>$request->id]);
                   return redirect()->back()->with('success','Client Deactivated Successfully');
}

           
    }






 
    public function EndDomains(Request $request){
            if($request->end==1){



                       DB::Table('domains')->where('id',$request->id)->update(['domain_status'=>'1']);       
                DB::table('domain_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'client_id'=>$request->id,'comment'=>'Domain Reactivated.<br>'.$request->reason]);

               DB::table('domain_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Domain successfully Reactivated.','client_id'=>$request->id]);
    return redirect()->back()->with('success','Domain Reactivated');
            }
            else{
             DB::Table('domains')->where('id',$request->id)->update(['domain_status'=>'0']);       
                DB::table('domain_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'client_id'=>$request->id,'comment'=>'Domain successfully Deactivated.<br>'.$request->reason]);

               DB::table('domain_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Domain successfully Deactivated.','client_id'=>$request->id]);
                   return redirect()->back()->with('success','Domain Deactivated Successfully');
}

           
    }
    
    
    
    public function EndSystemCategory(Request $request){
            if($request->end==1){



                       DB::Table('system_category')->where('id',$request->id)->update(['domain_status'=>'1']);       
                DB::table('system_category_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'client_id'=>$request->id,'comment'=>'System Category Reactivated.<br>'.$request->reason]);

               DB::table('system_category_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'System Category successfully Reactivated.','client_id'=>$request->id]);
    return redirect()->back()->with('success','System Category Reactivated');
            }
            else{
             DB::Table('system_category')->where('id',$request->id)->update(['domain_status'=>'0']);       
                DB::table('system_category_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'client_id'=>$request->id,'comment'=>'System Category successfully Deactivated.<br>'.$request->reason]);

               DB::table('system_category_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'System Category successfully Deactivated.','client_id'=>$request->id]);
                   return redirect()->back()->with('success','System Category Deactivated Successfully');
}

           
    }
    
    
    public function EndSystemTypes(Request $request){
            if($request->end==1){



                       DB::Table('system_types')->where('id',$request->id)->update(['domain_status'=>'1']);       
                DB::table('system_type_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'client_id'=>$request->id,'comment'=>'System type Reactivated.<br>'.$request->reason]);

               DB::table('system_type_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'System type successfully Reactivated.','client_id'=>$request->id]);
    return redirect()->back()->with('success','System type Reactivated');
            }
            else{
             DB::Table('system_types')->where('id',$request->id)->update(['domain_status'=>'0']);       
                DB::table('system_type_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'client_id'=>$request->id,'comment'=>'System type successfully Deactivated.<br>'.$request->reason]);

               DB::table('system_type_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'System type successfully Deactivated.','client_id'=>$request->id]);
                   return redirect()->back()->with('success','System type Deactivated Successfully');
}

           
    }



 
    public function EndUsers(Request $request){
            if($request->end==1){



                       DB::Table('users')->where('id',$request->id)->update(['portal_access'=>'1']);       
                DB::table('user_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'user_id'=>$request->id,'comment'=>'User Reactivated.<br>'.$request->reason]);

               DB::table('user_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'User successfully Reactivated.','client_id'=>$request->id]);
    return redirect()->back()->with('success','User Reactivated');
            }
            else{
             DB::Table('users')->where('id',$request->id)->update(['portal_access'=>'0']);       
                DB::table('user_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'user_id'=>$request->id,'comment'=>'User successfully Deactivated.<br>'.$request->reason]);

               DB::table('user_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'User successfully Deactivated.','client_id'=>$request->id]);
                   return redirect()->back()->with('success','User Deactivated Successfully');
}

           
    }

public function getClientContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('clients as a') ->where('a.id',$id)->first();
   
                $client_emails=DB::Table('client_emails')->where('client_id',$q->id)->get();
                $client_ssl_emails=DB::Table('client_ssl_emails')->where('client_id',$q->id)->get();   
 
                           $html.='<div class="block card-round   '.($q->client_status==1?'bg-new-green':'bg-new-red').' new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-white-client.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Client</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.=' 
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-clients?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
                                            }

                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Primary Contact</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->salutation.' '.$q->firstname.' '.$q->lastname.'</b></div> 
                                     
                                            </div>

                                         </div>

                                           <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Company</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->company_name.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                           <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Display Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->client_display_name.'</b></div> 
                                     
                                            </div>

                                         </div>
                                          <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Email Address</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->email_address.'</b></div> 
                                     
                                            </div>

                                         </div>
                                      

                                                  <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Telephone</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-sec"><b>'.$q->work_phone.'</b></div> 
                                     
                                            </div>

                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-sec"><b>'.$q->mobile.'</b></div> 
                                     
                                            </div>

                                         </div>
                                               <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Website</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->website.'</b></div> 
                                     
                                            </div>
                                         
                                    </div> </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                         
   ';
                                                        if($q->logo!=''){

                                                      $html.='<img src="public/client_logos/'.$q->logo.'" style="width: 100%;">';
                                                      }else{
                                                            $html.='<img src="public/img/image-default.png" style="width: 100%;">';
                                                      }
                                                $html.='
                                                      
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>

         </div>

  <div class="block-content pb-0  mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">Address Info</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Country</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->country.'</b></div> 
                                     
                                            </div>

                                         </div>

                                           <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Address</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->client_address.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                           <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">City</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->city.'</b></div> 
                                     
                                            </div>

                                         </div>
                                          <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">State/Province
</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->state.'</b></div> 
                                     
                                            </div>

                                         </div>
                                      

                                                  <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Zip/Postal Code</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-sec"><b>'.$q->zip.'</b></div> 
                                     
                                            </div>
                                
                                         
                                    </div> 
                                    <div class="row pt-3">
                  <div class="col-lg-4">
                                        <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="">
       <input type="checkbox" class="custom-control-input" id="monitored1" name="monitored" value="1" disabled=""'.($q->renewal_notification==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt " for="monitored1">Contract Notification Email</label>
</div>
                                          </div>

                                              <div class="col-lg-4">
                                        <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="">
       <input type="checkbox" class="custom-control-input" id="monitored2" name="monitored" value="1" disabled="" '.($q->ssl_notification==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt " for="monitored2">SSL Notification Email</label>
</div>
                                          </div>
                                          </div></div>
                                  
                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>

             </div>

                                                     
                 
                 </div>
             </div>


         </div>

 </div>

 
';
if($q->renewal_notification==1 && sizeof($client_emails)){
$html.='

<div class="block new-block position-relative mt-5 " >
                                                <div class="top-div text-capitalize">Contract Renewal Notifications
</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock">

                                                          <div class=" row mb-3">
                                                         
                                          
                                        </div>
';


foreach($client_emails as $e){

                                                          $html.='<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 2px;padding-bottom: 7px;">
                                                         <h1 class="mb-0 mr-2" style=""><b>@</b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0" style="">
                                                        <label class="mb-0">'.$e->renewal_email.'</label>
                                                    </td>
                                                   
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
} 

                                    $html.='

                                     </div></div></div>
    ';
   
 }









if($q->ssl_notification==1 && sizeof($client_ssl_emails)){
$html.='

<div class="block new-block position-relative mt-5 " >
                                                <div class="top-div text-capitalize">SSL Certifi
                                                cate  Renewal Notifications
</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock">

                                                          <div class=" row mb-3">
                                                         
                                          
                                        </div>
';


foreach($client_ssl_emails as $e){

                                                          $html.='<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 2px;padding-bottom: 7px;">
                                                         <h1 class="mb-0 mr-2" style=""><b>@</b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0" style="">
                                                        <label class="mb-0">'.$e->renewal_email.'</label>
                                                    </td>
                                                   
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
} 

                                    $html.='

                                     </div></div></div>
    ';
   
 }

   $contract=DB::table('client_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('client_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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




 $contract=DB::table('client_attachments')->where('client_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('client_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.client_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
 

public function getSiteContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('sites as a')->select('a.*' ,'c.client_display_name','c.logo' ,'usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','c.logo' )->join('clients as c','c.id','=','a.client_id') ->leftjoin('users as usr','usr.id','=','a.created_by')->leftjoin('users as upd','upd.id','=','a.updated_by')->where('a.id',$id)->first();
   
                   
 
                           $html.='<div class="block card-round   bg-new-dark new-nav" '.($q->status == 1 ? 'style="background-color: #4194f6;"' : '').' >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-sites-white.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Site</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.='       <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" > 
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="'.asset('public/img/paper-clip-white.png').'" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="'.asset('public/img/comment-white.png').'" width="20px"></a>
                                         </span>
                                         <a  target="_blank" href="pdf-site?id='.$q->id.'"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Pdf" class=" " style="padding:5px 7px">
                                                <img src="public/img/action-white-pdf.png" width="24px">
                                            </a>
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-sites?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
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
   
 

   $contract=DB::table('site_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('site_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('site_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.site_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
   
                   
 
                           $html.='<div class="block card-round   bg-new-dark new-nav" '.($q->status == 1 ? 'style="background-color: #4194f6;"' : '') . ' >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-network-segment-white.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">  Network Segment</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                                    
                                                       
                                                      
                                  
                                        
$html.='  <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" > 
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="'.asset('public/img/paper-clip-white.png').'" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="'.asset('public/img/comment-white.png').'" width="20px"></a>
                                         </span><a  target="_blank" href="pdf-network?id='.$q->id.'"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Pdf" class=" " style="padding:5px 7px">
                                                <img src="public/img/action-white-pdf.png" width="24px">
                                            </a>
     <a  href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-network?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png" width="17px"></a>';
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
                                      <div class=" text-center border-none   font-size-md   bubble-white-new bubble-text-sec"  style="border: none;border-radius:10px;box-shadow:none;;color:'.@$net_zone->tag_text_color.';background-color: '.@$net_zone->tag_back_color.'" ><b>'.$q->zone.'</b></div>
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
  <input type="checkbox" class="custom-control-input" id="wifi_enabled" name="wifi_enabled" value="1" disabled '.($q->internet_facing==1?'checked':'').'>
  <label class="btn btn-new w-100 WifiDiv " for="wifi_enabled">Internet Facing</label>
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

';
    if($q->wifi_enabled ==1){


                            $html.='<div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
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
         </div>';



}




                            $html.='<div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
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
   
 

   $contract=DB::table('network_comments as v')->select('v.*','u.user_image')->leftjoin('users as u','u.id','=','v.added_by')->where('network_id',$q->id) ->get();  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
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


   $contract=DB::table('network_audit_trail as c')->select('c.*','u.firstname','u.lastname','u.user_image')->leftjoin('users as u','u.id','=','c.user_id')->where('c.network_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="'.($c->user_image==''?'public/img/profile-white.png':'public/client_logos/'.$c->user_image).'"> </b></h1>
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

public function ChangeNetworkStatus(Request $request)
{
    $qry= DB::Table('network')->where('id',$request->id)->first();
         $status=0;      
        if($qry->status==1){
            $detail='Network successfully deactivated.';
        $status=0;
        $InactiveDate=date('Y-m-d');
        }
        else{
            $detail='Network successfully Re-activated.';
        $status=1;
        $InactiveDate='';
        }
             DB::Table('network')
             ->where('id',$request->id)
             ->update(['status'=>$status ]);       
                DB::table('network_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'network_id'=>$request->id,'comment'=>$detail.'<br>'.$request->reason]);

               DB::table('network_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>$detail,'network_id'=>$request->id]);


               return redirect()->back()->with('success',$detail);
}
    
    
    public function ChangeSiteStatus(Request $request)
    {
        $qry= DB::Table('sites')->where('id',$request->id)->first();
         $status=0;      
        if($qry->status==1){
            $detail='Site successfully deactivated.';
        $status=0;
        $InactiveDate=date('Y-m-d');
        }
        else{
            $detail='Site successfully Re-activated.';
        $status=1;
        $InactiveDate='';
        }
             DB::Table('sites')
             ->where('id',$request->id)
             ->update(['status'=>$status ]);       
                DB::table('site_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'site_id'=>$request->id,'comment'=>$detail.'<br>'.$request->reason]);

               DB::table('site_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>$detail,'site_id'=>$request->id]);


               return redirect()->back()->with('success',$detail);
    }
    
}