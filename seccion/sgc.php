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
$graficaAbiertosAreas=$ModuloGestCal->grafica_areas_abiertos($fechaInicio, $fechaFin);

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


   <div class="col-md-4">
     <!-- small box -->
     <div class="small-box bg-morado">
       <div class="inner">
         <h3 class="text-center"><?= $graficaTotalSgc[0]["TODOS"] ?></h3>
         <center><b>TOTAL DE SACP REGISTRADOS</b></center>
       </div>
       <div class="icon">
         <i class="ion ion-loop"></i>
       </div>
       <a onclick="visualizar(1);" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
     </div>
   </div>

   <div class="col-md-4">
   <div class="small-box bg-info">
     <div class="inner">
       <h3 class="text-center"><?= $graficaTotalSgc[0]["CERRADOS"] ?></h3>
       <center><b>SACP CERRADOS</b></center>
     </div>
     <div class="icon">
       <i class="fa fa-check-square"></i>
     </div>
     <a onclick="visualizar(2);" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
   </div>
   </div>

   <div class="col-md-4">
   <div class="small-box bg-verde">
     <div class="inner">
       <h3 class="text-center"><?= $graficaTotalSgc[0]["ABIERTOS"] ?></h3>
       <center><b>SACP ABIERTOS </b></center>
     </div>
     <div class="icon">
       <i class="ion ion-alert-circled"></i>
     </div>
     <a onclick="visualizar(3);" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
   </div>
   </div>
   <!--
  <div class="col-md-4 col-sm-6">
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

  <div class="col-md-3 col-sm-6">
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

  <div class="col-md-3 col-sm-6">
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
  </div>-->
 </div>
