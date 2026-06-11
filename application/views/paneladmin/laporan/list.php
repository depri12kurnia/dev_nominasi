<!-- Main Content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file mr-2"></i>
                    Laporan
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="filter_prodi">Prodi:</label>
                        <select id="filter_prodi" class="form-control form-control">
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
                        <select id="filter_kelas" class="form-control form-control">
                            <option value="">Semua Kelas</option>
                            <option value="Reguler">Reguler</option>
                            <option value="Internasional">Internasional</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_jenis">Jenis Pilihan :</label>
                        <select id="filter_jenis" class="form-control form-control">
                            <option value="">Semua Pilihan</option>
                            <option value="Utama">Utama</option>
                            <option value="Cadangan">Cadangan</option>
                        </select>
                    </div>

                    <div class="col-md-12 text-right">
                        <label>&nbsp;</label><br>
                        <button type="button" id="btn_filter" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                        <button type="button" id="btn_reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</button>
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
                                    <span class="badge badge-success text-sm py-1 px-2">UTAMA: <span id="lbl_terisi_utama">0</span> / <span id="lbl_kuota_utama">0</span></span>
                                    &nbsp;&nbsp;
                                    <span class="badge badge-warning text-sm py-1 px-2">CADANGAN: <span id="lbl_terisi_cadangan">0</span> / <span id="lbl_kuota_cadangan">0</span> </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Header & Export Action -->
                <div class="row mb-3">
                    <div class="col-md-12 text-right">
                        <button type="button" id="btn_export_excel" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                </div>
                <!-- End Header & Export Action -->

                <div class="table-responsive">
                    <table id="nominasi_table" class="table table-bordered table-striped">
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
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<input type="hidden" id="csrf_token" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

<script>
    var table;
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';

    function getCsrfToken() {
        return $('#csrf_token').val();
    }

    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#nominasi_table')) {
            table = $('#nominasi_table').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": false,
                "autoWidth": false,
                "pageLength": 100,
                "ordering": false,
                "deferLoading": 0,
                "order": [
                    [6, "DESC"]
                ],
                "ajax": {
                    "url": "<?= site_url('admin/laporan/ajax_list') ?>",
                    "type": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    "data": function(d) {
                        d[csrfName] = getCsrfToken();
                        // MENGIRIM DATA FILTER KE SERVER
                        d.prodi = $('#filter_prodi').val();
                        d.kelas = $('#filter_kelas').val();
                        d.jenis = $('#filter_jenis').val();
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
            updateData();
        });

        window.updateData = function() {
            var prodi = $('#filter_prodi').val();
            var kelas = $('#filter_kelas').val();
            var jenis = $('#filter_jenis').val();

            if (prodi !== '') {
                $.ajax({
                    url: "<?= site_url('admin/laporan/ajax_get_kuota') ?>",
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

                        if (parseInt(res.kuota_cadangan) >= parseInt(res.kuota_cadangan) && parseInt(res.kuota_cadangan) > 0) {
                            $('#lbl_terisi_cadangan').parent().removeClass('badge-danger').addClass('badge-warning');
                        } else {
                            $('#lbl_terisi_cadangan').parent().removeClass('badge-warning').addClass('badge-danger');
                        }
                        // Tampilkan panel
                        $('#panel_kuota').slideDown();
                    }
                });
                table.ajax.reload();
            } else {
                alert("Pilih Prodi terlebih dahulu!");
            }
        }

        // Refresh button
        $('#btn_refresh').click(function() {
            table.ajax.reload(null, false);
        });

        $(document).on('click', '#btn_export_excel', function() {
            var prodi = $('#filter_prodi').val();
            var kelas = $('#filter_kelas').val();
            var jenis = $('#filter_jenis').val();

            // Tambahkan parameter &jenis=
            var url = "<?= site_url('admin/laporan/export_excel') ?>?prodi=" + encodeURIComponent(prodi) +
                "&kelas=" + encodeURIComponent(kelas) +
                "&jenis=" + encodeURIComponent(jenis);
            window.open(url, '_blank');
        });
        // Action Tombol RESET FILTER
        $('#btn_reset').click(function() {
            // 1. Reset dropdown
            $('#filter_prodi').val('').trigger('change');
            $('#filter_kelas').val('').trigger('change');
            $('#filter_jenis').val('').trigger('change');

            // 2. Reset teks ke 0 / 0
            $('#nama_prodi_kuota').text('Nama Prodi');
            $('#kelas').text('Kelas');
            $('#lbl_kuota_utama').text('0');
            $('#lbl_kuota_cadangan').text('0');
            $('#lbl_terisi_utama').text('0');
            $('#lbl_terisi_cadangan').text('0');

            // 3. Reset warna badge ke default (success/warning)
            $('#lbl_terisi_utama').parent().removeClass('badge-danger').addClass('badge-success');
            $('#lbl_terisi_cadangan').parent().removeClass('badge-danger').addClass('badge-warning');

            // 4. Bersihkan tabel
            table.ajax.reload(null, false);
        });
    });
</script>