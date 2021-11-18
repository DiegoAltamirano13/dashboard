<?php
/**
* © Argo Almacenadora ®
* Fecha: 19/01/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard (PERMISOS PARA USUARIOS)
* Version --
*/
include_once '../libs/conOra.php';

class Perfil
{

	 public $var = "llamando";

	function __construct()
	{
	$fila = array();
	$res ;
	$res_info_usuario = array();

	}
////////////////////////////////////////// MÉTODO DE PROYECTOS ACTIVOS
public function modulos_valida($iid_empleado, $iid_modulo)
{
	$conn = conexion::conectar();

		// $sql = "SELECT per_mod.iid_empleado AS empleado, per_mod.iid_perfil AS perfil, COUNT(*) AS permiso FROM ss_permisos_modulos per_mod LEFT JOIN se_usuarios us ON us.iid_empleado = per_mod.iid_empleado WHERE per_mod.iid_empleado = '".$iid_empleado."' AND per_mod.iid_modulo = '".$iid_modulo."' GROUP BY (per_mod.iid_empleado, per_mod.iid_perfil)";
		$sql = "SELECT COUNT(*) AS permiso
			FROM ss_permisos_modulos per_mod
			LEFT JOIN se_usuarios us ON us.iid_empleado = per_mod.iid_empleado
			WHERE per_mod.iid_empleado = '".$iid_empleado."' AND per_mod.iid_modulo IN (".$iid_modulo.")";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid );
		#echo $sql;
		$row = oci_fetch_array($stid, OCI_BOTH);
		oci_free_statement($stid);
		oci_close($conn);
		return $row["PERMISO"];

}
public function modulos_valida_old($iid_empleado, $iid_modulo)
{
	$conn = conexion::conectar();

	$sql = "SELECT COUNT(*) AS permiso
			FROM ss_permisos_modulos per_mod
			LEFT JOIN se_usuarios us ON us.iid_empleado = per_mod.iid_empleado
			WHERE per_mod.iid_empleado = '".$iid_empleado."' AND per_mod.iid_modulo IN (".$iid_modulo.")";

	$stid = oci_parse($conn, $sql);
	oci_execute($stid );

	$row = oci_fetch_array($stid, OCI_BOTH);
	oci_free_statement($stid);
	oci_close($conn);
	return $row["PERMISO"];

}
////////////////////////////////////////// MÉTODO DE PROYECTOS ACTIVOS
public function modulos_valida_op($iid_empleado, $iid_modulo)
{
	$conn = conexion::conectar();

		//$sql = "SELECT per_mod.iid_empleado, COUNT(*) AS permiso  FROM ss_permisos_modulos per_mod LEFT JOIN se_usuarios us ON us.iid_empleado = per_mod.iid_empleado WHERE per_mod.iid_empleado = '".$iid_empleado."' AND per_mod.iid_modulo = '".$iid_modulo."' GROUP BY (per_mod.iid_empleado)";
		$sql = "SELECT  per_mod.iid_perfil AS perfil  FROM ss_permisos_modulos per_mod LEFT JOIN se_usuarios us ON us.iid_empleado = per_mod.iid_empleado WHERE per_mod.iid_empleado = '".$iid_empleado."' and per_mod.iid_modulo = '".$iid_modulo."'";
		// $stid = oci_parse($conn, $sql);
		// oci_execute($stid );

		// $row = oci_fetch_array($stid, OCI_BOTH);
		// oci_free_statement($stid);
		// oci_close($conn);

		 //return $row["PERFIL"];
		 ///////////////////
		 $stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_BOTH);
		oci_free_statement($stid);
		oci_close($conn);
		if ($row["PERFIL"] > 0) {

			$_SESSION['valor_perfil'] 	= $row["PERFIL"];
			//$_SESSION['iid_empleado'] 	= $row["IID_EMPELADO"];



		}else{
			echo "error";
}

}

	/////////////////////////////////////////////////////////////OPCIONES
	public function info_permisos($iid_empleado)
	{

		$conn = conexion::conectar();
		$sql = " SELECT  per_inf.iid_empleado,
		per_inf.iid_modulo AS id_modulo, modu.v_nombre AS modulo,
		per_inf.iid_perfil AS id_perfil, per.v_nombre AS perfil
		FROM ss_permisos_modulos per_inf
		INNER JOIN ss_modulos modu ON modu.iid_modulo = per_inf.iid_modulo
		LEFT JOIN ss_perfiles per ON per.iid_perfil = per_inf.iid_perfil
		WHERE per_inf.iid_empleado = '".$iid_empleado."'" ;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		//while (($row = oci_fetch_array($stid, OCI_BOTH)) != false)
		while (($row = oci_fetch_assoc($stid)) != false)
		{

				$this->fila[]=$row;

		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->fila;

	}

/////////////////////////////////////////////////////////////INFORMACION DEL USUARIO
	public function info_usuario($iid_empleado)
	{

		$conn = conexion::conectar();
		$sql = " SELECT pers.iid_plaza AS id_plaza, pers.iid_empleado AS id_empleado, pers.v_nombre AS nombre, pers.v_ape_pat AS apellido_p, pers.v_ape_mat AS apellido_m
				,contra.iid_contrato AS id_contrato, depto.iid_depto AS id_departamento, depto.v_descripcion AS departamento, area.iid_area AS id_area, area.v_descripcion AS area
				FROM no_personal pers
				INNER JOIN no_contrato contra ON contra.iid_empleado = pers.iid_empleado
				INNER JOIN rh_cat_depto depto ON depto.iid_depto = contra.iid_depto
				INNER JOIN rh_cat_areas area ON area.iid_area = contra.iid_area AND area.iid_depto = depto.iid_depto
				WHERE contra.iid_empleado = ".$iid_empleado." " ;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		//while (($row = oci_fetch_array($stid, OCI_BOTH)) != false)
		while (($row = oci_fetch_assoc($stid)) != false)
		{

				$this->res_info_usuario[]=$row;

		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_info_usuario;

	}

	public function css_class_active($active,$url)
		{
		if ($active == $url){
			$status_active = "active";
			return $status_active;
		}
	}

	public function test($data)
	{

		$conn = conexion::conectar();
		$sql = $data;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		//while (($row = oci_fetch_array($stid, OCI_BOTH)) != false)
		while (($row = oci_fetch_assoc($stid)) != false)
		{

				$this->res_info_usuario[]=$row;

		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_info_usuario;

	}


} ?>
