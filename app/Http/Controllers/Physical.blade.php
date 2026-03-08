  
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')



<?php 

$limit=10;
        $no_check=DB::Table('settings')->where('user_id',Auth::id())->first();
 
if(isset($_GET['limit']) && $_GET['limit']!=''){
    $limit=$_GET['limit'];
 
        if($no_check!=''){
                  if($page_type==''){
                DB::table('settings')->where('user_id',Auth::id())->update(['physical_asset'=>$limit]);
        }
        elseif($page_type=='servers'){
            DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_server'=>$limit]);
        }
        elseif($page_type=='other'){
             DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_others'=>$limit]);
        }
        elseif($page_type=='managed'){
             DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_managed'=>$limit]);
        }
        elseif($page_type=='support-contracts'){
             DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_support'=>$limit]);
        }
            elseif($page_type=='ssl-certificate'){
             DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_ssl'=>$limit]);
        }
            elseif($page_type=='inactive'){
             DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_inactive'=>$limit]);
        }


       
        }
        else{
              if($page_type==''){

                     DB::table('settings')->insert(['user_id'=>Auth::id(),'physical_asset'=>$limit]);
        }
        elseif($page_type=='servers'){

                      DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_server'=>$limit]);
        }
        elseif($page_type=='other'){
                       DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_others'=>$limit]);
        }
        elseif($page_type=='managed'){
                         DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_managed'=>$limit]);
        }
        elseif($page_type=='support-contracts'){
                          DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_support'=>$limit]);
        }
            elseif($page_type=='ssl-certificate'){
               DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_ssl'=>$limit]);
        }
            elseif($page_type=='inactive'){
                     DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_inactive'=>$limit]);
        }


     
        }
        
}
else{
           
        if($no_check!=''){

            if($no_check->physical_asset!=''){
     
            if($page_type==''){
                 $limit=$no_check->physical_asset;
        }

        elseif($page_type=='servers'){
             if($no_check->asset_physical_server!=''){
                 $limit=$no_check->asset_physical_server;
        }
        elseif($page_type=='other'){
             if($no_check->asset_physical_others!=''){
                   $limit=$no_check->asset_physical_others;
        }
    }
        elseif($page_type=='managed'){
             if($no_check->asset_physical_managed!=''){
                    $limit=$no_check->asset_physical_managed;
        }
    }
        elseif($page_type=='support-contracts'){
             if($no_check->asset_physical_support!=''){
               $limit=$no_check->asset_physical_support;
        }
    }
            elseif($page_type=='ssl-certificate'){
                 if($no_check->asset_physical_ssl!=''){
                   $limit=$no_check->asset_physical_ssl;
        }
    }
            elseif($page_type=='inactive'){
                 if($no_check->asset_physical_inactive!=''){
               $limit=$no_check->asset_physical_inactive;
        }
    }
}





        }
        }
}
$userAccess=explode(',',Auth::user()->access_to_client);

if(sizeof($_GET)>0){


 


if(isset($_GET['advance_search'])){

$orderby='asc';
$field='a.position';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}
 
 
 
$cond='';
 if(isset($_GET['client_id'])  && $_GET['client_id']!='' ){
                    $client_id=$_GET['client_id'];
                $cond.=" and a.client_id ='$client_id'";
 }
  if(isset($_GET['site_id']) && sizeof($_GET['site_id'])>0){

                    $site_id=implode(',',$_GET['site_id']);
                $cond.=" and a.site_id in ($site_id)";
 }

  if(isset($_GET['domain']) && sizeof($_GET['domain'])>0){
                    $domain=implode(',',$_GET['domain']);
                $cond.=" and a.domain in ($domain)";
 }
    if(isset($_GET['asset_type_id']) && sizeof($_GET['asset_type_id'])>0){

                $asset_type_id=implode(',',$_GET['asset_type_id']);
                $cond.=" and a.asset_type_id in ($asset_type_id)";
 }

 if(isset($_GET['asset_status'])  && $_GET['asset_status']!=''){
                $asset_status=$_GET['asset_status'];      
                $cond.=" and a.AssetStatus='$asset_status'";
 }
 if(isset($_GET['hostname'])  && $_GET['hostname']!=''){
                $hostname=$_GET['hostname'];
                $cond.=" and a.hostname='$hostname'";
 }
 if(isset($_GET['sla'])  && $_GET['sla']!=''){
            $sla=$_GET['sla'];
                $cond.=" and a.sla='$sla'";
 }

  if(isset($_GET['os']) && sizeof($_GET['os'])>0){

                    $os=implode(',',$_GET['os']);
                $cond.=" and a.os in ($os)";
 }

 
  if(isset($_GET['ntp'])  && $_GET['ntp']!=''){
                $ntp=$_GET['ntp'];
                $cond.=" and a.ntp='$ntp'";
 }
   if(isset($_GET['internet_facing'])  && $_GET['internet_facing']!=''){
                $internet_facing=$_GET['internet_facing'];
                $cond.=" and a.internet_facing='$internet_facing'";
 }
   if(isset($_GET['SupportStatus'])  && $_GET['SupportStatus']!=''){
                $SupportStatus=$_GET['SupportStatus'];

                if($_GET['SupportStatus']=='N/A'){
                        $cond.=" and (a.SupportStatus='N/A' || a.SupportStatus is null || a.SupportStatus='') ";
                    }
                    else{
                        $cond.=" and a.SupportStatus='$SupportStatus' ";   
                    }
}

  if(isset($_GET['ip_address'])  && $_GET['ip_address']!=''){
                 $ip_address=$_GET['ip_address'];
                $cond.=" and a.ip_address='$ip_address'";
 }

   if(isset($_GET['vlan_id'])  && $_GET['vlan_id']!=''){
            $vlan_id=$_GET['vlan_id'];
                $cond.=" and a.vlan_id='$vlan_id'";
 }
   if(isset($_GET['network_zone'])  && $_GET['network_zone']!=''){
                  $network_zone=$_GET['network_zone'];
                $cond.=" and a.network_zone='$network_zone'";
 }
 
if(Auth::user()->role=='admin'){



if($page_type=='servers'){
  

$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('at.asset_type_description','Physical Server')->orderBy($field,$orderby) ->paginate($limit); 
}
elseif($page_type=='other'){

 
 

$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('at.asset_type_description','!=','Physical Server')->orderBy($field,$orderby) ->paginate($limit); 
}

elseif($page_type=='managed'){

 
 

$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('a.managed',1)->orderBy($field,$orderby) ->paginate($limit); 
}

elseif($page_type=='support-contracts'){
 
 
$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('HasWarranty',1)->where('AssetStatus',1)->orderBy($field,$orderby) ->paginate($limit); 
}
elseif($page_type=='ssl-certificate'){

  
 
$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('ntp',1)->where('AssetStatus',1)->orderBy($field,$orderby) ->paginate($limit); 
}
elseif($page_type=='inactive'){
  

$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus','!=',1)->orderBy($field,$orderby) ->paginate($limit); 
}

else{

$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->orderBy($field,$orderby) ->paginate($limit); 

}
}
else{





if($page_type=='servers'){
 
    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('at.asset_type_description','Physical Server')->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit);  
}
elseif($page_type=='other'){

 

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('at.asset_type_description','!=','Physical Server')->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 
}

elseif($page_type=='managed'){

 
    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('a.managed',1)->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 
}

elseif($page_type=='support-contracts'){
 

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('HasWarranty',1)->where('AssetStatus',1) ->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 
}
elseif($page_type=='ssl-certificate'){

  

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->where('ntp',1)->where('AssetStatus',1)->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 
}
elseif($page_type=='inactive'){
 

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical') ->where('AssetStatus','!=',1)->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 
}

else{




    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->whereRaw("c.is_deleted=0 $cond")->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0) ->where('a.is_deleted',0)->where('a.asset_type','physical')->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 
}
}


}
else{


$orderby='asc';
$field='a.position';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}
 
