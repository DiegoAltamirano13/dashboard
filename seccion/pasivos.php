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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '12');
if($modulos_valida == 0)
{ 
  header('Location: index.php');
}

///////////////INSTANCIAS PHPExcel
require_once('../plugins/PHPExcel.php');
require_once('../plugins/PHPExcel/Reader/Excel2007.php'); 
$obj_reader_excel = new PHPExcel_Reader_Excel2007();
/////////////////////////////////////////// 
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>

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
              <h3>0</h3>
              <p>Widgets</p>
            </div>
            <div class="icon">
              <i class="fa fa-bell-o"></i>
            </div> 
             <!-- <button type="submit" name="tipo" id="tipo" value="1" class="btn bg-aqua  btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button>  -->
          </div>
        </div>
        <!-- Widgets Numero de descargas -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">  
              <h3>0</h3>
              <p>Widgets</p>
            </div>
            <div class="icon">
              <i class="fa fa-credit-card"></i>
            </div> 
             <!-- <button type="submit" name="tipo" id="tipo" value="2" class="btn bg-green btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
          </div>
        </div>
        <!-- Widgets Cargas a tiempo -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>0</h3>
              <p>Widgets</p>
            </div> 
            <div class="icon">
              <i class="fa fa-ticket"></i>
            </div>
            <!-- <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-yellow btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button>  -->
          </div>
        </div>
        <!-- Widgets Desfasados --> 
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="small-box bg-red">
            <div class="inner">
              <h3>0</h3> 
              <p>Widgets</p>
            </div>
            <div class="icon">
              <i class="fa fa-warning"></i>
            </div>
              <!-- <button type="submit" name="tipo" id="tipo" value="3" class="btn bg-red btn-block">Más Información <i class="fa fa-arrow-circle-right"></i></button> -->
          </div>
        </div>
        <!-- Termino Widgets Desfasados -->  
      </div>
      <!-- /.row --> 
      </section><!-- Termina la seccion de los Widgets -->
<!-- ######################################### Termino de Widgets ######################################### --> 


 
 

<!-- ############################ INICIA SECCION DE LA GRAFICA OTROS POR PLAZA ############################# --> 
<section>
  <div class="box box-default">    
    <div class="box-header with-border">
      <h3 class="box-title"> </h3> 
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body"><!--box-body-->  
      
       
    </div><!--/.box-body--> 
  </div> 
</section> 
<!-- ########################### TERMINA SECCION DE LA GRAFICA OTROS POR PLAZA ########################### -->


 

      

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
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>  
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