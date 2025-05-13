<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penggajian</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Laporan Penggajian</h2>
    <table>
        <thead>
            <tr>
                <th>No Slip</th>
                <th>Nama Pegawai</th>
                <th>Tanggal</th>
                <th>Total Gaji</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penggajian as $item)
                <tr>
                    <td>{{ $item->no_slip_gaji }}</td>
                    <td>{{ $item->pegawai->nama_pegawai ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl)->format('d M Y') }}</td>
                    <td>Rp {{ number_format($item->total_gaji, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($item->status_pembayaran) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
