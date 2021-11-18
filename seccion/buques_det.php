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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '38');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
//////////////////////
include_once '../class/Mapas_Operaciones.php';
$ins_objeto_comercial = new Mapas_Operaciones();
/////////////////////////////
$iid_emple = $_GET["iid_emple"];
if ($iid_emple == false){
  header('Location: lista_Personal.php');
}
/////////// SESIONES DE TOP PROMOTOR SELECCIONA //////////////////
if(isset($_POST['co_top_promo']))
    $_SESSION['co_top_promo'] = $_POST['co_top_promo'];
    $co_top_promo = $_SESSION['co_top_promo'];
/////////// SESIONES DE GRAFICA SELECCIONA //////////////////
if(isset($_POST['iid_emple']))
    $_SESSION['iid_emple'] = $_POST['iid_emple'];
    $iid_empleado = $_SESSION['iid_emple'];

    #$carga_ImagenDañados = $descargaImagen->descargaImagenDestruccion();

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
  <link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
  <link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h5 class="page-header text-center">
          <i class="fa fa-ship"></i> Detalles del Buque
          <!-- <small class="pull-right">Date: 2/10/2014</small> -->
        </h5>
      </div>
      <!-- /.col -->
    </div>

    <?php
    $prospecto_det = $ins_objeto_comercial->buquesDetallados($iid_emple);
    	for ($i=0; $i <count($prospecto_det) ; $i++) {
    ?>
    <div class="row">
        <div class="col-xs-12 table-responsive">
         <p class="lead"><i class="fa fa-ship"></i> Datos Generales:</p>
          <table class="table table-hover">
            <tr>
              <th>CLIENTE:</th>
              <td colspan="3"><?= $prospecto_det[$i]["CLIENTEVR"] ?></td>
              <!-- TERMINA CODE TIPO DE REGIMEN FISCAL -->
            </tr>
            <tr>
              <th>NUM CDT:</th>
              <td style="text-align:left"><?= $prospecto_det[$i]["IID_NUM_CERT_S"] ?></td>
              <th>TENEDOR:</th>
              <td style="text-align:left"><?= $prospecto_det[$i]["V_TENEDOR"] ?></td>
              <th>TONELADAS:</th>
              <td style="text-align:left"><?= number_format($prospecto_det[$i]["N_CANTIDAD"], 2) ?></td>
            </tr>
            <tr>
              <th>MERCANCIA</th>
              <td><?= $prospecto_det[$i]["V_DESCRIPCION"] ?></td>
              <th>FACTURA:</th>
              <td><?= $prospecto_det[$i]["V_FACTURA"] ?></td>
            </tr>
            <tr>
              <th>VALOR:</th>
              <td><?= number_format($prospecto_det[$i]["N_TOTAL_VALOR_DEC"], 2) ?></td>
              <th>DESTINO:</th>
              <td><?= $prospecto_det[$i]["N_DESTINO"] ?></td>
            </tr>
            <tr>
              <th>NOMBRE BUQUE:</th>
              <td><?= $prospecto_det[$i]["V_NOM_BUQUE"] ?></td>
              <th>ARRIBO ESTIMADO:</th>
              <td><?= $prospecto_det[$i]["ARRIBO_ESTIMADO"] ?></td>
            </tr>
            <tr>
              <th>EMISION CDT:</th>
              <td><?= $prospecto_det[$i]["FECHAEMISION"] ?></td>
              <th>FONDEO :</th>
              <td><?= $prospecto_det[$i]["FECHA_FONDEO"] ?></td>
              <th>ATRAQUE :</th>
              <td><?= $prospecto_det[$i]["FECHA_ATRAQUE"] ?></td>
            </tr>
            <tr>
              <th>TRASLADO :</th>
              <td><?= $prospecto_det[$i]["FECHA_TRASLADO"] ?></td>
            </tr>
          </table>
        </div>
      </div>

    <!-- /.col -->
    <?php
      $vid_certificado = $prospecto_det[$i]["V_ID_RECIBO"];
    } ?>

    <div class="table-responsive">
      <table id="tabla_activos" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small" bgcolor="#0073B7"><font color="white">ARRIBOS</font></th>
            <th class="small" bgcolor="#0073B7"><font color="white">CD</font></th>
            <th class="small" bgcolor="#0073B7"><font color="white">ALMACEN</font></th>
            <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
            <th class="small" bgcolor="#0073B7"><font color="white">CANTIDAD ARRIBADA</font></th>
          </tr>
        </thead>
        <tbody>
          <?php
            $tabla_det = $ins_objeto_comercial->tablaDetallados($vid_certificado);
            for ($i=0; $i <count($tabla_det) ; $i++) { ?>
          <tr>
            <td><?= $tabla_det[$i]["D_PLAZO_DEP_INI"]?></td>
            <td><?= $tabla_det[$i]["VID_CERTIFICADO"] ?></td>
            <td><?= $tabla_det[$i]["V_NOMBRE"] ?></td>
            <td><?= $tabla_det[$i]["V_DESCRIPCION"] ?></td>
            <td><?= number_format($tabla_det[$i]["CANTIDAD"], 2) ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
	<!-- /*/*/*/*/ BOTON IMPRIMIR /*/*/*/*/ -->
  </section>
<!-- ./wrapper -->

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
<script src="../plugins/datatables/extensions/buttons_datatable/buttons.print.min.js"></script>>


</body>
</html>
