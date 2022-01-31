<?php
//BY DAS 12/12/2019

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  //header("location:detalles_ingresos.php");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 40);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/Tabla_Ocupacion.php';
$modelNomina = new NominaPagada();
//SQL ULTIMA FECHA DE CORTE
$fec_corte = $modelNomina->sql(6,null, null);
/*----- GET FECHA -----*/
$fecha = $fec_corte[0]["MES1"];
if( isset($_GET["fecha"]) ){
    $fecha = $_GET["fecha"];
}

$tabla_toneladas = $modelNomina->tabla_toneladas($_SESSION['nomPlaza'], $fecha);
//$selectAlmacen = $modelNomina->almacenSql($plaza);
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- Select2 -->
<link rel="stylesheet" href="../plugins/select2/select2.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">}


<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
  <section class="content-header">
    <h1>Dashboard<small> CAPACIDAD DE ALMACEN</small></h1>
  </section>
  <section>
    <div class="row"><!-- row -->

    <div class="col-md-9"><!-- col-md-9 -->
    <div class="box box-primary">

        <div class="row">

          <div class="col-md-12">
            <?php /*if ($_SESSION['usuario'] == 'diego13' || $_SESSION['usuario'] == 'david' || $_SESSION['usuario'] == 'fernando_s' ) { */?>
              <section>
                <div class="box box-success">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-table"></i> CAPACIDAD DE ALMACEN</h3>
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
                            <th class="small" bgcolor="#5858FA"><font color="white">NOMBRE</font></th> <!--nombre -->
                            <th class="small" bgcolor="#5858FA"><font color="white">CAPACIDAD DE ALMACENAJE MAXIMO EN SACO DE 50 KG (TONELADAS)</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">CAPACIDAD DE ALMACENAJE MAXIMO EN SS (TONELADAS)</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">M2 DE BODEGA</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">EXISTENCIA EN SACOS (TONELADAS)</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">EXISTENCIA EN SUPER SACOS (TONELADAS)</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">TONELADAS</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">OCUPACION EN SACOS</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">OCUPACION EN SUPER SACOS</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">SUMA</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">M2 OCUPADOS</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">M2 DISPONIBLES</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">OCUPACION</font></th>
                            <th class="small" bgcolor="#5858FA"><font color="white">ESPACIO LIBRE</font></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php for ($i=0; $i < count($tabla_toneladas) ; $i++) {
                              $toneladas = $tabla_toneladas[$i]["TOTAL_SACOS"]+$tabla_toneladas[$i]["TOTAL_SUPER_SACOS"];
                              if ($tabla_toneladas[$i]["TOTAL_SACOS"] == 0) {
                                $ocupacion_sacos = 0.00;
                              }
                              else {
                                if ($tabla_toneladas[$i]["TOTAL_SACOS"] > 0 AND $tabla_toneladas[$i]["SACOS"] > 0 ) {
                                  $ocupacion_sacos = ($tabla_toneladas[$i]["TOTAL_SACOS"]/$tabla_toneladas[$i]["SACOS"])*100;
                                }else {
                                  $ocupacion_sacos = 0;
                                }

                              }
                              if ( $tabla_toneladas[$i]["TOTAL_SUPER_SACOS"] == 0 ) {
                                $ocupacion_en_supersacos = 0.00;
                              }
                              else {
                                if ( $tabla_toneladas[$i]["TOTAL_SUPER_SACOS"] > 0  AND $tabla_toneladas[$i]["SUPERSACOS"] > 0 ) {
                                  $ocupacion_en_supersacos = ($tabla_toneladas[$i]["TOTAL_SUPER_SACOS"]/$tabla_toneladas[$i]["SUPERSACOS"])*100;
                                }
                                else {
                                  $ocupacion_en_supersacos = 0;
                                }

                              }
                              $suma = $ocupacion_sacos + $ocupacion_en_supersacos;
                              if ($tabla_toneladas[$i]["TOTAL_SACOS"] > 0 AND $tabla_toneladas[$i]["SACOS"] > 0 AND  $ocupacion_en_supersacos > 0 ) {
                                $suma2 = ($tabla_toneladas[$i]["TOTAL_SACOS"]/$tabla_toneladas[$i]["SACOS"]) + $ocupacion_en_supersacos;
                              }
                              else {
                                $suma2 = 0;
                              }
                              if ($tabla_toneladas[$i]["METROS_CUADRADOS"] > 0  AND $toneladas > 0  AND $tabla_toneladas[$i]["SACOS"] > 0.00 AND $tabla_toneladas[$i]["TOTAL_SACOS"] > 0 ) {

                                $m2_ocupados = ($tabla_toneladas[$i]["METROS_CUADRADOS"]* $toneladas)/$tabla_toneladas[$i]["SACOS"];
                              }
                              elseif ($tabla_toneladas[$i]["METROS_CUADRADOS"] > 0  AND $toneladas > 0  AND $tabla_toneladas[$i]["SUPERSACOS"] > 0  AND $tabla_toneladas[$i]["TOTAL_SACOS"] == 0) {
                                $m2_ocupados = ($tabla_toneladas[$i]["METROS_CUADRADOS"]* $toneladas)/$tabla_toneladas[$i]["SUPERSACOS"];
                              }
                              else {
                                $m2_ocupados = 0;
                              }

                              $m2_disponibles = $tabla_toneladas[$i]["METROS_CUADRADOS"]- $m2_ocupados;  ?>
                            <tr>
                              <td class="small"><?= $tabla_toneladas[$i]["V_NOMBRE"] ?></td> <!--nombre -->
                              <td class="small"><?= number_format($tabla_toneladas[$i]["SACOS"], 2) ?></td> <!--capacidad de almacenaje maximo sacos 50kh -->
                              <td class="small"><?= number_format($tabla_toneladas[$i]["SUPERSACOS"], 2) ?></td> <!--super sacos almacenaje -->
                              <td class="small"><?= number_format($tabla_toneladas[$i]["METROS_CUADRADOS"], 2) ?></td> <!--metros cuadrados -->
                              <td class="small"><?= number_format($tabla_toneladas[$i]["TOTAL_SACOS"], 2) ?></td> <!--existencia en sacos -->
                              <td class="small"><?= number_format($tabla_toneladas[$i]["TOTAL_SUPER_SACOS"], 2) ?></td> <!--existencia en super sacos -->
                              <td class="small"><?= number_format($toneladas, 2)?></td> <!-- toneladsa -->
                              <td class="small"><?= number_format($ocupacion_sacos, 2)?>%</td>  <!--ocupacion en   sacos -->
                              <td class="small"><?= number_format($ocupacion_en_supersacos, 2)?>%</td>  <!--ocupacion en   SUPER sacos -->
                              <td class="small"><?= number_format($suma, 2)?>%</td>  <!-- SUPER SUMA -->
                              <td class="small"><?= number_format($m2_ocupados, 2)?></td> <!--M2 OCUPADOS -->
                              <td class="small"><?= number_format($tabla_toneladas[$i]["METROS_CUADRADOS"]-$m2_ocupados, 2)?></td> <!--M2 disponibles -->
                              <td class="small"><?= number_format($suma, 2)?>%</td> <!-- OCUPACION -->
                              <td class="small"><?= number_format((100-$suma), 2)?>%</td> <!-- ESPACIO LIBRE -->
                            </tr>
                          <?php }  ?>
                        </tbody>
                        <tfoot style="background-color: #000000; color:#ffffff ">

                       </tfoot>
                      </table>
                    </div>

                  </div><!--/.box-body-->
                </div>
              </section>
            <?php /*}*/ ?>

          </div>
          <!--GRAFICA NOMINA POR MES DIEGO ALTAMIRANO SUAREZ-->
        </div>
    </div>
    </div><!-- /.col-md-9 -->


    <div class="col-md-3"><!-- col-md-9 -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
        <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
        <a href="tabla_ocupacion.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
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
        <div class="input-group">
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>

      </div><!--/.box-body-->
    </div>

    </div><!-- /.col-md-3 -->

    </div><!-- /.row -->
  </section>
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
<?php
/* ------------------- INICIA OPCIONES PARA LA GRÁFICA DE DONA ------------------- */
$donut_series = "pie3d: {
                  stroke: { /*define linea separadora*/
                    width: 0,
                    /*color: '#222D32'*/
                  } ,
                  show: true,
                  radius: .80, /*radius: 1,  tamño radio del circulo*/
                  tilt: .9,/*rotacion de angulo */
                  depth: 20,/*grosor de sombra 3d*/
                  innerRadius: 60,/*radio dona o pastel*/
                  label: {
                    show: false,
                    radius:2/3,/*0.90 posicion del label con data*/
                    formatter: labelFormatter,
                  },
                }";

