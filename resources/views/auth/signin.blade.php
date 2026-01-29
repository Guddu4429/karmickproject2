<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sign In | DPS Ruby Park</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Global CSS -->
  <link rel="stylesheet" href="{{ asset('css/signin.css') }}">
  <!-- Responsive CSS -->
  <link rel="stylesheet" href="{{ asset('css/signin-responsive.css') }}">
</head>

<body class="login-body">

<div class="page-overlay">

  <!-- Left Branding -->
  <div class="branding-panel">
    <h1>
      Delhi Public School<br>
      Ruby Park
    </h1>
  </div>

  <!-- Sign In Area -->
  <div class="login-area">
    <div class="login-box signin-box">
      <div class="signin-header">
        <h2>Create Account</h2>
        <p class="signin-subtitle">Register a new user</p>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-circle"></i>
          <div class="alert-content">
            <strong>Please fix the following :</strong>
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      @if (session('success'))
        <div class="alert alert-success">
          <i class="bi bi-check-circle"></i>
          <div class="alert-content">{{ session('success') }}</div>
        </div>
      @endif

      <form method="POST" action="{{ route('signin.store') }}" class="signin-form">
        @csrf

        <div class="form-row">
          <div class="form-group">
            <label for="name">
              <i class="bi bi-person"></i>
              Full Name
            </label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter full name" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="email">
              <i class="bi bi-envelope"></i>
              Email Address
            </label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter email address" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="phone">
              <i class="bi bi-phone"></i>
              Phone Number <span class="optional">(Optional)</span>
            </label>
            <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Enter phone number">
          </div>
        </div>

        <div class="form-row form-row-split">
          <div class="form-group">
            <label for="password">
              <i class="bi bi-lock"></i>
              Password
            </label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
          </div>

          <div class="form-group">
            <label for="password_confirmation">
              <i class="bi bi-lock-fill"></i>
              Confirm Password
            </label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="login-btn btn-primary">
            <i class="bi bi-person-plus"></i>
            Create Account
          </button>
          
          <a href="{{ route('login') }}" class="login-btn login-btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Back to Login
          </a>
        </div>
      </form>
    </div>
  </div>

</div>

<footer class="login-footer">
  <div class="footer-inner">

    <!-- Copyright -->
    <div>
      <span>© Delhi Public School, Ruby Park. All rights reserved.</span>
    </div>

    <!-- Address -->
    <div class="footer-address">
      <strong>DPS Ruby Park</strong><br>
      Address: XYZ Road, PO Jadavpur, Kolkata – 700045<br>
      Phone: +91 12345 67890<br>
      Email: info@dpsrubypark.edu
    </div>

    <!-- Policies -->
    <div>
      <span>Privacy Policy & Cookies Policy</span>
    </div>

  </div>
</footer>


</body>
</html>
