<?php
ini_set('display_errors', false);

if( $_SERVER['REQUEST_METHOD'] == 'POST')
{
  header("location: ".$_SERVER["PHP_SELF"]." ");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '9');
if($modulos_valida == 0)
{
  header('Location: index.php');
}

/*=============== INSTANCIA DEL OBJETO PARA PEDIMENTOS ===============*/
include_once '../class/Pedimentos.php';
$ins_obj_ce_pedeimentos = new Pedimentos();

/*--------------- SESSION PARA TIPO DE PEDIMENTO ---------------*/
if (isset($_POST["ce_cv_pedimento"]))
  $_SESSION["ce_cv_pedimento"] = $_POST["ce_cv_pedimento"];
  $ce_cv_pedimento = $_SESSION["ce_cv_pedimento"];
/*--------------- SESSION PARA PLAZA EN PEDIMENTO ---------------*/
if (isset($_POST["ce_pedi_plaza"]))
  $_SESSION["ce_pedi_plaza"] = $_POST["ce_pedi_plaza"];
  $ce_pedi_plaza = $_SESSION["ce_pedi_plaza"];
/*--------------- SESSION PARA ALMACEN EN PEDIMENTO ---------------*/
if (isset($_POST["ce_pedi_alm"]))
  $_SESSION["ce_pedi_alm"] = $_POST["ce_pedi_alm"];
  $ce_pedi_alm = $_SESSION["ce_pedi_alm"];
/*--------------- SESSION PARA EL DIA DEL PEDIMENTO ---------------*/
if( $_SESSION["ce_pedi_dia"] == false ){
  $_SESSION["ce_pedi_dia"] = $ins_obj_ce_pedeimentos->date_base();
  $ce_pedi_dia = $_SESSION["ce_pedi_dia"];
}else{
  if (isset($_POST["ce_pedi_dia"]))
  $_SESSION["ce_pedi_dia"] = $_POST["ce_pedi_dia"];
  $ce_pedi_dia = $_SESSION["ce_pedi_dia"];
}
/*--------------- SESSION PARA FECHA PERSONALIZADA ---------------*/
/* FECHA INICIO */
if (isset($_POST["fec_ini_ce_ped"]))
  $_SESSION["fec_ini_ce_ped"] = $_POST["fec_ini_ce_ped"];
  $fec_ini_ce_ped = $_SESSION["fec_ini_ce_ped"];
/*FECHA FIN */
if (isset($_POST["fec_fin_ce_ped"]))
  $_SESSION["fec_fin_ce_ped"] = $_POST["fec_fin_ce_ped"];
  $fec_fin_ce_ped = $_SESSION["fec_fin_ce_ped"];

/*=============== INSTANCIA DEL OBJETO PARA PEDIMENTOS ===============*/
$tabla_ped_alm = $ins_obj_ce_pedeimentos->tabla_ped_alm($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza);
/*==============TITULO PARA PEDIMENTOS FECHAS==============*/
if ($ce_pedi_dia == true && $fec_ini_ce_ped == true && $fec_fin_ce_ped == true)
{
  $titulo_ce_ped_fec = $fec_ini_ce_ped."/".$fec_fin_ce_ped ;
}else{
  $titulo_ce_ped_fec = $ce_pedi_dia ;
}
switch (true) {
  case ($ce_pedi_plaza == true) && ($ce_pedi_alm == true):
    $titulo_pla_alm = "PLAZA ".$ce_pedi_plaza." ALMACEN ".$ce_pedi_alm ;
    break;
  case ($ce_pedi_plaza == true):
    $titulo_pla_alm = "PLAZA ".$ce_pedi_plaza ;
    break;
}
///////////////////////////////////////////
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- *********** INICIA INCLUDE CSS *********** -->
<!-- DataTables -->
<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>Comercio Exterior</small>
      </h1>
      <h5 class="content-header text-blue text-center"> PEDIMENTOS <?php echo $ce_cv_pedimento." ".$titulo_pla_alm." ".$titulo_ce_ped_fec ;?> </h5>
    </section>
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->


