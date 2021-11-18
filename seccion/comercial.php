<?php
ini_set('display_errors', false);
if( $_SERVER['REQUEST_METHOD'] == 'POST' /* isset($_POST["co_plaza"]) || isset($_POST["fec_per_co"]) || isset($_POST["grafica_co_pros"]) || isset($_POST["status_pros_co_graf"]) || isset($_POST["promotor_co"]) */ )  // $_SERVER['REQUEST_METHOD'] == 'POST'
{
  header("location:comercial.php");
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
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '7');
if($modulos_valida == 0)
{
  header('Location: index.php');
}
/////////// SESIONES DE COERCIAL //////////////////
if(isset($_POST['co_plaza']) )
    $_SESSION['co_plaza'] = $_POST['co_plaza'];
    $co_plaza = $_SESSION['co_plaza'];

if( isset($_POST['co_plaza_nombre']) )
    $_SESSION['co_plaza_nombre'] = $_POST["co_plaza_nombre"];
    $co_plaza_nombre = $_SESSION['co_plaza_nombre'];
/////////// SESIONES DE GRAFICA SELECCIONA //////////////////
if(isset($_POST['grafica_co_pros']))
    $_SESSION['grafica_co_pros'] = $_POST['grafica_co_pros'];
    $grafica_co_pros = $_SESSION['grafica_co_pros'];
/////////// SESIONES DE TIPO DE PROSPECTO //////////////////
if(isset($_POST['t_prospecto_co']))
    $_SESSION['t_prospecto_co'] = $_POST['t_prospecto_co'];
    $t_prospecto_co = $_SESSION['t_prospecto_co'];
/////////// SESIONES DE TIPO DE SERVICIO //////////////////
if(isset($_POST['t_servicio_co']))
    $_SESSION['t_servicio_co'] = $_POST['t_servicio_co'];
    $t_servicio_co = $_SESSION['t_servicio_co'];
/////////// SESIONES DE ID PROMOTOR //////////////////
if(isset($_POST['promotor_co']))
    $_SESSION['promotor_co'] = $_POST['promotor_co'];
    $promotor_co = $_SESSION['promotor_co'];
/////////// SESIONES DE ANIO COMERCIAL //////////////////
if ( $_SESSION['anio_co'] == false)
{
  $_SESSION['anio_co'] = date('Y');
  $anio_co = $_SESSION['anio_co'];
}else{
if(isset($_POST['anio_co']))
    $_SESSION['anio_co'] = $_POST['anio_co'];
    $anio_co = $_SESSION['anio_co'];
}
/////////// SESIONES DE RANGO DE FECHAS COMERCIAL //////////////////
// Rango inicio
if(isset($_POST['fec_ini_co']))
    $_SESSION['fec_ini_co'] = $_POST['fec_ini_co'];
    $fec_ini_co = $_SESSION['fec_ini_co'];
// Rango Fin
if(isset($_POST['fec_fin_co']))
    $_SESSION['fec_fin_co'] = $_POST['fec_fin_co'];
    $fec_fin_co = $_SESSION['fec_fin_co'];

/////////// SESIONES DE TOP PROMOTOR SELECCIONA //////////////////
if(isset($_POST['co_top_promo']))
    $_SESSION['co_top_promo'] = $_POST['co_top_promo'];
    $co_top_promo = $_SESSION['co_top_promo'];
/////////// SESIONES PARA LA GRAFICA DE PROSPECTOS //////////////////
if ( $_SESSION['status_pros_co_graf'] == false ) {

  $_SESSION['status_pros_co_graf'] = "0,1,2";
  $status_pros_co_graf = $_SESSION['status_pros_co_graf'];

}else{
 if(isset($_POST['status_pros_co_graf']))
    $_SESSION['status_pros_co_graf'] = $_POST['status_pros_co_graf'];
    $status_pros_co_graf = $_SESSION['status_pros_co_graf'];
}

//////////////////////
include_once '../class/Comercial.php';
$ins_objeto_comercial = new Comercial();


?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>

<!-- *********** INICIA INCLUDE CSS *********** -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../plugins/datatables/jquery.dataTables_themeroller.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<!-- <link rel="stylesheet" href="../plugins/datatables/jquery.dataTables.min.css">  -->

<style type="text/css" media="screen">
 div.dataTables_wrapper {
      /*width: 850px;
      margin: 0 auto;*/
       width: 95%;
  }
</style>
<!-- *********** TERMINA INCLUDE CSS *********** -->
<!-- XXXXXXXXXXXX INICIA TITULO DEL DASHBOARD XXXXXXXXXXXX -->
<?php

  if ($anio_co == true ){
    if ($fec_ini_co == true && $fec_fin_co == true){
      $titulo_fecha = "".$fec_ini_co."/".$fec_fin_co."" ;
    }else{
      $titulo_fecha = $anio_co ;
    }
  }
  if ( $t_prospecto_co == true){
      $titulo_tipo_prospecto = "Tipo de Prospecto(".$t_prospecto_co.")"  ;
  }
  if ( $t_servicio_co == true){
      $titulo_tipo_servicio = "Tipo de Servicios(".$t_servicio_co.")"  ;
  }
  if ( $promotor_co == true){
      $titulo_prospecto = "Promotor(".$promotor_co.")" ;
  }

  $titulo_prospecto_co = $co_plaza_nombre." ".$titulo_tipo_prospecto." ".$titulo_tipo_servicio." ".$titulo_prospecto." ".$titulo_fecha;
?>
<!-- XXXXXXXXXXXX TERMINA TITULO DEL DASHBOARD XXXXXXXXXXXX -->
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>

        Dashboard
        <small>Comercial</small>
      </h1>
    </section>
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->


