<?php  
/*
* © Argo Almacenadora ®
* Fecha: 30/11/2016
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard 
* Version:
*/
session_start();
/*inicia code solucion quita mensaje reenvio de form*/
if( $_SERVER['REQUEST_METHOD'] == 'POST')
{ 
  header("location: ".$_SERVER["PHP_SELF"]." ");
}
/*termina code solucion quita mensaje reenvio de form*/

include_once '../class/Perfil.php';
$instacia_modulo  = new Perfil;
$iid_empleado = $_SESSION['iid_empleado'];

//comprobar sesion iniciada
  if(!isset($_SESSION['usuario']))
    header('Location: ../index.php');
  //comprobar tiempo de expiracion
  $now = time();
  if($now > $_SESSION['expira']){
    session_destroy();
    header('Location: ../index.php');
  }
//objeto conexion a base de datos
include_once '../libs/conOra.php';
$conn   = conexion::conectar();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], "18");
if($modulos_valida == 0)
{ 
  header('Location: index.php');
}
///////////////////////////////////////////
/*Class*/
include_once "../class/Rack.php";
$objRack = new Rack();

/**----------------------sessiones----------------------**/
/*id_plaza*/
if ( isset($_POST["rackId_plaza"]) )
  $_SESSION['rackId_plaza'] = $_POST['rackId_plaza'];
  $id_plaza = @$_SESSION['rackId_plaza'];

/*id_almacen*/
if (  isset($_POST["rackId_almacen"]) )
  $_SESSION['rackId_almacen'] = $_POST['rackId_almacen'];
  $id_almacen = @$_SESSION['rackId_almacen']; 

/*id_cliente*/
if (  isset($_POST["rackId_cliente"]) )
  $_SESSION['rackId_cliente'] = $_POST['rackId_cliente'];
  $id_cliente = @$_SESSION['rackId_cliente']; 

/*mercancia*/
if (  isset($_POST["rackId_mercancia"]) )
  $_SESSION['rackId_mercancia'] = $_POST['rackId_mercancia'];
  $mercancia = @$_SESSION['rackId_mercancia']; 

