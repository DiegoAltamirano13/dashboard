<?php
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:rh_cat_cursos.php");
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

include_once '../class/RH_car_cursos.php';
$obj_class = new RotacionPersonal();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 31);
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
/* $_GET PLAZA */
$plaza = "ALL";
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

// recuperacion del cursor$almacen = "ALL";
$curso = "ALL";
if (isset($_GET["curso"])) {
    $curso = $_GET["curso"];
}

$empleado = "ALL";
if (isset($_GET["empleado"])) {
    $empleado = $_GET["empleado"];
}
//GRAFICA
$grafica = $obj_class->grafica($plaza,$fil_check,$fecha,$curso,$empleado);
$tabla_curso = $obj_class->tabla_Curso($plaza,$fil_check,$fecha,$curso,$empleado);
$widgets = $obj_class->widget($plaza,$fil_check,$fecha,$curso,$empleado);
$widgets2 = $obj_class->widgettt($plaza,$fil_check,$fecha,$curso,$empleado);
$graficaMensual = $obj_class->graficaMensual($plaza,$fil_check,$fecha,$curso,$empleado);
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
        <small>Rotación de Personal</small>
      </h1>
    </section>
    <section class="content">

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
                <div class="col-md-12">
                  <div id="graf_bar_cur"></div>
                  <div id="graf_bar_cur2"></div>
                  <div id="graf_bar"></div>
                </div>

                <table id="tabla_cursos" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                      <th class="small" bgcolor="#2b35b6"><font color="white">CURSO</font></th>
                      <th class="small" bgcolor="#2b35b6"><font color="white">INSTRUCTOR EXTERNO</font></th>
                      <th class="small" bgcolor="#2b35b6"><font color="white">INSTRUCTOR INTERNO</font></th>
                      <th class="small" bgcolor="#2b35b6"><font color="white">EMPLEADO</font></th>
                      <th class="small" bgcolor="#2b35b6"><font color="white">PLAZA</font></th>
                      <th class="small" bgcolor="#2b35b6"><font color="white">FECHA INICIO</font></th>
                      <th class="small" bgcolor="#2b35b6"><font color="white">FECHA FIN</font></th>
                      <th class="small" bgcolor="#2b35b6"><font color="white">DURACION</font></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < COUNT($tabla_curso); $i++) { ?>
                      <tr>
                        <td class="small">
                          <?php echo $tabla_curso[$i]["DESCRIPCION"]; ?>
                        </td>
                        <td class="small">
                          <?php echo $tabla_curso[$i]["NOMBRE_INSTRUCTOR"]; ?>
                        </td>
                        <td class="small">
                          <?php echo $tabla_curso[$i]["NOMBRE"]; ?>
                        </td>
                        <td class="small">
                          <?php echo $tabla_curso[$i]["TOMO_CURSO"]; ?>
                        </td>
                        <td class="small">
                          <?php echo $tabla_curso[$i]["PLAZA_CURSO"]; ?>
                        </td>
                        <td class="small">
                          <?php echo $tabla_curso[$i]["INICIO"]; ?>
                        </td>
                        <td class="small">
                          <?php echo $tabla_curso[$i]["FIN"]; ?>
                        </td>
                        <td class="small">
                          <?php echo $tabla_curso[$i]["DURACION"]; ?>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <div class="col-md-12">
                  <div id="graf_Men"></div>
                </div>
              </div><!--/.box-body-->
            </div>
          </div>

          <?php //if ($plaza != 'ALL'){ ?>
          <div class="col-md-3">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
                <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
                <a href="rh_cat_cursos.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
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
                <div class="input-group">
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

                <!-- FILTRAR POR CURSOS -->
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Curso:</span>
                  <select class="form-control select2" style="width: 100%;" id="nomCur">
                    <option value="ALL" <?php if( $curso == 'ALL'){echo "selected";} ?> >ALL</option>
                    <?php
                    $plaza = $_GET["plaza"];
                    $selectCursos = $obj_class->cursosSql();
                    for ($i=0; $i <count($selectCursos) ; $i++) { ?>
                      <option value="<?=$selectCursos[$i]["ID_CURSO"]?>" <?php if($selectCursos[$i]["ID_CURSO"] == $curso){echo "selected";} ?>><?=$selectCursos[$i]["V_NOMBRE"]?> </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Empleado:</span>
                  <select class="form-control select2" style="width: 100%;" id="nomEmpl">
                    <option value="ALL" <?php if( $empleado == 'ALL'){echo "selected";} ?> >ALL</option>
                    <?php
                    $plaza = $_GET["plaza"];
                    $selectEmpleado = $obj_class->empSql($plaza);
                    for ($i=0; $i <count($selectEmpleado) ; $i++) { ?>
                      <option value="<?=$selectEmpleado[$i]["IID_EMPLEADO"]?>" <?php if($selectEmpleado[$i]["IID_EMPLEADO"] == $empleado){echo "selected";} ?>><?=$selectEmpleado[$i]["V_NOMBRE"]?> </option>
                    <?php } ?>
                  </select>
                </div>
                <!-- FILTRAR POR AREA -->
                <div class="input-group">
                  <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil"><i class="fa fa-check"></i> Filtrar</button> </span>
                </div>

              </div><!--/.box-body-->
            </div>
            <!--  WIDGETS -->
          </div>
          <div id="div_widgets" class="col-md-3">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Widgets</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
              </div>
              <div class="box-body"><!--box-body-->

                <!-- WIDGETS #1 -->
                <div class="info-box bg-blue">
                  <span class="info-box-icon"><i class="fa fa-users"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Personal Capacitado <?=$plaza?></span>
                    <span class="info-box-number"><?php echo $widgets[0]["N_EMPLEADO"]; ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 70%"></div>
                    </div>
                  </div>
                </div>
                <!--WIDGET TOTAL HORAS-->
                <!-- WIDGETS #1 -->
                <div class="info-box bg-blue">
                  <span class="info-box-icon"><i class="fa fa-users"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Tiempo Capacitación <?=$plaza?></span>
                    <span class="info-box-number"><?php echo $widgets2[0]["TOTAL_HORAS"]."Hrs"; ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 70%"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- -->

          <?php  ?>
        </div>
      </section>

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

//BOTON FILTRAR
$(".btn_fil").on("click", function(){

  fil_fecha = $('input[name="fil_fecha"]').val();
  fil_plaza = $('#fil_plaza').val();
  fil_check = 'off';

  //Fill habilitados
  fil_habilitado = 'off';

  fil_curso = $('#nomCur').val();
  fil_empleado = $('#nomEmpl').val();
  url = '?plaza='+fil_plaza+'&check='+fil_check;
  if ($('input[name="fil_check"]').is(':checked')) {
    fil_check = 'on';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&curso='+fil_curso+'&empleado='+fil_empleado;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&curso='+fil_curso+'&empleado='+fil_empleado;
    }

  }else{
    fil_check = 'off';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
        fil_habilitado = 'on';
        url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&curso='+fil_curso+'&empleado='+fil_empleado;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&curso='+fil_curso+'&empleado='+fil_empleado;
    }
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
$(document).ready(function() {
    $('#tabla_cursos').DataTable( {
        "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    //i.replace('.','').replace(/[\$,]/g, '.')*1:
                    typeof i === 'number' ?
                        i : 0;
            };

            // Total over all pages
            total = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return Intl.NumberFormat().format(intVal(a) + intVal(b));
                    //return intVal(a) + intVal(b);
                    //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                }, 0 );

            // Total over this page
            pageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return Intl.NumberFormat().format(intVal(a) + intVal(b));
                    //return Math.round(intVal(a) + intVal(b));
                }, 0 );

            // Update footer
            $( api.column( 5 ).footer() ).html(
                //''+pageTotal +' ('+ total +' total)'
                ''+pageTotal +' Toneladas'
            );
        },

        "scrollY": 450,
        fixedHeader: true,
        "dom": '<"toolbar">frtip',
        stateSave: true,
        "scrollX": true,
        "language": {
            "url": "../plugins/datatables/Spanish.json"
        },

        dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
            buttons: [

              {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                exportOptions: {//muestra/oculta visivilidad de columna
                    columns: ':visible'
                },
                title: 'Suma Toneladas',
              },

              {
                extend: 'print',
                text: '<i class="fa fa-print"></i>',
                titleAttr: 'Imprimir',
                exportOptions: {//muestra/oculta visivilidad de columna
                    columns: ':visible',
                },
                title: 'Suma Toneladas',
              },

              {
                extend: 'colvis',
                collectionLayout: 'fixed two-column',
                text: '<i class="fa fa-eye-slash"></i>',
                titleAttr: '(Mostrar/ocultar) Columnas',
                autoClose: true,
              }
            ],
    } );
} );

