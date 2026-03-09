<?php

use App\Http\Controllers\AssetsController;
use App\Http\Controllers\ContractController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => false, 'register' => false]);

Route::get('/uploads', 'GeneralUploadController@handle');
Route::post('/uploads/save', 'GeneralUploadController@save');
Route::get('/uploads/delete/{id}', 'GeneralUploadController@delete');

Route::get('/privacy-policy', 'HomeController@PrivacyPolicy');

Route::get('/contact-us', 'HomeController@ContactUs');
Route::get('/error', 'HomeController@error');
Route::get('/CheckContractStatus', 'HomeController@CheckContractStatus');
Route::get('/CheckSSLStatus', 'HomeController@CheckSSLStatus');


Route::post('/insert-contact-us', 'HomeController@InsertContactUs');
Route::get('/SharinglinkContract', 'ContractController@SharinglinkContract');

Route::get('/error', 'HomeController@Error');

Route::get('/checkVacations', 'ClientsController@sendVacationPlanningReminders');
Route::get('/change-password', 'AdminController@showChangePasswordForm')->middleware('auth');
Route::post('/change-password', 'AdminController@changePassword')->middleware('auth');
Route::post('/update-user-profile', 'AdminController@updateUserProfile')->middleware('auth');
Route::group(['middleware' => ['auth', 'statuscheck']], function () {
  
  
  Route::get('/home', 'AdminController@index');
  
  Route::get('/', 'AdminController@index');
  // users
  Route::get('/vacations', 'VacationController@index');
  Route::get('/get-vacation-content', 'VacationController@getVacationContent');
  Route::get('/payments', 'PaymentsController@index')->middleware('isadminorstaff');
  Route::get('/get-payment-content', 'PaymentsController@getPaymentContent')->middleware('isadminorstaff');
  Route::get('/add-vacation', 'VacationController@AddVacation');
  Route::post('/insert-attachment-vacation', 'VacationController@InsertAttachmentVacation');
  Route::post('/insert-comment-vacation', 'VacationController@InsertCommentVacation');
  Route::post('/delete-attachment-vacation', 'VacationController@delete_attachment_vacation');
  Route::post('/undo-delete-attachment-vacation', 'VacationController@undo_delete_attachment_vacation');
  Route::post('/delete-comment-vacation', 'VacationController@DeleteCommentVacation');
  Route::post('/undo-delete-comment-vacation', 'VacationController@UndoDeleteCommentVacation');
  Route::post('/end-vacation', 'VacationController@EndVacation');
  Route::post('/delete-vacation', 'VacationController@DeleteVacation')->middleware('isadmin');
  Route::post('/delete-vacation-undo', 'VacationController@DeleteVacationUndo')->middleware('isadmin');
  Route::post('/insert-vacation', 'VacationController@InsertVacation');
  Route::post('/update-vacation', 'VacationController@updateVacation');
  Route::get('/edit-vacation', 'VacationController@editVacation');
  Route::get('/clone-vacation', 'VacationController@cloneVacation');
  Route::get('/get-comments-vacation', 'VacationController@getCommentsVacation');
Route::get('/get-attachment-vacation', 'VacationController@getAttachmentVacation');
Route::post('/mark-vacation-planned', 'VacationController@markPlanned');
  Route::get('/get-students-by-client/{client_id}', function ($client_id) {
    $clients_students = DB::table('client_students')
      ->where('client_id', $client_id)
      ->orderBy('student_name', 'asc')
      ->get();

    return response()->json($clients_students);
  });
  // users
  Route::get('/users', 'UserController@index')->middleware('isadmin');
  Route::get('/get-user-content', 'UserController@getUserContent')->middleware('isadmin');
  Route::get('/add-user', 'UserController@AddUser')->middleware('isadmin');
  Route::post('/insert-attachment-user', 'UserController@InsertAttachmentUser')->middleware('isadmin');
  Route::post('/insert-comment-user', 'UserController@InsertCommentUser')->middleware('isadmin');
  Route::post('/delete-attachment-user', 'UserController@delete_attachment_user')->middleware('isadmin');
  Route::post('/undo-delete-attachment-user', 'UserController@undo_delete_attachment_user')->middleware('isadmin');
  Route::post('/delete-comment-user', 'UserController@DeleteCommentUser')->middleware('isadmin');
  Route::post('/undo-delete-comment-user', 'UserController@UndoDeleteCommentUser')->middleware('isadmin');
  Route::post('/end-user', 'UserController@EndUser')->middleware('isadmin');
  Route::post('/delete-user', 'UserController@DeleteUser')->middleware('isadmin');
  Route::post('/delete-user-undo', 'UserController@DeleteUserUndo')->middleware('isadmin');
  Route::post('/insert-user', 'UserController@InsertUser')->middleware('isadmin');
  Route::post('/update-user', 'UserController@updateUser')->middleware('isadmin');
  Route::post('/reset-user-password', 'UserController@resetUserPassword')->middleware('isadmin');
  Route::post('/resend-user-invite', 'UserController@resendUserInvite')->middleware('isadmin');
  Route::get('/edit-user', 'UserController@editUser')->middleware('isadmin');
  Route::get('/get-comments-user', 'UserController@getCommentsUser')->middleware('isadmin');
  Route::get('/get-attachment-user', 'UserController@getAttachmentUser')->middleware('isadmin');

  // clients
  Route::get('/clients', 'ClientsController@Clients')->middleware('isadminorstaff');
  Route::get('/add-Client', 'ClientsController@AddClient')->middleware('isadminorstaff');
  Route::get('/clone-client', 'ClientsController@cloneClient')->middleware('isadminorstaff');
  Route::get('/edit-client', 'ClientsController@editClient')->middleware('isadminorstaff');
  Route::post('/insert-client', 'ClientsController@InsertClients')->middleware('isadminorstaff');
  Route::get('/edit-clients', 'ClientsController@EditClients')->middleware('isadminorstaff');
  Route::post('/update-client', 'ClientsController@updateClient')->middleware('isadminorstaff');
  Route::get('/delete-clients', 'ClientsController@DeleteClients')->middleware('isadmin');
  Route::get('/show-clients', 'ClientsController@ShowClients')->middleware('isadminorstaff');
  Route::get('/export-excel-clients', 'ClientsController@ExportExcelClients')->middleware('isadminorstaff');
  Route::get('/export-pdf-clients', 'ClientsController@ExportPdfClients')->middleware('isadminorstaff');
  Route::get('/export-print-clients', 'ClientsController@ExportPrintClients')->middleware('isadminorstaff');

  Route::post('/send-renewal-notification', 'ClientsController@sendPaymentReminder');

  Route::get('/get-email-contract-clients', 'ClientsController@getEmailContractClients')->middleware('isadminorstaff');
  Route::get('/get-comments-clients', 'ClientsController@getCommentsClients')->middleware('isadminorstaff');
  Route::get('/get-attachment-clients', 'ClientsController@getAttachmentClients')->middleware('isadminorstaff');
  Route::get('/get-email-clients', 'ClientsController@getEmailClients')->middleware('isadminorstaff');
  Route::get('/get-client-content', 'ClientsController@getClientContent')->middleware('isadminorstaff');

  Route::get('/get-client-students', 'ClientsController@getClientStudents')->middleware('isadminorstaff');
Route::get('/get-client-payments', 'ClientsController@getClientPayments')->middleware('isadminorstaff');
Route::get('/clients/export', 'ClientsController@exportClientData')->middleware('isadminorstaff');
Route::post('/send-payment-receipt', 'ClientsController@sendPaymentReceipt')->middleware('isadminorstaff');
Route::post('/reserve-tax-receipt-number', 'ClientsController@reserveTaxReceiptNumber')->middleware('isadminorstaff');
Route::post('/clients/import', 'ClientsController@importClientData')->middleware('isadminorstaff');
Route::get('/center-settings', 'ClientsController@getCenterSettings')->middleware('isadminorstaff');
Route::post('/center-settings', 'ClientsController@saveCenterSettings')->middleware('isadminorstaff');
  Route::get('/get-client-vacation', 'ClientsController@getClientVacation')->middleware('isadminorstaff');
  Route::get('/confirm-price-change/{id}', 'ClientsController@confirmPriceChange')->middleware('isadminorstaff');
  Route::get('/confirm-method-change/{id}', 'ClientsController@confirmMethodChange')->middleware('isadminorstaff');

  Route::post('/uploadNetworkAttachment', 'ClientsController@uploadNetworkAttachment')->middleware('isadminorstaff');

  Route::post('/insert-comments-client', 'ClientsController@InsertCommentClient')->middleware('isadminorstaff');
  Route::post('/insert-attachment-client', 'ClientsController@InsertAttachmentClient')->middleware('isadminorstaff');
  Route::post('save-pinned-messages', 'ClientsController@add_pinned_message')->middleware('isadminorstaff');

  Route::post('/delete-attachment-client', 'ClientsController@delete_attachment_client')->middleware('isadminorstaff');
  Route::post('/undo-delete-attachment-client', 'ClientsController@undo_delete_attachment_client')->middleware('isadminorstaff');
  Route::post('/delete-comment-client', 'ClientsController@DeleteCommentclient')->middleware('isadminorstaff');
  Route::post('/undo-delete-comment-client', 'ClientsController@UndoDeleteCommentclient')->middleware('isadminorstaff');
  Route::post('/end-client', 'ClientsController@EndClient')->middleware('isadminorstaff');

  Route::post('/delete-client', 'ClientsController@DeleteClient')->middleware('isadmin');
  Route::post('/delete-client-undo', 'ClientsController@DeleteClientUndo')->middleware('isadmin');
});
