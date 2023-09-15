@extends('layouts.index')

@section('content')
    <div class="row g-3">
        <div class="d-flex gap-3 align-items-center">
            <h5 class="mb-1">Form Cut</h5>
            <button class="btn btn-sm btn-success" id="start-process" onclick="startProcess()">Mulai Pengerjaan</button>
        </div>
        <div class="col-md-6">
            <div class="card card-sb" id="header-data-card">
                <div class="card-header">
                    <h3 class="card-title">Header Data</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    @php
                        $thisActCosting = $actCostingData->where('id', $formCutInputData->act_costing_id)->first();
                        $thisMarkerDetails = $markerDetailData->where('kode_marker', $formCutInputData->id_marker);
                    @endphp
                    <div class="row">
                        <input type="hidden" name="id" id="id" value="{{ $id }}" readonly>
                        <input type="hidden" name="act_costing_id" id="act_costing_id" value="{{ $formCutInputData->act_costing_id }}" readonly>
                        <input type="hidden" name="status" id="status" value="{{ $formCutInputData->status }}" readonly>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Start</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="start" id="start-time" value="{{ $formCutInputData->waktu_mulai }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Finish</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="finish" id="finish-time" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Shell</b></small></label>
                                <select class="form-select form-select-sm" name="shell" id="shell">
                                    <option value="">-</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>No. Form</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="no_form" id="no_form" value="{{ $formCutInputData->no_form }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Tanggal</b></small></label>
                                <input type="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Kode Marker</b></small></label>
                                <input type="text" class="form-control form-control-sm" value="{{ $formCutInputData->id_marker }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>No. WS</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="no_ws" value="{{ $formCutInputData->act_costing_ws }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Buyer</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="buyer" value="{{ $thisActCosting->buyer }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Style</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="style" value="{{ $thisActCosting->style }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Color</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="color" id="color" value="{{ $formCutInputData->color }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Panel</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="panel" id="panel" value="{{ $formCutInputData->panel }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>PO</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="po" value="{{ $formCutInputData->po_marker }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>QTY Gelar Marker</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="gelar_qty" value="{{ $formCutInputData->gelar_qty }}" readonly>
                            </div>
                        </div>
                    </div>
                    <table id="ratio-datatable" class="table table-striped table-sm w-100 text-center mt-3">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th>Ratio</th>
                                <th>Qty Cut Marker</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalRatio = 0;
                                $totalCutQty = 0;
                            @endphp
                            @foreach ($thisMarkerDetails as $item)
                                <tr>
                                    @php
                                        $totalRatio += $item->ratio;
                                        $totalCutQty += $item->cut_qty;
                                    @endphp
                                    <td>{{ $soDetData->where('id', $item->so_det_id)->first()->size }}</td>
                                    <td>{{ $item->ratio }}</td>
                                    <td>{{ $item->cut_qty }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th id="totalRatio">{{ $totalRatio }}</th>
                                <th id="totalQtyCutMarker">{{ $totalCutQty }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <button class="btn btn-sb mb-3 d-none" id="next-process-1" onclick="nextProcessOne()">NEXT</button>
            <div class="card card-sb d-none" id="detail-data-card">
                <div class="card-header">
                    <h3 class="card-title">Detail Data</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>P. Marker</b></small></label>
                                <input type="number" class="form-control form-control-sm" value="{{ $formCutInputData->panjang_marker }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Unit</b></small></label>
                                <input type="text" class="form-control form-control-sm" value="{{ strtoupper($formCutInputData->unit_panjang_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Comma</b></small></label>
                                <input type="number" class="form-control form-control-sm" value="{{ $formCutInputData->comma_marker }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Unit</b></small></label>
                                <input type="text" class="form-control form-control-sm" value="{{ strtoupper($formCutInputData->unit_comma_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>P. Act</b></small></label>
                                <input type="number" class="form-control form-control-sm" name="p_actual" id="p_actual" value="{{ $formCutInputData->p_act }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Unit Act</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="unit_p_actual" id="unit_p_actual" value="{{ strtoupper($formCutInputData->unit_panjang_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Comma Act</b></small></label>
                                <input type="number" class="form-control form-control-sm" name="comma_actual" id="comma_actual" value="{{ $formCutInputData->p_act }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Unit Act</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="unit_comma_actual" id="unit_comma_actual" value="{{ strtoupper($formCutInputData->unit_comma_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>L. Marker</b></small></label>
                                <input type="number" class="form-control form-control-sm" value="{{ strtoupper($formCutInputData->lebar_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Unit</b></small></label>
                                <input type="text" class="form-control form-control-sm" value="{{ strtoupper($formCutInputData->unit_lebar_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>L. Act</b></small></label>
                                <input type="number" class="form-control form-control-sm" name="l_actual" id="l_actual" value="{{ $formCutInputData->l_act }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Unit Act</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="unit_l_actual" id="unit_l_actual" value="{{ strtoupper($formCutInputData->unit_lebar_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Cons WS</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="cons_ws" id="cons_ws" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Cons Marker</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="cons_marker" id="cons_marker" value="{{ $formCutInputData->cons_marker }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Cons Actual</b></small></label>
                                <input type="number" class="form-control form-control-sm" name="cons_actual" id="cons_actual" value="{{ $formCutInputData->cons_act }}" step=".01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Cons Pipping</b></small></label>
                                <input type="number" class="form-control form-control-sm" step=".01" name="cons_pipping" id="cons_pipping">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Cons 1 Ampar</b></small></label>
                                <input type="number" class="form-control form-control-sm" step=".01" name="cons_ampar" id="cons_ampar">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Est. Kebutuhan Kain Pipping</b></small></label>
                                <div class="row g-1">
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" step=".01" name="est_pipping" id="est_pipping">
                                    </div>
                                    <div class="col-6">
                                        <select class="form-select form-select-sm" name="est_pipping_unit" id="est_pipping_unit">
                                            <option value="meter">METER</option>
                                            <option value="yard">YARD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Est. Kebutuhan Kain</b></small></label>
                                <div class="row g-1">
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" step=".01" name="est_kain" id="est_kain">
                                    </div>
                                    <div class="col-6">
                                        <select class="form-select form-select-sm" name="est_kain_unit" id="est_kain_unit">
                                            <option value="meter">METER</option>
                                            <option value="yard">YARD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <button class="btn btn-sb mb-3 float-end d-none" id="next-process-2" onclick="nextProcessTwo()">NEXT</button>
            <div class="card card-sb d-none" id="scan-qr-card">
                <div class="card-header">
                    <h3 class="card-title">Scan QR</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <div class="row align-items-end">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div id="reader" width="600px"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Kode Barang</b></small></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" name="kode_barang" id="kode_barang">
                                    <button class="btn btn-sm btn-primary" type="button" id="scan-button" onclick="initScan()">Scan</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>ID Item</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="id_item" id="id_item" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Detail Item</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="detail_item" id="detail_item" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Color Actual</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="color_act" id="color_act">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-auto mb-3">
                                <button class="btn btn-sb btn-sm btn-block d-none" id="next-process-3" onclick="nextProcessThree()">START</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card card-sb d-none" id="time-record-card">
                <div class="card-header">
                    <h3 class="card-title">Time Record</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex gap-3 mb-3">
                                <div class="d-flex gap-1">
                                    <input type="text" class="form-control form-control-sm" id="minutes" value="00" readonly class="mx-1">
                                    :
                                    <input type="text" class="form-control form-control-sm" id="seconds" value="00" readonly class="mx-1">
                                </div>
                                <button type="button" class="btn btn-success btn-sm" id="startLapButton" onclick="startTimeRecord()">Start</button>
                                <button type="button" class="btn btn-primary btn-sm" id="nextLapButton" onclick="addNewTimeRecord()">Next Lap</button>
                                <button type="button" class="btn btn-warning btn-sm" id="pauseLapButton" onclick="pauseTimeRecord()">Pause</button>
                                <button type="button" class="btn btn-danger btn-sm" id="stopLapButton" onclick="stopTimeRecord()">Stop</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="w-100 table-responsive">
                                <form id="time-record-lap-form">
                                    <table class="table table-bordered table-sm" id="timeRecordTable">
                                        <thead>
                                            <tr>
                                                <th>Lap</th>
                                                <th>Waktu</th>
                                                <th class="d-none"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="w-100 table-responsive">
                                <table class="table table-bordered table-sm" id="scannedItemTable">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Group</th>
                                            <th>Lot</th>
                                            <th>Roll</th>
                                            <th>Qty</th>
                                            <th>Unit</th>
                                            <th>Lap</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-script')
    <script>
        var id = document.getElementById("id").value;
        var status = document.getElementById("status").value;
        var startProcessButton = document.getElementById("start-process");
        var nextProcessOneButton = document.getElementById("next-process-1");
        var nextProcessTwoButton = document.getElementById("next-process-2");
        var nextProcessThreeButton = document.getElementById("next-process-3");
        var startTime = document.getElementById("start-time");
        var finishTime = document.getElementById("finish-time");

        $(document).ready(() => {
            checkStatus();

            getNumberData();

            if (startTime.value == "" || startTime.value == null) {
                startTime.value = "";
            }

            if (finishTime.value == "" || finishTime.value == null) {
                finishTime.value = ""
            }

            document.getElementById('kode_barang').value = "";
            document.getElementById("id_item").value = "";
            document.getElementById("detail_item").value = "";
        })

        function pad2(n) {
            return n < 10 ? '0' + n : n
        }

        function checkIfNull(value) {
            if (value == "" || value == null) {
                return false
            }

            return true;
        }

        function checkStatus() {
            if (status == "PENGERJAAN FORM CUTTING") {
                startProcessButton.classList.add("d-none");
                nextProcessOneButton.classList.remove("d-none");
            }

            if (status == "PENGERJAAN FORM CUTTING DETAIL") {
                startProcessButton.classList.add("d-none");
                nextProcessOneButton.classList.add("d-none");

                $('#header-data-card').CardWidget('collapse');
                $('#detail-data-card').removeClass('d-none');
                nextProcessTwoButton.classList.remove("d-none");
            }

            if (status == "PENGERJAAN FORM CUTTING SPREAD") {
                startProcessButton.classList.add("d-none");
                nextProcessOneButton.classList.add("d-none");
                nextProcessTwoButton.classList.add("d-none");
                nextProcessThreeButton.classList.remove("d-none");

                $('#header-data-card').CardWidget('collapse');
                $('#detail-data-card').removeClass('d-none');
                $('#detail-data-card').CardWidget('collapse');
                $('#detail-data-card').removeClass('d-none');
                $('#scan-qr-card').removeClass('d-none');

                initScan();
            }

            if (status == "PENGERJAAN FORM CUTTING SPREAD TIME") {
                startProcessButton.classList.add("d-none");
                nextProcessOneButton.classList.add("d-none");
                nextProcessTwoButton.classList.add("d-none");
                nextProcessThreeButton.classList.add("d-none");

                $('#header-data-card').CardWidget('collapse');
                $('#detail-data-card').removeClass('d-none');
                $('#detail-data-card').CardWidget('collapse');
                $('#scan-qr-card').removeClass('d-none');
            }
        }

        function startProcess() {
            let now = new Date();
            startTime.value = now.getFullYear().toString() + "-" + pad2(now.getMonth() + 1) + "-" + pad2( now.getDate()) + "-" + pad2( now.getHours() ) + ":" + pad2( now.getMinutes() ) + ":" + pad2( now.getSeconds() );

            updateToStartProcess();

            startProcessButton.classList.add("d-none");
            nextProcessOneButton.classList.remove("d-none");
        }

        function nextProcessOne() {
            updateToNextProcessOne();

            $('#header-data-card').CardWidget('collapse');
            $('#detail-data-card').removeClass('d-none');
            nextProcessOneButton.classList.add("d-none");
            nextProcessTwoButton.classList.remove("d-none");
        }

        function nextProcessTwo() {
            updateToNextProcessTwo();
        }

        function nextProcessThree() {
            updateToNextProcessThree();
        }

        function getNumberData() {
            return $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/form-cut-input/get-number-data/',
                type: 'get',
                data: {
                    act_costing_id: $("#act_costing_id").val(),
                    color: $("#color").val(),
                    panel: $("#panel").val(),
                },
                dataType: 'json',
                success: function (res) {
                    if (res) {
                        document.getElementById('cons_ws').value = res.cons_ws
                    }
                }
            });
        }

        function updateToStartProcess() {
            return $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/form-cut-input/start-process/'+id,
                type: 'put',
                dataType: 'json',
                data: {
                    startTime: startTime.value,
                },
                success: function (res) {
                    if (res) {
                        console.log(res);
                    }
                }
            });
        }

        function updateToNextProcessOne() {
            return $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/form-cut-input/next-process-one/'+id,
                type: 'put',
                dataType: 'json',
                success: function (res) {
                    if (res) {
                        console.log(res);
                    }
                }
            });
        }

        function updateToNextProcessTwo() {
            let pActual = document.getElementById('p_actual').value;
            let pUnitActual = document.getElementById('unit_p_actual').value;
            let commaActual = document.getElementById('comma_actual').value;
            let commaUnitActual = document.getElementById('unit_comma_actual').value;
            let lActual = document.getElementById('l_actual').value;
            let lUnitActual = document.getElementById('unit_l_actual').value;
            let consActual = document.getElementById('cons_actual').value;
            let consPipping = document.getElementById('cons_pipping').value;
            let consAmpar = document.getElementById('cons_ampar').value;
            let estPipping = document.getElementById('est_pipping').value;
            let estPippingUnit = document.getElementById('est_pipping_unit').value;
            let estKain = document.getElementById('est_kain').value;
            let estKainUnit = document.getElementById('est_kain_unit').value;

            clearModified();

            return $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/form-cut-input/next-process-two/'+id,
                type: 'put',
                dataType: 'json',
                data: {
                    p_actual: pActual,
                    unit_p_actual: pUnitActual,
                    comma_actual: commaActual,
                    unit_comma_actual: commaUnitActual,
                    l_actual: lActual,
                    unit_l_actual: lUnitActual,
                    cons_actual: consActual,
                    cons_pipping: consPipping,
                    cons_ampar: consAmpar,
                    est_pipping: estPipping,
                    est_pipping_unit: estPippingUnit,
                    est_kain: estKain,
                    est_kain_unit: estKainUnit,
                },
                success: function (res) {
                    if (res) {
                        console.log(res.message);

                        if (res.status == 200) {
                            $('#header-data-card').CardWidget('collapse');
                            $('#detail-data-card').CardWidget('collapse');
                            $('#scan-qr-card').removeClass('d-none');

                            nextProcessTwoButton.classList.add("d-none");
                            nextProcessThreeButton.classList.remove("d-none");
                            initScan();
                        }
                    }
                }, error: function (jqXHR) {
                    let res = jqXHR.responseJSON;
                    let message = '';
                    let i = 0;

                    for (let key in res.errors) {
                        message = res.errors[key];
                        document.getElementById(key).classList.add('is-invalid');
                        modified.push(
                            [key, '.classList', '.remove(', "'is-invalid')"],
                        )

                        if (i == 0) {
                            document.getElementById(key).focus();
                            i++;
                        }
                    };
                }
            });
        }

        function updateToNextProcessThree() {
            if (checkIfNull(document.getElementById("id_item").value) && checkIfNull(document.getElementById("detail_item").value) && checkIfNull(document.getElementById("color_act").value) && currentScannedItem) {
                nextProcessThreeButton.classList.add("d-none");
                $('#time-record-card').removeClass('d-none');

                startLapButton.disabled = false;
                nextLapButton.disabled = false;
                pauseLapButton.disabled = false;
                stopLapButton.disabled = true;

                startLapButton.focus();

                appendScannedItem(currentScannedItem);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Pastikan item yang di scan tersedia dan color actual sudah diisi',
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonText: 'Oke',
                })
            }
        }

        var currentScannedItem = null;
        // Scan QR
        function initScan() {
            if (document.getElementById("reader")) {
                function onScanSuccess(decodedText, decodedResult) {
                    // handle the scanned code as you like, for example:
                    console.log(`Code matched = ${decodedText}`, decodedResult);

                    // store to input text
                    let breakDecodedText = decodedText.split('-');

                    document.getElementById('kode_barang').value = breakDecodedText[0];

                    getScannedItem(breakDecodedText[0]);

                    html5QrcodeScanner.clear();
                }

                function onScanFailure(error) {
                    // handle scan failure, usually better to ignore and keep scanning.
                    // for example:
                    console.warn(`Code scan error = ${error}`);
                }

                let html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader",
                    { fps: 10, qrbox: {width: 250, height: 250} },
                    /* verbose= */ false);
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            }
        }

        function getScannedItem(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/form-cut-input/get-scanned-item/'+id,
                type: 'get',
                dataType: 'json',
                success: function (res) {
                    if (res) {
                        currentScannedItem = res;

                        document.getElementById("id_item").value = res.id_item;
                        document.getElementById("detail_item").value = res.itemdesc;
                    }
                }
            });
        }

        var scannedItemTable = document.getElementById("scannedItemTable");
        var scannedItemTableTbody = scannedItemTable.getElementsByTagName("tbody")[0];
        var totalScannedItem = 0;

        function appendScannedItem(data) {
            let tr = document.createElement('tr');
            let td1 = document.createElement('td');
            let td2 = document.createElement('td');
            let td3 = document.createElement('td');
            let td4 = document.createElement('td');
            let td5 = document.createElement('td');
            let td6 = document.createElement('td');
            let td7 = document.createElement('td');
            td1.innerHTML = totalScannedItem + 1;
            td2.innerHTML = `<input type='text' class="form-control form-control-sm w-auto" name='group[`+totalScannedItem+`]' id='group-`+totalScannedItem+`'>`;
            td3.innerHTML = data.lot_no;
            td4.innerHTML = data.roll_no;
            td5.innerHTML = data.roll_qty;
            td6.innerHTML = data.unit;
            td7.innerHTML = `<input type='number' class="form-control form-control-sm w-auto" name='lap[`+totalScannedItem+`]' id='lap-`+totalScannedItem+`'>`;
            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            tr.appendChild(td4);
            tr.appendChild(td5);
            tr.appendChild(td6);
            tr.appendChild(td7);

            scannedItemTableTbody.appendChild(tr);

            totalScannedItem++;
        }

        function backToProcessThree() {
            storeTimeRecord();

            $("#kode_barang").val("");
            $("#id_item").val("");
            $("#detail_item").val("");

            initScan();

            startLapButton.disabled = true;
            nextLapButton.disabled = true;
            pauseLapButton.disabled = true;
            stopLapButton.disabled = true;

            nextProcessThreeButton.classList.remove("d-none");
        }

        // function storeTimeRecord() {
        //     return $.ajax({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         url: '/form-cut-input/store-time-record/',
        //         type: 'post',
        //         dataType: 'json',
        //         data: {
        //             no_form_cut_input: $("#no_form").val(),
        //             id_item: $("#id_item").val(),
        //             group: $("#group-"+(totalScannedItem-1)).val(),
        //             lot: currentScannedItem.lot_no,
        //             roll: currentScannedItem.roll_no,
        //             qty: currentScannedItem.roll_qty,
        //             unit: currentScannedItem.unit,
        //             lap: $("#lap-"+(totalScannedItem-1)).val(),
        //         },
        //         success: function (res) {
        //             if (res) {
        //                 console.log(res);
        //             }
        //         }
        //     });
        // }

        var startLapButton = document.getElementById("startLapButton");
        var pauseLapButton = document.getElementById("pauseLapButton");
        var stopLapButton = document.getElementById("stopLapButton");
        var nextLapButton = document.getElementById("nextLapButton");

        var minutes = document.getElementById("minutes");
        var seconds = document.getElementById("seconds");

        var timeRecordTable = document.getElementById('timeRecordTable');
        var timeRecordTableTbody = timeRecordTable.getElementsByTagName("tbody")[0];

        var lap = 0;
        var totalSeconds = 0;
        var timeRecordInterval = 0;

        seconds.value = pad(totalSeconds % 60);
        minutes.value = pad(parseInt(totalSeconds / 60));

        startLapButton.focus()

        function pad(val) {
            var valString = val + "";
            if (valString.length < 2) {
                return "0" + valString;
            } else {
                return valString;
            }
        }

        function setTime() {
            ++totalSeconds;
            seconds.value = pad(totalSeconds % 60);
            minutes.value = pad(parseInt(totalSeconds / 60));
        }

        function startTimeRecord() {
            timeRecordInterval = setInterval(setTime, 1000);

            pauseLapButton.removeAttribute("disabled");
            startLapButton.setAttribute("disabled", true);
            nextLapButton.focus();
        }

        function pauseTimeRecord() {
            clearTimeout(timeRecordInterval);

            pauseLapButton.setAttribute("disabled", true);
            startLapButton.removeAttribute("disabled");
            startLapButton.focus();
        }

        async function stopTimeRecord() {
            clearTimeout(timeRecordInterval);
            totalSeconds = 0;
            timeRecordInterval = 0;

            seconds.value = pad(totalSeconds % 60);
            minutes.value = pad(parseInt(totalSeconds / 60));
            lap = 0;

            startLapButton.removeAttribute("disabled");
            startLapButton.focus();

            await backToProcessThree();

            timeRecordTableTbody.innerHTML = "";
        }

        function addNewTimeRecord() {
            totalSeconds = 0;

            lap++;

            let tr = document.createElement('tr');
            let td1 = document.createElement('td');
            let td2 = document.createElement('td');
            let td3 = document.createElement('td');
            td1.innerHTML = lap;
            td2.innerHTML = minutes.value+' : '+seconds.value;
            td3.classList.add('d-none');
            td3.innerHTML = `<input type='hidden' name="time_record[`+lap+`]" value="`+minutes.value+':'+seconds.value+`" />`;
            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);

            timeRecordTableTbody.appendChild(tr);

            if (document.getElementById("lap-"+(totalScannedItem-1))) {
                document.getElementById("lap-"+(totalScannedItem-1)).value = lap;
            }

            stopLapButton.disabled = false;
        }

        $(document).keyup(function(e) {
            if (e.key === "Backspace") {
                pauseTimeRecord()
            }

            if (e.key === "Escape") {
                stopTimeRecord()
            }
        });
    </script>
@endsection