</script>
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
      echo "'".$grafica[$i]["DESCRIPCION"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($grafica) ; $i++) {
      echo $grafica[$i]["ID_CURSO"].",";
    }
    ?>
    ];
    $('#graf_bar').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'CURSOS'
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
                text: 'EMPLEADOS'
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
        colors: ['#0073B7'],
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
              url = '?plaza='+this.value+'&check=<?= $fil_check?>&curso<?=$curso?>&empleado<?=$empleado?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>&curso<?=$curso?>&empleado<?=$empleado?>';
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
        series:  [{
          showInLegend:false,
            name: 'Personal Activ',
            data: data1,
        }]

    });
});
</script>
<!--
<script type="text/javascript">

$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
    if ($fecha == "ALL") {
      $anio = date("Y");
    }else {
      $anio = substr($fecha, 17, 4);
    }
    $porcentaje_anual = $obj_class->horas_vs_empleados($anio, $plaza);
     for ($i=0; $i < COUNT($porcentaje_anual) ; $i++) {
      echo "'".$porcentaje_anual[$i]["MES"]."',";
     }
    ?>
    ];
    var data1 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }
      $porcentaje_anual = $obj_class->horas_vs_empleados($anio, $plaza);
      for ($i=0; $i < COUNT($porcentaje_anual); $i++) {
        if ($porcentaje_anual[$i]["HORAS_REALES"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = number_format($porcentaje_anual[$i]["HORAS_REALES"], 2);
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];
    var data2 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }
      $anio2 = $anio -1 ;
      $porcentaje_anual = $obj_class->horas_vs_empleados($anio2, $plaza);
      for ($i=0; $i < COUNT($porcentaje_anual); $i++) {
        if ($porcentaje_anual[$i]["HORAS_REALES"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = number_format($porcentaje_anual[$i]["HORAS_REALES"], 2);
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];
    var data4 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }
      $anio2 = $anio -2 ;
      $porcentaje_anual = $obj_class->horas_vs_empleados($anio2, $plaza);
      for ($i=0; $i < COUNT($porcentaje_anual); $i++) {
        if ($porcentaje_anual[$i]["HORAS_REALES"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = number_format($porcentaje_anual[$i]["HORAS_REALES"], 2);
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];
    var data3 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }
      $anio2 = $anio -3 ;
      $porcentaje_anual = $obj_class->horas_vs_empleados($anio2, $plaza);
      for ($i=0; $i < COUNT($porcentaje_anual); $i++) {
        if ($porcentaje_anual[$i]["HORAS_REALES"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = number_format($porcentaje_anual[$i]["HORAS_REALES"], 2);
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];
    $('#graf_bar_cur').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'HORAS POR PERSONAL CAPACITADO'
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
                text: 'HORAS X PERSONAL CAPACITADO'
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
        colors: ['#F0FF04', "#024EFE","#FE0202", "#56FE02" ],
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
              url = '?plaza='+this.value+'&check=<?= $fil_check?>&curso<?=$curso?>&empleado<?=$empleado?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>&curso<?=$curso?>&empleado<?=$empleado?>';
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
        series:  [{
          showInLegend:true,
            name: '<?php if ($fecha == "ALL") { $anio = date("Y"); }else { $anio = substr($fecha, 17, 4); } echo "$anio"; ?>',
            data: data1,
        },{
          showInLegend:true,
            name: '<?php if ($fecha == "ALL") { $anio = date("Y"); }else { $anio = substr($fecha, 17, 4); } echo $anio-1; ?>',
            data: data2,
        },{
          showInLegend:true,
            name: '<?php if ($fecha == "ALL") { $anio = date("Y"); }else { $anio = substr($fecha, 17, 4); } echo $anio-2; ?>',
            data: data3,
        },{
          showInLegend:true,
            name: '<?php if ($fecha == "ALL") { $anio = date("Y"); }else { $anio = substr($fecha, 17, 4); } echo $anio-3; ?>',
            data: data4,
        }]

    });
});
</script> !-->


<script type="text/javascript">

$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
    if ($fecha == "ALL") {
      $anio = date("Y");
    }else {
      $anio = substr($fecha, 17, 4);
    }
     for ($i=0; $i < 5 ; $i++) {
       $anio2 = $anio -4 + $i;

      echo "'".$anio2."',";
     }
    ?>
    ];
    var data1 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }

      for ($i=0; $i < 5; $i++) {
        $anio2 = $anio -4 + $i;
        if ($anio2 == $anio and $fecha <> "ALL") {
            $porcentaje_anual = $obj_class->horas_general2($fecha, $plaza);
        }else {
            $porcentaje_anual = $obj_class->horas_general($anio2, $plaza);
        }
        if ($porcentaje_anual[0]["TOTAL"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = number_format($porcentaje_anual[0]["TOTAL"], 2);
        }
        echo $caantidad_horas.",";
      }
       ?>
    ];
    var data2 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }

      for ($i=0; $i < 5; $i++) {
        $anio2 = $anio -4 + $i;
        if($anio2 == $anio and $fecha <> "ALL"){
          $porcentaje_anual = $obj_class->horas_real_emp2($fecha, $plaza);
        }else {
          $porcentaje_anual = $obj_class->horas_real_emp($anio2, $plaza);
        }
        if ($porcentaje_anual[0]["TOTAL_REALES"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = number_format($porcentaje_anual[0]["TOTAL_REALES"], 2);
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];
    var data3 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }

      for ($i=0; $i < 5; $i++) {
        $anio2 = $anio -4 + $i;
        $porcentaje_anual = $obj_class->costos_general($anio2, $plaza);
        if ($porcentaje_anual[0]["TOTAL"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = number_format($porcentaje_anual[0]["TOTAL"], 2);
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];

    var data4 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }

      for ($i=0; $i < 5; $i++) {
        $anio2 = $anio -4 + $i;
        $porcentaje_anua2l = $obj_class->costos_general_emp_cap($anio2, $plaza);
        if ($porcentaje_anua2l[0]["TOTAL"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = $porcentaje_anua2l[0]["TOTAL"];
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];


    $('#graf_bar_cur').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'HORAS POR PERSONAL CAPACITADO'
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
                text: 'HORAS'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          valueDecimals: 2
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
        colors: ['#F0FF04', "#024EFE","#FE0202", "#56FE02" ],
        plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: true,
              formatter: function () {
                   return Highcharts.numberFormat(this.y,2);
               }
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
              url = '?plaza='+this.value+'&check=<?= $fil_check?>&curso<?=$curso?>&empleado<?=$empleado?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>&curso<?=$curso?>&empleado<?=$empleado?>';
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
        series:  [{
          showInLegend:true,
            name: 'HORAS/PERSONAL ACTIVO ',
            data: data1,
        },{
          showInLegend:true,
            name: 'HORAS/PERSONAL CAPACITADO',
            data: data2,
        }]

    });
});
</script>
<script type="text/javascript">

