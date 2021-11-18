<?php
ini_set('display_errors', false);

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

$iid_empleado = $_SESSION['iid_empleado'];
$modulos_valida = Perfil::modulos_valida($iid_empleado, '10');
if($modulos_valida == 0 )
{ 
  header('Location: index.php');
}
///////////////INTANCIAS
include_once '../class/Informacion_financiera.php';
$obj_excel_info_finan = new Informacion_financiera();

///////////////////////////////////////////
if ( is_uploaded_file($_FILES['archivo_excel_info_finan']['tmp_name']) && $_FILES['archivo_excel_info_finan']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $_FILES['archivo_excel_info_finan']['type'] == 'application/vnd.ms-excel' )
{
  $mensaje_alert = "<b class='text-red'>El tamaño del archivo sobrepasa a los 2 MB permitidos!!!</b><br>";
  $size_excel= round($_FILES['archivo_excel_info_finan']['size']/1024,2); 

  //inicia validacion para determinar el tamaño maximo del archivo
  if ( $size_excel <= 2048 ){
    $mensaje_alert = "<b class='text-red'>Faltan rellenar algunos campos!!!</b><br>";    

    //inicia validacion de campo para inserta en la DB
    if ( $_POST["titulo_excel"] == true && $_POST["fecha_info_finan"] == true && $_SESSION["iid_empleado"] == true ){
     $extension = end(explode(".", $_FILES['archivo_excel_info_finan']['name']));
     $nombre_excel = strtotime("now")."_INFORMACION_FINANCIERA.".$extension; 
     //move_uploaded_file($_FILES['archivo_excel_info_finan']['tmp_name'], '/var/www/dashboard/uploads_files/'.$nombre_excel.'');

     /*-------INICIA REGISTRO DE DATOS-------*/ 
     $titulo_excel= strtoupper($_POST["titulo_excel"]);
     $fecha_info_finan= $_POST["fecha_info_finan"];
     $usuario_insert = $_SESSION["iid_empleado"];
     $fecha_insert = date('Y/m/d H:i:s');
     $status = 1;
     $insert_excel_info_finan = $obj_excel_info_finan->insertar_excel_info_finan($nombre_excel,$titulo_excel,$size_excel,$fecha_info_finan,$usuario_insert,$fecha_insert,$status,$extension);
     /*-------TERMINA REGISTRO DE DATOS-------*/ 
     copy($_FILES['archivo_excel_info_finan']['tmp_name'], '/var/www/dashboard/uploads_files/'.$nombre_excel.'');
     $mensaje_alert = "<b class='text-green'>Archivo subido correctamente!!!</b><br> <b class='text-green'>Datos registrados correctamente!!!</b><br>";
    }
    //termina validacion de campo para inserta en la DB

  }else{
    $mensaje_alert = "<b class='text-red'>El tamaño del archivo sobrepasa a los 2 MB permitidos!!!</b><br>"; 
  }
  //termina validacion para determinar el tamaño maximo del archivo

}

//DETERMINA EL CONTENIDO DEL MENSAJE
if($mensaje_alert) {
  $_SESSION["mensaje_alert"] = $mensaje_alert;
  header('Location: ../seccion/informacion_financiera.php');
} else {
  $mensaje_alert = "<b class='text-red'>!!!No se eligió un archivo Excel (.xls ó .xlsx) ó el archivo sobrepasa a los 2MB permitidos</b><br>";
  echo "<br>Menssage ".$mensaje_alert;
  $_SESSION["mensaje_alert"] = $mensaje_alert;
  header('Location: ../seccion/informacion_financiera.php');
} 
?>