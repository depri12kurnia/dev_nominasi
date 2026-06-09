<!-- Main Content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Manajemen Nominasi Camaba
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="filter_prodi">Prodi:</label>
                        <select id="filter_prodi" class="form-control form-control-sm">
                            <option value="">Semua Prodi</option>
                            <option value="Program Studi D-III Kebidanan (Diploma III)">Program Studi D-III Kebidanan (Diploma III)</option>
                            <option value="Program Studi D-III Keperawatan (Diploma III)">Program Studi D-III Keperawatan (Diploma III)</option>
                            <option value="Program Studi D-III Teknologi Laboratorium Medis (Diploma III)">Program Studi D-III Teknologi Laboratorium Medis (Diploma III)</option>
                            <option value="Program Studi Sarjana Terapan Kebidanan (Diploma IV)">Program Studi Sarjana Terapan Kebidanan (Diploma IV)</option>
                            <option value="Program Studi Sarjana Terapan Keperawatan (Diploma IV)">Program Studi Sarjana Terapan Keperawatan (Diploma IV)</option>
                            <option value="Program Studi Sarjana Terapan Promosi Kesehatan (Diploma IV)">Program Studi Sarjana Terapan Promosi Kesehatan (Diploma IV)</option>
                            <option value="Program Studi Sarjana Terapan Fisioterapi (Diploma IV)">Program Studi Sarjana Terapan Fisioterapi (Diploma IV)</option>
                            <option value="Program Studi Sarjana Terapan TLM (Diploma IV)">Program Studi Sarjana Terapan TLM (Diploma IV)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_kelas">Pilihan :</label>
                        <select id="filter_kelas" class="form-control form-control-sm">
                            <option value="">Semua Kelas</option>
                            <option value="Reguler">Reguler</option>
                            <option value="Internasional">Internasional</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_pilihan">Pilihan :</label>
                        <select id="filter_pilihan" class="form-control form-control-sm">
                            <option value="">Semua Pilihan</option>
                            <option value="1"> 1</option>
                            <option value="2"> 2</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label><br>
                        <button type="button" id="btn_filter" class="btn btn-primary btn-sm">Filter</button>
                        <button type="button" id="btn_reset" class="btn btn-secondary btn-sm">Reset</button>
                    </div>
                </div>
                <div class="row mb-4" id="panel_kuota">
                    <div class="col-md-12">
                        <div class="info-box bg-light border border-info">
                            <span class="info-box-icon bg-info"><i class="fas fa-chart-pie"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text text-bold text-info" id="nama_prodi_kuota">Nama Prodi</span>
                                <span class="info-box-text text-bold text-info" id="kelas">Kelas</span>
                                <span class="info-box-number mt-1">
                                    <span class="badge badge-success text-sm py-1 px-2">UTAMA: <span id="lbl_terisi_utama">0</span> / <span id="lbl_kuota_utama">0</span> Terisi</span>
                                    &nbsp;&nbsp;
                                    <span class="badge badge-warning text-sm py-1 px-2">CADANGAN: <span id="lbl_terisi_cadangan">0</span> / <span id="lbl_kuota_cadangan">0</span> Terisi</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Header & Export Action -->
                <div class="row mb-3">
                    <div class="col-md-12 text-right">
                        <button type="button" id="btn_import_excel" class="btn btn-primary btn-sm">
                            <i class="fas fa-file-excel"></i> Import dari Excel
                        </button>
                        <button type="button" id="btn_export_excel_utama" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Utama
                        </button>
                        <button type="button" id="btn_export_excel_cadangan" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-excel"></i> Export Cadangan
                        </button>
                        <button type="button" id="btn_refresh" class="btn btn-secondary btn-sm">
                            <i class="fas fa-sync"></i> Refresh Data
                        </button>
                    </div>
                </div>
                <!-- End Header & Export Action -->

                <div class="table-responsive">
                    <table id="nominasi_table" class="table table-bordered table-striped small">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No. Ujian</th>
                                <th>Nama Lengkap</th>
                                <th>No. Pendaftaran</th>
                                <th>Asal Sekolah</th>
                                <th>Jurusan Sekolah</th>
                                <th>Skor</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Import Excel -->
<div class="modal fade" id="modal-import">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import Data Nominasi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/nominasi/import_excel'); ?>" method="post" enctype="multipart/form-data">

                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="file_excel">Upload File (.csv)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file_excel" name="file_excel" accept=".csv" required>
                            <label class="custom-file-label" for="file_excel">Pilih file CSV</label>
                        </div>
                        <small class="form-text text-muted">Pastikan format kolom sesuai dengan template Excel yang diekspor ke CSV.</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Proses Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<input type="hidden" id="csrf_token" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

