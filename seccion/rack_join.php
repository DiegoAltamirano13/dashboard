<?php
session_start();
/*inicia code solucion quita mensaje reenvio de form*/
if( $_SERVER['REQUEST_METHOD'] == 'POST')
{ 
  header("location: ".$_SERVER["PHP_SELF"]." ");
}
/*termina code solucion quita mensaje reenvio de form*/
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], "18");
if($modulos_valida == 0)
{ 
  header('Location: index.php');
}
///////////////////////////////////////////
/*Class*/
include_once "../class/Dispositivo.php";
include_once "../class/Rack.php";
$objRack = new Rack();

/**----------------------sessiones----------------------**/
/*id_plaza*/
if ( isset($_POST["rackId_plaza"]) )
	$_SESSION['rackId_plaza'] = $_POST['rackId_plaza'];
	$id_plaza = @$_SESSION['rackId_plaza'];

/*id_almacen*/
if (  isset($_POST["rackId_almacen"]) )
	$_SESSION['rackId_almacen'] = $_POST['rackId_almacen'];
	$id_almacen = @$_SESSION['rackId_almacen']; 

/*id_cliente*/
if (  isset($_POST["rackId_cliente"]) )
	$_SESSION['rackId_cliente'] = $_POST['rackId_cliente'];
	$id_cliente = @$_SESSION['rackId_cliente']; 

/*mercancia*/
if (  isset($_POST["rackId_mercancia"]) )
	$_SESSION['rackId_mercancia'] = $_POST['rackId_mercancia'];
	$mercancia = @$_SESSION['rackId_mercancia']; 

/*letraRack*/
if (  isset($_POST["letraRack"]) )
	$_SESSION['letraRack'] = $_POST['letraRack'];
	$letraRack = @$_SESSION['letraRack'];

?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>

<style type="text/css">
/*TABLA PARA EL RACK*/
.tablaRack {
        width: 110px;
        height: 172px; /* Ancho y alto fijo */
        overflow: hidden; /* Se oculta el contenido desbordado */
        background-color: #efefef;
        /*border: 2px solid #b2b2b2;*/
    }
</style>

