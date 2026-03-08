{{-- {{ dd($asset_type) }} --}}
@if ($type == 'ip_dns')
    <table>
        <thead class="thead thead-dark">
            <tr>
                <th style="min-width:70px"> # </th>
                <th style="min-width:70px">DNSType</th>
                <th style="min-width:70px">VLANID</th>
                <th style="min-width:70px">Hostname</th>
                <th style="min-width:70px">IP Address</th>
                <th style="min-width:70px">Alias</th>
                <th style="min-width:70px">Subnet Mask</th>
                <th style="min-width:70px">Gateway</th>
                <th style="min-width:70px">Description</th>
                <th style="min-width:70px">Primary DNS</th>
                <th style="min-width:70px">Secondary DNS</th>
                <th style="min-width:70px">Primary NTP</th>
                <th style="min-width:70px">Secondary NTP</th>
                <th style="min-width:70px">STATUS</th>
                <th style="min-width:70px">ASSET TYPE</th>
                <th style="min-width:70px">PLATFORM</th>
                <th style="min-width:70px">CLIENT</th>
                <th style="min-width:70px">SITE</th>
                <th style="min-width:70px">HOSTNAME</th>
            </tr>
        </thead>
        <tbody id="showdata">
            <?php
            function isValidDate($date)
            {
                return date('Y-m-d', strtotime($date)) === $date;
            }
            
            ?>

            @php  $sno=0;@endphp
            @foreach ($qry as $q)
                <?php $data = DB::table('asset_ip_dns')->where('asset_id', $q->id)->where('is_deleted', 0)->get(); ?>

                @foreach ($data as $row)
                    <tr>
                        <td>{{ ++$sno }}</td>
                        <td>{{ @$row->dns_type }}</td>
                        <td>{{ @$row->vlan_id }}</td>
                        <td>{{ @$row->host_name }}</td>
                        <td>{{ @$row->ip_address }}</td>
                        <td>{{ @$row->alias }}</td>
                        <td>{{ @$row->mask }}</td>
                        {{-- <td>{{ @$row->subnet_ip }}</td> --}}
                        <td>{{ @$row->gateway_ip }}</td>
                        <td>{{ @$row->description }}</td>
                        <td>{{ @$row->primary_dns }}</td>
                        <td>{{ @$row->secondary_dns }}</td>
                        <td>{{ @$row->primary_ntp }}</td>
                        <td>{{ @$row->secondary_ntp }}</td>
                        <td>
                            @if ($q->AssetStatus == 1)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ @$q->asset_type }}</td>
                        <td>{{ @$q->platform }}</td>
                        <td>{{ @$q->client_display_name }}</td>
                        <td>{{ @$q->site_name }}</td>
                        <td>{{ @$q->hostname }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endif
@if ($type == 'network_adapters')
    @if ($asset_type == 'virtual')
        <table>
            <thead class="thead thead-dark">
                <tr>
                    <th style="min-width:70px"> # </th>
                    {{-- <th style="min-width:70px">VMNIC#</th> --}}
                    {{-- <th style="min-width:70px">PortGroup</th> --}}
                    <th style="min-width:70px">VMINC#</th>
                    <th style="min-width:70px">Port Group</th>
                    <th style="min-width:70px">Adapter Type</th>
                    <th style="min-width:70px">Mac Address</th>
                    {{-- <th style="min-width:70px">Port Media</th> --}}
                    <th style="min-width:70px">STATUS</th>
                    <th style="min-width:70px">ASSET TYPE</th>
                    <th style="min-width:70px">PLATFORM</th>
                    <th style="min-width:70px">CLIENT</th>
                    <th style="min-width:70px">SITE</th>
                    <th style="min-width:70px">HOSTNAME</th>
                </tr>
            </thead>
            <tbody id="showdata">
                <?php
                
                function isValidDate($date)
                {
                    return date('Y-m-d', strtotime($date)) === $date;
                }
                
                ?>

                @php  $sno=0; @endphp
                @foreach ($qry as $q)
                    <?php $data = DB::table('asset_network_adapter')->where('asset_id', $q->id)->where('is_deleted', 0)->get();
                    ?>
                    @foreach ($data as $row)
                        @php
                            $tag_details = '';
                            if (@$row->adapter_type == 'MGMT') {
                                $tag_details = @$row->adapter_type;
                            } elseif (
                                @$row->adapter_type == 'EMB' ||
                                @$row->adapter_type == 'MEZ' ||
                                @$row->adapter_type == 'SwPort'
                            ) {
                                $tag_details = @$row->adapter_type . ' ' . @$row->port;
                            } else {
                                $tag_details = @$row->adapter_type . ' ' . @$row->slot . ':' . @$row->port;
                            }
                        @endphp
                        <tr>
                            <td>{{ ++$sno }}</td>
                            {{-- <td>{{ @$row->vmic }}</td> --}}
                            {{-- <td>{{ @$row->port_group }}</td> --}}
                            <td>vmnic{{ @$row->vmic }}</td>
                            <td>{{ @$row->port_group }}</td>
                            {{-- <td>{{ @$row->adapter_type }}</td> --}}
                            <td>{{ @$row->adapter_type }}</td>
                            <td>{{ @$row->mac_address }}</td>
                            {{-- <td>{{ @$row->port_media }}</td> --}}
                            <td>
                                @if ($q->AssetStatus == 1)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ @$q->asset_type }}</td>
                            <td>{{ @$q->platform }}</td>
                            <td>{{ @$q->client_display_name }}</td>
                            <td>{{ @$q->site_name }}</td>
                            <td>{{ @$q->hostname }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @else
        <table>
            <thead class="thead thead-dark">
                <tr>
                    <th style="min-width:70px"> # </th>
                    {{-- <th style="min-width:70px">VMNIC#</th> --}}
                    {{-- <th style="min-width:70px">PortGroup</th> --}}
                    <th style="min-width:70px">Adapter Name</th>
                    <th style="min-width:70px">Adapter Type</th>
                    <th style="min-width:70px">Mac Address</th>
                    <th style="min-width:70px">Port Media</th>
                    <th style="min-width:70px">STATUS</th>
                    <th style="min-width:70px">ASSET TYPE</th>
                    <th style="min-width:70px">PLATFORM</th>
                    <th style="min-width:70px">CLIENT</th>
                    <th style="min-width:70px">SITE</th>
                    <th style="min-width:70px">HOSTNAME</th>
                </tr>
            </thead>
            <tbody id="showdata">
                <?php
                function isValidDate($date)
                {
                    return date('Y-m-d', strtotime($date)) === $date;
                }
                
                ?>

                @php  $sno=0;@endphp
                @foreach ($qry as $q)
                    <?php $data = DB::table('asset_network_adapter')->where('asset_id', $q->id)->where('is_deleted', 0)->get();
                    ?>
                    @foreach ($data as $row)
                        @php
                            $tag_details = '';
                            if (@$row->adapter_type == 'MGMT') {
                                $tag_details = @$row->adapter_type;
                            } elseif (
                                @$row->adapter_type == 'EMB' ||
                                @$row->adapter_type == 'MEZ' ||
                                @$row->adapter_type == 'SwPort'
                            ) {
                                $tag_details = @$row->adapter_type . ' ' . @$row->port;
                            } else {
                                $tag_details = @$row->adapter_type . ' ' . @$row->slot . ':' . @$row->port;
                            }
                        @endphp
                        <tr>
                            <td>{{ ++$sno }}</td>
                            {{-- <td>{{ @$row->vmic }}</td> --}}
                            {{-- <td>{{ @$row->port_group }}</td> --}}
                            <td>{{ @$row->adapter_name }}</td>
                            {{-- <td>{{ @$row->adapter_type }}</td> --}}
                            <td>{{ @$tag_details }}</td>
                            <td>{{ @$row->mac_address }}</td>
                            <td>{{ @$row->port_media }}</td>
                            <td>
                                @if ($q->AssetStatus == 1)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ @$q->asset_type }}</td>
                            <td>{{ @$q->platform }}</td>
                            <td>{{ @$q->client_display_name }}</td>
                            <td>{{ @$q->site_name }}</td>
                            <td>{{ @$q->hostname }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif
@endif
@if ($type == 'power_connections')
    <table>
        <thead class="thead thead-dark">
            <tr>
                <th style="min-width:70px"> # </th>
                <th style="min-width:70px">Host PSU#</th>
                <th style="min-width:70px">Host Type</th>
                <th style="min-width:70px">PDU#</th>
                <th style="min-width:70px">PDU Type</th>
                <th style="min-width:70px">Cable Length (ft)</th>
                <th style="min-width:70px">STATUS</th>
                <th style="min-width:70px">ASSET TYPE</th>
                <th style="min-width:70px">PLATFORM</th>
                <th style="min-width:70px">CLIENT</th>
                <th style="min-width:70px">SITE</th>
                <th style="min-width:70px">HOSTNAME</th>
            </tr>
        </thead>
        <tbody id="showdata">
            <?php
            function isValidDate($date)
            {
                return date('Y-m-d', strtotime($date)) === $date;
            }
            
            ?>

            @php  $sno=0;@endphp
            @foreach ($qry as $q)
                <?php $data = DB::table('assets_power_connection')->where('asset_id', $q->id)->where('is_deleted', 0)->get(); ?>

                @foreach ($data as $row)
                    <tr>
                        <td>{{ ++$sno }}</td>
                        <td>{{ @$row->host_psu_no }}</td>
                        <td>{{ @$row->host_psu }}</td>
                        <td>{{ @$row->host_pdu_no }}</td>
                        <td>{{ @$row->host_pdu }}</td>
                        <td>{{ @$row->cable_length }}</td>
                        <td>
                            @if ($q->AssetStatus == 1)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ @$q->asset_type }}</td>
                        <td>{{ @$q->platform }}</td>
                        <td>{{ @$q->client_display_name }}</td>
                        <td>{{ @$q->site_name }}</td>
                        <td>{{ @$q->hostname }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endif
@if ($type == 'port_mapping')
    <table>
        <thead class="thead thead-dark">
            <tr>
                <th style="min-width:70px"> # </th>
                <th style="min-width:70px">Network Adapter</th>
                <th style="min-width:70px">SSID</th>
                <th style="min-width:70px">Media Type</th>
                <th style="min-width:70px">Switch</th>
                <th style="min-width:70px">Port</th>
                <th style="min-width:70px">Port Mode</th>
                <th style="min-width:70px">VLAN IDs</th>
                <th style="min-width:70px">Comments</th>
                <th style="min-width:70px">STATUS</th>
                <th style="min-width:70px">ASSET TYPE</th>
                <th style="min-width:70px">PLATFORM</th>
                <th style="min-width:70px">CLIENT</th>
                <th style="min-width:70px">SITE</th>
                <th style="min-width:70px">HOSTNAME</th>
            </tr>
        </thead>
        <tbody id="showdata">
            <?php
            function isValidDate($date)
            {
                return date('Y-m-d', strtotime($date)) === $date;
            }
            
            ?>

            @php  $sno=0;@endphp
            @foreach ($qry as $q)
                <?php $data = DB::table('asset_port_map')->where('asset_id', $q->id)->where('is_deleted', 0)->get(); ?>

                @foreach ($data as $row)
                    <tr>
                        <td>{{ ++$sno }}</td>
                        <td>{{ @$row->network_adapter }}</td>
                        <td>{{ @$row->ssid }}</td>
                        <td>{{ @$row->media_type }}</td>
                        <td>{{ @$row->switch }}</td>
                        <td>{{ @$row->port }}</td>
                        <td>{{ @$row->port_mode }}</td>
                        <td>{{ @$row->vlan_ids }}</td>
                        <td>{{ @$row->comments }}</td>
                        <td>
                            @if ($q->AssetStatus == 1)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ @$q->asset_type }}</td>
                        <td>{{ @$q->platform }}</td>
                        <td>{{ @$q->client_display_name }}</td>
                        <td>{{ @$q->site_name }}</td>
                        <td>{{ @$q->hostname }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endif
@if ($type == 'raid_volumes')
    <table>
        <thead class="thead thead-dark">
            <tr>
                <th style="min-width:70px"> # </th>
                <th style="min-width:70px">VolumeID</th>
                <th style="min-width:70px">Description</th>
                <th style="min-width:70px">Controller</th>
                <th style="min-width:70px">Drive Type</th>
                <th style="min-width:70px">RAID Level</th>
                <th style="min-width:70px">#ofSets</th>
                <th style="min-width:70px">#ofDrives</th>
                <th style="min-width:70px">Drive Size</th>
                <th style="min-width:70px">Dive Size Units</th>
                <th style="min-width:70px">Volume Size</th>
                <th style="min-width:70px">STATUS</th>
                <th style="min-width:70px">ASSET TYPE</th>
                <th style="min-width:70px">PLATFORM</th>
                <th style="min-width:70px">CLIENT</th>
                <th style="min-width:70px">SITE</th>
                <th style="min-width:70px">HOSTNAME</th>
            </tr>
        </thead>
        <tbody id="showdata">
            <?php
            function isValidDate($date)
            {
                return date('Y-m-d', strtotime($date)) === $date;
            }
            
            ?>

            @php  $sno=0;@endphp
            @foreach ($qry as $q)
                <?php $data = DB::table('asset_raid_volume')->where('asset_id', $q->id)->where('is_deleted', 0)->get(); ?>

                @foreach ($data as $row)
                {{-- {{ dd($row) }} --}}
                    <tr>
                        <td>{{ ++$sno }}</td>
                        <td>{{ @$row->name }}</td>
                        <td>{{ @$row->volume_description }}</td>
                        <td>{{ @$row->controller }}</td>
                        <td>{{ @$row->drive_type }}</td>
                        <td>{{ @$row->raid_level }}</td>
                        <td>{{ @$row->no_of_sets }}</td>
                        <td>{{ @$row->no_of_drives }}</td>
                        <td>{{ @$row->drive_size }}</td>
                        <td>{{ @$row->drive_size_unit }}</td>
                        <td>{{ @$row->volume_size }}</td>
                        <td>
                            @if ($q->AssetStatus == 1)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ @$q->asset_type }}</td>
                        <td>{{ @$q->platform }}</td>
                        <td>{{ @$q->client_display_name }}</td>
                        <td>{{ @$q->site_name }}</td>
                        <td>{{ @$q->hostname }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endif
@if ($type == 'logical_volumes')
    <table>
        <thead class="thead thead-dark">
            <tr>
                <th style="min-width:70px"> # </th>
                <th style="min-width:70px">Source Disk</th>
                <th style="min-width:70px">Volume</th>
                <th style="min-width:70px">Description</th>
                <th style="min-width:70px">Size</th>
                <th style="min-width:70px">Size Units</th>
                <th style="min-width:70px">Format</th>
                <th style="min-width:70px">Block Size</th>
                <th style="min-width:70px">STATUS</th>
                <th style="min-width:70px">ASSET TYPE</th>
                <th style="min-width:70px">PLATFORM</th>
                <th style="min-width:70px">CLIENT</th>
                <th style="min-width:70px">SITE</th>
                <th style="min-width:70px">HOSTNAME</th>
            </tr>
        </thead>
        <tbody id="showdata">
            <?php
            function isValidDate($date)
            {
                return date('Y-m-d', strtotime($date)) === $date;
            }
            
            ?>

            @php  $sno=0;@endphp
            @foreach ($qry as $q)
                <?php $data = DB::table('asset_logical_volume')->where('asset_id', $q->id)->where('is_deleted', 0)->get(); ?>

                @foreach ($data as $row)
                    <tr>
                        <td>{{ ++$sno }}</td>
                        <td>{{ @$row->source_disk }}</td>
                        <td>{{ @$row->volume }}</td>
                        <td>{{ @$row->volume_name }}</td>
                        <td>{{ @$row->size }}</td>
                        <td>{{ @$row->size_unit }}</td>
                        <td>{{ @$row->format }}</td>
                        <td>{{ @$row->block_size }}</td>
                        <td>
                            @if ($q->AssetStatus == 1)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ @$q->asset_type }}</td>
                        <td>{{ @$q->platform }}</td>
                        <td>{{ @$q->client_display_name }}</td>
                        <td>{{ @$q->site_name }}</td>
                        <td>{{ @$q->hostname }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endif
@if ($type == 'virtual_disks')
    <table>
        <thead class="thead thead-dark">
            <tr>
                <th style="min-width:70px"> # </th>
                <th style="min-width:70px">vDIsk#</th>
                <th style="min-width:70px">Datastore</th>
                <th style="min-width:70px">SCSI ID </th>
                <th style="min-width:70px">Drive Type</th>
                <th style="min-width:70px">Drive Size</th>
                <th style="min-width:70px">Drive Size Units</th>
                <th style="min-width:70px">STATUS</th>
                <th style="min-width:70px">ASSET TYPE</th>
                <th style="min-width:70px">PLATFORM</th>
                <th style="min-width:70px">CLIENT</th>
                <th style="min-width:70px">SITE</th>
                <th style="min-width:70px">HOSTNAME</th>
            </tr>
        </thead>
        <tbody id="showdata">
            <?php
            function isValidDate($date)
            {
                return date('Y-m-d', strtotime($date)) === $date;
            }
            
            ?>

            @php  $sno=0;@endphp
            @foreach ($qry as $q)
                <?php $data = DB::table('asset_virtual_disks')->where('asset_id', $q->id)->where('is_deleted', 0)->get(); ?>

                @foreach ($data as $row)
                    <tr>
                        <td>{{ ++$sno }}</td>
                        <td>{{ @$row->vdisk_no }}</td>
                        <td>{{ @$row->datastore }}</td>
                        <td>{{ @$row->scsi_id_a }}:{{ $row->scsi_id_b }}</td>
                        <td>{{ @$row->device_type }}</td>
                        <td>{{ @$row->drive_size }}</td>
                        <td>{{ @$row->drive_size_unit }}</td>
                        <td>
                            @if ($q->AssetStatus == 1)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ @$q->asset_type }}</td>
                        <td>{{ @$q->platform }}</td>
                        <td>{{ @$q->client_display_name }}</td>
                        <td>{{ @$q->site_name }}</td>
                        <td>{{ @$q->hostname }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endif
