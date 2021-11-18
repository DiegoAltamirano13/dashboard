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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '14');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
//////////VAR GET //////////
$id_remate = $_GET["id_remate"];
$remate_status = $_GET["remate_status"];
//////////INSTACIAS//////////
include_once '../class/Remate.php';
$obj_remates_det = new Remate($remates_plaza,$remates_almacen,$id_remate);

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
        <h2 class="page-header">
          <i class="fa fa-legal"></i> Detalle de Remate
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">

    <?php
    //echo $id_remate;
    $remates_det = $obj_remates_det->info_remates($remate_status);
    for ($i=0; $i <count($remates_det) ; $i++) {
    ?>
      <div class="row invoice-info">

        <div class="col-sm-3 invoice-col">
          <address>
            <strong>FECHA DE REGISTRO:</strong><br>
            <?= $remates_det[$i]["FEC_REG"] ?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-3 invoice-col">
          <address>
            <strong>VALOR DE MERCANCÍA:</strong><br>
            <?= "$".number_format($remates_det[$i]["V_ALMONEDA1"],2) ?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-6 invoice-col">
          <address>
            <strong>TIPO DE MERCANCÍA:</strong><br>
            <?= $remates_det[$i]["TIPO_MERCANCIA"] ?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-3 invoice-col">
          <address>
            <strong>CLIENTE:</strong><br>
            <?= $remates_det[$i]["CLIENTE"] ?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-3 invoice-col">
          <address>
            <strong>PLAZA:</strong><br>
            <?= $remates_det[$i]["PLAZA"] ?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-3 invoice-col">
          <address>
            <strong>ALMACEN:</strong><br>
            <?= $remates_det[$i]["ALMACEN"] ?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-3 invoice-col">
          <address>
            <strong>REGIMEN:</strong><br>
            <?= $remates_det[$i]["REGIMEN"] ?>
          </address>
        </div>
        <!-- /.col -->


        <div class="col-sm-4 invoice-col">
          <address>
            <strong>STATUS:</strong><br>
            <?php if ($remates_det[$i]["ID_STATUS"] >= 1 && $remates_det[$i]["ID_STATUS"] <= 9 ) {echo "EN PROCESO";}else{ echo $remates_det[$i]["STATUS"];} ?>
          </address>
        </div>
        <!-- /.col -->
        <?php if ($remates_det[$i]["ID_STATUS"] >= 1 && $remates_det[$i]["ID_STATUS"] <= 9){ ?>
         <div class="col-sm-4 invoice-col">
          <address>
            <strong>ALMONEDA ACTUAL:</strong><br>
            <?= $remates_det[$i]["STATUS"]; ?>
          </address>
        </div>
        <?php } ?>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          <address>
            <strong>PROMOTOR:</strong><br>
            <?= $remates_det[$i]["PROMOTOR"] ?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          <address>
            <strong>OPORTUNIDAD DE VENTA:</strong><br>
            <?php if ($remates_det[$i]["OP_VTA"] == 1 ) {
                    echo "NO";
                  }else {
                    echo "SI";
                  }?>
            <!--<?= $remates_det[$i]["OP_VTA"] ?>-->
          </address>
        </div>
        <!-- /.col -->

      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <i class="fa fa-tag"></i> Almonedas
          </h2>
        </div>
        <!-- /.col -->
      </div>

      <div class="row invoice-info">
      <?php
      if ($remates_det[$i]["FEC_ALMONEDA1"]==true){
        echo '<div class="col-sm-3 invoice-col">';
          echo '<address>';
            echo '<strong>1RA ALMONEDA:</strong><br>';
            echo 'Fecha: <b>'.$remates_det[$i]["FEC_ALMONEDA1"].'</b>';
            echo '<br>Valor: <b>$'.number_format($remates_det[$i]["V_ALMONEDA1"],2).'</b>';
          echo '</address>';
        echo '</div>';
      }
      if ($remates_det[$i]["FEC_ALMONEDA2"]==true){
        echo '<div class="col-sm-3 invoice-col">';
          echo '<address>';
            echo '<strong>2DA ALMONEDA:</strong><br>';
            echo 'Fecha: <b>'.$remates_det[$i]["FEC_ALMONEDA2"].'</b>';
            echo '<br>Valor: <b>$'.number_format($remates_det[$i]["V_ALMONEDA2"],2).'</b>';
          echo '</address>';
        echo '</div>';
      }
      if ($remates_det[$i]["FEC_ALMONEDA3"]==true){
        echo '<div class="col-sm-3 invoice-col">';
          echo '<address>';
            echo '<strong>3RA ALMONEDA:</strong><br>';
            echo 'Fecha: <b>'.$remates_det[$i]["FEC_ALMONEDA3"].'</b>';
            echo '<br>Valor: <b>$'.number_format($remates_det[$i]["V_ALMONEDA3"],2).'</b>';
          echo '</address>';
        echo '</div>';
      }
      if ($remates_det[$i]["FEC_ALMONEDA4"]==true){
        echo '<div class="col-sm-3 invoice-col">';
          echo '<address>';
            echo '<strong>4TA ALMONEDA:</strong><br>';
            echo 'Fecha: <b>'.$remates_det[$i]["FEC_ALMONEDA4"].'</b>';
            echo '<br>Valor: <b>$'.number_format($remates_det[$i]["V_ALMONEDA4"],2).'</b>';
          echo '</address>';
        echo '</div>';
      }
      if ($remates_det[$i]["FEC_ALMONEDA5"]==true){
        echo '<div class="col-sm-3 invoice-col">';
          echo '<address>';
            echo '<strong>5TA ALMONEDA:</strong><br>';
            echo 'Fecha: <b>'.$remates_det[$i]["FEC_ALMONEDA5"].'</b>';
            echo '<br>Valor: <b>$'.number_format($remates_det[$i]["V_ALMONEDA5"],2).'</b>';
          echo '</address>';
        echo '</div>';
      }
      if ($remates_det[$i]["FEC_ALMONEDA6"]==true){
        echo '<div class="col-sm-3 invoice-col">';
          echo '<address>';
            echo '<strong>6TA ALMONEDA:</strong><br>';
            echo 'Fecha: <b>'.$remates_det[$i]["FEC_ALMONEDA6"].'</b>';
            echo '<br>Valor: <b>$'.number_format($remates_det[$i]["V_ALMONEDA6"],2).'</b>';
          echo '</address>';
        echo '</div>';
      }
      if ($remates_det[$i]["FEC_ALMONEDA7"]==true){
        echo '<div class="col-sm-3 invoice-col">';
          echo '<address>';
            echo '<strong>7MA ALMONEDA:</strong><br>';
            echo 'Fecha: <b>'.$remates_det[$i]["FEC_ALMONEDA7"].'</b>';
            echo '<br>Valor: <b>$'.number_format($remates_det[$i]["V_ALMONEDA7"],2).'</b>';
          echo '</address>';
        echo '</div>';
      }
      if ($remates_det[$i]["FEC_ALMONEDA8"]==true){
        echo '<div class="col-sm-3 invoice-col">';
          echo '<address>';
            echo '<strong>8VA ALMONEDA:</strong><br>';
            echo 'Fecha: <b>'.$remates_det[$i]["FEC_ALMONEDA8"].'</b>';
            echo '<br>Valor: <b>$'.number_format($remates_det[$i]["V_ALMONEDA8"],2).'</b>';
          echo '</address>';
        echo '</div>';
      }
      if ($remates_det[$i]["FEC_ALMONEDA9"]==true){
        echo '<div class="col-sm-3 invoice-col">';
          echo '<address>';
            echo '<strong>9NA ALMONEDA:</strong><br>';
            echo 'Fecha: <b>'.$remates_det[$i]["FEC_ALMONEDA9"].'</b>';
            echo '<br>Valor: <b>$'.number_format($remates_det[$i]["V_ALMONEDA9"],2).'</b>';
          echo '</address>';
        echo '</div>';
      }
      ?>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <h4 class="page-header">
            <i class="ion-social-usd"></i> Adeudo por Servicio
          </h4>
        </div>
        <!-- /.col -->
      </div>

      <div class="row invoice-info">

        <div class="col-sm-2 invoice-col">
        </div>
        <div class="col-sm-8 invoice-col">
          <address>
            <table class="table table-bordered table-hover table-striped">
              <tr>
                <th class="small">CONCEPTO</th>
                <th class="small">MONTO</th>
              </tr>
              <?php
                $total_publicaciones = ($remates_det[$i]["PUBLICA1"]+$remates_det[$i]["PUBLICA2"]+$remates_det[$i]["PUBLICA3"]+$remates_det[$i]["PUBLICA4"]+$remates_det[$i]["PUBLICA5"]+$remates_det[$i]["PUBLICA6"]+$remates_det[$i]["PUBLICA7"]+$remates_det[$i]["PUBLICA8"]+$remates_det[$i]["PUBLICA9"]) ;

            /*  if($remate_status == 10){*/
              ?>
              <tr>
                <td class="small">VALOR ADJUDICADO</td>
                <td class="small">$<?=number_format($remates_det[$i]["VAL_ADJUDICADO"],2)?></td>
              </tr>
              <tr>
                <td class="small">ALMACENAJE</td>
                <td class="small">$<?= (number_format($remates_det[$i]["VAL_ADJUDICADO"]-$total_publicaciones-$remates_det[$i]["NOTARIO1"],2)) ?></td>
              </tr>
              <?php /*}*/ ?>
              <tr>
                <td class="small">PUBLICACIONES</td>
                <td class="small"><?= '$'.number_format($total_publicaciones,2) ?>   <small id="click_det_publi" class="btn label label-primary"><i class="fa fa-plus"></i> Detalles</small></td>
              </tr>
              <?php
              if ($remates_det[$i]["PUBLICA1"]==true){
                echo '<tr id="det_publi[]" style="display:none;">';
                echo '<td class="small">Publicacion 1</td>';
                echo '<td class="small">$'.number_format($remates_det[$i]["PUBLICA1"],2).'</td>';
                echo '</tr>';
              }
              if ($remates_det[$i]["PUBLICA2"]==true){
                echo '<tr id="det_publi[]" style="display:none;">';
                echo '<td class="small">Publicacion 2</td>';
                echo '<td class="small">$'.number_format($remates_det[$i]["PUBLICA2"],2).'</td>';
                echo '</tr>';
              }
              if ($remates_det[$i]["PUBLICA3"]==true){
                echo '<tr id="det_publi[]" style="display:none;">';
                echo '<td class="small">Publicacion 3</td>';
                echo '<td class="small">$'.number_format($remates_det[$i]["PUBLICA3"],2).'</td>';
                echo '</tr>';
              }
              if ($remates_det[$i]["PUBLICA4"]==true){
                echo '<tr id="det_publi[]" style="display:none;">';
                echo '<td class="small">Publicacion 4</td>';
                echo '<td class="small">$'.number_format($remates_det[$i]["PUBLICA4"],2).'</td>';
                echo '</tr>';
              }
              if ($remates_det[$i]["PUBLICA5"]==true){
                echo '<tr id="det_publi[]" style="display:none;">';
                echo '<td class="small">Publicacion 5</td>';
                echo '<td class="small">$'.number_format($remates_det[$i]["PUBLICA5"],2).'</td>';
                echo '</tr>';
              }
              if ($remates_det[$i]["PUBLICA6"]==true){
                echo '<tr id="det_publi[]" style="display:none;">';
                echo '<td class="small">Publicacion 6</td>';
                echo '<td class="small">$'.number_format($remates_det[$i]["PUBLICA6"],2).'</td>';
                echo '</tr>';
              }
              if ($remates_det[$i]["PUBLICA7"]==true){
                echo '<tr id="det_publi[]" style="display:none;">';
                echo '<td class="small">Publicacion 7</td>';
                echo '<td class="small">$'.number_format($remates_det[$i]["PUBLICA7"],2).'</td>';
                echo '</tr>';
              }
              if ($remates_det[$i]["PUBLICA8"]==true){
                echo '<tr id="det_publi[]" style="display:none;">';
                echo '<td class="small">Publicacion 8</td>';
                echo '<td class="small">$'.number_format($remates_det[$i]["PUBLICA8"],2).'</td>';
                echo '</tr>';
              }
              if ($remates_det[$i]["PUBLICA9"]==true){
                echo '<tr id="det_publi[]" style="display:none;">';
                echo '<td class="small">Publicacion 9</td>';
                echo '<td class="small">$'.number_format($remates_det[$i]["PUBLICA9"],2).'</td>';
                echo '</tr>';
              }
              ?>
              <tr>
                <td class="small">NOTARIO</td>
                <td class="small"><?= '$'.number_format($remates_det[$i]["NOTARIO1"],2) ?></td>
              </tr>
              <tr>
                <td class="small">SALDO DEUDOR</td>
                <td class="small"><?= '$'.number_format($remates_det[$i]["SALDO_DEUDOR"],2) ?></td>
              </tr>
            </table>
          </address>
        </div>

        <div class="col-sm-2 invoice-col">
        </div>

      </div>

      <!--DETALLES DE VENTA -->
      <?php
      //echo $id_remate;
      $remates_cost = $obj_remates_det->info_costo_destruccion($id_remate);
      for ($i=0; $i <count($remates_cost) ; $i++) {
      ?>
      <div class="row">
        <div class="col-xs-12">
          <h4 class="page-header">
            <i class="ion-social-usd"></i> Costo De Destrucción
          </h4>
        </div>
        <!-- /.col -->
      </div>
      <div class="row invoice-info">

        <div class="col-sm-3 invoice-col">
          <address>
            <strong>FECHA DE DESTRUCCION:</strong><br>
            <?= $remates_cost[$i]["D_FEC_DESTRUCCION"] ?>
          </address>
        </div>

        <div class="col-sm-3 invoice-col">
          <address>
            <strong>COSTO DE DESTRUCCION:</strong><br>$
            <?= number_format($remates_cost[$i]["N_COSTO_DESTRUCCION"], 2) ?>
          </address>
        </div>
      </div>
      <?php } ?>


      <!--Pruebas de tabla 2 historial de ventas-->

      <div class="row">
        <div class="col-xs-12">
          <h4 class="page-header">
            <i class="ion-social-usd"></i> Detalles de Venta
          </h4>
        </div>
        <!-- /.col -->
      </div>

      <div class="row invoice-info">
        <div>
          <address>
            <table class="table table-bordered table-hover table-striped">
              <thead>
                <tr>
                    <th align="center" class="small"># ID</th>
                    <th align="center" class="small">Actividad</th>
                    <th align="center" class="small">Responsable Actividad</th>
                    <th align="center" class="small">Fecha Inicio</th>
                    <th align="center" class="small">Fecha Fin </th>
                </tr>
              </thead>
              <tbody>
                <?php
                //echo $id_remate;
                $OPORTU_VENTA = 0;
                #echo "La oportunidad de venta es = ".$OPORTU_VENTA;
                $infoVentas = $obj_remates_det->info_remates4($id_remate, $OPORTU_VENTA);
                for ($i=0; $i <count($infoVentas) ; $i++) {
                  //  echo $remate_status;
                ?>
                  <tr>
                    <td align="center"><?= $infoVentas[$i]["IID_NUMERO_REGISTRO"] ?></td>
                    <td align="center"><?= $infoVentas[$i]["COMENTARIOS"] ?></td>
                    <td align="center"><?= $infoVentas[$i]["NOMBRE"] ?></td>
                    <td align="center"><?= $infoVentas[$i]["FECHA_INI_PROSPECCION"] ?></td>
                    <td align="center"><?= $infoVentas[$i]["FECHA_LIMITE_VENTA"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </address>
        </div>

        <div class="col-sm-2 invoice-col">
        </div>

      </div>

      <div class="row">
        <div class="col-xs-12">
          <h4 class="page-header">
            <i class="iconify"></i> Comentarios
          </h4>
        </div>
        <!-- /.col -->
      </div>

      <div class="row invoice-info">
        <div>
          <address>
            <table class="table table-bordered table-hover table-striped">
              <thead>
                <tr>
                    <th align="center" class="small"># ID</th>
                    <th align="center" class="small">Actividad</th>
                    <th align="center" class="small">Responsable Actividad</th>
                    <th align="center" class="small">Fecha Inicio</th>
                    <th align="center" class="small">Fecha Fin </th>
                </tr>
              </thead>
              <tbody>
                <?php
                //echo $id_remate;
                $OPORTU_VENTA = 1;
                #echo "La oportunidad de venta es = ".$OPORTU_VENTA;
                $infoVentas = $obj_remates_det->info_remates4($id_remate, $OPORTU_VENTA);
                for ($i=0; $i <count($infoVentas) ; $i++) {
                  //  echo $remate_status;
                ?>
                  <tr>
                    <td align="center"><?= $infoVentas[$i]["IID_NUMERO_REGISTRO"] ?></td>
                    <td align="center"><?= $infoVentas[$i]["COMENTARIOS"] ?></td>
                    <td align="center"><?= $infoVentas[$i]["NOMBRE"] ?></td>
                    <td align="center"><?= $infoVentas[$i]["FECHA_INI_PROSPECCION"] ?></td>
                    <td align="center"><?= $infoVentas[$i]["FECHA_LIMITE_VENTA"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </address>
        </div>

        <div class="col-sm-2 invoice-col">
        </div>

      </div>


    <?php } ?>

    </div>
    <!-- /.row -->


  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $("#click_det_publi").click(function() {
      $("#det_publi\\[\\]").toggle();
    });
  });
</script>
