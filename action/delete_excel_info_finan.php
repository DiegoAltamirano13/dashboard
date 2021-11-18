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
$iid_excel_info_finan = $_POST["delete_reg_info_finan"];
$id_usuario = $_SESSION["iid_empleado"];
$fecha_delete= date('Y/m/d H:i:s');

if ($_POST["delete_reg_info_finan"] == true){
  $delete_excel = $obj_excel_info_finan->delete_excel_info_finan($iid_excel_info_finan,$id_usuario,$fecha_delete);
}else{
  $mensaje_alert = "<b class='text-red'>ERROR =( !!!</b><br>"; 
}

//DETERMINA EL CONTENIDO DEL MENSAJE
if($mensaje_alert) {
  $_SESSION["mensaje_alert"] = $mensaje_alert;
  header('Location: ../seccion/informacion_financiera.php');
} 

?>  