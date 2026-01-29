<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login | DPS Ruby Park</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Global CSS -->
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <!-- Responsive CSS -->
  <link rel="stylesheet" href="{{ asset('css/login-responsive.css') }}">
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

  <!-- Login Area -->
  <div class="login-area">
    <div class="login-box">
      <h2>Select Login</h2>

      <a href="{{ route('student.login') }}" class="login-btn">Student Login</a>
      <a href="{{ route('faculty.login') }}" class="login-btn">Faculty Login</a>
      <a href="{{ route('admin.login') }}" class="login-btn">Admin Login</a>
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
