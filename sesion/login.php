<?php

session_start();

include_once '../libs/conOra.php';
$conn 	= conexion::conectar();

include_once '../class/Perfil.php';
$instacia_modulo  = new Perfil;


$userName 		= $_POST['formusername'];
$userPassword 	= $_POST['formpassword'];
$userPassword =  $_POST['formpassword'];

/*
if ($userName == "jesus_cb") {
  $userPassword = "merino%148";
}
*/

/*Ejecutar el paquete*/
$stid = oci_parse($conn, "BEGIN PCK_LOGIN.VALIDA_USUARIO(:p_vid_usuario, :p_v_password1, :p_iid_empleado, :p_i_adm, :p_code, :p_code_sql, :p_msg); END;");
oci_bind_by_name($stid, ':p_vid_usuario', $userName); //3
oci_bind_by_name($stid, ':p_v_password1', $userPassword); //2019
oci_bind_by_name($stid, ':p_iid_empleado', $iid_empleado, 300); // ALL
oci_bind_by_name($stid, ':p_i_adm',$padm, 300);
oci_bind_by_name($stid, ':p_code',$pcode, 300);
oci_bind_by_name($stid, ':p_code_sql',$pcodesql,300);
oci_bind_by_name($stid, ':p_msg',$error_msg, 300);

oci_execute($stid);

if ($pcode == 1 or $pcode == 2){
    /*Ejecutar consulta*/

    /*$sql			=  "SELECT COUNT(*) AS cuenta,
    perm.iid_empleado AS iid_empelado, users.v_nombre AS  nombre_user
    FROM se_usuarios users
    INNER JOIN ss_permisos_modulos perm ON perm.iid_empleado = users.iid_empleado
    INNER JOIN no_personal personal ON personal.iid_empleado = users.iid_empleado AND personal.iid_empleado = perm.iid_empleado
    WHERE users.vid_usuario = '".$userName."'
    AND personal.s_status = 1
    group by (perm.iid_empleado, users.v_nombre )";*/

    $sql= "SELECT   COUNT(*) AS cuenta,
                    perm.iid_empleado AS iid_empelado, users.v_nombre AS  nombre_user, contrato.iid_depto, contrato.iid_plaza, replace(p.v_razon_social, ' (ARGO)') as plaza
          FROM      se_usuarios users
                    INNER JOIN ss_permisos_modulos perm ON perm.iid_empleado = users.iid_empleado
                    INNER JOIN no_personal personal ON personal.iid_empleado = users.iid_empleado AND personal.iid_empleado = perm.iid_empleado
                    INNER JOIN rh_personal_contrato contrato ON users.iid_empleado=contrato.iid_empleado and personal.iid_contrato = contrato.iid_rcontrato
                    INNER JOIN plaza p on contrato.iid_plaza=p.iid_plaza
          WHERE     users.vid_usuario = '".$userName."' AND
                    personal.s_status = 1
          GROUP BY  (perm.iid_empleado, users.v_nombre, contrato.iid_depto, contrato.iid_plaza, P.V_RAZON_SOCIAL)";


    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_BOTH);

    if ($row["CUENTA"] > 0) {
      $_SESSION['usuario'] 	= $userName;
    	$_SESSION['password'] 	= $userPassword;
    	$_SESSION['inicia'] 	= time();
    	$_SESSION['expira'] 	= $_SESSION['inicia'] + (30*120); //30*60
    	$_SESSION['mesFactura'] = (date("m")-1);
    	$_SESSION['nombre_user'] 	= $row["NOMBRE_USER"];
    	$_SESSION['iid_empleado'] 	= $row["IID_EMPELADO"];
    	$_SESSION['n_modulos'] 	= $row["CUENTA"];
      $_SESSION['area'] 	= $row["IID_DEPTO"];
      $_SESSION['i_plaza'] 	= $row["IID_PLAZA"];
      $_SESSION['n_plaza'] 	= $row["PLAZA"];
      $_SESSION['mostrar'] = "0";
    	echo "success";
    }else{
    	echo "error"." CONSULTA" .$sql;
    }
}elseif ($pcode == 3) {
  echo "error3";
}elseif ($pcode == -1) {
  echo "errornegativo".$userPassword." ".$_POST['formpassword'];
}

?>
