<?php
include_once '../libs/conOra.php';
require_once ('../libs/spreadsheet-reader-master/SpreadsheetReader.php');
require_once ('../libs/spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
$conn = conexion::conectar();//coneccion
if (isset($_POST["import"]))
{

  $allowedFileType = array('application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

  if(in_array($_FILES["file"]["type"],$allowedFileType)){

        $targetPath = 'uploads/'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

        $Reader = new SpreadsheetReader($targetPath);
        $sheetCount = count($Reader->sheets());
        for($i=0; $i<$sheetCount; $i++)
        {

            $Reader->ChangeSheet($i);

            foreach ($Reader as $Row)
            {

                $iid_plaza = 0;
                if(isset($Row[0])) {
                    $iid_plaza = $Row[0];
                }

                $iid_almacen = 0;
                if(isset($Row[1])) {
                    $iid_almacen = $Row[1];
                }

                $anio = 0;
                if(isset($Row[2])) {
                    $anio = $Row[2];
                }

                $mes = 0;
                if(isset($Row[3])) {
                    $mes = $Row[3];
                }

                $via_arrendamiento = 0;
                if(isset($Row[4])) {
                    $via_arrendamiento = $Row[4];
                }

                $via_tradicional = 0;
                if(isset($Row[5])) {
                    $via_tradicional = $Row[5];
                }

                $edo_resultado = 0;
                if(isset($Row[6])) {
                    $edo_resultado = $Row[6];
                }

                $via_mercado = 0;
                if(isset($Row[7])) {
                    $via_mercado = $Row[7];
                }

                $gasto_real = 0;
                if(isset($Row[8])) {
                    $gasto_real = $Row[8];
                }

                $via_r_o = 0;
                if(isset($Row[9])) {
                    $via_r_o = $Row[9];
                }

                $via_p_cotizar = 0;
                if(isset($Row[10])) {
                    $via_p_cotizar = $Row[10];
                }

                $pos_racks_vs_via_trad = 0;
                if(isset($Row[11])) {
                    $pos_racks_vs_via_trad = $Row[11];
                }

                $pos_racks_vs_real = 0;
                if(isset($Row[12])) {
                    $pos_racks_vs_real = $Row[12];
                }

                $pos_racks_vs_via_cotizar = 0;
                if(isset($Row[13])) {
                    $pos_racks_vs_via_cotizar = $Row[13];
                }

                $pos_racks_promedio = 0;
                if(isset($Row[14])) {
                    $pos_racks_promedio = $Row[14];
                }

                $pos_racks_minima = 0;
                if(isset($Row[15])) {
                    $pos_racks_minima = $Row[15];
                }

                $pos_racks_deseable = 0;
                if(isset($Row[16])) {
                    $pos_racks_deseable = $Row[16];
                }

                if (!empty($id_almacen) || !empty($iid_plaza) ) {
                  //QUERY CONSULTA
                    $consulta = "SELECT COUNT(*)AS ID FROM OP_IN_VIAS_ALMACEN WHERE IID_ALMACEN = ".$iid_almacen." AND N_ANIO = ".$anio."";
                    //SE PARSEA
                    #echo $consulta;
                    $still = oci_parse($conn, $consulta);
                    oci_execute($still);
                    while (oci_fetch($still)) {
                      $reg = oci_result($still, "ID");
                      if( $reg > 0){
                        $type = "warning";
                        $message = "El archivo ya se exporto anteriormente!!!";
                      }
                      else {
                    #  echo "prueba llego al = 0";
                      ###################                     PRESUPUESTO 1                 ####################################
                      $query = "INSERT INTO OP_IN_VIAS_ALMACEN (IID_PLAZA, IID_ALMACEN, N_ANIO,  N_MES, VIA_ARRENDAMIENTO, VIA_TRADICIONAL, EDO_RESULTADO,
                                                                VIA_DE_MERCADO, GASTO_REAL, VIA_R_O, VIA_P_COTIZAR, POS_RACKS_vs_VIA_TRAD, POS_RACKS_VS_REAL,
                                                                POS_RACKS_VS_VIA_COTIZAR, POS_RACKS_PROMEDIO, POS_RACKS_MINIMA, POS_RACKS_DESEABLE)
                                       VALUES ($iid_plaza, $iid_almacen, $anio, $mes, $via_arrendamiento, $via_tradicional, $edo_resultado,
                                               $via_mercado, $gasto_real, $via_r_o, $via_p_cotizar, $pos_racks_vs_via_trad, $pos_racks_vs_real,
                                               $pos_racks_vs_via_cotizar, $pos_racks_promedio, $pos_racks_minima, $pos_racks_deseable)";
                      $sti = oci_parse($conn , $query);
                      $lanza = oci_execute($sti);
                      ###################                     PRESUPUESTO 2                 ####################################

                      if (!empty($lanza)) {
                          $type = "success";
                          $message = "Excel Importado Correctamente!!!";
                      } else {
                          $type = "error";
                          $message = "Problema Importando Excel!!!";
                      }
                    }
                  }
                }
             }

         }
  }
  else
  {
        $type = "error";
        $message = "Invalid File Type. Upload Excel File.";
  }
}
?>

<?php
//BY JTJ 28/12/2018

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  //header("location:Gastos_Maquinaria.php");
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
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 45);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////
include '../class/Vias_Info.php';
$modelNomina = new ViasInfo();
//SQL ULTIMA FECHA DE CORTE
$fec_corte = $modelNomina->sql(1,null, null);
/*----- GET FECHA -----*/
$fecha = $fec_corte[0]["MES1"];
if( isset($_GET["fecha"]) ){
  if ( $modelNomina->validateDate($_GET["fecha"]) ){
    $fecha = $_GET["fecha"];
  }else{
    $fecha = $fec_corte[0]["MES1"];
  }
}

