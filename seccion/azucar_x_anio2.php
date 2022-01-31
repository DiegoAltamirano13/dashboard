<?php
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:azucar_x_anio2.php");
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

include_once '../class/azucar_x_anio.php';
$obj_class = new RotacionPersonal();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 32);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
$anio = date("Y");
if (isset($_GET["anio"])) {
  $anio = $_GET["anio"];
}
// recuperacion del cursor$almacen = "ALL";
$curso = "ALL";
if (isset($_GET["curso"])) {
    $curso = $_GET["curso"];
}
//GRAFICA
$grafica = $obj_class->grafica($anio,1);
$graficaAnt = $obj_class->grafica_anio_anterior($anio,1);

$graficaHab = $obj_class->graficaHabilitados($anio,1);
$graficaAntHab = $obj_class->grafica_anio_anteriorHabilitadas($anio,1);

$graficaHabSemana = $obj_class->graficaHabilitadoSemana($anio,1);
$graficaAntHabSemanaGnrl = $obj_class->grafica_anio_anteriorHabilitadaSemana($anio,1);

//Calculo operado
$graficaDirSemana = $obj_class->graficaDirectaSemana($anio,1);
$grafica_ocupacion = $obj_class->grafica_semana_ocupacion($anio, 1);
$grafica_ocupacion_hab_dir = $obj_class->grafica_semana_hab_directas($anio, 1);
$inv_promedio_semana_zafra = $obj_class->inv_prom_semanal_zafra($anio, 1);
$inv_promedio_semanal_ejercicio = $obj_class->promedio_semanal_ejercicio($anio, 1);
$hist_lineal = $obj_class->histograma_lineal($anio, 1);
$media_histo = $obj_class->media_historica($anio, 1);
$media_histo2 = $obj_class->media_historica2($anio, 1);
$media_histo2020 = $obj_class->media_historica2_2020($anio, 1);

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
        Dashboard
        <small>Azucar Graficas</small>
      </h1>
    </section>
    <section class="content">
      <!-- ############################ SECCION GRAFICA Y WIDGETS ############################# -->
      <section>
        <div class="row">
          <!--<div class="col-md-12">
            <div id="graf_bar"></div>
          </div>-->
          <!--FILTRO POR FECHA -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
              <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
              <a href="azucar_x_anio2.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
              <?php } ?>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body"><!--box-body-->
              <style type="text/css">
                .select2-selection__choice {
                  background: #3c8dbc !important;
                  background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #3c8dbc), color-stop(1, #67a8ce)) !important;
                  background: -ms-linear-gradient(bottom, #3c8dbc, #67a8ce) !important;
                  background: -moz-linear-gradient(center bottom, #1b45cf 0%, #67a8ce 100%) !important;
                  background: -o-linear-gradient(#67a8ce, #3c8dbc) !important;
                  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#67a8ce', endColorstr='#3c8dbc', GradientType=0) !important;
                  color: #fff;
                }
              </style>
              <!-- FILTRAR POR fecha -->
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
                <!--<input type="text" class="form-control pull-right" name="fil_fecha" disabled>-->
                <?php
                echo "<select name='anio' id= 'anio' class='form-control select2'>";
                    for($i=1970;$i<=date("Y");$i++)
                    {
                      if ($i == $anio) {
                        echo "<option value='".$i."' selected>".$i."</option>";
                      }
                        echo "<option value='".$i."'>".$i."</option>";
                    }
                echo "</select>";
                ?>
                <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil"><i class="fa fa-check"></i> Filtrar</button> </span>
              </div>

            </div><!--/.box-body-->
          </div>
          <!--FILTRO POR FECHA -->
          <div class="col-md-3">
          </div>
          <div class="col-md-12">
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
              </div>
              <div class="box-body"><!--box-body-->
                <div class="col-md-12">
                    <!-- <div id="graf_bar_hab_dir" style=" height: 700px;"></div> -->
                     <div id="graf_promedio_semanal"></div>
                     <div id="graf_ejercicio"></div>
                    <!-- <div id="graf_bar_hist_digital"></div> -->
                     <div id="graf_bar"></div>
                      <section id="E">
                        <div class="box box-info">
                          <div class="box-header with-border">
                            <div class="box-tools pull-right">
                              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                          </div>
                          <div class="box-body">
                            <div class="table-responsive">
                              <table id="tabla_ce_cce" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
                              <thead>
                                <tr>
                                  <th class="small">AÑO</th>
                                  <th class="small">ENERO</th>
                                  <th class="small">FEBRERO</th>
                                  <th class="small">MARZO</th>
                                  <th class="small">ABRIL</th>
                                  <th class="small">MAYO</th>
                                  <th class="small">JUNIO</th>
                                  <th class="small">JULIO</th>
                                  <th class="small">AGOSTO</th>
                                  <th class="small">SEPTIEMBRE</th>
                                  <th class="small">OCTUBRE</th>
                                  <th class="small">NOVIEMBRE</th>
                                  <th class="small">DICIEMBRE</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="small"><?= $anio ?></td>
                                  <?php
                                  for ($i=0; $i <count($grafica) ; $i++) {
                                    $anio_actual = date("Y");
                                    $semana_actual = date("m");
                                    //$semana_actual = $semana_actual - 2;
                                    if ($anio == $anio_actual) {
                                      if ($semana_actual-1 == $i) {
                                        $grafica_ocupacion_hab_endweek = $obj_class->lastWeekDirectos();
                                        $valor_real_end_week = round($grafica_ocupacion_hab_endweek[0]["CANTIDADTOTAL"]);
                                        echo "<td class='small'>". number_format($valor_real_end_week, 2)."</td>";
                                        //echo "0.00 ,";
                                      }else {
                                        $valor_real = round($grafica[$i]["VALOR"]);
                                        echo  "<td class='small'>".number_format($valor_real, 2)."</td>";
                                      }
                                    }else {
                                      $valor_real = round($grafica[$i]["VALOR"]);
                                      echo "<td class='small'>".number_format($valor_real, 2)."</td>";
                                    }
                                  }

                                  ?>
                                </tr>
                                <tr>
                                  <td class="small"><?= $anio-1 ?></td>
                                  <?php
                                    for ($i=0; $i < count($graficaAnt) ; $i++) {
                                      $valor_real2 = round($graficaAnt[$i]["VALOR"]);
                                      echo "<td class='small'>".number_format($valor_real2, 2)."</td>";
                                    }
                                  ?>
                                </tr>
                              </tbody>
                              </table>
                            </div>

                          </div>
                        </div>
                      </section>

                     <div id="graf_barHabilitado"></div>

                    <section id="E">
                      <div class="box box-info">
                        <div class="box-header with-border">
                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                          </div>
                        </div>
                        <div class="box-body">
                          <div class="table-responsive">
                            <table id="tabla_ce_cce" class="table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                              <tr>
                                <th class="small">AÑO</th>
                                <th class="small">ENERO</th>
                                <th class="small">FEBRERO</th>
                                <th class="small">MARZO</th>
                                <th class="small">ABRIL</th>
                                <th class="small">MAYO</th>
                                <th class="small">JUNIO</th>
                                <th class="small">JULIO</th>
                                <th class="small">AGOSTO</th>
                                <th class="small">SEPTIEMBRE</th>
                                <th class="small">OCTUBRE</th>
                                <th class="small">NOVIEMBRE</th>
                                <th class="small">DICIEMBRE</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td class="small"><?= $anio ?></td>
                                <?php
                                for ($i=0; $i <count($graficaHab) ; $i++) {
                                  $anio_actual = date("Y");
                                  $semana_actual = date("m");
                                  //$semana_actual = $semana_actual-1;
                                  if ($anio == $anio_actual) {
                                      if ($semana_actual-1 == $i) {
                                        $grafica_ocupacion_hab_endweek = $obj_class->lastWeekHabilitados();
                                        $valor_real_end_week = round($grafica_ocupacion_hab_endweek[0]["CANTIDADTOTAL"]);
                                        echo "<td class='small'>".number_format($valor_real_end_week, 2)."</td>";
                                          //echo "<td class='small'>"."0.00"."</td>";
                                      }else {
                                          $valor_real = round($graficaHab[$i]["VALOR"]);
                                          echo "<td class='small'>".number_format($valor_real, 2)."</td>";;
                                      }
                                  }else {
                                      $valor_real = round($graficaHab[$i]["VALOR"]);
                                      echo "<td class='small'>".number_format($valor_real, 2)."</td>";
                                  }
                                }
                                ?>
                              </tr>
                              <tr>
                                <td class="small"><?= $anio-1 ?></td>
                                <?php
                                  for ($i=0; $i < count($graficaAntHab) ; $i++) {
                                    $valor_real2 = round($graficaAntHab[$i]["VALOR"]);
                                    echo "<td class='small'>".number_format($valor_real2, 2)."</td>";
                                  }
                                ?>
                              </tr>
                            </tbody>
                            </table>
                          </div>

                        </div>
                      </div>
                    </section>


                     <div id="graf_barHabilitadoSemana"></div>
                    <!-- <div id="graf_barHabilitadoSemana2"></div> -->
                </div>
              </div><!--/.box-body-->
            </div>
          </div>
        </div>
      </section>
      <?php
        $valor_semana = 0;
        //echo date("m");
        for ($i=0; $i < COUNT($media_histo2) ; $i++) {
          if ($i == 0) {
            $valor_semana = 38529;
          //  echo "38529</br>";
          }else {
            $y = $i -1;
            $valor_semana = $valor_semana * ($media_histo2[$i]["VALOR"]/ $media_histo2[$y]["VALOR"]);
            //echo  $media_histo2020[0]["VALOR"]. "</br>";
            //echo round($valor_semana, 2)."</br>";
            //echo round($media_histo[$i]["VALOR"]/ $media_histo[$y]["VALOR"], 2).",";
          }
          //echo $media_histo[$i]["VALOR"]. "</br>";
        }
       ?>
    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<script type="text/javascript">


</script>
<script>

$(".btn_fil").on("click", function(){

  fil_anio = $('#anio').val();
  url = '?anio='+fil_anio;
  location.href = url;

});
</script>
<!--ESCROLL TO MVIEW -->

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
<!-- Grafica Barras. -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="../plugins/colorSegment/multicolor_series.js"></script>
<script type="text/javascript">
$(function () {
    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($grafica_ocupacion_hab_dir) ; $i++) {
      echo "'SEMANA ".$grafica_ocupacion_hab_dir[$i]["NO_SEMANA"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($grafica_ocupacion_hab_dir) ; $i++) {
      $anio_actual = date("Y");
      $semana_actual = date("W");
      $semana_actual = $semana_actual - 2;
      if ($anio == $anio_actual) {
        if ($i == $semana_actual + 1) {
          $grafica_ocupacion_hab_endweek = $obj_class->lastWeek();
          $valor_real_end_week = round($grafica_ocupacion_hab_endweek[0]["CANTIDADTOTAL"]);
          echo "$valor_real_end_week ,";
        }elseif($i > $semana_actual +1 and $i <>  count($grafica_ocupacion_hab_dir) - 1 ) {
          if ($i == count($grafica_ocupacion_hab_dir) - 1) {
            $grafica_ocupacion_hab_endweek = $obj_class->lastWeek();
            $valor_real_end_week = round($grafica_ocupacion_hab_endweek[0]["CANTIDADTOTAL"]);
            echo "$valor_real_end_week ,";
          }else {
            $valor_real = round($grafica_ocupacion_hab_dir[$i]["VALOR"]);
            echo $valor_real.",";
          }
        }
        else {
          $valor_real = round($grafica_ocupacion_hab_dir[$i]["VALOR"]);
          echo $valor_real.",";
        }

      }else {
        $valor_real = round($grafica_ocupacion_hab_dir[$i]["VALOR"]);
        echo $valor_real.",";
      }
    }
    ?>
    ];
    var data2= [
      <?php
        $anio2 = $anio -1;
        $grafica_ocupacion_hab_dir2 = $obj_class->grafica_semana_hab_directas($anio2, 1);
        for ($i=0; $i < count($grafica_ocupacion_hab_dir2) ; $i++) {
          $valor_real2 = round($grafica_ocupacion_hab_dir2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    var data3= [
      <?php
        $anio2 = $anio -2;
        $grafica_ocupacion_hab_dir2 = $obj_class->grafica_semana_hab_directas($anio2, 1);
        for ($i=0; $i < count($grafica_ocupacion_hab_dir2) ; $i++) {
          $valor_real2 = round($grafica_ocupacion_hab_dir2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    var data4= [
      <?php
        $anio2 = $anio -3;
        $grafica_ocupacion_hab_dir2 = $obj_class->grafica_semana_hab_directas($anio2, 1);
        for ($i=0; $i < count($grafica_ocupacion_hab_dir2) ; $i++) {
          $valor_real2 = round($grafica_ocupacion_hab_dir2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    var data5= [
      <?php
        $anio2 = $anio -4;
        $grafica_ocupacion_hab_dir2 = $obj_class->grafica_semana_hab_directas($anio2, 1);
        for ($i=0; $i < count($grafica_ocupacion_hab_dir2) ; $i++) {
          $valor_real2 = round($grafica_ocupacion_hab_dir2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    var data6= [
      <?php
        $anio2 = $anio -5;
        $grafica_ocupacion_hab_dir2 = $obj_class->grafica_semana_hab_directas($anio2, 1);
        for ($i=0; $i < count($grafica_ocupacion_hab_dir2) ; $i++) {
          $valor_real2 = round($grafica_ocupacion_hab_dir2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    var data7= [
      <?php
        $anio2 = $anio -6;
        $grafica_ocupacion_hab_dir2 = $obj_class->grafica_semana_hab_directas($anio2, 1);
        for ($i=0; $i < count($grafica_ocupacion_hab_dir2) ; $i++) {
          $valor_real2 = round($grafica_ocupacion_hab_dir2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    var data8= [
      <?php
        $anio2 = $anio -7;
        $grafica_ocupacion_hab_dir2 = $obj_class->grafica_semana_hab_directas($anio2, 1);
        for ($i=0; $i < count($grafica_ocupacion_hab_dir2) ; $i++) {
          $valor_real2 = round($grafica_ocupacion_hab_dir2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    var data9= [
      <?php
        $anio2 = $anio -8;
        $grafica_ocupacion_hab_dir2 = $obj_class->grafica_semana_hab_directas($anio2, 1);
        for ($i=0; $i < count($grafica_ocupacion_hab_dir2) ; $i++) {
          $valor_real2 = round($grafica_ocupacion_hab_dir2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    var data10= [
      <?php
        $anio2 = $anio -9;
        $grafica_ocupacion_hab_dir2 = $obj_class->grafica_semana_hab_directas($anio2, 1);
        for ($i=0; $i < count($grafica_ocupacion_hab_dir2) ; $i++) {
          $valor_real2 = round($grafica_ocupacion_hab_dir2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    $('#graf_bar_hab_dir').highcharts({
        chart: {
            type: 'line',
            zoomType: 'x',
            panning: true,
            panKey: 'shift'
          // zoomType : 'x'
        },
         title: {
            text: 'Inventario en Bodegas Directas y Habilitadas Corte Semanal (Toneladas Metricas) '
        },

        legend: {
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        yAxis: {
            lineWidth: 2,
            min: 0,
            max: 600000,
            startOnTick: false,
            endOnTick: false,
            offset: 10,
            tickWidth: 1,
            title: {
                text: 'TONELADAS'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' TONELADAS',
          useHTML: true,
        },
        lang: {
          printChart: 'Imprimir Grafica',
          downloadPNG: 'Descargar PNG',
          downloadJPEG: 'Descargar JPEG',
          downloadPDF: 'Descargar PDF',
          downloadSVG: 'Descargar SVG',
          contextButtonTitle: 'Exportar grafica',
          viewFullscreen: 'Ver en pantalla completa'
        },
        credits: {
            enabled: false
        },
        colors: ['#0073B7', '#D81B60'],
        plotOptions: {
          series: {
            minPointLength:4
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?anio=<?=$anio?>';
                return '<a>' +this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: ' ',
          align: 'right',
          x: -10,
        },
        series:  [{
        //  showInLegend:false,
            name: 'REAL AÑO  <?php echo $anio; ?>',
            data: data1,
            color: 'red',
        },{
          //showInLegend:false,
          name: 'CONSOLIDADO AÑO  <?php $anioa = $anio-1; echo $anioa;  ?>',
          data: data2,
          color: 'brown',
          marker:{enabled: false}
        },{
          //showInLegend:false,
          color:'orange',
          name: 'CONSOLIDADO AÑO  <?php $anioa = $anio-2; echo $anioa;  ?>',
          data: data3,
          dashStyle: 'ShortDash',
          marker:{enabled: false}
        },{
          //showInLegend:false,
          color:'black',
          name: 'CONSOLIDADO AÑO  <?php $anioa = $anio-3; echo $anioa;  ?>',
          dashStyle: 'ShortDash',
          data: data4,
          marker:{enabled: false}
        },{
          //showInLegend:false,
          name: 'CONSOLIDADO AÑO  <?php $anioa = $anio-4; echo $anioa;  ?>',
          data: data5,
          color: 'orange',
          lineWidth: 5,
          marker:{enabled: false}
        },{
          //showInLegend:false,
          name: 'CONSOLIDADO AÑO  <?php $anioa = $anio-5; echo $anioa;  ?>',
          data: data6,
          color: '#72F45B',
          dashStyle: 'ShortDash',
          marker:{enabled: false}
        },{
          //showInLegend:false,
          name: 'CONSOLIDADO AÑO  <?php $anioa = $anio-6; echo $anioa;  ?>',
          data: data7,
          color: 'green',
          dashStyle: 'ShortDash',
          marker:{enabled: false}
        },{
          //showInLegend:false,
          name: 'CONSOLIDADO AÑO  <?php $anioa = $anio-7; echo $anioa;  ?>',
          data: data8,
          color: '#9307FA',
          dashStyle: 'ShortDash',
          marker:{enabled: false}
        },{
          //showInLegend:false,
          name: 'CONSOLIDADO AÑO  <?php $anioa = $anio-8; echo $anioa;  ?>',
          data: data9,
          color: '#EBFA07',
          dashStyle: 'ShortDash',
          marker:{enabled: false}
        },{
          //showInLegend:false,
          color: 'blue',
          name: 'CONSOLIDADO AÑO  <?php $anioa = $anio-9; echo $anioa;  ?>',
          data: data10,
          marker:{enabled: false}

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
     for ($i=0; $i <count($inv_promedio_semana_zafra) ; $i++) {
      echo "'SEMANA ".$inv_promedio_semana_zafra[$i]["NUMERO_FILA"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($inv_promedio_semana_zafra) ; $i++) {
      $anio_actual = date("Y");
      $semana_actual = date("W");
      $semana_actual = $semana_actual -1 ;
      if ($anio == $anio_actual) {
        if ($semana_actual < $i) {
          echo "0.0".",";
        }else {
          $valor_real = round($inv_promedio_semana_zafra[$i]["VALOR"]);
          echo $valor_real.",";
          //$valor_real = round($inv_promedio_semana_zafra[$i]["VALOR"]);

        }
      }else {
        $valor_real = round($inv_promedio_semana_zafra[$i]["VALOR"]);
        echo $valor_real.",";
      }
    }
    ?>
    ];
    var data2= [
      <?php
        $anio2 = $anio -1;
        $inv_promedio_semana_zafra2 = $obj_class->inv_prom_semanal_zafra($anio2, 1);
        for ($i=0; $i < count($inv_promedio_semana_zafra2) ; $i++) {
          $valor_real2 = round($inv_promedio_semana_zafra2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    var data3= [
      <?php
        $anio3 = 2011;
        $inv_promedio_semana_zafra2 = $obj_class->inv_prom_semanal_zafra2011($anio3, 1);
        for ($i=0; $i < count($inv_promedio_semana_zafra2) ; $i++) {
          $valor_real2 = round($inv_promedio_semana_zafra2[$i]["VALOR"], 2);
          echo $valor_real2.",";
        }
      ?>
    ];

    var data4 = [
      <?php
        $anio3 = 2011;
        $inv_promedio_semana_zafra2 = $obj_class->inv_prom_semanal_zafra2011($anio3, 1);
        $anio2 = $anio;
        $inv_promedio_semana_zafra3 = $obj_class->inv_prom_semanal_zafra($anio2, 1);
        for ($i=0; $i < 52 ; $i++) {
          $valor_real2 = round($inv_promedio_semana_zafra3[$i]["VALOR"] - $inv_promedio_semana_zafra2[$i]["VALOR"], 2);
          echo $valor_real2.",";
        }
      ?>
    ];
    $('#graf_promedio_semanal').highcharts({
        chart: {
            type: 'line'
        },
         title: {
            text: 'Inventario Promedio Semanal En El Periodo De Zafra <?php echo $anio-1; echo "/"; echo $anio; ?>'
        },

        legend: {
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        yAxis: {
            lineWidth: 2,
            //min: 0,
            offset: 10,
            tickWidth: 1,
            title: {
                text: 'TONELADAS'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' TONELADAS',
          useHTML: true,
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
            minPointLength:4
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?anio=<?=$anio?>';
                return '<a>' +this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: ' ',
          align: 'right',
          x: -10,
        },
        series:  [{
        //  showInLegend:false,
            name: 'PROMEDIO AÑO  <?php echo $anio; ?>',
            type: 'column',
            data: data1,
            color: 'red',
        },{
          //showInLegend:false,
          name: 'PROMEDIO AÑO  <?php $anioa = $anio-1; echo $anioa;  ?>',
          data: data2,
          color: 'brown',
          marker:{enabled: false}
        },{
          //showInLegend:false,
          name: 'PROMEDIO AÑO  <?php $anioa =2011; echo $anioa;  ?>',
          data: data3,
          dashStyle: 'LongDash',
          marker:{enabled: false}
        },{
          //showInLegend:false,
          name: 'DIFERENCIA',
          data: data4,
          dashStyle: 'LongDash',
          marker:{enabled: false}
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
     for ($i=0; $i <count($inv_promedio_semanal_ejercicio) ; $i++) {
      echo "'SEMANA ".$inv_promedio_semanal_ejercicio[$i]["NO_SEMANA"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($inv_promedio_semanal_ejercicio) ; $i++) {
      $anio_actual = date("Y");
      $semana_actual = date("W");
      $semana_actual = $semana_actual -1;
      if ($anio == $anio_actual) {
        if ($semana_actual < $i) {
          echo "0.0".",";
        }else {
          $valor_real = round($inv_promedio_semanal_ejercicio[$i]["VALOR"]);
          echo $valor_real.",";
          //$valor_real = round($inv_promedio_semana_zafra[$i]["VALOR"]);

        }
      }else {
        $valor_real = round($inv_promedio_semanal_ejercicio[$i]["VALOR"]);
        echo $valor_real.",";
      }

    }
    ?>
    ];
    var data2= [
      <?php
        for ($i=0; $i < COUNT($media_histo) ; $i++) {
          $valor_rial =round(($media_histo[$i]["VALOR"])/($i +1));
          echo $valor_rial.",";
          //echo $media_histo[$i]["VALOR"]. "</br>";

        }
       ?>

    ];
    var data3= [
      <?php
        $anio2 = 2011;
        $grafica_ocupacion_hab_dir2 = $obj_class->promedio_semanal_ejercicio2011($anio2, 1);
        for ($i=0; $i < count($grafica_ocupacion_hab_dir2) ; $i++) {
          $valor_real2 = round($grafica_ocupacion_hab_dir2[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];

    var data4= [
      <?php
        $valor_semana = 38529;
        $valor_semana2 = 0;
        $valor_autosum = 0;
        for ($i=0; $i < COUNT($media_histo2) ; $i++) {
          if ($i == 0) {
            $valor_semana = $media_histo2020[$i]["VALOR"]/13;
            echo round($valor_semana).",";
            //echo $valor_semana.", ";
          }else {
            $y = $i -1;
            $promedio = $media_histo2[$i]["VALOR"]/ $media_histo2[$y]["VALOR"];
            $promedio2 =  $valor_semana * $promedio;
            $valor_semana = $promedio2;
            $valor_autosum = $valor_autosum + $promedio2;
            echo round(($valor_autosum +  $media_histo2020[0]["VALOR"])/(13+$i) , 2).",";

            //echo round($media_histo[$i]["VALOR"]/ $media_histo[$y]["VALOR"], 2).",";
          }
          //echo $media_histo[$i]["VALOR"]. "</br>";

        }
       ?>
    ];
    $('#graf_ejercicio').highcharts({
        chart: {
            type: 'line'
        },
         title: {
            text: 'Inventario Promedio Semanal En El Ejercicio  <?php echo $anio; ?>'
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
                text: 'TONELADAS'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' TONELADAS',
          useHTML: true,
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
          series: {animation: {
                duration: 2000
            },
            minPointLength:4
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?anio=<?=$anio?>';
                return '<a>' +this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: ' ',
          align: 'right',
          x: -10,
        },
        series:  [{
        //  showInLegend:false,
            name: 'PROMEDIO AÑO  <?php echo $anio; ?>',
            data: data1,
            type: 'column',
            color: 'red',
        },{
          //showInLegend:false,
          name: 'PROMEDIO PROYECTADO <?php echo "$anio"; ?>',
          data: data4,
          marker:{enabled: false}
        },{
          //showInLegend:false,
          name: 'PROMEDIO AÑO  2011',
          data: data3,
          marker:{enabled: false}
        },{
          //showInLegend:false,
          name: 'PROMEDIO MEDIA HISTORICA 4',
          data: data2,
          color: 'brown',
          marker:{enabled: false}
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
     for ($i=0; $i <count($hist_lineal) ; $i++) {
      echo "'FECHA: ".$hist_lineal[$i]["FECHA"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    $mes = 1;
    for ($i=0; $i <count($hist_lineal) ; $i++) {
      $anio_actual = date("Y");
      $semana_actual = date("W");
      $semana_actual = $semana_actual - 2;
      $valor_real = round($hist_lineal[$i]["VALOR_TOTAL"]);
      $mes_actual = date("m");

      if($anio_actual == substr($hist_lineal[$i]["FECHA"], -4)){
          $year = substr($hist_lineal[$i]["FECHA"], -4);
          $month = substr($hist_lineal[$i]["FECHA"], 0, 2);
          $fecha_real =  date("d-m-Y",(mktime(0,0,0,$month+1,1,$year)-1));
          $total_ultimo_mes = $obj_class->lastday($fecha_real);
          $valor_real_end_month = round($total_ultimo_mes[0]["CANTIDADTOTAL"]);
          if ($month == 1 ) {
            echo " {y:".$valor_real_end_month.", color:'blue'}, ";
          }elseif ($month == 2 ) {
            echo " {y:".$valor_real_end_month.", color:'red'}, ";
          }elseif ($month == 3) {
            echo " {y:".$valor_real_end_month.", color:'yellow'}, ";
          }elseif ($month == 4) {
            echo " {y:".$valor_real_end_month.", color:'black'}, ";
          }elseif ($month == 5) {
            echo " {y:".$valor_real_end_month.", color:'brown'}, ";
          }elseif ($month == 6) {
            echo " {y:".$valor_real_end_month.", color:'orange'}, ";
          }elseif ($month == 7) {
            echo " {y:".$valor_real_end_month.", color:'purple'}, ";
          }elseif ($month == 8) {
            echo " {y:".$valor_real_end_month.", color:'pink'}, ";
          }elseif ($month == 9) {
            echo " {y:".$valor_real_end_month.", color:'violet'}, ";
          }elseif ($month == 10) {
            echo " {y:".$valor_real_end_month.", color:'golden'}, ";
          }elseif ($month == 11) {
            echo " {y:".$valor_real_end_month.", color:'#A238E0'}, ";
          }elseif ($month == 12) {
            echo " {y:".$valor_real_end_month.", color:'magenta'}, ";
          }
      }else {
        if ($i == count($hist_lineal) - 1) {
          $grafica_ocupacion_hab_endweek = $obj_class->lastWeek();
          $valor_real_end_week = round($grafica_ocupacion_hab_endweek[0]["CANTIDADTOTAL"]);
          //echo "$valor_real_end_week ,";
          if ($mes == 1 ) {
            echo " {y:".$valor_real_end_week.", color:'blue'}, ";
            $mes = 2;
          }elseif ($mes == 2 ) {
            echo " {y:".$valor_real_end_week.", color:'red'}, ";
            $mes = 3;
          }elseif ($mes == 3) {
            echo " {y:".$valor_real_end_week.", color:'yellow'}, ";
            $mes = 4;
          }elseif ($mes == 4) {
            echo " {y:".$valor_real_end_week.", color:'black'}, ";
            $mes = 5;
          }elseif ($mes == 5) {
            echo " {y:".$valor_real_end_week.", color:'brown'}, ";
            $mes = 6;
          }elseif ($mes == 6) {
            echo " {y:".$valor_real_end_week.", color:'orange'}, ";
            $mes = 7;
          }elseif ($mes == 7) {
            echo " {y:".$valor_real_end_week.", color:'purple'}, ";
            $mes = 8;
          }elseif ($mes == 8) {
            echo " {y:".$valor_real_end_week.", color:'pink'}, ";
            $mes = 9;
          }elseif ($mes == 9) {
            echo " {y:".$valor_real_end_week.", color:'violet'}, ";
            $mes = 10;
          }elseif ($mes == 10) {
            echo " {y:".$valor_real_end_week.", color:'golden'}, ";
            $mes = 11;
          }elseif ($mes == 11) {
            echo " {y:".$valor_real_end_week.", color:'#A238E0'}, ";
            $mes = 12;
          }elseif ($mes == 12) {
            echo " {y:".$valor_real_end_week.", color:'magenta'}, ";
            $mes = 1;
          }
        }else {
          if ($mes == 1 ) {
            echo " {y:".$valor_real.", color:'blue'}, ";
            $mes = 2;
          }elseif ($mes == 2 ) {
            echo " {y:".$valor_real.", color:'red'}, ";
            $mes = 3;
          }elseif ($mes == 3) {
            echo " {y:".$valor_real.", color:'yellow'}, ";
            $mes = 4;
          }elseif ($mes == 4) {
            echo " {y:".$valor_real.", color:'black'}, ";
            $mes = 5;
          }elseif ($mes == 5) {
            echo " {y:".$valor_real.", color:'brown'}, ";
            $mes = 6;
          }elseif ($mes == 6) {
            echo " {y:".$valor_real.", color:'orange'}, ";
            $mes = 7;
          }elseif ($mes == 7) {
            echo " {y:".$valor_real.", color:'purple'}, ";
            $mes = 8;
          }elseif ($mes == 8) {
            echo " {y:".$valor_real.", color:'pink'}, ";
            $mes = 9;
          }elseif ($mes == 9) {
            echo " {y:".$valor_real.", color:'violet'}, ";
            $mes = 10;
          }elseif ($mes == 10) {
            echo " {y:".$valor_real.", color:'golden'}, ";
            $mes = 11;
          }elseif ($mes == 11) {
            echo " {y:".$valor_real.", color:'#A238E0'}, ";
            $mes = 12;
          }elseif ($mes == 12) {
            echo " {y:".$valor_real.", color:'magenta'}, ";
            $mes = 1;
          }
        }
    }
    //  echo " {y:".$valor_real.", segmentColor:'green'}, ";

    }
    ?>
    ];

    $('#graf_bar_hist_digital').highcharts({
        chart: {
            type: 'line'
        },
         title: {
            text: 'Histograma Lineal'
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
                text: 'TONELADAS'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' TONELADAS',
          useHTML: true,
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
            minPointLength:4
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,

          labels: {
            type: 'category',
            labels:{
              step:3
            },
            formatter: function () {
              url = '?anio=<?=$anio?>';
                return '<a>' +this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: ' ',
          align: 'right',
          x: -10,
        },
        series:  [{
            showInLegend:false,
          //  name: 'AÑO  <?php echo $anio; ?>',
            data: data1,
            //color: 'red',
            marker:{enabled: true}
        }]

    });
});
</script>
<!--PRUEBA DE GRAFICAS MOSTRAR DEPENDIENDO LA SEMANA -->

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
      echo "'".$grafica[$i]["MES"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($grafica) ; $i++) {
      $anio_actual = date("Y");
      $semana_actual = date("m");
      //$semana_actual = $semana_actual - 2;
      if ($anio == $anio_actual) {
        if ($semana_actual-1 == $i) {
          $grafica_ocupacion_hab_endweek = $obj_class->lastWeekDirectos();
          $valor_real_end_week = round($grafica_ocupacion_hab_endweek[0]["CANTIDADTOTAL"]);
          echo "$valor_real_end_week ,";
          //echo "0.00 ,";
        }else {
          $valor_real = round($grafica[$i]["VALOR"]);
          echo $valor_real.",";
        }
      }else {
        $valor_real = round($grafica[$i]["VALOR"]);
        echo $valor_real.",";
      }
    }
    ?>
    ];
    var data2= [
      <?php
        for ($i=0; $i < count($graficaAnt) ; $i++) {
          $valor_real2 = round($graficaAnt[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    $('#graf_bar').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'Inventarios Mensuales Comparativo de Bodegas Directas  '
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
                text: 'TONELADAS'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' TONELADAS',
          useHTML: true,
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
            minPointLength:3
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?anio=<?=$anio?>';
                return '<a>' +this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: ' ',
          align: 'right',
          x: -10,
        },
        series:  [{
        //  showInLegend:false,
            name: 'AÑO  <?php echo $anio; ?>',
            data: data1,
        },{
          //showInLegend:false,
          name: 'AÑO  <?php $anioa = $anio-1; echo $anioa;  ?>',
          data: data2,
        }]

    });
});
</script>
<!-- DataTables -->
<!--Habilitadas graficas-->
<script type="text/javascript">
$(function () {
    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($graficaHab) ; $i++) {
      echo "'".$graficaHab[$i]["MES"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaHab) ; $i++) {
      $anio_actual = date("Y");
      $semana_actual = date("m");
      //$semana_actual = $semana_actual - 2;
      if ($anio == $anio_actual) {
        if ($semana_actual-1 == $i) {
          $grafica_ocupacion_hab_endweek = $obj_class->lastWeekHabilitados();
          $valor_real_end_week = round($grafica_ocupacion_hab_endweek[0]["CANTIDADTOTAL"]);
          echo "$valor_real_end_week ,";
          //echo "0.00 ,";
        }else {
          $valor_real = round($graficaHab[$i]["VALOR"]);
          echo $valor_real.",";
        }
      }else {
        $valor_real = round($graficaHab[$i]["VALOR"]);
        echo $valor_real.",";
      }
    }
    ?>
    ];
    var data2= [
      <?php
        for ($i=0; $i < count($graficaAntHab) ; $i++) {
          $valor_real2 = round($graficaAntHab[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    $('#graf_barHabilitado').highcharts({
        chart: {
            type: 'column'
        },
         title: {
            text: 'Inventario Mensual Comparativo de Bodegas Habilitadas '
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
                text: 'TONELADAS'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' TONELADAS',
          useHTML: true,
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
            minPointLength:3
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?anio=<?=$anio?>';
                return '<a>' +this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: ' ',
          align: 'right',
          x: -10,
        },
        series:  [{
        //  showInLegend:false,
            name: 'AÑO  <?php echo $anio; ?>',
            data: data1,
        },{
          //showInLegend:false,
          name: 'AÑO  <?php $anioa = $anio-1; echo $anioa;  ?>',
          data: data2,
        }]

    });
});
</script>
<!--GRAFICAS POR SEMANA-->
<script type="text/javascript">


$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($graficaHabSemana) ; $i++) {
      echo "'SEMANA ".$graficaHabSemana[$i]["NO_SEMANA"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaHabSemana) ; $i++) {
      $anio_actual = date("Y");
      $semana_actual = date("W");
      $semana_actual = $semana_actual - 1;
      #echo $semana_actual;
      if ($anio == $anio_actual) {
        if ($i < $semana_actual) {
          $valor_real = round($graficaHabSemana[$i]["VALOR"]);
          echo $valor_real.",";
        }
        elseif ($semana_actual == $i) {
          $valor_real = 0;
          $grafica_ocupacion_hab_endweek_hab = $obj_class->lastWeekHabilitados();
          $valor_real = round($grafica_ocupacion_hab_endweek_hab[0]["CANTIDADTOTAL"]);
          echo "$valor_real ,";
        }elseif ( $i > $semana_actual) {
          echo "0 ,";
        }
      }else {
        $valor_real = round($graficaHabSemana[$i]["VALOR"]);
        echo $valor_real.",";
      }
    }
    ?>
    ];
    var data2= [
      <?php
        for ($i=0; $i < count($graficaAntHabSemanaGnrl) ; $i++) {
          $valor_real2 = round($graficaAntHabSemanaGnrl[$i]["VALOR"]);
          echo $valor_real2.",";
        }
      ?>
    ];
    $('#graf_barHabilitadoSemana').highcharts({
        chart: {
            type: 'line'
        },
         title: {
            text: 'Inventario Semanal Comparativo de Bodegas Habilitadas '
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
                text: 'TONELADAS'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' TONELADAS',
          useHTML: true,
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
            minPointLength:3
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?anio=<?=$anio?>';
                return '<a>' +this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: ' ',
          align: 'right',
          x: -10,
        },
        series:  [{
        //  showInLegend:false,
            name: 'AÑO  <?php echo $anio; ?>',
            data: data1,
        },{
          //showInLegend:false,
          name: 'AÑO  <?php $anioa = $anio-1; echo $anioa;  ?>',
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
      $anio_actual = date("Y");
      $semana_actual = date("W");
      $semana_actual = $semana_actual - 2;
      if ($anio == $anio_actual) {
          $semana = date("W", strtotime($anio));
          $semana = $semana  ;
          $semana = 52;
      }else {
        $semana = 52;
      }
     for ($i=0; $i < $semana ; $i++) {
      echo "'SEMANA ".$graficaDirSemana[$i]["NO_SEMANA"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    $anio_actual = date("Y");
    $semana_actual = date("W");
    $semana_actual = $semana_actual - 2;
    if ($anio == $anio_actual) {
        $semana = date("W", strtotime($anio));
        $semana = $semana ;
    }else {
      $semana = 52;
    }
    for ($i=0; $i < $semana ; $i++) {
      $anio_actual = date("Y");
      $semana_actual = date("W");
      $semana_actual = $semana_actual - 2;
      $valor_real = round($graficaDirSemana[$i]["VALOR"]);

      if ($semana-1 == $i ) {
        $grafica_ocupacion_hab_endweek = $obj_class->lastWeek();
        $valor_real_end_week = round($grafica_ocupacion_hab_endweek[0]["CANTIDADTOTAL"]);
        echo "$valor_real_end_week ,";
      }else {
          echo $valor_real.",";
      }

    }
    ?>
    ];
    var data2= [
      <?php
      $valorAcumulado = 0;
      $anio_actual = date("Y");
      $semana_actual = date("W");
      $semana_actual = $semana_actual - 2;
      $valor_weeek = $obj_class->lastWeek();
      $graficaDirSemana = $obj_class->graficaDirectaSemana($anio, 1);
      $grafica_ocupacionant = $obj_class->grafica_semana_ocupacion($anio, 1);
      if ($anio == $anio_actual) {
          $semana = date("W", strtotime($anio));
          $semana = $semana ;
      }else {
        $semana = 52;
      }
        for ($i=0; $i < $semana ; $i++) {
          if ($i == $semana-1 ) {
            $valorAcumulado += $grafica_ocupacion[$i]["ENTRIES"];
            //$valorAcumulado = 0;
            //$valor_real2 = $valor_weeek[0]["CANTIDADTOTAL"];
            if ($graficaDirSemana[$i]["VALOR"] == 0) {
                $valor_real2 = round($valor_weeek[0]["CANTIDADTOTAL"]+$valorAcumulado);
            }else {
                $valor_real2 = round($graficaDirSemana[$i]["VALOR"]+$valorAcumulado);
            }
            echo $valor_real2.",";
          }else {
            $valorAcumulado += $grafica_ocupacion[$i]["ENTRIES"];
            $valor_real2 = $graficaDirSemana[$i]["VALOR"]+$valorAcumulado;
            echo "$valor_real2 , ";
          }

        }
      ?>
    ];

    var data3= [
      <?php
      $valorAcumulado = 0;
      $anio_actual = date("Y");
      $semana_actual = date("W");
      $semana_actual = $semana_actual - 2;
      $ani2 = $anio -1;
      $grafica_ocupacionant = $obj_class->grafica_semana_ocupacion($ani2, 1);
      $graficaDirSemanaant = $obj_class->graficaDirectaSemana($ani2, 1);
      $valor_weeek = $obj_class->lastWeek();
      if ($anio == $anio_actual) {
          $semana = date("W", strtotime($anio));
          $semana = $semana ;
      }else {
        $semana = 52;
      }
        for ($i=0; $i < $semana ; $i++) {
          $valorAcumulado += $grafica_ocupacionant[$i]["ENTRIES"];
          if ($graficaDirSemanaant[$i]["VALOR"] == 0) {
            $valor_real2 = round($valor_weeek+$valorAcumulado);
          }
          else {
            $valor_real2 = round($graficaDirSemanaant[$i]["VALOR"]+$valorAcumulado);
          }
          echo $valor_real2.",";
        }
      ?>
    ];
    $('#graf_barHabilitadoSemana2').highcharts({
        chart: {
            type: 'line'
        },
         title: {
            text: 'Volumen Operado Año <?php echo $anio; ?> Vs Año <?php echo $anio -1; ?>'
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
                text: 'TONELADAS'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' TONELADAS',
          useHTML: true,
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
            minPointLength:3
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?anio=<?=$anio?>';
                return '<a>' +this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: ' ',
          align: 'right',
          x: -10,
        },
        series:  [{
          //showInLegend:false,
          type: 'spline',
          name: 'VOLUMEN OPERADO <?php echo $anio; ?>',
          data: data2,
        },{
          //showInLegend:false,
          type: 'spline',
          name: 'VOLUMEN OPERADO <?php echo $anio-1; ?>',
          data: data3,
        }]

    });
});
</script>

<script>

window.onload = function(){
  //alert("AVISO");
  var elemnt = document.getElementById("graf_bar_hab_dir");
  //Window.location.hash='#graf_bar_hab_dir';
  elemnt.scrollIntoView();
};

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

<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<!-- Inicia FancyBox JS -->
  <!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript" src="../plugins/fancybox/lib/jquery.mousewheel.pack.js?v=3.1.3"></script>
  <!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="../plugins/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
  <!-- Add Button helper (this is optional) -->
<script type="text/javascript" src="../plugins/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
  <!-- Add Thumbnail helper (this is optional) -->
<script type="text/javascript" src="../plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
<!-- <script type="text/javascript">
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
  </script> -->
<!-- Termina FancyBox JS -->
<!-- PACE -->
<script src="../plugins/pace/pace.min.js"></script>
</html>
<?php conexion::cerrar($conn); ?>
