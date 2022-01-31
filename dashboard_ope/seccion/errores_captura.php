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

include_once '../class/errores_captura.php';
$obj_class = new RotacionPersonal();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 36);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////

/* $_GET FECHA */
$fecha = 'ALL';
if ( isset($_GET["fecha"]) ){
  if ( $obj_class->validateDate(substr($_GET["fecha"],0,10)) AND $obj_class->validateDate(substr($_GET["fecha"],11,10)) ){
    $fecha = $_GET["fecha"];
  }else{
    $fecha = "ALL";
  }
}
/* $_GET FIL_CHECK */
$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}

if($_SESSION['area']==3){
  $plaza = $_SESSION['nomPlaza'];
}else {
  $plaza = "ALL";
}

$plaza=$_SESSION["nomPlaza"];

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
//GRAFICA
$grafica = $obj_class->grafica($plaza,$fil_check,$fecha);
$graficaMensual = $obj_class->graficaMensual($plaza,$fil_check,$fecha);
$tabla = $obj_class->tabla($plaza,$fil_check,$fecha);

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
        Dashboard
        <small>Errores Captura</small>
        <?php //if($_SESSION['area']==3){echo "<center><h4> PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>";} ?><!--FILTRAR UNICAMENTE P/DEPTO. OPERACIONES -->
        <?php echo "<center><h4>PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>"; ?><!--FILTRO GENERAL -->
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

        <?php ?>
        <div class="col-md-12">
          <div id="graf_bar"></div>

          <!--AQUI PONDREMOS UNA TABLA-->
          <div class="table-responsive">
            <table id="tabla_activo" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PLAZA</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tabla) ; $i++) { ?>
                <tr>
                  <td><?= $tabla[$i]["N_SERVICIO"] ?></td>
                  <td><?= $tabla[$i]["NOMBRE"] ?></td>
                  <td><?= $tabla[$i]["DESCRIPCION"] ?></td>
                  <td><?= $tabla[$i]["PLAZA"] ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

          <div id="graf_perM"></div>
        </div>
        <?php  ?>

        </div><!--/.box-body-->
      </div>
    </div>

    <?php //if ($plaza != 'ALL'){ ?>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
          <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="errores_captura.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
          <?php } ?>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <!-- FILTRAR POR fecha -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
            <input type="text" class="form-control pull-right" name="fil_fecha" disabled>
            <span class="input-group-addon"> <input type="checkbox" name="fil_check" <?php if( $fil_check == 'on' ){ echo "checked";} ?> > </span>
          </div>

          <!-- FILTRAR POR PLAZA -->
          <input id="fil_plaza" type="hidden" value=<?= $plaza ?>>
          <?php if($_SESSION['area']!=3){ ?>
          <!--<div class="input-group">
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
        <?php } else{?>
          <input id="fil_plaza" type="hidden" value=<?= $plaza ?>>-->
        <?php }?>

          <div class="input-group">
            <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil"><i class="fa fa-check"></i> Filtrar</button> </span>
          </div>
        </div><!--/.box-body-->
      </div>
    </div>
    <?php //} ?>

  </div>
</section>
<!-- ############################ ./SECCION GRAFICA Y WIDGETS ############################# -->




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
<?php if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){ ?>
  $('input[name="fil_fecha"]').attr("disabled", false);
<?php } ?>
$('input[name="fil_check"]').on("click", function (){

  if ($('input[name="fil_check"]').is(':checked')) {
    $('input[name="fil_fecha"]').attr("disabled", false);
  }else{
    $('input[name="fil_fecha"]').attr("disabled", true);
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
  fil_check = 'off';

  //Fill habilitados
  fil_habilitado = 'off';

  url = '?plaza='+fil_plaza+'&check='+fil_check+'&contrato='+fil_contrato+'&depto='+fil_departamento+'&area='+fil_area+'&fil_habilitado='+fil_habilitado;
  if ($('input[name="fil_check"]').is(':checked')) {
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
     for ($i=0; $i <count($grafica) ; $i++) {
      echo "'".$grafica[$i]["PLAZA"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($grafica) ; $i++) {
      echo $grafica[$i]["CANTIDAD"].",";
    }
    ?>
    ];
    $('#graf_bar').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'ERRORES CAPTURA'
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
                text: ''
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
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';

                return '<a>' +
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
            name: 'Personal Activ',
            data: data1,
        }]

    });
});
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
          type: 'pie'
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
          echo $graficaMensual[$i]["TOTAL"].",";
    }
     ?>
  ];
  $('#graf_perM').highcharts({
    chart:{
      type:'line'
    },
    title:{
      text:'ERRORES EN CAPTURA POR MES'
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
        text:'N° Errores'
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
    colors:['#1399C2', '#C21313', '#1C00ff00', '#1C00ff00'],
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
      name:'Cantidad de Errores del <?php if ($fecha == 'ALL') { echo date('Y');} else { echo substr($fecha, 6, 5);} ?>',
      data: data1,
    }
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
