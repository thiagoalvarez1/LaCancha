<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    //Profit Loss Report
    Route::get('/profit-loss-report', '\Modules\Reports\Http\Controllers\ReportsController@profitLossReport')
        ->name('profit-loss-report.index');
    //Payments Report
    Route::get('/payments-report', '\Modules\Reports\Http\Controllers\ReportsController@paymentsReport')
        ->name('payments-report.index');
    //Sales Report
    Route::get('/sales-report', '\Modules\Reports\Http\Controllers\ReportsController@salesReport')
        ->name('sales-report.index');
    //Purchases Report
    Route::get('/purchases-report', '\Modules\Reports\Http\Controllers\ReportsController@purchasesReport')
        ->name('purchases-report.index');
    //Sales Return Report
    Route::get('/sales-return-report', '\Modules\Reports\Http\Controllers\ReportsController@salesReturnReport')
        ->name('sales-return-report.index');
    //Purchases Return Report
    Route::get('/purchases-return-report', '\Modules\Reports\Http\Controllers\ReportsController@purchasesReturnReport')
        ->name('purchases-return-report.index');
    //Sales by Branch Report
    Route::get('/reports/sales-by-branch', '\Modules\Reports\Http\Controllers\ReportsController@salesByBranchReport')->name('reports.sales-by-branch');
});
