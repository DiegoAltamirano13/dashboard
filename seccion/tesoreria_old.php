<?php
ini_set('display_errors', false);

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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '11');
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
<!-- *********** INICIA INCLUDE CSS *********** -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
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
            $archivo_excel = $obj_reader_excel->load("../uploads_files/".$nombre_load_excel."");
            $archivo_excel_afisa = $obj_reader_excel->load("../uploads_files/".$nombre_load_excel."");
            $archivo_excel_info_inv = $obj_reader_excel->load("../uploads_files/".$nombre_load_excel."");
                

            //COMPRUEBA SI EXISTE LA HOJA Tesoreria
            try {
             //Cuenta cuantas filas tiene una hoja en especifico
             $t_filas_tesoreria = $archivo_excel->setActiveSheetIndexByName("Tesoreria")->getHighestRow();

              //COMPRUEBA SI EXISTE LA HOJA AFISA
              try {
               //Cuenta cuantas filas tiene una hoja en especifico
               $t_filas_tesoreria_afisa = $archivo_excel_afisa->setActiveSheetIndexByName("AFISA")->getHighestRow();
              }catch (Exception $e) {
                $error_hoja_afisa = '<div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-info"></i> Aviso!</h4>
                No existe la hoja <code>AFISA</code> en el Archivo <code>'.$nombre_load_excel.'</code> <cite>(LOS DETALLES DE PLANES DE AUTOFINACIAMIENTO AFISA NO ESTÁN DISPONIBLES)</cite>!!!
                </div>';
                echo  $error_hoja_afisa;
              }

              //COMPRUEBA SI EXISTE LA HOJA Info. Inversiones
              try {
               //Cuenta cuantas filas tiene una hoja en especifico
               $t_filas_tesoreria_info_inv = $archivo_excel_info_inv->setActiveSheetIndexByName("Info. Inversiones")->getHighestRow();
              }catch (Exception $e) {
                $error_hoja_info_inv = '<div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-info"></i> Aviso!</h4>
                No existe la hoja <code>Info. Inversiones</code> en el Archivo <code>'.$nombre_load_excel.'</code>!!!
                </div>';
                echo  $error_hoja_info_inv;
              }

            }
            // SI NO EXISTE LA HOJA Tesoreria ERROR
            catch (Exception $e) {
                echo '<div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-info"></i> Aviso!</h4>
                No existe la hoja <code>Tesoreria</code> en el Archivo <code>'.$nombre_load_excel.'</code>!!!
                </div>';
            }

          }else{//si no existe el archivo
            echo '<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-warning"></i> Aviso!</h4>
                NO HAY NINGÚN ARCHIVO EN EL SERVIDOR CON EL NOMBRE <code>'.$nombre_load_excel.'</code>!!!
                </div>';
            $archivo_excel = $obj_reader_excel->load("../uploads_files/excel.xlsx");
            //Cuenta cuantas filas tiene una hoja en especifico
            /////////////////////////////
            $archivo_excel_afisa = $obj_reader_excel->load("../uploads_files/excel.xlsx");
            //Cuenta cuantas filas tiene una hoja en especifico
             /////////////////////////////
            $archivo_excel_info_inv = $obj_reader_excel->load("../uploads_files/excel.xlsx");
            //Cuenta cuantas filas tiene una hoja en especifico
          } 
        }
        //si no existe ningun registro en la base lee un documento por defecto
        else{
          echo '<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-warning"></i> Aviso!</h4>
                NO HAY UN REGISTRO EN LA BASE O NO HAY UNA FECHA SELECCIONADA EN EL HISTORIAL
                </div>';
          $archivo_excel = $obj_reader_excel->load("../uploads_files/excel.xlsx");
          //Cuenta cuantas filas tiene una hoja en especifico 
          /////////////////////////////
          $archivo_excel_afisa = $obj_reader_excel->load("../uploads_files/excel.xlsx"); 
           /////////////////////////////
          $archivo_excel_info_inv = $obj_reader_excel->load("../uploads_files/excel.xlsx"); 
        }
      ?>
    </section> 
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->


 
<!-- ######################################## Inicio de Widgets ######################################### -->
    <section><!-- Inicia la seccion de los Widgets -->
      <div class="row">

      <!-- Widgets Disponibilidad con Reserva --> 
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-aqua">
          <span class="info-box-icon"><i class="ion ion-cash"></i></span> 
          <div class="info-box-content">
            <span class="info-box-text" title="DISPONIBILIDAD CON RESERVA">DISPONIBILIDAD CON RESERVA</span>
            <?php  
            for ($i=20;$i<=20;$i++){  
            ?> 
            <span class="info-box-number"><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),2)?></span>
            <?php } ?>  
          </div>
          <!-- <button type="submit" name="tipo" id="tipo" value="1" class="btn bg-aqua  btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
        </div> 
      </div>
      <!-- Widgets Disponibilidad Sin Reserva -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green">
          <span class="info-box-icon"><i class="fa fa-money"></i></span> 
          <div class="info-box-content">
            <span class="info-box-text" title="DISPONIBILIDAD SIN RESERVA">DISPONIBILIDAD SIN RESERVA</span>
            <?php  
            for ($i=19;$i<=19;$i++){  
            ?> 
            <span class="info-box-number"><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),2)?></span>
            <?php } ?> 
          </div>
          <!-- <button type="submit" name="tipo" id="tipo" value="1" class="btn bg-green  btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
        </div> 
      </div>
      <!-- Widgets -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-yellow">
          <span class="info-box-icon"><i class="fa fa-calculator"></i></span> 
          <div class="info-box-content">
            <span class="info-box-text" title="PAGOS PENDIENTES">PAGOS PENDIENTES</span>
            <?php  
            for ($i=37;$i<$t_filas_tesoreria-11;$i++){  
            ?> 
              <span class="info-box-text"><b>
                <?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(),2)?>
              </b></span>
            <?php } ?> 
            <span class="info-box-text" title="INGRESOS PENDIENTES">INGRESOS PENDIENTES</span>
            <?php  
            for ($i=46;$i<$t_filas_tesoreria-2;$i++){  
            ?> 
              <span class="info-box-text"><b>
                <?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(),2)?>
              </b></span>
            <?php } ?> 
          </div>
          <!-- <button type="submit" name="tipo" id="tipo" value="1" class="btn bg-yellow  btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
        </div> 
      </div>
      <!-- Widgets Disponibilidad Final -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-dollar"></i></span> 
          <div class="info-box-content">
            <span class="info-box-text" title="DISPONIBILIDAD FINAL">DISPONIBILIDAD FINAL</span>
            <?php  
            for ($i=47;$i<=47;$i++){ 
            ?> 
            <span class="info-box-number"><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(),2)?></span>
            <?php } ?>
          </div>
          <!-- <button type="submit" name="tipo" id="tipo" value="1" class="btn bg-red  btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
        </div> 
      </div> 
      <!-- Termino Disponibilidad Final -->  
      </div>
      <!-- /.row --> 
      </section><!-- Termina la seccion de los Widgets -->
