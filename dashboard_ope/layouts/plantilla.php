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
      <span class="logo-lg"><i class="fa fa-home" aria-hidden="true"></i><b>Admin</b>ARGO</span>
    </a>
    <!-- Header Navbar: style can be found in header.less
    <img src="../dist/img/inicio1.png" width="30px" height="30px"> -->
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
        <li class="<?php if($active=="remates.php"||$active=="facturacion.php"){echo "active";}?> treeview">
          <a href="#">
            <i class="fa fa-folder-open"></i> <span>Administración</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <!-- BOTTON ADMINISTRACION -->

            <!--TERMINA BOTTON ADMINISTRACION -->
            <!-- BOTTON REMATES -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '14'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="remates.php"){echo "active";}?>"><a class="click_modal_cargando" href="remates.php"><i class="fa fa-circle-o"></i> Remates </a></li>
            <?php } ?>

            <!-- BOTTON REGISTRO DE INFORMACION FINANCIERA -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '10'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="informacion_financiera.php"){echo "active";}?>"><a class="click_modal_cargando" href="informacion_financiera.php"><i class="fa fa-circle-o"></i> Registrar Inf. Financiera</a></li>
              <?php } ?>
            <!--TERMINA BOTTON REGISTRO DE INFORMACION FINANCIERA -->
            <!-- BOTTON TESORERÍA -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '11'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="tesoreria.php"){echo "active";}?>"><a class="click_modal_cargando" href="tesoreria.php"><i class="fa fa-circle-o"></i> Tesorería</a></li>
              <?php } ?>
            <!--TERMINA BOTTON TESORERÍA -->
            <!-- BOTTON PASIVOS -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '12'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="pasivos.php"){echo "active";}?>"><a class="click_modal_cargando" href="pasivos.php"><i class="fa fa-circle-o"></i> Pasivos Hipotecarios</a></li>
              <?php } ?>
            <!--TERMINA BOTTON PASIVOS -->
            <!-- BOTTON CAPs (Coberturas) -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '13'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="coberturas.php"){echo "active";}?>"><a class="click_modal_cargando" href="coberturas.php"><i class="fa fa-circle-o"></i> CAPs (Coberturas)</a></li>
              <?php } ?>
            <!--TERMINA BOTTON CAPs (Coberturas) -->
            <!-- BOTTON SALDOS DE CLIENTES -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '15'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="saldos_clientes.php"){echo "active";}?>"><a class="click_modal_cargando" href="saldos_clientes.php"><i class="fa fa-circle-o"></i> Saldos de Clientes</a></li>
              <?php } ?>
            <!--TERMINA BOTTON SALDOS DE CLIENTES -->
            <!--PRESUPUESTOS VS INGRESOS-->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '37'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="detalles_Ingresos.php"){echo "active";}?>"><a class="click_modal_cargando" href="detalles_Ingresos.php"><i class="fa fa-circle-o"></i> Presupuesto vs Ingresos</a></li>
            <?php } ?>
            <!--TERMINA BOTTON REMATES -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '46'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="facturacion_saldos.php"){echo "active";}?>"><a class="click_modal_cargando" href="facturacion_saldos.php"><i class="fa fa-circle-o"></i> Facturacion X Semana</a></li>
            <?php } ?>
            <!--TERMINA remates BONIFICACIONES -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '54'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="indicadores_por_bonificacion.php"){echo "active";}?>"><a class="click_modal_cargando" href="indicadores_por_bonificacion.php"><i class="fa fa-circle-o"></i> Indice de Emision de Notas de Credito</a></li>
            <?php } ?>
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '55'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="indicadores_por_error.php"){echo "active";}?>"><a class="click_modal_cargando" href="indicadores_por_error.php"><i class="fa fa-circle-o"></i> Indice de Cumplimiento en Facturación de Servicios</a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
      <!-- ****** TERMINA MENU DE ADMINISTRACION ****** -->

      <!-- ****** INICIA MENU DE COMERCIAL ****** -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '7,16,17,44, 48'); if ($modulos_valida > 0){ ?>
        <li class="<?php if($active=="comercial.php" || $active=="comp_ingre_clientes.php" || $active=="venta_promotor.php"|| $active == "habilitaciones_bodega.php"){echo "active";}?> treeview">
          <a href="#">
            <i class="fa fa-briefcase"></i> <span>Comercial</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <!-- BOTTON COMERCIAL -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '7'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="comercial.php"){echo "active";}?>"><a class="click_modal_cargando" href="comercial.php"><i class="fa fa-circle-o"></i> Prospectos</a></li>
              <?php }//cierra if para ver modulo activado ?>
            <!--TERMINA BOTTON COMERCIAL -->
            <!-- BOTTON COMPARATIVO DE INGRESOS DE CLIENTES -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '16'); if ($modulos_valida > 0){ ?>
              <li title="Comparativos ingresos de clientes" class="<?php if($active=="comp_ingre_clientes.php"){echo "active";}?>"><a class="click_modal_cargando" href="comp_ingre_clientes.php"><i class="fa fa-circle-o"></i> Compara Ingresos de clientes</a></li>
              <?php }//cierra if para ver modulo activado ?>
            <!--TERMINA BOTTON COMPARATIVO DE INGRESOS DE CLIENTES -->
            <!-- BOTTON FACTURADO VS PRESUPUESTO PROMOTORES -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '17'); if ($modulos_valida > 0){ ?>
              <li title="Facturado vs Presupuesto Promotores" class="<?php if($active=="venta_promotor.php"){echo "active";}?>"><a class="click_modal_cargando" href="venta_promotor.php"><i class="fa fa-circle-o"></i> Fac.VSPresupuesto Promo.</a></li>
              <?php }//cierra if para ver modulo activado ?>
              <!-- POR PLAZAS HABILITADAS -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '21'); if ($modulos_valida > 0){ ?>
              <li title="Ingresos" class="<?php if($active=="habilitaciones_bodega.php"){echo "active";}?>"><a class="click_modal_cargando" href="habilitaciones_bodega.php"><i class="fa fa-circle-o"></i> Ingresos.</a></li>
              <?php }//cierra if para ver modulo activado ?>
            <!--TERMINA BOTTON FACTURADO VS PRESUPUESTO PROMOTORES -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '44'); if ($modulos_valida > 0){ ?>
            <li title="Facturacion en clientes" class="<?php if($active=="informacionClientesPresupuesto.php"){echo "active";}?>"><a class="click_modal_cargando" href="informacionClientesPresupuesto.php"><i class="fa fa-circle-o"></i> Facturacion Clientes.</a></li>
            <?php }//cierra if para ver modulo activado ?>
            <!--TERMINA BOTTON FACTURADO VS PRESUPUESTO PROMOTORES -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '48'); if ($modulos_valida > 0){ ?>
            <li title="Encuestas Realizadas" class="<?php if($active=="encuestas_realizadas.php"){echo "active";}?>"><a class="click_modal_cargando" href="encuestas_realizadas.php"><i class="fa fa-circle-o"></i> Encuestas Realizadas.</a></li>
            <?php }//cierra if para ver modulo activado ?>

          </ul>
        </li>
        <?php } ?>
      <!-- ****** TERMINA MENU DE COMERCIAL ****** -->

      <!-- ****** INICIA MENU DE COMERCIO EXTERIOR ****** -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '8,9,24,25,34,35,36'); if ($modulos_valida > 0){ ?>
        <li class="<?php if($active=="cartas_cupo.php"||$active=="pedimentos.php"){echo "active";}?> treeview">
          <a href="#">
            <i class="fa fa-ship"></i> <span>Comercio exterior</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <!-- BOTTON CARTAS CUPO -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '8'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="cartas_cupo.php"){echo "active";}?>"><a class="click_modal_cargando" href="cartas_cupo.php"><i class="fa fa-circle-o"></i> Cartas cupo</a></li>
            <?php } ?>
            <!--TERMINA BOTTON CARTAS CUPO -->
            <!-- BOTTON PEDIMENTOS-->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '9'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="pedimentos.php"){echo "active";}?>"><a class="click_modal_cargando" href="pedimentos.php"><i class="fa fa-circle-o"></i> Pedimentos</a></li>
            <?php } ?>
            <!--TERMINA BOTTON PEDIMENTOS-->
            <!--CARTAS CUPO -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '24'); if ($modulos_valida > 0){ ?>
                <li class="<?php if($active=="cartas_cupo_anuales.php"){echo "active";}?>"><a class="click_modal_cargando" href="cartas_cupo_anuales.php"><i class="fa fa-circle-o"></i> Cartas Cupo Por Año</a></li>
              <?php } ?>
            <!--CARTAS CUPO -->
            <!--CARTAS CUPO -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '25'); if ($modulos_valida > 0){ ?>
                <li class="<?php if($active=="pedimentos_anuales.php"){echo "active";}?>"><a class="click_modal_cargando" href="pedimentos_anuales.php"><i class="fa fa-circle-o"></i> Pedimentos Por Año</a></li>
              <?php } ?>
            <!--CARTAS CUPO -->

            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '39'); if ($modulos_valida > 0){ ?>
            <li class="<?php if ($active == "cartas_cupo_anuales_crossdock.php"){echo "active";} ?>"><a class="click_modal_cargando" href="cartas_cupo_anuales_crossdock.php"><i class="fa fa-circle-o"></i> Cartas Cupo CrossDock </a></li>
            <?php } ?>

          </ul>
        </li>
        <?php } ?>
      <!-- ****** TERMINA MENU DE COMERCIO EXTERIOR ****** -->
      <!-- ******** INICIA MENU DE SGC ******** -->
       <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '56'); if ($modulos_valida > 0){ ?>
       <li class="<?php if($active=="sgc.php"||$active=="notificaciones.php"||$active=="upload_minuta.php"){echo "active";}?> treeview" title="SISTEMA DE GESTION DE CALIDAD">
         <a href="#">
           <i class="fa fa-line-chart"></i> <span>Sistema de Gestion de Calidad</span>
           <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
         </a>
         <ul class="treeview-menu">
           <!-- BOTTON CONTROL DE SACP -->
           <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '56'); if ($modulos_valida > 0){ ?>
           <li class="<?php if($active=="sgc.php"||$active=="upload_minuta.php"){echo "active";}?>"><a class="click_modal_cargando" href="sgc.php"><i class="fa fa-circle-o"></i> SACP</a></li>
           <?php } ?>
         </ul>
       </li>
       <?php } ?>
    <!-- ******** TERMINA MENU DE SGC ******** -->

      <!-- ****** INICIA MENU DE SISTEMAS ****** -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '1,2,50'); if ($modulos_valida > 0){ ?>
        <li class="<?php if($active=="tic.php"||$active=="notificaciones.php"||$active=="upload_minuta.php"){echo "active";}?> treeview" title="TECNOLOGÍAS DE LA INFORMACIÓN">
          <a href="#">
            <i class="fa fa-laptop"></i> <span>Tecnologías De la Información</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <!-- BOTTON CONTROL DE PROYECTOS -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '1'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="tic.php"||$active=="upload_minuta.php"){echo "active";}?>"><a class="click_modal_cargando" href="tic.php"><i class="fa fa-circle-o"></i> Control de Proyectos</a></li>
            <?php } ?>
          </ul>
          <ul class="treeview-menu" style="display:none;">
            <!-- BOTTON CONTROL DE PROYECTOS -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '50'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="Mttos_Tics.php"||$active=="Mttos_Tics.php"){echo "active";}?>"><a class="click_modal_cargando" href="Mttos_Tics.php"><i class="fa fa-circle-o"></i> Control de Mttos</a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
      <!-- ****** TERMINA MENU DE SISTEMAS ****** -->


      <!-- ****** INICIA MENU DE TALENTO HUMANO ****** -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '19,20,22,23,31,52'); if ($modulos_valida > 0){ ?>
        <li class="<?php if($active=="rotacion_personal.php" || $active=="nomina_pagada.php" ){echo "active";}?> treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Talento Humano</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <!-- BOTTON LISTA PERSONAL -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '19'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="lista_Personal.php"){echo "active";}?>"><a class="click_modal_cargando" href="lista_Personal.php"><i class="fa fa-circle-o"></i> Lista de Personal</a>
            </li>
            <?php } ?>
              <!-- BOTTON ROTACION DE PERSONAL -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '19'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="rotacion_personal.php"){echo "active";}?>"><a class="click_modal_cargando" href="rotacion_personal.php"><i class="fa fa-circle-o"></i> Rotación de Personal(Quincenal)</a>
            </li>
            <?php } ?>
            <!--TERMINA BOTTON ROTACION DE PERSONAL -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '19'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="rotacion_personal_semana_quincena.php"){echo "active";}?>"><a class="click_modal_cargando" href="rotacion_personal_semana_quincena.php"><i class="fa fa-circle-o"></i> Rotación de Personal (Semanal)</a>
            </li>
            <?php } ?>
            <!-- BOTTON OMINA PAGADA -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '20'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="nomina_pagada.php"){echo "active";}?>"><a class="click_modal_cargando" href="nomina_pagada.php"><i class="fa fa-circle-o"></i> Nomina Pagada</a></li>
            <?php } ?>
            <!--TERMINA BOTTON OMINA PAGADA -->
            <!-- Faltas -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '22'); if ($modulos_valida > 0){ ?>
            <li class="<?php if($active=="dias_descansados.php"){echo "active";}?>"><a class="click_modal_cargando" href="dias_descansados.php"><i class="fa fa-circle-o"></i> Faltas Por Ausentismo/Prestaciones</a></li>
            <?php } ?>
              <!-- Faltas -->
              <!--tiempo extra -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '23'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="tiempo_extra.php"){echo "active";}?>"><a class="click_modal_cargando" href="tiempo_extra.php"><i class="fa fa-circle-o"></i> Tiempo extra</a></li>
              <?php } ?>
              <!--tiempo extra -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '23'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="riesgo.php"){echo "active";}?>"><a class="click_modal_cargando" href="riesgo.php"><i class="fa fa-circle-o"></i> Prima de riesgo</a></li>
              <?php } ?>
              <!-- cursos -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '31'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="rh_cat_cursos.php"){echo "active";}?>"><a class="click_modal_cargando" href="rh_cat_cursos.php"><i class="fa fa-circle-o"></i> Capacitación</a></li>
              <?php } ?>
              <!-- Finiquitos -->
              <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '52'); if ($modulos_valida > 0){ ?>
              <li class="<?php if($active=="finiquitos_RH.php"){echo "active";}?>"><a class="click_modal_cargando" href="finiquitos_RH.php"><i class="fa fa-circle-o"></i> Finiquitos</a></li>
              <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <!-- ****** TERMINA MENU DE TALENTO HUMANO ****** -->

      <!-- ****** INICIA MENU DE OPERACIONES ****** -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '6, 29, 32, 30, 38, 40, 43, 5, 27, 28, 18, 45,34,36,41,42,47,49'); if ($modulos_valida > 0){ ?>
        <li class="<?php if ($active == "rack.php" || $active == "manufactura.php"||$active == "agronegocios.php"||$active == "agronegocios_capbodega.php"){echo "active";} ?> treeview">
          <a href="#">
            <i class="fa fa-truck"></i> <span>Operaciones</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <!--TERMINA BOTTON MANUFACTURA -->
            <!-- BOTTON AGRONEGOCIOS -->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '6, 29, 32, 30, 38, 40, 43'); if ($modulos_valida > 0){ ?>
            <li class="<?php if ($active == "agronegocios.php"||$active == "agronegocios_capbodega.php"){echo "active";} ?>">
              <a href="#"><i class="fa fa-circle-o"></i> AGRONEGOCIOS
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '6'); if ($modulos_valida > 0){ ?>
                <li class="<?php if ($active == "agronegocios.php"){echo "active";} ?>"><a class="click_modal_cargando" href="agronegocios.php"><i class="fa fa-circle-o"></i> VEHÍCULOS AGRONEGOCIOS</a></li>
                <?php } ?>
                <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '53'); if ($modulos_valida > 0){ ?>
                <li class="<?php if ($active == "agronegocios_capbodega.php"){echo "active";} ?>"><a class="click_modal_cargando" href="agronegocios_capbodega.php"><i class="fa fa-circle-o"></i> CAPACIDAD DESCARGA/CARGA</a></li>
                  <?php } ?>
                <!--aZUCAr-->
                <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '29'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "detalles_azucar.php"){echo "active";} ?>"><a class="click_modal_cargando" href="detalles_azucar.php"><i class="fa fa-circle-o"></i> RESUMEN AZÚCAR</a></li>
                <?php } ?>
                <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '32'); if ($modulos_valida > 0){ ?>
                <li>
                  <a href="#"><i class="fa fa-circle-o"></i> GRAFICAS AZUCAR
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '32'); if ($modulos_valida > 0){ ?>
                      <li class="<?php if ($active == "azucar_x_anio.php"){echo "active";} ?>"><a class="click_modal_cargando" href="azucar_x_anio.php"><i class="fa fa-circle-o"></i> GRAFICAS DIR. GRAL.</a></li>
                    <?php } ?>
                    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '32'); if ($modulos_valida > 0){ ?>
                      <li class="<?php if ($active == "azucar_x_anio2.php"){echo "active";} ?>"><a class="click_modal_cargando" href="azucar_x_anio2.php"><i class="fa fa-circle-o"></i> GRAFICAS AZUCAR</a></li>
                    <?php } ?>
                  </ul>
                </li>
                <?php } ?>
                <!--granel-->
                <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '30'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "detalles_granos.php"){echo "active";} ?>"><a class="click_modal_cargando" href="detalles_granos.php"><i class="fa fa-circle-o"></i> RESUMEN GRANOS</a></li>
                <?php } ?>
                <!--Ubicacion de embarques -->
                <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '38'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "mapas_operaciones.php"){echo "active";} ?>"><a class="click_modal_cargando" href="mapas_operaciones.php"><i class="fa fa-circle-o"></i> BUQUES</a></li>
                <?php } ?>
                <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '40'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "tabla_ocupacion.php"){echo "active";} ?>"><a class="click_modal_cargando" href="tabla_ocupacion.php"><i class="fa fa-circle-o"></i> M2 EN ALMACEN</a></li>
                <?php } ?>
                <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '43'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "temperatura_Granos.php"){echo "active";} ?>"><a class="click_modal_cargando" href="temperatura_Granos.php"><i class="fa fa-circle-o"></i> CALIDAD GRANOS</a></li>
                <?php } ?>
              </ul>
            </li>
            <?php } ?>
            <!--Manufactura-->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '5, 27, 28, 18, 45'); if ($modulos_valida > 0){ ?>
            <li class="<?php if ($active == "agronegocios.php"||$active == "agronegocios_capbodega.php"){echo "active";} ?>">
              <a href="#"><i class="fa fa-circle-o"></i> MANUFACTURA
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
                <ul class="treeview-menu">
                  <!-- BOTTON MANUFACTURA -->
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '5'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "manufactura.php"){echo "active";} ?>"><a class="click_modal_cargando" href="manufactura.php"><i class="fa fa-circle-o"></i>  VEHÍCULOS MANUFACTURA</a></li>
                  <?php } ?>
                  <!--Boton Costos-->
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '27'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "Gastos_Maquinaria.php"){echo "active";} ?>"><a class="click_modal_cargando" href="Gastos_Maquinaria.php"><i class="fa fa-circle-o"></i>  COSTOS</a></li>
                  <?php } ?>
                  <!--  ocupacion -->
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '28'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "calculo_Ocupacion.php"){echo "active";} ?>"><a class="click_modal_cargando" href="calculo_Ocupacion.php"><i class="fa fa-circle-o"></i>  OCUPACIÓN DE ALMACEN</a></li>
                  <?php } ?>
                  <!-- BOTTON UBICACIÓN DE MERCANCÍA -->
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '18'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "rack.php"){echo "active";} ?>"><a class="click_modal_cargando" href="rack.php"><i class="fa fa-circle-o"></i>  UBICACIÓN DE MERCANCÍA</a></li>
                  <?php } ?>
                  <!-- INFORMACION VIAS -->
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '45'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "vias_Informacion.php"){echo "active";} ?>"><a class="click_modal_cargando" href="vias_Informacion.php"><i class="fa fa-circle-o"></i>  INFORMACION DE VIAS</a></li>
                  <?php } ?>

                </ul>
              </a>
            </li>
            <?php } ?>

            <!--OPERACIONES ALO-->
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '34,36,41,42,47,49');
                  $modulos_valida2 = Perfil::modulos_valida($iid_empleado, '47'); if ($modulos_valida > 0){ ?>
            <li class="<?php if ($active == "agronegocios.php"||$active == "agronegocios_capbodega.php"){echo "active";} ?>">
              <a href="#"><i class="fa fa-circle-o"></i> OPERACIONES ALO
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
                <ul class="treeview-menu">

                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '34'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "piezas_danadas.php"){echo "active";} ?>"><a class="click_modal_cargando" href="piezas_danadas.php"><i class="fa fa-circle-o"></i> Mercancia No Conforme</a></li>
                  <?php } ?>
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '36'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "errores_captura.php"){echo "active";} ?>"><a class="click_modal_cargando" href="errores_captura.php"><i class="fa fa-circle-o"></i> Errores Captura </a></li>
                  <?php } ?>
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '41'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "operaciones_manufactura_alo.php"){echo "active";} ?>"><a class="click_modal_cargando" href="operaciones_manufactura_alo.php"><i class="fa fa-circle-o"></i> Efectividad Carga y Descarga (ALO)</a></li>
                  <?php } ?>
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '42'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "OcupacionClienteAlo.php"){echo "active";} ?>"><a class="click_modal_cargando" href="OcupacionClienteAlo.php"><i class="fa fa-circle-o"></i> Tiempo De Mercancia En Almacen (ALO)</a></li>
                  <?php } ?>
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '42'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "CalculoOcupacionAlo.php"){echo "active";} ?>"><a class="click_modal_cargando" href="CalculoOcupacionAlo.php"><i class="fa fa-circle-o"></i>  OcupaciÓn De Almacen (ALO)</a></li>
                  <?php } ?>
                  <!-- REPORTE ALO -->
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '47'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "reporte_alo.php"){echo "active";} ?>"><a class="click_modal_cargando" href="reporte_alo.php"><i class="fa fa-circle-o"></i>  REPORTE (ALO)</a></li>
                  <?php } ?>
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '49'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "Contenedores_Pendientes_ALO.php"){echo "active";} ?>"><a class="click_modal_cargando" href="Contenedores_Pendientes_ALO.php"><i class="fa fa-circle-o"></i>  REPORTE CONTENEDORES PENDIENTES/ASIGNADOS (ALO)</a></li>
                  <?php } ?>
                  <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '51'); if ($modulos_valida > 0){ ?>
                  <li class="<?php if ($active == "rackAlo.php"){echo "active";} ?>"><a class="click_modal_cargando" href="rackAlo.php"><i class="fa fa-circle-o"></i> REPORTE UBICACIONES (ALO)</a></li>
                  <?php } ?>
                </ul>
              </a>
            </li>
            <?php } ?>


            <!--TERMINA BOTTON AGRONEGOCIOS -->
          </ul>
        </li>
        <?php } ?>
      <!-- ****** TERMINA MENU DE OPERACIONES ****** -->

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
<!--  ###################################### Termina Menu Izquierdo ################################# -->
<!-- ############################################# Menu Derecho ##################################-->
