<?php

use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\InvoiceController;
use App\Role;
use App\Http\Controllers\Payroll\EmployeeSyncController;
use App\Http\Controllers\Payroll\LeaveController;
use App\Http\Controllers\Payroll\PayslipController;
use Illuminate\Support\Facades\Route;

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::get('/roles', function () {
    return Role::all();
});


Route::middleware(['api', 'auth:api'])->group(function () {
    // must be BEFORE apiResource so "self" isn't treated as {employee}
    Route::get('/employee/self', [EmployeeController::class, 'self'])->name('employee.self');

    // constrain {employee} so it only matches numbers, not "self"
    Route::apiResource('/employee', EmployeeController::class)
        ->where(['employee' => '[0-9]+']);
});
Route::apiResource('/supplier', 'Api\SupplierController');
Route::apiResource('/jobcard', 'Api\JobcardController');
Route::apiResource('/settings', 'Api\SystemSettingController')->only(['index', 'update']);
Route::apiResource('/quotes', 'Api\QuoteController');
Route::apiResource('/invoices', 'Api\InvoiceController');
Route::apiResource('/payments', 'Api\PaymentController');
Route::apiResource('/salaries', 'Api\SalaryController');


//Reporting Routes
Route::get('/reports/quotes', 'Api\ReportController@quotesReport');
Route::get('/reports/invoices', 'Api\ReportController@invoicesReport');
Route::get('/reports/payments', 'Api\ReportController@paymentsReport');
Route::get('/reports/jobcards', 'Api\ReportController@jobcardsReport');
Route::get('/reports/salaries', 'Api\ReportController@salariesReport');
Route::get('/reports/{type}/download', 'Api\ReportController@downloadReportPdf');
Route::post('/reports/{type}/email', 'Api\ReportController@emailReport');




//custom routes
Route::get('/invoices/{id}/download', [InvoiceController::class, 'downloadPdf']);
Route::post('/invoices/{id}/send', [InvoiceController::class, 'sendInvoiceEmail']);
Route::post('/invoices/{id}/generate-payment', [InvoiceController::class, 'generatePayment']);
Route::prefix('payroll')->group(function () {
    // Leave
    Route::get('/leave-types', [LeaveController::class, 'types']);
    Route::get('/leave-applications', [LeaveController::class, 'index']);
    Route::post('/leave-applications', [LeaveController::class, 'store']);

    // Payslips list (optional; you already have show + pdf)
    Route::get('/payslips', [PayslipController::class, 'list']);
    Route::post('/simplepay/employee/sync', [EmployeeSyncController::class, 'syncOne']);
    Route::get('/payslip', [PayslipController::class, 'show']);           // ?employee_id=&client_id=
    Route::get('/payslip/{id}/pdf', [PayslipController::class, 'pdf']);   // {id} = payslip_id
});

Route::get('/dashboard/summary', 'Api\DashboardController@summary');
