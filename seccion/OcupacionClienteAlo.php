<?php
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
 //header("location:calculo_Ocupacion.php");
}

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

include_once '../class/ocupacionClienteAlo.php';
$obj_class = new OcupacionCliente();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
   include "../class/" . $clase . ".php";
 }
 spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 42);
if($modulos_valida == 0)
{
 header('Location: index.php');
}

/* $_GET FECHA */
$fecha = 'ALL';
if ( isset($_GET["fecha"]) ){
  if ( $obj_class->validateDate(substr($_GET["fecha"],0,10)) AND $obj_class->validateDate(substr($_GET["fecha"],11,10)) ){
    $fecha = $_GET["fecha"];
  }else{
    $fecha = "ALL";
  }
}
/* $_GET FIL_CHECK */
$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}


$plaza =$_SESSION['nomPlaza'];

$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}

$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}

//GRAFICA
$grafica_pza_dañada = $obj_class->graficaDonut($plaza);
$tabla30 = $obj_class->tabla30($plaza);
$tabla60 = $obj_class->tabla60($plaza);
$tabla90 = $obj_class->tabla90($plaza);
$tabla120 = $obj_class->tabla120($plaza);
$tabla150 = $obj_class->tabla150($plaza);
$tablaViva = $obj_class->tablaViva($plaza);
$tablaHANKOOK = $obj_class->tablaHANKOOK($plaza);
$tablaHONDA = $obj_class->tablaHONDA($plaza);
$tablaLINGLONG = $obj_class->tablaLINGLONG($plaza);
$tablaLIUFENG = $obj_class->tablaLIUFENG($plaza);
$tablaSAAA = $obj_class->tablaSAAA($plaza);
$tablaSP = $obj_class->tablaSinProyecto($plaza);
//$tabla_pza_dañada = $obj_class->tabla($plaza,$fecha,$fil_check);
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<style media="screen">
.subir{
 padding: 5px 10px;
 background: #f55d3e;
 color:#fff;
 border:0px solid #fff;
}

.subir:hover{
 color:#fff;
 background: #f29f3e;
}
.outer-container {
 background: #F0F0F0;
 border: #e0dfdf 1px solid;
 padding: 40px 20px;
 border-radius: 2px;
}

.btn-submit {
 background: #333;
 border: #1d1d1d 1px solid;
   border-radius: 2px;
 color: #f0f0f0;
 cursor: pointer;
   padding: 5px 20px;
   font-size:0.9em;
}

#response {
   padding: 10px;
   margin-top: 10px;
   border-radius: 2px;
   display:none;
}

.success {
   background: #c7efd9;
   border: #bbe2cd 1px solid;
}

.warning{
 background: #b3a258;
 border: #c8bd26 1px solid;
}
.error {
   background: #fbcfcf;
   border: #f3c6c7 1px solid;
}

div#response.display-block {
   display: block;
}

</style>
<div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
   <!-- Content Header (Page header) -->
   <section class="content-header">
     <h1>
       Dashboard
       <small>Tiempo de mercancia en almacen  <?= $_SESSION["nomPlaza"] ?></small>
     </h1>
   </section>
   <!-- Main content -->

   <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

<!-- ############################ SECCION GRAFICA Y WIDGETS ############################# -->
<section>
 <div class="row">
   <div >
     <div class="box box-info">
       <?php #echo COUNT($graficaCliente); ?>
       <div class="box-header with-border">
         <h3 class="box-title"><i class="fa fa-table"></i> DURACIÓN MERCANCIA </h3>
         <div class="box-tools pull-right">
           <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
         </div>
       </div>
       <div class="box-body"><!--box-body-->
         <div id="graf_donut_prospectos" style="height: 380px;"></div>

       </div><!--/.box-body-->
     </div>
   </div>
 </div>
