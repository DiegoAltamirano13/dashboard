<?php
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:dias_descansados.php");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 20);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/Riesgo.php';
$modelNomina = new NominaPagada();
//SQL ULTIMA FECHA DE CORTE
$fec_corte = $modelNomina->sql(1,null);
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
  if( $_GET["plaza"] <> "ALL"){
    $plaza = $_GET["plaza"];
  }else{
    $plaza = "ALL";
  }
}
/*----- GET TIPO NOMINA -----*/
$tipo = "1,2,3,4,5,6,7,8,9,10,11";
if( isset($_GET["tipo"]) ){
$tipoArray = explode(",",$_GET["tipo"]);
  for ($i=0; $i <count($tipoArray) ; $i++) {
    if ( $tipoArray[$i] == "1"||$tipoArray[$i] == "2"||$tipoArray[$i] == "3"||$tipoArray[$i] == "4"||$tipoArray[$i] == "5"||$tipoArray[$i] == "6" || $tipoArray[$i] == "7" || $tipoArray[$i] == "8" || $tipoArray[$i] == "9" || $tipoArray[$i] == "10" || $tipoArray[$i] == "11" ){
      $tipo = $_GET["tipo"];
    }else{
      $tipo = "1,2,3,4,5,6,7,8,9,10,11"; break;
    }
  }
}
/*----- GET STATUS NOMINA -----*/
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
  $select_depto = $modelNomina->sql(3,$depto);
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
    $select_area = $modelNomina->sql(4,$depto);
    for ($i=0; $i <count($select_area) ; $i++) { // FOR
      if ( $select_area[$i]["IID_AREA"] == $_GET["area"]){
        $area = $_GET["area"]; break;
      }
    }// /.FOR
  }
}

//GRAFICA NOMINA
//$GRAFICAina = $modelNomina->graficaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area);
$graficaNomina = $modelNomina->graficaNomina($fecha,$plaza, $tipo);
$graficaMensual = $modelNomina->grafica_Mensual($fecha,$plaza,$tipo);
//TABLA DETALLE DE NOMINA PAGADA
$tablaNomina = $modelNomina->tablaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area);
$tablaNomina2 = $modelNomina->tablaNomina2();
$tablaNomina3 = $modelNomina->tablaNomina3();
$tablaNomina4 = $modelNomina->tablaNomina4();
$tablaNomina5 = $modelNomina->tablaNomina5();
$widgets = $modelNomina->widgetsRiesgo($plaza);
$widgetsPrimaAnt = $modelNomina->widgetsRiesgo2($plaza);
$widgetsPrimaNue = $modelNomina->widgetsRiesgo3($plaza);

