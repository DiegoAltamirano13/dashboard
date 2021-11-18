<?php

ini_set('display_errors', false);
//phpinfo();
//PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
if( $_SERVER['REQUEST_METHOD'] == 'POST')
{
  header("location: ".$_SERVER["PHP_SELF"]." ");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '13');
if($modulos_valida == 0)
{
  header('Location: index.php');
}

///////////////INSTANCIAS PHPExcel
require_once('../plugins/PHPExcel.php');
require_once('../plugins/PHPExcel/Reader/Excel2007.php');
$obj_reader_excel = new PHPExcel_Reader_Excel2007();
//////////////INSTANCIA INFORMACION FINANCIERA
include_once('../class/Informacion_financiera.php');
$obj_info_financiera = new Informacion_financiera();
/////////////////////INICIA SESSIONES PARA TESORERIA
if ($_SESSION['historial_info_finan_id'] == false){
  $info_finan_ultimo = $obj_info_financiera->info_finan_ultimo();
  $_SESSION['historial_info_finan_id'] = $info_finan_ultimo["ID_EXCEL"];
  $historial_info_finan_id = $_SESSION['historial_info_finan_id'];
}else{
  if(isset($_POST['historial_info_finan_id']))
  $_SESSION['historial_info_finan_id'] = $_POST['historial_info_finan_id'];
  $historial_info_finan_id = $_SESSION['historial_info_finan_id'];
}
///////////////////////////////////////////
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>

<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>

        Dashboard
        <small>Información Financiera</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="" data-toggle="modal" data-target="#modal_historial_tesoreria"><i class="fa fa-history"></i> Historial</a></li>
      </ol>

      <?php
      //PRIMERO LEEMOS EL ULTIMO REGISTRO DE LA BASE
        $leer_excel_phpexcel = $obj_info_financiera->leer_excel_phpexcel($historial_info_finan_id);
        for ($i=0; $i<count($leer_excel_phpexcel); $i++){
          $nombre_load_excel = $leer_excel_phpexcel[$i]["NOMBRE_EXCEL"];
          $fecha_load_excel = $leer_excel_phpexcel[$i]["FECHA"];
        }

        //Si hay un registro en la base lee el documento
        if ($nombre_load_excel == true){

          //Comprobamos si existe el archivo
          if (file_exists("../uploads_files/".$nombre_load_excel."") )
          {
            // Especificamos el Excel a leer
           $excel_coberturas = $obj_reader_excel->load("../uploads_files/".$nombre_load_excel."");
            //$excel_coberturas -> setActiveSheetIndex(0);
            //echo "pruebas_ here";

            //COMPRUEBA SI EXISTE LA HOJA ESPECIFICADA
            try {
             //Cuenta cuantas filas tiene una hoja en especifico
             $t_filas_coberturas = $excel_coberturas->setActiveSheetIndexByName("CAPs (Coberturas)")->getHighestRow();
            }
            // SI NO EXISTE LA HOJA ESPECIFICADA ERROR
            catch (Exception $e) {
                echo '<div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-info"></i> Aviso!</h4>
                No existe la hoja CAPs (Coberturas) en el Archivo <code>'.$nombre_load_excel.'</code>!!!
                </div>';
            }

          }else{//si no existe el archivo
            echo '<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-warning"></i> Aviso!</h4>
                NO HAY NINGÚN ARCHIVO EN EL SERVIDOR CON EL NOMBRE <code>'.$nombre_load_excel.'</code>!!!
                </div>';
            $excel_coberturas = $obj_reader_excel->load("../uploads_files/excel.xlsx");
            //Cuenta cuantas filas tiene una hoja en especifico
            $t_filas_coberturas = $excel_coberturas->setActiveSheetIndex(0)->getHighestRow();
          }
        }
        //si no existe ningun registro en la base lee un documento por defecto
        else{
          echo '<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-warning"></i> Aviso!</h4>
                NO HAY UN REGISTRO EN LA BASE O NO HAY UNA FECHA SELECCIONADA EN EL HISTORIAL
                </div>';
          $excel_coberturas = $obj_reader_excel->load("../uploads_files/excel.xlsx");
        }
      ?>

    </section>
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->



<!-- ############################ INICIA SECCION DE MOADALS ############################# -->