<!-- ######################################### Termino de Widgets ######################################### --> 


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

<!-- INICIA MODAL PARA DETALLES AFISA -->
<style>
#modal_det_afisa .modal-dialog { 
    width: 90%; 
  }
</style>
<div class="modal fade" id="modal_det_afisa" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">PLANES DE AUTOFINACIAMIENTO (AFISA)</h4>
      </div>
      <div class="modal-body">

        <?= $error_hoja_afisa;?>

        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
        <thead>
          <tr>
            <th class="bg-blue text-center" colspan="13">SALDO EN AUTOFINANCIAMIENTO</th>
          </tr>
        <?php for($i=6; $i<$t_filas_tesoreria_afisa-13; $i++){ ?> 
          <tr>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('C'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('E'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('F'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('G'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('H'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('I'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('J'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('K'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('L'.$i)->getCalculatedValue(); ?></th>
            <th class="small text-muted"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('M'.$i)->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('N'.$i)->getCalculatedValue(); ?></th>
          </tr>
        <?php } ?>
        </thead>
        <tbody>
        <?php for($i=7; $i<$t_filas_tesoreria_afisa; $i++){ ?>
          <tr>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('C'.$i)->getCalculatedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('E'.$i)->getFormattedValue();?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('F'.$i)->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('G'.$i)->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('H'.$i)->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('I'.$i)->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('J'.$i)->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('K'.$i)->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('L'.$i)->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('M'.$i)->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('N'.$i)->getFormattedValue(); ?></td>
          </tr>
        <?php } ?>
        </tbody>
        </table>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- TERMINA MODAL PARA DETALLES AFISA -->

<!-- INICIA MODAL PARA INFO INVERSIONES MEDIANO PLAZO -->
<style>
#modal_info_inv_med_plazo .modal-dialog { 
    width: 80%; 
  }
</style>
<div class="modal fade" id="modal_info_inv_med_plazo" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">INFORMACIÓN INVERSIONES</h4>
      </div>
      <div class="modal-body">

        <?= $error_hoja_info_inv; ?>
        <div class="table-responsive">
        
        <table class="table table-bordered table-hover table-striped">
          <thead>
            <tr>
              <th colspan="11" class="bg-blue text-center">MERCADO DE DINERO</th>
            </tr>
          <?php for($i=26; $i<$t_filas_tesoreria_info_inv-15; $i++){ ?>
            <tr>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(); ?></th>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('C'.$i)->getCalculatedValue(); ?></th>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(); ?></th>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('E'.$i)->getCalculatedValue(); ?></th>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('F'.$i)->getCalculatedValue(); ?></th>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('G'.$i)->getCalculatedValue(); ?></th>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('H'.$i)->getCalculatedValue(); ?></th>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('I'.$i)->getCalculatedValue(); ?></th>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('J'.$i)->getCalculatedValue(); ?></th>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('K'.$i)->getCalculatedValue(); ?></th>
              <th><?= $archivo_excel_info_inv->getActiveSheet()->getCell('L'.$i)->getCalculatedValue(); ?></th>
            </tr>
          <?php } ?>
          </thead>
          <tbody>
          <?php for($i=27; $i<$t_filas_tesoreria_info_inv; $i++){ ?>
            <tr>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('B'.$i)->getFormattedValue(); ?></td>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('C'.$i)->getFormattedValue(); ?></td>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(); ?></td>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('E'.$i)->getCalculatedValue(); ?></td>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('F'.$i)->getCalculatedValue(); ?></td>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('G'.$i)->getFormattedValue(); ?></td>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('H'.$i)->getFormattedValue(); ?></td>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('I'.$i)->getFormattedValue(); ?></td>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('J'.$i)->getFormattedValue(); ?></td>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('K'.$i)->getFormattedValue(); ?></td>
              <td><?= $archivo_excel_info_inv->getActiveSheet()->getCell('L'.$i)->getCalculatedValue(); ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- TERMINA MODAL PARA INFO INVERSIONES MEDIANO PLAZO -->

<!-- INICIA MODAL PARA INFO INVERSIONES -->
<style>
#modal_info_inv .modal-dialog { 
    width: 80%; 
  }
</style>
<div class="modal fade" id="modal_info_inv" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">INFORMACIÓN INVERSIONES</h4>
      </div>
      <div class="modal-body">

        <?= $error_hoja_info_inv; ?>
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
          <thead>
            <tr>
              <th colspan="5" class="bg-navy text-center">FONDOS DE INVERSION</th>
            </tr>
            <tr id="ver_scotiabank[]" style="display:none;">
              <th colspan="5" class="small bg-blue text-center">SCOTIABANK</th>
            </tr>
            <tr id="ver_bancovepormas[]" style="display:none;">
              <th colspan="5" class="small bg-blue text-center">BANCO VE POR MAS</th>
            </tr>
            <tr id="ver_bbvabancomer[]" style="display:none;">
              <th colspan="5" class="small bg-blue text-center">BANCOMER</th>
            </tr>
          <?php for($i=7; $i<$t_filas_tesoreria_info_inv-34; $i++){ ?>
            <tr>
              <th class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(); ?></th>
              <th class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('E'.$i)->getCalculatedValue(); ?></th>
              <th class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('F'.$i)->getCalculatedValue(); ?></th>
              <th class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('G'.$i)->getCalculatedValue(); ?></th>
              <th class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('H'.$i)->getCalculatedValue(); ?></th>
            </tr>
          <?php } ?>
          </thead>
          <tbody>
          <?php for($i=8; $i<$t_filas_tesoreria_info_inv-25; $i++){ ?>
            <tr id="ver_scotiabank[]" style="display:none;"><!-- INICIA INFO SCOTIABANK -->
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('E'.$i)->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('F'.$i)->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('G'.$i)->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('H'.$i)->getFormattedValue(); ?></td>
            </tr><!-- TERMINA INFO SCOTIABANK -->
          <?php } ?>
            <tr id="ver_bancovepormas[]" style="display:none;"><!-- INICIA INFO BANCO VE POR MAS -->
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('D18')->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('E18')->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('F18')->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('G18')->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('H18')->getFormattedValue(); ?></td>
            </tr><!-- TERMINA INFO BANCO VE POR MAS -->
            <tr id="ver_bbvabancomer[]" style="display:none;"><!-- INICIA BBVA BANCOMER  -->
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('D20')->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('E20')->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('F20')->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('G20')->getCalculatedValue(); ?></td>
              <td class="small"><?= $archivo_excel_info_inv->getActiveSheet()->getCell('H20')->getFormattedValue(); ?></td>
            </tr><!-- TERMINA BBVA BANCOMER  -->
          </tbody>
        </table>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- TERMINA MODAL PARA INFO INVERSIONES -->

 <!-- ############################ TERMINA SECCION DE MOADALS ############################# --> 

 

<!-- ############################ INICIA SECCION TABLA DE INVERSIONES ############################# --> 
<section>
  <div class="box box-default">    
    <div class="box-header with-border">
      <h3 class="box-title"></h3> 
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> 
      </div>
    </div>
    <div class="box-body"><!--box-body-->   
      <div class="row"><!-- row -->
        <h4 class="content-header text-blue text-center"><i class="ion ion-cash"></i> INVERSIONES Y DISPONIBILIDADES <code><?=$fecha_load_excel?></code></h4><hr>
        
        <div class="col-md-6"><!-- col-md-6 -->

          <div class="table-responsive"><!-- table-responsive -->
          <table class="table compact table-striped table-bordered table-hover">
            <thead>
              <tr class="bg-blue">
                <th colspan="2" class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('B5')->getCalculatedValue()?></th>
                <th colspan="2" class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('D5')->getCalculatedValue()?></th> 
              </tr>
            </thead>
            <tbody>
            <?php  
            for ($i=6;$i<$t_filas_tesoreria-38;$i++){
            ?>
              <tr>
              <?php

              $encuentra_bancovepormas = strpos($archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(), "VE POR MAS");
              $encuentra_bbvabancomer = strpos($archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(), "BANCOMER");
              $encuentra_scotiabank = strpos($archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(), "SCOTIABANK");

              if ( $encuentra_bancovepormas !== false || $encuentra_bbvabancomer !== false || $encuentra_scotiabank !== false ){
                if ($encuentra_bancovepormas !== false) {
                echo '<td class="small">'.$archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue().' <div id="click_bancovepormas[]" class="label label-info pull-right btn" data-toggle="modal" data-target="#modal_info_inv"><i class="ion-search"></i> INFO. INV.</div></td>';
                }
                if ($encuentra_bbvabancomer !== false) {
                   echo '<td class="small">'.$archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue().' <div id="click_bbvabancomer[]" class="label label-info pull-right btn" data-toggle="modal" data-target="#modal_info_inv"><i class="ion-search"></i> INFO. INV.</div></td>';
                }
                if ($encuentra_scotiabank !== false) {
                  echo '<td class="small">'.$archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue().' <div id="click_scotiabank[]" class="label label-info pull-right btn" data-toggle="modal" data-target="#modal_info_inv"><i class="ion-search"></i> INFO. INV.</div></td>';
                }
              }else {
                echo '<td class="small">'.$archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue().'</td>';
              }
              
              ?>
                <!-- <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue()?></td> -->
                <td class="small"><?= $archivo_excel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue(); ?></td>
                <th class="small"><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),2)?></th> 
              </tr>
            <?php } ?>
              <tr>
                <td class="small"><?= $archivo_excel->getActiveSheet()->getCell('B11')->getCalculatedValue(); ?></td>
                <td class="small"><?= $archivo_excel->getActiveSheet()->getCell('C11')->getCalculatedValue(); ?> <div class="label label-success pull-right btn" data-toggle="modal" data-target="#modal_info_inv_med_plazo"><i class="ion-search"></i> MERCADO DE DINERO</div></td>
                <th class="small"><?= "$".number_format($archivo_excel->getActiveSheet()->getCell('D11')->getCalculatedValue(),2); ?> <div class="label label-info pull-right btn" id="click_det_inv_med_plazo"><i class="ion-search"></i> DESGLOSE</div></th>
              </tr>
            <?php for ($i=12;$i<$t_filas_tesoreria-30;$i++){ ?>
              <tr id="det_inv_med_plazo[]" style="display: none;">
                <td class="small"></td>
                <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()?></td>
                <th class="small text-aqua"><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),2)?></th>
              </tr>
            <?php } ?>
              <tr id="det_inv_med_plazo[]" style="display: none;">
                <th></th>
                <th>Total:</th>
                <th><code><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('D11')->getCalculatedValue(),2)?></code></th>
              </tr>
            </tbody>
          </table>  

          </div><!-- ./table-responsive --> 

          <!-- INICIA TABLA GRAN TOTAL -->
          <br><br><br>
          <div class="table-responsive ">
          <table class="table compact table-striped table-bordered table-hover"> 
            <?php  
            for ($i=24;$i<$t_filas_tesoreria-24;$i++){  
            ?>
              <tr>
                <td class="small">
                <?php echo $archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue()?>
                <a href="" data-toggle="modal" data-target="#modal_det_afisa">
                <div class="pull-right label label-primary pull-right btn"><i class="ion-search"></i> DETALLES AUTOFINANCIAMIENTO</div>
                </a>
                </td>
                <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()?></td>
                <th class="small">
                <?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),2)?>
                </th> 
              </tr>
            <?php } ?>
            <?php  
            for ($i=25;$i<$t_filas_tesoreria-23;$i++){  
            ?>
              <tr class="bg-green">
                <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue()?></td>
                <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()?></td>
                <th class="small"><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),2)?></th> 
              </tr>
            <?php } ?>
          </table>
          </div>
          <!-- INICIA TABLA GRAN TOTAL -->  
        </div><!-- ./col-md-6 -->

        <div class="col-md-6"><!-- col-md-6 --> 

          <!-- INICIA TABLA TOTAL DISPONIBLE -->
          <div class="table-responsive ">
          <table id="graf_contingencia" class="table compact table-striped table-bordered table-hover">
            <tr class="bg-blue"> 
              <th colspan="2">Inversiones</th>
              <th>Total</th>
            </tr> 
          <?php  
          for ($i=19;$i<$t_filas_tesoreria-28;$i++){  
          ?>
            <tr>
              <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue()?></td>
              <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()?></td>
              <th class="small text-blue"><?php echo $archivo_excel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue()?></th> 
            </tr>
          <?php } ?>
          </table>
          </div>
          <!-- INICIA TABLA TOTAL DISPONIBLE -->

          <div id="grafica1" style="min-width: 230px; height: 400px; margin: 0 auto"></div>

        </div><!-- ./col-md-6 -->

      </div><!-- ./row -->

        <!-- INICIA TABLA BANCOMER DOLARES -->
          <div class="table-responsive ">
          <table class="table compact table-striped table-bordered table-hover"> 
            <?php
            for ($i=27;$i<$t_filas_tesoreria-21;$i++){  
            ?>
              <tr class="bg-blue">
                <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('B'.$i)->getFormattedValue()?></td>
                <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()?></td>
                <th class="small"><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),2)?></th> 
              </tr>
            <?php } ?>
            <?php
            for ($i=28;$i<$t_filas_tesoreria-18;$i++){  
            ?>
              <tr>
                <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('B'.$i)->getFormattedValue()?></td>
                <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()?></td>
                <th class="small"><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),2)?></th> 
              </tr>
            <?php } ?>
          </table>
          </div>
        <!-- INICIA TABLA BANCOMER DOLARES --> 

      <div class="row"><!-- row -->
        <div class="col-md-6"><!-- col-md-6 -->
          <!-- INICIA TABLA PAGOS PENDIENTES POR APLICAR -->
          <div class="table-responsive ">

          <table class="table compact table-striped table-bordered table-hover">
            <tr class="bg-blue">
              <th colspan="3">PAGOS PENDIENTES POR APLICAR</th>
            </tr>
          <?php  
          for ($i=35;$i<$t_filas_tesoreria-10;$i++){  
          ?>
            <tr>
              <th class="small"><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(),2)?></th>
              <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()?></td>
              <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('D'.$i)->getFormattedValue()?></td> 
            </tr>
          <?php } ?> 
          </table>
          </div>
          <!-- INICIA TABLA PAGOS PENDIENTES POR APLICAR --> 
        </div><!-- ./col-md-6 -->

        <div class="col-md-6"><!-- col-md-6 -->
          <!-- INICIA TABLA INGRESOS PENDIENTES POR APLICAR -->
          <div class="table-responsive ">

          <table class="table compact table-striped table-bordered table-hover">
            <tr class="bg-blue">
              <th colspan="3">INGRESOS PENDIENTES POR APLICAR</th>
            </tr>
          <?php  
          for ($i=44;$i<$t_filas_tesoreria-1;$i++){  
          ?>
            <tr>
              <th class="small"><?php echo "$".number_format($archivo_excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(),2)?></th>
              <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()?></td>
              <td class="small"><?php echo $archivo_excel->getActiveSheet()->getCell('D'.$i)->getFormattedValue()?></td> 
            </tr>
          <?php } ?> 
          </table>
          </div>
          <!-- INICIA TABLA INGRESOS PENDIENTES POR APLICAR --> 
        </div><!-- ./col-md-6 -->

      </div><!-- ./row -->

      <div class="row"><!-- row -->
        <div class="col-md-12">
        <div id="grafica2" style="min-width: 230px; height: 400px; margin: 0 auto"></div>
        <table border="1" id="graf_pag_pen_ing" style="display:none;">  
          <tr>
            <th>CONCEPTO</th>
            <th>TOTAL</th>
          </tr> 
          <tr>
            <td>PAGOS PENDIENTES</td>
            <td><?php echo $archivo_excel->getActiveSheet()->getCell('B37')->getCalculatedValue()?></td>   
          </tr>
          <tr>
            <td>INGRESOS PENDIENTES</td>
            <td><?php echo $archivo_excel->getActiveSheet()->getCell('B46')->getCalculatedValue()?></td>  
          </tr> 
        </table>
        </div>
      </div><!-- ./row -->

    </div><!--/.box-body--> 
  </div> 
