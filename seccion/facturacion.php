<?php
ini_set('display_errors', false);

if( $_SERVER['REQUEST_METHOD'] == 'POST')
{ 
  header("location: ".$_SERVER["PHP_SELF"]." ");
  //return;
}

session_start();

//comprobar sesion iniciada
  if(!isset($_SESSION['usuario']))
  {
    header('Location: ../index.php');
  }

  //comprobar tiempo de expiracion
  $now = time();
  if($now > $_SESSION['expira']){
    session_destroy();
    header('Location: ../index.php');
  }

  if(isset($_POST['mesFactura']))
    $_SESSION['mesFactura'] = $_POST['mesFactura'];;

$mesFactura = $_SESSION['mesFactura'];

//objeto conexion a base de datos
include_once '../libs/conOra.php';
$conn   = conexion::conectar();

//clases para facturacion
include_once '../graficas/Facturacion.php';
include_once '../graficas/ArrayFacturacion.php';

$objFac   = new Facturacion();

//GRAFICA MES MES ANIO
$arrayFac2  = new ArrayFacturacion();
$arrayFac2  = $objFac->getGraficaMesMes(date("Y"), (date("Y")-1), $objFac->getMeses($mesFactura, 0, 1), $objFac->getMeses($mesFactura, 1, 1), '3, 4, 5, 6, 7, 8, 17, 18, 23');
$arrayCBA2  = $arrayFac2->getArrayCordoba();
$arrayMEX2  = $arrayFac2->getArrayMexico();
$arrayVER2  = $arrayFac2->getArrayGolfo();
$arrayMER2  = $arrayFac2->getArrayPeninsula();
$arrayPUE2  = $arrayFac2->getArrayPuebla();
$arrayQRO2  = $arrayFac2->getArrayBajio();
$arrayGDL2  = $arrayFac2->getArrayOccidente();
$arrayMTY2  = $arrayFac2->getArrayNoreste();
$arrayLEO2  = $arrayFac2->getArrayLeon();

//GRAFICA ACUMULADO MES ANIO
$arrayFac3  = new ArrayFacturacion();
$arrayFac3  = $objFac->getGraficaAcumulado($objFac->getAnios(2010, date("Y"), $mesFactura), $objFac->getMeses($mesFactura, 0, 2), '3, 4, 5, 6, 7, 8, 17, 18, 23');
$arrayCBA3  = $arrayFac3->getArrayCordoba();
$arrayMEX3  = $arrayFac3->getArrayMexico();
$arrayVER3  = $arrayFac3->getArrayGolfo();
$arrayMER3  = $arrayFac3->getArrayPeninsula();
$arrayPUE3  = $arrayFac3->getArrayPuebla();
$arrayQRO3  = $arrayFac3->getArrayBajio();
$arrayGDL3  = $arrayFac3->getArrayOccidente();
$arrayMTY3  = $arrayFac3->getArrayNoreste();
$arrayLEO3  = $arrayFac3->getArrayLeon();

//GRAFICA POR MES ANIO
$arrayFac4  = new ArrayFacturacion();
$arrayFac4  = $objFac->getGraficaPorMes($objFac->getAnios(2010, date("Y"), $mesFactura), $objFac->getMeses($mesFactura, 0, 3), '3, 4, 5, 6, 7, 8, 17, 18, 23');
$arrayCBA4  = $arrayFac4->getArrayCordoba();
$arrayMEX4  = $arrayFac4->getArrayMexico();
$arrayVER4  = $arrayFac4->getArrayGolfo();
$arrayMER4  = $arrayFac4->getArrayPeninsula();
$arrayPUE4  = $arrayFac4->getArrayPuebla();
$arrayQRO4  = $arrayFac4->getArrayBajio();
$arrayGDL4  = $arrayFac4->getArrayOccidente();
$arrayMTY4  = $arrayFac4->getArrayNoreste();
$arrayLEO4  = $arrayFac4->getArrayLeon();

//cliente activos 
$stid_cte_new = oci_parse($conn, "select count(*) from cliente t where to_char(t.d_fecha_alta_cliente, 'yyyy') = to_char(sysdate, 'yyyy') and t.s_status = 1");
oci_execute($stid_cte_new);

