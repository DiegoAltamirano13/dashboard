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


/*MESES Y AÑOS*/
include_once '../class/Comp_ingre_cliente.php';
$ins_obj_ingre_clie = new Comparativo();
$consulta_fecha = $ins_obj_ingre_clie->consulta_fecha();

if ($_SESSION['fecha_ingre_clie'] == false){
  for ($i=0; $i <count($consulta_fecha) ; $i++)
  {
    $fecha_ingre_clie_anterior = strtotime ( '-1 month' , strtotime ( $consulta_fecha[$i]["ANIO"].'-'.$consulta_fecha[$i]["MES"] ) ) ;
    $fecha_ingre_clie_anterior = date ( 'Y-m' , $fecha_ingre_clie_anterior );
    $_SESSION['fecha_ingre_clie'] = $fecha_ingre_clie_anterior;
    $fecha_ingre_clie = $_SESSION['fecha_ingre_clie'];
  }
}else{
  if(isset($_POST['fecha_ingre_clie']))
  $_SESSION['fecha_ingre_clie'] = $_POST['fecha_ingre_clie'];
  $fecha_ingre_clie = $_SESSION['fecha_ingre_clie'];
}

$meses = array("ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SEP","OCT","NOV","DIC");
/*======GUARDA EL VALOR DEL MES ANTERIOR======*/
$mes_anterior = strtotime ( '-1 month' , strtotime ($fecha_ingre_clie) ) ;
$mes_anterior = date ( 'Y-m' , $mes_anterior );
/*======GUARDA EL VALOR DEL COMPARATIVO AÑO ANTERIOR======*/
$anio_anterior = strtotime ( '-1 year' , strtotime ($fecha_ingre_clie) ) ;
$anio_anterior = date ( 'Y-m' , $anio_anterior );
/*======GUARDA EL VALOR DEL ACUMULADO======*/
$acomulado = date("Y", strtotime($mes_anterior)).'-'.'01';
/*======GUARDA EL VALOR DEL ACUMULADO ANIO ANTERIOR======*/
$acomulado_anterior = date("Y", strtotime($anio_anterior)).'-'.'01';

$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '7');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
//////////////////////
include_once '../class/Comercial.php';
$ins_objeto_comercial = new Comercial();
/////////////////////////////
$id_prospecto_co = $_GET["id_prospecto_co"];
if ($id_prospecto_co == false){
  header('Location: comercial.php');
}
/////////// SESIONES DE TOP PROMOTOR SELECCIONA //////////////////
if(isset($_POST['co_top_promo']))
    $_SESSION['co_top_promo'] = $_POST['co_top_promo'];
    $co_top_promo = $_SESSION['co_top_promo'];
/////////// SESIONES DE GRAFICA SELECCIONA //////////////////
if(isset($_POST['grafica_co_pros']))
    $_SESSION['grafica_co_pros'] = $_POST['grafica_co_pros'];
    $grafica_co_pros = $_SESSION['grafica_co_pros'];