</section>

  <section>                                                                     <!--INICIAN GRAFICAS-->
      <div class="row">

        <div class="col-md-9" id="g1" style="display: ">                        <!--GENERAL GRAFICA # 1 TOTAL DE SACP-->
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

        <div class="col-md-9" id="g2" style="display: ">                        <!--GENERAL GRAFICA # 2 TIEMPO TRANSCURRIDO ENTRE FECHA DE SACP Y FECHA DE PLAN DE ACCION-->
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
                    //echo $x5."as x5";
                }else{
                 $x5 = 0;
                 $y5 = 0;
                 echo "<center><h4>No se encontraron SACP Registrados</h4></center>";
                }
               ?>
              <div id="graf_sacp2" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g3" style="display: none">                    <!--ABIERTOS GRAFICA # 3 DETALLE GENERAL DE SACP ABIERTOS POR PLAZA-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> DETALLE GRAL. SACP ABIERTOS POR PLAZA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <input type="submit" value="DETALLE POR PLAZA" onclick="visualizarDetalle(1);">
              <div id="graf_sacp3" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g4" style="display: none">                    <!--ABIERTOS GRAFICA # 4 DETALLE DE SACP ABIERTOS POR PLAZA-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP ABIERTOS POR PLAZA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div style="padding:0;width:900;">
                <?php for ($i=0; $i <count($graficaAbiertosPlaza) ; $i++) { ?>
                  <div id="grafAb<?=$graficaAbiertosPlaza[$i]["ID_PLAZA"]?>" style="height: 380px;">
                 </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g5" style="display: none">                    <!--ABIERTOS GRAFICA # 5 DETALLE GRAL DE SACP ABIERTOS POR PROCESO-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> DETALLE GRAL. SACP ABIERTOS POR PROCESO</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <input type="submit" value="DETALLE POR PROCESO" onclick="visualizarDetalle(2);">
              <div id="graf_sacp5" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g11" style="display: none">                   <!--ABIERTOS GRAFICA # 11 DETALLE POR PROCESO-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP ABIERTOS POR PROCESO</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div style="padding:0;width:900;">
                <?php for ($i=0; $i <count($graficaAbiertosAreas) ; $i++) { ?>
                  <div id="grafAp<?=$graficaAbiertosAreas[$i]["AREA"]?><?=$graficaAbiertosAreas[$i]["DEPTO"]?>" style="height: 380px;">
                 </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g6" style="display: none">                    <!--ABIERTOS GRAFICA # 6 DETALLE GRAL DE SACP ABIERTOS POR CAP. NORMA-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> DETALLE GRAL. SACP ABIERTOS POR CAPITULO DE NORMA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <input type="submit" value="DETALLE POR CAPITULO" onclick="visualizarDetalle(3);">
              <div id="graf_sacp6" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g12" style="display: none">                   <!--ABIERTOS GRAFICA # 12 DETALLE POR CAPITULO DE NORMA-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP ABIERTOS POR CAPITULO DE NORMA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div style="padding:0;width:900;">
                <?php for ($i=0; $i <count($graficaAbiertosProceso) ; $i++) { ?>
                  <div id="grafAcn<?=$graficaAbiertosProceso[$i]["PROCESO"]?>" style="height: 380px;">
                 </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g7" style="display: none">                    <!--ABIERTOS GRAFICA # 7 TIEMPO TRANSCURRIDO ENTRE FECHA DE SOLICITUD Y FECHA DE PLAN DE ACCION-->
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
              <div id="graf_sacp7" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g8" style="display: none">                    <!--CERRADOS GRAFICA # 8 DETALLE GENERAL DE SACP CERRADOS-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> DETALLE GRAL. SACP CERRADOS POR PLAZA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <input type="submit" value="DETALLE POR PLAZA" onclick="visualizarDetalle(4);">
              <div id="graf_sacp8" class="col-md-12" style="height:380px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-9" id="g9" style="display: none">                    <!--CERRADOS GRAFICA # 9 DETALLE DE SACP CERRADOS POR PLAZA-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> SACP CERRADOS POR PLAZA</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">

              <div style="padding:0;width:900;">
                <?php for ($i=0; $i <count($graficaCerradosPlaza) ; $i++) { ?>
                  <div id="grafCe<?=$graficaCerradosPlaza[$i]["ID_PLAZA"]?>" style="height: 380px;">
                 </div>
                <?php } ?>
              </div>

            </div>
          </div>
        </div>

        <div class="col-md-9" id="g10" style="display: none">                   <!--CERRADOS GRAFICA # 10 TIEMPO TRANSCURRIDO ENTRE FECHA DE SOLICITUD Y FECHA DE CIERRE-->
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
                $x7 = 0;
                $y7 = 0;
                echo "<center><h4>No se encontraron SACP cerrados</h4></center>";
              }
             ?>
            <div class="box-body">
              <div id="graf_sacp10" class="col-md-12" style="height:380px;"></div>
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
      document.getElementById("g8").style.display="none";
      document.getElementById("g9").style.display="none";
      document.getElementById("g10").style.display="none";
      document.getElementById("g11").style.display="none";
      document.getElementById("g12").style.display="none";
      break;
    case 2://SACP CERRADOS
      document.getElementById("g8").style.display="";
      document.getElementById("g9").style.display="none";
      document.getElementById("g10").style.display="";

      document.getElementById("g1").style.display="none";
      document.getElementById("g2").style.display="none";
      document.getElementById("g3").style.display="none";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="none";
      document.getElementById("g6").style.display="none";
      document.getElementById("g7").style.display="none";
      document.getElementById("g11").style.display="none";
      document.getElementById("g12").style.display="none";
      break;
    case 3:// SACP ABIERTOS
      document.getElementById("g3").style.display="";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="";
      document.getElementById("g11").style.display="none";
      document.getElementById("g6").style.display="";
      document.getElementById("g12").style.display="none";
      document.getElementById("g7").style.display="";

      document.getElementById("g1").style.display="none";
      document.getElementById("g2").style.display="none";
      document.getElementById("g8").style.display="none";
      document.getElementById("g9").style.display="none";
      document.getElementById("g10").style.display="none";
      break;
    default://DETALLE GENERAL DE SACP
      document.getElementById("g1").style.display="";
      document.getElementById("g2").style.display="";
      document.getElementById("g3").style.display="none";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="none";
      document.getElementById("g6").style.display="none";
      document.getElementById("g7").style.display="none";
      document.getElementById("g8").style.display="none";
      document.getElementById("g9").style.display="none";
      document.getElementById("g10").style.display="none";
      document.getElementById("g11").style.display="none";
      document.getElementById("g12").style.display="none";
  }
}
</script>

