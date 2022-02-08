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

                $fecha_reg = 0;
                if(isset($Row[0])) {
                        $fecha_reg = $Row[0];
                        $fecha = date_create_from_format('m-j-y', $fecha_reg);
                        $fecha_real = date_format($fecha, 'd/m/Y');
                }

                $nombre_silo = "";
                if(isset($Row[1])) {
                    $nombre_silo = $Row[1];
                }

                $plaza = 0;
                if(isset($Row[2])) {
                    $plaza = $Row[2];
                }

                $almacen = 0;
                if(isset($Row[3])) {
                    $almacen = intVal($Row[3]);
                }

                $financiera = "";
                if(isset($Row[4])) {
                    $financiera = $Row[4];
                }

                $cd = "";
                if(isset($Row[5])) {
                      $cd = $Row[5];
                }

                $tipo_grano = "";
                if(isset($Row[6])) {
                    $tipo_grano = $Row[6];
                }

                $temperatura = 0.00;
                if(isset($Row[7])) {
                    $temperatura = floatval($Row[7]);
                }

                $humedad = 0.00;
                if(isset($Row[8])) {
                    $humedad = floatval($Row[8]);
                }

                $estatus = 0;
                if(isset($Row[9])) {
                    $estatus = intVal($Row[9]);
                }

                $notas = 0;
                if(isset($Row[10])) {
                    $notas = intVal($Row[10]);
                }

                if (!empty($nombre_silo)) {
                  //QUERY CONSULTA
                    $consulta = "SELECT COUNT(*)AS ID FROM OP_IN_GR_SILOS_TEMP
                                 WHERE NOMBRE_SILO = '".$nombre_silo."'
                                 AND FECHA = TO_DATE('".$fecha_real."', 'dd/mm/yy')
                                 AND IID_PLAZA = $plaza
                                 AND IID_ALMACEN = $almacen
                                 AND CD = '$cd'";

                    #echo $consulta;
                    $still = oci_parse($conn, $consulta);
                    oci_execute($still);
                    while (oci_fetch($still)) {
                      $reg = oci_result($still, "ID");
                      if( $reg > 0){
                        $query2 = "UPDATE OP_IN_GR_SILOS_TEMP
                                        SET ESTATUS = $estatus
                                        WHERE NOMBRE_SILO = '".$nombre_silo."'
                                        AND FECHA = TO_DATE('".$fecha_real."', 'dd/mm/yy')
                                        AND IID_PLAZA = $plaza
                                        AND IID_ALMACEN = $almacen
                                        AND CD = '$cd' ";
                        $sti2 = oci_parse($conn , $query2);
                        #echo $query."</br>";
                        $lanza = oci_execute($sti2);
                      }
                      else {
                    #  echo "prueba llego al = 0";

                      $query = "INSERT INTO OP_IN_GR_SILOS_TEMP
                                      (FECHA,
                                       NOMBRE_SILO,
                                       IID_PLAZA,
                                       IID_ALMACEN,
                                       FINANCIERA,
                                       CD,
                                       TIPO_GRANO,
                                       TEMPERATURA,
                                       HUMEDAD ,
                                       ESTATUS,
                                       NOTAS)
                                VALUES(to_date('".$fecha_real."', 'dd/mm/yy'),
                                        '".$nombre_silo."' ,
                                        ".$plaza." ,
                                        ".$almacen." ,
                                        '".$financiera."' ,
                                        '".$cd."',
                                        '".$tipo_grano."',
                                        $temperatura,
                                        $humedad,
                                        $estatus,
                                        $notas)";
                      $sti = oci_parse($conn , $query);
                      #echo $query."</br>";
                      $lanza = oci_execute($sti);
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


if (isset($_POST["import2"]))
{

  $allowedFileType = array('application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

  if(in_array($_FILES["file2"]["type"],$allowedFileType)){

        $targetPath = 'uploads/'.$_FILES['file2']['name'];
        move_uploaded_file($_FILES['file2']['tmp_name'], $targetPath);

        $Reader = new SpreadsheetReader($targetPath);
        $sheetCount = count($Reader->sheets());
        for($i=0; $i<$sheetCount; $i++)
        {

            $Reader->ChangeSheet($i);

            foreach ($Reader as $Row)
            {

                $fecha_reg = 0;
                if(isset($Row[0])) {
                        $fecha_reg = $Row[0];
                        $fecha = date_create_from_format('m-j-y', $fecha_reg);
                        $fecha_real = date_format($fecha, 'd/m/Y');
                }

                $nombre_silo = "";
                if(isset($Row[1])) {
                    $nombre_silo = $Row[1];
                }

                $plaza = 0;
                if(isset($Row[2])) {
                    $plaza = $Row[2];
                }

                $almacen = 0;
                if(isset($Row[3])) {
                    $almacen = intVal($Row[3]);
                }

                $financiera = "";
                if(isset($Row[4])) {
                    $financiera = $Row[4];
                }

                $cd = "";
                if(isset($Row[5])) {
                      $cd = $Row[5];
                }

                $tipo_grano = "";
                if(isset($Row[6])) {
                    $tipo_grano = $Row[6];
                }

                $impureza = "";
                if(isset($Row[7])) {
                      $impureza = floatval($Row[7]);
                }
                $quebrados = "";
                if(isset($Row[8])) {
                      $quebrados = floatval($Row[8]);
                }
                $danos_calor = "";
                if(isset($Row[9])) {
                      $danos_calor = floatval($Row[9]);
                }
                $grano_verde = "";
                if(isset($Row[10])) {
                      $grano_verde = floatval($Row[10]);
                }
                $podridos = "";
                if(isset($Row[11])) {
                      $podridos = floatval($Row[11]);
                }
                $insectos = "";
                if(isset($Row[12])) {
                      $insectos = floatval($Row[12]);
                }
                $otros = "";
                if(isset($Row[13])) {
                      $otros = floatval($Row[13]);
                }
                $estatus = 0;
                if(isset($Row[14])) {
                    $estatus = intVal($Row[14]);
                }


                if (!empty($nombre_silo)) {
                  //QUERY CONSULTA
                    $consulta = "SELECT COUNT(*)AS ID FROM OP_IN_GR_SILOS_CALIDAD
                                 WHERE NOMBRE_SILO = '".$nombre_silo."'
                                 AND FECHA = TO_DATE('".$fecha_real."', 'dd/mm/yy')
                                 AND IID_PLAZA = $plaza
                                 AND IID_ALMACEN = $almacen
                                 AND CD = '$cd'";
                    #echo $consulta;
                    $still = oci_parse($conn, $consulta);
                    oci_execute($still);
                    while (oci_fetch($still)) {
                      $reg = oci_result($still, "ID");
                      if( $reg > 0){
                        $query2 = "UPDATE OP_IN_GR_SILOS_CALIDAD
                                        SET ESTATUS = $estatus
                                        WHERE NOMBRE_SILO = '".$nombre_silo."'
                                        AND FECHA = TO_DATE('".$fecha_real."', 'dd/mm/yy')
                                        AND IID_PLAZA = $plaza
                                        AND IID_ALMACEN = $almacen
                                        AND CD = '$cd'";
                        $sti2 = oci_parse($conn , $query2);
                        #echo $query."</br>";
                        $lanza = oci_execute($sti2);
                      }
                      else {
                    #  echo "prueba llego al = 0";

                      $query = "INSERT INTO OP_IN_GR_SILOS_CALIDAD
                                      (FECHA,
                                       NOMBRE_SILO,
                                       IID_PLAZA,
                                       IID_ALMACEN,
                                       FINANCIERA,
                                       CD,
                                       TIPO_GRANO,
                                       IMPUREZAS,
                                       QUEBRADOS,
                                       DAÑADOS_X_CALOR,
                                       GRANO_VERDE,
                                       GRANOS_PODRIDOS,
                                       DATOS_X_INSECTOS,
                                       OTROS_DAÑOS,
                                       ESTATUS)
                                VALUES( to_date('".$fecha_real."', 'dd/mm/yy'),
                                        '".$nombre_silo."' ,
                                        $plaza,
                                        $almacen,
                                        '".$financiera."',
                                        '".$cd."',
                                        '".$tipo_grano."',
                                        $impureza,
                                        $quebrados,
                                        $danos_calor,
                                        $grano_verde,
                                        $podridos,
                                        $insectos,
                                        $otros,
                                        $estatus)";
                      $sti = oci_parse($conn , $query);
                      #echo $query."</br>";
                      $lanza = oci_execute($sti);
                    }
                  }
                }
             }
         }
  }
  else
  {
        $type2 = "error";
        $message2 = "Invalid File Type. Upload Excel File.";
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

include_once '../class/Temperatura_Granos.php';
$obj_class = new Temperatura_Granos();
//////////////////////////// INICIO DE AUTOLOAD
function autoload($clase){
    include "../class/" . $clase . ".php";
  }
  spl_autoload_register('autoload');
//////////////////////////// VALIDACION DEL MODULO ASIGNADO
$modulos_valida = Perfil::modulos_valida($_SESSION['iid_empleado'], 28);
$modulos_valida2 = Perfil::modulos_valida($_SESSION['iid_empleado'], 43);
if($modulos_valida == 0 )
{
  header('Location: index.php');
}
///////////////////////////////////////////
error_reporting(0);
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

if($_SESSION['area']==3){
  $plaza=$_SESSION['nomPlaza'];
}else {
  $plaza = "ALL";
}

$plaza=$_SESSION["nomPlaza"];

$silo = "ALL";
if ( isset($_GET["silo"]) ){
  $silo = $_GET["silo"];
}

$fil_check = "ALL";
if ( isset($_GET["check"]) ){
  $fil_check = $_GET["check"];
}

$almacen = "ALL";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}
//GRAFICA
$grafica = $obj_class->grafica($plaza,$fecha,$almacen,$silo,$fil_check);
$graficaGranos = $obj_class->grafica2($plaza,$fecha,$almacen,$silo,$fil_check);
//$graficaGranos = $obj_class->graficaDetallesGranos($plaza,$fecha,$almacen,$silo,$fil_check);

$graficaAlmacen = $obj_class->graficaSilo($plaza,$fecha,$almacen,$silo,$fil_check);

$graficaCliente = $obj_class->graficaCliente($plaza,$fecha,$fil_check,$almacen);
$graficaMensual = $obj_class->graficaMensual($plaza,$fecha,$almacen,$fil_check);
$datos = $obj_class->datos($plaza,$fecha,$almacen,$fil_check);
$capacidad_almacen = $obj_class->capacidad_almacen($plaza,$fecha,$fil_check);
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
        Dashboard<small>TEMPERATURA GRANOS</small> <small>PLAZA ( <?php echo $_SESSION['nomPlaza'] ?> )</small>
      </h1>
    </section>
    <!-- Main content -->
    <section>
        <table  style="margin: 1em;">
            <tr>
              <td  class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Fecha</td>
              <td  class="col-md-1"></td>
              <?php if($_SESSION['area']!=3){ ?>
              <!--<td  class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Plaza</td>-->
            <?php } ?>
              <td  class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Almacen</td>
              <td  class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Silo</td>
              <td  class="col-lg-2 col-md-2 col-sm-2 col-xs-2"></td>
            </tr>
            <tr>
              <td>
                  <input type="text" class="form-control pull-right" name="fil_fecha" disabled>
              </td>
              <td>
                <div class="col col-lg-2">
                  <span class="input-group-addon"> <input  type="checkbox" name="fil_check" <?php if( $fil_check == 'on' ){ echo "checked";} ?> > </span>
                </div>
              </td>

              <input id="fil_plaza" type="hidden" value=<?= $plaza ?>>

              <!--<?php if($_SESSION['area']!=3){ ?>
              <td>
                  <select class="form-control select2" id="fil_plaza" style="width: 100%;">
                    <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
                    <?php
                    $select_plaza = $obj_class->filtros(1,$departamento);;
                    for ($i=0; $i <count($select_plaza) ; $i++) { ?>
                      <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
                    <?php } ?>
                  </select>
              </td>
            <?php } else{?>
              <input id="fil_plaza" type="hidden" value=<?= $plaza ?>>
            <?php }?>-->

              <td>
                <select class="form-control select2" style="width: 100%;" id="nomAlm">
                    <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
                    <?php
                    //$plazas=$plaza;
                  ##  $plazas = $_GET["plaza"];
                    $plaza = $_SESSION["nomPlaza"];
                    $selectAlmacen = $obj_class->almacenSql($plaza);
                    #echo COUNT($selectAlmacen)."  ".$plaza;
                    for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
                      <option value='<?=$selectAlmacen[$i]["IID_ALMACEN"]?>' <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
                    <?php } ?>
                  </select>
              </td>
              <td>
                  <select class="form-control select2 w-25 p-3" id="fil_silo" style="width: 100%;">
                    <option value="ALL" <?php if( $silo == 'ALL'){echo "selected";} ?> >ALL</option>
                    <option value="1" <?php if( $silo == '1'){echo "selected";} ?> >SILO 1</option>
                    <option value="2" <?php if( $silo == '2'){echo "selected";} ?> >SILO 2</option>
                    <option value="3" <?php if( $silo == '3'){echo "selected";} ?> >SILO 3</option>
                    <option value="4" <?php if( $silo == '4'){echo "selected";} ?> >SILO 4</option>
                    <option value="5" <?php if( $silo == '5'){echo "selected";} ?> >SILO 5</option>
                  </select>
              </td>
              <td>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                          <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil"><i class="fa fa-check"></i> Filtrar</button> </span>
                </div>
              </td>
            </tr>
        </table>
    </section>
    <section class="content"><!-- Inicia la seccion de Todo el contenido principal -->

<!-- ############################ SECCION GRAFICA Y WIDGETS ############################# -->
<section>

  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-list-alt"></i>GRANOS</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>

  <div class="box-body"><!--box-body-->

    <div class="nav-tabs-custom">

      <ul class="nav nav-pills" id="myTab">
        <li class="active"><a href="#tab_corporativo" data-toggle="tab"><i class="fa fa-truck"></i> TEMPERATURA GRANOS</a>
        </li>
        <li><a href="#tab_golfo" data-toggle="tab"><i class="fa fa-truck"></i> ANALISIS DE CALIDAD</a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="tab_corporativo">

  <div class="row">
    <div class="col-md-9">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafica Temperatura</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <?php #echo COUNT($graficaCliente); ?>
        <div class="box-body"><!--box-body-->
          <div class="col-md-12">
            <?php if ($silo == 'ALL') {?>
              <div id="graf_perM"></div>

              <div class="table-responsive" id="container">
                <table id="tabla_nomina_real" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                      <th class="small" bgcolor="#2a7a1a"><font color="white">FECHA</font></th>
                      <th class="small" bgcolor="#2a7a1a"><font color="white">NOMBRE</font></th>
                      <th class="small" bgcolor="#2a7a1a"><font color="white">GRANO</font></th>
                      <th class="small" bgcolor="#2a7a1a"><font color="white">FINANCIERA</font></th>
                      <th class="small" bgcolor="#2a7a1a"><font color="white">CERTIFICADO</font></th>
                      <th class="small" bgcolor="#2a7a1a"><font color="white">ESTATUS CD</font></th>
                      <th class="small" bgcolor="#2a7a1a"><font color="white">TEMPERATURA ANALISIS</font></th>
                      <th class="small" bgcolor="#2a7a1a"><font color="white">HUMEDAD</font></th>
                      <th class="small" bgcolor="#2a7a1a"><font color="white">ESTATUS</font></th>
                      <th class="small" bgcolor="#2a7a1a"><font color="white">NOTA</font></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i <count($grafica) ; $i++) { ?>
                    <tr>
                      <!--<td class="small">CL</td>

                      -->
                      <td class="small"><?= $grafica[$i]["FECHA"] ?></td>
                      <td class="small"><?= $grafica[$i]["NOMBRE_SILO"] ?></td>
                      <td class="small"><?= $grafica[$i]["TIPO_GRANO"] ?></td>
                      <td class="small"><?= $grafica[$i]["FINANCIERA"] ?></td>
                      <td class="small"><?= $grafica[$i]["CD"] ?></td>

                      <td class="small"><?= $grafica[$i]["ESTATUS"] ?></td>
                      <td class="small"><?= $grafica[$i]["TEMPERATURA"] ?> °C</td>
                      <td class="small"><?= $grafica[$i]["HUMEDAD"] ?> %</td>
                      <td class="small"><?php
                                        if ($grafica[$i]["TEMPERATURA"] > 27.0) { ?>
                                            <input  class="btn btn-danger" type='submit' name='' value='Notificar' id='boton1' onclick = "enviar_correos('<?= str_replace(array('(',')'),'',$grafica[$i]["NOMBRE_SILO"])?>', '<?= $grafica[$i]["CD"] ?>', '<?= $grafica[$i]["FECHA"]?>',
                                                                                                  '<?= $grafica[$i]["TIPO_GRANO"] ?>', '<?= $grafica[$i]["FINANCIERA"] ?>')"/>
                                        <?php
                                      }else{
                                          echo "DENTRO DEL RANGO";
                                        }
                      ?></td>
                      <td class="small"><?= $grafica[$i]["NOTAS"] ?></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <input type="hidden" name="ejemplo" id="ejemplo" value="">
              </div>
            <?php }else {?>
              <?php if ($silo <> 'ALL') { ?>
                <div id="graf_perAlmacen"></div>

                <div class="table-responsive" id="container">
                  <table id="tabla_nomina_real" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                      <tr>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">FECHA</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">TIPO GRANO</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">FINANCIERA </font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">CERTIFICADO</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">ESTATUS CD</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">TEMPERATURA ANALISIS</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">HUMEDAD</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">ESTATUS </font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">NOTA</font></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php for ($i=0; $i <count($graficaAlmacen) ; $i++) { ?>
                      <tr>
                        <!--<td class="small">CL</td>-->
                        <td class="small"><?= $graficaAlmacen[$i]["FECHA"] ?></td>
                        <td class="small"><?= $graficaAlmacen[$i]["TIPO_GRANO"] ?></td>
                        <td class="small"><?= $graficaAlmacen[$i]["FINANCIERA"] ?></td>
                        <td class="small"><?= $graficaAlmacen[$i]["CD"] ?></td>
                        <td class="small"><?= $graficaAlmacen[$i]["ESTATUS"] ?></td>
                        <td class="small"><?= $graficaAlmacen[$i]["TEMPERATURA"] ?> °C</td>
                        <td class="small"><?= $graficaAlmacen[$i]["HUMEDAD"] ?> %</td>
                        <td class="small"><?php
                                          if ($graficaAlmacen[$i]["TEMPERATURA"] > 27.0) { ?>
                                            <input class="btn btn-danger" type='submit' name='' value='Buscar' id='boton1' onclick = "enviar_correos('<?= str_replace(array('(',')'),'',$graficaAlmacen[$i]["NOMBRE_SILO"])?>', '<?= $graficaAlmacen[$i]["CD"] ?>', '<?= $graficaAlmacen[$i]["FECHA"]?>',
                                                                                                  '<?= $graficaAlmacen[$i]["TIPO_GRANO"] ?>', '<?= $graficaAlmacen[$i]["FINANCIERA"] ?>')"/>
                                          <?php
                                        }else {
                                            echo "DENTRO DEL RANGO";
                                          }
                        ?></td>
                        <td class="small"><?= $graficaAlmacen[$i]["NOTAS"] ?></td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              <?php }?>
              <?php }?>
            <?php if ($plaza == 'ALL' && $almacen == 'ALL') {?>

            <?php }?>
          </div>
        </div><!--/.box-body-->
      </div>
    </div>

    <!--Subir excel -->
    <?php
      $valor_usuario = $_SESSION['usuario'];
      if ($valor_usuario == "jose_cba" || $valor_usuario == "diego13" || $valor_usuario == 'david') {
    ?>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Subir Excel Temperaturas</h3>
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
                      <i class="fa fa-cloud-upload"></i>Selecciona Archivo
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

    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-sliders"></i> Subir Excel Calidad</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>

        <div class="box-body">

        <div class="outer-container">
            <form action="" method="post"
                name="frmExcelImport2" id="frmExcelImport2" enctype="multipart/form-data">
                <div>
                    <label for="file2" class="subir">
                      <i class="fa fa-cloud-upload"></i>Selecciona Archivo
                    </label>
                    <input type="file" name="file2" id="file2" accept=".xls,.xlsx" style="display: none;" onchange="cambiar2()">
                    <div id="info2"></div>
                    <button type="submit" id="submit2" name="import2"class="btn-submit" disabled="disabled"><i class="fa fa-check"></i>Importar</button>
                </div>

            </form>

        </div>
        <div id="response2" class="<?php if(!empty($type2)) { echo $type2 . " display-block"; } ?>"><?php if(!empty($message2)) { echo $message2; } ?></div>
        </div>
      </div>
    </div>
    <?php }  ?>
  </div>

  </div>

  <div class="tab-pane" id="tab_golfo">

    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bar-chart"></i> Registro Analisis De Calidad</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body"><!--box-body-->
            <div class="col-md-12">
                <!--<?php if ($silo == 'ALL') { ?>
                    <div id="graf_bar"></div>
                <?php }else { ?>
                    <div id="graf_perW"></div>
                <?php } ?>


                <div id="graf_perM2"></div>
                <div id="graf_perM3"></div>
                <div id="graf_perM4"></div>
                <div id="graf_perM5"></div>
                <div id="graf_perM6"></div>
                <div id="graf_perM7"></div>
                <div id="graf_perM8"></div>-->
                <div class="table-responsive" id="container">
                  <table id="tabla_granos_dañados" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                      <tr>
                        <!--<th class="small" bgcolor="#2a7a1a"><font color="white">ID</font></th>-->
                        <th class="small" bgcolor="#2a7a1a"><font color="white">NOMBRE</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">FECHA</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">CD</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">ESTATUS</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">GRANO</font></th>
                        <!--<th class="small" bgcolor="#2a7a1a"><font color="white">HUMEDAD</font></th>-->
                        <th class="small" bgcolor="#2a7a1a"><font color="white">IMPUREZAS</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">QUEBRADOS</font></th>
                         <th class="small" bgcolor="#2a7a1a"><font color="white">CALOR</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">GRANO VERDE</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">PODRIDOS</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">INSECTOS</font></th>
                        <th class="small" bgcolor="#2a7a1a"><font color="white">OTROS</font></th>
                        <!--<th class="small" bgcolor="#2a7a1a"><font color="white">SANIDAD</font></th>-->
                        <th class="small" bgcolor="#2a7a1a"><font color="white">NOTIFICAR</font></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php for ($i=0; $i <count($graficaGranos) ; $i++) { ?>
                      <tr>

                        <td class="small"><?= $graficaGranos[$i]["NOMBRE_SILO"] ?></td>
                        <td class="small"><?= $graficaGranos[$i]["FECHA"] ?></td>
                        <td class="small"><?= $graficaGranos[$i]["CD"] ?></td>
                        <td class="small"><?= $graficaGranos[$i]["ESTATUS"] ?></td>
                        <td class="small"><?= $graficaGranos[$i]["TIPO_GRANO"] ?></td>
                        <!--<td class="small"><?= $graficaGranos[$i]["HUMEDAD"] ?>%</td>-->
                        <td class="small"><?= $graficaGranos[$i]["IMPUREZAS"] ?>%</td>
                        <td class="small"><?= $graficaGranos[$i]["QUEBRADOS"] ?>%</td>
                        <td class="small"><?= $graficaGranos[$i]["CALOR"] ?>%</td>
                        <td class="small"><?= $graficaGranos[$i]["GRANO_VERDE"] ?>%</td>
                        <td class="small"><?= $graficaGranos[$i]["PODRIDOS"] ?>%</td>
                        <td class="small"><?= $graficaGranos[$i]["INSECTOS"] ?>%</td>
                        <td class="small"><?= $graficaGranos[$i]["OTROS"] ?>%</td>
                        <!--<td class="small"><?= $graficaGranos[$i]["SANIDAD"] ?>%</td>-->
                        <td class="small">

                            <?php
                            $cadenaOriginal = $graficaGranos[$i]["NOMBRE_SILO"];
                             $cadena_final = preg_replace("/\((.*?)\)/i", " ", $cadenaOriginal);
                             if ($graficaGranos[$i]["IMPUREZAS"] > 3 OR $graficaGranos[$i]["QUEBRADOS"] > 15
                                OR $graficaGranos[$i]["CALOR"] > 5 OR $graficaGranos[$i]["GRANO_VERDE"] > 5  ) {
                               ?>
                               <button type="button" name='button' class="btn btn-default" data-toggle='modal' data-target='#asignacion_activos'
                               onclick="add(this)"><i class="fa fa-envelope"></i> Notificar</button>
                            <?php
                          }else {
                            echo "Dentro Del Rango";
                          }
                            ?>



                        </td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                  <input type="hidden" name="ejemplo" id="ejemplo" value="">
                </div>

            </div>
          </div><!--/.box-body-->
        </div>
      </div>

      <?php //if ($plaza != 'ALL'){ ?>
      <div class="col-md-3" style="display:none">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-sliders"></i> Filtros</h3>
            <?php if ( strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['PHP_SELF']) ){ ?>
            <a href="temperatura_Granos.php"><button class="btn btn-sm btn-warning">Borrar Filtros <i class="fa fa-close"></i></button></a>
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
            <div class="input-group" style="display">
              <span class="input-group-addon"><i class="fa fa-cubes"></i> Plaza:</span>
              <select class="form-control select2" id="fil_plaza" style="width: 100%;">
                <option value="ALL" <?php if( $plaza == 'ALL'){echo "selected";} ?> >ALL</option>
                <?php
                $select_plaza = $obj_class->filtros(1,$departamento);;
                for ($i=0; $i <count($select_plaza) ; $i++) { ?>
                  <option value="<?=$select_plaza[$i]["PLAZA"]?>" <?php if( $select_plaza[$i]["PLAZA"] == $plaza){echo "selected";} ?>> <?=$select_plaza[$i]["PLAZA"]?> </option>
                <?php } ?>
              </select>
            </div>

            <div class="input-group" style="display">
              <span class="input-group-addon"><i class="fa fa-file-powerpoint-o"></i> Almacen:</span>
              <select class="form-control select2" style="width: 100%;" id="nomAlm">
                <option value="ALL" <?php if( $almacen == 'ALL'){echo "selected";} ?> >ALL</option>
                <?php
                $plazas = $_GET["plaza"];
                $selectAlmacen = $obj_class->almacenSql($plazas);
                for ($i=0; $i <count($selectAlmacen) ; $i++) { ?>
                  <option value="<?=$selectAlmacen[$i]["IID_ALMACEN"]?>" <?php if($selectAlmacen[$i]["IID_ALMACEN"] == $almacen){echo "selected";} ?>><?=$selectAlmacen[$i]["V_NOMBRE"]?> </option>
                <?php } ?>
              </select>
            </div>

            <div class="input-group" style="display">
              <span class="input-group-addon"><i class="fa fa-cubes"></i> Silo:</span>
              <select class="form-control select2" id="fil_silo" style="width: 100%;">
                <option value="ALL" <?php if( $silo == 'ALL'){echo "selected";} ?> >ALL</option>
                <option value="Silo 1" <?php if( $silo == 'Silo 1'){echo "selected";} ?> >SILO 1</option>
                <option value="Silo 2" <?php if( $silo == 'Silo 2'){echo "selected";} ?> >SILO 2</option>
                <option value="Silo 3" <?php if( $silo == 'Silo 3'){echo "selected";} ?> >SILO 3</option>
                <option value="Silo 4" <?php if( $silo == 'Silo 4'){echo "selected";} ?> >SILO 3</option>
                <option value="Silo 5" <?php if( $silo == 'Silo 5'){echo "selected";} ?> >SILO 5</option>
              </select>
            </div>

            <!-- FILTRAR POR AREA -->
            <div class="input-group" >
              <span class="input-group-addon"> <button type="button" class="btn btn-primary btn-xs pull-right btn_fil2"><i class="fa fa-check"></i> Filtrar</button> </span>
            </div>

          </div><!--/.box-body-->
        </div>
      </div>
      <?php //} ?>

      <!--Subir excel -->
      <?php
        $valor_usuario = $_SESSION['usuario'];
        if ($valor_usuario == "jose_cba" || $valor_usuario == "diego13" || $valor_usuario == 'david') {
      ?>
      <div class="col-md-3" style="display:none">
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
                        <i class="fa fa-cloud-upload"></i>Selecciona Archivo
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

  </div>


  </div>
  </div>
  </div>
</section>

<!-- ############################ ./SECCION GRAFICA Y WIDGETS ############################# -->
</section><!-- Termina la seccion de Todo el contenido principal -->
    <!-- /.content -->

<!-- ################################### Termina Contenido de la pagina ################################### -->
 <!-- Incluye Footer -->
<?php include_once('../layouts/footer.php'); ?>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
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
  fil_plaza = $('#fil_plaza').val();
  fil_silo = $('#fil_silo').val();
  almacen = $('#nomAlm').val();
  fil_contrato = $('#fil_contrato').val();
  fil_departamento = $('#fil_departamento').val();
  fil_area = $('#fil_area').val();
  fil_check = 'off';


  fil_habilitado = 'off';

  url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&silo='+fil_silo;
  if ($('input[name="fil_check"]').is(':checked')) {
    fil_check = 'on';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&silo='+fil_silo;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&silo='+fil_silo;
    }

  }else{
    fil_check = 'off';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
        fil_habilitado = 'on';
        url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado+'&silo='+fil_silo;
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado+'&silo='+fil_silo;
    }
    //url = '?plaza='+fil_plaza+'&check='+fil_check+'&contrato='+fil_contrato+'&depto='+fil_departamento+'&area='+fil_area;
  }

  location.href = url;

});

