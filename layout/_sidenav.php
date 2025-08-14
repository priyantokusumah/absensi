<?php
require_once '../helper/connection.php';

$current_page = basename($_SERVER['PHP_SELF']);
$is_setting_page = strpos($_SERVER['PHP_SELF'], '/setting/') !== false;

// Logo dynamic
$logo_path = '../img/logo.png';
$logo_version = file_exists($logo_path) ? filemtime($logo_path) : time();
$logo_url = $logo_path . '?v=' . $logo_version;

// Ambil jumlah izin pending
$q_izin = mysqli_query($connection, "SELECT COUNT(*) AS total FROM izin WHERE status = 'Pending'");
$izin_pending = mysqli_fetch_assoc($q_izin)['total'] ?? 0;

// Ambil jumlah cuti pending
$q_cuti = mysqli_query($connection, "SELECT COUNT(*) AS total FROM cuti WHERE status = 'Pending'");
$cuti_pending = mysqli_fetch_assoc($q_cuti)['total'] ?? 0;
?>

<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="">
        <img src="<?= $logo_url ?>" alt="image" width="25%">
      </a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="">
        <img src="<?= $logo_url ?>" alt="image" width="70%">
      </a>
    </div>

    <ul class="sidebar-menu">
      <li class="menu-header">Dashboard</li>
      <li class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
        <a class="nav-link" href="../">
          <i class="fas fa-fire"></i> <span>Home</span>
        </a>
      </li>

      <li class="menu-header">Main Feature</li>
      <li class="<?= strpos($_SERVER['PHP_SELF'], '/data_pegawai/') !== false ? 'active' : '' ?>">
        <a href="../data_pegawai/index.php" class="nav-link">
        <span ><i class="fas fa-user"></i> Data Karyawan</span>
        </a>
      </li>

      <li class="<?= strpos($_SERVER['PHP_SELF'], '/izin/') !== false ? 'active' : '' ?>">
        <a href="../izin/index.php" class="nav-link d-flex align-items-center justify-content-between">
          <span><i class="fas fa-tasks"></i> Izin Karyawan</span>
          <?php if ($izin_pending > 0): ?>
            <span class="badge badge-warning rounded-circle" style="width: 24px; height: 24px; padding: 6px; text-align: center; font-size: 12px;">
              <?= $izin_pending ?>
            </span>
          <?php endif; ?>
        </a>
      </li>

      <li class="<?= strpos($_SERVER['PHP_SELF'], '/cuti/') !== false ? 'active' : '' ?>">
        <a href="../cuti/index.php" class="nav-link d-flex align-items-center justify-content-between">
          <span><i class="fas fa-calendar"></i> Cuti Karyawan</span>
          <?php if ($cuti_pending > 0): ?>
            <span class="badge badge-danger rounded-circle" style="width: 24px; height: 24px; padding: 6px; text-align: center; font-size: 12px;">
              <?= $cuti_pending ?>
            </span>
          <?php endif; ?>
        </a>
      </li>

      <li class="<?= strpos($_SERVER['PHP_SELF'], '/onsite/') !== false ? 'active' : '' ?>">
        <a href="../onsite/index.php" class="nav-link">
        <span><i class="fas fa-city"></i>Onsite Karyawan</span>
        </a>
      </li>

      <li class="<?= strpos($_SERVER['PHP_SELF'], '/report/') !== false ? 'active' : '' ?>">
        <a href="../report/index.php" class="nav-link">
          <span><i class="fas fa-file"></i>Report Absensi</span>
        </a>
      </li>

      <li class="menu-header">Management Feature</li>
      <li class="nav-item dropdown <?= $is_setting_page ? 'active' : '' ?>">
        <a href="#" class="nav-link has-dropdown <?= $is_setting_page ? 'active' : '' ?>">
        <span><i class="fas fa-cog"></i>Pengaturan</span>
        </a>

        <ul class="dropdown-menu <?= $is_setting_page ? 'show' : '' ?>">
          <li class="<?= $current_page == 'header.php' ? 'active' : '' ?>">
              <a class="nav-link d-flex align-items-center" href="../setting/header.php">
                <i class="fas fa-heading mr-2"></i>
                <span>Setting Header</span>
              </a>
          </li>

          <!-- <li class="<?= $current_page == 'logo.php' ? 'active' : '' ?>">
              <a class="nav-link d-flex align-items-center" href="../setting/logo.php">
                <i class="fas fa-image mr-2"></i>
                <span>Pengaturan Logo</span>
              </a>
          </li> -->

          <li class="<?= $current_page == 'holiday_management.php' ? 'active' : '' ?>">
              <a class="nav-link d-flex align-items-center" href="../setting/holiday_management.php">
                <i class="fas fa-calendar mr-2"></i>
                <span>Holiday Management</span>
              </a>
          </li>
          
          <li class="<?= $current_page == 'log_activity.php' ? 'active' : '' ?>">
              <a class="nav-link d-flex align-items-center" href="../setting/log_activity.php">
                <i class="fas fa-history mr-2"></i>
                <span>Log Activity</span>
              </a>
          </li>
        </ul>
      </li>
    </ul>
  </aside>
</div>
