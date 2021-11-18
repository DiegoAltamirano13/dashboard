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
$_SESSION['modulo_actual'] = 15;//MODULO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], $_SESSION['modulo_actual'] );
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/*----------------------SESIONES PARA SALDOS DE CLIENTES----------------------*/
/*======GUARDA EL VALOR PARA FILTRO DIAS DE SALDO EN UNA SESSION======*/
if ($_SESSION['saldo_dias'] == false){
  $_SESSION['saldo_dias'] = 4;
  $saldo_dias = $_SESSION['saldo_dias'];
}else{
  if(isset($_POST['saldo_dias']))
  $_SESSION['saldo_dias'] = $_POST['saldo_dias'];
  $saldo_dias = $_SESSION['saldo_dias'];
}
/*======GUARDA EL VALOR PARA PLAZA SELECCIONADA======*/
if(isset($_POST["saldo_cliente_plaza"]))
  $_SESSION["saldo_cliente_plaza"] = $_POST["saldo_cliente_plaza"];
$saldo_cliente_plaza = $_SESSION["saldo_cliente_plaza"];
/*======GUARDA EL VALOR PARA PLAZA SELECCIONADA======*/
if(isset($_POST["saldo_cliente_resumen"]))
  $_SESSION["saldo_cliente_resumen"] = $_POST["saldo_cliente_resumen"];
$saldo_cliente_resumen = $_SESSION["saldo_cliente_resumen"];

//////////////// INICIA INSTANCIAS ///////////////////////////
include_once '../class/Saldo_cliente.php';
$ins_obj_saldos_clientes = new Saldos_clientes($saldo_dias,$saldo_cliente_plaza);
$saldos_plaza = $ins_obj_saldos_clientes->saldos_plaza($saldo_cliente_plaza);
$widgets_saldos_clientes = $ins_obj_saldos_clientes->widgets_saldos_clientes();
$widgets_saldos = $ins_obj_saldos_clientes->widgets_saldos();
$widgets_v_mercancia = $ins_obj_saldos_clientes->widgets_v_mercancia();
$tabla_saldos_clientes = $ins_obj_saldos_clientes->tabla_saldos_clientes($saldo_cliente_plaza,$importe_filtro_resumen,true);

/*----------------------ACTIVA INTRO AL INICIAR----------------------*/
if(isset($_POST["activa_intro_sal_clie"]))
$_SESSION["activa_intro_sal_clie"] = $_POST["activa_intro_sal_clie"];
$activa_intro_sal_clie = $_SESSION["activa_intro_sal_clie"];

?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">


<!-- ########################################## Incia Contenido de la pagina ########################################## -->

 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->



<div align="center" class="row">
  <table>
  <tr>
  <?php if ( $saldo_cliente_plaza == true || $saldo_cliente_resumen == true ){ ?>
    <th data-intro="Botón para regresar a la gráfica principal">
    <form method="post" action="saldos_clientes.php">
    <input type="hidden" name="saldo_cliente_resumen" value="">
    <div class="step1"></div><button type="submit" name="saldo_cliente_plaza" value="" class="click_modal_cargando btn btn-xs bg-primary click_car_btn_reg"><i class="fa fa-reply"></i> Regresar</button>
    </form>
    </th>
  <?php } ?>
    <th>
    <h4><i class="fa fa-fw fa-money"></i> CLIENTES CON ADEUDO:</h4>
    </th>
    <th data-intro="Botón para filtrar los días de adeudo">
    <form method="post">
    <select onchange="this.form.submit();"  name="saldo_dias" class="select_plaza_grafica form-control">
      <?php
      switch ($saldo_dias) {
        case 1:
          echo '<option value="1">DE 1 A 30 DÍAS</option>';
          $titulo_dias = "DE 1 A 30 DÍAS";
          break;
        case 2:
          echo '<option value="2">DE 31 A 60 DÍAS</option>';
          $titulo_dias = "DE 31 A 60 DÍAS";
          break;
        case 3:
          echo '<option value="3">DE 61 A 90 DÍAS</option>';
          $titulo_dias = "DE 61 A 90 DÍAS";
          break;
        default:
          echo '<option value="4">MAS DE 90 DÍAS</option>';
          $titulo_dias = "MAS DE 90 DÍAS";
          break;
      }
      ?>
      <?php if ($saldo_dias <> 1){?><option value="1">DE 1 A 30 DÍAS</option><?php } ?>
      <?php if ($saldo_dias <> 2){?><option value="2">DE 31 A 60 DÍAS</option><?php } ?>
      <?php if ($saldo_dias <> 3){?><option value="3">DE 61 A 90 DÍAS</option><?php } ?>
      <?php if ($saldo_dias <> 4){?><option value="4">MAS DE 91 DÍAS</option><?php } ?>
    </select>
  </form>
  </th>
  </tr>
  </table><br>
</div>