</section> 
<!-- ########################### TERMINA SECCION TABLA DE INVERSIONES ########################### -->


 

      

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
<script type="text/javascript">
  $( "#click_det_inv_med_plazo" ).click(function() {
  $( "#det_inv_med_plazo\\[\\]" ).toggle( "slow", function() {
    // Animation complete.
  });
});

  $("#click_bancovepormas\\[\\]").click(function(){
    $("#ver_bancovepormas\\[\\]").show();
    $("#ver_scotiabank\\[\\]").hide();
    $("#ver_bbvabancomer\\[\\]").hide();
  });

  $("#click_bbvabancomer\\[\\]").click(function(){
    $("#ver_bbvabancomer\\[\\]").show();
    $("#ver_scotiabank\\[\\]").hide();
    $("#ver_bancovepormas\\[\\]").hide();
  });

  $("#click_scotiabank\\[\\]").click(function(){
    $("#ver_scotiabank\\[\\]").show();
    $("#ver_bancovepormas\\[\\]").hide();
    $("#ver_bbvabancomer\\[\\]").hide();
  });

</script> 

<!-- Grafica Highcharts --> 
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<script>
Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
  });

Highcharts.chart('grafica1', {
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
    data: {
        table: 'graf_contingencia', 
        //cellRow: 1
        //startRow: 2
        startColumn:1, 
        endColumn:2,  
    },
    chart: {
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: 'pie'
    },
    title: {
        text: 'INVERSIONES Y DISPONIBILIDADES'
    },
    yAxis: { 
        title: {
            text: 'Monto'
        },
        labels: {
        formatter: function() { 
          return '$' + (this.value).toFixed(2);
        }
      }
    },
    plotOptions: {

      pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
              enabled: false
          },
          showInLegend: true
      },
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">Total: </td>' +
            '<td style="padding:0"><b>${point.y:,.2f} </b></td></tr>',  
        footerFormat: '</table>', 
        useHTML: true
    },
});
</script>

