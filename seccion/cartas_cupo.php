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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '8');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/////////////// INSTANCIA PARA LA CLASE CARTAS CUPO ///////////////
include_once '../class/Cartas_cupo.php';
$obj_ce_cartas_cupo = new Cartascupo();

// SESION PARA PLAZAS COMERCIO EXTERIOR
if(isset($_POST['ce_cc_plaza']))
  $_SESSION['ce_cc_plaza'] = $_POST['ce_cc_plaza'];
  $ce_cc_plaza = $_SESSION['ce_cc_plaza'];
// SESION PARA ALMACEN COMERCIO EXTERIOR
if(isset($_POST['ce_cc_almacen']))
  $_SESSION['ce_cc_almacen'] = $_POST['ce_cc_almacen'];
  $ce_cc_almacen = $_SESSION['ce_cc_almacen'];
// SESION PARA TIPO DE CARTA CUPO
if (isset($_POST['op_ce_cartas_cupo']))
  $_SESSION['op_ce_cartas_cupo'] = $_POST['op_ce_cartas_cupo'];
  $op_ce_cartas_cupo = $_SESSION['op_ce_cartas_cupo'];
//SESION PARA HISTORIAL POR DIA CARTAS CUPO
if ( $_SESSION['dia_ce_cc'] == false)
{
  $_SESSION['dia_ce_cc'] = $obj_ce_cartas_cupo->date_base();
  $dia_ce_cc = $_SESSION['dia_ce_cc'];
}else{

  if(isset($_POST["dia_ce_cc"]))
  $_SESSION["dia_ce_cc"] = $_POST["dia_ce_cc"] ;
  $dia_ce_cc = $_SESSION["dia_ce_cc"];

}
//SESION PARA FECHA INICIO PERSONALIZADA
if(isset($_POST["fec_ini_ce_cc"]))
  $_SESSION["fec_ini_ce_cc"] = $_POST["fec_ini_ce_cc"] ;
  $fec_ini_ce_cc = $_SESSION["fec_ini_ce_cc"];
//SESION PARA FECHA FIN PERSONALIZADA
if(isset($_POST["fec_fin_ce_cc"]))
  $_SESSION["fec_fin_ce_cc"] = $_POST["fec_fin_ce_cc"] ;
  $fec_fin_ce_cc = $_SESSION["fec_fin_ce_cc"];
//SESION PARA FECHA CARTAS CUPO EXPEDIDAS ANIO //
if(isset($_POST["anio_ce_cce"]))
  $_SESSION["anio_ce_cce"] = $_POST["anio_ce_cce"] ;
  $anio_ce_cce = $_SESSION["anio_ce_cce"];

/*----------------- TITULO PARA CARTAS CUPO --------------------*/
switch ($op_ce_cartas_cupo) {
  case 'E':
    $titulo_cc = "CARTAS CUPO EXPEDIDAS";
    break;
  case 'RD':
    $titulo_cc = "CARTAS CUPO ARRIBADAS";
    break;
  case 'CC':
    $titulo_cc = "CARTAS CUPO CANCELADAS";
    break;
}
if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){
  $titulo_fecha_cc_ce = "(".$fec_ini_ce_cc.")-(".$fec_fin_ce_cc.")" ;
}else{
  $titulo_fecha_cc_ce = $dia_ce_cc ;
}
switch (true) {
  case ($ce_cc_plaza == true) && ($ce_cc_almacen ==true):
    $titulo_plaza_almacen = "PLAZA ".$ce_cc_plaza." ALMACEN ".$ce_cc_almacen ;
    break;
  case ($ce_cc_plaza == true) && ($ce_cc_almacen ==false):
    $titulo_plaza_almacen = "PLAZA ".$ce_cc_plaza ;
    break;
}

$titulo_cc_ce = $titulo_cc." ".$titulo_plaza_almacen." ".$titulo_fecha_cc_ce;

// asigna fecha en el Widgets
if ( $fec_ini_ce_cc == true and $fec_fin_ce_cc == true ){
  $f_widgets_ac = 'Fecha: ('.$fec_ini_ce_cc.')-('.$fec_fin_ce_cc.')' ;
  $f_widgets_en = $f_widgets_ac;
}else{
  $f_widgets_ac = 'Fecha: '.$dia_ce_cc ;
  if ( $obj_ce_cartas_cupo->consulta_mes_base() == 01 ){
    $anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
    $anio_anterior = date ( 'Y' , $anio_anterior );
    $f_widgets_en = 'Fecha: '.$anio_anterior.'-'.date('Y');
  }else{
    $f_widgets_en = 'Fecha: '.date('Y');
  }
}
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->

<?php include_once('../layouts/plantilla.php'); ?>
<!-- *********** INICIA INCLUDE CSS *********** -->
<!-- DataTables -->
<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">

<!-- CSS BOTON FLOTANTE VOLVER ATRAS -->
<style>
.back-to-top {
  position: fixed;

  bottom: 2em;
  right: 0px;
  text-decoration: none;
  color: #000000;
  background-color: rgba(235, 235, 235, 0.80);
  font-size: 12px;
  padding: 1em;
  display: none;




}
.back-to-top:hover {
  background-color: rgba(135, 135, 135, 0.50);
}

</style>

<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>

        Dashboard
        <small>Comercio Exterior</small>
      </h1>

      <h4 class="content-header text-blue text-center"> CARTAS CUPO </h4>
    </section>
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->


<!-- ########################## INICIA MODAL HISTORIAL CARTAS CUPO ########################## -->
    <div class="modal fade" id="modal_fec_cartas_cupo" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><code>Historial</code></h4>
        </div>
        <div class="modal-body">
          <!-- ::::::::::::::::::::::::::: Inicia Select historial cartas cupo expedidas :::::::::::::::::::::::::: -->
          <div style="display: none;" id="div_ce_cce">
          <form method="post">
            <input type="hidden" name="fec_ini_ce_cc" value="">
            <input type="hidden" name="fec_fin_ce_cc" value="">
            <label>Cartas Cupo Expedidas</label>
            <select  onchange="this.form.submit();" name="dia_ce_cc" class="form-control dia_ce_cc change_modal_cargando" style="width: 100%;">
              <option value=''></option>
              <option value='<?= $obj_ce_cartas_cupo->date_base(); ?>'><?= $obj_ce_cartas_cupo->date_base(); ?></option>
              <?php
              $historial_cce = $obj_ce_cartas_cupo->historial_cce($ce_cc_plaza);
                for ($i=0; $i <count($historial_cce) ; $i++) {
                  echo "<option value='".$historial_cce[$i]["HISTORIAL_CCE"]."'>".$historial_cce[$i]["HISTORIAL_CCE"]."</option>";
                }
              ?>
            </select>
          </form>
          </div>
          <!-- ::::::::::::::::::::::::::: Termina Select historial cartas cupo expedidas :::::::::::::::::::::::::: -->
          <!-- ::::::::::::::::::::::::::: Inicia Select historial cartas cupo arribadas :::::::::::::::::::::::::: -->
          <div style="display: none;" id="div_ce_cca">
          <form method="post">
            <input type="hidden" name="fec_ini_ce_cc" value="">
            <input type="hidden" name="fec_fin_ce_cc" value="">
            <label>Cartas Cupo Arribadas</label>
            <select  onchange="this.form.submit();" name="dia_ce_cc" class="form-control dia_ce_cc change_modal_cargando" style="width: 100%;">
              <option value=''></option>
              <option value='<?= $obj_ce_cartas_cupo->date_base(); ?>'><?= $obj_ce_cartas_cupo->date_base(); ?></option>
              <?php
              $historial_cca = $obj_ce_cartas_cupo->historial_cca($ce_cc_plaza);
                for ($i=0; $i <count($historial_cca) ; $i++) {
                  echo "<option value='".$historial_cca[$i]["HISTORIAL_CCA"]."'>".$historial_cca[$i]["HISTORIAL_CCA"]."</option>";
                }
              ?>
            </select>
          </form>
          </div>
          <!-- ::::::::::::::::::::::::::: Termina Select historial cartas cupo arribadas :::::::::::::::::::::::::: -->
          <!-- ::::::::::::::::::::::::::: Inicia Select historial cartas cupo expedidas :::::::::::::::::::::::::: -->
          <div style="display: none;" id="div_ce_ccc">
          <form method="post">
            <input type="hidden" name="fec_ini_ce_cc" value="">
            <input type="hidden" name="fec_fin_ce_cc" value="">
            <label>Cartas Cupo Canceladas</label>
            <select  onchange="this.form.submit();" name="dia_ce_cc" class="form-control dia_ce_cc change_modal_cargando" style="width: 100%;">
              <option value=''></option>
              <option value='<?= $obj_ce_cartas_cupo->date_base(); ?>'><?= $obj_ce_cartas_cupo->date_base(); ?></option>
              <?php
              $historial_ccc = $obj_ce_cartas_cupo->historial_ccc($ce_cc_plaza);
                for ($i=0; $i <count($historial_ccc) ; $i++) {
                  echo "<option value='".$historial_ccc[$i]["HISTORIAL_CCC"]."'>".$historial_ccc[$i]["HISTORIAL_CCC"]."</option>";
                }
              ?>
            </select>
          </form>
          </div>
          <!-- ::::::::::::::::::::::::::: Termina Select historial cartas cupo expedidas :::::::::::::::::::::::::: -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
<!-- ########################## TERMINA MODAL HISTORIAL CARTAS CUPO ########################## -->



