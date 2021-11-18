<?php
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:rotacion_personal.php");
  //return;
}

session_start();
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

include_once '../class/Encuestas_Realizadas.php';
$obj_class = new RotacionPersonal();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
    //echo $clase;
  }

  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 48);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////

/* $_GET FECHA */
include '../class/Nomina_pagada.php';
$modelNomina = new NominaPagada();
$fec_corte = $modelNomina->sql(1,null);
/*----- GET FECHA -----*/
$fecha = $fec_corte[0]["MES1"]."-".$fec_corte[0]["MES2"];

if ( isset($_GET["fecha"]) ){
  if ( $obj_class->validateDate(substr($_GET["fecha"],0,10)) AND $obj_class->validateDate(substr($_GET["fecha"],11,10)) ){
    $fecha = $_GET["fecha"];
  }else{
    $fecha = $fec_corte[0]["MES1"]."-".$fec_corte[0]["MES2"];
  }
}
/* $_GET FIL_CHECK */
$fil_check = "on";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}


$fil_habilitado = "ALL";
if (isset($_GET["fil_habilitado"])) {
  $fil_habilitado = $_GET["fil_habilitado"];
}
/* $_GET PLAZA */
$plaza = "ALL";
if ( isset($_GET["plaza"]) ){
  switch ($_GET["plaza"]) {
    case 'CORPORATIVO': $plaza = $_GET["plaza"]; break;
    case 'CÓRDOBA': $plaza = $_GET["plaza"]; break;
    case 'MÉXICO': $plaza = $_GET["plaza"]; break;
    case 'GOLFO': $plaza = $_GET["plaza"]; break;
    case 'PENINSULA': $plaza = $_GET["plaza"]; break;
    case 'PUEBLA': $plaza = $_GET["plaza"]; break;
    case 'BAJIO': $plaza = $_GET["plaza"]; break;
    case 'OCCIDENTE': $plaza = $_GET["plaza"]; break;
    case 'NORESTE': $plaza = $_GET["plaza"]; break;
    default: $plaza = "ALL"; break;
  }
}


/*ALMACEN */
$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}

$cliente = "ALL";
if (isset($_GET["cliente"])) {
    $cliente = $_GET["cliente"];
}
/* $_GET CONTRATO */
$contrato = "ALL";
if ( isset($_GET["contrato"]) ){
  switch ($_GET["contrato"]) {
    case '0': $contrato = $_GET["contrato"]; break;
    case '1': $contrato = $_GET["contrato"]; break;
    case '3': $contrato = $_GET["contrato"]; break;
    case '2': $contrato = $_GET["contrato"]; break;
    default: $contrato = "ALL"; break;
  }
}
/* $_GET DEPARTAMENTO */
$departamento = "ALL";
if ( isset($_GET["depto"]) ){
  $select_depto = $obj_class->filtros(2,$departamento);
  for ($i=0; $i <count($select_depto) ; $i++) {
    if ( $select_depto[$i]["IID_DEPTO"] == $_GET["depto"]){
      $departamento = $_GET["depto"]; break;
    }
  }
}
/*----- GET AREA -----*/
$area = "ALL";
if ( isset($_GET["area"]) ){
  if ( $departamento != 'ALL' ){
    $select_area = $obj_class->filtros(3,$departamento);
    for ($i=0; $i <count($select_area) ; $i++) { // FOR
      if ( $select_area[$i]["IID_AREA"] == $_GET["area"]){
        $area = $_GET["area"]; break;
      }
    }// /.FOR
  }
}

if($plaza != 'ALL'){
  $grafica_Paste = $obj_class->grafica_Paste($plaza,$contrato,$departamento,$area,$fil_check,$fecha,$fil_habilitado);
}
//GRAFICA
$grafica = $obj_class->grafica($fecha, $cliente);
$graficaMensual = $obj_class->graficaMensual($cliente, $fecha);
$widgetsClientes = $obj_class->widgets($fecha, $cliente);
$widgetsClientesHabDir = $obj_class->tipo_respuestas($fecha, $cliente);
//TABLA DETALLE ACTIVOS

$tablaEncuestas = $obj_class->tablaBaja($cliente, $fecha);

$consulta_clientes = $obj_class->filtros(5, 'ALL');
//$selectAlmacen = $obj_class->filtros(4,$departamento);

