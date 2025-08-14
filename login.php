<?php
require_once 'helper/connection.php';
session_start();

// Atur timezone ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

$old_username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $old_username = $username;

  // Cek user berdasarkan username
  $sql_user = "SELECT * FROM pegawai WHERE username='$username' LIMIT 1";
  $result_user = mysqli_query($connection, $sql_user);

  if ($result_user && mysqli_num_rows($result_user) > 0) {
    $row_user = mysqli_fetch_assoc($result_user);

    // Cek apakah password benar
    if (password_verify($password, $row_user['password'])) {
      
      // Cek apakah status user
     if ($row_user['status'] === 'Tidak Aktif') {
      echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
            icon: 'warning',
            title: 'Login Gagal',
            text: 'Akun Anda Sudah Tidak Aktif. Silahkan Hubungi Admin.'
          });
        });
      </script>";
    } elseif ($row_user['status'] === 'Pending') {
      echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
            icon: 'info',
            title: 'Login Tertunda',
            text: 'Akun Anda sedang menunggu persetujuan. Silakan tunggu atau hubungi Admin.'
          });
        });
      </script>";

      } else {
        // Set session jika login sukses
        $_SESSION['login'] = true;
        $_SESSION['role'] = $row_user['role'];
        $_SESSION['username'] = $row_user['username'];
        $_SESSION['id_pegawai'] = $row_user['id_pegawai'];

        // Redirect sesuai role
        if ($row_user['role'] == 'Karyawan') {
          header('Location: /phire_absensi/karyawan/index.php');
        } elseif ($row_user['role'] == 'Manajemen') {
          header('Location: /phire_absensi/admin/index.php');
        }
        exit();
      }
    } else {
      echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: 'Username atau Password salah!'
          });
        });
      </script>";
    }
  } else {
    echo "<script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'error',
          title: 'Login Gagal',
          text: 'User tidak ditemukan!'
        });
      });
    </script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login Absensi Phire Studio</title>

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="card card-primary">
              <div class="login-brand">
                <img src="./img/image.jpeg" alt="image" width="50%">
              </div>

              <div class="card-header">
                <h4 class="text-center">Login Absensi Phire Studio</h4>
              </div>

              <div class="card-body">
                <form method="POST" action="" class="needs-validation" novalidate="">
                  <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" type="text" class="form-control" name="username" required autofocus value="<?php echo htmlspecialchars($old_username); ?>">
                    <div class="invalid-feedback">
                      Mohon isi username
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                      <input id="password" type="password" class="form-control" name="password" required>
                      <div class="input-group-append">
                        <button type="button" id="togglePassword">
                          <i class="fa fa-eye-slash"></i>
                        </button>
                      </div>
                    </div>
                    <div class="invalid-feedback">
                      Mohon isi kata sandi
                    </div>
                  </div>

                  <div class="form-group">
                    <button name="submit" type="submit" class="btn btn-primary btn-lg btn-block">LOGIN</button>
                  </div>
                </form>
              </div>
            </div>

            <div class="simple-footer">
              Copyright &copy; 2025 Support Team Phire Studio
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- JS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    #togglePassword {
      border: none;
      background: none;
      cursor: pointer;
      padding: 0 10px;
      display: flex;
      align-items: center;
    }

    #togglePassword i {
      font-size: 18px;
      color: #6c757d;
    }

    #togglePassword:focus {
      outline: none;
      box-shadow: none;
    }
  </style>

  <script>
    document.getElementById("togglePassword").addEventListener("click", function () {
      let passwordField = document.getElementById("password");
      let icon = this.querySelector("i");

      if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      } else {
        passwordField.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      }
    });
  </script>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
