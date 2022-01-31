<?php
if( $_SERVER['REQUEST_METHOD'] == 'POST')
{
  header("location: ".$_SERVER["PHP_SELF"]." ");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '17');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/* .-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.- INICIA CODE INDEPENDIENTE .-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-  */
include_once '../class/Venta_promotor.php';
$insObj_VentaPromotor = new VentaPromotor();
/*declaracion de sessiones*/
/* var presupuesto */
$pre = 1;
if ( isset($_GET["pre"]) ){
	if ( $_GET["pre"] == 1 || $_GET["pre"] == 2 ){
		$pre = $_GET["pre"];
	}
}
/* var año */
$ultmAnio = $insObj_VentaPromotor->sql(" SELECT DISTINCT MAX(p.n_valor_anio) AS anio FROM co_promotor_fac_vs_pre p ");
$fecha = $ultmAnio[0]["ANIO"];
if ( isset($_GET["fecha"]) ){
	$validaAnio = $insObj_VentaPromotor->sql(" SELECT COUNT(p.n_valor_anio) valida FROM co_promotor_fac_vs_pre P WHERE p.n_valor_anio = ".(int)$_GET["fecha"]." ");
	if ( $validaAnio[0]["VALIDA"] ){ $fecha = (int)$_GET["fecha"];}
}
/* var promotor */
$promotor = "ALL";
if ( isset($_GET["promotor"]) ){
  if ( $pre == 1 ){
    $validaPro = $insObj_VentaPromotor->sql(" SELECT COUNT(*) valida FROM co_promotor_fac_vs_pre p WHERE p.id_promotor = ".(int)$_GET["promotor"]." AND p.n_valor_anio = ".$fecha." AND p.n_tipo = ".$pre." ");
    if ( $validaPro[0]["VALIDA"] > 0 ){ $promotor = $_GET["promotor"]; }
  }
}
/* var plaza*/
$plaza = "ALL";
if ( isset($_GET["plaza"]) ){
  if ( $promotor != "ALL" || $pre == 2 ){
    $validaPla = $insObj_VentaPromotor->sql(" SELECT COUNT(*) valida FROM plaza p WHERE p.i_empresa_padre = 1 AND p.iid_plaza = ".(int)$_GET["plaza"]." ");
    if ( $validaPla[0]["VALIDA"] > 0 ){ $plaza = $_GET["plaza"]; }
  }
}
/* var mes */
$mes = "ALL";
if ( isset($_GET["mes"]) ){
  if ( $_GET["mes"] == "Ene" || $_GET["mes"] == "Feb" || $_GET["mes"] == "Mar" || $_GET["mes"] == "Abr" || $_GET["mes"] == "May" || $_GET["mes"] == "Jun" || $_GET["mes"] == "Jul" || $_GET["mes"] == "Ago" || $_GET["mes"] == "Sep" || $_GET["mes"] == "Oct" || $_GET["mes"] == "Nov" || $_GET["mes"] == "Dic" ){
    $mes = $_GET["mes"];
  }
}
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- *********** INICIA INCLUDE CSS *********** -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/Select/select.dataTables.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/Responsive/css/responsive.dataTables.min.css">

