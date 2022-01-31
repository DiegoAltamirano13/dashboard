<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class Mapas_Operaciones
{
	/*######################## TABLA DE EMPLEADOS #########################*/
	public function buquesDetalles()
	{
		$conn = conexion::conectar();
		$res_array = array();


		$sql = "SELECT S.IID_NUM_CERT_S AS IID_NUM_CDT,
				       T.V_NOM_BUQUE,
				       C.V_RAZON_SOCIAL,
				       T.V_MERCANCIA,
				       T.V_FACTURA,
				       T.N_TONELADAS,
							 T.N_VALOR,
							 T.V_TENEDOR,
				       TO_DATE(S.D_FECHA_EMISION, 'DD/MM/YYYY') AS FECHA_EMISION,
				       OP.V_DESCRIPCION,
				       SD.N_CANTIDAD,
							 SD.N_UNIT_VALOR_DEC,
							 SD.N_TOTAL_VALOR_DEC,
							 (SELECT sum(d.c_cantidad) AS CANTIDAD
                from op_in_origen_cd_n_s       t,
                     op_in_recibo_deposito     r,
                     op_in_recibo_deposito_det d,
                     almacen                   al,
                     almacen_areas             a
               where t.vid_recibo_destino = r.vid_recibo
                 and r.iid_almacen = al.iid_almacen
                 and d.vid_recibo = r.vid_recibo
                 and r.s_area = a.s_area
                 and a.iid_almacen = al.iid_almacen
                 and r.vid_certificado is not null
                 and t.vid_recibo_origen = s.v_id_recibo) AS TON_DESCARGADAS
				  FROM OP_IN_REGISTROS_TRANSITOS T,
				       AD_CE_CERT_S              S,
				       OP_IN_RECIBO_DEPOSITO     R,
				       CLIENTE                   C,
				       AD_CE_CERT_S_DET          SD,
				       OP_IN_PARTES              OP
				 WHERE T.IID_NUM_CDT(+) = S.IID_NUM_CERT_S
				   AND S.IID_NUM_CERT_S = SD.IID_NUM_CERT_S
				   AND SD.IID_MERCANCIA = OP.VID_NUM_PARTE
				   AND C.IID_NUM_CLIENTE = S.IID_CLIENTE
				   AND S.V_ID_RECIBO = R.VID_RECIBO
				   AND R.I_SAL_CERO = 1
				   AND S.IID_ALMACEN = 9999";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

	#	echo $sql;
		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}

	public function buquesDetallesHist()
	{
		$conn = conexion::conectar();
		$res_array = array();


		$sql = "SELECT S.IID_NUM_CERT_S AS IID_NUM_CDT,
				       T.V_NOM_BUQUE,
				       C.V_RAZON_SOCIAL,
				       T.V_MERCANCIA,
				       T.V_FACTURA,
				       T.N_TONELADAS,
							 T.N_VALOR,
							 T.V_TENEDOR,
				       TO_DATE(S.D_FECHA_EMISION, 'DD/MM/YYYY') AS FECHA_EMISION,
				       OP.V_DESCRIPCION,
				       SD.N_CANTIDAD,
							 SD.N_UNIT_VALOR_DEC,
							 SD.N_TOTAL_VALOR_DEC,
							 P.V_RAZON_SOCIAL AS PLAZA,
							 (SELECT sum(d.c_cantidad) AS CANTIDAD
                from op_in_origen_cd_n_s       t,
                     op_in_recibo_deposito     r,
                     op_in_recibo_deposito_det d,
                     almacen                   al,
                     almacen_areas             a
               where t.vid_recibo_destino = r.vid_recibo
                 and r.iid_almacen = al.iid_almacen
                 and d.vid_recibo = r.vid_recibo
                 and r.s_area = a.s_area
                 and a.iid_almacen = al.iid_almacen
                 and r.vid_certificado is not null
                 and t.vid_recibo_origen = s.v_id_recibo) AS TON_DESCARGADAS
				  FROM OP_IN_REGISTROS_TRANSITOS T,
				       AD_CE_CERT_S              S,
				       OP_IN_RECIBO_DEPOSITO     R,
				       CLIENTE                   C,
				       AD_CE_CERT_S_DET          SD,
				       OP_IN_PARTES              OP,
							 PLAZA                     P
				 WHERE T.IID_NUM_CDT = S.IID_NUM_CERT_S
				   AND S.IID_NUM_CERT_S = SD.IID_NUM_CERT_S
				   AND SD.IID_MERCANCIA = OP.VID_NUM_PARTE
				   AND C.IID_NUM_CLIENTE = S.IID_CLIENTE
					 AND T.N_DESTINO = P.IID_PLAZA
				   AND S.V_ID_RECIBO = R.VID_RECIBO
				   AND R.I_SAL_CERO = 0
				   AND S.IID_ALMACEN = 9999
					 AND T.V_NOM_BUQUE NOT LIKE '%(FFCC)%'";

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

	public function buquesDetallesHist2()
	{
		$conn = conexion::conectar();
		$res_array = array();


		$sql = "SELECT S.IID_NUM_CERT_S AS IID_NUM_CDT,
				       T.V_NOM_BUQUE,
				       C.V_RAZON_SOCIAL,
				       T.V_MERCANCIA,
				       T.V_FACTURA,
				       T.N_TONELADAS,
							 T.N_VALOR,
							 T.V_TENEDOR,
				       TO_DATE(S.D_FECHA_EMISION, 'DD/MM/YYYY') AS FECHA_EMISION,
				       OP.V_DESCRIPCION,
				       SD.N_CANTIDAD,
							 SD.N_UNIT_VALOR_DEC,
							 SD.N_TOTAL_VALOR_DEC,
							 P.V_RAZON_SOCIAL AS PLAZA,
							 (SELECT sum(d.c_cantidad) AS CANTIDAD
                from op_in_origen_cd_n_s       t,
                     op_in_recibo_deposito     r,
                     op_in_recibo_deposito_det d,
                     almacen                   al,
                     almacen_areas             a
               where t.vid_recibo_destino = r.vid_recibo
                 and r.iid_almacen = al.iid_almacen
                 and d.vid_recibo = r.vid_recibo
                 and r.s_area = a.s_area
                 and a.iid_almacen = al.iid_almacen
                 and r.vid_certificado is not null
                 and t.vid_recibo_origen = s.v_id_recibo) AS TON_DESCARGADAS
				  FROM OP_IN_REGISTROS_TRANSITOS T,
				       AD_CE_CERT_S              S,
				       OP_IN_RECIBO_DEPOSITO     R,
				       CLIENTE                   C,
				       AD_CE_CERT_S_DET          SD,
				       OP_IN_PARTES              OP,
							 PLAZA                     P
				 WHERE T.IID_NUM_CDT = S.IID_NUM_CERT_S
				   AND S.IID_NUM_CERT_S = SD.IID_NUM_CERT_S
				   AND SD.IID_MERCANCIA = OP.VID_NUM_PARTE
				   AND C.IID_NUM_CLIENTE = S.IID_CLIENTE
					 AND T.N_DESTINO = P.IID_PLAZA
				   AND S.V_ID_RECIBO = R.VID_RECIBO
				   AND R.I_SAL_CERO = 0
				   AND S.IID_ALMACEN = 9999
					 AND T.V_NOM_BUQUE LIKE '%(FFCC)%'";

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


	public function buquesDetallados($iid_cdt)
	{
		$conn = conexion::conectar();
		$res_array = array();


		$sql = "SELECT Y.IID_NUM_CDT,
							 S.IID_NUM_CERT_S,
				       Y.V_NOM_BUQUE,
				       Y.V_TENEDOR,
				       C.V_RAZON_SOCIAL AS CLIENTEVR,
				       Y.V_NOM_BUQUE,
				       Y.ARRIBO_ESTIMADO,
				       Y.FECHA_FONDEO,
				       Y.FECHA_ATRAQUE,
				       TO_DATE(S.d_Fecha_Emision, 'DD/MM/YYYY') AS FECHAEMISION,
				       Y.FECHA_TRASLADO,
				       Y.FECHA_ARRIBO,
				       Y.V_MERCANCIA,
							 OP.V_DESCRIPCION,
				       Y.V_FACTURA,
				       Y.N_VALOR,
				       CASE WHEN Y.N_DESTINO IS NULL THEN
				              ''
				       ELSE
				              (SELECT P.V_RAZON_SOCIAL FROM PLAZA P WHERE P.IID_PLAZA = Y.N_DESTINO )
				       END  AS N_DESTINO,
				       SD.N_CANTIDAD,
				       Y.FECHA_EMISION_CDT,
				       S.V_ID_RECIBO,
							 SD.N_UNIT_VALOR_DEC,
							 SD.N_TOTAL_VALOR_DEC
				  FROM OP_IN_REGISTROS_TRANSITOS Y,
				       AD_CE_CERT_S              S,
				       OP_IN_RECIBO_DEPOSITO     R,
				       CLIENTE                   C,
							 AD_CE_CERT_S_DET          SD,
							 OP_IN_PARTES 						 OP
				 WHERE Y.IID_NUM_CDT(+) = S.IID_NUM_CERT_S
				   AND C.IID_NUM_CLIENTE = S.IID_CLIENTE
					 AND S.IID_NUM_CERT_S = SD.IID_NUM_CERT_S
					 AND SD.IID_MERCANCIA = OP.VID_NUM_PARTE
				   AND S.V_ID_RECIBO = R.VID_RECIBO
				   AND R.I_SAL_CERO = 1
				   AND S.IID_ALMACEN = 9999
					    AND S.IID_NUM_CERT_S = $iid_cdt";
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


	public function tablaDetallados($iid_cdt)
	{
		$conn = conexion::conectar();
		$res_array = array();


		$sql = "SELECT r.d_plazo_dep_ini,
                               al.v_nombre,
                               a.v_descripcion,
                               r.vid_certificado,
                               sum(d.c_cantidad) AS CANTIDAD
						from op_in_origen_cd_n_s t,
						     op_in_recibo_deposito r,
						     op_in_recibo_deposito_det d,
						     almacen al,
						     almacen_areas a
						where t.vid_recibo_destino = r.vid_recibo and
						      r.iid_almacen = al.iid_almacen and
						      d.vid_recibo = r.vid_recibo and
						      r.s_area = a.s_area and
						      a.iid_almacen = al.iid_almacen and
						                r.vid_certificado is not null and
						      t.vid_recibo_origen = $iid_cdt
						group by r.d_plazo_dep_ini, al.v_nombre,a.v_descripcion, r.vid_certificado
						order by r.d_plazo_dep_ini";
	#	echo $sql;
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

}
?>
