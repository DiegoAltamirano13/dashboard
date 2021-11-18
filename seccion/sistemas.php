<?php
ini_set('display_errors', false);

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

  /////////////////////////////
require_once("../class/Perfil.php");
$instaciaPer = new Perfil();
///////////////////////////////////////////
require_once("../class/Sistemas.php");
$instanciaSis = new Sistemas();
//Proyectos Activos 
$pro_acti=$instanciaSis->pro_acti_leer();
//Proyectos en procesos 
$pro_proc=$instanciaSis->pro_proc_leer();
//Proyectos por iniciar
$pro_inic=$instanciaSis->pro_inic_leer();
//Proyectos Desfasados
$pro_desf=$instanciaSis->pro_desf_leer();
// ################################################# Graficas de Proyectos #############################
 
 //////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');

 //////////////////////////// VALIDACION DEL MODULO ASIGNADO
$iid_empleado = $_SESSION['iid_empleado'];
$modulos_valida = $instaciaPer->modulos_valida($iid_empleado, '1');
if($modulos_valida == 0){
  header('Location: index.php');
}
///////////////////////////////////////////
//////////////////////////// VALIDACION OPCIONES 
    
    $modulos_valida_op = $instaciaPer->modulos_valida_op($iid_empleado, '1');
     
///////////////////////////////   
if(isset($_POST['iid_proyecto'])) 
$_SESSION['id_proyecto'] = $_POST['iid_proyecto'];;
$id_proyecto = $_SESSION['id_proyecto'];
 
 
///////////////////////////////  
if(isset($_POST['iid_tarea'])) 
$_SESSION['id_tarea'] = $_POST['iid_tarea'];;
$id_tarea = $_SESSION['id_tarea'];


///////////////////////////////   
if(isset($_POST['iid_actividad'])) 
$_SESSION['id_actividad'] = $_POST['iid_actividad'];;
$id_actividad = $_SESSION['id_actividad'];
/////////////////////// GUARDA EL VALOR DE STATUS DE PROYECTO
if ($_SESSION['status_proyecto'] == false){ 
  $_SESSION['status_proyecto'] = '2,4';
  $status_proyecto = $_SESSION['status_proyecto'];
}else{
  if(isset($_POST['status_proyecto']))
  $_SESSION['status_proyecto'] = $_POST['status_proyecto'];
  $status_proyecto = $_SESSION['status_proyecto'];
} 

//CODE PARA ASIGNAR TITULO DEPENDIENDO DEL STATUS
switch ($status_proyecto) {
  case '1':
    $titulo_status = "Proyectos por iniciar";
    break;
  case '1,2,4':
    $titulo_status = "Proyectos Activos";
    break;
  case '2,4':
    $titulo_status = "Proyectos en Procesos y Desfasados";
    break;
  case '3':
    $titulo_status = "Proyectos Terminados";
    break;
  case '4':
    $titulo_status = "Proyectos Desfasados";
    break;
}

?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) --> 
    <section class="content-header">
      <h1>

        Dashboard 
        <small>Sistemas</small> 
      </h1> 
    </section> 
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal --> 

<!-- ######################################## Inicio de Widgets ######################################### -->
<?php if ($_SESSION['valor_perfil'] == 1){ ?>
<section><!-- Inicia la seccion de los Widgets -->
 <div class="row">

  <div class="col-lg-5"><!-- col-lg-5 -->
    <div class="row"><!-- row -->
    <!-- INICIA WIDGETS RESERVA DE CONTINGENCIA -->
    <div class="col-lg-6">
      <div class="small-box bg-aqua">
        <div class="inner text-center">
          <b>Proyectos Activos</b>
          <h3 class="text-center">
          <?php
            for($i=0;$i<sizeof($pro_acti);$i++)
              {
                echo $pro_acti[$i]["TOTAL_PRO_ACTI"] ;
              }
          ?>
          </h3>
        </div>
        <div class="icon">
          <i class="ion ion-clipboard"></i>
        </div>
        <!-- <div class="inner"></div> -->
        <form action='sistemas.php' method="post"><input type="hidden" name="iid_proyecto" value=""><input type='hidden' value='' name='iid_tarea'><input type='hidden' value='' name='iid_actividad'><button type="submit" name="status_proyecto" value="1,2,4" class="btn bg-aqua-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button></form>
      </div>
    </div>
    <!-- TERMINA WIDGETS RESERVA DE CONTINGENCIA -->
    <!-- INICIA WIDGETS INVERSIONES -->
    <div class="col-lg-6">
      <div class="small-box bg-green">
        <div class="inner text-center"> 
          <b>Proyectos en Proceso</b> 
          <h3 class="text-center">
          <?php
            for($i=0;$i<sizeof($pro_proc);$i++)
              {
                echo $pro_proc[$i]["TOTAL_PRO_PROC"] ;
              }
          ?>
          </h3>
        </div>
        <div class="icon">
          <i class="ion ion-loop"></i>
        </div>
        <!-- <div class="inner"></div> -->
        <form action='sistemas.php' method="post"><input type="hidden" name="iid_proyecto" value=""><input type='hidden' value='' name='iid_tarea'><input type='hidden' value='' name='iid_actividad'><button type="submit" name="status_proyecto" value="2,4" class="btn bg-green-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button></form>
      </div>
    </div>
    <!-- TERMINA WIDGETS INVERSIONES -->
    </div><!-- ./row --> 
  </div><!-- ./col-lg-5 -->
  <div class="col-lg-2"><!-- col-lg-2 -->
    <div class="row"><!-- row -->
    <!-- INICIA WIDGETS BANCOS -->
      <div class="col-lg-12">
        <div class="small-box bg-yellow">
          <div class="inner text-center">
            <b>Proyectos por iniciar</b>
            <h3 class="text-center">
            <?php
            for($i=0;$i<sizeof($pro_inic);$i++)
              {
                echo $pro_inic[$i]["TOTAL_PRO_INIC"] ;
              }
            ?>
            </h3>
          </div>
          <div class="icon">
            <i class="ion ion-gear-b"></i>
          </div>
          <!-- <div class="inner"></div> -->
          <form action='sistemas.php' method="post"><input type="hidden" name="iid_proyecto" value=""><input type='hidden' value='' name='iid_tarea'><input type='hidden' value='' name='iid_actividad'><button type="submit" name="status_proyecto" value="1" class="btn bg-yellow-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button></form>
        </div>
      </div>
    <!-- TERMINA WIDGETS BANCOS -->
    </div> <!-- ./row -->  
  </div><!-- ./col-lg-2 -->
    <div class="col-lg-5"><!-- col-lg-5 -->
      <div class="row"><!-- row -->
      <!-- INICIA WIDGETS INGRESOS POR COBRANZA -->
        <div class="col-lg-6">
          <div class="small-box bg-red">
            <div class="inner text-center">
              <b>Proyectos Desfasados</b>
              <h3 class="text-center">
              <?php
              for($i=0;$i<sizeof($pro_desf);$i++)
                {
                  echo $pro_desf[$i]["TOTAL_PRO_DESF"] ;
                }
              ?>
              </h3>
            </div>
            <div class="icon">
              <i class="ion ion-alert-circled"></i>
            </div>
            <!-- <div class="inner"></div> -->
              <form action='sistemas.php' method="post"><input type="hidden" name="iid_proyecto" value=""><input type='hidden' value='' name='iid_tarea'><input type='hidden' value='' name='iid_actividad'><button type="submit" name="status_proyecto" value="4" class="btn bg-red-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button></form>
          </div>
        </div>
      <!-- TERMINA WIDGETS INGRESOS POR COBRANZA -->
      <!-- TERMINA WIDGETS EGRESOS POR GASTOS -->
        <div class="col-lg-6">
          <div class="small-box bg-blue">
            <div class="inner text-center">
              <b>Proyectos Terminados</b>
              <h3 class="text-center">
              <?php
              $pro_ter = $instanciaSis->pro_ter_leer();
              for($i=0;$i<sizeof($pro_ter);$i++)
                {
                  echo $pro_ter[$i]["TOTAL_PRO_TER"] ;
                }
              ?>
              </h3>
            </div>
            <div class="icon">
              <i class="fa fa-check-square"></i>
            </div>
            <!-- <div class="inner"></div> -->
              <form action='sistemas.php' method="post"><input type="hidden" name="iid_proyecto" value=""><input type='hidden' value='' name='iid_tarea'><input type='hidden' value='' name='iid_actividad'><button type="submit" name="status_proyecto" value="3" class="btn bg-light-blue-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button></form>
         </div>
        </div>
      <!-- TERMINA WIDGETS EGRESOS POR GASTOS -->
      </div><!-- ./row -->  
    </div><!-- ./col-lg-5 -->
    
 </div>
</section><!-- Termina la seccion de los Widgets -->

<?php } ?>
<!-- ######################################### Termino de Widgets ######################################### --> 



