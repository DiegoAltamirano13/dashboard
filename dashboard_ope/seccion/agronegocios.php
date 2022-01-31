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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '6');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/* HORA MEXICO */
$time = time();
date_default_timezone_set("America/Mexico_City");

/* INICIA RECUPERACION DE VALORES DEL FILTRO INDEX */

if (empty($_SESSION['counter']))
  $_SESSION['counter'] = 1;
else
  $_SESSION['counter']++;

$agro_plaza = $_SESSION["agro_plaza"];
  if ( $_SESSION['counter'] == 1 )  {
    //if($_SESSION['area']==3){
      $agro_plaza = $_SESSION["agro_plaza"];
    //}else {
      //$_SESSION["agro_plaza"] = "CÓRDOBA (ARGO)";
      //$agro_plaza = $_SESSION["agro_plaza"];
    //}
  }else{
    if( isset($_POST["agro_plaza"]))
    $_SESSION["agro_plaza"] = $_POST["agro_plaza"];
    $agro_plaza = $_SESSION["agro_plaza"];
  }

  //$agro_plaza = $_SESSION["agro_plaza"];

/* TERMINA RECUPERACION DE VALORES DEL FILTRO INDEX*/

//----------------INICIA SESSIONES PARA AGRONEGOCIOS----------------//
/*SESSION PARA PLAZA AGRONEGOCIO*/
/*if (empty($_SESSION['counter']))
  $_SESSION['counter'] = 1;
else
  $_SESSION['counter']++;

if ( $_SESSION['counter'] == 1 )  {
  $_SESSION["agro_plaza"] = "CÓRDOBA (ARGO)";
  $agro_plaza = $_SESSION["agro_plaza"];
}else{
  if( isset($_POST["agro_plaza"]))
  $_SESSION["agro_plaza"] = $_POST["agro_plaza"];
  $agro_plaza = $_SESSION["agro_plaza"];
}*/

/*SESSION PARA EL HISTORIAL AGRONEGOCIO*/
if ($_SESSION["agro_historial"]==false){
  $_SESSION["agro_historial"] = date("d-m-Y", $time);
  $agro_historial = $_SESSION["agro_historial"];
}else{
if( isset($_POST["agro_historial"]))
  $_SESSION["agro_historial"] = $_POST["agro_historial"];
  $agro_historial = $_SESSION["agro_historial"];
}
/*SESSION PARA FECHA PERSONALIZADA AGRONEGOCIO*/
//FECHA INICIO
   #echo $_POST["fec_ini_agro"];
   #echo $_SESSION["fec_ini_agro"];
$destroySessionFlag = filter_input(INPUT_POST, "destroySession");
if ($destroySessionFlag == 1) {
  unset($_SESSION['fec_ini_agro']);
  unset($_SESSION['fec_fin_agro']);
}
if( isset($_POST["fec_ini_agro"]))
  $_SESSION["fec_ini_agro"] = $_POST["fec_ini_agro"];
  $fec_ini_agro = $_SESSION["fec_ini_agro"];

//FECHA FIN
if( isset($_POST["fec_fin_agro"]))
  $_SESSION["fec_fin_agro"] = $_POST["fec_fin_agro"];
  $fec_fin_agro = $_SESSION["fec_fin_agro"];
/*SESSION PARA ALMACEN AGRONEGOCIO*/
if( isset($_POST["agro_almacen"]))
  $_SESSION["agro_almacen"] = $_POST["agro_almacen"];
  $agro_almacen = $_SESSION["agro_almacen"];
/*SESSION PARA CLIENTE AGRONEGOCIO*/
if( isset($_POST["agro_cliente"]))
  $_SESSION["agro_cliente"] = $_POST["agro_cliente"];
  $agro_cliente = $_SESSION["agro_cliente"];
//----------------TERMINA SESSIONES PARA AGRONEGOCIOS----------------//
//titulo para fecha
if($agro_historial == true && $fec_ini_agro == true && $fec_fin_agro == true ){
  $titulo_fecha = $fec_ini_agro."|".$fec_fin_agro;
}else{
  $titulo_fecha = $agro_historial;
}
//----------------INICIA INSTANCIAS DE OBJETOS ----------------//
include_once '../class/Agronegocio_carga.php';
$obj_agro_carga = new Consulta_carga($agro_plaza,$agro_historial, $fec_ini_agro, $fec_fin_agro, $agro_almacen, $agro_cliente);
$obj_agro_carga_status = new Consulta_status_carga($agro_plaza,$agro_historial, $fec_ini_agro, $fec_fin_agro, $agro_almacen, $agro_cliente);
include_once '../class/Agronegocio_descarga.php';
$obj_agro_descarga = new Consulta_descarga($agro_plaza,$agro_historial, $fec_ini_agro, $fec_fin_agro, $agro_almacen, $agro_cliente);
$obj_agro_descarga_status = new Consulta_status_descarga($agro_plaza,$agro_historial, $fec_ini_agro, $fec_fin_agro, $agro_almacen, $agro_cliente);
//----------------TERMINA INSTANCIAS DE OBJETOS ----------------//

///////////////////////////////////////////
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<!-- CSS PARA DISEÑO DE LINEA DE TIEMPO -->
<link rel="stylesheet" href="../plugins/line_time.css">
<style type="text/css" media="screen">
 div.dataTables_wrapper {
      width: 800px;
      margin: 0 auto;
  }
</style>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>

        Dashboard
        <small>Agronegocios</small>
        <?php //if($_SESSION['area']==3){echo "<center><h4> PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>";} ?><!--FILTRAR UNICAMENTE P/DEPTO. OPERACIONES -->
        <?php echo "<center><h4>PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>"; ?><!--FILTRO GENERAL -->
      </h1>
    </section>
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->


<!-- ############################ INICIA SECCION DE MODALS ############################# -->
<!-- INICIA MODAL HISTORIAL CARGAS -->
  <div class="modal fade" id="modal_his_carga" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="div_historial_cargas[]" style="display:none;">Historial Cargas</h4>
          <h4 class="modal-title" id="div_historial_descargas[]" style="display:none;">Historial Descargas</h4>
        </div>
        <div id="content_img_modal_his[]" class="modal-body"><!-- modal-body -->

          <!-- ::::::::::::::::::::::::::::::::: Inicia Select Operaciones cargas :::::::::::::::::::::::::::::::: -->
          <div id="div_historial_cargas[]" style="display:none;">
          <form method="post">
            <input type="hidden" name="fec_ini_agro" value="">
            <input type="hidden" name="fec_fin_agro" value="">
            <select id="click_modal_his[]" onchange="this.form.submit();" name="agro_historial" class="form-control agro_historial" style="width: 100%;">
              <option value=''></option>
              <?php
              $historial_carga = $obj_agro_carga->select_historial_carga($agro_plaza);
              for ($i=0; $i <count($historial_carga) ; $i++) {
                echo '<option value="'.$historial_carga[$i]["FECHA"].'">'.$historial_carga[$i]["FECHA"].'</option>';
              }
              ?>
            </select>
          </form>
          </div>
          <!-- ::::::::::::::::::::::::::::::::: Termina Select Operaciones cargas :::::::::::::::::::::::::::::::: -->

          <!-- ::::::::::::::::::::::::::::::::: Inicia Select Operaciones descargas :::::::::::::::::::::::::::::::: -->
          <div id="div_historial_descargas[]" style="display:none;">
          <form method="post">
            <input type="hidden" name="fec_ini_agro" value="">
            <input type="hidden" name="fec_fin_agro" value="">
            <select id="click_modal_his[]"  onchange="this.form.submit();" name="agro_historial" class="form-control agro_historial" style="width: 100%;">
              <option value=''></option>
              <?php
              $historial_descarga = $obj_agro_descarga->select_historial_descarga($agro_plaza);
              for ($i=0; $i <count($historial_descarga) ; $i++) {
                echo '<option value="'.$historial_descarga[$i]["FECHA"].'">'.$historial_descarga[$i]["FECHA"].'</option>';
              }
              ?>
            </select>
          </form>
          </div>
          <!-- ::::::::::::::::::::::::::::::::: Termina Select Operaciones descargas :::::::::::::::::::::::::::::::: -->

        </div><!-- ./modal-body -->
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<!-- TERMINA MODAL HISTORIAL CARGAS -->
<!-- ############################ TERMINA SECCION DE MODALS ############################# -->
<section>
  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">OPERACIONES DESCARGAS</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

    <p class="text-center">
      <h4>
       <div class="text-light-blue" align="center">
          <i class="fa fa-truck fa-flip-horizontal"><i class="fa fa-long-arrow-down"></i></i> DESCARGAS-ENTRADAS <?= $agro_plaza." ".$agro_almacen." ".$agro_cliente." ".$titulo_fecha ?>
       </div>
      </h4>
    </p>

      <!-- inicia menu opciones descarga -->
      <ol class="breadcrumb">
      <?php
      if ($agro_plaza == true  or isset($_SESSION['fec_ini_agro']) == true ){
      ?>
        <li>
        <form method="post">
          <input type="hidden" name="agro_plaza" value="">
          <input type="hidden" name="agro_almacen" value="">
          <input type="hidden" name="agro_cliente" value="">
          <input type="hidden" name="destroySession" value="1">
          <?php
           ?>
          <div class="content_text_btn6"><button type='submit' class='click_btn_text6 btn btn-link'><i class="ion-arrow-left-a"></i> Regresar</button></div>
        </form>
        </li>
        <?php } ?>
        <li>
          <div class="btn-group">
            <?php //if ($_SESSION['area']!=3){?>
              <!--<button type='button' class='content_text_btn7 btn btn-link dropdown-toggle' data-toggle="dropdown"><i class="fa fa-cube"></i> Filtrar por Plaza</button>-->
            <?php //} ?>
            <ul class="dropdown-menu" role="menu">
            <form method="post">
              <input type="hidden" name="agro_almacen" value="">
              <input type="hidden" name="agro_cliente" value="">
              <?php if ($agro_plaza==true){ echo '<li><button name="agro_plaza" value="" class="click_btn_text7 btn btn-link">ALL</button></li>';} ?>
              <li><button name="agro_plaza" value="CÓRDOBA (ARGO)" class='click_btn_text7 btn btn-link'>CÓRDOBA (ARGO)</button></li>
              <li><button name="agro_plaza" value="OCCIDENTE (ARGO)" class='click_btn_text7 btn btn-link'>OCCIDENTE (ARGO)</button></li>
            </form>
            </ul>
          </div>
        </li>
        <li>
          <div class="btn-group">
           <button type='button' class='content_text_btn8 btn btn-link dropdown-toggle' data-toggle="dropdown"><i class="fa fa-cubes"></i> Filtrar por Almacen</button>
            <ul class="dropdown-menu" role="menu">
            <?php
            if ($agro_almacen == true) { echo '<li><a><form method="POST"><input type="hidden" name="agro_cliente" value=""><button type="submit" name="agro_almacen" value="" class="click_btn_text8 btn btn-link">ALL</button></form></a></li>';}
            $filtro_almacen_descarga = $obj_agro_descarga->select_almacen($agro_plaza);
            for ($i=0; $i <count($filtro_almacen_descarga) ; $i++) {
              echo '<li><a><form method="POST"><input type="hidden" name="agro_cliente" value=""><button type="submit" name="agro_almacen" value="'.$filtro_almacen_descarga[$i]["ALMACEN"].'" class="click_btn_text8 btn btn-link">'.$filtro_almacen_descarga[$i]["ALMACEN"].'</button></form></a></li>';
            }
            ?>
            </ul>
          </div>
        </li>
        <li>
          <div class="btn-group">
           <button type='button' class='content_text_btn9 btn btn-link dropdown-toggle' data-toggle="dropdown"><i class="fa fa-briefcase"></i> Filtrar por Cliente</button>
            <ul class="dropdown-menu" role="menu">
            <?php
            if ($agro_cliente == true){echo '<li><a><form method="POST"><button type="submit" name="agro_cliente" value="" class="click_btn_text9 btn btn-link">ALL</button></form></a></li>';}
            $filtro_almacen_descarga = $obj_agro_descarga->select_cliente($agro_plaza,$agro_almacen);
            for ($i=0; $i <count($filtro_almacen_descarga) ; $i++) {
              echo '<li><a><form method="POST"><button type="submit" name="agro_cliente" value="'.$filtro_almacen_descarga[$i]["CLIENTE"].'" class="click_btn_text9 btn btn-link">'.$filtro_almacen_descarga[$i]["CLIENTE"].'</button></form></a></li>';
            }
            ?>
            </ul>
          </div>
        </li>
        <li>
          <button type='button' class='btn btn-link' data-toggle="modal" data-target="#modal_his_carga" id="btn_his_des"><i class="fa fa-history"></i> Historial</button>
        </li>
        <li>
          <a data-toggle="tab" href="#fec_per_agro_des">
          <i class="fa fa-calendar"></i> <button type='button' class='btn btn-link'>Fecha Personalizada</button>
          </a>
        </li>
      </ol>
    <!-- termina menu opciones descarga -->

