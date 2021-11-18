<?php
include_once '../libs/conOra.php';
require_once ('../libs/spreadsheet-reader-master/SpreadsheetReader.php');
require_once ('../libs/spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
$conn = conexion::conectar();//coneccion
if (isset($_POST["import"]))
{

  $allowedFileType = array('application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

  if(in_array($_FILES["file"]["type"],$allowedFileType)){

        $targetPath = 'uploads/'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

        $Reader = new SpreadsheetReader($targetPath);
        $sheetCount = count($Reader->sheets());
        for($i=0; $i<$sheetCount; $i++)
        {

            $Reader->ChangeSheet($i);

            foreach ($Reader as $Row)
            {

                $id_almacen = 0;
                if(isset($Row[0])) {
                    $id_almacen = $Row[0];
                }

                $id_anio = 0;
                if(isset($Row[1])) {
                    $id_anio = $Row[1];
                }

                $ppto1 = 0;
                if(isset($Row[2])) {
                    $ppto1 = $Row[2];
                }

                $ppto2 = 0;
                if(isset($Row[3])) {
                    $ppto2 = $Row[3];
                }

                $ppto3 = 0;
                if(isset($Row[4])) {
                    $ppto3 = $Row[4];
                }

                $ppto4 = 0;
                if(isset($Row[5])) {
                    $ppto4 = $Row[5];
                }

                $ppto5 = 0;
                if(isset($Row[6])) {
                    $ppto5 = $Row[6];
                }

                $ppto6 = 0;
                if(isset($Row[7])) {
                    $ppto6 = $Row[7];
                }

                $ppto7 = 0;
                if(isset($Row[8])) {
                    $ppto7 = $Row[8];
                }

                $ppto8 = 0;
                if(isset($Row[9])) {
                    $ppto8 = $Row[9];
                }

                $ppto9 = 0;
                if(isset($Row[10])) {
                    $ppto9 = $Row[10];
                }

                $ppto10 = 0;
                if(isset($Row[11])) {
                    $ppto10 = $Row[11];
                }

                $ppto11 = 0;
                if(isset($Row[12])) {
                    $ppto11 = $Row[12];
                }

                $ppto12 = 0;
                if(isset($Row[13])) {
                    $ppto12 = $Row[13];
                }

                if (!empty($id_almacen) || !empty($id_anio) ) {
                  //QUERY CONSULTA
                    $consulta = "SELECT COUNT(*)AS ID FROM AD_FA_REP_PRES_ALMACEN WHERE IID_ALMACEN = ".$id_almacen." AND ANIO = ".$id_anio."";
                    //SE PARSEA
                    #echo $consulta;
                    $still = oci_parse($conn, $consulta);
                    oci_execute($still);
                    while (oci_fetch($still)) {
                      $reg = oci_result($still, "ID");
                      if( $reg > 0){
                        $type = "warning";
                        $message = "El archivo ya se exporto anteriormente!!!";
                      }
                      else {
                    #  echo "prueba llego al = 0";
                      ###################                     PRESUPUESTO 1                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 1 , ".$ppto1.")";
                      $sti = oci_parse($conn , $query);
                      $lanza = oci_execute($sti);
                      ###################                     PRESUPUESTO 2                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 2 , ".$ppto2.")";
                      $sti = oci_parse($conn , $query);
                      $lanza2 = oci_execute($sti);
                      ###################                     PRESUPUESTO 3                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 3 , ".$ppto3.")";
                      $sti = oci_parse($conn , $query);
                      $lanza3 = oci_execute($sti);
                      ###################                     PRESUPUESTO 4                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 4 , ".$ppto4.")";
                      $sti = oci_parse($conn , $query);
                      $lanza4 = oci_execute($sti);
                      ###################                     PRESUPUESTO 5                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 5 , ".$ppto5.")";
                      $sti = oci_parse($conn , $query);
                      $lanza5 = oci_execute($sti);
                      ###################                     PRESUPUESTO 6                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 6 , ".$ppto6.")";
                      $sti = oci_parse($conn , $query);
                      $lanza6 = oci_execute($sti);
                      ###################                     PRESUPUESTO 7                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 7 , ".$ppto7.")";
                      $sti = oci_parse($conn , $query);
                      $lanza7 = oci_execute($sti);
                      ###################                     PRESUPUESTO 8                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 8 , ".$ppto8.")";
                      $sti = oci_parse($conn , $query);
                      $lanza8 = oci_execute($sti);
                      ###################                     PRESUPUESTO 9                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 9 , ".$ppto9.")";
                      $sti = oci_parse($conn , $query);
                      $lanza9 = oci_execute($sti);
                      ###################                     PRESUPUESTO 10                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 10 , ".$ppto10.")";
                      $sti = oci_parse($conn , $query);
                      $lanza10 = oci_execute($sti);
                      ###################                     PRESUPUESTO 11                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 11 , ".$ppto11.")";
                      $sti = oci_parse($conn , $query);
                      $lanza11 = oci_execute($sti);
                      ###################                     PRESUPUESTO 12                 ####################################
                      $query = "INSERT INTO AD_FA_REP_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES1, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 12 , ".$ppto12.")";
                      $sti = oci_parse($conn , $query);
                      $lanza12 = oci_execute($sti);

                      if (!empty($lanza) || !empty($lanza2) || !empty($lanza3) || !empty($lanza4) || !empty($lanza5) || !empty($lanza6) || !empty($lanza7) || !empty($lanza8) || !empty($lanza9) || !empty($lanza10) || !empty($lanza11) || !empty($lanza12) ) {
                          $type = "success";
                          $message = "Excel Importado Correctamente!!!";
                      } else {
                          $type = "error";
                          $message = "Problema Importando Excel!!!";
                      }
                    }
                  }
                }
             }

         }
  }
  else
  {
        $type = "error";
        $message = "Invalid File Type. Upload Excel File.";
  }
}
?>

<?php
//BY DAS 12/12/2019

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  //header("location:detalles_ingresos.php");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 37);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/detalles_ingresosR.php';
$modelNomina = new NominaPagada();
//SQL ULTIMA FECHA DE CORTE
$fec_corte = $modelNomina->sql(5,null, null);
/*----- GET FECHA -----*/
$fecha = $fec_corte[0]["MES1"];
if( isset($_GET["fecha"]) ){
    $fecha = $_GET["fecha"];
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
/*if( isset($_GET["almacen"]) ){
  if( $_GET["plaza"] == "CORPORATIVO" || $_GET["plaza"] == "CÓRDOBA" || $_GET["plaza"] == "MÉXICO" || $_GET["plaza"] == "GOLFO" || $_GET["plaza"] == "PENINSULA" || $_GET["plaza"] == "PUEBLA" || $_GET["plaza"] == "BAJIO" || $_GET["plaza"] == "OCCIDENTE" || $_GET["plaza"] == "NORESTE" ){
    $plaza = $_GET["plaza"];
  }else{
    $plaza = "ALL";
  }
}*/

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

$mesIni = substr($fecha, 0, 2);
$anioIni = substr($fecha, 3, 4);
$anioIni2 = IntVal($anioIni)-1;

switch ($mesIni) {
  case '01':
    $nombreMes = "ENERO";
    $nombreMesAnt = "DICIEMBRE";
    break;
  case '02':
    $nombreMes = "FEBRERO";
    $nombreMesAnt = "ENERO";
    break;
  case '03':
    $nombreMes = "MARZO";
    $nombreMesAnt = "FEBRERO";
    break;
  case '04':
    $nombreMes = "ABRIL";
    $nombreMesAnt = "MARZO";
    break;
  case '05':
    $nombreMes = "MAYO";
    $nombreMesAnt = "ABRIL";
    break;
  case '06':
    $nombreMes = "JUNIO";
    $nombreMesAnt = "MAYO";
    break;
  case '07':
    $nombreMes = "JULIO";
    $nombreMesAnt = "JUNIO";
    break;
  case '08':
    $nombreMes = "AGOSTO";
    $nombreMesAnt = "JULIO";
    break;
  case '09':
    $nombreMes = "SEPTIEMBRE";
    $nombreMesAnt = "AGOSTO";
    break;
  case '10':
    $nombreMes = "OCTUBRE";
    $nombreMesAnt = "SEPTIEMBRE";
    break;
  case '11':
    $nombreMes = "NOVIEMBRE";
    $nombreMesAnt = "OCTUBRE";
    break;
  case '12':
    $nombreMes = "DICIEMBRE";
    $nombreMesAnt = "NOVIEMBRE";
    break;
  default:
    // code...
    break;
}
//$selectAlmacen = $modelNomina->almacenSql($plaza);
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- Select2 -->
<link rel="stylesheet" href="../plugins/select2/select2.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">}

<!--STYLE DE BOTON DE SUBIDA --->
<style media="screen">
.subir{
  padding: 5px 10px;
  background: #f55d3e;
  color:#fff;
  border:0px solid #fff;
}

.subir:hover{
  color:#fff;
  background: #f29f3e;
}
.outer-container {
	background: #F0F0F0;
	border: #e0dfdf 1px solid;
	padding: 40px 20px;
	border-radius: 2px;
}

.btn-submit {
	background: #333;
	border: #1d1d1d 1px solid;
    border-radius: 2px;
	color: #f0f0f0;
	cursor: pointer;
    padding: 5px 20px;
    font-size:0.9em;
}

#response {
    padding: 10px;
    margin-top: 10px;
    border-radius: 2px;
    display:none;
}

.success {
    background: #c7efd9;
    border: #bbe2cd 1px solid;
}

.warning{
  background: #b3a258;
  border: #c8bd26 1px solid;
}
.error {
    background: #fbcfcf;
    border: #f3c6c7 1px solid;
}

div#response.display-block {
    display: block;
}

