<?php

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
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, enctype');
header('Access-Control-Allow-Methods: GET, PATCH, POST, DELETE');

// Route::get('/', function () {
//     return view('welcome');
// });
Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('/user', 'UserController@getAuthenticatedUser');
    Route::get('/', 'UserController@index');

    Route::get('/person', 'PersonController@index');
    Route::get('/person/{id}', 'PersonController@searchById');
    Route::get('/personbyrole/{role}', 'PersonController@show');
    Route::post('/personbyrole/{role}', 'PersonController@store');
    Route::delete('/deleteperson/{id}', 'PersonController@destroy');
    Route::post('/updateperson/{id}', 'PersonController@update');

    Route::get('/branch','BranchController@index');
    Route::get('/branchEmployee/{id}','BranchController@employeeByBranch');
    Route::get('/branch/{id}','BranchController@show');
    Route::post('/branch','BranchController@store');
    Route::post('/branch/{id}','BranchController@update');
    Route::delete('/branch/{id}','BranchController@destroy');
    
    Route::get('/role','RoleController@index');
    Route::get('/role/{id}','RoleController@show');
    Route::post('/role','RoleController@store');
    Route::post('/role/{id}','RoleController@update');
    Route::delete('/role/{id}','RoleController@destroy');
    
    Route::get('/service','ServiceController@index');
    Route::get('/service/{id}','ServiceController@show');
    Route::post('/service','ServiceController@store');
    Route::post('/service/{id}','ServiceController@update');
    Route::delete('/service/{id}','ServiceController@destroy');

    Route::get('/sparepart','SparepartController@index');
    Route::get('/sparepartBySupplier/{supplierId}','SparepartController@showBySupplier');
    Route::get('/sparepart/{code}','SparepartController@show');
    Route::post('/sparepart','SparepartController@store');
    Route::post('/sparepart/{code}','SparepartController@update');
    Route::delete('/sparepart/{code}','SparepartController@destroy');

    Route::get('/vehicle','VehicleController@index');
    Route::get('/vehicle/{id}','VehicleController@show');
    Route::post('/vehicle','VehicleController@store');
    Route::post('/vehicleCustomer','VehicleController@vehicleCustomer');
    Route::post('/vehicleSparepart','VehicleController@vehicleSparepart');
    Route::post('/vehicle/{id}','VehicleController@update');
    Route::delete('/vehicle/{id}','VehicleController@destroy');

    Route::get('/employee','EmployeeController@index');
    Route::get('/employee/{id}','EmployeeController@show');
    Route::post('/employee','EmployeeController@store');
    Route::post('/employee/{id}','EmployeeController@update');
    Route::delete('/employee/{id}','EmployeeController@destroy');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', 'AuthController@login');
    // Route::post('/register', 'AuthController@register');
    Route::post('/logout', 'AuthController@logout');
});