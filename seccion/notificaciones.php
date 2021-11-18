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
//conexion a la clase Notificaciones
require_once("../class/Notificaciones.php");
$instanciaNot = new Notificaciones();
$fila=$instanciaNot->leer();
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
include_once '../class/Perfil.php';
$instacia_modulo  = new Perfil;

$modulos_valida = $instacia_modulo->modulos_valida($_SESSION['iid_empleado'], '2');
if($modulos_valida == 0)
{ 
  header('Location: index.php');
}

/////////////////////////////////////////// 
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); 

?>

<!-- ##################################### Contenido de la pagina #########################-->
<!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard Notificaciones
      </h1>
    </section>
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->



      <!-- Main row -->
      <div class="row"><!-- Inicia primer row principal -->
        <!-- Left col -->
        <section class="col-lg-12 connectedSortable"><!-- Inicia seccion izquierda  -->

<!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& INICIA AREA NOTIFICACIONES ACTIVADAS &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Notificaciones Activadas</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              
              <div class="panel-group" id="accordion">
<!-- ################################ INICIA NOTIFICACIONES ACTIVADAS DE SISTEMAS ################################ -->
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#sistemas-coll">Sistemas</a>
                    </h4>
                  </div>
                  <div id="sistemas-coll" class="panel-collapse collapse in">
                    <div class="panel-body table-responsive no-padding">
                    <!-- /.box-header -->
              <table class="table table-bordered"> 
                <tr>
                  <th class="small">Proyecto</th>
                  <th class="small">Tarea</th>
                  <th class="small">Acividad</th>
                  <th class="small">Resp. Act</th>
                  <th class="small">Inicio</th>
                  <th class="small">Final</th>
                  <th class="small">Atraso</th>
                  <th class="small">Email</th>
                  <th class="small bg-gray">Desviación</th>
                  <th class="small bg-gray">Razón</th>
                  <th class="small bg-gray">Involucrado</th>
                  <th class="small bg-gray">Email</th>
                </tr>
                <?php 
               for($i=0;$i<sizeof($fila);$i++)
                {
                    if( $fila[$i]["ACT_FEC_FIN_REAL"] == false)
                    {
                      $var_fecha_hoy = date('d-m-Y') ; //Sacamos la fecha de hoy
                      $d_fecha_fin = date_create($fila[$i]["ACT_FEC_FIN"]);//Sacamos la fecha del fin de Proyecto
                      $fecha_hoy = date_create($var_fecha_hoy);//pasamos la variable $var_fecha_hoy a $fecha_hoy
                      $cuenta_dias = date_diff($d_fecha_fin, $fecha_hoy);//contamos los dias de diferencia
                      $imprime_dias_null = $cuenta_dias->format('%R%a DÍAS');//Imprimimos los dias de diferencia
                    if ($d_fecha_fin < $fecha_hoy)
                    {
                ?>
                <tr>
                  <td class="small"> <?= $fila[$i]["PRO_NOMBRE"] ?> </td>
                  <td class="small"> <?= $fila[$i]["TAR_NOMBRE"] ?> </td>
                  <td class="small"> <?= $fila[$i]["ACT_NOMBRE"] ?> </td>
                  <td class="small"> <?= $fila[$i]["PER1_NOMBRE"]." ".$fila[$i]["PER1_PAT"]." ".$fila[$i]["PER1_MAT"] ?> </td>
                  <td class="small"> <?= $fila[$i]["ACT_FEC_INI"] ?> </td>
                  <td class="small"> <?= $fila[$i]["ACT_FEC_FIN"] ?> </td>
                  <td class="small"><span class='badge btn-danger'><i class='fa fa-clock-o'></i><?php echo $imprime_dias_null; ?></span></td>
                  <td class="small"><i class='fa fa-envelope-o' data-toggle='tooltip' title='<?= $fila[$i]["INV1_MAIL"] ?>'></i></td>
                  <td class="small"> <?= $fila[$i]["DES_NOMBRE"] ?> </td>
                  <td class="small"> <?= $fila[$i]["DES_RAZON"] ?> </td>
                  <td class="small"> <?= $fila[$i]["INV_NOMBRE"]." ".$fila[$i]["INV_APE_PAT"]." ".$fila[$i]["INV_APE_MAT"] ?> </td>
                  <td class="small"><i class='fa fa-envelope' data-toggle='tooltip' title='<?= $fila[$i]["INV2_MAIL"] ?>'></i></td>
                </tr>
                <?php
                    // ############################ Inicia script para enviar las notificaciones ############################
                shell_exec ('yowsup-cli demos -l 5212881186646:waa1jpnHfenLIXH632o31kE68B8= -s 5212711034182 "Proyecto:'.$fila[$i]["PRO_NOMBRE"].' Responsable:'.$fila[$i]["PER1_NOMBRE"].' Dias de atraso '.$imprime_dias_null.' Involucrado: '.$fila[$i]["INV_NOMBRE"].'"');
                    $destinatario = "jorge_cba@argoalmacenadora.com.mx";  

                    //$destinatario = "Responsable de la Actividad <".$fila[$i]["INV1_MAIL"].">" . ", ";
                    //$destinatario .= "Responsable de la Desviacion <".$fila[$i]["INV2_MAIL"].">";
                    $asunto = "Actividad retrasada del proyecto ". $fila[$i]["PRO_NOMBRE"]; 
                    $cuerpo = '
                    <h5>
                    SE HA GENERADO UN ATRASO DE <font color="orange">'.$imprime_dias_null.' </font> 
                     EN EL PROYECTO <font color="orange">'.$fila[$i]["PRO_NOMBRE"].' </font>  
                      DE LA TAREA <font color="orange">'.$fila[$i]["TAR_NOMBRE"].' </font> 
                        EN LA ACTIVIDAD <font color="orange">'.$fila[$i]["ACT_NOMBRE"].' </font>
                    </h5>
                    <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                    } 
                    </style>
                    <table border="1">
                      <tr>
                        <th bgcolor="#5D7B9D"><font color="white">Proyecto</th>
                        <th bgcolor="#5D7B9D"><font color="white">Tarea</th>
                        <th bgcolor="#5D7B9D"><font color="white">Acividad</th>
                        <th bgcolor="#5D7B9D"><font color="white">Resp. Act</th>
                        <th bgcolor="#5D7B9D"><font color="white">Inicio</th>
                        <th bgcolor="#5D7B9D"><font color="white">Final</th>
                        <th bgcolor="#5D7B9D"><font color="white">Atraso</th>
                        <th bgcolor="#5D7B9D"><font color="white">Desviación</th>
                        <th bgcolor="#5D7B9D"><font color="white">Razón</th>
                        <th bgcolor="#5D7B9D"><font color="white">Involucrado</th>
                      </tr>
                      <tr>
                        <td bgcolor="#BDBDBD">'.$fila[$i]["PRO_NOMBRE"].'</td>
                        <td bgcolor="#BDBDBD">'.$fila[$i]["TAR_NOMBRE"].'</td>
                        <td bgcolor="#BDBDBD">'.$fila[$i]["ACT_NOMBRE"] .'</td>
                        <td bgcolor="#BDBDBD">'.$fila[$i]["PER1_NOMBRE"]." ".$fila[$i]["PER1_PAT"]." ".$fila[$i]["PER1_MAT"].'</td>
                        <td bgcolor="#BDBDBD">'.$fila[$i]["ACT_FEC_INI"].'</td>
                        <td bgcolor="#BDBDBD">'.$fila[$i]["ACT_FEC_FIN"].'</td>
                        <td bgcolor="#FFAB91">'.$imprime_dias_null.'</td>
                        <td bgcolor="#BDBDBD">'.$fila[$i]["DES_NOMBRE"].' </td>
                        <td bgcolor="#BDBDBD">'.$fila[$i]["DES_RAZON"].'</td>
                        <td bgcolor="#FFAB91">'.$fila[$i]["INV_NOMBRE"]." ".$fila[$i]["INV_APE_PAT"]." ".$fila[$i]["INV_APE_MAT"].' </td>
                      </tr>
                    </table>
                    ';
                    //para el envío en formato HTML 
                    $headers = "MIME-Version: 1.0\r\n"; 
                    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                    $headers .= "Cc: jorge_cba@argoalmacenadora.com.mx\r\n"; //direccion copia 

                    //dirección del remitente 
                    $headers .= "From: Notificacion de Actividad Retrasada<servidor@argoalmacenadora.com.mx>\r\n"; 
                    //mail($destinatario,$asunto,$cuerpo,$headers) ;
                    // ############################ Termina script para enviar las notificaciones ############################
                      }

                    }
                }
                ?>
              </table> 
            <!-- /.box-body -->
                    </div>
                  </div>
                </div>
