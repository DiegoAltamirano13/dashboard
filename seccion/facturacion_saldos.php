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

include_once '../class/Factur_Saldos.php';
  $obj_class = new Factur_Saldos();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 46);
$modulos_valida2 = Perfil::modulos_valida($_SESSION['iid_empleado'], 46);
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

    if ($dia == "Sabado") {
      #echo "HERE";
      $fecha = $fecha;
    }else {
      #echo "HERE2";
      $fecha = date("d/m/Y");
      $first = strtotime('last saturday');
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

    if ($dia == "Sabado") {
    //echo "HERE3";
    $fecha_actual = date("d-m-Y");
    $fecha = date("d/m/Y",strtotime($fecha_actual."- 7 days"));
    //$fecha =  date('d/m/Y', $first);
  }else {
    //echo "HERE4";
    $first = strtotime('last saturday');
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

$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}

$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}

$tipo_rep = "mensual";
if (isset($_GET["tipo_rep"])) {
  $tipo_rep = $_GET["tipo_rep"];
}

$cliente = "ALL";
if (isset($_GET["cliente"])) {
    $cliente = $_GET["cliente"];
}
//GRAFICA si anita lava la tina la tina la bananita
#phpinfo();

?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>SALDO POR CLIENTE </small>
      </h1>
    </section>
    <!-- Main content -->

    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

