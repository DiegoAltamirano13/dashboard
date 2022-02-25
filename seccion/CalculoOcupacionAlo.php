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

                $id_cliente = 0;
                if(isset($Row[0])) {
                    $id_cliente = $Row[0];
                }

                $id_plaza = 0;
                if(isset($Row[1])) {
                    $id_plaza = $Row[1];
                }

                $id_almacen = 0;
                if(isset($Row[2])) {
                    $id_almacen = $Row[2];
                }

                $semana = 0;
                if(isset($Row[3])) {
                    $semana = $Row[3];
                }

                $mes = 0;
                if(isset($Row[4])) {
                    $mes = $Row[4];
                }

                $anio = 0;
                if(isset($Row[5])) {
                    $anio = $Row[5];
                }

                $ocupacion = 0.00;
                if(isset($Row[6])) {
                    $ocupacion = $Row[6];
                }

                $mtsutiles = 0.00;
                if(isset($Row[7])) {
                  if (is_null($Row[7]) OR empty($Row[7])) {
                    $mtsutiles = 0;
                  }
                  else {
                    $mtsutiles = $Row[7];
                  }

                }

                $mtspasillo = 0.00;
                if(isset($Row[8])) {
                  if (is_null($Row[8]) OR empty($Row[8])) {
                      $mtspasillo = 0;
                  }
                  else {
                      $mtspasillo = $Row[8];
                  }
                }

                $mts_rack_almacen = 0.00;
                if(isset($Row[9])) {
                  if (is_null($Row[9]) OR empty($Row[9])) {
                      $mts_rack_almacen = 0;
                  }
                  else {
                      $mts_rack_almacen = $Row[9];
                  }
                }

                $mts_capacidad_total = 0.00;
                if(isset($Row[10])) {
                  if (is_null($Row[10]) OR empty($Row[10])) {
                      $mts_capacidad_total = 0;
                  }
                  else {
                      $mts_capacidad_total = $Row[10];
                  }
                }

                $usos_variados_piso = 0.00;
                if(isset($Row[11])) {
                  if (is_null($Row[11]) OR empty($Row[11])) {
                      $usos_variados_piso = 0;
                  }
                  else {
                      $usos_variados_piso = $Row[11];
                  }
                }

                $area_racks = 0.00;
                if(isset($Row[12])) {
                  if (is_null($Row[12]) OR empty($Row[12])) {
                      $area_racks = 0;
                  }
                  else {
                      $area_racks = $Row[12];
                  }
                }

                $tamanio_bodega = 0.00;
                if(isset($Row[13])) {
                  if (is_null($Row[13]) OR empty($Row[13])) {
                      $tamanio_bodega = 0;
                  }
                  else {
                      $tamanio_bodega = $Row[13];
                  }
                }

                $via_real = 0.00;
                if(isset($Row[14])) {
                  if (is_null($Row[14]) OR empty($Row[14])) {
                      $via_real = 0;
                  }
                  else {
                      $via_real = $Row[14];
                  }
                }

                $via_tradicional = 0.00;
                if(isset($Row[15])) {
                  if (is_null($Row[15]) OR empty($Row[15])) {
                      $via_tradicional = 0;
                  }
                  else {
                      $via_tradicional = $Row[15];
                  }
                }

                $proyecto = 0.00;
                if(isset($Row[16])) {
                  if (is_null($Row[16]) OR empty($Row[16])) {
                      $proyecto = 0;
                  }
                  else {
                      $proyecto = $Row[16];
                  }
                }


                if (!empty($id_cliente) || !empty($id_plaza) || !empty($id_almacen) || !empty($semana) || !empty($mes) || !empty($anio) || !empty($ocupacion) || !empty($mtsutiles) || !empty($mtspasillo) || !empty($mts_almacen) || !empty($mts_rack_almacen)) {
                  //QUERY CONSULTA
                    $consulta = "SELECT COUNT(*)AS ID FROM PRUEBA_SUBIDA WHERE ID_CLIENTE = ".$id_cliente." AND IID_PLAZA = ".$id_plaza." AND IID_ALMACEN = ".$id_almacen." AND SEMANA = ".$semana." AND MES = ".$mes." AND ANIO = ".$anio."  AND OCUPACION = ".$ocupacion." AND T_PROYECTO = '".$proyecto."'";
                    echo $consulta;
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

                      $query = "INSERT INTO PRUEBA_SUBIDA (ID_CLIENTE, IID_PLAZA,  IID_ALMACEN, SEMANA, MES, ANIO, OCUPACION, MTS_UTILIZADOS, MTS_UTILIZADOS_PASILLOS, T_PROYECTO) VALUES(".$id_cliente.", ".$id_plaza.", ".$id_almacen.", ".$semana.", ".$mes.", ".$anio.", ".$ocupacion.", ".$mtsutiles.", ".$mtspasillo.",'".$proyecto."')";
                      $sti = oci_parse($conn , $query);

                      $lanza = oci_execute($sti);

                            if (!empty($lanza)) {
                              $consulta_almacenes = "SELECT COUNT(*) AS NUMBERALMACEN FROM ALMACEN_CAPACIDAD WHERE IID_PLAZA = ".$id_plaza." AND IID_ALMACEN = ".$id_almacen. " AND ANIO = ".$anio." AND MES = ".$mes." AND SEMANA = ".$semana."";
                              $still2 = oci_parse($conn, $consulta_almacenes);
                              oci_execute($still2);
                              while (oci_fetch($still2)) {
                                  $reg2 = oci_result($still2, "NUMBERALMACEN");
                                  if ($reg2 > 0) {

                                  }
                                  else {
                                    //INSERTA CAPACIDAD DE ALMACEN DEL MISMO EXCEL
                                      $query2 = "INSERT INTO ALMACEN_CAPACIDAD (IID_PLAZA, IID_ALMACEN, ANIO, MES, SEMANA, MTS_RACKS, CAPACIDAD_TOTAL, USO_VARIADOS, AREA_RACKS, TAMANIO_BODEGA, VIA_REAL, VIA_TRADICIONAL) VALUES (".$id_plaza.", ".$id_almacen.", ".$anio.", ".$mes.", ".$semana.", ".$mts_rack_almacen.", ".$mts_capacidad_total.", ".$usos_variados_piso.", ".$area_racks.", ".$tamanio_bodega.", ".$via_real.", ".$via_tradicional.")";
                                      echo $query2;
                                      $sti2 = oci_parse($conn, $query2);
                                      $lanza2 = oci_execute($sti2);
                                      if (!empty($lanza2)) {
                                          $type = "success";
                                          $message = "Excel Importado Correctamente!!!";
                                      } else {
                                          $type = "error";
                                          $message = "Problema Importando Excel!!!";
                                      }
                                  }
                              }
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
  //header("location:calculo_Ocupacion.php");
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

include_once '../class/Calculo_Ocupacion_Alo.php';
$obj_class = new Calculo_Ocupacion();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 6);
$modulos_valida2 = Perfil::modulos_valida($_SESSION['iid_empleado'], 58);
if($modulos_valida == 0 AND $modulos_valida2 == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////

/* $_GET FECHA */
$fecha = 'ALL';
if ( isset($_GET["fecha"]) ){
  if ( $obj_class->validateDate(substr($_GET["fecha"],0,10)) AND $obj_class->validateDate(substr($_GET["fecha"],11,10)) ){
    $fecha = $_GET["fecha"];
  }else{
    $fecha = "ALL";
  }
}
/* $_GET FIL_CHECK */
$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}

$plaza = "BAJIO";
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

$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}

