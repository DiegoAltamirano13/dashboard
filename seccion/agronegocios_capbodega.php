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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '53');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/*INICIA VARIBLES PARA SESSIONES*/
/*session capacidad en bodaga atoyaquillo*/
if ($_SESSION["cap_atoyaquillo"]==false){
  $_SESSION["cap_atoyaquillo"] = 200;
  $cap_atoyaquillo = $_SESSION["cap_atoyaquillo"];
}else{
  if( isset($_POST["cap_atoyaquillo"]))
  $_SESSION["cap_atoyaquillo"] = $_POST["cap_atoyaquillo"];
  $cap_atoyaquillo = $_SESSION["cap_atoyaquillo"];
}
/*session capacidad en bodaga kenworth*/
if ($_SESSION["cap_kenworth"]==false){
  $_SESSION["cap_kenworth"] = 50;
  $cap_kenworth = $_SESSION["cap_kenworth"];
}else{
  if( isset($_POST["cap_kenworth"]))
  $_SESSION["cap_kenworth"] = $_POST["cap_kenworth"];
  $cap_kenworth = $_SESSION["cap_kenworth"];
}
/*session capacidad en bodaga peñuela*/
if ($_SESSION["cap_penuela"]==false){
  $_SESSION["cap_penuela"] = 250;
  $cap_penuela = $_SESSION["cap_penuela"];
}else{
  if( isset($_POST["cap_penuela"]))
  $_SESSION["cap_penuela"] = $_POST["cap_penuela"];
  $cap_penuela = $_SESSION["cap_penuela"];
}
/*session capacidad en bodaga argo fraile a*/
if ($_SESSION["cap_fraile_a"]==false){
  $_SESSION["cap_fraile_a"] = 100;
  $cap_fraile_a = $_SESSION["cap_fraile_a"];
}else{
  if( isset($_POST["cap_fraile_a"]))
  $_SESSION["cap_fraile_a"] = $_POST["cap_fraile_a"];
  $cap_fraile_a = $_SESSION["cap_fraile_a"];
}
/*session capacidad en bodaga argo ocaba*/
if ($_SESSION["cap_ocaba"]==false){
  $_SESSION["cap_ocaba"] = 100;
  $cap_ocaba = $_SESSION["cap_ocaba"];
}else{
  if( isset($_POST["cap_ocaba"]))
  $_SESSION["cap_ocaba"] = $_POST["cap_ocaba"];
  $cap_ocaba = $_SESSION["cap_ocaba"];
}

/*session inicio turno1*/
if (isset($_POST["t1_inicio_capbodega"]))
  $_SESSION["t1_inicio_capbodega"] = $_POST["t1_inicio_capbodega"];
  $t1_inicio_capbodega = $_SESSION["t1_inicio_capbodega"];
/*session fin turno1*/
if (isset($_POST["t1_fin_capbodega"]))
  $_SESSION["t1_fin_capbodega"] = $_POST["t1_fin_capbodega"];
  $t1_fin_capbodega = $_SESSION["t1_fin_capbodega"];
/*session inicio turno2*/
if (isset($_POST["t2_inicio_capbodega"]))
  $_SESSION["t2_inicio_capbodega"] = $_POST["t2_inicio_capbodega"];
  $t2_inicio_capbodega = $_SESSION["t2_inicio_capbodega"];
/*session fin turno2*/
if (isset($_POST["t2_fin_capbodega"]))
  $_SESSION["t2_fin_capbodega"] = $_POST["t2_fin_capbodega"];
  $t2_fin_capbodega = $_SESSION["t2_fin_capbodega"];
/*INICIA INSTANCIAS*/
include_once "../class/Agronegocio_cap_bodega.php";
$obj_turno_uno = new Turno_uno();
$obj_turno_dos = new Turno_dos();

///////////////////////////////////////////
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- *********** INICIA INCLUDE CSS *********** -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">

<link rel="stylesheet" href="../plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css">


<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Dashboard<small>Agronegocios</small></h1>
      <ol class="breadcrumb">
        <li><a href="" data-toggle="modal" data-target="#modal_config_capton"><i class="fa fa-gears"></i> Modificar Capacidad de Toneladas</a></li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->



<!-- ######################### INICIA VENTADA PARA LOS MODALS ######################### -->
<!-- *********************** INICIA MODAL INGRESOS DE DATOS PRIMER TURNO *********************** -->
  <div class="modal fade" id="modal_turno1" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Datos del Primer Turno</h4>
        </div>
        <div class="modal-body">

          <div class="col-md-5">
            <div class="col-md-9">
              <label>Inicio:</label>
              <div class='input-group date' id="datatime_t1_inicio_capbodega">
                <input type='text' class="form-control" value="<?=$t1_inicio_capbodega?>" />
                <span class="input-group-addon">
                  <span class="fa fa-calendar"></span>
                </span>
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="col-md-9">
              <label>Final:</label>
              <div class='input-group date' id="datatime_t1_fin_capbodega">
                <input type='text' class="form-control" value="<?=$t1_fin_capbodega?>" />
                <span class="input-group-addon">
                  <span class="fa fa-calendar"></span>
                </span>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
            <form method="post">
              <input type='hidden' class="form-control" id="t1_inicio_capbodega" name="t1_inicio_capbodega" value="<?=$t1_inicio_capbodega?>" />
              <input type='hidden' class="form-control" id="t1_fin_capbodega" name="t1_fin_capbodega" value="<?=$t1_fin_capbodega?>" />
              <button class="btn btn-sm btn-success" type="submit">OK</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </form>
        </div>
      </div>
    </div>
  </div>