/*----- GET PLAZA -----*/
$plaza=$_SESSION["nomPlaza"];
//$plaza = "ALL";
//echo $plaza;


$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}

$graficaDetalleAlmacen = $modelNomina->detalleGastos($fecha,$plaza,$almacen)
//$selectAlmacen = $modelNomina->almacenSql($plaza);
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- Select2 -->
<link rel="stylesheet" href="../plugins/select2/select2.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../plugins/datatables/extensions/buttons_datatable/buttons.dataTables.min.css">

<style media="screen">
.subir{
  padding: 5px 10px;
  background: #f55d3e;
  color:#fff;
  border:0px solid #fff;
}

.subir:hover{
  color:#fff;
  background: #f29f3e;
}
.outer-container {
	background: #F0F0F0;
	border: #e0dfdf 1px solid;
	padding: 40px 20px;
	border-radius: 2px;
}

.btn-submit {
	background: #333;
	border: #1d1d1d 1px solid;
    border-radius: 2px;
	color: #f0f0f0;
	cursor: pointer;
    padding: 5px 20px;
    font-size:0.9em;
}

#response {
    padding: 10px;
    margin-top: 10px;
    border-radius: 2px;
    display:none;
}

.success {
    background: #c7efd9;
    border: #bbe2cd 1px solid;
}

.warning{
  background: #b3a258;
  border: #c8bd26 1px solid;
}
.error {
    background: #fbcfcf;
    border: #f3c6c7 1px solid;
}

div#response.display-block {
    display: block;
}

</style>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
  <section class="content-header">
    <h1>
      Dashboard<small>Vias Por Almacen</small> <small>PLAZA ( <?php echo $_SESSION['nomPlaza'] ?> )</small>
      <?php //echo "<center><h4>PLAZA ( ".$_SESSION['nomPlaza']." )</h4></center>"; ?><!--FILTRO GENERAL -->
    </h1>

