<?php
ini_set('display_errors', false);

/*inicia code solucion quita mensaje reenvio de form*/
if( $_SERVER['REQUEST_METHOD'] == 'POST')
{
  header("location: ".$_SERVER["PHP_SELF"]." ");
}
/*termina code solucion quita mensaje reenvio de form*/
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '5');
if($modulos_valida == 0)
{
  header('Location: index.php');
}

//OBJETO PARA INFORMACION GENERAL CARGA, DESCARGAS Y OTROS
include_once '../class/Manufactura_infogral.php';
$obj_info_gral_manufactura   = new Info_gral_manufactura();
$obj_historial_manufac   = new Historial_manufac();
//OBJETO PARA INFORMACION DE CARGAS
include_once '../class/Manufactura.php';
$obj_op_in_car_des_info   = new Op_in_car_des_info();
$obj_info_otros_manufac   = new Op_in_otros_info();

/* INICIA RECUPERACION DE VALORES DEL FILTRO INDEX */
$i_plaza=$_SESSION['i_plaza'];

if ($_SESSION['select_manufac_global_plaza'] == false){
  //if($_SESSION['area']==3 ){
    $select_manufac_global_plaza = $_SESSION['select_manufac_global_plaza'];
  //}else{
    //$_SESSION['select_manufac_global_plaza'] = array(3,4,5,6,7,8,17,18,23);
    //$select_manufac_global_plaza = $_SESSION['select_manufac_global_plaza'];
  //}
}else{
  if(isset($_POST['select_manufac_global_plaza']))
  $_SESSION['select_manufac_global_plaza'] = $_POST['select_manufac_global_plaza'];
  $select_manufac_global_plaza = $_SESSION['select_manufac_global_plaza'];
}
/* TERMINA RECUPERACION DE VALORES DEL FILTRO INDEX*/

//SESIONES PARA INFORMACION GENERAL
//$select_manufac_global_plaza = array(3,0,0,6,0,0,0,18,23);
/////////////////////// GUARDA AL VALOR DE LAS PLAZAS A CONECTAR EN UNA SESSION
/*if ($_SESSION['select_manufac_global_plaza'] == false){
  $_SESSION['select_manufac_global_plaza'] = array(3,4,5,6,7,8,17,18,23);
  $select_manufac_global_plaza = $_SESSION['select_manufac_global_plaza'];
}else{
  if(isset($_POST['select_manufac_global_plaza']))
  $_SESSION['select_manufac_global_plaza'] = $_POST['select_manufac_global_plaza'];
  $select_manufac_global_plaza = $_SESSION['select_manufac_global_plaza'];
}*/
/////////////////////// GUARDA AL VALOR DEL DIA EN UNA SESSION
if ($_SESSION['dia_manufac'] == false){
  $_SESSION['dia_manufac'] = date('d-m-Y');
  $dia_manufac = $_SESSION['dia_manufac'];
}else{
  if(isset($_POST['dia_manufac']))
  $_SESSION['dia_manufac'] = $_POST['dia_manufac'];
  $dia_manufac = $_SESSION['dia_manufac'];
}
/////////////////////// GUARDA AL VALOR DE LA PLAZA EN UNA SESSION
if(isset($_POST['plaza_manufac']))
  $_SESSION['plaza_manufac'] = $_POST['plaza_manufac'];
  $plaza_manufac = $_SESSION['plaza_manufac'];
/////////////////////// GUARDA AL VALOR DE RANGO DE FECHAS EN UNA SESSION
/*FECHA INICIO*/
if(isset($_POST['fec_ini_per_manufac']))
  $_SESSION['fec_ini_per_manufac'] = $_POST['fec_ini_per_manufac'];
  $fec_ini_per_manufac = $_SESSION['fec_ini_per_manufac'];
/*FECHA FINAL*/
if(isset($_POST['fec_fin_per_manufac']))
  $_SESSION['fec_fin_per_manufac'] = $_POST['fec_fin_per_manufac'];
  $fec_fin_per_manufac = $_SESSION['fec_fin_per_manufac'];
///////////////TITULO FECHA MANUFACTURA
  if ($dia_manufac == true){
    switch (true) {
      case ($fec_ini_per_manufac == true) && ($fec_fin_per_manufac == true):
        $titulo_fec_manufac = $fec_ini_per_manufac."/".$fec_fin_per_manufac ;
        break;

      default:
        $titulo_fec_manufac = $dia_manufac ;
        break;
    }
  }

  $time = time();
  date_default_timezone_set("America/Mexico_City");


$info_manufac_car_desProgramados = $obj_op_in_car_des_info->car_des_prog_info($plaza_manufac,$dia_manufac,$fec_ini_per_manufac,$fec_fin_per_manufac,$select_manufac_global_plaza);
$contador_cargas=0;
$contador_descargas=0;
///////////////////////////////////////////
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- *********** INICIA INCLUDE CSS *********** -->
<style type="text/css" media="screen">
 .imgwrapper {
   width: 95%;
}

.main{
  max-width: auto;
  margin: 0 auto;
  display: grid;
  grid-template-columns: auto auto auto auto auto;
  padding: 1rem;
  grid-gap: 1rem;
}

@media all and (max-width: 775px){
  .main{
    display: inline;
  }
  .col-3{
    padding: 3px;
  }
}
</style>
<!-- DataTables -->
<!-- <link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">  -->
<!-- <link rel="stylesheet" href="../plugins/datatables/jquery.dataTables.min.css"> -->
<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<!-- DataTables ROW GROUP -->
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.0.0/css/rowGroup.dataTables.min.css">
<!-- CSS PARA DISEÑO DE LINEA DE TIEMPO -->
<link rel="stylesheet" href="../plugins/line_time_manufactura.css">
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Dashboard<small>Manufactura</small> <small>PLAZA ( <?php echo $_SESSION['nomPlaza'] ?> )</small></h1>

      <ol class="breadcrumb">
        <li>
        <?php if ($plaza_manufac == true) { ?>
          <form method="post">
          <input type="hidden" name="plaza_manufac" value="">
          <div id="content_car_text_btn_reg[]">
          <button type="submit" name='plaza' value='' class="btn btn-link click_car_btn_reg"><i class="fa fa-reply"></i> Regresar</button>
          </div>
          </form>
        <?php } ?>
        </li>
        <li>
          <button type='button' class='btn btn-link' data-toggle="modal" data-target="#modal_his_fecha_manufac"><i class="fa fa-history"></i>  Historial</button>
        </li>
        <li>
          <a data-toggle="tab" href="#op_manufactura_fec_per">
            <button type="submit" class="btn btn-link"><i class="fa fa-calendar"></i>  Fecha personalizada</button>
          </a>
        </li>
        <?php //if ($plaza_manufac==false && $_SESSION['area']!=3){ ?> <!-- FILTRO P/DEPTO DIFERENTE A OPERACIONES -->
        <!--<li>
          <button type='button' class='btn btn-link' data-toggle="modal" data-target="#modal_sel_plaza_glo"><i class="fa fa-toggle-on"></i>  Selección de plazas</button>
        </li>-->
        <?php  //} ?>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

<!-- INICIA CODE FECHA PERSONALIZADA -->
      <div class="tab-content" id="content_car_text_btn_reg[]">
        <div id="op_manufactura_fec_per" class="tab-pane fade">
          <form method="post">
            <div class="col-md-4">
              <!--  -->
              <div class="input-group" id="fec_rango_ce_cce">
                <div class="input-group-addon">
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_ini_per_manufac" id="fec_ini_per_manufac" value="<?= $fec_ini_per_manufac ?>" readonly>
                <div class="input-group-addon">
                  <i class="fa fa-calendar-plus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_fin_per_manufac" id="fec_fin_per_manufac" value="<?= $fec_fin_per_manufac ?>" readonly>
              </div>
              <!--  -->
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-sm bg-blue click_car_btn_reg">OK</button>
            </div>
          </form>
        </div>
      </div><br><br>
    <!-- TERMINA CODE FECHA PERSONALIZADA -->


<!-- ######################################## INICIO DE MODALS ######################################### -->

  <!-- MODAL SELECCION DE PLAZAS GLOBAL -->
  <div class="modal fade" id="modal_sel_plaza_glo" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-sm">
      <div id="content_img_modal_his[]" class="modal-content"><!-- Modal content -->
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Conexiones a Plazas</h4>
        </div>
        <div class="modal-body"><!-- modal-body -->
          <div class="form-group"><!-- form-group -->

            <div class="checkbox"><label>
              <input type="checkbox" id="check_cor" <?php if ($select_manufac_global_plaza[0]==true){echo "checked";} ?> >PLAZA CORDOBA
            </label></div>
            <div class="checkbox"><label>
              <input type="checkbox" id="check_mex" <?php if ($select_manufac_global_plaza[1]==true){echo "checked";} ?> >PLAZA MEXICO
            </label></div>
            <div class="checkbox"><label>
              <input type="checkbox" id="check_gol" <?php if ($select_manufac_global_plaza[2]==true){echo "checked";} ?> >PLAZA GOLFO
            </label></div>
            <div class="checkbox"><label>
              <input type="checkbox" id="check_pen" <?php if ($select_manufac_global_plaza[3]==true){echo "checked";} ?> >PLAZA PENINSULA
            </label></div>
            <div class="checkbox"><label>
              <input type="checkbox" id="check_pue" <?php if ($select_manufac_global_plaza[4]==true){echo "checked";} ?> >PLAZA PUEBLA
            </label></div>
            <div class="checkbox"><label>
              <input type="checkbox" id="check_baj" <?php if ($select_manufac_global_plaza[5]==true){echo "checked";} ?> >PLAZA BAJIO
            </label></div>
            <div class="checkbox"><label>
              <input type="checkbox" id="check_occ" <?php if ($select_manufac_global_plaza[6]==true){echo "checked";} ?> >PLAZA OCCIDENTE
            </label></div>
            <div class="checkbox"><label>
              <input type="checkbox" id="check_nor" <?php if ($select_manufac_global_plaza[7]==true){echo "checked";} ?> >PLAZA NORESTE
            </label></div>
            <div class="checkbox"><label>
              <!-- <input type="checkbox" id="check_leo" <?php if ($select_manufac_global_plaza[8]==true){echo "checked";} ?> >PLAZA LEON -->
            </label></div>

          </div><!-- ./form-group -->
        </div><!-- ./modal-body -->
        <div class="modal-footer">
        <form method="post">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
          <button type="submit" id="click_modal_his[]" class="btn btn-success">Ok</button>

          <input type="hidden" name="select_manufac_global_plaza[]" id="input_cor" <?php if ($select_manufac_global_plaza[0]==true){echo "value='3'";}else{echo "value='0'";} ?>>
          <input type="hidden" name="select_manufac_global_plaza[]" id="input_mex" <?php if ($select_manufac_global_plaza[1]==true){echo "value='4'";}else{echo "value='0'";} ?>>
          <input type="hidden" name="select_manufac_global_plaza[]" id="input_gol" <?php if ($select_manufac_global_plaza[2]==true){echo "value='5'";}else{echo "value='0'";} ?>>
          <input type="hidden" name="select_manufac_global_plaza[]" id="input_pen" <?php if ($select_manufac_global_plaza[3]==true){echo "value='6'";}else{echo "value='0'";} ?>>
          <input type="hidden" name="select_manufac_global_plaza[]" id="input_pue" <?php if ($select_manufac_global_plaza[4]==true){echo "value='7'";}else{echo "value='0'";} ?>>
          <input type="hidden" name="select_manufac_global_plaza[]" id="input_baj" <?php if ($select_manufac_global_plaza[5]==true){echo "value='8'";}else{echo "value='0'";} ?>>
          <input type="hidden" name="select_manufac_global_plaza[]" id="input_occ" <?php if ($select_manufac_global_plaza[6]==true){echo "value='17'";}else{echo "value='0'";} ?>>
          <input type="hidden" name="select_manufac_global_plaza[]" id="input_nor" <?php if ($select_manufac_global_plaza[7]==true){echo "value='18'";}else{echo "value='0'";} ?>>
          <input type="hidden" name="select_manufac_global_plaza[]" id="input_leo" <?php if ($select_manufac_global_plaza[8]==true){echo "value='23'";}else{echo "value='0'";} ?>>
        </form>
        </div>
      </div><!-- /.Modal content -->
    </div>
  </div>

  <!-- MODAL SELECCION DE HISTORIAL FECHA -->
  <div class="modal fade" id="modal_his_fecha_manufac" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content"><!-- Modal content -->
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Historial de Operaciones Manufacturas</h4>
        </div>
        <div id="content_img_modal_his[]" class="modal-body"><!-- modal-body -->
        <?= $plaza_manufac."<br>" ?>
          <form  method="post">
            <input type="hidden" name="fec_ini_per_manufac" value="">
            <input type="hidden" name="fec_fin_per_manufac" value="">


            <div class="form-group">
            <select id="click_modal_his[]"  onchange="this.form.submit();" name="dia_manufac" class="form-control select_manufac" style="width: 100%;">
            <option value=""></option>
            <option value="<?=date('d-m-Y')?>"><?=date('d-m-Y')?></option>
        <!-- INICIA SELECT GLOBAL -->
        <?php
        if ($plaza_manufac==false){
          $dia_select_manufac = $obj_historial_manufac->dia_select_manufac($select_manufac_global_plaza);
          ?>
            <!-- PLAZA CORDOBA -->
            <optgroup label="Plaza Córdoba">
            <?php
            for ($i=0; $i <count($dia_select_manufac) ; $i++) {
              switch ($dia_select_manufac[$i]["ID_PLAZA"]) {
                case 3:
                  echo '<option value="'.$dia_select_manufac[$i]["FECHA"].'">'.$dia_select_manufac[$i]["FECHA"].'</option>';
                  break;
              }
            }
            ?>
            </optgroup>
            <!-- PLAZA MEXICO -->
            <optgroup label="Plaza México">
            <?php
            for ($i=0; $i <count($dia_select_manufac) ; $i++) {
              switch ($dia_select_manufac[$i]["ID_PLAZA"]) {
                case 4:
                  echo '<option value="'.$dia_select_manufac[$i]["FECHA"].'">'.$dia_select_manufac[$i]["FECHA"].'</option>';
                  break;
              }
            }
            ?>
            </optgroup>
            <!-- PLAZA GOLFO -->
            <optgroup label="Plaza Golfo">
            <?php
            for ($i=0; $i <count($dia_select_manufac) ; $i++) {
              switch ($dia_select_manufac[$i]["ID_PLAZA"]) {
                case 5:
                  echo '<option value="'.$dia_select_manufac[$i]["FECHA"].'">'.$dia_select_manufac[$i]["FECHA"].'</option>';
                  break;
              }
            }
            ?>
            </optgroup>
            <!-- PLAZA PENINSULA -->
            <optgroup label="Plaza Peninsula">
            <?php
            for ($i=0; $i <count($dia_select_manufac) ; $i++) {
              switch ($dia_select_manufac[$i]["ID_PLAZA"]) {
                case 6:
                  echo '<option value="'.$dia_select_manufac[$i]["FECHA"].'">'.$dia_select_manufac[$i]["FECHA"].'</option>';
                  break;
              }
            }
            ?>
            </optgroup>
            <!-- PLAZA PUEBLA -->
            <optgroup label="Plaza Puebla">
            <?php
            for ($i=0; $i <count($dia_select_manufac) ; $i++) {
              switch ($dia_select_manufac[$i]["ID_PLAZA"]) {
                case 7:
                  echo '<option value="'.$dia_select_manufac[$i]["FECHA"].'">'.$dia_select_manufac[$i]["FECHA"].'</option>';
                  break;
              }
            }
            ?>
            </optgroup>
            <!-- PLAZA BAJIO -->
            <optgroup label="Plaza Bajio">
            <?php
            for ($i=0; $i <count($dia_select_manufac) ; $i++) {
              switch ($dia_select_manufac[$i]["ID_PLAZA"]) {
                case 8:
                  echo '<option value="'.$dia_select_manufac[$i]["FECHA"].'">'.$dia_select_manufac[$i]["FECHA"].'</option>';
                  break;
              }
            }
            ?>
            </optgroup>
            <!-- PLAZA OCCIDENTE -->
            <optgroup label="Plaza Occidente">
            <?php
            for ($i=0; $i <count($dia_select_manufac) ; $i++) {
              switch ($dia_select_manufac[$i]["ID_PLAZA"]) {
                case 17:
                  echo '<option value="'.$dia_select_manufac[$i]["FECHA"].'">'.$dia_select_manufac[$i]["FECHA"].'</option>';
                  break;
              }
            }
            ?>
            </optgroup>
            <!-- PLAZA NORESTE -->
            <optgroup label="Plaza Noreste">
            <?php
            for ($i=0; $i <count($dia_select_manufac) ; $i++) {
              switch ($dia_select_manufac[$i]["ID_PLAZA"]) {
                case 18:
                  echo '<option value="'.$dia_select_manufac[$i]["FECHA"].'">'.$dia_select_manufac[$i]["FECHA"].'</option>';
                  break; ;
              }
            }
            ?>
            </optgroup>

        <?php } ?>
        <!-- TERMINA SELECT GLOBAL -->
        <!-- INICIA SELECT POR PLAZA SELECCIONADA -->
        <?php if($plaza_manufac == true){
          $historial_plaza = $obj_op_in_car_des_info->historial_plaza($plaza_manufac);
          for ($i=0; $i <count($historial_plaza) ; $i++) {
            echo "<option value='".$historial_plaza[$i]["FECHA"]."'>".$historial_plaza[$i]["FECHA"]."</option>";
          }
        ?>
        <?php  } ?>
        <!-- TERMINA SELECT POR PLAZA SELECCIONADA -->

            </select>
            </div>
          </form>
        </div><!-- ./modal-body -->
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div><!-- /.Modal content -->
    </div>
  </div>

