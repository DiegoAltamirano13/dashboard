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

                $n_parte = 0;
                if(isset($Row[0])) {
                    $n_parte = $Row[0];
                }

                $n_factura = 0;
                if(isset($Row[1])) {
                    $n_factura = $Row[1];
                }

                $n_piezas_dañadas = 0;
                if(isset($Row[2])) {
                    $n_piezas_dañadas = $Row[2];
                }

                $piezas_totales = 0;
                if(isset($Row[3])) {
                    $piezas_totales = $Row[3];
                }

                $fecha_reg = 0;
                if(isset($Row[4])) {
                    $fecha_reg = $Row[4];
                    //echo $fecha_reg."</br>";

                    $fecha = date_create_from_format('m-j-y', strval($fecha_reg));
                    $fecha_real = date_format($fecha, 'd/m/Y');
                    //echo $fecha."</br>";
                    //echo $fecha_real."</br>";

                }

                $proyecto = 0;
                if(isset($Row[5])) {
                    $proyecto = $Row[5];
                }

                if (!empty($n_parte) || !empty($n_factura) || !empty($n_piezas_dañadas) || !empty($piezas_totales) || !empty($fecha_reg)) {
                  //QUERY CONSULTA
                    $consulta = "SELECT COUNT(*)AS ID FROM OP_IN_PIEZAS_DAÑADAS WHERE N_PARTE = '".$n_parte."' AND N_FACTURA = '".$n_factura."' AND N_PIEZAS_DAÑADAS = '".$n_piezas_dañadas."' AND N_TOTAL_PIEZAS = '".$piezas_totales."' AND FECHA_REG = to_date('".$fecha_real."', 'dd/mm/yyyy') AND proyecto = '".$proyecto."'";
                    //echo $consulta;
                    //SE PARSEA
                    $still = oci_parse($conn, $consulta);
                    oci_execute($still);
                    while (oci_fetch($still)) {
                      $reg = oci_result($still, "ID");
                      if( $reg > 0){
                        $type = "warning";
                        $message = "El archivo ya se exporto anteriormente!!!";
                      }
                      else {
                      $query = "INSERT INTO OP_IN_PIEZAS_DAÑADAS(N_PARTE, N_FACTURA,  N_PIEZAS_DAÑADAS, N_TOTAL_PIEZAS , FECHA_REG, PROYECTO) VALUES('".$n_parte."', '".$n_factura."', '".$n_piezas_dañadas."' , '".$piezas_totales."', to_date('".$fecha_real."', 'dd/mm/yyyy' ), '".$proyecto."')";
                      $sti = oci_parse($conn , $query);
                      #echo $query;
                      $lanza = oci_execute($sti);

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
  //header("location:calculo_Ocupacion.php");
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

include_once '../class/Piezas_Danadas.php';
$obj_class = new Calculo_Ocupacion();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 34);
if($modulos_valida == 0)
{
  header('Location: index.php');
}
///////////////////////////////////////////

/* $_GET FECHA */
$fecha = 'ALL';
if ( isset($_GET["fecha"]) ){
  if ( $obj_class->validateDate(substr($_GET["fecha"],0,10)) AND $obj_class->validateDate(substr($_GET["fecha"],11,10)) ){
    $fecha = $_GET["fecha"];
  }else{
    $fecha = "ALL";
  }
}
/* $_GET FIL_CHECK */
$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}

/*----- GET PLAZA -----*/
$plaza = "ALL";
$plaza = $_SESSION['nomPlaza'];

$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}

$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}
//GRAFICA
$grafica_pza_dañada = $obj_class->grafica($plaza,$fecha,$almacen,$fil_check);
$tabla_pza_dañada = $obj_class->tabla($plaza,$fecha,$fil_check);
?>
<!-- ####################################### Incluir Plantilla Principal ##########################-->
<?php include_once('../layouts/plantilla.php'); ?>
<!-- ########################################## Incia Contenido de la pagina ########################################## -->
<!-- DataTables -->
<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.min.css">
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
 <div class="content-wrapper"><!-- Inicia etiqueta content-wrapper principal -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>Piezas No Conforme </small>
      </h1>
    </section>
    <!-- Main content -->

    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

