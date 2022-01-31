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
                    $consulta = "SELECT COUNT(*)AS ID FROM OP_MF_PRES_ALMACEN WHERE IID_ALMACEN = ".$id_almacen." AND ANIO = ".$id_anio."";
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
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES (".$id_almacen.", ".$id_anio.", '01' , ".$ppto1.")";
                      $sti = oci_parse($conn , $query);
                      $lanza = oci_execute($sti);
                      ###################                     PRESUPUESTO 2                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", '02' , ".$ppto2.")";
                      $sti = oci_parse($conn , $query);
                      $lanza2 = oci_execute($sti);
                      ###################                     PRESUPUESTO 3                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", '03' , ".$ppto3.")";
                      $sti = oci_parse($conn , $query);
                      $lanza3 = oci_execute($sti);
                      ###################                     PRESUPUESTO 4                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", '04' , ".$ppto4.")";
                      $sti = oci_parse($conn , $query);
                      $lanza4 = oci_execute($sti);
                      ###################                     PRESUPUESTO 5                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", '05' , ".$ppto5.")";
                      $sti = oci_parse($conn , $query);
                      $lanza5 = oci_execute($sti);
                      ###################                     PRESUPUESTO 6                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", '06' , ".$ppto6.")";
                      $sti = oci_parse($conn , $query);
                      $lanza6 = oci_execute($sti);
                      ###################                     PRESUPUESTO 7                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", '07' , ".$ppto7.")";
                      $sti = oci_parse($conn , $query);
                      $lanza7 = oci_execute($sti);
                      ###################                     PRESUPUESTO 8                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", '08' , ".$ppto8.")";
                      $sti = oci_parse($conn , $query);
                      $lanza8 = oci_execute($sti);
                      ###################                     PRESUPUESTO 9                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", '09' , ".$ppto9.")";
                      $sti = oci_parse($conn , $query);
                      $lanza9 = oci_execute($sti);
                      ###################                     PRESUPUESTO 10                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 10 , ".$ppto10.")";
                      $sti = oci_parse($conn , $query);
                      $lanza10 = oci_execute($sti);
                      ###################                     PRESUPUESTO 11                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 11 , ".$ppto11.")";
                      $sti = oci_parse($conn , $query);
                      $lanza11 = oci_execute($sti);
                      ###################                     PRESUPUESTO 12                 ####################################
                      $query = "INSERT INTO OP_MF_PRES_ALMACEN (IID_ALMACEN, ANIO,  MES, PRESUPUESTO) VALUES(".$id_almacen.", ".$id_anio.", 12 , ".$ppto12.")";
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
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  //header("location:Gastos_Maquinaria.php");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 27);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/gastos_x_maquina.php';
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

//if($_SESSION['area']==3){
  $plaza=$_SESSION['nomPlaza'];
//}else {
  //$plaza = "ALL";
  if( isset($_GET["plaza"]) ){
    if( $_GET["plaza"] == "CORPORATIVO" || $_GET["plaza"] == "CÓRDOBA" || $_GET["plaza"] == "MÉXICO" || $_GET["plaza"] == "GOLFO" || $_GET["plaza"] == "PENINSULA" || $_GET["plaza"] == "PUEBLA" || $_GET["plaza"] == "BAJIO" || $_GET["plaza"] == "OCCIDENTE" || $_GET["plaza"] == "NORESTE" ){
      $plaza = $_GET["plaza"];
    }else{
      $plaza = "ALL";
    }
  }
//}