<!-- ######################################## TERMINO DE MODALS ######################################### -->


<!-- ######################################## Inicio de Widgets ######################################### -->
    <section><!-- Inicia la seccion de los Widgets -->
      <div class="row">
        <?php //echo "<center><h4>PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>"; ?><!--FILTRO GENERAL -->
      <h4 align="center" class="box-title text-light-blue"><i class="fa fa-truck"></i> OPERACIONES MANUFACTURA <?= $plaza_manufac ?> <code><?=$titulo_fec_manufac?></code></h4>
      <hr>

      <div class="main">
      <!-- Widgets Numero de cargas -->
        <div class="col-3">
          <div class="info-box bg-aqua">
            <span class="info-box-icon bg-aqua"><i class="fa fa-upload"></i></span>
            <div class="info-box-content bg-aqua">
              <span class="info-box-text">Cargas</span>
                  <span id="widgets_cargas" class="info-box-number">0</span>
                  <span class="progress-description">
                    En Proceso:<b id="widgets_cargas_pro[]">0</b>
                  </span>
                  <span class="progress-description">
                    Finalizado: <b id="widgets_cargas_fin[]">0</b>
                  </span>
            </div><!-- <button class="btn bg-aqua-active btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
          </div>
        </div>
        <!-- Widgets Numero de descargas -->
        <div class="col-3">
          <div class="info-box bg-green">
            <span class="info-box-icon bg-green"><i class="fa fa-download"></i></span>
            <div class="info-box-content bg-green">
              <span class="info-box-text">Descargas</span>
                  <span id="widgets_descargas" class="info-box-number">0</span>
                  <span class="progress-description">
                    En Proceso: <b id="widgets_descargas_pro[]">0</b>
                  </span>
                  <span class="progress-description">
                    Finalizado: <b id="widgets_descargas_fin[]">0</b>
                  </span>
            </div>
            <!-- <button class="btn bg-green-active btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
          </div>
        </div>
        <!-- Widgets Otros -->
        <div class="col-3">
          <div class="info-box bg-yellow">
            <span class="info-box-icon bg-yellow"><i class="fa fa-ticket"></i></span>
              <div class="info-box-content bg-yellow">
              <span class="info-box-text">Otros</span>
                <span class="info-box-number" id="widgets_otros">0</span>
                <span class="progress-description">
                  En proceso: <b id="widgets_otros_proceso">0</b>
                </span>
                <span class="progress-description">
                  Finalizado: <b id="widgets_otros_fin"></b>
                </span>
              </div>
             <!-- <button class="btn bg-yellow-active  btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>   -->
          </div>
        </div>
        <!-- Widgets Desfasados -->
        <div class="col-3">
          <div class="info-box bg-red">
            <span class="info-box-icon bg-red"><i class="fa fa-warning"></i></span>
            <div class="info-box-content bg-red">
              <span class="info-box-text">Desfasados</span>
                  <span id="widgets_desfasados" class="info-box-number">0</span>
                  <span class="progress-description">
                    Cargas: <b id="widgets_desfasados_car[]">0</b>
                  </span>
                  <span class="progress-description">
                    Descargas: <b id="widgets_desfasados_des[]">0</b>
                  </span>
            </div>
            <!-- <button class="btn bg-red-active btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
          </div>
        </div><!-- Termino Widgets Desfasados -->

        <div class="col-3"><!-- Widgets Programados-->
          <div class="info-box bg-morado">
            <span class="info-box-icon bg-morado"><i class="fa fa-calendar"></i></span>
            <div class="info-box-content bg-morado">
              <span class="info-box-text">Programados</span>
              <span id="widgets_programadas" class="info-box-number">0</span>
              <span class="progress-description">Carga: <b id="widgets_cargas_programadas[]">0</b></span>
              <span class="progress-description">Descarga: <b id="widgets_descargas_programadas[]">0</b></span>
            </div>
          </div>
        </div><!-- Termino Widgets Desfasados -->
      </div> <!-- /.row -->
    </div>
    </section><!-- Termina la seccion de los Widgets -->

<!-- ######################################### Termino de Widgets ######################################### -->

<!-- *********************************** INICIA SECCION GENERAL DE OPERACIONES MANUFACTURA *********************************** -->
<?php if ($plaza_manufac == false) { ?><!-- reduce las conecciones al seleccionar una plaza -->


<!-- ############################ INICIA SECCION PARA TABLAS DE INFO GENERAL  ############################# -->

<!-- ########################### TERMINA SECCION PARA TABLAS DE INFO GENERAL  ########################### -->

<?php } ?><!-- reduce las conecciones al seleccionar una plaza -->
<!-- *********************************** TERMINA SECCION GENERAL DE OPERACIONES MANUFACTURA *********************************** -->


