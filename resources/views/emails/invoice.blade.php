<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Email</title>
</head>
<body>
    <p>Yth. {{ $data['nama_vendor'] }},</p>
    <p>Berikut terlampir invoice pembelian dengan No Faktur <strong>{{ $data['no_faktur'] }}</strong>.</p>
    <p>Silakan lihat file PDF terlampir untuk rincian lengkap.</p>
    <p>Terima kasih atas kerjasamanya.</p>
</body>
</html>
