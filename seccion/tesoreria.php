<?php
ini_set('display_errors', false);
#phpinfo();
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], "11");
if($modulos_valida == 0)
{
  header('Location: index.php');
}
//-------------SECCION PARA INCLUIR CLASES E INSTANCIARLAS--------------//
include_once ('../class/Tesoreria.php');
$obj_tesoreria = new Tesoreria();
///////////////INSTANCIAS PHPExcel
require_once('../plugins/PHPExcel.php');
require_once('../plugins/PHPExcel/Reader/Excel2007.php');
$obj_reader_excel = new PHPExcel_Reader_Excel2007();
//////////////INSTANCIA INFORMACION FINANCIERA
include_once('../class/Informacion_financiera.php');
$obj_info_financiera = new Informacion_financiera();
///////////////////////////////////////////
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>

<!-- OPERACIONES RESERVA DE CONTINGENCIA E INVERSIONES -->
<?php
$cta_1902_8_4 = $obj_tesoreria->contingencia_inversiones(1902,8,4);
$v_cta_1902_8_4 = 0;
for ($i=0; $i < count($cta_1902_8_4); $i++) {
  //echo count($cta_1902_8_4);
  $v_cta_1902_8_4 = $v_cta_1902_8_4 + $cta_1902_8_4[$i]["SALDO"];
}

$cta_1104_6_17 = $obj_tesoreria->contingencia_inversiones(1104,6,17);
for ($i=0; $i < count($cta_1104_6_17); $i++) {
  $v_cta_1104_6_17 += $cta_1104_6_17[$i]["SALDO"];
}

$cta_1104_6_20 = $obj_tesoreria->contingencia_inversiones(1104,6,20);
for ($i=0; $i < count($cta_1104_6_20); $i++) {
  $v_cta_1104_6_20 = $cta_1104_6_20[$i]["SALDO"];
}

 $cta_1104_6_21 = $obj_tesoreria->contingencia_inversiones(1104,6,21);
for ($i=0; $i < count($cta_1104_6_21); $i++) {
  $v_cta_1104_6_21 = $cta_1104_6_21[$i]["SALDO"];
}

$cta_1104_6_19 = $obj_tesoreria->contingencia_inversiones(1104,6,19);
for ($i=0; $i < count($cta_1104_6_19); $i++) {
  $v_cta_1104_6_19 = $cta_1104_6_19[$i]["SALDO"];
}

//SUMA INVERSIONES
$t_inversiones = ($v_cta_1104_6_17+$v_cta_1104_6_20+$v_cta_1104_6_21+$v_cta_1104_6_19);

//SUMATORIA BANCOS
$bancos = $obj_tesoreria->bancos();
for ($i=0; $i < count($bancos) ; $i++) {
  $t_bancos[$i] = $bancos[$i]["SALDO"];
}
$sum_bancos = array_sum($t_bancos);

?>

<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
   <section class="content-header">
     <h1>

       Dashboard
       <small>Información Financiera</small>
     </h1>
   </section>
 <?php