<!-- INICIA CODE FECHA PERSONALIZADA DESCARGA -->
    <div class="content_text_btn10 row">
    <div class="tab-content">
      <div id="fec_per_agro_des" class="tab-pane fade">
      <form method="post">
        <div class="col-md-5">
        <div class="input-group" id="fec_rango_agro[]">
          <div class="btn input-group-addon">
            <i class="fa fa-calendar-minus-o"></i> Inicio
          </div>
          <input type="text" class="form-control pull-right" name="fec_ini_agro" id="fec_ini_agro[]" readonly>
          <div class="input-group-addon">
            <i class="fa fa-calendar-plus-o"></i> Fin
          </div>
          <input type="text" class="form-control pull-right" name="fec_fin_agro" id="fec_fin_agro[]" readonly>
        </div>
        </div>
        <div class="col-md-2">
          <button type='submit' class='click_btn_text10 btn btn-sm bg-blue'>Ok</button>
        </div>
      </form>
      </div>
    </div>
    </div><br>
<!-- TERMINA CODE FECHA PERSONALIZADA DESCARGA -->

<!-- ######################################## Inicio de Widgets para descargas ######################################### -->
      <div class="row"><!-- row -->
      <!-- WIDGETS DESCARGAS -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-aqua">
          <span class="info-box-icon"><i class="fa fa-truck fa-flip-horizontal"><i class="fa fa-long-arrow-down"></i></i></span>
            <div class="info-box-content">
              <center><span class="info-box-text"><b>DESCARGAS</b></span></center>
              <span class="info-box-text">Total: <b id="widgets_descargas_t_agro[]">0</b></span>
              <span class="info-box-text">Finalizado: <b id="widgets_descargas_fin_agro[]">0</b></span>
              <span class="info-box-text">En proceso: <b id="widgets_descargas_pro_agro[]">0</b></span>
            </div>
        </div>
      </div>
      <!-- WIDGETS ENTRADAS -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green">
          <span class="info-box-icon"><i class="fa fa-cloud-download"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">ENTRADAS</span>
              <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>
              <?php
              $widgets_ton_descargas = $obj_agro_descarga->widgets_ton_descargas($agro_plaza);
              for ($i=0; $i <count($widgets_ton_descargas) ; $i++) {
                $ton_descarga_cor = $widgets_ton_descargas[$i]["T_NETAS_ORACBA"];
                $ton_descarga_occ = $widgets_ton_descargas[$i]["T_NETAS_ORAR06"];
                echo '<span class="info-box-text">'.($ton_descarga_cor+$ton_descarga_occ).' TON</span>';
              }
              ?>
            </div>
          </div>
        </div>
        <!-- WIDGETS SACOS ENTRADAS -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon bg-yellow"><i class="ion ion-bag"></i></span>
            <div class="info-box-content bg-yellow"><!-- bg-primary -->
              <span class="info-box-text">SACOS-ENTRADAS</span>
              <div class="progress">
                  <div class="progress-bar" style="width: 100%"></div>
              </div>
                <table>
                  <tr>
                    <th>PLAZA</th>
                    <th>/BULTOS</th>
                    <th>/KG</th>
                  </tr>
                <?php
                $widgets_t_sacos_descarga = $obj_agro_descarga->widgets_t_sacos_descarga($agro_plaza);
                for ($i=0; $i <count($widgets_t_sacos_descarga) ; $i++) {
                  if ( $widgets_t_sacos_descarga[$i]["ID_UME"] == 59 ){
                ?>
                  <tr>
                    <td align="center"><?= $widgets_t_sacos_descarga[$i]["PLAZA_SIG"] ?></td>
                    <td align="center"><?= $widgets_t_sacos_descarga[$i]["BULTOS"] ?></td>
                    <td align="center"><?= $widgets_t_sacos_descarga[$i]["FACTOR"] ?></td>
                  </tr>
                <?php } } ?>
                </table>
            </div>
          </div>
        </div>
        <!-- WIDGETS SUPER SACOS ENTRADAS -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon bg-red"><i class="ion ion-bag"></i></span>
            <div class="info-box-content bg-red"><!-- bg-primary -->
            <span class="info-box-text">SUPERSACOS-ENTRADAS</span>
            <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>

              <table>
                <tr>
                  <th>PLAZA</th>
                  <th>/BULTOS</th>
                  <th>/KG</th>
                </tr>
               <?php
                for ($i=0; $i <count($widgets_t_sacos_descarga) ; $i++) {
                  if ( $widgets_t_sacos_descarga[$i]["ID_UME"] == 70 ){
              ?>
                <tr>
                  <td align="center"><?= $widgets_t_sacos_descarga[$i]["PLAZA_SIG"] ?></td>
                  <td align="center"><?= $widgets_t_sacos_descarga[$i]["BULTOS"] ?></td>
                  <td align="center"><?= $widgets_t_sacos_descarga[$i]["FACTOR"] ?></td>
                </tr>
               <?php } } ?>
              </table>
            </div>
          </div>
        </div>
        <!-- TERMINO WIDGETS SUPER SACOS ENTRADAS -->
      </div><!-- /.row -->
<!-- ######################################### Termino de Widgets para descargas ######################################### -->


    <p class="text-center">
      <strong>
        <div class="text-light-blue" align="center"><i class="fa fa-truck"></i> STATUS DE VEHICULO EN DESCARGA</div>
      </strong>
    </p>