</section>
<section>

  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-archive"></i> MERCANCIA RETIRADA</h3>

  </div>

  <div class="box-body"><!--box-body-->

    <div class="nav-tabs-custom">

      <ul class="nav nav-pills" id="myTab">
        <?php if (count($tabla30)> 0 ) { ?>
            <li class="active"><a href="#tab30" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO ALINK</a></li>
        <?php } ?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) > 0) { ?>
            <li class="active"><a href="#tab60" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO BMW</a></li>
        <?php }elseif (COUNT($tabla30) > 0 AND COUNT($tabla60)> 0) { ?>
            <li><a href="#tab60" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO BMW</a></li>
        <?php } else {

        }?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) == 0 AND COUNT($tabla90) > 0) { ?>
            <li class="active"><a href="#tab90" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO DICASTAL</a></li>
        <?php } elseif ((COUNT($tabla30) > 0 OR COUNT($tabla60) > 0) AND COUNT($tabla90) > 0 ) { ?>
            <li><a href="#tab90" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO DICASTAL</a></li>
        <?php } else {}?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) == 0 AND COUNT($tabla90) == 0 AND COUNT($tabla120) > 0) {  ?>
            <li class="active"><a href="#tab120" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO FCA</a></li>
        <?php } elseif ((COUNT($tabla30) > 0 OR COUNT($tabla60) > 0 OR COUNT($tabla90) > 0) AND COUNT($tabla120) > 0) { ?>
            <li><a href="#tab120" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO FCA</a></li>
        <?php } ?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) == 0 AND COUNT($tabla90) == 0 AND COUNT($tabla120) == 0 AND COUNT($tabla150) > 0) { ?>
            <li class="active"><a href="#tab150" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO FORD</a></li>
        <?php } elseif ((COUNT($tabla30) > 0 OR COUNT($tabla60) > 0 OR COUNT($tabla90) > 0 OR COUNT($tabla120) > 0) AND COUNT($tabla150) > 0) { ?>
            <li><a href="#tab150" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO FORD</a></li>
        <?php } ?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) == 0 AND COUNT($tabla90) == 0 AND COUNT($tabla120) == 0 AND COUNT($tabla150) == 0 AND COUNT($tablaViva)> 0) { ?>
            <li class="active"><a href="#tabviva" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO HANDS</a></li>
        <?php }elseif ((COUNT($tabla30) > 0 OR COUNT($tabla60) > 0 OR COUNT($tabla90) > 0 OR COUNT($tabla120) > 0 OR COUNT($tabla150) > 0) AND COUNT($tablaViva)> 0) { ?>
            <li><a href="#tabviva" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO HANDS</a></li>
        <?php } ?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) == 0 AND COUNT($tabla90) == 0 AND COUNT($tabla120) == 0 AND COUNT($tabla150) == 0 AND COUNT($tablaViva) == 0 AND COUNT($tablaHANKOOK) > 0) { ?>
            <li class="active"><a href="#HANKOOK" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO HANKOOK</a></li>
        <?php }elseif ((COUNT($tabla30) > 0 OR COUNT($tabla60) > 0 OR COUNT($tabla90) > 0 OR COUNT($tabla120) > 0 OR COUNT($tabla150) > 0 OR COUNT($tablaViva) > 0 ) AND COUNT($tablaHANKOOK) > 0) { ?>
          <li><a href="#HONDA" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO HANKOOK</a></li>
        <?php } ?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) == 0 AND COUNT($tabla90) == 0 AND COUNT($tabla120) == 0 AND COUNT($tabla150) == 0 AND COUNT($tablaViva) == 0 AND COUNT($tablaHANKOOK) == 0 AND COUNT($tablaHONDA) > 0) { ?>
            <li class="active"><a href="#HONDA" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO HONDA</a></li>
        <?php }elseif ((COUNT($tabla30) > 0 OR COUNT($tabla60) > 0 OR COUNT($tabla90) > 0 OR COUNT($tabla120) > 0 OR COUNT($tabla150) > 0 OR COUNT($tablaViva) > 0 OR COUNT($tablaHANKOOK) > 0 ) AND COUNT($tablaHONDA) > 0) { ?>
          <li><a href="#HONDA" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO HONDA</a></li>
        <?php } ?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) == 0 AND COUNT($tabla90) == 0 AND COUNT($tabla120) == 0 AND COUNT($tabla150) == 0 AND COUNT($tablaViva) == 0 AND COUNT($tablaHANKOOK) == 0 AND COUNT($tablaHONDA) == 0 AND COUNT($tablaLINGLONG) > 0) { ?>
            <li class="active"><a href="#LINGLONG" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO LING-LONG</a></li>
        <?php }elseif ((COUNT($tabla30) > 0 OR COUNT($tabla60) > 0 OR COUNT($tabla90) > 0 OR COUNT($tabla120) > 0 OR COUNT($tabla150) > 0 OR COUNT($tablaViva) > 0 OR COUNT($tablaHANKOOK) > 0 OR COUNT($tablaHONDA) > 0) AND COUNT($tablaLINGLONG) > 0) { ?>
          <li><a href="#LINGLONG" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO LING-LONG</a></li>
        <?php } ?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) == 0 AND COUNT($tabla90) == 0 AND COUNT($tabla120) == 0 AND COUNT($tabla150) == 0 AND COUNT($tablaViva) == 0 AND COUNT($tablaHANKOOK) == 0 AND COUNT($tablaHONDA) == 0 AND COUNT($tablaLINGLONG) == 0 AND COUNT($tablaLIUFENG) > 0) { ?>
            <li class="active"><a href="#LIUFENG" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO LIUFENG</a></li>
        <?php }elseif ((COUNT($tabla30) > 0 OR COUNT($tabla60) > 0 OR COUNT($tabla90) > 0 OR COUNT($tabla120) > 0 OR COUNT($tabla150) > 0 OR COUNT($tablaViva) > 0 OR COUNT($tablaHANKOOK) > 0 OR COUNT($tablaHONDA) > 0 OR COUNT($tablaLINGLONG) > 0) AND COUNT($tablaLIUFENG) > 0) { ?>
          <li><a href="#LIUFENG" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO LIUFENG</a></li>
        <?php } ?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) == 0 AND COUNT($tabla90) == 0 AND COUNT($tabla120) == 0 AND COUNT($tabla150) == 0 AND COUNT($tablaViva) == 0 AND COUNT($tablaHANKOOK) == 0 AND COUNT($tablaHONDA) == 0 AND COUNT($tablaLINGLONG) == 0 AND COUNT($tablaLIUFENG) == 0 AND COUNT($tablaSAAA) > 0) { ?>
            <li class="active"><a href="#SAAA" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO SAAA</a></li>
        <?php }elseif ((COUNT($tabla30) > 0 OR COUNT($tabla60) > 0 OR COUNT($tabla90) > 0 OR COUNT($tabla120) > 0 OR COUNT($tabla150) > 0 OR COUNT($tablaViva) > 0 OR COUNT($tablaHANKOOK) > 0 OR COUNT($tablaHONDA) > 0 OR COUNT($tablaLINGLONG) > 0 OR COUNT($tablaLIUFENG) > 0) AND COUNT($tablaSAAA) > 0) { ?>
          <li><a href="#SAAA" data-toggle="tab"><i class="fa fa-archive"></i> PROYECTO SAAA</a></li>
        <?php } ?>
        <?php if (COUNT($tabla30) == 0 AND COUNT($tabla60) == 0 AND COUNT($tabla90) == 0 AND COUNT($tabla120) == 0 AND COUNT($tabla150) == 0 AND COUNT($tablaViva) == 0 AND COUNT($tablaHANKOOK) == 0 AND COUNT($tablaHONDA) == 0 AND COUNT($tablaLINGLONG) == 0 AND COUNT($tablaLIUFENG) == 0 AND COUNT($tablaSAAA) == 0 AND COUNT($tablaSP) > 0) { ?>
            <li class="active"><a href="#SINP" data-toggle="tab"><i class="fa fa-archive"></i> SIN PROYECTO</a></li>
        <?php }elseif ((COUNT($tabla30) > 0 OR COUNT($tabla60) > 0 OR COUNT($tabla90) > 0 OR COUNT($tabla120) > 0 OR COUNT($tabla150) > 0 OR COUNT($tablaViva) > 0 OR COUNT($tablaHANKOOK) > 0 OR COUNT($tablaHONDA) > 0 OR COUNT($tablaLINGLONG) > 0 OR COUNT($tablaLIUFENG) > 0 OR COUNT($tablaSAAA) > 0) AND COUNT($tablaSP) > 0) { ?>
          <li><a href="#SINP" data-toggle="tab"><i class="fa fa-archive"></i> SIN PROYECTO</a></li>
        <?php } ?>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="tab30">

          <div class="table-wrapper">
            <table id="tabla_activo" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tabla30) ; $i++) { ?>
                <tr>
                  <td><?= $tabla30[$i]["VID_RECIBO"]?></td>
                  <td><?= $tabla30[$i]["V_DESCRIPCION"] ?></td>
                  <td><?= $tabla30[$i]["VID_CERTIFICADO"] ?></td>
                  <td><?= $tabla30[$i]["VID_FACTURA"] ?></td>
                  <td><?= $tabla30[$i]["TRANSLADO"] ?></td>
                  <td><?= $tabla30[$i]["TRANSLADO_CUARENTENA"] ?></td>
                  <td><?= $tabla30[$i]["D_PLAZO_DEP_INI"] ?></td>
                  <td><?= $tabla30[$i]["N_DIAS"] ?></td>
                  <td><?= $tabla30[$i]["EXISTENCIAS_VIVAS"] ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

        </div>

        <div class="tab-pane" id="tab60">

          <div class="table-wrapper">
            <table id="tabla_activo2" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tabla60) ; $i++) { ?>
                <tr>
                  <td><?= $tabla60[$i]["VID_RECIBO"]?></td>
                  <td><?= $tabla60[$i]["V_DESCRIPCION"] ?></td>
                  <td><?= $tabla60[$i]["VID_CERTIFICADO"] ?></td>
                  <td><?= $tabla60[$i]["VID_FACTURA"] ?></td>
                  <td><?= $tabla60[$i]["TRANSLADO"] ?></td>
                  <td><?= $tabla60[$i]["TRANSLADO_CUARENTENA"] ?></td>
                  <td><?= $tabla60[$i]["D_PLAZO_DEP_INI"] ?></td>
                  <td><?= $tabla60[$i]["N_DIAS"] ?></td>
                  <td><?= $tabla60[$i]["EXISTENCIAS_VIVAS"] ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane" id="tab90">

          <div class="table-wrapper">
            <table id="tabla_activo3" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tabla90) ; $i++) { ?>
                  <tr>
                    <td><?= $tabla90[$i]["VID_RECIBO"]?></td>
                    <td><?= $tabla90[$i]["V_DESCRIPCION"] ?></td>
                    <td><?= $tabla90[$i]["VID_CERTIFICADO"] ?></td>
                    <td><?= $tabla90[$i]["VID_FACTURA"] ?></td>
                    <td><?= $tabla90[$i]["TRANSLADO"] ?></td>
                    <td><?= $tabla90[$i]["TRANSLADO_CUARENTENA"] ?></td>
                    <td><?= $tabla90[$i]["D_PLAZO_DEP_INI"] ?></td>
                    <td><?= $tabla90[$i]["N_DIAS"] ?></td>
                    <td><?= $tabla90[$i]["EXISTENCIAS_VIVAS"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>


        <div class="tab-pane" id="tab120">

          <div class="table-wrapper">
            <table id="tabla_activo4" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tabla120) ; $i++) { ?>
                  <tr>
                    <td><?= $tabla120[$i]["VID_RECIBO"]?></td>
                    <td><?= $tabla120[$i]["V_DESCRIPCION"] ?></td>
                    <td><?= $tabla120[$i]["VID_CERTIFICADO"] ?></td>
                    <td><?= $tabla120[$i]["VID_FACTURA"] ?></td>
                    <td><?= $tabla120[$i]["TRANSLADO"] ?></td>
                    <td><?= $tabla120[$i]["TRANSLADO_CUARENTENA"] ?></td>
                    <td><?= $tabla120[$i]["D_PLAZO_DEP_INI"] ?></td>
                    <td><?= $tabla120[$i]["N_DIAS"] ?></td>
                    <td><?= $tabla120[$i]["EXISTENCIAS_VIVAS"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane" id="tab150">

          <div class="table-wrapper">
            <table id="tabla_activo5" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tabla150) ; $i++) { ?>
                  <tr>
                    <td><?= $tabla150[$i]["VID_RECIBO"]?></td>
                    <td><?= $tabla150[$i]["V_DESCRIPCION"] ?></td>
                    <td><?= $tabla150[$i]["VID_CERTIFICADO"] ?></td>
                    <td><?= $tabla150[$i]["VID_FACTURA"] ?></td>
                    <td><?= $tabla150[$i]["TRANSLADO"] ?></td>
                    <td><?= $tabla150[$i]["TRANSLADO_CUARENTENA"] ?></td>
                    <td><?= $tabla150[$i]["D_PLAZO_DEP_INI"] ?></td>
                    <td><?= $tabla150[$i]["N_DIAS"] ?></td>
                    <td><?= $tabla150[$i]["EXISTENCIAS_VIVAS"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane" id="tabviva">

          <div class="table-wrapper">
            <table id="tabla_activo6" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaViva) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaViva[$i]["VID_RECIBO"]?></td>
                    <td><?= $tablaViva[$i]["V_DESCRIPCION"] ?></td>
                    <td><?= $tablaViva[$i]["VID_CERTIFICADO"] ?></td>
                    <td><?= $tablaViva[$i]["VID_FACTURA"] ?></td>
                    <td><?= $tablaViva[$i]["TRANSLADO"] ?></td>
                    <td><?= $tablaViva[$i]["TRANSLADO_CUARENTENA"] ?></td>
                    <td><?= $tablaViva[$i]["D_PLAZO_DEP_INI"] ?></td>
                    <td><?= $tablaViva[$i]["N_DIAS"] ?></td>
                    <td><?= $tablaViva[$i]["EXISTENCIAS_VIVAS"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane" id="HANKOOK">

          <div class="table-wrapper">
            <table id="tabla_activo7" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaHANKOOK) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaHANKOOK[$i]["VID_RECIBO"]?></td>
                    <td><?= $tablaHANKOOK[$i]["V_DESCRIPCION"] ?></td>
                    <td><?= $tablaHANKOOK[$i]["VID_CERTIFICADO"] ?></td>
                    <td><?= $tablaHANKOOK[$i]["VID_FACTURA"] ?></td>
                    <td><?= $tablaHANKOOK[$i]["TRANSLADO"] ?></td>
                    <td><?= $tablaHANKOOK[$i]["TRANSLADO_CUARENTENA"] ?></td>
                    <td><?= $tablaHANKOOK[$i]["D_PLAZO_DEP_INI"] ?></td>
                    <td><?= $tablaHANKOOK[$i]["N_DIAS"] ?></td>
                    <td><?= $tablaHANKOOK[$i]["EXISTENCIAS_VIVAS"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>


        <div class="tab-pane" id="HONDA">

          <div class="table-wrapper">
            <table id="tabla_activo8" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaHONDA) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaHONDA[$i]["VID_RECIBO"]?></td>
                    <td><?= $tablaHONDA[$i]["V_DESCRIPCION"] ?></td>
                    <td><?= $tablaHONDA[$i]["VID_CERTIFICADO"] ?></td>
                    <td><?= $tablaHONDA[$i]["VID_FACTURA"] ?></td>
                    <td><?= $tablaHONDA[$i]["TRANSLADO"] ?></td>
                    <td><?= $tablaHONDA[$i]["TRANSLADO_CUARENTENA"] ?></td>
                    <td><?= $tablaHONDA[$i]["D_PLAZO_DEP_INI"] ?></td>
                    <td><?= $tablaHONDA[$i]["N_DIAS"] ?></td>
                    <td><?= $tablaHONDA[$i]["EXISTENCIAS_VIVAS"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>


        <div class="tab-pane" id="LINGLONG">

          <div class="table-wrapper">
            <table id="tabla_activo9" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaLINGLONG) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaLINGLONG[$i]["VID_RECIBO"]?></td>
                    <td><?= $tablaLINGLONG[$i]["V_DESCRIPCION"] ?></td>
                    <td><?= $tablaLINGLONG[$i]["VID_CERTIFICADO"] ?></td>
                    <td><?= $tablaLINGLONG[$i]["VID_FACTURA"] ?></td>
                    <td><?= $tablaLINGLONG[$i]["TRANSLADO"] ?></td>
                    <td><?= $tablaLINGLONG[$i]["TRANSLADO_CUARENTENA"] ?></td>
                    <td><?= $tablaLINGLONG[$i]["D_PLAZO_DEP_INI"] ?></td>
                    <td><?= $tablaLINGLONG[$i]["N_DIAS"] ?></td>
                    <td><?= $tablaLINGLONG[$i]["EXISTENCIAS_VIVAS"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>



        <div class="tab-pane" id="LIUFENG">

          <div class="table-wrapper">
            <table id="tabla_activo10" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaLIUFENG) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaLIUFENG[$i]["VID_RECIBO"]?></td>
                    <td><?= $tablaLIUFENG[$i]["V_DESCRIPCION"] ?></td>
                    <td><?= $tablaLIUFENG[$i]["VID_CERTIFICADO"] ?></td>
                    <td><?= $tablaLIUFENG[$i]["VID_FACTURA"] ?></td>
                    <td><?= $tablaLIUFENG[$i]["TRANSLADO"] ?></td>
                    <td><?= $tablaLIUFENG[$i]["TRANSLADO_CUARENTENA"] ?></td>
                    <td><?= $tablaLIUFENG[$i]["D_PLAZO_DEP_INI"] ?></td>
                    <td><?= $tablaLIUFENG[$i]["N_DIAS"] ?></td>
                    <td><?= $tablaLIUFENG[$i]["EXISTENCIAS_VIVAS"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>


        <div class="tab-pane" id="SAAA">

          <div class="table-wrapper">
            <table id="tabla_activo11" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaSAAA) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaSAAA[$i]["VID_RECIBO"]?></td>
                    <td><?= $tablaSAAA[$i]["V_DESCRIPCION"] ?></td>
                    <td><?= $tablaSAAA[$i]["VID_CERTIFICADO"] ?></td>
                    <td><?= $tablaSAAA[$i]["VID_FACTURA"] ?></td>
                    <td><?= $tablaSAAA[$i]["TRANSLADO"] ?></td>
                    <td><?= $tablaSAAA[$i]["TRANSLADO_CUARENTENA"] ?></td>
                    <td><?= $tablaSAAA[$i]["D_PLAZO_DEP_INI"] ?></td>
                    <td><?= $tablaSAAA[$i]["N_DIAS"] ?></td>
                    <td><?= $tablaSAAA[$i]["EXISTENCIAS_VIVAS"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>


        <div class="tab-pane" id="SINP">

          <div class="table-wrapper">
            <table id="tabla_activo12" class="display table table-bordered table-hover " cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">RECIBO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DESCRIPCION GENERICA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CERTIFICADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FACTURA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PROYECTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">CUARENTENA </font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">FECHA LLEGADA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DIAS EN ALMACEN</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EXISTENCIAS</font></th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaSP) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaSP[$i]["VID_RECIBO"]?></td>
                    <td><?= $tablaSP[$i]["V_DESCRIPCION"] ?></td>
                    <td><?= $tablaSP[$i]["VID_CERTIFICADO"] ?></td>
                    <td><?= $tablaSP[$i]["VID_FACTURA"] ?></td>
                    <td><?= $tablaSP[$i]["TRANSLADO"] ?></td>
                    <td><?= $tablaSP[$i]["TRANSLADO_CUARENTENA"] ?></td>
                    <td><?= $tablaSP[$i]["D_PLAZO_DEP_INI"] ?></td>
                    <td><?= $tablaSP[$i]["N_DIAS"] ?></td>
                    <td><?= $tablaSP[$i]["EXISTENCIAS_VIVAS"] ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>



      </div>
      <!-- /.tab-content -->
    </div>

  </div>

  <div class="modal fade bd-example-modal-xl" id="asignacion_activos" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
   <div class="modal-dialog  modal-lg" role="document">
     <div class="modal-content">
              <div class="modal-body" id='modal'>
              </div>
       <div class="modal-footer">
       <button type="button" class="btn btn-primary" onclick="javascript:imprSeleccion('modal')">Imprimir</button>
       <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
       </div>
   </div>
  </div>
  </div>


</section>
<!-- ############################ ./SECCION GRAFICA Y WIDGETS ############################# -->
</section><!-- Termina la seccion de Todo el contenido principal -->
   <!-- /.content -->
 </div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
<!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
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
<!-- RESPONSIVE DATATBLE -->
<script src="../plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>


<script src="../plugins/flot/jquery.flot.min.js"></script>
<!-- FLOT PIE CHARTS 3D -->
<script src="../plugins/flot/jquery.flot.pie3d.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="../plugins/flot/jquery.flot.resize.min.js"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="../plugins/flot/jquery.flot.pie_old.js"></script>
<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
<script src="../plugins/flot/jquery.flot.categories.js"></script>
<!-- FLOT ORDER BARS  -->
<script src="../plugins/flot/jquery.flot.orderBars.js"></script>
<!-- FLOT  bar charts click text -->
<script src="../plugins/flot/jquery.flot.tooltip.js"></script>
<script type="text/javascript">
$('#myTab a').click(function(e) {
 e.preventDefault();
 $(this).tab('show');
});

// store the currently selected tab in the hash value
$("ul.nav-pills > li > a").on("shown.bs.tab", function(e) {
 var id = $(e.target).attr("href").substr(1);
 window.location.hash = id;
});

// on load of the page: switch to the currently selected tab
var hash = window.location.hash;
$('#myTab a[href="' + hash + '"]').tab('show');
</script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
//ACTIVA FILTRO POR FECHA
<?php if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){ ?>
 $('input[name="fil_fecha"]').attr("disabled", false);
<?php } ?>
$('input[name="fil_check"]').on("click", function (){

 if ($('input[name="fil_check"]').is(':checked')) {
   $('input[name="fil_fecha"]').attr("disabled", false);
 }else{
   $('input[name="fil_fecha"]').attr("disabled", true);
 }

});

// CHECA AREAS
$("#fil_departamento").change(function (){

 $.ajax({
   type: 'post',
   url: '../action/rotacion_personal.php',
   data: { "depto" : $(this).val() },
   beforeSend: function () {
     //$('#fil_area').remove();
     $('#fil_area')
     .empty()
     .append('<option value="ALL">ALL</option>');
   },
   success: function (response) {// success
     var dataJson = JSON.parse(response);
       var $select = $('#fil_area');
       //$select.append('<option></option>');
       $.each(dataJson, function(i, val){
         $select.append($('<option></option>').attr('value', val.IID_AREA).text( val.V_DESCRIPCION ));
       });

   }// ./succes
 });

});

//BOTON FILTRAR
$(".btn_fil").on("click", function(){

 fil_fecha = $('input[name="fil_fecha"]').val();
 fil_plaza = $('#fil_plaza').val();
 almacen = $('#nomAlm').val();
 fil_contrato = $('#fil_contrato').val();
 fil_departamento = $('#fil_departamento').val();
 fil_area = $('#fil_area').val();
 fil_check = 'off';

 //Fill habilitados
 fil_habilitado = 'off';

 url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
 if ($('input[name="fil_check"]').is(':checked')) {
   fil_check = 'on';
   if ($('input[name="fil_habilitado"]').is(':checked')) {
     fil_habilitado = 'on';
     url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
   }
   else {
     fil_habilitado = 'off';
     url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
   }

 }else{
   fil_check = 'off';
   if ($('input[name="fil_habilitado"]').is(':checked')) {
       fil_habilitado = 'on';
       url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado;
   }
   else {
     fil_habilitado = 'off';
     url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado;
   }
   //url = '?plaza='+fil_plaza+'&check='+fil_check+'&contrato='+fil_contrato+'&depto='+fil_departamento+'&area='+fil_area;
 }

 location.href = url;

});

$('.select2').select2()
</script>

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo2').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo3').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo4').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>


