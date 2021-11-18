<?php
//BY DAS 12/12/2019

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:detalles_granos.php");
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
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
  include "../class/" . $clase . ".php";
}
spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 30);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/detalle_granos.php';
$modelNomina = new NominaPagada();
//SQL ULTIMA FECHA DE CORTE
$fec_corte = $modelNomina->sql(1,null, null);
/*----- GET FECHA -----*/
$fecha = $fec_corte[0]["MES1"]."-".$fec_corte[0]["MES2"];
if( isset($_GET["fecha"]) ){
  if ( $modelNomina->validateDate(substr($_GET["fecha"],0,10)) AND $modelNomina->validateDate(substr($_GET["fecha"],11,10)) ){
    $fecha = $_GET["fecha"];
  }else{
    $fecha = $fec_corte[0]["MES1"]."-".$fec_corte[0]["MES2"];
  }
}
/*----- GET PLAZA -----*/
$plaza = "ALL";
if( isset($_GET["plaza"]) ){
  if( $_GET["plaza"] == "CORPORATIVO" || $_GET["plaza"] == "CÓRDOBA" || $_GET["plaza"] == "MÉXICO" || $_GET["plaza"] == "GOLFO" || $_GET["plaza"] == "PENINSULA" || $_GET["plaza"] == "PUEBLA" || $_GET["plaza"] == "BAJIO" || $_GET["plaza"] == "OCCIDENTE" || $_GET["plaza"] == "NORESTE" ){
    $plaza = $_GET["plaza"];
  }else{
    $plaza = "ALL";
  }
}
//echo $plaza;


$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}

$tenedor = "ALL";
if (isset($_GET["tenedor"])) {
    $tenedor = $_GET["tenedor"];
}

$tipo = "ALL";
if( isset($_GET["tipo"]) ){
  if($_GET["tipo"] == "00017" || $_GET["tipo"] == "00050"
    || $_GET["tipo"] == "00056" || $_GET["tipo"] == "00057" || $_GET["tipo"] == "00059"
    || $_GET["tipo"] == "00060" || $_GET["tipo"] == "00065" || $_GET["tipo"] == "00073"
    || $_GET["tipo"] == "00074" || $_GET["tipo"] == "00077" || $_GET["tipo"] == "00078"
    || $_GET["tipo"] == "00083" || $_GET["tipo"] == "00084" || $_GET["tipo"] == "00085"
    || $_GET["tipo"] == "00086" || $_GET["tipo"] == "00087" || $_GET["tipo"] == "00088"
    || $_GET["tipo"] == "00089" || $_GET["tipo"] == "00091"){
    $tipo = $_GET["tipo"];
  }else{
    $tipo = "ALL";
  }
}

$status = "3";
if( isset($_GET["status"]) ){
$statusArray = explode(",",$_GET["status"]);
  for ($i=0; $i <count($statusArray) ; $i++) {
    if ( $statusArray[$i] == "1" || $statusArray[$i] == "2" || $statusArray[$i] == "3" ){
      $status = $_GET["status"];
    }else{
      $status = "3"; break;
    }
  }
}
/*----- GET CONTRATO -----*/
$contrato = "0,1,2,3";
if( isset($_GET["contrato"]) ){
$contratoArray = explode(",",$_GET["contrato"]);
  for ($i=0; $i <count($contratoArray) ; $i++) {
    if ( $contratoArray[$i] == "0" || $contratoArray[$i] == "1" || $contratoArray[$i] == "2" || $contratoArray[$i] == "3" ){
      $contrato = $_GET["contrato"];
    }else{
      $contrato = "0,1,2,3"; break;
    }
  }
}
/*----- GET DEPARTAMENTO -----*/
$depto = "ALL";
if ( isset($_GET["depto"]) ){
  $select_depto = $modelNomina->sql(3,$depto, null);
  for ($i=0; $i <count($select_depto) ; $i++) {
    if ( $select_depto[$i]["IID_DEPTO"] == $_GET["depto"]){
      $depto = $_GET["depto"]; break;
    }
  }
}
/*----- GET AREA -----*/
$area = "ALL";
if ( isset($_GET["area"]) ){
  if ( $depto != 'ALL' ){
    $select_area = $modelNomina->sql(4,$depto, null);
    for ($i=0; $i <count($select_area) ; $i++) { // FOR
      if ( $select_area[$i]["IID_AREA"] == $_GET["area"]){
        $area = $_GET["area"]; break;
      }
    }// /.FOR
  }
}