<?php if($saldo_cliente_resumen == true){ ?>
<!-- ############################ INICIA SECCION RESUMEN GENERAL CLIENTE CON SALDOS ############################# -->
<?php
if ($_SESSION['importe_filtro_resumen'] == false){
  $_SESSION['importe_filtro_resumen'] = 50000;
  $importe_filtro_resumen = $_SESSION['importe_filtro_resumen'];
}else{
  if(isset($_POST['importe_filtro_resumen']))
  $_SESSION['importe_filtro_resumen'] = $_POST['importe_filtro_resumen'];
  $importe_filtro_resumen = $_SESSION['importe_filtro_resumen'];
}
?>
<!-- Main content -->
<section class="content">
  <div class="row">

<!-- INICIA SECCION IZQUIERDA -->
  <div class="col-md-7">

    <!-- INICIA SECCION TABLA RESUMEN SALDO CLIENTES -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-pie-chart"></i> RESUMEN GENERAL SALDO TOTAL DE CLIENTES <?= $titulo_dias ?> </h3>

        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
      </div>
      <div class="box-body"><!-- box-body -->

      <?php
      if ( $saldo_cliente_plaza == false ) {
         echo "string";
      }
      ?>
      <form method="post" action="saldos_clientes.php">
        <div class="input-group input-group-sm">
            <div class="input-group-addon">
              <i class="ion-social-usd"></i> Clientes con saldo mayor a:
            </div>
          <div data-intro="Introduce una cantidad para ver los clientes con adeudo mayor a lo que especifiques"><input type="number" value="<?=$importe_filtro_resumen?>" name="importe_filtro_resumen" min="0" step="0.01" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency"></input></div>
          <span class="input-group-btn">
            <button type="submit" class="click_modal_cargando btn btn-success btn-flat">OK</button>
          </span>
        </div>
      </form>
      <hr>
      <div class="table-responsive"><!-- table-responsive -->
        <table class="table no-margin table-hover tabla_res_gen">
          <thead>
          <tr>
            <th class="small">CLIENTES</th>
            <th class="small">SALDO <?=$titulo_dias?></th>
            <th class="small">SALDO POR PLAZA</th>
            <th class="small">%</th>
          </tr>
          </thead>
          <tfoot>
            <tr>
              <th class="small">SALDO CLIENTES REPRESENTATIVOS</th>
              <th class="small"><p id="saldo_cliente_repre"></p></th>
              <th class="small"></th>
              <th class="small"><p id="saldo_cliente_repre_por"></p></th>
            </tr>
            <tr>
              <th class="small">SALDO OTROS CLIENTES</th>
              <th class="small"><p id="saldo_otros_clientes"></p></th>
              <th class="small"></th>
              <th class="small"><p id="saldo_otros_clientes_por"></p></th>
            </tr>
            <tr>
              <th class="small text-right">SALDO TOTAL</th>
              <?php
              for ($i=0; $i < count($widgets_saldos) ; $i++) {
                if (is_null($widgets_saldos[$i]["MONTO"])){
                   $saldo_total_array[$i] = $widgets_saldos[$i]["SALDO_MONTO"];
                   echo '<th class="small">$'.number_format($widgets_saldos[$i]["SALDO_MONTO"],2).'</th>';
                }else{
                   $saldo_total_array[$i] = $widgets_saldos[$i]["MONTO"];
                   echo '<th class="small">$'.number_format($widgets_saldos[$i]["MONTO"],2).'</th>';
                }
              }?>
              <th class="small"></th>
              <th class="small">100%</th>
            </tr>
          </tfoot>
          <tbody>
          <?php
          $tabla_saldos_clientes_resumen = $ins_obj_saldos_clientes->tabla_saldos_clientes($saldo_cliente_plaza,$importe_filtro_resumen,false);
          for ($i=0; $i <count($tabla_saldos_clientes_resumen) ; $i++) {

          $saldo_total = array_sum($saldo_total_array);

          ?>
          <tr>
            <td class="small"><?="<span class='text-muted'>(".$tabla_saldos_clientes_resumen[$i]["ID_CLIENTE"].")</span> ".$tabla_saldos_clientes_resumen[$i]["CLIENTE"]?></td>
            <!-- INICIA CODE SALDO DE CLIENTES EN RESUMEN -->
            <td class="small">
            <?php
            if (is_null($tabla_saldos_clientes_resumen[$i]["MONTO"])){
              echo "$".number_format($tabla_saldos_clientes_resumen[$i]["SALDO"],2);
              $total_importe_resumen[$i] = $tabla_saldos_clientes_resumen[$i]["SALDO"];
              $saldo_cliente_por = (($tabla_saldos_clientes_resumen[$i]["SALDO"]/$saldo_total)*100);
            }else{
              echo "$".number_format($tabla_saldos_clientes_resumen[$i]["MONTO"],2);
              $total_importe_resumen[$i] = $tabla_saldos_clientes_resumen[$i]["MONTO"];
              $saldo_cliente_por = (($tabla_saldos_clientes_resumen[$i]["MONTO"]/$saldo_total)*100);
            }

            ?>
            <a class="fancybox fancybox.iframe" href="saldo_cliente_det.php?cliente=<?=$tabla_saldos_clientes_resumen[$i]["ID_CLIENTE"]?>&filtro=<?=$saldo_dias?>" ><sup><span class="label bg-green">Ver<i class="fa fa-fw fa-plus"></i></span></sup></a>
            </td>
            <!-- TERMINA CODE SALDO DE CLIENTES EN RESUMEN -->
            <td class="small ">
              <div class="label label-info btn click_res_ajax<?=$i?>" id="clickme<?=$i?>"><i class="ion-android-add-circle"></i> MOSTRAR</div>
              <!-- INICIA DIV RESPUESTA AJAX -->

              <div class="div_res_ajax<?=$i?>" style="display:none">
                <div id="resultado<?=$i?>"></div>
              </div>
              <!-- TERMIN DIV RESPUESTA AJAX -->
            </td>
            <td><span class="label label-warning"><?= round($saldo_cliente_por)."%"?></span></td>
          </tr>
          <?php
          }
          $saldo_total = array_sum($saldo_total_array);
          $saldo_cliente_repre = array_sum($total_importe_resumen);
          $saldo_otros_clientes = $saldo_total-$saldo_cliente_repre;
          $saldo_cliente_repre_por = (($saldo_cliente_repre/$saldo_total)*100);
          $saldo_otros_clientes_por = (($saldo_otros_clientes/$saldo_total)*100);
          ?>
            <script>
            document.getElementById("saldo_cliente_repre").innerHTML = "<?= "$".number_format($saldo_cliente_repre,2)?>";
            document.getElementById("saldo_otros_clientes").innerHTML = "<?= "$".number_format($saldo_otros_clientes,2) ?>";
            document.getElementById("saldo_cliente_repre_por").innerHTML = "<?= round($saldo_cliente_repre_por).'%' ?>";
            document.getElementById("saldo_otros_clientes_por").innerHTML = "<?= round($saldo_otros_clientes_por).'%' ?>";
            </script>
          </tbody>
        </table>

      </div><!-- /.table-responsive -->
      </div><!-- /.box-body -->
    </div>
    <!-- TERMINA SECCION TABLA RESUMEN SALDO CLIENTES -->

  </div>
<!-- TERMINA SECCION IZQUIERDA-->


<!-- INICIA SECCION DERECHA -->
  <div class="col-md-5">

    <!-- INICIA GRAFICA RESUMEN GENERAL  -->
    <div class="box box-info">
      <div class="box-header with-border">
        <!-- <h3 class="box-title">Grafica</h3> -->
        <div class="box-tools pull-right">
          <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
        </div>
      </div>
      <div class="box-body"><!-- box-body -->
        <div id="grafica_resumen_saldo" style="min-width: 210px; height: 400px; max-width: 600px; margin: 0 auto"></div>
    <!-- /*/*/*/*/*/*/*/*/ INICIA WIDGETS RESUMEN GENERAL /*/*/*/*/*/*/*/*/ -->
        <div class="col-md-6 col-sm-12 col-xs-12">
          <div style="background-color:#39CCCC" class="info-box">
            <span class="info-box-number text-center" style="color:#FFFFFF">
                <?= count($tabla_saldos_clientes_resumen) ?>
            </span>
            <p class="text-center" style="color:#FFFFFF">Clientes con saldo mayor a <?= '$'.number_format($importe_filtro_resumen,2)?></p>
          </div>
        </div>

        <div class="col-md-6 col-sm-12 col-xs-12">
          <div style="background-color:#DD4B39" class="info-box">
            <span class="info-box-number text-center" style="color:#ffffff">
                <?= '$'.number_format($saldo_total,2)?>
            </span>
            <p class="text-center" style="color:#ffffff">Saldo Total <?=strtolower($titulo_dias)?></p>
          </div>
        </div>

        <div class="col-md-6 col-sm-12 col-xs-12">
          <div style="background-color:#D81B60" class="info-box">
            <span class="info-box-number text-center " style="color:#ffffff">
                <?= '$'.number_format($saldo_cliente_repre,2) ?>
            </span>
            <p class="text-center" style="color:#ffffff">Saldo clientes representativos <?=strtolower($titulo_dias)?></p>
          </div>
        </div>

        <div class="col-md-6 col-sm-12 col-xs-12">
          <div style="background-color:#FF851B" class="info-box">
            <span class="info-box-number text-center " style="color:#ffffff">
                <?= '$'.number_format($saldo_otros_clientes,2) ?>
            </span>
            <p class="text-center" style="color:#ffffff">Saldo otros clientes <?=strtolower($titulo_dias)?></p>
          </div>
        </div>
    <!-- /*/*/*/*/*/*/*/*/ INICIA WIDGETS RESUMEN GENERAL /*/*/*/*/*/*/*/*/ -->

      </div><!-- /.box-body -->
    </div>
    <!-- TERMINA GRAFICA RESUMEN GENERAL  -->



  </div>
<!-- TERMINA SECCION DERECHA-->
  </div>
  <!-- /.row -->

</section>
<!-- /.content -->

<!-- ########################### TERMINA SECCION RESUMEN GENERAL CLIENTE CON SALDOS ########################### -->
<?php } ?>


