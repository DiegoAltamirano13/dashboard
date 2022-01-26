<?php
if( $_SERVER['REQUEST_METHOD'] == 'POST')
{
  header("location: ".$_SERVER["PHP_SELF"]." ");
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
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 1);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/*================================ /////////// =======================================*/
include "../class/Tic.php";
$instClass = new Tic();
/* VALIDA PERFIL */
$validaPerfil = $instClass->sql(" SELECT P.IID_PERFIL perfil FROM SS_PERMISOS_MODULOS P WHERE P.IID_MODULO = 1 AND P.IID_EMPLEADO = ".$_SESSION['iid_empleado']." ");
$validaPerfil = $validaPerfil[0]["PERFIL"];
/* FECHA HOY/MAÑANA */
$fechas = $instClass->sql(" SELECT TO_CHAR(SYSDATE, 'YYYY') hoy FROM DUAL ");
$fechaActual = $instClass->sql(" SELECT TO_CHAR(SYSDATE, 'MM/YYYY') hoy FROM DUAL ");
$hoy = $fechas[0]["HOY"];
$hoy_bim = $fechaActual[0]["HOY"];
/*-------------------******* VARIABLES GET *******------------------- */
/* var fecha ini/fin */
//echo $hoy;
$mes = substr($hoy_bim, 0, 2);

switch (true) {
  case ($mes == '01' || $mes == '02') :
    $bimestre_real = '1';
    break;
  case ($mes == '03' || $mes == '04') :
    $bimestre_real = '2';
    break;
  case ($mes == '05' || $mes == '06') :
    $bimestre_real = '3';
    break;
  case ($mes == '07' || $mes == '08') :
    $bimestre_real = '4';
    break;
  case ($mes == '09' || $mes == '10') :
      $bimestre_real = '5';
      break;
  case ($mes == '11' || $mes == '12') :
        $bimestre_real = '6';
        break;
  default:
    $bimestre_real = '1 FALSO';
    break;
}

//$bimestre_actual =
if ($bimestre_real == 1) {
  $bimestre_sel = 6;
}else {
  $bimestre_sel = $bimestre_real-1;
}

if ( isset($_GET["bim"]) ){
    $bimestre_sel = $_GET["bim"];
}


$fecha = $hoy ;

if($bimestre_sel == 6){
  $fecha = $fecha -1;
}

if ( isset($_GET["anio"]) ){
  if ( $instClass->validateDate( substr($_GET["anio"],0,4),'Y') ){
    $fecha = $_GET["anio"];
  }
}
//echo $fecha;
//if ( $instClass->validateDate($f,'d-m-Y') ){
//}
/* var proyecto */
$pro = "ALL";
if( isset($_GET["pro"]) ){
  $validaPro = $instClass->sql(" SELECT P.IID_PROYECTO FROM SS_PROYECTO P ");
  for ($i=0; $i <count($validaPro) ; $i++) {
    if ( $validaPro[$i]["IID_PROYECTO"] == $_GET["pro"]){
      $pro = $_GET["pro"]; break;
    }
  }
}
/* var status */
$status = "ALL";
if ( isset($_GET["status"]) ){
  if ( $_GET["status"] == 1 || $_GET["status"] == 2 || $_GET["status"] == 3 || $_GET["status"] == 4 || $_GET["status"] == 5 ){
    $status = $_GET["status"];
  }
}
/* var bimestre */
$bimfil = "ALL";
if ( isset($_GET["bimfil"]) ){
  if ( $_GET["bimfil"] == 1 || $_GET["bimfil"] == 2 || $_GET["bimfil"] == 3 || $_GET["bimfil"] == 4 || $_GET["bimfil"] == 5 || $_GET["bimfil"] == 6 ){
    $bimfil = $_GET["bimfil"];
  }
}
/* var tarea */
$tarea = "ALL";
if( $pro != "ALL" ){
  if( isset($_GET["tarea"]) ){
    $validaTarea = $instClass->sql(" SELECT T.IID_TAREA FROM SS_TAREAS T WHERE T.IID_PROYECTO = $pro ");
    for ($i=0; $i <count($validaTarea) ; $i++) {
      if ( $validaTarea[$i]["IID_TAREA"] == $_GET["tarea"]){
        $tarea = $_GET["tarea"]; break;
      }
    }
  }
}
/* var actividad */
$actividad = "ALL";
if( $pro != "ALL" ){
  if( $tarea != "ALL" ){
    if( isset($_GET["actividad"]) ){
      $validaActividad = $instClass->sql(" SELECT T.IID_ACTIVIDAD FROM SS_ACTIVIDADES T WHERE T.IID_PROYECTO = $pro AND T.IID_TAREA = $tarea ");
      for ($i=0; $i <count($validaActividad) ; $i++) {
        if ( $validaActividad[$i]["IID_ACTIVIDAD"] == $_GET["actividad"]){
          $actividad = $_GET["actividad"]; break;
        }
      }
    }
  }
}

/*-------------------******* /.VARIABLES GET *******------------------- */
/* WIDGETS */
$widgets = $instClass->widgets($_SESSION['iid_empleado'],$bimestre_sel, $fecha);
/* PROYECTOS */
$proyecto = $instClass->proyecto($_SESSION['iid_empleado'],$bimestre_sel,$fecha,$status,$pro);
/* NUMERO DEL BIMESTRE*/
$bimestre = $instClass->bimestre();
/* FECHA INICIO/FIN DEL BIMESTRE */
$bimestreFecha = $instClass->bimestreFecha($bimestre);
/* COUNT PROYECTOS DEL BIMESTRE */
$countBim = $instClass->countBim($fecha,$bimestre_sel);
/*================================ /////////// =======================================*/
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<style type="text/css">
  tr.urlTr:hover { background: #A8DBA8; }
  td.urlTd{ cursor:pointer;}
</style>

<div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> Dashboard <small>Tecnología de la Información</small></h1>

    <div class="box-body bg-info">
      <!-- FILTRAR POR FECHA -->
      <div class="pull-right-container input-group col-md-12 col-sm-12 col-xs-12">
        <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Bimestre: </span>
        <select class="form-control select2" name="nomBimestre" id="nomBimestre" style="width: 100%;" <?php if ($_SESSION['iid_empleado'] == 1570 || $_SESSION['iid_empleado'] == 2466 ) { echo "disabled"; } ?>>
          <option value="1"   <?php if($bimestre_sel == 1){echo "selected";} ?>  >1 Bimestre</option>
          <option value="2"   <?php if($bimestre_sel == 2){echo "selected";} ?>>2 Bimestre</option>
          <option value="3"   <?php if($bimestre_sel == 3){echo "selected";} ?>>3 Bimestre</option>
          <option value="4"   <?php if($bimestre_sel == 4){echo "selected";} ?>>4 Bimestre</option>
          <option value="5"   <?php if($bimestre_sel == 5){echo "selected";} ?>>5 Bimestre</option>
          <option value="6"   <?php if($bimestre_sel == 6){echo "selected";} ?>>6 Bimestre</option>
        </select>

        <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Año: </span>
        <input type="text" name="datepicker" value = "<?= $fecha  ?>"class="form-control pull-right" id="datepicker" <?php if ($_SESSION['iid_empleado'] == 1570 || $_SESSION['iid_empleado'] == 2466 ) { echo "disabled"; } ?>>

        <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
        <span class="input-group-btn">
          <a href="<?= basename($_SERVER['PHP_SELF']) ?>"><button type="button" class="btn btn-warning btn-flat">Borrar Filtros <i class="fa fa-close"></i></button></a>
        </span>
        <?php } ?>
      </div>
    </div>

  </section>
  <!-- Main content -->
  <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->


