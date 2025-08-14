"use strict";

$("[data-checkboxes]").each(function () {
  var me = $(this),
    group = me.data("checkboxes"),
    role = me.data("checkbox-role");

  me.change(function () {
    var all = $( '[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"])' ),
      checked = $( '[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"]):checked' ),
      dad = $('[data-checkboxes="' + group + '"][data-checkbox-role="dad"]'),
      total = all.length,
      checked_length = checked.length;

    if (role == "dad") {
      if (me.is(":checked")) {
        all.prop("checked", true);
      } else {
        all.prop("checked", false);
      }
    } else {
      if (checked_length >= total) {
        dad.prop("checked", true);
      } else {
        dad.prop("checked", false);
      }
    }
  });
});

// Hapus instance DataTables lama sebelum inisialisasi ulang
$("#table-1").DataTable().destroy();
$("#table-2").DataTable().destroy();

// Inisialisasi DataTables tanpa filter pencarian
$("#table-1").DataTable({
  "destroy": true,    // Pastikan bisa di-refresh ulang
  "searching": true, // Hilangkan fitur pencarian
  "paging": true,     // Aktifkan pagination
  "ordering": false,   // Bisa diurutkan
  "info": false,      // Sembunyikan info jumlah data
  "lengthChange": true, // Hilangkan dropdown jumlah data per halaman
  "pageLength": 10,   // Tampilkan 10 data per halaman
  "language": {
    "paginate": {
      "first": "Awal",
      "last": "Akhir",
      "next": "Berikutnya",
      "previous": "Sebelumnya"
    }
  }
});

$("#table-2").DataTable({
  "destroy": true,
  "searching": true,
  "paging": true,
  "ordering": false,
  "info": false,
  "lengthChange": false,
  "pageLength": 10,
  columnDefs: [{ sortable: false, targets: [0, 2, 3] }]
});
