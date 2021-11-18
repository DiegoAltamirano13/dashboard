<?php
ini_set('display_errors', false);
/*inicia code solucion quita mensaje reenvio de form*/
if( $_SERVER['REQUEST_METHOD'] == 'POST')
{
  header("location: ".$_SERVER["PHP_SELF"]." ");
}
/*termina code solucion quita mensaje reenvio de form*/

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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '38');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/* HORA MEXICO */
$time = time();
date_default_timezone_set("America/Mexico_City");

//----------------INICIA SESSIONES PARA AGRONEGOCIOS----------------//
/*SESSION PARA PLAZA AGRONEGOCIO*/
if (empty($_SESSION['counter']))
  $_SESSION['counter'] = 1;
else
  $_SESSION['counter']++;

if ( $_SESSION['counter'] == 1 )  {
  $_SESSION["agro_plaza"] = "CÓRDOBA (ARGO)";
  $agro_plaza = $_SESSION["agro_plaza"];
}else{
  if( isset($_POST["agro_plaza"]))
  $_SESSION["agro_plaza"] = $_POST["agro_plaza"];
  $agro_plaza = $_SESSION["agro_plaza"];
}

/*SESSION PARA EL HISTORIAL AGRONEGOCIO*/
if ($_SESSION["agro_historial"]==false){
  $_SESSION["agro_historial"] = date("d-m-Y", $time);
  $agro_historial = $_SESSION["agro_historial"];
}else{
if( isset($_POST["agro_historial"]))
  $_SESSION["agro_historial"] = $_POST["agro_historial"];
  $agro_historial = $_SESSION["agro_historial"];
}
/*SESSION PARA FECHA PERSONALIZADA AGRONEGOCIO*/
//FECHA INICIO
if( isset($_POST["fec_ini_agro"]))
  $_SESSION["fec_ini_agro"] = $_POST["fec_ini_agro"];
  $fec_ini_agro = $_SESSION["fec_ini_agro"];
//FECHA FIN
if( isset($_POST["fec_fin_agro"]))
  $_SESSION["fec_fin_agro"] = $_POST["fec_fin_agro"];
  $fec_fin_agro = $_SESSION["fec_fin_agro"];
/*SESSION PARA ALMACEN AGRONEGOCIO*/
if( isset($_POST["agro_almacen"]))
  $_SESSION["agro_almacen"] = $_POST["agro_almacen"];
  $agro_almacen = $_SESSION["agro_almacen"];
/*SESSION PARA CLIENTE AGRONEGOCIO*/
if( isset($_POST["agro_cliente"]))
  $_SESSION["agro_cliente"] = $_POST["agro_cliente"];
  $agro_cliente = $_SESSION["agro_cliente"];
//----------------TERMINA SESSIONES PARA AGRONEGOCIOS----------------//
//titulo para fecha
if($agro_historial == true && $fec_ini_agro == true && $fec_fin_agro == true ){
  $titulo_fecha = $fec_ini_agro."|".$fec_fin_agro;
}else{
  $titulo_fecha = $agro_historial;
}
//----------------INICIA INSTANCIAS DE OBJETOS ----------------//
include_once '../class/Mapas_Operaciones.php';
$obj_agro_carga = new Mapas_Operaciones();
$tablaBuques = $obj_agro_carga->buquesDetalles();
$tablaBuquesHist = $obj_agro_carga->buquesDetallesHist();
$tablaBuquesHist2 = $obj_agro_carga->buquesDetallesHist2();

//----------------TERMINA INSTANCIAS DE OBJETOS ----------------//

///////////////////////////////////////////
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<!-- CSS PARA DISEÑO DE LINEA DE TIEMPO -->
<link rel="stylesheet" href="../plugins/line_time.css">
<style type="text/css" media="screen">
 div.dataTables_wrapper {
      width: 800px;
      margin: 0 auto;
  }
