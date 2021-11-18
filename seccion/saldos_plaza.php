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

/*======GUARDA EL VALOR PARA FECHA DE CORTE EN UNA SESSION======*/
if ($_SESSION['fecha_corte'] == false){ 
  $_SESSION['fecha_corte'] = date('d/m/Y');;
  $fecha_corte = $_SESSION['fecha_corte'];
}else{
  if(isset($_POST['fecha_corte']))
  $_SESSION['fecha_corte'] = $_POST['fecha_corte'];
  $fecha_corte = $_SESSION['fecha_corte'];
}
/*======GUARDA EL VALOR PARA PLAZA SELECCIONADA======*/
if(isset($_POST["saldo_cliente_plaza"]))
  $_SESSION["saldo_cliente_plaza"] = $_POST["saldo_cliente_plaza"];
$saldo_cliente_plaza = $_SESSION["saldo_cliente_plaza"];
 
//////////////// INICIA INSTANCIAS /////////////////////////// 
include_once '../class/Saldo_plaza.php';
$ins_obj_saldos_clientes = new Saldos_plaza($saldo_cliente_plaza);
$grafica_saldos_plaza = $ins_obj_saldos_clientes->grafica_saldos_plaza($fecha_corte);
$tabla_saldos_clientes = $ins_obj_saldos_clientes->tabla_saldos_clientes($fecha_corte,$saldo_cliente_plaza,$importe_filtro_resumen);


/*----------------------ACTIVA INTRO AL INICIAR----------------------*/  
if(isset($_POST["activa_intro_sal_pla"]))
$_SESSION["activa_intro_sal_pla"] = $_POST["activa_intro_sal_pla"];
$activa_intro_sal_pla = $_SESSION["activa_intro_sal_pla"]; 

?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- DataTables -->  
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css"> 
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css"> 
<!-- DATAILS RESPONSIVE -->
<link rel="stylesheet" href="../plugins/datatables/extensions/Responsive/css/responsive.dataTables.min.css">
<!-- bootstrap datepicker -->
<script>
window.onload = function() {
  addHints();
};
</script>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) --> 
    <section class="content-header">
      <h1>Dashboard<small>Antigüedad de saldos</small></h1>
      <ol class="breadcrumb">
        <li data-intro="Botón para activar la guía de ayuda" class="intro-lead-in">
        <a href="javascript:void(0)" onclick="tutorial_modal();" class="page-scroll btn btn-xl"><i class="ion-chatbubble-working">Tutorial</i></a>
        </li>
        <li data-intro="Clic aquí para desactivar o activar el mensaje de guía de ayuda al cargar la pagina">
        <form action="saldos_plaza.php" method="post">
        <?php 
        if ($activa_intro_sal_pla == false){ 
          echo '<button class="btn btn-link btn-xs click_modal_cargando" type="submit" name="activa_intro_sal_pla" value="1"><i class="ion-android-done-all">Desactivar</i></button>';
        }else{
          echo '<button class="btn btn-link btn-xs click_modal_cargando" type="submit" name="activa_intro_sal_pla" value=""><i class="ion-android-done">Activar</i></button>';
        }
        ?>  
        </form>
        </li>
      </ol><br>
    </section> 
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
 

