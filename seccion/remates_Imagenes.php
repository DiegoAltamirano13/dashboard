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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 20);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/descargaImagenes.php';
$descargaImagen = new descargaImagenes();

$carga_Imagen = $descargaImagen->descargaImagen();
$tabla_Remates = $descargaImagen->tablaImagenes();

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
    <h1>Dashboard<small>Remates.</small></h1>
  </section>

  <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

  <!-- ############################ TABLA DETALLE DE NOMINA PAGADA ############################# -->
  <section>
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-table"></i> REMATES</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <div class="table-responsive" id="container">
          <table id="tabla_remates" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="small" bgcolor="#383F6D"><font color="white">ID</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">MERCANCIA</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">VALOR</font></th>
                <th class="small" bgcolor="#383F6D"><font color="white">GALERIA</font></th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i <count($tabla_Remates) ; $i++) { ?>
              <tr>
                <td class="small"><?= $tabla_Remates[$i]["IID_CONSECUTIVO"] ?></td>
                <td class="small"><?= $tabla_Remates[$i]["V_TIPO_MERCANCIA"] ?></td>
                <td class="small"><?= "$".number_format($tabla_Remates[$i]["N_VALOR_MERCANCIA"], 2, '.', ' '); ?></td>
                <td class="small"><button type="button" name="button" data-toggle='modal' data-target='#asignacion_activos'
                    onclick='cargarImagen(<?= $tabla_Remates[$i]["IID_CONSECUTIVO"]?>)'
                    ><img src="../dist/img/modulos/galeria.png" width="50px" height="50px"></img></button></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>

      </div><!--/.box-body-->
    </div>
  </section>

  <div class="modal fade" id="asignacion_activos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
       <div class="modal-header">
         <h5 class="modal-title"> Remates Imagenes</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
              <div class="modal-body" id='modal'>
              </div>
       <div class="modal-footer">
       <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
       </div>
   </div>
 </div>
</div>

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
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript" src="../plugins/fancybox/lib/jquery.mousewheel.pack.js?v=3.1.3"></script>
  <!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="../plugins/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
  <!-- Add Button helper (this is optional) -->
<script type="text/javascript" src="../plugins/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
  <!-- Add Thumbnail helper (this is optional) -->
<script type="text/javascript" src="../plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
<!-- Termina FancyBox JS -->
<!-- PACE -->
<script src="../plugins/pace/pace.min.js"></script>
<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_remates').DataTable( {
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
<!-- Custom styles for this template -->
<link href="../libs/librerias_slider/assets/sticky-footer-navbar.css" rel="stylesheet">

<!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> -->
<script>
     function cargarImagen(iidconsecutivo){
               $("#modal").load("../class/imagenes.php?iidconsecutivo="+iidconsecutivo+"");
     }
</script>
<style>
.carousel-inner > .item > img {
  object-fit: scale-down;
  height: 50vh;
  width: 100%;
}
</style>
<script>
$(document).ready(function () {
    //Duracion del slider
    $('.carousel').carousel({
        interval: 7000
    });

    $('#myCarousel').on('slid.bs.carousel', function () {
        //Recuperar el valor de los datos datos de la diapositiva a estando activo
        var numeroSlide = $('#valor-car.active').data('slide-to');
        //$("#msg").html(numeroSlide);

        //Ocultar descripcion anterior
        $('.contenido').hide();

        //Apresentar o contenido hacer diapositiva
        $('.imagen' + numeroSlide).show();
    });
});
</script>
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