<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo5').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo6').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo7').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>


<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo8').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>


<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo9').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>


<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo10').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo11').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>


<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_activo12').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": 1400,
      "language": {
          "url": "../plugins/datatables/Spanish.json"
      },

      //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Buques Argo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Buques Argo',
          },

          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
            text: '<i class="fa fa-eye-slash"></i>',
            titleAttr: '(Mostrar/ocultar) Columnas',
            autoClose: true,
          }
        ],
    //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

    });

});
</script>
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<?php
/* ------------------- INICIA OPCIONES PARA LA GRÁFICA DE DONA ------------------- */
$donut_series = "pie3d: {
                 stroke: { /*define linea separadora*/
                   width: 0,
                   /*color: '#222D32'*/
                 } ,
                 show: true,
                 radius: .80, /*radius: 1,  tamño radio del circulo*/
                 tilt: .9,/*rotacion de angulo */
                 depth: 20,/*grosor de sombra 3d*/
                 innerRadius: 60,/*radio dona o pastel*/
                 label: {
                   show: false,
                   radius:2/3,/*0.90 posicion del label con data*/
                   formatter: labelFormatter,
                 },
               }";

$donut_grid =  "hoverable: true,
               clickable: false,
               verticalLines: false,
               horizontalLines: false,";
$donut_legend = "/*labelBoxBorderColor: 'none'*/
               show: true "; //-- PONE LOS LABEL DEL ALDO IZQUIERDO //

