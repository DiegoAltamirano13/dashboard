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

$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 57);        //VALIDACION DEL MODULO ASIGNADO
if($modulos_valida == 0){
  header('Location: index.php');
}

include '../class/Rotacion_personal_gral.php';
$rotPersonalGral=new Rotacion();


$años=$rotPersonalGral->fechas();
$anio_uno=$años[0]["ANIO_ANT"];//2021 2020
$anio_dos=$años[0]["SDO_ANIO_ANT"];//2020 2019

//if (isset($_GET["anio_ant"])) {
    //$anio_uno = substr($_GET["anio_ant"],5,10);
    //$anio_dos=  substr($_GET["anio_ant"],0,4);
//}

if (isset($_GET["anio_uno"]) && isset($_GET["anio_dos"])) {
    //$anio_uno = substr($_GET["anio_ant"],5,10);
    //$anio_dos=  substr($_GET["anio_ant"],0,4);
    $anio_uno =$_GET["anio_uno"];
    $anio_dos =$_GET["anio_dos"];
}

$plantilla=$rotPersonalGral->plantilla_trabajadores($anio_uno, $anio_dos);
$rotacion_plaza=$rotPersonalGral-> rotacion_plaza($anio_uno, $anio_dos);

?>

<?php include_once('../layouts/plantilla.php'); ?>                              <!--INCLUIR PLANTILLA PHP-->

<link rel="stylesheet" href="../plugins/select2/select2.min.css">               <!--ESTILOS-->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<link rel="../plugins/daterangepicker/daterangepicker.css">


<div class="content-wrapper">                                                   <!--INICIA PLANTILLA ROTACION PERSONAL-->
  <section class="content-header">
    <h1>Dashboard<small>ROTACION DE PERSONAL (DETALLE GENERAL)</small></h1>
  </section>


