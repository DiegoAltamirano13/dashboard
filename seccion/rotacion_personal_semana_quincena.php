<?php
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:rotacion_personal.php");
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

include_once '../class/Rotacion_personal_Semanal_Quincenal.php';
$obj_class = new RotacionPersonal();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 19);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////

/* $_GET FECHA */
include '../class/Nomina_pagada.php';
$modelNomina = new NominaPagada();
$fec_corte = $modelNomina->sql(1,null);
/*----- GET FECHA -----*/
$fecha = $fec_corte[0]["MES1"]."-".$fec_corte[0]["MES2"];

if ( isset($_GET["fecha"]) ){
  if ( $obj_class->validateDate(substr($_GET["fecha"],0,10)) AND $obj_class->validateDate(substr($_GET["fecha"],11,10)) ){
    $fecha = $_GET["fecha"];
  }else{
    $fecha = $fec_corte[0]["MES1"]."-".$fec_corte[0]["MES2"];
  }
}
/* $_GET FIL_CHECK */
$fil_check = "on";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}


$fil_habilitado = "ALL";
if (isset($_GET["fil_habilitado"])) {
  $fil_habilitado = $_GET["fil_habilitado"];
}
/* $_GET PLAZA */
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
/* $_GET CONTRATO */
$contrato = "ALL";
if ( isset($_GET["contrato"]) ){
  switch ($_GET["contrato"]) {
    case '0': $contrato = $_GET["contrato"]; break;
    case '1': $contrato = $_GET["contrato"]; break;
    case '3': $contrato = $_GET["contrato"]; break;
    case '2': $contrato = $_GET["contrato"]; break;
    default: $contrato = "ALL"; break;
  }
}
/* $_GET DEPARTAMENTO */
$departamento = "ALL";
if ( isset($_GET["depto"]) ){
  $select_depto = $obj_class->filtros(2,$departamento);
  for ($i=0; $i <count($select_depto) ; $i++) {
    if ( $select_depto[$i]["IID_DEPTO"] == $_GET["depto"]){
      $departamento = $_GET["depto"]; break;
    }
  }
}
/*----- GET AREA -----*/
$area = "ALL";
if ( isset($_GET["area"]) ){
  if ( $departamento != 'ALL' ){
    $select_area = $obj_class->filtros(3,$departamento);
    for ($i=0; $i <count($select_area) ; $i++) { // FOR
      if ( $select_area[$i]["IID_AREA"] == $_GET["area"]){
        $area = $_GET["area"]; break;
      }
    }// /.FOR
  }
}