$('.select2').select2()
</script>

<script>
     function enviar_correos(nombre_silo, cd, fechas, tipo_grano, financiera){
       var n_silo = nombre_silo;
       var cd_n = cd;
       var fechat = fechas;
       var t_grano = tipo_grano;
       var finan = financiera;
           $.ajax({
               type:'GET', //aqui puede ser igual get
               url: '../class/enviarMail.php',//aqui va tu direccion donde esta tu funcion php
               data: "valor_nombre="+ n_silo +"&numcd="+cd_n+"&fechas_a="+fechat+"&tipo_grano="+t_grano+"&financiera="+finan,//aqui tus datos
               success:function(data){
                  //alert(data);
                  /**/
                       alert("Correo Enviado Correctamente");

               },
               error:function(data){
                   alert("Correo No Enviado");
               }
            });

     }
</script>

<script>
     function cargarPantalla(iidconsecutivo){

     }

     function add(button) {
    var row = button.parentNode.parentNode;
    var cells = row.querySelectorAll('td:not(:last-of-type)');
    var valor = cells[0].innerText;
    if(valor){
        document.getElementById("ejemplo").value = valor;
        $.ajax({
            type:'POST', //aqui puede ser igual get
            url: '../class/enviarMail.php',//aqui va tu direccion donde esta tu funcion php
            data: "valor_nombre="+ valor,//aqui tus datos
            success:function(data){

                    alert("Correo Enviado Correctamente");

            },
            error:function(data){
                alert("Correo No Enviado");
            }
         });
    }
}
</script>

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
$(".btn_fil2").on("click", function(){

  fil_fecha = $('input[name="fil_fecha"]').val();
  fil_plaza = $('#fil_plaza').val();
  fil_silo = $('#fil_silo').val();
  almacen = $('#nomAlm').val();
  fil_contrato = $('#fil_contrato').val();
  fil_departamento = $('#fil_departamento').val();
  fil_area = $('#fil_area').val();
  fil_check = 'off';

  //Fill habilitados
  fil_habilitado = 'off';

  url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&silo='+fil_silo+'#tab_golfo';
  if ($('input[name="fil_check"]').is(':checked')) {
    fil_check = 'on';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
      fil_habilitado = 'on';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&silo='+fil_silo+'#tab_golfo';
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&check='+fil_check+'&fecha='+fil_fecha+'&almacen='+almacen+'&fil_habilitado='+fil_habilitado+'&silo='+fil_silo+'#tab_golfo';
    }

  }else{
    fil_check = 'off';
    if ($('input[name="fil_habilitado"]').is(':checked')) {
        fil_habilitado = 'on';
        url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado+'&silo='+fil_silo+'#tab_golfo';
    }
    else {
      fil_habilitado = 'off';
      url = '?plaza='+fil_plaza+'&almacen='+almacen+'&check='+fil_check+'&fil_habilitado='+fil_habilitado+'&silo='+fil_silo+'#tab_golfo';
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

<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {
    $('#tabla_nomina_real').DataTable( {
      "ordering": true,
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
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust()
      .responsive.recalc();
});

$(document).ready(function() {
    $('#tabla_granos_dañados').DataTable( {
      "ordering": true,
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

$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($grafica) ; $i++) {
         echo "'".$grafica[$i]["NOMBRE_SILO"]."',";

     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($grafica) ; $i++) {
      echo "40"." ,";
    }
    ?>
    ];
    var data2 = [
    <?php
    for ($i=0; $i <count($grafica) ; $i++) {
      echo "35"." ,";
    }
    ?>
    ];
    var data3 = [
    <?php
    for ($i=0; $i <count($grafica) ; $i++) {
      echo number_format($grafica[$i]["TEMPERATURA"], 2)." ,";
    }
    ?>
    ];
    var data4 = [
    <?php
    for ($i=0; $i <count($grafica) ; $i++) {
      echo number_format($grafica[$i]["HUMEDAD"], 2).",";
    }
    ?>
    ];
    $('#graf_perM').highcharts({
        chart: {
            type: 'line'
        },
         title: {
            text: 'TEMPERATURA SILOS  <?php if ($plaza == "ALL") { ECHO "DE TODAS LAS PLAZAS "; }else { echo " DE PLAZA ".$plaza;} ?>'
        },

        legend: {
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },

        yAxis: {
            lineWidth: 2,
            min: 0,
            offset: 10,
            tickWidth: 1,
            plotLines: [{
                value: 35,
                color: 'orange',
                dashStyle: 'shortdash',
                width: 2,
                label: {
                    text: 'ALERTA DE CALENTAMIENTO'
                }
            }, {
                value: 40,
                color: 'red',
                dashStyle: 'shortdash',
                width: 2,
                label: {
                    text: 'LIMITE PERMITIDO '
                }
            }],
            title: {
                text: 'TEMPERATURAS'
            },
            labels: {
                formatter: function () {
                  return this.value;
                }
            }
        },
        tooltip: {
          shared: true,
          valueSuffix: ' ',
          useHTML: true,
          valueSuffix: ' °C',
        },
        lang: {
          printChart: 'Imprimir Grafica',
          downloadPNG: 'Descargar PNG',
          downloadJPEG: 'Descargar JPEG',
          downloadPDF: 'Descargar PDF',
          downloadSVG: 'Descargar SVG',
          contextButtonTitle: 'Exportar grafica'
        },
        credits: {
            enabled: false
        },
        colors: ['#FF0000', '#7CFC00', '#FF5733' ],
        plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: false,
              format: '{y} °C'
            },
            enableMouseTracking:true
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>';
                return '<a>' +
                    this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: '',
          align: 'right',
          x: -10,
        },
        series:  [{
        //  showInLegend:false,

            name: ' TEMPERATURA ANALISIS',
            color: 'green',
            data: data3,
        }/*,{
        //  showInLegend:false,
            name: ' HUMEDAD',
            data: data4,
        }*/
        ]

    });
});
</script>

<script type="text/javascript">

$(function () {

    Highcharts.setOptions({
    lang: {
      thousandsSep: ','
    }
    });
    var categories = [
    <?php
     for ($i=0; $i <count($graficaAlmacen) ; $i++) {
          echo "'".$graficaAlmacen[$i]["FECHA"]."',";
     }
    ?>
    ];
    var data1 = [
    <?php
    for ($i=0; $i <count($graficaAlmacen) ; $i++) {
            echo "40 ,";
    }
    ?>
    ];
    var data2 = [
    <?php
    for ($i=0; $i <count($graficaAlmacen) ; $i++) {
            echo "35 ,";
    }
    ?>
    ];
    var data3 = [
    <?php
    for ($i=0; $i <count($graficaAlmacen) ; $i++) {
            echo ROUND(($graficaAlmacen[$i]["TEMPERATURA"]), 2)." ,";
    }
    ?>
    ];
    $('#graf_perAlmacen').highcharts({
        chart: {
            type: 'line'
        },
         title: {
            text: 'Temperaturas Del  <?php echo $silo; ?>'
        },

        legend: {
            y: -40,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },

        yAxis: {
            lineWidth: 2,
            min: 0,
            max: 50,
            offset: 20,
            tickWidth: 1,
            plotLines: [{
                value: 35,
                color: 'orange',
                dashStyle: 'shortdash',
                width: 2,
                label: {
                    text: 'ALERTA DE CALENTAMIENTO'
                }
            }, {
                value: 40,
                color: 'red',
                dashStyle: 'shortdash',
                width: 2,
                label: {
                    text: 'LIMITE PERMITIDO '
                }
            }],
            title: {
                text: ' Temperatura'
            },

        },
        tooltip: {
          shared: true,
          valueSuffix: ' ',
          useHTML: true,
          //valueDecimals: 2,
          //valuePrefix: '$',
          valueSuffix: ' °C'
        },
        lang: {
          printChart: 'Imprimir Grafica',
          downloadPNG: 'Descargar PNG',
          downloadJPEG: 'Descargar JPEG',
          downloadPDF: 'Descargar PDF',
          downloadSVG: 'Descargar SVG',
          contextButtonTitle: 'Exportar grafica'
        },
        credits: {
            enabled: false
        },
        colors: ['#FF0000', '#7CFC00', '#FF5733'],
        plotOptions: {
          series: {
            minPointLength: 3,
            dataLabels:{
              enabled: false,
              format: '{y} °C'
            },
            enableMouseTracking:true
          }
        },
        xAxis: {
          //tickmarkPlacement: 'on',
          //gridLineWidth: 1,
          categories: categories,
          labels: {
            formatter: function () {
              url = '?plaza='+this.value+'&check=<?= $fil_check; ?>';
              url = '?plaza='+this.value+'&check=<?=$fil_check?>&fecha=<?=$fecha?>';
                return '<a>' +
                    this.value + '</a>';
            }
          }
        },
        subtitle: {
          text: '',
          align: 'right',
          x: -10,
        },
        series:  [
        {
        //  showInLegend:false,
            color: 'green',
            name: ' TEMPERATURA DE ANALISIS',
            data: data3,
        }
        ]

    });
});
</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#tabla_nomina').DataTable( {
        "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    //i.replace('.','').replace(/[\$,]/g, '.')*1:
                    typeof i === 'number' ?
                        i : 0;
            };

            // Total over all pages
            total = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return Intl.NumberFormat().format(intVal(a) + intVal(b));
                    //return intVal(a) + intVal(b);
                    //return parseFloat(intVal(a)) + parseFloat(intVal(b));
                }, 0 );

            // Total over this page
            pageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return Intl.NumberFormat().format(intVal(a) + intVal(b));
                    //return Math.round(intVal(a) + intVal(b));
                }, 0 );

            // Update footer
            $( api.column( 5 ).footer() ).html(
                //''+pageTotal +' ('+ total +' total)'
                ''+pageTotal +' Toneladas'
            );
        },

        "scrollY": 450,
        fixedHeader: true,
        "dom": '<"toolbar">frtip',
        stateSave: true,
        "scrollX": true,
        "language": {
            "url": "../plugins/datatables/Spanish.json"
        },

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
    } );
} );

</script>


<script type="text/javascript">
function cambiar(){
    var pdrs = document.getElementById('file').files[0].name;
    document.getElementById('info').innerHTML = pdrs;
    document.getElementById('submit').disabled = false;
}
</script>

<script type="text/javascript">
function cambiar2(){
    var pdrs = document.getElementById('file2').files[0].name;
    document.getElementById('info2').innerHTML = pdrs;
    document.getElementById('submit2').disabled = false;
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
