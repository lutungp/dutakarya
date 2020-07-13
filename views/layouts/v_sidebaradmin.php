  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block"><?php echo $user->user_pegawai ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item has-treeview">
          <a href="<?php echo BASE_URL ?>/controllers/C_dashboard" class="nav-link">
            <i class="nav-icon"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <?php 
          $rolemenu_lv1 = array_filter($rolemenu, function ($value) { 
              return $value['menu_level'] == 1; 
          });
          
          foreach ($rolemenu_lv1 as $value) {
              $activeclass1 = $active1 == $value["menu_kode"] ? "menu-open" : "";
              echo '<li class="nav-item has-treeview '.$activeclass1.'">';
              $activeclass1 = $active1 == $value["menu_kode"] ? "active" : "";
              echo '<a href="#" class="nav-link '.$activeclass1.'">
                <i class="nav-icon"></i>
                <p>
                  '.$value["menu_nama"].'
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">';
                  $menuparent = $value["menu_id"];
                  $rolemenu_lv2 = array_filter($rolemenu, function ($value) use ($menuparent) { 
                      return $value['menu_level'] == 2 && $value['menu_parent'] == $menuparent; 
                  });
                  foreach ($rolemenu_lv2 as $valchild) {
                      $activeclass2 = $active2 == $valchild["menu_kode"] ? "active" : "";
                      $url = BASE_URL . $valchild["menu_url"];
                      echo '<li class="nav-item">
                            <a href="'.$url.'" class="nav-link '.$activeclass2.'">
                            <i class="far fa-circle nav-icon"></i>
                            <p>'.$valchild["menu_nama"].'</p>
                            </a>
                            </li>' ;
                  }
              echo '</ul>
                  </li>';
          }
        ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>