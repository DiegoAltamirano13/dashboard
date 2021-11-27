<?php

if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
  header("location:sgc.php");
}
session_start();

  if(!isset($_SESSION['usuario']))                                              //COMPROBAR SESSION INICIADA
    header('Location: ../index.php');

  $now = time();
  if($now > $_SESSION['expira']){                                               //COMPROBAR TIEMPO DE EXPIRACION
    session_destroy();
    header('Location: ../index.php');
  }

include_once '../libs/conOra.php';                                              //CONEXION A LA BD
$conn   = conexion::conectar();

function autoload($clase){                                                      //INICIO DE AUTOLOAD
  include "../class/" . $clase . ".php";
}
spl_autoload_register('autoload');

$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 56);        //VALIDACION DEL MODULO ASIGNADO
if($modulos_valida == 0){
  header('Location: index.php');
}

include '../class/Sgc.php';
$ModuloGestCal=new Sgc();                                                       //LLAMADA AL MODELO

$fechaInput=$ModuloGestCal->ObtenerFecha();                                     //OBTENER RANGO DE FECHAS DESDE SQL
$fechaInicio = $fechaInput[0]["MES1"];
$fechaFin    = $fechaInput[0]["MES2"];

if( isset($_GET["fecha"]) ){                                                    //OBTENER FECHAS DE URL (EN CASO DE QUE NO VENGAN EN LA URL SE TOMAN LOS DE LA BD)
  if ( $ModuloGestCal->validateDate(substr($_GET["fecha"],0,10)) AND $ModuloGestCal->validateDate(substr($_GET["fecha"],11,10)) ){
    $fechaInicio = substr($_GET["fecha"],0,10);
    $fechaFin=substr($_GET["fecha"],11,10);
  }else{
    $fechaInicio = $fechaInput[0]["MES1"]; $fechaFin=$fechaInput[0]["MES2"];
  }
}

$fil_habilitado = "ALL";                                                        //OBTENER EL STATUS DEL FILTRO
if (isset($_GET["fil_habilitado"])) {
  $fil_habilitado = $_GET["fil_habilitado"];
}

$graficaTotalSgc=$ModuloGestCal->grafica_total_sgc($fechaInicio, $fechaFin);    //LLAMANDO A LAS CONSULTAS PARA OBTENER GRAFICAS
$graficaAbiertosPlaza=$ModuloGestCal->grafica_abiertos_plaza($fechaInicio, $fechaFin);
$graficaCerradosPlaza=$ModuloGestCal->grafica_cerrados_plaza($fechaInicio, $fechaFin);
$graficaAbiertosProceso=$ModuloGestCal->grafica_procesos_abiertos($fechaInicio, $fechaFin);
$graficaFechasPlanAccion=$ModuloGestCal->grafica_plan_accion($fechaInicio, $fechaFin);
$graficaFechasCierre=$ModuloGestCal->grafica_fecha_cierre($fechaInicio, $fechaFin);
$graficaFechasPlanAccionAbiertos=$ModuloGestCal->grafica_plan_accion_abiertos($fechaInicio, $fechaFin);

$descargarExcel=$ModuloGestCal->crearExcel($fechaInicio, $fechaFin);

if (isset($_GET["download_xls"])){
$ModuloGestCal->exportar($descargarExcel, $fechaInicio, $fechaFin);
}

?>

<?php include_once('../layouts/plantilla.php'); ?>                              <!--INCLUIR PLANTILLA PHP-->

<link rel="stylesheet" href="../plugins/select2/select2.min.css">               <!--ESTILOS-->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<link rel="../plugins/daterangepicker/daterangepicker.css">

<div class="content-wrapper">                                                   <!--INICIA PLANTILLA SGC ACCIONES CORRECTIVAS-->
  <section class="content-header">
    <h1>Dashboard<small>RESUMEN GENERAL DE SACP</small></h1>
  </section>