<!-- ############################ INICIA SECCION DE LA GRAFICA OTROS POR PLAZA ############################# --> 
<section>
  <div class="box box-default">    
    <div class="box-header with-border">
      <h3 class="box-title"> </h3> 
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
      </div>
    </div>
    <div class="box-body"><!--box-body-->

      <!-- INICIA CODE FECHA DE CORTE -->
      <div class="row"><!-- row --> 
        <div class="col-md-5">
          <form method="post">
          <div class="col-md-9" data-intro="Campo para introducir la fecha de corte">
            <label>Fecha de corte:</label>
            <div class='input-group date' id="datatime_t1_inicio_capbodega">
              <span class="input-group-addon">
                <span class="fa fa-calendar"></span>
              </span>
              <input type="text" class="form-control pull-right" id="datepicker" name="fecha_corte" value="<?=$fecha_corte?>">
              <span class="input-group-btn">
                <button class="btn btn-info btn-flat" type="submit">OK</button>
              </span>
            </div>
          </div>
          </form>
        </div>

        <?php if ( $saldo_cliente_plaza == true || $saldo_cliente_resumen == true ){ ?>
          <form method="post">
          <input type="hidden" name="saldo_cliente_resumen" value="">
          <button data-intro="Botón para regresar a la gráfica principal" type="submit" name="saldo_cliente_plaza" value="" class="click_modal_cargando btn btn-xs bg-primary click_car_btn_reg"><i class="fa fa-reply"></i>Regresar</button><div class="step1"></div>
          </form> 
        <?php } ?>

        <br> 
        </div>
        <!-- TERMINA CODE FECHA DE CORTE -->

