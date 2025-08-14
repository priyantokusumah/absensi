<?php
$current_page = basename($_SERVER['PHP_SELF']);
$is_setting_page = strpos($_SERVER['PHP_SELF'], '/setting/') !== false;

// Logo dynamic (biar nggak ke-cache)
$logo_path = '../img/logo.png';
$logo_version = file_exists($logo_path) ? filemtime($logo_path) : time();
$logo_url = $logo_path . '?v=' . $logo_version;

?>

<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="">
        <img src="../img/logo.png" alt="image" width="25%">
      </a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="">
        <img src="../img/logo.png" alt="image" width="70%">
      </a>
    </div>

    <ul class="sidebar-menu">
      <li class="menu-header">Dashboard</li>
       <li class="<?= $current_page == 'index.php' && strpos($_SERVER['PHP_SELF'], 'index') !== false ? 'active' : '' ?>">
      <li><a class="nav-link" href="../karyawan/index.php"><i class="fas fa-fire"></i> <span>Home</span></a></li>
    
      <li class="menu-header">Main Feature</li>
    <li>
        <li class="<?= $current_page == 'absensi_pegawai.php' && strpos($_SERVER['PHP_SELF'], 'absensi_pegawai') !== false ? 'active' : '' ?>">
        <a href="../karyawan/absensi_pegawai.php" class="nav-link"><i class="fas fa-user"></i> <span>Absensi Karyawan</span></a>
      </li>
      <li>
        <li class="<?= $current_page == 'izin.php' && strpos($_SERVER['PHP_SELF'], 'izin') !== false ? 'active' : '' ?>">
        <a href="../karyawan/izin.php" class="nav-link"><i class="fas fa-tasks"></i> <span>Izin Karyawan</span></a>
      </li>
      <li>
        <li class="<?= $current_page == 'cuti.php' && strpos($_SERVER['PHP_SELF'], 'cuti') !== false ? 'active' : '' ?>">
        <a href="../karyawan/cuti.php" class="nav-link"><i class="fas fa-calendar"></i> <span>Cuti Karyawan</span></a>
      </li>
         <li>
        <li class="<?= $current_page == 'onsite.php' && strpos($_SERVER['PHP_SELF'], 'onsite') !== false ? 'active' : '' ?>">
        <a href="../karyawan/onsite.php" class="nav-link"><i class="fas fa-city"></i> <span>Onsite Karyawan</span></a>
      </li>
      <li class="<?= $current_page == 'holiday.php' && strpos($_SERVER['PHP_SELF'], 'holiday') !== false ? 'active' : '' ?>">
        <a href="../karyawan/holiday.php" class="nav-link">
          <i class="fas fa-calendar-alt"></i> <span>Kalender Hari Libur</span>
        </a>
      </li>
      </ul>
  </aside>
</div>