if(Auth::user()->role=='admin'){

if($page_type=='servers'){
 
$qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
                $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
          
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('at.asset_type_description','Physical Server')->orderBy($field,$orderby) ->paginate($limit); 


}
elseif($page_type=='other'){


$qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
               $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('AssetStatus',1)->where('at.asset_type_description','!=','Physical Server')->where('a.asset_type','physical')->orderBy($field,$orderby) ->paginate($limit); 
}
elseif($page_type=='managed'){
 
 
$qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
               $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('a.managed',1)->where('AssetStatus',1)->orderBy($field,$orderby) ->paginate($limit); 
}

elseif($page_type=='support-contracts'){

 
$qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
               $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('HasWarranty',1)->where('AssetStatus',1)->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->orderBy($field,$orderby) ->paginate($limit); 
}
elseif($page_type=='ssl-certificate'){
 
$qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
               $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('ntp',1)->where('AssetStatus',1) ->orderBy($field,$orderby) ->paginate($limit); 
}
elseif($page_type=='inactive'){

     
$qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
               $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus','!=',1) ->orderBy($field,$orderby) ->paginate($limit);  
}
else{

 
$qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
               $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->orderBy($field,$orderby) ->paginate($limit); 
}
}else{

if($page_type=='servers'){
 


$qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
                $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->whereIn('a.client_id',$userAccess)->where('AssetStatus',1)->where('at.asset_type_description','Physical Server')->orderBy($field,$orderby) ->paginate($limit); 


}
elseif($page_type=='other'){
 
    $qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
                $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('at.asset_type_description','!=','Physical Server')->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 
}

elseif($page_type=='managed'){
 
    $qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
                $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->whereIn('a.client_id',$userAccess)->where('AssetStatus',1)->where('a.managed',1)->orderBy($field,$orderby) ->paginate($limit); 
}

elseif($page_type=='support-contracts'){
 
    $qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
                $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->whereIn('a.client_id',$userAccess)->where('AssetStatus',1)->where('a.asset_type','physical')->where('HasWarranty',1)->orderBy($field,$orderby) ->paginate($limit); 
}
elseif($page_type=='ssl-certificate'){

  
    $qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
                $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('ntp',1)->where('AssetStatus',1) ->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 
}
elseif($page_type=='inactive'){

    
    $qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
                $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->whereIn('a.client_id',$userAccess)->where('AssetStatus','!=',1)->orderBy($field,$orderby) ->paginate($limit); 
}
else{
$qry=DB::table('assets as a')->where(function($query){
        $query->Orwhere('client_display_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('hostname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('fqdn','like','%'.@$_GET['search'].'%');
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('use_','like','%'.@$_GET['search'].'%');
        $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
         $query->Orwhere('app_owner','like','%'.@$_GET['search'].'%');
          $query->Orwhere('ip_address','like','%'.@$_GET['search'].'%');
           $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
            $query->Orwhere('network_zone','like','%'.@$_GET['search'].'%');
             $query->Orwhere('sla','like','%'.@$_GET['search'].'%');
                $query->Orwhere('sn','like','%'.@$_GET['search'].'%');
              $query->Orwhere('memory','like','%'.@$_GET['search'].'%');

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 
 
}

}

}

}
 else{

  if(Auth::user()->role=='admin'){ 

if($page_type=='servers'){

$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('AssetStatus',1)->where('at.asset_type_description','Physical Server')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->orderBy('a.position','asc') ->paginate($limit); 
}
elseif($page_type=='other'){

   $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('AssetStatus',1)->where('at.asset_type_description','!=','Physical Server')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->orderBy('a.position','asc') ->paginate($limit); 
}
elseif($page_type=='managed'){
 
    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.managed',1)->where('AssetStatus',1)->where('a.asset_type','physical')->orderBy('a.position','asc') ->paginate($limit); 
}

elseif($page_type=='support-contracts'){

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('HasWarranty',1)->where('AssetStatus',1) ->orderBy('a.position','asc') ->paginate($limit); 
}
elseif($page_type=='ssl-certificate'){

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('ntp',1)->where('AssetStatus',1) ->orderBy('a.position','asc') ->paginate($limit); 
}
elseif($page_type=='inactive'){

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical') ->where('AssetStatus','!=',1) ->orderBy('a.position','asc') ->paginate($limit); 
}
else{
$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->orderBy('a.position','asc') ->paginate($limit); 
}
 
 }
else{

if($page_type=='servers'){

$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('at.asset_type_description','Physical Server')->whereIn('a.client_id',$userAccess)->orderBy('a.position','asc') ->paginate($limit); 
}
elseif($page_type=='other'){

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('at.asset_type_description','!=','Physical Server')->whereIn('a.client_id',$userAccess)->orderBy('a.position','asc') ->paginate($limit); 
}

elseif($page_type=='managed'){

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('AssetStatus',1)->where('a.managed',1)->whereIn('a.client_id',$userAccess)->orderBy('a.position','asc') ->paginate($limit); 
}

elseif($page_type=='support-contracts'){

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('HasWarranty',1)->where('AssetStatus',1) ->whereIn('a.client_id',$userAccess)->orderBy('a.position','asc') ->paginate($limit); 
}
elseif($page_type=='ssl-certificate'){

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->where('ntp',1) ->where('AssetStatus',1)->whereIn('a.client_id',$userAccess)->orderBy('a.position','asc') ->paginate($limit); 
}
elseif($page_type=='inactive'){

    $qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical') ->where('AssetStatus','!=',1)->whereIn('a.client_id',$userAccess)->orderBy('a.position','asc') ->paginate($limit); 
}
else{

$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.client_display_name','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset')->where('s.is_deleted',0)->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.asset_type','physical')->whereIn('a.client_id',$userAccess)->orderBy('a.position','asc') ->paginate($limit); 
}
}}

 ?>        <!-- Main Container -->
            <main id="main-container">
                <!-- Hero -->
           
<style type="text/css">
         .dropdown-menu {
        z-index: 100000!important;
    }
    
   
</style>
                <div class="bg-body-light">
                  
                </div>
                <!-- END Hero -->

                <!-- Page Content -->
                <div class="content">
                    <!-- Full Table -->
                    <div class="block block-rounded">
                       
                        <div class="block-content">

<div class="TopArea" style="position: sticky;
    top: 65px;
    padding-top: 15px;
    z-index: 1000;
    background: white;
    padding-bottom: 5px;">
    <div class="row" >
        <div class="col-sm-3">
                        <form class="push"   method="get">
                                        <input type="hidden" name="limit" value="{{$_GET['limit']??10}}">
                          
                                <div class="input-group">
                                    <input type="text" value="{{@$_GET['search']}}" class="form-control" name="search" placeholder="Quick Search">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-fw fa-search"></i>
                                        </span>
                                    </div>
                                </div>     <div class="block-  block-header- " role="tab" id="accordion2_h1">
                                              @if(!isset($_GET['advance_search']))
                                                <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Set Filters</a>
                                       @else
                                       <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Modify Filters</a>
                                       <a href="{{url('virtual')}}" class="text-danger">| Clear Filters</a>
                                       @endif
                                            </div>
                            </form>
                        </div>
                




    <div class="col-sm-5">
          {{$qry->appends($_GET)->links()}}
    </div>
      <div class="col-lg-1 text-right">
                                  <form id="limit_form">
                                <select name="limit"  onchange="document.getElementById('limit_form').submit()" class="float-right form-control mr-2 mb-2 px-0" style="width:auto">
                                        <option value="10" {{@$limit==10?'selected':''}}>10</option>
                                        <option value="25" {{@$limit==25?'selected':''}}>25</option>
                                        <option value="50" {{@$limit==50?'selected':''}}>50</option>
                                        <option value="100" {{@$limit==100?'selected':''}}>100</option>
                                </select>
                            </form>
                        </div>
                          
                        <div class="col-sm-3 text-right">
                              <button class="btn mr-2 btn-light" data-toggle="modal" data-target="#EditColumnModal">Edit Columns</button>
                     
                                 <div class="btn-group">
                                   





         <div class="btn-group">
                                    <div class="dropdown">
                                              
                                                 <button type="button" style="border-top-right-radius: 0px;border-bottom-right-radius: 0px;" class="dropdown-toggle btn btn-outline-primary " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="far fa-file-excel"></i>
                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-default-alt-primary">
                                                    <a class="dropdown-item" href="{{url('export-excel-assets')}}/{{$page_type}}?{{$_SERVER['QUERY_STRING']}}&type=physical">Excel</a>
                                         
                                                
                                                
                                                </div>
                                            </div>


                             @if(Auth::user()->role!='read')    
                                                      <a class="btn btn-outline-primary" href="{{url('add-assets')}}/physical">
                                  Add New  <i class="fa fa-plus-circle ml-1"></i>
                                </a>
                                @endif
                                                </div>

</div>
                    </div>
                </div>

</div>
                        
                                      

                                        </div>
                  <?php
                   
 
  
 
 $filter=(isset($_GET['advance_search'])?'advance_search='.$_GET['advance_search']:'').(isset($_GET['client_id'])?'&client_id='.$_GET['client_id']:'').(isset($_GET['site_id'])?'&'.http_build_query(array('site_id'=>$_GET['site_id'])):'').(isset($_GET['asset_type_id'])?'&'.http_build_query(array('asset_type_id'=>$_GET['asset_type_id'])):'').(isset($_GET['domain'])?'&'.http_build_query(array('domain'=>$_GET['domain'])):'').(isset($_GET['hostname'])?'&hostname='.$_GET['hostname']:'').(isset($_GET['asset_status'])?'&asset_status='.$_GET['asset_status']:'').(isset($_GET['sla'])?'&sla='.$_GET['sla']:'').(isset($_GET['SupportStatus'])?'&SupportStatus='.$_GET['SupportStatus']:'').(isset($_GET['os'])?'&'.http_build_query(array('os'=>$_GET['os'])):'').(isset($_GET['ntp'])?'&ntp='.$_GET['ntp']:'').(isset($_GET['internet_facing'])?'&internet_facing='.$_GET['internet_facing']:'').(isset($_GET['ip_address'])?'&ip_address='.$_GET['ip_address']:'').(isset($_GET['vlan_id'])?'&vlan_id='.$_GET['vlan_id']:'').(isset($_GET['network_zone'])?'&network_zone='.$_GET['network_zone']:''.(isset($_GET['limit'])?'&limit='.$_GET['limit']:''));