<!-- INICIA TAB PARA DESCARGAS -->
    <ul class="nav nav-tabs" id="myTab_otfc">
        <li class="active"><a id="tab_otfc_cor"  data-toggle="tab" href="#tab_otfc_cordoba">EN PROCESO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_descargas_pro_agro[]">0</span></a></li>
        <li><a id="otfc_occ"  data-toggle="tab" href="#tab_otfc_occidente">FINALIZADO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_descargas_fin_agro[]">0</span></a></li>
    </ul>
    <div class="tab-content"><!-- tab-content -->
      <div id="tab_otfc_cordoba" class="tab-pane fade in active"><!-- tab_otfc_pro -->
    <!-- INICA BODY DE TAB DESCARGAS EN PROCESO -->
      <br>
      <div class="text-light-blue" align="center"><strong><i class="fa fa-spin fa-refresh"></i> DESCARGAS EN PROCESO</strong></div>
      <br>

      <!-- INICIA OPCIONES DE FILTRO (STATUS,MAYO-MENOR TIEMPO) -->
      <div class="row">
        <div class="col-md-4">
        <!-- INICIA CODE BOTON FILTRO DE HORA DESCARGAS -->
          <button type="button" id="boton_descarga_min" class="btn bg-blue btn-xs">Descargas menores a 75 MIn.</button>
          <button type="button" id="boton_descarga_max" class="btn btn-danger btn-xs">Descargas mayores a 75 MIn.</button>

          <div style="display: none;">
          Descargas mayores <input type="text" id="descargas_min" name="descargas_min">
          Descargas menores <input type="text" id="descargas_max" name="descargas_max">
          </div>
          <!-- TERMINA CODE BOTON FILTRO DE HORA DESCARGAS -->
        </div>
        <!-- INICIA CODE SELECT STATUS VEHICULO DESCARGA -->
        <div class="col-md-3">
        <div class="form-group">
          <label>Status</label>
          <select name="select_status_des" id="select_status_des">
            <option value="">All</option>
            <option value="status1">Registro de vehículo</option>
            <option value="status2">Vehículo en bascula </option>
            <option value="status3">Inicia Descarga</option>
            <option value="status4">Termina Descarga</option>
          </select>
        </div>
        </div>
        <!-- TERMINA CODE SELECT STATUS VEHICULO DESCARGA -->
        </div>
      <!-- TERMINA OPCIONES DE FILTRO (STATUS,MAYO-MENOR TIEMPO) -->

      <!-- inicia tabla para status de cargas -->
        <div class="table-responsive" align="center"><!-- table-responsive -->
          <table id="tabla_proceso_descarga" class="display compact" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small">
                </th>
              </tr>
            </thead>
            <tbody>
            <?php
            $consulta_descarga_status = $obj_agro_descarga_status->consulta_descarga_status($agro_plaza,$par,$ofc,$fol);
            for ($i=0; $i <count($consulta_descarga_status) ; $i++) {
            $plaza_mov_des = $consulta_descarga_status[$i]["ID_PLAZA"];
            $vid_recibo_des = $consulta_descarga_status[$i]["RECIBO_DET"];
            $vid_movto_des = $consulta_descarga_status[$i]["MOV_ID_VEHICULO"].$consulta_descarga_status[$i]["MOV_BULTO"];
            #echo "FOLIO".$vid_recibo_des.$vid_movto_des."</br>";
            $consulta_descarga_mov = $obj_agro_descarga_status->consulta_descarga_mov($plaza_mov_des,$vid_recibo_des,$vid_movto_des);
            if ($consulta_descarga_mov == false){

            $descarga_proceso[$i] = $i;
            ?>
              <tr>
                <td colspan="7">
                <!-- INICIA CODE PARA CALCULAR TIEMPO TRANSCURRIDO OTFC  -->
                <?php
                $fechaInicio= $consulta_descarga_status[$i]["R_VEHICULO"];
                $fechaFin= strftime("%d-%m-%Y %H:%M:%S");
                $tiempo_proc_descarga = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
                $dif_min_descarga = $obj_agro_carga->dif_minutos($fechaFin,$fechaInicio);

                echo '<span style="display: none;" class="badge badge bg-teal-active">'.$dif_min_descarga.'</span>';

                switch (true) {
                  case ($dif_min_descarga >= 60):
                    echo '<span class="badge badge bg-red">';
                    echo '<i class="fa fa-hourglass-1"></i> Tiempo Transcurrido: ';
                    echo '<cite>'.$tiempo_proc_descarga.'</cite>';
                    echo '</span> ';
                    break;
                  default:
                    echo '<span class="badge badge bg-blue">';
                    echo '<i class="fa fa-hourglass-1"></i> Tiempo Transcurrido: ';
                    echo '<cite>'.$tiempo_proc_descarga.'</cite>';
                    echo '</span> ';
                    break;
                }
                ?>
                <!-- TERMINA CODE PARA CALCULAR TIEMPO TRANSCURRIDO OTFC  -->
                  <span class="badge badge bg-teal-active"><i class="fa fa-cube"></i> Plaza: <cite><?= $consulta_descarga_status[$i]["PLAZA"] ?> </cite></span>
                  <span class="badge badge bg-teal-active"><i class="fa fa-cubes"></i> Almacen: <cite><?= $consulta_descarga_status[$i]["ALMACEN"] ?> </cite></span>
                  <span class="badge badge bg-teal-active"><i class="fa fa-briefcase"></i> CLiente: <cite><?= $consulta_descarga_status[$i]["CLIENTE"] ?> </cite></span>
                  <span class="badge badge bg-teal-active"><i class="fa fa-truck"></i> Transporte: <cite><?= $consulta_descarga_status[$i]["TRANSPORTE"] ?> </cite></span>
                  <span class="badge badge bg-teal-active"><i class="fa fa-barcode"></i> Placas: <cite><?= $consulta_descarga_status[$i]["PLACAS"] ?> </cite></span>
                  <a class="fancybox fancybox.iframe" href="<?= 'agronegocios_det.php?op=otfc&par='.$consulta_descarga_status[$i]["PARTIDA"].'&ofc='.$consulta_descarga_status[$i]["OTFC"].'&fol='.$consulta_descarga_status[$i]["FOLIO_DET"] ?>">
                  <span class="badge badge bg-teal-active btn"> <i class="fa fa-eye"></i> Detalles </span>
                  </a>
                  <br><br><br>
                  <!-- **************** INICIA LINEA DE TIEMPO PARA EL ESTADO DE OTFC **************** -->
                  <ol class="timeline-line">
                  <?php
                  //-- ############### INICIA CODE REGISTRO DE VEHICULO OTFC #################### --//
                    $r_vehiculo_otfc = date_create($consulta_descarga_status[$i]["R_VEHICULO"]);
                    $r_vehiculo_fecha_otfc = date_format($r_vehiculo_otfc, "d-m-Y") ;
                    $r_vehiculo_hora_otfc = date_format($r_vehiculo_otfc, "H:i:s") ;
                    switch ( true ) {
                      case ( $consulta_descarga_status[$i]["R_VEHICULO"] == false ) :
                        echo '<li class="timeline__step">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> ... </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Registro de vehículo </span>';
                        echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                        echo '</li>';
                      break;

                      default:
                      $descarga_status = 1;// determina nivel del status que se encuentra la descarga
                        echo '<li class="timeline__step done">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> '.$r_vehiculo_hora_otfc.' </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Registro de vehículo<br>'.$r_vehiculo_fecha_otfc.'</span>';
                        if ($consulta_descarga_status[$i]["BASCULA_VEHICULO"] == true){
                          $dif_min_des_color = $obj_agro_carga->dif_minutos($consulta_descarga_status[$i]["BASCULA_VEHICULO"] ,$consulta_descarga_status[$i]["R_VEHICULO"] );
                          if ($dif_min_des_color>5){
                            echo '<i class="timeline__step-marker_red_fin"><i class="ion-ios-compose"></i></i>';
                          }else{
                            echo '<i class="timeline__step-marker_green"><i class="ion-ios-compose"></i></i>';
                          }
                        }else
                        {
                          echo '<i class="timeline__step-marker_green"><i class="ion-ios-compose"></i></i>';
                        }
                        echo '</li>';
                      break;
                    }
                  //-- ############### TERMINA CODE REGISTRO DE VEHICULO OTFC #################### --//
                  //-- ############### INICIA CODE VEHÍCULO EN BASCULA OTFC #################### --//
                    $bascula_vehiculo_otfc = date_create($consulta_descarga_status[$i]["BASCULA_VEHICULO"]);
                    $bascula_vehiculo_fecha_otfc = date_format($bascula_vehiculo_otfc, "d-m-Y") ;
                    $bascula_vehiculo_hora_otfc = date_format($bascula_vehiculo_otfc, "H:i:s") ;
                    switch ( true ) {
                      case ( $consulta_descarga_status[$i]["BASCULA_VEHICULO"] == false ) || ( $bascula_vehiculo_hora_otfc == "00:00:00" ):
                        echo '<li class="timeline__step">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> ... </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Vehículo en bascula</span>';
                        if ($consulta_descarga_status[$i]["R_VEHICULO"] == true){
                          $dif_min_des_color = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$consulta_descarga_status[$i]["R_VEHICULO"]);
                          if ($dif_min_des_color>5) {
                            echo '<i class="timeline__step-marker_red"><i class="fa fa-clock-o"></i></i>';
                          }else{
                            echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                          }
                        }else{
                          echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                        }
                        echo '</li>';
                      break;

                      default:
                      $descarga_status = 2;// determina nivel del status que se encuentra la descarga
                        echo '<li class="timeline__step done">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> '.$bascula_vehiculo_hora_otfc.' </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Vehículo en bascula <br>'.$bascula_vehiculo_fecha_otfc.'</span>';
                        if ($consulta_descarga_status[$i]["INICIA_DESCARGA"] == true){
                          $dif_min_des_color = $obj_agro_carga->dif_minutos($consulta_descarga_status[$i]["INICIA_DESCARGA"],$consulta_descarga_status[$i]["BASCULA_VEHICULO"] );
                          if ($dif_min_des_color>5){
                            echo '<i class="timeline__step-marker_red_fin"><i class="fa fa-truck"></i></i>';
                          }else{
                           echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';
                          }
                        }else{
                          echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';
                        }
                        echo '</li>';
                      break;
                    }
                  //-- ############### TERMINA CODE VEHÍCULO EN BASCULA OTFC #################### --//
                  //-- ############### INICIA CODE INICIA DESCARGA DE VEHICULO OTFC #################### --//
                  // $bascula_vehiculo_strtotime_otfc = strtotime($consulta_descarga_status[$i]["BASCULA_VEHICULO"]);
                  // $fecha_hoy_strtotime_otfc = strtotime( date("d-m-Y H:i:s") ) ;
                  // $inicia_carga_strtotime_otfc = $bascula_vehiculo_strtotime_otfc + 600 ;
                  //   switch ( true ) {
                  //     case ( $consulta_descarga_status[$i]["BASCULA_VEHICULO"] == false ) || ( $bascula_vehiculo_hora_otfc == "00:00:00" || $fecha_hoy_strtotime_otfc < $inicia_carga_strtotime_otfc):
                  $inicia_descarga_otfc = date_create($consulta_descarga_status[$i]["INICIA_DESCARGA"]);
                  $inicia_descarga_fecha_otfc = date_format($inicia_descarga_otfc, "d-m-Y") ;
                  $inicia_descarga_hora_otfc = date_format($inicia_descarga_otfc, "H:i:s") ;
                  switch ( true ) {
                    case ( $consulta_descarga_status[$i]["INICIA_DESCARGA"] == false ) || ( $inicia_descarga_hora_otfc == "00:00:00" ):
                      echo '<li class="timeline__step">';
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                      echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> ... </span>';
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Inicia Descarga</span>';
                      if ($consulta_descarga_status[$i]["BASCULA_VEHICULO"] == true){
                          $dif_min_des_color = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$consulta_descarga_status[$i]["BASCULA_VEHICULO"]);
                          if ($dif_min_des_color>5) {
                            echo '<i class="timeline__step-marker_red"><i class="fa fa-clock-o"></i></i>';
                          }else{
                            echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                          }
                      }else{
                        echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                      }
                      echo '</li>';
                    break;

                    default:
                    $descarga_status = 3;// determina nivel del status que se encuentra la descarga
                      echo '<li class="timeline__step done">';
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                      echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> '.$inicia_descarga_hora_otfc.' </span>';
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Inicia Descarga <br>'.$inicia_descarga_fecha_otfc.'</span>';

                      if ($consulta_descarga_status[$i]["TERMINA_DESCARGA"] == true){
                          $dif_min_des_color = $obj_agro_carga->dif_minutos($consulta_descarga_status[$i]["TERMINA_DESCARGA"],$consulta_descarga_status[$i]["INICIA_DESCARGA"] );
                          if ($dif_min_des_color>60){
                            echo '<i class="timeline__step-marker_red_fin"><i class="fa fa-truck"><i class="fa fa-long-arrow-down"></i></i></i>';
                          }else{
                           echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"><i class="fa fa-long-arrow-down"></i></i></i>';
                          }
                        }else{
                          echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"><i class="fa fa-long-arrow-down"></i></i></i>';
                        }


                      echo '</li>';
                    break;
                  }
                  //-- ############### TERMINA CODE INICIA DESCARGA DE VEHICULO OTFC #################### --//
                  //-- ############### INICIA CODE TERMINA DESCARGA DE VEHICULO OTFC #################### --//
                  $termina_descarga_otfc = date_create($consulta_descarga_status[$i]["TERMINA_DESCARGA"]);
                  $termina_descarga_fecha_otfc = date_format($termina_descarga_otfc, "d-m-Y") ;
                  $termina_descarga_hora_otfc = date_format($termina_descarga_otfc, "H:i:s") ;
                  switch ( true ) {
                    case ( $consulta_descarga_status[$i]["TERMINA_DESCARGA"] == false ) || ( $termina_descarga_hora_otfc == "00:00:00" ):
                      echo '<li class="timeline__step">';
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                      echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> ... </span>';
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Termina Descarga</span>';
                      if ($consulta_descarga_status[$i]["INICIA_DESCARGA"] == true){
                          $dif_min_des_color = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$consulta_descarga_status[$i]["INICIA_DESCARGA"]);
                          if ($dif_min_des_color>60) {
                            echo '<i class="timeline__step-marker_red"><i class="fa fa-clock-o"></i></i>';
                          }else{
                            echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                          }
                      }else{
                        echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                      }
                      echo '</li>';
                    break;

                    default:
                    $descarga_status = 4;// determina nivel del status que se encuentra la descarga
                      echo '<li class="timeline__step done">';
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                      echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> '.$termina_descarga_hora_otfc.' </span>';
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Termina Descarga <br>'.$termina_descarga_fecha_otfc.'</span>';
                      echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';
                      echo '</li>';
                    break;
                  }
                  //-- ############### TERMINA CODE TERMINA DESCARGA DE VEHICULO OTFC #################### --//
                  ?>
                 <!-- ############### INICIA CODE DOCUMENTACIÓN DE VEHICULO ####################  -->
                    <li class="timeline__step">
                      <input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">
                      <label class="timeline__step-label" for="trigger1{{identifier}}">
                      <span class="timeline__step-content"><i class="fa fa-clock-o"></i> ... </span>
                      </label>
                      <span class="timeline__step-title"> Pre-Registro </span>
                      <?php
                      if ($consulta_descarga_status[$i]["TERMINA_DESCARGA"] == true){
                          $dif_min_des_color = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$consulta_descarga_status[$i]["TERMINA_DESCARGA"]);
                          $dif_min_des_color = $obj_agro_carga->dif_minutos("08-05-2016 05:48:25",$consulta_descarga_status[$i]["TERMINA_DESCARGA"]);
                          if ($dif_min_des_color>5) {
                            echo '<i class="timeline__step-marker_red"><i class="fa fa-clock-o"></i></i>';
                          }else{
                            echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                          }
                      }else{
                        echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                      }
                      ?>
                    </li>
                    <li class="timeline__step">
                        <input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">
                        <label class="timeline__step-label" for="trigger1{{identifier}}">
                        <span class="timeline__step-content"><i class="fa fa-clock-o"></i> ...</span>
                        </label>
                        <span class="timeline__step-title"> Despacho de Vehículo</span>
                        <i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>
                      </li>
                  </ol>
                  <!-- **************** TERMINA LINEA DE TIEMPO PARA EL ESTADO DE OTFC **************** -->
                  <br>  <br>  <br>
                </td>
              </tr>
            <?php } } ?>
            </tbody>
          </table>
        </div><!-- ./table-responsive -->
      <!-- termina tabla para status de descargas -->

    <!-- TERMINA BODY DE TAB DESCARGAS EN PROCESO -->
      </div><!-- ./tab_otfc_pro -->
      <div id="tab_otfc_occidente" class="tab-pane fade"><!-- tab_otfc_fin -->
        <!-- INICA BODY DE TAB DESCARGAS FINALIZADAS -->

          <!-- INICIA TABLA DESCARGAS FINALIZADAS -->
          <br>
          <div class="text-light-blue" align="center"><strong><i class="fa fa-truck"></i> DESCARGAS FINALIZADAS</strong></div>
          <br>
          <div class="table-responsive" align="center"><!-- table-responsive -->
          <table id="tabla_op_fin_des" class="table-striped table-bordered table-hover" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small">PLAZA</th>
                <th class="small">ALMACEN</th>
                <th class="small">CLIENTE</th>
                <th class="small">TRANSPORTE</th>
                <th class="small">PLACAS</th>
                <th class="small">CHOFER</th>
                <th class="small">INICIO</th>
                <th class="small">FINALIZA</th>
                <th class="small">TIEMPO</th>
                <th class="small">DETALLES</th>
              </tr>
            </thead>
            <tbody>
            <?php
            for ($i=0; $i <count($consulta_descarga_status) ; $i++) {
            $plaza_mov_des = $consulta_descarga_status[$i]["ID_PLAZA"];
            $vid_recibo_des = $consulta_descarga_status[$i]["RECIBO_DET"];
            $vid_movto_des = $consulta_descarga_status[$i]["MOV_ID_VEHICULO"].$consulta_descarga_status[$i]["MOV_BULTO"];
            $consulta_descarga_mov = $obj_agro_descarga_status->consulta_descarga_mov($plaza_mov_des,$vid_recibo_des,$vid_movto_des);
            if ($consulta_descarga_mov == true){
            $descargas_finalizadas[$i] = $i;

            /////calculo de tiempo
            $fechaInicio = $consulta_descarga_status[$i]["R_VEHICULO"];
            $fechaFin = $consulta_descarga_mov;
            $total_tiempo_otfc = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);

            $dif_min_descarga = $obj_agro_carga->dif_minutos($fechaFin,$fechaInicio);
            if ($dif_min_descarga>80){
              $color_td = "text-red";
            }else{
              $color_td = "text-green";
            }
            ?>
              <tr class="<?=$color_td?>">
                <td class="small"><?= $consulta_descarga_status[$i]["PLAZA"] ?></td>
                <td class="small"><?= $consulta_descarga_status[$i]["ALMACEN"] ?></td>
                <td class="small"><?= $consulta_descarga_status[$i]["CLIENTE"] ?></td>
                <td class="small"><?= $consulta_descarga_status[$i]["TRANSPORTE"] ?></td>
                <td class="small"><?= $consulta_descarga_status[$i]["PLACAS"] ?></td>
                <td class="small"><?= $consulta_descarga_status[$i]["CHOFER"] ?></td>
                <td class="small"><?= $consulta_descarga_status[$i]["R_VEHICULO"] ?></td>
                <td class="small"><?= $consulta_descarga_mov ?></td>
              <!-- inicia code total de tiempo menos o mas descargas -->
              <?php
                if (strtotime($fechaInicio) < strtotime($fechaFin) ){
                  echo '<td class="small">'.$total_tiempo_otfc.'</td>';
                }else{
                  echo '<td class="small">-'.$total_tiempo_otfc.'</td>';
                }
              ?>
              <!-- termina code total de tiempo menos o mas descargas -->
                <td class="small">
                <a class="fancybox fancybox.iframe" href="<?= 'agronegocios_det.php?op=otfc&par='.$consulta_descarga_status[$i]["PARTIDA"].'&ofc='.$consulta_descarga_status[$i]["OTFC"].'&fol='.$consulta_descarga_status[$i]["FOLIO_DET"] ?>">
                  <span class="badge bg-blue btn btn-xs"> <i class="fa fa-eye"></i> Detalles </span>
                </a>
                </td>
              </tr>
            <?php } } ?>
            </tbody>
          </table>
          </div><!-- ./table-responsive -->
          <!-- INICIA TABLA DESCARGAS FINALIZADAS -->

        <!-- TERMINA BODY DE TAB DESCARGAS FINALIZADAS -->
      </div><!-- ./tab_otfc_fin -->
    </div><!-- ./tab-content -->
