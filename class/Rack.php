<?php
/**
* © Argo Almacenadora ®
* Fecha: 20/07/2018
* Developer: Jorge Tejeda J.
* Proyecto: Rack WMS
* Version --
*/
include_once "../libs/conOra.php";

class Rack
{

	/* ================== INICIA FUNCION PARA SELECT OPTION PLAZA ================== */
	public function selectPlaza()
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT p.iid_plaza, p.v_razon_social, p.v_siglas FROM plaza p WHERE p.i_empresa_padre = 1 ORDER BY p.v_razon_social";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION PARA SELECT OPTION PLAZA ================== */


	/* ================== INICIA FUNCION PARA SELECT OPTION ALMACEN ================== */
	public function selectAlmacen($id_plaza)
	{
		$conn = conexion::conectar();
		$res_array = array();

		//$sql = "SELECT a.iid_almacen, a.iid_plaza, a.v_nombre, a.v_iniciales FROM almacen a WHERE a.iid_plaza =  $id_plaza AND a.s_status = 1 ORDER BY a.v_nombre";

		$sql = "SELECT  distinct t.iid_almacen as iid_almacen, a.iid_plaza, a.v_nombre, a.v_iniciales
		from op_in_clientes_wms t, almacen a
		where t.iid_plaza = $id_plaza and t.iid_almacen = a.iid_almacen";

		//$sql = "SELECT a.iid_almacen, a.iid_plaza, a.v_nombre, a.v_iniciales FROM almacen a WHERE a.iid_plaza =  $id_plaza AND a.s_status = 1 ORDER BY a.v_nombre";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION PARA SELECT OPTION ALMACEN ================== */


	/* ================== INICIA FUNCION PARA SELECT OPTION CLIENTE ================== */
	public function selectCliente($id_plaza,$id_almacen)
	{
		$conn = conexion::conectar();
		$res_array = array();
		$datalink = "";

		//$sql = "SELECT DISTINCT a.iid_num_cliente, d.v_razon_social, d.v_nombre_corto  FROM co_convenio_contrato a  INNER JOIN co_convenio_contrato_anexos b ON b.nid_folio = a.nid_folio AND b.s_status = 1 INNER JOIN co_convenio_contrato_almacen c ON c.nid_folio = a.nid_folio AND c.s_tipo = a.s_tipo INNER JOIN cliente d ON d.iid_num_cliente = a.iid_num_cliente WHERE a.s_status IN (2,3) AND  c.iid_almacen = $id_almacen AND d.iid_num_cliente IN (2905,4078,941,2580,3902,3121,2960,4004,2027,3727,3465,3748,3002,3598,4099,500) ";

		switch ($id_plaza) {
			case '4':
				$datalink = " ";
				break;
			case '17':
				$datalink = " ";
				break;
			default:
				$datalink = "";
				break;
		}

		$sql = "SELECT  distinct t.iid_num_cliente as iid_num_cliente, c.v_razon_social, c.v_nombre_corto
							from op_in_clientes_wms t, op_in_recibo_deposito r, cliente c
							where t.iid_num_cliente = r.iid_num_cliente
									and c.iid_num_cliente = t.iid_num_cliente
									and  r.i_sal_cero = 1 and t.iid_plaza = $id_plaza  and t.iid_almacen = $id_almacen
							union
							select distinct t.iid_num_cliente as iid_num_cliente, c.v_razon_social, c.v_nombre_corto
							from op_in_clientes_wms".$datalink." t, op_in_recibo_deposito".$datalink." r, cliente c
							where t.iid_num_cliente = r.iid_num_cliente
									and c.iid_num_cliente = t.iid_num_cliente
									and  r.i_sal_cero = 1 and t.iid_plaza = $id_plaza  and t.iid_almacen = $id_almacen";

		/*$sql = "SELECT wms.iid_num_cliente  , cli.v_razon_social, cli.v_nombre_corto
						FROM op_in_clientes_wms wms
						INNER JOIN cliente cli ON cli.iid_num_cliente = wms.iid_num_cliente
						WHERE wms.iid_almacen = 1700
						UNION
						SELECT wms.iid_num_cliente  , cli.v_razon_social, cli.v_nombre_corto
						FROM op_in_clientes_wms".$datalink." wms
						INNER JOIN cliente cli ON cli.iid_num_cliente = wms.iid_num_cliente
						WHERE wms.iid_almacen = $id_almacen";*/


		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION PARA SELECT OPTION CLIENTE ================== */


	/* ================== INICIA FUNCION PARA SELECT OPTION RACK ================== */
	public function selectRack($id_almacen)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT DISTINCT SUBSTR(u.v_descripcion,1,1) AS bateria
						FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen
						AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
						AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
						AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
						--AND SUBSTR(u.v_descripcion,2,6) <> '000000'
						ORDER BY bateria";

#echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION PARA SELECT OPTION RACK ================== */

/* ===================INICIA FUNCION PARA SELECT OPCION PISO =======================*/
public function selectPiso($id_almacen)
{
	$conn = conexion::conectar();
	$res_array = array();

	$sql = "SELECT DISTINCT SUBSTR(u.v_descripcion,1,1) AS bateria
					FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen
					AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
					AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
					AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
					AND SUBSTR(u.v_descripcion,2,6) = '000000'
					ORDER BY bateria";

#echo $sql;
	$stid = oci_parse($conn, $sql);
	oci_execute($stid);

	while (($row = oci_fetch_assoc($stid)) != false)
	{
		$res_array[]= $row;
	}

	oci_free_statement($stid);
	oci_close($conn);

	return $res_array;
}

	/* ================== INICIA FUNCION SELECT RACK PROFUNDIDAD ================== */
	public function rackProfundidad($id_almacen,$letraRack)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($letraRack != "TODOS" ){
			$andRack = " AND SUBSTR(u.v_descripcion,1,1) = '".$letraRack."' ";
		}else{
			$andRack = "  ";
		}

		$sql = "SELECT  DISTINCT SUBSTR(u.v_descripcion,1,1) AS rack, SUBSTR(u.v_descripcion,8,2) AS profundidad
				,DECODE(SUBSTR(u.v_descripcion,1,1),'A','#009888','B','#00BCD9','C','#3C8DBC','D','#7F4FC9','E','#87C735','F','#00A5F9','G','#3E49BB','H','#FF9A00','I','#EC4C32','J','#5F7D8E','K','#FFCD00','L','#FF5500','M','#526EFF','N','#27AE61','O','#FF9C00','P','#3952B5','Q','#00948C','R','#6339B5','S','#EF2264','T','#01ACC0','U','#5E478D','#D2D6DE') AS color
				FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				".$andRack."
				AND SUBSTR(U.V_DESCRIPCION, 2, 6) <> '000000'
				ORDER BY rack ASC, profundidad DESC";

			#echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION SELECT RACK PROFUNDIDAD ================== */
	/* ================== INICIA FUNCION SELECT PISO PROFUNDIDAD ===================*/
	public function pisoProfundidad($id_almacen,$letraRack)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($letraRack != "TODOS" ){
			$andRack = " AND SUBSTR(u.v_descripcion,1,1) = '".$letraRack."' ";
		}else{
			$andRack = "  ";
		}

		$sql = "SELECT  DISTINCT SUBSTR(u.v_descripcion,1,1) AS rack, SUBSTR(u.v_descripcion,2,2) AS profundidad
				,DECODE(SUBSTR(u.v_descripcion,1,1),'A','#009888','B','#00BCD9','C','#3C8DBC','D','#7F4FC9','E','#87C735','F','#00A5F9','G','#3E49BB','H','#FF9A00','I','#EC4C32','J','#5F7D8E','K','#FFCD00','L','#FF5500','M','#526EFF','N','#27AE61','O','#FF9C00','P','#3952B5','Q','#00948C','R','#6339B5','S','#EF2264','T','#01ACC0','U','#5E478D','#D2D6DE') AS color
				FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				".$andRack."
				AND SUBSTR(U.V_DESCRIPCION, 2, 6) = '000000'
				ORDER BY rack ASC, profundidad DESC";

			#echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ===================TERMINA FUNCION SELECT PISO PROFUNDIDAD ===============*/
	/* ================== INICIA FUNCION SELECT RACK PROFUNDIDAD ================== */
	public function rackProfundidadN($id_almacen,$letraRack)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($letraRack != "TODOS" ){
			$andRack = " AND SUBSTR(u.v_descripcion,1,1) = '".$letraRack."' ";
		}else{
			$andRack = "  ";
		}

		$sql = "SELECT  DISTINCT SUBSTR(u.v_descripcion,1,1) AS rack, SUBSTR(u.v_descripcion,8,2) AS profundidad
				,DECODE(SUBSTR(u.v_descripcion,1,1),'A','#009888','B','#00BCD9','C','#3C8DBC','D','#7F4FC9','E','#87C735','F','#00A5F9','G','#3E49BB','H','#FF9A00','I','#EC4C32','J','#5F7D8E','K','#FFCD00','L','#FF5500','M','#526EFF','N','#27AE61','O','#FF9C00','P','#3952B5','Q','#00948C','R','#6339B5','S','#EF2264','T','#01ACC0','U','#5E478D','#D2D6DE') AS color
				,SYS.STRAGG( DISTINCT SUBSTR(u.v_descripcion,2,2)||',' ) AS columna
				FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				".$andRack."
				GROUP BY SUBSTR(u.v_descripcion,1,1), SUBSTR(u.v_descripcion,8,2)
				ORDER BY rack ASC, profundidad DESC";


		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION SELECT RACK PROFUNDIDAD ================== */


	/* ================== INICIA FUNCION SELECT RACK COLUMNA ================== */
	public function rackColumna($id_almacen,$letra,$profundidad)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT DISTINCT SUBSTR(u.v_descripcion,1,1) AS rack, SUBSTR(u.v_descripcion,8,2) AS profundidad, SUBSTR(u.v_descripcion,2,2) AS columna
				FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				AND SUBSTR(u.v_descripcion,1,1) = '".$letra."' AND SUBSTR(u.v_descripcion,8,2) = '".$profundidad."' ORDER BY columna";


		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION SELECT RACK COLUMNA ================== */
	/* ================== INICIA FUNCION SELECT PISO COLUMNAS ================== */
	public function pisoColumna($id_almacen,$letra,$profundidad)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT DISTINCT SUBSTR(u.v_descripcion,1,1) AS rack, SUBSTR(u.v_descripcion,2,2) AS profundidad, SUBSTR(u.v_descripcion,8,2) AS columna
				FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				AND SUBSTR(u.v_descripcion,1,1) = '".$letra."' AND SUBSTR(u.v_descripcion,2,2) = '".$profundidad."' ORDER BY columna";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION SELECT PISO COLUMNA ==================*/


	/* ================== INICIA FUNCION SELECT RACK NIVEL ================== */
	public function rackNivel($id_almacen,$letra,$profundidad,$columna)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT DISTINCT SUBSTR(u.v_descripcion,1,1) AS rack, SUBSTR(u.v_descripcion,8,2) AS profundidad, SUBSTR(u.v_descripcion,2,2) AS columna, SUBSTR(u.v_descripcion,4,2) AS nivel
				FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				AND SUBSTR(u.v_descripcion,1,1) = '".$letra."' AND SUBSTR(u.v_descripcion,8,2) = '".$profundidad."' AND SUBSTR(u.v_descripcion,2,2) = '".$columna."'
				ORDER BY NIVEL DESC";

		$sql = "SELECT DISTINCT SUBSTR(u.v_descripcion,1,1) AS rack, SUBSTR(u.v_descripcion,8,2) AS profundidad, SUBSTR(u.v_descripcion,2,2) AS columna, SUBSTR(u.v_descripcion,4,2) AS nivel, SYS.STRAGG( DISTINCT SUBSTR(u.v_descripcion,6,2)||',' ) AS posicion
				FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				AND SUBSTR(u.v_descripcion,1,1) = '".$letra."' AND SUBSTR(u.v_descripcion,8,2) = '".$profundidad."' AND SUBSTR(u.v_descripcion,2,2) = '".$columna."'
				GROUP BY SUBSTR(u.v_descripcion,1,1), SUBSTR(u.v_descripcion,8,2), SUBSTR(u.v_descripcion,2,2), SUBSTR(u.v_descripcion,4,2)
				ORDER BY NIVEL DESC";


		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION SELECT RACK NIVEL ================== */

	/* ================== INICA FUNCION PARA PISO NIVEL =====================*/
	public function pisoNivel($id_almacen,$letra,$profundidad,$columna)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT DISTINCT SUBSTR(u.v_descripcion, 1, 1) AS rack,
                SUBSTR(u.v_descripcion, 2, 2) AS profundidad,
                SUBSTR(u.v_descripcion, 8, 2) AS columna,
                SUBSTR(u.v_descripcion, 2, 2) AS nivel,
                SYS.STRAGG(DISTINCT SUBSTR(u.v_descripcion, 2, 2) || ',') AS posicion
				FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				AND SUBSTR(u.v_descripcion,1,1) = '".$letra."' AND SUBSTR(u.v_descripcion,2,2) = '".$profundidad."' AND SUBSTR(u.v_descripcion,8,2) = '".$columna."'
				ORDER BY NIVEL DESC";

		$sql = "SELECT DISTINCT SUBSTR(u.v_descripcion, 1, 1) AS rack,
                SUBSTR(u.v_descripcion, 2, 2) AS profundidad,
                SUBSTR(u.v_descripcion, 8, 2) AS columna,
                SUBSTR(u.v_descripcion, 2, 2) AS nivel,
                SYS.STRAGG(DISTINCT SUBSTR(u.v_descripcion, 2, 2) || ',') AS posicion
				FROM op_in_ubicaciones u WHERE u.iid_almacen = $id_almacen AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				AND SUBSTR(u.v_descripcion,1,1) = '".$letra."' AND SUBSTR(u.v_descripcion,2,2) = '".$profundidad."' AND SUBSTR(u.v_descripcion,8,2) = '".$columna."'
				GROUP BY SUBSTR(u.v_descripcion, 1, 1),
          SUBSTR(u.v_descripcion, 2, 2),
          SUBSTR(u.v_descripcion, 8, 2),
          SUBSTR(u.v_descripcion, 2, 2)
				ORDER BY NIVEL DESC";

		#echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================= TERMINA FUNCION PARA PISO NIVEL =================== */

	/* ================== INICIA FUNCION SELECT RACK POSICION ================== */
	public function rackPosicion($id_almacen,$letra,$profundidad,$columna,$nivel)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT DISTINCT SUBSTR(u.v_descripcion,1,1) AS rack, SUBSTR(u.v_descripcion,8,2) AS profundidad, SUBSTR(u.v_descripcion,2,2) AS columna, SUBSTR(u.v_descripcion,4,2) AS nivel, SUBSTR(u.v_descripcion,6,2) AS posicion
				,u.v_descripcion
				FROM op_in_ubicaciones u
				WHERE u.iid_almacen = $id_almacen AND u.s_area = '1' AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				AND SUBSTR(u.v_descripcion,1,1) = '".$letra."' AND SUBSTR(u.v_descripcion,8,2) = '".$profundidad."' AND SUBSTR(u.v_descripcion,2,2) = '".$columna."' AND SUBSTR(u.v_descripcion,4,2) = '".$nivel."'
				ORDER BY posicion";


		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION SELECT RACK POSICION ================== */

	/* ================== INICIA FUNCION SELECT RACK POSICION ================== */
	public function rackAll($id_almacen)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT DISTINCT SUBSTR(u.v_descripcion,1,1) AS rack, SUBSTR(u.v_descripcion,8,2) AS profundidad, SUBSTR(u.v_descripcion,2,2) AS columna, SUBSTR(u.v_descripcion,4,2) AS nivel, SUBSTR(u.v_descripcion,6,2) AS posicion
				,u.v_descripcion
				FROM op_in_ubicaciones u
				WHERE u.iid_almacen = $id_almacen AND u.s_area = '1' AND LENGTH( SUBSTR(u.v_descripcion,1) ) = 9
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,1,1), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'TEXTO'
				AND (CASE WHEN TRIM(TRANSLATE(SUBSTR(u.v_descripcion,2,8), '0123456789', ' ')) IS NULL THEN 'NUMERO' ELSE 'TEXTO' end) = 'NUMERO'
				AND SUBSTR(u.v_descripcion,1,1) = 'A'
				ORDER BY 1,3,4,5";


		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION SELECT RACK POSICION ================== */


	/* ================== INICIA FUNCION SELECT RACK POSICION ================== */
	public function rackDetPosicion($id_plaza,$id_almacen,$id_cliente,$mercancia,$rack,$profundidad)
	{
		$conn = conexion::conectar();

		$res_array = array();

		$curs = oci_new_cursor($conn);/**/

		$curs = oci_new_cursor($conn);

		//echo $mercancia. " ". $rack. "   ".$profundidad;
		$stid = oci_parse($conn, "begin PCK_DASHBOARD.ARRIBO_UBI_RACK(:id_plaza,:id_almacen,:fil_cliente,:fil_db,:rackBateria,:rackProfundidad,:cur_posicion); end;");
		oci_bind_by_name($stid, ':id_plaza', $id_plaza);
		oci_bind_by_name($stid, ':id_almacen', $id_almacen);
		oci_bind_by_name($stid, ':fil_cliente', $id_cliente);
		oci_bind_by_name($stid, ':fil_db', $mercancia);
		oci_bind_by_name($stid, ':rackBateria', $rack);
		oci_bind_by_name($stid, ':rackProfundidad', $profundidad);
		oci_bind_by_name($stid, ":cur_posicion", $curs, -1, OCI_B_CURSOR);
		oci_execute($stid);

		oci_execute($curs);  // Ejecutar el REF CURSOR como un ide de sentencia normal
		while (($row = oci_fetch_array($curs, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		    $res_array[]=$row;
		}

		oci_free_statement($stid);
		oci_free_statement($curs);
		#echo $curs;
		oci_close($conn);
		return $res_array;
	}
	/* ================== TERMINA FUNCION SELECT RACK POSICION ================== */

	/* ================== INICIA FUNCION SELECT RACK POSICION ================== */
	public function rackDetPosicionPiso($id_plaza,$id_almacen,$id_cliente,$mercancia,$rack,$profundidad)
	{
		$conn = conexion::conectar();

		$res_array = array();

		$curs = oci_new_cursor($conn);/**/

		$curs = oci_new_cursor($conn);

		//echo $mercancia. " ". $rack. "   ".$profundidad;
		$stid = oci_parse($conn, "begin PCK_DASHBOARD.ARRIBO_UBI_PISO(:id_plaza,:id_almacen,:fil_cliente,:fil_db,:rackBateria,:rackProfundidad,:cur_posicion); end;");
		oci_bind_by_name($stid, ':id_plaza', $id_plaza);
		oci_bind_by_name($stid, ':id_almacen', $id_almacen);
		oci_bind_by_name($stid, ':fil_cliente', $id_cliente);
		oci_bind_by_name($stid, ':fil_db', $mercancia);
		oci_bind_by_name($stid, ':rackBateria', $rack);
		oci_bind_by_name($stid, ':rackProfundidad', $profundidad);
		oci_bind_by_name($stid, ":cur_posicion", $curs, -1, OCI_B_CURSOR);
		oci_execute($stid);

		oci_execute($curs);  // Ejecutar el REF CURSOR como un ide de sentencia normal
		while (($row = oci_fetch_array($curs, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
				$res_array[]=$row;
		}

		oci_free_statement($stid);
		oci_free_statement($curs);
		#echo $curs;
		oci_close($conn);
		return $res_array;
	}
	/* ================== TERMINA FUNCION SELECT RACK POSICION ================== */


	/* ================== INICIA FUNCION DETALLE DE UBICACION ================== */
	public function rackDetalleUbica($ubicacion,$id_plaza,$fil_cliente,$id_almacen,$fil_db)
	{
		$conn = conexion::conectar();
		$res_array = array();


		$datalink = "";
		$datalinkP = "";
		$and_cliente = "";

		if ( $fil_cliente == "ALL" ){
			$and_cliente = "";
		}else{
			$and_cliente = " AND ubi.iid_num_cliente = ".$fil_cliente." ";
		}
		if ( $id_plaza == "4" ){
			$datalinkP = " ";
		}else if ( $id_plaza == "17" ){
			$datalinkP = " ";
		}

		if ( $fil_db == "ALL" ){
			$datalink = " ";
		}else if ($fil_db == "NACIONAL"){
				$datalink = " AND SAL.PEDIMENTO = 'NACIONAL'";
		}elseif ($fil_db == "FISCAL") {
				$datalink = " AND SAL.PEDIMENTO <> 'NACIONAL'";
		}

		/*
		$sql = " SELECT * FROM vista_arribo_detalle_ubi".$datalink." ubi WHERE ubi.v_ubicacion = '".$ubicacion."' AND ubi.iid_plaza = ".$id_plaza." AND ubi.iid_almacen = ".$id_almacen." ".$and_cliente." ";

		if ( $fil_db == "ALL" ){
			$sql = " SELECT * FROM vista_arribo_detalle_ubi ubi WHERE ubi.v_ubicacion = '".$ubicacion."' AND ubi.iid_plaza = ".$id_plaza." AND ubi.iid_almacen = ".$id_almacen." ".$and_cliente."  UNION SELECT * FROM vista_arribo_detalle_ubi".$datalinkP." ubi WHERE ubi.v_ubicacion = '".$ubicacion."' AND ubi.iid_plaza = ".$id_plaza." AND ubi.iid_almacen = ".$id_almacen." ".$and_cliente." ";
		}
		*/

		$sql = "SELECT  ubi.iid_num_cliente,
                ubi.v_cliente,
                ubi.v_cliente_corto,
                ubi.id_arribo,
                ubi.v_contenedor,
                ubi.vid_recibo,
                ubi.vid_certificado,
                ubi.vno_ped_imp,
                ubi.vid_num_parte,
                ubi.vid_num_parte_padre,
                ubi.v_descripcion,
                ubi.v_descripcion_generica,
                SUM(sal.ARRIBADO) AS ARRIBADO,
                SUM(sal.RETIRADO) AS RETIRADO,
                ubi.i_sal_cero,
                ubi.n_libre,
                ubi.v_ubicacion,
                sal.UME as nombre_ume,
								sal.v_lote_serie,
								sal.proyecto,
								sal.calidad,
								RTRIM(REGEXP_REPLACE(LISTAGG(MOVS.VID_FACTURA, '-') WITHIN
                                     GROUP(ORDER BY MOVS.VID_FACTURA),
                                     '([^-]*)(-'||chr(92)||'1)+($|-)',
                                     ''||chr(92)||'1' ||chr(92)|| '3'),
                      '-') VID_FACTURA
				FROM vista_arribo_detalle_ubi ubi
				INNER JOIN VISTA_WMS_INVENTARIO sal ON sal.recibo = ubi.vid_recibo AND sal.mercancia = ubi.vid_num_parte_padre AND sal.ubicacion = ubi.v_ubicacion
				INNER JOIN OP_IN_MOVIMIENTOS movs ON SAL.recibo = MOVS.VID_RECIBO and sal.mercancia = movs.vid_num_parte and movs.v_tipo_movto = 'ERD'
				WHERE ubi.v_ubicacion = '".$ubicacion."' AND ubi.iid_plaza = ".$id_plaza." AND ubi.iid_almacen = ".$id_almacen." ".$and_cliente." ".$datalink."
				GROUP BY   ubi.iid_num_cliente,
		                ubi.v_cliente,
		                ubi.v_cliente_corto,
		                ubi.id_arribo,
		                ubi.v_contenedor,
		                ubi.vid_recibo,
		                ubi.vid_certificado,
		                ubi.vno_ped_imp,
		                ubi.vid_num_parte,
		                ubi.vid_num_parte_padre,
		                ubi.v_descripcion,
		                ubi.v_descripcion_generica,
		                ubi.i_sal_cero,
		                ubi.n_libre,
		                ubi.v_ubicacion,
		                sal.UME ,
										sal.v_lote_serie,
										sal.proyecto,
										sal.calidad,
										MOVS.VID_FACTURA";

		if ( $fil_db == "ALL" ){
			$sql = "SELECT ubi.iid_num_cliente,
                ubi.v_cliente,
                ubi.v_cliente_corto,
                ubi.id_arribo,
                ubi.v_contenedor,
                ubi.vid_recibo,
                ubi.vid_certificado,
                ubi.vno_ped_imp,
                ubi.vid_num_parte,
                ubi.vid_num_parte_padre,
                ubi.v_descripcion,
                ubi.v_descripcion_generica,
								SUM(sal.ARRIBADO ) AS ARRIBADO,
                SUM(sal.retirado) AS RETIRADO,
                ubi.i_sal_cero,
                ubi.n_libre,
                ubi.v_ubicacion,
                sal.UME as nombre_ume,
								sal.v_lote_serie,
								sal.proyecto,
								sal.calidad,
								RTRIM(REGEXP_REPLACE(LISTAGG(MOVS.VID_FACTURA, '-') WITHIN
                                     GROUP(ORDER BY MOVS.VID_FACTURA),
                                     '([^-]*)(-'||chr(92)||'1)+($|-)',
                                     ''||chr(92)||'1' ||chr(92)|| '3'),
                      '-') VID_FACTURA
			        FROM vista_arribo_detalle_ubi ubi
							INNER JOIN VISTA_WMS_INVENTARIO sal ON sal.recibo = ubi.vid_recibo AND sal.mercancia = ubi.vid_num_parte_padre AND sal.ubicacion = ubi.v_ubicacion
							INNER JOIN OP_IN_MOVIMIENTOS movs ON SAL.recibo = MOVS.VID_RECIBO and sal.mercancia = movs.vid_num_parte and movs.v_tipo_movto = 'ERD'
				    WHERE ubi.v_ubicacion = '".$ubicacion."' AND ubi.iid_plaza = ".$id_plaza." AND ubi.iid_almacen = ".$id_almacen." ".$and_cliente." ".$datalink."
						GROUP BY  ubi.iid_num_cliente,
			                ubi.v_cliente,
			                ubi.v_cliente_corto,
			                ubi.id_arribo,
			                ubi.v_contenedor,
			                ubi.vid_recibo,
			                ubi.vid_certificado,
			                ubi.vno_ped_imp,
			                ubi.vid_num_parte,
			                ubi.vid_num_parte_padre,
			                ubi.v_descripcion,
			                ubi.v_descripcion_generica,
			                ubi.i_sal_cero,
			                ubi.n_libre,
			                ubi.v_ubicacion,
			                sal.UME ,
											sal.v_lote_serie,
											sal.proyecto,
											sal.calidad,
											MOVS.VID_FACTURA";
		}

	#	echo $sql;

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		#echo $sql;
		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION DETALLE DE UBICACION ================== */



	/* ================== INICIA FUNCION MERCANCIA NO UBICADA ================== */
	public function mercanciaNoUbi($id_plaza,$id_almacen,$fil_cliente,$fil_db)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$datalink = "";
		$datalinkP = "";
		$and_cliente = "";

		/*DEFINE DATALINK*/
		if ( $id_plaza == "4" ){
			$datalinkP = " ";
		}else if ( $id_plaza == "17" ){
			$datalinkP = " ";
		}

		/*DEFINE CLIENTE*/
		if ( $fil_cliente == "ALL" ){
			$and_cliente = "";
		}else{
			$and_cliente = " AND SAL.iid_num_cliente = ".$fil_cliente." ";
		}

		/*DEFINE MERCANCIA*/
		if ( $fil_db == "ALL" ){
			$datalink = " ";
		}else if ($fil_db == "NACIONAL"){
				$datalink = " AND SAL.PEDIMENTO = 'NACIONAL'";
		}elseif ($fil_db == "FISCAL") {
				$datalink = " AND SAL.PEDIMENTO <> 'NACIONAL'";
		}

		$sql = "SELECT SAL.ARRIBO AS ID_ARRIBO ,
								sys.stragg(distinct v1.iid_num_cliente||' '||v1.v_cliente||' '||v1.c_cliente_corto||' '||v1.vid_num_parte||' '||v1.vid_recibo ||' '||v1.vid_certificado||' '||v1.vno_ped_imp||' '||v1.v_contenedor||' '||v1.v_descripcion_generica||' '||v1.v_descripcion) AS v_detalle
								FROM vista_arribo_detalle_no_ubi v1
								RIGHT JOIN VISTA_WMS_INVENTARIO sal ON sal.recibo = v1.vid_recibo AND sal.mercancia = v1.vid_num_parte_padre AND sal.ubicacion = v1.v_ubicacion
								WHERE SAL.iid_almacen = ".$id_almacen.$and_cliente."
											AND (sal.ARRIBADO- SAL.RETIRADO)> 0
											$datalink
								GROUP BY SAL.Arribo ";
//AS  ID_ARRIBO
		if ( $fil_db == "ALL" ){
			$sql = "SELECT SAL.arribo AS ID_ARRIBO,
											sys.stragg(distinct v1.iid_num_cliente||' '||v1.v_cliente||' '||v1.c_cliente_corto||' '||v1.vid_num_parte||' '||v1.vid_recibo ||' '||v1.vid_certificado||' '||v1.vno_ped_imp||' '||v1.v_contenedor||' '||v1.v_descripcion_generica||' '||v1.v_descripcion) AS v_detalle
											FROM vista_arribo_detalle_no_ubi v1
											RIGHT JOIN VISTA_WMS_INVENTARIO sal ON sal.recibo = v1.vid_recibo AND sal.mercancia = v1.vid_num_parte_padre AND sal.ubicacion = v1.v_ubicacion
											WHERE SAL.iid_almacen = ".$id_almacen.$and_cliente."
														AND (sal.ARRIBADO - SAL.RETIRADO) > 0
														$datalink
											GROUP BY SAL.Arribo ";
		}

		#echo $sql;

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION MERCANCIA NO UBICADA ================== */


	/* ================== INICIA FUNCION MERCANCIA NO UBICADA ================== */
	public function detMercanciaNoUbi($arribo, $id_plaza, $fil_cliente, $id_almacen, $fil_db)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$datalink = "";
		$datalinkP = "";
		$and_cliente = "";

		/*DEFINE DATALINK*/
		if ( $id_plaza == "4" ){
			$datalinkP = " ";
		}else if ( $id_plaza == "17" ){
			$datalinkP = " ";
		}

		/*DEFINE CLIENTE*/
		if ( $fil_cliente == "ALL" ){
			$and_cliente = "";
		}else{
			$and_cliente = " AND v.iid_num_cliente = ".$fil_cliente." ";
		}

		/*DEFINE MERCANCIA*/
		if ( $fil_db == "ALL" ){
			$datalink = " ";
		}else if ($fil_db == "NACIONAL"){
				$datalink = " AND SAL.PEDIMENTO = 'NACIONAL'";
		}elseif ($fil_db == "FISCAL") {
				$datalink = " AND v.PEDIMENTO <> 'NACIONAL'";
		}

	 $sql = "SELECT  v.iid_num_cliente,
						                C.V_RAZON_SOCIAL as v_cliente,
						                C.V_NOMBRE_CORTO as c_cliente_corto,
						                v.arribo as id_arribo,
						                E.V_CONTENEDOR,
						                v.RECIBO as vid_recibo,
						                v.certificado as vid_certificado,
						                v.PEDIMENTO as vno_ped_imp,
						                v.mercancia as vid_num_parte,
						                v.v_descripcion as v_descripcion,
						                R.V_DESCRIPCION_GENERICA as v_descripcion_generica,
						                sum(V.ARRIBADO) as ARRIBADO,
						                SUM(V.retirado) AS RETIRADO,
						                R.I_SAL_CERO,
						                v.ubicacion as v_ubicacion,
						                V.ume,
						                V.v_lote_serie,
						                V.proyecto,
						                V.calidad,
						                v.vid_factura
						FROM VISTA_WMS_INVENTARIO V,
						     OP_IN_RECIBO_DEPOSITO R,
						     CLIENTE C,
						     OP_IN_ARRIBOS_NAD_ENC E
								WHERE V.iid_num_cliente = C.IID_NUM_CLIENTE
           						AND V.arribo = E.IID_ARRIBO
											AND V.recibo = R.VID_RECIBO AND
								v.arribo = ".$arribo.$and_cliente." AND (v.arribado - v.retirado) > 0 $datalink
								GROUP BY v.iid_num_cliente,
						 						                C.V_RAZON_SOCIAL ,
						 						                C.V_NOMBRE_CORTO ,
						 						                v.arribo ,
						 						                E.V_CONTENEDOR,
						 						                v.RECIBO ,
						 						                v.certificado ,
						 						                v.PEDIMENTO ,
						 						                v.mercancia ,
						 						                v.v_descripcion ,
						 						                R.V_DESCRIPCION_GENERICA ,
						 						                R.I_SAL_CERO,
						 						                v.ubicacion ,
						 						                V.ume,
						 						                V.v_lote_serie,
						 						                V.proyecto,
						 						                V.calidad,
						 						                v.vid_factura";

		if ( $fil_db == "ALL" ){
							$sql = "SELECT v.iid_num_cliente,
					 						                C.V_RAZON_SOCIAL as v_cliente,
					 						                C.V_NOMBRE_CORTO as c_cliente_corto,
					 						                v.arribo as id_arribo,
					 						                E.V_CONTENEDOR,
					 						                v.RECIBO as vid_recibo,
					 						                v.certificado as vid_certificado,
					 						                v.PEDIMENTO as vno_ped_imp,
					 						                v.mercancia as vid_num_parte,
					 						                v.v_descripcion as v_descripcion,
					 						                R.V_DESCRIPCION_GENERICA as v_descripcion_generica,
					 						                SUM(V.ARRIBADO) AS ARRIBADO,
					 						                SUM(V.retirado) AS RETIRADO,
					 						                R.I_SAL_CERO,
					 						                v.ubicacion as v_ubicacion,
					 						                V.ume,
					 						                V.v_lote_serie,
					 						                V.proyecto,
					 						                V.calidad,
					 						                v.vid_factura
					 						FROM VISTA_WMS_INVENTARIO V,
					 						     OP_IN_RECIBO_DEPOSITO R,
					 						     CLIENTE C,
					 						     OP_IN_ARRIBOS_NAD_ENC E
					 								WHERE V.iid_num_cliente = C.IID_NUM_CLIENTE
					            						AND V.arribo = E.IID_ARRIBO
																	AND V.recibo = R.VID_RECIBO  AND
					 								v.arribo = ".$arribo.$and_cliente." AND (v.arribado - v.retirado) > 0 $datalink
													GROUP BY v.iid_num_cliente,
											 						                C.V_RAZON_SOCIAL ,
											 						                C.V_NOMBRE_CORTO ,
											 						                v.arribo ,
											 						                E.V_CONTENEDOR,
											 						                v.RECIBO ,
											 						                v.certificado ,
											 						                v.PEDIMENTO ,
											 						                v.mercancia ,
											 						                v.v_descripcion ,
											 						                R.V_DESCRIPCION_GENERICA ,
											 						                R.I_SAL_CERO,
											 						                v.ubicacion ,
											 						                V.ume,
											 						                V.v_lote_serie,
											 						                V.proyecto,
											 						                V.calidad,
											 						                v.vid_factura";
		}

	 #echo $sql;


		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	/* ================== TERMINA FUNCION MERCANCIA NO UBICADA ================== */


}