?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<style media="screen">
@import url('//cdn.datatables.net/1.10.2/css/jquery.dataTables.css');
td.details-control {
  background: url('http://www.datatables.net/examples/resources/details_open.png') no-repeat center center;
  cursor: pointer;
}
tr.shown td.details-control {
  background: url('http://www.datatables.net/examples/resources/details_close.png') no-repeat center center;
}
</style>
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php /*echo $fecha;*/ ?>
        Dashboard
        <small>Encuestas Clientes</small>
      </h1>
    </section>
    <!-- Main content -->
    <section class="Content" style="margin:1%"><!-- Inicia la seccion de los Widgets -->
      <div class="row">
      <!-- Widgets Cartas cupo expedidas -->
      <div class="col-lg-3 col-xs-6" style="display:none;">
      <div class="info-box bg-green">
        <span class="info-box-icon"><i class="fa fa-question"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">TOTAL PREGUNTAS</span>
          <span class="info-box-number"><?php
                                          $TOTAL_PRE = 0;
                                          for ($i=0; $i < count($grafica); $i++) {
                                            $TOTAL_PRE = $TOTAL_PRE + $grafica[$i]["CONTAR"];
                                          }
                                          echo $TOTAL_PRE;
                                         ?></span>
          <div class="progress">
            <div class="progress-bar" style="width: 60%"></div>
          </div>
          <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
        </div>
      </div>
      </div>
        <!-- Termino Widgets Cartas cupo expedidas -->
        <!-- Widgets Cartas cupo no arribadas -->
        <div class="col-lg-3 col-xs-6" style="display:none;">
        <div class="info-box bg-green">
          <span class="info-box-icon"><i class="fa fa-question"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">POSITIVAS</span>
            <span class="info-box-number">
              <?php
                                              $TOTAL_PREA = 0;
                                              for ($i=0; $i < count($grafica); $i++) {
                                                if ($grafica[$i]["TIPO_RES"] == "POSITIVA") {
                                                    $TOTAL_PREA = $TOTAL_PREA + $grafica[$i]["CONTAR"];
                                                }

                                              }
                                              echo $TOTAL_PREA;
                                             ?>
            </span>
            <div class="progress">
              <div class="progress-bar" style="width: 80%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
          </div>
        </div>
        </div>
        <!-- Termina Widgets Cartas cupo no arribadas -->
        <!-- Widgets Cartas cupo canceladas -->
        <div class="col-lg-3 col-xs-6" style="display:none;">
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-question"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">NEGATIVAS</span>
            <span class="info-box-number"><?php
                                            $TOTAL_PREN = 0;
                                            for ($i=0; $i < count($grafica); $i++) {
                                              if ($grafica[$i]["TIPO_RES"] == "NEGATIVA") {
                                                  $TOTAL_PREN = $TOTAL_PREN + $grafica[$i]["CONTAR"];
                                              }

                                            }
                                            echo $TOTAL_PREN;
                                           ?></span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
          </div>
        </div>
        </div>

        <div class="col-lg-3 col-xs-6" style="display:none;">
        <div class="info-box bg-yellow">
          <span class="info-box-icon"><i class="fa fa-question"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">MEJORAR</span>
            <span class="info-box-number"><?php
                                            $TOTAL_PREN = 0;
                                            for ($i=0; $i < count($grafica); $i++) {
                                              if ($grafica[$i]["TIPO_RES"] == "MEJORA") {
                                                  $TOTAL_PREN = $TOTAL_PREN + $grafica[$i]["CONTAR"];
                                              }

                                            }
                                            echo $TOTAL_PREN;
                                           ?></span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
          </div>
        </div>
        </div>

        <!-- Termino Widgets Cartas cupo canceladas -->
        <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-percent"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">TOTAL EFECTIVIDAD HABILITADOS</span>
            <span class="info-box-number"><?php
                                            $tabla_porcentaje = $obj_class->consultaReal(0, 1, $fecha, $cliente);
                                            $pc = 0;
                                            for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                                           ?>
                                          <?php
                                          $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                                          ?>

                                        <?php }
                                        if ($pc == 0) {
                                            echo "0.00%";
                                        }else {
                                            echo round($pc/count($tabla_porcentaje), 2). "%";
                                        }
                                        ?>

            </span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
          </div>
        </div>
        </div>

        <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-percent"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">TOTAL EFECTIVIDAD DIRECTOS</span>
            <span class="info-box-number"><?php
                                            $tabla_porcentaje = $obj_class->consultaReal(0, 2, $fecha, $cliente);
                                            $pc = 0;
                                            for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                                           ?>
                                          <?php
                                          $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                                          ?>

                                          <?php }
                                          if ($pc == 0) {
                                              echo "0.00%";
                                          }else {
                                              echo round($pc/count($tabla_porcentaje), 2). "%";
                                          }
                                          ?>

            </span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
          </div>
        </div>
        </div>


        <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-blue">
          <span class="info-box-icon"><i class="fa fa-user"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">ENCUESTAS PARA CLIENTES HABILITADOS</span>
            <span class="info-box-number"><?php
                                                  echo $widgetsClientesHabDir[0]["HABILITADO"];
                                           ?></span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
          </div>
        </div>
        </div>

        <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-blue">
          <span class="info-box-icon"><i class="fa fa-user"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">ENCUESTAS PARA CLIENTES DIRECTOS</span>
            <span class="info-box-number"><?php
                                                  echo $widgetsClientesHabDir[0]["DIRECTO"];
                                           ?></span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
          </div>
        </div>
        </div>

        <div class="col-lg-3 col-xs-6">
        <div class="info-box">
          <span class="info-box-icon"><i class="fa fa-check-square"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">TOTAL ENCUESTAS</span>
            <span class="info-box-number">
            <?php
                        for ($i=0; $i < count($widgetsClientes); $i++) {
                          echo $widgetsClientes[$i]["RESPONDIDAS"] + $widgetsClientes[$i]["N_RESPONDIDAS"];
                        }
            ?></span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
          </div>
        </div>
        </div>

        <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-green">
          <span class="info-box-icon"><i class="fa fa-check-square"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">ENCUESTAS CONTESTADAS</span>
            <span class="info-box-number">
            <?php
                        for ($i=0; $i < count($widgetsClientes); $i++) {
                          echo $widgetsClientes[$i]["RESPONDIDAS"];
                        }
            ?></span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
          </div>
        </div>
        </div>

        <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-times"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">ENCUESTAS NO CONTESTADAS</span>
            <span class="info-box-number">
            <?php
                        for ($i=0; $i < count($widgetsClientes); $i++) {
                          echo $widgetsClientes[$i]["N_RESPONDIDAS"];
                        }
            ?></span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Fecha: <br> <?=$fecha?></span>
          </div>
        </div>
        </div>

      </div>
      <!-- /.row -->
      </section>

    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

