
<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class RotacionPersonal
{
	public function cantidadEmpleados($fecha){
		$fecha_ini = substr($fecha,0,10);
		$fecha_fin = substr($fecha, 11, 10);

		$conn = conexion::conectar();
		$res_array = array();
		//echo $fecha;
		if($fecha == 'ALL'){
			//TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),-1) )
			$sql = "SELECT ROUND(MONTHS_BETWEEN
									(TRUNC(SYSDATE),
									 TRUNC(SYSDATE-30) )) AS MESES
							FROM DUAL";
		}
		else {
			$sql = "SELECT ROUND(MONTHS_BETWEEN
									(TO_DATE('".$fecha_fin."','DD-MM-YYYY'),
									TO_DATE('".$fecha_ini."','DD-MM-YYYY') )) AS MESES
							FROM DUAL";
		}
				$stid = oci_parse($conn, $sql);
				#echo $sql;
				oci_execute($stid);
				while (($row = oci_fetch_assoc($stid)) != false) {
					$res_array[]= $row;
				}
				oci_free_statement($stid);
				oci_close($conn);
				return $res_array;

	}
	/*++++++++++++++++++++++++ WIDGETS ++++++++++++++++++++++++*/

	public function widgets($plaza,$contrato,$depto,$area,$fil_check,$fecha,$fil_habilitado)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha_act = "";
		$and_fecha_act2 = "";
		$and_fecha_can = " AND can.fecha_cancelacion >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND can.fecha_cancelacion < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),0) ) ";
		$and_fecha_cancel = " and RCAN.FECHA_CANCELACION <= TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),0) ) ";

		#	$and_fecha_can = " AND can.fecha_cancelacion >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND can.fecha_cancelacion < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),-1) +1) ";
		#	$fecha_cancel =" AND RCAN.FECHA_CANCELACION < trunc(ADD_MONTHS(TRUNC(SYSDATE, 'MM'),-0) ) ";
		if ($fil_check == 'on'){

			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
				$and_fecha_act = " AND per.d_fecha_ingreso < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
				$and_fecha_act2 = " per.d_fecha_ingreso <= trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') )  AND ";
				$and_fecha_cancel = " and RCAN.FECHA_CANCELACION <= trunc(to_date('".substr($fecha, 11, 10)."','dd/mm/yyyy') )" ;
				$and_fecha_can = " AND can.fecha_cancelacion >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND can.fecha_cancelacion < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
			}
		}
		$and_habilitado =  " AND CAN.HABILITADO = 0 ";//OR CAN.HABILITADO IS NULL

		$and_habilitado2 = "  AND CAN.HABILITADO = 1";

		$and_plaza = "";
		switch ($plaza) {
		  	case 'CORPORATIVO': $and_plaza = " AND per.iid_plaza = 2 "; break;
		    case 'CÓRDOBA': $and_plaza = " AND per.iid_plaza = 3 "; break;
		    case 'MÉXICO': $and_plaza = " AND per.iid_plaza = 4 "; break;
		    case 'GOLFO': $and_plaza = " AND per.iid_plaza = 5 "; break;
		    case 'PENINSULA': $and_plaza = " AND per.iid_plaza = 6 "; break;
		    case 'PUEBLA': $and_plaza = " AND per.iid_plaza = 7 "; break;
		    case 'BAJIO': $and_plaza = " AND per.iid_plaza = 8 "; break;
		    case 'OCCIDENTE': $and_plaza = " AND per.iid_plaza = 17 "; break;
		    case 'NORESTE': $and_plaza = " AND per.iid_plaza = 18 "; break;
		    default: $and_plaza = ""; break;
		  }

		$and_contrato = "";
		if ( $contrato != 'ALL' ){
			$and_contrato = " AND con.s_tipo_contrato = ".$contrato." ";
		}

		$and_depto = "";
		if ( $depto != 'ALL' ){
			$and_depto = " AND con.iid_depto = ".$depto." ";
		}

		$and_area = "";
		if ( $area != 'ALL' ){
			$and_area = " AND con.iid_area = ".$area." ";
		}

		$sql = "SELECT
		(SELECT count(per.iid_empleado)  FROM no_personal per
         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
         LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
         AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO ".$and_fecha_cancel."
         WHERE ".$and_fecha_act2." RCAN.FECHA_CANCELACION IS NULL AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074) ".$and_plaza.$and_contrato.$and_depto.$and_area."
				 AND PER.IID_NUMNOMINA <> 2
		) AS activo,
		(SELECT COUNT(*)
						FROM rh_cancelacion_contrato can
						INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato
						INNER JOIN no_contrato con ON con.iid_contrato = per.iid_contrato AND con.iid_empleado = per.iid_empleado
						WHERE per.s_status = 0 AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5  ".$and_fecha_can.$and_plaza.$and_contrato.$and_depto.$and_area.$and_habilitado."
						AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
						AND CAN.N_MOTIVO_CANCELA NOT IN (1)
						AND PER.IID_NUMNOMINA <> 2
		) AS baja,
		(SELECT COUNT(*)
						FROM rh_cancelacion_contrato can
						INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato
						INNER JOIN no_contrato con ON con.iid_contrato = per.iid_contrato AND con.iid_empleado = per.iid_empleado
						WHERE per.s_status = 0 AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5  ".$and_fecha_can.$and_plaza.$and_contrato.$and_depto.$and_area.$and_habilitado2."
		 AND per.iid_empleado not in (2400)
	 	 AND PER.IID_NUMNOMINA <> 2 ) as baja_habilitado,
		 (SELECT COUNT(*)
 						FROM rh_cancelacion_contrato can
 						INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato
 						INNER JOIN no_contrato con ON con.iid_contrato = per.iid_contrato AND con.iid_empleado = per.iid_empleado
 						WHERE per.s_status = 0 AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5  ".$and_fecha_can.$and_plaza.$and_contrato.$and_depto.$and_area.$and_habilitado."
 						AND CAN.N_MOTIVO_CANCELA IN (1)
						AND PER.IID_NUMNOMINA <> 2
 		) AS BAJA_NO_CONTEMPLADO
						FROM DUAL";


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

 /*####################################WIDGETS ANUALES######################################################*/
 public function anualesWidgets($plaza){
	 $conn = conexion::conectar();
	 $res_array = array();
	 $in_plaza = "2,3,4,5,6,7,8,17,18";
	 switch ($plaza) {
			 case 'CORPORATIVO': $in_plaza = 2; break;
			 case 'CÓRDOBA': $in_plaza = 3; break;
			 case 'MÉXICO': $in_plaza = 4; break;
			 case 'GOLFO': $in_plaza = 5; break;
			 case 'PENINSULA': $in_plaza = 6; break;
			 case 'PUEBLA': $in_plaza = 7; break;
			 case 'BAJIO': $in_plaza = 8; break;
			 case 'OCCIDENTE': $in_plaza = 17; break;
			 case 'NORESTE': $in_plaza = 18; break;
			 default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
	 }
	 $sql = "SELECT COUNT(D.baja) as baja_anual, COUNT(D.alta) as alta_anual FROM
       ( SELECT per.iid_empleado as baja ,
         TO_NUMBER(TO_CHAR(NULL)) as alta
         FROM no_personal per
         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
         LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO  AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO
              WHERE per.iid_plaza IN (".$in_plaza.") AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)  AND RCAN.FECHA_CANCELACION IS NULL
							UNION SELECT TO_NUMBER(to_char(null)) as baja, CAN.IID_EMPLEADO alta
                    FROM rh_cancelacion_contrato can
                    INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado
                          AND per.iid_contrato = can.iid_contrato
                          AND per.iid_plaza NOT IN (".$in_plaza.")
                    INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                          AND con.iid_contrato = per.iid_contrato
                          WHERE per.s_status = 0 AND to_char(can.fecha_cancelacion, 'yyyy') = to_char(SYSDATE, 'YYYY')
                          AND can.fecha_cancelacion <= trunc( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),0) ) AND CAN.HABILITADO = 0
													AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
													AND CAN.N_MOTIVO_CANCELA NOT IN (1)
													AND PER.IID_NUMNOMINA <> 2
                          AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5 ) D ";

														#echo $sql; OR CAN.HABILITADO IS NULL
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

	/*++++++++++++++++++++++++ GRAFICA PERSONAL ACTIVO ++++++++++++++++++++++++*/
	public function grafica($plaza,$contrato,$depto,$area,$fil_check,$fecha,$fil_habilitado)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha_act = " ";
		$and_fecha_act2 = " ";
		$and_fecha_can = " AND can.fecha_cancelacion >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND can.fecha_cancelacion < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),-1) ) ";
		$fecha_cancel =" and RCAN.FECHA_CANCELACION <= TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),0) ) ";
		if ($fil_check == 'on'){

			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
				$and_fecha_act = " AND per.d_fecha_ingreso < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') )  ";
				$and_fecha_act2 = " per.d_fecha_ingreso < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') )  AND";
				$and_fecha_act22 = " (per.d_fecha_ingreso < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') )  AND
               								per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."', 'dd/mm/yyyy')))";
				$and_fecha_can = " AND can.fecha_cancelacion >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND can.fecha_cancelacion <= trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) ";
				$fecha_cancel =" AND RCAN.FECHA_CANCELACION <= trunc(to_date('".substr($fecha, 11, 10)."','dd/mm/yyyy') ) ";
			}
		}

		if ($fil_habilitado == 'on') {
			$and_habilitado = " AND CAN.HABILITADO = 1";
		}
		else {
			$and_habilitado = " AND CAN.HABILITADO = 0 ";// OR CAN.HABILITADO IS NULL
		}
		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
		  	case 'CORPORATIVO': $in_plaza = 2; break;
		    case 'CÓRDOBA': $in_plaza = 3; break;
		    case 'MÉXICO': $in_plaza = 4; break;
		    case 'GOLFO': $in_plaza = 5; break;
		    case 'PENINSULA': $in_plaza = 6; break;
		    case 'PUEBLA': $in_plaza = 7; break;
		    case 'BAJIO': $in_plaza = 8; break;
		    case 'OCCIDENTE': $in_plaza = 17; break;
		    case 'NORESTE': $in_plaza = 18; break;
		    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}

		$and_contrato = "";
		if ( $contrato != 'ALL' ){
			$and_contrato = " AND con.s_tipo_contrato = ".$contrato." ";
		}

		$and_depto = "";
		if ( $depto != 'ALL' ){
			$and_depto = " AND con.iid_depto = ".$depto." ";
		}

		$and_area = "";
		if ( $area != 'ALL' ){
			$and_area = " AND con.iid_area = ".$area." ";
		}

		$sql = "SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas
				,(
				SELECT COUNT(CAN.IID_EMPLEADO)
				FROM rh_cancelacion_contrato can
				INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza = pla.iid_plaza
				INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
				WHERE per.s_status = 0 AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5 ".$and_fecha_can.$and_contrato.$and_depto.$and_area.$and_habilitado."
							AND can.iid_empleado not in (1930, 2272, 2074)
							AND CAN.N_MOTIVO_CANCELA NOT IN (1)
							AND PER.IID_NUMNOMINA <> 2
				) AS baja
				,(
					SELECT count(per.iid_empleado)  FROM no_personal per
	         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
	         LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
	                                                 AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO ".$fecha_cancel."
	         WHERE ".$and_fecha_act2." RCAN.FECHA_CANCELACION IS NULL AND per.iid_plaza = pla.iid_plaza AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
					 AND PER.IID_NUMNOMINA <> 2
			 ) as activo,
        (SELECT count(per.iid_empleado)
          FROM no_personal per
         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                                   AND con.iid_contrato = per.iid_contrato
          LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO =
                                                          CON.IID_CONTRATO
                                                      AND RCAN.IID_EMPLEADO =
                                                          CON.IID_EMPLEADO
                                                      AND RCAN.FECHA_CANCELACION <=
                                                          trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') )
         WHERE ".$and_fecha_act22."
           AND RCAN.FECHA_CANCELACION IS NULL
           AND per.iid_plaza = pla.iid_plaza
           AND per.iid_empleado not in (209, 1, 2400, 1930, 2272, 2074)
					 AND PER.IID_NUMNOMINA <> 2
				 	 ) as activon
				FROM plaza pla
				WHERE pla.iid_plaza IN (".$in_plaza.") ORDER BY pla.iid_plaza";
				//echo $sql;
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);

				while (($row = oci_fetch_assoc($stid)) != false)
				{
					$res_array[]= $row;
				}

		#echo $sql;
				oci_free_statement($stid);
				oci_close($conn);


				return $res_array;

	}

