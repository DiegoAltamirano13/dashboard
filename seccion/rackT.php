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
<link rel="stylesheet" href="../plugins/jquery-mobile/jquery.mobile.structure-1.4.5.min.css">
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>

<style type="text/css">
/*TABLA PARA EL RACK*/
.tablaRack {
    width: 110px;
    height: 172px; /* Ancho y alto fijo */
    overflow: hidden; /* Se oculta el contenido desbordado */
    background-color: #fff;
    /*border: 2px solid #b2b2b2;*/
}

.someRedStuff
{
 background:red !important;
}
</style>

<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
 

	<!-- ############################ INICIA SECCION FILTROS DISPONIBLES ############################# --> 
	<section>
	  <div class="box box-info">    
	    <div class="box-header with-border">
	      <h5 class="box-title"><i class="fa fa-search"></i> Filtros y Búsquedas de Mercancía</h5>
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
		          	$selectCliente = $objRack->selectCliente($id_plaza,$id_almacen);
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


            <div class="input-group">
              <span class="input-group-addon">Buscar</span>
              <input type="search" title="Buscar por: Oupado-Libre-Ubicación-Arribo-Cliente-Contenedor-Recibo-Certificado-Núm. Ped.-Núm. Parte-Lote-Serie-Descripción" placeholder="Oupado-Libre-Ubicación-Arribo-Cliente-Contenedor-Recibo-Certificado-Núm. Ped.-Núm. Parte-Lote-Serie-Descripción" class="form-control search-derecha">
                <span class="input-group-btn">
              <span class="input-group-btn">
                  <button type="button" class="btn btn-flat bg-blue search-btn"><i class="fa fa-search text-gray"></i></button> 
              </span>
            </div>

            <code class="titleCoin" style="display: none">Coincidencias encontradas:</code>

            <p id="lis_span" style="height: 70px; overflow: auto;"></p>

	    </div><!--/.box-body-->

	    <?php if ( $id_cliente <> 'ALL' ){  ?>
	    <div class="box-footer">
            <div class="row">
              <!-- /.col -->
              <div class="col-sm-4 col-xs-4">
                <div class="description-block border-right">
                  <span class="description-percentage text-info"><p id="valPosiciones">0</p></span>
                  <small class="description-header" style="font-size:12px;">Posiciones Totales en Almacen</small>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-4 col-xs-4">
                <div class="description-block">
                  <span class="description-percentage text-blue"><p id="valOcupado">0</p></span>
                  <small class="description-header" style="font-size:12px;">Ocupadas</small>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-4 col-xs-4">
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
        <?php }else{ ?>
        <div class="box-footer">
            <div class="row">
              <!-- /.col -->
              <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                  <span class="description-percentage text-info"><p id="valPosiciones">0</p></span>
                  <small class="description-header" style="font-size:12px;">Posiciones Totales en Almacen</small>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                  <span class="description-percentage text-green"><p id="valLibre">0</p></span>
                  <small class="description-header" style="font-size:12px;" title="">Libres</small>
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
        <?php } ?>

	  </div> 
	</section> 
	<!-- ########################### TERMINA SECCION FILTROS DISPONIBLES ########################### -->


