<?php
ini_set('display_errors', false);

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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '14');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/*--------------DECLARACIONES DE SESSIONES PARA REMATES--------------*/
/*SESSION PARA STATUS DE REMATES*/
if(isset($_POST["remate_status"]))
  $_SESSION["remate_status"] = $_POST["remate_status"];
$remate_status = $_SESSION["remate_status"];
/*SESSION PARA PLAZAS EN REMATES*/
if(isset($_POST["remates_plaza"]))
  $_SESSION["remates_plaza"] = $_POST["remates_plaza"];
$remates_plaza = $_SESSION["remates_plaza"];
/*SESSION PARA ALMACEN EN REMATES*/
if(isset($_POST["remates_almacen"]))
  $_SESSION["remates_almacen"] = $_POST["remates_almacen"];
$remates_almacen = $_SESSION["remates_almacen"];
//remates imagenes
include '../class/descargaImagenes.php';
$descargaImagen = new descargaImagenes();

$carga_Imagen = $descargaImagen->descargaImagen();
$carga_ImagenDañados = $descargaImagen->descargaImagenDestruccion();

////////INSTACIAS DE CLASES
include_once '../class/Remate.php';
$obj_remates = new Remate($remates_plaza,$remates_almacen);
$widgets_remates = $obj_remates->widgets();
/*VARIABLE PARA TITULO DE STATUS DE REMATES*/
switch ($remate_status) {
  case '0':
    $titulo_status_rem = "REGISTRADOS";
    break;
  case '1,2,3,4,5,6,7,8,9':
    $titulo_status_rem = "EN PROCESO";
    break;
  case '10':
    $titulo_status_rem = "ADJUDICADOS";
    break;
}
/*VARIABLE PARA TITULO PLAZA O ALMACEN*/
 if ($remates_plaza == true){$titulo_pla_alm = "PLAZA";}
 if ($remates_almacen== true){$titulo_pla_alm = "ALMACEN";}
///////////////////////////////////////////
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- *********** INICIA INCLUDE CSS *********** -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/Select/select.dataTables.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/Responsive/css/responsive.dataTables.min.css">
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Dashboard <small>Administración</small></h1>
    </section>
    <!-- Main content -->

    <!-- <h4 class="content-header text-blue text-center"><i class="ion-ios-pricetags"></i> Remates</h4> -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

    <?php if ($remate_status == true || $remate_status == '0') { ?>
    <form action="remates.php" method="post">
      <input type="hidden" name="remate_status" value="">
      <input type="hidden" name="remates_plaza" value="">
      <input type="hidden" name="remates_almacen" value="">
      <button type="submit" class="btn bg-blue btn-xs"><i class="fa fa-reply"></i> Regresar</button>
    </form>
    <?php } ?>
<!-- ######################################## Inicio de Widgets ######################################### -->
<?php if ($remate_status == null){ ?>
    <section><!-- Inicia la seccion de los Widgets -->
      <div class="row">
      <!-- Widgets Remates en Proceso -->
      <div class="col-md-4">
        <!-- small box -->
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php for ($i=0; $i <count($widgets_remates) ; $i++) {echo $widgets_remates[$i]["REGISTRO"];}?></h3>
            <p>REMATES POR NOTIFICAR</p>
          </div>
          <div class="icon">
            <i class="fa fa-check-square-o"></i>
          </div>
          <form action="remates.php" method="post">
          <input type="hidden" name="remates_plaza" value="">
          <input type="hidden" name="remates_almacen" value="">
          <button type="submit" name="remate_status" value="0" class="btn bg-yellow-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
          </form>
        </div>
      </div>
        <div class="col-md-4">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php for ($i=0; $i <count($widgets_remates) ; $i++) {echo $widgets_remates[$i]["PROCESO"];}?></h3>
              <p>REMATES EN PROCESO</p>
            </div>
            <div class="icon">
              <i class="fa fa-refresh"></i>
            </div>
            <form action="remates.php" method="post">
             <input type="hidden" name="remates_plaza" value="">
             <input type="hidden" name="remates_almacen" value="">
             <button type="submit" name="remate_status" value="1,2,3,4,5,6,7,8,9" class="btn bg-aqua-active  btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
        <!-- Widgets Remates Adjudicados -->
        <div class="col-md-4">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php for ($i=0; $i <count($widgets_remates) ; $i++) {echo $widgets_remates[$i]["ADJUDICADA"];}?></h3>
              <p>REMATES ADJUDICADOS</p>
            </div>
            <div class="icon">
              <i class="fa fa-legal"></i>
            </div>
            <form action="remates.php" method="post">
             <input type="hidden" name="remates_plaza" value="">
             <input type="hidden" name="remates_almacen" value="">
             <button type="submit" name="remate_status" value="10" class="btn bg-green-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>

        </div>
        <!-- Termino Widgets Remates Registrados -->
        <!-- Inicia Widgets Remates VENTA -->
        <div class="col-md-4">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php for ($i=0; $i <count($widgets_remates) ; $i++) {echo $widgets_remates[$i]["VENTA"];}?></h3>
              <p>REMATES VENDIDOS</p>
            </div>
            <div class="icon">
              <i class="fa fa-check-square-o"></i>
            </div>
            <form action="remates.php" method="post">
            <input type="hidden" name="remates_plaza" value="">
            <input type="hidden" name="remates_almacen" value="">
            <button type="submit" name="remate_status" value="11" class="btn bg-yellow-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
        <!-- Inicia Widgets Remates VENTA -->
        <!-- Inicia Widgets Remates VENTA -->
        <div class="col-md-4">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php for ($i=0; $i <count($widgets_remates) ; $i++) {echo $widgets_remates[$i]["DESTRUCCION"];}?></h3>
              <p>REMATES DESTRUIDOS</p>
            </div>
            <div class="icon">
              <i class="fa fa-times-circle"></i>
            </div>
            <form action="remates.php" method="post">
            <input type="hidden" name="remates_plaza" value="">
            <input type="hidden" name="remates_almacen" value="">
            <button type="submit" name="remate_status" value="12" class="btn bg-red-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
        <!-- Inicia Widgets Remates VENTA -->
      </div>
      <!-- /.row -->
      </section><!-- Termina la seccion de los Widgets -->
<?php } ?>
<!-- ######################################### Termino de Widgets ######################################### -->



