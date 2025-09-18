<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\JobcardController;
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SalaryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\AuthController;
use App\Role;
use App\Http\Controllers\Payroll\EmployeeSyncController;
use App\Http\Controllers\Payroll\LeaveController;
use App\Http\Controllers\Payroll\PayslipController;
use App\Http\Controllers\Api\SystemSettingController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function () {
    Route::post('login',   [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout',  [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me',      [AuthController::class, 'me']);
    Route::post('update-password', [AuthController::class, 'changePassword']);
});

Route::get('/roles', function () {
    return Role::all();
});

Route::get('/settings/public', [SystemSettingController::class, 'public'])->name('settings.public');

Route::middleware(['api', 'auth:api'])->group(function () {
    // must be BEFORE apiResource so "self" isn't treated as {employee}
    Route::get('/employee/self', [EmployeeController::class, 'self'])->name('employee.self');

    // constrain {employee} so it only matches numbers, not "self"
    Route::apiResource('/employee', EmployeeController::class)
        ->where(['employee' => '[0-9]+']);

    // System settings (read & update, auth required)
    Route::get('/settings',  [SystemSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings',  [SystemSettingController::class, 'update'])->name('settings.update');

    Route::apiResource('/supplier', SupplierController::class);
    Route::apiResource('/jobcard', JobcardController::class);
    Route::apiResource('/quotes', QuoteController::class);
    Route::apiResource('/invoices', InvoiceController::class);
    Route::apiResource('/payments', PaymentController::class);
    Route::apiResource('/salaries', SalaryController::class);

    Route::get('/reports/quotes',  [ReportController::class, 'quotesReport']);
    Route::get('/reports/invoices',  [ReportController::class, 'invoicesReport']);
    Route::get('/reports/payments',  [ReportController::class, 'paymentsReport']);
    Route::get('/reports/jobcards',  [ReportController::class, 'jobcardsReport']);
    Route::get('/reports/salaries',  [ReportController::class, 'salariesReport']);
    Route::get('/reports/{type}/download',  [ReportController::class, 'downloadReportPdf']);
    Route::get('/reports/{type}/email',  [ReportController::class, 'emailReport']);

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

        //get list of supported banks from SimplePay
        Route::get('/banks', function () {
            return response()->json(config('services.simplepay.banks') ?: []);
        });
    });

    Route::get('/dashboard/summary', [DashboardController::class, 'summary'])->name('dashboard.summary');
});
