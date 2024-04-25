@extends('layouts.index')

@section('custom-link')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="card card-info">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center ">
                <h5 class="card-title fw-bold mb-0"><i class="fas fa-shirt"></i> Input Transfer Garment</h5>
                <a href="{{ route('transfer-garment') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-reply"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row justify-content-center align-items-end">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label>Line</label>
                        <select class="form-control select2bs4" id="cbolok" name="cbolok" style="width: 100%;"
                            onchange="showlok()">
                            <option selected="selected" value="" disabled="true">Pilih Line</option>
                            @foreach ($data_line as $dataline)
                                <option value="{{ $dataline->isi }}">
                                    {{ $dataline->tampil }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-list"></i> List Garment</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable_tmp" class="table table-bordered table-sm w-100">
                                <thead>
                                    <tr>
                                        <th>No. Karton</th>
                                        <th>Brand</th>
                                        <th>Style</th>
                                        <th>Grade</th>
                                        <th>WS</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Qty</th>
                                        <th>Act</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="p-2 bd-highlight">
                                <a class="btn btn-outline-warning" onclick="undo()">
                                    <i class="fas fa-sync-alt
                                    fa-spin"></i>
                                    Undo
                                </a>
                            </div>
                            <div class="p-2 bd-highlight">
                                <a class="btn btn-outline-success" onclick="simpan()">
                                    <i class="fas fa-check"></i>
                                    Simpan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('custom-script')
    <!-- DataTables & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        // Select2 Autofocus
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        // Initialize Select2 Elements
        $('.select2').select2();

        // Initialize Select2BS4 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4',
        });
    </script>
    <script>
        function notif() {
            alert("Maaf, Fitur belum tersedia!");
        }

        $(document).ready(function() {
            $("#cbolok").val('').trigger('change');
            $("#cbobuyer").val('').trigger('change');
            $("#cbosumber").val('').trigger('change');
            dataTableReload();
            cleardet();
            $('input[type=number]').on('wheel', function(e) {
                return false;
            });
        })

        function cleardet() {
            // document.getElementById('txtno_carton').value = "";
            document.getElementById('txtqty').value = "";
            $("#cboproduct").val('').trigger('change');

        }

        function getno_ws() {
            let cbobuyer = document.form_h.cbobuyer.value;
            let html = $.ajax({
                type: "GET",
                url: '{{ route('getno_ws') }}',
                data: {
                    cbobuyer: cbobuyer
                },
                async: false
            }).responseText;
            // console.log(html != "");
            if (html != "") {
                $("#cbows").html(html);
                // $("#cbomarker").prop("disabled", false);
                // $("#txtqtyply").prop("readonly", false);
            }
        };

        function getcolor() {
            let cbows = document.form_h.cbows.value;
            let html = $.ajax({
                type: "GET",
                url: '{{ route('getcolor') }}',
                data: {
                    cbows: cbows
                },
                async: false
            }).responseText;
            if (html != "") {
                $("#cbocolor").html(html);
            }
        };

        function getsize() {
            let cbows = document.form_h.cbows.value;
            let cbocolor = document.form_h.cbocolor.value;
            let html = $.ajax({
                type: "GET",
                url: '{{ route('getsize') }}',
                data: {
                    cbows: cbows,
                    cbocolor: cbocolor
                },
                async: false
            }).responseText;
            if (html != "") {
                $("#cbosize").html(html);
            }
        };

        function getproduct() {
            let cbobuyer = document.form_h.cbobuyer.value;
            let cbows = document.form_h.cbows.value;
            let cbocolor = document.form_h.cbocolor.value;
            let cbosize = document.form_h.cbosize.value;
            let html = $.ajax({
                type: "GET",
                url: '{{ route('getproduct') }}',
                data: {
                    cbobuyer: cbobuyer,
                    cbows: cbows,
                    cbocolor: cbocolor,
                    cbosize: cbosize
                },
                async: false
            }).responseText;
            if (html != "") {
                $("#cboproduct").html(html);
            }
        };

        function tambah_data() {
            let cboproduct = document.form_d.cboproduct.value;
            let qty = document.form_d.txtqty.value;
            let no_carton = document.form_d.txtno_carton.value;
            let grade = document.form_d.cbograde.value;
            $.ajax({
                type: "post",
                url: '{{ route('store_tmp') }}',
                data: {
                    cboproduct: cboproduct,
                    qty: qty,
                    no_carton: no_carton,
                    grade: grade
                },
                success: function(response) {
                    if (response.icon == 'salah') {
                        iziToast.warning({
                            message: response.msg,
                            position: 'topCenter'
                        });
                    } else {
                        iziToast.success({
                            message: response.msg,
                            position: 'topCenter'
                        });
                    }
                    dataTableReload();
                    cleardet();
                },
                // error: function(request, status, error) {
                //     alert(request.responseText);
                // },
            });
        };

        function dataTableReload() {
            let datatable = $("#datatable_tmp").DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                paging: false,
                destroy: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('show_tmp') }}',
                    dataType: 'json',
                    dataSrc: 'data',
                    data: function(d) {
                        d.id = $('#id').val();
                    },
                },
                columns: [{
                        data: 'no_carton',
                    },
                    {
                        data: 'brand',
                    },
                    {
                        data: 'styleno',
                    },
                    {
                        data: 'grade',
                    },
                    {
                        data: 'ws',
                    },
                    {
                        data: 'color',
                    },
                    {
                        data: 'size',
                    },
                    {
                        data: 'qty',
                    },
                    {
                        data: 'id',
                    },
                ],
                columnDefs: [{
                    targets: [8],
                    render: (data, type, row, meta) => {
                        return `
                            <div class='d-flex gap-1 justify-content-center'>
                            <a class='btn btn-warning btn-sm' onclick='notif()'><i class='fas fa-edit'></i></a>
                            <a class='btn btn-danger btn-sm'  onclick="hapus('` + row.id + `');"><i class='fas fa-trash'></i></a>
                                </div>`;
                    }
                }, ]
            });
        }

        // <a class='btn btn-warning btn-sm' href='{{ route('create-dc-in') }}/` +
    //                     row.id_so_det +
    //                     `' data-bs-toggle='tooltip'><i class='fas fa-edit'></i></a>
        // <a class='btn btn-danger btn-sm' href='{{ route('create-dc-in') }}/` +
    //                     row.id_so_det +
    //                     `' data-bs-toggle='tooltip'><i class='fas fa-trash'></i></a>


        function simpan() {
            let tgl_terima = document.form_h.tgl_terima.value;
            let cbolok = document.form_h.cbolok.value;
            let cbosumber = document.form_h.cbosumber.value;

            if (cbolok == '') {
                iziToast.warning({
                    message: 'Lokasi masih kosong, Silahkan pilih lokasi',
                    position: 'topCenter'
                });
            }
            if (cbosumber == '') {
                iziToast.warning({
                    message: 'Sumber Penerimaan masih kosong, Silahkan pilih sumber penerimaan',
                    position: 'topCenter'
                });
            }
            if (cbolok != '' && cbosumber != '') {
                $.ajax({
                    type: "post",
                    url: '{{ route('store-bpb-fg-stock') }}',
                    data: {
                        tgl_terima: tgl_terima,
                        cbolok: cbolok,
                        cbosumber: cbosumber
                    },
                    success: function(response) {
                        if (response.icon == 'salah') {
                            iziToast.warning({
                                message: response.msg,
                                position: 'topCenter'
                            });
                        } else {
                            Swal.fire({
                                text: response.msg,
                                icon: "success"
                            });
                        }
                        dataTableReload();
                        $("#cbolok").val('').trigger('change');
                        $("#cbobuyer").val('').trigger('change');
                        $("#cbosumber").val('').trigger('change');
                        dataTableReload();
                        cleardet();
                    },
                    error: function(request, status, error) {
                        iziToast.warning({
                            message: 'Data Temporary Kosong cek lagi',
                            position: 'topCenter'
                        });
                    },
                });
            }
        };

        function undo() {
            let user = document.form_h.user.value;
            $.ajax({
                type: "post",
                url: '{{ route('undo') }}',
                data: {
                    user: user
                },
                success: function(response) {
                    if (response.icon == 'salah') {
                        iziToast.warning({
                            message: response.msg,
                            position: 'topCenter'
                        });
                    } else {
                        iziToast.success({
                            message: response.msg,
                            position: 'topCenter'
                        });
                    }
                    dataTableReload();
                },
                // error: function(request, status, error) {
                //     alert(request.responseText);
                // },
            });
        };

        function showlok() {
            let datatable = $("#datatable_karton").DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                paging: false,
                destroy: true,
                info: false,
                searching: true,
                "dom": 'ftip',
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('show_lok') }}',
                    dataType: 'json',
                    dataSrc: 'data',
                    data: function(d) {
                        d.cbolok = $('#cbolok').val();
                    },
                },
                columns: [{
                        data: 'lokasi',
                    },
                    {
                        data: 'no_carton',
                    },
                    {
                        data: 'qty_akhir',
                    },
                    {
                        data: 'lokasi',
                    },
                ],
                columnDefs: [{
                    targets: [3],
                    render: (data, type, row, meta) => {
                        return `
                        <div class='d-flex gap-1 justify-content-center'>
                        <a class='btn btn-info btn-sm' data-bs-toggle="modal" data-bs-target="#exampleModal"
                        onclick="getdetail('` + row.lokasi + `','` + row.no_carton + `');"><i class='fas fa-search'></i></a>
                            </div>`;
                    }
                }, ]
            });
        }

        function getdetail(id_l, id_k) {
            document.getElementById('id_l').value = id_l;
            document.getElementById('id_k').value = id_k;
            let datatable = $("#datatable_modal").DataTable({

                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api(),
                        data;

                    // converting to interger to find total
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    // computing column Total of the complete result
                    var sumTotal = api
                        .column(8)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer by showing the total with the reference of the column index
                    $(api.column(0).footer()).html('Total');
                    $(api.column(8).footer()).html(sumTotal);
                },

                ordering: false,
                processing: true,
                serverSide: true,
                paging: false,
                destroy: true,
                info: false,
                searching: true,
                "dom": 'ftip',
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('getdet_carton') }}',
                    dataType: 'json',
                    dataSrc: 'data',
                    data: function(d) {
                        d.lokasi = id_l,
                            d.karton = id_k;
                    },
                },
                columns: [{
                        data: 'buyer',
                    }, {
                        data: 'brand',
                    },
                    {
                        data: 'styleno',
                    },
                    {
                        data: 'grade',
                    },
                    {
                        data: 'ws',
                    },
                    {
                        data: 'color',
                    },
                    {
                        data: 'styleno',
                    },
                    {
                        data: 'size',
                    },
                    {
                        data: 'saldo',
                    },
                ],
            });
        }

        function hapus(id) {
            $.ajax({
                type: "post",
                url: '{{ route('hapus-data-temp-bpb-fg-stok') }}',
                data: {
                    id: id
                },
                success: async function(res) {
                    iziToast.success({
                        message: 'Data Berhasil Dihapus',
                        position: 'topCenter'
                    });
                    $('#datatable-modal').DataTable().ajax.reload();
                    dataTableReload();
                }
            });

        }
    </script>
@endsection
