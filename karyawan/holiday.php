<?php
require_once '../layout/_topkaryawan.php';
require_once '../helper/connection.php';

// Ambil data dari DB
$events = [];
$result = mysqli_query($connection, "SELECT nama_libur, tanggal_libur, jenis_libur FROM holiday");
while ($row = mysqli_fetch_assoc($result)) {
    $color = match ($row['jenis_libur']) {
        'Nasional' => '#28a745',       // Hijau
        'Cuti Bersama' => '#007bff',   // Biru
        'Custom' => '#ffc107',         // Kuning
        default => '#6c757d'           // Abu-abu
    };

    $events[] = [
        'title' => $row['nama_libur'],
        'start' => $row['tanggal_libur'],
        'color' => $color
    ];
}
?>

<!-- ✅ FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />

<!-- ✅ Custom Styling -->
<style>
  #calendar {
    max-width: 100%;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    min-height: 500px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .fc-toolbar-title {
    font-weight: bold;
    font-size: 1.2rem;
  }
</style>

<section class="section">
  <div class="section-header">
    <h1>Kalender Hari Libur</h1>
  </div>

  <div class="card">
    <div class="card-body">
      <div id="calendar"></div>
    </div>
  </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<!-- ✅ FullCalendar JS (versi global) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<!-- ✅ Inisialisasi Calendar -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      height: 600,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,listMonth'
      },
      events: <?= json_encode($events, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
    });

    console.log("Events loaded:", <?= json_encode($events); ?>); // Debug
    calendar.render();
  });
</script>