<!-- ########################## INICIA MODAL HISTORIAL DE PEDIMENTOS ########################## -->
    <div class="modal fade" id="modal_his_pedi" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title text-center"><code>HISTORIAL</code></h4>
        </div>
        <div class="modal-body">
          <!-- ::::::::::::::::::::::::::: Inicia Select historial de pedimentos G1 :::::::::::::::::::::::::: -->
          <div id="div_ce_his_ped_g1" style="display: none;">
          <form method="post">
            <input type="hidden" name="fec_ini_ce_ped" value="">
            <input type="hidden" name="fec_fin_ce_ped" value="">
            <label>PEDIMENTOS G1</label>
            <select  onchange="this.form.submit();" name="ce_pedi_dia" class="form-control historial_ped" style="width: 100%;">
              <option value=''></option>
              <option value='<?= $ins_obj_ce_pedeimentos->date_base() ?>'><?= $ins_obj_ce_pedeimentos->date_base() ?></option>
              <?php
              $historial_pedimento = $ins_obj_ce_pedeimentos->historial_pedimento($ce_pedi_plaza,$ce_pedi_alm);
                for ($i=0; $i <count($historial_pedimento) ; $i++) {
                  if ( $historial_pedimento[$i]["CVE_PED"] == "G1"){
                  echo "<option value='".$historial_pedimento[$i]["FECHA"]."'>".$historial_pedimento[$i]["FECHA"]."</option>";
                  }
                }
              ?>
            </select>
          </form>
          </div>
          <!-- ::::::::::::::::::::::::::: Termina Select historial de pedimentos G1 :::::::::::::::::::::::::: -->
          <!-- ::::::::::::::::::::::::::: Inicia Select historial de pedimentos E1 :::::::::::::::::::::::::: -->
          <div id="div_ce_his_ped_e1" style="display: none;">
          <form method="post">
            <input type="hidden" name="fec_ini_ce_ped" value="">
            <input type="hidden" name="fec_fin_ce_ped" value="">
            <label>PEDIMENTOS E1</label>
            <select  onchange="this.form.submit();" name="ce_pedi_dia" class="form-control historial_ped" style="width: 100%;">
              <option value=''></option>
              <option value='<?= $ins_obj_ce_pedeimentos->date_base() ?>'><?= $ins_obj_ce_pedeimentos->date_base() ?></option>
              <?php
                for ($i=0; $i <count($historial_pedimento) ; $i++) {
                  if ( $historial_pedimento[$i]["CVE_PED"] == "E1"){
                  echo "<option value='".$historial_pedimento[$i]["FECHA"]."'>".$historial_pedimento[$i]["FECHA"]."</option>";
                  }
                }
              ?>
            </select>
          </form>
          </div>
          <!-- ::::::::::::::::::::::::::: Termina Select historial de pedimentos E1 :::::::::::::::::::::::::: -->
          <!-- ::::::::::::::::::::::::::: Inicia Select historial de pedimentos K2 :::::::::::::::::::::::::: -->
          <div id="div_ce_his_ped_k2" style="display: none;">
          <form method="post">
            <input type="hidden" name="fec_ini_ce_ped" value="">
            <input type="hidden" name="fec_fin_ce_ped" value="">
            <label>PEDIMENTOS K2</label>
            <select  onchange="this.form.submit();" name="ce_pedi_dia" class="form-control historial_ped" style="width: 100%;">
              <option value=''></option>
              <option value='<?= $ins_obj_ce_pedeimentos->date_base() ?>'><?= $ins_obj_ce_pedeimentos->date_base() ?></option>
              <?php
                for ($i=0; $i <count($historial_pedimento) ; $i++) {
                  if ( $historial_pedimento[$i]["CVE_PED"] == "K2"){
                  echo "<option value='".$historial_pedimento[$i]["FECHA"]."'>".$historial_pedimento[$i]["FECHA"]."</option>";
                  }
                }
              ?>
            </select>
          </form>
          </div>
          <!-- ::::::::::::::::::::::::::: Termina Select historial de pedimentos K2 :::::::::::::::::::::::::: -->

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
<!-- ########################## TERMINA MODAL HISTORIAL DE PEDIMENTOS ########################## -->


