<?php

namespace App\Http\Controllers;

use App\Models\MasterPart;
use App\Models\Part;
use App\Models\PartDetail;
use App\Models\PartForm;
use App\Models\FormCutInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class PartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $partQuery = Part::selectRaw("
                    part.id,
                    part.kode,
                    part.buyer,
                    part.act_costing_ws ws,
                    part.style,
                    part.color,
                    part.panel,
                    COUNT(DISTINCT form_cut_input.id) total_form,
                    GROUP_CONCAT(DISTINCT master_part.nama_part ORDER BY master_part.nama_part SEPARATOR ', ') part_details
                ")->
                leftJoin("part_detail", "part_detail.part_id", "=", "part.id")->
                leftJoin("master_part", "master_part.id", "part_detail.master_part_id")->
                leftJoin("part_form", "part_form.part_id", "part.id")->
                leftJoin("form_cut_input", "form_cut_input.id", "part_form.form_id")->
                groupBy("part.id");

            return DataTables::eloquent($partQuery)->
                filterColumn('ws', function ($query, $keyword) {
                    $query->whereRaw("LOWER(act_costing_ws) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('style', function ($query, $keyword) {
                    $query->whereRaw("LOWER(style) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('color', function ($query, $keyword) {
                    $query->whereRaw("LOWER(color) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('panel', function ($query, $keyword) {
                    $query->whereRaw("LOWER(panel) LIKE LOWER('%" . $keyword . "%')");
                })->order(function ($query) {
                    $query->
                        orderBy('part.kode', 'desc')->
                        orderBy('part.updated_at', 'desc');
                })->toJson();
        }

        return view("part.part", ["page" => "dashboard-stocker"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $orders = DB::connection('mysql_sb')->
            table('act_costing')->
            select('id', 'kpno')->
            where('status', '!=', 'CANCEL')->
            where('cost_date', '>=', '2023-01-01')->
            where('type_ws', 'STD')->
            orderBy('cost_date', 'desc')->
            orderBy('kpno', 'asc')->
            groupBy('kpno')->
            get();

        $masterParts = MasterPart::all();

        return view('part.create-part', ['orders' => $orders, 'masterParts' => $masterParts, 'page' => 'dashboard-stocker']);
    }

    public function getOrderInfo(Request $request)
    {
        $order = DB::connection('mysql_sb')->
            table('act_costing')->
            selectRaw('
                act_costing.id,
                act_costing.kpno,
                act_costing.styleno,
                act_costing.qty order_qty,
                mastersupplier.supplier buyer,
                GROUP_CONCAT(DISTINCT so_det.color SEPARATOR ", ") colors
            ')->
            leftJoin('mastersupplier', 'mastersupplier.Id_Supplier', '=', 'act_costing.id_buyer')->
            leftJoin('so', 'so.id_cost', '=', 'act_costing.id')->
            leftJoin('so_det', 'so_det.id_so', '=', 'so.id')->
            where('act_costing.id', $request->act_costing_id)->
            groupBy('act_costing.id')->
            first();

        return json_encode($order);
    }

    public function getColorList(Request $request)
    {
        $colors = DB::connection('mysql_sb')->select("
            select sd.color from so_det sd
            inner join so on sd.id_so = so.id
            inner join act_costing ac on so.id_cost = ac.id
            where ac.id = '" . $request->act_costing_id . "' and sd.cancel = 'N'
            group by sd.color");

        return $colors ? $colors[0] : null;
    }

    public function getPanelList(Request $request)
    {
        $notInclude = "";
        $existParts = Part::where("act_costing_id", $request->act_costing_id)->get();

        if ($existParts->count() > 0){
            $i = 0;
            $notInclude = "not in (";

            foreach ($existParts as $existPart) {
                $notInclude .= ($i == 0 ? "'".$existPart->panel."'" : ", '".$existPart->panel."'");
                $i++;
            }

            $notInclude .= ")";
        }

        $panels = DB::connection('mysql_sb')->select("
                select nama_panel panel from
                    (select id_panel from bom_jo_item k
                        inner join so_det sd on k.id_so_det = sd.id
                        inner join so on sd.id_so = so.id
                        inner join act_costing ac on so.id_cost = ac.id
                        inner join masteritem mi on k.id_item = mi.id_gen
                        where ac.id = '" . $request->act_costing_id . "' and k.status = 'M'
                        ".$notInclude."
                        and k.cancel = 'N' and sd.cancel = 'N' and so.cancel_h = 'N' and ac.status = 'confirm' and mi.mattype = 'F'
                        group by id_panel
                    ) a
                inner join masterpanel mp on a.id_panel = mp.id
            ");

        $html = "<option value=''>Pilih Panel</option>";

        foreach ($panels as $panel) {
            $html .= " <option value='" . $panel->panel . "'>" . $panel->panel . "</option> ";
        }

        return $html;
    }

    public function getPartDetail(Request $request)
    {
        $partDetails =

        $html = "<option value=''>Pilih Panel</option>";

        foreach ($panels as $panel) {
            $html .= " <option value='" . $panel->panel . "'>" . $panel->panel . "</option> ";
        }

        return $html;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $part = Part::select("kode")->orderBy("kode", "desc")->first();
        $partNumber = $part ? intval(substr($part->kode, -5)) + 1 : 1;
        $partCode = 'PRT' . sprintf('%05s', $partNumber);
        $totalPartDetail = intval($request["jumlah_part_detail"]);

        $validatedRequest = $request->validate([
            "ws_id" => "required",
            "ws" => "required",
            "color" => "required",
            "panel" => "required",
            "buyer" => "required",
            "style" => "required",
        ]);

        if ($totalPartDetail > 0) {
            $partStore = Part::create([
                "kode" => $partCode,
                "act_costing_id" => $validatedRequest['ws_id'],
                "act_costing_ws" => $validatedRequest['ws'],
                "color" => $validatedRequest['color'],
                "panel" => $validatedRequest['panel'],
                "buyer" => $validatedRequest['buyer'],
                "style" => $validatedRequest['style'],
            ]);

            $timestamp = Carbon::now();
            $partId = $partStore->id;
            $partDetailData = [];
            for ($i = 0; $i < $totalPartDetail; $i++) {
                array_push($partDetailData, [
                    "part_id" => $partId,
                    "master_part_id" => $request["part_details"][$i],
                    "created_at" => $timestamp,
                    "updated_at" => $timestamp,
                ]);
            }

            $partDetailStore = PartDetail::insert($partDetailData);

            return array(
                "status" => 200,
                "message" => $partCode,
                "additional" => [],
                "redirect" => route('manage-part-form', ["id" => $partStore->id])
            );
        }

        return array(
            "status" => 400,
            "message" => "Harap pilih part",
            "additional" => []
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function show(Part $part)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function edit(Part $part)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Part $part, $id = 0)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function destroy(Part $part, $id = 0)
    {
        $countPartForm = PartForm::where("part_id", $id)->count();

        if ($countPartForm < 1) {
            $deletePart = Part::where("id", $id)->delete();

            if ($deletePart) {
                return array(
                    'status' => 200,
                    'message' => 'Part berhasil dihapus',
                    'redirect' => '',
                    'table' => 'datatable-part',
                    'additional' => [],
                );
            }
        }

        return array(
            'status' => 400,
            'message' => 'Part ini tidak dapat dihapus',
            'redirect' => '',
            'table' => 'datatable-part',
            'additional' => [],
        );
    }

    public function managePartForm(Request $request, $id = 0) {
        if ($request->ajax()) {
            $formCutInputs = FormCutInput::selectRaw("
                    form_cut_input.id,
                    form_cut_input.id_marker,
                    form_cut_input.no_form,
                    form_cut_input.tgl_form_cut,
                    users.name nama_meja,
                    marker_input.act_costing_ws,
                    marker_input.buyer,
                    marker_input.urutan_marker,
                    marker_input.style,
                    marker_input.color,
                    marker_input.panel,
                    GROUP_CONCAT(DISTINCT CONCAT(master_size_new.size, '(', marker_input_detail.ratio, ')') SEPARATOR ', ') marker_details,
                    form_cut_input.qty_ply,
                    form_cut_input.no_cut
                ")->
                leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.id_marker")->
                leftJoin("marker_input_detail", "marker_input_detail.marker_id", "=", "marker_input.id")->
                leftJoin("master_size_new", "master_size_new.size", "=", "marker_input_detail.size")->
                leftJoin("users", "users.id", "=", "form_cut_input.no_meja")->
                leftJoin("part_form", "part_form.form_id", "=", "form_cut_input.id")->
                where("form_cut_input.status", "SELESAI PENGERJAAN")->
                whereRaw("part_form.id is not null")->
                where("part_form.part_id", $id)->
                where("marker_input.act_costing_ws", $request->act_costing_ws)->
                where("marker_input.panel", $request->panel)->
                groupBy("form_cut_input.id");

                return Datatables::eloquent($formCutInputs)->
                    filterColumn('act_costing_ws', function ($query, $keyword) {
                        $query->whereRaw("LOWER(act_costing_ws) LIKE LOWER('%" . $keyword . "%')");
                    })->filterColumn('buyer', function ($query, $keyword) {
                        $query->whereRaw("LOWER(buyer) LIKE LOWER('%" . $keyword . "%')");
                    })->filterColumn('style', function ($query, $keyword) {
                        $query->whereRaw("LOWER(style) LIKE LOWER('%" . $keyword . "%')");
                    })->filterColumn('color', function ($query, $keyword) {
                        $query->whereRaw("LOWER(color) LIKE LOWER('%" . $keyword . "%')");
                    })->filterColumn('panel', function ($query, $keyword) {
                        $query->whereRaw("LOWER(panel) LIKE LOWER('%" . $keyword . "%')");
                    })->filterColumn('nama_meja', function ($query, $keyword) {
                        $query->whereRaw("LOWER(users.name) LIKE LOWER('%" . $keyword . "%')");
                    })->order(function ($query) {
                        $query->
                            orderBy('form_cut_input.waktu_selesai', 'desc')->
                            orderByRaw('FIELD(form_cut_input.tipe_form_cut, null, "NORMAL", "MANUAL")');
                    })->toJson();
        }

        $part = Part::selectRaw("
                part.id,
                part.kode,
                part.buyer,
                part.act_costing_ws,
                part.style,
                part.color,
                part.panel,
                GROUP_CONCAT(master_part.nama_part ORDER BY master_part.nama_part SEPARATOR ', ') part_details
            ")->
            leftJoin("part_detail", "part_detail.part_id", "=", "part.id")->
            leftJoin("master_part", "master_part.id", "part_detail.master_part_id")->
            where("part.id", $id)->
            groupBy("part.id")->
            first();

        return view("part.manage-part-form", ["part" => $part, "page" => "dashboard-stocker"]);
    }

    public function getFormCut(Request $request, $id = 0) {
        $formCutInputs = FormCutInput::selectRaw("
                form_cut_input.id,
                form_cut_input.id_marker,
                form_cut_input.no_form,
                form_cut_input.tgl_form_cut,
                users.name nama_meja,
                marker_input.act_costing_ws ws,
                marker_input.buyer,
                marker_input.urutan_marker,
                marker_input.color,
                marker_input.style,
                marker_input.panel,
                GROUP_CONCAT(DISTINCT CONCAT(master_size_new.size, '(', marker_input_detail.ratio, ')') SEPARATOR ', ') marker_details,
                form_cut_input.qty_ply,
                form_cut_input.no_cut
            ")->
            leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.id_marker")->
            leftJoin("marker_input_detail", "marker_input_detail.marker_id", "=", "marker_input.id")->
            leftJoin("master_size_new", "master_size_new.size", "=", "marker_input_detail.size")->
            leftJoin("users", "users.id", "=", "form_cut_input.no_meja")->
            leftJoin("part_form", "part_form.form_id", "=", "form_cut_input.id")->
            where("form_cut_input.status", "SELESAI PENGERJAAN")->
            whereRaw("part_form.id is null")->
            where("marker_input.act_costing_ws", $request->act_costing_ws)->
            where("marker_input.panel", $request->panel)->
            groupBy("form_cut_input.id");

        return Datatables::eloquent($formCutInputs)->
            filterColumn('act_costing_ws', function ($query, $keyword) {
                $query->whereRaw("LOWER(act_costing_ws) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('buyer', function ($query, $keyword) {
                $query->whereRaw("LOWER(buyer) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('style', function ($query, $keyword) {
                $query->whereRaw("LOWER(style) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('color', function ($query, $keyword) {
                $query->whereRaw("LOWER(color) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('panel', function ($query, $keyword) {
                $query->whereRaw("LOWER(panel) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('nama_meja', function ($query, $keyword) {
                $query->whereRaw("LOWER(users.name) LIKE LOWER('%" . $keyword . "%')");
            })->order(function ($query) {
                $query->
                    orderBy('form_cut_input.waktu_selesai', 'desc')->
                    orderByRaw('FIELD(form_cut_input.tipe_form_cut, null, "NORMAL", "MANUAL")');
            })->toJson();
    }

    public function storePartForm(Request $request) {
        $success = [];
        $fail = [];
        $exist = [];

        foreach ($request->partForms as $partForm) {
            $isExist = PartForm::where("part_id", $request->part_id)->where("form_id", $partForm['no_form'])->count();

            if ($isExist < 1) {
                $partForm = PartForm::select("kode")->orderBy("kode", "desc")->first();
                $partFormNumber = $partForm ? intval(substr($partForm->kode, -5)) + 1 : 1;
                $kodePartForm = "PFM" . sprintf('%05s', $urutanPartForm);

                $addToCutPlan = PartForm::create([
                    "kode" => $kodePartForm,
                    "part_id" => $request->part_id,
                    "form_id" => $partForm['form_id'],
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);

                if ($addToCutPlan) {
                    array_push($success, ['no_form' => $partForm['no_form']]);
                } else {
                    array_push($fail, ['no_form' => $partForm['no_form']]);
                }
            } else {
                array_push($exist, ['no_form' => $partForm['no_form']]);
            }
        }

        if (count($success) > 0) {
            return array(
                'status' => 200,
                'message' => 'Form berhasil ditambahkan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail, "exist" => $exist],
            );
        } else {
            return array(
                'status' => 400,
                'message' => 'Data tidak ditemukan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail, "exist" => $exist],
            );
        }
    }

    public function destroyPartForm(Request $request)
    {
        $success = [];
        $fail = [];
        $exist = [];

        foreach ($request->partForms as $partForm) {
            $isExist = PartForm::where("part_id", $request->part_id)->where("form_id", $partForm['form_id'])->count();

            if ($isExist > 0) {
                $removeCutPlan = PartForm::where("part_id", $request->part_id)->
                    where("form_id", $partForm['form_id'])->
                    delete();

                if ($removeCutPlan) {
                    array_push($success, ['no_form' => $partForm['no_form']]);
                } else {
                    array_push($fail, ['no_form' => $partForm['no_form']]);
                }
            } else {
                array_push($exist, ['no_form' => $partForm['no_form']]);
            }
        }

        if (count($success) > 0) {
            return array(
                'status' => 200,
                'message' => 'Part Form berhasil disingkirkan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail],
            );
        } else {
            return array(
                'status' => 400,
                'message' => 'Data tidak ditemukan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail],
            );
        }
    }

    public function showPartForm(Request $request) {
        $formCutInputs = FormCutInput::selectRaw("
                part_detail.id part_detail_id,
                form_cut_input.id form_cut_id,
                form_cut_input.id_marker,
                form_cut_input.no_form,
                form_cut_input.tgl_form_cut,
                users.name nama_meja,
                marker_input.act_costing_ws,
                marker_input.buyer,
                marker_input.urutan_marker,
                marker_input.style,
                marker_input.color,
                marker_input.panel,
                GROUP_CONCAT(DISTINCT CONCAT(master_size_new.size, '(', marker_input_detail.ratio, ')') SEPARATOR ', ') marker_details,
                form_cut_input.no_cut,
                form_cut_input.total_lembar,
                part_form.kode kode_part_form,
                part.kode kode_part,
                GROUP_CONCAT(DISTINCT master_part.nama_part) nama_part
            ")->
            leftJoin("part_form", "part_form.form_id", "=", "form_cut_input.id")->
            leftJoin("part", "part.id", "=", "part_form.part_id")->
            leftJoin("part_detail", "part_detail.part_id", "=", "part.id")->
            leftJoin("master_part", "master_part.id", "=", "part_detail.master_part_id")->
            leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.id_marker")->
            leftJoin("marker_input_detail", "marker_input_detail.marker_id", "=", "marker_input.id")->
            leftJoin("master_size_new", "master_size_new.size", "=", "marker_input_detail.size")->
            leftJoin("users", "users.id", "=", "form_cut_input.no_meja")->
            whereRaw("part_form.id is not null")->
            where("part.id", $request->id)->
            groupBy("form_cut_input.id");

            return Datatables::of($formCutInputs)->filterColumn('id_marker', function ($query, $keyword) {
                    $query->whereRaw("LOWER(form_cut_input.id_marker) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('no_form', function ($query, $keyword) {
                    $query->whereRaw("LOWER(form_cut_input.no_form) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('nama_meja', function ($query, $keyword) {
                    $query->whereRaw("LOWER(users.name) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('act_costing_ws', function ($query, $keyword) {
                    $query->whereRaw("LOWER(marker_input.act_costing_ws) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('buyer', function ($query, $keyword) {
                    $query->whereRaw("LOWER(marker_input.buyer) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('style', function ($query, $keyword) {
                    $query->whereRaw("LOWER(marker_input.style) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('color', function ($query, $keyword) {
                    $query->whereRaw("LOWER(marker_input.color) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('panel', function ($query, $keyword) {
                    $query->whereRaw("LOWER(marker_input.panel) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('kode_part_form', function ($query, $keyword) {
                    $query->whereRaw("LOWER(part_form.kode) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('kode_part', function ($query, $keyword) {
                    $query->whereRaw("LOWER(part.kode) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('nama_part', function ($query, $keyword) {
                    $query->whereRaw("LOWER(master_part.nama_part) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('no_cut', function ($query, $keyword) {
                    $query->whereRaw("LOWER(form_cut_input.no_cut) LIKE LOWER('%" . $keyword . "%')");
                })->filterColumn('total_lembar', function ($query, $keyword) {
                    $query->whereRaw("LOWER(form_cut_input.total_lembar) LIKE LOWER('%" . $keyword . "%')");
                })->order(function ($query) {
                    $query->
                        orderBy('marker_input.act_costing_ws', 'desc')->
                        orderBy('form_cut_input.no_cut', 'asc')->
                        orderBy('form_cut_input.waktu_selesai', 'asc')->
                        orderByRaw('FIELD(form_cut_input.tipe_form_cut, null, "NORMAL", "MANUAL")');
                })->toJson();
    }
}
