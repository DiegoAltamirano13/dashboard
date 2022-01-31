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
include_once '../class/contador_faltas_JJA.php';
include_once '../class/filtros_operaciones.php';                              /*OBJETO PARA CAMBIAR VALORES DE FILTROS DE OPERACIONES*/

$obj_filtros= new filtros_operaciones();
$obj_class = new FaltasJefes();
$empleado = $obj_class->empleados_Faltantes();
$conn   = conexion::conectar();

$_SESSION['modulo_actual'] = 0;//MODULO INDEX
$iid_modulo = $_SESSION['modulo_actual'];

/*----------------------ACTIVA INTRO AL INICIAR----------------------*/
if(isset($_POST["activa_intro_index"]))
$_SESSION["activa_intro_index"] = $_POST["activa_intro_index"];
$activa_intro_index = $_SESSION["activa_intro_index"];

/*-----------------VALIDAR SESION PARA PERSONAL DE OPERACIONES-----------------*/
//if($_SESSION['area']==3 ){

  $i_plaza=$_SESSION['i_plaza'];
  $i_depto=$_SESSION['area'];

  if ($_SESSION['nomPlaza'] == false){                                          /*APLICAR PRIMER FILTRO DEPENDIENDO DE SU PLAZA ORIGEN*/
    if($i_plaza==2){
      $_SESSION['nomPlaza'] ="CÓRDOBA";
    }else {
      $_SESSION['nomPlaza'] =$_SESSION['n_plaza'];
    }
  }else{
    if(isset($_POST['nomPlaza']))
    $_SESSION['nomPlaza'] = $_POST['nomPlaza'];
    $plaza = $_SESSION['nomPlaza'];
  }

  $plaza = $_SESSION['nomPlaza'];

  $obj_filtros->mod_5_vehiculos_manufactura();
  $obj_filtros->mod_6_operaciones_veh_agro();
  $obj_filtros->mod_30_operaciones_manufactura();
  $obj_filtros->mod_18_ubi_mercancia();
//}

?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ##################################### Contenido de la pagina #########################-->
<style type="text/css" media="screen">
 .imgwrapper {
   width: 95%;
}
</style>


