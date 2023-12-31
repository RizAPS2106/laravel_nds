<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CutPlanController;
use App\Http\Controllers\CutPlanNewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\SpreadingController;
use App\Http\Controllers\FormCutInputController;
use App\Http\Controllers\ManualFormCutController;
use App\Http\Controllers\PilotFormCutController;
use App\Http\Controllers\LapPemakaianController;
use App\Http\Controllers\MasterPartController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\StockerController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\MasterLokasiController;
use App\Http\Controllers\InMaterialController;
use App\Http\Controllers\OutMaterialController;
use App\Http\Controllers\MutLokasiController;
use App\Http\Controllers\QcPassController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\DCInController;
use App\Http\Controllers\SecondaryInController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\RackStockerController;
use App\Http\Controllers\TrolleyController;
use App\Http\Controllers\TrolleyStockerController;
use App\Http\Controllers\SecondaryInhouseController;
use App\Http\Controllers\MutasiMesinController;
use App\Http\Controllers\MasterSecondaryController;

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

Auth::routes(['register' => false]);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    // User
    Route::controller(UserController::class)->prefix("user")->group(function () {
        Route::put('/update/{id?}', 'update')->name('update-user');
    });

    // Marker
    Route::controller(MarkerController::class)->prefix("marker")->middleware('marker')->group(function () {
        Route::get('/', 'index')->name('marker');
        Route::get('/create', 'create')->name('create-marker');
        Route::post('/store', 'store')->name('store-marker');
        Route::get('/edit/{id?}', 'edit')->name('edit-marker');
        Route::put('/update/{id?}', 'update')->name('update-marker');
        Route::post('/show', 'show')->name('show-marker');
        Route::post('/show_gramasi', 'show_gramasi')->name('show_gramasi');
        Route::post('/update_status', 'update_status')->name('update_status');
        Route::put('/update_marker', 'update_marker')->name('update_marker');
        Route::post('/print-marker/{kodeMarker?}', 'printMarker')->name('print-marker');

        // get order
        Route::get('/get-order', 'getOrderInfo')->name('get-marker-order');
        // get colors
        Route::get('/get-colors', 'getColorList')->name('get-marker-colors');
        // get panels
        Route::get('/get-panels', 'getPanelList')->name('get-marker-panels');
        // get sizes
        Route::get('/get-sizes', 'getSizeList')->name('get-marker-sizes');
        // get count
        Route::get('/get-count', 'getCount')->name('get-marker-count');
        // get number
        Route::get('/get-number', 'getNumber')->name('get-marker-number');
    });

    // Spreading
    Route::controller(SpreadingController::class)->prefix("spreading")->middleware('spreading')->group(function () {
        Route::get('/', 'index')->name('spreading');
        Route::get('/create', 'create')->name('create-spreading');
        Route::post('/getno_marker', 'getno_marker')->name('getno_marker');
        Route::get('/getdata_marker', 'getdata_marker')->name('getdata_marker');
        Route::get('/getdata_ratio', 'getdata_ratio')->name('getdata_ratio');
        Route::post('/store', 'store')->name('store-spreading');
        Route::put('/update', 'update')->name('update-spreading');
        Route::put('/update-status', 'updateStatus')->name('update-status');
        Route::get('/get-order-info', 'getOrderInfo')->name('get-spreading-data');
        Route::get('/get-cut-qty', 'getCutQty')->name('get-cut-qty-data');
        Route::delete('/destroy/{id?}', 'destroy')->name('destroy-spreading');
        // export excel
        // Route::get('/export_excel', 'export_excel')->name('export_excel');
        // Route::get('/export', 'export')->name('export');
    });

    // Form Cut Input
    Route::controller(FormCutInputController::class)->prefix("form-cut-input")->middleware("meja")->group(function () {
        Route::get('/', 'index')->name('form-cut-input');
        Route::get('/process/{id?}', 'process')->name('process-form-cut-input');
        Route::get('/get-number-data', 'getNumberData')->name('get-number-form-cut-input');
        Route::get('/get-scanned-item/{id?}', 'getScannedItem')->name('get-scanned-form-cut-input');
        Route::get('/get-item', 'getItem')->name('get-item-form-cut-input');
        Route::put('/start-process/{id?}', 'startProcess')->name('start-process-form-cut-input');
        Route::put('/next-process-one/{id?}', 'nextProcessOne')->name('next-process-one-form-cut-input');
        Route::put('/next-process-two/{id?}', 'nextProcessTwo')->name('next-process-two-form-cut-input');
        Route::get('/get-time-record/{noForm?}', 'getTimeRecord')->name('get-time-form-cut-input');
        Route::post('/store-scanned-item', 'storeScannedItem')->name('store-scanned-form-cut-input');
        Route::post('/store-time-record', 'storeTimeRecord')->name('store-time-form-cut-input');
        Route::post('/store-time-record-extension', 'storeTimeRecordExtension')->name('store-time-ext-form-cut-input');
        Route::post('/store-this-time-record', 'storeThisTimeRecord')->name('store-this-time-form-cut-input');
        Route::put('/finish-process/{id?}', 'finishProcess')->name('finish-process-form-cut-input');
        Route::get('/check-spreading-form/{noForm?}/{noMeja?}', 'checkSpreadingForm')->name('check-spreading-form-cut-input');
        Route::get('/check-time-record/{detailId?}', 'checkTimeRecordLap')->name('check-time-record-form-cut-input');
        Route::post('/store-lost-time/{id?}', 'storeLostTime')->name('store-lost-form-cut-input');
        Route::get('/check-lost-time/{id?}', 'checkLostTime')->name('check-lost-form-cut-input');
        Route::get('/get-form-cut-ratio', 'getRatio')->name('get-form-cut-ratio');

        // get order
        Route::get('/get-order', 'getOrderInfo')->name('form-cut-get-marker-order');
        // get colors
        Route::get('/get-colors', 'getColorList')->name('form-cut-get-marker-colors');
        // get panels
        Route::get('/get-panels', 'getPanelList')->name('form-cut-get-marker-panels');
        // get sizes
        Route::get('/get-sizes', 'getSizeList')->name('form-cut-get-marker-sizes');
        // get count
        Route::get('/get-count', 'getCount')->name('form-cut-get-marker-count');
        // get number
        Route::get('/get-number', 'getNumber')->name('form-cut-get-marker-number');

        // no cut update
        Route::put('/update-no-cut', 'updateNoCut')->name('form-cut-update-no-cut');
    });

    // Manual Form Cut Input
    Route::controller(ManualFormCutController::class)->prefix("manual-form-cut")->middleware("meja")->group(function () {
        Route::get('/', 'index')->name('manual-form-cut');
        Route::get('/create', 'create')->name('create-manual-form-cut');
        Route::get('/create-new', 'createNew')->name('create-new-manual-form-cut');
        Route::get('/process/{id?}', 'process')->name('process-manual-form-cut');
        Route::get('/get-number-data', 'getNumberData')->name('get-number-manual-form-cut');
        Route::get('/get-scanned-item/{id?}', 'getScannedItem')->name('get-scanned-manual-form-cut');
        Route::get('/get-item', 'getItem')->name('get-item-manual-form-cut');
        Route::put('/start-process', 'startProcess')->name('start-process-manual-form-cut');
        Route::post('/store-marker/{id?}', 'storeMarker')->name('store-marker-manual-form-cut');
        Route::put('/next-process-one/{id?}', 'nextProcessOne')->name('next-process-one-manual-form-cut');
        Route::put('/next-process-two/{id?}', 'nextProcessTwo')->name('next-process-two-manual-form-cut');
        Route::get('/get-time-record/{noForm?}', 'getTimeRecord')->name('get-time-manual-form-cut');
        Route::post('/store-scanned-item', 'storeScannedItem')->name('store-scanned-manual-form-cut');
        Route::post('/store-time-record', 'storeTimeRecord')->name('store-time-manual-form-cut');
        Route::post('/store-time-record-extension', 'storeTimeRecordExtension')->name('store-time-ext-manual-form-cut');
        Route::post('/store-this-time-record', 'storeThisTimeRecord')->name('store-this-time-manual-form-cut');
        Route::put('/finish-process/{id?}', 'finishProcess')->name('finish-process-manual-form-cut');
        Route::get('/check-spreading-form/{noForm?}/{noMeja?}', 'checkSpreadingForm')->name('check-spreading-manual-form-cut');
        Route::get('/check-time-record/{detailId?}', 'checkTimeRecordLap')->name('check-time-record-manual-form-cut');
        Route::post('/store-lost-time/{id?}', 'storeLostTime')->name('store-lost-manual-form-cut');
        Route::get('/check-lost-time/{id?}', 'checkLostTime')->name('check-lost-manual-form-cut');
        Route::get('/get-form-cut-ratio', 'getRatio')->name('get-manual-form-cut-ratio');

        // get order
        Route::get('/get-order', 'getOrderInfo')->name('manual-form-cut-get-order');
        // get colors
        Route::get('/get-colors', 'getColorList')->name('manual-form-cut-get-colors');
        // get panels
        Route::get('/get-panels', 'getPanelList')->name('manual-form-cut-get-panels');
        // get sizes
        Route::get('/get-sizes', 'getSizeList')->name('manual-form-cut-get-sizes');
        // get count
        Route::get('/get-count', 'getCount')->name('manual-form-cut-get-count');
        // get number
        Route::get('/get-number', 'getNumber')->name('manual-form-cut-get-number');
    });

    // Pilot Form Cut Input
    Route::controller(PilotFormCutController::class)->prefix("pilot-form-cut")->middleware("meja")->group(function () {
        Route::get('/', 'index')->name('pilot-form-cut');
        Route::get('/create', 'create')->name('create-pilot-form-cut');
        Route::get('/create-new', 'createNew')->name('create-new-pilot-form-cut');
        Route::get('/process/{id?}', 'process')->name('process-pilot-form-cut');
        Route::get('/get-number-data', 'getNumberData')->name('get-number-pilot-form-cut');
        Route::get('/get-scanned-item/{id?}', 'getScannedItem')->name('get-scanned-pilot-form-cut');
        Route::get('/get-item', 'getItem')->name('get-item-pilot-form-cut');
        Route::put('/start-process', 'startProcess')->name('start-process-pilot-form-cut');
        Route::post('/store-marker/{id?}', 'storeMarker')->name('store-marker-pilot-form-cut');
        Route::put('/next-process-one/{id?}', 'nextProcessOne')->name('next-process-one-pilot-form-cut');
        Route::put('/next-process-two/{id?}', 'nextProcessTwo')->name('next-process-two-pilot-form-cut');
        Route::get('/get-time-record/{noForm?}', 'getTimeRecord')->name('get-time-pilot-form-cut');
        Route::post('/store-scanned-item', 'storeScannedItem')->name('store-scanned-pilot-form-cut');
        Route::post('/store-time-record', 'storeTimeRecord')->name('store-time-pilot-form-cut');
        Route::post('/store-time-record-extension', 'storeTimeRecordExtension')->name('store-time-ext-pilot-form-cut');
        Route::post('/store-this-time-record', 'storeThisTimeRecord')->name('store-this-time-pilot-form-cut');
        Route::put('/finish-process/{id?}', 'finishProcess')->name('finish-process-pilot-form-cut');
        Route::get('/check-spreading-form/{noForm?}/{noMeja?}', 'checkSpreadingForm')->name('check-spreading-pilot-form-cut');
        Route::get('/check-time-record/{detailId?}', 'checkTimeRecordLap')->name('check-time-record-pilot-form-cut');
        Route::post('/store-lost-time/{id?}', 'storeLostTime')->name('store-lost-pilot-form-cut');
        Route::get('/check-lost-time/{id?}', 'checkLostTime')->name('check-lost-pilot-form-cut');
        Route::get('/get-form-cut-ratio', 'getRatio')->name('get-pilot-form-cut-ratio');

        // get order
        Route::get('/get-order', 'getOrderInfo')->name('pilot-form-cut-get-order');
        // get colors
        Route::get('/get-colors', 'getColorList')->name('pilot-form-cut-get-colors');
        // get panels
        Route::get('/get-panels', 'getPanelList')->name('pilot-form-cut-get-panels');
        // get sizes
        Route::get('/get-sizes', 'getSizeList')->name('pilot-form-cut-get-sizes');
        // get count
        Route::get('/get-count', 'getCount')->name('pilot-form-cut-get-count');
        // get number
        Route::get('/get-number', 'getNumber')->name('pilot-form-cut-get-number');
    });

    // Cutting Plan
    Route::controller(CutPlanController::class)->prefix("cut-plan")->middleware('admin')->group(function () {
        Route::get('/', 'index')->name('cut-plan');
        Route::get('/create', 'create')->name('create-cut-plan');
        Route::post('/store', 'store')->name('store-cut-plan');
        Route::put('/update/{id?}', 'update')->name('update-cut-plan');
        Route::delete('/destroy', 'destroy')->name('destroy-cut-plan');
        Route::get('/get-selected-form/{noCutPlan?}', 'getSelectedForm')->name('get-selected-form');
        Route::get('/get-cut-plan-form', 'getCutPlanForm')->name('get-cut-plan-form');
    });

    // Cutting Plan New
    // Route::controller(CutPlanNewController::class)->prefix("cut-plan-new")->middleware('admin')->group(function () {
    //     Route::get('/', 'index')->name('cut-plan-new');
    //     Route::post('/show_detail', 'show_detail')->name('show_detail');
    //     Route::get('/create', 'create')->name('create-cut-plan');
    //     Route::post('/store', 'store')->name('store-cut-plan');
    //     Route::put('/update', 'update')->name('update-cut-plan');
    //     Route::delete('/destroy', 'destroy')->name('destroy-cut-plan');
    //     Route::get('/get-selected-form/{noCutPlan?}', 'getSelectedForm')->name('get-selected-form');
    // });

    // Laporan
    Route::controller(LapPemakaianController::class)->prefix("lap_pemakaian")->middleware('admin')->group(function () {
        Route::get('/', 'index')->name('lap_pemakaian');
        // export excel
        Route::get('/export_excel', 'export_excel')->name('export_excel');
        Route::get('/export', 'export')->name('export');
    });

    // Master Part
    Route::controller(MasterPartController::class)->prefix("master-part")->middleware('stocker')->group(function () {
        Route::get('/', 'index')->name('master-part');
        Route::post('/store', 'store')->name('store-master-part');
        Route::put('/update/{id?}', 'update')->name('update-master-part');
        Route::delete('/destroy/{id?}', 'destroy')->name('destroy-master-part');
    });

    // Master Secondary
    Route::controller(MasterSecondaryController::class)->prefix("master-secondary")->middleware('marker')->group(function () {
        Route::get('/', 'index')->name('master-secondary');
        Route::post('/store', 'store')->name('store-master-secondary');
        Route::get('/show_master_secondary', 'show_master_secondary')->name('show_master_secondary');
        Route::put('/update_master_secondary', 'update_master_secondary')->name('update_master_secondary');
        Route::delete('/destroy/{id?}', 'destroy')->name('destroy-master-secondary');
    });


    // Part
    Route::controller(PartController::class)->prefix("part")->middleware('stocker')->group(function () {
        Route::get('/', 'index')->name('part');
        Route::get('/create', 'create')->name('create-part');
        Route::post('/store', 'store')->name('store-part');
        Route::get('/edit', 'edit')->name('edit-part');
        Route::put('/update/{id?}', 'update')->name('update-part');
        Route::delete('/destroy/{id?}', 'destroy')->name('destroy-part');

        // part form
        Route::get('/manage-part-form/{id?}', 'managePartForm')->name('manage-part-form');
        Route::get('/get-form-cut/{id?}', 'getFormCut')->name('get-part-form-cut');
        Route::post('/store-part-form', 'storePartForm')->name('store-part-form');
        Route::delete('/destroy-part-form', 'destroyPartForm')->name('destroy-part-form');
        Route::get('/show-part-form', 'showPartForm')->name('show-part-form');

        // part secondary
        Route::get('/manage-part-secondary/{id?}', 'managePartSecondary')->name('manage-part-secondary');
        Route::get('/datatable_list_part/{id?}', 'datatable_list_part')->name('datatable_list_part');
        Route::get('/get_proses', 'get_proses')->name('get_proses');
        Route::post('/store_part_secondary', 'store_part_secondary')->name('store_part_secondary');

        // get order
        Route::get('/get-order', 'getOrderInfo')->name('get-part-order');
        // get colors
        Route::get('/get-colors', 'getColorList')->name('get-part-colors');
        // get panels
        Route::get('/get-panels', 'getPanelList')->name('get-part-panels');
        // get master part
        Route::get('/get-master-parts', 'getMasterParts')->name('get-master-parts');
        // get master tujuan
        Route::get('/get-master-tujuan', 'getMasterTujuan')->name('get-master-tujuan');
        // get master secondary
        Route::get('/get-master-secondary', 'getMasterSecondary')->name('get-master-secondary');
    });

    // Stocker
    Route::controller(StockerController::class)->prefix("stocker")->middleware('stocker')->group(function () {
        Route::get('/', 'index')->name('stocker');
        Route::get('/show/{partDetailId?}/{formCutId?}', 'show')->name('show-stocker');
        Route::post('/print-stocker/{index?}', 'printStocker')->name('print-stocker');
        Route::post('/print-stocker-all-size/{partDetailId?}', 'printStockerAllSize')->name('print-stocker-all-size');
        Route::post('/print-numbering/{index?}', 'printNumbering')->name('print-numbering');
        Route::post('/rearrange-group', 'rearrangeGroup')->name('rearrange-group');

        Route::put('/count-stocker-update', 'countStockerUpdate')->name('count-stocker-update');

        Route::get('/stocker-part', 'part')->name('stocker-part');

        // part form
        Route::get('/manage-part-form/{id?}', 'managePartForm')->name('stocker-manage-part-form');
        Route::get('/get-form-cut/{id?}', 'getFormCut')->name('stocker-get-part-form-cut');
        Route::post('/store-part-form', 'storePartForm')->name('stocker-store-part-form');
        Route::delete('/destroy-part-form', 'destroyPartForm')->name('stocker-destroy-part-form');
        Route::get('/show-part-form', 'showPartForm')->name('stocker-show-part-form');

        // part secondary
        Route::get('/manage-part-secondary/{id?}', 'managePartSecondary')->name('stocker-manage-part-secondary');
        Route::get('/datatable_list_part/{id?}', 'datatable_list_part')->name('stocker-datatable_list_part');
        Route::get('/get_proses', 'get_proses')->name('stocker-get_proses');
        Route::post('/store_part_secondary', 'store_part_secondary')->name('stocker-store_part_secondary');
    });

    // DC IN
    Route::controller(DCInController::class)->prefix("dc-in")->middleware('dc')->group(function () {
        Route::get('/', 'index')->name('dc-in');
        Route::get('/create/{no_form?}', 'create')->name('create-dc-in');
        Route::get('/getdata_stocker_info', 'getdata_stocker_info')->name('getdata_stocker_info');
        Route::get('/getdata_stocker_input', 'getdata_stocker_input')->name('getdata_stocker_input');
        Route::get('/getdata_dc_in', 'getdata_dc_in')->name('getdata_dc_in');
        Route::post('/show_tmp_dc_in', 'show_tmp_dc_in')->name('show_tmp_dc_in');
        Route::post('/get_alokasi', 'get_alokasi')->name('get_alokasi');
        Route::post('/get_det_alokasi', 'get_det_alokasi')->name('get_det_alokasi');
        Route::put('/update_tmp_dc_in', 'update_tmp_dc_in')->name('update_tmp_dc_in');
        Route::post('/store', 'store')->name('store_dc_in');
        Route::post('/simpan_final_dc_in', 'simpan_final_dc_in')->name('simpan_final_dc_in');
        Route::get('/getdata_stocker_history', 'getdata_stocker_history')->name('getdata_stocker_history');
    });

    // Secondary INHOUSE
    Route::controller(SecondaryInhouseController::class)->prefix("secondary-inhouse")->middleware('dc')->group(function () {
        Route::get('/', 'index')->name('secondary-inhouse');
        Route::get('/cek_data_stocker_inhouse', 'cek_data_stocker_inhouse')->name('cek_data_stocker_inhouse');
        Route::post('/store', 'store')->name('store-secondary-inhouse');
    });

    // Secondary IN
    Route::controller(SecondaryInController::class)->prefix("secondary-in")->middleware('dc')->group(function () {
        Route::get('/', 'index')->name('secondary-in');
        Route::get('/cek_data_stocker_in', 'cek_data_stocker_in')->name('cek_data_stocker_in');
        Route::post('/store', 'store')->name('store-secondary-in');
    });

    // Rack
    Route::controller(RackController::class)->prefix("rack")->middleware('dc')->group(function () {
        Route::get('/', 'index')->name('rack');
        Route::get('/create', 'create')->name('create-rack');
        Route::post('/store', 'store')->name('store-rack');
        Route::put('/update', 'update')->name('update-rack');
        Route::delete('/destroy/{id?}', 'destroy')->name('destroy-rack');
        Route::post('/print-rack/{id?}', 'printRack')->name('print-rack');
    });

    // Rack Stocker
    Route::controller(RackStockerController::class)->prefix("stock-rack")->middleware('dc')->group(function () {
        Route::get('/', 'index')->name('stock-rack');
        Route::get('/allocate', 'allocate')->name('allocate-rack');
        Route::get('/stock-rack-visual', 'stockRackVisual')->name('stock-rack-visual');
        Route::post('/store', 'store')->name('store-rack-stock');
        Route::put('/update', 'update')->name('update-rack-stock');
        Route::delete('/destroy/{id?}', 'destroy')->name('destroy-rack-stock');
        Route::post('/print-bon-mutasi/{id?}', 'printBonMutasi')->name('print-rack-stock');
    });

    // Trolley
    Route::controller(TrolleyController::class)->prefix("trolley")->middleware('dc')->group(function () {
        Route::get('/', 'index')->name('trolley');
        Route::get('/create', 'create')->name('create-trolley');
        Route::post('/store', 'store')->name('store-trolley');
        Route::put('/update', 'update')->name('update-trolley');
        Route::delete('/destroy/{id?}', 'destroy')->name('destroy-trolley');
        Route::post('/print-trolley/{id?}', 'printTrolley')->name('print-trolley');
    });

    // Trolley Stocker
    Route::controller(TrolleyStockerController::class)->prefix("stock-trolley")->middleware('dc')->group(function () {
        Route::get('/', 'index')->name('stock-trolley');
        Route::get('/allocate', 'allocate')->name('allocate-trolley');
        Route::post('/store', 'store')->name('store-trolley-stock');
        Route::put('/update', 'update')->name('update-trolley-stock');
        Route::delete('/destroy/{id?}', 'destroy')->name('destroy-trolley-stock');
        Route::post('/print-bon-mutasi/{id?}', 'printBonMutasi')->name('print-trolley-stock');
    });

    //Mutasi Karywawan
    // Route::controller(EmployeeController::class)->prefix("mut-karyawan")->middleware('hr')->group(function () {
    //     Route::get('/', 'index')->name('mut-karyawan');
    //     Route::get('/create', 'create')->name('create-mut-karyawan');
    //     Route::post('/store', 'store')->name('store-mut-karyawan');
    //     Route::put('/update', 'update')->name('update-mut-karyawan');
    //     Route::delete('/destroy', 'destroy')->name('destroy-mut-karyawan');
    //     Route::get('/getdataline', 'getdataline')->name('getdataline');
    //     Route::get('/gettotal', 'gettotal')->name('gettotal');
    //     Route::get('/getdatanik', 'getdatanik')->name('getdatanik');
    //     Route::get('/getdatalinekaryawan', 'getdatalinekaryawan')->name('getdatalinekaryawan');
    //     Route::get('/export_excel_mut_karyawan', 'export_excel_mut_karyawan')->name('export_excel_mut_karyawan');
    //     Route::get('/line-chart-data', 'lineChartData')->name('line-chart-data');
    // });

    // Mutasi Mesin
    Route::controller(MutasiMesinController::class)->prefix("mut-mesin")->middleware('hr')->group(function () {
        Route::get('/', 'index')->name('mut-mesin');
        Route::get('/create', 'create')->name('create-mut-mesin');
        Route::post('/store', 'store')->name('store-mut-mesin');
        // Route::put('/update', 'update')->name('update-mut-karyawan');
        // Route::delete('/destroy', 'destroy')->name('destroy-mut-karyawan');
        Route::get('/getdataline', 'getdataline')->name('getdataline');
        Route::get('/gettotal', 'gettotal')->name('gettotal');
        Route::get('/getdatamesin', 'getdatamesin')->name('getdatamesin');
        Route::get('/getdatalinemesin', 'getdatalinemesin')->name('getdatalinemesin');
        Route::get('/export_excel_mut_mesin', 'export_excel_mut_mesin')->name('export_excel_mut_mesin');
        Route::get('/line-chart-data', 'lineChartData')->name('line-chart-data');
    });


    Route::controller(SummaryController::class)->prefix("summary")->middleware('admin')->group(function () {
        Route::get('/', 'index')->name('summary');
    });

    // Manager
    Route::controller(ManagerController::class)->prefix("manager")->middleware('manager')->group(function () {
        Route::get('/cutting', 'cutting')->name('manage-cutting');
        Route::get('/cutting/detail/{id?}', 'detailCutting')->name('detail-cutting');
        Route::put('/cutting/generate/{id?}', 'generateStocker')->name('generate-stocker');
        Route::post('/cutting/update-form', 'updateCutting')->name('update-spreading-form');
        Route::put('/cutting/update-finish/{id?}', 'updateFinish')->name('finish-update-spreading-form');
        Route::delete('/cutting/destroy-roll/{id?}', 'destroySpreadingRoll')->name('destroy-spreading-roll');
    });

    //warehouse
    Route::controller(WarehouseController::class)->prefix("warehouse")->middleware('warehouse')->group(function () {
        Route::get('/', 'index')->name('warehouse');
    });

    //master lokasi
    Route::controller(MasterLokasiController::class)->prefix("master-lokasi")->middleware('master-lokasi')->group(function () {
        Route::get('/', 'index')->name('master-lokasi');
        Route::get('/create', 'create')->name('create-lokasi');
        Route::post('/store', 'store')->name('store-lokasi');
        Route::get('/update/{id?}', 'update')->name('update-lokasi');
        Route::get('/updatestatus', 'updatestatus')->name('updatestatus');
        Route::get('/simpanedit', 'simpanedit')->name('simpan-edit');
        Route::post('/print-lokasi/{id?}', 'printlokasi')->name('print-lokasi');
    });

    //Penerimaan
    Route::controller(InMaterialController::class)->prefix("in-material")->middleware('in-material')->group(function () {
        Route::get('/', 'index')->name('in-material');
        Route::get('/create', 'create')->name('create-inmaterial');
        Route::get('/lokasi-material/{id?}', 'lokmaterial')->name('lokasi-inmaterial');
        Route::get('/edit-material/{id?}', 'editmaterial')->name('edit-inmaterial');
        Route::post('/store', 'store')->name('store-inmaterial-fabric');
        Route::get('/updatedet', 'updatedet')->name('update-inmaterial-fabric');
        Route::get('/get-po', 'getPOList')->name('get-po-list');
        Route::get('/get-ws', 'getWSList')->name('get-ws-list');
        Route::get('/get-detail', 'getDetailList')->name('get-detail-list');
        Route::get('/get-detail-lok', 'getdetaillok')->name('get-detail-addlok');
        Route::get('/show-detail-lok', 'showdetaillok')->name('get-detail-showlok');
        Route::post('/save-lokasi', 'savelokasi')->name('save-lokasi');
        Route::get('/approve-material', 'approvematerial')->name('approve-material');
        Route::post('/print-barcode-inmaterial/{id?}', 'barcodeinmaterial')->name('print-barcode-inmaterial');
        Route::post('/print-pdf-inmaterial/{id?}', 'pdfinmaterial')->name('print-pdf-inmaterial');
    });

    //Pengeluaran
    Route::controller(OutMaterialController::class)->prefix("out-material")->middleware('out-material')->group(function () {
        Route::get('/', 'index')->name('out-material');
        Route::get('/create', 'create')->name('create-outmaterial');
        Route::get('/get-detail_req', 'getdetailreq')->name('get-detail_req');
        Route::get('/get-detail', 'getDetailList')->name('get-detail-item');
        Route::get('/show-detail-item', 'showdetailitem')->name('get-detail-showitem');
        Route::get('/get-list-barcode', 'getListbarcode')->name('get-list-barcode');
        Route::get('/get-data-barcode', 'showdetailbarcode')->name('get-data-barcode');
        Route::post('/save-out-manual', 'saveoutmanual')->name('save-out-manual');
        Route::post('/save-out-scan', 'saveoutscan')->name('save-out-scan');
        Route::post('/store', 'store')->name('store-outmaterial-fabric');
    });


    //mutasi-lokasi
    Route::controller(MutLokasiController::class)->prefix("mutasi-lokasi")->middleware('mutasi-lokasi')->group(function () {
        Route::get('/', 'index')->name('mutasi-lokasi');
        Route::get('/create', 'create')->name('create-mutlokasi');
        Route::get('/get-rak', 'getRakList')->name('get-rak-list');
        Route::get('/get-list-roll', 'getListroll')->name('get-list-roll');
        Route::get('/get-sum-roll', 'getSumroll')->name('get-sum-roll');
        Route::post('/store', 'store')->name('store-mutlokasi');
        Route::get('/approve-mutlok', 'approvemutlok')->name('approve-mutlok');
        Route::get('/edit-mutlok/{id?}', 'editmutlok')->name('edit-mutlok');
        Route::get('/update-mutlokasi', 'updatemutlok')->name('update-mutlokasi');
    });

    //qc pass
    Route::controller(QcPassController::class)->prefix("qc-pass")->middleware('qc-pass')->group(function () {
        Route::get('/', 'index')->name('qc-pass');
        Route::post('/store', 'store')->name('store-qcpass');
        Route::get('/get-data-item', 'getListItem')->name('get-data-item');
        Route::get('/get-data-item2', 'getListItem2')->name('get-data-item2');
        Route::get('/get-defect', 'getdefect')->name('get-defect');
        Route::get('/create-qcpass/{id?}', 'create')->name('create-qcpass');
        Route::post('/store-defect', 'storedefect')->name('store-defect');
        Route::post('/store-qcdet-temp', 'storeQcTemp')->name('store-qcdet-temp');
        Route::post('/store-qcdet-save', 'storeQcSave')->name('store-qcdet-save');
        Route::get('/get-detail-defect', 'getDetailList')->name('get-detail-defect');
        Route::get('/get-sum-data', 'getDataSum')->name('get-sum-data');
        Route::get('/get-avg-poin', 'getavgpoin')->name('get-avg-poin');
        Route::get('/get-poin', 'getpoin')->name('get-poin');
        Route::get('/finish-data', 'finishdata')->name('finish-data');
        Route::get('/finish-data-modal', 'finishdatamodal')->name('finish-data-modal');
        Route::get('/get_data_detailqc', 'getdatadetailqc')->name('get_data_detailqc');
        Route::get('/delete-qc-temp', 'deleteqctemp')->name('delete-qc-temp');
        Route::get('/show-qcpass/{id?}', 'showdata')->name('show-qcpass');
        Route::get('/export-qcpass/{id?}', 'exportdata')->name('export-qcpass');
        Route::get('/get-no-form', 'getnoform')->name('get-no-form');
        Route::get('/delete-qc-det', 'deleteqcdet')->name('delete-qc-det');
    });
});