</style>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
  <section class="content-header">
    <h1>Dashboard<small>RESUMEN INGRESOS</small></h1>
  </section>

  <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
  <!-- ############################ SECCION GRAFICA ############################# -->
  <section>

    <div class="row"><!-- row -->

    <div class="col-md-9"><!-- col-md-9 -->
    <div class="box box-primary">
      <div class="box-body"><!--box-body-->

        <div class="row">

          <div class="col-md-12">

            <div class="nav-tabs-custom">

              <ul class="nav nav-pills" id="myTab">
                <li class="active"><a href="#tab_corporativo" data-toggle="tab"><i class="fa fa-money"></i> INGRESOS DEL MES <?= $nombreMes ?>
                  <span data-toggle="tooltip" title="" class="badge bg-verde" ></span></a>
                </li>
                <li><a href="#tab_cordoba" data-toggle="tab"><i class="fa fa-money"></i> INGRESOS DEL MES <?= $nombreMesAnt ?>
                  <span data-toggle="tooltip" title="" class="badge bg-verde" ></span></a>
                </li>
                <li><a href="#tab_acumulado" data-toggle="tab"><i class="fa fa-money"></i> INGRESOS ACUMULADOS DEL MES DE  <?= $nombreMes ?>
                  <span data-toggle = "tooltip" title="" class="badge bg-verde"></span></a>
                </li>
                <li><a href="#tab_comparativo" data-toggle="tab"><i class="fa fa-money"></i> INGRESOS COMPARATIVO <?php echo $anioIni."  VS  ".$anioIni2; ?>
                  <span data-toggle = "tooltip" title="" class="badge bg-verde"></span></a>
                </li>
                <li><a href="#tab_comparativoanterior" data-toggle="tab"><i class="fa fa-money"></i> INGRESOS COMPARATIVO <?php echo $nombreMes."  VS  ".$nombreMesAnt; ?>
                  <span data-toggle = "tooltip" title="" class="badge bg-verde"></span></a>
                </li>
              </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_corporativo">
                <?php /*if ($_SESSION['usuario'] == 'diego13' || $_SESSION['usuario'] == 'david' || $_SESSION['usuario'] == 'fernando_s' ) { */?>
                  <section>
                <div class="box box-success">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-table"></i> RESUMEN INGRESOS  <?php echo $nombreMes."  ".$anioIni; ?></h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body"><!--box-body-->

                    <!--TABULATOR-->

                    <div class="table-responsive" id="container">
                      <table id="tabla_nomina_real" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th class="small" colspan="4" style="text-align:center;"> REPORTE DE INGRESOS <?php echo $nombreMes."  ".$anioIni; ?></th>
                          </tr>
                          <tr>
                            <th class="small" colspan="4" style="text-align:center; " bgcolor="#000080"><font color="white"> CONSOLIDADO</font> </th>
                          </tr>
                          <tr>
                            <th class="small" bgcolor="#ffffff"><font color="black">RAZON SOCIAL</font></th>
                            <th class="small" bgcolor="#ffffff"><font color="black">PRESUPUESTO</font></th>
                            <th class="small" bgcolor="#ffffff"><font color="black">INGRESOS</font></th>
                            <th class="small" bgcolor="#ffffff"><font color="black">CUMPLIMIENTO</font></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
                          <tr>
                            <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
                            <td class="small"><?= number_format($tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
                            <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
                            <td class="small"><?php if ($tabla_Ingresos[$i]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]/$tabla_Ingresos[$i]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                          </tr>
                          <?php } ?>
                          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
                          <tr>
                            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                            <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                            <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                          </tr>
                          <?php } ?>
                          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
                          <tr>
                            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                            <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                            <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                          </tr>
                          <?php } ?>
                          <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos($fecha, 4)) ; $x++) {?> <!-- GDL -->
                                <tr>
                                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                  <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                  <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                                </tr>
                          <?php } ?>
                          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
                                <tr>
                                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                  <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                  <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                                </tr>
                          <?php } ?>
                          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
                                <tr>
                                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                  <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                  <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                                </tr>
                          <?php } ?>
                          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
                                <tr>
                                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                  <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                  <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                                </tr>
                          <?php } ?>
                          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos($fecha, 8)) ; $x++) {?> <!-- MEXICO -->
                                <tr>
                                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                  <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                  <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                                </tr>
                          <?php } ?>
                        </tbody>
                        <tfoot style="background-color: #ffffff; color:#000000 ">
                           <tr>
                               <th style="text-align:right">Total:</th>
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
              <?php /*}*/ ?>

                <section>
              <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE INGRESOS DESGLOSADO  <?php echo $nombreMes."  ".$anioIni; ?></h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body"><!--box-body-->

                  <!--Table 2 LISTA FALTA PRESUPUESTO  -->
                  <div class="table-responsive" id="container">
                    <table id="tabla_nomina_real2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="small" colspan="4" style="text-align:center;"> REPORTE DE INGRESOS DESGLOSADOS <?php echo $nombreMes."  ".$anioIni; ?></th>
                        </tr>
                        <tr>
                          <th class="small" colspan="4" style="text-align:center; " bgcolor="#000080"><font color="white"> BODEGAS DIRECTAS </font> </th>
                        </tr>
                        <tr>
                          <th class="small" bgcolor="#FFFFFF"><font color="black">RAZON SOCIAL</font></th>
                          <th class="small" bgcolor="#FFFFFF"><font color="black">PRESUPUESTO</font></th>
                          <th class="small" bgcolor="#FFFFFF"><font color="black">INGRESOS</font></th>
                          <th class="small" bgcolor="#FFFFFF"><font color="black">CUMPLIMIENTO</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$i]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]/$tabla_Ingresos[$i]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                        <?php } ?>
                        <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 4)) ; $x++) {?> <!-- GDL -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 8)) ; $x++) {?> <!-- MTY -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 9)) ; $x++) {?> <!-- MTY -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 10)) ; $x++) {?> <!-- MTY -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 11)) ; $x++) {?> <!-- MTY -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 12)) ; $x++) {?> <!-- MTY -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 13)) ; $x++) {?> <!-- MTY -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 14)) ; $x++) {?> <!-- MTY -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 15)) ; $x++) {?> <!-- MTY -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos2($fecha, 16)) ; $x++) {?> <!-- MTY -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"],2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                      </tbody>
                      <tfoot style="background-color: #ffffff; color:#000000 ">
                         <tr>
                             <th style="text-align:right">Total:</th>
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
                  <h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE INGRESOS DESGLOSADO <?php echo $nombreMes."  ".$anioIni; ?></h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body"><!--box-body-->

                  <!--Table 2 LISTA FALTA PRESUPUESTO  -->
                  <div class="table-responsive" id="container">
                    <table id="tabla_nomina_real3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="small" colspan="4" style="text-align:center;"> REPORTE DE INGRESOS DESGLOSADOS <?php echo $nombreMes."  ".$anioIni; ?></th>
                        </tr>
                        <tr>
                          <th class="small" colspan="4" style="text-align:center; " bgcolor="#000080"><font color="white"> BODEGAS HABILITADAS Y PROYECTOS</font> </th>
                        </tr>
                        <tr>
                          <th class="small" bgcolor="#FFFFFF"><font color="black">RAZON SOCIAL</font></th>
                          <th class="small" bgcolor="#FFFFFF"><font color="black">PRESUPUESTO</font></th>
                          <th class="small" bgcolor="#FFFFFF"><font color="black">INGRESOS</font></th>
                          <th class="small" bgcolor="#FFFFFF"><font color="black">CUMPLIMIENTO</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos3($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$i]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]/$tabla_Ingresos[$i]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos3($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos3($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                        <?php } ?>
                        <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos3($fecha, 4)) ; $x++) {?> <!-- GDL -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos3($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos3($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos3($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                        <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos3($fecha, 8)) ; $x++) {?> <!-- MEXICO -->
                              <tr>
                                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                              </tr>
                        <?php } ?>
                      </tbody>
                      <tfoot style="background-color: #ffffff; color:#000000 ">
                         <tr>
                             <th style="text-align:right">Total:</th>
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

            <div class="tab-pane" id="tab_cordoba">
              <section>
            <div class="box box-success">
              <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-table"></i> RESUMEN INGRESOS <?php if ($nombreMesAnt == "DICIEMBRE") { echo $nombreMesAnt."  ".$anioIni2;}else {echo $nombreMesAnt."  ".$anioIni;} ?></h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body"><!--box-body-->

                <!--TABULATOR-->

                <div class="table-responsive" id="container">
                  <table id="tabla_nomina_real4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                      <tr>
                        <th class="small" colspan="4" style="text-align:center;"> REPORTE DE INGRESOS <?php if ($nombreMesAnt == "DICIEMBRE") { echo $nombreMesAnt."  ".$anioIni2;}else {echo $nombreMesAnt."  ".$anioIni;} ?> </th>
                      </tr>
                      <tr>
                        <th class="small" colspan="4" style="text-align:center; " bgcolor="#000080"><font color="white"> CONSOLIDADO</font> </th>
                      </tr>
                      <tr>
                        <th class="small" bgcolor="#ffffff"><font color="black">RAZON SOCIAL</font></th>
                        <th class="small" bgcolor="#ffffff"><font color="black">PRESUPUESTO</font></th>
                        <th class="small" bgcolor="#ffffff"><font color="black">INGRESOS</font></th>
                        <th class="small" bgcolor="#ffffff"><font color="black">CUMPLIMIENTO</font></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos4($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
                      <tr>
                        <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
                        <td class="small"><?= number_format($tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
                        <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
                        <td class="small"><?php if ($tabla_Ingresos[$i]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]/$tabla_Ingresos[$i]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                      </tr>
                      <?php } ?>
                      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos4($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
                      <tr>
                        <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                        <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                        <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                      </tr>
                      <?php } ?>
                      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos4($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
                      <tr>
                        <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                        <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                        <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                      </tr>
                      <?php } ?>
                      <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos4($fecha, 4)) ; $x++) {?> <!-- GDL -->
                            <tr>
                              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                              <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                            </tr>
                      <?php } ?>
                      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos4($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
                            <tr>
                              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                              <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                            </tr>
                      <?php } ?>
                      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos4($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
                            <tr>
                              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                              <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                            </tr>
                      <?php } ?>
                      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos4($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
                            <tr>
                              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                              <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                            </tr>
                      <?php } ?>
                      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos4($fecha, 8)) ; $x++) {?> <!-- MTY -->
                            <tr>
                              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                              <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                            </tr>
                      <?php } ?>
                    </tbody>
                    <tfoot style="background-color: #ffffff; color:#000000 ">
                       <tr>
                           <th style="text-align:right">Total:</th>
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
          <!--mES ANTERIOR -.-->
          <section>
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE INGRESOS DESGLOSADO <?php if ($nombreMesAnt == "DICIEMBRE") { echo $nombreMesAnt."  ".$anioIni2;}else {echo $nombreMesAnt."  ".$anioIni;} ?></h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body"><!--box-body-->

            <!--Table 2 LISTA FALTA PRESUPUESTO  -->
            <div class="table-responsive" id="container">
              <table id="tabla_nomina_real5" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" colspan="4" style="text-align:center;"> REPORTE DE INGRESOS DESGLOSADOS <?php if ($nombreMesAnt == "DICIEMBRE") { echo $nombreMesAnt."  ".$anioIni2;}else {echo $nombreMesAnt."  ".$anioIni;} ?></th>
                  </tr>
                  <tr>
                    <th class="small" colspan="4" style="text-align:center; " bgcolor="#000080"><font color="white"> BODEGAS DIRECTAS </font> </th>
                  </tr>
                  <tr>
                    <th class="small" bgcolor="#FFFFFF"><font color="black">RAZON SOCIAL</font></th>
                    <th class="small" bgcolor="#FFFFFF"><font color="black">PRESUPUESTO</font></th>
                    <th class="small" bgcolor="#FFFFFF"><font color="black">INGRESOS</font></th>
                    <th class="small" bgcolor="#FFFFFF"><font color="black">CUMPLIMIENTO</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
                  <tr>
                    <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
                    <td class="small"><?= number_format($tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
                    <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
                    <td class="small"><?php if ($tabla_Ingresos[$i]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]/$tabla_Ingresos[$i]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                  </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
                  <tr>
                    <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                    <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                    <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                    <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                  </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
                  <tr>
                    <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                    <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                    <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                    <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                  </tr>
                  <?php } ?>
                  <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 4)) ; $x++) {?> <!-- GDL -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 8)) ; $x++) {?> <!-- MTY -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 9)) ; $x++) {?> <!-- MTY -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 10)) ; $x++) {?> <!-- MTY -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 11)) ; $x++) {?> <!-- MTY -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 12)) ; $x++) {?> <!-- MTY -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 13)) ; $x++) {?> <!-- MTY -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 14)) ; $x++) {?> <!-- MTY -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 15)) ; $x++) {?> <!-- MTY -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos5($fecha, 16)) ; $x++) {?> <!-- MTY -->
                        <tr>
                          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                          <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                        </tr>
                  <?php } ?>
                </tbody>
                <tfoot style="background-color: #ffffff; color:#000000 ">
                   <tr>
                       <th style="text-align:right">Total:</th>
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
        <h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE INGRESOS DESGLOSADO  <?php if ($nombreMesAnt == "DICIEMBRE") { echo $nombreMesAnt."  ".$anioIni2;}else {echo $nombreMesAnt."  ".$anioIni;} ?></h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <!--Table 2 LISTA FALTA PRESUPUESTO  -->
        <div class="table-responsive" id="container">
          <table id="tabla_nomina_real6" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" colspan="4" style="text-align:center;"> REPORTE DE INGRESOS DESGLOSADOS  <?php if ($nombreMesAnt == "DICIEMBRE") { echo $nombreMesAnt."  ".$anioIni2;}else {echo $nombreMesAnt."  ".$anioIni;} ?></th>
              </tr>
              <tr>
                <th class="small" colspan="4" style="text-align:center; " bgcolor="#000080"><font color="white"> BODEGAS HABILITADAS Y PROYECTOS</font> </th>
              </tr>
              <tr>
                <th class="small" bgcolor="#FFFFFF"><font color="black">RAZON SOCIAL</font></th>
                <th class="small" bgcolor="#FFFFFF"><font color="black">PRESUPUESTO</font></th>
                <th class="small" bgcolor="#FFFFFF"><font color="black">INGRESOS</font></th>
                <th class="small" bgcolor="#FFFFFF"><font color="black">CUMPLIMIENTO</font></th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos6($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
              <tr>
                <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
                <td class="small"><?= number_format($tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
                <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
                <td class="small"><?php if ($tabla_Ingresos[$i]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]/$tabla_Ingresos[$i]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
              </tr>
              <?php } ?>
              <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos6($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
              <tr>
                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
              </tr>
              <?php } ?>
              <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos6($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
              <tr>
                <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
              </tr>
              <?php } ?>
              <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos6($fecha, 4)) ; $x++) {?> <!-- GDL -->
                    <tr>
                      <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                      <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                      <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                      <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                    </tr>
              <?php } ?>
              <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos6($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
                    <tr>
                      <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                      <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                      <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                      <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                    </tr>
              <?php } ?>
              <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos6($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
                    <tr>
                      <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                      <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                      <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                      <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                    </tr>
              <?php } ?>
              <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos6($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
                    <tr>
                      <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                      <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                      <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                      <td class="small"><?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] <> 0) { echo number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2);   } else { echo "100";      }  ?></td>
                    </tr>
              <?php } ?>
            </tbody>
            <tfoot style="background-color: #ffffff; color:#000000 ">
               <tr>
                   <th style="text-align:right">Total:</th>
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

