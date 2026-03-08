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

{{-- <h3>Subject: {{$data['subject']}}</h3>
<p></p>
 
 <h4>Cert Name :   {{$data['cert_name']}} </a></h4>
  <h4>Hostname :  <span style="text-transform: uppercase;">{{$data['hostname']}}</span></h4>
   <h4>Expiry Date:  {{date('Y/M/d',strtotime($data['cert_edate']))}}</h4> --}}

<h4>{{ $data['description'] }}</h4>
{{-- <h4>Vendor: {{ $data['vendor_name'] }}</h4> --}}
<h4>Cert Name : {{ $data['cert_name'] }}</h4>
<h4>Start Date: {{ $data['cert_sdate'] }}</h4>
<h4>End Date: {{ $data['cert_edate'] }}</h4>