<!-- ############################ INICIA SECCION BOTON INICIO Y FILTROS ############################# -->
<section>
   <!-- INICIA CODE BOTON INICIO Y FILTRO -->
    <div class="box box-default collapsed-box">
      <div class="box-header with-border">
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
        <?php if ($grafica_co_pros == true) { ?>
        <!-- <input type="hidden" name="anio_co" value="<?= date('Y') ?>"> -->
        <input type="hidden" name="grafica_co_pros" value="">
        <input type="hidden" name="co_top_promo" value="">
        <input type="hidden" name="co_plaza_nombre" value="">
        <input type="hidden" name="t_prospecto_co" value="">
        <input type="hidden" name="t_servicio_co" value="">
        <input type="hidden" name="promotor_co" value="">
        <input type="hidden" name="nombre_promotor_co" value="">
        <!-- <input type="hidden" name="fec_ini_co" value="">
        <input type="hidden" name="fec_fin_co" value="">  -->
        <button type="submit" name="co_plaza" value="" class="btn bg-blue btn-xs"><i class="fa fa-reply"></i> Regresar</button>
        <?php } ?>
        <button type="button" class="btn bg-blue btn-xs" data-widget="collapse"><i class="fa fa-filter"></i> Filtrar
        </button>
        <div class="small">
          <code>Filtros utilizados(
          <?php
          if ($co_plaza==true){
            echo " |PLAZA| ";
          }
          if ($t_prospecto_co==true){
            echo " |TIPO DE PROSPECTO| ";
          }
          if ($t_servicio_co==true){
            echo " |TIPO DE SERVICIO| ";
          }
          if ($promotor_co==true){
            echo " |PROMOTOR| ";
          }
          if ($anio_co==true){
            if($fec_ini_co == true && fec_fin_co){
            echo " |FECHA PERSONALIZADA| ";
            }else{
            echo " |AÑO| ";
            }
          }
          ?>
          )</code>
        </div>
        </form>
        <!-- /.box-tools -->
      </div>
      <!-- /.box-header -->
      <div class="box-body">

      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
      <div class="row">
      <?php if ($grafica_co_pros <> 3 ){ ?>
      <div class="col-md-3">
        <div class="form-group">
          <label>Tipo de Prospecto</label>
          <select class="form-control select2" name="t_prospecto_co" style="width: 100%;">
          <?php
            echo '<option selected="selected" value="">NULL</option>' ;
            switch ($t_prospecto_co) {
              case 1:
                echo '<option selected="selected" value="1">ESTACIONAL</option>' ;
                break;
              case 2:
                echo '<option selected="selected" value="2">HABITUAL</option>' ;
                break;
              case 3:
                echo '<option selected="selected" value="3">EVENTUAL</option>' ;
                break;
              case 4:
                echo '<option selected="selected" value="4">PROYECTO</option>' ;
                break;
              case 5:
                echo '<option selected="selected" value="5">OCASIONAL</option> ' ;
                break;
            }
            echo '<option value="1">ESTACIONAL</option>' ;
            echo '<option value="2">HABITUAL</option>' ;
            echo '<option value="3">EVENTUAL</option>' ;
            echo '<option value="4">PROYECTO</option>' ;
            echo '<option value="5">OCASIONAL</option> ' ;
          ?>
          </select>
        </div>
      </div>
      <?php } ?>
      <?php if ($grafica_co_pros <> 3 ){ ?>
      <div class="col-md-3">
        <div class="form-group">
          <label>Tipo de Servicio</label>
          <select class="form-control select2" name="t_servicio_co" style="width: 100%;">
          <?php
            echo '<option selected="selected" value="">NULL</option>' ;
            switch ($t_servicio_co) {
              case 1:
                echo '<option selected="selected" value="1">DIRECTA FISCAL</option>' ;
                break;
              case 2:
                echo '<option selected="selected" value="2">DIRECTA NACIONAL</option>' ;
                break;
              case 3:
                echo '<option selected="selected" value="3">HABILITADA NACIONAL</option>' ;
                break;
              case 4:
                echo '<option selected="selected" value="4">HABILITADA FISCAL</option> ' ;
                break;
            }
            echo '<option value="1">DIRECTA FISCAL</option>' ;
            echo '<option value="2">DIRECTA NACIONAL</option>' ;
            echo '<option value="3">HABILITADA NACIONAL</option>' ;
            echo '<option value="4">HABILITADA FISCAL</option> ' ;
          ?>
          </select>
        </div>
      </div>
      <?php } ?>
      <?php if ($grafica_co_pros <> 3 ){ ?>
      <div class="col-md-3">
        <div class="form-group">
          <label>Promotor</label>
          <select class="form-control select2" name="promotor_co" style="width: 100%;">
          <?php
              echo '<option selected="selected" value="">NULL</option>' ;
            switch (true) {
              case ($promotor_co == true):
                echo '<option selected="selected" value="'.$promotor_co.'">'.$promotor_co.'</option>' ;
                break;
            }
            $select_promotor = $ins_objeto_comercial->select_promotor($co_plaza);
            for ($i=0; $i <count($select_promotor) ; $i++) {
              switch ($select_promotor[$i]["STATUS"]) {
                case 1:
                  $info_status = "(ACTIVO)" ;
                  break;
                case 9:
                  $info_status = "(BAJA)" ;
                  break;
                default:
                  $info_status = "(NULL)" ;
                  break;
              }
              echo '<option value="'.$select_promotor[$i]["ID_PROMOTOR"].'">'.$select_promotor[$i]["ID_PROMOTOR"].'-'.$select_promotor[$i]["NOMBRE"].' '.$select_promotor[$i]["APE_PAT"].' '.$select_promotor[$i]["APE_MAT"].' '.$info_status.'</option>';
            }
          ?>
          </select>
        </div>
      </div>
      <?php } ?>
      <div class="col-md-3">
        <div class="form-group">
        <ul class="nav nav-tabs" id="myTab_anio_fec_per_pros">
          <a id="tab_anio_pros_co" data-toggle="tab" href="#anio_prospecto_co">
          <button type="submit" name="fec_per_co" id="reset_fec_per_co_pros" value="1" class="btn-link btn-xs">(<i class="fa fa-slack"></i> Año)</button>
          </a>
          <a id="tab_fec_per_pros_co" data-toggle="tab" href="#fec_per_prospecto_co">
          <button type="submit" name="fec_per_co" value="1" class="btn-link btn-xs">(<i class="fa fa-calendar"></i> Fecha personalizada)</button>
          </a>
        </ul>
          <!-- INICIA CODE FECHA PERSONALIZADA -->
          <div class="tab-content">
            <div id="fec_per_prospecto_co" class="tab-pane fade">
             <!--  -->
              <div class="input-group" id="fec_rango_co">
                <div class="input-group-addon">
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_ini_co" id="fec_ini_co" value="<?= $fec_ini_co ?>" readonly>
                <div class="input-group-addon">
                  <i class="fa fa-calendar-plus-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="fec_fin_co" id="fec_fin_co" value="<?= $fec_fin_co ?>" readonly>
              </div>
             <!--  -->
            </div>
            <div id="anio_prospecto_co" class="tab-pane fade in active">
             <!--  -->
              <select class="form-control select2" name="anio_co" style="width: 100%;">
              <?php
                $select_anio_co = $ins_objeto_comercial->select_anio_co();

                  switch (true) {
                    case ($anio_co==true):
                      echo '<option selected="selected" value="'.$anio_co.'">'.$anio_co.'</option>';
                      break;
                  }
                  for ($i=0; $i <count($select_anio_co) ; $i++) {
                    echo '<option value="'.$select_anio_co[$i]["ANIO"].'">'.$select_anio_co[$i]["ANIO"].'</option>';
                  }
              ?>
              </select>
             <!--  -->
            </div>
          </div>
         <!-- TERMINA CODE FECHA PERSONALIZADA -->
      </div>
      </div>
      <!-- Muestra filtro de plaza si selecciona la grafica por año -->
      <div class="col-md-3">
        <div class="form-group">
          <label>Plaza</label>
          <?php
          $selected_plaza = $co_plaza;
          ?>
          <select onclick="return plaza_nombre();"  class="form-control" name="co_plaza" id="selec_plaza_nombre" style="width: 100%;">
          <?php
            echo '<option selected="selected" value="">GENERAL</option>' ;
            switch ($co_plaza) {
              case 3:
                echo '<option selected="selected" value="3">CÓRDOBA</option>' ;
                break;
              case 4:
                echo '<option selected="selected" value="4">MÉXICO</option>' ;
                break;
              case 5:
                echo '<option selected="selected" value="5">GOLFO</option>' ;
                break;
              case 6:
                echo '<option selected="selected" value="6">PENINSULA</option> ' ;
                break;
              case 7:
                echo '<option selected="selected" value="7">PUEBLA</option>' ;
                break;
              case 8:
                echo '<option selected="selected" value="8">BAJIO</option>' ;
                break;
              case 17:
                echo '<option selected="selected" value="17">OCCIDENTE</option>' ;
                break;
              case 18:
                echo '<option selected="selected" value="18">NORESTE</option>' ;
                break;
              case 21:
                echo '<option selected="selected" value="21">PACIFICO NORTE</option>' ;
                break;
              case 23:
                echo '<option selected="selected" value="23">LEON</option>' ;
                break;
            }

            echo '<option value="3">CÓRDOBA</option>' ;
            echo '<option value="4">MÉXICO</option>' ;
            echo '<option value="5">GOLFO</option>' ;
            echo '<option value="6">PENINSULA</option> ' ;
            echo '<option value="7">PUEBLA</option>' ;
            echo '<option value="8">BAJIO</option>' ;
            echo '<option value="17">OCCIDENTE</option>' ;
            echo '<option value="18">NORESTE</option>' ;
            echo '<option value="21">PACIFICO NORTE</option>' ;
            //echo '<option value="23">LEON</option>' ;
          ?>
          </select>
          <input type="hidden" id="co_plaza_nombre" name="co_plaza_nombre">
          <script type="text/javascript">
          function plaza_nombre()
          {
          var obj=document.getElementById("selec_plaza_nombre");
          val=obj.options[obj.selectedIndex].text;
          document.getElementById("co_plaza_nombre").value=val;
          }
          </script>
        </div>
      </div>



      <div class="col-md-1">
      <BR>
          <button type="submit" class="btn btn-primary btn-sm">Ok</button>
      </div>


      </form>

      </div>
      <!-- /.box-body -->
    </div>
    <!-- TERMINA CODE BOTON INICIO Y FILTRO -->