<script>
    var table;
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';

    function getCsrfToken() {
        return $('#csrf_token').val();
    }

    // Tambahkan parameter 'nama' di sini
    function prosesUbahStatus(id, pilihanFilter, jenis, nama, prodi, kelasFilter) {
        // 1. Perbaikan: Gunakan parameter yang dikirim (kelasFilter), 
        // jangan gunakan variabel 'kelas' sebelum didefinisikan.
        console.log("Diterima - ID: " + id + ", Nama: " + nama + ", Prodi: " + prodi + ", Kelas: " + kelasFilter);
        console.log("Filter yang diterima:", pilihanFilter);

        // 2. Sekarang definisikan variabel 'kelas' berdasarkan input
        let pilihan = (pilihanFilter === '2') ? '2' : '1';
        let kelas = (kelasFilter === 'Internasional') ? 'Internasional' : 'Reguler';

        let statusValue = pilihan + '_' + jenis;
        console.log("Status yang dikirim ke server:", statusValue);

        Swal.fire({
            title: 'Konfirmasi',
            // 3. Gunakan variabel 'kelas' yang baru saja dibuat
            html: `Tetapkan <strong>${nama}</strong> sebagai <strong>${jenis}</strong> pada prodi <strong>${prodi}</strong> Kelas : <strong>${kelas}</strong> (Pilihan ${pilihan})?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= site_url('admin/nominasi/ajax_update_status'); ?>",
                    type: "POST",
                    dataType: "JSON", // Tambahkan ini agar res langsung terbaca sebagai objek
                    data: {
                        id: id,
                        status: statusValue,
                        prodi_manual: prodi,
                        nama: nama,
                        pilihan: pilihan,
                        kelas: kelas, // Mengirim kelas yang sudah diproses
                        "<?= $this->security->get_csrf_token_name(); ?>": getCsrfToken()
                    },
                    success: function(res) {
                        // Cek status dari server
                        if (res.status === false) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: res.message,
                                confirmButtonText: 'Mengerti'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Status telah disimpan.',
                                timer: 1500
                            });
                            table.ajax.reload(null, false);
                            $('#filter_prodi').trigger('change');
                        }
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#nominasi_table')) {
            table = $('#nominasi_table').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": false,
                "autoWidth": false,
                "pageLength": 10,
                "order": [
                    [6, "DESC"]
                ],
                "ajax": {
                    "url": "<?= site_url('admin/nominasi/ajax_list') ?>",
                    "type": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    "data": function(d) {
                        d[csrfName] = getCsrfToken();
                        // MENGIRIM DATA FILTER KE SERVER
                        d.prodi = $('#filter_prodi').val();
                        d.pilihan = $('#filter_pilihan').val();
                        d.kelas = $('#filter_kelas').val();
                    },
                    "dataSrc": function(json) {
                        if (json.csrf_token) {
                            $('#csrf_token').val(json.csrf_token);
                        }
                        return json.data;
                    },
                    "error": function(xhr, error, thrown) {
                        console.log('DataTable AJAX Error:', error, xhr.responseText);
                    }
                }
            });
        }
        // Action Tombol FILTER
        $('#btn_filter').click(function() {
            table.ajax.reload(); // Reload tabel beserta parameter filter baru
        });

        // Action Tombol RESET FILTER
        $('#btn_reset').click(function() {
            $('#filter_prodi').val('');
            $('#filter_pilihan').val('');
            $('#filter_kelas').val('');
            table.ajax.reload();
        });

        // Refresh button
        $('#btn_refresh').click(function() {
            table.ajax.reload(null, false);
        });

        $('#filter_prodi, #filter_kelas').on('change', function() {
            var prodi = $('#filter_prodi').val();
            var kelas = $('#filter_kelas').val();

            if (prodi !== '') {
                $.ajax({
                    url: "<?= site_url('admin/nominasi/ajax_get_kuota') ?>",
                    type: "POST",
                    data: {
                        prodi: prodi,
                        kelas: kelas,
                        // Gunakan variabel CSRF Name dari CodeIgniter Anda
                        "<?= $this->security->get_csrf_token_name(); ?>": getCsrfToken()
                    },
                    dataType: "JSON",
                    success: function(res) {
                        $('#csrf_token').val(res.csrf_token); // Update token form

                        // Render Teks
                        $('#nama_prodi_kuota').text(prodi);
                        $('#kelas').text(kelas);
                        $('#lbl_kuota_utama').text(res.kuota_utama);
                        $('#lbl_kuota_cadangan').text(res.kuota_cadangan);
                        $('#lbl_terisi_utama').text(res.terisi_utama);
                        $('#lbl_terisi_cadangan').text(res.terisi_cadangan);

                        // Efek merah jika kuota utama penuh
                        if (parseInt(res.terisi_utama) >= parseInt(res.kuota_utama) && parseInt(res.kuota_utama) > 0) {
                            $('#lbl_terisi_utama').parent().removeClass('badge-success').addClass('badge-danger');
                        } else {
                            $('#lbl_terisi_utama').parent().removeClass('badge-danger').addClass('badge-success');
                        }

                        // Tampilkan panel
                        $('#panel_kuota').slideDown();
                    }
                });
            } else {
                $('#panel_kuota').slideUp();
            }
        });

        // Export Excel dengan membawa Parameter Filter
        $(document).on('click', '#btn_export_excel', function() {
            var prodi = $('#filter_prodi').val();
            var pilihan = $('#filter_pilihan').val();
            var kelas = $('#filter_kelas').val();

            // Build URL dengan parameter GET
            var url = "<?= site_url('admin/nominasi/export_excel') ?>?prodi=" + encodeURIComponent(prodi) + "&pilihan=" + encodeURIComponent(pilihan) + "&kelas=" + encodeURIComponent(kelas);
            window.open(url, '_blank');
        });

        // Export Excel dengan membawa Parameter Filter
        $(document).on('click', '#btn_export_excel_utama', function() {
            var prodi = $('#filter_prodi').val();
            var pilihan = $('#filter_pilihan').val();
            var kelas = $('#filter_kelas').val();

            // Build URL dengan parameter GET
            var url = "<?= site_url('admin/nominasi/export_excel_utama') ?>?prodi=" + encodeURIComponent(prodi) + "&pilihan=" + encodeURIComponent(pilihan) + "&kelas=" + encodeURIComponent(kelas);
            window.open(url, '_blank');
        });

        $(document).on('click', '#btn_export_excel_cadangan', function() {
            var prodi = $('#filter_prodi').val();
            var pilihan = $('#filter_pilihan').val();
            var kelas = $('#filter_kelas').val();

            // Build URL dengan parameter GET
            var url = "<?= site_url('admin/nominasi/export_excel_utama_cadangan') ?>?prodi=" + encodeURIComponent(prodi) + "&pilihan=" + encodeURIComponent(pilihan) + "&kelas=" + encodeURIComponent(kelas);
            window.open(url, '_blank');
        });

        // Action Simpan Keputusan
        $('#btnSave').on('click', function() {
            var id = $('[name="id"]').val();
            var status = $('input[name="status_kelulusan"]:checked').val();

            if (typeof status === "undefined") {
                alert('Silakan pilih status kelulusan terlebih dahulu.');
                return;
            }

            if (confirm('Apakah Anda yakin ingin menyimpan status ini?')) {
                var postData = {
                    id: id,
                    status: status
                };
                postData[csrfName] = getCsrfToken();

                $.ajax({
                    url: "<?= site_url('admin/nominasi/ajax_update_status'); ?>", // Sesuaikan jika pakai prefix 'admin/'
                    type: "POST",
                    data: postData,
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    dataType: "json",
                    success: function(res) {
                        if (!res.status) {
                            alert('Error: ' + (res.message || 'Gagal mengupdate data'));
                            return;
                        }
                        if (res.csrf_token) {
                            $('#csrf_token').val(res.csrf_token);
                        }
                        $('#modal_detail').modal('hide');
                        table.ajax.reload(null, false); // Reload tabel
                        $('#filter_prodi').trigger('change');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('AJAX error: ' + textStatus);
                    }
                });
            }
        });
        // Trigger untuk memunculkan Modal Import Excel
        $('#btn_import_excel').click(function() {
            // Reset form setiap kali modal dibuka agar bersih dari sisa file sebelumnya
            $('#modal-import form')[0].reset();
            $('.custom-file-label').html('Pilih file CSV');

            $('#modal-import').modal('show');
        });

        // Menampilkan nama file pada input custom AdminLTE saat file diunggah
        $('#file_excel').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            } else {
                $(this).next('.custom-file-label').removeClass("selected").html('Pilih file CSV');
            }
        });
    });

    function resetStatus(id, nama) {
        Swal.fire({
            title: 'Konfirmasi Reset',
            html: `Apakah Anda yakin ingin <strong>mereset status kelulusan</strong> untuk <strong>${nama}</strong>? Data akan dikosongkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reset!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= site_url('admin/nominasi/ajax_reset_status'); ?>",
                    type: "POST",
                    data: {
                        id: id,
                        "<?= $this->security->get_csrf_token_name(); ?>": getCsrfToken()
                    },
                    success: function(res) {
                        Swal.fire('Berhasil!', 'Status telah direset.', 'success');
                        table.ajax.reload(null, false);
                        $('#filter_prodi').trigger('change');
                    }
                });
            }
        });
    }
</script>