<!-- ############################grafica hishcharts   ############################# -->
<section>
  <div class="row">

      <div class="col-md-12">
        <?php if ($plaza == 'ALL'){ ?>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> Porcentaje Encuestas Respondidas</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar2" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction(1)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"> Porcentaje Efectividad</i> </h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
              <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
              <a href="encuestas_realizadas.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
              <?php } ?>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body" ><!--box-body-->

              <!-- FILTRAR POR fecha -->
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
                <input type="text" class="form-control pull-right" name="fil_fecha" enabled>
                <!--<span class="input-group-addon" style="visibility: hidden"> <input type="checkbox" name="fil_check"  checked style="visibility: hidden"<?php /*if( $fil_check == 'on' ){ echo "checked";}*/ ?> > </span>-->
              </div>
              <!-- FILTRAR POR PLAZA -->
              <div class="input-group"  style="display:none">
                <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
                <select class="form-control select2" id="fil_plaza" style="width: 100%;">
                  <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
                  <?php
                  $select_plaza = $obj_class->filtros(1,$departamento);;
                  for ($i=0; $i <count($select_plaza) ; $i++) { ?>
                    <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
                  <?php } ?>
                </select>
              </div>
              <!-- FILTRAR POR CONTRATO -->
              <div class="input-group"  style="display:none">
                <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Almacen:</span>
                <select class="form-control select2" style="width: 100%;" id="nomAlm">
                  <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
                  <?php
                  $departamento = $_GET["plaza"];
                  $selectAlmacen = $obj_class->filtros(4,$departamento);
                  for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
                    <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
                  <?php } ?>
                </select>
              </div>
              <!-- FILTRAR POR DEPTO -->
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-folder"></i> Cliente:</span>
                <select class="form-control select2" style="width: 100%;" id="fil_cliente">
                  <option value="ALL" <?php if($cliente == 'ALL'){echo "selected";} ?>>ALL</option>
                  <?php
                  $departamento = $_GET["almacen"];
                  $select_ctl = $obj_class->filtros(5,$departamento);
                  for ($i=0; $i <count($consulta_clientes) ; $i++) { ?>
                    <option value="<?= $consulta_clientes[$i]["IID_NUM_CLIENTE"] ?>" <?php if($consulta_clientes[$i]["IID_NUM_CLIENTE"] == $cliente){echo "selected";} ?> ><?= $consulta_clientes[$i]["V_RAZON_SOCIAL"] ?></option>
                  <?php } ?>
                </select>
              </div>
              <!-- FILTRAR POR AREA -->
              <div class="input-group">
                <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil"><i class="fa fa-check"></i> Filtrar</button> </span>
              </div>

            </div><!--/.box-body-->
          </div>
        </div>
      </div>

      <!--*###############tabla respondidas###############*-->
      <div id="myDIV" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">ESTATUS ENCUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $tablaDetalleResp = $obj_class ->detalleRespondidas($fecha, $cliente);
                for ($i=0; $i <count($tablaDetalleResp) ; $i++) {
                  if ($tablaDetalleResp[$i]["ESTATUS"] == "CONTESTADA") {
                    $color_rgb = "#28FA07";
                  }else {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $tablaDetalleResp[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $tablaDetalleResp[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $tablaDetalleResp[$i]["ESTATUS"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-md-12">
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> ATENCIÓN COMERCIAL</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar3" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction2(1)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> PAGO IMPUESTOS</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar4" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction2(2)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> ATENCIÓN OPERATIVA A LA ENTRADA DE SU MERCANCÍA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar5" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction2(3)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
      </div>

      <div id="myDIV2" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo2" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_PreguntaTabla_Det(1, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "EXCELENTE") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "MALO") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div id="myDIV3" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo3" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_PreguntaTabla_Det(2, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "EXCELENTE") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "MALO") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div id="myDIV4" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo4" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_PreguntaTabla_Det(3, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "EXCELENTE") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "MALO") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-md-12">
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> ATENCIÓN OPERATIVA A LA SALIDA DE SU MERCANCÍA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar6" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction3(1)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> CONTROL, ORGANIZACIÓN Y REPORTE DE INVENTARIOS</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar7" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction3(2)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> FACTURACIÓN, COBRANZA Y ACLARACIÓN DE FACTURAS</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar8" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction3(3)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
      </div>

      <div id="myDIV5" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo5" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_PreguntaTabla_Det(4, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "EXCELENTE") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "MALO") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div id="myDIV6" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo6" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_PreguntaTabla_Det(5, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "EXCELENTE") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "MALO") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div id="myDIV7" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo7" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_PreguntaTabla_Det(6, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "EXCELENTE") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "MALO") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-md-12">
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> ACTUALMENTE TIENE ALGUNA INCORFORMIDAD</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar9" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction4(1)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> ATENCIÓN OPERATIVA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar12" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction4(2)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> EMISIÓN Y LIBERACIÓN DE CERTIFICADOS</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar13" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction4(3)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
      </div>

      <div id="myDIV8" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo8" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_Pregunta2Tabla_Det(1, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "NO") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "SI") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div id="myDIV9" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo9" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_PreguntaTabla_Det(7, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "EXCELENTE") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "MALO") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div id="myDIV10" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo10" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_PreguntaTabla_Det(8, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "EXCELENTE") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "MALO") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-md-12">
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> RECOMENDARIA LOS SERVICIOS ARGO</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar10" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction5(1)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> HAS VISITADO NUESTRO SITIO WEB</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="graf_bar11" class="col-md-12" style="height:380px;"></div>
              <button onclick="myFunction5(2)"class="btn btn-primary btn-xs pull-right"><i class="fa fa-check"></i>Detalles</button>
            </div>
          </div>
        </div>
      </div>

      <div id="myDIV11" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo11" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_Pregunta2Tabla_Det(2, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "SI") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "NO") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div id="myDIV12" style="display:none;" class="col-md-12">
        <div class="table-responsive">
          <table id="tabla_activo12" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#237BC7"><font color="black">ID</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#237BC7"><font color="black">RESPUESTA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $comercial = $obj_class->grafica_Pregunta2Tabla_Det(3, $cliente, $fecha);
                for ($i=0; $i <count($comercial) ; $i++) {
                  if ($comercial[$i]["RESPUESTA"] == "SI") {
                    $color_rgb = "#28FA07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "BUENO") {
                    $color_rgb = "#FA6707";
                  }elseif ($comercial[$i]["RESPUESTA"] == "REGULAR") {
                    $color_rgb = "#FAEF07";
                  }elseif ($comercial[$i]["RESPUESTA"] == "NO") {
                    $color_rgb = "#FA0707";
                  }
                  ?>
              <tr>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["ID_CLIENTE"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["V_RAZON_SOCIAL"] ?></td>
                <td style="background-color:<?= $color_rgb ?>"><?= $comercial[$i]["RESPUESTA"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-md-9">
        <div id="graf_perM" style="display:none;" ></div>

        <br>

          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> Tabla de Encuestas</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
          <div class="table-responsive">
          <table id="tabla_baja" class="display nowrap" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th></th>
                <th class="small" bgcolor="#AD164D"><font color="black">ID</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">CLIENTE</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">USUARIO</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">PUESTO</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">FECHA PROGRAMADA</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">FECHA REALIZADA</font></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i <count($tablaEncuestas) ; $i++) { ?>
              <tr data-child-value="<?= $tablaEncuestas[$i]["CONSECUTIVO_ENC"]  ?>">
                <td class="details-control"></td>
                <td><?= $tablaEncuestas[$i]["CONSECUTIVO_ENC"] ?></td>
                <td><?= $tablaEncuestas[$i]["V_RAZON_SOCIAL"] ?></td>
                <td><?= $tablaEncuestas[$i]["USUARIO"] ?></td>
                <td><?= $tablaEncuestas[$i]["PUESTO"] ?></td>
                <td><?= $tablaEncuestas[$i]["FECHA_PROG"] ?></td>
                <td><?= $tablaEncuestas[$i]["FECHA"] ?></td>
                <td></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
          <!--<input type="text" name="f1t1" id="f1t1" value="0">-->
        </div>
      </div>
      </div>
    </div>

        <!-- PLAZA -->
        <div class="col-md-9">
          <br>

            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bar-chart"></i> EFECTIVIDAD POR PLAZAS</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
              </div>
              <div class="box-body">
            <div class="table-responsive">
            <table id="tabla_baja2" class="display nowrap" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#AD164D"><font color="black">TIPO</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="black">CORDOBA</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="black">MEXICO</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="black">GOLFO</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="black">PENINSULA</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="black">PUEBLA</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="black">BAJIO</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="black">OCCIDENTE</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="black">NORESTE</font></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>HABILITADO</td>
                  <?php
                    $tabla_porcentaje = $obj_class->consultaReal(3, 1, $fecha, $cliente);
                    $pc = 0;
                    for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                  ?>
                      <?php
                            $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                      ?>

                  <?php } ?>
                      <td><?php
                          if ($pc == 0) {
                            echo "0.00%";
                          }else {
                            echo round($pc/count($tabla_porcentaje))."%";
                          }
                      ?>
                     </td>

                      <?php
                        $tabla_porcentaje = $obj_class->consultaReal(4, 1, $fecha, $cliente);
                        $pc = 0;
                        for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                      ?>
                          <?php
                                $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                          ?>

                      <?php } ?>
                          <td>
                            <?php
                                if ($pc == 0) {
                                  echo "0.00%";
                                }else {
                                  echo round($pc/count($tabla_porcentaje))."%";
                                }
                            ?>
                          </td>

                   <?php
                          $tabla_porcentaje = $obj_class->consultaReal(5, 1, $fecha, $cliente);
                          $pc = 0;
                          for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                  ?>
                  <?php
                          $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                  ?>

                  <?php } ?>
                         <td>
                           <?php
                               if ($pc == 0) {
                                 echo "0.00%";
                               }else {
                                 echo round($pc/count($tabla_porcentaje))."%";
                               }
                           ?>
                         </td>

                  <?php
                     $tabla_porcentaje = $obj_class->consultaReal(6, 1, $fecha, $cliente);
                     $pc = 0;
                   for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                  ?>
                             <?php
                                   $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                             ?>

                         <?php } ?>
                             <td>
                               <?php
                                   if ($pc == 0) {
                                     echo "0.00%";
                                   }else {
                                     echo round($pc/count($tabla_porcentaje))."%";
                                   }
                               ?>
                             </td>

                  <?php
                      $tabla_porcentaje = $obj_class->consultaReal(7, 1, $fecha, $cliente);
                      $pc = 0;
                  for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                  ?>
                            <?php
                                  $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                            ?>

                            <?php } ?>
                              <td><?php
                                  if ($pc == 0) {
                                    echo "0.00%";
                                  }else {
                                    echo round($pc/count($tabla_porcentaje))."%";
                                  }
                              ?>
                              </td>


                  <?php
                        $tabla_porcentaje = $obj_class->consultaReal(8, 1, $fecha, $cliente);
                        $pc = 0;
                    for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                  ?>
                            <?php
                                $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                            ?>

                             <?php } ?>
                                          <td>
                                            <?php
                                                if ($pc == 0) {
                                                  echo "0.00%";
                                                }else {
                                                  echo round($pc/count($tabla_porcentaje))."%";
                                                }
                                            ?>
                                          </td>

                <?php
                        $tabla_porcentaje = $obj_class->consultaReal(17, 1, $fecha, $cliente);
                        $pc = 0;
                    for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                ?>
                            <?php
                                  $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                            ?>

                           <?php } ?>
                                           <td>
                                             <?php
                                                 if ($pc == 0) {
                                                   echo "0.00%";
                                                 }else {
                                                   echo round($pc/count($tabla_porcentaje))."%";
                                                 }
                                             ?>
                                           </td>

                 <?php
                      $tabla_porcentaje = $obj_class->consultaReal(18, 1, $fecha, $cliente);
                      $pc = 0;
                     for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                 ?>
                             <?php
                                     $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                             ?>

                            <?php } ?>
                <td>
                  <?php
                      if ($pc == 0) {
                        echo "0.00%";
                      }else {
                        echo round($pc/count($tabla_porcentaje))."%";
                      }
                  ?>
                </td>
                </tr>
                <tr>
                  <td>DIRECTO</td>
                  <?php
                    $tabla_porcentaje = $obj_class->consultaReal(3, 2, $fecha, $cliente);
                    $pc = 0;
                    for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                  ?>
                      <?php
                            $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                      ?>

                  <?php } ?>
                      <td>
                        <?php
                            if ($pc == 0) {
                              echo "0.00%";
                            }else {
                              echo round($pc/count($tabla_porcentaje))."%";
                            }
                        ?>
                      </td>

                      <?php
                        $tabla_porcentaje = $obj_class->consultaReal(4, 2, $fecha, $cliente);
                        $pc = 0;
                        #echo count($tabla_porcentaje);
                        for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                      ?>
                          <?php
                                $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                          ?>

                      <?php } ?>
                          <td>
                            <?php
                                if ($pc == 0) {
                                  echo "0.00%";
                                }else {
                                  echo round($pc/count($tabla_porcentaje))."%";
                                }
                            ?>
                          </td>

                   <?php
                          $tabla_porcentaje = $obj_class->consultaReal(5, 2, $fecha, $cliente);
                          $pc = 0;
                          for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                  ?>
                  <?php
                          $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                  ?>

                  <?php } ?>
                         <td>
                           <?php
                               if ($pc == 0) {
                                 echo "0.00%";
                               }else {
                                 echo round($pc/count($tabla_porcentaje))."%";
                               }
                           ?>
                         </td>

                  <?php
                     $tabla_porcentaje = $obj_class->consultaReal(6, 2, $fecha, $cliente);
                     $pc = 0;
                   for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                  ?>
                             <?php
                                   $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                             ?>

                         <?php } ?>
                             <td>
                               <?php
                                   if ($pc == 0) {
                                     echo "0.00%";
                                   }else {
                                     echo round($pc/count($tabla_porcentaje))."%";
                                   }
                               ?>
                             </td>

                  <?php
                      $tabla_porcentaje = $obj_class->consultaReal(7, 2, $fecha, $cliente);
                      $pc = 0;
                  for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                  ?>
                            <?php
                                  $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                            ?>

                            <?php } ?>
                              <td>
                                <?php
                                    if ($pc == 0) {
                                      echo "0.00%";
                                    }else {
                                      echo round($pc/count($tabla_porcentaje))."%";
                                    }
                                ?>
                              </td>


                  <?php
                        $tabla_porcentaje = $obj_class->consultaReal(8, 2, $fecha, $cliente);
                        $pc = 0;
                    for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                  ?>
                            <?php
                                $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                            ?>

                             <?php } ?>
                                          <td>
                                            <?php
                                                if ($pc == 0) {
                                                  echo "0.00%";
                                                }else {
                                                  echo round($pc/count($tabla_porcentaje))."%";
                                                }
                                            ?>
                                          </td>

                <?php
                        $tabla_porcentaje = $obj_class->consultaReal(17, 2, $fecha, $cliente);
                        $pc = 0;
                    for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                ?>
                            <?php
                                  $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                            ?>

                           <?php } ?>
                                           <td>
                                             <?php
                                                 if ($pc == 0) {
                                                   echo "0.00%";
                                                 }else {
                                                   echo round($pc/count($tabla_porcentaje))."%";
                                                 }
                                             ?>
                                           </td>

                 <?php
                      $tabla_porcentaje = $obj_class->consultaReal(18, 2, $fecha, $cliente);
                      $pc = 0;
                     for ($i=0; $i <count($tabla_porcentaje) ; $i++) {
                 ?>
                             <?php
                                     $pc = $pc + $tabla_porcentaje[$i]["PORCENTAJE"];
                             ?>

                            <?php } ?>
                <td>
                  <?php
                      if ($pc == 0) {
                        echo "0.00%";
                      }else {
                        echo round($pc/count($tabla_porcentaje))."%";
                      }
                  ?>
                </td>
                </tr>
              </tbody>
            </table>
            <!--<input type="text" name="f1t1" id="f1t1" value="0">-->
          </div>
        </div>
        </div>
      </div>


        <!--fin tabla detalles-->
        <?php } ?>
        <?php if ($plaza != 'ALL'){ ?>
        <div class="row">

          <div class="col-md-8">
            <div id="grafPerAlm"></div>
          </div>

          <?php if ($plaza != 'ALL'){ ?>
          <div class="col-md-4">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Widgets</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
              </div>
              <div class="box-body"><!--box-body-->

                <!-- WIDGETS #1 -->
                <div class="info-box bg-blue">
                  <span class="info-box-icon"><i class="fa fa-users"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Personal Activo <?=$plaza?></span>
                    <span class="info-box-number"><?php echo $widgets[0]["ACTIVO"]; ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <?php
                    if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                      echo '<span class="progress-description">Fecha de consulta: '.$fecha.'</span>';
                    }else{
                      $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(SYSDATE, 'MM/DD/YYYY HH24:MI:SS') NOW FROM DUAL");
                       echo '<span class="progress-description">Fecha de consulta: '.$mesConsultaBaja[0]["NOW"].'</span>';
                    }
                    ?>
                  </div>
                </div>
                <!-- WIDGETS #2 -->
                <div class="info-box bg-maroon">
                  <span class="info-box-icon"><i class="fa fa-user-times"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Personal Baja<?=$plaza?></span>
                    <span class="info-box-number"><?php echo $widgets[0]["BAJA"]; ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <?php
                    if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                      echo '<span class="progress-description">Fecha de consulta: '.$fecha.'</span>';
                    }else{
                      $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -1), 'DD/MM/YYYY') mes2 FROM DUAL");
                       echo '<span class="progress-description">Fecha de consulta: '.$mesConsultaBaja[0]["MES1"].'-'.$mesConsultaBaja[0]["MES2"].'</span>';
                    }
                    ?>
                  </div>
                </div>
                <!-- ./WIDGETS -->
                <div class="info-box bg-maroon">
                  <span class="info-box-icon"><i class="fa fa-user-times"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Personal HABILITADO Baja<?=$plaza?></span>
                    <span class="info-box-number"><?php echo $widgets[0]["BAJA_HABILITADO"]; ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <?php
                    if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                      echo '<span class="progress-description">Fecha de consulta: '.$fecha.'</span>';
                    }else{
                      $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -1), 'DD/MM/YYYY') mes2 FROM DUAL");
                       echo '<span class="progress-description">Fecha de consulta: '.$mesConsultaBaja[0]["MES1"].'-'.$mesConsultaBaja[0]["MES2"].'</span>';
                    }
                    ?>
                  </div>
                </div>

              </div><!--/.box-body-->
            </div>
          </div>
          <?php } ?>
          <?php if ($plaza != 'ALL') { ?>
            <div id="graf_pie"></div>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
    </div>

    <?php //if ($plaza != 'ALL'){ ?>
    <div class="col-md-3"  style="display:none;">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
          <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="encuestas_realizadas.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
          <?php } ?>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <!-- FILTRAR POR fecha -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
            <input type="text" class="form-control pull-right" name="fil_fecha" enabled>
            <!--<span class="input-group-addon" style="visibility: hidden"> <input type="checkbox" name="fil_check"  checked style="visibility: hidden"<?php /*if( $fil_check == 'on' ){ echo "checked";}*/ ?> > </span>-->
          </div>
          <!-- FILTRAR POR PLAZA -->
          <div class="input-group"  style="display:none">
            <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
            <select class="form-control select2" id="fil_plaza" style="width: 100%;">
              <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $select_plaza = $obj_class->filtros(1,$departamento);;
              for ($i=0; $i <count($select_plaza) ; $i++) { ?>
                <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
              <?php } ?>
            </select>
          </div>
          <!-- FILTRAR POR CONTRATO -->
          <div class="input-group"  style="display:none">
            <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Almacen:</span>
            <select class="form-control select2" style="width: 100%;" id="nomAlm">
              <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $departamento = $_GET["plaza"];
              $selectAlmacen = $obj_class->filtros(4,$departamento);
              for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
                <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
              <?php } ?>
            </select>
          </div>
          <!-- FILTRAR POR DEPTO -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-folder"></i> Cliente:</span>
            <select class="form-control select2" style="width: 100%;" id="fil_cliente">
              <option value="ALL" <?php if($cliente == 'ALL'){echo "selected";} ?>>ALL</option>
              <?php
              $departamento = $_GET["almacen"];
              $select_ctl = $obj_class->filtros(5,$departamento);
              for ($i=0; $i <count($consulta_clientes) ; $i++) { ?>
                <option value="<?= $consulta_clientes[$i]["IID_NUM_CLIENTE"] ?>" <?php if($consulta_clientes[$i]["IID_NUM_CLIENTE"] == $cliente){echo "selected";} ?> ><?= $consulta_clientes[$i]["V_RAZON_SOCIAL"] ?></option>
              <?php } ?>
            </select>
          </div>
          <!-- FILTRAR POR AREA -->
          <div class="input-group">
            <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil"><i class="fa fa-check"></i> Filtrar</button> </span>
          </div>

        </div><!--/.box-body-->
      </div>
    </div>
    <?php //} ?>