//convenios contratos activos
$stid_cto_new = oci_parse($conn, "select count(*) from co_convenio_contrato t where to_char(t.d_fecha_reg, 'yyyy') = to_char(sysdate, 'yyyy') and t.s_status = 2");
oci_execute($stid_cto_new);

//faturacion emitidas recientes
$sqlFacturacion =   " select "
          ." to_char(t.d_fecha_movto,'yyyy') as anio, "
          ." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as cargos, "
          ." sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as abonos, "
          ." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) - decode(sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))), null, 0, sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2)))) as total "
          ." from "
          ." ad_cxc_movtos t, "
          ." ad_fa_factura f "
          ." where "
          ." t.iid_plaza                               = f.iid_plaza                                                                       and "
          ." t.iid_folio                               = f.iid_folio                                                                       and "
          ." f.status                                  = 7                                                                                 and "
          ." t.n_status                                = 2                                                                                 and "
          ." t.n_tipo_movto                            in (1,3,4)                                                                          and "
          ." to_char(t.d_fecha_movto,'yyyy')           in (".date("Y").")                                                                  and "
          ." f.iid_plaza                               in (3, 4, 5, 6, 7, 8, 17, 18, 23)                                                   and "
          ." to_number(to_char(t.d_fecha_movto, 'mm')) = '11' and "
          ." to_number(to_char(t.d_fecha_movto, 'dd')) <= 5 "
          ." group by to_char(t.d_fecha_movto,'yyyy') ";

$stid_fac_new = oci_parse($conn, $sqlFacturacion);
oci_execute($stid_fac_new);

//certificados N emitidos recientes
$stid_cern_new = oci_parse($conn, "select count(*) from ad_ce_cert_n t where to_char(t.d_fecha_emision, 'yyyy') = to_char(sysdate, 'yyyy') and t.n_status = 1");
oci_execute($stid_cern_new);

//certificados S emitidos recientes
$stid_cer_new = oci_parse($conn, "select count(*) from ad_ce_cert_s t where to_char(t.d_fecha_emision, 'yyyy') = to_char(sysdate, 'yyyy') and t.n_status = 1");
oci_execute($stid_cer_new);

//grafica certificados n emitidos
$stid_graf_cern = oci_parse($conn, "select t.v_serie, to_char(t.d_fecha_emision, 'yyyy'), count(t.iid_num_cert_n) from ad_ce_cert_n t where to_char(t.d_fecha_emision, 'yyyy') in ('2010','2011', '2012', '2013', '2014', '2015', '2016') and t.v_serie is not null group by t.v_serie, to_char(t.d_fecha_emision, 'yyyy') order by t.v_serie, to_char(t.d_fecha_emision, 'yyyy')");
oci_execute($stid_graf_cern);

//grafica certificados n emitidos
$stid_graf_cers = oci_parse($conn, "select t.v_serie, to_char(t.d_fecha_emision, 'yyyy'), count(t.iid_num_cert_s) from ad_ce_cert_s t where to_char(t.d_fecha_emision, 'yyyy') in ('2010','2011', '2012', '2013', '2014', '2015', '2016') and t.v_serie is not null group by t.v_serie, to_char(t.d_fecha_emision, 'yyyy') order by t.v_serie, to_char(t.d_fecha_emision, 'yyyy')");
oci_execute($stid_graf_cers);
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
include_once '../class/Perfil.php';
$instacia_modulo  = new Perfil;

$modulos_valida = $instacia_modulo->modulos_valida($_SESSION['iid_empleado'], '3');
if($modulos_valida == 0){
  header('Location: index.php');
}
///////////////////////////////////////////
 

?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ##################################### Contenido de la pagina #########################-->
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
    Dashboard
    <small>Facturación neta</small>
    </h1>
    <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Dashboard</li>
    </ol>
    </section>

     <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

 

<!-- ######################################## Inicio de Widgets ######################################### -->
    <section><!-- Inicia la seccion de los Widgets -->
      <div class="row">
