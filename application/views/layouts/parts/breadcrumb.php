<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1> <?php echo ucfirst(str_replace('_', ' ', $this->uri->segment(2))) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#"><?php echo ucfirst(str_replace('_', ' ', $this->uri->segment(1))) ?></a></li>
                    <li class="breadcrumb-item active"><?php echo ucfirst(str_replace('_', ' ', $this->uri->segment(2))) ?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>