$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}
//GRAFICA
$graficaMensual = $obj_class->grafica($plaza,$fecha,$almacen,$fil_check);
$graficaAlmacen = $obj_class->graficaAlmacen($plaza,$fecha,$fil_check);
$graficaCliente = $obj_class->graficaCliente($plaza,$fecha,$fil_check,$almacen);
$grafica = $obj_class->graficaMensual($plaza,$fecha,$almacen,$fil_check);
$capacidad_almacen = $obj_class->capacidad_almacen($plaza,$fecha,$fil_check);
$cuarentena = $obj_class->graficaAlmacenProyectoCuarentena($plaza,$fecha,$fil_check,'0')

//$graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'BMW');
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
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
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Dashboard<small>Calculo Ocupación </small><small>PLAZA ( <?php echo $_SESSION['nomPlaza'] ?> )</small>
      </h1>
    </section>
    <!-- Main content -->

    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

<!-- ############################ SECCION GRAFICA Y WIDGETS ############################# -->
<section>
  <div class="row">
    <div class="col-md-9">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <?php #echo COUNT($graficaCliente); ?>
        <div class="box-body"><!--box-body-->
          <div class="col-md-12">
            <!--widgets -->
            <div id="div_widgets" class="col-md-6">
              <div class="box box-primary">

                  <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-home"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">OCUPACIÓN GLOBAL</span>
                      <span class="info-box-number">
                        <?php
                                              $sumatoria_espacio_pasillos = 0;
                                              for ($i=0; $i < count($grafica) ; $i++) {
                                                  $sumatoria_espacio_pasillos += $grafica[$i]["MTS_UTILIZADOS"];
                                              }

                                              $sumatoria_metros_ocu_piso = 0;
                                              for ($i=0; $i < count($grafica) ; $i++) {
                                                  $sumatoria_metros_ocu_piso += $grafica[$i]["MTS_PASILLOS_ESPACIO"];
                                              }

                                              $sumatoria_total_posiciones = 0;
                                              for ($i=0; $i < count($grafica) ; $i++) {
                                                  $sumatoria_total_posiciones += $grafica[$i]["MTS_RACKS"];
                                              }

                                              $sumatoria_uso_variado = 0;
                                              for ($i=0; $i < count($grafica) ; $i++) {
                                                  $sumatoria_uso_variado = $grafica[$i]["CAPACIDAD_TOTAL"]- $grafica[$i]["USO_VARIADO"];
                                              }

                                              $sumatoria_uso_variado_solo = 0;
                                              for ($i=0; $i < count($grafica) ; $i++) {
                                                  $sumatoria_uso_variado_solo += $grafica[$i]["USO_VARIADO"];
                                              }
                                              $sumatoria_plaza_area_racks = 0;
                                              for ($i=0; $i < count($grafica) ; $i++) {
                                                $sumatoria_plaza_area_racks += $grafica[$i]["AREA_RACKS"];
                                              }

                                              $sumatoria_plaza_tamanio_bodega = 0;
                                              for ($i=0; $i < count($grafica) ; $i++) {
                                                $sumatoria_plaza_tamanio_bodega += $grafica[$i]["TAMANIO_BODEGA"];
                                              }
                                              $sum_cap_bodega = 0;
                                              for ($i=0; $i < count($grafica) ; $i++) {
                                                $sum_cap_bodega += $grafica[$i]["CAPACIDAD_TOTAL"];
                                              }
                                              $sumatoria_ubicacions_vacias_racks = $sumatoria_total_posiciones - $sumatoria_espacio_pasillos;

                                              $sumatoria_espacio_libre = $sumatoria_uso_variado - $sumatoria_metros_ocu_piso;

                                              if ($sumatoria_plaza_area_racks > 0) {
                                                  $factor_racks = $sumatoria_total_posiciones / $sumatoria_plaza_area_racks;
                                              }
                                              else {
                                                  $factor_racks = $sumatoria_total_posiciones;
                                              }

                                              if ($sumatoria_espacio_pasillos == 0 || $factor_racks == 0) {
                                                  $factor_racks_piso = 0;
                                              }else {
                                                  $factor_racks_piso = $sumatoria_espacio_pasillos / $factor_racks;
                                              }

                                              if ($factor_racks_piso == 0 || $sumatoria_plaza_area_racks == 0) {
                                                $porcentaje_factor_racks_piso = 0;
                                              }
                                              else {
                                                $porcentaje_factor_racks_piso = $factor_racks_piso/ $sumatoria_plaza_area_racks;
                                              }


                                              //$tamanio_bod = $capacidad_almacen[0]["TAMANIO_BODEGA"];

                                              $oc_total = $factor_racks_piso+$sumatoria_metros_ocu_piso+$sumatoria_uso_variado_solo;
                                              //echo $capacidad_almacen[0]["USO_VARIADOS"]. "<br >";
                                              #echo number_format(ROUND($oc_total, 2));
                                              echo number_format($sumatoria_metros_ocu_piso, 2);
                        ?>
                      </span>
                      <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>

            <div id="div_widgets" class="col-md-6">
              <div class="box box-primary">

                  <div class="info-box bg-red">
                  <span class="info-box-icon"><i class="fa fa-home"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">OCUPACIÓN GLOBAL PORCENTAJE</span>

                    <span class="info-box-number">
                      <?php
                        if ($sumatoria_plaza_area_racks == 0) {
                          if ($sumatoria_metros_ocu_piso == 0 OR $sum_cap_bodega == 0) {
                            echo 0;
                          }else {
                            //echo $sum_cap_bodega;
                            //echo  ROUND(($sumatoria_metros_ocu_piso/$sum_cap_bodega)*100, 2);
                          }
                        }else {
                          if ($oc_total == 0 || $sumatoria_plaza_tamanio_bodega == 0 ) {
                            $por_total = 0;
                          }
                          else {
                              $por_total = $oc_total/$sumatoria_plaza_tamanio_bodega;
                          }
                        //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
                      }
                      #echo $sumatoria_plaza_tamanio_bodega;
                      echo ROUND(($sumatoria_metros_ocu_piso/$sumatoria_plaza_tamanio_bodega)*100, 2). "%";
                      ?>
                    </span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 70%"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


          </div>
          <div class="col-md-12">
            <?php if ($almacen != 'ALL') {?>
              <div id="graf_bar"></div>
              <section>
                <div class="box box-success">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-table"></i> OCUPACIÓN POR CLIENTE PLAZA <?php echo $plaza; ?></h3>
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
                            <th class="small" bgcolor="#2b35b6"><font color="white">NOMBRE</font></th>
                            <th class="small" bgcolor="#2b35b6"><font color="white">PORCENTAJE GLOBAL</font></th>
                          </tr>
                        </thead>
                        <tbody>

                          <?php
                          $uso_variado_dividio = $graficaCliente[0]["USO_VARIADO"]/count($graficaCliente);
                          for ($i=0; $i <count($graficaCliente) ; $i++) {  /*echo  $graficaCliente[$i]["PLAZA"]."-------------".$graficaCliente[$i]["MTS_UTILIZADOS"]."------------".$graficaCliente[$i]["MTS_PASILLOS_ESPACIO"]."</br>";*/?>

                            <!--<td class="small">CL</td>-->
                            <tr>
                            <td class="small">
                            <?php
                                echo $graficaCliente[$i]["PLAZA"];
                              ?>
                            </td>
                            <?php
                              if ($graficaCliente[$i]["MTS_UTILIZADOS"] == 0 AND $graficaCliente[$i]["MTS_PASILLOS_ESPACIO"] == 0) {
                            ?>
                              <td class="small">0.00 </td>
                            <?php
                              }
                              if ($graficaCliente[$i]["MTS_UTILIZADOS"] <> 0 OR $graficaCliente[$i]["MTS_PASILLOS_ESPACIO"] <> 0) {
                            ?>
                              <td class="small">
                            <?php
                                $sumatoria_grafica_x_cliente_esp_pasillos = 0;
                                $sumatoria_grafica_x_cliente_esp_pasillos = $graficaCliente[$i]["MTS_UTILIZADOS"];
                                $sumatoria_grafica_x_cliente_ocu_piso = 0;
                                $sumatoria_grafica_x_cliente_ocu_piso = $graficaCliente[$i]["MTS_PASILLOS_ESPACIO"];
                                $sumatoria_grafica_x_cliente_total_posiciones = 0;
                                $sumatoria_grafica_x_cliente_total_posiciones = $graficaCliente[$i]["MTS_RACKS"];
                                $sumatoria_grafica_x_cliente_uso_variado = 0;
                                $sumatoria_grafica_x_cliente_uso_variado_solo = 0;
                                $sumatoria_grafica_x_cliente_uso_variado_solo = $graficaCliente[$i]["USO_VARIADO"];
                                $sumatoria_grafica_x_cliente_uso_variado = $graficaCliente[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_cliente_uso_variado_solo;
                                $sumatoria_cap_ttt = $graficaCliente[$i]["CAPACIDAD_TOTAL"];
                                $sumatoria_grafica_x_cliente_ubicaciones_vacias_racks = $graficaCliente[$i]["MTS_RACKS"]- $sumatoria_grafica_x_cliente_esp_pasillos;
                                $sumatoria_grafica_x_cliente_espacio_libre = $sumatoria_grafica_x_cliente_uso_variado- $sumatoria_grafica_x_cliente_ocu_piso;
                                $sumatoria_grafica_x_cliente_area_racks = $graficaCliente[$i]["AREA_RACKS"];
                                $sumatoria_grafica_x_cliente_tamanio_bod = $graficaCliente[$i]["TAMANIO_BODEGA"];
                                if ($sumatoria_grafica_x_cliente_esp_pasillos == 0 OR $sumatoria_grafica_x_cliente_total_posiciones == 0) {
                                  echo ROUND(($sumatoria_grafica_x_cliente_ocu_piso/$sumatoria_cap_ttt)*100, 2)."%";
//                                echo $sumatoria_grafica_x_cliente_ocu_piso;
                                }
                                else {
                                  if ($sumatoria_grafica_x_cliente_area_racks > 0) {
                                    $sumatoria_grafica_x_cliente_factor_racks = $sumatoria_grafica_x_cliente_total_posiciones / $sumatoria_grafica_x_cliente_area_racks;
                                  }
                                  else {
                                    $sumatoria_grafica_x_cliente_factor_racks = $sumatoria_grafica_x_cliente_total_posiciones;
                                  }

                                  if ($sumatoria_grafica_x_cliente_esp_pasillos == 0 || $sumatoria_grafica_x_cliente_factor_racks == 0 ) {
                                    $sumatoria_grafica_x_cliente_factor_racks_piso = 0;
                                  }
                                  else {
                                    $sumatoria_grafica_x_cliente_factor_racks_piso = $sumatoria_grafica_x_cliente_esp_pasillos / $sumatoria_grafica_x_cliente_factor_racks;
                                  }

                                  if ($sumatoria_grafica_x_cliente_factor_racks_piso == 0 || $sumatoria_grafica_x_cliente_area_racks == 0) {
                                    $sumatoria_grafica_x_cliente_porcentaje_racks_piso = 0;
                                  }
                                  else {
                                    $sumatoria_grafica_x_cliente_porcentaje_racks_piso = $sumatoria_grafica_x_cliente_factor_racks_piso / $sumatoria_grafica_x_cliente_area_racks;
                                  }

                                  $sumatoria_grafica_x_cliente_oc_total = $sumatoria_grafica_x_cliente_factor_racks_piso+$sumatoria_grafica_x_cliente_ocu_piso;
                                  //echo "FACTOR RACKS".$sumatoria_grafica_x_cliente_factor_racks . "</br> "."factor racks piso".$sumatoria_grafica_x_cliente_factor_racks_piso."</br>"."porcentaje factor".$sumatoria_grafica_x_cliente_porcentaje_racks_piso;
                                  /*echo $sumatoria_grafica_x_cliente_factor_racks."</br>";
                                  echo $sumatoria_grafica_x_cliente_factor_racks_piso."</br>";
                                  echo $sumatoria_grafica_x_cliente_porcentaje_racks_piso."</br>";
                                  echo $sumatoria_grafica_x_cliente_oc_total."</br>";*/
                                  echo ROUND(($sumatoria_grafica_x_cliente_oc_total/$sumatoria_grafica_x_cliente_tamanio_bod)*100, 2)."%";
                                  //echo "here";
                                }
                                ?>
                                </td>
                                </tr>
                                <?php
                              }
                            ?>

                          <?php } ?>
                        </tbody>
                      </table>
                    </div>

                  </div><!--/.box-body-->
                </div>
              </section>
            <?php } ?>
            <?php if ($plaza == 'ALL') {?>
              <div id="graf_perM"></div>
            <?php }else {?>
              <?php if ($almacen == 'ALL') { ?>
                <div id="graf_perAlmacen"></div>
                <div id="graf_perAlmacen2"></div>


                <div class="table-responsive" id="container">
                  <table id="tabla_nomina" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                      <tr>
                        <th class="small" bgcolor="#2b35b6"><font color="white">NOMBRE</font></th>
                        <th class="small" bgcolor="#2b35b6"><font color="white">PORCENTAJE GLOBAL</font></th>
                      </tr>
                    </thead>
                    <tbody>

                      <?php
                      //$uso_variado_dividio = $graficaCliente[0]["USO_VARIADO"]/count($graficaCliente);
                      for ($i=0; $i <count($cuarentena) ; $i++) {
                          if ($cuarentena[$i]["MTS_UTILIZADOS"] <> 0 AND $cuarentena[$i]["MTS_PASILLOS_ESPACIO"] <> 0){
                        ?>

                        <tr>
                        <td class="small">
                        <?php
                            echo $cuarentena[$i]["T_PROYECTO"];
                          ?>
                        </td>
                        <?php
                          if ($cuarentena[$i]["MTS_UTILIZADOS"] == 0 AND $cuarentena[$i]["MTS_PASILLOS_ESPACIO"] == 0) {
                        ?>
                          <td class="small">0.00 </td>
                        <?php
                          }
                          elseif ($cuarentena[$i]["MTS_UTILIZADOS"] <> 0 OR $cuarentena[$i]["MTS_PASILLOS_ESPACIO"] <> 0) {
                        ?>
                          <td class="small">
                        <?php
                            $sumatoria_grafica_x_cliente_esp_pasillos = 0;
                            $sumatoria_grafica_x_cliente_esp_pasillos = $cuarentena[$i]["MTS_UTILIZADOS"];
                            $sumatoria_grafica_x_cliente_ocu_piso = 0;
                            $sumatoria_grafica_x_cliente_ocu_piso = $cuarentena[$i]["MTS_PASILLOS_ESPACIO"];
                            $sumatoria_grafica_x_cliente_total_posiciones = 0;
                            $sumatoria_grafica_x_cliente_total_posiciones = $cuarentena[$i]["MTS_RACKS"];
                            $sumatoria_grafica_x_cliente_uso_variado = 0;
                            $sumatoria_grafica_x_cliente_uso_variado_solo = 0;
                            $sumatoria_grafica_x_cliente_uso_variado_solo = $cuarentena[$i]["USO_VARIADO"];
                            $sumatoria_grafica_x_cliente_uso_variado = $cuarentena[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_cliente_uso_variado_solo;
                            $sumatoria_cap_ttt = $cuarentena[$i]["CAPACIDAD_TOTAL"];
                            $sumatoria_grafica_x_cliente_ubicaciones_vacias_racks = $cuarentena[$i]["MTS_RACKS"]- $sumatoria_grafica_x_cliente_esp_pasillos;
                            $sumatoria_grafica_x_cliente_espacio_libre = $sumatoria_grafica_x_cliente_uso_variado- $sumatoria_grafica_x_cliente_ocu_piso;
                            $sumatoria_grafica_x_cliente_area_racks = $cuarentena[$i]["AREA_RACKS"];
                            $sumatoria_grafica_x_cliente_tamanio_bod = $cuarentena[$i]["TAMANIO_BODEGA"];
                            if ($sumatoria_grafica_x_cliente_esp_pasillos == 0 OR $sumatoria_grafica_x_cliente_total_posiciones == 0) {
                              echo ROUND(($sumatoria_grafica_x_cliente_ocu_piso/$sumatoria_cap_ttt)*100, 2)."%";
//                                echo $sumatoria_grafica_x_cliente_ocu_piso;
                            }
                            else {
                              if ($sumatoria_grafica_x_cliente_area_racks > 0) {
                                $sumatoria_grafica_x_cliente_factor_racks = $sumatoria_grafica_x_cliente_total_posiciones / $sumatoria_grafica_x_cliente_area_racks;
                              }
                              else {
                                $sumatoria_grafica_x_cliente_factor_racks = $sumatoria_grafica_x_cliente_total_posiciones;
                              }

                              if ($sumatoria_grafica_x_cliente_esp_pasillos == 0 || $sumatoria_grafica_x_cliente_factor_racks == 0 ) {
                                $sumatoria_grafica_x_cliente_factor_racks_piso = 0;
                              }
                              else {
                                $sumatoria_grafica_x_cliente_factor_racks_piso = $sumatoria_grafica_x_cliente_esp_pasillos / $sumatoria_grafica_x_cliente_factor_racks;
                              }

                              if ($sumatoria_grafica_x_cliente_factor_racks_piso == 0 || $sumatoria_grafica_x_cliente_area_racks == 0) {
                                $sumatoria_grafica_x_cliente_porcentaje_racks_piso = 0;
                              }
                              else {
                                $sumatoria_grafica_x_cliente_porcentaje_racks_piso = $sumatoria_grafica_x_cliente_factor_racks_piso / $sumatoria_grafica_x_cliente_area_racks;
                              }

                              $sumatoria_grafica_x_cliente_oc_total = $sumatoria_grafica_x_cliente_factor_racks_piso+$sumatoria_grafica_x_cliente_ocu_piso;
                              echo ROUND(($sumatoria_grafica_x_cliente_oc_total/$sumatoria_grafica_x_cliente_tamanio_bod)*100, 2)."%";
                              //echo "here";
                            }
                            ?>
                            </td>
                            </tr>
                            <?php
                            }
                          }
                        ?>

                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              <?php }?>
              <?php }?>
            <?php if ($plaza == 'ALL' && $almacen == 'ALL') {?>

            <?php }?>
          </div>
        </div><!--/.box-body-->
      </div>
    </div>

    <?php //if ($plaza != 'ALL'){ ?>
    <div class="col-md-3" >
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
          <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="calculoOcupacionAlo.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
          <?php } ?>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <!-- FILTRAR POR fecha -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
            <input type="text" class="form-control pull-right" name="fil_fecha" disabled>
            <span class="input-group-addon"> <input type="checkbox" name="fil_check" <?php if( $fil_check == 'on' ){ echo "checked";} ?> > </span>
          </div>
          <!-- FILTRAR POR PLAZA -->
          <div class="input-group" style="display:none">
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
          <!-- FILTRAR POR ALMACEN -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Almacen:</span>
            <select class="form-control select2" style="width: 100%;" id="nomAlm">
              <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $plazas = $_GET["plaza"];
              $selectAlmacen = $obj_class->almacenSql("BAJIO");
              for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
                <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
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

    <!--Subir excel -->
    <?php
      $valor_usuario = $_SESSION['usuario'];
      if ($valor_usuario == "jose_cba" || $valor_usuario == "diego13" || $valor_usuario == 'david' || $valor_usuario == 'fernando_s' || $valor_usuario == 'martin' || $valor_usuario == "victor_cs" || $valor_usuario == "hdaniel" || $valor_usuario == "carlosd" || $valor_usuario == "guisa" || $valor_usuario == "rubenkl"  || $valor_usuario == "lmreyes" || $valor_usuario == "rlopez" || $valor_usuario == "paco_pue" || $valor_usuario == "octaviogm" || $valor_usuario == "heoc810508" || $valor_usuario == "florescf" ) {
    ?>
    <div class="col-md-3" style="display:none;">
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


    <div class="col-md-9" style="display:none;">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-info-circle"></i> Informacion Por Plaza</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body">
          <div class="row">
                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/rack.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Posiciones Utilizadas Racks</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      echo "<button disabled class='btn bg-gray  btn-block' >".number_format(round($sumatoria_espacio_pasillos, 2))."</button>";
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Metros Ocupados En Piso</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      echo "<button disabled class='btn bg-gray  btn-block' >".number_format(round($sumatoria_metros_ocu_piso, 2))."</button>";
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/rack.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Total Posiciones Rack</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      echo "<button disabled class='btn bg-gray  btn-block' >".number_format(round($sumatoria_total_posiciones, 2))."</button>";
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Mts. Disponibles</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      echo "<button disabled class='btn bg-gray  btn-block' >".number_format(round($sumatoria_uso_variado, 2))."</button>";
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/rack.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Ubicaciones Vacias (Racks)</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      echo "<button disabled class='btn bg-gray  btn-block' >".number_format(round($sumatoria_ubicacions_vacias_racks, 2))."</button>";
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Ubicaciones Vacias (Mts Piso)</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      echo "<button disabled class='btn bg-gray  btn-block' >".number_format(round($sumatoria_espacio_libre, 2))."</button>";
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/rack.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>% Ubicacion Racks</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      if ($sumatoria_espacio_pasillos == 0 || $capacidad_almacen == 0 ) {
                        echo "<button disabled class='btn bg-gray  btn-block' >0.00%</button>";
                      }
                      else {
                        echo "<button disabled class='btn bg-gray  btn-block' >".number_format(ROUND(($sumatoria_espacio_pasillos / $sumatoria_total_posiciones)*100, 2))."%"."</button>";
                      }
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>% Ubicacion Mts. Piso</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      if ($sumatoria_metros_ocu_piso == 0 || $sumatoria_uso_variado == 0) {
                        echo "<button disabled class='btn bg-gray  btn-block' > 0.00%</button>";
                      }
                      else {
                        echo "<button disabled class='btn bg-gray  btn-block' >".number_format(ROUND(($sumatoria_metros_ocu_piso/ $sumatoria_uso_variado)*100, 2))."%"."</button>";
                      }
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/rack.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>% De Espacio Libre Racks</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      if ($sumatoria_ubicacions_vacias_racks == 0 || $sumatoria_total_posiciones == 0) {
                        echo "<button disabled class='btn bg-gray  btn-block' > 0.00%</button>";
                      }
                      else {
                        echo "<button disabled class='btn bg-gray  btn-block' >".number_format(ROUND(($sumatoria_ubicacions_vacias_racks/ $sumatoria_total_posiciones)*100), 2)."%"."</button>";
                      }
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>% De Espacio Libre Mts Piso</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      if ($sumatoria_espacio_libre == 0 || $sumatoria_uso_variado == 0) {
                          echo "<button disabled class='btn bg-gray  btn-block' >0.00%"."</button>";
                      }
                      else {
                          echo "<button disabled class='btn bg-gray  btn-block' >".number_format(ROUND(($sumatoria_espacio_libre/ $sumatoria_uso_variado)*100, 2))."%"."</button>";
                      }
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/rack.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Factor Racks</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      echo "<button disabled class='btn bg-gray  btn-block' >".number_format(ROUND($factor_racks , 2))."</button>";
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Ocupación Racks Piso</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      echo "<button disabled class='btn bg-gray  btn-block' >".number_format(ROUND($factor_racks_piso , 2))."</button>";
                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Porcentaje Racks Piso</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      if ($porcentaje_factor_racks_piso == 0) {
                        echo "<button disabled class='btn bg-gray  btn-block' >0.00</button>";
                      }
                      else {
                        echo "<button disabled class='btn bg-gray  btn-block' >".number_format(ROUND(($porcentaje_factor_racks_piso)*100 , 2))."</button>";
                      }

                      ?>
                    </a>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class=" info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Tamaño De Bodega</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando">
                      <?php
                      echo "<button disabled class='btn bg-gray  btn-block' >".number_format(ROUND($sumatoria_plaza_tamanio_bodega, 2))."</button>";
                      ?>
                    </a>
                  </div>
                </div>

            </div>
          </div>
        </div>
      </div>

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
<?php if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){ ?>
  $('input[name="fil_fecha"]').attr("disabled", false);
<?php } ?>
$('input[name="fil_check"]').on("click", function (){

  if ($('input[name="fil_check"]').is(':checked')) {
    $('input[name="fil_fecha"]').attr("disabled", false);
  }else{
    $('input[name="fil_fecha"]').attr("disabled", true);
  }

});

// CHECA AREAS
$("#fil_departamento").change(function (){

  $.ajax({
    type: 'post',
    url: '../action/rotacion_personal.php',
    data: { "depto" : $(this).val() },
    beforeSend: function () {
      //$('#fil_area').remove();
      $('#fil_area')
      .empty()
      .append('<option value="ALL">ALL</option>');
    },
    success: function (response) {// success
      var dataJson = JSON.parse(response);
        var $select = $('#fil_area');
        //$select.append('<option></option>');
        $.each(dataJson, function(i, val){
          $select.append($('<option></option>').attr('value', val.IID_AREA).text( val.V_DESCRIPCION ));
        });

    }// ./succes
  });

});

//BOTON FILTRAR
$(".btn_fil").on("click", function(){

  fil_fecha = $('input[name="fil_fecha"]').val();
  fil_plaza = $('#fil_plaza').val();
  almacen = $('#nomAlm').val();
  fil_contrato = $('#fil_contrato').val();
  fil_departamento = $('#fil_departamento').val();
  fil_area = $('#fil_area').val();
  fil_check = 'off';

  //Fill habilitados
  fil_habilitado = 'off';

  url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
  if ($('input[name="fil_check"]').is(':checked')) {
    fil_check = 'on';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
    }

  }else{
    fil_check = 'off';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
        fil_habilitado = 'on';
        url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado;
    }
    //url = '?plaza='+fil_plaza+'&check='+fil_check+'&contrato='+fil_contrato+'&depto='+fil_departamento+'&area='+fil_area;
  }

  location.href = url;

});

