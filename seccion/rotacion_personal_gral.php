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


if (isset($_GET["anio_uno"]) && isset($_GET["anio_dos"])) {
    $anio_uno =$_GET["anio_uno"];
    $anio_dos =$_GET["anio_dos"];
}

$nomina=0;
if (isset($_GET["nom"])) {
    $nomina =$_GET["nom"];
}

$plantilla_semanal=$rotPersonalGral->plantilla_trabajadores($anio_uno, $anio_dos, 2);
$plantilla_quincenal=$rotPersonalGral->plantilla_trabajadores($anio_uno, $anio_dos, 1);

$rotacion_plaza_semanal=$rotPersonalGral-> rotacion_plaza($anio_uno, $anio_dos, 2);
$rotacion_plaza_quincenal=$rotPersonalGral-> rotacion_plaza($anio_uno, $anio_dos, 1);

$bajas_puesto_semanal=$rotPersonalGral->comparativo_puestos_bajas($anio_uno, $anio_dos, 2);
$bajas_puesto_quincenal=$rotPersonalGral->comparativo_puestos_bajas($anio_uno, $anio_dos, 1);

$imputable_semanal=$rotPersonalGral->detalle_depto_imputable($anio_uno, $anio_dos, 2);
$imputable_quincenal=$rotPersonalGral->detalle_depto_imputable($anio_uno, $anio_dos, 1);


$sum_act_semanal=0; $sum_act_semanal_sdo=0; $sum_inac_semanal=0; $sum_inac_semanal_sdo=0; $rotacion=0; $rotacion_sdo=0; $sumatoria_rot=0; $sumatoria_rot_sdo=0;
$sum_act_quincenal=0; $sum_act_quincenal_sdo=0; $sum_inac_quincenal=0; $sum_inac_quincenal_sdo=0; $rotacion_q=0; $rotacion_sdo_q=0; $sumatoria_rot_q=0; $sumatoria_rot_sdo_q=0;

for($i=0; $i<count($plantilla_semanal); $i ++){
  $sum_act_semanal=$sum_act_semanal+$plantilla_semanal[$i]["ACTIVO"];
  $sum_act_semanal_sdo=$sum_act_semanal_sdo+$plantilla_semanal[$i]["ACTIVO_ANTERIOR"];

  $sum_inac_semanal=$sum_inac_semanal+$plantilla_semanal[$i]["BAJA"];
  $sum_inac_semanal_sdo=$sum_inac_semanal_sdo+$plantilla_semanal[$i]["BAJA_ANTERIOR"];

  if($plantilla_semanal[$i]["ACTIVO"]>0){
    $rotacion=($plantilla_semanal[$i]["BAJA"]/$plantilla_semanal[$i]["ACTIVO"])*100;
  }else {
    $rotacion=0;
  }

  if($plantilla_semanal[$i]["ACTIVO_ANTERIOR"]>0){
    $rotacion_sdo=($plantilla_semanal[$i]["BAJA_ANTERIOR"]/$plantilla_semanal[$i]["ACTIVO_ANTERIOR"])*100;
  }else {
    $rotacion_sdo=0;
  }

  $sumatoria_rot=number_format(($sumatoria_rot+$rotacion),2);
  $sumatoria_rot_sdo=number_format(($sumatoria_rot_sdo+$rotacion_sdo),2);
}

$promedio_ant_act=number_format(($sum_act_semanal/12),2);
$promedio_sdo_act=number_format(($sum_act_semanal_sdo/12),2);

$promedio_ant_inac=number_format(($sum_inac_semanal/12),2);
$promedio_sdo_inac=number_format(($sum_inac_semanal_sdo/12),2);

$promedio_rot=number_format(($sumatoria_rot/12),2);
$promedio_rot_sdo=number_format(($sumatoria_rot_sdo/12),2);

for($i=0; $i<count($plantilla_quincenal); $i ++){
  $sum_act_quincenal=$sum_act_quincenal+$plantilla_quincenal[$i]["ACTIVO"];
  $sum_act_quincenal_sdo=$sum_act_quincenal_sdo+$plantilla_quincenal[$i]["ACTIVO_ANTERIOR"];

  $sum_inac_quincenal=$sum_inac_quincenal+$plantilla_quincenal[$i]["BAJA"];
  $sum_inac_quincenal_sdo=$sum_inac_quincenal_sdo+$plantilla_quincenal[$i]["BAJA_ANTERIOR"];

  if($plantilla_quincenal[$i]["ACTIVO"]>0){
    $rotacion_q=($plantilla_quincenal[$i]["BAJA"]/$plantilla_quincenal[$i]["ACTIVO"])*100;
  }else {
    $rotacion_q=0;
  }

  if($plantilla_quincenal[$i]["ACTIVO_ANTERIOR"]>0){
    $rotacion_sdo_q=($plantilla_quincenal[$i]["BAJA_ANTERIOR"]/$plantilla_quincenal[$i]["ACTIVO_ANTERIOR"])*100;
  }else {
    $rotacion_sdo_q=0;
  }

  $sumatoria_rot_q=number_format(($sumatoria_rot_q+$rotacion_q),2);
  $sumatoria_rot_sdo_q=number_format(($sumatoria_rot_sdo_q+$rotacion_sdo_q),2);
}

$promedio_ant_act_q=number_format(($sum_act_quincenal/12),2);
$promedio_sdo_act_q=number_format(($sum_act_quincenal_sdo/12),2);

$promedio_ant_inac_q=number_format(($sum_inac_quincenal/12),2);
$promedio_sdo_inac_q=number_format(($sum_inac_quincenal_sdo/12),2);