<!-- ****************************** INICIA SECCION POR PLAZA SELECCIONADA DE OPERACIONES MANUFACTURA ****************************** -->
<?php if ($plaza_manufac == false){ ?><!-- reduce las conecciones al seleccionar una plaza -->


<!-- ############################ INICIA SECCION PARA CARGAS ############################# -->



<!-- ######### INICIA SECCION TABLA CARGAS INFO ########## -->
<section>
  <div class="box box-info">
    <div class="box-header with-border">
      <p class="box-card">
        <ul class="nav nav-tabs" id="myTab_manufac_carga">
          <li class="active"><a id="tab_car_pro" data-toggle="tab" href="#link_tab_car_pro">EN PROCESO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_cargas_pro[]">0</span></a></li>
          <li><a id="tab_car_fin" data-toggle="tab" href="#link_tab_car_fin">FIN. EN TIEMPO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_fintiempo_car[]">0</span></a></li>
          <li><a id="tab_car_fin_des" data-toggle="tab" href="#link_tab_car_fin_des">FIN. DESFASADO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_desfasados_car[]">0</span></a></li>
          <li><a id="tab_car_fin_des_ritmo" data-toggle="tab" href="#link_tab_car_fin_des_ritmo">FIN. DESFASADO POR RITMO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_desfasados_car_ritmo[]">0</span></a></li>
          <li><a id="tab_car_can" data-toggle="tab" href="#link_tab_car_can">CANCELADO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_can_car[]">0</span></a></li>
          <li><a id="tab_car_des" data-toggle="tab" href="#link_tab_car_prog">PROGRAMADOS&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_cargas_programadas[]">0</span></a></li>
        </ul>
      </p>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

      <div class="nav-tabs-custom"><!-- nav-tabs-custom -->
        <div class="tab-content"><!-- tab-content -->
        <!-- TAB PARA CARGAS EN PROCESO -->
  <div id="link_tab_car_pro" class="tab-pane fade in active">

          <h5 class="text-blue text-center"> CARGAS EN PROCESO <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>

    <!-- ################################ INICIA TABLA CARGA EN PROCESO ################################  -->
    <div class="table-responsive" align="center"><!-- table-responsive -->
    <table id="tabla_proceso_carga" class="display compact" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th class="small"></th>
        </tr>
      </thead>
      <tbody>
          <?php
          $info_manufac_car_des = $obj_op_in_car_des_info->car_des_info($plaza_manufac,$dia_manufac,$fec_ini_per_manufac,$fec_fin_per_manufac,$select_manufac_global_plaza);
          for ($i=0; $i <count($info_manufac_car_des) ; $i++) {
            if ( $info_manufac_car_des[$i]["STATUS"] <= 4 && $info_manufac_car_des[$i]["TIPO"] == 1 ){
            $cargas_proceso[$i]= $i;

            //----------------------
            if ($info_manufac_car_des[$i]["STATUS"]==1){
              // -- //
              switch (true) {
                  case ( $info_manufac_car_des[$i]["REGISTRADO"] == false ):
                   $calculo_tiempo = "<code>error no se registró tiempo</code>";
                  break;
                default:
                  $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                  $calculo_tiempo = $obj_info_gral_manufactura->calculo_tiempo($info_manufac_car_des[$i]["REGISTRADO"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_info_gral_manufactura->dif_minutos($fechaFin_car_gen,$info_manufac_car_des[$i]["REGISTRADO"]);
                  break;
                }
              // -- //
            }else{
              //--  --//
              switch (true) {
              case ( $info_manufac_car_des[$i]["LLEGA"] == false ):
                 $calculo_tiempo = "<code>error no se registró tiempo</code>";
                break;
              case ( $info_manufac_car_des[$i]["DESPACHO"] == true && $info_manufac_car_des[$i]["STATUS"]==5 ):
                $fechaFin_car_gen = $info_manufac_car_des[$i]["DESPACHO"] ;
                $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($fechaFin_car_gen,$info_manufac_car_des[$i]["LLEGA"]);
                break;
              default:
                $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($fechaFin_car_gen,$info_manufac_car_des[$i]["LLEGA"]);
                break;
              }
              //--  --//
            }
            //----------------------

          ?>
        <tr><!-- INICIA TR CARGAS PROCESO -->
          <td colspan="7"><!-- INICIA TD CARGAS PROCESO -->

            <!-- INICIA LINEA DE TIEMPO PARA EL ESTADO DE CARGAS -->
            <span class="badge bg-teal"><i class="fa fa-industry"></i> Almacen: <cite><?= $info_manufac_car_des[$i]["ALMACEN"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-briefcase"></i> Cliente: <cite><?= $info_manufac_car_des[$i]["RS"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-truck"></i> Vehículo: <cite><?= $info_manufac_car_des[$i]["VEHICULO"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-support"></i> Placas: <cite>(<?= $info_manufac_car_des[$i]["PLACAS1"] ?>) (<?= $info_manufac_car_des[$i]["PLACAS2"] ?>)</cite></span>
            <span class="badge bg-teal"><i class="fa fa-arrows-h"></i> Anden: <cite><?= $info_manufac_car_des[$i]["ANDEN"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-clock-o"></i> Tiempo transcurrido: <cite><?= $calculo_tiempo ?></cite></span>
            <!-- INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
            <?php
              switch ($plaza_manufac) {
                case "BAJIO (ARGO)":
                  echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_des[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["ARRIBO"]."'><span class='badge bg-teal btn'><i class='fa fa-folder-open'></i> Detalles Retiro: <cite>".$info_manufac_car_des[$i]["ARRIBO"]."</cite></span></a>";
                  break;
                default:
                  echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_des[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["SOLICITUD"]."'><span class='badge bg-teal btn'><i class='fa fa-folder-open'></i> Detalles Vehiculo: <cite>".$info_manufac_car_des[$i]["SOLICITUD"]."</cite></span></a>";
                  break;
              }
              //echo "<a class='fancybox fancybox.iframe' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].")'><span class='badge bg-teal btn'><i class='fa fa-folder-open'></i> Galeria Vehiculo: <cite>".$info_manufac_car_des[$i]["SOLICITUD"]."</cite></span></a>";
              echo "<a class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'>
              <span class='badge bg-teal btn'>GALERIA</span></button></a>";


            ?>
            <!-- TERMINA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
            <br> <br> <br>

            <!-- ############### INICIA REGISTRO DE VEHICULO #################### -->
            <ol class="timeline-line">
            <?php
              switch ( $info_manufac_car_des[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $info_manufac_car_des[$i]["STATUS"] >= 1 )://si es mayor o igual a 1
                 $reg_veh_carga = date_create($info_manufac_car_des[$i]["REGISTRADO"]);
                  $dif_min = (strtotime($info_manufac_car_des[$i]["LLEGA"])-strtotime($info_manufac_car_des[$i]["REGISTRADO"]))/60;
                  if ($dif_min > 10) {// 10 mintos
                    echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($reg_veh_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Registro de vehículo <br>'.date_format($reg_veh_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }
                  else {
                    echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($reg_veh_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Registro de vehículo <br>'.date_format($reg_veh_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }

                   break;
                 default://si no
                   echo '<li class="timeline__step">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Registro de vehículo</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
            <!-- ############### INICIA LLEGADA DE VEHICULO #################### -->
            <?php
              switch ( $info_manufac_car_des[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $info_manufac_car_des[$i]["STATUS"] >= 2 )://si es mayor o igual a 2
                   $veh_enram_carga = date_create($info_manufac_car_des[$i]["LLEGA"]);
                   $dif_min = (strtotime($info_manufac_car_des[$i]["INICIA"])-strtotime($info_manufac_car_des[$i]["LLEGA"]))/60;
                   if ($dif_min> 5) {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($veh_enram_carga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Vehículo enrampado<br>'.date_format($veh_enram_carga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }
                   else{
                   echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($veh_enram_carga, "H:i:s").'</span>';//si se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Vehículo enrampado<br>'.date_format($veh_enram_carga, "d-m-Y").'</span>';
                     echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                   echo '</li>';
                   }
                   break;
                 default://si no
                   echo '<li class="timeline__step">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Vehículo enrampado</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
            <!-- ############### INICIA CARGA DE VEHICULO #################### -->
            <?php
              switch ( $info_manufac_car_des[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $info_manufac_car_des[$i]["STATUS"] >= 3 )://si es mayor o igual a 3
                  $inicia_carga = date_create($info_manufac_car_des[$i]["INICIA"]);
                  $dif_min = (strtotime($info_manufac_car_des[$i]["FIN"])-strtotime($info_manufac_car_des[$i]["INICIA"]))/60;
                  $tiempo_ope = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"];
                  if (intval($dif_min) > intval($tiempo_ope)) {
                    echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($inicia_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Inicia carga<br>'.date_format($inicia_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }else {
                    echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($inicia_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Inicia carga<br>'.date_format($inicia_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }
                   break;
                 default://si no
                   echo '<li class="timeline__step">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Inicia carga</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
            <!-- ############### FINALIZA CARGA DE VEHICULO #################### -->
            <?php
              switch ( $info_manufac_car_des[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $info_manufac_car_des[$i]["STATUS"] >= 4 )://si es mayor o igual a 4
                  $fin_carga = date_create($info_manufac_car_des[$i]["FIN"]);
                  $dif_min = (strtotime($info_manufac_car_des[$i]["FIN"])-strtotime($info_manufac_car_des[$i]["INICIA"]))/60;
                  $tiempo_ope = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"];
                  if ($dif_min > $tiempo_ope) {
                    echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($fin_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Finaliza carga<br>'.date_format($fin_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }else {
                    echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($fin_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Finaliza carga<br>'.date_format($fin_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }

                   break;
                 default://si no
                   echo '<li class="timeline__step">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Finaliza carga</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
            <!-- ############### DESPACHO DE VEHICULO CARGA #################### -->
            <?php
              switch ( $info_manufac_car_des[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $info_manufac_car_des[$i]["STATUS"] >= 5 )://si es mayor o igual a 5
                   $des_veh_carga = date_create($info_manufac_car_des[$i]["DESPACHO"]);
                   $dif_min = (strtotime($info_manufac_car_des[$i]["FIN"])-strtotime($info_manufac_car_des[$i]["INICIA"]))/60;
                   if (!empty($des_veh_carga)) {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($des_veh_carga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Vehículo despachado<br>'.date_format($des_veh_carga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }else {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($des_veh_carga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Vehículo despachado<br>'.date_format($des_veh_carga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }
                   break;
                 default://si no
                   echo '<li class="timeline__step">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Vehículo despachado</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
              </ol>
              <br> <br> <br>
              <!-- TERMINA LINEA DE TIEMPO PARA EL ESTADO DE CARGAS -->
            <?php } } ?>

          </td><!-- TERMINA TD CARGAS PROCESO -->
        </tr><!-- TERMINA TR CARGAS PROCESO -->
      </tbody>
    </table>
    </div>
    <!-- ################################ TERMINA TABLA CARGA EN PROCESO ################################  -->

      </div>


        <!-- TAB PARA CARGAS FINALIZADO EN TIEMPO -->
          <div id="link_tab_car_fin" class="tab-pane fade">

          <h5 class="text-green text-center"><i class="fa fa-check-square-o"></i> CARGAS FINALIZADAS EN TIEMPO <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
            <!-- INICIA TABLA PARA CARGAS FINALIZADAS EN TIEMPO -->
            <div class="table-responsive">
              <table id="tabla_manufac_car_tiem" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">CLIENTE</th>
                  <th class="small">LLEGADA</th>
                  <th class="small">DESPACHO</th>
                  <th class="small">TIEMPO</th>
                  <th class="small">STATUS</th>
                  <th class="small">REGIMEN</th>
                  <th class="small">OBS.</th>
                  <th class="small">T/VEHÍCULO</th>
                  <th class="small">PLACAS</th>
                  <th class="small">UME</th>
                  <th class="small">CANTIDAD</th>
                  <th class="small">ALMACENISTA</th>
                  <th class="small">DETALLES VEHICULO</th>
                  <th class="small">GALERIA</th>
                </tr>
              </thead>
              <tbody>
              <?php
                for ($i=0; $i <count($info_manufac_car_des) ; $i++) {
                //--  --//
                switch (true) {
                case ( $info_manufac_car_des[$i]["LLEGA"] == false ):
                   $calculo_tiempo = "<code>error no se registró tiempo</code>";
                  break;
                case ( $info_manufac_car_des[$i]["DESPACHO"] == true ):
                  $fechaFin_car_gen = $info_manufac_car_des[$i]["DESPACHO"] ;
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"]+25;// POR LOS ESTANDARES
                  break;
                default:
                  $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"]+25;// POR LOS ESTANDARES
                  break;
                }
                //--  --//
                if ( $info_manufac_car_des[$i]["TIPO"] == 1 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR  $info_manufac_car_des[$i]["STATUS"] == 9) && $calculo_minutos < $TIEMPO_OPERACION  && $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] <> 2905 ){ // Aqui esta a 90
                  #echo $info_manufac_car_des[$i]["SOLICITUD"]. " " .$TIEMPO_OPERACION;
                  $cargas_fin_tiempo[$i] = $i;
                  #echo $TIEMPO_OPERACION;
              ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-green">CUMPLIO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                  <td class="small">
                  <!-- INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  <?php
                  echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_des[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["SOLICITUD"]."'>
                  <span class='badge bg-teal btn'><i class='fa fa-truck'></i> ".$info_manufac_car_des[$i]["SOLICITUD"]."</span>
                  </a> ";
                  ?>
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                  <!-- TERMINA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->

                </tr>
              <?php  } elseif ($info_manufac_car_des[$i]["TIPO"] == 1 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9 )&& $calculo_minutos < 90  && $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] == 2905 ) {
                $cargas_fin_tiempo[$i] = $i;
                ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-green">CUMPLIO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                  <td class="small">
                  <!-- INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  <?php
                  echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_des[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["SOLICITUD"]."'>
                  <span class='badge bg-teal btn'><i class='fa fa-truck'></i> ".$info_manufac_car_des[$i]["SOLICITUD"]."</span>
                  </a> ";
                  ?>
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                  <!-- TERMINA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->

                </tr>
              <?php }} ?>
              </tbody>
              </table>
               </div>
            <!-- TERMINA TABLA PARA CARGAS FINALIZADAS EN TIEMPO -->
          </div>

<!--    INICIA TABLA DE CONTROL DE CARGAS PROGRAMADAS   -->
          <div id="link_tab_car_prog" class="tab-pane fade">
            <h5 class="text-green text-center"><i class="fa fa-check-square-o"></i> CARGAS PROGRAMADAS <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
            <div class="table-responsive">
              <table id="tabla_manufac_car_tiem" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small">ALMACEN</th>
                    <th class="small">CLIENTE</th>
                    <th class="small">FECHA APROX LLEGADA</th>
                    <!--<th class="small">DESPACHO</th>-->
                    <th class="small">TIEMPO APROX LLEGADA</th>
                    <th class="small">STATUS</th>
                    <th class="small">REGIMEN</th>
                    <th class="small">OBS.</th>
                    <th class="small">LINEA DE TRANSPORTE</th>
                    <th class="small">T/VEHÍCULO</th>
                    <th class="small">PLACAS</th>
                    <th class="small">CONDUCTOR</th>
                    <th class="small">IDENTIFICACION</th>
                    <th class="small">UME</th>
                    <th class="small">CANTIDAD</th>
                    <!--<th class="small">ALMACENISTA</th>-->
                    <!--<th class="small">DETALLES VEHICULO</th>-->
                  </tr>
                </thead>
              <tbody>
              <?php
                for ($i=0; $i <count($info_manufac_car_desProgramados) ; $i++) {
                  switch (true) {
                  case ( $info_manufac_car_desProgramados[$i]["LLEGA_APROX"] == false ):
                     $calculo_tiempo = "<code>error no se registró tiempo</code>";
                    break;
                  case ( $info_manufac_car_desProgramados[$i]["DESPACHO"] == true ):
                    $fechaFin_car_gen = $info_manufac_car_desProgramados[$i]["DESPACHO"] ;
                    $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_desProgramados[$i]["LLEGA_APROX"],$fechaFin_car_gen);
                    $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_desProgramados[$i]["DESPACHO"],$info_manufac_car_desProgramados[$i]["LLEGA"]);
                    $TIEMPO_OPERACION = $info_manufac_car_desProgramados[$i]["N_TIEMPO_OPERACION"]+25;// POR LOS ESTANDARES
                    break;
                  default:
                    $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                    $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_desProgramados[$i]["LLEGA_APROX"],$fechaFin_car_gen);
                    $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_desProgramados[$i]["DESPACHO"],$info_manufac_car_desProgramados[$i]["LLEGA"]);
                    $TIEMPO_OPERACION = $info_manufac_car_desProgramados[$i]["N_TIEMPO_OPERACION"]+25;// POR LOS ESTANDARES
                    break;
                  }
                if ( $info_manufac_car_desProgramados[$i]["TIPO"] == 1 && $info_manufac_car_desProgramados[$i]["IID_NUM_CLIENTE"]!=2905){
                     $contador_cargas +=1;
                  ?>
                  <tr>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["ALMACEN"] ?></td>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["RS"] ?></td>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["LLEGA_APROX"] ?></td>
                    <!--<td class="small"><?= $info_manufac_car_desProgramados[$i]["DESPACHO"] ?></td>-->
                    <td class="small"><?= $calculo_tiempo ?></td>
                    <td class="small"><span class="badge bg-green">CUMPLIO</span></td>
                    <td class="small">
                                    <?php if ($info_manufac_car_desProgramados[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["OBS"] ?></td>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["LINEA_TRANSPORTE"] ?></td>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["VEHICULO"] ?></td>
                    <td class="small"><span class="badge label-info"><?=$info_manufac_car_desProgramados[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_desProgramados[$i]["PLACAS2"]?></span></td>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["CHOFER"] ?></td>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["IDENTIFICACION"] ?></td>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["UME"] ?></td>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["CANTIDAD"] ?></td>
                    <!--<td class="small"><?= $info_manufac_car_desProgramados[$i]["ALMACENISTA_N"]." ".$info_manufac_car_desProgramados[$i]["ALMACENISTA_P"]." ".$info_manufac_car_desProgramados[$i]["ALMACENISTA_M"] ?></td>-->
                    <!-- <td class="small"><?= $info_manufac_car_desProgramados[$i]["PROYECTO"] ?></td>
                    <td class="small"><?= $info_manufac_car_desProgramados[$i]["FACTURA"] ?></td> -->
                    <!--<td class="small">INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA
                    <?php
                    #echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_desProgramados[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_desProgramados[$i]["SOLICITUD"]."'>
                    #<span class='badge bg-teal btn'><i class='fa fa-truck'></i> ".$info_manufac_car_desProgramados[$i]["SOLICITUD"]."</span>
                    #</a> ";
                    ?>
                    </td>-->
                  </tr>
                <?php }} ?>
              </tbody>
              </table>
            </div>
          </div>
<!--    TERMINA TABLA DE CONTROL DE CARGAS PROGRAMADAS   -->


        <!-- TAB PARA CARGAS FINALIZADO DESFASADO -->
          <div id="link_tab_car_fin_des" class="tab-pane fade">

          <h5 class="text-yellow text-center"><i class="fa fa-warning"></i> CARGAS FINALIZADAS DESFASADO <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
            <!-- INICIA TABLA PARA CARGAS FINALIZADAS DESFASADAS -->
            <div class="table-responsive">
              <table id="tabla_manufac_car_des" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">CLIENTE</th>
                  <th class="small">LLEGADA</th>
                  <th class="small">DESPACHO</th>
                  <th class="small">TIEMPO</th>
                  <th class="small">STATUS</th>
                  <th class="small">REGIMEN</th>
                  <th class="small">OBS.</th>
                  <th class="small">T/VEHÍCULO</th>
                  <th class="small">PLACAS</th>
                  <th class="small">UME</th>
                  <th class="small">CANTIDAD</th>
                  <th class="small">ALMACENISTA</th>
                  <th class="small">DETALLES VEHICULO</th>
                  <th class="small">GALERIA</th>

                </tr>
              </thead>
              <tbody>
              <?php
                for ($i=0; $i <count($info_manufac_car_des) ; $i++) {
                $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"]+25;
                switch (true) {
                case ( $info_manufac_car_des[$i]["LLEGA"] == false ):
                   $calculo_tiempo = "<code>error no se registró tiempo</code>";
                  break;
                case ( $info_manufac_car_des[$i]["DESPACHO"] == true ):
                  $fechaFin_car_gen = $info_manufac_car_des[$i]["DESPACHO"] ;
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  $calculo_minutos_por_ritmo = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["FIN"],$info_manufac_car_des[$i]["INICIA"]);
                  break;
                default:
                  $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  $calculo_minutos_por_ritmo = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["FIN"],$info_manufac_car_des[$i]["INICIA"]);
                  break;
                }
#                echo $info_manufac_car_des[$i]["SOLICITUD"]."$calculo_minutos_por_ritmo"." </br> "."$TIEMPO_OPERACION"."</br> ";
                //--  --//
                if ( $info_manufac_car_des[$i]["TIPO"] == 1 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9) && $calculo_minutos > $TIEMPO_OPERACION &&  $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] <> 2905
                && $calculo_minutos_por_ritmo < $TIEMPO_OPERACION-25){
                #  echo "uno";
                $cargas_fin_des[$i] = $i;
              ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-yellow">RETRASO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                  <td class="small">
                  <!-- INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  <?php
                  echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_des[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["SOLICITUD"]."'>
                  <span class='badge bg-teal btn'><i class='fa fa-truck'></i> ".$info_manufac_car_des[$i]["SOLICITUD"]."</span>
                  </a> ";
                  ?>
                  <!-- TERMINA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                </tr>
              <?php  } elseif ($info_manufac_car_des[$i]["TIPO"] == 1 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9  )&& $calculo_minutos > 90 &&  $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] == 2905
                && $calculo_minutos_por_ritmo < $TIEMPO_OPERACION-25){
                $cargas_fin_des[$i] = $i; ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-yellow">RETRASO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                  </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                  <td class="small">
                  <!-- INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  <?php
                  echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_des[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["SOLICITUD"]."'>
                  <span class='badge bg-teal btn'><i class='fa fa-truck'></i> ".$info_manufac_car_des[$i]["SOLICITUD"]."</span>
                  </a> ";
                  ?>
                  <!-- TERMINA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                </tr>
              <?PHP }} ?>
              </tbody>
              </table>
            </div>
            <!-- TERMINA TABLA PARA CARGAS FINALIZADAS DESFASADAS -->
          </div>
          <!--Desfasado por ritmo -->
          <div id="link_tab_car_fin_des_ritmo" class="tab-pane fade">

          <h5 class="text-yellow text-center"><i class="fa fa-warning"></i> CARGAS FINALIZADAS DESFASADO POR OPERACIÓN <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
            <!-- INICIA TABLA PARA CARGAS FINALIZADAS DESFASADAS -->
            <div class="table-responsive">
              <table id="tabla_manufac_car_des_rt" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">CLIENTE</th>
                  <th class="small">LLEGADA</th>
                  <th class="small">DESPACHO</th>
                  <th class="small">TIEMPO</th>
                  <th class="small">STATUS</th>
                  <th class="small">REGIMEN</th>
                  <th class="small">OBS.</th>
                  <th class="small">T/VEHÍCULO</th>
                  <th class="small">PLACAS</th>
                  <th class="small">UME</th>
                  <th class="small">CANTIDAD</th>
                  <th class="small">ALMACENISTA</th>
                  <th class="small">DETALLES VEHICULO</th>
                  <th class="small">GALERIA</th>

                </tr>
              </thead>
              <tbody>
              <?php
                for ($i=0; $i <count($info_manufac_car_des) ; $i++) {
                $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"]+25;
                switch (true) {
                case ( $info_manufac_car_des[$i]["LLEGA"] == false ):
                   $calculo_tiempo = "<code>error no se registró tiempo</code>";
                  break;
                case ( $info_manufac_car_des[$i]["DESPACHO"] == true ):
                  $fechaFin_car_gen = $info_manufac_car_des[$i]["DESPACHO"] ;
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  $calculo_minutos_por_ritmo = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["FIN"],$info_manufac_car_des[$i]["INICIA"]);
                //  echo $TIEMPO_OPERACION-25;
                  break;
                default:
                  $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  $calculo_minutos_por_ritmo = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["FIN"],$info_manufac_car_des[$i]["INICIA"]);
                  //echo $calculo_minutos_por_ritmo;
                //  echo $TIEMPO_OPERACION-25;
                  break;
                }
                #echo $info_manufac_car_des[$i]["SOLICITUD"]."$calculo_minutos_por_ritmo"." </br> "."$TIEMPO_OPERACION-25"."</br> ";
                //--  --//
                if ( $info_manufac_car_des[$i]["TIPO"] == 1 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9) && $calculo_minutos > $TIEMPO_OPERACION &&  $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] <> 2905
                && $calculo_minutos_por_ritmo > $TIEMPO_OPERACION-25){
                $cargas_fin_des_ritmo[$i] = $i;
                #echo $calculo_minutos_por_ritmo;
              ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-yellow">RETRASO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                  <td class="small">
                  <!-- INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  <?php
                  echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_des[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["SOLICITUD"]."'>
                  <span class='badge bg-teal btn'><i class='fa fa-truck'></i> ".$info_manufac_car_des[$i]["SOLICITUD"]."</span>
                  </a> ";
                  ?>
                  <!-- TERMINA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                </tr>
              <?php  } elseif ($info_manufac_car_des[$i]["TIPO"] == 1 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9  )&& $calculo_minutos > 90 &&  $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] == 2905
                && $calculo_minutos_por_ritmo > $TIEMPO_OPERACION-25) {
                $cargas_fin_des_ritmo[$i] = $i; ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-yellow">RETRASO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                  </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                  <td class="small">
                  <!-- INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  <?php
                  echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_des[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["SOLICITUD"]."'>
                  <span class='badge bg-teal btn'><i class='fa fa-truck'></i> ".$info_manufac_car_des[$i]["SOLICITUD"]."</span>
                  </a> ";
                  ?>
                  <!-- TERMINA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                </tr>
              <?PHP }} ?>
              </tbody>
              </table>
            </div>
            <!-- TERMINA TABLA PARA CARGAS FINALIZADAS DESFASADAS -->
          </div>
        <!-- TAB PARA CARGAS CANCELADAS -->
          <div id="link_tab_car_can" class="tab-pane fade">

          <h5 class="text-red text-center"><i class="fa fa-ban"></i> CARGAS CANCELADAS <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
            <!-- INICIA TABLA PARA CARGAS CANCELADAS -->
            <div class="table-responsive">
              <table id="tabla_manufac_car_can" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">CLIENTE</th>
                  <th class="small">LLEGADA</th>
                  <th class="small">DESPACHO</th>
                  <th class="small">TIEMPO</th>
                  <th class="small">STATUS</th>
                  <th class="small">REGIMEN</th>
                  <th class="small">OBS.</th>
                  <th class="small">T/VEHÍCULO</th>
                  <th class="small">PLACAS</th>
                  <th class="small">UME</th>
                  <th class="small">CANTIDAD</th>
                  <th class="small">ALMACENISTA</th>
                  <th class="small">DETALLES VEHICULO</th>
                  <th class="small">GALERIA</th>
                </tr>
              </thead>
              <tbody>
              <?php
                for ($i=0; $i <count($info_manufac_car_des) ; $i++) {
                $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"];
                switch (true) {
                case ( $info_manufac_car_des[$i]["LLEGA"] == false ):
                   $calculo_tiempo = "<code>error no se registró tiempo</code>";
                  break;
                case ( $info_manufac_car_des[$i]["DESPACHO"] == true ):
                  $fechaFin_car_gen = $info_manufac_car_des[$i]["DESPACHO"] ;
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  break;
                default:
                  $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  break;
                }
                //--  --//
                if ( $info_manufac_car_des[$i]["TIPO"] == 1 && $info_manufac_car_des[$i]["STATUS"] == 6 ){
                $cargas_canceladas[$i] = $i;
              ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-red">CANCELADO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS_CAN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <td class="small">
                  <!-- INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  <?php
                    echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_des[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["SOLICITUD"]."'>
                    <span class='badge bg-teal btn'><i class='fa fa-truck'></i> ".$info_manufac_car_des[$i]["SOLICITUD"]."</span>
                    </a> ";
                  ?>
                  <!-- TERMINA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                </tr>
              <?php  } } ?>
              </tbody>
              </table>
            </div>
            <!-- TERMINA TABLA PARA CARGAS CANCELADAS -->
          </div>

        </div><!-- ./tab-content -->
      </div><!-- ./nav-tabs-custom -->

    </div><!--/.box-body-->
  </div>
</section>
<section>
  <div class="box box-success">
    <div class="box-header with-border">
      <p class="box-card">
        <ul class="nav nav-tabs" id="myTab_manufac_descarga">
          <li class="active"><a id="tab_des_pro" data-toggle="tab" href="#link_tab_des_pro">EN PROCESO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_descargas_pro[]">0</span></a></li>
          <li><a id="tab_des_fin" data-toggle="tab" href="#link_tab_des_fin">FIN. EN TIEMPO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_fintiempo_des[]">0</span></a></li>
          <li><a id="tab_des_fin_des" data-toggle="tab" href="#link_tab_des_fin_des">FIN. DESFASADO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_desfasados_des[]">0</span></a></li>
          <li><a id="tab_des_fin_des" data-toggle="tab" href="#link_tab_des_fin_des_ritmo">FIN. DESFASADO POR RITMO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_desfasados_des_ritmo[]">0</span></a></li>
          <li><a id="tab_des_can" data-toggle="tab" href="#link_tab_des_can">CANCELADO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_can_des[]">0</span></a></li>
          <li><a id="tab_des_prog" data-toggle="tab" href="#link_tab_descar_prog">PROGRAMADOS&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_descargas_programadas[]">0</span></a></li>
        </ul>
      </p>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

      <div class="nav-tabs-custom"><!-- nav-tabs-custom -->
        <div class="tab-content"><!-- tab-content -->
        <!-- TAB PARA DESCARGAS EN PROCESO -->
          <div id="link_tab_des_pro" class="tab-pane fade in active">

          <h5 class="text-blue text-center"></i> DESCARGAS EN PROCESO <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
<!-- ################################ INICIA TABLA DESCARGA EN PROCESO ################################  -->
    <div class="table-responsive" align="center"><!-- table-responsive -->
    <table id="tabla_proceso_descarga" class="display compact" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th class="small"></th>
        </tr>
      </thead>
      <tbody>
      <?php
      for ($i=0; $i <count($info_manufac_car_des) ; $i++) {
      if ( $info_manufac_car_des[$i]["STATUS"] <= 4 && $info_manufac_car_des[$i]["TIPO"] == 2 ){
      $descargas_proceso[$i]= $i;

      //----------------------
      if ($info_manufac_car_des[$i]["STATUS"]==1){
        // -- //
        switch (true) {
            case ( $info_manufac_car_des[$i]["REGISTRADO"] == false ):
             $calculo_tiempo = "<code>error no se registró tiempo</code>";
            break;
          default:
            $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
            $calculo_tiempo = $obj_info_gral_manufactura->calculo_tiempo($info_manufac_car_des[$i]["REGISTRADO"],$fechaFin_car_gen);
            $calculo_minutos = $obj_info_gral_manufactura->dif_minutos($fechaFin_car_gen,$info_manufac_car_des[$i]["REGISTRADO"]);
            break;
          }
        // -- //
      }else{
        //--  --//
        switch (true) {
        case ( $info_manufac_car_des[$i]["LLEGA"] == false ):
           $calculo_tiempo = "<code>error no se registró tiempo</code>";
          break;
        case ( $info_manufac_car_des[$i]["DESPACHO"] == true ):
          $fechaFin_car_gen = $info_manufac_car_des[$i]["DESPACHO"] ;
          $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
          $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($fechaFin_car_gen,$info_manufac_car_des[$i]["LLEGA"]);
          break;
        default:
          $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
          $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
          $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($fechaFin_car_gen,$info_manufac_car_des[$i]["LLEGA"]);
          break;
        }
        //--  --//
      }
      //----------------------

      ?>
        <tr><!-- INICIA TR DESCARGAS EN PROCESO -->
          <td colspan="7"><!-- INICIA TD DESCARGAS EN PROCESO -->

            <!-- INICIA LINEA DE TIEMPO PARA EL ESTADO DE DESCARGAS -->
            <span class="badge bg-teal"><i class="fa fa-industry"></i> Almacen: <cite><?= $info_manufac_car_des[$i]["ALMACEN"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-briefcase"></i> Cliente: <cite><?= $info_manufac_car_des[$i]["RS"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-truck"></i> Vehículo: <cite><?= $info_manufac_car_des[$i]["VEHICULO"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-support"></i> Placas: <cite>(<?= $info_manufac_car_des[$i]["PLACAS1"] ?>) (<?= $info_manufac_car_des[$i]["PLACAS2"] ?>)</cite></span>
            <span class="badge bg-teal"><i class="fa fa-arrows-h"></i> Anden: <cite><?= $info_manufac_car_des[$i]["ANDEN"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-clock-o"></i> Tiempo transcurrido <cite><?= $calculo_tiempo ?></cite></span>
            <a class="fancybox fancybox.iframe" href="manufactura_det_retarr.php?almacen=<?= $info_manufac_car_des[$i]["ALMACEN_ID"] ?>&arribo=<?= $info_manufac_car_des[$i]["SOLICITUD"] ?>">
            <span class="badge bg-teal btn"><i class="fa fa-folder-open"></i> Detalles Vehiculo: <cite><?= $info_manufac_car_des[$i]["SOLICITUD"] ?></cite></span></a>

<?php
            echo "<a class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'>
            <span class='badge bg-teal btn'>GALERIA</span></button></a>";
?>

            </a>
            <br> <br> <br>

          <!-- ############### INICIA REGISTRO DE VEHICULO #################### -->
            <ol class="timeline-line">
              <?php
                switch ( $info_manufac_car_des[$i]["STATUS"] ) {//evaluamos el status de la descarga
                   case ( $info_manufac_car_des[$i]["STATUS"] >= 1 )://si es mayor o igual a 1
                   $reg_veh_descarga = date_create($info_manufac_car_des[$i]["REGISTRADO"]);

                   $dif_min = (strtotime($info_manufac_car_des[$i]["LLEGA"])-strtotime($info_manufac_car_des[$i]["REGISTRADO"]))/60;
                   //echo $dif_min;
                   if ($dif_min >= 10) {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($reg_veh_descarga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Registro de vehículo<br>'.date_format($reg_veh_descarga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_red_fin"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   } else {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($reg_veh_descarga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Registro de vehículo<br>'.date_format($reg_veh_descarga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }
                     break;
                   default://si no
                     echo '<li class="timeline__step">';//se pinta de color blanco el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Registro de vehículo</span>';
                       echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                     echo '</li>';
                 }
              ?>
              <!-- ############### INICIA LLEGADA DE VEHICULO #################### -->
              <?php
                switch ( $info_manufac_car_des[$i]["STATUS"] ) {//evaluamos el status de la descarga
                   case ( $info_manufac_car_des[$i]["STATUS"] >= 2 )://si es mayor o igual a 2
                   $veh_enram_descarga  = date_create($info_manufac_car_des[$i]["LLEGA"]);
                     $dif_min = (strtotime($info_manufac_car_des[$i]["INICIA"])-strtotime($info_manufac_car_des[$i]["LLEGA"]))/60;
                     if ($dif_min > 5) {
                       echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                         echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                         echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                            echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($veh_enram_descarga, "H:i:s").'</span>';//si se muestra la hora
                         echo '</label>';
                         echo '<span class="timeline__step-title"> Vehículo enrampado<br>'.date_format($veh_enram_descarga, "d-m-Y").'</span>';
                         echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                       echo '</li>';
                     }else {
                       echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                         echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                         echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                            echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($veh_enram_descarga, "H:i:s").'</span>';//si se muestra la hora
                         echo '</label>';
                         echo '<span class="timeline__step-title"> Vehículo enrampado<br>'.date_format($veh_enram_descarga, "d-m-Y").'</span>';
                         echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                       echo '</li>';
                     }

                     break;
                   default://si no
                     echo '<li class="timeline__step">';//se pinta de color blanco el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Vehículo enrampado</span>';
                       echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                     echo '</li>';
                 }
              ?>
              <!-- ############### INICIA DESCARGA DE VEHICULO #################### -->
              <?php
                switch ( $info_manufac_car_des[$i]["STATUS"] ) {//evaluamos el status de la descarga
                   case ( $info_manufac_car_des[$i]["STATUS"] >= 3 )://si es mayor o igual a 3
                   $inicia_descarga = date_create($info_manufac_car_des[$i]["INICIA"]) ;
                   $dif_min = (strtotime($info_manufac_car_des[$i]["FIN"])-strtotime($info_manufac_car_des[$i]["INICIA"]))/60;
                   $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"];
                   if ($dif_min > $TIEMPO_OPERACION) {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($inicia_descarga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Inicia descarga<br>'.date_format($inicia_descarga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }else {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($inicia_descarga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Inicia descarga<br>'.date_format($inicia_descarga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                    }
                     break;
                   default://si no
                     echo '<li class="timeline__step">';//se pinta de color blanco el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Inicia descarga</span>';
                       echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                     echo '</li>';
                 }
              ?>
              <!-- ############### FINALIZA DESCARGA DE VEHICULO #################### -->
              <?php
                switch ( $info_manufac_car_des[$i]["STATUS"] ) {//evaluamos el status de la descarga
                   case ( $info_manufac_car_des[$i]["STATUS"] >= 4 )://si es mayor o igual a 4
                   $fin_descarga = date_create($info_manufac_car_des[$i]["FIN"]) ;
                   $dif_min = (strtotime($info_manufac_car_des[$i]["FIN"])-strtotime($info_manufac_car_des[$i]["INICIA"]))/60;
                   $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"];
                   if ($dif_min > $TIEMPO_OPERACION) {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($fin_descarga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Finaliza descarga<br>'.date_format($fin_descarga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }else {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($fin_descarga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Finaliza descarga<br>'.date_format($fin_descarga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }

                     break;
                   default://si no
                     echo '<li class="timeline__step">';//se pinta de color blanco el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Finaliza descarga</span>';
                       echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                     echo '</li>';
                 }
              ?>
              <!-- ############### DESPACHO DE VEHICULO DESCARGA #################### -->
              <?php
                switch ( $info_manufac_car_des[$i]["STATUS"] ) {//evaluamos el status de la descarga
                   case ( $info_manufac_car_des[$i]["STATUS"] >= 5 )://si es mayor o igual a 5
                   $des_veh_descarga = date_create($info_manufac_car_des[$i]["DESPACHO"]);

                   if (!empty($des_veh_descarga)) {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($des_veh_descarga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Vehículo despachado<br>'.date_format($des_veh_descarga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }else {
                     echo '<li class="timeline__step done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($des_veh_descarga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Vehículo despachado<br>'.date_format($des_veh_descarga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }

                     break;
                   default://si no
                     echo '<li class="timeline__step">';//se pinta de color blanco el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Vehículo despachado</span>';
                       echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                     echo '</li>';
                 }
              ?>
            </ol>
            <br> <br> <br>
              <!-- TERMINA LINEA DE TIEMPO PARA EL ESTADO DE DESCARGAS -->

            <?php } } ?>

          </td><!-- TERMINA TD DESCARGAS EN PROCESO -->
        </tr><!-- TERMINA TR DESCARGAS EN PROCESO -->
      </tbody>
    </table>
    </div>
    <!-- ################################ TERMINA TABLA DESCARGA EN PROCESO ################################  -->
    </div>

        <!-- TAB PARA DESCARGAS FINALIZADO EN TIEMPO -->
          <div id="link_tab_des_fin" class="tab-pane fade">

          <h5 class="text-green text-center"><i class="fa fa-check-square-o"></i> DESCARGAS FINALIZADAS EN TIEMPO <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>

            <!-- INICIA TABLA PARA DESCARGAS FINALIZADAS EN TIEMPO -->
            <div class="table-responsive">
              <table id="tabla_manufac_car_tiem2" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">CLIENTE</th>
                  <th class="small">LLEGADA</th>
                  <th class="small">DESPACHO</th>
                  <th class="small">TIEMPO</th>
                  <th class="small">STATUS</th>
                  <th class="small">REGIMEN</th>
                  <th class="small">OBS.</th>
                  <th class="small">T/VEHÍCULO</th>
                  <th class="small">PLACAS</th>
                  <th class="small">UME</th>
                  <th class="small">CANTIDAD</th>
                  <th class="small">ALMACENISTA</th>
                  <th class="small">DETALLES VEHÍCULO</th>
                  <th class="small">GALERIA</th>
                </tr>
              </thead>
              <tbody>
              <?php
                for ($i=0; $i <count($info_manufac_car_des) ; $i++) {
                $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"]+25;
                switch (true) {
                case ( $info_manufac_car_des[$i]["LLEGA"] == false ):
                   $calculo_tiempo = "<code>error no se registró tiempo</code>";
                  break;
                case ( $info_manufac_car_des[$i]["DESPACHO"] == true ):
                  $fechaFin_car_gen = $info_manufac_car_des[$i]["DESPACHO"] ;
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  break;
                default:
                  $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  break;
                }
                //--  --//
                if ( $info_manufac_car_des[$i]["TIPO"] == 2 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9) && $calculo_minutos < $TIEMPO_OPERACION && $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] <> 2905){ //> 90
                  $descargas_fin_tiempo[$i] = $i;
              ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-green">CUMPLIO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                  <td class="small">
                  <a class="fancybox fancybox.iframe" href="manufactura_det_retarr.php?almacen=<?= $info_manufac_car_des[$i]["ALMACEN_ID"] ?>&arribo=<?= $info_manufac_car_des[$i]["SOLICITUD"] ?>">
                  <span class="badge bg-teal btn"><i class="fa fa-truck"></i> <?= $info_manufac_car_des[$i]["SOLICITUD"] ?></span>
                  </a>
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                </tr>
              <?php  } elseif( $info_manufac_car_des[$i]["TIPO"] == 2 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9) && $calculo_minutos < 90 && $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] == 2905) {
                $descargas_fin_tiempo[$i] = $i; ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-green">CUMPLIO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                  <td class="small">
                  <a class="fancybox fancybox.iframe" href="manufactura_det_retarr.php?almacen=<?= $info_manufac_car_des[$i]["ALMACEN_ID"] ?>&arribo=<?= $info_manufac_car_des[$i]["SOLICITUD"] ?>">
                  <span class="badge bg-teal btn"><i class="fa fa-truck"></i> <?= $info_manufac_car_des[$i]["SOLICITUD"] ?></span>
                  </a>
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                </tr>
              <?PHP } } ?>
              </tbody>
              </table>
            </div>
            <!-- TERMINA TABLA PARA DESCARGAS FINALIZADAS EN TIEMPO -->
          </div>

          <!--DESCARGAS EN PROCESO -- >


        <!-- TAB PARA DESCARGAS FINALIZADO DESFASADO -->
          <div id="link_tab_des_fin_des" class="tab-pane fade">

          <h5 class="text-yellow text-center"><i class="fa fa-warning"></i> DESCARGAS FINALIZADAS DESFASADO <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
            <!-- INICIA TABLA PARA DESCARGAS FINALIZADAS DESFASADAS -->
            <div class="table-responsive">
              <table id="tabla_manufac_car_des2" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">CLIENTE</th>
                  <th class="small">LLEGADA</th>
                  <th class="small">DESPACHO</th>
                  <th class="small">TIEMPO</th>
                  <th class="small">STATUS</th>
                  <th class="small">REGIMEN</th>
                  <th class="small">OBS.</th>
                  <th class="small">T/VEHÍCULO</th>
                  <th class="small">PLACAS</th>
                  <th class="small">UME</th>
                  <th class="small">CANTIDAD</th>
                  <th class="small">ALMACENISTA</th>
                  <th class="small">DETALLES VEHÍCULO</th>
                  <th class="small">GALERIA</th>
                </tr>
              </thead>
              <tbody>
              <?php
                for ($i=0; $i <count($info_manufac_car_des) ; $i++) {
                $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"] + 25;
                switch (true) {
                case ( $info_manufac_car_des[$i]["LLEGA"] == false ):
                   $calculo_tiempo = "<code>error no se registró tiempo</code>";
                  break;
                case ( $info_manufac_car_des[$i]["DESPACHO"] == true ):
                  $fechaFin_car_gen = $info_manufac_car_des[$i]["DESPACHO"] ;
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  $calculo_minutos_ritmo = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["FIN"],$info_manufac_car_des[$i]["INICIA"]);
                  break;
                default:
                  $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  $calculo_minutos_ritmo = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["FIN"],$info_manufac_car_des[$i]["INICIA"]);
                  break;
                }
                //--  --//
                if ( $info_manufac_car_des[$i]["TIPO"] == 2 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9) && $calculo_minutos > $TIEMPO_OPERACION && $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] <> 2905 && $calculo_minutos_ritmo < $TIEMPO_OPERACION-25){ //90
                $descargas_fin_des[$i] = $i;
              ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-yellow">RETRASO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                  <td class="small">
                    <a class="fancybox fancybox.iframe" href="manufactura_det_retarr.php?almacen=<?= $info_manufac_car_des[$i]["ALMACEN_ID"] ?>&arribo=<?= $info_manufac_car_des[$i]["SOLICITUD"] ?>">
                    <span class="badge bg-teal btn"><i class="fa fa-truck"></i> <?= $info_manufac_car_des[$i]["SOLICITUD"] ?></span>
                    </a>
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                </tr>
              <?php  } elseif ( $info_manufac_car_des[$i]["TIPO"] == 2 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9) && $calculo_minutos > 90 && $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] == 2905
                && $calculo_minutos_ritmo < $TIEMPO_OPERACION-25) {
                $descargas_fin_des[$i] = $i;?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-yellow">RETRASO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                  <td class="small">
                    <a class="fancybox fancybox.iframe" href="manufactura_det_retarr.php?almacen=<?= $info_manufac_car_des[$i]["ALMACEN_ID"] ?>&arribo=<?= $info_manufac_car_des[$i]["SOLICITUD"] ?>">
                    <span class="badge bg-teal btn"><i class="fa fa-truck"></i> <?= $info_manufac_car_des[$i]["SOLICITUD"] ?></span>
                    </a>
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                </tr>
              <?PHP } } ?>
              </tbody>
              </table>
            </div>
            <!-- TERMINA TABLA PARA DESCARGAS FINALIZADAS DESFASADAS -->
          </div>
          <!-- TAB PARA DESCARGAS FINALIZADO DESFASADO POR RITMO -->
            <div id="link_tab_des_fin_des_ritmo" class="tab-pane fade">

            <h5 class="text-yellow text-center"><i class="fa fa-warning"></i> DESCARGAS FINALIZADAS DESFASADO POR RITMO<?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
              <!-- INICIA TABLA PARA DESCARGAS FINALIZADAS DESFASADAS -->
              <div class="table-responsive">
                <table id="tabla_manufac_car_des2" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small">ALMACEN</th>
                    <th class="small">CLIENTE</th>
                    <th class="small">LLEGADA</th>
                    <th class="small">DESPACHO</th>
                    <th class="small">TIEMPO</th>
                    <th class="small">STATUS</th>
                    <th class="small">REGIMEN</th>
                    <th class="small">OBS.</th>
                    <th class="small">T/VEHÍCULO</th>
                    <th class="small">PLACAS</th>
                    <th class="small">UME</th>
                    <th class="small">CANTIDAD</th>
                    <th class="small">ALMACENISTA</th>
                    <th class="small">DETALLES VEHÍCULO</th>
                    <th class="small">GALERIA</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  for ($i=0; $i <count($info_manufac_car_des) ; $i++) {
                  $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"] + 25;
                  switch (true) {
                  case ( $info_manufac_car_des[$i]["LLEGA"] == false ):
                     $calculo_tiempo = "<code>error no se registró tiempo</code>";
                    break;
                  case ( $info_manufac_car_des[$i]["DESPACHO"] == true ):
                    $fechaFin_car_gen = $info_manufac_car_des[$i]["DESPACHO"] ;
                    $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                    $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                    $calculo_minutos_ritmo = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["FIN"],$info_manufac_car_des[$i]["INICIA"]);
                    break;
                  default:
                    $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                    $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                    $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                    $calculo_minutos_ritmo = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["FIN"],$info_manufac_car_des[$i]["INICIA"]);
                    break;
                  }
                  //--  --//
                  if ( $info_manufac_car_des[$i]["TIPO"] == 2 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9) && $calculo_minutos > $TIEMPO_OPERACION && $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] <> 2905 && $calculo_minutos_ritmo > $TIEMPO_OPERACION-25){ //90
                  $descargas_fin_des_ritmo[$i] = $i;
                ?>
                  <tr>
                    <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                    <td class="small"><?= $calculo_tiempo ?></td>
                    <td class="small"><span class="badge bg-yellow">RETRASO</span></td>
                    <td class="small">
                                      <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                        echo "NACIONAL";
                                      }else {
                                        echo "FISCAL";
                                      } ?>
                                      </td>
                    <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                    <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                    <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                    <td class="small">
                      <a class="fancybox fancybox.iframe" href="manufactura_det_retarr.php?almacen=<?= $info_manufac_car_des[$i]["ALMACEN_ID"] ?>&arribo=<?= $info_manufac_car_des[$i]["SOLICITUD"] ?>">
                      <span class="badge bg-teal btn"><i class="fa fa-truck"></i> <?= $info_manufac_car_des[$i]["SOLICITUD"] ?></span>
                      </a>
                    </td>
                    <?php
                    echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                     ?>
                  </tr>
                <?php  } elseif ( $info_manufac_car_des[$i]["TIPO"] == 2 && ($info_manufac_car_des[$i]["STATUS"] == 5 OR $info_manufac_car_des[$i]["STATUS"] == 9) && $calculo_minutos > 90 && $info_manufac_car_des[$i]["IID_NUM_CLIENTE"] == 2905
                  && $calculo_minutos_ritmo > $TIEMPO_OPERACION-25){ //90
                  $descargas_fin_des_ritmo[$i] = $i;?>
                  <tr>
                    <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                    <td class="small"><?= $calculo_tiempo ?></td>
                    <td class="small"><span class="badge bg-yellow">RETRASO</span></td>
                    <td class="small">
                                      <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                        echo "NACIONAL";
                                      }else {
                                        echo "FISCAL";
                                      } ?>
                                      </td>
                    <td class="small"><?= $info_manufac_car_des[$i]["OBS"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                    <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                    <!-- <td class="small"><?= $info_manufac_car_des[$i]["PROYECTO"] ?></td>
                    <td class="small"><?= $info_manufac_car_des[$i]["FACTURA"] ?></td> -->
                    <td class="small">
                      <a class="fancybox fancybox.iframe" href="manufactura_det_retarr.php?almacen=<?= $info_manufac_car_des[$i]["ALMACEN_ID"] ?>&arribo=<?= $info_manufac_car_des[$i]["SOLICITUD"] ?>">
                      <span class="badge bg-teal btn"><i class="fa fa-truck"></i> <?= $info_manufac_car_des[$i]["SOLICITUD"] ?></span>
                      </a>
                    </td>
                    <?php
                    echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["SOLICITUD"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                     ?>
                  </tr>
                <?PHP } } ?>
                </tbody>
                </table>
              </div>
              <!-- TERMINA TABLA PARA DESCARGAS FINALIZADAS DESFASADAS -->
            </div>
        <!-- TAB PARA DESCARGAS CANCELADAS -->
          <div id="link_tab_des_can" class="tab-pane fade">

          <h5 class="text-red text-center"><i class="fa fa-ban"></i> DESCARGAS CANCELADAS <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
            <!-- INICIA TABLA PARA DESCARGAS CANCELADAS -->
            <div class="table-responsive">
              <table id="tabla_manufac_car_can2" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">CLIENTE</th>
                  <th class="small">LLEGADA</th>
                  <th class="small">DESPACHO</th>
                  <th class="small">TIEMPO</th>
                  <th class="small">STATUS</th>
                  <th class="small">REGIMEN</th>
                  <th class="small">OBS.</th>
                  <th class="small">T/VEHÍCULO</th>
                  <th class="small">PLACAS</th>
                  <th class="small">UME</th>
                  <th class="small">CANTIDAD</th>
                  <th class="small">ALMACENISTA</th>
                  <th class="small">DETALLES VEHÍCULO</th>
                  <th class="small">GALERIA</th>
                </tr>
              </thead>
              <tbody>
              <?php
                for ($i=0; $i <count($info_manufac_car_des) ; $i++) {
                $TIEMPO_OPERACION = $info_manufac_car_des[$i]["N_TIEMPO_OPERACION"];
                switch (true) {
                case ( $info_manufac_car_des[$i]["LLEGA"] == false ):
                   $calculo_tiempo = "<code>error no se registró tiempo</code>";
                  break;
                case ( $info_manufac_car_des[$i]["DESPACHO"] == true ):
                  $fechaFin_car_gen = $info_manufac_car_des[$i]["DESPACHO"] ;
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  break;
                default:
                  $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_des[$i]["LLEGA"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_des[$i]["DESPACHO"],$info_manufac_car_des[$i]["LLEGA"]);
                  break;
                }
                //--  --//
                if ( $info_manufac_car_des[$i]["TIPO"] == 2 && $info_manufac_car_des[$i]["STATUS"] == 6 ){
                $descargas_canceladas[$i] = $i;
              ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["LLEGA"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["DESPACHO"] ?></td>
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-red">CANCELADO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_des[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                  </td>
                  <td class="small"><?= $info_manufac_car_des[$i]["OBS_CAN"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_des[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["CANTIDAD"] ?></td>
                  <td class="small"><?= $info_manufac_car_des[$i]["ALMACENISTA_N"]." ".$info_manufac_car_des[$i]["ALMACENISTA_P"]." ".$info_manufac_car_des[$i]["ALMACENISTA_M"] ?></td>
                  <td class="small">
                    <a class="fancybox fancybox.iframe" href="manufactura_det_retarr.php?almacen=<?= $info_manufac_car_des[$i]["ALMACEN_ID"] ?>&arribo=<?= $info_manufac_car_des[$i]["SOLICITUD"] ?>">
                    <span class="badge bg-teal btn"><i class="fa fa-truck"></i> <?= $info_manufac_car_des[$i]["SOLICITUD"] ?></span>
                    </a>
                  </td>
                  <?php
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_manufac_car_des[$i]["ARRIBO"].", ".$info_manufac_car_des[$i]["IID_NUM_CLIENTE"].", ".$info_manufac_car_des[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                   ?>
                </tr>
              <?php  } } ?>
              </tbody>
              </table>
            </div>
            <!-- TERMINA TABLA PARA DESCARGAS CANCELADAS -->
          </div>

          <!--    INICIA TABLA DE DESCARGAS PROGRAMADAS     -->
          <div id="link_tab_descar_prog" class="tab-pane fade">
          <h5 class="text-green text-center"><i class="fa fa-check-square-o"></i> DESCARGAS PROGRAMADAS <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
            <div class="table-responsive">
              <table id="tabla_manufac_car_tiem2" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">CLIENTE</th>
                  <th class="small">FECHA APROX LLEGADA</th>
                  <!--<th class="small">DESPACHO</th>-->
                  <th class="small">TIEMPO APROX LLEGADA</th>
                  <th class="small">STATUS</th>
                  <th class="small">REGIMEN</th>
                  <th class="small">OBS.</th>
                  <th class="small">LINEA DE TRANSPORTE</th>
                  <th class="small">T/VEHÍCULO</th>
                  <th class="small">PLACAS</th>
                  <th class="small">CONDUCTOR</th>
                  <th class="small">IDENTIFICACION</th>
                  <th class="small">UME</th>
                  <th class="small">CANTIDAD</th>
                  <!--<th class="small">ALMACENISTA</th>-->
                  <!--<th class="small">DETALLES VEHICULO</th>-->
                </tr>
              </thead>
              <tbody>
              <?php
              for ($i=0; $i <count($info_manufac_car_desProgramados) ; $i++) {
                switch (true) {
                case ( $info_manufac_car_desProgramados[$i]["LLEGA_APROX"] == false ):
                   $calculo_tiempo = "<code>error no se registró tiempo</code>";
                  break;
                case ( $info_manufac_car_desProgramados[$i]["DESPACHO"] == true ):
                  $fechaFin_car_gen = $info_manufac_car_desProgramados[$i]["DESPACHO"] ;
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_desProgramados[$i]["LLEGA_APROX"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_desProgramados[$i]["DESPACHO"],$info_manufac_car_desProgramados[$i]["LLEGA_APROX"]);
                  $TIEMPO_OPERACION = $info_manufac_car_desProgramados[$i]["N_TIEMPO_OPERACION"]+25;// POR LOS ESTANDARES
                  break;
                default:
                  $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                  $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($info_manufac_car_desProgramados[$i]["LLEGA_APROX"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($info_manufac_car_desProgramados[$i]["DESPACHO"],$info_manufac_car_desProgramados[$i]["LLEGA_APROX"]);
                  $TIEMPO_OPERACION = $info_manufac_car_desProgramados[$i]["N_TIEMPO_OPERACION"]+25;// POR LOS ESTANDARES
                  break;
                }
                if ( $info_manufac_car_desProgramados[$i]["TIPO"] == 2 && $info_manufac_car_desProgramados[$i]["IID_NUM_CLIENTE"]!=2509 ){
                  $contador_descargas+=1;
                  ?>
                <tr>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["RS"] ?></td>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["LLEGA_APROX"] ?></td>
                  <!--<td class="small"><?= $info_manufac_car_desProgramados[$i]["DESPACHO"] ?></td>-->
                  <td class="small"><?= $calculo_tiempo ?></td>
                  <td class="small"><span class="badge bg-green">CUMPLIO</span></td>
                  <td class="small">
                                    <?php if ($info_manufac_car_desProgramados[$i]["IID_REGIMEN"] == 1) {
                                      echo "NACIONAL";
                                    }else {
                                      echo "FISCAL";
                                    } ?>
                                    </td>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["OBS"] ?></td>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["LINEA_TRANSPORTE"] ?></td>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["VEHICULO"] ?></td>
                  <td class="small"><span class="badge label-info"><?=$info_manufac_car_desProgramados[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$info_manufac_car_desProgramados[$i]["PLACAS2"]?></span></td>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["CHOFER"] ?></td>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["IDENTIFICACION"] ?></td>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["UME"] ?></td>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["CANTIDAD"] ?></td>
                  <!--<td class="small"><?= $info_manufac_car_desProgramados[$i]["ALMACENISTA_N"]." ".$info_manufac_car_desProgramados[$i]["ALMACENISTA_P"]." ".$info_manufac_car_desProgramados[$i]["ALMACENISTA_M"] ?></td>-->
                  <!-- <td class="small"><?= $info_manufac_car_desProgramados[$i]["PROYECTO"] ?></td>
                  <td class="small"><?= $info_manufac_car_desProgramados[$i]["FACTURA"] ?></td> -->
                  <!--<td class="small">INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA
                  <?php
                  #echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$info_manufac_car_desProgramados[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_desProgramados[$i]["SOLICITUD"]."'>
                  #<span class='badge bg-teal btn'><i class='fa fa-truck'></i> ".$info_manufac_car_desProgramados[$i]["SOLICITUD"]."</span>
                  #</a> ";
                  ?>
                  </td>-->
                  <!-- TERMINA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
                </tr>
              <?php }} ?>
              </tbody>
              </table>
            </div>
          </div>
          <!--    TERMINA TABLA DE DESCARGAS PROGRAMADAS     -->

        </div><!-- ./tab-content -->
      </div><!-- ./nav-tabs-custom -->
    </div><!--/.box-body-->
  </div>
</section>
<!-- ######### TERMINA SECCION TABLA DESCARGAS INFO ########## -->
<!-- ############################ TERMINA SECCION PARA DESCARGAS ############################# -->
<!-- ############################ INICIA SECCION PARA OPERACIONES OTROS ############################# -->
<!-- ######### INICIA SECCION PARA LA GRAFICA DE OPERACIONES OTROS ########## -->
<!-- ######### TERMINA SECCION PARA LA GRAFICA DE OPERACIONES OTROS ########## -->


<!-- ######### INICIA SECCION PARA LA TABLA DE OPERACIONES OTROS ########## -->
<?php
  $valor_usuario = $_SESSION['usuario'];
  if ($valor_usuario == 'martin' || $valor_usuario == 'diego13'  || $valor_usuario == 'david' || $valor_usuario == 'mary_salas'): ?>
<section>
   <div class="box box-warning">
    <div class="box-header with-border">
      <p class="box-card">
        <ul class="nav nav-tabs" id="myTab_manufac_otros">
          <li class="active"><a id="tab_otr_pro" data-toggle="tab" href="#link_tab_otr_pro">EN PROCESO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_otros_proceso2[]">0</span></a></li>
          <li><a id="tab_otr_con" data-toggle="tab" href="#link_tab_otr_con">FIN EN TIEMPO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_otros_proceso3[]">0</span></a></li>
          <li><a id="tab_otr_conDES" data-toggle="tab" href="#link_tab_otr_condes">DESFASADOS&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_otros_proceso4[]">0</span></a></li>
          <li><a id="tab_otr_can" data-toggle="tab" href="#link_tab_otr_can">CANCELADO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_otros_proceso5[]">0</span></a></li>
          <li><a id="tab_otr_prog" data-toggle="tab" href="#link_tab_otr_prog">PROGRAMADOS&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_cross_programadas[]">0</span></a></li>
        </ul>
      </p>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

       <div class="nav-tabs-custom"><!-- nav-tabs-custom -->
        <div class="tab-content"><!-- tab-content -->
          <!-- TAB OTROS PENDIENTES -->
          <!-- TAB OTROS EN PROCESO -->
          <div id="link_tab_otr_pro" class="tab-pane fade in active">

          <h5 class="text-blue text-center"> CROSSDOCK EN PROCESO <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>

          <!-- ################################ INICIA TABLA CARGA EN PROCESO ################################  -->
          <div class="table-responsive" align="center"><!-- table-responsive -->
          <table id="tabla_proceso_carga" class="display compact" cellspacing="0" width="100%">
          <thead>
          <tr>
          <th class="small"></th>
          </tr>
          </thead>
          <tbody>
          <?php
          $otros_info = $obj_info_otros_manufac->otros_info($plaza_manufac,$dia_manufac,$fec_ini_per_manufac,$fec_fin_per_manufac,$select_manufac_global_plaza);
          for ($i=0; $i <count($otros_info) ; $i++) {
            if ( $otros_info[$i]["STATUS"] <= 6 ){
          //$cargas_proceso[$i]= $i;
            $otros_proceso[$i]=$i;
            //----------------------
            if ($otros_info[$i]["STATUS"]==1){
              // -- //
              switch (true) {
                  case ( $otros_info[$i]["F_INICIO"] == false ):
                   $calculo_tiempo = "<code>error no se registró tiempo</code>";
                  break;
                default:
                  $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                  $calculo_tiempo = $obj_info_gral_manufactura->calculo_tiempo($otros_info[$i]["F_INICIO"],$fechaFin_car_gen);
                  $calculo_minutos = $obj_info_gral_manufactura->dif_minutos($fechaFin_car_gen,$otros_info[$i]["F_INICIO"]);
                  break;
                }
              // -- //
            }else{
              //--  --//
              switch (true) {
              case ( $otros_info[$i]["F_LLEGADAR"] == false ):
                 $calculo_tiempo = "<code>error no se registró EL tiempo</code>";
                break;
              case ( $otros_info[$i]["TERMINO"] == true && $otros_info[$i]["STATUS"]==7 ):
                $fechaFin_car_gen = $otros_info[$i]["TERMINO"] ;
                $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($otros_info[$i]["F_LLEGADAR"],$fechaFin_car_gen);
                $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($fechaFin_car_gen,$otros_info[$i]["F_LLEGADAR"]);
                break;
              default:
                $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($otros_info[$i]["F_LLEGADAR"],$fechaFin_car_gen);
                $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($fechaFin_car_gen,$otros_info[$i]["F_LLEGADAR"]);
                break;
              }
              //--  --//
            }
            //----------------------

          ?>
          <tr><!-- INICIA TR CARGAS PROCESO -->
          <td colspan="12"><!-- INICIA TD CARGAS PROCESO -->

            <!-- INICIA LINEA DE TIEMPO PARA EL ESTADO DE CARGAS -->
            <span class="badge bg-teal"><i class="fa fa-industry"></i> Almacen: <cite><?= $otros_info[$i]["ALMACEN"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-briefcase"></i> Cliente: <cite><?= $otros_info[$i]["RS_CLI"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-truck"></i> Vehículo: <cite><?= $otros_info[$i]["VEHICULO"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-support"></i> Placas: <cite>(<?= $otros_info[$i]["PLACAS1"] ?>) (<?= $info_manufac_car_des[$i]["PLACAS2"] ?>)</cite></span>
            <span class="badge bg-teal"><i class="fa fa-arrows-h"></i> Anden: <cite><?= $otros_info[$i]["ANDEN"] ?></cite></span>
            <span class="badge bg-teal"><i class="fa fa-clock-o"></i> Tiempo transcurrido: <cite><?= $calculo_tiempo ?></cite></span>
            <!-- INICIA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
            <?php
              switch ($plaza_manufac) {
                case "BAJIO (ARGO)":
                  //echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$otros_info[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["ARRIBO"]."'><span class='badge bg-teal btn'><i class='fa fa-folder-open'></i> Detalles Retiro: <cite>".$info_manufac_car_des[$i]["ARRIBO"]."</cite></span></a>";
                  break;
                default:
                  //echo "<a class='fancybox fancybox.iframe' href='manufactura_det_retarr.php?almacen=".$otros_info[$i]["ALMACEN_ID"]."&retarr=".$info_manufac_car_des[$i]["SOLICITUD"]."'><span class='badge bg-teal btn'><i class='fa fa-folder-open'></i> Detalles Vehiculo: <cite>".$info_manufac_car_des[$i]["SOLICITUD"]."</cite></span></a>";
                  break;
              }

              echo "<a class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$otros_info[$i]["SOLICITUD"].", ".$otros_info[$i]["IID_NUM_CLIENTE"].", ".$otros_info[$i]["ID_PLAZA"].")'>
              <span class='badge bg-teal btn'>GALERIA</span></button></a>";
            ?>
            <!-- TERMINA CODE QUE MUESTRA EL ARRIBO O SOLICITUD DEPENDIENDO LA PLAZA -->
            <br> <br> <br>

            <!-- ############### INICIA REGISTRO DE VEHICULO #################### -->
            <ol class="timeline-line">
            <?php
              switch ( $otros_info[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $otros_info[$i]["STATUS"] >= 1 )://si es mayor o igual a 1
                 $reg_veh_carga = date_create($otros_info[$i]["F_INICIO"]);
                  $dif_min = (strtotime($otros_info[$i]["F_LLEGADAR"])-strtotime($otros_info[$i]["F_INICIO"]))/60;
                  if ($dif_min > 5) {
                    echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($reg_veh_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Registro de vehículo <br>'.date_format($reg_veh_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }
                  else {
                    echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($reg_veh_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Registro de vehículo <br>'.date_format($reg_veh_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }

                   break;
                 default://si no
                   echo '<li class="timeline__stepCross">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Registro de vehículo</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
            <!-- ############### INICIA LLEGADA DE VEHICULO #################### -->
            <?php
              switch ( $otros_info[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $otros_info[$i]["STATUS"] >= 2 )://si es mayor o igual a 2
                   $veh_enram_carga = date_create($otros_info[$i]["F_LLEGADAR"]);
                   $dif_min = (strtotime($otros_info[$i]["INICIA_DESCARGA"])-strtotime($otros_info[$i]["F_LLEGADAR"]))/60;
                   if ($dif_min> 5) {
                     echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($veh_enram_carga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Vehículo enrampado<br>'.date_format($veh_enram_carga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }
                   else{
                   echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($veh_enram_carga, "H:i:s").'</span>';//si se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Vehículo enrampado<br>'.date_format($veh_enram_carga, "d-m-Y").'</span>';
                     echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                   echo '</li>';
                   }
                   break;
                 default://si no
                   echo '<li class="timeline__stepCross">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Vehículo enrampado</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
            <!-- ############### INICIA CARGA DE VEHICULO #################### -->
            <?php
              switch ( $otros_info[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $otros_info[$i]["STATUS"] >= 3 )://si es mayor o igual a 3
                  $inicia_carga = date_create($otros_info[$i]["INICIA_DESCARGA"]);
                  $dif_min = (strtotime($otros_info[$i]["FINALIZA_DESCARGA"])-strtotime($otros_info[$i]["INICIA_DESCARGA"]))/60;
                  if ($dif_min > 60) {
                    echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($inicia_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Inicia Descarga<br>'.date_format($inicia_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }else {
                    echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($inicia_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Inicia Descarga<br>'.date_format($inicia_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }
                   break;
                 default://si no
                   echo '<li class="timeline__stepCross">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Inicia Descarga</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
            <!-- ############### FINALIZA CARGA DE VEHICULO #################### -->
            <?php
              switch ( $otros_info[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $otros_info[$i]["STATUS"] >= 4 )://si es mayor o igual a 4
                  $fin_carga = date_create($otros_info[$i]["FINALIZA_DESCARGA"]);
                  $dif_min = (strtotime($otros_info[$i]["INICIACARGA"])-strtotime($otros_info[$i]["FINALIZA_DESCARGA"]))/60;
                  if ($dif_min > 60) {
                    echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($fin_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Finaliza Descarga<br>'.date_format($fin_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }else {
                    echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                         echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($fin_carga, "H:i:s").'</span>';//si se muestra la hora
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Finaliza Descarga<br>'.date_format($fin_carga, "d-m-Y").'</span>';
                      echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                    echo '</li>';
                  }

                   break;
                 default://si no
                   echo '<li class="timeline__stepCross">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Finaliza Descarga</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
            <!-- ############### DESPACHO DE VEHICULO CARGA #################### -->
            <?php
              switch ( $otros_info[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $otros_info[$i]["STATUS"] >= 5 )://si es mayor o igual a 5
                   $des_veh_carga = date_create($otros_info[$i]["INICIACARGA"]);
                   $dif_min = (strtotime($otros_info[$i]["FINALIZA_CARGA"])-strtotime($otros_info[$i]["INICIACARGA"]))/60;
                   if ($dif_min < 60) {
                     echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($des_veh_carga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Inicia Carga<br>'.date_format($des_veh_carga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }else {
                     echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($des_veh_carga, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Inicia Carga<br>'.date_format($des_veh_carga, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }
                   break;
                 default://si no
                   echo '<li class="timeline__stepCross">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Inicia Carga</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
            <!---->
            <?php
              switch ( $otros_info[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $otros_info[$i]["STATUS"] >= 6 )://si es mayor o igual a 5
                   $des_veh_carga = date_create($otros_info[$i]["FINALIZA_CARGA"]);
                   $dif_min = (strtotime($otros_info[$i]["DESPACHO"])-strtotime($otros_info[$i]["FINALIZA_CARGA"]))/60;
                   if ($dif_min > 60) {
                     echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($des_veh_carga2, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Finaliza Carga<br>'.date_format($des_veh_carga2, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }else {
                     echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($des_veh_carga2, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Finaliza Carga<br>AQUI'.date_format($des_veh_carga2, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }
                   break;
                 default://si no
                   echo '<li class="timeline__stepCross">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Finaliza Carga</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
            <!---->
            <?php
              switch ( $otros_info[$i]["STATUS"] ) {//evaluamos el status de la carga
                 case ( $otros_info[$i]["STATUS"] >= 7 )://si es mayor o igual a 5
                   $des_veh_carga2 = date_create($otros_info[$i]["DESPACHO"]);
                   if (!isset($des_veh_carga2)) {
                     echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($des_veh_carga2, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Despachado<br>'.date_format($des_veh_carga2, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }else {
                     echo '<li class="timeline__stepCross done">';//se pinta de color azul el circulo
                       echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                       echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                          echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i>'.date_format($des_veh_carga2, "H:i:s").'</span>';//si se muestra la hora
                       echo '</label>';
                       echo '<span class="timeline__step-title"> Despachado<br>AQUI'.date_format($des_veh_carga2, "d-m-Y").'</span>';
                       echo '<i class="timeline__step-marker_red"><i class="fa fa-truck"></i></i>';//icono carro
                     echo '</li>';
                   }
                   break;
                 default://si no
                   echo '<li class="timeline__stepCross">';//se pinta de color blanco el circulo
                     echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                     echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa  fa-clock-o"></i></span>';//no se muestra la hora
                     echo '</label>';
                     echo '<span class="timeline__step-title"> Despachado</span>';
                     echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';//icono reloj
                   echo '</li>';
               }
            ?>
              </ol>
              <br> <br> <br>
              <!-- TERMINA LINEA DE TIEMPO PARA EL ESTADO DE CARGAS -->
            <?php } } ?>

          </td><!-- TERMINA TD CARGAS PROCESO -->
          </tr><!-- TERMINA TR CARGAS PROCESO -->
          </tbody>
          </table>
          </div>
          <!-- ################################ TERMINA TABLA CARGA EN PROCESO ################################  -->

          </div>

          <!-- TAB OTROS CONCLUIDOS -->
          <div id="link_tab_otr_con" class="tab-pane fade">

          <h5 class="text-green text-center"><i class="fa fa-check-square-o"></i> OPERACIONES CROSSDOCK CONCLUIDAS TIEMPO <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>

            <!-- INICIA TABLA PARA OTROS CONCLUIDOS -->
              <div class="table-responsive">
                <table id="tabla_manufac_car_can3" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small">ALMACEN</th>
                    <th class="small">SOL.</th>
                    <th class="small">CLIENTE</th>
                    <th class="small">INICIO</th>
                    <th class="small">TERMINO</th>
                    <th class="small">Tiempo</th>
                    <th class="small">STATUS</th>
                    <th class="small">OBS.</th>
                    <th class="small">T/VEHÍCULO</th>
                    <th class="small">PLACAS</th>
                    <th class="small">UME</th>
                    <th class="small">CANTIDAD</th>
                    <th class="small">MERCANCÍA</th>
                    <th class="small">ALMACENISTA</th>
                    <th class="small">GALERIA</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  for ($i=0; $i <count($otros_info) ; $i++) {
                  //--  --//

                  switch (true) {
                  case ( $otros_info[$i]["F_LLEGADAR"] == false ):
                     $calculo_tiempo = "<code>error no se registró tiempo</code>";
                    break;
                  case ( $otros_info[$i]["DESPACHO"] == true ):
                    $fechaFin_car_gen = $otros_info[$i]["DESPACHO"] ;
                    $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($otros_info[$i]["F_LLEGADAR"],$fechaFin_car_gen);
                    $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($fechaFin_car_gen,$otros_info[$i]["F_LLEGADAR"]);
                    break;
                  default:
                    $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                    $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($otros_info[$i]["F_LLEGADAR"],$fechaFin_car_gen);
                    $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($fechaFin_car_gen,$otros_info[$i]["F_LLEGADAR"]);
                    break;
                  }
                  //--  --//
                  if ( $otros_info[$i]["STATUS"] == 7 && $calculo_minutos <90 ){
                  #$descargas_fin_des[$i] = $i;
                  $otros_procesoEnTiempo[$i]= $i;
                ?>
                  <tr>
                    <td class="small"><?= $otros_info[$i]["ALMACEN"] ?></td>
                    <td class="small"><code><?= $otros_info[$i]["SOLICITUD"] ?></code></td>
                    <td class="small"><?= $otros_info[$i]["RS_CLI"] ?></td>
                    <td class="small"><span class="badge bg-info"><i class="fa  fa-clock-o"></i> <?= $otros_info[$i]["F_LLEGADAR"] ?></span></td>
                    <td class="small"><span class="badge bg-info"><i class="fa  fa-clock-o"></i> <?= $otros_info[$i]["DESPACHO"] ?></span></td>
                    <td class="small"><code> <?php
                    switch (true) {
                      case ($otros_info[$i]["F_LLEGADAR"] == false):
                        $calculo_tiempo_otros = "<code>No se Registro Tiempo</code>";
                        break;

                      default:
                        $fechaInicio = $otros_info[$i]["F_LLEGADAR"];
                        $fechaFin = $otros_info[$i]["DESPACHO"];
                        $calculo_tiempo_otros = $obj_op_in_car_des_info->calculo_tiempo($fechaInicio,$fechaFin);
                        break;
                    }
                    echo $calculo_tiempo_otros; ?> </code></td>
                    <!-- TERMINA CALCULO DE TIEMPO EN OTROS -->
                    <td class="small"><span class="badge bg-purple">CONCLUIDO</span></td>
                    <td class="small"><?= $otros_info[$i]["OBS_CON"] ?></td>
                    <td class="small"><?= $otros_info[$i]["VEHICULO"] ?></td>
                    <td class="small"><?= $otros_info[$i]["PLACAS"] ?></td>
                    <td class="small"><?= $otros_info[$i]["UME"] ?></td>
                    <td class="small"><?= $otros_info[$i]["CANTIDAD"] ?></td>
                    <td class="small"><?= $otros_info[$i]["DES_MER"] ?></td>
                    <td class="small"><?= $otros_info[$i]["AL_NOM"].' '.$otros_info[$i]["AL_APEP"].' '.$otros_info[$i]["AL_APEM"] ?></td>
                    <?php
                      echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$otros_info[$i]["SOLICITUD"].", ".$otros_info[$i]["IID_NUM_CLIENTE"].", ".$otros_info[$i]["ID_PLAZA"].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                    ?>
                  </tr>
                <?php } } ?>
                </tbody>
                </table>
              </div>
            <!-- TERMINA TABLA PARA OTROS CONCLUIDOS -->
          </div>

          <!--DESFAZADPS-->
          <div id="link_tab_otr_condes" class="tab-pane fade">

          <h5 class="text-green text-center"><i class="fa fa-check-square-o"></i> OPERACIONES CROSSDOCK CONCLUIDAS DESTIEMPO <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>

            <!-- INICIA TABLA PARA OTROS CONCLUIDOS -->
              <div class="table-responsive">
                <table id="tabla_manufac_car_can4" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small">ALMACEN</th>
                    <th class="small">SOL.</th>
                    <th class="small">CLIENTE</th>
                    <th class="small">INICIO</th>
                    <th class="small">TERMINO</th>
                    <th class="small">Tiempo</th>
                    <th class="small">STATUS</th>
                    <th class="small">OBS.</th>
                    <th class="small">T/VEHÍCULO</th>
                    <th class="small">PLACAS</th>
                    <th class="small">UME</th>
                    <th class="small">CANTIDAD</th>
                    <th class="small">MERCANCÍA</th>
                    <th class="small">ALMACENISTA</th>
                    <th class="small">GALERIA</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  for ($i=0; $i <count($otros_info) ; $i++) {
                  //--  --//

                  switch (true) {
                  case ( $otros_info[$i]["F_LLEGADAR"] == false ):
                     $calculo_tiempo = "<code>error no se registró tiempo</code>";
                    break;
                  case ( $otros_info[$i]["DESPACHO"] == true ):
                    $fechaFin_car_gen = $otros_info[$i]["DESPACHO"] ;
                    $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($otros_info[$i]["F_LLEGADAR"],$fechaFin_car_gen);
                    $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($fechaFin_car_gen,$otros_info[$i]["F_LLEGADAR"]);
                    break;
                  default:
                    $fechaFin_car_gen = strftime("%d-%m-%Y %H:%M:%S");
                    $calculo_tiempo = $obj_op_in_car_des_info->calculo_tiempo($otros_info[$i]["F_LLEGADAR"],$fechaFin_car_gen);
                    $calculo_minutos = $obj_op_in_car_des_info->dif_minutos($fechaFin_car_gen,$otros_info[$i]["F_LLEGADAR"]);
                    break;
                  }
                  //--  --//
                  if ( $otros_info[$i]["STATUS"] == 7 && $calculo_minutos > 90 ){
                  $otros_procesoEnDesface[$i]= $i;
                ?>
                  <tr>
                    <td class="small"><?= $otros_info[$i]["ALMACEN"] ?></td>
                    <td class="small"><code><?= $otros_info[$i]["SOLICITUD"] ?></code></td>
                    <td class="small"><?= $otros_info[$i]["RS_CLI"] ?></td>
                    <td class="small"><span class="badge bg-info"><i class="fa  fa-clock-o"></i> <?= $otros_info[$i]["F_LLEGADAR"] ?></span></td>
                    <td class="small"><span class="badge bg-info"><i class="fa  fa-clock-o"></i> <?= $otros_info[$i]["DESPACHO"] ?></span></td>
                    <!-- INICIA CALCULO DE TIEMPO EN OTROS -->

                    <td class="small"><code> <?php
                    switch (true) {
                      case ($otros_info[$i]["F_LLEGADAR"] == false):
                        $calculo_tiempo_otros = "<code>No se Registro Tiempo</code>";
                        break;

                      default:
                        $fechaInicio = $otros_info[$i]["F_LLEGADAR"];
                        $fechaFin = $otros_info[$i]["DESPACHO"];
                        $calculo_tiempo_otros = $obj_op_in_car_des_info->calculo_tiempo($fechaInicio,$fechaFin);
                        break;
                    }
                    echo $calculo_tiempo_otros; ?> </code></td>
                    <!-- TERMINA CALCULO DE TIEMPO EN OTROS -->
                    <td class="small"><span class="badge bg-purple"> CONCLUIDO</span></td>
                    <td class="small"><?= $otros_info[$i]["OBS_CON"] ?></td>
                    <td class="small"><?= $otros_info[$i]["VEHICULO"] ?></td>
                    <td class="small"><?= $otros_info[$i]["PLACAS"] ?></td>
                    <td class="small"><?= $otros_info[$i]["UME"] ?></td>
                    <td class="small"><?= $otros_info[$i]["CANTIDAD"] ?></td>
                    <td class="small"><?= $otros_info[$i]["DES_MER"] ?></td>
                    <td class="small"><?= $otros_info[$i]["AL_NOM"].' '.$otros_info[$i]["AL_APEP"].' '.$otros_info[$i]["AL_APEM"] ?></td>
                    <?php
                      echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$otros_info[$i]["SOLICITUD"].", ".$otros_info[$i]["IID_NUM_CLIENTE"].", ".$otros_info[$i]["ID_PLAZA"]." )'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
                    ?>
                  </tr>
                <?php } } ?>
                </tbody>
                </table>
              </div>
            <!-- TERMINA TABLA PARA OTROS CONCLUIDOS -->
          </div>

          <!-- TAB OTROS CANCELADOS -->
          <div id="link_tab_otr_can" class="tab-pane fade">
          <h5 class="text-red text-center"><i class="fa fa-ban"></i> OPERACIONES CROSSDOCK CANCELADOS <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>

            <!-- INICIA TABLA PARA OTROS CANCELADOS -->
              <div class="table-responsive">
                <table id="tabla_manufac_car_can5" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small">ALMACEN</th>
                    <th class="small">SOL.</th>
                    <th class="small">OPERACIÓN</th>
                    <th class="small">CLIENTE</th>
                    <th class="small">FECHA CANCELACIÓN</th>
                    <th class="small">STATUS</th>
                    <th class="small">OBS.</th>
                    <th class="small">T/VEHÍCULO</th>
                    <th class="small">PLACAS</th>
                    <th class="small">UME</th>
                    <th class="small">CANTIDAD</th>
                    <th class="small">MERCANCÍA</th>
                    <th class="small">ALMACENISTA</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                for ($i=0; $i <count($otros_info) ; $i++) {
                  if( $otros_info[$i]["STATUS"]== 8 ){
                  $otros_cancelados[$i] = $i;
                ?>
                  <tr>
                    <td class="small"><?= $otros_info[$i]["ALMACEN"] ?></td>
                    <td class="small"><code><?= $otros_info[$i]["SOLICITUD"] ?></code></td>
                    <td class="small"><?= $otros_info[$i]["OPERACION"] ?></td>
                    <td class="small"><?= $otros_info[$i]["RS_CLI"] ?></td>
                    <td class="small"><span class="badge bg-info"><i class="fa  fa-clock-o"></i> <?= $otros_info[$i]["F_CANCELADO"] ?></span></td>
                    <td class="small"><span class="badge bg-red">CANCELADO</span></td>
                    <td class="small"><?= $otros_info[$i]["OBS_CAN"] ?></td>
                    <td class="small"><?= $otros_info[$i]["VEHICULO"] ?></td>
                    <td class="small"><?= $otros_info[$i]["PLACAS"] ?></td>
                    <td class="small"><?= $otros_info[$i]["UME"] ?></td>
                    <td class="small"><?= $otros_info[$i]["CANTIDAD"] ?></td>
                    <td class="small"><?= $otros_info[$i]["DES_MER"] ?></td>
                    <td class="small"><?= $otros_info[$i]["AL_NOM"].' '.$otros_info[$i]["AL_APEP"].' '.$otros_info[$i]["AL_APEM"] ?></td>
                  </tr>
                <?php } } ?>
                </tbody>
                </table>
              </div>
            <!-- TERMINA TABLA PARA OTROS CANCELADOS -->
          </div>


            <div id="link_tab_otr_prog" class="tab-pane fade"><!-- INICIA TABLA PARA VEHICULOS CANCELADOS -->
              <h5 class="text-green text-center"><i class="fa fa-ban"></i> OPERACIONES CROSSDOCK PROGRAMADOS <?= $plaza_manufac." <code>". $titulo_fec_manufac."</code>" ?> </h5><hr>
              <div class="table-responsive">
                <table id="tabla_manufac_car_can5" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small">ALMACEN</th>
                    <th class="small">SOL.</th>
                    <!--<th class="small">OPERACIÓN</th>-->
                    <th class="small">CLIENTE</th>
                    <th class="small">FECHA APROX LLEGADA</th>
                    <th class="small">STATUS</th>
                    <th class="small">OBS.</th>
                    <th class="small">T/VEHÍCULO</th>
                    <th class="small">PLACAS</th>
                    <th class="small">UME</th>
                    <th class="small">CANTIDAD</th>
                    <!--<th class="small">MERCANCÍA</th>
                    <th class="small">ALMACENISTA</th>-->
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $otros_car_des_info = $obj_info_otros_manufac->otros_info_car_des_prog($plaza_manufac,$dia_manufac,$fec_ini_per_manufac,$fec_fin_per_manufac,$select_manufac_global_plaza);
                  for ($i=0; $i <count($otros_car_des_info) ; $i++) {
                    if($otros_car_des_info[$i]["STATUS"]== 0 ){
                     $otros_cross_programados+=1;
                  ?>
                  <tr>
                    <td class="small"><?= $otros_car_des_info[$i]["ALMACEN"] ?></td>
                    <td class="small"><code><?= $otros_car_des_info[$i]["SOLICITUD"] ?></code></td>
                    <!--<td class="small"><?= $otros_car_des_info[$i]["OPERACION"] ?></td>-->
                    <td class="small"><?= $otros_car_des_info[$i]["RS"] ?></td>
                    <td class="small"><span class="badge bg-info"><i class="fa  fa-clock-o"></i> <?= $otros_car_des_info[$i]["LLEGA_APROX"] ?></span></td>
                    <td class="small"><span class="badge bg-red">PROGRAMADO</span></td>
                    <td class="small"><?= $otros_car_des_info[$i]["OBS_CAN"] ?></td>
                    <td class="small"><?= $otros_car_des_info[$i]["VEHICULO"] ?></td>
                    <td class="small"><span class="badge label-info"><?=$otros_car_des_info[$i]["PLACAS1"]?></span><br><span class="badge label-info"><?=$otros_car_des_info[$i]["PLACAS2"]?></span></td>
                    <td class="small"><?= $otros_car_des_info[$i]["UME"] ?></td>
                    <td class="small"><?= $otros_car_des_info[$i]["CANTIDAD"] ?></td>
                    <!--<td class="small"><?= $otros_car_des_info[$i]["DES_MER"] ?></td>
                    <td class="small"><?= $otros_car_des_info[$i]["AL_NOM"].' '.$otros_car_des_info[$i]["AL_APEP"].' '.$otros_car_des_info[$i]["AL_APEM"] ?></td>-->
                  </tr>
                <?php } } ?>
                </tbody>
                </table>
              </div>
           </div><!-- TERMINA TABLA PARA VEHICULOS PROGRAMADOS -->

        </div><!-- ./tab-content -->
      </div><!-- ./nav-tabs-custom -->
    </div><!--/.box-body-->
  </div>
</section>

<?php endif; ?>
<!-- ######### TERMINA SECCION PARA LA TABLA DE OPERACIONES OTROS ########## -->


<!-- ############################ TERMINA SECCION PARA OPERACIONES OTROS ############################# -->



<?php } ?><!-- reduce las conecciones al seleccionar una plaza -->
<!-- ****************************** TERMINA SECCION POR PLAZA SELECCIONADA DE OPERACIONES MANUFACTURA ****************************** -->




<!-- ########################### INICIA SECCION PARA INCLUIR OPERACIONES ########################### -->
<?php
include_once 'operaciones_manufactura.php'
?>
<!-- ########################### TERMINA SECCION PARA INCLUIR OPERACIONES ########################### -->



    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->

<div class="modal fade" id="asignacion_activos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
 <div class="modal-dialog" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <h5 class="modal-title"> Manufactura Imagenes</h5>
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
       </button>
     </div>
            <div class="modal-body" id='modal'>
            </div>
     <div class="modal-footer">
     <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
     </div>
 </div>
</div>
</div>
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- SCRIPT PARA SELECCION DE PLAZAS PARA CONEXIONES -->
<script>
$("#check_cor,#check_mex,#check_gol,#check_pen,#check_pue,#check_baj,#check_occ,#check_nor,#check_leo").click(function(){
    //////////////////
    if ($("#check_cor").is(':checked')) {
        $("#input_cor").val(3);
    }else{
        $("#input_cor").val(0);
    }
    //////////////////
    if ($("#check_mex").is(':checked')) {
        $("#input_mex").val(4);
    }else{
        $("#input_mex").val(0);
    }
    //////////////////
    if ($("#check_gol").is(':checked')) {
        $("#input_gol").val(5);
    }else{
        $("#input_gol").val(0);
    }
    //////////////////
    if ($("#check_pen").is(':checked')) {
        $("#input_pen").val(6);
    }else{
        $("#input_pen").val(0);
    }
    //////////////////
    if ($("#check_pue").is(':checked')) {
        $("#input_pue").val(7);
    }else{
        $("#input_pue").val(0);
    }
    //////////////////
    if ($("#check_baj").is(':checked')) {
        $("#input_baj").val(8);
    }else{
        $("#input_baj").val(0);
    }
    //////////////////
    if ($("#check_occ").is(':checked')) {
        $("#input_occ").val(17);
    }else{
        $("#input_occ").val(0);
    }
    //////////////////
    if ($("#check_nor").is(':checked')) {
        $("#input_nor").val(18);
    }else{
        $("#input_nor").val(0);
    }
    //////////////////
    if ($("#check_leo").is(':checked')) {
        $("#input_leo").val(23);
    }else{
        $("#input_leo").val(0);
    }
    //////////////////
});
</script>
<!-- SCRIPT PARA ASIGNAR EL VALOR A LOS WIDGETS -->
<script>
 $(document).ready(function () {
  //WIDGETS PARA CARGAS
    $('#widgets_cargas').text('<?=$total_cargas?>')
    $('#widgets_cargas_pro\\[\\]').text('<?=$total_cargas_proceso?>')
    $('#widgets_cargas_fin\\[\\]').text('<?=$total_cargas_finalizadas+$total_cargas_finalizadas_des?>')

  //WIDGETS PARA DESCARGAS
    $('#widgets_descargas').text('<?=$total_descargas?>')
    $('#widgets_descargas_pro\\[\\]').text('<?=$total_descargas_proceso?>')
    $('#widgets_descargas_fin\\[\\]').text('<?=$total_descargas_finalizadas+$total_descargas_finalizadas_des?>')

  //WIDGETS PARA OTROS
    $('#widgets_otros').text('<?=$total_otros?>')
    $('#widgets_otros_proceso').text('<?=$total_otros_proceso?>')
    $('#widgets_otros_proceso2\\[\\]').text('<?=$total_otros_proceso?>')
    $('#widgets_otros_proceso3\\[\\]').text('<?=$total_otros_procesoTiempo?>')
    $('#widgets_otros_proceso4\\[\\]').text('<?=$total_otros_procesoDesf?>')
    $('#widgets_otros_proceso5\\[\\]').text('<?=$total_otros_cancelados?>')
    $('#widgets_otros_fin').text('<?=$total_otros_fin?>')


  //WIDGETS PARA CARGAS-DESCARGAS DESFASADO
    $('#widgets_desfasados').text('<?=$total_car_des_desfasados?>')
    $('#widgets_desfasados_car\\[\\]').text('<?=$total_cargas_finalizadas_des?>')
    $('#widgets_desfasados_car_ritmo\\[\\]').text('<?=$total_cargas_finalizadas_des_ritm?>')
    $('#widgets_desfasados_des\\[\\]').text('<?=$total_descargas_finalizadas_des?>')
    $('#widgets_desfasados_des_ritmo\\[\\]').text('<?=$total_descargas_finalizadas_des_ritmo?>')

  //WIDGETS PARA CARGAS-DESCARGAS FIN EN TIEMPO
    $('#widgets_fintiempo_car\\[\\]').text('<?=$total_cargas_finalizadas?>')
    $('#widgets_fintiempo_des\\[\\]').text('<?=$total_descargas_finalizadas?>')

  //WIDGETS PARA CARGAS-DESCARGAS CANCELADAS

    $('#widgets_can_des\\[\\]').text('<?=$total_descargas_canceladas?>')
    $('#widgets_can_car\\[\\]').text('<?=$total_cargas_canceladas?>')

  //WIDGETS VEHICULOS PROGRAMADAS POR EL CLIENTE
    $('#widgets_programadas').text('<?=$contador_cargas+$contador_descargas?>')
    $('#widgets_cargas_programadas\\[\\]').text('<?=$contador_cargas?>')
    $('#widgets_descargas_programadas\\[\\]').text('<?=$contador_descargas?>')
    $('#widgets_cross_programadas\\[\\]').text('<?=$otros_cross_programados?>')

});
</script>
<!-- script para guargar el tap seleccionado -->
<script type="text/javascript">
$(function() {
// OP MANUFACTURA CARGAS
    /*TAB CARGAS EN PROCESO*/
    $('#tab_car_pro,#tab_car_pro').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_cargas_manufac', $(e.target).attr('href'));
    });
    /*TAB CARGAS FINALIZADAS EN TIEMPO*/
    $('#tab_car_fin,#tab_car_fin').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_cargas_manufac', $(e.target).attr('href'));
    });
    /*TAB CARGAS FINALIZADAS DESFASADO*/
    $('#tab_car_fin_des,#tab_car_fin_des').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_cargas_manufac', $(e.target).attr('href'));
    });
    /*TAB CARGAS CANCELADAS*/
    $('#tab_car_can,#tab_car_can').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_cargas_manufac', $(e.target).attr('href'));
    });
    /*GUARDA EL TAB DONDE SELECCIONO EN CARGAS*/
    var activeTab_cargas_manufac = localStorage.getItem('activeTab_cargas_manufac');
    if(activeTab_cargas_manufac){
      $('#myTab_manufac_carga a[href="' + activeTab_cargas_manufac + '"]').tab('show');
    }
// OP MANUFACTURA DESCARGAS
    /*TAB DESCARGAS EN PROCESO*/
    $('#tab_des_pro,#tab_des_pro').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_descargas_manufac', $(e.target).attr('href'));
    });
    /*TAB DESCARGAS FINALIZADAS EN TIEMPO*/
    $('#tab_des_fin,#tab_des_fin').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_descargas_manufac', $(e.target).attr('href'));
    });
    /*TAB DESCARGAS FINALIZADAS DESFASADO*/
    $('#tab_des_fin_des,#tab_des_fin_des').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_descargas_manufac', $(e.target).attr('href'));
    });
    /*TAB DESCARGAS CANCELADAS*/
    $('#tab_des_can,#tab_des_can').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_descargas_manufac', $(e.target).attr('href'));
    });
    /*GUARDA EL TAB DONDE SELECCIONO EN DESCARGAS*/
    var activeTab_descargas_manufac = localStorage.getItem('activeTab_descargas_manufac');
    if(activeTab_descargas_manufac){
      $('#myTab_manufac_descarga a[href="' + activeTab_descargas_manufac + '"]').tab('show');
    }
// OP MANUFACTURA OTROS
    /*TAB OTROS PENDIENTES*/
    /*TAB OTROS EN PROCESO*/
    $('#tab_otr_pro,#tab_otr_pro').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_otros_manufac', $(e.target).attr('href'));
    });
    /*TAB OTROS CONCLUIDOS*/
    $('#tab_otr_con,#tab_otr_con').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_otros_manufac', $(e.target).attr('href'));
    });
    /*desface*/
    $('#tab_otr_conDES,#tab_otr_conDES').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_otros_manufac', $(e.target).attr('href'));
    });
    /*TAB OTROS CANCELADOS*/
    $('#tab_otr_can,#tab_otr_can').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_otros_manufac', $(e.target).attr('href'));
    });
    /*GUARDA EL TAB DONDE SELECCIONO EN OTROS*/
    var activeTab_otros_manufac = localStorage.getItem('activeTab_otros_manufac');
    if(activeTab_otros_manufac){
      $('#myTab_manufac_otros a[href="' + activeTab_otros_manufac + '"]').tab('show');
    }

});
</script>
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

  $(".select_manufac").select2({
   placeholder: "Elija una opción",
  allowClear: true
});
</script>
<script>
function realizaProceso(v_numero, v_mensaje){
        var parametros = {
                "numero" : v_numero,
                "mensaje" : v_mensaje
        };
        $.ajax({
                data:  parametros,
                url:   '../action/enviar_whats.php',
                type:  'post',
                beforeSend: function () {
                        $("#resultado\\[\\]").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                        $("#resultado\\[\\]").html(response);
                }
        });
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
<!-- DataTables ROW GROUP -->
<script src="https://cdn.datatables.net/rowgroup/1.0.0/js/dataTables.rowGroup.min.js"></script>
<script>
$(document).ready(function() {

  $('#tabla_proceso_carga,#tabla_proceso_descarga').DataTable( {
        stateSave: true,
        "ordering": false,
        "searching": true,
        "scrollX": false,
        "sScrollX": "100%",
        "sScrollXInner": "100%",
        lengthMenu: [[3, 10, 25, -1], [3, 10, 25, "All"]],
         pageLength: 3,
        "language": {
            "url": "../plugins/datatables/Spanish.json"
        }
    } );

});



$(document).ready(function() {
    $('#tabla_alm_carga,#tabla_alm_descarga,#tabla_alm_otros,#tabla_alm_des').DataTable( {
        "scrollY": "200px",
        "scrollX": true,
        "searching": true,
        stateSave: true,
        "scrollCollapse": false,
        "paging": false,
        "info": false,
        "language": {
          "url": "../plugins/datatables/Spanish.json"
        },
    } );
} );

$(document).ready(function() {
    ////////// INICIA CODE FINTRO ENTIEMPO-ENRETRASO /////////
    var carga_tiem = $('#tabla_alm_carga').DataTable();
    $('#boton_carga_tiem,#boton_carga_retr').click( function() {
        carga_tiem.search( this.value ).draw();
    } );

    var descarga_tiem = $('#tabla_alm_descarga').DataTable();
    $('#boton_descarga_tiem,#boton_descarga_retr').click( function() {
        descarga_tiem.search( this.value ).draw();
    } );
    ////////// TERMINA CODE FINTRO ENTIEMPO-ENRETRASO /////////
} );

//-----------------------------------------------------------//
$(document).ready(function() {


  var button_view = {
                    extend: 'colvis',
                    collectionLayout: 'fixed two-column',
                    text: '<i class="fa fa-eye-slash"></i>',
                    titleAttr: '(Mostrar/ocultar) Columnas',
                    autoClose: true,
                    }
    $('#tabla_manufac_car_tiem,#tabla_manufac_car_des,#tabla_manufac_car_des_rt,#tabla_manufac_car_can,#tabla_manufac_car_tiem2,#tabla_manufac_car_des2,#tabla_manufac_car_can2,#tabla_manufac_car_can3,#tabla_manufac_car_can4,#tabla_manufac_car_can5').DataTable({
      stateSave: true,
      "ordering": true,
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "language": {
          "url": "../plugins/datatables/Spanish.json"
        },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Manufactura',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: '<h5>Manufactura</h5>',
          },

          button_view
        ],
//---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });
} );
</script>

<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script>

$('#fec_rango_ce_cce,#fec_rango_ce_cca,#fec_rango_ce_ccc').daterangepicker(
        {
        "linkedCalendars": false,
        "showDropdowns": true,
      //INICIA CODE OPCION PARA FORMATO EN ESPAÑOL
        "locale": {
        "format": "DD-MM-YYYY",
        "separator": " - ",
        "applyLabel": "Aplicar",
        "cancelLabel": "Cancelar",
        "fromLabel": "From",
        "toLabel": "To",
        "customRangeLabel": "Fecha Personalizada",
        "daysOfWeek": [
            "Do",
            "Lu",
            "Ma",
            "Mi",
            "Ju",
            "Vi",
            "Sa"
        ],
        "monthNames": [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agusto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ],
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
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function (start, end) {
          $('#fec_ini_per_manufac').val(start.format('DD-MM-YYYY'));
          $('#fec_fin_per_manufac').val(end.format('DD-MM-YYYY'));
        }
    );
</script>

<script>
     function cargarImagen(iidconsecutivo, cliente, plaza){
               $("#modal").load("../class/imagenes_manufac.php?iidconsecutivo="+iidconsecutivo+"&cliente="+cliente+"&plaza="+plaza+"");
     }
</script>

<!-- FLOT CHARTS -->
<script src="../plugins/flot/jquery.flot.min.js"></script>
<!-- FLOT PIE CHARTS 3D -->
<script src="../plugins/flot/jquery.flot.pie3d.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="../plugins/flot/jquery.flot.resize.min.js"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="../plugins/flot/jquery.flot.pie.min.js"></script>
<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
<script src="../plugins/flot/jquery.flot.categories.js"></script>
<!-- FLOT ORDER BARS  -->
<script src="../plugins/flot/jquery.flot.orderBars.js"></script>
<!-- FLOT  bar charts click text -->
<script src="../plugins/flot/jquery.flot.tooltip.js"></script>
<script>
// *--*-*-*-*-*-*-*-*-*.*. INICIA VALORES NECESARIOS PARA GRAFICA DE OPERACIONES GLOBAL *--*-*-*-*-*-*-*-*-*-*.*.
    var v_cargas = [
        [1.0, <?=$total_cargas_cor?> ],// CÓRDOBA
        [2.0, <?=$total_cargas_mex?> ],// MÉXICO
        [3.0, <?=$total_cargas_gol?> ],// GOLFO
        [4.0, <?=$total_cargas_pen?> ],// PENINSULA
        [5.0, <?=$total_cargas_pue?> ],// PUEBLA
        [6.0, <?=$total_cargas_baj?> ],// BAJIO
        [7.0, <?=$total_cargas_occ?> ],// OCCIDENTE
        [8.0, <?=$total_cargas_nor?> ],// NORESTE
        // [9.0, <?=$total_cargas_leo?> ] // LEON
    ];

    var v_descargas = [
        [1.0, <?=$total_descargas_cor?> ],// CÓRDOBA
        [2.0, <?=$total_descargas_mex?> ],// MÉXICO
        [3.0, <?=$total_descargas_gol?> ],// GOLFO
        [4.0, <?=$total_descargas_pen?> ],// PENINSULA
        [5.0, <?=$total_descargas_pue?> ],// PUEBLA
        [6.0, <?=$total_descargas_baj?> ],// BAJIO
        [7.0, <?=$total_descargas_occ?> ],// OCCIDENTE
        [8.0, <?=$total_descargas_nor?> ],// NORESTE
        // [9.0, <?=$total_descargas_leo?> ] // LEON
    ];

    var v_otros = [
        [1.0, <?=$total_otros_cor?> ],// CÓRDOBA
        [2.0, <?=$roral_otros_mex?> ],// MÉXICO
        [3.0, <?=$roral_otros_gol?> ],// GOLFO
        [4.0, <?=$roral_otros_pen?> ],// PENINSULA
        [5.0, <?=$roral_otros_pue?> ],// PUEBLA
        [6.0, <?=$roral_otros_baj?> ],// BAJIO
        [7.0, <?=$roral_otros_occ?> ],// OCCIDENTE
        [8.0, <?=$roral_otros_nor?> ],// NORESTE
        // [9.0, <?=$roral_otros_leo?> ] // LEON
    ];

    var data1 = [
        {
            label: "Cargas",
            data: v_cargas,
            bars: {
                show: true,
                barWidth: 0.2,
                fill: true,
                lineWidth: 1,
                order: 1,
                fillColor:  "#00C0EF"
            },
            show: true,
            color: "#00C0EF"
        },
        {
            label: "Descargas",
            data: v_descargas,
            bars: {
                show: true,
                barWidth: 0.2,
                fill: true,
                lineWidth: 1,
                order: 2,
                fillColor:  "#00A65A"
            },
            color: "#00A65A"
        },

        {
            label: "Otros",
            data: v_otros,
            bars: {
                show: true,
                barWidth: 0.2,
                fill: true,
                lineWidth: 1,
                order: 2,
                fillColor:  "#F39C12"
            },
            color: "#F39C12"
        }
    ];

  var ticks = [
    [1, "<form method='post'><button type='submit' value='CÓRDOBA (ARGO)' name='plaza_manufac' class='btn btn-link'>Córdoba</button></form>"],
    [2, "<form method='post'><button type='submit' value='MÉXICO (ARGO)' name='plaza_manufac' class='btn btn-link'>México</button></form>"],
    [3, "<form method='post'><button type='submit' value='GOLFO (ARGO)' name='plaza_manufac' class='btn btn-link'>Golfo</button></form>"],
    [4, "<form method='post'><button type='submit' value='PENINSULA (ARGO)' name='plaza_manufac' class='btn btn-link'>Peninsula</button></form>"],
    [5, "<form method='post'><button type='submit' value='PUEBLA (ARGO)' name='plaza_manufac' class='btn btn-link'>Puebla</button></form>"],
    [6, "<form method='post'><button type='submit' value='BAJIO (ARGO)' name='plaza_manufac' class='btn btn-link'>Bajio</button></form>"],
    [7, "<form method='post'><button type='submit' value='OCCIDENTE (ARGO)' name='plaza_manufac' class='btn btn-link'>Occidente</button></form>"],
    [8, "<form method='post'><button type='submit' value='NORESTE (ARGO)' name='plaza_manufac' class='btn btn-link'>Noreste</button></form>"],
    // [9, "<form method='post'><button type='submit' value='LEON (ARGO)' name='plaza_manufac' class='btn btn-link'>Leon</button></form>"]
  ];

  var option_graf_global =
  {
    xaxis: {
      min: 0.0,
      max: 10.0,
      mode: null,
      ticks: ticks,
      tickLength: 0, // hide gridlines
      axisLabelUseCanvas: true,
      //rotateTicks: 135,
      axisLabel: 'Plazas',
      axisLabelFontSizePixels: 10,
      axisLabelFontFamily: 'Verdana, Arial',
      axisLabelPadding: 50,
      tickLength: 20, //linea
    },
    yaxis: {
      axisLabel:"Valor",
      axisLabelUseCanvas: true,
      axisLabelFontSizePixels: 10,
      axisLabelFontFamily: 'Verdana, Arial',
      axisLabelPadding: 5,
      min:0
    },
    grid: {
      hoverable: true,
      //clickable: false,
      //borderWidth: 1,
      borderColor: {
              top: "#e5e5e5",
              right: "#e5e5e5",
              bottom: "#a5b2c0",
              left: "#a5b2c0"
      },
    },
    legend: {
          show: false,
    },
    series: {
            shadowSize: 1,
            bars: {  align: 'center'},
            // lines: { show: true },
            points: { show: true, }
    },
    /////////////////////////
    tooltip: {
        show: true,
        content: "<div style='font-size: 13px; border: 2px solid; padding: 2px;background-color: rgba(255, 247, 255, 0.8); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color:%c;'> <font color='#3C8DBC'><b> %s </b></font><font color='#222D32'><b><div class='text-center'>Total = %y</div></b></font></div>",
        shifts: {
            x:15,
            y:-55
        },
        defaultTheme: false,
    },
    ////////////////////////////
    yaxis: {
        tickDecimals: 0,
        min:0,

    },
  };
// *--*-*-*-*-*-*-*-*-*.*. INICIA VALORES NECESARIOS PARA GRAFICA DE CARGAS *--*-*-*-*-*-*-*-*-*-*.*.
//VARIABLE PARA GRAFICA DE BARRAS EN CARGAS
  var v_data_cargas= [
    { data: [['<a id="tab_car_pro" data-toggle="tab" href="#link_tab_car_pro">En Proceso</a>', <?= $total_cargas_proceso ?> ]],
      color: '#30BBBB', },

    { data: [['<a id="tab_car_fin" data-toggle="tab" href="#link_tab_car_fin">Fin. En Tiempo</a>', <?= $total_cargas_finalizadas ?> ]],
      color: '#00C0EF'},

    { data: [['<a id="tab_car_fin_des" data-toggle="tab" href="#link_tab_car_fin_des">Fin. Desfasado</a>', <?= $total_cargas_finalizadas_des ?> ]],
      color: '#F39C12'},

    { data: [['<a id="tab_car_fin_des" data-toggle="tab" href="#link_tab_car_fin_des_ritmo">Fin. Desfasado Por Ritmo</a>', <?= $total_cargas_finalizadas_des_ritm ?> ]],
        color: '#F39C12'},

    { data: [['<a id="tab_car_can" data-toggle="tab" href="#link_tab_car_can">Cancelado</a>', <?= $total_cargas_canceladas  ?> ]],
      color: '#DD4B39'}
    ];

//VARIABLE PARA GRAFICA DE BARRAS EN DESCARGAS
  var v_data_descargas= [
    { data: [['<a id="tab_des_pro" data-toggle="tab" href="#link_tab_des_pro">En Proceso</a>', <?= $total_descargas_proceso ?> ]],
      color: '#30BBBB', },

    { data: [['<a id="tab_des_fin" data-toggle="tab" href="#link_tab_des_fin">Fin. En Tiempo</a>', <?= $total_descargas_finalizadas ?> ]],
      color: '#00A65A'},

    { data: [['<a id="tab_des_fin_des" data-toggle="tab" href="#link_tab_des_fin_des">Fin. Desfasado</a>', <?= $total_descargas_finalizadas_des ?> ]],
      color: '#F39C12'},

    { data: [['<a id="tab_des_can" data-toggle="tab" href="#link_tab_des_can">Cancelado</a>', <?= $total_descargas_canceladas  ?> ]],
      color: '#DD4B39'}
    ];

//VARIABLE PARA GRAFICA DE BARRAS OPERACIONES OTROS
  var v_data_otros= [
    { data: [['<a id="tab_otr_pro" data-toggle="tab" href="#link_tab_otr_pro">EN PROCESO </a>', <?=$total_otros?> ]],
      color: '#39CCCC'},

    { data: [['<a id="tab_otr_con" data-toggle="tab" href="#link_tab_otr_con">FIN EN TIEMPO </a>', <?=$total_otros_concluidos?> ]],
      color: '#555299'},

    { data: [['<a id="tab_otr_can" data-toggle="tab" href="#link_tab_otr_condes">DESFASADOS </a>', <?=$total_otros_cancelados?> ]],
      color: '#DD4B39'},

    { data: [['<a id="tab_otr_con" data-toggle="tab" href="#link_tab_otr_can">CANCELADO </a>', <?=$total_otros_concluidos?> ]],
      color: '#555299'},
    ];


//VARAIBLE PARA GRAFICA DE DONA EFECTIVIDAD EN CARGA
var donutData_efec_car = [
      {label: "EFECTIVIDAD",
       data: <?= $total_cargas_finalizadas ?>,
       color: "#00C0EF"},
      {label: "INEFICIENCIA",
       data: <?= $total_cargas_finalizadas_des ?>,
       color: "#D81B60"}
    ];
//VARAIBLE PARA GRAFICA DE DONA EFECTIVIDAD EN DESCARGA
var donutData_efec_des = [
      {label: "EFECTIVIDAD",
       data: <?= $total_descargas_finalizadas ?>,
       color: "#00A65A"},
      {label: "INEFICIENCIA",
       data: <?= $total_descargas_finalizadas_des ?>,
       color: "#D81B60"}
    ];

//OPCIONES PARA LA GRAFICA DE BARRAS EN CARGAs
  var v_option_bar_carga =
      {
      grid: {
         margin: { right: 0 },
        borderColor: {
                    top: "#e5e5e5",
                    right: "#e5e5e5",
                    bottom: "#a5b2c0",
                    left: "#a5b2c0"
                    },
        hoverable: true
      },
      /////////////////////////
      tooltip: {
        show: true,
        content: "<div class='btn btn-block bg-olive btn-xs'><i class='fa fa-slack'></i>  TOTAL = %n </div>", // show percentages, rounding to 2 decimal places
        shifts: {
          x: 20,
          y: 0
        },
        defaultTheme: false
      },
      ////////////////////////////
      series:  {
                bars: {
                    show: true,
                    align: 'center',
                    barWidth: 0.3,//anchura grafica
                    lineWidth: 1.5,
                    fill: 0.9,//opacidad
                },
                lines: { show: true },
                    points: { show: true }
      },
      ////////////////////
      xaxis: {
            mode: "categories",
            tickLength: 20, //linea
            min: -0.5, //separacion izquierda
            max: 3.7, //separacion derecha
            //rotateTicks: 135
            },
      //////////////////////
      yaxis: {
        //tickDecimals: 0,
        min:0,

      },
      ////////////////////////
    };
//OPCIONES PARA GRAFICA DE DONA EFECTIVIDAD
  var option_donut_efec = {
      series: {
        pie3d: {
        stroke: { //define linea separadora
            width: 8,
            color: '#FFFFFF'
          } ,
          show: true,
          radius: 1, //radius: 1,  tamño radio del circulo
          tilt: 1,//rotacion de angulo
          innerRadius: 80,//radio dona o pastel
          label: {
            show: true,
            radius:3/4,//0.90,//posicion del label con data
            formatter: labelFormatter,
            background: {
                    //opacity: 0.5,///opacidad del fondo label
                    //color: '#fff' //7FDDE9
                    }
          },
        }
      },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: {
          //labelBoxBorderColor: "none"
           show: false,
           // position: "ne" or "nw" or "se" or "sw"
      },

       grid: {
          hoverable: true,
          clickable: true,
          verticalLines: false,
          horizontalLines: false,
      },
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {
      show: true,
      content: "<div style='font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;'>  <b> %s</b></font> <br>  <div class='text-center'>  Total = %n</div>  </div>",
      defaultTheme: false
      }

    };
  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="color:#fff; text-shadow:#222D32 1px -1px, #222D32 -1px 1px, #222D32 1px 1px, #222D32 -1px -1px;text-align:center;">'
        + label
        + "<br>"
        + Math.round(series.percent) + "%</div>";
  }




// *--*-*-*-*-*-*-*-*-*.*. TERMINA SCRIP PARA GRAFICA DE CARGAS POR PLAZA *--*-*-*-*-*-*-*-*-*-*.*.
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

$('#click_modal_his\\[\\]').on('click change',function(){
  $.ajax({url: '../class/Manufactura.php', success: function(result){
      $('#content_img_modal_his\\[\\]').fadeIn(10).html('<div class="imgwrapper"><img class="img-responsive center-block" src="../dist/img/gif-argo-carnado-circulo_l2.gif"/></div>');
  }});
});

<?php for ($i=0; $i <count($info_gral_car_des_otr_cor) ; $i++) { ?>
$('.click_cargando_cargas<?=$info_gral_car_des_otr_cor[$i]["SOLICITUD"]?>').click(function(){
  $.ajax({url: '../class/Manufactura.php', success: function(result){
    $('#cargando_img_cargas<?=$info_gral_car_des_otr_cor[$i]["SOLICITUD"]?>').fadeIn(10).html('<div class="imgwrapper"><img class="img-responsive" src="../dist/img/gif_circulo_b.gif"></div>');
    $('#cargando_text_cargas<?=$info_gral_car_des_otr_cor[$i]["SOLICITUD"]?>').fadeIn(10).html('<b class="text-blue">Cargando...</b>');
  }});
});
<?php } ?>

$('.click_car_btn_reg').click(function(){
  $.ajax({url: '../class/Manufactura.php', success: function(result){
    $('#content_car_text_btn_reg\\[\\]').fadeIn(10).html('<b class="text-blue"><i class="fa fa-cog fa-spin fa-lg fa-fw margin-bottom"></i> CARGANDO...</b>');
  }});
});
</script>
</html>
<?php conexion::cerrar($conn); ?>
