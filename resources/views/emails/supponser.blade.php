<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Source+Sans+Pro:wght@200;300;400;600;700;900&display=swap"
    rel="stylesheet">

<style type="text/css">
    * {
        font-family: Source Sans Pro;
    }
</style>
<?php

// $hash = uniqid() . Hash::make(time() . rand(1, 100000000000000));
// DB::table('contract_sharable_links')->insert([
//     'contract_id' => $data['contract_id'],
//     'hash' => $hash,
//     'expiry_date' => date('Y-m-d', strtotime('+3 days')),
// ]);
?>

<h4>{{ $data['sub_type'] }} {{ ucFirst($data['type']) }} has been completed.</h4>
{{-- <h4>Cert Name :<a href="{{url('print-asset')}}?key={{$hash}}">{{$data['description']}} (Click to view details)</a> </a></h4>
 
   <h4>Hostname:  {{$data['hostname']}}</h4> --}}
@if ($data['sub_type'] == 'User')
    <h4>Client: {{ $data['client_name'] }}</h4>
    <h4 class="mb-3">Site: {{ $data['site_name'] }}</h4>
    <h4>Open: <a href="{{ url('tech-specs'.'/'. $data['type']) }}?id={{ $data['tech_spec_id'] }}">Tech Specs</a></h4>
@elseif($data['sub_type'] == 'Workstation')
    <h4>Client: {{ $data['client_name'] }}</h4>
    <h4>Site: {{ $data['site_name'] }}</h4>
    <h4>Asset Type: {{ $data['asset'] }}</h4>
    <h4 class="mb-3">Platform: {{ $data['platform'] }}</h4>
    <h4>Open: <a href="{{ url('tech-specs'.'/'. $data['type']) }}?id={{ $data['tech_spec_id'] }}">Tech Specs</a></h4>
@else
    @php
        $asset_data = DB::table('asset_type')
            ->where('asset_type_id', $data['asset'])
            ->first();
    @endphp

    <h4>Client: {{ $data['client_name'] }}</h4>
    <h4>Site: {{ $data['site_name'] }}</h4>
    <h4>Device Type: {{ $data['device_type'] }}</h4>
    <h4>Asset Type: {{ $asset_data->asset_type_description }}</h4>
    <h4 class="mb-3">Platform: {{ $data['platform'] }}</h4>
    <h4>Open: <a href="{{ url('tech-specs'.'/'. $data['type']) }}?id={{ $data['tech_spec_id'] }}">Tech Specs</a></h4>
@endif