</section>
<!-- ########################### TERMINA SECCION BOTON INICIO Y FILTROS ########################### -->

<!-- ######################################## Inicio de Widgets ######################################### -->
<?php if ($grafica_co_pros <=2 ) { ?>
    <section>
      <div class="row">

      <h4 class="content-header text-blue text-center"><i class="fa fa-users"></i>  Prospectos <?= $titulo_prospecto_co ?>  </h4><br>



      <!-- Widgets Promotores -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon bg-aqua"><i class="fa fa-briefcase"></i></span>
              <div class="info-box-content bg-aqua">
              <span class="info-box-text"><i class="fa fa-fw fa-star"></i> TOP PROMOTORES </span>
                <!-- <span class="info-box-number">¿?</span>  -->
                <span class="progress-description">
                  <b>Mejor cierre:</b>
                  <?php
                  $cierre_promotor = $ins_objeto_comercial->cierre_promotor($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co);
                    for ($i=0; $i <1 ; $i++) {
                      echo "<small>".$cierre_promotor[0]["PROMO_NOMBRE"]." ".$cierre_promotor[0]["PROMO_APE_PAT"]." ".$cierre_promotor[0]["PROMO_APE_MAT"]."</small> ";
                    }
                  ?>
                </span>
                <span class="progress-description">
                  <b>Mayor prospecto:</b>
                  <?php
                  $prospectos_promotor = $ins_objeto_comercial->prospectos_promotor($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co);
                    for ($i=0; $i <1 ; $i++) {
                      echo "<small>".$prospectos_promotor[0]["PROMO_NOMBRE"]." ".$prospectos_promotor[0]["PROMO_APE_PAT"]." ".$prospectos_promotor[0]["PROMO_APE_MAT"]."</small> ";
                    }
                  ?>
                </span>
                <span class="progress-description">
                  <b>Mayor cliente:</b>
                  <?php
                  $clientes_promotor = $ins_objeto_comercial->clientes_promotor($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co);
                    for ($i=0; $i <1 ; $i++) {
                      echo "<small>".$clientes_promotor[0]["PROMO_NOMBRE"]." ".$clientes_promotor[0]["PROMO_APE_PAT"]." ".$clientes_promotor[0]["PROMO_APE_MAT"]."</small> ";
                    }
                  ?>
                </span>
              </div>
            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
            <button type="submit" name="grafica_co_pros" value="3" class="btn bg-aqua-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
      <!-- Widgets Prospectos -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon bg-green"><i class="fa fa-users""></i></span>
              <div class="info-box-content bg-green">
                <span class="info-box-number">
                  <?php
                  $win_pros_fac = $ins_objeto_comercial->t_pros_fact($co_plaza,$t_prospecto_co,$t_servicio_co,$promotor_co,$anio_co,$fec_ini_co,$fec_fin_co);
                  for ($i=0; $i <count($win_pros_fac) ; $i++) {
                    echo  "<h3>".$win_pros_fac[$i]["T_PROSPECTOS"]."</h3>" ;
                  }
                  ?>
                </span>

                <span class="progress-description"><b>Prospectos Activos</b></span>
              </div>
            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
             <button type="submit" name="grafica_co_pros" value="2" class="btn bg-green-active  btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
             </form>
          </div>
        </div>
        <!-- Widgets Prospectos cerrados -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon bg-yellow"><i class="fa fa-user-plus""></i></span>
              <div class="info-box-content bg-yellow">
                <span class="info-box-number">
                  <?php
                    $win_pros_cer_can = $ins_objeto_comercial->t_pros_cer_can($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co,$t_prospecto_co,$t_servicio_co,$promotor_co);
                    for ($i=0; $i <count($win_pros_cer_can) ; $i++) {
                      echo "<h3>".$win_pros_cer_can[$i]["T_PROS_CERRADOS"]."</h3>";
                    }
                  ?>
                </span>

                <span class="progress-description"><b>Prospectos Cerrados</b></span>
              </div>
            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
            <button type="submit" name="grafica_co_pros" value="10" class="btn bg-yellow-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>
        <!-- Widgets Prospectos cancelados -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon bg-red"><i class="fa fa-user-times"></i></span>
              <div class="info-box-content bg-red">
                <span class="info-box-number">
                  <?php
                  for ($i=0; $i <count($win_pros_cer_can) ; $i++) {
                      echo "<h3>".$win_pros_cer_can[$i]["T_PROS_CANCELADOS"]."</h3>";
                    }
                  ?>
                </span>

                <span class="progress-description"><b>Prospectos Cancelados</b></span>
              </div>
            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
             <button type="submit" name="grafica_co_pros" value="4" class="btn bg-red-active btn-block">VER <i class="fa fa-arrow-circle-right"></i></button>
            </form>
          </div>
        </div>


        <!-- Widgets Facturación Estimada -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon bg-aqua"><i class="fa fa-money"></i><!-- <i class="fa fa-dribbble"></i> --></span>
            <div class="info-box-content bg-aqua">
              <span class="info-box-text">Facturación Estimada</span>
               <?php
              for ($i=0; $i <count($win_pros_fac) ; $i++) {
                echo "<h2>$".number_format($win_pros_fac[$i]["FAC_ESTIMADA"])."</h2>";
              }
              ?>
            </div>
          </div>
        </div>
        <!-- Widgets Servicios Directos -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon bg-green"><img src="../dist/img/ser_dierectos.png"><!-- <i class="fa fa-dribbble"></i> --></span>
            <div class="info-box-content bg-green">
              <span class="info-box-text">Servicios Directos</span>
              <?php
                $win_ser_directos = $ins_objeto_comercial->t_ser_directos($co_plaza,$t_prospecto_co,$t_servicio_co,$promotor_co,$anio_co,$fec_ini_co,$fec_fin_co);
                for ($i=0; $i <count($win_ser_directos) ; $i++) {
              ?>
                  <span class="info-box-number"><?=($win_ser_directos[$i]["DIRECTA_F"]+$win_ser_directos[$i]["DIRECTA_N"])?></span>
                  <span class="progress-description">
                    <b>Nacional:</b> <?=$win_ser_directos[$i]["DIRECTA_N"]?>
                  </span>
                  <span class="progress-description">
                    <b>Fiscal:</b> <?=$win_ser_directos[$i]["DIRECTA_F"]?>
                  </span>
              <?php } ?>
            </div>
          </div>
        </div>
        <!-- Widgets Servicios Habilitados -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon bg-yellow"><!-- <i class="fa fa-street-view"></i> --><img src="../dist/img/ser_habilitados.png"></span>
            <div class="info-box-content bg-yellow">
              <span class="info-box-text">Servicios Habilitados</span>
              <?php
                $win_ser_habilitados = $ins_objeto_comercial->t_ser_habilitados($co_plaza,$t_prospecto_co,$t_servicio_co,$promotor_co,$anio_co);
                for ($i=0; $i <count($win_ser_habilitados) ; $i++) {
              ?>
                  <span class="info-box-number"><?=($win_ser_habilitados[$i]["HABILITADA_F"]+$win_ser_habilitados[$i]["HABILITADA_N"])?></span>
                  <span class="progress-description">
                    <b>Nacional:</b> <?=$win_ser_habilitados[$i]["HABILITADA_N"]?>
                  </span>
                  <span class="progress-description">
                    <b>Fiscal:</b> <?=$win_ser_habilitados[$i]["HABILITADA_F"]?>
                  </span>
              <?php } ?>
            </div>
          </div>
        </div>
        <!-- Termino Widgets Servicios Habilitados -->

      </div>
      </section>


<?php } ?>
<!-- ######################################### Termino de Widgets ######################################### -->



