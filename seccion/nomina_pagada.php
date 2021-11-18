<?php
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:nomina_pagada.php");
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
include '../class/Nomina_pagada.php';
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
  if( $_GET["plaza"] == "CORPORATIVO" || $_GET["plaza"] == "CÓRDOBA" || $_GET["plaza"] == "MÉXICO" || $_GET["plaza"] == "GOLFO" || $_GET["plaza"] == "PENINSULA" || $_GET["plaza"] == "PUEBLA" || $_GET["plaza"] == "BAJIO" || $_GET["plaza"] == "OCCIDENTE" || $_GET["plaza"] == "NORESTE" ){
    $plaza = $_GET["plaza"];
  }else{
    $plaza = "ALL";
  }
}

$tipo = "ALL";
if ( isset($_GET["tipo"]) ){
  switch ($_GET["tipo"]) {
    case '1': $tipo = $_GET["tipo"]; break;
    case '2': $tipo = $_GET["tipo"]; break;
    case '8': $tipo = $_GET["tipo"]; break;
    case '9': $tipo = $_GET["tipo"]; break;
    case '12': $tipo = $_GET["tipo"]; break;
    case '17': $tipo = $_GET["tipo"]; break;
    case '22': $tipo = $_GET["tipo"]; break;
    case '18': $tipo = $_GET["tipo"]; break;
    case '10': $tipo = $_GET["tipo"]; break;
    case '11': $tipo = $_GET["tipo"]; break;
    //Conceptos de Nueva Tabla
    case '23': $tipo = $_GET["tipo"]; break;
    case '24': $tipo = $_GET["tipo"]; break;
    case '25': $tipo = $_GET["tipo"]; break;
    case '26': $tipo = $_GET["tipo"]; break;
    case '27': $tipo = $_GET["tipo"]; break;
    case '28': $tipo = $_GET["tipo"]; break;
    case '29': $tipo = $_GET["tipo"]; break;
    case '30': $tipo = $_GET["tipo"]; break;
    case '31': $tipo = $_GET["tipo"]; break;
    case '32': $tipo = $_GET["tipo"]; break;
    case '33': $tipo = $_GET["tipo"]; break;
    default: $tipo = "ALL"; break;
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

//WIDGETS
$widgetsNomina = $modelNomina->widgetsNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area);
//cantidad de meses
$cantidadEmp = $modelNomina->cantidadEmpleados($fecha, $plaza);
//GRAFICA NOMINA
$graficaNomina = $modelNomina->graficaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area);
$graficaNominaMes = $modelNomina->graficaPorMes($fecha,$plaza,$tipo);
//TABLA DETALLE DE NOMINA PAGADA
$tablaNomina = $modelNomina->tablaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area);

