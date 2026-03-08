<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use Auth;

class ExportAssets implements FromView
{
  protected $request;

  function __construct($request)
  {
    $this->request = $request;
  }

  public function view(): View
  {
    $page_type = '';

    $no_check = DB::Table('settings')->where('user_id', Auth::id())->first();
    $_GET['asset_type'] = $_GET['asset_type'] ?? '';
    if (isset($_GET['limit']) && $_GET['limit'] != '') {
      $limit = $_GET['limit'];

      if ($no_check != '') {
        if ($page_type == '') {
          DB::table('settings')->where('user_id', Auth::id())->update(['physical_asset' => $limit]);
        } elseif ($page_type == 'servers') {
          DB::table('settings')->where('user_id', Auth::id())->update(['asset_physical_server' => $limit]);
        } elseif ($page_type == 'other') {
          DB::table('settings')->where('user_id', Auth::id())->update(['asset_physical_others' => $limit]);
        } elseif ($page_type == 'managed') {
          DB::table('settings')->where('user_id', Auth::id())->update(['asset_physical_managed' => $limit]);
        } elseif ($page_type == 'support-contracts') {
          DB::table('settings')->where('user_id', Auth::id())->update(['asset_physical_support' => $limit]);
        } elseif ($page_type == 'ssl-certificate') {
          DB::table('settings')->where('user_id', Auth::id())->update(['asset_physical_ssl' => $limit]);
        } elseif ($page_type == 'inactive') {
          DB::table('settings')->where('user_id', Auth::id())->update(['asset_physical_inactive' => $limit]);
        }
      } else {
        if ($page_type == '') {

          DB::table('settings')->insert(['user_id' => Auth::id(), 'physical_asset' => $limit]);
        } elseif ($page_type == 'servers') {

          DB::table('settings')->insert(['user_id' => Auth::id(), 'asset_physical_server' => $limit]);
        } elseif ($page_type == 'other') {
          DB::table('settings')->insert(['user_id' => Auth::id(), 'asset_physical_others' => $limit]);
        } elseif ($page_type == 'managed') {
          DB::table('settings')->insert(['user_id' => Auth::id(), 'asset_physical_managed' => $limit]);
        } elseif ($page_type == 'support-contracts') {
          DB::table('settings')->insert(['user_id' => Auth::id(), 'asset_physical_support' => $limit]);
        } elseif ($page_type == 'ssl-certificate') {
          DB::table('settings')->insert(['user_id' => Auth::id(), 'asset_physical_ssl' => $limit]);
        } elseif ($page_type == 'inactive') {
          DB::table('settings')->insert(['user_id' => Auth::id(), 'asset_physical_inactive' => $limit]);
        }
      }
    } else {

      if ($no_check != '') {

        if ($no_check->physical_asset != '') {

          if ($page_type == '') {
            $limit = $no_check->physical_asset;
          } elseif ($page_type == 'servers') {
            if ($no_check->asset_physical_server != '') {
              $limit = $no_check->asset_physical_server;
            } elseif ($page_type == 'other') {
              if ($no_check->asset_physical_others != '') {
                $limit = $no_check->asset_physical_others;
              }
            } elseif ($page_type == 'managed') {
              if ($no_check->asset_physical_managed != '') {
                $limit = $no_check->asset_physical_managed;
              }
            } elseif ($page_type == 'support-contracts') {
              if ($no_check->asset_physical_support != '') {
                $limit = $no_check->asset_physical_support;
              }
            } elseif ($page_type == 'ssl-certificate') {
              if ($no_check->asset_physical_ssl != '') {
                $limit = $no_check->asset_physical_ssl;
              }
            } elseif ($page_type == 'inactive') {
              if ($no_check->asset_physical_inactive != '') {
                $limit = $no_check->asset_physical_inactive;
              }
            }
          }
        }
      }
    }
    $userAccess = explode(',', Auth::user()->access_to_client);

    if (sizeof($_GET) > 0) {




      $orderby = 'asc';
      $field = 'a.id';
      if (isset($_GET['orderBy'])) {
        $orderby = $_GET['orderBy'];
        $field = $_GET['field'];
      }



      $cond = '';
      if (isset($_GET['client_id'])  && $_GET['client_id'] != '') {
        $client_id = $_GET['client_id'];
        $cond .= " and a.client_id ='$client_id'";
      }
      if (isset($_GET['site_id']) && sizeof($_GET['site_id']) > 0) {

        $site_id = implode(',', $_GET['site_id']);
        $cond .= " and a.site_id in ($site_id)";
      }

      if (isset($_GET['domain']) && sizeof($_GET['domain']) > 0) {
        $domain = implode(',', $_GET['domain']);
        $cond .= " and a.domain in ($domain)";
      }
      if (isset($_GET['system_category']) && sizeof($_GET['system_category']) > 0) {
        $system_category = implode(',', $_GET['system_category']);
        $cond .= " and a.system_category in ($system_category)";
      }
      if (isset($_GET['system_type']) && sizeof($_GET['system_type']) > 0) {
        $system_type = implode(',', $_GET['system_type']);
        $cond .= " and a.system_type in ($system_type)";
      }
      if (isset($_GET['asset_type_id']) && sizeof($_GET['asset_type_id']) > 0) {

        $asset_type_id = implode(',', $_GET['asset_type_id']);
        $cond .= " and a.asset_type_id in ($asset_type_id)";
      }


      if (isset($_GET['asset_type'])  && $_GET['asset_type'] != '') {
        $asset_type = $_GET['asset_type'];
        $cond .= " and a.asset_type='$asset_type'";
      }

      if (isset($_GET['asset_status'])  && $_GET['asset_status'] != '') {
        $asset_status = $_GET['asset_status'];
        $cond .= " and a.AssetStatus='$asset_status'";
      }
      if (isset($_GET['hostname'])  && $_GET['hostname'] != '') {
        $hostname = $_GET['hostname'];
        $cond .= " and a.hostname='$hostname'";
      }
      if (isset($_GET['sla'])  && $_GET['sla'] != '') {
        $sla = $_GET['sla'];
        $cond .= " and a.sla='$sla'";
      }

      if (isset($_GET['os']) && sizeof($_GET['os']) > 0) {

        $os = implode(',', $_GET['os']);
        $cond .= " and a.os in ($os)";
      }


      if (isset($_GET['ntp'])  && $_GET['ntp'] != '') {
        $ntp = $_GET['ntp'];
        if ($_GET['ntp'] == 2) {
          $cond .= " and a.ssl_certificate_status='Unassigned'";
        } else {
          $cond .= " and a.ntp='$ntp'";
        }
      }
      if (isset($_GET['internet_facing'])  && $_GET['internet_facing'] != '') {
        $internet_facing = $_GET['internet_facing'];
        $cond .= " and a.internet_facing='$internet_facing'";
      }
      if (isset($_GET['SupportStatus'])  && $_GET['SupportStatus'] != '') {
        $SupportStatus = $_GET['SupportStatus'];

        if ($_GET['SupportStatus'] == 'N/A') {
          $cond .= " and (a.SupportStatus='N/A' || a.SupportStatus is null || a.SupportStatus='') ";
        } else {

          if ($_GET['SupportStatus'] == 'End Of Life' || $_GET['SupportStatus'] == 'Other Partner' || $_GET['SupportStatus'] == 'Forgone') {
            $cond .= " and a.NotSupportedReason='$SupportStatus' ";
          } else {

            $cond .= " and a.SupportStatus='$SupportStatus' ";
          }
        }
      }

      if (isset($_GET['ip_address'])  && $_GET['ip_address'] != '') {
        $ip_address = $_GET['ip_address'];
        $cond .= " and a.ip_address='$ip_address'";
      }

      if (isset($_GET['vlan_id'])  && $_GET['vlan_id'] != '') {
        $vlan_id = $_GET['vlan_id'];
        $cond .= " and a.vlan_id='$vlan_id'";
      }
      if (isset($_GET['network_zone'])  && $_GET['network_zone'] != '') {
        $network_zone = $_GET['network_zone'];
        $cond .= " and a.network_zone='$network_zone'";
      }





      if (isset($_GET['os']) && sizeof($_GET['os']) > 0) {
        $os = implode(',', $_GET['os']);
        $cond .= " and a.os in ($os)";
      }

      if (isset($_GET['manufacturer']) && sizeof($_GET['manufacturer']) > 0) {
        $manufacturer = implode(',', $_GET['manufacturer']);
        $cond .= " and a.manufacturer in ($manufacturer)";
      }

      if (isset($_GET['use_'])  && $_GET['use_'] != '') {
        $use_ = $_GET['use_'];
        $cond .= " and a.use_='$use_'";
      }

      if (isset($_GET['model'])  && $_GET['model'] != '') {
        $model = $_GET['model'];
        $cond .= " and a.model='$model'";
      }

      if (isset($_GET['managed'])  && $_GET['managed'] != '') {
        $managed = $_GET['managed'];
        $cond .= " and a.managed='$managed'";
      }


      if (isset($_GET['asset_type_id']) && sizeof($_GET['asset_type_id']) > 0) {

        $asset_type_id = implode(',', $_GET['asset_type_id']);
        $cond .= " and a.asset_type_id in ($asset_type_id)";
      }




      $sear = @$_GET['search'];

      if (Auth::user()->role == 'admin') {

        $qry = DB::table('assets as a')->where(function ($query) use ($sear) {

          $query->Orwhere('a.location', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.hostname', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.fqdn', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.role', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.model', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.type', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.sn', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.cpu_model', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('d.domain_name', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.ip_address', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('n.vlan_id', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.app_owner', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.sla', 'like', '%' . @$_GET['search'] . '%');
          $query->OrwhereRaw("exists (select 1
              from asset_comments i 
              where i.asset_id = a.id and
                    (i.comment LIKE '%$sear%'  )
             )");
          $query->OrwhereRaw("exists (select 1
              from asset_ip_addresses i 
              where i.asset_id = a.id and
                    (i.ip_address_value LIKE '%$sear%' or  i.ip_address_name LIKE '%$sear%' )
             )");
        })->select('a.*', 's.site_name', 'p.hostname as parent_asset_name', 'd.domain_name', 'sc.domain_name as system_category_name', 'st.domain_name as system_type_name', 'c.client_display_name', 'o.operating_system_name', 'm.vendor_name', 'at.asset_icon', 'at.asset_type_description', 'n.vlan_id  as vlanId', 'c.logo', 'n.mask')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')->leftjoin('system_category as sc', 'sc.id', '=', 'a.system_category')->leftjoin('system_types as st', 'st.id', '=', 'a.system_type')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->leftjoin('network as n', 'a.vlan_id', '=', 'n.id')->leftjoin('assets as p', 'p.id', '=', 'a.parent_asset')->whereRaw("a.is_deleted=0 $cond")->where('a.is_deleted', 0)->orderBy($field, $orderby)->get();
      } else {

        $qry = DB::table('assets as a')->where(function ($query) use ($sear) {

          $query->Orwhere('a.location', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.hostname', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.fqdn', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.role', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.model', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.type', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.sn', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.cpu_model', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('d.domain_name', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.ip_address', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('n.vlan_id', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.app_owner', 'like', '%' . @$_GET['search'] . '%');
          $query->Orwhere('a.sla', 'like', '%' . @$_GET['search'] . '%');
          $query->OrwhereRaw("exists (select 1
              from asset_comments i 
              where i.asset_id = a.id and
                    (i.comment LIKE '%$sear%'  )
             )");
          $query->OrwhereRaw("exists (select 1
              from asset_ip_addresses i 
              where i.asset_id = a.id and
                    (i.ip_address_value LIKE '%$sear%' or  i.ip_address_name LIKE '%$sear%' )
             )");
        })->select('a.*', 's.site_name', 'p.hostname as parent_asset_name', 'd.domain_name', 'sc.domain_name as system_category_name', 'st.domain_name as system_type_name', 'c.client_display_name', 'o.operating_system_name', 'm.vendor_name', 'at.asset_icon', 'at.asset_type_description', 'n.vlan_id  as vlanId', 'c.logo', 'n.mask')->join('clients as c', 'c.id', '=', 'a.client_id')->join('sites as s', 's.id', '=', 'a.site_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('system_category as sc', 'sc.id', '=', 'a.system_category')->leftjoin('system_types as st', 'st.id', '=', 'a.system_type')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->leftjoin('network as n', 'a.vlan_id', '=', 'n.id')->leftjoin('assets as p', 'p.id', '=', 'a.parent_asset')->whereRaw("a.is_deleted=0 $cond")->where('a.is_deleted', 0)->whereIn('a.client_id', $userAccess)->orderBy($field, $orderby)->get();
      }
    } else {

      if (Auth::user()->role == 'admin') {


        $qry = DB::table('assets as a')->select('a.*', 's.site_name', 'p.hostname as parent_asset_name', 'd.domain_name', 'sc.domain_name as system_category_name', 'st.domain_name as system_type_name', 'c.client_display_name', 'o.operating_system_name', 'm.vendor_name', 'at.asset_icon', 'at.asset_type_description', 'n.vlan_id  as vlanId', 'c.logo', 'n.mask')->leftjoin('clients as c', 'c.id', '=', 'a.client_id')->leftjoin('sites as s', 's.id', '=', 'a.site_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('system_category as sc', 'sc.id', '=', 'a.system_category')->leftjoin('system_types as st', 'st.id', '=', 'a.system_type')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->leftjoin('network as n', 'a.vlan_id', '=', 'n.id')->leftjoin('assets as p', 'p.id', '=', 'a.parent_asset')->where('a.is_deleted', 0)->orderBy('a.id', 'asc')->get();
      } else {

        $qry = DB::table('assets as a')->select('a.*', 's.site_name', 'p.hostname as parent_asset_name', 'd.domain_name', 'sc.domain_name as system_category_name', 'st.domain_name as system_type_name', 'c.client_display_name', 'o.operating_system_name', 'm.vendor_name', 'at.asset_icon', 'at.asset_type_description', 'n.vlan_id  as vlanId', 'c.logo', 'n.mask')->leftjoin('clients as c', 'c.id', '=', 'a.client_id')->leftjoin('sites as s', 's.id', '=', 'a.site_id')->leftjoin('operating_systems as o', 'o.id', '=', 'a.os')->leftjoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')->leftjoin('domains as d', 'd.id', '=', 'a.domain')->leftjoin('system_category as sc', 'sc.id', '=', 'a.system_category')->leftjoin('system_types as st', 'st.id', '=', 'a.system_type')->leftjoin('vendors as m', 'm.id', '=', 'a.manufacturer')->leftjoin('network as n', 'a.vlan_id', '=', 'n.id')->leftjoin('assets as p', 'p.id', '=', 'a.parent_asset')->where('a.is_deleted', 0)->whereIn('a.client_id', $userAccess)->orderBy('a.id', 'asc')->get();
      }
    }

    if ($_GET['asset_type'] == 'physical') {
      return view('exports.ExportPhysical', [
        'qry' => $qry,
        'page_type' => $page_type,
        'type' => $_GET['asset_type']

      ]);
    } else {
      return view('exports.ExportVirtual', [
        'qry' => $qry,
        'page_type' => $page_type,
        'type' => $_GET['asset_type']

      ]);
    }
  }
}
