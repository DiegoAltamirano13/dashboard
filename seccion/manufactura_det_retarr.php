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

include_once '../class/Manufactura.php';
//INSTACIA PARA VER INFORMACION DEL ARR-RET DE CARGAS
$obj_det_ret_arr = new Det_ret_arr();

$plaza_manufac = $_SESSION['plaza_manufac'];
$valor_almacen = $_GET["almacen"];
$valor_arribo = $_GET["retarr"];
$valor_arribo_descarga = $_GET["arribo"];

/* HORA MEXICO */
$time = time();
date_default_timezone_set("America/Mexico_City");

//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '5');
$modulos_valida2 =Perfil::modulos_valida($_SESSION['iid_empleado'], '35');
$modulos_valida3 =Perfil::modulos_valida($_SESSION['iid_empleado'], '41');
if($modulos_valida == 0 AND $modulos_valida2 == 0 AND $modulos_valida3 == 0)
{
  header('Location: index.php');
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Argo Almacenadora | Dashboard</title>
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
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
  <!-- <link rel="stylesheet" href="../plugins/datatables/jquery.dataTables.min.css">    -->
  <link rel="stylesheet" href="../plugins/datatables/jquery.dataTables_themeroller.css">
  <link rel="shortcut icon" href="../assets/ico/favicon.png">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
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
        $info_retarr_cargasEnc = $obj_det_ret_arr->info_det_arr_retEnc($plaza_manufac,$valor_almacen,$valor_arribo,$valor_arribo_descarga);
        //echo $ofc."  ".$fol;
        for ($i=0; $i <count($info_retarr_cargasEnc) ; $i++) {
    ?>
    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table no-margin table-hover">
                            <!-- Inicia tabla para detalles de OFC -->
                              <thead>
                              <tr>
                                <th colspan="8"><div align="center" class="text-light-blue"><i class="fa fa-folder-open"></i> Detalles de Carga/Descarga</div></th>
                              </tr>
                              </thead>
                              <tbody>
                               <tr>
                                <th class="small">SOLICITUD</th>
                                <td><code><?= $info_retarr_cargasEnc[$i]["ID_SOLICITUD"] ?></code></td>
                                <th class="small">ARRIBO/RETIRO</th>
                                <td><code><?= $info_retarr_cargasEnc[$i]["IID_ARR_RET"] ?></td>
                               </tr>
                               <tr>
                                <th class="small">CLIENTE</th>
                                <td><code><?= $info_retarr_cargasEnc[$i]["CLIENTE"] ?></code></td>
                                <th class="small">PLAZA</th>
                                <td><code><?= $info_retarr_cargasEnc[$i]["V_RAZON_SOCIAL"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">ALMACEN</th>
                                <td><code><?= $info_retarr_cargasEnc[$i]["V_NOMBRE"] ?></code></td>
                                <th class="small">REGIMEN</th>

                                <td><code><?php
                                          if ($info_retarr_cargasEnc[$i]["IID_REGIMEN"] == 1 ) {
                                            echo "NACIONAL";
                                          }elseif ($info_retarr_cargasEnc[$i]["IID_REGIMEN"] == 2) {
                                            echo "FISCAL";
                                          }
                                           ?></code></td>
                               </tr>
                               <tr>
                                 <th class="small">MERCANCIA</th>
                                 <td><code><?= $info_retarr_cargasEnc[$i]["V_MERCANCIA"] ?></code></td>
                                 <th class="small">CANTIDAD</th>
                                 <td><code> <?php echo $info_retarr_cargasEnc[$i]["N_CANTIDAD_UME"]." "; ?> <?= $info_retarr_cargasEnc[$i]["UME_NOMBRE"] ?></code></td>
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
                                <td><code><?= $info_retarr_cargasEnc[$i]["D_FEC_RECEPCION"] ?></code></td>
                                <th class="small">CHOFER</th>
                                <td><code><?= $info_retarr_cargasEnc[$i]["V_NOMBRE_CHOFER"] ?></code></td>
                               </tr>
                               <tr>
                                <th class="small">TRANSPORTE</th>
                                <td><code><?= $info_retarr_cargasEnc[$i]["V_TRANSPORTES"] ?></code></td>
                                <th class="small">PLACAS</th>
                                <td><code><?= $info_retarr_cargasEnc[$i]["V_PLACAS_VEHICULO_REAL"] ?></code></td>
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
                                  case ($info_retarr_cargasEnc[$i]["REGISTRADO"] == false):
                                    echo '<li class="time-label">' ;
                                      echo '<span class="bg-blue">' ;
                                      echo '<i class="fa fa-clock-o"></i> Registro de vehículo: <code>En proceso'.$info_retarr_cargasEnc[$i]["REGISTRADO"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    $fechaInicio = $info_retarr_cargasEnc[$i]["REGISTRADO"];
                                    $fechaFin = $info_retarr_cargasEnc[$i]["LLEGA"];
                                    $dif_min = (strtotime($fechaFin)-strtotime($fechaInicio))/60;
//                                    $obj_det_ret_arr->dif_minutos($info_retarr_cargasEnc[$i]["INICIO"],$info_retarr_cargasEnc[$i]["REGISTRADO"]);
                                    if ($dif_min>5){
                                      echo '<span class="bg-red">' ;
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                    echo '<i class="fa fa-clock-o"></i> Registro de vehículo: <code>'.$info_retarr_cargasEnc[$i]["REGISTRADO"].'</code>' ;
                                    echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }

                                //Termina timeline Registro de vehículo
                                //Inicia Dif. Tiempo Registro de vehículo a Vehículo en bascula
                                switch (true){
                                  case ($info_retarr_cargasEnc[$i]["REGISTRADO"] == false) || ($info_retarr_cargasEnc[$i]["LLEGA"] == false):
                                    echo '<li>';
                                      echo '<i class="fa fa-hourglass-o bg-blue"></i>';
                                      echo '<div class="timeline-item bg-default">';
                                        echo '<a class="bg-default"><b> Diferencia: En proceso.</b> </a>';
                                      echo '</div>';
                                    echo '</li>';
                                    break;

                                  default:
                                  // $dif_reg_bas = (strtotime($detalle_ofc[$i]["R_VEHICULO"])-strtotime($detalle_ofc[$i]["BASCULA_VEHICULO"]) )/60;
                                  // $dif_reg_bas = abs($dif_reg_bas); $dif_reg_bas = round($dif_reg_bas,1);
                                  $fechaInicio = $info_retarr_cargasEnc[$i]["REGISTRADO"];
                                  $fechaFin = $info_retarr_cargasEnc[$i]["LLEGA"];
                                   //$info_retarr_cargasEnc[$i]["INICIA"];
                                  //echo $fechaInicio."  ".$fechaFin;
                                  $dif_reg_bas = $obj_det_ret_arr->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
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
                                  case ($info_retarr_cargasEnc[$i]["INICIA"] == false):
                                    echo '<li class="time-label">' ;
                                      $dif_min = (strtotime("%d-%m-%Y %H:%M:%S")-strtotime($info_manufac_car_des[$i]["INICIA"]))/60;
                                      //$dif_min = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$info_retarr_cargasEnc[$i]["INICIA"]);
                                      if ($dif_min>5){
                                      echo '<span class="bg-red">' ;
                                      }else{
                                      echo '<span class="bg-blue">' ;
                                      }
                                      echo '<i class="fa fa-clock-o"></i> Vehiculo Enrampado: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    $fechaInicio = $info_retarr_cargasEnc[$i]["LLEGA"];
                                    $fechaFin = $info_retarr_cargasEnc[$i]["INICIA"];
                                    $dif_min = (strtotime($fechaFin)-strtotime($fechaInicio))/60;
                                    //$dif_min = $obj_agro_carga->dif_minutos($info_retarr_cargasEnc[$i]["INICIA"],$info_retarr_cargasEnc[$i]["FIN"]);
                                    if ($dif_min>5){
                                      echo '<span class="bg-red">' ;
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Vehiculo Enrampado: <code>'.$info_retarr_cargasEnc[$i]["LLEGA"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Vehículo en bascula
                                //Inicia Dif. Tiempo Vehículo en bascula a Inicia Carga
                                switch (true){
                                  case ($info_retarr_cargasEnc[$i]["INICIA"] == false) || ($info_retarr_cargasEnc[$i]["LLEGA"] == false):
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
                                  $fechaInicio = $info_retarr_cargasEnc[$i]["LLEGA"];
                                  $fechaFin = $info_retarr_cargasEnc[$i]["INICIA"];
                                  $dif_bas_inides = $obj_det_ret_arr->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
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
                                  case ($info_retarr_cargasEnc[$i]["FIN"] == false):
                                    echo '<li class="time-label">' ;
                                    $dif_min = (strtotime("%d-%m-%Y %H:%M:%S")-strtotime($info_manufac_car_des[$i]["INICIA"])/60);
                                    //$dif_min = $obj_det_ret_arr->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$info_retarr_cargasEnc[$i]["INICIA"]);
                                      if ($dif_min>5){
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
                                    $fechaInicio = $info_retarr_cargasEnc[$i]["INICIA"];
                                    $fechaFin = $info_retarr_cargasEnc[$i]["FIN"];
                                    $dif_min = (strtotime($fechaFin)-strtotime($fechaInicio))/60;
                                    //$dif_min = $obj_agro_carga->dif_minutos($info_retarr_cargasEnc[$i]["FIN"],$info_retarr_cargasEnc[$i]["FIN"]);
                                    if ($dif_min>60){
                                      echo '<span class="bg-red">' ;
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Inicia Carga: <code>'.$info_retarr_cargasEnc[$i]["INICIA"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Inicia Carga
                                //Inicia Dif. Tiempo Inicia Carga a Termina Carga
                                switch (true){
                                  case ($info_retarr_cargasEnc[$i]["FIN"] == false) || ($info_retarr_cargasEnc[$i]["INICIA"] == false):
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
                                  $fechaInicio = $info_retarr_cargasEnc[$i]["INICIA"];
                                  $fechaFin = $info_retarr_cargasEnc[$i]["FIN"];
                                  $dif_inicar_tercar = $obj_det_ret_arr->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
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
                                  case ($info_retarr_cargasEnc[$i]["DESPACHO"] == false):
                                    echo '<li class="time-label">' ;
                                    $dif_min = (strftime("%d-%m-%Y %H:%M:%S")-strtotime($info_retarr_cargasEnc[$i]["DESPACHO"]));
                                      if ($dif_min>60){
                                      echo '<span class="bg-red">' ;
                                      }else{
                                      echo '<span class="bg-blue">' ;
                                      }
                                      echo '<i class="fa fa-clock-o"></i> Finaliza Carga: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    $fechaInicio = $info_retarr_cargasEnc[$i]["FIN"];
                                    $fechaFin = $info_retarr_cargasEnc[$i]["DESPACHO"];
                                    $dif_min = (strtotime($fechaFin)-strtotime($fechaInicio))/60;
                                    if ($dif_min>5){
                                      echo '<span class="bg-red">' ;
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Finaliza Carga: <code>'.$info_retarr_cargasEnc[$i]["FIN"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }
                                //Termina timeline Termina Carga
                                //Inicia Dif. Tiempo Termina Carga a Documentación
                                switch (true) {
                                  case ($info_retarr_cargasEnc[$i]["FIN"] == false) || ($info_retarr_cargasEnc[$i]["DESPACHO"] == false):
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
                                  $fechaInicio = $info_retarr_cargasEnc[$i]["FIN"];
                                  $fechaFin = $info_retarr_cargasEnc[$i]["DESPACHO"];
                                  $dif_tercar_doc = $obj_det_ret_arr->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
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
                                  case ($info_retarr_cargasEnc[$i]["DESPACHO"] == false):
                                  $var_t_tiempo = "Tiempo Transcurrido: ";
                                    echo '<li class="time-label">' ;
                                    $dif_min = (strftime("%d-%m-%Y %H:%M:%S")-strftime($info_retarr_cargasEnc[$i]["DESPACHO"])/60);
                                      if ($dif_min>5){
                                      echo '<span class="bg-red">' ;
                                      }else{
                                      echo '<span class="bg-blue">' ;
                                      }
                                      echo '<i class="fa fa-clock-o"></i> Despacho: <code>En proceso</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;

                                  default:
                                    echo '<li class="time-label">' ;
                                    $fechaInicio = $info_retarr_cargasEnc[$i]["INICA"];
                                    $fechaFin = $info_retarr_cargasEnc[$i]["DESPACHO"];
                                    $dif_min = (strtotime($fechaFin)-strtotime($fechaInicio))/60;
                                    if ($dif_min>$info_retarr_cargasEnc[$i]["N_TIEMPO_OPERACION"]){
                                      echo '<span class="bg-red">' ;
                                    }else{
                                      echo '<span class="bg-green">' ;
                                    }
                                      echo '<i class="fa fa-clock-o"></i> Despacho Carga: <code>'.$info_retarr_cargasEnc[$i]["DESPACHO"].'</code>' ;
                                      echo '</span> ' ;
                                    echo '</li>' ;
                                    break;
                                }

                                switch (true) {
                                  case ($info_retarr_cargasEnc[$i]["FIN"] == false) || ($info_retarr_cargasEnc[$i]["DESPACHO"] == false):
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
                                  $fechaInicio = $info_retarr_cargasEnc[$i]["INICIA"];
                                  $fechaFin = $info_retarr_cargasEnc[$i]["DESPACHO"];
                                  $dif_tercar_doc = $obj_det_ret_arr->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                                  $dif_min = (strtotime($fechaFin)-strtotime($fechaInicio))/60;
                                  /** INICIA CODE OFC PARA VER SI LA FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                  if($dif_min > $info_retarr_cargasEnc[$i]["N_TIEMPO_OPERACION"]){$fecha_t_v=false;}else{$fecha_t_v=true;}
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
                                            echo '<a class="bg-default"><code><i class="fa fa-warning"></i> Diferencia entre tiempo programado: '.$info_retarr_cargasEnc[$i]["N_TIEMPO_OPERACION"]." y tiempo real operación ".$dif_min.'</code><span class="text-muted"></span></a>';
                                            break;
                                        }
                                        /** TERMINA CODE OFC FECHA INI ES MAYOR O MENOR A LA DE FIN **/
                                      echo '</div>';
                                    echo '</li>';
                                    break;
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
    <!-- TERMINA TABLA DETALLES RETIRO EN CARGAS -->

    <div class="row no-print">
        <div class="col-xs-12">
          <a href="javascript:window.print()"  class="btn btn-default"><i class="fa fa-print"></i> Imprimir </a>
        </div>
      </div>
  </section>






<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#example').DataTable( {

        "scrollY": 200,
        "scrollX": true,
        "language": {
            "url": "../plugins/datatables/Spanish.json"
        }
    } );
} );
</script>
<!-- SlimScroll -->
<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
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
</body>
</html>
<?php conexion::cerrar($conn); ?>