<?php if ( isset($letraRack) && !empty($letraRack) ){// if consulta posicion ?>
	<!-- ############################## INICIA SECCION IZQUIERDA DERECHA ############################## -->
	<div class="row">
		<!-- =========== INICIA SECCION MERCANCIA IZQUIERDA (MERCANICA UBICADA-NO UBICADA) =========== -->
		<section class="col-lg-12 connectedSortable"><!-- col-lg-9 -->
  			
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
                            //var span = [];

                            for(var k in dataJson) {
							   	//console.log( (dataJson[k].DETALLE1+dataJson[k].DETALLE2) );
								var v_ubicacion = dataJson[k].V_DESCRIPCION;
								var v_detalle = $.trim(dataJson[k].DETALLE1+" "+dataJson[k].DETALLE2);

	                   		    if ( v_detalle == "" ){
	                   		    	$("#imgUrl"+v_ubicacion).replaceWith('<a class="fancybox fancybox.iframe" href="rack_detubi.php?detUbi='+v_ubicacion+'&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>"><img class="v_libre itemMer" id="imgUrl'+v_ubicacion+'" data-info="libre '+(v_ubicacion).toLowerCase()+'" style="vertical-align: text-bottom;" width="40" height="22" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB8AAAAOCAYAAADXJMcHAAAACXBIWXMAAAE7AAABOwEf329xAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAACtSURBVHja7NQ7igJREIXhr+VqoxvwERk4odtwGZpP7Cpcg6FgbGIuiGDgCiYyVyYYJhhHaZMWGrE1bJA+WR3uqb+KCxUlSaIoVRSoEl7CS/j7w8N+PRuji/+MHz3J5J3E6AUrmwv4ChiiX8Di24C/O/OCOXaoZ/wz2vhE/KDZBov0K6NMpoYRPu7enwIO+E6HqOIXU6weAFoYoJk2vqmBJSY5W/bQwU9axzheBwADvh4qHwKphwAAAABJRU5ErkJggg==" ></a>');
	                   		    	//span[k] = '<a class="itemDer" style="display: none;" href="#imgUrl'+v_ubicacion+'" data-info="libre '+(v_ubicacion).toLowerCase()+'"><span class="label label-success">Ubicación: '+v_ubicacion+'</span><a/>';

	                   		    }else{
	                   		    	$("#imgUrl"+v_ubicacion).replaceWith('<a class="fancybox fancybox.iframe" href="rack_detubi.php?detUbi='+v_ubicacion+'&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>"><img class="v_ocupado itemMer" id="imgUrl'+v_ubicacion+'" data-info="ocupado '+(v_detalle).toLowerCase()+'" style="vertical-align: text-bottom;" width="40" height="20" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABsAAAANCAYAAABYWxXTAAAACXBIWXMAAAEUAAABFAH7OeD/AAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAK+SURBVHjafJI9b1xVEIafOefeXa7X6wQLI6IIiYYiXaRYFGkoIjoaJBD8BEpQCsRPoIWe30BBQUMRKZQ0RLIAURAQCibGH2t79+4580FxvWBLXqY6czRnnnnfMxIRrGL/yVcgmbR8jsyfwWhKajdwq4S0yGiKpJTdscXvj1/PvvhMtTxWbx6Z1r1oJot6fsDo1j3GL90hrPDa7rv/9m+4HOEgCSS1wE3gBrAD3EbiVXR+x5HJuY4+YvvubHz2wz3p6ztS+mNCv9cajwi+EUlPRKQPrsYV2MYrdymnf27W470vs/W71Nw5dDTjzZTanNqOYrA4fHb7xnTinqcvS2vkmN9MsniQ6+yBxPzTcrD3bZHJB0334vlaWB5Ppxz9/DCVk/dyt42Mt5B2E3KHS4NLS784oZz88WaSDo0g0oQqgqoi0TF6gU7mv7zdn2x/GN0bnwP1Wtjs6Xcfx/FPD91bZqc9p/tnnPdKXypWC+JKk5Rb22OaPCIjCA1t3uSvpfD85IzSJ7qcmRz99klfvHD//S+uhR39+PXuxphNN8Wr0lZly5QtAmkCESHnhlYbZgd/4+4QTkQwUmUnF5ZNoaiiC9s53f/1LeB6mFpdejVSLBnhjBonkhHhmCnuBqos50qY4W5EGOE2QN1pcNowioL1Udb+mamhqZK84upDozDcnAgHNzyMiCAC3ANYnYc8PMCNakG1iPWwWnCpRJQB4Eb4APIwCMNtdWeEXQyzyn14gytmYBb8j7KCpYpEvQIKHywM98FKNwIfrIzVUP8NgRsXZethVSuaCsmXQ+O4pM5XChRWal2vql8N4oY6mKX1MLxm8QK+BHckBv+JYUkkHHEdLHVHTIkwJJwwv6h1Ek5DQ4PntbDDMzu0sR9lYuFGQDBstxAhREBYwh1ACAc8XVgdOA4iCIha6maWDy/3/2cAEXI9I4en7jkAAAAASUVORK5CYII=" ></a>');
	                   		    	//span[k] = '<a class="itemDer" style="display: none;" href="#imgUrl'+v_ubicacion+'" data-info="ocupado '+(v_detalle).toLowerCase()+'"><span class="label label-primary">Ubicación: '+v_ubicacion+'</span></a>';
	                   		    }
							}
							//$(span.join('  ')).appendTo('#lis_span');
							//var x = document.getElementById("lis_span");
    						//x.innerHTML = span.join(' -- ');

                            }//.success
                        });
					}	
					</script>

					<div class="loading" style="display:none;width:150px;height:189px;border:1px solid black;position:absolute;top:20%;left:25%;padding:2px;background:#fff"><img src='../dist/img/gif-argo-carnado-circulo_l2.gif' width="150" height="180" /></div>

                    <div style="height: 600px; overflow: auto;"><!-- overflow #1 -->

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
	                        <!-- <table class="tablaRack" style="background:#fff url('../dist/img/rack/columna_new.png'); background-size:100% 100%;"  border="0" width="250"> -->
	                        <table class="tablaRack" bgcolor="#00FF00" style=" border: 3px solid #255F98;"  border="0" width="250">

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
                    <div style="height: 600px; overflow: auto;"><!-- overflow #2 -->

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
	                      	//var span = [];
	                      	</script>
	                      	<!-- inicia for li mercancia no ubicada -->
	                      	<?php
	                      	$mercanciaNoUbi = $objRack->mercanciaNoUbi($id_plaza,$id_almacen,$id_cliente,$mercancia);
	                      	for ($i=0; $i <count($mercanciaNoUbi) ; $i++) {// for mercanciaNoUbi
	                      	$detalle = str_replace ("\r\n", "<\br>", $mercanciaNoUbi[$i]["V_DETALLE"]);
	                      	$detalle = str_replace("\"", '', $detalle);
	                      	?>
							<li>						
							  <a class="fancybox fancybox.iframe" href="rack_detubi.php?detArr=<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>">
							  <img id="imgUrl<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>" class="v_noubi itemMer" data-info="no ubicado <?=strtolower($mercanciaNoUbi[$i]["ID_ARRIBO"].' '.$detalle)?>" width="60" height="60" src="data:image/png;base64,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"></a>
							  <span style="font-size:10px;" class="label bg-gray itemMer"><?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?></span>
		                    </li>

		                    <script type="text/javascript">
		                    //span[<?=$i?>] = '<a class="itemDer" style="display: none;" href="#imgUrl<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>" data-info="no ubicado <?=strtolower($mercanciaNoUbi[$i]["ID_ARRIBO"].' '.$detalle)?>"><span class="label label-warning">Arribo: <?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?></span></a>';
		                    </script>
		                    <?php }// /.for mercanciaNoUbi ?>
		                    <!-- /.inicia for li mercancia no ubicada --> 
		                    <script type="text/javascript">
							//$(span.join('  ')).appendTo('#lis_span');		                    	
		                    </script>
						
	                      </ul><!-- /.users-list -->
	                    </div>
	                  </div>
	                <!-- termina li mercancia no ubicada -->

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
		  //busquedaJs();

			/*COLOREA IMAGEN CON CLICK*/		  
			$(".itemDer").click(function (){
				//$('.itemMer').css('background-color','inherit');//background img normal
				$('.itemMer').removeClass( 'someRedStuff' );
				//$( $(this).attr("href") ).css("background-color", "#E51C23");
				$( $(this).attr("href") ).toggleClass('someRedStuff');
			}); 


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
        	busquedaJs();
			operacion();
		});
    },
};
$(document).ready(CLICK.onReady);