<section class="content">

  <section>                                                                     <!-- INICIAN WIDGETS -->
    <div class="row">

      <div class="col-md-3">                                                    <!--PROMEDIO MENSUAL DE PERSONAL ACTIVO-->
        <div class="small-box bg-aqua">
          <div class="inner">
            <!--
                FORMULA
                PROMEDIO MENSUAL DE PERSONAL ACTIVO= SUMATORIA DEL PERSONAL ACTIVO DURANTE EL AÑO /12 (MESES DEL AÑO)
              -->
            <?php
            $sumatoria_act=0;
            $sumatoria_sdo_act=0;
            for($i=0; $i<count($plantilla); $i ++){
              $sumatoria_act=$sumatoria_act+$plantilla[$i]["ACTIVO"];
              $sumatoria_sdo_act=$sumatoria_sdo_act+$plantilla[$i]["ACTIVO_ANTERIOR"];
            }
            $promedio_ant_act=round($sumatoria_act/12);
            $promedio_sdo_act=round($sumatoria_sdo_act/12);
             ?>
            <h4 class="text-center"><b><?php echo $anio_dos.": ".$promedio_sdo_act;?></b></h4>
            <h4 class="text-center"><b><?php echo $anio_uno.": ".$promedio_ant_act;?></b></h4>
            <center><b>PROMEDIO MENSUAL DE PERSONAL ACTIVO</b></center>
          </div>
          <div class="icon">
            <i class="fa fa-check-square-o"></i>
          </div>
        </div>
      </div>

      <div class="col-md-3">                                                    <!--PROMEDIO MENSUAL DE PERSONAL INACTIVO-->
        <div class="small-box bg-red">
          <div class="inner">
            <!--
                FORMULA
                PROMEDIO MENSUAL DE PERSONAL INACTIVO= SUMATORIA DEL PERSONAL INACTIVO DURANTE EL AÑO /12 (MESES DEL AÑO)
              -->
            <?php
            $sumatoria_inac=0;
            $sumatoria_sdo_inac=0;
            for($i=0; $i<count($plantilla); $i ++){
              $sumatoria_inac=$sumatoria_inac+$plantilla[$i]["BAJA"];
              $sumatoria_sdo_inac=$sumatoria_sdo_inac+$plantilla[$i]["BAJA_ANTERIOR"];
            }
            $promedio_ant_inac=round($sumatoria_inac/12);
            $promedio_sdo_inac=round($sumatoria_sdo_inac/12);
             ?>
            <h4 class="text-center"><b><?php echo $anio_dos.": ".$promedio_sdo_inac;?></b></h4>
            <h4 class="text-center"><b><?php echo $anio_uno.": ".$promedio_ant_inac;?></b></h4>
            <center><b>PROMEDIO MENSUAL PERSONAL INACTIVO</b></center>
          </div>
          <div class="icon">
            <i class="fa fa-times"></i>
          </div>
        </div>
      </div>

      <div class="col-md-3">                                                    <!--PROMEDIO ROTACION MENSUAL-->
        <div class="small-box bg-verde">
          <div class="inner">
            <!--
                FORMULA
                PROMEDIO DE ROTACION MENSUAL= (SUMATORIA DEL PERSONAL INACTIVO DURANTE EL AÑO /SUMATORIA DEL PERSONAL CTIVO DURANTE EL AÑO)*100
              -->
            <?php
            $sumatoria_rot_ant=0;
            $sumatoria_rot_sdo_ant=0;
            for($i=0; $i<count($plantilla); $i ++){

              if($plantilla[$i]["ACTIVO"]>0){
                $rotacion=($plantilla[$i]["BAJA"]/$plantilla[$i]["ACTIVO"])*100;
                $sumatoria_rot_ant=$sumatoria_rot_ant+$rotacion;
              }else {
                $rotacion=0;
              }

              if($plantilla[$i]["ACTIVO_ANTERIOR"]>0){
                $rotacion_2=($plantilla[$i]["BAJA_ANTERIOR"]/$plantilla[$i]["ACTIVO_ANTERIOR"])*100;
                $sumatoria_rot_sdo_ant=$sumatoria_rot_sdo_ant+$rotacion_2;
              }else {
                $rotacion_2=0;
              }

            }
            $promedio_rot_ant=round($sumatoria_rot_ant/12);
            $promedio_rot_sdo_ant=round($sumatoria_rot_sdo_ant/12);
             ?>
            <h4 class="text-center"><b><?php echo $anio_dos.": ".$promedio_rot_sdo_ant."%";?></b></h4>
            <h4 class="text-center"><b><?php echo $anio_uno.": ".$promedio_rot_ant."%";?></b></h4>
            <center><b>PROMEDIO DE ROTACION MENSUAL</b></center>
          </div>
          <div class="icon">
            <i class="fa fa-refresh"></i>
          </div>
        </div>
      </div>

      <div class="col-md-3">                                                    <!--PROMEDIO ROTACION ANUAL-->
        <div class="small-box bg-morado">
          <div class="inner">
            <!--
                FORMULA
                PROMEDIO DE ROTACION ANUAL= (SUMATORIA DEL PERSONAL INACTIVO DURANTE EL AÑO /PROMEDIO DE ROTACION MENSUAL)*100
              -->
            <?php
              $promedio_rotacion_ant=round(($sumatoria_inac/$promedio_ant_act)*100);
              $promedio_rotacion_sdo_ant=round(($sumatoria_sdo_inac/$promedio_sdo_act)*100);
              ?>
            <h4 class="text-center"><b><?php echo $anio_dos.": ".$promedio_rotacion_sdo_ant."%";?></b></h4>
            <h4 class="text-center"><b><?php echo $anio_uno.": ".$promedio_rotacion_ant."%";?></b></h4>
            <center><b>PROMEDIO DE ROTACION ANUAL</b></center>
          </div>
          <div class="icon">
            <i class="fa fa-refresh"></i>
          </div>
        </div>
      </div>

    </div>
  </section>                                                                    <!-- TERMINAN WIDGETS -->

  <section>
    <div class="row">

      <div class="col-md-9">                                                    <!--INICIA GRAFICA COMPARATIVA DE PLANTILLA DE TRABAJADORES-->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bar-chart"></i> PLANTILLA DE PERSONAL</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body">
            <div id="graf_1" class="col-md-12" style="height:380px;"></div>
          </div>
        </div>
      </div>

      <table id="datatable" style="display: none">
        <thead>
          <tr>
            <th></th>
            <th><?php echo $anio_dos?></th>
            <th><?php echo $anio_uno ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          for ($i=0; $i <count($plantilla) ; $i++) {
          ?>
          <tr>
            <th><?php echo $plantilla[$i]["MES"] ?></th>
            <td><?php echo $plantilla[$i]["ACTIVO_ANTERIOR"] ?></td>
            <td><?php echo $plantilla[$i]["ACTIVO"] ?></td>
          </tr>
          <?php } ?>
        </tbody>
    </table>                                                                    <!--TERMINA GRAFICA COMPARATIVA DE PLANTILLA DE TRABAJADORES-->


      <div class="col-md-3">                                                    <!--INICIA FILTRO -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros </h3>
            <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
              <a href="rotacion_personal_gral.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
            <?php } ?>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body">

            <!-- FILTRAR POR AÑO -->
            <!--<div class="input-group">
              <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Años:</span>
              <select class="form-control select2" id="fil_anios" style="width: 100%;">
                <?php for($i=date("Y"); $i>1996; $i--){?>
                  <option <?php if( $i==$anio_uno && $i-1==$anio_dos){echo "selected";} ?>><?php echo $i-1 ?>-<?php echo $i ?></option>
                <?php }?>
              </select>
            </div>-->
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Comparar Año:</span>
              <select class="form-control select2" id="fil_anio_uno" style="width: 100%;">
                <?php for($i=date("Y"); $i>1996; $i--){?>
                  <option <?php if( $i==$anio_uno ){echo "selected";} ?>><?php echo $i ?></option>
                <?php }?>
              </select>
            </div>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Contra Año:</span>
              <select class="form-control select2" id="fil_anio_dos" style="width: 100%;">
                <?php for($i=date("Y"); $i>1996; $i--){?>
                  <option <?php if( $i==$anio_dos ){echo "selected";} ?>><?php echo $i ?></option>
                <?php }?>
              </select>
            </div>
            <div class="input-group">
              <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil"><i class="fa fa-check"></i> Filtrar</button> </span>
            </div>
          </div>
        </div>
      </div>                                                                    <!--TERMINA FILTRO -->


    <div class="col-md-9">                                                      <!--INICIA GRAFICA COMPARATIVA DE INDICE DE ROTACION DE TRABAJADORES-->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> ROTACION DE PERSONAL</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div id="graf_2" class="col-md-12" style="height:380px;"></div>
        </div>
      </div>
    </div>                                                                      <!--INICIA GRAFICA COMPARATIVA DE INDICE DE ROTACION DE TRABAJADORES-->


    <div class="col-md-9">                                                      <!--INICIA GRAFICA COMPARATIVA DE INDICE DE ROTACION DE TRABAJADORES POR PLAZA-->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> ROTACION DE PERSONAL PLAZA</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div id="graf_3" class="col-md-12" style="height:380px;"></div>
        </div>
      </div>
    </div>                                                                      <!--INICIA GRAFICA COMPARATIVA DE INDICE DE ROTACION DE TRABAJADORES POR PLAZA-->


    <div class="col-md-9">                                                      <!--INICIA GRAFICA COMPARATIVA DE INDICE DE ROTACION ACUMULADA-->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> ROTACION DE PERSONAL ACUMULADA</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div id="graf_4" class="col-md-12" style="height:380px;"></div>
        </div>
      </div>
    </div>                                                                      <!--INICIA GRAFICA COMPARATIVA DE INDICE DE ROTACION ACUMULADA-->


    <div class="col-md-9">                                                      <!--INICIA TABLA COMPARATIVA GENARAL DE CAUSAS BAJA-->
      <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-table"></i> BAJAS POR PUESTO </h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>

            <div class="box-body">
              <div class="table-responsive" id="container">
                <center><h4><b>COMPARATIVA DE BAJAS POR PUESTO <?=$anio_dos?> VS <?=$anio_uno?></b></h4></center>
                <table id="tabla_3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                      <th class="small" bgcolor="#4791de"><font color="white">BAJAS <?=$anio_dos?></font></th>
                      <th class="small" bgcolor="#4791de"><font color="white">BAJAS <?=$anio_uno?></font></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sum_anio_ant=0; $sum_sdo_anio_ant=0;
                    $bajas=$rotPersonalGral->comparativo_puestos_bajas($anio_uno, $anio_dos);
                    for ($i=0; $i <count($bajas) ; $i++) {
                      $sum_anio_ant=$sum_anio_ant+$bajas[$i]["TOTAL_ANT"];
                      $sum_sdo_anio_ant=$sum_sdo_anio_ant+$bajas[$i]["TOTAL_ANIO_ANT"];
                      ?>
                      <tr>
                        <td ><?php echo $bajas[$i]["NOM_PUESTO"] ?></td>
                        <td align="center"><?php echo number_format($bajas[$i]["TOTAL_ANIO_ANT"]) ?></td>
                        <td align="center"><?php echo number_format($bajas[$i]["TOTAL_ANT"]) ?></td>
                      </tr>
                    <?php }?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td align="center"><b>TOTAL POR PUESTO:</b></td>
                      <td align="center"><b><?php echo $sum_sdo_anio_ant;?></b></td>
                      <td align="center"><b><?php echo $sum_anio_ant;?></b></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>                                                                  <!--TERMINA TABLA COMPARATIVA GENARAL DE CAUSAS BAJA-->


    <div class="col-md-9">                                                      <!--INICIA TABLA DE CAUSAS BAJA DE 2 AÑOS ANTERIORES AL ACTUAL-->
      <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-table"></i> CAUSAS DE BAJA <?php echo $anio_dos?></h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>

            <div class="box-body">
              <div class="table-responsive" id="container">
                <center><h4><b>CAUSAS DE BAJA DEL AÑO:  <?=$anio_dos?></b></h4></center>
                <table id="tabla_1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                      <?php $motivos=$rotPersonalGral->motivos($anio_dos);
                        for ($i=0; $i <count($motivos) ; $i++) {
                      ?>
                      <th><?php echo $motivos[$i]["MOTIVO"] ?></th>
                      <?php } ?>
                      <th>TOTAL POR PUESTO</th>
                      <th >%</th>
                    </tr>
                  </thead>
                  <tbody>
                <?php
                if(count($motivos)>0){
                  $puestos=$rotPersonalGral->causas_baja_anio_ant($motivos, $anio_dos);
                  $contador=0; $total=0; $porcentaje=0;
                  for ($i=0; $i <count($puestos) ; $i++) {
                    $contador=0;
                ?>
              <tr>
                <td bgcolor="#A7CAE1"><?php echo $puestos[$i]["NOM_PUESTO"] ?></td>
                <?php for($x=0; $x <count($motivos); $x++){
                  $nom_motivo = str_replace(" ", "_", $motivos[$x]['MOTIVO']);
                  ?>
                <td align="center"><?php echo number_format($puestos[$i][$nom_motivo]) ?></td>
                <?php
                  $contador=$contador+$puestos[$i][$nom_motivo];
                  $total=$total+$puestos[$i][$nom_motivo];
                  }?>
                <td align="center"><?php echo $contador ?></td>
                <td align="center"><?php echo number_format(($contador/$sum_sdo_anio_ant)*100,2) ?></td>
              </tr>
            <?php
            $porcentaje=$porcentaje+ (($contador/$sum_sdo_anio_ant)*100);
              }
            }else{
              $total=0; $porcentaje=0;
            }?>
              </tbody>
              <tfoot>
                 <tr>
                   <td align="center"><b>TOTAL POR MOTIVO:</b></td>
                   <?php for($x=0; $x <count($motivos); $x++){?>
                   <td align="center"><b>0</b></td>
                   <?php }?>
                   <td align="center"><b><?php echo $total ?></b></td>
                   <td ><b><?php echo $porcentaje."%" ?></b></td>
                </tr>
             </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>                                                                      <!--TERMINA TABLA DE CAUSAS BAJA DE 2 AÑOS ANTERIORES AL ACTUAL-->


    <div class="col-md-9">                                                      <!--INICIA TABLA DE CAUSAS BAJA DE 1 AÑO ANTERIOR AL ACTUAL-->
      <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-table"></i> CAUSAS DE BAJA <?php echo $anio_uno ?></h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>

            <div class="box-body">
              <div class="table-responsive" id="container">
                <center><h4><b>CAUSAS DE BAJA DEL AÑO:  <?=$anio_uno?></b></h4></center>
                <table id="tabla_2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                      <?php $motivos=$rotPersonalGral->motivos($anio_uno);
                        for ($i=0; $i <count($motivos) ; $i++) {
                      ?>
                      <th><?php echo $motivos[$i]["MOTIVO"] ?></th>
                      <?php } ?>
                      <th>TOTAL POR PUESTO</th>
                      <th>%</th>
                    </tr>
                  </thead>
                  <tbody>
                <?php
                if(count($motivos)>0){
                $puestos=$rotPersonalGral->causas_baja_anio_ant($motivos, $anio_uno);
                $contador=0; $total=0; $porcentaje=0;
                for ($i=0; $i <count($puestos) ; $i++) {
                  $contador=0;
                ?>
              <tr>
                <td bgcolor="#A7CAE1"><?php echo $puestos[$i]["NOM_PUESTO"] ?></td>
                <?php for($x=0; $x <count($motivos); $x++){
                  $nom_motivo = str_replace(" ", "_", $motivos[$x]['MOTIVO']);
                  ?>
                <td align="center"><?php echo number_format($puestos[$i][$nom_motivo]) ?></td>
                <?php
                  $contador=$contador+$puestos[$i][$nom_motivo];
                  $total=$total+$puestos[$i][$nom_motivo];
                  }?>
                <td align="center"><?php echo $contador ?></td>
                <td align="center"><?php echo number_format(($contador/$sum_anio_ant)*100,2) ?></td>
              </tr>
            <?php
            $porcentaje=$porcentaje+ (($contador/$sum_anio_ant)*100);
              }
            }else {
              $total=0; $porcentaje=0;
            }?>
              </tbody>
              <tfoot>
                 <tr>
                   <td align="center"><b>TOTAL POR MOTIVO:</b></td>
                   <?php for($x=0; $x <count($motivos); $x++){?>
                   <td align="center"><b>0</b></td>
                   <?php }?>
                   <td align="center"><b><?php echo $total ?></b></td>
                   <td ><b><?php echo $porcentaje."%" ?></b></td>
                </tr>
             </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>                                                                      <!--TERMINA TABLA DE CAUSAS BAJA DE 1 AÑO ANTERIOR AL ACTUAL-->


  </section>