/*$plaza = "ALL";
if( isset($_GET["plaza"]) ){
  if( $_GET["plaza"] == "CORPORATIVO" || $_GET["plaza"] == "CÓRDOBA" || $_GET["plaza"] == "MÉXICO" || $_GET["plaza"] == "GOLFO" || $_GET["plaza"] == "PENINSULA" || $_GET["plaza"] == "PUEBLA" || $_GET["plaza"] == "BAJIO" || $_GET["plaza"] == "OCCIDENTE" || $_GET["plaza"] == "NORESTE" ){
    $plaza = $_GET["plaza"];
  }else{
    $plaza = "ALL";
  }
}
//echo $plaza;*/


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
    || $_GET["tipo"] == "00089" || $_GET["tipo"] == "00091" || $_GET["tipo"] == "00009"){
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

//WIDGETS
//GRAFICA NOMINA

$graficaNominaMes = $modelNomina->graficaPorMes($fecha,$plaza,$tipo,$almacen,$fil_habilitado);



  $graficaPlazaAlmacen = $modelNomina->graficaPlazaAlmacen($fecha,$plaza,$tipo,$fil_habilitado);

  $graficaNomina = $modelNomina->graficaNomina($fecha,$plaza,$tipo,$fil_habilitado);


//WIDGETS
$widgetsNomina = $modelNomina->widgetsNomina($fecha,$plaza,$tipo,$fil_habilitado,$almacen);


//$selectAlmacen = $modelNomina->almacenSql($plaza);
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- Select2 -->
<link rel="stylesheet" href="../plugins/select2/select2.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">

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
    <h1>Dashboard<small>Gastos de Operaciones</small></h1>
    <center><h4> PLAZA (<?php echo $_SESSION['nomPlaza']  ?>)</h4></center><!--FILTRO GENERAL -->


<?php
      $anio_elegido = substr($fecha, -4);
      //echo count($graficaNominaMes);
      $mes_elegido = substr($fecha, 14, 2);
      #echo $mes_elegido;

      $Anio_actual = date("Y");

      if ($Anio_actual) {
        $mes_actual = idate("m");
        //echo $mes_actual;
      }else {
        $mes_actual = 12;
      }
?>


  </section>


  <section class="Content"><!-- Inicia la seccion de los Widgets -->
    <div class="row">
    <!-- Widgets Cartas cupo expedidas -->
    <div class="col-lg-3 col-xs-6">
    <div class="info-box bg-green">
      <span class="info-box-icon"><i class="fa fa-usd"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">% PRESUPUESTO</span>
        <span class="info-box-number"><?php
                                        if (count($widgetsNomina) == 0) {
                                          echo "0.00";
                                        }
                                        else {
                                         echo number_format($widgetsNomina[0]["PRESUPUESTO"],2);
                                        }
                                       ?></span>
        <div class="progress">
          <div class="progress-bar" style="width: 60%"></div>
        </div>
        <span class="progress-description" title="<?=$fecha?>">Fecha: <?=$fecha?></span>
      </div>
    </div>
    </div>
      <!-- Termino Widgets Cartas cupo expedidas -->
      <!-- Widgets Cartas cupo no arribadas -->
      <div class="col-lg-3 col-xs-6">
      <div class="info-box bg-green">
        <span class="info-box-icon"><i class="fa fa-usd"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">% GASTO TOTAL</span>
          <span class="info-box-number"><?php
                        if (count($widgetsNomina) == 0) {
                            echo "0.00";
                        }else {
                            echo number_format($widgetsNomina[0]["PAGADO"],2);
                        }
                         ?></span>
          <div class="progress">
            <div class="progress-bar" style="width: 80%"></div>
          </div>
          <span class="progress-description" title="<?=$fecha?>">Fecha: <?=$fecha?></span>
        </div>
      </div>
      </div>
      <!-- Termina Widgets Cartas cupo no arribadas -->
      <!-- Widgets Cartas cupo canceladas -->
      <div class="col-lg-3 col-xs-6">
      <div class="info-box bg-green">
        <span class="info-box-icon"><i class="fa fa-percent"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">% PORCENTAJE</span>
          <span class="info-box-number"><?php
                                          if (count($widgetsNomina) == 0) {
                                              echo "0.00";
                                          }else {
                                              if (number_format($widgetsNomina[0]["PAGADO"], 2) == 0.00) {
                                                echo "-100.00";
                                              }elseif (number_format($widgetsNomina[0]["PRESUPUESTO"], 2) == 0.00) {
                                                echo "100.00";
                                              }else {
                                                  echo number_format(($widgetsNomina[0]["PAGADO"]*100)/$widgetsNomina[0]["PRESUPUESTO"],2);
                                              }
                                          }
                                         ?></span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description" title="<?=$fecha?>">Fecha: <?=$fecha?></span>
        </div>
      </div>
      </div>
      <!-- Termino Widgets Cartas cupo canceladas -->
    </div>
    <!-- /.row -->
    </section>


  <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
  <!-- ############################ SECCION GRAFICA ############################# -->
  <section>

    <div class="row"><!-- row -->

    <div class="col-md-9"><!-- col-md-9 -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-bar-chart"></i> INFORMACIÓN </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <div class="row">

          <div class="col-md-12">
            <?php if ($plaza == "ALL"): ?>
              <div id="graficaNom"></div>

            <?php elseif($almacen == 'ALL'): ?>
              <!--TABLE-->
              <div class="col-md-12">
                <?php /*$graficaDetalleAlmacen = $modelNomina->detalleGastos($fecha,$plaza,$tipo,$almacen,$fil_habilitado); */?>
                  <section>
                    <div class="box box-success">
                      <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-table"></i>GASTOS DEL ALMACEN</h3>
                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                      </div>
                      <div class="box-body"><!--box-body-->

                        <div class="table-responsive" id="container">
                          <table id="tabla_nomina" class="table table-bordered" cellspacing="0" width="100%">
                            <thead>
                              <tr>
                                <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                                <th class="small" bgcolor="#AB06F3"><font color="white">ALMACEN</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">ENERO</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">FEBRERO</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">MARZO</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">ABRIL</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">MAYO</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">JUNIO</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">JULIO</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">AGOSTO</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">SEPTIEMBRE</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">OCTUBRE</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">NOVIEMBRE</font></th>
                                <th class="small" bgcolor="#AB06F3"><font color="white">DICIEMBRE</font></th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $almacen_2 = "TODOS";
                              $mes_2 = "";
                              $graficaPlazaAlmacenReal = $modelNomina->graficaPlazaAlmacenxmes($fecha,$plaza,$almacen_2,$mes_2,$tipo,$fil_habilitado);
                              for ($i=0; $i <count($graficaPlazaAlmacenReal) ; $i++) { ?>
                              <tr>
                                <!--<td class="small">CL</td>-->
                                <td class="small"><?= $graficaPlazaAlmacenReal[$i]["ALMACEN"] ?></td>
                                <?php
                                  $almacen_2 = $graficaPlazaAlmacenReal[$i]["IID"];
                                  if ($almacen_2 == 'PANT') {
                                    $almacen_2 = 2;
                                  }elseif ($almacen_2 == 'PEÑU') {//$almacen == 'PEÑU' || $almacen == 'PANT' || $almacen == 'VICT'
                                    $almacen_2 = 1;
                                  }elseif ($almacen_2 == 'VICT') {
                                    $almacen_2 = 3;
                                  }

                                  $graficaPlazaMes2 = $modelNomina->graficaPorMes($fecha,$plaza,$tipo,$almacen_2,$fil_habilitado);    ?>
                                      <?php
                                              for ($y=0; $y < count($graficaPlazaMes2) ; $y++) {
                                                echo "<td class='small'>".number_format($graficaPlazaMes2[$y]["PAGADO"], 2)."<br>".
                                                                          number_format($graficaPlazaMes2[$y]["PRESUPUESTO1"], 2)."<br>";
                                                                          if ($graficaPlazaMes2[$y]["PAGADO"] == 0 AND $graficaPlazaMes2[$y]["PRESUPUESTO1"] > 0) {
                                                                            echo "%100";
                                                                          }elseif ($graficaPlazaMes2[$y]["PAGADO"] > 0 AND $graficaPlazaMes2[$y]["PRESUPUESTO1"] == 0) {
                                                                              echo "-%100";
                                                                          }elseif ($graficaPlazaMes2[$y]["PAGADO"] == 0 AND $graficaPlazaMes2[$y]["PRESUPUESTO1"] == 0) {
                                                                            echo "%0.00";
                                                                          }
                                                                          elseif($graficaPlazaMes2[$y]["PAGADO"] > 0 AND $graficaPlazaMes2[$y]["PRESUPUESTO1"] > 0) {
                                                                            echo "%".number_format(($graficaPlazaMes2[$y]["PAGADO"]/$graficaPlazaMes2[$y]["PRESUPUESTO1"])* 100, 2);
                                                                          }
                                                                          echo "</td>";
                                              }
                                       ?>
                              </tr>
                              <?php } ?>

                            </tbody>
                          </table>
                        </div>

                      </div><!--/.box-body-->
                    </div>
                  </section>
              </div>
            <?php endif; ?>
          </div>
          <div class="col-md-12">
            <?php if ($almacen != 'ALL'): $graficaDetalleAlmacen = $modelNomina->detalleGastos($fecha,$plaza,$tipo,$almacen,$fil_habilitado); ?>
              <section>
                <div class="box box-success">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-table"></i><?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen); if ($almacen == 1) {
                      echo " GASTOS DEL ALMACEN PEÑUELA";
                    }elseif($almacen == 2) {
                      echo " GASTOS DEL ALMACEN PANTACO";
                    }elseif ($almacen == 3) {
                      echo " GASTOS DEL ALMACEN VICTORIA";
                    }  else {
                    echo " GASTOS DEL ALMACEN ".$nombreAlm[0]["V_NOMBRE"];;
                    }?></h3>
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
                            <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                            <th class="small" bgcolor="#43ADBF"><font color="white">CUENTA</font></th>
                            <th class="small" bgcolor="#2D7380"><font color="white"><?php $anioIni = substr($fecha, 6,4); $anioIni3 = $anioIni-2; echo 'AÑO '.$anioIni3; ?></font></th>
                            <th class="small" bgcolor="#59E6FF"><font color="black"><?php $anioIni = substr($fecha, 6,4); $anioIni2 = $anioIni-1; echo 'AÑO '.$anioIni2; ?></font></th>
                            <th class="small" bgcolor="#163A40"><font color="white"><?php $anioIni = substr($fecha, 6,4); echo 'AÑO '.$anioIni; ?></font></th>
                            <th class="small" bgcolor="#50CFE6"><font color="white">PORCENTAJE</font></th> </th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">ACUMULADO</font></th> </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php for ($i=0; $i <count($graficaDetalleAlmacen) ; $i++) { ?>
                          <tr>
                            <!--<td class="small">CL</td>-->
                            <td class="small"><?= $graficaDetalleAlmacen[$i]["DESCRIPCION"] ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["PAGO3"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["PAGO2"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["PAGO1"], 2) ?></td>
                            <td class="small">%<?= number_format(($graficaDetalleAlmacen[$i]["PAGO1"]/$widgetsNomina[0]["PAGADO"])*100, 2) ?></td>
                            <td class="small"><?= number_format(($graficaDetalleAlmacen[$i]["PAGO4"]), 2) ?></td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>

                  </div><!--/.box-body-->
                </div>
              </section>
            <?php endif; ?>
          </div>

          <!--GRAFICA NOMINA POR MES DIEGO ALTAMIRANO SUAREZ-->
          <div class="col-md-12">
            <div id="graficaNomMes"></div>
            <div id="graficaNomMes2"></div>
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
        <a href="Gastos_Maquinaria.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
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

        <!-- FILTRAR POR PLAZA -->
        <input id="nomPlaza" type="hidden" value=<?= $plaza ?>>
        <?php //if($_SESSION['area']!=3){  ?>
        <!--<div class="input-group">
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
      <?php //} else{?>
        <input id="nomPlaza" type="hidden" value=<?= $plaza ?>>-->
      <?php //}?>

        <!--FILTRAR POR ALMACEN -->
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Almacen:</span>
          <select class="form-control select2" style="width: 100%;" id="nomAlm">
            <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $plazas=$plaza;
            //$plazas = $_GET["plaza"];
            $selectAlmacen = $modelNomina->almacenSql($plazas);
            for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
              <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
            <?php } ?>
          </select>
        </div>
        <div class="input-group">
          <span class="input-group-addon"> <input type="checkbox" name="fil_habilitado" <?php if( $fil_habilitado == 'on' ){ echo "checked";} ?> > ALMACEN HABILITADO</span>
        </div>
        <!-- FILTRAR POR TIPO NOMINA -->
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Tipo Gasto:</span>
          <select class="form-control select2" style="width: 100%;" id="nomTipo">
            <option value="ALL" <?php if( $tipo == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $selectTipo = $modelNomina->sql(5,$depto, null);
            for ($i=0; $i <count($selectTipo) ; $i++) { ?>
              <option value="<?=$selectTipo[$i]["CUENTA"]?>" <?php if($selectTipo[$i]["CUENTA"] == $tipo){echo "selected";} ?>><?=$selectTipo[$i]["DESCRIPCION"]?> </option>
            <?php } ?>
          </select>
        </div>
        <div class="input-group">
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>

      </div><!--/.box-body-->
    </div>

    </div><!-- /.col-md-3 -->

    <?php
      $valor_usuario = $_SESSION['usuario'];
      if ($valor_usuario == "julio" || $valor_usuario == "diego13" || $valor_usuario == "david") {
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
    <?php
  }  ?>

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
  fil_habilitado = 'off';

  if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?fecha='+fecha+'&plaza='+plaza+'&tipo='+tipo+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
  }
  else {
    fil_habilitado = 'off';
    url = '?fecha='+fecha+'&plaza='+plaza+'&tipo='+tipo+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
  }
  location.href = url;

});
</script>
<!-- DataTables -->
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
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>
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
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php for ($i=0; $i <count($graficaNomina) ; $i++) {  ?>
  "<?=$graficaNomina[$i]["PLAZA"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i <count($graficaNomina) ; $i++) {  ?>
  <?=$graficaNomina[$i]["PAGADO"]?>,
  <?php }  ?>
  ];

  $('#graficaNom').highcharts({
    chart: { type: 'column' },
    title: { text: 'GASTOS TOTALES OPERACIONES POR PLAZAS' },
    legend:{
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
          lineWidth: 2,
          //min: 0,
          offset: 10,
          tickWidth: 1,
          title: {
            text: 'Monto'
          },
          labels:{
                formatter: function () {
                  return this.value;
                }
          }
    },
    tooltip:{
            shared: true,
            valueSuffix: ' MXN',
            useHTML: true,
            valueDecimals: 2,
            valuePrefix: '$',
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
    colors: ['#464f88'],
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
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle: {
      text: '* Click en el nombre de la plaza para filtrar',
      align: 'right',
      x: -10,
    },
    series:[{
            name: 'Total Pagado',
            data: data1,
            }]
    });

});
</script>