<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Dashboard <small>Venta vs Presupuesto Promotores</small></h1>
    </section>
    <!-- Main content -->

    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

    <!-- ############################ INICIA SECCION TABLA PRESUPUESTADO ############################# -->
    <section>
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title"> <i class="fa fa-sliders"></i> Filtros </h3>
          <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="<?= basename($_SERVER['PHP_SELF']) ?>"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
          <?php } ?>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-line-chart"></i> Presupuesto:</span>
            <select class="form-control select2" id="selectPre">
              <option value="1" <?php if( $pre == 1 ){ echo "selected"; } ?> >PROMOTOR</option>
              <option value="2" <?php if( $pre == 2 ){ echo "selected"; } ?> >PLAZA</option>
            </select>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Año:</span>
            <select class="form-control select2" id="selectAnio"></select>
          </div>
        </div>
        <?php if ( $pre == 1 ){ ?>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user"></i> Promotor:</span>
            <select class="form-control select2" id="selectPromotor"></select>
          </div>
        </div>
        <?php } ?>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-sitemap"></i> Plaza:</span>
            <select class="form-control select2" id="selectPlaza"></select>
          </div>
        </div>

        </div><!--/.box-body-->
      </div>
    </section>
    <!-- ########################### TERMINA SECCION TABLA PRESUPUESTADO ########################### -->


    <!-- ############################ INICIA SECCION DE LA GRAFICA ############################# -->
    <section>
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"> <i class="fa fa-area-chart"></i> Grafica </h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <?php if( $mes == "ALL" ){ ?>
          <div id="graficaPreVsVta"></div>
          <?php }else{ ?>
          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
              <div id="graficaPreVsVta"></div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
              <div id="histMesConsol"></div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
              <div id="histMesNewCli"></div>
            </div>
          </div>
          <?php } ?>

        </div><!--/.box-body-->
      </div>
    </section>
    <!-- ########################### TERMINA SECCION DE LA GRAFICA ########################### -->



    <!-- ############################ INICIA SECCION TABLA PRESUPUESTADO ############################# -->
    <section>
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title" id="nomTabla2"><i class="fa fa-line-chart"></i> DISTRIBUCIÓN DE PRESUPUESTO <?=$fecha?> <?php if($pre == 2){echo "PLAZAS";}else{echo "PROMOTORES";} ?> </h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <style type="text/css"> #tablaInfoPre tr { cursor: pointer; } </style>
          <div class="table-responsive loadInfoPre">
            <table id="tablaInfoPre" class="table table-bordered table-hover table-striped">
              <thead>
                <tr>
                  <th style="background-color: #6099B4; color:white;"><?php if($pre == 2){echo "PLAZA";}else{echo "PROMOTOR";} ?></th>
                  <th style="background-color: #6099B4; color:white;">ENERO</th>
                  <th style="background-color: #6099B4; color:white;">FEBRERO</th>
                  <th style="background-color: #6099B4; color:white;">MARZO</th>
                  <th style="background-color: #6099B4; color:white;">ABRIL</th>
                  <th style="background-color: #6099B4; color:white;">MAYO</th>
                  <th style="background-color: #6099B4; color:white;">JUNIO</th>
                  <th style="background-color: #6099B4; color:white;">JULIO</th>
                  <th style="background-color: #6099B4; color:white;">AGOSTO</th>
                  <th style="background-color: #6099B4; color:white;">SEPTIEMBRE</th>
                  <th style="background-color: #6099B4; color:white;">OCTUBRE</th>
                  <th style="background-color: #6099B4; color:white;">NOVIEMBRE</th>
                  <th style="background-color: #6099B4; color:white;">DICIEMBRE</th>
                  <th style="background-color: #6099B4; color:white;">TOTAL</th>
                </tr>
              </thead>
              <tfoot>
                <tr <?php if ($pre == 2){if($plaza == "ALL"){echo 'style="background-color: #C8E5EE;';}}else{if($promotor == "ALL"){echo 'style="background-color: #C8E5EE;';}} ?> data-info="ALL">
                  <th>TOTAL</th>
                  <th id="totalMes01">$0.00</th>
                  <th id="totalMes02">$0.00</th>
                  <th id="totalMes03">$0.00</th>
                  <th id="totalMes04">$0.00</th>
                  <th id="totalMes05">$0.00</th>
                  <th id="totalMes06">$0.00</th>
                  <th id="totalMes07">$0.00</th>
                  <th id="totalMes08">$0.00</th>
                  <th id="totalMes09">$0.00</th>
                  <th id="totalMes10">$0.00</th>
                  <th id="totalMes11">$0.00</th>
                  <th id="totalMes12">$0.00</th>
                  <th id="totalSum">$0.00</th>
                </tr>
              </tfoot>
            </table>
          </div>

        </div><!--/.box-body-->
      </div>
    </section>
    <!-- ########################### TERMINA SECCION TABLA PRESUPUESTADO ########################### -->

    <!-- ########################### TERMINA MODAL PARA VER DETALLE DE LO FACTURADO ########################### -->



    <!-- ############################ INICIA SECCION DE LA GRAFICA ############################# -->
    <section>
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"> <?php if($pre == 2){echo '<i class="fa fa-sitemap"></i>';}else{echo '<i class="fa fa-user"></i>';} ?> PRESUPUESTO <?=$fecha?> VS RESULTADO <?=$fecha?> </h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <div class="table-responsive">
            <table id="tablaResVsPre" class="table table-bordered table-hover table-striped">
              <thead>
                <tr>
                  <th style="background-color: #36608B; color:white;">CONCEPTO</th>
                  <th style="background-color: #003366; color:white;">ENERO</th>
                  <th style="background-color: #003366; color:white;">FEBRERO</th>
                  <th style="background-color: #003366; color:white;">MARZO</th>
                  <th style="background-color: #003366; color:white;">ABRIL</th>
                  <th style="background-color: #003366; color:white;">MAYO</th>
                  <th style="background-color: #003366; color:white;">JUNIO</th>
                  <th style="background-color: #003366; color:white;">JULIO</th>
                  <th style="background-color: #003366; color:white;">AGOSTO</th>
                  <th style="background-color: #003366; color:white;">SEPTIEMBRE</th>
                  <th style="background-color: #003366; color:white;">OCTUBRE</th>
                  <th style="background-color: #003366; color:white;">NOVIEMBRE</th>
                  <th style="background-color: #003366; color:white;">DICIEMBRE</th>
                  <th style="background-color: #003366; color:white;">TOTAL</th>
                </tr>
              </thead>
              <tbody>
                <!-- ************************* PRESUPUESTO OBJETIVO ************************* -->
                <tr>
                  <td>PRESUPUESTO OBJETIVO</td>
                  <td id="PreMes01">$0.00</td>
                  <td id="PreMes02">$0.00</td>
                  <td id="PreMes03">$0.00</td>
                  <td id="PreMes04">$0.00</td>
                  <td id="PreMes05">$0.00</td>
                  <td id="PreMes06">$0.00</td>
                  <td id="PreMes07">$0.00</td>
                  <td id="PreMes08">$0.00</td>
                  <td id="PreMes09">$0.00</td>
                  <td id="PreMes10">$0.00</td>
                  <td id="PreMes11">$0.00</td>
                  <td id="PreMes12">$0.00</td>
                  <td id="TotalPreMes">$0.00</td>
                </tr>
                <!-- ************************* /.PRESUPUESTO OBJETIVO ************************* -->
                <!-- ************************* PRESUPUESTO OBJETIVO ************************* -->
                <tr>
                  <td>VENTA REAL</td>
                  <td id="RealMes01" style="cursor: pointer;" class="clickInfo" data-info="01">$0.00</td>
                  <td id="RealMes02" style="cursor: pointer;" class="clickInfo" data-info="02">$0.00</td>
                  <td id="RealMes03" style="cursor: pointer;" class="clickInfo" data-info="03">$0.00</td>
                  <td id="RealMes04" style="cursor: pointer;" class="clickInfo" data-info="04">$0.00</td>
                  <td id="RealMes05" style="cursor: pointer;" class="clickInfo" data-info="05">$0.00</td>
                  <td id="RealMes06" style="cursor: pointer;" class="clickInfo" data-info="06">$0.00</td>
                  <td id="RealMes07" style="cursor: pointer;" class="clickInfo" data-info="07">$0.00</td>
                  <td id="RealMes08" style="cursor: pointer;" class="clickInfo" data-info="08">$0.00</td>
                  <td id="RealMes09" style="cursor: pointer;" class="clickInfo" data-info="09">$0.00</td>
                  <td id="RealMes10" style="cursor: pointer;" class="clickInfo" data-info="10">$0.00</td>
                  <td id="RealMes11" style="cursor: pointer;" class="clickInfo" data-info="11">$0.00</td>
                  <td id="RealMes12" style="cursor: pointer;" class="clickInfo" data-info="12">$0.00</td>
                  <td id="TotalRealMes">$0.00</td>
                </tr>
                <!-- ************************* /.PRESUPUESTO OBJETIVO ************************* -->
              </tbody>
              <tfoot>
                <tr>
                  <th>CUMPLIMIENTO</th>
                  <th id="CumpMes01">$0.00</th>
                  <th id="CumpMes02">$0.00</th>
                  <th id="CumpMes03">$0.00</th>
                  <th id="CumpMes04">$0.00</th>
                  <th id="CumpMes05">$0.00</th>
                  <th id="CumpMes06">$0.00</th>
                  <th id="CumpMes07">$0.00</th>
                  <th id="CumpMes08">$0.00</th>
                  <th id="CumpMes09">$0.00</th>
                  <th id="CumpMes10">$0.00</th>
                  <th id="CumpMes11">$0.00</th>
                  <th id="CumpMes12">$0.00</th>
                  <th id="CumpTotalMes">$0.00</th>
                </tr>
              </tfoot>
            </table>
          </div>

        </div><!--/.box-body-->
      </div>
    </section>
    <!-- ########################### TERMINA SECCION DE LA GRAFICA ########################### -->


    <!-- ########################### MODAL INFO DETALLE FAC ########################### -->
    <div class="modal fade" id="modalInfoFac" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="titleModalInfoFac"></h4>
          </div>
          <div class="modal-body">

            <div class="table-responsive">
              <table id="tablaDetFacturado" class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>ID/CLTE.</th>
                    <th>CLIENTE</th>
                    <th>ID/ALM</th>
                    <th>ALMACEN</th>
                    <th>PLAZA</th>
                    <th>PROMOTOR</th>
                    <th>FACTURADO</th>
                  </tr>
                </thead>
              </table>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- ########################### /.MODAL INFO DETALLE FAC ########################### -->

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
<!-- FORMATO NUMERO CON COMAS EN INPUT -->
<script src="http://afarkas.github.io/webshim/js-webshim/minified/polyfiller.js"></script>
<script>
webshims.setOptions('forms-ext', {
    replaceUI: 'auto',
    types: 'number'
});
webshims.polyfill('forms forms-ext');
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
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
$.ajax({url: '../action/venta_PromorAjax.php',
  beforeSend: function (){ $('#modal_cargando').modal('show'); }
});

