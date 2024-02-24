<!DOCTYPE html>
<html lang="en">

<table class="table">
    <tr>
        <td colspan='16'>Laporan Penerimaan</td>
    </tr>
    <tr>
        <td colspan='16'>{{ date('d-M-Y', strtotime($from)) }} - {{ date('d-M-Y', strtotime($to)) }}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <thead>
        <tr>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">No</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">No. Trans Out</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Tgl. Pengeluaran</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Buyer</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">WS</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Brand</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Style</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Color</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Size</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Qty</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Grade</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">No. Carton</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Lokasi</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Tujuan</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">User</th>
            <th style="background-color: yellow;border:1px solid black;font-weight:bold">Tgl. Input</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach ($data as $item)
            <tr>
                <td>{{ $no++ }}.</td>
                <td>{{ $item->no_trans }}</td>
                <td>{{ $item->tgl_pengeluaran_fix }}</td>
                <td>{{ $item->buyer }}</td>
                <td>{{ $item->ws }}</td>
                <td>{{ $item->brand }}</td>
                <td>{{ $item->styleno }}</td>
                <td>{{ $item->color }}</td>
                <td>{{ $item->size }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ $item->grade }}</td>
                <td>{{ $item->no_carton }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ $item->tuj_pengeluaran }}</td>
                <td>{{ $item->created_by }}</td>
                <td>{{ $item->created_at }}</td>
            </tr>
        @endforeach
    </tbody>

</table>

</html>
