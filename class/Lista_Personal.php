<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class ListaPersonal
{
	/*######################## TABLA DE EMPLEADOS #########################*/
	public function empleados_Detalle($iid_empleado, $tipo)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha = "";
		$and_fecha2 = "";

		$and_plaza = "";
		if($plaza != 'ALL'){
			$and_plaza = " AND REPLACE(pla.v_razon_social, ' (ARGO)') = '".$plaza."' ";
		}



		$sql = "SELECT per.iid_empleado,
       per.iid_plaza,
       REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza,
       pla.v_siglas,
       per.v_nombre || ' ' || per.v_ape_pat || ' ' || per.v_ape_mat AS nombre,
       per.v_ubi_1 AS lugar_trabajo,
       per.v_sexo,
       per.v_imss,
       per.v_rfc,
       per.v_curp,
			 per.v_estado,
       per.v_ciudad,
			 per.v_cp,
       per.v_domicilio,
			 per.V_NUMERO,
			 per.V_DEPTO,
			 per.V_COLONIA,
			 per.V_ENTRE_CALLES,
			 per.v_telefono1,
			 per.v_telefono2,
       per.v_email,
       per.n_edad,
       per.s_status,
       per.iid_contrato,
       to_char(per.d_fecha_ingreso, 'dd/mm/yyyy') d_fecha_ingreso,
       per.nid_solicitud,
       per.c_salario_mensual,
       per.i_antiguedad,
       con.iid_puesto,
       pue.v_descripcion AS puesto,
       con.iid_depto,
       dep.v_descripcion AS depto,
       con.s_tipo_contrato,
       con.d_fec_inicio,
       con.d_fec_final,
       con.iid_area,
       ar.v_descripcion AS area,
       RHESC.V_DESCRIPCION as nivel_Esc,
       PERAD.N_NUM_HIJOS,
       PERAD.V_PASATIEMPO,
       PERAD.V_TIPO_TRANSPORTE,
       PERAD.V_TIEMPO_CASA_TRABAJO,
       per.v_edo_civil,
			 max(rdes.s_ano),
       max(rdes.s_mes),
       rdes.c_calificacion,
			 esc.v_nombre_escuela,
       RSOLICITUD.LONGITUD,
       RSOLICITUD.LATITUD,
			 RCAN.OBSERVACION_DESPIDO,
			 TO_CHAR(RCAN.FECHA_CANCELACION, 'DD/MM/YYYY') FECHA_CANCELACION
				  FROM no_personal per
				 INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
				                           AND con.iid_contrato = per.iid_contrato
				 LEFT OUTER JOIN plaza pla ON pla.iid_plaza = per.iid_plaza
				 LEFT OUTER JOIN rh_puestos pue ON pue.iid_puesto = con.iid_puesto
				 LEFT OUTER JOIN rh_cat_depto dep ON dep.iid_depto = con.iid_depto
				 LEFT OUTER JOIN rh_cat_areas ar ON ar.iid_area = con.iid_area
				                           AND ar.iid_depto = con.iid_depto
				 LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO =
				                                                  CON.IID_CONTRATO
				                                              AND RCAN.IID_EMPLEADO =
				                                                  CON.IID_EMPLEADO
				 LEFT OUTER JOIN NO_PERSONAL_ESCOLARIDAD ESC ON ESC.IID_EMPLEADO = PER.IID_EMPLEADO
				 LEFT OUTER JOIN RH_ESCOLARIDAD RHESC ON ESC.I_NIVEL_ACADEMICO = RHESC.IID_ESCOLARIDAD
				 LEFT OUTER JOIN NO_PERSONAL_ADICIONAL PERAD ON PER.IID_EMPLEADO = PERAD.IID_EMPLEADO
				 LEFT OUTER JOIN RH_DESEMPENO RDES ON RDES.IID_EMPLEADO = PER.IID_EMPLEADO AND RDES.S_ANO = (SELECT MAX(P.s_ano) FROM RH_DESEMPENO P WHERE P.IID_EMPLEADO = $iid_empleado)
				 																																					 AND RDES.S_MES = (SELECT MAX(P.S_MES) FROM RH_DESEMPENO P WHERE P.IID_EMPLEADO = $iid_empleado)
				 INNER JOIN RH_SOLICITUD RSOLICITUD ON RSOLICITUD.NID_SOLICITUD = PER.NID_SOLICITUD
				 WHERE  per.iid_empleado = ".$iid_empleado."
					 GROUP BY per.iid_empleado, per.iid_plaza,  pla.v_razon_social, pla.v_siglas, per.v_nombre, per.v_ape_pat,  per.v_ape_mat, per.v_ubi_1, per.v_sexo, per.v_imss,
	        per.v_rfc, per.v_curp, per.v_estado, per.v_ciudad, per.v_cp, per.v_domicilio,per.V_NUMERO,
	 			  per.V_DEPTO,
	 			  per.V_COLONIA,
	 			 	per.V_ENTRE_CALLES, per.v_telefono1, per.v_email, per.n_edad, per.s_status, per.iid_contrato,
	        per.d_fecha_ingreso,  per.nid_solicitud, per.c_salario_mensual, per.i_antiguedad, con.iid_puesto, pue.v_descripcion, con.iid_depto, dep.v_descripcion,
	        con.s_tipo_contrato, con.d_fec_inicio, con.d_fec_final, con.iid_area, ar.v_descripcion, RHESC.V_DESCRIPCION, PERAD.N_NUM_HIJOS, PERAD.V_PASATIEMPO, PERAD.V_TIPO_TRANSPORTE,
	        PERAD.V_TIEMPO_CASA_TRABAJO, per.v_edo_civil, rdes.c_calificacion, per.v_telefono2, esc.v_nombre_escuela,
          RSOLICITUD.LONGITUD,
          RSOLICITUD.LATITUD,
					RCAN.OBSERVACION_DESPIDO,
					RCAN.FECHA_CANCELACION";
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

	public function empleados_Detalle_Ref($iid_empleado, $tipo)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha = "";
		$and_fecha2 = "";

		$and_plaza = "";
		if($plaza != 'ALL'){
			$and_plaza = " AND REPLACE(pla.v_razon_social, ' (ARGO)') = '".$plaza."' ";
		}

		if ($tipo == 1 ) {
			$S_TIPO = " (R_REF.TIPO_REFERENCIA = 1 OR R_REF.TIPO_REFERENCIA IS NULL) ";
		}elseif ($tipo == 2) {
			$S_TIPO = " R_REF.TIPO_REFERENCIA = 2 ";
		}


		$sql = "SELECT R_REF.v_Nombre, R_REF.V_DOMICILIO, R_REF.V_TELEFONO, R_REF.V_OCUPACION FROM RH_SOLICITUD_REFERENCIA R_REF
               INNER JOIN NO_PERSONAL NP ON R_REF.NID_SOLICITUD = NP.NID_SOLICITUD
               WHERE $S_TIPO AND NP.IID_EMPLEADO = $iid_empleado";

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

	/*++++++++++++++++++++++++ SQL TABLA DETALLE ACTIVOS ++++++++++++++++++++++++*/
	public function tablaActivos($plaza)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha = "";
		$and_fecha2 = "";

		$and_plaza = "";
		if($plaza != 'ALL'){
			$and_plaza = " AND REPLACE(pla.v_razon_social, ' (ARGO)') = '".$plaza."' ";
		}

		$sql = "SELECT per.iid_empleado, per.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas, per.v_nombre||' '||per.v_ape_pat||' '||per.v_ape_mat AS nombre, per.v_ubi_1 AS lugar_trabajo
				,per.v_sexo,per.v_imss, per.v_rfc, per.v_curp, per.n_edad, per.s_status, per.iid_contrato, to_char(per.d_fecha_ingreso, 'dd/mm/yyyy') d_fecha_ingreso, per.nid_solicitud, per.c_salario_mensual, per.i_antiguedad
				,con.iid_puesto, pue.v_descripcion AS puesto, con.iid_depto, dep.v_descripcion AS depto, con.s_tipo_contrato, con.d_fec_inicio, con.d_fec_final, con.iid_area, ar.v_descripcion AS area
				FROM no_personal per
				INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
				INNER JOIN plaza pla ON pla.iid_plaza = per.iid_plaza
				INNER JOIN rh_puestos pue ON pue.iid_puesto = con.iid_puesto
				INNER JOIN rh_cat_depto dep ON dep.iid_depto = con.iid_depto
				LEFT JOIN rh_cat_areas ar ON ar.iid_area = con.iid_area AND ar.iid_depto = con.iid_depto
				LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
                                                 AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO ".$and_fecha2."
				WHERE RCAN.FECHA_CANCELACION IS NULL ".$and_plaza." AND per.iid_empleado not in(209, 1, 2400)";
			#echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		if ($plaza == 'CÓRDOBA') {
			#echo $sql;
		}

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}



	public function tablaBajas($plaza){
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha = "";
		$and_fecha2 = "";

		$and_plaza = "";
		if($plaza != 'ALL'){
			$and_plaza = " AND REPLACE(pla.v_razon_social, ' (ARGO)') = '".$plaza."' ";
		}

		$sql = "SELECT per.iid_empleado, per.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas, per.v_nombre||' '||per.v_ape_pat||' '||per.v_ape_mat AS nombre, per.v_ubi_1 AS lugar_trabajo
				,per.v_sexo,per.v_imss, per.v_rfc, per.v_curp, per.n_edad, per.s_status, per.iid_contrato, to_char(per.d_fecha_ingreso, 'dd/mm/yyyy') d_fecha_ingreso, per.nid_solicitud, per.c_salario_mensual, per.i_antiguedad
				,con.iid_puesto, pue.v_descripcion AS puesto, con.iid_depto, dep.v_descripcion AS depto, con.s_tipo_contrato, con.d_fec_inicio, con.d_fec_final, con.iid_area, ar.v_descripcion AS area
				FROM no_personal per
				INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
				INNER JOIN plaza pla ON pla.iid_plaza = per.iid_plaza
				INNER JOIN rh_puestos pue ON pue.iid_puesto = con.iid_puesto
				INNER JOIN rh_cat_depto dep ON dep.iid_depto = con.iid_depto
				LEFT JOIN rh_cat_areas ar ON ar.iid_area = con.iid_area AND ar.iid_depto = con.iid_depto
				LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
																								 AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO ".$and_fecha2."
				WHERE RCAN.FECHA_CANCELACION IS NOT NULL ".$and_plaza." AND per.iid_empleado not in(209, 1, 2400)";
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
}