if($plaza != 'ALL'){
  $grafica_Paste = $obj_class->grafica_Paste($plaza,$contrato,$departamento,$area,$fil_check,$fecha,$fil_habilitado);
}
//WIDGETS
$widgets = $obj_class->widgets($plaza,$contrato,$departamento,$area,$fil_check,$fecha,$fil_habilitado);
$widgetsAnuales = $obj_class->anualesWidgets($plaza);
$empleado = $obj_class->cantidadEmpleados($fecha);
//GRAFICA
$grafica = $obj_class->grafica($plaza,$contrato,$departamento,$area,$fil_check,$fecha,$fil_habilitado, 0);
$graficaMensual = $obj_class->graficaMensual($plaza,$fecha,$fil_check,$fil_habilitado);
//TABLA DETALLE ACTIVOS
$tablaActivos = $obj_class->tablaActivos($plaza,$contrato,$departamento,$area,$fil_check,$fecha);
$tablaBaja = $obj_class->tablaBaja($plaza,$contrato,$departamento,$area,$fil_check,$fecha,$fil_habilitado);
$rotacionPorAlmacen = $obj_class->grafica_PerAlmacen($plaza,$contrato,$departamento,$area,$fil_check,$fecha,$fil_habilitado);

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
        <?php /*echo $fecha;*/ ?>
        Dashboard
        <small>Rotación de Personal(Semanal)</small>
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
        <div class="box-body"><!--box-body-->



        <?php if ($plaza == 'ALL'){ ?>
        <div class="col-md-12">
          <!--<div id="graf_perM2"></div>-->
          <div id="graf_perMSemanal"></div>
          <!--<div id="graf_bar"></div>-->
          <div id="graf_barSemanal"></div>
          <div id="graf_perM"></div>
        </div>
        <?php } ?>
        <?php if ($plaza != 'ALL'){ ?>
        <div class="row">

          <div class="col-md-8">
            <div id="grafPerAlm"></div>
          </div>

          <?php if ($plaza != 'ALL'){ ?>
          <div class="col-md-4">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Widgets</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
              </div>
              <div class="box-body"><!--box-body-->

                <!-- WIDGETS #1 -->
                <div class="info-box bg-blue">
                  <span class="info-box-icon"><i class="fa fa-users"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Personal Activo <?=$plaza?></span>
                    <span class="info-box-number"><?php echo $widgets[0]["ACTIVO"]; ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <?php
                    if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                      echo '<span class="progress-description">Fecha de consulta: '.$fecha.'</span>';
                    }else{
                      $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(SYSDATE, 'MM/DD/YYYY HH24:MI:SS') NOW FROM DUAL");
                       echo '<span class="progress-description">Fecha de consulta: '.$mesConsultaBaja[0]["NOW"].'</span>';
                    }
                    ?>
                  </div>
                </div>

                <!---->
                <div class="info-box bg-blue">
                  <span class="info-box-icon"><i class="fa fa-users"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Personal Baja No Contemplado <?=$plaza?></span>
                    <span class="info-box-number"><?php echo $widgets[0]["BAJA_NO_CONTEMPLADO"]; ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <?php
                    if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                      echo '<span class="progress-description">Fecha de consulta: '.$fecha.'</span>';
                    }else{
                      $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(SYSDATE, 'MM/DD/YYYY HH24:MI:SS') NOW FROM DUAL");
                       echo '<span class="progress-description">Fecha de consulta: '.$mesConsultaBaja[0]["NOW"].'</span>';
                    }
                    ?>
                  </div>
                </div>
                <!-- WIDGETS #2 -->
                <div class="info-box bg-maroon">
                  <span class="info-box-icon"><i class="fa fa-user-times"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Personal Baja<?=$plaza?></span>
                    <span class="info-box-number"><?php echo $widgets[0]["BAJA"]; ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <?php
                    if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                      echo '<span class="progress-description">Fecha de consulta: '.$fecha.'</span>';
                    }else{
                      $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -1), 'DD/MM/YYYY') mes2 FROM DUAL");
                       echo '<span class="progress-description">Fecha de consulta: '.$mesConsultaBaja[0]["MES1"].'-'.$mesConsultaBaja[0]["MES2"].'</span>';
                    }
                    ?>
                  </div>
                </div>

                <!-- ./WIDGETS -->
                <div class="info-box bg-maroon">
                  <span class="info-box-icon"><i class="fa fa-user-times"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Personal HABILITADO Baja<?=$plaza?></span>
                    <span class="info-box-number"><?php echo $widgets[0]["BAJA_HABILITADO"]; ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <?php
                    if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                      echo '<span class="progress-description">Fecha de consulta: '.$fecha.'</span>';
                    }else{
                      $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -1), 'DD/MM/YYYY') mes2 FROM DUAL");
                       echo '<span class="progress-description">Fecha de consulta: '.$mesConsultaBaja[0]["MES1"].'-'.$mesConsultaBaja[0]["MES2"].'</span>';
                    }
                    ?>
                  </div>
                </div>

              </div><!--/.box-body-->
            </div>
          </div>
          <?php } ?>
          <?php if ($plaza != 'ALL') { ?>
            <div id="graf_pie"></div>
          <?php } ?>
        </div>
        <?php } ?>

        </div><!--/.box-body-->
      </div>
    </div>

    <?php //if ($plaza != 'ALL'){ ?>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
          <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="rotacion_personal.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
          <?php } ?>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <!-- FILTRAR POR fecha -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
            <input type="text" class="form-control pull-right" name="fil_fecha" enabled>
            <!--<span class="input-group-addon" style="visibility: hidden"> <input type="checkbox" name="fil_check"  checked style="visibility: hidden"<?php /*if( $fil_check == 'on' ){ echo "checked";}*/ ?> > </span>-->
          </div>
          <!-- FILTRAR POR PLAZA -->
          <div class="input-group">
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
          <!-- FILTRAR POR CONTRATO -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-book"></i> Contrato:</span>
            <select class="form-control select2" style="width: 100%;" id="fil_contrato">
              <option value="ALL">ALL</option>
              <option value="0" <?php if ($contrato == "0"){ echo "selected";} ?> >DETERMINADO</option>
              <option value="1" <?php if ($contrato == "1"){ echo "selected";} ?> >TIEMPO INDETERMINADO</option>
              <option value="3" <?php if ($contrato == "3"){ echo "selected";} ?> >POR OBRA DETERMINADA</option>
              <option value="2" <?php if ($contrato == "2"){ echo "selected";} ?> >CONTRATO TERMINADO</option>
            </select>
          </div>
          <!-- FILTRAR POR DEPTO -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-folder"></i> Depto:</span>
            <select class="form-control select2" style="width: 100%;" id="fil_departamento">
              <option value="ALL" <?php if($departamento == 'ALL'){echo "selected";} ?>>ALL</option>
              <?php
              $select_depto = $obj_class->filtros(2,$departamento);
              for ($i=0; $i <count($select_depto) ; $i++) { ?>
                <option value="<?= $select_depto[$i]["IID_DEPTO"] ?>" <?php if($select_depto[$i]["IID_DEPTO"] == $departamento){echo "selected";} ?> ><?= $select_depto[$i]["V_DESCRIPCION"] ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="input-group">
            <span class="input-group-addon"> <input type="checkbox" name="fil_habilitado" <?php if( $fil_habilitado == 'on' ){ echo "checked";} ?> > PERSONAL HABILITADO</span>
          </div>
          <!-- FILTRAR POR AREA -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-folder-open"></i> Area:</span>
            <select class="form-control select2" style="width: 100%;" id="fil_area">
              <option value="ALL" <?php if($area == 'ALL'){echo 'selected';} ?> >ALL</option>
              <?php
              if ($departamento != 'ALL'){ // IF
                $select_area = $obj_class->filtros(3,$departamento);
                for ($i=0; $i <count($select_area) ; $i++) { // FOR ?>
                  <option value="<?= $select_area[$i]["IID_AREA"] ?>" <?php if($select_area[$i]["IID_AREA"] == $area){echo 'selected';} ?>><?= $select_area[$i]["V_DESCRIPCION"] ?></option>
              <?php
                } // /. FOR
              } // /.IF
              ?>
            </select>
            <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil"><i class="fa fa-check"></i> Filtrar</button> </span>
          </div>

        </div><!--/.box-body-->
      </div>
    </div>
    <?php //} ?>
<!-- WIDGETS -->
    <?php if ($plaza == 'ALL'){ ?>
    <div id="div_widgets" class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-tags"></i> Widgets</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <!-- WIDGETS #1 -->
          <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Personal Activo</span>
              <span class="info-box-number"><?php echo $widgets[0]["ACTIVO"]; ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <?php
              if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                echo '<span class="progress-description">Fecha de consulta: </br> '.$fecha.'</span>';
              }else{
                $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(SYSDATE, 'MM/DD/YYYY HH24:MI:SS') NOW FROM DUAL");
                 echo '<span class="progress-description">Fecha de consulta: </br> '.$mesConsultaBaja[0]["NOW"].'</span>';
              }
              ?>
            </div>
          </div>
          <!-- WIDGETS #2 -->
          <div class="info-box bg-maroon">
            <span class="info-box-icon"><i class="fa fa-user-times"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Persona Baja</span>
              <span class="info-box-number"><?php echo $widgets[0]["BAJA"]; ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <?php
              if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                echo '<span class="progress-description">Fecha de consulta: </br> '.$fecha.'</span>';
              }else{
                $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -1), 'DD/MM/YYYY') mes2 FROM DUAL");
                 echo '<span class="progress-description">Fecha de consulta:  </br> '.$mesConsultaBaja[0]["MES1"].'-'.$mesConsultaBaja[0]["MES2"].'</span>';
              }
              ?>
            </div>
          </div>
          <!--WIDGETS #3-->
          <div class="info-box">
            <span class="info-box-icon"><i class="fa fa-user-times"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Bajas No Contemplado <?=$plaza?></span>
              <span class="info-box-number"><?php echo $widgets[0]["BAJA_NO_CONTEMPLADO"]; ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <?php
              if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                echo '<span class="progress-description">Fecha de consulta: </br> '.$fecha.'</span>';
              }else{
                $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(SYSDATE, 'MM/DD/YYYY HH24:MI:SS') NOW FROM DUAL");
                 echo '<span class="progress-description">Fecha de consulta:  </br> '.$mesConsultaBaja[0]["NOW"].'</span>';
              }
              ?>
            </div>
          </div>
          <!---->
          <div class="info-box bg-maroon">
            <span class="info-box-icon"><i class="fa fa-user-times"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Personal Habilitado Baja</span>
              <span class="info-box-number"><?php echo $widgets[0]["BAJA_HABILITADO"]; ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <?php
              if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                echo '<span class="progress-description">Fecha de consulta: </br> '.$fecha.'</span>';
              }else{
                $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -1), 'DD/MM/YYYY') mes2 FROM DUAL");
                 echo '<span class="progress-description">Fecha de consulta: </br> '.$mesConsultaBaja[0]["MES1"].'-'.$mesConsultaBaja[0]["MES2"].'</span>';
              }
              ?>
            </div>
          </div>
          <!-- ./WIDGETS PORCENTAJE -->
          <?php if ($widgets[0]["BAJA"] <> 0 AND $widgets[0]["ACTIVO"]<>0) {?>
          <div class="info-box bg-green" style="display:none;">
            <span class="info-box-icon"><i class="fa fa-percent"></i></span>
            <div class="info-box-content">
              <span class="info-box-text"> ROTACIÓN MENSUAL</span>
              <span class="info-box-number">
                <?php
                //echo $fil_check;
                $valor_anual = 0.00;
                for ($i=0; $i < count($graficaMensual); $i++) {
                  $valor_baja = $graficaMensual[$i]["BAJA"];
                  if ($valor_baja > 0) {
                      $valor_anual =number_format($graficaMensual[$i]["BAJA"]*100/$graficaMensual[$i]["ACTIVO"], 2) + $valor_anual;
                  }
                }
                //echo $valor_anual;
                echo number_format($widgets[0]["BAJA"]*100/ $widgets[0]["ACTIVO"] , 2);
                ?>
                <!--<?php  # echo number_format(($widgetsAnuales[0]["ALTA_ANUAL"]*100)/$widgetsAnuales[0]["BAJA_ANUAL"], 2)?></span>-->
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <?php
              if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                echo '<span class="progress-description">Fecha de consulta: </br> '.$fecha.'</span>';
              }else{
                $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -1), 'DD/MM/YYYY') mes2 FROM DUAL");
                 echo '<span class="progress-description">Fecha de consulta: </br> '.$mesConsultaBaja[0]["MES1"].'-'.$mesConsultaBaja[0]["MES2"].'</span>';
              }
              ?>
            </div>
          </div>
          <?php } ?>

          <?php if ($widgets[0]["BAJA"] <> 0 AND $widgets[0]["ACTIVO"]<>0) {?>
          <div class="info-box bg-green" style="display:none">
            <span class="info-box-icon"><i class="fa fa-percent"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">ROTACIÓN ACUMULADO ANUAL</span>
              <span class="info-box-number">
                <?php
                $valoranual = 0.00;
                $mes = substr($fecha, 14, 2);

                switch ($mes) {
                  case '01':
                    $valor = 1;
                    break;
                  case '02':
                    $valor = 2;
                    break;
                  case '03':
                    $valor = 3;
                    break;
                  case '04':
                    $valor = 4;
                    break;
                  case '05':
                    $valor = 5;
                    break;
                  case '06':
                    $valor = 6;
                    break;
                  case '07':
                    $valor = 7;
                    break;
                  case '08':
                    $valor = 8;
                    break;
                  case '09':
                    $valor = 9;
                    break;
                  case '10':
                    $valor = 10;
                    break;
                  case '11':
                    $valor = 11;
                    break;
                  case '12':
                    $valor = 12;
                    break;
                  default:
                    $valor = 6;
                    break;
                }
                for ($i=0; $i < $valor ; $i++) {
                  $valorBa = $graficaMensual[$i]["BAJA"];
                  if ($valorBa > 0) {

                    $valoranual = number_format($graficaMensual[$i]["BAJA"]*100/$graficaMensual[$i]["ACTIVO"], 2) + $valoranual;
                    //$valoranual2 = number_format($graficaMensual[$i]["BAJA"]*100/$graficaMensual[$i]["ACTIVO"], 2);
                    //echo $valoranual2. "   ".$graficaMensual[$i]["BAJA"]."<BR>";
                  }
                }
                echo $valoranual;
                ?>
              </span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <?php
              if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
                echo '<span class="progress-description">Fecha de consulta: </br> '.$fecha.'</span>';
              }else{
                $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -1), 'DD/MM/YYYY') mes2 FROM DUAL");
                 echo '<span class="progress-description">Fecha de consulta:  </br> '.$mesConsultaBaja[0]["MES1"].'-'.$mesConsultaBaja[0]["MES2"].'</span>';
              }
              ?>
            </div>
          </div>
          <?php } ?>

        <!--WIDGETS ANUALES -->
        <?php if ($widgetsAnuales[0]["BAJA_ANUAL"] <> 0 AND $widgetsAnuales[0]["ALTA_ANUAL"]<>0) {?>
        <div class="info-box bg-maroon">
          <span class="info-box-icon"><i class="fa fa-user-times"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">BAJAS ACUMULADAS ANUALES</span>
            <span class="info-box-number">
              <?php
              $valor_anual = 0.00;
              $mes = substr($fecha,14,2);
              #echo $mes;
              switch ($mes) {
                case '01':
                  $valor = 1;
                  break;

                case '02':
                  $valor = 2;
                  break;
                case '03':
                  $valor = 3;
                  break;
                case '04':
                  $valor = 4;
                  break;
                case '05':
                    $valor = 5;
                    break;
                case '06':
                  $valor = 6;
                  break;
                case '07':
                  $valor = 7;
                  break;
                case '08':
                  $valor = 8;
                  break;
                case '09':
                  $valor = 9;
                  break;
                case '10':
                  $valor = 10;
                  break;
                case '11':
                  $valor = 11;
                  break;
                case '12':
                  $valor = 12;
                  break;
                default:
                  $valor = 6;
                  break;
              }
              for ($i=0; $i < $valor; $i++) {
                $valor_baja = $graficaMensual[$i]["BAJA"];
                if ($valor_baja > 0) {
                    $valor_anual =number_format($graficaMensual[$i]["BAJA"], 2) + $valor_anual;
                }
              }
              echo $valor_anual;?>
              <!--<?php  # echo number_format(($widgetsAnuales[0]["ALTA_ANUAL"]*100)/$widgetsAnuales[0]["BAJA_ANUAL"], 2)?></span>-->
            <div class="progress">
              <div class="progress-bar" style="width: 70%"></div>
            </div>
            <?php
            if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){
              echo '<span class="progress-description">Fecha de consulta: '.$fecha.'</span>';
            }else{
              $mesConsultaBaja = $obj_class->dual( "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -1), 'DD/MM/YYYY') mes2 FROM DUAL");
               echo '<span class="progress-description">Fecha de consulta: '.$mesConsultaBaja[0]["MES1"].'-'.$mesConsultaBaja[0]["MES2"].'</span>';
            }
            ?>
          </div>
        </div>
        <?php } ?>



        </div><!--/.box-body-->
      </div>
    </div>
    <?php } ?>

  </div>