<?php if ($saldo_cliente_plaza == false && $saldo_cliente_resumen == false){ ?>
<!-- ######################################## Inicio de Widgets ######################################### -->
    <section><!-- Inicia la seccion de los Widgets -->
      <div class="row">
      <!-- WIDGETS PLAZAS CON SALDO -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?= count($saldos_plaza) ?></h3>
              <p>PLAZAS CON SALDO</p>
            </div>
            <div class="icon">
              <i class="fa fa-cubes"></i>
            </div>
             <form method="post" action="saldos_clientes.php"><button type="submit" name="saldo_cliente_resumen" value="1" class="click_modal_cargando btn bg-aqua-active btn-block" data-intro="Botón para ver el resumen general de clientes con adeudo">Resumen <i class="fa fa-arrow-circle-right"></i></button></form>
          </div>
        </div>
        <!-- WIDGETS CLIENTES CON SALDO -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-purple">
            <div class="inner">
              <h3>
              <?php
              for ($i=0; $i < count($widgets_saldos_clientes) ; $i++) {
              echo $widgets_saldos_clientes[$i]["CLIENTES_SALDO"];
              } ?>
              </h3>
              <p>CLIENTES CON SALDO</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
             <button type="submit" name="tipo" id="tipo" value="2" class="btn bg-purple-active btn-block"><br><!-- Ver <i class="fa fa-arrow-circle-right"></i> --></button>
          </div>
        </div>
        <!-- WIDGETS SALDO -->
        <div class="col-md-4 col-sm-12 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>
              <?php
              for ($i=0; $i < count($widgets_saldos) ; $i++) {
                if (is_null($widgets_saldos[$i]["MONTO"])){
                  echo "$".number_format($widgets_saldos[$i]["SALDO_MONTO"],2);
                }else{
                  echo "$".number_format($widgets_saldos[$i]["MONTO"],2);
                }
              } ?>
              </h3>
              <p>SALDO</p>
            </div>
            <div class="icon">
              <i class="ion-cash"></i>
            </div>
            <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-red-active btn-block"><br><!-- Ver <i class="fa fa-arrow-circle-right"></i> --></button>
          </div>
        </div>
        <!-- WIDGETS VALOR DE MERCANCÍA -->
        <!-- <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="small-box bg-purple">
            <div class="inner">
              <h3>
              </h3>
              <p>SALDO NO VENCIDO</p>
            </div>
            <div class="icon">
              <i class="fa fa-calendar-minus-o"></i>
            </div>
              <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-purple-active btn-block"><br></button>
          </div>
        </div> -->
        <!-- TERMINO WIDGETS VALOR DE MERCANCÍA -->
      </div>
      <!-- /.row -->
      </section><!-- Termina la seccion de los Widgets -->
<!-- ######################################### Termino de Widgets ######################################### -->



<!-- ############################ INICIA SECCION DE LA GRAFICA OTROS POR PLAZA ############################# -->
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">  PLAZA CON SALDO <?=$titulo_dias?> </h3>
      <div class="box-tools pull-right">
        <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
      </div>
    </div>
    <div class="box-body"><!--box-body-->

    <div class="row"><!-- row -->

      <div class="col-md-8"><!-- col-md-9 -->
        <div id="grafica_pastel_saldo_clientes" class="" style="height: 350px;"></div>
      </div><!-- ./col-md-9 -->

      <div class="col-md-4"><!-- col-md-3 -->
        <div class="table-responsive">
                <table class="table no-margin   compact table-hover table-striped table-bordered">
                  <thead>
                  <tr>
                    <th data-intro="Seleccione una plaza para ver los clientes con deuda">PLAZA</th>
                    <th>SALDO <?=$titulo_dias?></th>
                  </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th align="right">TOTAL</th>
                      <th><p id="saldos_plaza"></p></th>
                    </tr>
                  </tfoot>
                  <tbody>
                  <?php
                  for ($i=0; $i < count($saldos_plaza); $i++) {
                    $plaza = $saldos_plaza[$i]["PLAZA"];
                    $separador  = ' ';
                    $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA
                  ?>
                  <tr>
                    <td><form method="post" action="#tabla"><button type="submit" class="click_modal_cargando btn btn-link btn-xs" name="saldo_cliente_plaza" value="<?=$saldos_plaza[$i]["PLAZA"]?>"><?= $plaza_corta ?></button></form></td>
                    <?php
                    // |-|-|-|-|-|-|-|-|-|-|-|-|-|-| INICIA CODE SALDO |-|-|-|-|-|-|-|-|-|-|-|-|-|-| //
                    if (is_null($saldos_plaza[$i]["MONTO"])){
                      echo '<td>$'.number_format($saldos_plaza[$i]["SALDO"],2).'  <a class="fancybox fancybox.iframe" href="saldo_cliente_det.php?plaza='.$saldos_plaza[$i]["PLAZA"].'&filtro='.$saldo_dias.'"><sup><span class="label bg-green">Ver<i class="fa fa-fw fa-plus"></i></span></sup></a></td>';
                      $monto_plazas[$i] = $saldos_plaza[$i]["SALDO"];
                    }else{
                      echo '<td>$'.number_format($saldos_plaza[$i]["MONTO"],2).'  <a class="fancybox fancybox.iframe" href="saldo_cliente_det.php?plaza='.$saldos_plaza[$i]["PLAZA"].'&filtro='.$saldo_dias.'"><sup><span class="label bg-green">Ver<i class="fa fa-fw fa-plus"></i></span></sup></a></td>';
                      $monto_plazas[$i] = $saldos_plaza[$i]["MONTO"];
                    }
                    // |-|-|-|-|-|-|-|-|-|-|-|-|-|-| TERMINA CODE SALDO |-|-|-|-|-|-|-|-|-|-|-|-|-|-| //
                    ?>
                  </tr>
                  <?php } ?>
                  </tbody>
                </table>
              </div>
              <script>
                document.getElementById("saldos_plaza").innerHTML = "<?= "$".number_format(array_sum($monto_plazas),2)?>";
              </script>
              <!-- /.table-responsive -->
      </div><!-- ./col-md-3 -->

    </div><!-- ./row -->

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION DE LA GRAFICA OTROS POR PLAZA ########################### -->
<?php } ?>