$('.select2').select2()
</script>
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<script type="text/javascript">

$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($graficaMensual) ; $i++) {
       if ($graficaMensual[$i]["MTS_UTILIZADOS"] > 0 OR $graficaMensual[$i]["MTS_PASILLOS_PASILLOS"] > 0) {
         echo "'".$graficaMensual[$i]["MES"]."',";
       }
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaMensual) ; $i++) {
      if ($graficaMensual[$i]["MTS_UTILIZADOS"] > 0 OR $graficaMensual[$i]["MTS_PASILLOS_PASILLOS"] > 0) {
        $sumatoria_grafica_x_mes_esp_pasillos = 0;
        $sumatoria_grafica_x_mes_esp_pasillos = $graficaMensual[$i]["MTS_UTILIZADOS"];
        $sumatoria_grafica_x_mes_ocu_piso = 0;
        $sumatoria_grafica_x_mes_ocu_piso = $graficaMensual[$i]["MTS_PASILLOS_PASILLOS"];
        $sumatoria_grafica_x_mes_total_posiciones = 0;
        $sumatoria_grafica_x_mes_total_posiciones = $graficaMensual[$i]["MTS_RACKS"];
        $sumatoria_grafica_x_mes_uso_variado = 0;
        $sumatoria_grafica_x_mes_uso_variado_solo = 0;
        $sumatoria_grafica_x_mes_uso_variado_solo = $graficaMensual[$i]["USO_VARIADO"];
        $sumatoria_grafica_x_mes_uso_variado = $graficaMensual[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_mes_uso_variado_solo;
        $sumatoria_grafica_x_mes_ubicaciones_vacias_racks = $graficaMensual[$i]["MTS_RACKS"]- $sumatoria_grafica_x_mes_esp_pasillos;
        $sumatoria_grafica_x_mes_espacio_libre = $sumatoria_grafica_x_mes_uso_variado- $sumatoria_grafica_x_mes_ocu_piso;
        $sumatoria_grafica_x_mes_area_racks = $graficaMensual[$i]["AREA_RACKS"];
        $sumatoria_grafica_x_mes_tamanio_bod = $graficaMensual[$i]["TAMANIO_BODEGA"];
            if ($sumatoria_grafica_x_mes_area_racks == 0) {
              //echo 0;
                echo ROUND(($sumatoria_grafica_x_mes_ocu_piso/$graficaMensual[$i]["CAPACIDAD_TOTAL"])*100, 0, PHP_ROUND_HALF_EVEN).",";
            }else {

                if ($sumatoria_grafica_x_mes_area_racks > 0) {
                  $sumatoria_grafica_x_mes_factor_racks = $sumatoria_grafica_x_mes_total_posiciones / $sumatoria_grafica_x_mes_area_racks;
                }
                else {
                  $sumatoria_grafica_x_mes_factor_racks =$sumatoria_grafica_x_mes_total_posiciones;
                }

                if ($sumatoria_grafica_x_mes_esp_pasillos == 0 || $sumatoria_grafica_x_mes_factor_racks == 0) {
                  $sumatoria_grafica_x_mes_factor_racks_piso = 0;
                }
                else {
                  $sumatoria_grafica_x_mes_factor_racks_piso = $sumatoria_grafica_x_mes_esp_pasillos / $sumatoria_grafica_x_mes_factor_racks;
                }

                if ($sumatoria_grafica_x_mes_factor_racks_piso == 0 || $sumatoria_grafica_x_mes_area_racks == 0 ) {
                  $sumatoria_grafica_x_mes_porcentaje_racks_piso = 0;
                }
                else {
                  $sumatoria_grafica_x_mes_porcentaje_racks_piso = $sumatoria_grafica_x_mes_factor_racks_piso / $sumatoria_grafica_x_mes_area_racks;
                }

                $sumatoria_grafica_x_mes_oc_total = $sumatoria_grafica_x_mes_factor_racks_piso+$sumatoria_grafica_x_mes_uso_variado_solo+$sumatoria_grafica_x_mes_ocu_piso;

                echo ROUND(($sumatoria_grafica_x_mes_oc_total/$sumatoria_grafica_x_mes_tamanio_bod)*100, 0, PHP_ROUND_HALF_EVEN).",";


            }
      }
    }
    ?>
    ];
    $('#graf_bar').highcharts({
        chart: {
            type: 'line'
        },
         title: {
            text: 'OCUPACIÓN MENSUAL <?php if ($plaza != 'ALL' && $almacen == 'ALL') {echo "PLAZA ".$plaza;} else{$nombreAlmacen = $obj_class->almacenNombre($plaza,$almacen); echo "ALMACEN ".$nombreAlmacen[0]["V_NOMBRE"]; } ?>'
        },

        legend: {
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },

        yAxis: {
            lineWidth: 2,
            min: 0,
            offset: 10,
            tickWidth: 1,
            title: {
                text: 'PORCENTAJE GLOBAL'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: false,
          valueSuffix: ' ',
          useHTML: false,
          valueSuffix: ' %',
        },
        lang: {
          printChart: 'Imprimir Grafica',
          downloadPNG: 'Descargar PNG',
          downloadJPEG: 'Descargar JPEG',
          downloadPDF: 'Descargar PDF',
          downloadSVG: 'Descargar SVG',
          contextButtonTitle: 'Exportar grafica'
        },
        credits: {
            enabled: false
        },
        colors: ['#0073B7', '#D81B60'],
        plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: true,
              format: '{y} %'
            },
            enableMouseTracking:false
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>';
                return '<a>' +
                    this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: '',
          align: 'right',
          x: -10,
        },
        series:  [{
            showInLegend:false,
            name: ' PORCENTAJE GLOBAL',
            data: data1,
        }]

    });
});
</script>