$w_vales = $modelNomina->widgetFondoAhorro($fecha,$plaza);
$w_valesAcum = $modelNomina->widgetFondoAhorro3($fecha,$plaza);
$w_fondo = $modelNomina->widgetFondoAhorro2($fecha,$plaza);
$w_fondoAcum = $modelNomina->widgetFondoAhorro4($fecha,$plaza);

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
    <h1>Dashboard<small>Nomina Pagada</small></h1>
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

          <div class="col-md-9">
            <div id="graficaNom"></div>
          </div>

          <div class="col-md-3">

            <!-- WIDGETS #1 -->
            <div class="info-box bg-morado">
              <span class="info-box-icon"><i class="fa fa-money"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total Pagado</span>
                <span class="info-box-number"><?= number_format($widgetsNomina[0]["TOTAL"],2) ?></span>
                <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description" title="<?=$fecha?>">Fecha de consulta: <?=$fecha?></span>
              </div>
            </div>
            <!-- WIDGETS #1 -->
            <?php if( !isset($_GET["plaza"]) || $_GET["plaza"] == "ALL" ){ ?>
            <div class="info-box bg-verde">
              <span class="info-box-icon"><i class="fa fa-link"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Promedio</span>
                <span class="info-box-number"><?= number_format( ($widgetsNomina[0]["TOTAL"])/9,2 ) ?></span>
                <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description" title="<?=$fecha?>">Fecha de consulta: <?=$fecha?></span>
              </div>
            </div>
            <?php } ?>
            <!-- WIDGET PORCENTAJE ENTRE MESES -->
            <div class="info-box bg-green">
              <span class="info-box-icon"><i class="fa fa-percent"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">PROMEDIO POR EMPLEADO</span>
                <span class="info-box-number"><?= number_format($widgetsNomina[0]["TOTAL"]/$cantidadEmp[0]["EMPLEADOS"],2) ?></span>
                <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description" title="<?=$fecha?>">Porcentaje Mensual: <?=$fecha?></span>
              </div>
            </div>

          </div>
          <!--GRAFICA NOMINA POR MES DIEGO ALTAMIRANO SUAREZ-->
          <div class="col-md-12">
            <div id="graficaNomMes"></div>
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
        <a href="nomina_pagada.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
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
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
          <select class="form-control select2" id="nomPlaza" style="width: 100%;">
            <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $select_plaza = $modelNomina->sql(2,$depto);
            for ($i=0; $i <count($select_plaza) ; $i++) { ?>
              <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
            <?php } ?>
          </select>
        </div>
        <!-- FILTRAR POR TIPO NOMINA -->
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-cubes"></i> Conceptos:</span>
          <select class="form-control" id="nomTipo" style="width: 100%;">
            <option value="ALL" <?php if( $tipo == 'ALL'){echo "selected";} ?> >ALL</option>
            <option value="1" <?php if ($tipo == "1"){ echo "selected";}?>>SUELDO</option>
            <option value="25" <?php if ($tipo == "25"){ echo "selected";}?>>RETROACTIVO</option>
            <option value="8" <?php if ($tipo == "8"){ echo "selected";}?>>GRATIFICACION UNICA</option>
            <option value="9" <?php if ($tipo == "9"){ echo "selected";}?>>PTU</option>
            <option value="12" <?php if ($tipo == "12"){ echo "selected";}?>>TIEMPO EXTRA</option>
            <option value="17" <?php if ($tipo == "17"){ echo "selected";}?>>PRIMA ANTIGUEDAD</option>
            <option value="22" <?php if ($tipo == "22"){ echo "selected";}?>>COMISION</option>
            <option value="18" <?php if ($tipo == "18"){ echo "selected";}?>>INDENMIZACION</option>
            <option value="10" <?php if ($tipo == "10"){ echo "selected";}?>>VACACIONES </option>
            <option value="11" <?php if ($tipo == "11"){ echo "selected";}?>>PRIM. VACACIONAL</option>
            <option value="23" <?php if ($tipo == "23"){ echo "selected";}?>>642</option>
            <option value="24" <?php if ($tipo == "24"){ echo "selected";}?>>AGUINALDO</option>
            <option value="2" <?php  if ($tipo == "2"){ echo "selected";}?>>VALES</option>
            <option value="33" <?php if ($tipo == "33"){ echo "selected";}?>>F.A. PATRON</option>
            <option value="32" <?php if ($tipo == "32"){ echo "selected";}?>>SAR</option>
            <option value="26" <?php if ($tipo == "26"){ echo "selected";}?>>C Y V</option>
            <option value="27" <?php if ($tipo == "27"){ echo "selected";}?>>IMSS</option>
            <option value="28" <?php if ($tipo == "28"){ echo "selected";}?>>INFONAVIT</option>
            <option value="29" <?php if ($tipo == "29"){ echo "selected";}?>>3% ISN/MES</option>
            <option value="30" <?php if ($tipo == "30"){ echo "selected";}?>>FOMENTO A LA EDUCACIÓN</option>
            <option value="31" <?php if ($tipo == "31"){ echo "selected";}?>>PROVISIONES</option>
          </select>
        </div>
        <div class="input-group">
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>

      </div><!--/.box-body-->
    </div>

    <div class="info-box bg-blue">
      <span class="info-box-icon"><i class="fa fa-money"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">MONTO DE VALES</span>
        <span class="info-box-number"><?= number_format($w_vales[0]["MONTO_VALES"],2) ?></span>
        <div class="progress">
          <div class="progress-bar" style="width: 70%"></div>
        </div>
        <span class="progress-description" title="<?=$fecha?>">Fecha: <?=$fecha?></span>
      </div>
    </div>

    <div class="info-box bg-yellow">
      <span class="info-box-icon"><i class="fa fa-money"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">MONTO DE VALES ACUMULADO</span>
        <span class="info-box-number"><?= number_format($w_valesAcum[0]["VALES_ACUMULADO"],2) ?></span>
        <div class="progress">
          <div class="progress-bar" style="width: 70%"></div>
        </div>
        <span class="progress-description" title="<?=$fecha?>">Fecha: 01/01/<?=substr($fecha,6, 4)?> AL <?=substr($fecha,11, 10)?></span>
      </div>
    </div>


    <div class="info-box bg-blue" style="display:none;">
      <span class="info-box-icon"><i class="fa fa-money"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">FONDO DE AHORRO PATRON</span>
        <span class="info-box-number"><?= number_format($w_fondo[0]["FONDO"],2) ?></span>
        <div class="progress">
          <div class="progress-bar" style="width: 70%"></div>
        </div>
        <span class="progress-description" title="<?=$fecha?>">Fecha: <?=$fecha?></span>
      </div>
    </div>

    <div class="info-box bg-yellow">
      <span class="info-box-icon"><i class="fa fa-money"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">FONDO DE AHORRO ACUMULADO</span>
        <span class="info-box-number"><?= number_format($w_fondoAcum[0]["FONDO_ACUMULADO"],2) ?></span>
        <div class="progress">
          <div class="progress-bar" style="width: 70%"></div>
        </div>
        <span class="progress-description" title="<?=$fecha?>">Fecha: 01/01/<?=substr($fecha,6, 4)?> AL <?=substr($fecha,11, 10)?></span>
      </div>
    </div>

    </div><!-- /.col-md-3 -->

    </div><!-- /.row -->
  </section>
  <!-- ############################ /.SECCION GRAFICA ############################# -->


