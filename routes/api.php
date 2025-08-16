<?php
use App\Http\Controllers\Api\InvoiceController;
use App\Role;

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

Route::apiResource('/employee', 'Api\EmployeeController');
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
