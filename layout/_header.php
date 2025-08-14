<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['login'])) {
    header('Location: /phire_absensi/login.php');
    exit();
}

// Ambil username
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>

<!-- Link stylesheet untuk Dark Mode -->
<link rel="stylesheet" href="../theme/theme.css">

<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
  <form class="form-inline mr-auto">
    <ul class="navbar-nav mr-3">
      <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
      <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
    </ul>
  </form>
  <ul class="navbar-nav navbar-right">

    <!-- Tombol Toggle Tema -->
    <li class="nav-item">
      <a href="#" class="nav-link nav-link-lg" id="toggle-theme" title="Ganti Tema">
        <i class="fas fa-moon" id="theme-icon"></i>
      </a>
    </li>

    <!-- Dropdown User -->
    <li class="dropdown">
      <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
        <img alt="image" src="../assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
        <div class="d-sm-none d-lg-inline-block">Hi, <?= htmlspecialchars($username) ?></div>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <a href="#" class="dropdown-item has-icon" id="btnChangePassword">
          <i class="fas fa-key"></i> Ganti Password
        </a>
        <a href="../logout.php" class="dropdown-item has-icon text-danger">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </li>
  </ul>
</nav>

<!-- SCRIPT JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../theme/theme.js"></script>

<!-- SCRIPT Ganti Password -->
<script>
$(document).ready(function() {
    $('#btnChangePassword').click(function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Ganti Password',
            html:
                '<input type="password" id="new_password" class="swal2-input" placeholder="Password Baru">' +
                '<input type="password" id="confirm_password" class="swal2-input" placeholder="Konfirmasi Password Baru">',
            confirmButtonText: 'Ganti Password',
            focusConfirm: false,
            preConfirm: () => {
                const newPassword = Swal.getPopup().querySelector('#new_password').value;
                const confirmPassword = Swal.getPopup().querySelector('#confirm_password').value;

                if (!newPassword || !confirmPassword) {
                    Swal.showValidationMessage(`Semua kolom harus diisi`);
                } else if (newPassword !== confirmPassword) {
                    Swal.showValidationMessage(`Password baru dan konfirmasi tidak cocok`);
                } else {
                    return { newPassword };
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'change_password.php',
                    type: 'POST',
                    data: { new_password: result.value.newPassword },
                    success: function(response) {
                        if (response === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Password berhasil diganti! Silakan login kembali dengan password baru 🙏🏻',
                                icon: 'success',
                                timer: 3000,
                                timerProgressBar: true,
                                willClose: () => {
                                    window.location.href = '../login.php';
                                }
                            });
                        } else {
                            Swal.fire('Gagal!', response, 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>
