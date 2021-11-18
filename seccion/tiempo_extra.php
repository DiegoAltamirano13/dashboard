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
  #echo $clase;
}
spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 20);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/tiempo_extra.php';
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
/*----- GET TIPO NOMINA -----*/
$tipo = "1,10,21,9,18,14";
if( isset($_GET["tipo"]) ){
$tipoArray = explode(",",$_GET["tipo"]);
  for ($i=0; $i <count($tipoArray) ; $i++) {
    if ( $tipoArray[$i] == "1"||$tipoArray[$i] == "10"||$tipoArray[$i] == "21"||$tipoArray[$i] == "9"||$tipoArray[$i] == "18"||$tipoArray[$i] == "14"){
      $tipo = $_GET["tipo"];
    }else{
      $tipo = "1,10,21,9,18,14"; break;
    }
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
$widgetsNom = $modelNomina->widgetsTiempoExtra($fecha,$plaza);
//GRAFICA NOMINA
$graficaNomina = $modelNomina->graficaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area);
$graficaMensual = $modelNomina->graficaMensualTiempoExtr($fecha,$plaza);
//TABLA DETALLE DE NOMINA PAGADA
$tablaNomina = $modelNomina->tablaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area);
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
    <h1>Dashboard<small>Horas Extra</small></h1>
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

          <div class="col-xs-9 col-md-12">
            <div id="graficaNom2"></div>
            <div id="graficaNom"></div>
          </div>
          <div class="col-xs-9 col-md-12">
            <div id="graficaMensualTE">
            </div>
          </div>

        </div>

      </div><!--/.box-body-->
    </div>
    </div><!-- /.col-md-9 -->


    <div class="col-md-3"><!-- col-md-9 -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
        <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
        <a href="tiempo_extra.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
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
        <style type="text/css">
          .select2-selection__choice {
  background: #3c8dbc !important;
  background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #3c8dbc), color-stop(1, #67a8ce)) !important;
  background: -ms-linear-gradient(bottom, #3c8dbc, #67a8ce) !important;
  background: -moz-linear-gradient(center bottom, #3c8dbc 0%, #67a8ce 100%) !important;
  background: -o-linear-gradient(#67a8ce, #3c8dbc) !important;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#67a8ce', endColorstr='#3c8dbc', GradientType=0) !important;
  color: #fff;
}
        </style>
        <div class="input-group">
            <?php
            if ($depto != 'ALL'){ // IF
              $select_area = $modelNomina->sql(4,$depto);
              for ($i=0; $i <count($select_area) ; $i++) { // FOR ?>
                <option value="<?= $select_area[$i]["IID_AREA"] ?>" <?php if($select_area[$i]["IID_AREA"] == $area){echo 'selected';} ?>><?= $select_area[$i]["V_DESCRIPCION"] ?></option>
            <?php
              } // /. FOR
            } // /.IF
            ?>
          </select>
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>

      </div><!--/.box-body-->
    </div>
    <!--WIDGETS -->
    <div class="id_widgets" class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-tags"></i>WIDGETS</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget = "collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
      </div>
    </div>
    <div class="box-body">
      <!--WIDGET 1-->
      <div class="info-box bg-blue">
        <span class="info-box-icon"><i class="fa fa-money"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Importes Dobles</span>
          <span class="info-box-number"><?php echo number_format($widgetsNom[0]["RECIBO"], 2); ?></span>
          <div class="progress">
            <div class="progress-bar" style="width: 50%"></div>
          </div>
        </div>

      </div>

      <div class="info-box bg-green">
        <span class="info-box-icon"><i class="fa fa-money"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Importes Triples</span>
          <span class="info-box-number"><?php echo number_format($widgetsNom[0]["RECIBO_TRIPLE"], 2); ?></span>
          <div class="progress">
            <div class="progress-bar" style="width: 50%"></div>
          </div>
        </div>

      </div>

      <div class="info-box bg-purple">
        <span class="info-box-icon"><i class="fa fa-money"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">TOTAL</span>
          <span class="info-box-number"><?php echo number_format($widgetsNom[0]["RECIBO_TRIPLE"]+$widgetsNom[0]["RECIBO"], 2); ?></span>
          <div class="progress">
            <div class="progress-bar" style="width: 50%"></div>
          </div>
        </div>

      </div>
    </div>
    <!--WIDGETS -->
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
                <th class="small" bgcolor="#383F6D"><font color="white">ID</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">EMPLEADO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">PLAZA</font></th>
                <th class="small" bgcolor="#383F6D"><font color= "white">HORAS DOBLES</font></th>
                <th class="small" bgcolor="#383F6D"><font color= "white">HORAS TRIPLES</font></th>
                <th class="small" bgcolor="#383F6D"><font color= "white">IMPORTE DOBLES</font></th>
                <th class="small" bgcolor="#383F6D"><font color= "white">IMPORTE TRIPLES</font></th>
                <th class="small" bgcolor="#383F6D"><font color= "white">TOTAL</font></th>
                </th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i <count($tablaNomina) ; $i++) { ?>
              <tr>
                <td class="small"><?= $tablaNomina[$i]["IID_EMPLEADO"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["NOMBRE"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["PLAZA"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["HORAS_DOBLES"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["HORAS_TRIPLES"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["PAGADO_DOBLE"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["PAGO_TRIPLE"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["PAGADO_DOBLE"] + $tablaNomina[$i]["PAGO_TRIPLE"] ?></td>
              </tr>
              <?php } ?>
            </tbody>
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
/*---- SELECT TIPO DE NOMINA ----*/
$('#nomTipo').select2({
  tags: true,
  tokenSeparators: [","]
});
$('#nomTipo').val([<?=$tipo?>]).trigger("change");
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
  <?php for ($i=0; $i <count($graficaNomina) ; $i++) {
    echo $graficaNomina[$i]["DOBLES"].",";
  }  ?>
  ];
  var data2 = [
    <?php for ($i=0; $i <count($graficaNomina) ; $i++) {
      echo $graficaNomina[$i]["TRIPLES"].",";
    }  ?>
  ];
  var data3 = [
    <?php for ($i=0; $i <count($graficaNomina) ; $i++) {
      echo $graficaNomina[$i]["TOTAL"].",";
    }  ?>
  ];
  $('#graficaNom').highcharts({
    chart: { type: 'column' },
    title: { text: 'TIEMPO EXTRA' },
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
            valueSuffix: '',
            useHTML: true,
            valueDecimals: 0,
            valuePrefix: '',
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
    colors: ['#464f88', '#E51C23', '#40F102'],
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
            name: 'Horas dobles',
            color: 'yellow',
            data: data1,
          },
          <?php  ?>
          {
            name: 'Horas triples',
            color: 'red',
            data: data2,
          },
          {
            name: 'Importe Total',
            color: 'blue',
            data: data3,
          }
        ]
    });

});
</script>

<!-- GRAFICA COMPARATIVO ANUAL -->
<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
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
    for ($i=0; $i < 5 ; $i++) {
      $anio2 = $anio - 4 + $i;
      $graficaNominaAnual = $modelNomina->graficaAnualComp($anio2,$plaza,$tipo,$status,$contrato,$depto,$area);
      $TOTAL_IMPORTE = $graficaNominaAnual[0]["RECIBO"] + $graficaNominaAnual[0]["RECIBO_TRIPLE"];
      echo $TOTAL_IMPORTE.",";
    }
  ?>
  ];
  var data2 = [
    <?php
      $anio = substr($fecha, 17, 4);
      for ($i=0; $i < 5 ; $i++) {
        $anio2 = $anio - 4 + $i;
        $graficaNominaAnual = $modelNomina->graficaAnualComp($anio2,$plaza,$tipo,$status,$contrato,$depto,$area);
        $TOTAL_IMPORTE = $graficaNominaAnual[0]["HORAS_DOBLES"] + $graficaNominaAnual[0]["HORAS_TRIPLES"];
        echo $TOTAL_IMPORTE.",";
      }
    ?>
  ];
  $('#graficaNom2').highcharts({
    chart: { type: 'column' },
    title: { text: 'COMPARATIVO DE TIEMPO EXTRA ANUAL' },
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
            valueSuffix: '',
            useHTML: true,
            valueDecimals: 0,
            valuePrefix: '',
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
    colors: ['#464f88', '#E51C23'],
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
            name: 'IMPORTES',
            data: data1,
            color: "yellow",
          },
          <?php  ?>
          {
            name: 'TOTAL HORAS',
            color:"red",
            data: data2,
          }
        ]
    });

});
</script>

<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php for ($i=0; $i <count($graficaMensual) ; $i++) {  ?>
  "<?=$graficaMensual[$i]["NOMBRE_MES"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i <count($graficaMensual) ; $i++) {
    echo $graficaMensual[$i]["DOBLES"].",";
  }  ?>
  ];
  var data2 = [
    <?php for ($i=0; $i <count($graficaMensual) ; $i++) {
      echo $graficaMensual[$i]["TRIPLES"].",";
    }  ?>
  ];
  $('#graficaMensualTE').highcharts({
    chart: { type: 'line' },
    title: { text: 'TIEMPO EXTRA DEL AÑO <?php echo substr($fecha, 6,4); ?>' },
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
            valueSuffix: '',
            useHTML: true,
            valueDecimals: 0,
            valuePrefix: '',
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
    colors: ['#464f88', '#E51C23'],
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
      text: '',
      align: 'right',
      x: -10,
    },
    series:[{
            name: 'Horas dobles',
            data: data1,
          },
          <?php  ?>
          {
            name: 'Horas triples',
            data: data2,
          }
        ]
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
