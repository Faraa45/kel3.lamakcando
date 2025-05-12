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

  <link rel="shortcut icon" type="image/png" href="{{asset('images/logos/favicon.png')}}" />
  <link rel="stylesheet" href="{{asset('css/styles.min.css')}}" />

  <style>
    body {
      background: linear-gradient(135deg, #f8f9fa, #cfe2f3);
    }


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

    .card {
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .card-body {
      padding: 2.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    h3 {
      font-weight: bold;
      margin-bottom: 1.5rem;
      color: #2c3e50;
      text-align: center;
    }

    form {
      width: 100%;
    }

    .form-label {
      font-weight: 600;
    }

    .btn-primary {
      background-color: #2c3e50;
      border-color: #2c3e50;
    }

    .btn-primary:hover {
      background-color: #1a252f;
    }

    .error-msg {
      color: red;
      margin-bottom: 1rem;

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

    <div class="card col-md-8 col-lg-6 col-xxl-4">
      <div class="card-body">
        <!-- Judul -->
        <h3>Lamak Cando Balala</h3>

        <!-- Alert Error -->
        @if ($errors->any())
          <div class="error-msg">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ url('/login') }}">
          @csrf
          <div class="mb-3">
            <label for="email" class="form-label">Username</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-2 fs-5 mb-3 rounded-2">Login</button>
        </form>
      </div>
    </div>
  </div>


</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="./index.html" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="{{asset('images/logos/mukena.PNG')}}" width="180" alt="">
                </a>

                <!-- Tambahan alert -->
                @if ($errors->any())
                    <div style="color: red;">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ url('/login') }}">
                  @csrf
                  <div class="mb-3">
                    <label for="email" class="form-label">Username</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                  </div>
                 
                  <!-- <a href="./index.html" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign In</a> -->
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Login</button>
                  <div class="d-flex align-items-center justify-content-center">
                    <!-- <p class="fs-4 mb-0 fw-bold">New to Modernize?</p> -->
                    <!-- <a class="text-primary fw-bold ms-2" href="./authentication-register.html">Create an account</a> -->
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{asset('libs/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>

