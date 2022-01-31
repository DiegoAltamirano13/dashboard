<?php
//BY DAS 12/12/2019

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:reporte:alo.php");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 47);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/Reporte_Alo.php';
$modelNomina = new Rep_Alo();
//SQL ULTIMA FECHA DE CORTE
$fec_corte = $modelNomina->sql(1,null, null);
$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}

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
//if($_SESSION['area']==3){
  //$plaza = $_SESSION['nomPlaza'];
//}else {
  //$plaza = "ALL";
//}

$plaza = $_SESSION['nomPlaza'];

if( isset($_GET["plaza"]) ){
    $plaza = $_GET["plaza"];
}else{
  //$plaza = "ALL";
  $plaza = $_SESSION['nomPlaza'];
}


$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}


$proyecto = "ALL";
if (isset($_GET["proyecto"])) {
    $proyecto = $_GET["proyecto"];
}

$contenedor = "ALL";
if (isset($_GET["contenedor"])) {
    $contenedor = $_GET["contenedor"];
}

$parte = "ALL";
if (isset($_GET["parte"])) {
    $parte = $_GET["parte"];
}

$cond = "ALL";
if (isset($_GET["cond"])) {
    $cond = $_GET["cond"];
}

$lote = "ALL";
if (isset($_GET["lotes"])) {
    $lote = $_GET["lotes"];
}

$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}

$tabla_toneladas6 = $modelNomina->tabla_toneladas($plaza, $almacen, $proyecto, $fecha, $contenedor, $parte, $fil_check, $cond, $lote);