</section>
</div>

<?php include_once('../layouts/footer.php'); ?>                                 <!--INCLUIR PLANTILLA PHP-->

<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
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

<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="../plugins/datatables/extensions/buttons_datatable/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables/extensions/buttons_datatable/buttons.html5.min.js"></script>
<script src="../plugins/datatables/extensions/buttons_datatable/jszip.min.js"></script>
<script src="../plugins/datatables/extensions/buttons_datatable/buttons.colVis.min.js"></script>
<script src="../plugins/datatables/extensions/buttons_datatable/buttons.print.min.js"></script>
<script src="../plugins/datatables/extensions/Select/dataTables.select.min.js"></script>
<script src="../plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>

<script type="text/javascript">                                                 /*GRAFICA # 1 COMPARATIVO DE PLANTILLA DE EMPLEADOS*/

Highcharts.chart('graf_1', {
    data: {
        table: 'datatable'
    },
    credits: {
      enabled: false
    },
    chart: {
        type: 'spline'
    },
    title: {
        text: '<b>PLANTILLA DE PERSONAL <?=$anio_dos?> VS <?=$anio_uno?></b>'
    },
    yAxis: {
        allowDecimals: false,
        title: {
            text: 'Empleados'
        }
    },
    tooltip: {
        formatter: function () {
            return '<b> AÑO: </b>' + this.series.name + '<br/> <b>MES:</b> ' +
                this.point.name.toUpperCase() + ' <br><b>TOTAL:</b> ' + this.point.y;
        }
    },
    colors: [ '#F5B918', '#7E91B9'],
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.y}'
            },
       }
    },
});
</script>