/* ---------------------------------- inicia script busqueda ---------------------------------- */
function busquedaJs(){
	var items = $('.itemMer'),
	//itemDer = $('.itemDer'),
	input = $(".search-derecha").val();
	  
	// Search Listener
	switch(true){	
		case input === '':
			//$('.itemMer').css('background-color','inherit');//background img normal
			$('.itemMer').removeClass( 'someRedStuff' );
			$(".titleCoin").css('display', 'none');//oculta titulo coincidencia
			//items.css('opacity', '1');
			items.show();
			//itemDer.hide(); 
			break;
		default:
			//$('.itemMer').css('background-color','inherit');//background img normal
			$('.itemMer').removeClass( 'someRedStuff' );
			$(".titleCoin").css('display', 'block');//muetra titulo coincidencia
			//items.css('opacity', '0.1');
			//items.filter('[data-info*="' + input.toLowerCase() + '"]').css('opacity', '1');
			items.css('display', 'none');
			items.filter('[data-info*="' + input.toLowerCase() + '"]').show();

			//itemDer.hide(); 
            //itemDer.filter('[data-info*="' + input.toLowerCase() + '"]').show();

            var coinEnc = $('.itemDer').filter(function(){ return $(this).css('display') == 'inline'; }).length ;

            //$(".titleCoin").text('Coincidencias encontradas: '+coinEnc+'  ');
			break;
	}
	

	 
}	
/* ---------------------------------- termina script busqueda ---------------------------------- */

function operacion(){

	// var v_libre =  $('.v_libre').filter(function(){ return $(this).css('opacity') == 1; }).length,
	// v_ocupado =  $('.v_ocupado').filter(function(){ return $(this).css('opacity') == 1; }).length,
	// v_posicion = v_libre + v_ocupado,
	// v_noubi =  $('.v_noubi').filter(function(){ return $(this).css('opacity') == 1; }).length;

	var v_libre =  $('.v_libre').filter(function(){ return $(this).css('display') == 'inline'; }).length,
	v_ocupado =  $('.v_ocupado').filter(function(){ return $(this).css('display') == 'inline'; }).length,
	v_posicion = v_libre + v_ocupado,
	v_noubi =  $('.v_noubi').filter(function(){ return $(this).css('display') == 'inline'; }).length;

	document.getElementById("valPosiciones").innerHTML = v_posicion;
	<?php if ($id_cliente == 'ALL'){ ?>
	document.getElementById("valLibre").innerHTML = v_libre; 
	<?php } ?>
	document.getElementById("valOcupado").innerHTML = v_ocupado;
	document.getElementById("valNoUbi").innerHTML = v_noubi;

	$(".titleCoin").text('Coincidencias encontradas: '+ (v_posicion + v_noubi));

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
<!-- Inicia FancyBox JS -->
  <!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="../plugins/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('.fancybox').fancybox(); 
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