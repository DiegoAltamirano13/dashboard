<?php
/**
* © Argo Almacenadora ®
* Fecha: 18/05/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Comercio Exterior
* Version --
*/
include_once '../libs/conOra.php';


class Cartascupo
{

	function __construct()
	{
		$res_historial_cce = array();
		$res_historial_cca = array();
		$res_historial_ccc = array();
		$res_widgets_cartas_cupo = array();
		$res_grafica_cc_expedidas = array();
		$res_grafica_cc_no_aduanadas = array();
		$res_grafica_cc_arribadas = array();
		$res_grafica_cc_canceladas = array();
		$res_tabla_ce_cc = array();
		$res_row ;
		$res_det_cc_ce = array();
	}

	function consulta_mes_base()
	{
		$conn = conexion::conectar();

		$sql = "SELECT TO_CHAR(SYSDATE, 'MM') AS MES FROM DUAL";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_row($stid)) != false) {
    		$this->res_consulta_mes_base = $row["0"];
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_consulta_mes_base;
	}

// ************************** FUNCION PARA FECHA DEL LA BASE **************************  //

	function date_base()
	{
		$conn = conexion::conectar();

		$sql = "SELECT TO_CHAR(SYSDATE, 'dd-mm-yyyy') AS FECHA FROM DUAL";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_row($stid)) != false) {
    		$this->res_date_base = $row["0"];
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_date_base;

	}


// ************************** FUNCION PARA HISTORIAL CARTAS CUPO EXPEDIDAS **************************  //
	function historial_cce($ce_cc_plaza)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($ce_cc_plaza==true){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */

		$conn = conexion::conectar();

		$sql = "SELECT distinct to_char(cupo.d_fecha_expedicion, 'dd-mm-yyyy')  AS historial_cce
				FROM op_ce_cartas_cupo cupo
        		LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
        		LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
        		LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				WHERE cupo.v_status IN ('E','PE') AND d_fecha_expedicion IS NOT NULL AND dep.d_plazo_dep_ini IS NULL
				".$and_sql_plaza."
				ORDER BY TO_DATE(historial_cce,'dd-mm-yyyy') DESC ";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_cce[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_historial_cce;
			#echo $sql;

	}

// ************************** FUNCION PARA HISTORIAL CARTAS CUPO NO ARRIBADAS **************************  //
	function historial_ccna($ce_cc_plaza)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($ce_cc_plaza==true){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */

		$conn = conexion::conectar();

		$sql = "SELECT distinct to_char(cupo.d_fecha_cancelacion, 'dd-mm-yyyy')  AS historial_ccna
				FROM op_ce_cartas_cupo cupo
				INNER JOIN op_ce_cc_bit b ON b.iid_almacen = cupo.iid_almacen AND b.iid_numero = cupo.iid_numero AND b.v_tipo = 'NA'
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE cupo.v_status IN ('NA')
				".$and_sql_plaza."
				ORDER BY TO_DATE(historial_ccna,'dd-mm-yyyy') DESC ";
			#	echo $sql;
		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_ccna[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_historial_ccna;

	}

// ************************** FUNCION PARA HISTORIAL CARTAS CUPO ARRIBADAS **************************  //
	function historial_cca($ce_cc_plaza)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($ce_cc_plaza==true){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql = "SELECT * FROM(
				SELECT distinct to_char(cupo.d_fecha_arribo, 'dd-mm-yyyy')  AS historial_cca
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
        		LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE cupo.v_status = 'RD' AND d_fecha_arribo IS NOT NULL
        		".$and_sql_plaza."
				UNION
				SELECT distinct to_char(dep.d_plazo_dep_ini, 'dd-mm-yyyy')  AS historial_cca
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
		        LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
		        LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				WHERE cupo.v_status = 'PE' AND dep.d_plazo_dep_ini IS NOT NULL
				AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) <> 'NA'
        		".$and_sql_plaza."
				)
				ORDER BY TO_DATE(historial_cca,'dd-mm-yyyy') DESC";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

#echo $sql;
		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_cca[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_historial_cca;

	}

// ************************** FUNCION PARA HISTORIAL CARTAS CUPO CANCELADAS **************************  //
	function historial_ccc($ce_cc_plaza)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($ce_cc_plaza==true){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql = "SELECT distinct to_char(cupo.d_fecha_cancelacion, 'dd-mm-yyyy')  AS historial_ccc
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
        		LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE cupo.v_status IN ('CC') AND d_fecha_cancelacion IS NOT NULL
				".$and_sql_plaza."
				ORDER BY TO_DATE(historial_ccc,'dd-mm-yyyy') DESC ";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_ccc[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_historial_ccc;

	}