<script>
function visualizarDetalle(id){

  switch (id) {
    case 1://SACP ABIERTOS PLAZA DETALLE
    if(document.getElementById("g4").style.display==="none"){
      document.getElementById("g3").style.display="";
      document.getElementById("g4").style.display="";
      document.getElementById("g5").style.display="none";
      document.getElementById("g11").style.display="none";
      document.getElementById("g6").style.display="none";
      document.getElementById("g12").style.display="none";
      document.getElementById("g7").style.display="none";
    }else{
      document.getElementById("g3").style.display="";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="";
      document.getElementById("g11").style.display="none";
      document.getElementById("g6").style.display="";
      document.getElementById("g12").style.display="none";
      document.getElementById("g7").style.display="";
    }
    break;
    case 2://SACP ABIERTOS PROCESO DETALLE
    if(document.getElementById("g11").style.display==="none"){
      document.getElementById("g3").style.display="none";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="";
      document.getElementById("g11").style.display="";
      document.getElementById("g6").style.display="none";
      document.getElementById("g12").style.display="none";
      document.getElementById("g7").style.display="none";
    }else{
      document.getElementById("g3").style.display="";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="";
      document.getElementById("g11").style.display="none";
      document.getElementById("g6").style.display="";
      document.getElementById("g12").style.display="none";
      document.getElementById("g7").style.display="";
    }
    break;
    case 3://SACP ABIERTOS NORMA DETALLE
    if(document.getElementById("g12").style.display==="none"){
      document.getElementById("g3").style.display="none";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="none";
      document.getElementById("g11").style.display="none";
      document.getElementById("g6").style.display="";
      document.getElementById("g12").style.display="";
      document.getElementById("g7").style.display="none";
    }else{
      document.getElementById("g3").style.display="";
      document.getElementById("g4").style.display="none";
      document.getElementById("g5").style.display="";
      document.getElementById("g11").style.display="none";
      document.getElementById("g6").style.display="";
      document.getElementById("g12").style.display="none";
      document.getElementById("g7").style.display="";
    }
    break;
    case 4:
    if(document.getElementById("g9").style.display==="none"){
      document.getElementById("g8").style.display="";
      document.getElementById("g9").style.display="";
      document.getElementById("g10").style.display="none";
    }else{
      document.getElementById("g8").style.display="";
      document.getElementById("g9").style.display="none";
      document.getElementById("g10").style.display="";
    }
    default:

  }
}
</script>

<script>                                                                        /*GENERAL GRAFICA # 1 DETALLE GENERAL DE SACP */
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

<script>                                                                        /*GENERAL GRAFICA #2 TIEMPO TRANSCURRIDO ENTRE  FECHA SACP Y FECHA DE PLAN DE ACCION */

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
  totalCat: <?=$graficaFechasPlanAccion[$i]["CANT_SACP"]?>,
  plaza: "<?=$graficaFechasPlanAccion[$i]["PLAZA"]?>",
},
<?php }  ?>
];

