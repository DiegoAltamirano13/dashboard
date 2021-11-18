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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '19');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
//////////////////////
include_once '../class/Lista_Personal.php';
$ins_objeto_comercial = new ListaPersonal();
/////////////////////////////
$iid_emple = $_GET["iid_emple"];
if ($iid_emple == false){
  header('Location: lista_Personal.php');
}
$iid_tipo = $_GET["tipo"];
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


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
<style type="text/css">
  #mapCanvas {
    width: 100%;
    height: 100%;
}
    </style>
</head>
<body>
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h5 class="page-header text-center">
          <i class="fa fa-edit"></i> Detalles del Empleado
          <!-- <small class="pull-right">Date: 2/10/2014</small> -->
        </h5>
      </div>
      <!-- /.col -->
    </div>

    <?php
    $prospecto_det = $ins_objeto_comercial->empleados_Detalle($iid_emple, $iid_tipo);
    	for ($i=0; $i <count($prospecto_det) ; $i++) {
    ?>
    <div class="row">
        <div class="col-xs-12 table-responsive">
         <p class="lead"><i class="fa fa-user"></i> Datos Generales:</p>
          <table class="table table-hover">
            <tr>
              <th>
                <td colspan="2">
                  <img src="../imagenes_Empleados/empleado_<?= $prospecto_det[$i]["IID_EMPLEADO"] ?>.jpg" height="250" width="250">
                </td>
              </th>
              <th>EMPLEADO:</th>
              <td colspan="3"><?= $prospecto_det[$i]["NOMBRE"] ?></td>
              <!-- TERMINA CODE TIPO DE REGIMEN FISCAL -->
            </tr>
            <tr>
              <th>SEXO:</th>
              <!-- INICIA CODE TIPO DE REGIMEN FISCAL -->
              <?php
              	switch ($prospecto_det[$i]["V_SEXO"]) {
              		case 1:
              			echo "<td>MUJER</td>";
              			break;
              		case 2:
              			echo "<td>HOMBRE</td>";
              			break;
              		default:
              			echo "<td>NO DEFINIDO</td>";
              			break;
              	}
              ?>
              <th>EDAD:</th>
              <td style="text-align:left"><?= $prospecto_det[$i]["N_EDAD"] ?> Años</td>
            </tr>
            <tr>
              <th>IMSS</th>
              <td><?= $prospecto_det[$i]["V_IMSS"] ?></td>
              <th>RFC:</th>
              <td><?= $prospecto_det[$i]["V_RFC"] ?></td>
              <th>CURP:</th>
              <td><?= $prospecto_det[$i]["V_CURP"] ?></td>
            </tr>
            <tr>
              <th>Estado:</th>
              <td><?= $prospecto_det[$i]["V_ESTADO"] ?></td>
              <th>Ciudad:</th>
              <td><?= $prospecto_det[$i]["V_CIUDAD"] ?></td>
              <th>C.P</th>
              <td><?= $prospecto_det[$i]["V_CP"] ?></td>
            </tr>
            <tr>

              <th>Dirección:</th>
              <td><?php echo $prospecto_det[$i]["V_DOMICILIO"]."</br>";
                                                        if (!empty($prospecto_det[$i]["V_NUMERO"])) { echo " #CASA: ".$prospecto_det[$i]["V_NUMERO"]."</br>"; }
                                                        if (!empty($prospecto_det[$i]["V_DEPTO"])) { echo " #DEPTO: ".$prospecto_det[$i]["V_DEPTO"]."</br>"; }
                                                        if (!empty($prospecto_det[$i]["V_COLONIA"])) { echo " COLONIA: ".$prospecto_det[$i]["V_COLONIA"]."</br>"; }
                                                        if (!empty($prospecto_det[$i]["V_ENTRE_CALLES"])) { echo " ENTRE CALLES: ".$prospecto_det[$i]["V_ENTRE_CALLES"]."</br>";}
              ?></td>
              <th>Teléfonos:</th>
              <td><?= "(".$prospecto_det[$i]["V_TELEFONO1"].") (".$prospecto_det[$i]["V_TELEFONO2"].")" ?></td>
              <th>Email:</th>
              <td><?= $prospecto_det[$i]["V_EMAIL"] ?></td>
            </tr>
          </table>
        </div>
      </div>

      <!-- /.col -->
      <div class="row">
      	<div class="col-xs-12 table-responsive">
        <p class="lead"><i class="fa fa-user-plus"></i> Información Laboral:</p>
          <table class="table table-hover">
            <tr>
              <th>Plaza:</th>
              <td><?= $prospecto_det[$i]["PLAZA"] ?></td>
              <th>Lugar Trabajo:</th>
              <td><?= $prospecto_det[$i]["LUGAR_TRABAJO"] ?></td>
              <th>Fecha de Registro:</th>
              <td><?= $prospecto_det[$i]["D_FECHA_INGRESO"] ?></td>
            </tr>
            <tr>
              <th>Departamento:</th>
              <td><?= $prospecto_det[$i]["DEPTO"] ?></td>
              <th>Area:</th>
              <td><?= $prospecto_det[$i]["AREA"] ?></td>
              <th>Puesto:</th>
              <td><?= $prospecto_det[$i]["PUESTO"] ?></td>
            </tr>
            <tr>
              <th>Antiguedad:</th>
              <td><?= $prospecto_det[$i]["I_ANTIGUEDAD"] ?></td>
              <th>Salario Mensual:</th>
              <td>$<?= number_format($prospecto_det[$i]["C_SALARIO_MENSUAL"], 2) ?></td>
              <th>Evaluacion</th>
              <td><?= number_format($prospecto_det[$i]["C_CALIFICACION"], 2) ?></td>
            </tr>
          </table>
        </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
      <p class="lead"><i class="fa fa-user-plus"></i> Informacion Personal Y Profesional:</p>
        <table class="table table-hover">
          <tr>
            <th>Escuela</th>
            <td><?= $prospecto_det[$i]["V_NOMBRE_ESCUELA"] ?></td>
            <th>Nivel Escolaridad:</th>
            <td><?= $prospecto_det[$i]["NIVEL_ESC"] ?></td>
            <th>Estado Civil:</th>
            <?php
              switch ($prospecto_det[$i]["V_EDO_CIVIL"]) {
                case 1:
                  echo "<td>Casado</td>";
                  break;
                case 2:
                  echo "<td>Soltero</td>";
                  break;
                default:
                  echo "<td>NO DEFINIDO</td>";
                  break;
              }
            ?>
          </tr>
          <tr>
            <th>Numero De Hijos:</th>
            <td><?= number_format($prospecto_det[$i]["N_NUM_HIJOS"]) ?></td>
          </tr>
          <tr>
            <th>Pasatiempo:</th>
            <td><?= $prospecto_det[$i]["V_PASATIEMPO"] ?></td>
          </tr>
          <tr>
            <th>Transporte:</th>
            <td><?= $prospecto_det[$i]["V_TIPO_TRANSPORTE"] ?></td>
            <th>Tiempo Transporte (Casa - Trabajo):</th>
            <td><?= $prospecto_det[$i]["V_TIEMPO_CASA_TRABAJO"] ?></td>
          </tr>
        </table>
      </div>
    </div>

    <?php if ($iid_tipo == 2) { ?>

    <div class="row">
      <div class="col-xs-12 table-responsive">
      <p class="lead">Motivos de despido:</p>
        <table class="table table-hover">
          <tr>
            <th>Fecha Salida</th>
            <td><?= $prospecto_det[$i]["FECHA_CANCELACION"] ?></td>
            <th>Motivo Despido</th>
            <td><?= $prospecto_det[$i]["OBSERVACION_DESPIDO"] ?></td>
          </tr>
        </table>
      </div>
    </div>

    <?php } ?>

    <div class="row">
      <div class="row">
        <div class="col-xs-12 table-responsive">
        <p class="lead">Referencias Personales:</p>
          <table class="table table-hover">
      <?php
        $prospecto_det_ref = $ins_objeto_comercial->empleados_Detalle_Ref($iid_emple, 1);
        for ($o=0; $o <count($prospecto_det_ref) ; $o++) {
       ?>

             <tr>
               <th>Nombre:</th>
               <td><?= $prospecto_det_ref[$o]["V_NOMBRE"] ?></td>
               <th>Domicilio:</th>
                <td><?= $prospecto_det_ref[$o]["V_DOMICILIO"] ?></td>
                <th>Telefono:</th>
                 <td><?= $prospecto_det_ref[$o]["V_TELEFONO"] ?></td>
            </tr>
            <tr>
                 <th>Ocupacion:</th>
                  <td><?= $prospecto_det_ref[$o]["V_OCUPACION"] ?></td>
             </tr>
           <?php } ?>
         </table>
       </div>
      </div>
    </div>

    <div class="row">
      <div class="row">
        <div class="col-xs-12 table-responsive">
        <p class="lead">Referencias Laborales:</p>
          <table class="table table-hover">
      <?php
        $prospecto_det_ref = $ins_objeto_comercial->empleados_Detalle_Ref($iid_emple, 2);
        for ($o=0; $o <count($prospecto_det_ref) ; $o++) {
       ?>

             <tr>
               <th>Nombre:</th>
               <td><?= $prospecto_det_ref[$o]["V_NOMBRE"] ?></td>
               <th>Domicilio:</th>
                <td><?= $prospecto_det_ref[$o]["V_DOMICILIO"] ?></td>
                <th>Telefono:</th>
                 <td><?= $prospecto_det_ref[$o]["V_TELEFONO"] ?></td>
            </tr>
            <tr>
                 <th>Ocupacion:</th>
                  <td><?= $prospecto_det_ref[$o]["V_OCUPACION"] ?></td>
             </tr>
           <?php } ?>
         </table>
       </div>
      </div>
    </div>

    <div class="row">
      <?php
        $latitud = $prospecto_det[$i]['LATITUD'];
        $longitud = $prospecto_det[$i]['LONGITUD'];
        if (is_null($latitud)) {

        }else {


      ?>
      <p class="lead"><i class="fa fa-map-marker"></i>Domicilio: </p>
        <a href='<?php echo "https://www.google.es/maps/place/$latitud,$longitud"; ?>' target="_blank"><button class="map">Ver en google maps</button></a>
      <?php
        }
       ?>
    </div>


    <script>

    var greenIcon = L.icon({
        iconUrl: 'uploads/logo.png',
        iconSize:     [38, 35], // size of the icon
        shadowSize:   [50, 64], // size of the shadow
        iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
        shadowAnchor: [4, 62],  // the same for the shadow
        popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
    });


	var mymap = L.map('mapCanvas').setView([<?php echo $prospecto_det[$i]["LATITUD"]; ?>, <?php echo $prospecto_det[$i]["LONGITUD"]; ?>], 16);

	//L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {

		maxZoom: 30,
    attribution:
     '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
		id: 'mapbox/streets-v11',
		tileSize: 512,
		zoomOffset: -1
	}).addTo(mymap);


	L.marker([<?php echo $prospecto_det[$i]["LATITUD"]; ?>, <?php echo $prospecto_det[$i]["LONGITUD"]; ?>]).addTo(mymap);

</script>


    <?php } ?>

	<!-- /*/*/*/*/ BOTON IMPRIMIR /*/*/*/*/ -->
  </section>
<!-- ./wrapper -->


</html>