</section>
<!-- ############################ ./SECCION GRAFICA Y WIDGETS ############################# -->


<?php if ($plaza != 'ALL'){ ?>
<!-- ############################ INICIA SECCION TABLA DETALLE ############################# -->
<section>
  <div class="box box-success">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-list-alt"></i> DETALLE ROTACIÓN DE PERSONAL <?=$plaza?></h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>

    <div class="box-body"><!--box-body-->

      <div class="nav-tabs-custom">

        <ul class="nav nav-pills" id="myTab">
          <li class="active"><a href="#tab_activo" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL ACTIVO
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo $widgets[0]["ACTIVO"]; ?></span></a></li>
          <?php if($fil_habilitado <>  'on'){ ?>
          <li><a href="#tab_baja" data-toggle="tab"><i class="fa fa-user-times"></i> PERSONAL DE BAJA
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo $widgets[0]["BAJA"]; ?></span></a>
          </li>
        <?php } else{?>
          <li><a href="#tab_baja" data-toggle="tab"><i class="fa fa-user-times"></i> PERSONAL DE BAJA
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo $widgets[0]["BAJA_HABILITADO"]; ?></span></a>
          </li>
        <?php } ?>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_activo">

          <div class="table-responsive">
            <table id="tabla_activo" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PLAZA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">LUGAR DE TRABAJO</font></th>
                <!--  <th class="small" bgcolor="#0073B7"><font color="white">GENERO</font></th>-->
                  <th class="small" bgcolor="#0073B7"><font color="white">NSS</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">RFC</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CURP</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">INGRESO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">ANTIGUEDAD</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CONTRATO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PUESTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DEPTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaActivos) ; $i++) { ?>
                <tr>
                  <td><?= $tablaActivos[$i]["IID_EMPLEADO"] ?></td>
                  <td><?= $tablaActivos[$i]["NOMBRE"] ?></td>
                  <td><?= $tablaActivos[$i]["PLAZA"] ?></td>
                  <td><?= $tablaActivos[$i]["LUGAR_TRABAJO"] ?></td>
                <!--  <td>
                    <?php switch ($tablaActivos[$i]["V_SEXO"]) {
                    case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                    case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                    default: echo "NO REGISTRADO"; break;
                  } ?>-->
                  </td>
                  <td><?= $tablaActivos[$i]["V_IMSS"] ?></td>
                  <td><?= $tablaActivos[$i]["V_RFC"] ?></td>
                  <td><?= $tablaActivos[$i]["V_CURP"] ?></td>
                  <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos[$i]["D_FECHA_INGRESO"] ?></span></td>
                  <td><?= $tablaActivos[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <td>
                    <?php switch ($tablaActivos[$i]["S_TIPO_CONTRATO"]) {
                    case 0: echo "DETERMINADO"; break;
                    case 1: echo "TIEMPO INDETERMINADO"; break;
                    case 3: echo "POR OBRA DETERMINADA"; break;
                    default: echo "INDEFINIDO"; break;
                    } ?>
                  </td>
                  <td><?= $tablaActivos[$i]["PUESTO"] ?></td>
                  <td><?= $tablaActivos[$i]["DEPTO"] ?></td>
                  <td><?= $tablaActivos[$i]["AREA"] ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="tab_baja">

            <div class="table-responsive">
            <table id="tabla_baja" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#AD164D"><font color="white">ID</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">EMPLEADO</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">PLAZA</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">LUGAR DE TRABAJO</font></th>
                  <!--<th class="small" bgcolor="#AD164D"><font color="white">GENERO</font></th>-->
                  <th class="small" bgcolor="#AD164D"><font color="white">NSS</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">RFC</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">CURP</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">INGRESO</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">CANCELACIÓN</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">ANTIGUEDAD</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">CONTRATO</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">PUESTO</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">DEPTO</font></th>
                  <th class="small" bgcolor="#AD164D"><font color="white">AREA</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaBaja) ; $i++) { ?>
                <tr>
                  <td><?= $tablaBaja[$i]["IID_EMPLEADO"] ?></td>
                  <td><?= $tablaBaja[$i]["NOMBRE"] ?></td>
                  <td><?= $tablaBaja[$i]["PLAZA"] ?></td>
                  <td><?= $tablaBaja[$i]["LUGAR_TRABAJO"] ?></td>
                <!--  <td>
                    <?php switch ($tablaBaja[$i]["V_SEXO"]) {
                    case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                    case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                    default: echo "NO REGISTRADO"; break;
                    } ?>
                  </td>-->
                  <td><?= $tablaBaja[$i]["V_IMSS"] ?></td>
                  <td><?= $tablaBaja[$i]["V_RFC"] ?></td>
                  <td><?= $tablaBaja[$i]["V_CURP"] ?></td>
                  <td><span class="badge bg-red"><i class="fa fa-calendar-check-o"></i> <?= $tablaBaja[$i]["INGRESO"] ?></span></td>
                  <td><span class="badge bg-red"><i class="fa fa-calendar-check-o"></i> <?= $tablaBaja[$i]["FECHA_CANCELACION"] ?></span></td>
                  <td><?= $tablaBaja[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <td>
                    <?php switch ($tablaBaja[$i]["S_TIPO_CONTRATO"]) {
                    case 0: echo "DETERMINADO"; break;
                    case 1: echo "TIEMPO INDETERMINADO"; break;
                    case 3: echo "POR OBRA DETERMINADA"; break;
                    case 2: echo "CONTRATO TERMINADO"; break;
                    default: echo "INDEFINIDO"; break;
                    } ?>
                  </td>
                  <td><?= $tablaBaja[$i]["PUESTO"] ?></td>
                  <td><?= $tablaBaja[$i]["DEPTO"] ?></td>
                  <td><?= $tablaBaja[$i]["AREA"] ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>

          </div>

          </div>
          <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
      </div>

    </div><!--/.box-body-->
  </div>
</section>
<?php for ($i=0; $i < COUNT($rotacionPorAlmacen); $i++) {
  //echo $rotacionPorAlmacen[$i]['ALMACEN'];
} ?>
<!-- ########################### TERMINA SECCION TABLA DETALLE ########################### -->
<?php } ?>




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
  $('input[name="fil_fecha"]').attr("disabled", false);
<?php
if ( $fil_check == 'ALL' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){ ?>
  $('input[name="fil_fecha"]').attr("disabled", false);
<?php } ?>
$('input[name="fil_check"]').on("click", function (){

  if ($('input[name="fil_check"]').is(':checked')) {
    $('input[name="fil_fecha"]').attr("disabled", false);
  }else{
    $('input[name="fil_fecha"]').attr("disabled", false);
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
  fil_contrato = $('#fil_contrato').val();
  fil_departamento = $('#fil_departamento').val();
  fil_area = $('#fil_area').val();
  fil_check = 'on';

  //Fill habilitados
  fil_habilitado = 'off';




  url = '?plaza='+fil_plaza+'&check='+fil_check+'&contrato='+fil_contrato+'&depto='+fil_departamento+'&area='+fil_area+'&fil_habilitado='+fil_habilitado;
  if (fil_check = 'on') {
    //alert(fil_check);
    fil_check = 'on';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&contrato='+fil_contrato+'&depto='+fil_departamento+'&area='+fil_area+'&fecha='+fil_fecha+'&fil_habilitado='+fil_habilitado;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&contrato='+fil_contrato+'&depto='+fil_departamento+'&area='+fil_area+'&fecha='+fil_fecha+'&fil_habilitado='+fil_habilitado;
    }

  }else{
    fil_check = 'off';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
        fil_habilitado = 'on';
        url = '?plaza='+fil_plaza+'&check='+fil_check+'&contrato='+fil_contrato+'&depto='+fil_departamento+'&area='+fil_area+'&fecha='+fil_fecha+'&fil_habilitado='+fil_habilitado;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&contrato='+fil_contrato+'&depto='+fil_departamento+'&area='+fil_area+'&fecha='+fil_fecha+'&fil_habilitado='+fil_habilitado;
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
    $graficaQuincenal = $obj_class->grafica($plaza,$contrato,$departamento,$area,$fil_check,$fecha,$fil_habilitado, 1);
     for ($i=0; $i <count($graficaQuincenal) ; $i++) {

       if ($graficaQuincenal[$i]["BAJA"]> 0) {
          $valor_calculo = number_format(($graficaQuincenal[$i]["BAJA"]/$graficaQuincenal[$i]["ACTIVO"]) * 100, 2);
          echo "'".$graficaQuincenal[$i]["PLAZA"]. "  ".$valor_calculo ." %',";
       }else {
          echo "'".$graficaQuincenal[$i]["PLAZA"]." 0.00 %',";
       }

     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaQuincenal) ; $i++) {
      echo $graficaQuincenal[$i]["ACTIVO"].",";
    }
    ?>
    ];
    var data2 = [
    <?php
    for ($i=0; $i <count($graficaQuincenal) ; $i++) {
      echo $graficaQuincenal[$i]["BAJA"].",";
    }
    ?>
    ];
    var data3 = [
    <?php
    for ($i=0; $i <count($graficaQuincenal) ; $i++) {
      echo $graficaQuincenal[$i]["ACTIVON"].",";
    }
    ?>
    ];

    $('#graf_bar').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'ROTACIÓN DE PERSONAL (QUINCENAL)'
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
                text: 'Personal'
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
          //valueDecimals: 2,
          //valuePrefix: '$',
          //valueSuffix: ' USD'
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
        colors: ['#0073B7', '#D81B60', '#008000'],
        plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: true
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
              var plaza = this.value;
              var separador = " ";
              var limite = 1;
              var plazaReal = plaza.split(separador, limite);

              console.log("lA PLAZA REAL "+plazaReal);
              url = '?plaza='+this.value+'&check=on';
              url = '?plaza='+plazaReal+'&check=on&contrato=<?=$contrato?>&depto=<?=$departamento?>&area=<?=$area?>&fecha=<?=$fecha?>';
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
        series:  [{
          showInLegend:false,
            name: 'Personal Activo',
            data: data1,
        }, {
          showInLegend:false,
            name: 'Personal de Baja',
            data: data2,
        },{
          showInLegend:false,
            name: 'Personal Nuevo',
            data: data3,
        }]

    });
});
<?php if ($plaza != 'ALL'){ ?>
  Highcharts.setOptions({
    lang:{
      thousandsSep: ','
    }
  });
  var categories = [
    <?php
    for ($i=0; $i < count($grafica_Paste) ; $i++) {
      echo "'".$grafica_Paste[$i]["MES"]."',";
    }
    ?>
  ];
  var data1 = [
    <?php
    for ($i=0; $i < count($grafica_Paste) ; $i++) {

      $mes_Comparar = substr($fecha, 14, 2);
      if ($i < $mes_Comparar){
        echo $grafica_Paste[$i]["ACTIVO"].",";
      }
      else {
        echo "0 ,";
      }
    }
    ?>
  ];
  var data2 = [
    <?php
    for ($i=0; $i < count($grafica_Paste) ; $i++) {
      $mes_Comparar = substr($fecha, 14, 2);
      if ($i < $mes_Comparar){
        echo $grafica_Paste[$i]["BAJA"].",";
      }else{
        echo "0 ,";
      }
    }
    ?>
  ];
  $('#graf_pie').highcharts({
    chart:{
      type: 'column'
    },
    title:{
      text: 'ROTACIÓN DE PERSONAL POR AÑO '
    },

    legend:{
      y:-40,
      borderWidth:1,
      backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
      lineWidth: 2,
      min: 0,
      offset: 10,
      tickWidth: 1,
      title: {
          text: 'Personal'
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
      //valueDecimals: 2,
      //valuePrefix: '$',
      //valueSuffix: ' USD'
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
          enabled: true
        },
        enableMouseTracking:false
      }
    },
    xAxis:{
      categories: categories,
      labels:{
        formatter:function(){
          url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
          url = '?plaza='+this.value+'&check=<?=$fil_check?>&contrato=<?=$contrato?>&depto=<?=$departamento?>&area=<?=$area?>&fecha=<?=$fecha?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle:{
      text: '',
      align: 'right',
      x:-10,
    },
    series:[{
      showInLegend:false,
      name:'Personal Activo',
      data:data1,
    },{
      showInLegend:false,
      name: 'Personal de baja',
      data:data2,
    }]
  });
<?php } ?>
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
     $graficaSemanal = $obj_class->grafica($plaza,$contrato,$departamento,$area,$fil_check,$fecha,$fil_habilitado, 2);
     for ($i=0; $i <count($graficaSemanal) ; $i++) {

       if ($graficaSemanal[$i]["BAJA"]> 0) {
          $valor_calculo = number_format(($graficaSemanal[$i]["BAJA"]/$graficaSemanal[$i]["ACTIVO"]) * 100, 2);
          echo "'".$graficaSemanal[$i]["PLAZA"]. "  ".$valor_calculo ." %',";
       }else {
          echo "'".$graficaSemanal[$i]["PLAZA"]." 0.00 %',";
       }

     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaSemanal) ; $i++) {
      echo $graficaSemanal[$i]["ACTIVO"].",";
    }
    ?>
    ];
    var data2 = [
    <?php
    for ($i=0; $i <count($graficaSemanal) ; $i++) {
      echo $graficaSemanal[$i]["BAJA"].",";
    }
    ?>
    ];
    var data3 = [
    <?php
    for ($i=0; $i <count($graficaSemanal) ; $i++) {
      echo $graficaSemanal[$i]["ACTIVON"].",";
    }
    ?>
    ];

    $('#graf_barSemanal').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'ROTACIÓN DE PERSONAL (SEMANAL)'
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
                text: 'Personal'
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
          //valueDecimals: 2,
          //valuePrefix: '$',
          //valueSuffix: ' USD'
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
        colors: ['#0073B7', '#D81B60', '#008000'],
        plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: true
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
              var plaza = this.value;
              var separador = " ";
              var limite = 1;
              var plazaReal = plaza.split(separador, limite);

              console.log("lA PLAZA REAL "+plazaReal);
              url = '?plaza='+this.value+'&check=on';
              url = '?plaza='+plazaReal+'&check=on&contrato=<?=$contrato?>&depto=<?=$departamento?>&area=<?=$area?>&fecha=<?=$fecha?>';
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
        series:  [{
          showInLegend:false,
            name: 'Personal Activo',
            data: data1,
        }, {
          showInLegend:false,
            name: 'Personal de Baja',
            data: data2,
        },{
          showInLegend:false,
            name: 'Personal Nuevo',
            data: data3,
        }]

    });
});
<?php if ($plaza != 'ALL'){ ?>
  Highcharts.setOptions({
    lang:{
      thousandsSep: ','
    }
  });
  var categories = [
    <?php
    for ($i=0; $i < count($grafica_Paste) ; $i++) {
      echo "'".$grafica_Paste[$i]["MES"]."',";
    }
    ?>
  ];
  var data1 = [
    <?php
    for ($i=0; $i < count($grafica_Paste) ; $i++) {

      $mes_Comparar = substr($fecha, 14, 2);
      if ($i < $mes_Comparar){
        echo $grafica_Paste[$i]["ACTIVO"].",";
      }
      else {
        echo "0 ,";
      }
    }
    ?>
  ];
  var data2 = [
    <?php
    for ($i=0; $i < count($grafica_Paste) ; $i++) {
      $mes_Comparar = substr($fecha, 14, 2);
      if ($i < $mes_Comparar){
        echo $grafica_Paste[$i]["BAJA"].",";
      }else{
        echo "0 ,";
      }
    }
    ?>
  ];
  $('#graf_pie').highcharts({
    chart:{
      type: 'column'
    },
    title:{
      text: 'ROTACIÓN DE PERSONAL POR AÑO '
    },

    legend:{
      y:-40,
      borderWidth:1,
      backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
      lineWidth: 2,
      min: 0,
      offset: 10,
      tickWidth: 1,
      title: {
          text: 'Personal'
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
      //valueDecimals: 2,
      //valuePrefix: '$',
      //valueSuffix: ' USD'
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
          enabled: true
        },
        enableMouseTracking:false
      }
    },
    xAxis:{
      categories: categories,
      labels:{
        formatter:function(){
          url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
          url = '?plaza='+this.value+'&check=<?=$fil_check?>&contrato=<?=$contrato?>&depto=<?=$departamento?>&area=<?=$area?>&fecha=<?=$fecha?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle:{
      text: '',
      align: 'right',
      x:-10,
    },
    series:[{
      showInLegend:false,
      name:'Personal Activo',
      data:data1,
    },{
      showInLegend:false,
      name: 'Personal de baja',
      data:data2,
    }]
  });
<?php } ?>
</script>

<script type="text/javascript">
  <?php if ($plaza != 'ALL' ) {?>

      Highcharts.setOptions({
        lang:{
          thousandsSep: ','
        }
      });
      var categories = [
        <?php
        for ($i=0; $i < count($rotacionPorAlmacen) ; $i++) {
          if ($rotacionPorAlmacen[$i]["ACTIVO"] == 0 AND $rotacionPorAlmacen[$i]["BAJAXALM"] == 0 ) {

          }else{
              echo "'".$rotacionPorAlmacen[$i]["ALMACEN"]."',";
          }
        }
        ?>
      ];

      var data1 = [
        <?php
        for ($i=0; $i < count($rotacionPorAlmacen) ; $i++) {
          if ($rotacionPorAlmacen[$i]["ACTIVO"] == 0 AND $rotacionPorAlmacen[$i]["BAJAXALM"] == 0 ) {

          }else{
            echo $rotacionPorAlmacen[$i]["ACTIVO"].",";
          }
        }
        ?>
      ];
      var data2 = [
        <?php
        for ($i=0; $i < count($rotacionPorAlmacen) ; $i++) {
          if ($rotacionPorAlmacen[$i]["ACTIVO"] == 0 AND $rotacionPorAlmacen[$i]["BAJAXALM"] == 0 ) {

          }else{
            echo $rotacionPorAlmacen[$i]["BAJAXALM"].",";
          }
        }
        ?>
      ];
      $('#grafPerAlm').highcharts({
        chart:{
          type: 'line'
        },
        title:{
          text: 'ROTACION DE PERSONAL POR ALMACEN DE PLAZA <?php echo $plaza; ?>'
        },

        legend:{
          y:-40,
          borderWidth:1,
          backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        yAxis:{
          lineWidth: 2,
          min: 0,
          offset: 10,
          tickWidth: 1,
          title: {
              text: 'Personal'
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
          //valueDecimals: 2,
          //valuePrefix: '$',
          //valueSuffix: ' USD'
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
              enabled: true
            },
            enableMouseTracking:false
          }
        },
        xAxis:{
          categories: categories,
          labels:{
            formatter:function(){
              url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&contrato=<?=$contrato?>&depto=<?=$departamento?>&area=<?=$area?>&fecha=<?=$fecha?>';
                return '<a href="'+url+'">' +
                    this.value + '</a>';
            }
          }
        },
        subtitle:{
          text: '',
          align: 'right',
          x:-10,
        },
        series:[{
          showInLegend:false,
          name:'Personal Activo',
          data:data1,
        },{
          showInLegend:false,
          name: 'Personal de baja',
          data:data2,
        }]
      });
  <?php } ?>
</script>

<script type= "text/javascript">
$(function(){
  Highcharts.setOptions({
    lang:{
      thousandsSep: ','
    }
  });
  var categories = [
    <?php
    for ($i=0; $i < count($graficaMensual) ; $i++) {
      echo "'".$graficaMensual[$i]["MES"]."',";
    }
    ?>
  ];
  var data1 = [
    <?php
    for ($i=0; $i < count($graficaMensual); $i++) {
      $año_actual = date("Y");
      $anoComparar = substr($fecha, 6,4);
      $mes_Comparar = substr($fecha, 14, 2);
        if ($i < $mes_Comparar){
          echo $graficaMensual[$i]["ACTIVO"].",";
        }
        else {
          echo "0".",";
        }
    }
     ?>
  ];
  var data2 = [
    <?php
    for ($i=0; $i < count($graficaMensual); $i++) {
      $mes_Comparar = substr($fecha, 14, 2);
      if($i < $mes_Comparar){
        echo $graficaMensual[$i]["BAJA"].",";
      }
      else{
        echo "0".",";
      }

    }
     ?>
  ];
  var data3 = [
    <?php
    for ($i=0; $i < count($graficaMensual) ; $i++) {
      echo $graficaMensual[$i]["ACTIVO_ANTERIOR"].",";
    }
     ?>
  ];
  var data4 = [
    <?php
    for ($i=0; $i < count($graficaMensual) ; $i++) {
      echo $graficaMensual[$i]["BAJA_ANTERIOR"].",";
    }
     ?>
  ];
  var data5 = [
    <?php
    for ($i=0; $i < count($graficaMensual) ; $i++) {
      echo $graficaMensual[$i]["ACTIVO_ANTERIOR2"].",";
    }
     ?>
  ];
  var data6 = [
    <?php
    for ($i=0; $i < count($graficaMensual) ; $i++) {
      echo $graficaMensual[$i]["BAJA_ANTERIOR2"].",";
    }
     ?>
  ];
  var data7 = [
    <?php
    for ($i=0; $i < count($graficaMensual) ; $i++) {
      echo $graficaMensual[$i]["ACTIVO_ANTERIOR3"].",";
    }
     ?>
  ];
  var data8 = [
    <?php
    for ($i=0; $i < count($graficaMensual) ; $i++) {
      echo $graficaMensual[$i]["BAJA_ANTERIOR3"].",";
    }
     ?>
  ];
  $('#graf_perM').highcharts({
    chart:{
      type:'line'
    },
    title:{
      text:'ROTACIÓN DE PERSONAL MENSUAL (SEMANAL)'
    },
    legend:{
      y: -40,
      borderWidth:1,
      backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
      lineWidth:2,
      offset:10,
      tickWidth:1,
      title:{
        text:'Personal'
      },
      labels:{
        formatter:function(){
          return this.value;
        }
      }
    },
    tooltip:{
      shared:true,
      valueSuffix: ' ',
      useHTML: true,
    },
    lang:{
      printChart:'Imprmir Grafica',
      downloadPNG:'Descargar PNG',
      downloadJPEG:'Descargar JPEG',
      downloadPDF:'Descargar PDF',
      downloadSVG:'Descargar SVG',
      contextButtonTitle: 'Exportar Grafica'
    },
    credits:{
      enabled:false
    },
    colors:['#1399C2', '#C21313', '#5CBC0C', '#060606', '#FFC300', '#C100FF', '#FF4D00', '#00FF49'],
    //colors:['#1399C2', '#C21313', '#0D6580', '#E61717'],
    plotOptions:{
      series:{
        minPointLength:3
      }
    },
    xAxis:{
      categories:categories,
      labels:{
        formatter: function(){
          url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
          url = '?plaza='+this.value+'&check=<?=$fil_check?>&contrato=<?=$contrato?>&depto=<?=$departamento?>&area=<?=$area?>&fecha=<?=$fecha?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle:{
      text:'',
      align:'right',
      x:-10,
    },
    series:[{
      name:'Personal Activo del <?php if ($fecha == 'ALL') { echo date('Y');} else { echo substr($fecha, 6, 5);} ?>',
      data: data1,
    },{
      name:'Personal Baja del <?php if ($fecha == 'ALL') { echo date('Y');} else { echo substr($fecha, 6, 5);} ?>',
      data: data2,
    },{
      name:'Personal Activo del <?php if ($fecha == 'ALL') { echo date('Y')-1;} else { echo substr($fecha, 6, 5)-1;} ?>',
      data: data3,
    },{
      name:'Personal Baja del <?php if ($fecha == 'ALL') { echo date('Y')-1;} else { echo substr($fecha, 6, 5)-1;} ?>',
      data: data4,
    },{
      name:'Personal Activo del <?php if ($fecha == 'ALL') { echo date('Y')-2;} else { echo substr($fecha, 6, 5)-2;} ?>',
      data: data5,
    },{
      name:'Personal Baja del <?php if ($fecha == 'ALL') { echo date('Y')-2;} else { echo substr($fecha, 6, 5)-2;} ?>',
      data: data6,
    },{
      name:'Personal Activo del <?php if ($fecha == 'ALL') { echo date('Y')-3;} else { echo substr($fecha, 6, 5)-3;} ?>',
      data: data7,
    },{
      name:'Personal Baja del <?php if ($fecha == 'ALL') { echo date('Y')-3;} else { echo substr($fecha, 6, 5)-3;} ?>',
      data: data8,
    }
    ]
  });
});
</script>
<!--
<script type= "text/javascript">
$(function(){
  Highcharts.setOptions({
    lang:{
      thousandsSep: ','
    }
  });
  var categories = [
    <?php
    $anio = substr($fecha, 17, 4);
    for ($i=0; $i < 5 ; $i++) {
      $anio2 = $anio - 4 + $i;
      echo "'".$anio2."',";
    }
    ?>
  ];
  var data1 = [
    <?php
    $anio = substr($fecha, 17, 4);
    $mes = substr($fecha, 14, 2);
    for ($i=0; $i < 5; $i++) {
      $anio2 = $anio - 4 + $i;
      $tipo = 2 ;
      $tipo2 = 3 ;
      if ($anio2 == $anio) {
        $porcentaje_anual1 = $obj_class->portAlmacen2($plaza,$anio2,$fil_check,$fil_habilitado, $tipo, $mes, 1);
        $porcentaje_anual2 = $obj_class->portAlmacen2($plaza,$anio2,$fil_check,$fil_habilitado, $tipo2, $mes, 1);
      }else {
        $porcentaje_anual1 = $obj_class->portAlmacen($plaza,$anio2,$fil_check,$fil_habilitado, $tipo , 1);
        $porcentaje_anual2 = $obj_class->portAlmacen($plaza,$anio2,$fil_check,$fil_habilitado, $tipo2, 1);
      }
          echo number_format($porcentaje_anual1[0]["PORCENTAJE"]+$porcentaje_anual2[0]["PORCENTAJE"], 2).",";
    }
     ?>
  ];
  var data2 = [
    <?php
    $anio = substr($fecha, 17, 4);
    for ($i=0; $i < 5; $i++) {
      $anio2 = $anio - 4 +  $i;
      $tipo = 3 ;
      if ($anio2 == $anio) {
          $porcentaje_anual = $obj_class->portAlmacen2($plaza,$anio2,$fil_check,$fil_habilitado, $tipo, $mes, 1);
      }else {
          $porcentaje_anual = $obj_class->portAlmacen($plaza,$anio2,$fil_check,$fil_habilitado, $tipo, 1);
      }
      echo number_format($porcentaje_anual[0]["PORCENTAJE"], 2).",";
    }
     ?>
  ];
  var data3 = [
    <?php
    $anio = substr($fecha, 17, 4);
    for ($i=0; $i < 5; $i++) {
      $anio2 = $anio - 4 + $i;
      $tipo = 2 ;
      if ($anio2 == $anio) {
          $porcentaje_anual = $obj_class->portAlmacen2($plaza,$anio2,$fil_check,$fil_habilitado, $tipo, $mes, 1);
      }else {
          $porcentaje_anual = $obj_class->portAlmacen($plaza,$anio2,$fil_check,$fil_habilitado, $tipo,  1);
      }
      echo number_format($porcentaje_anual[0]["PORCENTAJE"], 2).",";
    }
     ?>
  ];
  var data4 = [
    <?php
    $anio = substr($fecha, 17, 4);
    for ($i=0; $i < 5; $i++) {
      $anio2 = $anio - $i;
      $tipo = 1 ;
      $porcentaje_anual = $obj_class->portAlmacen($plaza,$anio2,$fil_check,$fil_habilitado, $tipo, 1);
        //if ($i < $mes_Comparar){
          echo number_format($porcentaje_anual[0]["PORCENTAJE"], 2).",";
        //}
        //else {
          //echo "0".",";
        //}
    }
     ?>
  ];
  $('#graf_perM2').highcharts({
    chart:{
      type:'column'
    },
    title:{
      text:'ROTACIÓN ANUAL GENERAL, ADMINISTRATIVO Y OPERATIVO (QUINCENAL).'
    },
    legend:{
      y: -40,
      borderWidth:1,
      backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
      lineWidth:2,
      offset:10,
      tickWidth:1,
      title:{
        text:'Personal'
      },
      labels:{
        formatter:function(){
          return this.value;
        }
      }
    },
    tooltip:{
      shared:true,
      valueSuffix: ' ',
      useHTML: true,
    },
    lang:{
      printChart:'Imprmir Grafica',
      downloadPNG:'Descargar PNG',
      downloadJPEG:'Descargar JPEG',
      downloadPDF:'Descargar PDF',
      downloadSVG:'Descargar SVG',
      contextButtonTitle: 'Exportar Grafica'
    },
    credits:{
      enabled:false
    },
    colors:['#1399C2', '#C21313', '#5CBC0C', '#060606'],
    //colors:['#1399C2', '#C21313', '#0D6580', '#E61717'],
    plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: true
            },
            enableMouseTracking:false
          }
    },
    xAxis:{
      categories:categories,
      labels:{
        formatter: function(){
          url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
          url = '?plaza='+this.value+'&check=<?=$fil_check?>&contrato=<?=$contrato?>&depto=<?=$departamento?>&area=<?=$area?>&fecha=<?=$fecha?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle:{
      text:'',
      align:'right',
      x:-10,
    },
    series:[{
      name:'% ROTACION DE PERSONAL GENERAL',
      type: 'column',
      data: data1,
      color: "yellow",
    },{
      name:'% ROTACIÓN DE PERSONAL ADMINISTRATIVO',
      type: 'column',
      data: data2,
    },{
      name:'% ROTACIÓN DE PERSONAL OPERATIVO',
      type: 'column',
      data: data3,
    }/*,{
      name:'Personal Baja del <?php if ($fecha == 'ALL') { echo date('Y')-1;} else { echo substr($fecha, 6, 5)-1;} ?>',
      data: data4,
    }*/
    ]
  });
});
</script>
-->
<script type= "text/javascript">
$(function(){
  Highcharts.setOptions({
    lang:{
      thousandsSep: ','
    }
  });
  var categories = [
    <?php
    $anio = substr($fecha, 17, 4);
    for ($i=0; $i < 5 ; $i++) {
      $anio2 = $anio - 4 + $i;
      echo "'".$anio2."',";
    }
    ?>
  ];
  var data1 = [
    <?php
    $anio = substr($fecha, 17, 4);
    $mes = substr($fecha, 14, 2);
    for ($i=0; $i < 5; $i++) {
      $anio2 = $anio - 4 + $i;
      $tipo = 2 ;
      $tipo2 = 3 ;
      if ($anio2 == $anio) {
        $porcentaje_anual1 = $obj_class->portAlmacen2($plaza,$anio2,$fil_check,$fil_habilitado, $tipo, $mes, 2);
        $porcentaje_anual2 = $obj_class->portAlmacen2($plaza,$anio2,$fil_check,$fil_habilitado, $tipo2, $mes, 2);
      }else {
        $porcentaje_anual1 = $obj_class->portAlmacen($plaza,$anio2,$fil_check,$fil_habilitado, $tipo , 2);
        $porcentaje_anual2 = $obj_class->portAlmacen($plaza,$anio2,$fil_check,$fil_habilitado, $tipo2, 2);
      }

      if (COUNT($porcentaje_anual1) == 0 AND COUNT($porcentaje_anual2) == 0) {
        echo number_format( "0" , 2). ",";
      }elseif (COUNT($porcentaje_anual2) == 0 AND COUNT($porcentaje_anual1) > 0 ) {
        echo number_format($porcentaje_anual1[0]["PORCENTAJE"], 2). ",";
      }elseif (COUNT($porcentaje_anual1) == 0 AND COUNT($porcentaje_anual2) > 0){
        echo number_format($porcentaje_anual2[0]["PORCENTAJE"], 2). ",";
      }else {
        echo number_format($porcentaje_anual1[0]["PORCENTAJE"]+$porcentaje_anual2[0]["PORCENTAJE"], 2).",";
      }

    }
     ?>
  ];
  var data2 = [
    <?php
    $anio = substr($fecha, 17, 4);
    for ($i=0; $i < 5; $i++) {
      $anio2 = $anio - 4 +  $i;
      $tipo = 3 ;
      if ($anio2 == $anio) {
          $porcentaje_anual = $obj_class->portAlmacen2($plaza,$anio2,$fil_check,$fil_habilitado, $tipo, $mes, 2);
      }else {
          $porcentaje_anual = $obj_class->portAlmacen($plaza,$anio2,$fil_check,$fil_habilitado, $tipo, 2);
      }

      if (COUNT($porcentaje_anual) == 0 ) {
        echo number_format( "0" , 2). ",";
      }else {
        //echo number_format($porcentaje_anual1[0]["PORCENTAJE"]+$porcentaje_anual2[0]["PORCENTAJE"], 2).",";
        echo number_format($porcentaje_anual[0]["PORCENTAJE"], 2).",";
      }
    }
     ?>
  ];
  var data3 = [
    <?php
    $anio = substr($fecha, 17, 4);
    for ($i=0; $i < 5; $i++) {
      $anio2 = $anio - 4 + $i;
      $tipo = 2 ;
      if ($anio2 == $anio) {
          $porcentaje_anual = $obj_class->portAlmacen2($plaza,$anio2,$fil_check,$fil_habilitado, $tipo, $mes, 2);
      }else {
          $porcentaje_anual = $obj_class->portAlmacen($plaza,$anio2,$fil_check,$fil_habilitado, $tipo,  2);
      }
      //echo number_format($porcentaje_anual[0]["PORCENTAJE"], 2).",";
      if (COUNT($porcentaje_anual) == 0 ) {
        echo number_format( "0" , 2). ",";
      }else {
        //echo number_format($porcentaje_anual1[0]["PORCENTAJE"]+$porcentaje_anual2[0]["PORCENTAJE"], 2).",";
        echo number_format($porcentaje_anual[0]["PORCENTAJE"], 2).",";
      }
    }
     ?>
  ];
  var data4 = [
    <?php
    $anio = substr($fecha, 17, 4);
    for ($i=0; $i < 5; $i++) {
      $anio2 = $anio - $i;
      $tipo = 1 ;
      $porcentaje_anual = $obj_class->portAlmacen($plaza,$anio2,$fil_check,$fil_habilitado, $tipo, 2);
        //if ($i < $mes_Comparar){
//          echo number_format($porcentaje_anual[0]["PORCENTAJE"], 2).",";
          if (COUNT($porcentaje_anual) == 0 ) {
            echo number_format( "0" , 2). ",";
          }else {
            //echo number_format($porcentaje_anual1[0]["PORCENTAJE"]+$porcentaje_anual2[0]["PORCENTAJE"], 2).",";
            echo number_format($porcentaje_anual[0]["PORCENTAJE"], 2).",";
          }
    }
     ?>
  ];
  $('#graf_perMSemanal').highcharts({
    chart:{
      type:'line'
    },
    title:{
      text:'ROTACIÓN ANUAL GENERAL (SEMANAL).'
    },
    legend:{
      y: -40,
      borderWidth:1,
      backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
      lineWidth:2,
      offset:10,
      tickWidth:1,
      title:{
        text:'Personal'
      },
      labels:{
        formatter:function(){
          return this.value;
        }
      }
    },
    tooltip:{
      shared:true,
      valueSuffix: ' ',
      useHTML: true,
    },
    lang:{
      printChart:'Imprmir Grafica',
      downloadPNG:'Descargar PNG',
      downloadJPEG:'Descargar JPEG',
      downloadPDF:'Descargar PDF',
      downloadSVG:'Descargar SVG',
      contextButtonTitle: 'Exportar Grafica'
    },
    credits:{
      enabled:false
    },
    colors:['#1399C2', '#C21313', '#5CBC0C', '#060606'],
    //colors:['#1399C2', '#C21313', '#0D6580', '#E61717'],
    plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: true
            },
            enableMouseTracking:false
          }
    },
    xAxis:{
      categories:categories,
      labels:{
        formatter: function(){
          url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
          url = '?plaza='+this.value+'&check=<?=$fil_check?>&contrato=<?=$contrato?>&depto=<?=$departamento?>&area=<?=$area?>&fecha=<?=$fecha?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle:{
      text:'',
      align:'right',
      x:-10,
    },
    series:[{
      name:'% ROTACION DE PERSONAL GENERAL',
      type: 'column',
      data: data1,
      color: "yellow",
    },{
      name:'% ROTACIÓN DE PERSONAL ADMINISTRATIVO',
      type: 'column',
      data: data2,
    },{
      name:'% ROTACIÓN DE PERSONAL OPERATIVO',
      type: 'column',
      data: data3,
    }/*,{
      name:'Personal Baja del <?php if ($fecha == 'ALL') { echo date('Y')-1;} else { echo substr($fecha, 6, 5)-1;} ?>',
      data: data4,
    }*/
    ]
  });
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

    $('#tabla_activo').DataTable( {
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
            title: 'Personal Activo',
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
$(document).ready(function() {
    $('#tabla_baja').DataTable( {
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
            title: 'Personal de Baja',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal de Baja',
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
