<?php
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  //header("location:Gastos_Maquinaria.php");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 55);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/indicador_error.php';
$modelNomina = new ViasInfo();
//SQL ULTIMA FECHA DE CORTE
$fec_corte = $modelNomina->sql(1,null, null);
/*----- GET FECHA -----*/
$fecha = $fec_corte[0]["MES1"];
if( isset($_GET["fecha"]) ){
  if ( $modelNomina->validateDate($_GET["fecha"]) ){
    $fecha = $_GET["fecha"];
  }else{
    $fecha = $fec_corte[0]["MES1"];
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
//echo $plaza;


$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}

$graficaDetalleAlmacen = $modelNomina->detalleGastos($fecha,$plaza,$almacen)
//$selectAlmacen = $modelNomina->almacenSql($plaza);
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
    <h1>Dashboard<small>Indice de Cumplimiento en Facturación de Servicios</small></h1>

<?php
      $anio_elegido = substr($fecha, -4);
      //echo count($graficaNominaMes);
      $mes_elegido = substr($fecha, 14, 2);
      #echo $mes_elegido;

      $Anio_actual = date("Y");

      if ($Anio_actual) {
        $mes_actual = idate("m");
        //echo $mes_actual;
      }else {
        $mes_actual = 12;
      }
?>


  </section>


  <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
  <!-- ############################ SECCION GRAFICA ############################# -->
  <section>

    <div class="row"><!-- row -->

    <div class="col-md-9"><!-- col-md-9 -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-bar-chart"></i> INFORMACIÓN </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <div class="row">
          <div class="col-md-6">
              <section>
                <div class="box box-success">
                  <div class="box-body"><!--box-body-->

                    <div class="table-responsive" id="container">
                      <table id="tabla_nomina" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                            <th class="small" bgcolor="#43ADBF"><font color="white">PLAZA</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">N° FACTURAS</font></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $cantidad_valores = 0;
                          $cantidad_facturas_error = 0;
                          for ($i=0; $i <count($graficaDetalleAlmacen) ; $i++) { ?>
                          <tr>
                            <!--<td class="small">CL</td>-->
                            <td class="small"><?= $graficaDetalleAlmacen[$i]["V_RAZON_SOCIAL"] ?></td>
                            <td class="small"><?= $graficaDetalleAlmacen[$i]["FACTURAS_REALIZADAS"] ?></td>
                            <?php $cantidad_valores = $cantidad_valores+$graficaDetalleAlmacen[$i]["FACTURAS_REALIZADAS"];
                                  $cantidad_facturas_error = $cantidad_facturas_error+$graficaDetalleAlmacen[$i]["FACTURAS_CANCELADAS"];
                            ?>
                          </tr>
                          <?php } ?>


                          <tr>
                            <th>Total Facturas Emitidas Cuatrimestre</th>
                            <th><?php echo $cantidad_valores; ?></th>
                          </tr>
                          <tr>
                            <th>Total Facturas Canceladas Cuatrimestral De Admon</th>
                            <th><?php echo $cantidad_facturas_error; ?></th>
                          </tr>
                          <tr>
                            <th>Criterio de Efectividad</th>
                            <th><?php $criterio = ($cantidad_facturas_error/$cantidad_valores)*100; echo round($criterio, 2); ?></th>
                          </tr>
                          <tr>
                            <th>Ponderación</th>
                            <th>
                                <?php
                                if ($criterio >= 0 and $criterio <= 1.99) {
                                  $valor = "100%";
                                  $color1 = "#43ADBF";
                                  $color2 = "white";
                                  $color3 = "white";
                                  $color4 = "white";
                                  $color5 = "white";
                                  $color6 = "white";

                                  $colort1 = "white";
                                  $colort2 = "black";
                                  $colort3 = "black";
                                  $colort4 = "black";
                                  $colort5 = "black";
                                  $colort6 = "black";
                                }elseif ($criterio >= 2 and $criterio <= 2.99) {
                                  $valor = "90%";
                                  $color1 = "white";
                                  $color2 = "#43ADBF";
                                  $color3 = "white";
                                  $color4 = "white";
                                  $color5 = "white";
                                  $color6 = "white";

                                  $colort1 = "black";
                                  $colort2 = "white";
                                  $colort3 = "black";
                                  $colort4 = "black";
                                  $colort5 = "black";
                                  $colort6 = "black";
                                }elseif ($criterio >= 3 and $criterio <= 3.99) {
                                  $valor = "80%";
                                  $color1 = "white";
                                  $color2 = "white";
                                  $color3 = "#43ADBF";
                                  $color4 = "white";
                                  $color5 = "white";
                                  $color6 = "white";

                                  $colort1 = "black";
                                  $colort2 = "black";
                                  $colort3 = "white";
                                  $colort4 = "black";
                                  $colort5 = "black";
                                  $colort6 = "black";
                                }elseif ($criterio >= 4 and $criterio <= 4.99) {
                                  $valor = "70%";
                                  $color1 = "white";
                                  $color2 = "white";
                                  $color3 = "white";
                                  $color4 = "#43ADBF";
                                  $color5 = "white";
                                  $color6 = "white";

                                  $colort1 = "black";
                                  $colort2 = "black";
                                  $colort3 = "black";
                                  $colort4 = "white";
                                  $colort5 = "black";
                                  $colort6 = "black";
                                }elseif ($criterio >= 5 and $criterio <= 5.99) {
                                  $valor = "60%";
                                  $color1 = "white";
                                  $color2 = "white";
                                  $color3 = "white";
                                  $color4 = "white";
                                  $color5 = "#43ADBF";
                                  $color6 = "white";

                                  $colort1 = "black";
                                  $colort2 = "black";
                                  $colort3 = "black";
                                  $colort4 = "black";
                                  $colort5 = "white";
                                  $colort6 = "black";
                                }elseif ($criterio >= 6) {
                                  $valor = "50%";
                                  $color1 = "white";
                                  $color2 = "white";
                                  $color3 = "white";
                                  $color4 = "white";
                                  $color5 = "white";
                                  $color6 = "#43ADBF";

                                  $colort1 = "black";
                                  $colort2 = "black";
                                  $colort3 = "black";
                                  $colort4 = "black";
                                  $colort5 = "black";
                                  $colort6 = "white";
                                }
                               echo $valor;
                                ?>
                            </th>
                          </tr>
                        </tbody>
                        <tfoot>

                        </tfoot>
                      </table>
                    </div>

                  </div><!--/.box-body-->
                </div>
              </section>
          </div>

          <!--PONDERACION -->
          <div class="col-md-6">
              <section>
                <div class="box box-success">
                  <div class="box-body"><!--box-body-->

                    <div class="table-responsive" id="container">
                      <table id="tabla_nomina2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                            <th class="small" bgcolor="#43ADBF"><font color="white">Criterio (Porcentaje De Efectividad)</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">Ponderacion De Calidad En El Servicio</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">Calificacion</font></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td class="small"  bgcolor="<?= $color1 ?>"><font color="<?= $colort1 ?>">A) 0 a 1.99% Facturas con Error</font></td>
                            <td class="small"  bgcolor="<?= $color1 ?>"><font color="<?= $colort1 ?>">100%</font></td>
                            <td class="small"  bgcolor="<?= $color1 ?>"><font color="<?= $colort1 ?>">Excelente</font></td>
                          </tr>
                          <tr>
                            <td class="small" bgcolor="<?= $color2 ?>"><font color="<?= $colort2 ?>">B) 2% a 2.99% Facturas con Error</font></td>
                            <td class="small" bgcolor="<?= $color2 ?>"><font color="<?= $colort2 ?>">90%</font></td>
                            <td class="small" bgcolor="<?= $color2 ?>"><font color="<?= $colort2 ?>">Muy Bueno</font></td>
                          </tr>
                          <tr>
                            <td class="small" bgcolor="<?= $color3 ?>"><font color="<?= $colort3 ?>">C) 3% a 3.99% Facturas con Error</font></td>
                            <td class="small" bgcolor="<?= $color3 ?>"><font color="<?= $colort3 ?>">80%</font></td>
                            <td class="small" bgcolor="<?= $color3 ?>"><font color="<?= $colort3 ?>">Bueno</font></td>
                          </tr>
                          <tr>
                            <td class="small" bgcolor="<?= $color4 ?>"><font color="<?= $colort4 ?>">D) 4% a 4.99% Facturas con Error</font></td>
                            <td class="small" bgcolor="<?= $color4 ?>"><font color="<?= $colort4 ?>">70%</font></td>
                            <td class="small" bgcolor="<?= $color4 ?>"><font color="<?= $colort4 ?>">Regular</font></td>
                          </tr>
                          <tr>
                            <td class="small" bgcolor="<?= $color5 ?>"><font color="<?= $colort5 ?>">E) 5% a 5.99% Facturas con Error</font></td>
                            <td class="small" bgcolor="<?= $color5 ?>"><font color="<?= $colort5 ?>">60%</font></td>
                            <td class="small" bgcolor="<?= $color5 ?>"><font color="<?= $colort5 ?>">Malo</font></td>
                          </tr>
                          <tr>
                            <td class="small" bgcolor="<?= $color6 ?>"><font color="<?= $colort6 ?>">F) 6% en adelante Facturas con Error</font></td>
                            <td class="small" bgcolor="<?= $color6 ?>"><font color="<?= $colort6 ?>">50%</font></td>
                            <td class="small" bgcolor="<?= $color6 ?>"><font color="<?= $colort6 ?>">Deficiente</font></td>
                          </tr>
                        </tbody>
                        <tfoot>

                        </tfoot>
                      </table>
                    </div>

                  </div><!--/.box-body-->
                </div>
              </section>
          </div>

          <!--GRAFICA NOMINA POR MES DIEGO ALTAMIRANO SUAREZ-->

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
        <a href="indicadores_por_error.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
        <?php } ?>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <!-- FILTRAR POR CONTRATO -->
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
          <input type="text" name="fecha_ingre_clie" value = "<?= $fecha  ?>"class="form-control pull-right" id="datepicker">
        </div>
        <!-- FILTRAR POR PLAZA -->
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
          <select class="form-control select2" id="nomPlaza" style="width: 100%;">
            <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $select_plaza = $modelNomina->sql(2,null,null);
            for ($i=0; $i <count($select_plaza) ; $i++) { ?>
              <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
            <?php } ?>
          </select>
        </div>
        <!--FILTRAR POR ALMACEN
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Almacen:</span>
          <select class="form-control select2" style="width: 100%;" id="nomAlm">
            <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $plazas = $_GET["plaza"];
            $selectAlmacen = $modelNomina->almacenSql($plazas);
            for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
              <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
             <?php } ?>
          </select>
        </div>-->
        <div class="input-group">
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>

      </div><!--/.box-body-->
    </div>

    </div><!-- /.col-md-3 -->

    <div class="col-md-3"><!-- col-md-9 -->
    <div class="box box-info">

      <div class="small-box bg-blue">
        <div class="inner">
          <h1>PONDERACIÓN</h3>

          <p><?php
          if ($criterio >= 0 and $criterio <= 1.99) {
            $valor2 = "Excelente 100%";
          }elseif ($criterio >= 2 and $criterio <= 2.99) {
            $valor2 = "Muy Bueno 90%";
          }elseif ($criterio >= 3 and $criterio <= 3.99) {
            $valor2 = "Bueno 80%";
          }elseif ($criterio >= 4 and $criterio <= 4.99) {
            $valor2 = "Regular 70%";
          }elseif ($criterio >= 5 and $criterio <= 5.99) {
            $valor2 = "Malo 60%";
          }elseif ($criterio >= 6 ) {
            $valor2 = "Deficiente 50%";
          }
         echo $valor2;
          ?></p>
        </div>
        <div class="icon">
          <i class="ion ion-card"></i>
        </div>
        <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
      </div>

    </div>

    </div><!-- /.col-md-3 -->
    </div><!-- /.row -->
  </section>
  <!-- ############################ /.SECCION GRAFICA ############################# -->