//PRIMERO LEEMOS EL ULTIMO REGISTRO DE LA BASE
  $leer_excel_det = $obj_tesoreria->leer_excel_det();
  for ($i=0; $i<count($leer_excel_det); $i++){
    $nombre_load_excel = $leer_excel_det[$i]["NOMBRE_EXCEL"];
  }

  //Si hay un registro en la base lee el documento

  if ($nombre_load_excel == true){

    //Comprobamos si existe el archivo
    if (file_exists("uploads/".$nombre_load_excel."") )
    {
      // Especificamos el Excel a leer
      $archivo_excel_afisa = $obj_reader_excel->load("uploads/".$nombre_load_excel."", "uploads/".$nombre_load_excel."");

      $archivo_excel_info_inv = $obj_reader_excel->load("uploads/".$nombre_load_excel."", "uploads/".$nombre_load_excel."");
        //COMPRUEBA SI EXISTE LA HOJA AFISA
        try {
         //Cuenta cuantas filas tiene una hoja en especifico

         $t_filas_tesoreria_afisa = $archivo_excel_afisa->setActiveSheetIndexByName("AFISA")->getHighestRow();
         //echo "NUMERO DE FILAS = ".$t_filas_tesoreria_afisa;

        }catch (Exception $e) {
          $error_hoja_afisa = '<div class="alert alert-warning alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-info"></i> Aviso!</h4>
          No existe la hoja <code>AFISA</code> en el Archivo <code>'.$nombre_load_excel.'</code> <br><cite>(NO SE PODRÁN MOSTRAR DETALLES DE PLANES DE AUTOFINANCIAMIENTO)</cite>!!!
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
          <br><cite>(NO SE PODRÁN MOSTRAR DETALLES DE INVERSIONES)</cite></div>';
          echo  $error_hoja_info_inv;
        }


    }else{//si no existe el archivo ----------------------------------------------------------------
      echo '<div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-warning"></i> Aviso!</h4>
          NO HAY NINGÚN ARCHIVO EN EL SERVIDOR CON EL NOMBRE <code>'.$nombre_load_excel.'</code>!!!
          <br><cite>(NO SE PODRÁN MOSTRAR DETALLES DE INVERSIONES Y PLANES DE AUTOFINANCIAMIENTO)</cite></div>';
      //Cuenta cuantas filas tiene una hoja en especifico
      /////////////////////////////
      //$archivo_excel_afisa = $obj_reader_excel->load("uploads_files/excel.xlsx");
      //Cuenta cuantas filas tiene una hoja en especifico
       /////////////////////////////
      //$archivo_excel_info_inv = $obj_reader_excel->load("uploads_files/excel.xlsx");
      //Cuenta cuantas filas tiene una hoja en especifico
    }
  }
  //si no existe ningun registro en la base lee un documento por defecto
  else{
    echo '<div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-warning"></i> Aviso!</h4>
          NO HAY NINGÚN REGISTRO EN LA BASE, NO SE PODRÁN MOSTRAR DETALLES DE INVERSIONES Y PLANES DE AUTOFINANCIAMIENTO
          </div>';
    //Cuenta cuantas filas tiene una hoja en especifico
    /////////////////////////////
    $archivo_excel_afisa = $obj_reader_excel->load("uploads/excel.xlsx", "uploads/excel.xlsx");
     /////////////////////////////
    $archivo_excel_info_inv = $obj_reader_excel->load("uploads/excel.xlsx", "uploads/excel.xlsx");
  }
?>
<!-- ############################# INICIA SECCION PARA LOS MODALS ############################# -->