//CALCULAR RANGO DE FECHAS
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
    <h1>Dashboard<small>Faltas en Gral.</small></h1>
  </section>

  <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
  <!-- ############################ SECCION GRAFICA ############################# -->
  <section>

    <div class="row"><!-- row -->

    <div class="col-md-9"><!-- col-md-9 -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-bar-chart"></i> GRAFICA</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <div class="row">

          <div class="col-md-12">
            <div id="graficaNom"></div>
          </div>
        </div><!--ROW-->
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE EMPLEADOS RIESGO ENFERMEDAD GENERAL</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body"><!--box-body-->
            <div class="nav-tabs-custom">
              <ul class="nav nav-pills" id="myTab">
                <li ><a href="#2017" data-toggle="tab"><i class="fa fa-users"></i>
                  <?php
                    $fecha_act = date('Y');
                    $nfecha = strtotime('-1 year', strtotime($fecha_act));
                    $nfecha = date('Y', $nfecha);
                    echo $nfecha;
                   ?>
                  <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"></span></a>
                </li>
                <li class="active"> <a href="#2018" data-toggle="tab"><i class="fa fa-users"></i>
                  <?php
                    $fecha_act = date('Y');
                    echo $fecha_act;
                   ?>
                  <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"></span></a>
                </li>
              </ul>
            </div>
              <div class="tab-content">
                <div class="tab-pane active" id="2018">
                  <div class="table-responsive" id="container">
                    <table id="activo2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="small" bgcolor="#383F6D"><font color="white">ID</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">EMPLEADO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PLAZA</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PUESTO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">FECHA ACCIDENTE</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">TIPO DE RIESGO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">DIAS SUBSIDIADOS</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PORCENTAJE DE INCAPACIDAD PERMANENTE PARCIAL O TOTAL</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">DEFUNCION</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">FECHA DE ALTA</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i <count($tablaNomina3) ; $i++) { ?>
                        <tr>
                          <td class="small"><?= $tablaNomina3[$i]["IID_EMPLEADO"] ?></td>
                          <td class="small"><?= $tablaNomina3[$i]["NOMBRE"] ?></td>
                          <td class="small"><?= $tablaNomina3[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= $tablaNomina3[$i]["V_DESCRIPCION"] ?></td>
                          <td class="small"><?= $tablaNomina3[$i]["FECHA_ACCIDENTE"] ?></td>
                          <td class="small"><?= $tablaNomina3[$i]["TIPO_RIESGO"] ?></td>
                          <td class="small"><?= $tablaNomina3[$i]["DIAS_SUBSIDIADOS"] ?></td>
                          <td class="small"><?= $tablaNomina3[$i]["PORCENTAJE_INCAPACIDAD"] ?></td>
                          <td class="small"><?= $tablaNomina3[$i]["DEFUNCION"] ?></td>
                          <td class="small"><?= $tablaNomina3[$i]["FECHA_ALTA"] ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="tab-pane" id="2017">
                  <div class="table-responsive" id="container">
                    <table id="activo1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="small" bgcolor="#383F6D"><font color="white">ID</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">EMPLEADO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PLAZA</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PUESTO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">FECHA ACCIDENTE</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">TIPO DE RIESGO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">DIAS SUBSIDIADOS</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PORCENTAJE DE INCAPACIDAD PERMANENTE PARCIAL O TOTAL</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">DEFUNCION</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">FECHA DE ALTA</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i <count($tablaNomina2) ; $i++) { ?>
                        <tr>
                          <td class="small"><?= $tablaNomina2[$i]["IID_EMPLEADO"] ?></td>
                          <td class="small"><?= $tablaNomina2[$i]["NOMBRE"] ?></td>
                          <td class="small"><?= $tablaNomina2[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= $tablaNomina2[$i]["V_DESCRIPCION"] ?></td>
                          <td class="small"><?= $tablaNomina2[$i]["FECHA_ACCIDENTE"] ?></td>
                          <td class="small"><?= $tablaNomina2[$i]["TIPO_RIESGO"] ?></td>
                          <td class="small"><?= $tablaNomina2[$i]["DIAS_SUBSIDIADOS"] ?></td>
                          <td class="small"><?= $tablaNomina2[$i]["PORCENTAJE_INCAPACIDAD"] ?></td>
                          <td class="small"><?= $tablaNomina2[$i]["DEFUNCION"] ?></td>
                          <td class="small"><?= $tablaNomina2[$i]["FECHA_ALTA"] ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

          </div><!--/.box-body-->
        </div>
      </div><!--/.box-body-->
    </div>
    </div><!-- /.col-md-9 -->

    <div class="col-md-3"><!-- col-md-9 -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-table"></i>PRIMA DE RIESGO </h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body"><!--box-body-->
          <div class="table-responsive" id="container">
            <table id="tabla_riesgo" class="table table-striped table-bordered" cellspacing = "0"width="100%">
              <thead>
                <tr>
                    <th class="small" bgcolor="#1c9be2"><font color="white">AÑO</font></th>
                    <th class="small" bgcolor="#1c9be2"><font color="white">RIESGO</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for($i = 0; $i < count($tablaNomina); $i++){ ?>
                  <tr>
                  <td class="small"><?= $tablaNomina[$i]["N_ANIO"]?></td>
                  <td class="small"><?= $tablaNomina[$i]["N_PORCENTAJE"]?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
        </div>
    </div>
    <!--WIDGETS PERRONES-->
    <div id="div_widgets" class="col-md-3">

      <div class="info-box bg-red">
        <span class="info-box-icon"><i class="fa fa-exclamation"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">CLASE DE RIESGO</span>
          <span class="info-box-number">  IV   </span>
          <div class="progress">
            <div class="progress-bar" style="width: 70%"></div>
          </div>
        <?php
            $fecha_act = date('Y');
            $nfecha = strtotime('-1 year', strtotime($fecha_act));
            $nfecha = date('Y', $nfecha);
            echo '<span class="progress-description">Fecha de consulta: '.$nfecha.'</span>';
           ?>
        </div>
      </div>

    <div class="info-box bg-blue">
      <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">DIAS SUBSIDIADOS</span>
        <span class="info-box-number"><?php echo $widgets[0]["DIAS"]; ?></span>
        <div class="progress">
          <div class="progress-bar" style="width: 70%"></div>
        </div>
        <?php
          $fecha_act = date('Y');
          $nfecha = strtotime('-1 year', strtotime($fecha_act));
          $nfecha = date('Y', $nfecha);
          echo '<span class="progress-description">Fecha de consulta: '.$nfecha.'</span>';
         ?>
      </div>
    </div>

    <div class="info-box bg-green">
      <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">PRIMA ANTERIOR</span>
        <span class="info-box-number"><?php echo $widgetsPrimaAnt[0]["N_PORCENTAJE"]; ?></span>
        <div class="progress">
          <div class="progress-bar" style="width: 70%"></div>
        </div>
        <?php
          $fecha_act = date('Y');
          $nfecha = strtotime('-1 year', strtotime($fecha_act));
          $nfecha = date('Y', $nfecha);
          echo '<span class="progress-description">Fecha de consulta: '.$nfecha.'</span>';
         ?>
      </div>
    </div>


    <div class="info-box bg-green">
      <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">PRIMA NUEVA</span>
        <span class="info-box-number"><?php echo $widgetsPrimaNue[0]["N_PORCENTAJE"]; ?></span>
        <div class="progress">
          <div class="progress-bar" style="width: 70%"></div>
        </div>
        <?php
          $fecha_act = date('Y');
          $nfecha = strtotime('-1 year', strtotime($fecha_act));
          $nfecha = date('Y', $nfecha);
          echo '<span class="progress-description">Fecha de consulta: '.$nfecha.'</span>';
         ?>
      </div>
    </div>

    </div>

    <!--################################################################# riesgo trayecto #########################################################################-->
    <div class="col-md-9"><!-- col-md-9 -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-bar-chart"></i> RIESGO DE TRAYECTO</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <div class="row">

          <div class="col-md-12">
            <div id="graficaNom"></div>
          </div>
        </div><!--ROW-->
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE EMPLEADOS RIESGO TRAYECTO</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body"><!--box-body-->
            <div class="nav-tabs-custom">
              <ul class="nav nav-pills" id="myTab">
                <li ><a href="#anterior" data-toggle="tab"><i class="fa fa-users"></i>
                  <?php
                    $fecha_act = date('Y');
                    $nfecha = strtotime('-1 year', strtotime($fecha_act));
                    $nfecha = date('Y', $nfecha);
                    echo $nfecha;
                   ?>
                  <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"></span></a>
                </li>
                <li class="active"> <a href="#actual" data-toggle="tab"><i class="fa fa-users"></i>
                  <?php
                    $fecha_act = date('Y');
                    echo $fecha_act;
                   ?>
                  <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"></span></a>
                </li>
              </ul>
            </div>
              <div class="tab-content">
                <div class="tab-pane active" id="actual">
                  <div class="table-responsive" id="container">
                    <table id="activo3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="small" bgcolor="#383F6D"><font color="white">ID</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">EMPLEADO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PLAZA</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PUESTO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">FECHA ACCIDENTE</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">TIPO DE RIESGO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">DIAS SUBSIDIADOS</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PORCENTAJE DE INCAPACIDAD PERMANENTE PARCIAL O TOTAL</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">DEFUNCION</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">FECHA DE ALTA</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i <count($tablaNomina4) ; $i++) { ?>
                        <tr>
                          <td class="small"><?= $tablaNomina4[$i]["IID_EMPLEADO"] ?></td>
                          <td class="small"><?= $tablaNomina4[$i]["NOMBRE"] ?></td>
                          <td class="small"><?= $tablaNomina4[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= $tablaNomina4[$i]["V_DESCRIPCION"] ?></td>
                          <td class="small"><?= $tablaNomina4[$i]["FECHA_ACCIDENTE"] ?></td>
                          <td class="small"><?= $tablaNomina4[$i]["TIPO_RIESGO"] ?></td>
                          <td class="small"><?= $tablaNomina4[$i]["DIAS_SUBSIDIADOS"] ?></td>
                          <td class="small"><?= $tablaNomina4[$i]["PORCENTAJE_INCAPACIDAD"] ?></td>
                          <td class="small"><?= $tablaNomina4[$i]["DEFUNCION"] ?></td>
                          <td class="small"><?= $tablaNomina4[$i]["FECHA_ALTA"] ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="tab-pane" id="anterior">
                  <div class="table-responsive" id="container">
                    <table id="activo4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="small" bgcolor="#383F6D"><font color="white">ID</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">EMPLEADO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PLAZA</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PUESTO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">FECHA ACCIDENTE</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">TIPO DE RIESGO</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">DIAS SUBSIDIADOS</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">PORCENTAJE DE INCAPACIDAD PERMANENTE PARCIAL O TOTAL</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">DEFUNCION</font></th>
                          <th class="small" bgcolor="#383F6D"><font color="white">FECHA DE ALTA</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i <count($tablaNomina5) ; $i++) { ?>
                        <tr>
                          <td class="small"><?= $tablaNomina5[$i]["IID_EMPLEADO"] ?></td>
                          <td class="small"><?= $tablaNomina5[$i]["NOMBRE"] ?></td>
                          <td class="small"><?= $tablaNomina5[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= $tablaNomina5[$i]["V_DESCRIPCION"] ?></td>
                          <td class="small"><?= $tablaNomina5[$i]["FECHA_ACCIDENTE"] ?></td>
                          <td class="small"><?= $tablaNomina5[$i]["TIPO_RIESGO"] ?></td>
                          <td class="small"><?= $tablaNomina5[$i]["DIAS_SUBSIDIADOS"] ?></td>
                          <td class="small"><?= $tablaNomina5[$i]["PORCENTAJE_INCAPACIDAD"] ?></td>
                          <td class="small"><?= $tablaNomina5[$i]["DEFUNCION"] ?></td>
                          <td class="small"><?= $tablaNomina5[$i]["FECHA_ALTA"] ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

          </div><!--/.box-body-->
        </div>
      </div><!--/.box-body-->
    </div>
    </div><!-- /.col-md-9 -->
  </section>
  <!-- ############################ /.SECCION GRAFICA ############################# -->

  <!-- ############################ TABLA DETALLE DE NOMINA PAGADA ############################# -->
  <section>

  </section>
  <!-- ############################ /.TABLA DETALLE DE NOMINA PAGADA ############################# -->


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
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#activo1').DataTable( {
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
            title: 'Personal Riesgo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal Riesgo',
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

    $('#activo2').DataTable( {
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
              title: 'Personal Riesgo',
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

    $('#activo3').DataTable( {
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
              title: 'Personal Riesgo',
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

    $('#activo4').DataTable( {
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
              title: 'Personal Riesgo',
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
$('.select2').select2()

</script>
<script type="text/javascript">
/*---- SELECT TIPO DE NOMINA ----*/
$('#nomTipo').select2({
  tags: true,
  tokenSeparators: [","]
});
$('#nomTipo').val([<?=$tipo?>]).trigger("change");
/*---- SELECT STATUS NOMINA ----*/
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
/*
*/
$(".btnNomFiltro").on("click", function(){
  fecha = $('input[name="nomFecha"]').val();
  plaza = $('#nomPlaza').val();
  tipo = $('#nomTipo').val();

  url = '?fecha='+fecha+'&plaza='+plaza+'&tipo='+tipo;
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
<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_nomina').DataTable( {
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
            title: 'Nomina Pagada',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Nomina Pagada',
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
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<script type="text/javascript">

$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php for ($i=0; $i <count($graficaNomina) ; $i++) {  ?>
  "<?=$graficaNomina[$i]["N_ANIO"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i <count($graficaNomina) ; $i++) {  ?>
  <?=round($graficaNomina[$i]["N_PORCENTAJE"], 2)?>,
  <?php }  ?>
  ];

  $('#graficaNom').highcharts({
    chart: { type: 'column' },
    title: { text: 'PRIMA DE RIESGO' },
    legend:{
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#c6c2c2'
    },
    yAxis:{
          lineWidth: 2,
          //min: 0,
          /*DIEGO ALTAMIRANO ES LA MERA VERENGENA SI TE TIRO MIS BARRAS SON REALES AUNQUE NO LO CREAS, TU ERES UN RAPER DE PAPEL Y AMI NO ME DAS PELEA. */
          offset: 10,
          tickWidth: 1,
          title: {
            text: 'Porcentaje de riesgo'
          },
          labels:{
                formatter: function () {
                  return this.value;
                }
          }
    },
    tooltip:{
            shared: true,
            valueSuffix: ' RIESGO',
            useHTML: true,
            valueDecimals: 2,
            valuePrefix: '',
    },
    credits:{
            enabled: false
    },
    lang: {
      printChart: 'Imprimir Grafica',
      downloadPNG: 'Descargar PNG',
      downloadJPEG: 'Descargar JPEG',
      downloadPDF: 'Descargar PDF',
      downloadSVG: 'Descargar SVG',
      contextButtonTitle: 'Exportar grafica'
    },
    colors: ['#2f56e0'],
    plotOptions:{
                series: {
                  minPointLength: 3
                }
    },
    xAxis: {
      //tickmarkPlacement: 'on',
      //gridLineWidth: 1,
      categories: categories,
      labels: {
        formatter: function () {
          //url = '?fecha=<?=$fecha?>&plaza='+this.value';
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle: {
      text: '',
      align: 'right',
      x: -10,
    },
    series:[{
            name: ' Porcentaje',
            data: data1,
            }]
    });
});
</script>
<script type="text/javascript">
  $(function(){
    Highcharts.setOptions({lag:{thousandsSep: ','} });
    var categories= [
      <?php for($i=0; $i<count($graficaMensual); $i++){?>
        "<?=$graficaMensual[$i]["NOMBRE_MES"]?>",
      <?php } ?>
    ];
    var data1 = [
      <?php for($i=0; $i<count($graficaMensual); $i++){ ?>
        <?=$graficaMensual[$i]["DIAS_DESCANSADOS"]?>,
      <?php } ?>
    ];

    $('#graficaMensual').highcharts({
      chart:{type: 'line'},
      title:{text: 'Reporte del año <?php echo substr($fecha, 6,4); ?>'},
      legend:{
        y:-40,
        borderWidth:1,
        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#c6c2c2'
      },
      yAxis:{
        lineWidth:2,
        offset:10,
        tickWidth: 1,
        title:{
          text:'DIAS'
        },
        labels:{
          formatter:function(){
            return this.value;
          }
        }
      },
      tooltip:{
        shared:true,
        valueSuffix:'FALTAS',
        useHTML:true,
        valueDecimals:0,
        valuePrefix:'',
      },
      credits:{
        enabled: false
      },
      lang:{
        printChart: 'Imprimir Grafica',
        downloadPNG: 'Descargar PNG',
        downloadJPEG: 'Descargar JPEG',
        downloadPDF: 'Descargar PDF',
        downloadSVG: 'Descargar SVG',
        contextButtonTitle: 'Exportar Grafica'
      },
      colors: ['#179f21'],
      plotOptions:{
        series:{
          minPointLength:3
        }
      },
      xAxis:{
        categories:categories,
        labels:{
          formatter: function () {
            //url = '?fecha=<?=$fecha?>&plaza='+this.value';
            url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>';
              return '<a href="'+url+'">' +
                  this.value + '</a>';
          }
        }
      },
      subtitle:{
        text: '',
        align:'right',
        x:-10,
      },
      series:[{
        name: 'Total Dias',
        data: data1,
      }]
    });
  });
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
