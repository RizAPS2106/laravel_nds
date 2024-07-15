<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Rack;
use App\Models\Stocker;
use App\Models\Marker;
use App\Models\DCIn;
use App\Models\FormCutInput;
use Yajra\DataTables\Facades\DataTables;
use DB;

class DashboardController extends Controller
{
    public function track(Request $request) {
        return Redirect::to('/home');
    }

    // Marker
    public function marker(Request $request) {
        ini_set("max_execution_time", 0);
        ini_set("memory_limit", '2048M');

        $months = [['angka' => 1,'nama' => 'Januari'],['angka' => 2,'nama' => 'Februari'],['angka' => 3,'nama' => 'Maret'],['angka' => 4,'nama' => 'April'],['angka' => 5,'nama' => 'Mei'],['angka' => 6,'nama' => 'Juni'],['angka' => 7,'nama' => 'Juli'],['angka' => 8,'nama' => 'Agustus'],['angka' => 9,'nama' => 'September'],['angka' => 10,'nama' => 'Oktober'],['angka' => 11,'nama' => 'November'],['angka' => 12,'nama' => 'Desember']];
        $years = array_reverse(range(1999, date('Y')));

        if ($request->ajax()) {
            $month = date("m");
            $year = date("Y");

            if ($request->month) {
                $month = $request->month;
            }
            if ($request->year) {
                $year = $request->year;
            }

            $marker = Marker::selectRaw("
                    marker_input.buyer,
                    marker_input.act_costing_ws,
                    marker_input.style,
                    marker_input.color,
                    marker_input.tgl_cutting,
                    marker_input.kode,
                    marker_input.urutan_marker,
                    marker_input.gelar_qty,
                    marker_input.panel,
                    GROUP_CONCAT(DISTINCT CONCAT(master_sb_ws.size, '(', marker_input_detail.ratio, ')') ORDER BY master_size_new.urutan ASC SEPARATOR ' / ') marker_details,
                    COALESCE(CONCAT(master_part.nama_part, ' / ', CONCAT(COALESCE(part_detail.cons, '-'), ' ', COALESCE(UPPER(part_detail.unit), '-')), ' / ', CONCAT(COALESCE(master_secondary.tujuan, '-'), ' - ', COALESCE(master_secondary.proses, '-')) ), '-') nama_part
                ")->
                leftJoin("part", function ($join) {
                    $join->on("part.act_costing_id", "=", "marker_input.act_costing_id");
                    $join->on("part.panel", "=", "marker_input.panel");
                })->
                leftJoin("part_detail", "part_detail.part_id", "=", "part.id")->
                leftJoin("master_part", "master_part.id", "=", "part_detail.master_part_id")->
                leftJoin("master_secondary", "master_secondary.id", "=", "part_detail.master_secondary_id")->
                leftJoin("marker_input_detail", "marker_input_detail.marker_id", "=", "marker_input.id")->
                leftJoin("master_sb_ws", "master_sb_ws.id_so_det", "=", "marker_input_detail.so_det_id")->
                leftJoin("master_size_new", "master_size_new.size", "=", "master_sb_ws.size")->
                whereRaw("(MONTH(marker_input.tgl_cutting) = '".$month."')")->
                whereRaw("(YEAR(marker_input.tgl_cutting) = '".$year."')")->
                whereRaw("marker_input_detail.ratio > 0")->
                groupBy("marker_input.act_costing_id", "marker_input.buyer", "marker_input.style", "marker_input.color", "marker_input.panel", "marker_input.id", "part_detail.id")->
                orderBy("marker_input.buyer", "asc")->
                orderBy("marker_input.act_costing_ws", "asc")->
                orderBy("marker_input.style", "asc")->
                orderBy("marker_input.color", "asc")->
                orderBy("marker_input.panel", "asc")->
                orderByRaw("CAST(marker_input.urutan_marker AS UNSIGNED) asc");

            return DataTables::eloquent($marker)->toJson();
        }

        return view('dashboard', ['page' => 'dashboard-marker', 'months' => $months, 'years' => $years]);
    }

    public function markerQty(Request $request) {
        ini_set("max_execution_time", 0);
        ini_set("memory_limit", '2048M');

        $month = date("m");
        $year = date("Y");

        if ($request->month) {
            $month = $request->month;
        }
        if ($request->year) {
            $year = $request->year;
        }

        $markerQty = DB::select("
            SELECT
                COUNT(id) marker_count,
                SUM(gelar_qty) total_gelar
            FROM (
                SELECT
                    id,
                    gelar_qty
                FROM
                    marker_input
                WHERE
                    MONTH(tgl_cutting) = '".$month."' AND YEAR(tgl_cutting) = '".$year."'
            ) marker
        ")[0];

        $partQty = DB::select("
            SELECT
                COUNT(id) part_count
            FROM (
                SELECT
                    id
                FROM
                    part
                WHERE
                    (MONTH(created_at) = '".$month."' AND  YEAR(created_at) = '".$year."') OR
                    (MONTH(updated_at) = '".$month."' AND  YEAR(updated_at) = '".$year."')
            ) part
        ")[0]->part_count;

        $wsQty = DB::select("
            SELECT
                COUNT(act_costing_ws) ws_count
            FROM (
                SELECT
                    act_costing_ws
                FROM
                    marker_input
                WHERE
                    (MONTH(tgl_cutting) = '".$month."' AND  YEAR(tgl_cutting) = '".$year."') OR
                    (MONTH(tgl_cutting) = '".$month."' AND  YEAR(tgl_cutting) = '".$year."')
                GROUP BY
                    act_costing_ws
            ) ws
        ")[0]->ws_count;

        return array(
            "markerQty" => $markerQty->marker_count,
            "markerSum" => $markerQty->total_gelar,
            "partQty" => $partQty,
            "wsQty" => $wsQty
        );
    }

    // Cutting
    public function cutting(Request $request) {
        ini_set("max_execution_time", 0);
        ini_set("memory_limit", '2048M');

        $months = [['angka' => 1,'nama' => 'Januari'],['angka' => 2,'nama' => 'Februari'],['angka' => 3,'nama' => 'Maret'],['angka' => 4,'nama' => 'April'],['angka' => 5,'nama' => 'Mei'],['angka' => 6,'nama' => 'Juni'],['angka' => 7,'nama' => 'Juli'],['angka' => 8,'nama' => 'Agustus'],['angka' => 9,'nama' => 'September'],['angka' => 10,'nama' => 'Oktober'],['angka' => 11,'nama' => 'November'],['angka' => 12,'nama' => 'Desember']];
        $years = array_reverse(range(1999, date('Y')));

        if ($request->ajax()) {
            $month = date("m");
            $year = date("Y");

            if ($request->month) {
                $month = $request->month;
            }
            if ($request->year) {
                $year = $request->year;
            }

            $form = FormCutInput::selectRaw("
                    marker_input.buyer,
                    marker_input.act_costing_ws,
                    marker_input.style,
                    marker_input.color,
                    marker_input.kode,
                    marker_input.urutan_marker,
                    marker_input.panel,
                    COALESCE(form_cut_input.tgl_form_cut, '-') tgl_form_cut,
                    COALESCE(form_cut_input.no_form, '-') no_form,
                    COALESCE(form_cut_input.no_cut, '-') no_cut,
                    COALESCE(form_cut_input.total_lembar, '-') total_lembar,
                    COALESCE(form_cut_input_detail.id_roll, '-') id_roll,
                    COALESCE(form_cut_input_detail.id_item, '-') id_item,
                    COALESCE(LEFT(form_cut_input_detail.detail_item, 10), '-') detail_item,
                    COALESCE(form_cut_input_detail.group_roll, '-') group_roll,
                    COALESCE(form_cut_input_detail.lot, '-') lot,
                    COALESCE(form_cut_input_detail.roll, '-') roll,
                    COALESCE(form_cut_input_detail.qty, '-') qty,
                    COALESCE(form_cut_input_detail.unit, '-') unit,
                    COALESCE(form_cut_input_detail.total_pemakaian_roll, '-') total_pemakaian_roll,
                    COALESCE(form_cut_input_detail.piping, '-') piping,
                    COALESCE(form_cut_input_detail.short_roll, '-') short_roll,
                    COALESCE(form_cut_input_detail.remark, '-') remark
                ")->
                leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.id_marker")->
                leftJoin("form_cut_input_detail", "form_cut_input_detail.no_form_cut_input", "=", "form_cut_input.no_form")->
                whereRaw("(MONTH(form_cut_input.tgl_form_cut) = '".$month."')")->
                whereRaw("(YEAR(form_cut_input.tgl_form_cut) = '".$year."')")->
                groupBy("marker_input.id", "form_cut_input.id", "form_cut_input_detail.id")->
                orderBy("marker_input.tgl_cutting", "desc")->
                orderBy("marker_input.buyer", "asc")->
                orderBy("marker_input.act_costing_ws", "asc")->
                orderBy("marker_input.style", "asc")->
                orderBy("marker_input.color", "asc")->
                orderBy("marker_input.panel", "asc")->
                orderBy("marker_input.urutan_marker", "asc")->
                orderBy("form_cut_input.no_cut", "asc")->
                orderBy("form_cut_input_detail.id", "asc");

            return DataTables::eloquent($form)->toJson();
        }

        return view('dashboard', ['page' => 'dashboard-cutting', 'months' => $months, 'years' => $years]);
    }

    // Cutting Qty
    public function cuttingQty(Request $request) {
        $month = date("m");
        $year = date("Y");

        if ($request->month) {
            $month = $request->month;
        }
        if ($request->year) {
            $year = $request->year;
        }

        $dataQty = DB::select("
            SELECT
                FLOOR( SUM( CASE WHEN `status` = 'SPREADING' AND cutting_plan_id IS NULL THEN 1 ELSE 0 END ) ) pending,
                FLOOR( SUM( CASE WHEN `status` = 'SPREADING' AND cutting_plan_id IS NULL THEN total_lembar ELSE 0 END ) ) pending_total,
                FLOOR( SUM( CASE WHEN `status` = 'SPREADING' AND cutting_plan_id IS NOT NULL THEN 1 ELSE 0 END ) ) plan,
                FLOOR( SUM( CASE WHEN `status` = 'SPREADING' AND cutting_plan_id IS NOT NULL THEN total_lembar ELSE 0 END ) ) plan_total,
                FLOOR( SUM( CASE WHEN `status` != 'SPREADING' AND `status` != 'SELESAI PENGERJAAN' THEN 1 ELSE 0 END ) ) progress,
                FLOOR( SUM( CASE WHEN `status` != 'SPREADING' AND `status` != 'SELESAI PENGERJAAN' THEN total_lembar ELSE 0 END ) ) progress_total,
                FLOOR( SUM( CASE WHEN `status` = 'SELESAI PENGERJAAN' THEN 1 ELSE 0 END ) ) finished,
                FLOOR( SUM( CASE WHEN `status` = 'SELESAI PENGERJAAN' THEN total_lembar ELSE 0 END ) ) finished_total
            FROM
                (
                    SELECT
                        form_cut_input.id form_id,
                        form_cut_input.`status`,
                        COALESCE(form_cut_input.total_lembar, form_cut_input.qty_ply, 0) total_lembar,
                        cutting_plan.id cutting_plan_id
                    FROM
                        form_cut_input
                        LEFT JOIN cutting_plan ON cutting_plan.no_form_cut_input = form_cut_input.no_form
                    WHERE
                        ( MONTH ( form_cut_input.tgl_form_cut ) = '".$month."' ) AND ( YEAR ( form_cut_input.tgl_form_cut ) = '".$year."' ) OR
                        ( MONTH ( form_cut_input.waktu_selesai ) = '".$month."' ) AND ( YEAR ( form_cut_input.waktu_selesai ) = '".$year."' )
                    GROUP BY
                        form_cut_input.id
                ) frm
        ");

        return $dataQty;
    }

    // Stocker
    public function stocker(Request $request) {
        ini_set("max_execution_time", 0);
        ini_set("memory_limit", '2048M');

        $months = [['angka' => 1,'nama' => 'Januari'],['angka' => 2,'nama' => 'Februari'],['angka' => 3,'nama' => 'Maret'],['angka' => 4,'nama' => 'April'],['angka' => 5,'nama' => 'Mei'],['angka' => 6,'nama' => 'Juni'],['angka' => 7,'nama' => 'Juli'],['angka' => 8,'nama' => 'Agustus'],['angka' => 9,'nama' => 'September'],['angka' => 10,'nama' => 'Oktober'],['angka' => 11,'nama' => 'November'],['angka' => 12,'nama' => 'Desember']];
        $years = array_reverse(range(1999, date('Y')));

        if ($request->ajax()) {
            $month = date("m");
            $year = date("Y");

            if ($request->month) {
                $month = $request->month;
            }
            if ($request->year) {
                $year = $request->year;
            }

            $stocker = MarkerDetail::selectRaw("
                    marker_input.buyer,
                    marker_input.act_costing_ws,
                    marker_input.style,
                    marker_input.color,
                    marker_input.kode,
                    marker_input.urutan_marker,
                    marker_input.panel,
                    marker_input_detail.so_det_id,
                    MAX(form_cut_input.no_form) no_form,
                    MAX(stocker_input.id_qr_stocker) id_qr_stocker,
                    COALESCE(stocker_input.ratio, marker_input_detail.ratio) ratio,
                    form_cut_input.no_cut,
                    MAX(stocker_input.id) stocker_id,
                    MAX(stocker_input.shade) shade,
                    MAX(stocker_input.group_stocker) group_stocker,
                    MAX(stocker_input.qty_ply) qty_ply,
                    MAX(CAST(stocker_input.range_akhir as UNSIGNED)) range_akhir,
                    modify_size_qty.difference_qty
                ")->
                leftJoin("marker_input", "marker_input_detail.marker_id", "=", "marker_input.id")->
                leftJoin("form_cut_input", "form_cut_input.id_marker", "=", "marker_input.kode")->
                leftJoin("part_form", "part_form.form_id", "=", "form_cut_input.id")->
                leftJoin("stocker_input", function ($join) {
                    $join->on("stocker_input.form_cut_id", "=", "form_cut_input.id");
                    $join->on("stocker_input.so_det_id", "=", "marker_input_detail.so_det_id");
                })->
                leftJoin("modify_size_qty", function ($join) {
                    $join->on("modify_size_qty.no_form", "=", "form_cut_input.no_form");
                    $join->on("modify_size_qty.so_det_id", "=", "marker_input_detail.so_det_id");
                })->
                where("marker_input.act_costing_ws", $dataSpreading->ws)->
                where("marker_input.color", $dataSpreading->color)->
                where("marker_input.panel", $dataSpreading->panel)->
                where("form_cut_input.no_cut", "<=", $dataSpreading->no_cut)->
                where("part_form.part_id", $dataSpreading->part_id)->
                // where("marker_input_detail.ratio", ">", "0")->
                groupBy("form_cut_input.no_form", "form_cut_input.no_cut", "marker_input_detail.so_det_id")->
                orderBy("form_cut_input.no_cut", "desc")->
                orderBy("form_cut_input.no_form", "desc")->
                get();

            $stocker = Stocker::selectRaw("
                    marker_input.buyer,
                    marker_input.act_costing_ws,
                    marker_input.style,
                    marker_input.color,
                    marker_input.kode,
                    marker_input.urutan_marker,
                    marker_input.panel,
                    COALESCE(form_cut_input.tgl_form_cut, '-') tgl_form_cut,
                    COALESCE(form_cut_input.no_form, '-') no_form,
                    COALESCE(form_cut_input.no_cut, '-') no_cut,
                    COALESCE(form_cut_input.total_lembar, '-') total_lembar,
                    COALESCE(form_cut_input_detail.id_roll, '-') id_roll,
                    COALESCE(form_cut_input_detail.id_item, '-') id_item,
                    COALESCE(LEFT(form_cut_input_detail.detail_item, 10), '-') detail_item,
                    COALESCE(form_cut_input_detail.group_roll, '-') group_roll,
                    COALESCE(form_cut_input_detail.lot, '-') lot,
                    COALESCE(form_cut_input_detail.roll, '-') roll,
                    COALESCE(form_cut_input_detail.qty, '-') qty,
                    COALESCE(form_cut_input_detail.unit, '-') unit,
                    COALESCE(form_cut_input_detail.total_pemakaian_roll, '-') total_pemakaian_roll,
                    COALESCE(form_cut_input_detail.piping, '-') piping,
                    COALESCE(form_cut_input_detail.short_roll, '-') short_roll,
                    COALESCE(form_cut_input_detail.remark, '-') remark
                ")->
                leftJoin("form_cut_input", "form_cut_input.id", "=", "stocker.form_cut_id")->
                leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.id_marker")->
                leftJoin("form_cut_input_detail", "form_cut_input_detail.no_form_cut_input", "=", "form_cut_input.no_form")->
                whereRaw("(MONTH(form_cut_input.tgl_form_cut) = '".$month."')")->
                whereRaw("(YEAR(form_cut_input.tgl_form_cut) = '".$year."')")->
                groupBy("marker_input.id", "form_cut_input.id", "form_cut_input_detail.id")->
                orderBy("marker_input.tgl_cutting", "desc")->
                orderBy("marker_input.buyer", "asc")->
                orderBy("marker_input.act_costing_ws", "asc")->
                orderBy("marker_input.style", "asc")->
                orderBy("marker_input.color", "asc")->
                orderBy("marker_input.panel", "asc")->
                orderBy("marker_input.urutan_marker", "asc")->
                orderBy("form_cut_input.no_cut", "asc")->
                orderBy("form_cut_input_detail.id", "asc");

            return DataTables::eloquent($stocker)->toJson();
        }

        return view('dashboard', ['page' => 'dashboard-stocker', 'months' => $months, 'years' => $years]);
    }

    // DC
    public function dc(Request $request) {
        ini_set("max_execution_time", 0);
        ini_set("memory_limit", '2048M');

        $months = [['angka' => 1,'nama' => 'Januari'],['angka' => 2,'nama' => 'Februari'],['angka' => 3,'nama' => 'Maret'],['angka' => 4,'nama' => 'April'],['angka' => 5,'nama' => 'Mei'],['angka' => 6,'nama' => 'Juni'],['angka' => 7,'nama' => 'Juli'],['angka' => 8,'nama' => 'Agustus'],['angka' => 9,'nama' => 'September'],['angka' => 10,'nama' => 'Oktober'],['angka' => 11,'nama' => 'November'],['angka' => 12,'nama' => 'Desember']];
        $years = array_reverse(range(1999, date('Y')));

        if ($request->ajax()) {
            $month = date("m");
            $year = date("Y");

            if ($request->month) {
                $month = $request->month;
            }
            if ($request->year) {
                $year = $request->year;
            }

            $dc = Stocker::selectRaw("
                    stocker_input.id stocker_id,
                    stocker_input.id_qr_stocker,
                    stocker_input.act_costing_ws,
                    stocker_input.color,
                    stocker_input.size,
                    stocker_input.so_det_id,
                    stocker_input.shade,
                    stocker_input.ratio,
                    master_part.nama_part,
                    CONCAT(stocker_input.range_awal, ' - ', stocker_input.range_akhir, (CASE WHEN dc_in_input.qty_reject IS NOT NULL AND dc_in_input.qty_replace IS NOT NULL THEN CONCAT(' (', (COALESCE(dc_in_input.qty_replace, 0) + COALESCE(secondary_in_input.qty_replace, 0) + COALESCE(secondary_inhouse_input.qty_replace, 0) - COALESCE(dc_in_input.qty_reject, 0) - COALESCE(secondary_in_input.qty_reject, 0) - COALESCE(secondary_inhouse_input.qty_reject, 0)), ') ') ELSE ' (0)' END)) stocker_range,
                    stocker_input.status,
                    dc_in_input.id dc_in_id,
                    dc_in_input.tujuan,
                    dc_in_input.tempat,
                    dc_in_input.lokasi,
                    (CASE WHEN dc_in_input.tujuan = 'SECONDARY DALAM' OR dc_in_input.tujuan = 'SECONDARY LUAR' THEN dc_in_input.lokasi ELSE '-' END) secondary,
                    COALESCE(rack_detail_stocker.nm_rak, (CASE WHEN dc_in_input.tempat = 'RAK' THEN dc_in_input.lokasi ELSE null END), (CASE WHEN dc_in_input.lokasi = 'RAK' THEN dc_in_input.det_alokasi ELSE null END), '-') rak,
                    COALESCE(trolley.nama_trolley, (CASE WHEN dc_in_input.tempat = 'TROLLEY' THEN dc_in_input.lokasi ELSE null END), '-') troli,
                    COALESCE((COALESCE(dc_in_input.qty_awal, stocker_input.qty_ply_mod, stocker_input.qty_ply, 0) - COALESCE(dc_in_input.qty_reject, 0) - COALESCE(secondary_in_input.qty_reject, 0) - COALESCE(secondary_inhouse_input.qty_reject, 0) + COALESCE(dc_in_input.qty_replace, 0) + COALESCE(secondary_in_input.qty_replace, 0) + COALESCE(secondary_inhouse_input.qty_replace, 0)), stocker_input.qty_ply) dc_in_qty,
                    CONCAT(form_cut_input.no_form, ' / ', form_cut_input.no_cut) no_cut,
                    COALESCE(UPPER(loading_line.nama_line), '-') line,
                    stocker_input.updated_at latest_update
                ")->
                leftJoin("form_cut_input", "form_cut_input.id", "=", "stocker_input.form_cut_id")->
                leftJoin("part_detail", "stocker_input.part_detail_id", "=", "part_detail.id")->
                leftJoin("master_part", "master_part.id", "=", "part_detail.master_part_id")->
                leftJoin("dc_in_input", "dc_in_input.id_qr_stocker", "=", "stocker_input.id_qr_stocker")->
                leftJoin("secondary_in_input", "secondary_in_input.id_qr_stocker", "=", "stocker_input.id_qr_stocker")->
                leftJoin("secondary_inhouse_input", "secondary_inhouse_input.id_qr_stocker", "=", "stocker_input.id_qr_stocker")->
                leftJoin("rack_detail_stocker", "rack_detail_stocker.stocker_id", "=", "stocker_input.id_qr_stocker")->
                leftJoin("trolley_stocker", "trolley_stocker.stocker_id", "=", "stocker_input.id")->
                leftJoin("trolley", "trolley.id", "=", "trolley_stocker.trolley_id")->
                leftJoin("loading_line", "loading_line.stocker_id", "=", "stocker_input.id")->
                whereRaw("(MONTH(form_cut_input.waktu_selesai) = '".$month."')")->
                whereRaw("(YEAR(form_cut_input.waktu_selesai) = '".$year."')")->
                orderBy("stocker_input.act_costing_ws", "asc")->
                orderBy("stocker_input.color", "asc")->
                orderBy("form_cut_input.no_cut", "asc")->
                orderBy("master_part.nama_part", "asc")->
                orderBy("stocker_input.so_det_id", "asc")->
                orderBy("stocker_input.shade", "desc")->
                orderBy("stocker_input.id_qr_stocker", "asc");

            return DataTables::eloquent($dc)->toJson();
        }

        return view('dashboard', ['page' => 'dashboard-dc', 'months' => $months, 'years' => $years]);
    }

    public function dcQty(Request $request) {
        $month = date("m");
        $year = date("Y");

        if ($request->month) {
            $month = $request->month;
        }
        if ($request->year) {
            $year = $request->year;
        }

        $dataQty = DB::select("
            SELECT
                MIN(secondary) secondary,
                MIN(rak) rak,
                MIN(troli) troli,
                MIN(line) line,
                MIN(qty_ply) qty_ply,
                MIN(dc_in_qty) dc_in_qty
            FROM
            (
                SELECT
                    stocker_input.form_cut_id,
                    stocker_input.so_det_id,
                    stocker_input.group_stocker,
                    stocker_input.ratio,
                    stocker_input.STATUS,
                    (
                        CASE WHEN dc_in_input.tujuan = 'SECONDARY DALAM'
                        OR dc_in_input.tujuan = 'SECONDARY LUAR'
                        THEN dc_in_input.lokasi ELSE '-' END
                    ) secondary,
                    COALESCE (
                        rack_detail_stocker.nm_rak,
                        ( CASE WHEN dc_in_input.tempat = 'RAK' THEN dc_in_input.lokasi ELSE NULL END ),
                        ( CASE WHEN dc_in_input.lokasi = 'RAK' THEN dc_in_input.det_alokasi ELSE NULL END ),
                        '-'
                    ) rak,
                    COALESCE (
                        trolley.nama_trolley,
                        ( CASE WHEN dc_in_input.tempat = 'TROLLEY' THEN dc_in_input.lokasi ELSE NULL END ),
                        '-'
                    ) troli,
                    COALESCE (
                        UPPER( loading_line.nama_line ),
                        '-'
                    ) line,
                    COALESCE((COALESCE(dc_in_input.qty_awal, stocker_input.qty_ply_mod, stocker_input.qty_ply, 0) - COALESCE(dc_in_input.qty_reject, 0) - COALESCE(secondary_in_input.qty_reject, 0) - COALESCE(secondary_inhouse_input.qty_reject, 0) + COALESCE(dc_in_input.qty_replace, 0) + COALESCE(secondary_in_input.qty_replace, 0) + COALESCE(secondary_inhouse_input.qty_replace, 0)), stocker_input.qty_ply) dc_in_qty,
                    stocker_input.qty_ply,
                    stocker_input.updated_at,
                    form_cut_input.waktu_selesai
                FROM
                    `stocker_input`
                    LEFT JOIN `form_cut_input` ON `form_cut_input`.`id` = `stocker_input`.`form_cut_id`
                    LEFT JOIN `part_detail` ON `stocker_input`.`part_detail_id` = `part_detail`.`id`
                    LEFT JOIN `master_part` ON `master_part`.`id` = `part_detail`.`master_part_id`
                    LEFT JOIN `dc_in_input` ON `dc_in_input`.`id_qr_stocker` = `stocker_input`.`id_qr_stocker`
                    LEFT JOIN `secondary_in_input` ON `secondary_in_input`.`id_qr_stocker` = `stocker_input`.`id_qr_stocker`
                    LEFT JOIN `secondary_inhouse_input` ON `secondary_inhouse_input`.`id_qr_stocker` = `stocker_input`.`id_qr_stocker`
                    LEFT JOIN `rack_detail_stocker` ON `rack_detail_stocker`.`stocker_id` = `stocker_input`.`id_qr_stocker`
                    LEFT JOIN `trolley_stocker` ON `trolley_stocker`.`stocker_id` = `stocker_input`.`id`
                    LEFT JOIN `trolley` ON `trolley`.`id` = `trolley_stocker`.`trolley_id`
                    LEFT JOIN `loading_line` ON `loading_line`.`stocker_id` = `stocker_input`.`id`
            ) stock_location
            WHERE
                (MONTH(stock_location.waktu_selesai) = '".$month."') AND
                (YEAR(stock_location.waktu_selesai) = '".$year."')
            GROUP BY
                `stock_location`.`form_cut_id`,
                `stock_location`.`so_det_id`,
                `stock_location`.`group_stocker`,
                `stock_location`.`ratio`
        ");

        return $dataQty;
    }

    public function sewingEff(Request $request) {
        ini_set("max_execution_time", 0);
        ini_set("memory_limit", '2048M');

        $months = [['angka' => 1,'nama' => 'Januari'],['angka' => 2,'nama' => 'Februari'],['angka' => 3,'nama' => 'Maret'],['angka' => 4,'nama' => 'April'],['angka' => 5,'nama' => 'Mei'],['angka' => 6,'nama' => 'Juni'],['angka' => 7,'nama' => 'Juli'],['angka' => 8,'nama' => 'Agustus'],['angka' => 9,'nama' => 'September'],['angka' => 10,'nama' => 'Oktober'],['angka' => 11,'nama' => 'November'],['angka' => 12,'nama' => 'Desember']];
        $years = array_reverse(range(1999, date('Y')));

        if ($request->ajax()) {
            $month = date("m");
            $year = date("Y");

            if ($request->month) {
                $month = $request->month;
            }
            if ($request->year) {
                $year = $request->year;
            }

            $sewingEfficiencyData = DB::connection('mysql_sb')->table('master_plan')->
                selectRaw("
                    tgl_plan tgl_produksi,
                    ROUND((SUM(IFNULL( rfts.rft, 0 )) / SUM((IFNULL( rfts.rft, 0 ) + IFNULL( defects.defect, 0 ) + IFNULL( reworks.rework, 0 ) + IFNULL( rejects.reject, 0 ))) * 100 ), 2) rft,
                    AVG(master_plan.target_effy) target_efficiency,
                    ROUND((SUM(((IFNULL( rfts.rft, 0 ) + IFNULL( reworks.rework, 0 ))* master_plan.smv ))/SUM( master_plan.man_power * master_plan.jam_kerja * 60 ) * 100 ), 2) efficiency
                ")->
                leftJoin(DB::raw("(SELECT count(rfts.id) rft, master_plan.id master_plan_id from output_rfts rfts inner join master_plan on master_plan.id = rfts.master_plan_id where (MONTH(rfts.updated_at) = '".$month."' AND YEAR(rfts.updated_at) = '".$year."') and status = 'NORMAL' and (MONTH(master_plan.tgl_plan) = '".$month."' AND YEAR(master_plan.tgl_plan) = '".$year."') GROUP BY master_plan.id, master_plan.tgl_plan) as rfts"), "master_plan.id", "=", "rfts.master_plan_id")->
                leftJoin(DB::raw("(SELECT count(defects.id) defect, master_plan.id master_plan_id from output_defects defects inner join master_plan on master_plan.id = defects.master_plan_id where defects.defect_status = 'defect' and (MONTH(defects.updated_at) = '".$month."' AND YEAR(defects.updated_at) = '".$year."') and (MONTH(master_plan.tgl_plan) = '".$month."' AND YEAR(master_plan.tgl_plan) = '".$year."') GROUP BY master_plan.id, master_plan.tgl_plan) as defects"), "master_plan.id", "=", "defects.master_plan_id")->
                leftJoin(DB::raw("(SELECT count(defrew.id) rework, master_plan.id master_plan_id from output_defects defrew inner join master_plan on master_plan.id = defrew.master_plan_id where defrew.defect_status = 'reworked' and (MONTH(defrew.updated_at) = '".$month."' AND YEAR(defrew.updated_at) = '".$year."') and (MONTH(master_plan.tgl_plan) = '".$month."' AND YEAR(master_plan.tgl_plan) = '".$year."') GROUP BY master_plan.id, master_plan.tgl_plan) as reworks"), "master_plan.id", "=", "reworks.master_plan_id")->
                leftJoin(DB::raw("(SELECT count(rejects.id) reject, master_plan.id master_plan_id from output_rejects rejects inner join master_plan on master_plan.id = rejects.master_plan_id where (MONTH(rejects.updated_at) = '".$month."' AND YEAR(rejects.updated_at) = '".$year."') and (MONTH(master_plan.tgl_plan) = '".$month."' AND YEAR(master_plan.tgl_plan) = '".$year."') GROUP BY master_plan.id, master_plan.tgl_plan) as rejects"), "master_plan.id", "=", "rejects.master_plan_id")->
                where("master_plan.cancel", 'N')->
                whereRaw("(
                    MONTH(master_plan.tgl_plan) = '".$month."'
                    AND
                    YEAR(master_plan.tgl_plan) = '".$year."'
                )")->
                groupBy("master_plan.tgl_plan")->get();

            return json_encode($sewingEfficiencyData);
        }

        return view('dashboard', ['page' => 'dashboard-sewing-eff', 'months' => $months, 'years' => $years]);
    }

    public function sewingSummary(Request $request) {
        $month = date("m");
        $year = date("Y");

        if ($request->month) {
            $month = $request->month;
        }
        if ($request->year) {
            $year = $request->year;
        }

        $sewingSummaryData = DB::connection('mysql_sb')->table('master_plan')->
            selectRaw("
                COUNT(DISTINCT master_plan.id_ws) total_order,
                SUM(IFNULL( rfts.rft, 0 ) + IFNULL( reworks.rework, 0 )) total_output,
                ROUND((SUM(((IFNULL( rfts.rft, 0 )+ IFNULL( reworks.rework, 0 ))* master_plan.smv ))/SUM( master_plan.man_power * master_plan.jam_kerja * 60 ) * 100 ), 2) total_efficiency
            ")->
            leftJoin(DB::raw("(SELECT count(rfts.id) rft, master_plan.id master_plan_id from output_rfts rfts inner join master_plan on master_plan.id = rfts.master_plan_id where (MONTH(rfts.updated_at) = '".$month."' AND YEAR(rfts.updated_at) = '".$year."') and status = 'NORMAL' and (MONTH(master_plan.tgl_plan) = '".$month."' AND YEAR(master_plan.tgl_plan) = '".$year."') GROUP BY master_plan.id, master_plan.tgl_plan) as rfts"), "master_plan.id", "=", "rfts.master_plan_id")->
            leftJoin(DB::raw("(SELECT count(defects.id) defect, master_plan.id master_plan_id from output_defects defects inner join master_plan on master_plan.id = defects.master_plan_id where defects.defect_status = 'defect' and (MONTH(defects.updated_at) = '".$month."' AND YEAR(defects.updated_at) = '".$year."') and (MONTH(master_plan.tgl_plan) = '".$month."' AND YEAR(master_plan.tgl_plan) = '".$year."') GROUP BY master_plan.id, master_plan.tgl_plan) as defects"), "master_plan.id", "=", "defects.master_plan_id")->
            leftJoin(DB::raw("(SELECT count(defrew.id) rework, master_plan.id master_plan_id from output_defects defrew inner join master_plan on master_plan.id = defrew.master_plan_id where defrew.defect_status = 'reworked' and (MONTH(defrew.updated_at) = '".$month."' AND YEAR(defrew.updated_at) = '".$year."') and (MONTH(master_plan.tgl_plan) = '".$month."' AND YEAR(master_plan.tgl_plan) = '".$year."') GROUP BY master_plan.id, master_plan.tgl_plan) as reworks"), "master_plan.id", "=", "reworks.master_plan_id")->
            leftJoin(DB::raw("(SELECT count(rejects.id) reject, master_plan.id master_plan_id from output_rejects rejects inner join master_plan on master_plan.id = rejects.master_plan_id where (MONTH(rejects.updated_at) = '".$month."' AND YEAR(rejects.updated_at) = '".$year."') and (MONTH(master_plan.tgl_plan) = '".$month."' AND YEAR(master_plan.tgl_plan) = '".$year."') GROUP BY master_plan.id, master_plan.tgl_plan) as rejects"), "master_plan.id", "=", "rejects.master_plan_id")->
            where("master_plan.cancel", 'N')->
            whereRaw("(
                MONTH(master_plan.tgl_plan) = '".$month."'
                AND
                YEAR(master_plan.tgl_plan) = '".$year."'
            )")->
            groupByRaw("MONTH(master_plan.tgl_plan), YEAR(master_plan.tgl_plan)")->first();

        return json_encode($sewingSummaryData);
    }

    public function sewingOutputData(Request $request) {
        $groupBy = $request->groupBy;
        $outputType = $request->outputType;

        $dailyOrderOutputSql = MasterPlan::selectRaw("
                master_plan.tgl_plan tanggal,
                ".($this->groupBy == 'size' ? ' output_rfts'.($this->outputType).'.so_det_id, so_det.size, ' : '')."
                count(output_rfts".($this->outputType).".id) output,
                act_costing.kpno ws,
                act_costing.styleno style,
                master_plan.color,
                master_plan.sewing_line,
                master_plan.smv smv,
                master_plan.jam_kerja jam_kerja,
                master_plan.man_power man_power,
                master_plan.plan_target plan_target,
                coalesce(max(output_rfts".($this->outputType).".updated_at), master_plan.tgl_plan) latest_output
            ")->
            leftJoin("output_rfts".($this->outputType)."", "output_rfts".($this->outputType).".master_plan_id", "=", "master_plan.id")->
            leftJoin("act_costing", "act_costing.id", "=", "master_plan.id_ws");
            if ($this->groupBy == "size") {
                $dailyOrderOutputSql->leftJoin('so_det', 'so_det.id', '=', 'output_rfts'.($this->outputType).'.so_det_id');
            }
            $dailyOrderOutputSql->
                where("act_costing.id", $this->selectedOrder)->
                groupByRaw("master_plan.id_ws, act_costing.styleno, master_plan.color, master_plan.sewing_line , master_plan.tgl_plan ".($this->groupBy == 'size' ? ', so_det.size' : '')."")->
                orderBy("master_plan.id_ws", "asc")->
                orderBy("act_costing.styleno", "asc")->
                orderBy("master_plan.color", "asc")->
                orderByRaw("master_plan.sewing_line asc ".($this->groupBy == 'size' ? ', so_det.id asc' : ''));
            if ($this->dateFromFilter) {
                $dailyOrderOutputSql->where('master_plan.tgl_plan', '>=', $this->dateFromFilter);
            }
            if ($this->dateToFilter) {
                $dailyOrderOutputSql->where('master_plan.tgl_plan', '<=', $this->dateToFilter);
            }
            if ($this->colorFilter) {
                $dailyOrderOutputSql->where('master_plan.color', $this->colorFilter);
            }
            if ($this->lineFilter) {
                $dailyOrderOutputSql->where('master_plan.sewing_line', $this->lineFilter);
            }
            if ($this->groupBy == "size" && $this->sizeFilter) {
                $dailyOrderOutputSql->where('so_det.size', $this->sizeFilter);
            }
            $this->dailyOrderOutputs = $dailyOrderOutputSql->get();
    }
}