<!-- ################################# Inicia Seccion de grafica proyecto,informacion y tabla tareas ################################## --> 
<section><!-- Inicia la seccion de Grafica Principal --> 
<!-- Main row -->
      <div class="row">


        <!-- Left col -->
        <div class="col-md-6 connectedSortable"><!-- Inicia seccion izquierda  -->
<!-- ###################################### Inicia Graficas Principales ####################### --> 
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><?=$titulo_status?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button> 
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-header with-border"><!-- Inicia seccion del titulo y botones minimizar y cerrar -->
              <h6 class="box-title">Indicador de color</h6>
                <div class="timeline-footer">
                  <a class="btn btn-info btn-flat btn-xs">Proyectos a Tiempo</a>
                  <a class="btn btn-warning btn-flat btn-xs">Proyectos en riesgo de Desfase</a>
                  <a class="btn btn-danger btn-flat btn-xs">Proyectos Desfasados</a>
                </div>
            </div><!-- Termina seccion del titulo y botones minimizar y cerrar -->
            <div class="box-body"> 
              <div class="row"><!-- Inicia row para contener graficas -->
                
                <?php 
// $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ INICA SECCION PARA LAS GRAFICAS $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
    

                  $graf_proyecto_admin=$instanciaSis->graf_pro_leer($_SESSION['valor_perfil'],$iid_empleado,$status_proyecto);
                  for($i=0;$i<sizeof($graf_proyecto_admin);$i++)
                  {//Abre for para traer los registros de la grafica 
                  
 
// ############################## INICIA CONDICION PARA CAMBIAR EL COLOR DE LA GRAFICA ############################
                  echo "<div class='col-xs-6 col-md-3 text-center'>"; //Inicia div para justificar graficas

                  $var_fecha_hoy = date('d-m-Y') ; //Sacamos la fecha de hoy
                  $var_fecha_manana = date('d-m-Y', strtotime('+1 day')) ; //Sacamos la fecha de mañana
                  $fecha_hoy = date_create($var_fecha_hoy); //convertimos en formato de fecha la variable $var_fecha_hoy
                  $fecha_manana = date_create($var_fecha_manana); //convertimos en formato de fecha la variable $var_fecha_manana

                  if ($graf_proyecto_admin[$i]['STATUS_PRO'] == 3){//Inicia evaluacion de color para los proyectos terminados
                    if ( strtotime($graf_proyecto_admin[$i]['FEC_FIN_PRO']) >= strtotime($graf_proyecto_admin[$i]['FEC_FIN_PRO_REAL']) )
                    {
                      echo  "<input type='text' readonly='true' class='knob' value='".$graf_proyecto_admin[$i]['PORCENTAJE_PRO']."' data-width='100' data-height='100'   data-skin='tron'  data-thickness='.2' data-fgColor='#00c0ef'>";  
                    }else{
                      echo  "<input type='text' readonly='true' class='knob' value='".$graf_proyecto_admin[$i]['PORCENTAJE_PRO']."' data-width='100' data-height='100'   data-skin='tron'  data-thickness='.2' data-fgColor='#f56954'>"; 
                    }
                  }//Termina evaluacion de color para los proyectos terminados
                  else { // Inicia evaluacion de color para los proyectos pendientes, en proceso y desfasados

                  //########### Inicia evaluacion para proyectos desfasados graficas en rojo###########
                         if(date_create($graf_proyecto_admin[$i]['FEC_FIN_PRO']) < $fecha_hoy || $graf_proyecto_admin[$i]['STATUS_PRO'] == 4)//si d_fecha_fin es menor a la fecha de hoy color rojo
                         {
                          if ($graf_proyecto_admin[$i]['STATUS_PRO'] <> 1) {
                            echo  "<input type='text' readonly='true' class='knob' value='".$graf_proyecto_admin[$i]['PORCENTAJE_PRO']."' data-width='100' data-height='100'   data-skin='tron'  data-thickness='.2' data-fgColor='#f56954'>";
                            }else{
                              echo  "<input type='text' readonly='true' class='knob' value='".$graf_proyecto_admin[$i]['PORCENTAJE_PRO']."' data-width='100' data-height='100'   data-skin='tron'  data-thickness='.2' data-fgColor='#00c0ef'>"; 
                            }
                         }
                  //########### Termina evaluacion para proyectos desfasados graficas en rojo###########
                  //########### Inicia evaluacion para proyectos en riesgo de desface graficas en amarillo###########
                         else if(date_create($graf_proyecto_admin[$i]['FEC_FIN_PRO']) == $fecha_hoy || date_create($graf_proyecto_admin[$i]['FEC_FIN_PRO']) == $fecha_manana )
                         {
                            if ($graf_proyecto_admin[$i]['STATUS_PRO'] <> 1){
                            echo  "<input type='text' readonly='true' class='knob' value='".$graf_proyecto_admin[$i]['PORCENTAJE_PRO']."' data-width='100' data-height='100'   data-skin='tron'  data-thickness='.2' data-fgColor='#f39c12'>";
                            }else{
                              echo  "<input type='text' readonly='true' class='knob' value='".$graf_proyecto_admin[$i]['PORCENTAJE_PRO']."' data-width='100' data-height='100'   data-skin='tron'  data-thickness='.2' data-fgColor='#00c0ef'>"; 
                            }
                         }
                  //########### Termina evaluacion para proyectos en riesgo de desface graficas en amarillo###########
                         else{
                          echo  "<input type='text' readonly='true' class='knob' value='".$graf_proyecto_admin[$i]['PORCENTAJE_PRO']."' data-width='100' data-height='100'   data-skin='tron'  data-thickness='.2' data-fgColor='#00c0ef'>";  
                         }

                  }// Termina evaluacion de color para los proyectos pendientes, en proceso y desfasados
                   
            
                 

// ################################ TERMINA CONDICION PARA CAMBIAR EL COLOR DE LA GRAFICA ############################
                  echo "<form action='sistemas.php' method='post'><div class='knob-label'><div class='radio' title='".$graf_proyecto_admin[$i]['NOMBRE_PRO']."'><label class='radio-inline'><input type='hidden' value='' name='iid_tarea'><input type='hidden' value='' name='iid_actividad'><input style='width:0px;height:0px' onchange='this.form.submit()' type='radio'  name='iid_proyecto' value='".$graf_proyecto_admin[$i]['ID_PROYECTO']."' ><MARQUEE BGCOLOR='#F5F5F5' SCROLLDELAY =180>".$graf_proyecto_admin[$i]['NOMBRE_PRO']."</MARQUEE></label></div></div></form>";
                  //echo "<form action='sistemas.php' method='post'><div class='knob-label'><div class='radio' title='".$graf_proyecto_admin[$i]['NOMBRE_PRO']."'><label class='radio-inline'><input type='hidden' value='' name='iid_tarea'><input type='hidden' value='' name='iid_actividad'><input style='width:0px;height:0px' onchange='this.form.submit()' type='radio'  name='iid_proyecto' value='".$graf_proyecto_admin[$i]['ID_PROYECTO']."' >".substr($graf_proyecto_admin[$i]['NOMBRE_PRO'],0,10)."</label></div></div></form>";
                   
                  echo "</div>"; //Termina div para justificar graficas
                  }//Cierra for para traer los registros de la grafica 
      

