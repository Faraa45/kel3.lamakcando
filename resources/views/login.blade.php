<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lamak Cando Balala</title>
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

  <script src="{{asset('libs/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>