<div class="col-lg-3 col-xs-6">
<!-- small box -->
<div class="small-box bg-aqua">
<div class="inner">
<h3><?php $row = oci_fetch_array($stid_cte_new, OCI_BOTH); echo number_format($row[0]); ?></h3>

              <p>Clientes <?php echo date("Y"); ?></p>
            </div>
            <div class="icon">
              <!--  <i class="ion ion-bag"></i>-->
              <i class="ion ion-person-add"></i>
            </div>
            <!--  <a href="#" class="small-box-footer">Mas informacion <i class="fa fa-arrow-circle-right"></i></a>-->
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php $row = oci_fetch_array($stid_cto_new, OCI_BOTH); echo number_format($row[0]); ?></h3>

              <p>Contratos <?php echo date("Y"); ?></p>
            </div>
            <div class="icon">
              <!--  <i class="ion ion-stats-bars"></i>-->
              <i class="ion ion-compose"></i>
            </div>
            <!--  <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php $row = oci_fetch_array($stid_cern_new, OCI_BOTH); echo number_format($row[0]); ?></h3>
              <p>Certificados N <?php echo date("Y"); ?></p>
            </div>
            <div class="icon">
              <i class="ion ion-cash"></i>
            </div>
            <!--  <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php $row = oci_fetch_array($stid_cer_new, OCI_BOTH); echo number_format($row[0]); ?></h3>

              <p>Certificados S <?php echo date("Y"); ?></p>
            </div>
            <div class="icon">
              <i class="ion ion-card"></i>
            </div>
            <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
          </div>
        </div>
        <!-- ./col -->
      </div>
      <!-- /.row -->
    </section><!-- Termina la seccion de los Widgets -->
<!-- ######################################### Termino de Widgets ######################################### --> 

 



<!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& INICIA GRAFICA DE EFECTIVIDAD  &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->    
<section class="col-lg-7 connectedSortable">
          <!-- Custom tabs (Charts with tabs)-->
          <!--<div class="nav-tabs-custom">-->
            <!-- Tabs within a box -->
            <!--<ul class="nav nav-tabs pull-right">-->
              <!--<li class="active"><a href="#revenue-chart" data-toggle="tab">Area</a></li>-->
              <!--<li><a href="#sales-chart" data-toggle="tab">Donut</a></li>  -->
              <!--<li class="pull-left header"><i class="fa fa-inbox"></i> Facturacion por año</li>-->
            <!--</ul>-->
            <!--<div class="tab-content no-padding">-->
              <!-- Morris chart - Sales -->
              <!--<div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>-->
              <!--<div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;"></div>-->
            <!--</div>-->
          <!--</div>-->
          <!-- /.nav-tabs-custom --> 
          
          <!-- Input addon -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Lista de meses facturados cerrados:</h3>
            </div>
            <div class="box-body">
              
              <!-- /input-group -->
              <!--<p class="margin">Small <code>.input-group.input-group-sm</code></p>-->
      <form action="facturacion.php" method="post" >
              <div class="input-group input-group-sm">
                <!--<input type="text" class="form-control"> -->
                  <select id="mesFactura" name="mesFactura" class="form-control">
                  <?php 
                    for($i = (date("m")-1); $i > 0; $i--){
                      $selected = "";
                      if($mesFactura == str_pad($i, 2, "0", STR_PAD_LEFT))
                        $selected = "selected";
                      echo '<option value = "'.str_pad($i, 2, "0", STR_PAD_LEFT).'" '.$selected.'>'.$objFac->getMes(str_pad($i, 2, "0", STR_PAD_LEFT)).'</option>';
                    }
                  ?> 
                  </select>
                    <span class="input-group-btn">
                    <?php if($mesFactura > 0) { ?>
                    <button type="submit" class="btn btn-info btn-flat">Ok</button>
                    <?php }else{ ?>
                    <button type="button" class="btn btn-info btn-flat">Ok</button>
                    <?php } ?>
                    </span>
              </div>
              </form>
              <!-- /input-group -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->          
          
          <!-- AREA CHART -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Facturación anualizada al mes de <?=$objFac->getMes($mesFactura) ?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="chart" id="revenue-chart1" style="height: 510px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->   
                         
          
          <!-- AREA CHART -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Facturación acumulada al mes de <?=$objFac->getMes($mesFactura) ?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="chart" id="revenue-chart2" style="height: 505px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->                 
          
          <!-- AREA CHART -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Facturación del mes de <?=$objFac->getMes($mesFactura) ?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="chart" id="revenue-chart3" style="height: 505px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  




          <!-- quick email widget -->
          <div class="box box-info">
 
          </div>

        </section>
