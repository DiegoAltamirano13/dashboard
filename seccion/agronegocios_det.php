<?php
ini_set('display_errors', false);

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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '6');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
/* HORA MEXICO */
$time = time();
date_default_timezone_set("America/Mexico_City");

/* --------------------- INICIA CREACION DE OBJETOS --------------------- */

/* INSTANCIA PARA OPERACIONES AGRONEGOCIOS OFC  */
include_once '../class/Agronegocio_carga.php';
$obj_agro_carga = new Consulta_carga();
$obj_det_carga = new Consulta_status_carga();

/* INSTANCIA PARA OPERACIONES AGRONEGOCIOS OTFC  */
include_once '../class/Agronegocio_descarga.php';
$obj_det_descarga = new Consulta_status_descarga();

/* --------------------- TERMINA CREACION DE OBJETOS --------------------- */
/*SESSION PARA PLAZA AGRONEGOCIO*/
if( isset($_POST["agro_plaza"]))
  $_SESSION["agro_plaza"] = $_POST["agro_plaza"];
  $agro_plaza = $_SESSION["agro_plaza"];
/*SESSION PARA EL HISTORIAL AGRONEGOCIO*/
if ($_SESSION["agro_historial"]==false){
  $_SESSION["agro_historial"] = date("d-m-Y");
  $agro_historial = $_SESSION["agro_historial"];
}else{
if( isset($_POST["agro_historial"]))
  $_SESSION["agro_historial"] = $_POST["agro_historial"];
  $agro_historial = $_SESSION["agro_historial"];
}
////////////////////GUARDA AL VALOR DEL DIA EN UNA SESSION
    if(isset($_POST['dia']))
    // fecha completa
    $_SESSION['dia'] = $_POST['dia'];
    $valor_dia = $_SESSION['dia'];
    // año
    $_SESSION['anio'] = substr($valor_dia,-4);
    $valor_anio = $_SESSION['anio'];

    $tipo_op = $_GET["op"] ;
    $par = $_GET["par"] ;
    $ofc = $_GET["ofc"] ;
    $fol = $_GET["fol"] ;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Argo Almacenadora, S.A. de C.V.</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <link rel="shortcut icon" href="../assets/ico/favicon.png">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<?php
