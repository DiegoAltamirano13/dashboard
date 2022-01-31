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

                $porcentaje = 0.00;
                if(isset($Row[17])) {
                  if (is_null($Row[17]) OR empty($Row[17])) {
                      $porcentaje = 0;
                  }
                  else {
                      $porcentaje = $Row[17];
                  }
                }

                $porcentajeRacks = 0.00;
                if(isset($Row[18])) {
                  if (is_null($Row[18]) OR empty($Row[18])) {
                      $porcentajeRacks = 0;
                  }
                  else {
                      $porcentajeRacks = $Row[18];
                  }
                }
              #  echo $porcentajeRacks;


                if (!empty($id_cliente) || !empty($id_plaza) || !empty($id_almacen) || !empty($semana) || !empty($mes) || !empty($anio) || !empty($ocupacion) || !empty($mtsutiles) || !empty($mtspasillo) || !empty($mts_almacen) || !empty($mts_rack_almacen)) {
                  //QUERY CONSULTA
                    $consulta = "SELECT COUNT(*)AS ID FROM PRUEBA_SUBIDA WHERE ID_CLIENTE = ".$id_cliente." AND IID_PLAZA = ".$id_plaza." AND IID_ALMACEN = ".$id_almacen." AND SEMANA = ".$semana." AND MES = ".$mes." AND ANIO = ".$anio."  AND OCUPACION = ".$ocupacion." AND T_PROYECTO = '".$proyecto."'";
                    ##echo $consulta;
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

                      #echo $query;
                      $lanza = oci_execute($sti);

                            if (!empty($lanza)) {
                              $consulta_almacenes = "SELECT COUNT(*) AS NUMBERALMACEN FROM ALMACEN_CAPACIDAD WHERE IID_PLAZA = ".$id_plaza." AND IID_ALMACEN = ".$id_almacen. " AND ANIO = ".$anio." AND MES = ".$mes." AND SEMANA = ".$semana."";
                              $still2 = oci_parse($conn, $consulta_almacenes);
                              oci_execute($still2);

                              $query3 = "INSERT INTO OP_IN_PORCENTAJE_OCUPACION (IID_CLIENTE, IID_PLAZA, IID_ALMACEN, ANIO, MES, SEMANA, PORCENTAJE, PORCENTAJE_RACKS)
                                        VALUES (".$id_cliente.", ".$id_plaza.", ".$id_almacen.", ".$anio.", ".$mes.", ".$semana.", ".$porcentaje.", ".$porcentajeRacks.")";
                              $sti3 = oci_parse($conn, $query3);
                              $lanza3 = oci_execute($sti3);
                              #echo $query3;
                              while (oci_fetch($still2)) {
                                  $reg2 = oci_result($still2, "NUMBERALMACEN");
                                  if ($reg2 > 0) {

                                  }
                                  else {
                                    //INSERTA CAPACIDAD DE ALMACEN DEL MISMO EXCEL
                                      $query2 = "INSERT INTO ALMACEN_CAPACIDAD (IID_PLAZA, IID_ALMACEN, ANIO, MES, SEMANA, MTS_RACKS, CAPACIDAD_TOTAL, USO_VARIADOS, AREA_RACKS, TAMANIO_BODEGA, VIA_REAL, VIA_TRADICIONAL) VALUES (".$id_plaza.", ".$id_almacen.", ".$anio.", ".$mes.", ".$semana.", ".$mts_rack_almacen.", ".$mts_capacidad_total.", ".$usos_variados_piso.", ".$area_racks.", ".$tamanio_bodega.", ".$via_real.", ".$via_tradicional.")";
                                      #echo $query2;
                                      $sti2 = oci_parse($conn, $query2);
                                      $lanza2 = oci_execute($sti2);

                                      if (!empty($lanza2)) {
                                          $type = "success";
                                          $message = "Excel Importado Correctamente!!!";
                                      } else {
                                          $type = "error";
                                          $message = "Problema Importando Excel Almacen!!!";
                                      }
                                  }
                              }
                            } else {
                            #  echo $query;
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

include_once '../class/Calculo_Ocupacion.php';
$obj_class = new Calculo_Ocupacion();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 28);
$modulos_valida2 = Perfil::modulos_valida($_SESSION['iid_empleado'], 42);
if($modulos_valida == 0 AND $modulos_valida2 == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
date_default_timezone_set('UTC');

date_default_timezone_set("America/Mexico_City");
/* $_GET FECHA */
$fecha = 'ALL';
if ( isset($_GET["fecha"]) ){
  if ( $obj_class->validateDate(substr($_GET["fecha"],0,10)) ){
    $fecha = $_GET["fecha"];
    #echo $fecha;
  }else{

    $formatted = vsprintf('%3$04d/%2$02d/%1$02d', sscanf($fecha,'%02d/%02d/%04d'));
    $fechats = strtotime($formatted);
    //echo $fechats;
    switch (date('w', $fechats)){
      case 0: $dia = "Domingo"; break;
      case 1: $dia = "Lunes"; break;
      case 2: $dia = "Martes"; break;
      case 3: $dia = "Miercoles"; break;
      case 4: $dia = "Jueves"; break;
      case 5: $dia = "Viernes"; break;
      case 6: $dia = "Sabado"; break;
    }

    if ($dia == "Lunes") {
      //echo "HERE";
      $fecha = $fecha;
    }else {
      //echo "HERE2";
      $fecha = date("d/m/Y");
      $first = strtotime('last Monday -7 days');
      $fecha =  date('d/m/Y', $first);
    }


  //  echo $fecha;
  }
}else {
  $fecha = date("d/m/Y");
  $formatted = vsprintf('%3$04d/%2$02d/%1$02d', sscanf($fecha,'%02d/%02d/%04d'));
  $fechats = strtotime($formatted);
  //echo $fechats;
  switch (date('w', $fechats)){
    case 0: $dia = "Domingo"; break;
    case 1: $dia = "Lunes"; break;
    case 2: $dia = "Martes"; break;
    case 3: $dia = "Miercoles"; break;
    case 4: $dia = "Jueves"; break;
    case 5: $dia = "Viernes"; break;
    case 6: $dia = "Sabado"; break;
  }

  if ($dia == "Lunes") {
    //echo "HERE3";
    $fecha_actual = date("d-m-Y");
    $fecha = date("d/m/Y",strtotime($fecha_actual."- 7 days"));
    //$fecha =  date('d/m/Y', $first);
  }else {
    //echo "HERE4";
    $first = strtotime('last Monday -7 days');
    $fecha =  date('d/m/Y', $first);
  }

  /*$fecha_ini = date("d/m/Y");
  $fecha = date("d/m/Y", strtotime("$fecha_ini -7 day"));

  $first = strtotime('last Monday -7 days');

  $fecha =  date('d/m/Y', $first);*/
  #echo $fecha;
}
/* $_GET FIL_CHECK */
$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}


if ( $_SESSION['area']==3 ){
  $plaza = $_SESSION['nomPlaza'];
}else {
  $plaza = "ALL";
}

$plaza=$_SESSION['nomPlaza'];

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

$cliente = "ALL";
if (isset($_GET["cliente"])) {
    $cliente = $_GET["cliente"];
}
//GRAFICA si anita lava la tina la tina la bananita

$graficaMensual = $obj_class->grafica($plaza,$fecha,$almacen,$fil_check,$cliente);
$graficaSemana = $obj_class->graficaSemanal($plaza,$fecha,$almacen,$fil_check,$cliente);
$graficaAlmacen = $obj_class->graficaAlmacen($plaza,$fecha,$fil_check,$cliente);
$graficaCliente = $obj_class->graficaCliente($plaza,$fecha,$fil_check,$almacen);
$grafica = $obj_class->graficaMensual($plaza,$fecha,$almacen,$fil_check,$cliente);
$datos = $obj_class->datos($plaza,$fecha,$almacen,$fil_check);
$capacidad_almacen = $obj_class->capacidad_almacen($plaza,$fecha,$fil_check);
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
      <h1>
        Dashboard
        <small>Calculo Ocupación </small>
        <?php //if($_SESSION['area']==3){echo "<center><h4> PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>";} ?><!--FILTRAR UNICAMENTE P/DEPTO. OPERACIONES -->
        <?php echo "<center><h4>PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>"; ?><!--FILTRO GENERAL -->
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
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica de <?php
                                                                                $formatted = vsprintf('%3$04d/%2$02d/%1$02d', sscanf($fecha,'%02d/%02d/%04d'));
                                                                                $fechats = strtotime($formatted);
                                                                                //echo $fechats;
                                                                                switch (date('w', $fechats)){
                                                                                  case 0: $dia = "Domingo"; break;
                                                                                  case 1: $dia = "Lunes"; break;
                                                                                  case 2: $dia = "Martes"; break;
                                                                                  case 3: $dia = "Miercoles"; break;
                                                                                  case 4: $dia = "Jueves"; break;
                                                                                  case 5: $dia = "Viernes"; break;
                                                                                  case 6: $dia = "Sabado"; break;
                                                                                }

                                                                                if ($dia == "Lunes") {
                                                                                  echo "Lunes ".$fecha;
                                                                                }else {
                                                                                  $format = "d/m/Y";
                                                                                  $date = DateTime::createFromFormat($format, $fecha);
                                                                                  $date->modify('last monday');
                                                                                  $fecha32= $date->format("d/m/Y");
                                                                                  echo "Lunes ".$fecha32;
                                                                                }

                                                                                ?></h3>
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
                      <span class="info-box-text">OCUPACIÓN GLOBAL M<sup>2</sup></span>
                      <span class="info-box-number">
                        <?php
                                              $sumatoria_espacio_pasillos = 0;
                                              for ($i=0; $i < count($datos) ; $i++) {
                                                  $sumatoria_espacio_pasillos += $datos[$i]["MTS_UTILIZADOS"];
                                              }

                                              $sumatoria_metros_ocu_piso = 0;
                                              for ($i=0; $i < count($datos) ; $i++) {
                                                  $sumatoria_metros_ocu_piso += $datos[$i]["MTS_PASILLOS_ESPACIO"];
                                              }

                                              $sumatoria_total_posiciones = 0;
                                              for ($i=0; $i < count($datos) ; $i++) {
                                                  $sumatoria_total_posiciones += $datos[$i]["MTS_RACKS"];
                                              }

                                              $sumatoria_uso_variado = 0;
                                              for ($i=0; $i < count($datos) ; $i++) {
                                                  $sumatoria_uso_variado = $datos[$i]["CAPACIDAD_TOTAL"]- $datos[$i]["USO_VARIADO"];
                                              }

                                              $sumatoria_uso_variado_solo = 0;
                                              for ($i=0; $i < count($datos) ; $i++) {
                                                  $sumatoria_uso_variado_solo += $datos[$i]["USO_VARIADO"];
                                              }
                                              $sumatoria_plaza_area_racks = 0;
                                              for ($i=0; $i < count($datos) ; $i++) {
                                                $sumatoria_plaza_area_racks += $datos[$i]["AREA_RACKS"];
                                              }

                                              $sumatoria_plaza_tamanio_bodega = 0;
                                              for ($i=0; $i < count($datos) ; $i++) {
                                                $sumatoria_plaza_tamanio_bodega += $datos[$i]["TAMANIO_BODEGA"];
                                              }
                                              $sum_cap_bodega = 0;
                                              for ($i=0; $i < count($datos) ; $i++) {
                                                $sum_cap_bodega += $datos[$i]["CAPACIDAD_TOTAL"];
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
+
                                              $oc_total = $factor_racks_piso+$sumatoria_metros_ocu_piso+$sumatoria_uso_variado_solo;
                                              //echo $capacidad_almacen[0]["USO_VARIADOS"]. "<br >";
                                              echo number_format(ROUND($oc_total, 2));
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
                    <span class="info-box-text">CAPACIDAD GLOBAL DE <br> ALMACENAMIENTO M<sup>2</sup></span>

                    <span class="info-box-number">
                      <?php

                        /*$porcentaje_total = 0;
                        if ($plaza == "ALL" && $almacen == "ALL") {
                          for ($i=0; $i < COUNT($grafica); $i++) {
                            $porcentaje_total += $grafica[$i]["RACK_PORCENTAJE"];
                          }
                          if (COUNT($grafica)== 0 ) {
                            echo 0;
                          }
                          else {
                            echo number_format($porcentaje_total/COUNT($grafica), 2);
                          }

                        }elseif ($plaza <> "ALL" && $almacen == "ALL") {
                            for ($i=0; $i < count($graficaAlmacen) ; $i++) {
                              $porcentaje_total += $graficaAlmacen[$i]["RACK_PORCENTAJE"]/count($graficaAlmacen);
                            }
                            echo number_format($porcentaje_total, 2);
                        }elseif ($plaza <> "ALL" && $almacen <> "ALL") {
                          for ($i=0; $i < COUNT($graficaMensual) ; $i++) {
                            $porcentaje_total = $graficaMensual[$i]["RACK_PORCENTAJE"];
                          }
                          echo number_format($porcentaje_total, 2);
                        }*/
                        echo number_format(ROUND($sumatoria_plaza_tamanio_bodega, 2));
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
              <div id="graf_perW"></div>
              <div id="graf_bar"></div>
              <section>
                <div class="box box-success">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-table"></i> OCUPACION POR CLIENTE PLAZA <?php echo $plaza; ?></h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>

                  <!--cliente vacio-->
                  <?php if ($cliente == 'ALL') { ?>
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
                          for ($i=0; $i <count($graficaCliente) ; $i++) {  ?>

                            <tr>
                            <td class="small">
                            <?php
                                echo $graficaCliente[$i]["CLIENTE"];
                              ?>
                            </td>
                            <td class="small">
                              <?php
                                  echo number_format($graficaCliente[$i]["RACK_PORCENTAJE"], 2);
                              ?>
                            </td>

                             </tr>
                                <?php
                              }
                            ?>
                        </tbody>
                      </table>
                    </div>

                  </div><!--/.box-body-->
                  <?php  } ?>
                  <!--cliente vacio-->
                </div>
              </section>
            <?php } ?>
            <?php if ($plaza == 'ALL') {?>
              <div id="graf_perM"></div>
            <?php }else {?>
              <?php if ($almacen == 'ALL') { ?>
                <div id="graf_perAlmacen"></div>
                <div id="graf_perW"></div>
              <?php } ?>
              <?php }?>
            <?php if ($plaza == 'ALL' && $almacen == 'ALL') {?>

            <?php }?>
          </div>
        </div><!--/.box-body-->
      </div>
    </div>

    <?php //if ($plaza != 'ALL'){ ?>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
          <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="calculo_Ocupacion.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
          <?php } ?>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <!-- FILTRAR POR fecha -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
            <input type="text" class="form-control pull-right" name="fil_fecha" disabled value="<?= $fecha ?>">
            <span class="input-group-addon"> <input type="checkbox" name="fil_check" <?php if( $fil_check == 'on' ){ echo "checked";} ?> > </span>
          </div>

          <!-- FILTRAR POR PLAZA -->
          <input id="fil_plaza" type="hidden" value=<?= $plaza ?>>

          <?php if($_SESSION['area']!=3){  ?>
          <!--<div class="input-group">
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
        <?php } else{?>
          <input id="fil_plaza" type="hidden" value=<?= $plaza ?>>-->
        <?php }?>

          <!-- FILTRAR POR ALMACEN -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Almacen:</span>
            <select class="form-control select2" style="width: 100%;" id="nomAlm">
              <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $plazas=$plaza;
              //$plazas = $_GET["plaza"];
              $selectAlmacen = $obj_class->almacenSql($plazas);
              for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
                <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
              <?php } ?>
            </select>
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Cliente:</span>
            <select class="form-control select2" style="width: 100%;" id="nomCli">
              <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $almacen = $_GET["almacen"];
              $selectCliente = $obj_class->clienteSql($almacen);
              for ($i=0; $i <count($selectCliente) ; $i++) { ?>
                <option value="<?=$selectCliente[$i]["ID_CLIENTE"]?>" <?php if($selectCliente[$i]["ID_CLIENTE"] == $cliente){echo "selected";} ?>><?=$selectCliente[$i]["NOMBRE"]?> </option>
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


    <div class="col-md-9" style="display:none">
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
<?php if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) ){ ?>
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
  cliente = $('#nomCli').val();
  fil_contrato = $('#fil_contrato').val();
  fil_departamento = $('#fil_departamento').val();
  fil_area = $('#fil_area').val();
  fil_check = 'off';

  //Fill habilitados
  fil_habilitado = 'off';

  url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&cliente='+cliente;
  if ($('input[name="fil_check"]').is(':checked')) {
    fil_check = 'on';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&cliente='+cliente;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&cliente='+cliente;
    }

  }else{
    fil_check = 'off';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
        fil_habilitado = 'on';
        url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado+'&cliente='+cliente;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado+'&cliente='+cliente;
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

         echo "'".$graficaMensual[$i]["MES"]."',";

     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaMensual) ; $i++) {
                 echo number_format($graficaMensual[$i]["RACK_PORCENTAJE"], 2).",";
    }
    ?>
    ];
    $('#graf_bar').highcharts({
        chart: {
            type: 'line'
        },
         title: {
           /*text: 'OCUPACIÓN SEMANAL DE PLAZA <?php //echo $plaza; ?> <?php //if($almacen != 'ALL' and $cliente == 'ALL') {$nombreAlmacen = $obj_class->almacenNombre($plaza,$almacen); echo "DEL ALMACEN ".$nombreAlmacen[0]["V_NOMBRE"]; } else {$nombre_cliente = $obj_class->clienteNombre($cliente); echo " DEL CLIENTE ".$nombre_cliente[0]["V_RAZON_SOCIAL"]; } ?>'*/
            text: 'OCUPACIÓN MENSUAL <?php if ($plaza != 'ALL' && $almacen == 'ALL') {echo "PLAZA ".$plaza;} elseif ($almacen != 'ALL' and $plaza != 'ALL' and $cliente == 'ALL') {
                    $nombreAlmacen = $obj_class->almacenNombre($plaza,$almacen); echo "ALMACEN ".$nombreAlmacen[0]["V_NOMBRE"];
                  }elseif($cliente != 'ALL') {
                    $nombre_cliente = $obj_class->clienteNombre($cliente); echo " DEL CLIENTE ".$nombre_cliente[0]["V_RAZON_SOCIAL"];
                  } ?>'
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
         echo "'".$grafica[$i]["PLAZA"]."',";

     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($grafica) ; $i++) {
      echo number_format($grafica[$i]["RACK_PORCENTAJE"], 2).",";
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
          echo "'".$graficaAlmacen[$i]["ALMACEN"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaAlmacen) ; $i++) {
            echo ROUND(($graficaAlmacen[$i]["RACK_PORCENTAJE"]), 2).",";
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

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($graficaSemana) ; $i++) {
          echo "'".$graficaSemana[$i]["SEMANA"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaSemana) ; $i++) {
            echo ROUND(($graficaSemana[$i]["RACK_PORCENTAJE"]), 2).",";
    }
    ?>
    ];
    $('#graf_perW').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'OCUPACIÓN SEMANAL DE PLAZA <?php echo $plaza; ?> <?php if($almacen != 'ALL' and $cliente == 'ALL') {$nombreAlmacen = $obj_class->almacenNombre($plaza,$almacen); echo "DEL ALMACEN ".$nombreAlmacen[0]["V_NOMBRE"]; } elseif($cliente != 'ALL') {$nombre_cliente = $obj_class->clienteNombre($cliente); echo " DEL CLIENTE ".$nombre_cliente[0]["V_RAZON_SOCIAL"]; } ?>'
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
          valueSuffix: ' % DE OCUPACIÓN',
          useHTML: true,
          //valueDecimals: 2,
          //valuePrefix: '$',
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
            //showInLegend:false,
            name: ' PORCENTAJE GLOBAL SEMANAL ',
            data: data1,
        }]

    });
});
</script>

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
                    return Intl.NumberFormat().format(intVal(a) + intVal(b));
                    //return intVal(a) + intVal(b);
                    //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                }, 0 );

            // Total over this page
            pageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return Intl.NumberFormat().format(intVal(a) + intVal(b));
                    //return Math.round(intVal(a) + intVal(b));
                }, 0 );

            // Update footer
            $( api.column( 5 ).footer() ).html(
                //''+pageTotal +' ('+ total +' total)'
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
    locale: {
         format: 'DD/MM/YYYY',
         applyLabel: 'Aplicar',
         cancelLabel: 'Limpiar'
       },
      singleDatePicker: true,
      showDropdowns: true,
      minYear: 1901,
      maxYear: parseInt(moment().format('YYYY'),10)
  }

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