<!-- INICIA MODAL PARA INFO INVERSIONES -->
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
          <tr>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('B6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('C6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('D6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('E6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('F6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('G6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('H6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('I6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('J6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('K6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('L6')->getCalculatedValue(); ?></th>
            <th class="small text-muted"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('M6')->getCalculatedValue(); ?></th>
            <th class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('N6')->getCalculatedValue(); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr style="display:none;" id="tr_det_ahorradores">
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('B7')->getCalculatedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('C7')->getCalculatedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('D7')->getCalculatedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('E7')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('F7')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('G7')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('H7')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('I7')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('J7')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('K7')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('L7')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('M7')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('N7')->getFormattedValue(); ?></td>
          </tr>
          <tr style="display:none;" id="tr_det_ajudicados">
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('B8')->getCalculatedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('C8')->getCalculatedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('D8')->getCalculatedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('E8')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('F8')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('G8')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('H8')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('I8')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('J8')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('K8')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('L8')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('M8')->getFormattedValue(); ?></td>
            <td class="small"><?php echo $archivo_excel_afisa->getActiveSheet()->getCell('N8')->getFormattedValue(); ?></td>
          </tr>
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

<!-- ############################# TERMINA SECCION PARA LOS MODALS ############################# -->
    <!-- Content Header (Page header) -->



    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

    <h4 class="content-header text-blue text-center"><i class="fa fa-calculator"></i> TESORERÍA</h4>

<!-- ######################################## Inicio de Widgets ######################################### -->
<section><!-- Inicia la seccion de los Widgets -->
 <div class="row">

  <div class="col-lg-5"><!-- col-lg-5 -->
    <div class="row"><!-- row -->
    <!-- INICIA WIDGETS RESERVA DE CONTINGENCIA -->
    <div class="col-lg-6">
      <div class="small-box bg-aqua">
        <div class="inner text-center">
          <b>RESERVA DE CONTINGENCIA</b>
          <h4 class="text-center">$<?= number_format($v_cta_1902_8_4,2) ?></h4>
        </div>
        <div class="icon">
          <i class="ion ion-pie-graph"></i>
        </div>
        <div class="inner"></div>
      </div>
    </div>
    <!-- TERMINA WIDGETS RESERVA DE CONTINGENCIA -->
    <!-- INICIA WIDGETS INVERSIONES -->
    <div class="col-lg-6">
      <div class="small-box bg-green">
        <div class="inner text-center">
          <b>INVERSIONES</b>
          <h4 class="text-center">$<?= number_format($t_inversiones,2) ?></h4>
        </div>
        <div class="icon">
          <i class="ion-arrow-graph-up-right"></i>
        </div>
        <div class="inner"></div>
        <!-- <button type="submit" name="tipo" id="tipo" value="2" class="btn bg-green btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
      </div>
    </div>
    <!-- TERMINA WIDGETS INVERSIONES -->
    </div><!-- ./row -->
  </div><!-- ./col-lg-5 -->
  <div class="col-lg-2"><!-- col-lg-2 -->
    <div class="row"><!-- row -->
    <!-- INICIA WIDGETS BANCOS -->
      <div class="col-lg-12">
        <div class="small-box bg-yellow">
          <div class="inner text-center">
            <b>BANCOS</b>
            <h4 class="text-center">$<?= number_format($sum_bancos,2) ?></h4>
          </div>
          <div class="icon">
            <i class="ion-card"></i>
          </div>
          <div class="inner"></div>
          <!-- <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-yellow btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button>  -->
        </div>
      </div>
    <!-- TERMINA WIDGETS BANCOS -->
    </div> <!-- ./row -->
  </div><!-- ./col-lg-2 -->
    <div class="col-lg-5"><!-- col-lg-5 -->
      <div class="row"><!-- row -->
      <!-- INICIA WIDGETS INGRESOS POR COBRANZA -->
        <div class="col-lg-6">
          <div class="small-box bg-red">
            <div class="inner text-center">
              <b>INGRESOS POR COBRANZA</b>
              <h4 class="text-center">
              <?php
              $widgets_ingresos_egresos = $obj_tesoreria->widgets_ingresos_egresos();
              for ($i=0; $i < count($widgets_ingresos_egresos); $i++) {
                echo "$".number_format($widgets_ingresos_egresos[$i]["INGRESO_COBRANZA"],2);
              }
              ?>
              </h4>
            </div>
            <div class="icon">
              <i class="ion-social-usd"></i>
            </div>
            <div class="inner"></div>
              <!-- <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-red btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
          </div>
        </div>
      <!-- TERMINA WIDGETS INGRESOS POR COBRANZA -->
      <!-- TERMINA WIDGETS EGRESOS POR GASTOS -->
        <div class="col-lg-6">
          <div class="small-box bg-primary">
            <div class="inner text-center">
              <b>EGRESOS POR GASTOS</b>
              <h4 class="text-center">
              <?php
              for ($i=0; $i < count($widgets_ingresos_egresos); $i++) {
                $ingresos_egresos = abs($widgets_ingresos_egresos[$i]["EGRESOS_GASTOS"]);
                echo "$".number_format($ingresos_egresos,2);
              }
              ?>
              </h4>
            </div>
            <div class="icon">
              <i class="ion-cash"></i>
            </div>
            <div class="inner"></div>
              <!-- <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-red btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
         </div>
        </div>
      <!-- TERMINA WIDGETS EGRESOS POR GASTOS -->
      </div><!-- ./row -->
    </div><!-- ./col-lg-5 -->

 </div>
</section><!-- Termina la seccion de los Widgets -->
<!-- ######################################### Termino de Widgets ######################################### -->




<div class="row"><!-- row padre -->
<!-- ############################ INICIA SECCION RESERVA DE CONTINGENCIA E INVERSIONES ############################# -->
<section class="col-md-6  connectedSortable">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">RESERVA DE CONTINGENCIA E INVERSIONES</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

    <!-- INICIA TABLA RESERVA DE CONTINGENCIA -->
      <div class="table-responsive">
      <table class="table table-bordered table-condensed table-hover">
        <thead class="bg-navy disabled ">
          <tr>
            <th class="text-center" colspan="3">RESERVA DE CONTINGENCIA</th>
          </tr>
          <tr>
            <th>INSTITUCIÓN FINANCIERA</th>
            <th>CONCEPTO</th>
            <th>SALDO</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>SCOTIABANK INVERLAT</td>
            <td>RESERVA DE CONTINGENCIA</td>
            <td> $<?= number_format($v_cta_1902_8_4,2); ?> </td>
          </tr>
        </tbody>
      </table>
      </div>
    <!-- TERMINA TABLA RESERVA DE CONTINGENCIA -->
    <br><br>
    <!-- INICIA TABLA INVERSIONES -->
    <div class="table-responsive">
      <table class="table table-bordered table-condensed table-striped table-hover">
        <thead class="bg-navy disabled ">
          <tr>
            <th class="text-center" colspan="3">INVERSIONES</th>
          </tr>
          <tr>
            <th>INSTITUCIÓN FINANCIERA</th>
            <th>CONCEPTO</th>
            <th>SALDO</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
            BBVA BANCOMER
            <div id="click_bbvabancomer[]" class="label label-info pull-right btn" data-toggle="modal" data-target="#modal_info_inv"><i class="ion-search"></i> INFO. INV.</div>
            </td>
            <td>EXCEDENTES DE TESORERIA</td>
            <td> $<?= number_format($v_cta_1104_6_17,2); ?></td>
          </tr>
          <tr>
            <td>
            BANCO VE POR MAS
            <div id="click_bancovepormas[]" class="label label-info pull-right btn" data-toggle="modal" data-target="#modal_info_inv"><i class="ion-search"></i> INFO. INV.</div>
            </td>
            <td>EXCEDENTES DE TESORERIA</td>
            <td> $<?= number_format($v_cta_1104_6_20,2); ?></td>
          </tr>
          <tr>
            <td>
            BANCO VE POR MAS
            <div id="click_bancovepormas[]" class="label label-info pull-right btn" data-toggle="modal" data-target="#modal_info_inv"><i class="ion-search"></i> INFO. INV.</div>
            </td>
            <td>EXCEDENTES DE TESORERIA</td>
            <td> $<?= number_format($v_cta_1104_6_21,2); ?></td>
          </tr>
          <tr>
            <td>
            SCOTIABANK INVERLAT
            <div id="click_scotiabank[]" class="label label-info pull-right btn" data-toggle="modal" data-target="#modal_info_inv"><i class="ion-search"></i> INFO. INV.</div>
            </td>
            <td>
            EXCEDENTES DE TESORERIA
            <div class="label label-success pull-right btn" data-toggle="modal" data-target="#modal_info_inv_med_plazo"><i class="ion-search"></i> MERCADO DE DINERO</div>
            </td>
            <td> $<?= number_format($v_cta_1104_6_19,2); ?></td>
          </tr>
          <tr>
            <td colspan="2" class="bg-navy disabled text-center">TOTAL INVERSIONES</td>
            <td colspan="1" class="bg-navy disabled ">$<?= number_format($t_inversiones,2) ?></td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- TERMINA TABLA INVERSIONES -->

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION RESERVA DE CONTINGENCIA E INVERSIONES ########################### -->

<!-- ############################ INICIA SECCION GRAFICA RESERVA DE CONTINGENCIA E INVERSIONES ############################# -->
<section class="col-md-6  connectedSortable">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">GRAFICA</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

      <div id="grafica_reser_inver" style="min-width: 210px; height: 400px; max-width: 600px; margin: 0 auto"></div>

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION GRAFICA RESERVA DE CONTINGENCIA E INVERSIONES ########################### -->
</div><!-- ./row padre -->



<div class="row"><!-- row padre -->
<!-- ############################ INICIA SECCION PLANES DE AUTOFINANCIAMIENTO ############################# -->
<section class="col-md-6  connectedSortable">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">PLANES DE AUTOFINANCIAMIENTO</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->
    <br>
    <!-- INICIA TABLA PLANES DE AUTOFINANCIAMIENTO -->
    <div class="table-responsive">
      <table class="table table-bordered table-condensed table-striped table-hover">
        <thead class="bg-navy disabled ">
          <tr>
            <th class="text-center" colspan="3">PLANES DE AUTOFINANCIAMIENTO</th>
          </tr>
          <tr>
            <th>INSTITUCIÓN</th>
            <th>CONCEPTO</th>
            <th>SALDO</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td rowspan="2">AFISA</td>
            <td>
            CONTRATOS AHORRADORES
            <a href="" data-toggle="modal" data-target="#modal_det_afisa">
              <div class="click_ahorradores pull-right label label-primary pull-right btn"><i class="ion-search"></i> DETALLES</div>
            </a>
            </td>
            <td>
            <?php
            $autofinanciamiento = $obj_tesoreria->autofinanciamiento();
            for ($i=0; $i < count($autofinanciamiento) ; $i++) {
              $v_ahorradores = $autofinanciamiento[$i]["AHORRADORES"];
              $v_adjudicados = $autofinanciamiento[$i]["ADJUDICADOS"];
              echo "$".number_format($v_ahorradores,2) ;
            }
            ?>
            </td>
          </tr>
          <tr>
            <td>
            CONTRATOS ADJUDICADOS
            <a href="" data-toggle="modal" data-target="#modal_det_afisa">
              <div class="click_adjudicados pull-right label label-primary pull-right btn"><i class="ion-search"></i> DETALLES</div>
            </a>
            </td>
            <td>$<?= number_format($v_adjudicados,2) ?></td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- TERMINA TABLA PLANES DE AUTOFINANCIAMIENTO -->
    <br>

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION PLANES DE AUTOFINANCIAMIENTO ########################### -->

<!-- ############################ INICIA SECCION GRAFICA PLANES DE AUTOFINANCIAMIENTO ############################# -->
<section class="col-md-6  connectedSortable">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">GRAFICA</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

      <div id="grafica_autofinan" style="min-width: 210px; height: 350px; max-width: 600px; margin: 0 auto"></div>

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION GRAFICA PLANES DE AUTOFINANCIAMIENTO ########################### -->
</div><!-- ./row padre -->



<div class="row"><!-- row padre -->
<!-- ############################ INICIA SECCION BANCOS ############################# -->
<section class="col-md-6  connectedSortable">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">BANCOS</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

    <!-- INICIA TABLA BANCOS -->
    <div class="table-responsive">
      <table class="table table-bordered table-condensed table-striped table-hover">
        <thead class="bg-navy disabled ">
          <tr>
            <th class="text-center" colspan="4">BANCOS</th>
          </tr>
          <tr>
            <th>INSTITUCIÓN FINANCIERA</th>
            <th>CUENTA</th>
            <th>CONCEPTO</th>
            <th>SALDO</th>
          </tr>
        </thead>
        <tbody>
        <?php for ($i=0; $i < count($bancos) ; $i++) { ?>
          <tr>
            <td><?= $bancos[$i]["INSTITUCION_F"] ?></td>
            <td>
            <?php
            $cta_bancos = intval(preg_replace('/[^0-9]+/', '', $bancos[$i]["CTA"]), 10);
            echo $cta_bancos;
            ?>
            </td>
            <td><?= $bancos[$i]["CONCEPTO"] ?></td>
            <td>$<?= number_format($bancos[$i]["SALDO"],2) ?></td>
        <?php } ?>
          <tr>
            <td colspan="3" class="bg-navy disabled text-center">TOTAL BANCOS</td>
            <td colspan="1" class="bg-navy disabled ">$<?=number_format($sum_bancos,2)?></td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- TERMINA TABLA BANCOS -->

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION BANCOS ########################### -->

<!-- ############################ INICIA SECCION GRAFICA BANCOS ############################# -->
<section class="col-md-6  connectedSortable">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">GRAFICA</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

      <div id="grafica_bancos" style="min-width: 210px; height: 350px; max-width: 600px; margin: 0 auto"></div>
      <br>

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION GRAFICA BANCOS ########################### -->
</div><!-- ./row padre -->




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
$(".click_adjudicados").click(function(){
  $("#tr_det_ajudicados").show();
  $("#tr_det_ahorradores").hide();

});

$(".click_ahorradores").click(function(){
  $("#tr_det_ahorradores").show();
  $("#tr_det_ajudicados").hide();
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
<!-- Acomoda secciones -->
<script src="../dist/js/move_section.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
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

// GRAFICA RESERVA DE CONTINGENCIA E INVERSIONES
Highcharts.chart('grafica_reser_inver', {
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
      text: 'Reserva de Contingencia e Inversiones'
  },
  tooltip: {
  headerFormat: '<span style="font-size:10px">{point.key}</span> <b>{point.percentage:.1f}%</b><table>',
  pointFormat: '<tr><td style="color:{series.color};padding:0">Total: </td>' +
      '<td style="padding:0"><b>${point.y:,.2f} </b></td></tr>',
  footerFormat: '</table>',
  useHTML: true
  },
  plotOptions: {
      pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
              enabled: false
          },
          showInLegend: true
      }
  },
  series: [{
      name: 'Total',
      colorByPoint: true,
      data: [{
          name: 'Reserva de Contingencia',
          y: <?= $v_cta_1902_8_4 ?>
      }, {
          name: 'Inversiones',
          y: <?= $t_inversiones ?>,
          sliced: true,
          selected: true
      }
      ]
  }]
});
// ******************************

// GRAFICA PLANES DE AUTOFINANCIAMIENTO
Highcharts.chart('grafica_autofinan', {
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
      text: 'Planes de Autofinanciamiento'
  },
  tooltip: {
  headerFormat: '<span style="font-size:10px">{point.key}</span> <b>{point.percentage:.1f}%</b><table>',
  pointFormat: '<tr><td style="color:{series.color};padding:0">Total: </td>' +
      '<td style="padding:0"><b>${point.y:,.2f} </b></td></tr>',
  footerFormat: '</table>',
  useHTML: true
  },
  plotOptions: {
      pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
              enabled: false
          },
          showInLegend: true
      }
  },
  series: [{
      colors: ['#00B8BF', '#EDFF9F'],
      name: 'Total',
      colorByPoint: true,
      data: [{
          name: 'Contratos Ahorradores',
          y: <?= $v_ahorradores ?>
      }, {
          name: 'Contratos Adjudicados',
          y: <?= $v_adjudicados ?>,
          //sliced: true,
          //selected: true
      }
      ]
  }]
});
// ******************************


// GRAFICA BANCOS
Highcharts.chart('grafica_bancos', {
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
      text: 'Bancos'
  },
  tooltip: {
  headerFormat: '<span style="font-size:10px">{point.key}</span> <b>{point.percentage:.1f}%</b><table>',
  pointFormat: '<tr><td style="color:{series.color};padding:0">Total: </td>' +
      '<td style="padding:0"><b>${point.y:,.2f} </b></td></tr>',
  footerFormat: '</table>',
  useHTML: true
  },
  plotOptions: {
      pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
              enabled: false
          },
          showInLegend: true
      }
  },
  legend: {
    enabled: true,
            align: 'left',
            floating: true,
            x: 50,
            y: 10,
            verticalAlign: 'bottom',
            padding: 0,
            margin:5,
            itemMarginTop: 0,
            itemMarginBottom: 0,
            itemStyle:{
                fontSize: '10px'
                }
  },//ECBDC0
  series: [{
      colors: ['#32DADD','#98d9ff','#c758d0','#C2CDF4','#e11e84','#ff7300','#ffaf00','#ECBDC0','#ffec01'],
      name: 'Total',
      colorByPoint: true,
      data: [
      <?php for ($i=0; $i < count($bancos) ; $i++) { ?>
      {
          name: '<?=$bancos[$i]["INSTITUCION_F"]."<small> (".$bancos[$i]["CONCEPTO"].")</small>"?>',
          y: <?=$bancos[$i]["SALDO"]?>,
      },
      <?php } ?>
      ]
  }]
});
// ******************************


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