<script>
Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
  });

Highcharts.chart('grafica2', {
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
    // data: {
    //     table: 'graf_pag_pen_ing',  
    // },
    chart: {
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: 'bar'
    },
    title: {
        text: 'PAGOS E INGRESOS PENDIENTES'
    },
    xAxis: {
        categories: [
            'PAGOS E INGRESOS PENDIENTES',
        ],
        crosshair: true
    },
    yAxis: { 
        title: {
            text: 'Monto'
        },
        labels: {
        formatter: function() { 
          return '$' + (this.value).toFixed(2);
        }
      }
    },
    plotOptions: {

      pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
              enabled: false
          },
          showInLegend: true
      },
    },
    // tooltip: {
    //     formatter: function () {
    //         return '<b>Total</b><br/>' +//'<br>'+this.point.percentage+'<br>'  //+ this.series.name + 
    //             '$'+this.point.y + ' ' + this.point.name.toLowerCase();
    //     }
    // },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>${point.y:,.2f} </b></td></tr>',  
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    series: [{
        name: 'PAGOS PENDIENTES',
        data: [<?php echo $archivo_excel->getActiveSheet()->getCell('B37')->getCalculatedValue()?>]
    }, {
        name: 'INGRESOS PENDIENTES',
        data: [<?php echo $archivo_excel->getActiveSheet()->getCell('B46')->getCalculatedValue()?>]
    }]

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
<script> 
$(document).ready(function() {
    $('#tabla_inversiones').DataTable({
      stateSave: true, 
      "ordering": true,
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
      "language": {
          "url": "../plugins/datatables/Spanish.json"
        },  
    });
} );
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