<section class="content">

  <section>                                                                     <!--INICIA PLANTILLA SGC ACCIONES CORRECTIVAS TOTALES WIDGETS-->
 <div class="row">

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="small-box bg-morado">
      <div class="inner">
        <h3 class="text-center"><?= $graficaTotalSgc[0]["TODOS"] ?></h3>
        <center><b>TOTAL DE SACP REGISTRADOS</b></centaer>
        <br><br>
      </div>
      <div class="icon">
        <i class="ion ion-loop"></i>
      </div>
      <a onclick="visualizar(1);" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="small-box bg-info">
      <div class="inner">
        <h3 class="text-center"><?= $graficaTotalSgc[0]["CERRADOS"] ?></h3>
        <center><b>SACP CERRADOS</b></center>
        <br>
        <div class="icon">
          <i class="fa fa-check-square"></i>
        </div>
      </div>
      <a onclick="visualizar(2);" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="small-box bg-verde">
      <div class="inner">
        <h3 class="text-center"><?= $graficaTotalSgc[0]["ABIERTOS"] ?></h3>
        <center><b>SACP ABIERTOS </b></center>
        <br>
        <div class="icon">
          <i class="ion ion-alert-circled"></i>
        </div>
      </div>
      <a onclick="visualizar(3);" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-md-3">                                                        <!-- FILTROS-->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros </h3>
        <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="sgc.php"><button class="btn btn-sm btn-warning">Borrar Filtro <i class="fa fa-close"></i></button></a>
        <?php } ?>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body">
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
          <input type="text" class="form-control pull-right" name="dateFilter">
        </div>
        <div class="input-group">
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>                                                                  <!-- ENLACE PARA DESCARGAR EXCEL CON DETALLE GENERAL-->
        <?php
        echo "<b><a href=sgc.php?fecha=".$fechaInicio."-".$fechaFin."&fil_habilitado".$fil_habilitado."&download_xls=1>DESCARGAR EXCEL GRAL.</a><b>";
        ?>
      </div>
    </div>
  </div>



 </div>
