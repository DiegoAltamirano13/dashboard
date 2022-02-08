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
$plaza = $_SESSION["nomPlaza"];
switch($plaza){
 		 //case 'CORPORATIVO': $in_plaza = 2; break;
 		 case 'CÓRDOBA': $id_plaza = 3; break;
 		 case 'MÉXICO': $id_plaza = 4; break;
 		 case 'GOLFO': $id_plaza = 5; break;
 		 case 'PENINSULA': $id_plaza = 6; break;
 		 case 'PUEBLA': $id_plaza = 7; break;
 		 case 'BAJIO': $id_plaza = 8; break;
 		 case 'OCCIDENTE': $id_plaza = 17; break;
 		 case 'NORESTE': $id_plaza = 18; break;
 		 default: $id_plaza = "40"; break;
 	 }

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
        background-color: #fff;
        /*border: 2px solid #b2b2b2;*/
    }

    .tablaRack2{
            width: 110px;
            height: 50px; /* Ancho y alto fijo */
            overflow: hidden; /* Se oculta el contenido desbordado */
            background-color: #fff;
            /*border: 2px solid #b2b2b2;*/
        }
</style>

<!-- ########################################## Incia Contenido de la pagina ########################################## -->
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header"><!-- Inicia la seccion de Todo el contenido principal -->
      <h1>
        Dashboard<small>UBICACION MERCANCIA</small> <small>PLAZA ( <?php echo $_SESSION['nomPlaza'] ?> )</small>
      </h1>
      <?php //echo "<center><h4>PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>"; ?><!--FILTRO GENERAL -->

    </section><br>


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
        <input type="hidden" name="rackId_plaza" value="<?=$id_plaza?>">
        <input type="hidden" name="rackId_almacen" value="">
        <input type="hidden" name="rackId_cliente" value="">
        <input type="hidden" name="rackId_mercancia" value="">
        <input type="hidden" name="letraRack" value="">

        <?php //if($_SESSION['area']!=3){ ?>
	    	<!--<div class="col-md-2 col-sm-2 col-xs-12 invoice-col">
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
      </div>-->
        <?php //} ?>

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


	    <code class="loading" style="display:none;width:240px;height:25px;border:1px solid black;position:absolute;top:50%;left:0%;padding:2px;">
			Procesando, espere un momento …….
		</code>


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
            <section id="mercancia_ubicada">
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
                          //  alert(response);
                            success: function (response) {//success
                            var dataJson = JSON.parse(response);
                            var span = [];
                            for(var k in dataJson) {
							   	            //console.log( (dataJson[k].DETALLE1+dataJson[k].DETALLE2) );
              								var v_ubicacion = dataJson[k].V_DESCRIPCION;
              								var v_detalle = $.trim(dataJson[k].DETALLE1+" "+dataJson[k].DETALLE2);
                              var saldo = $.trim(dataJson[k].SALDO);

                //alert(dataJson);


                            if ( v_detalle == ""  || v_detalle === 'undefined' || saldo == 0){
	                   		    	$("#imgUrl"+v_ubicacion).replaceWith('<a class="fancybox fancybox.iframe" href="rack_detubi.php?detUbi='+v_ubicacion+'&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>"><img class="v_libre itemMer" id="imgUrl'+v_ubicacion+'" data-info="libre '+(v_ubicacion).toLowerCase()+'" style="vertical-align: text-bottom;" width="40" height="22" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB8AAAAOCAYAAADXJMcHAAAACXBIWXMAAAE7AAABOwEf329xAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAACtSURBVHja7NQ7igJREIXhr+VqoxvwERk4odtwGZpP7Cpcg6FgbGIuiGDgCiYyVyYYJhhHaZMWGrE1bJA+WR3uqb+KCxUlSaIoVRSoEl7CS/j7w8N+PRuji/+MHz3J5J3E6AUrmwv4ChiiX8Di24C/O/OCOXaoZ/wz2vhE/KDZBov0K6NMpoYRPu7enwIO+E6HqOIXU6weAFoYoJk2vqmBJSY5W/bQwU9axzheBwADvh4qHwKphwAAAABJRU5ErkJggg==" ></a>');
	                   		    	span[k] = '<a class="itemDer" style="display: none;" href="#imgUrl'+v_ubicacion+'" data-info="libre '+(v_ubicacion).toLowerCase()+'"><span class="label label-success">Ubicación: '+v_ubicacion+'</span><a/>';

	                   		    }else{
	                   		    	$("#imgUrl"+v_ubicacion).replaceWith('<a class="fancybox fancybox.iframe" href="rack_detubi.php?detUbi='+v_ubicacion+'&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>"><img class="v_ocupado itemMer" id="imgUrl'+v_ubicacion+'" data-info="ocupado '+(v_detalle).toLowerCase()+'" style="vertical-align: text-bottom;" width="40" height="40" src="data:image/jpeg;base64,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" ></a>');
	                   		    	span[k] = '<a class="itemDer" style="display: none;" href="#imgUrl'+v_ubicacion+'" data-info="ocupado '+(v_detalle).toLowerCase()+'"><span class="label label-primary">Ubicación: '+v_ubicacion+'</span></a>';
	                   		    }
							}
							$(span.join('  ')).appendTo('#lis_span');
							//var x = document.getElementById("lis_span");
    						//x.innerHTML = span.join(' -- ');

                            }//.success
                        });
					}
					</script>

					<div class="loading" style="display:none;width:150px;height:189px;border:1px solid black;position:absolute;top:20%;left:25%;padding:2px;background:#fff"><img src='../dist/img/gif-argo-carnado-circulo_l2.gif' width="150" height="180" /></div>
                    <?php
                    $rackProfundidad = $objRack->rackProfundidad($id_almacen,$letraRack);
                    if (count($rackProfundidad) == 0) { ?>
                      <div style="height: auto; overflow: auto;"><!-- overflow #1 -->
                    <?PHP
                  }else {
                    ?>
                      <div style="height: 600px; overflow: auto;"><!-- overflow #1 -->
                    <?php

                  }
                     ?>


                    	<?php
                      #echo count($rackProfundidad);
                    	for ($i=0; $i <count($rackProfundidad) ; $i++) {// for rackProfundidad
                      //  echo $letraRack;
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
            <!-- INICIA SECCION MERCANCIA UBICADA EN PISO -->
            <section>
              <div class="row">
                <div class="col-xs-12">
                  <div class="box box-default box-solid"><!-- box -->
                    <div class="box-header"><!-- box-header -->
                      <h3 class="box-title"><i class="fa fa-table"></i> Mercancía Ubicada en Piso </h3>
                      <div class="box-tools">
                         <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      </div>
                    </div><!-- /.box-header -->

                    <div class="box-body"><!-- box-body rack dibujo -->

                    <script type="text/javascript">

            function DetalleUbica2(id_plaza,id_almacen,id_cliente,mercancia,rack,profundidad){

            //console.log(id_plaza+" -- "+id_almacen+" -- "+id_cliente+" -- "+mercancia+" -- "+rack+" -- "+profundidad);

            $.ajax({
                            type: 'POST',
                            url: '../action/rackAjax.php',
                            cache:false,
                            async : false,
                            data: {"DibMercaPiso" : 1, "id_plaza" : id_plaza, "id_almacen" : id_almacen, "id_cliente" : id_cliente, "mercancia" : mercancia, "rack" : rack, "profundidad" : profundidad},
                            success: function (response) {//success
                            var dataJson = JSON.parse(response);
                            //alert( "id_plaza" + id_plaza + "id_almacen" + id_almacen + "id_cliente" + id_cliente + "mercancia" + mercancia + "rack" + rack + "profundidad" + profundidad);
                            //console.log(dataJson);
                            var span = [];

                            for(var k in dataJson) {

                            var v_ubicacion = dataJson[k].V_DESCRIPCION;
                            var v_detalle = $.trim(dataJson[k].DETALLE1);
                            var saldo = $.trim(dataJson[k].SALDO);

                            alert(saldo);
                            //console.log( (dataJson[k].DETALLE1+dataJson[k].V_DESCRIPCION) );
                            /*if (v_detalle) {
                            }*/

                            if ( v_detalle == ""  || v_detalle === 'undefined' || saldo == 0){
                              //alert(v_ubicacion);
                              $("#imgUrl"+v_ubicacion).replaceWith('<a class="fancybox fancybox.iframe" href="rack_detubi.php?piso_detubi='+v_ubicacion+'&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>"><img class="v_libre itemMer" id="imgUrl'+v_ubicacion+'" data-info="libre '+(v_ubicacion).toLowerCase()+'" style="vertical-align: text-bottom;" width="40" height="22" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB8AAAAOCAYAAADXJMcHAAAACXBIWXMAAAE7AAABOwEf329xAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAACtSURBVHja7NQ7igJREIXhr+VqoxvwERk4odtwGZpP7Cpcg6FgbGIuiGDgCiYyVyYYJhhHaZMWGrE1bJA+WR3uqb+KCxUlSaIoVRSoEl7CS/j7w8N+PRuji/+MHz3J5J3E6AUrmwv4ChiiX8Di24C/O/OCOXaoZ/wz2vhE/KDZBov0K6NMpoYRPu7enwIO+E6HqOIXU6weAFoYoJk2vqmBJSY5W/bQwU9axzheBwADvh4qHwKphwAAAABJRU5ErkJggg==" ></a>');
                              span[k] = '<a class="itemDer" style="display: none;" href="#imgUrl'+v_ubicacion+'" data-info="libre '+(v_ubicacion).toLowerCase()+'"><span class="label label-success">Ubicación: '+v_ubicacion+'</span><a/>';

                            }else{
                              $("#imgUrl"+v_ubicacion).replaceWith('<a class="fancybox fancybox.iframe" href="rack_detubi.php?piso_detubi='+v_ubicacion+'&p=<?=$id_plaza?>&c=<?=$id_cliente?>&a=<?=$id_almacen?>&m=<?=$mercancia?>"><img class="v_ocupado itemMer" id="imgUrl'+v_ubicacion+'" data-info="ocupado '+(v_detalle).toLowerCase()+'" style="vertical-align: text-bottom;" width="40" height="40" src="data:image/jpeg;base64,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" ></a>');
                              span[k] = '<a class="itemDer" style="display: none;" href="#imgUrl'+v_ubicacion+'" data-info="ocupado '+(v_detalle).toLowerCase()+'"><span class="label label-primary">Ubicación: '+v_ubicacion+'</span></a>';
                            }
              }
              $(span.join('  ')).appendTo('#lis_span');
              //var x = document.getElementById("lis_span");
                //x.innerHTML = span.join(' -- ');

                            }//.success
                        });
            }
            </script>

            <div class="loading" style="display:none;width:150px;height:100px;border:1px solid black;position:absolute;top:20%;left:25%;padding:2px;background:#fff"><img src='../dist/img/gif-argo-carnado-circulo_l2.gif' width="150" height="180" /></div>

                    <?php
                    $rackProfundidad = $objRack->pisoProfundidad($id_almacen,$letraRack);
                    //echo $id_almacen. ' '.$letraRack;
                    if (count($rackProfundidad) == 0) { ?>
                      <div style="height: auto; overflow: auto;"><!-- overflow #1 -->
                    <?php
                    }else { ?>
                      <div style="height: 300px; overflow: auto;"><!-- overflow #1 -->
                    <?php
                    }
                    ?>


                      <?php
                      //$rackProfundidad = $objRack->pisoProfundidad($id_almacen,$letraRack);
                      for ($i=0; $i <count($rackProfundidad) ; $i++) {// for rackProfundidad
                        #echo $rackProfundidad;
                        //echo $letraRack;
                      ?>

                      <div class="table-responsive"><!-- table-responsive -->
                        <table  style="height:100px;"><!-- table principal -->
                            <caption><small class="pull-right-container badge" style="background-color:<?=$rackProfundidad[$i]["COLOR"]?>;">
                              Linea: <?=$rackProfundidad[$i]["RACK"]?> <!--PROFUNDIDAD <?=$rackProfundidad[$i]["PROFUNDIDAD"]?>-->
                            </small> </caption>
                            <tr style="height:100px;"><!-- ******* TR DEFINE RACK PROFUNDIDAD ******* -->

            <!-- ===================================================== CONSULTA DE COLUMNAS DENTRO DE DEL FOR ===================================================== -->
              <?php
              $rackColumna = $objRack->pisoColumna($id_almacen,$rackProfundidad[$i]["RACK"],$rackProfundidad[$i]["PROFUNDIDAD"]);
              for ($a=0; $a <count($rackColumna) ; $a++) {// for columnas
              ?>
                          <td><!-- ******* TD DEFINE COLUMNAS ******* -->
                          <small class="pull-right-container badge" style="background-color: <?=$rackProfundidad[$i]["COLOR"]?>;">Linea:<?=$rackProfundidad[$i]["RACK"]?> Piso:<?=$rackColumna[$a]["COLUMNA"]?></small>
                          <!-- <table class="tablaRack" style="background:#fff url('../dist/img/rack/columna_new.png'); background-size:100% 100%;"  border="0" width="250"> -->
                          <table class="tablaRack2" bgcolor="#00FF00" style=" border: 3px solid #255F98;"  border="0" width="100px">

            <!-- ****************************************************** CONSULTA DE NIVEL DENTRO DE DEL FOR ****************************************************** -->
              <?php
              $rackNivel = $objRack->pisoNivel($id_almacen,$rackProfundidad[$i]["RACK"],$rackProfundidad[$i]["PROFUNDIDAD"],$rackColumna[$a]["COLUMNA"]);
              for ($b=0; $b <count($rackNivel) ; $b++) {// for nivel
              ?>
              <tr><!-- TR DEFINE NIVEL -->

            <!-- /////////////////////////////////////////////////// CONSULTA DE POSICION DENTRO DE DEL FOR /////////////////////////////////////////////////// -->


              <?php
              $v_posicion = explode(",", substr($rackNivel[$b]["POSICION"],0,-1));
              for ($c=0; $c <count($v_posicion) ; $c++) {
              ?>
              <!-- Nivel: <?=$rackNivel[$b]["NIVEL"]?> &#10;  Posición: <?=$v_posicion[$c]?>  Profundidad: <?=$rackNivel[$b]["PROFUNDIDAD"]?>-->
              <td style="height:100px;" title="Linea: <?=$rackNivel[$b]["RACK"]?> Piso: <?=$rackColumna[$a]["COLUMNA"]?>" style="border-bottom: 3px solid #054A8B;" valign="bottom" align="center"><!-- TD DEFINE POSICION -->

                          <img id="imgUrl<?=$rackNivel[$b]["RACK"].$rackNivel[$b]["COLUMNA"].$rackNivel[$b]["NIVEL"].$v_posicion[$c].$rackNivel[$b]["PROFUNDIDAD"]?>" >

                          </td> <!-- /.TD DEFINE POSICION -->

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
                        DetalleUbica2(<?=$id_plaza?>,<?=$id_almacen?>,'<?=$id_cliente?>','<?=$mercancia?>','<?=$rackProfundidad[$i]["RACK"]?>','00');

                      </script>

                      <?php }// /.for rackProfundidad ?>

                    </div><!-- /.overflow #1 -->
                    </div><!-- /.box-body rack dibujo -->
                  </div><!-- /.box -->
                </div>
              </div>
            </section>

            <!-- termina seccion mercancia ubicada en piso -->
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
	                      	var span = [];
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
							  <img id="imgUrl<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>" class="v_noubi itemMer" data-info="no ubicado <?=strtolower($mercanciaNoUbi[$i]["ID_ARRIBO"].' '.$detalle)?>" width="60" height="60" src="data:image/jpeg;base64,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"></a>
							  <span style="font-size:10px;" class="label bg-gray itemMer"><?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?></span>
		                    </li>

		                    <script type="text/javascript">
		                    span[<?=$i?>] = '<a class="itemDer" style="display: none;" href="#imgUrl<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>" data-info="no ubicado <?=strtolower($mercanciaNoUbi[$i]["ID_ARRIBO"].' '.$detalle)?>"><span class="label label-warning">Arribo: <?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?></span></a>';
		                    </script>
		                    <?php }// /.for mercanciaNoUbi ?>
		                    <!-- /.inicia for li mercancia no ubicada -->
		                    <script type="text/javascript">
							$(span.join('  ')).appendTo('#lis_span');
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
				$('.itemMer').css('background-color','inherit');//background img normal
				$( $(this).attr("href") ).css("background-color", "#E51C23");
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
	itemDer = $('.itemDer'),
	input = $(".search-derecha").val();

	// Search Listener
	switch(true){
		case input === '':
			$('.itemMer').css('background-color','inherit');//background img normal
			$(".titleCoin").css('display', 'none');//oculta titulo coincidencia
			items.css('opacity', '1');
			itemDer.hide();
			break;
		default:
			$('.itemMer').css('background-color','inherit');//background img normal
			$(".titleCoin").css('display', 'block');//muetra titulo coincidencia
			items.css('opacity', '0.1');
			items.filter('[data-info*="' + input.toLowerCase() + '"]').css('opacity', '1');

			itemDer.hide();
            itemDer.filter('[data-info*="' + input.toLowerCase() + '"]').show();

            var coinEnc = $('.itemDer').filter(function(){ return $(this).css('display') == 'inline'; }).length ;

            $(".titleCoin").text('Coincidencias encontradas: '+coinEnc);
			break;
	}



}
/* ---------------------------------- termina script busqueda ---------------------------------- */

function operacion(){

	var v_libre =  $('.v_libre').filter(function(){ return $(this).css('opacity') == 1; }).length,
	v_ocupado =  $('.v_ocupado').filter(function(){ return $(this).css('opacity') == 1; }).length,
	v_posicion = v_libre + v_ocupado,
	v_noubi =  $('.v_noubi').filter(function(){ return $(this).css('opacity') == 1; }).length;

	document.getElementById("valPosiciones").innerHTML = v_posicion;
	<?php if ($id_cliente == 'ALL'){ ?>
	document.getElementById("valLibre").innerHTML = v_libre;
	<?php } ?>
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
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
  });
</script>
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