<!-- ############################ INICIA SECCION DE LA GRAFICA PROSPECTOS ############################# -->
<?php
if ($grafica_co_pros == null ){
?>
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title"> </h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body"> <!-- box-body -->

    <?php
      switch ($status_pros_co_graf) {
        case "0,1,2":
          $titulo_grafica = "activos";
          break;
        case "3":
          $titulo_grafica = "cerrados";
          break;
        case "0,1,2,3":
          $titulo_grafica = "activos y cerrados";
          break;
      }
    ?>
      <h4 class="content-header text-blue text-center"><i class="fa fa-pie-chart"></i> Porcentaje de prospectos <?= $titulo_grafica." ".$titulo_prospecto_co ?></h4><hr>

    <div class="row">

    <div class="col-md-6">
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
    <button type="submit" name="status_pros_co_graf" value="0,1,2,3" class="btn bg-blue btn-xs"><i class="fa fa-users"></i> Todos los prospectos</button>
    <button type="submit" name="status_pros_co_graf" value="0,1,2" class="btn bg-blue btn-xs"><i class="fa fa-user"></i> Prospectos activos</button>
    <button type="submit" name="status_pros_co_graf" value="3" class="btn bg-blue btn-xs"><i class="fa fa-user-plus"></i> Prospectos cerrados</button>
    </form>
<?php /*echo $status_pros_co_graf */?>
      <div id="graf_donut_prospectos" style="height: 380px;"></div>
    </div>

    <div class="col-md-6">
    <div id="bar-chart-prospectos" style="height: 300px; "></div>

    </div>

    </div>

    </div><!-- /.box-body -->
  </div>
</section>
<?php } ?>
<!-- ########################### Termina SECCION DE LA GRAFICA PROSPECTOS ########################### -->