<!-- ############################ SECCION GRAFICA Y WIDGETS ############################# -->
<section>
  <div class="row">
    <div class="col-md-9">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <?php #echo COUNT($graficaCliente); ?>
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> NO CONFORMIDAD POR FACTURA</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->
          <div id="graf_donut_prospectos" style="height: 380px;"></div>

        </div><!--/.box-body-->
      </div>
    </div>
    <!--Subir excel -->
    <?php //if ($plaza != 'ALL'){ ?>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
          <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
          <a href="errores_captura.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
          <?php } ?>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body"><!--box-body-->

          <!-- FILTRAR POR fecha -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> Fecha:</span>
            <input type="text" class="form-control pull-right" name="fil_fecha" disabled>
            <span class="input-group-addon"> <input type="checkbox" name="fil_check" <?php if( $fil_check == 'on' ){ echo "checked";} ?> > </span>
          </div>


          <!-- FILTRAR POR PLAZA -->
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-cubes"></i> Proyecto:</span>
            <select class="form-control select2" id="nomPlaza" style="width: 100%;">
              <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
              <?php
              $select_plaza = $obj_class->sql(2,null,null);
              for ($i=0; $i <count($select_plaza) ; $i++) { ?>
                <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
              <?php } ?>
            </select>
          </div>

          <div class="input-group">
            <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil"><i class="fa fa-check"></i> Filtrar</button> </span>
          </div>
        </div><!--/.box-body-->
      </div>
    </div>
    <?php //} ?>

    <?php
      $valor_usuario = $_SESSION['usuario'];
      if ($valor_usuario == "julio_ope" || $valor_usuario == "diego13" || $valor_usuario == 'erika_qro') {
    ?>
    <div class="col-md-3" style="display:none;">
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
    <?php }  ?>
  </div>
</section>
<section>
  <div class="row">
    <div class="col-md-9">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica</h3>
          <div class="table-responsive" id="container">
           <table id="tabla_nomina_real" class="table table-striped table-bordered" cellspacing="0" width="100%">
             <thead>
               <tr>
                 <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                 <th class="small" bgcolor="#2a7a1a"><font color="white">NUMERO PARTE</font></th>
                 <th class="small" bgcolor="#2a7a1a"><font color="white">NUMERO FACTURA</font></th>
                 <th class="small" bgcolor="#2a7a1a"><font color="white">PROYECTO</font></th>
                 <th class="small" bgcolor="#2a7a1a"><font color="white">TOTAL PIEZAS NO CONFORME</font></th>
                 <th class="small" bgcolor="#2a7a1a"><font color="white">TOTAL PIEZAS</font></th>
                 <th class="small" bgcolor="#2a7a1a"><font color="white">PORCENTAJE</font></th>
                 <th class="small" bgcolor="#2a7a1a"><font color="white">FECHA DE REGISTRO</font></th>
               </tr>
             </thead>
             <tbody>
               <?php  for ($i=0; $i <count($tabla_pza_dañada) ; $i++) { ?>
               <tr>
                 <!--<td class="small">CL</td>-->
                 <td class="small"><?= $tabla_pza_dañada[$i]["N_PARTE"] ?></td>
                 <td class="small"><?= $tabla_pza_dañada[$i]["N_FACTURA"] ?></td>
                 <td class="small"><?= $tabla_pza_dañada[$i]["PROYECTO"] ?></td>
                 <td class="small"><?= $tabla_pza_dañada[$i]["N_PIEZAS_DAÑADAS"] ?></td>
                 <td class="small"><?= $tabla_pza_dañada[$i]["N_TOTAL_PIEZAS"] ?></td>
                 <td class="small"><?= NUMBER_FORMAT(($tabla_pza_dañada[$i]["N_PIEZAS_DAÑADAS"]/$tabla_pza_dañada[$i]["N_TOTAL_PIEZAS"])*100, 2)."%" ?></td>
                 <td class="small"><?= $tabla_pza_dañada[$i]["FECHA_REG"] ?></td>
               </tr>
               <?php } ?>
             </tbody>
             <tfoot style="background-color: #000000; color:#ffffff ">
            </tfoot>
           </table>
         </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ############################ ./SECCION GRAFICA Y WIDGETS ############################# -->
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
<script type="text/javascript">
$('#myTab a').click(function(e) {
  e.preventDefault();
  $(this).tab('show');
});