/*letraRack*/
if (  isset($_POST["letraRack"]) )
  $_SESSION['letraRack'] = $_POST['letraRack'];
  $letraRack = @$_SESSION['letraRack'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="author" content="Jorge Tejeda Juan">
  <title>Argo Almacenadora | Dashboard</title>
  <link rel="shortcut icon" href="../assets/ico/favicon.png"> 
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- jQuery 2.2.3 -->
  
    <!-- Incluye los estilos de JQuery -->

  <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">

  <!-- Incluye la biblioteca Jquery -->

  <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

  <!-- Incluye la biblioteca Jquery mobile -->

  <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>


  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
  <!-- Inicia fancyBox SS files -->
    <!-- Add fancyBox main JS and CSS files -->
  <link rel="stylesheet" type="text/css" href="../plugins/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
    <!-- Add Button helper (this is optional) -->
  <link rel="stylesheet" type="text/css" href="../plugins/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
    <!-- Add Thumbnail helper (this is optional) -->
  <link rel="stylesheet" type="text/css" href="../plugins/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" /> 
  <!-- Termina fancyBox SS files --> 
  


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav" data-spy="scroll" data-target="#scrollspy">
<div class="wrapper">

  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <a href="index.php" class="navbar-brand"><b>Argo</b>ALM</a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="index.php">Inicio <span class="sr-only">(current)</span></a></li>
          </ul>
        </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <!-- Messages: style can be found in dropdown.less-->
            <!-- /.messages-menu -->

            <!-- Notifications Menu -->
            <!-- Tasks Menu -->
            <!-- User Account Menu -->
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
              <!-- Menu Body -->
              <!--<li class="user-body">
                 <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                  </div>
                </div>
              </li>-->
              <!-- Menu Footer-->
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
          </ul>
        </div>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>





<style type="text/css">
/*TABLA PARA EL RACK*/
.tablaRack {
        width: 110px;
        height: 172px; /* Ancho y alto fijo */
        overflow: hidden; /* Se oculta el contenido desbordado */
        background-color: #fff;
        /*border: 2px solid #b2b2b2;*/
    }
</style>

<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

  <!-- ############################ INICIA SECCION FILTROS DISPONIBLES ############################# --> 
  <section>
    <div class="box box-info">    
      <div class="box-header with-border">
        <h5 class="box-title"><i class="fa fa-search"></i> Filtros y Búsquedas de Mercancía</h5>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <!-- SELECT OPTION PLAZA -->
        <div class="col-md-2 col-sm-2 col-xs-12 invoice-col">
        <form method="post">
            <strong><i class="fa fa-cube"></i> Plaza:</strong>
            <address>
              <select class="form-control select2" name="rackId_plaza" style='width: 100%;' onchange='this.form.submit()'>
              <option selected="true" disabled>Seleccione la Plaza</option>
              <?php 
              $selectPlaza = $objRack->selectPlaza();
              for ($i=0; $i <count($selectPlaza) ; $i++) {
            ?>
            <option <?php if (@$id_plaza == $selectPlaza[$i]["IID_PLAZA"]) echo "selected"; ?> value="<?=$selectPlaza[$i]["IID_PLAZA"]?>">
              (<?=$selectPlaza[$i]["IID_PLAZA"]?>)--<?=$selectPlaza[$i]["V_RAZON_SOCIAL"]?>
            </option>
            <?php
              }
            ?>
              </select>
              <input type="hidden" name="rackId_almacen" value="">
              <input type="hidden" name="rackId_cliente" value="">
              <input type="hidden" name="rackId_mercancia" value="">
              <input type="hidden" name="letraRack" value="">
            </address>
        </form>
        </div>
        <!-- SELECT OPTION ALMACEN -->
        <div class="col-md-2 col-sm-2 col-xs-12 invoice-col">
        <form method="post">
            <strong><i class="fa fa-cubes"></i> Almacen:</strong>
            <address>
              <select class="form-control select2" name="rackId_almacen" style='width: 100%;' onchange='this.form.submit()'>
              <option selected="true" disabled>Seleccione el Almacen</option>
              <?php
              if ( isset($id_plaza) && !empty($id_plaza) ){//if
                $selectAlmacen = $objRack->selectAlmacen($id_plaza);
                for ($i=0; $i <count($selectAlmacen) ; $i++) {// for
              ?>
              <option <?php if (@$id_almacen == $selectAlmacen[$i]["IID_ALMACEN"]) echo "selected"; ?> value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>">
              (<?=$selectAlmacen[$i]["IID_ALMACEN"]?>)--<?=$selectAlmacen[$i]["V_NOMBRE"]?>
            </option>
              <?php
                }// /.for
              }// /.if
              ?>
              </select>
            <input type="hidden" name="rackId_cliente" value="">
            <input type="hidden" name="rackId_mercancia" value="">
            <input type="hidden" name="letraRack" value="">
            </address>
        </form>
        </div>
        <!-- SELECT OPTION CLIENTE -->
        <div class="col-md-3 col-sm-3 col-xs-12 invoice-col">
        <form method="post">
            <strong><i class="fa fa-users"></i> Cliente:</strong>
            <address>
              <select class="form-control select2" name="rackId_cliente" style="width: 100%;" onchange='this.form.submit()'>
              <option selected="true" disabled>Seleccione el Cliente</option>
              <?php
              if ( isset($id_almacen) && !empty($id_almacen) ){//if
                $selectCliente = $objRack->selectCliente($id_almacen);
                if ($id_cliente == 'ALL'){ echo '<option selected value="ALL">ALL</option>';}else{echo '<option value="ALL">ALL</option>';}
                
                for ($i=0; $i <count($selectCliente) ; $i++) {// for
              ?>
              <option <?php if (@$id_cliente == $selectCliente[$i]["IID_NUM_CLIENTE"]) echo "selected"; ?> value="<?=$selectCliente[$i]["IID_NUM_CLIENTE"]?>">
              (<?=$selectCliente[$i]["IID_NUM_CLIENTE"]?>)--<?=$selectCliente[$i]["V_RAZON_SOCIAL"]?>
            </option>
              <?php
                }// /.for
              }// /.if
              ?>
              </select>
            </address>
        </form>
        </div>
        <!-- SELECT OPTION MERCANCIA -->
        <div class="col-md-3 col-sm-3 col-xs-12 invoice-col">
      <form method="post">          
            <strong><i class="fa fa-dropbox"></i> Mercancia:</strong>
            <address>
              <select class="form-control select2" name="rackId_mercancia" style="width: 100%;" onchange='this.form.submit()'>
              <option selected="true" disabled>Seleccione Tipo de Mercancia</option>
              <?php if ( isset($id_cliente) && !empty($id_cliente) ){//if ?>
              <option <?php if(@$mercancia == 'ALL') echo 'selected'; ?> value="ALL">ALL</option>
              <option <?php if(@$mercancia == 'FISCAL') echo 'selected'; ?> value="FISCAL">FISCAL</option>
              <option <?php if(@$mercancia == 'NACIONAL') echo 'selected'; ?> value="NACIONAL">NACIONAL</option>
              <?php }// /.if ?>
              </select>
            </address>
        </form>
        </div>
        <!-- SELECT OPTION RACK -->
        <div class="col-md-2 col-sm-2 col-xs-12 invoice-col">
        <form method="post">
            <strong><i class="fa fa-table"></i> Rack:</strong>
            <address>
              <select class="form-control select2" name="letraRack" style='width: 100%;' onchange='this.form.submit()'>
              <option selected="true" disabled>Seleccione Letra del Rack</option>
              <?php
              if ( isset($mercancia) && !empty($mercancia) ){//if
                $selectRack = $objRack->selectRack($id_almacen);
                if ($letraRack == 'TODOS'){ echo '<option selected value="TODOS">TODOS</option>';}else{echo '<option value="TODOS">TODOS</option>';}
                
                for ($i=0; $i <count($selectRack) ; $i++) {// for
              ?>
              <option <?php if (@$letraRack == $selectRack[$i]["BATERIA"]) echo "selected"; ?> value="<?=$selectRack[$i]["BATERIA"]?>">
              <?=$selectRack[$i]["BATERIA"]?>
            </option>
              <?php
                }// /.for
              }// /.if
              ?>
              </select>
            </address>
        </form>
        </div>
        <!-- /.SELECT OPTION -->

            <div class="input-group">
              <span class="input-group-addon">Buscar</span>
              <input type="search" class="form-control search-derecha">
                <span class="input-group-btn">
              <span class="input-group-btn">
                  <button type="button" class="btn btn-flat bg-blue search-btn"><i class="fa fa-search text-gray"></i></button> 
              </span>
            </div>

            <code class="titleCoin" style="display: none">Coincidencias encontradas:</code>

            <p id="lis_span" style="height: 70px; overflow: auto;"></p>

      </div><!--/.box-body-->

      <code class="loading" style="display:none;width:240px;height:25px;border:1px solid black;position:absolute;top:50%;left:0%;padding:2px;">
        Procesando, espere un momento …….
      </code>
      
      <div class="box-footer">
            <div class="row">
              <!-- /.col -->
              <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                  <span class="description-percentage text-info"><p id="valPosiciones">0</p></span>
                  <small class="description-header" style="font-size:12px;">Posiciones</small>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                  <span class="description-percentage text-green"><p id="valLibre">0</p></span>
                  <small class="description-header" style="font-size:12px;">Libres</small>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-3 col-xs-6">
                <div class="description-block">
                  <span class="description-percentage text-blue"><p id="valOcupado">0</p></span>
                  <small class="description-header" style="font-size:12px;">Ocupadas</small>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                  <span class="description-percentage text-yellow" title="Arribos no Ubicados"><p id="valNoUbi">0</p></span>
                  <small class="description-header" style="font-size:12px;" title="Arribos no Ubicados">No Ubicado</small>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>


    </div> 
  </section> 
  <!-- ########################### TERMINA SECCION FILTROS DISPONIBLES ########################### -->


<?php if ( isset($letraRack) && !empty($letraRack) ){// if consulta posicion ?>

  <!-- ############################## INICIA SECCION IZQUIERDA DERECHA ############################## -->
  <div class="row">
    <!-- =========== INICIA SECCION MERCANCIA IZQUIERDA (MERCANICA UBICADA-NO UBICADA) =========== -->
    <section class="col-lg-12 connectedSortable"><!-- col-lg-9 -->
        
        <!-- INICIA SECCION MERCANCIA UBICADA -->
            <section>
              <div class="row">
                <div class="col-xs-12">
                  <div class="box box-default box-solid"><!-- box -->
                    <div class="box-header"><!-- box-header -->  
                      <h3 class="box-title"><i class="fa fa-table"></i> Mercancía Ubicada en Rack </h3>
              
                      <div class="box-tools">
                         <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      </div>
                    </div><!-- /.box-header -->  
              
                    <div class="box-body"><!-- box-body rack dibujo -->

                    <style type="text/css" media="screen">
                    .caja {
            width:3.7vh;
            max-width:100px;
            height:2.5vh;
            max-height:100px;
            position:relative;
            background:#D79F46;
          }

          .cajanoubi {
                      width:4.5vh;
                      max-width:100px;
                      height:4vh;
                      max-height:100px;
                      position:relative;
                      background:#F1DBBE;
                    }

          .palet {
            width:3.45vh;
            max-width:100px;
            height:.6vh;
            max-height:100px;
            position:relative;
            background:#ECDAC6;
          } 
                    </style>

                    <script type="text/javascript">                     
          function DetalleUbica(id_plaza,id_almacen,id_cliente,mercancia,rack,profundidad){
             
            $.ajax({
                            type: 'POST',
                          url: '../action/rackAjax.php',
                          cache:false,
                            data: {"DibMerca" : 1, "id_plaza" : id_plaza, "id_almacen" : id_almacen, "id_cliente" : id_cliente, "mercancia" : mercancia, "rack" : rack, "profundidad" : profundidad},
                            success: function (response) {//success
                            var dataJson = JSON.parse(response);
                            //console.log(dataJson);
                            var span = [];

                            for(var k in dataJson) {
                  //console.log( (dataJson[k].DETALLE1+dataJson[k].DETALLE2) );
                var v_ubicacion = dataJson[k].V_DESCRIPCION;
                var v_detalle = $.trim(dataJson[k].DETALLE1+" "+dataJson[k].DETALLE2);

                            if ( v_detalle == "" ){
                              $("#imgUrl"+v_ubicacion).replaceWith('<a class="fancybox fancybox.iframe" href="rack_detubi.php?detUbi='+v_ubicacion+'&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>"><div class="palet" id="imgUrl'+v_ubicacion+'" ></div></a>');
                              span[k] = '<a class="itemDer v_libre" style="display: none;" href="#imgUrl'+v_ubicacion+'" data-info="libre '+(v_ubicacion).toLowerCase()+'"><b>Ubicación: '+v_ubicacion+'</b><a/>';

                            }else{
                              $("#imgUrl"+v_ubicacion).replaceWith('<a class="fancybox fancybox.iframe" href="rack_detubi.php?detUbi='+v_ubicacion+'&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>"><div class="caja" id="imgUrl'+v_ubicacion+'" ></div></a>');
                              span[k] = '<a class="itemDer v_ocupado" style="display: none;" href="#imgUrl'+v_ubicacion+'" data-info="ocupado '+(v_detalle).toLowerCase()+'"><b>Ubicación: '+v_ubicacion+'</b></a>';
                            }
              }
              $(span.join('  ')).appendTo('#lis_span');
              //var x = document.getElementById("lis_span");
                //x.innerHTML = span.join(' -- ');

                            }//.success
                        });
          } 
          </script>

          <code class="loading" style="display:none;width:240px;height:25px;border:1px solid black;position:absolute;top:50%;left:0%;padding:2px;">
              Procesando, espere un momento …….
            </code>

                    <div style="height: 600px; overflow: auto;"><!-- overflow #1 -->

                      <?php
                      $rackProfundidad = $objRack->rackProfundidad($id_almacen,$letraRack);
                      for ($i=0; $i <count($rackProfundidad) ; $i++) {// for rackProfundidad 
                      ?>

                      <div class="table-responsive"><!-- table-responsive -->
                        <table><!-- table principal -->
                            <caption><b>RACK <?=$rackProfundidad[$i]["RACK"]?> PROFUNDIDAD <?=$rackProfundidad[$i]["PROFUNDIDAD"]?></b></caption>
                            <tr><!-- ******* TR DEFINE RACK PROFUNDIDAD ******* -->

<!-- ===================================================== CONSULTA DE COLUMNAS DENTRO DE DEL FOR ===================================================== -->
              <?php 
              $rackColumna = $objRack->rackColumna($id_almacen,$rackProfundidad[$i]["RACK"],$rackProfundidad[$i]["PROFUNDIDAD"]);
              for ($a=0; $a <count($rackColumna) ; $a++) {// for columnas
              ?>
                          <td><!-- ******* TD DEFINE COLUMNAS ******* -->
                          <b>COLUMNA <?=$rackColumna[$a]["COLUMNA"]?></b>
                          <table class="tablaRack" bgcolor="#00FF00" style=" border: 3px solid #255F98;"  border="0" width="250">

<!-- ****************************************************** CONSULTA DE NIVEL DENTRO DE DEL FOR ****************************************************** -->
              <?php 
              $rackNivel = $objRack->rackNivel($id_almacen,$rackProfundidad[$i]["RACK"],$rackProfundidad[$i]["PROFUNDIDAD"],$rackColumna[$a]["COLUMNA"]);
              for ($b=0; $b <count($rackNivel) ; $b++) {// for nivel
              ?>
              <tr><!-- TR DEFINE NIVEL -->

<!-- /////////////////////////////////////////////////// CONSULTA DE POSICION DENTRO DE DEL FOR /////////////////////////////////////////////////// -->
               
              
              <?php
              $v_posicion = explode(",", substr($rackNivel[$b]["POSICION"],0,-1));
              for ($c=0; $c <count($v_posicion) ; $c++) { 
              ?>

              <td title="Rack: <?=$rackNivel[$b]["RACK"]?> Nivel: <?=$rackNivel[$b]["NIVEL"]?> &#10; Posición: <?=$v_posicion[$c]?>  Profundidad: <?=$rackNivel[$b]["PROFUNDIDAD"]?>" style="border-bottom: 3px solid #054A8B;" valign="bottom" align="center"><!-- TD DEFINE POSICION -->
                        
                          <img id="imgUrl<?=$rackNivel[$b]["RACK"].$rackNivel[$b]["COLUMNA"].$rackNivel[$b]["NIVEL"].$v_posicion[$c].$rackNivel[$b]["PROFUNDIDAD"]?>" >

                          </td><!-- /.TD DEFINE POSICION -->

                          <?php } ?>


<!-- /////////////////////////////////////////////////// /.CONSULTA DE POSICION DENTRO DE DEL FOR /////////////////////////////////////////////////// -->


              </tr><!-- /.TR DEFINE NIVEL -->
              <?php }// /.for nivel ?>
<!-- ****************************************************** /.CONSULTA DE NIVEL DENTRO DE DEL FOR ****************************************************** -->                            

                          </table>
                        </td><!-- ******* /.TD DEFINE COLUMNAS ******* -->
                        <?php }// /.for columnas ?>
<!-- ===================================================== /.CONSULTA DE COLUMNAS DENTRO DE DEL FOR ===================================================== -->                       


                            </tr><!-- ******* TR DEFINE RACK PROFUNDIDAD ******* -->
                          </table><!-- /.table principal -->
                      </div><!-- /.table-responsive -->

                      <script type="text/javascript">
                        DetalleUbica(<?=$id_plaza?>,<?=$id_almacen?>,'<?=$id_cliente?>','<?=$mercancia?>','<?=$rackProfundidad[$i]["RACK"]?>','<?=$rackProfundidad[$i]["PROFUNDIDAD"]?>');
                      </script>

                      <?php }// /.for rackProfundidad ?>

                    </div><!-- /.overflow #1 -->

                    </div><!-- /.box-body rack dibujo -->

                  </div><!-- /.box -->
                </div>
              </div>
            </section>
            <!-- TERMINA SECCION MERCANCIA UBICADA -->



            <!-- INICIA SECCION MERCANCIA NO UBICADA -->
            <section>
              <div class="row">
                <div class="col-xs-12">
                  <div class="box box-default box-solid"><!-- box -->
                    <div class="box-header"><!-- box-header -->  
                      <h3 class="box-title"><i class="fa fa-dropbox"></i> Mercancía no Ubicada </h3>
              
                      <div class="box-tools">
                         <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      </div>
                    </div><!-- /.box-header -->  
              
                    <div class="box-body"><!-- box-body rack dibujo --> 

                    <div style="height: 600px; overflow: auto;"><!-- overflow #2 -->

                    <style type="text/css">
                    .liMerNoUbi li {
                      width: 65px;
                        height: 80px;
                        margin: 3px;
                    } 
                    </style>

                    <!-- incia li mercancia no ubicada -->
                    <div class="row liMerNoUbi">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 center">
                        <ul class="users-list"><!-- users-list -->

                          <script type="text/javascript">
                          var span = [];
                          </script>
                          <!-- inicia for li mercancia no ubicada -->
                          <?php
                          $mercanciaNoUbi = $objRack->mercanciaNoUbi($id_plaza,$id_almacen,$id_cliente,$mercancia);
                          for ($i=0; $i <count($mercanciaNoUbi) ; $i++) {// for mercanciaNoUbi
                          $detalle = str_replace ("\r\n", "<\br>", $mercanciaNoUbi[$i]["V_DETALLE"]);
                          ?>
              <li>            
                <a class="fancybox fancybox.iframe" href="rack_detubi.php?detArr=<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>">
                <div id="imgUrl<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>" class="cajanoubi" ></div></a>
                <span style="font-size:10px;" class="label bg-gray"><?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?></span>
                        </li>

                        <script type="text/javascript">
                        span[<?=$i?>] = '<a class="itemDer v_noubi" style="display: none;" href="#imgUrl<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>" data-info="no ubicado <?=strtolower($mercanciaNoUbi[$i]["ID_ARRIBO"].' '.$detalle)?>"><b>Arribo: <?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?></b></a>';
                        </script>
                        <?php }// /.for mercanciaNoUbi ?>
                        <!-- /.inicia for li mercancia no ubicada --> 
                        <script type="text/javascript">
              $(span.join('  ')).appendTo('#lis_span');                         
                        </script>
            
                        </ul><!-- /.users-list -->
                      </div>
                    </div>
                  <!-- termina li mercancia no ubicada -->

                    </div><!-- /.overflow #2 -->

                    <code class="loading" style="display:none;width:240px;height:25px;border:1px solid black;position:absolute;top:50%;left:0%;padding:2px;">
              Procesando, espere un momento …….
            </code>

                    </div><!-- /.box-body rack dibujo -->
                  </div><!-- /.box -->
                </div>
              </div>
            </section>
            <!-- TERMINA SECCION MERCANCIA NO UBICADA -->

            
          
    </section><!-- /.col-lg-9 -->
    <!-- =========== TERMINA SECCION MERCANCIA IZQUIERDA (MERCANICA UBICADA-NO UBICADA) =========== -->



    <script>
    $( document ).ajaxStop(function() {
      console.log("termino toda peticion ajax");
      busquedaJs();
      operacion2();

      /*COLOREA IMAGEN CON CLICK*/      
      $(".itemDer").click(function (){
        //$('.itemMer').css('background-color','inherit');//background img normal
        $( $(this).attr("href") ).css("background", "#E51C23");
      });

    });
    </script>




  </div>
  <!-- ############################## TERMINA SECCION IZQUIERDA DERECHA ############################## -->
<?php }// /.if consulta posicion ?> 
      

    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal --> 
<!-- ################################### Termina Contenido de la pagina ################################### -->



</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<?php if ( isset($letraRack) && !empty($letraRack) ){// if consulta posicion ?>
<script type="text/javascript">
var CLICK = {
    onReady : function() {

      operacion2();

        $(".search-btn").click(function (){
          busquedaJs();
    });
    },
};
$(document).ready(CLICK.onReady);

/* ---------------------------------- inicia script busqueda ---------------------------------- */
function busquedaJs(){
  
  var itemDer = $('.itemDer'),
  input = $(".search-derecha").val();
    
  // Search Listener
  switch(true){ 
    case input === '':
      $(".titleCoin").css('display', 'none');//oculta titulo coincidencia
      itemDer.css('display', 'none');
      operacion2();
      break;
    default:
      //$('.itemMer').css('background-color','inherit');//background img normal
      $(".titleCoin").css('display', 'block');//muetra titulo coincidencia
      itemDer.css('display', 'none');
            itemDer.filter('[data-info*="' + input.toLowerCase() + '"]').css('display', 'inline');

            operacion();
            var coinEnc = $('.itemDer').filter(function(){ return $(this).css('display') == 'inline'; }).length ;

            $(".titleCoin").text('Coincidencias encontradas: '+coinEnc);
      break;
  }
   
} 
/* ---------------------------------- termina script busqueda ---------------------------------- */

function operacion(){

  var v_libre =  $('.v_libre').filter(function(){ return $(this).css('display') == 'inline'; }).length,
  v_ocupado =  $('.v_ocupado').filter(function(){ return $(this).css('display') == 'inline'; }).length,
  v_posicion = v_libre + v_ocupado,
  v_noubi =  $('.v_noubi').filter(function(){ return $(this).css('display') == 'inline'; }).length;

  document.getElementById("valPosiciones").innerHTML = v_posicion;
  document.getElementById("valLibre").innerHTML = v_libre;
  document.getElementById("valOcupado").innerHTML = v_ocupado;
  document.getElementById("valNoUbi").innerHTML = v_noubi;

}

function operacion2(){

  var v_libre =  $('.v_libre').filter(function(){ return $(this).css('display') == 'none'; }).length,
  v_ocupado =  $('.v_ocupado').filter(function(){ return $(this).css('display') == 'none'; }).length,
  v_posicion = v_libre + v_ocupado,
  v_noubi =  $('.v_noubi').filter(function(){ return $(this).css('display') == 'none'; }).length;

  document.getElementById("valPosiciones").innerHTML = v_posicion;
  document.getElementById("valLibre").innerHTML = v_libre;
  document.getElementById("valOcupado").innerHTML = v_ocupado;
  document.getElementById("valNoUbi").innerHTML = v_noubi;

}

</script>
<?php }// /.if consulta posicion ?>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script> 
<!-- Inicia FancyBox JS -->
<script type="text/javascript" src="../plugins/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('.fancybox').fancybox();
});
</script>
<!-- Termina FancyBox JS -->
</body>
</html>