// ************************** FUNCION PARA WIDGETS CARTAS CUPO EXPEDIDAS-ARRIBADAS-CANCELADAS **************************  //
function widgets_cartas_cupo($ce_cc_plaza,$ce_cc_almacen,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc){
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($ce_cc_plaza==true){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		if ($ce_cc_almacen==true){
			$and_sql_almacen = " AND alm.v_nombre = '".$ce_cc_almacen."' ";
		}
		if ($dia_ce_cc==true) {
			if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){
			//fecha de expedicion
			$and_sql_fecha_cce = " AND cupo.d_fecha_expedicion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_expedicion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
			//fecha de no arribo
			$and_sql_fecha_ccna = " AND cupo.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
			//fecha de arribo
			$and_sql_fecha_cca = " AND cupo.d_fecha_arribo >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_arribo < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
			$and_sql_fecha_cca_2 = " AND dep.d_plazo_dep_ini >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND dep.d_plazo_dep_ini < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
			//fecha de cancelacion
			$and_sql_fecha_ccc = " AND cupo.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
			}else{
			//fecha de expedicion
			if (date('m') == 01){
				$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
      			$anio_anterior = date ( 'Y' , $anio_anterior );

				$and_sql_fecha_cce = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
				$and_sql_fecha_ccna = " AND to_char(cupo.d_fecha_cancelacion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
			}else{
				$and_sql_fecha_cce = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
				$and_sql_fecha_ccna = " AND to_char(cupo.d_fecha_cancelacion, 'yyyy') = '".date('Y')."' ";
			}
			//fecha de arribo
			$and_sql_fecha_cca = " AND to_char(cupo.d_fecha_arribo, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
			$and_sql_fecha_cca_2 = " AND to_char(dep.d_plazo_dep_ini, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
			//fecha de cancelacion
			$and_sql_fecha_ccc = " AND to_char(cupo.d_fecha_cancelacion, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
			}
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql = "SELECT * FROM
				(
				SELECT COUNT(cupo.iid_numero) AS total_cce_sp
				FROM op_ce_cartas_cupo cupo
        		LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
        		LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE cupo.v_status IN ('E')
				".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_cce." AND pla.iid_plaza <> 23
				),
				(
				SELECT COUNT(cupo.iid_numero) AS total_cce_cp
				FROM op_ce_cartas_cupo cupo
        		LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
        		LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
        		LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				WHERE cupo.v_status IN ('PE')
				".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_cce." AND pla.iid_plaza <> 23
				AND dep.d_plazo_dep_ini IS NULL
				AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) <> 'NA'
				),
				(
					SELECT count(distinct(dep.vno_ped_imp)) AS total_cca
	          FROM op_ce_cartas_cupo cupo
	          LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
	          LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
	          INNER JOIN OP_IN_RECIBO_DEPOSITO DEP ON DEP.VNO_PED_IMP = CUPO.VNO_PEDIMENTO
				WHERE cupo.v_status = 'RD'
				".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_cca." AND pla.iid_plaza <> 23
				),

				(
				SELECT COUNT(cupo.iid_numero) AS total_cca_2
				FROM op_ce_cartas_cupo cupo
        		LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
        		LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
            	LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				WHERE cupo.v_status = 'PE'
				".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_cca_2." AND pla.iid_plaza <> 23
        		AND dep.d_plazo_dep_ini IS NOT NULL
				),

				(SELECT COUNT(cupo.iid_numero) AS total_cci
				FROM op_ce_cartas_cupo cupo
        		LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
        		LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE cupo.v_status = 'CI'
				".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_ccc." AND pla.iid_plaza <> 23
				),
				(SELECT COUNT(cupo.iid_numero) AS total_ccc
				FROM op_ce_cartas_cupo cupo
        		LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
        		LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE cupo.v_status = 'CC'
				".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_ccc." AND pla.iid_plaza <> 23
				),
				(SELECT COUNT(cupo.iid_numero) AS total_cna
				FROM op_ce_cartas_cupo cupo
        		LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
        		LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE cupo.v_status IN ('NA','PE')
				AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero
					AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) = 'NA'
				".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_ccna." AND pla.iid_plaza <> 23
				),
				(SELECT COUNT(*) AS ND_NA
          FROM op_ce_cartas_cupo cupo
          LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
          LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
          LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp =
                                                 cupo.vno_pedimento
         WHERE cupo.v_status IN ('NA')
           ".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_cce."
           AND pla.iid_plaza <> 23
           AND (SELECT b1.v_tipo
                  FROM op_ce_cc_bit b1
                 WHERE b1.iid_almacen = cupo.iid_almacen
                   AND b1.iid_numero = cupo.iid_numero
                   AND b1.i_consecutivo =
                       (SELECT MAX(B2.I_CONSECUTIVO)
                          FROM op_ce_cc_bit b2
                         WHERE b2.iid_almacen = cupo.iid_almacen
                           AND b2.iid_numero = cupo.iid_numero)) = 'ND')";

		#echo $sql;
		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_widgets_cartas_cupo[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_widgets_cartas_cupo;

	}