<script type="text/javascript">                                                 /*GRAFICA # 2 INDICE DE ROTACION DE LOS 2 AÑOS ANTERIORES*/

var categories = [
  <?php
  for ($i=0; $i < count($plantilla) ; $i++) {
    echo "'".$plantilla[$i]["MES"]."',";
  }
  ?>
];

var data_sdo_ant = [
  <?php
  for ($i=0; $i < count($plantilla) ; $i++) {
    if($plantilla[$i]["ACTIVO_ANTERIOR"]>0){
      echo number_format(($plantilla[$i]["BAJA_ANTERIOR"]/$plantilla[$i]["ACTIVO_ANTERIOR"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }
  }
  ?>
];

var data_ant = [
  <?php
  for ($i=0; $i < count($plantilla) ; $i++) {
    if($plantilla[$i]["ACTIVO"]>0){
      echo $valor_calculo = number_format(($plantilla[$i]["BAJA"]/$plantilla[$i]["ACTIVO"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }

  }
  ?>
];

Highcharts.chart('graf_2', {
    chart: {
        type: 'spline'
    },
    credits: {
      enabled: false
    },
    title: {
        text: '<b>INDICE DE ROTACION DE PERSONAL <?=$anio_dos?> VS <?=$anio_uno?></b>'
    },
    xAxis: {
        categories: categories
    },
    yAxis: {
        title: {
            text: '% INDICE DE ROTACION'
        },
        labels: {
            formatter: function () {
                return this.value + '%';
            }
        }
    },
    tooltip: {
        crosshairs: true,
        shared: true
    },
    colors: [ '#F5B918', '#7E91B9'],
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.y}'
            },
       }
    },
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.y}%'
            },
       }
    },
    /*plotOptions: {
        spline: {
            marker: {
                radius: 4,
                lineWidth: 1
            }
        }
    },*/
    series: [{
        name: '<?=$anio_dos?>',
        marker: {
            symbol: 'square'
        },
        data: data_sdo_ant

    }, {
        name: '<?=$anio_uno?>',
        marker: {
            symbol: 'square'
        },
        data: data_ant
    }]
});
</script>

