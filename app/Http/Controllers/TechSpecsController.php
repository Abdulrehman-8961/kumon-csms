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
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Validator;

class TechSpecsController extends Controller
{
    //
    public function __construct() {}

    public function users()
    {
        return view('techspecUsers');
    }
    public function addusers()
    {
        return view('techSpecUserForm');
    }
    public function editTechSpecs()
    {
        return view('editTechSpecForm');
    }
    public function editusers()
    {
        return view('editTechSpecUserForm');
    }
    public function getDeviceContent(Request $request)
    {
        $data = DB::table('coorprate_device')->where('tech_spec_id', $request->id)->where('page', 'users')->get();
        return response()->json($data);
    }
    public function view($type)
    {
        return view('techSpecView', compact('type'));
    }
    public function add($type)
    {
        return view('techSpecForm', compact('type'));
    }
    public function addUser(Request $request)
    {
        $image = '';
        if ($request->logo != '') {
            $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('client_logos'), $image);
        }
        $data = [
            'client_id' => $request->client_id,
            'site_id' => $request->site_id,
            'user_type' => $request->user_type,
            'user_image' => $image,
            'start_date' => date('Y-m-d', strtotime($request->user_info_start_date)),
            'salutation' => $request->salutation,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'contact_email' => $request->user_info_email,
            'contact_telephone' => $request->user_info_phone,
            'network_user_id' => $request->network_user_id,
            'network_ad_domain' => $request->network_id_domain,
            'network_azure_domain' => $request->azure_domain,
            'network_corporate_email' => $request->corporate_email,
            'network_corporate_telephone' => $request->corporate_phone,
            'network_extension' => $request->extension,
            'employee_no' => $request->employee_no_id,
            'employee_department' => $request->employee_info_department,
            'employee_manager' => $request->employee_info_manager,
            'created_by' => Auth::user()->id,
        ];

        // dd($data);

        // dd($request->all());

        $techSpec_id = DB::table('tech_spec_users')->insertGetId($data);

        $deviceArray = $request->deviceArray;
        if (isset($request->deviceArray)) {
            foreach ($deviceArray as $a) {
                $a = json_decode($a);
                DB::table('coorprate_device')->insert([
                    'tech_spec_id' => $techSpec_id,
                    'asset_id' => $a->asset_id,
                    'serial' => $a->serial,
                    'cpu' => $a->cpu,
                    'memory' => $a->memory,
                    'manufacturer' => $a->manufacturer,
                    'model' => @$a->model,
                    'type' => @$a->type,
                    'name' => $a->name,
                    'device_type' => $a->device_type,
                    'platform' => @$a->platform,
                    'asset_type' => @$a->asset_type,
                    'page' => 'users',
                ]);
            }
        }