function widgets_cartas_cupoValue($ce_cc_plaza,$ce_cc_almacen,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc){
			/* ----------------- CONCATENACION SQL ----------------- */
			if ($ce_cc_plaza==true){
				$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
			}
			if ($ce_cc_almacen==true){
				$and_sql_almacen = " AND alm.v_nombre = '".$ce_cc_almacen."' ";
			}
			if ($dia_ce_cc==true) {
				if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){
				//fecha de expedicion
				$and_sql_fecha_cce = " AND t.d_fecha_expedicion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND t.d_fecha_expedicion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
				//fecha de no arribo
				$and_sql_fecha_ccna = " AND t.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND t.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
				//fecha de arribo
				$and_sql_fecha_cca = " AND t.d_fecha_arribo >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND t.d_fecha_arribo < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
				$and_sql_fecha_cca_2 = " AND dep.d_plazo_dep_ini >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND dep.d_plazo_dep_ini < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
				//fecha de cancelacion
				$and_sql_fecha_ccc = " AND t.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND t.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
				}else{
				//fecha de expedicion
				if (date('m') == 01){
					$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
	      			$anio_anterior = date ( 'Y' , $anio_anterior );

					$and_sql_fecha_cce = " AND to_char(t.d_fecha_expedicion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
					$and_sql_fecha_ccna = " AND to_char(t.d_fecha_cancelacion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
				}else{
					$and_sql_fecha_cce = " AND to_char(t.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
					$and_sql_fecha_ccna = " AND to_char(t.d_fecha_cancelacion, 'yyyy') = '".date('Y')."' ";
				}
				//fecha de arribo
				$and_sql_fecha_cca = " AND to_char(t.d_fecha_arribo, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
				$and_sql_fecha_cca_2 = " AND to_char(dep.d_plazo_dep_ini, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
				//fecha de cancelacion
				$and_sql_fecha_ccc = " AND to_char(t.d_fecha_cancelacion, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
				}
			}
			/* ----------------- CONCATENACION SQL ----------------- */
			$conn = conexion::conectar();

			$sql = "SELECT * FROM (SELECT SUM(
            					T.C_VAL_DOLARES * (SELECT R.C_TIPO_CAMBIO FROM OP_CE_TIPO_CAMBIO R WHERE R.DID_FECHA LIKE T.D_FECHA_EXPEDICION AND ROWNUM = 1)
      							) AS total_cce_spt
        FROM OP_CE_CARTAS_CUPO T
        LEFT JOIN almacen alm ON alm.iid_almacen = T.IID_ALMACEN
        LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
        WHERE T.V_STATUS = 'E'
							".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_cce."
              AND PLA.IID_PLAZA <> 23
				),
        (SELECT SUM(
            T.C_VAL_DOLARES * (SELECT R.C_TIPO_CAMBIO FROM OP_CE_TIPO_CAMBIO R WHERE R.DID_FECHA LIKE T.D_FECHA_EXPEDICION AND ROWNUM = 1)
        ) AS total_cce_cpt
         FROM OP_CE_CARTAS_CUPO T
         LEFT JOIN almacen alm ON alm.iid_almacen = T.IID_ALMACEN
         LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
         LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp =
                                                 t.vno_pedimento
         WHERE  T.V_STATUS = 'PE'
				 				".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_cce."
                AND PLA.IID_PLAZA <> 23
								AND dep.d_plazo_dep_ini IS NULL
                AND (SELECT b1.v_tipo
                            FROM op_ce_cc_bit b1
                            WHERE b1.iid_almacen = T.iid_almacen
                                  AND b1.iid_numero = T.iid_numero
                                  AND b1.i_consecutivo = (SELECT COUNT(*)
                                                          FROM op_ce_cc_bit b2
                                                          WHERE b2.iid_almacen = T.iid_almacen
                                                          AND b2.iid_numero = T.iid_numero)) <> 'NA'
				 ),
         (SELECT SUM(
            T.C_VAL_DOLARES * (SELECT R.C_TIPO_CAMBIO FROM OP_CE_TIPO_CAMBIO R WHERE R.DID_FECHA LIKE T.D_FECHA_EXPEDICION AND ROWNUM = 1)
          ) AS total_cnat
          FROM OP_CE_CARTAS_CUPO T
          LEFT JOIN almacen alm ON alm.iid_almacen = T.IID_ALMACEN
          LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
          WHERE T.V_STATUS IN('NA', 'PE')
          AND (SELECT b1.v_tipo
               FROM op_ce_cc_bit b1
               WHERE b1.iid_almacen = T.iid_almacen
                     AND b1.iid_numero = T.iid_numero
                     AND b1.i_consecutivo = (SELECT COUNT(*)
                                             FROM op_ce_cc_bit b2
                                             WHERE b2.iid_almacen = T.iid_almacen
                                             AND b2.iid_numero = T.iid_numero)) = 'NA'
					".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_ccna."
					AND PLA.IID_PLAZA <> 23 ),
					(SELECT SUM(ENC.C_VALOR_DOLARES * (SELECT R.C_TIPO_CAMBIO
                               FROM OP_CE_TIPO_CAMBIO R
                              WHERE R.DID_FECHA LIKE CTRL.D_FECHA_ENT
                                AND ROWNUM = 1)) AS total_ccat
          FROM OP_CE_CARTAS_CUPO T
          LEFT JOIN almacen alm ON alm.iid_almacen = T.IID_ALMACEN
          LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
          INNER JOIN OP_IN_RECIBO_DEPOSITO DEP ON DEP.VNO_PED_IMP = T.VNO_PEDIMENTO
					INNER JOIN OP_CE_PED_CTRL CTRL ON CTRL.VNO_PED_IMP = DEP.VNO_PED_IMP
					INNER JOIN OP_CE_PED_IMP_ENC ENC ON CTRL.VNO_PED_IMP = ENC.VNO_PED_IMP
          WHERE T.V_STATUS = 'RD'
								".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_cca."
                AND PLA.IID_PLAZA <> 23),
          (SELECT SUM(CASE
          WHEN T.S_TIPO = 2 THEN
              T.C_VAL_DOLARES * (SELECT R.C_TIPO_CAMBIO FROM OP_CE_TIPO_CAMBIO R WHERE R.DID_FECHA LIKE T.D_FECHA_EXPEDICION AND ROWNUM = 1)
          WHEN T.S_TIPO = 1 THEN T.C_VAL_DOLARES
          END) AS total_ccct
          FROM OP_CE_CARTAS_CUPO T
          LEFT JOIN almacen alm ON alm.iid_almacen = T.IID_ALMACEN
          LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
          WHERE T.V_STATUS = 'CC'
							".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_ccc."
                AND PLA.IID_PLAZA <> 23),
					(SELECT SUM(
				            T.C_VAL_DOLARES * (SELECT R.C_TIPO_CAMBIO FROM OP_CE_TIPO_CAMBIO R WHERE R.DID_FECHA LIKE T.D_FECHA_EXPEDICION AND ROWNUM = 1)
				        ) AS TOTAL_N_ADUANA
				         FROM OP_CE_CARTAS_CUPO T
				         LEFT JOIN almacen alm ON alm.iid_almacen = T.IID_ALMACEN
				         LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				         LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp =
				                                                 t.vno_pedimento
				         WHERE  T.V_STATUS = 'NA'
								 				".$and_sql_plaza.$and_sql_almacen.$and_sql_fecha_cce."
				                AND PLA.IID_PLAZA <> 23
												AND dep.d_plazo_dep_ini IS NULL
				                AND (SELECT b1.v_tipo
				                            FROM op_ce_cc_bit b1
				                            WHERE b1.iid_almacen = T.iid_almacen
				                                  AND b1.iid_numero = T.iid_numero
				                                  AND b1.i_consecutivo = (SELECT MAX(b2.i_consecutivo)
				                                                          FROM op_ce_cc_bit b2
				                                                          WHERE b2.iid_almacen = T.iid_almacen
				                                                          AND b2.iid_numero = T.iid_numero)) = 'ND'
								 )";

			#echo $sql;
			$stid = oci_parse($conn, $sql);
					oci_execute($stid );

			while (($row = oci_fetch_assoc($stid)) != false)
			{
				$this->res_widgets_cartas_cupo[]=$row;
			}
				oci_free_statement($stid);
				oci_close($conn);
				return $this->res_widgets_cartas_cupo;

}
// ************************** FUNCION PARA GRAFICA CARTAS CUPO EXPEDIDAS **************************  //

	function grafica_cc_expedidas($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($dia_ce_cc==true) {
			if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){
			$and_sql_fecha = " AND cupo.d_fecha_expedicion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_expedicion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
			}else{
			//$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
				if (date('m') == 01){
					$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
	      			$anio_anterior = date ( 'Y' , $anio_anterior );

					$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
				}else{
					$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
				}
			}
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		 $sql = "SELECT pla.iid_plaza AS id_plaza, decode(to_char(pla.iid_plaza),
				3,'CÓRDOBA',
				4,'MÉXICO',
				5,'GOLFO',
				6,'PENINSULA',
				7,'PUEBLA',
				8,'BAJIO',
				17,'OCCIDENTE',
				18,'NORESTE',
				23,'LEON'
				) as plaza_decode,pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas
        		,COUNT(cupo.iid_almacen) AS total_cce
				FROM plaza pla
				LEFT JOIN almacen alm ON alm.iid_plaza = pla.iid_plaza
				LEFT JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen AND cupo.v_status in ('PE','E') ".$and_sql_fecha."
				LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18/*,23*/) AND dep.vno_ped_imp is null
				AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) <> 'NA'
				GROUP BY (pla.iid_plaza,pla.v_razon_social,pla.v_siglas)
				ORDER BY pla.iid_plaza";

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_grafica_cc_expedidas[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_grafica_cc_expedidas;
	}