<!-- ############################ INICIA SECCION GRAFICA 3 PROMOTORES ############################# -->
<?php
 if ($grafica_co_pros == 3){//ABRE IF PARA MOSTRAR SECCION DE PROMOTORES
?>
<section>
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">PROMOTORES</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body"> <!-- box-body -->

    <h4 class="content-header text-blue text-center"><i class="ion-ribbon-a"></i> TOP <code><?= $titulo_prospecto_co ?></code>  </h4><hr>

<!--*/*/*/*/*/*/*/* INICIA TABLAS */*/*/*/*/*/*/*  -->
  <div class="row">
    <!-- INICIA TABLA DE PROMOTOR MEJOR CIERRE -->
    <div class="col-md-4 table-responsive">
      <h5 class="content-header text-blue text-center"><i class="fa fa-bar-chart"></i> MEJOR PORCENTAJE DE CIERRE </h5><hr>
      <table id="tabla_top_cierre" class="display compact table table-bordered table-striped hover" cellspacing="0">

      <thead>
        <tr>
          <th class="small">#</th>
          <th class="small">CIERRE</th>
          <th class="small" title="PROSPECTOS">PROS.</th>
          <th class="small">PROMOTOR</th>
          <th class="small">PLAZA</th>
          <th class="small">VER</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $cierre_promotor = $ins_objeto_comercial->cierre_promotor($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co);
          for ($i=0; $i <count($cierre_promotor) ; $i++) {
        ?>
        <tr>
          <th class='small'><?= $i+1 ?></th>
          <td class='small'><span class="badge bg-teal"><?php $porciento_res = ($cierre_promotor[$i]["PORCENTAJE"]*100)/$cierre_promotor[$i]["TOTAL_PROSPECTOS"]; echo round($porciento_res)."%"  ?></span></td>
          <td class='small'><span class="badge bg-green"><?= $cierre_promotor[$i]["TOTAL_PROSPECTOS"] ?></span></td>
          <td class='small'><?= "<span class='text-muted'>(".$cierre_promotor[$i]["ID_PROMOTOR"].")</span> ".$cierre_promotor[$i]["PROMO_NOMBRE"]." ".$cierre_promotor[$i]["PROMO_APE_PAT"]." ".$cierre_promotor[$i]["PROMO_APE_MAT"] ?></td>
          <td class='small'><?= $cierre_promotor[$i]["PLAZA"] ?></td>
          <!-- CODE STATUS -->
          <td class="small">
          <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
          <input type="hidden" name="co_top_promo" value="3">
          <button type="submit" name="promotor_co" value="<?=$cierre_promotor[$i]["ID_PROMOTOR"]?>" class=" label btn bg-blue btn-xs"><i class="fa fa-search"></i></button>
          </form>
          </td>
          <!-- FIN CODE STATUS -->
        </tr>
        <?php }  ?>
      </tbody>
      </table>
    </div>
    <!-- TERMINA TABLA DE PROMOTOR MEJOR CIERRE -->


    <!-- INICIA TABLA DE PROMOTOR MAYOR PROSPECTOS -->
    <div class="col-md-4 table-responsive">
    <h5 class="content-header text-blue text-center"><i class="fa fa-users"></i> MAYOR NÚMERO DE PROSPECTOS ACTIVOS </h5><hr>
      <table id="tabla_top_pros" class="display compact table table-bordered table-striped hover" cellspacing="0">

      <thead>
        <tr>
          <th class="small">#</th>
          <th class="small">PROSPECTOS</th>
          <th class="small">PROMOTOR</th>
          <th class="small">PLAZA</th>
          <th class="small">VER</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $prospectos_promotor = $ins_objeto_comercial->prospectos_promotor($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co);
          for ($i=0; $i <count($prospectos_promotor) ; $i++) {
        ?>
        <tr>
          <th class='small'><?= $i+1 ?></th>
          <td class='small'><span class="badge bg-teal"><?= $prospectos_promotor[$i]["T_PROSPECTOS"] ?></span></td>
          <td class='small'><?= "<span class='text-muted'>(".$prospectos_promotor[$i]["ID_PROMOTOR"].")</span> ".$prospectos_promotor[$i]["PROMO_NOMBRE"]." ".$prospectos_promotor[$i]["PROMO_APE_PAT"]." ".$prospectos_promotor[$i]["PROMO_APE_MAT"] ?></td>
          <td class='small'><?= $prospectos_promotor[$i]["PLAZA"] ?></td>
          <!-- CODE STATUS -->
          <td class="small">
          <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
          <input type="hidden" name="co_top_promo" value="2">
          <button type="submit" name="promotor_co" value="<?=$prospectos_promotor[$i]["ID_PROMOTOR"]?>" class=" label btn bg-blue btn-xs"><i class="fa fa-search"></i></button>
          </form>
          </td>
          <!-- FIN CODE STATUS -->
        </tr>
        <?php }  ?>
      </tbody>
      </table>
    </div>
    <!-- TERMINA TABLA DE PROMOTOR MAYOR PROSPECTOS -->
    <!-- INICIA TABLA DE PROMOTOR MAYOR CLIENTE -->
    <div class="col-md-4 table-responsive">
    <h5 class="content-header text-blue text-center"><i class="fa fa-briefcase"></i> MAYOR NUMERO DE PROSPECTOS CERRADOS </h5><hr>
      <table id="tabla_top_cliente" class="display compact table table-bordered table-striped hover" cellspacing="0">

      <thead>
        <tr>
          <th class="small">#</th>
          <th class="small">CLIENTES</th>
          <th class="small">PROMOTOR</th>
          <th class="small">PLAZA</th>
          <th class="small">VER</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $clientes_promotor = $ins_objeto_comercial->clientes_promotor($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co);
          for ($i=0; $i <count($clientes_promotor) ; $i++) {
        ?>
        <tr>
          <th class='small'><?= $i+1 ?></th>
          <td class='small'><span class="badge bg-teal"><?= $clientes_promotor[$i]["T_CLIENTES"] ?></span></td>
          <td class='small'>
          <?= "<span class='text-muted'>(".$clientes_promotor[$i]["ID_PROMOTOR"].")</span>"?>
          <?=$clientes_promotor[$i]["PROMO_NOMBRE"]." ".$clientes_promotor[$i]["PROMO_APE_PAT"]." ".$clientes_promotor[$i]["PROMO_APE_MAT"] ?></td>
          <td class='small'><?= $clientes_promotor[$i]["PLAZA"] ?></td>
          <!-- CODE STATUS -->
          <td class="small">
          <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
          <input type="hidden" name="co_top_promo" value="3">
          <button type="submit" name="promotor_co" value="<?=$clientes_promotor[$i]["ID_PROMOTOR"]?>" class=" label btn bg-blue btn-xs"><i class="fa fa-search"></i></button>
          </form>
          </td>
          <!-- FIN CODE STATUS -->
        </tr>
        <?php }  ?>
      </tbody>
      </table>
    </div>
    <!-- TERMINA TABLA DE PROMOTOR MAYOR CLIENTE -->
  </div>
<!--*/*/*/*/*/*/*/* TERMINA TABLAS */*/*/*/*/*/*/*  -->

    </div><!-- /.box-body -->
  </div>
</section>
<?php }//CIERRA IF PARA MOSTRAR SECCION DE PROMOTORES ?>
<!-- ########################### Termina SECCION GRAFICA 3 PROMOTORES ########################### -->
<?php if ( $grafica_co_pros == 10 && $promotor_co == false )  { ?>
<section>
  <div class="box box-default">
    <!-- <div class="box-header with-border">
      <h3 class="box-title">PROMOTORES</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div> -->
    <div class="box-body"> <!-- box-body -->
    <?php
      switch ($grafica_co_pros) {
        case 3:
          if ($co_top_promo == 3){
          echo '<h5 class="content-header text-blue text-center"><i class="fa fa-table"></i> TABLA DE CLIENTES '.$co_plaza_nombre.' '.$titulo_fecha.'</h5><hr>';
          }
          break;
        case 4:
          echo '<h5 class="content-header text-blue text-center"><i class="fa fa-table"></i> TABLA DE PROSPECTOS CERRADOS '.$co_plaza_nombre.' '.$titulo_fecha.'</h5><hr>';
          break;
        case 5:
          echo '<h5 class="content-header text-blue text-center"><i class="fa fa-table"></i> TABLA DE PROSPECTOS CANCELADOS '.$co_plaza_nombre.' '.$titulo_fecha.'</h5><hr>';
          break;
        case 10:
          echo '<h5 class="content-header text-blue text-center"><i class="fa fa-table"></i> TABLA DE PROSPECTOS CERRADOS '.$co_plaza_nombre.' '.$titulo_fecha.'</h5><hr>';
          break;
        default:
          echo '<h5 class="content-header text-blue text-center"><i class="fa fa-table"></i> TABLA DE PROSPECTOS '.$co_plaza_nombre.' '.$titulo_fecha.'</h5><hr>';
          break;
      }
    ?>


    <!-- INICIA TABLA DE PROSPECTOS -->
    <br>
    <div class="table-responsive">
      <table id="tabla_prospecto" class="table compact table-bordered table-striped hover" cellspacing="0">
        <thead>
            <tr>
                <th class="small">PROSPECTO</th>
                <th class="small">STATUS/PROSPECTO</th>
                <?php if ($grafica_co_pros == 4){ ?>
                <th class="small">STATUS/CLIENTE</th>
                <?php } ?>
                <th class="small">PLAZA</th>
                <th class="small" title="FACTURACIÓN ESTIMADA">FACTURACIÓN ESTIMADA</th>
                <th class="small" title="TIPO DE SERVICIO">TIPO DE SERVICIO</th>
                <th class="small" title="TIPO DE PROSPECTO">TIPO DE PROSPECTO</th> <!-- 1: Habitual, 2=Eventual, 3=Proyecto, 4=Ocacional -->
                <th class="small" title="EJECUTIVO COMERCIAL">PROMOTOR</th>
                <th class="small" title="FECHA DE REGISTRO" style="width: 80px">REGISTRO</th>
                <?php if ($grafica_co_pros == 4 || $grafica_co_pros == 3 && $co_top_promo == 3){ ?>
                <th class="small" style="width: 80px">CIERRE</th>
                <th class="small">TIEMPO TRANSCURRIDO</th>
                <?php } ?>
                <?php if ($grafica_co_pros == 1 || $grafica_co_pros == 3 && $co_top_promo <= 2){ ?>
                <?php if ($co_top_promo <> 1 ) {?>
                <th class="small">TIEMPO TRANSCURRIDO</th>
                <?php } ?>
                <th class="small">PORCENTAJE DE CIERRE</th>
                <?php } ?>
                <th class="small">DETALLES</th>
                <th class="small">FACTURACIÓN</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $tabla_prospectos = $ins_objeto_comercial->tabla_prospectos($co_plaza,$t_prospecto_co,$t_servicio_co,$promotor_co,$anio_co,$id_prospecto_co,$grafica_co_pros,$co_top_promo,$fec_ini_co,$fec_fin_co,$status_pros_co_graf);
          for ($i=0; $i <count($tabla_prospectos) ; $i++) {
        ?>

        <?php if ($grafica_co_pros == 10) {
          #echo "<h3>CERRADOS PRUEBA</h3>";
        } ?>

            <tr>
                <td class="small"><span class="text-muted"><?="(".$tabla_prospectos[$i]["ID_PROSPECTO"].") </span>".$tabla_prospectos[$i]["PROSPECTO"]?></td>
                <!-- INICIA CODE STATUS PROSPECTO -->
                <?php
                    switch ($tabla_prospectos[$i]["STATUS_PROS"]) {
                      case 0:
                        echo '<td class="small"><span class="badge bg-green">PRIMER CONTACTO</span></td>' ;
                        break;
                      case 1:
                        echo '<td class="small"><span class="badge bg-yellow">COTIZADO</span></td>' ;
                        break;
                      case 2:
                        echo '<td class="small"><span class="badge badge-primary">DOCUMENTACION</span></td>' ;
                        break;
                      case 3:
                        echo '<td class="small"><span class="badge bg-green">CLIENTE</span></td>' ;
                        break;
                      case 4:
                          echo '<td class="small"><span class="badge bg-red">BAJA</span></td>' ;
                          break;
                      default:
                        echo '<td class="small"><span class="badge bg-gray">NO DEFINIDO</span></td>' ;
                        break;
                    }
                ?>
                <!-- TERMINA CODE STATUS PROSPECTO -->
                <!-- INICIA CODE STATUS DEL CLIENTE -->
                <?php
                if ($grafica_co_pros == 4){
                    switch ($tabla_prospectos[$i]["STATUS_CLI"]) {
                      case 1:
                        echo '<td class="small"><span class="badge bg-green">ACTIVO</span></td>' ;
                        break;
                      case 2:
                        echo '<td class="small"><span class="badge bg-red">INACTIVO</span></td>' ;
                        break;
                      default:
                        echo '<td class="small"><span class="badge bg-gray">NO DEFINIDO</span></td>' ;
                        break;
                    }
                  }
                ?>
                <!-- TERMINA CODE STATUS DEL CLIENTE -->
                <td class="small"><?=$tabla_prospectos[$i]["PLAZA"]?></td>
                <td class="small"><?=$tabla_prospectos[$i]["FAC_ESTIMADA"]?></td>
                <td class="small"><?=$tabla_prospectos[$i]["TIPO_SERVICIO"]?></td>
                <td class="small"><?=$tabla_prospectos[$i]["TIPO_PROS"]?></td>
                <td class="small"><?=$tabla_prospectos[$i]["NOM_PROM"]." ".$tabla_prospectos[$i]["APEPAT_PROM"]." ".$tabla_prospectos[$i]["APEMAT_PROM"]?></td>
                <td class="small"><?=$tabla_prospectos[$i]["FEC_REG"]?></td>
                <?php if ($grafica_co_pros == 4 || $grafica_co_pros == 3 && $co_top_promo == 3 ){ ?>
                <td class="small"><?=$tabla_prospectos[$i]["FEC_REG_CLIE"]?></td>
                <td class="small"><?=$ins_objeto_comercial->tiempoTranscurridoFechas($tabla_prospectos[$i]["FEC_REG"],$tabla_prospectos[$i]["FEC_REG_CLIE"]);?></td>
                <?php } ?>
                <!-- INICIA CODE DIAS TRANSCURRIDOS DE PROSPECTOS -->
                <?php
                if ($grafica_co_pros == 1 || $grafica_co_pros == 3 && $co_top_promo == 2){
                $fecha_actual = date("d-m-Y");
                ?>
                <td class="small"><?=$ins_objeto_comercial->tiempoTranscurridoFechas($tabla_prospectos[$i]["FEC_REG"],$fecha_actual);?></td>
                <?php } ?>
                <!-- TERMINA CODE DIAS TRANSCURRIDOS DE PROSPECTOS -->
                <!-- INICIA CODE PORCENTAJE DE CIERRE -->
                <?php
                if ($grafica_co_pros == 1 || $grafica_co_pros == 3 && $co_top_promo <= 2){

                  $porc_cierre = $tabla_prospectos[$i]["PORC_CIERRE"] * 100 ;
                  switch (true) {
                    case ($porc_cierre > 50):
                      echo '<td class="small"><span class="badge bg-green">'.$porc_cierre.'%</span></td>';
                      break;

                    default:
                      echo '<td class="small"><span class="badge bg-yellow">'.$porc_cierre.'%</span></td>';
                      break;
                  }
                }
                ?>
                <!-- TERMINA CODE PORCENTAJE DE CIERRE -->
                <td class="small">
                <a class="fancybox fancybox.iframe" href="<?= 'prospecto_det.php?id_prospecto_co='.$tabla_prospectos[$i]["ID_PROSPECTO"]?>">
                            <span class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </span>
                            </a></td>
                <td class="small">
                <a class="fancybox fancybox.iframe" href="<?= 'detalles_factur_cliente.php?id_prospecto_co='.$tabla_prospectos[$i]["PROSPECTO"]?>">
                                        <span class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </span>
                                        </a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    </div>

    <!-- TERMINA TABLA DE PROSPECTOS -->

    </div><!-- /.box-body -->
  </div>

</section>
<?php }  ?>


<!-- ############################ INICIA SECCION DE TABLA DE PROSPECTOS ############################# -->
<?php //if ($co_plaza == true || $anio_co == true || $promotor_co == true ) { if (  $grafica_co_pros == true )  { ?>
<?php if ( $grafica_co_pros == 1 || $grafica_co_pros == 2 || $grafica_co_pros == 4 || $grafica_co_pros == 5 || $grafica_co_pros == 3 || $grafica_co_pros == 10 && $promotor_co == true )  { ?>
<section>
  <div class="box box-default">
    <!-- <div class="box-header with-border">
      <h3 class="box-title">PROMOTORES</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div> -->
    <div class="box-body"> <!-- box-body -->
    <?php
      switch ($grafica_co_pros) {
        case 3:
          if ($co_top_promo == 3){
          echo '<h5 class="content-header text-blue text-center"><i class="fa fa-table"></i> TABLA DE CLIENTES '.$co_plaza_nombre.' '.$titulo_fecha.'</h5><hr>';
          }
          break;
        case 4:
          echo '<h5 class="content-header text-blue text-center"><i class="fa fa-table"></i> TABLA DE PROSPECTOS CERRADOS '.$co_plaza_nombre.' '.$titulo_fecha.'</h5><hr>';
          break;
        case 5:
          echo '<h5 class="content-header text-blue text-center"><i class="fa fa-table"></i> TABLA DE PROSPECTOS CANCELADOS '.$co_plaza_nombre.' '.$titulo_fecha.'</h5><hr>';
          break;
        case 10:
          echo '<h5 class="content-header text-blue text-center"><i class="fa fa-table"></i> TABLA DE PROSPECTOS CERRADOS '.$co_plaza_nombre.' '.$titulo_fecha.'</h5><hr>';
          break;
        default:
          echo '<h5 class="content-header text-blue text-center"><i class="fa fa-table"></i> TABLA DE PROSPECTOS '.$co_plaza_nombre.' '.$titulo_fecha.'</h5><hr>';
          break;
      }
    ?>


    <!-- INICIA TABLA DE PROSPECTOS -->
    <br>
    <div class="table-responsive">
      <table id="tabla_prospecto" class="table compact table-bordered table-striped hover" cellspacing="0">
        <thead>
            <tr>
                <th class="small">PROSPECTO</th>
                <th class="small">STATUS/PROSPECTO</th>
                <?php if ($grafica_co_pros == 4){ ?>
                <th class="small">STATUS/CLIENTE</th>
                <?php } ?>
                <th class="small">PLAZA</th>
                <th class="small" title="FACTURACIÓN ESTIMADA">FACTURACIÓN ESTIMADA</th>
                <th class="small" title="TIPO DE SERVICIO">TIPO DE SERVICIO</th>
                <th class="small" title="TIPO DE PROSPECTO">TIPO DE PROSPECTO</th> <!-- 1: Habitual, 2=Eventual, 3=Proyecto, 4=Ocacional -->
                <th class="small" title="EJECUTIVO COMERCIAL">PROMOTOR</th>
                <th class="small" title="FECHA DE REGISTRO" style="width: 80px">REGISTRO</th>
                <?php if ($grafica_co_pros == 4 || $grafica_co_pros == 3 && $co_top_promo == 3){ ?>
                <th class="small" style="width: 80px">CIERRE</th>
                <th class="small">TIEMPO TRANSCURRIDO</th>
                <?php } ?>
                <?php if ($grafica_co_pros == 1 || $grafica_co_pros == 3 && $co_top_promo <= 2){ ?>
                <?php if ($co_top_promo <> 1 ) {?>
                <th class="small">TIEMPO TRANSCURRIDO</th>
                <?php } ?>
                <th class="small">PORCENTAJE DE CIERRE</th>
                <?php } ?>
                <th class="small">DETALLES</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $tabla_prospectos = $ins_objeto_comercial->tabla_prospectos($co_plaza,$t_prospecto_co,$t_servicio_co,$promotor_co,$anio_co,$id_prospecto_co,$grafica_co_pros,$co_top_promo,$fec_ini_co,$fec_fin_co,$status_pros_co_graf);
          for ($i=0; $i <count($tabla_prospectos) ; $i++) {
        ?>

        <?php if ($grafica_co_pros == 10) {
          echo "<h3>CERRADOS PRUEBA</h3>";
        } ?>

            <tr>
                <td class="small"><span class="text-muted"><?="(".$tabla_prospectos[$i]["ID_PROSPECTO"].") </span>".$tabla_prospectos[$i]["PROSPECTO"]?></td>
                <!-- INICIA CODE STATUS PROSPECTO -->
                <?php
                    switch ($tabla_prospectos[$i]["STATUS_PROS"]) {
                      case 0:
                        echo '<td class="small"><span class="badge bg-green">PRIMER CONTACTO</span></td>' ;
                        break;
                      case 1:
                        echo '<td class="small"><span class="badge bg-yellow">COTIZADO</span></td>' ;
                        break;
                      case 2:
                        echo '<td class="small"><span class="badge badge-primary">DOCUMENTACION</span></td>' ;
                        break;
                      case 3:
                        echo '<td class="small"><span class="badge bg-green">CLIENTE</span></td>' ;
                        break;
                      case 4:
                          echo '<td class="small"><span class="badge bg-red">BAJA</span></td>' ;
                          break;
                      default:
                        echo '<td class="small"><span class="badge bg-gray">NO DEFINIDO</span></td>' ;
                        break;
                    }
                ?>
                <!-- TERMINA CODE STATUS PROSPECTO -->
                <!-- INICIA CODE STATUS DEL CLIENTE -->
                <?php
                if ($grafica_co_pros == 4){
                    switch ($tabla_prospectos[$i]["STATUS_CLI"]) {
                      case 1:
                        echo '<td class="small"><span class="badge bg-green">ACTIVO</span></td>' ;
                        break;
                      case 2:
                        echo '<td class="small"><span class="badge bg-red">INACTIVO</span></td>' ;
                        break;
                      default:
                        echo '<td class="small"><span class="badge bg-gray">NO DEFINIDO</span></td>' ;
                        break;
                    }
                  }
                ?>
                <!-- TERMINA CODE STATUS DEL CLIENTE -->
                <td class="small"><?=$tabla_prospectos[$i]["PLAZA"]?></td>
                <td class="small"><?=$tabla_prospectos[$i]["FAC_ESTIMADA"]?></td>
                <td class="small"><?=$tabla_prospectos[$i]["TIPO_SERVICIO"]?></td>
                <td class="small"><?=$tabla_prospectos[$i]["TIPO_PROS"]?></td>
                <td class="small"><?=$tabla_prospectos[$i]["NOM_PROM"]." ".$tabla_prospectos[$i]["APEPAT_PROM"]." ".$tabla_prospectos[$i]["APEMAT_PROM"]?></td>
                <td class="small"><?=$tabla_prospectos[$i]["FEC_REG"]?></td>
                <?php if ($grafica_co_pros == 4 || $grafica_co_pros == 3 && $co_top_promo == 3 ){ ?>
                <td class="small"><?=$tabla_prospectos[$i]["FEC_REG_CLIE"]?></td>
                <td class="small"><?=$ins_objeto_comercial->tiempoTranscurridoFechas($tabla_prospectos[$i]["FEC_REG"],$tabla_prospectos[$i]["FEC_REG_CLIE"]);?></td>
                <?php } ?>
                <!-- INICIA CODE DIAS TRANSCURRIDOS DE PROSPECTOS -->
                <?php
                if ($grafica_co_pros == 1 || $grafica_co_pros == 3 && $co_top_promo == 2){
                $fecha_actual = date("d-m-Y");
                ?>
                <td class="small"><?=$ins_objeto_comercial->tiempoTranscurridoFechas($tabla_prospectos[$i]["FEC_REG"],$fecha_actual);?></td>
                <?php } ?>
                <!-- TERMINA CODE DIAS TRANSCURRIDOS DE PROSPECTOS -->
                <!-- INICIA CODE PORCENTAJE DE CIERRE -->
                <?php
                if ($grafica_co_pros == 1 || $grafica_co_pros == 3 && $co_top_promo <= 2){

                  $porc_cierre = $tabla_prospectos[$i]["PORC_CIERRE"] * 100 ;
                  switch (true) {
                    case ($porc_cierre > 50):
                      echo '<td class="small"><span class="badge bg-green">'.$porc_cierre.'%</span></td>';
                      break;

                    default:
                      echo '<td class="small"><span class="badge bg-yellow">'.$porc_cierre.'%</span></td>';
                      break;
                  }
                }
                ?>
                <!-- TERMINA CODE PORCENTAJE DE CIERRE -->
                <td class="small">
                <a class="fancybox fancybox.iframe" href="<?= 'prospecto_det.php?id_prospecto_co='.$tabla_prospectos[$i]["ID_PROSPECTO"]?>">
                            <span class="badge badge bg-teal-active btn"> <i class="fa fa-search"></i> Ver </span>
                            </a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    </div>

    <!-- TERMINA TABLA DE PROSPECTOS -->

    </div><!-- /.box-body -->
  </div>

</section>
<?php }  ?>
<!-- ########################### TERMINA SECCION DE TABLA DE PROSPECTOS ########################### -->



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
<!-- SlimScroll -->
<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script>
$(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
  });
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
<script>
//========================= INICIA CODE JS DATATABLE PROSPECTOS =========================//
$(document).ready(function() {
  $('#tabla_prospecto').DataTable({
    stateSave: true,
    "ordering": false,
    "language": {
        "url": "../plugins/datatables/Spanish.json"
      },
//---------- INICIA CODE BOTONES (EXCEL-PINT-VIEW) ----------//
    dom: 'Bfrtip',
        buttons: [

          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible'
            },
            title: 'Prospectos <?= $titulo_prospecto_co ?>',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Prospectos <?= $titulo_prospecto_co ?>',
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
//========================= TERMINA CODE JS DATATABLE PROSPECTOS =========================//
$(document).ready(function() {
    $('#tabla_top_cierre,#tabla_top_pros,#tabla_top_cliente').DataTable( {
        "paging":   true,
        stateSave: true,
        "ordering": false,
        "info":     false,
        "searching": false,
        "bLengthChange": false,
        pageLength: 5,
        "language": {
          "url": "../plugins/datatables/Spanish.json"
        }

    } );
} );
</script>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>

<script>
$('#reset_fec_per_co_pros').click(function(){
  $('#fec_ini_co').val("");
  $('#fec_fin_co').val("");
});



$('#fec_rango_co').daterangepicker(
        {
        "linkedCalendars": false,
        "showDropdowns": true,
      //INICIA CODE OPCION PARA FORMATO EN ESPAÑOL
        "locale": {
        "format": "DD-MM-YYYY",
        "separator": " - ",
        "applyLabel": "Aplicar",
        "cancelLabel": "Cancelar",
        "fromLabel": "From",
        "toLabel": "To",
        "customRangeLabel": "Fecha Personalizada",
        "daysOfWeek": [
            "Do",
            "Lu",
            "Ma",
            "Mi",
            "Ju",
            "Vi",
            "Sa"
        ],
        "monthNames": [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agusto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ],
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
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function (start, end) {
          //$('#fec_rango_co span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
          $('#fec_ini_co').val(start.format('DD-MM-YYYY'));
          $('#fec_fin_co').val(end.format('DD-MM-YYYY'));
        }
    );
</script>
<!-- FLOT CHARTS -->
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
<?php
  if ($grafica_co_pros == null){//abre if para mostrar graficas solo al inicio
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
                    show: true,
                    radius:2/3,/*0.90 posicion del label con data*/
                    formatter: labelFormatter,
                  },
                }";

$donut_grid =  "hoverable: true,
                clickable: true,
                verticalLines: false,
                horizontalLines: false,";
$donut_legend = "/*labelBoxBorderColor: 'none'*/
                show: false "; //-- PONE LOS LABEL DEL ALDO IZQUIERDO //

$donut_content = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> T.PROSPECTO = %y.0 </b>  </div>' ;

$donut_tooltip = "show: true,
      content: '".$donut_content."',
      defaultTheme: false ";
/* ------------------- INICIA OPCIONES PARA LA GRÁFICA DE BARRAS ------------------- */
$bar_grid = " margin: { right: 0 },
              borderColor: {
                          top: '#e5e5e5',
                          right: '#e5e5e5',
                          bottom: '#a5b2c0',
                          left: '#a5b2c0'
                          },
              hoverable: true ";

$bar_content = '<div class="btn btn-block bg-teal btn-xs"><i class="fa fa-users"></i>  PROSPECTOS = %n </div>';
$bar_tooltip = "show: true,
                content: '".$bar_content."',
                shifts: { x: 20, y: 0 },
                defaultTheme: false ";

$bar_series = " bars: {
                    show: true,
                    align: 'center',
                    barWidth: 0.5,/*anchura grafica*/
                    lineWidth: 1.5,
                    fill: 0.9,/*opacidad */
                },
                lines: { show: true },
                points: { show: true } ";

$bar_xaxis = "mode: 'categories',
              tickLength: 20, /*linea*/
              min: -0.5, /*separacion izquierda*/
              max: 12, /*separacion derecha*/ ";

$bar_yaxis = "/* tickDecimals: 0, */ min:0, ";

?>
<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
      $grafica_pastel = $ins_objeto_comercial->grafica_pastel($co_plaza,$anio_co,$t_servicio_co,$t_prospecto_co,$promotor_co,$fec_ini_co,$fec_fin_co,$status_pros_co_graf);
        for ($i=0; $i <count($grafica_pastel) ; $i++) {
          $plaza = $grafica_pastel[$i]["PLAZA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //

          switch ($status_pros_co_graf) {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_pastel[$i]["PLAZA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_pastel[$i]["ID_PLAZA"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$grafica_pastel[$i]["ID_PLAZA"].'</button></form>' ;
                $donut_content2 = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> '.$plaza_corta.' = '. $grafica_pastel[$i]["TOTAL_PROS"].'.0 </b>  </div>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_pastel[$i]["PLAZA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_pastel[$i]["ID_PLAZA"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$grafica_pastel[$i]["ID_PLAZA"].'</button></form>' ;
               $donut_content2 = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> '.$plaza_corta.' = '. $grafica_pastel[$i]["TOTAL_PROS"].'.0 </b>  </div>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_pastel[$i]["PLAZA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_pastel[$i]["ID_PLAZA"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$grafica_pastel[$i]["ID_PLAZA"].'</button></form>' ;
                $donut_content2 = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> '.$plaza_corta.' = '. $grafica_pastel[$i]["TOTAL_PROS"].'.0 </b>  </div>' ;
                break;
            }

            $donut_tooltip2 = "show: true,
                  content: '".$donut_content2."',
                  defaultTheme: false ";

          $data =  $grafica_pastel[$i]["TOTAL_PROS"];
          $color = $grafica_pastel[$i]["COLOR"];
          // _-_-_-_-_- TERMNA VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
      ?>

        {label: '<?= $plaza_corta ?>', data: <?=$data?> , color: '<?= $color ?>'},

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

<script>
/* BAR CHART */
var bar_data= [

    <?php
    $grafica_mes_prospectos = $ins_objeto_comercial->grafica_mes_prospectos($anio_co,$co_plaza,$promotor_co,$t_servicio_co,$t_prospecto_co,$fec_ini_co,$fec_fin_co,$status_pros_co_graf);
    {
      for ($i=0; $i <count($grafica_mes_prospectos) ; $i++) {
       echo '{ data: [["'.$grafica_mes_prospectos[$i]["MES"].'" ,'.$grafica_mes_prospectos[$i]["T_PROSPECTO"].' ]], color: "#0073B7", },';
      }
    }
    ?>
    ];

    $.plot("#bar-chart-prospectos", bar_data, {
      grid: { <?= $bar_grid ?> },
      tooltip: { <?= $bar_tooltip ?>},
      series: { <?= $bar_series ?> },
      xaxis: { <?= $bar_xaxis ?>},
      yaxis: { <?= $bar_yaxis ?> },
    });
    /* END BAR CHART */
    $(".flot-tick-label").css("zIndex","2");
</script>
 <?php }//cierra if para mostrar graficas solo al inicio ?>
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
<script type="text/javascript">
$(function() {

  $('#tab_anio_pros_co').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_anio_fec_per_pros', $(e.target).attr('href'));
    });

    $('#tab_fec_per_pros_co').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab_anio_fec_per_pros', $(e.target).attr('href'));
    });

    var activeTab_anio_fec_per_pros = localStorage.getItem('activeTab_anio_fec_per_pros');
  if(activeTab_anio_fec_per_pros){
    $('#myTab_anio_fec_per_pros a[href="' + activeTab_anio_fec_per_pros + '"]').tab('show');
  }

});
</script>
</html>
<?php conexion::cerrar($conn); ?>
