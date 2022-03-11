<?php
/*
* © Argo Almacenadora ®
* Fecha: 30/11/2016
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard
* Version:
*/

include_once '../class/Perfil.php';
$instacia_modulo  = new Perfil;
$iid_empleado = $_SESSION['iid_empleado'];

//VAR PARA PONER LA CLASE ACTIVE DEPENDIENTO AL MODULO DONDE SE ENCUENTRA
$class_active = $_SESSION['modulo_actual'];
@$active = array_pop(explode('/', $_SERVER['PHP_SELF']));

 $nom_plazaLayout = $_SESSION['nomPlaza'];
 $visible2="";

if($nom_plazaLayout=='CÓRDOBA' || $nom_plazaLayout=='CORPORATIVO' || $nom_plazaLayout=='ALL'){
  $visible = "display: ";
  $visible2= "display: ";
}elseif ($nom_plazaLayout=='BAJIO' || $nom_plazaLayout=='PUEBLA' ) {
  $visible = "display: none";
  $visible2 = "display: ";
}
else {
  $visible= "display: none";
  $visible2= "display: none";
}
//echo $visible;


?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="author" content="Jorge Tejeda Juan">
  <title>Argo Almacenadora | Dashboard</title>

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">-->
  <link rel="stylesheet" href="../dist/css/font-awesome-min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../plugins/iCheck/flat/blue.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="../plugins/morris/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="../plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="../plugins/datepicker/datepicker3.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <!-- Selected 2 -->
  <link rel="stylesheet" href="../plugins/select2/select2.min.css">
  <link rel="shortcut icon" href="../assets/ico/favicon.png">
  <!-- PLUGINS INTRO -->
  <link href="../plugins/intro/introjs.css" rel="stylesheet">
  <!-- Add Nazanin template -->
  <link href="../plugins/intro/themes/introjs-modern.css" rel="stylesheet">
  <!-- Inicia fancyBox SS files -->
    <!-- Add fancyBox main JS and CSS files -->
  <link rel="stylesheet" type="text/css" href="../plugins/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
    <!-- Add Button helper (this is optional) -->
  <link rel="stylesheet" type="text/css" href="../plugins/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
    <!-- Add Thumbnail helper (this is optional) -->
  <link rel="stylesheet" type="text/css" href="../plugins/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
  <!-- Termina fancyBox SS files -->
   <!-- SCRIP DE CARGA -->
  <!-- Pace style -->
  <link rel="stylesheet" href="../plugins/pace/pace.css">

  <!-- ICONOS PERSONALES -->
  <link rel="stylesheet" href="../plugins/icon/icon.css">
  <?php
  if ( basename($_SERVER['PHP_SELF']) == 'agronegocios.php' || basename($_SERVER['PHP_SELF']) == 'manufactura.php') {
    echo '<meta http-equiv="refresh" content="300" />';
  }
  else {

  }
  ?>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->