<!--comparativo-->
<div class="tab-pane" id="tab_acumulado">
  <section>
<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-table"></i> RESUMEN INGRESOS ACUMULADOS <?php echo $nombreMes."  ".$anioIni; ?></h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body"><!--box-body-->

    <!--TABULATOR-->

    <div class="table-responsive" id="container">
      <table id="tabla_nomina_real7" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small" colspan="5" style="text-align:center;"> REPORTE DE INGRESOS ACUMULADO A  <?php echo $nombreMes."  ".$anioIni; ?></th>
          </tr>
          <tr>
            <th class="small" colspan="5" style="text-align:center; " bgcolor="#000080"><font color="white"> CONSOLIDADO</font> </th>
          </tr>
          <tr>
            <th class="small" bgcolor="#ffffff"><font color="black">RAZON SOCIAL</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">PRESUPUESTO</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">ACUMULADO</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">DIFERENCIA</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">%INCREMENTO</font></th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos7($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
            <?php if ($tabla_Ingresos[$i]["PRESUPUESTO"] == 0) { ?>
                <td class="small">%100</td>
            <?php } else{ ?>
                <td class="small"><?= number_format((($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["PRESUPUESTO"])/$tabla_Ingresos[$i]["PRESUPUESTO"])*100, 2) ?></td>
            <?php } ?>
          </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos7($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                <td class="small">%100</td>
            <?php } else{ ?>
                <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
            <?php } ?>
          </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos7($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                <td class="small">%100</td>
            <?php } else{ ?>
                <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
            <?php } ?>
          </tr>
          <?php } ?>
          <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos7($fecha, 4)) ; $x++) {?> <!-- GDL -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                  <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                      <td class="small">%100</td>
                  <?php } else{ ?>
                      <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
                  <?php } ?>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos7($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                  <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                      <td class="small">%100</td>
                  <?php } else{ ?>
                      <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
                  <?php } ?>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos7($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                  <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                      <td class="small">%100</td>
                  <?php } else{ ?>
                      <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
                  <?php } ?>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos7($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                  <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                      <td class="small">%100</td>
                  <?php } else{ ?>
                      <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
                  <?php } ?>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos7($fecha, 8)) ; $x++) {?> <!-- MTY -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
                  <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                      <td class="small">%100</td>
                  <?php } else{ ?>
                      <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
                  <?php } ?>
                </tr>
          <?php } ?>
        </tbody>
        <tfoot style="background-color: #ffffff; color:#000000 ">
           <tr>
               <th style="text-align:right">Total:</th>
               <th></th>
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
<!--mES ANTERIOR -.-->
<section>
<div class="box box-success">
<div class="box-header with-border">
<h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE INGRESOS ACUMULADOS A <?php echo $nombreMes."  ".$anioIni; ?></h3>
<div class="box-tools pull-right">
  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