</section>

  <section>                                                                     <!--INICIAN GRAFICAS-->
      <div class="row">

        <div class="col-md-9" id="g1" style="display: ">                         <!--GRAFICA # 1 TOTAL DE SACP-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP REGISTRADOS</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <?php
              if($graficaTotalSgc[0]["TODOS"]!=0){
                 $total=$graficaTotalSgc[0]["TODOS"];
                 $totalAb=$graficaTotalSgc[0]["ABIERTOS"];
                 $totalCe=$graficaTotalSgc[0]["CERRADOS"];
                 $totalCerrados=round((($totalCe*100)/$total),2);
                 $totalAbiertos=round((($totalAb*100)/$total),2);
               }else{
                echo "<center><h4>No se encontraron SACP Registrados</h4></center>";
               }
              ?>
              <div id="graf_sacp1" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g2" style="display: ">                        <!--GRAFICA # 5 TOTAL DE SACP Y TIEMPO TRANSCURRIDO ENTRE FECHA DE SACP Y FECHA DE PLAN DE ACCION-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP REGISTRADOS</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
            <?php
                if($graficaTotalSgc[0]["TODOS"]!=0){
                  $mayorY=$ModuloGestCal->ObtenerMax(5,$fechaInicio, $fechaFin);
                  $y5=$mayorY[0]["VMAX"];
                  $mayorX=$mayorY[0]["CREG"];
                    if($mayorX>20){
                      $x5=19;
                    }else{
                      $x5=$mayorX-1;
                    }
                }else{
                 echo "<center><h4>No se encontraron SACP Registrados</h4></center>";
                }
               ?>
              <div id="graf_sacp5" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g3" style="display: none">                    <!--GRAFICA # 2 TOTAL DE SACP ABIERTOS POR PLAZA-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP ABIERTOS POR PLAZA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <?php
                if($graficaTotalSgc[0]["ABIERTOS"]!=0){
                    $porcentajeAbiertos= array();
                    for ($x=0; $x<count($graficaAbiertosPlaza); $x++) {
                          $Abiertos=$graficaTotalSgc[0]["ABIERTOS"];
                          $totalAbiertosPlaza=round((($graficaAbiertosPlaza[$x]["SACP_ABIERTOS"]*100)/$graficaTotalSgc[0]["ABIERTOS"]),2);
                          $porcentajeAbiertos[$x]=$totalAbiertosPlaza;
                     }
                }else{
                  echo "<center><h4>No se encontraron SACP Abiertos</h4></center>";
                }
              ?>
              <div id="graf_sacp2" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g4" style="display: none">                    <!--GRAFICA # 4 TOTAL DE SACP ABIERTOS POR PROCESO-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP ABIERTOS POR PROCESO</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <?php
                if($graficaTotalSgc[0]["ABIERTOS"]!=0){
                    $porcentajeProceso= array();
                    for ($x=0; $x<count($graficaAbiertosProceso); $x++) {
                          $totalProceso=round((($graficaAbiertosProceso[$x]["SACP_ABIERTOS"]*100)/$graficaTotalSgc[0]["ABIERTOS"]),2);
                          $porcentajeProceso[$x]=$totalProceso;
                    }
                }else{
                  echo "<center><h4>No se encontraron SACP abiertos</h4></center>";
                }
              ?>
              <div id="graf_sacp4" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g5" style="display: none">                    <!--GRAFICA # 6 TOTAL DE SACP ABIERTOS Y TIEMPO TRANSCURRIDO ENTRE FECHA DE SACP Y FECHA DE PLAN DE ACCION-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP ABIERTOS</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <?php
                if($graficaTotalSgc[0]["ABIERTOS"]!=0){
                    $mayorY=$ModuloGestCal->ObtenerMax(6,$fechaInicio, $fechaFin);
                    $y6=$mayorY[0]["VMAX"];
                    $mayorX=$mayorY[0]["CREG"];

                    if($mayorX>20){
                       $x6=19;
                    }else{
                       $x6=$mayorX-1;
                    }
                }else{
                  echo "<center><h4>No se encontraron SACP abiertos</h4></center>";
                }
               ?>
              <div id="graf_sacp6" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g6" style="display: none">                    <!--GRAFICA # 3 TOTAL DE SACP CERRADOS POR PLAZA-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP CERRADOS POR PLAZA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <?php
                if($graficaTotalSgc[0]["CERRADOS"]!=0){
                    $Cerrados=$graficaTotalSgc[0]["CERRADOS"];
                    $porcentajeCerrados= array();
                    for ($x=0; $x<count($graficaCerradosPlaza); $x++) {
                        $totalCerradosPlaza=round((($graficaCerradosPlaza[$x]["SACP_CERRADOS"]*100)/$graficaTotalSgc[0]["CERRADOS"]),2);
                        $porcentajeCerrados[$x]=$totalCerradosPlaza;
                    }
                }else{
                  echo "<center><h4>No se encontraron SACP cerrados</h4></center>";
                }
              ?>
              <div id="graf_sacp3" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g7" style="display: none">                    <!--GRAFICA # 7 TOTAL DE SACP CERRADOS Y TIEMPO TRANSCURRIDO ENTRE FECHA DE SACP Y FECHA DE CIERRE-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP CERRADOS</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <?php
              if($graficaTotalSgc[0]["CERRADOS"]!=0){
                  $mayorY=$ModuloGestCal->ObtenerMax(7,$fechaInicio, $fechaFin);
                  $y7=$mayorY[0]["VMAX"];
                  $mayorX=$mayorY[0]["CREG"];

                  if($mayorX>20){
                     $x7=19;
                  }else{
                     $x7=$mayorX-1;
                  }
              }else{
                echo "<center><h4>No se encontraron SACP cerrados</h4></center>";
              }
             ?>
            <div class="box-body">
              <div id="graf_sacp7" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

  </section>
</section>
</div>

<?php include_once('../layouts/footer.php'); ?>                                 <!--INCLUIR PLANTILLA PHP-->

<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>                   <!--SCRIPT-->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script src="../plugins/fastclick/fastclick.js"></script>
<script src="../dist/js/app.min.js"></script>
<script src="../dist/js/demo.js"></script>
<script src="../plugins/select2/select2.full.min.js"></script>

<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/stock.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<script src="../plugins/flot/jquery.flot.min.js"></script>

<script src="../plugins/flot/jquery.flot.pie3d.js"></script>
<script src="../plugins/flot/jquery.flot.resize.min.js"></script>
<script src="../plugins/flot/jquery.flot.pie_old.js"></script>
<script src="../plugins/flot/jquery.flot.categories.js"></script>
<script src="../plugins/flot/jquery.flot.orderBars.js"></script>
<script src="../plugins/flot/jquery.flot.tooltip.js"></script>

<script src="../plugins/daterangepicker/moment.min.js"></script>
<script src="../plugins/daterangepicker/moment.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>


<script>                                                                        /*---- EVENTO BOTON FILTRAR ----*/
  $(".btnNomFiltro").on("click", function(){
  fecha = $('input[name="dateFilter"]').val();
  fil_habilitado = 'off';

  if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?fecha='+fecha+'&fil_habilitado='+fil_habilitado;
  }
  else {
    fil_habilitado = 'off';
    url = '?fecha='+fecha+'&fil_habilitado='+fil_habilitado;
  }
  location.href = url;
});
</script>