<?php if ($saldo_cliente_plaza == true){ ?>
<!-- ############################ INICIA SECCION PLAZA CÓRDOBA ############################# -->
<section id="tabla">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title"> <i class="fa fa-pie-chart"></i> PLAZA <?= $saldo_cliente_plaza ?> </h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->

    <!-- ######################################## Inicio de Widgets ######################################### -->
    <section><!-- Inicia la seccion de los Widgets -->
      <div class="row">
        <!-- WIDGETS CLIENTES CON SALDO -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-purple">
            <div class="inner">
              <h3><?= count($tabla_saldos_clientes) ?></h3>
              <p>CLIENTES CON SALDO</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
             <button type="submit" name="tipo" id="tipo" value="2" class="btn bg-purple-active btn-block"><br><!-- Ver <i class="fa fa-arrow-circle-right"></i> --></button>
          </div>
        </div>
        <!-- WIDGETS SALDO -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>
              <?php
              for ($i=0; $i < count($widgets_saldos) ; $i++) {
                if (is_null($widgets_saldos[$i]["MONTO"])){
                  echo "$".number_format($widgets_saldos[$i]["SALDO_MONTO"],2);
                }else{
                  echo "$".number_format($widgets_saldos[$i]["MONTO"],2);
                }

              } ?>
              </h3>
              <p>SALDO</p>
            </div>
            <div class="icon">
              <i class="ion-cash"></i>
            </div>
            <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-red-active btn-block"><br><!-- Ver <i class="fa fa-arrow-circle-right"></i> --></button>
          </div>
        </div>
        <!-- WIDGETS VALOR DE MERCANCÍA -->
        <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="small-box bg-green">
            <div class="inner">
              <h3 id="mercancia_cliente_tabla"></h3>
              <p>VALOR DE MERCANCÍA</p>
            </div>
            <div class="icon">
              <i class="ion-social-usd"></i>
            </div>
              <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-green-active btn-block"><br></button>
          </div>
        </div>
        <!-- TERMINO WIDGETS VALOR DE MERCANCÍA -->
      </div>
      <!-- /.row -->
      </section><!-- Termina la seccion de los Widgets -->
<!-- ######################################### Termino de Widgets ######################################### -->

      <h4 class="content-header text-blue text-center"><i class="fa fa-users"></i> CLIENTES CON SALDO <?=$titulo_dias?> PLAZA <?= $saldo_cliente_plaza ?></h4><hr>

      <div class="table-responsive"><!-- table-responsive -->
        <table class="tabla_saldo_clientes table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small">CLIENTE</th>
            <th class="small step2">IMPORTE</th>
            <th class="small">VALOR MERCANCIA</th>
            <th class="small step6">TIPO DE MERCANCIA</th>
            <th class="small">PROMOTOR</th>
            <th class="small">REGIMEN</th>
            <th class="small">FECHA DE DEPOSITO</th>
            <th class="small">IMPUESTOS</th>
            <?php if($saldo_dias == 4){?>
            <th class="small">STATUS REMATE</th>
            <?php } ?>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th class="text-right">TOTAL POR PLAZA</th>
            <th><p id="saldo_cliente"></p></th>
            <th><p id="mercancia_cliente_widgets"></p></th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
        <tbody>
        <?php for ($i=0; $i < count($tabla_saldos_clientes) ; $i++) { ?>
          <tr>
            <td class="small"><?= "(".$tabla_saldos_clientes[$i]["ID_CLIENTE"].")".$tabla_saldos_clientes[$i]["CLIENTE"] ?></td>
            <!-- ////////// INICIA CODE PARA PONER SALDO EN PESOS ////////// -->
            <?php
            if (is_null($tabla_saldos_clientes[$i]["MONTO"])){
              echo '<td class="small">$'.number_format($tabla_saldos_clientes[$i]["SALDO"],2).' <a class="fancybox fancybox.iframe" href="saldo_cliente_det.php?plaza='.$tabla_saldos_clientes[$i]["PLAZA"].'&cliente='.$tabla_saldos_clientes[$i]["ID_CLIENTE"].'&filtro='.$saldo_dias.'"><sup><span class="label bg-green">Ver<i class="fa fa-fw fa-plus"></i></span></sup></a></td>';
              $saldo_cliente[$i] = $tabla_saldos_clientes[$i]["SALDO"];
            }else{
              echo '<td class="small">$'.number_format($tabla_saldos_clientes[$i]["MONTO"],2).' <a class="fancybox fancybox.iframe" href="saldo_cliente_det.php?plaza='.$tabla_saldos_clientes[$i]["PLAZA"].'&cliente='.$tabla_saldos_clientes[$i]["ID_CLIENTE"].'&filtro='.$saldo_dias.'"><sup><span class="label bg-green">Ver<i class="fa fa-fw fa-plus"></i></span></sup></a></td>';
              $saldo_cliente[$i] = $tabla_saldos_clientes[$i]["MONTO"];
            }
            ?>
            <!-- ////////// TERMINA CODE PARA PONER SALDO EN PESOS ////////// -->
            <!-- ////////// INICIA CODE PARA BUSCAR VALOR DE MERCANCIA ////////// -->
            <?php
            $valor_mercancia = $ins_obj_saldos_clientes->valor_mercancia($tabla_saldos_clientes[$i]["ID_PLAZA"],$tabla_saldos_clientes[$i]["ID_CLIENTE"]);
            $mercancia_cliente[$i] = $valor_mercancia;
              echo '<td class="small">$'.number_format($valor_mercancia,2).'</td>';
            ?>
            <!-- ////////// TERMINA CODE PARA BUSCAR VALOR DE MERCANCIA ////////// -->
            <!-- ////////// INICIA CODE PARA BUSCAR TIPO DE MERCANCIA ////////// -->
            <td class="small">
            <?php
            // INICIA CODE BUSCA EN AD_FA_FACTURA
            $ad_fa_factura = $ins_obj_saldos_clientes->ad_fa_factura($tabla_saldos_clientes[$i]["ID_PLAZA"],$tabla_saldos_clientes[$i]["ID_CLIENTE"]);
            for ($j=0; $j <count($ad_fa_factura) ; $j++) {
              $ad_fa_factura_id_folio[$i][$j] = implode($ad_fa_factura[$j]);
              //echo "0,".$ad_fa_factura_id_folio;
            }
            $iid_folio = implode(",",$ad_fa_factura_id_folio[$i]);
            // TERMINA CODE BUSCA EN AD_FA_FACTURA

            // INICIA CODE BUSCA EN AD_FA_CER_FACTURA Y SACAR FOLIOS N
            $ad_fa_cer_factura_n = $ins_obj_saldos_clientes->ad_fa_cer_factura_n($tabla_saldos_clientes[$i]["ID_PLAZA"],$iid_folio);
            for ($k=0; $k <count($ad_fa_cer_factura_n) ; $k++) {
              $ad_fa_cer_factura_num_cd_n[$i][$k] = implode($ad_fa_cer_factura_n[$k]);
            }
            $num_cd_n = implode(",",$ad_fa_cer_factura_num_cd_n[$i]);
            //echo "NUM_CD_N= ".$num_cd_n."<br>";
            // TERMINA CODE BUSCA EN AD_FA_CER_FACTURA Y SACAR FOLIOS N
            // INICIA CODE BUSCA EN AD_FA_CER_FACTURA Y SACAR FOLIOS S
            $ad_fa_cer_factura_s = $ins_obj_saldos_clientes->ad_fa_cer_factura_s($tabla_saldos_clientes[$i]["ID_PLAZA"],$iid_folio);
            for ($k=0; $k <count($ad_fa_cer_factura_s) ; $k++) {
              $ad_fa_cer_factura_num_cd_s[$i][$k] = implode($ad_fa_cer_factura_s[$k]);
            }
            $num_cd_s = implode(",",$ad_fa_cer_factura_num_cd_s[$i]);
            //echo "NUM_CD_S= ".$num_cd_s."<br>";
            // TERMINA CODE BUSCA EN AD_FA_CER_FACTURA Y SACAR FOLIOS S

            // INICIA CODE BUSCA EN AD_CE_CERT_N_DET Y SACAR TIPO DE MERCANCIA N
            $ad_ce_cert_n_det = $ins_obj_saldos_clientes->ad_ce_cert_n_det($num_cd_n);
            for ($k=0; $k <count($ad_ce_cert_n_det) ; $k++) {
              $ad_ce_cert_n_det_mer[$i][$k] = implode($ad_ce_cert_n_det[$k]);
            }
            $mercancia_n = implode(",",$ad_ce_cert_n_det_mer[$i]);
            //echo "NUM_CD_N= ".$mercancia_n."<br>";
            // TERMINA CODE BUSCA EN AD_CE_CERT_N_DET Y SACAR TIPO DE MERCANCIA N
            // INICIA CODE BUSCA EN AD_CE_CERT_S_DET Y SACAR TIPO DE MERCANCIA S
            $ad_ce_cert_s_det = $ins_obj_saldos_clientes->ad_ce_cert_s_det($num_cd_s);
            for ($k=0; $k <count($ad_ce_cert_s_det) ; $k++) {
              $ad_ce_cert_s_det_mer[$i][$k] = implode($ad_ce_cert_s_det[$k]);
            }
            $mercancia_s = implode(",",$ad_ce_cert_s_det_mer[$i]);
            //echo "NUM_CD_S= ".$mercancia_s."<br>";
            $tipo_mercancia = $mercancia_n." ".$mercancia_s;
              echo substr($tipo_mercancia, 0,50).'<sup><span class="btn btn-xs label bg-teal" data-toggle="modal" data-target="#modal_t_mercancia'.$i.'">MÁS</span></sup></sup>';
            // TERMINA CODE BUSCA EN AD_CE_CERT_S_DET Y SACAR TIPO DE MERCANCIA S
            ?>
            <!-- INICIA MODAL MAS DETALLES TIPO DE MERCANCIA -->
            <div class="modal fade" id="modal_t_mercancia<?=$i?>" role="dialog">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= "(".$tabla_saldos_clientes[$i]["ID_CLIENTE"].")".$tabla_saldos_clientes[$i]["CLIENTE"] ?></h4>
                  </div>
                  <div class="modal-body">

                    <div class="box box-solid">
                      <div class="box-header with-border">
                        <i class="fa fa-archive"></i><h4 class="box-title">TIPO DE MERCANCIA</h4>
                      </div>
                      <!-- /.box-header -->
                      <div class="box-body">
                      <?php
                      echo "<li>".implode("<li>",$ad_ce_cert_n_det_mer[$i])."</li>";
                      echo "<li>".implode("<li>",$ad_ce_cert_s_det_mer[$i])."</li>";
                      //echo implode("<li>",$ad_ce_cert_s_det_mer[$i])."</li>";
                      ?>
                      </div>
                      <!-- /.box-body -->
                    </div>


                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- INICIA MODAL MAS DETALLES TIPO DE MERCANCIA -->
            </td>
            <!-- ////////// TERMINA CODE PARA BUSCAR TIPO DE MERCANCIA ////////// -->
            <td class="small"><?= $tabla_saldos_clientes[$i]["NOM_PRO"]." ".$tabla_saldos_clientes[$i]["APE_PAT_PRO"]." ".$tabla_saldos_clientes[$i]["APE_MAT_PRO"] ?></td>
            <!-- ////////// INICIA CODE PARA BUSCAR REGIMEN CLIENTE ////////// -->
            <?php
            switch ($tabla_saldos_clientes[$i]["REGIMEN"]) {
              case 1:
                echo '<td class="small">NACIONAL</td>';
                break;
              case 2:
                echo '<td class="small">FISCAL</td>';
                break;
              default:
                echo '<td class="small">NACIONAL/FISCAL</td>';
                break;
            }

            ?>
            <!-- ////////// TERMINA CODE PARA BUSCAR REGIMEN CLIENTE ////////// -->
            <!-- ////////// INICIA CODE PARA BUSCAR FECHA DE DEPOSITO ////////// -->
            <td class="small">
            <?php
            $op_in_recibo_deposito = $ins_obj_saldos_clientes->op_in_recibo_deposito($tabla_saldos_clientes[$i]["ID_CLIENTE"]);
            for ($j=0; $j < count($op_in_recibo_deposito) ; $j++) {
              echo ''.$op_in_recibo_deposito[$j]["FECHA_INI_CER"].'';
            }
            ?>
            </td>
            <!-- ////////// TERMINA CODE PARA BUSCAR FECHA DE DEPOSITO ////////// -->
            <td class="small">$<?=number_format($tabla_saldos_clientes[$i]["IMPUESTOS"],2)?></td>
            <!-- INICIA CODE PARA DETERMINAR STATUS DE REMATES CLIENTES CON SALDO >90 DIAS -->
            <?php
            if($saldo_dias == 4){

              if (is_null($tabla_saldos_clientes[$i]["STATUS_REMATE"])){
                echo '<td class="small"></td>';
              }else{
                switch ($tabla_saldos_clientes[$i]["STATUS_REMATE"]) {
                case 0:
                  echo '<td class="small">REGISTRADO</td>';
                  break;
                case 1:
                  echo '<td class="small">1RA ALMONEDA</td>';
                  break;
                case 2:
                  echo '<td class="small">2DA ALMONEDA</td>';
                  break;
                case 3:
                  echo '<td class="small">3RA ALMONEDA</td>';
                  break;
                case 4:
                  echo '<td class="small">4TA ALMONEDA</td>';
                  break;
                case 5:
                  echo '<td class="small">5TA ALMONEDA</td>';
                  break;
                case 6:
                  echo '<td class="small">6TA ALMONEDA</td>';
                  break;
                case 7:
                  echo '<td class="small">7MA ALMONEDA</td>';
                  break;
                case 8:
                  echo '<td class="small">8VA ALMONEDA</td>';
                  break;
                case 9:
                  echo '<td class="small">9NA ALMONEDA</td>';
                  break;
                case 10:
                  echo '<td class="small">ADJUDICADO</td>';
                  break;
                default:
                  echo '<td class="small">REGISTRADO</td>';
                  break;
              }
              }

            }
            ?>
            <!-- TERMINA CODE PARA DETERMINAR STATUS DE REMATES CLIENTES CON SALDO >90 DIAS -->
          </tr>
        <?php } ?>
        </tbody>
        </table>
        <script>
        document.getElementById("saldo_cliente").innerHTML = "<?= "$".number_format(array_sum($saldo_cliente),2)?>";
        document.getElementById("mercancia_cliente_tabla").innerHTML = "<?= "$".number_format(array_sum($mercancia_cliente),2)?>";
        document.getElementById("mercancia_cliente_widgets").innerHTML = "<?= "$".number_format(array_sum($mercancia_cliente),2)?>";
        </script>
      </div><!-- ./table-responsive -->

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION PLAZA CÓRDOBA ########################### -->
<?php } ?>

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
<!-- FORMATO NUMERO CON COMAS EN INPUT -->
<script src="http://afarkas.github.io/webshim/js-webshim/minified/polyfiller.js"></script>
<script>
webshims.setOptions('forms-ext', {
    replaceUI: 'auto',
    types: 'number'
});
webshims.polyfill('forms forms-ext');
</script>
<!-- DATALLES PLAZAS SALDO RESUMEN -->
<script>