<!-- WIDGETS -->
  </div>
</section>
<!-- ############################ ./SECCION GRAFICA Y WIDGETS ############################# -->


    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
$('#myTab a').click(function(e) {
  e.preventDefault();
  $(this).tab('show');
});

// store the currently selected tab in the hash value
$("ul.nav-pills > li > a").on("shown.bs.tab", function(e) {
  var id = $(e.target).attr("href").substr(1);
  window.location.hash = id;
});

// on load of the page: switch to the currently selected tab
var hash = window.location.hash;
$('#myTab a[href="' + hash + '"]').tab('show');
</script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
//ACTIVA FILTRO POR FECHA
  $('input[name="fil_fecha"]').attr("disabled", false);
<?php
if ( $fil_check == 'ALL' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){ ?>
  $('input[name="fil_fecha"]').attr("disabled", false);
<?php } ?>
$('input[name="fil_check"]').on("click", function (){

  if ($('input[name="fil_check"]').is(':checked')) {
    $('input[name="fil_fecha"]').attr("disabled", false);
  }else{
    $('input[name="fil_fecha"]').attr("disabled", false);
  }

});



//BOTON FILTRAR
$(".btn_fil").on("click", function(){

  fil_fecha = $('input[name="fil_fecha"]').val();
  fil_plaza = $('#fil_plaza').val();
  almacen = $('#nomAlm').val();
  cliente = $('#fil_cliente').val();
  fil_check = 'on';

  //Fill habilitados
  fil_habilitado = 'off';




  url = '?cliente='+cliente+'&fecha='+fil_fecha;


  location.href = url;

});