<!-- ############################ SECCION GRAFICA Y WIDGETS ############################# -->
<section>
  <div class="row">
    <!--tabla reporte de 52 semanas-->
    <div class="col-md-9"><!-- col-md-9 -->
    <div class="box box-primary">
      <div class="box-body"><!--box-body-->

        <div class="row">

          <div class="col-md-12">

            <?php if ($tipo_rep == "anual") { ?>
              <section>
                <div class="box box-success">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-table"></i> SALDOS POR CLIENTE SEMANAL</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body"><!--box-body-->

                    <div class="table-responsive" id="container">
                      <table id="tabla_nomina_real" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                            <th class="small" bgcolor="#2a7a1a"><font color="white">IID</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">RAZON SOCIAL</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white"><?php

                                                                                    $fecha1 = str_replace('/', '-' , $fecha);
                                                                                    //echo "gas".$fecha;
                                                                                    $fecha1 =  date("d-m-Y",strtotime($fecha1."- 12 month"));
                                                                                    //echo $fecha;
                                                                                    //echo $fecha1;
                                                                                    $fecha32 = date('d/m/Y', strtotime($fecha1));
                                                                                    #echo $fecha;
                                                                                    date_default_timezone_set('UTC');

                                                                                    date_default_timezone_set("America/Mexico_City");
                                                                                    $formatted = vsprintf('%3$04d/%2$02d/%1$02d', sscanf($fecha32,'%02d/%02d/%04d'));
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

                                                                                    #echo $dia;

                                                                                    if ($dia == "Sabado") {
                                                                                      echo $fecha32;
                                                                                    }else {
                                                                                      $format = "d/m/Y";
                                                                                      $date = DateTime::createFromFormat($format, $fecha32);
                                                                                      $date->modify('next saturday');
                                                                                      $fecha32= $date->format("d/m/Y");
                                                                                      echo $fecha32;
                                                                                    }

                                                                                    ?></font></th>
                                        <?php $fecha1 = $fecha32;
                                              for ($i=1; $i <=  52; $i++) {
                                        ?>
                                                                                    <th class="small" bgcolor="#2a7a1a"><font color="white">
                                                                                    <?php

                                                                                        $fecha1 = str_replace('/', '-' , $fecha1);
                                                                                        $fecha1 =  date("d-m-Y",strtotime($fecha1."+ 7 days"));
                                                                                        $fecha1 = date('d/m/Y', strtotime($fecha1));
                                                                                        echo $fecha1;

                                                                                    ?></font></th><!--eSTO SE DEBE CAMBIAR-->
                                    <?php } ?>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">SALDO MENOR A 90 DIAS</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">SALDO MAYOR A 90 DIAS</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">FECHA ULTIMO PAGO</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">IMPORTE PAGADO</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">DIAS ULTIMO PAGO</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">VALOR MECANCIA</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">NOMBRE MECANCIA</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">PROMEDIO DIAS FACTURADOS</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">VER COMENTARIOS</font></th>

                          </tr>
                        </thead>
                        <tbody>
                            <?php
                            $graficaMensual = $obj_class->tabla_porcentaje($fecha, $plaza, $almacen);
                            for ($i=0; $i <count($graficaMensual) ; $i++) {  ?>
                              <tr>
                              <td class="small">
                              <?php
                                  $valor_cliente = $graficaMensual[$i]["IID_NUM_CLIENTE"];
                                  echo $graficaMensual[$i]["IID_NUM_CLIENTE"];
                                ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo $graficaMensual[$i]["V_RAZON_SOCIAL"];
                                  ?>
                              </td>

                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_1"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_2"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_3"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_4"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_5"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_6"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_7"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_8"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_9"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_10"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_11"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_12"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_13"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_14"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_15"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_16"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_17"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_18"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_19"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_20"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_21"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_22"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_23"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_24"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_25"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_26"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_27"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_28"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_29"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_30"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_31"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_32"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_33"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_34"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_35"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_36"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_37"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_38"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_39"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_40"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_41"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_42"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_43"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_44"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_45"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_46"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_47"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_48"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_49"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_50"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_51"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_52"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_53"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["MENOS_90"], 2);
                                  ?>
                              </td>

                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["MAS_90"], 2);
                                  ?>
                              </td>

                              <td class="small">
                                <?php
                                    echo $graficaMensual[$i]["FECHA_ULTIMO_PAGO"];
                                  ?>
                              </td>

                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["IMPORTE_PAGADO"], 2);
                                  ?>
                              </td>

                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["DIAS_ULTIMO_PAGO"], 2);
                                  ?>
                              </td>

                              <td class="small">

                                <?php
                                    echo number_format($graficaMensual[$i]["VALOR_MERCA"], 2);
                                  ?>
                              </td>

                              <td class="small">
                                  <?php
                                  echo '<a href="mercancias_saldos_det.php?cliente='.$graficaMensual[$i]["IID_NUM_CLIENTE"].'&plaza='.$plaza.'&fecha='.$fecha.'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> MERCANCIA</a>';
                                   ?>
                              </td>
                              <td>
                                <?php
                                    $promedio_dias = $obj_class->promedio_dias($graficaMensual[$i]["IID_NUM_CLIENTE"], $plaza, $fecha);
                                    for ($k=0; $k <count($promedio_dias) ; $k++) {
                                          echo number_format($promedio_dias[$k]["DIAS_PROM"], 2);
                                    }
                                 ?>
                              </td>

                              <td class="small">
                                  <?php
                                  echo '<a href="saldos_det.php?cliente='.$graficaMensual[$i]["IID_NUM_CLIENTE"].'&plaza='.$plaza.'&fecha='.$fecha.'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> VER</a>';
                                   ?>
                              </td>

                              </tr>
                                  <?php
                                }
                              ?>
                       </tfoot>
                      </table>
                    </div>

                  </div><!--/.box-body-->
                </div>
              </section>
            <?php } ?>


            <?php if ($tipo_rep == "mensual") { ?>
              <section>
                <div class="box box-success">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-table"></i> SALDOS POR CLIENTE SEMANAL A UN MES</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body"><!--box-body-->

                    <div class="table-responsive" id="container">
                      <table id="tabla_nomina_real2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                            <th class="small" bgcolor="#2a7a1a"><font color="white">IID</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">RAZON SOCIAL</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white"><?php

                                                                                    $fecha1 = str_replace('/', '-' , $fecha);
                                                                                    //echo "gas".$fecha;
                                                                                    $fecha1 =  date("d-m-Y",strtotime($fecha1."- 28 days"));
                                                                                    //echo $fecha;
                                                                                    //echo $fecha1;
                                                                                    $fecha32 = date('d/m/Y', strtotime($fecha1));
                                                                                    #echo $fecha;
                                                                                    date_default_timezone_set('UTC');

                                                                                    date_default_timezone_set("America/Mexico_City");
                                                                                    $formatted = vsprintf('%3$04d/%2$02d/%1$02d', sscanf($fecha32,'%02d/%02d/%04d'));
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

                                                                                    #echo $dia;

                                                                                    if ($dia == "Sabado") {
                                                                                      #echo $fecha32;
                                                                                      $fecha32 = str_replace('/', '-' , $fecha32);
                                                                                      //echo "gas".$fecha;
                                                                                      $fecha32 =  date("d-m-Y",strtotime($fecha32."+ 7 days"));
                                                                                      $fecha32 = date('d/m/Y', strtotime($fecha32));
                                                                                      //$fecha32= $date->format("d/m/Y");
                                                                                      echo $fecha32;
                                                                                    }else {
                                                                                      $format = "d/m/Y";
                                                                                      $date = DateTime::createFromFormat($format, $fecha32);
                                                                                      $date->modify('next saturday');
                                                                                      $fecha32= $date->format("d/m/Y");
                                                                                      echo $fecha32;
                                                                                    }

                                                                                    ?></font></th>
                                        <?php $fecha1 = $fecha32;
                                              for ($i=1; $i <=  3; $i++) {
                                        ?>
                                                                                    <th class="small" bgcolor="#2a7a1a"><font color="white">
                                                                                    <?php

                                                                                        $fecha1 = str_replace('/', '-' , $fecha1);
                                                                                        $fecha1 =  date("d-m-Y",strtotime($fecha1."+ 7 days"));
                                                                                        $fecha1 = date('d/m/Y', strtotime($fecha1));
                                                                                        echo $fecha1;

                                                                                    ?></font></th><!--eSTO SE DEBE CAMBIAR-->
                                    <?php } ?>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">SALDO MENOR A 90 DIAS</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">SALDO MAYOR A 90 DIAS</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">FECHA ULTIMO PAGO</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">IMPORTE PAGADO</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">DIAS ULTIMO PAGO</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">VALOR MECANCIA</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">NOMBRE MECANCIA</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">PROMEDIO DIAS FACTURADOS</font></th>
                                      <th class="small" bgcolor="#2a7a1a"><font color="white">VER COMENTARIOS</font></th>

                          </tr>
                        </thead>
                        <tbody>
                            <?php
                            $graficaMensual = $obj_class->tabla_porcentaje2($fecha, $plaza, $almacen);
                            for ($i=0; $i <count($graficaMensual) ; $i++) {  ?>
                              <tr>
                              <td class="small">
                              <?php
                                  $valor_cliente = $graficaMensual[$i]["IID_NUM_CLIENTE"];
                                  echo $graficaMensual[$i]["IID_NUM_CLIENTE"];
                                ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo $graficaMensual[$i]["V_RAZON_SOCIAL"];
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_50"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_51"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_52"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["SALDO_ADEUDADO_SEMANA_53"], 2);
                                  ?>
                              </td>
                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["MENOS_90"], 2);
                                  ?>
                              </td>

                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["MAS_90"], 2);
                                  ?>
                              </td>

                              <td class="small">
                                <?php
                                    echo $graficaMensual[$i]["FECHA_ULTIMO_PAGO"];
                                  ?>
                              </td>

                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["IMPORTE_PAGADO"], 2);
                                  ?>
                              </td>

                              <td class="small">
                                <?php
                                    echo number_format($graficaMensual[$i]["DIAS_ULTIMO_PAGO"], 2);
                                  ?>
                              </td>

                              <td class="small">

                                <?php
                                    echo number_format($graficaMensual[$i]["VALOR_MERCA"], 2);
                                  ?>
                              </td>

                              <td class="small">
                                  <?php
                                  echo '<a href="mercancias_saldos_det.php?cliente='.$graficaMensual[$i]["IID_NUM_CLIENTE"].'&plaza='.$plaza.'&fecha='.$fecha.'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> MERCANCIA</a>';
                                   ?>
                              </td>
                              <td>
                                <?php
                                    $promedio_dias = $obj_class->promedio_dias($graficaMensual[$i]["IID_NUM_CLIENTE"], $plaza, $fecha);
                                    for ($k=0; $k <count($promedio_dias) ; $k++) {
                                          echo number_format($promedio_dias[$k]["DIAS_PROM"], 2);
                                    }
                                 ?>
                              </td>

                              <td class="small">
                                  <?php
                                  echo '<a href="saldos_det.php?cliente='.$graficaMensual[$i]["IID_NUM_CLIENTE"].'&plaza='.$plaza.'&fecha='.$fecha.'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> VER</a>';
                                   ?>
                              </td>

                              </tr>
                                  <?php
                                }
                              ?>
                       </tfoot>
                      </table>
                    </div>

                  </div><!--/.box-body-->
                </div>
              </section>
            <?php } ?>

          </div>
          <!--GRAFICA NOMINA POR MES DIEGO ALTAMIRANO SUAREZ-->
        </div>

      </div><!--/.box-body-->
    </div>
    </div><!-- /.col-md-9 -->

    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
          <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="facturacion_saldos.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
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
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
            <select class="form-control select2" id="fil_plaza" style="width: 100%;">
              <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $departamento = 1;
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
              $selectAlmacen = $obj_class->almacenSql($plazas);
              for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
                <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
              <?php } ?>
            </select>
          </div>

          <div class="input-group" style="display:none">
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

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Tipo:</span>
            <select class="form-control" id="tipo_rep">
              <option value= "mensual" <?php if ($tipo_rep == "mensual") {echo "selected";} ?>>Desglozado 1 Mes</option>
              <option value="anual" <?php if ($tipo_rep == "anual") {echo "selected";} ?>>Desglozado 1 Año</option>
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
  tipo_rep = $('#tipo_rep').val();
  fil_contrato = $('#fil_contrato').val();
  fil_departamento = $('#fil_departamento').val();
  fil_area = $('#fil_area').val();
  fil_check = 'off';

  //Fill habilitados
  fil_habilitado = 'off';

  url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&cliente='+cliente+'&tipo_rep='+tipo_rep;
  if ($('input[name="fil_check"]').is(':checked')) {
    fil_check = 'on';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&cliente='+cliente+'&tipo_rep='+tipo_rep;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&cliente='+cliente+'&tipo_rep='+tipo_rep;
    }

  }else{
    fil_check = 'off';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
        fil_habilitado = 'on';
        url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado+'&cliente='+cliente+'&tipo_rep='+tipo_rep;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado+'&cliente='+cliente+'&tipo_rep='+tipo_rep;
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
<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {
    $('#tabla_nomina_real').DataTable( {
      "ordering": false,
      "searching":true,
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
    $('#tabla_nomina_real2').DataTable( {
      "ordering": false,
      "searching":true,
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
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
var someDateString = moment().format("DD/MM/YYYY");
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
      //maxYear: parseInt(moment().format('YYYY'),10)
      //maxYear: parseInt(moment.min().format('YYYY'),10)
      maxDate: someDateString
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
