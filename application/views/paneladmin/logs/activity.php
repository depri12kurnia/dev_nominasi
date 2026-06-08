<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Activity Users Logs</h3>
            </div>
            <div class="col-6">
                <p class="btn btn-group">
                    <button class="btn btn-danger btn-sm" onclick="delete_activity()">
                        <i class="fa fa-trash"></i> Delete All Activity Logs
                    </button>
                </p>
            </div>

            <!-- /.card-header -->
            <div class="card-body">
                <table id="data_activity" class="table table-bordered table-hover small">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>user</th>
                            <th>action</th>
                            <th>timestamp</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>user</th>
                            <th>action</th>
                            <th>timestamp</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.col -->

<input type="hidden" id="csrf_token" name="csrf_token_jkt3" value="<?= $this->security->get_csrf_hash(); ?>">

<script type="text/javascript">
    var save_method;
    var table;

    // CSRF Token handling
    var csrfName = '<?php echo $csrf_token; ?>';
    var csrfHash = '<?php echo $csrf_hash; ?>';

    function refreshCsrfToken() {
        $.get('<?php echo base_url('admin/activity/get_csrf_token'); ?>', function(response) {
            var data = JSON.parse(response);
            csrfName = data.csrf_token;
            csrfHash = data.csrf_hash;
        });
    }

    function getCsrfToken() {
        // Get current CSRF token from hidden input or variable
        var token = $('#csrf_token').val() || csrfHash;
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
        table = $('#data_activity').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo site_url('admin/activity/get_data') ?>",
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

    function delete_activity() {
        if (confirm('Are you sure you want to delete all activity logs?')) {
            $.ajax({
                url: "<?php echo site_url('admin/activity/delete_all_activity') ?>",
                type: "POST",
                dataType: "JSON",
                cache: false,
                data: {
                    'csrf_token_jkt3': getCsrfToken() // Send CSRF token
                },
                success: function(data) {
                    if (data.status === "success") {
                        alert("All activity logs deleted successfully!");
                        table.ajax.reload(); // Reload DataTable

                        // Update CSRF token untuk request selanjutnya
                        if (data.csrf_token) {
                            csrfHash = data.csrf_token;
                        }
                    } else {
                        alert("Failed to delete activity: " + data.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error: ", textStatus, errorThrown);
                    console.error("Response: ", jqXHR.responseText);
                    alert("Error deleting data. Please try again.");
                }
            });
        }
    }
</script>