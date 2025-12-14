<?php

Route::group(['namespace' => '\Modules\Gudang\Http\Controllers\Backend', 'as' => 'backend.', 'middleware' => ['web', 'auth', 'can:view_backend'], 'prefix' => 'admin'], function () {

    $module_name = 'gudang';
    $controller_name = 'GudangController';
    Route::get("$module_name/datatable", ['as' => "$module_name.datatable", 'uses' => "$controller_name@datatable"]);
    Route::get("$module_name/index_list", ['as' => "$module_name.index_list", 'uses' => "$controller_name@index_list"]);
    Route::get("$module_name/index_data", ['as' => "$module_name.index_data", 'uses' => "$controller_name@index_data"]);
    Route::get("$module_name/trashed", ['as' => "$module_name.trashed", 'uses' => "$controller_name@trashed"]);
    Route::patch("$module_name/trashed/{id}", ['as' => "$module_name.restore", 'uses' => "$controller_name@restore"]);

    Route::get('gudang/export/csv', 'GudangController@exportCsv')->name('gudang.export.csv');

    Route::resource("$module_name", "$controller_name");
});