<?php
      $anio_elegido = substr($fecha, -4);
      //echo count($graficaNominaMes);
      $mes_elegido = substr($fecha, 14, 2);
      #echo $mes_elegido;

      $Anio_actual = date("Y");

      if ($Anio_actual) {
        $mes_actual = idate("m");
        //echo $mes_actual;
      }else {
        $mes_actual = 12;
      }
?>


  </section>


  <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->
  <!-- ############################ SECCION GRAFICA ############################# -->
  <section>

    <div class="row"><!-- row -->

    <div class="col-md-9"><!-- col-md-9 -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-bar-chart"></i> INFORMACIÓN </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <div class="row">
          <div class="col-md-12">
              <section>
                <div class="box box-success">
                  <div class="box-header with-border">
                    <?php if ($almacen != 'ALL') { ?>
                      <h3 class="box-title"><i class="fa fa-table"></i>
                          <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);
                            if ($almacen == 1 or $almacen == 2 or $almacen == 3) {
                                if ($almacen == 1 ) {
                                      echo " VIAS DEL ALMACEN PEÑUELA";
                                }elseif ($almacen == 2 ) {
                                      echo " VIAS DEL ALMACEN PANTACO";
                                }else {
                                      echo " VIAS DEL ALMACEN VICTORIA";
                                }
                            }else {
                              echo " GASTOS DEL ALMACEN ".$nombreAlm[0]["V_NOMBRE"];
                            }

                          ?>
                      </h3>
                    <?php  } else { ?>
                      <h3 class="box-title"><i class="fa fa-table"></i>
                         TODOS LOS ALMACENES
                      </h3>
                    <?php  }?>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body"><!--box-body-->

                    <div class="table-responsive" id="container">
                      <table id="tabla_nomina" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                            <th class="small" bgcolor="#43ADBF"><font color="white">PLAZA</font></th>
                            <th class="small" bgcolor="#50CFE6"><font color="white">ALMACEN</font></th> </th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">VIA ARRENDAMIENTO</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">VIA TRADICIONAL</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">ESTADO DE RESULTADO</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">VIA DE MERCADO</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">GASTO REAL</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">VIA R. O.</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">VIA P/COTIZAR</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">POSICION RACKS VS VIA TRADICIONAL</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">POSICION RACKS VS REAL</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">POSICION RACKS VS VIA COTIZAR</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">POSICION RACKS PROMEDIO</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">POSICION RACKS MINIMA</font></th>
                            <th class="small" bgcolor="#43ADBF"><font color="white">POSICION RACKS DESEABLE</font></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php for ($i=0; $i <count($graficaDetalleAlmacen) ; $i++) { ?>
                          <tr>
                            <!--<td class="small">CL</td>-->
                            <td class="small"><?= $graficaDetalleAlmacen[$i]["V_RAZON_SOCIAL"] ?></td>
                            <td class="small"><?php
                                                  echo $graficaDetalleAlmacen[$i]["V_NOMBRE"]
                                              ?>
                            </td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["VIA_ARRENDAMIENTO"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["VIA_TRADICIONAL"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["EDO_RESULTADO"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["VIA_DE_MERCADO"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["GASTO_REAL"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["VIA_R_O"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["VIA_P_COTIZAR"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["POS_RACKS_VS_VIA_TRAD"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["POS_RACKS_VS_REAL"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["POS_RACKS_VS_VIA_COTIZAR"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["POS_RACKS_PROMEDIO"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["POS_RACKS_MINIMA"], 2) ?></td>
                            <td class="small"><?= number_format($graficaDetalleAlmacen[$i]["POS_RACKS_DESEABLE"], 2) ?></td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>

                  </div><!--/.box-body-->
                </div>
              </section>
          </div>

          <!--GRAFICA NOMINA POR MES DIEGO ALTAMIRANO SUAREZ-->

          <!--GRAFICA NOMINA POR MES DIEGO ALTAMIRANO SUAREZ-->
        </div>

      </div><!--/.box-body-->
    </div>
    </div><!-- /.col-md-9 -->


    <div class="col-md-3"><!-- col-md-9 -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
        <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
        <a href="vias_informacion.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
        <?php } ?>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body"><!--box-body-->

        <!-- FILTRAR POR CONTRATO -->
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
          <input type="text" name="fecha_ingre_clie" value = "<?= $fecha  ?>"class="form-control pull-right" id="datepicker">
        </div>
        <!-- FILTRAR POR PLAZA -->
        <input id="nomPlaza" type="hidden" value=<?= $plaza ?>>
        <!--<div class="input-group">
          <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
          <select class="form-control select2" id="nomPlaza" style="width: 100%;">
            <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $select_plaza = $modelNomina->sql(2,null,null);
            for ($i=0; $i <count($select_plaza) ; $i++) { ?>
              <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
            <?php } ?>
          </select>
        </div>-->
        <!--FILTRAR POR ALMACEN -->
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Almacen:</span>
          <select class="form-control select2" style="width: 100%;" id="nomAlm">
            <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
            <?php
            $plazas=$plaza;
            //$plazas = $_GET["plaza"];
            $selectAlmacen = $modelNomina->almacenSql($plazas);
            for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
              <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
            <?php } ?>
          </select>
        </div>
        <div class="input-group">
          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btnNomFiltro"><i class="fa fa-check"></i> Filtrar</button> </span>
        </div>

      </div><!--/.box-body-->
    </div>

    </div><!-- /.col-md-3 -->

    <?php
      $valor_usuario = $_SESSION['usuario'];
      if ($valor_usuario == "julio" || $valor_usuario == "diego13" || $valor_usuario == "david") {
    ?>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Subir Excel</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>

        <div class="box-body">

        <div class="outer-container">
            <form action="" method="post"
                name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
                <div>
                    <label for="file" class="subir">
                      <i class="fa fa-cloud-upload"></i>Seleciona Archivo
                    </label>
                    <input type="file" name="file" id="file" accept=".xls,.xlsx" style="display: none;" onchange="cambiar()">
                    <div id="info"></div>
                    <button type="submit" id="submit" name="import"class="btn-submit" disabled="disabled"><i class="fa fa-check"></i>Importar</button>
                </div>

            </form>

        </div>
        <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>
        </div>
      </div>
    </div>
    <?php
  }  ?>

    </div><!-- /.row -->
  </section>
  <!-- ############################ /.SECCION GRAFICA ############################# -->


<?php if ( isset($_GET["fecha"]) || isset($_GET["plaza"]) || isset($_GET["tipo"]) || isset($_GET["status"]) || isset($_GET["contrato"]) || isset($_GET["depto"]) || isset($_GET["almacen"]) ){ ?>
  <!-- ############################ TABLA DETALLE DE NOMINA PAGADA ############################# -->
  <!-- ############################ /.TABLA DETALLE DE NOMINA PAGADA ############################# -->
<?php } ?>


  </section><!-- Termina la seccion de Todo el contenido principal -->
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
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
$('.select2').select2()

</script>
<script type="text/javascript">
/*---- CLICK BOTON FILTRAR ----*/
$(".btnNomFiltro").on("click", function(){
  fecha = $('#datepicker').val();
  plaza = $('#nomPlaza').val();
  almacen = $('#nomAlm').val();

  url = '?fecha='+fecha+'&plaza='+plaza+'&almacen='+almacen;
  location.href = url;

});
</script>
<!-- DataTables -->
<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {

    $('#tabla_nomina').DataTable( {
      "scrollY": 450,
      fixedHeader: true,
      "dom": '<"toolbar">frtip',
      stateSave: true,
      "scrollX": true,
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
            title: 'Gastos',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Gastos',
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
</script>
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
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php for ($i=0; $i <count($graficaNomina) ; $i++) {  ?>
  "<?=$graficaNomina[$i]["PLAZA"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i <count($graficaNomina) ; $i++) {  ?>
  <?=$graficaNomina[$i]["PAGADO"]?>,
  <?php }  ?>
  ];

  $('#graficaNom').highcharts({
    chart: { type: 'column' },
    title: { text: 'GASTOS TOTALES OPERACIONES POR PLAZAS' },
    legend:{
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
          lineWidth: 2,
          //min: 0,
          offset: 10,
          tickWidth: 1,
          title: {
            text: 'Monto'
          },
          labels:{
                formatter: function () {
                  return this.value;
                }
          }
    },
    tooltip:{
            shared: true,
            valueSuffix: ' MXN',
            useHTML: true,
            valueDecimals: 2,
            valuePrefix: '$',
    },
    credits:{
            enabled: false
    },
    lang: {
      printChart: 'Imprimir Grafica',
      downloadPNG: 'Descargar PNG',
      downloadJPEG: 'Descargar JPEG',
      downloadPDF: 'Descargar PDF',
      downloadSVG: 'Descargar SVG',
      contextButtonTitle: 'Exportar grafica'
    },
    colors: ['#464f88'],
    plotOptions:{
                series: {
                  minPointLength: 3
                }
    },
    xAxis: {
      //tickmarkPlacement: 'on',
      //gridLineWidth: 1,
      categories: categories,
      labels: {
        formatter: function () {
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle: {
      text: '* Click en el nombre de la plaza para filtrar',
      align: 'right',
      x: -10,
    },
    series:[{
            name: 'Total Pagado',
            data: data1,
            }]
    });

});
</script>

<script type="text/javascript">
function cambiar(){
    var pdrs = document.getElementById('file').files[0].name;
    document.getElementById('info').innerHTML = pdrs;
    document.getElementById('submit').disabled = false;
}
</script>
<!--grafica por mes -->
<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php
  for ($i=0; $i < $mes_actual ; $i++) {  ?>
  "<?=$graficaNominaMes[$i]["MES"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO"]?>,
  <?php }  ?>
  ];
  var data2 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO2"]?>,
  <?php }  ?>
  ];
  var data3 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO3"]?>,
  <?php }  ?>
  ];
  var data4 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PRESUPUESTO1"]?>,
  <?php }  ?>
  ];
  var data5 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PRESUPUESTO2"]?>,
  <?php }  ?>
  ];
  var data6 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PRESUPUESTO3"]?>,
  <?php }  ?>
  ];

  $('#graficaNomMes2').highcharts({
    chart: { type: 'line' },
    title: { text: <?php if ($plaza == 'ALL' && $almacen == 'ALL' && $tipo == 'ALL'): ?>
                    'GASTOS Y PRESUPUESTOS TOTALES ANUALES' },
                    <?php elseif($almacen == 1):  ?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN PEÑUELA' },
                    <?php elseif($almacen == 2):  ?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN PANTACO' },
                    <?php elseif($almacen == 3):  ?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN VICTORIA' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo == 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);?>
                    'GASTOS Y PRESUPUESTOS ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo != 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);
                          $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS Y PRESUPUESTOS DE <?=$nombreTip[0]["DESCRIPCION"]?>  ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($tipo != 'ALL' && $plaza == 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS Y PRESUPUESTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES' },
                    <?php elseif ($tipo != 'ALL' && $plaza != 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS Y PRESUPUESTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES PLAZA <?=$plaza?>' },
                    <?php else: ?>
                    'GASTOS Y PRESUPUESTOS TOTALES ANUALES PLAZA  <?=$plaza?>' },
                    <?php endif; ?>
    legend:{
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
          lineWidth: 2,
          //min: 0,
          offset: 10,
          tickWidth: 1,
          title: {
            text: 'Monto'
          },
          labels:{
                formatter: function () {
                  return this.value;
                }
          }
    },
    tooltip:{
            shared: true,
            valueSuffix: ' MXN',
            useHTML: true,
            valueDecimals: 2,
            valuePrefix: '$',
    },
    credits:{
            enabled: false
    },
    lang: {
      printChart: 'Imprimir Grafica',
      downloadPNG: 'Descargar PNG',
      downloadJPEG: 'Descargar JPEG',
      downloadPDF: 'Descargar PDF',
      downloadSVG: 'Descargar SVG',
      contextButtonTitle: 'Exportar grafica'
    },
    colors: ['#464f88', '#C21313', '#2DF306'],
    plotOptions:{
                series: {
                  minPointLength: 3
                }
    },
    xAxis: {
      categories: categories,
      labels: {
        formatter: function () {
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle: {
      text: ' ',
      align: 'right',
      x: -10,
    },
      series:[{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha; ?>',
              data: data1,
      }/*,{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-1;?>',
              data: data2,
      },{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-2;?>',
              data: data3,
      }*/,{
              name: 'Total Presupuesto del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha;?>',
              data: data4,
      }/*,{
              name: 'Total Presupuesto del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-1;?>',
              data: data5,
      },{
              name: 'Total Presupuesto del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-2;?>',
              data: data6,
      }*/]
    });

});
</script>


<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php
  for ($i=0; $i < $mes_actual ; $i++) {  ?>
  "<?=$graficaNominaMes[$i]["MES"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO"]?>,
  <?php }  ?>
  ];
  var data2 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO2"]?>,
  <?php }  ?>
  ];
  var data3 = [
  <?php for ($i=0; $i < $mes_actual ; $i++) {  ?>
  <?=$graficaNominaMes[$i]["PAGADO3"]?>,
  <?php }  ?>
  ];

  $('#graficaNomMes').highcharts({
    chart: { type: 'line' },
    title: { text: <?php if ($plaza == 'ALL' && $almacen == 'ALL' && $tipo == 'ALL'): ?>
                    'GASTOS TOTALES ANUALES' },
                    <?php elseif($almacen == 1):  ?>
                    'GASTOS ANUALES DEL ALMACEN PEÑUELA' },
                    <?php elseif($almacen == 2):  ?>
                    'GASTOS ANUALES DEL ALMACEN PANTACO' },
                    <?php elseif($almacen == 3):  ?>
                    'GASTOS ANUALES DEL ALMACEN VICTORIA' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo == 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);?>
                    'GASTOS ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($plaza != 'ALL' && $almacen != 'ALL' && $tipo != 'ALL'): ?>
                    <?php $nombreAlm = $modelNomina->almacenNombre($plaza, $almacen);
                          $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS DE <?=$nombreTip[0]["DESCRIPCION"]?>  ANUALES DEL ALMACEN <?=$nombreAlm[0]["V_NOMBRE"]?>' },
                    <?php elseif ($tipo != 'ALL' && $plaza == 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES' },
                    <?php elseif ($tipo != 'ALL' && $plaza != 'ALL' && $almacen == 'ALL'): ?>
                    <?php $nombreTip = $modelNomina->nombreTipo($tipo);?>
                    'GASTOS DE <?=$nombreTip[0]["DESCRIPCION"]?> ANUALES PLAZA <?=$plaza?>' },
                    <?php else: ?>
                    'GASTOS TOTALES ANUALES PLAZA  <?=$plaza?>' },
                    <?php endif; ?>
    legend:{
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
          lineWidth: 2,
          //min: 0,
          offset: 10,
          tickWidth: 1,
          title: {
            text: 'Monto'
          },
          labels:{
                formatter: function () {
                  return this.value;
                }
          }
    },
    tooltip:{
            shared: true,
            valueSuffix: ' MXN',
            useHTML: true,
            valueDecimals: 2,
            valuePrefix: '$',
    },
    credits:{
            enabled: false
    },
    lang: {
      printChart: 'Imprimir Grafica',
      downloadPNG: 'Descargar PNG',
      downloadJPEG: 'Descargar JPEG',
      downloadPDF: 'Descargar PDF',
      downloadSVG: 'Descargar SVG',
      contextButtonTitle: 'Exportar grafica'
    },
    colors: ['#464f88', '#C21313', '#2DF306'],
    plotOptions:{
                series: {
                  minPointLength: 3
                }
    },
    xAxis: {
      categories: categories,
      labels: {
        formatter: function () {
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
            return '<a href="'+url+'">' +
                this.value + '</a>';
        }
      }
    },
    subtitle: {
      text: ' ',
      align: 'right',
      x: -10,
    },
      series:[{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha; ?>',
              data: data1,
      },{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-1;?>',
              data: data2,
      },{
              name: 'Total Pagado del Año <?php  $andFecha = substr($fecha, 6,4); echo $andFecha-2;?>',
              data: data3,
      }]
    });

});
</script>

