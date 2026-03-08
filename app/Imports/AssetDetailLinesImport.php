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
use Illuminate\Support\Facades\DB;

class AssetDetailLinesImport implements ToCollection, WithStartRow
{
    public $data;
    private $detail_line;
    private $pageType;
    private $clientHostErrors = 0;
    private $detail_line_name = "";
    private $vlanErrors = 0;
    private $successfulImports = 0;

    public function __construct($detail_line, $pageType)
    {
        $this->detail_line = $detail_line;
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


        $array = array();



        foreach ($rows as $r) {

            $clients = DB::table('clients')->where('client_display_name', trim($r[0]))->where('is_deleted', 0)->first();
            if (!$clients) {
                $this->clientHostErrors++;
                continue;
            }
            $assets = DB::table('assets')->where('client_id', @$clients->id)->where('hostname', trim($r[1]))
                ->where(function ($q) {
                    if ($this->detail_line == 'logical_volumes' && $this->pageType == "virtual") {
                        $q->where('asset_type', 'virtual');
                    }
                    if ($this->detail_line == 'logical_volumes' && $this->pageType == "physical") {
                        $q->where('asset_type', 'physical');
                    }
                })
                ->where('is_deleted', 0)->get();
            if (!$assets) {
                $this->clientHostErrors++;
                continue;
            }

            if ($this->detail_line == 'ip_dns') {
                $this->detail_line_name = "IP & DNS";
                foreach ($assets as $key => $a) {
                    if (isset($r[2])) {
                        if ($r[2] == "A") {
                            if (isset($r[3])) {
                                $vlan = DB::table('network')->where('vlan_id', trim($r[3]))->where('client_id', $a->client_id)->where('is_deleted', 0)->first();
                            }
                            if (isset($vlan)) {
                                $network_zone = DB::table('network_zone')->where('network_zone_description', $vlan->zone)->where('is_deleted', 0)->first();
                                DB::table('asset_ip_dns')->insert([
                                    'asset_id' => $a->id,
                                    'dns_type' => isset($r[2]) ? trim($r[2]) : null,
                                    'vlan_id_no' => $vlan->id ?? null,
                                    'vlan_id' => isset($r[3]) ? trim($r[3]) : null,
                                    'zone' => $vlan->zone,
                                    'subnet_ip' => $vlan->subnet_ip,
                                    'mask' => $vlan->mask,
                                    'gateway' => "on",
                                    'color' => @$network_zone->tag_text_color,
                                    'background' => @$network_zone->tag_back_color,
                                    'alias' => isset($r[4]) ? trim($r[4]) : null,
                                    'host_name' => isset($r[5]) ? trim($r[5]) : null,
                                    'ip_address' => isset($r[6]) ? trim($r[6]) : null,
                                    'gateway_ip' => isset($r[7]) ? trim($r[7]) : null,
                                    'description' => isset($r[8]) ? trim($r[8]) : null,
                                    'primary_dns' => isset($r[9]) ? trim($r[9]) : null,
                                    'secondary_dns' => isset($r[10]) ? trim($r[10]) : null,
                                    'primary_ntp' => isset($r[11]) ? trim($r[11]) : null,
                                    'secondary_ntp' => isset($r[12]) ? trim($r[12]) : null
                                ]);
                                $this->successfulImports++;
                            } else {
                                DB::table('asset_ip_dns')->insert([
                                    'asset_id' => $a->id,
                                    'dns_type' => isset($r[2]) ? trim($r[2]) : null,
                                    'vlan_id_no' => $vlan->id ?? null,
                                    'vlan_id' => isset($r[3]) ? trim($r[3]) : "NONE(0)",
                                    'zone' => null,
                                    'subnet_ip' => null,
                                    'mask' => null,
                                    'gateway' => "on",
                                    'color' => null,
                                    'background' => null,
                                    'alias' => isset($r[4]) ? trim($r[4]) : null,
                                    'host_name' => isset($r[5]) ? trim($r[5]) : null,
                                    'ip_address' => isset($r[6]) ? trim($r[6]) : null,
                                    'gateway_ip' => isset($r[7]) ? trim($r[7]) : null,
                                    'description' => isset($r[8]) ? trim($r[8]) : null,
                                    'primary_dns' => isset($r[9]) ? trim($r[9]) : null,
                                    'secondary_dns' => isset($r[10]) ? trim($r[10]) : null,
                                    'primary_ntp' => isset($r[11]) ? trim($r[11]) : null,
                                    'secondary_ntp' => isset($r[12]) ? trim($r[12]) : null
                                ]);
                                $this->successfulImports++;
                            }
                        } else {
                            DB::table('asset_ip_dns')->insert([
                                'asset_id' => $a->id,
                                'dns_type' => isset($r[2]) ? trim($r[2]) : null,
                                'alias' => isset($r[4]) ? trim($r[4]) : null,
                                'host_name' => isset($r[1]) ? trim($r[1]) : null,
                                'description' => isset($r[8]) ? trim($r[8]) : null
                            ]);
                        }
                    }
                }
            }
            if ($this->detail_line == 'network_adapters') {
                $this->detail_line_name = "Network Adapter";
                if ($this->pageType == "physical") {
                    foreach ($assets as $key => $a) {
                        if (trim($r[3]) != "Wireless") {
                            $adapterType = null;
                            $slot = null;
                            $port = null;
                            if (isset($r[3])) {
                                $adapterTypeParts = explode(' ', trim($r[3]));
                                $adapterType = $adapterTypeParts[0];

                                if (isset($adapterTypeParts[1])) {
                                    // Check if it contains a slot/port format
                                    if (strpos($adapterTypeParts[1], ':') !== false) {
                                        $parts = explode(':', $adapterTypeParts[1]);
                                        $slot = $parts[0] ?? null;
                                        $port = $parts[1] ?? null;
                                    } else {
                                        $port = $adapterTypeParts[1];
                                    }
                                }
                            }
                            DB::table('asset_network_adapter')->insert([
                                'asset_id' => $a->id,
                                'connection_type' => "Wired",
                                'adapter_name' => isset($r[2]) ? trim($r[2]) : null,
                                'adapter_name_' => isset($r[2]) ? trim($r[2]) : null,
                                'adapter_type' => $adapterType,
                                'slot' => $slot,
                                'port' => $port,
                                'mac_address' => isset($r[4]) ? trim($r[4]) : null,
                                'port_media' => isset($r[5]) ? trim($r[5]) : null,
                                'port_media_' => isset($r[5]) ? trim($r[5]) : null
                            ]);
                            $this->successfulImports++;
                        } else {
                            DB::table('asset_network_adapter')->insert([
                                'asset_id' => $a->id,
                                'connection_type' => "Wifi",
                                'adapter_name' => isset($r[2]) ? trim($r[2]) : null,
                                'adapter_name_' => isset($r[2]) ? trim($r[2]) : null,
                                'adapter_type' => isset($r[3]) ? trim($r[3]) : null,
                                'mac_address' => isset($r[4]) ? trim($r[4]) : null
                            ]);
                            $this->successfulImports++;
                        }
                    }
                } else {
                    // dd($rows, $r);
                    foreach ($assets as $key => $a) {
                        DB::table('asset_network_adapter')->insert([
                            'asset_id' => $a->id,
                            'connection_type' => 'Virtual',
                            'vmic' => isset($r[2]) ? trim($r[2]) : null,
                            'port_group' => isset($r[3]) ? trim($r[3]) : null,
                            'port_group_' => isset($r[3]) ? trim($r[3]) : null,
                            'adapter_type' => isset($r[4]) ? trim($r[4]) : null,
                            'mac_address' => isset($r[5]) ? trim($r[5]) : null
                        ]);
                        $this->successfulImports++;
                    }
                }
            }
            if ($this->detail_line == 'virtual_disks') {
                $this->detail_line_name = "Virtual Disk";
                foreach ($assets as $key => $a) {
                    $scsi_id_a = null;
                    $scsi_id_b = null;
                    if (isset($r[4])) {
                        $parts = explode(':', $r[4]);
                        $scsi_id_a = $parts[0];
                        $scsi_id_b = $parts[1];
                    }
                    DB::table('asset_virtual_disks')->insert([
                        'asset_id' => $a->id,
                        'vdisk_no' => isset($r[2]) ? trim($r[2]) : null,
                        'datastore' => isset($r[3]) ? trim($r[3]) : null,
                        'datastore_' => isset($r[3]) ? trim($r[3]) : null,
                        'scsi_id_a' => $scsi_id_a,
                        'scsi_id_b' => $scsi_id_b,
                        'device_type' => isset($r[5]) ? trim($r[5]) : null,
                        'drive_size' => isset($r[6]) ? trim($r[6]) : null,
                        'drive_size_unit' => isset($r[7]) ? trim($r[7]) : null
                    ]);
                    $this->successfulImports++;
                }
            }
            if ($this->detail_line == 'raid_volumes') {
                $this->detail_line_name = "RAID Volume";
                if ($this->pageType == "physical") {
                    foreach ($assets as $key => $a) {
                        $unit = isset($r[10]) ? trim($r[10]) : null;
                        $raid = isset($r[6]) ? trim($r[6]) : null;
                        $sets = isset($r[7]) ? trim($r[7]) : null;
                        $no_of_drives = isset($r[8]) ? trim($r[8]) : null;
                        $driveSize = isset($r[9]) ? trim($r[9]) : null;
                        $formattedResult = null;
                        if ($unit && $raid !== null && $raid >= 0 && $sets && $no_of_drives && $driveSize) {
                            $result = 0;
                            $divisor = ($unit === 'GB') ? 1.047 : 1.1;
                            $multiplier = 0;

                            if ($raid == 0) {
                                $multiplier = $no_of_drives;
                            } elseif (in_array($raid, [1, 10])) {
                                $multiplier = $no_of_drives / 2;
                            } elseif (in_array($raid, [5, 50])) {
                                $multiplier = $no_of_drives - 1;
                            } elseif (in_array($raid, [6, 60])) {
                                $multiplier = $no_of_drives - 2;
                            }

                            $result = ($sets * $multiplier * $driveSize) / $divisor;

                            // Rounding the result based on the unit
                            if ($unit === 'GB') {
                                $result = round($result); // Round to the nearest whole number
                                $formattedResult = number_format($result) . ' GiB';
                            } elseif ($unit === 'TB') {
                                $result = round($result, 1); // Round to the nearest 10th
                                $formattedResult = number_format($result, 1) . ' TiB';
                            }
                        }
                        DB::table('asset_raid_volume')->insert([
                            'asset_id' => $a->id,
                            'name' => isset($r[2]) ? trim($r[2]) : null,
                            'volume_description' => isset($r[3]) ? trim($r[3]) : null,
                            'controller' => isset($r[4]) ? trim($r[4]) : null,
                            'controller_' => isset($r[4]) ? trim($r[4]) : null,
                            'drive_type' => isset($r[5]) ? trim($r[5]) : null,
                            'drive_type_' => isset($r[5]) ? trim($r[5]) : null,
                            'raid_level' => isset($r[6]) ? trim($r[6]) : null,
                            'no_of_sets' => isset($r[7]) ? trim($r[7]) : null,
                            'no_of_drives' => isset($r[8]) ? trim($r[8]) : null,
                            'drive_size' => isset($r[9]) ? trim($r[9]) : null,
                            'drive_size_unit' => isset($r[10]) ? trim($r[10]) : null,
                            'volume_size' => $formattedResult
                        ]);
                        $this->successfulImports++;
                    }
                }
            }
            if ($this->detail_line == 'logical_volumes') {
                $this->detail_line_name = "Logical Volume";
                // dd($assets);
                foreach ($assets as $key => $a) {
                    $source_disk = $this->getNonZeroInteger($r[2]);
                    if ($this->pageType === "physical") {
                        $raidVolume = DB::table('asset_raid_volume')->where('name', $source_disk)->where('is_deleted', 0)->where('asset_id', $a->id)->first();
                    } else {
                        $virtualDisk = DB::table('asset_virtual_disks')->where('vdisk_no', $source_disk)->where('is_deleted', 0)->where('asset_id', $a->id)->first();
                    }
                    $tooltipContent = "<span class='HostActive text-yellow text-center'>";

                    if ($this->pageType === "physical") {
                        $tooltipContent .= @$raidVolume->drive_size . " " .
                            @$raidVolume->drive_size_unit . " " .
                            @$raidVolume->drive_type . " DRIVES";
                    } else {
                        $tooltipContent .= @$virtualDisk->datastore;
                    }

                    $tooltipContent .= "</span><br><p class='pb-0 mb-0 HostActive text-white text-center'>";

                    if ($this->pageType === "physical") {
                        $tooltipContent .= "RAID" . @$raidVolume->raid_level . " (" .
                            @$raidVolume->no_of_sets . "x" .
                            @$raidVolume->no_of_drives . ")";
                    } else {
                        $tooltipContent .= @$virtualDisk->device_type . " (SCSI " .
                            @$virtualDisk->scsi_id_a . ":" .
                            @$virtualDisk->scsi_id_b . ")";
                    }

                    $tooltipContent .= "</p>";
                    if ($a->platform == "Windows") {
                        DB::table('asset_logical_volume')->insert([
                            'asset_id' => $a->id,
                            'source_disk' => isset($r[2]) ? trim($r[2]) : null,
                            'volume' => isset($r[3]) ? trim($r[3]) : null,
                            // 'volume1' => $source_disk ?? null,
                            'volume_' => isset($r[3]) ? trim($r[3]) : null,
                            'volume_name' => isset($r[4]) ? trim($r[4]) : null,
                            'size' => isset($r[5]) ? trim($r[5]) : null,
                            'size_unit' => isset($r[6]) ? trim($r[6]) : null,
                            'format' => isset($r[7]) ? trim($r[7]) : null,
                            'tooltip' => @$tooltipContent,
                            'block_size' => isset($r[8]) ? trim($r[8]) : null
                        ]);
                        $this->successfulImports++;
                    } else {
                        DB::table('asset_logical_volume')->insert([
                            'asset_id' => $a->id,
                            'source_disk' => isset($r[2]) ? trim($r[2]) : null,
                            // 'volume' => isset($r[3]) ? trim($r[3]) : null,
                            'volume1' => $source_disk ?? null,
                            // 'volume_' => isset($r[3]) ? trim($r[3]) : null,
                            'volume_name' => isset($r[4]) ? trim($r[4]) : null,
                            'size' => isset($r[5]) ? trim($r[5]) : null,
                            'size_unit' => isset($r[6]) ? trim($r[6]) : null,
                            'format' => isset($r[7]) ? trim($r[7]) : null,
                            'tooltip' => @$tooltipContent,
                            'block_size' => isset($r[8]) ? trim($r[8]) : null
                        ]);
                        $this->successfulImports++;
                    }
                }
            }
            if ($this->detail_line == 'port_mapping') {
                $this->detail_line_name = "Port Mapping";
                $host =  isset($r[1]) ? trim($r[1]) : null;
                $network_adapter =  isset($r[2]) ? trim($r[2]) : null;
                $comment =  isset($r[8]) ? trim($r[8]) : null;
                $sub_text = $host . " : port= " . $network_adapter . " for " . $comment;

                $vlan_ids = isset($r[7]) ? trim($r[7]) : null;
                if ($vlan_ids && strpos($vlan_ids, '-') !== false) {
                    $vlan_ids = preg_replace('/\s*-\s*/', ' - ', $vlan_ids);
                }
                foreach ($assets as $key => $a) {
                    DB::table('asset_port_map')->insert([
                        'asset_id' => $a->id,
                        'mapping_type' => "Wired",
                        'network_adapter' => isset($r[2]) ? trim($r[2]) : null,
                        'media_type' => isset($r[3]) ? trim($r[3]) : null,
                        'media_type_' => isset($r[3]) ? trim($r[3]) : null,
                        'switch' => isset($r[4]) ? trim($r[4]) : null,
                        'port' => isset($r[5]) ? trim($r[5]) : null,
                        'port_mode' => isset($r[6]) ? trim($r[6]) : null,
                        'vlan_ids' => $vlan_ids ?? null,
                        'comments' => isset($r[8]) ? trim($r[8]) : null,
                        'sub_text' => $sub_text ?? null
                    ]);
                    $this->successfulImports++;
                }
            }
        }
        $notifications = [];

        if ($this->clientHostErrors > 0) {
            $notifications[] = "{$this->clientHostErrors} {$this->detail_line_name} records could not be imported, client and host were not found.";
        }

        if ($this->vlanErrors > 0) {
            $notifications[] = "{$this->vlanErrors} {$this->detail_line_name} records could not be imported, VLANID for Client does not exist.";
        }

        if ($this->successfulImports > 0) {
            $notifications[] = "{$this->successfulImports} {$this->detail_line_name} records imported successfully.";
        }

        $this->data = implode("\n", $notifications);

        // $this->data = $array;
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

    function getNonZeroInteger($string)
    {
        preg_match('/[1-9]\d*/', $string, $matches);
        return isset($matches[0]) ? (int) $matches[0] : null;
    }
}