Highcharts.chart('graf_sacp2', {
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
      max: <?=$x5?> ,
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
        pointFormat: '<span style="font-size:13px; color:{point.color}"> {point.name}:</span></b><br/> TIEMPO TRANSCURRIDO {point.y:.0f} DIAS <br> PLAZA: {point.plaza}'
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

<script>
var data1 = [
<?php for ($i=0; $i <count($graficaAbiertosPlaza) ; $i++) {  ?>
{
  name: "<?=$graficaAbiertosPlaza[$i]["PLAZA"]?>",
  y: <?=$totalAbiertos=round((($graficaAbiertosPlaza[$i]["ABIERTOS"]*100)/$graficaTotalSgc[0]["ABIERTOS"]),2);?>,
  totalCat: <?=$graficaAbiertosPlaza[$i]["ABIERTOS"]?>
},
<?php }  ?>
];
                                                                                /*ABIERTOS GRAFICA # 3 DETALLE GENERAL DE SACP ABIERTOS*/
Highcharts.chart('graf_sacp3', {
  chart: {
        type: 'pie'
    },
    title: {
        text: 'SACP ABIERTOS DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
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
            name: "SACP ABIERTOS",
            colorByPoint: true,
            data: data1
        }
    ]
});
</script>

<script>                                                                        /*ABIERTOS GRAFICA # 4 DETALLE DE SACP ABIERTOS POR PLAZA*/
<?php for ($i=0; $i <count($graficaAbiertosPlaza) ; $i++) {?>
  <?php if($graficaAbiertosPlaza[$i]["CERRADOS"]==null){
    $graficaAbiertosPlaza[$i]["CERRADOS"]=0;
  } ?>

  Highcharts.chart('grafAb<?=$graficaAbiertosPlaza[$i]["ID_PLAZA"]?>', {
    colors: ['#8FCEEA','#1FBC0C','#EA856F'],
      chart: {
          type: 'pie'
      },
      title: {
        useHTML: true,
          text: '<h4>SACP ABIERTOS <b>PLAZA <?=$graficaAbiertosPlaza[$i]["PLAZA"]?></b> DEL <?=$fechaInicio?> AL <?=$fechaFin?></h4>'
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
          pointFormat:  '<span style="font-size:13px; ">TOTAL SACP {point.totalGral} <br>SACP {point.name} {point.totalCat}</span> = <b>{point.y:.2f}%</b> <span style="font-size:13px; ">',
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
                      y: <?=$totalCerrados=round((($graficaAbiertosPlaza[$i]["CERRADOS"]*100)/$graficaAbiertosPlaza[$i]["TOTAL"]),2);?>,
                      totalGral: <?=$graficaAbiertosPlaza[$i]["TOTAL"]?>,
                      totalCat: <?=$graficaAbiertosPlaza[$i]["CERRADOS"]?>,
                  },
                  {
                      name: "ABIERTOS",
                      y: <?=$totalAbiertos=round((($graficaAbiertosPlaza[$i]["ABIERTOS"]*100)/$graficaAbiertosPlaza[$i]["TOTAL"]),2);?>,
                      totalGral: <?=$graficaAbiertosPlaza[$i]["TOTAL"]?>,
                      totalCat: <?=$graficaAbiertosPlaza[$i]["ABIERTOS"]?>,
                  },
              ]
          }
      ]
  });
  <?php } ?>
</script>

<script>
var data1 = [
<?php for ($i=0; $i <count($graficaAbiertosAreas) ; $i++) {  ?>
{
  name: "<?=$graficaAbiertosAreas[$i]["PROCESO"]?>",
  y: <?=$totalAbiertos=round((($graficaAbiertosAreas[$i]["SACP_ABIERTOS"]*100)/$graficaTotalSgc[0]["ABIERTOS"]),2);?>,
  totalCat: <?=$graficaAbiertosAreas[$i]["SACP_ABIERTOS"]?>
},
<?php }  ?>
];
                                                                                /*ABIERTOS GRAFICA # 5 DETALLE DE SACP ABIERTOS POR PROCESO*/
Highcharts.chart('graf_sacp5', {
  chart: {
        type: 'pie'
    },
    title: {
        text: 'SACP ABIERTOS DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
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
            name: "SACP ABIERTOS",
            colorByPoint: true,
            data: data1
        }
    ]
});
</script>

<script>                                                                        /*ABIERTOS GRAFICA # 11 DETALLE DE SACP ABIERTOS POR PLAZA*/
<?php for ($i=0; $i <count($graficaAbiertosAreas) ; $i++) {?>
  <?php $graficaProcesoAb=$ModuloGestCal->grafica_proceso($fechaInicio, $fechaFin, $graficaAbiertosAreas[$i]["AREA"], $graficaAbiertosAreas[$i]["DEPTO"]); ?>

  var data1 = [
  <?php for ($x=0; $x <count( $graficaProcesoAb) ; $x++) {  ?>
  {
    name: "<?=$graficaProcesoAb[$x]["PLAZA"]?>",
    y: <?=$totalCerrados=round((($graficaProcesoAb[$x]["TOTAL_PROC"]*100)/$graficaAbiertosAreas[$i]["SACP_ABIERTOS"]),2);?>,
    totalGral: <?=$graficaAbiertosAreas[$i]["SACP_ABIERTOS"]?>,
    totalCat: <?=$graficaProcesoAb[$x]["TOTAL_PROC"]?>,
  },
  <?php }  ?>
  ];
  Highcharts.chart('grafAp<?=$graficaAbiertosAreas[$i]["AREA"]?><?=$graficaAbiertosAreas[$i]["DEPTO"]?>', {
    colors: ['#8FCEEA','#1FBC0C','#EA856F'],
      chart: {
          type: 'pie'
      },
      title: {
        useHTML: true,
          text: '<h4>SACP ABIERTOS <b>PROCESO <?=$graficaAbiertosAreas[$i]["PROCESO"]?></b> DEL <?=$fechaInicio?> AL <?=$fechaFin?></h4>'
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
          pointFormat:  '<span style="font-size:13px; ">TOTAL SACP {point.totalGral} <br>SACP {point.name} {point.totalCat}</span> = <b>{point.y:.2f}%</b> <span style="font-size:13px; ">',
      },
      credits: {
      enabled: false
    },
      series: [
          {
              name: "SACP",
              colorByPoint: true,
              data:data1,
          }
      ]
  });
  <?php } ?>