<?php if ( isset($_GET["fecha"]) || isset($_GET["plaza"]) || isset($_GET["tipo"]) || isset($_GET["status"]) || isset($_GET["contrato"]) || isset($_GET["depto"]) || isset($_GET["area"]) ){ ?>
  <!-- ############################ TABLA DETALLE DE NOMINA PAGADA ############################# -->
  <section>
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-table"></i> DETALLES DE NOMINA PAGADA</h3>
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
                <th class="small" bgcolor="#383F6D"><font color="white">ID PLAZA</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">SUELDO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">RETROACTIVO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">GRATIFICACION UNICA</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">TIEMPO EXTRA</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">642</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">AGUINALDO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">PRIMA ANTIGUEDAD</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">COMISION</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">INDENMIZACION</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">VACACIONES</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">PRIMA VACACIONAL</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">PTU</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">TOTAL REMUNERACION</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">VALES</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">F.A. PATRON</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">SAR</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">C Y V</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">IMSS</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">INFONAVIT</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">3% DE ISN/MES</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">FOMENTO A LA EDUCACION</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">PROVISIONES</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">TOTAL PRESTACIONES</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">TOTAL</font></th>
              </tr>
            </thead>
            <tbody>
              <?php
                $tablaNomina2 = $modelNomina->table_Funcion($fecha,40,1000); // AQUI VA EL TIPO
                for ($i=0; $i <count($tablaNomina2) ; $i++) { ?>
              <tr>
                <td class="small"><?= $tablaNomina2[$i]["V_RAZON_SOCIAL"] ?></td>
                <?php //GRATIFICACION UNICA
                              $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  #echo $n_plaza."r";
                              $table_Io = $modelNomina -> table_Funcion($fecha, $n_plaza,1);
                              if (count($table_Io) > 0 ) { ?>
                                    <td class="small">$<?php $sueldo = $table_Io[0]["TOTAL"];
                                            echo number_format($sueldo, 2);
                                  ?></td>
                      <?php  }else { ?>
                <td class="small">$<?php $sueldo = 0;
                                echo number_format($sueldo, 2);
                ?></td>
                <?php  } ?>



                <?php //GRATIFICACION UNICA
                                    $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                                    #echo $n_plaza."r";
                                    $table_32 = $modelNomina -> table_Funcion($fecha, $n_plaza,25);
                                    if (count($table_32) > 0 ) { ?>
                                        <td class="small">$<?php $retroactivo = $table_32[0]["TOTAL"];
                                                              echo number_format($retroactivo, 2);
                                                          ?></td>
                <?php  }else { ?>
                                        <td class="small">$<?php $retroactivo = 0;
                                                              echo number_format($retroactivo, 2);
                                                          ?></td>
                <?php  } ?>

                <?php //GRATIFICACION UNICA
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  #echo $n_plaza."r";
                  $table_2 = $modelNomina -> table_Funcion($fecha, $n_plaza,8);
                  if (count($table_2) > 0 ) { ?>
                      <td class="small">$<?php $grat_unica = $table_2[0]["TOTAL"];
                                            echo number_format($grat_unica, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $grat_unica = 0;
                                            echo number_format($grat_unica, 2);
                                        ?></td>
                <?php  } ?>
                <?php //TIEMPO EXTRA
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_3 = $modelNomina -> table_Funcion($fecha, $n_plaza,12);
                  if (count($table_3) > 0 ) { ?>
                      <td class="small">$<?php $tiempo_extra = $table_3[0]["TOTAL"];
                                            echo number_format($tiempo_extra, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $tiempo_extra = 0;
                                            echo number_format($tiempo_extra, 2);
                                        ?></td>
                <?php  } ?>
                <?php // 642
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_4 = $modelNomina -> table_Funcion($fecha, $n_plaza,23);
                  if (count($table_4) > 0 ) { ?>
                      <td class="small">$<?php $seis42 = $table_4[0]["TOTAL"];
                                              echo number_format($seis42, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $seis42 = 0;
                                              echo number_format($seis42, 2);
                                        ?></td>
                <?php  } ?>
                <?php //AGUINALDO
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_5 = $modelNomina -> table_Funcion($fecha, $n_plaza,24);
                  if (count($table_5) > 0 ) { ?>
                      <td class="small">$<?php $aguinaldo = $table_5[0]["TOTAL"];
                                            echo number_format($aguinaldo, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $aguinaldo = 0;
                                              echo number_format($aguinaldo, 2);
                                        ?></td>
                <?php  } ?>
                <?php //prima antiguedad
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_6 = $modelNomina -> table_Funcion($fecha, $n_plaza,17);
                  if (count($table_6) > 0 ) { ?>
                      <td class="small">$<?php $prima_antiguedad = $table_6[0]["TOTAL"];
                                            echo number_format($prima_antiguedad, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $prima_antiguedad = 0;
                                            echo number_format($prima_antiguedad, 2);
                                        ?></td>
                <?php  } ?>
                <?php //comision
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_7 = $modelNomina -> table_Funcion($fecha, $n_plaza,22);
                  if (count($table_7) > 0 ) { ?>
                      <td class="small">$<?php $comision =  $table_7[0]["TOTAL"];
                                            echo number_format($comision, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $comision =  0;
                                            echo number_format($comision, 2);
                                        ?></td>
                <?php  } ?>
                <?php //indemnizacion
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_8 = $modelNomina -> table_Funcion($fecha, $n_plaza,18);
                  if (count($table_8) > 0 ) { ?>
                      <td class="small">$<?php $indemnizacion =  $table_8[0]["TOTAL"];
                                            echo number_format($indemnizacion, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $indemnizacion =  0;
                                            echo number_format($indemnizacion, 2);
                                        ?></td>
                <?php  } ?>
                <?php
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_9 = $modelNomina -> table_Funcion($fecha, $n_plaza,10);
                  if (count($table_9) > 0 ) { ?>
                      <td class="small">$<?php $vacaciones = $table_9[0]["TOTAL"];
                                            echo number_format($vacaciones, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $vacaciones = 0;
                                            echo number_format($vacaciones, 2);
                                        ?></td>
                <?php  } ?>
                <?php
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_10 = $modelNomina -> table_Funcion($fecha, $n_plaza,11);
                  if (count($table_10) > 0 ) { ?>
                      <td class="small">$<?php $prima_vacacional = $table_10[0]["TOTAL"];
                                            echo number_format($prima_vacacional);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $prima_vacacional = 0;
                                              echo number_format($prima_vacacional, 2);
                                        ?></td>
                <?php  } ?>

                <?php
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_ptu = $modelNomina -> table_Funcion($fecha, $n_plaza,9);
                  if (count($table_ptu) > 0 ) { ?>
                      <td class="small">$<?php $ptu = $table_ptu[0]["TOTAL"];
                                            echo number_format($ptu);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $ptu = 0;
                                              echo number_format($ptu, 2);
                                        ?></td>
                <?php  } ?>

                <td class="small">$<?php $total_remunera = $sueldo +
                                                          $retroactivo +
                                                          $grat_unica +
                                                          $tiempo_extra +
                                                          $seis42 +
                                                          $aguinaldo +
                                                          $prima_antiguedad +
                                                          $comision +
                                                          $indemnizacion +
                                                          $vacaciones +
                                                          $prima_vacacional+
                                                          $ptu;
                                          echo number_format($total_remunera, 2);?></td>

                                          <?php
                                            $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                                            $table_33 = $modelNomina -> table_Funcion($fecha, $n_plaza,2);
                                            if (count($table_33) > 0 ) { ?>
                                                <td class="small">$<?php $vales =  $table_33[0]["TOTAL"];
                                                                      echo number_format($vales, 2);
                                                                  ?></td>
                                          <?php  }else { ?>
                                                <td class="small">$<?php $vales = 0;
                                                                      echo number_format($vales, 2);
                                                                  ?></td>
                                          <?php  } ?>

                                          <?php
                                            $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                                            $table_34 = $modelNomina -> table_Funcion($fecha, $n_plaza,33);
                                            if (count($table_34) > 0 ) { ?>
                                                <td class="small">$<?php $fapatron =  $table_34[0]["TOTAL"];
                                                                      echo number_format($fapatron, 2);
                                                                  ?></td>
                                          <?php  }else { ?>
                                                <td class="small">$<?php $fapatron = 0;
                                                                      echo number_format($fapatron, 2);
                                                                  ?></td>
                                          <?php  } ?>



                <?php
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_11 = $modelNomina -> table_Funcion($fecha, $n_plaza,32);
                  if (count($table_11) > 0 ) { ?>
                      <td class="small">$<?php $sar =  $table_11[0]["TOTAL"];
                                            echo number_format($sar, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $sar = 0;
                                            echo number_format($sar, 2);
                                        ?></td>
                <?php  } ?>
                <?php
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_12 = $modelNomina -> table_Funcion($fecha, $n_plaza,26);
                  if (count($table_12) > 0 ) { ?>
                      <td class="small">$<?php $cyb =  $table_12[0]["TOTAL"];
                                          echo number_format($cyb, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $cyb =  0;
                                          echo number_format($cyb, 2);
                                        ?></td>
                <?php  } ?>
                <?php
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_13 = $modelNomina -> table_Funcion($fecha, $n_plaza,27);
                  if (count($table_13) > 0 ) { ?>
                      <td class="small">$<?php $imss = $table_13[0]["TOTAL"];
                                        echo number_format($imss, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $imss = 0;
                                        echo number_format($imss, 2);?></td>
                <?php  } ?><?php
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_14 = $modelNomina -> table_Funcion($fecha, $n_plaza,28);
                  if (count($table_14) > 0 ) { ?>
                      <td class="small">$<?php $infonavit = $table_14[0]["TOTAL"];
                                        echo number_format($infonavit, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $infonavit = 0;
                                        echo number_format($infonavit, 2);
                                        ?></td>
                <?php  } ?>
                <?php
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_15 = $modelNomina -> table_Funcion($fecha, $n_plaza,29);
                  if (count($table_15) > 0 ) { ?>
                      <td class="small">$<?php $isn = $table_15[0]["TOTAL"];
                                        echo number_format($isn, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $isn = 0;
                                        echo number_format($isn, 2);
                                        ?></td>
                <?php  } ?>
                <?php
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_16 = $modelNomina -> table_Funcion($fecha, $n_plaza,30);
                  if (count($table_16) > 0 ) { ?>
                      <td class="small">$<?php $fomento_educacion = $table_16[0]["TOTAL"];
                                        echo number_format($fomento_educacion, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $fomento_educacion = 0;
                                        echo number_format($fomento_educacion, 2);
                                        ?></td>
                <?php  } ?>
                <?php
                  $n_plaza = $tablaNomina2[$i]["IID_PLAZA"];
                  $table_17 = $modelNomina -> table_Funcion($fecha, $n_plaza,31);
                  if (count($table_17) > 0 ) { ?>
                      <td class="small">$<?php $proviciones = $table_17[0]["TOTAL"];
                                        echo number_format($proviciones, 2);
                                        ?></td>
                <?php  }else { ?>
                      <td class="small">$<?php $proviciones = 0;
                                        echo number_format($proviciones, 2);?></td>
                <?php  } ?>
                <td class="small">$<?php $total_prestaciones =  $sar + $cyb + $imss + $infonavit + $isn + $fomento_educacion + $proviciones + $vales + $fapatron;
                                        echo number_format($total_prestaciones, 2);
                                  ?></td>
                <td class="small">$<?= $total_bruto = number_format($sueldo + $retroactivo + $grat_unica + $tiempo_extra + $prima_antiguedad + $comision + $indemnizacion + $vacaciones + $prima_vacacional + $seis42 + $aguinaldo + $sar + $cyb + $imss + $infonavit + $isn + $fomento_educacion + $proviciones + $vales + $fapatron + $ptu, 2);  ?></td>
              </tr>
              <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <th style="text-align:right">Total</th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
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
  <!-- ############################ /.TABLA DETALLE DE NOMINA PAGADA ############################# -->
<?php } ?>



<?php if ( isset($_GET["fecha"]) || isset($_GET["plaza"]) || isset($_GET["tipo"]) || isset($_GET["status"]) || isset($_GET["contrato"]) || isset($_GET["depto"]) || isset($_GET["area"]) ){ ?>
  <!-- ############################ TABLA DETALLE DE NOMINA PAGADA ############################# -->
  <section>
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-table"></i> DETALLES DE NOMINA PAGADA</h3>
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
                <th class="small" bgcolor="#383F6D"><font color="white">CONCEPTO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">ENERO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">FEBRERO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">MARZO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">ABRIL</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">MAYO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">JUNIO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">JULIO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">AGOSTO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">SEPTIEMBRE</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">OCTUBRE</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">NOVIEMBRE</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">DICIEMBRE</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">TOTAL</font></th>
              </tr>
            </thead>
            <tbody>
                          <tr>
                                <td class="small">SUELDO</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,1);
                                    $total = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                        $total += $tablaNominaPerMonth[$i]["TOTAL"] ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">RETROACTIVO</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,25);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">GRATIFICACION UNICA </td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,8);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                        $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>


                          <tr>
                                <td class="small">TIEMPO EXTRA</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,12);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">PRIMA ANTIGUEDAD</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,17);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">COMISION</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,22);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">INDENMIZACION</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,18);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">VACACIONES</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,10);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">PRIMA VACACIONAL</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,11);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>


                          <tr>
                                <td class="small">PTU</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,9);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>


                          <tr>
                                <td class="small">642</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,23);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">AGUINALDO</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,24);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">VALES</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,2);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">F.A. PATRON</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,33);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">SAR</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,32);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">C Y V</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,26);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">IMSS</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,27);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">INFONAVIT</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,28);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2) ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">3% ISN</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,29);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">FOMENTO A LA EDUCACION</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,30);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

                          <tr>
                                <td class="small">PROVISIONES</td>
                                <?php
                                    $tablaNominaPerMonth = $modelNomina->graficaPorMes($fecha,$plaza,31);
                                    $total2 = 0;
                                    for ($i=0; $i < count($tablaNominaPerMonth); $i++) {
                                      $total2 += $tablaNominaPerMonth[$i]["TOTAL"]  ?>
                                        <td class="small">$<?= number_format($tablaNominaPerMonth[$i]["TOTAL"], 2)  ?></td>
                                <?php
                                    }
                                ?>
                                <td class="small"> <?= number_format($total2, 2) ?></td>
                          </tr>

              </tbody>
              <tfoot>
                <tr>
                  <th style="text-align:right">Total</th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
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
$(".btnNomFiltro").on("click", function(){
  fecha = $('input[name="nomFecha"]').val();
  plaza = $('#nomPlaza').val();
  tipo = $('#nomTipo').val();
  status = $('#nomStatus').val();
  contrato = $('#nomContrato').val();
  depto = $('#nomDepto').val();
  area = $('#nomArea').val();

  url = '?fecha='+fecha+'&plaza='+plaza+'&tipo='+tipo+'&status='+status+'&contrato='+contrato+'&depto='+depto+'&area='+area;
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
      "footerCallback": function(row, data, start, end, display){
        var api = this.api(), data;

        var intVal = function(i){
          return typeof i === 'string' ?
          i.replace(/[\$,]/g, '')*1 :
          typeof i === 'number' ?
          i:0;
        };

        total = api
            .column(1)
            .data()
            .reduce(function(a, b){
              return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
            }, 0);

         pageTotal = api
            .column(1, {page:'current'})
            .data()
            .reduce(function(a, b){
              return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
            }, 0);

          total2 = api
              .column(2)
              .data()
              .reduce(function(a, b){
                return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
              }, 0);

           pageTotal2 = api
              .column(2, {page:'current'})
              .data()
              .reduce(function(a, b){
                return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
              }, 0);

            total3 = api
                .column(3)
                .data()
                .reduce(function(a, b){
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                }, 0);

             pageTotal3 = api
                .column(3, {page:'current'})
                .data()
                .reduce(function(a, b){
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                }, 0);

              total4 = api
                  .column(4)
                  .data()
                  .reduce(function(a, b){
                    return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  }, 0);

               pageTotal4 = api
                  .column(4, {page:'current'})
                  .data()
                  .reduce(function(a, b){
                    return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  }, 0);

                total5 = api
                    .column(5)
                    .data()
                    .reduce(function(a, b){
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                    }, 0);

                 pageTotal5 = api
                    .column(5, {page:'current'})
                    .data()
                    .reduce(function(a, b){
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                    }, 0);

                  total6 = api
                      .column(2)
                      .data()
                      .reduce(function(a, b){
                        return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      }, 0);

                   pageTotal6 = api
                      .column(6, {page:'current'})
                      .data()
                      .reduce(function(a, b){
                        return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      }, 0);

                    total7 = api
                        .column(7)
                        .data()
                        .reduce(function(a, b){
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                        }, 0);

                     pageTotal7 = api
                        .column(7, {page:'current'})
                        .data()
                        .reduce(function(a, b){
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                        }, 0);


                      total8 = api
                          .column(8)
                          .data()
                          .reduce(function(a, b){
                            return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          }, 0);

                       pageTotal8 = api
                          .column(8, {page:'current'})
                          .data()
                          .reduce(function(a, b){
                            return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          }, 0);

                        total9 = api
                            .column(9)
                            .data()
                            .reduce(function(a, b){
                              return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                            }, 0);

                         pageTotal9 = api
                            .column(9, {page:'current'})
                            .data()
                            .reduce(function(a, b){
                              return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                            }, 0);

                          total10 = api
                              .column(10)
                              .data()
                              .reduce(function(a, b){
                                return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                              }, 0);

                           pageTotal10 = api
                              .column(10, {page:'current'})
                              .data()
                              .reduce(function(a, b){
                                return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                              }, 0);


                            total11 = api
                                .column(11)
                                .data()
                                .reduce(function(a, b){
                                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                }, 0);

                             pageTotal11 = api
                                .column(11, {page:'current'})
                                .data()
                                .reduce(function(a, b){
                                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                }, 0);


                              total12 = api
                                  .column(12)
                                  .data()
                                  .reduce(function(a, b){
                                    return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                  }, 0);

                               pageTotal12 = api
                                  .column(12, {page:'current'})
                                  .data()
                                  .reduce(function(a, b){
                                    var numero = intVal(a) + intVal(b);
                                    //return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                    return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                  }, 0);


                                total13 = api
                                    .column(13)
                                    .data()
                                    .reduce(function(a, b){
                                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                    }, 0);

                                 pageTotal13 = api
                                    .column(13, {page:'current'})
                                    .data()
                                    .reduce(function(a, b){
                                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                    }, 0);

                                  total14 = api
                                      .column(14)
                                      .data()
                                      .reduce(function(a, b){
                                        return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                      }, 0);

                                   pageTotal14 = api
                                      .column(14, {page:'current'})
                                      .data()
                                      .reduce(function(a, b){
                                        return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                      }, 0);

                                    total15 = api
                                        .column(15)
                                        .data()
                                        .reduce(function(a, b){
                                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                        }, 0);

                                     pageTotal15 = api
                                        .column(15, {page:'current'})
                                        .data()
                                        .reduce(function(a, b){
                                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                        }, 0);


                                      total16 = api
                                          .column(16)
                                          .data()
                                          .reduce(function(a, b){
                                            return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                          }, 0);

                                       pageTotal16 = api
                                          .column(16, {page:'current'})
                                          .data()
                                          .reduce(function(a, b){
                                            return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                          }, 0);

                                        total17 = api
                                            .column(17)
                                            .data()
                                            .reduce(function(a, b){
                                              return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                            }, 0);

                                         pageTotal17 = api
                                            .column(17, {page:'current'})
                                            .data()
                                            .reduce(function(a, b){
                                              return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                            }, 0);

                                          total18 = api
                                              .column(18)
                                              .data()
                                              .reduce(function(a, b){
                                                return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                              }, 0);

                                           pageTotal18 = api
                                              .column(18, {page:'current'})
                                              .data()
                                              .reduce(function(a, b){
                                                return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                              }, 0);


                                            total19 = api
                                                .column(19)
                                                .data()
                                                .reduce(function(a, b){
                                                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                }, 0);

                                             pageTotal19 = api
                                                .column(19, {page:'current'})
                                                .data()
                                                .reduce(function(a, b){
                                                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                }, 0);


                                              total20 = api
                                                  .column(20)
                                                  .data()
                                                  .reduce(function(a, b){
                                                    return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                  }, 0);

                                               pageTotal20 = api
                                                  .column(20, {page:'current'})
                                                  .data()
                                                  .reduce(function(a, b){
                                                    return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                  }, 0);

                                                total21 = api
                                                    .column(21)
                                                    .data()
                                                    .reduce(function(a, b){
                                                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                    }, 0);

                                                 pageTotal21 = api
                                                    .column(21, {page:'current'})
                                                    .data()
                                                    .reduce(function(a, b){
                                                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                    }, 0);


                                                  total22 = api
                                                      .column(22)
                                                      .data()
                                                      .reduce(function(a, b){
                                                        return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                      }, 0);

                                                   pageTotal22 = api
                                                      .column(22, {page:'current'})
                                                      .data()
                                                      .reduce(function(a, b){
                                                        return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                      }, 0);


                                                    total23 = api
                                                        .column(23)
                                                        .data()
                                                        .reduce(function(a, b){
                                                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                        }, 0);

                                                     pageTotal23 = api
                                                        .column(23, {page:'current'})
                                                        .data()
                                                        .reduce(function(a, b){
                                                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                        }, 0);

                                                      total24 = api
                                                          .column(24)
                                                          .data()
                                                          .reduce(function(a, b){
                                                            return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                          }, 0);

                                                       pageTotal24 = api
                                                          .column(24, {page:'current'})
                                                          .data()
                                                          .reduce(function(a, b){
                                                            return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                                          }, 0);


                                                        $( api.column( 1 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal, 2)
                                                        );

                                                        $( api.column( 2 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal2, 2) +''
                                                        );

                                                        $( api.column( 3 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal3, 2)
                                                        );

                                                        $( api.column( 4 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal4, 2)
                                                        );

                                                        $( api.column( 5 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal5, 2)
                                                        );

                                                        $( api.column( 6 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal6, 2)
                                                        );

                                                        $( api.column( 7 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal7, 2)
                                                        );

                                                        $( api.column( 8 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal8, 2)
                                                        );

                                                        $( api.column( 9 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal9, 2)
                                                        );

                                                        $( api.column( 10 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal10, 2)
                                                        );

                                                        $( api.column( 11 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal11, 2)
                                                        );

                                                        $( api.column( 12 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal12, 2)
                                                        );

                                                        $( api.column( 13 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal13, 2)
                                                        );

                                                        $( api.column( 14 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal14, 2)
                                                        );

                                                        $( api.column( 15 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal15, 2)
                                                        );

                                                        $( api.column( 16 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal16, 2)
                                                        );

                                                        $( api.column( 17 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal17, 2)
                                                        );

                                                        $( api.column( 18 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal18, 2)
                                                        );

                                                        $( api.column( 19 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal19, 2)
                                                        );

                                                        $( api.column( 20 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal20, 2)
                                                        );

                                                        $( api.column( 21 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal21, 2)
                                                        );

                                                        $( api.column( 22 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal22, 2)
                                                        );

                                                        $( api.column( 23 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal23, 2)
                                                        );

                                                        $( api.column( 24 ).footer() ).html(
                                                            //''+pageTotal +' ('+ total +' total)'
                                                            ''+number_format(pageTotal24, 2)
                                                        );


      },

      "ordering": false,
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
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_nomina2').DataTable( {
      "footerCallback": function(row, data, start, end, display){
        var api = this.api(), data;

        var intVal = function(i){
          return typeof i === 'string' ?
          i.replace(/[\$,]/g, '')*1 :
          typeof i === 'number' ?
          i:0;
        };

        total = api
            .column(1)
            .data()
            .reduce(function(a, b){
              return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
            }, 0);

         pageTotal = api
            .column(1, {page:'current'})
            .data()
            .reduce(function(a, b){
              return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
            }, 0);

          $(api.column(1).footer()).html(
            '$'+total
          );

          total2 = api
              .column(2)
              .data()
              .reduce(function(a, b){
                return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
              }, 0);

           pageTotal2 = api
              .column(2, {page:'current'})
              .data()
              .reduce(function(a, b){
                return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
              }, 0);

            $(api.column(2).footer()).html(
              '$'+total2
            );

            total3 = api
                .column(3)
                .data()
                .reduce(function(a, b){
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                }, 0);

             pageTotal3 = api
                .column(3, {page:'current'})
                .data()
                .reduce(function(a, b){
                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                }, 0);

              $(api.column(3).footer()).html(
                '$'+total3
              );


              total4 = api
                  .column(4)
                  .data()
                  .reduce(function(a, b){
                    return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  }, 0);

               pageTotal4 = api
                  .column(4, {page:'current'})
                  .data()
                  .reduce(function(a, b){
                    return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                  }, 0);

                $(api.column(4).footer()).html(
                  '$'+total4
                );

                total5 = api
                    .column(5)
                    .data()
                    .reduce(function(a, b){
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                    }, 0);

                 pageTotal5 = api
                    .column(5, {page:'current'})
                    .data()
                    .reduce(function(a, b){
                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                    }, 0);

                  $(api.column(5).footer()).html(
                    '$'+total5
                  );

                  total6 = api
                      .column(2)
                      .data()
                      .reduce(function(a, b){
                        return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      }, 0);

                   pageTotal6 = api
                      .column(6, {page:'current'})
                      .data()
                      .reduce(function(a, b){
                        return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                      }, 0);

                    $(api.column(6).footer()).html(
                      '$'+total6
                    );

                    total7 = api
                        .column(7)
                        .data()
                        .reduce(function(a, b){
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                        }, 0);

                     pageTotal7 = api
                        .column(7, {page:'current'})
                        .data()
                        .reduce(function(a, b){
                          return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                        }, 0);

                      $(api.column(7).footer()).html(
                        '$'+total7
                      );

                      total8 = api
                          .column(8)
                          .data()
                          .reduce(function(a, b){
                            return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          }, 0);

                       pageTotal8 = api
                          .column(8, {page:'current'})
                          .data()
                          .reduce(function(a, b){
                            return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                          }, 0);

                        $(api.column(8).footer()).html(
                          '$'+total8
                        );

                        total9 = api
                            .column(9)
                            .data()
                            .reduce(function(a, b){
                              return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                            }, 0);

                         pageTotal9 = api
                            .column(9, {page:'current'})
                            .data()
                            .reduce(function(a, b){
                              return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                            }, 0);

                          $(api.column(9).footer()).html(
                            '$'+total9
                          );

                          total10 = api
                              .column(10)
                              .data()
                              .reduce(function(a, b){
                                return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                              }, 0);

                           pageTotal10 = api
                              .column(10, {page:'current'})
                              .data()
                              .reduce(function(a, b){
                                return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                              }, 0);

                            $(api.column(10).footer()).html(
                              '$'+total10
                            );

                            total11 = api
                                .column(11)
                                .data()
                                .reduce(function(a, b){
                                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                }, 0);

                             pageTotal11 = api
                                .column(11, {page:'current'})
                                .data()
                                .reduce(function(a, b){
                                  return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                }, 0);

                              $(api.column(11).footer()).html(
                                '$'+total11
                              );

                              total12 = api
                                  .column(12)
                                  .data()
                                  .reduce(function(a, b){
                                    return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                  }, 0);

                               pageTotal12 = api
                                  .column(12, {page:'current'})
                                  .data()
                                  .reduce(function(a, b){
                                    return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                  }, 0);

                                $(api.column(12).footer()).html(
                                  '$'+total12
                                );


                                total13 = api
                                    .column(13)
                                    .data()
                                    .reduce(function(a, b){
                                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                    }, 0);

                                 pageTotal12 = api
                                    .column(13, {page:'current'})
                                    .data()
                                    .reduce(function(a, b){
                                      return Intl.NumberFormat('es-MX').format(intVal(a) + intVal(b));
                                    }, 0);

                                  $(api.column(13).footer()).html(
                                    '$'+total13
                                  );
      },

      "ordering": false,
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
  "<?=$graficaNomina[$i]["PLAZA"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i <count($graficaNomina) ; $i++) {  ?>
  <?=$graficaNomina[$i]["TOTAL"]?>,

  <?php }  ?>
  ];

  $('#graficaNom').highcharts({
    chart: { type: 'column' },
    title: { text: 'NOMINA PAGADA' },
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
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>';
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

<!--grafica por mes -->
<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php for ($i=0; $i <count($graficaNominaMes) ; $i++) {  ?>
  "<?=$graficaNominaMes[$i]["MES"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i <count($graficaNominaMes) ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["TOTAL"]?>,
  <?php }  ?>
  ];

  $('#graficaNomMes').highcharts({
    chart: { type: 'line' },
    title: { text: 'NOMINA MENSUAL PAGADA' },
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
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>';
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