// Dashboard
Route::get('/dashboard-marker', function () {
    return view('dashboard', ['page' => 'dashboard-marker']);
})->middleware('auth')->name('dashboard-marker');

Route::get('/dashboard-cutting', function () {
    return view('dashboard', ['page' => 'dashboard-cutting']);
})->middleware('auth')->name('dashboard-cutting');

Route::get('/dashboard-stocker', function () {
    return view('dashboard', ['page' => 'dashboard-stocker']);
})->middleware('auth')->name('dashboard-stocker');

//warehouse
Route::get('/dashboard-warehouse', function () {
    return view('dashboard', ['page' => 'dashboard-warehouse']);
})->middleware('auth')->name('dashboard-warehouse');

Route::get('/dashboard-dc', [DashboardController::class, 'dc'])->middleware('auth')->name('dashboard-dc');

Route::get('/dashboard-mut-karyawan', function () {
    return view('dashboard', ['page' => 'dashboard-mut-karyawan']);
})->middleware('auth')->name('dashboard-mut-karyawan');

Route::get('/dashboard-mut-mesin', function () {
    return view('dashboard-mesin', ['page' => 'dashboard-mut-mesin']);
})->middleware('auth')->name('dashboard-mut-mesin');



// Misc
Route::get('/timer', function () {
    return view('example.timeout');
})->middleware('auth');