<?php if ( isset($_GET["fecha"]) || isset($_GET["plaza"]) || isset($_GET["tipo"]) || isset($_GET["status"]) || isset($_GET["contrato"]) || isset($_GET["depto"]) || isset($_GET["almacen"]) ){ ?>
  <!-- ############################ TABLA DETALLE DE NOMINA PAGADA ############################# -->
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
/*---- CLICK BOTON FILTRAR ----*/
$(".btnNomFiltro").on("click", function(){
  fecha = $('#datepicker').val();
  plaza = $('#nomPlaza').val();
  almacen = $('#nomAlm').val();

  url = '?fecha='+fecha+'&plaza='+plaza+'&almacen='+almacen;
  location.href = url;

});
</script>
<!-- DataTables -->
<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_nomina').DataTable( {
      paging: false,
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "ordering": false,
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
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
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
  <?=$graficaNomina[$i]["PAGADO"]?>,
  <?php }  ?>
  ];

  $('#graficaNom').highcharts({
    chart: { type: 'column' },
    title: { text: 'GASTOS TOTALES OPERACIONES POR PLAZAS' },
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
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
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

<script type="text/javascript">
function cambiar(){
    var pdrs = document.getElementById('file').files[0].name;
    document.getElementById('info').innerHTML = pdrs;
    document.getElementById('submit').disabled = false;
}
</script>
<!--grafica por mes -->
<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php
  for ($i=0; $i < $mes_actual ; $i++) {  ?>
  "<?=$graficaNominaMes[$i]["MES"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO"]?>,
  <?php }  ?>
  ];
  var data2 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO2"]?>,
  <?php }  ?>
  ];
  var data3 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO3"]?>,
  <?php }  ?>
  ];
  var data4 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PRESUPUESTO1"]?>,
  <?php }  ?>
  ];
  var data5 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PRESUPUESTO2"]?>,
  <?php }  ?>
  ];
  var data6 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PRESUPUESTO3"]?>,
  <?php }  ?>
  ];

  $('#graficaNomMes2').highcharts({
    chart: { type: 'line' },
    title: { text: <?php if ($plaza == 'ALL' && $almacen == 'ALL' && $tipo == 'ALL'): ?>
                    'GASTOS Y PRESUPUESTOS TOTALES ANUALES' },
                    <?php elseif($almacen == 1):  ?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN PEÑUELA' },
                    <?php elseif($almacen == 2):  ?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN PANTACO' },
                    <?php elseif($almacen == 3):  ?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN VICTORIA' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo == 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo != 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);
                          $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS Y PRESUPUESTOS DE <?=$nombreTip[0]["DESCRIPCION"]?>  ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($tipo != 'ALL' && $plaza == 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS Y PRESUPUESTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES' },
                    <?php elseif ($tipo != 'ALL' && $plaza != 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS Y PRESUPUESTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES PLAZA <?=$plaza?>' },
                    <?php else: ?>
                    'GASTOS Y PRESUPUESTOS TOTALES ANUALES PLAZA  <?=$plaza?>' },
                    <?php endif; ?>
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
    colors: ['#464f88', '#C21313', '#2DF306'],
    plotOptions:{
                series: {
                  minPointLength: 3
                }
    },
    xAxis: {
      categories: categories,
      labels: {
        formatter: function () {
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
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
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha; ?>',
              data: data1,
      }/*,{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-1;?>',
              data: data2,
      },{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-2;?>',
              data: data3,
      }*/,{
              name: 'Total Presupuesto del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha;?>',
              data: data4,
      }/*,{
              name: 'Total Presupuesto del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-1;?>',
              data: data5,
      },{
              name: 'Total Presupuesto del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-2;?>',
              data: data6,
      }*/]
    });

});
</script>