<!-- ################################ TERMINA NOTIFICACIONES ACTIVADAS DE SISTEMAS ################################ -->  
<!-- ############################# TERMINA NOTIFICACIONES ACTIVADAS DE OP MANUFACTURA ############################# -->                
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#opmanufac_coll">Op. Manufactura</a>
                    </h4>
                  </div>
                  <div id="opmanufac_coll" class="panel-collapse collapse">
                    <div class="panel-body table-responsive no-padding">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    </div>
                  </div>
                </div> 
<!-- ############################# TERMINA NOTIFICACIONES ACTIVADAS DE OP MANUFACTURA ############################# -->  
              </div> 

            </div> 
          </div> 
<!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& TERMINA AREA NOTIFICACIONES ACTIVADAS &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->




<!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& INICIA AREA NOTIFICACIONES DESACTIVADAS &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
          <div class="box box-warning box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Notificaciones Desactivadas</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              
              <div class="panel-group" id="accordion-desac">
<!-- ################################ INICIA NOTIFICACIONES DESACTIVADAS DE SISTEMAS ################################ -->
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion-desac" href="#sistemas-coll-des" aria-expanded="false">Sistemas</a>
                         
                    </h4>
                  </div>
                  <div id="sistemas-coll-des" class="panel-collapse collapse in">
                    <div class="panel-body table-responsive no-padding">
                    <!-- /.box-header -->
              <table class="table table-bordered"> 
                <tr>
                  <th class="small">Proyecto</th>
                  <th class="small">Tarea</th>
                  <th class="small">Acividad</th>
                  <th class="small">Resp. Act</th>
                  <th class="small">Inicio</th>
                  <th class="small">Final</th>
                  <th class="small">Atraso</th>
                  <th class="small">Email</th>
                  <th class="small bg-gray">Desviación</th>
                  <th class="small bg-gray">Razón</th>
                  <th class="small bg-gray">Involucrado</th>
                  <th class="small bg-gray">Email</th>
                </tr>
                <?php 
                for($i=0;$i<sizeof($fila);$i++)
                {
                    if( $fila[$i]["ACT_FEC_FIN_REAL"] == true)  
                    {
                      $d_fecha_fin = date_create($fila[$i]["ACT_FEC_FIN"]);//Sacamos la fecha del fin de Proyecto
                      $d_fecha_real =  date_create($fila[$i]["ACT_FEC_FIN_REAL"]);//pasamos la variable $d_fecha_real a $d_fecha_real
                      $cuenta_dias = date_diff($d_fecha_fin, $d_fecha_real);
                      $imprime_dias = $cuenta_dias->format('%R%a días');
                ?>
                <tr>
                  <td class="small"> <?= $fila[$i]["PRO_NOMBRE"] ?> </td>
                  <td class="small"> <?= $fila[$i]["TAR_NOMBRE"] ?> </td>
                  <td class="small"> <?= $fila[$i]["ACT_NOMBRE"] ?> </td>
                  <td class="small"> <?= $fila[$i]["PER1_NOMBRE"]." ".$fila[$i]["PER1_PAT"]." ".$fila[$i]["PER1_MAT"] ?> </td>
                  <td class="small"> <?= $fila[$i]["ACT_FEC_INI_REAL"] ?> </td>
                  <td class="small"> <?= $fila[$i]["ACT_FEC_FIN_REAL"] ?> </td>
                  <td class="small"><span class='badge bg-yellow'><i class='fa fa-clock-o'></i><?php echo $imprime_dias; ?></span></td>
                  <td class="small"><i class='fa fa-envelope-o' data-toggle='tooltip' title='<?= $fila[$i]["INV1_MAIL"] ?>'></i></td>
                  <td class="small"> <?= $fila[$i]["DES_NOMBRE"] ?> </td>
                  <td class="small"> <?= $fila[$i]["DES_RAZON"] ?> </td>
                  <td class="small"> <?= $fila[$i]["INV_NOMBRE"]." ".$fila[$i]["INV_APE_PAT"]." ".$fila[$i]["INV_APE_MAT"] ?> </td>
                  <td class="small"><i class='fa fa-envelope' data-toggle='tooltip' title='<?= $fila[$i]["INV2_MAIL"] ?>'></i></td>
                </tr>
                <?php
                    }
                  }
                ?>
              </table> 
            <!-- /.box-body --> 
                    </div>  
                  </div>
                </div>
