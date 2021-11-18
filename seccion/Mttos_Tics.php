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

include_once '../class/mttos_tics.php';
$obj_class = new RotacionPersonal();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
    //echo $clase;
  }

  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 50);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////

/* $_GET FECHA */
include '../class/Nomina_pagada.php';
$modelNomina = new NominaPagada();
$fec_corte = $obj_class->sql(1,null,null);
/*----- GET FECHA -----*/
$fecha = $fec_corte[0]["MES1"];
if( isset($_GET["fecha"]) ){
  if ( $obj_class->validateDate($_GET["fecha"]) ){
    $fecha = $_GET["fecha"];
  }else{
    $fecha = $fec_corte[0]["MES1"];
  }
}
/* $_GET FIL_CHECK */
$fil_check = "on";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}


$fil_habilitado = "ALL";
if (isset($_GET["fil_habilitado"])) {
  $fil_habilitado = $_GET["fil_habilitado"];
}
/* $_GET PLAZA */
$plaza = "ALL";
if ( isset($_GET["plaza"]) ){
  switch ($_GET["plaza"]) {
    case 'COMPUTADOR': $plaza = $_GET["plaza"]; break;
    case 'TELEFONO': $plaza = $_GET["plaza"]; break;
    default: $plaza = "ALL"; break;
  }
}

$plazas = "ALL";
if ( isset($_GET["plazas"]) ){
  switch ($_GET["plazas"]) {
    case 'CORPORATIVO': $plazas = $_GET["plazas"]; break;
    case 'CÓRDOBA': $plazas = $_GET["plazas"]; break;
    case 'MÉXICO': $plazas = $_GET["plazas"]; break;
    case 'GOLFO': $plazas = $_GET["plazas"]; break;
    case 'PENINSULA': $plazas = $_GET["plazas"]; break;
    case 'PUEBLA': $plazas = $_GET["plazas"]; break;
    case 'BAJIO': $plaza = $_GET["plazas"]; break;
    case 'OCCIDENTE': $plazas = $_GET["plazas"]; break;
    case 'NORESTE': $plazas = $_GET["plazas"]; break;
    default: $plazas = "ALL"; break;
  }
}


/*ALMACEN */
$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}

$cliente = "ALL";
if (isset($_GET["cliente"])) {
    $cliente = $_GET["cliente"];
}
/* $_GET CONTRATO */
$contrato = "ALL";
if ( isset($_GET["contrato"]) ){
  switch ($_GET["contrato"]) {
    case '0': $contrato = $_GET["contrato"]; break;
    case '1': $contrato = $_GET["contrato"]; break;
    case '3': $contrato = $_GET["contrato"]; break;
    case '2': $contrato = $_GET["contrato"]; break;
    default: $contrato = "ALL"; break;
  }
}
/* $_GET DEPARTAMENTO */
$departamento = "ALL";
if ( isset($_GET["depto"]) ){
  $select_depto = $obj_class->filtros(2,$departamento);
  for ($i=0; $i <count($select_depto) ; $i++) {
    if ( $select_depto[$i]["IID_DEPTO"] == $_GET["depto"]){
      $departamento = $_GET["depto"]; break;
    }
  }
}
/*----- GET AREA -----*/
$area = "ALL";
if ( isset($_GET["area"]) ){
  if ( $departamento != 'ALL' ){
    $select_area = $obj_class->filtros(3,$departamento);
    for ($i=0; $i <count($select_area) ; $i++) { // FOR
      if ( $select_area[$i]["IID_AREA"] == $_GET["area"]){
        $area = $_GET["area"]; break;
      }
    }// /.FOR
  }
}


//GRAFICA
$grafica = $obj_class->grafica($fecha, $cliente, $plaza,$plazas);
$graficaMensual = $obj_class->graficaMensual($cliente, $fecha, $plaza, $plazas);
$graficaMensual2 = $obj_class->graficaMensual2($cliente, $fecha, $plaza, $plazas);
//$grafica_Paste = $obj_class->grafica_Paste($plaza,$contrato,$departamento,$area,$fil_check,$fecha,$fil_habilitado);
//TABLA DETALLE ACTIVOS

$tablaEncuestas = $obj_class->tablaBaja($cliente, $fecha);

$consulta_clientes = $obj_class->filtros(5, 'ALL');
//$selectAlmacen = $obj_class->filtros(4,$departamento);