<?php for ($i=0; $i <count($tabla_saldos_clientes_resumen) ; $i++) { ?>


$( "#clickme<?=$i?>" ).click(function() {
   $(".div_res_ajax<?=$i?>").toggle(1000);
   realizaProceso<?=$i?>(0,1,1,<?=$tabla_saldos_clientes_resumen[$i]["ID_CLIENTE"]?>,<?=$saldo_dias?>);
  //$( ".div_res_ajax<?=$i?>" ).toggle( "slow", function() {
  //  realizaProceso(0,1,1,<?=$tabla_saldos_clientes_resumen[$i]["ID_CLIENTE"]?>);
  //});
});

//code ajax detalle plaza saldo
function realizaProceso<?=$i?>(v_plaza,v_resumen,v_group,v_cliente,v_saldo_dias){
        var parametros = {
                "plaza" : v_plaza,
                "resumen" : v_resumen,
                "group" : v_group,
                "cliente" : v_cliente,
                "saldo_dias" : v_saldo_dias,
        };
        $.ajax({
                data:  parametros,
                url:   '../action/saldo_cliente_plaza_det.php',
                type:  'post',
                beforeSend: function () {
                        $("#resultado<?=$i?>").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                        $("#resultado<?=$i?>").html(response);
                }
        });
}
<?php } ?>

