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
$_SESSION['modulo_actual'] = 16;//MODULO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], $_SESSION['modulo_actual'] );
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/*----------------------INICIA INSTANCIAS----------------------*/
include_once '../class/Comp_ingre_cliente.php';

$ins_obj_ingre_clie = new Comparativo();
$consulta_fecha = $ins_obj_ingre_clie->consulta_fecha();

/*----------------------ACTIVA INTRO AL INICIAR----------------------*/
if(isset($_POST["activa_intro_comp_ingre_cli"]))
$_SESSION["activa_intro_comp_ingre_cli"] = $_POST["activa_intro_comp_ingre_cli"];
$activa_intro_comp_ingre_cli = $_SESSION["activa_intro_comp_ingre_cli"];

/*----------------------SESIONES----------------------*/
/*======GUARDA EL VALOR DEL MES A COMPARAR======*/
if ($_SESSION['fecha_ingre_clie'] == false){
  for ($i=0; $i <count($consulta_fecha) ; $i++)
  {
    $fecha_ingre_clie_anterior = strtotime ( '-1 month' , strtotime ( $consulta_fecha[$i]["ANIO"].'-'.$consulta_fecha[$i]["MES"] ) ) ;
    $fecha_ingre_clie_anterior = date ( 'Y-m' , $fecha_ingre_clie_anterior );
    $_SESSION['fecha_ingre_clie'] = $fecha_ingre_clie_anterior;
    $fecha_ingre_clie = $_SESSION['fecha_ingre_clie'];
  }
}else{
  if(isset($_POST['fecha_ingre_clie']))
  $_SESSION['fecha_ingre_clie'] = $_POST['fecha_ingre_clie'];
  $fecha_ingre_clie = $_SESSION['fecha_ingre_clie'];
}
/*====== FORMATO EN ESPAÑOL MESES======*/
$meses = array("ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SEP","OCT","NOV","DIC");
/*======GUARDA EL VALOR DEL MES ANTERIOR======*/
$mes_anterior = strtotime ( '-1 month' , strtotime ($fecha_ingre_clie) ) ;
$mes_anterior = date ( 'Y-m' , $mes_anterior );
/*======GUARDA EL VALOR DEL COMPARATIVO AÑO ANTERIOR======*/
$anio_anterior = strtotime ( '-1 year' , strtotime ($fecha_ingre_clie) ) ;
$anio_anterior = date ( 'Y-m' , $anio_anterior );
/*======GUARDA EL VALOR DEL ACUMULADO======*/
$acomulado = date("Y", strtotime($mes_anterior)).'-'.'01';
/*======GUARDA EL VALOR DEL ACUMULADO ANIO ANTERIOR======*/
$acomulado_anterior = date("Y", strtotime($anio_anterior)).'-'.'01';
/*======GUARDA LAS OPCIONES DE VISUALIZACIÓN ======*/
if ($_SESSION['op_visual'] == false){
    $_SESSION['op_visual'] = array(1,2,3);
    $op_visual = $_SESSION['op_visual'];
}else{
  if(isset($_POST['op_visual']))
  $_SESSION['op_visual'] = $_POST['op_visual'];
  $op_visual = $_SESSION['op_visual'];
}
/*======GUARDA EL TIPO DE GIRO======*/
if ($_SESSION['giro_ingre_clie'] == false){
    $_SESSION['giro_ingre_clie'] = 1;
    $giro_ingre_clie = $_SESSION['giro_ingre_clie'];
}else{
  if(isset($_POST['giro_ingre_clie']))
  $_SESSION['giro_ingre_clie'] = $_POST['giro_ingre_clie'];
  $giro_ingre_clie = $_SESSION['giro_ingre_clie'];
}
/*======GUARDA EL CLIENTE======*/
if(isset($_POST["comp_cliente"]))
  $_SESSION["comp_cliente"] = $_POST["comp_cliente"] ;
  $comp_cliente = $_SESSION["comp_cliente"];