<!-- *********************** INICIA MODAL INGRESOS DE DATOS SEGUNDO TURNO *********************** -->
  <div class="modal fade" id="modal_turno2" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Datos del Segundo Turno</h4>
        </div>
        <div class="modal-body">

          <div class="col-md-5">
            <div class="col-md-9">
              <label>Inicio:</label>
              <div class='input-group date' id="datatime_t2_inicio_capbodega">
                <input type='text' class="form-control" value="<?=$t2_inicio_capbodega?>" />
                <span class="input-group-addon">
                  <span class="fa fa-calendar"></span>
                </span>
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="col-md-9">
              <label>Final:</label>
              <div class='input-group date' id="datatime_t2_fin_capbodega">
                <input type='text' class="form-control" value="<?=$t2_fin_capbodega?>" />
                <span class="input-group-addon">
                  <span class="fa fa-calendar"></span>
                </span>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
            <form method="post">
              <input type='hidden' class="form-control" id="t2_inicio_capbodega" name="t2_inicio_capbodega" value="<?=$t2_inicio_capbodega?>" />
              <input type='hidden' class="form-control" id="t2_fin_capbodega" name="t2_fin_capbodega" value="<?=$t2_fin_capbodega?>" />
              <button class="btn btn-sm btn-success" type="submit">OK</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </form>
        </div>
      </div>
    </div>
  </div>
<!-- *********************** INICIA MODAL CONFIGURAR CAPACIDAD DE TONELADAS *********************** -->
  <div class="modal fade" id="modal_config_capton" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Capacidad de Bodegas por Horas</h4>
        </div>
        <div class="modal-body">

          <form method="post">

          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-responsive">
              <thead>
                <tr>
                  <th class="small">ALMACÉN</th>
                  <th class="small">LÍMITE DE CAPACIDAD POR HORA</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="small">ATOYAQUILLO</td>
                  <td class="small"><input type="number" step="0.1" name="cap_atoyaquillo" value="<?=$cap_atoyaquillo?>"></td>
                </tr>
                <tr>
                  <td class="small">KENWORTH</td>
                  <td class="small"><input type="number" step="0.1" name="cap_kenworth" value="<?=$cap_kenworth?>"></td>
                </tr>
                <tr>
                  <td class="small">PEÑUELA</td>
                  <td class="small"><input type="number" step="0.1" name="cap_penuela" value="<?=$cap_penuela?>"></td>
                </tr>
                <tr>
                  <td class="small">ARGO FRAILE A</td>
                  <td class="small"><input type="number" step="0.1" name="cap_fraile_a" value="<?=$cap_fraile_a?>"></td>
                </tr>
                <tr>
                  <td class="small">ARGO OCABA</td>
                  <td class="small"><input type="number" step="0.1" name="cap_ocaba" value="<?=$cap_ocaba?>"></td>
                </tr>

              </tbody>
            </table>
            </div>

            <button class="btn btn-sm btn-success" type="submit">OK</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

          </form>

        </div>
      </div>
    </div>
  </div>
<!-- ######################### TERMINA VENTADA PARA LOS MODALS ######################### -->


