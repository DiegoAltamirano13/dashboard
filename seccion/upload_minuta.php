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

//////////////////////////// VALIDACION DEL MODULO ASIGNADO
include_once '../class/Perfil.php';
$instacia_modulo  = new Perfil;

$modulos_valida = $instacia_modulo->modulos_valida($_SESSION['iid_empleado'], '1');
if($modulos_valida == 0)
{
  header('Location: index.php');
}

if( $_SESSION['valor_perfil'] != 1 )
{
  header('Location: index.php');
}

///////////////////////////////////////////

?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php 
include_once('../layouts/plantilla.php'); 
?>
<!-- ##################################### Contenido de la pagina #########################-->
<!-- ##################################### Contenido de la pagina #########################-->

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1> 
        Subir minutas 
      </h1>
    </section>

    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

      <!-- Main row -->
      <div class="row"><!-- Inicia primer row principal -->
        <!-- Left col -->
        <section class="col-lg-12 connectedSortable"><!-- Inicia seccion izquierda  -->

          <!-- AREA CHART -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Minuta de la actividad <?php echo $actividad_id; ?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
      <?php
// ################################## Inicia code para traer los registros de la tabla proyectos ##################################
          if ( !empty($_GET["iid_proyecto"]) && is_array($_GET["iid_proyecto"]) ) //si el GET iid_proyecto devuelve un valor y es un array para el titulo
                  {//abre if si el GET devuelve un valor y es un array para el titulo

                    foreach ( $_GET["iid_proyecto"] as $poyecto_id ) {//abre foreach para traer el titulo del GET iid_proyecto seleccionado
                      $sql = "SELECT iid_proyecto, v_nombre FROM ss_proyecto WHERE iid_proyecto ='".$poyecto_id."' ";
                      $tabla_tarea_titulo= oci_parse($conn, $sql);
                    oci_execute($tabla_tarea_titulo);
                    while (($row = oci_fetch_array($tabla_tarea_titulo,OCI_BOTH)) !=false ){ //Abre while para traer registros del titulo de la tabla tarea 
                  }//Cierra while para traer registros del titulo de la tabla tarea

                  }//cierra foreach para traer el titulo del GET iid_proyecto seleccionado
                }//cierra if si el GET devuelve un valor y es un array para el titulo
// ################################## Termina code para traer los registros de la tabla proyectos ##################################

// ################################## inicia code para traer registros de la tabla de tareas ################################## 
          if ( !empty($_GET["iid_tarea"]) && is_array($_GET["iid_tarea"]) ) 
            {//abre if para ver el GET de iid_tarea
            foreach ( $_GET["iid_tarea"] as $tarea_id ) {//abre foreach para traer  la actividad seleccionada to_char(a.d_fecha_fin,'dd-mm-yyyy')
              $tabla_actividades= oci_parse($conn, "select t.v_nombre, a.iid_actividad, a.v_nombre, per.v_nombre, a.d_fecha_inicio, a.d_fecha_ini_real, to_char(a.d_fecha_fin,'dd-mm-yyyy'), to_char(a.d_fecha_real,'dd-mm-yyyy'), a.iid_status, per.v_ape_pat, per.v_ape_mat, a.b_minuta from ss_actividades a inner join ss_tareas t on a.iid_tarea = t.iid_tarea and a.iid_proyecto = t.iid_proyecto inner join  no_personal per on a.iid_empleado_resp = per.iid_empleado  ".
                                                   "  where a.iid_tarea ='".$tarea_id."' order by iid_actividad asc  ");
                oci_execute($tabla_actividades);           
        
                //for($i=-1 ;$i<count($row = oci_fetch_array($tabla_tarea,OCI_BOTH));$i++){
                while (($row = oci_fetch_array($tabla_actividades,OCI_BOTH)) !=false ){ //Abre while para traer registros de ss_tareas
                    if ($row[2] == 1)
                    {
                      echo "TAREA ($row[2])" ;
                      break;
                    }
                  
                }//cierra while para traer registros de ss_tareas
              }//cierra foreach para traer  la actividad seleccionada to_char(a.d_fecha_fin,'dd-mm-yyyy')
            }//cierra if para ver el GET de iid_tarea
// ################################## Termina code para traer registros de la tabla de tareas ################################## 