<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
 
    <?php
	if ($tablet_browser > 0 || $mobile_browser > 0) {
	// Si es tablet o movil has lo que necesites
	   $medidaM = "250px";
	   $medidaD = "400px";
	}else {
	// Si es ordenador de escritorio has lo que necesites
	   $medidaM = "1120px";
	   $medidaD = "600px";
	}  
	?>
	<!-- ############################ INICIA SECCION FILTROS DISPONIBLES ############################# --> 
	<section>
	  <div class="box box-info">    
	    <div class="box-header with-border">
	      <h3 class="box-title"><i class="fa fa-filter"></i> Filtros Disponibles </h3>
	      <div class="box-tools pull-right">
	        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	      </div>
	    </div>
	    <div class="box-body"><!--box-body-->

	    	<!-- SELECT OPTION PLAZA -->
	    	<div class="col-md-2 col-sm-2 col-xs-12 invoice-col">
	    	<form method="post">
		        <strong><i class="fa fa-cube"></i> Plaza:</strong>
		        <address>
		          <select class="form-control select2" name="rackId_plaza" style='width: 100%;' onchange='this.form.submit()'>
		          <option selected="true" disabled>Seleccione la Plaza</option>
		          <?php 
			        $selectPlaza = $objRack->selectPlaza();
			        for ($i=0; $i <count($selectPlaza) ; $i++) {
			      ?>
			      <option <?php if (@$id_plaza == $selectPlaza[$i]["IID_PLAZA"]) echo "selected"; ?> value="<?=$selectPlaza[$i]["IID_PLAZA"]?>">
			      	(<?=$selectPlaza[$i]["IID_PLAZA"]?>)--<?=$selectPlaza[$i]["V_RAZON_SOCIAL"]?>
			      </option>
			      <?php
			        }
			      ?>
		          </select>
		          <input type="hidden" name="rackId_almacen" value="">
		          <input type="hidden" name="rackId_cliente" value="">
		          <input type="hidden" name="rackId_mercancia" value="">
		          <input type="hidden" name="letraRack" value="">
		        </address>
		    </form>
		    </div>
		    <!-- SELECT OPTION ALMACEN -->
	    	<div class="col-md-2 col-sm-2 col-xs-12 invoice-col">
	    	<form method="post">
		        <strong><i class="fa fa-cubes"></i> Almacen:</strong>
		        <address>
		          <select class="form-control select2" name="rackId_almacen" style='width: 100%;' onchange='this.form.submit()'>
		          <option selected="true" disabled>Seleccione el Almacen</option>
		          <?php
		          if ( isset($id_plaza) && !empty($id_plaza) ){//if
		          	$selectAlmacen = $objRack->selectAlmacen($id_plaza);
		          	for ($i=0; $i <count($selectAlmacen) ; $i++) {// for
		          ?>
		          <option <?php if (@$id_almacen == $selectAlmacen[$i]["IID_ALMACEN"]) echo "selected"; ?> value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>">
			      	(<?=$selectAlmacen[$i]["IID_ALMACEN"]?>)--<?=$selectAlmacen[$i]["V_NOMBRE"]?>
			      </option>
		          <?php
		          	}// /.for
		          }// /.if
		          ?>
		          </select>
			      <input type="hidden" name="rackId_cliente" value="">
			      <input type="hidden" name="rackId_mercancia" value="">
			      <input type="hidden" name="letraRack" value="">
		        </address>
		    </form>
		    </div>
		    <!-- SELECT OPTION CLIENTE -->
	    	<div class="col-md-3 col-sm-3 col-xs-12 invoice-col">
	    	<form method="post">
		        <strong><i class="fa fa-users"></i> Cliente:</strong>
		        <address>
		          <select class="form-control select2" name="rackId_cliente" style="width: 100%;" onchange='this.form.submit()'>
		          <option selected="true" disabled>Seleccione el Cliente</option>
		          <?php
		          if ( isset($id_almacen) && !empty($id_almacen) ){//if
		          	$selectCliente = $objRack->selectCliente($id_almacen);
		          	if ($id_cliente == 'ALL'){ echo '<option selected value="ALL">ALL</option>';}else{echo '<option value="ALL">ALL</option>';}
		          	
		          	for ($i=0; $i <count($selectCliente) ; $i++) {// for
		          ?>
		          <option <?php if (@$id_cliente == $selectCliente[$i]["IID_NUM_CLIENTE"]) echo "selected"; ?> value="<?=$selectCliente[$i]["IID_NUM_CLIENTE"]?>">
			      	(<?=$selectCliente[$i]["IID_NUM_CLIENTE"]?>)--<?=$selectCliente[$i]["V_RAZON_SOCIAL"]?>
			      </option>
		          <?php
		          	}// /.for
		          }// /.if
		          ?>
		          </select>
		        </address>
		    </form>
		    </div>
		    <!-- SELECT OPTION MERCANCIA -->
	    	<div class="col-md-3 col-sm-3 col-xs-12 invoice-col">
			<form method="post">	    		
		        <strong><i class="fa fa-dropbox"></i> Mercancia:</strong>
		        <address>
		          <select class="form-control select2" name="rackId_mercancia" style="width: 100%;" onchange='this.form.submit()'>
		          <option selected="true" disabled>Seleccione Tipo de Mercancia</option>
		          <?php if ( isset($id_cliente) && !empty($id_cliente) ){//if ?>
		          <option <?php if(@$mercancia == 'ALL') echo 'selected'; ?> value="ALL">ALL</option>
		          <option <?php if(@$mercancia == 'FISCAL') echo 'selected'; ?> value="FISCAL">FISCAL</option>
		          <option <?php if(@$mercancia == 'NACIONAL') echo 'selected'; ?> value="NACIONAL">NACIONAL</option>
		          <?php }// /.if ?>
		          </select>
		        </address>
		    </form>
		    </div>
		    <!-- SELECT OPTION RACK -->
	    	<div class="col-md-2 col-sm-2 col-xs-12 invoice-col">
	    	<form method="post">
		        <strong><i class="fa fa-table"></i> Rack:</strong>
		        <address>
		          <select class="form-control select2" name="letraRack" style='width: 100%;' onchange='this.form.submit()'>
		          <option selected="true" disabled>Seleccione Letra del Rack</option>
		          <?php
		          if ( isset($mercancia) && !empty($mercancia) ){//if
		          	$selectRack = $objRack->selectRack($id_almacen);
		          	if ($letraRack == 'TODOS'){ echo '<option selected value="TODOS">TODOS</option>';}else{echo '<option value="TODOS">TODOS</option>';}
		          	
		          	for ($i=0; $i <count($selectRack) ; $i++) {// for
		          ?>
		          <option <?php if (@$letraRack == $selectRack[$i]["BATERIA"]) echo "selected"; ?> value="<?=$selectRack[$i]["BATERIA"]?>">
			      	<?=$selectRack[$i]["BATERIA"]?>
			      </option>
		          <?php
		          	}// /.for
		          }// /.if
		          ?>
		          </select>
		        </address>
		    </form>
		    </div>
		    <!-- /.SELECT OPTION -->

	    </div><!--/.box-body--> 
	  </div> 
	</section> 
	<!-- ########################### TERMINA SECCION FILTROS DISPONIBLES ########################### -->


