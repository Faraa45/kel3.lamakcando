<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #eee; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Slip Gaji</h2>
    <table>
        <tr>
            <th>No Slip Gaji</th>
            <td>{{ $no_slip_gaji }}</td>
        </tr>
        <tr>
            <th>Nama Pegawai</th>
            <td>{{ $nama_pegawai }}</td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>{{ $tgl }}</td>
        </tr>
        <tr>
            <th>Total Gaji</th>
            <td>Rp {{ number_format($total_gaji, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