?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<style media="screen">
@import url('//cdn.datatables.net/1.10.2/css/jquery.dataTables.css');
td.details-control {
  background: url('http://www.datatables.net/examples/resources/details_open.png') no-repeat center center;
  cursor: pointer;
}
tr.shown td.details-control {
  background: url('http://www.datatables.net/examples/resources/details_close.png') no-repeat center center;
}
</style>
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php /*echo $fecha;*/ ?>
        Dashboard
        <small>Mttos. Realizados</small>
      </h1>
    </section>
    <!-- Main content -->
    <section class="Content" style="margin:1%"><!-- Inicia la seccion de los Widgets -->
      <div class="row">
      <!-- Widgets Cartas cupo expedidas -->
      <div class="col-lg-3 col-xs-6">
      <div class="info-box bg-green">
        <span class="info-box-icon"><i class="fa fa-laptop"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">MANTENIMIENTOS <BR> A REALIZAR</span>
          <span class="info-box-number"><?php
                                          $TOTAL_PRE = 0;
                                          for ($i=0; $i < count($grafica); $i++) {
                                            $TOTAL_PRE=  $grafica[$i]["PROGRAMADOS"];
                                          }
                                          echo $TOTAL_PRE;
                                         ?></span>
          <div class="progress">
            <div class="progress-bar" style="width: 60%"></div>
          </div>
          <span class="progress-description" title="<?=$fecha?>">Año: <?=$fecha?></span>
        </div>
      </div>
      </div>
        <!-- Termino Widgets Cartas cupo expedidas -->
        <!-- Widgets Cartas cupo no arribadas -->
        <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-green">
          <span class="info-box-icon"><i class="fa fa-laptop"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">MANTENIMIENTOS </BR> REALIZADOS</span>
            <span class="info-box-number">
              <?php
                                              $TOTAL_PREA = 0;
                                              for ($i=0; $i < count($grafica); $i++) {
                                                    $TOTAL_PREA =  $grafica[$i]["EFECTIVIDAD"];
                                              }
                                              echo $TOTAL_PREA;
                                             ?>
            </span>
            <div class="progress">
              <div class="progress-bar" style="width: 80%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Año: <?=$fecha?></span>
          </div>
        </div>
        </div>
        <!-- Termina Widgets Cartas cupo no arribadas -->
        <!-- Widgets Cartas cupo canceladas -->
        <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-laptop"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">MANTENIMIENTOS </BR> NO REALIZADOS</span>
            <span class="info-box-number"><?php
                                            $TOTAL_PREN = 0;
                                            for ($i=0; $i < count($grafica); $i++) {
                                                  $TOTAL_PREN = $TOTAL_PREN + $grafica[$i]["INEFECTIVIDAD"];
                                            }
                                            echo $TOTAL_PREN;
                                           ?></span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Año: <?=$fecha?></span>
          </div>
        </div>
        </div>

        <!-- Termino Widgets Cartas cupo canceladas -->
        <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-percent"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">TOTAL </br> EFECTIVIDAD</span>
            <span class="info-box-number"><?php
                                            if ($TOTAL_PRE == 0) {
                                                echo 0.0;
                                            }else {
                                                echo number_format(($TOTAL_PREA/$TOTAL_PRE)*100, 2);
                                            }

                                           ?></span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description" title="<?=$fecha?>">Año: <?=$fecha?></span>
          </div>
        </div>
        </div>
      </div>
      <!-- /.row -->
      </section>

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
          <div id="graf_bar" style="height: 380px;"></div>
          <?php if ($plazas <> "ALL") { ?>

          <?php }else { ?>
            <div id="graf_perM"></div>
          <?php } ?>

          <!--TABLA DETALLES-->
          <div class="table-responsive">
          <table id="tabla_baja" class="display nowrap" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#AD164D"><font color="black">ID</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">PLAZA</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">TIPO SERVICIO</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">MTTOS. PROGRAMADOS</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">MTTOS. NO REALIZADOS</font></th>
                <!--<th class="small" bgcolor="#AD164D"><font color="white">GENERO</font></th>-->
                <th class="small" bgcolor="#AD164D"><font color="black">MTTOS. REALIZADOS</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">COSTO</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">OBSERVACIONES</font></th>
                <th class="small" bgcolor="#AD164D"><font color="black">FECHA</font></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i <count($graficaMensual2) ; $i++) { ?>
              <tr>
                <td><?= $graficaMensual2[$i]["IID_PLAZA"] ?></td>
                <td><?= $graficaMensual2[$i]["V_RAZON_SOCIAL"] ?></td>
                <td><?php if ($graficaMensual2[$i]["TIPO_SERVICIO"] == 1) {
                                    echo "EQUIPOS DE COMPUTO";
                              }else {
                                    echo "EQUIPOS TELEFONICOS";
                              } ?>
                </td>
                <td><?= $graficaMensual2[$i]["PROGRAMADOS"] ?></td>
                <td><?= $graficaMensual2[$i]["INEFECTIVIDAD"] ?></td>
                <td><?= $graficaMensual2[$i]["EFECTIVIDAD"] ?></td>
                <td><?= $graficaMensual2[$i]["COSTO"] ?></td>
                <td><?= $graficaMensual2[$i]["V_OBSERVACIONES"] ?></td>
                <td><?= $graficaMensual2[$i]["FECHA"] ?></td>
                <td></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
          <!--<input type="text" name="f1t1" id="f1t1" value="0">-->
        </div>
        <!--fin tabla detalles-->
        </div>
        <?php ?>

        </div><!--/.box-body-->
      </div>
    </div>

    <?php //if ($plaza != 'ALL'){ ?>
    <div class="col-md-3" >
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
          <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="Mttos_Tics.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
          <?php } ?>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <!-- FILTRAR POR fecha -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Año:</span>
            <input type="text" name="fecha_ingre_clie" value = "<?= $fecha  ?>"class="form-control pull-right" id="datepicker">
          </div>
          <!-- FILTRAR POR TIPO -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-cubes"></i> TIPO:</span>
            <select class="form-control select2" id="fil_plaza" style="width: 100%;">
              <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
              <option value="TELEFONO" <?php if( $plaza == 'TELEFONO'){echo "selected";} ?> >EQUIPO DE TELEFONO</option>
              <option value="COMPUTADOR" <?php if( $plaza == 'COMPUTADOR'){echo "selected";} ?> >EQUIPO DE COMPUTO</option>
            </select>
          </div>
          <!--FILTRAR POR PLAZA-->
          <!-- FILTRAR POR PLAZA -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
            <select class="form-control select2" id="fil_plazas" style="width: 100%;">
              <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $select_plaza = $obj_class->filtros(1,$departamento);;
              for ($i=0; $i <count($select_plaza) ; $i++) { ?>
                <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plazas){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
              <?php } ?>
            </select>
          </div>
          <!-- FILTRAR POR CONTRATO -->
          <div class="input-group"  style="display:none">
            <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Almacen:</span>
            <select class="form-control select2" style="width: 100%;" id="nomAlm">
              <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $departamento = $_GET["plaza"];
              $selectAlmacen = $obj_class->filtros(4,$departamento);
              for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
                <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
              <?php } ?>
            </select>
          </div>
          <!-- FILTRAR POR DEPTO -->
          <div class="input-group" style="display:none">
            <span class="input-group-addon"><i class="fa fa-folder"></i> Cliente:</span>
            <select class="form-control select2" style="width: 100%;" id="fil_cliente">
              <option value="ALL" <?php if($cliente == 'ALL'){echo "selected";} ?>>ALL</option>
              <?php
              $departamento = $_GET["almacen"];
              $select_ctl = $obj_class->filtros(5,$departamento);
              for ($i=0; $i <count($consulta_clientes) ; $i++) { ?>
                <option value="<?= $consulta_clientes[$i]["IID_NUM_CLIENTE"] ?>" <?php if($consulta_clientes[$i]["IID_NUM_CLIENTE"] == $cliente){echo "selected";} ?> ><?= $consulta_clientes[$i]["V_RAZON_SOCIAL"] ?></option>
              <?php } ?>
            </select>
          </div>
          <!-- FILTRAR POR AREA -->
          <div class="input-group">
            <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil"><i class="fa fa-check"></i> Filtrar</button> </span>
          </div>

        </div><!--/.box-body-->
      </div>
    </div>
    <?php //} ?>