<!-- ######################################## Inicio de Widgets ######################################### -->
    <section><!-- Inicia la seccion de los Widgets -->
      <div class="row">
      <!-- Widgets Cartas cupo expedidas -->
        <div class="col-lg-3 col-xs-4" style="height:200px;">
          <div class="info-box bg-aqua">
            <span class="info-box-icon bg-aqua"><i class="fa fa-edit"></i></span>
              <div class="info-box-content bg-aqua">
                <span class="info-box-number">
                  <?php
                  $widgets_cartas_cupo = $obj_ce_cartas_cupo->widgets_cartas_cupo($ce_cc_plaza,$ce_cc_almacen,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                  for ($i=0; $i <count($widgets_cartas_cupo) ; $i++) {
                    echo '<h3>'.($widgets_cartas_cupo[$i]["TOTAL_CCE_SP"]+$widgets_cartas_cupo[$i]["TOTAL_CCE_CP"]).'</h3>';
                  }
                  ?>
                </span>
                <span>
                  <?php
                  $widgets_cartas_cupo_val = $obj_ce_cartas_cupo->widgets_cartas_cupoValue($ce_cc_plaza,$ce_cc_almacen,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                  for ($x=1; $x <count($widgets_cartas_cupo_val) ; $x++) {
                    echo '<h5>$'.number_format(($widgets_cartas_cupo_val[$x]["TOTAL_CCE_SPT"]+$widgets_cartas_cupo_val[$x]["TOTAL_CCE_CPT"]), 2).' MXN</h5>';
                  }
                   ?>
                </span>
                <span class="progress-description"><b>C.C. Expedidas</b></span>
                <small class="progress-description" title="<?= $f_widgets_en ?>"> <?= $f_widgets_en ?> </small>
              </div>
            <form method="post" action="cartas_cupo.php#E">
             <button type="submit" name="op_ce_cartas_cupo" value="E" class="btn bg-aqua-active btn-block click_modal_cargando">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
        <!-- Termino Widgets Cartas cupo expedidas -->
        <!-- Widgets Cartas cupo no arribadas -->
        <div class="col-lg-3 col-xs-6" style="height:200px;">
          <div class="info-box bg-yellow">
            <span class="info-box-icon bg-yellow"><i class="fa fa-file-text-o"></i></span>
              <div class="info-box-content bg-yellow">
                <span class="info-box-number">
                  <?php
                    for ($i=0; $i <count($widgets_cartas_cupo) ; $i++) {
                      echo "<h3>".$widgets_cartas_cupo[$i]["TOTAL_CNA"]."</h3>";
                    }
                  ?>
                </span>
                <span>
                  <?php
                    for ($i=1; $i <count($widgets_cartas_cupo_val) ; $i++) {
                      echo "<h5>$".number_format($widgets_cartas_cupo_val[$i]["TOTAL_CNAT"], 2)." MXN</h5>";
                    }
                  ?>
                </span>
                <span class="progress-description"><b>C.C. No arribadas</b></span>
                <span class="progress-description" title="<?= $f_widgets_en ?>"> <?= $f_widgets_en ?> </span>
              </div>
            <form method="post" action="cartas_cupo.php#NA">
             <button type="submit" name="op_ce_cartas_cupo" value="NA" class="btn bg-yellow-active btn-block click_modal_cargando">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
        <!-- Termina Widgets Cartas cupo no arribadas -->
        <!-- Widgets Cartas cupo arribadas -->
        <div class="col-lg-3 col-xs-6" style="height:200px;">
          <div class="info-box bg-green">
            <span class="info-box-icon bg-green"><i class="ion-android-clipboard"></i></span>
              <div class="info-box-content bg-green">
                <span class="info-box-number">
                  <?php
                    for ($i=0; $i <count($widgets_cartas_cupo) ; $i++) {
                      echo "<h3>".($widgets_cartas_cupo[$i]["TOTAL_CCA"]/*+$widgets_cartas_cupo[$i]["TOTAL_CCA_2"]*/)."</h3>";
                    }
                  ?>
                </span>
                <span>
                  <?php
                    for ($i=1; $i <count($widgets_cartas_cupo_val) ; $i++) {
                      echo "<h5>$".number_format($widgets_cartas_cupo_val[$i]["TOTAL_CCAT"], 2)." MXN</h5>";
                    }
                  ?>
                </span>
                <span class="progress-description"><b>C.C. Arribadas</b></span>
                <span class="progress-description" title="<?= $f_widgets_ac ?>"> <?= $f_widgets_ac ?> </span>
              </div>
            <form method="post" action="cartas_cupo.php#RD">
             <button type="submit" name="op_ce_cartas_cupo" value="RD" class="btn bg-green-active btn-block click_modal_cargando">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
        <!-- Termina Widgets Cartas cupo arribadas -->
        <!--INICIA WIDGET CARTAS CUPO NO ADUANADAS -->
        <div class="col-lg-3 col-xs-6" style="height:200px;">
          <div class="info-box bg-yellow">
            <span class="info-box-icon bg-yellow"><i class="ion-android-clipboard"></i></span>
              <div class="info-box-content bg-yellow">
                <span class="info-box-number">
                  <?php
                    for ($i=0; $i <count($widgets_cartas_cupo) ; $i++) {
                      echo "<h3>".($widgets_cartas_cupo[$i]["ND_NA"]/*+$widgets_cartas_cupo[$i]["TOTAL_CCA_2"]*/)."</h3>";
                    }
                  ?>
                </span>
                <span>
                  <?php
                    for ($i=1; $i <count($widgets_cartas_cupo_val) ; $i++) {
                      echo "<h5>$".number_format($widgets_cartas_cupo_val[$i]["TOTAL_N_ADUANA"], 2)." MXN</h5>";
                    }
                  ?>
                </span>
                <span class="progress-description"><b>C.C. No Desaduanadas</b></span>
                <span class="progress-description" title="<?= $f_widgets_ac ?>"> <?= $f_widgets_ac ?> </span>
              </div>
            <form method="post" action="cartas_cupo.php#ND">
             <button type="submit" name="op_ce_cartas_cupo" value="ND" class="btn bg-yellow-active btn-block click_modal_cargando">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
        <!-- TERMINA WIDGET CARTAS CUPO NO ADUANADAS -->
        <!-- Widgets Cartas cupo canceladas -->
        <div class="col-lg-3 col-xs-6" style="height:200px;">
          <div class="info-box bg-red">
            <span class="info-box-icon bg-red"><i class="fa fa-ban"></i></span>
              <div class="info-box-content bg-red">
                <span class="info-box-number">
                  <?php
                    for ($i=0; $i <count($widgets_cartas_cupo) ; $i++) {
                      echo "<h3>".$widgets_cartas_cupo[$i]["TOTAL_CCC"]."</h3>";
                    }
                  ?>
                </span>
                <span>
                  <?php
                    for ($i=1; $i <count($widgets_cartas_cupo_val) ; $i++) {
                      echo "<h5>$".number_format($widgets_cartas_cupo_val[$i]["TOTAL_CCCT"], 2)." MXN</h5>";
                    }
                  ?>
                </span>
                <span class="progress-description"><b>C.C. Canceladas</b></span>
                <span class="progress-description" title="<?= $f_widgets_ac ?>"> <?= $f_widgets_ac ?> </span>
              </div>
              <form method="post" action="cartas_cupo.php#CC">
                <button type="submit" name="op_ce_cartas_cupo" value="CC" class="btn bg-red-active btn-block click_modal_cargando">VER <i class="fa fa-arrow-circle-right"></i></button>
              </form>
          </div>
        </div>
        <!-- Termino Widgets Cartas cupo canceladas -->
      </div>
      <!-- /.row -->
      </section><!-- Termina la seccion de los Widgets -->
<!-- ######################################### Termino de Widgets ######################################### -->




<!-- ############################ INICIA SECCION DE LA GRAFICA CARTAS CUPO EXPEDIDAS ############################# -->
<?php if( $op_ce_cartas_cupo == false || $op_ce_cartas_cupo == 'E' ) { ?>
<section>
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica Cartas Cupo Expedida <?= $f_widgets_en ?></h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">  <!-- box-body -->

    <!-- INICIA CODE MUESTRA MENSAJE SOLO EN ENERO -->
    <?php if ( $obj_ce_cartas_cupo->consulta_mes_base() == 01 ){ ?>
    <div class="alert alert-warning alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <h4><i class="icon fa fa-warning"></i> Aviso!</h4>
      Después de enero del <?=date("Y")?> toda la información de “Cartas cupo expedidas” del año anterior, ya no se reflejarán en la gráfica principal de Cartas Cupo Expedida, a menos que la consulte con una fecha personalizada.
    </div>
    <?php } ?>
    <!-- TERMINA CODE MUESTRA MENSAJE SOLO EN ENERO -->


    <ol class="breadcrumb">
    <?php if ( $op_ce_cartas_cupo == true ){ ?>
      <li>
        <form method="post" action="cartas_cupo.php"><!-- INICIA FORM TIPO POST -->
        <input type="hidden" value="" name="ce_cc_plaza">
        <input type="hidden" value="" name="ce_cc_almacen">
        <input type="hidden" value="" name="op_ce_cartas_cupo">
        <button type="submit" class="btn btn-link click_modal_cargando"><i class="fa fa-reply"></i> Regresar</button>
        </form>
      </li>
    <?php } ?>
      <!-- <li>
        <button type="button" id="btn_his_cce" class="btn btn-link" data-toggle="modal" data-target="#modal_fec_cartas_cupo"><i class="fa fa-history"></i> Historial</button>
      </li> -->
      <li>
        <a id="tab_fec_per_pros_co" data-toggle="tab" href="#ce_cce_fec_per">
          <button type="submit" class="btn btn-link"><i class="fa fa-calendar"></i>  Fecha personalizada</button>
        </a>
      </li>
    </ol>

    <!-- INICIA CODE FECHA PERSONALIZADA -->
      <div class="tab-content">
        <div id="ce_cce_fec_per" class="tab-pane fade">
          <form method="post">
            <div class="col-md-4">
              <!--  -->
              <div class="input-group" id="fec_rango_ce_cce">
                <div class="input-group-addon">
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_ini_ce_cc" id="fec_ini_ce_cc" value="<?= $fec_ini_ce_cc ?>" readonly>
                <div class="input-group-addon">
                  <i class="fa fa-calendar-plus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_fin_ce_cc" id="fec_fin_ce_cc" value="<?= $fec_fin_ce_cc ?>" readonly>
              </div>
              <!--  -->
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-sm bg-blue click_modal_cargando">OK</button>
            </div>
          </form>
        </div>
      </div><br>
    <!-- TERMINA CODE FECHA PERSONALIZADA -->

    <div class="row"><!-- div-row -->

      <div class="col-md-8">
        <div id="graf_bar_cp_expedidas" style="height: 350px; "></div>
      </div>

      <div class="col-md-4">
        <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="small-box bg-aqua">
            <div class="inner">
              <p>C.C. EXPEDIDAS</p>
            </div>
            <div style="font-size:50px;" class="icon">
              <img  src="../dist/img/ser_dierectos.png" >
            </div>
          </div>
            <div class="box-footer no-padding"><br>
            <table id="tabla_ce_cce_alm" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">E,PE</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $tabla_cce_almacen = $obj_ce_cartas_cupo->tabla_cce_almacen($ce_cc_plaza,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                for ($i=0; $i <count($tabla_cce_almacen) ; $i++) {  ?>
                <tr>
                  <td class="small"><?= $tabla_cce_almacen[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $tabla_cce_almacen[$i]["PLAZA_SIGLAS"] ?></td>
                  <td>
                  <form method="post" action="cartas_cupo.php#E">
                  <input type="hidden" name="op_ce_cartas_cupo" value="E">
                  <input type="hidden" name="ce_cc_plaza" value="<?=$tabla_cce_almacen[$i]["PLAZA"]?>">
                  <button type="submit" class="btn pull-right badge bg-aqua click_modal_cargando" name="ce_cc_almacen" value="<?= $tabla_cce_almacen[$i]["ALMACEN"] ?>"><?= $tabla_cce_almacen[$i]["TOTAL_CC"] ?></button>
                  </form>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
            </div>
          </div>
          <!-- /.widget-user -->
      </div>

    </div><!-- ./div-row -->

    </div> <!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION DE LA GRAFICA CARTAS CUPO EXPEDIDAS ########################### -->


<!-- ############################ INICIA SECCION DE LA TABLA CARTAS CUPO EXPEDIDAS ############################# -->
<?php if ( $op_ce_cartas_cupo == 'E') { ?>
<section id="E">
  <div class="box box-info">
    <div class="box-header with-border">
      <!-- <h3 class="box-title"><i class="fa fa-table"></i> Tabla de Cartas Cupo Expedida</h3>  -->
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">  <!-- box-body -->

      <h5 class="content-header text-blue text-center"><i class="fa fa-edit"></i> <?=$titulo_cc_ce ?></h5><hr>

      <div class="table-responsive">
        <table id="tabla_ce_cce" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small">IMPORTADOR</th>
            <!-- <th class="small">CONPEDIMENTO</th> -->
            <th class="small">BITÁCORA</th>
            <th class="small">FOLIO</th>
            <th class="small">FECHA EXPEDICIÓN</th>
            <th class="small">FECHA VENCIMIENTO</th>
            <th class="small">TIEMPO TRANSCURRIDO</th>
            <th class="small">SIDEFI</th>
            <th class="small">VALOR DÓLARES</th>
            <th class="small">ADUANA DESPACHO</th>
            <th class="small">PLAZA</th>
            <th class="small">ALMACEN</th>
            <th class="small">DETALLES</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $tabla_ce_cc = $obj_ce_cartas_cupo->tabla_ce_cc($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc,$op_ce_cartas_cupo,$ce_cc_plaza,$ce_cc_almacen);
          for ($i=0; $i <count($tabla_ce_cc) ; $i++) {//abre for para tabla_ce_cc
          if ($tabla_ce_cc[$i]["STATUS_CC"] == 'E' || $tabla_ce_cc[$i]["STATUS_CC"] == 'PE'){//abre if para mostrar solo cartas cupo expedidas
          //---INICIA CODE CONSULTA DE FIRMA DE ACUSE EN BITACORA---//
            $bit_cc = $obj_ce_cartas_cupo->bit_cc($tabla_ce_cc[$i]["ID_NUMERO"],$tabla_ce_cc[$i]["ID_ALMACEN"]);
        ?>
          <tr>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_IMPORTADOR"]."</cite>) ".$tabla_ce_cc[$i]["IMPORTADOR"] ?></td>
            <?php
            //INICIA CODE EVALUA SI TIENE PEDIMENTO
            // if ($tabla_ce_cc[$i]["STATUS_CC"] == 'PE'){
            //  echo '<td class=""><span class="label label-info">SI</span></td>';
            // }else{
            //   echo '<td class=""></td>';
            // }
            //TERMINA CODE EVALUA SI TIENE PEDIMENTO
            //INICIA CODE BITACORA
            if ( count($bit_cc) == 0 ){
              echo '<td class="small"><code>NO EXISTEN REGISTRO DE EVENTO EN LA BITÁCORA</code></td>';
            }else{
              for ($j=0; $j <count($bit_cc) ; $j++) {
                  if ($bit_cc[$j]["FIRMA_ACUSE"] == false){
                  echo '<td class="small">ULTIMO EVENTO SIN FIRMA DE ACUSE <a class="click_ajax_bit'.$i.'"><small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small></a><div class="div_res_ajax'.$i.'" style="display:none"><div id="div_bitacora'.$i.'"></div></div></td>';
                }else{
                  echo '<td class="small">ULTIMO EVENTO CON FIRMA DE ACUSE <a class="click_ajax_bit'.$i.'"><small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small></a><div class="div_res_ajax'.$i.'" style="display:none"><div id="div_bitacora'.$i.'"></div></div></td>';
                }
              }
            }

            //TERMINA CODE BITACORA
            ?>
            <td class="small"><?= $tabla_ce_cc[$i]["ID_NUMERO"] //$tabla_ce_cc[$i]["CVE_SIDEFI"]."".?></td>
            <td class="small"><?= $tabla_ce_cc[$i]["FECHA_EXPEDICION"] ?></td>
        <!-- INICIA FECHA DE VENCIMIENTO Y DIAS TRANSCURRIDOS -->
        <?php
          $fecha_vencimiento = strtotime ( "+20 day" , strtotime ( $tabla_ce_cc[$i]["FECHA_EXPEDICION"] ) ) ;
          $fecha_vencimiento = date ( "d-m-Y" , $fecha_vencimiento );

          switch (true) {
             case ( strtotime($fecha_vencimiento) >= strtotime(date("d-m-Y")) ):
               $color_fec_ven_cc = 'class="label label-success"';
               break;

             default:
               $color_fec_ven_cc = 'class="label label-danger"';
               break;
           }

            echo '<td><span '.$color_fec_ven_cc.'>'.$fecha_vencimiento.'</span></td>';

          $dias_transcurridos = $obj_ce_cartas_cupo->tiempoTranscurridoFechas($tabla_ce_cc[$i]["FECHA_EXPEDICION"],$obj_ce_cartas_cupo->date_base());

        ?>
            <td><span <?= $color_fec_ven_cc ?>><?= $dias_transcurridos ?></span></td>
        <!-- TERMINA FECHA DE VENCIMIENTO Y DIAS TRANSCURRIDOS -->
            <td class="small"><?= $tabla_ce_cc[$i]["CVE_SIDEFI"] ?></td>
            <td class="small">$<?= number_format($tabla_ce_cc[$i]["V_DOLARES"],2) ?></td>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_ADU_DESP"]."</cite>) ".$tabla_ce_cc[$i]["ADUANA_DESP"] ?></td>
            <td class="small"><?= $tabla_ce_cc[$i]["PLAZA"] ?></td>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_ALMACEN"]."</cite>) ".$tabla_ce_cc[$i]["ALMACEN"] ?></td>
            <td class="small">
              <a class="fancybox fancybox.iframe" href="<?= 'cartas_cupo_det.php?id_almacen_cc_ce='.$tabla_ce_cc[$i]["ID_ALMACEN"].'&id_num_cc_ce='.$tabla_ce_cc[$i]["NUMERO_CC"] ?>">
                <small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small>
              </a>
            </td>
          </tr>
        <?php
          }//cierra if para mostrar solo cartas cupo expedidas
            }//cierra for para tabla_ce_cc
        ?>
        </tbody>
        </table>
      </div>

    </div> <!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION DE LA TABLA CARTAS CUPO EXPEDIDAS ########################### -->




<!-- ############################ INICIA SECCION DE LA GRAFICA CARTAS CUPO NO ARRIBADAS ############################# -->
<?php if( $op_ce_cartas_cupo == false || $op_ce_cartas_cupo == 'NA' ) { ?>
<section>
  <div class="box box-warning">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica Cartas Cupo no arribadas <?= $f_widgets_en ?></h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">  <!-- box-body -->

    <ol class="breadcrumb">
    <?php if ( $op_ce_cartas_cupo == true ){ ?>
      <li>
        <form method="post" action="cartas_cupo.php"><!-- INICIA FORM TIPO POST -->
        <input type="hidden" value="" name="ce_cc_plaza">
        <input type="hidden" value="" name="ce_cc_almacen">
        <input type="hidden" value="" name="op_ce_cartas_cupo">
        <button type="submit" class="btn btn-link click_modal_cargando"><i class="fa fa-reply"></i> Regresar</button>
        </form>
      </li>
    <?php } ?>
      <!-- <li>
        <button type="button" id="btn_his_cce" class="btn btn-link" data-toggle="modal" data-target="#modal_fec_cartas_cupo"><i class="fa fa-history"></i> Historial</button>
      </li> -->
      <li>
        <a id="tab_fec_per_pros_co" data-toggle="tab" href="#ce_ccna_fec_per">
          <button type="submit" class="btn btn-link"><i class="fa fa-calendar"></i>  Fecha personalizada</button>
        </a>
      </li>
    </ol>

    <!-- INICIA CODE FECHA PERSONALIZADA -->
      <div class="tab-content">
        <div id="ce_ccna_fec_per" class="tab-pane fade">
          <form method="post">
            <div class="col-md-4">
              <!--  -->
              <div class="input-group" id="fec_rango_ce_cce">
                <div class="input-group-addon">
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_ini_ce_cc" id="fec_ini_ce_cc" value="<?= $fec_ini_ce_cc ?>" readonly>
                <div class="input-group-addon">
                  <i class="fa fa-calendar-plus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_fin_ce_cc" id="fec_fin_ce_cc" value="<?= $fec_fin_ce_cc ?>" readonly>
              </div>
              <!--  -->
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-sm bg-blue click_modal_cargando">OK</button>
            </div>
          </form>
        </div>
      </div><br>
    <!-- TERMINA CODE FECHA PERSONALIZADA -->

    <div class="row"><!-- div-row -->

      <div class="col-md-8">
        <div id="graf_bar_cp_noarribadas" style="height: 350px; "></div>
      </div>

      <div class="col-md-4">
        <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="small-box bg-yellow">
            <div class="inner">
              <p>C.C. NO ARRIBADAS</p>
            </div>
            <div style="font-size:50px;" class="icon">
              <img  src="../dist/img/ser_dierectos.png" >
            </div>
          </div>
            <div class="box-footer no-padding"><br>
            <table id="tabla_ce_cce_alm" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">NA</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $tabla_ccna_almacen = $obj_ce_cartas_cupo->tabla_ccna_almacen($ce_cc_plaza,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                for ($i=0; $i <count($tabla_ccna_almacen) ; $i++) {  ?>
                <tr>
                  <td class="small"><?= $tabla_ccna_almacen[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $tabla_ccna_almacen[$i]["PLAZA_SIGLAS"] ?></td>
                  <td>
                  <form method="post" action="cartas_cupo.php#NA">
                  <input type="hidden" name="op_ce_cartas_cupo" value="NA">
                  <input type="hidden" name="ce_cc_plaza" value="<?=$tabla_ccna_almacen[$i]["PLAZA"]?>">
                  <button type="submit" class="btn pull-right badge bg-yellow click_modal_cargando" name="ce_cc_almacen" value="<?= $tabla_ccna_almacen[$i]["ALMACEN"] ?>"><?= $tabla_ccna_almacen[$i]["TOTAL_CCNA"] ?></button>
                  </form>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
            </div>
          </div>
          <!-- /.widget-user -->
      </div>

    </div><!-- ./div-row -->

    </div> <!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION DE LA GRAFICA CARTAS CUPO NO ARRIBADAS ########################### -->

<!-- ############################ INICIA SECCION DE LA TABLA CARTAS CUPO NO ARRIBADAS ############################# -->
<?php if ( $op_ce_cartas_cupo == 'NA') { ?>
<section id="NA">
  <div class="box box-warning">
    <div class="box-header with-border">
      <!-- <h3 class="box-title"><i class="fa fa-table"></i> Tabla de Cartas Cupo Canceladas</h3>  -->
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">  <!-- box-body -->

      <h5 class="content-header text-blue text-center"><i class="fa fa-file-text-o"></i> Cartas Cupo no arribadas <?=$f_widgets_en ?></h5><hr>

      <div class="table-responsive">
        <table id="tabla_ce_ccc" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small">IMPORTADOR</th>
            <th class="small">BITÁCORA</th>
            <th class="small">FOLIO</th>
            <th class="small">FECHA EXPEDICIÓN</th>
            <th class="small">FECHA CANCELACIÓN</th>
            <th class="small">SIDEFI</th>
            <!-- <th class="small">CANTIDAD UMT</th> -->
            <th class="small">VALOR DÓLARES</th>
            <th class="small">ADUANA DESPACHO</th>
            <th class="small">PLAZA</th>
            <th class="small">ALMACEN</th>
            <th class="small">DETALLES</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $tabla_ce_cc = $obj_ce_cartas_cupo->tabla_ce_cc($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc,$op_ce_cartas_cupo,$ce_cc_plaza,$ce_cc_almacen);
          for ($i=0; $i <count($tabla_ce_cc) ; $i++) {//abre for para tabla_ce_cc
          if ($tabla_ce_cc[$i]["STATUS_CC"] == 'NA' || $tabla_ce_cc[$i]["STATUS_CC"] == 'PE'){//abre if para mostrar solo cartas cupo expedidas
          //---INICIA CODE CONSULTA DE FIRMA DE ACUSE EN BITACORA---//
            $bit_cc = $obj_ce_cartas_cupo->bit_cc($tabla_ce_cc[$i]["ID_NUMERO"],$tabla_ce_cc[$i]["ID_ALMACEN"]);
        ?>
          <tr>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_IMPORTADOR"]."</cite>) ".$tabla_ce_cc[$i]["IMPORTADOR"] ?></td>
        <?php
          //INICIA CODE BITACORA
            if ( count($bit_cc) == 0 ){
              echo '<td class="small"><code>NO EXISTEN REGISTRO DE EVENTO EN LA BITÁCORA</code></td>';
            }else{
              for ($j=0; $j <count($bit_cc) ; $j++) {
                  if ($bit_cc[$j]["FIRMA_ACUSE"] == false){
                  echo '<td class="small">ULTIMO EVENTO SIN FIRMA DE ACUSE <a class="click_ajax_bit'.$i.'"><small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small></a><div class="div_res_ajax'.$i.'" style="display:none"><div id="div_bitacora'.$i.'"></div></div></td>';
                }else{
                  echo '<td class="small">ULTIMO EVENTO CON FIRMA DE ACUSE <a class="click_ajax_bit'.$i.'"><small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small></a><div class="div_res_ajax'.$i.'" style="display:none"><div id="div_bitacora'.$i.'"></div></div></td>';
                }
              }
            }
            //TERMINA CODE BITACORA
        ?>
            <td class="small"><?= $tabla_ce_cc[$i]["ID_NUMERO"] ?></td>
            <td><span class="label label-success"><?= $tabla_ce_cc[$i]["FECHA_EXPEDICION"] ?></span></td>
            <td><span class="label label-danger"><?= $tabla_ce_cc[$i]["FECHA_CANCELACION"] ?></span></td>
            <!-- INICIA CODE TIPO DE CANCELACION -->
            <!-- TERMINA CODE TIPO DE CANCELACION -->
            <td class="small"><?= $tabla_ce_cc[$i]["CVE_SIDEFI"] ?></td>
        <!-- INICIA CODE CANTIDAD UMT -->
        <?php
          // $numero_cc_can_utm = $tabla_ce_cc[$i]["NUMERO_CC"]; $id_almacen_cc_can_utm = $tabla_ce_cc[$i]["ID_ALMACEN"];
          // $cantidad_umt_cc = $obj_ce_cartas_cupo->cantidad_umt_cc($numero_cc_can_utm,$id_almacen_cc_can_utm);
          // echo "<td class='small'>".$cantidad_umt_cc."</td>";
        ?>
        <!-- TERMINA CODE CANTIDAD UMT -->
            <td class="small">$<?= number_format($tabla_ce_cc[$i]["V_DOLARES"],2) ?></td>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_ADU_DESP"]."</cite>) ".$tabla_ce_cc[$i]["ADUANA_DESP"] ?></td>
            <td class="small"><?= $tabla_ce_cc[$i]["PLAZA"] ?></td>
            <td class="small"><cite>(<?= $tabla_ce_cc[$i]["ID_ALMACEN"] ?>)</cite><?= $tabla_ce_cc[$i]["ALMACEN"] ?></td>
            <td class="small">
              <a class="fancybox fancybox.iframe" href="<?= 'cartas_cupo_det.php?id_almacen_cc_ce='.$tabla_ce_cc[$i]["ID_ALMACEN"].'&id_num_cc_ce='.$tabla_ce_cc[$i]["NUMERO_CC"] ?>">
                <small class="badge badge bg-teal-active btn"> <i class="fa fa-eye"></i> Ver </small>
              </a>
            </td>
          </tr>
        <?php
          }//cierra if para mostrar solo cartas cupo expedidas
            }//cierra for para tabla_ce_cc
        ?>
        </tbody>
        </table>
      </div>

    </div> <!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION DE LA TABLA CARTAS CUPO NO ARRIBADAS ########################### -->





<!-- ############################ INICIA SECCION DE LA GRAFICA CARTAS CUPO ARRIBADAS ############################# -->
<?php if( $op_ce_cartas_cupo == false || $op_ce_cartas_cupo == 'RD' ) { ?>
<section>
  <div class="box box-success">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica Cartas Cupo Arribadas <?= $f_widgets_ac ?></h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">  <!-- box-body -->

    <ol class="breadcrumb">
      <?php if ( $op_ce_cartas_cupo == true ){ ?>
      <li>
        <form method="post" action="cartas_cupo.php"><!-- INICIA FORM TIPO POST -->
        <input type="hidden" value="" name="ce_cc_plaza">
        <input type="hidden" value="" name="ce_cc_almacen">
        <input type="hidden" value="" name="op_ce_cartas_cupo">
        <button type="submit" class="btn btn-link click_modal_cargando"><i class="fa fa-reply"></i> Regresar</button>
        </form>
      </li>
    <?php } ?>
      <li>
        <button type="button" id="btn_his_cca" class="btn btn-link" data-toggle="modal" data-target="#modal_fec_cartas_cupo"><i class="fa fa-history"></i> Historial</button>
      </li>
      <li>
        <a id="tab_fec_per_pros_co" data-toggle="tab" href="#ce_cca_fec_per">
          <button type="submit" class="btn btn-link"><i class="fa fa-calendar"></i>  Fecha personalizada</button>
        </a>
      </li>
    </ol>

    <!-- INICIA CODE FECHA PERSONALIZADA -->
      <div class="tab-content">
        <div id="ce_cca_fec_per" class="tab-pane fade">
          <form method="post">
            <div class="col-md-4">
              <!--  -->
              <div class="input-group" id="fec_rango_ce_cca">
                <div class="input-group-addon">
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_ini_ce_cc" id="fec_ini_ce_cc" value="<?= $fec_ini_ce_cc ?>" readonly>
                <div class="input-group-addon">
                  <i class="fa fa-calendar-plus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_fin_ce_cc" id="fec_fin_ce_cc" value="<?= $fec_fin_ce_cc ?>" readonly>
              </div>
              <!--  -->
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-sm bg-blue click_modal_cargando">OK</button>
            </div>
          </form>
        </div>
      </div><br>
    <!-- TERMINA CODE FECHA PERSONALIZADA -->

      <div class="row"><!-- div-row -->

        <div class="col-md-8">
          <div id="graf_bar_cp_arribadas" style="height: 350px; "></div>
        </div>

        <div class="col-md-4">
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="small-box bg-green">
            <div class="inner">
              <p>C.C. ARRIBADAS</p>
            </div>
            <div style="font-size:50px;" class="icon">
              <img  src="../dist/img/ser_dierectos.png" >
            </div>
          </div>
            <div class="box-footer no-padding"><br>
            <table id="tabla_ce_cca_alm" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">RD</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $tabla_cca_almacen = $obj_ce_cartas_cupo->tabla_cca_almacen($ce_cc_plaza,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                for ($i=0; $i <count($tabla_cca_almacen) ; $i++) {  ?>
                <tr>
                  <td class="small"><?= $tabla_cca_almacen[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $tabla_cca_almacen[$i]["PLAZA_SIGLAS"] ?></td>
                  <td>
                  <form method="post" action="cartas_cupo.php#RD">
                  <input type="hidden" name="op_ce_cartas_cupo" value="RD">
                  <input type="hidden" name="ce_cc_plaza" value="<?=$tabla_cca_almacen[$i]["PLAZA"]?>">
                  <button type="submit" class="btn pull-right badge bg-green click_modal_cargando" name="ce_cc_almacen" value="<?= $tabla_cca_almacen[$i]["ALMACEN"] ?>"><?= $tabla_cca_almacen[$i]["TOTAL_CC"] ?></button>
                  </form>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
            </div>
          </div>
          <!-- /.widget-user -->
        </div>

      </div><!-- ./div-row -->

    </div> <!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION DE LA GRAFICA CARTAS CUPO ARRIBADAS ########################### -->


<!-- ############################ INICIA SECCION DE LA TABLA CARTAS CUPO ARRIBADAS ############################# -->
<?php if ( $op_ce_cartas_cupo == 'RD') { ?>
<section id="RD">
  <div class="box box-success">
    <div class="box-header with-border">
      <!-- <h3 class="box-title"><i class="fa fa-table"></i> Tabla de Cartas Cupo Arribadas</h3>  -->
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">  <!-- box-body -->

      <h5 class="content-header text-blue text-center"><i class="ion-android-clipboard"></i> <?=$titulo_cc_ce ?></h5><hr>

      <div class="table-responsive">
        <table id="tabla_ce_cca" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small">IMPORTADOR</th>
            <th class="small">BITÁCORA</th>
            <th class="small">FOLIO</th>
            <th class="small">FECHA EXPEDICIÓN</th>
            <th class="small">FECHA ARRIBO</th>
            <th class="small">SIDEFI</th>
            <!-- <th class="small">CANTIDAD UMT</th> -->
            <th class="small">VALOR DÓLARES</th>
            <th class="small">ADUANA DESPACHO</th>
            <th class="small">PLAZA</th>
            <th class="small">ALMACEN</th>
            <th class="small">DETALLES</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $tabla_ce_cc = $obj_ce_cartas_cupo->tabla_cc_arribada($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc,$op_ce_cartas_cupo,$ce_cc_plaza,$ce_cc_almacen);
          for ($i=0; $i <count($tabla_ce_cc) ; $i++) {//abre for para tabla_ce_cc
          if ($tabla_ce_cc[$i]["STATUS_CC"] == 'RD' || $tabla_ce_cc[$i]["STATUS_CC"] == 'PE'){//abre if para mostrar solo cartas cupo arribadas
          //---INICIA CODE CONSULTA DE FIRMA DE ACUSE EN BITACORA---//
            $bit_cc = $obj_ce_cartas_cupo->bit_cc($tabla_ce_cc[$i]["ID_NUMERO"],$tabla_ce_cc[$i]["ID_ALMACEN"]);
        ?>
          <tr>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_IMPORTADOR"]."</cite>) ".$tabla_ce_cc[$i]["IMPORTADOR"] ?></td>
        <?php
          //INICIA CODE BITACORA
            if ( count($bit_cc) == 0 ){
              echo '<td class="small"><code>NO EXISTEN REGISTRO DE EVENTO EN LA BITÁCORA</code></td>';
            }else{
                for ($j=0; $j <count($bit_cc) ; $j++) {
                  if ($bit_cc[$j]["FIRMA_ACUSE"] == false){
                  echo '<td class="small">ULTIMO EVENTO SIN FIRMA DE ACUSE <a class="click_ajax_bit'.$i.'"><small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small></a><div class="div_res_ajax'.$i.'" style="display:none"><div id="div_bitacora'.$i.'"></div></div></td>';
                }else{
                  echo '<td class="small">ULTIMO EVENTO CON FIRMA DE ACUSE <a class="click_ajax_bit'.$i.'"><small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small></a><div class="div_res_ajax'.$i.'" style="display:none"><div id="div_bitacora'.$i.'"></div></div></td>';
                }
              }
            }
            //TERMINA CODE BITACORA
        ?>
            <td class="small"><?= $tabla_ce_cc[$i]["ID_NUMERO"] ?></td>
            <td><span class="label label-success"><?= $tabla_ce_cc[$i]["FECHA_EXPEDICION"] ?></span></td>
            <td><span class="label label-primary"><?= $tabla_ce_cc[$i]["FECHA_ARRIBO"] ?></span></td>
            <td class="small"><?= $tabla_ce_cc[$i]["CVE_SIDEFI"] ?></td>
        <!-- INICIA CODE CANTIDAD UMT -->
        <?php
          // $numero_cc_can_utm = $tabla_ce_cc[$i]["NUMERO_CC"]; $id_almacen_cc_can_utm = $tabla_ce_cc[$i]["ID_ALMACEN"];
          // $cantidad_umt_cc = $obj_ce_cartas_cupo->cantidad_umt_cc($numero_cc_can_utm,$id_almacen_cc_can_utm);
          // echo "<td class='small'>".$cantidad_umt_cc."</td>";
        ?>
        <!-- TERMINA CODE CANTIDAD UMT -->
            <td class="small">$<?= number_format($tabla_ce_cc[$i]["V_DOLARES"],2) ?></td>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_ADU_DESP"]."</cite>) ".$tabla_ce_cc[$i]["ADUANA_DESP"] ?></td>
            <td class="small"><?= $tabla_ce_cc[$i]["PLAZA"] ?></td>
            <td class="small"><cite>(<?= $tabla_ce_cc[$i]["ID_ALMACEN"] ?>)</cite><?= $tabla_ce_cc[$i]["ALMACEN"] ?></td>
            <td class="small">
              <a class="fancybox fancybox.iframe" href="<?= 'cartas_cupo_det.php?id_almacen_cc_ce='.$tabla_ce_cc[$i]["ID_ALMACEN"].'&id_num_cc_ce='.$tabla_ce_cc[$i]["NUMERO_CC"] ?>">
                <small class="badge badge bg-teal-active btn"> <i class="fa fa-eye"></i> Ver </small>
              </a>
            </td>
          </tr>
        <?php
          }//cierra if para mostrar solo cartas cupo arribadas
            }//cierra for para tabla_ce_cc
        ?>
        </tbody>
        </table>
      </div>

    </div> <!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION DE LA TABLA CARTAS CUPO ARRIBADAS ########################### -->

<!-- ############################ INICIA SECCION DE LA GRAFICA CARTAS CUPO NO ADUANADAS ############################# -->
<?php if( $op_ce_cartas_cupo == false || $op_ce_cartas_cupo == 'ND' ) { ?>
<section>
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica Cartas Cupo No Aduanadas <?= $f_widgets_en ?></h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">  <!-- box-body -->

    <!-- INICIA CODE MUESTRA MENSAJE SOLO EN ENERO -->
    <?php if ( $obj_ce_cartas_cupo->consulta_mes_base() == 01 ){ ?>
    <div class="alert alert-warning alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <h4><i class="icon fa fa-warning"></i> Aviso!</h4>
      Después de enero del <?=date("Y")?> toda la información de “Cartas cupo No Aduanadas” del año anterior, ya no se reflejarán en la gráfica principal de Cartas Cupo Expedida, a menos que la consulte con una fecha personalizada.
    </div>
    <?php } ?>
    <!-- TERMINA CODE MUESTRA MENSAJE SOLO EN ENERO -->


    <ol class="breadcrumb">
    <?php if ( $op_ce_cartas_cupo == true ){ ?>
      <li>
        <form method="post" action="cartas_cupo.php"><!-- INICIA FORM TIPO POST -->
        <input type="hidden" value="" name="ce_cc_plaza">
        <input type="hidden" value="" name="ce_cc_almacen">
        <input type="hidden" value="" name="op_ce_cartas_cupo">
        <button type="submit" class="btn btn-link click_modal_cargando"><i class="fa fa-reply"></i> Regresar</button>
        </form>
      </li>
    <?php } ?>
      <!-- <li>
        <button type="button" id="btn_his_cce" class="btn btn-link" data-toggle="modal" data-target="#modal_fec_cartas_cupo"><i class="fa fa-history"></i> Historial</button>
      </li> -->
      <li>
        <a id="tab_fec_per_pros_co" data-toggle="tab" href="#ce_cce_fec_per">
          <button type="submit" class="btn btn-link"><i class="fa fa-calendar"></i>  Fecha personalizada</button>
        </a>
      </li>
    </ol>

    <!-- INICIA CODE FECHA PERSONALIZADA -->
      <div class="tab-content">
        <div id="ce_cce_fec_per" class="tab-pane fade">
          <form method="post">
            <div class="col-md-4">
              <!--  -->
              <div class="input-group" id="fec_rango_ce_cce">
                <div class="input-group-addon">
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_ini_ce_cc" id="fec_ini_ce_cc" value="<?= $fec_ini_ce_cc ?>" readonly>
                <div class="input-group-addon">
                  <i class="fa fa-calendar-plus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_fin_ce_cc" id="fec_fin_ce_cc" value="<?= $fec_fin_ce_cc ?>" readonly>
              </div>
              <!--  -->
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-sm bg-blue click_modal_cargando">OK</button>
            </div>
          </form>
        </div>
      </div><br>
    <!-- TERMINA CODE FECHA PERSONALIZADA -->

    <div class="row"><!-- div-row -->

      <div class="col-md-8">
        <div id="graf_bar_cp_expedidas2" style="height: 350px; "></div>
      </div>

      <div class="col-md-4">
        <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="small-box bg-yellow">
            <div class="inner">
              <p>C.C. No Aduanadas</p>
            </div>
            <div style="font-size:50px;" class="icon">
              <img  src="../dist/img/ser_dierectos.png" >
            </div>
          </div>
            <div class="box-footer no-padding"><br>
            <table id="tabla_ce_cce_alm" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">NA</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $tabla_cce_almacen2 = $obj_ce_cartas_cupo->tabla_na_nd_almacen($ce_cc_plaza,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                for ($i=0; $i <count($tabla_cce_almacen2) ; $i++) {  ?>
                <tr>

                  <td class="small"><?= $tabla_cce_almacen2[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $tabla_cce_almacen2[$i]["PLAZA_SIGLAS"] ?></td>
                  <td>
                  <form method="post" action="cartas_cupo.php#ND">
                  <input type="hidden" name="op_ce_cartas_cupo" value="ND">
                  <input type="hidden" name="ce_cc_plaza" value="<?=$tabla_cce_almacen2[$i]["PLAZA"]?>">
                  <button type="submit" class="btn pull-right badge bg-aqua click_modal_cargando" name="ce_cc_almacen" value="<?= $tabla_cce_almacen2[$i]["ALMACEN"] ?>"><?= $tabla_cce_almacen2[$i]["TOTAL_CC"] ?></button>
                  </form>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
            </div>
          </div>
          <!-- /.widget-user -->
      </div>

    </div><!-- ./div-row -->

    </div> <!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION DE LA GRAFICA CARTAS CUPO NO ADUANADAS ########################### -->
<!-- ############################ INICIA SECCION DE LA TABLA CARTAS CUPO NO ADUANALES ############################# -->
<?php if ( $op_ce_cartas_cupo == 'ND') { ?>
<section id="RD">
  <div class="box box-success">
    <div class="box-header with-border">
      <!-- <h3 class="box-title"><i class="fa fa-table"></i> Tabla de Cartas Cupo Arribadas</h3>  -->
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">  <!-- box-body -->

      <h5 class="content-header text-blue text-center"><i class="ion-android-clipboard"></i> <?=$titulo_cc_ce ?></h5><hr>

      <div class="table-responsive">
        <table id="tabla_ce_cca" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small">IMPORTADOR</th>
            <th class="small">BITÁCORA</th>
            <th class="small">FOLIO</th>
            <th class="small">FECHA EXPEDICIÓN</th>
            <th class="small">SIDEFI</th>
            <!-- <th class="small">CANTIDAD UMT</th> -->
            <th class="small">VALOR DÓLARES</th>
            <th class="small">ADUANA DESPACHO</th>
            <th class="small">PLAZA</th>
            <th class="small">ALMACEN</th>
            <th class="small">DETALLES</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $tabla_ce_cc = $obj_ce_cartas_cupo->tabla_ce_nd($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc,$op_ce_cartas_cupo,$ce_cc_plaza,$ce_cc_almacen);
          for ($i=0; $i <count($tabla_ce_cc) ; $i++) {//abre for para tabla_ce_cc
          if ($tabla_ce_cc[$i]["STATUS_CC"] == 'NA' || $tabla_ce_cc[$i]["STATUS_CC"] == 'PE'){//abre if para mostrar solo cartas cupo arribadas
          //---INICIA CODE CONSULTA DE FIRMA DE ACUSE EN BITACORA---//
            $bit_cc = $obj_ce_cartas_cupo->bit_cc($tabla_ce_cc[$i]["ID_NUMERO"],$tabla_ce_cc[$i]["ID_ALMACEN"]);
        ?>
          <tr>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_IMPORTADOR"]."</cite>) ".$tabla_ce_cc[$i]["IMPORTADOR"] ?></td>
        <?php
          //INICIA CODE BITACORA
            if ( count($bit_cc) == 0 ){
              echo '<td class="small"><code>NO EXISTEN REGISTRO DE EVENTO EN LA BITÁCORA</code></td>';
            }else{
                for ($j=0; $j <count($bit_cc) ; $j++) {
                  if ($bit_cc[$j]["FIRMA_ACUSE"] == false){
                  echo '<td class="small">ULTIMO EVENTO SIN FIRMA DE ACUSE <a class="click_ajax_bit'.$i.'"><small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small></a><div class="div_res_ajax'.$i.'" style="display:none"><div id="div_bitacora'.$i.'"></div></div></td>';
                }else{
                  echo '<td class="small">ULTIMO EVENTO CON FIRMA DE ACUSE <a class="click_ajax_bit'.$i.'"><small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small></a><div class="div_res_ajax'.$i.'" style="display:none"><div id="div_bitacora'.$i.'"></div></div></td>';
                }
              }
            }
            //TERMINA CODE BITACORA
        ?>
            <td class="small"><?= $tabla_ce_cc[$i]["ID_NUMERO"] ?></td>
            <td><span class="label label-success"><?= $tabla_ce_cc[$i]["FECHA_EXPEDICION"] ?></span></td>
            <td class="small"><?= $tabla_ce_cc[$i]["CVE_SIDEFI"] ?></td>
        <!-- INICIA CODE CANTIDAD UMT -->
        <?php
          // $numero_cc_can_utm = $tabla_ce_cc[$i]["NUMERO_CC"]; $id_almacen_cc_can_utm = $tabla_ce_cc[$i]["ID_ALMACEN"];
          // $cantidad_umt_cc = $obj_ce_cartas_cupo->cantidad_umt_cc($numero_cc_can_utm,$id_almacen_cc_can_utm);
          // echo "<td class='small'>".$cantidad_umt_cc."</td>";
        ?>
        <!-- TERMINA CODE CANTIDAD UMT -->
            <td class="small">$<?= number_format($tabla_ce_cc[$i]["V_DOLARES"],2) ?></td>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_ADU_DESP"]."</cite>) ".$tabla_ce_cc[$i]["ADUANA_DESP"] ?></td>
            <td class="small"><?= $tabla_ce_cc[$i]["PLAZA"] ?></td>
            <td class="small"><cite>(<?= $tabla_ce_cc[$i]["ID_ALMACEN"] ?>)</cite><?= $tabla_ce_cc[$i]["ALMACEN"] ?></td>
            <td class="small">
              <a class="fancybox fancybox.iframe" href="<?= 'cartas_cupo_det.php?id_almacen_cc_ce='.$tabla_ce_cc[$i]["ID_ALMACEN"].'&id_num_cc_ce='.$tabla_ce_cc[$i]["NUMERO_CC"] ?>">
                <small class="badge badge bg-teal-active btn"> <i class="fa fa-eye"></i> Ver </small>
              </a>
            </td>
          </tr>
        <?php
          }//cierra if para mostrar solo cartas cupo arribadas
            }//cierra for para tabla_ce_cc
        ?>
        </tbody>
        </table>
      </div>

    </div> <!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION DE LA TABLA CARTAS CUPO NO ADUANALES ########################### -->


<!-- ############################ INICIA SECCION DE LA GRAFICA CARTAS CUPO CANCELADAS ############################# -->
<?php if( $op_ce_cartas_cupo == false || $op_ce_cartas_cupo == 'CC' ) { ?>
<section>
  <div class="box box-danger">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica Cartas Cupo Canceladas <?= $f_widgets_ac ?></h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">  <!-- box-body -->

    <ol class="breadcrumb">
      <?php if ( $op_ce_cartas_cupo == true ){ ?>
      <li>
        <form method="post" action="cartas_cupo.php"><!-- INICIA FORM TIPO POST -->
        <input type="hidden" value="" name="ce_cc_plaza">
        <input type="hidden" value="" name="ce_cc_almacen">
        <input type="hidden" value="" name="op_ce_cartas_cupo">
        <button type="submit" class="btn btn-link click_modal_cargando"><i class="fa fa-reply"></i> Regresar</button>
        </form>
      </li>
    <?php } ?>
      <li>
        <button type="button" id="btn_his_ccc" class="btn btn-link" data-toggle="modal" data-target="#modal_fec_cartas_cupo"><i class="fa fa-history"></i> Historial</button>
      </li>
      <li>
        <a id="tab_fec_per_pros_co" data-toggle="tab" href="#ce_ccc_fec_per">
          <button type="submit" class="btn btn-link"><i class="fa fa-calendar"></i>  Fecha personalizada</button>
        </a>
      </li>
    </ol>

    <!-- INICIA CODE FECHA PERSONALIZADA -->
      <div class="tab-content">
        <div id="ce_ccc_fec_per" class="tab-pane fade">
          <form method="post">
            <div class="col-md-4">
              <!--  -->
              <div class="input-group" id="fec_rango_ce_ccc">
                <div class="input-group-addon">
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_ini_ce_cc" id="fec_ini_ce_cc" value="<?= $fec_ini_ce_cc ?>" readonly>
                <div class="input-group-addon">
                  <i class="fa fa-calendar-plus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_fin_ce_cc" id="fec_fin_ce_cc" value="<?= $fec_fin_ce_cc ?>" readonly>
              </div>
              <!--  -->
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-sm bg-blue click_modal_cargando">OK</button>
            </div>
          </form>
        </div>
      </div><br>
    <!-- TERMINA CODE FECHA PERSONALIZADA -->

      <div class="row"><!-- div-row -->

        <div class="col-md-8">
         <div id="graf_bar_cp_canceladas" style="height: 350px; "></div>
        </div>

        <div class="col-md-4">
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="small-box bg-red">
            <div class="inner">
              <p>C.C. CANCELADAS</p>
            </div>
            <div style="font-size:50px;" class="icon">
              <img  src="../dist/img/ser_dierectos.png" >
            </div>
          </div>
            <div class="box-footer no-padding"><br>
            <table id="tabla_ce_ccc_alm" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">CA</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $tabla_ccc_almacen = $obj_ce_cartas_cupo->tabla_ccc_almacen($ce_cc_plaza,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                for ($i=0; $i <count($tabla_ccc_almacen) ; $i++) {  ?>
                <tr>
                  <td class="small"><?= $tabla_ccc_almacen[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $tabla_ccc_almacen[$i]["PLAZA_SIGLAS"] ?></td>
                  <td>
                  <form method="post" action="cartas_cupo.php#CC">
                  <input type="hidden" name="op_ce_cartas_cupo" value="CC">
                  <input type="hidden" name="ce_cc_plaza" value="<?=$tabla_ccc_almacen[$i]["PLAZA"]?>">
                  <button type="submit" class="btn pull-right badge bg-red click_modal_cargando" name="ce_cc_almacen" value="<?= $tabla_ccc_almacen[$i]["ALMACEN"] ?>"><?= $tabla_ccc_almacen[$i]["TOTAL_CC"] ?></button>
                  </form>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
            </div>
          </div>
          <!-- /.widget-user -->
        </div>

      </div><!-- ./div-row -->

    </div> <!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### Termina SECCION DE LA GRAFICA CARTAS CUPO CANCELADAS ########################### -->



<!-- ############################ INICIA SECCION DE LA TABLA CARTAS CUPO CANCELADAS ############################# -->
<?php if ( $op_ce_cartas_cupo == 'CC') { ?>
<section id="CC">
  <div class="box box-danger">
    <div class="box-header with-border">
      <!-- <h3 class="box-title"><i class="fa fa-table"></i> Tabla de Cartas Cupo Canceladas</h3>  -->
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">  <!-- box-body -->

      <h5 class="content-header text-blue text-center"><i class="fa fa-ban"></i> <?=$titulo_cc_ce ?></h5><hr>

      <div class="table-responsive">
        <table id="tabla_ce_ccc" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small">IMPORTADOR</th>
            <th class="small">BITÁCORA</th>
            <th class="small">FOLIO</th>
            <th class="small">FECHA EXPEDICIÓN</th>
            <th class="small">FECHA CANCELACIÓN</th>
            <th class="small">SIDEFI</th>
            <!-- <th class="small">CANTIDAD UMT</th> -->
            <th class="small">VALOR DÓLARES</th>
            <th class="small">ADUANA DESPACHO</th>
            <th class="small">PLAZA</th>
            <th class="small">ALMACEN</th>
            <th class="small">DETALLES</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $tabla_ce_cc = $obj_ce_cartas_cupo->tabla_ce_cc($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc,$op_ce_cartas_cupo,$ce_cc_plaza,$ce_cc_almacen);
          for ($i=0; $i <count($tabla_ce_cc) ; $i++) {//abre for para tabla_ce_cc
          if ($tabla_ce_cc[$i]["STATUS_CC"] == 'CC'){//abre if para mostrar solo cartas cupo expedidas
          //---INICIA CODE CONSULTA DE FIRMA DE ACUSE EN BITACORA---//
            $bit_cc = $obj_ce_cartas_cupo->bit_cc($tabla_ce_cc[$i]["ID_NUMERO"],$tabla_ce_cc[$i]["ID_ALMACEN"]);
        ?>
          <tr>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_IMPORTADOR"]."</cite>) ".$tabla_ce_cc[$i]["IMPORTADOR"] ?></td>
        <?php
          //INICIA CODE BITACORA
            if ( count($bit_cc) == 0 ){
              echo '<td class="small"><code>NO EXISTEN REGISTRO DE EVENTO EN LA BITÁCORA</code></td>';
            }else{
                for ($j=0; $j <count($bit_cc) ; $j++) {
                  if ($bit_cc[$j]["FIRMA_ACUSE"] == false){
                  echo '<td class="small">ULTIMO EVENTO SIN FIRMA DE ACUSE <a class="click_ajax_bit'.$i.'"><small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small></a><div class="div_res_ajax'.$i.'" style="display:none"><div id="div_bitacora'.$i.'"></div></div></td>';
                }else{
                  echo '<td class="small">ULTIMO EVENTO CON FIRMA DE ACUSE <a class="click_ajax_bit'.$i.'"><small class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </small></a><div class="div_res_ajax'.$i.'" style="display:none"><div id="div_bitacora'.$i.'"></div></div></td>';
                }
              }
            }

            //TERMINA CODE BITACORA
        ?>
            <td class="small"><?= $tabla_ce_cc[$i]["ID_NUMERO"] ?></td>
            <td><span class="label label-success"><?= $tabla_ce_cc[$i]["FECHA_EXPEDICION"] ?></span></td>
            <td><span class="label label-danger"><?= $tabla_ce_cc[$i]["FECHA_CANCELACION"] ?></span></td>
            <!-- INICIA CODE TIPO DE CANCELACION -->
            <!-- TERMINA CODE TIPO DE CANCELACION -->
            <td class="small"><?= $tabla_ce_cc[$i]["CVE_SIDEFI"] ?></td>
        <!-- INICIA CODE CANTIDAD UMT -->
        <?php
          // $numero_cc_can_utm = $tabla_ce_cc[$i]["NUMERO_CC"]; $id_almacen_cc_can_utm = $tabla_ce_cc[$i]["ID_ALMACEN"];
          // $cantidad_umt_cc = $obj_ce_cartas_cupo->cantidad_umt_cc($numero_cc_can_utm,$id_almacen_cc_can_utm);
          // echo "<td class='small'>".$cantidad_umt_cc."</td>";
        ?>
        <!-- TERMINA CODE CANTIDAD UMT -->
            <td class="small">$<?= number_format($tabla_ce_cc[$i]["V_DOLARES"],2) ?></td>
            <td class="small"><?= "<cite>(".$tabla_ce_cc[$i]["ID_ADU_DESP"]."</cite>) ".$tabla_ce_cc[$i]["ADUANA_DESP"] ?></td>
            <td class="small"><?= $tabla_ce_cc[$i]["PLAZA"] ?></td>
            <td class="small"><cite>(<?= $tabla_ce_cc[$i]["ID_ALMACEN"] ?>)</cite><?= $tabla_ce_cc[$i]["ALMACEN"] ?></td>
            <td class="small">
              <a class="fancybox fancybox.iframe" href="<?= 'cartas_cupo_det.php?id_almacen_cc_ce='.$tabla_ce_cc[$i]["ID_ALMACEN"].'&id_num_cc_ce='.$tabla_ce_cc[$i]["NUMERO_CC"] ?>">
                <small class="badge badge bg-teal-active btn"> <i class="fa fa-eye"></i> Ver </small>
              </a>
            </td>
          </tr>
        <?php
          }//cierra if para mostrar solo cartas cupo expedidas
            }//cierra for para tabla_ce_cc
        ?>
        </tbody>
        </table>
      </div>

    </div> <!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION DE LA TABLA CARTAS CUPO CANCELADAS ########################### -->

<!-- INICIA CODE PARA EL MODAL DE CARGANDO -->
  <div class="modal fade" id="modal_cargando_cc" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="img_cargando modal-content">
        <!-- <div class="modal-header">
        </div> -->
        <div class="modal-body">
          <div class="imgwrapper"><img height="230" width="300" class="img-responsive center-block" src="../dist/img/gif-argo-carnado-circulo_l2.gif"/></div>
        </div>
      </div>
    </div>
  </div>
<!-- TERMINA CODE PARA EL MODAL DE CARGANDO -->

  <!-- BOTON FLOTANTE VOLVER ATRAS -->
  <a href="#" class="back-to-top " rel="prev">Volver arriba</a>

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
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- CODE BOTON FLOTANTE -->
<script>
      jQuery(document).ready(function() {
        var offset = 220;
        var duration = 500;
        jQuery(window).scroll(function() {
          if (jQuery(this).scrollTop() > offset) {
            jQuery('.back-to-top').fadeIn(duration);
          } else {
            jQuery('.back-to-top').fadeIn(duration);//fadeOut
          }
        });

        jQuery('.back-to-top').click(function(event) {
          event.preventDefault();
          jQuery('html, body').animate({scrollTop: 0}, duration);
          return false;
        })
      });
    </script>
<!-- INICIA CODE CONSULTA DE AJAX EN BITACORA -->
<script>
<?php
for ($i=0; $i <count($tabla_ce_cc) ; $i++) {//abre for para tabla_ce_cc

?>
  $(".click_ajax_bit<?=$i?>").click(function() {
    $(".div_res_ajax<?=$i?>").toggle(1000);
    realizaProceso<?=$i?>(<?=$tabla_ce_cc[$i]["ID_NUMERO"]?>,<?=$tabla_ce_cc[$i]["ID_ALMACEN"]?>);
  });

function realizaProceso<?=$i?>(v_numero,v_almacen){
        var parametros = {
                "numero" : v_numero,
                "almacen" : v_almacen,
        };
        $.ajax({
                data:  parametros,
                url:   '../action/cartas_cupo_bit.php',
                type:  'post',
                beforeSend: function () {
                        $("#div_bitacora<?=$i?>").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                        $("#div_bitacora<?=$i?>").html(response);
                }
        });
}
<?php } ?>
</script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script>

$(function () {
    //Initialize Select2 Elements
    $(".dia_ce_cc").select2();
  });


//OCULTA SELECT HISTORIAL DE CARTAS CUPO
$( "#btn_his_cce" ).click(function() {
    $("#div_ce_cca").hide();
    $("#div_ce_cce").show();
    $("#div_ce_ccc").hide();
});

$( "#btn_his_cca" ).click(function() {
    $("#div_ce_cca").show();
    $("#div_ce_cce").hide();
    $("#div_ce_ccc").hide();
});

$( "#btn_his_ccc" ).click(function() {
    $("#div_ce_cca").hide();
    $("#div_ce_cce").hide();
    $("#div_ce_ccc").show();
});
</script>
<!-- date-range-picker -->
<script src="../plugins/daterangepicker/moment.min.js"></script>
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
          $('#fec_ini_ce_cc,#fec_ini_ce_cc,#fec_ini_ce_cc').val(start.format('DD-MM-YYYY'));
          $('#fec_fin_ce_cc,#fec_fin_ce_cc,#fec_fin_ce_cc').val(end.format('DD-MM-YYYY'));
        }
    );
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
<script>
$(document).ready(function() {
    $('#tabla_ce_cce_alm,#tabla_ce_cca_alm,#tabla_ce_ccc_alm').DataTable( {
        "scrollY":        "200px",
        "searching": true,
        //"dom": '<"top"i>rt<"bottom"flp><"clear">',
        "dom": '<"toolbar">frtip',
        stateSave: true,
        "scrollCollapse": true,
        "paging": false,
        "info": false,
        "language": {
          "url": "../plugins/datatables/Spanish.json"
        },
    } );
} );

$(document).ready(function() {
    $('#tabla_ce_cce,#tabla_ce_cca,#tabla_ce_ccc').DataTable({
      stateSave: true,
      "ordering": true,
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
            title: '<?= $titulo_cc_ce ?>',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: '<h5><?= $titulo_cc_ce ?></h5>',
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
} );
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
/*--------------- OPCIONES PARA LA GRAFICA DE BARRAS ---------------*/
  var options = {

    grid: {
          margin: 30,
          borderColor: { top: '#e5e5e5', right: '#e5e5e5', bottom: '#a5b2c0', left: '#a5b2c0' },
          hoverable: true
          },
      /////////////////////////
      tooltip: {
        show: true,
        content: "<div style='font-size: 13px; border: 2px solid; padding: 2px;background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: #367FA9;'> <font color='#5AD6F5'><b> %x </b></font><font color='#222D32'><b><div class='text-center'>Total = %n</div></b></font></div>",
        defaultTheme: false
      },
      ////////////////////////////
      series: {
              bars:{show: true,
                    align: 'center',
                    barWidth: 0.6,/*anchura grafica*/
                    lineWidth: 4,fill: 0.7,/*opacidad*/
                    },
              lines: { show: true },
              points: { show: false }
              },
      ////////////////////
      xaxis: {
              axisLabel: 'Plazas',
              axisLabelPadding: -20,
              axisLabelUseCanvas: true,
              mode: 'categories',
              tickLength: 20,/*linea*/
              //min: -1, /*separacion izquierda*/
              //max: 9, /*separacion derecha*/ /*rotateTicks: 135*/
            },
      //////////////////////
      yaxis: {
              axisLabel: 'Cartas Cupo Expedidas',
              axisLabelPadding: -20,
              axisLabelUseCanvas: true,
              tickDecimals: 0, min:0,
              },
      ////////////////////////
  };
/*--------------- DATOS PARA LA GRAFICA DE CARTAS CUPO EXPEDIDAS ---------------*/
var bar_cp_expedidas= [
              <?php
                $grafica_cc_expedidas = $obj_ce_cartas_cupo->grafica_cc_expedidas($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                for ($i=0; $i <count($grafica_cc_expedidas) ; $i++) {
                  echo "{ data: [['".'<form method="post" action="cartas_cupo.php#E"><input type="hidden" name="op_ce_cartas_cupo" value="E"><input type="hidden" name="ce_cc_almacen" value=""><button type="submit" name="ce_cc_plaza" value="'.$grafica_cc_expedidas[$i]["PLAZA"].'" class="btn btn-xs btn-link l_modal_cargando">'.$grafica_cc_expedidas[$i]["PLAZA_DECODE"]."</button></form>',
                        ".$grafica_cc_expedidas[$i]["TOTAL_CCE"]." ]],
                        color: '#00C0EF', },";
                }
              ?>
              ];
/*--------------- DATOS PARA LA GRAFICA DE CARTAS CUPO NO ARRIBADAS ---------------*/
var bar_cp_noarrinadas= [
              <?php
                $grafica_cc_noarribadas = $obj_ce_cartas_cupo->grafica_cc_noarribadas($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                for ($i=0; $i <count($grafica_cc_noarribadas) ; $i++) {
                  echo "{ data: [['".'<form method="post" action="cartas_cupo.php#NA"><input type="hidden" name="op_ce_cartas_cupo" value="NA"><input type="hidden" name="ce_cc_almacen" value=""><button type="submit" name="ce_cc_plaza" value="'.$grafica_cc_noarribadas[$i]["PLAZA"].'" class="btn btn-xs btn-link l_modal_cargando">'.$grafica_cc_noarribadas[$i]["PLAZA_DECODE"]."</button></form>',
                        ".$grafica_cc_noarribadas[$i]["TOTAL_CCNA"]." ]],
                        color: '#F39C12', },";
                }
              ?>
              ];
/*--------------- DATOS PARA LA GRAFICA DE CARTAS CUPO ARRIBADAS ---------------*/
var bar_cp_arribadas= [
              <?php
                $grafica_cc_arribadas = $obj_ce_cartas_cupo->grafica_cc_arribadas($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                for ($i=0; $i <count($grafica_cc_arribadas) ; $i++) {
                  echo "{ data: [['".'<form method="post" action="cartas_cupo.php#RD"><input type="hidden" name="op_ce_cartas_cupo" value="RD"><input type="hidden" name="ce_cc_almacen" value=""><button type="submit" name="ce_cc_plaza" value="'.$grafica_cc_arribadas[$i]["PLAZA"].'" class="btn btn-xs btn-link l_modal_cargando">'.$grafica_cc_arribadas[$i]["PLAZA_DECODE"]."</button></form>',
                        ".($grafica_cc_arribadas[$i]["TOTAL_CCE"]/*+$grafica_cc_arribadas[$i]["TOTAL_CPE"]*/)." ]],
                        color: '#00A65A', },";
                }
              ?>
              ];
/*--------------- DATOS PARA LA GRAFICA DE CARTAS CUPO CANCELADAS ---------------*/
var bar_cp_canceladas= [
              <?php
                $grafica_cc_canceladas = $obj_ce_cartas_cupo->grafica_cc_canceladas($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                for ($i=0; $i <count($grafica_cc_canceladas) ; $i++) {
                  echo "{ data: [['".'<form method="post" action="cartas_cupo.php#CC"><input type="hidden" name="op_ce_cartas_cupo" value="CC"><input type="hidden" name="ce_cc_almacen" value=""><button type="submit" name="ce_cc_plaza" value="'.$grafica_cc_canceladas[$i]["PLAZA"].'" class="btn btn-xs btn-link l_modal_cargando">'.$grafica_cc_canceladas[$i]["PLAZA_DECODE"]."</button></form>',
                        ".$grafica_cc_canceladas[$i]["TOTAL_CCE"]." ]],
                        color: '#DD4B39', },";
                }
              ?>
              ];
              /*-----------------DATOS PARA LA GRAFICA DE CARTAS CUPO NO ADUANADAS --------------*/