<script type="text/javascript">                                                 /*GRAFICA # 4 INDICE DE ROTACION ACUMULADA*/

var categories = [
  <?php
  for ($i=0; $i < count($plantilla) ; $i++) {
    echo "'".$plantilla[$i]["MES"]."',";
  }
  ?>
];

var data_sdo_ant = [
  <?php
  $ind_acu_sdo_anio=0;
  for ($i=0; $i < count($plantilla) ; $i++) {
    if($plantilla[$i]["ACTIVO_ANTERIOR"]>0){
      $ind_sdo_ant= number_format(($plantilla[$i]["BAJA_ANTERIOR"]/$plantilla[$i]["ACTIVO_ANTERIOR"]) * 100, 2);
      $ind_acu_sdo_anio=$ind_acu_sdo_anio+$ind_sdo_ant;
      echo $ind_acu_sdo_anio.",";
    }else {
      $ind_acu_sdo_anio=0;
      echo $ind_acu_sdo_anio.",";
    }

  }
  ?>
];

var data_ant = [
  <?php
  $ind_acu_ant_anio=0;
  for ($i=0; $i < count($plantilla) ; $i++) {
    if($plantilla[$i]["ACTIVO"]>0){
      $ind_ant = number_format(($plantilla[$i]["BAJA"]/$plantilla[$i]["ACTIVO"]) * 100, 2);
      $ind_acu_ant_anio=$ind_acu_ant_anio+$ind_ant;
      echo $ind_acu_ant_anio.",";
    }else {
      $ind_acu_ant_anio=0;
      echo $ind_acu_ant_anio.",";
    }

  }
  ?>
];

