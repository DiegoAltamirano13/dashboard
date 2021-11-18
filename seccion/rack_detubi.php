<?php
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

include_once '../class/Rack.php';
//INSTACIA PARA VER INFORMACION DEL ARR-RET DE CARGAS
$objRack = new Rack();

//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '18');
if($modulos_valida == 0)
{
  header('Location: index.php');
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Argo Almacenadora | Dashboard</title>
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
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
  <!-- <link rel="stylesheet" href="../plugins/datatables/jquery.dataTables.min.css">    -->
  <link rel="stylesheet" href="../plugins/datatables/jquery.dataTables_themeroller.css">
  <link rel="shortcut icon" href="../assets/ico/favicon.png">
  <link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
  <!-- DataTables ROW GROUP -->
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.0.0/css/rowGroup.dataTables.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">


  <?php
  if ( isset($_GET["detUbi"]) && !empty($_GET["detUbi"])  ){// if solo detalle mercancia ubicada
  $ubicacion = $_GET["detUbi"];
  $id_plaza = $_GET["p"];
  $fil_cliente = $_GET["c"];
  $id_almacen = $_GET["a"];
  $fil_db = $_GET["m"];
  ?>
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped">
      <thead>
      <tr>
        <th class="bg-blue">RACK: <?= substr($ubicacion,0,1) ?></th>
        <th class="bg-blue">COLUMNA: <?= substr($ubicacion,1,2) ?></th>
        <th class="bg-blue">NIVEL: <?= substr($ubicacion,3,2) ?></th>
        <th class="bg-blue">POSICIÓN: <?= substr($ubicacion,5,2) ?></th>
        <th class="bg-blue">PROFUNDIDAD: <?= substr($ubicacion,7,2) ?></th>
      </tr>
      </thead>
    </table>
  </div><hr>

       <table id="example" class="display nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
              <th>CLIENTE</th>
              <th>ARRIBO</th>
              <th>CONTENEDOR</th>
              <th>CERTIFICADO</th>
              <th>FACTURA</th>
              <th>PED.IMP.</th>

              <th>PROYECTO</th>
              <th>CONDICION</th>

              <th>LOTE SERIE</th>
              <th>UBICACIÓN</th>
              <th>DESCRIPCION</th>

              <th>UME</th>
              <th>ENTRADA</th>
              <th>SALIDA</th>
              <th>SALDO</th>
            </tr>
        </thead>
        <tbody>
        <?php
          $rackDetalleUbica = $objRack->rackDetalleUbica($ubicacion,$id_plaza,$fil_cliente,$id_almacen,$fil_db);
          for ($i=0; $i <count($rackDetalleUbica) ; $i++) {// FOR DETALLE MERCANCIA UBICADA
        ?>
        <tr>
            <td title="<?= $rackDetalleUbica[$i]["V_CLIENTE_CORTO"] ?>"><?= "(".$rackDetalleUbica[$i]["IID_NUM_CLIENTE"].")".$rackDetalleUbica[$i]["V_CLIENTE"] ?></td>
            <td><?= $rackDetalleUbica[$i]["ID_ARRIBO"] ?></td>
            <td><?= $rackDetalleUbica[$i]["V_CONTENEDOR"] ?></td>
            <!-- <td><?= $rackDetalleUbica[$i]["VID_RECIBO"] ?></td>-->
            <td><?= $rackDetalleUbica[$i]["VID_CERTIFICADO"] ?></td>
            <td><?= $rackDetalleUbica[$i]["VID_FACTURA"] ?></td>
            <td><?= $rackDetalleUbica[$i]["VNO_PED_IMP"] ?></td>
            <td><?= $rackDetalleUbica[$i]["PROYECTO"] ?></td>
            <td><?= $rackDetalleUbica[$i]["CALIDAD"] ?></td>

            <td><?= $rackDetalleUbica[$i]["V_LOTE_SERIE"] ?></td>

            <td><?= $rackDetalleUbica[$i]["V_UBICACION"] ?></td>
            <!-- <td><?php if ($rackDetalleUbica[$i]["N_LIBRE"] == 0){ echo 'OCUPADO';}else{echo 'LIBRE';} ?></td>-->
            <td><?= $rackDetalleUbica[$i]["V_DESCRIPCION_GENERICA"] ?></td>

            <td><?= $rackDetalleUbica[$i]["NOMBRE_UME"] ?></td>
            <?php if ($rackDetalleUbica[$i]["I_SAL_CERO"] >= 1 ){ ?>
            <td bgcolor="#34D377"><?= $rackDetalleUbica[$i]["ARRIBADO"] ?></td>
            <td bgcolor="#FFC95D"><?= $rackDetalleUbica[$i]["RETIRADO"] ?></td>
            <td bgcolor="#33B6E5"><?= ($rackDetalleUbica[$i]["ARRIBADO"] - $rackDetalleUbica[$i]["RETIRADO"] )?></td>
            <?php }else{ ?>
            <td bgcolor="#34D377"><?= $rackDetalleUbica[$i]["ARRIBADO"] ?></td>
            <td bgcolor="#FFC95D"><?= $rackDetalleUbica[$i]["RETIRADO"] ?></td>
            <td bgcolor="#FF5D6C"><?= ($rackDetalleUbica[$i]["ARRIBADO"] - $rackDetalleUbica[$i]["RETIRADO"] )?></td>
            <?php } ?>
        </tr>
        <?php
          }// /.FOR DETALLE MERCANCIA UBICADA
        ?>
        </tbody>
    </table>
      <!-- TERMINA TABLA DETALLES RETIRO EN CARGAS -->
  <?php }// /.if solo detalle mercancia ubicada ?>


  <?php
  if ( isset($_GET["piso_detubi"]) && !empty($_GET["piso_detubi"])  ){// if solo detalle mercancia ubicada piso
  $ubicacion = $_GET["piso_detubi"];
  $id_plaza = $_GET["p"];
  $fil_cliente = $_GET["c"];
  $id_almacen = $_GET["a"];
  $fil_db = $_GET["m"];
  ?>
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped">
      <thead>
      <tr>
        <th class="bg-blue">PISO: <?= substr($ubicacion,0,1) ?></th>
        <th class="bg-blue">AREA: <?= substr($ubicacion,1,2) ?></th>
        <!--<th class="bg-blue">NIVEL: <?= substr($ubicacion,3,2) ?></th>-->
        <!--<th class="bg-blue">POSICIÓN: <?= substr($ubicacion,5,2) ?></th>-->
        <!--<th class="bg-blue">PROFUNDIDAD: <?= substr($ubicacion,7,2) ?></th>-->
      </tr>
      </thead>
    </table>
  </div><hr>

       <table id="example" class="display nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>CLIENTE</th>
                <th>ARRIBO</th>
                <th>CONTENEDOR</th>
                <th>CERTIFICADO</th>
                <th>FACTURA</th>
                <th>PED.IMP.</th>
                <th>PROYECTO</th>
                <th>CONDICION</th>

                <th>LOTE SERIE</th>
                <th>UBICACIÓN</th>
                <th>DESCRIPCION</th>

                <th>UME</th>
                <th>ENTRADA</th>
                <th>SALIDA</th>
                <th>SALDO</th>
            </tr>
        </thead>
        <tbody>
        <?php
          #echo $ubicacion."<br>";
          $letra = substr($ubicacion, 0 , 1);
          #echo $letra. "<br>";
          $columna = substr($ubicacion, 1 , 2);
          #echo $columna."</br>";
          $ubicacion = substr($ubicacion, 0 , 1).'000000'.substr($ubicacion, 1, 2);
          #echo $ubicacion;
          $rackDetalleUbica = $objRack->rackDetalleUbica($ubicacion,$id_plaza,$fil_cliente,$id_almacen,$fil_db);
          for ($i=0; $i <count($rackDetalleUbica) ; $i++) {// FOR DETALLE MERCANCIA UBICADA
        ?>
            <tr>
                <td title="<?= $rackDetalleUbica[$i]["V_CLIENTE_CORTO"] ?>"><?= "(".$rackDetalleUbica[$i]["IID_NUM_CLIENTE"].")".$rackDetalleUbica[$i]["V_CLIENTE"] ?></td>
                <td><?= $rackDetalleUbica[$i]["ID_ARRIBO"] ?></td>
                <td><?= $rackDetalleUbica[$i]["V_CONTENEDOR"] ?></td>
                <!-- <td><?= $rackDetalleUbica[$i]["VID_RECIBO"] ?></td>-->
                <td><?= $rackDetalleUbica[$i]["VID_CERTIFICADO"] ?></td>
                <td><?= $rackDetalleUbica[$i]["VID_FACTURA"] ?></td>
                <td><?= $rackDetalleUbica[$i]["VNO_PED_IMP"] ?></td>
                <td><?= $rackDetalleUbica[$i]["PROYECTO"] ?></td>
                <td><?= $rackDetalleUbica[$i]["CALIDAD"] ?></td>

                <td><?= $rackDetalleUbica[$i]["V_LOTE_SERIE"] ?></td>

                <td><?= $rackDetalleUbica[$i]["V_UBICACION"] ?></td>
                <!-- <td><?php if ($rackDetalleUbica[$i]["N_LIBRE"] == 0){ echo 'OCUPADO';}else{echo 'LIBRE';} ?></td>-->
                <td><?= $rackDetalleUbica[$i]["V_DESCRIPCION_GENERICA"] ?></td>

                <td><?= $rackDetalleUbica[$i]["NOMBRE_UME"] ?></td>
                <?php if ($rackDetalleUbica[$i]["I_SAL_CERO"] >= 1 ){ ?>
                <td bgcolor="#34D377"><?= $rackDetalleUbica[$i]["ARRIBADO"] ?></td>
                <td bgcolor="#FFC95D"><?= $rackDetalleUbica[$i]["RETIRADO"] ?></td>
                <td bgcolor="#33B6E5"><?= ($rackDetalleUbica[$i]["ARRIBADO"] - $rackDetalleUbica[$i]["RETIRADO"] )?></td>
                <?php }else{ ?>
                <td bgcolor="#34D377"><?= $rackDetalleUbica[$i]["ARRIBADO"] ?></td>
                <td bgcolor="#FFC95D"><?= $rackDetalleUbica[$i]["RETIRADO"] ?></td>
                <td bgcolor="#FF5D6C"><?= ($rackDetalleUbica[$i]["ARRIBADO"] - $rackDetalleUbica[$i]["RETIRADO"] )?></td>
                <?php } ?>
            </tr>
        <?php
          }// /.FOR DETALLE MERCANCIA UBICADA
        ?>
        </tbody>
    </table>
      <!-- TERMINA TABLA DETALLES RETIRO EN CARGAS -->
  <?php }// /.if solo detalle mercancia ubicada piso ?>

  <?php
  if ( isset($_GET["detArr"]) && !empty($_GET["detArr"])  ){// if solo detalle mercancia no ubicada
  $arribo = $_GET["detArr"];
  $id_plaza = $_GET["p"];
  $fil_cliente = $_GET["c"];
  $id_almacen = $_GET["a"];
  $fil_db = $_GET["m"];
  ?>


      <div class="direct-chat-msg">
        <!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="../dist/img/rack/cajaico_noUbi.jpg" alt="img"><!-- /.direct-chat-img -->
        <div class="direct-chat-text text-danger">
          <b>ARRIBO:</b> <code><?=$arribo?></code>
        </div>
        <!-- /.direct-chat-text -->
      </div><hr>

       <table id="example" class="display nowrap" cellspacing="0" width="100%">
        <thead>
          <tr>
              <th>CLIENTE</th>
              <th>ARRIBO</th>
              <th>CONTENEDOR</th>
              <th>CERTIFICADO</th>
              <th>FACTURA</th>
              <th>PED.IMP.</th>
              <th>PROYECTO</th>
              <th>CONDICION</th>

              <th>LOTE SERIE</th>
              <th>UBICACIÓN</th>
              <th>DESCRIPCION</th>

              <th>UME</th>
              <th>ENTRADA</th>
              <th>SALIDA</th>
              <th>SALDO</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $detMercanciaNoUbi = $objRack->detMercanciaNoUbi($arribo,$id_plaza,$fil_cliente,$id_almacen,$fil_db);

          for ($i=0; $i <count($detMercanciaNoUbi) ; $i++) {// FOR DETALLE MERCANCIA NO UBICADA
        ?>
            <tr>
                <td title="<?= $detMercanciaNoUbi[$i]["V_CLIENTE_CORTO"] ?>"><?= "(".$detMercanciaNoUbi[$i]["IID_NUM_CLIENTE"].")".$detMercanciaNoUbi[$i]["V_CLIENTE"] ?></td>
                <td><?= $detMercanciaNoUbi[$i]["ID_ARRIBO"] ?></td>
                <td><?= $detMercanciaNoUbi[$i]["V_CONTENEDOR"] ?></td>
                <!-- <td><?= $rackDetalleUbica[$i]["VID_RECIBO"] ?></td>-->
                <td><?= $detMercanciaNoUbi[$i]["VID_CERTIFICADO"] ?></td>
                <td><?= $detMercanciaNoUbi[$i]["VID_FACTURA"] ?></td>
                <td><?= $detMercanciaNoUbi[$i]["VNO_PED_IMP"] ?></td>
                <td><?= $detMercanciaNoUbi[$i]["PROYECTO"] ?></td>
                <td><?= $detMercanciaNoUbi[$i]["CALIDAD"] ?></td>

                <td><?= $detMercanciaNoUbi[$i]["V_LOTE_SERIE"] ?></td>

                <td><?= $detMercanciaNoUbi[$i]["V_UBICACION"] ?></td>
                <!-- <td><?php if ($rackDetalleUbica[$i]["N_LIBRE"] == 0){ echo 'OCUPADO';}else{echo 'LIBRE';} ?></td>-->

                <td><?= $detMercanciaNoUbi[$i]["V_DESCRIPCION_GENERICA"] ?></td>
                <td><?= $detMercanciaNoUbi[$i]["UME"] ?></td>
                <?php if ($detMercanciaNoUbi[$i]["I_SAL_CERO"] >= 1 ){ ?>
                <td bgcolor="#34D377"><?= $detMercanciaNoUbi[$i]["ARRIBADO"] ?></td>
                <td bgcolor="#FFC95D"><?= $detMercanciaNoUbi[$i]["RETIRADO"] ?></td>
                <td bgcolor="#33B6E5"><?= ($detMercanciaNoUbi[$i]["ARRIBADO"] - $detMercanciaNoUbi[$i]["RETIRADO"] )?></td>
                <?php }else{ ?>
                <td bgcolor="#34D377"><?= $detMercanciaNoUbi[$i]["ARRIBADO"] ?></td>
                <td bgcolor="#FFC95D"><?= $detMercanciaNoUbi[$i]["RETIRADO"] ?></td>
                <td bgcolor="#FF5D6C"><?= ($detMercanciaNoUbi[$i]["ARRIBADO"] - $detMercanciaNoUbi[$i]["RETIRADO"] )?></td>
                <?php } ?>
            </tr>
        <?php
          }// /.FOR DETALLE MERCANCIA NO UBICADA
        ?>
        </tbody>
    </table>
      <!-- TERMINA TABLA DETALLES RETIRO EN CARGAS -->
  <?php }// /.if solo detalle mercancia no ubicada ?>




<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
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
<!-- DataTables ROW GROUP -->
<script src="https://cdn.datatables.net/rowgroup/1.0.0/js/dataTables.rowGroup.min.js"></script>
<script>
$(document).ready(function() {

  var button_view = {
                    extend: 'colvis',
                    collectionLayout: 'fixed two-column',
                    text: '<i class="fa fa-eye-slash"></i>',
                    titleAttr: '(Mostrar/ocultar) Columnas',
                    autoClose: true,
                    }

    $('#example').DataTable( {

        "scrollY": 200,
        "scrollX": true,
        "language": {
            "url": "../plugins/datatables/Spanish.json"
        },

         //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
          dom: 'lBfrtip',
          buttons: [

            {
              extend: 'excelHtml5',
              text: '<i class="fa fa-file-excel-o"></i>',
              titleAttr: 'Excel',
              exportOptions: {//muestra/oculta visivilidad de columna
                  columns: ':visible'
              },
              title: 'Reporte',
            },

            {
              extend: 'print',
              text: '<i class="fa fa-print"></i>',
              titleAttr: 'Imprimir',
              exportOptions: {//muestra/oculta visivilidad de columna
                  columns: ':visible',
              },
              title: '<h5>Reporte</h5>',
            },

            button_view
          ],
      //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    } );
} );
</script>
<!-- SlimScroll -->
<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
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
</body>
</html>
<?php conexion::cerrar($conn); ?>
