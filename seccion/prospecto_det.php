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
          <i class="fa fa-edit"></i> Detalles del Prospecto 
          <!-- <small class="pull-right">Date: 2/10/2014</small> -->
        </h5>
      </div>
      <!-- /.col -->
    </div>

    <?php
    $prospecto_det = $ins_objeto_comercial->tabla_prospectos($co_plaza,$t_prospecto_co,$t_servicio_co,$promotor_co,$anio_co,$id_prospecto_co,$grafica_co_pros,$co_top_promo);
    	for ($i=0; $i <count($prospecto_det) ; $i++) {  
    ?>
    <div class="row">  
        <div class="col-xs-12 table-responsive">
         <p class="lead"><i class="fa fa-user"></i> Datos Generales:</p>
          <table class="table table-hover"> 
            <tr> 
              <th>#Prospecto:</th>
              <td><?= $prospecto_det[$i]["ID_PROSPECTO"] ?></td>
              <th>Razón Social:</th>
              <td><?= $prospecto_det[$i]["PROSPECTO"] ?></td>
              <th>Régimen Fiscal:</th>
              <!-- INICIA CODE TIPO DE REGIMEN FISCAL -->
              <?php
              	switch ($prospecto_det[$i]["REGIMEN_FIS"]) {
              		case 1:
              			echo "<td>PERSONA FISICA</td>";
              			break;
              		case 2:
              			echo "<td>PERSONA MORAL</td>";
              			break; 
              		default:
              			echo "<td>NO DEFINIDO</td>";
              			break;
              	}
              ?> 
              <!-- TERMINA CODE TIPO DE REGIMEN FISCAL --> 
            </tr>
            <tr>
              <th>Plaza</th>
              <td><?= $prospecto_det[$i]["PLAZA"] ?></td>
              <th>Almacen:</th>
              <td><?= $prospecto_det[$i]["ALMACEN"] ?></td>
              <th>Pais:</th>
              <td><?= $prospecto_det[$i]["PAIS"] ?></td>
            </tr> 
            <tr>
              <th>Estado:</th>
              <td><?= $prospecto_det[$i]["ESTADO"] ?></td>
              <th>Ciudad:</th>
              <td><?= $prospecto_det[$i]["CIUDAD"] ?></td>
              <th>C.P</th>
              <td><?= $prospecto_det[$i]["CP"] ?></td>
            </tr> 
            <tr>
              
              <th>Dirección:</th>
              <td><?= $prospecto_det[$i]["DIRECCION"] ?></td>
              <th>Teléfonos:</th>
              <td><?= "(".$prospecto_det[$i]["TELEFONO1"].") (".$prospecto_det[$i]["TELEFONO2"].")" ?></td>
              <th>Email:</th>
              <td><?= $prospecto_det[$i]["EMAIL"] ?></td>
            </tr>  
          </table>
        </div> 
      </div>

      <!-- /.col -->
      <div class="row">
      	<div class="col-xs-12 table-responsive">
        <p class="lead"><i class="fa fa-user-plus"></i> Información Adicional:</p> 
          <table class="table table-hover">
            <tr>
              <th>Fecha de Registro:</th>
              <td><?= $prospecto_det[$i]["FEC_REG"] ?></td>
              <!-- INICIA CODE TIEMPO TRANSCURRIDO -->
              <?php
			  $fechaInicio = $prospecto_det[$i]["FEC_REG"] ;
              $fechaFin = date('d-m-Y') ;
              $tiempo_trascurrido = $ins_objeto_comercial->tiempoTranscurridoFechas($fechaInicio,$fechaFin);
              ?>
              <!-- TERMINA CODE TIEMPO TRANSCURRIDO -->
              <th>Tiempo Transcurrido:</th>
              <td><?= $tiempo_trascurrido ?></td>
            </tr>
            <tr>
              <th>Cierre</th>
              <td><?= ($prospecto_det[$i]["PORC_CIERRE"]*100) ?>%</td>
              <th>Plazo de Cierre:</th>
              <!-- inicia code plazo de cierre -->
              <?php
                switch ($prospecto_det[$i]["PLAZO_CIERRE"]) {
                  case 1:
                    echo "<td>CORTO</td>";
                    break;
                  case 2:
                    echo "<td>MEDIANO</td>";
                    break;
                  case 3:
                    echo "<td>LARGO PLAZO</td>";
                    break;  
                  default:
                    echo "<td>NO DEFINIDO</td>";
                    break;
                }
              ?>
              <!-- termina code plazo de cierre --> 
            </tr>
            <tr>
              <th>Valor Aproximado:</th>
              <td><?= $prospecto_det[$i]["VALOR_APRO"] ?></td>
              <th>Facuración Estimada:</th>
              <td><?= $prospecto_det[$i]["FAC_ESTIMADA"] ?></td>
            </tr>
            <tr>
              <th>Tipo de CLiente:</th> 
              <td><?= $prospecto_det[$i]["TIPO_PROS"] ?></td>
              <th>Tipo de Servicio:</th>
              <td><?= $prospecto_det[$i]["TIPO_SERVICIO"] ?></td>
            </tr>
            <tr>
              <th>Volumen:</th>
              <td><?= $prospecto_det[$i]["VOLUMEN"] ?></td>
              <th>Rotación:</th>
              <td><?= $prospecto_det[$i]["ROTACION"] ?></td>
            </tr> 
            <tr>
              <th>Origen:</th>
              <td><?= $prospecto_det[$i]["ORIGEN"] ?></td>
              <th>Mercancia:</th>
              <td><?= $prospecto_det[$i]["MERCANCIA"] ?></td>
            </tr> 
          </table> 
        </div> 
      <!-- /.col -->
      <?php } ?>
    </div>
    <!-- /.row -->

    <!-- /.col -->
      <div class="row">
      	<div class="col-xs-12 table-responsive">
        <p class="lead"><i class="fa fa-phone-square"></i> Contacto:</p> 
          <table class="table table-hover">
          <?php
          $contacto_prospecto = $ins_objeto_comercial->contacto_prospecto($id_prospecto_co);
          	for ($i=0; $i <count($contacto_prospecto) ; $i++) {  
          ?>
            <tr>
              <th>Nombre:</th>
              <td><?= $contacto_prospecto[$i]["CON_PROM_NOMBRE"] ?></td>
              <th>Puesto:</th>
              <td><?= $contacto_prospecto[$i]["CON_PROM_PUESTO"] ?></td>
            </tr>
            <tr>
              <th>Teléfono 1:</th>
              <td><?= $contacto_prospecto[$i]["CON_PROM_TEL1"] ?></td>
              <th>Teléfono 2:</th>
              <td><?= $contacto_prospecto[$i]["CON_PROM_TEL2"] ?></td>
            </tr>
            <tr>
              <th>Email:</th>
              <td><?= $contacto_prospecto[$i]["CON_PROM_EMAIL"] ?></td>
            </tr> 
            <?php } ?>
          </table>
        </div> 
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- /.col -->
    <?php
    for ($i=0; $i <count($prospecto_det) ; $i++) { 
    ?>
      <div class="row">
      	<div class="col-xs-12 table-responsive">
        <p class="lead"><i class="fa fa-briefcase"></i> Promotor:</p> 
          <table class="table table-hover">
            <tr>
              <th>Nombre:</th>
              <td><span class="text-muted">(<?= $prospecto_det[$i]["ID_PROMO"] ?>)</span> <?= $prospecto_det[$i]["NOM_PROM"]." ".$prospecto_det[$i]["APEPAT_PROM"]." ".$prospecto_det[$i]["APEMAT_PROM"] ?></td>
              <th>Status:</th>
              <!-- INICIA CODE STATUS PROMOTOR -->
              <?php
              switch ($prospecto_det[$i]["STATUS_PROM"]) {
              	case 1:
              		echo "<td>ACTIVO</td>";
              		break;
              	case 9:
              		echo "<td>BAJA</td>";
              		break;
              	
              	default:
              		echo "<td>NULL</td>";
              		break;
              }
              ?>
              <!-- TERMINA CODE STATUS PROMOTOR --> 
            </tr>
            <tr>
              <th>Celular:</th>
              <td><?= $prospecto_det[$i]["CEL_PROM"] ?></td>
              <th>Teléfono:</th>
              <td><?= $prospecto_det[$i]["TEL_PROM"] ?></td>
            </tr>
            <tr>
              <th>Email:</th>
              <td><?= $prospecto_det[$i]["EMAIL_PROM"] ?></td>
            </tr> 
          </table>
        </div> 
      <!-- /.col -->
    </div>
    <?php } ?>
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
