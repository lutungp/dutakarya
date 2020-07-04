<!DOCTYPE html>
<html>
<head>
    <?php
        define("BASE_URL", "http://" . $_SERVER['HTTP_HOST'] . "/dutakarya");
        define('__ROOT__', dirname(dirname(__FILE__)));
    ?>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>DUTA KARYA</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/summernote/summernote-bs4.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- ./wrapper -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
  <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/img/favicon.ico">
	<link rel="manifest" href="<?php echo BASE_URL; ?>/manifest.json">
  <!-- jQuery -->
  <script src="<?php echo BASE_URL ?>/assets/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="<?php echo BASE_URL ?>/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
      <script>
        $.widget.bridge('uibutton', $.ui.button);
      </script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
      <!-- Bootstrap 4 -->
      <script src="<?php echo BASE_URL ?>/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- ChartJS -->
      <script src="<?php echo BASE_URL ?>/assets/plugins/chart.js/Chart.min.js"></script>
      <!-- Sparkline -->
      <script src="<?php echo BASE_URL ?>/assets/plugins/sparklines/sparkline.js"></script>
      <!-- JQVMap -->
      <script src="<?php echo BASE_URL ?>/assets/plugins/jqvmap/jquery.vmap.min.js"></script>
      <script src="<?php echo BASE_URL ?>/assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
      <!-- jQuery Knob Chart -->
      <script src="<?php echo BASE_URL ?>/assets/plugins/jquery-knob/jquery.knob.min.js"></script>
      <!-- daterangepicker -->
      <script src="<?php echo BASE_URL ?>/assets/plugins/moment/moment.min.js"></script>
      <script src="<?php echo BASE_URL ?>/assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
      <script src="<?php echo BASE_URL ?>/assets/plugins/daterangepicker/daterangepicker.js"></script>
      <!-- Tempusdominus Bootstrap 4 -->
      <script src="<?php echo BASE_URL ?>/assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
      <!-- Summernote -->
      <script src="<?php echo BASE_URL ?>/assets/plugins/summernote/summernote-bs4.min.js"></script>
      <!-- overlayScrollbars -->
      <script src="<?php echo BASE_URL ?>/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
      <!-- AdminLTE App -->
      <script src="<?php echo BASE_URL ?>/assets/dist/js/adminlte.js"></script>
      <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
      <script src="<?php echo BASE_URL ?>/assets/dist/js/pages/dashboard.js"></script>
      <!-- AdminLTE for demo purposes -->
      <script src="<?php echo BASE_URL ?>/assets/dist/js/demo.js"></script>

      <!-- <script src="<?php // echo BASE_URL ?>/assets/plugins/datatables/jquery.dataTables.min.js"></script>
      <script src="<?php // echo BASE_URL ?>/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
      <script src="<?php // echo BASE_URL ?>/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
      <script src="<?php // echo BASE_URL ?>/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script> -->
      <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/select2/css/select2.min.css">
      <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <style>
    html,body {
      font-size: 14px;
    }
    .content-wrapper>.content {
      padding: 0 1.5rem;
    }

    .form-control:disabled, .form-control[readonly] {
        background-color: #fff;
    }
    .modal-body {
        padding: 2px
        ;
    }
    .form-group {
        margin-bottom: 0rem;
    }

    .form-control {
      height: 29px;
      padding: 4px 5px;
      font-size: 14px;
    }
    .modal-header {
      padding: 5px !important;
    }
    .modal-footer {
      padding: 2px !important;
    }

    @media (max-width: 480px) {
      .content-wrapper>.content {
        padding: 10px !important;
      }

      .content-header {
        padding: 2px !important;
      }
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 15px;
    }

  </style>
  <script>
    function formatMoney(number, decPlaces, decSep, thouSep) {
        decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSep = typeof decSep === "undefined" ? "." : decSep;
        thouSep = typeof thouSep === "undefined" ? "," : thouSep;
        var sign = number < 0 ? "-" : "";
        var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
        var j = (j = i.length) > 3 ? j % 3 : 0;

        return sign + (j ? i.substr(0, j) + thouSep : "") + i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) + (decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");
    }
  </script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" href="<?php echo BASE_URL ?>/controllers/C_login.php?action=logout">
          <i class="fas fa-power-off"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  <!-- Main Sidebar Container -->
 <?php include '../views/layouts/v_sidebaradmin.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <!-- <section class="content">
      
    </section> -->
  