// total pagos
	public function graficaMensual($plaza,$fecha,$fil_check,$fil_habilitado){
		if ($fil_check == 'on'){
			$andFecha = substr($fecha, 6,4);
			$and_fecha2 = $andFecha-1;
			$and_fecha3 = $andFecha-2;
			$and_fecha4 = $andFecha-3;
		}
		else{
			$andFecha = date("Y");
			$and_fecha2 = $andFecha -1;
		}

		if ($fil_habilitado == 'on') {
			$and_habilitado = " AND CAN.HABILITADO = 1";
		}
		else {
			$and_habilitado = " AND CAN.HABILITADO = 0  ";//OR CAN.HABILITADO IS NULL
		}

		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
		  	case 'CORPORATIVO': $in_plaza = 2; break;
		    case 'CÓRDOBA': $in_plaza = 3; break;
		    case 'MÉXICO': $in_plaza = 4; break;
		    case 'GOLFO': $in_plaza = 5; break;
		    case 'PENINSULA': $in_plaza = 6; break;
		    case 'PUEBLA': $in_plaza = 7; break;
		    case 'BAJIO': $in_plaza = 8; break;
		    case 'OCCIDENTE': $in_plaza = 17; break;
		    case 'NORESTE': $in_plaza = 18; break;
		    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql ="SELECT PLA.N_MES,
       				  PLA.MES,
                    ( SELECT COUNT(CAN.IID_EMPLEADO) AS EMPLEADO
                             FROM rh_cancelacion_contrato can
                     INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(".$in_plaza.")
                     INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
                     WHERE per.s_status = 0
										 			AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
                           AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '".$andFecha."'
													 AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
                           AND TO_CHAR(CAN.FECHA_CANCELACION, 'MM') = PLA.N_MES " .$and_habilitado."
													 AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)
													 AND PER.IID_NUMNOMINA <> 2
												 ) AS BAJA ,
                    ( SELECT COUNT(per.iid_empleado) AS BAJA
                             FROM no_personal per
                     INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                           AND con.iid_contrato = per.iid_contrato
										 LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
													 AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO AND RCAN.FECHA_CANCELACION <= LAST_DAY(to_date(PLA.N_MES||'/".$andFecha."','mm/yyyy') )
                     WHERE per.iid_plaza IN(".$in_plaza.")
                           AND (PER.d_fecha_ingreso <= LAST_DAY(TO_DATE(PLA.N_MES||'/".$andFecha."', 'mm/yyyy')))
													 AND RCAN.FECHA_CANCELACION IS NULL
													 AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
													 AND PER.IID_NUMNOMINA <> 2
												 ) as ACTIVO,
									 ( SELECT COUNT(CAN.IID_EMPLEADO) AS EMPLEADO
										 FROM rh_cancelacion_contrato can
									   INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(".$in_plaza.")
										 INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
										 WHERE per.s_status = 0
										 					 AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
												 			 AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
											  			 AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '".$and_fecha2."'
												 			 AND TO_CHAR(CAN.FECHA_CANCELACION, 'MM') = PLA.N_MES " .$and_habilitado."
															 AND PER.IID_NUMNOMINA <> 2
														 	 AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)) AS BAJA_ANTERIOR,
										( SELECT COUNT(per.iid_empleado) AS BAJA
										  FROM no_personal per
											INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
											LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
															AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO AND RCAN.FECHA_CANCELACION <= LAST_DAY(to_date(PLA.N_MES||'/".$and_fecha2."','mm/yyyy') )
										  WHERE per.iid_plaza IN(".$in_plaza.")
															AND (PER.d_fecha_ingreso <= LAST_DAY(TO_DATE(PLA.N_MES||'/".$and_fecha2."', 'mm/yyyy')))
															AND RCAN.FECHA_CANCELACION IS NULL
															AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
															AND PER.IID_NUMNOMINA <> 2
											) as ACTIVO_ANTERIOR,
											( SELECT COUNT(CAN.IID_EMPLEADO) AS EMPLEADO
	 										 FROM rh_cancelacion_contrato can
	 									   INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(".$in_plaza.")
	 										 INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
	 										 WHERE per.s_status = 0
	 										 					 AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
	 												 			 AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
	 											  			 AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '".$and_fecha3."'
	 												 			 AND TO_CHAR(CAN.FECHA_CANCELACION, 'MM') = PLA.N_MES " .$and_habilitado."
																 AND PER.IID_NUMNOMINA <> 2
	 														 	 AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)) AS BAJA_ANTERIOR2,
	 										( SELECT COUNT(per.iid_empleado) AS BAJA
	 										  FROM no_personal per
	 											INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
	 											LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
	 															AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO AND RCAN.FECHA_CANCELACION <= LAST_DAY(to_date(PLA.N_MES||'/".$and_fecha3."','mm/yyyy') )
	 										  WHERE per.iid_plaza IN(".$in_plaza.")
	 															AND (PER.d_fecha_ingreso <= LAST_DAY(TO_DATE(PLA.N_MES||'/".$and_fecha3."', 'mm/yyyy')))
	 															AND RCAN.FECHA_CANCELACION IS NULL
	 															AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
																AND PER.IID_NUMNOMINA <> 2
	 											) as ACTIVO_ANTERIOR2,
												( SELECT COUNT(CAN.IID_EMPLEADO) AS EMPLEADO
		 										 FROM rh_cancelacion_contrato can
		 									   INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(".$in_plaza.")
		 										 INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
		 										 WHERE per.s_status = 0
		 										 					 AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
		 												 			 AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
		 											  			 AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '".$and_fecha4."'
		 												 			 AND TO_CHAR(CAN.FECHA_CANCELACION, 'MM') = PLA.N_MES " .$and_habilitado."
																	 AND PER.IID_NUMNOMINA <> 2
		 														 	 AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)) AS BAJA_ANTERIOR3,
		 										( SELECT COUNT(per.iid_empleado) AS BAJA
		 										  FROM no_personal per
		 											INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
		 											LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
		 															AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO AND RCAN.FECHA_CANCELACION <= LAST_DAY(to_date(PLA.N_MES||'/".$and_fecha4."','mm/yyyy') )
		 										  WHERE per.iid_plaza IN(".$in_plaza.")
		 															AND (PER.d_fecha_ingreso <= LAST_DAY(TO_DATE(PLA.N_MES||'/".$and_fecha4."', 'mm/yyyy')))
		 															AND RCAN.FECHA_CANCELACION IS NULL
		 															AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
																	AND PER.IID_NUMNOMINA <> 2
		 											) as ACTIVO_ANTERIOR3
													 FROM RH_MESES_GRAFICAS pla
                           GROUP BY PLA.N_MES, PLA.MES
													 ORDER BY pla.N_MES";//315
			  #echo $sql;
				$stid = oci_parse($conn,$sql);
				oci_execute($stid);
				while (($row = oci_fetch_assoc($stid))!= false) {
					$res_array[] = $row;
				}
				oci_free_statement($stid);
				oci_close($conn);
				return $res_array;
	}


	/*PORCENTAJE ALMACEN*/
	public function portAlmacen($plaza,$anio,$fil_check,$fil_habilitado, $tipo){

		if ($fil_habilitado == 'on') {
			$and_habilitado = " AND CAN.HABILITADO = 1";
		}
		else {
			$and_habilitado = " AND CAN.HABILITADO = 0 ";//OR CAN.HABILITADO IS NULL
		}

		if ($tipo == 1 ) {
			$and_tipo = " ";
		}elseif ($tipo == 2 ) {
			$and_tipo = " AND con.iid_puesto in (180, 167, 213, 107, 210, 201, 211, 112, 111, 115,
                                          32, 135, 138, 173, 164, 113, 161, 38, 109, 110, 108,
                                          73, 77, 114, 63, 288, 302, 287, 267, 291, 303, 281,
                                          297, 254, 272, 176, 293, 179, 256, 273, 224, 306, 309,
                                          305, 271, 342, 299, 125, 119, 124, 250, 126, 120, 116,
                                          121, 35, 122, 118, 123, 117, 253, 251, 341, 331, 255, 300,
                                          325, 280, 337, 259, 257, 301, 264, 200, 67, 66 , 208, 4,
                                          203, 220, 245, 311, 310, 105, 33, 49, 106, 252, 59, 219,
                                          284, 304, 313, 202, 162, 163, 104, 56, 58, 277, 23, 239,
                                          241, 248, 78, 157, 79, 50, 158, 199, 80, 39, 223, 227, 40,
                                          265, 283, 151, 262, 5, 129, 128, 279, 154, 142, 229, 347,
                                          346, 330, 315, 339, 298, 175, 275, 274, 332, 296, 34, 28,
                                          96, 97, 95, 94, 98, 93, 198, 238, 233, 334, 320, 319, 314,
                                          153, 134, 317, 177, 243, 318, 295, 350, 260, 290, 269, 326,
                                          244, 316, 263, 338, 308, 289) ";
		}elseif ($tipo == 3 ) {
			$and_tipo = " AND CON.iid_puesto IN (170, 182, 172, 19, 294, 292, 324, 268, 165, 41, 168, 51, 44, 72,
				 																	 52, 43, 166, 258, 71, 195, 207, 197, 205, 178, 209, 214, 237, 88,
																					 89, 60, 145, 226, 92, 90, 148, 91, 312, 68, 70, 54, 82, 83, 9, 246, 155, 192, 18, 321, 141, 232, 184, 327, 12, 37, 348, 152, 278, 87,
																					 42, 86, 84, 85, 349, 188, 150, 20, 25, 22, 55, 156, 21, 76, 212, 29, 74, 24, 344, 247, 240, 217, 2, 235, 1, 236, 276, 204, 216, 206,
																					 234, 335, 270, 100, 102, 14, 99, 101, 103, 169, 322, 333, 	343, 3, 130, 242, 48, 187, 340, 261, 323, 351, 222, 225, 15, 215, 228, 11,
																					 81, 46, 16,  27, 36, 7, 47, 159, 285, 307, 174, 10, 149, 282, 143,  13, 75, 6, 218, 185, 57, 230, 231, 133, 196, 345,  31) ";
		}

		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
		  	case 'CORPORATIVO': $in_plaza = 2; break;
		    case 'CÓRDOBA': $in_plaza = 3; break;
		    case 'MÉXICO': $in_plaza = 4; break;
		    case 'GOLFO': $in_plaza = 5; break;
		    case 'PENINSULA': $in_plaza = 6; break;
		    case 'PUEBLA': $in_plaza = 7; break;
		    case 'BAJIO': $in_plaza = 8; break;
		    case 'OCCIDENTE': $in_plaza = 17; break;
		    case 'NORESTE': $in_plaza = 18; break;
		    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}

		if ($anio < 2020) {
			$CANCELACION_TIPO = "";
		}else {
			$CANCELACION_TIPO = "AND CAN.N_MOTIVO_CANCELA NOT IN (1 )";
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql ="SELECT  SUM((X.BAJA *100)/X.ACTIVO) AS PORCENTAJE, X.ANIO  FROM (SELECT PLA.N_MES,
				       PLA.MES,
				       (SELECT COUNT(CAN.IID_EMPLEADO) AS EMPLEADO
				          FROM rh_cancelacion_contrato can
				         INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado
				                                   AND per.iid_contrato = can.iid_contrato
				                                   AND per.iid_plaza IN
				                                       (2, 3, 4, 5, 6, 7, 8, 17, 18)
				         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
				                                   AND con.iid_contrato = per.iid_contrato
				         WHERE per.s_status = 0
				           AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
				           AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '$anio'
				           AND TO_CHAR(CAN.FECHA_CANCELACION, 'MM') = PLA.N_MES
				           AND CAN.HABILITADO = 0
									 AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
									 AND PER.IID_NUMNOMINA <> 2
									 $CANCELACION_TIPO
								 	 $and_tipo) AS BAJA,
				       (SELECT COUNT(per.iid_empleado) AS BAJA
				          FROM no_personal per
				         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
				                                   AND con.iid_contrato = per.iid_contrato
				          LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO =
				                                                          CON.IID_CONTRATO
				                                                      AND RCAN.IID_EMPLEADO =
				                                                          CON.IID_EMPLEADO
				                                                      AND RCAN.FECHA_CANCELACION <=
				                                                          LAST_DAY(to_date(PLA.N_MES ||
				                                                                           '/$anio',
				                                                                           'mm/yyyy'))
				         WHERE per.iid_plaza IN (2, 3, 4, 5, 6, 7, 8, 17, 18)
				           AND (PER.d_fecha_ingreso <=
				               LAST_DAY(TO_DATE(PLA.N_MES || '/$anio', 'mm/yyyy')))
				           AND RCAN.FECHA_CANCELACION IS NULL
				           AND per.iid_empleado not in (209, 1, 2400, 1930, 2272, 2074)
									 AND PER.IID_NUMNOMINA <> 2
								 		) as ACTIVO,
				           '$anio' AS ANIO
				  FROM RH_MESES_GRAFICAS pla
				 GROUP BY PLA.N_MES, PLA.MES
				 ORDER BY pla.N_MES) X
				 GROUP BY X.ANIO ";//315
			 # echo $sql;
				$stid = oci_parse($conn,$sql);
				oci_execute($stid);
				while (($row = oci_fetch_assoc($stid))!= false) {
					$res_array[] = $row;
				}
				oci_free_statement($stid);
				oci_close($conn);
				return $res_array;
	}

	/*PORCENTAJE ALMACEN*/
	public function portAlmacen2($plaza,$anio,$fil_check,$fil_habilitado, $tipo, $mes){

		if ($fil_habilitado == 'on') {
			$and_habilitado = " AND CAN.HABILITADO = 1";
		}
		else {
			$and_habilitado = " AND CAN.HABILITADO = 0";//
		}

		if ($tipo == 1 ) {
			$and_tipo = " ";
		}elseif ($tipo == 2 ) {
			$and_tipo = " AND con.iid_puesto in (180, 167, 213, 107, 210, 201, 211, 112, 111, 115,
                                          32, 135, 138, 173, 164, 113, 161, 38, 109, 110, 108,
                                          73, 77, 114, 63, 288, 302, 287, 267, 291, 303, 281,
                                          297, 254, 272, 176, 293, 179, 256, 273, 224, 306, 309,
                                          305, 271, 342, 299, 125, 119, 124, 250, 126, 120, 116,
                                          121, 35, 122, 118, 123, 117, 253, 251, 341, 331, 255, 300,
                                          325, 280, 337, 259, 257, 301, 264, 200, 67, 66 , 208, 4,
                                          203, 220, 245, 311, 310, 105, 33, 49, 106, 252, 59, 219,
                                          284, 304, 313, 202, 162, 163, 104, 56, 58, 277, 23, 239,
                                          241, 248, 78, 157, 79, 50, 158, 199, 80, 39, 223, 227, 40,
                                          265, 283, 151, 262, 5, 129, 128, 279, 154, 142, 229, 347,
                                          346, 330, 315, 339, 298, 175, 275, 274, 332, 296, 34, 28,
                                          96, 97, 95, 94, 98, 93, 198, 238, 233, 334, 320, 319, 314,
                                          153, 134, 317, 177, 243, 318, 295, 350, 260, 290, 269, 326,
                                          244, 316, 263, 338, 308, 289) ";
		}elseif ($tipo == 3 ) {
			$and_tipo = " AND CON.iid_puesto IN (170, 182, 172, 19, 294, 292, 324, 268, 165, 41, 168, 51, 44, 72,
				 																	 52, 43, 166, 258, 71, 195, 207, 197, 205, 178, 209, 214, 237, 88,
																					 89, 60, 145, 226, 92, 90, 148, 91, 312, 68, 70, 54, 82, 83, 9, 246, 155, 192, 18, 321, 141, 232, 184, 327, 12, 37, 348, 152, 278, 87,
																					 42, 86, 84, 85, 349, 188, 150, 20, 25, 22, 55, 156, 21, 76, 212, 29, 74, 24, 344, 247, 240, 217, 2, 235, 1, 236, 276, 204, 216, 206,
																					 234, 335, 270, 100, 102, 14, 99, 101, 103, 169, 322, 333, 	343, 3, 130, 242, 48, 187, 340, 261, 323, 351, 222, 225, 15, 215, 228, 11,
																					 81, 46, 16,  27, 36, 7, 47, 159, 285, 307, 174, 10, 149, 282, 143,  13, 75, 6, 218, 185, 57, 230, 231, 133, 196, 345,  31) ";
		}

		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
		  	case 'CORPORATIVO': $in_plaza = 2; break;
		    case 'CÓRDOBA': $in_plaza = 3; break;
		    case 'MÉXICO': $in_plaza = 4; break;
		    case 'GOLFO': $in_plaza = 5; break;
		    case 'PENINSULA': $in_plaza = 6; break;
		    case 'PUEBLA': $in_plaza = 7; break;
		    case 'BAJIO': $in_plaza = 8; break;
		    case 'OCCIDENTE': $in_plaza = 17; break;
		    case 'NORESTE': $in_plaza = 18; break;
		    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}

		if ($anio < 2020) {
			$CANCELACION_TIPO = "";
		}else {
			$CANCELACION_TIPO = "AND CAN.N_MOTIVO_CANCELA NOT IN (1 )";
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql ="SELECT  SUM((X.BAJA *100)/X.ACTIVO) AS PORCENTAJE, X.ANIO  FROM (SELECT PLA.N_MES,
				       PLA.MES,
				       (SELECT COUNT(CAN.IID_EMPLEADO) AS EMPLEADO
				          FROM rh_cancelacion_contrato can
				         INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado
				                                   AND per.iid_contrato = can.iid_contrato
				                                   AND per.iid_plaza IN
				                                       (2, 3, 4, 5, 6, 7, 8, 17, 18)
				         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
				                                   AND con.iid_contrato = per.iid_contrato
				         WHERE per.s_status = 0
				           AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
				           AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '$anio'
				           AND TO_CHAR(CAN.FECHA_CANCELACION, 'MM') = PLA.N_MES
				           AND CAN.HABILITADO = 0
									 AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
									 AND PER.IID_NUMNOMINA <> 2
									 $CANCELACION_TIPO
								 	 $and_tipo) AS BAJA,
				       (SELECT COUNT(per.iid_empleado) AS BAJA
				          FROM no_personal per
				         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
				                                   AND con.iid_contrato = per.iid_contrato
				          LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO =
				                                                          CON.IID_CONTRATO
				                                                      AND RCAN.IID_EMPLEADO =
				                                                          CON.IID_EMPLEADO
				                                                      AND RCAN.FECHA_CANCELACION <=
				                                                          LAST_DAY(to_date(PLA.N_MES ||
				                                                                           '/$anio',
				                                                                           'mm/yyyy'))
				         WHERE per.iid_plaza IN (2, 3, 4, 5, 6, 7, 8, 17, 18)
				           AND (PER.d_fecha_ingreso <=
				               LAST_DAY(TO_DATE(PLA.N_MES || '/$anio', 'mm/yyyy')))
				           AND RCAN.FECHA_CANCELACION IS NULL
				           AND per.iid_empleado not in (209, 1, 2400, 1930, 2272, 2074)
									 AND PER.IID_NUMNOMINA <> 2
								 		) as ACTIVO,
				           '$anio' AS ANIO
				  FROM RH_MESES_GRAFICAS pla
					WHERE PLA.N_MES <= '$mes'
				 GROUP BY PLA.N_MES, PLA.MES
				 ORDER BY pla.N_MES) X
				 GROUP BY X.ANIO ";//315
				 #echo $sql;
				$stid = oci_parse($conn,$sql);
				oci_execute($stid);
				while (($row = oci_fetch_assoc($stid))!= false) {
					$res_array[] = $row;
				}
				oci_free_statement($stid);
				oci_close($conn);
				return $res_array;
	}

	public function grafica_PerAlmacen($plaza,$contrato,$depto,$area,$fil_check,$fecha,$fil_habilitado)
	{
	  $conn = conexion::conectar();
	  $res_array = array();

	  $and_fecha_act = " ";
	  $and_fecha_act2 = " ";
	  $and_fecha_can = " AND can.fecha_cancelacion >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND can.fecha_cancelacion < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),-1) ) ";
	  $fecha_cancel =" and RCAN.FECHA_CANCELACION <= TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),0) ) ";
	  if ($fil_check == 'on'){

	    if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
	      //AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
	      $and_fecha_act = " AND per.d_fecha_ingreso < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') )  ";
	      $and_fecha_act2 = " per.d_fecha_ingreso < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') )  AND";
	      $and_fecha_can = " AND can.fecha_cancelacion >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND can.fecha_cancelacion <= trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) ";
	      $fecha_cancel =" AND RCAN.FECHA_CANCELACION <= trunc(to_date('".substr($fecha, 11, 10)."','dd/mm/yyyy') ) ";
	    }
	  }

	  if ($fil_habilitado == 'on') {
	    $and_habilitado = " AND CAN.HABILITADO = 1";
	  }
	  else {
	    $and_habilitado = " AND CAN.HABILITADO = 0  ";//OR CAN.HABILITADO IS NULL
	  }
	  $in_plaza = "2,3,4,5,6,7,8,17,18";
	  switch ($plaza) {
	      case 'CORPORATIVO': $in_plaza = 2; break;
	      case 'CÓRDOBA': $in_plaza = 3; break;
	      case 'MÉXICO': $in_plaza = 4; break;
	      case 'GOLFO': $in_plaza = 5; break;
	      case 'PENINSULA': $in_plaza = 6; break;
	      case 'PUEBLA': $in_plaza = 7; break;
	      case 'BAJIO': $in_plaza = 8; break;
	      case 'OCCIDENTE': $in_plaza = 17; break;
	      case 'NORESTE': $in_plaza = 18; break;
	      default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
	  }

	  $and_contrato = "";
	  if ( $contrato != 'ALL' ){
	    $and_contrato = " AND con.s_tipo_contrato = ".$contrato." ";
	  }

	  $and_depto = "";
	  if ( $depto != 'ALL' ){
	    $and_depto = " AND con.iid_depto = ".$depto." ";
	  }

	  $and_area = "";
	  if ( $area != 'ALL' ){
	    $and_area = " AND con.iid_area = ".$area." ";
	  }

	  $sql = "SELECT ALM.IID_PLAZA,
	         ALM.IID_ALMACEN,
	         REPLACE(ALM.V_NOMBRE, '''', '') AS ALMACEN,
	         ( SELECT COUNT(CAN.IID_EMPLEADO)
	                  FROM rh_cancelacion_contrato can
	                  INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado
	                                             AND per.iid_contrato = can.iid_contrato
	                                             AND per.iid_plaza = ALM.iid_plaza
	                  INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
	                                             AND con.iid_contrato = per.iid_contrato
	                  INNER JOIN RH_ALMACEN_EMP RHMP ON RHMP.NID_EMPLEADO = CAN.IID_EMPLEADO
	                                                    AND RHMP.NID_ALMACEN = ALM.IID_ALMACEN
	                                                    AND RHMP.N_BASE = 1
	                                                    AND RHMP.NID_ALMACEN <> 0
	                  WHERE per.s_status = 0
													AND PER.IID_PLAZA IN(".$in_plaza.")
	                        AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
													AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
													AND CAN.N_MOTIVO_CANCELA NOT IN (1 )
													AND PER.IID_NUMNOMINA <> 2
	                        ".$and_fecha_can.$and_contrato.$and_depto.$and_area.$and_habilitado." ) AS bajaxalm ,
	          ( SELECT count(per.iid_empleado)
	                   FROM no_personal per
	                   INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
	                                              AND con.iid_contrato = per.iid_contrato
	                   LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
	                                                                AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO
	                                                                ".$fecha_cancel."
	                   INNER JOIN RH_ALMACEN_EMP RHEMP ON RHEMP.NID_EMPLEADO = PER.IID_EMPLEADO
	                                                   AND RHEMP.NID_ALMACEN = ALM.IID_ALMACEN
	                                                   AND RHEMP.N_BASE = 1
	                                                   AND RHEMP.NID_ALMACEN <> 0
	                   WHERE per.iid_plaza = ALM.iid_plaza AND
										 			 ".$and_fecha_act2."
	                         RCAN.FECHA_CANCELACION IS NULL
													 AND PER.IID_PLAZA IN (".$in_plaza.")
	                         AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
												 	 AND PER.IID_NUMNOMINA <> 2  ) as ACTIVO
	  FROM ALMACEN ALM
	  WHERE ALM.iid_plaza IN (3)
	  ORDER BY ALM.iid_plaza";
	  #echo $sql;
	  $stid = oci_parse($conn, $sql);
	  oci_execute($stid);

	  while (($row = oci_fetch_assoc($stid)) != false)
	  {
	    $res_array[]= $row;
	  }

	#echo $sql;
	  oci_free_statement($stid);
	  oci_close($conn);

	//	echo $sql;
	  return $res_array;

	}

	/*++++++++++++++++++++++++ SQL DINAMICO FROM DUAL ++++++++++++++++++++++++*/
	public function dual($sql)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = $sql;

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
	public function tablaActivos($plaza,$contrato,$depto,$area,$fil_check,$fecha)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha = "";
		$and_fecha2 = "";
		if ($fil_check == 'on'){
			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
				$and_fecha = " AND per.d_fecha_ingreso < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
				$and_fecha2 = " AND RCAN.FECHA_CANCELACION <= trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') )";
			}
		}

		$and_plaza = "";
		if($plaza != 'ALL'){
			$and_plaza = " AND REPLACE(pla.v_razon_social, ' (ARGO)') = '".$plaza."' ";
		}

		$and_contrato = "";
		if ( $contrato != 'ALL' ){
			$and_contrato = " AND con.s_tipo_contrato = ".$contrato." ";
		}

		$and_depto = "";
		if ( $depto != 'ALL' ){
			$and_depto = " AND con.iid_depto = ".$depto." ";
		}

		$and_area = "";
		if ( $area != 'ALL' ){
			$and_area = " AND con.iid_area = ".$area." ";
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
				WHERE RCAN.FECHA_CANCELACION IS NULL ".$and_plaza.$and_contrato.$and_depto.$and_area.$and_fecha." AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074) AND PER.IID_NUMNOMINA <> 2 ";
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


	/*++++++++++++++++++++++++ SQL TABLA DETALLE BAJA ++++++++++++++++++++++++*/
	public function tablaBaja($plaza,$contrato,$depto,$area,$fil_check,$fecha,$fil_habilitado)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha = " AND can.fecha_cancelacion >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),-1) ) AND can.fecha_cancelacion < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),-1) ) +1 ";
		if ($fil_check == 'on'){
			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				$and_fecha = " AND can.fecha_cancelacion >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND can.fecha_cancelacion < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
			}
		}

		$and_plaza = "";
		if($plaza != 'ALL'){
			$and_plaza = " AND REPLACE(pla.v_razon_social, ' (ARGO)') = '".$plaza."' ";
		}

		$and_contrato = "";
		if ( $contrato != 'ALL' ){
			$and_contrato = " AND con.s_tipo_contrato = ".$contrato." ";
		}

		$and_depto = "";
		if ( $depto != 'ALL' ){
			$and_depto = " AND con.iid_depto = ".$depto." ";
		}

		$and_area = "";
		if ( $area != 'ALL' ){
			$and_area = " AND con.iid_area = ".$area." ";
		}

		if ($fil_habilitado == 'on') {
				$and_habilitado = " AND CAN.HABILITADO = 1";
		}
		else {
		 		$and_habilitado = " 	AND CAN.HABILITADO = 0  ";//OR CAN.HABILITADO IS NULL
		}
		$sql = "SELECT per.iid_empleado, per.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas, per.v_nombre||' '||per.v_ape_pat||' '||per.v_ape_mat AS nombre, per.v_ubi_1 AS lugar_trabajo
				,per.v_sexo,per.v_imss, per.v_rfc, per.v_curp, per.n_edad, per.s_status, per.iid_contrato, TO_CHAR(can.fecha_cancelacion, 'DD/MM/YYYY') fecha_cancelacion, per.nid_solicitud, per.c_salario_mensual, per.i_antiguedad
				,con.iid_puesto, pue.v_descripcion AS puesto, con.iid_depto, dep.v_descripcion AS depto, con.s_tipo_contrato, con.d_fec_inicio, con.d_fec_final, con.iid_area, ar.v_descripcion AS area, TO_CHAR(PER.D_FECHA_INGRESO, 'DD/MM/YYYY') AS INGRESO
				FROM no_personal per
				INNER JOIN rh_cancelacion_contrato can ON can.iid_contrato = per.iid_contrato AND can.iid_empleado = per.iid_empleado
				INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
				INNER JOIN plaza pla ON pla.iid_plaza = per.iid_plaza
				INNER JOIN rh_puestos pue ON pue.iid_puesto = con.iid_puesto
				INNER JOIN rh_cat_depto dep ON dep.iid_depto = con.iid_depto
				LEFT JOIN rh_cat_areas ar ON ar.iid_area = con.iid_area AND ar.iid_depto = con.iid_depto
				WHERE per.s_status = 0 AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
				AND CAN.N_MOTIVO_CANCELA NOT IN (1 )
				AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5 ".$and_plaza.$and_contrato.$and_depto.$and_area.$and_fecha.$and_habilitado." AND PER.IID_NUMNOMINA <> 2 ";


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


	/*++++++++++++++++++++++++ SQL FILTROS ++++++++++++++++++++++++*/
	public function filtros($option,$depto)
	{
			$conn = conexion::conectar();
			$res_array = array();

			switch ($option) {
				case '1':
					$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (2,3,4,5,6,7,8,17,18) ";
					break;
				case '2':
					$sql = "SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep";
					break;
				case '3':
					$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
					break;
			}

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


	function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}


	/**************************************GRAFICA DE PIE ********************************************************/
	public function grafica_Paste($plaza,$contrato,$depto,$area,$fil_check,$fecha,$fil_habilitado){
		if ($fil_check == 'on'){
			$andFecha = substr($fecha, 6,4);
		}
		else{
			$andFecha = date("Y");
		}

		if ($fil_habilitado == 'on') {
			$and_habilitado = " AND CAN.HABILITADO = 1";
		}
		else {
			$and_habilitado = " AND CAN.HABILITADO = 0 "; // OR CAN.HABILITADO IS NULL
		}

		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
				case 'CORPORATIVO': $in_plaza = 2; break;
				case 'CÓRDOBA': $in_plaza = 3; break;
				case 'MÉXICO': $in_plaza = 4; break;
				case 'GOLFO': $in_plaza = 5; break;
				case 'PENINSULA': $in_plaza = 6; break;
				case 'PUEBLA': $in_plaza = 7; break;
				case 'BAJIO': $in_plaza = 8; break;
				case 'OCCIDENTE': $in_plaza = 17; break;
				case 'NORESTE': $in_plaza = 18; break;
				default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql ="SELECT PLA.N_MES,
						PLA.MES,
										( SELECT COUNT(CAN.IID_EMPLEADO) AS EMPLEADO
														 FROM rh_cancelacion_contrato can
										 INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(".$in_plaza.")
										 INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
										 WHERE per.s_status = 0
										 			AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
													AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
													 AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '".$andFecha."'
													 AND TO_CHAR(CAN.FECHA_CANCELACION, 'MM') = PLA.N_MES " .$and_habilitado."
												 	 AND PER.IID_NUMNOMINA <> 2 ) AS BAJA ,
										( SELECT COUNT(per.iid_empleado) AS BAJA
														 FROM no_personal per
										 INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
													 AND con.iid_contrato = per.iid_contrato
										 LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
													 AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO AND RCAN.FECHA_CANCELACION <= LAST_DAY(to_date(PLA.N_MES||'/".$andFecha."','mm/yyyy') )
										 WHERE per.iid_plaza IN(".$in_plaza.")
													 AND (PER.d_fecha_ingreso <= LAST_DAY(TO_DATE(PLA.N_MES||'/".$andFecha."', 'mm/yyyy')))
													 AND RCAN.FECHA_CANCELACION IS NULL
													 AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
													 AND PER.IID_NUMNOMINA <> 2
												 ) as ACTIVO
													 FROM RH_MESES_GRAFICAS pla
													 GROUP BY PLA.N_MES, PLA.MES
													 ORDER BY pla.N_MES";//315
			//echo $sql;
				$stid = oci_parse($conn,$sql);
				oci_execute($stid);
				while (($row = oci_fetch_assoc($stid))!= false) {
					$res_array[] = $row;
				}
				oci_free_statement($stid);
				oci_close($conn);
				return $res_array;
  }


}