?>            <div class="table-responsive">
                                <table class="table   table-striped floathead table-bordered table-vcenter">
                                    <thead class="thead thead-dark">
                                        <tr>
                                              <th class="text-center ">Actions</th>
                                 <th data-index=0 style="min-width:70px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.id" class=" 
                                                "># <i class="fa fa-sort"></i>  </a></th>
                                          
                                

                                        <th data-index=42 style="min-width: 50px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.AssetStatus" class="  ">Active</a></th>

                                          <!--   <th data-index=45  style="min-width: 70px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=at.asset_type_description" class=" 
                                                ">Type  <i class="fa fa-sort"></i>  </a></th> -->
                                            <th data-index=1  style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=c.client_display_name" class=" 
                                                ">Client  <i class="fa fa-sort"></i>  </a></th>
                                            <th data-index=2 style="min-width: 90px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=s.site_name" class=" 
                                                ">Site   <i class="fa fa-sort"></i> </<a></th>

                                                      <th data-index=3 style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=m.vendor_name" class=" 
                                                ">Manu <i class="fa fa-sort"></i> </<a></th>

                                                     <th data-index=4 style="min-width: 75px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.model" class=" 
                                                ">Model <i class="fa fa-sort"></i> </<a></th>
                                                       <th data-index=5 style="min-width: 75px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.type" class=" 
                                                ">Type <i class="fa fa-sort"></i> </<a></th>
                                                               <th data-index=6 style="min-width: 60px"><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.sn" class=" 
                                                ">SN  <span class="tooltiptext">Serial Number</span> <i class="fa fa-sort"></i> </<a></th>
                                                        <th data-index=9><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.fqdn" class=" 
                                                ">FQDN   <span class="tooltiptext">Fully Qualified Domain Name</span> <i class="fa fa-sort"></i> </a></th>
                                                    <th  data-index=10 style="min-width: 80px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.role" class=" 
                                                ">Role  <i class="fa fa-sort"></i> </a></th>
                                                    <th  data-index=11 style="min-width: 60px"><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.use_" class=" 
                                                ">Env   <span class="tooltiptext">Environment</span> <i class="fa fa-sort"></i> </a></th>

                                            <th data-index=7 style="min-width:120px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.hostname" class=" 
                                                ">Hostname  <i class="fa fa-sort"></i> </a></th>
                                                    <th  data-index=12 style="min-width: 150px"><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=o.operating_system_name" class=" 
                                                ">O/S  <span class="tooltiptext">Operating System</span><i class="fa fa-sort"></i> </a></th>

   <th  data-index=37 style="min-width: 60px" ><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.memory" class=" 
                                                ">Mem<Br>(Gb)  <i class="fa fa-sort"></i>  </a></th>

                     @if($page_type=='servers')
                                                    <th  data-index=30  style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.cpu_model" class=" 
                                                ">CPU</a></th>
                        @endif                        

   <th data-index=8><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=d.domain_name" class=" 
                                                ">Domain  <i class="fa fa-sort"></i> </a></th>
                                                    <th data-index=15  style="min-width: 80px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=n.vlan_id" class=" 
                                                "><span class="text-lowercase">v</span> LANID   <i class="fa fa-sort"></i> </a></th>

                                                 <th data-index=14 style="min-width: 110px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.ip_address" class=" 
                                                ">IP Address   <i class="fa fa-sort"></i> </a></th>
      @if($page_type=='servers' || $page_type=='other' || $page_type=='managed')
            <th  data-index=16 style="min-width: 90px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.network_zone" class="   ">   NETZONE
                                                    @else
                    <th  data-index=16 style="min-width: 70px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.network_zone" class="   ">   ZONE @endif
                                                  <i class="fa fa-sort"></i> </a></th>
                                                 <th data-index=17  style="min-width: 50px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.internet_facing" class="tooltip1">IF<span class="tooltiptext">Internet Facing</span></a></th>

                                                <th data-index=19  style="min-width: 50px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.load_balancing"   class="tooltip1">LB<span class="tooltiptext">Load Balancing </span></a></th>

                                                <th data-index=20 style="min-width: 50px" ><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.clustered"  class="tooltip1">CL<span class="tooltiptext">Clustered</span></a></th>
                                                  <th data-index=40 style="min-width:50px"><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.HasWarranty" class=" ">VNDR<br>SUPR <span class="tooltiptext">Vendor Support</span></a></th>

     <th data-index=38 style="min-width: 140px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.SupportStatus" class=" 
                                                ">
                                                @if($page_type=='support-contracts')
                                                    Status
                                                @else
                                                    Support Status
                                                @endif

                                                 <i class="fa fa-sort"></i> </a></th>
                                                     
                                                   

    <th data-index=44> Contract# </th>


                                                   <th data-index=39 style="min-width: 140px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.warranty_end_date" class=" 
                                                ">
                                                  @if($page_type=='support-contracts') 
                                                    End Date
                                                  @else
                                                  SupportEndDate
                                                  @endif <i class="fa fa-sort"></i> </a></th>


                                              

                                            <th  data-index=27  style="min-width: 50px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.ntp" class=" 
                                                ">SSL<br>CERT</a></th>
                                              
                                                  
                                                  <th data-index=46     style="min-width: 130px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.ssl_certificate_status" class="  ">
                                                       @if($page_type=='ssl-certificate') 
                                                        STATUS
                                                       @else
                                                       CERT STATUS
                                                       @endif
                                                   <i class="fa fa-sort"></i> </a></th>
                                           <th data-index=47     style="min-width: 130px"><a href="#" >
                                              @if($page_type=='ssl-certificate') 
                                                        ISSUER
                                                       @else
                                                       CERT ISSUER
                                                       @endif
                                                       <i class="fa fa-sort"></i> </a></th>
                                             <th data-index=48     style="min-width: 200px"><a  href="#" >
                                                @if($page_type=='ssl-certificate') 
                                                 EXPIRATION
                                                @else
                                                CERT EXPIRATION
                                                @endif
 <i class="fa fa-sort"></i> </a></th>                                       
                                                                
                                            <th data-index=49 style="min-width:80px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.managed" class=" 
                                                ">Managed</a></th>
                                                     
                                        
                                                     
                                               
                                          
                                         
                                            
                                                <th data-index=13  style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.app_owner" class=" 
                                                ">App Owner   <i class="fa fa-sort"></i> </a></th>
                                               
                                                <th  data-index=29  style="min-width: 90px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.sla" class=" 
                                                ">SLA  <i class="fa fa-sort"></i>  </a></th>

                                                <th data-index=18 style="min-width: 50px" ><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.disaster_recovery" class=" 
                                                ">DR    <span class="tooltiptext">Disaster Recovery</span> </a></th>
                                              
                                                <th  data-index=21  style="min-width: 50px"><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.monitored" class=" 
                                                ">MNT  <span class="tooltiptext">Monitored</span>  </a></th>
                                                <th  data-index=22 style="min-width: 50px" ><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.patched" class=" 
                                                ">PTC    <span class="tooltiptext">Patched</span> </a></th>
                                                <th  data-index=23 style="min-width: 50px" ><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.antivirus" class=" 
                                                ">AV     <span class="tooltiptext">Anti-Virus</span></a></th>
                                                <th  data-index=24  style="min-width: 50px"><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.backup" class=" 
                                                ">BKP  <span class="tooltiptext">Data Protection (Backup)</span>   </a></th>
                                                <th  data-index=25  style="min-width: 50px"><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.replicated" class=" 
                                                ">REP    <span class="tooltiptext">Replicated</span>   </a></th>
                                                <th  data-index=26 style="min-width: 60px" ><a class="tooltip1" href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.smtp" class=" 
                                                ">SMTP     <span class="tooltiptext">Uses SMTP Relay</span> </a></th>
                                                
                                                <th  data-index=28  style="min-width: 60px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.syslog" class=" 
                                                ">Syslog    </a></th>
                                            
                                                
                                           
                                          
                                                    @if($page_type!='servers')
                                                    <th  data-index=30  style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.cpu_model" class=" 
                                                ">CPU</a></th>
                                                @endif

<!-- 
                                                <th  data-index=31  style="min-width: 120px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.cpu_sockets" class=" 
                                                ">CPU Sockets<i class="fa fa-sort"></i>  </a></th>
                                                  <th  data-index=32  style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.cpu_cores" class=" 
                                                ">CPU Cores<i class="fa fa-sort"></i>  </a></th>
                                                  <th  data-index=33  style="min-width: 140px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.cpu_freq" class=" 
                                                ">CPU Freq(GHz)<i class="fa fa-sort"></i>  </a></th> -->
                                                  <th  data-index=34  style="min-width: 170px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.cpu_hyperthreadings" class=" 
                                                ">CPU Hyperthreadings<i class="fa fa-sort"></i>  </a></th>
                                                  <th  data-index=35  style="min-width: 150px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.cpu_total_cores" class=" 
                                                ">CPU Total Cores <i class="fa fa-sort"></i>  </a></th>
            

       <th data-index=43 style="min-width:50px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.InactiveDate" class="  ">DECOM<br>DATE</a></th>


                                           
                                           
                                        </tr>
                                    </thead>
                                    <tbody id="showdata">
                                        <?php 
                                        function isValidDate($date) {
    return date('Y-m-d', strtotime($date)) === $date;
}


?>
                                          @php  $sno= $qry->perPage() * ($qry->currentPage() - 1);@endphp
                                        @foreach($qry as $q)

                                        <tr data="{{$q->id}}" data-pos="{{$q->position}}" >
                                                     <?php $ssl=DB::table('ssl_certificate as s')->leftjoin('vendors as v','v.id','=','s.cert_issuer')->where('cert_hostname',$q->id)->where('s.is_deleted',0)->first();
                                                     $cert='';
                                                      ?>
                                               @if(@$ssl->cert_type=='internal')
                                                 <?php $cert='Internal Cert';?>
                                          @elseif(@$ssl->cert_type=='public')
                                                <?php $cert=@$ssl->vendor_name.($ssl->vendor_name!=''?'Public Cert':'');?> 
                                          @endif  
 
                                            <td class="text-center">
                                                <div class="btn-group">
                                                       
                                                <button type="button" class="btn btn-alt-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-default-alt-primary" style="">
                                                    <a class="dropdown-item" target="_blank" href="{{url('print-asset')}}?id={{$q->id}}">Print</a>
                                                    <a class="dropdown-item" target="_blank" href="{{url('pdf-asset')}}?id={{$q->id}}">Pdf</a> 

                                                   @if(Auth::user()->role!='read')    
                                                   <a class="dropdown-item" href="{{url('edit-assets')}}?id={{$q->id}}" >Edit</a>
                                                    
                                                    <a class="dropdown-item btnDelete"  data="{{$q->id}}"  href="javascript:void(0)">Delete</a>
                                                        @endif
                                               
                                                </div>
                  
                      <button type="button"   data="{{$q->id}}" data1="{{$cert}}" data2="{{@$ssl->cert_edate!=''?date('Y-M-d',strtotime($ssl->cert_edate)):''}}"  class="btn btn-sm btn-alt-success btnEdit" data-toggle="tooltip" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </button>

        
                                              
                          
                                                </div>
                                            </td>
                                             <td  data-index=0>
                                               

@if(!isset($_GET['search']) && !isset($_GET['advance_search']) && !isset($_GET['field']))

         @if(Auth::user()->role=='admin')    
<i class="fa fa-align-justify"></i>

@endif
@endif