<!-- TERMINA TAB PARA DESCARGAS -->


    </div><!--/.box-body-->
  </div>
</section>


<!-- ############################ INICIA SECCION OPERACIONES CARGAS ############################# -->
<section>
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">OPERACIONES CARGAS</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

    <p class="text-center">
      <h4>
       <div class="text-light-blue" align="center">
          <i class="fa fa-truck"><i class="fa fa-long-arrow-up"></i></i> CARGAS-SALIDAS <?= $agro_plaza." ".$agro_almacen." ".$agro_cliente." ".$titulo_fecha ?>
       </div>
      </h4>
    </p>

      <!-- inicia menu opciones carga -->
      <ol class="breadcrumb">
        <?php
        if ($agro_plaza == true or isset($_SESSION['fec_ini_agro']) == true ){
        ?>
        <li>
        <form method="post">
          <input type="hidden" name="agro_plaza" value="">
          <input type="hidden" name="agro_almacen" value="">
          <input type="hidden" name="agro_cliente" value="">
          <input type="hidden" name="destroySession" value="1">
          <?php
          #unset($_SESSION['fec_ini_agro']);
          #unset($_SESSION['fec_fin_agro']);
           ?>
          <div class="content_text_btn1"><button type='submit' class='click_btn_text1 btn btn-link'><i class="ion-arrow-left-a"></i> Regresar</button></div>
        </form>
        </li>
        <?php } ?>
        <li>
          <div class="btn-group">
            <?php if ($_SESSION['area']!=3){?>
              <button type='button' class='content_text_btn2 btn btn-link dropdown-toggle' data-toggle="dropdown"><i class="fa fa-cube"></i> Filtrar por Plaza</button>
            <?php } ?>
           <ul class="dropdown-menu" role="menu">
            <form method="post">
              <input type="hidden" name="agro_almacen" value="">
              <input type="hidden" name="agro_cliente" value="">
              <?php if ($agro_plaza==true){ echo '<li><button name="agro_plaza" value="" class="click_btn_text2 btn btn-link">ALL</button></li>';} ?>
              <li><button name="agro_plaza" value="CÓRDOBA (ARGO)" class='click_btn_text2 btn btn-link'>CÓRDOBA (ARGO)</button></li>
              <li><button name="agro_plaza" value="OCCIDENTE (ARGO)" class='click_btn_text2 btn btn-link'>OCCIDENTE (ARGO)</button></li>
            </form>
            </ul>
          </div>
        </li>
        <li>
          <div class="btn-group">
           <button type='button' class='content_text_btn3 btn btn-link dropdown-toggle' data-toggle="dropdown"><i class="fa fa-cubes"></i> Filtrar por Almacen</button>
            <ul class="dropdown-menu" role="menu">
            <?php
            if ($agro_almacen == true) { echo '<li><a><form method="POST"><input type="hidden" name="agro_cliente" value=""><button type="submit" name="agro_almacen" value="" class="click_btn_text3 btn btn-link">ALL</button></form></a></li>';}
            $filtro_almacen_carga = $obj_agro_carga->select_almacen($agro_plaza);
            for ($i=0; $i <count($filtro_almacen_carga) ; $i++) {
              echo '<li><a><form method="POST"><input type="hidden" name="agro_cliente" value=""><button type="submit" name="agro_almacen" value="'.$filtro_almacen_carga[$i]["ALMACEN"].'" class="click_btn_text3 btn btn-link">'.$filtro_almacen_carga[$i]["ALMACEN"].'</button></form></a></li>';
            }
            ?>
            </ul>
          </div>
        </li>
        <li>
          <div class="btn-group">
           <button type='button' class='content_text_btn4 btn btn-link dropdown-toggle' data-toggle="dropdown"><i class="fa fa-briefcase"></i> Filtrar por Cliente</button>
            <ul class="dropdown-menu" role="menu">
            <?php
            if ($agro_cliente == true){echo '<li><a><form method="POST"><button type="submit" name="agro_cliente" value="" class="click_btn_text4 btn btn-link">ALL</button></form></a></li>';}
            $filtro_almacen_carga = $obj_agro_carga->select_cliente($agro_plaza,$agro_almacen);
            for ($i=0; $i <count($filtro_almacen_carga) ; $i++) {
              echo '<li><a><form method="POST"><button type="submit" name="agro_cliente" value="'.$filtro_almacen_carga[$i]["CLIENTE"].'" class="click_btn_text4 btn btn-link">'.$filtro_almacen_carga[$i]["CLIENTE"].'</button></form></a></li>';
            }
            ?>
            </ul>
          </div>
        </li>
        <li>
          <button type='button' class='btn btn-link' data-toggle="modal" data-target="#modal_his_carga" id="btn_his_car"><i class="fa fa-history"></i> Historial</button>
        </li>
        <li>
          <a data-toggle="tab" href="#fec_per_agro">
          <i class="fa fa-calendar"></i> <button type='button' class='btn btn-link'>Fecha Personalizada</button>
          </a>
        </li>
      </ol>
    <!-- termina menu opciones carga -->