function numberWithCommas(number) {
    var parts = number.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
}
/* select change plaza */
$("#selectPre").on("change",function (){
  location.href = "<?=basename($_SERVER['PHP_SELF']).'?'?>"+"pre="+$(this).val()+"&fecha=<?=$fecha?>";
});

$(".select2").select2({ });
/*------------------------ SELECT AÑO PRESUPUESTO ------------------------*/
$.ajax( {
	type : "POST",
	url : "../action/venta_PromorAjax.php",
	cache: false,
	data : { "selectAnio": 1},
	beforeSend: function () {
      var $select = $('#selectAnio');
      $select.append('<option>Procesando...</option>');
  },
  success: function (response) {
  	var dataJson = JSON.parse(response);
  	var $select = $('#selectAnio');
  	$select.empty()
  	$.each(dataJson, function(i, val){
    	$select.append( $("<option select></option>").attr('value', val.ANIO).text( val.ANIO ) );
    	$select.find('option[value="'+<?=$fecha?>+'"]').attr('selected', 'selected');
    });
  }
});
/* select change anio */
$("#selectAnio").on("change",function (){
  location.href = "<?=basename($_SERVER['PHP_SELF']).'?'?>"+"pre=<?=$pre?>&fecha="+$(this).val();
});

/*----------------------- SELECT PROMOTOR PRESUPUESTO -----------------------*/
$.ajax( {
	type : "POST",
	url : "../action/venta_PromorAjax.php",
	cache: false,
	data : { "selectPromotor": 1, 'fecha' : <?= $fecha ?>, 'pre' : <?= $pre ?>},
	beforeSend: function () {
      var $select = $('#selectPromotor');
      $select.append('<option>Procesando...</option>');
  },
  success: function (response) {
  	var dataJson = JSON.parse(response);
  	var $select = $('#selectPromotor');
  	$select.empty();
    $select.append('<option value="ALL">ALL</option>');
  	$.each(dataJson, function(i, val){
      var texto;
      if (val.V_NOMBRE != null){ texto = val.V_NOMBRE; }else{ texto = ""; }
      if (val.V_APELLIDO_PAT != null){ texto = texto+" "+val.V_APELLIDO_PAT; }else{ texto = texto+" "+""; }
      if (val.V_APELLIDO_MAT != null){ texto = texto+" "+val.V_APELLIDO_MAT; }else{ texto = texto+" "+""; }
    	$select.append( $("<option select></option>").attr('value', val.ID_PROMOTOR).text(  texto ) );
    	$select.find('option[value="<?=$promotor?>"]').attr('selected', 'selected');
    });
  }
});
/* select change promotor */
$("#selectPromotor").on("change",function (){
  location.href = "<?=basename($_SERVER['PHP_SELF']).'?'?>"+ "pre=<?=$pre?>&fecha="+<?=$fecha?>+"&promotor="+$(this).val();
});

/*---------------------- SELECT PROMOTOR PRESUPUESTO ----------------------*/
$.ajax( {
  type : "POST",
  url : "../action/venta_PromorAjax.php",
  cache: false,
  data : { "selectPlaza": 1, 'fecha' : <?= $fecha ?>, 'pre' : <?= $pre ?> },
  beforeSend: function () {
    var $select = $('#selectPlaza');
    $select.append('<option>Procesando...</option>');
  },
  success: function (response) {
    var dataJson = JSON.parse(response);
    var $select = $('#selectPlaza');
    $select.empty();
    <?php if ( $promotor == "ALL" && $pre == 1 ){ ?>
    $select.append('<option value="ALL">Seleccione Promotor</option>');
    <?php }else{ ?>
    $select.append('<option value="ALL">ALL</option>');
    $.each(dataJson, function(i, val){
      $select.append( $("<option select></option>").attr('value', val.IID_PLAZA).text( "("+val.IID_PLAZA+") "+val.V_RAZON_SOCIAL ) );
      $select.find('option[value="<?=$plaza?>"]').attr('selected', 'selected');
    });
    <?php } ?>
  }
});
/* select change promotor */
$("#selectPlaza").on("change",function (){
  location.href = "<?=basename($_SERVER['PHP_SELF']).'?'?>"+ "pre=<?=$pre?>&fecha="+<?=$fecha?>+"&promotor=<?=$promotor?>&plaza="+$(this).val();
});