<!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& TERMINA GRAFICA DE EFECTIVIDAD  &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->



<!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& INICIA GRAFICA DE EFECTIVIDAD  &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->  
<section> 
  <div class="row">  
    <section class="col-lg-5 connectedSortable"> 


    <!-- Input addon -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Facturación neta a la fecha: <b><?=date("d")."-".$objFac->getMes(date("m"))."-".date("Y");?></b></h3> 
            </div>
            <div class="box-body" style="text-align: center;">
              <p><span class="badge bg-red"><?php $row = oci_fetch_array($stid_fac_new, OCI_BOTH); echo "$ ".number_format($row[3],2); ?></span></p>
              <!-- /input-group -->
              <!--<p class="margin">Small <code>.input-group.input-group-sm</code></p>-->

              <!-- /input-group -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  

          <!-- Facturacion neta por año TOTALES -->
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Facturación anualizada total al mes de <?=$objFac->getMes($mesFactura) ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <table class="table table-striped">
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Año</th>
                  <th>Mes</th>
                  <th style="width: 40px">Total</th>
                </tr>
                
            <?php 
             
           for($i=0;$i<sizeof($arrayCBA2);$i++){
            $totalAnioMes   = 0;
            $leonTotal    = 0;
            for($j=0;$j<sizeof($arrayLEO2);$j++){
              if ($arrayCBA2[$i]->getAnio() == $arrayLEO2[$j]->getAnio() and $arrayCBA2[$i]->getMes() == $arrayLEO2[$j]->getMes()){
                $leonTotal = $arrayLEO2[$j]->getTotal();
                break;
              }
                $leonTotal = 0;
              
            }       
                $totalAnioMes = $arrayCBA2[$i]->getTotal() + $arrayMEX2[$i]->getTotal() + $arrayVER2[$i]->getTotal() + $arrayMER2[$i]->getTotal() + $arrayPUE2[$i]->getTotal() + $arrayQRO2[$i]->getTotal() + $arrayGDL2[$i]->getTotal() + $arrayMTY2[$i]->getTotal();  
                echo "<tr>";
                echo "<td>".($i+1)."</td>";
                echo "<td>".$arrayCBA2[$i]->getAnio()."</td>";
                echo "<td>".$arrayCBA2[$i]->getMes()." - ".$objFac->getMes($arrayCBA2[$i]->getMes()) ."</td>";
                echo '<td style="text-align: right;"><span class="badge bg-green">$ '.number_format($totalAnioMes, 2).'</span></td>';
                echo "</tr>";
            }
            
          ?>                
    
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box --> 


          <!-- Facturacion neta por año TOTALES -->
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Facturación acumulada al mes de <?=$objFac->getMes($mesFactura) ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <table class="table table-striped">
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Año</th>
                  <th style="width: 40px">Total</th>
                </tr>
                
            <?php 
             
           for($i=0;$i<sizeof($arrayCBA3);$i++){
            $totalAnio  = 0;
            $leonTotal  = 0;
            for($j=0;$j<sizeof($arrayLEO3);$j++){
              if ($arrayCBA3[$i]->getAnio() == $arrayLEO3[$j]->getAnio() and $arrayCBA3[$i]->getMes() == $arrayLEO3[$j]->getMes()){
                $leonTotal = $arrayLEO3[$j]->getTotal();
                break;
              }
                $leonTotal = 0;
              
            }       
                $totalAnio = $arrayCBA3[$i]->getTotal() + $arrayMEX3[$i]->getTotal() + $arrayVER3[$i]->getTotal() + $arrayMER3[$i]->getTotal() + $arrayPUE3[$i]->getTotal() + $arrayQRO3[$i]->getTotal() + $arrayGDL3[$i]->getTotal() + $arrayMTY3[$i]->getTotal();  
                echo "<tr>";
                echo "<td>".($i+1)."</td>";
                echo "<td>".$arrayCBA3[$i]->getAnio()."</td>";
                echo '<td style="text-align: right;"><span class="badge bg-green">$ '.number_format($totalAnio, 2).'</span></td>';
                echo "</tr>";
            }
            
          ?>  
                        
                 <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
                <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
                <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
                <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
                <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
                <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box --> 
          
        <!-- Facturacion neta por año TOTALES -->
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Facturación del mes de <?=$objFac->getMes($mesFactura) ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <table class="table table-striped">
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Año</th>
                  <th style="width: 40px">Total</th>
                </tr>
                
            <?php 
             
           for($i=0;$i<sizeof($arrayCBA4);$i++){
            $totalAnio  = 0;
            $leonTotal  = 0;
            for($j=0;$j<sizeof($arrayLEO4);$j++){
              if ($arrayCBA4[$i]->getAnio() == $arrayLEO4[$j]->getAnio() and $arrayCBA4[$i]->getMes() == $arrayLEO4[$j]->getMes()){
                $leonTotal = $arrayLEO4[$j]->getTotal();
                break;
              }
                $leonTotal = 0;
              
            }       
                $totalAnio = $arrayCBA4[$i]->getTotal() + $arrayMEX4[$i]->getTotal() + $arrayVER4[$i]->getTotal() + $arrayMER4[$i]->getTotal() + $arrayPUE4[$i]->getTotal() + $arrayQRO4[$i]->getTotal() + $arrayGDL4[$i]->getTotal() + $arrayMTY4[$i]->getTotal();  
                echo "<tr>";
                echo "<td>".($i+1)."</td>";
                echo "<td>".$arrayCBA4[$i]->getAnio()."</td>";
                echo '<td style="text-align: right;"><span class="badge bg-green">$ '.number_format($totalAnio, 2).'</span></td>';
                echo "</tr>";
            }
            
          ?>                
                 <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
                <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
                <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
                <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
                <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
                <tr>
                  <td style="color: white;">.</td>
                  <td></td>
                  <td><span class="badge bg-light-blue"></span></td>
                </tr>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->      



    </section>
  </div> 
