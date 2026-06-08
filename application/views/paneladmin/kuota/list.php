<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Master Data Kuota</h3>
                <button class="card-title btn btn-success btn-sm float-right" onclick="add_kuota()"><i class="fa fa-plus"></i> Add</a></button>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="table" class="table table-bordered table-hover small">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Prodi</th>
                            <th>Kuota Utama</th>
                            <th>Persentase Cadangan</th>
                            <th>Kuota Cadangan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datatables -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Nama Prodi</th>
                            <th>Kuota Utama</th>
                            <th>Persentase Cadangan</th>
                            <th>Kuota Cadangan</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Kuota Form</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id" />
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Prodi</label>
                            <div class="col-md-12">
                                <input name="nama_prodi" placeholder="Name" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Kuota Utama</label>
                            <div class="col-md-12">
                                <input name="kuota_utama" placeholder="Kuota Utama" class="form-control" type="number">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Persentase Cadangan</label>
                            <div class="col-md-12">
                                <input name="persentase_cad" placeholder="Persentase Cadangan" class="form-control" type="number">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnSave">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="csrf_token" name="csrf_token_jkt3" value="<?= $this->security->get_csrf_hash(); ?>">


<script type="text/javascript">
    var save_method;
    var table;

    function getCsrfToken() {
        let token = document.cookie.split('; ')
            .find(row => row.startsWith('csrf_cookie_jkt3='))
            ?.split('=')[1] || '';

        // console.log("CSRF Token dari Cookie:", token); // Debug
        return token;
    }

    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    $(document).ajaxSend(function(e, xhr, options) {
        let csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (csrfToken) {
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        }
    });

    $(document).ready(function() {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "autoWidth": false,
            "lengthChange": true,
            "ajax": {
                "url": "<?php echo site_url('admin/kuota/ajax_list') ?>",
                "type": "POST",
                "data": function(d) {
                    d.csrf_token_jkt3 = getCsrfToken(); // Kirim CSRF token sebagai data POST
                },
                "error": function(xhr) {
                    console.log("Error:", xhr.responseText);
                }
            }

        });
    });

    function add_kuota() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('Add Kuota');
    }

    function edit_kuota(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $.ajax({
            url: "<?php echo site_url('admin/kuota/ajax_edit/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id"]').val(data.id);
                $('[name="nama_prodi"]').val(data.nama_prodi);
                $('[name="kuota_utama"]').val(data.kuota_utama);
                $('[name="persentase_cad"]').val(data.persentase_cad);
                $('[name="kuota_cadangan"]').val(data.kuota_cadangan);
                $('#modal_form').modal('show');
                $('.modal-title').text('Edit User');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error getting data from ajax');
            }
        });
    }

    function delete_kuota(id) {
        if (confirm('Are you sure delete this data?')) {
            $.ajax({
                url: "<?php echo site_url('admin/kuota/ajax_delete/') ?>" + id,
                type: "POST",
                data: {
                    csrf_token_jkt3: getCsrfToken()
                }, // Kirim CSRF token

                dataType: "JSON",
                success: function(data) {
                    $('#modal_form').modal('hide');
                    reload_table();

                    // debug
                    // console.log("Token CSRF baru:", data.csrf_token);
                    // Perbarui CSRF token setelah request berhasil
                    document.cookie = "csrf_cookie_jkt3=" + data.csrf_token + "; path=/";
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error deleting data');
                }
            });
        }
    }

    $('#btnSave').click(function() {
        var url;
        if (save_method == 'add') {
            url = "<?php echo site_url('admin/kuota/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('admin/kuota/ajax_update') ?>";
        }

        var formData = $('#form').serialize();
        formData += '&csrf_token_jkt3=' + getCsrfToken(); // Tambahkan CSRF token ke form data

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize() + '&csrf_token_jkt3=' + getCsrfToken(),
            dataType: "JSON",
            beforeSend: function(xhr) {
                xhr.setRequestHeader("X-CSRF-Token", getCsrfToken());
            },
            success: function(data) {
                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_table();
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }

                // Debug
                // console.log("Token CSRF baru:", data.csrf_token);
                // Perbarui CSRF token setelah request berhasil
                document.cookie = "csrf_cookie_jkt3=" + data.csrf_token + "; path=/";
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
            }
        });
    });

    function reload_table() {
        table.ajax.reload(null, false);
    }
</script>