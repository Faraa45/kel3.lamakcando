
@include('layouts/header')
@yield('konten')
@include('layouts/footer')

<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'LAMAK')</title>
    <!-- Tambahkan link ke Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Jika ada CSS lokal, tambahkan juga -->
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
</head>
<body>
    @yield('content')

    <!-- Tambahkan Bootstrap JS CDN (wajib untuk komponen interaktif) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Jika ada JS lokal, tambahkan juga -->
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
</body>
</html>

