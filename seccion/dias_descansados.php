<?php
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:dias_descansados.php");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 22);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/Dias_descansados.php';
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
//
$tipo = "ALL";
if( isset($_GET["tipo"]) ){
  if( $_GET["tipo"] == "1" || $_GET["tipo"] == "2" || $_GET["tipo"] == "3" || $_GET["tipo"] == "4" || $_GET["tipo"] == "5" || $_GET["tipo"] == "6" || $_GET["tipo"] == "7" || $_GET["tipo"] == "8" || $_GET["tipo"] == "9" || $_GET["tipo"] == "10"  || $_GET["tipo"] == "11" || $_GET["tipo"] == "13"){
    $tipo = $_GET["tipo"];
  }else{
    $tipo = "ALL";
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

//GRAFICA NOMINA
//$GRAFICAina = $modelNomina->graficaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area);
$graficaNomina = $modelNomina->graficaNomina($fecha,$plaza, $tipo);
$graficaMensual = $modelNomina->grafica_Mensual($fecha,$plaza,$tipo);
//TABLA DETALLE DE NOMINA PAGADA
$tablaNomina = $modelNomina->tablaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area);
$widgetsNomina = $modelNomina->widgetFaltas($fecha, $plaza, $tipo);
$empleados = $modelNomina->widgets($plaza,$fecha);
//CALCULAR RANGO DE FECHAS
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
    <h1>Dashboard<small>Faltas en Gral.</small></h1>
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
      <div class="box-body">

        <div class="row">

          <div class="col-md-9">
            <div id="grafica_general"></div>
          </div>
          <div class="col-md-3">
            <!-- WIDGETS #1 -->
            <div class="info-box bg-morado">
              <span class="info-box-icon"><i class="fa fa-percent"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">PORCENTAJE FALTAS</span>
              <!--<span class="info-box-number"><?=  $empleados[0]["ACTIVO"] ?></span>-->
                  <span class="info-box-number"><?= number_format(($widgetsNomina[0]["TOTAL_FALTAS"]*100)/$empleados[0]["ACTIVO"], 2) ?></span>
                <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description" title="<?=$fecha?>">Fecha de consulta: <?=$fecha?></span>
              </div>
            </div>

          </div>
          <div class="col-md-3">
            <!-- WIDGETS #1 -->
            <div class="info-box bg-red">
              <span class="info-box-icon"><i class="fa fa-times"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">
                  <?php
                  switch ($tipo) {
                    case '1':
                      echo "Permiso con sueldo";
                      break;
                    case '2':
                        echo "Falta justificada";
                        break;
                    case '3':
                        echo "Riesgo de trabajo";
                        break;
                    case '4':
                        echo "Enfermedad en general";
                        break;
                    case '5':
                        echo "Maternidad";
                        break;
                    case '6':
                        echo "Injustificada";
                        break;
                    case '7':
                        echo "Retardos";
                        break;
                    case '8':
                        echo "Trabajo en plaza";
                        break;
                    case '9':
                        echo "Vacaciones";
                        break;
                    case '10':
                        echo "Tiempo por tiempo";
                        break;
                    case '11':
                        echo "Paternidad";
                       break;
                    case '13':
                        echo "Riesgo Trayecto";
                        break;
                    default:
                      echo "Absentismo General";
                      break;
                  }
                   ?>
                </span>
                  <span class="info-box-number"><?= number_format($widgetsNomina[0]["TOTAL_FALTAS"], 0) ?></span>
                <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
              </div>
            </div>
            <div class="info-box bg-green">
              <span class="info-box-icon"><i class="fa fa-users"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">EMPLEADOS ACTIVOS</span>
                  <span class="info-box-number"><?= number_format($empleados[0]["ACTIVO"], 0) ?></span>
                <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <!-- WIDGETS #1 -->
            <div class="info-box bg-morado">
              <span class="info-box-icon"><i class="fa fa-percent"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">ACUMULADO DE FALTAS</span>
              <!--<span class="info-box-number"><?=  $empleados[0]["ACTIVO"] ?></span>-->
                  <span class="info-box-number">
                    <?php
                      $valor_anual = 0.00;
                      $mes = substr($fecha, 14, 2);
                      switch ($mes) {
                        case '01':
                          $valor = 1;
                          break;
                        case '02':
                          $valor = 2;
                          break;
                        case '03':
                          $valor = 3;
                          break;
                        case '04':
                          $valor = 4;
                          break;
                        case '05':
                          $valor = 5;
                          break;
                        case '06':
                          $valor = 6;
                          break;
                        case '07':
                          $valor = 7;
                          break;
                        case '08':
                          $valor = 8;
                          break;
                        case '09':
                          $valor = 9;
                          break;
                        case '10':
                          $valor = 10;
                          break;
                        case '11':
                          $valor = 11;
                          break;
                        case '12':
                          $valor = 12;
                          break;
                        default:
                          $valor = 6;
                          break;
                      }
                      for ($i=0; $i < $valor ; $i++) {
                        $valor_faltas = $graficaMensual[$i]["DIAS_DESCANSADOS"];
                        if ($valor_faltas > 0) {
                          $valor_anual += $valor_faltas;
                        }
                      }
                      echo round($valor_anual);
                    ?>
                  </span>
                <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description" title="<?=$fecha?>">Fecha de consulta: <?=$fecha?></span>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <?php if ($tipo == "ALL") {

            }else { ?>
            <div id="graficaNom2"></div>
            <?php } ?>
            <div id="graficaNom"></div>
          </div>
          <div class="col-md-12">
            <div id="graficaMensual"></div>
          </div>
        </div><!--ROW-->
      </div><!--/.box-body-->
    </div>
    </div><!-- /.col-md-9 -->


    <div class="col-md-3"><!-- col-md-9 -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
        <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
        <a href="dias_descansados.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
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
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> FALTA:</span>
          <select class="form-control select2" style="width: 100%;" id="nomTipo">
            <option value="ALL" <?php if( $tipo == 'ALL'){echo "selected";} ?> >ALL</option>
            <option value="1" <?php if($tipo == '1') echo "selected"; ?>>Permiso con sueldo</option>
            <option value="2" <?php if($tipo == '2') echo "selected"; ?>>Falta justificada</option>
            <option value="3" <?php if($tipo == '3') echo "selected"; ?>>Riesgo de trabajo</option>
            <option value="4" <?php if($tipo == '4') echo "selected"; ?>>Enfermedad en general</option>
            <option value="5" <?php if($tipo == '5') echo "selected"; ?>>Maternidad</option>
            <option value="6" <?php if($tipo == '6') echo "selected"; ?>>Injustificada</option>
            <option value="7" <?php if($tipo == '7') echo "selected"; ?>>Retardos</option>
            <option value="8" <?php if($tipo == '8') echo "selected"; ?>>Trabajo plaza</option>
            <option value="9" <?php if($tipo == '9') echo "selected"; ?>>Vacaciones</option>
            <option value="10" <?php if($tipo == '10') echo "selected"; ?>>Tiempo por tiempo</option>
            <option value="11" <?php if($tipo == '11') echo "selected"; ?>>Paternidad</option>
            <option value="13" <?php if($tipo == '13') echo "selected"; ?>>Riesgo Trayecto</option>
          </select>
        </div>
        <!-- FILTRAR POR AREA -->
        <div class="input-group">
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>

      </div><!--/.box-body-->
    </div>
    </div><!-- /.col-md-3 -->

    </div><!-- /.row -->
  </section>
  <!-- ############################ /.SECCION GRAFICA ############################# -->