<script type="text/javascript">

$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($grafica) ; $i++) {
       if ($grafica[$i]["MTS_UTILIZADOS"] > 0 OR $grafica[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
         echo "'".$grafica[$i]["PLAZA"]."',";
       }
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($grafica) ; $i++) {
      if ($grafica[$i]["MTS_UTILIZADOS"] > 0 OR $grafica[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
        $sumatoria_grafica_plaza_esp_pasillos = 0;
        $sumatoria_grafica_plaza_esp_pasillos = $grafica[$i]["MTS_UTILIZADOS"];
        $sumatoria_grafica_plaza_ocu_piso = 0;
        $sumatoria_grafica_plaza_ocu_piso = $grafica[$i]["MTS_PASILLOS_ESPACIO"];
        $sumatoria_grafica_plaza_total_posiciones = 0;
        $sumatoria_grafica_plaza_total_posiciones = $grafica[$i]["MTS_RACKS"];
        $sumatoria_grafica_plaza_uso_variado = 0;
        $sumatoria_grafica_plaza_uso_variado_solo = 0;
        $sumatoria_grafica_plaza_uso_variado_solo = $grafica[$i]["USO_VARIADO"];
        $sumatoria_grafica_plaza_uso_variado = $grafica[$i]["CAPACIDAD_TOTAL"] - $sumatoria_grafica_plaza_uso_variado_solo;
        $sumatoria_grafica_plaza_ubicaciones_vacias_racks = $sumatoria_grafica_plaza_total_posiciones- $sumatoria_grafica_plaza_esp_pasillos;
        $sumatoria_grafica_plaza_espacio_libre = $sumatoria_grafica_plaza_uso_variado-$sumatoria_grafica_plaza_ocu_piso;
        $sumatoria_grafica_plaza_area_racks = $grafica[$i]["AREA_RACKS"];
        $sumatoria_grafica_plaza_tamanio_bodega = $grafica[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_plaza_area_racks == 0) {
          echo ROUND(($sumatoria_grafica_plaza_ocu_piso/$grafica[$i]["CAPACIDAD_TOTAL"] )*100, 2).",";
          //echo "1, ";
        }
        else {
          if ($sumatoria_grafica_plaza_area_racks > 0 ) {
            $sumatoria_grafica_plaza_factor_racks = $sumatoria_grafica_plaza_total_posiciones / $sumatoria_grafica_plaza_area_racks;
          }
          else {
            $sumatoria_grafica_plaza_factor_racks = $sumatoria_grafica_plaza_total_posiciones;
          }

          if ($sumatoria_grafica_plaza_esp_pasillos == 0 OR $sumatoria_grafica_plaza_factor_racks == 0) {
            $sumatoria_grafica_plaza_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_plaza_factor_racks_piso = $sumatoria_grafica_plaza_esp_pasillos/$sumatoria_grafica_plaza_factor_racks;
          }

          if ($sumatoria_grafica_plaza_factor_racks_piso == 0 OR $sumatoria_grafica_plaza_area_racks == 0) {
            $sumatoria_grafica_plaza_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_plaza_porcentaje_racks_piso = $sumatoria_grafica_plaza_factor_racks_piso/$sumatoria_grafica_plaza_area_racks;
          }

          $sumatoria_grafica_plaza_oc_total = $sumatoria_grafica_plaza_factor_racks_piso+$sumatoria_grafica_plaza_uso_variado_solo+$sumatoria_grafica_plaza_ocu_piso;

          //echo "0 , ";
          echo ROUND(($sumatoria_grafica_plaza_oc_total/$sumatoria_grafica_plaza_tamanio_bodega)*100, 2).",";
        }

        //echo $grafica[$i]["MTS_UTILIZADOS"].",";
      }
    }
    ?>
    ];
    $('#graf_perM').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'OCUPACIÓN PLAZA'
        },

        legend: {
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },

        yAxis: {
            lineWidth: 2,
            min: 0,
            offset: 10,
            tickWidth: 1,
            title: {
                text: 'PORCENTAJE GLOBAL'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' ',
          useHTML: true,
          valueSuffix: ' %',
        },
        lang: {
          printChart: 'Imprimir Grafica',
          downloadPNG: 'Descargar PNG',
          downloadJPEG: 'Descargar JPEG',
          downloadPDF: 'Descargar PDF',
          downloadSVG: 'Descargar SVG',
          contextButtonTitle: 'Exportar grafica'
        },
        credits: {
            enabled: false
        },
        colors: ['#0073B7', '#D81B60'],
        plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: true,
              format: '{y} %'
            },
            enableMouseTracking:false
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>';
                return '<a>' +
                    this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: '',
          align: 'right',
          x: -10,
        },
        series:  [{
        //  showInLegend:false,
            name: ' PORCENTAJE GLOBAL',
            data: data1,
        }]

    });
});
</script>

