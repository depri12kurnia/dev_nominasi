<div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
            <div class="container-fluid">
                  <div class="row mb-2">
                        <div class="col-sm-6">
                              <h1></h1>
                        </div>
                  </div>
            </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
            <div class="container-fluid">
                  <div class="row">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-8">
                              <div class="card card-info">
                                    <div class="card-header">
                                          <h3 class="card-title">Forgot Password</h3>
                                    </div>
                                    <div class="card-body">
                                          <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
                                          <div id="infoMessage" class="login-box-msg"><?php echo $message; ?></div>
                                          <?php echo form_open("auth/forgot_password_process"); ?>
                                          <div class="input-group mb-3">
                                                <input type="email" class="form-control" placeholder="Email" name="email">
                                                <div class="input-group-append">
                                                      <div class="input-group-text">
                                                            <span class="fas fa-envelope"></span>
                                                      </div>
                                                </div>
                                          </div>
                                          <div class="row">
                                                <div class="col-4">
                                                </div>
                                                <div class="col-4">
                                                      <button type="submit" class="btn btn-primary btn-block">Request new password</button>
                                                </div>
                                                <div class="col-4">
                                                </div>
                                                <!-- /.col -->
                                          </div>
                                          <?php echo form_close(); ?>
                                          <p class="mt-3 mb-1">
                                                <a href="<?php echo site_url('auth/login'); ?>">Login</a>
                                          </p>
                                    </div>

                              </div>
                              <div class="col-md-2">
                              </div>
                        </div>
                  </div>
      </section>
      <!-- /.content -->
</div>
<!-- /.content-wrapper -->