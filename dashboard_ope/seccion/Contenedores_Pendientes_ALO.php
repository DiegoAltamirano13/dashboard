<?php
//BY DAS 12/12/2019

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:detalles_granos.php");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 49);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/contenedores_pendientes_ALO.php';
$modelNomina = new Calculo_Ocupacion();
//SQL ULTIMA FECHA DE CORTE
$fec_corte = $modelNomina->sql(1,null, null);
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
$plaza=$_SESSION["nomPlaza"];
//$plaza = "ALL";
if( isset($_GET["plaza"]) ){
  if( $_GET["plaza"] == "CORPORATIVO" || $_GET["plaza"] == "CÓRDOBA" || $_GET["plaza"] == "MÉXICO" || $_GET["plaza"] == "GOLFO" || $_GET["plaza"] == "PENINSULA" || $_GET["plaza"] == "PUEBLA" || $_GET["plaza"] == "BAJIO" || $_GET["plaza"] == "OCCIDENTE" || $_GET["plaza"] == "NORESTE" ){
    $plaza = $_GET["plaza"];
  }else{
    $plaza = "ALL";
  }
}
//echo $plaza;


$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}

$tipo = "ALL";
if( isset($_GET["tipo"]) ){
  if($_GET["tipo"] == "00017" || $_GET["tipo"] == "00050"
    || $_GET["tipo"] == "00056" || $_GET["tipo"] == "00057" || $_GET["tipo"] == "00059"
    || $_GET["tipo"] == "00060" || $_GET["tipo"] == "00065" || $_GET["tipo"] == "00073"
    || $_GET["tipo"] == "00074" || $_GET["tipo"] == "00077" || $_GET["tipo"] == "00078"
    || $_GET["tipo"] == "00083" || $_GET["tipo"] == "00084" || $_GET["tipo"] == "00085"
    || $_GET["tipo"] == "00086" || $_GET["tipo"] == "00087" || $_GET["tipo"] == "00088"
    || $_GET["tipo"] == "00089" || $_GET["tipo"] == "00091"){
    $tipo = $_GET["tipo"];
  }else{
    $tipo = "ALL";
  }
}

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
  $select_depto = $modelNomina->sql(3,$depto, null);
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
    $select_area = $modelNomina->sql(4,$depto, null);
    for ($i=0; $i <count($select_area) ; $i++) { // FOR
      if ( $select_area[$i]["IID_AREA"] == $_GET["area"]){
        $area = $_GET["area"]; break;
      }
    }// /.FOR
  }
}

$fil_habilitado = "ALL";
if (isset($_GET["fil_habilitado"])) {
  $fil_habilitado = $_GET["fil_habilitado"];
}