</section>  
<!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& TERMINA GRAFICA DE EFECTIVIDAD  &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
 

      

    </section><!-- Termina la seccion de Todo el contenido principal -->
  </div>
  <!-- /.content-wrapper -->
<!-- ##################################### Termina Contenido de la pagina #########################-->
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
<script src="../plugins/morris/morris.min.js"></script>

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
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="../dist/js/pages/dashboard.js"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<script type="text/javascript">

  /* Morris.js Charts */
  
  //FACTURACION POR AÑO
  // Sales chart
  var area = new Morris.Area({
    element: 'revenue-chart1',
    resize: true,
    data: [
        {y: '2016-01', item1: 3349187.58, item2: 3766785.83, item3: 1267233.46, item4: 546391.01, item5: 1301294.75, item6: 1105215.06, item7: 958233.09, item8: 143174.48, item9: 21811.23 },{y: '2016-02', item1: 4084566.97, item2: 3441052.76, item3: 1165552.15, item4: 561332.39, item5: 1245418.62, item6: 1240455.51, item7: 1658530.74, item8: 127376.29, item9: 29061.27 },{y: '2016-03', item1: 7501804.49, item2: 3661145.92, item3: 1149991.13, item4: 705137.47, item5: 1078099.70, item6: 1204937.39, item7: 1469848.21, item8: 162369.10, item9: 60105.99 },{y: '2016-04', item1: 8165117.05, item2: 3996984.39, item3: 1240594.72, item4: 630522.53, item5: 941058.03, item6: 983276.18, item7: 2665949.16, item8: 126016.83, item9: 116340.06 },{y: '2016-05', item1: 8922983.51, item2: 3985613.21, item3: 1153251.75, item4: 712481.58, item5: 820175.09, item6: 882759.99, item7: 2200319.85, item8: 98274.75, item9: 74557.26 },{y: '2016-06', item1: 7880189.78, item2: 3422123.97, item3: 1165491.34, item4: 776408.89, item5: 977865.92, item6: 978081.87, item7: 3307902.62, item8: 301730.21, item9: 63518.93 },{y: '2016-07', item1: 7371481.05, item2: 3075767.54, item3: 1303804.44, item4: 732309.12, item5: 917702.59, item6: 1377201.09, item7: 4276972.20, item8: 141568.82, item9: 98253.38 },{y: '2016-08', item1: 7800281.85, item2: 45268509.64, item3: 1332097.99, item4: 729487.20, item5: 893324.88, item6: 1194637.10, item7: 3673020.97, item8: 107790.47, item9: 146380.89 },{y: '2016-09', item1: 6089704.03, item2: 2926320.35, item3: 1233078.12, item4: 720793.58, item5: 1074158.06, item6: 1277334.27, item7: 3095706.13, item8: 106960.32, item9: 127363.57 },{y: '2016-10', item1: 4763385.31, item2: 2840723.36, item3: 1339962.04, item4: 655211.76, item5: 884654.81, item6: 1153511.58, item7: 3033602.96, item8: 101639.21, item9: 218508.99 },{y: '2016-11', item1: 4495017.01, item2: 2929031.11, item3: 1249566.25, item4: 581017.18, item5: 983211.58, item6: 1152527.45, item7: 2810144.08, item8: 104325.41, item9: 278576.99 },{y: '2016-12', item1: 4786763.60, item2: 2935429.89, item3: 1120421.19, item4: 729517.46, item5: 912839.43, item6: 1074397.53, item7: 3607566.53, item8: 99274.12, item9: 264931.13 }
    ],
    xkey: 'y',
    ykeys: ['item1', 'item2', 'item3', 'item4', 'item5', 'item6', 'item7', 'item8', 'item9'],
    labels: ['Cordoba', 'Mexico', 'Golfo', 'Peninsula', 'Puebla', 'Bajio', 'Occidente', 'Noreste', 'Leon'],
    //lineColors: ['#a0d0e0', '#00c0ef', '#80b5d3', '#f39c12', '#00a65a', '#f56954', '#d81b60', '#d2d6de', '#605ca8'],
    //lineColors: ['#3c8dbc', '#00a7d0', '#80b5d3', '#008d4c', '#3c8dbc', '#00a7d0', '#80b5d3', '#008d4c', '#3c8dbc'],
    lineColors: ['#4572a7', '#aa4643', '#89a54e', '#71588f', '#4198af', '#db843d', '#93a9cf', '#00a65a', '#00c0ef'],
    //lineColors: ['#0073b7', '#0073b7', '#0073b7', '#0073b7', '#0073b7', '#0073b7', '#0073b7', '#0073b7', '#0073b7'],
    hideHover: 'false',
    //pointSize: '0px',
    lineWidth: '1px',
    fillOpacity: '1'
  });

    // AREA CHART
    var area = new Morris.Area({
      element: 'revenue-chart2',
      resize: true,
      data: [
          {y: '2010', item1: 22269220.95, item2: 32017300.19, item3: 12350131.19, item4: 8597480.63, item5: 8948185.03, item6: 6302166.78, item7: 14483266.15, item8: 4350685.38, item9: 0 },{y: '2011', item1: 26512428.33, item2: 46814656.8, item3: 14394351.15, item4: 8043475.93, item5: 11976545.55, item6: 6836538, item7: 19618850.99, item8: 6110564.92, item9: 0 },{y: '2012', item1: 26027663.09, item2: 47261065.74, item3: 14033691.99, item4: 7716381.63, item5: 13010586.28, item6: 9910673.9, item7: 19838551.86, item8: 6095213.13, item9: 0 },{y: '2013', item1: 55179633.63, item2: 39620237.67, item3: 15695856.77, item4: 6441112.7, item5: 16955259.07, item6: 7735715.62, item7: 29431327.98, item8: 5463412.95, item9: 0 },{y: '2014', item1: 30500348.9, item2: 34531919.59, item3: 15290814.31, item4: 5804168.6, item5: 15763808.93, item6: 7219368.56, item7: 18174588.6, item8: 2434986.32, item9: 0 },{y: '2015', item1: 44167990.59, item2: 40540266.83, item3: 17904254.67, item4: 8077728.06, item5: 12989178.37, item6: 9195265.47, item7: 24252721.24, item8: 1677328.08, item9: 752955.08 },{y: '2016', item1: 75210482.23, item2: 82249487.97, item3: 14721044.58, item4: 8080610.17, item5: 12029803.46, item6: 13624335.02, item7: 32757796.54, item8: 1620500.01, item9: 1499409.69 }
        ],
        xkey: 'y',
        ykeys: ['item1', 'item2', 'item3', 'item4', 'item5', 'item6', 'item7', 'item8', 'item9'],
        labels: ['Cordoba', 'Mexico', 'Golfo', 'Peninsula', 'Puebla', 'Bajio', 'Occidente', 'Noreste', 'Leon'],
        //lineColors: ['#a0d0e0', '#00c0ef', '#3c8dbc', '#f39c12', '#00a65a', '#f56954', '#d81b60', '#d2d6de', '#605ca8'],ff851b
        lineColors: ['#4572a7', '#aa4643', '#89a54e', '#71588f', '#4198af', '#db843d', '#93a9cf', '#00a65a', '#00c0ef'],
        hideHover: 'auto',
        lineWidth: '1px',
        fillOpacity: '1'    
      });

  
    // AREA CHART
    var area = new Morris.Area({
      element: 'revenue-chart3',
      resize: true,
      data: [
              {y: '2010', item1: 1246502.74, item2: 2272496.96, item3: 961363.09, item4: 749752.67, item5: 1114013.62, item6: 594802.33, item7: 1236434.23, item8: 372273.22, item9: 0 },{y: '2011', item1: 1110825.94, item2: 4407128.07, item3: 2798195.78, item4: 739643.18, item5: 889291.91, item6: 859715.98, item7: 1252515.27, item8: 599857.85, item9: 0 },{y: '2012', item1: 2334826.07, item2: 5030735.03, item3: 1178799.84, item4: 531330.3, item5: 1193985.39, item6: 829123.83, item7: 1552329.09, item8: 277873.31, item9: 0 },{y: '2013', item1: 2383868.67, item2: 2120625, item3: 1205340.79, item4: 543123.46, item5: 1195016.71, item6: 739402.93, item7: 1451633.93, item8: 420664.19, item9: 0 },{y: '2014', item1: 2482902.06, item2: 3618272.21, item3: 887485.06, item4: 606450.31, item5: 1179209.48, item6: 535664.52, item7: 1024597.26, item8: 132025.88, item9: 0 },{y: '2015', item1: 1373008.95, item2: 3299456.36, item3: 1477398.3, item4: 432313.94, item5: 932751.61, item6: 958471.44, item7: 1079692.04, item8: 111321.32, item9: 122179.67 },{y: '2016', item1: 4786763.6, item2: 2935429.89, item3: 1120421.19, item4: 729517.46, item5: 912839.43, item6: 1074397.53, item7: 3607566.53, item8: 99274.12, item9: 264931.13 }
                ],
                xkey: 'y',
                ykeys: ['item1', 'item2', 'item3', 'item4', 'item5', 'item6', 'item7', 'item8', 'item9'],
                labels: ['Cordoba', 'Mexico', 'Golfo', 'Peninsula', 'Puebla', 'Bajio', 'Occidente', 'Noreste', 'Leon'],
                //lineColors: ['#a0d0e0', '#00c0ef', '#3c8dbc', '#f39c12', '#00a65a', '#f56954', '#d81b60', '#d2d6de', '#605ca8'],
                //lineColors: ['#3c8dbc', '#00a7d0', '#80b5d3', '#008d4c', '#3c8dbc', '#00a7d0', '#80b5d3', '#008d4c', '#3c8dbc'],
                lineColors: ['#4572a7', '#aa4643', '#89a54e', '#71588f', '#4198af', '#db843d', '#93a9cf', '#00a65a', '#00c0ef'],
                hideHover: 'auto',
                  lineWidth: '1px',
                  fillOpacity: '1'    
              });

    // if(isMobile.any())alert('hola');
  
</script>
<?php conexion::cerrar($conn); ?>