<!-- INICIA CODE FECHA PERSONALIZADA CARGA -->
    <div class="row content_text_btn5">
    <div class="tab-content">
      <div id="fec_per_agro" class="tab-pane fade">
      <form method="post">
        <div class="col-md-5">
        <div class="input-group" id="fec_rango_agro[]">
          <div class="btn input-group-addon">
            <i class="fa fa-calendar-minus-o"></i> Inicio
          </div>
          <input type="text" class="form-control pull-right" name="fec_ini_agro" id="fec_ini_agro[]" readonly>
          <div class="input-group-addon">
            <i class="fa fa-calendar-plus-o"></i> Fin
          </div>
          <input type="text" class="form-control pull-right" name="fec_fin_agro" id="fec_fin_agro[]" readonly>
        </div>
        </div>
        <div class="col-md-2">
          <button type='submit' class='click_btn_text5 btn btn-sm bg-blue'>Ok</button>
        </div>
      </form>
      </div>
    </div>
    </div><br>
<!-- TERMINA CODE FECHA PERSONALIZADA CARGA -->


<!-- ######################################## Inicio de Widgets para cargas ######################################### -->
      <div class="row"><!-- row -->
      <!-- WIDGETS CARGAS -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-aqua">
          <span class="info-box-icon"><i class="fa fa-truck"><i class="fa fa-long-arrow-up"></i></i></span>
            <div class="info-box-content">
              <center><span class="info-box-text"><b>CARGAS</b></span></center>
              <span class="info-box-text">Total: <b id="widgets_cargas_t_agro[]">0</b></span>
              <span class="info-box-text">Finalizado: <b id="widgets_cargas_fin_agro[]">0</b></span>
              <span class="info-box-text">En proceso: <b id="widgets_cargas_pro_agro[]">0</b></span>
            </div>
        </div>
      </div>
      <!-- WIDGETS SALIDAS -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green">
          <span class="info-box-icon"><i class="fa fa-cloud-upload"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">SALIDA</span>
              <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>
              <?php
              $widgets_ton_cargas = $obj_agro_carga->widgets_ton_cargas($agro_plaza);
              for ($i=0; $i <count($widgets_ton_cargas) ; $i++) {
                $ton_carga_cor = $widgets_ton_cargas[$i]["T_NETAS_ORACBA"];
                $ton_car_occ = $widgets_ton_cargas[$i]["T_NETAS_ORAR06"];
                echo '<span class="info-box-text">'.($ton_carga_cor+$ton_car_occ).' TON</span>';
              }
              ?>
            </div>
          </div>
        </div>
        <!-- WIDGETS SACOS SALIDA -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon bg-yellow"><i class="ion ion-bag"></i></span>
            <div class="info-box-content bg-yellow"><!-- bg-primary -->
              <span class="info-box-text">SACOS-SALIDAS</span>
              <div class="progress">
                  <div class="progress-bar" style="width: 100%"></div>
              </div>
                <table>
                  <tr>
                    <th>PLAZA</th>
                    <th>/BULTOS</th>
                    <th>/KG</th>
                  </tr>
                <?php
                $widgets_t_sacos_carga = $obj_agro_carga->widgets_t_sacos_carga($agro_plaza);
                for ($i=0; $i <count($widgets_t_sacos_carga) ; $i++) {
                  if ( $widgets_t_sacos_carga[$i]["ID_UME"] == 59 ){
                ?>
                  <tr>
                    <td align="center"><?= $widgets_t_sacos_carga[$i]["PLAZA_SIG"] ?></td>
                    <td align="center"><?= $widgets_t_sacos_carga[$i]["BULTOS"] ?></td>
                    <td align="center"><?= $widgets_t_sacos_carga[$i]["FACTOR"] ?></td>
                  </tr>
                <?php } } ?>
                </table>
            </div>
          </div>
        </div>
        <!-- WIDGETS SUPER SACOS SALIDA -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon bg-red"><i class="ion ion-bag"></i></span>
            <div class="info-box-content bg-red"><!-- bg-primary -->
            <span class="info-box-text">SUPERSACOS-SALIDAS</span>
            <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>

              <table>
                <tr>
                  <th>PLAZA</th>
                  <th>/BULTOS</th>
                  <th>/KG</th>
                </tr>
               <?php
                for ($i=0; $i <count($widgets_t_sacos_carga) ; $i++) {
                  if ( $widgets_t_sacos_carga[$i]["ID_UME"] == 70 ){
              ?>
                <tr>
                  <td align="center"><?= $widgets_t_sacos_carga[$i]["PLAZA_SIG"] ?></td>
                  <td align="center"><?= $widgets_t_sacos_carga[$i]["BULTOS"] ?></td>
                  <td align="center"><?= $widgets_t_sacos_carga[$i]["FACTOR"] ?></td>
                </tr>
               <?php } } ?>
              </table>
            </div>
          </div>
        </div>
        <!-- TERMINO WIDGETS SUPER SACOS SALIDA -->
      </div><!-- /.row -->
<!-- ######################################### Termino de Widgets para cargas ######################################### -->


    <p class="text-center">
      <strong>
        <div class="text-light-blue" align="center"><i class="fa fa-truck"></i> STATUS DE VEHICULO EN CARGA</div>
      </strong>
    </p>