/*----------------------- DATOS TABLA PRESUPUESTO -----------------------*/
tablaInfoPre()
function tablaInfoPre(){
  $.ajax({
    type: 'POST',
    cache: false,
    url : "../action/venta_PromorAjax.php",
    data: { "tablaInfoPre" : 1, "pre" : <?=$pre?>, "fecha" : <?=$fecha?> },
    success: function(response){
      var dataJson = JSON.parse(response);
      /* SUMA TOTAL DE LOS MESES */
      var mes01 = 0; mes02 = 0; mes03 = 0; mes04 = 0; mes05 = 0; mes06 = 0; mes07 = 0; mes08 = 0; mes09 = 0; mes10 = 0; mes11 = 0; mes12 = 0; totalSum = 0;
      $.each(dataJson, function (index, value) {
        mes01 += parseFloat(value.N_VALOR_MES1);
        mes02 += parseFloat(value.N_VALOR_MES2);
        mes03 += parseFloat(value.N_VALOR_MES3);
        mes04 += parseFloat(value.N_VALOR_MES4);
        mes05 += parseFloat(value.N_VALOR_MES5);
        mes06 += parseFloat(value.N_VALOR_MES6);
        mes07 += parseFloat(value.N_VALOR_MES7);
        mes08 += parseFloat(value.N_VALOR_MES8);
        mes09 += parseFloat(value.N_VALOR_MES9);
        mes10 += parseFloat(value.N_VALOR_MES10);
        mes11 += parseFloat(value.N_VALOR_MES11);
        mes12 += parseFloat(value.N_VALOR_MES12);
      });
      totalSum = mes01+mes02+mes03+mes04+mes05+mes06+mes07+mes08+mes09+mes10+mes11+mes12;
      $("#totalMes01").html( "$"+numberWithCommas(mes01.toFixed(2)) );
      $("#totalMes02").html( "$"+numberWithCommas(mes02.toFixed(2)) );
      $("#totalMes03").html( "$"+numberWithCommas(mes03.toFixed(2)) );
      $("#totalMes04").html( "$"+numberWithCommas(mes04.toFixed(2)) );
      $("#totalMes05").html( "$"+numberWithCommas(mes05.toFixed(2)) );
      $("#totalMes06").html( "$"+numberWithCommas(mes06.toFixed(2)) );
      $("#totalMes07").html( "$"+numberWithCommas(mes07.toFixed(2)) );
      $("#totalMes08").html( "$"+numberWithCommas(mes08.toFixed(2)) );
      $("#totalMes09").html( "$"+numberWithCommas(mes09.toFixed(2)) );
      $("#totalMes10").html( "$"+numberWithCommas(mes10.toFixed(2)) );
      $("#totalMes11").html( "$"+numberWithCommas(mes11.toFixed(2)) );
      $("#totalMes12").html( "$"+numberWithCommas(mes12.toFixed(2)) );
      $("#totalSum").html( "$"+numberWithCommas(totalSum.toFixed(2)) );
      /* /.SUMA TOTAL DE LOS MESES */
      $('#tablaInfoPre').dataTable( {
        stateSave: true,
        "ordering": true,
        "bDestroy": true,
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
              title: 'Venta vs Presupuesto Promotores',
            },
            {
              extend: 'print',
              text: '<i class="fa fa-print"></i>',
              titleAttr: 'Imprimir',
              exportOptions: {//muestra/oculta visivilidad de columna
                  columns: ':visible',
              },
              title: '<h5>Venta vs Presupuesto Promotores</h5>',
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
        data : dataJson,
        "createdRow": function( row, data, dataIndex ) {
        <?php if($pre == 2){ ?>
          $(row).attr('data-info', data.IID_PLAZA);
          if ( data.IID_PLAZA == "<?= $plaza ?>" ){
            $(row).css("background-color", "#C8E5EE");
          }
        <?php }else{ ?>
          $(row).attr('data-info', data.ID_PROMOTOR);
          if ( data.ID_PROMOTOR == "<?= $promotor ?>" ){
            $(row).css("background-color", "#C8E5EE");
          }
        <?php } ?>
        },


        columns: [
          {"data": null, render: function ( data, type, row ) {
            var valor;
            <?php if($pre == 2){ ?>
              valor = "("+data.IID_PLAZA+")"+data.V_NOMBRE;
            <?php }else{ ?>
              valor = "("+data.ID_PROMOTOR+")"+data.V_NOMBRE;
            <?php } ?>
          return valor }
          },
          {"data": "N_VALOR_MES1", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES2", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES3", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES4", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES5", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES6", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES7", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES8", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES9", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES10", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES11", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": "N_VALOR_MES12", render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
          {"data": null, render: function ( data, type, row ) {
          var suma = parseFloat(data.N_VALOR_MES1)+parseFloat(data.N_VALOR_MES2)+parseFloat(data.N_VALOR_MES3)+parseFloat(data.N_VALOR_MES4)+parseFloat(data.N_VALOR_MES5)+parseFloat(data.N_VALOR_MES6)+parseFloat(data.N_VALOR_MES7)+parseFloat(data.N_VALOR_MES8)+parseFloat(data.N_VALOR_MES9)+parseFloat(data.N_VALOR_MES10)+parseFloat(data.N_VALOR_MES11)+parseFloat(data.N_VALOR_MES12);
          suma = $.fn.dataTable.render.number(',', '.', 2, '$').display(suma);
          return "<b>"+suma+"</b>"; }
          },
        ],
      }
      );

    }
  });
}
/* click tr tabla info presupuesto */
$('#tablaInfoPre').on('click', 'tbody tr, tfoot tr', function() {
  var url;
  <?php if($pre == 2){ ?>
    url = "pre=<?=$pre?>&fecha=<?=$fecha?>&promotor=<?=$promotor?>&plaza="+$(this).data("info");
  <?php }else{ ?>
    url = "pre=<?=$pre?>&fecha=<?=$fecha?>&promotor="+$(this).data("info")+"&plaza=<?=$plaza?>";
  <?php } ?>
  location.href = "<?=basename($_SERVER['PHP_SELF']).'?'?>"+url;
})
</script>

<script type="text/javascript">
/* AJAX SELECT WHERE PRESUPUESTO */
wherePresupuesto();
function wherePresupuesto(){
  $.ajax({
    type : "post",
    url: "../action/venta_PromorAjax.php",
    cache : false,
    data : { "wherePresupuesto": 1, "v_pre" : <?=$pre?>, "v_fecha": <?=$fecha?>, "v_promotor" : "<?=$promotor?>", "v_plaza" : "<?=$plaza?>" },
    success: function (response) {
      var dataJson = JSON.parse(response);
      var TotalWherePre = 0;
      $.each(dataJson, function( index, value ) {
        $("#PreMes01").html("$"+numberWithCommas((+value.N_VALOR_MES1).toFixed(2)));$("#PreMes02").html("$"+numberWithCommas((+value.N_VALOR_MES2).toFixed(2)));
        $("#PreMes03").html("$"+numberWithCommas((+value.N_VALOR_MES3).toFixed(2)));$("#PreMes04").html("$"+numberWithCommas((+value.N_VALOR_MES4).toFixed(2)));
        $("#PreMes05").html("$"+numberWithCommas((+value.N_VALOR_MES5).toFixed(2)));$("#PreMes06").html("$"+numberWithCommas((+value.N_VALOR_MES6).toFixed(2)));
        $("#PreMes07").html("$"+numberWithCommas((+value.N_VALOR_MES7).toFixed(2)));$("#PreMes08").html("$"+numberWithCommas((+value.N_VALOR_MES8).toFixed(2)));
        $("#PreMes09").html("$"+numberWithCommas((+value.N_VALOR_MES9).toFixed(2)) );$("#PreMes10").html("$"+numberWithCommas((+value.N_VALOR_MES10).toFixed(2)));
        $("#PreMes11").html("$"+numberWithCommas((+value.N_VALOR_MES11).toFixed(2)));$("#PreMes12").html("$"+numberWithCommas((+value.N_VALOR_MES12).toFixed(2)));
        TotalWherePre += parseFloat(value.N_VALOR_MES1)+parseFloat(value.N_VALOR_MES2)+parseFloat(value.N_VALOR_MES3)+parseFloat(value.N_VALOR_MES4)+parseFloat(value.N_VALOR_MES5)+parseFloat(value.N_VALOR_MES6)+parseFloat(value.N_VALOR_MES7)+parseFloat(value.N_VALOR_MES8)+parseFloat(value.N_VALOR_MES9)+parseFloat(value.N_VALOR_MES10)+parseFloat(value.N_VALOR_MES11)+parseFloat(value.N_VALOR_MES12);
      });
      $("#TotalPreMes").html("<b>$"+numberWithCommas(TotalWherePre.toFixed(2))+"</b>" );
    }
  });
}

/* AJAX VENTA REAL */
ventaReal();
function ventaReal(){
  $.ajax({
    type : "post",
    url: "../action/venta_PromorAjax.php",
    cache : false,
    data : { "ventaReal": 1, "v_pre" : <?=$pre?>, "v_fecha": <?=$fecha?>, "v_promotor" : "<?=$promotor?>", "v_plaza" : "<?=$plaza?>" },
    success: function (response) {
      var dataJson = JSON.parse(response);
      $("#RealMes01").html("$"+numberWithCommas((+dataJson[0].FACTURADO_TOTAL).toFixed(2)));$("#RealMes02").html("$"+numberWithCommas((+dataJson[1].FACTURADO_TOTAL).toFixed(2)));
      $("#RealMes03").html("$"+numberWithCommas((+dataJson[2].FACTURADO_TOTAL).toFixed(2)));$("#RealMes04").html("$"+numberWithCommas((+dataJson[3].FACTURADO_TOTAL).toFixed(2)));
      $("#RealMes05").html("$"+numberWithCommas((+dataJson[4].FACTURADO_TOTAL).toFixed(2)));$("#RealMes06").html("$"+numberWithCommas((+dataJson[5].FACTURADO_TOTAL).toFixed(2)));
      $("#RealMes07").html("$"+numberWithCommas((+dataJson[6].FACTURADO_TOTAL).toFixed(2)));$("#RealMes08").html("$"+numberWithCommas((+dataJson[7].FACTURADO_TOTAL).toFixed(2)));
      $("#RealMes09").html("$"+numberWithCommas((+dataJson[8].FACTURADO_TOTAL).toFixed(2)));$("#RealMes10").html("$"+numberWithCommas((+dataJson[9].FACTURADO_TOTAL).toFixed(2)));
      $("#RealMes11").html("$"+numberWithCommas((+dataJson[10].FACTURADO_TOTAL).toFixed(2)));$("#RealMes12").html("$"+numberWithCommas((+dataJson[11].FACTURADO_TOTAL).toFixed(2)));
      var TotalRealMes = parseFloat(dataJson[0].FACTURADO_TOTAL)+parseFloat(dataJson[1].FACTURADO_TOTAL)+parseFloat(dataJson[2].FACTURADO_TOTAL)+parseFloat(dataJson[3].FACTURADO_TOTAL)+parseFloat(dataJson[4].FACTURADO_TOTAL)+parseFloat(dataJson[5].FACTURADO_TOTAL)+parseFloat(dataJson[6].FACTURADO_TOTAL)+parseFloat(dataJson[7].FACTURADO_TOTAL)+parseFloat(dataJson[8].FACTURADO_TOTAL)+parseFloat(dataJson[9].FACTURADO_TOTAL)+parseFloat(dataJson[10].FACTURADO_TOTAL)+parseFloat(dataJson[11].FACTURADO_TOTAL);
      $("#TotalRealMes").html("<b>$"+numberWithCommas(TotalRealMes.toFixed(2))+"</b>" );

      /* CUMPLIMIENTO EN PORCENTAJE*/
      if ($('#PreMes01').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes01").html("0.00" + "%");
      }else {
          $("#CumpMes01").html( (($('#RealMes01').html().replace(/\$|\,/g, '') / $('#PreMes01').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes02').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes02").html("0.00" + "%");
      }else {
          $("#CumpMes02").html( (($('#RealMes02').html().replace(/\$|\,/g, '') / $('#PreMes02').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes03').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes03").html("0.00" + "%");
      }else {
          $("#CumpMes03").html( (($('#RealMes03').html().replace(/\$|\,/g, '') / $('#PreMes03').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes04').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes04").html("0.00" + "%");
      }else {
          $("#CumpMes04").html( (($('#RealMes04').html().replace(/\$|\,/g, '') / $('#PreMes04').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes05').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes05").html("0.00" + "%");
      }else {
          $("#CumpMes05").html( (($('#RealMes05').html().replace(/\$|\,/g, '') / $('#PreMes05').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes06').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes06").html("0.00" + "%");
      }else {
          $("#CumpMes06").html( (($('#RealMes06').html().replace(/\$|\,/g, '') / $('#PreMes06').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes07').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes07").html("0.00" + "%");
      }else {
          $("#CumpMes07").html( (($('#RealMes07').html().replace(/\$|\,/g, '') / $('#PreMes07').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes08').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes08").html("0.00" + "%");
      }else {
          $("#CumpMes08").html( (($('#RealMes08').html().replace(/\$|\,/g, '') / $('#PreMes08').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes09').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes09").html("0.00" + "%");
      }else {
          $("#CumpMes09").html( (($('#RealMes09').html().replace(/\$|\,/g, '') / $('#PreMes09').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes10').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes10").html("0.00" + "%");
      }else {
          $("#CumpMes10").html( (($('#RealMes10').html().replace(/\$|\,/g, '') / $('#PreMes10').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes11').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes11").html("0.00" + "%");
      }else {
          $("#CumpMes11").html( (($('#RealMes11').html().replace(/\$|\,/g, '') / $('#PreMes11').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      if ($('#PreMes12').html().replace(/\$|\,/g, '') == 0.00) {
        $("#CumpMes12").html("0.00" + "%");
      }else {
          $("#CumpMes12").html( (($('#RealMes12').html().replace(/\$|\,/g, '') / $('#PreMes12').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      }

      //$("#CumpMes01").html( (($('#RealMes01').html().replace(/\$|\,/g, '') / $('#PreMes01').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes02").html( (($('#RealMes02').html().replace(/\$|\,/g, '') / $('#PreMes02').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes03").html( (($('#RealMes03').html().replace(/\$|\,/g, '') / $('#PreMes03').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes04").html( (($('#RealMes04').html().replace(/\$|\,/g, '') / $('#PreMes04').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes05").html( (($('#RealMes05').html().replace(/\$|\,/g, '') / $('#PreMes05').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes06").html( (($('#RealMes06').html().replace(/\$|\,/g, '') / $('#PreMes06').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes07").html( (($('#RealMes07').html().replace(/\$|\,/g, '') / $('#PreMes07').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes08").html( (($('#RealMes08').html().replace(/\$|\,/g, '') / $('#PreMes08').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes09").html( (($('#RealMes09').html().replace(/\$|\,/g, '') / $('#PreMes09').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes10").html( (($('#RealMes10').html().replace(/\$|\,/g, '') / $('#PreMes10').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes11").html( (($('#RealMes11').html().replace(/\$|\,/g, '') / $('#PreMes11').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      //$("#CumpMes12").html( (($('#RealMes12').html().replace(/\$|\,/g, '') / $('#PreMes12').html().replace(/\$|\,/g, ''))*100).toFixed(0)+"%" );
      $("#CumpTotalMes").html( (($('#TotalRealMes').text().replace(/\$|\,/g, '') / $('#TotalPreMes').text().replace(/\$|\,/g, ''))*100).toFixed(0) +"%" );

    }
  });


}

$('#tablaResVsPre').DataTable({
  stateSave: true,
    "ordering": true,
    "bDestroy": true,
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "language": {
      "url": "../plugins/datatables/Spanish.json"
    },
    //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
      buttons:[
        {
          extend: 'excelHtml5',
          text: '<i class="fa fa-file-excel-o"></i>',
          titleAttr: 'Excel',
          exportOptions: {//muestra/oculta visivilidad de columna
              columns: ':visible'
          },
          title: 'Venta vs Presupuesto Promotores',
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i>',
          titleAttr: 'Imprimir',
          exportOptions: {//muestra/oculta visivilidad de columna
              columns: ':visible',
          },
          title: '<h5>Venta vs Presupuesto Promotores</h5>',
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

/*------------ CLICK TFOOT DETALLE DE FACTURACION ------------*/
$('.clickInfo').on('click', function() {
  var tituloModal = '';
  switch( $(this).data("info") ){
    case "01": tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES ENERO <?=$fecha?>"; break;
    case "02": tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES FEBRERO <?=$fecha?>"; break;
    case "03": tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES MARZO <?=$fecha?>"; break;
    case "04": tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES ABRIL <?=$fecha?>"; break;
    case "05": tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES MAYO <?=$fecha?>"; break;
    case "06": tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES JUNIO <?=$fecha?>"; break;
    case "07": tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES JULIO <?=$fecha?>"; break;
    case "08": tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES AGOSTO <?=$fecha?>"; break;
    case "09": tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES SEPTIEMBRE <?=$fecha?>"; break;
    case 10: tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES OCTUBRE <?=$fecha?>"; break;
    case 11: tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES NOVIEMBRE <?=$fecha?>"; break;
    case 12: tituloModal = "<i class='fa fa-list-alt'></i> DETALLE DE FACTURACIÓN MES DICIEMBRE <?=$fecha?>"; break;
  }

  $('#modalInfoFac').modal('toggle');
   $.ajax({
    type: 'POST',
    url: '../action/venta_PromorAjax.php',
    cache:false,
    data: { "modalDetFac" : 1, "pre" : <?=$pre?>, "fecha" : <?=$fecha?>, "v_promotor" : '<?=$promotor?>', "v_plaza" : '<?=$plaza?>', "mes" : $(this).data("info") },
    beforeSend: function (){
      $("#titleModalInfoFac").html('<i class="fa fa-spin fa-refresh"></i> Procesando...')
    },
    success: function (response) {
      $("#titleModalInfoFac").html(tituloModal)
      var dataJson = JSON.parse(response);
      $('#tablaDetFacturado').dataTable( {
        destroy: true,
        stateSave: true,
        "scrollY": 320,
        "bDestroy": true,
        "ordering": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "language": { "url": "../plugins/datatables/Spanish.json" },
        //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
        dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [
        { extend: 'excelHtml5',
          text: '<i class="fa fa-file-excel-o"></i>',
          titleAttr: 'Excel',
          exportOptions: { columns: ':visible' },
          title: 'Venta vs Presupuesto Promotores', },
        { extend: 'print',
          text: '<i class="fa fa-print"></i>',
          titleAttr: 'Imprimir',
          exportOptions: { columns: ':visible', },
          title: '<h5>Venta vs Presupuesto Promotores</h5>', },
        { extend: 'colvis',
          collectionLayout: 'fixed two-column',
          text: '<i class="fa fa-eye-slash"></i>',
          titleAttr: '(Mostrar/ocultar) Columnas',
          autoClose: true, }
        ],
        //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
        data : dataJson,
        columns: [
          {"data" : "IID_NUM_CLIENTE" },
          {"data" : "CLIENTE"},
          {"data" : "IID_ALMACEN"},
          {"data" : "ALMACEN"},
          {"data" : "PLAZA"},
          { "data": null, render: function ( data, type, row ) {
            return '('+data.IID_PROMOTOR+')'+data.V_NOMBRE+' '+data.V_APELLIDO_PAT+' '+data.V_APELLIDO_MAT;
          } },
          {"data": null, render: function ( data, type, row ) {
          var total = data.FACTURADO_TOTAL; total = $.fn.dataTable.render.number(',', '.', 2, '$').display(total);
          return "<b>"+total+"</b>"; } },
        ],
      });
    }
  });

})
</script>
<script type="text/javascript">

  graficaPreVsVta()
  function graficaPreVsVta (meses) {


    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });

    <?php $ventaReal = $insObj_VentaPromotor->ventaReal($pre,$fecha,$promotor,$plaza) ?>
    <?php $wherePresupuesto = $insObj_VentaPromotor->wherePresupuesto($pre,$fecha,$promotor,$plaza) ?>

    //var categories = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    var categories = [
    <?php for ($i=0; $i <count($ventaReal) ; $i++) {
      echo "'".$ventaReal[$i]["NOM_MES"]."'".",";
    } ?>
    ];
    /*TERMINA GRAFICA PRESUPUESTO SELECIONADO*/
    var dataDef = [
    <?php for ($i=0; $i <count($wherePresupuesto) ; $i++) {
      echo $wherePresupuesto[$i]["N_VALOR_MES1"].","
      .$wherePresupuesto[$i]["N_VALOR_MES2"].","
      .$wherePresupuesto[$i]["N_VALOR_MES3"].","
      .$wherePresupuesto[$i]["N_VALOR_MES4"].","
      .$wherePresupuesto[$i]["N_VALOR_MES5"].","
      .$wherePresupuesto[$i]["N_VALOR_MES6"].","
      .$wherePresupuesto[$i]["N_VALOR_MES7"].","
      .$wherePresupuesto[$i]["N_VALOR_MES8"].","
      .$wherePresupuesto[$i]["N_VALOR_MES9"].","
      .$wherePresupuesto[$i]["N_VALOR_MES10"].","
      .$wherePresupuesto[$i]["N_VALOR_MES11"].","
      .$wherePresupuesto[$i]["N_VALOR_MES12"].",";
    } ?>
    ];

    <?php
    for ($a=0; $a <4 ; $a++) {
      $var = array();
      $fecha_new = $fecha - (3-$a);
      $ventaRealAnio = $insObj_VentaPromotor->ventaReal($pre,$fecha_new,$promotor,$plaza);
      for ($i=0; $i < count($ventaRealAnio) ; $i++) {
        $var[] = $ventaRealAnio[$i]["FACTURADO_TOTAL"];
      }
      $comas = implode(",", $var);
    ?>
    var data<?=$a?> = [<?=$comas?>];
    <?php
    }
    ?>



    $('#graficaPreVsVta').highcharts({
        chart: {
          //type: 'spline'
          type: 'line'
        },
         title: {
          text: '<?php if( $pre == 2 ){ echo "PRESUPUESTO PLAZA $fecha VS RESULTADO PLAZA $fecha"; }else{echo "PRESUPUESTO PROMOTOR $fecha VS RESULTADO PROMOTOR $fecha";} ?>'
        },
        subtitle: {
          text: '*Clic en el mes para mostrar el historial del mes*'
        },
        lang: {
          printChart: 'Imprimir Grafica',
          downloadPNG: 'Descargar PNG',
          downloadJPEG: 'Descargar JPEG',
          downloadPDF: 'Descargar PDF',
          downloadSVG: 'Descargar SVG',
          contextButtonTitle: 'Exportar grafica'
        },
        legend: {
          //layout: 'vertical',
          //align: 'left',
          //verticalAlign: 'top',
          //x: 150,
          //y: 100,
          //floating: true,
          borderWidth: 1,
          backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },

        yAxis: {
          lineWidth: 2,
          //min: 0,
          offset: 10,
          tickWidth: 1,
          title: { text: 'Monto' },
          labels: {
          formatter: function () { return this.value; }
          }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' MXN',
          useHTML: true,
          valueDecimals: 2,
          valuePrefix: '$',
        },
        credits: {
          enabled: false
        },
        colors: ['#E51C23', '#003366', '#00BCD4', '#E31D60', '#3D4EAF', '#3F50B4'],
        plotOptions: {
          areaspline: {
            fillOpacity: 0.4
          },
          series: {
            cursor: 'pointer',
            point: {
              events: {
                click: function () {
                  $('#modal_cargando').modal('show');
                  url = '?pre=<?=$pre?>&fecha=<?=$fecha?>&promotor=<?=$promotor?>&plaza=<?=$plaza?>&mes='+this.category+'';
                  location.href = "<?=basename($_SERVER['PHP_SELF'])?>"+url;
                }
              }
            }
          }
        },
        xAxis: {
          tickmarkPlacement: 'on',
          gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?pre=<?=$pre?>&fecha=<?=$fecha?>&promotor=<?=$promotor?>&plaza=<?=$plaza?>&mes='+this.value+'';
              return '<a class="btnLabel" href="'+url+'">' + this.value + '</a>';
            }
          }
        },
        series:  [{
            name: 'PRESUPUESTO <?=$fecha?>',
            data: dataDef,
            marker: {
                fillColor: '#3C8DBC',
                //lineWidth: 2,
                //lineColor: null // inherit from series
            }
        },

        <?php for ($a=0; $a < 4 ; $a++) { $fecha_new = $fecha - (3-$a); ?>

        {
            name: 'RESULTADO <?=$fecha_new?>',
            data: data<?=$a?>,
            dashStyle: 'Dash',
            marker: {
            symbol: 'url(../dist/img/markerX.png)',
            width: 16,
            height: 16
            },
        },

        <?php } ?>


        // {
        //     name: 'RESULTADO <?=$fecha?>',
        //     data: data0,
        //     dashStyle: 'Dash',
        //     marker: {
        //     symbol: 'url(../dist/img/markerX.png)',
        //     width: 16,
        //     height: 16
        //     },
        // }


        ]

    });
}

$(".btnLabel").click(function() { $('#modal_cargando').modal('show'); });
</script>
<?php if( $mes != "ALL" ){ ?>
<script type="text/javascript">
graficaConsol();
function graficaConsol(){// function
<?php
  $anioConsol = array();
  $facConsol = array();
  for ($a=0; $a <5 ; $a++) {
    $fecha_new = $fecha - (4-$a);
    $histMesConsol = $insObj_VentaPromotor->histMesConsol($pre,$fecha_new,$promotor,$plaza,$mes,0);
    for ($i=0; $i <count($histMesConsol) ; $i++) {
      $anioConsol[] = "'".$histMesConsol[$i]["ANIO"]."'";
      $facConsol[] = $histMesConsol[$i]["FACTURADO_TOTAL"];
    }
  }
  $anioConsol = implode(",", $anioConsol);
  $facConsol = implode(",", $facConsol);
?>

Highcharts.chart('histMesConsol', {
  chart: {
    type: 'column'
  },
  title: {
    text: 'Facturación Consolidada'
  },
  subtitle: {
    text: '*Clic en la columna para ver el detalle*'
  },
  credits: {
    enabled: false
  },
  xAxis: {
    categories: [<?= $anioConsol ?>],
    crosshair: true,
  },
  yAxis: {
    lineWidth: 2,
    //min: 0,
    offset: 10,
    tickWidth: 1,
    title: {
      text: 'Monto'
    },
    labels:{
      formatter: function () { return this.value; }
    }
  },
  tooltip: {
    headerFormat: '<span style="font-size:12px">{series.name}-{point.key}</span><br>',
    pointFormat: '<b>$ {point.y:,.2f} MXN</b>',
    shared: true,
    useHTML: true
  },
  plotOptions: {
    series: {
      /*dataLabels: {
        enabled: false,
        crop: false,
        overflow: 'none',
        format: '${point.y:,.2f} MXN'
      },*/
      cursor: 'pointer',
      point: {
        events: {
          click: function () {
          /* detalle facturado */
            var anio = this.category;
            $('#modalInfoFac').modal('toggle');
            $.ajax({
              type: 'POST',
              url: '../action/venta_PromorAjax.php',
              cache:false,
              data: { "DetFacConsol" : 1, "pre" : <?=$pre?>, "fecha" : anio, "v_promotor" : '<?=$promotor?>', "v_plaza" : '<?=$plaza?>', "mes" : "<?=$mes?>", "v_det" : 1 },
              beforeSend: function (){
                $("#titleModalInfoFac").html('<i class="fa fa-spin fa-refresh"></i> Procesando...')
                var table = $('#tablaDetFacturado').DataTable();
                table.clear().draw();
              },
              success: function (response) {
                $("#titleModalInfoFac").html('<i class="fa fa-list-alt"></i> DETALLE DE FACTURACIÓN CONSOLIDADA <?= strtoupper($mes) ?>-'+anio)
                var dataJson = JSON.parse(response);
                $('#tablaDetFacturado').dataTable( {
                  destroy: true,
                  stateSave: true,
                  "scrollY": 320,
                  "bDestroy": true,
                  "ordering": true,
                  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                  "language": { "url": "../plugins/datatables/Spanish.json" },
                  //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
                  dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
                  buttons: [
                  { extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    exportOptions: { columns: ':visible' },
                    title: 'Venta vs Presupuesto Promotores', },
                  { extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Imprimir',
                    exportOptions: { columns: ':visible', },
                    title: '<h5>Venta vs Presupuesto Promotores</h5>', },
                  { extend: 'colvis',
                    collectionLayout: 'fixed two-column',
                    text: '<i class="fa fa-eye-slash"></i>',
                    titleAttr: '(Mostrar/ocultar) Columnas',
                    autoClose: true, }
                  ],
                  //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
                  data : dataJson,
                  columns: [
                    {"data" : "IID_NUM_CLIENTE" },
                    {"data" : "CLIENTE"},
                    {"data" : "IID_ALMACEN"},
                    {"data" : "ALMACEN"},
                    {"data" : "PLAZA"},
                    { "data": null, render: function ( data, type, row ) {
                      return '('+data.IID_PROMOTOR+')'+data.V_NOMBRE+' '+data.V_APELLIDO_PAT+' '+data.V_APELLIDO_MAT;
                    } },
                    {"data": null, render: function ( data, type, row ) {
                    var total = data.FACTURADO_TOTAL; total = $.fn.dataTable.render.number(',', '.', 2, '$').display(total);
                    return "<b>"+total+"</b>"; } },
                  ],
                });
              }
            });
          /* /.detalle facturado */
          }
        }
      }
    }
  },
  colors: ['#464F88'],
  series: [{
    name: '<?= strtoupper($mes) ?>',
    data: [ <?= $facConsol ?> ]
  }]
});

}// ./function

/*----------------------------- ACOMULADO CLIENTES NUEVOS -----------------------------*/
graficaClieNew();
function graficaClieNew(){ //function
<?php
  $anioClieNew = array();
  $facClieNew = array();
  for ($a=0; $a <5 ; $a++) {
    $fecha_new = $fecha - (4-$a);
    $histMesClieNew = $insObj_VentaPromotor->histMesClieNew($pre,$fecha_new,$promotor,$plaza,$mes,0);
    for ($i=0; $i <count($histMesClieNew) ; $i++) {
      $anioClieNew[] = "'".$histMesClieNew[$i]["ANIO"]."'";
      $facClieNew[] = $histMesClieNew[$i]["FACTURADO_TOTAL"];
    }
  }
  $anioClieNew = implode(",", $anioClieNew);
  $facClieNew = implode(",", $facClieNew);
?>

Highcharts.chart('histMesNewCli', {
  chart: {
    type: 'column'
  },
  title: {
    text: 'Acumulado Clientes Nuevos'
  },
  subtitle: {
    text: '*Clic en la columna para ver el detalle*'
  },
  credits: {
    enabled: false
  },
  xAxis: {
    categories: [<?= $anioClieNew ?>],
    crosshair: true
  },
  yAxis: {
    lineWidth: 2,
    //min: 0,
    offset: 10,
    tickWidth: 1,
    title: {
      text: 'Monto'
    },
    labels:{
      formatter: function () { return this.value; }
    }
  },
  tooltip: {
    headerFormat: '<span style="font-size:12px">{series.name}-{point.key}</span><br>',
    pointFormat: '<b>$ {point.y:,.2f} MXN</b>',
    shared: true,
    useHTML: true
  },
  plotOptions: {
    series: {
      /*dataLabels: {
        enabled: true,
        crop: false,
        overflow: 'none',
        format: '${point.y:,.2f} MXN'
      },*/
      cursor: 'pointer',
      point: {
        events: {
          click: function () {
          /* detalle facturado */
            var anio = this.category;
            $('#modalInfoFac').modal('toggle');
            $.ajax({
              type: 'POST',
              url: '../action/venta_PromorAjax.php',
              cache:false,
              data: { "DetFacCliNew" : 1, "pre" : <?=$pre?>, "fecha" : anio, "v_promotor" : '<?=$promotor?>', "v_plaza" : '<?=$plaza?>', "mes" : "<?=$mes?>", "v_det" : 1 },
              beforeSend: function (){
                $("#titleModalInfoFac").html('<i class="fa fa-spin fa-refresh"></i> Procesando...')
                var table = $('#tablaDetFacturado').DataTable();
                table.clear().draw();
              },
              success: function (response) {
                $("#titleModalInfoFac").html('<i class="fa fa-list-alt"></i> DETALLE DE ACOMULADO CLIENTES NUEVOS <?= strtoupper($mes) ?>-'+anio)
                var dataJson = JSON.parse(response);
                $('#tablaDetFacturado').dataTable( {
                  destroy: true,
                  stateSave: true,
                  "bProcessing": true,
                  "scrollY": 320,
                  "bDestroy": true,
                  "ordering": true,
                  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                  "language": { "url": "../plugins/datatables/Spanish.json" },
                  //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
                  dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
                  buttons: [
                  { extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    exportOptions: { columns: ':visible' },
                    title: 'Venta vs Presupuesto Promotores', },
                  { extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Imprimir',
                    exportOptions: { columns: ':visible', },
                    title: '<h5>Venta vs Presupuesto Promotores</h5>', },
                  { extend: 'colvis',
                    collectionLayout: 'fixed two-column',
                    text: '<i class="fa fa-eye-slash"></i>',
                    titleAttr: '(Mostrar/ocultar) Columnas',
                    autoClose: true, }
                  ],
                  //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
                  data : dataJson,
                  columns: [
                    {"data" : "IID_NUM_CLIENTE" },
                    {"data" : "CLIENTE"},
                    {"data" : "IID_ALMACEN"},
                    {"data" : "ALMACEN"},
                    {"data" : "PLAZA"},
                    { "data": null, render: function ( data, type, row ) {
                      return '('+data.IID_PROMOTOR+')'+data.V_NOMBRE+' '+data.V_APELLIDO_PAT+' '+data.V_APELLIDO_MAT;
                    } },
                    {"data": null, render: function ( data, type, row ) {
                    var total = data.FACTURADO_TOTAL; total = $.fn.dataTable.render.number(',', '.', 2, '$').display(total);
                    return "<b>"+total+"</b>"; } },
                  ],
                });
              }
            });
          /* /.detalle facturado */
          }
        }
      }
    }
  },
  colors: ['#1AB394'],
  series: [{
    name: '<?= strtoupper($mes) ?>',
    data: [ <?= $facClieNew ?> ]
  }]
});

} // /.function
</script>
<?php } ?>
<!-- PACE -->
<script src="../plugins/pace/pace.min.js"></script>
<!-- page script -->
<script type="text/javascript">
  // To make Pace works on Ajax calls
  $(document).ajaxStart(function() { Pace.restart(); });
</script>
<script type="text/javascript">
setInterval( function (){
  tablaInfoPre(); wherePresupuesto(); ventaReal(); graficaPreVsVta(); graficaConsol(); graficaClieNew();
},120000);
</script>
<script type="text/javascript">
function numberWithCommas(number) {
    var parts = number.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
}

$.ajax({url: '../action/venta_PromorAjax.php',
  success: function(result){ $('#modal_cargando').modal('hide'); }
});
</script>
</html>
<?php conexion::cerrar($conn); ?>