<script type="text/javascript">
$(function () {

  Highcharts.setOptions({ lang:{ thousandsSep: ',' } });
  var categories = [
  <?php for ($i=0; $i <count($graficaPlazaAlmacen) ; $i++) {  ?>
  "<?=$graficaPlazaAlmacen[$i]["ALMACEN"]?>",
  <?php }  ?>
  ];
  var data1 = [
  <?php for ($i=0; $i <count($graficaPlazaAlmacen) ; $i++) {  ?>
  <?=$graficaPlazaAlmacen[$i]["PAGADO"]?>,
  <?php }  ?>
  ];

  $('#graficaAlmacen').highcharts({
    chart: { type: 'column' },
    title: { text: <?php if ($plaza == 'ALL' && $almacen == 'ALL'): ?>
                    'ALMACENES DE LA PLAZA' },
                   <?php elseif ($almacen != 'ALL'): ?>
                   'ALMACENES DE LA PLAZA' },
                   <?php ?>
                   <?php else: ?>
                    'ALMACENES DE LA PLAZA <?=$plaza?>' },
                   <?php endif; ?>
    legend:{
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    yAxis:{
          lineWidth: 2,
          //min: 0,
          offset: 10,
          tickWidth: 1,
          title: {
            text: 'Monto'
          },
          labels:{
                formatter: function () {
                  return this.value;
                }
          }
    },
    tooltip:{
            shared: true,
            valueSuffix: ' MXN',
            useHTML: true,
            valueDecimals: 2,
            valuePrefix: '$',
    },
    credits:{
            enabled: false
    },
    lang: {
      printChart: 'Imprimir Grafica',
      downloadPNG: 'Descargar PNG',
      downloadJPEG: 'Descargar JPEG',
      downloadPDF: 'Descargar PDF',
      downloadSVG: 'Descargar SVG',
      contextButtonTitle: 'Exportar grafica'
    },
    colors: ['#464f88'],
    plotOptions:{
                series: {
                  minPointLength: 3
                }
    },
    xAxis: {
      categories: categories,
      labels: {
        formatter: function () {
          url = '?fecha=<?=$fecha?>&plaza='+this.value+'&tipo=<?=$tipo?>&status=<?=$status?>&contrato=<?=$contrato?>&depto=<?=$depto?>&area=<?=$area?>&fil_habilitado=<?=$fil_habilitado?>';
            return '<a>' +
                this.value + '</a>';
        }
      }
    },
    subtitle: {
      text: ' ',
      align: 'right',
      x: -10,
    },
    series:[{
            name: 'Total Pagado',
            data: data1,
            }]
    });

});
</script>

<!-- date-range-picker -->

<script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
  //Date picker
    $('#datepicker').datepicker({
      autoclose: true,
      language: "es",
      format: "yyyy-mm",
      viewMode: "months",
      minViewMode: "months",
      startView: 1,
      minViewMode: 1
    });
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
  $('.fancybox').fancybox();
  $(".fancybox-effects-a").fancybox({
    helpers: { title : { type : 'outside' }, overlay : { speedOut : 0 } }
  });
  $(".fancybox-effects-b").fancybox({openEffect  : 'none',closeEffect : 'none',helpers : {title : {type : 'over'}} });
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
