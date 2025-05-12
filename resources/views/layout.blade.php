<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'LAMAK')</title>
    <!-- Link ke Google Fonts untuk font yang lebih menarik (Lobster) -->
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    
    <!-- Tambahkan link ke Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Header -->
    <nav class="navbar navbar-light bg-light border-bottom shadow-sm px-4 py-2">
        <div class="container-fluid d-flex justify-content-center">
            <a class="navbar-brand fs-1" href="{{ url('/') }}" style="font-family: 'Lobster', cursive; font-weight: normal; color: orange;">
                Lamak Cando Balala
            </a>
        </div>
    </nav>

    <!-- Konten Halaman -->
    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- Tambahkan Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