<!-- /*INICIA MODAL PARA EL HISTORIAL*/ -->
<div class="modal fade" id="modal_historial_tesoreria" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"> <i class="fa fa-history"></i> Historial</h4>
        </div>
        <div class="modal-body">

          <form method="post">
            <div class="form-group">
              <select  onchange="this.form.submit();" name="historial_info_finan_id" class="form-control select_hist_tesoreria" style="width: 100%;">
                <option value=""></option>
                <?php
                 $historial_info_finan = $obj_info_financiera->historial_info_finan();
                  for ($i=0; $i <count($historial_info_finan) ; $i++) {
                  echo '<option title="'.$historial_info_finan[$i]['TITULO_EXCEL'].'" value="'.$historial_info_finan[$i]['ID_EXCEL'].'">'.$historial_info_finan[$i]['FECHA'].' (ID:'.$historial_info_finan[$i]['ID_EXCEL'].')</option> ';
                }
                ?>
              </select>
            </div>
          </form>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<!-- /*TERMINA MODAL PARA EL HISTORIAL*/ -->

<!-- ############################ INICIA SECCION DE MOADALS ############################# -->




<!-- ######################################## Inicio de Widgets ######################################### -->
    <section><!-- Inicia la seccion de los Widgets -->
      <div class="row">

      <h4 class="content-header text-blue text-center"><i class="ion-ios-pie"></i> COBERTURAS SOBRE TASA DE INTERES <code><?=$fecha_load_excel?></code></h4><hr>

      <!-- Widgets Numero de cargas -->
        <div class="col-md-4 col-sm-612 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?=$excel_coberturas->getActiveSheet()->getCell('E11')->getFormattedValue(); ?></h3>
              <p>MONTO DEL CREDITO</p>
            </div>
            <div class="icon">
              <i class="ion-social-usd"></i>
            </div>
             <!-- <button type="submit" name="tipo" id="tipo" value="1" class="btn bg-aqua  btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button>  -->
          </div>
        </div>
        <!-- Widgets Numero de descargas -->
        <div class="col-md-4 col-sm-612 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?=$excel_coberturas->getActiveSheet()->getCell('G11')->getFormattedValue(); ?></h3>
              <p>PRIMA PAGADA</p>
            </div>
            <div class="icon">
              <i class="ion-cash"></i>
            </div>
             <!-- <button type="submit" name="tipo" id="tipo" value="2" class="btn bg-green btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
          </div>
        </div>
        <!-- Widgets Cargas a tiempo -->
        <div class="col-md-4 col-sm-612 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?=$excel_coberturas->getActiveSheet()->getCell('E30')->getFormattedValue(); ?></h3>
              <p>MONTO LIQUIDAR</p>
            </div>
            <div class="icon">
              <i class="fa fa-calculator"></i>
            </div>
            <!-- <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-yellow btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button>  -->
          </div>
        </div>
        <!-- Termino Widgets Desfasados -->
      </div>
      <!-- /.row -->
      </section><!-- Termina la seccion de los Widgets -->
<!-- ######################################### Termino de Widgets ######################################### -->