Highcharts.chart('graf_4', {
    chart: {
        type: 'spline'
    },
    credits: {
      enabled: false
    },
    title: {
        text: '<b>INDICE DE ROTACION ACUMULADA <?=$anio_dos?> VS <?=$anio_uno?></b>'
    },
    xAxis: {
        categories: categories
    },
    yAxis: {
        title: {
            text: '% INDICE DE ROTACION'
        },
        labels: {
            formatter: function () {
                return this.value + '%';
            }
        }
    },
    tooltip: {
        crosshairs: true,
        shared: true
    },
    colors: [ '#F5B918', '#7E91B9'],
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.y}'
            },
       }
    },
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.y}%'
            },
       }
    },
    /*plotOptions: {
        spline: {
            marker: {
                radius: 4,
                lineWidth: 1
            }
        }
    },*/
    series: [{
        name: '<?=$anio_dos?>',
        marker: {
            symbol: 'square'
        },
        data: data_sdo_ant

    }, {
        name: '<?=$anio_uno?>',
        marker: {
            symbol: 'square'
        },
        data: data_ant
    }]
});
</script>

<script type="text/javascript">                                                 /*GRAFICA # 3 INDICE DE ROTACION DE LOS 2 AÑOS ANTERIORES*/

var categories = [
  <?php
  for ($i=0; $i < count($rotacion_plaza) ; $i++) {
    echo "'".$rotacion_plaza[$i]["PLAZA"]."',";
  }
  ?>
];

