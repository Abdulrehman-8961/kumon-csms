<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Auth;
use DB;

class GeneralUploadController extends Controller
{
    public function __constrcut()
    {
        $this->middleware(['auth', 'isadmin']);
    }
    public function handle()
    {
        return view("GeneralUploads");
    }
    public function save(Request $request)
    { 
        ini_set('upload_max_filesize','1000M');
        ini_set('post_max_size','1000M');
        ini_set('max_execution_time','100000000');
        ini_set('memory_limit','-1');
        $validated = $request->validate([
            "document" => "required",    
        ]);
        if($validated) {
            if($request->hasFile("document")) {
                $document = $request->file('document');
                 $filename = bin2hex(random_bytes(5)) . '' .date("Ymd") . '' . date("Hi") . '' . (microtime(true) * 1000) . '' . rand(100, 999) . '.' . $document->getClientOriginalExtension();
                 if($document->move(public_path() . '/general_uploads', $filename)) {
                    DB::table('general_uploads')->insert([
                        "user_id" => Auth::user()->id,
                        "filename" => $filename,
                       "url" => "https://".RequestFacade::getHost().'/public/general_uploads/'.$filename, 
                    ]);
                    return redirect()->back()->with('success', 'File successfully added');
                 }
            }
        }
        return redirect()->back()->with('error', 'enable to add file');
    }
    public function delete($id)
    {
        $oldFile = DB::table('general_uploads')->where('id', $id)->first();
        if(@$oldFile) {
            unlink(public_path('/general_uploads') . DIRECTORY_SEPARATOR . $oldFile->filename);
            DB::table('general_uploads')->where('id', $id)->delete();
            return redirect()->back()->with('success', 'File deleted successfully');
        }
        return redirect()->back()->with('error', 'Unable to delete file.');
    }
}