$promedio_rot_q=number_format(($sumatoria_rot_q/12),2);
$promedio_rot_sdo_q=number_format(($sumatoria_rot_sdo_q/12),2);


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

  <section>
    <div class="row">

      <div class="col-md-12">
        <div class="box box-primary">

          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-tags"></i> Detalle por Nómina</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>

          <div class="box-body">

                <div class="col-md-3">                                          <!--PROMEDIO MENSUAL DE PERSONAL ACTIVO-->
                  <div class="small-box bg-aqua">
                    <div class="inner">
                      <h4 class="text-center"><b>Nómina Semanal</b></h4>
                      <h5 class="text-center">AÑO <?php echo $anio_dos.": ".$promedio_sdo_act;?></h5>
                      <h5 class="text-center">AÑO <?php echo $anio_uno.": ".$promedio_ant_act;?></h5>
                      <h4 class="text-center"><b>Nómina Quincenal</b></h4>
                      <h5 class="text-center">AÑO <?php echo $anio_dos.": ".$promedio_sdo_act_q;?></h5>
                      <h5 class="text-center">AÑO <?php echo $anio_uno.": ".$promedio_ant_act_q;?></h5>
                    </div>
                    <div class="icon">
                      <i class="fa fa-check-square-o"></i>
                    </div>
                    <a class="small-box-footer"><b>PROM. MENSUAL PERSONAL ACTIVO </b></a>
                  </div>
                </div>

                <div class="col-md-3">                                          <!--PROMEDIO MENSUAL DE PERSONAL INACTIVO-->
                  <div class="small-box bg-red">
                    <div class="inner">
                      <h4 class="text-center"><b>Nómina Semanal</b></h4>
                      <h5 class="text-center">AÑO <?php echo $anio_dos.": ".$promedio_sdo_inac;?></h5>
                      <h5 class="text-center">AÑO <?php echo $anio_uno.": ".$promedio_ant_inac;?></h5>
                      <h4 class="text-center"><b>Nómina Quincenal</b></h4>
                      <h5 class="text-center">AÑO <?php echo $anio_dos.": ".$promedio_sdo_inac_q;?></h5>
                      <h5 class="text-center">AÑO <?php echo $anio_uno.": ".$promedio_ant_inac_q;?></h5>
                    </div>
                    <div class="icon">
                      <i class="fa fa-times"></i>
                    </div>
                    <a class="small-box-footer"><b>PROM. MENSUAL PERSONAL INAC. </b></a>
                  </div>
                </div>

                <div class="col-md-3">                                          <!--PROMEDIO ROTACION MENSUAL-->
                  <div class="small-box bg-verde">
                    <div class="inner">
                      <h4 class="text-center"><b>Nómina Semanal</b></h4>
                      <h5 class="text-center">AÑO <?php echo $anio_dos.": ".$promedio_rot_sdo."%";?></h5>
                      <h5 class="text-center">AÑO <?php echo $anio_uno.": ".$promedio_rot."%";?></h5>
                      <h4 class="text-center"><b>Nómina Quincenal</b></h4>
                      <h5 class="text-center">AÑO <?php echo $anio_dos.": ".$promedio_rot_sdo_q."%";?></h5>
                      <h5 class="text-center">AÑO <?php echo $anio_uno.": ".$promedio_rot_q."%";?></h5>
                    </div>
                    <div class="icon">
                      <i class="fa fa-refresh"></i>
                    </div>
                    <a class="small-box-footer"><b>PROM. ROTACION MENSUAL </b></a>
                  </div>
                </div>

                <div class="col-md-3">                                          <!--PROMEDIO ROTACION ANUAL-->
                  <div class="small-box bg-morado">
                    <div class="inner">
                      <h4 class="text-center"><b>Nómina Semanal</b></h4>
                      <h5 class="text-center">AÑO <?php echo $anio_dos.": ".$sumatoria_rot_sdo."%";?></h5>
                      <h5 class="text-center">AÑO <?php echo $anio_uno.": ".$sumatoria_rot."%";?></h5>
                      <h4 class="text-center"><b>Nómina Quincenal</b></h4>
                      <h5 class="text-center">AÑO <?php echo $anio_dos.": ".$sumatoria_rot_sdo_q."%";?></h5>
                      <h5 class="text-center">AÑO <?php echo $anio_uno.": ".$sumatoria_rot_q."%";?></h5>
                    </div>
                    <div class="icon">
                      <i class="fa fa-refresh"></i>
                    </div>
                    <a class="small-box-footer"><b>PROM. ROTACION ANUAL </b></a>
                  </div>
                </div>

          </div>
        </div>
      </div>

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
            <th><?php echo "PERSONAL ACTIVO ".$anio_dos." SEMANAL"?></th>
            <th><?php echo "PERSONAL ACTIVO ".$anio_uno." SEMANAL"?></th>
            <th><?php echo "PERSONAL ACTIVO ".$anio_dos." QUINCENAL"?></th>
            <th><?php echo "PERSONAL ACTIVO ".$anio_uno." QUINCENAL"?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          for ($i=0; $i <count($plantilla_semanal) ; $i++) {
          ?>
          <tr>
            <th><?php echo $plantilla_semanal[$i]["MES"] ?></th>
            <td><?php echo $plantilla_semanal[$i]["ACTIVO_ANTERIOR"] ?></td>
            <td><?php echo $plantilla_semanal[$i]["ACTIVO"] ?></td>
            <td><?php echo $plantilla_quincenal[$i]["ACTIVO_ANTERIOR"] ?></td>
            <td><?php echo $plantilla_quincenal[$i]["ACTIVO"] ?></td>
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
    </div>


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
    </div>


    <table id="datatable_2" style="display: none">
      <thead>
        <tr>
          <th>Mes</th>
          <th><?php echo "NÓM. SEMANAL ".$anio_dos." TH"?></th>
          <th><?php echo "NÓM. SEMANAL ".$anio_dos." OTROS"?></th>
          <th><?php echo "NÓM. SEMANAL ".$anio_uno." TH"?></th>
          <th><?php echo "NÓM. SEMANAL ".$anio_uno." OTROS"?></th>

          <th><?php echo "NÓM. QUIN ".$anio_dos." TH"?></th>
          <th><?php echo "NÓM. QUIN ".$anio_dos." OTROS"?></th>
          <th><?php echo "NÓM. QUIN ".$anio_uno." TH"?></th>
          <th><?php echo "NÓM. QUIN ".$anio_uno." OTROS"?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        for ($i=0; $i <count($imputable_semanal) ; $i++) {
        ?>
        <tr>
          <td><?php echo $imputable_semanal[$i]["MES"] ?></td>
          <td><?php echo $imputable_semanal[$i]["TH"] ?></td>
          <td><?php echo $imputable_semanal[$i]["OTROS"] ?></td>
          <td><?php echo $imputable_semanal[$i]["TH_ANT"] ?></td>
          <td><?php echo $imputable_semanal[$i]["OTROS_ANT"] ?></td>

          <td><?php echo $imputable_quincenal[$i]["TH"] ?></td>
          <td><?php echo $imputable_quincenal[$i]["OTROS"] ?></td>
          <td><?php echo $imputable_quincenal[$i]["TH_ANT"] ?></td>
          <td><?php echo $imputable_quincenal[$i]["OTROS_ANT"] ?></td>

        </tr>
        <?php } ?>
      </tbody>
  </table>

    <div class="col-md-9">                                                      <!--INICIA GRAFICA COMPARATIVA DE DEPTO IMPUTABLE-->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> DETALLE DEPTO IMPUTABLE</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">

          <div class="col-md-6">
            <div class="small-box bg-red">
              <div class="inner">
                <h4 class="text-center"><b>Nómina Semanal</b></h4>
                <table width="100%">
                  <thead>
                    <tr>
                      <th></th>
                      <th>TOTAL</th>
                      <th>TH</th>
                      <th>OTROS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td align="center"><?php echo $anio_dos ?></td>
                      <?php $total_imp_sem=$rotPersonalGral->total_detalle_depto_imputable($anio_uno, $anio_dos, 2);
                      for($i=0;$i<count($total_imp_sem); $i++){?>
                      <td align="center"><?php echo $total_imp_sem[$i]['TOTAL'] ?></td>
                      <td align="center"><?php echo $total_imp_sem[$i]['TH'] ?></td>
                      <td align="center"><?php echo $total_imp_sem[$i]['OTROS'] ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                      <?php for($i=0;$i<count($total_imp_sem); $i++){?>
                      <td align="center"><?php echo $anio_uno ?></td>
                      <td align="center"><?php echo $total_imp_sem[$i]['TOTAL_ANT'] ?></td>
                      <td align="center"><?php echo $total_imp_sem[$i]['TH_ANT'] ?></td>
                      <td align="center"><?php echo $total_imp_sem[$i]['OTROS_ANT'] ?></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>

              </div>
              <div class="icon">
                <i class="fa fa-times"></i>
              </div>
            </div>
          </div>

          <div class="col-md-6">                                          <!--PROMEDIO ROTACION ANUAL-->
            <div class="small-box bg-red">
              <div class="inner">
                <h4 class="text-center"><b>Nómina Quincenal</b></h4>
                <table width="100%">
                  <thead>
                    <tr>
                      <th></th>
                      <th>TOTAL</th>
                      <th>TH</th>
                      <th>OTROS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td align="center"><?php echo $anio_dos ?></td>
                      <?php $total_imp_quin=$rotPersonalGral->total_detalle_depto_imputable($anio_uno, $anio_dos, 1);
                      for($i=0;$i<count($total_imp_quin); $i++){?>
                      <td align="center"><?php echo $total_imp_quin[$i]['TOTAL'] ?></td>
                      <td align="center"><?php echo $total_imp_quin[$i]['TH'] ?></td>
                      <td align="center"><?php echo $total_imp_quin[$i]['OTROS'] ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                      <?php for($i=0;$i<count($total_imp_quin); $i++){?>
                      <td align="center"><?php echo $anio_uno ?></td>
                      <td align="center"><?php echo $total_imp_quin[$i]['TOTAL_ANT'] ?></td>
                      <td align="center"><?php echo $total_imp_quin[$i]['TH_ANT'] ?></td>
                      <td align="center"><?php echo $total_imp_quin[$i]['OTROS_ANT'] ?></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
              <div class="icon">
                <i class="fa fa-times"></i>
              </div>
            </div>
          </div>

          <div id="graf_4" class="col-md-12" style="height:380px;"></div>
        </div>
      </div>
    </div>


    <div class="col-md-9">                                                      <!--INICIA TABLA COMPARATIVA GENERAL DE CAUSAS BAJA NOMINA SEMANAL-->
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> BAJAS POR PUESTO NOMINA SEMANAL</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>

        <div class="box-body">
          <div class="table-responsive" id="container">
            <center><h4><b>COMPARATIVA BAJAS POR PUESTO NOMINA SEMANAL <?=$anio_dos?> VS <?=$anio_uno?></b></h4></center>
            <a style="float: right;" href="<?= "?anio_uno=".$anio_uno."&anio_dos=".$anio_dos."&nom=2"; ?>" class="small-box-footer">Detalle por Año <i class="fa fa-arrow-circle-right"></i></a>
            <table id="tabla_1" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                  <th class="small" bgcolor="#4791de"><font color="white">BAJAS <?=$anio_dos?></font></th>
                  <th class="small" bgcolor="#4791de"><font color="white">BAJAS <?=$anio_uno?></font></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sum_anio_ant_s=0; $sum_sdo_anio_ant_s=0;
                for ($i=0; $i <count($bajas_puesto_semanal) ; $i++) {
                  $sum_anio_ant_s=$sum_anio_ant_s+$bajas_puesto_semanal[$i]["TOTAL_ANT"];
                  $sum_sdo_anio_ant_s=$sum_sdo_anio_ant_s+$bajas_puesto_semanal[$i]["TOTAL_ANIO_ANT"];
                  ?>
                  <tr>
                    <td ><?php echo $bajas_puesto_semanal[$i]["NOM_PUESTO"] ?></td>
                    <td align="center"><?php echo number_format($bajas_puesto_semanal[$i]["TOTAL_ANIO_ANT"]) ?></td>
                    <td align="center"><?php echo number_format($bajas_puesto_semanal[$i]["TOTAL_ANT"]) ?></td>
                  </tr>
                <?php }?>
              </tbody>
              <tfoot>
                <tr>
                  <td align="center"><b>TOTAL POR PUESTO:</b></td>
                  <td align="center"><b><?php echo $sum_sdo_anio_ant_s;?></b></td>
                  <td align="center"><b><?php echo $sum_anio_ant_s;?></b></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <?php if($nomina==2){ ?>
    <div class="col-md-9">                                                      <!--INICIA TABLA DETALLE DE CAUSAS BAJA AÑO ANTERIOR NOMINA SEMANAL-->
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> CAUSAS DE BAJA <?php echo $anio_dos?> NOMINA SEMANAL</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>

        <div class="box-body">
          <div class="table-responsive" id="container">
            <center><h4><b>CAUSAS DE BAJA DEL AÑO:  <?=$anio_dos?></b></h4></center>
            <table id="tabla_3" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                  <?php $motivos=$rotPersonalGral->motivos($anio_dos, 2);
                  for ($i=0; $i <count($motivos) ; $i++) {?>
                    <th><?php echo $motivos[$i]["MOTIVO"] ?></th>
                  <?php } ?>
                  <th>TOTAL POR PUESTO</th>
                  <th >%</th>
                </tr>
              </thead>
              <tbody>
                <?php if(count($motivos)>0){
                  $puestos=$rotPersonalGral->causas_baja_anio_ant($motivos, $anio_dos, 2);
                  $contador=0; $total=0; $porcentaje=0;
                  for ($i=0; $i <count($puestos) ; $i++) {
                    $contador=0;?>
                    <tr>
                      <td bgcolor="#A7CAE1"><?php echo $puestos[$i]["NOM_PUESTO"] ?></td>
                      <?php for($x=0; $x <count($motivos); $x++){
                        $nom_motivo = str_replace(" ", "_", $motivos[$x]['MOTIVO']);?>
                        <td align="center"><?php echo number_format($puestos[$i][$nom_motivo]) ?></td>
                        <?php
                        $contador=$contador+$puestos[$i][$nom_motivo];
                        $total=$total+$puestos[$i][$nom_motivo];}?>
                        <td align="center"><?php echo $contador ?></td>
                        <td align="center"><?php echo number_format(($contador/$sum_sdo_anio_ant_s)*100,2) ?></td>
                      </tr>
                      <?php
                      $porcentaje=$porcentaje+ (($contador/$sum_sdo_anio_ant_s)*100);
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
     </div>

     <div class="col-md-9">                                                     <!--INICIA TABLA DETALLE DE CAUSAS BAJA AÑO ANTERIOR NOMINA SEMANAL-->
       <div class="box box-success">
         <div class="box-header with-border">
           <h3 class="box-title"><i class="fa fa-table"></i> CAUSAS DE BAJA <?php echo $anio_uno?> NOMINA SEMANAL</h3>
           <div class="box-tools pull-right">
             <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
           </div>
         </div>

         <div class="box-body">
           <div class="table-responsive" id="container">
             <center><h4><b>CAUSAS DE BAJA DEL AÑO:  <?=$anio_uno?></b></h4></center>
             <table id="tabla_4" class="table table-striped table-bordered" cellspacing="0" width="100%">
               <thead>
                 <tr>
                   <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                   <?php $motivos=$rotPersonalGral->motivos($anio_uno, 2);
                   for ($i=0; $i <count($motivos) ; $i++) {?>
                     <th><?php echo $motivos[$i]["MOTIVO"] ?></th>
                   <?php } ?>
                   <th>TOTAL POR PUESTO</th>
                   <th >%</th>
                 </tr>
               </thead>
               <tbody>
                 <?php if(count($motivos)>0){
                   $puestos=$rotPersonalGral->causas_baja_anio_ant($motivos, $anio_uno, 2);
                   $contador=0; $total=0; $porcentaje=0;
                   for ($i=0; $i <count($puestos) ; $i++) {
                     $contador=0;?>
                     <tr>
                       <td bgcolor="#A7CAE1"><?php echo $puestos[$i]["NOM_PUESTO"] ?></td>
                       <?php for($x=0; $x <count($motivos); $x++){
                         $nom_motivo = str_replace(" ", "_", $motivos[$x]['MOTIVO']);?>
                         <td align="center"><?php echo number_format($puestos[$i][$nom_motivo]) ?></td>
                         <?php
                         $contador=$contador+$puestos[$i][$nom_motivo];
                         $total=$total+$puestos[$i][$nom_motivo];}?>
                         <td align="center"><?php echo $contador ?></td>
                         <td align="center"><?php echo number_format(($contador/$sum_anio_ant_s)*100,2) ?></td>
                       </tr>
                       <?php
                       $porcentaje=$porcentaje+ (($contador/$sum_anio_ant_s)*100);
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
      </div>

      <div class="col-md-9">                                                     <!--INICIA TABLA DETALLE DE CAUSAS BAJA NOMINA SEMANAL-->
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-table"></i> DETALLE BAJAS POR PUESTO NOMINA SEMANAL</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>

          <div class="box-body">
            <div class="table-responsive" id="container">
              <center><h4><b>DETALLE DE BAJAS NOMINA SEMANAL <?=$anio_dos?> VS <?=$anio_uno?></b></h4></center>
              <table id="tabla_7" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                    <th># CONTRATO</th>
                    <th>MOTIVO</th>
                    <th >OBSERVACIONES</th>
                    <th>DEPTO. IMPUTABLE</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th bgcolor="#A7CAE1"> AÑO <?php echo $anio_dos ?></th>
                    <td bgcolor="#A7CAE1"></td>
                    <td bgcolor="#A7CAE1"></td>
                    <td bgcolor="#A7CAE1"></td>
                    <td bgcolor="#A7CAE1"></td>
                  </tr>
                  <?php
                  $detalle_bajas_semanal=$rotPersonalGral->detalle_baja_contrato($anio_uno, $anio_dos, 2);
                  for($i=0; $i<count($detalle_bajas_semanal); $i++){
                    if($detalle_bajas_semanal[$i]["ANIO"]==$anio_dos){?>
                    <tr>
                      <td><?php echo $detalle_bajas_semanal[$i]["NOM_PUESTO"] ?></td>
                      <td align="center"><?php echo $detalle_bajas_semanal[$i]["CONTRATO"] ?></td>
                      <td ><?php echo $detalle_bajas_semanal[$i]["MOTIVO"] ?></td>
                      <td ><?php echo $detalle_bajas_semanal[$i]["OBSERVACIONES"] ?></td>
                      <td align="center"><?php echo $detalle_bajas_semanal[$i]["DEPTO_IMPUTABLE"] ?></td>
                    </tr>
                  <?php }} ?>
                  <tr>
                    <th bgcolor="#A7CAE1"> AÑO <?php echo $anio_uno ?></th>
                    <td bgcolor="#A7CAE1"></td>
                    <td bgcolor="#A7CAE1"></td>
                    <td bgcolor="#A7CAE1"></td>
                    <td bgcolor="#A7CAE1"></td>
                  </tr>
                  <?php
                  for($i=0; $i<count($detalle_bajas_semanal); $i++){
                    if($detalle_bajas_semanal[$i]["ANIO"]==$anio_uno){?>
                    <tr>
                      <td><?php echo $detalle_bajas_semanal[$i]["NOM_PUESTO"] ?></td>
                      <td align="center"><?php echo $detalle_bajas_semanal[$i]["CONTRATO"] ?></td>
                      <td ><?php echo $detalle_bajas_semanal[$i]["MOTIVO"] ?></td>
                      <td ><?php echo $detalle_bajas_semanal[$i]["OBSERVACIONES"] ?></td>
                      <td align="center"><?php echo $detalle_bajas_semanal[$i]["DEPTO_IMPUTABLE"] ?></td>
                    </tr>
                  <?php }} ?>
                  </tbody>
                  <tfoot>
                    <tr>
                   </tr>
                 </tfoot>
               </table>
             </div>
           </div>
         </div>
       </div>
      <?php  } ?>

    <div class="col-md-9">                                                      <!--INICIA TABLA COMPARATIVA GENERAL DE CAUSAS BAJA NOMINA SEMANAL-->
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> BAJAS POR PUESTO NOMINA QUINCENAL</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>

        <div class="box-body">
          <div class="table-responsive" id="container">
            <center><h4><b>COMPARATIVA DE BAJAS POR PUESTO NOMINA QUINCENAL <?=$anio_dos?> VS <?=$anio_uno?></b></h4></center>
            <a style="float: right;" href="<?= "?anio_uno=".$anio_uno."&anio_dos=".$anio_dos."&nom=1"; ?>" class="small-box-footer">Detalle por Año <i class="fa fa-arrow-circle-right"></i></a>
            <table id="tabla_2" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                  <th class="small" bgcolor="#4791de"><font color="white">BAJAS <?=$anio_dos?></font></th>
                  <th class="small" bgcolor="#4791de"><font color="white">BAJAS <?=$anio_uno?></font></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sum_anio_ant_q=0; $sum_sdo_anio_ant_q=0;
                for ($i=0; $i <count($bajas_puesto_quincenal) ; $i++) {
                  $sum_anio_ant_q=$sum_anio_ant_q+$bajas_puesto_quincenal[$i]["TOTAL_ANT"];
                  $sum_sdo_anio_ant_q=$sum_sdo_anio_ant_q+$bajas_puesto_quincenal[$i]["TOTAL_ANIO_ANT"];
                  ?>
                  <tr>
                    <td ><?php echo $bajas_puesto_quincenal[$i]["NOM_PUESTO"] ?></td>
                    <td align="center"><?php echo number_format($bajas_puesto_quincenal[$i]["TOTAL_ANIO_ANT"]) ?></td>
                    <td align="center"><?php echo number_format($bajas_puesto_quincenal[$i]["TOTAL_ANT"]) ?></td>
                  </tr>
                <?php }?>
              </tbody>
              <tfoot>
                <tr>
                  <td align="center"><b>TOTAL POR PUESTO:</b></td>
                  <td align="center"><b><?php echo $sum_sdo_anio_ant_q;?></b></td>
                  <td align="center"><b><?php echo $sum_anio_ant_q;?></b></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

      <?php if($nomina==1){ ?>
      <div class="col-md-9">                                                      <!--INICIA TABLA DETALLE DE CAUSAS BAJA AÑO ANTERIOR NOMINA QUINCENAL-->
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-table"></i> CAUSAS DE BAJA <?php echo $anio_dos?> NOMINA QUINCENAL</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>

          <div class="box-body">
            <div class="table-responsive" id="container">
              <center><h4><b>CAUSAS DE BAJA DEL AÑO:  <?=$anio_dos?></b></h4></center>
              <table id="tabla_5" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                    <?php $motivos=$rotPersonalGral->motivos($anio_dos, 1);
                    for ($i=0; $i <count($motivos) ; $i++) {?>
                      <th><?php echo $motivos[$i]["MOTIVO"] ?></th>
                    <?php } ?>
                    <th>TOTAL POR PUESTO</th>
                    <th >%</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(count($motivos)>0){
                    $puestos=$rotPersonalGral->causas_baja_anio_ant($motivos, $anio_dos, 1);
                    $contador=0; $total=0; $porcentaje=0;
                    for ($i=0; $i <count($puestos) ; $i++) {
                      $contador=0;?>
                      <tr>
                        <td bgcolor="#A7CAE1"><?php echo $puestos[$i]["NOM_PUESTO"] ?></td>
                        <?php for($x=0; $x <count($motivos); $x++){
                          $nom_motivo = str_replace(" ", "_", $motivos[$x]['MOTIVO']);?>
                          <td align="center"><?php echo number_format($puestos[$i][$nom_motivo]) ?></td>
                          <?php
                          $contador=$contador+$puestos[$i][$nom_motivo];
                          $total=$total+$puestos[$i][$nom_motivo];}?>
                          <td align="center"><?php echo $contador ?></td>
                          <td align="center"><?php echo number_format(($contador/$sum_sdo_anio_ant_q)*100,2) ?></td>
                        </tr>
                        <?php
                        $porcentaje=$porcentaje+ (($contador/$sum_sdo_anio_ant_q)*100);
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
       </div>


       <div class="col-md-9">                                                      <!--INICIA TABLA DETALLE DE CAUSAS BAJA AÑO ANTERIOR NOMINA QUINCENAL-->
         <div class="box box-success">
           <div class="box-header with-border">
             <h3 class="box-title"><i class="fa fa-table"></i> CAUSAS DE BAJA <?php echo $anio_uno?> NOMINA QUINCENAL</h3>
             <div class="box-tools pull-right">
               <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
             </div>
           </div>

           <div class="box-body">
             <div class="table-responsive" id="container">
               <center><h4><b>CAUSAS DE BAJA DEL AÑO:  <?=$anio_uno?></b></h4></center>
               <table id="tabla_6" class="table table-striped table-bordered" cellspacing="0" width="100%">
                 <thead>
                   <tr>
                     <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                     <?php $motivos=$rotPersonalGral->motivos($anio_uno, 1);
                     for ($i=0; $i <count($motivos) ; $i++) {?>
                       <th><?php echo $motivos[$i]["MOTIVO"] ?></th>
                     <?php } ?>
                     <th>TOTAL POR PUESTO</th>
                     <th >%</th>
                   </tr>
                 </thead>
                 <tbody>
                   <?php if(count($motivos)>0){
                     $puestos=$rotPersonalGral->causas_baja_anio_ant($motivos, $anio_uno, 1);
                     $contador=0; $total=0; $porcentaje=0;
                     for ($i=0; $i <count($puestos) ; $i++) {
                       $contador=0;?>
                       <tr>
                         <td bgcolor="#A7CAE1"><?php echo $puestos[$i]["NOM_PUESTO"] ?></td>
                         <?php for($x=0; $x <count($motivos); $x++){
                           $nom_motivo = str_replace(" ", "_", $motivos[$x]['MOTIVO']);?>
                           <td align="center"><?php echo number_format($puestos[$i][$nom_motivo]) ?></td>
                           <?php
                           $contador=$contador+$puestos[$i][$nom_motivo];
                           $total=$total+$puestos[$i][$nom_motivo];}?>
                           <td align="center"><?php echo $contador ?></td>
                           <td align="center"><?php echo number_format(($contador/$sum_anio_ant_q)*100,2) ?></td>
                         </tr>
                         <?php
                         $porcentaje=$porcentaje+ (($contador/$sum_anio_ant_q)*100);
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
        </div>


       <div class="col-md-9">                                                     <!--INICIA TABLA DETALLE DE CAUSAS BAJA NOMINA SEMANAL-->
         <div class="box box-success">
           <div class="box-header with-border">
             <h3 class="box-title"><i class="fa fa-table"></i> DETALLE BAJAS POR PUESTO NOMINA QUINCENAL</h3>
             <div class="box-tools pull-right">
               <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
             </div>
           </div>

           <div class="box-body">
             <div class="table-responsive" id="container">
               <center><h4><b>DETALLE DE BAJAS NOMINA QUINCENAL <?=$anio_dos?> VS <?=$anio_uno?></b></h4></center>
               <table id="tabla_8" class="table table-striped table-bordered" cellspacing="0" width="100%">
                 <thead>
                   <tr>
                     <th class="small" bgcolor="#4791de"><font color="white">PUESTO</font></th>
                     <th># CONTRATO</th>
                     <th>MOTIVO</th>
                     <th >OBSERVACIONES</th>
                     <th>DEPTO. IMPUTABLE</th>
                   </tr>
                 </thead>
                 <tbody>
                   <tr>
                     <th bgcolor="#A7CAE1"> AÑO <?php echo $anio_dos ?></th>
                     <td bgcolor="#A7CAE1"></td>
                     <td bgcolor="#A7CAE1"></td>
                     <td bgcolor="#A7CAE1"></td>
                     <td bgcolor="#A7CAE1"></td>
                   </tr>
                   <?php
                   $detalle_bajas_quincenal=$rotPersonalGral->detalle_baja_contrato($anio_uno, $anio_dos, 1);
                   for($i=0; $i<count($detalle_bajas_quincenal); $i++){
                     if($detalle_bajas_quincenal[$i]["ANIO"]==$anio_dos){?>
                     <tr>
                       <td><?php echo $detalle_bajas_quincenal[$i]["NOM_PUESTO"] ?></td>
                       <td align="center"><?php echo $detalle_bajas_quincenal[$i]["CONTRATO"] ?></td>
                       <td ><?php echo $detalle_bajas_quincenal[$i]["MOTIVO"] ?></td>
                       <td ><?php echo $detalle_bajas_quincenal[$i]["OBSERVACIONES"] ?></td>
                       <td align="center"><?php echo $detalle_bajas_quincenal[$i]["DEPTO_IMPUTABLE"] ?></td>
                     </tr>
                   <?php }} ?>
                   <tr>
                     <th bgcolor="#A7CAE1"> AÑO <?php echo $anio_uno ?></th>
                     <td bgcolor="#A7CAE1"></td>
                     <td bgcolor="#A7CAE1"></td>
                     <td bgcolor="#A7CAE1"></td>
                     <td bgcolor="#A7CAE1"></td>
                   </tr>
                   <?php
                   for($i=0; $i<count($detalle_bajas_quincenal); $i++){
                     if($detalle_bajas_quincenal[$i]["ANIO"]==$anio_uno){?>
                     <tr>
                       <td><?php echo $detalle_bajas_quincenal[$i]["NOM_PUESTO"] ?></td>
                       <td align="center"><?php echo $detalle_bajas_quincenal[$i]["CONTRATO"] ?></td>
                       <td ><?php echo $detalle_bajas_quincenal[$i]["MOTIVO"] ?></td>
                       <td ><?php echo $detalle_bajas_quincenal[$i]["OBSERVACIONES"] ?></td>
                       <td align="center"><?php echo $detalle_bajas_quincenal[$i]["DEPTO_IMPUTABLE"] ?></td>
                     </tr>
                   <?php }} ?>
                   </tbody>
                   <tfoot>
                     <tr>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>

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
        type: 'column'
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
            return '<b>' + this.series.name + '</b><br/> <b>MES:</b> ' +
                this.point.name.toUpperCase() + ' <br><b>TOTAL:</b> ' + this.point.y;
        }
    },
    colors: [ '#F5B918', '#8DD949', '#139DDE', '#F8F406'],
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

<script type="text/javascript">                                                 /*GRAFICA # 2 INDICE DE ROTACION DE PERSONAL POR MES*/

var categories = [
  <?php
  for ($i=0; $i < count($plantilla_semanal) ; $i++) {
    echo "'".$plantilla_semanal[$i]["MES"]."',";
  }
  ?>
];

var data_sdo_ant_s = [
  <?php
  for ($i=0; $i < count($plantilla_semanal) ; $i++) {
    if($plantilla_semanal[$i]["ACTIVO_ANTERIOR"]>0){
      echo number_format(($plantilla_semanal[$i]["BAJA_ANTERIOR"]/$plantilla_semanal[$i]["ACTIVO_ANTERIOR"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }
  }
  ?>
];

var data_ant_s = [
  <?php
  for ($i=0; $i < count($plantilla_semanal) ; $i++) {
    if($plantilla_semanal[$i]["ACTIVO"]>0){
      echo number_format(($plantilla_semanal[$i]["BAJA"]/$plantilla_semanal[$i]["ACTIVO"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }
  }
  ?>
];

var data_sdo_ant_q = [
  <?php
  for ($i=0; $i < count($plantilla_quincenal) ; $i++) {
    if($plantilla_quincenal[$i]["ACTIVO_ANTERIOR"]>0){
      echo number_format(($plantilla_quincenal[$i]["BAJA_ANTERIOR"]/$plantilla_quincenal[$i]["ACTIVO_ANTERIOR"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }
  }
  ?>
];

var data_ant_q = [
  <?php
  for ($i=0; $i < count($plantilla_quincenal) ; $i++) {
    if($plantilla_quincenal[$i]["ACTIVO"]>0){
      echo number_format(($plantilla_quincenal[$i]["BAJA"]/$plantilla_quincenal[$i]["ACTIVO"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }
  }
  ?>
];


Highcharts.chart('graf_2', {
    chart: {
        type: 'column'
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
    colors: [ '#F5B918', '#8DD949', '#139DDE', '#F8F406'],
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
    series: [
      {
        name: 'NOMINA SEMANAL AÑO: <?=$anio_dos?>',
        marker: {
            symbol: 'square'
        },
        data: data_sdo_ant_s
      },
      {
        name: 'NOMINA SEMANAL AÑO: <?=$anio_uno?>',
        marker: {
          symbol: 'square'
        },
        data: data_ant_s
      },
      {
        name: 'NOMINA QUINCENAL AÑO: <?=$anio_dos?>',
        marker: {
          symbol: 'square'
        },
        data: data_sdo_ant_q
      },
      {
        name: 'NOMINA QUINCENAL AÑO: <?=$anio_uno?>',
        marker: {
          symbol: 'square'
        },
        data: data_ant_q
      }
  ]
});
</script>

<script type="text/javascript">                                                 /*GRAFICA # 3 INDICE DE ROTACION DE PERSONAL POR PLAZA*/

var categories = [
  <?php
  for ($i=0; $i < count($rotacion_plaza_semanal) ; $i++) {
    echo "'".$rotacion_plaza_semanal[$i]["PLAZA"]."',";
  }
  ?>
];

var data_sdo_ant_s = [
  <?php
  for ($i=0; $i < count($rotacion_plaza_semanal) ; $i++) {
    if($rotacion_plaza_semanal[$i]["ACTIVO_ANT"]>0){
      echo number_format(($rotacion_plaza_semanal[$i]["BAJA_ANT"]/$rotacion_plaza_semanal[$i]["ACTIVO_ANT"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }

  }
  ?>
];

var data_ant_s = [
  <?php
  for ($i=0; $i < count($rotacion_plaza_semanal) ; $i++) {
    if($rotacion_plaza_semanal[$i]["ACTIVO"]){
      echo $valor_calculo = number_format(($rotacion_plaza_semanal[$i]["BAJA"]/$rotacion_plaza_semanal[$i]["ACTIVO"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }

  }
  ?>
];

var data_sdo_ant_q = [
  <?php
  for ($i=0; $i < count($rotacion_plaza_quincenal) ; $i++) {
    if($rotacion_plaza_quincenal[$i]["ACTIVO_ANT"]>0){
      echo number_format(($rotacion_plaza_quincenal[$i]["BAJA_ANT"]/$rotacion_plaza_quincenal[$i]["ACTIVO_ANT"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }

  }
  ?>
];

var data_ant_q = [
  <?php
  for ($i=0; $i < count($rotacion_plaza_quincenal) ; $i++) {
    if($rotacion_plaza_quincenal[$i]["ACTIVO"]){
      echo $valor_calculo = number_format(($rotacion_plaza_quincenal[$i]["BAJA"]/$rotacion_plaza_quincenal[$i]["ACTIVO"]) * 100, 2).",";
    }else {
      echo number_format(0, 2).",";
    }

  }
  ?>
];

Highcharts.chart('graf_3', {
    chart: {
        type: 'column'
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
    colors: [ '#F5B918', '#8DD949', '#139DDE', '#F8F406'],
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
        name: 'NOMINA SEMANAL AÑO: <?=$anio_dos?>',
        marker: {
            symbol: 'square'
        },
        data: data_sdo_ant_s

    },
    {
        name: 'NOMINA SEMANAL AÑO: <?=$anio_uno?>',
        marker: {
            symbol: 'square'
        },
        data: data_ant_s
    },
    {
        name: 'NOMINA QUINCENAL AÑO: <?=$anio_dos?>',
        marker: {
            symbol: 'square'
        },
        data: data_sdo_ant_q
    },
    {
        name: 'NOMINA QUINCENAL AÑO: <?=$anio_uno?>',
        marker: {
            symbol: 'square'
        },
        data: data_ant_q
    },
  ]
});
</script>


<script type="text/javascript">                                                 /*GRAFICA # 1 COMPARATIVO DE PLANTILLA DE EMPLEADOS*/

Highcharts.chart('graf_4', {
    data: {
        table: 'datatable_2'
    },
    credits: {
      enabled: false
    },
    chart: {
        type: 'column'
    },
    title: {
        text: '<b>DETALLE DEPTO. IMPUTABLE <?=$anio_dos?> VS <?=$anio_uno?></b>'
    },
    yAxis: {
        allowDecimals: false,
        title: {
            text: 'Empleados'
        }
    },
    tooltip: {
        formatter: function () {
            return '<b>' + this.series.name + '</b><br/> <b>MES:</b> ' +
                this.point.name.toUpperCase() + ' <br><b>TOTAL:</b> ' + this.point.y;
        }
    },
    //colors: [ '#F5B918', '#8DD949', '#139DDE', '#F8F406'],
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


<script type="text/javascript">                                                 /*TABLA # 1 COMPARATIVO DE CAUSAS DE BAJA NOMINA SEMANAL*/
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
            title: 'COMPARATIVO DE CAUSAS DE BAJA AÑOS: <?=$anio_dos?> y <?=$anio_uno?> NOMINA SEMANAL',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'COMPARATIVO DE CAUSAS DE BAJA AÑOS: <?=$anio_dos?> y <?=$anio_uno?> NOMINA SEMANAL',
          },
        ],
    });

});
</script>


<script type="text/javascript">                                                 /*TABLA # 2 COMPARATIVO DE CAUSAS DE BAJA NOMINA QUINCENAL*/
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
            title: 'COMPARATIVO DE CAUSAS DE BAJA AÑOS: <?=$anio_dos?> y <?=$anio_uno?> NOMINA QUINCENAL',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'COMPARATIVO DE CAUSAS DE BAJA AÑOS: <?=$anio_dos?> y <?=$anio_uno?> NOMINA QUINCENAL',
          },
        ],
    });

});
</script>

<script type="text/javascript">                                                 /*TABLA # 3 DETALLE DE CAUSAS BAJA AÑO ANTERIOR NOMINA SEMANAL*/
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
            title: 'CAUSAS DE BAJA AÑO: <?=$anio_dos?> NOMINA SEMANAL',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'CAUSAS DE BAJA AÑO: <?=$anio_dos?> NOMINA SEMANAL',
          },
        ],
    });

});
</script>

<script>                                                                        /* SUMATORIA DE COLUMNAS DE TABLA #3 */
<?php $motivos=$rotPersonalGral->motivos($anio_dos, 2); ?>

$(document).ready(function(){


  var suma=0;
  <?php

  for($x=1; $x<=count($motivos); $x++){ ?>
    //var suma = 0;
    var columna=<?=$x?>;
    $('#tabla_3 tr').each(function(){
     suma += parseInt($(this).find('td').eq(columna).text()||0,10)
      })
      //console.log(suma);
      $('#tabla_3 tfoot tr td b').eq(columna).text( suma);
      suma=0;
  <?php } ?>
});
</script>

<script type="text/javascript">                                                 /*TABLA # 4 DETALLE DE CAUSAS BAJA AÑO ANTERIOR NOMINA SEMANAL*/
$(document).ready(function() {

    $('#tabla_4').DataTable( {
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
            title: 'CAUSAS DE BAJA AÑO: <?=$anio_uno?> NOMINA SEMANAL',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'CAUSAS DE BAJA AÑO: <?=$anio_uno?> NOMINA SEMANAL',
          },
        ],
    });

});
</script>

<script>                                                                        /* SUMATORIA DE COLUMNAS DE TABLA #4 */
<?php $motivos=$rotPersonalGral->motivos($anio_uno, 2); ?>
$(document).ready(function(){

  var suma=0;
  <?php

  for($x=1; $x<=count($motivos); $x++){ ?>
    //var suma = 0;
    var columna=<?=$x?>;
    $('#tabla_4 tr').each(function(){
     suma += parseInt($(this).find('td').eq(columna).text()||0,10)
      })
      //console.log(suma);
      $('#tabla_4 tfoot tr td b').eq(columna).text( suma);
      suma=0;
  <?php } ?>
});
</script>


<script type="text/javascript">                                                 /*TABLA # 5 DETALLE DE CAUSAS BAJA AÑO ANTERIOR NOMINA SEMANAL*/
$(document).ready(function() {

    $('#tabla_5').DataTable( {
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
            title: 'CAUSAS DE BAJA AÑO: <?=$anio_dos?> NOMINA QUINCENAL',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'CAUSAS DE BAJA AÑO: <?=$anio_dos?> NOMINA QUINCENAL',
          },
        ],
    });

});
</script>

<script>                                                                        /* SUMATORIA DE COLUMNAS DE TABLA #5 */
<?php $motivos=$rotPersonalGral->motivos($anio_dos, 1); ?>
$(document).ready(function(){

  var suma=0;
  <?php

  for($x=1; $x<=count($motivos); $x++){ ?>
    //var suma = 0;
    var columna=<?=$x?>;
    $('#tabla_5 tr').each(function(){
     suma += parseInt($(this).find('td').eq(columna).text()||0,10)
      })
      //console.log(suma);
      $('#tabla_5 tfoot tr td b').eq(columna).text( suma);
      suma=0;
  <?php } ?>
});
</script>

<script type="text/javascript">                                                 /*TABLA # 5 DETALLE DE CAUSAS BAJA AÑO ANTERIOR NOMINA SEMANAL*/
$(document).ready(function() {

    $('#tabla_6').DataTable( {
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
            title: 'CAUSAS DE BAJA AÑO: <?=$anio_uno?> NOMINA QUINCENAL',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'CAUSAS DE BAJA AÑO: <?=$anio_uno?> NOMINA QUINCENAL',
          },
        ],
    });

});
</script>

<script>                                                                        /* SUMATORIA DE COLUMNAS DE TABLA #5 */
<?php $motivos=$rotPersonalGral->motivos($anio_uno, 1); ?>
$(document).ready(function(){

  var suma=0;
  <?php

  for($x=1; $x<=count($motivos); $x++){ ?>
    //var suma = 0;
    var columna=<?=$x?>;
    $('#tabla_6 tr').each(function(){
     suma += parseInt($(this).find('td').eq(columna).text()||0,10)
      })
      //console.log(suma);
      $('#tabla_6 tfoot tr td b').eq(columna).text( suma);
      suma=0;
  <?php } ?>
});
</script>

<script type="text/javascript">                                                 /*TABLA # 5 DETALLE DE CAUSAS BAJA AÑO ANTERIOR NOMINA SEMANAL*/
$(document).ready(function() {

    $('#tabla_7').DataTable( {
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
            title: 'DETALLE DE BAJAS: <?=$anio_dos?>  Y <?=$anio_uno?> NOMINA SEMANAL',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'DETALLE DE BAJAS: <?=$anio_dos?>  Y <?=$anio_uno?> NOMINA SEMANAL',
          },
        ],
    });

});
</script>

<script type="text/javascript">                                                 /*TABLA # 5 DETALLE DE CAUSAS BAJA AÑO ANTERIOR NOMINA SEMANAL*/
$(document).ready(function() {

    $('#tabla_8').DataTable( {
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
            title: 'DETALLE DE BAJAS: <?=$anio_dos?>  Y <?=$anio_uno?> NOMINA QUINCENAL',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: ':visible',
            },
            title: 'DETALLE DE BAJAS: <?=$anio_dos?>  Y <?=$anio_uno?> NOMINA QUINCENAL',
          },
        ],
    });

});
</script>

<script>
$(".btn_fil").on("click", function(){

  fil_anio_uno = $('#fil_anio_uno').val();
  fil_anio_dos = $('#fil_anio_dos').val();
  url = '?anio_uno='+fil_anio_uno+'&anio_dos='+fil_anio_dos;
  location.href = url;

});
</script>


<?php conexion::cerrar($conn); ?>