if ( $tipo_op == 'ofc')
{
?>
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h3 class="page-header">
          <i class="fa fa-truck"><i class="fa fa-long-arrow-up"></i></i> Detalles de Carga
          <!-- <small class="pull-right">Fecha: <?= $valor_dia ?></small> -->
        </h3>
      </div>
      <!-- /.col -->
    </div>

    <?php
      $detalle_ofc = $obj_det_carga->consulta_carga_status($agro_plaza,$par,$ofc,$fol);
        for ($i=0; $i <count($detalle_ofc ) ; $i++) {
    ?>
    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table no-margin table-hover">
                            <!-- Inicia tabla para detalles de OFC -->
                              <thead>
                              <tr>
                                <th colspan="8"><div align="center" class="text-light-blue"><i class="fa fa-folder-open"></i> Detalles de Carga</div></th>
                              </tr>
                              </thead>
                              <tbody>
                               <tr>
                                <th class="small">FOLIO/CONVENIO</th>
                                <td><code><?= $detalle_ofc[$i]["FOLIO_OFC"] ?></code></td>
                                <th class="small">DESCRIPCIÓN</th>
                                <td><code><?= $detalle_ofc[$i]["DES_PARTE"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">CLIENTE</th>
                                <td><code><?= $detalle_ofc[$i]["CLIENTE"] ?></code></td>
                                <th class="small">PLAZA</th>
                                <td><code><?= $detalle_ofc[$i]["PLAZA"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">ALMACEN</th>
                                <td><code><?= $detalle_ofc[$i]["ALMACEN"] ?></code></td>
                                <th class="small">OBSERVACIONES</th>
                                <td><code><?= $detalle_ofc[$i]["OBSERVACIONES"] ?></code></td>
                               </tr>
                              </tbody>
                            <!-- /.Termina tabla para detalles de OFC -->
                            <!-- Inicia tabla para detalles de Vehículo -->
                              <thead>
                              <tr>
                                <th colspan="8"><div class="text-light-blue" align="center"><i class="fa fa-truck"></i> Detalles de Vehículo</div></th>
                              </tr>
                              </thead>
                              <tbody>
                               <tr>
                                <th class="small">FECHA DE REGISTRO</th>
                                <td><code><?= $detalle_ofc[$i]["R_VEHICULO"] ?></code></td>
                                <th class="small">REMISIÓN</th>
                                <td><code><?= $detalle_ofc[$i]["REMISION"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">TRANSPORTE</th>
                                <td><code><?= $detalle_ofc[$i]["TRANSPORTE"] ?></code></td>
                                <th class="small">CHOFER</th>
                                <td><code><?= $detalle_ofc[$i]["CHOFER"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">PLACAS</th>
                                <td><code><?= $detalle_ofc[$i]["PLACAS"] ?></code></td>
                                <th class="small">VEHÍCULO</th>
                                <td><code><?= $detalle_ofc[$i]["VEHICULO"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">PESO</th>
                                <td><code><?= $detalle_ofc[$i]["TON_NETAS"]." TONELADAS." ?></code></td>
                                <th class="small"><?= $detalle_ofc[$i]["UME"] ?></th>
                                <td><code><?= $detalle_ofc[$i]["BULTOS"]." DE ".$detalle_ofc[$i]["FACTOR"]." KG." ?></code></td>
                               </tr>
                              </tbody>
                            <!-- /.Termina tabla para detalles de Vehículo -->
                            <!-- Inicia tabla para detalles de Vehículo -->
                              <thead>
                              <tr>
                                <th colspan="8"><div class="text-light-blue" align="center"><i class="fa fa-clock-o"></i> Detalles de Tiempo</div></th>
                              </tr>
                              </thead>
                            <!-- /.Termina tabla para detalles de Vehículo -->
                            </table>

                            <!-- *-*-*-*-*-* INICIA LINIA DE TIEMPO STATUS DE VEHICULO EN CARGA *-*-*-*-*-* -->
                            <div class="col-md-4">
                              <!-- The time line -->
                              <ul class="timeline small">
                                <?php
                                //Inicia timeline Registro de vehículo
                                switch (true) {
                                  case ($detalle_ofc[$i]["R_VEHICULO"] == false):
                                    echo '<li class="time-label">' ;
                                      echo '<span class="bg-blue">' ;
                                      echo '<i class="fa fa-clock-o"></i> Registro de vehículo: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    $dif_min = $obj_agro_carga->dif_minutos($detalle_ofc[$i]["BASCULA_VEHICULO"],$detalle_ofc[$i]["R_VEHICULO"]);
                                    if ($dif_min>5){
                                      echo '<span class="bg-red">' ;
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Registro de vehículo: <code>'.$detalle_ofc[$i]["R_VEHICULO"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Registro de vehículo
                                //Inicia Dif. Tiempo Registro de vehículo a Vehículo en bascula
                                switch (true){
                                  case ($detalle_ofc[$i]["R_VEHICULO"] == false) || ($detalle_ofc[$i]["BASCULA_VEHICULO"] == false):
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-o bg-blue"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        echo '<a class="bg-default"><b> Diferencia: En proceso.</b> </a>';
                                      echo '</div>';
                                    echo '</li>';
                                    break;

                                  default:

                                  $fechaInicio = $detalle_ofc[$i]["R_VEHICULO"];
                                  $fechaFin = $detalle_ofc[$i]["BASCULA_VEHICULO"];
                                #  echo $fechaInicio."   ".$fechaFin;
                                  $dif_reg_bas = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                  /** INICIA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if( strtotime($fechaInicio) > strtotime($fechaFin) ){$fecha_t_v=false;}else{$fecha_t_v=true;}
                                  /** TERMINA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-1 bg-green"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        /** INICIA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                        switch ($fecha_t_v) {
                                          case true:
                                            echo '<a class="bg-default"><b> Diferencia: '.$dif_reg_bas.'</b> </a>';
                                            break;
                                          default:
                                            echo '<a class="bg-default"><code><i class="fa fa-warning"></i> Diferencia: -'.$dif_reg_bas.'</code><span class="text-muted">(fecha inicial es mayor a la final)</span></a>';
                                            break;
                                        }
                                        /** TERMINA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                    break;
                                }
                                //Termina Dif. Tiempo Registro de vehículo a Vehículo en bascula
                                //Inicia timeline Vehículo en bascula
                                switch (true) {
                                  case ($detalle_ofc[$i]["BASCULA_VEHICULO"] == false):
                                    echo '<li class="time-label">' ;
                                      $dif_min = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$detalle_ofc[$i]["R_VEHICULO"]);
                                      if ($dif_min>5){
                                      echo '<span class="bg-red">' ;
                                      }else{
                                      echo '<span class="bg-blue">' ;
                                      }
                                      echo '<i class="fa fa-clock-o"></i> Vehículo en bascula: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    $dif_min = $obj_agro_carga->dif_minutos($detalle_ofc[$i]["INICIA_CARGA"],$detalle_ofc[$i]["BASCULA_VEHICULO"]);
                                    if ($dif_min>5){
                                      echo '<span class="bg-red">' ;
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Vehículo en bascula: <code>'.$detalle_ofc[$i]["BASCULA_VEHICULO"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Vehículo en bascula
                                //Inicia Dif. Tiempo Vehículo en bascula a Inicia Carga
                                switch (true){
                                  case ($detalle_ofc[$i]["BASCULA_VEHICULO"] == false) || ($detalle_ofc[$i]["INICIA_CARGA"] == false):
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-o bg-blue"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        echo '<a class="bg-default"><b> Diferencia: En proceso.</b> </a>';
                                      echo '</div>';
                                    echo '</li>';
                                    break;

                                  default:
                                  // $dif_bas_inides = (strtotime($detalle_ofc[$i]["BASCULA_VEHICULO"])-strtotime($detalle_ofc[$i]["INICIA_CARGA"]) )/60;
                                  // $dif_bas_inides = abs($dif_bas_inides); $dif_bas_inides = round($dif_bas_inides,1);
                                  $fechaInicio = $detalle_ofc[$i]["BASCULA_VEHICULO"];
                                  $fechaFin = $detalle_ofc[$i]["INICIA_CARGA"];
                                  $dif_bas_inides = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                  /** INICIA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if( strtotime($fechaInicio) > strtotime($fechaFin) ){$fecha_t_v=false;}else{$fecha_t_v=true;}
                                  /** TERMINA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-1 bg-green"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        /** INICIA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                        switch ($fecha_t_v) {
                                          case true:
                                            echo '<a class="bg-default"><b> Diferencia: '.$dif_bas_inides.'</b> </a>';
                                            break;
                                          default:
                                            echo '<a class="bg-default"><code><i class="fa fa-warning"></i> Diferencia: -'.$dif_bas_inides.'</code><span class="text-muted">(fecha inicial es mayor a la final)</span></a>';
                                            break;
                                        }
                                        /** TERMINA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                    break;
                                }
                                //Termina Dif. Tiempo Vehículo en bascula a Inicia Carga
                                //Inicia timeline Inicia Carga
                                switch (true) {
                                  case ($detalle_ofc[$i]["INICIA_CARGA"] == false):
                                    echo '<li class="time-label">' ;
                                    $dif_min = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$detalle_ofc[$i]["BASCULA_VEHICULO"]);
                                      if ($dif_min>5 && $detalle_ofc[$i]["BASCULA_VEHICULO"] == true){
                                      echo '<span class="bg-red">' ;
                                      }else{
                                      echo '<span class="bg-blue">' ;
                                      }
                                      echo '<i class="fa fa-clock-o"></i> Inicia Carga: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    $dif_min = $obj_agro_carga->dif_minutos($detalle_ofc[$i]["TERMINA_CARGA"],$detalle_ofc[$i]["INICIA_CARGA"]);
                                    if ($dif_min>60){
                                      echo '<span class="bg-red">' ;
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Inicia Carga: <code>'.$detalle_ofc[$i]["INICIA_CARGA"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Inicia Carga
                                //Inicia Dif. Tiempo Inicia Carga a Termina Carga
                                switch (true){
                                  case ($detalle_ofc[$i]["INICIA_CARGA"] == false) || ($detalle_ofc[$i]["TERMINA_CARGA"] == false):
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-o bg-blue"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        echo '<a class="bg-default"><b> Diferencia: En proceso.</b> </a>';
                                      echo '</div>';
                                    echo '</li>';
                                    break;

                                  default:
                                  //$dif_inicar_tercar = (strtotime($detalle_ofc[$i]["INICIA_CARGA"])-strtotime($detalle_ofc[$i]["TERMINA_CARGA"]) )/60;
                                  //$dif_inicar_tercar = abs($dif_inicar_tercar); $dif_inicar_tercar = round($dif_inicar_tercar,1);
                                  $fechaInicio = $detalle_ofc[$i]["INICIA_CARGA"];
                                  $fechaFin = $detalle_ofc[$i]["TERMINA_CARGA"];
                                  $dif_inicar_tercar = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                  /** INICIA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if( strtotime($fechaInicio) > strtotime($fechaFin) ){$fecha_t_v=false;}else{$fecha_t_v=true;}
                                  /** TERMINA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-1 bg-green"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        /** INICIA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                        switch ($fecha_t_v) {
                                          case true:
                                            echo '<a class="bg-default"><b> Diferencia: '.$dif_inicar_tercar.'</b> </a>';
                                            break;
                                          default:
                                            echo '<a class="bg-default"><code><i class="fa fa-warning"></i> Diferencia: -'.$dif_inicar_tercar.'</code><span class="text-muted">(fecha inicial es mayor a la final)</span></a>';
                                            break;
                                        }
                                        /** TERMINA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                    break;
                                }
                                //Termina Dif. Tiempo Inicia Carga a Termina Carga
                                //Inicia timeline Termina Carga
                                switch (true) {
                                  case ($detalle_ofc[$i]["TERMINA_CARGA"] == false):
                                    echo '<li class="time-label">' ;
                                    $dif_min = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$detalle_ofc[$i]["INICIA_CARGA"]);
                                      if ($dif_min>60 && $detalle_ofc[$i]["INICIA_CARGA"] == true){
                                      echo '<span class="bg-red">' ;
                                      }else{
                                      echo '<span class="bg-blue">' ;
                                      }
                                      echo '<i class="fa fa-clock-o"></i> Termina Carga: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    $dif_min = $obj_agro_carga->dif_minutos($detalle_ofc[$i]["FECHA_DOCUMENTACION"],$detalle_ofc[$i]["TERMINA_CARGA"]);
                                    if ($dif_min>5){
                                      echo '<span class="bg-red">' ;
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Termina Carga: <code>'.$detalle_ofc[$i]["TERMINA_CARGA"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Termina Carga
                                //Inicia Dif. Tiempo Termina Carga a Documentación
                                switch (true) {
                                  case ($detalle_ofc[$i]["TERMINA_CARGA"] == false) || ($detalle_ofc[$i]["FECHA_DOCUMENTACION"] == false):
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-o bg-blue"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        echo '<a class="bg-default"><b> Diferencia: En proceso.</b> </a>';
                                      echo '</div>';
                                    echo '</li>';
                                    break;

                                  default:
                                  // $dif_tercar_doc = (strtotime($detalle_ofc[$i]["TERMINA_CARGA"])-strtotime($detalle_ofc[$i]["FECHA_DOCUMENTACION"]) )/60;
                                  // $dif_tercar_doc = abs($dif_tercar_doc); $dif_tercar_doc = round($dif_tercar_doc,1);
                                  $fechaInicio = $detalle_ofc[$i]["TERMINA_CARGA"];
                                  $fechaFin = $detalle_ofc[$i]["FECHA_DOCUMENTACION"];
                                  $dif_tercar_doc = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                  /** INICIA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if( strtotime($fechaInicio) > strtotime($fechaFin) ){$fecha_t_v=false;}else{$fecha_t_v=true;}
                                  /** TERMINA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-1 bg-green"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        /** INICIA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                        switch ($fecha_t_v) {
                                          case true:
                                            echo '<a class="bg-default"><b> Diferencia: '.$dif_tercar_doc.'</b> </a>';
                                            break;
                                          default:
                                            echo '<a class="bg-default"><code><i class="fa fa-warning"></i> Diferencia: -'.$dif_tercar_doc.'</code><span class="text-muted">(fecha inicial es mayor a la final)</span></a>';
                                            break;
                                        }
                                        /** TERMINA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                    break;
                                }
                                //Termina Dif. Tiempo Termina Carga a Documentación
                                //Inicia timeline Documentación
                                switch (true) {
                                  case ($detalle_ofc[$i]["FECHA_DOCUMENTACION"] == false):
                                  $var_t_tiempo = "Tiempo Transcurrido: ";
                                    echo '<li class="time-label">' ;
                                    $dif_min = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$detalle_ofc[$i]["TERMINA_CARGA"]);
                                      if ($dif_min>5 && $detalle_ofc[$i]["TERMINA_CARGA"] == true){
                                      echo '<span class="bg-red">' ;
                                      }else{
                                      echo '<span class="bg-blue">' ;
                                      }
                                      echo '<i class="fa fa-clock-o"></i> Documentación: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                  $var_t_tiempo = "Total de Tiempo (Inicio-Documentación): ";
                                    echo '<li class="time-label">' ;
                                      echo '<span class="bg-green">' ;
                                      echo '<i class="fa fa-clock-o"></i> Documentación: <code>'.$detalle_ofc[$i]["FECHA_DOCUMENTACION"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Documentación
                                //Inicia Total de tiempo Transcurrido en carga
                                //$tiempo_descarga = $dif_reg_bas + $dif_bas_inides + $dif_inicar_tercar + $dif_tercar_doc   ;
                                $fechaInicio = $detalle_ofc[$i]["R_VEHICULO"];
                                $fechaFin = $detalle_ofc[$i]["FECHA_DOCUMENTACION"];
                                $tiempo_descarga = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                /** INICIA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if( strtotime($fechaInicio) > strtotime($fechaFin) ){$fecha_t_v=false;}else{$fecha_t_v=true;}
                                /** TERMINA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass bg-gray"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        /** INICIA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                        switch ($fecha_t_v) {
                                          case true:
                                            echo '<a class="bg-default"><b>'.$var_t_tiempo.'<br>'.$tiempo_descarga.'</b> </a>';
                                            break;
                                          default:
                                            if ( $var_t_tiempo == "Tiempo Transcurrido: " )
                                            {
                                              //// INICIA CODE SI LA FECHA DE REG VEHICULO ES MAYOR A LA FECHA DE HOY CARGAS
                                              $fecha_hoy = strftime("%d-%m-%Y %H:%M:%S");
                                              switch (true) {
                                                case (strtotime($fecha_hoy) < strtotime($detalle_otfc[$i]["R_VEHICULO"]) ):
                                                  echo '<a class="bg-default"><code><i class="fa fa-warning"></i>'.$var_t_tiempo.' -'.$tiempo_descarga.'</code><span class="text-muted">(Fechas inconsistentes)</span></a>';
                                                  break;

                                                default:
                                                  echo '<a class="bg-default"><b>'.$var_t_tiempo.$tiempo_descarga.'</b></a>';
                                                  break;
                                              }
                                              //// TERMINA CODE SI LA FECHA DE REG VEHICULO ES MAYOR A LA FECHA DE HOY CARGAS
                                            }else{
                                              echo '<a class="bg-default"><code><i class="fa fa-warning"></i>'.$var_t_tiempo.' -'.$tiempo_descarga.'</code><span class="text-muted">(Fechas inconsistentes)</span></a>';
                                            }
                                            break;
                                        }
                                        /** TERMINA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                //Termina Total de tiempo Transcurrido en carga

                                    if ($detalle_ofc[$i]["FECHA_DOCUMENTACION"] == true){
                                      echo  '<li class="time-label">';
                                        echo  '<span class="bg-green">';
                                        echo  '<i class="fa fa-clock-o"></i> Despacho de Vehículo <code>Finalizado</code>';
                                        echo  '</span> ';
                                      echo  '</li>';
                                    }else{
                                      echo  '<li class="time-label">';
                                        echo  '<span class="bg-blue">';
                                        echo  '<i class="fa fa-clock-o"></i> Despacho de Vehículo <code>En proceso...</code>';
                                        echo  '</span> ';
                                      echo  '</li>';
                                    }
                                ?>

                              </ul>
                            </div>
                          <!-- *-*-*-*-*-* TERMINA LINIA DE TIEMPO STATUS DE VEHICULO EN CARGA *-*-*-*-*-* -->


      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <?php
      }
    ?>
    <br><br>
    <div class="row no-print">
        <div class="col-xs-12">
          <a href="javascript:window.print()"  class="btn btn-default"><i class="fa fa-print"></i> Imprimir </a>
        </div>
      </div>
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
<?php
}
?>

<?php
if ( $tipo_op == 'otfc')
{
?>
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h3 class="page-header">
          <i class="fa fa-truck"><i class="fa fa-long-arrow-down"></i></i> Detalle de Descarga
          <!-- <small class="pull-right">Fecha: <?= $valor_dia ?></small> -->
        </h3>
      </div>
      <!-- /.col -->
    </div>


         <?php
         #echo $agro_plaza;
         $agro_plaza = $_SESSION["nomPlaza"];
         $detalle_otfc = $obj_det_descarga->consulta_descarga_status($agro_plaza,$par,$ofc,$fol);
          for ($i=0; $i <count($detalle_otfc) ; $i++) {//Inicia for para traer los registros otfc finalizado
          $plaza_mov_des = $detalle_otfc[$i]["ID_PLAZA"];
          $vid_recibo_des = $detalle_otfc[$i]["RECIBO_DET"];
          $vid_movto_des = $detalle_otfc[$i]["MOV_ID_VEHICULO"].$detalle_otfc[$i]["MOV_BULTO"];

          //CONSULTA FECHA PRE-REGISTRO EN OP_IN_MOVIMIENTOS
          $consulta_descarga_mov = $obj_det_descarga->consulta_descarga_mov($plaza_mov_des,$vid_recibo_des,$vid_movto_des);
          ?>


  <!-- INICIA CODE HTML PARA STATUS DE CARROS OTFC  -->
 <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table no-margin table-hover">
                            <!-- Inicia tabla para detalles de OTFC -->
                              <thead>
                              <tr>
                                <th colspan="8"><div align="center" class="text-light-blue"><i class="fa fa-folder-open"></i> Detalles de Descarga</div></th>
                              </tr>
                              </thead>
                              <tbody>
                               <tr>
                                <th class="small">FOLIO/CONVENIO</th>
                                <td><code><?= $detalle_otfc[$i]["FOLIO_OTFC"] ?></code></td>
                                <th class="small">DESCRIPCIÓN</th>
                                <td><code><?= $detalle_otfc[$i]["DES_PARTE"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">CLIENTE</th>
                                <td><code><?= $detalle_otfc[$i]["CLIENTE"] ?></code></td>
                                <th class="small">PLAZA</th>
                                <td><code><?= $detalle_otfc[$i]["PLAZA"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">ALMACEN</th>
                                <td><code><?= $detalle_otfc[$i]["ALMACEN"] ?></code></td>
                                <th class="small">OBSERVACIONES</th>
                                <td><code><?= $detalle_otfc[$i]["OBSERVACIONES"] ?></code></td>
                               </tr>
                              </tbody>
                            <!-- /.Termina tabla para detalles de OTFC -->
                            <!-- Inicia tabla para detalles de Vehículo -->
                              <thead>
                              <tr>
                                <th colspan="8"><div class="text-light-blue" align="center"><i class="fa fa-truck"></i> Detalles de Vehículo</div></th>
                              </tr>
                              </thead>
                              <tbody>
                               <tr>
                                <th class="small">FECHA DE REGISTRO</th>
                                <td><code><?= $detalle_otfc[$i]["R_VEHICULO"] ?></code></td>
                                <th class="small">REMISIÓN</th>
                                <td><code><?= $detalle_otfc[$i]["REMISION"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">TRANSPORTE</th>
                                <td><code><?= $detalle_otfc[$i]["TRANSPORTE"] ?></code></td>
                                <th class="small">CHOFER</th>
                                <td><code><?= $detalle_otfc[$i]["CHOFER"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">PLACAS</th>
                                <td><code><?= $detalle_otfc[$i]["PLACAS"] ?></code></td>
                                <th class="small">VEHÍCULO</th>
                                <td><code><?= $detalle_otfc[$i]["VEHICULO"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">PESO</th>
                                <td><code><?= $detalle_otfc[$i]["TON_NETAS"]." TONELADAS." ?></code></td>
                                <th class="small"><?= $detalle_otfc[$i]["UME"] ?></th>
                                <td><code><?= $detalle_otfc[$i]["BULTOS"]." DE ".$detalle_otfc[$i]["FACTOR"]." KG." ?></code></td>
                               </tr>
                              </tbody>
                            <!-- /.Termina tabla para detalles de Vehículo -->
                            <!-- Inicia tabla para detalles de Vehículo -->
                              <thead>
                              <tr>
                                <th colspan="8"><div class="text-light-blue" align="center"><i class="fa fa-clock-o"></i> Detalles de Tiempo</div></th>
                              </tr>
                              </thead>
                            <!-- /.Termina tabla para detalles de Vehículo -->
                            </table>

                          <!-- *-*-*-*-*-* INICIA LINIA DE TIEMPO STATUS DE VEHICULO EN DESCARGA *-*-*-*-*-* -->
                            <div class="col-md-4">
                              <!-- The time line -->
                              <ul class="timeline small">
                                <?php
                                //Inicia timeline Registro de vehículo
                                switch (true) {
                                  case ($detalle_otfc[$i]["R_VEHICULO"] == false):
                                    echo '<li class="time-label">' ;
                                      echo '<span class="bg-blue">' ;
                                      echo '<i class="fa fa-clock-o"></i> Registro de vehículo: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    if ($detalle_otfc[$i]["BASCULA_VEHICULO"] == true){
                                      $dif_min_des = $obj_agro_carga->dif_minutos($detalle_otfc[$i]["BASCULA_VEHICULO"],$detalle_otfc[$i]["R_VEHICULO"]);
                                      if ($dif_min_des>5){
                                        echo '<span class="bg-red">' ;
                                      }else{
                                        echo '<span class="bg-green">' ;
                                      }
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Registro de vehículo: <code>'.$detalle_otfc[$i]["R_VEHICULO"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Registro de vehículo
                                //Inicia Dif. Tiempo Registro de vehículo a Vehículo en bascula
                                switch (true){
                                  case ($detalle_otfc[$i]["R_VEHICULO"] == false) || ($detalle_otfc[$i]["BASCULA_VEHICULO"] == false):
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-o bg-blue"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        echo '<a class="bg-default"><b> Diferencia: En proceso.</b> </a>';
                                      echo '</div>';
                                    echo '</li>';
                                    break;

                                  default:
                                  // $dif_reg_bas = (strtotime($detalle_otfc[$i]["R_VEHICULO"])-strtotime($detalle_otfc[$i]["BASCULA_VEHICULO"]) )/60;
                                  // $dif_reg_bas = abs($dif_reg_bas); $dif_reg_bas = round($dif_reg_bas,1);
                                  $fechaInicio = $detalle_otfc[$i]["R_VEHICULO"];
                                  $fechaFin = $detalle_otfc[$i]["BASCULA_VEHICULO"];
                                  $dif_reg_bas = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                  /** INICIA CODE OTFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if( strtotime($fechaInicio) > strtotime($fechaFin) ){$fecha_t_v=false;}else{$fecha_t_v=true;}
                                  /** TERMINA CODE OTFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-1 bg-green"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        /** INICIA CODE OTFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                        switch ($fecha_t_v) {
                                          case true:
                                            echo '<a class="bg-default"><b> Diferencia: '.$dif_reg_bas.'</b> </a>';
                                            break;
                                          default:
                                            echo '<a class="bg-default"><code><i class="fa fa-warning"></i> Diferencia: -'.$dif_reg_bas.'</code><span class="text-muted">(fecha inicial es mayor a la final)</span></a>';
                                            break;
                                        }
                                        /** TERMINA CODE OTFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                    break;
                                }
                                //Termina Dif. Tiempo Registro de vehículo a Vehículo en bascula
                                //Inicia timeline Vehículo en bascula
                                switch (true) {
                                  case ($detalle_otfc[$i]["BASCULA_VEHICULO"] == false):
                                    echo '<li class="time-label">' ;
                                    if ($detalle_otfc[$i]["R_VEHICULO"] == true){
                                      $dif_min_des = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$detalle_otfc[$i]["R_VEHICULO"]);
                                      if ($dif_min_des>5){
                                        echo '<span class="bg-red">';
                                      }else{
                                        echo '<span class="bg-blue">';
                                      }
                                    }else{
                                      echo '<span class="bg-blue">';
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Vehículo en bascula: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    if ($detalle_otfc[$i]["INICIA_DESCARGA"] == true){
                                      $dif_min_des = $obj_agro_carga->dif_minutos($detalle_otfc[$i]["INICIA_DESCARGA"],$detalle_otfc[$i]["BASCULA_VEHICULO"]);
                                      if ($dif_min_des>5){
                                        echo '<span class="bg-red">' ;
                                      }else{
                                        echo '<span class="bg-green">' ;
                                      }
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Vehículo en bascula: <code>'.$detalle_otfc[$i]["BASCULA_VEHICULO"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Vehículo en bascula
                                //Inicia Dif. Tiempo Vehículo en bascula a Inicia Descarga
                                switch (true){
                                  case ($detalle_otfc[$i]["BASCULA_VEHICULO"] == false) || ($detalle_otfc[$i]["INICIA_DESCARGA"] == false):
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-o bg-blue"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        echo '<a class="bg-default"><b> Diferencia: En proceso.</b> </a>';
                                      echo '</div>';
                                    echo '</li>';
                                    break;

                                  default:
                                  // $dif_bas_inides = (strtotime($detalle_otfc[$i]["BASCULA_VEHICULO"])-strtotime($detalle_otfc[$i]["INICIA_DESCARGA"]) )/60;
                                  // $dif_bas_inides = abs($dif_bas_inides); $dif_bas_inides = round($dif_bas_inides,1);
                                  $fechaInicio = $detalle_otfc[$i]["BASCULA_VEHICULO"];
                                  $fechaFin = $detalle_otfc[$i]["INICIA_DESCARGA"];
                                  $dif_bas_inides = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                  /** INICIA CODE OTFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if( strtotime($fechaInicio) > strtotime($fechaFin) ){$fecha_t_v=false;}else{$fecha_t_v=true;}
                                  /** TERMINA CODE OTFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-1 bg-green"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        /** INICIA CODE OTFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                        switch ($fecha_t_v) {
                                          case true:
                                            echo '<a class="bg-default"><b> Diferencia: '.$dif_bas_inides.'</b> </a>';
                                            break;
                                          default:
                                            echo '<a class="bg-default"><code><i class="fa fa-warning"></i> Diferencia: -'.$dif_bas_inides.'</code><span class="text-muted">(fecha inicial es mayor a la final)</span></a>';
                                            break;
                                        }
                                        /** TERMINA CODE OTFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                    break;
                                }
                                //Termina Dif. Tiempo Vehículo en bascula a Inicia Descarga
                                //Inicia timeline Inicia Descarga
                                switch (true) {
                                  case ($detalle_otfc[$i]["INICIA_DESCARGA"] == false):
                                    echo '<li class="time-label">' ;
                                    if ($detalle_otfc[$i]["BASCULA_VEHICULO"] == true){
                                      $dif_min_des = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$detalle_otfc[$i]["BASCULA_VEHICULO"]);
                                      if ($dif_min_des>5){
                                        echo '<span class="bg-red">';
                                      }else{
                                        echo '<span class="bg-blue">';
                                      }
                                    }else{
                                      echo '<span class="bg-blue">';
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Inicia Descarga: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    if ($detalle_otfc[$i]["TERMINA_DESCARGA"] == true){
                                      $dif_min_des = $obj_agro_carga->dif_minutos($detalle_otfc[$i]["TERMINA_DESCARGA"],$detalle_otfc[$i]["INICIA_DESCARGA"]);
                                      if ($dif_min_des>60){
                                        echo '<span class="bg-red">' ;
                                      }else{
                                        echo '<span class="bg-green">' ;
                                      }
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Inicia Descarga: <code>'.$detalle_otfc[$i]["INICIA_DESCARGA"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Inicia Descarga
                                //Inicia Dif. Tiempo Inicia Descarga a Termina Descarga
                                switch (true){
                                  case ($detalle_otfc[$i]["INICIA_DESCARGA"] == false) || ($detalle_otfc[$i]["TERMINA_DESCARGA"] == false):
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-o bg-blue"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        echo '<a class="bg-default"><b> Diferencia: En proceso.</b> </a>';
                                      echo '</div>';
                                    echo '</li>';
                                    break;

                                  default:
                                  // $dif_inides_terdes = (strtotime($detalle_otfc[$i]["INICIA_DESCARGA"])-strtotime($detalle_otfc[$i]["TERMINA_DESCARGA"]) )/60;
                                  // $dif_inides_terdes = abs($dif_inides_terdes); $dif_inides_terdes = round($dif_inides_terdes,1);
                                  $fechaInicio = $detalle_otfc[$i]["INICIA_DESCARGA"];
                                  $fechaFin = $detalle_otfc[$i]["TERMINA_DESCARGA"];
                                  $dif_inides_terdes = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                  /** INICIA CODE OTFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if( strtotime($fechaInicio) > strtotime($fechaFin) ){$fecha_t_v=false;}else{$fecha_t_v=true;}
                                  /** TERMINA CODE OTFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-1 bg-green"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        /** INICIA CODE OTFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                        switch ($fecha_t_v) {
                                          case true:
                                            echo '<a class="bg-default"><b> Diferencia: '.$dif_inides_terdes.'</b> </a>';
                                            break;
                                          default:
                                            echo '<a class="bg-default"><code><i class="fa fa-warning"></i> Diferencia: -'.$dif_inides_terdes.'</code><span class="text-muted">(fecha inicial es mayor a la final)</span></a>';
                                            break;
                                        }
                                        /** TERMINA CODE OTFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                    break;
                                }
                                //Termina Dif. Tiempo Inicia Descarga a Termina Descarga
                                //Inicia timeline Termina Descarga
                                switch (true) {
                                  case ($detalle_otfc[$i]["TERMINA_DESCARGA"] == false):
                                    echo '<li class="time-label">' ;
                                    if ($detalle_otfc[$i]["INICIA_DESCARGA"] == true){
                                      $dif_min_des = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$detalle_otfc[$i]["INICIA_DESCARGA"]);
                                      if ($dif_min_des>60){
                                        echo '<span class="bg-red">';
                                      }else{
                                        echo '<span class="bg-blue">';
                                      }
                                    }else{
                                      echo '<span class="bg-blue">';
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Termina Descarga: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;

                                    if ($consulta_descarga_mov == true){
                                      $dif_min_des = $obj_agro_carga->dif_minutos($consulta_descarga_mov,$detalle_otfc[$i]["TERMINA_DESCARGA"]);
                                      if ($dif_min_des>5){
                                        echo '<span class="bg-red">' ;
                                      }else{
                                        echo '<span class="bg-green">' ;
                                      }
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }


                                      echo '<i class="fa fa-clock-o"></i> Termina Descarga: <code>'.$detalle_otfc[$i]["TERMINA_DESCARGA"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Termina Descarga
                                //Inicia Dif. Tiempo Termina Descarga a Pre-Registro
                                switch (true) {
                                  case ($detalle_otfc[$i]["TERMINA_DESCARGA"] == false) || ($consulta_descarga_mov == false):
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-o bg-blue"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        echo '<a class="bg-default"><b> Diferencia: En proceso.</b> </a>';
                                      echo '</div>';
                                    echo '</li>';
                                    break;

                                  default:
                                  // $dif_terdes_prereg = (strtotime($detalle_otfc[$i]["TERMINA_DESCARGA"])-strtotime($consulta_descarga_mov) )/60;
                                  // $dif_terdes_prereg = abs($dif_terdes_prereg); $dif_terdes_prereg = round($dif_terdes_prereg,1);
                                  $fechaInicio = $detalle_otfc[$i]["TERMINA_DESCARGA"];
                                  $fechaFin = $consulta_descarga_mov;
                                  $dif_terdes_prereg = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                  /** INICIA CODE OTFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if( strtotime($fechaInicio) > strtotime($fechaFin) ){$fecha_t_v=false;}else{$fecha_t_v=true;}
                                  /** TERMINA CODE OTFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-1 bg-green"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        /** INICIA CODE OTFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                        switch ($fecha_t_v) {
                                          case true:
                                            echo '<a class="bg-default"><b> Diferencia: '.$dif_terdes_prereg.'</b> </a>';
                                            break;
                                          default:
                                            echo '<a class="bg-default"><code><i class="fa fa-warning"></i> Diferencia: -'.$dif_terdes_prereg.'</code><span class="text-muted">(fecha inicial es mayor a la final)</span></a>';
                                            break;
                                        }
                                        /** TERMINA CODE OTFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                    break;
                                }
                                //Termina Dif. Tiempo Termina Descarga a Pre-Registro
                                //Inicia timeline Pre-Registro
                                switch (true) {
                                  case ($consulta_descarga_mov == false):
                                  $var_t_tiempo = "Tiempo Transcurrido: ";
                                    echo '<li class="time-label">' ;
                                    if ($detalle_otfc[$i]["TERMINA_DESCARGA"] == true){
                                      $dif_min_des = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$detalle_otfc[$i]["TERMINA_DESCARGA"]);
                                      if ($dif_min_des>5){
                                        echo '<span class="bg-red">';
                                      }else{
                                        echo '<span class="bg-blue">';
                                      }
                                    }else{
                                      echo '<span class="bg-blue">';
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Pre-Registro: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                  $var_t_tiempo = "Total de Tiempo (Inicio-Pre-Registro): ";
                                    echo '<li class="time-label">' ;
                                      echo '<span class="bg-green">' ;
                                      echo '<i class="fa fa-clock-o"></i> Pre-Registro: <code>'.$consulta_descarga_mov.'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Pre-Registro
                                //Inicia Total de tiempo Transcurrido
                                //$tiempo_descarga = $dif_reg_bas + $dif_bas_inides + $dif_inides_terdes + $dif_terdes_prereg ;
                                $fechaInicio = $detalle_otfc[$i]["R_VEHICULO"];
                                $fechaFin = $consulta_descarga_mov;
                                $tiempo_descarga = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                /** INICIA CODE OTFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if( strtotime($fechaInicio) > strtotime($fechaFin) ){$fecha_t_v=false;}else{$fecha_t_v=true;}
                                /** TERMINA CODE OTFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass bg-gray"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        /** INICIA CODE OTFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                        switch ($fecha_t_v) {
                                          case true:
                                            echo '<a class="bg-default"><b>'.$var_t_tiempo.'<br>'.$tiempo_descarga.'</b> </a>';
                                            break;
                                          default:
                                            /////////////////////////////////
                                            if ( $var_t_tiempo == "Tiempo Transcurrido: " )
                                            {
                                              //// INICIA CODE SI LA FECHA DE REG VEHICULO ES MAYOR A LA FECHA DE HOY DESCARGAS
                                              $fecha_hoy = strftime("%d-%m-%Y %H:%M:%S");
                                              switch (true) {
                                                case (strtotime($fecha_hoy) < strtotime($detalle_otfc[$i]["R_VEHICULO"]) ):
                                                  echo '<a class="bg-default"><code><i class="fa fa-warning"></i>'.$var_t_tiempo.' -'.$tiempo_descarga.'</code><span class="text-muted">(Fechas inconsistentes)</span></a>';
                                                  break;

                                                default:
                                                  echo '<a class="bg-default"><b>'.$var_t_tiempo.$tiempo_descarga.'</b></a>';
                                                  break;
                                              }
                                              //// TERMINA CODE SI LA FECHA DE REG VEHICULO ES MAYOR A LA FECHA DE HOY DESCARGAS
                                            }else{
                                              echo '<a class="bg-default"><code><i class="fa fa-warning"></i>'.$var_t_tiempo.' -'.$tiempo_descarga.'</code><span class="text-muted">(Fechas inconsistentes)</span></a>';
                                            }
                                            /////////////////////////////////
                                            break;
                                        }
                                        /** TERMINA CODE OTFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                //Termina Total de tiempo Transcurrido

                                    if ($consulta_descarga_mov == true){
                                      echo '<li class="time-label">';
                                        echo '<span class="bg-green">';
                                        echo '<i class="fa fa-clock-o"></i> Despacho de vehículo: <code>Finalizado</code>';
                                        echo '</span>';
                                      echo '</li>';
                                    }else{
                                      echo '<li class="time-label">';
                                        echo '<span class="bg-blue">';
                                        echo '<i class="fa fa-clock-o"></i> Despacho de vehículo: <code>En proceso</code>';
                                        echo '</span>';
                                      echo '</li>';
                                    }
                                ?>

                              </ul>
                            </div>
                          <!-- *-*-*-*-*-* TERMINA LINIA DE TIEMPO STATUS DE VEHICULO EN DESCARGA *-*-*-*-*-* -->


      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  <!-- TERMINA CODE HTML PARA STATUS DE CARROS OTFC  -->


          <?php
            //}//cierra if si ya se genero un registro en op_in_movimiento
          }//Termina for para traer los registros otfc finalizado
         ?>
<br><br>
    <div class="row no-print">
        <div class="col-xs-12">
          <a href="javascript:window.print()" class="btn btn-default"><i class="fa fa-print"></i> Imprimir </a>
        </div>
      </div>
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
            <?php
          }
          ?>
</body>
</html>
<?php conexion::cerrar($conn); ?>