</script>

<script>
var data1 = [
<?php for ($i=0; $i <count($graficaAbiertosProceso) ; $i++) {  ?>
{
  name: "<?=$graficaAbiertosProceso[$i]["PROCESO"]?>",
  y: <?=$totalAbiertos=round((($graficaAbiertosProceso[$i]["SACP_ABIERTOS"]*100)/$graficaTotalSgc[0]["ABIERTOS"]),2);?>,
  totalCat: <?=$graficaAbiertosProceso[$i]["SACP_ABIERTOS"]?>
},
<?php }  ?>
];
                                                                                /*ABIERTOS GRAFICA # 6 DETALLE DE SACP ABIERTOS POR CAP. NORMA*/
Highcharts.chart('graf_sacp6', {
  chart: {
        type: 'pie'
    },
    title: {
        text: 'SACP ABIERTOS DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
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
            name: "SACP ABIERTOS",
            colorByPoint: true,
            data: data1
        }
    ]
});
</script>

<script>                                                                        /*ABIERTOS GRAFICA # 12 DETALLE DE SACP ABIERTOS POR PLAZA*/
<?php for ($i=0; $i <count($graficaAbiertosProceso) ; $i++) {?>
  <?php $graficaCapitulo=$ModuloGestCal->grafica_capitulo($fechaInicio, $fechaFin, $graficaAbiertosProceso[$i]["IID_PROCESO"]); ?>

  var data1 = [
  <?php for ($x=0; $x <count( $graficaCapitulo) ; $x++) {  ?>
  {
    name: "<?=$graficaCapitulo[$x]["PLAZA"]?>",
    y: <?=$total=round((($graficaCapitulo[$x]["SACP_ABIERTOS"]*100)/$graficaAbiertosProceso[$i]["SACP_ABIERTOS"]),2);?>,
    totalGral: <?=$graficaAbiertosProceso[$i]["SACP_ABIERTOS"]?>,
    totalCat: <?=$graficaCapitulo[$x]["SACP_ABIERTOS"]?>,
  },
  <?php }  ?>
  ];
  Highcharts.chart('grafAcn<?=$graficaAbiertosProceso[$i]["PROCESO"]?>', {
    colors: ['#8FCEEA','#1FBC0C','#EA856F'],
      chart: {
          type: 'pie'
      },
      title: {
        useHTML: true,
          text: '<h4>SACP ABIERTOS <b>CAPITULO <?=$graficaAbiertosProceso[$i]["PROCESO"]?></b> DEL <?=$fechaInicio?> AL <?=$fechaFin?></h4>'
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
          pointFormat:  '<span style="font-size:13px; ">TOTAL SACP {point.totalGral} <br>SACP {point.name} {point.totalCat}</span> = <b>{point.y:.2f}%</b> <span style="font-size:13px; ">',
      },
      credits: {
      enabled: false
    },
      series: [
          {
              name: "SACP",
              colorByPoint: true,
              data:data1,
          }
      ]
  });
  <?php } ?>
</script>

<script>                                                                        /*ABIERTOS GRAFICA # 7 DETALLE DE SACP ABIERTOS TIEMPO TRANSCURRIDO ENTRE FECHA DE SOLICITUD Y FECHA DE PLAN DE ACCION*/

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
  totalCat: <?=$graficaFechasPlanAccionAbiertos[$i]["CANT_SACP"]?>,
  plaza: "<?=$graficaFechasPlanAccionAbiertos[$i]["PLAZA"]?>",
},
<?php }  ?>
];