<!-- ######################################## Inicio de Widgets ######################################### -->
<?php if( $pro == "ALL" ){ ?>
<section><!-- Inicia la seccion de los Widgets -->
 <div class="row">

  <!-- WIDGETS PROYECTOS EN PROCESO -->
  <div class="col-md-2">
    <div class="small-box bg-verde">
      <div class="inner">
        <h3 class="text-center"><?= $widgets[0]["TODOS"] ?></h3>
        <b>Total de Proyectos <?php if ( isset($_GET["fecha"]) ){echo $fecha;}else{ echo substr($fecha,6,4);} ?></b>
      </div>
      <div class="icon">
        <i class="ion ion-loop"></i>
      </div>
      <a href="<?= "?bimfil=".$bimfil."&fecha=".$fecha."&status=ALL&pro=".$pro."&tarea=".$tarea."&actividad=".$actividad; ?>" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- WIDGETS PROYECTOS POR INICIAR -->
  <div class="col-md-2">
    <div class="small-box bg-fushia3">
      <div class="inner">
        <h3 class="text-center"><?= $widgets[0]["INICIAR"] ?></h3>
        <b>Proyectos por iniciar <?php if ( isset($_GET["fecha"]) ){echo $fecha;}else{ echo substr($fecha,6,4);} ?></b>
      </div>
      <div class="icon">
        <i class="ion ion-gear-b"></i>
      </div>
      <a href="<?= "?bimfil=".$bimfil."&fecha=".$fecha."&status=2&pro=".$pro."&tarea=".$tarea."&actividad=".$actividad; ?>" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!--PROCESO WIDGED -->
  <div class="col-md-2">
    <div class="small-box bg-morado">
      <div class="inner">
        <h3 class="text-center"><?= $widgets[0]["PROCESO"] ?></h3>
        <b>Proyectos en proceso <?php if ( isset($_GET["fecha"]) ){echo $fecha;}else{ echo substr($fecha,6,4);} ?></b>
      </div>
      <div class="icon">
        <i class="ion ion-loop"></i>
      </div>
      <a href="<?= "?bimfil=".$bimfil."&fecha=".$fecha."&status=ALL&pro=".$pro."&tarea=".$tarea."&actividad=".$actividad; ?>" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- WIDGETS PROYECTOS DESFASADOS -->
  <div class="col-md-2">
    <div class="small-box bg-fuchsia2">
      <div class="inner">
        <h3 class="text-center"><?= $widgets[0]["DESFASADOS"] ?></h3>
        <b>Proyectos Desfasados <?php if ( isset($_GET["fecha"]) ){echo $fecha;}else{ echo substr($fecha,6,4);} ?></b>
      </div>
      <div class="icon">
        <i class="ion ion-alert-circled"></i>
      </div>
      <a href="<?= "?bimfil=".$bimfil."&fecha=".$fecha."&status=4&pro=".$pro."&tarea=".$tarea."&actividad=".$actividad; ?>" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- WIDGETS PROYECTOS TERMINADOS -->
  <div class="col-md-2">
    <!-- small box -->
    <div class="small-box bg-blue">
      <div class="inner">
        <h3 class="text-center"><?= $widgets[0]["TERMINADOS"] ?></h3>
        <b>Proyectos Terminados <?php if ( isset($_GET["fecha"]) ){echo $fecha;}else{ echo substr($fecha,6,4);} ?></b>
      </div>
      <div class="icon">
        <i class="fa fa-check-square"></i>
      </div>
      <a href="<?= "?bimfil=".$bimfil."&fecha=".$fecha."&status=3&pro=".$pro."&tarea=".$tarea."&actividad=".$actividad; ?>" class="small-box-footer">Detalles <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->



 </div>
</section><!-- Termina la seccion de los Widgets -->
<?php } ?>
<!-- ######################################### Termino de Widgets ######################################### -->