$('.select2').select2()
</script>
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<script src="../plugins/flot/jquery.flot.min.js"></script>
<!-- FLOT PIE CHARTS 3D -->
<script src="../plugins/flot/jquery.flot.pie3d.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="../plugins/flot/jquery.flot.resize.min.js"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="../plugins/flot/jquery.flot.pie_old.js"></script>
<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
<script src="../plugins/flot/jquery.flot.categories.js"></script>
<!-- FLOT ORDER BARS  -->
<script src="../plugins/flot/jquery.flot.orderBars.js"></script>
<!-- FLOT  bar charts click text -->
<script src="../plugins/flot/jquery.flot.tooltip.js"></script>

<script type="text/javascript">
function myFunction(ventana) {
    var x1 = document.getElementById("myDIV");
    if (ventana == 1){
      if (x1.style.display === "none") {
          x1.style.display = "block";
      } else {
          x1.style.display = "none";
      }
    }
}
</script>

<script type="text/javascript">
function myFunction2(ventana) {
    var x1 = document.getElementById("myDIV2");
    var x2 = document.getElementById("myDIV3");
    var x3 = document.getElementById("myDIV4");
    if (ventana == 1){
      if (x1.style.display === "none") {
          x1.style.display = "block";
          x2.style.display = "none";
          x3.style.display = "none";
      } else {
          x1.style.display = "none";
      }
    }else if (ventana == 2) {
      if (x2.style.display === "none") {
          x1.style.display = "none";
          x2.style.display = "block";
          x3.style.display = "none";
      } else {
          x2.style.display = "none";
      }
    }else if (ventana == 3) {
      if (x3.style.display === "none") {
          x1.style.display = "none";
          x2.style.display = "none";
          x3.style.display = "block";
      } else {
          x3.style.display = "none";
      }
    }
}

function myFunction3(ventana) {
    var x1 = document.getElementById("myDIV5");
    var x2 = document.getElementById("myDIV6");
    var x3 = document.getElementById("myDIV7");
    if (ventana == 1){
      if (x1.style.display === "none") {
          x1.style.display = "block";
          x2.style.display = "none";
          x3.style.display = "none";
      } else {
          x1.style.display = "none";
      }
    }else if (ventana == 2) {
      if (x2.style.display === "none") {
          x1.style.display = "none";
          x2.style.display = "block";
          x3.style.display = "none";
      } else {
          x2.style.display = "none";
      }
    }else if (ventana == 3) {
      if (x3.style.display === "none") {
          x1.style.display = "none";
          x2.style.display = "none";
          x3.style.display = "block";
      } else {
          x3.style.display = "none";
      }
    }
}

