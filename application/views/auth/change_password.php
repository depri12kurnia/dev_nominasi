<!-- <h1><?php echo lang('change_password_heading'); ?></h1>

<div id="infoMessage"><?php echo $message; ?></div>

<?php echo form_open("auth/change_password"); ?>

<p>
      <?php echo lang('change_password_old_password_label', 'old_password'); ?> <br />
      <?php echo form_input($old_password); ?>
</p>

<p>
      <label for="new_password"><?php echo sprintf(lang('change_password_new_password_label'), $min_password_length); ?></label> <br />
      <?php echo form_input($new_password); ?>
</p>

<p>
      <?php echo lang('change_password_new_password_confirm_label', 'new_password_confirm'); ?> <br />
      <?php echo form_input($new_password_confirm); ?>
</p>

<?php echo form_input($user_id); ?>
<p><?php echo form_submit('submit', lang('change_password_submit_btn')); ?></p>

<?php echo form_close(); ?> -->

<section class="content">
      <div class="row">
            <div class="col-md-12">
                  <div class="card card-info">
                        <div class="card-header">
                              <h3 class="card-title">Change Password</h3>
                        </div>
                        <?php if (validation_errors()) : ?>
                              <div class="alert alert-danger">
                                    <?php echo validation_errors(); ?>
                              </div>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('message')) : ?>
                              <div class="alert alert-info">
                                    <?php echo $this->session->flashdata('message'); ?>
                              </div>
                        <?php endif; ?>

                        <?php echo form_open("auth/change_password"); ?>
                        <div class="card-body">
                              <div class="form-group">
                                    <label for="oldPassword">Old Password</label>
                                    <input type="password" name="oldPassword" id="oldPassword" class="form-control">
                              </div>
                              <div class="form-group">
                                    <label for="newPassword">New Password</label>
                                    <input type="password" name="newPassword" id="newPassword" class="form-control">
                              </div>
                              <div class="form-group">
                                    <label for="confirmPassword">Confirm Password</label>
                                    <input type="password" name="confirmPassword" id="confirmPassword" class="form-control">
                              </div>
                              <div class="form-group">
                                    <div class="col-8">
                                          <div class="icheck-primary">
                                                <input type="checkbox" id="showPassword">
                                                <label for="showPassword">
                                                      Show Password
                                                </label>
                                          </div>
                                    </div>
                              </div>
                              <div class="row">
                                    <div class="col-12">
                                          <input type="submit" value="Change Password" class="btn btn-success float-right">
                                    </div>
                              </div>
                        </div>
                        <?php echo form_close(); ?>
                        <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
            </div>
      </div>
</section>

<script>
      $(document).ready(function() {
            $('#showPassword').click(function() {
                  if ($(this).is(':checked')) {
                        $('#oldPassword').attr('type', 'text');
                        $('#newPassword').attr('type', 'text');
                        $('#confirmPassword').attr('type', 'text');
                  } else {
                        $('#oldPassword').attr('type', 'password');
                        $('#newPassword').attr('type', 'password');
                        $('#confirmPassword').attr('type', 'password');
                  }
            });
      });
</script>