<script type="text/javascript">

$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($graficaAlmacen) ; $i++) {
       if ($graficaAlmacen[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
          echo "'".$graficaAlmacen[$i]["PLAZA"]."',";
       }
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaAlmacen) ; $i++) {
      if ($graficaAlmacen[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
        $sumatoria_grafica_x_almacen_esp_pasillos = 0;
        $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen[$i]["MTS_UTILIZADOS"];
        $sumatoria_grafica_x_almacen_ocu_piso = 0;
        $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen[$i]["MTS_PASILLOS_ESPACIO"];
        $sumatoria_grafica_x_almacen_total_posiciones = 0;
        $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen[$i]["MTS_RACKS"];
        $sumatoria_grafica_x_almacen_uso_variado = 0;
        $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
        $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen[$i]["USO_VARIADO"];
        $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
        $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
        $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
        $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen[$i]["AREA_RACKS"];
        $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen[$i]["TAMANIO_BODEGA"];
          if ($sumatoria_grafica_x_almacen_area_racks == 0) {
            echo round(($sumatoria_grafica_x_almacen_ocu_piso/$sumatoria_grafica_x_almacen_tamanio_bod) * 100, 2). ", ";
          }else {

            if ($sumatoria_grafica_x_almacen_area_racks > 0) {
              $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
            }
            else {
              $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
            }

            if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
              $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
            }
            else {
              $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
            }

            if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
              $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
            }
            else {
              $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
            }

            $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

            //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
            #echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
            echo round($sumatoria_grafica_x_almacen_esp_pasillos, 2). ", ";
        }
      }
    }
    ?>
    ];
    $('#graf_perAlmacen').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'OCUPACIÓN POR ALMACEN DE PLAZA <?php echo $plaza; ?>'
        },

        legend: {
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },

        yAxis: {
            lineWidth: 2,
            min: 0,
            offset: 10,
            tickWidth: 1,
            title: {
                text: 'PORCENTAJE'
            },
        },
        tooltip: {
          shared: true,
          valueSuffix: ' ',
          useHTML: true,
          //valueDecimals: 2,
          //valuePrefix: '$',
          valueSuffix: ' %'
        },
        lang: {
          printChart: 'Imprimir Grafica',
          downloadPNG: 'Descargar PNG',
          downloadJPEG: 'Descargar JPEG',
          downloadPDF: 'Descargar PDF',
          downloadSVG: 'Descargar SVG',
          contextButtonTitle: 'Exportar grafica'
        },
        credits: {
            enabled: false
        },
        colors: ['#0073B7', '#D81B60'],
        plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: true,
              format: '{y} %'
            },
            enableMouseTracking:false
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>';
                return '<a>' +
                    this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: '',
          align: 'right',
          x: -10,
        },
        series:  [{
        //  showInLegend:false,
            name: ' PORCENTAJE GLOBAL ',
            data: data1,
        }]

    });
});
</script>