function myFunction4(ventana) {
    var x1 = document.getElementById("myDIV8");
    var x2 = document.getElementById("myDIV9");
    var x3 = document.getElementById("myDIV10");
    if (ventana == 1){
      if (x1.style.display === "none") {
          x1.style.display = "block";
          x2.style.display = "none";
          x3.style.display = "none";
      } else {
          x1.style.display = "none";
      }
    }else if (ventana == 2) {
      if (x2.style.display === "none") {
          x1.style.display = "none";
          x2.style.display = "block";
          x3.style.display = "none";
      } else {
          x2.style.display = "none";
      }
    }else if (ventana == 3) {
      if (x3.style.display === "none") {
          x1.style.display = "none";
          x2.style.display = "none";
          x3.style.display = "block";
      } else {
          x3.style.display = "none";
      }
    }
}

function myFunction5(ventana) {
    var x1 = document.getElementById("myDIV11");
    var x2 = document.getElementById("myDIV12");
    if (ventana == 1){
      if (x1.style.display === "none") {
          x1.style.display = "block";
          x2.style.display = "none";
      } else {
          x1.style.display = "none";
      }
    }else if (ventana == 2) {
      if (x2.style.display === "none") {
          x1.style.display = "none";
          x2.style.display = "block";
      } else {
          x2.style.display = "none";
      }
    }
}
</script>
<?php
/* ------------------- INICIA OPCIONES PARA LA GRÁFICA DE DONA ------------------- */
$donut_series = "pie3d: {
                  stroke: { /*define linea separadora*/
                    width: 0,
                    /*color: '#222D32'*/
                  } ,
                  show: true,
                  radius: .80, /*radius: 1,  tamño radio del circulo*/
                  tilt: .9,/*rotacion de angulo */
                  depth: 10,/*grosor de sombra 3d*/
                  innerRadius: 40,/*radio dona o pastel*/
                  label: {
                    show: true,
                    radius:2/3,/*0.90 posicion del label con data*/
                    formatter: labelFormatter,
                  },
                }";

$donut_series2 = "pie3d: {
                                  stroke: { /*define linea separadora*/
                                    width: 2,
                                    /*color: '#222D32'*/
                                  } ,
                                  show: true,
                                  radius: .80, /*radius: 1,  tamño radio del circulo*/
                                  tilt: .9,/*rotacion de angulo */
                                  depth: 10,/*grosor de sombra 3d*/
                                  innerRadius: 70,/*radio dona o pastel*/
                                  label: {
                                    show: true,
                                    radius:2/3,/*0.90 posicion del label con data*/
                                    formatter: labelFormatter,
                                  },
                                }";

$donut_grid =  "hoverable: false,
                clickable: false,
                verticalLines: false,
                horizontalLines: false,";
$donut_legend = "/*labelBoxBorderColor: 'none'*/
                show: true "; //-- PONE LOS LABEL DEL ALDO IZQUIERDO  //

$donut_content = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> Toneladas = %y.0 </b>  </div>' ;

$donut_tooltip = "show: false,
      content: '".$donut_content."',
      defaultTheme: true ";
 ?>
