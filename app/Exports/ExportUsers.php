<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportUsers implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{      

 

 
if(sizeof($_GET)>0){

$orderby='desc';
$field='id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}


 
     $qry=DB::table('users')->where(function($query){
        $query->Orwhere('role','like','%'.@$_GET['search'].'%');
        $query->Orwhere('name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('salutation','like','%'.@$_GET['search'].'%');
        $query->Orwhere('firstname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('lastname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('work_phone','like','%'.@$_GET['search'].'%');
        $query->Orwhere('mobile','like','%'.@$_GET['search'].'%');
        $query->Orwhere('email','like','%'.@$_GET['search'].'%');
 
     }) ->orderBy($field,$orderby)->get(); 
}
 else{
$qry=DB::table('users') ->orderBy('id','desc')->get(); 
 
 }
 
 


 
 
    return view('exports.ExportUsers', [
        'qry' => $qry
    ]);
 }
}