        // dd($techSpec_id);


        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('techspec_attachment/' . $a->attachment));
                DB::table('techspec_user_attachments')->insert([
                    'techspec_user_id' => $techSpec_id,
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


                DB::table('techspec_user_comments')->insert([
                    'techspec_user_id' => $techSpec_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }
        return response()->json('success');
    }
    public function updateUser(Request $request)
    {
        $image = '';
        if ($request->logo != '') {
            $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('client_logos'), $image);
        }
        $data = [
            'client_id' => $request->client_id,
            'site_id' => $request->site_id,
            'user_type' => $request->user_type,
            'start_date' => date('Y-m-d', strtotime($request->user_info_start_date)),
            'salutation' => $request->salutation,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'contact_email' => $request->user_info_email,
            'contact_telephone' => $request->user_info_phone,
            'network_user_id' => $request->network_user_id,
            'network_ad_domain' => $request->network_id_domain,
            'network_azure_domain' => $request->azure_domain,
            'network_corporate_email' => $request->corporate_email,
            'network_corporate_telephone' => $request->corporate_phone,
            'network_extension' => $request->extension,
            'employee_no' => $request->employee_no_id,
            'employee_department' => $request->employee_info_department,
            'employee_manager' => $request->employee_info_manager,
            'created_by' => Auth::user()->id,
        ];
        // dd($data);

        if ($image != '') {
            $data['user_image'] = $image;
        }

        $techSpec_id = DB::table('tech_spec_users')->where('id', $request->update_id)->update($data);

        $deviceArray = $request->deviceArray;
        if (isset($request->deviceArray)) {
            DB::table('coorprate_device')->where('id', $request->update_id)->where('page', 'users')->update([
                'is_deleted' => 1
            ]);
            foreach ($deviceArray as $a) {
                $a = json_decode($a);
                DB::table('coorprate_device')->insert([
                    'tech_spec_id' => $request->update_id,
                    'asset_id' => $a->asset_id,
                    'serial' => $a->serial,
                    'cpu' => $a->cpu,
                    'memory' => $a->memory,
                    'manufacturer' => $a->manufacturer,
                    'model' => @$a->model,
                    'type' => @$a->type,
                    'name' => $a->name,
                    'device_type' => $a->device_type,
                    'platform' => @$a->platform,
                    'asset_type' => @$a->asset_type,
                    'page' => 'users',
                ]);
            }
        }


        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('techspec_attachment/' . $a->attachment));
                DB::table('techspec_user_attachments')->insert([
                    'techspec_user_id' => $techSpec_id,
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


                DB::table('techspec_user_comments')->insert([
                    'techspec_user_id' => $techSpec_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }
        return response()->json('success');
    }
    public function insertTechSpec(Request $request)
    {
        if ($request->saved_id) {
            $image = '';
            if ($request->logo != '') {
                $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
                $request->file('logo')->move(public_path('client_logos'), $image);
            }
            $data = [
                'tech_spec_type' => $request->tech_spec_type,
                'client_id' => $request->client_id,
                'site_id' => $request->site,
                'sub_type' => $request->sub_type,
                'device_type' => $request->device_type ?? null,
                'asset_type' => $request->sub_type == 'Device' ? $request->asset_type : $request->asset_type_2,
                'platform' => $request->platform ?? null,
                'assigned_to' => $request->asigned_to,
                'approver' => $request->approver,
                'user_type' => $request->sub_type == 'User' ? $request->user_type : '',
                'user_image' => $request->sub_type == 'User' ? $image : '',
                'start_date' => $request->sub_type == 'User' ? date('Y-m-d', strtotime($request->user_info_start_date)) : '',
                'salutation' => $request->sub_type == 'User' ? $request->salutation : '',
                'firstname' => $request->sub_type == 'User' ? $request->firstname : '',
                'lastname' => $request->sub_type == 'User' ? $request->lastname : '',
                'contact_email' => $request->sub_type == 'User' ? $request->user_info_email : '',
                'contact_telephone' => $request->sub_type == 'User' ? $request->user_info_phone : '',
                'network_user_id' => $request->sub_type == 'User' ? $request->network_user_id : '',
                'network_ad_domain' => $request->sub_type == 'User' ? $request->network_id_domain : '',
                'network_azure_domain' => $request->sub_type == 'User' ? $request->azure_domain : '',
                'network_corporate_email' => $request->sub_type == 'User' ? $request->corporate_email : '',
                'network_corporate_telephone' => $request->sub_type == 'User' ? $request->corporate_phone : '',
                'network_extension' => $request->sub_type == 'User' ? $request->extension : '',
                'employee_no' => $request->sub_type == 'User' ? $request->employee_no_id : '',
                'employee_department' => $request->sub_type == 'User' ? $request->employee_info_department : '',
                'employee_manager' => $request->sub_type == 'User' ? $request->employee_info_manager : '',
                'project_no' => $request->sub_type == 'Device' ? $request->project_value : '',
                'project_change_no' => $request->sub_type == 'Device' ? $request->project_change_no : '',
                'project_due_date' => $request->sub_type == 'Device' ? date('Y-m-d', strtotime($request->project_due_date)) : '',
                'project_client_sponsor' => $request->sub_type == 'Device' ? $request->client_main_sponsor : '',
                'host_operating_system' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_os : '',
                'host_location' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_location : '',
                'host_role_description' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_description : '',
                'host_name' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_name : '',
                'host_domain' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_domain : '',
                'host_system_type' => $request->sub_type == 'Device' ? $request->host_info_system_type : '',
                'host_system_category' => $request->sub_type == 'Device' ? $request->host_info_system_category : '',
                'host_ad_domain' => $request->sub_type == 'Device' && $request->platform == 'Windows' ? $request->host_info_addomain : '',
                'host_ad_ou' => $request->sub_type == 'Device' && $request->platform == 'Windows' ? $request->host_info_ou : '',
                'v_center_server' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->center_server : '',
                'cluster_host' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->cluster_host : '',
                'vm_folder' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_folder : '',
                'vm_datastore' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_datastore : '',
                'vm_restart_priority' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_retart_priority : '',
                'disaster_recovery' => $request->sub_type == 'Device' ? ($request->disaster_recovery1 ?? 0) : '',
                'ssl_certificate' => $request->sub_type == 'Device' ? ($request->ntp ?? 0) : '',
                'supported' => $request->sub_type == 'Device' ? ($request->HasWarranty ?? 0) : '',
                'clustered' => $request->sub_type == 'Device' ? ($request->clustered ?? 0) : '',
                'internet_facing' => $request->sub_type == 'Device' ? ($request->internet_facing ?? 0) : '',
                'load_balanced' => $request->sub_type == 'Device' ? ($request->load_balancing ?? 0) : '',
                'hardware_information_manufacturer' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_manufacturer : $request->manufacturer) : ($request->sub_type == 'Workstation' ? $request->physical_server_manufacturer : ''),
                'hardware_information_model' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->model : $request->model_2) : ($request->sub_type == 'Workstation' ? $request->model : ''),
                'hardware_information_type' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->type : $request->type_2) : ($request->sub_type == 'Workstation' ? $request->type : ''),
                'hardware_information_sn' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_sn : $request->hardware_info_sn) : ($request->sub_type == 'Workstation' ? $request->physical_server_sn : ''),
                'hardware_information_cpu_model' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_model : '',
                'hardware_information_sockets' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_sockets : '',
                'hardware_information_cores' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_cores : '',
                'hardware_information_frequency' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_freq : '',
                'hardware_information_memory' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_memory : $request->hardware_info_memory) : ($request->sub_type == 'Workstation' ? $request->physical_server_memory : ''),
                'vm_cpu' => $request->device_type == 'Virtual' ? $request->vcpu : '',
                'vm_memory' => $request->device_type == 'Virtual' ? $request->vm_memory : '',
                'managed' => $request->sub_type == 'Device' ? ($request->managed ?? 0) : '',
                'app_owner' => $request->sub_type == 'Device' ? ($request->app_owner ?? '') : '',
                'sla' => $request->sub_type == 'Device' ? ($request->sla ?? '') : '',
                'patched' => $request->sub_type == 'Device' ? ($request->patched ?? '') : '',
                'monitored' => $request->sub_type == 'Device' ? ($request->monitored ?? '') : '',
                'backup' => $request->sub_type == 'Device' ? ($request->backup ?? '') : '',
                'anti_virus' => $request->sub_type == 'Device' ? ($request->antivirus ?? '') : '',
                'replicated' => $request->sub_type == 'Device' ? ($request->replicated ?? '') : '',
                'vulnerability_scan' => $request->sub_type == 'Device' ? ($request->disaster_recovery ?? '') : '',
                'siem' => $request->sub_type == 'Device' ? ($request->syslog ?? '') : '',
                'smtp' => $request->sub_type == 'Device' ? ($request->smtp ?? '') : '',
                'not_supported_reason' => $request->sub_type == 'Device' ? ($request->NotSupportedReason ?? '') : '',
                'stage' => 'Validate C\L',
                'created_by' => Auth::user()->id,
            ];

            $techSpec_id = DB::table('tech_spec')->where('id', $request->saved_id)->update($data);

            $portMap = $request->portMap;
            // dd($portMap);
            if (isset($request->portMap)) {
                DB::table('tech_spec_port_map')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($portMap as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_port_map')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'mapping_type' => @$a->mappingType,
                        'network_adapter' => @$a->networkAdapter,
                        'media_type' => @$a->mediaType,
                        'switch' => @$a->switch,
                        'port' => @$a->port,
                        'port_mode' => @$a->portMode,
                        'selectedIds' => is_array($a->selectedIds) ? implode(', ', @$a->selectedIds) : @$a->selectedIds,
                        'vlan_ids' => implode(', ', @$a->vlanIds),
                        'sub_text' => @$a->sub_text,
                        'comments' => @$a->comments,
                        'ssid' => @$a->ssid,
                    ]);
                }
            }
            $deviceArray = $request->deviceArray;
            if (isset($request->deviceArray)) {
                DB::table('coorprate_device')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($deviceArray as $a) {
                    $a = json_decode($a);
                    DB::table('coorprate_device')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'asset_id' => $a->asset_id,
                        'serial' => $a->serial,
                        'cpu' => $a->cpu,
                        'memory' => $a->memory,
                        'manufacturer' => $a->manufacturer,
                        'model' => @$a->model,
                        'type' => @$a->type,
                        'name' => $a->name,
                        'device_type' => $a->device_type,
                    ]);
                }
            }
            $maintenance = $request->maintenance;
            if (isset($request->maintenance)) {
                DB::table('additional_maintenance')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($maintenance as $a) {
                    $a = json_decode($a);
                    DB::table('additional_maintenance')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'frequency' => $a->frequency,
                        'day' => $a->day,
                        'occurance' => $a->occurance,
                        'month' => $a->month,
                        'start_time' => $a->start_time,
                        'time_zone' => $a->time_zone,
                        'duration_hours' => $a->duration_hours,
                        'created_at' => Auth::user()->id,
                    ]);
                }
            }
            $ipDns_array = $request->ipDns;
            if (isset($request->ipDns)) {
                DB::table('tech_spec_ip_dns')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($ipDns_array as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_ip_dns')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'dns_type' => $a->type,
                        'alias' => $a->alias,
                        'vlan_id_no' => $a->vlan_id,
                        'vlan_id' => $a->vlan_text,
                        'host_name' => $a->host_name,
                        'ip_address' => $a->address,
                        'gateway' => $a->dns_gateway,
                        'description' => $a->description,
                        'primary_dns' => $a->primary_dns,
                        'secondary_dns' => $a->secondary_dns,
                        'primary_ntp' => $a->primary_ntp,
                        'secondary_ntp' => $a->secondary_ntp,
                        'subnet_ip' => $a->subnet_ip,
                        'mask' => $a->mask,
                        'gateway_ip' => $a->gateway,
                        'zone' => $a->zone,
                        'background' => $a->background,
                        'color' => $a->color,
                    ]);
                }
            }
            $networkAdapter_array = $request->networkAdapter;
            if (isset($request->networkAdapter)) {
                DB::table('tech_spec_network_adapter')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($networkAdapter_array as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_network_adapter')->insert([
                        'tech_spec_id' => $request->saved_id,
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
            $virtualDiskArray = $request->virtualDisk;
            if (isset($request->virtualDisk)) {
                DB::table('tech_spec_virtual_disks')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($virtualDiskArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_virtual_disks')->insert([
                        'tech_spec_id' => $request->saved_id,
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
            $raidVolumeArray = $request->raidVolume;
            if (isset($request->raidVolume)) {
                DB::table('tech_spec_raid_volume')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($raidVolumeArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_raid_volume')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'name' => @$a->volume_name,
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
            $logicalVolumeArray = $request->logicalVolume;
            if (isset($request->logicalVolume)) {
                DB::table('tech_spec_logical_volume')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($logicalVolumeArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_logical_volume')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'source_disk' => @$a->source_disk,
                        'volume' => @$a->volume,
                        'volume_name' => @$a->volume_name,
                        'size' => @$a->size,
                        'size_unit' => @$a->size_unit,
                        'format' => @$a->format,
                        'block_size' => @$a->block_size
                    ]);
                }
            }

            $attachment_array = $request->attachmentArray;
            if (isset($request->attachmentArray)) {
                DB::table('tech_spec_attachments')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($attachment_array as $a) {
                    $a = json_decode($a);

                    copy(public_path('temp_uploads/' . $a->attachment), public_path('techspec_attachment/' . $a->attachment));
                    DB::table('tech_spec_attachments')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                        'attachment' => $a->attachment,
                        'name' => $a->name,
                        'added_by' => Auth::id(),
                    ]);
                }
            }

            $commentArray = $request->commentArray;
            if (isset($request->commentArray)) {
                DB::table('tech_spec_comments')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($commentArray as $a) {
                    $a = json_decode($a);


                    DB::table('tech_spec_comments')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                        'comment' => $a->comment,
                        'name' => $a->name,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
            $emailArray = $request->emailArray;
            if (isset($request->emailArray)) {
                DB::table('tech_spec_email')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($emailArray as $a) {
                    $a = json_decode($a);


                    DB::table('tech_spec_email')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'name' => $a->name,
                        'email' => $a->email,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
            return response()->json(['status' => 'success']);
        } else if ($request->clone_id) {
            $image = '';
            if ($request->logo != '') {
                $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
                $request->file('logo')->move(public_path('client_logos'), $image);
            }
            $data = [
                'tech_spec_type' => $request->tech_spec_type,
                'client_id' => $request->client_id,
                'site_id' => $request->site,
                'sub_type' => $request->sub_type,
                'device_type' => $request->device_type ?? null,
                'asset_type' => $request->sub_type == 'Device' ? $request->asset_type : $request->asset_type_2,
                'platform' => $request->platform ?? null,
                'assigned_to' => $request->asigned_to,
                'approver' => $request->approver,
                'user_type' => $request->sub_type == 'User' ? $request->user_type : '',
                'user_image' => $request->sub_type == 'User' ? $image : '',
                'start_date' => $request->sub_type == 'User' ? date('Y-m-d', strtotime($request->user_info_start_date)) : '',
                'salutation' => $request->sub_type == 'User' ? $request->salutation : '',
                'firstname' => $request->sub_type == 'User' ? $request->firstname : '',
                'lastname' => $request->sub_type == 'User' ? $request->lastname : '',
                'contact_email' => $request->sub_type == 'User' ? $request->user_info_email : '',
                'contact_telephone' => $request->sub_type == 'User' ? $request->user_info_phone : '',
                'network_user_id' => $request->sub_type == 'User' ? $request->network_user_id : '',
                'network_ad_domain' => $request->sub_type == 'User' ? $request->network_id_domain : '',
                'network_azure_domain' => $request->sub_type == 'User' ? $request->azure_domain : '',
                'network_corporate_email' => $request->sub_type == 'User' ? $request->corporate_email : '',
                'network_corporate_telephone' => $request->sub_type == 'User' ? $request->corporate_phone : '',
                'network_extension' => $request->sub_type == 'User' ? $request->extension : '',
                'employee_no' => $request->sub_type == 'User' ? $request->employee_no_id : '',
                'employee_department' => $request->sub_type == 'User' ? $request->employee_info_department : '',
                'employee_manager' => $request->sub_type == 'User' ? $request->employee_info_manager : '',
                'project_no' => $request->sub_type == 'Device' ? $request->project_value : '',
                'project_change_no' => $request->sub_type == 'Device' ? $request->project_change_no : '',
                'project_due_date' => $request->sub_type == 'Device' ? date('Y-m-d', strtotime($request->project_due_date)) : '',
                'project_client_sponsor' => $request->sub_type == 'Device' ? $request->client_main_sponsor : '',
                'host_operating_system' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_os : '',
                'host_location' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_location : '',
                'host_role_description' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_description : '',
                'host_name' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_name : '',
                'host_domain' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_domain : '',
                'host_system_type' => $request->sub_type == 'Device' ? $request->host_info_system_type : '',
                'host_system_category' => $request->sub_type == 'Device' ? $request->host_info_system_category : '',
                'host_ad_domain' => $request->sub_type == 'Device' && $request->platform == 'Windows' ? $request->host_info_addomain : '',
                'host_ad_ou' => $request->sub_type == 'Device' && $request->platform == 'Windows' ? $request->host_info_ou : '',
                'v_center_server' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->center_server : '',
                'cluster_host' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->cluster_host : '',
                'vm_folder' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_folder : '',
                'vm_datastore' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_datastore : '',
                'vm_restart_priority' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_retart_priority : '',
                'disaster_recovery' => $request->sub_type == 'Device' ? ($request->disaster_recovery1 ?? 0) : '',
                'ssl_certificate' => $request->sub_type == 'Device' ? ($request->ntp ?? 0) : '',
                'supported' => $request->sub_type == 'Device' ? ($request->HasWarranty ?? 0) : '',
                'clustered' => $request->sub_type == 'Device' ? ($request->clustered ?? 0) : '',
                'internet_facing' => $request->sub_type == 'Device' ? ($request->internet_facing ?? 0) : '',
                'load_balanced' => $request->sub_type == 'Device' ? ($request->load_balancing ?? 0) : '',
                'hardware_information_manufacturer' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_manufacturer : $request->manufacturer) : ($request->sub_type == 'Workstation' ? $request->physical_server_manufacturer : ''),
                'hardware_information_model' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->model : $request->model_2) : ($request->sub_type == 'Workstation' ? $request->model : ''),
                'hardware_information_type' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->type : $request->type_2) : ($request->sub_type == 'Workstation' ? $request->type : ''),
                'hardware_information_sn' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_sn : $request->hardware_info_sn) : ($request->sub_type == 'Workstation' ? $request->physical_server_sn : ''),
                'hardware_information_cpu_model' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_model : '',
                'hardware_information_sockets' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_sockets : '',
                'hardware_information_cores' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_cores : '',
                'hardware_information_frequency' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_freq : '',
                'hardware_information_memory' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_memory : $request->hardware_info_memory) : ($request->sub_type == 'Workstation' ? $request->physical_server_memory : ''),
                'vm_cpu' => $request->device_type == 'Virtual' ? $request->vcpu : '',
                'vm_memory' => $request->device_type == 'Virtual' ? $request->vm_memory : '',
                'managed' => $request->sub_type == 'Device' ? ($request->managed ?? 0) : '',
                'app_owner' => $request->sub_type == 'Device' ? ($request->app_owner ?? '') : '',
                'sla' => $request->sub_type == 'Device' ? ($request->sla ?? '') : '',
                'patched' => $request->sub_type == 'Device' ? ($request->patched ?? '') : '',
                'monitored' => $request->sub_type == 'Device' ? ($request->monitored ?? '') : '',
                'backup' => $request->sub_type == 'Device' ? ($request->backup ?? '') : '',
                'anti_virus' => $request->sub_type == 'Device' ? ($request->antivirus ?? '') : '',
                'replicated' => $request->sub_type == 'Device' ? ($request->replicated ?? '') : '',
                'vulnerability_scan' => $request->sub_type == 'Device' ? ($request->disaster_recovery ?? '') : '',
                'siem' => $request->sub_type == 'Device' ? ($request->syslog ?? '') : '',
                'smtp' => $request->sub_type == 'Device' ? ($request->smtp ?? '') : '',
                'not_supported_reason' => $request->sub_type == 'Device' ? ($request->NotSupportedReason ?? '') : '',
                'created_by' => Auth::user()->id,
            ];

            $techSpec_id = DB::table('tech_spec')->insertGetId($data);

            $portMap = $request->portMap;
            // dd($portMap);
            if (isset($request->portMap)) {
                foreach ($portMap as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_port_map')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'mapping_type' => @$a->mappingType,
                        'network_adapter' => @$a->networkAdapter,
                        'media_type' => @$a->mediaType,
                        'switch' => @$a->switch,
                        'port' => @$a->port,
                        'port_mode' => @$a->portMode,
                        'selectedIds' => is_array($a->selectedIds) ? implode(', ', @$a->selectedIds) : @$a->selectedIds,
                        'vlan_ids' => implode(', ', @$a->vlanIds),
                        'sub_text' => @$a->sub_text,
                        'comments' => @$a->comments,
                        'ssid' => @$a->ssid,
                    ]);
                }
            }
            $deviceArray = $request->deviceArray;
            if (isset($request->deviceArray)) {
                foreach ($deviceArray as $a) {
                    $a = json_decode($a);
                    DB::table('coorprate_device')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'asset_id' => $a->asset_id,
                        'serial' => $a->serial,
                        'cpu' => $a->cpu,
                        'memory' => $a->memory,
                        'manufacturer' => $a->manufacturer,
                        'model' => @$a->model,
                        'type' => @$a->type,
                        'name' => $a->name,
                        'device_type' => $a->device_type,
                    ]);
                }
            }
            $maintenance = $request->maintenance;
            if (isset($request->maintenance)) {
                foreach ($maintenance as $a) {
                    $a = json_decode($a);
                    DB::table('additional_maintenance')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'frequency' => $a->frequency,
                        'day' => $a->day,
                        'occurance' => $a->occurance,
                        'month' => $a->month,
                        'start_time' => $a->start_time,
                        'time_zone' => $a->time_zone,
                        'duration_hours' => $a->duration_hours,
                        'created_at' => Auth::user()->id,
                    ]);
                }
            }
            $ipDns_array = $request->ipDns;
            if (isset($request->ipDns)) {
                foreach ($ipDns_array as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_ip_dns')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'dns_type' => $a->type,
                        'alias' => $a->alias,
                        'vlan_id_no' => $a->vlan_id,
                        'vlan_id' => $a->vlan_text,
                        'host_name' => $a->host_name,
                        'ip_address' => $a->address,
                        'gateway' => $a->dns_gateway,
                        'description' => $a->description,
                        'primary_dns' => $a->primary_dns,
                        'secondary_dns' => $a->secondary_dns,
                        'primary_ntp' => $a->primary_ntp,
                        'secondary_ntp' => $a->secondary_ntp,
                        'subnet_ip' => $a->subnet_ip,
                        'mask' => $a->mask,
                        'gateway_ip' => $a->gateway,
                        'zone' => $a->zone,
                        'background' => $a->background,
                        'color' => $a->color,
                    ]);
                }
            }
            $networkAdapter_array = $request->networkAdapter;
            if (isset($request->networkAdapter)) {
                foreach ($networkAdapter_array as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_network_adapter')->insert([
                        'tech_spec_id' => $techSpec_id,
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
            $virtualDiskArray = $request->virtualDisk;
            if (isset($request->virtualDisk)) {
                foreach ($virtualDiskArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_virtual_disks')->insert([
                        'tech_spec_id' => $techSpec_id,
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
            $raidVolumeArray = $request->raidVolume;
            if (isset($request->raidVolume)) {
                foreach ($raidVolumeArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_raid_volume')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'name' => @$a->volume_name,
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
            $logicalVolumeArray = $request->logicalVolume;
            if (isset($request->logicalVolume)) {
                foreach ($logicalVolumeArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_logical_volume')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'source_disk' => @$a->source_disk,
                        'volume' => @$a->volume,
                        'volume_name' => @$a->volume_name,
                        'size' => @$a->size,
                        'size_unit' => @$a->size_unit,
                        'format' => @$a->format,
                        'block_size' => @$a->block_size
                    ]);
                }
            }

            $attachment_array = $request->attachmentArray;
            if (isset($request->attachmentArray)) {
                foreach ($attachment_array as $a) {
                    $a = json_decode($a);

                    copy(public_path('temp_uploads/' . $a->attachment), public_path('techspec_attachment/' . $a->attachment));
                    DB::table('tech_spec_attachments')->insert([
                        'tech_spec_id' => $techSpec_id,
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


                    DB::table('tech_spec_comments')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                        'comment' => $a->comment,
                        'name' => $a->name,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
            $emailArray = $request->emailArray;
            if (isset($request->emailArray)) {
                foreach ($emailArray as $a) {
                    $a = json_decode($a);


                    DB::table('tech_spec_email')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'name' => $a->name,
                        'email' => $a->email,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
            // dd($techSpec_id);
            return response()->json(['status' => 'success', 'id' => $techSpec_id]);
        }
        return response()->json(['status' => 'error']);
    }
    public function insertTechSpec_(Request $request)
    {
        if ($request->saved_id) {
            $image = '';
            if ($request->logo != '') {
                $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
                $request->file('logo')->move(public_path('client_logos'), $image);
            }
            $data = [
                'tech_spec_type' => $request->tech_spec_type,
                'client_id' => $request->client_id,
                'site_id' => $request->site,
                'sub_type' => $request->sub_type,
                'device_type' => $request->device_type ?? null,
                'asset_type' => $request->sub_type == 'Device' ? $request->asset_type : $request->asset_type_2,
                'platform' => $request->platform ?? null,
                'assigned_to' => $request->asigned_to,
                'approver' => $request->approver,
                'user_type' => $request->sub_type == 'User' ? $request->user_type : '',
                'user_image' => $request->sub_type == 'User' ? $image : '',
                'start_date' => $request->sub_type == 'User' ? date('Y-m-d', strtotime($request->user_info_start_date)) : '',
                'salutation' => $request->sub_type == 'User' ? $request->salutation : '',
                'firstname' => $request->sub_type == 'User' ? $request->firstname : '',
                'lastname' => $request->sub_type == 'User' ? $request->lastname : '',
                'contact_email' => $request->sub_type == 'User' ? $request->user_info_email : '',
                'contact_telephone' => $request->sub_type == 'User' ? $request->user_info_phone : '',
                'network_user_id' => $request->sub_type == 'User' ? $request->network_user_id : '',
                'network_ad_domain' => $request->sub_type == 'User' ? $request->network_id_domain : '',
                'network_azure_domain' => $request->sub_type == 'User' ? $request->azure_domain : '',
                'network_corporate_email' => $request->sub_type == 'User' ? $request->corporate_email : '',
                'network_corporate_telephone' => $request->sub_type == 'User' ? $request->corporate_phone : '',
                'network_extension' => $request->sub_type == 'User' ? $request->extension : '',
                'employee_no' => $request->sub_type == 'User' ? $request->employee_no_id : '',
                'employee_department' => $request->sub_type == 'User' ? $request->employee_info_department : '',
                'employee_manager' => $request->sub_type == 'User' ? $request->employee_info_manager : '',
                'project_no' => $request->sub_type == 'Device' ? $request->project_value : '',
                'project_change_no' => $request->sub_type == 'Device' ? $request->project_change_no : '',
                'project_due_date' => $request->sub_type == 'Device' ? date('Y-m-d', strtotime($request->project_due_date)) : '',
                'project_client_sponsor' => $request->sub_type == 'Device' ? $request->client_main_sponsor : '',
                'host_operating_system' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_os : '',
                'host_location' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_location : '',
                'host_role_description' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_description : '',
                'host_name' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_name : '',
                'host_domain' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_domain : '',
                'host_system_type' => $request->sub_type == 'Device' ? $request->host_info_system_type : '',
                'host_system_category' => $request->sub_type == 'Device' ? $request->host_info_system_category : '',
                'host_ad_domain' => $request->sub_type == 'Device' && $request->platform == 'Windows' ? $request->host_info_addomain : '',
                'host_ad_ou' => $request->sub_type == 'Device' && $request->platform == 'Windows' ? $request->host_info_ou : '',
                'v_center_server' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->center_server : '',
                'cluster_host' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->cluster_host : '',
                'vm_folder' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_folder : '',
                'vm_datastore' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_datastore : '',
                'vm_restart_priority' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_retart_priority : '',
                'disaster_recovery' => $request->sub_type == 'Device' ? ($request->disaster_recovery1 ?? 0) : '',
                'ssl_certificate' => $request->sub_type == 'Device' ? ($request->ntp ?? 0) : '',
                'supported' => $request->sub_type == 'Device' ? ($request->HasWarranty ?? 0) : '',
                'clustered' => $request->sub_type == 'Device' ? ($request->clustered ?? 0) : '',
                'internet_facing' => $request->sub_type == 'Device' ? ($request->internet_facing ?? 0) : '',
                'load_balanced' => $request->sub_type == 'Device' ? ($request->load_balancing ?? 0) : '',
                'hardware_information_manufacturer' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_manufacturer : $request->manufacturer) : ($request->sub_type == 'Workstation' ? $request->physical_server_manufacturer : ''),
                'hardware_information_model' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->model : $request->model_2) : ($request->sub_type == 'Workstation' ? $request->model : ''),
                'hardware_information_type' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->type : $request->type_2) : ($request->sub_type == 'Workstation' ? $request->type : ''),
                'hardware_information_sn' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_sn : $request->hardware_info_sn) : ($request->sub_type == 'Workstation' ? $request->physical_server_sn : ''),
                'hardware_information_cpu_model' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_model : '',
                'hardware_information_sockets' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_sockets : '',
                'hardware_information_cores' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_cores : '',
                'hardware_information_frequency' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_freq : '',
                'hardware_information_memory' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_memory : $request->hardware_info_memory) : ($request->sub_type == 'Workstation' ? $request->physical_server_memory : ''),
                'vm_cpu' => $request->device_type == 'Virtual' ? $request->vcpu : '',
                'vm_memory' => $request->device_type == 'Virtual' ? $request->vm_memory : '',
                'managed' => $request->sub_type == 'Device' ? ($request->managed ?? 0) : '',
                'app_owner' => $request->sub_type == 'Device' ? ($request->app_owner ?? '') : '',
                'sla' => $request->sub_type == 'Device' ? ($request->sla ?? '') : '',
                'patched' => $request->sub_type == 'Device' ? ($request->patched ?? '') : '',
                'monitored' => $request->sub_type == 'Device' ? ($request->monitored ?? '') : '',
                'backup' => $request->sub_type == 'Device' ? ($request->backup ?? '') : '',
                'anti_virus' => $request->sub_type == 'Device' ? ($request->antivirus ?? '') : '',
                'replicated' => $request->sub_type == 'Device' ? ($request->replicated ?? '') : '',
                'vulnerability_scan' => $request->sub_type == 'Device' ? ($request->disaster_recovery ?? '') : '',
                'siem' => $request->sub_type == 'Device' ? ($request->syslog ?? '') : '',
                'smtp' => $request->sub_type == 'Device' ? ($request->smtp ?? '') : '',
                'not_supported_reason' => $request->sub_type == 'Device' ? ($request->NotSupportedReason ?? '') : '',
                'stage' => 'Validate C\L',
                'created_by' => Auth::user()->id,
            ];

            $techSpec_id = DB::table('tech_spec')->where('id', $request->saved_id)->update($data);

            $portMap = $request->portMap;
            // dd($portMap);
            if (isset($request->portMap)) {
                DB::table('tech_spec_port_map')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($portMap as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_port_map')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'mapping_type' => @$a->mappingType,
                        'network_adapter' => @$a->networkAdapter,
                        'media_type' => @$a->mediaType,
                        'switch' => @$a->switch,
                        'port' => @$a->port,
                        'port_mode' => @$a->portMode,
                        'selectedIds' => is_array($a->selectedIds) ? implode(', ', @$a->selectedIds) : @$a->selectedIds,
                        'vlan_ids' => implode(', ', @$a->vlanIds),
                        'sub_text' => @$a->sub_text,
                        'comments' => @$a->comments,
                        'ssid' => @$a->ssid,
                    ]);
                }
            }
            $deviceArray = $request->deviceArray;
            if (isset($request->deviceArray)) {
                DB::table('coorprate_device')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($deviceArray as $a) {
                    $a = json_decode($a);
                    DB::table('coorprate_device')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'asset_id' => $a->asset_id,
                        'serial' => $a->serial,
                        'cpu' => $a->cpu,
                        'memory' => $a->memory,
                        'manufacturer' => $a->manufacturer,
                        'model' => @$a->model,
                        'type' => @$a->type,
                        'name' => $a->name,
                        'device_type' => $a->device_type,
                    ]);
                }
            }
            $maintenance = $request->maintenance;
            if (isset($request->maintenance)) {
                DB::table('additional_maintenance')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($maintenance as $a) {
                    $a = json_decode($a);
                    DB::table('additional_maintenance')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'frequency' => $a->frequency,
                        'day' => $a->day,
                        'occurance' => $a->occurance,
                        'month' => $a->month,
                        'start_time' => $a->start_time,
                        'time_zone' => $a->time_zone,
                        'duration_hours' => $a->duration_hours,
                        'created_at' => Auth::user()->id,
                    ]);
                }
            }
            $ipDns_array = $request->ipDns;
            if (isset($request->ipDns)) {
                DB::table('tech_spec_ip_dns')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($ipDns_array as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_ip_dns')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'dns_type' => $a->type,
                        'alias' => $a->alias,
                        'vlan_id_no' => $a->vlan_id,
                        'vlan_id' => $a->vlan_text,
                        'host_name' => $a->host_name,
                        'ip_address' => $a->address,
                        'gateway' => $a->dns_gateway,
                        'description' => $a->description,
                        'primary_dns' => $a->primary_dns,
                        'secondary_dns' => $a->secondary_dns,
                        'primary_ntp' => $a->primary_ntp,
                        'secondary_ntp' => $a->secondary_ntp,
                        'subnet_ip' => $a->subnet_ip,
                        'mask' => $a->mask,
                        'gateway_ip' => $a->gateway,
                        'zone' => $a->zone,
                        'background' => $a->background,
                        'color' => $a->color,
                    ]);
                }
            }
            $networkAdapter_array = $request->networkAdapter;
            if (isset($request->networkAdapter)) {
                DB::table('tech_spec_network_adapter')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($networkAdapter_array as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_network_adapter')->insert([
                        'tech_spec_id' => $request->saved_id,
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
            $virtualDiskArray = $request->virtualDisk;
            if (isset($request->virtualDisk)) {
                DB::table('tech_spec_virtual_disks')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($virtualDiskArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_virtual_disks')->insert([
                        'tech_spec_id' => $request->saved_id,
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
            $raidVolumeArray = $request->raidVolume;
            if (isset($request->raidVolume)) {
                DB::table('tech_spec_raid_volume')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($raidVolumeArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_raid_volume')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'name' => @$a->volume_name,
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
            $logicalVolumeArray = $request->logicalVolume;
            if (isset($request->logicalVolume)) {
                DB::table('tech_spec_logical_volume')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($logicalVolumeArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_logical_volume')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'source_disk' => @$a->source_disk,
                        'volume' => @$a->volume,
                        'volume_name' => @$a->volume_name,
                        'size' => @$a->size,
                        'size_unit' => @$a->size_unit,
                        'format' => @$a->format,
                        'block_size' => @$a->block_size
                    ]);
                }
            }

            $attachment_array = $request->attachmentArray;
            if (isset($request->attachmentArray)) {
                DB::table('tech_spec_attachments')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($attachment_array as $a) {
                    $a = json_decode($a);

                    copy(public_path('temp_uploads/' . $a->attachment), public_path('techspec_attachment/' . $a->attachment));
                    DB::table('tech_spec_attachments')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                        'attachment' => $a->attachment,
                        'name' => $a->name,
                        'added_by' => Auth::id(),
                    ]);
                }
            }

            $commentArray = $request->commentArray;
            if (isset($request->commentArray)) {
                DB::table('tech_spec_comments')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($commentArray as $a) {
                    $a = json_decode($a);


                    DB::table('tech_spec_comments')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                        'comment' => $a->comment,
                        'name' => $a->name,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
            $emailArray = $request->emailArray;
            if (isset($request->emailArray)) {
                DB::table('tech_spec_email')->where('tech_spec_id', $request->saved_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($emailArray as $a) {
                    $a = json_decode($a);


                    DB::table('tech_spec_email')->insert([
                        'tech_spec_id' => $request->saved_id,
                        'name' => $a->name,
                        'email' => $a->email,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
            return response()->json(['status' => 'success']);
        } else {
            $image = '';
            if ($request->logo != '') {
                $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
                $request->file('logo')->move(public_path('client_logos'), $image);
            }
            $data = [
                'tech_spec_type' => $request->tech_spec_type,
                'client_id' => $request->client_id,
                'site_id' => $request->site,
                'sub_type' => $request->sub_type,
                'device_type' => $request->device_type ?? null,
                'asset_type' => $request->sub_type == 'Device' ? $request->asset_type : $request->asset_type_2,
                'platform' => $request->platform ?? null,
                'assigned_to' => $request->asigned_to,
                'approver' => $request->approver,
                'user_type' => $request->sub_type == 'User' ? $request->user_type : '',
                'user_image' => $request->sub_type == 'User' ? $image : '',
                'start_date' => $request->sub_type == 'User' ? date('Y-m-d', strtotime($request->user_info_start_date)) : '',
                'salutation' => $request->sub_type == 'User' ? $request->salutation : '',
                'firstname' => $request->sub_type == 'User' ? $request->firstname : '',
                'lastname' => $request->sub_type == 'User' ? $request->lastname : '',
                'contact_email' => $request->sub_type == 'User' ? $request->user_info_email : '',
                'contact_telephone' => $request->sub_type == 'User' ? $request->user_info_phone : '',
                'network_user_id' => $request->sub_type == 'User' ? $request->network_user_id : '',
                'network_ad_domain' => $request->sub_type == 'User' ? $request->network_id_domain : '',
                'network_azure_domain' => $request->sub_type == 'User' ? $request->azure_domain : '',
                'network_corporate_email' => $request->sub_type == 'User' ? $request->corporate_email : '',
                'network_corporate_telephone' => $request->sub_type == 'User' ? $request->corporate_phone : '',
                'network_extension' => $request->sub_type == 'User' ? $request->extension : '',
                'employee_no' => $request->sub_type == 'User' ? $request->employee_no_id : '',
                'employee_department' => $request->sub_type == 'User' ? $request->employee_info_department : '',
                'employee_manager' => $request->sub_type == 'User' ? $request->employee_info_manager : '',
                'project_no' => $request->sub_type == 'Device' ? $request->project_value : '',
                'project_change_no' => $request->sub_type == 'Device' ? $request->project_change_no : '',
                'project_due_date' => $request->sub_type == 'Device' ? date('Y-m-d', strtotime($request->project_due_date)) : '',
                'project_client_sponsor' => $request->sub_type == 'Device' ? $request->client_main_sponsor : '',
                'host_operating_system' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_os : '',
                'host_location' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_location : '',
                'host_role_description' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_description : '',
                'host_name' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_name : '',
                'host_domain' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_domain : '',
                'host_system_type' => $request->sub_type == 'Device' ? $request->host_info_system_type : '',
                'host_system_category' => $request->sub_type == 'Device' ? $request->host_info_system_category : '',
                'host_ad_domain' => $request->sub_type == 'Device' && $request->platform == 'Windows' ? $request->host_info_addomain : '',
                'host_ad_ou' => $request->sub_type == 'Device' && $request->platform == 'Windows' ? $request->host_info_ou : '',
                'v_center_server' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->center_server : '',
                'cluster_host' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->cluster_host : '',
                'vm_folder' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_folder : '',
                'vm_datastore' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_datastore : '',
                'vm_restart_priority' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_retart_priority : '',
                'disaster_recovery' => $request->sub_type == 'Device' ? ($request->disaster_recovery1 ?? 0) : '',
                'ssl_certificate' => $request->sub_type == 'Device' ? ($request->ntp ?? 0) : '',
                'supported' => $request->sub_type == 'Device' ? ($request->HasWarranty ?? 0) : '',
                'clustered' => $request->sub_type == 'Device' ? ($request->clustered ?? 0) : '',
                'internet_facing' => $request->sub_type == 'Device' ? ($request->internet_facing ?? 0) : '',
                'load_balanced' => $request->sub_type == 'Device' ? ($request->load_balancing ?? 0) : '',
                'hardware_information_manufacturer' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_manufacturer : $request->manufacturer) : ($request->sub_type == 'Workstation' ? $request->physical_server_manufacturer : ''),
                'hardware_information_model' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->model : $request->model_2) : ($request->sub_type == 'Workstation' ? $request->model : ''),
                'hardware_information_type' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->type : $request->type_2) : ($request->sub_type == 'Workstation' ? $request->type : ''),
                'hardware_information_sn' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_sn : $request->hardware_info_sn) : ($request->sub_type == 'Workstation' ? $request->physical_server_sn : ''),
                'hardware_information_cpu_model' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_model : '',
                'hardware_information_sockets' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_sockets : '',
                'hardware_information_cores' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_cores : '',
                'hardware_information_frequency' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_freq : '',
                'hardware_information_memory' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_memory : $request->hardware_info_memory) : ($request->sub_type == 'Workstation' ? $request->physical_server_memory : ''),
                'vm_cpu' => $request->device_type == 'Virtual' ? $request->vcpu : '',
                'vm_memory' => $request->device_type == 'Virtual' ? $request->vm_memory : '',
                'managed' => $request->sub_type == 'Device' ? ($request->managed ?? 0) : '',
                'app_owner' => $request->sub_type == 'Device' ? ($request->app_owner ?? '') : '',
                'sla' => $request->sub_type == 'Device' ? ($request->sla ?? '') : '',
                'patched' => $request->sub_type == 'Device' ? ($request->patched ?? '') : '',
                'monitored' => $request->sub_type == 'Device' ? ($request->monitored ?? '') : '',
                'backup' => $request->sub_type == 'Device' ? ($request->backup ?? '') : '',
                'anti_virus' => $request->sub_type == 'Device' ? ($request->antivirus ?? '') : '',
                'replicated' => $request->sub_type == 'Device' ? ($request->replicated ?? '') : '',
                'vulnerability_scan' => $request->sub_type == 'Device' ? ($request->disaster_recovery ?? '') : '',
                'siem' => $request->sub_type == 'Device' ? ($request->syslog ?? '') : '',
                'smtp' => $request->sub_type == 'Device' ? ($request->smtp ?? '') : '',
                'not_supported_reason' => $request->sub_type == 'Device' ? ($request->NotSupportedReason ?? '') : '',
                'created_by' => Auth::user()->id,
            ];

            $techSpec_id = DB::table('tech_spec')->insertGetId($data);

            $portMap = $request->portMap;
            // dd($portMap);
            if (isset($request->portMap)) {
                foreach ($portMap as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_port_map')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'mapping_type' => @$a->mappingType,
                        'network_adapter' => @$a->networkAdapter,
                        'media_type' => @$a->mediaType,
                        'switch' => @$a->switch,
                        'port' => @$a->port,
                        'port_mode' => @$a->portMode,
                        'selectedIds' => is_array($a->selectedIds) ? implode(', ', @$a->selectedIds) : @$a->selectedIds,
                        'vlan_ids' => implode(', ', @$a->vlanIds),
                        'sub_text' => @$a->sub_text,
                        'comments' => @$a->comments,
                        'ssid' => @$a->ssid,
                    ]);
                }
            }
            $deviceArray = $request->deviceArray;
            if (isset($request->deviceArray)) {
                foreach ($deviceArray as $a) {
                    $a = json_decode($a);
                    DB::table('coorprate_device')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'asset_id' => $a->asset_id,
                        'serial' => $a->serial,
                        'cpu' => $a->cpu,
                        'memory' => $a->memory,
                        'manufacturer' => $a->manufacturer,
                        'model' => @$a->model,
                        'type' => @$a->type,
                        'name' => $a->name,
                        'device_type' => $a->device_type,
                    ]);
                }
            }
            $maintenance = $request->maintenance;
            if (isset($request->maintenance)) {
                foreach ($maintenance as $a) {
                    $a = json_decode($a);
                    DB::table('additional_maintenance')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'frequency' => $a->frequency,
                        'day' => $a->day,
                        'occurance' => $a->occurance,
                        'month' => $a->month,
                        'start_time' => $a->start_time,
                        'time_zone' => $a->time_zone,
                        'duration_hours' => $a->duration_hours,
                        'created_at' => Auth::user()->id,
                    ]);
                }
            }
            $ipDns_array = $request->ipDns;
            if (isset($request->ipDns)) {
                foreach ($ipDns_array as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_ip_dns')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'dns_type' => $a->type,
                        'alias' => $a->alias,
                        'vlan_id_no' => $a->vlan_id,
                        'vlan_id' => $a->vlan_text,
                        'host_name' => $a->host_name,
                        'ip_address' => $a->address,
                        'gateway' => $a->dns_gateway,
                        'description' => $a->description,
                        'primary_dns' => $a->primary_dns,
                        'secondary_dns' => $a->secondary_dns,
                        'primary_ntp' => $a->primary_ntp,
                        'secondary_ntp' => $a->secondary_ntp,
                        'subnet_ip' => $a->subnet_ip,
                        'mask' => $a->mask,
                        'gateway_ip' => $a->gateway,
                        'zone' => $a->zone,
                        'background' => $a->background,
                        'color' => $a->color,
                    ]);
                }
            }
            $networkAdapter_array = $request->networkAdapter;
            if (isset($request->networkAdapter)) {
                foreach ($networkAdapter_array as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_network_adapter')->insert([
                        'tech_spec_id' => $techSpec_id,
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
            $virtualDiskArray = $request->virtualDisk;
            if (isset($request->virtualDisk)) {
                foreach ($virtualDiskArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_virtual_disks')->insert([
                        'tech_spec_id' => $techSpec_id,
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
            $raidVolumeArray = $request->raidVolume;
            if (isset($request->raidVolume)) {
                foreach ($raidVolumeArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_raid_volume')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'name' => @$a->volume_name,
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
            $logicalVolumeArray = $request->logicalVolume;
            if (isset($request->logicalVolume)) {
                foreach ($logicalVolumeArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_logical_volume')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'source_disk' => @$a->source_disk,
                        'volume' => @$a->volume,
                        'volume_name' => @$a->volume_name,
                        'size' => @$a->size,
                        'size_unit' => @$a->size_unit,
                        'format' => @$a->format,
                        'block_size' => @$a->block_size
                    ]);
                }
            }

            $attachment_array = $request->attachmentArray;
            if (isset($request->attachmentArray)) {
                foreach ($attachment_array as $a) {
                    $a = json_decode($a);

                    copy(public_path('temp_uploads/' . $a->attachment), public_path('techspec_attachment/' . $a->attachment));
                    DB::table('tech_spec_attachments')->insert([
                        'tech_spec_id' => $techSpec_id,
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


                    DB::table('tech_spec_comments')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                        'comment' => $a->comment,
                        'name' => $a->name,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
            $emailArray = $request->emailArray;
            if (isset($request->emailArray)) {
                foreach ($emailArray as $a) {
                    $a = json_decode($a);


                    DB::table('tech_spec_email')->insert([
                        'tech_spec_id' => $techSpec_id,
                        'name' => $a->name,
                        'email' => $a->email,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
            // dd($techSpec_id);
            return response()->json(['status' => 'success', 'id' => $techSpec_id]);
        }
    }

    public function updateTechSpec(Request $request)
    {
        // dd($request->all());
        if ($request->update_id) {
            $image = '';
            if ($request->logo != '') {
                $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
                $request->file('logo')->move(public_path('client_logos'), $image);
            }
            $data = [
                'tech_spec_type' => $request->tech_spec_type,
                'client_id' => $request->client_id,
                'site_id' => $request->site,
                'sub_type' => $request->sub_type,
                'device_type' => $request->device_type ?? null,
                'asset_type' => $request->sub_type == 'Device' ? $request->asset_type : $request->asset_type_2,
                'platform' => $request->platform ?? null,
                'assigned_to' => $request->asigned_to,
                'approver' => $request->approver,
                'user_type' => $request->sub_type == 'User' ? $request->user_type : '',
                'user_image' => $request->sub_type == 'User' ? $image : '',
                'start_date' => $request->sub_type == 'User' ? date('Y-m-d', strtotime($request->user_info_start_date)) : '',
                'salutation' => $request->sub_type == 'User' ? $request->salutation : '',
                'firstname' => $request->sub_type == 'User' ? $request->firstname : '',
                'lastname' => $request->sub_type == 'User' ? $request->lastname : '',
                'contact_email' => $request->sub_type == 'User' ? $request->user_info_email : '',
                'contact_telephone' => $request->sub_type == 'User' ? $request->user_info_phone : '',
                'network_user_id' => $request->sub_type == 'User' ? $request->network_user_id : '',
                'network_ad_domain' => $request->sub_type == 'User' ? $request->network_id_domain : '',
                'network_azure_domain' => $request->sub_type == 'User' ? $request->azure_domain : '',
                'network_corporate_email' => $request->sub_type == 'User' ? $request->corporate_email : '',
                'network_corporate_telephone' => $request->sub_type == 'User' ? $request->corporate_phone : '',
                'network_extension' => $request->sub_type == 'User' ? $request->extension : '',
                'employee_no' => $request->sub_type == 'User' ? $request->employee_no_id : '',
                'employee_department' => $request->sub_type == 'User' ? $request->employee_info_department : '',
                'employee_manager' => $request->sub_type == 'User' ? $request->employee_info_manager : '',
                'project_no' => $request->sub_type == 'Device' ? $request->project_value : '',
                'project_change_no' => $request->sub_type == 'Device' ? $request->project_change_no : '',
                'project_due_date' => $request->sub_type == 'Device' ? date('Y-m-d', strtotime($request->project_due_date)) : '',
                'project_client_sponsor' => $request->sub_type == 'Device' ? $request->client_main_sponsor : '',
                'host_operating_system' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_os : '',
                'host_location' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_location : '',
                'host_role_description' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_description : '',
                'host_name' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_name : '',
                'host_domain' => $request->sub_type == 'Device' || $request->sub_type == 'Workstation' ? $request->host_info_domain : '',
                'host_system_type' => $request->sub_type == 'Device' ? $request->host_info_system_type : '',
                'host_system_category' => $request->sub_type == 'Device' ? $request->host_info_system_category : '',
                'host_ad_domain' => $request->sub_type == 'Device' && $request->platform == 'Windows' ? $request->host_info_addomain : '',
                'host_ad_ou' => $request->sub_type == 'Device' && $request->platform == 'Windows' ? $request->host_info_ou : '',
                'v_center_server' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->center_server : '',
                'cluster_host' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->cluster_host : '',
                'vm_folder' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_folder : '',
                'vm_datastore' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_datastore : '',
                'vm_restart_priority' => $request->sub_type == 'Device' && $request->device_type == 'Virtual' ? $request->vm_retart_priority : '',
                'disaster_recovery' => $request->sub_type == 'Device' ? ($request->disaster_recovery1 ?? 0) : '',
                'ssl_certificate' => $request->sub_type == 'Device' ? ($request->ntp ?? 0) : '',
                'supported' => $request->sub_type == 'Device' ? ($request->HasWarranty ?? 0) : '',
                'clustered' => $request->sub_type == 'Device' ? ($request->clustered ?? 0) : '',
                'internet_facing' => $request->sub_type == 'Device' ? ($request->internet_facing ?? 0) : '',
                'load_balanced' => $request->sub_type == 'Device' ? ($request->load_balancing ?? 0) : '',
                'hardware_information_manufacturer' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_manufacturer : $request->manufacturer) : ($request->sub_type == 'Workstation' ? $request->physical_server_manufacturer : ''),
                'hardware_information_model' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->model : $request->model_2) : ($request->sub_type == 'Workstation' ? $request->model : ''),
                'hardware_information_type' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->type : $request->type_2) : ($request->sub_type == 'Workstation' ? $request->type : ''),
                'hardware_information_sn' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_sn : $request->hardware_info_sn) : ($request->sub_type == 'Workstation' ? $request->physical_server_sn : ''),
                'hardware_information_cpu_model' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_model : '',
                'hardware_information_sockets' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_sockets : '',
                'hardware_information_cores' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_cores : '',
                'hardware_information_frequency' => ($request->sub_type == 'Device' && $request->asset_type == '5') || $request->sub_type == 'Workstation' ? $request->cpu_freq : '',
                'hardware_information_memory' => $request->sub_type == 'Device' ? ($request->asset_type == '5' ? $request->physical_server_memory : $request->hardware_info_memory) : ($request->sub_type == 'Workstation' ? $request->physical_server_memory : ''),
                'vm_cpu' => $request->device_type == 'Virtual' ? $request->vcpu : '',
                'vm_memory' => $request->device_type == 'Virtual' ? $request->vm_memory : '',
                'managed' => $request->sub_type == 'Device' ? ($request->managed ?? 0) : '',
                'app_owner' => $request->sub_type == 'Device' ? ($request->app_owner ?? '') : '',
                'sla' => $request->sub_type == 'Device' ? ($request->sla ?? '') : '',
                'patched' => $request->sub_type == 'Device' ? ($request->patched ?? '') : '',
                'monitored' => $request->sub_type == 'Device' ? ($request->monitored ?? '') : '',
                'backup' => $request->sub_type == 'Device' ? ($request->backup ?? '') : '',
                'anti_virus' => $request->sub_type == 'Device' ? ($request->antivirus ?? '') : '',
                'replicated' => $request->sub_type == 'Device' ? ($request->replicated ?? '') : '',
                'vulnerability_scan' => $request->sub_type == 'Device' ? ($request->disaster_recovery ?? '') : '',
                'siem' => $request->sub_type == 'Device' ? ($request->syslog ?? '') : '',
                'smtp' => $request->sub_type == 'Device' ? ($request->smtp ?? '') : '',
                'not_supported_reason' => $request->sub_type == 'Device' ? ($request->NotSupportedReason ?? '') : '',
                // 'stage' => 'Validate C\L',
                'created_by' => Auth::user()->id,
            ];

            $techSpec_id = DB::table('tech_spec')->where('id', $request->update_id)->update($data);

            $portMap = $request->portMap;
            // dd($portMap);
            if (isset($request->portMap)) {
                DB::table('tech_spec_port_map')->where('tech_spec_id', $request->update_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($portMap as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_port_map')->insert([
                        'tech_spec_id' => $request->update_id,
                        'mapping_type' => @$a->mappingType,
                        'network_adapter' => @$a->networkAdapter,
                        'media_type' => @$a->mediaType,
                        'switch' => @$a->switch,
                        'port' => @$a->port,
                        'port_mode' => @$a->portMode,
                        'selectedIds' => is_array($a->selectedIds) ? implode(', ', @$a->selectedIds) : @$a->selectedIds,
                        'vlan_ids' => implode(', ', @$a->vlanIds),
                        'sub_text' => @$a->sub_text,
                        'comments' => @$a->comments,
                        'ssid' => @$a->ssid,
                    ]);
                }
            }
            $deviceArray = $request->deviceArray;
            if (isset($request->deviceArray)) {
                DB::table('coorprate_device')->where('tech_spec_id', $request->update_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($deviceArray as $a) {
                    $a = json_decode($a);
                    DB::table('coorprate_device')->insert([
                        'tech_spec_id' => $request->update_id,
                        'asset_id' => $a->asset_id,
                        'serial' => $a->serial,
                        'cpu' => $a->cpu,
                        'memory' => $a->memory,
                        'manufacturer' => $a->manufacturer,
                        'model' => @$a->model,
                        'type' => @$a->type,
                        'name' => $a->name,
                        'device_type' => $a->device_type,
                    ]);
                }
            }
            $maintenance = $request->maintenance;
            if (isset($request->maintenance)) {
                DB::table('additional_maintenance')->where('tech_spec_id', $request->update_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($maintenance as $a) {
                    $a = json_decode($a);
                    DB::table('additional_maintenance')->insert([
                        'tech_spec_id' => $request->update_id,
                        'frequency' => $a->frequency,
                        'day' => $a->day,
                        'occurance' => $a->occurance,
                        'month' => $a->month,
                        'start_time' => $a->start_time,
                        'time_zone' => $a->time_zone,
                        'duration_hours' => $a->duration_hours,
                        'created_at' => Auth::user()->id,
                    ]);
                }
            }
            $ipDns_array = $request->ipDns;
            if (isset($request->ipDns)) {
                DB::table('tech_spec_ip_dns')->where('tech_spec_id', $request->update_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($ipDns_array as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_ip_dns')->insert([
                        'tech_spec_id' => $request->update_id,
                        'dns_type' => $a->type,
                        'alias' => $a->alias,
                        'vlan_id_no' => $a->vlan_id,
                        'vlan_id' => $a->vlan_text,
                        'host_name' => $a->host_name,
                        'ip_address' => $a->address,
                        'gateway' => $a->dns_gateway,
                        'description' => $a->description,
                        'primary_dns' => $a->primary_dns,
                        'secondary_dns' => $a->secondary_dns,
                        'primary_ntp' => $a->primary_ntp,
                        'secondary_ntp' => $a->secondary_ntp,
                        'subnet_ip' => $a->subnet_ip,
                        'mask' => $a->mask,
                        'gateway_ip' => $a->gateway,
                        'zone' => $a->zone,
                        'background' => $a->background,
                        'color' => $a->color,
                    ]);
                }
            }
            $networkAdapter_array = $request->networkAdapter;
            if (isset($request->networkAdapter)) {
                DB::table('tech_spec_network_adapter')->where('tech_spec_id', $request->update_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($networkAdapter_array as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_network_adapter')->insert([
                        'tech_spec_id' => $request->update_id,
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
            $virtualDiskArray = $request->virtualDisk;
            if (isset($request->virtualDisk)) {
                DB::table('tech_spec_virtual_disks')->where('tech_spec_id', $request->update_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($virtualDiskArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_virtual_disks')->insert([
                        'tech_spec_id' => $request->update_id,
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
            $raidVolumeArray = $request->raidVolume;
            if (isset($request->raidVolume)) {
                DB::table('tech_spec_raid_volume')->where('tech_spec_id', $request->update_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($raidVolumeArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_raid_volume')->insert([
                        'tech_spec_id' => $request->update_id,
                        'name' => @$a->volume_name,
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
            $logicalVolumeArray = $request->logicalVolume;
            if (isset($request->logicalVolume)) {
                DB::table('tech_spec_logical_volume')->where('tech_spec_id', $request->update_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($logicalVolumeArray as $a) {
                    $a = json_decode($a);
                    DB::table('tech_spec_logical_volume')->insert([
                        'tech_spec_id' => $request->update_id,
                        'source_disk' => @$a->source_disk,
                        'volume' => @$a->volume,
                        'volume_name' => @$a->volume_name,
                        'size' => @$a->size,
                        'size_unit' => @$a->size_unit,
                        'format' => @$a->format,
                        'block_size' => @$a->block_size
                    ]);
                }
            }

            $attachment_array = $request->attachmentArray;
            if (isset($request->attachmentArray)) {
                DB::table('tech_spec_attachments')->where('tech_spec_id', $request->update_id)->delete();
                foreach ($attachment_array as $a) {
                    $a = json_decode($a);

                    copy(public_path('temp_uploads/' . $a->attachment), public_path('techspec_attachment/' . $a->attachment));
                    DB::table('tech_spec_attachments')->insert([
                        'tech_spec_id' => $request->update_id,
                        'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                        'attachment' => $a->attachment,
                        'name' => $a->name,
                        'added_by' => Auth::id(),
                    ]);
                }
            }

            $commentArray = $request->commentArray;
            if (isset($request->commentArray)) {
                DB::table('tech_spec_comments')->where('tech_spec_id', $request->update_id)->delete();
                foreach ($commentArray as $a) {
                    $a = json_decode($a);


                    DB::table('tech_spec_comments')->insert([
                        'tech_spec_id' => $request->update_id,
                        'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                        'comment' => $a->comment,
                        'name' => $a->name,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
            $emailArray = $request->emailArray;
            if (isset($request->emailArray)) {
                DB::table('tech_spec_email')->where('tech_spec_id', $request->update_id)->update([
                    'is_deleted' => 1
                ]);
                foreach ($emailArray as $a) {
                    $a = json_decode($a);


                    DB::table('tech_spec_email')->insert([
                        'tech_spec_id' => $request->update_id,
                        'name' => $a->name,
                        'email' => $a->email,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error']);
    }


    public function getTechSpecContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('tech_spec as a')->where('a.id', $id)->first();
        $client = DB::table('clients')->where('id', $q->client_id)->where('is_deleted', 0)->first();
        if ($q->updated_at == null) {
            $user_data = DB::table('users')->where('id', $q->created_by)->first();
        } else {

            $user_data = DB::table('users')->where('id', $q->updated_by)->first();
        }
        $html .= '<div class="block card-round  ' . ($q->techspec_status == 1 ? "bg-new-blue" : "bg-new-red") . ' new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="../public/icons/icon-white-techspecs.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">' . @$client->client_display_name . ' ' . @$q->sub_type . ' ' . ucfirst(@$q->tech_spec_type) . '</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">Last Modified on ' . (@$q->updated_at == null ? date('Y-M-d', strtotime(@$q->created_at)) : date('Y-M-d', strtotime(@$q->updated_at)))  . ' by ' . $user_data->firstname . ' ' . $user_data->lastname . '</p>
                                    </div>
                                </div>';

        $html .= '<div class="new-header-icon-div d-flex align-items-center no-print">';

        // if ($q->status == '1') {
        //     $html .= '<a class=" btn-clone"   data="' . $q->id . '" data-id="' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Clone" class=" ">
        //     <img src="public/icons/icon-white-clone.png?cache=1" width="20px">
        // </a>';
        if ($q->techspec_status == '1') {
            $html .= '<a class="btnEnd" href="javascript:;" data="' . $q->id . '" data-id="' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Cancel" class=" ">
                                        <img src="../public/img/action-white-end-revoke.png" width="20px">
                                    </a>';
        } else {
            $html .= '<a class="btnEnd" href="javascript:;" data="' . $q->id . '" data-id="' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Re-open" class=" ">
                                    <img src="../public/img/status-white-active.png" width="20px">
                                </a>';
        }
        $html .= '<a class="" href="' . url("export-techspec?id=" . $q->id) . '" data="' . $q->id . '" data-id="' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Export" class=" ">
                                        <img src="../public/img/ui-icon-export.png" width="20px">
                                    </a>';
        $html .= '<a class="" href="' . url("techSpec-form/$q->tech_spec_type?id=" . $q->id) . '" data="' . $q->id . '" data-id="' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Clone" class=" ">
                                        <img src="../public/icons/icon-white-clone.png" width="20px">
                                    </a>';
        $html .= '<a href="javascript:;" onclick="window.print()" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="../public/img/action-white-print.png" width="20px">
                                            </a>';
        $html .= '<a class="" href="' . url("edit-techspecs") . '?id=' . $q->id . '" data="' . $q->id . '" data-id="' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit" class=" ">
                                        <img src="../public/img/action-white-edit.png" width="20px">
                                    </a>';
        // }
        // if ($q->status == '0') {


        //     $html .= '<span  > 
        //                              <a href="javascript:;" class="btnEnd"   data="' . $q->id . '" data-id="' . $q->id . '" data-ended="1" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="public/img/icon-header-white-reactivate.png?cache=1" width="20px"></a>
        //                          </span>';
        // }


        if (Auth::user()->role != 'read') {

            $html .= '
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="../public/img/action-white-delete.png" width="17px"></a>';
        }


        $client_data = DB::table('clients')
            ->where('id', $q->client_id)
            ->first();
        $asset_type_data = DB::table('asset_type')
            ->where('asset_type_id', @$q->asset_type)
            ->first();
        $site_data = DB::table('sites')->where('id', $q->site_id)->where('is_deleted', 0)->first();
        // $os_data = DB::table('operating_systems')
        //     ->where('id', $q->operating_system_id)
        //     ->first();


        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));
        $assigned_to = DB::Table('users')->where('id', $q->assigned_to)->where('is_deleted', 0)->first();
        $approver = DB::Table('users')->where('id', $q->approver)->where('is_deleted', 0)->first();
        // dd($q->assigned_to , $q->approver);
        $html .= '</div></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center px-5"
                      style="background-color: #ffffff; border: 1px solid lightgrey; border-radius: 10px; position: sticky; z-index: 100; top: 90px;">
                      <div class="d-flex flex-column align-items-center"><img
                              src="../public/icons/icon-user.png" alt="" style="width: 75px"
                              data-toggle="tooltip" data-trigger="hover" data-placement="right" title="" data-original-title="' . @$assigned_to->firstname . ' ' . @$assigned_to->lastname . '">
                          <p class="mb-0" style="color: #7F7F7F; font-size: 12pt;">Assigned to</p>
                      </div>
                      <div style="width: 80%">
                          <div class="container-1">
                              <ul class="container-progressbar">
                                  <li data="' . $id . '" class="active" ' . ($q->stage == "Finalize T/S" || $q->stage == null ? 'onclick="window.location.href=\'' . url("techSpec-form/$q->tech_spec_type") . '?id=' . $q->id . '\'"' : '') . '>Finalize T/S</li>

                                  <li data="' . $id . '" class="' . ($q->stage == "Validate C\L" ? "active last-active" : (in_array($q->stage, ['Assign', 'Execute', 'Submit', 'Review', 'Approve']) ? "active" : "")) . '" 
                                  ' . ($q->stage == "Validate C/L" ? 'id="ValidateCL"' : '') . '>Validate C/L</li>
                                  <li data-assignee="' . @$assigned_to->firstname . ' ' . @$assigned_to->lastname . '" data="' . $id . '" class="' . ($q->stage == "Assign" ? "active last-active" : (in_array($q->stage, ['Execute', 'Submit', 'Review', 'Approve']) ? "active" : "")) . '" ' . ($q->stage == "Assign" ? 'id="assign"' : '') . ' >Assign</li>
                                  <li data="' . $id . '" class="' . ($q->stage == "Execute" ? "active last-active" : (in_array($q->stage, ['Submit', 'Review', 'Approve']) ? "active" : "")) . '" ' . ($q->stage == "Execute" ? 'id="Execute"' : '') . ' >Execute</li>
                                  <li data-approver="' . @$approver->firstname . ' ' . @$approver->lastname . '" data="' . $id . '" class="' . ($q->stage == "Submit" ? "active last-active" : (in_array($q->stage, ['Review', 'Approve']) ? "active" : "")) . '" ' . ($q->stage == "Submit" ? 'id="submit"' : '') . ' >Submit</li>
                                  <li data="' . $id . '" class="' . ($q->stage == "Review" ? "active last-active" : (in_array($q->stage, ['Approve']) ? "active" : "")) . '" ' . ($q->stage == "Review" ? 'id="review"' : '') . ' >Review</li>
                                  <li data="' . $id . '" data-type="' . $q->sub_type . '" class="' . (($q->stage == "Approve" && $q->is_completed == "1") ? "active" : ($q->stage == "Approve" ? "active last-active" : "")) . '"  ' . (($q->stage == "Approve" && $q->is_completed == "0") ? 'id="approve"' : '') . ' >Approve</li>
                              </ul>
                          </div>
                      </div>
                      <div class="d-flex flex-column align-items-center"><img
                              src="../public/icons/icon-user.png" alt="" style="width: 75px"
                              data-toggle="tooltip" data-trigger="hover" data-placement="left" title="" data-original-title="' . @$approver->firstname . ' ' . @$approver->lastname . '">
                          <p class="mb-0"  style="color: #7F7F7F; font-size: 12pt;">Approver</p>
                      </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center px-2 mb-3" style="margin-top: -15px; position: sticky; z-index: 100; top: 217px;">
                    <div class="d-flex justify-content-between px-2 align-items-center py-2" style="min-width: 50%; width: fit-content; background-color: #F2F8FB; border: 1px solid #4bacc6; border-radius: 5px;">
                        ';
        if ($q->sub_type == 'Device') {
            $html .= '<div class="mr-3" style="white-space: nowrap;"><img src="../public/asset_icon/' . $asset_type_data->asset_icon . '" class="me-2" alt="" width="30">
                            <span>' . @$client->client_display_name . ', ' . $site_data->site_name . '</span></div>
                            ';
            if ($q->platform == "Windows") {
                $html .= '<div>
                                <img src="../public/img/icon-os-windows-color.png" alt="" width="24">
                            </div>';
            } elseif ($q->platform == "Linux") {
                $html .= '<div>
                                <img src="../public/img/icon-os-linux-color.png" alt="" width="30">
                            </div>';
            } elseif ($q->platform == "MacOS") {
                $html .= '<div>
                                <img src="../public/img/mac-icon.png" alt="" width="24">
                            </div>';
            } elseif ($q->platform == "ESXi") {
                $html .= '<div>
                                <img src="../public/img/icon-os-esxi-color.png" alt="" width="24">
                            </div>';
            }
        } else if ($q->sub_type == 'Workstation') {
            $html .= '<div class="mr-3" style="white-space: nowrap;"><img src="../public/icons/' . ($q->asset_type == "PC" ? 'icon-workstation-pc.png' : 'icon-workstation-laptop.png') . '" class="me-2" alt="" width="30">
                            <span>' . @$client->client_display_name . ', ' . $site_data->site_name . '</span></div>';
            if ($q->platform == "Windows") {
                $html .= '<div>
                                    <img src="../public/img/icon-os-windows-color.png" alt="" width="24">
                                </div>';
            } elseif ($q->platform == "Linux") {
                $html .= '<div>
                                    <img src="../public/img/icon-os-linux-color.png" alt="" width="30">
                                </div>';
            } elseif ($q->platform == "MacOS") {
                $html .= '<div>
                                    <img src="../public/img/mac-icon.png" alt="" width="24">
                                </div>';
            } elseif ($q->platform == "ESXi") {
                $html .= '<div>
                                    <img src="../public/img/icon-os-esxi-color.png" alt="" width="24">
                                </div>';
            }
        } elseif ($q->sub_type == 'User') {
            if ($q->user_image) {
                $html .= '<div class="mr-3" style="white-space: nowrap;"><img src="../public/client_logos/' . $q->user_image . '" class="me-2" alt="" width="30">
                                <span>' . @$client->firstname . ' ' . @$client->lastname . ', ' . $site_data->site_name . '</span></div>
                                <div style="color: #4194F6;">
                                    ' . date('D d M, Y', strtotime($q->start_date)) . '
                                </div>';
            } else {
                $html .= '<div class="mr-3" style="white-space: nowrap;"><img src="../public/icons/icon-user.png" class="me-2" alt="" width="30">
                                <span>' . @$client->firstname . ' ' . @$client->lastname . ', ' . $site_data->site_name . '</span></div>
                                <div style="color: #4194F6;">
                                    ' . date('D d M, Y', strtotime($q->start_date)) . '
                                </div>';
            }
        }
        $html .= '
                        
                    </div>
                    ';
        if ($q->status == 'Open') {
            $html .= '<div class="d-flex justify-content-between px-2 align-items-center py-2" style="width: 38%; background-color: #ffffff; border: 1px solid black; border-radius: 5px;">
                        <div><img src="../public/icons/icon-status-open.png" class="mr-3" alt="" width="24"><span style="">Status:</span>
     <span>Open</span></div>
                    </div>';
        } elseif ($q->status == 'Assigned') {
            $html .= '<div class="d-flex justify-content-between px-2 align-items-center py-2" style="width: 38%; background-color: #ffffff; border: 1px solid black; border-radius: 5px;">
                        <div><img src="../public/icons/icon-user.png" class="mr-3" alt="" width="24"><span style="">Status:</span>
     <span>Assigned</span></div>
                    </div>';
        } elseif ($q->status == 'In Progress') {
            $html .= '<div class="d-flex justify-content-between px-2 align-items-center py-2" style="width: 38%; background-color: #fbf9f2; border: 1px solid #FFAF04; border-radius: 5px;">
                        <div><img src="../public/icons/icon-status-inprogress.png" class="mr-3" alt="" width="24"><span style="color: #FFAF04;">Status:</span>
 <span>In Progress</span></div>
                    </div>';
        } elseif ($q->status == 'Pending Approval') {
            $html .= '<div class="d-flex justify-content-between px-2 align-items-center py-2" style="width: 38%; background-color: #f2f5fb; border: 1px solid #0070dd; border-radius: 5px;">
                    <div><img src="../public/icons/icon-status-pending-approval.png" class="mr-3" alt="" width="24"><span style="color: #0070dd;">Status:</span>
<span>Pending Approval</span></div>
                </div>';
        } elseif ($q->status == 'Approved') {
            $html .= '<div class="d-flex justify-content-between px-2 align-items-center py-2" style="width: 38%; background-color: #f2faf5; border: 1px solid #4EA833; border-radius: 5px;">
                    <div><img src="../public/icons/icon-status-pending-approval.png" class="mr-3" alt="" width="24"><span style="color: #4EA833;">Status:</span>
<span>Approved By ' . @$approver->firstname . ' ' . @$approver->lastname . '</span></div>
                </div>';
        } else {
            $html .= '<div class="d-flex justify-content-between px-2 align-items-center py-2" style="width: 38%; background-color: #FBF4F2; border: 1px solid #C41E3A; border-radius: 5px;">
                        <div><img src="../public/icons/icon-user.png" class="mr-3" alt="" width="24"><span style="COLOR: #C41E3A;">Status:</span>
     <span>Assigned</span></div>
                    </div>';
        }
        $html .= '
                  </div>

                        <div class="block new-block 5" style="padding-top: 0mm !important;">
                                              
                            <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;">
                             
                                <div class="row justify-content-  push" >
                                    <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">General Info</div>
                            
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
                                                
                                           <div class="bubble-white-new bubble-text-first client_name" style="color: #4194F6"><b>' . @$client->client_display_name . '</b></div> 
                                     
                                            </div>

                                         </div>

                                         
                                          <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Site</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first site_name" style="color:#7F7F7F;">' . @$site_data->site_name . '</div> 
                                     
                                            </div>

                                         </div>
                                          <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Type</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first tech_type" style="color:#7F7F7F;">' . ucfirst(@$q->tech_spec_type) . '</div> 
                                     
                                            </div>

                                         </div>
                                         <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Sub-type</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first sub_type" style="color:#7F7F7F;">' . @$q->sub_type . '</div> 
                                     
                                            </div>

                                         </div>';
        if (@$q->sub_type == 'Device') {
            $html .= '<div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Device Type</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first device_type" style="color:#7F7F7F;">' . @$q->device_type . '</div> 
                                     
                                            </div>
                                            </div>

                                         ';
        }
        if (@$q->sub_type == 'Device' || @$q->sub_type == 'Workstation') {
            if ($q->sub_type == 'Device') {
                $asset_type = DB::table('asset_type')->where('asset_type_id', @$q->asset_type)->where('is_deleted', 0)->first();
            }
            $html .= '<div class="form-group row"><div class="col-sm-4">
                                           <div class="bubble-new">Asset Type</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first asset_type" style="color:#7F7F7F;">' . (@$q->sub_type == 'Workstation' ? @$q->asset_type : @$asset_type->asset_type_description) . '</div> 
                                     
                                            </div>
                                            </div>';
            // dd($q->sub_type);
            $html .= '<div class="form-group row"><div class="col-sm-4">
                                           <div class="bubble-new">Platform</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first platform" style="color:#7F7F7F;">' . $q->platform . '</div> 
                                     
                                            </div>
                                            </div>

                                         
                                         
                                         ';
        }
        $html .= '</div><div class="col-sm-2" style="display: flex;
    flex-direction: column;
    justify-content: space-between;">
                                               <div class="bubble-text-sec" style="padding:10px">
                                         ';
        if (@$client_data->logo != '') {

            $html .= '<img src="../public/client_logos/' . @$client_data->logo . '" style="width: 100%;">';
        } else {
            $html .= '<img src="../public/img/image-default.png" style="width: 100%;">';
        }
        $html .= '</div>';
        // if ($q->platform) {
        //     $html .= '<div class="bubble-white-new bubble-text-sec" style="padding:10px">
        //                                   ';
        //     if (@$q->platform == 'Windows') {
        //         $html .= '<img src="public/img/icon-os-windows-color.png?cache=1" style="width: 100%;">';
        //     } else if (@$q->platform == 'ESXi') {
        //         $html .= '<img src="public/img/icon-os-esxi-color.png?cache=1" style="width: 100%;">';
        //     } else if (@$q->platform == 'MacOS') {
        //         $html .= '<img src="public/img/mac-icon.png?cache=1" style="width: 100%;">';
        //     } else if (@$q->platform == 'Linux') {
        //         $html .= '<img src="public/img/icon-os-linux-color.png?cache=1" style="width: 100%;">';
        //     }
        //     $html .= '

        //                                          </div> ';
        // }

        $html .= '</div>
        </div>

                                      
                                               </div>      

                         </div>

             </div>


         </div> ';
        if ($q->sub_type == 'Device') {
            $project_data = DB::table('project')->where('id', $q->project_no)->first();
            $project_client_main = DB::table('users')->select('id', 'email', DB::raw("CONCAT(firstname, ' ', lastname) as full_name"))->where('id', $q->project_client_sponsor)->where('is_deleted', 0)->first();
            $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
            <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
 <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Project Information</div>
                            
                            <div class="col-sm-12" >
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Project #</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$project_data->project_no . '</div> 
                                     
                                            </div>

                                         </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Description</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$project_data->description . '</div> 
                                     
                                            </div>

                                         </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Change #</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->project_change_no . '</div> 
                                     
                                            </div>

                                         </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Due Date</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . date('D d M, Y', strtotime(@$q->project_due_date)) . '</div> 
                                     
                                            </div>

                                         </div>
                                         <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Client Main Sponsor</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-5">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$project_client_main->full_name . '</div> 
                                     
                                            </div>
                                            <div class="col-sm-4">
                                                
                                           <div class="pt-2" style="color:#7F7F7F;">' . @$project_client_main->email . '</div> 
                                     
                                            </div>

                                         </div>
                                         
                                         </div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';
        }
        $checklist_data = DB::table('techspec_task')->where('tech_spec_id', $id)->get();
        $task_count = '001';
        if (count($checklist_data) > 0 && $q->stage != 'Validate C/L') {
            $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
            <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
 <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Checklist</div>';
            if ($q->stage == 'Review') {
                $html .= '<div class="col-sm-12 px-3">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="contract_type_button mb-3 ml-3">
                            <input type="checkbox" id="btn-na" name="btn-na" class="btn-na" value="1">
                            <label class="btn btn-new label1 btn-na-label" for="btn-na">Hide N/A</label>
                        </div>
                        <div class="contract_type_button mb-3 ml-3">
                            <input type="checkbox" id="btn-closed" name="btn-closed" class="btn-closed" value="1">
                            <label class="btn btn-new label1" for="btn-closed">Hide Closed</label>
                        </div>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end align-items-center pb-3">                        
                        <button data="' . $id . '" class="btn ml-5 btn-new px-5 review-task-btn">Review Tasks</button>
                    </div>
                </div>
            </div>';
            }

            $html .= '<div class="col-sm-12" >';
            foreach ($checklist_data as $row) {
                $task = DB::table('checklist_tasks')->where('id', @$row->task_id)->first();
                $task_user = DB::table('users')->where('id', @$row->added_by)->first();
                if ($task && $task_user) {
                    $html .= '<div class="js-task block block-rounded mb-2 checklist-new-block ' . @$row->status . '" style="position: relative;" data="${i}">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3 rounded px-1" style="margin-top: -5px; color: #7F7F7F; font-size: 2.60rem;">
                                                            <b style="' . (@$row->status == 'in-progress' ? 'color: #ffcc00;' : (@$row->status == 'complete' ? 'color: #4EA833;' : '')) . '">
                                                          ' . @$task_count . '</b></h1>
                                                    </td>
                                                    <td class="js-task-content pl-0" style="max-width: 296px">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="' . (@$row->status == 'in-progress' ? 'color: #ffcc00;' : (@$row->status == 'complete' ? 'color: #4EA833;' : '')) . 'display: flex;align-items: center; display: inline-block; max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle;">
                                                            ' . @$task->description . ' &nbsp;&nbsp
                                                        </div><span class="comments-subtext" style="display: block;">' . @$task_user->firstname . ' ' . @$task_user->lastname . ' | ' . date('d-M-Y H:i', strtotime(@$task->created_at)) . ' GMT</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                       <!-- -->
                                                       <div class="d-flex justify-content-end align-items-center">
                                                       <div class="mr-3">
                                                            <a data="' . @$task->id . '" class="task_details_btn" href="javascript:void()" ><img class="pt-1" src="../public/icons/icon-checklist-show-details.png" style="width: 35px;"></a>
                                                        </div>
                                                       <div class="mr-3">
                                                            <a data="${i}" class="" href="javascript:void()" ><img class="pt-1" src="../public/icons/icon-checklist-add-comments.png" style="width: 35px;"></a>
                                                        </div>
                                                       <div>';
                    if (@$row->status == 'na') {
                        $html .= '<a data-status="open" data="' . @$row->id . '" class="' . ($q->stage == 'Execute' || $q->approver == Auth::user()->id ? 'task-status-btn' : '') . '" href="javascript:void()" ><img src="../public/icons/icon-checklist-na.png" style="width: 35px;"></a>';
                    } elseif (@$row->status == 'open') {
                        $html .= '<a data-status="in-progress" data="' . @$row->id . '" class="' . ($q->stage == 'Execute' || $q->approver == Auth::user()->id ? 'task-status-btn' : '') . '" href="javascript:void()" ><img src="../public/icons/icon-status-open.png" style="width: 35px;"></a>';
                    } elseif (@$row->status == 'in-progress') {
                        $html .= '<a data-status="complete" data="' . @$row->id . '" class="' . ($q->stage == 'Execute' || $q->approver == Auth::user()->id ? 'task-status-btn' : '') . '" href="javascript:void()" ><img src="../public/icons/icon-status-inprogress.png" style="width: 35px;"></a>';
                    } else {
                        $html .= '<a data-status="done" data="' . @$row->id . '" class="' . ($q->stage == 'Execute' || $q->approver == Auth::user()->id ? 'task-status-btn' : '') . '" href="javascript:void()" ><img src="../public/icons/icon-status-done.png" style="width: 35px;"></a>';
                    }
                    $html .= '</div>
                                                        </div>
                                                    </td>
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
                    $task_count = str_pad((int)$task_count + 1, 3, '0', STR_PAD_LEFT);
                }
            }
            $html .= '</div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';
        }
        // user Information
        if ($q->sub_type == 'User') {
            $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">User Information</div>
                                                                            
                                                <div class="col-sm-12" >
                                                <div class="d-flex">
                                                <div class="col-10 pl-0">
                                                        <div class="form-group row">
                                                                            <div class="col-sm-4">
                                                                <div class="bubble-new">User Type</div> 
                                                            </div>
                                                                                                    
                                                                <div class="col-sm-4">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$q->user_type . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                                        <div class="form-group row">
                                                                            <div class="col-sm-4">
                                                                <div class="bubble-new">Start Date</div> 
                                                            </div>
                                                            <div class="col-sm-4">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . ($q->start_date ? date('d-M-Y', strtotime($q->start_date)) : '') . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                                            <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->salutation . '. ' . @$q->firstname . ' ' . @$q->lastname . '</div> 
                                     
                                            </div>

                                         </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Contact Email</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->contact_email . '</div> 
                                     
                                            </div>

                                         </div>
                                         <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Contact Telephone</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->contact_telephone . '</div> 
                                     
                                            </div>
                                            

                                         </div>
                                                </div>
                                                <div class="col-2 pr-0 d-flex justify-content-end">
                                                <div class="bubble-text-sec" style="padding:10px; ">
                                                <img src="../public/client_logos/' . @$client_data->logo . '" style="width: 75px;">
                                                </div>
                                                </div>
                                                </div>
                                     
                                         
                                         </div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';
            $network_domain = DB::table('domains')->where('id', $q->network_ad_domain)->where('is_deleted', 0)->first();
            $network_azure_domain = DB::table('domains')->where('id', $q->network_azure_domain)->where('is_deleted', 0)->first();
            $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                                    <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                        <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Network ID</div>
                                                                    
                                        <div class="col-sm-12" >
                                                <div class="form-group row">
                                                                    <div class="col-sm-3">
                                                        <div class="bubble-new">Network User ID</div> 
                                                    </div>
                                                                                            
                                                        <div class="col-sm-3">
                                                            
                                                        <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$q->network_user_id . '</div> 
                                                
                                                        </div>

                                                        <div class="col-sm-2">
                                                        <div class="bubble-new">A/D Domain</div> 
                                                    </div>
                                                    <div class="col-sm-3">
                                                            
                                                        <div class="bubble-white-new bubble-text-first" style="color: #4194F6">' . @$network_domain->domain_name . '</div> 
                                                
                                                        </div>

                                                    </div>
                                                <div class="form-group row">
                                                                    <div class="col-sm-3">
                                                        <div class="bubble-new">Azure Domain</div> 
                                                    </div>
                                                    <div class="col-sm-9">
                                                            
                                                        <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$network_azure_domain->domain_name . '</div> 
                                                
                                                        </div>

                                                    </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Corporate Email</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->network_corporate_email . '</div> 
                                     
                                            </div>

                                         </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Corporate Telephone</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->network_corporate_telephone . '</div> 
                                     
                                            </div>
                                            <div class="col-sm-2">
                                           <div class="bubble-new">Extension</div> 
                                       </div>
                                       <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->network_extension . '</div> 
                                     
                                            </div>

                                         </div>
                                         
                                         
                                         </div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';
            if (@$q->user_type != "External") {
                $departments = DB::table('departments')->where('id', $q->employee_department)->where('is_deleted', 0)->first();
                $managers = DB::table('managers')->where('id', $q->employee_department)->where('is_deleted', 0)->first();
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                            <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Employee Information</div>
                                                                        
                                            <div class="col-sm-12" >
                                                    <div class="form-group row">
                                                                        <div class="col-sm-3">
                                                            <div class="bubble-new">Employee ID</div> 
                                                        </div>
                                                                                                
                                                            <div class="col-sm-9">
                                                                
                                                            <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$q->employee_no . '</div> 
                                                    
                                                            </div>

                                                            

                                                        </div>
                                                    <div class="form-group row">
                                                                        <div class="col-sm-3">
                                                            <div class="bubble-new">Department</div> 
                                                        </div>
                                                        <div class="col-sm-9">
                                                                
                                                            <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$departments->name . '</div> 
                                                    
                                                            </div>

                                                        </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Manager</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$managers->name . '</div> 
                                     
                                            </div>
                                            <div class="col-sm-6 pt-2">
                                                
                                           <div>' . @$managers->email . '</div> 
                                     
                                            </div>

                                         </div>
                                     
                                         
                                         
                                         </div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';
            }

            $coorprate_device = DB::table('coorprate_device')->where('tech_spec_id', $id)->where('page', 'techspecs')->get();
            
            if (count($coorprate_device) > 0) {
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Corporate Assigned Device</div>
                                                                            
                                                <div class="col-sm-12" >';
                foreach ($coorprate_device as $row) {
                    $html .= '<div class="js-task block block-rounded mb-2" style="border:1px solid lightgrey;" data="${i}">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                        <div class="d-flex align-items-center">
                                                         <div class="mr-2">
                                                            <img src="../public/img/mac-icon.png" style="width: 50px;">
                                                        </div>
                                                         <div class="mr-2">
                                                            <img src="../public/icons/icon-workstation-laptop.png" style="width: 50px;">
                                                        </div>
                                                        </div>
                                                    </td>
                                                    <td class="js-task-content pl-0">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="display: flex;align-items: center;">
                                                            ' . $row->name . ' &nbsp;&nbsp
                                                        </div><span class="comments-subtext" style="display: block;">' . @$row->manufacturer . '  ' . @$row->model . '  ' . @$row->type . '</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                       <!-- -->
                                                       <div class="d-flex justify-content-end align-items-center">
                                                        <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important;border: none;" class=" bg-new-green ml-auto  badge-new  text-center  font-weight-bold   text-white">
                                                          <span class=" ">' . $row->serial . '</span>
                                                      </div>
                                                       <div class="ml-3"  data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                          data-title="' . $row->cpu . '" data-html="true"
                                                          data-original-title="<span class=\' HostActive text-yellow\' >CPU</span><span class=\'HostActive text-white\' > ' . $row->cpu . '</span><br><span class=\' HostActive text-yellow\' >Memory</span><span class=\'HostActive text-white\' > ' . $row->memory . ' GB</span>">
                                                            <a data="${i}" class="" href="javascript:void()"><img src="../public/icons/icon-details-vlanid.png" style="width: 40px;"></a>
                                                        </div>
                                                       
                                                          </div>
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

        if ($q->sub_type == 'Device') {
            $host_operating_system = DB::table('operating_systems')->where('id', $q->host_operating_system)->where('is_deleted', 0)->first();
            $host_domain = DB::table('operating_systems')->where('id', $q->host_domain)->where('is_deleted', 0)->first();
            $host_system_type = DB::table('system_types')->where('id', $q->host_system_type)->where('is_deleted', 0)->first();
            $host_system_category = DB::table('system_category')->where('id', $q->host_system_category)->where('is_deleted', 0)->first();
            $host_ad_domain = DB::table('domains')->where('id', $q->host_ad_domain)->where('is_deleted', 0)->first();
            $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
            <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
 <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Host Information</div>
                            
 <div class="col-sm-12" >
          <div class="form-group row">
                             <div class="col-sm-3">
                <div class="bubble-new">Operating System</div> 
            </div>
                                                       
                 <div class="col-sm-9">
                     
                <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$host_operating_system->operating_system_name . '</div> 
          
                 </div>

              </div>
          <div class="form-group row">
                             <div class="col-sm-3">
                <div class="bubble-new">Location</div> 
            </div>
            <div class="col-sm-9">
                     
                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->host_location . '</div> 
          
                 </div>

              </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Role/Description</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->host_location . '</div> 
                                     
                                            </div>

                                         </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Hostname</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$q->host_name . '</div> 
                                     
                                            </div>
                                            <div class="col-sm-3">
                                           <div class="bubble-new">Domain</div> 
                                       </div>
                                       <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$host_domain->domain_name . '</div> 
                                     
                                            </div>

                                         </div>';
            if (@$q->sub_type != 'Workstation') {
                $html .= '<div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">System Type</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$host_system_type->domain_name . '</div> 
                                     
                                            </div>
                                            <div class="col-sm-3">
                                           <div class="bubble-new">System Category</div> 
                                       </div>
                                       <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$host_system_category->domain_name . '</div> 
                                     
                                            </div>

                                         </div>';
            }
            if (@$q->platform == 'Windows') {
                $html .= '<div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">A/D Domain</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$host_ad_domain->domain_name . '</div> 
                                     
                                            </div>
                                            <div class="col-sm-3">
                                           <div class="bubble-new">A/D OU</div> 
                                       </div>
                                       <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->host_ad_ou . '</div> 
                                     
                                            </div>

                                         </div>';
            }

            $html .= '</div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';

            if ($q->device_type == "Virtual") {
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
            <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
 <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">VM Information</div>
                            
 <div class="col-sm-12" >
          <div class="form-group row">
                             <div class="col-sm-3">
                <div class="bubble-new">vCenter Server</div> 
            </div>
                                                       
                 <div class="col-sm-9">
                     
                <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$q->v_center_server . '</div> 
          
                 </div>

              </div>
          <div class="form-group row">
                             <div class="col-sm-3">
                <div class="bubble-new">Cluster/Host</div> 
            </div>
            <div class="col-sm-9">
                     
                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->cluster_host . '</div> 
          
                 </div>

              </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">VM Folder</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->vm_folder . '</div> 
                                     
                                            </div>

                                         </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">VM Datastore</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->vm_datastore . '</div> 
                                     
                                            </div>
                                            

                                         </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">VM Restart Priority</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->vm_restart_priority . '</div> 
                                     
                                            </div>
                                            

                                         </div>
                                         
                                         </div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';
            }
            $additional_maintenance = DB::table('additional_maintenance')->where('tech_spec_id', $id)->where('is_deleted', 0)->get();
            $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                                                <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                    <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Critical Information</div>
                                                                               <div class="col-12 mb-4">';
            foreach ($additional_maintenance as $m) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 50px;">
                                                         <p class="ml-3 mb-0 mr-3 rounded px-1" style="margin-top: -5px;background-color: #FFCC00;width: 75px;font-size: 18px;font-weight: 600;">
                                                          
                                                          ' . $m->frequency . '</p>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="display: flex;align-items: center;">
                                                            ' . $m->day . ' @ ' . $m->start_time . ' ' . $m->time_zone . ' for ' . $m->duration_hours . '-hours &nbsp;&nbsp</div><span class="comments-subtext mandatory" style="display: block;margin: .3rem;">Maintenance Window</span></h2>
                                                    </td>
                                                    <td style="width: 20%;" class="text-right pt-0">   </td>
                                                </tr>
                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div> 
                                                    <div class="col-sm-12" >
                                    <div class="row form-group ">
                                  <div class="col-sm-4">
                                      <div class="contract_type_button  w-100 mr-4 mb-3">
                                          <input type="checkbox" class="custom-control-input" id="disaster_recovery1"
                                              name="disaster_recovery1" disabled ' . ($q->disaster_recovery == 1 ? 'checked' : '') . '>
                                          <label class="btn btn-new w-75 " for="disaster_recovery1">D/R Plan</label>
                                      </div>
                                  </div>

                                  <div class="col-sm-4 text-center">
                                      <div class="contract_type_button  w-100 mr-4 mb-3">
                                          <input type="checkbox" class="custom-control-input" id="ntp"
                                              name="ntp" disabled ' . ($q->ssl_certificate == 1 ? 'checked' : '') . '>
                                          <label class="btn btn-new w-75 " for="ntp" data-toggle="tooltip"
                                              data-trigger="hover" data-html="true"
                                              title="Allows you to assign SSL Certs to asset"> SSL Certificate</label>
                                      </div>
                                  </div>
                                  <div class="col-sm-4 text-right ">
                                      <div class="contract_type_button  w-100 mr-4 mb-3">
                                          <input type="checkbox" class="custom-control-input" id="HasWarranty"
                                              name="HasWarranty" disabled ' . ($q->supported == 1 ? 'checked' : '') . '>
                                          <label class="btn btn-new w-75 supported " for="HasWarranty"
                                              data-toggle="tooltip" data-trigger="hover" data-html="true"
                                              title="Allows you to assign
                                            contracts to asset">
                                              Supported</label>
                                      </div>
                                  </div>
                                  <div class="col-sm-4  ">
                                      <div class="contract_type_button  w-100 mr-4  ">
                                          <input type="checkbox" class="custom-control-input" id="clustered"
                                              name="clustered" disabled ' . ($q->clustered == 1 ? 'checked' : '') . '>
                                          <label class="btn btn-new w-75 " for="clustered"> Clustered</label>
                                      </div>
                                  </div>
                                  <div class="col-sm-4 text-center">
                                      <div class="contract_type_button  w-100 mr-4  ">
                                          <input type="checkbox" class="custom-control-input" id="internet_facing"
                                              name="internet_facing" disabled ' . ($q->internet_facing == 1 ? 'checked' : '') . '>
                                          <label class="btn btn-new w-75 " for="internet_facing"> Internet Facing</label>
                                      </div>
                                  </div>
                                  <div class="col-sm-4  text-right">
                                      <div class="contract_type_button  w-100 mr-4 ">
                                          <input type="checkbox" class="custom-control-input" id="load_balancing"
                                              name="load_balancing" disabled ' . ($q->load_balanced == 1 ? 'checked' : '') . '>
                                          <label class="btn btn-new w-75 " for="load_balancing"> Load Balanced</label>
                                      </div>
                                  </div>
                              </div>                                         
                            </div>
                            </div>
                            </div>
                         </div>';

            if ($q->asset_type == 5 || $q->sub_type == 'Workstation') {
                $hardware_information_manufacturer = DB::table('vendors')->where('id', @$q->hardware_information_manufacturer)->where('is_deleted', 0)->first();
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Hardware Information</div>
                                                                            
                                                <div class="col-sm-12" >
                                                        <div class="form-group row">
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">Manufacturer</div> 
                                                            </div>
                                                                                                    
                                                                <div class="col-sm-9">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$hardware_information_manufacturer->vendor_name . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                                        <div class="form-group row">
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">Model</div> 
                                                            </div>
                                                            <div class="col-sm-3">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->hardware_information_model . '</div> 
                                                        
                                                                </div>
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">Type</div> 
                                                            </div>
                                                            <div class="col-sm-3">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->hardware_information_type . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                                </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Serial Number</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->hardware_information_sn . '</div> 
                                     
                                            </div>

                                         </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">CPU Model</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->hardware_information_cpu_model . '</div> 
                                     
                                            </div>

                                         </div>
                                         <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">No of Sockets</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-2">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->hardware_information_sockets . '</div> 
                                     
                                            </div>
                                            <div class="col-sm-2">
                                           <div class="bubble-new">No of Cores</div> 
                                       </div>
                                       <div class="col-sm-2">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->hardware_information_cores . '</div> 
                                     
                                            </div>
                                            <div class="col-sm-2">
                                           <div class="bubble-new">Frequency</div> 
                                       </div>
                                       <div class="col-sm-2">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->hardware_information_frequency . '</div> 
                                     
                                            </div>
                                            

                                         </div>
                                         <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Memory (GB)</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-2">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->hardware_information_memory . '</div> 
                                     
                                            </div>
                                            

                                         </div>
                                         
                                         </div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';
            }

            if ($q->device_type == 'Physical' && $q->asset_type != 5) {
                $hardware_information_manufacturer = DB::table('vendors')->where('id', @$q->hardware_information_manufacturer)->where('is_deleted', 0)->first();
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Hardware Information</div>
                                                                            
                                                <div class="col-sm-12" >
                                                        <div class="form-group row">
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">Manufacturer</div> 
                                                            </div>
                                                                                                    
                                                                <div class="col-sm-9">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$hardware_information_manufacturer->vendor_name . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                                        <div class="form-group row">
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">Model</div> 
                                                            </div>
                                                            <div class="col-sm-3">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->hardware_information_model . '</div> 
                                                        
                                                                </div>
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">Type</div> 
                                                            </div>
                                                            <div class="col-sm-3">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->hardware_information_type . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Serial Number</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->hardware_information_sn . '</div> 
                                     
                                            </div>

                                         </div>
                                     
                                         <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Memory (GB)</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-2">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->hardware_information_memory . '</div> 
                                     
                                            </div>
                                            

                                         </div>
                                                </div>
                                         
                                         </div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';
            }

            if ($q->device_type == 'Virtual') {
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">VM Resources</div>
                                                                            
                                                <div class="col-sm-12" >
                                                        <div class="form-group row">
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">vCPUs</div> 
                                                            </div>
                                                                                                    
                                                                <div class="col-sm-2">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7">' . @$q->vm_cpu . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                                        <div class="form-group row">
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">Memory (GB)</div> 
                                                            </div>
                                                            <div class="col-sm-2">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->vm_memory . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                                </div> 
                                         </div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';
            }

            $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Managed Services</div>
                                                                            
                                                <div class="col-sm-12" >
                                                        <div class="form-group row">
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">Managed</div> 
                                                            </div>
                                                                                                    
                                                                <div class="col-sm-3">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color:#4194F6; font-weight: 700;">' . (@$q->managed == '1' ? 'Managed' : 'Unmanaged') . '</div> 
                                                        
                                                                </div>

                                                            </div>';
            if ($q->managed == '1') {
                $html .= '<div class="form-group row">
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">App Owner</div> 
                                                            </div>
                                                            <div class="col-sm-3">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->app_owner . '</div> 
                                                        
                                                                </div>
                                                                            <div class="col-sm-3">
                                                                <div class="bubble-new">SLA</div> 
                                                            </div>
                                                            <div class="col-sm-2">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="background:' . (@$q->sla == 'Basic' ? '#4194f6' : ($q->sla == 'Critical' ? '#e54643' : '#c2c2c2')) . '; color:' . (@$q->sla == 'Basic' ? '#ffffff' : ($q->sla == 'Critical' ? '#ffffff' : '#4f4f4f')) . '">' . $q->sla . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                                </div> 

                                            <div class="row">

                                      <div class="col-sm-3 mb-3 ">

                                          <div class="contract_type_button  w-100 mr-4  px-4" data-toggle="tooltip"
                                              data-trigger="hover" data-placement="top" title=""
                                              data-original-title="System is patched automatically or manually">
                                              <input type="checkbox" class="custom-control-input" id="patched"
                                                  name="patched" disabled ' . ($q->patched == '1' ? 'checked' : '') . '>
                                              <label class="btn btn-new w-100 " for="patched"> Patched</label>
                                          </div>
                                      </div>

                                      <div class="col-sm-3   mb-3">

                                          <div class="contract_type_button  w-100 mr-4  px-4" data-toggle="tooltip"
                                              data-trigger="hover" data-placement="top" title=""
                                              data-original-title="System is monitored">
                                              <input type="checkbox" class="custom-control-input" id="monitored"
                                                  name="monitored" disabled ' . ($q->monitored == '1' ? 'checked' : '') . '>
                                              <label class="btn btn-new w-100 " for="monitored">Monitored</label>
                                          </div>
                                      </div>

                                      <div class="col-sm-3  mb-3">

                                          <div class="contract_type_button  w-100 mr-4 px-4 " data-toggle="tooltip"
                                              data-trigger="hover" data-placement="top" title=""
                                              data-original-title="System data is protected">
                                              <input type="checkbox" class="custom-control-input" id="backup"
                                                  name="backup" disabled ' . ($q->backup == '1' ? 'checked' : '') . '>
                                              <label class="btn btn-new w-100 " for="backup">Backup</label>
                                          </div>
                                      </div>


                                      <div class="col-sm-3  mb-3">

                                          <div class="contract_type_button  w-100 mr-4 px-4 " data-toggle="tooltip"
                                              data-trigger="hover" data-placement="top" title=""
                                              data-original-title="System has Anti-Virus installed">
                                              <input type="checkbox" class="custom-control-input" id="antivirus"
                                                  name="antivirus" disabled ' . ($q->anti_virus == '1' ? 'checked' : '') . '>
                                              <label class="btn btn-new w-100 " for="antivirus">Anti-Virus
                                              </label>
                                          </div>
                                      </div>


                                      <div class="col-sm-3  mb-3">

                                          <div class="contract_type_button  w-100 mr-4  px-4" data-toggle="tooltip"
                                              data-trigger="hover" data-placement="top" title=""
                                              data-original-title="System is replicated">
                                              <input type="checkbox" class="custom-control-input" id="replicated"
                                                  name="replicated" disabled ' . ($q->replicated == '1' ? 'checked' : '') . '>
                                              <label class="btn btn-new w-100 " for="replicated">Replicated
                                              </label>
                                          </div>
                                      </div>

                                      <div class="col-sm-3 ">

                                          <div class="contract_type_button  w-100 mr-4 px-4 " data-toggle="tooltip"
                                              data-trigger="hover" data-placement="top" title=""
                                              data-original-title="System is scanned by Drawbridge">
                                              <input type="checkbox" class="custom-control-input"
                                                  id="disaster_recovery" name="disaster_recovery" disabled ' . ($q->vulnerability_scan == '1' ? 'checked' : '') . '>
                                              <label class="btn btn-new w-100 " for="disaster_recovery">Vulnerability
                                                  Scan</label>
                                          </div>
                                      </div>

                                      <div class="col-sm-3  ">


                                          <div class="contract_type_button  w-100 mr-4  px-4" data-toggle="tooltip"
                                              data-trigger="hover" data-placement="top" title=""
                                              data-original-title="System sends info to SIEM/Syslog">
                                              <input type="checkbox" class="custom-control-input" id="syslog"
                                                  name="syslog" disabled ' . ($q->siem == '1' ? 'checked' : '') . '>
                                              <label class="btn btn-new w-100 " for="syslog">SIEM</label>
                                          </div>
                                      </div>

                                      <div class="col-sm-3  ">

                                          <div class="contract_type_button  w-100    px-4 " data-toggle="tooltip"
                                              data-trigger="hover" data-placement="top" title=""
                                              data-original-title="System requires SMTP Relay Access">
                                              <input type="checkbox" class="custom-control-input" id="smtp"
                                                  name="smtp" disabled ' . ($q->smtp == '1' ? 'checked' : '') . '>
                                              <label class="btn btn-new w-100 " for="smtp">SMTP</label>
                                          </div>
                                      </div>


                                  </div>';
            }
            $html .= '</div>
                                         
                                         </div>      

                         </div>
                         </div>
                                        ';

            $ipDns = DB::table('tech_spec_ip_dns')->where('tech_spec_id', $id)->where('is_deleted', 0)->get();
            if (count($ipDns) > 0) {
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">IP and DNS</div>
                                                                            
                                                <div class="col-sm-12" >';
                foreach ($ipDns as $ip) {
                    if ($ip->dns_type == "A") {
                        $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9"
                                          data-task-completed="false" data-task-starred="false">
                                          <div class="d-flex" style="padding: 0.75rem;">
                                            <div class="d-flex align-items-center" style="width: 20%;">
                                                <p class="ml-3 mb-0 mr-3 rounded px-3"
                                                              style="font-size: 27px;font-weight: 600;border: 1px solid #303030;border-radius: 20% !important;">
    
                                                              ' . $ip->dns_type . '</p>
                                                              <h2 class="mb-0 comments-text">
                                                          <div style="display: flex;align-items: center;margin-top: 2px;font-size: 35px;font-weight: 700;color: #4194f6;font-family: calibri;height: 29px;">
                                                              ' . $ip->vlan_id_no . ' </div><span
                                                              class="comments-subtext ml-0 mt-0 mb-0"
                                                              style="display: block;margin: .3rem;">VLANID</span>
                                                      </h2>
                                            </div>
                                            <div class="d-flex align-items-center" style="width: 30%;">
                                                <h2 class="mb-0 comments-text">
                                                              <div style="display: flex;align-items: center;">
                                                                  ' . $ip->subnet_ip . $ip->mask . '</div><span
                                                                  class="comments-subtext ml-0"
                                                                  style="display: block;margin: .3rem;">' . $ip->description . '</span>
                                                          </h2>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center" style="width: 42%;">
                                                <h2 class="mb-0 comments-text pr-3">
                                                              <div style="display: flex;justify-content: end;">
                                                                  ' . $ip->host_name . '</div><span
                                                                  class="comments-subtext mr-0"
                                                                  style="display: block;margin: .3rem;display: flex;justify-content: end;">' . ($ip->gateway == 'on' ? $ip->gateway_ip : 'N/A') . '</span>
                                                          </h2>
                                                          <div class="text-center font-size-md bubble-white-new border-none bubble-text-sec px-2"
                                                              style="background:' . $ip->background . ';color:' . $ip->color . ';width:fit-content!important;border-radius:5px;min-height:32px!important;border:none;">
                                                              <span class=" ">' . $ip->zone . '</span>
                                                          </div>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center" style="width: fit-content;">
                                                <a type="button"
                                                              data="${i}"
                                                              class="js-   btn btn-sm  text-warning">
                                                              <img src="../public/icons/icon-details-ip-dns.png" style="width: 25px;"
                                                                  data-toggle="tooltip" data-trigger="hover"
                                                                  data-placement="top" title="" data-html="true" data-original-title="<span class=\' HostActive text-yellow \' >Primary DNS </span><span class=\' HostActive text-white \' >' . $ip->primary_dns . '</span><br><span class=\' HostActive text-yellow \' >Secondary DNS </span><span class=\' HostActive text-white \' >' . $ip->secondary_dns . '</span>">
                                                          </a>
                                                        <a type="button"
                                                              data="${i}"
                                                              class="js-   btn btn-sm  text-warning">
                                                              <img src="../public/icons/icon-details-ntp.png" style="width: 25px;"
                                                                  data-toggle="tooltip" data-trigger="hover"
                                                                  data-placement="top" title="" data-html="true" data-original-title="<span class=\' HostActive text-yellow \' >Primary NTP </span><span class=\' HostActive text-white \' >' . $ip->primary_ntp . '</span><br><span class=\' HostActive text-yellow \' >Secondary NTP </span><span class=\' HostActive text-white \' >' . $ip->secondary_ntp . '</span>"}
                                                                                >
                                                          </a>
                                            </div>
                                            </div>
                                          
    
                                      </div>';
                    } else {
                        $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn">
                                      <table class="table table-borderless table-vcenter mb-0">
                                          <tbody>
                                              <tr>
                                                  <td class="text-center pr-0"
                                                      style="display: flex;align-items: center;">
                                                      <p class="ml-3 mb-0 mr-3 rounded px-3"
                                                          style="/* margin-top: -5px; *//* background-color: #FFCC00; *//* width: 169px; */font-size: 24px;font-weight: 600;border: 1px solid #303030;border-radius: 25px !important;">
    
                                                          ' . $ip->dns_type . '</p>
                                                          <h2 class="mb-0 comments-text" style="text-align: left;">
                                                              <div style="display: flex;">
                                                                  ' . $ip->alias . '</div><span
                                                                  class="comments-subtext"
                                                                  style="display: block;margin: .3rem;">
                                                                  ' . $ip->description . '</span>
                                                          </h2>
                                                  </td>
                                                  <td class="js-task-content text-center  pl-0">
                                                    <img src="../public/img/arrow-right.png" alt="" style="width: 65px;">
                                                </td>
                                                  <td class="js-task-content  pl-0">
                                                      <h2 class="mb-0 comments-text pr-2">
                                                          <div style="display: flex;justify-content: end;">
                                                              ' . $ip->host_name . '</div>
                                                      </h2>
                                                  </td>
                                                  
                                                  <td style="width: 20%;" class="text-right  0">
                                                  </td>
                                              </tr>
                                          </tbody>
                                      </table>
    
                                  </div>
                                  
                                  
                                  <div class="js-task block block-rounded mb-2 animated fadeIn">
                                        <div class="d-flex" style="padding: 0.75rem;">
                                            <div class="d-flex align-items-center" style="width: 20%;">
                                                <p class="ml-3 mb-0 mr-3 rounded px-3"
                                                              style="/* margin-top: -5px; *//* background-color: #FFCC00; *//* width: 169px; */font-size: 24px;font-weight: 600;border: 1px solid #303030;border-radius: 15px !important;">
    
                                                              ' . $ip->dns_type . '</p>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between" style="width: 30%;">
                                                <h2 class="mb-0 comments-text" style="text-align: left;">
                                                                  <div style="display: flex;">
                                                                      ' . $ip->alias . ' </div><span
                                                                      class="comments-subtext ml-0"
                                                                      style="display: block;margin: .3rem;">
                                                                      ' . $ip->description . '</span>
                                                              </h2>
                                                              <img src="../public/img/arrow-right.png" style="width: 130px;height: 50px;">
                                            </div>
                                            <div class="d-flex align-items-center justify-content-end" style="width: 30%;">
                                                <h2 class="mb-0 comments-text pr-2">
                                                              <div style="display: flex;justify-content: end;">
                                                                  ' . $ip->host_name . '</div>
                                                          </h2>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-end" style="width: 20%;">
                                            </div>
                                            
                                        </div>
                                          
    
                                      </div>';
                    }
                }
                $html .= '</div> 
                                        </div>
                                        
                                        </div>      
    
                                    </div>
                                    </div>
                                        ';
            }
            $networkAdapter = DB::table('tech_spec_network_adapter')->where('tech_spec_id', $id)->where('is_deleted', 0)->get();
            if (count($networkAdapter) > 0) {
                $html .= '<div class="block new-block mt-4" style="padding-top: 0mm !important;">
                                            <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push mb-0" >
                                                    <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Network Adapters</div>
                                                                                
                                                    <div class="col-sm-12" >';
                foreach ($networkAdapter as $row) {
                    if ($row->connection_type == "Virtual") {
                        $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                              <table class="table table-borderless table-vcenter mb-0">
                                                  <tbody>
                                                      <tr>
                                                          <td class="text-center pr-0" style="width: 50px;">
                                                               <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">vmnic' . $row->vmic . '</p>
                                                          </td>
                                                          <td class="js-task-content  pl-0">
                                                              <h2 class="mb-0 comments-text">
                                                                  <div style="display: flex;align-items: center;">
                                                                  ' . $row->port_group . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">' . $row->mac_address . '</span></h2>
                                                          </td>
                                                          <td style="width: 20%;" class="text-right  0">
                                                            <div class="d-flex">
                                                            <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important;border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                                              <span class=" ">' . $row->adapter_type . '</span>
                                                          </div>  </div></td>
                                                      </tr>
                                              </tbody>
                                          </table>
        
                                          </div>';
                    } else if ($row->connection_type == "Wifi") {
                        $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                              <table class="table table-borderless table-vcenter mb-0">
                                                  <tbody>
                                                      <tr>
                                                          <td class="text-center pr-0" style="width: 50px;">
                                                               <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size:24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">' . $row->connection_type . '</p>
                                                          </td>
                                                          <td class="js-task-content  pl-0">
                                                              <h2 class="mb-0 comments-text">
                                                                  <div style="display: flex;align-items: center;">
                                                                  ' . $row->adapter_name . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">' . $row->mac_address . '</span></h2>
                                                          </td>
                                                          <td style="width: 20%;" class="text-right  "><div class="d-flex">
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
                        $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                              <table class="table table-borderless table-vcenter mb-0">
                                                  <tbody>
                                                      <tr>
                                                          <td class="text-center pr-0" style="width: 50px;">
                                                               <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size:24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">' . $row->connection_type . '</p>
                                                          </td>
                                                          <td class="js-task-content  pl-0">
                                                              <h2 class="mb-0 comments-text">
                                                                  <div style="display: flex;align-items: center;">
                                                                  ' . $row->adapter_name . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">' . $row->mac_address . '</span></h2>
                                                          </td>
                                                          <td style="width: 20%;" class="text-right  "><div class="d-flex align-items-center justify-content-end"><div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important;border: none;" class=" bg-new-blue ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                                              <span class=" ">' . $tag_details . '</span>
                                                          </div>  
                                                          <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                                              <span class=" ">' . $row->port_media . '</span>
                                                          </div>
                                                               </div></td>
                                                      </tr>
                                              </tbody>
                                          </table>
        
                                          </div>';
                    }
                }
                $html .= '</div> 
                                            </div>
                                            
                                            </div>      
    
                                        </div>
                                        </div>
                                            ';
            }
            $portMap = DB::table('tech_spec_port_map')->where('tech_spec_id', $id)->where('is_deleted', 0)->get();
            if (count($portMap) > 0) {
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Port Mapping</div>
                                                                            
                                                <div class="col-sm-12" >';
                foreach ($portMap as $row) {
                    if ($row->mapping_type == "Wired") {
                        $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                          <table class="table table-borderless table-vcenter mb-0">
                                              <tbody>
                                                  <tr>
                                                      <td class="text-center pr-0" style="width: 50px;">
                                                           <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">                                                          
                                                            ' . @$row->mapping_type . '</p>
                                                      </td>
                                                      <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">
                                                              <div style="display: flex;align-items: center;">
                                                              ' . @$row->switch . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">Port Description</span></h2>
                                                      </td>
                                                      <td style="width: 20%;" class="text-right ">
                                                        <div class="d-flex">
                                                        <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-blue ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                                          <span class=" ">' . @$row->port_mode . '</span>
                                                      </div>
                                                      <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                                          <span class=" ">' . $row->media_type . '</span>
                                                      </div>
                                                      <a type="button" class="js-  btn btn-sm  text-warning">
                                                           <img src="../public/icons/icon-details-vlanid.png" style="width: 30px;"  data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                                            data-title="" data-html="true"
                                                                            data-original-title="<span class=\' HostActive text-yellow \' >vLAN IDs</span><br><span class=\' HostActive text-white \' >' . $row->selectedIds . '</span>">
                                                          </a>
                                                       </div></td>
                                                  </tr>
                                          </tbody>
                                      </table>
    
                                      </div>';
                    } else {
                        $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                          <table class="table table-borderless table-vcenter mb-0">
                                              <tbody>
                                                  <tr>
                                                      <td class="text-center pr-0" style="width: 50px;">
                                                           <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">                                                          
                                                            WIFI</p>
                                                      </td>
                                                      <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">
                                                              <div style="display: flex;align-items: center;">
                                                              ' . @$row->ssid . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">Comments</span></h2>
                                                      </td>
                                                      <td style="width: 20%;" class="text-right  ">
                                                        <div class="d-flex">
                                                        <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                                          <span class=" ">' . @$row->ssid . '</span>
                                                      </div>  </div></td>
                                                  </tr>
                                          </tbody>
                                      </table>
    
                                      </div>';
                    }
                }
                $html .= '</div> 
                                        </div>
                                        
                                        </div>      

                                    </div>
                                    </div>
                                        ';
            }
            $virtualDisks = DB::table('tech_spec_virtual_disks')->where('tech_spec_id', $id)->where('is_deleted', 0)->get();
            if (count($virtualDisks) > 0) {
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Virtual Disks</div>
                                                                            
                                                <div class="col-sm-12" >';
                foreach ($virtualDisks as $row) {
                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                          <table class="table table-borderless table-vcenter mb-0">
                                              <tbody>
                                                  <tr>
                                                      <td class="text-center pr-0" style="width: 50px;">
                                                           <p class="ml-3 mb-0 mr-3  px-1" style=" width: 125px;font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">' . $row->drive_size . ' ' . $row->drive_size_unit . '</p>
                                                      </td>
                                                      <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">
                                                              <div style="display: flex;align-items: center;">
                                                              ' . $row->datastore . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">vDisk' . $row->vdisk_no . '</span></h2>
                                                      </td>
                                                      <td style="width: 20%;" class="text-right  ">
                                                        <div class="d-flex">
                                                        <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-blue ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                                          <span class=" ">SCSI ' . $row->scsi_id_a . ':' . $row->scsi_id_b . '</span>
                                                      </div> <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-dark ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                                          <span class=" ">' . $row->device_type . '</span>
                                                      </div>  </div></td>
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
            $raidVolume = DB::table('tech_spec_raid_volume')->where('tech_spec_id', $id)->where('is_deleted', 0)->get();
            if (count($raidVolume) > 0) {
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Raid Volume</div>
                                                                            
                                                <div class="col-sm-12" >';
                foreach ($raidVolume as $row) {
                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                        <table class="table table-borderless table-vcenter mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-center pr-0" style="width: 50px;">
                                         <p class="ml-3 mb-0 mr-3  px-1" style=" width: 170px;font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px;">                                                          
                                          ' . $row->volume_size . '</p>
                                    </td>
                                    <td class="js-task-content  pl-0">
                                        <h2 class="mb-0 comments-text">
                                            <div style="display: flex;align-items: center;">
                                            ' . $row->controller . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">vDisk' . $row->name . '</span></h2>
                                    </td>
                                    <td style="width: 20%;" class="text-right ">
                                      <div class="d-flex">
                                      <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-blue ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                        <span class=" ">' . $row->drive_size . ' ' . $row->drive_size_unit . '</span>
                                    </div><div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                        <span class=" ">' . $row->drive_type . '</span>
                                    </div>
                                    <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none; color: #262626;" class=" bg-new-yellow ml-auto mr-3  badge-new  text-center  font-weight-bold ">
                                        <span class=" ">RAID' . $row->raid_level . '</span>
                                    </div> <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-dark ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                        <span class=" ">' . $row->no_of_sets . 'x' . $row->no_of_drives . '</span>
                                    </div>  </div></td>
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
            $logicalVolume = DB::table('tech_spec_logical_volume')->where('tech_spec_id', $id)->where('is_deleted', 0)->get();
            if (count($logicalVolume) > 0) {
                $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Logical Volume</div>
                                                                            
                                                <div class="col-sm-12" >';
                foreach ($logicalVolume as $row) {
                    $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                    <table class="table table-borderless table-vcenter mb-0">
                        <tbody>
                            <tr>
                                <td class="text-center pr-0" style="width: 50px;">
                                    <p class="ml-3 mb-0 mr-3  px-3" style=" font-size: 24px;font-weight: 600;border: 1px solid #595959;border-radius: 15px">' . $row->volume . '</p>
                                </td>
                                <td class="js-task-content  pl-0">
                                    <h2 class="mb-0 comments-text">
                                        <div style="display: flex;align-items: center;">
                                        ' . $row->source_disk . ' &nbsp;&nbsp</div><span class="comments-subtext" style="display: block;margin: .3rem;">vDisk' . $row->volume_name . '</span></h2>
                                </td>
                                <td style="width: 20%;" class="text-right  ">
                                    <div class="d-flex">
                                    <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-blue ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                    <span class=" ">' . $row->size . ' ' . $row->size_unit . '</span>
                                </div><div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none;" class=" bg-new-green ml-auto mr-3  badge-new  text-center  font-weight-bold   text-white">
                                    <span class=" ">' . $row->format . '</span>
                                </div>
                                <div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important; border: none; color: #262626;" class=" bg-new-yellow ml-auto mr-3  badge-new  text-center  font-weight-bold">
                                    <span class=" ">' . $row->block_size . '</span>
                                </div>
                                <a type="button" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-html="true" data-original-title="' . ($row->tooltip ? $row->tooltip : '')  . '">
                                    <img src="../public/icons/icon-details-raid-disk.png" style="margin-top: 2px; width: 26px;">
                                    </a>
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

        $html .= ' </div>
               </div> 
                  </div>
                    </div>
                      
 
';

        $contract = DB::table('tech_spec_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.tech_spec_id', $q->id)->get();
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
                                                          <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b></h1>
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




        $contract = DB::table('tech_spec_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.tech_spec_id', $q->id)->get();
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
                                                          <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
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




        $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';


        // dd($html);

        return response()->json($html);
    }

    public function getUserContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('tech_spec_users')->where('id', $id)->first();


        if ($q->status == 1) {
            $html .= '<div class="block card-round   bg-new-green new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/icon-active-removebg-preview.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">  ' . ($q->status == '1' ? 'Active' : 'Inactive') . ' User</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>';
        } else {
            $html .= '<div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/icon-ended-removebg-preview.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">' . ($q->status == '1' ? 'Active' : 'Inactive') . ' User</h4>';
            $renewed_qry = DB::Table('users')->Where('id', $q->Inactive_by)->first();



            $html .= '<p class="mb-0  header-new-subtext" style="line-height:17px">On ' . date('Y-M-d', strtotime($q->created_at)) . ' by ' . @$renewed_qry->firstname . ' ' . @$renewed_qry->lastname . '</p>
                                    </div>
                                </div>';
        }


        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print"> <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" > 
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="' . asset('public/img/paper-clip-white.png') . '" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="' . asset('public/img/comment-white.png') . '" width="20px"></a></span>';

        if (Auth::user()->role != 'read') {



            if ($q->status == 1) {
                $html .= '<span  > 
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->status . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Decomission" class=" "><img src="public/img/icon-header-white-end-decom.png?cache=1" width="22px"></a>
                                         </span>';
            } else {
                $html .= '    <span  > 
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->status . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="public/img/icon-header-white-reactivate.png?cache=1" width="22px"></a>
                                         </span>';
            }
        }

        $html .= '
                                                <img src="public/img/action-white-pdf.png?cache=1" width="24px"  >
                                            </a>
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png?cache=1" width="20px">
                                            </a>';


        if (Auth::user()->role != 'read') {

            $html .= '<a   href="' . url('edit-user-account') . '?id=' . $q->id . '" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png?cache=1" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png?cache=1" width="17px"></a>';
        }
        $client_data = DB::table('clients')
            ->where('id', $q->client_id)
            ->first();
        $network_domain = DB::table('domains')->where('id', $q->network_ad_domain)->where('is_deleted', 0)->first();
        $network_azure_domain = DB::table('domains')->where('id', $q->network_azure_domain)->where('is_deleted', 0)->first();
        $client = DB::table('clients')->where('id', $q->client_id)->where('is_deleted', 0)->first();
        $site_data = DB::table('sites')->where('id', $q->site_id)->where('is_deleted', 0)->first();
        $html .= '</div></div>
                            </div>
                        </div>

                            
                            <div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">User Information</div>
                                                                            
                                                <div class="col-sm-12" >
                                                <div class="d-flex">
                                                <div class="col-10 pl-0">
                                                <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Client</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first client_name" style="color: #4194F6"><b>' . @$client->client_display_name . '</b></div> 
                                     
                                            </div>

                                         </div>

                                         
                                          <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Site</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first site_name" style="color:#7F7F7F;">' . @$site_data->site_name . '</div> 
                                     
                                            </div>

                                         </div>
                                                        <div class="form-group row">
                                                                            <div class="col-sm-4">
                                                                <div class="bubble-new">User Type</div> 
                                                            </div>
                                                                                                    
                                                                <div class="col-sm-4">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$q->user_type . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                                        <div class="form-group row">
                                                                            <div class="col-sm-4">
                                                                <div class="bubble-new">Start Date</div> 
                                                            </div>
                                                            <div class="col-sm-4">
                                                                    
                                                                <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . ($q->start_date ? date('d-M-Y', strtotime($q->start_date)) : '') . '</div> 
                                                        
                                                                </div>

                                                            </div>
                                                            <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->salutation . '. ' . @$q->firstname . ' ' . @$q->lastname . '</div> 
                                     
                                            </div>

                                         </div>
                                     <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Contact Email</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->contact_email . '</div> 
                                     
                                            </div>

                                         </div>
                                         <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Contact Telephone</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . $q->contact_telephone . '</div> 
                                     
                                            </div>
                                            

                                         </div>
                                                </div>
                                                <div class="col-2 pr-0 d-flex flex-column align-items-end">
                                                <div class="bubble-text-sec" style="padding:10px">';
        if (@$client_data->logo != '') {

            $html .= '<img src="public/client_logos/' . @$client_data->logo . '" style="width: 100px;">';
        } else {
            $html .= '<img src="public/img/image-default.png" style="width: 100px;">';
        }
        $html .= '</div>
                                                <div class="bubble-text-sec" style="padding:10px; ">';
        if (@$q->user_image != '') {

            $html .= '<img src="public/client_logos/' . @$q->user_image . '" style="width: 100px;">';
        } else {
            $html .= '<img src="public/client_logos/icon-user.png" style="width: 100px;">';
        }
        $html .= '</div>
                                                </div>
                                                </div>
                                     
                                         
                                         </div>
                                         
                                         </div>      

                         </div>
                         </div>
<div class="block new-block 5" style="padding-top: 0mm !important;">
                                                    <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                        <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Network ID</div>
                                                                    
                                        <div class="col-sm-12" >
                                                <div class="form-group row">
                                                                    <div class="col-sm-3">
                                                        <div class="bubble-new">Network User ID</div> 
                                                    </div>
                                                                                            
                                                        <div class="col-sm-3">
                                                            
                                                        <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$q->network_user_id . '</div> 
                                                
                                                        </div>

                                                        <div class="col-sm-2">
                                                        <div class="bubble-new">A/D Domain</div> 
                                                    </div>
                                                    <div class="col-sm-3">
                                                            
                                                        <div class="bubble-white-new bubble-text-first" style="color: #4194F6">' . @$network_domain->domain_name . '</div> 
                                                
                                                        </div>

                                                    </div>
                                                <div class="form-group row">
                                                                    <div class="col-sm-3">
                                                        <div class="bubble-new">Azure Domain</div> 
                                                    </div>
                                                    <div class="col-sm-9">
                                                            
                                                        <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$network_azure_domain->domain_name . '</div> 
                                                
                                                        </div>

                                                    </div>
                                     
                                                        ';
        if (@$q->user_type != "External") {
            $html .= '<div class="form-group row"><div class="col-sm-3">
                                           <div class="bubble-new">Corporate Email</div> 
                                       </div><div class="col-sm-9">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->network_corporate_email . '</div> 
                                     
                                            </div></div>';
        }
        if (@$q->user_type != "External") {
            $html .= '<div class="form-group row">
                                                        <div class="col-sm-3">
                                           <div class="bubble-new">Corporate Telephone</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->network_corporate_telephone . '</div> 
                                     
                                            </div>
                                            <div class="col-sm-2">
                                           <div class="bubble-new">Extension</div> 
                                       </div>
                                       <div class="col-sm-3">
                                                
                                           <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$q->network_extension . '</div> 
                                     
                                            </div>

                                         </div>';
        }

        $html .= '</div>
                                         
                                         </div>      

                         </div>
                         </div>
         

         ';

        if (@$q->user_type != "External") {
            $departments = DB::table('departments')->where('id', $q->employee_department)->where('is_deleted', 0)->first();
            $managers = DB::table('managers')->where('id', $q->employee_manager)->where('is_deleted', 0)->first();
            // dd($q->employee_department, $q->employee_manager);
            $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                                    <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                        <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Employee Information</div>
                                                                    
                                        <div class="col-sm-12" >
                                                <div class="form-group row">
                                                                    <div class="col-sm-3">
                                                        <div class="bubble-new">Employee ID</div> 
                                                    </div>
                                                                                            
                                                        <div class="col-sm-9">
                                                            
                                                        <div class="bubble-white-new bubble-text-first" style="color: #4194F6; font-weight: 700;">' . @$q->employee_no . '</div> 
                                                
                                                        </div>

                                                        

                                                    </div>
                                                <div class="form-group row">
                                                                    <div class="col-sm-3">
                                                        <div class="bubble-new">Department</div> 
                                                    </div>
                                                    <div class="col-sm-9">
                                                            
                                                        <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$departments->name . '</div> 
                                                
                                                        </div>

                                                    </div>
                                 <div class="form-group row">
                                                    <div class="col-sm-3">
                                       <div class="bubble-new">Manager</div> 
                                   </div>
                                                                              
                                        <div class="col-sm-3">
                                            
                                       <div class="bubble-white-new bubble-text-first" style="color:#7F7F7F;">' . @$managers->name . '</div> 
                                 
                                        </div>
                                        <div class="col-sm-6 pt-2">
                                            
                                       <div>' . @$managers->email . '</div> 
                                 
                                        </div>

                                     </div>
                                 
                                     
                                     
                                     </div>
                                     
                                     </div>      

                     </div>
                     </div>
                                    ';
        }

        $coorprate_device = DB::table('coorprate_device')->where('tech_spec_id', $id)->where('page', 'users')->get();
        // dd($coorprate_device);
        if (count($coorprate_device) > 0) {
            $html .= '<div class="block new-block 5" style="padding-top: 0mm !important;">
                                        <div class="block-content pb-0" style="padding-left: 20px;padding-right: 20px;"> <div class="row justify-content- push" >
                                                <div class="mb-3" style="font-size: 20px; margin-left: 10px; font-weight: 600;">Corporate Assigned Device</div>
                                                                            
                                                <div class="col-sm-12" >';
            foreach ($coorprate_device as $row) {
                $device_icon = "";
                $platform_icon = "";
                if($row->device_type == 'Workstation'){
                    $device_icon = $row->asset_type == "PC" ? "icon-workstation-pc.png" : "icon-workstation-laptop.png";
                    if($row->platform == 'Windows'){
                        $platform_icon = "icon-os-windows-color.png";
                    } else if($row->platform == 'ESXi') {
                        $platform_icon = "icon-platform-esxi.png";
                    } else {
                        $platform_icon = "icon-platform-linuxos.png";
                    }
                } else {
                    $device_icon = "icon-device-mobile.png";
                    if($row->platform == 'IOS'){
                        $platform_icon = "icon-mobile-platform-ios.png";
                    } else {
                        $platform_icon = "icon-mobile-platform-android.png";
                    }
                }
                $html .= '<div class="js-task block block-rounded mb-2" style="border:1px solid lightgrey;" data="${i}">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                        <div class="d-flex align-items-center">
                                                         <div class="mr-2">
                                                            <img src="public/icons/'. $platform_icon .'" style="width: 50px;">
                                                        </div>
                                                         <div class="mr-2">
                                                            <img src="public/icons/'. $device_icon .'" style="width: 50px;">
                                                        </div>
                                                        </div>
                                                    </td>
                                                    <td class="js-task-content pl-0">
                                                        <h2 class="mb-0 comments-text">
                                                            <div style="display: flex;align-items: center;">
                                                            ' . ($row->device_type == "Workstation" ? $row->name : $row->serial) . ' &nbsp;&nbsp
                                                        </div><span class="comments-subtext" style="display: block;">' . @$row->manufacturer . '  ' . @$row->model . '  ' . @$row->type . '</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                       <!-- -->
                                                       <div class="d-flex justify-content-end align-items-center">';
                                                       if($row->device_type == "Workstation"){
                                                        $html .= '<div style="white-space: nowrap; padding: 6px 15px; font-size: 12pt !important;border: none;" class=" bg-new-green ml-auto  badge-new  text-center  font-weight-bold   text-white">
                                                          <span class=" ">' . $row->serial . '</span>
                                                      </div>';
                                                       }
                                                       $html .= '<div class="ml-3"  data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                          data-title="' . $row->cpu . '" data-html="true"
                                                          data-original-title="<span class=\' HostActive text-yellow\' >CPU</span><span class=\'HostActive text-white\' > ' . $row->cpu . '</span><br><span class=\' HostActive text-yellow\' >Memory</span><span class=\'HostActive text-white\' > ' . $row->memory . ' GB</span>">
                                                            <a data="${i}" class="" href="javascript:void()"><img src="public/icons/icon-details-vlanid.png" style="width: 40px;"></a>
                                                        </div>
                                                       
                                                          </div>
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


        $contract = DB::table('techspec_user_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('techspec_user_id', $q->id)->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative">
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Comments</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock"> ';
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




        $contract = DB::table('techspec_user_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('techspec_user_id', $q->id)->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative">
                                                <div class="mb-3" style="font-size: 20px; margin-left: 15px; font-weight: 600;">Attachments</div>
                            
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
 <a href="public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="' . url('public') . '/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
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

    public function getAssetList(Request $request)
    {
        $asset_type = $request->asset_type ? ($request->asset_type == 'Workstation' ? 'workstation' : 'mobile') : 'workstation';
        $data = DB::table('assets as a')
            ->select('a.*', 'v.vendor_name as manufacturer')
            ->join('vendors as v', 'v.id', '=', 'a.manufacturer')
            ->where('asset_type', $asset_type)
            ->where('AssetStatus', 1)
            ->where('client_id', $request->client_id)
            ->where('a.is_deleted', 0)->get();
        return response()->json($data);
    }
    public function getAsset(Request $request)
    {
        $id = $request->asset_id;
        $data = DB::table('asset_type')->where('asset_type_id', $id)->where('is_deleted', 0)->first();
        return response()->json($data);
    }

    public function saveProject(Request $request)
    {
        $request->validate([
            'project_no' => 'required|unique:project,project_no',
        ], [
            'project_no.required' => 'The project number is required.',
            'project_no.unique' => 'The project number already exist.',
        ]);
        $data = DB::table('project')->insertGetId([
            'project_no' => $request->project_no,
            'description' => $request->project_description,
        ]);
        return response()->json($data);
    }

    public function getprojectlist()
    {
        $data = DB::table('project')->where('is_deleted', 0)->orderBy('project_no', 'asc')->get();
        return response()->json($data);
    }
    public function saveDepartment(Request $request)
    {
        $request->validate([
            'new_department' => 'required|unique:departments,name',
        ], [
            'new_department.required' => 'The department name is required.',
            'new_department.unique' => 'The department name already exist.',
        ]);
        $data = DB::table('departments')->insertGetId([
            'name' => $request->new_department,
        ]);
        return response()->json($data);
    }
    public function getdepartmentlist()
    {
        $data = DB::table('departments')->where('is_deleted', 0)->orderBy('name', 'asc')->get();
        return response()->json($data);
    }
    public function saveManger(Request $request)
    {
        $request->validate([
            'manager_name' => 'required',
            'manager_email' => 'required|unique:managers,email|email',
        ], [
            'manager_name.required' => 'The manager name is required.',
            'manager_email.required' => 'The manager email is required.',
            'manager_email.unique' => 'The manager email already exist.',
        ]);
        $data = DB::table('managers')->insertGetId([
            'name' => $request->manager_name,
            'email' => $request->manager_email,
        ]);
        return response()->json($data);
    }
    public function getManagerlist()
    {
        $data = DB::table('managers')->where('is_deleted', 0)->orderBy('name', 'asc')->get();
        return response()->json($data);
    }
    public function getNetworkssid(Request $request)
    {
        $data = DB::table('network')->where('client_id', $request->client_id)->where('site_id', $request->site_id)->whereNotNull('ssid_name')->orderBy('ssid_name', 'asc')->get();
        return response()->json($data);
    }
    public function getSwitchlist(Request $request)
    {
        $data = DB::table('assets')->where('client_id', $request->client_id)->where('site_id', $request->site_id)->where('asset_type_id', 20)->whereNotNull('hostname')->orderBy('hostname', 'asc')->get();
        return response()->json($data);
    }
    public function getChecklists(Request $request)
    {
        $techSpec = DB::table('tech_spec')->where('id', $request->id)->where('is_deleted', 0)->first();
        if (!$techSpec) {
            return response()->json(['error' => 'Tech spec not found'], 404);
        }

        $checklistQuery = DB::table('checklist')
            ->where('client_id', $techSpec->client_id)
            ->where('checklist_status', $techSpec->tech_spec_type)
            ->where('sub_type', $techSpec->sub_type);

        if ($techSpec->sub_type == "Device") {
            $checklistQuery->where('asset_id', @$techSpec->asset_type)
                ->where('device_type', @$techSpec->device_type);
        }
        if ($techSpec->sub_type == "Workstation") {
            $checklistQuery->where('workstation_asset_type', @$techSpec->asset_type);
        }

        $checkLists = $checklistQuery->get();

        $data = [];

        if ($checkLists->isNotEmpty()) {
            foreach ($checkLists as $row) {
                $tasks = DB::table('checklist_tasks as c')
                    ->select('c.*', DB::raw("CONCAT(u.firstname, ' ', u.lastname) as added_by_name"))
                    ->where('checklist_id', $row->id)
                    ->join('users as u', 'u.id', '=', 'c.added_by')
                    ->get();
                foreach ($tasks as $t) {
                    $data[] = [
                        'checklist_id' => $row->id,
                        'task_id' => $t->id,
                        'responsible' => $t->responsible,
                        'description' => $t->description,
                        'added_by_name' => $t->added_by_name,
                        'details' => $t->details,
                        'dateTime' => date('d-M-Y H:i', strtotime($t->created_at)) . ' GMT',
                    ];
                }
            }
            return response()->json(['success' => true, 'data' => $data]);
        }

        return response()->json(['success' => false, 'data' => $techSpec]);
    }

    public function setChecklists(Request $request, $id)
    {

        DB::table('tech_spec')->where('id', $id)->update([
            'stage' => 'Assign'
        ]);

        $taskArray = $request->taskArray;
        if (isset($request->taskArray)) {
            foreach ($taskArray as $a) {
                $a = json_decode($a);
                DB::table('techspec_task')->insert([
                    'tech_spec_id' => $id,
                    'checklist_id' => @$a->checklist_id,
                    'task_id' => @$a->task_id,
                    'status' => @$a->status,
                    'created_by' => Auth::user()->id
                ]);
            }
        }

        return response()->json('success');
    }

    public function assign(Request $request, $id)
    {
        $settings = DB::Table('notification_settings')->first();
        $techSpec = DB::table('tech_spec')->where('id', $id)->where('is_deleted', 0)->first();
        $client = DB::table('clients')->where('id', $techSpec->client_id)->where('is_deleted', 0)->first();
        $site = DB::table('sites')->where('id', $techSpec->site_id)->where('is_deleted', 0)->first();
        $assigned_to = DB::table('users')->where('id', $techSpec->assigned_to)->where('is_deleted', 0)->first();
        $recipients = $assigned_to->email;
        // $recipients = 'danimughal8961@gmail.com';
        $data = array('emails' => $recipients, 'client_name' => $client->client_display_name, 'site_name' => $site->site_name, 'subject' => 'New ' . $techSpec->sub_type . ' ' . ucfirst($techSpec->tech_spec_type) . ' Assigned to you', 'tech_spec_id' => $techSpec->id, 'type' => $techSpec->tech_spec_type, 'sub_type' => $techSpec->sub_type, 'asset' => $techSpec->asset_type, 'device_type' => $techSpec->device_type, 'platform' => $techSpec->platform, 'from_name' => $settings->from_name, 'contract_description' => $request->contract_description);
        Mail::send('emails.assigned_to_email', ['data' => $data], function ($message) use ($data) {
            $message->to($data['emails']);
            $message->subject($data['subject']);
            $message->from('support@consultationamaltitek.com', $data['from_name']);
        });

        DB::table('tech_spec')->where('id', $id)->update([
            'stage' => 'Execute',
            'status' => 'Assigned'
        ]);
        return response()->json('success');
    }
    public function submit_approval(Request $request, $id)
    {
        $settings = DB::Table('notification_settings')->first();
        $techSpec = DB::table('tech_spec')->where('id', $id)->where('is_deleted', 0)->first();
        $client = DB::table('clients')->where('id', $techSpec->client_id)->where('is_deleted', 0)->first();
        $site = DB::table('sites')->where('id', $techSpec->site_id)->where('is_deleted', 0)->first();
        $approver = DB::table('users')->where('id', $techSpec->approver)->where('is_deleted', 0)->first();
        $recipients = $approver->email;
        // $recipients = 'danimughal8961@gmail.com';
        $data = array('emails' => $recipients, 'client_name' => $client->client_display_name, 'site_name' => $site->site_name, 'subject' => 'New ' . $techSpec->sub_type . ' ' . ucfirst($techSpec->tech_spec_type) . ' Assigned to you', 'tech_spec_id' => $techSpec->id, 'type' => $techSpec->tech_spec_type, 'sub_type' => $techSpec->sub_type, 'asset' => $techSpec->asset_type, 'device_type' => $techSpec->device_type, 'platform' => $techSpec->platform, 'from_name' => $settings->from_name, 'contract_description' => $request->contract_description);
        Mail::send('emails.approver', ['data' => $data], function ($message) use ($data) {
            $message->to($data['emails']);
            $message->subject($data['subject']);
            $message->from('support@consultationamaltitek.com', $data['from_name']);
        });

        DB::table('tech_spec')->where('id', $id)->update([
            'stage' => 'Review',
            'status' => 'Pending Approval'
        ]);
        return response()->json('success');
    }

    public function review_passed(Request $request, $id)
    {
        // $techSpec = DB::table('tech_spec')->where('id', $id)->where('is_deleted', 0)->first();
        DB::table('tech_spec')->where('id', $id)->update([
            'stage' => 'Approve'
        ]);

        return response()->json('success');
    }
    public function review_failed(Request $request, $id)
    {
        // $techSpec = DB::table('tech_spec')->where('id', $id)->where('is_deleted', 0)->first();
        DB::table('tech_spec')->where('id', $id)->update([
            'stage' => 'Execute'
        ]);

        return response()->json('success');
    }

    public function updateTaskStatus(Request $request)
    {
        $techspec_task = DB::table('techspec_task')->where('id', $request->id)->first();
        $techspec = DB::table('tech_spec')->where('id', $techspec_task->tech_spec_id)->first();

        if ($techspec->approver == Auth::user()->id) {
            DB::table('techspec_task')->where('id', $request->id)->update([
                'status' => 'open'
            ]);
        } else {
            DB::table('techspec_task')->where('id', $request->id)->update([
                'status' => $request->status
            ]);
            DB::table('tech_spec')->where('id', $techspec_task->tech_spec_id)->update([
                'status' => 'In Progress'
            ]);

            $c = DB::table('techspec_task')
                ->where('tech_spec_id', $techspec_task->tech_spec_id)
                ->where(function ($query) {
                    $query->where('status', 'open')
                        ->orWhere('status', 'in-progress');
                })
                ->count();
            if ($c == 0) {
                DB::table('tech_spec')->where('id', $techspec_task->tech_spec_id)->update([
                    'stage' => 'Submit'
                ]);
            }
        }

        return response()->json('success');
    }

    public function supponser_notification(Request $request, $id)
    {
        $settings = DB::Table('notification_settings')->first();
        $techSpec = DB::table('tech_spec')->where('id', $id)->where('is_deleted', 0)->first();
        $client = DB::table('clients')->where('id', $techSpec->client_id)->where('is_deleted', 0)->first();
        $site = DB::table('sites')->where('id', $techSpec->site_id)->where('is_deleted', 0)->first();
        $supponser = DB::table('users')->where('id', $techSpec->project_client_sponsor)->where('is_deleted', 0)->first();
        // dd($techSpec->project_client_sponsor);
        $recipients = @$supponser->email;
        // $recipients = 'danimughal8961@gmail.com';
        $data = array('emails' => $recipients, 'client_name' => $client->client_display_name, 'site_name' => $site->site_name, 'subject' => $techSpec->sub_type . ' ' . ucfirst($techSpec->tech_spec_type) . ' Completed', 'tech_spec_id' => $techSpec->id, 'type' => $techSpec->tech_spec_type, 'sub_type' => $techSpec->sub_type, 'asset' => $techSpec->asset_type, 'device_type' => $techSpec->device_type, 'platform' => $techSpec->platform, 'from_name' => $settings->from_name, 'contract_description' => $request->contract_description);
        if ($techSpec->project_client_sponsor) {
            Mail::send('emails.supponser', ['data' => $data], function ($message) use ($data) {
                $message->to($data['emails']);
                $message->subject($data['subject']);
                $message->from('support@consultationamaltitek.com', $data['from_name']);
            });
        }
        $assigned_to = DB::table('users')->where('id', $techSpec->assigned_to)->where('is_deleted', 0)->first();
        $recipients = $assigned_to->email;
        // $recipients = 'danimughal8961@gmail.com';
        Mail::send('emails.techspecapprove', ['data' => $data], function ($message) use ($data) {
            $message->to($data['emails']);
            $message->subject($data['subject']);
            $message->from('support@consultationamaltitek.com', $data['from_name']);
        });

        DB::table('tech_spec')->where('id', $id)->update([
            'is_completed' => 1,
            'stage' => 'Approve',
            'status' => 'Approved'
        ]);
        return response()->json('success');
    }
    public function tech_spec_approve(Request $request, $id)
    {
        $settings = DB::Table('notification_settings')->first();
        $techSpec = DB::table('tech_spec')->where('id', $id)->where('is_deleted', 0)->first();
        $client = DB::table('clients')->where('id', $techSpec->client_id)->where('is_deleted', 0)->first();
        $site = DB::table('sites')->where('id', $techSpec->site_id)->where('is_deleted', 0)->first();
        $approver = DB::table('users')->where('id', $techSpec->assigned_to)->where('is_deleted', 0)->first();
        $recipients = $approver->email;
        // $recipients = 'danimughal8961@gmail.com';
        $data = array('emails' => $recipients, 'client_name' => $client->client_display_name, 'site_name' => $site->site_name, 'subject' => $techSpec->sub_type . ' ' . ucfirst($techSpec->tech_spec_type) . ' Approved', 'tech_spec_id' => $techSpec->id, 'type' => $techSpec->tech_spec_type, 'sub_type' => $techSpec->sub_type, 'asset' => $techSpec->asset_type, 'device_type' => $techSpec->device_type, 'platform' => $techSpec->platform, 'from_name' => $settings->from_name, 'contract_description' => $request->contract_description);
        Mail::send('emails.techspecapprove', ['data' => $data], function ($message) use ($data) {
            $message->to($data['emails']);
            $message->subject($data['subject']);
            $message->from('support@consultationamaltitek.com', $data['from_name']);
        });

        DB::table('tech_spec')->where('id', $id)->update([
            'is_completed' => 1,
            'stage' => 'Approve',
            'status' => 'Approved'
        ]);
        return response()->json('success');
    }

    public function EndTechSpec(Request $request)
    {
        if ($request->end == 1) {
            DB::Table('tech_spec')->where('id', $request->id)->update(['techspec_status' => '1']);
            DB::table('tech_spec_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'tech_spec_id' => $request->id, 'comment' => 'Tech Specs Re-opened.<br>' . $request->reason]);
            return redirect()->back()->with('success', 'Tech Specs Re-opened');
        } else {
            DB::Table('tech_spec')->where('id', $request->id)->update(['techspec_status' => '0']);
            DB::table('tech_spec_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'tech_spec_id' => $request->id, 'comment' => 'Tech Specs Canceled.<br>' . $request->reason]);
            return redirect()->back()->with('success', 'Tech Specs Canceled');
        }
    }

    public function exportCSV(Request $request)
    {
        $techspecs = DB::table('tech_spec')->where('id', $request->id)->first();

        $csvData = [];
        if ($techspecs) {
            $client = DB::table('clients')->where('id', $techspecs->client_id)->first();
            $assigned_to = DB::table('users')->where('id', $techspecs->assigned_to)->first();
            $approver = DB::table('users')->where('id', $techspecs->approver)->first();
            $site = DB::table('sites')->where('id', $techspecs->site_id)->first();

            if ($techspecs->sub_type == "User") {
                $department = DB::table('departments')->where('id', $techspecs->employee_department)->first();
                $manager = DB::table('managers')->where('id', $techspecs->employee_manager)->first();
                $project = DB::table('project')->where('id', $techspecs->project_no)->first();
                $sponsor = DB::table('clients')->where('id', $techspecs->project_client_sponsor)->first();
                $network_ad_domain = DB::table('network')->where('id', $techspecs->network_ad_domain)->first();
                $network_azure_domain = DB::table('network')->where('id', $techspecs->network_azure_domain)->first();
                $csvData[] = [
                    'Tech Specs Type' => ucfirst($techspecs->tech_spec_type),
                    'Client' => $client->client_display_name ?? '',
                    'Site' => $site->site_name ?? '',
                    'Sub Type' => $techspecs->sub_type,
                    'Assigned To' => $assigned_to->firstname . ' ' . $assigned_to->lastname,
                    'Approver' => $approver->firstname . ' ' . $approver->lastname,
                    'User Type' => $techspecs->user_type,
                    'Start Date' => date('M d,Y', strtotime($techspecs->start_date)),
                    'Salutation' => $techspecs->salutation,
                    'First Name' => $techspecs->firstname,
                    'Last Name' => $techspecs->lastname,
                    'Contact Email' => $techspecs->contact_email,
                    'Contact Phone' => $techspecs->contact_telephone,
                    'Network User ID' => $techspecs->network_user_id,
                    'AD Domain' => $network_ad_domain->description ?? '',
                    'Azure Domain' => $network_azure_domain->description ?? '',
                    'Corporate Email' => $techspecs->network_corporate_email,
                    'Corporate Telephone' => $techspecs->network_corporate_telephone,
                    'Network Extension' => $techspecs->network_extension,
                    'Employee no' => $techspecs->employee_no,
                    'Department' => $department->name ?? '',
                    'Manager' => $manager->name ?? '',
                    'Project No' => $project->project_no ?? '',
                    'Change No' => $techspecs->project_change_no,
                    'Due Date' => date('M d,Y', strtotime($techspecs->project_due_date)),
                    'Client Main Sponsor' => $sponsor->client_display_name ?? '',
                    'Stage' => $techspecs->stage ?? '',
                    'Status' => $techspecs->status ?? '',
                ];
            } else if ($techspecs->sub_type == "Workstation") {
                $os = DB::table('operating_systems')->where('id', $techspecs->host_operating_system)->first();
                $host_domain = DB::table('network')->where('id', $techspecs->host_domain)->first();
                $manufacturer = DB::table('vendors')->where('id', $techspecs->host_domain)->first();
                $csvData[] = [
                    'Tech Specs Type' => ucfirst($techspecs->tech_spec_type),
                    'Client' => $client->client_display_name ?? '',
                    'Site' => $site->site_name ?? '',
                    'Sub Type' => $techspecs->sub_type,
                    'Asset Type' => $techspecs->asset_type,
                    'Platform' => $techspecs->platform,
                    'Assigned To' => $assigned_to->firstname . ' ' . $assigned_to->lastname,
                    'Approver' => $approver->firstname . ' ' . $approver->lastname,
                    'Operating System' => $os->operating_system_name,
                    'Location' => $techspecs->host_location,
                    'Role/Description' => $techspecs->host_role_description,
                    'Hostname' => $techspecs->host_name ?? '',
                    'Domain' => $host_domain->description ?? '',
                    'Manufacturer' => $manufacturer->vendor_name ?? '',
                    'Model' => $techspecs->hardware_information_model ?? '',
                    'Type' => $techspecs->hardware_information_type ?? '',
                    'Serial Number' => $techspecs->hardware_information_sn ?? '',
                    'CPU' => $techspecs->hardware_information_cpu_model ?? '',
                    'Sockets' => $techspecs->hardware_information_sockets ?? '',
                    'Cores' => $techspecs->hardware_information_cores ?? '',
                    'Frequency' => $techspecs->hardware_information_frequency ?? '',
                    'Memory' => $techspecs->hardware_information_memory ?? '',
                    'Stage' => $techspecs->stage ?? '',
                    'Status' => $techspecs->status ?? '',
                ];
            } else if ($techspecs->sub_type == "Device") {
                $os = DB::table('operating_systems')->where('id', $techspecs->host_operating_system)->first();
                $asset_type = DB::table('asset_type')->where('asset_type_id', $techspecs->asset_type)->first();
                $project = DB::table('project')->where('id', @$techspecs->project_no)->first();
                $project_sponsor = DB::table('users')->where('id', $techspecs->project_client_sponsor)->first();
                $host_domain = DB::table('network')->where('id', @$techspecs->host_domain)->first();
                $ad_domain = DB::table('network')->where('id', @$techspecs->host_ad_domain)->first();
                $manufacturer = DB::table('vendors')->where('id', $techspecs->host_domain)->first();
                $system_type = DB::table('system_types')->where('id', @$techspecs->host_system_type)->first();
                $system_category = DB::table('system_category')->where('id', @$techspecs->host_system_category)->first();
                $csvData[] = [
                    'Tech Specs Type' => ucfirst($techspecs->tech_spec_type),
                    'Client' => @$client->client_display_name ?? '',
                    'Site' => @$site->site_name ?? '',
                    'Sub Type' => @$techspecs->sub_type,
                    'Device Type' => @$techspecs->device_type,
                    'Asset Type' => @$asset_type->asset_type_description,
                    'Platform' => @$techspecs->platform,
                    'Assigned To' => @$assigned_to->firstname . ' ' . @$assigned_to->lastname,
                    'Approver' => @$approver->firstname . ' ' . @$approver->lastname,
                    'Project no' => @$project->project_no,
                    'Description' => @$project->description,
                    'Change no' => @$techspecs->project_change_no,
                    'Due Date' => date('M d,Y', strtotime(@$techspecs->project_due_date)),
                    'Client Main Sponsor' => @$project_sponsor->firstname . ' ' . @$project_sponsor->lastname,


                    'Operating System' => @$os->operating_system_name,
                    'Location' => @$techspecs->host_location,
                    'Role/Description' => @$techspecs->host_role_description,
                    'Hostname' => @$techspecs->host_name ?? '',
                    'Domain' => @$host_domain->description ?? '',
                    'System Type' => @$system_type->domain_name ?? '',
                    'System Category' => @$system_category->domain_name ?? '',
                    'A/D Domain' => @$ad_domain->domain_name ?? '',
                    'A/D OU' => @$techspecs->host_ad_ou ?? '',


                    'D/R Plan' => @$techspecs->disaster_recovery == "1" ? 'Yes' : 'No',
                    'SSL Certificate' => @$techspecs->ssl_certificate == "1" ? 'Yes' : 'No',
                    'Support' => @$techspecs->supported == "1" ? 'Yes' : 'No',
                    'Clustered' => @$techspecs->clustered == "1" ? 'Yes' : 'No',
                    'Internet Facing' => @$techspecs->internet_facing == "1" ? 'Yes' : 'No',
                    'Load Balanced' => @$techspecs->load_balanced == "1" ? 'Yes' : 'No',

                    'Manufacturer' => @$manufacturer->vendor_name ?? '',
                    'Model' => @$techspecs->hardware_information_model ?? '',
                    'Type' => @$techspecs->hardware_information_type ?? '',
                    'Serial Number' => @$techspecs->hardware_information_sn ?? '',
                    'Memory' => @$techspecs->hardware_information_memory ?? '',


                    'Managed' => @$techspecs->managed == "1" ? 'Managed' : 'Unmanaged',
                    'App Owner' => @$techspecs->app_owner,
                    'SLA' => @$techspecs->sla,
                    'Patched' => @$techspecs->patched == "1" ? 'Yes' : 'No',
                    'Monitored' => @$techspecs->monitored == "1" ? 'Yes' : 'No',
                    'Backup' => @$techspecs->backup == "1" ? 'Yes' : 'No',
                    'Anti-virus' => @$techspecs->anti_virus == "1" ? 'Yes' : 'No',
                    'Replicated' => @$techspecs->replicated == "1" ? 'Yes' : 'No',
                    'Vulnerability Scan' => @$techspecs->vulnerability_scan == "1" ? 'Yes' : 'No',
                    'SIEM' => @$techspecs->siem == "1" ? 'Yes' : 'No',
                    'SMTP' => @$techspecs->smtp == "1" ? 'Yes' : 'No',


                    'Stage' => @$techspecs->stage ?? '',
                    'Status' => @$techspecs->status ?? '',
                ];
            }
        }

        $filename = 'techspec_data.csv';

        // Generate CSV file
        $file = fopen('php://temp', 'w');
        if (!empty($csvData)) {
            fputcsv($file, array_keys($csvData[0])); // Add header row
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
        }

        if ($techspecs && $techspecs->sub_type == "Device") {
            $tech_spec_ip_dns = DB::table('tech_spec_ip_dns')->where('tech_spec_id', $request->id)->get();
            $logical_volume = DB::table('tech_spec_logical_volume')->where('tech_spec_id', $request->id)->get();
            $network_adapter = DB::table('tech_spec_network_adapter')->where('tech_spec_id', $request->id)->get();
            $port_map = DB::table('tech_spec_port_map')->where('tech_spec_id', $request->id)->get();
            $raid_volume = DB::table('tech_spec_raid_volume')->where('tech_spec_id', $request->id)->get();
            $virtual_disks = DB::table('tech_spec_virtual_disks')->where('tech_spec_id', $request->id)->get();

            if (count($tech_spec_ip_dns) > 0) {
                fputcsv($file, []);
                fputcsv($file, ['IP & DNS']);
                fputcsv($file, ['DNS Type', 'vLAN ID', 'Alias', 'Hostname', 'IP Address', 'Description', 'Primary DNS', 'Secondary DNS', 'Primary NTP', 'Secondary NTP']);
                foreach ($tech_spec_ip_dns as $ip) {
                    fputcsv($file, [
                        $ip->dns_type,
                        $ip->vlan_id_no,
                        $ip->alias,
                        $ip->host_name,
                        $ip->ip_address,
                        $ip->description,
                        $ip->primary_dns,
                        $ip->secondary_dns,
                        $ip->primary_ntp,
                        $ip->secondary_ntp,
                    ]);
                }
            }

            if (count($network_adapter) > 0) {
                fputcsv($file, []);
                fputcsv($file, ['Network Adapter']);
                fputcsv($file, ['Connection Type', 'Adapter Name', 'Adapter Type', 'Slot', 'Port', 'Port Media', 'MAC Address', 'VMNIC']);
                foreach ($network_adapter as $n) {
                    fputcsv($file, [
                        @$n->connection_type,
                        @$n->adapter_name,
                        @$n->adapter_type,
                        @$n->slot,
                        @$n->port,
                        @$n->port_media,
                        @$n->mac_address,
                        @$n->vmic
                    ]);
                }
            }
            if (count($port_map) > 0) {
                fputcsv($file, []);
                fputcsv($file, ['Network Adapter']);
                fputcsv($file, ['Mapping Type', 'SSID', 'Network Adapter', 'Media Type', 'Switch', 'Port', 'Port Mode', 'vLAN ID(s)', 'Comments']);
                foreach ($port_map as $p) {
                    fputcsv($file, [
                        @$p->mapping_type,
                        @$p->ssid,
                        @$p->network_adapter,
                        @$p->media_type,
                        @$p->switch,
                        @$p->port,
                        @$p->port_mode,
                        @$p->vlan_ids,
                        @$p->comments,
                    ]);
                }
            }
            if (count($raid_volume) > 0) {
                fputcsv($file, []);
                fputcsv($file, ['Raid Volume']);
                fputcsv($file, ['Volume Name', 'Contoller', 'Drive Type', 'RAID Level', '# of Sets', '# of Drives (per set)', 'Drive Size', 'Volume Size']);
                foreach ($raid_volume as $r) {
                    fputcsv($file, [
                        @$r->name,
                        @$r->controller,
                        @$r->drive_type,
                        @$r->raid_level,
                        @$r->no_of_sets,
                        @$r->no_of_drives,
                        @$r->drive_size . ' ' . $r->drive_size_unit,
                        @$r->volume_size
                    ]);
                }
            }
            if (count($virtual_disks) > 0) {
                fputcsv($file, []);
                fputcsv($file, ['Virtual Disks']);
                fputcsv($file, ['vDisk #', 'Datastore', 'SCSI', 'Drive Type', 'Drive Size']);
                foreach ($virtual_disks as $v) {
                    fputcsv($file, [
                        @$v->vdisk_no,
                        @$v->datastore,
                        @$v->scsi_id_a . ' : ' . $v->scsi_id_b,
                        @$v->device_type,
                        @$v->drive_size . ' ' . $v->drive_size_unit,
                    ]);
                }
            }
            if (count($logical_volume) > 0) {
                fputcsv($file, []);
                fputcsv($file, ['Logical Volume']);
                fputcsv($file, ['Source Disk', 'Volume', 'volume name', 'Size', 'Format', 'Block Size']);
                foreach ($logical_volume as $lv) {
                    fputcsv($file, [
                        @$lv->source_disk,
                        @$lv->volume,
                        @$lv->volume_name,
                        @$lv->size . ' ' . @$lv->size_unit,
                        @$lv->format,
                        @$lv->block_size
                    ]);
                }
            }
        }

        rewind($file);
        $csv = stream_get_contents($file);
        fclose($file);

        // Download CSV file
        return Response::streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename);
    }

    public function getDevice(Request $request)
    {
        $data = DB::table('coorprate_device')->where('tech_spec_id', $request->id)->where('page', 'techspecs')->get();
        return response()->json($data);
    }
    public function getIpDns(Request $request)
    {
        $data = DB::table('tech_spec_ip_dns')->where('tech_spec_id', $request->id)->get();
        return response()->json($data);
    }
    public function getNetworkAdapter(Request $request)
    {
        $data = DB::table('tech_spec_network_adapter')->where('tech_spec_id', $request->id)->get();
        return response()->json($data);
    }
    public function getPortMap(Request $request)
    {
        $data = DB::table('tech_spec_port_map')->where('tech_spec_id', $request->id)->get();
        return response()->json($data);
    }
    public function getLogicalVolume(Request $request)
    {
        $data = DB::table('tech_spec_logical_volume')->where('tech_spec_id', $request->id)->get();
        return response()->json($data);
    }
    public function getRaidVolume(Request $request)
    {
        $data = DB::table('tech_spec_raid_volume')->where('tech_spec_id', $request->id)->get();
        return response()->json($data);
    }
    public function getVirtualDisks(Request $request)
    {
        $data = DB::table('tech_spec_virtual_disks')->where('tech_spec_id', $request->id)->get();
        return response()->json($data);
    }
    public function getEmail(Request $request)
    {
        $data = DB::table('tech_spec_email')->where('tech_spec_id', $request->id)->get();
        return response()->json($data);
    }

    public function getComments(Request $request)
    {
        $qry = DB::table('tech_spec_comments as c')
            ->select('c.*', 'u.user_image')
            ->join('users as u', function ($j) {
                $j->on('u.id', '=', 'c.added_by');
            })
            ->where('c.tech_spec_id', $request->id)->get();
        return response()->json($qry);
    }

    public function getAttachment(Request $request)
    {
        $qry = DB::table('tech_spec_attachments as c')
            ->select('c.*', 'u.user_image')
            ->join('users as u', function ($j) {
                $j->on('u.id', '=', 'c.added_by');
            })
            ->where('c.tech_spec_id', $request->id)->get();
        return response()->json($qry);
    }

    public function deleteUser(Request $request)
    {
        DB::Table('tech_spec_users')->where('id', $request->id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', 'User Deleted Successfully  <a href="javascript:;" class="  btn-notify btnUndo ml-4" data-id=' . $request->id . '>Undo</a>');
    }

    public function deleteUserUndo(Request $request)
    {
        $id = $request->id;

        DB::table('tech_spec_users')->where('id', $id)->update(['is_deleted' => 0, 'deleted_at' => null]);
        return redirect()->back()->with('success', 'User Restored Successfully');
    }

    public function EndUser(Request $request)
    {
        if ($request->end == 1) {
            DB::Table('tech_spec_users')->where('id', $request->id)->update(['status' => '1', 'Inactive_by' => Auth::user()->id]);
            DB::table('techspec_user_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'techspec_user_id' => $request->id, 'comment' => 'User Reactivated.<br>' . $request->reason]);
            return redirect()->back()->with('success', 'User Reactivated');
        } else {
            DB::Table('tech_spec_users')->where('id', $request->id)->update(['status' => '0', 'Inactive_by' => null]);
            DB::table('techspec_user_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'techspec_user_id' => $request->id, 'comment' => 'User successfully Deactivated.<br>' . $request->reason]);
            return redirect()->back()->with('success', 'User Deactivated Successfully');
        }
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
        return response()->json('success');
    }
    public function remove_option(Request $request)
    {
        DB::table($request->table)->where('id', $request->id)->update([
            'is_deleted' => 1
        ]);
        return response()->json('success');
    }

    public function InsertCommentUser(Request $request)
    {
        DB::table('techspec_user_comments')->insert([
            'techspec_user_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        return redirect()->back()->with('success', 'Comment Added Successfully');
    }

    public function InsertAttachmentUser(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {


                copy(public_path('temp_uploads/' . $a), public_path('network_attachment/' . $a));
                DB::table('techspec_user_attachments')->insert([
                    'techspec_user_id' => $request->id,
                    'date' => date('Y-m-d H:i:s'),
                    'attachment' => $a,
                    'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Attachment Added Successfully');
    }
}