</div>
</div>
<div class="box-body"><!--box-body-->

<!--Table 2 LISTA FALTA PRESUPUESTO  -->
<div class="table-responsive" id="container">
  <table id="tabla_nomina_real8" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
      <tr>
        <th class="small" colspan="5" style="text-align:center;"> REPORTE DE INGRESOS ACUMULADOS A <?php echo $nombreMes."  ".$anioIni; ?> </th>
      </tr>
      <tr>
        <th class="small" colspan="5" style="text-align:center; " bgcolor="#000080"><font color="white"> BODEGAS DIRECTAS </font> </th>
      </tr>
      <tr>
        <th class="small" bgcolor="#FFFFFF"><font color="black">RAZON SOCIAL</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">PRESUPUESTO</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">ACUMULADO</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">DIFERENCIA</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">% INCREMENTO</font></th>
      </tr>
    </thead>
    <tbody>
      <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
        <?php if ($tabla_Ingresos[$i]["PRESUPUESTO"] == 0) { ?>
            <td class="small">%100</td>
        <?php } else{ ?>
            <td class="small"><?= number_format((($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["PRESUPUESTO"])/$tabla_Ingresos[$i]["PRESUPUESTO"])*100, 2) ?></td>
        <?php } ?>
      </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
        <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
            <td class="small">%100</td>
        <?php } else{ ?>
          <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
        <?php } ?>
      </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
        <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
            <td class="small">%100</td>
        <?php } else{ ?>
            <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
        <?php } ?>
      </tr>
      <?php } ?>
      <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 4)) ; $x++) {?> <!-- GDL -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 8)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 9)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 10)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 11)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 12)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 13)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 14)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 15)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos8($fecha, 16)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                  <td class="small">%100</td>
              <?php } else{ ?>
                  <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
              <?php } ?>
            </tr>
      <?php } ?>
    </tbody>
    <tfoot style="background-color: #ffffff; color:#000000 ">
       <tr>
           <th style="text-align:right">Total:</th>
           <th></th>
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
<h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE INGRESOS ACUMULADO A <?php echo $nombreMes."  ".$anioIni; ?></h3>
<div class="box-tools pull-right">
<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
</div>
</div>
<div class="box-body"><!--box-body-->

