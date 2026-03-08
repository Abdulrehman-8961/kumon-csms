 @if ($type == 'virtual')

     <?php
     
     $no_check = DB::Table('settings')->where('user_id', Auth::id())->first();
     if ($page_type == '') {
         $column_array = [32, 1, 2, 5, 6, 7, 8, 27, 34, 10, 12, 13, 15, 16, 30, 23, 40, 33];
     
         if (@$no_check->virtual_all_columns != '') {
             $column_array = explode(',', $no_check->virtual_all_columns);
         }
     } elseif ($page_type == 'managed') {
         $column_array = [2, 5, 6, 7, 8, 27, 34, 10, 12, 13, 15, 16, 9, 25, 14, 17, 18, 19, 20, 21, 22, 24];
         if (@$no_check->virtual_managed_columns != '') {
             $column_array = explode(',', $no_check->virtual_managed_columns);
         }
     } elseif ($page_type == 'support-contracts') {
         $column_array = [1, 2, 5, 6, 28, 35, 29];
         if (@$no_check->virtual_support_columns != '') {
             $column_array = explode(',', $no_check->virtual_support_columns);
         }
     } elseif ($page_type == 'ssl-certificate') {
         $column_array = [1, 2, 5, 6, 37, 38, 39];
         if (@$no_check->virtual_ssl_columns != '') {
             $column_array = explode(',', $no_check->virtual_ssl_columns);
         }
     } elseif ($page_type == 'inactive') {
         $column_array = [1, 2, 5, 6, 7, 10, 12, 33];
         if (@$no_check->virtual_inactive_columns != '') {
             $column_array = explode(',', $no_check->virtual_inactive_columns);
         }
     }
     ?>



     <table>
         <thead class="thead thead-dark">
             <tr>
                 <th class="text-center ">Actions</th>
                 @if (in_array(0, $column_array))
                     <th data-index=0 style="min-width:70px"> # </th>
                 @endif
                 @if (in_array(32, $column_array))
                     <th data-index=32 style="min-width: 50px"> Active </th>
                 @endif
                 <!--           <th data-index=36  style="min-width: 70px"><a   class=" 
                                                ">Type     </a></th> -->
                 @if (in_array(1, $column_array))
                     <th data-index=1 style="min-width: 100px"> Client </th>
                 @endif

                 @if (in_array(2, $column_array))
                     <th data-index=2 style="min-width: 90px">
                         <a class="  ">Site
                     </th>
                 @endif
                 @if (in_array(5, $column_array))
                     <th data-index=5 style="min-width:200px"> FQDN </th>
                 @endif
                 @if (in_array(6, $column_array))
                     <th data-index=6 style="min-width: 100px"> Role </th>
                 @endif
                 @if (in_array(7, $column_array))
                     <th data-index=7 style="min-width: 120px"> Env </th>
                 @endif
                 @if (in_array(3, $column_array))
                     <th data-index=3 style="min-width:120px"> Hostname </th>
                 @endif
                 @if (in_array(8, $column_array))
                     <th data-index=8 style="min-width: 150px"> O/S </th>
                 @endif
                 @if (in_array(27, $column_array))
                     <th data-index=27 style="min-width: 60px"> MEM (GB) </th>
                 @endif
                 @if (in_array(34, $column_array))
                     <th data-index=34 style="min-width: 70px">CPU</th>
                 @endif

                 @if (in_array(4, $column_array))
                     <th data-index=4> Domain </th>
                 @endif
                 @if (in_array(11, $column_array))
                     <th data-index=11 style="min-width: 80px">vLANID </th>
                 @endif
                 @if (in_array(10, $column_array))
                     <th data-index=10 style="min-width: 100px"> IP ADDRESS </th>
                 @endif
                 @if (in_array(12, $column_array))
                     <th data-index=12 style="min-width: 90px"> NET ZONE </th>
                 @endif
                 @if (in_array(13, $column_array))
                     <th data-index=13 style="min-width:50px"> IF </th>
                 @endif
                 @if (in_array(15, $column_array))
                     <th data-index=15 style="min-width: 50px"> LB </th>
                 @endif
                 @if (in_array(16, $column_array))
                     <th data-index=16 style="min-width: 50px"> CL </th>
                 @endif
                 @if (in_array(30, $column_array))
                     <th data-index=30 style="min-width: 85px"> SUPPORT </th>
                 @endif
                 @if (in_array(28, $column_array))
                     <th data-index=28 style="min-width: 135px">
                         @if ($page_type == 'support-contracts')
                             Status
                         @else
                             Support Status
                         @endif


                     </th>
                 @endif
                 @if (in_array(35, $column_array))
                     <th data-index=35 class="text-left"> Contract# </th>
                 @endif

                 @if (in_array(29, $column_array))
                     <th data-index=29 style="min-width: 150px">
                         @if ($page_type == 'support-contracts')
                             End Date
                         @else
                             Support End Date
                         @endif
                     </th>
                 @endif
                 @if (in_array(23, $column_array))
                     <th data-index=23 style="min-width: 50px"> SSL CERT </th>
                 @endif

                 @if (in_array(37, $column_array))
                     <th data-index=37 style="min-width: 110px">
                         @if ($page_type == 'ssl-certificate')
                             STATUS
                         @else
                             CERT STATUS
                         @endif
                     </th>
                 @endif
                 @if (in_array(38, $column_array))
                     <th data-index=38 style="min-width: 100px">
                         @if ($page_type == 'ssl-certificate')
                             CERT ISSUER
                         @else
                             ISSUER
                         @endif
                     </th>
                 @endif
                 @if (in_array(39, $column_array))
                     <th data-index=39 style="min-width: 135px">
                         @if ($page_type == 'ssl-certificate')
                             CERT EXPIRATION
                         @else
                             EXP DATE
                         @endif
                     </th>
                 @endif



                 @if ($page_type != 'managed' && $page_type != 'support-contracts' && $page_type != 'ssl-certfificate')
                     @if (in_array(40, $column_array))
                         <th data-index=40 style="min-width:60px"> Managed </th>
                     @endif
                 @endif




                 @if (in_array(9, $column_array))
                     <th data-index=9 style="min-width: 100px"> App Owner </th>
                 @endif
                 @if (in_array(25, $column_array))
                     <th data-index=25 style="min-width: 70px"> SLA </th>
                 @endif
                 @if (in_array(14, $column_array))
                     <th data-index=14 style="min-width: 45px"> DR </th>
                 @endif

                 @if ($page_type != 'managed' && $page_type != 'support-contracts' && $page_type != 'ssl-certfificate')
                     @if (in_array(17, $column_array))
                         <th data-index=17 style="min-width: 50px"> MNT </th>
                     @endif
                 @endif
                 @if (in_array(18, $column_array))
                     <th data-index=18 style="min-width: 50px">PTC</th>
                 @endif
                 @if (in_array(19, $column_array))
                     <th data-index=19 style="min-width: 50px">AV</th>
                 @endif
                 @if (in_array(20, $column_array))
                     <th data-index=20 style="min-width: 50px">BKP</th>
                 @endif
                 @if (in_array(21, $column_array))
                     <th data-index=21 style="min-width: 50px">REP</th>
                 @endif
                 @if (in_array(22, $column_array))
                     <th data-index=22 style="min-width:50px"> SMTP </th>
                 @endif
                 @if (in_array(24, $column_array))
                     <th data-index=24 style="min-width: 60px"><a
                             class=" 
                                                ">Syslog</a></th>
                 @endif


                 @if (in_array(33, $column_array))
                     <th data-index=33 style="min-width: 50px"><a class="  ">DECOM<br>DATE</a></th>
                 @endif






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
                 <?php $cert = ''; ?>


                 <tr data="{{ $q->id }}" data-pos="{{ $q->position }}">
                     <?php $ssl = DB::table('ssl_certificate as s')
                         ->leftjoin('vendors as v', 'v.id', '=', 's.cert_issuer')
                         ->where('cert_hostname', $q->id)
                         ->where('s.is_deleted', 0)
                         ->first();
                     $cert = ''; ?>
                     @if (@$ssl->cert_type == 'internal')
                         <?php $cert = 'Internal Cert'; ?>
                     @elseif(@$ssl->cert_type == 'public')
                         <?php $cert = @$ssl->vendor_name . ($ssl->vendor_name != '' ? 'Public Cert' : ''); ?>
                     @endif
                     @if (in_array(0, $column_array))
                         <td data-index=0>{{ ++$sno }}</td>
                     @endif
                     @if (in_array(32, $column_array))
                         <td data-index=32>
                             @if ($q->AssetStatus == 1)
                                 <span class="badge badge-success">Yes</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif


                         </td>
                     @endif
                     <!--       <td data-index="36" class="text-center">
                                                     </td> -->
                     @if (in_array(1, $column_array))
                         <td data-index=1 class="font-w600">
                             {{ $q->client_display_name }}
                         </td>
                     @endif
                     @if (in_array(2, $column_array))
                         <td data-index=2>{{ $q->site_name }}</td>
                     @endif
                     @if (in_array(5, $column_array))
                         <td data-index=5 class="text- ">
                             <div style="display: inline-flex;">
                                 <!--       <img class="img-avatar     "    style="object-fit: cover;width: 50px ;height: 50px" src="{{ asset('public/asset_icon/') }}/{{ $q->asset_icon }}" alt=""> -->
                                 <div class="mt-2">
                                     <a href="javascript:;" data="{{ $q->id }}" data1="{{ $cert }}"
                                         data2="{{ @$ssl->cert_edate != '' ? date('Y-M-d', strtotime($ssl->cert_edate)) : '' }}"
                                         class="btnEdit">{{ $q->fqdn }}

                                     </a>
                                     <p class="text-secondary mb-0">{{ $q->asset_type_description }}</p>
                                 </div>
                             </div>

                         </td>
                     @endif
                     @if (in_array(6, $column_array))
                         <td data-index=6>{{ $q->role }}</td>
                     @endif
                     @if (in_array(7, $column_array))
                         <td data-index=7>{{ $q->use_ }}</td>
                     @endif
                     @if (in_array(3, $column_array))
                         <td data-index=3>{{ $q->hostname }}</td>
                     @endif
                     @if (in_array(8, $column_array))
                         <td data-index=8>{{ $q->operating_system_name }}</td>
                     @endif
                     @if (in_array(27, $column_array))
                         <td data-index=27>{{ $q->memory }}</td>
                     @endif
                     @if (in_array(34, $column_array))
                         <td data-index=34>{{ $q->vcpu }}</td>
                     @endif
                     @if (in_array(4, $column_array))
                         <td data-index=4>{{ $q->domain_name }}</td>
                     @endif
                     @if (in_array(11, $column_array))
                         <td data-index=11>{{ $q->vlanId }}</td>
                     @endif
                     @if (in_array(10, $column_array))
                         <td data-index=10>{{ $q->ip_address }}</td>
                     @endif
                     @if (in_array(12, $column_array))
                         <td data-index=12>
                             @if ($q->network_zone == 'Internal')
                                 <div class="badge badge-secondary">{{ $q->network_zone }}</div>
                             @elseif($q->network_zone == 'Secure')
                                 <div class="badge badge-info">{{ $q->network_zone }}</div>
                             @elseif($q->network_zone == 'Greenzone')
                                 <div class="badge badge-success">{{ $q->network_zone }}</div>
                             @elseif($q->network_zone == 'Guest')
                                 <div class="badge badge-warning">{{ $q->network_zone }}</div>
                             @elseif($q->network_zone == 'Semi-Trusted')
                                 <div class="badge  " style="background:#FFFF11;color: black">{{ $q->network_zone }}
                                 </div>
                             @elseif($q->network_zone == 'Public DMZ' || $q->network_zone == 'Public' || $q->network_zone == 'Servers Public DMZ')
                                 <div class="badge badge-danger">{{ $q->network_zone }}</div>
                             @else
                                 {{ $q->network_zone }}
                             @endif

                         </td>
                     @endif
                     @if (in_array(13, $column_array))
                         <td data-index=13>
                             @if ($q->internet_facing == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->internet_facing == 2)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif
                     @if (in_array(15, $column_array))
                         <td data-index=15>
                             @if ($q->load_balancing == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->load_balancing == 2)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif

                     @if (in_array(16, $column_array))
                         <td data-index=16>
                             @if ($q->clustered == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->clustered == 2)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif
                     @if (in_array(30, $column_array))
                         <td data-index=30>
                             @if ($q->HasWarranty == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->HasWarranty == 2)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif
                     @if (in_array(28, $column_array))
                         <td data-index="28">

                             @if ($q->SupportStatus == 'N/A')
                                 <span class="badge text-white bg-secondary">
                                     {{ $q->SupportStatus }}
                                 </span>
                             @elseif($q->SupportStatus == 'Supported')
                                 <span class="badge badge-success">
                                     {{ $q->SupportStatus }}
                                 </span>
                             @elseif($q->SupportStatus == 'Unassigned')
                                 <span class="badge text-white bg-orange">
                                     {{ $q->SupportStatus }}
                                 </span>
                             @elseif($q->SupportStatus == 'Expired')
                                 <span class="badge badge-danger">
                                     {{ $q->SupportStatus }}
                                 </span>
                             @else
                                 <span class="badge text-white bg-secondary">
                                     N/A
                                 </span>
                             @endif

                         </td>
                     @endif

                     <?php
                     
                     $contract = DB::table('contract_assets as a')
                         ->join('contracts as c', 'c.id', '=', 'a.contract_id')
                         ->where('a.hostname', $q->id)
                         ->where('c.contract_status', '!=', 'Inactive')
                         ->where(function ($query) {
                             $query->Orwhere('a.status', '!=', 'Inactive');
                             $query->Orwhere('a.status', null);
                         })
                         ->where('a.is_deleted', 0)
                         ->first();
                     ?>
                     @if (in_array(35, $column_array))
                         <td data-index=35>{{ @$contract->contract_no }}</td>
                     @endif
                     @if (in_array(29, $column_array))
                         <td data-index="29">
                             {{ isValidDate($q->warranty_end_date) ? date('Y-M-d', strtotime($q->warranty_end_date)) : $q->warranty_end_date }}
                         </td>
                     @endif
                     @if (in_array(37, $column_array))
                         <td data-index=37>


                             @if ($q->ntp == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->ntp == 2)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif

                         </td>
                     @endif
                     @if (in_array(23, $column_array))
                         <td data-index=23>

                             @if ($q->ssl_certificate_status == 'N/A')
                                 <span class="badge text-white bg-secondary">
                                     {{ $q->ssl_certificate_status }}
                                 </span>
                             @elseif($q->ssl_certificate_status == 'Active')
                                 <span class="badge badge-success">
                                     {{ $q->ssl_certificate_status }}
                                 </span>
                             @elseif($q->ssl_certificate_status == 'Unassigned')
                                 <span class="badge text-white bg-orange">
                                     {{ $q->ssl_certificate_status }}
                                 </span>
                             @elseif($q->ssl_certificate_status == 'Expired/Ended')
                                 <span class="badge badge-danger">
                                     {{ $q->ssl_certificate_status }}
                                 </span>
                             @else
                                 <span class="badge text-white bg-secondary">
                                     N/A
                                 </span>
                             @endif


                         </td>
                     @endif
                     @if (in_array(38, $column_array))
                         <td data-index=38> {{ $cert }} </td>
                     @endif
                     @if (in_array(39, $column_array))
                         <td data-index=39>{{ @$ssl->cert_edate != '' ? date('Y-M-d', strtotime($ssl->cert_edate)) : '' }}
                         </td>
                     @endif



                     @if ($page_type != 'managed' && $page_type != 'support-contracts' && $page_type != 'ssl-certfificate')
                         @if (in_array(40, $column_array))
                             <td data-index=40>
                                 @if ($q->managed == 1)
                                     <span class="badge badge-success">Yes</span>
                                 @elseif($q->managed == 2)
                                     <span class="badge badge-secondary bg-secondary">N/A</span>
                                 @else
                                     <span class="badge badge-danger">No</span>
                                 @endif
                             </td>
                         @endif
                     @endif
                     @if (in_array(9, $column_array))
                         <td data-index=9>{{ $q->app_owner }}</td>
                     @endif
                     @if (in_array(25, $column_array))
                         <td data-index=25>{{ $q->sla }}</td>
                     @endif



                     @if (in_array(14, $column_array))
                         <td data-index=14>
                             @if ($q->disaster_recovery == 1 && $q->managed == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->disaster_recovery == 2 || $q->managed != 1)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif
                     @if (in_array(17, $column_array))
                         <td data-index=17>
                             @if ($q->monitored == 1 && $q->managed == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->syslog == 2 || $q->managed != 1)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif

                     @if (in_array(18, $column_array))
                         @if ($page_type != 'managed' && $page_type != 'support-contracts' && $page_type != 'ssl-certfificate')
                             <td data-index=18>
                                 @if ($q->patched == 1 && $q->managed == 1)
                                     <span class="badge badge-success">Yes</span>
                                 @elseif($q->patched == 2 || $q->managed != 1)
                                     <span class="badge badge-secondary bg-secondary">N/A</span>
                                 @else
                                     <span class="badge badge-danger">No</span>
                                 @endif
                             </td>
                         @endif
                     @endif
                     @if (in_array(19, $column_array))
                         <td data-index=19>
                             @if ($q->antivirus == 1 && $q->managed == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->antivirus == 2 || $q->managed != 1)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif
                     @if (in_array(20, $column_array))
                         <td data-index=20>
                             @if ($q->backup == 1 && $q->managed == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->backup == 2 || $q->managed != 1)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif
                     @if (in_array(21, $column_array))
                         <td data-index=21>
                             @if ($q->replicated == 1 && $q->managed == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->replicated == 2 || $q->managed != 1)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif
                     @if (in_array(22, $column_array))
                         <td data-index=22>
                             @if ($q->smtp == 1 && $q->managed == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->smtp == 2 || $q->managed != 1)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif
                     @if (in_array(24, $column_array))
                         <td data-index=24>
                             @if ($q->syslog == 1 && $q->managed == 1)
                                 <span class="badge badge-success">Yes</span>
                             @elseif($q->syslog == 2 || $q->managed != 1)
                                 <span class="badge badge-secondary bg-secondary">N/A</span>
                             @else
                                 <span class="badge badge-danger">No</span>
                             @endif
                         </td>
                     @endif

                     @if (in_array(33, $column_array))
                         <td data-index=33>
                             @if ($q->InactiveDate != '' && $q->AssetStatus != 1)
                                 {{ date('Y-M-d', strtotime($q->InactiveDate)) }}
                             @else
                                 <div class="badge badge-secondary">N/A</div>
                             @endif
                         </td>
                     @endif



                 </tr>
             @endforeach
         </tbody>
     </table>
 @else
     <?php $no_check = DB::Table('settings')->where('user_id', Auth::id())->first(); ?>




     <?php
     if ($page_type == '') {
         $column_array = [42, 1, 2, 3, 4, 5, 6, 9, 10, 11, 12, 14, 16, 17, 19, 20, 40, 27, 49, 43];
     
         if (@$no_check->physical_all_columns != '') {
             $column_array = explode(',', $no_check->physical_all_columns);
         }
     } elseif ($page_type == 'servers') {
         $column_array = [1, 2, 3, 4, 5, 6, 9, 10, 11, 12, 14, 16, 17, 19, 20, 40, 27, 49, 37, 30];
         if (@$no_check->physical_servers_columns != '') {
             $column_array = explode(',', $no_check->physical_servers_columns);
         }
     } elseif ($page_type == 'other') {
         $column_array = [1, 2, 3, 4, 5, 6, 9, 10, 11, 12, 14, 16, 40, 27, 49];
         if (@$no_check->physical_other_columns != '') {
             $column_array = explode(',', $no_check->physical_other_columns);
         }
     } elseif ($page_type == 'managed') {
         $column_array = [1, 2, 9, 10, 11, 12, 37, 14, 16, 17, 19, 20, 13, 29, 18, 21, 22, 23, 24, 25, 26, 28];
         if (@$no_check->physical_managed_columns != '') {
             $column_array = explode(',', $no_check->physical_managed_columns);
         }
     } elseif ($page_type == 'support-contracts') {
         $column_array = [1, 2, 9, 10, 38, 44, 39];
         if (@$no_check->physical_support_columns != '') {
             $column_array = explode(',', $no_check->physical_support_columns);
         }
     } elseif ($page_type == 'ssl-certificate') {
         $column_array = [1, 2, 9, 10, 46, 47, 48];
         if (@$no_check->physical_ssl_columns != '') {
             $column_array = explode(',', $no_check->physical_ssl_columns);
         }
     } elseif ($page_type == 'inactive') {
         $column_array = [43, 1, 2, 9, 10, 11, 12, 14, 16];
         if (@$no_check->physical_inactive_columns != '') {
             $column_array = explode(',', $no_check->physical_inactive_columns);
         }
     }
     ?>






     <table class="table   table-striped floathead table-bordered table-vcenter">
         <thead class="thead thead-dark">
             <tr>
                 @if (in_array(0, $column_array))
                     <th data-index=0 style="min-width:70px"> # </th>
                 @endif

                 @if (in_array(42, $column_array))
                     <th data-index=42 style="min-width: 50px"> Active </th>
                 @endif
                 <!--   <th data-index=45  style="min-width: 70px"><a   class=" 
                                                ">Type     </a></th> -->
                 @if (in_array(1, $column_array))
                     <th data-index=1 style="min-width: 100px"> Client </th>
                 @endif
                 @if (in_array(2, $column_array))
                     <th data-index=2 style="min-width: 90px"> Site </th>
                 @endif
                 @if (in_array(3, $column_array))
                     <th data-index=3 style="min-width: 100px"> Manu </th>
                 @endif
                 @if (in_array(4, $column_array))
                     <th data-index=4 style="min-width: 75px"> Model </th>
                 @endif
                 @if (in_array(5, $column_array))
                     <th data-index=5 style="min-width: 75px"> Type </th>
                 @endif
                 @if (in_array(6, $column_array))
                     <th data-index=6 style="min-width: 60px">Serial Number </th>
                 @endif
                 @if (in_array(9, $column_array))
                     <th data-index=9> FQDN </th>
                 @endif
                 @if (in_array(10, $column_array))
                     <th data-index=10 style="min-width: 80px"> Role </th>
                 @endif
                 @if (in_array(11, $column_array))
                     <th data-index=11 style="min-width: 60px"> Env </th>
                 @endif
                 @if (in_array(7, $column_array))
                     <th data-index=7 style="min-width:120px"> >Hostname </th>
                 @endif
                 @if (in_array(12, $column_array))
                     <th data-index=12 style="min-width: 150px"> O/S </th>
                 @endif
                 @if (in_array(37, $column_array))
                     <th data-index=37 style="min-width: 60px"> Mem (Gb) </th>
                 @endif


                 @if ($page_type == 'servers')
                     @if (in_array(30, $column_array))
                         <th data-index=30 style="min-width: 100px"> CPU </th>
                     @endif
                 @endif
                 @if (in_array(8, $column_array))
                     <th data-index=8><a class=" 
                                                ">Domain </a></th>
                 @endif
                 @if (in_array(15, $column_array))
                     <th data-index=15 style="min-width: 80px"> vLANID </th>
                 @endif
                 @if (in_array(14, $column_array))
                     <th data-index=14 style="min-width: 110px"> IP Address </th>
                 @endif
                 @if (in_array(16, $column_array))
                     @if ($page_type == 'servers' || $page_type == 'other' || $page_type == 'managed')
                         <th data-index=16 style="min-width: 90px"> NETZONE
                         @else
                         <th data-index=16 style="min-width: 70px"> ZONE
                     @endif
                     </th>
                 @endif
                 @if (in_array(17, $column_array))
                     <th data-index=17 style="min-width: 50px"> IF </th>
                 @endif
                 @if (in_array(19, $column_array))
                     <th data-index=19 style="min-width: 50px"> LB </th>
                 @endif
                 @if (in_array(20, $column_array))
                     <th data-index=20 style="min-width: 50px"> CL </th>
                 @endif
                 @if (in_array(40, $column_array))
                     <th data-index=40 style="min-width:50px"> VNDR SUPR </th>
                 @endif
                 @if (in_array(38, $column_array))
                     <th data-index=38 style="min-width: 140px">
                         @if ($page_type == 'support-contracts')
                             Status
                         @else
                             Support Status
                         @endif

                     </th>
                 @endif

                 @if (in_array(44, $column_array))
                     <th data-index=44> Contract# </th>
                 @endif

                 @if (in_array(39, $column_array))
                     <th data-index=39 style="min-width: 140px">
                         @if ($page_type == 'support-contracts')
                             End Date
                         @else
                             SupportEndDate
                         @endif
                     </th>

                 @endif

                 @if (in_array(27, $column_array))
                     <th data-index=27 style="min-width: 50px"> SSL CERT </th>
                 @endif
                 @if (in_array(46, $column_array))
                     <th data-index=46 style="min-width: 130px">
                         @if ($page_type == 'ssl-certificate')
                             STATUS
                         @else
                             CERT STATUS
                         @endif
                     </th>
                 @endif
                 @if (in_array(47, $column_array))
                     <th data-index=47 style="min-width: 130px">
                         @if ($page_type == 'ssl-certificate')
                             ISSUER
                         @else
                             CERT ISSUER
                         @endif
                     </th>
                 @endif
                 @if (in_array(48, $column_array))
                     <th data-index=48 style="min-width: 200px">
                         @if ($page_type == 'ssl-certificate')
                             EXPIRATION
                         @else
                             CERT EXPIRATION
                         @endif
                     </th>

                 @endif
                 @if (in_array(49, $column_array))
                     <th data-index=49 style="min-width:80px"> Managed </th>
                 @endif





                 @if (in_array(13, $column_array))
                     <th data-index=13 style="min-width: 100px"> App Owner </th>
                 @endif
                 @if (in_array(29, $column_array))
                     <th data-index=29 style="min-width: 90px"> SLA </th>
                 @endif
                 @if (in_array(18, $column_array))
                     <th data-index=18 style="min-width: 50px"> DR </th>
                 @endif
                 @if (in_array(21, $column_array))
                     <th data-index=21 style="min-width: 50px"> MNT </th>
                 @endif
                 @if (in_array(22, $column_array))
                     <th data-index=22 style="min-width: 50px"> PTC </th>
                 @endif
                 @if (in_array(23, $column_array))
                     <th data-index=23 style="min-width: 50px"> AV </th>
                 @endif
                 @if (in_array(24, $column_array))
                     <th data-index=24 style="min-width: 50px"> BKP </th>
                 @endif
                 @if (in_array(25, $column_array))
                     <th data-index=25 style="min-width: 50px"> REP </th>
                 @endif
                 @if (in_array(26, $column_array))
                     <th data-index=26 style="min-width: 60px"> SMTP </th>
                 @endif
                 @if (in_array(28, $column_array))
                     <th data-index=28 style="min-width: 60px"><a
                             class=" 
                                                ">Syslog </a></th>
                 @endif



                 @if ($page_type != 'servers')
                     @if (in_array(30, $column_array))
                         <th data-index=30 style="min-width: 100px"><a
                                 class=" 
                                                ">CPU</a></th>
                     @endif
                 @endif

                 <!--
                                                <th  data-index=31  style="min-width: 120px"><a   class=" 
                                                ">CPU Sockets   </a></th>
                                                  <th  data-index=32  style="min-width: 100px"><a   class=" 
                                                ">CPU Cores   </a></th>
                                                  <th  data-index=33  style="min-width: 140px"><a   class=" 
                                                ">CPU Freq(GHz)   </a></th> -->
                 @if (in_array(34, $column_array))
                     <th data-index=34 style="min-width: 170px"><a
                             class=" 
                                                ">CPU Hyperthreadings </a></th>
                 @endif
                 @if (in_array(35, $column_array))
                     <th data-index=35 style="min-width: 150px"><a
                             class=" 
                                                ">CPU Total Cores </a></th>
                 @endif

                 @if (in_array(43, $column_array))
                     <th data-index=43 style="min-width:50px"><a class="  ">DECOM DATE</a></th>
                 @endif



             </tr>
         </thead>
         <tbody id="showdata">
             <?php
             function isValidDate($date)
             {
                 return date('Y-m-d', strtotime($date)) === $date;
             }
             
             ?>
             @php  $sno=0 @endphp
             @foreach ($qry as $q)
                 <tr data="{{ $q->id }}" data-pos="{{ $q->position }}">
                     <?php $ssl = DB::table('ssl_certificate as s')
                         ->leftjoin('vendors as v', 'v.id', '=', 's.cert_issuer')
                         ->where('cert_hostname', $q->id)
                         ->where('s.is_deleted', 0)
                         ->first();
                     $cert = '';
                     ?>
                     @if (@$ssl->cert_type == 'internal')
                         <?php $cert = 'Internal Cert'; ?>
                     @elseif(@$ssl->cert_type == 'public')
                         <?php $cert = @$ssl->vendor_name . ($ssl->vendor_name != '' ? 'Public Cert' : ''); ?>
                     @endif


                     <td data-index=0>


                         @if (!isset($_GET['search']) && !isset($_GET['advance_search']) && !isset($_GET['field']))
                             @if (Auth::user()->role == 'admin')
                                 <i class="fa fa-align-justify"></i>
                             @endif
                         @endif

                         {{ ++$sno }}
                     </td>

                     <td data-index=42>
                         @if ($q->AssetStatus == 1)
                             <span class="badge badge-success">Yes</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>

                     <!--  <td data-index="45" class="text-center">
                                                    </td> -->



                     <td data-index=1 class="font-w600">
                         {{ $q->client_display_name }}
                     </td>
                     <td data-index=2>{{ $q->site_name }}</td>
                     <td data-index=3>{{ $q->vendor_name }}</td>
                     <td data-index=4>{{ $q->model }}</td>
                     <td data-index=5>{{ $q->type }}</td>
                     <td data-index=6>{{ $q->sn }}</td>



                     <td data-index=9 class="text- ">
                         <div style="display: inline-flex;">
                             <div class="mt-2">
                                 <a href="javascript:;" data="{{ $q->id }}" data1="{{ $cert }}"
                                     data2="{{ @$ssl->cert_edate != '' ? date('Y-M-d', strtotime($ssl->cert_edate)) : '' }}"
                                     class="btnEdit"> {{ $q->fqdn }}</a>
                                 <p class="text-secondary mb-0">{{ $q->asset_type_description }}</p>
                             </div>
                         </div>

                     </td>
                     <td data-index=10>{{ $q->role }}</td>
                     <td data-index=11>{{ $q->use_ }}</td>

                     <td data-index=7>{{ $q->hostname }}</td>

                     <td data-index=12>{{ $q->operating_system_name }}</td>
                     <td data-index=37>{{ $q->memory }}</td>
                     @if ($page_type == 'servers')
                         <td data-index=30>{{ $q->cpu_sockets }} {{ $q->cpu_model }} {{ $q->cpu_cores }} C @
                             {{ $q->cpu_freq }} GHz</td>
                     @endif
                     <td data-index=8>{{ $q->domain_name }}</td>
                     <td data-index=15>{{ $q->vlanId }}</td>
                     <td data-index=14>{{ $q->ip_address }}</td>
                     <td data-index=16>
                         @if ($q->network_zone == 'Internal')
                             <div class="badge badge-secondary">{{ $q->network_zone }}</div>
                         @elseif($q->network_zone == 'Secure')
                             <div class="badge badge-info">{{ $q->network_zone }}</div>
                         @elseif($q->network_zone == 'Greenzone')
                             <div class="badge badge-success">{{ $q->network_zone }}</div>
                         @elseif($q->network_zone == 'Guest')
                             <div class="badge badge-warning">{{ $q->network_zone }}</div>
                         @elseif($q->network_zone == 'Semi-Trusted')
                             <div class="badge  " style="background:#FFFF11;color: black">{{ $q->network_zone }}
                             </div>
                         @elseif($q->network_zone == 'Public DMZ' || $q->network_zone == 'Public' || $q->network_zone == 'Servers Public DMZ')
                             <div class="badge badge-danger">{{ $q->network_zone }}</div>
                         @else
                             {{ $q->network_zone }}
                         @endif

                     </td>
                     <td data-index=17>
                         @if ($q->internet_facing == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->internet_facing == 2)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>
                     <td data-index=19>
                         @if ($q->load_balancing == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->load_balancing == 2)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>
                     <td data-index=20>
                         @if ($q->clustered == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->clustered == 2)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>

                     <td data-index=40>
                         @if ($q->HasWarranty == 1)
                             <span class="badge badge-success">Yes</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>
                     <td data-index="38">


                         @if ($q->SupportStatus == 'N/A')
                             <span class="badge text-white bg-secondary">
                                 {{ $q->SupportStatus }}
                             </span>
                         @elseif($q->SupportStatus == 'Supported')
                             <span class="badge badge-success">
                                 {{ $q->SupportStatus }}
                             </span>
                         @elseif($q->SupportStatus == 'Unassigned')
                             <span class="badge text-white bg-orange">
                                 {{ $q->SupportStatus }}
                             </span>
                         @elseif($q->SupportStatus == 'Expired')
                             <span class="badge badge-danger">
                                 {{ $q->SupportStatus }}
                             </span>
                         @else
                             <span class="badge text-white bg-secondary">
                                 N/A
                             </span>
                         @endif

                     </td>
                     <?php
                     
                     $contract = DB::table('contract_assets as a')
                         ->join('contracts as c', 'c.id', '=', 'a.contract_id')
                         ->where('a.hostname', $q->id)
                         ->where('c.contract_status', '!=', 'Inactive')
                         ->where(function ($query) {
                             $query->Orwhere('a.status', '!=', 'Inactive');
                             $query->Orwhere('a.status', null);
                         })
                         ->where('a.is_deleted', 0)
                         ->first();
                     ?> <td data-index=44>{{ @$contract->contract_no }}</td>





                     <td data-index="39">
                         {{ isValidDate($q->warranty_end_date) ? date('Y-M-d', strtotime($q->warranty_end_date)) : $q->warranty_end_date }}
                     </td>

                     <td data-index=27>
                         @if ($q->ntp == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->ntp == 2)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>

                     <td data-index=46>
                         @if ($q->ssl_certificate_status == 'N/A')
                             <span class="badge text-white bg-secondary">
                                 {{ $q->ssl_certificate_status }}
                             </span>
                         @elseif($q->ssl_certificate_status == 'Active')
                             <span class="badge badge-success">
                                 {{ $q->ssl_certificate_status }}
                             </span>
                         @elseif($q->ssl_certificate_status == 'Unassigned')
                             <span class="badge text-white bg-orange">
                                 {{ $q->ssl_certificate_status }}
                             </span>
                         @elseif($q->ssl_certificate_status == 'Expired/Ended')
                             <span class="badge badge-danger">
                                 {{ $q->ssl_certificate_status }}
                             </span>
                         @else
                             <span class="badge text-white bg-secondary">
                                 N/A
                             </span>
                         @endif
                     </td>

                     <?php $ssl = DB::table('ssl_certificate as s')
                         ->leftjoin('vendors as v', 'v.id', '=', 's.cert_issuer')
                         ->where('cert_hostname', $q->id)
                         ->where('s.is_deleted', 0)
                         ->first(); ?>
                     <td data-index=47>
                         @if (@$ssl->cert_type == 'internal')
                             Internal Cert
                         @elseif(@$ssl->cert_type == 'public')
                             {{ @$ssl->vendor_name }}
                         @endif
                     </td>

                     <td data-index=48>{{ @$ssl->cert_edate != '' ? date('Y-M-d', strtotime($ssl->cert_edate)) : '' }}</td>


                     <td data-index=49>
                         @if ($q->managed == 1)
                             <span class="badge badge-success">Yes</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>

                     <td data-index=13>{{ $q->app_owner }}</td>


                     <td data-index=29>{{ $q->sla }}</td>









                     <td data-index=18>
                         @if ($q->disaster_recovery == 1 && $q->managed == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->disaster_recovery == 2 || $q->managed != 1)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>

                     <td data-index=21>
                         @if ($q->monitored == 1 && $q->managed == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->monitored == 2 || $q->managed != 1)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>

                     <td data-index=22>
                         @if ($q->patched == 1 && $q->managed == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->patched == 2 || $q->managed != 1)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>
                     <td data-index=23>
                         @if ($q->antivirus == 1 && $q->managed == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->antivirus == 2 || $q->managed != 1)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>
                     <td data-index=24>
                         @if ($q->backup == 1 && $q->managed == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->backup == 2 || $q->managed != 1)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>
                     <td data-index=25>
                         @if ($q->replicated == 1 && $q->managed == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->replicated == 2 || $q->managed != 1)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>

                     <td data-index=26>
                         @if ($q->smtp == 1 && $q->managed == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->smtp == 2 || $q->managed != 1)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>

                     <td data-index=28>
                         @if ($q->syslog == 1 && $q->managed == 1)
                             <span class="badge badge-success">Yes</span>
                         @elseif($q->syslog == 2 || $q->managed != 1)
                             <span class="badge badge-secondary">N/A</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>





                     @if ($page_type != 'servers')
                         <td data-index=30>{{ $q->cpu_sockets }} {{ $q->cpu_model }} {{ $q->cpu_cores }} C @
                             {{ $q->cpu_freq }} GHz</td>
                     @endif


                     <td data-index=34>
                         @if ($q->cpu_hyperthreadings == 1)
                             <span class="badge badge-success">Enabled</span>
                         @else
                             <span class="badge badge-danger">No</span>
                         @endif
                     </td>

                     <td data-index=35>{{ $q->cpu_total_cores }}</td>

                     <td data-index=43>
                         @if ($q->InactiveDate != '' && $q->AssetStatus != 1)
                             {{ date('Y-M-d', strtotime($q->InactiveDate)) }}
                         @else
                             <div class="badge badge-secondary">N/A</div>
                         @endif
                     </td>





                 </tr>
             @endforeach
         </tbody>
     </table>
 @endif