<!-- WIDGETS -->
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
  $('input[name="fil_fecha"]').attr("disabled", false);
<?php
if ( $fil_check == 'ALL' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){ ?>
  $('input[name="fil_fecha"]').attr("disabled", false);
<?php } ?>
$('input[name="fil_check"]').on("click", function (){

  if ($('input[name="fil_check"]').is(':checked')) {
    $('input[name="fil_fecha"]').attr("disabled", false);
  }else{
    $('input[name="fil_fecha"]').attr("disabled", false);
  }

});



//BOTON FILTRAR
$(".btn_fil").on("click", function(){

  fil_fecha = $('#datepicker').val();
  fil_plaza = $('#fil_plaza').val();
  almacen = $('#nomAlm').val();
  cliente = $('#fil_cliente').val();
  fil_check = 'on';
  fil_plazas= $('#fil_plazas').val();

  //Fill habilitados
  fil_habilitado = 'off';




  url = '?fecha='+fil_fecha+'&plaza='+fil_plaza+'&plazas='+fil_plazas;


  location.href = url;

});

$('.select2').select2()
</script>
<script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
  //Date picker
    $('#datepicker').datepicker({
      format: "yyyy",
      viewMode: "years",
      minViewMode: "years",
      autoclose: true
    });
</script>
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
                    show: true,
                    radius:2/3,/*0.90 posicion del label con data*/
                    formatter: labelFormatter,
                  },
                }";

