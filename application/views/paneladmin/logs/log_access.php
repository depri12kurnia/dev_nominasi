<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Data Logs</h3>
            </div>
            <div class="col-6">
                <p class="btn btn-group">
                    <button class="btn btn-danger btn-sm" onclick="delete_logs()"><i class="fa fa-trash"></i> Delete All</a></button>
                </p>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="data_logs" class="table table-bordered table-hover small">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>timestamp</th>
                            <th>ip_address</th>
                            <th>user_agent</th>
                            <th>uri</th>
                            <th>method</th>
                            <th>message</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>timestamp</th>
                            <th>ip_address</th>
                            <th>user_agent</th>
                            <th>uri</th>
                            <th>method</th>
                            <th>message</th>
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
        table = $('#data_logs').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo site_url('admin/logs/get_data') ?>",
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

    function delete_logs() {
        if (confirm('Are you sure delete this data?')) {
            $.ajax({
                url: "<?php echo site_url('admin/logs/delete_all_logs') ?>",
                type: "POST",
                data: {
                    csrf_token_jkt3: getCsrfToken()
                }, // Kirim CSRF token

                dataType: "JSON",
                success: function(data) {
                    if (data.status === "success") {
                        alert("All logs deleted successfully!");
                        table.ajax.reload(); // Reload DataTable

                        // Perbarui CSRF token setelah request berhasil
                        if (data.csrf_token) {
                            document.cookie = "csrf_cookie_jkt3=" + data.csrf_token + "; path=/";
                        }
                    } else {
                        alert("Failed to delete logs: " + (data.message || "Unknown error"));
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error deleting data');
                }
            });
        }
    }
</script>