$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
    if ($fecha == "ALL") {
      $anio = date("Y");
    }else {
      $anio = substr($fecha, 17, 4);
    }
     for ($i=0; $i < 5 ; $i++) {
       $anio2 = $anio -4 + $i;

      echo "'".$anio2."',";
     }
    ?>
    ];
    var data1 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }

      for ($i=0; $i < 5; $i++) {
        $anio2 = $anio -4 + $i;
        $porcentaje_anual = $obj_class->horas_general($anio2, $plaza);
        if ($porcentaje_anual[0]["TOTAL"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = number_format($porcentaje_anual[0]["TOTAL"], 2);
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];
    var data2 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }

      for ($i=0; $i < 5; $i++) {
        $anio2 = $anio -4 + $i;
        if($anio2 == $anio and $fecha <> "ALL"){
          $porcentaje_anual = $obj_class->horas_real_emp2($fecha, $plaza);
        }else {
          $porcentaje_anual = $obj_class->horas_real_emp($anio2, $plaza);
        }

        if ($porcentaje_anual[0]["TOTAL_REALES"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = number_format($porcentaje_anual[0]["TOTAL_REALES"], 2);
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];
    var data3 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }

      for ($i=0; $i < 5; $i++) {
        $anio2 = $anio -4 + $i;
        if ($anio2 == $anio and $fecha <> "ALL") {
          $porcentaje_anual = $obj_class->costos_general2($fecha, $plaza);
        }else {
            $porcentaje_anual = $obj_class->costos_general($anio2, $plaza);
        }

        if ($porcentaje_anual[0]["TOTAL"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = number_format($porcentaje_anual[0]["TOTAL"], 2);
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];

    var data4 = [
      <?php
      if ($fecha == "ALL") {
        $anio = date("Y");
      }else {
        $anio = substr($fecha, 17, 4);
      }

      for ($i=0; $i < 5; $i++) {
        $anio2 = $anio -4 + $i;
        if ($anio2 == $anio and $fecha <> "ALL") {
          $porcentaje_anua2l = $obj_class->costos_general_emp_cap2($fecha, $plaza);
        }else {
          $porcentaje_anua2l = $obj_class->costos_general_emp_cap($anio2, $plaza);
        }


        if ($porcentaje_anua2l[0]["TOTAL"] == 0) {
          $caantidad_horas = 0;
        }else {
          $caantidad_horas = $porcentaje_anua2l[0]["TOTAL"];
        }

        echo $caantidad_horas.",";
      }
       ?>
    ];


    $('#graf_bar_cur2').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'COSTO POR PERSONAL CAPACITADO'
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
                text: 'COSTO'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          valueDecimals: 2,
          valuePrefix: '$'
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
        colors: ['#F0FF04', "#024EFE","#FE0202", "#56FE02" ],
        plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: true,
              formatter: function () {
                   return '$'+Highcharts.numberFormat(this.y,2);
               }
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
              url = '?plaza='+this.value+'&check=<?= $fil_check?>&curso<?=$curso?>&empleado<?=$empleado?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>&curso<?=$curso?>&empleado<?=$empleado?>';
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
        series:  [{
          showInLegend:true,
            name: 'COSTO POR CURSOS / PERSONAL ACTIVO',
            data: data3,
        },{
          showInLegend:true,
            name: 'COSTO POR CURSOS / PERSONAL CAPACITADO',
            data: data4,
        }]

    });
});
</script>
<script type="text/javascript">

$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($graficaMensual) ; $i++) {
      echo "'".$graficaMensual[$i]["MES"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaMensual) ; $i++) {
      echo $graficaMensual[$i]["CONTADOR"].",";
    }
    ?>
    ];
    $('#graf_Men').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'CURSOS'
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
                text: 'EMPLEADOS'
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
        colors: ['#0073B7'],
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
              url = '?plaza='+this.value+'&check=<?= $fil_check?>&curso<?=$curso?>&empleado<?=$empleado?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>&curso<?=$curso?>&empleado<?=$empleado?>';
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
        series:  [{
          showInLegend:false,
            name: 'Personal Activ',
            data: data1,
        }]

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
