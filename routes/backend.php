<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('demo-backend', 'API\DinningController@demoGetRequestFromBackend');

Route::post('login', 'API\DinningController@backendLogin');
Route::get('/unauthorized', 'API\DinningController@unauthorized')->name('unauthorized');
Route::get('logout', 'API\BackendUserController@logout');
Route::get('/role/list', 'API\RoleController@list');

Route::group(['middleware' => 'auth:backend-api'] , function () {
 
    Route::prefix('role/')->group(function () {
        
        Route::get('{id}/delete', 'API\RoleController@delete');
        // Route::get('list', 'API\RoleController@list');
        Route::get('{id}/get-by-id', 'API\RoleController@upsert');
        
        Route::post('create', 'API\RoleController@upsert');
        Route::get('tree' , 'API\RoleController@getUserTree');
        
        Route::post('sync', 'API\RoleController@syncPermission');
        
    });
    
    Route::prefix('user/')->group(function () {
        
        Route::post('create', 'API\BackendUserController@upsert');
        Route::get('{id}/delete', 'API\BackendUserController@delete');
        Route::get('list', 'API\BackendUserController@list');
        Route::get('{id}/get-by-id', 'API\BackendUserController@upsert');
        
    });
    
    Route::prefix('permission/')->group(function () {
        
        Route::get('list', 'API\BackendUserController@permissionList');

    });
    
    // role name and username map API
    
    
});