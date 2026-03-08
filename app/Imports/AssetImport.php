<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use DB;

class AssetImport implements ToCollection, WithStartRow
{
    public $data;
    private $pageType;

    public function __construct($pageType)
    {
        $this->pageType = $pageType;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function startRow(): int
    {
        return 2;
    }
    public function collection(Collection $rows)
    {

        // $columns = $this->transposeRowsToColumns($rows);
        // dd($columns);


        $array = array();



        foreach ($rows as $r) {

            $clients = DB::table('clients')->where('client_display_name', trim($r[0]))->where('is_deleted', 0)->first();
            $sites = DB::table('sites')->where('site_name', trim($r[1]))->where('client_id', @$clients->id)->where('is_deleted', 0)->first();
            if ($this->pageType == 'workstation') {
                $domain = DB::table('domains')->where('domain_name', trim($r[7]))->where('is_deleted', 0)->first();
                $addomain = DB::table('domains')->where('domain_name', trim($r[8]))->where('is_deleted', 0)->first();
                $os = DB::table('operating_systems')->where('operating_system_name', trim($r[4]))->where('is_deleted', 0)->first();
                $manufacturer = DB::table('vendors')->where('vendor_name', trim($r[10]))->where('is_deleted', 0)->first();


                if ($clients != '') {
                    DB::Table('assets')->insert([
                        'asset_type' => 'workstation',
                        'client_id' => @$clients->id,
                        'site_id' => @$sites->id,
                        'platform' => @$r[2],
                        'asset_type_id' => @$r[3],
                        'os' => @$os->id,
                        'role' => $r[5],
                        'hostname' => $r[6],
                        'domain' => @$domain->id,
                        'device_info_ad_domain' => @$addomain->id,
                        'device_info_ou' => $r[9],  
                        'manufacturer' => @$manufacturer->id,
                        'model' => $r[11],
                        'type' => $r[12],
                        'sn' => $r[13],
                        'cpu_model' => $r[14],
                        'memory' => $r[15],
                        'AssetStatus' => 1,
                    ]);
                } else {
                    if (!in_array($r[1], $array)) {

                        $array[] = $r[1];
                    }
                }
            } elseif ($this->pageType == 'mobile') {
                $manufacturer = DB::table('vendors')->where('vendor_name', trim($r[5]))->where('is_deleted', 0)->first();
                $os = DB::table('operating_systems')->where('operating_system_name', trim($r[3]))->where('is_deleted', 0)->first();


                if ($clients != '') {
                    DB::Table('assets')->insert([
                        'asset_type' => 'mobile',
                        'client_id' => @$clients->id,
                        'site_id' => @$sites->id,
                        'platform' => @$r[2],
                        'os' => @$os->id,
                        'role' => $r[4],
                        'manufacturer' => @$manufacturer->id,
                        'model' => $r[6],
                        'type' => $r[7],
                        'sn' => $r[8],
                        'cpu_model' => $r[9],
                        'memory' => $r[10],
                        'AssetStatus' => 1
                    ]);
                } else {
                    if (!in_array($r[1], $array)) {

                        $array[] = $r[1];
                    }
                }
                // $domain = DB::table('domains')->where('domain_name', trim($r[8]))->where('is_deleted', 0)->first();
                // $manufacturer = DB::table('vendors')->where('vendor_name', trim($r[3]))->where('is_deleted', 0)->first();
                // $os = DB::table('operating_systems')->where('operating_system_name', trim($r[11]))->where('is_deleted', 0)->first();


                // if ($clients != '') {
                //     DB::Table('assets')->insert(['asset_type' => 'mobile', 'client_id' => @$clients->id, 'site_id' => @$sites->id, 'manufacturer' => @$manufacturer->id, 'model' => $r[4], 'type' => $r[5], 'sn' => $r[6], 'hostname' => $r[7], 'domain' => @$domain->id, 'fqdn' => $r[7] . '.' . $r[8], 'role' => $r[9], 'use_' => $r[10], 'os' => @$os->id, 'app_owner' => $r[12], 'ip_address' => $r[13], 'vlan_id' => $r[14], 'network_zone' => $r[15], 'internet_facing' => $r[16], 'disaster_recovery' => $r[17], 'load_balancing' => $r[18], 'clustered' => $r[19], 'monitored' => $r[20], 'patched' => $r[21], 'antivirus' => $r[22], 'backup' => $r[23], 'replicated' => $r[24], 'smtp' => $r[25], 'ntp' => $r[26], 'syslog' => $r[27], 'HasWarranty' => $r[28], 'AssetStatus' => $r[29], 'SLA' => $r[30], 'cpu_model' => $r[31], 'cpu_sockets' => $r[33], 'cpu_cores' => $r[34], 'cpu_freq' => $r[35], 'cpu_hyperthreadings' => $r[36], 'cpu_total_cores' => $r[33] * $r[34], 'memory' => $r[36], 'comments' => $r[37]]);
                // } else {
                //     if (!in_array($r[1], $array)) {

                //         $array[] = $r[1];
                //     }
                // }
            } elseif($this->pageType == 'user'){
                $addomain = DB::table('domains')->where('domain_name', trim($r[10]))->where('is_deleted', 0)->first();
                $azdomain = DB::table('domains')->where('domain_name', trim($r[11]))->where('is_deleted', 0)->first();
                $departments = DB::table('departments')->where('name', trim($r[16]))->where('is_deleted', 0)->first();
                $managers = DB::table('managers')->where('name', trim($r[17]))->where('is_deleted', 0)->first();

                if(!$departments){
                    $data = DB::table('departments')->insertGetId([
                        'name' => $r[17],
                    ]);
                    $dep = $data;
                } else {
                    $dep = $departments->id;
                }


                if ($clients != '') {
                    DB::Table('tech_spec_users')->insert([
                        'client_id' => @$clients->id,
                        'site_id' => @$sites->id,
                        'user_type' => @$r[2],
                        'start_date' => @$r[3] ? date('Y-m-d', strtotime(@$r[3])) : '',
                        'salutation' => $r[4],
                        'firstname' => $r[5],
                        'lastname' => $r[6],
                        'contact_email' => $r[7],
                        'contact_telephone' => $r[8],
                        'network_user_id' => $r[9],
                        'network_ad_domain' => @$addomain->id,
                        'network_azure_domain' => @$azdomain->id,
                        'network_corporate_email' => $r[12],
                        'network_corporate_telephone' => $r[13],
                        'network_extension' => $r[14],
                        'employee_no' => $r[15],
                        'employee_department' => @$dep,
                        'employee_manager' => @$managers->id,
                        'status' => 1
                    ]);
                } else {
                    if (!in_array($r[1], $array)) {

                        $array[] = $r[1];
                    }
                }
            }
        }

        $this->data = $array;
        return 1;
        // return new Availibility([

        //     'class_id'     => $row[0],
        //     'date'    => $date,
        //     'location_id' => $row[2],
        //     'class_limit' => $row[3],
        //     'time' =>$time,

        //]);

    }
    private function transposeRowsToColumns(Collection $rows): array
    {
        $columns = [];

        foreach ($rows as $row) {
            foreach ($row as $key => $value) {
                $columns[$key][] = $value;
            }
        }

        return $columns;
    }
}
