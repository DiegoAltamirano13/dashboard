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
if($modulos_valida == 0)
{ 
  header('Location: index.php');
}
///////////////INTANCIAS
include_once '../class/Informacion_financiera.php';
$obj_excel_info_finan = new Informacion_financiera();

///////////////////////////////////////////


if ( is_uploaded_file($_FILES['update_archivo_excel_info_finan']['tmp_name']) && $_FILES['update_archivo_excel_info_finan']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $_FILES['update_archivo_excel_info_finan']['type'] == 'application/vnd.ms-excel')
{
  $mensaje_alert = "<b class='text-red'>El tamaño del archivo sobrepasa a los 2 MB permitidos!!!</b><br>";
  $size_excel= round($_FILES['update_archivo_excel_info_finan']['size']/1024,2); 

  //inicia validacion para determinar el tamaño maximo del archivo
  if ( $size_excel <= 2048 ){     

    //inicia validacion de campo para inserta en la DB 
     $id_excel = $_POST["id_archivo_excel"];

     $nombre_excel_anterior = substr($_POST["nombre_archivo_excel"],0,10); 

     $extension = end(explode(".", $_FILES['update_archivo_excel_info_finan']['name']));
     $nombre_excel = $nombre_excel_anterior."_INFORMACION_FINANCIERA_".strtotime("now").".".$extension;

     /*-------INICIA REGISTRO DE DATOS-------*/  
     $update_titulo_excel = strtoupper($_POST["update_titulo_excel"]);
     $update_fecha_excel = $_POST["update_fecha_info_finan"];

     $usuario_update = $_SESSION["iid_empleado"];
     $fecha_update = date('Y/m/d H:i:s');
     $status = 2;

     $update_excel_info_finan = $obj_excel_info_finan->update_excel_info_finan($id_excel,$nombre_excel,$update_titulo_excel,$size_excel,$update_fecha_excel,$usuario_update,$fecha_update,$status,$extension);
     /*-------TERMINA REGISTRO DE DATOS-------*/ 
     copy($_FILES['update_archivo_excel_info_finan']['tmp_name'], '/var/www/dashboard/uploads_files/'.$nombre_excel.'');
     $mensaje_alert = "<b class='text-green'>EL REGISTRO CON EL ID ".$id_excel." SE ACTUALIZO CORRECTAMENTE!!!</b><br>"; 

  }else{
    $mensaje_alert = "<b class='text-red'>EL TAMAÑO DEL ARCHIVO SOBREPASA A LOS 2 MB PERMITIDOS!!!</b><br>"; 
  }
  //termina validacion para determinar el tamaño maximo del archivo

}else{

  //inicia validacion de campo para inserta en la DB  

    if ( $_POST["id_archivo_excel"] == true && $_POST["update_titulo_excel"] == true && $_POST["update_fecha_info_finan"] == true){

       /*-------INICIA REGISTRO DE DATOS-------*/ 
       $id_excel = $_POST["id_archivo_excel"]; 
       $update_titulo_excel = strtoupper($_POST["update_titulo_excel"]);
       $update_fecha_excel = $_POST["update_fecha_info_finan"];

       $usuario_update = $_SESSION["iid_empleado"];
       $fecha_update = date('Y/m/d H:i:s');
       $status = 2;
       $update_excel_info_finan = $obj_excel_info_finan->update_excel_info_finan($id_excel,$nombre_excel,$update_titulo_excel,$size_excel,$update_fecha_excel,$usuario_update,$fecha_update,$status,$extension);
       /*-------TERMINA REGISTRO DE DATOS-------*/  
       $mensaje_alert = "<b class='text-green'>EL REGISTRO CON EL ID ".$id_excel." SE ACTUALIZO CORRECTAMENTE!!!</b><br>"; 
    }else{
      $mensaje_alert = "<b class='text-red'>ERROR =( !!!</b><br>"; 
    }
    

}

//DETERMINA EL CONTENIDO DEL MENSAJE
if($mensaje_alert) {
  $_SESSION["mensaje_alert"] = $mensaje_alert;
  header('Location: ../seccion/informacion_financiera.php');
} else {
  $mensaje_alert = "<b class='text-red'>!!!El archivo sobrepasa a los 2MB permitidos</b><br>";
  echo "<br>Menssage ".$mensaje_alert;
  $_SESSION["mensaje_alert"] = $mensaje_alert;
  header('Location: ../seccion/informacion_financiera.php');
}  
?>