<?php if ( isset($_GET["fecha"]) || isset($_GET["plaza"]) || isset($_GET["tipo"]) || isset($_GET["status"]) || isset($_GET["contrato"]) || isset($_GET["depto"]) || isset($_GET["area"]) ){ ?>
  <!-- ############################ TABLA DETALLE DE NOMINA PAGADA ############################# -->
  <section>
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-table"></i> DETALLES DE FALTAS</h3>
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
                <th class="small" bgcolor="#383F6D"><font color="white">PERMISO CON SUELDO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">FALTA JUSTIFICADA</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">RIESGO TRABAJO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">ENFERMEDAD EN GRAL.</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">MATERNIDAD</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">INJUSTIFICADA</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">RETARDOS</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">TRABAJOS PLAZA</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">VACACIONES</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">TIEMPO X TIEMPO</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">PATERNIDAD</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">RIESGO TRAYECTO</font></th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i <count($tablaNomina) ; $i++) { ?>
              <tr>
                <td class="small"><?= $tablaNomina[$i]["EMPLEADO"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["NOMBRE"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["PLAZA"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["PERMISO_CON_SUELDO"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["FALTA_JUSTIFICADA"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["RIESGO_TRABAJO"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["ENFERMEDAD_EN_GRAL"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["MATERNIDAD"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["INJUSTIFICADA"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["RETARDOS"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["TRABAJOS_PLAZA"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["VACACIONES"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["TIEMPO_TIEMPO"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["PATERNIDAD"] ?></td>
                <td class="small"><?= $tablaNomina[$i]["RIESGO_TRAYECTO"] ?></td>
              <!--  <td class="small"><?= "$".number_format( $tablaNomina[$i]["DEPOSITO"],2 ) ?></td>
                <td class="small"><?= "$".number_format( $tablaNomina[$i]["VALES"],2 ) ?></td>-->
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

  url = '?fecha='+fecha+'&plaza='+plaza+'&tipo='+tipo;
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
<!--GRAFICAS GENERAL-->
<script type="text/javascript">
  $(function(){
    Highcharts.setOptions({lag:{thousandsSep: ','} });
    var categories= [
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
      for($i=0; $i< 5; $i++){
        $anio2 = $anio - 4 + $i;
        $tipo2 = "ALL" ;
        $graficaGral = $modelNomina->grafica_Gral($anio2,$plaza, $tipo2);
        echo  round($graficaGral[0]["TOTAL"], 0, PHP_ROUND_HALF_UP).",";
     } ?>
    ];

    $('#grafica_general').highcharts({
      chart:{type: 'column'},
      title:{text: 'REPORTE DE ABSENTISMO GENERAL'},
      legend:{
        y:-40,
        borderWidth:1,
        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#c6c2c2'
      },
      yAxis:{
        lineWidth:2,
        offset:10,
        tickWidth: 1,
        title:{
          text:'DIAS'
        },
        labels:{
          formatter:function(){
            return this.value;
          }
        }
      },
      tooltip:{
        shared:true,
        useHTML:true,
        valueDecimals:0,
        valuePrefix:'',
      },
      credits:{
        enabled: false
      },
      lang:{
        printChart: 'Imprimir Grafica',
        downloadPNG: 'Descargar PNG',
        downloadJPEG: 'Descargar JPEG',
        downloadPDF: 'Descargar PDF',
        downloadSVG: 'Descargar SVG',
        contextButtonTitle: 'Exportar Grafica'
      },
      colors: ['#56FE02', '#FEA602', '#02FEB5', '#024BFE', '#8002FE', '#D802FE', '#FE0230', '#A30A0A', '#935020', '#209387', '#F08080	'],
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
        categories:categories,
        labels:{
          formatter: function () {
            //url = '?fecha=<?=$fecha?>&plaza='+this.value';
            url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>';
              return '<a href="'+url+'">' +
                  this.value + '</a>';
          }
        }
      },
      subtitle:{
        text: '',
        align:'right',
        x:-10,
      },
      series:[{
        name: 'Reporte General',
        data: data1,
      }]
    });
  });
</script>

<script type="text/javascript">
  $(function(){
    Highcharts.setOptions({lag:{thousandsSep: ','} });
    var categories= [
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
      for($i=0; $i< 5; $i++){
        $anio2 = $anio - 4 + $i;
        $graficaGral = $modelNomina->grafica_Gral($anio2,$plaza, $tipo);
        echo  round($graficaGral[0]["TOTAL"], 0, PHP_ROUND_HALF_UP).",";
     } ?>
    ];

    $('#graficaNom2').highcharts({
      chart:{type: 'column'},
      title:{text: 'REPORTE GENERAL DE <?php //cho $tipo;
                                    if($tipo == 1 ){
                                      echo" PERMISO CON SUELDO";
                                    }elseif($tipo == 2){
                                      echo " FALTA INJUSTIFICADA";
                                    }elseif($tipo == 3){
                                      echo " RIESGO DE TRABAJO";
                                    }elseif($tipo == 4){
                                      echo " ENFERMEDAD EN GENERAL";
                                    }elseif($tipo == 5){
                                      echo " MATERNIDAD";
                                    }elseif($tipo == 6){
                                      echo " INJUSTIFICADA";
                                    }elseif($tipo == 7){
                                      echo " RETARDOS";
                                    }elseif($tipo == 8){
                                      echo " TRABAJO PLAZA";
                                    }elseif($tipo == 9){
                                      echo " VACACIONES";
                                    }elseif($tipo == 10){
                                      echo " TIEMPO POR TIEMPO";
                                    }elseif($tipo == 11){
                                      echo " PATERNIDAD";
                                    }elseif($tipo == 13){
                                      echo " RIESGO DE TRAYECTO";
                                    }
                                    elseif($tipo == 'ALL'){
                                      echo "FALTAS EN GENERAL";
                                    }

                                   ?>'},
      legend:{
        y:-40,
        borderWidth:1,
        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#c6c2c2'
      },
      yAxis:{
        lineWidth:2,
        offset:10,
        tickWidth: 1,
        title:{
          text:'DIAS'
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
      credits:{
        enabled: false
      },
      lang:{
        printChart: 'Imprimir Grafica',
        downloadPNG: 'Descargar PNG',
        downloadJPEG: 'Descargar JPEG',
        downloadPDF: 'Descargar PDF',
        downloadSVG: 'Descargar SVG',
        contextButtonTitle: 'Exportar Grafica'
      },
      colors: ['#FEA602', '#02FEB5', '#024BFE', '#8002FE', '#D802FE', '#FE0230', '#A30A0A', '#935020', '#209387', '#F08080	'],
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
        categories:categories,
        labels:{
          formatter: function () {
            //url = '?fecha=<?=$fecha?>&plaza='+this.value';
            url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>';
              return '<a href="'+url+'">' +
                  this.value + '</a>';
          }
        }
      },
      subtitle:{
        text: '',
        align:'right',
        x:-10,
      },
      series:[{
        name: 'Reporte General',
        type: 'column',
        data: data1,
      }]
    });
  });
</script>

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
  <?=$graficaNomina[$i]["DIAS_DESCANSADOS"]?>,
  <?php }  ?>
  ];

  $('#graficaNom').highcharts({
    chart: { type: 'column' },
    title: { text: '<?php //cho $tipo;
                                  if($tipo == 1 ){
                                    echo" PERMISO CON SUELDO";
                                  }elseif($tipo == 2){
                                    echo " FALTA INJUSTIFICADA";
                                  }elseif($tipo == 3){
                                    echo " RIESGO DE TRABAJO";
                                  }elseif($tipo == 4){
                                    echo " ENFERMEDAD EN GENERAL";
                                  }elseif($tipo == 5){
                                    echo " MATERNIDAD";
                                  }elseif($tipo == 6){
                                    echo " INJUSTIFICADA";
                                  }elseif($tipo == 7){
                                    echo " RETARDOS";
                                  }elseif($tipo == 8){
                                    echo " TRABAJO PLAZA";
                                  }elseif($tipo == 9){
                                    echo " VACACIONES";
                                  }elseif($tipo == 10){
                                    echo " TIEMPO POR TIEMPO";
                                  }elseif($tipo == 11){
                                    echo " PATERNIDAD";
                                  }elseif($tipo == 13){
                                    echo " RIESGO TRAYECTO";
                                  }
                                  elseif($tipo == 'ALL'){
                                    echo "FALTAS EN GENERAL";
                                  }

                                 ?>'},
    legend:{
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#c6c2c2'
    },
    yAxis:{
          lineWidth: 2,
          //min: 0,
          offset: 10,
          tickWidth: 1,
          title: {
            text: 'Dias'
          },
          labels:{
                formatter: function () {
                  return this.value;
                }
          }
    },
    tooltip:{
            shared: true,
            valueSuffix: ' <?php
                                  if($tipo == 1 ){
                                    echo" PERMISO CON SUELDO";
                                  }elseif($tipo == 2){
                                    echo " FALTA INJUSTIFICADA";
                                  }elseif($tipo == 3){
                                    echo " RIESGO DE TRABAJO";
                                  }elseif($tipo == 4){
                                    echo " ENFERMEDAD EN GENERAL";
                                  }elseif($tipo == 5){
                                    echo " MATERNIDAD";
                                  }elseif($tipo == 6){
                                    echo " INJUSTIFICADA";
                                  }elseif($tipo == 7){
                                    echo " RETARDOS";
                                  }elseif($tipo == 8){
                                    echo " TRABAJO PLAZA";
                                  }elseif($tipo == 9){
                                    echo " VACACIONES";
                                  }elseif($tipo == 10){
                                    echo " TIEMPO POR TIEMPO";
                                  }elseif($tipo == 11){
                                    echo " PATERNIDAD";
                                  }
                                  elseif($tipo == 13){
                                    echo " RIESGO TRAYECTO";
                                  }
                                  elseif($tipo == 'ALL'){
                                    echo "FALTAS";
                                  }

                                 ?>',
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
    colors: ['#2f56e0'],
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
          //url = '?fecha=<?=$fecha?>&plaza='+this.value';
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
            name: 'Total Dias',
            data: data1,
            }]
    });
});
</script>
<script type="text/javascript">
  $(function(){
    Highcharts.setOptions({lag:{thousandsSep: ','} });
    var categories= [
      <?php for($i=0; $i<count($graficaMensual); $i++){?>
        "<?=$graficaMensual[$i]["NOMBRE_MES"]?>",
      <?php } ?>
    ];
    var data1 = [
      <?php for($i=0; $i<count($graficaMensual); $i++){ ?>
        <?=$graficaMensual[$i]["DIAS_DESCANSADOS"]?>,
      <?php } ?>
    ];

    $('#graficaMensual').highcharts({
      chart:{type: 'line'},
      title:{text: 'REPORTE DEL AÑO <?php echo substr($fecha, 6,4); ?>  DE <?php
                                  if($tipo == 1 ){
                                    echo" PERMISO CON SUELDO";
                                  }elseif($tipo == 2){
                                    echo " FALTA INJUSTIFICADA";
                                  }elseif($tipo == 3){
                                    echo " RIESGO DE TRABAJO";
                                  }elseif($tipo == 4){
                                    echo " ENFERMEDAD EN GENERAL";
                                  }elseif($tipo == 5){
                                    echo " MATERNIDAD";
                                  }elseif($tipo == 6){
                                    echo " INJUSTIFICADA";
                                  }elseif($tipo == 7){
                                    echo " RETARDOS";
                                  }elseif($tipo == 8){
                                    echo " TRABAJO PLAZA";
                                  }elseif($tipo == 9){
                                    echo " VACACIONES";
                                  }elseif($tipo == 10){
                                    echo " TIEMPO POR TIEMPO";
                                  }elseif($tipo == 11){
                                    echo " PATERNIDAD";
                                  }elseif($tipo == 13){
                                    echo " RIESGO TRAYECTO";
                                  }
                                  elseif($tipo == 'ALL'){
                                    echo "FALTAS EN GENERAL";
                                  }

                                 ?>'},
      legend:{
        y:-40,
        borderWidth:1,
        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#c6c2c2'
      },
      yAxis:{
        lineWidth:2,
        offset:10,
        tickWidth: 1,
        title:{
          text:'DIAS'
        },
        labels:{
          formatter:function(){
            return this.value;
          }
        }
      },
      tooltip:{
        shared:true,
        valueSuffix:' <?php
                                  if($tipo == 1 ){
                                    echo" PERMISOS CON SUELDO";
                                  }elseif($tipo == 2){
                                    echo " FALTAS INJUSTIFICADAS";
                                  }elseif($tipo == 3){
                                    echo " RIESGO DE TRABAJO";
                                  }elseif($tipo == 4){
                                    echo " ENFERMEDAD EN GENERAL";
                                  }elseif($tipo == 5){
                                    echo " MATERNIDAD";
                                  }elseif($tipo == 6){
                                    echo " INJUSTIFICADA";
                                  }elseif($tipo == 7){
                                    echo " RETARDOS";
                                  }elseif($tipo == 8){
                                    echo " TRABAJO PLAZA";
                                  }elseif($tipo == 9){
                                    echo " VACACIONES";
                                  }elseif($tipo == 10){
                                    echo " TIEMPO POR TIEMPO";
                                  }elseif($tipo == 11){
                                    echo " PATERNIDAD";
                                  }elseif($tipo == 13){
                                    echo " RIESGO TRAYECTO";
                                  }
                                  elseif($tipo == 'ALL'){
                                    echo "FALTAS EN GENERAL";
                                  }

                                 ?>',
        useHTML:true,
        valueDecimals:0,
        valuePrefix:'',
      },
      credits:{
        enabled: false
      },
      lang:{
        printChart: 'Imprimir Grafica',
        downloadPNG: 'Descargar PNG',
        downloadJPEG: 'Descargar JPEG',
        downloadPDF: 'Descargar PDF',
        downloadSVG: 'Descargar SVG',
        contextButtonTitle: 'Exportar Grafica'
      },
      colors: ['#2f56e0'],
      plotOptions:{
        series:{
          minPointLength:3
        }
      },
      xAxis:{
        categories:categories,
        labels:{
          formatter: function () {
            //url = '?fecha=<?=$fecha?>&plaza='+this.value';
            url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>';
              return '<a href="'+url+'">' +
                  this.value + '</a>';
          }
        }
      },
      subtitle:{
        text: '',
        align:'right',
        x:-10,
      },
      series:[{
        name: 'Total Dias',
        data: data1,
      }]
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
