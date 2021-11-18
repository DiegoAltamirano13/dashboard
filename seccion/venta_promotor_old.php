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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], '17');
if($modulos_valida == 0)
{ 
  header('Location: index.php');
}
/* .-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.- INICIA CODE INDEPENDIENTE .-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-  */
include_once '../class/Venta_promotor.php';
$insObj_VentaPromotor = new VentaPromotor();
/*declaracion de sessiones*/
/* SESSION ANIO */
if ( !isset( $_SESSION['d17_pro_pla'] ) ){ 
  $_SESSION['d17_pro_pla'] = 1;
  $d17_pro_pla = $_SESSION['d17_pro_pla'];
}else{
  if(isset($_POST['d17_pro_pla']))
  $_SESSION['d17_pro_pla'] = $_POST['d17_pro_pla'];
  $d17_pro_pla = $_SESSION['d17_pro_pla'];
}
/* SESSION ANIO */
if ( !isset( $_SESSION['d17_anio'] ) ){ 
  $_SESSION['d17_anio'] = date("Y");
  $d17_anio = $_SESSION['d17_anio'];
}else{
  if(isset($_POST['d17_anio']))
  $_SESSION['d17_anio'] = $_POST['d17_anio'];
  $d17_anio = $_SESSION['d17_anio'];
}
/* SESSION PROMOTOR */
if ( !isset( $_SESSION['d17_promotor'] ) ){ 
  $_SESSION['d17_promotor'] = "ALL";
  $d17_promotor = $_SESSION['d17_promotor'];
}else{
  if(isset($_POST['d17_promotor']))
  $_SESSION['d17_promotor'] = $_POST['d17_promotor'];
  $d17_promotor = $_SESSION['d17_promotor'];
}
/* SESSION PLAZA */
if ( !isset( $_SESSION['d17_plaza'] ) ){ 
  $_SESSION['d17_plaza'] = "ALL";
  $d17_plaza = $_SESSION['d17_plaza'];
}else{
  if(isset($_POST['d17_plaza']))
  $_SESSION['d17_plaza'] = $_POST['d17_plaza'];
  $d17_plaza = $_SESSION['d17_plaza'];
}


?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- *********** INICIA INCLUDE CSS *********** -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/Select/select.dataTables.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/Responsive/css/responsive.dataTables.min.css">