$donut_grid =  "hoverable: true,
                clickable: false,
                verticalLines: false,
                horizontalLines: false,";
$donut_legend = "/*labelBoxBorderColor: 'none'*/
                show: true "; //-- PONE LOS LABEL DEL ALDO IZQUIERDO  //

$donut_content = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> Toneladas = %y.0 </b>  </div>' ;

$donut_tooltip = "show: false,
      content: '".$donut_content."',
      defaultTheme: true ";
 ?>
<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        for ($i=0; $i <count($grafica) ; $i++) {
          #echo $grafica[$i]["TIPO_RES"];
          //$plaza = $grafica[$i]["PROGRAMADOS"];
          //$plaza_corta = str_word_count($plaza, 1);
          //$separador  = ' ';
          //$plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica[$i]["PROGRAMADOS"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica[$i]["PROGRAMADOS"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$grafica[$i]["PROGRAMADOS"].'</button></form>' ;
                $label2 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica[$i]["INEFECTIVIDAD"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica[$i]["INEFECTIVIDAD"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$grafica[$i]["INEFECTIVIDAD"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica[$i]["PROGRAMADOS"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica[$i]["PROGRAMADOS"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$grafica[$i]["PROGRAMADOS"].'</button></form>' ;
               $label2 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica[$i]["INEFECTIVIDAD"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica[$i]["INEFECTIVIDAD"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$grafica[$i]["INEFECTIVIDAD"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica[$i]["PROGRAMADOS"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica[$i]["PROGRAMADOS"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$grafica[$i]["PROGRAMADOS"].'</button></form>' ;
                $label2 =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica[$i]["INEFECTIVIDAD"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica[$i]["INEFECTIVIDAD"].'"  name="co_plaza" class="btn btn-link btn-xs" disabled>'.$grafica[$i]["INEFECTIVIDAD"].'</button></form>' ;
                break;
            }
            switch ($i) {
              case '1':
                $color ='#FAEF07';
                break;
              case '2':
                $color = '#1FBC0C';
                break;
              case '3':
                $color = '#BC0C0C';
                break;
              case '4':
                $color = '#BC0C0C';
                break;
              case '5':
                $color = '#BC0C0C';
                break;
              case '6':
                $color = '#BC0C0C';
                break;
              case '7':
                $color = '#BC0C0C';
                break;
              default:
                $color = '#BC0C0C';
                break;
            }

          $data = round($grafica[$i]["PROGRAMADOS"], 2);
          $data2 = round($grafica[$i]["INEFECTIVIDAD"], 2);
          $color1 = "#BC0C0C";
          $color2 = '#1FBC0C';
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= "REALIZADOS" ?>', data: <?=$data?> , color: '<?= $color2 ?>'},
        {label: '<?= "NO REALIZADOS" ?>', data: <?=$data2?> , color: '<?= $color1 ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_bar", donutData_pros_general, {
      series: { <?= $donut_series ?> },
      grid: { <?= $donut_grid  ?> },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: { <?= $donut_legend ?>},
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {<?= $donut_tooltip ?>},
    });
    /* END DONUT CHART */

  });



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
        + "</div>";
  }
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
          type: 'column'
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
      echo "'".$graficaMensual[$i]["V_RAZON_SOCIAL"]."',";
    }
    ?>
  ];
  var data2 = [
    <?php
    for ($i=0; $i < count($graficaMensual); $i++) {
      $mes_Comparar = substr($fecha, 14, 2);

        if($graficaMensual[$i]["EFECTIVIDAD"] == 0){
          $porcentaje_total_neg = 0.00;
        }else {
            $porcentaje_total_neg = number_format(($graficaMensual[$i]["EFECTIVIDAD"]/$graficaMensual[$i]["PROGRAMADOS"])*100, 2);

        }
        echo $porcentaje_total_neg." ,";


    }
     ?>
  ];

  var data3 = [
    <?php
    for ($i=0; $i < count($graficaMensual); $i++) {
      $mes_Comparar = substr($fecha, 14, 2);

        if($graficaMensual[$i]["INEFECTIVIDAD"] == 0){
          $porcentaje_total_neg = 0.00;
        }else {
            $porcentaje_total_neg =  number_format(($graficaMensual[$i]["INEFECTIVIDAD"]/$graficaMensual[$i]["PROGRAMADOS"])*100, 2);

        }
        echo $porcentaje_total_neg." ,";


    }
     ?>
  ];

  $('#graf_perM').highcharts({
    chart:{
      type:'line'
    },
    title:{
      text:'EFECTIVIDAD POR PLAZA'
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
        text:'PORCENTAJE'
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
    colors:['#33FA07', '#C21313', '#FAEB07', '#1C00ff00'],
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
      name:'% REALIZADAS',
      data: data2,
    },{
      name:'% NO REALIZADAS',
      data: data3,
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
function format(value) {
    return '<div>Hidden Value:'+value+'  </div>';

}

$(document).ready(function () {
    var table = $('#tabla_baja').DataTable({
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

    });

    var tr = $(this).closest('tr');

    // Add event listener for opening and closing details
    $('#tabla_baja').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            console.log(tr.data('child-value'));
            res2 =tr.data('child-value');
            var a = [];
            $.ajax({
              type:'POST',
              url: '../class/Encuestas_Realizadas_Det.php',
              data: "encuesta=" + res2,
              success: function(response){
                  //$("#f1t1").val(res);
                  //console.log(response);
                  res = response;
                  var types = JSON.parse(response);
                  //console.log(types);
                  var table_head;
                  table_head = "<table class='egt'><tr><th>PREGUNTA</th> <th>RESPUESTA NEGATIVA</th><th>RESPUESTA CLIENTE</th><th>VALOR RESPUESTA</th></tr>";
                  var body_table = "";
                  for(x=0; x<types.length; x++) {
                        var pregunta;
                        var resp_sino;
                        var resp_cliente;
                        var tipo_resp;
                        var color_fila;
                        var color_texto;

                        if(types[x].PREGUNTA === null){
                          pregunta = "";
                        }else {
                          pregunta = types[x].PREGUNTA;
                        }

                        if(types[x].RESP_SIONO === null){
                          resp_sino = "";
                        }else {
                          resp_sino = types[x].RESP_SIONO;
                        }

                        if (types[x].RESPUESTA === null) {
                          resp_cliente = "";
                        }else {
                          resp_cliente = types[x].RESPUESTA;
                        }
                        if (types[x].TIPO_RES === null) {
                          tipo_resp = "";
                        }else {
                          tipo_resp = types[x].TIPO_RES;
                        }

                        if (resp_sino === resp_cliente  && types[x].RESP_SIONO !== null ) {
                          color_fila = 'red';
                          color_texto = 'white';
                        }
                        else if (resp_cliente === "BUENO") {
                          color_fila = 'green';
                          color_texto = 'white';
                        }
                        else if (resp_cliente === "MALO") {
                          color_fila = 'red';
                          color_texto = 'white';
                        }else if (resp_cliente === "EXCELENTE") {
                          color_fila = 'green';
                          color_texto = 'white';
                        }else if (resp_cliente === "REGULAR") {
                          color_fila = 'yellow';
                          color_texto = 'black';
                        }
                        else if (resp_sino === resp_cliente && types[x].RESP_SIONO === null) {
                          color_fila = 'white';
                          color_texto = 'black';
                        }else if (resp_sino !== resp_cliente && types[x].RESP_SIONO !== null) {
                          color_fila = 'green';
                          color_texto = 'white';
                        }else if (resp_sino !== resp_cliente && types[x].RESP_SIONO === null) {
                          color_fila = 'white';
                          color_texto = 'black';
                        }

                       body_table = body_table + "<tr style = 'background-color:"+color_fila+"; color: "+color_texto+";'><td>"+pregunta+"</td><td>"+resp_sino+"</td><td>"+resp_cliente+"</td><td>"+tipo_resp+"</td></tr>"
                      //console.log(types[x].PREGUNTA);
                      //console.log(types[x].RESP_SIONO);
                      //console.log(types[x].RESPUESTA);
                      //console.log(types[x].TIPO_RES);
                  }

                  console.log(table_head + body_table);

                  row.child(table_head + body_table).show();
                  tr.addClass('shown');
              }

            });
            //row.child(format(tr.data('child-value'))).show();

        }
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
