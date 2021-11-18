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
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$iid_empleado = $_SESSION['iid_empleado'];
$modulos_valida = Perfil::modulos_valida($iid_empleado, '10');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/*---------------INICIA INSTANCIAS---------------*/
include_once('../class/Informacion_financiera.php');
$obj_info_financiera = new Informacion_financiera();
///////////////////////////////////////////
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- *********** INICIA INCLUDE CSS *********** -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">

<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>

        Dashboard
        <small>Información Financiera</small>
      </h1>
    </section>
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->



<!-- ######################################## Inicio de Widgets ######################################### -->
    <section><!-- Inicia la seccion de los Widgets -->
      <div class="row">
      <!-- Widgets Numero de cargas -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <?php
              $info_finan_ultimo = $obj_info_financiera->info_finan_ultimo();
              if ($info_finan_ultimo == false){
                echo "<h3>NULL</h3>";
              }else{
                echo "<h3>".$info_finan_ultimo["FECHA"]."</h3>";
              }

              ?>
              <p>Ultimo Registro</p>
            </div>
            <div class="icon">
              <i class="fa fa-file-excel-o"></i>
            </div>
             <!-- <button type="submit" name="tipo" id="tipo" value="1" class="btn bg-aqua  btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button>  -->
          </div>
        </div>
        <!-- Widgets Numero de descargas -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <?php
              $widgets_reg_excel_info_finan = $obj_info_financiera->widgets_reg_excel_info_finan();
              for ($i=0; $i<count($widgets_reg_excel_info_finan); $i++){
                echo "<h3>".$widgets_reg_excel_info_finan[$i]["TOTAL_REG"]."</h3>";
              }
              ?>
              <p>Registros Activos</p>
            </div>
            <div class="icon">
              <i class="ion-android-checkmark-circle"></i>
            </div>
             <!-- <button type="submit" name="tipo" id="tipo" value="2" class="btn bg-green btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
          </div>
        </div>
        <!-- Widgets Cargas a tiempo -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <?php
              for ($i=0; $i<count($widgets_reg_excel_info_finan); $i++){
                echo "<h3>".$widgets_reg_excel_info_finan[$i]["TOTAL_REG_UPDATE"]."</h3>";
              }
              ?>
              <p>Registros Modificados</p>
            </div>
            <div class="icon">
              <i class="ion-loop"></i>
            </div>
            <!-- <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-yellow btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button>  -->
          </div>
        </div>
        <!-- Widgets Desfasados -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="small-box bg-red">
            <div class="inner">
              <?php
              for ($i=0; $i<count($widgets_reg_excel_info_finan); $i++){
                echo "<h3>".$widgets_reg_excel_info_finan[$i]["TOTAL_REG_DELETE"]."</h3>";
              }
              ?>
              <p>Registros Eliminados</p>
            </div>
            <div class="icon">
              <i class="ion-trash-a"></i>
            </div>
              <!-- <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-red btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
          </div>
        </div>
        <!-- Termino Widgets Desfasados -->
      </div>
      <!-- /.row -->
      </section><!-- Termina la seccion de los Widgets -->
<!-- ######################################### Termino de Widgets ######################################### -->