{{++$sno}}</td>
                                       
                                        <td  data-index=42>
                                                @if($q->AssetStatus==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>

                                            <!--  <td data-index="45" class="text-center">
                                                    </td> -->



                                                   <td  data-index=1 class="font-w600">
                                                {{$q->client_display_name}} 
                                            </td>
                                            <td  data-index=2>{{$q->site_name}}</td>
                                                 <td  data-index=3>{{$q->vendor_name}}</td>
                                                      <td  data-index=4>{{$q->model}}</td>
                                                             <td  data-index=5>{{$q->type}}</td>
                                                       <td  data-index=6>{{$q->sn}}</td>



                                              <td  data-index=9 class="text- " >
                                             <div style="display: inline-flex;"> <img class="img-avatar     "    style="object-fit: cover;width: 50px ;height: 50px" src="{{asset('public/asset_icon/')}}/{{$q->asset_icon}}" alt="">
                                                <div class="mt-2">


                                       <a href="javascript:;" data="{{$q->id}}" data1="{{$cert}}" data2="{{@$ssl->cert_edate!=''?date('Y-M-d',strtotime($ssl->cert_edate)):''}}"  class="btnEdit">  
                                        @if($q->asset_type_description=='Storage Expansion')
                                              {{$q->parent_asset_name}}  
                                        @else
                                        {{$q->fqdn}}
                                        
                                        @endif


                                    </a>



                                       <p class="text-secondary mb-0">{{$q->asset_type_description}}</p>
                                                </div>
                                            </div>

                                   </td>
                                                  <td  data-index=10>{{$q->role}}</td>
                                                   <td  data-index=11>{{$q->use_}}</td>
                                                        
                                               <td  data-index=7>{{$q->hostname}}</td>

                                                <td  data-index=12>{{$q->operating_system_name}}</td>
 <td  data-index=37>{{$q->memory}}</td>
   @if($page_type=='servers')
                                                 <td  data-index=30>{{$q->cpu_sockets}} {{$q->cpu_model}} {{$q->cpu_cores}} C @ {{$q->cpu_freq}} GHz</td>
                                                           
                                                @endif
   <td  data-index=8>{{$q->domain_name}}</td>
                             <td  data-index=15>{{$q->vlanId}}</td>
      <td  data-index=14>{{$q->ip_address}}</td>
         <td  data-index=16>
                @if($q->network_zone=='Internal')
                                                           <div class="badge badge-secondary"  >{{$q->network_zone}}</div>
                                            @elseif($q->network_zone=='Secure')
                                                <div class="badge badge-info"  >{{$q->network_zone}}</div>
                                                @elseif($q->network_zone=='Greenzone')
                                                <div class="badge badge-success"  >{{$q->network_zone}}</div>
                                                @elseif($q->network_zone=='Guest')
                                                <div class="badge badge-warning"  >{{$q->network_zone}}</div>
                                                @elseif($q->network_zone=='Semi-Trusted')
                                                <div class="badge  " style="background:#FFFF11;color: black"  >{{$q->network_zone}}</div>
                                                @elseif($q->network_zone=='Public DMZ' || $q->network_zone=='Public' || $q->network_zone=='Servers Public DMZ' )
                                                <div class="badge badge-danger"  >{{$q->network_zone}}</div>
                                                @else
                                                {{$q->network_zone}}
                                                @endif

         </td>
<td  data-index=17>
                                                @if($q->internet_facing==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->internet_facing==2)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
<td  data-index=19>
                                                @if($q->load_balancing==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->load_balancing==2)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                               <td  data-index=20>
                                                @if($q->clustered==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @elseif($q->clustered==2)
                                                         <span class="badge badge-secondary">N/A</span>
                                                          @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>

    <td  data-index=40>
                                                @if($q->HasWarranty==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                                                      <td  data-index="38">

                                                      
                                                  @if($q->SupportStatus=='N/A')
                                                        <span class="badge text-white bg-secondary">
                                                               {{$q->SupportStatus}}
                                                        </span>
                                                        @elseif($q->SupportStatus=='Supported')
                                                        <span class="badge badge-success">
                                                               {{$q->SupportStatus}}
                                                        </span>
                                                        @elseif($q->SupportStatus=='Unassigned')
                                                        <span class="badge text-white bg-orange">
                                                               {{$q->SupportStatus}}
                                                        </span>
                                                        @elseif($q->SupportStatus=='Expired')
                                                            <span class="badge badge-danger">
                                                                   {{$q->SupportStatus}}
                                                        </span>
                                                                   @else
                                                                <span class="badge text-white bg-secondary">
                                                             N/A
                                                        </span>

                                                        @endif

                                                    </td>
                                                                                                                       <?php 

     $contract=DB::table('contract_assets as a')->join('contracts as c','c.id','=','a.contract_id')->where('a.hostname',$q->id)->where('c.contract_status','!=','Inactive')->where(function($query){
        $query->Orwhere('a.status','!=','Inactive');
    $query->Orwhere('a.status',null);
    }) ->where('a.is_deleted',0)->first();
?>                                                <td  data-index=44>{{@$contract->contract_no}}</td>             
                                          
                                           
                                                  


                                                       <td  data-index="39">{{isValidDate($q->warranty_end_date)?date('Y-M-d',strtotime($q->warranty_end_date)):$q->warranty_end_date}} </td>

                                                        <td  data-index=27>
                                                @if($q->ntp==1)
                                                    <span class="badge badge-success">Yes</span>
                                                      @elseif($q->ntp==2)
                                                         <span class="badge badge-secondary">N/A</span>
                                                    @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>

                                               <td  data-index=46>@if($q->ssl_certificate_status=='N/A')
                                                        <span class="badge text-white bg-secondary">
                                                               {{$q->ssl_certificate_status}}
                                                        </span>
                                                        @elseif($q->ssl_certificate_status=='Active')
                                                        <span class="badge badge-success">
                                                               {{$q->ssl_certificate_status}}
                                                        </span>
                                                        @elseif($q->ssl_certificate_status=='Unassigned')
                                                        <span class="badge text-white bg-orange">
                                                               {{$q->ssl_certificate_status}}
                                                        </span>
                                                        @elseif($q->ssl_certificate_status=='Expired/Ended')
                                                            <span class="badge badge-danger">
                                                                   {{$q->ssl_certificate_status}}
                                                        </span>
                                                        @else
                                                                <span class="badge text-white bg-secondary">
                                                               N/A
                                                        </span>
                                                        @endif </td>

                                                                <?php $ssl=DB::table('ssl_certificate as s')->leftjoin('vendors as v','v.id','=','s.cert_issuer')->where('cert_hostname',$q->id)->where('s.is_deleted',0)->first(); ?>
                                                             <td  data-index=47> @if(@$ssl->cert_type=='internal')
                                                  Internal Cert
                                          @elseif(@$ssl->cert_type=='public')
                                                {{@$ssl->vendor_name}}  
                                          @endif
                                      </td>

                                                                 <td  data-index=48>{{@$ssl->cert_edate!=''?date('Y-M-d',strtotime($ssl->cert_edate)):''}}</td>
                       
   
                                             <td  data-index=49>
                                                @if($q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>

                                                     <td  data-index=13>{{$q->app_owner}}</td>
                                  
                                                        
  <td  data-index=29>{{$q->sla}}</td>
                         


 
                                                          

                                             
                                                
   
                                             <td  data-index=18>
                                                @if($q->disaster_recovery==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->disaster_recovery==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                             
                                             <td  data-index=21>
                                                @if($q->monitored==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->monitored==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>

                                             <td  data-index=22>
                                                @if($q->patched==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->patched==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                             <td  data-index=23>
                                                @if($q->antivirus==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                      @elseif($q->antivirus==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                        @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                             <td  data-index=24>
                                                @if($q->backup==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @elseif($q->backup==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span> @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                             <td  data-index=25>
                                                @if($q->replicated==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @elseif($q->replicated==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                           @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                            
                                             <td  data-index=26>
                                                @if($q->smtp==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                      @elseif($q->smtp==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                    @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                            
                                             <td  data-index=28>
                                                @if($q->syslog==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @elseif($q->syslog==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                    @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                          
                                                 



                                                    @if($page_type!='servers')
                                                 <td  data-index=30>{{$q->cpu_sockets}} {{$q->cpu_model}} {{$q->cpu_cores}} C @ {{$q->cpu_freq}} GHz</td>
                                                           
                                                @endif
                                        
                                                          
                                                                    <td  data-index=34>
                                                @if($q->cpu_hyperthreadings==1)
                                                    <span class="badge badge-success">Enabled</span>
                                                    @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                                                    
                                                                     <td  data-index=35>{{$q->cpu_total_cores}}</td>
 
                                                                         <td  data-index=43>              @if($q->InactiveDate!='' && $q->AssetStatus!=1)
                                                        {{date('Y-M-d',strtotime($q->InactiveDate))}}
                                                        @else
                                                        <div class="badge badge-secondary">N/A</div> 
                                                        @endif</td>

                                          
                                          

                                   
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            
                             
                            </div>
                            <div class="row pt-3 mb-2">
                             
                               <div class="col-lg-12   d-inline-flex justify-content-end ">
                                
                        </div>
                        </div>
                        </div>

                    </div>
                    <!-- END Full Table -->
 
                </div>










<?php 
     if($page_type==''){  
        $column_array=array(42,1,2,3,4,5,6,9,10,11,12,14,16,17,19,20,40,27,49,43 );
        
            if(@$no_check->physical_all_columns!='' ){
                        $column_array=explode(',',$no_check->physical_all_columns);
            }
        }
        elseif($page_type=='servers'){
    
                $column_array=array(1,2,3,4,5,6,9,10,11,12,14,16,17,19,20,40,27,49,37,30 );
          if(@$no_check->physical_servers_columns!='' ){
                        $column_array=explode(',',$no_check->physical_servers_columns);
            }
        }
        elseif($page_type=='other'){
 
               $column_array=array(1,2,3,4,5,6 ,9,10,11,12,14,16,40,27,49);
        if(@$no_check->physical_other_columns!='' ){
                        $column_array=explode(',',$no_check->physical_other_columns);
            }
        }
        elseif($page_type=='managed'){
             
                     $column_array=array(1,2,9,10,11,12,37,14,16,17,19,20,13,29,18,21,22,23,24,25,26,28);
            if(@$no_check->physical_managed_columns!='' ){
                        $column_array=explode(',',$no_check->physical_managed_columns);
            }
        }
        elseif($page_type=='support-contracts'){
             
                $column_array=array(1,2,9,10 ,38,44,39);
              if(@$no_check->physical_support_columns!='' ){
                        $column_array=explode(',',$no_check->physical_support_columns);
            }
        }
          elseif($page_type=='ssl-certificate'){
             
                $column_array=array(1,2,9,10,46,47,48);
              if(@$no_check->physical_ssl_columns!='' ){
                        $column_array=explode(',',$no_check->physical_ssl_columns);
            }
        }
          elseif($page_type=='inactive'){
             
                $column_array=array(43,1,2,9,10,11,12,14,16);
              if(@$no_check->physical_inactive_columns!='' ){
                        $column_array=explode(',',$no_check->physical_inactive_columns);
            }
        }
?>




                        
                               
                                           


                             <div class="modal" id="EditColumnModal" tabindex="-1" role="dialog" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
                <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" >Show/Hide Columns</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content">

                                <table class="table table-sm table-striped table-bordered">
                                    <thead>
                                        <th>Column</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td> #  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox1"  {{in_array(0,$column_array)?'checked':''}}  value="0">
                                            <label class="custom-control-label" for="checkbox1"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>Active   </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox2"  {{in_array(42,$column_array)?'checked':''}}  value="42">
                                            <label class="custom-control-label" for="checkbox2"></label>
                                        </div>
                                             </td>
                                        </tr>
                                        <tr>
                                            <td>  Client </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox4"  {{in_array(1,$column_array)?'checked':''}}  value="1">
                                            <label class="custom-control-label" for="checkbox4"></label>
                                        </div>
                                             </td>
                                        </tr>

                                         <tr>
                                            <td> Site  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox5"  {{in_array(2,$column_array)?'checked':''}}  value="2">
                                            <label class="custom-control-label" for="checkbox5"></label>
                                        </div>
                                             </td>
                                        </tr> <tr>
                                            <td> Manufacture  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox6"  {{in_array(3,$column_array)?'checked':''}}  value="3">
                                            <label class="custom-control-label" for="checkbox16"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Model  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox7"  {{in_array(4,$column_array)?'checked':''}}  value="4">
                                            <label class="custom-control-label" for="checkbox7"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Type</td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox8"  {{in_array(5,$column_array)?'checked':''}}  value="5">
                                            <label class="custom-control-label" for="checkbox8"></label>
                                        </div>
                                             </td>
                                        </tr>


                                         <tr>
                                            <td>  SN </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox9"  {{in_array(6,$column_array)?'checked':''}}  value="6">
                                            <label class="custom-control-label" for="checkbox9"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>   FQDN</td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox10"  {{in_array(9,$column_array)?'checked':''}}  value="9">
                                            <label class="custom-control-label" for="checkbox10"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Role </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox11"  {{in_array(10,$column_array)?'checked':''}}  value="10">
                                            <label class="custom-control-label" for="checkbox11"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Environment  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox12"  {{in_array(11,$column_array)?'checked':''}}  value="11">
                                            <label class="custom-control-label" for="checkbox12"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Hostname  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox13"  {{in_array(7,$column_array)?'checked':''}}  value="7">
                                            <label class="custom-control-label" for="checkbox13"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>OS   </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox14"  {{in_array(12,$column_array)?'checked':''}}  value="12">
                                            <label class="custom-control-label" for="checkbox14"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Memory(Gb) </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox15"  {{in_array(37,$column_array)?'checked':''}}  value="37">
                                            <label class="custom-control-label" for="checkbox15"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Domain </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox16"  {{in_array(8,$column_array)?'checked':''}}  value="8">
                                            <label class="custom-control-label" for="checkbox16"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  vLanId </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox17"  {{in_array(15,$column_array)?'checked':''}}  value="15">
                                            <label class="custom-control-label" for="checkbox17"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> IP  Address  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox18"  {{in_array(14,$column_array)?'checked':''}}  value="14">
                                            <label class="custom-control-label" for="checkbox18"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Network Zone  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox19"  {{in_array(16,$column_array)?'checked':''}}  value="16">
                                            <label class="custom-control-label" for="checkbox19"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Internet Facing </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox20"  {{in_array(17,$column_array)?'checked':''}}  value="17">
                                            <label class="custom-control-label" for="checkbox20"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Load Balancing  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox21"  {{in_array(19,$column_array)?'checked':''}}  value="19">
                                            <label class="custom-control-label" for="checkbox21"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Clustered </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox22"  {{in_array(20,$column_array)?'checked':''}}  value="20">
                                            <label class="custom-control-label" for="checkbox22"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>   Support/Warranty</td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox23"  {{in_array(40,$column_array)?'checked':''}}  value="40">
                                            <label class="custom-control-label" for="checkbox23"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Support Status </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox24"  {{in_array(38,$column_array)?'checked':''}}  value="38">
                                            <label class="custom-control-label" for="checkbox24"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Contract #  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox25"  {{in_array(44,$column_array)?'checked':''}}  value="44">
                                            <label class="custom-control-label" for="checkbox25"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Support End Date  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox26"  {{in_array(39,$column_array)?'checked':''}}  value="39">
                                            <label class="custom-control-label" for="checkbox26"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  SSL Certificate </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox27"  {{in_array(27,$column_array)?'checked':''}}  value="27">
                                            <label class="custom-control-label" for="checkbox27"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> SSL Cert Status  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox28"  {{in_array(46,$column_array)?'checked':''}}  value="46">
                                            <label class="custom-control-label" for="checkbox28"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  SSL Cert Issuer </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox29"  {{in_array(47,$column_array)?'checked':''}}  value="47">
                                            <label class="custom-control-label" for="checkbox29"></label>
                                        </div>
                                             </td>
                                        </tr>

                                         <tr>
                                            <td>  SSL Cert Expiration Date </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox30"  {{in_array(48,$column_array)?'checked':''}}  value="48">
                                            <label class="custom-control-label" for="checkbox30"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Managed  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox31"  {{in_array(49,$column_array)?'checked':''}}  value="49">
                                            <label class="custom-control-label" for="checkbox31"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  App Owner </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox32"  {{in_array(13,$column_array)?'checked':''}}  value="13">
                                            <label class="custom-control-label" for="checkbox32"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> SLA  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox33"  {{in_array(29,$column_array)?'checked':''}}  value="29">
                                            <label class="custom-control-label" for="checkbox33"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Disaster Recovery  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox34"  {{in_array(18,$column_array)?'checked':''}}  value="18">
                                            <label class="custom-control-label" for="checkbox34"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>Monitored   </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox35"  {{in_array(21,$column_array)?'checked':''}}  value="21">
                                            <label class="custom-control-label" for="checkbox35"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>Patched   </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox36"  {{in_array(22,$column_array)?'checked':''}}  value="22">
                                            <label class="custom-control-label" for="checkbox36"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  AntiVirus </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox37"  {{in_array(23,$column_array)?'checked':''}}  value="23">
                                            <label class="custom-control-label" for="checkbox37"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Backup </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox38"  {{in_array(24,$column_array)?'checked':''}}  value="24">
                                            <label class="custom-control-label" for="checkbox38"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Replicated </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox39"  {{in_array(25,$column_array)?'checked':''}}  value="25">
                                            <label class="custom-control-label" for="checkbox39"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>SMTP   </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox40"  {{in_array(26,$column_array)?'checked':''}}  value="26">
                                            <label class="custom-control-label" for="checkbox40"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Syslog  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox41"  {{in_array(28,$column_array)?'checked':''}}  value="28">
                                            <label class="custom-control-label" for="checkbox41"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Cpu Model  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox42"  {{in_array(30,$column_array)?'checked':''}}  value="30">
                                            <label class="custom-control-label" for="checkbox42"></label>
                                        </div>
                                             </td>
                                        </tr>
                                    
                                         <tr>
                                            <td> Cpu hyperthreadings  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox46"  {{in_array(34,$column_array)?'checked':''}}  value="34">
                                            <label class="custom-control-label" for="checkbox46"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Cpu Total Cores  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox47"  {{in_array(35,$column_array)?'checked':''}}  value="35">
                                            <label class="custom-control-label" for="checkbox47"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> InactiveDate  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox48"  {{in_array(43,$column_array)?'checked':''}}  value="43">
                                            <label class="custom-control-label" for="checkbox48"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         
                            
                                        
                                    </tbody>
                                </table>

  </div>
                       <div class="block-content block-content-full   bg-light">
                             

                            <button type="button" class="btn btn-sm float-right btn-light" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
     





































<form action="">
                             <div class="modal" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
                <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" id="hostnameDisplay">Filters</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="block-content">

                                       <input type="hidden" name="limit" value="{{$_GET['limit']??10}}">
                                          
                                                <div class="block-content   row ">
                                  <div class="col-sm-3  form-group">     
                                                     <label>Active</label>                 
                                                 <select type="" class="form-control  "   data-style="btn-outline-light border text-dark"      value="" name="asset_status"  >
                                                        <option value="">All</option>
                                                        <option value="1" {{@$_GET['asset_status']==1?'selected':''}}>Yes</option>
                                                        <option value="0" {{isset($_GET['asset_status']) && $_GET['asset_status']==0?'selected':''}}>No</option>
     
                                                    </select>
                             </div>
                                                 <div class="col-sm-3 form-group">
                                          
                 
                                            <label class="   " for="example-hf-email">Hostname</label>
                                       
                                                 <input type="text" class="form-control"   value="{{@$_GET['hostname']}}" name="hostname" placeholder="All"  >
                                            </div>
                                                     
                                   <div class="col-sm-3 form-group">
                                          
                 
                                            <label class="   " for="example-hf-email">Ip Address</label>
                                       
                                                 <input type="text" class="form-control"  value="{{@$_GET['ip_address']}}" name="ip_address" placeholder="All"  >
                                            </div>
                                                                                     

                                                          <div class="col-sm-3  form-group">
                                            <label class="   " for="example-hf-client_id">Client</label>
                                            <?php
                                              $userAccess=explode(',',Auth::user()->access_to_client);

                                            if(Auth::user()->role=='admin'){
                                            $client=DB::Table('clients')->where('is_deleted',0)->where('client_status',1)->orderBy('client_display_name','asc')->get();
                                            }
                                            else{
                                                $client=DB::Table('clients')->whereIn('id',$userAccess)->where('is_deleted',0)->where('client_status',1)->orderBy('client_display_name','asc')->get();   
                                            }
                                             ?>
                              
                                                 <select type="client_id" class="form-control selectpicker"   data-style="btn-outline-light border text-dark" data-live-search="true" id="client_id"  title="All" value="" name="client_id" placeholder="Client"  >
                                           
                                                    @foreach($client as $c)
                                                    <option value="{{$c->id}}" {{@$_GET['client_id']==$c->id?'selected':''}}>{{$c->client_display_name}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                 
                                         
                                        <div class="col-sm-3  form-group">
                                            <label class="   " for="example-hf-client_id">Site</label>
                                          
                                          
                                                 <select type="" class="form-control  {{!isset($_GET['site_id']) || @$_GET['client_id']==''?'selectpicker':''}}" id="site_id"  data-style="btn-outline-light border text-dark"  data-actions-box="true"  data-live-search="true"  title="All" value="" name="site_id[]" multiple=""   >
                                                     <?php
                                            $site=DB::Table('sites')->where('is_deleted',0)->orderBy('site_name','asc') ->get();

                                                          $siteArray=$_GET['site_id'] ?? [];
                                             ?>
                                                         @foreach($site as $s)
                                                    <option value="{{$s->id}}" {{in_array($s->id,$siteArray)?'selected':''}}  >{{$s->site_name}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>

                                                 <div class="col-sm-3  form-group">
                                            <label class="   " for="example-hf-client_id">Type</label>
                                          
                                          
                                                 <select type="" class="form-control  selectpicker   " id="asset_type_id"  data-style="btn-outline-light border text-dark"  data-actions-box="true"  data-live-search="true"  title="All" value="" name="asset_type_id[]" multiple=""   >
                                                     <?php
                                            $asset_type_qry=DB::Table('asset_type')->where('is_deleted',0)->orderBy('asset_type_description','asc') ->get();

                                                          $asset_typeArray=$_GET['asset_type_id'] ?? [];
                                             ?>
                                                         @foreach($asset_type_qry as $s)
                                                    <option value="{{$s->asset_type_id}}" {{in_array($s->asset_type_id,$asset_typeArray)?'selected':''}}  >{{$s->asset_type_description}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>

                                         <div class="col-sm-3 form-group">
                                             <label class="   " for="example-hf-email">VLAN Id</label>
                                  
                                                 <select  type="number" class="form-control select2" id="vlan_id"  name="vlan_id" placeholder="All"  >
                                         <option value="">All</option>
                                            <?php
                                                    if(@$_GET['site_id']!=''){
                                                     
                                            $vlan=DB::Table('network')->where('is_deleted',0) ->where('client_id',$_GET['client_id'])->whereIn('site_id',$_GET['site_id'])->get();
                                            

                                                     ?>
                                                   
                                                    @foreach($vlan as $s)
                                                        <option value="{{$s->id}}" {{@$_GET['vlan_id']==$s->id?'selected':''}}>{{$s->vlan_id
                                                        }}</option>
                                                    @endforeach
                                                <?php } ?>
                                            </select>
                                        </div>


                                            
                                            <div class="col-sm-3 form-group">
                                                        <label class="   " for="example-hf-client_id">Domain</label>
                                                 <select type="text" class="form-control  {{!isset($_GET['domain']) || @$_GET['client_id']==''?'selectpicker':''}}" id="domain"     data-style="btn-outline-light border text-dark" data-actions-box="true" data-live-search="true" title="All" name="domain[]" placeholder="" multiple=""  >
                                                      
                                                              <?php
                                            $client=DB::Table('domains')->where('is_deleted',0) ->orderBy('domain_name','asc') ->get();
                                              $domainArray=$_GET['domain'] ?? [];
                                             ?>
                                                         @foreach($client as $c)
                                                    <option value="{{$c->id}}" {{in_array($c->id,$domainArray)?'selected':''}}>{{$c->domain_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                   
                                  
 
                                   

                                          <div class="col-sm-3 form-group">
                                            <label class="   " for="example-hf-client_id">OS</label>
                                            <?php
                                            $operating_system=DB::Table('operating_systems')->where('is_deleted',0)->orderBy('operating_system_name','asc') ->get();
                                             ?>
                                           <select type="" class="form-control   selectpicker"   data-style="btn-outline-light border text-dark" data-live-search="true" title="All" multiple="" id="os"  value="" name="os[]"   >
                                                
                                                        <?php $osArray=$_GET['os'] ?? [] ?>
                                                    @foreach($operating_system as $c)

                                                    <option value="{{$c->id}}"  {{in_array($c->id,$osArray)?'selected':''}} >{{$c->operating_system_name}}</option>
                                                    @endforeach
                                                    </select>
                                           
                                        </div>

 


                                               



                                             
                                                <div class="col-sm-3 form-group">
                              
                                 
                                            <label class="   " for="example-hf-email">Network Zone</label>
                                       
                                                 <input type="text" class="form-control" list="networkDatalist" value="{{@$_GET['network_zone']}}"   id="network_zone" name="network_zone" placeholder="All"  > 
                                                <datalist id="networkDatalist">
                                                    <?php $use=DB::Table('assets')->select(DB::raw('distinct(network_zone) as network_zone')) ->get(); ?>
                                                    @foreach($use as $u)
                                                        <option value="{{$u->network_zone}}"></option>
                                                    @endforeach

                                                </datalist>
                                        
                                        </div>
              
                                         <div class="col-sm-3 form-group">
                                          
                 
                                            <label class="   " for="example-hf-email">SLA</label>
                                       
                                                 <input type="text" class="form-control"   value="{{@$_GET['sla']}}" name="sla" placeholder="All"  >
                                            </div>

                                             <div class="col-sm-3  form-group">     
                                                     <label>Support Status</label>                 
                                                 <select type="" class="form-control  "   data-style="btn-outline-light border text-dark"      value="" name="SupportStatus"  >
                                                        <option value="">All</option>
                                                    <option value="N/A" {{@$_GET['SupportStatus']==1?'selected':''}}>N/A</option>
                                                    <option value="Supported" {{@$_GET['SupportStatus']=='Supported'?'selected':''}}>Supported</option>
                                                    <option value="Unassigned" {{@$_GET['SupportStatus']=='Unassigned'?'selected':''}}>Unassigned</option>
                                                    <option value="Expired" {{@$_GET['SupportStatus']=='Expired'?'selected':''}}>Expired</option>
 

                                                        </select>
                                            </div>
                                            
                                             <div class="col-sm-3  form-group">     
                                                     <label>SSL Certificate </label>                 
                                                 <select type="" class="form-control  "   data-style="btn-outline-light border text-dark"      value="" name="ntp"  >
                                                        <option value="">All</option>
                                                    <option value="1" {{@$_GET['ntp']==1?'selected':''}}>Yes</option>
                                                    <option value="0"  {{isset($_GET['ntp']) && $_GET['ntp']==0?'selected':''}} >No</option>
 
                                                    </select>
                                            </div>


                                            <div class="col-sm-3  form-group">     
                                                     <label>Internet Facing </label>                 
                                                 <select type="" class="form-control  "   data-style="btn-outline-light border text-dark"      value="" name="internet_facing"  >
                                                        <option value="">All</option>
                                                    <option value="1" {{@$_GET['internet_facing']==1?'selected':''}}>Yes</option>
                                                    <option value="0"  {{isset($_GET['internet_facing']) && $_GET['internet_facing']==0?'selected':''}} >No</option>
 
                                                    </select>
                                            </div>
                                     
                        </div>
                          <div class="block-content block-content-full  text-right bg-light">
                             
 
                               <button class="btn   btn-primary"   name="advance_search"  >Filter</button>
                                                <button type="button" class="btn   btn-danger" data-dismiss="modal" >Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </form> 





                <!-- END Page Content -->
                             <div class="modal" id="viewData" tabindex="-1" role="dialog" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
                <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title"  id="hostnameDisplay">All Info</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content">
                           <table class="table tablemodal">
                   
                 
      
                               
           <tbody>
                 <tr>
                                        <th>Active</th>
                                        <td id="AssetStatusDisplay"></td>
                                    </tr> 
                                    <tr>
                                        <th>Client</th>
                                        <td id="client_display_name"></td>
                                    </tr>
                                    <tr>
                                        <th>Site</th>
                                        <td id="site_name"></td>
                                    </tr>

                                         <tr>
                                        <th>Manufacture</th>
                                        <td id="manufactureDisplay"></td>
                                    </tr>

                                     <tr>
                                        <th>Model</th>
                                        <td id="modelDisplay"></td>
                                    </tr>

                                     <tr>
                                        <th>Type</th>
                                        <td id="typeDisplay"></td>
                                    </tr>

                                   
                                    <tr>
                                        <th>Domain</th>
                                        <td id="domain_nameDisplay"></td>
                                    </tr>
                                    <tr>
                                        <th>FQDN</th>
                                        <td id="fqdnDisplay"></td>
                                    </tr>
                                    <tr>
                                        <th>Role</th>
                                        <td id="roleDisplay"></td>
                                    </tr>
                                    <tr>
                                        <th>Environment</th>
                                        <td id="use_Display"></td>
                                    </tr>
                                    <tr>
                                        <th>OS</th>
                                        <td id="operating_system_nameDisplay"></td>
                                    </tr>
                                
                                    <tr>
                                        <th>IP Address</th>
                                        <td id="ip_addressDisplay"></td>
                                    </tr>
                                    <tr>
                                        <th>vLanId</th>
                                        <td id="vlan_idDisplay"></td>
                                    </tr>
                                     <tr  class="cpuDiv">
                                        <th>Cpu Model</th>
                                        <td id="cpu_modelDisplay"></td>
                                    </tr>
                                     <tr  class="cpuDiv">
                                        <th>Cpu Sockets</th>
                                        <td id="cpu_socketsDisplay"></td>
                                    </tr>
                                     <tr  class="cpuDiv">
                                        <th>Cpu Cores</th>
                                        <td id="cpu_coresDisplay"></td>
                                    </tr>
                                     <tr  class="cpuDiv">
                                        <th>Cpu Freq(Hz)</th>
                                        <td id="cpu_freqDisplay"></td>
                                    </tr>
                                     <tr class="cpuDiv">
                                        <th>Cpu hyperthreadings</th>
                                        <td id="cpu_hyperthreadingsDisplay"></td>
                                    </tr>
                                    <tr>
                                        <th>Cpu  Total Cores</th>
                                        <td id="cpu_total_coresDisplay"></td>
                                    </tr>
                                     <tr id="networkDiv">
                                        <th>Network Zone</th>
                                        <td id="network_zoneDisplay"></td>
                                    </tr>
                                           <tr  class="divHide"> 
                                        <th>Internet Facing</th>
                                        <td id="internet_facingDisplay"></td>
                                    </tr>
                                     <tr>
                                        <th>Memory(Gb)</th>
                                        <td id="memoryDisplay"></td>
                                    </tr> 

                                  <tr  class="divHide">
                                        <th>Clustered</th>
                                        <td id="clusteredDisplay"></td>
                                    </tr>
                                    <tr  class="divHide">
                                        <th>Load Balancing</th>
                                        <td id="load_balancingDisplay"></td>
                                    </tr>

                                    <tr>
                                        <th>Managed</th>
                                        <td id="managedDisplay"></td>
                                    </tr>
                                        <tr  class="divHide">
                                        <th>App Owner</th>
                                        <td id="app_ownerDisplay"></td>
                                    </tr>
                                        <tr class="divHide">
                                        <th>SLA</th>
                                        <td id="slaDisplay"></td>
                                    </tr>

                                     <tr  class="divHide">
                                        <th>Backup</th>
                                        <td id="backupDisplay"></td>
                                    </tr>
                                    <tr  class="divHide">
                                        <th>Replicated</th>
                                        <td id="replicatedDisplay"></td>
                                    </tr>
                                    <tr  class="divHide">
                                        <th>Disaster Recovery</th>
                                        <td id="disaster_recoveryDisplay"></td>
                                    </tr>
                                
                                    
                                       <tr  class="divHide">
                                        <th>Syslog</th>
                                        <td id="syslogDisplay"></td>
                                    </tr>
                                
                                    <tr  class="divHide">
                                        <th>Monitored</th>
                                        <td id="monitoredDisplay"></td>
                                    </tr>
                                    <tr  class="divHide">
                                        <th>Patched</th>
                                        <td id="patchedDisplay"></td>
                                    </tr>
                                  
                                    <tr  class="divHide">
                                        <th>AntiVirus</th>
                                        <td id="antivirusDisplay"></td>
                                    </tr>

 
                                   
                                     
                                     <tr class="divHide">
                                        <th>SMTP</th>
                                        <td id="smtpDisplay"></td>
                                    </tr>
                                  
                               

            
                                
                                     <tr>
                                        <th>Support/Warranty</th>
                                        <td id="HasWarrantyDisplay"></td>
                                    </tr > 
                                     <tr class="supportHide">
                                        <th>Support Status</th>
                                        <td id="SupportStatusDisplay"></td>
                                    </tr> 
                                
                                     <tr class="inactveShow d-none">
                                        <th>InactiveDate</th>
                                        <td id="InactiveDateDisplay"></td>
                                    </tr> 

                                    
                                     <tr class="d-none contractDisplay">
                                        <th>Contract #</th>
                                        <td id="ContractNoDisplay"></td>
                                    </tr> 
                                     <tr class="d-none contractDisplay">
                                        <th>Contract End Date</th>
                                        <td id="ContractStartDateDisplay"></td>
                                    </tr> 
                                      <tr class="divHide">
                                        <th>SSL Certificate</th>
                                        <td id="ntpDisplay"></td>
                                    </tr>

                            <tr class="divHide">
                                        <th>SSL Cert Status</th>
                                        <td id="ssl_cert_statusDisplay"></td>
                                    </tr>

                                     <tr class="divHide">
                                        <th>SSL Cert Issuer</th>
                                        <td id="ssl_cert_issuerDisplay"></td>
                                    </tr>

                                     <tr class="divHide">
                                        <th>SSL Cert Expiration Date</th>
                                        <td id="ssl_cert_edateDisplay"></td>
                                    </tr>

                                    
                                             <tr>
                                        <th>Created By</th>
                                        <td id="created_by"></td>
                                        <td></td><td></td> 
                                    </tr>
                                      <tr>
                                        <th>Created On</th>
                                        <td id="created_at"></td>
                                        <td></td><td></td> 
                                    </tr>
                                        <tr>
                                        <th>Last Modified By</th>
                                        <td id="updated_by"></td>
                                        <td></td><td></td> 
                                    </tr>
                                        <tr>
                                        <th>Last Modified On</th>
                                        <td id="updated_at"></td>
                                        <td></td><td></td> 
                                    </tr>
                                 
                                </tbody>
                           </table>
                            <hr>
                           <h5>Comments</h5>
                           <div id="commentsDisplay"></div>
                        </div>
                        <div class="block-content block-content-full   bg-light">
                                          <a class="btn btn-primary printDiv"  target="_blank">Print</a>
                                   <a class="btn btn-primary pdfDiv" target="_blank">PDF</a>


                            <button type="button" class="btn btn-sm float-right btn-light" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
     

            </main>
            <!-- END Main Container -->
            @endsection('content')

      <?php $column_array=json_encode($column_array);?>


<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" defer=""></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
$(function(){
   @if(Session::has('success'))
             Swal.fire({
  title: '{{Session::get('success')}}',
 
 
  confirmButtonText: 'Ok'
})
             @endif

          
        var page_type="{{$page_type}}";
var  locArray=JSON.parse('<?php echo $column_array?>');


  
 

$('.changeSelect').each(function(i,e){
 
 
    var val=$(this).val()
   
    if($(this).prop('checked')){
    
 $('td[data-index='+val+']').removeClass('d-none')
              $('th[data-index='+val+']').removeClass('d-none')

}
else{
   
     $('td[data-index='+val+']').addClass('d-none')
              $('th[data-index='+val+']').addClass('d-none')
}


if(locArray.length==0){
 
 $('td[data-index='+val+']').removeClass('d-none')
              $('th[data-index='+val+']').removeClass('d-none')
}
});

@if(!isset($_GET['search']) && !isset($_GET['advance_search']) && !isset($_GET['field']) )


         @if(Auth::user()->role=='admin')    
 $("#showdata").sortable({
            delay: 150,
            update: function() {
                var selectedData = new Array();
                var position = new Array();
                $('#showdata  > tr').each(function() {
                    selectedData.push($(this).attr("data"));
                    position.push($(this).attr("data-pos"));
                });


                    
                 $.ajax({
            
                type:'get',
                data:{id:selectedData,position:position,page:'{{@$_GET['page']}}',limit:'{{@$_GET['limit']}}'},
                    url:"{{url('swap-physical-rows')}}",
                async:false,
                success:function(data){
                    
                }
            })

            }

        });


@endif
@endif



$('.changeSelect').change(function(){


 var array=[];
        $('.changeSelect:checked').each(function(){
                array.push($(this).val());
        })
        console.log(array);
    $('td[data-index],th[data-index]').addClass('d-none')
    
    for(var i=0;i<array.length;i++)
    {
            $('td[data-index='+array[i]+']').removeClass('d-none')
              $('th[data-index='+array[i]+']').removeClass('d-none')
    }
     

$.ajax({
    type:'get',
    data:{array:array,type:page_type},
    url:"{{url('change-physical-asset-columns')}}",
    success:function(res){
        console.log(res)
    }
    ,error:function(e) {
        console.log(e)
    }
})


})

             
               $('#showdata').on('click','.btnEdit',function(){
                    var id=$(this).attr('data');
                       var cert=$(this).attr('data1');
                    var certedate=$(this).attr('data2');
                $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-assets')}}',
                    success:function(res){

 
                        $('#viewData').modal('show');
                            $('#client_display_name').html(res.client_display_name)
                            $('#site_name').html(res.site_name)
                                               var managed=res.managed==1?'<div class="badge   badge-success" style="color:white!important;">Managed</div>':'';     
                              $('#hostnameDisplay').html('<img class="img-avatar   atar48" style="object-fit: cover" src="{{asset('public/asset_icon/')}}/'+res.asset_icon+'" alt=""> &nbsp;&nbsp;+'+res.hostname+'['+res.sn+']'+' &nbsp;&nbsp;&nbsp;'+managed)
                            $('#domain_nameDisplay').html(res.domain_name)
                                    
                                  $('#fqdnDisplay').html(res.fqdn)
                            
                            $('#roleDisplay').html(res.role)


                             $('#manufactureDisplay').html(res.vendor_name)
                              $('#modelDisplay').html(res.model)
                               $('#typeDisplay').html(res.type)
                                $('#snDisplay').html(res.sn)
                                 $('#cpu_modelDisplay').html(res.cpu_model)
                                  $('#cpu_socketsDisplay').html(res.cpu_sockets)
                                   $('#cpu_coresDisplay').html(res.cpu_cores)
                                    $('#cpu_freqDisplay').html(res.cpu_freq)
                                        $('#created_at').html(res.created_at)
                               $('#created_by').html(res.created_by!=null?res.created_firstname+' '+res.created_lastname:'')
                                  $('#updated_by').html(res.updated_by!=null?res.updated_firstname+' '+res.updated_lastname:'')
                               $('#updated_at').html(res.updated_at)
                                      $('#cpu_total_coresDisplay').html(res.cpu_total_cores)
                            

        if(res.HasWarranty==1){
                                                        $('.supportHide').removeClass('d-none')
                                                    }
                                                    else{
                                                        $('.supportHide').addClass('d-none')   
                                                    }
                            $('#use_Display').html(res.use_)
                            $('#operating_system_nameDisplay').html(res.operating_system_name)
                            $('#app_ownerDisplay').html(res.app_owner)
                            $('#ip_addressDisplay').html(res.ip_address)
                            $('#vlan_idDisplay').html(res.vlanId)
                            $('#app_ownerDisplay').html(res.app_owner)
                     var network_zone=res.network_zone;
                             if(res.network_zone=='Internal'){
                                                           network_zone='<div class="badge badge-secondary"  >'+res.network_zone+'</div>';
                                            }
                                            else if(res.network_zone=='Secure'){
                                             network_zone='<div class="badge badge-info"  >'+res.network_zone+'</div>';
                                         }
                                                else if(res.network_zone=='Greenzone'){
                                                network_zone='<div class="badge badge-success"  >'+res.network_zone+'</div>';
                                          }
                                                else if(res.network_zone=='Guest'){
                                                network_zone='<div class="badge badge-warning"  >'+res.network_zone+'</div>';
                                                } else if(res.network_zone=='Semi-Trusted'){
                                                network_zone='<div class="badge  " style="background:#FFFF11;color: black"  >'+res.network_zone+'</div>';;
                                                } else if(res.network_zone=='Public DMZ' || res.network_zone=='Public' || res.network_zone=='Servers Public DMZ' ){
                                                network_zone='<div class="badge badge-danger"  >'+res.network_zone+'</div>';
                                                }


                            $('#network_zoneDisplay').html(network_zone)

                            $('#SupportStatusDisplay').html(res.SupportStatus)
                            $('#InactiveDateDisplay').html(res.InactiveDate)
                            
    
                             $('#managedDisplay').html(res.managed=='1'?'<div class="badge badge-success">Yes</div>':'<div class="badge badge-danger">No</div>') 
                            if(res.asset_type_name=='Physical Server'){
                                    $('.cpuDiv').removeClass('d-none')
                            }
                            else{
                                 $('.cpuDiv').addClass('d-none')   
                            }

                         $('#HasWarrantyDisplay').html(res.HasWarranty=='1'?'<div class="badge badge-success">Yes</div>':'<div class="badge badge-danger">No</div>')
                          $('#AssetStatusDisplay').html(res.AssetStatus=='1'?'<div class="badge badge-success">Yes</div>':'<div class="badge badge-danger">No</div>')

                             if(res.internet_facing==2){
                                    $('#internet_facingDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.internet_facing==1){
                                 $('#internet_facingDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#internet_facingDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                           
                           if(res.disaster_recovery==2  || res.managed!=1){
                                    $('#disaster_recoveryDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.disaster_recovery==1){
                                 $('#disaster_recoveryDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#disaster_recoveryDisplay').html('<div class="badge badge-danger">No</div>');
                            }


                           if(res.load_balancing==2 || res.managed!=1){
                                    $('#load_balancingDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.load_balancing==1){
                                 $('#load_balancingDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#load_balancingDisplay').html('<div class="badge badge-danger">No</div>');
                            }

                            if(res.clustered==2 || res.managed!=1){
                                    $('#clusteredDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.clustered==1){
                                 $('#clusteredDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#clusteredDisplay').html('<div class="badge badge-danger">No</div>');
                            }

                       if(res.monitored==2 || res.managed!=1){
                                    $('#monitoredDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.monitored==1){
                                 $('#monitoredDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#monitoredDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                            

                            if(res.patched==2 || res.managed!=1){
                                    $('#patchedDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.patched==1){
                                 $('#patchedDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#patchedDisplay').html('<div class="badge badge-danger">No</div>');
                            }

                              if(res.antivirus==2 || res.managed!=1){
                                    $('#antivirusDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.antivirus==1){
                                 $('#antivirusDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#antivirusDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                      
                               if(res.backup==2 || res.managed!=1){
                                    $('#backupDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.backup==1){
                                 $('#backupDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#backupDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                      
                              
                         if(res.replicated==2 || res.managed!=1){
                                    $('#replicatedDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.replicated==1){
                                 $('#replicatedDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#replicatedDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                             

                             if(res.smtp==2 || res.managed!=1){
                                    $('#smtpDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.smtp==1){
                                 $('#smtpDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#smtpDisplay').html('<div class="badge badge-danger">No</div>');
                            }

                              if(res.ntp==2 || res.managed!=1){
                                    $('#ntpDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.ntp==1){
                                 $('#ntpDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#ntpDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                             
                              if(res.syslog==2 || res.managed!=1){
                                    $('#syslogDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.syslog==1){
                                 $('#syslogDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#syslogDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                    
                     
  $('#cpu_hyperthreadingsDisplay').html(res.cpu_hyperthreadings=='1'?'<div class="badge badge-success">Yes</div>':'<div class="badge badge-danger">No</div>')


                                                    if(res.SupportStatus=='N/A'){
                                                     $('#SupportStatusDisplay').html('<span class="badge text-white bg-secondary">'+res.SupportStatus+'</span>');
                                                        }
                                                        else if(res.SupportStatus=='Supported'){
                                                        $('#SupportStatusDisplay').html('<span class="badge badge-success">'+res.SupportStatus+'</span>');
                                                                 
                                                         }

                                                        else if(res.SupportStatus=='Unassigned'){
                                                        $('#SupportStatusDisplay').html('<span class="badge text-white bg-orange">'+res.SupportStatus+'</span>');
                                                         }else if(res.SupportStatus=='Expired'){
                                                            $('#SupportStatusDisplay').html('<span class="badge badge-danger">'+res.SupportStatus+'</span>');
                                                        }
                                                        else{
                                                          $('#SupportStatusDisplay').html('<span class="badge text-white bg-secondary">N/A</span>');  
                                                        }

                                                                if(res.ssl_certificate_status=='N/A'){
                                                     $('#ssl_cert_statusDisplay').html('<span class="badge text-white bg-secondary">'+res.ssl_certificate_status+'</span>');
                                                        }
                                                        else if(res.ssl_certificate_status=='Active'){
                                                        $('#ssl_cert_statusDisplay').html('<span class="badge badge-success">'+res.ssl_certificate_status+'</span>');
                                                                 
                                                         }

                                                        else if(res.ssl_certificate_status=='Unassigned'){
                                                        $('#ssl_cert_statusDisplay').html('<span class="badge text-white bg-orange">'+res.ssl_certificate_status+'</span>');
                                                         }else if(res.ssl_certificate_status=='Expired'){
                                                            $('#ssl_cert_statusDisplay').html('<span class="badge badge-danger">'+res.ssl_certificate_status+'</span>');
                                                        }
                                                           else{
                                                          $('#ssl_cert_statusDisplay').html('<span class="badge text-white bg-secondary">N/A</span>');  
                                                        }

                                $('#ssl_cert_issuerDisplay').html(cert)
                                $('#ssl_cert_edateDisplay').html(certedate)


                                  $('#slaDisplay').html(res.sla)
                                       
                                              $('#memoryDisplay').html(res.memory)
                                                    $('#commentsDisplay').html(res.comments)
             
                         
  if(res.AssetStatus==1){
                            $('.inactveShow').addClass('d-none')
                }   
                else{
                            $('.inactveShow').removeClass('d-none')
                }      
                        

      $('.printDiv').attr('href','{{url('print-asset')}}?id='+id)
                                $('.pdfDiv').attr('href','{{url('pdf-asset')}}?id='+id)



                
  $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-contract-asset')}}',
                    success:function(res){
                    if(res==''){
                           $('.contractDisplay').addClass('d-none');   
                


                }
                else{
                     $('.contractDisplay').removeClass('d-none');
                        $('#ContractStartDateDisplay').html(res.contract_end_date)
                        $('#ContractNoDisplay').html('<a  target="_blank" href="{{url("print-contract")}}?id='+res.id+'">'+res.contract_no+'</a>')
                }
              
                 

                       
                    }
                })


 $('.ip').remove()
  $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-asset-ip')}}',
                    success:function(res){
                            var html='';
                            for(var i=0;i<res.length;i++){
                                html+='<tr class="ip"><td><b>'+res[i].ip_address_name+'</b></td><td>'+res[i].ip_address_value+'</td></tr>';
                            }
                           $('#networkDiv').after(html);   
                
         
                    }
                })







                    }
                })

               })

@if(isset($_GET['advance_search']) && $_GET['client_id']!='')

run('{{$_GET['client_id']}}','on')
@endif




function run(id,on){ 
    $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('getSiteByClientId')}}',
        success:function(res){
            var html='';
                   var check='{{@$site_id}}';
                        check=check.split(',');
            for(var i=0;i<res.length;i++){
                if(on){
                        if(check.includes(String(res[i].id))){
                           html+='<option value="'+res[i].id+'" selected>'+res[i].site_name+'</option>';                            
                        }
                        else{
                                 html+='<option value="'+res[i].id+'" >'+res[i].site_name+'</option>';
                        }
                }
                else{
                html+='<option value="'+res[i].id+'" >'+res[i].site_name+'</option>';
                }
            } 

            $('#site_id').html(html);
            $('#site_id').selectpicker('refresh');
        }
    })
       $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('getDomainByClientId')}}',
        success:function(res){
            var html='';

               var check='{{@$domain}}';
                        check=check.split(',');
          
            for(var i=0;i<res.length;i++){
                    if(on){
                        if(check.includes(String(res[i].id))){
                           html+='<option value="'+res[i].id+'" selected>'+res[i].domain_name+'</option>';                            
                        }
                        else{
                                 html+='<option value="'+res[i].id+'" >'+res[i].domain_name+'</option>';
                        }
                }
                else{
                html+='<option value="'+res[i].id+'" >'+res[i].domain_name+'</option>';
            }
        }
         
            $('#domain').html(html);
              $('#domain').selectpicker('refresh');
        }
    })
   }

$('#site_id').change(function(){
    var id=$(this).val()
    var client_id=$('#client_id').val();
    $.ajax({
        type:'get',
        data:{site_id:id,client_id:client_id},
        url:'{{url('getVlanIdAll')}}',
        success:function(res){
            var html='';
             html+='<option value>Select Vlan Id</option>';
            for(var i=0;i<res.length;i++){
                html+='<option value="'+res[i].id+'"   >'+res[i].vlan_id+'</option>';
            }
            $('#network_zone').val('');
            $('#ip_address').val('');
            $('#vlan_id').select2('destroy');
            $('#vlan_id').html(html);
$('#vlanInfo').addClass('d-none')
            $('#vlan_id').select2();
        }
    })
       
})

$('#client_id').change(function(){
    var id=$(this).val()

    run(id)

})



               $('#showdata').on('click','.btnDelete',function(){
                    var id=$(this).attr('data');
                   
                    var c=confirm("Are you sure want to delete this Assets");
                    if(c){
                        window.location.href="{{url('delete-physical-assets')}}?id="+id;
                    }
                            })  
           })
</script>