/*======GUARDA EL ID CLIENTE======*/
if(isset($_POST["comp_id_cliente"]))
  $_SESSION["comp_id_cliente"] = $_POST["comp_id_cliente"] ;
  $comp_id_cliente = $_SESSION["comp_id_cliente"];


?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">

<script>
window.onload = function() {
  addHints();
};
</script>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Dashboard Comercial <small>Comparativos ingresos de clientes</small></h1>

    </section>

    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
    <br><br>

<!-- ############################ INICIA SECCION DE LA TABLA PRINCIPAL ############################# -->
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title"> </h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <i class="fa fa-filter"></i><h3 class="box-title">FILTROS</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->

      <form method="post">
        <div class="box-body" style="display: block;">

          <div class="row">

            <div class="col-lg-3">
              <label>Selecciona el mes a comparar :</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="fecha_ingre_clie" value="<?= $fecha_ingre_clie ?>" class="form-control pull-right" id="datepicker">
              </div>
            </div>

            <div class="col-lg-3">
              <input type="hidden" name="comp_cliente" value="">
              <input type="hidden" name="comp_id_cliente" value="">
              <label>Giro :</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-chevron-circle-right"></i></span>
                <select class="form-control" name="giro_ingre_clie">
                  <option value="1" <?php if ($giro_ingre_clie == 1){echo 'selected';} ?> >Habilitado</option>
                  <option value="2" <?php if ($giro_ingre_clie == 2){echo 'selected';} ?> >Depositante en Bodega Directa</option>
                  <option value="3" <?php if ($giro_ingre_clie == 3){echo 'selected';} ?> >Tercero Depositante Bod. Hab.</option>
                </select>
              </div>

            </div>

            <div class="col-lg-6">
              <label>Opciones de visualización :</label>
              <div class="input-group">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" id="checkbox_visual1" <?php if($op_visual[0]==true){echo "checked";}?> >Comparativo vs mes anterior
                  </label>
                  <label>
                    <input type="checkbox" id="checkbox_visual2" <?php if($op_visual[1]==false){echo "checked";}?> >Comparativo
                  </label>
                  <label>
                    <input type="checkbox" id="checkbox_visual3" <?php if($op_visual[2]==false){echo "checked";}?> >Acumulado
                  </label>
                </div>
              </div>
            </div>

          </div>

        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          <div class="input-group">
            <input type="hidden" name="op_visual[]" id="text_visual1" <?php if($op_visual[0]==true){echo "value='1'";}else{echo "value='0'";}?> >
            <input type="hidden" name="op_visual[]" id="text_visual2" <?php if($op_visual[1]==true){echo "value='2'";}else{echo "value='0'";}?> >
            <input type="hidden" name="op_visual[]" id="text_visual3" <?php if($op_visual[2]==true){echo "value='3'";}else{echo "value='0'";}?> >
          </div>
          <button type="submit" class="btn btn-primary click_modal_cargando">Ok!</button>
        </div>
      </form>
      </div>

      <?php if ($comp_cliente == true){?>
      <h5 class="text-blue text-center"><i class="fa fa-user"></i> CLIENTE: <?=$comp_cliente?></h5>
      <form method="post">
      <input type="hidden" name="comp_cliente" value="">
      <input type="hidden" name="comp_id_cliente" value="">
      <button type="submit" class="btn bg-maroon btn-flat margin click_modal_cargando"><i class="fa fa-reply"></i> Regresar</button>
      </form>
      <?php } ?>

      <div class="table-responsive"><!-- table-responsive -->
      <table id="tabla_lista" class="table table-striped table-hover table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small"></th>
            <!-- COMPARATIVO #1 -->
            <?php if ($op_visual[0] == 1){ ?>
            <th class="small" colspan="5">COMPARATIVO VS MES ANTERIOR</th>
            <?php } ?>
            <!-- COMPARATIVO #2 -->
            <?php if ($op_visual[1] == 2){ ?>
            <th class="small" colspan="4">COMPARATIVO</th>
            <?php } ?>
            <!-- COMPARATIVO #3 -->
            <?php if ($op_visual[2] == 3){ ?>
            <th class="small" colspan="4">ACUMULADO</th>
            <?php } ?>
          </tr>
          <tr>
            <th class="small">CLIENTE</th>
            <!-- COMPARATIVO #1 -->
            <?php if ($op_visual[0] == 1){ ?>
            <th class="small"><?= $meses[date(date("n", strtotime($mes_anterior)))-1].'-'.date("Y", strtotime($mes_anterior)) ?></th>
            <th class="small"><?= $meses[date(date("n", strtotime($fecha_ingre_clie)))-1].'-'.date("Y", strtotime($fecha_ingre_clie)) ?></th>
            <th class="small">DIFERENCIA</th>
            <th class="small" style="width: 90px"></th>
            <?php } ?>
            <!-- COMPARATIVO #2 -->
            <?php if ($op_visual[1] == 2){ ?>
            <th class="small"><?= $meses[date(date("n", strtotime($anio_anterior)))-1].'-'.date("Y", strtotime($anio_anterior)) ?></th>
            <th class="small"><?= $meses[date(date("n", strtotime($fecha_ingre_clie)))-1].'-'.date("Y", strtotime($fecha_ingre_clie)) ?></th>
            <th class="small">DIFERENCIA</th>
            <th class="small" style="width: 90px"></th>
            <?php } ?>
            <!-- COMPARATIVO #3 -->
            <?php if ($op_visual[2] == 3){ ?>
            <th class="small"><?= $meses[date(date("n", strtotime($acomulado_anterior)))-1].'/'.$meses[date(date("n", strtotime($anio_anterior)))-1].'-'.date("Y", strtotime($anio_anterior)) ?></th>
            <th class="small">
              <?= $meses[date(date("n", strtotime($acomulado)))-1].'/'.$meses[date(date("n", strtotime($fecha_ingre_clie)))-1].'-'.date("Y", strtotime($fecha_ingre_clie)) ?>
            </th>
            <th class="small">DIFERENCIA</th>
            <th class="small" style="width: 90px"></th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
        <?php
        $consulta_cliente = $ins_obj_ingre_clie->consulta_cliente($giro_ingre_clie,$comp_cliente);
        for ($i=0; $i <count($consulta_cliente) ; $i++) {
        ?>
          <tr>
            <td class="small">
              <form method="post">
              <input type="hidden" name="comp_cliente" value="<?=$consulta_cliente[$i]["CLIENTE"]?>">
              <input type="hidden" name="comp_id_cliente" value="<?=$consulta_cliente[$i]["ID_CLIENTE"]?>">
              <button type="submit" class="btn-link bnt-xs"><?= '('.$consulta_cliente[$i]["ID_CLIENTE"].') '.$consulta_cliente[$i]["CLIENTE"]?></button>
              </form>
            </td>
            <!-- COMPARATIVO #1 -->
            <?php
            if ($op_visual[0] == 1){
              $consulta_ingreso_mes_anterior = $ins_obj_ingre_clie->consulta_ingreso(1,$consulta_cliente[$i]["ID_CLIENTE"],date("Y", strtotime($mes_anterior)),date("m", strtotime($mes_anterior)));
              $consulta_ingreso_mes = $ins_obj_ingre_clie->consulta_ingreso(1,$consulta_cliente[$i]["ID_CLIENTE"],date("Y", strtotime($fecha_ingre_clie)),date("m", strtotime($fecha_ingre_clie)));
            ?>
            <?php for ($j=0; $j < count($consulta_ingreso_mes_anterior) ; $j++) { $saldo_mes_anterior[$j] = $consulta_ingreso_mes_anterior[$j]["INGRESO"]; ?>
            <td class="small"><?= "$".number_format($consulta_ingreso_mes_anterior[$j]["INGRESO"],2) ?></td>
            <?php } ?>
            <?php for ($k=0; $k < count($consulta_ingreso_mes) ; $k++) { $saldo_mes[$k] = $consulta_ingreso_mes[$k]["INGRESO"]; ?>
            <td class="small"><?= "$".number_format($consulta_ingreso_mes[$k]["INGRESO"],2) ?></td>
            <?php } ?>
            <td class="small"><?php $diferencia_com_mes = array_sum($saldo_mes) - array_sum($saldo_mes_anterior); echo '$'.number_format($diferencia_com_mes,2) ?></td>
            <td class="small">
              <?php
              if ($diferencia_com_mes >0){
                echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="green" sparkfillcolor="#69F0AE">'.array_sum($saldo_mes_anterior).','.array_sum($saldo_mes).'</span><span class="badge bg-green">↑</span>';}
                else if($diferencia_com_mes == 0){
                echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="orange" sparkfillcolor="#FFE082">'.array_sum($saldo_mes_anterior).','.array_sum($saldo_mes).'</span><span class="badge bg-yellow">→</span>';}
                else{echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="red" sparkfillcolor="#FFCDD2">'.array_sum($saldo_mes_anterior).','.array_sum($saldo_mes).'</span><span class="badge bg-red">↓</span>';}
              ?>
            </td>
            <?php } ?>
            <!-- COMPARATIVO #2 -->
            <?php if ($op_visual[1] == 2){
              $consulta_ingreso_anio_anterior = $ins_obj_ingre_clie->consulta_ingreso(1,$consulta_cliente[$i]["ID_CLIENTE"],date("Y", strtotime($anio_anterior)),date("m", strtotime($anio_anterior)));
              $consulta_ingreso_anio = $ins_obj_ingre_clie->consulta_ingreso(1,$consulta_cliente[$i]["ID_CLIENTE"],date("Y", strtotime($fecha_ingre_clie)),date("m", strtotime($fecha_ingre_clie)));
            ?>
            <?php for ($l=0; $l < count($consulta_ingreso_anio_anterior) ; $l++) { $saldo_anio_anterior[$l] = $consulta_ingreso_anio_anterior[$l]["INGRESO"]; ?>
            <td class="small"><?= "$".number_format($consulta_ingreso_anio_anterior[$l]["INGRESO"],2) ?></td>
            <?php } ?>
            <?php for ($m=0; $m < count($consulta_ingreso_anio) ; $m++) { $saldo_anio[$m] = $consulta_ingreso_anio[$m]["INGRESO"]; ?>
            <td class="small"><?= "$".number_format($consulta_ingreso_anio[$m]["INGRESO"],2) ?></td>
            <?php } ?>
            <td class="small"><?php $diferencia_com_anio = array_sum($saldo_anio) - array_sum($saldo_anio_anterior); echo '$'.number_format($diferencia_com_anio,2) ?></td>
            <td class="small">
              <?php
              if ($diferencia_com_anio >0){
                echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="green" sparkfillcolor="#69F0AE">'.array_sum($saldo_anio_anterior).','.array_sum($saldo_anio).'</span><span class="badge bg-green">↑</span>';}
                else if($diferencia_com_anio == 0){
                echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="orange" sparkfillcolor="#FFE082">'.array_sum($saldo_anio_anterior).','.array_sum($saldo_anio).'</span><span class="badge bg-yellow">→</span>';}
                else{echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="red" sparkfillcolor="#FFCDD2">'.array_sum($saldo_anio_anterior).','.array_sum($saldo_anio).'</span><span class="badge bg-red">↓</span>';}
              ?>
            </td>
            <?php } ?>
            <!-- COMPARATIVO #3 -->
            <?php
            if ($op_visual[2] == 3){
              $consulta_diferencia_anio_anterior = $ins_obj_ingre_clie->consulta_ingreso(2,$consulta_cliente[$i]["ID_CLIENTE"],date("Y", strtotime($anio_anterior)),date("m", strtotime($anio_anterior)));
              $consulta_diferencia_anio = $ins_obj_ingre_clie->consulta_ingreso(2,$consulta_cliente[$i]["ID_CLIENTE"],date("Y", strtotime($fecha_ingre_clie)),date("m", strtotime($fecha_ingre_clie)));
            ?>
            <?php for ($a=0; $a < count($consulta_diferencia_anio_anterior) ; $a++) { $saldo_diferencia_anio_anterior[$a] = $consulta_diferencia_anio_anterior[$a]["INGRESO"]; ?>
            <td class="small"><?= '$'.number_format($consulta_diferencia_anio_anterior[$a]["INGRESO"],2) ?></td>
            <?php } ?>
            <?php for ($b=0; $b < count($consulta_diferencia_anio) ; $b++) { $saldo_diferencia_anio[$b] = $consulta_diferencia_anio[$b]["INGRESO"]; ?>
            <td class="small"><?= '$'.number_format($consulta_diferencia_anio[$b]["INGRESO"],2) ?></td>
            <?php } ?>
            <td class="small"><?php $diferencia_diferencia = array_sum($saldo_diferencia_anio) - array_sum($saldo_diferencia_anio_anterior); echo "$".number_format($diferencia_diferencia,2); ?></td>
            <td class="small">
              <?php
              if ($diferencia_diferencia >0){
                echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="green" sparkfillcolor="#69F0AE">'.array_sum($saldo_diferencia_anio_anterior).','.array_sum($saldo_diferencia_anio).'</span><span class="badge bg-green">↑</span>';}
                else if($diferencia_diferencia == 0){
                echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="orange" sparkfillcolor="#FFE082">'.array_sum($saldo_diferencia_anio_anterior).','.array_sum($saldo_diferencia_anio).'</span><span class="badge bg-yellow">→</span>';}
                else{echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="red" sparkfillcolor="#FFCDD2">'.array_sum($saldo_diferencia_anio_anterior).','.array_sum($saldo_diferencia_anio).'</span><span class="badge bg-red">↓</span>';}
              ?>
            </td>
            <?php } ?>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div><!-- /.table-responsive -->


    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION DE LA TABLA PRINCIPAL ########################### -->

<!-- ############################ INICIA SECCION DE LA GRAFICA INGRESOS POR CLIENTE ############################# -->
<?php if ($comp_cliente == true){ ?>
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title"> </h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->
      <form method="POST">
        <input type="hidden" name="comp_cliente" value="">
        <input type="hidden" name="comp_id_cliente" value="">
        <button type="submit" class="btn bg-maroon btn-flat margin click_modal_cargando"><i class="fa fa-reply"></i> Regresar</button>
      </form>
      <div id="grafica_monto_anio"></div>

    </div><!--/.box-body-->
  </div>
</section>
<?php } ?>
<!-- ########################### TERMINA SECCION DE LA GRAFICA INGRESOS POR CLIENTE ########################### -->



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
<!-- Script para los checkbox de visualizaxion -->
<script>
  $("#checkbox_visual1,#checkbox_visual2,#checkbox_visual3").click(function() {
    if ($("#checkbox_visual1").is(':checked')) {
        $("#text_visual1").val(1);
    }else{
        $("#text_visual1").val(0);
    }
    //////////////////
    if ($("#checkbox_visual2").is(':checked')) {
        $("#text_visual2").val(2);
    }else{
        $("#text_visual2").val(0);
    }
    //////////////////
    if ($("#checkbox_visual3").is(':checked')) {
        $("#text_visual3").val(3);
    }else{
        $("#text_visual3").val(0);
    }
    //////////////////
  });
</script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Acomoda secciones -->
<script src="../dist/js/move_section.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- bootstrap datepicker -->
<script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
  //Date picker
    $('#datepicker').datepicker({
      autoclose: true,
      language: "es",
       format: "yyyy-mm",
    viewMode: "months",
    minViewMode: "months",
    startView: 1,
  minViewMode: 1
    });
</script>
<!-- Intro Plugins -->
<script src="../plugins/intro/intro.js"></script>
<script type="text/javascript">

function addHints(){
  intro = introJs();
    intro.setOptions({
      "hintButtonLabel": "OK",
      hints: [
        {
          element: document.querySelector('.step1'),
          hint: "Botón para regresar a la gráfica principal",
          hintPosition: 'bottom-middle',
        },
        {
          element: '.step2',
          hint: '<i class="fa fa-sort-amount-desc"></i> Botón para ordenar ascendente o descendente.',
          position: 'left'
        },
        {
          element: '.step3',
          hint: "Botón para exportar la tabla a Excel.",
          hintPosition: 'top-middle'
        },
      ]
    });

    intro.addHints();
    <?php if ($activa_intro_comp_ingre_cli == false){?>  tutorial_modal(); <?php } ?>
}

function tutorial_modal(){
  introJs().setOptions({
    'showStepNumbers':'false',
    'skipLabel':'omitir',
    'prevLabel':'atras',
    'nextLabel':'siguiente',
    'doneLabel':'finalizado'
  }).start();
};
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
<script>
  $(document).ready(function() {
    $('#tabla_lista').DataTable({
      stateSave: true,
      select: true,
      "ordering": false,
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
              title: 'Comparativo Ingresos Clientes',
            },

            {
              extend: 'print',
              text: '<i class="fa fa-print"></i>',
              titleAttr: 'Imprimir',
              exportOptions: {//muestra/oculta visivilidad de columna
                  columns: ':visible',
              },
              title: '<h5>Comparativo Ingresos Clientes</h5>',
            },

            //button_view
          ],
      //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    });
  });
</script>
<!-- Sparkline -->
<script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
<script>
  // Line charts taking their values from the tag
    $('.sparkline-1').sparkline('html',
        {type: 'line', width: '6em',});

    $("#sparkline").sparkline([5,6], {
    type: 'line',
    width: '80',
    lineColor: '#ddd7d7',
    fillColor: '#aaffaa',
    lineWidth: 0.2,
    spotColor: '#56aaff'});

    $('.sparklines_demo').sparkline('html', { enableTagOptions: true });
</script>
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script>
  $(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });

    var categories = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    //var data2 = [3,5,3,11,4,13,9,5,2,3,2,1];
    var data1 = [
    <?php
    $grafica_monto_1 = $ins_obj_ingre_clie->grafica_monto($comp_id_cliente,date("Y", strtotime($fecha_ingre_clie)));
    for ($i=0; $i <count($grafica_monto_1) ; $i++) {
      echo $grafica_monto_1[$i]["INGRESO"].',';
    }
    ?>
    ];
    var data2 = [
    <?php
    $grafica_monto_2 = $ins_obj_ingre_clie->grafica_monto($comp_id_cliente,date("Y", strtotime($anio_anterior)));
    for ($i=0; $i <count($grafica_monto_2) ; $i++) {
      echo $grafica_monto_2[$i]["INGRESO"].',';
    }
    ?>
    ];
    $('#grafica_monto_anio').highcharts({
        chart: {
            type: 'areaspline'
        },
         title: {
            text: 'INGRESOS CLIENTE <?=$comp_cliente?> AÑO <?= date("Y", strtotime($fecha_ingre_clie)).'-'.date("Y", strtotime($anio_anterior))?>'
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 150,
            y: 100,
            floating: true,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },

        yAxis: {
            lineWidth: 2,
            //min: 0,
            offset: 10,
            tickWidth: 1,
            title: {
                text: 'Monto'
            },
            labels: {
                formatter: function () {
                    return this.value;
                }
            }
        },
        tooltip: {
            shared: true,
            valueSuffix: ' pesos'
        },
        credits: {
            enabled: false
        },
        colors: ['#1ab394', '#464f88'],
        plotOptions: {
            areaspline: {
                fillOpacity: 0.4
            }
        },
        xAxis: {
            tickmarkPlacement: 'on',
            gridLineWidth: 1,
            categories: categories
        },
        series:  [{
            name: 'Ingresos <?=date("Y", strtotime($fecha_ingre_clie))?>',
            data: data1
        }, {
            name: 'Ingresos <?=date("Y", strtotime($anio_anterior))?>',
            data: data2
        }]

    });
});
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