<!--Table 2 LISTA FALTA PRESUPUESTO  -->
<div class="table-responsive" id="container">
  <table id="tabla_nomina_real9" class="table table-striped table-bordered" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="small" colspan="5" style="text-align:center;"> REPORTE DE INGRESOS ACUMULADOS A <?php echo $nombreMes."  ".$anioIni; ?></th>
    </tr>
    <tr>
      <th class="small" colspan="5" style="text-align:center; " bgcolor="#000080"><font color="white"> BODEGAS HABILITADAS Y PROYECTOS</font> </th>
    </tr>
    <tr>
      <th class="small" bgcolor="#FFFFFF"><font color="black">RAZON SOCIAL</font></th>
      <th class="small" bgcolor="#FFFFFF"><font color="black">PRESUPUESTO</font></th>
      <th class="small" bgcolor="#FFFFFF"><font color="black">ACUMULADO</font></th>
      <th class="small" bgcolor="#FFFFFF"><font color="black">DIFERENCIA</font></th>
      <th class="small" bgcolor="#FFFFFF"><font color="black">%INCREMENTO</font></th>
    </tr>
  </thead>
  <tbody>
    <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos9($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
    <tr>
      <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
      <td class="small"><?= number_format($tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
      <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
      <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["PRESUPUESTO"], 2) ?></td>
      <?php if ($tabla_Ingresos[$i]["PRESUPUESTO"] == 0) { ?>
          <td class="small">%100</td>
      <?php } else{ ?>
          <td class="small"><?= number_format((($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["PRESUPUESTO"])/$tabla_Ingresos[$i]["PRESUPUESTO"])*100, 2) ?></td>
      <?php } ?>
    </tr>
    <?php } ?>
    <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos9($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
    <tr>
      <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
      <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
      <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
      <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
      <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
          <td class="small">%100</td>
      <?php } else{ ?>
        <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
      <?php } ?>
    </tr>
    <?php } ?>
    <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos9($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
    <tr>
      <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
      <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
      <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
      <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
      <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
          <td class="small">%100</td>
      <?php } else{ ?>
          <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
      <?php } ?>
    </tr>
    <?php } ?>
    <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos9($fecha, 4)) ; $x++) {?> <!-- GDL -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                <td class="small">%100</td>
            <?php } else{ ?>
                <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
            <?php } ?>
          </tr>
    <?php } ?>
    <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos9($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                <td class="small">%100</td>
            <?php } else{ ?>
                <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
            <?php } ?>
          </tr>
    <?php } ?>
    <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos9($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                <td class="small">%100</td>
            <?php } else{ ?>
                <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
            <?php } ?>
          </tr>
    <?php } ?>
    <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos9($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"], 2) ?></td>
            <?php if ($tabla_Ingresos[$x]["PRESUPUESTO"] == 0) { ?>
                <td class="small">%100</td>
            <?php } else{ ?>
                <td class="small"><?= number_format((($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["PRESUPUESTO"])/$tabla_Ingresos[$x]["PRESUPUESTO"])*100, 2) ?></td>
            <?php } ?>
          </tr>
    <?php } ?>
  </tbody>
  <tfoot style="background-color: #ffffff; color:#000000 ">
     <tr>
         <th style="text-align:right">Total:</th>
         <th></th>
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

<!--COMPARATIVO CON EL AÑO PASADO-->
<div class="tab-pane" id="tab_comparativo">
  <section>