<!-- ############################ SECCION WHERE PROYECTO ############################# -->
<?php if( $pro != "ALL" ) { ?>
<div class="row">
<section class="col-lg-5 connectedSortable">

  <div class="connectedSortable">
  <section>
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-folder"></i> PROYECTO</h3> <a href="?bimfil=<?=$bimfil?>&fecha=<?=$fecha?>"><button class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i> Regresar al Inicio <i class="fa fa-dashboard"></i></button></a>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body">

        <?php for ($i=0; $i <count($proyecto) ; $i++) { ?>

          <center>
            <blockquote>
              <small> <?=$proyecto[$i]["IID_PROYECTO"]." - ". $proyecto[$i]["V_NOMBRE"] ?> </small>
            </blockquote>
            <div id="container1" style="height: 210px; width: 210px;"></div>
          </center>

        <!-- DIV COLLAPSE DETALLE DEL PROYECTO -->
        <div class="panel-group" id="descripcion_proyecto"><!-- abre panel-group para el acordion de descripcion -->
          <div class="panel panel-default"><!-- Inicia acordion de descripcion -->
            <div class="panel-heading"><!-- inicia panel-heading de la descripcion -->
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#descripcion_proyecto" href="#descripcion">Descripción</a>
              </h4>
            </div><!-- termina panel-heading de la descripcion -->
            <div id="descripcion" class="panel-collapse collapse in"><!-- Inicia id="descripcion"  -->
              <div class="panel-body"><!-- Inicia el body del acordion para la descripcion -->
              <?= $proyecto[$i]["V_DESCRIPCION"] ?>
              </div><!-- termina el body del acordion para la descripcion -->
            </div><!-- termina id="descripcion"  -->
          </div><!-- Termina acordion de descripcion -->
          <div class="panel panel-default"><!-- Inicia acordion de Alcance -->
            <div class="panel-heading"><!-- inicia panel-heading del Alcance -->
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#descripcion_proyecto" href="#alcance">Alcance</a>
              </h4>
            </div><!-- cierra panel-heading del Alcance -->
            <div id="alcance" class="panel-collapse collapse"><!-- Inicia id="alcance"  -->
              <div class="panel-body"><!-- Inicia el body del acordion para el alcance -->
              <?= $proyecto[$i]["V_ALCANCE"] ?>
              </div><!-- Termina el body del acordion para el alcance -->
            </div><!-- Termina id="alcance"  -->
          </div><!-- Termina acordion de Alcance -->
          <div class="panel panel-default"><!-- Inicia acordion de Justificacion -->
            <div class="panel-heading"><!-- inicia panel-heading de Justificacion -->
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#descripcion_proyecto" href="#justificacion">Justificación</a>
              </h4>
            </div><!-- cierra panel-heading de Justificacion -->
            <div id="justificacion" class="panel-collapse collapse"><!-- Inicia id="justificacion"  -->
              <div class="panel-body"><!-- Inicia el body del acordion para la justificacion -->
              <?= $proyecto[$i]["V_JUSTIFICACION"] ?>
              </div><!-- Termina el body del acordion para la justificacion -->
            </div><!-- Termina id="justificacion"  -->
          </div><!-- Termina acordion de Justificacion -->
          <div class="panel panel-default"><!-- Inicia acordion del Solicitante -->
            <div class="panel-heading"><!-- inicia panel-heading del Solicitante -->
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#descripcion_proyecto" href="#solicitante">Solicitante del Proyecto</a>
              </h4>
            </div><!-- cierra panel-heading del Solicitante -->
            <div id="solicitante" class="panel-collapse collapse"><!-- Inicia id="solicitante"  -->
              <div class="panel-body"><!-- Inicia el body del acordion para el Solicitante -->
              <?= $proyecto[$i]["SOLICITA"] ?>
              </div><!-- Termina el body del acordion para el Solicitante -->
            </div><!-- Termina id="solicitante"  -->
          </div><!-- Termina acordion de Solicitante -->
           <div class="panel panel-default"><!-- Inicia acordion del Solicitante -->
            <div class="panel-heading"><!-- inicia panel-heading del Solicitante -->
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#descripcion_proyecto" href="#lider_pro">Líder del Proyecto</a>
              </h4>
            </div><!-- cierra panel-heading del Solicitante -->
            <div id="lider_pro" class="panel-collapse collapse"><!-- Inicia id="lider_pro"  -->
              <div class="panel-body"><!-- Inicia el body del acordion para el Solicitante -->
              <?= $proyecto[$i]["LIDER"] ?>
              </div><!-- Termina el body del acordion para el Solicitante -->
            </div><!-- Termina id="lider_pro"  -->
          </div><!-- Termina acordion de Solicitante -->

          <div class="panel panel-default"><!-- FECHA DE INICIO -->
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#descripcion_proyecto" href="#fech_ini">Fecha de Inicio</a>
              </h4>
            </div>
            <div id="fech_ini" class="panel-collapse collapse">
              <div class="panel-body">
              <div><i class="fa fa-calendar-plus-o"></i>  <b>Fecha Estimada:</b> <?= $proyecto[$i]["D_FECHA_INICIO"] ?> </div> <div><i class="fa fa-fw fa-calendar-check-o"></i> <b>Fecha Real:</b> <?= $proyecto[$i]["D_FECHA_INI_REAL"] ?> </div>
              </div>
            </div>
          </div><!-- /.FECHA DE INICIO -->
          <div class="panel panel-default"><!-- FECHA FINAL -->
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#descripcion_proyecto" href="#fech_fin">Fecha Final</a>
              </h4>
            </div>
            <div id="fech_fin" class="panel-collapse collapse">
              <div class="panel-body">
              <div><i class="fa fa-calendar-plus-o"></i>  <b>Fecha Estimada:</b> <?= $proyecto[$i]["D_FECHA_FIN"] ?> </div> <div><i class="fa fa-fw fa-calendar-check-o"></i> <b>Fecha Real:</b> <?= $proyecto[$i]["D_FECHA_FIN_REAL"] ?> </div>
              </div>
            </div>
          </div><!-- /.FECHA FINAL -->

        </div> <!-- cierra panel-group para el acordion de descripcion -->
        <!-- /.DIV COLLAPSE DETALLE DEL PROYECTO -->
        <?php } ?>

      </div>
    </div>
  </section>
  </div>

</section>

<section class="col-lg-7 connectedSortable">


<!-- **************************** SECCION DE TAREAS **************************** -->
  <div class="connectedSortable">
  <section>
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-folder-open"></i> TAREAS</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body">

        <?php
        $tareas = $instClass->tareas($_SESSION['iid_empleado'],$pro);
        ?>
        <div class="table-responsive"><!-- table-responsive -->
          <table class="table table-bordered"><!-- inicia tabla de tareas -->
            <tr>
              <th style="width: 10px">ID</th>
              <th class='small'>Nombre</th>
              <th class='small'>Inicio</th>
              <th class='small'>Final</th>
              <th class='small'>Progreso</th>
              <th class='small'>Porcentaje</th>
              <th class='small'>Status</th>
            </tr>
            <?php for ($i=0; $i <count($tareas) ; $i++) {
              /* CONDICIONES SI TENEMOS DATOS EN D_FECHA_INICIO,D_FECHA_INI_REAL,D_FECHA_FIN,D_FECHA_FIN_REAL */
              #echo  $tareas[$i]["D_FECHA_INICIO";

              if (is_null($tareas[$i]["D_FECHA_INICIO"]) AND is_null($tareas[$i]["D_FECHA_INI_REAL"]) AND is_null($tareas[$i]["D_FECHA_FIN"]) && is_null($tareas[$i]["D_FECHA_FIN_REAL"])){
                $colorBar = "default"; $colorBtn = "default";
              }
              else if ( $tareas[$i]["D_FECHA_INICIO"]==true && $tareas[$i]["D_FECHA_INI_REAL"] ==true
                  && $tareas[$i]["D_FECHA_FIN"]==true && $tareas[$i]["D_FECHA_FIN_REAL"]==true ){ // if

                //Tareas que se iniciaron desfasadas pero se terminó en el tiempo estipulado aunque pasó la fecha de entrega – barra en rojo e indicador de porcentaje en amarillo
                if ( strtotime($tareas[$i]["D_FECHA_INICIO"]) < strtotime($tareas[$i]["D_FECHA_INI_REAL"])
                    && strtotime($tareas[$i]["D_FECHA_FIN"] ) < strtotime($tareas[$i]["D_FECHA_FIN_REAL"] )
                    ){// if para inicio-desafasado termino-desafasado
                  $colorBar = "red"; $colorBtn = "yellow";
                }//IF INICIO CORRECTO TERMINO DESFASADO
                if ( strtotime($tareas[$i]["D_FECHA_INICIO"]) <= strtotime($tareas[$i]["D_FECHA_INI_REAL"])
                    && strtotime($tareas[$i]["D_FECHA_FIN"] ) < strtotime($tareas[$i]["D_FECHA_FIN_REAL"] )
                    ){// if para inicio-desafasado termino-desafasado
                  $colorBar = "red"; $colorBtn = "red";
                }
                // /.if para inicio-desafasado termino-desafasado
                //Tareas que se iniciaron desfasadas pero se terminó  y entregó en la fecha de indicada – barra en rojo e indicador de porcentaje en azul
                else if(strtotime($tareas[$i]["D_FECHA_INICIO"]) < strtotime($tareas[$i]["D_FECHA_INI_REAL"])
                        && $tareas[$i]["D_FECHA_FIN"] == $tareas[$i]["D_FECHA_FIN_REAL"] ){ // else if para inicio-desafasado termino-bien /* @ki */
                  $colorBar = "red"; $colorBtn = "aqua";
                }// /.else if para inicio-desafasado termino-bien
                //Tareas que se iniciaron bien pero se terminó  y entregó en la fecha de desface – barra en azul e indicador de porcentaje en amarillo
                else if(strtotime($tareas[$i]["D_FECHA_INICIO"]) >= strtotime($tareas[$i]["D_FECHA_INI_REAL"])
                        && strtotime($tareas[$i]["D_FECHA_FIN"]) < strtotime($tareas[$i]["D_FECHA_FIN_REAL"])){// else if para inicio-bien termino-desafasado  /* @ki */
                  $colorBar = "aqua"; $colorBtn = "yellow";
                }// /.else if para inicio-bien termino-desafasado
                //Tareas que se iniciaron en tiempo y se terminó en tiempo y forma/Tareas que se iniciaron en tiempo y se terminó antes de la fecha indicada/Tareas que se iniciaron antes de tiempo y se terminó antes de la fecha indicada– barra en azul e indicador de porcentaje en azul
                else if($tareas[$i]["D_FECHA_INICIO"] == $tareas[$i]["D_FECHA_INI_REAL"]
                        && $tareas[$i]["D_FECHA_FIN"] == $tareas[$i]["D_FECHA_FIN_REAL"]
                        || $tareas[$i]["D_FECHA_INICIO"] == $tareas[$i]["D_FECHA_INI_REAL"]
                        && $tareas[$i]["D_FECHA_FIN"] > $tareas[$i]["D_FECHA_FIN_REAL"]
                        || $tareas[$i]["D_FECHA_INICIO"] > $tareas[$i]["D_FECHA_INI_REAL"]
                        && isset($tareas[$i]["D_FECHA_INI_REAL"])
                        && strtotime($tareas[$i]["D_FECHA_FIN"]) > strtotime($tareas[$i]["D_FECHA_INI_REAL"])
                        && isset($tareas[$i]["D_FECHA_FIN_REAL"])
                        || strtotime($tareas[$i]["D_FECHA_INICIO"]) > strtotime($tareas[$i]["D_FECHA_INI_REAL"])
                        && $tareas[$i]["D_FECHA_FIN"] == $tareas[$i]["D_FECHA_FIN_REAL"]){// else if para inicio-bien termino-desafasado /* @ki */
                  $colorBar = "aqua"; $colorBtn = "aqua";
                  }// /.else if para inicio-bien termino-desafasado
                  //evalua si el estatus esta en desface para pintar el color en rojo
                  else{
                    $colorBar = "red"; $colorBtn = "red";
                  }
                  //echo "1";
              }// /.if
              /* /.CONDICIONES SI TENEMOS DATOS EN D_FECHA_INICIO,D_FECHA_INI_REAL,D_FECHA_FIN,D_FECHA_FIN_REAL */

              /* EVALUAMOS SI HAY DATOS EN D_FECHA_INI_REAL */
              else if($tareas[$i]["D_FECHA_INICIO"] == true && $tareas[$i]["STATUS"] <> 'PENDIENTE'){// else if para ver hay datos en d_fecha_ini_real

                //Tareas que se iniciaron desfasadas pero se terminó en el tiempo estipulado aunque pasó la fecha de entrega//Tareas que se iniciaron bien pero paso la fecha de entrega - todo en rojo
                if(strtotime($tareas[$i]["D_FECHA_INICIO"]) < strtotime($tareas[$i]["D_FECHA_INI_REAL"])&& strtotime($tareas[$i]["D_FECHA_FIN"]) < strtotime($hoy) || strtotime($tareas[$i]["D_FECHA_INICIO"]) >= strtotime($tareas[$i]["D_FECHA_INI_REAL"]) && strtotime($tareas[$i]["D_FECHA_FIN"]) < strtotime($hoy)  ){
                  $colorBar = "red"; $colorBtn = "red";
                }
                //Tareas apunto de terminar barra en amarillo
                else if( strtotime($tareas[$i]["D_FECHA_FIN"]) == strtotime($hoy)  ){
                  $colorBar = "yellow"; $colorBtn = "yellow";
                }
                //Tareas que iniciaron desfasadas pero se entregara en tiempo estupulado barra en rojo y porcentaje naranja
                else if ( strtotime($tareas[$i]["D_FECHA_INICIO"]) < strtotime($tareas[$i]["D_FECHA_INI_REAL"]) && strtotime($tareas[$i]["D_FECHA_FIN"]) >= strtotime($hoy) && $tareas[$i]["STATUS"] <> 'DESFASADO'  ){ /* @ki */
                  $colorBar = "red"; $colorBtn = "aqua";
                }
                //tareas que iniciaron bien o antes del la fecha inicial y se entregaran a tiempo- todo de azul
                else if ( strtotime($tareas[$i]["D_FECHA_INICIO"]) >= strtotime($tareas[$i]["D_FECHA_INI_REAL"]) && strtotime($tareas[$i]["D_FECHA_FIN"]) > strtotime($hoy) && $tareas[$i]["STATUS"] <> 'DESFASADO'  ){/* @ki */
                  $colorBar = "aqua"; $colorBtn = "aqua";
                }else{
                  $colorBar = "red"; $colorBtn = "red";
                }
              }// /.else if para ver hay datos en d_fecha_ini_real
              /* /.EVALUAMOS SI HAY DATOS EN D_FECHA_INI_REAL */

              /* EVALUAMOS SI D_FECHA_INI_REAL NO TIENE DATOS */
              else if ($tareas[$i]["D_FECHA_INI_REAL"] == false OR is_null($tareas[$i]["D_FECHA_INI_REAL"]))
              {

                //evaluamos si d_fecha_fin es igual o menor a D_FECHA_FIN_REAL mientras que no este basio D_FECHA_FIN_REAL para pintar todo en azul
                if( $tareas[$i]["D_FECHA_FIN"] >= $tareas[$i]["D_FECHA_FIN_REAL"] && isset($tareas[$i]["D_FECHA_FIN_REAL"]) && $tareas[$i]["STATUS"] <> 'DESFASADO' ){/* @ki */
                  $colorBar = "aqua"; $colorBtn = "aqua";
                }
                // evaluamos si d_fecha_fin es menor a la fecha de hoy para pinta todo de color rojo
                else if(strtotime($tareas[$i]["D_FECHA_FIN"]) < strtotime($hoy) ){
                  if($tareas[$i]["D_FECHA_FIN"] == 'PENDIENTE' )//aqui code mal aproposito =D ¿?
                  {
                    $colorBar = "aqua"; $colorBtn = "aqua";
                  }else{
                    $colorBar = "red"; $colorBtn = "red";
                  }
                }
                // evaluamos si no hay datos en d_fecha_fin y d_fcha
                else if( $tareas[$i]["D_FECHA_FIN"] == false ){
                  $colorBar = "default"; $colorBtn = "default";
                }
                // evaluamos si d_fecha_fin es mallor a la fecha de hoy para pinta todo de color azul
                else if(strtotime($tareas[$i]["D_FECHA_FIN"]) >= strtotime($hoy) && $tareas[$i]["D_FECHA_INI_REAL"]==true ){
                  $colorBar = "aqua"; $colorBtn = "aqua";
                }
                else if ( strtotime($tareas[$i]["D_FECHA_INICIO"]) > strtotime($hoy) ){
                  $colorBar = "default"; $colorBtn = "default";
                }else{
                  $colorBar = "default"; $colorBtn = "red";
                }

              }
              /* /.EVALUAMOS SI D_FECHA_INI_REAL NO TIENE DATOS */
              elseif (empty($tareas[$i]["D_FECHA_INI_REAL"])) {
                $colorBar = "default"; $colorBtn = "default";
              }
              else{

              }
            ?>

            <tr <?php if( $tareas[$i]["IID_TAREA"] == $tarea ){ echo 'style="background: #A8DBA8;"'; } ?> class="urlTr" onclick="location.href='?bimfil=<?=$bimfil?>&fecha=<?=$fecha?>&pro=<?=$pro?>&tarea=<?= $tareas[$i]["IID_TAREA"] ?>';">
              <td class='urlTd'><?= $tareas[$i]["IID_TAREA"] ?></td>
              <td class='urlTd'><?= $tareas[$i]["V_NOMBRE"] ?></a></td>
              <td class='urlTd'>
                <i class='fa fa-calendar' data-toggle='tooltip' title='Inicio: <?= $tareas[$i]["D_FECHA_INICIO"] ?>'></i>
                <i class='fa fa-calendar' data-toggle='tooltip' title='Inicio Real: <?= $tareas[$i]["D_FECHA_INI_REAL"] ?>'></i>
              </td>
              <td class='urlTd'>
                <i class='fa fa-calendar-check-o' data-toggle='tooltip' title='Inicio: <?= $tareas[$i]["D_FECHA_FIN"] ?>'></i>
                <i class='fa fa-calendar-check-o' data-toggle='tooltip' title='Inicio Real: <?= $tareas[$i]["D_FECHA_FIN_REAL"] ?>'></i>
              </td>
              <td class='urlTd'>
                <div class='progress progress-xs'>
                  <div class='progress-bar bg-<?=$colorBar?>' style='width:<?= $tareas[$i]["N_PORCENTAJE"] ?>%'></div>
                </div>
              </td>
              <td class='urlTd'><span class='badge bg-<?=$colorBtn?>'><?= $tareas[$i]["N_PORCENTAJE"] ?>%</span></td>
              <td class='urlTd'><?= $tareas[$i]["STATUS"] ?></td>
            </tr>
            <?php } ?>
          </table>
        </div><!-- /.table-responsive -->

      </div>
    </div>
  </section>
  </div>
<!-- **************************** /.SECCION DE TAREAS **************************** -->


<!-- **************************** SECCION DE ACTIVIDADES **************************** -->
  <?php if( $tarea != "ALL" ){ ?>
  <?php $actividades = $instClass->actividades($_SESSION['iid_empleado'],$pro,$tarea); ?>

  <div class="connectedSortable">
  <section>
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-cubes"></i> ACTIVIDADES DE LA TAREA <b><?= @$actividades[0]["V_NOMBRE"] ?></b> </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body">

        <div class="table-responsive">
          <table class="table table-bordered">
            <tr>
              <th class='small' >ID</th>
              <th class='small' >Nombre de la Act.</th>
              <th class='small'>Inicio</th>
              <th class='small'>Final</th>
              <th class='small' >Responsable</th>
              <th class='small' >Observaciones</th>
              <th style="width: 190px" class='small' >Atraso</th>
            </tr>
            <?php
            for ($i=0; $i <count($actividades) ; $i++) {
            // #################### Inicia code para encontrar la palabra REUNIÓN-REUNION-REUNIONES #######################
              $cadena_string = @$actividades[$i]["ACTIVIDAD"];//Especificamos en que columna busamos la palabra
              $palabra1   = 'REUNIÓN'; //Especificamos la palabra a buscar
              $palabra2   = 'REUNIONES';//Especificamos la palabra a buscar
              $palabra3   = 'REUNION'; //Especificamos la palabra a buscar

              $busca1 = strpos($cadena_string, $palabra1);//indicamos la palabra que buscara en $cadena_string
              $busca2 = strpos($cadena_string, $palabra2);//indicamos la palabra que buscara en $cadena_string
              $busca3 = strpos($cadena_string, $palabra3);//indicamos la palabra que buscara en $cadena_string
            // #################### Termina code para encontrar la palabra REUNIÓN-REUNION-REUNIONES #######################
            ?>
            <tr <?php if( $actividades[$i]["IID_ACTIVIDAD"] == $actividad ){ echo 'style="background: #B8C7CE;"'; } ?>>
              <td> <?= @$actividades[$i]["IID_ACTIVIDAD"] ?> </td>
              <td>
                <?php if ( $busca1 == false && $busca2 == false && $busca3 == false ){
                  echo @$actividades[$i]["ACTIVIDAD"];
                }else{

                  if ( isset($actividades[$i]["B_MINUTA"]) ){
                    echo @$actividades[$i]["ACTIVIDAD"].'<a target="_blank" href="'.@$actividades[$i]["B_MINUTA"].'"><span class="badge btn-info"><i class="fa fa-eye"></i>  Ver Minuta</span></a>';
                  }else{
                    echo @$actividades[$i]["ACTIVIDAD"];
                    if ( $validaPerfil == 1 || $validaPerfil == 2 ){
                       echo '  <a href="upload_minuta.php?iid_proyecto[]='.@$actividades[$i]["IID_PROYECTO"].'&iid_tarea[]='.@$actividades[$i]["IID_TAREA"].'&iid_actividad[]='.@$actividades[$i]["IID_ACTIVIDAD"].'"><span class="badge btn-warning"><i class="fa fa-cloud-upload"></i> Subir Minuta</span></a>';
                    }
                  }
                }
                ?>
              </td>
              <td>
                <i class='fa fa-calendar' data-toggle='tooltip' title='Inicio: <?= @$actividades[$i]["D_FECHA_INICIO"] ?>'></i>
                <i class='fa fa-calendar' data-toggle='tooltip' title='Inicio Real: <?= @$actividades[$i]["D_FECHA_INI_REAL"] ?>'></i>
              </td>
              <td>
                <i class='fa fa-calendar-check-o' data-toggle='tooltip' title='Inicio: <?= @$actividades[$i]["D_FECHA_FIN"] ?>'></i>
                <i class='fa fa-calendar-check-o' data-toggle='tooltip' title='Inicio Real: <?= @$actividades[$i]["D_FECHA_FIN_REAL"] ?>'></i>
              </td>
              <td> <?= @$actividades[$i]["RESPONSABLE"] ?> </td>
              <td><?= @$actividades[$i]["V_OBSERVACIONES"]?></td>
              <td style='width: 90px'>
              <?php
              $colorDet = "btn-warning";
              //evaluamos si la actividad no tiene datos en D_FECHA_FIN_REAL para contar desde la variable $var_fecha_hoy
               if ( is_null(@$actividades[$i]["D_FECHA_FIN_REAL"]) ){
                $date1=date_create(@$actividades[$i]["D_FECHA_FIN"]);
                $date2=date_create($hoy);
                $diff=date_diff($date1,$date2);
                $diasDiff = $diff->format("%R%a días");
                //evaluamos si $d_fecha_fin es menor a la fecha de hoy para mostrar los dias de atraso y los detalles de la desviacion
                if( $date1< $date2 ){
                  echo "<span class='badge bg-red'><i class='fa fa-clock-o'></i>  $diasDiff </span>";
                  /*ver si tiene desviciones*/
                  $colorDet = "btn-danger";
                }
                //evaluamos si $d_fecha_fin es iagual a la fecha de hoy para alerta en riesgo de tener una desviacion
                else if ( $date1 == $date2 ){
                  echo "<span class='badge bg-yellow'><i class='fa fa-clock-o'></i>  $imprime_dias_null</span>";
                }else{//si $d_fecha_fin es mallor a la fecha de hoy entonces no hay riesgo de tener desviaciones
                  echo "<span class='badge btn-info'><i class='fa fa-clock-o'></i> +0 días</span>";
                }
               }
               //evaluamos si la actividad si tiene datos en D_FECHA_FIN_REAL para contar desde D_FECHA_FIN_REAL
               else if ( @$actividades[$i]["D_FECHA_FIN_REAL"] == true ) {
                $date1=date_create(@$actividades[$i]["D_FECHA_FIN"]);
                $date2=date_create(@$actividades[$i]["D_FECHA_FIN_REAL"]);
                $diff=date_diff($date1,$date2);
                $diasDiff = $diff->format("%R%a días");
                //evaluaion si los dias son 0 para no mostrar detalles
                if ($date1 > $date2 ){
                  if($date1 == $date2){
                    echo "<span class='badge btn-info'><i class='fa fa-clock-o'></i>  $diasDiff </span>";
                  }else{
                    echo "<span class='badge btn-info'><i class='fa fa-clock-o'></i>  +0 días </span>";
                  }
                }
                //si los dias de atrazo son mas de 0 entonces mostramos los detalles
                else if ($date1 < $date2 ){
                  echo "<span class='badge bg-yellow'><i class='fa fa-clock-o'></i>  $diasDiff </span> ";
                  /*ver si tiene desviciones*/
                  $colorDet = "btn-danger";
                }
                //si los dias de atrazo son mas de 0 entonces mostramos los detalles
                else{
                  echo "<span class='badge btn-info'><i class='fa fa-clock-o'></i>  $diasDiff </span>";
                }
               }
               /*ver si tiene desviciones*/
                $checaDes = $instClass->sql(" SELECT COUNT(*) VAL FROM SS_DESVIACIONES D WHERE D.IID_PROYECTO = ".@$actividades[$i]["IID_PROYECTO"]." AND D.IID_TAREA = ".@$actividades[$i]["IID_TAREA"]." AND D.IID_ACTIVIDAD = ".@$actividades[$i]["IID_ACTIVIDAD"]." ");
                if ( $checaDes[0]["VAL"] > 0 ){
                  echo "  <a href='".basename($_SERVER['PHP_SELF'])."?bimfil=".$bimfil."&fecha=".$fecha."&pro=".$pro."&tarea=".$tarea."&actividad=".@$actividades[$i]["IID_ACTIVIDAD"]."'><button type='button' class='btn $colorDet btn-xs'><i class='fa fa-eye'> Detalles</i></button></a>";
                }
              ?>
              </td>
            </tr>
            <?php } ?>
          </table>
        </div>

      </div>
    </div>
  </section>
  </div>
  <?php } ?>
<!-- **************************** /.SECCION DE ACTIVIDADES **************************** -->


<!-- **************************** SECCION DE DESVIACIONES **************************** -->
  <?php if( $actividad != "ALL" ){ ?>
  <?php $desviciones = $instClass->desviciones($pro,$tarea,$actividad); ?>
  <div class="connectedSortable">
  <section>
    <div class="box box-danger">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-unlink"></i> DESVIACIONES DE LA ACTIVIDAD <b class="small"><?= $desviciones[0]["V_NOMBRE"] ?></b> </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body">

      <div class="table-responsive">
        <table class="table table-bordered"><!-- inicia tabla de desviaciones -->
        <tr>
          <th style="width: 10px">ID</th>
          <th class='small' >Nombre</th>
          <th class='small' >Razón</th>
          <th class='small' >Comentarios</th>
          <th class='small' >Involucrado</th>
        </tr>
        <?php for ($i=0; $i <count($desviciones) ; $i++) { ?>
        <tr>
          <td><?= $desviciones[$i]["IID_DESVIACION"] ?></td>
          <td><?= $desviciones[$i]["DESVIACION"] ?></td>
          <td><?= $desviciones[$i]["V_RAZON"] ?></td>
          <td><?= $desviciones[$i]["V_OBSERVACIONES"] ?></td>
          <td><?= $desviciones[$i]["RESPONSABLE"] ?></td>
        </tr>
        <?php } ?>
      </table>
      </div>

      </div>
    </div>
  </section>
  </div>
  <?php } ?>
<!-- **************************** /.SECCION DE DESVIACIONES **************************** -->



</section>
</div>
<?php } ?>
<!-- ########################### /.SECCION WHERE PROYECTO ########################### -->