</style>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>

        Dashboard
        <small>Ubicación Embarques</small>
      </h1>
    </section>
    <!-- Main content -->
    <section>
    <div class="box box-primary">
      <div class="box-header with-border">
          <script type="text/javascript">
            	width='100%';		// the width of the embedded map in pixels or percentage
            	height='450';		// the height of the embedded map in pixels or percentage
            	border='1';		// the width of the border around the map (zero means no border)
            	shownames='false';	// to display ship names on the map (true or false)
            	latitude='21.31052';	// the latitude of the center of the map, in decimal degrees
            	longitude='-89.670455';	// the longitude of the center of the map, in decimal degrees
            	zoom='9';		// the zoom level of the map (values between 2 and 17)
            	maptype='1';		// use 0 for Normal Map, 1 for Satellite, 2 for OpenStreetMap
            	trackvessel='0';	// MMSI of a vessel (note: vessel will be displayed only if within range of the system) - overrides "zoom" option
            	fleet='';		// the registered email address of a user-defined fleet (user's default fleet is used)
        </script>
        <script type="text/javascript" src="//www.marinetraffic.com/js/embed.js"></script>
      </div>
    </div>
  </section> <!--Termina mAP -->
    <section>

      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-list-alt"></i>LISTA DE BUQUES</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
      </div>

      <div class="box-body"><!--box-body-->

        <div class="nav-tabs-custom">

          <ul class="nav nav-pills" id="myTab">
            <li class="active"><a href="#tab_corporativo" data-toggle="tab"><i class="fa fa-ship"></i> TRANSITOS ACTIVOS</a>
            </li>
            <li><a href="#tab_golfo" data-toggle="tab"><i class="fa fa-ship"></i> PROGRAMACION API</a>
            </li>
            <li><a href="#tab_terminados" data-toggle="tab"><i class="fa fa-ship"></i> HISTORICO BUQUES</a>
            </li>
            <li><a href="#tab_terminadosF" data-toggle="tab"><i class="fa fa-ship"></i> HISTORICO FERROCARRILES</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_corporativo">

              <div class="table-wrapper">
                <table id="tabla_activo" class="display table table-bordered table-hover " cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="small" bgcolor="#0073B7"><font color="white">FFCC/BUQUE</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">CLIENTE</font></th>
                      <!--<th class="small" bgcolor="#0073B7"><font color="white">CD-S</font></th>-->
                      <th class="small" bgcolor="#0073B7"><font color="white">MERCANCIA</font></th>
                      <!--<th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>-->
                      <th class="small" bgcolor="#0073B7"><font color="white">TONELADAS A ARRIBAR</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">DESCARGADAS</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">RESTANTES</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">PORCENTAJE ARRIBADO</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">PORCENTAJE RESTANTE</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">DETALLES</font></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i <count($tablaBuques) ; $i++) { ?>
                    <tr>
                      <td><?= $tablaBuques[$i]["V_NOM_BUQUE"]?></td>
                      <td><?= $tablaBuques[$i]["V_RAZON_SOCIAL"] ?></td>
                      <td><?= $tablaBuques[$i]["V_DESCRIPCION"] ?></td>
                      <td><?= number_format($tablaBuques[$i]["N_CANTIDAD"], 2) ?></td>
                      <td><?= number_format($tablaBuques[$i]["TON_DESCARGADAS"], 2) ?></td>
                      <td><?php
                            $toneladas_reales = $tablaBuques[$i]["N_CANTIDAD"];
                            $toneladas_reales_descargadas = $tablaBuques[$i]["TON_DESCARGADAS"];
                            ?>
                           <?= number_format($toneladas_reales-$toneladas_reales_descargadas, 2) ?></td>
                      <td> <?php
                              $restantes =$toneladas_reales - $toneladas_reales_descargadas;
                           ?>
                        <?= number_format(($toneladas_reales_descargadas/$toneladas_reales)*100, 2) ?>%
                      </td>
                      <td><?= number_format(($restantes/$toneladas_reales)*100, 2) ?>%</td>
                      <td class="small">

                          <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                          onclick='cargarPantalla(". $tablaBuques[$i]["IID_NUM_CDT"].")'>Ver</button>";
                          ?>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

            </div>

            <div class="tab-pane" id="tab_golfo">

              <div class="table-responsive">
                <div class="box box-primary">
                  <div class="box-header with-border" align="center">
                      <iframe src="https://www.puertosyucatan.com/cgi-bin/arribos2.cgi" width="920" scrolling="No" frameborder="0" height="1800">Lo
                                            sentimos necesita actualizar su explorador.</iframe>
                                          <p >Fuente: Sistema Informático DASHBOARD</p>
                  </div>
                </div>
              </div>

            </div>
            <div class="tab-pane" id="tab_terminados">

              <div class="table-wrapper">
                <table id="tabla_activo2" class="display table table-bordered table-hover " cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="small" bgcolor="#0073B7"><font color="white">NOMBRE BUQUE</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">CLIENTE</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">NUM CDT</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">TENEDOR</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">MERCANCIA</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">TONELADAS A ARRIBAR</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">VALOR</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">DESTINO</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">EMISION CDT</font></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i <count($tablaBuquesHist) ; $i++) { ?>
                    <tr>
                      <td><?= $tablaBuquesHist[$i]["V_NOM_BUQUE"]?></td>
                      <td><?= $tablaBuquesHist[$i]["V_RAZON_SOCIAL"] ?></td>
                      <td><?= $tablaBuquesHist[$i]["IID_NUM_CDT"]?></td>
                      <td><?= $tablaBuquesHist[$i]["V_TENEDOR"]?></td>
                      <td><?= $tablaBuquesHist[$i]["V_DESCRIPCION"] ?></td>
                      <td><?= number_format($tablaBuquesHist[$i]["N_CANTIDAD"], 2) ?></td>
                      <td><?= number_format($tablaBuquesHist[$i]["N_VALOR"], 2) ?></td>
                      <td><?= $tablaBuquesHist[$i]["PLAZA"] ?></td>
                      <td><?= $tablaBuquesHist[$i]["FECHA_EMISION"] ?></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

            </div>
            <div class="tab-pane" id="tab_terminadosF">

              <div class="table-wrapper">
                <table id="tabla_activo3" class="display table table-bordered table-hover " cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="small" bgcolor="#0073B7"><font color="white">FERROCARRIL</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">CLIENTE</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">NUM CDT</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">TENEDOR</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">MERCANCIA</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">TONELADAS A ARRIBAR</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">VALOR</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">DESTINO</font></th>
                      <th class="small" bgcolor="#0073B7"><font color="white">EMISION CDT</font></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i <count($tablaBuquesHist2) ; $i++) { ?>
                    <tr>
                      <td><?= $tablaBuquesHist2[$i]["V_NOM_BUQUE"]?></td>
                      <td><?= $tablaBuquesHist2[$i]["V_RAZON_SOCIAL"] ?></td>
                      <td><?= $tablaBuquesHist2[$i]["IID_NUM_CDT"]?></td>
                      <td><?= $tablaBuquesHist2[$i]["V_TENEDOR"]?></td>
                      <td><?= $tablaBuquesHist2[$i]["V_DESCRIPCION"] ?></td>
                      <td><?= number_format($tablaBuquesHist2[$i]["N_CANTIDAD"], 2) ?></td>
                      <td><?= number_format($tablaBuquesHist2[$i]["N_VALOR"], 2) ?></td>
                      <td><?= $tablaBuquesHist2[$i]["PLAZA"] ?></td>
                      <td><?= $tablaBuquesHist2[$i]["FECHA_EMISION"] ?></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
          <!-- /.tab-content -->
        </div>

      </div>

      <div class="modal fade bd-example-modal-xl" id="asignacion_activos" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
       <div class="modal-dialog  modal-lg" role="document">
         <div class="modal-content">
                  <div class="modal-body" id='modal'>
                  </div>
           <div class="modal-footer">
           <button type="button" class="btn btn-primary" onclick="javascript:imprSeleccion('modal')">Imprimir</button>
           <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
           </div>
       </div>
      </div>
      </div>


    </section>
    <!--<div class="box box-primary">
      <div class="box-header with-border" align="center">
          <iframe src="https://www.puertodeveracruz.com.mx/datosBuques/principal.php?jmlp=1" width="920" scrolling="No" frameborder="0" height="1800">Lo
                                sentimos necesita actualizar su explorador.</iframe>
                              <p >Fuente: Sistema Informático DASHBOARD</p>
      </div>
    </div>-->


  </div>


<!-- ########################### TERMINA SECCION OPERACIONES CARGAS ########################### -->

<!-- ########################### TERMINA SECCION OPERACIONES DESCARGAS ########################### -->
<?php
//OPERACIONES CARGAS-DESCARGAS EN PROCESO,FIN,TOTAL
$total_carga_proceso = count($carga_proceso);
$total_cargas_finalizadas = count($cargas_finalizadas);
$total_cargas_agro = $total_carga_proceso+$total_cargas_finalizadas;

$total_descarga_proceso = count($descarga_proceso);
$total_descargas_finalizadas = count($descargas_finalizadas);
$total_descargas_agro = $total_descarga_proceso+$total_descargas_finalizadas;
?>


    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>


<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<script>
function realizaProceso(v_numero, v_mensaje){
        var parametros = {
                "numero" : v_numero,
                "mensaje" : v_mensaje
        };
        $.ajax({
                data:  parametros,
                url:   '../action/enviar_whats.php',
                type:  'post',
                beforeSend: function () {
                        $("#resultado\\[\\]").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                        $("#resultado\\[\\]").html(response);
                }
        });
}
</script>
<script>
$(document).ready(function(){

  $("#btn_his_car").click(function(){
    $("#div_historial_cargas\\[\\]").show()
    $("#div_historial_descargas\\[\\]").hide()
  });

  $("#btn_his_des").click(function(){
    $("#div_historial_descargas\\[\\]").show();
    $("#div_historial_cargas\\[\\]").hide();
  });

});

// script para poner total status de operaciones
$(document).ready(function(){
  $("#widgets_cargas_fin_agro\\[\\]").text('<?= $total_cargas_finalizadas ?>');
  $("#widgets_cargas_pro_agro\\[\\]").text('<?= $total_carga_proceso ?>');
  $("#widgets_cargas_t_agro\\[\\]").text('<?= $total_cargas_agro ?>');

  $("#widgets_descargas_fin_agro\\[\\]").text('<?= $total_descargas_finalizadas ?>');
  $("#widgets_descargas_pro_agro\\[\\]").text('<?= $total_descarga_proceso ?>');
  $("#widgets_descargas_t_agro\\[\\]").text('<?= $total_descargas_agro ?>');
})
</script>
<!-- GUARDA TAB SELECCIONADO -->
<script type="text/javascript">
$(function() {
  $('#tab_ofc_proceso').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_ofc_status', $(e.target).attr('href'));
    });

    $('#tab_ofc_finalizado').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_ofc_status', $(e.target).attr('href'));
    });

    var activeTab_ofc_status = localStorage.getItem('activeTab_ofc_status');
  if(activeTab_ofc_status){
    $('#myTab_ofc a[href="' + activeTab_ofc_status + '"]').tab('show');
  }