<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-table"></i> RESUMEN INGRESOS COMPARATIVO <?php echo $nombreMes."  ".$anioIni. "  VS ".$nombreMes."  ".$anioIni2; ?></h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body"><!--box-body-->

    <!--TABULATOR-->

    <div class="table-responsive" id="container">
      <table id="tabla_nomina_real10" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small" colspan="5" style="text-align:center;"> REPORTE DE INGRESOS <?php echo $nombreMes."  ".$anioIni. "  VS ".$nombreMes."  ".$anioIni2; ?></th>
          </tr>
          <tr>
            <th class="small" colspan="5" style="text-align:center; " bgcolor="#000080"><font color="white"> CONSOLIDADO</font> </th>
          </tr>
          <tr>
            <th class="small" bgcolor="#ffffff"><font color="black">RAZON SOCIAL</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">AÑO ANTERIOR</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">AÑO ACTUAL</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">DIFERENCIA</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">%</font></th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos10($fecha, 1)) ; $i++) {  ?> <!-- QUERETARO -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2) ?></td>
            <td class="small">% <?= number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2)*100 ?></td>
          </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos10($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
            <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
          </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos10($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
            <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
          </tr>
          <?php } ?>
          <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos10($fecha, 4)) ; $x++) {?> <!-- GDL -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos10($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos10($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos10($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos10($fecha, 8)) ; $x++) {?> <!-- MTY -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
                </tr>
          <?php } ?>
        </tbody>
        <tfoot style="background-color: #ffffff; color:#000000 ">
           <tr>
               <th style="text-align:right">Total:</th>
               <th></th>
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
<!--mES ANTERIOR -.-->
<section>
<div class="box box-success">
<div class="box-header with-border">
<h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE INGRESOS COMPARATIVO  <?php echo $nombreMes."  ".$anioIni. "  VS ".$nombreMes."  ".$anioIni2; ?></h3>
<div class="box-tools pull-right">
  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
</div>
</div>
<div class="box-body"><!--box-body-->

<!--Table 2 LISTA FALTA PRESUPUESTO  -->
<div class="table-responsive" id="container">
  <table id="tabla_nomina_real11" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
      <tr>
        <th class="small" colspan="5" style="text-align:center;"> REPORTE DE INGRESOS COMPARATIVO <?php echo $nombreMes."  ".$anioIni. "  VS ".$nombreMes."  ".$anioIni2; ?></th>
      </tr>
      <tr>
        <th class="small" colspan="5" style="text-align:center; " bgcolor="#000080"><font color="white"> BODEGAS DIRECTAS </font> </th>
      </tr>
      <tr>
        <th class="small" bgcolor="#FFFFFF"><font color="black">RAZON SOCIAL</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">AÑO ANTERIOR</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">AÑO ACTUAL</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">DIFERENCIA</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">%</font></th>
      </tr>
    </thead>
    <tbody>
      <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"] , 2) ?></td>
        <?php if ($tabla_Ingresos[$i]["ANIO_ANTERIOR"] == 0) { ?>
            <td class="small">% <?= "100%" ?></td>
        <?PHP } elseif ($tabla_Ingresos[$i]["TOTAL_FACT"] == 0 )  { ?>
            <td class="small">-%100</td>
        <?php } else { ?>
            <td class="small">% <?= number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2)*100 ?></td>
        <?php  }?>
      </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
        <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
            <td class="small">% <?= "100%" ?></td>
        <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
            <td class="small">-%100</td>
        <?php } else { ?>
            <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
        <?php  }?>
      </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
        <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
            <td class="small">% <?= "100%" ?></td>
        <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
            <td class="small">-%100</td>
        <?php } else { ?>
            <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
        <?php  }?>
      </tr>
      <?php } ?>
      <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 4)) ; $x++) {?> <!-- GDL -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 8)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 9)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 10)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 11)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 12)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 13)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 14)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 15)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos11($fecha, 16)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
    </tbody>
    <tfoot style="background-color: #ffffff; color:#000000 ">
       <tr>
           <th style="text-align:right">Total:</th>
           <th></th>
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
<h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE INGRESOS DESGLOSADO COMPARATIVO <?php echo $nombreMes."  ".$anioIni. "  VS ".$nombreMes."  ".$anioIni2; ?></h3>
<div class="box-tools pull-right">
<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
</div>
</div>
<div class="box-body"><!--box-body-->

<!--Table 2 LISTA FALTA PRESUPUESTO  -->
<div class="table-responsive" id="container">
<table id="tabla_nomina_real12" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
      <tr>
        <th class="small" colspan="5" style="text-align:center;"> REPORTE DE INGRESOS COMPARATIVO <?php echo $nombreMes."  ".$anioIni. "  VS ".$nombreMes."  ".$anioIni2; ?></th>
      </tr>
      <tr>
        <th class="small" colspan="5" style="text-align:center; " bgcolor="#000080"><font color="white"> BODEGAS HABILITADAS Y PROYECTOS</font> </th>
      </tr>
      <tr>
        <th class="small" bgcolor="#FFFFFF"><font color="black">RAZON SOCIAL</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">AÑO ANTERIOR</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">AÑO ACTUAL</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">DIFERENCIA</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">%</font></th>
      </tr>
    </thead>
    <tbody>
      <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"] , 2) ?></td>
        <?php if ($tabla_Ingresos[$i]["ANIO_ANTERIOR"] == 0) { ?>
            <td class="small">% <?= "100" ?></td>
        <?PHP } elseif ($tabla_Ingresos[$i]["TOTAL_FACT"] == 0 )  { ?>
            <td class="small">-%100</td>
        <?php } else { ?>
            <td class="small">% <?= number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2)*100 ?></td>
        <?php  }?>
      </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
        <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
            <td class="small">% <?= "100" ?></td>
        <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
            <td class="small">-%100</td>
        <?php } else { ?>
            <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
        <?php  }?>
      </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
        <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
            <td class="small">% <?= "100" ?></td>
        <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
            <td class="small">-%100</td>
        <?php } else { ?>
            <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
        <?php  }?>
      </tr>
      <?php } ?>
      <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 4)) ; $x++) {?> <!-- GDL -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
    </tbody>
    <tfoot style="background-color: #ffffff; color:#000000 ">
       <tr>
           <th style="text-align:right">Total:</th>
           <th></th>
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


<!--COMPARATIVO CON EL MES ANTERIOR -->
<div class="tab-pane" id="tab_comparativoanterior">
  <section>
<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-table"></i> RESUMEN INGRESOS</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body"><!--box-body-->

    <!--TABULATOR-->

    <div class="table-responsive" id="container">
      <table id="tabla_nomina_real13" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small" colspan="5" style="text-align:center;"> REPORTE DE INGRESOS </th>
          </tr>
          <tr>
            <th class="small" colspan="5" style="text-align:center; " bgcolor="#000080"><font color="white"> CONSOLIDADO</font> </th>
          </tr>
          <tr>
            <th class="small" bgcolor="#ffffff"><font color="black">RAZON SOCIAL</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">AÑO ANTERIOR</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">AÑO ACTUAL</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">DIFERENCIA</font></th>
            <th class="small" bgcolor="#ffffff"><font color="black">%</font></th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos13($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2) ?></td>
            <td class="small">% <?= number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2)*100 ?></td>
          </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos13($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
            <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
          </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos13($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
          <tr>
            <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
            <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
            <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
          </tr>
          <?php } ?>
          <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos13($fecha, 4)) ; $x++) {?> <!-- GDL -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos13($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos13($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos13($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
                </tr>
          <?php } ?>
          <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos13($fecha, 8)) ; $x++) {?> <!-- MTY -->
                <tr>
                  <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
                  <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
                </tr>
          <?php } ?>
        </tbody>
        <tfoot style="background-color: #ffffff; color:#000000 ">
           <tr>
               <th style="text-align:right">Total:</th>
               <th></th>
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
<!--mES ANTERIOR -.-->
<section>
<div class="box box-success">
<div class="box-header with-border">
<h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE INGRESOS DESGLOSADO</h3>
<div class="box-tools pull-right">
  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
</div>
</div>
<div class="box-body"><!--box-body-->

<!--Table 2 LISTA FALTA PRESUPUESTO  -->
<div class="table-responsive" id="container">
  <table id="tabla_nomina_real14" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
      <tr>
        <th class="small" colspan="5" style="text-align:center;"> REPORTE DE INGRESOS DESGLOSADOS</th>
      </tr>
      <tr>
        <th class="small" colspan="5" style="text-align:center; " bgcolor="#000080"><font color="white"> BODEGAS DIRECTAS </font> </th>
      </tr>
      <tr>
        <th class="small" bgcolor="#FFFFFF"><font color="black">RAZON SOCIAL</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">AÑO ANTERIOR</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">AÑO ACTUAL</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">DIFERENCIA</font></th>
        <th class="small" bgcolor="#FFFFFF"><font color="black">%</font></th>
      </tr>
    </thead>
    <tbody>
      <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"] , 2) ?></td>
        <?php if ($tabla_Ingresos[$i]["ANIO_ANTERIOR"] == 0) { ?>
            <td class="small">% <?= "100%" ?></td>
        <?PHP } elseif ($tabla_Ingresos[$i]["TOTAL_FACT"] == 0 )  { ?>
            <td class="small">-%100</td>
        <?php } else { ?>
            <td class="small">% <?= number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2)*100 ?></td>
        <?php  }?>
      </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
        <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
            <td class="small">% <?= "100%" ?></td>
        <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
            <td class="small">-%100</td>
        <?php } else { ?>
            <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
        <?php  }?>
      </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
      <tr>
        <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
        <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
        <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
            <td class="small">% <?= "100%" ?></td>
        <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
            <td class="small">-%100</td>
        <?php } else { ?>
            <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
        <?php  }?>
      </tr>
      <?php } ?>
      <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 4)) ; $x++) {?> <!-- GDL -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 8)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 9)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 10)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 11)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 12)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 13)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 14)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 15)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100%" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
      <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos14($fecha, 16)) ; $x++) {?> <!-- MTY -->
            <tr>
              <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
              <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
              <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
                  <td class="small">% <?= "100" ?></td>
              <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
                  <td class="small">-%100</td>
              <?php } else { ?>
                  <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
              <?php  }?>
            </tr>
      <?php } ?>
    </tbody>
    <tfoot style="background-color: #ffffff; color:#000000 ">
       <tr>
           <th style="text-align:left">Total:</th>
           <th></th>
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
<h3 class="box-title"><i class="fa fa-table"></i> REPORTE DE INGRESOS DESGLOSADO</h3>
<div class="box-tools pull-right">
<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
</div>
</div>
<div class="box-body"><!--box-body-->

