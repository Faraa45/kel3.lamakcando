<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lamak Cando Balala</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}" />

  <!-- Tambahkan CSS custom -->
  <style>
    .login-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f4f4f4;
    }

    .login-card {
      width: 100%;
      max-width: 400px;
      padding: 30px;
      border-radius: 10px;
      background: white;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .login-title {
      font-size: 28px;
      font-weight: bold;
      margin-top: 15px;
      margin-bottom: 30px;
    }

    .form-group {
      position: relative;
      margin-bottom: 20px;
    }

    .form-group input {
      padding-left: 40px;
    }

    .form-group i {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
    }

    .btn-login {
      width: 100%;
      padding: 10px;
      font-size: 16px;
    }
  </style>
</head>

<body>
  <div class="login-wrapper">
    <div class="login-card">
      <!-- Logo -->
      <img src="{{ asset('images/logos/mukena.PNG') }}" width="100" alt="logo">
      
      <!-- Title -->
      <div class="login-title">LOGIN AREA</div>

      <!-- Alert Error -->
      @if ($errors->any())
      <div style="color: red; text-align:left;">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <!-- Form -->
      <form method="POST" action="{{ url('/login') }}">
        @csrf

        <div class="form-group">
          <i class="ti ti-user"></i>
          <input type="email" name="email" class="form-control" placeholder="Email Address" required>
        </div>

        <div class="form-group">
          <i class="ti ti-lock"></i>
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-login">login</button>
      </form>
    </div>
  </div>

  <script src="{{ asset('libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