<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php
  for ($i=0; $i < $mes_actual ; $i++) {  ?>
  "<?=$graficaNominaMes[$i]["MES"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO"]?>,
  <?php }  ?>
  ];
  var data2 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO2"]?>,
  <?php }  ?>
  ];
  var data3 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO3"]?>,
  <?php }  ?>
  ];

  $('#graficaNomMes').highcharts({
    chart: { type: 'line' },
    title: { text: <?php if ($plaza == 'ALL' && $almacen == 'ALL' && $tipo == 'ALL'): ?>
                    'GASTOS TOTALES ANUALES' },
                    <?php elseif($almacen == 1):  ?>
                    'GASTOS ANUALES DEL ALMACEN PEÑUELA' },
                    <?php elseif($almacen == 2):  ?>
                    'GASTOS ANUALES DEL ALMACEN PANTACO' },
                    <?php elseif($almacen == 3):  ?>
                    'GASTOS ANUALES DEL ALMACEN VICTORIA' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo == 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);?>
                    'GASTOS ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo != 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);
                          $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS DE <?=$nombreTip[0]["DESCRIPCION"]?>  ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($tipo != 'ALL' && $plaza == 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES' },
                    <?php elseif ($tipo != 'ALL' && $plaza != 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES PLAZA <?=$plaza?>' },
                    <?php else: ?>
                    'GASTOS TOTALES ANUALES PLAZA  <?=$plaza?>' },
                    <?php endif; ?>
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
    colors: ['#464f88', '#C21313', '#2DF306'],
    plotOptions:{
                series: {
                  minPointLength: 3
                }
    },
    xAxis: {
      categories: categories,
      labels: {
        formatter: function () {
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
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
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha; ?>',
              data: data1,
      },{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-1;?>',
              data: data2,
      },{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-2;?>',
              data: data3,
      }]
    });

});
</script>

<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php for ($i=0; $i <count($graficaPlazaAlmacen) ; $i++) {  ?>
  "<?=$graficaPlazaAlmacen[$i]["ALMACEN"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i <count($graficaPlazaAlmacen) ; $i++) {  ?>
  <?=$graficaPlazaAlmacen[$i]["PAGADO"]?>,
  <?php }  ?>
  ];

  $('#graficaAlmacen').highcharts({
    chart: { type: 'column' },
    title: { text: <?php if ($plaza == 'ALL' && $almacen == 'ALL'): ?>
                    'ALMACENES DE LA PLAZA' },
                   <?php elseif ($almacen != 'ALL'): ?>
                   'ALMACENES DE LA PLAZA' },
                   <?php ?>
                   <?php else: ?>
                    'ALMACENES DE LA PLAZA <?=$plaza?>' },
                   <?php endif; ?>
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
      categories: categories,
      labels: {
        formatter: function () {
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
            return '<a>' +
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
