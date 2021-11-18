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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '8');
if($modulos_valida == 0)
{ 
  header('Location: index.php');
}
//////////// VALIDACION PARA VER DETALLES SOLO SI MANDAN DATOS REQUERIDOS
if ( is_null($_GET["id_almacen_cc_ce"]) && is_null($_GET["id_num_cc_ce"]) )
{
  header('Location: cartas_cupo.php');
}
/////////////// INSTANCIA PARA LA CLASE CARTAS CUPO ///////////////
include_once '../class/Cartas_cupo.php';
$obj_ce_cartas_cupo = new Cartascupo();
// SESION PARA TIPO DE CARTA CUPO 
if (isset($_POST['op_ce_cartas_cupo']))
  $_SESSION['op_ce_cartas_cupo'] = $_POST['op_ce_cartas_cupo'];
  $op_ce_cartas_cupo = $_SESSION['op_ce_cartas_cupo'];
/////// VARIABLES CAPTURADA METODO GET  /////////
$id_almacen_cc_ce = $_GET["id_almacen_cc_ce"];
$id_num_cc_ce = $_GET["id_num_cc_ce"];

/*----------------- TITULO PARA CARTAS CUPO --------------------*/
switch ($op_ce_cartas_cupo) {
  case 'E':
    $titulo_cc = "<i class='fa fa-edit'></i> Detalles Cartas cupo expedidas";
    break; 
  case 'RD':
    $titulo_cc = "<i class='ion-android-clipboard'></i> Detalles Cartas cupo arribadas";
    break;
  case 'CC':
    $titulo_cc = "<i class='fa fa-ban'></i> Detalles Cartas cupo canceladas";
    break;
  case 'NA':
    $titulo_cc = "<i class='fa fa-file-text-o'></i> Detalles Cartas cupo no arribadas";
    break;
}
            
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
            <?= $titulo_cc ?>
            <!-- <small class="pull-right">Date: 2/10/2014</small> -->
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info"> 
       <!-- /.col -->
       <div class="col-xs-12 table-responsive">
         <table style="width:100%">
         <?php 
          $tabla_ce_cc = $obj_ce_cartas_cupo->tabla_ce_cc($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc,$op_ce_cartas_cupo,$ce_cc_plaza,$ce_cc_almacen,$id_almacen_cc_ce,$id_num_cc_ce);
          for ($i=0; $i <count($tabla_ce_cc) ; $i++) {   
          ?>
          <tr>
            <th></th>
            <td><b> Numero carta cupo: #<code><?= $tabla_ce_cc[$i]["ID_NUMERO"] ?></code></b></td>
          </tr>
          <tr>
            <!-- inicia code para separa id importador -->
            <?php
            if ($op_ce_cartas_cupo == 'RD'){
              echo '<td><b>Importador:</b>'.$tabla_ce_cc[$i]["IMPORTADOR"].'</td>';
              echo '<td><b>Importador ID:</b>'.$tabla_ce_cc[$i]["ID_IMPORTADOR"].'</td>';
            }else{
              echo '<td><b>Importador:</b><code>('.$tabla_ce_cc[$i]["ID_IMPORTADOR"].')</code>'.$tabla_ce_cc[$i]["IMPORTADOR"].'</td>';
              if ($op_ce_cartas_cupo == 'E'){
                if($tabla_ce_cc[$i]["STATUS_CC"] == 'E'){
                  echo '<td><b>Con Pedimento:</b>NO</td>';
                }else{
                  echo '<td><b>Con Pedimento:</b>SI</td>';
                }
              }else{
                switch ($tabla_ce_cc[$i]["STATUS_CC"]) {
                  case 'CI':
                    echo '<td><b>Tipo de Cancelación:</b>NIVEL INTERNO(CI)</td>';
                    break;
                  case 'CC':
                    echo '<td><b>Tipo de Cancelación:</b>POR CLIENTE O AGENTE ADUANAL(CC)</td>';
                    break;
                  case 'NA':
                    echo '<td><b>Tipo de Cancelación:</b>POR NO ARRIBO(NA)</td>';
                    break;
                }
              }
            }
            ?>
            <!-- termina code para separa id importador -->
          </tr>
          <tr>
            <td><b>Fecha de Expedición:</b> <?= $tabla_ce_cc[$i]["FECHA_EXPEDICION"] ?></td>
            <?php
            switch ($op_ce_cartas_cupo) {
              case 'E':
                $fecha_vencimiento = strtotime ( "+20 day" , strtotime ( $tabla_ce_cc[$i]["FECHA_EXPEDICION"] ) ) ;
                $fecha_vencimiento = date ( "d-m-Y" , $fecha_vencimiento );
                echo "<td><b>Fecha de vencimiento:</b> ".$fecha_vencimiento."</td>";
                break;
              case 'RD': 
                echo "<td><b>Fecha de Arribo:</b> ".$tabla_ce_cc[$i]["FECHA_ARRIBO"]."</td>";
                break;
              case 'CC': 
                echo "<td><b>Fecha de Cancelación:</b> ".$tabla_ce_cc[$i]["FECHA_CANCELACION"]."</td>";
                break;
            } 
            ?> 
          </tr>
          <tr>
            <td><b>Folio carta cupo:</b> <?= $tabla_ce_cc[$i]["CVE_SIDEFI"]."".$tabla_ce_cc[$i]["ID_NUMERO"] ?></td>
            <td><b>SIDEFI:</b> <?= $tabla_ce_cc[$i]["CVE_SIDEFI"] ?></td>
          </tr>
          <tr>
            <td><b>Cve. Aduana:</b> <?= $tabla_ce_cc[$i]["ID_ADU_DESP"] ?></td>
            <td><b>Aduana:</b> <?= $tabla_ce_cc[$i]["ADUANA_DESP"] ?></td>
          </tr>
          <tr>
            <td><b>Plaza:</b> <?= $tabla_ce_cc[$i]["PLAZA"] ?></td>
            <td><b>Almacen:</b> <?= $tabla_ce_cc[$i]["ALMACEN"] ?></td>
          </tr>
         </table>
        </div>
        <!-- /.col -->
      </div>
      <?php } ?>
      <!-- /.row --> 
      <br>
      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th>PARTIDA</th>
              <th>UMT</th> 
              <th>CANTIDAD</th>
              <th>FRACCIONES ARANCELARIAS</th>
              <th>VALOR</th>
            </tr>
            </thead>
            <tbody>
          <?php
            $det_cc_ce = $obj_ce_cartas_cupo->det_cc_ce($id_almacen_cc_ce,$id_num_cc_ce);
            for ($i=0; $i <count($det_cc_ce) ; $i++) { 
            $total_val_dolares[$i] =  $det_cc_ce[$i]["VAL_DOLARES"];
          ?>
            <tr>
              <td><?= $det_cc_ce[$i]["PARTIDA_DET"] ?></td> 
              <td><?= $det_cc_ce[$i]["UM"] ?></td>  
              <td><?= $det_cc_ce[$i]["CANTIDAD_UMT"] ?></td> 
              <td><cite>(<?= $det_cc_ce[$i]["NUM_FRACCION"] ?>)</cite><?= $det_cc_ce[$i]["DES_ARAN"] ?></td> 
              <td>$<?= number_format($det_cc_ce[$i]["VAL_DOLARES"],2) ?></td> 
          <?php
           } $total_val_dolares =  array_sum($total_val_dolares);
           ?>
            </tbody>
          </table> 
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-8"> 
        </div>

        <div class="col-xs-4 table-responsive">
          <table class="table table-striped"> 
          <?php
          for ($i=0; $i <count($tabla_ce_cc) ; $i++) {  
          ?>
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th>Total: </th>
              <th>$<?= number_format($tabla_ce_cc[$i]["V_DOLARES"],2) ?></th>
            </tr> 
          <?php } ?> 
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
 

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
</html>