</head>
<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">
<!-- ##################################### Empieza Encabezado ################################ -->
  <header class="main-header">
    <!-- Logo -->
    <a href="index.php" class="logo click_modal_cargando" <?php if($_SESSION['modulo_actual']==0){ echo 'data-intro="Botón para Regresar al inicio del Dashboard"';} ?> >
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>A</b>RG</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><i class="fa fa-home" aria-hidden="true"></i><b> Admin</b>ARGO</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" <?php if($_SESSION['modulo_actual']==0){ echo 'data-intro="Botón para ocultar menú da navegación lateral"'; } ?> >
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <!-- User Account: style can be found in dropdown.less como renunciar sin que me genere problemas -->
          <li class="dropdown user user-menu" <?php if($_SESSION['modulo_actual']==0){ echo 'data-intro="Botón para salir del Dashboard"'; } ?> >
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="../dist/img/argo_new_160x160.jpg" class="user-image" alt="User Image">
              <span class="hidden-xs">Argo Almacenadora</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="../dist/img/argo_new_160x160.jpg" class="img-circle" alt="User Image">

                <p>
                  Argo Almacenadora, S.A. de C.V.
                  <small>Fundada en 1991</small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <!--<a href="#" class="btn btn-default btn-flat">Profile</a>-->
                </div>
                <div class="pull-right">
                  <a href="../sesion/logout.php" class="btn btn-default btn-flat">Salir</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li <?php if($_SESSION['modulo_actual']==0){ echo 'data-intro="Botón para cambiar de color al Skins al Dashboard"'; } ?> >
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
<!-- ##################################### Termina Encabezado ################################ -->
<!--  ###################################### Menu Izquierdo ################################# -->
<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="../dist/img/argo_new_160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Argo Almacenadora</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online: <?=$_SESSION['usuario'];?></a>
          <br>
          <a href="#"><i class="fa fa-calendar text-success"></i>Date: <?=date("d")."-".date("m")."-".date("Y");?></a>

        </div>

      </div>
      <!-- search form -->
      <!--<form action="index.php" method="post" class="sidebar-form">-->
        <div class="input-group">
          <!--  <input type="text" name="q" class="form-control" placeholder="Buscar...">-->
              <!-- select -->
               <!--<select id="mesFactura" name="mesFactura" class="form-control">-->
                <?php
                  //for($i = $mesFactura; $i > 0; $i--){
                    //$selected = "";
                    //if($mesFactura == str_pad($i, 2, "0", STR_PAD_LEFT))
                      //$selected = "selected";
                    //echo '<option value = "'.str_pad($i, 2, "0", STR_PAD_LEFT).'" '.$selected.'>'.$objFac->getMes(str_pad($i, 2, "0", STR_PAD_LEFT)).'</option>';
                  //}
                ?>
              <!--</select> -->
              <!--<span class="input-group-btn">-->
              <?php //if($mesFactura > 0) { ?>
                <!--<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-check fa-1x"></i>-->
                <!--</button>-->
              <?php //} ?>
              <!--</span>-->
              <p>.</p>
        </div>
      <!--</form>-->
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" <?php if($_SESSION['modulo_actual']==0){ echo 'data-intro="Sección para entrar al Dashboard de cada área"';} ?> >
        <!-- <li class="header">NAVEGACION PRINCIPAL</li> -->
        <li class="header">NAVEGACION PRINCIPAL</li>

      <!-- ****** INICIA MENU DE ADMINISTRACION ****** -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '3,14,10,11,12,13,15,37,46,54'); if ($modulos_valida > 0){ ?>
            <?php include_once('m_administracion.php'); ?>
        <?php } ?>
      <!-- ****** TERMINA MENU DE ADMINISTRACION ****** -->

      <!-- ****** INICIA MENU DE COMERCIAL ****** -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '7,16,17,44, 48'); if ($modulos_valida > 0){ ?>
            <?php include_once('m_comercial.php'); ?>
        <?php } ?>
      <!-- ****** TERMINA MENU DE COMERCIAL ****** -->

      <!-- ****** INICIA MENU DE COMERCIO EXTERIOR ****** -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '8,9,24,25,34,35,36'); if ($modulos_valida > 0){ ?>
            <?php include_once('m_comercio_exterior.php'); ?>
        <?php } ?>
      <!-- ****** TERMINA MENU DE COMERCIO EXTERIOR ****** -->

      <!-- ******** INICIA MENU DE SGC ******** -->
       <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '56'); if ($modulos_valida > 0){ ?>
            <?php include_once('m_sgc.php'); ?>
       <?php } ?>
    <!-- ******** TERMINA MENU DE SGC ******** -->

      <!-- ****** INICIA MENU DE SISTEMAS ****** -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '1,2,50'); if ($modulos_valida > 0){ ?>
            <?php include_once('m_tics.php'); ?>
        <?php } ?>
      <!-- ****** TERMINA MENU DE SISTEMAS ****** -->


      <!-- ****** INICIA MENU DE TALENTO HUMANO ****** -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '19,20,22,23,31,52,57'); if ($modulos_valida > 0){ ?>
            <?php include_once('m_talento_humano.php'); ?>
      <?php } ?>
      <!-- ****** TERMINA MENU DE TALENTO HUMANO ****** -->

      <!-- ****** INICIA MENU DE OPERACIONES ****** -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '6, 29, 32, 30, 38, 40, 43, 5, 27, 28, 18, 45,34,36,41,42,47,49'); if ($modulos_valida > 0){ ?>
            <?php include_once('m_operaciones.php'); ?>
        <?php } ?>
      <!-- ****** TERMINA MENU DE OPERACIONES ****** -->

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
<!--  ###################################### Termina Menu Izquierdo ################################# -->
<!-- ############################################# Menu Derecho ##################################-->