<!-- ############################ INICIA SECCION TONELADAS ACOMULADAS TURNO 1 ############################# -->
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Turno 1</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_turno1"> Ingresa Datos del Primer Turno</button>

    <!-- Inicia code para contar las toneladas en bodega turno 1 -->
    <?php $turno1_cap_bodega = $obj_turno_uno->capacidad_bodega($t1_inicio_capbodega,$t1_fin_capbodega);
    for ($i=0; $i <count($turno1_cap_bodega) ; $i++) {
      //BUSCA BODEGAS DE ATOYAQUILLO
      $busca_bod = strpos($turno1_cap_bodega[$i]["ALMACEN"], 'ATOYAQUILLO');
      if ($busca_bod !== false) { $t1_bod_atoyaquillo[$i] = $turno1_cap_bodega[$i]["TONELADAS"];}else{$t1_bod_atoyaquillo[$i] = 0;}
      $t1_sumarray_atoyaquillo = array_sum($t1_bod_atoyaquillo);
      //BUSCA BODEGAS DE KENWORTH
      $busca_bod = strpos($turno1_cap_bodega[$i]["ALMACEN"], 'KENWORTH');
      if ($busca_bod !== false) { $t1_bod_kenworth[$i] = $turno1_cap_bodega[$i]["TONELADAS"];}else{$t1_bod_kenworth[$i] = 0;}
      $t1_sumarray_kenworth = array_sum($t1_bod_kenworth);
      //BUSCA BODEGAS DE PEÑUELA
      $busca_bod = strpos($turno1_cap_bodega[$i]["ALMACEN"], 'PEÑUELA');
      if ($busca_bod !== false) { $t1_bod_penuela[$i] = $turno1_cap_bodega[$i]["TONELADAS"];}else{$t1_bod_penuela[$i] = 0;}
      $t1_sumarray_penuela = array_sum($t1_bod_penuela);
      //BUSCA BODEGAS ARGO FRAILE A
      $busca_bod = strpos($turno1_cap_bodega[$i]["ALMACEN"], 'FRAILE A');
      if ($busca_bod !== false) { $t1_bod_fraile_a[$i] = $turno1_cap_bodega[$i]["TONELADAS"];}else{$t1_bod_fraile_a[$i] = 0;}
      $t1_sumarray_fraile_a = array_sum($t1_bod_fraile_a);
      //BUSCA BODEGAS ARGO OCABA
      $busca_bod = strpos($turno1_cap_bodega[$i]["ALMACEN"], 'ARGO OCABA');
      if ($busca_bod !== false) { $t1_bod_ocaba[$i] = $turno1_cap_bodega[$i]["TONELADAS"];}else{$t1_bod_ocaba[$i] = 0;}
      $t1_sumarray_ocaba = array_sum($t1_bod_ocaba);
    }

    $t1_fecha_diferencia = strtotime($t1_fin_capbodega)-strtotime($t1_inicio_capbodega) ;
    $turno1_hrs = ($t1_fecha_diferencia/60/60); //Pasamos el tiempo a horas
    $turno1_hrs = (INT)($turno1_hrs);
    ?>
    <!-- Termina code para contar las toneladas en bodega turno 1 -->

    <!-- INICIA CODE TABLA Y GRAFICA CAPACIDAD DE ALMACENAJE EN BODEGA -->
    <div class="row">
    <!-- tabla cap carga turno1 -->
      <div class="col-sm-12 col-md-5">
        <br>

        <div class="table-responsive">
        <table class="table table-hover table-condensed table-bordered table-striped">
          <thead>
            <tr>
              <th class="small" colspan="4">
              <form method="post">
                <div class="input-group">
                  <small class="input-group-addon"><i class="fa fa-clock-o"></i> INICIO TURNO:</small>
                  <input type="text" class="form-control maskInputDT" name="t1_inicio_capbodega" value="<?=$t1_inicio_capbodega?>" placeholder="dd-mm-yyyy hh:mm am">
                </div>
                <div class="input-group">
                  <small class="input-group-addon"><i class="fa fa-clock-o"></i> TERMINO TURNO:</small>
                  <input type="text" class="form-control maskInputDT" name="t1_fin_capbodega" value="<?=$t1_fin_capbodega?>" placeholder="dd-mm-yyyy hh:mm am">
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-flat">Filtrar!</button>
                  </span>
                </div>
              </form>
              </th>
            </tr>
            <tr>
              <th class="small" colspan="2">HORAS EN TURNO:</th>
              <td class="small" colspan="2"><?=$turno1_hrs?> HRS.</td>
            </tr>
            <tr>
              <th class="small">BODEGA</th>
              <th class="small">LIMITE EN BODEGA</th>
              <th class="small">TONELADAS REGISTRADAS EN TURNO</th>
              <th class="small">SOBREPASO EN BODEGA</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="small">ATOYAQUILLO</td>
              <td class="small"><?php echo $t1_limite_atoyaquillo=$cap_atoyaquillo*$turno1_hrs;?> Ton.</td>
              <td class="small"><div class="btn btn-link btn-xs" id="click_turno1_det_atoyaquillo"><?= $t1_sumarray_atoyaquillo ?> Ton.</div></td>
              <td class="small">
              <?php
              $sobrepaso_t1_atoyaquillo = $t1_sumarray_atoyaquillo - $t1_limite_atoyaquillo;
              if ($sobrepaso_t1_atoyaquillo>0){echo "<code>".$sobrepaso_t1_atoyaquillo." Ton.</code>";}else{echo "0 Ton.";}
              ?>
              </td>
            </tr>
            <?php
            //DETALLE BODEGA ATOYAQUILLO
            for ($i=0; $i <count($turno1_cap_bodega) ; $i++) {
              $busca_bod = strpos($turno1_cap_bodega[$i]["ALMACEN"], 'ATOYAQUILLO');
              if ($busca_bod !== false) {
            ?>
            <tr class="bg-gray" id="turno1_det_atoyaquillo[]" style="display: none;">
              <td colspan="2" class="small"><?=$turno1_cap_bodega[$i]["ALMACEN"]?></td>
              <td colspan="2" class="small"><?=round($turno1_cap_bodega[$i]["TONELADAS"],2)?> Ton. <cite>(Operación: <?= $turno1_cap_bodega[$i]["TIPO"]?>)</cite></td>
            </tr>
            <?php } } ?>
            <tr>
              <td class="small">KENWORTH</td>
              <td class="small"><?php echo $t1_limite_kenworth=$cap_kenworth*$turno1_hrs;?> Ton.</td>
              <td class="small"><div class="btn btn-link btn-xs" id="click_turno1_det_kenworth"><?= $t1_sumarray_kenworth ?> Ton.</div></td>
              <td class="small">
              <?php
              $sobrepaso_t1_kenworth = $t1_sumarray_kenworth - $t1_limite_kenworth;
              if ($sobrepaso_t1_kenworth>0){echo "<code>".$sobrepaso_t1_kenworth." Ton.</code>";}else{echo "0 Ton.";}
              ?>
              </td>
            </tr>
            <?php
            //DETALLE BODEGA KENWORTH
            for ($i=0; $i <count($turno1_cap_bodega) ; $i++) {
              $busca_bod = strpos($turno1_cap_bodega[$i]["ALMACEN"], 'KENWORTH');
              if ($busca_bod !== false) {
            ?>
            <tr class="bg-gray" id="turno1_det_kenworth[]" style="display: none;">
              <td colspan="2" class="small"><?=$turno1_cap_bodega[$i]["ALMACEN"]?></td>
              <td colspan="2" class="small"><?=round($turno1_cap_bodega[$i]["TONELADAS"],2)?> Ton. <cite>(Operación: <?= $turno1_cap_bodega[$i]["TIPO"]?>)</cite></td>
            </tr>
            <?php } } ?>
            <tr>
              <td class="small">PEÑUELA</td>
              <td class="small"><?php echo $t1_limite_penuela=$cap_penuela*$turno1_hrs;?> Ton.</td>
              <td class="small"><div class="btn btn-link btn-xs" id="click_turno1_det_penuela"><?= $t1_sumarray_penuela ?> Ton.</div></td>
              <td class="small">
              <?php
              $sobrepaso_t1_penuela = $t1_sumarray_penuela - $t1_limite_penuela;
              if ($sobrepaso_t1_penuela>0){echo "<code>".$sobrepaso_t1_penuela." Ton.</code>";}else{echo "0 Ton.";}
              ?>
              </td>
            </tr>
            <?php
            //DETALLE BODEGA PEÑUELA
            for ($i=0; $i <count($turno1_cap_bodega) ; $i++) {
              $busca_bod = strpos($turno1_cap_bodega[$i]["ALMACEN"], 'PEÑUELA');
              if ($busca_bod !== false) {
            ?>
            <tr class="bg-gray" id="turno1_det_penuela[]" style="display: none;">
              <td colspan="2" class="small"><?=$turno1_cap_bodega[$i]["ALMACEN"]?></td>
              <td colspan="2" class="small"><?=round($turno1_cap_bodega[$i]["TONELADAS"],2)?> Ton. <cite>(Operación: <?= $turno1_cap_bodega[$i]["TIPO"]?>)</cite></td>
            </tr>
            <?php } } ?>
            <tr>
              <td class="small">ARGO FRAILE A</td>
              <td class="small"><?php echo $t1_limite_fraile_a=$cap_fraile_a*$turno1_hrs;?> Ton.</td>
              <td class="small"><div class="btn btn-link btn-xs" id="click_turno1_det_fraile_a"><?= $t1_sumarray_fraile_a ?> Ton.</div></td>
              <td class="small">
              <?php
              $sobrepaso_t1_fraile_a = $t1_sumarray_fraile_a - $t1_limite_fraile_a;
              if ($sobrepaso_t1_fraile_a>0){echo "<code>".$sobrepaso_t1_fraile_a." Ton.</code>";}else{echo "0 Ton.";}
              ?>
              </td>
            </tr>
            <?php
            //DETALLE ARGO FRAILE A
            for ($i=0; $i <count($turno1_cap_bodega) ; $i++) {
              $busca_bod = strpos($turno1_cap_bodega[$i]["ALMACEN"], 'FRAILE A');
              if ($busca_bod !== false) {
            ?>
            <tr class="bg-gray" id="turno1_det_fraile_a[]" style="display: none;">
              <td colspan="2" class="small"><?=$turno1_cap_bodega[$i]["ALMACEN"]?></td>
              <td colspan="2" class="small"><?=round($turno1_cap_bodega[$i]["TONELADAS"],2)?> Ton. <cite>(Operación: <?= $turno1_cap_bodega[$i]["TIPO"]?>)</cite></td>
            </tr>
            <?php } } ?>
            <tr>
              <td class="small">ARGO OCABA</td>
              <td class="small"><?php echo $t1_limite_ocaba=$cap_ocaba*$turno1_hrs;?> Ton.</td>
              <td class="small"><div class="btn btn-link btn-xs" id="click_turno1_det_ocaba"><?= $t1_sumarray_ocaba ?> Ton.</div></td>
              <td class="small">
              <?php
              $sobrepaso_t1_ocaba = $t1_sumarray_ocaba - $t1_limite_ocaba;
              if ($sobrepaso_t1_ocaba>0){echo "<code>".$sobrepaso_t1_ocaba." Ton.</code>";}else{echo "0 Ton.";}
              ?>
              </td>
            </tr>
            <?php
            //DETALLE ARGO OCABA
            for ($i=0; $i <count($turno1_cap_bodega) ; $i++) {
              $busca_bod = strpos($turno1_cap_bodega[$i]["ALMACEN"], 'ARGO OCABA');
              if ($busca_bod !== false) {
            ?>
            <tr class="bg-gray" id="turno1_det_ocaba[]" style="display: none;">
              <td colspan="2" class="small"><?=$turno1_cap_bodega[$i]["ALMACEN"]?></td>
              <td colspan="2" class="small"><?=round($turno1_cap_bodega[$i]["TONELADAS"],2)?> Ton. <cite>(Operación: <?= $turno1_cap_bodega[$i]["TIPO"]?>)</cite></td>
            </tr>
            <?php } } ?>


          </tbody>
        </table>
        </div>

      </div>
    <!-- grafica turno 1 cap carga -->
      <div class="col-sm-12 col-md-7">
        <div id="grafica_turno1" style="min-width: 200px; height: 400px; margin: 0 auto"></div>
      </div>
    </div>
    <!-- TERMINA CODE TABLA Y GRAFICA CAPACIDAD DE ALMACENAJE EN BODEGA -->

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION TONELADAS ACOMULADAS TURNO 1 ########################### -->



