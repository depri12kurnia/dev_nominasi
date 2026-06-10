  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
          <li class="nav-item">
              <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
          <li class="nav-item d-none d-sm-inline-block">
              <a href="<?php echo base_url('admin/dashboard'); ?>" class="nav-link">Dashboard</a>
          </li>
          <li class="nav-item d-none d-sm-inline-block">
              <a href="<?php echo base_url('auth/logout'); ?>" class="nav-link">Logout</a>
          </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
          <li class="nav-item">
              <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                  <i class="fas fa-expand-arrows-alt"></i>
              </a>
          </li>
          <li class="nav-item dropdown">
              <a class="nav-link" data-toggle="dropdown" href="#">
                  <i class="far fa-user"></i>
                  <!-- <span class="badge badge-warning navbar-badge">15</span> -->
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                  <span class="dropdown-item dropdown-header"><?php echo $this->session->userdata('email'); ?></span>
                  <div class="dropdown-divider"></div>
                  <a href="<?php echo base_url('auth/change_password'); ?>" class="dropdown-item">
                      <i class="fas fa-lock mr-2"></i> Change Password
                      <span class="float-right text-muted text-sm"></span>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a href="<?php echo base_url('auth/logout'); ?>" class="dropdown-item">
                      <i class="fas fa-sign-out-alt mr-2"></i> Logout
                      <span class="float-right text-muted text-sm"></span>
                  </a>

              </div>
          </li>
      </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="<?php echo base_url('dashboard'); ?>" class="brand-link">
          <img src="<?php echo base_url(); ?>public/settings/logo/logo.png" class="brand-image">
          <span class="brand-text font-weight-light"></span>
      </a>
      <!-- Sidebar -->
      <div class="sidebar">
          <!-- Sidebar user (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
              <div class="image">
                  <img src="https://cdn.jsdelivr.net/gh/depri12kurnia/assetsadminlte3.2.0@19d5f7d70f5a32386894c2573713049dc9e2e5f0/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
              </div>
              <div class="info">
                  <a href="#" class="d-block"><?php echo $this->session->userdata('email'); ?></a>
              </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                  <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                  <!-- Akses Administrator -->
                  <?php if ($this->ion_auth->in_group('admin')) {  ?>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/dashboard'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'dashboard' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-tachometer-alt"></i>
                              <p>
                                  Dashboard
                              </p>
                          </a>
                      </li>
                      <li class="nav-header">Master Nominasi</li>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/nominasi'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'nominasi' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-trophy"></i>
                              <p>
                                  Data Nominasi
                              </p>
                          </a>
                      </li>
                      <li class="nav-header">Master Data</li>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/kuota'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'kuota' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-list"></i>
                              <p>
                                  Master Kuota
                              </p>
                          </a>
                      </li>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/laporan'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'laporan' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-file"></i>
                              <p>
                                  Laporan
                              </p>
                          </a>
                      </li>

                      <li class="nav-header">Administrator</li>

                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/users'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'users' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-user"></i>
                              <p>
                                  Master Users
                              </p>
                          </a>
                      </li>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/groups'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'groups' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-users"></i>
                              <p>
                                  Master Groups
                              </p>
                          </a>
                      </li>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/activity'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'activity' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-history"></i>
                              <p>
                                  Activity
                              </p>
                          </a>
                      </li>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/logs'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'logs' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-history"></i>
                              <p>
                                  Logs System
                              </p>
                          </a>
                      </li>
                  <?php } ?>
                  <?php if ($this->ion_auth->in_group('auditor')) {  ?>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/dashboard'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'dashboard' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-tachometer-alt"></i>
                              <p>
                                  Dashboard
                              </p>
                          </a>
                      </li>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/verifikasi'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'verifikasi' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-ticket-alt"></i>
                              <p>
                                  Verifikasi
                              </p>
                          </a>
                      </li>
                  <?php } ?>
                  <?php if ($this->ion_auth->in_group('reviewer')) {  ?>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/dashboard'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'dashboard' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-tachometer-alt"></i>
                              <p>
                                  Dashboard
                              </p>
                          </a>
                      </li>
                      <li class="nav-item">
                          <a href="<?php echo base_url('admin/submissions'); ?>" class="nav-link <?= ($this->uri->segment(1) == 'admin') && $this->uri->segment(2) == 'submissions' ? 'active' : ''; ?>">
                              <i class="nav-icon fas fa-file-alt"></i>
                              <p>
                                  Submissions
                              </p>
                          </a>
                      </li>
                  <?php } ?>

              </ul>
          </nav>
          <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->

      <div class="sidebar-custom">
          <a href="<?php echo base_url('admin/settings'); ?>" class="btn btn-link"><i class="fas fa-cogs"></i></a>
          <a href="#" class="btn btn-secondary hide-on-collapse pos-right">Help</a>
      </div>
      <!-- /.sidebar-custom -->
  </aside>