?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Argo Almacenadora, S.A. de C.V.</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <link rel="shortcut icon" href="../assets/ico/favicon.png">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h5 class="page-header text-center">
          <i class="fa fa-edit"></i> Detalles Facturación del Prospecto
          <!-- <small class="pull-right">Date: 2/10/2014</small> -->
        </h5>
      </div>
      <!-- /.col -->
    </div>

    <div class="table-responsive"><!-- table-responsive -->
    <table id="tabla_lista" class="table table-striped table-hover table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th class="small"></th>
          <!-- COMPARATIVO #1 -->
          <th class="small" colspan="5">COMPARATIVO VS MES ANTERIOR</th>
          <!-- COMPARATIVO #2 -->
          <th class="small" colspan="4">COMPARATIVO</th>
          <!-- COMPARATIVO #3 -->
          <th class="small" colspan="4">ACUMULADO</th>
        </tr>
        <tr>
          <th class="small">CLIENTE</th>
          <!-- COMPARATIVO #1 -->

          <th class="small" colspan="1"><?= $meses[date(date("n", strtotime($mes_anterior)))-1].'-'.date("Y", strtotime($mes_anterior)) ?></th>
          <th class="small" colspan="1"><?= $meses[date(date("n", strtotime($fecha_ingre_clie)))-1].'-'.date("Y", strtotime($fecha_ingre_clie)) ?></th>
          <th class="small" colspan="2">DIFERENCIA</th>
          <th class="small" colspan="1" style="width: 90px"></th>

          <!-- COMPARATIVO #2 -->

          <th class="small" colspan="1"><?= $meses[date(date("n", strtotime($anio_anterior)))-1].'-'.date("Y", strtotime($anio_anterior)) ?></th>
          <th class="small" colspan="1"><?= $meses[date(date("n", strtotime($fecha_ingre_clie)))-1].'-'.date("Y", strtotime($fecha_ingre_clie)) ?></th>
          <th class="small" colspan="1">DIFERENCIA</th>
          <th class="small" colspan="1"style="width: 90px"></th>

          <!-- COMPARATIVO #3 -->
          <th class="small"><?= $meses[date(date("n", strtotime($acomulado_anterior)))-1].'/'.$meses[date(date("n", strtotime($anio_anterior)))-1].'-'.date("Y", strtotime($anio_anterior)) ?></th>
          <th class="small">
            <?= $meses[date(date("n", strtotime($acomulado)))-1].'/'.$meses[date(date("n", strtotime($fecha_ingre_clie)))-1].'-'.date("Y", strtotime($fecha_ingre_clie)) ?>
          </th>
          <th class="small">DIFERENCIA</th>
          <th class="small" style="width: 90px"></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="small">
            <button type="submit" class="btn-link bnt-xs"><?= $id_prospecto_co?></button>
          </td>
          <!-- COMPARATIVO #1 -->
          <?php
            $consulta_ingreso_mes_anterior = $ins_obj_ingre_clie->consulta_ingresosProspecto(1,$id_prospecto_co,date("Y", strtotime($mes_anterior)),date("m", strtotime($mes_anterior)));
            $consulta_ingreso_mes = $ins_obj_ingre_clie->consulta_ingresosProspecto(1,$id_prospecto_co,date("Y", strtotime($fecha_ingre_clie)),date("m", strtotime($fecha_ingre_clie)));
          ?>
          <?php
          //  echo count($consulta_ingreso_mes_anterior);
            for ($j=0; $j < count($consulta_ingreso_mes_anterior) ; $j++) { $saldo_mes_anterior[$j] = $consulta_ingreso_mes_anterior[$j]["INGRESO"]; ?>
          <td class="small" colspan="1"><?= "$".number_format($consulta_ingreso_mes_anterior[$j]["INGRESO"],2) ?></td>
        <?php } if (count($consulta_ingreso_mes_anterior) == 0) {
           $saldo_mes_anterior[$j] = 0;
        ?>
            <td class="small" colspan="1"><?= "$".number_format(0,2) ?></td>
        <?php } ?>
          <?php
          for ($k=0; $k < count($consulta_ingreso_mes) ; $k++) { $saldo_mes[$k] = $consulta_ingreso_mes[$k]["INGRESO"]; ?>
          <td class="small" colspan="1"><?= "$".number_format($consulta_ingreso_mes[$k]["INGRESO"],2) ?> </td>
        <?php } if (count($consulta_ingreso_mes)== 0) {
           $saldo_mes[$k] = 0; ?>
           <td class="small" colspan="1"><?= "$".number_format(0,2) ?> </td>
         <?php    } ?>
          <td class="small" colspan="2"><?php $diferencia_com_mes = array_sum($saldo_mes) - array_sum($saldo_mes_anterior);
                    //echo $diferencia_com_mes."diego";
                    if ($diferencia_com_mes >= 0) {
                        echo '$'.number_format($diferencia_com_mes,2);
                    }else {
                      $diferencia_com_mes = $diferencia_com_mes * -1 ;
                      echo "-$".number_format($diferencia_com_mes, 2);
                    }
                    //echo $diferencia_com_mes."gere";
                     ?></td>
          <td class="small">
            <?php
            //-echo (array_sum($saldo_mes) - array_sum($saldo_mes_anterior)) ;
            if (array_sum($saldo_mes) - array_sum($saldo_mes_anterior) >0){
              echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="green" sparkfillcolor="#69F0AE">'.array_sum($saldo_mes_anterior).','.array_sum($saldo_mes).'</span><span class="badge bg-green">↑</span>';}
              else if(array_sum($saldo_mes) - array_sum($saldo_mes_anterior) == 0){
              echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="orange" sparkfillcolor="#FFE082">'.array_sum($saldo_mes_anterior).','.array_sum($saldo_mes).'</span><span class="badge bg-yellow">→</span>';}
              else{echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="red" sparkfillcolor="#FFCDD2">'.array_sum($saldo_mes_anterior).','.array_sum($saldo_mes).'</span><span class="badge bg-red">↓</span>';}
            ?>
          </td>

          <!-- COMPARATIVO #2 -->
          <?php
            $consulta_ingreso_anio_anterior = $ins_obj_ingre_clie->consulta_ingresosProspecto(1,$id_prospecto_co,date("Y", strtotime($anio_anterior)),date("m", strtotime($anio_anterior)));
            $consulta_ingreso_anio = $ins_obj_ingre_clie->consulta_ingresosProspecto(1,$id_prospecto_co,date("Y", strtotime($fecha_ingre_clie)),date("m", strtotime($fecha_ingre_clie)));
          ?>
          <?php for ($l=0; $l < count($consulta_ingreso_anio_anterior) ; $l++) { $saldo_anio_anterior[$l] = $consulta_ingreso_anio_anterior[$l]["INGRESO"]; ?>
          <td class="small"><?= "$".number_format($consulta_ingreso_anio_anterior[$l]["INGRESO"],2) ?></td>
        <?php } if (count($consulta_ingreso_anio_anterior) == 0) {
                      $saldo_anio_anterior[$l] = 0; ?>
                      <td class="small"><?= "$".number_format(0,2) ?></td>
        <?php } ?>
          <?php for ($m=0; $m < count($consulta_ingreso_anio) ; $m++) { $saldo_anio[$m] = $consulta_ingreso_anio[$m]["INGRESO"]; ?>
          <td class="small"><?= "$".number_format($consulta_ingreso_anio[$m]["INGRESO"],2) ?></td>
        <?php } if (count($consulta_ingreso_anio) == 0) {
          $saldo_anio[$m] = 0; ?>
          <td class="small"><?= "$".number_format(0,2) ?></td>
        <?php } ?>
          <td class="small"><?php $diferencia_com_anio = array_sum($saldo_anio) - array_sum($saldo_anio_anterior);
              if ($diferencia_com_anio >= 0) {
                  echo '$'.number_format($diferencia_com_anio,2);
              }else {
                $diferencia_com_anio = $diferencia_com_anio * -1 ;
                echo "-$".number_format($diferencia_com_anio, 2);
              }
          ?> </td>
          <td class="small">
            <?php
            if (array_sum($saldo_anio) - array_sum($saldo_anio_anterior) >0){
              echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="green" sparkfillcolor="#69F0AE">'.array_sum($saldo_anio_anterior).','.array_sum($saldo_anio).'</span><span class="badge bg-green">↑</span>';}
              else if(array_sum($saldo_anio) - array_sum($saldo_anio_anterior) == 0){
              echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="orange" sparkfillcolor="#FFE082">'.array_sum($saldo_anio_anterior).','.array_sum($saldo_anio).'</span><span class="badge bg-yellow">→</span>';}
              else{echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="red" sparkfillcolor="#FFCDD2">'.array_sum($saldo_anio_anterior).','.array_sum($saldo_anio).'</span><span class="badge bg-red">↓</span>';}
            ?>
          </td>
          <?php  ?>
          <!-- COMPARATIVO #3 -->
          <?php
            $consulta_diferencia_anio_anterior = $ins_obj_ingre_clie->consulta_ingresosProspecto(2,$id_prospecto_co,date("Y", strtotime($anio_anterior)),date("m", strtotime($anio_anterior)));
            $consulta_diferencia_anio = $ins_obj_ingre_clie->consulta_ingresosProspecto(2,$id_prospecto_co,date("Y", strtotime($fecha_ingre_clie)),date("m", strtotime($fecha_ingre_clie)));
          ?>
          <?php for ($a=0; $a < count($consulta_diferencia_anio_anterior) ; $a++) { $saldo_diferencia_anio_anterior[$a] = $consulta_diferencia_anio_anterior[$a]["INGRESO"]; ?>
          <td class="small" colspan="1"><?= '$'.number_format($consulta_diferencia_anio_anterior[$a]["INGRESO"],2) ?></td>
          <?php } ?>
          <?php for ($b=0; $b < count($consulta_diferencia_anio) ; $b++) { $saldo_diferencia_anio[$b] = $consulta_diferencia_anio[$b]["INGRESO"]; ?>
          <td class="small" colspan="1"><?= '$'.number_format($consulta_diferencia_anio[$b]["INGRESO"],2) ?></td>
          <?php } ?>
          <td class="small" colspan="1"><?php $diferencia_diferencia = array_sum($saldo_diferencia_anio) - array_sum($saldo_diferencia_anio_anterior);
          if ($diferencia_diferencia >= 0) {
              echo '$'.number_format($diferencia_diferencia,2);
          }else {
            $diferencia_diferencia = $diferencia_diferencia * -1 ;
            echo "-$".number_format($diferencia_diferencia, 2);
          }
           ?></td>
          <td class="small" colspan="1">
            <?php
            if (array_sum($saldo_diferencia_anio) - array_sum($saldo_diferencia_anio_anterior) >0){
              echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="green" sparkfillcolor="#69F0AE">'.array_sum($saldo_diferencia_anio_anterior).','.array_sum($saldo_diferencia_anio).'</span><span class="badge bg-green">↑</span>';}
              else if(array_sum($saldo_diferencia_anio) - array_sum($saldo_diferencia_anio_anterior) == 0){
              echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="orange" sparkfillcolor="#FFE082">'.array_sum($saldo_diferencia_anio_anterior).','.array_sum($saldo_diferencia_anio).'</span><span class="badge bg-yellow">→</span>';}
              else{echo '<span class="sparklines_demo" sparkType="line" sparkwidth="50" sparkheight="22" sparkLineColor="red" sparkfillcolor="#FFCDD2">'.array_sum($saldo_diferencia_anio_anterior).','.array_sum($saldo_diferencia_anio).'</span><span class="badge bg-red">↓</span>';}
            ?>
          </td>
          <?php ?>
        </tr>
      </tbody>
    </table>
  </div>



    <!-- /*/*/*/*/ BOTON IMPRIMIR /*/*/*/*/ -->
    <div class="row no-print">
		<div class="col-xs-12">
		  <a href="javascript:window.print()" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Imprimir </a>
		</div>
	</div>
	<!-- /*/*/*/*/ BOTON IMPRIMIR /*/*/*/*/ -->
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
<script>
  // Line charts taking their values from the tag
    $('.sparkline-1').sparkline('html',
        {type: 'line', width: '6em',});

    $("#sparkline").sparkline([5,6], {
    type: 'line',
    width: '80',
    lineColor: '#ddd7d7',
    fillColor: '#aaffaa',
    lineWidth: 0.2,
    spotColor: '#56aaff'});

    $('.sparklines_demo').sparkline('html', { enableTagOptions: true });
</script>
</html>