<div class="row"><!-- row -->

  <!-- *************************** INICIA REMATES REGISTRADOS *************************** -->
  <?php if ($remate_status == null || $remate_status == '0'){ ?>
    <div class="col-md-4 connectedSortable"><!-- col -->
      <div class="box box-warning">
        <div class="box-header with-border"><!-- box-header -->
          <i class="fa fa-pie-chart"></i>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
          </div>
        </div><!-- /.box-header -->
        <div class="box-body"><!-- box-body -->

        <?php if ($remate_status == true || $remate_status == '0'){ ?>
          <div class="info-box bg-yellow">
            <span class="info-box-number text-center">
            <div class="icon">
                <i class="fa fa-check-square-o"></i>
              </div>
              <div class="inner">
              <p class="small">REMATES POR NOTIFICAR</p>
              <h3><?php for ($i=0; $i <count($widgets_remates) ; $i++) {echo $widgets_remates[$i]["REGISTRO"];}?></h3>
              <p class="small"><?= $titulo_pla_alm." ".$remates_plaza. " ".$remates_almacen ?></p>
              </div>
            </span>
          </div>
        <?php } ?>

        <?php if ($remate_status == null || $remate_status == '0' && $remates_almacen == false){ ?>
          <div  id="grafica_registro" style="height: 320px;"></div>
        <?php } ?>

        <?php if ( $remates_almacen == true || $remates_plaza == true) {?>
        <form method="post">
        <input type="hidden" name="remates_plaza" value="">
        <button type="post" name="remates_almacen" value="" class="btn bg-blue btn-xs"><i class="fa fa-reply"></i> Regresar</button>
        </form>
        <?php } ?>

        </div><!-- /.box-body -->
        <div class="box-footer"><!-- box-footer -->
          <!-- Widget: user widget style 1 -->
            <div class="box box-widget widget-user-2">
              <div class="small-box bg-yellow">
                <div class="inner">
                  <p>REMATES POR NOTIFICAR EN ALMACEN</p>
                </div>
                <div style="font-size:50px;" class="icon">
                  <i class="fa fa-check-square-o"></i>
                </div>
              </div>
              <div class="box-footer no-padding"><br>
                <table id="tabla_rem_almacen[]" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
                  <thead>
                  <tr>
                    <th class="small">ALMACEN</th>
                    <th class="small">PLAZA</th>
                    <th class="small">REGISTRADOS</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $grafica_alm_registro = $obj_remates->grafica_remates("ALM","0");
                  for ($i=0; $i <count($grafica_alm_registro) ; $i++) { ?>
                  <tr>
                    <td class="small"><form action="remates.php" method="post">
                    <input type="hidden" name="remate_status" value="0">
                    <input type="hidden" name="remates_plaza" value="">
                    <button type="submit" class="btn btn-link btn-xs" name="remates_almacen" value="<?=$grafica_alm_registro[$i]["PLA_ALM"]?>"><?=$grafica_alm_registro[$i]["PLA_ALM"];?></button>

                    </form></td>
                    <td class="small"><?=$grafica_alm_registro[$i]["PLAZA_SIG"];?></td>
                    <td>
                    <div style='background-color:<?=$grafica_alm_registro[$i]["COLOR"]?>;' class="badge"><?=$grafica_alm_registro[$i]["TOTAL"]?></div>
                    </td>
                  </tr>
                  <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          <!-- /.widget-user -->
        </div><!-- /.box-footer -->
      </div>
    </div><!-- /.col -->
  <?php } ?>
  <!-- *************************** TERMINA REMATES REGISTRADOS *************************** -->

<!-- *************************** INICIA REMATES EN PROCESO *************************** -->
<?php if ($remate_status == null || $remate_status == '1,2,3,4,5,6,7,8,9' ){ ?>
  <div class="col-md-4 connectedSortable"><!-- col -->
    <div class="box box-info">
      <div class="box-header with-border"><!-- box-header -->
        <i class="fa fa-pie-chart"></i>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
          </button>
        </div>
      </div><!-- /.box-header -->
      <div class="box-body"><!-- box-body -->

      <?php if ($remate_status == true || $remate_status == '0'){ ?>
        <div class="info-box bg-aqua">
          <span class="info-box-number text-center">
          <div class="icon">
              <i class="fa fa-refresh"></i>
            </div>
            <div class="inner">
            <p class="small">REMATES EN PROCESO</p>
            <h3><?php for ($i=0; $i <count($widgets_remates) ; $i++) {echo $widgets_remates[$i]["PROCESO"];}?></h3>
            <p class="small"><?= $titulo_pla_alm." ".$remates_plaza. " ".$remates_almacen ?></p>
            </div>
          </span>
        </div>
      <?php } ?>


      <?php  if ($remate_status == null || $remate_status == '1,2,3,4,5,6,7,8,9' && $remates_almacen == false){ ?>
        <div  id="grafica_proceso" style="height: 320px;"></div>
      <?php } ?>

      <?php if ( $remates_almacen == true || $remates_plaza == true) {?>
      <form method="post">
      <input type="hidden" name="remates_plaza" value="">
      <button type="post" name="remates_almacen" value="" class="btn bg-blue btn-xs"><i class="fa fa-reply"></i> Regresar</button>
      </form>
      <?php } ?>


      </div><!-- /.box-body -->
      <div class="box-footer"><!-- box-footer -->
        <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <div class="small-box bg-aqua">
              <div class="inner">
                <p>REMATES EN PROCESO POR ALMACEN</p>
              </div>
              <div style="font-size:50px;" class="icon">
                <i class="fa fa-refresh"></i>
              </div>
            </div>
            <div class="box-footer no-padding"><br>

              <table id="tabla_rem_almacen[]" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
                <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">ENPROCESO</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $grafica_alm_proceso = $obj_remates->grafica_remates("ALM","1,2,3,4,5,6,7,8,9");
                for ($i=0; $i <count($grafica_alm_proceso) ; $i++) { ?>
                <tr>
                  <td class="small">
                  <form action="remates.php" method="post">
                  <input type="hidden" name="remate_status" value="1,2,3,4,5,6,7,8,9">
                  <input type="hidden" name="remates_plaza" value="">
                  <button type="submit" class="btn btn-link btn-xs" name="remates_almacen" value="<?=$grafica_alm_proceso[$i]["PLA_ALM"]?>"><?=$grafica_alm_proceso[$i]["PLA_ALM"];?></button>

                  </form>
                  </td>
                  <td class="small"><?=$grafica_alm_proceso[$i]["PLAZA_SIG"];?></td>
                  <td>
                  <div style='background-color:<?=$grafica_alm_proceso[$i]["COLOR"]?>;' class="badge"><?=$grafica_alm_proceso[$i]["TOTAL"]?></div>
                  </td>
                </tr>
                <?php } ?>
                </tbody>
              </table>

            </div>
          </div>
          <!-- /.widget-user -->
      </div><!-- /.box-footer -->
    </div>
  </div><!-- /.col -->
<?php } ?>
<!-- *************************** TERMINA REMATES EN PROCESO *************************** -->

<!-- *************************** INICIA REMATES ADJUDICADOS *************************** -->
<?php if ($remate_status == null || $remate_status == '10'){ ?>
  <div class="col-md-4 connectedSortable"><!-- col -->
    <div class="box box-success">
      <div class="box-header with-border"><!-- box-header -->
        <i class="fa fa-pie-chart"></i>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
          </button>
        </div>
      </div><!-- /.box-header -->
      <div class="box-body"><!-- box-body -->

      <?php if ($remate_status == true || $remate_status == '0'){ ?>
        <div class="info-box bg-green">
          <span class="info-box-number text-center">
          <div class="icon">
              <i class="fa fa-legal"></i>
            </div>
            <div class="inner">
            <p class="small">REMATES ADJUDICADOS</p>
            <h3><?php for ($i=0; $i <count($widgets_remates) ; $i++) {echo $widgets_remates[$i]["ADJUDICADA"];}?></h3>
            <p class="small"><?= $titulo_pla_alm." ".$remates_plaza. " ".$remates_almacen ?></p>
            </div>
          </span>
        </div>
      <?php } ?>

      <?php if ( $remates_almacen == true || $remates_plaza == true) {?>
      <form method="post">
      <input type="hidden" name="remates_plaza" value="">
      <button type="post" name="remates_almacen" value="" class="btn bg-blue btn-xs"><i class="fa fa-reply"></i> Regresar</button>
      </form>
      <?php } ?>

      <?php if ($remate_status == null || $remate_status == '10' && $remates_almacen == false){ ?>
        <div  id="grafica_adjudicada" style="height: 320px;"></div>
      <?php } ?>

      </div><!-- /.box-body -->
      <div class="box-footer"><!-- box-footer -->
        <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <div class="small-box bg-green">
              <div class="inner">
                <p>REMATES ADJUDICADOS POR ALMACEN</p>
              </div>
              <div style="font-size:50px;" class="icon">
                <i class="fa fa-legal"></i>
              </div>
            </div>
            <div class="box-footer no-padding"><br>
              <table id="tabla_rem_almacen[]" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
                <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">ADJUDICADOS</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $grafica_alm_adjudicada = $obj_remates->grafica_remates("ALM","10");
                for ($i=0; $i <count($grafica_alm_adjudicada) ; $i++) { ?>
                <tr>
                  <td class="small">
                  <form action="remates.php" method="post">
                  <input type="hidden" name="remate_status" value="10">
                  <input type="hidden" name="remates_plaza" value="">
                  <button type="submit" class="btn btn-link btn-xs" name="remates_almacen" value="<?=$grafica_alm_adjudicada[$i]["PLA_ALM"]?>"><?=$grafica_alm_adjudicada[$i]["PLA_ALM"];?></button>

                  </form>
                  </td>
                  <td class="small"><?=$grafica_alm_adjudicada[$i]["PLAZA_SIG"];?></td>
                  <td>
                  <div style='background-color:<?=$grafica_alm_adjudicada[$i]["COLOR"]?>;' class="badge"><?=$grafica_alm_adjudicada[$i]["TOTAL"]?></div>
                  </td>
                </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        <!-- /.widget-user -->
      </div><!-- /.box-footer -->
    </div>
  </div><!-- /.col -->
<?php } ?>
<!-- *************************** TERMINA REMATES ADJUDICADOS *************************** -->

<!--INICIA REMATES EN VENTA PAPU -->
<?php if ($remate_status == null || $remate_status == '11'){ ?>
  <div class="col-md-4 connectedSortable"><!-- col -->
    <div class="box box-danger">
      <div class="box-header with-border"><!-- box-header -->
        <i class="fa fa-pie-chart"></i>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
          </button>
        </div>
      </div><!-- /.box-header -->
      <div class="box-body"><!-- box-body -->

      <?php if ($remate_status == true || $remate_status == '11'){ ?>
        <div class="info-box bg-primary">
          <span class="info-box-number text-center">
          <div class="icon">
              <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="inner">
            <p class="small">REMATES VENDIDOS</p>
            <h3><?php for ($i=0; $i <count($widgets_remates) ; $i++) {echo $widgets_remates[$i]["VENTA"];}?></h3>
            <p class="small"><?= $titulo_pla_alm." ".$remates_plaza. " ".$remates_almacen ?></p>
            </div>
          </span>
        </div>
      <?php } ?>

      <?php if ($remate_status == null || $remate_status == '11' && $remates_almacen == false){ ?>
        <div  id="grafica_venta" style="height: 320px;"></div>
      <?php } ?>

      <?php if ( $remates_almacen == true || $remates_plaza == true) {?>
      <form method="post">
      <input type="hidden" name="remates_plaza" value="">
      <button type="post" name="remates_almacen" value="" class="btn bg-blue btn-xs"><i class="fa fa-reply"></i> Regresar</button>
      </form>
      <?php } ?>

      </div><!-- /.box-body -->
      <div class="box-footer"><!-- box-footer -->
        <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <div class="small-box bg-primary">
              <div class="inner">
                <p>REMATES VENDIDOS POR ALMACEN</p>
              </div>
              <div style="font-size:50px;" class="icon">
                <i class="fa fa-shopping-cart"></i>
              </div>
            </div>
            <div class="box-footer no-padding"><br>
              <table id="tabla_rem_almacen[]" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
                <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">REGISTRADOS</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $grafica_alm_registro = $obj_remates->grafica_remates("ALM","11");
                for ($i=0; $i <count($grafica_alm_registro) ; $i++) { ?>
                <tr>
                  <td class="small"><form action="remates.php" method="post">
                  <input type="hidden" name="remate_status" value="0">
                  <input type="hidden" name="remates_plaza" value="">
                  <button type="submit" class="btn btn-link btn-xs" name="remates_almacen" value="<?=$grafica_alm_registro[$i]["PLA_ALM"]?>"><?=$grafica_alm_registro[$i]["PLA_ALM"];?></button>

                  </form></td>
                  <td class="small"><?=$grafica_alm_registro[$i]["PLAZA_SIG"];?></td>
                  <td>
                  <div style='background-color:<?=$grafica_alm_registro[$i]["COLOR"]?>;' class="badge"><?=$grafica_alm_registro[$i]["TOTAL"]?></div>
                  </td>
                </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        <!-- /.widget-user -->
      </div><!-- /.box-footer -->
    </div>
  </div><!-- /.col -->
<?php } ?>
<!--Termina remantes en venta -->

<!-- *************************** INICIA REMATES DESTRUCCION *************************** -->
<?php if ($remate_status == null || $remate_status == '12'){ ?>
  <div class="col-md-4 connectedSortable"><!-- col -->
    <div class="box box-danger">
      <div class="box-header with-border"><!-- box-header -->
        <i class="fa fa-pie-chart"></i>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
          </button>
        </div>
      </div><!-- /.box-header -->
      <div class="box-body"><!-- box-body -->

      <?php if ($remate_status == true || $remate_status == '12'){ ?>
        <div class="info-box bg-red">
          <span class="info-box-number text-center">
          <div class="icon">
              <i class="fa fa-check-square-o"></i>
            </div>
            <div class="inner">
            <p class="small">REMATES DESTRUIDOS</p>
            <h3><?php for ($i=0; $i <count($widgets_remates) ; $i++) {echo $widgets_remates[$i]["DESTRUCCION"];}?></h3>
            <p class="small"><?= $titulo_pla_alm." ".$remates_plaza. " ".$remates_almacen ?></p>
            </div>
          </span>
        </div>
      <?php } ?>

      <?php if ($remate_status == null || $remate_status == '12' && $remates_almacen == false){ ?>
        <div  id="grafica_destruido" style="height: 320px;"></div>
      <?php } ?>

      <?php if ( $remates_almacen == true || $remates_plaza == true) {?>
      <form method="post">
      <input type="hidden" name="remates_plaza" value="">
      <button type="post" name="remates_almacen" value="" class="btn bg-blue btn-xs"><i class="fa fa-reply"></i> Regresar</button>
      </form>
      <?php } ?>

      </div><!-- /.box-body -->
      <div class="box-footer"><!-- box-footer -->
        <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <div class="small-box bg-red">
              <div class="inner">
                <p>REMATES DESTRUIDOS POR ALMACEN</p>
              </div>
              <div style="font-size:50px;" class="icon">
                <i class="fa fa-times-circle"></i>
              </div>
            </div>
            <div class="box-footer no-padding"><br>
              <table id="tabla_rem_almacen[]" class="table display compact table-striped table-hover" cellspacing="0" width="100%">
                <thead>
                <tr>
                  <th class="small">ALMACEN</th>
                  <th class="small">PLAZA</th>
                  <th class="small">REGISTRADOS</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $grafica_alm_registro = $obj_remates->grafica_remates("ALM","12");
                for ($i=0; $i <count($grafica_alm_registro) ; $i++) { ?>
                <tr>
                  <td class="small"><form action="remates.php" method="post">
                  <input type="hidden" name="remate_status" value="0">
                  <input type="hidden" name="remates_plaza" value="">
                  <button type="submit" class="btn btn-link btn-xs" name="remates_almacen" value="<?=$grafica_alm_registro[$i]["PLA_ALM"]?>"><?=$grafica_alm_registro[$i]["PLA_ALM"];?></button>

                  </form></td>
                  <td class="small"><?=$grafica_alm_registro[$i]["PLAZA_SIG"];?></td>
                  <td>
                  <div style='background-color:<?=$grafica_alm_registro[$i]["COLOR"]?>;' class="badge"><?=$grafica_alm_registro[$i]["TOTAL"]?></div>
                  </td>
                </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        <!-- /.widget-user -->
      </div><!-- /.box-footer -->
    </div>
  </div><!-- /.col -->
<?php } ?>
<!-- *************************** TERMINA REMATES DESTRUCCION *************************** -->



<!-- *************************** INICIA COL PARA TABLA DE INFORMACION DE REMATES *************************** -->
<?php if ($remate_status != null) { ?>
  <div class="col-md-8 connectedSortable"><!-- col -->
    <div class="box box-primary">
      <div class="box-header with-border"><!-- box-header -->
        <i class="fa fa-pie-chart"></i><h3 class="box-title">REMATES <?=$titulo_status_rem." ".$titulo_pla_alm." ".$remates_plaza." ".$remates_almacen?></h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
          </button>
        </div>
      </div><!-- /.box-header -->
      <div class="box-body"><!-- box-body -->

      <!-- INICIA TABLA PARA INFORMACION DE REMATES -->
      <div class="table-responsive">
      <table id="tabla_info_remates" class="table table-bordered table-striped table-hover">
        <thead>
        <tr>
          <th class="small">PLAZA</th>
          <th class="small">CLIENTE</th>
          <th class="small">ALMACEN</th>
          <th class="small">PROMOTOR</th>
          <th class="small">MERCANCIA</th>
          <th class="small">VALOR INICIAL</th>
          <th class="small">REGIMEN</th>
          <!-- INICIA TH PARA REMATES EN PROCESO -->
          <?php if ($remate_status == "1,2,3,4,5,6,7,8,9"){ ?>
          <th class="small">ALMONEDA ACTUAL</th>
          <th class="small">FECHA ALMONEDA ACTUAL</th>
          <th class="small">VALOR ALMONEDA ACTUAL</th>
          <th class="small">COSTO PUBLICACIÓN</th>
          <th class="small">COSTO NOTARIO</th>
          <th class="small">SALDO DEUDOR</th>

          <th class="small">DETALLES</th>
          <th class="small">GALERIA</th>
          <?php } ?>
          <!-- TERMINA TH PARA REMATES EN PROCESO -->
          <!-- INICIA TH PARA REMATES ADJUDICADOS -->
          <?php if ($remate_status == "10"){ ?>
          <th class="small">STATUS</th>
          <th class="small">FECHA ADJUDICADO</th>
          <th class="small">VALOR/ADJUDICADO</th>
          <th class="small">ULTIMA/ALMONEDA</th>
          <th class="small">COSTO PUBLICACIÓN</th>
          <th class="small">COSTO NOTARIO</th>
          <th class="small">SALDO DEUDOR</th>

          <th class="small">DETALLES</th>
          <th class="small">GALERIA</th>
          <?php } ?>

          <?php if ($remate_status == "11"){ ?>
          <th class="small">STATUS</th>
          <th class="small">FECHA ADJUDICADO</th>
          <th class="small">VALOR/ADJUDICADO</th>
          <th class="small">ULTIMA/ALMONEDA</th>
          <th class="small">COSTO PUBLICACIÓN</th>
          <th class="small">COSTO NOTARIO</th>
          <th class="small">SALDO DEUDOR</th>

          <th class="small">DETALLES</th>
          <th class="small">GALERIA</th>
          <?php } ?>

          <!-- TERMINA TH PARA REMATES ADJUDICADOS -->
          <!-- INICIA TH PARA REMATES REGISTRADOS -->
          <?php if ($remate_status == "0"){ ?>
          <th class="small">STATUS</th>
          <th class="small">SALDO DEUDOR</th>
          <th class="small">DETALLES</th>
          <th class="small">GALERIA</th>
          <?php } ?>
          <!-- REMATES EN DESTRUCCION-->
          <?php if ($remate_status == "12"){ ?>
          <th class="small">STATUS</th>
          <th class="small">SALDO DEUDOR</th>

          <th class="small">DETALLES</th>
          <th class="small">GALERIA</th>
          <th class="small">GALERIA DESTRUCCIÓN</th>
          <?php } ?>
          <!-- TERMINA TH PARA REMATES REGISTRADOS -->
        </tr>
        </thead>
        <tbody>
        <?php
        $info_remates = $obj_remates->info_remates($remate_status);
        for ($i=0; $i <count($info_remates); $i++) {
        ?>
         <tr>
          <td class="small"><?= $info_remates[$i]["PLAZA"]?></td>
          <td class="small"><?= $info_remates[$i]["CLIENTE"]?></td>
          <td class="small"><?= $info_remates[$i]["ALMACEN"]?></td>
          <td class="small"><?= $info_remates[$i]["PROMOTOR"]?></td>
          <td class="small"><?= $info_remates[$i]["TIPO_MERCANCIA"]?></td>
          <td class="small"><?= "$".number_format($info_remates[$i]["V_ALMONEDA1"],2)?></td>
          <td class="small"><?= $info_remates[$i]["REGIMEN"]?></td>
          <!-- INICIA TD PARA REMATES EN PROCESO -->
          <?php if ($remate_status == "1,2,3,4,5,6,7,8,9"){
            echo '<td class="small">'.$info_remates[$i]["STATUS"].'</td>';
          switch ($info_remates[$i]["ID_STATUS"]){
            case 1:
              echo '<td class="small">'.$info_remates[$i]["FEC_ALMONEDA1"].'</td>';
              if ($info_remates[$i]["V_ALMONEDA1"] == false){
                echo '<td class="small"><code>NO DEFINIDO</code></td>';
              }else{
                echo '<td class="small">$'.number_format($info_remates[$i]["V_ALMONEDA1"],2).'</td>';
              }
              break;
            case 2:
              echo '<td class="small">'.$info_remates[$i]["FEC_ALMONEDA2"].'</td>';
              if ($info_remates[$i]["V_ALMONEDA2"] == false){
                echo '<td class="small"><code>NO DEFINIDO</code></td>';
              }else{
                echo '<td class="small">$'.number_format($info_remates[$i]["V_ALMONEDA2"],2).'</td>';
              }
              break;
            case 3:
              echo '<td class="small">'.$info_remates[$i]["FEC_ALMONEDA3"].'</td>';
              if ($info_remates[$i]["V_ALMONEDA3"] == false){
                echo '<td class="small"><code>NO DEFINIDO</code></td>';
              }else{
                echo '<td class="small">$'.number_format($info_remates[$i]["V_ALMONEDA3"],2).'</td>';
              }
              break;
            case 4:
              echo '<td class="small">'.$info_remates[$i]["FEC_ALMONEDA4"].'</td>';
              if ($info_remates[$i]["V_ALMONEDA4"] == false){
                echo '<td class="small"><code>NO DEFINIDO</code></td>';
              }else{
                echo '<td class="small">$'.number_format($info_remates[$i]["V_ALMONEDA4"],2).'</td>';
              }
              break;
            case 5:
              echo '<td class="small">'.$info_remates[$i]["FEC_ALMONEDA5"].'</td>';
              if ($info_remates[$i]["V_ALMONEDA5"] == false){
                echo '<td class="small"><code>NO DEFINIDO</code></td>';
              }else{
                echo '<td class="small">$'.number_format($info_remates[$i]["V_ALMONEDA5"],2).'</td>';
              }
              break;
            case 6:
              echo '<td class="small">'.$info_remates[$i]["FEC_ALMONEDA6"].'</td>';
              if ($info_remates[$i]["V_ALMONEDA6"] == false){
                echo '<td class="small"><code>NO DEFINIDO</code></td>';
              }else{
                echo '<td class="small">$'.number_format($info_remates[$i]["V_ALMONEDA6"],2).'</td>';
              }
              break;
            case 7:
              echo '<td class="small">'.$info_remates[$i]["FEC_ALMONEDA7"].'</td>';
              if ($info_remates[$i]["V_ALMONEDA7"] == false){
                echo '<td class="small"><code>NO DEFINIDO</code></td>';
              }else{
                echo '<td class="small">$'.number_format($info_remates[$i]["V_ALMONEDA7"],2).'</td>';
              }
              break;
            case 8:
              echo '<td class="small">'.$info_remates[$i]["FEC_ALMONEDA8"].'</td>';
              if ($info_remates[$i]["V_ALMONEDA8"] == false){
                echo '<td class="small"><code>NO DEFINIDO</code></td>';
              }else{
                echo '<td class="small">$'.number_format($info_remates[$i]["V_ALMONEDA8"],2).'</td>';
              }
              break;
            case 9:
              echo '<td class="small">'.$info_remates[$i]["FEC_ALMONEDA9"].'</td>';
              if ($info_remates[$i]["V_ALMONEDA9"] == false){
                echo '<td class="small"><code>NO DEFINIDO</code></td>';
              }else{
                echo '<td class="small">$'.number_format($info_remates[$i]["V_ALMONEDA9"],2).'</td>';
              }
              break;
          }
            echo '<td class="small">'.'$'.number_format(($info_remates[$i]["PUBLICA1"]+$info_remates[$i]["PUBLICA2"]+$info_remates[$i]["PUBLICA3"]+$info_remates[$i]["PUBLICA4"]+$info_remates[$i]["PUBLICA5"]+$info_remates[$i]["PUBLICA6"]+$info_remates[$i]["PUBLICA7"]+$info_remates[$i]["PUBLICA8"]+$info_remates[$i]["PUBLICA9"]),2).'</td>';
            echo '<td class="small">'.'$'.number_format(($info_remates[$i]["NOTARIO1"]+$info_remates[$i]["NOTARIO2"]+$info_remates[$i]["NOTARIO3"]+$info_remates[$i]["NOTARIO4"]+$info_remates[$i]["NOTARIO5"]+$info_remates[$i]["NOTARIO6"]+$info_remates[$i]["NOTARIO7"]+$info_remates[$i]["NOTARIO8"]+$info_remates[$i]["NOTARIO9"]),2).'</td>';
            echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SALDO_DEUDOR"]),2).'</td>';

            echo '<td class="small"><a href="remates_det.php?remate_status=1,2,3,4,5,6,7,8,9&id_remate='.$info_remates[$i]["ID_REMATE"].'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> VER</a></td>';

            if ($info_remates[$i]["NUMERO_IMAGENES"] == 0 ) {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")' disabled><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }else {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }

            #echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
          } ?>
          <!-- TERMINA TD PARA REMATES EN PROCESO -->
          <!-- INICIA TD PARA REMATES ADJUDICADOS -->
          <?php if ($remate_status == "10" || $info_remates[$i]["ID_STATUS"] == 10){
            echo '<td class="small">'.$info_remates[$i]["STATUS"].'</td>';
            echo '<td class="small">'.$info_remates[$i]["FEC_ADJUDICADO"].'</td>';
            echo '<td class="small">'."$".number_format($info_remates[$i]["VAL_ADJUDICADO"],2).'</td>';
            if ($info_remates[$i]["FEC_ALMONEDA9"]==true){
            echo '<td class="small">9NA ALMONEDA</td>';
            }else{
              if ($info_remates[$i]["FEC_ALMONEDA8"]==true){
                echo '<td class="small">8VA ALMONEDA</td>';
              }else{
                if ($info_remates[$i]["FEC_ALMONEDA7"]==true){
                  echo '<td class="small">7MA ALMONEDA</td>';
                }else{
                  if ($info_remates[$i]["FEC_ALMONEDA6"]==true){
                    echo '<td class="small">6MA ALMONEDA</td>';
                  }else{
                    if ($info_remates[$i]["FEC_ALMONEDA5"]==true){
                    echo '<td class="small">5TA ALMONEDA</td>';
                    }else{
                      if ($info_remates[$i]["FEC_ALMONEDA4"]==true){
                      echo '<td class="small">4TA ALMONEDA</td>';
                      }else{
                        if ($info_remates[$i]["FEC_ALMONEDA3"]==true){
                          echo '<td class="small">3RA ALMONEDA</td>';
                        }else{
                          if ($info_remates[$i]["FEC_ALMONEDA2"]==true){
                          echo '<td class="small">2DA ALMONEDA</td>';
                          }else{
                            if ($info_remates[$i]["FEC_ALMONEDA1"]==true){
                              echo '<td class="small">1RA ALMONEDA</td>';
                            }else{
                              echo '<td class="small">INDEFINIDO</td>';
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            echo '<td class="small">'.'$'.number_format(($info_remates[$i]["PUBLICA1"]+$info_remates[$i]["PUBLICA2"]+$info_remates[$i]["PUBLICA3"]+$info_remates[$i]["PUBLICA4"]+$info_remates[$i]["PUBLICA5"]+$info_remates[$i]["PUBLICA6"]+$info_remates[$i]["PUBLICA7"]+$info_remates[$i]["PUBLICA8"]+$info_remates[$i]["PUBLICA9"]),2).'</td>';
            echo '<td class="small">'.'$'.number_format(($info_remates[$i]["NOTARIO1"]+$info_remates[$i]["NOTARIO2"]+$info_remates[$i]["NOTARIO3"]+$info_remates[$i]["NOTARIO4"]+$info_remates[$i]["NOTARIO5"]+$info_remates[$i]["NOTARIO6"]+$info_remates[$i]["NOTARIO7"]+$info_remates[$i]["NOTARIO8"]+$info_remates[$i]["NOTARIO9"]),2).'</td>';
            if (number_format($info_remates[$i]["SUELDO_REAL"]) == 0) {
              echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SALDO_DEUDOR"]),2).'</td>';
            }else {
              echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SUELDO_REAL"]),2).'</td>';
            }

            echo '<td class="small"><a href="remates_det.php?remate_status=10&id_remate='.$info_remates[$i]["ID_REMATE"].'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> VER</a></td>';

            if ($info_remates[$i]["NUMERO_IMAGENES"] == 0 ) {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")' disabled><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }else {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }
            #echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            //echo $info_remates[$i]["ID_STATUS"];
          }?>


          <?php if ($remate_status == "11" || $info_remates[$i]["ID_STATUS"] == 11){
            echo '<td class="small">'.$info_remates[$i]["STATUS"].'</td>';
            echo '<td class="small">'.$info_remates[$i]["FEC_ADJUDICADO"].'</td>';
            echo '<td class="small">'."$".number_format($info_remates[$i]["VAL_ADJUDICADO"],2).'</td>';
            if ($info_remates[$i]["FEC_ALMONEDA9"]==true){
            echo '<td class="small">9NA ALMONEDA</td>';
            }else{
              if ($info_remates[$i]["FEC_ALMONEDA8"]==true){
                echo '<td class="small">8VA ALMONEDA</td>';
              }else{
                if ($info_remates[$i]["FEC_ALMONEDA7"]==true){
                  echo '<td class="small">7MA ALMONEDA</td>';
                }else{
                  if ($info_remates[$i]["FEC_ALMONEDA6"]==true){
                    echo '<td class="small">6MA ALMONEDA</td>';
                  }else{
                    if ($info_remates[$i]["FEC_ALMONEDA5"]==true){
                    echo '<td class="small">5TA ALMONEDA</td>';
                    }else{
                      if ($info_remates[$i]["FEC_ALMONEDA4"]==true){
                      echo '<td class="small">4TA ALMONEDA</td>';
                      }else{
                        if ($info_remates[$i]["FEC_ALMONEDA3"]==true){
                          echo '<td class="small">3RA ALMONEDA</td>';
                        }else{
                          if ($info_remates[$i]["FEC_ALMONEDA2"]==true){
                          echo '<td class="small">2DA ALMONEDA</td>';
                          }else{
                            if ($info_remates[$i]["FEC_ALMONEDA1"]==true){
                              echo '<td class="small">1RA ALMONEDA</td>';
                            }else{
                              echo '<td class="small">INDEFINIDO</td>';
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            echo '<td class="small">'.'$'.number_format(($info_remates[$i]["PUBLICA1"]+$info_remates[$i]["PUBLICA2"]+$info_remates[$i]["PUBLICA3"]+$info_remates[$i]["PUBLICA4"]+$info_remates[$i]["PUBLICA5"]+$info_remates[$i]["PUBLICA6"]+$info_remates[$i]["PUBLICA7"]+$info_remates[$i]["PUBLICA8"]+$info_remates[$i]["PUBLICA9"]),2).'</td>';
            echo '<td class="small">'.'$'.number_format(($info_remates[$i]["NOTARIO1"]+$info_remates[$i]["NOTARIO2"]+$info_remates[$i]["NOTARIO3"]+$info_remates[$i]["NOTARIO4"]+$info_remates[$i]["NOTARIO5"]+$info_remates[$i]["NOTARIO6"]+$info_remates[$i]["NOTARIO7"]+$info_remates[$i]["NOTARIO8"]+$info_remates[$i]["NOTARIO9"]),2).'</td>';
            if (number_format($info_remates[$i]["SUELDO_REAL"]) == 0) {
              echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SALDO_DEUDOR"]),2).'</td>';
            }else {
              echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SUELDO_REAL"]),2).'</td>';
            }

            echo '<td class="small"><a href="remates_det.php?remate_status=11&id_remate='.$info_remates[$i]["ID_REMATE"].'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> VER</a></td>';
            #echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            if ($info_remates[$i]["NUMERO_IMAGENES"] == 0 ) {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")' disabled><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }else {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }
            //echo $info_remates[$i]["ID_STATUS"];
          }?>

          <!-- TERMINA TD PARA REMATES ADJUDICADOS -->
          <!-- INICIA TD PARA REMATES REGISTRADOS -->
          <?php if ($remate_status == "0" && $info_remates[$i]["ID_STATUS"] == 0){
            echo '<td class="small">'.$info_remates[$i]["STATUS"].'</td>';
            if (number_format($info_remates[$i]["SUELDO_REAL"]) == 0) {
              echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SALDO_DEUDOR"]),2).'</td>';
            }else {
              echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SUELDO_REAL"]),2).'</td>';
            }
            echo '<td class="small"><a href="remates_det.php?remate_status=0&id_remate='.$info_remates[$i]["ID_REMATE"].'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> VER</a></td>';

            if ($info_remates[$i]["NUMERO_IMAGENES"] == 0 ) {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")' disabled><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }else {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }

            #echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
          } ?>
          <!-- TERMINA TD PARA REMATES REGISTRADOS -->
          <?php if ($remate_status == "12" && $info_remates[$i]["ID_STATUS"] == 12){
            echo '<td class="small">'.$info_remates[$i]["STATUS"].'</td>';
            if (number_format($info_remates[$i]["SUELDO_REAL"]) == 0) {
              echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SALDO_DEUDOR"]),2).'</td>';
            }else {
              echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SUELDO_REAL"]),2).'</td>';
            }

            echo '<td class="small"><a href="remates_det.php?remate_status=12&id_remate='.$info_remates[$i]["ID_REMATE"].'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> VER</a></td>';

            if ($info_remates[$i]["NUMERO_IMAGENES"] == 0 ) {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")' disabled><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }else {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }

            if ($info_remates[$i]["NUMERO_IMAGENES_DESTRUIDAS"] == 0 ) {
                  echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagenDañada(".$info_remates[$i]['ID_REMATE'].")' disabled><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }else {
                echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagenDañada(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
            }


          } ?>
        </tr>
        <?php } ?>
        </tbody>
      </table>

    </div>

<?php
  #echo $titulo_status_rem;
  if ($titulo_status_rem == 'ADJUDICADOS' ) {
    //echo "PRUEBA";

      $valor_usuario = $_SESSION['usuario'];
      //if ($remate_status == "10" AND $valor_usuario == 'jmanuel' OR $valor_usuario == 'david' ){ ?>
    <div class="box-header with-border"><!-- box-header -->
      <i class="fa fa-pie-chart"></i><h3 class="box-title">REMATES <?=$titulo_status_rem." ".$titulo_pla_alm." ".$remates_plaza." ".$remates_almacen?> PARA DESTRUCCION</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
        </button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
        </button>
      </div>
    </div><!-- /.box-header -->



    <div class="table-responsive">
    <table id="tabla_info_remates2" class="table table-bordered table-striped table-hover">
      <thead style="background-color:red; color:white;">
      <tr>
        <th class="small">PLAZA</th>
        <th class="small">CLIENTE</th>
        <th class="small">ALMACEN</th>
        <th class="small">PROMOTOR</th>
        <th class="small">MERCANCIA</th>
        <th class="small">VALOR INICIAL</th>
        <th class="small">REGIMEN</th>
        <th class="small">STATUS</th>
        <th class="small">FECHA ADJUDICADO</th>
        <th class="small">VALOR/ADJUDICADO</th>
        <th class="small">ULTIMA/ALMONEDA</th>
        <th class="small">COSTO PUBLICACIÓN</th>
        <th class="small">COSTO NOTARIO</th>
        <th class="small">SALDO DEUDOR</th>
        <th class="small">SEGUIMIENTO MERCANCIA</th>
        <th class="small">DETALLES</th>
        <th class="small">GALERIA</th>
      </tr>
      </thead>
      <tbody>
      <?php
      $info_remates = $obj_remates->info_remates2($remate_status);
      for ($i=0; $i <count($info_remates); $i++) {
      ?>
       <tr>
        <td class="small"><?= $info_remates[$i]["PLAZA"]?></td>
        <td class="small"><?= $info_remates[$i]["CLIENTE"]?></td>
        <td class="small"><?= $info_remates[$i]["ALMACEN"]?></td>
        <td class="small"><?= $info_remates[$i]["PROMOTOR"]?></td>
        <td class="small"><?= $info_remates[$i]["TIPO_MERCANCIA"]?></td>
        <td class="small"><?= "$".number_format($info_remates[$i]["V_ALMONEDA1"],2)?></td>
        <td class="small"><?= $info_remates[$i]["REGIMEN"]?></td>
        <!-- INICIA TD PARA REMATES EN PROCESO -->
        <?php if ($remate_status == "10" || $info_remates[$i]["ID_STATUS"] == 10){
          echo '<td class="small">'.$info_remates[$i]["STATUS"].'</td>';
          echo '<td class="small">'.$info_remates[$i]["FEC_ADJUDICADO"].'</td>';
          echo '<td class="small">'."$".number_format($info_remates[$i]["VAL_ADJUDICADO"],2).'</td>';
          if ($info_remates[$i]["FEC_ALMONEDA9"]==true){
          echo '<td class="small">9NA ALMONEDA</td>';
          }else{
            if ($info_remates[$i]["FEC_ALMONEDA8"]==true){
              echo '<td class="small">8VA ALMONEDA</td>';
            }else{
              if ($info_remates[$i]["FEC_ALMONEDA7"]==true){
                echo '<td class="small">7MA ALMONEDA</td>';
              }else{
                if ($info_remates[$i]["FEC_ALMONEDA6"]==true){
                  echo '<td class="small">6MA ALMONEDA</td>';
                }else{
                  if ($info_remates[$i]["FEC_ALMONEDA5"]==true){
                  echo '<td class="small">5TA ALMONEDA</td>';
                  }else{
                    if ($info_remates[$i]["FEC_ALMONEDA4"]==true){
                    echo '<td class="small">4TA ALMONEDA</td>';
                    }else{
                      if ($info_remates[$i]["FEC_ALMONEDA3"]==true){
                        echo '<td class="small">3RA ALMONEDA</td>';
                      }else{
                        if ($info_remates[$i]["FEC_ALMONEDA2"]==true){
                        echo '<td class="small">2DA ALMONEDA</td>';
                        }else{
                          if ($info_remates[$i]["FEC_ALMONEDA1"]==true){
                            echo '<td class="small">1RA ALMONEDA</td>';
                          }else{
                            echo '<td class="small">INDEFINIDO</td>';
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
          echo '<td class="small">'.'$'.number_format(($info_remates[$i]["PUBLICA1"]+$info_remates[$i]["PUBLICA2"]+$info_remates[$i]["PUBLICA3"]+$info_remates[$i]["PUBLICA4"]+$info_remates[$i]["PUBLICA5"]+$info_remates[$i]["PUBLICA6"]+$info_remates[$i]["PUBLICA7"]+$info_remates[$i]["PUBLICA8"]+$info_remates[$i]["PUBLICA9"]),2).'</td>';
          echo '<td class="small">'.'$'.number_format(($info_remates[$i]["NOTARIO1"]+$info_remates[$i]["NOTARIO2"]+$info_remates[$i]["NOTARIO3"]+$info_remates[$i]["NOTARIO4"]+$info_remates[$i]["NOTARIO5"]+$info_remates[$i]["NOTARIO6"]+$info_remates[$i]["NOTARIO7"]+$info_remates[$i]["NOTARIO8"]+$info_remates[$i]["NOTARIO9"]),2).'</td>';
          if (number_format($info_remates[$i]["SUELDO_REAL"]) == 0) {
            echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SALDO_DEUDOR"]),2).'</td>';
          }else {
            echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SUELDO_REAL"]),2).'</td>';
          }
          echo '<td class="small">'.$info_remates[$i]["SEGUIMIENTO_MERCA"].'</td>';
          echo '<td class="small"><a href="remates_det.php?remate_status=10&id_remate='.$info_remates[$i]["ID_REMATE"].'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> VER</a></td>';
#          echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagenDañada(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";


          if ($info_remates[$i]["NUMERO_IMAGENES_DESTRUIDAS"] == 0 ) {
                echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagenDañada(".$info_remates[$i]['ID_REMATE'].")' disabled><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
          }else {
              echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagenDañada(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
          }

          //echo $info_remates[$i]["ID_STATUS"];
        }?>
      </tr>
      <?php } ?>
      </tbody>
    </table>

  </div>

<?php }//} }?>


<?php

if ($titulo_status_rem == 'ADJUDICADOS') {
  // code...
      $valor_usuario = $_SESSION['usuario'];
      //if ($remate_status == "10" AND $valor_usuario == 'jmanuel' OR $valor_usuario == 'david' ){ ?>
<div class="box-header with-border"><!-- box-header -->
  <i class="fa fa-pie-chart"></i><h3 class="box-title">REMATES <?=$titulo_status_rem." ".$titulo_pla_alm." ".$remates_plaza." ".$remates_almacen?> PARA VENTA</h3>
  <div class="box-tools pull-right">
    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
    </button>
    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
    </button>
  </div>
</div><!-- /.box-header -->

<div class="table-responsive">
<table id="tabla_info_remates3" class="table table-bordered table-striped table-hover">
  <thead style="background-color:blue; color:white;">
  <tr>
    <th class="small">PLAZA</th>
    <th class="small">CLIENTE</th>
    <th class="small">ALMACEN</th>
    <th class="small">PROMOTOR</th>
    <th class="small">MERCANCIA</th>
    <th class="small">VALOR INICIAL</th>
    <th class="small">REGIMEN</th>
    <th class="small">STATUS</th>
    <th class="small">FECHA ADJUDICADO</th>
    <th class="small">VALOR/ADJUDICADO</th>
    <th class="small">ULTIMA/ALMONEDA</th>
    <th class="small">COSTO PUBLICACIÓN</th>
    <th class="small">COSTO NOTARIO</th>
    <th class="small">SALDO DEUDOR</th>
    <th class="small">DETALLES</th>
    <th class="small">GALERIA</th>
  </tr>
  </thead>
  <tbody>
  <?php
  $info_remates = $obj_remates->info_remates3($remate_status);
  for ($i=0; $i <count($info_remates); $i++) {
  ?>
   <tr>
    <td class="small"><?= $info_remates[$i]["PLAZA"]?></td>
    <td class="small"><?= $info_remates[$i]["CLIENTE"]?></td>
    <td class="small"><?= $info_remates[$i]["ALMACEN"]?></td>
    <td class="small"><?= $info_remates[$i]["PROMOTOR"]?></td>
    <td class="small"><?= $info_remates[$i]["TIPO_MERCANCIA"]?></td>
    <td class="small"><?= "$".number_format($info_remates[$i]["V_ALMONEDA1"],2)?></td>
    <td class="small"><?= $info_remates[$i]["REGIMEN"]?></td>
    <!-- INICIA TD PARA REMATES EN PROCESO -->
    <?php if ($remate_status == "10" || $info_remates[$i]["ID_STATUS"] == 10){
      echo '<td class="small">'.$info_remates[$i]["STATUS"].'</td>';
      echo '<td class="small">'.$info_remates[$i]["FEC_ADJUDICADO"].'</td>';
      echo '<td class="small">'."$".number_format($info_remates[$i]["VAL_ADJUDICADO"],2).'</td>';
      if ($info_remates[$i]["FEC_ALMONEDA9"]==true){
      echo '<td class="small">9NA ALMONEDA</td>';
      }else{
        if ($info_remates[$i]["FEC_ALMONEDA8"]==true){
          echo '<td class="small">8VA ALMONEDA</td>';
        }else{
          if ($info_remates[$i]["FEC_ALMONEDA7"]==true){
            echo '<td class="small">7MA ALMONEDA</td>';
          }else{
            if ($info_remates[$i]["FEC_ALMONEDA6"]==true){
              echo '<td class="small">6MA ALMONEDA</td>';
            }else{
              if ($info_remates[$i]["FEC_ALMONEDA5"]==true){
              echo '<td class="small">5TA ALMONEDA</td>';
              }else{
                if ($info_remates[$i]["FEC_ALMONEDA4"]==true){
                echo '<td class="small">4TA ALMONEDA</td>';
                }else{
                  if ($info_remates[$i]["FEC_ALMONEDA3"]==true){
                    echo '<td class="small">3RA ALMONEDA</td>';
                  }else{
                    if ($info_remates[$i]["FEC_ALMONEDA2"]==true){
                    echo '<td class="small">2DA ALMONEDA</td>';
                    }else{
                      if ($info_remates[$i]["FEC_ALMONEDA1"]==true){
                        echo '<td class="small">1RA ALMONEDA</td>';
                      }else{
                        echo '<td class="small">INDEFINIDO</td>';
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
      echo '<td class="small">'.'$'.number_format(($info_remates[$i]["PUBLICA1"]+$info_remates[$i]["PUBLICA2"]+$info_remates[$i]["PUBLICA3"]+$info_remates[$i]["PUBLICA4"]+$info_remates[$i]["PUBLICA5"]+$info_remates[$i]["PUBLICA6"]+$info_remates[$i]["PUBLICA7"]+$info_remates[$i]["PUBLICA8"]+$info_remates[$i]["PUBLICA9"]),2).'</td>';
      echo '<td class="small">'.'$'.number_format(($info_remates[$i]["NOTARIO1"]+$info_remates[$i]["NOTARIO2"]+$info_remates[$i]["NOTARIO3"]+$info_remates[$i]["NOTARIO4"]+$info_remates[$i]["NOTARIO5"]+$info_remates[$i]["NOTARIO6"]+$info_remates[$i]["NOTARIO7"]+$info_remates[$i]["NOTARIO8"]+$info_remates[$i]["NOTARIO9"]),2).'</td>';
      if (number_format($info_remates[$i]["SUELDO_REAL"]) == 0) {
        echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SALDO_DEUDOR"]),2).'</td>';
      }else {
        echo '<td class="small">'.'$'.number_format(($info_remates[$i]["SUELDO_REAL"]),2).'</td>';
      }
      echo '<td class="small"><a href="remates_det.php?remate_status=10&id_remate='.$info_remates[$i]["ID_REMATE"].'" class="fancybox fancybox.iframe btn btn-xs btn-primary"><i class="ion-search"></i> VER</a></td>';
      #echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";

      if ($info_remates[$i]["NUMERO_IMAGENES"] == 0 ) {
            echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")' disabled><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
      }else {
            echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
      }

      //echo "<td class='small'><button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos' onclick='cargarImagen(".$info_remates[$i]['ID_REMATE'].")'><img src='../dist/img/modulos/galeria.png' width='50px' height='50px'></img></button></td>";
      //echo $info_remates[$i]["ID_STATUS"];
    }?>
  </tr>
  <?php } ?>
  </tbody>
</table>

</div>
<?php } //} ?>
      <!-- TERMINA TABLA PARA INFORMACION DE REMATES -->

      </div><!-- /.box-body -->
      <div class="box-footer"><!-- box-footer -->

      </div><!-- /.box-footer -->
    </div>
  </div><!-- /.col -->
  <?php } ?>
<!-- *************************** TERMINA COL PARA TABLA DE INFORMACION DE REMATES *************************** -->


</div><!-- /.row -->







    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
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

<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
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
<!-- DataTables -->
<!-- <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script> -->
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
<script>
$(document).ready(function() {
    $('#tabla_rem_almacen\\[\\]').DataTable( {
        "scrollY":        "200px",
        "searching": false,
        stateSave: true,
        "scrollY": 200,
        "scrollX": true,
        "scrollCollapse": true,
        "paging": false,
        "info": false,
        "language": {
          "url": "../plugins/datatables/Spanish.json"
        },
    } );

    $('#tabla_info_remates').DataTable( {
      stateSave: true,
      select: true,
      "scrollY": 350,
      "scrollX": true,
      "ordering": true,
      "lengthMenu": [[5, 10, 50, -1], [5, 10, 50, "All"]],
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
            title: '<?= "REMATES ".$titulo_status_rem." ".$remates_almacen." ".$remates_plaza ?>',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: '<h5><?= "REMATES ".$titulo_status_rem." ".$remates_almacen." ".$remates_plaza ?></h5>',
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
    } );

});
</script>

<script>
$(document).ready(function() {

    $('#tabla_info_remates2').DataTable( {
      stateSave: true,
      select: true,
      "scrollY": 350,
      "scrollX": true,
      "ordering": true,
      "lengthMenu": [[5, 10, 50, -1], [5, 10, 50, "All"]],
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
            title: '<?= "REMATES ".$titulo_status_rem." ".$remates_almacen." ".$remates_plaza ?>',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: '<h5><?= "REMATES ".$titulo_status_rem." ".$remates_almacen." ".$remates_plaza ?></h5>',
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
    } );

});
</script>

<script>
$(document).ready(function() {

    $('#tabla_info_remates3').DataTable( {
      stateSave: true,
      select: true,
      "scrollY": 350,
      "scrollX": true,
      "ordering": true,
      "lengthMenu": [[5, 10, 50, -1], [5, 10, 50, "All"]],
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
            title: '<?= "REMATES ".$titulo_status_rem." ".$remates_almacen." ".$remates_plaza ?>',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: '<h5><?= "REMATES ".$titulo_status_rem." ".$remates_almacen." ".$remates_plaza ?></h5>',
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
    } );

});
</script>

<script>
     function cargarImagen(iidconsecutivo){
               $("#modal").load("../class/imagenes.php?iidconsecutivo="+iidconsecutivo+"");
     }
</script>

<script>
    function cargarImagenDañada(iid_remate){
      $("#modal").load("../class/imagenes_daniadas.php?iid_remate="+iid_remate+"");
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
        //Recuperar el valor de los datos datos de la diapositiva a estando activo si me crece el nepe 2 cms mas tendre un pene de 25 cms arriba las chivas hdspm
        var numeroSlide = $('#valor-car.active').data('slide-to');
        //$("#msg").html(numeroSlide);

        //Ocultar descripcion anterior
        $('.contenido').hide();

        //Apresentar o contenido hacer diapositiva
        $('.imagen' + numeroSlide).show();
    });
});
</script>
<!-- FLOT CHARTS -->
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
<script>
/*--------------- OPCIONES PARA LA GRAFICA DE DONA ---------------*/
  var options = {
     series: {
        pie3d: {
        stroke: { //define linea separadora
            width: 9,
            color: '#FFFFFF'
          } ,
          show: true,
          radius: 1, //radius: 1,  tamño radio del circulo
          tilt: 1,//rotacion de angulo
          innerRadius: 0,//radio dona o pastel
          label: {
            show: true,
            radius:3/4,//0.90,//posicion del label con data
            formatter: labelFormatter,
            background: {
                    //opacity: 0.5,///opacidad del fondo label
                    //color: '#fff' //7FDDE9
                    }
          },
        }
      },
      //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
      legend: {
          //labelBoxBorderColor: "none"
           show: false,
           // position: "ne" or "nw" or "se" or "sw"
      },

       grid: {
          hoverable: true,
          clickable: true,
          verticalLines: false,
          horizontalLines: false,
      },
      //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
      tooltip: {
      show: true,
      content: "<div style='font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;'>  <b> %s</b></font> <br><div style='text-align: center;color: white;text-shadow: -1px -1px 1px #333, 1px -1px 1px #333, -1px 1px 1px #333, 1px 1px 1px #333;'>%n</div></div>  </div>",
      defaultTheme: false
      }
};
/*--------------- DATOS PARA LA GRAFICA DE REMATES EN PROCESO ---------------*/
<?php
if ($remates_plaza == true){
$grafica_pla_proceso = $obj_remates->grafica_remates("ALM","1,2,3,4,5,6,7,8,9");
$label_donutProceso = '';
}else{
$grafica_pla_proceso = $obj_remates->grafica_remates("PLA","1,2,3,4,5,6,7,8,9");
$label_donutProceso = '<form action="remates.php" method="post">';
}
?>
var donutProceso = [<?php for ($i=0; $i <count($grafica_pla_proceso) ; $i++) { ?>
  {label: '<?=$label_donutProceso?><input type="hidden" name="remate_status" value="1,2,3,4,5,6,7,8,9"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="<?=$grafica_pla_proceso[$i]["PLA_ALM"]?>"  name="remates_plaza" class="btn btn-link btn-xs"><?=$grafica_pla_proceso[$i]["PLA_ALM"]?></button>',data:<?=$grafica_pla_proceso[$i]["TOTAL"]?>, color:"<?=$grafica_pla_proceso[$i]["COLOR"]?>"},
<?php } ?>];
/*--------------- DATOS PARA LA GRAFICA DE REMATES ADJUDICADOS ---------------*/
<?php
if ($remates_plaza == true){
$grafica_pla_adjudicada = $obj_remates->grafica_remates("ALM","10");
$label_donutAdjudicada = '';
}else{
$grafica_pla_adjudicada = $obj_remates->grafica_remates("PLA","10");
$label_donutAdjudicada = '<form action="remates.php" method="post">';
}
?>
    var donutAdjudicada = [<?php for ($i=0; $i <count($grafica_pla_adjudicada) ; $i++) { ?>
    {label: '<?=$label_donutAdjudicada?><input type="hidden" name="remate_status" value="10"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="<?=$grafica_pla_adjudicada[$i]["PLA_ALM"]?>"  name="remates_plaza" class="btn btn-link btn-xs"><?=$grafica_pla_adjudicada[$i]["PLA_ALM"]?></button>',data:<?=$grafica_pla_adjudicada[$i]["TOTAL"]?>, color:"<?=$grafica_pla_adjudicada[$i]["COLOR"]?>"},
    <?php } ?>];
/*--------------- DATOS PARA LA GRAFICA DE REMATES REGISTRO ---------------*/
<?php
if ($remates_plaza == true){
$grafica_pla_registro = $obj_remates->grafica_remates("ALM","0");
$label_donutRegistro = '';
}else{
$grafica_pla_registro = $obj_remates->grafica_remates("PLA","0");
$label_donutRegistro = '<form action="remates.php" method="post">';
}
?>
    var donutRegistro = [<?php for ($i=0; $i <count($grafica_pla_registro) ; $i++) { ?>
    {label: '<?=$label_donutRegistro ?><input type="hidden" name="remate_status" value="0"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="<?=$grafica_pla_registro[$i]["PLA_ALM"]?>"  name="remates_plaza" class="btn btn-link btn-xs"><?=$grafica_pla_registro[$i]["PLA_ALM"]?></button></form>',data:<?=$grafica_pla_registro[$i]["TOTAL"]?>, color:"<?=$grafica_pla_registro[$i]["COLOR"]?>"},
    <?php } ?>];
/**-------------------------------GRAFICA DESTRUIDO -----------------------------*/
<?php
if ($remates_plaza == true){
$grafica_pla_registro = $obj_remates->grafica_remates("ALM","12");
$label_donutRegistro = '';
}else{
$grafica_pla_registro = $obj_remates->grafica_remates("PLA","12");
$label_donutRegistro = '<form action="remates.php" method="post">';
}
?>
    var donutDestruido = [<?php for ($i=0; $i <count($grafica_pla_registro) ; $i++) { ?>
    {label: '<?=$label_donutRegistro ?><input type="hidden" name="remate_status" value="0"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="<?=$grafica_pla_registro[$i]["PLA_ALM"]?>"  name="remates_plaza" class="btn btn-link btn-xs"><?=$grafica_pla_registro[$i]["PLA_ALM"]?></button></form>',data:<?=$grafica_pla_registro[$i]["TOTAL"]?>, color:"<?=$grafica_pla_registro[$i]["COLOR"]?>"},
    <?php } ?>];
    /**-------------------------------GRAFICA  VENTA -----------------------------*/
    <?php
    if ($remates_plaza == true){
    $grafica_pla_registro = $obj_remates->grafica_remates("ALM","11");
    $label_donutRegistro = '';
    }else{
    $grafica_pla_registro = $obj_remates->grafica_remates("PLA","11");
    $label_donutRegistro = '<form action="remates.php" method="post">';
    }
    ?>
        var donuntVenta = [<?php for ($i=0; $i <count($grafica_pla_registro) ; $i++) { ?>
        {label: '<?=$label_donutRegistro ?><input type="hidden" name="remate_status" value="0"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="<?=$grafica_pla_registro[$i]["PLA_ALM"]?>"  name="remates_plaza" class="btn btn-link btn-xs"><?=$grafica_pla_registro[$i]["PLA_ALM"]?></button></form>',data:<?=$grafica_pla_registro[$i]["TOTAL"]?>, color:"<?=$grafica_pla_registro[$i]["COLOR"]?>"},
        <?php } ?>];
/*---------------------------------ASIGNANDO LA GRAFICA----------------------------*/
$(document).ready(function(){
  <?php if ($remate_status == null || $remate_status == '1,2,3,4,5,6,7,8,9' && $remates_almacen == false){ ?>
  if (($(donutProceso).length > 0)){
    $.plot($("#grafica_proceso"), donutProceso, options);
  }
  <?php } ?>
  <?php if ($remate_status == null || $remate_status == '10' && $remates_almacen == false){ ?>
  if (($(donutAdjudicada).length > 0)){
    $.plot($("#grafica_adjudicada"), donutAdjudicada, options);
  }
  <?php } ?>
  <?php if ($remate_status == null || $remate_status == '0' && $remates_almacen == false){ ?>
  if (($(donutRegistro).length > 0)){
    $.plot($("#grafica_registro"), donutRegistro, options);
  }
  <?php } ?>
  <?php if ($remate_status == null || $remate_status == '12' && $remates_almacen == false){ ?>
  if (($(donutRegistro).length > 0)){
    $.plot($("#grafica_destruido"), donutDestruido, options);
  }
  <?php } ?>
  <?php if ($remate_status == null || $remate_status == '11' && $remates_almacen == false){ ?>
  if (($(donutRegistro).length > 0)){
    $.plot($("#grafica_venta"), donuntVenta, options);
  }
  <?php } ?>
});
///////////
function labelFormatter(label, series) {
    return label
        +"<div style='text-align: center;color: white;text-shadow: -1px -1px 1px #333, 1px -1px 1px #333, -1px 1px 1px #333, 1px 1px 1px #333;'>"+(series.percent).toFixed(1)+ "%</div>"
        + "</div></button>";//<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + series.data[0][1] + '</div>
  }
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