<!-- |-|-|-|-|-|-|-|-|-|-|-|-|-|-| INICIA CODE GRAFICA Y TABLA DE PLAZAS CON ADEUDO |-|-|-|-|-|-|-|-|-|-|-|-|-|-| -->
<?php if ( $saldo_cliente_plaza == false){ ?>
      <div class="row"><!-- row -->

      <div class="col-md-4"><!-- col-md-3 -->

        <div class="table-responsive">
          <table class="table no-margin   compact table-hover table-striped table-bordered">
            <thead>
            <tr>
              <th data-intro="Seleccione una plaza para ver los clientes con deuda">PLAZA</th>
              <th>IMPORTE</th> 
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
            for ($i=0; $i < count($grafica_saldos_plaza) ; $i++) {
              if (is_null($grafica_saldos_plaza[$i]["MONTO"])){
                $monto_plazas[$i] = $grafica_saldos_plaza[$i]["SALDO"];
              }else{
                $monto_plazas[$i] = $grafica_saldos_plaza[$i]["MONTO"];
              }
              $plaza = $grafica_saldos_plaza[$i]["PLAZA"];
              $separador  = ' '; 
              $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA 
            ?>
            <tr>
              <td><form method="post"><button type="submit" class="click_modal_cargando btn btn-link btn-xs" name="saldo_cliente_plaza" value="<?=$grafica_saldos_plaza[$i]["PLAZA"]?>"><?= $plaza_corta ?></button></form></td>
              <td>
                <?php 
                if (is_null($grafica_saldos_plaza[$i]["MONTO"])){
                  echo "$".number_format($grafica_saldos_plaza[$i]["SALDO"],2);
                }else{
                  echo "$".number_format($grafica_saldos_plaza[$i]["MONTO"],2);
                } 
                ?>
              </td>
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

      <div class="col-md-8"><!-- col-md-9 -->
        <h4 class="content-header text-blue text-center"> Antigüedad de Saldos en Plazas</h4><hr>
        <div id="grafica_pastel_saldo_clientes" class="" style="height: 350px;"></div>
      </div><!-- ./col-md-9 -->

    </div><!-- ./row -->
<?php } ?>
<!-- |-|-|-|-|-|-|-|-|-|-|-|-|-|-| TERMINA CODE GRAFICA Y TABLA DE PLAZAS CON ADEUDO |-|-|-|-|-|-|-|-|-|-|-|-|-|-| -->


<?php if ($saldo_cliente_plaza == true){ ?>
<!-- |-|-|-|-|-|-|-|-|-|-|-|-|-|-| INICIA CODE TABLA DETALLES |-|-|-|-|-|-|-|-|-|-|-|-|-|-| -->
      <h4 class="content-header text-blue text-center"> ANTIGÜEDAD DE SALDOS PLAZAS <?=$saldo_cliente_plaza?></h4><hr>

      <div class="table-responsive"><!-- table-responsive -->
        <!-- <h4 class="content-header text-blue text-center"><i class="fa fa-pie-chart"></i> PLAZA CÓRDOBA</h4><hr> -->
        <table class="tabla_saldo_clientes table compact table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th></th>
            <th class="small">CLIENTE</th>
            <th class="small step2">SALDO</th>
            <th class="small">NO VENCIDO</th>
            <th class="small">1-15 DÍAS</th>
            <th class="small">16-30 DÍAS</th>
            <th class="small">MÁS DE 31 DÍAS</th>
            <th class="small">MÁS DE 61 DÍAS</th>
            <th class="small">MÁS DE 91 DÍAS</th>
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
            <th></th>
            <th class="text-right">TOTAL:</th>
            <th><p id="saldo_cliente"></p></th>
            <th><p id="total_saldo_no_vencido"></p></th>
            <th><p id="total_saldo_monto_1_15"></p></th>
            <th><p id="total_saldo_monto_16_30"></p></th>
            <th><p id="total_saldo_monto_mas_31"></p></th>
            <th><p id="total_saldo_monto_mas_61"></p></th>
            <th><p id="total_saldo_monto_mas_91"></p></th>
            <th><p id="mercancia_cliente_tabla"></p></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
          </tr>
        </tfoot> 
        <tbody>
        <?php for ($i=0; $i < count($tabla_saldos_clientes) ; $i++) { 
          //$saldo_cliente[$i] = $tabla_saldos_clientes[$i]["SALDO"];
          if (is_null($tabla_saldos_clientes[$i]["MONTO"])){
              $saldo_cliente[$i] = $tabla_saldos_clientes[$i]["SALDO"];
          }else{
              $saldo_cliente[$i] = $tabla_saldos_clientes[$i]["MONTO"];
          }
        ?> 
          <tr>
            <td></td> 
            <td class="small"><?= "(".$tabla_saldos_clientes[$i]["ID_CLIENTE"].")".$tabla_saldos_clientes[$i]["CLIENTE"] ?></td>
            <!-- ////////// INICIA CODE PARA PONER SALDO EN PESOS ////////// -->
            <td class="small" bgcolor="#ffff00">
            <?php 
            if (is_null($tabla_saldos_clientes[$i]["MONTO"])){
              echo "$".number_format($tabla_saldos_clientes[$i]["SALDO"],2);
            }else{
              echo "$".number_format($tabla_saldos_clientes[$i]["MONTO"],2) ;
            }
            ?>
            </td> 
            <!-- ////////// TERMINA CODE PARA PONER SALDO EN PESOS ////////// -->
            <?php 
            // °-°-°-°-°-°-°-°-°-°-°-°- INICIA CODE MUESTRA SALDO NO VENCIDO °-°-°-°-°-°-°-°-°-°-°-°- //
            //echo '<td class="small" bgcolor="#808000">$545454545';

            $saldo_no_vencido = $ins_obj_saldos_clientes->saldo_no_vencido($fecha_corte,$tabla_saldos_clientes[$i]["ID_CLIENTE"],$tabla_saldos_clientes[$i]["ID_PLAZA"]);
            for ($j=0; $j < count($saldo_no_vencido) ; $j++) { 
              if (is_null($saldo_no_vencido[$j]["NO_VENCIDO_FAC"])){
                echo '<td class="small" bgcolor="#808000">$'.number_format($saldo_no_vencido[$j]["NO_VENCIDO_MOV"],2).'';
                $total_saldo_no_vencido[$i] = $saldo_no_vencido[$j]["NO_VENCIDO_MOV"];
              }else{
                echo '<td class="small" bgcolor="#808000">$'.number_format($saldo_no_vencido[$j]["NO_VENCIDO_FAC"],2).'';
                $total_saldo_no_vencido[$i] = $saldo_no_vencido[$j]["NO_VENCIDO_FAC"];
              }   
            }

            // °-°-°-°-°-°-°-°-°-°-°-°- TERMINA CODE MUESTRA SALDO NO VENCIDO °-°-°-°-°-°-°-°-°-°-°-°- //
            // °-°-°-°-°-°-°-°-°-°-°-°- INICIA CODE MUESTRA SALDO 1-15 DIAS °-°-°-°-°-°-°-°-°-°-°-°- //
            $saldo_monto_1_15 = $ins_obj_saldos_clientes->saldo_monto_1_15($fecha_corte,$tabla_saldos_clientes[$i]["ID_CLIENTE"],$tabla_saldos_clientes[$i]["ID_PLAZA"]);
            for ($j=0; $j < count($saldo_monto_1_15) ; $j++) { 
              if (is_null($saldo_monto_1_15[$j]["MONTO_1_15"])){
                echo '<td class="small" bgcolor="#008080">$'.number_format($saldo_monto_1_15[$j]["SALDO_1_15"],2).'';
                $total_saldo_monto_1_15[$i] = $saldo_monto_1_15[$j]["SALDO_1_15"];
              }else{
                echo '<td class="small" bgcolor="#008080">$'.number_format($saldo_monto_1_15[$j]["MONTO_1_15"],2).'';
                $total_saldo_monto_1_15[$i] = $saldo_monto_1_15[$j]["MONTO_1_15"];
              }   
            }
            // °-°-°-°-°-°-°-°-°-°-°-°- TERMINA CODE MUESTRA SALDO 1-15 DIAS °-°-°-°-°-°-°-°-°-°-°-°- //
            // °-°-°-°-°-°-°-°-°-°-°-°- INICIA CODE MUESTRA SALDO 16-30 DIAS °-°-°-°-°-°-°-°-°-°-°-°- //
            $saldo_monto_16_30 = $ins_obj_saldos_clientes->saldo_monto_16_30($fecha_corte,$tabla_saldos_clientes[$i]["ID_CLIENTE"],$tabla_saldos_clientes[$i]["ID_PLAZA"]);
            for ($j=0; $j < count($saldo_monto_16_30) ; $j++) { 
              if (is_null($saldo_monto_16_30[$j]["MONTO_16_30"])){
                echo '<td class="small" bgcolor="#a6caf0">$'.number_format($saldo_monto_16_30[$j]["SALDO_16_30"],2).'';
                $total_saldo_monto_16_30[$i] = $saldo_monto_16_30[$j]["SALDO_16_30"];
              }else{
                echo '<td class="small" bgcolor="#a6caf0">$'.number_format($saldo_monto_16_30[$j]["MONTO_16_30"],2).'';
                $total_saldo_monto_16_30[$i] = $saldo_monto_16_30[$j]["MONTO_16_30"];
              }   
            }
            // °-°-°-°-°-°-°-°-°-°-°-°- TERMINA CODE MUESTRA SALDO 16-30 DIAS °-°-°-°-°-°-°-°-°-°-°-°- //
            // °-°-°-°-°-°-°-°-°-°-°-°- INICIA CODE MUESTRA SALDO MAS 31 DIAS °-°-°-°-°-°-°-°-°-°-°-°- //
            $saldo_monto_mas_31 = $ins_obj_saldos_clientes->saldo_monto_mas_31($fecha_corte,$tabla_saldos_clientes[$i]["ID_CLIENTE"],$tabla_saldos_clientes[$i]["ID_PLAZA"]);
            for ($j=0; $j < count($saldo_monto_mas_31) ; $j++) { 
              if (is_null($saldo_monto_mas_31[$j]["MONTO_mas_31"])){
                echo '<td class="small" bgcolor="#00ffff">$'.number_format($saldo_monto_mas_31[$j]["SALDO_MAS_31"],2).'';
                $total_saldo_monto_mas_31[$i] = $saldo_monto_mas_31[$j]["SALDO_MAS_31"];
              }else{
                echo '<td class="small" bgcolor="#00ffff">$'.number_format($saldo_monto_mas_31[$j]["MONTO_MAS_31"],2).'';
                $total_saldo_monto_mas_31[$i] = $saldo_monto_mas_31[$j]["MONTO_MAS_31"];
              }   
            }
            // °-°-°-°-°-°-°-°-°-°-°-°- TERMINA CODE MUESTRA SALDO MAS 31 DIAS °-°-°-°-°-°-°-°-°-°-°-°- //
            // °-°-°-°-°-°-°-°-°-°-°-°- INICIA CODE MUESTRA SALDO MAS 61 DIAS °-°-°-°-°-°-°-°-°-°-°-°- //
            $saldo_monto_mas_61 = $ins_obj_saldos_clientes->saldo_monto_mas_61($fecha_corte,$tabla_saldos_clientes[$i]["ID_CLIENTE"],$tabla_saldos_clientes[$i]["ID_PLAZA"]);
            for ($j=0; $j < count($saldo_monto_mas_61) ; $j++) { 
              if (is_null($saldo_monto_mas_61[$j]["MONTO_mas_61"])){
                echo '<td class="small" bgcolor="#c0c0c0">$'.number_format($saldo_monto_mas_61[$j]["SALDO_MAS_61"],2).'';
                $total_saldo_monto_mas_61[$i] = $saldo_monto_mas_61[$j]["SALDO_MAS_61"];
              }else{
                echo '<td class="small" bgcolor="#c0c0c0">$'.number_format($saldo_monto_mas_61[$j]["MONTO_MAS_61"],2).'';
                $total_saldo_monto_mas_61[$i] = $saldo_monto_mas_61[$j]["MONTO_MAS_61"];
              }   
            }
            // °-°-°-°-°-°-°-°-°-°-°-°- TERMINA CODE MUESTRA SALDO MAS 61 DIAS °-°-°-°-°-°-°-°-°-°-°-°- //
            // °-°-°-°-°-°-°-°-°-°-°-°- INICIA CODE MUESTRA SALDO MAS 91 DIAS °-°-°-°-°-°-°-°-°-°-°-°- //
            $saldo_monto_mas_91 = $ins_obj_saldos_clientes->saldo_monto_mas_91($fecha_corte,$tabla_saldos_clientes[$i]["ID_CLIENTE"],$tabla_saldos_clientes[$i]["ID_PLAZA"]);
            for ($j=0; $j < count($saldo_monto_mas_91) ; $j++) { 
              if (is_null($saldo_monto_mas_91[$j]["MONTO_mas_91"])){
                echo '<td class="small" bgcolor="#00ff00">$'.number_format($saldo_monto_mas_91[$j]["SALDO_MAS_91"],2).'';
                $total_saldo_monto_mas_91[$i] = $saldo_monto_mas_91[$j]["SALDO_MAS_91"];
              }else{
                echo '<td class="small" bgcolor="#00ff00">$'.number_format($saldo_monto_mas_91[$j]["MONTO_MAS_91"],2).'';
                $total_saldo_monto_mas_91[$i] = $saldo_monto_mas_91[$j]["MONTO_MAS_91"];
              }   
            }
            // °-°-°-°-°-°-°-°-°-°-°-°- TERMINA CODE MUESTRA SALDO MAS 91 DIAS °-°-°-°-°-°-°-°-°-°-°-°- //
            ?>
            <!-- ////////// INICIA CODE PARA BUSCAR VALOR DE MERCANCIA ////////// -->
            <?php 
            $valor_mercancia = $ins_obj_saldos_clientes->valor_mercancia($tabla_saldos_clientes[$i]["ID_CLIENTE"]);
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
        document.getElementById("total_saldo_no_vencido").innerHTML = "<?= "$".number_format(array_sum($total_saldo_no_vencido),2)?>";
        document.getElementById("total_saldo_monto_1_15").innerHTML = "<?= "$".number_format(array_sum($total_saldo_monto_1_15),2)?>";
        document.getElementById("total_saldo_monto_16_30").innerHTML = "<?= "$".number_format(array_sum($total_saldo_monto_16_30),2)?>";
        document.getElementById("total_saldo_monto_mas_31").innerHTML = "<?= "$".number_format(array_sum($total_saldo_monto_mas_31),2)?>";
        document.getElementById("total_saldo_monto_mas_61").innerHTML = "<?= "$".number_format(array_sum($total_saldo_monto_mas_61),2)?>";
        document.getElementById("total_saldo_monto_mas_91").innerHTML = "<?= "$".number_format(array_sum($total_saldo_monto_mas_91),2)?>";
        document.getElementById("mercancia_cliente_tabla").innerHTML = "<?= "$".number_format(array_sum($mercancia_cliente),2)?>";

        </script> 
      </div><!-- ./table-responsive -->
<!-- |-|-|-|-|-|-|-|-|-|-|-|-|-|-| TERMINA CODE TABLA DETALLES |-|-|-|-|-|-|-|-|-|-|-|-|-|-| -->
<?php } ?>

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
<!-- bootstrap datepicker -->
<script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
//Date picker
    $('#datepicker').datepicker({
      clearBtn: true,
    language: "es",
    format: "dd/mm/yyyy",
    autoclose: true
    });
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
      ]
    });   

    intro.addHints();
    <?php if ($activa_intro_sal_pla == false){?>  tutorial_modal(); <?php } ?>
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
<!-- DATAILS RESPONSIVE -->
<script src="../plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
<script>
$(document).ready(function() {
    $('.tabla_saldo_clientes').DataTable({
      stateSave: true,
      responsive: {
            details: {
                type: 'column'
            }
        },
        columnDefs: [ {
            className: 'control',
            orderable: false,
            targets:   0
        } ],
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
      for ($i=0; $i < count($grafica_saldos_plaza); $i++) { 
      $plaza = $grafica_saldos_plaza[$i]["PLAZA"]; 
      $separador  = ' ';

      if ($saldo_cliente_plaza == true){
        $plaza_corta = $grafica_saldos_plaza[$i]["ALMACEN"]; 

        $hex = $grafica_saldos_plaza[$i]["COLOR"];
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");  
        //$rgb = (($r)-(rand(1,20))).",".(($b)+(rand(5,15))).",".(($b)+(rand($i,25))); 
        $rgb = ($r)-(8*$i).",".($g).",".($b+$i); 

        $rgbarr=explode(",", $rgb);
        $color_grafica= sprintf("#%02x%02x%02x", $rgbarr[0], $rgbarr[1], $rgbarr[2]);

      }else{
        $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA 
        $color_grafica =  $grafica_saldos_plaza[$i]["COLOR"]; 
      }
      
      ?>

      {
      label: '<form method="post">'
             +'<button data-toggle="modal" data-target="#modal_cargando" class="btn btn-link btn-xs" style="text-align: center;color: white;text-shadow: -1px -1px 1px #333, 1px -1px 1px #333, -1px 1px 1px #333, 1px 1px 1px #333;" name="saldo_cliente_plaza" value="<?=$grafica_saldos_plaza[$i]["PLAZA"]?>">'
             +'<?=$plaza_corta?>'
             +'</form>', 
      data: <?php if (is_null($grafica_saldos_plaza[$i]["MONTO"])){ echo $grafica_saldos_plaza[$i]["SALDO"]; }else{ echo $grafica_saldos_plaza[$i]["MONTO"];} ?>, 
      color: '<?= $color_grafica ?>'
      }, 

      <?php } ?> 

    ];  

<?php //if ($saldo_cliente_plaza == false && $saldo_cliente_resumen == false){ ?>
$(document).ready(function () {
  $.plot($("#grafica_pastel_saldo_clientes"), donutData_sal_clie, optionsDonut); 
    
});
<?php //} ?>

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