<!-- INICIA TAB PARA CARGAS -->
  <ul class="nav nav-tabs" id="myTab_ofc">
    <li class="active">
      <a id="tab_ofc_proceso" data-toggle="tab" href="#tab_ofc_pro">EN PROCESO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_cargas_pro_agro[]">0</span></a>
    </li>
    <li>
      <a id="tab_ofc_finalizado" data-toggle="tab" href="#tab_ofc_fin">FINALIZADO&nbsp; &nbsp;<span class="badge bg-light-blue" id="widgets_cargas_fin_agro[]">0</span></a>
    </li>
  </ul>

  <div class="tab-content">
    <div id="tab_ofc_pro" class="tab-pane fade in active"><!-- tab_ofc_pro -->
    <!-- INICA BODY DE TAB CARGAS EN PROCESO -->
    <br>
    <div class="text-light-blue" align="center"><strong><i class="fa fa-spin fa-refresh"></i> CARGAS EN PROCESO</strong></div>
    <br>
    <!-- INICIA FILTRO POR STATUS Y CARGAS -->
    <div class="row">

      <div class="col-md-4">
        <!-- INICIA CODE BOTON FILTRO DE HORA CARGAS -->
        <button type="button" id="boton_carga_min" class="btn bg-blue btn-xs">Cargas menores a 80 Min.</button>
        <button type="button" id="boton_carga_max" class="btn btn-danger btn-xs">Cargas mayores a 80 Min.</button>

        <div style="display: none;">
        Cargas mayores <input type="text" id="cargas_min" name="cargas_min">
        Cargas menores <input type="text" id="cargas_max" name="cargas_max">
        </div>
        <br><br>
        <!-- TERMINA CODE BOTON FILTRO DE HORA CARGAS -->
      </div>

      <!-- INICIA CODE SELECT STATUS VEHICULO CARGA -->
      <div class="col-md-3">
      <div class="form-group">
        <label>Status</label>
        <select name="select_status_car" id="select_status_car">
          <option value="">All</option>
          <option value="status1">Registro de vehículo</option>
          <option value="status2">Vehículo en bascula </option>
          <option value="status3">Inicia Carga</option>
          <option value="status4">Termina Carga</option>
        </select>
      </div>
      </div>
        <br><br>
      <!-- TERMINA CODE SELECT STATUS VEHICULO CARGA -->

    </div>
    <!-- TERMINA FILTRO POR STATUS Y CARGAS -->

      <!-- inicia tabla para status de cargas -->
        <div class="table-responsive" align="center"><!-- table-responsive -->
          <table id="tabla_proceso_carga" class="display compact" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small">
                </th>
              </tr>
            </thead>
            <tbody>
            <?php
            $consulta_carga_status = $obj_agro_carga_status->consulta_carga_status($agro_plaza);
            for ($i=0; $i <count($consulta_carga_status) ; $i++) {
              if ( $consulta_carga_status[$i]["FECHA_DOCUMENTACION"] == false ){
              $carga_proceso[] = $i;
            ?>
              <tr>
                <td colspan="7">
                  <!-- INICIA CODE PARA CALCULAR TIEMPO TRANSCURRIDO OFC  -->
                  <?php
                  $fechaInicio= $consulta_carga_status[$i]["R_VEHICULO"];
                  $fechaFin= strftime("%d-%m-%Y %H:%M:%S");
                  $tiempo_proc_carga = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);

                  $dif_min_carga = $obj_agro_carga->dif_minutos($fechaFin,$fechaInicio);

                  echo '<span style="display:none;" class="badge badge bg-teal-active">'.$dif_min_carga.'</span>';

                  switch (true) {
                    case ($dif_min_carga > 80):
                      $bgcolor = "#FFCDD2";
                      $var_tiempo = "EnRetraso";
                      echo '<span class="badge badge bg-red">';
                      echo '<i class="fa fa-hourglass-1"></i> Tiempo Transcurrido: ';
                      echo '<cite>'.$tiempo_proc_carga.'</cite>';
                      echo '</span> ';
                      break;

                    default:
                      $bgcolor = "#C8E6C9";
                      $var_tiempo = "EnTiempo";
                      echo '<span class="badge badge bg-blue">';
                      echo '<i class="fa fa-hourglass-1"></i> Tiempo Transcurrido: ';
                      echo '<cite>'.$tiempo_proc_carga.'</cite>';
                      echo '</span> ';
                      break;
                  }
                  ?>
                  <!-- TERMINA CODE PARA CALCULAR TIEMPO TRANSCURRIDO OFC  -->
                  <span class="badge badge bg-teal-active"><i class="fa fa-cube"></i> Plaza: <cite><?= $consulta_carga_status[$i]["PLAZA"] ?> </cite></span>
                  <span class="badge badge bg-teal-active"><i class="fa fa-cubes"></i> Almacen: <cite><?= $consulta_carga_status[$i]["ALMACEN"] ?> </cite></span>
                  <span class="badge badge bg-teal-active"><i class="fa fa-briefcase"></i> CLiente: <cite><?= $consulta_carga_status[$i]["CLIENTE"] ?> </cite></span>
                  <span class="badge badge bg-teal-active"><i class="fa fa-truck"></i> Transporte: <cite><?= $consulta_carga_status[$i]["TRANSPORTE"] ?> </cite></span>
                  <span class="badge badge bg-teal-active"><i class="fa fa-barcode"></i> Placas: <cite><?= $consulta_carga_status[$i]["PLACAS"] ?> </cite></span>
                  <a class="fancybox fancybox.iframe" href="<?= 'agronegocios_det.php?op=ofc&par='.$consulta_carga_status[$i]["PARTIDA"].'&ofc='.$consulta_carga_status[$i]["OFC"].'&fol='.$consulta_carga_status[$i]["FOLIO_DET"] ?>">
                  <span class="badge badge bg-teal-active btn"> <i class="fa fa-eye"></i> Detalles </span>
                  </a>
                  <!-- INICIA CODE NOTIFICACION
                  <button type="button" class="btn btn-sm badge bg-green" data-toggle="modal" data-target="#modal_what<?=$consulta_carga_status[$i]["PARTIDA"].$consulta_carga_status[$i]["OFC"].$consulta_carga_status[$i]["FOLIO_DET"]?>"> <i class="fa fa-whatsapp"></i> Notificar</button>-->
                  <!-- TERMINA CODE NOTIFICACION -->
                  <br><br><br>

                  <!-- MODAL WHATSAPP-->
                <div class="modal fade" id="modal_what<?=$consulta_carga_status[$i]["PARTIDA"].$consulta_carga_status[$i]["OFC"].$consulta_carga_status[$i]["FOLIO_DET"]?>" role="dialog">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title">Notificacion por Whatsapp <i class="ion-social-whatsapp-outline"></i></h4>
                        </div>

                        <div class="modal-body">

                        <div id="resultado[]"></div>

                        <div class="form-group">
                          <label> <i class="ion-iphone"></i> Movil:</label>
                          <input name="caja_texto" id="valor1<?=$info_gral_car_des_otr_cor[$i]["SOLICITUD"]?>" type="text" class="form-control" placeholder="Ingrese el número de celular">
                        </div>

                        <input type="hidden" name="caja_texto" id="valor2<?=$info_gral_car_des_otr_cor[$i]["SOLICITUD"]?>" value="<?php echo 'Carga '.$var_tiempo.' *Almacen:* '.$consulta_carga_status[$i]["ALMACEN"].' *Plaza:* '.$consulta_carga_status[$i]["PLAZA"].' *Cliente:* '.$consulta_carga_status[$i]["CLIENTE"].' *Transporte:* '.$consulta_carga_status[$i]["TRANSPORTE"].' *Placas:* '.$consulta_carga_status[$i]["PLACAS"] ?>"/>

                        <div class="callout callout" style="background: <?=$bgcolor?>">
                          <h4>Mensaje: <i class="ion-social-whatsapp-outline"></i></h4>
                          <b>Carga <?=$var_tiempo?> </b><br>
                          <b>Almacen: </b>  <?= $consulta_carga_status[$i]["ALMACEN"]?><br>
                          <b>Plaza: </b> <?= $consulta_carga_status[$i]["PLAZA"]  ?><br>
                          <b>Cliente: </b> <?= $consulta_carga_status[$i]["CLIENTE"] ?><br>
                          <b>Transporte: </b> <?= $consulta_carga_status[$i]["TRANSPORTE"] ?><br>
                          <b>Placas: </b> <?= $consulta_carga_status[$i]["PLACAS"] ?><br>
                          <b>Tiempo: </b> <?= $tiempo_proc_carga ?><br>
                        </div>

                        <input class="btn btn-sm btn-primary" type="button" href="javascript:;" onclick="realizaProceso($('#valor1<?=$info_gral_car_des_otr_cor[$i]["SOLICITUD"]?>').val(), $('#valor2<?=$info_gral_car_des_otr_cor[$i]["SOLICITUD"]?>').val());return false;" value="Enviar"/>
                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <!-- MODAL WHATSAPP-->
                  <!-- **************** INICIA LINEA DE TIEMPO PARA EL ESTADO DE OFC **************** -->
                  <ol class="timeline-line">
                <?php
                //-- ############### INICIA CODE REGISTRO DE VEHICULO #################### --//
                $r_vehiculo = date_create($consulta_carga_status[$i]["R_VEHICULO"]);
                $r_vehiculo_fecha = date_format($r_vehiculo, "d-m-Y") ;
                $r_vehiculo_hora = date_format($r_vehiculo, "H:i:s") ;
                  switch ( true ) {
                    case ( $consulta_carga_status[$i]["R_VEHICULO"] == false ) :
                      echo '<li class="timeline__step">';
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                      echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> ... </span>';
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Registro de vehículo </span>';
                      echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                      echo '</li>';
                    break;

                    default:
                      $carga_status = 1;// determina nivel del status que se encuentra la carga
                      echo '<li class="timeline__step done">';
                      echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                      echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                      echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> '.$r_vehiculo_hora.' </span>';
                      echo '</label>';
                      echo '<span class="timeline__step-title"> Registro de vehículo<br>'.$r_vehiculo_fecha.'</span>';
                      if ($consulta_carga_status[$i]["BASCULA_VEHICULO"] == true){
                      /*calculo de tiempo entre reg. vehiculo y hoy*/
                      $dif_min_carga = $obj_agro_carga->dif_minutos($consulta_carga_status[$i]["BASCULA_VEHICULO"],$consulta_carga_status[$i]["R_VEHICULO"]);
                        if($dif_min_carga>5){
                          echo '<i class="timeline__step-marker_red_fin"><i class="ion-ios-compose"></i></i>';
                        }else{
                          echo '<i class="timeline__step-marker_green"><i class="ion-ios-compose"></i></i>';
                        }
                      }else{
                      echo '<i class="timeline__step-marker_green"><i class="ion-ios-compose"></i></i>';
                      }

                      echo '</li>';
                    break;
                  }
                  //-- ############### TERMINA CODE REGISTRO DE VEHICULO #################### --//
                  //-- ############### INICIA CODE VEHÍCULO EN BASCULA #################### --//
                  $bascula_vehiculo = date_create($consulta_carga_status[$i]["BASCULA_VEHICULO"]);
                  $bascula_vehiculo_fecha = date_format($bascula_vehiculo, "d-m-Y") ;
                  $bascula_vehiculo_hora = date_format($bascula_vehiculo, "H:i:s") ;
                    switch ( true ) {
                      case ( $consulta_carga_status[$i]["BASCULA_VEHICULO"] == false ) || ( $bascula_vehiculo_hora == "00:00:00" ):
                        echo '<li class="timeline__step">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> ... </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Vehículo en bascula </span>';
                    /*calculo de tiempo entre reg. vehiculo y hoy*/
                    $dif_min_carga = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$consulta_carga_status[$i]["R_VEHICULO"]);
                        if($dif_min_carga>5){
                        echo '<i class="timeline__step-marker_red"><i class="fa fa-clock-o"></i></i>';
                        }else{
                        echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                        }
                        echo '</li>';
                      break;

                      default:
                        $carga_status = 2;// determina nivel del status que se encuentra la carga
                        echo '<li class="timeline__step done">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> '.$bascula_vehiculo_hora.' </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Vehículo en bascula <br>'.$bascula_vehiculo_fecha.'</span>';
                        if ($consulta_carga_status[$i]["INICIA_CARGA"] == true){
                        /*calculo de tiempo entre reg. vehiculo y hoy*/
                        $dif_min_carga = $obj_agro_carga->dif_minutos($consulta_carga_status[$i]["INICIA_CARGA"],$consulta_carga_status[$i]["BASCULA_VEHICULO"]);
                          if($dif_min_carga>5){
                            echo '<i class="timeline__step-marker_red_fin"><i class="fa fa-truck"></i></i>';
                          }else{
                            echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';
                          }
                        }else{
                        echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';
                        }
                        echo '</li>';
                      break;
                    }
                  //-- ############### TERMINA CODE VEHÍCULO EN BASCULA #################### --//
                  //-- ############### INICIA CODE INICIA CARGA DE VEHICULO #################### --//
                  $inicia_carga = date_create($consulta_carga_status[$i]["INICIA_CARGA"]);
                  $inicia_carga_fecha = date_format($inicia_carga, "d-m-Y") ;
                  $inicia_carga_hora = date_format($inicia_carga, "H:i:s") ;
                    switch ( true ) {
                      case ( $consulta_carga_status[$i]["INICIA_CARGA"] == false ) || ( $inicia_carga_hora == "00:00:00" ):
                        echo '<li class="timeline__step">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> ... </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Inicia Carga</span>';
                    /*calculo de tiempo entre reg. vehiculo y hoy*/
                    if ($consulta_carga_status[$i]["BASCULA_VEHICULO"] == true){
                      $dif_min_carga = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$consulta_carga_status[$i]["BASCULA_VEHICULO"]);
                      if ($dif_min_carga>5){
                        echo '<i class="timeline__step-marker_red"><i class="fa fa-clock-o"></i></i>';
                      }else{
                        echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                      }
                    }else{
                        echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                    }
                        echo '</li>';
                      break;

                      default:
                        $carga_status = 3;// determina nivel del status que se encuentra la carga
                        echo '<li class="timeline__step done">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> '.$inicia_carga_hora.' </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Inicia Carga <br>'.$inicia_carga_fecha.'</span>';
                        if ($consulta_carga_status[$i]["TERMINA_CARGA"] == true){
                        /*calculo de tiempo entre reg. vehiculo y hoy*/
                        $dif_min_carga = $obj_agro_carga->dif_minutos($consulta_carga_status[$i]["TERMINA_CARGA"],$consulta_carga_status[$i]["INICIA_CARGA"]);
                          if($dif_min_carga>60){
                            echo '<i class="timeline__step-marker_red_fin"><i class="fa fa-truck"><i class="fa fa-long-arrow-up"></i></i></i>';
                          }else{
                            echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"><i class="fa fa-long-arrow-up"></i></i></i>';
                          }
                        }else{
                        echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"><i class="fa fa-long-arrow-up"></i></i></i>';
                        }
                        echo '</li>';
                      break;
                    }
                  //-- ############### TERMINA CODE INICIA CARGA DE VEHICULO #################### --//
                  //-- ############### INICIA CODE FINALIZA CARGA DE VEHICULO #################### --//
                  $termina_carga = date_create($consulta_carga_status[$i]["TERMINA_CARGA"]);
                  $termina_carga_fecha = date_format($termina_carga, "d-m-Y") ;
                  $termina_carga_hora = date_format($termina_carga, "H:i:s") ;
                    switch ( true ) {
                      case ( $consulta_carga_status[$i]["TERMINA_CARGA"] == false ) || ( $termina_carga_hora == "00:00:00" ):
                        echo '<li class="timeline__step">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> ... </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Termina Carga </span>';
                        /*calculo de tiempo entre reg. vehiculo y hoy*/
                        if ($consulta_carga_status[$i]["INICIA_CARGA"] == true){
                          $dif_min_carga = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$consulta_carga_status[$i]["INICIA_CARGA"]);
                          if ($dif_min_carga>60){
                            echo '<i class="timeline__step-marker_red"><i class="fa fa-clock-o"></i></i>';
                          }else{
                            echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                          }
                        }else{
                            echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                        }
                        echo '</li>';
                      break;

                      default:
                        $carga_status = 4;// determina nivel del status que se encuentra la carga
                        echo '<li class="timeline__step done">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> '.$termina_carga_hora.' </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Termina Carga <br>'.$termina_carga_fecha.'</span>';
                        echo '<i class="timeline__step-marker_green"><i class="fa fa-truck"></i></i>';
                        echo '</li>';
                      break;
                    }
                  //-- ############### TERMINA CODE FINALIZA CARGA DE VEHICULO #################### --//
                  //-- ############### INICIA CODE DOCUMENTACIÓN DE VEHICULO #################### --//
                  $fecha_documentacion = date_create($consulta_carga_status[$i]["FECHA_DOCUMENTACION"]);
                  $fecha_documentacion_fecha = date_format($fecha_documentacion, "d-m-Y") ;
                  $fecha_documentacion_hora = date_format($fecha_documentacion, "H:i:s") ;
                    switch ( true ) {
                      case ( $consulta_carga_status[$i]["FECHA_DOCUMENTACION"] == false ) || ( $fecha_documentacion_hora == "00:00:00" ):
                        echo '<li class="timeline__step">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> ... </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Documentación </span>';
                        /*calculo de tiempo entre reg. vehiculo y hoy*/
                        if ($consulta_carga_status[$i]["TERMINA_CARGA"] == true){
                          $dif_min_carga = $obj_agro_carga->dif_minutos(strftime("%d-%m-%Y %H:%M:%S"),$consulta_carga_status[$i]["TERMINA_CARGA"]);
                          if ($dif_min_carga>5){
                            echo '<i class="timeline__step-marker_red"><i class="fa fa-clock-o"></i></i>';
                          }else{
                            echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                          }
                        }else{
                            echo '<i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>';
                        }
                        echo '</li>';
                      break;

                      default:
                        echo '<li class="timeline__step done">';
                        echo '<input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">';
                        echo '<label class="timeline__step-label" for="trigger1{{identifier}}">';
                        echo '<span class="timeline__step-content"><i class="fa fa-clock-o"></i> '.$fecha_documentacion_hora.' </span>';
                        echo '</label>';
                        echo '<span class="timeline__step-title"> Documentación <br>'.$fecha_documentacion_fecha.'</span>';
                        echo '<i class="timeline__step-marker_green"><i class="fa fa-file-text"></i></i>';
                        echo '</li>';
                      break;
                    }
                  //-- ############### TERMINA CODE DOCUMENTACIÓN DE VEHICULO #################### --//
                  ?>
                      <li class="timeline__step">
                        <input class="timeline__step-radio" id="trigger1{{identifier}}" name="trigger{{identifier}}" type="radio">
                        <label class="timeline__step-label" for="trigger1{{identifier}}">
                        <span class="timeline__step-content"><i class="fa fa-clock-o"></i> ...</span>
                        </label>
                        <span class="timeline__step-title"> Despacho de Vehículo</span>
                        <i class="timeline__step-marker"><i class="fa fa-clock-o"></i></i>
                      </li>
                  </ol>
                  <div style="display: none;">status<?= $carga_status ?></div>
                  <!-- **************** TERMINA LINEA DE TIEMPO PARA EL ESTADO DE OFC **************** -->
                  <br>  <br>  <br>
                </td>
              </tr>
            <?php } } ?>
            </tbody>
          </table>
        </div><!-- ./table-responsive -->
      <!-- termina tabla para status de cargas -->

    <!-- TERMINA BODY DE TAB CARGAS EN PROCESO -->
    </div><!-- ./tab_ofc_pro -->
    <div id="tab_ofc_fin" class="tab-pane fade"><!-- tab_ofc_fin -->
    <!-- INICA BODY DE TAB CARGAS FINALIZADAS -->

      <!-- INICIA TABLA CARGAS FINALIZADAS -->
      <br>
      <div class="text-light-blue" align="center"><strong><i class="fa fa-truck"></i> CARGAS FINALIZADAS</strong></div>
      <br>

      <div class="table-responsive" align="center"><!-- table-responsive -->
      <table id="tabla_op_fin_car" class="table-striped table-bordered table-hover" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small">PLAZA</th>
            <th class="small">ALMACEN</th>
            <th class="small">CLIENTE</th>
            <th class="small">TRANSPORTE</th>
            <th class="small">PLACAS</th>
            <th class="small">CHOFER</th>
            <th class="small">INICIO</th>
            <th class="small">FINALIZA</th>
            <th class="small">TIEMPO</th>
            <th class="small">DETALLES</th>
          </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $i <count($consulta_carga_status) ; $i++) {
          if ( $consulta_carga_status[$i]["FECHA_DOCUMENTACION"] == true ){
          $cargas_finalizadas[] = $i;
          //calculo de tiempo en minutos
          $dif_minutos = $obj_agro_carga->dif_minutos($consulta_carga_status[$i]["FECHA_DOCUMENTACION"],$consulta_carga_status[$i]["R_VEHICULO"]);
          if ($dif_minutos>80){
            $color_td = "text-red";
          }else{
            $color_td = "text-green";
          }
        ?>
          <tr class="<?=$color_td?>">
            <td class="small"><?= $consulta_carga_status[$i]["PLAZA"] ?></td>
            <td class="small"><?= $consulta_carga_status[$i]["ALMACEN"] ?></td>
            <td class="small"><?= $consulta_carga_status[$i]["CLIENTE"] ?></td>
            <td class="small"><?= $consulta_carga_status[$i]["TRANSPORTE"] ?></td>
            <td class="small"><?= $consulta_carga_status[$i]["PLACAS"] ?></td>
            <td class="small"><?= $consulta_carga_status[$i]["CHOFER"] ?></td>
            <td class="small"><?= $consulta_carga_status[$i]["R_VEHICULO"] ?></td>
            <td class="small"><?= $consulta_carga_status[$i]["FECHA_DOCUMENTACION"] ?></td>
            <?php
            $fechaInicio = $consulta_carga_status[$i]["R_VEHICULO"];
            $fechaFin = $consulta_carga_status[$i]["FECHA_DOCUMENTACION"];
            $total_tiempo_ofc = $obj_agro_carga->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
            ?>
            <!-- inicia code total de tiempo menos o mas cargas -->
              <?php
                if (strtotime($fechaInicio) < strtotime($fechaFin) ){
                  echo '<td class="small">'.$total_tiempo_ofc.'</td>';
                }else{
                  echo '<td class="small">-'.$total_tiempo_ofc.'</td>';
                }
              ?>
            <!-- termina code total de tiempo menos o mas cargas -->
            <td class="small"><a class="fancybox fancybox.iframe" href="<?= 'agronegocios_det.php?op=ofc&par='.$consulta_carga_status[$i]["PARTIDA"].'&ofc='.$consulta_carga_status[$i]["OFC"].'&fol='.$consulta_carga_status[$i]["FOLIO_DET"] ?>">
                              <span class="badge bg-blue btn"> <i class="fa fa-eye"></i> Detalles </span> <b style="display:none;"><br>(Tiempo: <?=$dif_minutos?> Min.)</b>
                              </a></td>
          </tr>
        <?php } } ?>
        </tbody>
      </table>
      </div><!-- ./table-responsive -->
      <!-- INICIA TABLA CARGAS FINALIZADAS -->

    <!-- TERMINA BODY DE TAB CARGAS FINALIZADAS -->
    </div><!-- ./tab_ofc_fin -->
  </div>
