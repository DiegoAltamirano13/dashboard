<?php 
/**
* © Argo Almacenadora ®
* Fecha: 26/12/2016
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Notificaciones
* Version --
*/
include_once '../libs/conOra.php';

class Notificaciones
{
      
	function __construct()
	{
	$fila = array();
	}
	public function leer()
	{
		
		$conn = conexion::conectar();
 
		$sql = "SELECT  pro.v_nombre AS pro_nombre, tar.v_nombre AS tar_nombre, act.v_nombre AS act_nombre, 
res1.iid_empleado AS res1_empleado,
inv1.v_mail AS inv1_mail,
per1.v_nombre AS per1_nombre, per1.v_ape_pat AS per1_pat, per1.v_ape_mat AS per1_mat,
to_char(act.d_fecha_inicio, 'dd-mm-yyyy') AS act_fec_ini, act.d_fecha_ini_real AS act_fec_ini_real, to_char(act.d_fecha_fin, 'dd-mm-yyyy') AS act_fec_fin, act.d_fecha_real AS act_fec_fin_real,
des.v_nombre AS des_nombre, des.v_razon AS des_razon,
res2.iid_empleado AS inv_iid_emp, 
inv2.v_mail AS inv2_mail,
per2.v_nombre AS inv_nombre, per2.v_ape_pat AS inv_ape_pat, per2.v_ape_mat AS inv_ape_mat
FROM ss_actividades act
INNER JOIN ss_proyecto pro ON pro.iid_proyecto = act.iid_proyecto 
INNER JOIN ss_tareas tar ON tar.iid_proyecto = act.iid_proyecto and tar.iid_tarea = act.iid_tarea
INNER JOIN ss_desviaciones des ON des.iid_proyecto = act.iid_proyecto and des.iid_tarea = act.iid_tarea and des.iid_actividad = act.iid_actividad
INNER JOIN ss_responsables res1 ON act.iid_empleado_resp = res1.iid_empleado_resp and res1.iid_proyecto = act.iid_proyecto
LEFT JOIN ss_responsables res2 ON des.iid_empleado_resp = res2.iid_empleado_resp and res2.iid_proyecto = des.iid_proyecto
INNER JOIN ss_involucrados inv1 ON inv1.iid_empleado = res1.iid_empleado and inv1.iid_proyecto = des.iid_proyecto
LEFT JOIN ss_involucrados inv2 ON inv2.iid_empleado = res2.iid_empleado and inv2.iid_proyecto = act.iid_proyecto
INNER JOIN no_personal per1 ON per1.iid_empleado = res1.iid_empleado
LEFT JOIN no_personal per2 ON per2.iid_empleado = res2.iid_empleado";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$this->fila[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->fila;

	}
}
?>