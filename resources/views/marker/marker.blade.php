@extends('layouts.index')

@section('custom-link')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-sb text-light">
                    <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="edit_id" name="edit_id">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small>Tgl. Cutting</small></label>
                                <input type="text" class="form-control" id="edit_no_form" name="edit_no_form"
                                    value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small>Tgl Form</small></label>
                                <input type="text" class="form-control" id="edit_tgl_form_cut" name="edit_tgl_form_cut"
                                    value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small>WS</small></label>
                                <input type="text" class="form-control" id="edit_ws" name="edit_ws" value=""
                                    readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small>Color</small></label>
                                <input type="text" class="form-control" id="edit_color" name="edit_color" value=""
                                    readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small>Panel</small></label>
                                <input type="text" class="form-control" id="edit_panel" name="edit_panel" value=""
                                    readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small>No. Meja</small></label>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-sb">Simpan</button>
                </div>
            </div>
        </div>
    </div>


    <div class="card card-sb card-outline">
        <div class="card-header">
            <h5 class="card-title fw-bold mb-0">Data Marker</h5>
        </div>
        <div class="card-body">
            <a href="{{ route('create-marker') }}" class="btn btn-primary btn-sm mb-3">
                <i class="fas fa-plus"></i>
                Baru
            </a>
            <div class="d-flex align-items-end gap-3 mb-3">
                <div class="mb-3">
                    <label class="form-label"><small>Tgl Awal</small></label>
                    <input type="date" class="form-control form-control-sm" id="tgl-awal" name="tgl_awal"
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label"><small>Tgl Akhir</small></label>
                    <input type="date" class="form-control form-control-sm" id="tgl-akhir" name="tgl_akhir"
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary btn-sm" onclick="filterTable()">Tampilkan</button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered table-striped table-sm w-100">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No. Marker</th>
                            <th>No. WS</th>
                            <th>Color</th>
                            <th>Panel</th>
                            <th>Panjang Marker</th>
                            {{-- <th>Comma Marker</th> --}}
                            <th>Lebar Marker</th>
                            <th>Gelar QTYs</th>
                            <th>PO</th>
                            <th>Urutan</th>
                            <th>Act</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('custom-script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        let datatable = $("#datatable").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            paging: false,
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('marker') }}',
                dataType: 'json',
                dataSrc: 'data',
                data: function(d) {
                    d.tgl_awal = $('#tgl-awal').val();
                    d.tgl_akhir = $('#tgl-akhir').val();
                },
            },
            columns: [{
                    data: 'tgl_cutting'
                },
                {
                    data: 'kode'
                },
                {
                    data: 'act_costing_ws'
                },
                {
                    data: 'color'
                },
                {
                    data: 'panel'
                },
                {
                    data: 'panjang_marker_fix'
                },
                // {
                //     data: 'comma_marker'
                // },
                {
                    data: 'lebar_marker'
                },
                {
                    data: 'gelar_qty'
                },
                {
                    data: 'po_marker'
                },
                {
                    data: 'urutan_marker'
                },
                {
                    data: 'id'
                },
                // {
                //     data: 'id'
                // }
            ],
            columnDefs: [
                // {
                //     targets: [11],
                //     render: (data, type, row, meta) => "<button class='btn btn-sm btn-primary' onclick=''>Edit</button>"
                // },
                {
                    targets: [10],
                    render: (data, type, row, meta) => {
                        // return `<div class='d-flex gap-1 justify-content-center'><a class='btn btn-primary btn-sm' href='{{ route('show-marker') }}&` +
                        //     row.id +
                        //     `' data-bs-toggle='tooltip' target='_blank'><i class='fa fa-search'></i></a></div>`;
                        return `<div class='d-flex gap-1 justify-content-center'><a class='btn btn-primary btn-sm'
                        data-bs-toggle="modal" data-bs-target="#exampleModal" onclick='getdetail();'><i class='fa fa-search'></i></a></div>`;
                        // "<a href='javascript:void(0);' class='btn btn-primary btn-sm' onclick='editData(" +
                        // JSON.stringify(row) +
                        //     ", \"ExampleModal\", [])'><i class='fa fa-pen'></i></a>": "";
                    }
                }
            ]
        });

        function filterTable() {
            datatable.ajax.reload();
        }

        function getdetail() {

            $("#exampleModalLabel").html('Marker Detail');

        };
    </script>
@endsection