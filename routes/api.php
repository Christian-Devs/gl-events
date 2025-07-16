<?php
use App\Http\Controllers\Api\QuoteController;
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