<script>                                                                        /*---- MOSTRAR CALENDARIO ----*/
$(function() {
  $('input[name="dateFilter"]').daterangepicker(
    {
    startDate:'<?=$fechaInicio?>',
    endDate:'<?=$fechaFin?>',
     opens: 'left',
    "locale":{ "format": "DD/MM/YYYY",
    "separator": "-",
    "applyLabel": "Aplicar",
    "cancelLabel": "Cancelar",
    "daysOfWeek": ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
    "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
  }
  },
   function(start, end, label) {
    console.log("Fecha: " + start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY'));
  });
});
</script>

<script>
function visualizar(id){                                                        /*---- MOSTRAR GRAFICAS POR SECCION ----*/

  switch (id) {
    case 1://DETALLE GENERAL DE SACP
      document.getElementById("g1").style.display="";
      document.getElementById("g2").style.display="";
      document.getElementById("g3").style.display="none";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="none";
      document.getElementById("g6").style.display="none";
      document.getElementById("g7").style.display="none";
      break;
    case 2://SACP CERRADOS
      document.getElementById("g1").style.display="none";
      document.getElementById("g2").style.display="none";
      document.getElementById("g3").style.display="none";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="none";
      document.getElementById("g6").style.display="";
      document.getElementById("g7").style.display="";
      break;
    case 3:// SACP ABIERTOS
      document.getElementById("g1").style.display="none";
      document.getElementById("g2").style.display="none";
      document.getElementById("g3").style.display="";
      document.getElementById("g4").style.display="";
      document.getElementById("g5").style.display="";
      document.getElementById("g6").style.display="none";
      document.getElementById("g7").style.display="none";
        break;
    default://DETALLE GENERAL DE SACP
      document.getElementById("g1").style.display="";
      document.getElementById("g2").style.display="";
      document.getElementById("g3").style.display="none";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="none";
      document.getElementById("g6").style.display="none";
      document.getElementById("g7").style.display="none";
  }
}
</script>

<script>                                                                        /* DIBUJANDO TABLA # 1 DETALLE GENERAL DE SACP */
Highcharts.chart('graf_sacp1', {
  colors: ['#8FCEEA','#1FBC0C','#EA856F'],
    chart: {
        type: 'pie'
    },
    title: {
        text: 'SACP REGISTRADOS DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
    },

    accessibility: {
        announceNewData: {
            enabled: true
        },
        point: {
            valueSuffix: '%'
        }
    },

    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: 'SACP {point.name}: {point.y:.2f}% '
            },
       }
    },

    tooltip: {
        headerFormat: '<span style="font-size:13px">{series.name}</span><br>',
        pointFormat:  '<span style="font-size:13px; ">{point.name}</span>: <b>{point.y:.2f}%</b><br/> <span style="font-size:13px; ">TOTAL: </span> <b>{point.totalCat}</b>',
    },
    credits: {
    enabled: false
  },

    series: [
        {
            name: "SACP",
            colorByPoint: true,
            data: [
                {
                    name: "CERRADOS",
                    y: <?=$totalCerrados?>,
                    totalGral: <?=$total?>,
                    totalCat: <?=$totalCe?>,
                },
                {
                    name: "ABIERTOS",
                    y: <?=$totalAbiertos?>,
                    totalGral: <?=$total?>,
                    totalCat: <?=$totalAb?>,
                }
            ]
        }
    ]
});
</script>

<script>                                                                        /* DIBUJANDO TABLA # 2 SACP ABIERTOS POR PLAZA */
var data1 = [
<?php for ($i=0; $i <count($graficaAbiertosPlaza) ; $i++) {  ?>
{
  name: "<?=$graficaAbiertosPlaza[$i]["PLAZA"]?>",
  y: <?=$porcentajeAbiertos[$i]?>,
  totalCat: <?=$graficaAbiertosPlaza[$i]["SACP_ABIERTOS"]?>
},
<?php }  ?>
];