<!--Table 2 LISTA FALTA PRESUPUESTO  -->
<div class="table-responsive" id="container">
<table id="tabla_nomina_real15" class="table table-striped table-bordered" cellspacing="0" width="100%">
<thead>
  <tr>
    <th class="small" colspan="5" style="text-align:center;"> REPORTE DE INGRESOS DESGLOSADOS</th>
  </tr>
  <tr>
    <th class="small" colspan="5" style="text-align:center; " bgcolor="#000080"><font color="white"> BODEGAS HABILITADAS Y PROYECTOS</font> </th>
  </tr>
  <tr>
    <th class="small" bgcolor="#FFFFFF"><font color="black">RAZON SOCIAL</font></th>
    <th class="small" bgcolor="#FFFFFF"><font color="black">AÑO ANTERIOR</font></th>
    <th class="small" bgcolor="#FFFFFF"><font color="black">AÑO ACTUAL</font></th>
    <th class="small" bgcolor="#FFFFFF"><font color="black">DIFERENCIA</font></th>
    <th class="small" bgcolor="#FFFFFF"><font color="black">%</font></th>
  </tr>
</thead>
<tbody>
  <?php for ($i=0; $i <count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 1)) ; $i++) { ?> <!-- QUERETARO -->
  <tr>
    <td class="small"><?= $tabla_Ingresos[$i]["V_RAZON_SOCIAL"] ?></td>
    <td class="small"><?= number_format($tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2) ?></td>
    <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"], 2) ?></td>
    <td class="small"><?= number_format($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"] , 2) ?></td>
    <?php if ($tabla_Ingresos[$i]["ANIO_ANTERIOR"] == 0) { ?>
        <td class="small">% <?= "100" ?></td>
    <?PHP } elseif ($tabla_Ingresos[$i]["TOTAL_FACT"] == 0 )  { ?>
        <td class="small">-%100</td>
    <?php } else { ?>
        <td class="small">% <?= number_format(($tabla_Ingresos[$i]["TOTAL_FACT"]-$tabla_Ingresos[$i]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$i]["ANIO_ANTERIOR"], 2)*100 ?></td>
    <?php  }?>
  </tr>
  <?php } ?>
  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 2)) ; $x++) {?> <!-- MERIDA -->
  <tr>
    <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
    <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
    <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
    <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
    <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
        <td class="small">% <?= "100" ?></td>
    <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
        <td class="small">-%100</td>
    <?php } else { ?>
        <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
    <?php  }?>
  </tr>
  <?php } ?>
  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 3)) ; $x++) {?> <!-- PUEBLA -->
  <tr>
    <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
    <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
    <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
    <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
    <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
        <td class="small">% <?= "100" ?></td>
    <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
        <td class="small">-%100</td>
    <?php } else { ?>
        <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
    <?php  }?>
  </tr>
  <?php } ?>
  <?php  for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 4)) ; $x++) {?> <!-- GDL -->
        <tr>
          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
          <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
              <td class="small">% <?= "100" ?></td>
          <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
              <td class="small">-%100</td>
          <?php } else { ?>
              <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
          <?php  }?>
        </tr>
  <?php } ?>
  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 5)) ; $x++) {?> <!-- VERACRUZ -->
        <tr>
          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
          <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
              <td class="small">% <?= "100" ?></td>
          <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
              <td class="small">-%100</td>
          <?php } else { ?>
              <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
          <?php  }?>
        </tr>
  <?php } ?>
  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 6)) ; $x++) {?> <!-- CORDOBA -->
        <tr>
          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
          <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
              <td class="small">% <?= "100" ?></td>
          <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
              <td class="small">-%100</td>
          <?php } else { ?>
              <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
          <?php  }?>
        </tr>
  <?php } ?>
  <?php for ($x=0; $x < count($tabla_Ingresos = $modelNomina->tabla_Ingresos12($fecha, 7)) ; $x++) {?> <!-- MEXICO -->
        <tr>
          <td class="small"><?= $tabla_Ingresos[$x]["V_RAZON_SOCIAL"] ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2) ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"], 2) ?></td>
          <td class="small"><?= number_format($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"] , 2) ?></td>
          <?php if ($tabla_Ingresos[$x]["ANIO_ANTERIOR"] == 0) { ?>
              <td class="small">% <?= "100" ?></td>
          <?PHP } elseif ($tabla_Ingresos[$x]["TOTAL_FACT"] == 0 )  { ?>
              <td class="small">-%100</td>
          <?php } else { ?>
              <td class="small">% <?= number_format(($tabla_Ingresos[$x]["TOTAL_FACT"]-$tabla_Ingresos[$x]["ANIO_ANTERIOR"])/ $tabla_Ingresos[$x]["ANIO_ANTERIOR"], 2)*100 ?></td>
          <?php  }?>
        </tr>
  <?php } ?>