//************************INICIA CARTAS CUPO NO ADUANADAS ********************************************//
function grafica_cc_no_aduanadas($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc)
{
	/* ----------------- CONCATENACION SQL ----------------- */
	if ($dia_ce_cc==true) {
		if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){
		$and_sql_fecha = " AND cupo.d_fecha_expedicion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_expedicion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
		}else{
		//$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
			if (date('m') == 01){
				$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
						$anio_anterior = date ( 'Y' , $anio_anterior );

				$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
			}else{
				$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
			}
		}
	}
	/* ----------------- CONCATENACION SQL ----------------- */
	$conn = conexion::conectar();

	 $sql = "SELECT pla.iid_plaza AS id_plaza, decode(to_char(pla.iid_plaza),
			3,'CÓRDOBA',
			4,'MÉXICO',
			5,'GOLFO',
			6,'PENINSULA',
			7,'PUEBLA',
			8,'BAJIO',
			17,'OCCIDENTE',
			18,'NORESTE',
			23,'LEON'
			) as plaza_decode,pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas
					,COUNT(cupo.iid_almacen) AS total_cce
			FROM plaza pla
			LEFT JOIN almacen alm ON alm.iid_plaza = pla.iid_plaza
			LEFT JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen AND cupo.v_status in ('NA') ".$and_sql_fecha."
			LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
			WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18/*,23*/) AND dep.vno_ped_imp is null
			AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1
			WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero
			AND b1.i_consecutivo = ( SELECT MAX(B2.I_CONSECUTIVO) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) = 'ND'
			GROUP BY (pla.iid_plaza,pla.v_razon_social,pla.v_siglas)
			ORDER BY pla.iid_plaza";

		#	echo $sql;
	$stid = oci_parse($conn, $sql);
			ociexecute($stid);

	while (($row = oci_fetch_assoc($stid)) != false)
	{
		$this->res_grafica_cc_no_aduanadas[] = $row ;
	}

		oci_free_statement($stid);
		oci_close($conn);
		return $this->res_grafica_cc_no_aduanadas;
}
// ************************** FUNCION PARA GRAFICA CARTAS CUPO NO ARRIBADAS **************************  //

	function grafica_cc_noarribadas($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($dia_ce_cc==true) {
			if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){
			$and_sql_fecha = " AND cupo.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
			}else{
			//$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_cancelacion, 'yyyy') = '".date('Y')."' ";
				if (date('m') == 01){
					$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
	      			$anio_anterior = date ( 'Y' , $anio_anterior );

					$and_sql_fecha = " AND to_char(cupo.d_fecha_cancelacion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
				}else{
					$and_sql_fecha = " AND to_char(cupo.d_fecha_cancelacion, 'yyyy') = '".date('Y')."' ";
				}
			}
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		 $sql = "SELECT pla.iid_plaza AS id_plaza,
		       decode(to_char(pla.iid_plaza),
				3,'CÓRDOBA',
				4,'MÉXICO',
				5,'GOLFO',
				6,'PENINSULA',
				7,'PUEBLA',
				8,'BAJIO',
				17,'OCCIDENTE',
				18,'NORESTE',
				23,'LEON'
				) as plaza_decode,
		        pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas, COUNT( b.v_tipo ) AS total_ccna
				FROM  plaza pla
				LEFT JOIN almacen alm ON pla.iid_plaza = alm.iid_plaza
			  	LEFT JOIN op_ce_cartas_cupo cupo ON alm.iid_almacen = cupo.iid_almacen AND cupo.v_status IN ('NA','PE') ".$and_sql_fecha."
        		LEFT JOIN op_ce_cc_bit b ON cupo.iid_almacen = b.iid_almacen AND cupo.iid_numero = b.iid_numero AND b.v_tipo = 'NA'
        		AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) = 'NA'
			    WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18/*,23*/)
				GROUP BY pla.iid_plaza, pla.v_razon_social, pla.v_siglas
			    ORDER BY pla.iid_plaza";

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_grafica_cc_noarribadas[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_grafica_cc_noarribadas;
	}

// ************************** FUNCION PARA GRAFICA CARTAS CUPO ARRIBADAS **************************  //

	function grafica_cc_arribadas($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($dia_ce_cc==true) {
			if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){
			$and_sql_fecha = " cupo.d_fecha_arribo >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_arribo < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
			$and_sql_fecha_2 = " dep.d_plazo_dep_ini >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND dep.d_plazo_dep_ini < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
			}else{
			$and_sql_fecha = " to_char(cupo.d_fecha_arribo, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
			$and_sql_fecha_2 = " to_char(dep.d_plazo_dep_ini, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
			}
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql = "SELECT pla.iid_plaza AS id_plaza,
		       decode(to_char(pla.iid_plaza),
				3,'CÓRDOBA',
				4,'MÉXICO',
				5,'GOLFO',
				6,'PENINSULA',
				7,'PUEBLA',
				8,'BAJIO',
				17,'OCCIDENTE',
				18,'NORESTE',
				23,'LEON'
				) as plaza_decode,
		        pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas
        		,COUNT(distinct( DECODE (cupo.v_status, 'RD',
        		case when ( ".$and_sql_fecha." ) then (dep.vno_ped_imp) end
        		) )) AS total_cce
        		,COUNT(DECODE (cupo.v_status, 'PE',
        		case when ( ".$and_sql_fecha_2." ) then (cupo.iid_almacen) end
        		) ) AS total_cpe
				FROM  plaza pla
				LEFT OUTER JOIN almacen alm ON alm.iid_plaza = pla.iid_plaza
				LEFT OUTER JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen
        		INNER JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
		        WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18/*,23*/)
				GROUP BY pla.iid_plaza, pla.v_razon_social, pla.v_siglas
		        ORDER BY pla.iid_plaza"	;

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_grafica_cc_arribadas[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_grafica_cc_arribadas;
	}

// ************************** FUNCION PARA GRAFICA CARTAS CUPO CANCELADAS **************************  //

	function grafica_cc_canceladas($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($dia_ce_cc==true) {
			if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){
			$and_sql_fecha = " AND cupo.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
			}else{
			$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_cancelacion, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
			}
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql = "SELECT pla.iid_plaza AS id_plaza,
		       decode(to_char(pla.iid_plaza),
				3,'CÓRDOBA',
				4,'MÉXICO',
				5,'GOLFO',
				6,'PENINSULA',
				7,'PUEBLA',
				8,'BAJIO',
				17,'OCCIDENTE',
				18,'NORESTE',
				23,'LEON'
				) as plaza_decode,
		        pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas, COUNT(cupo.iid_almacen) AS total_cce
				FROM  plaza pla
				LEFT OUTER JOIN almacen alm ON alm.iid_plaza = pla.iid_plaza
				LEFT OUTER JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen AND cupo.v_status IN ('CC')
				".$and_sql_fecha."
		        WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18/*,23*/)
				GROUP BY pla.iid_plaza, pla.v_razon_social, pla.v_siglas
		        ORDER BY pla.iid_plaza"	;

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_grafica_cc_canceladas[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_grafica_cc_canceladas;
	}