<!-- ############################ SECCION GRAFICA PIE PROYECTOS ############################# -->
<?php if( $pro == "ALL" ) { ?>
<div class="row">

  <div class="col-md-12 col-sm-12 col-xs-12 connectedSortable">
  <section>
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">
          <?php
          switch ($status) {
            case '1': echo 'Proyectos Activos'; break;
            case '2': echo '<i class="ion ion-gear-b"></i> Proyectos por Iniciar'; break;
            case '3': echo '<i class="fa fa-check-square"></i> Proyectos Terminados'; break;
            case '4': echo '<i class="ion ion-alert-circled"></i> Proyectos Desfasados'; break;
            case '5': echo '<i class="fa fa-check-square"></i> Proyectos Terminados en Tiempo'; break;
            default: echo '<i class="ion ion-loop"></i> Todos los Proyectos'; break;
          }
          ?>
        </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-header with-border"><!-- Inicia seccion del titulo y botones minimizar y cerrar -->
        <strong><i class="fa fa-pencil margin-r-5"></i> Indicador de color</strong>
        <p>
          <span class="label bg-verde">PROYECTOS A TIEMPO</span>
          <span class="label label-warning">PROYECTOS EN RIESGO DE DESFASE</span>
          <span class="label bg-fuchsia2">PROYECTOS DESFASADOS</span>
          <span class="label bg-morado">PROYECTOS POR INICIAR</span>
        </p>
      </div><!-- Termina seccion del titulo y botones minimizar y cerrar -->
      <div class="box-body"><!--box-body-->

      <div style="padding:0;width:700;">
        <?php for ($i=0; $i <count($proyecto) ; $i++) { ?>
          <div data-toggle="tooltip" data-placement="bottom"
           title="<?= htmlspecialchars($proyecto[$i]["IID_PROYECTO"]."-".str_replace( '<', '', $proyecto[$i]['V_NOMBRE'])); ?>" id="pieProyecto<?=$proyecto[$i]["IID_PROYECTO"]?>" style="height: 150px; width: 150px;display:inline-block;"></div>
        <?php } ?>
      </div>

      </div><!--/.box-body-->
    </div>
  </section>
  </div>

</div>
<?php } ?>
<!-- ########################### /.SECCION GRAFICA PIE PROYECTOS ########################### -->