// store the currently selected tab in the hash value
$("ul.nav-pills > li > a").on("shown.bs.tab", function(e) {
  var id = $(e.target).attr("href").substr(1);
  window.location.hash = id;
});

// on load of the page: switch to the currently selected tab
var hash = window.location.hash;
$('#myTab a[href="' + hash + '"]').tab('show');
</script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
//ACTIVA FILTRO POR FECHA
<?php if ( $fil_check == 'on' AND $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){ ?>
  $('input[name="fil_fecha"]').attr("disabled", false);
<?php } ?>
$('input[name="fil_check"]').on("click", function (){

  if ($('input[name="fil_check"]').is(':checked')) {
    $('input[name="fil_fecha"]').attr("disabled", false);
  }else{
    $('input[name="fil_fecha"]').attr("disabled", true);
  }

});

// CHECA AREAS
$("#fil_departamento").change(function (){

  $.ajax({
    type: 'post',
    url: '../action/rotacion_personal.php',
    data: { "depto" : $(this).val() },
    beforeSend: function () {
      //$('#fil_area').remove();
      $('#fil_area')
      .empty()
      .append('<option value="ALL">ALL</option>');
    },
    success: function (response) {// success
      var dataJson = JSON.parse(response);
        var $select = $('#fil_area');
        //$select.append('<option></option>');
        $.each(dataJson, function(i, val){
          $select.append($('<option></option>').attr('value', val.IID_AREA).text( val.V_DESCRIPCION ));
        });

    }// ./succes
  });

});

//BOTON FILTRAR
$(".btn_fil").on("click", function(){

  fil_fecha = $('input[name="fil_fecha"]').val();
  fil_plaza = $('#nomPlaza').val();
  almacen = $('#nomAlm').val();
  fil_contrato = $('#fil_contrato').val();
  fil_departamento = $('#fil_departamento').val();
  fil_area = $('#fil_area').val();
  fil_check = 'off';

  //Fill habilitados
  fil_habilitado = 'off';

  url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
  if ($('input[name="fil_check"]').is(':checked')) {
    fil_check = 'on';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado;
    }

  }else{
    fil_check = 'off';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
        fil_habilitado = 'on';
        url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado;
    }
    //url = '?plaza='+fil_plaza+'&check='+fil_check+'&contrato='+fil_contrato+'&depto='+fil_departamento+'&area='+fil_area;
  }

  location.href = url;

});

$('.select2').select2()
</script>
<!-- Grafica Highcharts -->
<script src="../plugins/highcharts/highcharts.js"></script>
<script src="../plugins/highcharts/modules/data.js"></script>
<script src="../plugins/highcharts/modules/exporting.js"></script>
<?php
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
                    show: false,
                    radius:2/3,/*0.90 posicion del label con data*/
                    formatter: labelFormatter,
                  },
                }";

$donut_grid =  "hoverable: true,
                clickable: true,
                verticalLines: false,
                horizontalLines: false,";
$donut_legend = "/*labelBoxBorderColor: 'none'*/
                show: true "; //-- PONE LOS LABEL DEL ALDO IZQUIERDO //

$donut_content = '<div style="font-size: 13px; border: 2px solid; padding: 2px; background-color: rgba(255, 247, 255, 0.6); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; border-color: %c;"><center><b>%s</b></center> <b style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px"> %PIEZAS = %y.0 </b>  </div>' ;

$donut_tooltip = "show: true,
      content: '".$donut_content."',
      defaultTheme: true ";
 ?>
