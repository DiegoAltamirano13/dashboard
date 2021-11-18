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

if( is_null($_GET["filtro"]) )
{ 
  header('Location: index.php');
} 
/*----------------------SESIONES PARA SALDOS DE CLIENTES----------------------*/
 

//////////////// INICIA INSTANCIAS /////////////////////////// 
include_once '../class/Saldo_cliente.php';
$ins_obj_saldos_clientes = new Saldo_cliente_detalle();
$saldos_plaza_det = $ins_obj_saldos_clientes->saldos_plaza_det($_GET["plaza"],$_GET["cliente"]);
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

</head>
<body>
<?php 
if ($_GET['filtro'] == true){
  switch ($_GET['filtro']) {
    case 1:
      $resaltado1 = '<mark><b>';
      break;
    case 2:
      $resaltado2 = '<mark><b>';
      break;
    case 3:
      $resaltado3 = '<mark><b>';
      break;
    case 4:
      $resaltado4 = '<mark><b>';
      break;
    case 5:
      $resaltado5 = '<mark><b>';
      break;
  }
  
}
?>
<div class="wrapper">
  <!-- Main content -->
    <section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
           <?php
            
           

            if ( $_GET["cliente"] == true  && $_GET["plaza"] == false ){
              for ($i=0; $i <count($saldos_plaza_det) ; $i++) {
              echo '<small class="page-header"><i class="fa fa-fw fa-plus"></i>DETALLE DE SALDO TOTAL CLIENTE ('.$saldos_plaza_det[$i]["ID_CLIENTE"].')'.$saldos_plaza_det[$i]["CLIENTE"].'</small>';
              }
            }else if ( $_GET["cliente"] == true  && $_GET["plaza"] == true ){
              for ($i=0; $i <count($saldos_plaza_det) ; $i++) {
              echo '<small class="page-header"><i class="fa fa-fw fa-plus"></i>DETALLE DE SALDO TOTAL CLIENTE ('.$saldos_plaza_det[$i]["ID_CLIENTE"].')'.$saldos_plaza_det[$i]["CLIENTE"].' EN PLAZA '.$_GET["plaza"].'</small>';
              }
            }else if ( $_GET["cliente"] == false  && $_GET["plaza"] == true ){
              echo '<small class="page-header"><i class="fa fa-fw fa-plus"></i>DETALLE DE SALDO TOTAL EN PLAZA '.$_GET["plaza"].'</small>';
            }
           ?> 
          </h2>

          <div class="table-responsive">
            <table class="table no-margin table-bordered">
              <thead>
              <tr>
                <th class="small">SALDO</th>
                <th class="small">NO VENCIDO</th>
                <th class="small">1-30 DÍAS</th>
                <!-- <th class="small">16-30 DÍAS</th> -->
                <th class="small"><i class="fa fa-fw fa-plus"></i>DE 30 DÍAS</th>
                <th class="small"><i class="fa fa-fw fa-plus"></i>DE 60 DÍAS</th>
                <th class="small"><i class="fa fa-fw fa-plus"></i>DE 90 DÍAS</th>
                <th class="small">VENCIDO</th>
              </tr>
              </thead>
              <tbody>
              <?php for ($i=0; $i <count($saldos_plaza_det) ; $i++) { ?>
              <tr>
              <?php
              // *-*-*-*-*-*-*-*-*-*-*-*-*-* TD SALDO *-*-*-*-*-*-*-*-*-*-*-*-*-* //
              if (is_null($saldos_plaza_det[$i]["MONTO"])){
                echo '<td class="small" bgcolor="#ffff80">$'.number_format($saldos_plaza_det[$i]["SALDO"],2).'</td>';
              }else{
                echo '<td class="small" bgcolor="#ffff80">$'.number_format($saldos_plaza_det[$i]["MONTO"],2).'</td>';
              }
              // *-*-*-*-*-*-*-*-*-*-*-*-*-* TD SALDO NO VENCIDO *-*-*-*-*-*-*-*-*-*-*-*-*-* //
              if (is_null($saldos_plaza_det[$i]["MONTO_NO_VENCIDO"])){
                echo '<td class="small" bgcolor="#c0c080">$'.number_format($saldos_plaza_det[$i]["SALDO_NO_VENCIDO"],2).'</td>';
              }else{
                echo '<td class="small" bgcolor="#c0c080">$'.number_format($saldos_plaza_det[$i]["MONTO_NO_VENCIDO"],2).'</td>';
              }
              // *-*-*-*-*-*-*-*-*-*-*-*-*-* TD 1-15 DIAS *-*-*-*-*-*-*-*-*-*-*-*-*-* //
              if (is_null($saldos_plaza_det[$i]["MONTO_1_15"])){
                //echo '<td class="small" bgcolor="#80c0c0">'.$resaltado1.'$'.number_format($saldos_plaza_det[$i]["SALDO_1_15"],2).'</td>';
                $total_saldo_1_15 = $saldos_plaza_det[$i]["SALDO_1_15"];
              }else{
                //echo '<td class="small" bgcolor="#80c0c0">'.$resaltado1.'$'.number_format($saldos_plaza_det[$i]["MONTO_1_15"],2).'</td>';
                $total_saldo_1_15 = $saldos_plaza_det[$i]["MONTO_1_15"];
              }
              // *-*-*-*-*-*-*-*-*-*-*-*-*-* TD 1-30 DIAS *-*-*-*-*-*-*-*-*-*-*-*-*-* //
              if (is_null($saldos_plaza_det[$i]["MONTO_16_30"])){
                $total_saldo_16_30 = $saldos_plaza_det[$i]["SALDO_16_30"];
                //echo '<td class="small" bgcolor="#d3e5f8">'.$resaltado1.'$'.number_format($total_saldo_1_15+$total_saldo_16_30,2).'</td>';
              }else{
                $total_saldo_16_30 = $saldos_plaza_det[$i]["MONTO_16_30"];
                //echo '<td class="small" bgcolor="#d3e5f8">'.$resaltado1.'$'.number_format($total_saldo_1_15+$total_saldo_16_30,2).'</td>';
              }
                echo '<td class="small" bgcolor="#d3e5f8">'.$resaltado1.'$'.number_format($total_saldo_1_15+$total_saldo_16_30,2).'</td>';
              // *-*-*-*-*-*-*-*-*-*-*-*-*-* TD MAS 31 DIAS *-*-*-*-*-*-*-*-*-*-*-*-*-* // MAS_31
              if (is_null($saldos_plaza_det[$i]["MONTO_MAS_31"])){
                echo '<td class="small" bgcolor="#80ffff">'.$resaltado2.'$'.number_format($saldos_plaza_det[$i]["SALDO_MAS_31"],2).'</td>';
                $total_mas_31 = $saldos_plaza_det[$i]["SALDO_MAS_31"];
              }else{
                echo '<td class="small" bgcolor="#80ffff">'.$resaltado2.'$'.number_format($saldos_plaza_det[$i]["MONTO_MAS_31"],2).'</td>';
                $total_mas_31 = $saldos_plaza_det[$i]["MONTO_MAS_31"];
              }
              // *-*-*-*-*-*-*-*-*-*-*-*-*-* TD MAS 61 DIAS *-*-*-*-*-*-*-*-*-*-*-*-*-* // MAS_31
              if (is_null($saldos_plaza_det[$i]["MONTO_MAS_61"])){
                echo '<td class="small" bgcolor="#e0e0e0">'.$resaltado3.'$'.number_format($saldos_plaza_det[$i]["SALDO_MAS_61"],2).'</td>';
                $total_mas_61 = $saldos_plaza_det[$i]["SALDO_MAS_61"];
              }else{
                echo '<td class="small" bgcolor="#e0e0e0">'.$resaltado3.'$'.number_format($saldos_plaza_det[$i]["MONTO_MAS_61"],2).'</td>';
                $total_mas_61 = $saldos_plaza_det[$i]["MONTO_MAS_61"];
              }
              // *-*-*-*-*-*-*-*-*-*-*-*-*-* TD MAS 91 DIAS *-*-*-*-*-*-*-*-*-*-*-*-*-* // MAS_31
              if (is_null($saldos_plaza_det[$i]["MONTO_MAS_91"])){
                echo '<td class="small" bgcolor="#80ff80">'.$resaltado4.'$'.number_format($saldos_plaza_det[$i]["SALDO_MAS_91"],2).'</td>';
                $total_mas_91 = $saldos_plaza_det[$i]["SALDO_MAS_91"];
              }else{
                echo '<td class="small" bgcolor="#80ff80">'.$resaltado4.'$'.number_format($saldos_plaza_det[$i]["MONTO_MAS_91"],2).'</td>';
                $total_mas_91 = $saldos_plaza_det[$i]["MONTO_MAS_91"];
              }
              ?>
              <td class="small" bgcolor="#E99B92">$<?= number_format($total_saldo_1_15+$total_saldo_16_30+$total_mas_31+$total_mas_61+$total_mas_91,2) ?></td>
              </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>

        </div>
        <!-- /.col -->
      </div>
       

      
 
 
    </section>
    <!-- /.content -->
</div>
<!-- ./wrapper -->

</body>
</html>
<?php conexion::cerrar($conn); ?>