Highcharts.chart('graf_sacp7', {
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
      pointFormat: '<span style="font-size:13px; color:{point.color}"> {point.name}:</span></b><br/> TIEMPO TRANSCURRIDO {point.y:.0f} DIAS <br> PLAZA: {point.plaza}'
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

<script>
var data1 = [
<?php for ($i=0; $i <count($graficaCerradosPlaza) ; $i++) {  ?>
{
  name: "<?=$graficaCerradosPlaza[$i]["PLAZA"]?>",
  y: <?=$totalCerrados=round((($graficaCerradosPlaza[$i]["CERRADOS"]*100)/$graficaTotalSgc[0]["CERRADOS"]),2);?>,
  totalCat: <?=$graficaCerradosPlaza[$i]["CERRADOS"]?>
},
<?php }  ?>
];
                                                                                /*CERRADOS GRAFICA # 8 DETALLE GENERAL DE SACP CERRADOS*/
Highcharts.chart('graf_sacp8', {
  chart: {
        type: 'pie'
    },
    title: {
        text: 'SACP CERRADOS DEL <?=$fechaInicio?> AL <?=$fechaFin?>'
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
            name: "SACP CERRADOS",
            colorByPoint: true,
            data: data1
        }
    ]
});
</script>

<script>                                                                        /*CERRADOS GRAFICA # 9 DETALLE DE SACP CERRADOS POR PLAZA*/
<?php for ($i=0; $i <count($graficaCerradosPlaza) ; $i++) {?>
  <?php if($graficaCerradosPlaza[$i]["ABIERTOS"]==null){
    $graficaCerradosPlaza[$i]["ABIERTOS"]=0;
  } ?>
  Highcharts.chart('grafCe<?=$graficaCerradosPlaza[$i]["ID_PLAZA"]?>', {
    colors: ['#8FCEEA','#1FBC0C','#EA856F'],
      chart: {
          type: 'pie'
      },
      title: {
        useHTML: true,
          text: '<h4>SACP CERRADOS <b>PLAZA <?=$graficaCerradosPlaza[$i]["PLAZA"]?></b> DEL <?=$fechaInicio?> AL <?=$fechaFin?></h4>'
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
          pointFormat:  '<span style="font-size:13px; ">TOTAL SACP {point.totalGral} <br>SACP {point.name} {point.totalCat}</span> = <b>{point.y:.2f}%</b> <span style="font-size:13px; ">',
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
                      y: <?=$totalCerrados=round((($graficaCerradosPlaza[$i]["CERRADOS"]*100)/$graficaCerradosPlaza[$i]["TOTAL"]),2);?>,
                      totalGral: <?=$graficaCerradosPlaza[$i]["TOTAL"]?>,
                      totalCat: <?=$graficaCerradosPlaza[$i]["CERRADOS"]?>,
                  },
                  {
                      name: "ABIERTOS",
                      y: <?=$totalCerrados=round((($graficaCerradosPlaza[$i]["ABIERTOS"]*100)/$graficaCerradosPlaza[$i]["TOTAL"]),2);?>,
                      totalGral: <?=$graficaCerradosPlaza[$i]["TOTAL"]?>,
                      totalCat: <?=$graficaCerradosPlaza[$i]["ABIERTOS"]?>,
                  },
              ]
          }
      ]
  });
  <?php } ?>
</script>

<script>                                                                        /*CERRADOS GRAFICA # 10 DETALLE DE SACP CERRADOS TIEMPO TRANSCURRIDO ENTRE FECHA DE SOLICITUD Y FECHA DE PLAN DE ACCION*/

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
  totalCat: <?=$graficaFechasCierre[$i]["CANT_SACP"]?>,
  plaza: "<?=$graficaFechasCierre[$i]["PLAZA"]?>",
},
<?php }  ?>
];
Highcharts.chart('graf_sacp10', {
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
      pointFormat: '<span style="font-size:13px; color:{point.color}"> {point.name}:</span></b><br/> TIEMPO TRANSCURRIDO {point.y:.0f} DIAS <br> PLAZA: {point.plaza}'
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
