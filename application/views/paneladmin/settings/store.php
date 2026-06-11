<div class="row">
    <div class="col-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Backup Database Website</h3>
            </div>
            <div class="card-body">
                <a href="<?php echo base_url('admin/backup/data_all'); ?>" class="btn btn-primary"><i class="fas fa-database"></i> Backup Databases All</a>
                <a href="<?php echo base_url('admin/backup/data_nominasi'); ?>" class="btn btn-primary"><i class="fas fa-table"></i> Backup Data Nominasi</a>
                <a href="javascript:void(0)"
                    class="btn btn-danger"
                    onclick="confirmTruncate()">
                    <i class="fas fa-trash"></i> Truncate tbl_nominasi
                </a>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Settings Website</h3>
            </div>
            <div class="card-body">
                <table id="data" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Company</th>
                            <th>Address</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Logo</th>
                            <th>Icon</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($settings as $row) { ?>
                            <tr data-widget="expandable-table" aria-expanded="true">
                                <td><?php echo $row->id; ?></td>
                                <td><?php echo $row->name; ?></td>
                                <td><?php echo $row->description; ?></td>
                                <td><?php echo $row->company; ?></td>
                                <td><?php echo $row->address; ?></td>
                                <td><?php echo $row->telepon; ?></td>
                                <td><?php echo $row->email; ?></td>
                                <td><?php echo $row->logo; ?></td>
                                <td><?php echo $row->icon; ?></td>
                                <td>
                                    <a href="settings/edit/<?php echo $row->id; ?>" class="btn btn-warning btn-block btn-sm" title="Update"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<script>
    function confirmTruncate() {
        Swal.fire({
            title: 'PERINGATAN!',
            text: "Anda akan menghapus SELURUH data nominasi. Apakah Anda sudah melakukan backup?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Kosongkan!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= site_url('admin/backup/truncate_nominasi'); ?>";
            }
        });
    }
</script>