<!-- ######################################## Inicio de Widgets ######################################### -->
    <section><!-- Inicia la seccion de los Widgets -->
      <div class="row">
      <!-- Widgets Numero de cargas -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
            <?php
              //MUESTRA VOLOR SI HAY PEDIMENTOS
              $widgets_ped = $ins_obj_ce_pedeimentos->widgets_ped($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza,$ce_pedi_alm);
              for ($i=0; $i <count($widgets_ped) ; $i++) {
                if ($widgets_ped[$i]["CVE_PED"] == "G1"){
                  $widgets_ped_g1[$i] = $i;
                  echo "<h3>".$widgets_ped[$i]["TOTAL_PED"]."</h3>";
                }
                $val_ped_g1 = count($widgets_ped_g1);
              }
              //MUESTRA CERO SI NO ENCUENTRA PEDIMENTOS
              if ($val_ped_g1 == false){
                echo "<h3>0</h3>";
              }
            ?>

            <?php
              //MUESTRA VOLOR SI HAY PEDIMENTOS
              $widgets_ped2 = $ins_obj_ce_pedeimentos->widgets_pedValue($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza,$ce_pedi_alm);
              for ($i=0; $i <count($widgets_ped2) ; $i++) {
                if ($widgets_ped2[$i]["CVE_PED"] == "G1"){
                  $widgets_ped_g2[$i] = $i;
                  if ($widgets_ped2[$i]["TOTALG1"] > 0 ) {
                    echo "<h5>$".number_format($widgets_ped2[$i]["TOTALG1"], 2)."</h5>";
                  }
                }
                $val_ped_g2 = count($widgets_ped_g2);
              }
              //MUESTRA CERO SI NO ENCUENTRA PEDIMENTOS
              if ($val_ped_g2 == false){
                echo "<h5>0.00</h5>";
              }
            ?>


              <p>PEDIMENTOS G1</p>
            </div>
            <div class="icon">
              <i class="fa fa-edit"></i>
            </div>
            <form method="post">
             <button type="submit" name="ce_cv_pedimento" value="G1" class="btn bg-aqua-active  btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
        <!-- Widgets Numero de descargas -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <?php
              //MUESTRA VOLOR SI HAY PEDIMENTOS
              for ($i=0; $i <count($widgets_ped) ; $i++) {
                if ($widgets_ped[$i]["CVE_PED"] == "E1"){
                  $widgets_ped_e1[$i] = $i;
                  echo "<h3>".$widgets_ped[$i]["TOTAL_PED"]."</h3>";
                }
                $val_ped_e1 = count($widgets_ped_e1);
              }
              //MUESTRA CERO SI NO ENCUENTRA PEDIMENTOS
              if ($val_ped_e1 == false){
                echo "<h3>0</h3>";
              }
            ?>

            <?php
              //MUESTRA VOLOR SI HAY PEDIMENTOS
              $widgets_pede2 = $ins_obj_ce_pedeimentos->widgets_pedValue($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza,$ce_pedi_alm);
              for ($i=0; $i <count($widgets_ped2) ; $i++) {
                if ($widgets_pede2[$i]["CVE_PED"] == "E1"){
                  $widgets_ped_e2[$i] = $i;
                  if ($widgets_pede2[$i]["TOTALG1"] > 0 ) {
                    echo "<h5>$".number_format($widgets_pede2[$i]["TOTALG1"], 2)."</h5>";
                  }
                }
                $val_ped_e2 = count($widgets_ped_e2);
                //var_dump($val_ped_e2);
              }
              //MUESTRA CERO SI NO ENCUENTRA PEDIMENTOS
              if ($val_ped_e2 == false){
                echo "<h5>0.00</h5>";
              }
            ?>

              <p>PEDIMENTOS E1</p>
            </div>
            <div class="icon">
              <i class="fa fa-file-text-o"></i>
            </div>
            <form method="post">
             <button type="submit" name="ce_cv_pedimento" value="E1" class="btn bg-green-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
        <!-- Widgets Cargas a tiempo -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <?php
              //MUESTRA VOLOR SI HAY PEDIMENTOS
              for ($i=0; $i <count($widgets_ped) ; $i++) {
                if ($widgets_ped[$i]["CVE_PED"] == "K2"){
                  $widgets_ped_k2[$i] = $i;
                  echo "<h3>".$widgets_ped[$i]["TOTAL_PED"]."</h3>";
                }
                $val_ped_k2 = count($widgets_ped_k2);
              }
              //MUESTRA CERO SI NO ENCUENTRA PEDIMENTOS
              if ($val_ped_k2 == false){
                echo "<h3>0</h3>";
              }
            ?>

            <?php
              //MUESTRA VOLOR SI HAY PEDIMENTOS
              $widgets_pedee2 = $ins_obj_ce_pedeimentos->widgets_pedValue($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza,$ce_pedi_alm);
              for ($i=0; $i <count($widgets_ped2) ; $i++) {
                if ($widgets_pedee2[$i]["CVE_PED"] == "K2"){
                  $widgets_ped_k2[$i] = $i;
                  if ($widgets_pedee2[$i]["TOTALG1"] > 0 ) {
                    echo "<h5>$".number_format($widgets_pedee2[$i]["TOTALG1"], 2)."</h5>";
                  }
                }
                $val_ped_k2 = count($widgets_ped_k2);
              }
              //MUESTRA CERO SI NO ENCUENTRA PEDIMENTOS
              if ($val_ped_k2 == false){
                echo "<h5>0.00</h5>";
              }
            ?>
              <p>PEDIMENTOS K2</p>
            </div>
            <div class="icon">
              <i class="fa fa-file-o"></i>
            </div>
          <form method="post">
            <button type="submit" name="ce_cv_pedimento" value="K2" class="btn bg-yellow-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
          </form>
          </div>
        </div>
        <!-- Widgets Desfasados -->
        <!-- <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="small-box bg-red">
            <div class="inner">
              <h3>0</h3>
              <p>Widgets</p>
            </div>
            <div class="icon">
              <i class="fa fa-warning"></i>
            </div>
              <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-red btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
          </div>
        </div> -->
        <!-- Termino Widgets Desfasados -->
      </div>
      <!-- /.row -->
      </section><!-- Termina la seccion de los Widgets -->
<!-- ######################################### Termino de Widgets ######################################### -->