// //code ajax detalle plaza saldo
// function realizaProceso(v_plaza,v_resumen,v_group,v_cliente){
//         var parametros = {
//                 "plaza" : v_plaza,
//                 "resumen" : v_resumen,
//                 "group" : v_group,
//                 "cliente" : v_cliente,
//         };
//         $.ajax({
//                 data:  parametros,
//                 url:   '../action/saldo_cliente_plaza_det.php',
//                 type:  'post',
//                 beforeSend: function () {
//                         $("#resultado\\[\\]").html("Procesando, espere por favor...");
//                 },
//                 success:  function (response) {
//                         $("#resultado\\[\\]").html(response);
//                 }
//         });
// }
</script>
<!-- Intro Plugins -->
<script src="../plugins/intro/intro.js"></script>
<script type="text/javascript">

function addHints(){
  intro = introJs();
    intro.setOptions({
      "hintButtonLabel": "OK",
      hints: [
        {
          element: document.querySelector('.step1'),
          hint: "Botón para regresar a la gráfica principal",
          hintPosition: 'bottom-middle',
        },
        {
          element: '.step2',
          hint: '<i class="fa fa-sort-amount-desc"></i> Botón para ordenar ascendente o descendente.',
          position: 'left'
        },
        {
          element: '.step3',
          hint: "Botón para exportar la tabla a Excel.",
          hintPosition: 'top-middle'
        },
        {
          element: '.step4',
          hint: "Botón para imprimir la tabla.",
          hintPosition: 'top-middle'
        },
        {
          element: '.step5',
          hint: "Botón para Ocultar o Mostrar columnas de la tabla.",
          hintPosition: 'top-middle'
        },
        {
          element: '.step6',
          hint: "Para ver más a detalle el tipo de mercancía clic en el botón mas.",
          hintPosition: 'top-middle'
        },
        {
          element: '.step_buscar',
          hint: "Busca registros de acuerdo al texto introducido.",
          hintPosition: 'left'
        },
        {
          element: '.step_muestra_reg',
          hint: "Seleccione el límite de registros para mostrar en la tabla.",
          hintPosition: 'left'
        },
        {
          element: '.step_exportar_hi',
          hint: "Opciones para exportar la grafica.",
          hintPosition: 'left'
        },
      ]
    });

    intro.addHints();
    <?php if ($activa_intro_sal_clie == false){?>  tutorial_modal(); <?php } ?>
}