<!-- TERMINA TAB PARA CARGAS -->


    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION OPERACIONES CARGAS ########################### -->

<!-- ########################### TERMINA SECCION OPERACIONES DESCARGAS ########################### -->
<?php
//OPERACIONES CARGAS-DESCARGAS EN PROCESO,FIN,TOTAL
$total_carga_proceso = count($carga_proceso);
$total_cargas_finalizadas = count($cargas_finalizadas);
$total_cargas_agro = $total_carga_proceso+$total_cargas_finalizadas;

$total_descarga_proceso = count($descarga_proceso);
$total_descargas_finalizadas = count($descargas_finalizadas);
$total_descargas_agro = $total_descarga_proceso+$total_descargas_finalizadas;
?>


    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
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
<script>
$(document).ready(function(){

  $("#btn_his_car").click(function(){
    $("#div_historial_cargas\\[\\]").show()
    $("#div_historial_descargas\\[\\]").hide()
  });

  $("#btn_his_des").click(function(){
    $("#div_historial_descargas\\[\\]").show();
    $("#div_historial_cargas\\[\\]").hide();
  });

});

// script para poner total status de operaciones
$(document).ready(function(){
  $("#widgets_cargas_fin_agro\\[\\]").text('<?= $total_cargas_finalizadas ?>');
  $("#widgets_cargas_pro_agro\\[\\]").text('<?= $total_carga_proceso ?>');
  $("#widgets_cargas_t_agro\\[\\]").text('<?= $total_cargas_agro ?>');

  $("#widgets_descargas_fin_agro\\[\\]").text('<?= $total_descargas_finalizadas ?>');
  $("#widgets_descargas_pro_agro\\[\\]").text('<?= $total_descarga_proceso ?>');
  $("#widgets_descargas_t_agro\\[\\]").text('<?= $total_descargas_agro ?>');
})
</script>
<!-- GUARDA TAB SELECCIONADO -->
<script type="text/javascript">
$(function() {
  $('#tab_ofc_proceso').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_ofc_status', $(e.target).attr('href'));
    });

    $('#tab_ofc_finalizado').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_ofc_status', $(e.target).attr('href'));
    });

    var activeTab_ofc_status = localStorage.getItem('activeTab_ofc_status');
  if(activeTab_ofc_status){
    $('#myTab_ofc a[href="' + activeTab_ofc_status + '"]').tab('show');
  }