<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $positivasReal = 0;
        $negativasReal = 0;
        for ($i=0; $i <count($grafica) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $positivasReal = $positivasReal + $grafica[$i]["PROMEDIO_POSITIVA"];
          $negativasReal = $negativasReal + $grafica[$i]["PROMEDIO_NEGATIVO"];
        }
          $positivasReal = round($positivasReal/count($grafica), 2);
          $negativasReal = round($negativasReal/COUNT($grafica), 2);

          $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$positivasReal.'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$positivasReal.'"  name="co_plaza" class="btn btn-link btn-xs" disabled>EFECTIVIDAD</button></form>' ;
          $label2 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$negativasReal.'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$negativasReal.'"  name="co_plaza" class="btn btn-link btn-xs" disabled>INEFECTIVIDAD</button></form> ' ;

          $data = $positivasReal;
          $data2 = $negativasReal;
          $color = "#FF0000";
          $color2 = "#00FF00";
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color2 ?>'},
        {label: '<?= $label2 ?>', data: <?=$data2?> , color: '<?= $color ?>'}

    ];

    $.plot("#graf_bar", donutData_pros_general, {
      series: { <?= $donut_series2 ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>


<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
          #echo $grafica[$i]["TIPO_RES"];
          $contestadas = "CONTESTADAS";
          $no_contestadas ="NO CONTESTADAS";
          //$plaza_corta = str_word_count($plaza, 1);

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //

          $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$widgetsClientes[0]["N_RESPONDIDAS"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$widgetsClientes[0]["N_RESPONDIDAS"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>NO RESPONDIDAS</button></form>' ;
          $label2 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$widgetsClientes[0]["RESPONDIDAS"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$widgetsClientes[0]["RESPONDIDAS"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>RESPONDIDAS</button></form>' ;
          //$label3 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica[$i]["TIPO_RES"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica[$i]["TIPO_RES"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$grafica[$i]["TIPO_RES"].'</button></form>' ;

          $color ='#FAEF07';
          $color2 = '#1FBC0C';

          $data = round($widgetsClientes[0]["N_RESPONDIDAS"], 2);
          $data2 = round($widgetsClientes[0]["RESPONDIDAS"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},
        {label: '<?= $label2 ?>', data: <?=$data2?> , color: '<?= $color2 ?>'}

    ];

    $.plot("#graf_bar2", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>




<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 1;
        $graf_Pregunta = $obj_class->grafica_Pregunta($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'BUENO':
                $color = '#FA6707';
                break;
              case 'MALO':
                $color = '#BC0C0C';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar3", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>

<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 2;
        $graf_Pregunta = $obj_class->grafica_Pregunta($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'BUENO':
                $color = '#FA6707';
                break;
              case 'MALO':
                $color = '#BC0C0C';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar4", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>

<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 3;
        $graf_Pregunta = $obj_class->grafica_Pregunta($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'BUENO':
                $color = '#FA6707';
                break;
              case 'MALO':
                $color = '#BC0C0C';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar5", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>

<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 4;
        $graf_Pregunta = $obj_class->grafica_Pregunta($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'BUENO':
                $color = '#FA6707';
                break;
              case 'MALO':
                $color = '#BC0C0C';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar6", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>

<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 5;
        $graf_Pregunta = $obj_class->grafica_Pregunta($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'BUENO':
                $color = '#FA6707';
                break;
              case 'MALO':
                $color = '#BC0C0C';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar7", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IdZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>

<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 6;
        $graf_Pregunta = $obj_class->grafica_Pregunta($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'BUENO':
                $color = '#FA6707';
                break;
              case 'MALO':
                $color = '#BC0C0C';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar8", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>


<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 1;
        $graf_Pregunta = $obj_class->grafica_Pregunta2($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'SI':
                $color = '#BC0C0C';
                break;
              case 'NO':
                $color = '#2FFA07';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar9", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>

<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 2;
        $graf_Pregunta = $obj_class->grafica_Pregunta2($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'NO':
                $color = '#BC0C0C';
                break;
              case 'SI':
                $color = '#2FFA07';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar10", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>

<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 3;
        $graf_Pregunta = $obj_class->grafica_Pregunta2($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'NO':
                $color = '#BC0C0C';
                break;
              case 'SI':
                $color = '#2FFA07';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar11", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>

<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 7;
        $graf_Pregunta = $obj_class->grafica_Pregunta($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'NO':
                $color = '#BC0C0C';
                break;
              case 'SI':
                $color = '#2FFA07';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case 'BUENO':
                  $color = '#FF8000';
                  break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar12", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>

<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $tipo = 8;
        $graf_Pregunta = $obj_class->grafica_Pregunta($tipo, $cliente, $fecha);
        for ($i=0; $i <count($graf_Pregunta) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          $plaza = $graf_Pregunta[$i]["RESPUESTA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$graf_Pregunta[$i]["RESPUESTA"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$graf_Pregunta[$i]["RESPUESTA"].'</button></form>' ;
                break;
            }
            switch ($graf_Pregunta[$i]["RESPUESTA"]) {
              case 'REGULAR':
                $color ='#FAEF07';
                break;
              case 'NO':
                $color = '#BC0C0C';
                break;
              case 'SI':
                $color = '#2FFA07';
                break;
              case 'EXCELENTE':
                $color = '#2FFA07';
                break;
              case 'BUENO':
                $color = '#FF8000';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($graf_Pregunta[$i]["CANTIDAD_RESPUESTA"], 2);
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar13", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
</script>

<script type= "text/javascript">
$(function(){
  Highcharts.setOptions({
    lang:{
      thousandsSep: ','
    }
  });
  var categories = [
    <?php
    for ($i=0; $i < count($graficaMensual) ; $i++) {
      echo "'".$graficaMensual[$i]["MES"]."',";
    }
    ?>
  ];
  var data1 = [
    <?php
    for ($i=0; $i < count($graficaMensual); $i++) {
      $año_actual = date("Y");
      $anoComparar = substr($fecha, 6,4);
      $mes_Comparar = substr($fecha, 14, 2);

          if($graficaMensual[$i]["POSITIVAS"] == 0){
            $porcentaje_total_pos = 0.00;
          }else {
              $total = $graficaMensual[$i]["POSITIVAS"] + $graficaMensual[$i]["NEGATIVAS"] + $graficaMensual[$i]["MEJORAS"];
              $porcentaje_total_pos = number_format(($graficaMensual[$i]["POSITIVAS"]/$total)*100, 2);

          }
          echo $porcentaje_total_pos." ,";
    }
     ?>
  ];
  var data2 = [
    <?php
    for ($i=0; $i < count($graficaMensual); $i++) {
      $mes_Comparar = substr($fecha, 14, 2);

        if($graficaMensual[$i]["NEGATIVAS"] == 0){
          $porcentaje_total_neg = 0.00;
        }else {
            $total = $graficaMensual[$i]["POSITIVAS"] + $graficaMensual[$i]["NEGATIVAS"] +$graficaMensual[$i]["MEJORAS"];
            $porcentaje_total_neg = number_format(($graficaMensual[$i]["NEGATIVAS"]/$total)*100, 2);

        }
        echo $porcentaje_total_neg." ,";


    }
     ?>
  ];

  var data3 = [
    <?php
    for ($i=0; $i < count($graficaMensual); $i++) {
      $mes_Comparar = substr($fecha, 14, 2);

        if($graficaMensual[$i]["NEGATIVAS"] == 0){
          $porcentaje_total_neg = 0.00;
        }else {
            $total = $graficaMensual[$i]["POSITIVAS"] + $graficaMensual[$i]["NEGATIVAS"]+ $graficaMensual[$i]["MEJORAS"];
            $porcentaje_total_neg = number_format(($graficaMensual[$i]["MEJORAS"]/$total)*100, 2);

        }
        echo $porcentaje_total_neg." ,";


    }
     ?>
  ];

  $('#graf_perM').highcharts({
    chart:{
      type:'line'
    },
    title:{
      text:'EFECTIVIDAD POR MES'
    },
    legend:{
      y: -40,
      borderWidth:1,
      backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
      lineWidth:2,
      offset:10,
      tickWidth:1,
      title:{
        text:'PORCENTAJE'
      },
      labels:{
        formatter:function(){
          return this.value;
        }
      }
    },
    tooltip:{
      shared:true,
      valueSuffix: ' ',
      useHTML: true,
    },
    lang:{
      printChart:'Imprmir Grafica',
      downloadPNG:'Descargar PNG',
      downloadJPEG:'Descargar JPEG',
      downloadPDF:'Descargar PDF',
      downloadSVG:'Descargar SVG',
      contextButtonTitle: 'Exportar Grafica'
    },
    credits:{
      enabled:false
    },
    colors:['#33FA07', '#C21313', '#FAEB07', '#1C00ff00'],
    //colors:['#1399C2', '#C21313', '#0D6580', '#E61717'],
    plotOptions:{
      series:{
        minPointLength:3
      }
    },
    xAxis:{
      categories:categories,
      labels:{
        formatter: function(){
          url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';

            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle:{
      text:'',
      align:'right',
      x:-10,
    },
    series:[{
      name:'% EFECTIVIDAD',
      data: data1,
    },{
      name:'% INEFECTIVIDAD',
      data: data2,
    },{
      name:'% MEJORAR',
      data: data3,
    }
    ]
  });
});
</script>
<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- DataTables buttons -->
<script src="../plugins/datatables/extensions/buttons_datatable/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables/extensions/buttons_datatable/buttons.html5.min.js"></script>
<!-- DataTables export exel -->
<script src="../plugins/datatables/extensions/buttons_datatable/jszip.min.js"></script>
<!-- DataTables muestra/oculta columna -->
<script src="../plugins/datatables/extensions/buttons_datatable/buttons.colVis.min.js"></script>
<!-- DataTables button print -->
<script src="../plugins/datatables/extensions/buttons_datatable/buttons.print.min.js"></script>
<!-- SELECT DATATBLE -->
<script src="../plugins/datatables/extensions/Select/dataTables.select.min.js"></script>
<!-- RESPONSIVE DATATBLE -->
<script src="../plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo2').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo3').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo4').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo5').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo6').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo7').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo8').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>
<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo9').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo10').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo11').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_activo12').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>

<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_baja').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',

            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            text:'PDF',
            extend : 'pdf',
            collectionLayout: 'fixed two-column',
            footer: true,
            header: true,
            customize: function(doc){
                var table = $('#tabla_baja').dataTable();
                var rowData = table.rows({order: 'applied', search:'applied'}).data();
                var headerLines = 0;

                var newBody = [];
                doc.content[1].table.body.forEach(function(line, i){
                  newBody.push(
                    [line[1], line[2], line[3], line[4]]
                  );
                  if (line[0].style !==  'tablaHeader' && line[0].style !== 'tableFooter') {
                    var data = rowData[i - headerLines];
                    newBody.push(
                      [
                          {text: '** Child data:', style:'defaultStyle'},
                          {text: data.name, style:'defaultStyle'},
                          {text: data.extn, style:'defaultStyle'},
                          {text: '', style:'defaultStyle'},
                      ]
                    );
                  }else {
                    headerLines++;
                  }
                });

                doc.content[1].table.headerRows = 1;
                //doc.content[1].table.widths = [50, 50, 50, 50, 50, 50];
                doc.content[1].table.body = newBody;
                doc.content[1].layout = 'lightHorizontalLines';

                doc.styles = {
                  subheader:{
                    fontSize:10,
                    bold:true,
                    color: 'black'
                  },
                  tableHeader:{
                    bold:true,
                    fontSize:10.5,
                    color:'black'
                  },
                  lastLine:{
                    bold:true,
                    fontSize:11,
                    color:'blue'
                  },
                  defaultStyle:{
                    fontSize:10,
                    color:'black',
                    text:'center'
                  }
                };
            }
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
    $('#tabla_baja').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            console.log(tr.data('child-value'));
            res2 =tr.data('child-value');
            var a = [];
            $.ajax({
              type:'POST',
              url: '../class/Encuestas_Realizadas_Det.php',
              data: "encuesta=" + res2,
              success: function(response){
                  //$("#f1t1").val(res);
                  //console.log(response);
                  res = response;
                  var types = JSON.parse(response);
                  //console.log(types);
                  var table_head;
                  table_head = "<table class='egt'><tr><th>PREGUNTA</th> <th>RESPUESTA NEGATIVA</th><th>RESPUESTA CLIENTE</th><th>COMENTARIO</th><th>VALOR RESPUESTA</th></tr>";
                  var body_table = "";
                  for(x=0; x<types.length; x++) {
                        var pregunta;
                        var resp_sino;
                        var resp_cliente;
                        var tipo_resp;
                        var color_fila;
                        var color_texto;
                        var comentarios;

                        if(types[x].PREGUNTA === null){
                          pregunta = "";
                        }else {
                          pregunta = types[x].PREGUNTA;
                        }


                        if (types[x].RESPUESTA2 === null) {
                          comentarios = "";
                        }else {
                          comentarios = types[x].RESPUESTA2;
                        }

                        if(types[x].RESP_SIONO === null){
                          resp_sino = "";
                        }else {
                          resp_sino = types[x].RESP_SIONO;
                        }

                        if (types[x].RESPUESTA === null) {
                          resp_cliente = "";
                        }else {
                          resp_cliente = types[x].RESPUESTA;
                        }
                        if (types[x].TIPO_RES === null) {
                          tipo_resp = "";
                        }else {
                          tipo_resp = types[x].TIPO_RES;
                        }



                        if (resp_sino === resp_cliente  && types[x].RESP_SIONO !== null ) {
                          color_fila = 'red';
                          color_texto = 'white';
                        }
                        else if (resp_cliente === "BUENO") {
                          color_fila = 'orange';
                          color_texto = 'white';
                        }
                        else if (resp_cliente === "MALO") {
                          color_fila = 'red';
                          color_texto = 'white';
                        }else if (resp_cliente === "EXCELENTE") {
                          color_fila = 'green';
                          color_texto = 'white';
                        }else if (resp_cliente === "REGULAR") {
                          color_fila = 'yellow';
                          color_texto = 'black';
                        }
                        else if (resp_sino === resp_cliente && types[x].RESP_SIONO === null) {
                          color_fila = 'white';
                          color_texto = 'black';
                        }else if (resp_sino !== resp_cliente && types[x].RESP_SIONO !== null) {
                          color_fila = 'green';
                          color_texto = 'white';
                        }else if (resp_sino !== resp_cliente && types[x].RESP_SIONO === null) {
                          color_fila = 'white';
                          color_texto = 'black';
                        }

                        if (pregunta == '¿RECOMENDARÍA LOS SERVICIOS DE ARGO?') {
                            if (types[x].RESPUESTA == 'SI') {
                              color_fila = 'green';
                              color_texto = 'white';
                            }else {
                              color_fila = 'red';
                              color_texto = 'white';
                            }
                        }

                        if (pregunta == '¿HAN VISITADO NUESTRA PÁGINA WEB?') {
                            if (types[x].RESPUESTA == 'SI') {
                              color_fila = 'green';
                              color_texto = 'white';
                            }else {
                              color_fila = 'red';
                              color_texto = 'white';
                            }
                        }

                        //<td></td>
                       body_table = body_table + "<tr style = 'background-color:"+color_fila+"; color: "+color_texto+";'><td>"+pregunta+"</td><td>"+resp_sino+"</td><td>"+resp_cliente+"</td><td>"+comentarios+"</td><td>"+tipo_resp+"</td></tr>"
                      //console.log(types[x].PREGUNTA);
                      //console.log(types[x].RESP_SIONO);
                      //console.log(types[x].RESPUESTA);
                      //console.log(types[x].TIPO_RES);
                  }

                  console.log(table_head + body_table);

                  row.child(table_head + body_table).show();
                  tr.addClass('shown');
              }

            });
            //row.child(format(tr.data('child-value'))).show();

        }
    });
});
</script>


<script type="text/javascript">
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_baja2').DataTable({
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },
          {
            extend: 'collection',
            text: 'Export',
            buttons:[
              'copy',
              'excel',
              'csv',
              'pdf',
              'print',
            ]
          }
        ],

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
});
</script>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
$('input[name="fil_fecha"]').daterangepicker(
  {
  "linkedCalendars": false,
  "showDropdowns": true,
//INICIA CODE OPCION PARA FORMATO EN ESPAÑOL
  "locale": {
  "format": "DD/MM/YYYY",
  "separator": "-",
  "applyLabel": "Aplicar",
  "cancelLabel": "Cancelar",
  "fromLabel": "From",
  "toLabel": "To",
  "customRangeLabel": "Fecha Personalizada",
  "daysOfWeek": ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
  "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agusto","Septiembre","Octubre","Noviembre","Diciembre"],
  "firstDay": 1
  },
//TERMINA CODE OPCION PARA FORMATO EN ESPAÑOL
    ranges: {
      'Hoy': [moment(), moment()],
      'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Los últimos 7 días': [moment().subtract(6, 'days'), moment()],
      'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
      'Este mes': [moment().startOf('month'), moment().endOf('month')],
      'El mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
      'Este Año': [moment().startOf('year'), moment().endOf('year')]
    },

    <?php if( $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){ ?>
      startDate: '<?=substr($fecha,0,10)?>',
      endDate: '<?=substr($fecha,11,10)?>'
    <?php }else{ ?>
      startDate: moment().subtract(29, 'days'),
      endDate: moment()
    <?php } ?>
  },

);
</script>
<!-- Inicia FancyBox JS -->
  <!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript" src="../plugins/fancybox/lib/jquery.mousewheel.pack.js?v=3.1.3"></script>
  <!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="../plugins/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
  <!-- Add Button helper (this is optional) -->
<script type="text/javascript" src="../plugins/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
  <!-- Add Thumbnail helper (this is optional) -->
<script type="text/javascript" src="../plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
<script type="text/javascript">
    $(document).ready(function() {
      /*
       *  Simple image gallery. Uses default settings
       */

      $('.fancybox').fancybox();

      /*
       *  Different effects
       */

      // Change title type, overlay closing speed
      $(".fancybox-effects-a").fancybox({
        helpers: {
          title : {
            type : 'outside'
          },
          overlay : {
            speedOut : 0
          }
        }
      });

      // Disable opening and closing animations, change title type
      $(".fancybox-effects-b").fancybox({
        openEffect  : 'none',
        closeEffect : 'none',

        helpers : {
          title : {
            type : 'over'
          }
        }
      });



    });
  </script>
<!-- Termina FancyBox JS -->
<!-- PACE -->
<script src="../plugins/pace/pace.min.js"></script>
<!-- page script -->
<script type="text/javascript">
  // To make Pace works on Ajax calls
  $(document).ajaxStart(function() { Pace.restart(); });
    $('.ajax').click(function(){
        $.ajax({url: '#', success: function(result){
            $('.ajax-content').html('<hr>Ajax Request Completed !');
        }});
    });
</script>
</html>
<?php conexion::cerrar($conn); ?>
