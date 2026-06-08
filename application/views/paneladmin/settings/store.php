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