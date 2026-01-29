<div style="display: flex; flex: 1; width: 100%;">
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

      <a href="{{ route('login.form', ['type' => 'guardian']) }}" class="login-btn">
        <i class="bi bi-person"></i>
        Guardian Login
      </a>
      <a href="{{ route('login.form', ['type' => 'teacher']) }}" class="login-btn">
        <i class="bi bi-person-badge"></i>
        Teacher Login
      </a>
      <a href="{{ route('login.form', ['type' => 'admin']) }}" class="login-btn">
        <i class="bi bi-shield-lock"></i>
        Admin Login
      </a>

    </div>
  </div>
</div>
