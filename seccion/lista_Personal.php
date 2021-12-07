<?php
//BY JTJ 28/12/2018
error_reporting(0);
if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  header("location:Lista_Personal.php");
  //return;
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

include_once '../class/Lista_Personal.php';
$obj_class = new ListaPersonal();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 19);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
$op_visual = 1;
if( isset($_GET["tipo"]) ){
    $op_visual = $_GET["tipo"];
}
//remates imagenes
include '../class/descargaImagenesEmpleados.php';
$descargaImagen = new descargaImagenes();

$carga_Imagen = $descargaImagen->descargaImagenesEmpleados();

$codigobars = " ";
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">

<!--leaflet js maps-->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>

<!-- Make sure you put this AFTER Leaflet's CSS -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>Lista de Personal</small>
      </h1>
    </section>
    <!-- Main content -->
<!-- ############################ ./SECCION GRAFICA Y WIDGETS ############################# -->
<section>

  <div class="box box-default box-solid">
    <div class="box-header with-border">
      <i class="fa fa-filter"></i><h3 class="box-title">FILTROS</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
        </button>
      </div>
    </div>
    <!-- /.box-header -->

  <form method="post">
    <div class="box-body" style="display: block;">

      <div class="row">

        <div class="col-lg-6">
          <label>Lista De Personal :</label>
          <div class="input-group">
            <div class="checkbox">
              <label>
                <input type="radio" name="tipo" value="1" id="tipo" <?php if($op_visual==1){echo "checked";}?> >Activos
              </label>
              <label>
                <input type="radio" name="tipo" value="2" id="tipo" <?php if($op_visual== 2){echo "checked";}?> >Inactivos
              </label>
            </div>
          </div>
        </div>

      </div>

    </div>
    <!-- /.box-body -->
    <div class="box-footer">
      <button type="button" class="btn btn-primary btn-xs pull-left btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button>
    </div>
  </form>
  </div>

  <div class="box box-success">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-list-alt"></i>LISTA DE PERSONAL <?php if ($op_visual == 1 ) {echo "ACTIVO";}else {echo "INACTIVO";} ?></h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->
      <?php if ($op_visual == 1) { ?>
      <div class="nav-tabs-custom">
            <?php
                  $tablaActivos = $obj_class->tablaActivos("CORPORATIVO");
                  $tablaActivos2 = $obj_class->tablaActivos("CÓRDOBA");
                  $tablaActivos3 = $obj_class->tablaActivos("MÉXICO");
                  $tablaActivos4 = $obj_class->tablaActivos("GOLFO");
                  $tablaActivos5 = $obj_class->tablaActivos("PENINSULA");
                  $tablaActivos6 = $obj_class->tablaActivos("PUEBLA");
                  $tablaActivos7 = $obj_class->tablaActivos("BAJIO");
                  $tablaActivos8 = $obj_class->tablaActivos("OCCIDENTE");
                  $tablaActivos9 = $obj_class->tablaActivos("NORESTE");
            ?>


        <ul class="nav nav-pills" id="myTab">
          <li class="active"><a href="#tab_corporativo" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL COORPORATIVO
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo count($tablaActivos); ?></span></a>
          </li>
          <li><a href="#tab_cordoba" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL CÓRDOBA
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo count($tablaActivos2); ?></span></a>
          </li>
          <li><a href="#tab_mex" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL MÉXICO
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo count($tablaActivos3); ?></span></a>
          </li>
          <li><a href="#tab_golfo" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL GOLFO
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo count($tablaActivos4); ?></span></a>
          </li>
          <li><a href="#tab_peninsula" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL PENINSULA
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo count($tablaActivos5); ?></span></a>
          </li>
          <li><a href="#tab_puebla" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL PUEBLA
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo count($tablaActivos6); ?></span></a>
          </li>
          <li><a href="#tab_bajio" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL BAJIO
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo count($tablaActivos7); ?></span></a>
          </li>
          <li><a href="#tab_occidete" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL OCCIDENTE
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo count($tablaActivos8); ?></span></a>
          </li>
          <li><a href="#tab_noreste" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL NORESTE
            <span data-toggle="tooltip" title="" class="badge bg-verde" data-original-title="Total de Personal"><?php echo count($tablaActivos9); ?></span></a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane  active" id="tab_corporativo">

          <div class="table-responsive">
            <table id="tabla_activo" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">NSS</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">RFC</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">INGRESO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">ANTIGUEDAD</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">PUESTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">DEPTO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">SALARIO</font></th>
                  <th class="small" bgcolor="#0073B7"><font color="white">VER</th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaActivos) ; $i++) { ?>
                <tr>
                  <td><?= $tablaActivos[$i]["IID_EMPLEADO"] ?></td>
                  <td><?= $tablaActivos[$i]["NOMBRE"] ?></td>
                  <!--<td>
                    <?php switch ($tablaActivos[$i]["V_SEXO"]) {
                    case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                    case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                    default: echo "NO REGISTRADO"; break;
                    } ?>
                  </td>-->
                  <td><?= $tablaActivos[$i]["V_IMSS"] ?></td>
                  <td><?= $tablaActivos[$i]["V_RFC"] ?></td>
                  <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos[$i]["D_FECHA_INGRESO"] ?></span></td>
                  <td><?= $tablaActivos[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <!--<td>
                    <?php switch ($tablaActivos[$i]["S_TIPO_CONTRATO"]) {
                    case 0: echo "DETERMINADO"; break;
                    case 1: echo "TIEMPO INDETERMINADO"; break;
                    case 3: echo "POR OBRA DETERMINADA"; break;
                    default: echo "INDEFINIDO"; break;
                    } ?>
                  </td>-->
                  <td><?= $tablaActivos[$i]["PUESTO"] ?></td>
                  <td><?= $tablaActivos[$i]["DEPTO"] ?></td>
                  <td><?= $tablaActivos[$i]["AREA"] ?></td>
                  <td>$<?= number_format($tablaActivos[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                  <td class="small">

                      <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                      onclick='cargarPantalla(". $tablaActivos[$i]["IID_EMPLEADO"].", 1)'>Ver</button>";
                      ?>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="tab_cordoba">

            <div class="table-responsive">
              <table id="tablaActivos10" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos2) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos2[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos2[$i]["NOMBRE"] ?></td>
                  <!--  <td>
                      <?php switch ($tablaActivos2[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos2[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos2[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos2[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos2[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                    <!--<td>
                      <?php switch ($tablaActivos2[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos2[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos2[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos2[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos2[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos2[$i]["IID_EMPLEADO"].", 1)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>
          <!--MX -->
          <div class="tab-pane" id="tab_mex">

            <div class="table-responsive">
              <table id="tabla_activo2" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos3) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos3[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos3[$i]["NOMBRE"] ?></td>
                  <!--  <td>
                      <?php switch ($tablaActivos3[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos3[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos3[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos3[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos3[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                    <!--<td>
                      <?php switch ($tablaActivos3[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos3[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos3[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos3[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos3[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos3[$i]["IID_EMPLEADO"].", 1)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="tab-pane" id="tab_golfo">

            <div class="table-responsive">
              <table id="tabla_activo3" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos4) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos4[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos4[$i]["NOMBRE"] ?></td>
                    <!--<td>
                      <?php switch ($tablaActivos3[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos4[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos4[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos4[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos4[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <!--  <td>
                      <?php switch ($tablaActivos4[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos4[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos4[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos4[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos4[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos4[$i]["IID_EMPLEADO"].", 1)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>
          <div class="tab-pane" id="tab_peninsula">

            <div class="table-responsive">
              <table id="tabla_activo4" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos5) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos5[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos5[$i]["NOMBRE"] ?></td>
                  <!--  <td>
                      <?php switch ($tablaActivos5[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos5[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos5[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos5[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos5[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                    <!--<td>
                      <?php switch ($tablaActivos5[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos5[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos5[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos5[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos5[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos5[$i]["IID_EMPLEADO"].", 1)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="tab-pane" id="tab_puebla">

            <div class="table-responsive">
              <table id="tabla_activo5" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos6) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos6[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos6[$i]["NOMBRE"] ?></td>
                    <!--<td>
                      <?php switch ($tablaActivos6[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos6[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos6[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos6[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos6[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <!--  <td>
                      <?php switch ($tablaActivos6[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos6[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos6[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos6[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos6[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos6[$i]["IID_EMPLEADO"].", 1)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="tab-pane" id="tab_bajio">

            <div class="table-responsive">
              <table id="tabla_activo6" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos7) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos7[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos7[$i]["NOMBRE"] ?></td>
                  <!--  <td>
                      <?php switch ($tablaActivos7[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos7[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos7[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos7[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos7[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <!--  <td>
                      <?php switch ($tablaActivos7[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos7[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos7[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos7[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos7[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos7[$i]["IID_EMPLEADO"].", 1)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="tab-pane" id="tab_occidete">

            <div class="table-responsive">
              <table id="tabla_activo7" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos8) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos8[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos8[$i]["NOMBRE"] ?></td>
                    <!--<td>
                      <?php switch ($tablaActivos8[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos8[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos8[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos8[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos8[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <!--  <td>
                      <?php switch ($tablaActivos8[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos8[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos8[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos8[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos8[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos8[$i]["IID_EMPLEADO"].", 1)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="tab-pane" id="tab_noreste">

            <div class="table-responsive">
              <table id="tabla_activo8" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#0073B7"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">EMPLEADO</font></th>
                    <!--<th class="small" bgcolor="#0073B7"><font color="white">GENERO</font></th>-->
                    <th class="small" bgcolor="#0073B7"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">ANTIGUEDAD</font></th>
                    <!--<th class="small" bgcolor="#0073B7"><font color="white">CONTRATO</font></th>-->
                    <th class="small" bgcolor="#0073B7"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#0073B7"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos9) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos9[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos9[$i]["NOMBRE"] ?></td>
                    <!--<td>
                      <?php switch ($tablaActivos9[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos9[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos9[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos9[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos9[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                    <!--<td>
                      <?php switch ($tablaActivos9[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos9[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos9[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos9[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos9[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos9[$i]["IID_EMPLEADO"].", 1)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>
          <!-- /.tab-pane tab_peninsula-->
        </div>
        <!-- /.tab-content -->
      </div>
    <?php } else { ?>
      <div class="nav-tabs-custom">
            <?php
                  $tablaActivos = $obj_class->tablaBajas("CORPORATIVO");
                  $tablaActivos2 = $obj_class->tablaBajas("CÓRDOBA");
                  $tablaActivos3 = $obj_class->tablaBajas("MÉXICO");
                  $tablaActivos4 = $obj_class->tablaBajas("GOLFO");
                  $tablaActivos5 = $obj_class->tablaBajas("PENINSULA");
                  $tablaActivos6 = $obj_class->tablaBajas("PUEBLA");
                  $tablaActivos7 = $obj_class->tablaBajas("BAJIO");
                  $tablaActivos8 = $obj_class->tablaBajas("OCCIDENTE");
                  $tablaActivos9 = $obj_class->tablaBajas("NORESTE");
            ?>


        <ul class="nav nav-pills" id="myTab">
          <li class="active" style="color:#2EA620;"><a href="#tab_corporativo" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL INACTIVO COORPORATIVO
            <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="Total de Personal"><?php echo count($tablaActivos); ?></span></a>
          </li>
          <li><a href="#tab_cordoba" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL INACTIVO CÓRDOBA
            <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="Total de Personal"><?php echo count($tablaActivos2); ?></span></a>
          </li>
          <li><a href="#tab_mex" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL INACTIVO MÉXICO
            <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="Total de Personal"><?php echo count($tablaActivos3); ?></span></a>
          </li>
          <li><a href="#tab_golfo" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL INACTIVO GOLFO
            <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="Total de Personal"><?php echo count($tablaActivos4); ?></span></a>
          </li>
          <li><a href="#tab_peninsula" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL INACTIVO PENINSULA
            <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="Total de Personal"><?php echo count($tablaActivos5); ?></span></a>
          </li>
          <li><a href="#tab_puebla" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL INACTIVO PUEBLA
            <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="Total de Personal"><?php echo count($tablaActivos6); ?></span></a>
          </li>
          <li><a href="#tab_bajio" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL INACTIVO BAJIO
            <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="Total de Personal"><?php echo count($tablaActivos7); ?></span></a>
          </li>
          <li><a href="#tab_occidete" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL INACTIVO OCCIDENTE
            <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="Total de Personal"><?php echo count($tablaActivos8); ?></span></a>
          </li>
          <li><a href="#tab_noreste" data-toggle="tab"><i class="fa fa-users"></i> PERSONAL INACTIVO NORESTE
            <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="Total de Personal"><?php echo count($tablaActivos9); ?></span></a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_corporativo">

          <div class="table-responsive">
            <table id="tabla_activo" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th class="small" bgcolor="#FF0000"><font color="white">ID</font></th>
                  <th class="small" bgcolor="#FF0000"><font color="white">EMPLEADO</font></th>
                  <th class="small" bgcolor="#FF0000"><font color="white">NSS</font></th>
                  <th class="small" bgcolor="#FF0000"><font color="white">RFC</font></th>
                  <th class="small" bgcolor="#FF0000"><font color="white">INGRESO</font></th>
                  <th class="small" bgcolor="#FF0000"><font color="white">ANTIGUEDAD</font></th>
                  <th class="small" bgcolor="#FF0000"><font color="white">PUESTO</font></th>
                  <th class="small" bgcolor="#FF0000"><font color="white">DEPTO</font></th>
                  <th class="small" bgcolor="#FF0000"><font color="white">AREA</font></th>
                  <th class="small" bgcolor="#FF0000"><font color="white">SALARIO</font></th>
                  <th class="small" bgcolor="#FF0000"><font color="white">VER</th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i=0; $i <count($tablaActivos) ; $i++) { ?>
                <tr>
                  <td><?= $tablaActivos[$i]["IID_EMPLEADO"] ?></td>
                  <td><?= $tablaActivos[$i]["NOMBRE"] ?></td>
                  <!--<td>
                    <?php switch ($tablaActivos[$i]["V_SEXO"]) {
                    case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                    case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                    default: echo "NO REGISTRADO"; break;
                    } ?>
                  </td>-->
                  <td><?= $tablaActivos[$i]["V_IMSS"] ?></td>
                  <td><?= $tablaActivos[$i]["V_RFC"] ?></td>
                  <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos[$i]["D_FECHA_INGRESO"] ?></span></td>
                  <td><?= $tablaActivos[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <!--<td>
                    <?php switch ($tablaActivos[$i]["S_TIPO_CONTRATO"]) {
                    case 0: echo "DETERMINADO"; break;
                    case 1: echo "TIEMPO INDETERMINADO"; break;
                    case 3: echo "POR OBRA DETERMINADA"; break;
                    default: echo "INDEFINIDO"; break;
                    } ?>
                  </td>-->
                  <td><?= $tablaActivos[$i]["PUESTO"] ?></td>
                  <td><?= $tablaActivos[$i]["DEPTO"] ?></td>
                  <td><?= $tablaActivos[$i]["AREA"] ?></td>
                  <td>$<?= number_format($tablaActivos[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                  <td class="small">

                      <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                      onclick='cargarPantalla(". $tablaActivos[$i]["IID_EMPLEADO"].", 2)'>Ver</button>";
                      ?>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="tab_cordoba">

            <div class="table-responsive">
              <table id="tablaActivos10" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#FF0000"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">ANTIGUEDAD</font></th>
                    <!--<th class="small" bgcolor="#0073B7"><font color="white">CONTRATO</font></th>-->
                    <th class="small" bgcolor="#FF0000"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos2) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos2[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos2[$i]["NOMBRE"] ?></td>
                  <!--  <td>
                      <?php switch ($tablaActivos2[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos2[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos2[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos2[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos2[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                    <!--<td>
                      <?php switch ($tablaActivos2[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos2[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos2[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos2[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos2[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos2[$i]["IID_EMPLEADO"].", 2)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>
          <!--MX -->
          <div class="tab-pane" id="tab_mex">

            <div class="table-responsive">
              <table id="tabla_activo2" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#FF0000"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos3) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos3[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos3[$i]["NOMBRE"] ?></td>
                  <!--  <td>
                      <?php switch ($tablaActivos3[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos3[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos3[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos3[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos3[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                    <!--<td>
                      <?php switch ($tablaActivos3[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos3[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos3[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos3[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos3[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos3[$i]["IID_EMPLEADO"].", 2)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="tab-pane" id="tab_golfo">

            <div class="table-responsive">
              <table id="tabla_activo3" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#FF0000"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos4) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos4[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos4[$i]["NOMBRE"] ?></td>
                    <!--<td>
                      <?php switch ($tablaActivos3[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos4[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos4[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos4[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos4[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <!--  <td>
                      <?php switch ($tablaActivos4[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos4[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos4[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos4[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos4[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos4[$i]["IID_EMPLEADO"].", 2)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>
          <div class="tab-pane" id="tab_peninsula">

            <div class="table-responsive">
              <table id="tabla_activo4" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#FF0000"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos5) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos5[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos5[$i]["NOMBRE"] ?></td>
                  <!--  <td>
                      <?php switch ($tablaActivos5[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos5[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos5[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos5[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos5[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                    <!--<td>
                      <?php switch ($tablaActivos5[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos5[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos5[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos5[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos5[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos5[$i]["IID_EMPLEADO"].", 2)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="tab-pane" id="tab_puebla">

            <div class="table-responsive">
              <table id="tabla_activo5" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#FF0000"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos6) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos6[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos6[$i]["NOMBRE"] ?></td>
                    <!--<td>
                      <?php switch ($tablaActivos6[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos6[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos6[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos6[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos6[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <!--  <td>
                      <?php switch ($tablaActivos6[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos6[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos6[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos6[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos6[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos6[$i]["IID_EMPLEADO"].", 2)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="tab-pane" id="tab_bajio">

            <div class="table-responsive">
              <table id="tabla_activo6" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#FF0000"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos7) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos7[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos7[$i]["NOMBRE"] ?></td>
                  <!--  <td>
                      <?php switch ($tablaActivos7[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos7[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos7[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos7[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos7[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <!--  <td>
                      <?php switch ($tablaActivos7[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos7[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos7[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos7[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos7[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos7[$i]["IID_EMPLEADO"].", 2)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="tab-pane" id="tab_occidete">

            <div class="table-responsive">
              <table id="tabla_activo7" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#FF0000"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">EMPLEADO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">ANTIGUEDAD</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos8) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos8[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos8[$i]["NOMBRE"] ?></td>
                    <!--<td>
                      <?php switch ($tablaActivos8[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos8[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos8[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos8[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos8[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                  <!--  <td>
                      <?php switch ($tablaActivos8[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos8[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos8[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos8[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos8[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos8[$i]["IID_EMPLEADO"].", 2)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="tab-pane" id="tab_noreste">

            <div class="table-responsive">
              <table id="tabla_activo8" class="display table table-bordered table-hover table-striped" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="small" bgcolor="#FF0000"><font color="white">ID</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">EMPLEADO</font></th>
                    <!--<th class="small" bgcolor="#0073B7"><font color="white">GENERO</font></th>-->
                    <th class="small" bgcolor="#FF0000"><font color="white">NSS</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">RFC</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">INGRESO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">ANTIGUEDAD</font></th>
                    <!--<th class="small" bgcolor="#0073B7"><font color="white">CONTRATO</font></th>-->
                    <th class="small" bgcolor="#FF0000"><font color="white">PUESTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">DEPTO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">AREA</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">SALARIO</font></th>
                    <th class="small" bgcolor="#FF0000"><font color="white">VER</font></th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i <count($tablaActivos9) ; $i++) { ?>
                  <tr>
                    <td><?= $tablaActivos9[$i]["IID_EMPLEADO"] ?></td>
                    <td><?= $tablaActivos9[$i]["NOMBRE"] ?></td>
                    <!--<td>
                      <?php switch ($tablaActivos9[$i]["V_SEXO"]) {
                      case 1: echo "<div title='FEMENINO'>FEMENINO</div>"; break;
                      case 2: echo "<div title=MASCULINO>MASCULINO</div>"; break;
                      default: echo "NO REGISTRADO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos9[$i]["V_IMSS"] ?></td>
                    <td><?= $tablaActivos9[$i]["V_RFC"] ?></td>
                    <td><span class="badge bg-verde"><i class="fa fa-calendar-check-o"></i> <?= $tablaActivos9[$i]["D_FECHA_INGRESO"] ?></span></td>
                    <td><?= $tablaActivos9[$i]["I_ANTIGUEDAD"] ?> AÑOS</td>
                    <!--<td>
                      <?php switch ($tablaActivos9[$i]["S_TIPO_CONTRATO"]) {
                      case 0: echo "DETERMINADO"; break;
                      case 1: echo "TIEMPO INDETERMINADO"; break;
                      case 3: echo "POR OBRA DETERMINADA"; break;
                      default: echo "INDEFINIDO"; break;
                      } ?>
                    </td>-->
                    <td><?= $tablaActivos9[$i]["PUESTO"] ?></td>
                    <td><?= $tablaActivos9[$i]["DEPTO"] ?></td>
                    <td><?= $tablaActivos9[$i]["AREA"] ?></td>
                    <td>$<?= number_format($tablaActivos9[$i]["C_SALARIO_MENSUAL"],2); ?></td>
                    <td class="small">
                        <?php  echo "<button type='button' name='button' data-toggle='modal' data-target='#asignacion_activos'
                        onclick='cargarPantalla(". $tablaActivos9[$i]["IID_EMPLEADO"].", 2)'>Ver</button>";
                        ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>
          <!-- /.tab-pane tab_peninsula-->
        </div>
        <!-- /.tab-content -->
      </div>
    <?php } ?>
      <!--TERMINAN LOS ACTIOS -->

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION TABLA DETALLE ########################### -->




    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
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
<?php include_once('../layouts/footer.php'); ?>

<script language="Javascript">
	function imprSeleccion(nombre) {
	  var ficha = document.getElementById(nombre);
	  var ventimp = window.open(' ', 'popimpr');
	  ventimp.document.write( ficha.innerHTML );
	  ventimp.document.close();
	  ventimp.print( );
	  ventimp.close();

	}
	</script>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
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
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
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

<!--boton de filtrar-->
<script type="text/javascript">
/*---- SELECT STATUS NOMINA ----*/

/*---- CLICK BOTON FILTRAR ----*/
$(".btnNomFiltro").on("click", function(){
  tipo = $('input:radio[name=tipo]:checked').val();

  url = '?tipo='+tipo;
  location.href = url;

});
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal Activo',
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal Activo',
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal Activo',
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal Activo',
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal Activo',
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal Activo',
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal Activo',
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal Activo',
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

    $('#tablaActivos10').DataTable( {
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
            title: 'Personal Activo',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Personal Activo',
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

<script>
     function cargarPantalla(iidconsecutivo, tipo){
               $("#modal").load("empleados_det.php?iid_emple="+iidconsecutivo+"&tipo="+tipo+"");
     }
</script>
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