/////////////////////////////////////////////////////////////////////
    $('#tab_otfc_cor').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_otfc', $(e.target).attr('href'));
    });

    $('#otfc_occ').on('shown.bs.tab', function (e) {
       localStorage.setItem('activeTab_otfc', $(e.target).attr('href'));
    });

    var activeTab_otfc = localStorage.getItem('activeTab_otfc');
  if(activeTab_otfc){
    $('#myTab_otfc a[href="' + activeTab_otfc + '"]').tab('show');
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
<!-- Inicia Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">

  $(".agro_historial").select2({
   placeholder: "Elija una opción",
  allowClear: true
});
</script>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>

<script>
$('#fec_rango_agro\\[\\]').daterangepicker(
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
            'El mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function (start, end) {
          $('#fec_ini_agro\\[\\]').val(start.format('DD-MM-YYYY'));
          $('#fec_fin_agro\\[\\]').val(end.format('DD-MM-YYYY'));
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
/*TABLA OPERACIONES FINALIZADAS*/
$(document).ready(function() {

  $('#tabla_op_fin_car,#tabla_op_fin_des').DataTable( {
        stateSave: true,
        "ordering": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "language": {
        "url": "../plugins/datatables/Spanish.json"
        },
        //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
        dom: 'lBfrtip',//lBfrtip muestra opcion para ver n datos
            buttons: [

              {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                exportOptions: {//muestra/oculta visivilidad de columna
                    columns: ':visible'
                },
                title: 'OPERACIONES <?= $titulo_fecha ?>',
              },

              {
                extend: 'print',
                text: '<i class="fa fa-print"></i>',
                titleAttr: 'Imprimir',
                exportOptions: {//muestra/oculta visivilidad de columna
                    columns: ':visible',
                },
                title: 'OPERACIONES <?= $titulo_fecha ?>',
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

////////////////////////////// INICIA CODE DE FILTRO MAYO-MENOR HORAS CARGAS /////////////
/* Custom filtering function which will search data in column four between two values */
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = parseInt( $('#cargas_min').val(), 10 );
        var max = parseInt( $('#cargas_max').val(), 10 );
        var minutos = parseFloat( data[0] ) || 0; // use data for the age column

        if ( ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && minutos <= max ) ||
             ( min <= minutos   && isNaN( max ) ) ||
             ( min <= minutos   && minutos <= max ) )
        {
            return true;
        }
        return false;
    }
);

$(document).ready(function() {
    ////////// INICIA CODE SELECT STATUS VEHICULO CARGA /////////
    var select_car = $('#tabla_proceso_carga').DataTable();
    // #myInput is a <input type="text"> element
    $('#select_status_car').click( function() {
        select_car.search( this.value ).draw();
    } );
    ////////// TERMINA CODE SELECT STATUS VEHICULO CARGA /////////

    var table = $('#tabla_proceso_carga').DataTable();

    // Event listener to the two range filtering inputs to redraw on input
    //$('#min, #max').keyup( function() { //defaul
    $('#boton_carga_min').click( function() {

        $('#cargas_max').val(79);
        $('#cargas_min').val("");
        $('#descargas_max').val("");
        $('#descargas_min').val("");
        console.log( "cargas <60 min" );
        table.draw();
    } );

    $('#boton_carga_max').click( function() {
        $('#cargas_max').val("");
        $('#cargas_min').val(80);
        $('#descargas_max').val("");
        $('#descargas_min').val("");
        console.log( "cargas >60 min" );
        table.draw();
    } );

});

////////////////////////////// INICIA CODE DE FILTRO MAYO-MENOR HORAS DESCARGAS /////////////
/* Custom filtering function which will search data in column four between two values */
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = parseInt( $('#descargas_min').val(), 10 );
        var max = parseInt( $('#descargas_max').val(), 10 );
        var minutos = parseFloat( data[0] ) || 0; // use data for the age column

        if ( ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && minutos <= max ) ||
             ( min <= minutos   && isNaN( max ) ) ||
             ( min <= minutos   && minutos <= max ) )
        {
            return true;
        }
        return false;
    }
);

$(document).ready(function() {
////////// INICIA CODE SELECT STATUS VEHICULO DESCARGA /////////
    var select_des = $('#tabla_proceso_descarga').DataTable();
    // #myInput is a <input type="text"> element
    $('#select_status_des').click( function() {
        select_des.search( this.value ).draw();
    } );
    ////////// TERMINA CODE SELECT STATUS VEHICULO DESCARGA /////////

    var table = $('#tabla_proceso_descarga').DataTable();

    // Event listener to the two range filtering inputs to redraw on input
    //$('#min, #max').keyup( function() { //defaul
    $('#boton_descarga_min').click( function() {
        $('#descargas_max').val(74);
        $('#descargas_min').val("");
        $('#cargas_max').val("");
        $('#cargas_min').val("");
        console.log( "descargas <60 min" );
        table.draw();
    } );

    $('#boton_descarga_max').click( function() {
        $('#descargas_max').val("");
        $('#descargas_min').val(75);
        $('#cargas_max').val("");
        $('#cargas_min').val("");
        console.log( "descargas >60 min" );
        table.draw();
    } );
});
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
      $('#content_img_modal_his\\[\\]').fadeIn(10).html('<div><img class="img-responsive center-block" src="../dist/img/gif-argo-carnado-circulo_l2.gif"/></div>');
  }});
});

<?php for ($i=1; $i <=10; $i++) { ?>
$('.click_btn_text<?=$i?>').click(function(){
  $.ajax({url: '../class/Agronegocios.php', success: function(result){
    $('.content_text_btn<?=$i?>').fadeIn(10).html('<b class="text-blue"><i class="fa fa-cog fa-spin fa-lg fa-fw"></i> CARGANDO...</b>');
  }});
});
<?php } ?>
</script>
</html>
<?php conexion::cerrar($conn); ?>