$tabla_toneladas = $modelNomina->graficaMensual($plaza, $fil_habilitado,$fecha);


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
    <h1>Dashboard<small>CONTENEDORES PENDIENTES/ASIGNADOS AUTOMOTIVE LOGISTIC</small>
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
            <section>
              <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-table"></i> CONTENEDORES</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body"><!--box-body-->

                  <div class="table-responsive" id="container">
                    <table id="tabla_nomina" class="table table-striped table-bordered" cellspacing="0" style="text-align: center;">
                      <thead>
                        <tr>
                          <th class="small" bgcolor="#4791de" ><font color="white">ID</font></th>
                          <th class="small" bgcolor="#4791de" ><font color="white">CONTENEDOR</font></th>
                          <th class="small" bgcolor="#4791de" ><font color="white">FACTURA</font></th>
                          <th class="small" bgcolor="#4791de" ><font color="white">MERCANCIA</font></th>
                          <th class="small" bgcolor="#4791de" align="center"><font color="white">CANTIDAD</font></th>
                          <th class="small" bgcolor="#4791de" ><font color="white">TIEMPO ESTIMADO DE LLEGADA (ETA)</font></th>
                          <th class="small" bgcolor="#4791de" ><font color="white">FECHA ARRIBO</font></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i <count($tabla_toneladas) ; $i++) { ?>
                        <tr>
                          <td class="small" width="10"><?= $tabla_toneladas[$i]["ID"] ?></td>
                          <td class="small" width="10"><?= $tabla_toneladas[$i]["CONTAINERNUMBER"] ?></td>
                          <td class="small" width="10"><?= $tabla_toneladas[$i]["INVOICENUMBER"] ?></td>
                          <td class="small" width="10"><?= $tabla_toneladas[$i]["PARTNO"] ?></td>
                          <td class="small" width="10" align="center"><?= $tabla_toneladas[$i]["QUANTITY"] ?></td>
                          <td class="small" width="10"><?= $tabla_toneladas[$i]["ETA"] ?></td>
                          <td class="small" width="10"><?= $tabla_toneladas[$i]["D_FEC_LLEGADA_REAL"] ?></td>

                        </tr>
                        <?php } ?>
                      </tbody>

                    </table>
                  </div>

                </div><!--/.box-body-->
              </div>
            </section>

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
        <a href="Contenedores_Pendientes_ALO.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
        <?php } ?>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <!-- FILTRAR POR CONTRATO-->
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
          <input type="text" class="form-control pull-right" name="nomFecha">
        </div>
        <!-- FILTRAR POR PLAZA -->
        <input type="hidden" id="nomPlaza" value="<?=$plaza?>">
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
        </div>-->
        <div class="input-group">
          <span class="input-group-addon"> <input type="checkbox" name="fil_habilitado" <?php if( $fil_habilitado == 'on' ){ echo "checked";} ?> > ASIGNADOS</span>
        </div>
        <div class="input-group">
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>

      </div><!--/.box-body-->
    </div>

    </div><!-- /.col-md-3 -->

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
/*---- SELECT TIPO DE NOMINA ----*/
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
  status = $('#nomStatus').val();
  contrato = $('#nomContrato').val();
  depto = $('#nomDepto').val();
  almacen = $('#nomAlm').val();
  fil_habilitado = 'off';

  if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?plaza='+plaza+'&fil_habilitado='+fil_habilitado+'&fecha='+fecha;
  }
  else {
    fil_habilitado = 'off';
    url = '?plaza='+plaza+'&fil_habilitado='+fil_habilitado+'&fecha='+fecha;
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
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#tabla_nomina').DataTable( {
        "searching":true,
        "ordering": true,
        "scrollY": 450,
        fixedHeader: true,
        "dom": '<"toolbar">frtip',
        stateSave: true,
        "scrollX": true,
        columnDefs: [
            { width: 30, targets: 0 }
        ],
        fixedColumns: true,
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
                title: 'Reporte Alo',
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

function number_format(amount, decimals) {

        amount += ''; // por si pasan un numero en vez de un string
        amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

        decimals = decimals || 0; // por si la variable no fue fue pasada

        // si no es un numero o es igual a cero retorno el mismo cero
        if (isNaN(amount) || amount === 0)
            return parseFloat(0).toFixed(decimals);

        // si es mayor o menor que cero retorno el valor formateado como numero
        amount = '' + amount.toFixed(decimals);

        var amount_parts = amount.split('.'),
            regexp = /(\d+)(\d{3})/;

        while (regexp.test(amount_parts[0]))
            amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

        return amount_parts.join('.');
    }

</script>

<script type="text/javascript">
$(document).ready(function() {

    $('#tabla_nomina2').DataTable( {
      "lengthMenu": [[25, 25, -1], [25, 25, "All"]],
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
              .column( 2 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

          // Total over this page
          pageTotal = api
              .column( 2, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var number = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(number);
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

          // Update footer
          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal , 2)
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
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_nomina3').DataTable( {
      "lengthMenu": [[25, 25, -1], [25, 25, "All"]],
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
              .column( 4 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 2 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );

          // Total over this page
          pageTotal = api
              .column( 4, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var number = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(number);
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 3, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                    var number = intVal(a) + intVal(b);
                    return Intl.NumberFormat('es-MX').format(number);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 2, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                        var number = intVal(a) + intVal(b);
                        return Intl.NumberFormat('es-MX').format(number);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal , 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2 , 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3 , 2)
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
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>


<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_nomina4').DataTable( {
      "lengthMenu": [[25, 25, -1], [25, 25, "All"]],
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
              .column( 4 )
              .data()
              .reduce( function (a, b) {
                  return Intl.NumberFormat().format(intVal(a) + intVal(b));
                  //return intVal(a) + intVal(b);
                  //return parseFloat(intVal(a)) + parseFloat(intVal(b));
              }, 0 );

              total2 = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return Intl.NumberFormat().format(intVal(a) + intVal(b));
                      //return intVal(a) + intVal(b);
                      //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                  }, 0 );

                  total3 = api
                      .column( 2 )
                      .data()
                      .reduce( function (a, b) {
                          return Intl.NumberFormat().format(intVal(a) + intVal(b));
                          //return intVal(a) + intVal(b);
                          //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                      }, 0 );

          // Total over this page
          pageTotal = api
              .column( 4, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                var number = intVal(a) + intVal(b);
                return Intl.NumberFormat('es-MX').format(number);
                  //return Math.round(intVal(a) + intVal(b));
              }, 0 );

              pageTotal2 = api
                  .column( 3, { page: 'current'} )
                  .data()
                  .reduce( function (a, b) {
                    var number = intVal(a) + intVal(b);
                    return Intl.NumberFormat('es-MX').format(number);
                      //return Math.round(intVal(a) + intVal(b));
                  }, 0 );

                  pageTotal3 = api
                      .column( 2, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                        var number = intVal(a) + intVal(b);
                        return Intl.NumberFormat('es-MX').format(number);
                          //return Math.round(intVal(a) + intVal(b));
                      }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal , 2)
          );

          $( api.column( 3 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal2 , 2)
          );

          $( api.column( 2 ).footer() ).html(
              //''+pageTotal +' ('+ total +' total)'
              ''+number_format(pageTotal3 , 2)
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