<script>
// window.onload = function() {
//   tutorial_button();
// };
</script>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!--VENTANA MODAL-->
    <div class="modal fade" id="mostrarmodal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
      <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
      <h3>PERSONAL FALTANTE</h3>
      </div>
      <div class=modal-body>

        <table class="table table-striped">
             <thead>
               <tr>
                 <th>EMPLEADO</th>
                 <th>DESCRIPCION</th>
                 <th>FECHA INICIO</th>
                 <th>FECHA FIN</th>
                 <th>DIAS</th>
               </tr>
             </thead>
             <tbody>
               <?php for ($i=0; $i < COUNT($empleado); $i++) { ?>
                 <tr>
                   <td class="small">
                     <?php echo $empleado[$i]["NOMBRE"]; ?>
                   </td>
                   <td class="small">
                     <?php echo $empleado[$i]["S_DESCRIPCION"]; ?>
                   </td>
                   <td class="small">
                     <?php echo $empleado[$i]["D_FEC_INICIO"]; ?>
                   </td>
                   <td class="small">
                     <?php echo $empleado[$i]["D_FEC_FIN"]; ?>
                   </td>
                   <td class="small">
                     <?php echo $empleado[$i]["C_DIAS_FALTA"]; ?>
                   </td>
                 </tr>
               <?php } ?>
             </tbody>
        </table>
      </div>
      <div class="modal-footer">
      <a href="#" data-dismiss="modal" class="btn btn-danger">Cerrar</a>
      </div>
      </div>
      </div>
      </div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>Control panel</small>
        <?php //if($_SESSION['area']==3){echo "<center><h4> PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>";} ?><!--FILTRAR UNICAMENTE P/DEPTO. OPERACIONES -->
      </h1>
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '6, 29, 32, 30, 38, 40, 43, 5, 27, 28, 18, 45,34,36,41,42,47,49'); if ($modulos_valida > 0){ ?>
      <?php echo "<br><center><h4>PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>"; ?><!--FILTRO GENERAL -->
      <?php } ?>
      <ol class="breadcrumb">
        <li data-intro="Botón para activar la guía de ayuda" class="intro-lead-in">
        <a href="javascript:void(0)" onclick="tutorial_modal();" class="page-scroll btn btn-xl"><i class="ion-chatbubble-working">Tutorial</i></a>
        </li>
        <li data-intro="Clic aquí para desactivar o activar el mensaje de guía de ayuda al cargar la pagina">
        <form action="index.php" method="post">
        <?php
        if ($activa_intro_index == false){
          echo '<button class="btn btn-link btn-xs click_modal_cargando" type="submit" name="activa_intro_index" value="1"><i class="ion-android-done-all">Desactivar</i></button>';
        }else{
          echo '<button class="btn btn-link btn-xs click_modal_cargando" type="submit" name="activa_intro_index" value=""><i class="ion-android-done">Activar</i></button>';
        }
        ?>
        </form>
        </li>
        <?php //if ($i_plaza==2 && $_SESSION['area']==3){ ?><!-- FILTRO P/DEPTO OPERACIONES CON PLAZA CORPORATIVO -->
        <!--<li>
          <button type='button' class='btn btn-link' data-toggle="modal" data-target="#modal_sel_plaza_glo"><i class="fa fa-toggle-on"></i>  Selección de plazas</button>
        </li>-->
        <?php //} ?>
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '6, 29, 32, 30, 38, 40, 43, 5, 27, 28, 18, 45,34,36,41,42,47,49'); if ($modulos_valida > 0){ ?>
        <?php if ($i_plaza==2){ ?><!-- FILTRO P/PLAZA CORPORATIVO -->
        <li>
          <button type='button' class='btn btn-link' data-toggle="modal" data-target="#modal_sel_plaza_glo"><i class="fa fa-toggle-on"></i>  Selección de plazas</button>
        </li>
        <?php } ?>
        <?php } ?>
      </ol>
    </section><br>

    <!-- INICIA MODAL FILTRO DE SELECCION DE PLAZAS -->
    <div class="modal fade" id="modal_sel_plaza_glo" data-backdrop="static" role="dialog">
      <div class="modal-dialog modal-sm">
        <div id="content_img_modal_his[]" class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Conexiones a Plazas</h4>
          </div>
          <div class="modal-body">
            <form method="post">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
                <select class="form-control select2" name="nomPlaza" id="nomPlaza" style="width: 100%;">
                  <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
                  <?php $select_plaza = $obj_filtros->sql();
                  for ($i=0; $i <count($select_plaza) ; $i++) { ?>
                    <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
                  <?php } ?>
                </select>
              </div>
              <br>
              <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
              <button type="submit" id="click_modal_his[]" class="btn btn-success">Ok</button>
            </form>
          </div>
          <div class="modal-footer">
          </div>
        </div>
      </div>
    </div>

    <!-- TERMINA MODAL FILTRO DE SELECCION DE PLAZAS -->


    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

      <!-- =========================================================== -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa  fa-user"></i></span>

            <div class="info-box-content">
              <span class="progress-description">NICK!!</span>
              <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>
                  <span class="info-box-number">
                    <?=$_SESSION['usuario'];?>
                  </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-user-secret"></i></span>
            <div class="info-box-content">
              <span class="progress-description">Área</span>
              <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>
                  <span class="info-box-number">
                  <?php
                   $info_usuario = $instacia_modulo->info_usuario($iid_empleado);
                    for ($i=0; $i <count($info_usuario) ; $i++) {
                      echo $info_usuario[$i]["AREA"];
                    }
                  ?>
                  </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa  fa-archive"></i></span>
            <div class="info-box-content">
              <span class="progress-description"> Módulos asignados</span>
              <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>
                  <span class="info-box-number"><?= $_SESSION['n_modulos']  ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
            <div class="info-box-content">
              <span class="progress-description">FECHA ACTUAL</span>

              <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>
                  <span class="progress-description">
                     <?php
                   $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

                   $arrayDias = array( 'Domingo', 'Lunes', 'Martes',
                       'Miercoles', 'Jueves', 'Viernes', 'Sabado');

                   echo $arrayDias[date('w')].", ".date('d')." de ".$arrayMeses[date('m')-1]." de ".date('Y');
                    ?>
                  </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->



      <!-- Main row -->
      <div class="row"><!-- Inicia primer row principal -->

        <!-- Left col -->
        <section class="col-lg-12 connectedSortable"><!-- Inicia seccion izquierda  -->

          <!-- AREA CHART -->
          <div class="box box-primary box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Panel</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="box-body"><!-- box-body -->

              <!-- INICIA BOTONES DE CADA SECCION -->
              <div class="row">
                <!-- INICIA BOTON FACTURACION -->

                <!-- TERMINA BOTON FACTURACION -->
                <!-- INICIA BOTON SISTEMAS -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 1); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/sistemas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Dashboard Control de Proyectos (TIC)</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="tic.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Sistemas">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON SISTEMAS -->
                <!-- INICIA BOTON NOTIFICACIONES -->

                <!-- TERMINA BOTON NOTIFICACIONES -->
                <!-- INICIA BOTON OP MANUFACTURA -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 5); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/cargas_descargas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4> Vehiculos Manufactura</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="manufactura.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Op. Manufactura">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON OP MANUFACTURA -->
                <!-- INICIA BOTON OP AGRONEGOCIOS -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 6); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/agronegocios.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4> Vehiculos Agronegocios</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="agronegocios.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Op. Agronegocios">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON OP AGRONEGOCIOS -->
                <!-- INICIA BOTON CAPACIDAD EN BODEGA AGRONEGOCIOS -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 33); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Capacidad de Descarga/Carga <small>(Agronegocios)</small></h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="agronegocios_capbodega.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Capacidad en Bodega">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON CAPACIDAD EN BODEGA AGRONEGOCIOS -->
                <!-- INICIA BOTON COMERCIAL -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 7); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/comercial.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Dashboard Comercial</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="comercial.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Comercial">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON COMERCIAL -->
                <!-- INICIA BOTON CARTAS CUPO -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 8); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/comercio_cartas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Dashboard Cartas Cupo</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="cartas_cupo.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Cartas Cupo">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON CARTAS CUPO -->

                <!-- INICIA BOTON CARTAS CUPO ANUALES -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 24); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/comercio_cartas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Dashboard Cartas Cupo Anuales</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="cartas_cupo_anuales.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Cartas Cupo">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON CARTAS CUPO -->

                <!-- INICIA BOTON PEDIMENTOS -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 9); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/comercio_pedimentos.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Dashboard Pedimentos</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="pedimentos.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Pedimentos">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON PEDIMENTOS -->
                <!-- INICIA BOTON PEDIMENTOS ANUALES-->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 25); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/comercio_pedimentos.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class="info-box-number">
                        <h4>Dashboard Pedimentos Anuales</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="pedimentos_anuales.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Pedimentos">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- INICIA BOTON REG. INFO FINANCIERA -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 10); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/reg_info_financiera.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Dashboard Reg. Info. Financiera</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="informacion_financiera.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Reg. Info. Financiera">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON REG. INFO FINANCIERA -->
                <!-- INICIA BOTON TESORERIA -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 11); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/tesoreria_.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Dashboard Tesorería</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="tesoreria.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Tesorería">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON TESORERIA -->
                <!-- INICIA BOTON PASIVOS HIPOTECARIOS -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 12); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/pasivos_hipotecarios.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Dashboard Pasivos Hipotecarios</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="pasivos.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Pasivos Hipotecarios">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON PASIVOS HIPOTECARIOS -->
                <!-- INICIA BOTON CAPS (COBERTURAS) -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 13); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/caps_coberturas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Dashboard CAPS (COBERTURAS)</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="coberturas.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard CAPS (COBERTURAS)">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON CAPS (COBERTURAS) -->
                <!-- INICIA BOTON REMATES -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 14); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/reamtes.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Dashboard Remates</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="remates.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Remates">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON REMATES -->
                <!-- INICIA BOTON SALDO CLIENTES -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 15); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/saldos.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Dashboard Saldos Clientes</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="saldos_clientes.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Saldos Clientes">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON SALDO CLIENTES -->
                <!-- INICIA BOTON COMPARATVIO INGRESO DE CLIENTES -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 16); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/compa_ingre_clie.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Dashboard Comp. Ingresos de Clientes</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="comp_ingre_clientes.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Comparativo Ingresos de clientes">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON COMPARATISVO INGRESO DE CLIENTES -->
                <!-- INICIA BOTON VENTA DE PROMOTOR -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 17); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/venta_promotor.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Dashboard Fact. vs Presupuesto Promotores</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="venta_promotor.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Facturado vs Presupuesto Promotores">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON VENTA DE PROMOTOR -->
                <!-- INICIA BOTON BODEGA -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 21); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Ingresos</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="habilitaciones_bodega.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Facturado vs Presupuesto Promotores">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON BODEGA -->
                <!-- INICIA BOTON UBICACIÓN DE MERCANCÍA -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 18); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/rack.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Ubicación de Mercancía</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="rack.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Ubicación de Mercancía">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON UBICACIÓN DE MERCANCÍA -->
                <!-- LISTA DE PERSONAL -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 19); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/usuarios_act.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Lista de Personal</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="lista_Personal.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Lista de Personal">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>

                <!-- INICIA BOTON ROTACIÓN DE PERSONAL -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 19); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/rotacion_personal.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Rotación de Personal</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="rotacion_personal.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Rotación de Personal">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON ROTACIÓN DE PERSONAL -->
                <!-- INICIA BOTON NOMINA PAGADA -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 20); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/nomina_pagada.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Nomina Pagada</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="nomina_pagada.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!-- TERMINA BOTON NOMINA PAGADA -->
                <!-- Inicia el boton de FALTAS EMPLEADO -->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 22); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/Empleado_faltas.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Faltas Por Ausentismo/Prestaciones</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="dias_descansados.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                <!--Termina el boton de FALTAS EMPLEADO -->

                <!--HORAS EXTRAS-->
                <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 23); if($modulos_valida > 0){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div style="background-color:#D9EDF7" class="info-box">
                    <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/reloj-circular.png" alt="Icono Modulo"></span>
                    <div class="info-box-content">
                      <span class=" info-box-number">
                        <h4>Horas Extra</h4>
                      </span>
                    </div>
                    <a class="click_modal_cargando" href="tiempo_extra.php">
                    <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                    </a>
                  </div>
                </div>
                <?php } ?>
                  <!--HORAS EXTRA SALIDA -->
                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 22); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/Empleado_faltas.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Prima de riesgo</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="riesgo.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <!--Remates-->
                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 27); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/caps_coberturas.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Costos</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="Gastos_Maquinaria.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 28); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Ocupacion de Almacen</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="calculo_Ocupacion.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>


                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 41); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/cap_bodegas.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Ocupacion de Almacen (ALO)</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="CalculoOcupacionAlo.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 29); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/azucar.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Resumen Azucar</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="detalles_azucar.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 30); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/azucar.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Resumen Granos</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="detalles_granos.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 31); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/curso.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Capacitación</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="rh_cat_cursos.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 32); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/graf_azucar.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Graficas Azucar</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="azucar_x_anio.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 34); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/mercancia_danada.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4> Mercancia No Conforme</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="piezas_danadas.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>


                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 41); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/efectividad_icon.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Efectividad Cargas Y Descargas(ALO)</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="operaciones_manufactura_alo.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>


                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 42); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/tabla_ocupacion.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Tiempo de mercancia en almacen</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="OcupacionClienteAlo.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 36); if($modulos_valida > 0){
                    /*v traslado op_in_vehiculos_recibidos*/ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/correccion_icon.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Errores En Captura</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="errores_captura.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 37); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/money.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class="info-box-number">
                          <h4>Presupuesto VS Ingresos</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="detalles_Ingresos.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Sistemas">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <!--BUQUES -->
                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 38); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/buques.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class="info-box-number">
                          <h4>Transitos</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="mapas_operaciones.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Sistemas">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <!-- cartas cupo crossdock -->
                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 39); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/comercio_cartas.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class="info-box-number">
                          <h4>Cartas Cupo Anuales CrossDock</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="cartas_cupo_anuales_crossdock.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Sistemas">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>


                  <!-- Almacenajes en m2 -->
                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 40); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/tabla_ocupacion.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class="info-box-number">
                          <h4>Ocupacion En Almacen</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="tabla_ocupacion.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Sistemas">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 43); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/azucar.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Calidad en Granos</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="temperatura_Granos.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>


                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 44);  if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/saldos.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Facturacion Clientes</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="informacionClientesPresupuesto.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Facturacion Clientes Vs Presupuesto">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>


                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 45);  if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/saldos.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>INFORMACIÓN VIAS</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="vias_informacion.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Informacion Vias">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 46); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/caps_coberturas.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Facturacion X Semana</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="facturacion_saldos.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Nomina Pagada">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 47); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/rack.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Reporte (ALO)</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="reporte_alo.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Reporte Alo">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 49); if($modulos_valida > 0){ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/alo_container.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Contenedores Pendientes / Asignados (ALO)</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="Contenedores_Pendientes_ALO.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Reporte Alo">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <!--ENCUESTAS REALIZADAS-->
                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 48); if($modulos_valida > 0){
                    /*v traslado op_in_vehiculos_recibidos*/ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/correccion_icon.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Encuestas Realizadas</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="encuestas_realizadas.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Encuestas Realizadas">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>


                  <!--ENCUESTAS REALIZADAS-->
                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 50); if($modulos_valida > 0){
                    /*v traslado op_in_vehiculos_recibidos*/ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/tic_mtto.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Mttos. Realizados</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="Mttos_Tics.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Mttos Realizados">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <!--ENCUESTAS REALIZADAS-->
                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 52); if($modulos_valida > 0){
                    /*v traslado op_in_vehiculos_recibidos*/ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/saldos.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Finiquitos Realizados</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="finiquitos_RH.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Mttos Realizados">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>


                  <!--ENCUESTAS REALIZADAS-->
                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 54); if($modulos_valida > 0){
                    /*v traslado op_in_vehiculos_recibidos*/ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/cumplimiento_admon.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Indice de Emision de Notas de Credito</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="indicadores_por_bonificacion.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Indicadores por Bonificacion">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <!--ENCUESTAS REALIZADAS-->
                  <?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 55); if($modulos_valida > 0){
                    /*v traslado op_in_vehiculos_recibidos*/ ?>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div style="background-color:#D9EDF7" class="info-box">
                      <span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/complimiento_error.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class=" info-box-number">
                          <h4>Indice de Cumplimiento en Facturación de Servicios</h4>
                        </span>
                      </div>
                      <a class="click_modal_cargando" href="indicadores_por_error.php">
                      <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Indicadores por error">Entrar <i class="fa fa-arrow-circle-right"></i></button>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                    <!-- INICIA BOTON SGC -->

  	<?php $modulos_valida = $instacia_modulo->modulos_valida($iid_empleado, 56); if($modulos_valida > 0){ ?>
  		  <div class="col-md-3 col-sm-6 col-xs-12">
  		  	<div style="background-color:#D9EDF7" class="info-box">
  		  		<span style="background-color:#D9EDF7" class="info-box-icon"><img class="img-circle" src="../dist/img/modulos/revision-positiva.png" alt="Icono Modulo"></span>
                      <div class="info-box-content">
                        <span class="info-box-number">
                          <h4>Dashboard Acciones Correctivas</h4>
                         </span>
                       </div>
                     <a class="click_modal_cargando" href="sgc.php">
                  <button class="btn bg-gray  btn-block" data-intro="Botón para entrar al Dashboard Sistemas">Entrar <i class="fa fa-arrow-circle-right"></i></button>                 </a>
                </div>
              </div>
      <?php } ?>
      <!-- TERMINA BOTON SGC -->

              </div>
              <!-- TERMINA BOTONES DE CADA SECCION -->

            </div><!-- /.box-body -->
          </div>
          <!-- TERMINA AREA CHART -->

        </section><!-- Termina seccion izquierda  -->
        <!-- /.Left col -->

      </div><!-- Termina primer row principal -->
      <!-- /.row (main row)

      PLAZAS INVENTARIOS

      -->


      <!-- =========================================================== -->
      <div class="row">
        <div class="col-md-12 connectedSortable">
          <!-- Box Comment -->
          <div class="box box-widget">
            <div class="box-header  with-border">
              <div class="user-block">
                <img class="img-circle" src="../dist/img/argo_new_160x160.jpg" alt="User Image">
                <span class="username"><?=$_SESSION['nombre_user'];?></span>
                <span class="description">Inicio de sesión: <?=  date("(H:i:s)", $_SESSION['inicia']); ?></span>
              </div>
              <!-- /.user-block -->
              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Módulos Asignados</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="tabla_modulos_asignados" class="display table table-hover table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr class="bg-light-blue disabled">
                  <th class="bg-light-blue disabled" style="width: 10px">#</th>
                  <th>Módulo</th>
                  <th>Perfil</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $info_permiso = $instacia_modulo->info_permisos($iid_empleado);
                  for($i=0; $i<count($info_permiso); $i++)
                  {
                ?>
                <tr>
                  <td><?=  $i+1; ?></td>
                  <td><?= $info_permiso[$i]["MODULO"]; ?></td>
                  <td>
                  <?php
                    if ( $info_permiso[$i]["ID_PERFIL"] == 0 )
                    {
                      echo "<span class='badge bg-gray'>".$info_permiso[$i]["PERFIL"]."</span>";
                    }
                    else
                    {
                      echo "<span class='badge bg-green'>".$info_permiso[$i]["PERFIL"]."</span>";
                    }
                  ?>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
              </table>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
          <!-- /.box -->
        </div>
        <!-- /.col -->
            </div>
            <!-- /.box-body -->
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
      </div>
      <!-- /.row -->
       <!-- =========================================================== -->


    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
  <!-- /.content-wrapper -->

<!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- ######################### Libreria de Script ############################-->
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Intro Plugins -->
<script src="../plugins/intro/intro.js"></script>
<script type="text/javascript">
function tutorial_button(){
    <?php if ($activa_intro_index == false){?>  tutorial_modal(); <?php } ?>
}

function tutorial_modal(){
  introJs().setOptions({
    'showStepNumbers':'false',
    'skipLabel':'omitir',
    'prevLabel':'atras',
    'nextLabel':'siguiente',
    'doneLabel':'finalizado'
  }).start();
};
</script>

<script type="text/javascript">
      function mostrarPersonal(){
        $(document).ready(function()
             {
                $("#mostrarmodal").modal("show");
             });
      };
</script>

<?php
if (($_SESSION['usuario'] == "diego13" OR  $_SESSION['usuario'] == 'jalvarez' OR $_SESSION['usuario'] == 'david' OR $_SESSION['usuario'] == 'jorgels') && $_SESSION['mostrar'] == "0" && COUNT($empleado) > 0) {
          echo '<script>';
          echo 'mostrarPersonal();';
          echo '</script>';
          $value = "1";
          $_SESSION['mostrar'] = "1";
}
?>
<!-- Acomoda secciones -->
<script src="../dist/js/move_section.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- Sparkline -->
<script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll 1.3.0 -->
<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS 1.0.1 -->
<script src="../plugins/chartjs/Chart.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#tabla_modulos_asignados').DataTable( {
        "scrollY":        "200px",
        "scrollCollapse": true,
        "ordering": false,
        "searching": false,
        "paging":         false,
        "language": {
          "url": "../plugins/datatables/Spanish.json"
        },
    } );
} );
</script>
<!-- #################################### Termina Libreria de Script ######################################-->
<?php conexion::cerrar($conn); ?>