<!-- ################################ TERMINA NOTIFICACIONES DESACTIVADAS DE SISTEMAS ################################ -->  
<!-- ############################# TERMINA NOTIFICACIONES DESACTIVADAS DE OP MANUFACTURA ############################# -->                
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion-desac" href="#opmanufac_coll-des">Op. Manufactura</a>
                    </h4>
                  </div>
                  <div id="opmanufac_coll-des" class="panel-collapse collapse">
                    <div class="panel-body table-responsive no-padding">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    </div>
                  </div>
                </div> 
<!-- ############################# TERMINA NOTIFICACIONES DESACTIVADAS DE OP MANUFACTURA ############################# -->  
              </div> 

            </div> 
          </div> 
<!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& TERMINA AREA NOTIFICACIONES DESACTIVADAS &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->


    

        </section><!-- Termina seccion izquierda  -->
        <!-- /.Left col -->
      </div><!-- Termina primer row principal -->
      <!-- /.row (main row) -->

    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
  <!-- /.content-wrapper -->
<!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- FLOT CHARTS -->
<script src="../plugins/flot/jquery.flot.min.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="../plugins/flot/jquery.flot.resize.min.js"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="../plugins/flot/jquery.flot.pie.min.js"></script>
<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
<script src="../plugins/flot/jquery.flot.categories.min.js"></script>
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
<!-- Page script --> 
</html>
<?php conexion::cerrar($conn); ?>