Route::get('/widgets', function () {
    return view('component.widgets');
})->middleware('auth');

Route::get('/kanban', function () {
    return view('component.kanban');
})->middleware('auth');

Route::get('/gallery', function () {
    return view('component.gallery');
})->middleware('auth');

Route::get('/calendar', function () {
    return view('component.calendar');
})->middleware('auth');

Route::get('/timeline', function () {
    return view('component.UI.timeline');
})->middleware('auth');

Route::get('/sliders', function () {
    return view('component.UI.sliders');
})->middleware('auth');

Route::get('/modals', function () {
    return view('component.UI.modals');
})->middleware('auth');

Route::get('/ribbons', function () {
    return view('component.UI.ribbons');
})->middleware('auth');

Route::get('/general', function () {
    return view('component.UI.general');
})->middleware('auth');

Route::get('/datatable', function () {
    return view('component.tables.data');
})->middleware('auth');

Route::get('/jsgrid', function () {
    return view('component.tables.jsgrid');
})->middleware('auth');

Route::get('/simpletable', function () {
    return view('component.tables.simple');
})->middleware('auth');

Route::get('/advanced-form', function () {
    return view('component.forms.advanced');
})->middleware('auth');

Route::get('/general-form', function () {
    return view('component.forms.general');
})->middleware('auth');

Route::get('/validation-form', function () {
    return view('component.forms.validation');
})->middleware('auth');

Route::get('/bon-mutasi', function () {
    return view('bon-mutasi');
})->middleware('auth');
