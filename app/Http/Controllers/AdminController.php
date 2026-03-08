<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Mail;
use Hash;
use PDF;
use VerumConsilium\Browsershot\Facades\PDF as NEWPDF;

use Validator;

class AdminController extends Controller
{
    //
    public function __construct() {}

    public function index()
    {
        return view('dashboard');
    }
    //  public function changePassword(){
    //  return view('changePassword');
    // }

    public function showChangePasswordForm()
    {
        return view('auth.force_change_password');
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'new_password' => [
                    'required',
                    'min:8',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*?&#^(){}\[\]<>~+=|\/.,:;\'"-]/',
                ],
                'confirm_password' => 'required|same:new_password',
            ]);

            DB::table('users')
                ->where('id', auth()->id())
                ->update([
                    // 'name' => $request->new_password,
                    'password' => Hash::make($request->new_password),
                    'password_verified' => now(),
                    'must_change' => 0,
                ]);

            auth()->user()->refresh();

            return redirect('/home')->with('success', 'Password changed successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // You can keep field-level errors
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('general_error', 'Change password failed validation.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Something went wrong. Please try again.']);
        }
    }
}