<!-- ############################ INICIA SECCION PARA LA GRAFICA DE DONA PEDIMENTOS G1 ############################# -->
<?php if ($ce_cv_pedimento == false || $ce_cv_pedimento == "G1"){ ?>
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title"> <i class="fa fa-pie-chart"></i> PEDIMENTOS G1 </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body"><!-- box-body -->

      <ol class="breadcrumb">
        <?php if ($ce_cv_pedimento == true){ ?>
        <li>
          <form method="post"><!-- INICIA FORM TIPO POST -->
          <input type="hidden" name="ce_pedi_plaza" value="">
          <input type="hidden" name="ce_pedi_alm" value="">
          <input type="hidden" name="ce_cv_pedimento" value="">
          <button type="submit" class="btn btn-link"><i class="fa fa-reply"></i> Regresar</button>
          </form>
        </li>
        <?php } ?>
        <li>
          <button type="button" id="btn_his_ce_ped_g1" class="btn btn-link" data-toggle="modal" data-target="#modal_his_pedi"><i class="fa fa-history"></i> Historial</button>
        </li>
        <li>
          <a id="tab_ce_fec_per_ped" data-toggle="tab" href="#ce_ped_g1_fec_per">
            <button type="submit" class="btn btn-link"><i class="fa fa-calendar"></i>  Fecha personalizada</button>
          </a>
        </li>
      </ol>

      <!-- INICIA CODE FECHA PERSONALIZADA -->
      <div class="tab-content">
        <div id="ce_ped_g1_fec_per" class="tab-pane fade">
          <form method="post">
            <div class="col-md-4">
              <!--  -->
              <div class="input-group" id="fec_rango_ce_ped_g1">
                <div class="input-group-addon">
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_ini_ce_ped" id="fec_ini_ce_ped" value="<?= $fec_ini_ce_ped ?>" readonly>
                <div class="input-group-addon">
                  <i class="fa fa-calendar-plus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_fin_ce_ped" id="fec_fin_ce_ped" value="<?= $fec_fin_ce_ped ?>" readonly>
              </div>
              <!--  -->
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-sm bg-blue">OK</button>
            </div>
          </form>
        </div>
      </div><br><br>
    <!-- TERMINA CODE FECHA PERSONALIZADA -->

      <div class="row"><!-- div-row -->

        <!-- INICIA GRAFICA PEDIMENTO G1 -->
        <div class="col-md-7">
          <h5 class="content-header text-blue text-center"> PEDIMENTOS G1 EN PLAZAS</h5><hr>
          <div  id="grafica_ce_pedi_g1" style="height: 350px;"></div>
        </div>
        <!-- TERMINA GRAFICA PEDIMENTO G1 -->
        <!-- INICIA TABLA PEDIMENTOS G1 EN ALMACEN  -->
        <div class="col-md-4">
          <br><br>
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="small-box bg-aqua">
            <div class="inner">
              <p>PEDIMENTOS G1 EN ALMACEN</p>
            </div>
            <div style="font-size:50px;" class="icon">
              <img  src="../dist/img/ser_dierectos.png" >
            </div>
          </div>
            <div class="box-footer no-padding"><br>
            <table id="tabla_ped_g1_alm" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">G1</th>
                </tr>
              </thead>
              <tbody>
              <?php
                for ($i=0; $i < count($tabla_ped_alm); $i++) {
                if ($tabla_ped_alm[$i]["CVE_PED"] == "G1"){
              ?>
                <tr>
                  <td class="small"><?= $tabla_ped_alm[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $tabla_ped_alm[$i]["PLAZA"] ?></td>
                  <td>
                  <form method="post">
                  <input type="hidden" name="ce_cv_pedimento" value="G1">
                  <input type="hidden" name="ce_pedi_plaza" value="<?=$tabla_ped_alm[$i]["PLAZA"]?>">
                  <button style="background-color:<?=$tabla_ped_alm[$i]["COLOR"]?>;color:#fff" type="submit" class="btn pull-right badge" name="ce_pedi_alm" value="<?= $tabla_ped_alm[$i]["ALMACEN"] ?>"> <?= $tabla_ped_alm[$i]["T_PEDIMENTO"] ?></button>
                  </form>
                  </td>
                </tr>
              <?php } } ?>
              </tbody>
            </table>
            </div>
          </div>
          <!-- /.widget-user -->
        </div>
        <!-- TERMINA TABLA PEDIMENTOS G1 EN ALMACEN  -->
        <div class="col-md-1">
        </div>

      </div><!-- /.div-row -->

    </div><!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION PARA LA GRAFICA DE DONA PEDIMENTOS G1 ########################### -->




<!-- ############################ INICIA SECCION PARA LA GRAFICA DE DONA PEDIMENTOS E1 ############################# -->
<?php if ($ce_cv_pedimento == false || $ce_cv_pedimento == "E1"){ ?>
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title"> <i class="fa fa-pie-chart"></i> PEDIMENTOS E1 </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body"><!-- box-body -->

      <ol class="breadcrumb">
        <?php if ($ce_cv_pedimento == true){ ?>
        <li>
          <form method="post"><!-- INICIA FORM TIPO POST -->
          <input type="hidden" name="ce_pedi_plaza" value="">
          <input type="hidden" name="ce_pedi_alm" value="">
          <input type="hidden" name="ce_cv_pedimento" value="">
          <button type="submit" class="btn btn-link"><i class="fa fa-reply"></i> Regresar</button>
          </form>
        </li>
        <?php } ?>
          <button type="button" id="btn_his_ce_ped_e1" class="btn btn-link" data-toggle="modal" data-target="#modal_his_pedi"><i class="fa fa-history"></i> Historial</button>
        </li>
        <li>
          <a id="tab_ce_fec_per_ped" data-toggle="tab" href="#ce_ped_e1_fec_per">
            <button type="submit" class="btn btn-link"><i class="fa fa-calendar"></i>  Fecha personalizada</button>
          </a>
        </li>
      </ol>

      <!-- INICIA CODE FECHA PERSONALIZADA -->
      <div class="tab-content">
        <div id="ce_ped_e1_fec_per" class="tab-pane fade">
          <form method="post">
            <div class="col-md-4">
              <!--  -->
              <div class="input-group" id="fec_rango_ce_ped_e1">
                <div class="input-group-addon">
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_ini_ce_ped" id="fec_ini_ce_ped" value="<?= $fec_ini_ce_ped ?>" readonly>
                <div class="input-group-addon">
                  <i class="fa fa-calendar-plus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_fin_ce_ped" id="fec_fin_ce_ped" value="<?= $fec_fin_ce_ped ?>" readonly>
              </div>
              <!--  -->
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-sm bg-blue">OK</button>
            </div>
          </form>
        </div>
      </div><br><br>
    <!-- TERMINA CODE FECHA PERSONALIZADA -->

      <div class="row"><!-- div-row -->
      <!-- INICIA GRAFICA PEDIMENTO E1 -->
        <div class="col-md-7">
          <h5 class="content-header text-blue text-center"> PEDIMENTOS E1 EN PLAZAS</h5><hr>
          <div  id="grafica_ce_pedi_e1" style="height: 350px;"></div>
        </div>
      <!-- TERMINA GRAFICA PEDIMENTO E1 -->
      <!-- INICIA TABLA PEDIMENTOS E1 EN ALMACEN  -->
        <div class="col-md-4">
          <br><br>
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="small-box bg-green">
            <div class="inner">
              <p>PEDIMENTOS E1 EN ALMACEN</p>
            </div>
            <div style="font-size:50px;" class="icon">
              <img  src="../dist/img/ser_dierectos.png" >
            </div>
          </div>
            <div class="box-footer no-padding"><br>
            <table id="tabla_ped_e1_alm" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">E1</th>
                </tr>
              </thead>
              <tbody>
              <?php
                for ($i=0; $i < count($tabla_ped_alm); $i++) {
                if ($tabla_ped_alm[$i]["CVE_PED"] == "E1"){
              ?>
                <tr>
                  <td class="small"><?= $tabla_ped_alm[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $tabla_ped_alm[$i]["PLAZA"] ?></td>
                  <td>
                  <form method="post">
                  <input type="hidden" name="ce_cv_pedimento" value="E1">
                  <input type="hidden" name="ce_pedi_plaza" value="<?=$tabla_ped_alm[$i]["PLAZA"]?>">
                  <button style="background-color:<?=$tabla_ped_alm[$i]["COLOR"]?>;color:#fff" type="submit" class="btn pull-right badge" name="ce_pedi_alm" value="<?= $tabla_ped_alm[$i]["ALMACEN"] ?>"> <?= $tabla_ped_alm[$i]["T_PEDIMENTO"] ?></button>
                  </form>
                  </td>
                </tr>
              <?php } } ?>
              </tbody>
            </table>
            </div>
          </div>
          <!-- /.widget-user -->
        </div>
      <!-- TERMINA TABLA PEDIMENTOS E1 EN ALMACEN  -->
      <!-- DIV SEPARA TABLA DE LADO DERECHO -->
        <div class="col-md-1">
        </div>

      </div><!-- /.div-row -->

    </div><!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION PARA LA GRAFICA DE DONA PEDIMENTOS E1 ########################### -->



<!-- ############################ INICIA SECCION PARA LA GRAFICA DE DONA PEDIMENTOS K2 ############################# -->
<?php if ($ce_cv_pedimento == false || $ce_cv_pedimento == "K2"){ ?>
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title"> <i class="fa fa-pie-chart"></i> PEDIMENTOS K2 </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body"><!-- box-body -->

      <ol class="breadcrumb">
        <?php if ($ce_cv_pedimento == true){ ?>
        <li>
          <form method="post"><!-- INICIA FORM TIPO POST -->
          <input type="hidden" name="ce_pedi_plaza" value="">
          <input type="hidden" name="ce_pedi_alm" value="">
          <input type="hidden" name="ce_cv_pedimento" value="">
          <button type="submit" class="btn btn-link"><i class="fa fa-reply"></i> Regresar</button>
          </form>
        </li>
        <?php } ?>
          <button type="button" id="btn_his_ce_ped_k2" class="btn btn-link" data-toggle="modal" data-target="#modal_his_pedi"><i class="fa fa-history"></i> Historial</button>
        </li>
        <li>
          <a id="tab_ce_fec_per_ped" data-toggle="tab" href="#ce_ped_k2_fec_per">
            <button type="submit" class="btn btn-link"><i class="fa fa-calendar"></i>  Fecha personalizada</button>
          </a>
        </li>
      </ol>

      <!-- INICIA CODE FECHA PERSONALIZADA -->
      <div class="tab-content">
        <div id="ce_ped_k2_fec_per" class="tab-pane fade">
          <form method="post">
            <div class="col-md-4">
              <!--  -->
              <div class="input-group" id="fec_rango_ce_ped_k2">
                <div class="input-group-addon">
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_ini_ce_ped" id="fec_ini_ce_ped" value="<?= $fec_ini_ce_ped ?>" readonly>
                <div class="input-group-addon">
                  <i class="fa fa-calendar-plus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_fin_ce_ped" id="fec_fin_ce_ped" value="<?= $fec_fin_ce_ped ?>" readonly>
              </div>
              <!--  -->
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-sm bg-blue">OK</button>
            </div>
          </form>
        </div>
      </div><br><br>
    <!-- TERMINA CODE FECHA PERSONALIZADA -->

      <div class="row"><!-- div-row -->
      <!-- INICIA GRAFICA PEDIMENTO K2 -->
        <div class="col-md-7">
          <h5 class="content-header text-blue text-center"> PEDIMENTOS K2 EN PLAZAS</h5><hr>
          <div  id="grafica_ce_pedi_k2" style="height: 350px;"></div>
        </div>
      <!-- TERMINA GRAFICA PEDIMENTO K2 -->
      <!-- INICIA TABLA PEDIMENTOS K2 EN ALMACEN  -->
        <div class="col-md-4">
          <br><br>
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="small-box bg-yellow">
            <div class="inner">
              <p>PEDIMENTOS K2 EN ALMACEN</p>
            </div>
            <div style="font-size:50px;" class="icon">
              <img  src="../dist/img/ser_dierectos.png" >
            </div>
          </div>
            <div class="box-footer no-padding"><br>
            <table id="tabla_ped_k2_alm" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">K2</th>
                </tr>
              </thead>
              <tbody>
              <?php
                for ($i=0; $i < count($tabla_ped_alm); $i++) {
                if ($tabla_ped_alm[$i]["CVE_PED"] == "K2"){
              ?>
                <tr>
                  <td class="small"><?= $tabla_ped_alm[$i]["ALMACEN"] ?></td>
                  <td class="small"><?= $tabla_ped_alm[$i]["PLAZA"] ?></td>
                  <td>
                  <form method="post">
                  <input type="hidden" name="ce_cv_pedimento" value="K2">
                  <input type="hidden" name="ce_pedi_plaza" value="<?=$tabla_ped_alm[$i]["PLAZA"]?>">
                  <button style="background-color:<?=$tabla_ped_alm[$i]["COLOR"]?>;color:#fff" type="submit" class="btn pull-right badge" name="ce_pedi_alm" value="<?= $tabla_ped_alm[$i]["ALMACEN"] ?>"> <?= $tabla_ped_alm[$i]["T_PEDIMENTO"] ?></button>
                  </form>
                  </td>
                </tr>
              <?php } } ?>
              </tbody>
            </table>
            </div>
          </div>
          <!-- /.widget-user -->
        </div>
      <!-- TERMINA TABLA PEDIMENTOS K2 EN ALMACEN  -->
      <!-- DIV SEPARA TABLA DE LADO DERECHO -->
        <div class="col-md-1">
        </div>

      </div><!-- /.div-row -->

    </div><!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION PARA LA GRAFICA DE DONA PEDIMENTOS K2 ########################### -->


<!-- ############################ INICIA SECCION TABLA PARA PEDIMENTOS ############################# -->
<?php if ($ce_cv_pedimento==true){ ?>
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title"> </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body"><!-- box-body -->

      <h4 class="page-header text-blue text-center"><i class="fa fa-pie-chart"></i> PEDIMENTOS <?= $ce_cv_pedimento ?></h4>

      <div class="table-responsive"><!-- table-responsive -->
      <table id="tabla_ce_pedimentos" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="small">IMPORTADOR</th>
                <th class="small">#PEDIMENTO</th>
                <th class="small">A4</th>
                <th class="small">VALOR ADUANA</th>
                <th class="small">SIDEFI</th>
                <th class="small">FECHA PAGO</th>
                <th class="small">IMPUESTOS PAGADOS</th>
            </tr>
        </thead>
        <tbody>
          <?php
           $tabla_pedimentos= $ins_obj_ce_pedeimentos->tabla_pedimentos($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza,$ce_pedi_alm,$ce_cv_pedimento);
           for ($i=0; $i <count($tabla_pedimentos) ; $i++) {
          ?>
            <tr>
                <td class="small"><?= "<cite>(".$tabla_pedimentos[$i]["ID_IMPORTADOR"]."</cite>) ".$tabla_pedimentos[$i]["IMPORTADOR"] ?></td>
                <td class="small"><?= substr($tabla_pedimentos[$i]["N_PEDIMENTO"], -11) ?></td>
                <td class="small"><?= substr($tabla_pedimentos[$i]["PEDIMENTO_A4"], -11) ?></td>
                <td class="small">$<?= number_format($tabla_pedimentos[$i]["V_ADUANA"],2) ?></td>
                <td class="small"><?= $tabla_pedimentos[$i]["N_SIDEFI"] ?></td>
                <td class="small"><?= $tabla_pedimentos[$i]["FEC_PAGO"] ?></td>
                <td class="small">$<?= number_format($tabla_pedimentos[$i]["T_IMPUESTOS_PAG"],2) ?></td>
            </tr>
          <?php } ?>
        </tbody>
    </table>
    </div><!-- /.table-responsive -->

    </div><!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION TABLA PARA PEDIMENTOS ########################### -->




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
    $('#tabla_ped_g1_alm,#tabla_ped_e1_alm,#tabla_ped_k2_alm').DataTable( {
        "scrollY":        "200px",
        "searching": false,
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
    $('#tabla_ce_pedimentos').DataTable({
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
            title: 'PEDIMENTOS <?= $ce_cv_pedimento." ".$titulo_pla_alm." ".$titulo_ce_ped_fec ?>',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: '<h5>PEDIMENTOS <?= $ce_cv_pedimento." ".$titulo_pla_alm." ".$titulo_ce_ped_fec ?></h5>'
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          },


        ],
//---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });
} );
</script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script>

$(function () {
    //Initialize Select2 Elements
    $(".historial_ped").select2();
  });

//MUESTRA EL SELECT DEL HISTORIAL PEDIMENTOS G1
$("#btn_his_ce_ped_g1").click(function(){
  $("#div_ce_his_ped_g1").show();
  $("#div_ce_his_ped_e1").hide();
  $("#div_ce_his_ped_k2").hide();
});
//MUESTRA EL SELECT DEL HISTORIAL PEDIMENTOS E1
$("#btn_his_ce_ped_e1").click(function(){
  $("#div_ce_his_ped_g1").hide();
  $("#div_ce_his_ped_e1").show();
  $("#div_ce_his_ped_k2").hide();
});
//MUESTRA EL SELECT DEL HISTORIAL PEDIMENTOS K2
$("#btn_his_ce_ped_k2").click(function(){
  $("#div_ce_his_ped_g1").hide();
  $("#div_ce_his_ped_e1").hide();
  $("#div_ce_his_ped_k2").show();
});


</script>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script>

$('#fec_rango_ce_ped_g1,#fec_rango_ce_ped_e1,#fec_rango_ce_ped_k2').daterangepicker(
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
          $('#fec_ini_ce_ped,#fec_ini_ce_ped,#fec_ini_ce_ped').val(start.format('DD-MM-YYYY'));
          $('#fec_fin_ce_ped,#fec_fin_ce_ped,#fec_fin_ce_ped').val(end.format('DD-MM-YYYY'));
        }
    );
</script>
<!-- FLOT CHARTS -->
<script src="../plugins/flot/jquery.flot.min.js"></script>
<!-- FLOT PIE CHARTS 3D -->
<script src="../plugins/flot/jquery.flot.pie3d.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="../plugins/flot/jquery.flot.resize.min.js"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="../plugins/flot/jquery.flot.pie_old.js"></script>
<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
<script src="../plugins/flot/jquery.flot.categories.js"></script>
<!-- FLOT ORDER BARS  -->
<script src="../plugins/flot/jquery.flot.orderBars.js"></script>
<!-- FLOT  bar charts click text -->
<script src="../plugins/flot/jquery.flot.tooltip.js"></script>
<script>
/*--------------- OPCIONES PARA LA GRAFICA DE DONA ---------------*/
  var options = {
     series: {
        pie3d: {
        stroke: { //define linea separadora
            width: 8,
            color: '#FFFFFF'
          } ,
          show: true,
          radius: 1, //radius: 1,  tamño radio del circulo
          tilt: 1,//rotacion de angulo
          innerRadius: 95,//radio dona o pastel
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
      content: "<div style='font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;'>  <b> %s</b></font> <br>  <div class='text-center'>  Porcentaje = %p.2% <br> Total = %n</div>  </div>",
      defaultTheme: false
      }
};
/*--------------- DATOS PARA LA GRAFICA DE PEDIMENTOS G1 ---------------*/
    var donutDataG1 = [
      <?php
        $graf_dona_ped = $ins_obj_ce_pedeimentos->graf_dona_ped($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza);
        for ($i=0; $i < count($graf_dona_ped); $i++) {
        if ($graf_dona_ped[$i]["CVE_PED"] == "G1"){
          $plaza = $graf_dona_ped[$i]["PLAZA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA
      ?>
      {
      label: '<form method="post">'
             +'<input type="hidden" name="ce_cv_pedimento" value="G1">'
             +'<input type="hidden" name="ce_pedi_alm" value="">'
             +'<button style="text-align: center;color: white;text-shadow: -1px -1px 1px #333, 1px -1px 1px #333, -1px 1px 1px #333, 1px 1px 1px #333;" class="btn btn-link btn-xs" style="color:#222D32;" name="ce_pedi_plaza" value="<?=$graf_dona_ped[$i]["PLAZA"]?>">'
             +'<?=$plaza_corta?>'
             +'</form>',
      data: <?=$graf_dona_ped[$i]["T_PEDIMENTO"]?> ,
      color: '<?=$graf_dona_ped[$i]["COLOR"]?>'
      },
      <?php } } ?>
    ];
/*--------------- DATOS PARA LA GRAFICA DE PEDIMENTOS E1 ---------------*/
    var donutDataE1 = [
      <?php
        for ($i=0; $i < count($graf_dona_ped); $i++) {
        if ($graf_dona_ped[$i]["CVE_PED"] == "E1"){
          $plaza = $graf_dona_ped[$i]["PLAZA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA
      ?>
      {
      label: '<form method="post">'
             +'<input type="hidden" name="ce_cv_pedimento" value="E1">'
             +'<input type="hidden" name="ce_pedi_alm" value="">'
             +'<button style="text-align: center;color: white;text-shadow: -1px -1px 1px #333, 1px -1px 1px #333, -1px 1px 1px #333, 1px 1px 1px #333;" class="btn btn-link btn-xs" style="color:#222D32;" name="ce_pedi_plaza" value="<?=$graf_dona_ped[$i]["PLAZA"]?>">'
             +'<?=$plaza_corta?>'
             +'</form>',
      data: <?=$graf_dona_ped[$i]["T_PEDIMENTO"]?> ,
      color: '<?=$graf_dona_ped[$i]["COLOR"]?>'
      },
      <?php } } ?>
    ];
/*--------------- DATOS PARA LA GRAFICA DE PEDIMENTOS K2 ---------------*/
    var donutDataK2 = [
      <?php
        for ($i=0; $i < count($graf_dona_ped); $i++) {
        if ($graf_dona_ped[$i]["CVE_PED"] == "K2"){
          $plaza = $graf_dona_ped[$i]["PLAZA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA
      ?>
      {
      label: '<form method="post">'
             +'<input type="hidden" name="ce_cv_pedimento" value="K2">'
             +'<input type="hidden" name="ce_pedi_alm" value="">'
             +'<button style="text-align: center;color: white;text-shadow: -1px -1px 1px #333, 1px -1px 1px #333, -1px 1px 1px #333, 1px 1px 1px #333;" class="btn btn-link btn-xs" style="color:#222D32;" name="ce_pedi_plaza" value="<?=$graf_dona_ped[$i]["PLAZA"]?>">'
             +'<?=$plaza_corta?>'
             +'</form>',
      data: <?=$graf_dona_ped[$i]["T_PEDIMENTO"]?> ,
      color: '<?=$graf_dona_ped[$i]["COLOR"]?>'
      },
      <?php } } ?>
    ];
/*--------------------------------------*/
 $(document).ready(function () {

    <?php if ($ce_cv_pedimento == false || $ce_cv_pedimento == "G1"){ ?>
    if (($(donutDataG1).length > 0)){
    $.plot($("#grafica_ce_pedi_g1"), donutDataG1, options);
    }
    <?php } ?>

    <?php if ($ce_cv_pedimento == false || $ce_cv_pedimento == "E1"){ ?>
    if (($(donutDataE1).length > 0)){
    $.plot($("#grafica_ce_pedi_e1"), donutDataE1, options);
    }
    <?php } ?>

    <?php if ($ce_cv_pedimento == false || $ce_cv_pedimento == "K2"){ ?>
    if (($(donutDataK2).length > 0)){
    $.plot($("#grafica_ce_pedi_k2"), donutDataK2, options);
    }
    <?php } ?>
});
  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return label
        +"<div style='text-align: center;color: white;text-shadow: -1px -1px 1px #333, 1px -1px 1px #333, -1px 1px 1px #333, 1px 1px 1px #333;'>"+series.data[0][1] + "</div>"
        + "</div></button>";//<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + series.data[0][1] + '</div>
  }
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