var bar_cp_no_aduana= [
                            <?php
                              $grafica_cc_noaduana = $obj_ce_cartas_cupo->grafica_cc_no_aduanadas($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc);
                              for ($i=0; $i <count($grafica_cc_noaduana) ; $i++) {
                                echo "{ data: [['".'<form method="post" action="cartas_cupo.php#ND"><input type="hidden" name="op_ce_cartas_cupo" value="ND"><input type="hidden" name="ce_cc_almacen" value=""><button type="submit" name="ce_cc_plaza" value="'.$grafica_cc_noaduana[$i]["PLAZA"].'" class="btn btn-xs btn-link l_modal_cargando">'.$grafica_cc_noaduana[$i]["PLAZA_DECODE"]."</button></form>',
                                      ".$grafica_cc_noaduana[$i]["TOTAL_CCE"]." ]],
                                      color: '#F39C12', },";
                              }
                            ?>
                            ];
/*--------------------------------------*/
 $(document).ready(function () {
    // ---------INICIA SCRIPT PARA GRAFICA CARTAS CUPO EXPEDIDAS ---------//
    <?php if( $op_ce_cartas_cupo == false || $op_ce_cartas_cupo == 'E' ) {  ?>
    $.plot($("#graf_bar_cp_expedidas"), bar_cp_expedidas, options);
    $(".flot-tick-label").css("zIndex","2");
    <?php } ?>
    // ---------INICIA SCRIPT PARA GRAFICA CARTAS NO ARRIBADAS---------//
    <?php if( $op_ce_cartas_cupo == false || $op_ce_cartas_cupo == 'NA' ) {  ?>
    $.plot($("#graf_bar_cp_noarribadas"), bar_cp_noarrinadas, options);
    $(".flot-tick-label").css("zIndex","2");
    <?php } ?>
    // ---------INICIA SCRIPT PARA GRAFICA CARTAS CUPO ARRIBADAS ---------//
    <?php if( $op_ce_cartas_cupo == false || $op_ce_cartas_cupo == 'RD' ) { ?>
    $.plot($("#graf_bar_cp_arribadas"), bar_cp_arribadas, options);
    $(".flot-tick-label").css("zIndex","2");
    <?php } ?>
    // ---------INICIA SCRIPT PARA GRAFICA CARTAS CUPO CANCELADAS ---------//
    <?php if( $op_ce_cartas_cupo == false || $op_ce_cartas_cupo == 'CC' ) { ?>
    $.plot($("#graf_bar_cp_canceladas"), bar_cp_canceladas, options);
    $(".flot-tick-label").css("zIndex","2");
    <?php } ?>
    //----------INICIA SCRIPT PARA GRAFICAS CARTAS CUPO NO ADUANADAS -------//
    <?php if( $op_ce_cartas_cupo == false || $op_ce_cartas_cupo == 'ND' ) {  ?>
    $.plot($("#graf_bar_cp_expedidas2"), bar_cp_no_aduana, options);
    $(".flot-tick-label").css("zIndex","2");
    <?php } ?>

});

    /* HABILITA EL BOTÓN PARA LAS PLAZAS */
    $(".flot-tick-label").css("zIndex","2");
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
//select change
$('.change_modal_cargando').on('change',function(){
  $.ajax({url: '../class/Cartas_cupo.php', success: function(result){
    $('#modal_cargando').modal('show');
  }});
});
//click
$('.l_modal_cargando').click(function(){
  $.ajax({url: '../class/Cartas_cupo.php', success: function(result){
    $('#modal_cargando').modal('show');
  }});
});
</script>
</html>
<?php conexion::cerrar($conn); ?>