/////////////////////////////////////////////////////////////////////
    $('#tab_otfc_cor').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_otfc', $(e.target).attr('href'));
    });

    $('#otfc_occ').on('shown.bs.tab', function (e) {
       localStorage.setItem('activeTab_otfc', $(e.target).attr('href'));
    });

    var activeTab_otfc = localStorage.getItem('activeTab_otfc');
  if(activeTab_otfc){
    $('#myTab_otfc a[href="' + activeTab_otfc + '"]').tab('show');
  }
});
</script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Inicia Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">

  $(".agro_historial").select2({
   placeholder: "Elija una opción",
  allowClear: true
});
</script>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>

<script>
$('#fec_rango_agro\\[\\]').daterangepicker(
        {
        "linkedCalendars": false,
        "showDropdowns": true,
      //INICIA CODE OPCION PARA FORMATO EN ESPAÑOL
        "locale": {
        "format": "DD-MM-YYYY",
        "separator": " - ",
        "applyLabel": "Aplicar",
        "cancelLabel": "Cancelar",
        "fromLabel": "From",
        "toLabel": "To",
        "customRangeLabel": "Fecha Personalizada",
        "daysOfWeek": [
            "Do",
            "Lu",
            "Ma",
            "Mi",
            "Ju",
            "Vi",
            "Sa"
        ],
        "monthNames": [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agusto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ],
        "firstDay": 1
        },
      //TERMINA CODE OPCION PARA FORMATO EN ESPAÑOL
          ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Los últimos 7 días': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'El mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function (start, end) {
          $('#fec_ini_agro\\[\\]').val(start.format('DD-MM-YYYY'));
          $('#fec_fin_agro\\[\\]').val(end.format('DD-MM-YYYY'));
        }
    );
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
<script src="../plugins/datatables/extensions/buttons_datatable/buttons.print.min.js"></script>>
<!-- Inicia FancyBox JS -->
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
      "scrollX": 1400,
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
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
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

    $('#tabla_activo2').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
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
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
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

    $('#tabla_activo3').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
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
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
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
  <!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript" src="../plugins/fancybox/lib/jquery.mousewheel.pack.js?v=3.1.3"></script>

<script>
     function cargarPantalla(iidconsecutivo){
               $("#modal").load("buques_det.php?iid_emple="+iidconsecutivo+"");
               //alert("Hello! I am an alert box!!");
     }
</script>

<script language="Javascript">
	function imprSeleccion(nombre) {
	  var ficha = document.getElementById(nombre);
	  var ventimp = window.open(' ', 'popimpr');
	  ventimp.document.write( ficha.innerHTML );
	  ventimp.document.close();
	  ventimp.print( );
	  ventimp.close();

	}
	</script>

  <script type="text/javascript">
  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
     $($.fn.dataTable.tables(true)).DataTable()
        .columns.adjust()
        .responsive.recalc();
  });

  $(document).ready(function() {

      $('#tabla_activos').DataTable( {
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
              title: 'Buques Argo',
            },

            {
              extend: 'print',
              text: '<i class="fa fa-print"></i>',
              titleAttr: 'Imprimir',
              exportOptions: {//muestra/oculta visivilidad de columna
                  columns: ':visible',
              },
              title: 'Buques Argo',
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

$('#click_modal_his\\[\\]').on('click change',function(){
  $.ajax({url: '../class/Manufactura.php', success: function(result){
      $('#content_img_modal_his\\[\\]').fadeIn(10).html('<div><img class="img-responsive center-block" src="../dist/img/gif-argo-carnado-circulo_l2.gif"/></div>');
  }});
});

<?php for ($i=1; $i <=10; $i++) { ?>
$('.click_btn_text<?=$i?>').click(function(){
  $.ajax({url: '../class/Agronegocios.php', success: function(result){
    $('.content_text_btn<?=$i?>').fadeIn(10).html('<b class="text-blue"><i class="fa fa-cog fa-spin fa-lg fa-fw"></i> CARGANDO...</b>');
  }});
});
<?php } ?>
</script>
</html>
<?php conexion::cerrar($conn); ?>