// $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ TERMINA SECCION PARA LAS GRAFICAS $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  
                ?>
                <!-- ./col -->
              </div><!-- Termina row para contener graficas -->
            </div>
            <!-- /.box-body --> 
          </div>
          <!-- /.box -->
<!-- ###################################### Termina Graficas Principales ####################### -->
        </div><!-- Termina seccion izquierda  -->
        <!-- /.col -->

        <!-- Right col -->
        <div class="col-md-6 connectedSortable"><!-- Inicia seccion derecha -->  

<!-- ############################# INICIA CODE DESCRICION DEL PROYECTO ##################################### --> 
          <div class="box box-solid box-primary">
            <div class="box-header with-border">
                <?php
                  $desc_pro = $instanciaSis->desc_pro_leer($id_proyecto);
                    if($desc_pro)
                    
                      { //Abre if para traer registros de la descripcion del proyecto
                        $nombre_proyecto = $desc_pro["DESC_PRO_NOMBRE"]; //GUARDAMOS EL NOMBRE DEL PROYECTO PARA MOSTRARLO EN LA TABLA DE ACTIVIDADES
                ?> 
              
                <h5 class=" ">INFORMACIÓN DEL PROYECTO <b><?= $desc_pro["DESC_PRO_NOMBRE"] ?> </b></h5>
              

              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div><!-- Termina cabecera para la descripcion del proyecto -->
            <div class="box-body no-padding"><!-- Inicia el box-body para traer la descripcion del proyecto -->
              <div class="panel-group" id="descripcion_proyecto"><!-- abre panel-group para el acordion de descripcion -->
                <div class="panel panel-default"><!-- Inicia acordion de descripcion -->
                  <div class="panel-heading"><!-- inicia panel-heading de la descripcion -->
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#descripcion_proyecto" href="#descripcion">Descripción</a>
                    </h4>
                  </div><!-- termina panel-heading de la descripcion -->
                  <div id="descripcion" class="panel-collapse collapse in"><!-- Inicia id="descripcion"  -->
                    <div class="panel-body"><!-- Inicia el body del acordion para la descripcion -->
                    <?= $desc_pro["DESC_PRO_DES"] ?>
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
                    <?= $desc_pro["DESC_PRO_ALCANCE"] ?>
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
                    <?= $desc_pro["DESC_PRO_JUST"] ?>
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
                    <?= $desc_pro["DESC_PRO_PER_NOM"]." ".$desc_pro["DESC_PRO_PER_PAT"]." ".$desc_pro["DESC_PRO_PER_MAT"]?>
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
                    <?= $desc_pro["DESC_PRO_PER_NOM_LIDER"]." ".$desc_pro["DESC_PRO_PER_PAT_LIDER"]." ".$desc_pro["DESC_PRO_PER_MAT_LIDER"]?>
                    </div><!-- Termina el body del acordion para el Solicitante -->
                  </div><!-- Termina id="lider_pro"  -->
                </div><!-- Termina acordion de Solicitante -->
              </div> <!-- cierra panel-group para el acordion de descripcion -->
              <?php 
                      } //Cierra if para traer registros de la descripcion del proyecto
                  else {//abre else para mostrar alerta si aun no hay una consulta get en descripcion del Proyecto
                    echo "<h4><i class='icon fa fa-warning'></i> Seleccione un Proyecto para mostrar la descripción!</h4>";
                  }//cierra else para mostrar alerta si aun no hay una consulta get en descripcion del Proyecto
              ?>
            </div>
            <!-- /.box-body --> 
          </div>
          <!-- /.box -->
<!-- ############################# TERMINA CODE DESCRICION DEL PROYECTO ##################################### -->         

