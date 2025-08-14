<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$conn = $connection ?? null;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['role'] !== 'Manajemen') {
    header('Location: ../login.php');
    exit();
}
// Format tanggal dd-NamaBulan-YYYY
function formatTanggal($tanggal) {
    if (!$tanggal) return '-';

    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $tgl = date('d', strtotime($tanggal));
    $bln = (int)date('m', strtotime($tanggal));
    $thn = date('Y', strtotime($tanggal));

    return $tgl . ' ' . $bulan[$bln] . ' ' . $thn;
}
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Manajemen Hari Libur</h1>
        <a href="./form_holiday.php" class="btn btn-primary">+ Tambah Hari Libur</a>    
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped w-100" id="table-1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Libur</th>
                                    <th>Tanggal Libur</th>
                                    <th>Jenis</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($conn, "SELECT * FROM holiday ORDER BY tanggal_libur DESC");
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $tanggal = $row['tanggal_libur']
                                        ? date('d-m-Y', strtotime($row['tanggal_libur']))
                                        : '';

                                    $badge = match ($row['jenis_libur']) {
                                        'Nasional' => 'badge-success',
                                        'Cuti Bersama' => 'badge-info',
                                        'Custom' => 'badge-warning',
                                        default => 'badge-secondary',
                                    };
                                ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['nama_libur']); ?></td>
                                         <td><?php echo formatTanggal($row['tanggal_libur']); ?></td>
                                        <td><span class="badge <?= $badge; ?>"><?= $row['jenis_libur']; ?></span></td>
                                        <td>
                                            <a href="edit_holiday.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $row['id']; ?>)">Hapus</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if (mysqli_num_rows($result) === 0): ?>
                                    <tr><td colspan="5" class="text-center text-muted">Belum ada data hari libur</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<!-- SweetAlert & Delete Logic -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Yakin hapus data ini?',
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "delete_holiday.php?id=" + id;
        }
    });
}
</script>

<?php if (isset($_SESSION['info'])): ?>
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: '<?= $_SESSION['info']['status'] === 'success' ? 'success' : 'error'; ?>',
    title: '<?= $_SESSION['info']['message']; ?>',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
</script>
<?php unset($_SESSION['info']); ?>
<?php endif; ?>

<script src="../assets/js/page/modules-datatables.js"></script>