<!-- ############################ INICIA SECCION TONELADAS ACOMULADAS TURNO 2 ############################# -->
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Turno 2</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_turno2"> Ingresa Datos del Segundo Turno</button>

    <!-- Inicia code para contar las toneladas en bodega turno 2 -->
    <?php $turno2_cap_bodega = $obj_turno_dos->capacidad_bodega($t2_inicio_capbodega,$t2_fin_capbodega);
    for ($i=0; $i <count($turno2_cap_bodega) ; $i++) {
      //BUSCA BODEGAS DE ATOYAQUILLO
      $busca_bod = strpos($turno2_cap_bodega[$i]["ALMACEN"], 'ATOYAQUILLO');
      if ($busca_bod !== false) { $t2_bod_atoyaquillo[$i] = $turno2_cap_bodega[$i]["TONELADAS"];}else{$t2_bod_atoyaquillo[$i] = 0;}
      $t2_sumarray_atoyaquillo = array_sum($t2_bod_atoyaquillo);
      //BUSCA BODEGAS DE KENWORTH
      $busca_bod = strpos($turno2_cap_bodega[$i]["ALMACEN"], 'KENWORTH');
      if ($busca_bod !== false) { $t2_bod_kenworth[$i] = $turno2_cap_bodega[$i]["TONELADAS"];}else{$t2_bod_kenworth[$i] = 0;}
      $t2_sumarray_kenworth = array_sum($t2_bod_kenworth);
      //BUSCA BODEGAS DE PEÑUELA
      $busca_bod = strpos($turno2_cap_bodega[$i]["ALMACEN"], 'PEÑUELA');
      if ($busca_bod !== false) { $t2_bod_penuela[$i] = $turno2_cap_bodega[$i]["TONELADAS"];}else{$t2_bod_penuela[$i] = 0;}
      $t2_sumarray_penuela = array_sum($t2_bod_penuela);
      //BUSCA BODEGAS ARGO FRAILE A
      $busca_bod = strpos($turno2_cap_bodega[$i]["ALMACEN"], 'FRAILE A');
      if ($busca_bod !== false) { $t2_bod_fraile_a[$i] = $turno2_cap_bodega[$i]["TONELADAS"];}else{$t2_bod_fraile_a[$i] = 0;}
      $t2_sumarray_fraile_a = array_sum($t2_bod_fraile_a);
      //BUSCA BODEGAS ARGO OCABA
      $busca_bod = strpos($turno2_cap_bodega[$i]["ALMACEN"], 'ARGO OCABA');
      if ($busca_bod !== false) { $t2_bod_ocaba[$i] = $turno2_cap_bodega[$i]["TONELADAS"];}else{$t2_bod_ocaba[$i] = 0;}
      $t2_sumarray_ocaba = array_sum($t2_bod_ocaba);



    }

    $t2_fecha_diferencia = strtotime($t2_fin_capbodega)-strtotime($t2_inicio_capbodega) ;
    $turno2_hrs = ($t2_fecha_diferencia/60/60); //Pasamos el tiempo a horas
    $turno2_hrs = (INT)($turno2_hrs);
    ?>
    <!-- Termina code para contar las toneladas en bodega turno 2 -->

    <!-- INICIA CODE TABLA Y GRAFICA CAPACIDAD DE ALMACENAJE EN BODEGA -->
    <div class="row">
    <!-- tabla cap carga turno2 -->
      <div class="col-sm-12 col-md-5">
        <br>

        <div class="table-responsive">
        <table class="table table-hover table-condensed table-bordered table-striped">
          <thead>
            <tr>
              <th class="small" colspan="4">
              <form method="post">
                <div class="input-group">
                  <small class="input-group-addon"><i class="fa fa-clock-o"></i> INICIO TURNO:</small>
                  <input type="text" class="form-control maskInputDT" name="t2_inicio_capbodega" value="<?=$t2_inicio_capbodega?>" placeholder="dd-mm-yyyy hh:mm am">
                </div>
                <div class="input-group">
                  <small class="input-group-addon"><i class="fa fa-clock-o"></i> TERMINO TURNO:</small>
                  <input type="text" class="form-control maskInputDT" name="t2_fin_capbodega" value="<?=$t2_fin_capbodega?>" placeholder="dd-mm-yyyy hh:mm am">
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-flat">Filtrar!</button>
                  </span>
                </div>
              </form>
              </th>
            </tr>
            <tr>
              <th class="small" colspan="2">HORAS EN TURNO:</th>
              <td class="small" colspan="2"><?=$turno2_hrs?>HRS .</td>
            </tr>
            <tr>
              <th class="small">BODEGA</th>
              <th class="small">LIMITE EN BODEGA</th>
              <th class="small">TONELADAS REGISTRADAS EN TURNO</th>
              <th class="small">SOBREPASO EN BODEGA</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="small">ATOYAQUILLO</td>
              <td class="small"><?php echo $t2_limite_atoyaquillo=$cap_atoyaquillo*$turno2_hrs;?> Ton.</td>
              <td class="small"><div class="btn btn-link btn-xs" id="click_turno2_det_atoyaquillo"><?= $t2_sumarray_atoyaquillo ?> Ton.</div></td>
              <td class="small">
              <?php
              $sobrepaso_t2_atoyaquillo = $t2_sumarray_atoyaquillo - $t2_limite_atoyaquillo;
              if ($sobrepaso_t2_atoyaquillo>0){echo "<code>".$sobrepaso_t2_atoyaquillo." Ton.</code>";}else{echo "0 Ton.";}
              ?>
              </td>
            </tr>
            <?php
            //DETALLE BODEGA ATOYAQUILLO
            for ($i=0; $i <count($turno2_cap_bodega) ; $i++) {
              $busca_bod = strpos($turno2_cap_bodega[$i]["ALMACEN"], 'ATOYAQUILLO');
              if ($busca_bod !== false) {
            ?>
            <tr class="bg-gray" id="turno2_det_atoyaquillo[]" style="display: none;">
              <td colspan="2" class="small"><?=$turno2_cap_bodega[$i]["ALMACEN"]?></td>
              <td colspan="2" class="small"><?=round($turno2_cap_bodega[$i]["TONELADAS"],2)?> Ton. <cite>(Operación:<?=$turno2_cap_bodega[$i]["TIPO"]?>)</cite></td>
            </tr>
            <?php } } ?>
            <tr>
              <td class="small">KENWORTH</td>
              <td class="small"><?php echo $t2_limite_kenworth=$cap_kenworth*$turno2_hrs;?> Ton.</td>
              <td class="small"><div class="btn btn-link btn-xs" id="click_turno2_det_kenworth"><?= $t2_sumarray_kenworth ?> Ton.</div></td>
              <td class="small">
              <?php
              $sobrepaso_t2_kenworth = $t2_sumarray_kenworth - $t2_limite_kenworth;
              if ($sobrepaso_t2_kenworth>0){echo "<code>".$sobrepaso_t2_kenworth." Ton.</code>";}else{echo "0 Ton.";}
              ?>
              </td>
            </tr>
            <?php
            //DETALLE BODEGA KENWORTH
            for ($i=0; $i <count($turno2_cap_bodega) ; $i++) {
              $busca_bod = strpos($turno2_cap_bodega[$i]["ALMACEN"], 'KENWORTH');
              if ($busca_bod !== false) {
            ?>
            <tr class="bg-gray" id="turno2_det_kenworth[]" style="display: none;">
              <td colspan="2" class="small"><?=$turno2_cap_bodega[$i]["ALMACEN"]?></td>
              <td colspan="2" class="small"><?=round($turno2_cap_bodega[$i]["TONELADAS"],2)?> Ton. <cite>(Operación:<?=$turno2_cap_bodega[$i]["TIPO"]?>)</cite></td>
            </tr>
            <?php } } ?>
            <tr>
              <td class="small">PEÑUELA</td>
              <td class="small"><?php echo $t2_limite_penuela=$cap_penuela*$turno2_hrs;?> Ton.</td>
              <td class="small"><div class="btn btn-link btn-xs" id="click_turno2_det_penuela"><?= $t2_sumarray_penuela ?> Ton.</div></td>
              <td class="small">
              <?php
              $sobrepaso_t2_penuela = $t2_sumarray_penuela - $t2_limite_penuela;
              if ($sobrepaso_t2_penuela>0){echo "<code>".$sobrepaso_t2_penuela." Ton.</code>";}else{echo "0 Ton.";}
              ?>
              </td>
            </tr>
            <?php
            //DETALLE BODEGA PEÑUELA
            for ($i=0; $i <count($turno2_cap_bodega) ; $i++) {
              $busca_bod = strpos($turno2_cap_bodega[$i]["ALMACEN"], 'PEÑUELA');
              if ($busca_bod !== false) {
            ?>
            <tr class="bg-gray" id="turno2_det_penuela[]" style="display: none;">
              <td colspan="2" class="small"><?=$turno2_cap_bodega[$i]["ALMACEN"]?></td>
              <td colspan="2" class="small"><?=round($turno2_cap_bodega[$i]["TONELADAS"],2)?> Ton. <cite>(Operación:<?=$turno2_cap_bodega[$i]["TIPO"]?>)</cite></td>
            </tr>
            <?php } } ?>
            <tr>
              <td class="small">ARGO FRAILE A</td>
              <td class="small"><?php echo $t2_limite_fraile_a=$cap_fraile_a*$turno2_hrs;?> Ton.</td>
              <td class="small"><div class="btn btn-link btn-xs" id="click_turno2_det_fraile_a"><?= $t2_sumarray_fraile_a ?> Ton.</div></td>
              <td class="small">
              <?php
              $sobrepaso_t2_fraile_a = $t2_sumarray_fraile_a - $t2_limite_fraile_a;
              if ($sobrepaso_t2_fraile_a>0){echo "<code>".$sobrepaso_t2_fraile_a." Ton.</code>";}else{echo "0 Ton.";}
              ?>
              </td>
            </tr>
            <?php
            //DETALLE ARGO FRAILE A
            for ($i=0; $i <count($turno2_cap_bodega) ; $i++) {
              $busca_bod = strpos($turno2_cap_bodega[$i]["ALMACEN"], 'FRAILE A');
              if ($busca_bod !== false) {
            ?>
            <tr class="bg-gray" id="turno2_det_fraile_a[]" style="display: none;">
              <td colspan="2" class="small"><?=$turno2_cap_bodega[$i]["ALMACEN"]?></td>
              <td colspan="2" class="small"><?=round($turno2_cap_bodega[$i]["TONELADAS"],2)?> Ton. <cite>(Operación: <?= $turno2_cap_bodega[$i]["TIPO"]?>)</cite></td>
            </tr>
            <?php } } ?>

            <tr>
              <td class="small">ARGO OCABA</td>
              <td class="small"><?php echo $t2_limite_ocaba=$cap_ocaba*$turno2_hrs;?> Ton.</td>
              <td class="small"><div class="btn btn-link btn-xs" id="click_turno2_det_ocaba"><?= $t2_sumarray_ocaba ?> Ton.</div></td>
              <td class="small">
              <?php
              $sobrepaso_t2_ocaba = $t2_sumarray_ocaba - $t2_limite_ocaba;
              if ($sobrepaso_t2_ocaba>0){echo "<code>".$sobrepaso_t2_ocaba." Ton.</code>";}else{echo "0 Ton.";}
              ?>
              </td>
            </tr>
            <?php
            //DETALLE ARGO OCABA
            for ($i=0; $i <count($turno2_cap_bodega) ; $i++) {
              $busca_bod = strpos($turno2_cap_bodega[$i]["ALMACEN"], 'ARGO OCABA');
              if ($busca_bod !== false) {
            ?>
            <tr class="bg-gray" id="turno2_det_ocaba[]" style="display: none;">
              <td colspan="2" class="small"><?=$turno2_cap_bodega[$i]["ALMACEN"]?></td>
              <td colspan="2" class="small"><?=round($turno2_cap_bodega[$i]["TONELADAS"],2)?> Ton. <cite>(Operación: <?= $turno2_cap_bodega[$i]["TIPO"]?>)</cite></td>
            </tr>
            <?php } } ?>

          </tbody>
        </table>
        </div>

      </div>
    <!-- grafica turno 1 cap carga -->
      <div class="col-sm-12 col-md-7">
        <div id="grafica_turno2" style="min-width: 200px; height: 400px; margin: 0 auto"></div>
      </div>
    </div>
    <!-- TERMINA CODE TABLA Y GRAFICA CAPACIDAD DE ALMACENAJE EN BODEGA -->

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION TONELADAS ACOMULADAS TURNO 2 ########################### -->




    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<script>
  //ALMACEN ATOYAQUILLO
  //TURNO1
  $( "#click_turno1_det_atoyaquillo" ).click(function() {
    $( "#turno1_det_atoyaquillo\\[\\]" ).toggle( "slow", function() {
    });
  });
  //TURNO2
  $( "#click_turno2_det_atoyaquillo" ).click(function() {
    $( "#turno2_det_atoyaquillo\\[\\]" ).toggle( "slow", function() {
    });
  });
  //ALMACEN KENWORTH
  //TURNO1
  $( "#click_turno1_det_kenworth" ).click(function() {
    $( "#turno1_det_kenworth\\[\\]" ).toggle( "slow", function() {
    });
  });
  //TURNO2
  $( "#click_turno2_det_kenworth" ).click(function() {
    $( "#turno2_det_kenworth\\[\\]" ).toggle( "slow", function() {
    });
  });
  //ALMACEN PENUELA
  //TURNO1
  $( "#click_turno1_det_penuela" ).click(function() {
    $( "#turno1_det_penuela\\[\\]" ).toggle( "slow", function() {
    });
  });
  //TURNO2
  $( "#click_turno2_det_penuela" ).click(function() {
    $( "#turno2_det_penuela\\[\\]" ).toggle( "slow", function() {
    });
  });
  //ALMACEN ARGO FRAILE A
  //TURNO1
  $( "#click_turno1_det_fraile_a" ).click(function() {
    $( "#turno1_det_fraile_a\\[\\]" ).toggle( "slow", function() {
    });
  });
  //TURNO2
  $( "#click_turno2_det_fraile_a" ).click(function() {
    $( "#turno2_det_fraile_a\\[\\]" ).toggle( "slow", function() {
    });
  });
  //ALMACEN ARGO OCABA
  //TURNO1
  $( "#click_turno1_det_ocaba" ).click(function() {
    $( "#turno1_det_ocaba\\[\\]" ).toggle( "slow", function() {
    });
  });
  //TURNO2
  $( "#click_turno2_det_ocaba" ).click(function() {
    $( "#turno2_det_ocaba\\[\\]" ).toggle( "slow", function() {
    });
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
<!-- Bootstrap-datetimepicker -->
<script src="../plugins/bootstrap-datetimepicker/moment.min.js"></script>
<script src="../plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js"></script>
<script src="../plugins/bootstrap-datetimepicker/es.js"></script>
<!-- input-mask -->
<script src="../plugins/input-mask/jquery.inputmask.js"></script>
<script src="../plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="../plugins/input-mask/jquery.inputmask.numeric.extensions.js"></script>
<script src="../plugins/input-mask/jquery.inputmask.phone.extensions.js"></script>
<script src="../plugins/input-mask/jquery.inputmask.regex.extensions.js"></script>
<script src="../plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script type="text/javascript">
/* MASK */
$('.maskInputDT').inputmask({
  mask: "1-2-y h:s t\\m",
  //alias: "dd-mm-yyyy",
  alias: "datetime",
  placeholder: "dd-mm-yyyy hh:mm:ss xm",
  separator: '-',
  hourFormat: "12"

})
</script>
<script type="text/javascript">
$(function() {
// ------------------ CONFIGURACION DEL PRIMER TURNO ------------------ //
  $('#datatime_t1_inicio_capbodega').datetimepicker({
    format: 'DD-MM-YYYY hh:mm a',
    locale: 'es',
    allowInputToggle: true,
    useCurrent: true
  }).on('dp.change', function(e) {

   // Adding two years so I can set the Min Date
    var tt = moment(new Date(e.date));
    tt.add(2, 'years');
    tt.add(23, 'hours');
    $('#datatime_t1_fin_capbodega').data('DateTimePicker').maxDate(tt);

    // Set the Min date
    var min = moment(new Date(e.date));
    min.add(1, 'hours');
    $('#datatime_t1_fin_capbodega').data('DateTimePicker').minDate(min);

    //Set the Max Date
    var m = moment(new Date(e.date));
    m.add(1, 'days');
    m.add(23, 'hours');
    //m.add(59, 'minutes');
    //m.add(59, 'seconds');
    $('#datatime_t1_fin_capbodega').data('DateTimePicker').maxDate(m);

    // Set End Date
    var temp = moment(new Date(e.date));
    temp.add(1, 'hours');
    //temp.add(59, 'minutes');
    //temp.add(59, 'seconds');

    $('#datatime_t1_fin_capbodega').data('DateTimePicker').date(temp);
    $('#t1_inicio_capbodega').val(moment(e.date).format('DD-MM-YYYY hh:mm a'));
  });

  $('#datatime_t1_fin_capbodega').datetimepicker({
    format: 'DD-MM-YYYY hh:mm a',
    locale: 'es',
    allowInputToggle: true,
    useCurrent: false
  }).on('dp.change', function(e) {
    $('#t1_fin_capbodega').val(moment(e.date).format('DD-MM-YYYY hh:mm a'));
  });
// ------------------ CONFIGURACION DEL SEGUNDO TURNO ------------------ //
  $('#datatime_t2_inicio_capbodega').datetimepicker({
    format: 'DD-MM-YYYY hh:mm a',
    locale: 'es',
    allowInputToggle: true,
    useCurrent: true
  }).on('dp.change', function(e) {

   // Adding two years so I can set the Min Date
    var tt = moment(new Date(e.date));
    tt.add(2, 'years');
    tt.add(23, 'hours');
    $('#datatime_t2_fin_capbodega').data('DateTimePicker').maxDate(tt);

    // Set the Min date
    var min = moment(new Date(e.date));
    min.add(1, 'hours');
    $('#datatime_t2_fin_capbodega').data('DateTimePicker').minDate(min);

    //Set the Max Date
    var m = moment(new Date(e.date));
    m.add(1, 'days');
    m.add(23, 'hours');
    //m.add(59, 'minutes');
    //m.add(59, 'seconds');
    $('#datatime_t2_fin_capbodega').data('DateTimePicker').maxDate(m);

    // Set End Date
    var temp = moment(new Date(e.date));
    temp.add(1, 'hours');
    //temp.add(59, 'minutes');
    //temp.add(59, 'seconds');

    $('#datatime_t2_fin_capbodega').data('DateTimePicker').date(temp);
    $('#t2_inicio_capbodega').val(moment(e.date).format('DD-MM-YYYY hh:mm a'));
  });

  $('#datatime_t2_fin_capbodega').datetimepicker({
    format: 'DD-MM-YYYY hh:mm a',
    locale: 'es',
    allowInputToggle: true,
    useCurrent: false
  }).on('dp.change', function(e) {
    $('#t2_fin_capbodega').val(moment(e.date).format('DD-MM-YYYY hh:mm a'));
  });

});
</script>

<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<script>
Highcharts.chart('grafica_turno1', {

  credits: {
        enabled: false,
        text: 'argoalmacenadora.com',
        href: 'http://www.argoalmacenadora.com.mx'
    },
    chart: {
        zoomType: 'xy'
    },
    title: {
        text: 'LÍMITE DE CAPACIDAD Y TONELADAS REGISTRADAS EN TURNO'
    },
    xAxis: [{
        categories: ['ATOYAQUILLO', 'KENWORTH', 'PEÑUELA', 'ARGO FRAILE A','ARGO OCABA'],
        crosshair: true
    }],
    yAxis: [{ // Primary yAxis
        labels: {
            format: '{value} Ton',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        title: {
            text: 'Total Toneladas',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        }
    }],
    tooltip: {
        shared: true
    },
    legend: {
        layout: 'vertical',
        align: 'left',
        x: 120,
        verticalAlign: 'top',
        y: 100,
        floating: true,
        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    series: [{
        name: 'Total Toneladas',
        type: 'column',
        //yAxis: 1,
        data: [<?= $t1_sumarray_atoyaquillo.",".$t1_sumarray_kenworth.",".$t1_sumarray_penuela.",".$t1_sumarray_fraile_a.",".$t1_sumarray_ocaba ?> ],
        tooltip: {
            valueSuffix: ' Ton'
        }

    }, {
        name: 'Límite de capacidad',
        type: 'spline',
        data: [ <?= $t1_limite_atoyaquillo.",".$t1_limite_kenworth.",".$t1_limite_penuela.",".$t1_limite_fraile_a.",".$t1_limite_ocaba?> ],
        color: '#E98B7F',
        tooltip: {
            valueSuffix: 'Ton'
        }
    }]
});
</script>
<script>
Highcharts.chart('grafica_turno2', {

  credits: {
        enabled: false,
        text: 'argoalmacenadora.com',
        href: 'http://www.argoalmacenadora.com.mx'
    },
    chart: {
        zoomType: 'xy'
    },
    title: {
        text: 'LÍMITE DE CAPACIDAD Y TONELADAS REGISTRADAS EN TURNO'
    },
    xAxis: [{
        categories: ['ATOYAQUILLO', 'KENWORTH', 'PEÑUELA','ARGO FRAILE A','ARGO OCABA'],
        crosshair: true
    }],
    yAxis: [{ // Primary yAxis
        labels: {
            format: '{value} Ton',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        title: {
            text: 'Total Toneladas',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        }
    }],
    tooltip: {
        shared: true
    },
    legend: {
        layout: 'vertical',
        align: 'left',
        x: 120,
        verticalAlign: 'top',
        y: 100,
        floating: true,
        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    series: [{
        name: 'Total Toneladas',
        type: 'column',
        //yAxis: 1,
        data: [<?= $t2_sumarray_atoyaquillo.",".$t2_sumarray_kenworth.",".$t2_sumarray_penuela.",".$t2_sumarray_fraile_a.",".$t2_sumarray_ocaba ?> ],
        tooltip: {
            valueSuffix: ' Ton'
        }

    }, {
        name: 'Límite de capacidad',
        type: 'spline',
        data: [ <?= $t2_limite_atoyaquillo.",".$t2_limite_kenworth.",".$t2_limite_penuela.",".$t2_limite_fraile_a.",".$t2_limite_ocaba ?> ],
        color: '#E98B7F',
        tooltip: {
            valueSuffix: 'Ton'
        }
    }]
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