// ############################# Inicia code para traer la informacion de la tabla de actividades  #############################
        if ( !empty($_GET["iid_actividad"]) && is_array($_GET["iid_actividad"]) )//Si hay una consulta con el iid_actividad de tipo GET mostrara la tabla de desviaciones 
            {//abre if para ver el GET de iid_actividad
              foreach ( $_GET["iid_actividad"] as $actividad_id ) {//abre foreach para traer  la desviacion de la actividad seleccionada  

              $tabla_desviaciones= oci_parse($conn, "select d.iid_desviacion, d.v_nombre, d.v_razon, d.iid_actividad, d.v_observaciones, per.v_nombre, per.v_ape_pat, per.v_ape_mat".
                                                    "  from ss_desviaciones d".
                                                    "  inner join no_personal per on d.iid_empleado_resp = per.iid_empleado  ".
                                                           "  where d.iid_actividad ='".$actividad_id."' and d.iid_tarea ='".$tarea_id."' order by iid_desviacion asc  ");
          oci_execute($tabla_desviaciones);          
    
            //for($i=-1 ;$i<count($row = oci_fetch_array($tabla_tarea,OCI_BOTH));$i++){
            while (($row = oci_fetch_array($tabla_desviaciones,OCI_BOTH)) !=false ){ //Abre while para traer registros de ss_tareas
              echo "";
            }
          }
        }
// ############################# Termina code para traer la informacion de la tabla de actividades  #############################
        // select blob
                // $mykey = 7 ;
                // $consulta = 'SELECT myblob, iid_proyecto FROM mytable WHERE mykey = :mykey';

                // $stid = oci_parse ($conn, $consulta);
                // oci_bind_by_name($stid, ":mykey", $mykey, 5);
                // oci_execute($stid);

                // echo '<table border="1">';
                // while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_LOBS)) {
                //     echo '<tr><td><textarea>'.$row['MYBLOB'].'</textarea></td></tr>';
                //     echo '<tr><td><textarea>'.$row['IID_PROYECTO'].'</textarea></td></tr>';
                //     // En un bucle, liberar la variable grande antes de la 2ª obtención reduce el uso de memoria de picos de PHP
                //     unset($row);  
                // }
                // echo '</table>';
        // select blob
