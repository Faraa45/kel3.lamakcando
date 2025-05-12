<!DOCTYPE html>
<html lang="en">
<head>
    <!-- masukkan header dari layouts -> header.blade -->
    @include('layouts/header')
</head>
<body>
    
    Selamat Datang {{ Auth::user()->name }}

    <hr>
    
    <!-- Masukkan untuk template konten -->
    <div>@yield('konten')</div>
    
    <hr>
    <!-- masukkan footer dari layouts -> footer.blade -->
    @include('layouts/footer')

</table>
</body>
</html>