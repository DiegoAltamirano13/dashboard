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
$cliente = $_GET["cliente"];
$plaza = $_GET["plaza"];
$fecha = $_GET["fecha"];

//////////INSTACIAS//////////
include_once '../class/Factur_Saldos.php';
$obj_remates_det = new Factur_Saldos($cliente, $plaza, $fecha);

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

<style type="text/css" media="screen">

    .timeline ul li {
    list-style-type: none;
    position: relative;
    width: 6px;
    margin: 0 auto;
    padding-top: 50px;
    background: #3b83bd;
    }

    .timeline ul li::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: 0;
    transform: translateX(-50%);
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: inherit;
    }

    .timeline ul li div {
  position: relative;
  bottom: 0;
  width: 400px;
  padding: 15px;
  background: #F45B69;
}

.timeline ul li div::before {
  content: '';
  position: absolute;
  bottom: 7px;
  width: 0;
  height: 0;
  border-style: solid;
}

.timeline ul li:nth-child(odd) div {
  left: 45px;
}

.timeline ul li:nth-child(odd) div::before {
  left: -15px;
  border-width: 8px 16px 8px 0;
  border-color: transparent #F45B69 transparent transparent;
}

.timeline ul li:nth-child(even) div {
  left: -439px;
}

.timeline ul li:nth-child(even) div::before {
  right: -15px;
  border-width: 8px 0 8px 16px;
  border-color: transparent transparent transparent #F45B69;
}



@media screen and (max-width: 900px) {
  .timeline ul li div {
    width: 250px;
  }
  .timeline ul li:nth-child(even) div {
    left: -289px; /*250+45-6*/
  }
}

@media screen and (max-width: 600px) {
  .timeline ul li {
    margin-left: 20px;
  }

  .timeline ul li div {
    width: calc(100vw - 91px);
  }

  .timeline ul li:nth-child(even) div {
    left: 45px;
  }

  .timeline ul li:nth-child(even) div::before {
    left: -15px;
    border-width: 8px 16px 8px 0;
    border-color: transparent #F45B69 transparent transparent;
  }
}
</style>
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
          <i class="fa fa-legal"></i> Comentarios De Facturacion.
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">

            <!--LINEA DE TIEMPO DE COMENTARIOS-->
            <section class="timeline">
              <ul>
                <?php

                  $graficaMensual = $obj_remates_det->datos_comentarios($cliente, $plaza, $fecha);
                  for ($i=0; $i < count($graficaMensual); $i++) { ?>
                <li>
                  <div>
                    <time style="color: white;"><?php echo $graficaMensual[$i]["FECHA_REGISTRO"]; ?></time></BR>
                    <p><b style="color: white;"> <?php echo $graficaMensual[$i]["COMENTARIO"]; ?></b></p>
                  </div>

                </li>
              <?php  }
              ?>

              </ul>
            </section>
    </div>
    <!-- /.row -->


  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
<script type="text/javascript">
function isElementInViewport(el) {
var rect = el.getBoundingClientRect();
return (
  rect.top >= 0 &&
  rect.left >= 0 &&
  rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
  rect.right <= (window.innerWidth || document.documentElement.clientWidth)
);
}

var items = document.querySelectorAll(".timeline li");

// code for the isElementInViewport function

function callbackFunc() {
  for (var i = 0; i < items.length; i++) {
    if (isElementInViewport(items[i])) {
      items[i].classList.add("in-view");
    }
  }
}

window.addEventListener("load", callbackFunc);
window.addEventListener("scroll", callbackFunc);
</script>
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