var data_sdo_ant = [
  <?php
  for ($i=0; $i < count($rotacion_plaza) ; $i++) {
    if($rotacion_plaza[$i]["ACTIVO_ANT"]>0){
      echo number_format(($rotacion_plaza[$i]["BAJA_ANT"]/$rotacion_plaza[$i]["ACTIVO_ANT"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }

  }
  ?>
];

var data_ant = [
  <?php
  for ($i=0; $i < count($rotacion_plaza) ; $i++) {
    if($rotacion_plaza[$i]["ACTIVO"]){
      echo $valor_calculo = number_format(($rotacion_plaza[$i]["BAJA"]/$rotacion_plaza[$i]["ACTIVO"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }

  }
  ?>
];

Highcharts.chart('graf_3', {
    chart: {
        type: 'spline'
    },
    credits: {
      enabled: false
    },
    title: {
        text: '<b>INDICE DE ROTACION DE PERSONAL POR PLAZA <?=$anio_dos?> VS <?=$anio_uno?></b>'
    },
    xAxis: {
        categories: categories
    },
    yAxis: {
        title: {
            text: '% INDICE DE ROTACION'
        },
        labels: {
            formatter: function () {
                return this.value + '%';
            }
        }
    },
    tooltip: {
        crosshairs: true,
        shared: true
    },
    colors: [ '#F5B918', '#7E91B9'],
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.y}'
            },
       }
    },
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.y}%'
            },
       }
    },
    /*plotOptions: {
        spline: {
            marker: {
                radius: 4,
                lineWidth: 1
            }
        }
    },*/
    series: [{
        name: '<?=$anio_dos?>',
        marker: {
            symbol: 'square'
        },
        data: data_sdo_ant

    }, {
        name: '<?=$anio_uno?>',
        marker: {
            symbol: 'square'
        },
        data: data_ant
    }]
});
</script>


