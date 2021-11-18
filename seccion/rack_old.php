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
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/Responsive/css/responsive.dataTables.min.css">

<style type="text/css">
/*TABLA PARA EL RACK*/
.tablaRack {
    width: 110px;
    height: 170px; /* Ancho y alto fijo */
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
 

	<!-- ############################ INICIA SECCION FILTROS DISPONIBLES ############################# --> 
	<section>
	  <div class="box box-info">    
	    <div class="box-header with-border">
	      <h3 class="box-title"><i class="fa fa-filter"></i> Filtros Disponibles</h3>
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


<?php if ( isset($letraRack) && !empty($letraRack) ){//if ?>
	<!-- ############################## INICIA SECCION IZQUIERDA DERECHA ############################## -->
	<div class="row">
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

                    <center>
                      <div class="imgLoad" style="display:;background-color:#FFFFFF;position:absolute;top:30%;left:30%;padding:2px;"><img src='../dist/img/gif-argo-carnado-circulo_l2.gif' width="150" height="175" /></div>
                    </center>
                    
                    <div style="height: 600px; overflow: auto;"><!-- overflow #1 -->

                    	<script type="text/javascript">
                    	$(".imgLoad").css("display", "block");
                		function DetalleUbica(id_plaza,id_almacen,id_cliente,mercancia,rack,profundidad){
							//console.log(id_plaza+" "+id_almacen+" "+id_cliente+" "+mercancia+" "+rack+" "+profundidad+" <br>");

							$.ajax({
                                type: 'POST',
		                        url: '../action/rackAjax.php',
		                        cache:false,
                                data: {"DibMerca" : 1, "id_plaza" : id_plaza, "id_almacen" : id_almacen, "id_cliente" : id_cliente, "mercancia" : mercancia, "rack" : rack, "profundidad" : profundidad},
                                success: function (response) {//success
                                var dataJson = JSON.parse(response);
                                //console.log(dataJson);
                                $.each(dataJson, function(i, val){

                                	var v_ubicaion = val.V_DESCRIPCION;
                       		    	var s_bateria = v_ubicaion.substr(0, 1);
	                       		    var s_columna = v_ubicaion.substr(1, 2);
	                       		    var s_nivel = v_ubicaion.substr(3, 2);
	                       		    var s_posicion = v_ubicaion.substr(5, 2);
	                       		    var s_profundidad = v_ubicaion.substr(7, 2);
                                    		    
                                    if ( $.trim(val.DETALLE1+" "+val.DETALLE2) == ""){                            
                                      $("#imgUrl"+val.V_DESCRIPCION).replaceWith('<img id="imgUrl'+val.V_DESCRIPCION+'" class="item colorUrl v_libre" data-roll="'+val.V_DESCRIPCION+'" data-type="libre '+(val.V_DESCRIPCION).toLowerCase()+'" style="cursor: pointer;vertical-align: text-bottom;" width="40" height="22" src="../dist/img/rack/pallet.png" data-toggle="modal" data-target="#modal_detalleUbi">');
                                      $("#origen"+val.V_DESCRIPCION).append('<div data-search="libre '+(val.V_DESCRIPCION).toLowerCase()+'" class="box-comment"><img class="direct-chat-img img-bordered-sm" src="../dist/img/rack/cajaico_libre.jpg" alt="img"><div class="comment-text">'+
                                          '<span class="username clickUrlB" data-roll="'+val.V_DESCRIPCION+'"><a href="#imgUrl'+val.V_DESCRIPCION+'">Ubicación: '+val.V_DESCRIPCION+'</a><span class="text-muted pull-right badge bg-green">Estado: LIBRE</span>'+
                                          '</span><li>Bateria: '+s_bateria+'</li><li>Columna: '+s_columna+'</li><li>Nivel: '+s_nivel+'</li><li>Posición: '+s_posicion+'</li><li>Profundidad: '+s_profundidad+'</li></div></div>');
                                      
                                    }else{
	                                  $("#imgUrl"+val.V_DESCRIPCION).replaceWith('<img id="imgUrl'+val.V_DESCRIPCION+'" class="item colorUrl v_ocupada" data-roll="'+val.V_DESCRIPCION+'" data-type="ocupado '+(val.DETALLE1+" "+val.DETALLE2).toLowerCase()+'" style="cursor: pointer;vertical-align: text-bottom;" width="45" height="22" src="../dist/img/rack/palletTrue.png" data-toggle="modal" data-target="#modal_detalleUbi">');
	                                  $("#origen"+val.V_DESCRIPCION).append('<div data-search="ocupado '+(val.DETALLE1+" "+val.DETALLE2).toLowerCase()+'" class="box-comment"><img class="direct-chat-img img-bordered-sm" src="../dist/img/rack/cajaico.jpg" alt="img"><div class="comment-text">'+
                                          '<span class="username clickUrlB" data-roll="'+val.V_DESCRIPCION+'"><a href="#imgUrl'+val.V_DESCRIPCION+'">Ubicación: '+val.V_DESCRIPCION+'</a><span class="text-muted pull-right badge bg-blue ">Estado: OCUPADO</span>'+
                                          '</span><li>Bateria: '+s_bateria+'</li><li>Columna: '+s_columna+'</li><li>Nivel: '+s_nivel+'</li><li>Posición: '+s_posicion+'</li><li>Profundidad: '+s_profundidad+'</li></div></div>');
	                                  
                                    }

                                });
                                }//.success
                            });

						}	
                    	</script>
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
							$rackPosicion = $objRack->rackPosicion($id_almacen,$rackProfundidad[$i]["RACK"],$rackProfundidad[$i]["PROFUNDIDAD"],$rackColumna[$a]["COLUMNA"],$rackNivel[$b]["NIVEL"]);
							for ($c=0; $c <count($rackPosicion) ; $c++) {// for posicion
							?>

							<td title="Rack: <?=$rackPosicion[$c]["RACK"]?> Nivel: <?=$rackPosicion[$c]["NIVEL"]?> &#10; Posición: <?=$rackPosicion[$c]["POSICION"]?>  Profundidad: <?=$rackPosicion[$c]["PROFUNDIDAD"]?>" style="border-bottom: 3px solid #054A8B;" valign="bottom" align="center"><!-- TD DEFINE POSICION -->

                        	<img id="imgUrl<?=$rackPosicion[$c]["V_DESCRIPCION"]?>" class="item colorUrl">
                        
	                        <form id="formdetUbi<?=$rackPosicion[$c]["V_DESCRIPCION"]?>">
	                          <input type="hidden" name="detUbicacion" value="1">
	                          <input type="hidden" name="ubicacion" value="<?=$rackPosicion[$c]["V_DESCRIPCION"]?>">
	                          <input type="hidden" name="id_plaza" value="<?=$id_plaza?>">
	                          <input type="hidden" name="fil_cliente" value="<?=$id_cliente?>">
	                          <input type="hidden" name="id_almacen" value="<?=$id_almacen?>">
	                          <input type="hidden" name="fil_db" value="<?=$mercancia?>">
	                        </form>
                    
                        <!--******************* INICIA DIV QUE SE COPIA A LA DERECHA *******************-->
                        <div style="display: none;">
	                        <div id="origen<?=$rackPosicion[$c]["V_DESCRIPCION"]?>">
	                        </div>
                        </div>
                        <!--******************* TERMINA DIV QUE SE COPIA A LA DERECHA *******************-->

                        	</td><!-- /.TD DEFINE POSICION -->


							<?php }// /.posicion ?>
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

                    	<script>
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


            <!-- ***************************************** INCIA MODAL ***************************************** -->
	          <div class="modal fade" id="modal_detalleUbi" role="dialog"><!-- Modal -->
	            <div class="modal-dialog"> 
	              <div class="modal-content"><!-- ./Modal content-->
	                <div class="modal-header">
	                  <button type="button" class="close" data-dismiss="modal">&times;</button>
	                  <h4 class="modal-title" id="tituloModal"> </h4>
	                </div>
	                <div class="modal-body"><!-- modal-body -->

	                  <!--******************* INCIA EL CONTENIDO EN EL MODAL DETALLE DE UBICACION *******************-->
	                  
	                  <div class="table-responsive">
	                    <table class="table no-margin table-bordered table-striped">
	                      <thead>
	                      <tr>
	                        <th style="background-color: #D2D6DE;">RACK</th>
	                        <th style="background-color: #D2D6DE;">COLUMNA</th>
	                        <th style="background-color: #D2D6DE;">NIVEL</th>
	                        <th style="background-color: #D2D6DE;">POSICIÓN</th>
	                        <th style="background-color: #D2D6DE;">PROFUNDIDAD</th>
	                      </tr>
	                      </thead>
	                      <tbody>
	                      <tr>
	                        <td><p id="bateriaScript"></p></td>
	                        <td><p id="columnaScript"></p></td>
	                        <td><p id="nivelScript"></p></td>
	                        <td><p id="posicionScript"></p></td>
	                        <td><p id="profundidadScript"></p></td>
	                      </tr>
	                      </tbody>
	                    </table>
	                  </div>
	                  <hr> 

	                  <div id="res_detUbi">
	                  	<center>
	                  	<div style="display:;top:50%;left:50%;padding:2px;"><img src='../dist/img/gif-argo-carnado-circulo_l2.gif' width="150" height="175" /><br>Procesando, espere por favor...</div></center>
	                  </div>

	                  <div class="detTabla">
		                <table id="tablaDetUbicacion" class="table table-bordered table-hover table-striped">
		                  <thead>
		                  <tr>
		                    <th class="bg-light-blue">CLIENTE</th>
			                <th class="bg-light-blue">ARRIBO</th>
			                <th class="bg-light-blue">CONTENEDOR</th>
			                <th class="bg-light-blue">RECIBO</th>
			                <th class="bg-light-blue">CERTIFICADO</th>
			                <th class="bg-light-blue">PED.IMP.</th>
			                <th class="bg-light-blue">NO.PARTE</th>
			                <th class="bg-light-blue">LOTESERIE</th>
			                <th class="bg-light-blue">DESC.NO.PARTE</th>
			                <th class="bg-light-blue">DESCRIPCIÓN</th>
			                <th class="bg-light-blue">ET</th>
			                <th class="bg-light-blue">CANTIDAD(ET)</th>
			                <th class="bg-light-blue">ES</th>
			                <th class="bg-light-blue">CANTIDAD(ES)</th>
			                <th class="bg-light-blue" >UP</th>
			                <th class="bg-light-blue">CANTIDAD(UP)</th>
		                  </tr>
		              	  </thead>
		                  <tbody>
		                  </tbody>
		                </table>
		              </div>
	                  
	                  <!--******************* TERMINA EL CONTENIDO EN EL MODAL DETALLE DE UBICACION *******************-->
	                </div><!-- ./modal-body -->
	                <div class="modal-footer">
	                  <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	                </div>
	              </div><!-- ./Modal content-->
	            </div>
	          </div><!-- ./Modal -->
	        <!-- ***************************************** TERMINA MODAL ***************************************** -->

	        <!-- ==================== INICIA SCRIPT AJAX STOP ==================== -->
	        <script>
          	$( document ).ajaxStop(function() {
          		console.log('Posiciones '+$(".colorUrl").length );

          		$(".colorUrl").each(function(){
	            	$(this).on("click", function(){
	            		ajaxDetalleUbi($(this).data("roll"));
	                });
	                $('#origen'+$(this).data("roll")).appendTo('#destinoDetalleUbica');
            	});

            	$(".clickUrlB").each(function(){
	            	$(this).on("click", function(){
	            		$('.colorUrl').css('background-color','inherit');
	            		$('.urlNoUbi').css('background-color','inherit');
	            		$("#imgUrl"+$(this).data("roll")).css("background-color", "#E51C23");
	            		
	                });
	            });

	            busquedaJs();

	            /*operaciones*/
	        	var val_libres =  $('.v_libre').filter(function(){ return $(this).css('opacity') == 1; }).length;
	        	var val_ocupadas =  $('.v_ocupada').filter(function(){ return $(this).css('opacity') == 1; }).length;
	        	var val_posiciones = val_libres + val_ocupadas;
	        	var val_noUbi =  $('.urlNoUbi').filter(function(){ return $(this).css('opacity') == 1; }).length;

	        	
	        	$("#valLibre").text( val_libres );
	        	$("#valOcupado").text( val_ocupadas );
	        	$("#valPosiciones").text( val_posiciones );
	        	$("#valNoUbi").text( val_noUbi );
	    	  	/*operaciones*/

	    	  	$(".imgLoad").css("display", "none");

          	});
          	</script>
          	<!-- ==================== TERMINA SCRIPT AJAX STOP ==================== -->
	          


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

	                      	<!-- inicia for li mercancia no ubicada -->
	                      	<?php
	                      	$mercanciaNoUbi = $objRack->mercanciaNoUbi($id_plaza,$id_almacen,$id_cliente,$mercancia);
	                      	for ($i=0; $i <count($mercanciaNoUbi) ; $i++) {// for mercanciaNoUbi
	                      	?>
							<li title="">
							  <img id="imgUrl<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>" class="item urlNoUbi" data-roll="<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>" data-type="no ubicado <?=strtolower($mercanciaNoUbi[$i]["ID_ARRIBO"].' '.$mercanciaNoUbi[$i]["V_DETALLE"])?>" style="cursor: pointer;vertical-align: text-bottom;" width="60" height="60" src="../dist/img/rack/palletNoUbi.png" data-toggle="modal" data-target="#modal_detalleNoUbi">
							  <span style="font-size:10px;" class="item label bg-gray"><?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?></span>
		                    </li>
		                    
		                    <div style="display: none;">


                           	<div data-arribo="<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>" data-search="no ubicado <?=strtolower($mercanciaNoUbi[$i]["ID_ARRIBO"].' '.$mercanciaNoUbi[$i]["V_DETALLE"])?>" class="box-comment imgNoUbi" id="origenNoUbi<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>">
                           		<img id="imgNoUbi<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>" class="direct-chat-img img-bordered-sm" src="../dist/img/rack/cajaico_noUbi.jpg" alt="img">
                            		<div class="comment-text"><!-- comment-text -->
                                    <span class="username">
                                      <a href="#imgUrl<?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?>">Arribo: <?=$mercanciaNoUbi[$i]["ID_ARRIBO"]?></a>
                                      <span class="text-muted pull-right badge bg-yellow">NO UBICADO</span>
                                    </span>
                                    <li>Arribo por Ubicar</li>
                           		</div>
                           	</div> 


		                    </div>
		                    <?php }// /.for mercanciaNoUbi ?>
		                    <!-- /.inicia for li mercancia no ubicada --> 
						
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


		<!-- ***************************************** INCIA MODAL MERCANCIA NO UBICADA ***************************************** -->
          <div class="modal fade" id="modal_detalleNoUbi" role="dialog"><!-- Modal -->
            <div class="modal-dialog"> 
              <div class="modal-content"><!-- ./Modal content-->
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" id="tituloModalNoUbi"></h4>
                </div>
                <div class="modal-body"><!-- modal-body -->

                  <!--******************* INCIA EL CONTENIDO EN EL MODAL DETALLE ARRIBO NO UBICADOA *******************--> 
    
                  <div id="res_detNoUbi">
                  	<center>
                  	<div style="display:;top:50%;left:50%;padding:2px;"><img src='../dist/img/gif-argo-carnado-circulo_l2.gif' width="150" height="175" /><br>Procesando, espere por favor...</div></center>

                  </div>

                  <div class="detTabla">
                  	<table id="tablaDetNoUbicado" class="table table-bordered table-hover table-striped">
	                  <thead>
	                  <tr>
	                    <th class="bg-light-blue">CLIENTE</th>
		                <th class="bg-light-blue">ARRIBO</th>
		                <th class="bg-light-blue">CONTENEDOR</th>
		                <th class="bg-light-blue">RECIBO</th>
		                <th class="bg-light-blue">CERTIFICADO</th>
		                <th class="bg-light-blue">PED.IMP.</th>
		                <th class="bg-light-blue">NO.PARTE</th>
		                <th class="bg-light-blue">DESC.NO.PARTE</th>
		                <th class="bg-light-blue">DESCRIPCIÓN</th>
		                <th class="bg-light-blue">MOVTO</th>
		                <th class="bg-light-blue">CANTIDAD(UME)</th>
		                <th class="bg-light-blue">CANTIDAD(UME ARRIBO)</th>
	                  </tr>
	              	  </thead>
	                  <tbody>
	                  </tbody>
                	</table>
                  </div>
                  
                  <!--******************* TERMINA EL CONTENIDO EN EL MODAL DETALLE ARRIBO NO UBICADOA *******************-->
                </div><!-- ./modal-body -->
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
              </div><!-- ./Modal content-->
            </div>
          </div><!-- ./Modal -->
          <!-- ***************************************** TERMINA MODAL MERCANCIA NO UBICADA ***************************************** -->



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

                    <div class="table-responsive" style="height: 1120px; overflow: auto;"><!-- overflow #3 -->
                    	<div class="box-footer box-comments containerItems destinoDetalleUbica" id="destinoDetalleUbica">
                        </div>
                        
                        <div class="box-footer box-comments containerItems" id="destinoMercanciaNoUbi">
                        </div>
                  	</div><!-- /.overflow #3 -->


                    </div><!-- /.box-body buscar --> 
                	</div><!-- /.box -->
                </div>
              	</div>
            </section>
            <!-- TERMINA SECCION BUSQUEDA -->


		</section><!-- /.col-lg-3 -->
		<!-- =========== TERMINA SECCION MERCANCIA DERECHA (BUSQUEDA DE MERCANCIA) =========== -->




	</div>
	<!-- ############################## TERMINA SECCION IZQUIERDA DERECHA ############################## -->
<?php }//if ?>

 

      

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
<!-- DataTables --> 
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
  });