<!-- ############################ INICIA SECCION ARCHIVOS REGISTRADOS ############################# -->
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Lista de archivos registrados</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->
    <!-- INICA MENSAJE DE ALERT -->
      <?php
       if(isset($_SESSION["mensaje_alert"]) ){
          echo $_SESSION["mensaje_alert"];
          unset( $_SESSION['mensaje_alert']);
      }
      ?>
    <!-- TERMINA MENSAJE DE ALERT -->

      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#insert_excel_info_finan">
        <i class="fa fa-plus"></i> Nuevo
      </button>

      <table id="tabla_list_excel" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="small">ID</th>
            <th class="small">NOMBRE DEL ARCHIVO</th>
            <th class="small">EXTENSIÓN</th>
            <th class="small">TITULO</th>
            <th class="small">TAMAÑO</th>
            <th class="small">FECHA</th>
            <th class="small">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $leer_reg_info_finan = $obj_info_financiera->leer_excel_info_finan();
          for ($i=0; $i <count($leer_reg_info_finan) ; $i++) {
        ?>
          <tr>
            <td class="small"><?=$leer_reg_info_finan[$i]["ID_EXCEL"]?></td>
            <td class="small"><?=$leer_reg_info_finan[$i]["NOMBRE_EXCEL"]?></td>
            <td class="small"><?=$leer_reg_info_finan[$i]["EXTENSION_EXCEL"]?></td>
            <td class="small"><?=$leer_reg_info_finan[$i]["TITULO_EXCEL"]?></td>
            <td class="small"><?=$leer_reg_info_finan[$i]["SIZE_EXCEL"]?> KB</td>
            <td class="small"><?=$leer_reg_info_finan[$i]["FECHA_EXCEL"]?></td>
            <td class="small">
            <a href="../uploads_files/<?=$leer_reg_info_finan[$i]["NOMBRE_EXCEL"]?>" target="_blank"><span class="badge bg-blue btn"><i class="ion-android-download"></i> Descargar</span></a>&nbsp&nbsp
            <a href="http://docs.google.com/gview?url=argodashboard.dnsalias.org/dashboard/uploads_files/<?=$leer_reg_info_finan[$i]["NOMBRE_EXCEL"]?>" target="_blank"><span class="badge bg-green btn"><i class="ion-eye"></i> Ver</span></a>&nbsp&nbsp

            <button class="btn bg-yellow btn-xs" data-toggle="modal" data-target="#update_excel_info_finan" onclick="valores_modal('<?=$leer_reg_info_finan[$i]["ID_EXCEL"]?>','<?=$leer_reg_info_finan[$i]["NOMBRE_EXCEL"]?>','<?=$leer_reg_info_finan[$i]["TITULO_EXCEL"]?>','<?=$leer_reg_info_finan[$i]["FECHA_EXCEL"]?>');" ><i class="ion-edit"></i> Editar</button>&nbsp&nbsp

            <button type="button" onclick="delete_reg_info_finan('<?=$leer_reg_info_finan[$i]["ID_EXCEL"]?>');" class="btn bg-red btn-xs"><i class="ion-trash-a"></i> Eliminar</button>
            </td>

          </tr>

          <div id="resp"></div>
          <!-- INICIA MODAL PARA ELIMINAR ARCHIVO -->
          <div class="modal modal-danger fade" id="modal-danger">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Advertencia!!!</h4>
                </div>
                <div class="modal-body">
                  <p>¿Estas seguro que deseas eliminar el registro de información financiera?</p><br>
                  <p>Nombre del archivo: <?=$leer_reg_info_finan[$i]["NOMBRE_EXCEL"]?></p><br>
                  <p>Titulo: <?=$leer_reg_info_finan[$i]["TITULO_EXCEL"]?></p><br>
                  <p>Fecha: <?=$leer_reg_info_finan[$i]["FECHA_EXCEL"]?></p><br>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
                  <button type="button" class="btn btn-outline">Aceptar</button>
                </div>
              </div>
            </div>
          </div>
          <!-- TERMINA MODAL PARA ELIMINAR ARCHIVO -->
        <?php } ?>
        </tbody>
      </table>

    </div><!--/.box-body-->
  </div>
</section>
<!-- ########################### TERMINA SECCION ARCHIVOS REGISTRADOS ########################### -->



<!-- ########################### INICIA SECCION DE LOS MODALS ####################### -->