Highcharts.chart('graf_sacp2', {
  colors: ['#1FBC0C'],
    chart: {
        type: 'column'
    },
    title: {
        text: 'SACP ABIERTOS POR PLAZA  DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
      type: 'category',
      title:{
        text: 'PLAZAS',
      }

    },
    yAxis: {
        title: {
            text: '% SACP ABIERTOS'
        }
    },
    legend: {
      enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:,.2f}%',
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:13px">{series.name}</span><br>',
        pointFormat: '<span style="font-size:13px; color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/> TOTAL: <b>{point.totalCat}</b>'

    },
    credits: {
    enabled: false
  },

    series: [
      {
        type: 'column',
        name : 'SACP ABIERTOS',
        data: data1,
      },
    ]
});
</script>

<script>                                                                        /* DIBUJANDO TABLA # 3 SACP CERRADOS POR PLAZA */

var data1 = [
<?php for ($i=0; $i <count($graficaCerradosPlaza) ; $i++) {  ?>
{
  name: "<?=$graficaCerradosPlaza[$i]["PLAZA"]?>",
  y: <?=$porcentajeCerrados[$i]?>,
  totalCat: <?=$graficaCerradosPlaza[$i]["SACP_CERRADOS"]?>
},
<?php }  ?>
];

Highcharts.chart('graf_sacp3', {
  colors: ['#8FCEEA'],
    chart: {
        type: 'column'
    },
    title: {
        text: 'SACP CERRADOS POR PLAZA  DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
      type: 'category',
      title:{
        text: 'PLAZAS',
      }

    },
    yAxis: {
        title: {
            text: '% SACP CERRADOS'
        }
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:,.2f}%'
            }
        }
    },

    tooltip: {
      headerFormat: '<span style="font-size:13px">{series.name}</span><br>',
      pointFormat: '<span style="font-size:13px; color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/> TOTAL: <b>{point.totalCat}</b>'

    },
    credits: {
    enabled: false
    },

    series: [
      {
        type: 'column',
        name : 'SACP CERRADOS',
        data: data1,
      },
    ]
});
</script>

<script>                                                                        /* DIBUJANDO TABLA # 4 SACP ABIERTOSS POR PROCESO */

var data1 = [
<?php for ($i=0; $i <count($graficaAbiertosProceso) ; $i++) {  ?>
{
  name: "<?=$graficaAbiertosProceso[$i]["PROCESO"]?>",
  y: <?=$porcentajeProceso[$i]?>,
  totalCat: <?=$graficaAbiertosProceso[$i]["SACP_ABIERTOS"]?>
},
<?php }  ?>
];

Highcharts.chart('graf_sacp4', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'SACP ABIERTOS POR PROCESO  DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
      type: 'category',
      title:{
        text: 'PROCESOS',
      }

    },
    yAxis: {
        title: {
            text: '% SACP ABIERTOS'
        }
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:,.2f}%'
            }
        }
    },

    tooltip: {
      headerFormat: '<span style="font-size:13px">{series.name}</span><br>',
      pointFormat: '<span style="font-size:13px; color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/> TOTAL: <b>{point.totalCat}</b>'
    },
    credits: {
    enabled: false
  },

    series: [
      {
        colorByPoint: true,
        type: 'column',
        name : 'SACP POR PROCESO',
        data: data1,
      },
    ]
});
</script>

<script>                                                                        /* DIBUJANDO TABLA # 5 TOTAL SACP Y TIEMPO TRANSCURRIDO ENTRE  FECHA SACP Y FECHA DE PLAN DE ACCION */

var categories = [
<?php for ($i=0; $i <count($graficaFechasPlanAccion) ; $i++) {  ?>
"<?=$graficaFechasPlanAccion[$i]["CANT_SACP"]?>",
<?php }  ?>
];
var data1 = [
<?php for ($i=0; $i <count($graficaFechasPlanAccion) ; $i++) {  ?>
{
  name: "<?=$graficaFechasPlanAccion[$i]["CANT_SACP"]?>",
  y: <?=$graficaFechasPlanAccion[$i]["DURACION"]?>,
  totalCat: <?=$graficaFechasPlanAccion[$i]["CANT_SACP"]?>
},
<?php }  ?>
];

Highcharts.chart('graf_sacp5', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TIEMPO TRANSCURRIDO ENTRE FECHA SACP Y PLAN DE ACCION DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
      categories: categories,
      title:{
        text: 'CANTIDAD DE SACP',
      },
      min: 0,
      max: <?=$x5?>,
      scrollbar: {
        enabled: true
      },
      //tickLength: 0
    },
    yAxis: {
      min: 0,
      max: <?=$y5?>,
        title: {
            text: 'TIEMPO TRANSCURRIDO (DIAS)'
        }
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:,.0f}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:13px">{series.name}</span>',
        pointFormat: '<span style="font-size:13px; color:{point.color}"> {point.name}:</span></b><br/> TIEMPO TRANSCURRIDO {point.y:.0f} DIAS'
    },
    credits: {
    enabled: false
  },

    series: [
      {
        colorByPoint: true,
        type: 'column',
        name : 'CANTIDAD SACP',
        data: data1,
      },
    ]
});
</script>