$donut_grid =  "hoverable: true,
                clickable: true,
                verticalLines: false,
                horizontalLines: false,";
$donut_legend = "/*labelBoxBorderColor: 'none'*/
                show: true "; //-- PONE LOS LABEL DEL ALDO IZQUIERDO //

$donut_content = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> Toneladas = %y.0 </b>  </div>' ;

$donut_tooltip = "show: false,
      content: '".$donut_content."',
      defaultTheme: true ";
 ?>

 <script type="text/javascript">
 function cambiar(){
     var pdrs = document.getElementById('file').files[0].name;
     document.getElementById('info').innerHTML = pdrs;
     document.getElementById('submit').disabled = false;
 }
 </script>


<script type="text/javascript">
$('.select2').select2()

</script>
<script type="text/javascript">


/*---- CLICK BOTON FILTRAR ----*/
$(".btnNomFiltro").on("click", function(){
  fecha = $('input[name="nomFecha"]').val();


  if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?fecha='+fecha;
  }
  else {
    fil_habilitado = 'off';
    url = '?fecha='+fecha;
  }
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
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>


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

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {
    $('#tabla_nomina_real').DataTable( {
      "ordering": true,
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
            title: 'Ingresos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Ingresos',
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

<style media="screen">
  .daterangepicker.single.ltr .ranges { display : block !important; }
</style>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script>
$(function() {
  $('input[name="nomFecha"]').daterangepicker({
     singleDatePicker: true,
     showDropdowns: true,
     locale: {
      format: 'DD/MM/YYYY'
     },
     startDate: '<?= $fecha?>',
     maxYear: parseInt(moment().format('YYYY'),10)}
     , function(start, end, label) {
    var years = moment().diff(start, 'years');
    //alert("You are " + years + " years old!");
  });
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