<!-- INICIA MODAL PARA SUBIR ARCHIVO -->
  <div class="modal fade" id="insert_excel_info_finan" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Nuevo Documento de Información Financiera</h4>
        </div>
        <div class="modal-body"><!-- modal-body -->

          <form method="POST" action="../action/insert_excel_info_finan.php" enctype="multipart/form-data">

          <div class="form-group">
            <label>Archivo de información financiera <cite>(xls,xlsx)</cite> </label>
            <input type="file" name="archivo_excel_info_finan">
          </div>
          <div class="form-group">
            <label>Título del archivo</label>
            <input type="text" class="form-control" name="titulo_excel" placeholder="Titulo" onkeyup="javascript:this.value=this.value.toUpperCase();">
          </div>
          <div class="form-group">
            <label>Fecha correspondiente a la información financiera</label>
            <input id="" type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" name="fecha_info_finan">
          </div>

          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"> <i class="fa fa-times"></i> Cancelar</button>
          <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Registrar</button>
        </form>

        </div><!-- ./modal-body -->
      </div>
    </div>
  </div>
<!-- TERMINA MODAL PARA SUBIR ARCHIVO -->

<!-- INICIA MODAL PARA EDITAR REGISTRO DE INFORMACION FINANCIERA -->
  <div class="modal fade" id="update_excel_info_finan" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Actualización del registro</h4>
        </div>
        <div class="modal-body"><!-- modal-body -->

          <form method="POST" action="../action/update_excel_info_finan.php" enctype="multipart/form-data">

          <div class="form-group">
            <label>ID del Registro</label>
            <input type="text" class="form-control" name="id_archivo_excel" id="id_archivo_excel" readonly>
          </div>
          <div class="form-group">
            <label>Nombre del archivo actual</label>
            <input type="text" class="form-control" name="nombre_archivo_excel" id="nombre_archivo_excel" readonly>
          </div>
          <div class="form-group">
            <label>Actualizar archivo de información financiera <cite>(xls,xlsx)</cite> </label>
            <input type="file" name="update_archivo_excel_info_finan">
          </div>
          <div class="form-group">
            <label>Título del archivo</label>
            <input type="text" class="form-control" name="update_titulo_excel" id="update_titulo_excel" onkeyup="javascript:this.value=this.value.toUpperCase();">
          </div>
          <div class="form-group">
            <label>Fecha correspondiente a la información financiera</label>
            <input id="update_fecha_excel" type="date" class="form-control"  name="update_fecha_info_finan">
          </div>

          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"> <i class="fa fa-times"></i> Cancelar</button>
          <button type="submit" class="btn btn-warning btn-sm"><i class="fa fa-refresh"></i> Actualizar</button>
        </form>

        </div><!-- ./modal-body -->
      </div>
    </div>
  </div>
<!-- TERMINA MODAL PARA EDITAR REGISTRO DE INFORMACION FINANCIERA -->

<!-- ######################## TERMINA SECCION DE LOS MODALS ######################## -->



    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal -->
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<script>
function delete_reg_info_finan(id_excel){

  var confirm = window.confirm("¿Estas seguro que deseas eliminar el registro con el id "+id_excel+"?");

    if(confirm){
      var url = "../action/delete_excel_info_finan.php";
      var parametros = {
                        "delete_reg_info_finan" : id_excel
                       };

      $.ajax({
        type : "POST",
        url: url,
        data: parametros ,
        beforeSend: function () {
                        $("#resp").html("Procesando, espere por favor...");
                },
        success: function (parametros)
        {
          $("#resp").html("<b class='text-yellow'>EL ARCHIVO CON EL ID "+id_excel+" FUE ELIMINADO CORRECTAMENTE!!!</b><br>" );
          window.location.href = '../seccion/informacion_financiera.php';
        }
      });
    }else{
      alert("Operación Cancelada");
    }
}

function valores_modal(id_excel_update,nombre_excel,titulo_excel,fecha_excel)
{
  $("#id_archivo_excel").val(id_excel_update);
  $("#nombre_archivo_excel").val(nombre_excel);
  $("#update_titulo_excel").val(titulo_excel);
  $("#update_fecha_excel").val(fecha_excel);
}
</script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
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
<script>
$(document).ready(function() {
    $('#tabla_list_excel').DataTable({
      stateSave: true,
      "ordering": true,
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "language": {
          "url": "../plugins/datatables/Spanish.json"
        },
    });
} );
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
