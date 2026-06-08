<!-- login yang baru -->
<section id="login" class="page-section"
  style="padding-top: 100px; background-color: #EDF2F7; min-height: 100vh;">
  <div class="container py-5 d-flex align-items-center justify-content-center">
    <div class="card card-custom border-0 shadow-lg w-100 overflow-hidden"
      style="max-width: 420px; border-radius: 24px;">
      <div class="p-5 text-center text-white"
        style="background: linear-gradient(135deg, var(--pk-teal) 0%, #007A71 100%);">
        <i class="far fa-user-circle fs-1 mb-3"></i>
        <h4 class="fw-extrabold mb-1">Selamat Datang</h4>
        <p class="mb-0 text-white-50 small">Sistem Autentikasi Admin & Humas</p>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function() {

          // 1. Alert untuk Validation Errors (CodeIgniter)
          <?php if (validation_errors()) : ?>
            Swal.fire({
              icon: 'error',
              title: 'Validation Error',
              html: '<?php echo str_replace(["\r", "\n"], '', validation_errors()); ?>',
              confirmButtonColor: '#3085d6',
            });
          <?php endif; ?>

          // 2. Alert untuk Flashdata Message
          <?php if ($this->session->flashdata('message')) : ?>
            Swal.fire({
              icon: 'warning',
              title: 'Attention',
              text: '<?php echo $this->session->flashdata('message'); ?>',
              confirmButtonColor: '#3085d6',
            });
          <?php endif; ?>

          // 3. Tambahan: Alert untuk Flashdata Success (Opsional tapi sering dipakai)
          <?php if ($this->session->flashdata('success')) : ?>
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: '<?php echo $this->session->flashdata('success'); ?>',
              timer: 3000,
              showConfirmButton: false
            });
          <?php endif; ?>

        });
      </script>

      <div class="card-body p-5 bg-white">
        <?php echo form_open("auth/login"); ?>
        <div class="mb-3">
          <label class="form-label small fw-bold text-dark">Username / Email</label>
          <input type="text" name="identity" required class="form-control form-control-custom text-center"
            placeholder="Masukkan username">
        </div>
        <div class="mb-4">
          <label class="form-label small fw-bold text-dark">Password</label>
          <input type="password" name="password" required class="form-control form-control-custom text-center"
            placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-pk-primary w-100 rounded-pill py-3 fw-bold">Masuk
          Aplikasi</button>
        <?php echo form_close(); ?>
        <div style="margin-top: 15px; text-align: center;">
          <p>Login tamu silahkan menggunakan:</p>
          <a href="<?php echo $google_login_url; ?>">
            <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Google Sign In" style="width: 200px;">
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?= htmlspecialchars($page_title ?? $website->name) ?> |
    <?= htmlspecialchars($website->name) ?>
  </title>
  <!-- Bootstrap 5.3.0 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS -->
  <style>
    body {
      background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      overflow: hidden;
      max-width: 450px;
      width: 100%;
    }

    .login-header {
      background: linear-gradient(135deg, #00B9AD 0%, #60C0D0 100%);
      padding: 2rem;
      text-align: center;
      color: white;
    }

    .login-header h2 {
      margin: 0;
      font-weight: 600;
      font-size: 1.8rem;
    }

    .login-header p {
      margin: 0.5rem 0 0 0;
      opacity: 0.9;
    }

    .login-body {
      padding: 2rem;
    }

    .form-floating {
      position: relative;
    }

    .form-floating>.form-control {
      border-radius: 12px;
      border: 2px solid #e3e6f0;
      padding: 1rem 0.75rem;
      height: calc(3.5rem + 2px);
    }

    .form-floating>.form-control:focus {
      border-color: #00B9AD;
      box-shadow: 0 0 0 0.2rem rgba(0, 185, 173, 0.25);
    }

    .form-floating>label {
      padding: 1rem 0.75rem;
      color: #6c757d;
    }

    .form-floating.position-relative .form-control {
      padding-right: 3rem;
    }

    .password-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #6c757d;
      cursor: pointer;
      z-index: 5;
      padding: 0.25rem;
      border-radius: 4px;
      transition: color 0.3s ease;
    }

    .password-toggle:hover {
      color: #00B9AD;
    }

    .btn-login {
      background: linear-gradient(135deg, #00B9AD 0%, #60C0D0 100%);
      border: none;
      border-radius: 12px;
      padding: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      width: 100%;
      color: white;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 185, 173, 0.4);
      color: white;
    }

    .form-check {
      margin: 1.5rem 0;
    }

    .form-check-input:checked {
      background-color: #00B9AD;
      border-color: #00B9AD;
    }

    .forgot-password {
      color: #00B9AD;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .forgot-password:hover {
      color: #60C0D0;
    }

    .alert {
      border-radius: 12px;
      border: none;
      margin-bottom: 1rem;
    }

    .alert-danger {
      background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
      color: white;
    }

    @media (max-width: 576px) {
      .login-header {
        padding: 1.5rem;
      }

      .login-body {
        padding: 1.5rem;
      }

      .login-header h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h2><i class="fas fa-graduation-cap me-2"></i>Jurusan <?= htmlspecialchars($website->name) ?></h2>
        <p>Silakan masuk ke akun Anda</p>
      </div>
      <div class="login-body">
        <?php if (validation_errors()) : ?>
          <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo validation_errors(); ?>
          </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('message')) : ?>
          <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo $this->session->flashdata('message'); ?>
          </div>
        <?php endif; ?>

        <?php echo form_open("auth/login", ['class' => 'needs-validation', 'novalidate' => '']); ?>

        <div class="form-floating mb-3">
          <input type="email"
            class="form-control"
            id="floatingEmail"
            name="identity"
            placeholder="name@example.com"
            required>
          <label for="floatingEmail">
            <i class="fas fa-envelope me-2"></i>Alamat Email
          </label>
          <div class="invalid-feedback">
            Silakan masukkan email yang valid.
          </div>
        </div>

        <div class="form-floating mb-3 position-relative">
          <input type="password"
            class="form-control"
            id="floatingPassword"
            name="password"
            placeholder="Password"
            required>
          <label for="floatingPassword">
            <i class="fas fa-lock me-2"></i>Kata Sandi
          </label>
          <button type="button" class="password-toggle" id="togglePassword">
            <i class="fas fa-eye-slash"></i>
          </button>
          <div class="invalid-feedback">
            Silakan masukkan kata sandi.
          </div>
        </div>

        <div class="form-check">
          <input class="form-check-input"
            type="checkbox"
            name="remember"
            id="remember"
            value="1">
          <label class="form-check-label" for="remember">
            Ingat saya
          </label>
        </div>

        <button type="submit" class="btn btn-primary btn-login">
          <i class="fas fa-sign-in-alt me-2"></i>Masuk
        </button>

        <div class="text-center mt-3">
          <a href="<?php echo site_url('auth/forgot_password'); ?>" class="forgot-password">
            <i class="fas fa-key me-1"></i>Lupa kata sandi?
          </a>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5.3.0 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom JavaScript -->
  <script>
    // Toggle Password Visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('floatingPassword');
      const toggleIcon = this.querySelector('i');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      }
    });

    // Form Validation
    (function() {
      'use strict';

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.querySelectorAll('.needs-validation');

      // Loop over them and prevent submission
      Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }

          form.classList.add('was-validated');
        }, false);
      });
    })();

    // Add floating label animation
    document.querySelectorAll('.form-floating input').forEach(function(input) {
      input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
      });

      input.addEventListener('blur', function() {
        if (this.value === '') {
          this.parentElement.classList.remove('focused');
        }
      });
    });

    // Add loading animation on form submit
    document.querySelector('form').addEventListener('submit', function() {
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;

      if (this.checkValidity()) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
        submitBtn.disabled = true;

        // Re-enable button after 3 seconds (in case of error)
        setTimeout(function() {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        }, 3000);
      }
    });

    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(function(alert) {
      setTimeout(function() {
        alert.style.opacity = '0';
        setTimeout(function() {
          alert.remove();
        }, 300);
      }, 5000);
    });

    // Add subtle animations on page load
    window.addEventListener('load', function() {
      document.querySelector('.login-card').style.animation = 'slideInUp 0.5s ease-out';
    });

    // Add CSS animation keyframes
    const style = document.createElement('style');
    style.textContent = `
            @keyframes slideInUp {
                from {
                    transform: translateY(30px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        `;
    document.head.appendChild(style);
  </script>
</body>

</html>