function tutorial_modal(){
  introJs().setOptions({
    'showStepNumbers':'false',
    'skipLabel':'omitir',
    'prevLabel':'atras',
    'nextLabel':'siguiente',
    'doneLabel':'finalizado'
  }).start();
};
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
<script>
// TABLA RESUMEN GENERAL
$(document).ready(function() {
    $('.tabla_res_gen').DataTable({
      stateSave: true,
      select: true,
      "ordering": false,
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "language": {
          "url": "../plugins/datatables/Spanish.json"
        },

    //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="step3"></i><i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: '<?= $titulo_cc_ce ?>',
          },

          {
            extend: 'print',
            text: '<i class="step4"></i><i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: '<h5><?= $titulo_cc_ce ?></h5>',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="step5"></i><i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
//---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    });
} );
// TABLA SALDOS CLIENTES POR PLAZA
$(document).ready(function() {
    $('.tabla_saldo_clientes').DataTable({
      stateSave: true,
      select: true,
      "ordering": true,
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "language": {
          "url": "../plugins/datatables/Spanish.json"
        },

    //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="step3"></i><i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: '<?= $titulo_cc_ce ?>',
          },

          {
            extend: 'print',
            text: '<i class="step4"></i><i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: '<h5><?= $titulo_cc_ce ?></h5>',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="step5"></i><i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
//---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    });
} );
</script>
<!-- FLOT CHARTS -->
<script src="../plugins/flot/jquery.flot.min.js"></script>
<!-- FLOT PIE CHARTS 3D -->
<script src="../plugins/flot/jquery.flot.pie3d.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="../plugins/flot/jquery.flot.resize.min.js"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="../plugins/flot/jquery.flot.pie.min.js"></script>
<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
<script src="../plugins/flot/jquery.flot.categories.js"></script>
<!-- FLOT ORDER BARS  -->
<script src="../plugins/flot/jquery.flot.orderBars.js"></script>
<!-- FLOT  bar charts click text -->
<script src="../plugins/flot/jquery.flot.tooltip.js"></script>
<!-- *=*=*=*=*=*=*=*=*=*=*=*=*=*= GRAFICA PRINCIPAL DE DONA *=*=*=*=*=*=*=*=*=*=*=*=*=*=-->
<script>
var optionsDonut = {
     series: {
        pie3d: {
        stroke: { //define linea separadora
            width: .5,
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
      content: content_donut, /*"<div style='font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;'>  <b> %s</b></font> <br>  <div class='text-center'>  Importe: %n <br>Porcentaje: %p.2%</div>  </div>"*/
      defaultTheme: false
      }
};

var donutData_sal_clie = [

      <?php
      for ($i=0; $i < count($saldos_plaza); $i++) {
      $plaza = $saldos_plaza[$i]["PLAZA"];
      $separador  = ' ';

      if ($saldo_cliente_plaza == true){
        $plaza_corta = $saldos_plaza[$i]["ALMACEN"];

        $hex = $saldos_plaza[$i]["COLOR"];
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        //$rgb = (($r)-(rand(1,20))).",".(($b)+(rand(5,15))).",".(($b)+(rand($i,25)));
        $rgb = ($r)-(8*$i).",".($g).",".($b+$i);

        $rgbarr=explode(",", $rgb);
        $color_grafica= sprintf("#%02x%02x%02x", $rgbarr[0], $rgbarr[1], $rgbarr[2]);

      }else{
        $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA
        $color_grafica =  $saldos_plaza[$i]["COLOR"];
      }

      ?>

      {
      label: '<form method="post" action="#tabla">'
             +'<button data-toggle="modal" data-target="#modal_cargando" class="btn btn-link btn-xs" style="text-align: center;color: white;text-shadow: -1px -1px 1px #333, 1px -1px 1px #333, -1px 1px 1px #333, 1px 1px 1px #333;" name="saldo_cliente_plaza" value="<?=$saldos_plaza[$i]["PLAZA"]?>">'
             +'<?=$plaza_corta?>'
             +'</form>',
      data: <?php if (is_null($saldos_plaza[$i]["MONTO"])){ echo $saldos_plaza[$i]["SALDO"]; }else{ echo $saldos_plaza[$i]["MONTO"];} ?>,
      color: '<?= $color_grafica ?>'
      },

      <?php } ?>

    ];

<?php if ($saldo_cliente_plaza == false && $saldo_cliente_resumen == false){ ?>
$(document).ready(function () {
  $.plot($("#grafica_pastel_saldo_clientes"), donutData_sal_clie, optionsDonut);

});
<?php } ?>

function labelFormatter(label, series) {
    return label
        +"<div style='text-align: center;color: white;text-shadow: -1px -1px 1px #333, 1px -1px 1px #333, -1px 1px 1px #333, 1px 1px 1px #333;'>"+(series.percent).toFixed(2)+ "%</div>"
        + "</div></button>";
  }

function content_donut(label, xval, yval) {
   var content = "<div style='border: 2px solid; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;'><b>%s</b><br> IMPORTE: "+yval.toLocaleString("es-MX",{style:"currency", currency:"MXN"})+"<br>PORCENTAJE: %p.2%</div>";
   return content;
  }


</script>
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<?php if($saldo_cliente_resumen == true){ ?>
<script>

$(document).ready(function () {
Highcharts.setOptions({
  lang: {
    thousandsSep: ','
  }
});

// GRAFICA RESUMEN SALDO CLIENTES
Highcharts.chart('grafica_resumen_saldo', {
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
      text: 'RESUMEN GENERAL SALDO TOTAL DE CLIENTES <?= $titulo_dias ?>'
  },
  tooltip: {
  headerFormat: '<span style="font-size:10px">{point.key}</span> <b>{point.percentage:.0f}%</b><table>',
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
      data: [
      {
          name: 'SALDO CLIENTES REPRESENTATIVOS',
          y: <?= $saldo_cliente_repre ?>
      },
      {
          name: 'SALDO OTROS CLIENTES',
          y: <?= $saldo_otros_clientes?>
      },
      ]
  }]
});
//******************************
// GRAFICA SALDO CLIENTES

// ******************************

});
</script>
<?php } ?>
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
$('.select_plaza_grafica').on('change',function(){
  $.ajax({url: '../class/Saldo_cliente.php', success: function(result){
    $('#modal_cargando').modal('show');
  }});
});
</script>
</html>
<?php conexion::cerrar($conn); ?>