$donut_content = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> PORCENTAJE = %y.0 % </b>  </div>' ;

$donut_tooltip = "show: true,
     content: '".$donut_content."',
     defaultTheme: true ";
?>
<script>
 $(function () {
   /* DONUT CHART */
   var donutData_pros_general = [
     <?php
     $grafica_pastel = $obj_class->graficaDonut();
       for ($i=0; $i <count($grafica_pastel) ; $i++) {
         $plaza = $grafica_pastel[$i]["TIEMPOESTADIA"];
         //$plaza_corta = str_word_count($plaza, 1);
         $separador  = ' ';
         $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

         // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
         switch ('1,2') {
             case '1':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_pastel[$i]["TIEMPOESTADIA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_pastel[$i]["TIEMPOESTADIA"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$grafica_pastel[$i]["TIEMPOESTADIA"].'</button></form>' ;
               break;
             case '2':
              $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_pastel[$i]["TIEMPOESTADIA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_pastel[$i]["TIEMPOESTADIA"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$grafica_pastel[$i]["TIEMPOESTADIA"].'</button></form>' ;
               break;
             case '1,2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_pastel[$i]["TIEMPOESTADIA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_pastel[$i]["TIEMPOESTADIA"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$grafica_pastel[$i]["TIEMPOESTADIA"].'</button></form>' ;
               break;
           }
           switch ($i) {
             case '1':
               $color ='#5ABF43';
               break;
             case '2':
               $color = '#3C802D';
               break;
             case '3':
               $color = '#78FF59';
               break;
             case '4':
               $color = '#1F4217';
               break;
             case '5':
               $color = '#6CE650';
               break;
             case '6':
               $color = '#D87A80';
               break;
             case '7':
               $color = '#FF6A6F';
               break;
             default:
               $color = '#5D8AA8';
               break;
           }

         #$data = ($grafica_pastel[$i]["N_PIEZAS_DAÑADAS"]/$grafica_pastel[$i]["N_TOTAL_PIEZAS"])*  100;
         $data = ($grafica_pastel[$i]["ENTRE_0_30"]);
         $color = $color;

     ?>

       {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

     <?php
       }
     ?>
   ];

   $.plot("#graf_donut_prospectos", donutData_pros_general, {
     series: { <?= $donut_series ?> },
     grid: { <?= $donut_grid  ?> },
     //-- PONE LOS LABEL DEL ALDO IZQUIERDO //
     legend: { <?= $donut_legend ?>},
     //-- VALOR AL PONER EL MAUSE SOBRE LA PLAZA //
     tooltip: {<?= $donut_tooltip ?>},
   });
   /* END DONUT CHART */

 });



 /*
  * Custom Label formatter
  * ----------------------
  */
 function labelFormatter(label, series) {
   return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
       + label
       +"<div style='color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px'>"+(series.percent).toFixed(2) + "%</div>"
       + "</div>";
 }
</script>
<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
  $($.fn.dataTable.tables(true)).DataTable()
     .columns.adjust()
     .responsive.recalc();
});

$(document).ready(function() {
   $('#tabla_nomina_real').DataTable( {
     "ordering": false,
     "searching":true,
     "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
     "scrollY": 450,
     fixedHeader: true,
     "dom": '<"toolbar">frtip',
     stateSave: true,
     "scrollX": true,
     "language": {
         "url": "../plugins/datatables/Spanish.json"
     },

     //---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
   dom: 'lBfrtip',//Bfrtip muestra opcion para ver n registros
       buttons: [

         {
           extend: 'excelHtml5',
           text: '<i class="fa fa-file-excel-o"></i>',
           titleAttr: 'Excel',
           exportOptions: {//muestra/oculta visivilidad de columna
               columns: ':visible'
           },
           title: 'Suma Toneladas',
         },

         {
           extend: 'print',
           text: '<i class="fa fa-print"></i>',
           titleAttr: 'Imprimir',
           exportOptions: {//muestra/oculta visivilidad de columna
               columns: ':visible',
           },
           title: 'Suma Toneladas',
         },

         {
           extend: 'colvis',
           collectionLayout: 'fixed two-column',
           text: '<i class="fa fa-eye-slash"></i>',
           titleAttr: '(Mostrar/ocultar) Columnas',
           autoClose: true,
         }
       ],
   //---------- TERMINA CODE BOTONES (EXCEL-PINT-VIEW) ----------//

   });

});
</script>


<script type="text/javascript">
function cambiar(){
   var pdrs = document.getElementById('file').files[0].name;
   document.getElementById('info').innerHTML = pdrs;
   document.getElementById('submit').disabled = false;
}
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
<!-- RESPONSIVE DATATBLE -->
<script src="../plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
$('input[name="fil_fecha"]').daterangepicker(
 {
 "linkedCalendars": false,
 "showDropdowns": true,
//INICIA CODE OPCION PARA FORMATO EN ESPAÑOL
 "locale": {
 "format": "DD/MM/YYYY",
 "separator": "-",
 "applyLabel": "Aplicar",
 "cancelLabel": "Cancelar",
 "fromLabel": "From",
 "toLabel": "To",
 "customRangeLabel": "Fecha Personalizada",
 "daysOfWeek": ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
 "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agusto","Septiembre","Octubre","Noviembre","Diciembre"],
 "firstDay": 1
 },
//TERMINA CODE OPCION PARA FORMATO EN ESPAÑOL
   ranges: {
     'Hoy': [moment(), moment()],
     'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
     'Los últimos 7 días': [moment().subtract(6, 'days'), moment()],
     'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
     'Este mes': [moment().startOf('month'), moment().endOf('month')],
     'El mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
     'Este Año': [moment().startOf('year'), moment().endOf('year')]
   },

   <?php if( $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){ ?>
     startDate: '<?=substr($fecha,0,10)?>',
     endDate: '<?=substr($fecha,11,10)?>'
   <?php }else{ ?>
     startDate: moment().subtract(29, 'days'),
     endDate: moment()
   <?php } ?>
 },

);
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