<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) --> 
    <section class="content-header">
      <h1>Dashboard <small>Venta vs Presupuesto Promotores</small></h1>
    </section> 
    <!-- Main content -->

    <!-- <h4 class="content-header text-blue text-center"><i class="ion-ios-pricetags"></i> Remates</h4> -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
      
    <div class="box box-primary box-solid">
      <div class="box-header with-border">
        <h3 class="box-title">Filtros Disponibles <?php //echo "d17_pro_pla= ".$d17_pro_pla." d17_anio= ".$d17_anio." d17_promotor= ".$d17_promotor." d17_plaza= ".$d17_plaza; ?> </h3>

        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
        </div> <!-- /.box-tools -->
      </div> <!-- /.box-header -->
      <div class="box-body">
        
        <div class="row form-horizontal"><!-- row filtros -->

          <!-- SELECT PLAZA/PROMOTOR -->
          <div class="col-md-4 col-sm-4 col-xs-12"> 
            
          <div class="form-group"><!-- form-group -->
            <label class="col-sm-2 control-label">Presupuesto:</label>
            <div class="col-sm-10">
              <form method="POST">
                <select class="form-control" name="d17_pro_pla" id="submitProPla" style="width: 100%;" onchange="this.form.submit()">
                  <option value="1" <?php if($d17_pro_pla == 1){ echo 'selected';} ?> >PROMOTOR</option>
                  <option value="2" <?php if($d17_pro_pla == 2){ echo 'selected';} ?>>PLAZA</option>
                </select>
                <input type="hidden" name="d17_promotor" value="ALL">
                <input type="hidden" name="d17_plaza" value="ALL">
              </form> 
            </div>
          </div><!-- /.form-group -->
          </div>
          <!-- SELECT ANIO -->
          <div class="col-md-2 col-sm-2 col-xs-12"> 
            
            <div class="form-group"><!-- form-group -->
              <label class="col-sm-2 control-label">Año</label>
              <div class="col-sm-10" id="modal_d17_anio" data-toggle="modal" data-target="#modalAnio">
                <div class="form-control" style="width: 100%;"> 
                  <option value="<?=$d17_anio?>"><?=$d17_anio?></option> 
                </div>
              </div>
            </div><!-- /.form-group --> 
            <div class="modal fade" id="modalAnio" role="dialog"><!-- Modal anio -->
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Año <?=$d17_anio?></h4>
                  </div>
                  <div class="modal-body">
                    <form method="POST">
                      <select class="form-control select2" name="d17_anio" id="d17_anio" style="width: 100%;" onchange="this.form.submit()">
                      </select> 
                    </form> 
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                  </div>
                </div>
              </div>
            </div><!-- Modal anio -->

          </div>
          <!-- SELECT PROMOTOR -->
          <div class="col-md-3 col-sm-3 col-xs-12">  
            <div class="form-group"><!-- form-group -->
              <label class="col-sm-2 control-label"><?php if ( $d17_pro_pla == 1){ echo 'Promotor';}else{ echo 'Plaza';} ?></label>
              <div class="col-sm-10" id="modal_d17_promotor" data-toggle="modal" data-target="#modalPromotor">
                <div class="form-control" style="width: 100%;"> 
                  <option class="optionPromo" value="<?=$d17_promotor?>"><?php echo $d17_promotor; ?> </option> 
                </div>
              </div>
            </div><!-- /.form-group -->
            <div class="modal fade" id="modalPromotor" role="dialog"><!-- Modal Promotor -->
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title optionPromo"> <?=$d17_promotor?></h4>
                  </div>
                  <div class="modal-body">
                    <form method="POST">
                      <?php if ( $d17_pro_pla == 1 ){ ?>
                      <select class="form-control select2" name="d17_promotor" id="d17_promotor" style="width: 100%;" onchange="this.form.submit()">
                      </select>
                      <input type="hidden" name="d17_plaza" value="ALL">
                      <?php }else{ ?>
                      <select class="form-control select2" name="d17_plaza" id="d17_promotor" style="width: 100%;" onchange="this.form.submit()">
                      </select>
                      <input type="hidden" name="d17_promotor" value="ALL">
                      <?php } ?>
                    </form> 
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                  </div>
                </div>
              </div>
            </div><!-- Modal Promotor -->
          </div>
          <!-- SELECT PLAZA -->
          <?php if ( $d17_pro_pla != 2 && $d17_promotor != 0 ) { ?> 
          <div class="col-md-3 col-sm-3 col-xs-12">  
            <div class="form-group"><!-- form-group -->
              <label class="col-sm-2 control-label">Plaza</label>
              <div class="col-sm-10" id="modal_d17_plaza" data-toggle="modal" data-target="#modalPlaza">
                <div class="form-control" style="width: 100%;"> 
                  <option value="<?=$d17_plaza?>"><?=$d17_plaza?></option> 
                </div>
              </div>
            </div><!-- /.form-group -->
            <div class="modal fade" id="modalPlaza" role="dialog"><!-- Modal Promotor -->
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Plaza <?=$d17_plaza?></h4>
                  </div>
                  <div class="modal-body">
                    <form method="POST">
                      <select class="form-control select2" name="d17_plaza" id="d17_plaza" style="width: 100%;" onchange="this.form.submit()">
                      </select> 
                    </form> 
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                  </div>
                </div>
              </div>
            </div><!-- Modal Promotor -->
          </div>
          <?php } ?>
          <!-- /.col -->

        </div><!-- ./ow filtros -->

      </div> <!-- /.box-body -->
    </div>

    <!-- ############################ INICIA SECCION TABLA PRESUPUESTADO ############################# --> 
    <section>
      <div class="box box-default">    
        <div class="box-header with-border">
          <h3 class="box-title"> DISTRIBUCIÓN DE ACUERDO A LA VENTA <?= $d17_anio ?> </h3> 
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->  
            
            <div class="table-responsive">

              <table id="tabla1" class="table table-bordered table-hover table-striped">
                <thead>
                <tr> 
                  <th style="background-color: #4CAF50; color:white;">PROMOTOR</th>
                  <th style="background-color: #4CAF50; color:white;">ENERO</th>
                  <th style="background-color: #4CAF50; color:white;">FEBRERO</th>
                  <th style="background-color: #4CAF50; color:white;">MARZO</th>
                  <th style="background-color: #4CAF50; color:white;">ABRIL</th>
                  <th style="background-color: #4CAF50; color:white;">MAYO</th>
                  <th style="background-color: #4CAF50; color:white;">JUNIO</th>
                  <th style="background-color: #4CAF50; color:white;">JULIO</th>
                  <th style="background-color: #4CAF50; color:white;">AGOSTO</th>
                  <th style="background-color: #4CAF50; color:white;">SEPTIEMBRE</th>
                  <th style="background-color: #4CAF50; color:white;">OCTUBRE</th>
                  <th style="background-color: #4CAF50; color:white;">NOVIEMBRE</th>
                  <th style="background-color: #4CAF50; color:white;">DICIEMBRE</th>
                  <th style="background-color: #4CAF50; color:white;">TOTAL</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $tablaPresupuesto = $insObj_VentaPromotor->tablaPresupuesto($d17_pro_pla,$d17_anio);
                for ($i=0; $i <count($tablaPresupuesto) ; $i++) {
                  $sumMes1[$i] = $tablaPresupuesto[$i]["N_VALOR_MES1"];
                  $sumMes2[$i] = $tablaPresupuesto[$i]["N_VALOR_MES2"];
                  $sumMes3[$i] = $tablaPresupuesto[$i]["N_VALOR_MES3"];
                  $sumMes4[$i] = $tablaPresupuesto[$i]["N_VALOR_MES4"];
                  $sumMes5[$i] = $tablaPresupuesto[$i]["N_VALOR_MES5"];
                  $sumMes6[$i] = $tablaPresupuesto[$i]["N_VALOR_MES6"];
                  $sumMes7[$i] = $tablaPresupuesto[$i]["N_VALOR_MES7"];
                  $sumMes8[$i] = $tablaPresupuesto[$i]["N_VALOR_MES8"];
                  $sumMes9[$i] = $tablaPresupuesto[$i]["N_VALOR_MES9"];
                  $sumMes10[$i] = $tablaPresupuesto[$i]["N_VALOR_MES10"];
                  $sumMes11[$i] = $tablaPresupuesto[$i]["N_VALOR_MES11"];
                  $sumMes12[$i] = $tablaPresupuesto[$i]["N_VALOR_MES12"];

                  $sumTotalMes = $tablaPresupuesto[$i]["N_VALOR_MES1"]+$tablaPresupuesto[$i]["N_VALOR_MES2"]+$tablaPresupuesto[$i]["N_VALOR_MES3"]+$tablaPresupuesto[$i]["N_VALOR_MES4"]+$tablaPresupuesto[$i]["N_VALOR_MES5"]+$tablaPresupuesto[$i]["N_VALOR_MES6"]+$tablaPresupuesto[$i]["N_VALOR_MES7"]+$tablaPresupuesto[$i]["N_VALOR_MES8"]+$tablaPresupuesto[$i]["N_VALOR_MES9"]+$tablaPresupuesto[$i]["N_VALOR_MES10"]+$tablaPresupuesto[$i]["N_VALOR_MES11"]+$tablaPresupuesto[$i]["N_VALOR_MES12"] ;
                  $sumTotalMesT[$i] = $sumTotalMes;
                ?>
                <tr <?php if ( $d17_promotor == $tablaPresupuesto[$i]["ID_PROMOTOR"] || $d17_plaza == $tablaPresupuesto[$i]["IID_PLAZA"] ){ echo 'class="selectColorPro" style="background-color: #E57373;color:white;"'; }  ?> > 
                  <td title='<?= $tablaPresupuesto[$i]["V_NOMBRE"]." ".$tablaPresupuesto[$i]["V_APELLIDO_PAT"]." ".$tablaPresupuesto[$i]["V_APELLIDO_MAT"] ?>'>
                  <form method="post">
                    <?php if ($d17_pro_pla == 1){ ?>
                    <button class="btn btn-link click_modal_cargando" name="d17_promotor" type="submit" value="<?=$tablaPresupuesto[$i]["ID_PROMOTOR"]?>">
                    <?= "(".$tablaPresupuesto[$i]["ID_PROMOTOR"].")".$tablaPresupuesto[$i]["V_NOMBRE"] ?>
                    </button><input type="hidden" class="nompromo" value='<?= "(".$tablaPresupuesto[$i]["ID_PROMOTOR"].")".$tablaPresupuesto[$i]["V_NOMBRE"] ?>'>
                    <?php }else{ ?>
                    <button class="btn btn-link click_modal_cargando" name="d17_plaza" type="submit" value="<?=$tablaPresupuesto[$i]["IID_PLAZA"]?>">
                    <?= "(".$tablaPresupuesto[$i]["IID_PLAZA"].")".$tablaPresupuesto[$i]["V_NOMBRE"] ?>
                    </button><input type="hidden" class="nompromo" value='<?= "(".$tablaPresupuesto[$i]["IID_PLAZA"].")".$tablaPresupuesto[$i]["V_NOMBRE"] ?>'>
                    <?php } ?>
                  </form>
                  </td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES1"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES2"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES3"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES4"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES5"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES6"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES7"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES8"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES9"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES10"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES11"],2 ) ?></td>
                  <td>$<?= number_format( $tablaPresupuesto[$i]["N_VALOR_MES12"],2 ) ?></td>
                  <th>$<?= number_format( $sumTotalMes,2 )?></th>
                </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <tr>
                  <th></th>
                  <th>$<?= number_format(array_sum($sumMes1),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes2),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes3),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes4),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes5),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes6),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes7),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes8),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes9),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes10),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes11),2) ?></th>
                  <th>$<?= number_format(array_sum($sumMes12),2) ?></th>
                  <th>$<?= number_format(array_sum($sumTotalMesT),2) ?></th>
                </tr>
              </tfoot>
              </table> 

            </div>
           
        </div><!--/.box-body--> 
      </div> 
    </section> 
    <!-- ########################### TERMINA SECCION TABLA PRESUPUESTADO ########################### -->


    <!-- ############################ INICIA SECCION TABLA PRESUPUESTADO ############################# --> 
    <section>
      <div class="box box-default">    
        <div class="box-header with-border">
          <h3 class="box-title" id="nomTabla2"> TABLA PRESUPUESTO <?=$d17_anio?> VS RESULTADO <?=$d17_anio?> </h3> 
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->  
            
            <div class="table-responsive">

              <table id="tabla2" class="table table-bordered table-hover table-striped">
                <thead>
                <tr> 
                  <th style="background-color: #36608B; color:white;"></th>
                  <th id="mesFac" style="background-color: #003366; color:white;">ENERO</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">FEBRERO</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">MARZO</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">ABRIL</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">MAYO</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">JUNIO</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">JULIO</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">AGOSTO</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">SEPTIEMBRE</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">OCTUBRE</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">NOVIEMBRE</th>
                  <th id="mesFac" style="background-color: #003366; color:white;">DICIEMBRE</th> 
                </tr>
                </thead>
                <tbody> 
                <tr> 
                  <td>PRESUPUESTO OBJETIVO</td>
                  <?php if ( $d17_promotor == "ALL" && $d17_plaza == 'ALL' ){ ?>
                  <td class="vP0Script"><?= "$".number_format( array_sum($sumMes1),2 ) ?></td>
                  <td class="vP1Script"><?= "$".number_format( array_sum($sumMes2),2 ) ?></td>
                  <td class="vP2Script"><?= "$".number_format( array_sum($sumMes3),2 ) ?></td>
                  <td class="vP3Script"><?= "$".number_format( array_sum($sumMes4),2 ) ?></td>
                  <td class="vP4Script"><?= "$".number_format( array_sum($sumMes5),2 ) ?></td>
                  <td class="vP5Script"><?= "$".number_format( array_sum($sumMes6),2 ) ?></td>
                  <td class="vP6Script"><?= "$".number_format( array_sum($sumMes7),2 ) ?></td>
                  <td class="vP7Script"><?= "$".number_format( array_sum($sumMes8),2 ) ?></td>
                  <td class="vP8Script"><?= "$".number_format( array_sum($sumMes9),2 ) ?></td>
                  <td class="vP9Script"><?= "$".number_format( array_sum($sumMes10),2 ) ?></td>
                  <td class="vP10Script"><?= "$".number_format( array_sum($sumMes11),2 ) ?></td>
                  <td class="vP11Script"><?= "$".number_format( array_sum($sumMes12),2 ) ?></td>
                  <?php 
                  }else{ 
                    $res_tablaPresupuestoVta = $insObj_VentaPromotor->tablaPresupuestoVta($d17_pro_pla,$d17_anio,$d17_promotor,$d17_plaza); 
                  ?>
                  
                  <td class="vP0Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES1"],2 ) ?></td>
                  <td class="vP1Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES2"],2 ) ?></td>
                  <td class="vP2Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES3"],2 ) ?></td>
                  <td class="vP3Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES4"],2 ) ?></td>
                  <td class="vP4Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES5"],2 ) ?></td>
                  <td class="vP5Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES6"],2 ) ?></td>
                  <td class="vP6Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES7"],2 ) ?></td>
                  <td class="vP7Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES8"],2 ) ?></td>
                  <td class="vP8Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES9"],2 ) ?></td>
                  <td class="vP9Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES10"],2 ) ?></td>
                  <td class="vP10Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES11"],2 ) ?></td>
                  <td class="vP11Script"><?= "$".number_format( $res_tablaPresupuestoVta[0]["N_VALOR_MES12"],2 ) ?></td>

                  <?php } ?>
                </tr>
                <tr> 
                  <td>VENTA REAL</td>
                  <?php
                  $res_cursor = $insObj_VentaPromotor->graficaVenta($d17_pro_pla,$d17_anio,$d17_promotor,$d17_plaza);
                  for ($i=0; $i <count($res_cursor) ; $i++) {
                    echo '<td id="btnAjaxModalDet'.$i.'" style="cursor:pointer; cursor: hand" class="vR'.$i.'Script" data-toggle="modal" data-target="#myModal"> $'.number_format($res_cursor[$i]["FACTURADO_TOTAL"],2).'</td>'; ?>

                    <script type="text/javascript">
                      $("#btnAjaxModalDet<?=$i?>").click(function() { 


                        $.ajax({
                          type: 'POST',
                          url: '../action/venta_PromorAjax.php',
                          cache:false,
                          data: { "btnAjaxModalDet" : 1, "d17_pro_pla" : <?=$d17_pro_pla?>, "det_anio" : <?=$d17_anio?>, "det_idPromotor" : '<?=$d17_promotor?>', "det_idPlaza" : '<?=$d17_plaza?>', "det_mes" : '<?=$res_cursor[$i]["MES"]?>' },
                          // beforeSend: function () {
                          //   $("#tablaDetFacturado").html("Procesando, espere por favor...");
                          // },
                          success: function (response) { 
                            var o = JSON.parse(response);//A la variable le asigno el json decodificado 

                            $('#tablaDetFacturado').dataTable( {
                                destroy: true,
                                stateSave: true, 
                                "ordering": true,
                                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
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
                                          title: 'Venta vs Presupuesto Promotores',  
                                        }, 

                                        {
                                          extend: 'print', 
                                          text: '<i class="fa fa-print"></i>',
                                          titleAttr: 'Imprimir',  
                                          exportOptions: {//muestra/oculta visivilidad de columna
                                              columns: ':visible', 
                                          }, 
                                          title: '<h5>Venta vs Presupuesto Promotores</h5>',
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

                                data : o,
                                columns: [

                                    {"data" : "IID_NUM_CLIENTE" },
                                    {"data" : "CLIENTE"},
                                    {"data" : "IID_ALMACEN"},
                                    {"data" : "ALMACEN"},
                                    {"data" : "PLAZA"},
                                    { "data": null, render: function ( data, type, row ) {
                                      // Combine the first and last names into a single table field
                                      return '('+data.IID_PROMOTOR+')'+data.V_NOMBRE+' '+data.V_APELLIDO_PAT+' '+data.V_APELLIDO_MAT;
                                    } },
                                    {"data" : "FACTURADO_TOTAL", render: $.fn.dataTable.render.number(',', '.', 2, '')}            
                                ],
                            });
                            //$("#resAjaxModalDet").html(response); 
                          }
                        });
 

                      }); 
                    </script>

                  <?php } ?>
                  <?php
                  $faltaTD = (12 - count($res_cursor) );
                  $num = 1;
                  for ($i=0; $i < $faltaTD ; $i++) { 
                    $num = $num+$i;
                    if ( $d17_plaza != "ALL"){
                      //echo "<td class='vR".(($faltaTD+$i)-2)."Script'></td>";
                      echo "<td class='vR".((count($res_cursor))+$i)."Script'></td>";
                    }else{
                      echo "<td class='vR".((count($res_cursor))+$i)."Script'></td>";
                    }
                  }
                  ?>
                </tr>
                <tfoot>
                <tr> 
                  <td>CUMPLIMIENTO</td>
                  <td class="vC0Script">%0.00</td>
                  <td class="vC1Script">%0.00</td>
                  <td class="vC2Script">%0.00</td>
                  <td class="vC3Script">%0.00</td>
                  <td class="vC4Script">%0.00</td>
                  <td class="vC5Script">%0.00</td>
                  <td class="vC6Script">%0.00</td>
                  <td class="vC7Script">%0.00</td>
                  <td class="vC8Script">%0.00</td>
                  <td class="vC9Script">%0.00</td>
                  <td class="vC10Script">%0.00</td>
                  <td class="vC11Script">%0.00</td>
                </tr>
                </tfoot>
                </tbody> 
              </table> 

            </div>
           
        </div><!--/.box-body--> 
      </div> 
    </section> 
    <!-- ########################### TERMINA SECCION TABLA PRESUPUESTADO ########################### -->

    <!-- ########################### INICIA MODAL PARA VER DETALLE DE LO FACTURADO ########################### -->

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 id="titleModalDetFac" class="modal-title"></h4>
          </div>
          <div class="modal-body">
              
              <div id="resAjaxModalDet"></div>
              
              <div class="table-responsive">
                <table id="tablaDetFacturado" class="table table-bordered table-hover table-striped">
                  <thead>
                  <tr>
                    <th>ID/CLTE.</th>
                    <th>CLIENTE</th>
                    <th>ID/ALM</th>
                    <th>ALMACEN</th>
                    <th>PLAZA</th>
                    <th>PROMOTOR</th>
                    <th>FACTURADO</th>
                  </tr> 
                  </tbody>
                </table>
              </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- ./Modal -->
    <script type="text/javascript">
      $("#btnAjaxModalDet0").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES ENERO <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet1").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES FEBRERO <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet2").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES MARZO <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet3").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES ABRIL <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet4").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES MAYO <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet5").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES JUNIO <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet6").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES JULIO <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet7").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES AGOSTO <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet8").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES SEPTIEMBRE <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet9").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES OCTUBRE <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet10").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES NOVIEMBRE <?=$d17_anio?> ") }); 
      $("#btnAjaxModalDet11").on('click',function () { $("#titleModalDetFac").html("DETALLE DE FACTURACIÓN MES DICIEMBRE <?=$d17_anio?> ") }); 
    </script>

    <!-- ########################### TERMINA MODAL PARA VER DETALLE DE LO FACTURADO ########################### -->
    


    <!-- ############################ INICIA SECCION DE LA GRAFICA ############################# --> 
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
          
          <div id="grafica"></div>
           
        </div><!--/.box-body--> 
      </div> 
    </section> 
    <!-- ########################### TERMINA SECCION DE LA GRAFICA ########################### -->
      

    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal --> 
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
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
<!-- SELECT DATATBLE -->
<script src="../plugins/datatables/extensions/Select/dataTables.select.min.js"></script>
<!-- RESPONSIVE DATATBLE -->
<script src="../plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
<!-- Grafica Highcharts --> 
<script src="../plugins/highcharts/highcharts.js"></script>
<script>