<!-- ############################ SECCION EVALUCION BIMESTRAL GRAFICA PIE/COLUMN ############################# -->
<?php if( $pro == "ALL" ) { ?>
<div class="row">

  <div class="col-md-12 col-sm-12 col-xs-12 connectedSortable">
  <section>
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">EVALUACIÓN BIMESTRAL</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->
        <div id="graficaPie" style="width:100%;height:100%;"></div>

      </div><!--/.box-body-->
    </div>
  </section>
  </div>


  <!--<div class="col-md-6 col-sm-6 col-xs-12 connectedSortable">
  <section>
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">EVALUACIÓN ANUALES</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body

        <div id="graficaAnio" style="width:100%;height:100%;"></div>

      </div><!--/.box-body
    </div>
  </section>
  </div>-->

</div>
<?php } ?>
<!-- ########################### /.SECCION EVALUCION BIMESTRAL GRAFICA PIE/COLUMN ########################### -->






  </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
</div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
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
<!-- Acomoda secciones -->
<script src="../dist/js/move_section.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>

<script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
  //Date picker
    $('#datepicker').datepicker({
      autoclose: true,
      format: "yyyy",
      viewMode: "years",
      minViewMode: "years"
    });
</script>

<!-- //////////////////////// SCRIPT PARA FILTRAR //////////////////////// -->
<script type="text/javascript">
$('input[name="datepicker"]').change(function(){
  bimestre = $("#nomBimestre").val();
  anio = $("#datepicker").val();

  url = '?bim='+bimestre+'&anio='+anio;
  location.href = url;

})
</script>
<script type="text/javascript">

