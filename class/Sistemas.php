<?php 
/**
* © Argo Almacenadora ®
* Fecha: 30/11/2016
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Sistemas
* Version --
*/
include_once '../libs/conOra.php';

class Sistemas
{
 

	function __construct()
	{
	$fila = array();
	$res_graf_pro_leer = array();
	$res_graf_pro_leer_admin = array();
	$res_pro_acti_leer = array();
	$res_pro_proc_leer = array();
	$res_pro_inic_leer = array();
	$res_pro_desf_leer = array();
	$res_tabla_tareas_leer = array();
	$res_tabla_actividades_leer = array();
	$res_desviaciones_tabla_leer = array();
	}
////////////////////////////////////////// MÉTODO DE PROYECTOS ACTIVOS ////////////////////////////////////////// 
	public function pro_acti_leer()
	{
		
		$conn = conexion::conectar();
 
		$sql = "SELECT COUNT(*) AS total_pro_acti FROM ss_proyecto p WHERE  p.iid_status <> 3 ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$this->res_pro_acti_leer[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_pro_acti_leer;

	}
////////////////////////////////////////// MÉTODO DE PROYECTOS EN PROCESO ////////////////////////////////////////// 
	public function pro_proc_leer()
	{
		
		$conn = conexion::conectar();
 
		$sql = "SELECT COUNT(*) AS total_pro_proc FROM ss_proyecto p WHERE p.iid_status = 2 OR p.iid_status = 4";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$this->res_pro_proc_leer[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_pro_proc_leer;

	}
////////////////////////////////////////// MÉTODO DE PROYECTOS POR INICIAR ////////////////////////////////////////// 
	public function pro_inic_leer()
	{
		
		$conn = conexion::conectar();
 
		$sql = "SELECT COUNT(*) AS total_pro_inic FROM ss_proyecto p WHERE p.iid_status = 1";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$this->res_pro_inic_leer[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_pro_inic_leer;

	}
////////////////////////////////////////// MÉTODO DE PROYECTOS DESFASADOS ////////////////////////////////////////// 
	public function pro_desf_leer()
	{
		
		$conn = conexion::conectar();
 
		$sql = "SELECT COUNT(*) AS total_pro_desf FROM ss_proyecto WHERE (d_fecha_fin < sysdate -1 AND iid_status <>3 AND iid_status <>1) OR iid_status = 4";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$this->res_pro_desf_leer[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_pro_desf_leer;

	}
////////////////////////////////////////// MÉTODO DE PROYECTOS TERMINADOS ////////////////////////////////////////// 
	public function pro_ter_leer()
	{
		
		$conn = conexion::conectar();
 
		$sql = "SELECT COUNT(*) AS total_pro_ter FROM ss_proyecto p WHERE p.iid_status = 3";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$this->res_pro_ter_leer[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_pro_ter_leer;

	}	
////////////////////////////////////////// MÉTODO PARA LAS GRAFICAS DE DONAS DEL PROYECTO ////////////////////////////////////////// 
	public function graf_pro_leer($valor_perfil,$iid_empleado,$status_proyecto)
	{
		
		$conn = conexion::conectar();

		switch ($status_proyecto) {
			case '1,2,4':
				$where_pro_admin = " p.iid_status IN (1,2,4) ";
				break;
			case '2,4':
				$where_pro_admin = " p.iid_status IN (2,4) ";
				break;
			case '1':
				$where_pro_admin = " p.iid_status IN (1) ";
				break;
			case '4':
				$where_pro_admin = " (d_fecha_fin < sysdate -1 AND p.iid_status <>3 AND p.iid_status <>1) OR p.iid_status = 4 ";
				break;
			case '3':
				$where_pro_admin = " p.iid_status IN (3) ";
				break;
			default:
				$where_pro_admin = " p.iid_status IN (2,4) ";
				break;
		}

		switch ($valor_perfil) {
			case 1:
				$sql = "SELECT p.iid_status AS status_pro, p.iid_proyecto AS id_proyecto, p.n_porcentaje AS porcentaje_pro, p.v_nombre AS nombre_pro, to_char(p.d_fecha_fin,'dd-mm-yyyy') AS fec_fin_pro, to_char(p.d_fecha_real,'dd-mm-yyyy') AS fec_fin_pro_real, p.iid_empleado_lider AS lider FROM ss_proyecto p WHERE ".$where_pro_admin." ORDER BY p.n_porcentaje DESC";
				break;
			case 2:
			case 3:
				$sql = "SELECT count(invo.iid_proyecto) AS t_act,  p.iid_status AS status_pro, p.iid_proyecto AS id_proyecto, p.n_porcentaje AS porcentaje_pro, 
					p.v_nombre AS nombre_pro, to_char(p.d_fecha_fin,'dd-mm-yyyy') AS fec_fin_pro, to_char(p.d_fecha_real,'dd-mm-yyyy') AS fec_fin_pro_real, 
					p.iid_empleado_solicita AS emp_solicita, p.iid_empleado_lider AS emp_lider
					FROM ss_proyecto p
					INNER JOIN ss_involucrados invo on invo.iid_proyecto = p.iid_proyecto AND invo.iid_empleado = ".$iid_empleado."
					WHERE ".$where_pro_admin."
					GROUP BY( p.iid_status, p.iid_proyecto, p.n_porcentaje, p.v_nombre, to_char(p.d_fecha_fin,'dd-mm-yyyy'), p.iid_empleado_solicita, p.iid_empleado_lider)
					ORDER BY p.n_Porcentaje DESC";
				break;
			default:
				$sql =  "";
				break;
		}
 
 
		
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$this->res_graf_pro_leer[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_graf_pro_leer;

	}
	//////////////////////////////////////// ADMINISTRADOR 
	public function graf_pro_leer_admin($status_proyecto)
	{
		
		$conn = conexion::conectar();

		switch ($status_proyecto) {
			case '1,2,4':
				$where_pro_admin = " p.iid_status IN (1,2,4) ";
				break;
			case '2,4':
				$where_pro_admin = " p.iid_status IN (2,4) ";
				break;
			case '1':
				$where_pro_admin = " p.iid_status IN (1) ";
				break;
			case '4':
				$where_pro_admin = " (p.d_fecha_fin < sysdate -1 AND p.iid_status <>3 AND p.iid_status <>1) OR p.iid_status = 4 ";
				break;
			case '3':
				$where_pro_admin = " p.iid_status IN (3) ";
				break;
			default:
				$where_pro_admin = " p.iid_status IN (2,4) ";
				break;
		}
 
		$sql = "SELECT p.iid_status AS status_pro, p.iid_proyecto AS id_proyecto, p.n_porcentaje AS porcentaje_pro, p.v_nombre AS nombre_pro, to_char(p.d_fecha_fin,'dd-mm-yyyy') AS fec_fin_pro, p.iid_empleado_lider AS lider FROM ss_proyecto p WHERE ".$where_pro_admin." ORDER BY n_porcentaje DESC";
		 
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$this->res_graf_pro_leer_admin[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_graf_pro_leer_admin;

	}
////////////////////////////////////////// MÉTODO PARA DESCRIPCION DEL PROYECTO ////////////////////////////////////////// 
	public function desc_pro_leer($id_proyecto)
	{
		$conn = conexion::conectar();
 
		//$sql = "SELECT p.v_nombre AS desc_pro_nombre, p.v_descripcion AS desc_pro_des, p.v_alcance AS desc_pro_alcance, p.v_justificacion AS desc_pro_just, per.v_nombre AS desc_pro_per_nom, per.v_ape_pat AS desc_pro_per_pat, per.v_ape_mat AS desc_pro_per_mat FROM ss_proyecto p INNER JOIN no_personal per ON p.iid_empleado_solicita = per.iid_empleado WHERE p.iid_proyecto ='".$id_proyecto."' ";
		$sql = "SELECT p.v_nombre AS desc_pro_nombre, p.v_descripcion AS desc_pro_des, p.v_alcance AS desc_pro_alcance,
				p.v_justificacion AS desc_pro_just, per.v_nombre AS desc_pro_per_nom,
				per.v_ape_pat AS desc_pro_per_pat, per.v_ape_mat AS desc_pro_per_mat 
				,per_lider.v_nombre AS desc_pro_per_nom_lider, per_lider.v_ape_pat AS desc_pro_per_pat_lider, per_lider.v_ape_mat AS desc_pro_per_mat_lider
				FROM ss_proyecto p 
				INNER JOIN no_personal per ON p.iid_empleado_solicita = per.iid_empleado
				INNER JOIN no_personal per_lider ON p.iid_empleado_lider = per_lider.iid_empleado  WHERE p.iid_proyecto ='".$id_proyecto."' ";
		$stid = oci_parse($conn, $sql);
		$row = oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res = $row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return  $res;

	}

////////////////////////////////////////// MÉTODO PARA LA TABLA DE TAREAS DEL PROYECTO ////////////////////////////////////////// 
	public function tabla_tareas_leer($poyecto_id)
	{
		
		$conn = conexion::conectar();
		$sql = " SELECT  t.iid_tarea AS tarea_id, to_char(t.d_fecha_inicio,'dd-mm-yyyy') AS tarea_fec_ini, to_char(t.d_fecha_ini_real,'dd-mm-yyyy') AS tarea_fecha_ini_real, to_char(t.d_fecha_fin, 'dd-mm-yyyy') AS tarea_fecha_fin, to_char(t.d_fecha_real, 'dd-mm-yyyy') AS tarea_d_fecha_fin_real, t.v_nombre AS tarea_nombre, t.n_porcentaje AS tarea_porcentaje, s.v_tipo AS tarea_tipo, t.n_tarea_ant AS tarea_n_ant FROM ss_tareas t INNER JOIN ss_status s ON t.iid_status = s.iid_status WHERE t.iid_proyecto ='".$poyecto_id."' ORDER BY t.iid_tarea ASC ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		//while (($row = oci_fetch_array($stid, OCI_BOTH)) != false) 
		while (($row = oci_fetch_assoc($stid)) != false) 	
		{
			
				$this->res_tabla_tareas_leer[]=$row;
			
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_tareas_leer;

	}
////////////////////////////////////////// MÉTODO PARA LA TABLA DE ACTIVIDADES DEL PROYECTO ////////////////////////////////////////// 
	public function tabla_actividades_leer($id_proyecto,$id_tarea)
	{
		
		$conn = conexion::conectar();
		$sql = "SELECT t.v_nombre AS act_tarea_nom , a.iid_actividad AS act_iid, a.v_nombre AS act_nombre, per.v_nombre AS act_per_nom, a.d_fecha_inicio AS act_fecha_ini, a.d_fecha_ini_real AS act_fecha_ini_real, to_char(a.d_fecha_fin,'dd-mm-yyyy') AS act_fecha_fin, to_char(a.d_fecha_real,'dd-mm-yyyy') AS act_fecha_fin_real , a.iid_status AS act_id_status, per.v_ape_pat AS act_per_pat, per.v_ape_mat AS act_per_mat, a.b_minuta AS act_b_minuta FROM ss_actividades a INNER JOIN ss_tareas t ON a.iid_tarea = t.iid_tarea AND a.iid_proyecto = t.iid_proyecto INNER JOIN ss_responsables res ON a.iid_empleado_resp = res.iid_empleado_resp AND res.iid_proyecto = a.iid_proyecto INNER JOIN  no_personal per ON res.iid_empleado = per.iid_empleado WHERE a.iid_proyecto ='".$id_proyecto."' AND a.iid_tarea ='".$id_tarea."' ORDER BY iid_actividad ASC  ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		

		while (($row = oci_fetch_array($stid,OCI_BOTH+OCI_RETURN_LOBS)) !=false )//OCI_RETURN_LOBS PARA TIPO DE DATOS BLOB
		//while (($row = oci_fetch_assoc($stid)) != false) 	
		{
			
				$this->res_tabla_actividades_leer[]=$row;
				 unset($row); 
			
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_actividades_leer;

	}
////////////////////////////////////// TABLA DE DESVIACIONES ///////////////////////////////////////////////

	public function desviaciones_tabla_leer($id_proyecto, $id_tarea, $id_actividad)
	{
		$conn = conexion::conectar();

		//$sql = "SELECT d.iid_desviacion AS des_id, d.v_nombre AS des_nombre, d.v_razon AS des_razon, d.iid_actividad AS des_id_act, d.v_observaciones AS des_observaciones, per.v_nombre AS des_per_nom, per.v_ape_pat AS des_per_pat , per.v_ape_mat AS des_per_mat FROM ss_desviaciones d LEFT JOIN ss_responsables res ON res.iid_empleado_resp = d.iid_empleado_resp AND res.iid_proyecto = d.iid_proyecto LEFT JOIN no_personal per ON res.iid_empleado = per.iid_empleado WHERE d.iid_proyecto ='".$poyecto_id."' AND  d.iid_tarea ='".$tarea_id."' AND d.iid_actividad ='".$actividad_id."' ORDER BY iid_desviacion ASC";
		$sql = "SELECT d.iid_desviacion AS des_id, act.v_nombre AS des_act_nombre, 
				d.v_nombre AS des_nombre, d.v_razon AS des_razon, d.iid_actividad AS des_id_act, d.v_observaciones AS des_observaciones, 
				per.v_nombre AS des_per_nom, per.v_ape_pat AS des_per_pat , per.v_ape_mat AS des_per_mat 
				FROM ss_desviaciones d 
				LEFT JOIN ss_responsables res ON res.iid_empleado_resp = d.iid_empleado_resp AND res.iid_proyecto = d.iid_proyecto 
				LEFT JOIN no_personal per ON res.iid_empleado = per.iid_empleado
				LEFT JOIN ss_actividades act ON act.iid_actividad = d.iid_actividad AND d.iid_proyecto = act.iid_proyecto AND act.iid_tarea = d.iid_tarea WHERE d.iid_proyecto ='".$id_proyecto."' AND  d.iid_tarea ='".$id_tarea."' AND d.iid_actividad ='".$id_actividad."' ORDER BY iid_desviacion ASC";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false) 	
		{
			
				$this->res_desviaciones_tabla_leer[]=$row;
			
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_desviaciones_tabla_leer;

	}



} ?>