//$selectAlmacen = $modelNomina->almacenSql($plaza);
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
    <h1>Dashboard<small>RESUMEN GENERAL MERCANCIA(ALO)</small>
      <?php //if($_SESSION['area']==3){echo "<center><h4> PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>";} ?><!--FILTRAR UNICAMENTE P/DEPTO. OPERACIONES -->
      <?php echo "<center><h4>PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>"; ?><!--FILTRO GENERAL -->

    </h1>
  </section>

  <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
  <!-- ############################ SECCION GRAFICA ############################# -->
  <section>

    <div class="row"><!-- row -->

    <div class="col-md-9"><!-- col-md-9 -->
    <div class="box box-primary">
      <div class="box-body"><!--box-body-->

        <div class="row">

          <div class="col-md-12">

            <?php ?>
              <section>
                <div class="box box-success">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-table"></i> INVENTARIO AUTOMOTIVE</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body"><!--box-body-->

                    <div class="table-responsive" id="container">
                      <table id="tabla_nomina_real" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                            <th class="small" bgcolor="#2a7a1a"><font color="white">ALMACEN</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">FECHA DEPOSITO</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white"># PARTE</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">UBICACION</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">CONDICION</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">LOTE SERIE</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">FACTURA</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">SALDO</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">UME</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">CERTIFICADO</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">CONTENEDOR</font></th>
                            <th class="small" bgcolor="#2a7a1a"><font color="white">PROYECTO</font></th>


                            <!-- Si anita lava la tina la tina lavananita -->

                          </tr>
                        </thead>
                        <tbody>
                          <?php for ($i=0; $i <count($tabla_toneladas6) ; $i++) { ?>
                          <tr>
                            <td class="small"><?= $tabla_toneladas6[$i]["V_NOMBRE"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["D_PLAZO_DEP_INI"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["MERCANCIA"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["UBICACION"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["CALIDAD"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["V_LOTE_SERIE"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["VID_FACTURA"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["SALDO"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["UME"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["CERTIFICADO"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["V_CONTENEDOR"] ?></td>
                            <td class="small"><?= $tabla_toneladas6[$i]["PROYECTO"] ?></td>

                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>

                  </div><!--/.box-body-->
                </div>
              </section>
            <?php /*}*/ ?>

          </div>
          <!--GRAFICA NOMINA POR MES DIEGO ALTAMIRANO SUAREZ-->
        </div>

      </div><!--/.box-body-->
    </div>
    </div><!-- /.col-md-9 -->


    <div class="col-md-3"><!-- col-md-9 -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
        <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
        <a href="reporte_alo.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
        <?php } ?>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->


        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
          <input type="text" class="form-control pull-right" name="nomFecha" disabled>
          <span class="input-group-addon"> <input type="checkbox" name="fil_check" <?php if( $fil_check == 'on' ){ echo "checked";} ?> > </span>
        </div>

        <!-- FILTRAR POR PLAZA -->
        <input id="nomPlaza" type="hidden" value=<?= $plaza ?>>
        <?php if($_SESSION['area']!=3){ ?>
        <!--<div class="input-group">
          <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
          <select class="form-control select2" id="nomPlaza" style="width: 100%;">
            <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $select_plaza = $modelNomina->sql(2,null,null);
            for ($i=0; $i <count($select_plaza) ; $i++) { ?>
              <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
            <?php } ?>
          </select>
        </div>
      <?php } else{?>
        <input id="nomPlaza" type="hidden" value=<?= $plaza ?>>-->
      <?php }?>

        <!--FILTRAR POR ALMACEN -->
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-home"></i> Almacen:</span>
          <select class="form-control select2" style="width: 100%;" id="nomAlm">
            <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
              $plazas=$plaza;
            //$plazas = $_GET["plaza"];
            $selectAlmacen = $modelNomina->almacenSql($plazas);
            for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
              <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
            <?php } ?>
          </select>
        </div>

        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-folder-open"></i> Proyecto:</span>
          <select class="form-control select2" style="width: 100%;" id="nomPro">
            <option value="ALL" <?php if( $proyecto == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $selectProyect = $modelNomina->proyectoSql();
            for ($i=0; $i <count($selectProyect) ; $i++) { ?>
              <option value="<?=$selectProyect[$i]["PROYECTO"]?>" <?php if($selectProyect[$i]["PROYECTO"] == $proyecto){echo "selected";} ?>><?=$selectProyect[$i]["PROYECTO"]?> </option>
            <?php } ?>
          </select>
        </div>

        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-truck"></i> Contenedor:</span>
          <select class="form-control select2" style="width: 100%;" id="nomCont">
            <option value="ALL" <?php if( $contenedor == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $selectCont = $modelNomina->contSql();
            for ($i=0; $i <count($selectCont) ; $i++) { ?>
              <option value="<?=$selectCont[$i]["CONTENEDOR"]?>" <?php if($selectCont[$i]["CONTENEDOR"] == $contenedor){echo "selected";} ?>><?=$selectCont[$i]["CONTENEDOR"]?> </option>
            <?php } ?>
          </select>
        </div>

        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-archive"></i> Numero Parte:</span>
          <select class="form-control select2" style="width: 100%;" id="nomParte">
            <option value="ALL" <?php if( $parte == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $selectParte = $modelNomina->parteSql();
            for ($i=0; $i <count($selectParte) ; $i++) { ?>
              <option value="<?=$selectParte[$i]["N_PARTE"]?>" <?php if($selectParte[$i]["N_PARTE"] == $parte){echo "selected";} ?>><?=$selectParte[$i]["N_PARTE"]?> </option>
            <?php } ?>
          </select>
        </div>


        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-archive"></i> Condicion:</span>
          <select class="form-control select2" style="width: 100%;" id="nCondicion">
            <option value="ALL" <?php if( $cond == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $selectCond = $modelNomina->condicionSql();
            for ($i=0; $i <count($selectCond) ; $i++) { ?>
              <option value="<?=$selectCond[$i]["V_DESCRIPCION"]?>" <?php if($selectCond[$i]["V_DESCRIPCION"] == $cond){echo "selected";} ?>><?=$selectCond[$i]["V_DESCRIPCION"]?> </option>
            <?php } ?>
          </select>
        </div>


        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-archive"></i> Lote Serie:</span>
          <select class="form-control select2" style="width: 100%;" id="nLote">
            <option value="ALL" <?php if( $lote == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $loteS = $modelNomina->loteSerieSql();
            for ($i=0; $i <count($loteS) ; $i++) { ?>
              <option value="<?=$loteS[$i]["V_LOTE_SERIE"]?>" <?php if($loteS[$i]["V_LOTE_SERIE"] == $lote){echo "selected";} ?>><?=$loteS[$i]["V_LOTE_SERIE"]?> </option>
            <?php } ?>
          </select>
        </div>


        <div class="input-group">
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>

      </div><!--/.box-body-->
    </div>

    </div><!-- /.col-md-3 -->
    <div class="col-md-3">
      <section>
        <div class="box box-success">
          <div class="box-header with-borderxds2001">
            <h3 class="box-title"><i class="fa fa-table"></i> SEPARACIÓN POR CALIDAD</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body"><!--box-body-->

            <div class="table-responsive" id="container">
              <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#2a7a1a"><font color="white">CALIDAD</font></th>
                    <th class="small" bgcolor="#2a7a1a"><font color="white">TOTAL PIEZAS</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $circle_graft = $modelNomina->circle_graf($plaza, $almacen, $proyecto, $fecha, $contenedor, $parte, $fil_check, $cond, $lote);
                  for ($i=0; $i <count($circle_graft) ; $i++) { ?>
                  <tr>
                    <td class="small"><?= $circle_graft[$i]["CALIDAD"] ?></td>
                    <td class="small"><?= number_format($circle_graft[$i]["CANTI_CAL"]) ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div><!--/.box-body-->
        </div>
      </section>
    <section>
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> SEPARACION POR CALIDAD</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->
          <div id="graf_donut_alo" style="height: 380px;"></div>

        </div><!--/.box-body-->
      </div>
    </section>
    </div>
    </div><!-- /.row -->
  </section>

  <!-- ############################ /.SECCION GRAFICA ############################# -->


<?php if ( isset($_GET["fecha"]) || isset($_GET["plaza"]) || isset($_GET["tipo"]) || isset($_GET["status"]) || isset($_GET["contrato"]) || isset($_GET["depto"]) || isset($_GET["almacen"]) ){ ?>
  <!-- ############################ TABLA DETALLE DE NOMINA PAGADA ############################# -->
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
//ACTIVA FILTRO POR FECHA
<?php if ( $fil_check == 'on' ){ ?>
  $('input[name="nomFecha"]').attr("disabled", false);
<?php } ?>
$('input[name="fil_check"]').on("click", function (){

  if ($('input[name="fil_check"]').is(':checked')) {
    $('input[name="nomFecha"]').attr("disabled", false);
  }else{
    $('input[name="nomFecha"]').attr("disabled", true);
  }

});
/*---- CLICK BOTON FILTRAR ----*/
$(".btnNomFiltro").on("click", function(){
  plaza = $('#nomPlaza').val();
  almacen = $('#nomAlm').val();
  proyecto = $('#nomPro').val();
  fil_fecha = $('input[name="nomFecha"]').val();
  contenedor = $('#nomCont').val();
  parte = $('#nomParte').val();
  condicion = $('#nCondicion').val();
  loteserie = $('#nLote').val();
  fil_check = 'off';

  if ($('input[name="fil_check"]').is(':checked')) {
      fil_check = 'on';
      url = '?plaza='+plaza+'&almacen='+almacen+'&proyecto='+proyecto+'&check='+fil_check+'&fecha='+fil_fecha+'&contenedor='+contenedor+'&parte='+parte+'&cond='+condicion+'&lotes='+loteserie;
  }
  else {
    fil_check = 'off';
    url = '?plaza='+plaza+'&almacen='+almacen+'&proyecto='+proyecto+'&check='+fil_check+'&fecha='+fil_fecha+'&contenedor='+contenedor+'&parte='+parte+'&cond='+condicion+'&lotes='+loteserie;
  }
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
<!--PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
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

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_nomina_real').DataTable( {
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros, lBfrtip
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Inventario Cliente AUTOMOTIVE <?php  if ($plaza <> "ALL") {echo $plaza; }if($almacen <> "ALL"){ echo " $almacen";} ?>',
          },

          {
            extend: 'pdfHtml5',
            text: '<i class="fa fa-print"></i>',
            orientation: 'landscape',
            pageSize: 'A4',
            customize: function ( doc ) {
                                doc.content.splice( 1, 0, {
                                    margin: [ 0, 0, 0, 12 ],
                                    alignment: 'center',
                                    image: 'data:image/png;base64,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'
                                } );
                            }

          },

        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

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
                clickable: true,
                verticalLines: false,
                horizontalLines: false,";
$donut_legend = "/*labelBoxBorderColor: 'none'*/
                show: true "; //-- PONE LOS LABEL DEL ALDO IZQUIERDO //

$donut_content = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> Toneladas = %y.0 </b>  </div>' ;

$donut_tooltip = "show: false,
      content: '".$donut_content."',
      defaultTheme: true ";
 ?>
<!-- Inicia FancyBox JS -->
<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
        $circle_graft = $modelNomina->circle_graf($plaza, $almacen, $proyecto, $fecha, $contenedor, $parte, $fil_check, $cond, $lote);
        for ($i=0; $i <count($circle_graft) ; $i++) {
          $plaza = $circle_graft[$i]["CALIDAD"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$circle_graft[$i]["CANTI_CAL"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$circle_graft[$i]["CANTI_CAL"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$circle_graft[$i]["CALIDAD"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$circle_graft[$i]["CANTI_CAL"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$circle_graft[$i]["CANTI_CAL"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$circle_graft[$i]["CALIDAD"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$circle_graft[$i]["CANTI_CAL"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$circle_graft[$i]["CANTI_CAL"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$circle_graft[$i]["CALIDAD"].'</button></form>' ;
                break;
            }
            switch ($i) {
              case '1':
                $color ='#5ABF43';
                break;
              case '2':
                $color = '#3C802D';
                break;
              case '3':
                $color = '#78FF59';
                break;
              case '4':
                $color = '#1F4217';
                break;
              case '5':
                $color = '#6CE650';
                break;
              case '6':
                $color = '#D87A80';
                break;
              case '7':
                $color = '#FF6A6F';
                break;
              default:
                $color = '#5D8AA8';
                break;
            }

          $data = $circle_graft[$i]["CANTI_CAL"];
          $color = $color;
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

      <?php
        }
      ?>
    ];

    $.plot("#graf_donut_alo", donutData_pros_general, {
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