// ########################################## Inicia Code para insertar minutas en la DB ##########################################
          if( isset($_POST['data_base64'])  ) //si en envio post de data_base64 no esta basio entonces actualiza en tabla ss_actividades 
          {//abre if si el post de data_base64 no esta basio entonces actualiza en tabla ss_actividades
            //$comprobacion_pos_get ; $_SESSION['id_proyecto'] $_SESSION['id_tarea'] $_SESSION['id_actividad']
              if ( $_POST["pdfbase64"] == false || $_POST["iid_proyecto"] == false || $_POST["iid_actividad"] == false || $_POST["iid_tarea"] == false)//evaluamos si no tiene datos el post de pdfbase64-iid_proyecto-iid_actividad-iid_tarea para no insrta datos y manda alert
              {
                echo "<div class='alert alert-danger alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4><i class='icon fa fa-ban'></i> Error!</h4>
                No se pudo Insertar el Registro en Dase de Batos.
              </div>";
              }
              else
              {//abre si tiene datos el post de pdfbase64-iid_proyecto-iid_actividad-iid_tarea actualizamos la tabla de ss_actividades
                ////////////////////////////// UPDATE ///////////////////////////////////////////
                $id = 512;
                //$mykey = 8 ;
                $iid_proyecto = $_POST["iid_proyecto"] ;
                $iid_tarea = $_POST["iid_tarea"] ;
                $iid_actividad = $_POST["iid_actividad"] ;
                $b_minuta = $_POST["pdfbase64"] ;
                $stid = oci_parse($conn, 'UPDATE ss_actividades 
                  SET b_minuta = EMPTY_BLOB()   
                  WHERE iid_proyecto= :iid_proyecto AND iid_tarea = :iid_tarea AND iid_actividad = :iid_actividad RETURNING b_minuta INTO :b_minuta'); 

                $blob = oci_new_descriptor($conn, OCI_D_LOB);
                oci_bind_by_name($stid, ':id', $id);
                //oci_bind_by_name($stid, ":mykey", $mykey, 5);//pues
                oci_bind_by_name($stid, ":iid_proyecto", $iid_proyecto, 5);//pues
                oci_bind_by_name($stid, ":iid_tarea", $iid_tarea, 5);//pues
                oci_bind_by_name($stid, ":iid_actividad", $iid_actividad, 5);//pues
                oci_bind_by_name($stid, ":b_minuta", $blob, -1, OCI_B_BLOB);
                oci_execute($stid, OCI_NO_AUTO_COMMIT);
                $blob->save($b_minuta);
                oci_commit($conn);
                ///////////////////////////////// UPDATE /////////////////////////////////
                
                // ##################################### INICIA INSERT ##################################### 
                // $datobase64 = $_POST["pdfbase64"] ;
                // $iid_proyecto = $_POST["iid_proyecto"] ;
                // $iid_tarea = $_POST["iid_tarea"] ;
                // $iid_actividad = $_POST["iid_actividad"] ;

                // $sql = "INSERT INTO ss_minutas (iid_proyecto, iid_tarea, iid_actividad, b_minuta)
                // VALUES (:iid_proyecto, :iid_tarea, :iid_actividad, EMPTY_BLOB())
                // RETURNING b_minuta INTO :b_minuta";

                // oci_bind_by_name($compiled, ':url', $url_name);

                // $stid = oci_parse($conn, $sql);
                // $blob = oci_new_descriptor($conn, OCI_D_LOB);
                // oci_bind_by_name($stid, ":iid_proyecto", $iid_proyecto, 5);//pues
                // oci_bind_by_name($stid, ":iid_tarea", $iid_tarea, 5);//pues
                // oci_bind_by_name($stid, ":iid_actividad", $iid_actividad, 5);//pues
                // oci_bind_by_name($stid, ":b_minuta", $blob, -1, OCI_B_BLOB);
                // oci_execute($stid, OCI_NO_AUTO_COMMIT); // utilice OCI_DEFAULT para PHP <= 5.3.1
                // $blob->save($datobase64);
                // oci_commit($conn);
                // ##################################### TERMINA INSERT ##################################### 


                $url = 'sistemas.php';//variable para sacar la url asi donde nos dirije
                echo "<script language='javascript'>window.location = '$url';</script>";//cuando termine la actualizacion nos dirije asia sistemas.php
              }// cierra si tiene datos el post de pdfbase64-iid_proyecto-iid_actividad-iid_tarea actualizamos la tabla de ss_actividades          
          }//cierra if si el post de data_base64 no esta basio entonces actualiza en tabla ss_actividades
// ########################################## Termina Code para insertar minutas en la DB ##########################################
          $proyecto = $poyecto_id;//variable para capturar el iid_proyecto
          $tarea = $tarea_id;//variable para capturar el iid_tarea
          $actividad = $actividad_id;//variable para capturar el iid_actividad

          
      ?> 
<!-- Modal -->
<form action="upload_minuta.php" method="POST">
  <div class="form-group">
            <label for="url_file_minuta">Seleccione la minuta </label>
              <input type="file" id="url_file_minuta" onclick="reset_textarea();">
            </div>
            <div class="form-group">

              <input type="hidden" name="iid_proyecto" value="<?php echo $proyecto ?>" />
              <input type="hidden" name="iid_tarea" value="<?php echo $tarea ?>" />
              <input type="hidden" name="iid_actividad" value="<?php echo $actividad ?>" />

              <input type="hidden" id='pdfbase64' name='pdfbase64'  />

            </div> 

        
        <div id="button_block" class="modal-footer"></div>
</form>
  <!-- codificar archivo base64 -->
      <script>
        var Div_block = document.getElementById('button_block');
        var button_submit = '<button type="submit" class="btn btn-primary" name="data_base64"><i class="fa fa-cloud-upload"></i> Subir Minuta</button>';
        var button_cancel = '<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="reset_textarea();"><i class="fa fa-warning"></i> Cancelar</button>';

        document.getElementById('url_file_minuta').addEventListener('change', function() {

          var files = document.getElementById('url_file_minuta').files;
            if (files.length > 0) {
                getBase64(files[0]);
              }
        });

        function getBase64(file) {
          var reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onload = function () {
            document.getElementById("pdfbase64").value = reader.result ;
            Div_block.innerHTML = button_submit + button_cancel; 
           };

               reader.onerror = function (error) {
               console.log('Error: ', error);
               };
        }

        function reset_textarea(){
                                     document.getElementById("pdfbase64").value = "";
                                     Div_block.innerHTML = '';
                                  }
      </script>
<!-- codificar archivo base64 -->
        
      
<!--######################################## termina Prueba de base 64 ########################################-->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- TERMINA AREA CHART -->
 
        </section><!-- Termina seccion izquierda  -->
        <!-- /.Left col -->
        
      </div><!-- Termina primer row principal -->
      <!-- /.row (main row) -->

    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
<!-- Page script -->
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
</html>
<?php conexion::cerrar($conn); ?>
