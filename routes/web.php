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
    Route::get('/dashboard', 'UserController@dashboard');
    Route::post('/changepassword/{id}', 'UserController@updatePassword');
    
    Route::get('/cekPhoneNumber/{phoneNumber}','PersonController@cekPhoneNumber');
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
    
    Route::get('/service/{id}','ServiceController@show');
    Route::post('/service','ServiceController@store');
    Route::post('/service/{id}','ServiceController@update');
    Route::delete('/service/{id}','ServiceController@destroy');


    Route::get('/sparepart','SparepartController@index');
    Route::get('/sparepartBySupplier/{supplierId}','SparepartController@showBySupplier');
    Route::get('/sparepartBranch/{branchId}','SparepartController@showByBranch');
    Route::post('/sparepartBranch','SparepartController@storeToBranch');
    Route::patch('/sparepartBranch/{spBrID}','SparepartController@updateSpBranch');
    Route::delete('/sparepartBranch/{spBrID}','SparepartController@destroyByBranch');

    Route::get('/sparepart/{code}','SparepartController@show');
    Route::post('/sparepart','SparepartController@store');
    Route::post('/sparepart/{code}','SparepartController@update');
    Route::delete('/sparepart/{code}','SparepartController@destroy');
    Route::post('/sparepartIsUnique','SparepartController@isUniqueCode');

    Route::get('/vehicle','VehicleController@index');
    Route::get('/vehicle/{id}','VehicleController@show');
    Route::post('/vehicle','VehicleController@store');
    Route::post('/vehicleCustomer','VehicleController@vehicleCustomer');
    Route::post('/vehicleSparepart','VehicleController@vehicleSparepart');
    Route::post('/vehicle/{id}','VehicleController@update');
    Route::delete('/vehicle/{id}','VehicleController@destroy');

    Route::get('/employee','EmployeeController@index');
    Route::get('/employeebyBranch/{branch_id}','EmployeeController@showByBranch');
    Route::get('/employee/{id}','EmployeeController@show');
    Route::post('/employee','EmployeeController@store');
    Route::post('/employee/{id}','EmployeeController@update');
    Route::delete('/employee/{id}','EmployeeController@destroy');
    Route::get('/montir/{branch_id}','EmployeeController@showMontirByBranch');

    Route::get('/transaction','TransactionController@index');
    Route::get('/transactionByBranch/{branch_id}','TransactionController@showByBranch');
    Route::post('/transaction','TransactionController@store');
    Route::patch('/transaction/status/{id}','TransactionController@updateStatus');
    Route::delete('/transaction/{id}','TransactionController@destroy');

    
    Route::get('/customer/{transactionId}','TransactionDetailController@getCustomer');
    Route::get('/detailTransaction/{transactionId}','TransactionDetailController@showByTransaction');
    Route::post('/detailTransaction','TransactionDetailController@store');
    Route::post('/detailTransaction/{vehiclecustomerid}','TransactionDetailController@update');
    Route::delete('/detailTransaction/{transactionDetailId}','TransactionDetailController@destroy');

    Route::get('/myvehicle/{transactionDetailId}','TransactionDetailController@myvehicle');

    Route::get('/detailTSp/{transactionDetailId}/{branchId}','TransactionDetailSparepartController@showByTransactionDetail');
    Route::post('/detailTSp','TransactionDetailSparepartController@store');
    Route::post('/detailTSp/{tspId}','TransactionDetailSparepartController@update');
    Route::delete('/detailTSp/{tspId}','TransactionDetailSparepartController@destroy');

    Route::get('/detailTSv/{transactionDetailId}','TransactiondetailServiceController@showByTransactionDetail');
    Route::post('/detailTSv','TransactiondetailServiceController@store');
    Route::post('/detailTSv/{tspId}','TransactiondetailServiceController@update');
    Route::delete('/detailTSv/{tspId}','TransactiondetailServiceController@destroy');
    Route::post('/spk/{idTransaction}/{idDetaiTransaction}','TransactionDetailController@spkExport');
    Route::post('/notaLunas/{idTransaction}','TransactionDetailController@notaLunasExport');
    // pemesanan
    Route::get('/pemesanan/{branchId}','OrderController@showByBranch');
    Route::delete('/pemesanan/{orderId}','OrderController@destroy');
    Route::get('/supplierBranch/{branchId}','SparepartController@showSupplierOfBranch');
    Route::get('/sparepartBS/{supplierId}/{branchId}','SparepartController@showByBranchSupplier');
    Route::post('/order','OrderDetailController@store');
});
Route::get('/mytransaction/{transactionId}','TransactionDetailController@mytransaction');

Route::get('/spk/{idTransaction}/{idDetaiTransaction}','TransactionDetailController@dataExport');

Route::get('/service','ServiceController@index');
Route::get('/cities', 'UserController@city');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', 'AuthController@login');
    // Route::post('/register', 'AuthController@register');
    Route::post('/logout', 'AuthController@logout');
});