<script>                                                                        /* DIBUJANDO TABLA # 6 TOTAL SACP ABIERTOS Y TIEMPO TRANSCURRIDO ENTRE  FECHA SACP Y FECHA DE PLAN DE ACCION */

var categories = [
<?php for ($i=0; $i <count($graficaFechasPlanAccionAbiertos) ; $i++) {  ?>
"<?=$graficaFechasPlanAccionAbiertos[$i]["CANT_SACP"]?>",
<?php }  ?>
];
var data1 = [
<?php for ($i=0; $i <count($graficaFechasPlanAccionAbiertos) ; $i++) {  ?>
{
  name: "<?=$graficaFechasPlanAccionAbiertos[$i]["CANT_SACP"]?>",
  y: <?=$graficaFechasPlanAccionAbiertos[$i]["DURACION"]?>,
  totalCat: <?=$graficaFechasPlanAccionAbiertos[$i]["CANT_SACP"]?>
},
<?php }  ?>
];

Highcharts.chart('graf_sacp6', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TIEMPO TRANSCURRIDO ENTRE FECHA SACP Y PLAN DE ACCION DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
      categories: categories,
      title:{
        text: 'CANTIDAD DE SACP',
      },
      min: 0,
      max: <?=$x6?>,
      scrollbar: {
        enabled: true
      },
      //tickLength: 0
    },
    yAxis: {
      min: 0,
      max: <?=$y6?>,
        title: {
            text: 'TIEMPO TRANSCURRIDO (DIAS)'
        }
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:,.0f}'
            }
        }
    },

    tooltip: {
      headerFormat: '<span style="font-size:13px">{series.name}</span>',
      pointFormat: '<span style="font-size:13px; color:{point.color}"> {point.name}:</span></b><br/> TIEMPO TRANSCURRIDO {point.y:.0f} DIAS'
  },
    credits: {
    enabled: false
  },

    series: [
      {
        colorByPoint: true,
        type: 'column',
        name : 'CANTIDAD SACP',
        data: data1,
      },
    ]
});
</script>

<script>                                                                        /* DIBUJANDO TABLA # 7 SACP CERRADOS Y TIEMPO TRANSCURRIDO ENTRE  FECHA SACP Y FECHA DE CIERRE */

var categories = [
<?php for ($i=0; $i <count($graficaFechasCierre) ; $i++) {  ?>
"<?=$graficaFechasCierre[$i]["CANT_SACP"]?>",
<?php }  ?>
];

var data1 = [
<?php for ($i=0; $i <count($graficaFechasCierre) ; $i++) {  ?>
{
  name: "<?=$graficaFechasCierre[$i]["CANT_SACP"]?>",
  y: <?=$graficaFechasCierre[$i]["DURACION"]?>,
  totalCat: <?=$graficaFechasCierre[$i]["CANT_SACP"]?>
},
<?php }  ?>
];
Highcharts.chart('graf_sacp7', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TIEMPO TRANSCURRIDO ENTRE FECHA SACP Y FECHA CIERRE DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
      categories: categories,
      title:{
        text: 'CANTIDAD DE SACP',
      },
      min: 0,
      max: <?=$x7?>,
      scrollbar: {
        enabled: true
      },
      tickLength: 0
    },
    yAxis: {
      min: 0,
      max: <?=$y7?>,
        title: {
            text: 'TIEMPO TRANSCURRIDO (DIAS)'
        }
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:,.0f}'
            }
        }
    },

    tooltip: {
      headerFormat: '<span style="font-size:13px">{series.name}</span>',
      pointFormat: '<span style="font-size:13px; color:{point.color}"> {point.name}:</span></b><br/> TIEMPO TRANSCURRIDO {point.y:.0f} DIAS'
  },
    credits: {
    enabled: false
  },

    series: [
      {
        colorByPoint: true,
        type: 'column',
        name : 'CANTIDAD SACP',
        data: data1,
      },
    ]
});
</script>

<?php conexion::cerrar($conn); ?>