<?php if ( isset($letraRack) && !empty($letraRack) ){// if consulta posicion ?>
	<!-- ############################## INICIA SECCION IZQUIERDA DERECHA ############################## -->
	<div class="row">



		<!-- =========== INICIA SECCION MERCANCIA DERECHA (BUSQUEDA DE MERCANCIA) =========== -->
		<section class="col-lg-3 connectedSortable"><!-- col-lg-3 -->

			<!-- INICIA SECCION BUSQUEDA -->
            <section>
              	<div class="row">
            	<div class="col-xs-12">
                 	<div class="box box-primary box-solid"><!-- box -->
                    <div class="box-header"><!-- box-header -->  
                      <h5 class="box-title"><i class="fa fa-search"></i> Buscar ubicación</h5>
                      <div class="box-tools">
                         <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      </div>
                    </div><!-- /.box-header -->
                    <div class="box-body"><!-- box-body buscar -->

					<div class="form">
				        <div class="input-group">
				          	<input type="search" title="Buscar por: Oupado-Libre-Ubicación-Arribo-Cliente-Contenedor-Recibo-Certificado-Núm. Ped.-Núm. Parte-Lote-Serie-Descripción" placeholder="Oupado-Libre-Ubicación-Arribo-Cliente-Contenedor-Recibo-Certificado-Núm. Ped.-Núm. Parte-Lote-Serie-Descripción" class="form-control search-derecha">
				            <span class="input-group-btn">
				            	<button type="button" class="btn btn-flat bg-blue search-btn"><i class="fa fa-search text-gray"></i></button> 
				            </span>
				        </div>
				    </div>

				    <div class="box-footer">
                        <div class="row">
                          <!-- /.col -->
                          <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                              <span class="description-percentage text-info"><p id="valPosiciones">0</p></span>
                              <small class="description-header" style="font-size:12px;">Posiciones</small>
                            </div>
                            <!-- /.description-block -->
                          </div>
                          <!-- /.col -->
                          <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                              <span class="description-percentage text-green"><p id="valLibre">0</p></span>
                              <small class="description-header" style="font-size:12px;">Libres</small>
                            </div>
                            <!-- /.description-block -->
                          </div>
                          <!-- /.col -->
                          <div class="col-sm-3 col-xs-6">
                            <div class="description-block">
                              <span class="description-percentage text-blue"><p id="valOcupado">0</p></span>
                              <small class="description-header" style="font-size:12px;">Ocupadas</small>
                            </div>
                            <!-- /.description-block -->
                          </div>
                          <!-- /.col -->
                          <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                              <span class="description-percentage text-yellow" title="Arribos no Ubicados"><p id="valNoUbi">0</p></span>
                              <small class="description-header" style="font-size:12px;" title="Arribos no Ubicados">No Ubicado</small>
                            </div>
                            <!-- /.description-block -->
                          </div>
                          <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>

                    <div class="table-responsive" style="height: <?=$medidaM?>; overflow: auto;"><!-- overflow #3 -->

                    	<ul id="listaDe" class="products-list product-list-in-box">
		                <!-- /.item -->
		              	</ul>

                  	</div><!-- /.overflow #3 -->

                    </div><!-- /.box-body buscar --> 
                	</div><!-- /.box -->
                </div>
              	</div>
            </section>
            <!-- TERMINA SECCION BUSQUEDA -->


		</section><!-- /.col-lg-3 -->
		<!-- =========== TERMINA SECCION MERCANCIA DERECHA (BUSQUEDA DE MERCANCIA) =========== -->




		<!-- =========== INICIA SECCION MERCANCIA IZQUIERDA (MERCANICA UBICADA-NO UBICADA) =========== -->
		<section class="col-lg-9 connectedSortable"><!-- col-lg-9 -->
  			
  			<!-- INICIA SECCION MERCANCIA UBICADA -->
            <section>
              <div class="row">
                <div class="col-xs-12">
                  <div class="box box-default box-solid"><!-- box -->
                    <div class="box-header"><!-- box-header -->  
                      <h3 class="box-title"><i class="fa fa-table"></i> Mercancía Ubicada en Rack </h3>
              
                      <div class="box-tools">
                         <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      </div>
                    </div><!-- /.box-header -->  
              
                    <div class="box-body"><!-- box-body rack dibujo -->

                    <script type="text/javascript">
					function DetalleUbica(id_plaza,id_almacen,id_cliente,mercancia,rack,profundidad){
						//console.log(id_plaza+" -- "+id_almacen+" -- "+id_cliente+" -- "+mercancia+" -- "+rack+" -- "+profundidad);
						$.ajax({
                            type: 'POST',
	                        url: '../action/rackAjax.php',
	                        cache:false,
                            data: {"DibMerca" : 1, "id_plaza" : id_plaza, "id_almacen" : id_almacen, "id_cliente" : id_cliente, "mercancia" : mercancia, "rack" : rack, "profundidad" : profundidad},
                            success: function (response) {//success
                            var dataJson = JSON.parse(response);
                            //console.log(dataJson);
                            var li = [];

                            for(var k in dataJson) {
							   	//console.log( (dataJson[k].DETALLE1+dataJson[k].DETALLE2) );
								var v_ubicacion = dataJson[k].V_DESCRIPCION,
								v_detalle = $.trim(dataJson[k].DETALLE1+" "+dataJson[k].DETALLE2),
	               		    	s_bateria = v_ubicacion.substr(0, 1),
	                   		    s_columna = v_ubicacion.substr(1, 2),
	                   		    s_nivel = v_ubicacion.substr(3, 2),
	                   		    s_posicion = v_ubicacion.substr(5, 2),
	                   		    s_profundidad = v_ubicacion.substr(7, 2);

	                   		    if ( v_detalle == "" ){
	                   		    	$("#imgUrl"+v_ubicacion).replaceWith('<a class="fancybox fancybox.iframe" href="rack_detubi.php?detUbi='+v_ubicacion+'&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>"><img class="v_libre" id="imgUrl'+v_ubicacion+'" data-info="libre '+(v_ubicacion).toLowerCase()+'" style="vertical-align: text-bottom;" width="40" height="22" src="../dist/img/rack/pallet.png" ></a>');
	                   		    	li[k] = '<li class="item"><div class="product-img"><img class="direct-chat-img img-bordered-sm" src="../dist/img/rack/cajaico_libre.jpg" alt="img"></div><div class="product-info"><a class="product-title">Ubicación: '+v_ubicacion+'<span class="label label-success pull-right">Estado: LIBRE</span></a><ul class="product-description"><li>Bateria: '+s_bateria+'</li><li>Columna: '+s_columna+'</li><li>Nivel: '+s_nivel+'</li><li>Posición: '+s_posicion+'</li><li>Profundidad: '+s_profundidad+'</li></ul></div></li>';
	                   		    }else{
	                   		    	$("#imgUrl"+v_ubicacion).replaceWith('<a class="fancybox fancybox.iframe" href="rack_detubi.php?detUbi='+v_ubicacion+'&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>"><img class="v_ocupado" id="imgUrl'+v_ubicacion+'" data-info="ocupado '+(v_detalle).toLowerCase()+'" style="vertical-align: text-bottom;" width="40" height="20" src="../dist/img/rack/palletTrue.png" ></a>');
	                   		    	li[k] = '<li class="item"><div class="product-img"><img class="direct-chat-img img-bordered-sm" src="../dist/img/rack/cajaico.jpg" alt="img"></div><div class="product-info"><a class="product-title">Ubicación: '+v_ubicacion+'<span class="label bg-blue pull-right">Estado: OCUPADO</span></a><ul class="product-description"><li>Bateria: '+s_bateria+'</li><li>Columna: '+s_columna+'</li><li>Nivel: '+s_nivel+'</li><li>Posición: '+s_posicion+'</li><li>Profundidad: '+s_profundidad+'</li></ul></div></li>';
	                   		    }
							}
							$(li.join('')).appendTo('#listaDe');

                            }//.success
                        });
					}	
					</script>

                    <div style="height: <?=$medidaD?>; overflow: auto;"><!-- overflow #1 -->

                    	<?php
                    	$rackProfundidad = $objRack->rackProfundidad($id_almacen,$letraRack);
                    	for ($i=0; $i <count($rackProfundidad) ; $i++) {// for rackProfundidad 
                    	?>

                    	<div class="table-responsive"><!-- table-responsive -->
                    		<table><!-- table principal -->
                            <caption><small class="pull-right-container badge" style="background-color:<?=$rackProfundidad[$i]["COLOR"]?>;">
                            	RACK <?=$rackProfundidad[$i]["RACK"]?> PROFUNDIDAD <?=$rackProfundidad[$i]["PROFUNDIDAD"]?>
                            </small> </caption>
                            <tr><!-- ******* TR DEFINE RACK PROFUNDIDAD ******* -->

<!-- ===================================================== CONSULTA DE COLUMNAS DENTRO DE DEL FOR ===================================================== -->
							<?php 
							$rackColumna = $objRack->rackColumna($id_almacen,$rackProfundidad[$i]["RACK"],$rackProfundidad[$i]["PROFUNDIDAD"]);
							for ($a=0; $a <count($rackColumna) ; $a++) {// for columnas
							?>
                        	<td><!-- ******* TD DEFINE COLUMNAS ******* -->
	                        <small class="pull-right-container badge" style="background-color: <?=$rackProfundidad[$i]["COLOR"]?>;">COLUMNA <?=$rackColumna[$a]["COLUMNA"]?></small>
	                        <table class="tablaRack" style="background:#fff url('../dist/img/rack/columna.png'); background-size:100% 100%;"  border="0" width="250">

<!-- ****************************************************** CONSULTA DE NIVEL DENTRO DE DEL FOR ****************************************************** -->
							<?php 
							$rackNivel = $objRack->rackNivel($id_almacen,$rackProfundidad[$i]["RACK"],$rackProfundidad[$i]["PROFUNDIDAD"],$rackColumna[$a]["COLUMNA"]);
							for ($b=0; $b <count($rackNivel) ; $b++) {// for nivel
							?>
							<tr><!-- TR DEFINE NIVEL -->

<!-- /////////////////////////////////////////////////// CONSULTA DE POSICION DENTRO DE DEL FOR /////////////////////////////////////////////////// -->
							 
							
							<?php
							$v_posicion = explode(",", substr($rackNivel[$b]["POSICION"],0,-1));
							for ($c=0; $c <count($v_posicion) ; $c++) { 
							?>

							<td title="Rack: <?=$rackNivel[$b]["RACK"]?> Nivel: <?=$rackNivel[$b]["NIVEL"]?> &#10; Posición: <?=$v_posicion[$c]?>  Profundidad: <?=$rackNivel[$b]["PROFUNDIDAD"]?>" style="border-bottom: 3px solid #054A8B;" valign="bottom" align="center"><!-- TD DEFINE POSICION -->
                  			
                        	<img id="imgUrl<?=$rackNivel[$b]["RACK"].$rackNivel[$b]["COLUMNA"].$rackNivel[$b]["NIVEL"].$v_posicion[$c].$rackNivel[$b]["PROFUNDIDAD"]?>" >

                        	</td><!-- /.TD DEFINE POSICION -->

                        	<?php } ?>


<!-- /////////////////////////////////////////////////// /.CONSULTA DE POSICION DENTRO DE DEL FOR /////////////////////////////////////////////////// -->


							</tr><!-- /.TR DEFINE NIVEL -->
							<?php }// /.for nivel ?>
<!-- ****************************************************** /.CONSULTA DE NIVEL DENTRO DE DEL FOR ****************************************************** -->	                        	

	                        </table>
	                    	</td><!-- ******* /.TD DEFINE COLUMNAS ******* -->
	                    	<?php }// /.for columnas ?>
<!-- ===================================================== /.CONSULTA DE COLUMNAS DENTRO DE DEL FOR ===================================================== -->	                    	


                            </tr><!-- ******* TR DEFINE RACK PROFUNDIDAD ******* -->
                        	</table><!-- /.table principal -->
                    	</div><!-- /.table-responsive -->

                    	<script type="text/javascript">
                    		DetalleUbica(<?=$id_plaza?>,<?=$id_almacen?>,'<?=$id_cliente?>','<?=$mercancia?>','<?=$rackProfundidad[$i]["RACK"]?>','<?=$rackProfundidad[$i]["PROFUNDIDAD"]?>');
                    	</script>

                    	<?php }// /.for rackProfundidad ?>

                    </div><!-- /.overflow #1 -->
                    </div><!-- /.box-body rack dibujo -->
                  </div><!-- /.box -->
                </div>
              </div>
            </section>
            <!-- TERMINA SECCION MERCANCIA UBICADA -->



            <!-- INICIA SECCION MERCANCIA NO UBICADA -->
            <section>
              <div class="row">
                <div class="col-xs-12">
                  <div class="box box-default box-solid"><!-- box -->
                    <div class="box-header"><!-- box-header -->  
                      <h3 class="box-title"><i class="fa fa-dropbox"></i> Mercancía no Ubicada </h3>
              
                      <div class="box-tools">
                         <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      </div>
                    </div><!-- /.box-header -->  
              
                    <div class="box-body"><!-- box-body rack dibujo -->
                    <div style="height: <?=$medidaD?>; overflow: auto;"><!-- overflow #2 -->

                    <style type="text/css">
                    .liMerNoUbi li {
                    	width: 65px;
                      	height: 80px;
                      	margin: 3px;
                    } 
                  	</style>

                  	<!-- incia li mercancia no ubicada -->
	                  <div class="row liMerNoUbi">
	                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 center">
	                      <ul class="users-list"><!-- users-list -->

	                      	<script type="text/javascript">
	                      	var liNoUbi = [];
	                      	</script>
	                      	<!-- inicia for li mercancia no ubicada -->
	                      	<?php
	                      	$mercanciaNoUbi = $objRack->mercanciaNoUbi($id_plaza,$id_almacen,$id_cliente,$mercancia);
	                      	for ($i=0; $i <count($mercanciaNoUbi) ; $i++) {// for mercanciaNoUbi
	                      	?>
							<li>						
							  <a class="fancybox fancybox.iframe" href="rack_detubi.php?detArr=<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>">
							  <img class="v_noubi" data-info="no ubicado <?=strtolower($mercanciaNoUbi[$i]["ID_ARRIBO"].' '.$mercanciaNoUbi[$i]["V_DETALLE"])?>" width="60" height="60" src="../dist/img/rack/palletNoUbi.png"></a>
							  <span style="font-size:10px;" class="label bg-gray"><?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?></span>
		                    </li>
		                    <script type="text/javascript">
		                    	liNoUbi[<?=$i?>] = '<li class="item"><div class="product-img"><img class="direct-chat-img img-bordered-sm" src="../dist/img/rack/cajaico_noUbi.jpg" alt="img"></div><div class="product-info"><a class="product-title">Arribo: <?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?><span class="label label-warning pull-right">Estado: NO UBICADO</span></a><ul class="product-description"><li>Arribo por Ubicar</li></ul></div></li>';
		                    </script>
		                    <?php }// /.for mercanciaNoUbi ?>
		                    <!-- /.inicia for li mercancia no ubicada --> 
						
	                      </ul><!-- /.users-list -->
	                    </div>
	                  </div>
	                <!-- termina li mercancia no ubicada -->

	                <script type="text/javascript">
	                	$(liNoUbi.join('')).appendTo('#listaDe');
	                </script>

                    </div><!-- /.overflow #2 -->
                    </div><!-- /.box-body rack dibujo -->
                  </div><!-- /.box -->
                </div>
              </div>
            </section>
            <!-- TERMINA SECCION MERCANCIA NO UBICADA -->

            
          
		</section><!-- /.col-lg-9 -->
		<!-- =========== TERMINA SECCION MERCANCIA IZQUIERDA (MERCANICA UBICADA-NO UBICADA) =========== -->




		<script>
		$( document ).ajaxStop(function() {
		  console.log("termino toda peticion ajax");
		  operacion();
		});
		</script>




	</div>
	<!-- ############################## TERMINA SECCION IZQUIERDA DERECHA ############################## -->
<?php }// /.if consulta posicion ?> 
      

    </section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->
  </div><!-- Termina etiqueta content-wrapper principal --> 
<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<?php if ( isset($letraRack) && !empty($letraRack) ){// if consulta posicion ?>
<script type="text/javascript">
var CLICK = {
    onReady : function() {
    	operacion();
        $(".search-btn").click(function (){
			operacion();
		});
    },
};
$(document).ready(CLICK.onReady);

function operacion(){
	
	var v_libre = $(".v_libre ").length,
	v_ocupado = $(".v_ocupado ").length,	
	v_noubi = $(".v_noubi ").length,
	v_posicion = v_libre + v_ocupado;

	document.getElementById("valPosiciones").innerHTML = v_posicion;
	document.getElementById("valLibre").innerHTML = v_libre;
	document.getElementById("valOcupado").innerHTML = v_ocupado;
	document.getElementById("valNoUbi").innerHTML = v_noubi;

}
</script>
<?php }// /.if consulta posicion ?>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<?php
if ( $mobile_browser <= 0) {
// Si es ordenador de escritorio has lo que necesites
?>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
  });
</script>
<?php } ?>
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
<script src="../plugins/pace/pace.js"></script>
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