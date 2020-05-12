  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <!-- <li class="nav-item has-treeview <?php echo $active1 == "MASTER" ? "menu-open" : ""; ?>">
            <a href="#" class="nav-link  <?php echo $active1 == "MASTER" ? "active" : ""; ?>">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>
                Master
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo BASE_URL ?>/controllers/C_tarif.php" class="nav-link  <?php echo $active2 == "TARIF" ? "active" : ""; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tarif</p>
                </a>
              </li>
            </ul>
          </li> -->
          <li class="nav-item has-treeview <?php echo $active1 == "TRANSAKSI" ? "menu-open" : ""; ?>">
            <a href="#" class="nav-link  <?php echo $active1 == "TRANSAKSI" ? "active" : ""; ?>">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>
                Transaksi
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=listdaftar" class="nav-link  <?php echo $active2 == "DAFTAR ANTRIAN" ? "active" : ""; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Antrian</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>