<!-- ############################ INICIA SECCION DE LA GRAFICA OTROS POR PLAZA ############################# -->
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title"></h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

      <div id="graf_coberturas" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>

      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th colspan="6" class="bg-blue text-center">CAPS SCOTIABANK</th>
          </tr>
          <tr>
            <th>FECHA INICIO</th>
            <th>FECHA FIN</th>
            <th>MONTO DEL CREDITO MXN</th>
            <th>STRIKE</th>
            <th>PRIMA PAGADA MXN</th>
            <th>PRIMA %</th>
          </tr>
        </thead>
        <tbody>
        <?php
        for($i=7; $i<$t_filas_coberturas-22; $i++){
        ?>
          <tr>
            <td><?= $excel_coberturas->getActiveSheet()->getCell('C'.$i)->getFormattedValue(); ?></td>
            <td><?= $excel_coberturas->getActiveSheet()->getCell('D'.$i)->getFormattedValue(); ?></td>
            <td><?= "$".$excel_coberturas->getActiveSheet()->getCell('E'.$i)->getFormattedValue(); ?></td>
            <td><?= $excel_coberturas->getActiveSheet()->getCell('F'.$i)->getFormattedValue(); ?></td>
            <td><?= "$".$excel_coberturas->getActiveSheet()->getCell('G'.$i)->getFormattedValue(); ?></td>
            <td><?= $excel_coberturas->getActiveSheet()->getCell('I'.$i)->getFormattedValue(); ?></td>
          </tr>
        <?php } ?>
        <?php
        for($i=11; $i<$t_filas_coberturas-21; $i++){
        ?>
          <tr class="bg-gray">
            <th><?= $excel_coberturas->getActiveSheet()->getCell('C'.$i)->getFormattedValue(); ?></th>
            <th><?= $excel_coberturas->getActiveSheet()->getCell('D'.$i)->getFormattedValue(); ?></th>
            <th><?= "$".$excel_coberturas->getActiveSheet()->getCell('E'.$i)->getFormattedValue(); ?></th>
            <th><?= $excel_coberturas->getActiveSheet()->getCell('F'.$i)->getFormattedValue(); ?></th>
            <th><?= "$".$excel_coberturas->getActiveSheet()->getCell('G'.$i)->getFormattedValue(); ?></th>
            <th><?= $excel_coberturas->getActiveSheet()->getCell('I'.$i)->getFormattedValue(); ?></th>
          </tr>
        <?php } ?>
        </tbody>
      </table>

      <br><br>

      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th colspan="7" class="bg-blue text-center">LIQUIDACION DE COBERTURAS</th>
          </tr>
          <tr>
            <th>CONTRATO</th>
            <th>FECHA DE LIQUIDACION</th>
            <th>MONTO LIQUIDAR</th>
            <th>STRIKE</th>
            <th>PRECIO SPOT</th>
            <th>DIFERENCIAL TASAS</th>
            <th>SALDO CREDITO</th>
          </tr>
        </thead>
        <tbody>
        <?php
        for($i=15; $i<$t_filas_coberturas-3; $i++){
        ?>
          <tr>
            <td><?= $excel_coberturas->getActiveSheet()->getCell('C'.$i)->getFormattedValue(); ?></td>
            <td><?= $excel_coberturas->getActiveSheet()->getCell('D'.$i)->getFormattedValue(); ?></td>
            <td><?= "$".$excel_coberturas->getActiveSheet()->getCell('E'.$i)->getFormattedValue(); ?></td>
            <td><?= $excel_coberturas->getActiveSheet()->getCell('F'.$i)->getFormattedValue(); ?></td>
            <td><?= $excel_coberturas->getActiveSheet()->getCell('G'.$i)->getFormattedValue(); ?></td>
            <td><?= $excel_coberturas->getActiveSheet()->getCell('H'.$i)->getFormattedValue() ?></td>
            <td><?= "$".$excel_coberturas->getActiveSheet()->getCell('I'.$i)->getFormattedValue(); ?></td>
          </tr>
        <?php } ?>
        <?php
        for($i=30; $i<$t_filas_coberturas-2; $i++){
        ?>
          <tr class="bg-gray">
            <th><?= $excel_coberturas->getActiveSheet()->getCell('C'.$i)->getFormattedValue(); ?></th>
            <th><?= $excel_coberturas->getActiveSheet()->getCell('D'.$i)->getFormattedValue(); ?></th>
            <th><?= "$".$excel_coberturas->getActiveSheet()->getCell('E'.$i)->getFormattedValue(); ?></th>
            <th><?= $excel_coberturas->getActiveSheet()->getCell('F'.$i)->getFormattedValue(); ?></th>
            <th><?= $excel_coberturas->getActiveSheet()->getCell('G'.$i)->getFormattedValue(); ?></th>
            <th><?= $excel_coberturas->getActiveSheet()->getCell('H'.$i)->getCalculatedValue(); ?></th>
            <th><?= $excel_coberturas->getActiveSheet()->getCell('I'.$i)->getFormattedValue(); ?></th>
          </tr>
        <?php } ?>
        </tbody>
      </table>

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION DE LA GRAFICA OTROS POR PLAZA ########################### -->






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
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
  $(".select_hist_tesoreria").select2({
   placeholder: "Elija una Fecha",
  allowClear: true
});
</script>
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<script>
$(document).ready(function () {
Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
  });

    // Build the chart
    Highcharts.chart('graf_coberturas', {
        lang: {
            printChart: 'Imprimir Grafica',
            downloadPNG: 'Descargar PNG',
            downloadJPEG: 'Descargar JPEG',
            downloadPDF: 'Descargar PDF',
            downloadSVG: 'Descargar SVG',
            contextButtonTitle: 'Exportar grafica'
        },
        credits: {
            enabled: false,
            text: 'argoalmacenadora.com',
            href: 'http://www.argoalmacenadora.com.mx'
        },
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'COBERTURAS SOBRE TASA DE INTERES'
        },
        tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">Total: </td>' +
            '<td style="padding:0"><b>${point.y:,.0f} </b></td></tr>',
        footerFormat: '</table>',
        useHTML: true
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true
                },
                showInLegend: false
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [{
                name: 'MONTO DEL CREDITO',
                y: <?=$excel_coberturas->getActiveSheet()->getCell('E11')->getCalculatedValue(); ?>,
                sliced: true,
                selected: true
            }, {
                name: 'PRIMA PAGADA',
                y: <?=$excel_coberturas->getActiveSheet()->getCell('G11')->getCalculatedValue(); ?>
            }, {
                name: 'MONTO LIQUIDAR',
                y:<?=$excel_coberturas->getActiveSheet()->getCell('E30')->getCalculatedValue(); ?>
            }]
        }]
    });
});
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