</script>
<script type="text/javascript">
$(document).ready(function() {
	operacionCalc();
	/*----------------------- /.SCRIPT PARA MERCANCIA NO UBICADA -----------------------*/
	var mer_noUbi = $(".urlNoUbi").length;
	
	$(".urlNoUbi").each(function(){
    	$(this).on("click", function(){ 
        	ajaxDetArriboNoUbi( $(this).data("roll") );
        });
    	$('#origenNoUbi'+$(this).data("roll")).appendTo('#destinoMercanciaNoUbi');
    });
	
	/*script cambai de color con el click arribos no ubicado*/
	$(".imgNoUbi").each(function (){
		$(this).on("click", function(){
			$('.colorUrl').css('background-color','inherit');
			$('.urlNoUbi').css('background-color','inherit');
    		$("#imgUrl"+$(this).data("arribo")).css("background-color", "#E51C23"); 
        });
	});
	/*----------------------- /.SCRIPT PARA MERCANCIA NO UBICADA -----------------------*/

	/*----------------------- AJAX DETALLE MERCANCIA NO UBICADA -----------------------*/
    function ajaxDetArriboNoUbi(v_arribo){
		$("#tituloModalNoUbi").html('<img class="direct-chat-img img-bordered-sm" src="../dist/img/rack/cajaico_noUbi.jpg" alt="img">   Arribo '+v_arribo);
		
		$.ajax({
        	type: "POST",
            data: { "detNoUniArribo" : v_arribo, "id_plaza" : <?=$id_plaza?>, "fil_cliente" : '<?=$id_cliente?>', "id_almacen" : '<?=$id_almacen?>', "fil_db" : '<?=$mercancia?>' },
            cache:false,
            url: '../action/rackAjax.php',
	        beforeSend: function () {
	            $("#res_detNoUbi").show();
	            $(".detTabla").hide();
	        },
	        success:  function (response) {
	        	$(".detTabla").show();
	        	$("#res_detNoUbi").hide();
	        	var res_json = JSON.parse(response);
	            $('#tablaDetNoUbicado').dataTable( {
        		"scrollY": 200,
        		"scrollX": true,
        		"scrollCollapse": true,
        		"paging":         false,
	            destroy: true,
		        "searching": false,
		        stateSave: true,
		        "paging": false,
		        "info": false,
	            data : res_json,
	            columns: [

	                { "data": null, render: function ( data, type, row ) {
		                // Combine the first and last names into a single table field
		                return "("+data.IID_NUM_CLIENTE+")"+data.V_CLIENTE;
		            } },
	                {"data" : "ID_ARRIBO" },
	                {"data" : "V_CONTENEDOR" },
	                {"data" : "VID_RECIBO" },
	                {"data" : "VID_CERTIFICADO" },
	                {"data" : "VNO_PED_IMP" },
	                {"data" : "VID_NUM_PARTE" },
	                {"data" : "V_DESCRIPCION" },
	                {"data" : "V_DESCRIPCION_GENERICA" },
	                {"data" : "VID_MOVTO" },
	                {"data" : "C_CANTIDAD_UME" },
	                {"data" : "C_CANTIDAD_UME_ARRIBO" },
	            ],
            });
	        }
        
        });
	}

});	
</script>	
<script>
/* ENVIO AJAX PARA VER DETALLE DE LA POSICION*/
function ajaxDetalleUbi(v_ubi){
	
	var rol =  v_ubi;
    $("#tituloModal").html("<i class='fa fa-map-marker'></i> Ubicación "+rol);/*asigna titulo a la ventana modal*/
    $("#bateriaScript").text(rol.substr(0, 1));/*asigna la letra del rack en la tabla*/
    $("#columnaScript").text(rol.substr(1, 2));/*asigna # columna en la tabla*/
    $("#nivelScript").text(rol.substr(3, 2));/*asigna # nivel en la tabla*/
    $("#posicionScript").text(rol.substr(5, 2));/*asigna # posicion en la tabla*/
    $("#profundidadScript").text(rol.substr(7, 2));/*asigna # profundidad en la tabla*/  
    
	$.ajax({
        type: "POST",
        data: $("#formdetUbi"+v_ubi+"").serialize(),
        cache:false,
        url: '../action/rackAjax.php',
      	beforeSend: function () {
            $("#res_detUbi").show();
	        $(".detTabla").hide();
        },
        success:  function (response) {
        	$(".detTabla").show();
	        $("#res_detUbi").hide();
        	var o = JSON.parse(response);//A la variable le asigno el json decodificado 
        	
        	$('#tablaDetUbicacion').dataTable( {
        		"scrollY": 200,
        		"scrollX": true,
        		"scrollCollapse": true,
        		"paging":         false,
	            destroy: true,
		        "searching": false,
		        stateSave: true,
		        "paging": false,
		        "info": false,
	            data : o,
	            columns: [

	                { "data": null, render: function ( data, type, row ) {
		                // Combine the first and last names into a single table field
		                return "("+data.IID_NUM_CLIENTE+")"+data.V_CLIENTE;
		            } },
	                {"data" : "ID_ARRIBO" },
	                {"data" : "V_CONTENEDOR" },
	                {"data" : "VID_RECIBO" },
	                {"data" : "VID_CERTIFICADO" },
	                {"data" : "VNO_PED_IMP" },
	                {"data" : "VID_NUM_PARTE" },
	                {"data" : "V_LOTE_SERIE" },
	                {"data" : "V_DESCRIPCION" },
	                {"data" : "V_DESCRIPCION_GENERICA" },
	                {"data" : "ET" },
	                {"data" : "CANTIDAD_ET" },
	                {"data" : "ES" },
	                {"data" : "CANTIDAD_ES" },
	                {"data" : "UP" },
	                {"data" : "CANTIDAD_UP" },
	            ],
            });


        }
    
    });
}
</script>
<script type="text/javascript">
$(".search-btn").click( function(){
    /*operaciones*/
    operacionCalc();
    busquedaJs();
    /*/.operaciones*/
});
function busquedaJs(){

	/* ---------------------------------- inicia script busqueda ---------------------------------- */
    //var jSearch = (function (){
      // Item List
      var input = $('input');
      var items = $('.item');
      var itemsdetubi = $('.box-comment');
      
      // Search Listener 

    	  var input = $(".search-derecha").val();
          $('.colorUrl').css('background-color','inherit');
          $('.urlNoUbi').css('background-color','inherit');
          switch(true){ 
            case input === '':
              items.css('opacity', '1');
                itemsdetubi.css('display', 'block');
              break;
            default:
              items.css('opacity', '0.1');
              items.filter('[data-type*="' + input.toLowerCase() + '"]').css('opacity', '1');
              
              itemsdetubi.css('display', 'none');
              itemsdetubi.filter('[data-search*="' + input.toLowerCase() + '"]').css('display', 'block'); 
              break;
          } 
    	  
          /*operaciones*/
          operacionCalc(); 
          /*/.operaciones*/ 

    //}());
    /* termina script busqueda */
    
}	
</script>
<script type="text/javascript">
function operacionCalc(){
	
	/*operaciones*/
	var val_libres =  $('.v_libre').filter(function(){ return $(this).css('opacity') == 1; }).length;
	var val_ocupadas =  $('.v_ocupada').filter(function(){ return $(this).css('opacity') == 1; }).length;
	var val_posiciones = val_libres + val_ocupadas;
	var val_noUbi =  $('.urlNoUbi').filter(function(){ return $(this).css('opacity') == 1; }).length;

	$("#valLibre").text( val_libres );
	$("#valOcupado").text( val_ocupadas );
	$("#valPosiciones").text( val_posiciones );
	$("#valNoUbi").text( val_noUbi );
	/*operaciones*/
	
}
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