<script type="text/javascript">                                                 /*TABLA # 1 CAUSAS DE BAJA DE 2 AÑOS ANTERIORES AL ACTUAL*/
$(document).ready(function() {

    $('#tabla_1').DataTable( {
      "bPaginate": false,
      "bInfo": false,
      "searching":false,
      "ordering": false,
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

    dom: 'Bfrtip',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {
                columns: ':visible'
            },
            title: 'CAUSAS DE BAJA <?=$anio_dos?>',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'CAUSAS DE BAJA <?=$anio_dos?>',
          },
        ],
    });

});
</script>

<script type="text/javascript">                                                 /*TABLA # 2 CAUSAS DE BAJA DE 1 AÑO ANTERIOR AL ACTUAL*/
$(document).ready(function() {

    $('#tabla_2').DataTable( {
      "bPaginate": false,
      "bInfo": false,
      "searching":false,
      "ordering": false,
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

    dom: 'Bfrtip',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {
                columns: ':visible'
            },
            title: 'CAUSAS DE BAJA <?=$anio_uno?>',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'CAUSAS DE BAJA <?=$anio_uno?>',
          },
        ],
    });

});
</script>

<script type="text/javascript">                                                 /*TABLA # 3 COMPARATIVO DE BAJAS 2 AÑOS ANTERIORES*/
$(document).ready(function() {

    $('#tabla_3').DataTable( {
      "bPaginate": false,
      "bInfo": false,
      "searching":false,
      "ordering": false,
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

    dom: 'Bfrtip',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {
                columns: ':visible'
            },
            title: 'COMPARATIVO DE CAUSAS DE BAJA AÑOS: <?=$anio_dos?> y <?=$anio_uno?>',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'COMPARATIVO DE CAUSAS DE BAJA AÑOS: <?=$anio_dos?> y <?=$anio_uno?>',
          },
        ],
    });

});
</script>

<script>                                                                        /* SUMATORIA DE COLUMNAS DE TABLA #1 */
$(document).ready(function(){

  var suma=0;
  <?php

  $motivos=$rotPersonalGral->motivos($anio_dos);
  for($x=1; $x<=count($motivos); $x++){ ?>
    //var suma = 0;
    var columna=<?=$x?>;
    $('#tabla_1 tr').each(function(){
     suma += parseInt($(this).find('td').eq(columna).text()||0,10)
      })
      //console.log(suma);
      $('#tabla_1 tfoot tr td b').eq(columna).text( suma);
      suma=0;
  <?php } ?>
});
</script>


<script>                                                                        /* SUMATORIA DE COLUMNAS DE TABLA #2 */
$(document).ready(function(){

  var suma=0;
  <?php
  $motivos=$rotPersonalGral->motivos($anio_uno);
  for($x=1; $x<=count($motivos); $x++){ ?>
    //var suma = 0;
    var columna=<?=$x?>;
    $('#tabla_2 tr').each(function(){
     suma += parseInt($(this).find('td').eq(columna).text()||0,10)
      })
      $('#tabla_2 tfoot tr td b').eq(columna).text( suma);
      suma=0;
  <?php } ?>
});
</script>

<script>
$(".btn_fil").on("click", function(){

  fil_anio_uno = $('#fil_anio_uno').val();
  fil_anio_dos = $('#fil_anio_dos').val();
  //url = '?anio_uno='+fil_anio_uno;
  url = '?anio_uno='+fil_anio_uno+'&anio_dos='+fil_anio_dos;
  location.href = url;

});
</script>


<?php conexion::cerrar($conn); ?>