<script type="text/javascript">

$(function () {
  var data1 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'ALINK');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          //echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_UTILIZADOS"], 2).", ";
      }
    }
  }
  ?>
  ];
  var data2 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'FCA');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          //echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];

  var data3 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'BMW');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          #echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];
  var data4 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'DICASTAL');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          //echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];

  var data5 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'FORD');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          //echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];

  var data6 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'HANDS');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          #echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];

  var data7 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'HANKOOK');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          #echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];
  var data8 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'HONDA');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          #echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];

  var data9 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'LING-LONG');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          #echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];

  var data10 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'LIUFENG');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          #echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];

  var data11 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'SAAA');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          #echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];

  var data12 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'0');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          #echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
      }
    }
  }
  ?>
  ];

  var data13 = [
    <?php
     for ($i=0; $i <count($graficaAlmacen) ; $i++) {
       if ($graficaAlmacen[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
         $uv =$graficaAlmacen[$i]["USO_VARIADO"];
         //echo $uv.",";
         echo ROUND(($uv/$graficaAlmacen[$i]["TAMANIO_BODEGA"])*100, 2).",";
       }
     }
    ?>
  ];

  var data14 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'MAXXIS');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          //echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
            echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          #echo ROUND($graficaAlmacen2[$i]["MTS_UTILIZADOS"], 2).", ";
            echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];


  var data15 = [
  <?php
  $graficaAlmacen2 = $obj_class->graficaAlmacenProyecto($plaza,$fecha,$fil_check,'VARIOS');
  for ($i=0; $i <count($graficaAlmacen2) ; $i++) {
    if ($graficaAlmacen2[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
      $sumatoria_grafica_x_almacen_esp_pasillos = 0;
      $sumatoria_grafica_x_almacen_esp_pasillos = $graficaAlmacen2[$i]["MTS_UTILIZADOS"];
      $sumatoria_grafica_x_almacen_ocu_piso = 0;
      $sumatoria_grafica_x_almacen_ocu_piso = $graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"];
      $sumatoria_grafica_x_almacen_total_posiciones = 0;
      $sumatoria_grafica_x_almacen_total_posiciones = $graficaAlmacen2[$i]["MTS_RACKS"];
      $sumatoria_grafica_x_almacen_uso_variado = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = 0;
      $sumatoria_grafica_x_almacen_uso_variado_solo = $graficaAlmacen2[$i]["USO_VARIADO"];
      $sumatoria_grafica_x_almacen_uso_variado = $graficaAlmacen2[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_almacen_uso_variado_solo;
      $sumatoria_grafica_x_almacen_ubicaciones_vacias_racks = $graficaAlmacen2[$i]["MTS_RACKS"]- $sumatoria_grafica_x_almacen_esp_pasillos;
      $sumatoria_grafica_x_almacen_espacio_libre = $sumatoria_grafica_x_almacen_uso_variado- $sumatoria_grafica_x_almacen_ocu_piso;
      $sumatoria_grafica_x_almacen_area_racks = $graficaAlmacen2[$i]["AREA_RACKS"];
      $sumatoria_grafica_x_almacen_tamanio_bod = $graficaAlmacen2[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_almacen_area_racks == 0) {
          //echo ROUND(($sumatoria_grafica_x_almacen_ocu_piso/$graficaAlmacen2[$i]["CAPACIDAD_TOTAL"])*100, 2).",";
            echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
        }else {

          if ($sumatoria_grafica_x_almacen_area_racks > 0) {
            $sumatoria_grafica_x_almacen_factor_racks = $sumatoria_grafica_x_almacen_total_posiciones / $sumatoria_grafica_x_almacen_area_racks;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks =$sumatoria_grafica_x_almacen_total_posiciones;
          }

          if ($sumatoria_grafica_x_almacen_esp_pasillos == 0 || $sumatoria_grafica_x_almacen_factor_racks == 0) {
            $sumatoria_grafica_x_almacen_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_factor_racks_piso = $sumatoria_grafica_x_almacen_esp_pasillos / $sumatoria_grafica_x_almacen_factor_racks;
          }

          if ($sumatoria_grafica_x_almacen_factor_racks_piso == 0 || $sumatoria_grafica_x_almacen_area_racks == 0 ) {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_almacen_porcentaje_racks_piso = $sumatoria_grafica_x_almacen_factor_racks_piso / $sumatoria_grafica_x_almacen_area_racks;
          }

          $sumatoria_grafica_x_almacen_oc_total = $sumatoria_grafica_x_almacen_factor_racks_piso+$sumatoria_grafica_x_almacen_uso_variado_solo+$sumatoria_grafica_x_almacen_ocu_piso;

          //echo ROUND(($por_total)*100, 0, PHP_ROUND_HALF_EVEN) ."%";
          //echo ROUND(($sumatoria_grafica_x_almacen_oc_total/$sumatoria_grafica_x_almacen_tamanio_bod)*100, 2).",";
          #echo ROUND($graficaAlmacen2[$i]["MTS_UTILIZADOS"], 2).", ";
            echo ROUND($graficaAlmacen2[$i]["MTS_PASILLOS_ESPACIO"], 2).", ";
      }
    }
  }
  ?>
  ];

  Highcharts.chart('graf_perAlmacen2', {

    chart: {
        type: 'column'
    },

    title: {
        text: 'PORCENTAJE TOTAL POR ALMACEN'
    },

    xAxis: {
        //categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']}
        categories: [
          <?php
           for ($i=0; $i <count($graficaAlmacen) ; $i++) {
             if ($graficaAlmacen[$i]["MTS_UTILIZADOS"] > 0 OR $graficaAlmacen[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
                echo "'".$graficaAlmacen[$i]["PLAZA"]."',";
             }
           }
          ?>
        ]
    },


    yAxis: {
        allowDecimals: false,
        min: 0,
        title: {
            text: '% OCUPACION'
        }
    },

    tooltip: {
        formatter: function () {
            if (this.series.name === 'BMW') {
              return '<b>' + this.x + '</b><br/>' +
                  this.series.name + ': ' + this.y + '<br/>' +
                  'BMW LIBRE: '+ Math.round((504-this.y)) + 'm2<br/>'+
                  'BMW OCUPADO: '+ Math.round((this.y/504)*100) + '%<br/>'+
                  'Total: ' + this.point.stackTotal;
            }else {
              return '<b>' + this.x + '</b><br/>' +
                  this.series.name + ': ' + this.y + '<br/>' +
                  'Total: ' + this.point.stackTotal;
            }

        }
    },

    plotOptions: {
        column: {
            stacking: 'normal'
        }
    },

    series: [{
        name: 'ALINK',
        data: data1,
        stack: 'male'
    }, {
        name: 'BMW',
        data: data3,
        stack: 'male'
    }, {
        name: 'DICASTAL',
        data: data4,
        stack: 'male'
    }, {
        name: 'FCA',
        data: data2,
        stack: 'male'
    },  {
        name: 'FORD',
        data: data5,
        stack: 'male'
    }, {
        name: 'HANDS',
        data: data6,
        stack: 'male'
    }, {
        name: 'HANKOOK',
        data: data7,
        stack: 'male'
    }, {
        name: 'HONDA',
        data: data8,
        stack: 'male'
    }, {
        name: 'LING-LONG',
        data: data9,
        stack: 'male'
    }, {
        name: 'LIUFENG',
        data: data10,
        stack: 'male'
    }, {
        name: 'SAAA',
        data: data11,
        stack: 'male'
    }, {
        color:'pink',
        name: 'Cuarentena',
        data: data12,
        stack: 'male'
    }, {
        color:'red',
        name: 'MAXXIS',
        data: data14,
        stack: 'male'

    },{
        color:'black',
        name: 'Varios',
        data: data15,
        stack: 'male'

    }]
});

});
</script>

<style>
.highcharts-figure, .highcharts-data-table table {
  min-width: 310px;
  max-width: 800px;
  margin: 1em auto;
}

#container {
  height: 400px;
}