$(document).ready(function() {
  if ( $(".selectColorPro").css( "background-color" ) ){
    var valSelectColor =  $(".selectColorPro").find('.nompromo').val();
    console.log(valSelectColor);
  }

<?php if ($d17_pro_pla == 1){ ?>
  $(".optionPromo").html( valSelectColor );
<?php  }?>
<?php if ($d17_pro_pla == 2){ ?>
  $(".optionPromo").html( valSelectColor );
<?php  }?>
});


  

  $(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });

    var categories = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

   
    <?php
    $nomPromoSelect = "GENERAL";
    ?>

    /*INICIA GRAFICA PRESUPUESTO SELECIONADO*/

    var valMes1 = parseFloat( $(".vP0Script").html().replace(/\$|\,/g, '') ); 
    var valMes2 = parseFloat($(".vP1Script").html().replace(/\$|\,/g, '') );
    var valMes3 = parseFloat($(".vP2Script").html().replace(/\$|\,/g, '') );
    var valMes4 = parseFloat($(".vP3Script").html().replace(/\$|\,/g, '') );
    var valMes5 = parseFloat($(".vP4Script").html().replace(/\$|\,/g, '') );
    var valMes6 = parseFloat($(".vP5Script").html().replace(/\$|\,/g, '') );
    var valMes7 = parseFloat($(".vP6Script").html().replace(/\$|\,/g, '') );
    var valMes8 = parseFloat($(".vP7Script").html().replace(/\$|\,/g, '') );
    var valMes9 = parseFloat($(".vP8Script").html().replace(/\$|\,/g, '') );
    var valMes10 = parseFloat($(".vP9Script").html().replace(/\$|\,/g, '') );
    var valMes11 = parseFloat($(".vP10Script").html().replace(/\$|\,/g, '') );
    var valMes12 = parseFloat($(".vP11Script").html().replace(/\$|\,/g, '') );
    /*TERMINA GRAFICA PRESUPUESTO SELECIONADO*/
    var data1 = [valMes1,valMes2,valMes3,valMes4,valMes5,valMes6,valMes7,valMes8,valMes9,valMes10,valMes11,valMes12];  

    var data2 = [<?php 
      for ($i=0; $i <count($res_cursor) ; $i++) { 
        echo $res_cursor[$i]["FACTURADO_TOTAL"].',';
      }
    ?>];

    $('#grafica').highcharts({
        chart: {
            //type: 'spline'
            type: 'line'
        },
         title: {
            text: 'PRESUPUESTO <?=$d17_anio?> VS RESULTADO <?=$d17_anio?>  '
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 150,
            y: 100,
            floating: true,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        }, 

        yAxis: {
            lineWidth: 2,
            //min: 0,
            offset: 10,
            tickWidth: 1,
            title: {
                text: 'Monto'
            },
            labels: {
                formatter: function () {
                    return this.value;
                }
            }
        },
        tooltip: {
            shared: true,
            valueSuffix: ' '/*valor */
        },
        credits: {
            enabled: false
        },
        colors: ['#E51C23', '#003366'],
        plotOptions: {
            areaspline: {
                fillOpacity: 0.4
            }
        },
        xAxis: {
            tickmarkPlacement: 'on',
            gridLineWidth: 1,
            categories: categories   
        },
        series:  [{
            name: 'PRESUPUESTO 2018',
            data: data1,
            marker: {
                fillColor: '#3C8DBC',
                //lineWidth: 2,
                //lineColor: null // inherit from series
            }
        }, {
            name: 'RESULTADO 2018',
            data: data2,
            dashStyle: 'Dash',
            marker: {
            symbol: 'url(../dist/img/markerX.png)',
            width: 16,
            height: 16
            },
        }]

    });
});
</script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
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
$(document).ready(function() {
    $('#tabla1,#tabla2').DataTable({
      stateSave: true, 
      "ordering": true,
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
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
            title: 'Venta vs Presupuesto Promotores',  
          }, 

          {
            extend: 'print', 
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',  
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible', 
            }, 
            title: '<h5>Venta vs Presupuesto Promotores</h5>',
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
} );
</script>
<script type="text/javascript"> 
$(".select2").select2({
  placeholder: "Elija una opción",
  allowClear: true
});  
</script>
<script type="text/javascript">
/* AJAX SELECT ANIO */
$("#modal_d17_anio").click(function() {
  $.ajax({
    type: 'POST',
    url: '../action/venta_PromorAjax.php',
    cache:false,
    data: { "anio" : 1 },
    beforeSend: function () {
      $("#d17_anio").html("Procesando, espere por favor...");
    },
    success: function (response) {
      $("#d17_anio").html(response); 
    }
  });
});
/* AJAX SELECT PROMOTOR */
$("#modal_d17_promotor").click(function() {
  $.ajax({
    type: 'POST',
    url: '../action/venta_PromorAjax.php',
    cache:false,
    data: { "promotor" : 1, "anio" : <?= $d17_anio ?>, "d17_pro_pla" : <?= $d17_pro_pla ?> },
    beforeSend: function () {
      $("#d17_promotor").html("Procesando, espere por favor...");
    },
    success: function (response) {
      $("#d17_promotor").html(response);

      var dataJson = JSON.parse(response);
      var $select = $('#d17_promotor');
      $select.append('<option></option><option value="ALL">ALL</option>');
      $.each(dataJson, function(i, val){
        if ( val.N_TIPO == 2 ){
            var v_nombre = '('+val.IID_PLAZA+')-'+val.V_NOMBRE ; 
            var valor_option = val.IID_PLAZA;
        }else{
          if ( val.IID_PLAZA == 2 ){ 
          var v_nombre = '('+val.ID_PROMOTOR+')-'+'CORPORATIVOS';
          var valor_option = val.ID_PROMOTOR; 
          }else{ 
            var v_nombre = '('+val.ID_PROMOTOR+')-'+val.V_NOMBRE+' '+val.V_APELLIDO_PAT+' '+val.V_APELLIDO_MAT ;
            var valor_option = val.ID_PROMOTOR;
          }
        } 
        $select.append($('<option></option>').attr('value', valor_option).text(v_nombre));
      });


    }
  });
});
/* AJAX SELECT PLAZA */
$("#modal_d17_plaza").click(function() {
  $.ajax({
    type: 'POST',
    url: '../action/venta_PromorAjax.php',
    cache:false,
    data: { "plaza" : 1 },
    beforeSend: function () {
      $("#d17_plaza").html("Procesando, espere por favor...");
    },
    success: function (response) {
      $("#d17_plaza").html(response); 
    }
  });
});
</script>
<script type="text/javascript">
$(document).ready(function() { 

  $('#tabla2').each(function(){
    /*valor Presupuestado*/
    var vP0Script = $('.vP0Script').html().replace(/\$|\,/g, '');
    var vP1Script = $('.vP1Script').html().replace(/\$|\,/g, '');
    var vP2Script = $('.vP2Script').html().replace(/\$|\,/g, '');
    var vP3Script = $('.vP3Script').html().replace(/\$|\,/g, '');
    var vP4Script = $('.vP4Script').html().replace(/\$|\,/g, '');
    var vP5Script = $('.vP5Script').html().replace(/\$|\,/g, '');
    var vP6Script = $('.vP6Script').html().replace(/\$|\,/g, '');
    var vP7Script = $('.vP7Script').html().replace(/\$|\,/g, '');
    var vP8Script = $('.vP8Script').html().replace(/\$|\,/g, '');
    var vP9Script = $('.vP9Script').html().replace(/\$|\,/g, '');
    var vP10Script = $('.vP10Script').html().replace(/\$|\,/g, '');
    var vP11Script = $('.vP11Script').html().replace(/\$|\,/g, ''); 
    /*valor Real*/
    var vR0Script = $('.vR0Script').html().replace(/\$|\,/g, '');
    var vR1Script = $('.vR1Script').html().replace(/\$|\,/g, '');
    var vR2Script = $('.vR2Script').html().replace(/\$|\,/g, '');
    var vR3Script = $('.vR3Script').html().replace(/\$|\,/g, '');
    var vR4Script = $('.vR4Script').html().replace(/\$|\,/g, '');
    var vR5Script = $('.vR5Script').html().replace(/\$|\,/g, '');
    var vR6Script = $('.vR6Script').html().replace(/\$|\,/g, '');
    var vR7Script = $('.vR7Script').html().replace(/\$|\,/g, '');
    var vR8Script = $('.vR8Script').html().replace(/\$|\,/g, '');
    var vR9Script = $('.vR9Script').html().replace(/\$|\,/g, '');
    var vR10Script = $('.vR10Script').html().replace(/\$|\,/g, '');
    var vR11Script = $('.vR11Script').html().replace(/\$|\,/g, '');
    /*ASIGNA PORCENTAJE DE CUMPLIMIENTO*/
    $('.vC0Script').html(( (vR0Script/vP0Script) *100 ).toFixed(0)+'%');
    $('.vC1Script').html(( (vR1Script/vP1Script) *100 ).toFixed(0)+'%');
    $('.vC2Script').html(( (vR2Script/vP2Script) *100 ).toFixed(0)+'%');
    $('.vC3Script').html(( (vR3Script/vP3Script) *100 ).toFixed(0)+'%');
    $('.vC4Script').html(( (vR4Script/vP4Script) *100 ).toFixed(0)+'%');
    $('.vC5Script').html(( (vR5Script/vP5Script) *100 ).toFixed(0)+'%');
    $('.vC6Script').html(( (vR6Script/vP6Script) *100 ).toFixed(0)+'%');
    $('.vC7Script').html(( (vR7Script/vP7Script) *100 ).toFixed(0)+'%');
    $('.vC8Script').html(( (vR8Script/vP8Script) *100 ).toFixed(0)+'%');
    $('.vC9Script').html(( (vR9Script/vP9Script) *100 ).toFixed(0)+'%');
    $('.vC10Script').html(( (vR10Script/vP10Script) *100 ).toFixed(0)+'%');
    $('.vC11Script').html(( (vR11Script/vP11Script) *100 ).toFixed(0)+'%');
      
  }); 
 

});
</script>
<script type="text/javascript">
<?php  if ( $d17_promotor == 'ALL' ) { ?>
  console.log('ALL');
<?php }?>
$('#nomTabla2').html('TABLA PRESUPUESTO <?=$d17_anio?> VS RESULTADO <?=$d17_anio?> ');
</script>

<script type="text/javascript">
  $('.select2').on('change',function(){
  $.ajax({url: '../class/Venta_promotor.php', success: function(result){
    $('#modal_cargando').modal('show'); 
  }});
});
</script>
</html>
<?php conexion::cerrar($conn); ?>