<script type="text/javascript">
function cambiar(){
    var pdrs = document.getElementById('file').files[0].name;
    document.getElementById('info').innerHTML = pdrs;
    document.getElementById('submit').disabled = false;
}
</script>
<!--grafica por mes -->
<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php
  for ($i=0; $i < $mes_actual ; $i++) {  ?>
  "<?=$graficaNominaMes[$i]["MES"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO"]?>,
  <?php }  ?>
  ];
  var data2 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO2"]?>,
  <?php }  ?>
  ];
  var data3 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO3"]?>,
  <?php }  ?>
  ];
  var data4 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PRESUPUESTO1"]?>,
  <?php }  ?>
  ];
  var data5 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PRESUPUESTO2"]?>,
  <?php }  ?>
  ];
  var data6 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PRESUPUESTO3"]?>,
  <?php }  ?>
  ];

  $('#graficaNomMes2').highcharts({
    chart: { type: 'line' },
    title: { text: <?php if ($plaza == 'ALL' && $almacen == 'ALL' && $tipo == 'ALL'): ?>
                    'GASTOS Y PRESUPUESTOS TOTALES ANUALES' },
                    <?php elseif($almacen == 1):  ?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN PEÑUELA' },
                    <?php elseif($almacen == 2):  ?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN PANTACO' },
                    <?php elseif($almacen == 3):  ?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN VICTORIA' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo == 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo != 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);
                          $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS Y PRESUPUESTOS DE <?=$nombreTip[0]["DESCRIPCION"]?>  ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($tipo != 'ALL' && $plaza == 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS Y PRESUPUESTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES' },
                    <?php elseif ($tipo != 'ALL' && $plaza != 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS Y PRESUPUESTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES PLAZA <?=$plaza?>' },
                    <?php else: ?>
                    'GASTOS Y PRESUPUESTOS TOTALES ANUALES PLAZA  <?=$plaza?>' },
                    <?php endif; ?>
    legend:{
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
          lineWidth: 2,
          //min: 0,
          offset: 10,
          tickWidth: 1,
          title: {
            text: 'Monto'
          },
          labels:{
                formatter: function () {
                  return this.value;
                }
          }
    },
    tooltip:{
            shared: true,
            valueSuffix: ' MXN',
            useHTML: true,
            valueDecimals: 2,
            valuePrefix: '$',
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
    colors: ['#464f88', '#C21313', '#2DF306'],
    plotOptions:{
                series: {
                  minPointLength: 3
                }
    },
    xAxis: {
      categories: categories,
      labels: {
        formatter: function () {
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle: {
      text: ' ',
      align: 'right',
      x: -10,
    },
      series:[{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha; ?>',
              data: data1,
      }/*,{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-1;?>',
              data: data2,
      },{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-2;?>',
              data: data3,
      }*/,{
              name: 'Total Presupuesto del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha;?>',
              data: data4,
      }/*,{
              name: 'Total Presupuesto del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-1;?>',
              data: data5,
      },{
              name: 'Total Presupuesto del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-2;?>',
              data: data6,
      }*/]
    });

});
</script>


<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php
  for ($i=0; $i < $mes_actual ; $i++) {  ?>
  "<?=$graficaNominaMes[$i]["MES"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO"]?>,
  <?php }  ?>
  ];
  var data2 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO2"]?>,
  <?php }  ?>
  ];
  var data3 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO3"]?>,
  <?php }  ?>
  ];

  $('#graficaNomMes').highcharts({
    chart: { type: 'line' },
    title: { text: <?php if ($plaza == 'ALL' && $almacen == 'ALL' && $tipo == 'ALL'): ?>
                    'GASTOS TOTALES ANUALES' },
                    <?php elseif($almacen == 1):  ?>
                    'GASTOS ANUALES DEL ALMACEN PEÑUELA' },
                    <?php elseif($almacen == 2):  ?>
                    'GASTOS ANUALES DEL ALMACEN PANTACO' },
                    <?php elseif($almacen == 3):  ?>
                    'GASTOS ANUALES DEL ALMACEN VICTORIA' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo == 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);?>
                    'GASTOS ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo != 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);
                          $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS DE <?=$nombreTip[0]["DESCRIPCION"]?>  ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($tipo != 'ALL' && $plaza == 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES' },
                    <?php elseif ($tipo != 'ALL' && $plaza != 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES PLAZA <?=$plaza?>' },
                    <?php else: ?>
                    'GASTOS TOTALES ANUALES PLAZA  <?=$plaza?>' },
                    <?php endif; ?>
    legend:{
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
          lineWidth: 2,
          //min: 0,
          offset: 10,
          tickWidth: 1,
          title: {
            text: 'Monto'
          },
          labels:{
                formatter: function () {
                  return this.value;
                }
          }
    },
    tooltip:{
            shared: true,
            valueSuffix: ' MXN',
            useHTML: true,
            valueDecimals: 2,
            valuePrefix: '$',
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
    colors: ['#464f88', '#C21313', '#2DF306'],
    plotOptions:{
                series: {
                  minPointLength: 3
                }
    },
    xAxis: {
      categories: categories,
      labels: {
        formatter: function () {
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle: {
      text: ' ',
      align: 'right',
      x: -10,
    },
      series:[{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha; ?>',
              data: data1,
      },{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-1;?>',
              data: data2,
      },{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-2;?>',
              data: data3,
      }]
    });

});
</script>

<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php for ($i=0; $i <count($graficaPlazaAlmacen) ; $i++) {  ?>
  "<?=$graficaPlazaAlmacen[$i]["ALMACEN"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i <count($graficaPlazaAlmacen) ; $i++) {  ?>
  <?=$graficaPlazaAlmacen[$i]["PAGADO"]?>,
  <?php }  ?>
  ];

  $('#graficaAlmacen').highcharts({
    chart: { type: 'column' },
    title: { text: <?php if ($plaza == 'ALL' && $almacen == 'ALL'): ?>
                    'ALMACENES DE LA PLAZA' },
                   <?php elseif ($almacen != 'ALL'): ?>
                   'ALMACENES DE LA PLAZA' },
                   <?php ?>
                   <?php else: ?>
                    'ALMACENES DE LA PLAZA <?=$plaza?>' },
                   <?php endif; ?>
    legend:{
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
          lineWidth: 2,
          //min: 0,
          offset: 10,
          tickWidth: 1,
          title: {
            text: 'Monto'
          },
          labels:{
                formatter: function () {
                  return this.value;
                }
          }
    },
    tooltip:{
            shared: true,
            valueSuffix: ' MXN',
            useHTML: true,
            valueDecimals: 2,
            valuePrefix: '$',
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
    colors: ['#464f88'],
    plotOptions:{
                series: {
                  minPointLength: 3
                }
    },
    xAxis: {
      categories: categories,
      labels: {
        formatter: function () {
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
            return '<a>' +
                this.value + '</a>';
        }
      }
    },
    subtitle: {
      text: ' ',
      align: 'right',
      x: -10,
    },
    series:[{
            name: 'Total Pagado',
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