.highcharts-data-table table {
font-family: Verdana, sans-serif;
border-collapse: collapse;
border: 1px solid #EBEBEB;
margin: 10px auto;
text-align: center;
width: 100%;
max-width: 500px;
}
.highcharts-data-table caption {
  padding: 1em 0;
  font-size: 1.2em;
  color: #555;
}
.highcharts-data-table th {
font-weight: 600;
  padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
  padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
  background: #f8f8f8;
}
.highcharts-data-table tr:hover {
  background: #f1f7ff;
}

</style>

<!--
<script type="text/javascript">

$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($graficaCliente) ; $i++) {
       if ($graficaCliente[$i]["MTS_UTILIZADOS"] > 0 OR $graficaCliente[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
          echo "'".$graficaCliente[$i]["PLAZA"]."',";
       }
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaCliente) ; $i++) {
      if ($graficaCliente[$i]["MTS_UTILIZADOS"] > 0 OR $graficaCliente[$i]["MTS_PASILLOS_ESPACIO"] > 0) {
        $sumatoria_grafica_x_cliente_esp_pasillos = 0;
        $sumatoria_grafica_x_cliente_esp_pasillos = $graficaCliente[$i]["MTS_UTILIZADOS"];
        $sumatoria_grafica_x_cliente_ocu_piso = 0;
        $sumatoria_grafica_x_cliente_ocu_piso = $graficaCliente[$i]["MTS_PASILLOS_ESPACIO"];
        $sumatoria_grafica_x_cliente_total_posiciones = 0;
        $sumatoria_grafica_x_cliente_total_posiciones = $graficaCliente[$i]["MTS_RACKS"];
        $sumatoria_grafica_x_cliente_uso_variado = 0;
        $sumatoria_grafica_x_cliente_uso_variado_solo = 0;
        $sumatoria_grafica_x_cliente_uso_variado_solo = $graficaCliente[$i]["USO_VARIADO"];
        $sumatoria_grafica_x_cliente_uso_variado = $graficaCliente[$i]["CAPACIDAD_TOTAL"]-$sumatoria_grafica_x_cliente_uso_variado_solo;
        $sumatoria_grafica_x_cliente_ubicaciones_vacias_racks = $graficaCliente[$i]["MTS_RACKS"]- $sumatoria_grafica_x_cliente_esp_pasillos;
        $sumatoria_grafica_x_cliente_espacio_libre = $sumatoria_grafica_x_cliente_uso_variado- $sumatoria_grafica_x_cliente_ocu_piso;
        $sumatoria_grafica_x_cliente_area_racks = $graficaCliente[$i]["AREA_RACKS"];
        $sumatoria_grafica_x_cliente_tamanio_bod = $graficaCliente[$i]["TAMANIO_BODEGA"];
        if ($sumatoria_grafica_x_cliente_area_racks == 0) {
          echo ROUND(($sumatoria_grafica_x_cliente_ocu_piso/$sumatoria_grafica_x_cliente_tamanio_bod)*100, 2).",";
        }
        else {
          if ($sumatoria_grafica_x_cliente_area_racks > 0) {
            $sumatoria_grafica_x_cliente_factor_racks = $sumatoria_grafica_x_cliente_total_posiciones / $sumatoria_grafica_x_cliente_area_racks;
          }
          else {
            $sumatoria_grafica_x_cliente_factor_racks = $sumatoria_grafica_x_cliente_total_posiciones;
          }

          if ($sumatoria_grafica_x_cliente_esp_pasillos == 0 || $sumatoria_grafica_x_cliente_factor_racks == 0 ) {
            $sumatoria_grafica_x_cliente_factor_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_cliente_factor_racks_piso = $sumatoria_grafica_x_cliente_esp_pasillos / $sumatoria_grafica_x_cliente_factor_racks;
          }

          if ($sumatoria_grafica_x_cliente_factor_racks_piso == 0 || $sumatoria_grafica_x_cliente_area_racks == 0) {
            $sumatoria_grafica_x_cliente_porcentaje_racks_piso = 0;
          }
          else {
            $sumatoria_grafica_x_cliente_porcentaje_racks_piso = $sumatoria_grafica_x_cliente_factor_racks_piso / $sumatoria_grafica_x_cliente_area_racks;
          }

          $sumatoria_grafica_x_cliente_oc_total = $sumatoria_grafica_x_cliente_factor_racks_piso+$sumatoria_grafica_x_cliente_uso_variado_solo+$sumatoria_grafica_x_cliente_ocu_piso;
          echo ROUND(($sumatoria_grafica_x_cliente_oc_total/$sumatoria_grafica_x_cliente_tamanio_bod)*100, 2).",";
        }
      }
    }
    ?>
    ];
    $('#graf_perCliente').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'OCUPACIÓN POR CLIENTE DE PLAZA <?php echo $plaza; ?>'
        },

        legend: {
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },

        yAxis: {
            lineWidth: 2,
            min: 0,
            offset: 10,
            tickWidth: 1,
            title: {
                text: 'PORCENTAJE'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' ',
          useHTML: true,
          valueSuffix: ' %'
        },
        lang: {
          printChart: 'Imprimir Grafica',
          downloadPNG: 'Descargar PNG',
          downloadJPEG: 'Descargar JPEG',
          downloadPDF: 'Descargar PDF',
          downloadSVG: 'Descargar SVG',
          contextButtonTitle: 'Exportar grafica'
        },
        credits: {
            enabled: false
        },
        colors: ['#0073B7', '#D81B60'],
        plotOptions: {
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
              url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>';
                return '<a>' +
                    this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: '',
          align: 'right',
          x: -10,
        },
        series:  [{
        //  showInLegend:false,
            name: ' PORCENTAJE GLOBAL',
            data: data1,
        }]

    });
});
</script>-->
<script type="text/javascript">
$(document).ready(function() {
    $('#tabla_nomina').DataTable( {
        "searching": false,
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                    i : 0;
            };

            total = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return Intl.NumberFormat().format(intVal(a) + intVal(b));
                }, 0 );

            pageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return Intl.NumberFormat().format(intVal(a) + intVal(b));
                }, 0 );

            $( api.column( 5 ).footer() ).html(
                ''+pageTotal +' Toneladas'
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

</script>


<script type="text/javascript">
function cambiar(){
    var pdrs = document.getElementById('file').files[0].name;
    document.getElementById('info').innerHTML = pdrs;
    document.getElementById('submit').disabled = false;
}
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