<script>
  $(function () {
    /* DONUT CHART */
    var donutData_pros_general = [
      <?php
      $grafica_pastel = $obj_class->tabla($plaza,$fecha,$fil_check);
        for ($i=0; $i <count($grafica_pastel) ; $i++) {
          $plaza = $grafica_pastel[$i]["N_FACTURA"];
          //$plaza_corta = str_word_count($plaza, 1);
          $separador  = ' ';
          $plaza_corta = strstr($plaza, " ", (true));//MUESTRA NOMBRE DE LA PLAZA

          // _-_-_-_-_- VAR DE PARAMETROS DE GRAFICA DONA _-_-_-_-_- //
          switch ('1,2') {
              case '1':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_pastel[$i]["N_FACTURA"].'"><input type="hidden" name="grafica_co_pros" value="1"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_pastel[$i]["N_FACTURA"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$grafica_pastel[$i]["N_FACTURA"].'</button></form>' ;
                break;
              case '2':
               $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_pastel[$i]["N_FACTURA"].'"><input type="hidden" name="grafica_co_pros" value="4"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_pastel[$i]["N_FACTURA"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$grafica_pastel[$i]["N_FACTURA"].'</button></form>' ;
                break;
              case '1,2':
                $label =  '<form method="post"><input type="hidden" name="co_plaza_nombre" value="'.$grafica_pastel[$i]["N_FACTURA"].'"><input type="hidden" name="grafica_co_pros" value="2"><button style="color:#222D32; text-shadow:#fff 1px -1px, #fff -1px 1px, #fff 1px 1px, #fff -1px -1px" type="submit" value="'.$grafica_pastel[$i]["N_FACTURA"].'"  name="co_plaza" class="btn btn-link btn-xs">'.$grafica_pastel[$i]["N_FACTURA"].'</button></form>' ;
                break;
            }
            switch ($i) {
              case '1':
                $color ='#5ABF43';
                break;
              case '2':
                $color = '#3C802D';
                break;
              case '3':
                $color = '#78FF59';
                break;
              case '4':
                $color = '#1F4217';
                break;
              case '5':
                $color = '#6CE650';
                break;
              case '6':
                $color = '#D87A80';
                break;
              case '7':
                $color = '#FF6A6F';
                break;
              default:
                $color = '#5D8AA8';
                break;
            }

          #$data = ($grafica_pastel[$i]["N_PIEZAS_DAÑADAS"]/$grafica_pastel[$i]["N_TOTAL_PIEZAS"])*  100;
          $data = ($grafica_pastel[$i]["N_PIEZAS_DAÑADAS"]/$grafica_pastel[$i]["N_TOTAL_PIEZAS"])*  100;
          $color = $color;

      ?>

        {label: '<?= $label ?>', data: <?=$data?> , color: '<?= $color ?>'},

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
<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {
    $('#tabla_nomina_real').DataTable( {
      "ordering": false,
      "searching":true,
      "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
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
            title: 'Suma Toneladas',
          },

          {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {//muestra/oculta visivilidad de columna
                columns: ':visible',
            },
            title: 'Suma Toneladas',
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


<script type="text/javascript">
function cambiar(){
    var pdrs = document.getElementById('file').files[0].name;
    document.getElementById('info').innerHTML = pdrs;
    document.getElementById('submit').disabled = false;
}
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
<!-- SELECT DATATBLE -->
<script src="../plugins/datatables/extensions/Select/dataTables.select.min.js"></script>
<!-- RESPONSIVE DATATBLE -->
<script src="../plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
$('input[name="fil_fecha"]').daterangepicker(
  {
  "linkedCalendars": false,
  "showDropdowns": true,
//INICIA CODE OPCION PARA FORMATO EN ESPAÑOL
  "locale": {
  "format": "DD/MM/YYYY",
  "separator": "-",
  "applyLabel": "Aplicar",
  "cancelLabel": "Cancelar",
  "fromLabel": "From",
  "toLabel": "To",
  "customRangeLabel": "Fecha Personalizada",
  "daysOfWeek": ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
  "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agusto","Septiembre","Octubre","Noviembre","Diciembre"],
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

    <?php if( $obj_class->validateDate(substr($fecha,0,10)) AND $obj_class->validateDate(substr($fecha,11,10)) ){ ?>
      startDate: '<?=substr($fecha,0,10)?>',
      endDate: '<?=substr($fecha,11,10)?>'
    <?php }else{ ?>
      startDate: moment().subtract(29, 'days'),
      endDate: moment()
    <?php } ?>
  },

);
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