$fil_habilitado = "ALL";
if (isset($_GET["fil_habilitado"])) {
  $fil_habilitado = $_GET["fil_habilitado"];
}

$tabla_toneladas = $modelNomina->tabla_toneladas($fecha, $almacen, $tenedor);

$tabla_toneladas2 = $modelNomina->tabla_toneladas2($fecha,$almacen, $tenedor);
$tabla_toneladas3 = $modelNomina->tabla_toneladas3($fecha,$almacen, $tenedor);
$tabla_toneladas4 = $modelNomina->tabla_toneladas4($fecha,$almacen, $tenedor);


$tabla_toneladas5 = $modelNomina->tabla_toneladas5($fecha);

$grafica_tenedor = $modelNomina->graficas_tenedor($fecha,$almacen, $tenedor);
$grafica_merca = $modelNomina->graficas_merca($fecha, $almacen, $tenedor);

//$selectAlmacen = $modelNomina->almacenSql($plaza);
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- Select2 -->
<link rel="stylesheet" href="../plugins/select2/select2.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">

<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
  <section class="content-header">
    <h1>Dashboard<small>RESUMEN GENERAL DE GRANOS</small></h1>
  </section>

  <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
  <!-- ############################ SECCION GRAFICA ############################# -->
  <section>
    <div class="row"><!-- row -->
      <div class="col-md-9">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bar-chart"></i> Porcentaje Toneladas Con/Sin Tenedor</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body">
            <div id="graf_bar2" class="col-md-12" style="height:380px;"></div>

          </div>
        </div>
      </div>

      <!--FILTROS-->
      <div class="col-md-3"><!-- col-md-9 -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
          <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="detalles_granos.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
          <?php } ?>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <!-- FILTRAR POR CONTRATO -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
            <input type="text" class="form-control pull-right" name="nomFecha">
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
            <select class="form-control select2" id="nomPlaza" style="width: 100%;">
              <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $select_plaza = $modelNomina->sql(2,null,null);
              for ($i=0; $i <count($select_plaza) ; $i++) { ?>
                <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
              <?php } ?>
            </select>
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Almacen:</span>
            <select class="form-control select2" style="width: 100%;" id="nomAlm">
              <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $plazas = $_GET["plaza"];
              $selectAlmacen = $modelNomina->almacenSql($plazas);
              for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
                <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
              <?php } ?>
            </select>
          </div>


          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Tenedor:</span>
            <select class="form-control select2" style="width: 100%;" id="nomTen">
              <option value="ALL" <?php if( $tenedor == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $selectTenedor = $modelNomina->filtro_inst_finan($fecha);
              for ($i=0; $i <count($selectTenedor) ; $i++) { ?>
                <option value="<?=$selectTenedor[$i]["ID_INST"]?>" <?php if($selectTenedor[$i]["ID_INST"] == $tenedor){echo "selected";} ?>><?=$selectTenedor[$i]["NOMBRE_FINAN"]?> </option>
              <?php } ?>
            </select>
          </div>
          <div class="input-group">
            <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
          </div>

        </div><!--/.box-body-->
      </div>

      </div>

      <div class="col-md-9">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bar-chart"></i> Porcentaje Tipo de Mercancia</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body">
            <div id="graf_bar3" class="col-md-12" style="height:380px;"></div>

          </div>
        </div>
      </div>

      <!--FILTROS-->      <!-- /.col-md-3 -->
    </div>

    <div class="row"><!-- row -->

    <div class="col-md-9"><!-- col-md-9 -->
    <div class="box box-primary">
      <div class="box-body"><!--box-body-->

        <div class="row">

          <div class="col-md-12">
            <section>
              <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-table"></i> RESUMEN GENERAL DE GRANOS</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body"><!--box-body-->

                  <div class="table-responsive" id="container">
                    <table id="tabla_nomina" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <!--<th class="small" bgcolor="#4791de"><font color="white">ID</font></th>-->
                          <th class="small" bgcolor="#4791de"><font color="white">NOMBRE</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">RAZON SOCIAL</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">INSTITUCION FINANCIERA</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">MERCANCIA</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">CDT</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">N</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">S</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">TOTAL</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $transitoN = 0; $transitoS = 0; $transitoCDT = 0;  for ($i=0; $i <count($tabla_toneladas2) ; $i++) { ?>
                        <tr>
                          <!--<td class="small">CL</td>-->
                          <td class="small"><?= $tabla_toneladas2[$i]["V_NOMBRE"] ?></td>
                          <td class="small"><?= $tabla_toneladas2[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= $tabla_toneladas2[$i]["NINF"] ?></td>
                          <td class="small"><?= $tabla_toneladas2[$i]["PARTE"] ?></td>
                          <td class="small"><?= number_format($tabla_toneladas2[$i]["CANTIDADCTD"]/1000, 2) ?></td>
                          <td class="small"><?= number_format($tabla_toneladas2[$i]["CANTIDADN"]/1000, 2) ?></td>
                          <td class="small"><?= number_format($tabla_toneladas2[$i]["CANTIDADS"]/1000, 2) ?></td>
                          <td class="small"><?= number_format(($tabla_toneladas2[$i]["CANTIDADS"]/1000) + ($tabla_toneladas2[$i]["CANTIDADN"]/1000) + ($tabla_toneladas2[$i]["CANTIDADCTD"]/1000),2) ?></td>
                        </tr>
                        <?php  $transitoCDT = $transitoCDT + $tabla_toneladas2[$i]["CANTIDADCTD"];
                               $transitoN  = $transitoN + $tabla_toneladas2[$i]["CANTIDADN"];
                               $transitoS = $transitoS +  $tabla_toneladas2[$i]["CANTIDADS"];} ?>
                        <tr style="background: #3b83bd">
                          <td></td>
                          <td>TOTAL EN TRANSITO</td>
                          <td></td>
                          <td></td>
                          <td><?= number_format($transitoCDT/1000, 2) ?></td>
                          <td><?= number_format($transitoN/1000, 2) ?></td>
                          <td><?= number_format($transitoS/1000, 2) ?></td>
                          <td><?= number_format(($transitoN+$transitoS+$transitoCDT)/1000, 2) ?></td>
                        </tr>
                        <?php $habN = 0; $habS = 0; $habcdt = 0; $transitoTotal = 0;for ($i=0; $i <count($tabla_toneladas3) ; $i++) { ?>
                        <tr>
                          <!--<td class="small">CL</td>-->
                          <td class="small"><?= $tabla_toneladas3[$i]["V_NOMBRE"] ?></td>
                          <td class="small"><?= $tabla_toneladas3[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= $tabla_toneladas3[$i]["NINF"] ?></td>
                          <td class="small"><?= $tabla_toneladas3[$i]["PARTE"] ?></td>
                          <td class="small"><?= number_format($tabla_toneladas3[$i]["CANTIDADCTD"]/1000, 2) ?></td>
                          <td class="small"><?= number_format($tabla_toneladas3[$i]["CANTIDADN"]/1000, 2) ?></td>
                          <td class="small"><?= number_format($tabla_toneladas3[$i]["CANTIDADS"]/1000, 2) ?></td>
                          <td class="small"><?= number_format(($tabla_toneladas3[$i]["CANTIDADS"]/1000) + ($tabla_toneladas3[$i]["CANTIDADN"]/1000) + ($tabla_toneladas3[$i]["CANTIDADCTD"]/1000),2) ?></td>
                        </tr>
                        <?php $habcdt = $habcdt + $tabla_toneladas3[$i]["CANTIDADCTD"];
                               $habN  = $habN + $tabla_toneladas3[$i]["CANTIDADN"];
                               $habS = $habS +  $tabla_toneladas3[$i]["CANTIDADS"];} ?>
                        <tr style="background: #3b83bd">
                          <td></td>
                          <td>TOTAL EN HABILITADO</td>
                          <td></td>
                          <td></td>
                          <td><?= number_format($habcdt/1000, 2) ?></td>
                          <td><?= number_format($habN/1000, 2) ?></td>
                          <td><?= number_format($habS/1000, 2) ?></td>
                          <td><?= number_format(($habS+$habN+$habcdt)/1000, 2) ?></td>
                        </tr>
                        <?php $dirn = 0; $dirs = 0; $dircdt = 0; $transitoTotal = 0; for ($i=0; $i <count($tabla_toneladas4) ; $i++) { ?>
                        <tr>
                          <!--<td class="small">CL</td>-->
                          <td class="small"><?= $tabla_toneladas4[$i]["V_NOMBRE"] ?></td>
                          <td class="small"><?= $tabla_toneladas4[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= $tabla_toneladas4[$i]["NINF"] ?></td>
                          <td class="small"><?= $tabla_toneladas4[$i]["PARTE"] ?></td>
                          <td class="small"><?= number_format($tabla_toneladas4[$i]["CANTIDADCTD"]/1000, 2) ?></td>
                          <td class="small"><?= number_format($tabla_toneladas4[$i]["CANTIDADN"]/1000, 2) ?></td>
                          <td class="small"><?= number_format($tabla_toneladas4[$i]["CANTIDADS"]/1000, 2) ?></td>
                          <td class="small"><?= number_format(($tabla_toneladas4[$i]["CANTIDADS"]/1000) + ($tabla_toneladas4[$i]["CANTIDADN"]/1000) + ($tabla_toneladas4[$i]["CANTIDADCTD"]/1000),2) ?></td>
                        </tr>
                        <?php $dircdt = $dircdt + $tabla_toneladas4[$i]["CANTIDADCTD"];
                               $dirn  = $dirn + $tabla_toneladas4[$i]["CANTIDADN"];
                               $dirs = $dirs +  $tabla_toneladas4[$i]["CANTIDADS"];} ?>
                        <tr style="background: #3b83bd">
                          <td></td>
                          <td>TOTAL DIRECTO</td>
                          <td></td>
                          <td></td>
                          <td><?= number_format($dircdt/1000, 2) ?></td>
                          <td><?= number_format($dirn/1000, 2) ?></td>
                          <td><?= number_format($dirs/1000, 2) ?></td>
                          <td><?= number_format(($dirs+$dirn+$dircdt)/1000, 2) ?></td>
                        </tr>
                      </tbody>
                      <tfoot>
                         <tr>
                           <td></td>
                           <td>TOTAL</td>
                           <td></td>
                           <td></td>
                           <td><?= number_format(($transitoCDT+$dircdt+$habcdt)/1000, 2)?></td>
                           <td><?= number_format(($transitoN+$dirn+$habN)/1000, 2)?></td>
                           <td><?= number_format(($transitoS+$dirs+$habS)/1000, 2)?></td>
                           <td><?= number_format(($transitoN+$dirn+$habN+$transitoS+$dirs+$habS+$transitoCDT+$dircdt+$habcdt)/1000, 2)?></td>
                         </tr>
                     </tfoot>
                    </table>
                  </div>

                </div><!--/.box-body-->
              </div>
            </section>

            <section>
              <div class="box box-success">
                <div class="box-header with-borderxds2001">
                  <h3 class="box-title"><i class="fa fa-table"></i> SEPARACION POR ALMACEN TRANSITO</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body"><!--box-body-->

                  <div class="table-responsive" id="container">
                    <table id="tabla_nomina2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="small" bgcolor="#4791de"><font color="white">NOMBRE</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">RAZON SOCIAL</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">INSTITUCION FINANCIERA</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">MERCANCIA</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">TONELADAS</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i <count($tabla_toneladas2) ; $i++) { ?>
                        <tr>
                          <td class="small"><?= $tabla_toneladas2[$i]["V_NOMBRE"] ?></td>
                          <td class="small"><?= $tabla_toneladas2[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= $tabla_toneladas2[$i]["NINF"] ?></td>
                          <td class="small"><?= $tabla_toneladas2[$i]["PARTE"] ?></td>
                          <td class="small"><?= number_format($tabla_toneladas2[$i]["CANTIDADCTD"]/1000, 2) ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                      <tfoot>
                         <tr>
                             <th colspan="4" style="text-align:right">Total Toneladas:</th>
                             <th></th>
                         </tr>
                     </tfoot>
                    </table>
                  </div>

                </div><!--/.box-body-->
              </div>
            </section>


            <section>
              <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-table"></i> SEPARACION POR ALMACEN HABILITADO</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body"><!--box-body-->

                  <div class="table-responsive" id="container">
                    <table id="tabla_nomina3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="small" bgcolor="#4791de"><font color="white">NOMBRE</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">RAZON SOCIAL</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">INSTITUCION FINANCIERA</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">MERCANCIA</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">S</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">N</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">TOTAL</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          for ($i=0; $i < count($tabla_toneladas3) ; $i++) { ?>
                        <tr>
                          <td class="small"><?= $tabla_toneladas3[$i]["V_NOMBRE"] ?></td>
                          <td class="small"><?= $tabla_toneladas3[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= $tabla_toneladas3[$i]["NINF"] ?></td>
                          <td class="small"><?= $tabla_toneladas3[$i]["PARTE"] ?></td>
                          <td class="small"><?= number_format($tabla_toneladas3[$i]["CANTIDADN"]/1000, 2) ?></td>
                          <td class="small"><?= number_format($tabla_toneladas3[$i]["CANTIDADS"]/1000, 2) ?></td>
                          <td class="small"><?= number_format($tabla_toneladas3[$i]["CANTIDADN"]/1000 + $tabla_toneladas3[$i]["CANTIDADS"]/1000, 2) ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                      <tfoot>
                         <tr>
                             <th colspan="4" style="text-align:right">Total Toneladas:</th>
                             <th></th>
                             <th></th>
                             <th></th>
                         </tr>
                     </tfoot>
                    </table>
                  </div>

                </div><!--/.box-body-->
              </div>
            </section>

            <section>
              <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-table"></i> SEPARACION POR ALMACEN DIRECTO</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body"><!--box-body-->

                  <div class="table-responsive" id="container">
                    <table id="tabla_nomina4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="small" bgcolor="#4791de"><font color="white">NOMBRE</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">RAZON SOCIAL</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">INSTITUCION FINANCIERA</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">MERCANCIA</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">S</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">N</font></th>
                          <th class="small" bgcolor="#4791de"><font color="white">TOTAL</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          for ($i=0; $i < count($tabla_toneladas4) ; $i++) { ?>
                        <tr>
                          <td class="small"><?= $tabla_toneladas4[$i]["V_NOMBRE"] ?></td>
                          <td class="small"><?= $tabla_toneladas4[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= $tabla_toneladas4[$i]["NINF"] ?></td>
                          <td class="small"><?= $tabla_toneladas4[$i]["PARTE"] ?></td>
                          <td class="small"><?= number_format($tabla_toneladas4[$i]["CANTIDADN"]/1000, 2) ?></td>
                          <td class="small"><?= number_format($tabla_toneladas4[$i]["CANTIDADS"]/1000, 2) ?></td>
                          <td class="small"><?= number_format($tabla_toneladas4[$i]["CANTIDADN"]/1000 + $tabla_toneladas4[$i]["CANTIDADS"]/1000, 2) ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                      <tfoot>
                         <tr>
                             <th colspan="2" style="text-align:right">Total Toneladas:</th>
                             <th></th>
                             <th></th>
                             <th></th>
                         </tr>
                     </tfoot>
                    </table>
                  </div>

                </div><!--/.box-body-->
              </div>
            </section>


          </div>
          <!--GRAFICA NOMINA POR MES DIEGO ALTAMIRANO SUAREZ-->
        </div>

      </div><!--/.box-body-->
    </div>
    </div><!-- /.col-md-9 -->
    </div><!-- /.row -->
  </section>
  <!-- ############################ /.SECCION GRAFICA ############################# -->


<?php if ( isset($_GET["fecha"]) || isset($_GET["plaza"]) || isset($_GET["tipo"]) || isset($_GET["status"]) || isset($_GET["contrato"]) || isset($_GET["depto"]) || isset($_GET["almacen"]) ){ ?>
  <!-- ############################ TABLA DETALLE DE NOMINA PAGADA ############################# -->
  <!-- ############################ /.TABLA DETALLE DE NOMINA PAGADA ############################# -->
<?php } ?>


  </section><!-- Termina la seccion de Todo el contenido principal -->
</div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
$('.select2').select2()

</script>
<script type="text/javascript">
/*---- SELECT TIPO DE NOMINA ----*/
$('#nomStatus').select2({
  tags: true,
  tokenSeparators: [","]
});
$('#nomStatus').val([<?=$status?>]).trigger("change");
/*---- SELECT TIPO CONTRATO ----*/
$('#nomContrato').select2({
  tags: true,
  tokenSeparators: [","]
});
$('#nomContrato').val([<?=$contrato?>]).trigger("change");
/*------ SELECT AREA ------*/
$("#nomDepto").change(function (){
  $.ajax({
    type: 'post',
    url: '../action/rotacion_personal.php',
    data: { "depto" : $(this).val() },
    beforeSend: function () {
      $('#nomArea')
      .empty()
      .append('<option value="ALL">ALL</option>');
    },
    success: function (response) {// success
      var dataJson = JSON.parse(response);
        var $select = $('#nomArea');
        $.each(dataJson, function(i, val){
          $select.append($('<option></option>').attr('value', val.IID_AREA).text( val.V_DESCRIPCION ));
        });

    }// ./succes
  });
});

/*---- CLICK BOTON FILTRAR ----*/
$(".btnNomFiltro").on("click", function(){
  fecha = $('input[name="nomFecha"]').val();
  plaza = $('#nomPlaza').val();
  tipo = $('#nomTipo').val();
  status = $('#nomStatus').val();
  contrato = $('#nomContrato').val();
  depto = $('#nomDepto').val();
  almacen = $('#nomAlm').val();
  tenedor = $('#nomTen').val();
  fil_habilitado = 'off';

  if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?fecha='+fecha+'&plaza='+plaza+'&tipo='+tipo+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&tenedor='+tenedor;
  }
  else {
    fil_habilitado = 'off';
    url = '?fecha='+fecha+'&plaza='+plaza+'&tipo='+tipo+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&tenedor='+tenedor;;
  }
  location.href = url;

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
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#tabla_nomina').DataTable( {
        "lengthMenu": [100],
        "searching":false,
        "ordering": false,
        "scrollY": 450,
        fixedHeader: true,
        "dom": '<"toolbar">frtip',
        stateSave: true,
        "scrollX": true,
        "language": {
            "url": "../plugins/datatables/Spanish.json"
        },

        dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
            buttons: [

              {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                exportOptions: {//muestra/oculta visivilidad de columna
                    columns: ':visible'
                },
                title: 'Suma Toneladas',
              },

              {
                extend: 'print',
                text: '<i class="fa fa-print"></i>',
                titleAttr: 'Imprimir',
                exportOptions: {//muestra/oculta visivilidad de columna
                    columns: ':visible',
                },
                title: 'Suma Toneladas',
              },

              {
                extend: 'colvis',
                collectionLayout: 'fixed two-column',
                text: '<i class="fa fa-eye-slash"></i>',
                titleAttr: '(Mostrar/ocultar) Columnas',
                autoClose: true,
              }
            ],
    } );
} );

function number_format(amount, decimals) {

        amount += ''; // por si pasan un numero en vez de un string
        amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

        decimals = decimals || 0; // por si la variable no fue fue pasada

        // si no es un numero o es igual a cero retorno el mismo cero
        if (isNaN(amount) || amount === 0)
            return parseFloat(0).toFixed(decimals);

        // si es mayor o menor que cero retorno el valor formateado como numero
        amount = '' + amount.toFixed(decimals);

        var amount_parts = amount.split('.'),
            regexp = /(\d+)(\d{3})/;

        while (regexp.test(amount_parts[0]))
            amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

        return amount_parts.join('.');
    }

</script>

<script type="text/javascript">
$(document).ready(function() {

    $('#tabla_nomina2').DataTable( {
      "lengthMenu": [[25, 25, -1], [25, 25, "All"]],
      "footerCallback": function ( row, data, start, end, display ) {
          var api = this.api(), data;

          // Remove the formatting to get integer data for summation
          var intVal = function ( i ) {
              return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  //i.replace('.','').replace(/[\$,]/g, '.')*1:
                  typeof i === 'number' ?
                      i : 0;
          };

          // Total over all pages
          total = api
              .column( 2 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

          // Total over this page
          pageTotal = api
              .column( 4, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var number = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(number);
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal , 2)
          );
      },
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
                columns: ':visible'
            },
            title: 'Suma Toneladas',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Suma Toneladas',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_nomina3').DataTable( {
      "lengthMenu": [[25, 25, -1], [25, 25, "All"]],
      "footerCallback": function ( row, data, start, end, display ) {
          var api = this.api(), data;

          // Remove the formatting to get integer data for summation
          var intVal = function ( i ) {
              return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  //i.replace('.','').replace(/[\$,]/g, '.')*1:
                  typeof i === 'number' ?
                      i : 0;
          };

          // Total over all pages
          total = api
              .column( 6 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 5 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 4 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );

          // Total over this page
          pageTotal = api
              .column( 6, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var number = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(number);
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 5, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                    var number = intVal(a) + intVal(b);
                    return Intl.NumberFormat('es-MX').format(number);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 4, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                        var number = intVal(a) + intVal(b);
                        return Intl.NumberFormat('es-MX').format(number);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 6 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal , 2)
          );

          $( api.column( 5 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2 , 2)
          );

          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3 , 2)
          );
      },
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
                columns: ':visible'
            },
            title: 'Suma Toneladas',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Suma Toneladas',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>


<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_nomina4').DataTable( {
      "lengthMenu": [[25, 25, -1], [25, 25, "All"]],
      "footerCallback": function ( row, data, start, end, display ) {
          var api = this.api(), data;

          // Remove the formatting to get integer data for summation
          var intVal = function ( i ) {
              return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  //i.replace('.','').replace(/[\$,]/g, '.')*1:
                  typeof i === 'number' ?
                      i : 0;
          };

          // Total over all pages
          total = api
              .column( 4 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 2 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );

          // Total over this page
          pageTotal = api
              .column( 4, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var number = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(number);
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 3, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                    var number = intVal(a) + intVal(b);
                    return Intl.NumberFormat('es-MX').format(number);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 2, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                        var number = intVal(a) + intVal(b);
                        return Intl.NumberFormat('es-MX').format(number);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal , 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2 , 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3 , 2)
          );
      },
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
                columns: ':visible'
            },
            title: 'Suma Toneladas',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Suma Toneladas',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>

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
          #echo $grafica[$i]["TIPO_RES"];
          if ($tenedor == "ALL") {
            $contestadas = "CON TENEDOR";
            $no_contestadas ="SIN TENEDOR";
          }else {
            $contestadas = "TENEDOR";
            if ($tenedor <> 0) {
              $nombreInsFin = $modelNomina->select_Insf($tenedor);
            }
          }

          //$plaza_corta = str_word_count($plaza, 1);


          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          #echo $tenedor ;
          if ($tenedor == "ALL") {
            $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_tenedor[0]["TOTAL_TENDEDOR"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_tenedor[0]["TOTAL_TENDEDOR"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>CON TENEDOR '.number_format($grafica_tenedor[0]["TOTAL_TENDEDOR"], 2).'</button></form>';
            $label2 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_tenedor[0]["TOTAL_SIN_TENEDOR"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_tenedor[0]["TOTAL_SIN_TENEDOR"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>SIN TENEDOR '.number_format($grafica_tenedor[0]["TOTAL_SIN_TENEDOR"], 2).'</button></form>' ;
          }else {
            if ($tenedor == 0) {
              $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_tenedor[0]["TOTAL_TENDEDOR"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_tenedor[0]["TOTAL_TENDEDOR"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled> '.$tenedor." ".number_format($grafica_tenedor[0]["TOTAL_TENDEDOR"], 2).'</button></form>' ;
              $label2 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_tenedor[0]["TOTAL_SIN_TENEDOR"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_tenedor[0]["TOTAL_SIN_TENEDOR"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>SIN TENEDOR '.number_format($grafica_tenedor[0]["TOTAL_SIN_TENEDOR"], 2).'</button></form>' ;
            }else {
              $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_tenedor[0]["TOTAL_TENDEDOR"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_tenedor[0]["TOTAL_TENDEDOR"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled> '.$nombreInsFin[0]["V_NOMBRE"]." ".number_format($grafica_tenedor[0]["TOTAL_TENDEDOR"], 2).'</button></form>' ;
              $label2 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_tenedor[0]["TOTAL_SIN_TENEDOR"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_tenedor[0]["TOTAL_SIN_TENEDOR"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>SIN TENEDOR '.number_format($grafica_tenedor[0]["TOTAL_SIN_TENEDOR"], 2).'</button></form>' ;
            }

          }

          //$label3 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica[$i]["TIPO_RES"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica[$i]["TIPO_RES"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$grafica[$i]["TIPO_RES"].'</button></form>' ;

          $color ='#FAEF07';
          $color2 = '#1FBC0C';

          $data = round($grafica_tenedor[0]["TOTAL_TENDEDOR"], 2);
          $data2 = round($grafica_tenedor[0]["TOTAL_SIN_TENEDOR"], 2);
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
          for($i = 0; $i < count($grafica_merca); $i++){

            #echo $grafica[$i]["TIPO_RES"];
            $contestadas = $grafica_merca[$i]["MERCH"];
            //$plaza_corta = str_word_count($plaza, 1);

            // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //

            $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_merca[$i]["TOTAL_REAL"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_merca[$i]["TOTAL_REAL"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.substr($grafica_merca[$i]["MERCH"], 0, 30).'</button></form>' ;
            //$label2 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_merca[0]["TOTAL_SIN_TENEDOR"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_merca[0]["TOTAL_SIN_TENEDOR"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>SIN TENEDOR '.number_format($grafica_merca[0]["TOTAL_SIN_TENEDOR"], 2).'</button></form>' ;
            //$label3 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica[$i]["TIPO_RES"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica[$i]["TIPO_RES"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$grafica[$i]["TIPO_RES"].'</button></form>' ;

            $color = substr(md5(time()*$i), 0, 6);
            $color = '#'.$color;
            //$color2 = '#1FBC0C';

            $data = round($grafica_merca[$i]["TOTAL_REAL"], 2);
            //$data2 = round($grafica_tenedor[0]["TOTAL_SIN_TENEDOR"], 2);
            $color = $color;
            #echo $label;
            ?>
            {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},
          <?php
          }
            // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        //{label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},
        /*{label: '<?= $label2 ?>', data: <?=$data2?> , color: '<?= $color2 ?>'}*/

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

<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
$('input[name="nomFecha"]').daterangepicker(
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
    startDate: '<?=substr($fecha,0,10)?>',
    endDate: '<?=substr($fecha,11,10)?>'
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
  $('.fancybox').fancybox();
  $(".fancybox-effects-a").fancybox({
    helpers: { title : { type : 'outside' }, overlay : { speedOut : 0 } }
  });
  $(".fancybox-effects-b").fancybox({openEffect  : 'none',closeEffect : 'none',helpers : {title : {type : 'over'}} });
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