// ************************** FUNCION PARA TABLA CARTAS CUPO EXPEDIDAS EN ALMACEN **************************  //

	function tabla_cce_almacen($ce_cc_plaza,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($dia_ce_cc == true){
			switch (true) {
				case ($fec_ini_ce_cc == true) && ($fec_fin_ce_cc == true):
					$and_sql_fecha = " AND cupo.d_fecha_expedicion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_expedicion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
					break;
				default:
					//$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
					if (date('m') == 01){
						$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
		      			$anio_anterior = date ( 'Y' , $anio_anterior );

						$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
					}else{
						$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
					}
					break;
			}
		}
		if ( $ce_cc_plaza == true ){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql ="	SELECT cupo.iid_almacen AS id_almacen, alm.v_nombre AS almacen, alm.v_iniciales AS iniciales_almacen, pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas, COUNT(cupo.iid_numero) AS total_cc
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
       		 	LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				WHERE cupo.v_status IN ('E','PE') AND dep.vno_ped_imp IS NULL
				".$and_sql_plaza.$and_sql_fecha." AND pla.iid_plaza <> 23
				AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) <> 'NA'
				GROUP BY cupo.iid_almacen, alm.v_nombre, alm.v_iniciales, pla.v_razon_social, pla.v_siglas
				ORDER BY total_cc ";

			#echo $sql;
		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_cce_almacen[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_cce_almacen;
	}


	function tabla_na_nd_almacen($ce_cc_plaza,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($dia_ce_cc == true){
			switch (true) {
				case ($fec_ini_ce_cc == true) && ($fec_fin_ce_cc == true):
					$and_sql_fecha = " AND cupo.d_fecha_expedicion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_expedicion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
					break;
				default:
					//$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
					if (date('m') == 01){
						$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
		      			$anio_anterior = date ( 'Y' , $anio_anterior );

						$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
					}else{
						$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
					}
					break;
			}
		}
		if ( $ce_cc_plaza == true ){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql ="	SELECT cupo.iid_almacen AS id_almacen, alm.v_nombre AS almacen, alm.v_iniciales AS iniciales_almacen, pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas, COUNT(cupo.iid_numero) AS total_cc
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
       		 	LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				WHERE cupo.v_status IN ('NA') AND dep.vno_ped_imp IS NULL
				".$and_sql_plaza.$and_sql_fecha." AND pla.iid_plaza <> 23
				AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen
					 AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT MAX(B2.I_CONSECUTIVO) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero) ) = 'ND'
				GROUP BY cupo.iid_almacen, alm.v_nombre, alm.v_iniciales, pla.v_razon_social, pla.v_siglas
				ORDER BY total_cc ";

			#echo $sql;
		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_cce_almacen2[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_cce_almacen2;
	}

// ************************** FUNCION PARA TABLA CARTAS CUPO NO ARRIBADAS EN ALMACEN **************************  //

	function tabla_ccna_almacen($ce_cc_plaza,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($dia_ce_cc == true){
			switch (true) {
				case ($fec_ini_ce_cc == true) && ($fec_fin_ce_cc == true):
					$and_sql_fecha = " AND cupo.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
					break;
				default:
					//$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_cancelacion, 'yyyy') = '".date('Y')."' ";
					if (date('m') == 01){
						$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
		      			$anio_anterior = date ( 'Y' , $anio_anterior );

						$and_sql_fecha = " AND to_char(cupo.d_fecha_cancelacion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
					}else{
						$and_sql_fecha = " AND to_char(cupo.d_fecha_cancelacion, 'yyyy') = '".date('Y')."' ";
					}
					break;
			}
		}
		if ( $ce_cc_plaza == true ){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql ="	SELECT cupo.iid_almacen AS id_almacen, alm.v_nombre AS almacen, alm.v_iniciales AS iniciales_almacen, pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas, COUNT(cupo.iid_numero) AS total_ccna
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE cupo.v_status IN ('NA','PE')
				".$and_sql_plaza.$and_sql_fecha." AND pla.iid_plaza <> 23
				AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) = 'NA'
				GROUP BY cupo.iid_almacen, alm.v_nombre, alm.v_iniciales, pla.v_razon_social, pla.v_siglas
				ORDER BY total_ccna ";

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_ccna_almacen[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_ccna_almacen;
	}

// ************************** FUNCION PARA TABLA CARTAS CUPO ARRIBADAS EN ALMACEN **************************  //

	function tabla_cca_almacen($ce_cc_plaza,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($dia_ce_cc == true){
			switch (true) {
				case ($fec_ini_ce_cc == true) && ($fec_fin_ce_cc == true):
					$and_sql_fecha = " cupo.d_fecha_arribo >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_arribo < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
					$and_sql_fecha_2 = " dep.d_plazo_dep_ini >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND dep.d_plazo_dep_ini < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
					break;
				default:
					$and_sql_fecha = " to_char(cupo.d_fecha_arribo, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
					$and_sql_fecha_2 = " to_char(dep.d_plazo_dep_ini, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
					break;
			}
		}
		if ( $ce_cc_plaza == true ){
			$and_sql_plaza = " AND plaza =  '".$ce_cc_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql = "SELECT * FROM (
				SELECT alm.iid_almacen, alm.v_nombre AS almacen, alm.v_iniciales AS iniciales_almacen, pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas,
				(
				COUNT(distinct(
				DECODE(cupo.v_status, 'RD',
				case when (".$and_sql_fecha.") then (cupo.vno_pedimento) end
				)
				))
				) AS total_cc
				FROM almacen alm
				INNER JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza AND pla.iid_plaza IN (3,4,5,6,7,8,17,18/*,23*/)
				INNER JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				GROUP BY alm.iid_almacen, alm.iid_almacen, alm.v_nombre, alm.v_iniciales, pla.v_razon_social, pla.v_siglas
				) WHERE total_cc <> 0 ".$and_sql_plaza.""	;

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_cca_almacen[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_cca_almacen;
	}
// ************************** FUNCION PARA TABLA CARTAS CUPO CANCELADAS EN ALMACEN **************************  //

	function tabla_ccc_almacen($ce_cc_plaza,$dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($dia_ce_cc == true){
			switch (true) {
				case ($fec_ini_ce_cc == true) && ($fec_fin_ce_cc == true):
					$and_sql_fecha = " AND cupo.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
					break;
				default:
					$and_sql_fecha = " AND to_char(cupo.d_fecha_cancelacion, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
					break;
			}
		}
		if ( $ce_cc_plaza == true ){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql = "SELECT cupo.iid_almacen AS id_almacen, alm.v_nombre AS almacen, alm.v_iniciales AS iniciales_almacen, pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas, COUNT(cupo.iid_almacen) AS total_cc
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE cupo.v_status IN ('CC')
				".$and_sql_plaza.$and_sql_fecha." AND pla.iid_plaza <> 23
				GROUP BY cupo.iid_almacen, alm.v_nombre, alm.v_iniciales, pla.v_razon_social, pla.v_siglas
				ORDER BY total_cc"	;

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_ccc_almacen[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_ccc_almacen;
	}
// ************************** FUNCION PARA TABLA CARTAS CUPO **************************  //

	function tabla_ce_cc($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc,$op_ce_cartas_cupo,$ce_cc_plaza,$ce_cc_almacen,$id_almacen_cc_ce,$id_num_cc_ce)
	{
		/* ----------------- CONCATENACION SQL ----------------- */

		if ( $op_ce_cartas_cupo == "E" ){
			$and_pe = " AND dep.vno_ped_imp is null AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) <> 'NA' ";
		}

		if ( $op_ce_cartas_cupo == "NA" ){
			$inner_join_na = " INNER JOIN op_ce_cc_bit b ON b.iid_almacen = cupo.iid_almacen AND b.iid_numero = cupo.iid_numero AND b.v_tipo = 'NA'
			AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) = 'NA' ";
		}

		if ($dia_ce_cc==true) {
			if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){

				switch ($op_ce_cartas_cupo) {
					case 'E':
						$and_sql_fecha = " AND cupo.d_fecha_expedicion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_expedicion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
						break;
					case 'NA':
						$and_sql_fecha = " AND cupo.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
						break;
					case 'RD':
						$and_sql_fecha = " AND cupo.d_fecha_arribo >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_arribo < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
						break;
					case 'CC':
						$and_sql_fecha = " AND cupo.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
						break;
				}
			}else{

				switch ($op_ce_cartas_cupo) {
					case 'E':
						//$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
						if (date('m') == 01){
						$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
		      			$anio_anterior = date ( 'Y' , $anio_anterior );

						$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
						}else{
							$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
						}
						break;
					case 'NA':
						if (date('m') == 01){
						$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
		      			$anio_anterior = date ( 'Y' , $anio_anterior );

						$and_sql_fecha = " AND to_char(cupo.d_fecha_cancelacion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
						}else{
							$and_sql_fecha = " AND to_char(cupo.d_fecha_cancelacion, 'yyyy') = '".date('Y')."' ";
						}
						break;
					case 'RD':
						$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_arribo, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
						break;
					case 'CC':
						$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_cancelacion, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
						break;
				}

			}
		}
		if ($ce_cc_plaza == true){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		if ($ce_cc_almacen == true){
			$and_sql_almacen = " AND alm.v_nombre = '".$ce_cc_almacen."' ";
		}
		if ($id_almacen_cc_ce == true && $id_num_cc_ce == true){
			$and_sql_det = " AND cupo.iid_almacen = '".$id_almacen_cc_ce."' AND cupo.iid_numero = '".$id_num_cc_ce."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql = "SELECT alm.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, cupo.iid_almacen AS id_almacen, alm.v_nombre AS almacen
				,cupo.iid_numero AS numero_cc, cupo.v_status AS status_cc, cupo.iid_importador AS id_importador, importador.v_razon_social AS importador
				,cupo.v_cve_adu_cir AS cve_adu_cir, cupo.v_cve_sidefi as cve_sidefi, cupo.iid_numero AS id_numero, cupo.c_val_dolares AS v_dolares, cupo.vid_adu_desp AS id_adu_desp, aduana.v_nombre AS aduana_desp
				,to_char(cupo.d_fecha_expedicion, 'dd-mm-yyyy') AS fecha_expedicion, to_char(cupo.d_fecha_arribo, 'dd-mm-yyyy') AS fecha_arribo, to_char(cupo.d_fecha_cancelacion, 'dd-mm-yyyy') AS fecha_cancelacion
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				LEFT JOIN op_ce_importador importador ON importador.iid_importador = cupo.iid_importador
				LEFT JOIN op_ce_aduana aduana ON aduana.vid_adu = cupo.vid_adu_desp
				LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				".$inner_join_na."
				WHERE cupo.v_status IN ('E','PE', 'RD', 'CI','CC','NA')
				".$and_sql_fecha.$and_sql_plaza.$and_sql_almacen.$and_pe.$and_sql_det." AND pla.iid_plaza <> 23
				ORDER BY v_dolares DESC ";

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_ce_cc[] = $row ;
		}
		#echo $sql;
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_ce_cc;
	}

	function tabla_ce_nd($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc,$op_ce_cartas_cupo,$ce_cc_plaza,$ce_cc_almacen,$id_almacen_cc_ce,$id_num_cc_ce)
	{
		/* ----------------- CONCATENACION SQL ----------------- */

		if ( $op_ce_cartas_cupo == "E" ){
			$and_pe = " AND dep.vno_ped_imp is null AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) <> 'NA' ";
		}

		if ( $op_ce_cartas_cupo == "NA" ){
			$inner_join_na = " INNER JOIN op_ce_cc_bit b ON b.iid_almacen = cupo.iid_almacen AND b.iid_numero = cupo.iid_numero AND b.v_tipo = 'NA'
			AND ( SELECT b1.v_tipo FROM op_ce_cc_bit b1 WHERE b1.iid_almacen = cupo.iid_almacen AND b1.iid_numero = cupo.iid_numero AND b1.i_consecutivo = ( SELECT COUNT(*) FROM op_ce_cc_bit b2 WHERE b2.iid_almacen = cupo.iid_almacen AND b2.iid_numero = cupo.iid_numero ) ) = 'NA' ";
		}

		if ($dia_ce_cc==true) {
			if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){

				switch ($op_ce_cartas_cupo) {
					case 'E':
						$and_sql_fecha = " AND cupo.d_fecha_expedicion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_expedicion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
						break;
					case 'ND':
						$and_sql_fecha = " AND cupo.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
						break;
					case 'RD':
						$and_sql_fecha = " AND cupo.d_fecha_arribo >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_arribo < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
						break;
					case 'CC':
						$and_sql_fecha = " AND cupo.d_fecha_cancelacion >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_cancelacion < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
						break;
				}
			}else{

				switch ($op_ce_cartas_cupo) {
					case 'E':
						//$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
						if (date('m') == 01){
						$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
								$anio_anterior = date ( 'Y' , $anio_anterior );

						$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
						}else{
							$and_sql_fecha = " AND to_char(cupo.d_fecha_expedicion, 'yyyy') = '".date('Y')."' ";
						}
						break;
					case 'ND':
						if (date('m') == 01){
						$anio_anterior = strtotime ( '-1 year' , strtotime ( date('Y') ) ) ;
								$anio_anterior = date ( 'Y' , $anio_anterior );

						$and_sql_fecha = " AND to_char(cupo.d_fecha_cancelacion, 'yyyy') IN ('".$anio_anterior."','".date('Y')."') ";
						}else{
							$and_sql_fecha = " AND to_char(cupo.d_fecha_cancelacion, 'yyyy') = '".date('Y')."' ";
						}
						break;
					case 'RD':
						$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_arribo, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
						break;
					case 'CC':
						$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_cancelacion, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
						break;
				}

			}
		}
		if ($ce_cc_plaza == true){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		if ($ce_cc_almacen == true){
			$and_sql_almacen = " AND alm.v_nombre = '".$ce_cc_almacen."' ";
		}
		if ($id_almacen_cc_ce == true && $id_num_cc_ce == true){
			$and_sql_det = " AND cupo.iid_almacen = '".$id_almacen_cc_ce."' AND cupo.iid_numero = '".$id_num_cc_ce."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql = "SELECT alm.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, cupo.iid_almacen AS id_almacen, alm.v_nombre AS almacen
				,cupo.iid_numero AS numero_cc, cupo.v_status AS status_cc, cupo.iid_importador AS id_importador, importador.v_razon_social AS importador
				,cupo.v_cve_adu_cir AS cve_adu_cir, cupo.v_cve_sidefi as cve_sidefi, cupo.iid_numero AS id_numero, cupo.c_val_dolares AS v_dolares, cupo.vid_adu_desp AS id_adu_desp, aduana.v_nombre AS aduana_desp
				,to_char(cupo.d_fecha_expedicion, 'dd-mm-yyyy') AS fecha_expedicion, to_char(cupo.d_fecha_arribo, 'dd-mm-yyyy') AS fecha_arribo, to_char(cupo.d_fecha_cancelacion, 'dd-mm-yyyy') AS fecha_cancelacion
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				LEFT JOIN op_ce_importador importador ON importador.iid_importador = cupo.iid_importador
				LEFT JOIN op_ce_aduana aduana ON aduana.vid_adu = cupo.vid_adu_desp
				LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				".$inner_join_na."
				WHERE cupo.v_status IN ('NA')
				AND (SELECT b1.v_tipo
                  FROM op_ce_cc_bit b1
                 WHERE b1.iid_almacen = cupo.iid_almacen
                   AND b1.iid_numero = cupo.iid_numero
                   AND b1.i_consecutivo =
                       (SELECT max(b2.i_consecutivo)
                          FROM op_ce_cc_bit b2
                         WHERE b2.iid_almacen = cupo.iid_almacen
                           AND b2.iid_numero = cupo.iid_numero)) = 'ND'
				".$and_sql_fecha.$and_sql_plaza.$and_sql_almacen.$and_pe.$and_sql_det." AND pla.iid_plaza <> 23
				ORDER BY v_dolares DESC ";

				#echo $sql;
		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_ce_cc[] = $row ;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_ce_cc;
	}
// ************************** FUNCION PARA TABLA CARTAS CUPO ARRIBADAS RD-PE **************************  //

	function tabla_cc_arribada($dia_ce_cc,$fec_ini_ce_cc,$fec_fin_ce_cc,$op_ce_cartas_cupo,$ce_cc_plaza,$ce_cc_almacen,$id_almacen_cc_ce,$id_num_cc_ce)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ($dia_ce_cc==true) {
			if ( $fec_ini_ce_cc == true && $fec_fin_ce_cc == true ){

				switch ($op_ce_cartas_cupo) {
					case 'RD':
						$and_sql_fecha = " AND cupo.d_fecha_arribo >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND cupo.d_fecha_arribo < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
						$and_sql_fecha_2 = " AND dep.d_plazo_dep_ini >= trunc(to_date('".$fec_ini_ce_cc."','dd-mm-yyyy') ) AND dep.d_plazo_dep_ini < trunc(to_date('".$fec_fin_ce_cc."','dd-mm-yyyy') ) +1 ";
						break;
				}
			}else{

				switch ($op_ce_cartas_cupo) {
					case 'RD':
						$and_sql_fecha = " AND TO_CHAR(cupo.d_fecha_arribo, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
						$and_sql_fecha_2 = " AND TO_CHAR(dep.d_plazo_dep_ini, 'dd-mm-yyyy') = '".$dia_ce_cc."' ";
						break;
				}

			}
		}
		if ($ce_cc_plaza == true){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_cc_plaza."' ";
		}
		if ($ce_cc_almacen == true){
			$and_sql_almacen = " AND alm.v_nombre = '".$ce_cc_almacen."' ";
		}
		if ($id_almacen_cc_ce == true && $id_num_cc_ce == true){
			$and_sql_det = " AND cupo.iid_almacen = '".$id_almacen_cc_ce."' AND cupo.iid_numero = '".$id_num_cc_ce."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */
		$conn = conexion::conectar();

		$sql = "SELECT alm.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, cupo.iid_almacen AS id_almacen, alm.v_nombre AS almacen
				,cupo.iid_numero AS numero_cc, cupo.v_status AS status_cc, cupo.iid_importador AS id_importador, importador.v_razon_social AS importador
				,cupo.v_cve_adu_cir AS cve_adu_cir, cupo.v_cve_sidefi as cve_sidefi, cupo.iid_numero AS id_numero, cupo.c_val_dolares AS v_dolares, cupo.vid_adu_desp AS id_adu_desp, aduana.v_nombre AS aduana_desp
				,to_char(cupo.d_fecha_expedicion, 'dd-mm-yyyy') AS fecha_expedicion, to_char(cupo.d_fecha_arribo, 'dd-mm-yyyy') AS fecha_arribo, to_char(cupo.d_fecha_cancelacion, 'dd-mm-yyyy') AS fecha_cancelacion
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				LEFT JOIN op_ce_importador importador ON importador.iid_importador = cupo.iid_importador
				LEFT JOIN op_ce_aduana aduana ON aduana.vid_adu = cupo.vid_adu_desp
				WHERE cupo.v_status IN ('RD')
				".$and_sql_fecha.$and_sql_plaza.$and_sql_almacen.$and_pe.$and_sql_det." AND pla.iid_plaza <> 23
				ORDER BY v_dolares DESC ";

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_cc_arribada[] = $row ;
		}

		$sql_2 = "SELECT alm.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, cupo.iid_almacen AS id_almacen, alm.v_nombre AS almacen
				,cupo.iid_numero AS numero_cc, cupo.v_status AS status_cc, cupo.iid_importador AS id_importador, importador.v_razon_social AS importador
				,cupo.v_cve_adu_cir AS cve_adu_cir, cupo.v_cve_sidefi as cve_sidefi, cupo.iid_numero AS id_numero, cupo.c_val_dolares AS v_dolares, cupo.vid_adu_desp AS id_adu_desp, aduana.v_nombre AS aduana_desp
				,to_char(cupo.d_fecha_expedicion, 'dd-mm-yyyy') AS fecha_expedicion, to_char(dep.d_plazo_dep_ini, 'dd-mm-yyyy') AS fecha_arribo, to_char(cupo.d_fecha_cancelacion, 'dd-mm-yyyy') AS fecha_cancelacion
				FROM op_ce_cartas_cupo cupo
				LEFT JOIN almacen alm ON alm.iid_almacen = cupo.iid_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				LEFT JOIN op_ce_importador importador ON importador.iid_importador = cupo.iid_importador
				LEFT JOIN op_ce_aduana aduana ON aduana.vid_adu = cupo.vid_adu_desp
				LEFT JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
				WHERE cupo.v_status IN ('PE')
				".$and_sql_fecha_2.$and_sql_plaza.$and_sql_almacen.$and_pe.$and_sql_det." AND dep.d_plazo_dep_ini IS NOT NULL
        		AND pla.iid_plaza <> 23
				ORDER BY v_dolares DESC ";

				#echo $sql;
		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_cc_arribada[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_cc_arribada;
	}
// ************************** FUNCION PARA BUSCAR FIRMA DE ACUSE EN BITACORA DE EVENTOS **************************  //

	function bit_cc($numero,$almacen,$consulta_ajax)
	{
		$conn = conexion::conectar();

		if ($consulta_ajax == true){
			$sql =" SELECT bit.vid_evento AS evento, bit.d_fecha_evento AS fecha_evento, bit.i_status_enviado AS status_enviado, bit.v_firma_acuse AS firma_acuse, bit.v_comentario AS comentario, bit.v_tipo AS tipo
					FROM op_ce_cc_bit bit
					WHERE bit.iid_numero = ".$numero." AND bit.iid_almacen = ".$almacen;
		}else{
			$sql =" SELECT DISTINCT(FIRST_VALUE(bit.v_firma_acuse) OVER (order by bit.d_fecha_evento desc)) AS firma_acuse,
				FIRST_VALUE(bit.vid_evento) OVER (order by bit.d_fecha_evento desc) AS evento, FIRST_VALUE(bit.v_tipo) OVER (order by bit.d_fecha_evento desc) AS tipo
					FROM op_ce_cc_bit bit
					WHERE bit.iid_numero = ".$numero." AND bit.iid_almacen = ".$almacen;
		}

		#echo $sql;

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_bit_cc[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $res_bit_cc;

	}
// ************************** FUNCION PARA SUMA DE CANTIDAD UMT **************************  //

	function cantidad_umt_cc($numero_cc_can_utm,$id_almacen_cc_can_utm)
	{
		$conn = conexion::conectar();

		$sql = "SELECT SUM(det.i_cantidad)
				FROM op_ce_cartas_cupo_det det
				WHERE det.iid_almacen = ".$id_almacen_cc_can_utm." AND det.iid_numero = ".$numero_cc_can_utm."";

		$stid = oci_parse($conn, $sql);
            oci_execute($stid);
            $res_row = oci_fetch_array($stid, OCI_BOTH);

			oci_free_statement($stid);
			oci_close($conn);
			return $res_row[0];

	}

// ************************** FUNCION PARA DETALLE DE CARTA CUPO **************************  //

	function det_cc_ce($id_almacen_cc_ce,$id_num_cc_ce)
	{
		$conn = conexion::conectar();

		$sql = "SELECT det.iid_partida AS partida_det, det.iid_um AS id_um, um.v_descripcion AS um, det.i_cantidad AS cantidad_umt, det.i_valor AS val_dolares, aran.vid_fraccion_ara AS num_fraccion, aran.v_descripcion AS des_aran
				FROM op_ce_cartas_cupo_det det
				LEFT JOIN op_ce_um um ON um.iid_um = det.iid_um
				LEFT JOIN op_ce_fraccion_arancelaria aran ON aran.vid_fraccion_ara = det.v_referencia
				WHERE det.iid_almacen = '".$id_almacen_cc_ce."' AND det.iid_numero = '".$id_num_cc_ce."'
				ORDER BY partida_det";

		$stid = oci_parse($conn, $sql);
				ociexecute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_det_cc_ce[] = $row ;
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_det_cc_ce;

	}

/* ============ INICIA FUNCION PARA CALCULAR TIEMPO DE FECHAS ============ */
	function tiempoTranscurridoFechas($fechaInicio,$fechaFin)
	{
	    $fecha1 = new DateTime($fechaInicio);
	    $fecha2 = new DateTime($fechaFin);
	    $fecha = $fecha1->diff($fecha2);
	    $tiempo = "";

	    //años
	    if($fecha->y > 0)
	    {
	        $tiempo .= $fecha->y;

	        if($fecha->y == 1)
	            $tiempo .= " AÑO, ";
	        else
	            $tiempo .= " AÑOS, ";
	    }

	    //meses
	    if($fecha->m > 0)
	    {
	        $tiempo .= $fecha->m;

	        if($fecha->m == 1)
	            $tiempo .= " MES, ";
	        else
	            $tiempo .= " MESES, ";
	    }

	    //dias
	    if($fecha->d >= 0)
	    {
	        $tiempo .= $fecha->d;

	        if($fecha->d == 1)
	            $tiempo .= " DÍA ";
	        else
	            $tiempo .= " DÍAS ";
	    }

	    // //horas
	    // if($fecha->h > 0)
	    // {
	    //     $tiempo .= $fecha->h;

	    //     if($fecha->h == 1)
	    //         $tiempo .= " hora, ";
	    //     else
	    //         $tiempo .= " horas, ";
	    // }

	    // //minutos
	    // if($fecha->i > 0)
	    // {
	    //     $tiempo .= $fecha->i;

	    //     if($fecha->i == 1)
	    //         $tiempo .= " minuto";
	    //     else
	    //         $tiempo .= " minutos";
	    // }
	    // else if($fecha->i == 0) //segundos
	    //     $tiempo .= $fecha->s." segundos";

	    return $tiempo;
	}
/* ============ TERMINA FUNCION PARA CALCULAR TIEMPO DE FECHAS ============ */

}
