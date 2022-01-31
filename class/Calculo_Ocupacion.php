<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class Calculo_Ocupacion
{
	/*++++++++++++++++++++++++ GRAFICA PERSONAL ACTIVO ++++++++++++++++++++++++*/
	public function grafica($plaza,$fecha,$almacen,$fil_check,$cliente)
	{
		$conn = conexion::conectar();
		$res_array = array();

		//CODIGO FECHAS SELECCIONAR # SEMANA :V SE MAMO
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;

		$mes = date("m");
		$mes2 = date("m")-1;

		$anio = date("Y");
		$anio2 = date("Y");

		//echo $no_semana." ".$no_semana_inf;
		if ($almacen == 'ALL') {
			$prueba_almacen = '';
			$prueba_capacidad = '';
		}
		else {
			$prueba_almacen = " AND PRU.IID_ALMACEN =".$almacen." ";
			$prueba_capacidad = "  AND t.IID_ALMACEN = ".$almacen." ";
		}

		if ($cliente == 'ALL') {
			$andcliente = ' ';
		}else {
			$andcliente = " AND T.IID_CLIENTE = ".$cliente. " ";
		}


			if ( $this->validateDate(substr($fecha,0,10)) ){
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));
			#	echo $no_semana_inf;

				//echo $no_semana;
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

		$sql = "SELECT P.MES,
       P.N_MES,
       NVL(SUM((T.PORCENTAJE) /
               (SELECT COUNT(DISTINCT S.IID_ALMACEN)
                  FROM OP_IN_PORCENTAJE_OCUPACION S
                 WHERE S.IID_PLAZA = T.IID_PLAZA
                   AND S.ANIO = T.ANIO
                   AND S.SEMANA = T.SEMANA
								 	 AND S.IID_ALMACEN = T.IID_ALMACEN)),
           0) + NVL(SUM((T.PORCENTAJE_RACKS) /
                        (SELECT COUNT(DISTINCT S.IID_ALMACEN)
                           FROM OP_IN_PORCENTAJE_OCUPACION S
                          WHERE S.IID_PLAZA = T.IID_PLAZA
                            AND S.ANIO = T.ANIO
                            AND S.SEMANA = T.SEMANA
														AND S.IID_ALMACEN = T.IID_ALMACEN)),
                    0) AS RACK_PORCENTAJE
				  FROM OP_IN_PORCENTAJE_OCUPACION t
				 INNER JOIN RH_MESES_GRAFICAS P ON P.N_MES = T.MES
				 WHERE T.ANIO = $anio
				   AND T.SEMANA = (SELECT MAX(PRU2.SEMANA)
														 FROM OP_IN_PORCENTAJE_OCUPACION PRU2
														WHERE PRU2.ANIO = $anio
															AND t.MES = PRU2.MES
															AND PRU2.IID_PLAZA = t.IID_PLAZA)
				  $prueba_capacidad
					$andcliente
				 GROUP BY P.MES, P.N_MES
				 ORDER BY P.N_MES";

				 #echo $sql;
				 /**/

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

	/****************************GRAFICA SEMANA*******************************/
	public function graficaSemanal($plaza,$fecha,$almacen,$fil_check,$cliente)
	{
		$conn = conexion::conectar();
		$res_array = array();

		//CODIGO FECHAS SELECCIONAR # SEMANA :V SE MAMO
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;

		$mes = date("m");
		$mes2 = date("m")-1;

		$anio = date("Y");
		$anio2 = date("Y");

		//echo $no_semana." ".$no_semana_inf;
		if ($almacen == 'ALL') {
			$prueba_almacen = '';
			$prueba_capacidad = '';
			$almacenfrom = ' ';
		}
		else {
			$prueba_almacen = " AND PRU.IID_ALMACEN =".$almacen." ";
			$prueba_capacidad = "  AND T.IID_ALMACEN = ".$almacen." ";
			$almacenfrom = " AND S.IID_ALMACEN = T.IID_ALMACEN ";
		}

		if ($fil_check == 'on'){
			//if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

				$dia2 = substr($fecha, 11,2);
				$mes2 = substr($fecha, 14,2);
				$anio2 = substr($fecha,17,4);
				$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
			//}
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

		if ($cliente == 'ALL') {
			$andcliente = " ";
		}else {
			$andcliente = " AND T.IID_CLIENTE = ".$cliente." ";
		}

		$sql = "SELECT P.NO_SEMANA AS SEMANA,
       NVL(SUM((T.PORCENTAJE) /
               (SELECT COUNT(DISTINCT S.IID_ALMACEN)
                  FROM OP_IN_PORCENTAJE_OCUPACION S
                 WHERE S.IID_PLAZA = T.IID_PLAZA
                   AND S.ANIO = T.ANIO
									 $almacenfrom
                   AND S.SEMANA = T.SEMANA)),
           0) + NVL(SUM((T.PORCENTAJE_RACKS) /
                        (SELECT COUNT(DISTINCT S.IID_ALMACEN)
                           FROM OP_IN_PORCENTAJE_OCUPACION S
                          WHERE S.IID_PLAZA = T.IID_PLAZA
                            AND S.ANIO = T.ANIO
														$almacenfrom
                            AND S.SEMANA = T.SEMANA)),
                    0) AS RACK_PORCENTAJE
				  FROM OP_IN_PORCENTAJE_OCUPACION t
				 INNER JOIN OP_NO_SEMANAS P ON P.NO_SEMANA = T.SEMANA
				 WHERE T.ANIO = $anio
				 AND T.IID_PLAZA in ($in_plaza)
				 $andcliente
				 $prueba_capacidad
				 GROUP BY P.NO_SEMANA
 			 	 ORDER BY P.NO_SEMANA";

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
	/*++++++++++++++++++++++++ GRAFICA PERSONAL ACTIVO ++++++++++++++++++++++++*/
	public function graficaAlmacen($plaza,$fecha,$fil_check,$cliente)
	{
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;


		$mes = date("m")-1;
		$mes2 = date("m");

		$anio = date("Y");
		$anio2 = date("Y");

		#	echo $mes." ".$mes2;
				#echo substr($fecha,0,10)."    ".substr($fecha,11,10);
			if ( $this->validateDate(substr($fecha,0,10)) ){
				//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));
				//echo $no_semana. " ". $no_semana_inf;
			}
		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza){
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

		if ($cliente == 'ALL') {
			$andcliente = " ";
		}else {
			$andcliente = " AND T.IID_CLIENTE = ".$cliente." ";
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql ="SELECT P.IID_ALMACEN,
       P.V_NOMBRE AS ALMACEN,
       NVL(SUM((T.PORCENTAJE) /
               (SELECT COUNT(DISTINCT S.IID_ALMACEN)
                  FROM OP_IN_PORCENTAJE_OCUPACION S
                 WHERE S.IID_PLAZA = T.IID_PLAZA
                   AND S.ANIO = T.ANIO
                   AND S.SEMANA = T.SEMANA
								 	 AND S.IID_ALMACEN = P.IID_ALMACEN)),
           0) + NVL(SUM((T.PORCENTAJE_RACKS) /
                        (SELECT COUNT(DISTINCT S.IID_ALMACEN)
                           FROM OP_IN_PORCENTAJE_OCUPACION S
                          WHERE S.IID_PLAZA = T.IID_PLAZA
                            AND S.ANIO = T.ANIO
                            AND S.SEMANA = T.SEMANA
													AND S.IID_ALMACEN = P.IID_ALMACEN)),
                    0) AS RACK_PORCENTAJE
					  FROM OP_IN_PORCENTAJE_OCUPACION t
					 INNER JOIN ALMACEN P ON T.IID_ALMACEN = P.IID_ALMACEN
					 WHERE T.ANIO = $anio
					   AND T.SEMANA = $no_semana_inf
					 AND T.IID_PLAZA in ($in_plaza)
					 $andcliente
					 GROUP BY P.IID_ALMACEN, P.V_NOMBRE";
					 #echo $sql;
					 /*(SELECT MAX(PRU2.SEMANA)
														 FROM OP_IN_PORCENTAJE_OCUPACION PRU2
														WHERE PRU2.ANIO = $anio
															AND t.MES = PRU2.MES
															AND PRU2.MES = $mes2
															AND PRU2.IID_PLAZA = t.IID_PLAZA)*/
		$stid = oci_parse($conn,$sql);
		oci_execute($stid);
		while (($row = oci_fetch_assoc($stid))!= false) {
		$res_array[] = $row;
		}
		oci_free_statement($stid);
		oci_close($conn);
		return $res_array;
	}

	public function graficaCliente($plaza,$fecha,$fil_check,$almacen)
	{
		$conn = conexion::conectar();
		$res_array = array();

		//CODIGO FECHAS SELECCIONAR # SEMANA :V SE MAMO
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;

		$mes = date("m");
		$mes2 = date("m")-1;

		$anio = date("Y");
		$anio2 = date("Y");

		//echo $no_semana." ".$no_semana_inf;
		if ($almacen == 'ALL') {
			$and_alm = "";
			$and_alm2 = "";
		}
		else {
			$and_alm = "AND PRU.IID_ALMACEN = ".$almacen;
			$and_alm2 = "AND t.IID_ALMACEN = ".$almacen;
		}


			if ( $this->validateDate(substr($fecha,0,10)) ){
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));
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

		$sql = "SELECT P.IID_NUM_CLIENTE,
       P.V_RAZON_SOCIAL AS CLIENTE,
       NVL(SUM((T.PORCENTAJE) /
               (SELECT COUNT(DISTINCT S.IID_CLIENTE)
                  FROM OP_IN_PORCENTAJE_OCUPACION S
                 WHERE S.IID_PLAZA = T.IID_PLAZA
                   AND S.ANIO = T.ANIO
                   AND S.SEMANA = T.SEMANA
								 	 AND S.IID_CLIENTE = P.IID_NUM_CLIENTE)),
           0) + NVL(SUM((T.PORCENTAJE_RACKS) /
                        (SELECT COUNT(DISTINCT S.IID_CLIENTE)
                           FROM OP_IN_PORCENTAJE_OCUPACION S
                          WHERE S.IID_PLAZA = T.IID_PLAZA
                            AND S.ANIO = T.ANIO
                            AND S.SEMANA = T.SEMANA
													  AND S.IID_CLIENTE = P.IID_NUM_CLIENTE)),
                    0) AS RACK_PORCENTAJE
					  FROM OP_IN_PORCENTAJE_OCUPACION t
					 INNER JOIN CLIENTE P ON T.IID_CLIENTE = P.IID_NUM_CLIENTE
					 WHERE T.ANIO = $anio
					   AND T.SEMANA = $no_semana_inf
						 AND T.IID_PLAZA IN ($in_plaza)
					   $and_alm2
					 GROUP BY P.IID_NUM_CLIENTE, P.V_RAZON_SOCIAL";

					# echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		#echo $sql;
		return $res_array;

	}


	public function graficaMensual($plaza,$fecha,$almacen,$fil_check,$cliente){

		$no_semana = date("W");
		$no_semana_inf = date("W")-4;


		$mes = date("m")-1;
		$mes2 = date("m");

		$anio = date("Y");
		$anio2 = date("Y");



			if ( $this->validateDate(substr($fecha,0,10)) ){
				//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

				//echo $no_semana. " ". $no_semana_inf;
			}

		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza){
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

		if ($almacen == 'ALL') {
			$prueba_almacen = '';
			$prueba_capacidad = '';
		}
		else {
			$prueba_almacen = " AND PRU.IID_ALMACEN =".$almacen." ";
			$prueba_capacidad = " AND CAP.IID_ALMACEN = ".$almacen." ";
		}

		if ($cliente == 'ALL') {
			 $clienteand = " ";
		}else {
			$clienteand = " AND T.IID_CLIENTE = ".$cliente." ";
		}

		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT T.IID_PLAZA,
       P.V_RAZON_SOCIAL AS PLAZA,
       NVL(SUM((T.PORCENTAJE)/(SELECT COUNT(DISTINCT S.IID_ALMACEN ) FROM OP_IN_PORCENTAJE_OCUPACION S
               WHERE S.IID_PLAZA =T.IID_PLAZA
                      AND S.ANIO = T.ANIO
                      AND S.SEMANA = T.SEMANA)), 0)+
       NVL(SUM((T.PORCENTAJE_RACKS)/(SELECT COUNT(DISTINCT S.IID_ALMACEN ) FROM OP_IN_PORCENTAJE_OCUPACION S
               WHERE S.IID_PLAZA =T.IID_PLAZA
                      AND S.ANIO = T.ANIO
                      AND S.SEMANA = T.SEMANA)), 0 ) AS RACK_PORCENTAJE
				FROM OP_IN_PORCENTAJE_OCUPACION t
				     INNER JOIN PLAZA P ON T.IID_PLAZA = P.IID_PLAZA
				WHERE T.ANIO = ".$anio."
				      AND T.SEMANA = $no_semana_inf
				      $clienteand
				GROUP BY T.IID_PLAZA, P.V_RAZON_SOCIAL";

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


	public function datos($plaza,$fecha,$almacen,$fil_check){

		$no_semana = date("W");
		$no_semana_inf = date("W")-4;


		$mes = date("m")-1;
		$mes2 = date("m");

		$anio = date("Y");
		$anio2 = date("Y");



		if ($fil_check == 'on'){

			//if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

				$dia2 = substr($fecha, 11,2);
				$mes2 = substr($fecha, 14,2);
				$anio2 = substr($fecha,17,4);
				$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
				//echo "string";
				//echo $no_semana. " ". $no_semana_inf;
			//}
		}
		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza){
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

		if ($almacen == 'ALL') {
			$prueba_almacen = '';
			$prueba_capacidad = '';
		}
		else {
			$prueba_almacen = " AND PRU.IID_ALMACEN =".$almacen." ";
			$prueba_capacidad = " AND CAP.IID_ALMACEN = ".$almacen." ";
		}

		$conn = conexion::conectar();
		$res_array = array();

		$sql ="SELECT PLA.IID_PLAZA ,
		       REPLACE(PLA.V_RAZON_SOCIAL, ' (ARGO)') AS PLAZA,
		       NVL(( SELECT SUM(PRU.MTS_UTILIZADOS)
		                    FROM PRUEBA_SUBIDA PRU
		                    WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
		                                               FROM PRUEBA_SUBIDA PRU2
		                                               WHERE PRU2.ANIO = ".$anio."
		                                               AND PRU.MES = PRU2.MES
		                                               AND PRU2.MES = ".$mes."
		                                               AND PRU2.IID_PLAZA = PRU.IID_PLAZA)
		                    AND PRU.IID_PLAZA IN(".$in_plaza.")
		                    ".$prueba_almacen."
		                    AND PRU.IID_PLAZA = PLA.IID_PLAZA
		                    AND PRU.OCUPACION = 1
		                    AND PRU.ANIO = ".$anio."
		                    AND PRU.MES = ".$mes."), 0) AS MTS_UTILIZADOS,
		       NVL(( SELECT SUM(PRU.MTS_UTILIZADOS_PASILLOS)
		                    FROM PRUEBA_SUBIDA PRU
		                    WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
		                                               FROM PRUEBA_SUBIDA PRU2
		                                               WHERE PRU2.ANIO = ".$anio."
		                                               AND PRU.MES = PRU2.MES
		                                               AND PRU2.MES = ".$mes."
		                                               AND PRU2.IID_PLAZA = PRU.IID_PLAZA)
		                    AND PRU.IID_PLAZA IN(".$in_plaza.")
		                    ".$prueba_almacen."
		                    AND PRU.IID_PLAZA = PLA.IID_PLAZA
		                    AND PRU.OCUPACION = 0
		                    AND PRU.ANIO = ".$anio."
		                    AND PRU.MES = ".$mes."), 0) AS MTS_PASILLOS_ESPACIO,
		       NVL((SELECT SUM (CAP.MTS_RACKS)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
		                                              AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
		                    ".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
		                    AND CAP.ANIO = ".$anio."
		                    AND CAP.MES = ".$mes."), 0) AS MTS_RACKS,
		       NVL((SELECT SUM (CAP.CAPACIDAD_TOTAL)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
		                                              AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
		                    ".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
		                    AND CAP.ANIO = ".$anio."
		                    AND CAP.MES = ".$mes."), 0) AS CAPACIDAD_TOTAL,
		       NVL((SELECT SUM (CAP.USO_VARIADOS)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
		                                              AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
		                    ".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
		                    AND CAP.ANIO = ".$anio."
		                    AND CAP.MES = ".$mes."), 0) AS USO_VARIADO,
		        NVL((SELECT SUM (CAP.AREA_RACKS)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
		                                              AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
		                    ".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
		                    AND CAP.ANIO = ".$anio."
		                    AND CAP.MES = ".$mes."), 0) AS AREA_RACKS,
		        NVL((SELECT SUM (CAP.TAMANIO_BODEGA)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
		                                              AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
		                    ".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
		                    AND CAP.ANIO = ".$anio."
		                    AND CAP.MES = ".$mes."), 0) AS TAMANIO_BODEGA
		        FROM PLAZA PLA
		        WHERE PLA.IID_PLAZA IN (2,3,4,5,6,7,8,17,18)
		        GROUP BY PLA.IID_PLAZA, PLA.V_RAZON_SOCIAL ORDER BY PLA.IID_PLAZA";

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

	public function filtros($option,$depto)
	{
		$conn = conexion::conectar();
		$res_array = array();

		switch ($option) {
			case '1':
				$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18) ";
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

	function capacidad_almacen($plaza,$fecha,$fil_check){
			$no_semana = date("W");
			$no_semana_inf = date("W")-4;


			$mes = date("m")-1;
			$mes2 = date("m");

			$anio = date("Y");
			$anio2 = date("Y");

		#	echo $mes." ".$mes2;


			if ($fil_check == 'on'){

				if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
					//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
					$dia = substr($fecha, 0,2);
					$mes = substr($fecha, 3,2);
					$anio = substr($fecha,6,4);
					$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

					$dia2 = substr($fecha, 11,2);
					$mes2 = substr($fecha, 14,2);
					$anio2 = substr($fecha,17,4);
					$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
					//echo $no_semana. " ". $no_semana_inf;
				}
			}
			$in_plaza = "2,3,4,5,6,7,8,17,18";
			switch ($plaza){
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
			$sql ="SELECT SUM(PRU.MTS_RACKS) AS MTS_RACKS,
										SUM(PRU.CAPACIDAD_TOTAL) AS CAPACIDAD_TOTAL,
										SUM(PRU.USO_VARIADOS) AS USO_VARIADOS,
										SUM(PRU.AREA_RACKS) AS AREA_RACKS,
										SUM(PRU.TAMANIO_BODEGA) AS TAMANIO_BODEGA
							FROM ALMACEN_CAPACIDAD PRU
							WHERE PRU.SEMANA = (SELECT MAX(SEMANA) FROM ALMACEN_CAPACIDAD PRU2 WHERE PRU2.MES =".$mes2." AND PRU2.ANIO = ".$anio2." AND PRU2.IID_PLAZA = PRU.IID_PLAZA)
							AND PRU.IID_PLAZA IN(".$in_plaza.")";

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

	/*****************************ALMACEN **************************************************/
	function almacenSql($plaza){
		$conn = conexion::conectar();
		$res_array = array();
		switch($plaza){
 		 //case 'CORPORATIVO': $in_plaza = 2; break;
 		 case 'CÓRDOBA': $in_plaza = 3; break;
 		 case 'MÉXICO': $in_plaza = 4; break;
 		 case 'GOLFO': $in_plaza = 5; break;
 		 case 'PENINSULA': $in_plaza = 6; break;
 		 case 'PUEBLA': $in_plaza = 7; break;
 		 case 'BAJIO': $in_plaza = 8; break;
 		 case 'OCCIDENTE': $in_plaza = 17; break;
 		 case 'NORESTE': $in_plaza = 18; break;
 		 default: $in_plaza = "3,4,5,6,7,8,17,18"; break;
 	 }
		$sql = "SELECT DISTINCT(AL.IID_ALMACEN), AL.V_NOMBRE
						FROM ALMACEN AL            
						WHERE AL.IID_PLAZA IN ($in_plaza) AND AL.IID_ALMACEN NOT IN (9998, 9999) AND S_STATUS =1 ORDER BY AL.IID_ALMACEN";
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

	function clienteSql($almacen){
		$conn = conexion::conectar();
		$res_array = array();
		if ($almacen == "ALL") {
			$And_almacen = " ";
		}
		else {
			$And_almacen = " WHERE P.IID_ALMACEN = ".$almacen;
		}
		$sql = "SELECT DISTINCT(P.ID_CLIENTE) AS ID_CLIENTE, C.V_RAZON_SOCIAL AS NOMBRE FROM CLIENTE C
         		INNER JOIN PRUEBA_SUBIDA P ON C.IID_NUM_CLIENTE = P.ID_CLIENTE ".$And_almacen. " ORDER BY P.ID_CLIENTE";
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


	function almacenNombre($plaza,$almacen){
		$conn = conexion::conectar();
		$res_array = array();
		switch($plaza){
		 //case 'CORPORATIVO': $in_plaza = 2; break;
		 case 'CÓRDOBA': $in_plaza = 3; break;
		 case 'MÉXICO': $in_plaza = 4; break;
		 case 'GOLFO': $in_plaza = 5; break;
		 case 'PENINSULA': $in_plaza = 6; break;
		 case 'PUEBLA': $in_plaza = 7; break;
		 case 'BAJIO': $in_plaza = 8; break;
		 case 'OCCIDENTE': $in_plaza = 17; break;
		 case 'NORESTE': $in_plaza = 18; break;
		 default: $in_plaza = "3,4,5,6,7,8,17,18"; break;
	 }
		$sql = "SELECT IID_ALMACEN, V_NOMBRE FROM ALMACEN WHERE IID_ALMACEN = $almacen AND IID_PLAZA = $in_plaza AND IID_ALMACEN NOT IN (9998, 9999) ORDER BY IID_ALMACEN";
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

	function clienteNombre($cliente){
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT IID_NUM_CLIENTE, V_RAZON_SOCIAL FROM CLIENTE WHERE IID_NUM_CLIENTE = $cliente  ORDER BY IID_NUM_CLIENTE";
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
}