</tbody>
<tfoot style="background-color: #ffffff; color:#000000 ">
   <tr>
       <th style="text-align:right">Total:</th>
       <th></th>
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

          </div>
        </div>

          </div>
          <!--GRAFICA NOMINA POR MES DIEGO ALTAMIRANO SUAREZ-->
        </div>

      </div><!--/.box-body-->
    </div>
    </div><!-- /.col-md-9 -->


    <div class="col-md-3"><!-- col-md-9 -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
        <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
        <a href="detalles_Ingresos.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
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
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>

      </div><!--/.box-body-->

    </div>




    </div><!-- /.col-md-3 -->
      <?php
        $valor_usuario = $_SESSION['usuario'];
        if ($valor_usuario == "angie_cba" || $valor_usuario == "diego13") {
      ?>
      <div class="col-md-3">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-sliders"></i> Subir Excel</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>

          <div class="box-body">

          <div class="outer-container">
              <form action="" method="post"
                  name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
                  <div>
                      <label for="file" class="subir">
                        <i class="fa fa-cloud-upload"></i>Seleciona Archivo
                      </label>
                      <input type="file" name="file" id="file" accept=".xls,.xlsx" style="display: none;" onchange="cambiar()">
                      <div id="info"></div>
                      <button type="submit" id="submit" name="import"class="btn-submit" disabled="disabled"><i class="fa fa-check"></i>Importar</button>
                  </div>

              </form>

          </div>
          <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>
          </div>
        </div>
      </div>
      <?php }  ?>

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
                  depth: 20,/*grosor de sombra 3d*/
                  innerRadius: 60,/*radio dona o pastel*/
                  label: {
                    show: false,
                    radius:2/3,/*0.90 posicion del label con data*/
                    formatter: labelFormatter,
                  },
                }";

$donut_grid =  "hoverable: true,
                clickable: true,
                verticalLines: false,
                horizontalLines: false,";
$donut_legend = "/*labelBoxBorderColor: 'none'*/
                show: true "; //-- PONE LOS LABEL DEL ALDO IZQUIERDO //

$donut_content = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> Toneladas = %y.0 </b>  </div>' ;

$donut_tooltip = "show: false,
      content: '".$donut_content."',
      defaultTheme: true ";
 ?>

 <script type="text/javascript">
 function cambiar(){
     var pdrs = document.getElementById('file').files[0].name;
     document.getElementById('info').innerHTML = pdrs;
     document.getElementById('submit').disabled = false;
 }
 </script>


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
  fil_habilitado = 'off';

  if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?fecha='+fecha;
  }
  else {
    fil_habilitado = 'off';
    url = '?fecha='+fecha;
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
$(document).ready(function() {
    $('#tabla_nomina').DataTable( {
        "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                    var numero  = intVal(a) + intVal(b);
                    return Intl.NumberFormat('es-MX').format(numero);
                    //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                }, 0 );

          total2 = api
                  .column( 4 )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                }, 0 );

                total3 = api
                      .column( 3 )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );
            // Total over this page
            pageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {

                    //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                    var number = intVal(a) + intVal(b);
                    return Intl.NumberFormat('es-MX').format(number);
                }, 0 );

                pageTotal2 = api
                    .column( 4, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {

                        //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                        var number = intVal(a) + intVal(b);
                        return Intl.NumberFormat('es-MX').format(number);
                    }, 0 );


                    pageTotal3 = api
                        .column( 3, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {

                            //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                            var number = intVal(a) + intVal(b);
                            return Intl.NumberFormat('es-MX').format(number);
                        }, 0 );
            // Update footer
            $( api.column( 3 ).footer() ).html(
                //''+pageTotal +' ('+ total +' total)'
                ''+number_format(pageTotal3, 2)
            );

            $( api.column( 4 ).footer() ).html(
                //''+pageTotal +' ('+ total +' total)'
                ''+number_format(pageTotal2, 2)
            );


            $( api.column( 5 ).footer() ).html(
                //''+pageTotal +' ('+ total +' total)'
                '' + number_format(pageTotal, 2)
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

        dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
            buttons: [

              {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                exportOptions: {//muestra/oculta visivilidad de columna
                    columns: ':visible'
                },
                title: 'Ingresos',
              },

              {
                extend: 'print',
                text: '<i class="fa fa-print"></i>',
                titleAttr: 'Imprimir',
                exportOptions: {//muestra/oculta visivilidad de columna
                    columns: ':visible',
                },
                title: 'Ingresos',
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
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
              .column( 1 )
              .data()
              .reduce( function (a, b) {
                  var numero  = intVal(a) + intVal(b);
                  return Intl.NumberFormat().format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

          // Total over this page
          pageTotal = api
              .column( 1, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  //var number_format =   Intl.NumberFormat().format(intVal(a) + intVal(b));
                  var numero  = intVal(a) + intVal(b);
                  return Intl.NumberFormat().format(numero);
                  //return intVal(a) + intVal(b);
              }, 0 );

          // Update footer
          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+pageTotal +' Tonelada'
          );
      },
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
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
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 2 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 4, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 3, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 2, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal, 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
              .column( 5 )
              .data()
              .reduce( function (a, b) {
                  var numero  = intVal(a) + intVal(b);
                  return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 4 )
                  .data()
                  .reduce( function (a, b) {
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 3 )
                      .data()
                      .reduce( function (a, b) {
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );

          // Total over this page
          pageTotal = api
              .column( 5, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  var numero  = intVal(a) + intVal(b);
                  return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 4, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 3, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 5 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal , 2)
          );

          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                         //return 500;
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                //var numero  = intVal(pageTotal2) + intVal(pageTotal3);
                return Intl.NumberFormat('es-MX').format(numero);
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));

                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              //''+number_format(pageTotal, 2)
              ''+number_format((intVal(pageTotal2) / intVal(pageTotal3))*100, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real2').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal2) / intVal(pageTotal3))*100, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real3').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal2) / intVal(pageTotal3))*100, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real4').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal2) / intVal(pageTotal3))*100, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real5').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal2) / intVal(pageTotal3))*100, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real6').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal2) / intVal(pageTotal3))*100, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real7').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal)/intVal(pageTotal3))*100, 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real8').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          $( api.column( 4 ).footer() ).html(
                          //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal)/intVal(pageTotal3))*100, 2)
          );
          // Update footer
          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real9').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer}}
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal)/intVal(pageTotal3))*100, 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real10').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal)/intVal(pageTotal3))*100, 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real11').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal)/intVal(pageTotal3))*100, 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real12').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal)/intVal(pageTotal3))*100, 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real13').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal)/intVal(pageTotal3))*100, 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real14').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal)/intVal(pageTotal3))*100, 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal, 2) + ''
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
    $('#tabla_nomina_real15').DataTable( {
      "ordering": true,
      "searching":true,
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
              .column( 3 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 1 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );


          // Total over this page
          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var numero  = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(numero);
                  //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 2, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                      //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      var numero  = intVal(a) + intVal(b);
                      return Intl.NumberFormat('es-MX').format(numero);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 1, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          //return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          var numero  = intVal(a) + intVal(b);
                          return Intl.NumberFormat('es-MX').format(numero);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format((intVal(pageTotal)/intVal(pageTotal3))*100, 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal, 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2, 2) +''
          );

          $( api.column( 1 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3, 2)
          );

      },
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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
<style media="screen">
  .daterangepicker.single.ltr .ranges { display : block !important; }
</style>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script>
$(function() {
  $('input[name="nomFecha"]').daterangepicker({
     singleDatePicker: true,
     showDropdowns: true,
     locale: {
      format: 'MM/YYYY'
     },
     startDate: '<?= $fecha?>',
     maxYear: parseInt(moment().format('YYYY'),10)}
     , function(start, end, label) {
    var years = moment().diff(start, 'years');
    //alert("You are " + years + " years old!");
  });
  });
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
