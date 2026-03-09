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
        if(Auth::user()->role == 'parent') {
            return redirect('/vacations');
        }
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

    public function updateUserProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'profile_image' => 'nullable|image|max:5120',
        ]);

        $user = auth()->user();
        $image = $user->user_image;

        if ((int) $request->input('remove_profile_image') === 1) {
            $image = '';
        } elseif ($request->hasFile('profile_image')) {
            $imageFile = $request->file('profile_image');
            $image = mt_rand(1, 1000) . '' . time() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('client_logos'), $image);
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'name' => trim($request->firstname . ' ' . $request->lastname),
                'user_image' => $image,
            ]);

        auth()->user()->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'image_url' => $image
                ? asset('public/client_logos/' . $image)
                : asset('public/dashboard_assets/media/avatars/avatar2.jpg'),
            'firstname' => auth()->user()->firstname,
            'lastname' => auth()->user()->lastname,
        ]);
    }
}