$('select[name="nomBimestre"]').change(function(){
  bimestre = $("#nomBimestre").val();
  anio = $("#datepicker").val();

  url = '?bim='+bimestre+'&anio='+anio;
  location.href = url;

})
</script>
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
/*--------------------------------------------------*/
<?php if( $pro == "ALL" ) { ?>
<?php for ($i=0; $i <count($proyecto) ; $i++) {
  if ($bimestre_sel == 1) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }elseif ($bimestre_sel == 2 ) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('04/$fecha','MM/YYYY')) , 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }elseif ($bimestre_sel == 3) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('06/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }elseif ($bimestre_sel == 4) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('08/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }elseif ($bimestre_sel == 5) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('10/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }elseif ($bimestre_sel == 6) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('12/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }

  $color = "#4CB5AE";
  /*COLOR PROYECTOS POR INICIAR (STATUS 1)*/
  if ($proyecto[$i]['IID_STATUS'] == 1){ $color = "#7E8C8D"; }
  /*COLOR PROYECTOS EN PROCESO (STATUS 2)*/
  if ($proyecto[$i]['IID_STATUS'] == 2){
    //DESFASADO
    if( strtotime($proyecto[$i]['D_FECHA_FIN']) < strtotime($hoy) ){ $color = "#D81B60"; }
    //RIESGO DE DESFASE
    if( strtotime($proyecto[$i]['D_FECHA_FIN']) == strtotime($hoy) || strtotime($proyecto[$i]['D_FECHA_FIN']) == strtotime($manana) ){ $color = "#DB8B0B"; }
  }
  /*COLOR PROYECTOS TERMINADOS (STATUS 3)*/
  if ($proyecto[$i]['IID_STATUS'] == 3){
    if( strtotime($proyecto[$i]['D_FECHA_FIN']) < strtotime($proyecto[$i]['D_FECHA_FIN_REAL']) ){ $color = "#D81B60"; }
  }
  /*COLOR PROYECTOS DESFASADOS (STATUS 4)*/
  if ($proyecto[$i]['IID_STATUS'] == 4){ $color = "#D81B60"; }
?>
$(function() {
  var chart1 = new Highcharts.Chart({
    chart: {
        renderTo: 'pieProyecto<?=$proyecto[$i]["IID_PROYECTO"]?>',
        type: 'pie'
    },
    exporting: {
      enabled:false
    },
    credits: {
        enabled: false
    },
    title: {
      useHTML: true,
      text: "<a href='?bimfil=<?=$bimfil?>&fecha=<?=$fecha?>&pro=<?=$proyecto[$i]["IID_PROYECTO"];?>'><marquee style='font-size:12px' bgcolor='#F5F5F5' scrolldelay =180> <?= htmlspecialchars(preg_replace("/(\n|\r|\n\r)/", ' ', $proyecto[$i]["V_NOMBRE"]));  ?> </marquee></a>",
      y: 10,
      verticalAlign: 'bottom',
    },
    // tooltip: {
    //   headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
    //   pointFormat: '<tr><td style="color:{series.color};padding:0"><b>AVANCE:</b> </td>' +
    //       '<td style="padding:0"><b>{point.y:,.0f}% </b></td></tr>',
    //   footerFormat: '</table>',
    //   useHTML: true
    // },
    tooltip: {
      headerFormat: '<span style="font-size:12px">{point.key}: </span>',
      pointFormat: '<b>{point.y:,.0f}% </b>',
      useHTML: true
    },
    plotOptions: {
      pie: {
        borderColor: '#E9E9E9',//color linea contorno
        innerSize: '70%',
        dataLabels: {
          enabled: false
        }
      }
    },
    colors: ['<?= $color ?>', '#464F88'],//color completado/restante
    series: [{
      data: [
          ['AVANCE', <?= $proyecto[$i]["N_PORCENTAJE"]; ?>],
          ['POR COMPLETAR', <?= (100 - $proyecto[$i]["N_PORCENTAJE"]); ?>]
          ]}]
  },
  // using

  function(chart1) { // on complete
    var xpos = '50%';
    var ypos = '50%';
    var circleradius = 40;

    // Render the circle
    chart1.renderer.circle(xpos, ypos, circleradius).attr({
        fill: '#E9E9E9',//color circulo centro
    }).add();

    // Render the text
    chart1.renderer.text(chart1.series[0].data[0].percentage + '%', 58, 80).css({
        width: circleradius * 2,
        color: '#4572A7',
        fontSize: '16px',
        textAlign: 'center'
    }).attr({
        // why doesn't zIndex get the text in front of the chart?
        zIndex: 999
    }).add();
  });

});