<!-- ############################# INICIA CODE TABLA TAREAS ##################################### -->
          <div class="box box-primary connectedSortable">
            <div class="box-header with-border">
              <h3 class="box-title"><!-- Inicia Titutlo de la Tabla -->
          <!-- ##### INICIA TITULO DE LA TABLA TAREAS #### -->
                <?php 
                if($desc_pro)//trae titulo del proyecto
                  {//abre if para el titulo de la tabla de tareas

                     echo "<p> TAREAS DEL PROYECTO <b>" .$desc_pro["DESC_PRO_NOMBRE"]."</b></p>" ;
                  }//cierra if  para el titulo de la tabla de tareas
                else { //abre Else para mostrar la alerta si no hay ninguna tarea del proyecto
                    echo "<div class='alert alert-warning alert-dismissible'>".
                         "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>".
                         "<h4><i class='icon fa fa-warning'></i> Tabla Tareas!</h4>".
                         "Aún no ha seleccionado un Proyecto para mostrar las Tareas.".
                         "</div>";
                  }//Cierra Else para mostrar la alerta si no hay ninguna tarea del proyecto
                 ?>
            <!-- ##### TERMINA TITULO DE LA TABLA TAREAS #### -->
              </h3><!-- Termina Titutlo de la Tabla -->

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button> 
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body"><!-- Inicia box-body de la tabla de tareas -->
              <div class="table-responsive">
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
    <!-- ############## Inicia TR para traer los registros de la tabla tareas ################### -->
          <?php      
          $tareas_pro = $instanciaSis->tabla_tareas_leer($id_proyecto);
           for($i=0;$i<sizeof($tareas_pro);$i++)
            {  
          ?>
                <tr> 
                  <td class='small'><?= $tareas_pro[$i]["TAREA_ID"] ?></td> 
                  <td class="small"><?= $tareas_pro[$i]["TAREA_NOMBRE"] ?></td>
                  <td class='small'>
                  <?php 
                  echo "<i class='fa fa-fw fa-calendar' data-toggle='tooltip' title='Inicio: ".$tareas_pro[$i]["TAREA_FEC_INI"]."'></i><i class='fa fa-fw fa-calendar' data-toggle='tooltip' title='Inicio Real: ".$tareas_pro[$i]["TAREA_FECHA_INI_REAL"]."'></i>"; 
                  ?>
                  </td>
                  <td class='small'>
                  <?php 
                  echo "<i class='fa fa-fw fa-calendar-check-o' data-toggle='tooltip' title='Final: ".$tareas_pro[$i]["TAREA_FECHA_FIN"]."'></i><i class='fa fa-fw fa-calendar-check-o' data-toggle='tooltip' title='Final Real: ".$tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"]."'></i>"; 
                  ?>
                  </td>
<!-- ####### inicia condicion para cambiar el color del progreso y porcentaje de la tabla de tareas ############-->
                  <?php
                  $var_fecha_hoy = date('d-m-Y') ; //Sacamos la fecha de hoy
                  $fecha_hoy = date_create($var_fecha_hoy); 
  // ############ Inicia condiciones si tenemos datos en d_fecha_inicio,d_fecha_ini_real,d_fecha_fin,d_fecha_real ##############
                  if ( $tareas_pro[$i]["TAREA_FEC_INI"]==true && $tareas_pro[$i]["TAREA_FECHA_INI_REAL"] ==true && $tareas_pro[$i]["TAREA_FECHA_FIN"]==true && $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"]==true )//evaluamos si tenemos datos en d_fecha_inicio,d_fecha_ini_real,d_fecha_fin,d_fecha_real
                  {//abre if para ver si tenemos datos en d_fecha_inicio,d_fecha_ini_real,d_fecha_fin,d_fecha_real

                    if ( strtotime($tareas_pro[$i]["TAREA_FEC_INI"]) < strtotime($tareas_pro[$i]["TAREA_FECHA_INI_REAL"]) && strtotime($tareas_pro[$i]["TAREA_FECHA_FIN"] ) < strtotime($tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"]) && $tareas_pro[$i]["TAREA_TIPO"] <> 'DESFASADO')//Tareas que se iniciaron desfasadas pero se terminó en el tiempo estipulado aunque pasó la fecha de entrega – barra en rojo e indicador de porcentaje en amarillo
                    {//abre if para inicio-desafasado termino-desafasado
                      echo "<td class='small'>".
                           "<div class='progress progress-xs'>".
                           "<div class='progress-bar progress-bar-danger ' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                           "</div>".
                           "</td>";
                      echo "<td class='small'><span class='badge btn-warning'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                    }//cierra if para inicio-desafasado termino-desafasado
                    else if(strtotime($tareas_pro[$i]["TAREA_FEC_INI"]) < strtotime($tareas_pro[$i]["TAREA_FECHA_INI_REAL"]) && $tareas_pro[$i]["TAREA_FECHA_FIN"] == $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] && $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] <> 'DESFASADO' )//Tareas que se iniciaron desfasadas pero se terminó  y entregó en la fecha de indicada – barra en rojo e indicador de porcentaje en azul
                    {//abre else if para inicio-desafasado termino-bien 
                      echo "<td class='small'>".
                           "<div class='progress progress-xs'>".
                           "<div class='progress-bar progress-bar-danger ' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                           "</div>".
                           "</td>";
                      echo "<td class='small'><span class='badge btn-info'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                    }//cierra else if para inicio-desafasado termino-bien 
                    else if(strtotime($tareas_pro[$i]["TAREA_FEC_INI"]) >= strtotime($tareas_pro[$i]["TAREA_FECHA_INI_REAL"]) && strtotime($tareas_pro[$i]["TAREA_FECHA_FIN"]) < strtotime($tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"]) && $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] <> 'DESFASADO')//Tareas que se iniciaron bien pero se terminó  y entregó en la fecha de desface – barra en azul e indicador de porcentaje en amarillo
                    {//abre else if para inicio-bien termino-desafasado 
                      echo "<td class='small'>".
                           "<div class='progress progress-xs'>".
                           "<div class='progress-bar progress-bar-info ' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                           "</div>".
                           "</td>";
                      echo "<td class='small'><span class='badge btn-warning'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                    }//cierra else if para inicio-bien termino-desafasado 
                    else if($tareas_pro[$i]["TAREA_FEC_INI"] == $tareas_pro[$i]["TAREA_FECHA_INI_REAL"] && $tareas_pro[$i]["TAREA_FECHA_FIN"] == $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] && $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] <> 'DESFASADO' || $tareas_pro[$i]["TAREA_FEC_INI"] == $tareas_pro[$i]["TAREA_FECHA_INI_REAL"] && $tareas_pro[$i]["TAREA_FECHA_FIN"] > $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"]|| $tareas_pro[$i]["TAREA_FEC_INI"] > $tareas_pro[$i]["TAREA_FECHA_INI_REAL"] && isset($tareas_pro[$i]["TAREA_FECHA_INI_REAL"]) && strtotime($tareas_pro[$i]["TAREA_FECHA_FIN"]) > strtotime($tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"]) && isset($tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"]) && $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] <> 'DESFASADO' || strtotime($tareas_pro[$i]["TAREA_FEC_INI"]) > strtotime($tareas_pro[$i]["TAREA_FECHA_INI_REAL"]) && $tareas_pro[$i]["TAREA_FECHA_FIN"] == $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"])//Tareas que se iniciaron en tiempo y se terminó en tiempo y forma/Tareas que se iniciaron en tiempo y se terminó antes de la fecha indicada/Tareas que se iniciaron antes de tiempo y se terminó antes de la fecha indicada– barra en azul e indicador de porcentaje en azul
                    {//abre else if para inicio-bien termino-desafasado 
                      echo "<td class='small'>".
                           "<div class='progress progress-xs'>".
                           "<div class='progress-bar progress-bar-info ' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                           "</div>".
                           "</td>";
                      echo "<td class='small'><span class='badge btn-info'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                    }//cierra else if para inicio-bien termino-desafasado
                    else//evalua si el estatus esta en desface para pintar el color en rojo 
                    {//abre else para evaluar status desfasado
                      echo "<td class='small'>".
                           "<div class='progress progress-xs'>".
                           "<div class='progress-bar progress-bar-danger ' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                           "</div>".
                           "</td>";
                      echo "<td class='small'><span class='badge btn-danger'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                    }//cierra else para evaluar status desfasado
                    
                  }//cierra if para ver si tenemos datos en d_fecha_inicio,d_fecha_ini_real,d_fecha_fin,d_fecha_real
// ############ termina condiciones si tenemos datos en d_fecha_inicio,d_fecha_ini_real,d_fecha_fin,d_fecha_real ##############

// ################################ Inicia condiciones si tenemos datos en d_fecha_ini_real ###################################
                  else if($tareas_pro[$i]["TAREA_FECHA_INI_REAL"] == true && $tareas_pro[$i]["TAREA_TIPO"] <> 'PENDIENTE')//evaluamos si hay datos en d_fecha_ini_real
                    {//abre else if para ver hay datos en d_fecha_ini_real
                      $var_fecha_hoy = date('d-m-Y') ; //Sacamos la fecha de hoy
                      $fecha_hoy = date_create($var_fecha_hoy); 
                      if(strtotime($tareas_pro[$i]["TAREA_FEC_INI"]) < strtotime($tareas_pro[$i]["TAREA_FECHA_INI_REAL"])&& date_create($tareas_pro[$i]["TAREA_FECHA_FIN"]) < $fecha_hoy || $tareas_pro[$i]["TAREA_FEC_INI"] >= $tareas_pro[$i]["TAREA_FECHA_INI_REAL"] && date_create($tareas_pro[$i]["TAREA_FECHA_FIN"]) < $fecha_hoy  )//Tareas que se iniciaron desfasadas pero se terminó en el tiempo estipulado aunque pasó la fecha de entrega//Tareas que se iniciaron bien pero paso la fecha de entrega - todo en rojo
                      {
                        echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progress-bar progress-bar-danger' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                        echo "<td class='small'><span class='badge btn-danger'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                      }
                      else if( date_create($tareas_pro[$i]["TAREA_FECHA_FIN"]) == $fecha_hoy  )//Tareas apunto de terminar barra en amarillo 
                      {
                        echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progress-bar progress-bar-warning' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                        echo "<td class='small'><span class='badge btn-warning'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                      }
                      else if ($tareas_pro[$i]["TAREA_FEC_INI"] < $tareas_pro[$i]["TAREA_FECHA_INI_REAL"] && date_create($tareas_pro[$i]["TAREA_FECHA_FIN"]) >= $fecha_hoy && $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] <> 'DESFASADO'  )//Tareas que iniciaron desfasadas pero se entregara en tiempo estupulado barra en rojo y porcentaje naranja
                        { 
                            echo "<td class='small'>".
                             "<div class='progress progress-xs'>".
                             "<div class='progress-bar progress-bar-danger' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                             "</div>".
                             "</td>";
                            echo "<td class='small'><span class='badge btn-info'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                        }
                      else if ($tareas_pro[$i]["TAREA_FEC_INI"] >= $tareas_pro[$i]["TAREA_FECHA_INI_REAL"] && date_create($tareas_pro[$i]["TAREA_FECHA_FIN"]) > $fecha_hoy && $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] <> 'DESFASADO'  )//tareas que iniciaron bien o antes del la fecha inicial y se entregaran a tiempo- todo de azul
                        {
                        echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progress-bar progress-bar-info' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                        echo "<td class='small'><span class='badge btn-info'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                        }
                      else
                      { 
                        echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progress-bar progress-bar-danger' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                        echo "<td class='small'><span class='badge btn-danger'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>"; 
                      }
                    }//abre else if para ver hay datos en d_fecha_ini_real
// ################################ termina condiciones si tenemos datos en d_fecha_ini_real ###################################

// ################################ inicia condiciones si no tenemos datos en d_fecha_ini_real ###################################
                    else if ($tareas_pro[$i]["TAREA_FECHA_INI_REAL"] == false)//evaluamos si d_fecha_ini_real no tiene datos
                    {
                        if( $tareas_pro[$i]["TAREA_FECHA_FIN"] >= $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] && isset($tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"]) && $tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] <> 'DESFASADO'  )//evaluamos si d_fecha_fin es igual o menor a d_fecha_real mientras que no este basio d_fecha_real para pintar todo en azul 
                       {
                        echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progress-bar progress-bar-info' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                        echo "<td class='small'><span class='badge btn-info'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                       }

                       else if(date_create($tareas_pro[$i]["TAREA_FECHA_FIN"]) < $fecha_hoy)//evaluamos si d_fecha_fin es menor a la fecha de hoy para pinta todo de color rojo
                       {

                        if($tareas_pro[$i]["TAREA_D_FECHA_FIN_REAL"] == 'PENDIENTE'/*$tareas_pro[$i]["TAREA_TIPO"] == 'PENDIENTE'*/)//aqui code mal aproposito =D ¿?
                        {  
                          echo "<td class='small'>".
                               "<div class='progress progress-xs'>".
                               "<div class='progresb' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                               "</div>".
                               "</td>";
                              echo "<td class='small'><span class='badge btn-d'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>"; 
                        } 
                        else{
                        echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progress-bar progress-bar-danger' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                        echo "<td class='small'><span class='badge btn-danger'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                        }
                       }
                       else if($tareas_pro[$i]["TAREA_FECHA_FIN"] == false )//evaluamos si no hay datos en d_fecha_fin y d_fcha 
                       {
                        echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progress ' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                        echo "<td class='small'><span class='badge btn- '>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                       }
                       else if(date_create($tareas_pro[$i]["TAREA_FECHA_FIN"]) >= $fecha_hoy && $tareas_pro[$i]["TAREA_FECHA_INI_REAL"]==true )//evaluamos si d_fecha_fin es mallor a la fecha de hoy para pinta todo de color azul
                       {
                        echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progress-bar progress-bar-info' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                        echo "<td><span class='badge btn-info'>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                       }
                       else if (date_create($tareas_pro[$i]["TAREA_FEC_INI"]) > $fecha_hoy )
                       {
                         
                        if(!each($tareas_pro[$i]["TAREA_FEC_INI"]))//
                        {
                          echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progres' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                        echo "<td class='small'><span class='badge btn-d'> ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                        }
                        else
                        {
                          echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progres' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                        echo "<td class='small'><span class='badge btn-d'> ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                        }
                        
                       }
                       else
                       {
                        echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progres' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td class='small'>";
                        echo "<td><span class='badge btn-warning'> ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                       }

                    }
// ################################ termina condiciones si no tenemos datos en d_fecha_ini_real ###################################

                  else 
                  {
                    echo "<td class='small'>".
                         "<div class='progress progress-xs'>".
                         "<div class='progre o' style='width: ".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%'></div>".
                         "</div>".
                         "</td>";
                    echo "<td class='small'><span class='badge btn-d '>".$tareas_pro[$i]["TAREA_PORCENTAJE"]."%</span></td>";
                  }
                  ?>
<!-- ######### termina condicion para cambiar el color del progreso y porcentaje de la tabla de tareas #########-->
                  <?php  
                    echo "<td class='small'><form method='post' action='sistemas.php#actividades'><input type='hidden' value='' name='iid_actividad'><button type='submit' name='iid_tarea' value='".$tareas_pro[$i]["TAREA_ID"]."' class='btn btn-link'>".$tareas_pro[$i]["TAREA_TIPO"]."</button></form></td>"; 
                  ?>
                </tr>
          <?php 
                  }//Cierra while para traer registros de ss_tareas 
          ?>
 
    <!-- ############## Terminaia TR para traer los registros de la tabla tareas ################### --> 
              </table><!-- termina tabla de tareas -->
              </div>

            </div><!-- Termina box-body de la tabla de tareas -->
            <!-- /.box-body --> 
          </div>
          <!-- /.box -->
<!-- ############################# TERMINA CODE TABLA TAREAS ##################################### -->

        </div><!-- Termina seccion derecha --> 
        <!-- /.col -->

      </div>
      <!-- /.row --> 
</section><!-- Termina la seccion de Grafica Principal --> 
<!-- ################################# Termina Seccion de grafica proyecto,informacion y tabla tareas ################################# -->



<!-- ################################# INICIA TABLA ACTIVIDADES-DESVIACIONES ################################## -->  
<?php
  if( $id_tarea == true ){
?> 
<section id="actividades"><!-- Inicia la seccion de la tabla actividades-desviaciones --> 
  <div class="box box-primary">    
            <div class="box-header with-border"> 
              <h5>
              <?php   
                $tabla_actividades = $instanciaSis->tabla_actividades_leer($id_proyecto,$id_tarea); 
                $titulo_tarea = sizeof($tabla_actividades) / sizeof($tabla_actividades) ; 
                for($i=0;$i<sizeof($titulo_tarea);$i++)
                         {//Abre if para traer titulo de la tabla actividades 
                  echo "ACTIVIDADES DE LA TAREA <b> ".$tabla_actividades[$i]["ACT_TAREA_NOM"]."</b> || PROYECTO <b>".$nombre_proyecto."</b>";
                 }//Cierra if para traer titulo de la tabla actividades 
              ?>
 
              </h5> 
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button> 
              </div>
            </div>
            <div class="box-body">   

              <div class="table-responsive">
              <table class="table table-bordered"><!-- inicia tabla de actividades -->
                <!-- <tr>
                  <th colspan="5">Actividades</th>
                </tr> -->
                <tr>
                  <th class='small' >ID</th>
                  <th class='small' >Nombre de la Act.</th>
                  <th class='small' >Responsable</th>
                  <!-- <th>Días de atraso al iniciar</th> -->
                  <th style="width: 190px" class='small' >Atraso</th>
                </tr>
                <?php   
                  for($i=0;$i<sizeof($tabla_actividades);$i++)
                   {//Abre if para traer registros de la tabla actividades 
// #################### Inicia code para encontrar la palabra REUNIÓN-REUNION-REUNIONES #######################
                    $cadena_string = $tabla_actividades[$i]["ACT_NOMBRE"];//Especificamos en que columna busamos la palabra
                    $palabra1   = 'REUNIÓN'; //Especificamos la palabra a buscar
                    $palabra2   = 'REUNIONES';//Especificamos la palabra a buscar
                    $palabra3   = 'REUNION'; //Especificamos la palabra a buscar

                    $pos = strpos($cadena_string, $palabra1);//indicamos la palabra que buscara en $cadena_string
                    $pos1 = strpos($cadena_string, $palabra2);//indicamos la palabra que buscara en $cadena_string
                    $pos2 = strpos($cadena_string, $palabra3);//indicamos la palabra que buscara en $cadena_string
// #################### Termina code para encontrar la palabra REUNIÓN-REUNION-REUNIONES #######################
                ?>
                <tr>
                  
                  <td class='small' ><?= $tabla_actividades[$i]["ACT_IID"] ?></td>
                  <td class='small' > 
                      <?PHP
                        if ($pos === false && $pos1 === false && $pos2 === false) {//si no encuentra la palabra REUNION/REUNIONES solo muestra nombre de la actividad
                          echo $tabla_actividades[$i]["ACT_NOMBRE"];
                            } //cierra si no encuentra la palabra REUNION/REUNIONES solo muestra nombre de la actividad
                            else {// si encuentra la palabra REUNION/REUNIONES muestra nombre de actidad y boton ver minuta
                              if ( is_null($tabla_actividades[$i]["ACT_B_MINUTA"]))//inicia evaluacion si no hay datos en b_minuta
                              {//inicia abre evaluacion si no hay datos en b_minuta para ver boton de subir minuta

                                echo $tabla_actividades[$i]["ACT_NOMBRE"]."<a href='upload_minuta.php?iid_proyecto[]=".$id_proyecto."&iid_tarea[]=".$id_tarea."&iid_actividad[]=".$tabla_actividades[$i]["ACT_IID"]."'>";
                            if($_SESSION['valor_perfil'] == 1)//validacion de admin para subir minuta
                                echo "<span class='badge btn-warning'><i class='fa fa-cloud-upload'></i>  Subir Minuta</span></a>"; 
                              }//cierra evaluacion si no hay datos en b_minuta para ver boton de subir minuta
                                
                              else{//si hay datos en b_minuta solo muestra el boton para ver la minuta
                                echo $tabla_actividades[$i]["ACT_NOMBRE"]."<a target='_blank' href='".$tabla_actividades[$i]["ACT_B_MINUTA"]."'><span class='badge btn-info'><i class='fa fa-eye'></i>  Ver Minuta</span></a>";
                              }//termina si hay datos en b_minuta solo muestra el boton para ver la minuta
                            }// cierra si encuentra la palabra REUNION/REUNIONES muestra nombre de actidad y boton ver minuta

                       ?>
                       
                  </td>
                  <td class='small' >
                  <?= $tabla_actividades[$i]["ACT_PER_NOM"]." ".$tabla_actividades[$i]["ACT_PER_PAT"]." ".$tabla_actividades[$i]["ACT_PER_MAT"] ?>
                  </td>
                  <!-- <td style="width: 90px"><span class="badge bg-green">0</span></td> -->
<!-- ##################### Empieza evaluacion si la actividad tiene datos en d_fecha_real ##################### -->
              <?php 
                if ( is_null($tabla_actividades[$i]["ACT_FECHA_FIN_REAL"]) ){ //evaluamos si la actividad no tiene datos en d_fecha_real para contar desde la variable $var_fecha_hoy

                  $var_fecha_hoy = date('d-m-Y') ; //Sacamos la fecha de hoy
                  $d_fecha_fin = date_create($tabla_actividades[$i]["ACT_FECHA_FIN"]);//Sacamos la fecha del fin de Proyecto
                  $fecha_hoy = date_create($var_fecha_hoy);//pasamos la variable $var_fecha_hoy a $fecha_hoy
                  $cuenta_dias = date_diff($d_fecha_fin, $fecha_hoy);//contamos los dias de diferencia
                  $imprime_dias_null = $cuenta_dias->format('%R%a días');//Imprimimos los dias de diferencia

                  echo "<td class='small'  style='width: 90px'>";
                    if($d_fecha_fin<$fecha_hoy)//evaluamos si $d_fecha_fin es menor a la fecha de hoy para mostrar los dias de atraso y los detalles de la desviacion 
                    {
                        echo "<form method='post' action='sistemas.php#desviaciones'><span class='badge bg-red'><i class='fa fa-clock-o'></i>  $imprime_dias_null </span>";
                        //echo "<a href='sistemas_old.php?iid_proyecto[]=$poyecto_id&iid_tarea[]=$tarea_id&iid_actividad[]=".$tabla_actividades[$i]["ACT_IID"]."'><span class='badge bg-red'><i class='fa fa-eye'></i>  Detalles</span></a>";
                        echo " <button type='submit' name='iid_actividad' value='".$tabla_actividades[$i]["ACT_IID"]."' class='btn btn-danger btn-xs'><i class='fa fa-eye'> Detalles</button></form>";
                    }
                    else if ($d_fecha_fin==$fecha_hoy){//evaluamos si $d_fecha_fin es iagual a la fecha de hoy para alerta en riesgo de tener una desviacion
                        echo "<span class='badge bg-yellow'><i class='fa fa-clock-o'></i>  $imprime_dias_null</span>";
                      }
                    else//si $d_fecha_fin es mallor a la fecha de hoy entonces no hay riesgo de tener desviaciones
                    {
                      echo "<span class='badge btn-info'><i class='fa fa-clock-o'></i> +0 días</span>";
                    }

                  echo "</td>";
                      
                }//Termina evaluamos si la actividad no tiene datos en d_fecha_real para contar desde la variable $var_fecha_hoy
                else if ( $tabla_actividades[$i]["ACT_FECHA_FIN_REAL"] == true ) {//evaluamos si la actividad si tiene datos en d_fecha_real para contar desde d_fecha_real
                  $d_fecha_fin = date_create($tabla_actividades[$i]["ACT_FECHA_FIN"]);//Sacamos la fecha del fin de Proyecto
                  $d_fecha_real =  date_create($tabla_actividades[$i]["ACT_FECHA_FIN_REAL"]);//pasamos la variable $d_fecha_real a $d_fecha_real
                  $cuenta_dias = date_diff($d_fecha_fin, $d_fecha_real);
                  $imprime_dias = $cuenta_dias->format('%R%a días');
                      
                  echo "<td class='small'  style='width: 90px'>";
                  
                  
                   if ($d_fecha_fin > $d_fecha_real ){//evaluaion si los dias son 0 para no mostrar detalles 
                      if($d_fecha_fin == $d_fecha_real)
                      {

                        echo "<span class='badge btn-info'><i class='fa fa-clock-o'></i>  $imprime_dias </span>";
                      }
                      else
                      {
                         echo "<span class='badge btn-info'><i class='fa fa-clock-o'></i>  +0 días </span>";
                      }

                      }//Termina evaluaion si los dias son 0 para no mostrar detalles 
                    else if ($d_fecha_fin < $d_fecha_real ){
                      //si los dias de atrazo son mas de 0 entonces mostramos los detalles
                        echo "<form method='post' action='sistemas.php#desviaciones'><span class='badge bg-yellow'><i class='fa fa-clock-o'></i>  $imprime_dias </span> ";
                        //echo "<a href='sistemas_old.php?iid_proyecto[]=$poyecto_id&iid_tarea[]=$tarea_id&iid_actividad[]=".$tabla_actividades[$i]["ACT_IID"]."'><span class='badge bg-red'><i class='fa fa-eye'></i>  Detalles</span></a>";
                        echo " <button type='submit' name='iid_actividad' value='".$tabla_actividades[$i]["ACT_IID"]."' class='btn btn-danger btn-xs'><i class='fa fa-eye'> Detalles</button></form>";
                      }//Termina si los dias de atrazo son mas de 0 entonces mostramos los detalles

                      else{//si los dias de atrazo son mas de 0 entonces mostramos los detalles
                        echo "<span class='badge btn-info'><i class='fa fa-clock-o'></i>  $imprime_dias </span>";
                      }//Termina si los dias de atrazo son mas de 0 entonces mostramos los detalles  

                  echo "</td>";
                }//Termina evaluamos si la actividad si tiene datos en d_fecha_real para contar desde d_fecha_real
              ?> 
<!-- #################### Termina evaluacion si la actividad tiene datos en d_fecha_real #################### -->
                </tr>
                  <?php  
                  }//Cierra if para traer registros de la tabla actividades
                  ?>
              </table><!-- termina tabla de actividades -->
              </div>


            </div>  
        <!-- /.box-body --> 
  </div> 
</section><!-- Termina la seccion de la tabla actividades-desviaciones -->
<?php
}
?> 
<!-- ################################# TERMINA TABLA ACTIVIDADES-DESVIACIONES ################################# -->



<!-- ################################# Inicia Seccion de la tabla de desviaciones ################################## --> 
<?php
  if( $id_actividad == true ){
    if($_SESSION['valor_perfil'] <> 3) // Restricción de involucrados para desviaciones
            { // abre if de Restricción de involucrados para desviaciones
?> 
<section id="desviaciones" class="connectedSortable"><!-- Inicia la seccion de la tabla de desviaciones --> 
  <div class="box box-danger">    
            <div class="box-header with-border"> 
              <h5>
              <?php
              $tabla_desviacioness = $instanciaSis->desviaciones_tabla_leer($id_proyecto, $id_tarea, $id_actividad);
              $titulo_desviaciones = count($tabla_desviacioness) / count($tabla_desviacioness) ;
                for ($i=0; $i <$titulo_desviaciones ; $i++) { 
                  echo "<b>DESVIACIONES DE LA ACTIVIDAD</b><code>".$tabla_desviacioness[$i]["DES_ACT_NOMBRE"]."</code>";
                } 
              ?> 
              </h5>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button> 
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
                  <?php  
                    for($i=0; $i<count($tabla_desviacioness); $i++)//for para traer los registros de la desviacion de la tarea
                      {//Abre while para traer los registros de la desviacion de la tarea  
                        if($tabla_desviacioness[$i]["DES_ID"] == true)//depura registros null
                        {//abre if de depuracion de registros
                          echo "<tr>";
                            echo "<td class='small' >".$tabla_desviacioness[$i]["DES_ID"]."</td>";
                            echo "<td class='small' >".$tabla_desviacioness[$i]["DES_NOMBRE"]."</td>";
                            echo "<td class='small' >".$tabla_desviacioness[$i]["DES_RAZON"]."</td>";
                            echo "<td class='small' >".$tabla_desviacioness[$i]["DES_OBSERVACIONES"]."</td>";
                            echo "<td class='small' >".$tabla_desviacioness[$i]["DES_PER_NOM"]." ".$tabla_desviacioness[$i]["DES_PER_PAT"]." ".$tabla_desviacioness[$i]["DES_PER_MAT"]."</td>";
                          echo "</tr>";
                        }//cierra if de depuracion de registros
                      }//Cierra while para traer los registros de la desviacion de la tarea 
                  ?>
              </table><!-- termina tabla de desviaciones -->
            </div>

            </div>  
        <!-- /.box-body --> 
  </div> 
</section><!-- Termina la seccion de la tabla de desviaciones --> 
<?php
    }
  }
?> 
<!-- ################################# Termina Seccion de la tabla de desviaciones ################################# -->
 
 
 
 

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
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script> 
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
<!-- page script -->
<script type="text/javascript">
  // To make Pace works on Ajax calls
  $(document).ajaxStart(function() { Pace.restart(); });
    $('.ajax').click(function(){
        $.ajax({url: '#', success: function(result){
            $('.ajax-content').html('<hr>Ajax Request Completed !');
        }});
    });

    $(".dial-container").redrawKnob();
</script> 
<!-- #################################### Termina Libreria de Script ######################################-->
<!-- jQuery Knob -->
<script src="../plugins/knob/jquery.knob.js"></script>
<!-- Sparkline -->
<script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- page script -->
<script>
  $(function () {
    /* jQueryKnob */

    $(".knob").knob({
      /*change : function (value) {
       //console.log("change : " + value);
       },
       release : function (value) {
       console.log("release : " + value);
       },
       cancel : function () {
       console.log("cancel : " + this.value);
       },*/
      draw: function () {

        // "tron" case
        if (this.$.data('skin') == 'tron') {

          var a = this.angle(this.cv)  // Angle
              , sa = this.startAngle          // Previous start angle
              , sat = this.startAngle         // Start angle
              , ea                            // Previous end angle
              , eat = sat + a                 // End angle
              , r = true;

          this.g.lineWidth = this.lineWidth;

          this.o.cursor
          && (sat = eat - 0.3)
          && (eat = eat + 0.3);

          if (this.o.displayPrevious) {
            ea = this.startAngle + this.angle(this.value);
            this.o.cursor
            && (sa = ea - 0.3)
            && (ea = ea + 0.3);
            this.g.beginPath();
            this.g.strokeStyle = this.previousColor;
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
            this.g.stroke();
          }

          this.g.beginPath();
          this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
          this.g.stroke();

          this.g.lineWidth = 2;
          this.g.beginPath();
          this.g.strokeStyle = this.o.fgColor;
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
          this.g.stroke();

          return false;
        }
      }
    });
    /* END JQUERY KNOB */

    //INITIALIZE SPARKLINE CHARTS
    $(".sparkline").each(function () {
      var $this = $(this);
      $this.sparkline('html', $this.data());
    });

    /* SPARKLINE DOCUMENTATION EXAMPLES http://omnipotent.net/jquery.sparkline/#s-about */
    drawDocSparklines();
    drawMouseSpeedDemo();

  });
  function drawDocSparklines() {

    // Bar + line composite charts
    $('#compositebar').sparkline('html', {type: 'bar', barColor: '#aaf'});
    $('#compositebar').sparkline([4, 1, 5, 7, 9, 9, 8, 7, 6, 6, 4, 7, 8, 4, 3, 2, 2, 5, 6, 7],
        {composite: true, fillColor: false, lineColor: 'red'});


    // Line charts taking their values from the tag
    $('.sparkline-1').sparkline();

    // Larger line charts for the docs
    $('.largeline').sparkline('html',
        {type: 'line', height: '2.5em', width: '4em'});

    // Customized line chart
    $('#linecustom').sparkline('html',
        {
          height: '1.5em', width: '8em', lineColor: '#f00', fillColor: '#ffa',
          minSpotColor: false, maxSpotColor: false, spotColor: '#77f', spotRadius: 3
        });

    // Bar charts using inline values
    $('.sparkbar').sparkline('html', {type: 'bar'});

    $('.barformat').sparkline([1, 3, 5, 3, 8], {
      type: 'bar',
      tooltipFormat: '{{value:levels}} - {{value}}',
      tooltipValueLookups: {
        levels: $.range_map({':2': 'Low', '3:6': 'Medium', '7:': 'High'})
      }
    });

    // Tri-state charts using inline values
    $('.sparktristate').sparkline('html', {type: 'tristate'});
    $('.sparktristatecols').sparkline('html',
        {type: 'tristate', colorMap: {'-2': '#fa7', '2': '#44f'}});

    // Composite line charts, the second using values supplied via javascript
    $('#compositeline').sparkline('html', {fillColor: false, changeRangeMin: 0, chartRangeMax: 10});
    $('#compositeline').sparkline([4, 1, 5, 7, 9, 9, 8, 7, 6, 6, 4, 7, 8, 4, 3, 2, 2, 5, 6, 7],
        {composite: true, fillColor: false, lineColor: 'red', changeRangeMin: 0, chartRangeMax: 10});

    // Line charts with normal range marker
    $('#normalline').sparkline('html',
        {fillColor: false, normalRangeMin: -1, normalRangeMax: 8});
    $('#normalExample').sparkline('html',
        {fillColor: false, normalRangeMin: 80, normalRangeMax: 95, normalRangeColor: '#4f4'});

    // Discrete charts
    $('.discrete1').sparkline('html',
        {type: 'discrete', lineColor: 'blue', xwidth: 18});
    $('#discrete2').sparkline('html',
        {type: 'discrete', lineColor: 'blue', thresholdColor: 'red', thresholdValue: 4});

    // Bullet charts
    $('.sparkbullet').sparkline('html', {type: 'bullet'});

    // Pie charts
    $('.sparkpie').sparkline('html', {type: 'pie', height: '1.0em'});

    // Box plots
    $('.sparkboxplot').sparkline('html', {type: 'box'});
    $('.sparkboxplotraw').sparkline([1, 3, 5, 8, 10, 15, 18],
        {type: 'box', raw: true, showOutliers: true, target: 6});

    // Box plot with specific field order
    $('.boxfieldorder').sparkline('html', {
      type: 'box',
      tooltipFormatFieldlist: ['med', 'lq', 'uq'],
      tooltipFormatFieldlistKey: 'field'
    });

    // click event demo sparkline
    $('.clickdemo').sparkline();
    $('.clickdemo').bind('sparklineClick', function (ev) {
      var sparkline = ev.sparklines[0],
          region = sparkline.getCurrentRegionFields();
      value = region.y;
      alert("Clicked on x=" + region.x + " y=" + region.y);
    });

    // mouseover event demo sparkline
    $('.mouseoverdemo').sparkline();
    $('.mouseoverdemo').bind('sparklineRegionChange', function (ev) {
      var sparkline = ev.sparklines[0],
          region = sparkline.getCurrentRegionFields();
      value = region.y;
      $('.mouseoverregion').text("x=" + region.x + " y=" + region.y);
    }).bind('mouseleave', function () {
      $('.mouseoverregion').text('');
    });
  }

  /**
   ** Draw the little mouse speed animated graph
   ** This just attaches a handler to the mousemove event to see
   ** (roughly) how far the mouse has moved
   ** and then updates the display a couple of times a second via
   ** setTimeout()
   **/
  function drawMouseSpeedDemo() {
    var mrefreshinterval = 500; // update display every 500ms
    var lastmousex = -1;
    var lastmousey = -1;
    var lastmousetime;
    var mousetravel = 0;
    var mpoints = [];
    var mpoints_max = 30;
    $('html').mousemove(function (e) {
      var mousex = e.pageX;
      var mousey = e.pageY;
      if (lastmousex > -1) {
        mousetravel += Math.max(Math.abs(mousex - lastmousex), Math.abs(mousey - lastmousey));
      }
      lastmousex = mousex;
      lastmousey = mousey;
    });
    var mdraw = function () {
      var md = new Date();
      var timenow = md.getTime();
      if (lastmousetime && lastmousetime != timenow) {
        var pps = Math.round(mousetravel / (timenow - lastmousetime) * 1000);
        mpoints.push(pps);
        if (mpoints.length > mpoints_max)
          mpoints.splice(0, 1);
        mousetravel = 0;
        $('#mousespeed').sparkline(mpoints, {width: mpoints.length * 2, tooltipSuffix: ' pixels per second'});
      }
      lastmousetime = timenow;
      setTimeout(mdraw, mrefreshinterval);
    };
    // We could use setInterval instead, but I prefer to do it this way
    setTimeout(mdraw, mrefreshinterval);
  }
</script>
<?php conexion::cerrar($conn); ?>