<?php } ?>
<?php } ?>
/*--------------------------------------------------*/

/*------------------------------------------------*/
<?php if( $pro != "ALL" ) { ?>
<?php for ($i=0; $i <count($proyecto) ; $i++) {
  if ($bimestre_sel == 1) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }elseif ($bimestre_sel == 2 ) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('04/$fecha','MM/YYYY')) , 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }elseif ($bimestre_sel == 3) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('06/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }elseif ($bimestre_sel == 4) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('08/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }elseif ($bimestre_sel == 5) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('10/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }elseif ($bimestre_sel == 6) {
    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('12/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
    $hoy = $cons_fecha_fin[0]["F_FIN"];

    $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')) +1, 'dd/mm/yyyy') AS F_FIN from dual");
    $manana = $cons_fecha_fin[0]["F_FIN"];
  }

  $color = "#4CB5AE";
  /*COLOR PROYECTOS POR INICIAR (STATUS 1)*/
  if ($proyecto[$i]['IID_STATUS'] == 1){ $color = "#7E8C8D"; }
  /*COLOR PROYECTOS EN PROCESO (STATUS 2)*/
  if ($proyecto[$i]['IID_STATUS'] == 2){
    //DESFASADO
    if( strtotime($proyecto[$i]['D_FECHA_FIN']) < strtotime($hoy) ){ $color = "#D81B60"; }
    //RIESGO DE DESFASE
    if( strtotime($proyecto[$i]['D_FECHA_FIN']) == strtotime($hoy) || strtotime($proyecto[$i]['D_FECHA_FIN']) == strtotime($manana) ){ $color = "#DB8B0B"; }
  }
  /*COLOR PROYECTOS TERMINADOS (STATUS 3)*/
  if ($proyecto[$i]['IID_STATUS'] == 3){
    if( strtotime($proyecto[$i]['D_FECHA_FIN']) < strtotime($proyecto[$i]['D_FECHA_FIN_REAL']) ){ $color = "#D81B60"; }
  }
  /*COLOR PROYECTOS DESFASADOS (STATUS 4)*/
  if ($proyecto[$i]['IID_STATUS'] == 4){ $color = "#D81B60"; }
?>
$(function() {
  var chart1 = new Highcharts.Chart({
    chart: {
      renderTo: 'container1',
      type: 'pie'
    },
    exporting: {
      enabled:false
    },
    credits: {
      enabled: false
    },
    title: {
      text: '',
      y: 5,
      verticalAlign: 'bottom'
    },
    tooltip: {
      headerFormat: '<span style="font-size:12px">{point.key}: </span>',
      pointFormat: '<b>{point.y:,.0f}% </b>',
      useHTML: true
    },
    plotOptions: {
      pie: {
        borderColor: '#000000',
        innerSize: '60%',
        dataLabels: {
          enabled: false
        }
      }
    },
    colors: ['<?= $color ?>', '#3B4C66'],//color completado/restante
    series: [{
      data: [
          ['AVANCE', <?= $proyecto[$i]["N_PORCENTAJE"]; ?>],
          ['POR COMPLETAR', <?= (100 - $proyecto[$i]["N_PORCENTAJE"]); ?>]
          ]}]
  },

    function(chart1) { // on complete
      var xpos = '50%';
      var ypos = '50%';
      var circleradius = 40;

      // Render the circle
      chart1.renderer.circle(xpos, ypos, circleradius).attr({
          fill: '#ddd',
      }).add();

      // Render the text
      chart1.renderer.text(chart1.series[0].data[0].percentage + '%', 85, 110).css({
          width: circleradius * 2,
          color: '#4572A7',
          fontSize: '16px',
          textAlign: 'center'
      }).attr({
          // why doesn't zIndex get the text in front of the chart?
          zIndex: 999
      }).add();
  });

});
<?php } ?>
<?php } ?>
/*------------------------------------------------*/

/*========== GRAFICA PASTEL PROYECTOS DESFASADO/ENTIEMPO DEL BIMESTRE ==========*/
<?php if( $pro == "ALL" ) { ?>
Highcharts.chart('graficaPie', {

  credits: {
    enabled: false,
    text: 'argoalmacenadora.com',
    href: 'http://www.argoalmacenadora.com.mx'
  },
  chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
  },
  lang: {
    printChart: 'Imprimir Grafica',
    downloadPNG: 'Descargar PNG',
    downloadJPEG: 'Descargar JPEG',
    downloadPDF: 'Descargar PDF',
    downloadSVG: 'Descargar SVG',
    contextButtonTitle: 'Exportar grafica'
  },
  title: {
    <?php if ( $bimfil != "ALL" ){ ?>
      text: 'EVALUACIÓN BIMESTRAL <?=$fecha.$bimfil?>',
    <?php }else{
      /*if ($bimestre_sel == 1 ) {
        $bimestre_sel = 6;
        $fecha = $fecha -1;
      }
      else {
        $bimestre_sel = $bimestre_sel -1;
      }*/

      if ($bimestre_sel == 1) {
        $fecha_ini = '01/01/'.$fecha;
        $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
        $fecha_fin = $cons_fecha_fin[0]["F_FIN"];
        //echo $fecha_fin;
      }elseif ($bimestre_sel == 2 ) {
        $fecha_ini = '01/03/'.$fecha;
        $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('04/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
        $fecha_fin = $cons_fecha_fin[0]["F_FIN"];
      }elseif ($bimestre_sel == 3) {
        $fecha_ini = '01/05/'.$fecha;
        $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('06/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
        $fecha_fin = $cons_fecha_fin[0]["F_FIN"];
      }elseif ($bimestre_sel == 4) {
        $fecha_ini = '01/07/'.$fecha;
        $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('08/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
        $fecha_fin = $cons_fecha_fin[0]["F_FIN"];
      }elseif ($bimestre_sel == 5) {
        $fecha_ini = '01/09/'.$fecha;
        $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('10/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
        $fecha_fin = $cons_fecha_fin[0]["F_FIN"];
      }elseif ($bimestre_sel == 6) {
        $fecha_ini = '01/11/'.$fecha;
        $cons_fecha_fin = $instClass->sql(" SELECT TO_CHAR(LAST_DAY(to_date('12/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
        $fecha_fin = $cons_fecha_fin[0]["F_FIN"];
      }

      ?>
      text: 'EVALUACIÓN BIMESTRE  <?=$fecha_ini." AL ".$fecha_fin?>',
    <?php } ?>
    enabled: false,
  },
  tooltip: {
    headerFormat: '<span style="font-size:10px">{point.key}</span> <b>{point.percentage:.0f}%</b><table>',
    pointFormat: '<tr><td style="color:{series.color};padding:0">Total: </td>' +
        '<td style="padding:0"><b>{point.y:,.0f} </b></td></tr>',
    footerFormat: '</table>',
    useHTML: true
  },
  plotOptions: {
    pie: {
      allowPointSelect: false,
      cursor: 'pointer',
      dataLabels: {
          enabled: false
      },
      showInLegend: true
    }
  },

  colors: ['#4CB5AE', '#D81B60'],
  series: [{
    name: 'Total',
    point:{
      events:{
        click: function (event) {

        <?php if ( $bimfil != "ALL" ){ ?>
          url = '?bimfil=<?=$bimfil?>&fecha=<?=$fecha?>' ;
        <?php }else{ ?>
          url = '?bimfil=<?=$bimfil?>&fecha=<?=$bimestreFecha[0]["INICIO"]."-".$bimestreFecha[0]["FIN"]?>' ;
        <?php } ?>

        if ( this.x == 0 ){
          url = url+'&status=5'
        }
        if ( this.x == 1 ){
          url = url+'&status=4'
        }
          location.href = url;
        }
      }
    },
    colorByPoint: true,
    data: [ {
      name: 'Proyectos Terminados en Tiempo',
      y: <?= $countBim[0]["ENTIEMPO"] ?>,
      sliced: true,
      selected: true
    }, {
      name: 'Proyectos Desfasados',
      y: <?= $countBim[0]["DESFASADO"] ?>
    }
    ]
  }]

});
/*========== /.GRAFICA PASTEL PROYECTOS DESFASADO/ENTIEMPO DEL BIMESTRE ==========*/

<?php
  $anioBim = $instClass->sql("SELECT DISTINCT TO_CHAR(p.d_fecha_inicio, 'YYYY') anio FROM ss_proyecto p WHERE TO_CHAR(P.D_FECHA_INICIO, 'yyyy') IS NOT NULL  ORDER BY anio ASC ");
?>
Highcharts.chart('graficaAnio', {
  chart: {
    defaultSeriesType: 'column',
  },
  credits: {
    enabled: false,
    text: 'argoalmacenadora.com',
    href: 'http://www.argoalmacenadora.com.mx'
  },
  lang: {
    printChart: 'Imprimir Grafica',
    downloadPNG: 'Descargar PNG',
    downloadJPEG: 'Descargar JPEG',
    downloadPDF: 'Descargar PDF',
    downloadSVG: 'Descargar SVG',
    contextButtonTitle: 'Exportar grafica'
  },
  title: {
    text: 'EVALUACIÓN BIMESTRAL <?php for ($i=0; $i <count($anioBim) ; $i++) {  echo $anioBim[$i]["ANIO"].','; } ?> '
  },
  xAxis: {
    categories: [
    <?php
    for ($i=0; $i <count($anioBim) ; $i++) {
      echo ' "'.$anioBim[$i]["ANIO"].'", ';
    }
    ?>
    ],
  },
  yAxis: {
    min: 0,
    title:{text: 'Total de Proyectos'},
    stackLabels: {
      enabled: true,
      style: {
        fontWeight: 'bold',
        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
      }
    }
  },
  legend:{
    showInLegend: false,
    align: 'right',
    x: -30,
    verticalAlign: 'top',
    y: 25,
    floating: true,
    backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
    borderColor: '#CCC',
    borderWidth: 1,
    shadow: false
  },

  tooltip:{
    //var stackName = this.series.userOptions.stack;
    headerFormat: '<b>{point.x} -- {series.userOptions.stack}</b><br/>',
    pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
  },
  plotOptions:{
    column:{
      stacking: 'normal',
      showInLegend: false,/**/
      cursor: 'pointer',
      dataLabels: {
        enabled: true,
        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
      },
      point:{
        events: {
          click: function (event) {
            /* CONSULTA DE BIMESTRE AJAX */
            varBimestre = this.series.userOptions.stack.substr(9) ;
            $.ajax({
              type: 'post',
              url: '../action/ticAjax.php',
              data: {option: 1, anio: this.category, bimestre : this.series.userOptions.stack.substr(9) },
              beforeSend: function () {
                //$("#resRegEmpresaAjax").html("Procesando, espere por favor...");
              },
              success: function (response) {
                var dataJson = JSON.parse(response);
                $.each(dataJson, function(i, val){
                  url = '?bimfil='+varBimestre+'&fecha='+val.INICIO+'-'+val.FIN ;
                  location.href = url;
                });
              }
            });
            /* /.CONSULTA DE BIMESTRE AJAX */
            //alert('Año:' + this.category +'\n'+ this.series.userOptions.stack.substr(9) );
          }
        }
      }
    },
  },

  colors: ['#4CB5AE', '#D81B60'],
  series:[

  <?php for ($a=0; $a <6 ; $a++) {
    $mes1="";$mes2="";
    if( $a == 0 ){ $mes1="01";$mes2="02"; }
    if( $a == 1 ){ $mes1="03";$mes2="04"; }
    if( $a == 2 ){ $mes1="05";$mes2="06"; }
    if( $a == 3 ){ $mes1="07";$mes2="08"; }
    if( $a == 4 ){ $mes1="09";$mes2="10"; }
    if( $a == 5 ){ $mes1="11";$mes2="12"; }
  ?>
    // { name: 'EnTiempo', data: [3, 3,], stack: 'Bimestre#<?=$a+1?>' },
    // { name: 'Desfasado', data: [4, 3,], stack: 'Bimestre#<?=$a+1?>' },

    { name: 'EnTiempo',  data: [
     <?php
     for ($i=0; $i <count($anioBim) ; $i++) {
      $tiempo1 = $instClass->sql(" SELECT COUNT(*) res FROM ss_proyecto pro WHERE pro.D_FECHA_FIN_REAL <= pro.d_fecha_fin AND TO_CHAR(pro.d_fecha_inicio, 'MM') IN ('".$mes1."','".$mes2."') AND TO_CHAR(pro.d_fecha_inicio, 'YYYY') = ".$anioBim[$i]["ANIO"]."
                                  AND pro.d_fecha_inicio <= trunc(to_date('".$bimestreFecha[0]["FIN"]."','dd-mm-yyyy') ) +1 ");
      //echo  "SELECT COUNT(*) res FROM ss_proyecto pro WHERE pro.D_FECHA_FIN_REAL <= pro.d_fecha_fin AND TO_CHAR(pro.d_fecha_inicio, 'MM') IN ('".$mes1."','".$mes2."') AND TO_CHAR(pro.d_fecha_inicio, 'YYYY') = ".$anioBim[$i]["ANIO"]." AND pro.d_fecha_inicio <= trunc(to_date('".$bimestreFecha[0]["FIN"]."','dd-mm-yyyy') ) +1 " ;
        for ($j=0; $j <count($tiempo1) ; $j++) {
          echo  $tiempo1[$j]["RES"].",";
        }
      }
     ?>
     ], stack: 'BIMESTRE <?=$a+1?>' },

     { name: 'Desfasado',  data: [
     <?php
     for ($i=0; $i <count($anioBim) ; $i++) {
      $tiempo1 = $instClass->sql(" SELECT COUNT(*) res FROM ss_proyecto pro WHERE pro.D_FECHA_FIN_REAL > pro.d_fecha_fin AND TO_CHAR(pro.d_fecha_inicio, 'MM') IN ('".$mes1."','".$mes2."') AND TO_CHAR(pro.d_fecha_inicio, 'YYYY') = ".$anioBim[$i]["ANIO"]." AND pro.d_fecha_inicio <= trunc(to_date('".$bimestreFecha[0]["FIN"]."','dd-mm-yyyy') ) +1 ");
        for ($j=0; $j <count($tiempo1) ; $j++) {
          echo  $tiempo1[$j]["RES"].",";
        }
      }
     ?>
     ], stack: 'BIMESTRE <?=$a+1?>' },
  <?php } ?>
  ],

});
<?php } ?>
</script>
<!-- Sparkline -->
<script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="../plugins/knob/jquery.knob.js"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- PACE -->
<script src="../plugins/pace/pace.min.js"></script>
<!-- #################################### Termina Libreria de Script ######################################-->
<!-- jQuery Knob -->
<script src="../plugins/knob/jquery.knob.js"></script>
<!-- Sparkline -->
<script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
<?php conexion::cerrar($conn); ?>
