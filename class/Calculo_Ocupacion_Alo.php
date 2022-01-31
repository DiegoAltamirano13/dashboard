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
	public function grafica($plaza,$fecha,$almacen,$fil_check)
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
			$prueba_capacidad = " AND CAP.IID_ALMACEN = ".$almacen." ";
		}

		if ($fil_check == 'on'){
			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

				$dia2 = substr($fecha, 11,2);
				$mes2 = substr($fecha, 14,2);
				$anio2 = substr($fecha,17,4);
				$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
			}
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

		$sql = "SELECT PLA.N_MES ,
       PLA.MES,
       NVL(( SELECT SUM(PRU.MTS_UTILIZADOS)
                    FROM PRUEBA_SUBIDA PRU
                    WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
                                               FROM PRUEBA_SUBIDA PRU2
                                               WHERE PRU2.ANIO = ".$anio."
                                               AND PRU.MES = PRU2.MES
                                               AND PRU2.IID_PLAZA = PRU.IID_PLAZA
																							 AND PRU2.OCUPACION IN (1)
													                      AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
													                      AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN)
                    AND PRU.IID_PLAZA IN(".$in_plaza.")
										AND PRU.ANIO = ".$anio."
                    AND PRU.MES = PLA.N_MES
										".$prueba_almacen."
                    AND PRU.OCUPACION IN (1)
										AND PRU.ID_CLIENTE = 2905), 0) AS MTS_UTILIZADOS,
       NVL(( SELECT SUM(PRU.MTS_UTILIZADOS_PASILLOS)
                    FROM PRUEBA_SUBIDA PRU
                    WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
                                               FROM PRUEBA_SUBIDA PRU2
                                               WHERE PRU2.ANIO = ".$anio."
                                               AND PRU.MES = PRU2.MES
                                               AND PRU2.IID_PLAZA = PRU.IID_PLAZA
																							 AND PRU2.OCUPACION IN (0, 2)
													                      AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
													                      AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN)
                    AND PRU.IID_PLAZA IN(".$in_plaza.")
										AND PRU.ANIO = ".$anio."
                    AND PRU.MES = PLA.N_MES
										".$prueba_almacen."
                    AND PRU.OCUPACION IN (0, 2)
										AND PRU.ID_CLIENTE = 2905), 0) AS MTS_PASILLOS_PASILLOS,
        NVL((SELECT SUM (CAP.TAMANIO_BODEGA)
                   FROM ALMACEN_CAPACIDAD CAP
                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
                                              FROM ALMACEN_CAPACIDAD CAP2
                                              WHERE CAP2.ANIO = ".$anio."
                                              AND CAP2.MES = cap.mes
                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
                    AND CAP.IID_PLAZA IN (".$in_plaza.")
										".$prueba_capacidad."
                    AND CAP.Mes = PLA.N_MES
									  AND CAP.ANIO = ".$anio."), 0) AS TAMANIO_BODEGA,
         NVL((SELECT SUM (CAP.MTS_RACKS)
                   FROM ALMACEN_CAPACIDAD CAP
                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
                                              FROM ALMACEN_CAPACIDAD CAP2
                                              WHERE CAP2.ANIO = ".$anio."
                                              AND CAP2.MES = cap.mes
                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
                    AND CAP.IID_PLAZA IN (".$in_plaza.")
										".$prueba_capacidad."
                    AND CAP.Mes = PLA.N_MES
									  AND CAP.ANIO = ".$anio."), 0) AS MTS_RACKS,
          NVL((SELECT SUM (CAP.CAPACIDAD_TOTAL)
                   FROM ALMACEN_CAPACIDAD CAP
                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
                                              FROM ALMACEN_CAPACIDAD CAP2
                                              WHERE CAP2.ANIO = ".$anio."
                                              AND CAP2.MES = cap.mes
                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
                    AND CAP.IID_PLAZA IN (".$in_plaza.")
										".$prueba_capacidad."
                    AND CAP.Mes = PLA.N_MES
									  AND CAP.ANIO = ".$anio."), 0) AS CAPACIDAD_TOTAL,
          NVL((SELECT SUM (CAP.USO_VARIADOS)
                   FROM ALMACEN_CAPACIDAD CAP
                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
                                              FROM ALMACEN_CAPACIDAD CAP2
                                              WHERE CAP2.ANIO = ".$anio."
                                              AND CAP2.MES = cap.mes
                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
                    AND CAP.IID_PLAZA IN (".$in_plaza.")
										".$prueba_capacidad."
                    AND CAP.Mes = PLA.N_MES
									  AND CAP.ANIO = ".$anio."), 0) AS USO_VARIADO,
          NVL((SELECT SUM (CAP.AREA_RACKS)
                   FROM ALMACEN_CAPACIDAD CAP
                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
                                              FROM ALMACEN_CAPACIDAD CAP2
                                              WHERE CAP2.ANIO = ".$anio."
                                              AND CAP2.MES = cap.mes
                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
                    AND CAP.IID_PLAZA IN (".$in_plaza.")
										".$prueba_capacidad."
                    AND CAP.Mes = PLA.N_MES
									  AND CAP.ANIO = ".$anio."), 0) AS AREA_RACKS
			    FROM RH_MESES_GRAFICAS PLA GROUP BY PLA.N_MES, PLA.MES ORDER BY PLA.N_MES ";

					# echo $sql;

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
	/*++++++++++++++++++++++++ GRAFICA ALMACEN ++++++++++++++++++++++++*/
	public function graficaAlmacen($plaza,$fecha,$fil_check)
	{
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;


		$mes = date("m")-1;
		$mes2 = date("m");

		$anio = date("Y");
		$anio2 = date("Y");

		#	echo $mes." ".$mes2;

		if ($fil_check == 'on'){
				#echo substr($fecha,0,10)."    ".substr($fecha,11,10);
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
		$sql ="SELECT PLA.IID_ALMACEN ,
				REPLACE(PLA.V_NOMBRE, ' (ARGO)') AS PLAZA,
				NVL(( SELECT SUM(PRU.MTS_UTILIZADOS)
             FROM PRUEBA_SUBIDA PRU
             WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
                                        FROM PRUEBA_SUBIDA PRU2
                                        WHERE PRU2.ANIO = ".$anio."
                                              AND PRU.MES = PRU2.MES
                                              AND PRU2.MES = ".$mes2."
                                              AND PRU2.IID_PLAZA = PRU.IID_PLAZA
																							AND PRU2.OCUPACION IN (1)
													                      AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
													                      AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN)
                 AND PRU.IID_PLAZA IN(".$in_plaza.")
                 AND PRU.IID_ALMACEN = PLA.IID_ALMACEN
								 AND PRU.ANIO = ".$anio."
								 AND PRU.MES = ".$mes2."
                 AND PRU.OCUPACION IN (1)
							 	 AND PRU.ID_CLIENTE = 2905), 0) AS MTS_UTILIZADOS,
			NVL(( SELECT SUM(PRU.MTS_UTILIZADOS_PASILLOS)
					FROM PRUEBA_SUBIDA PRU
					WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
												FROM PRUEBA_SUBIDA PRU2
												WHERE PRU2.ANIO = ".$anio."
												AND PRU.MES = PRU2.MES
												AND PRU2.MES = ".$mes2."
												AND PRU2.IID_PLAZA = PRU.IID_PLAZA
												AND PRU2.OCUPACION IN (0,2 )
	                      AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
	                      AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN)
					AND PRU.IID_PLAZA IN(".$in_plaza.")
					AND PRU.IID_ALMACEN = PLA.IID_ALMACEN
					AND PRU.ANIO = ".$anio."
					AND PRU.MES = ".$mes2."
					AND PRU.OCUPACION in(0, 2)
					AND PRU.ID_CLIENTE = 2905), 0) AS MTS_PASILLOS_ESPACIO,
			NVL((SELECT SUM(ALCAP.MTS_RACKS)
						FROM ALMACEN_CAPACIDAD ALCAP
						WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
															FROM ALMACEN_CAPACIDAD ALCAP2
															WHERE ALCAP2.ANIO = ".$anio."
																AND ALCAP.MES = ALCAP2.MES
																AND ALCAP2.MES = ".$mes2."
																AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA
																)
						AND ALCAP.IID_PLAZA IN (".$in_plaza.")
						AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
						AND ALCAP.ANIO = ".$anio."
						AND ALCAP.MES = ".$mes2." ), 0) AS MTS_RACKS,
			NVL((SELECT SUM(ALCAP.CAPACIDAD_TOTAL)
						FROM ALMACEN_CAPACIDAD ALCAP
						WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
															FROM ALMACEN_CAPACIDAD ALCAP2
															WHERE ALCAP2.ANIO = ".$anio."
																AND ALCAP.MES = ALCAP2.MES
																AND ALCAP2.MES = ".$mes2."
																AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
						AND ALCAP.IID_PLAZA IN (".$in_plaza.")
						AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
						AND ALCAP.ANIO = ".$anio."
						AND ALCAP.MES = ".$mes2."), 0) AS CAPACIDAD_TOTAL,
			NVL((SELECT SUM(ALCAP.USO_VARIADOS)
						FROM ALMACEN_CAPACIDAD ALCAP
						WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
															FROM ALMACEN_CAPACIDAD ALCAP2
															WHERE ALCAP2.ANIO = ".$anio."
																AND ALCAP.MES = ALCAP2.MES
																AND ALCAP2.MES = ".$mes2."
																AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
						AND ALCAP.IID_PLAZA IN (".$in_plaza.")
						AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
						AND ALCAP.ANIO = ".$anio."
						AND ALCAP.MES = ".$mes2."), 0) AS USO_VARIADO,
			NVL((SELECT SUM(ALCAP.AREA_RACKS)
						FROM ALMACEN_CAPACIDAD ALCAP
						WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
															FROM ALMACEN_CAPACIDAD ALCAP2
															WHERE ALCAP2.ANIO = ".$anio."
																AND ALCAP.MES = ALCAP2.MES
																AND ALCAP2.MES = ".$mes2."
																AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
						AND ALCAP.IID_PLAZA IN (".$in_plaza.")
						AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
						AND ALCAP.ANIO = ".$anio."
						AND ALCAP.MES = ".$mes2."), 0) AS AREA_RACKS,
			NVL((SELECT SUM(ALCAP.TAMANIO_BODEGA)
						FROM ALMACEN_CAPACIDAD ALCAP
						WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
															FROM ALMACEN_CAPACIDAD ALCAP2
															WHERE ALCAP2.ANIO = ".$anio."
																AND ALCAP.MES = ALCAP2.MES
																AND ALCAP2.MES = ".$mes2."
																AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
						AND ALCAP.IID_PLAZA IN (".$in_plaza.")
						AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
						AND ALCAP.ANIO = ".$anio."
						AND ALCAP.MES = ".$mes2."), 0) AS TAMANIO_BODEGA
				FROM ALMACEN PLA
				INNER JOIN ALMACEN_CAPACIDAD ALC ON PLA.IID_ALMACEN = ALC.IID_ALMACEN AND ALC.IID_PLAZA = PLA.IID_PLAZA AND PLA.IID_PLAZA IN (".$in_plaza.")
				GROUP BY PLA.IID_ALMACEN, PLA.V_NOMBRE ORDER BY PLA.IID_ALMACEN";
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

/*********************************************************************************************************/
public function graficaAlmacenProyecto($plaza,$fecha,$fil_check,$proyecto)
{
	if ($proyecto == '0') {
		$in_1 =  " AND PRU2.OCUPACION IN (2)";
		$in_2 =  " AND PRU2.OCUPACION  IN (2)";
		$in_3 =  " AND PRU.OCUPACION  IN (2)";
		$in_4 =  " AND PRU.OCUPACION  IN (2)";
		$and_pro = " ";
		$and_pro2 = " ";
	}else {
		$in_1 =  " AND PRU2.OCUPACION IN (1)";
		$in_2 =  " AND PRU2.OCUPACION IN (0)";
		$in_3 =  " AND PRU.OCUPACION IN (1)";
		$in_4 =  " AND PRU.OCUPACION IN (0)";
		$and_pro = " AND PRU.T_PROYECTO = '$proyecto'";
		$and_pro2 = " AND PRU2.T_PROYECTO = '$proyecto'";
	}

	$no_semana = date("W");
	$no_semana_inf = date("W")-4;


	$mes = date("m")-1;
	$mes2 = date("m");

	$anio = date("Y");
	$anio2 = date("Y");

	#	echo $mes." ".$mes2;

	if ($fil_check == 'on'){
			#echo substr($fecha,0,10)."    ".substr($fecha,11,10);
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
	$sql ="SELECT PLA.IID_ALMACEN ,
			REPLACE(PLA.V_NOMBRE, ' (ARGO)') AS PLAZA,
			NVL(( SELECT SUM(PRU.MTS_UTILIZADOS)
					 FROM PRUEBA_SUBIDA PRU
					 WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
																			FROM PRUEBA_SUBIDA PRU2
																			WHERE PRU2.ANIO = ".$anio."
																						AND PRU.MES = PRU2.MES
																						AND PRU2.MES = ".$mes2."
																						AND PRU2.IID_PLAZA = PRU.IID_PLAZA
																						$in_1
																							AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
																							AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN
																							$and_pro2)
							 AND PRU.IID_PLAZA IN(".$in_plaza.")
							 AND PRU.IID_ALMACEN = PLA.IID_ALMACEN
							 AND PRU.ANIO = ".$anio."
							 AND PRU.MES = ".$mes2."
							 $in_3
							 AND PRU.ID_CLIENTE = 2905
							 $and_pro
						 	 ), 0) AS MTS_UTILIZADOS,
		NVL(( SELECT SUM(PRU.MTS_UTILIZADOS_PASILLOS)
				FROM PRUEBA_SUBIDA PRU
				WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
											FROM PRUEBA_SUBIDA PRU2
											WHERE PRU2.ANIO = ".$anio."
											AND PRU.MES = PRU2.MES
											AND PRU2.MES = ".$mes2."
											AND PRU2.IID_PLAZA = PRU.IID_PLAZA
											$in_2
											AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
											AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN
											$and_pro2)
				AND PRU.IID_PLAZA IN(".$in_plaza.")
				AND PRU.IID_ALMACEN = PLA.IID_ALMACEN
				AND PRU.ANIO = ".$anio."
				AND PRU.MES = ".$mes2."
				$in_4
				AND PRU.ID_CLIENTE = 2905
				$and_pro), 0) AS MTS_PASILLOS_ESPACIO,
		NVL((SELECT SUM(ALCAP.MTS_RACKS)
					FROM ALMACEN_CAPACIDAD ALCAP
					WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
														FROM ALMACEN_CAPACIDAD ALCAP2
														WHERE ALCAP2.ANIO = ".$anio."
															AND ALCAP.MES = ALCAP2.MES
															AND ALCAP2.MES = ".$mes2."
															AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA
															)
					AND ALCAP.IID_PLAZA IN (".$in_plaza.")
					AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
					AND ALCAP.ANIO = ".$anio."
					AND ALCAP.MES = ".$mes2." ), 0) AS MTS_RACKS,
		NVL((SELECT SUM(ALCAP.CAPACIDAD_TOTAL)
					FROM ALMACEN_CAPACIDAD ALCAP
					WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
														FROM ALMACEN_CAPACIDAD ALCAP2
														WHERE ALCAP2.ANIO = ".$anio."
															AND ALCAP.MES = ALCAP2.MES
															AND ALCAP2.MES = ".$mes2."
															AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
					AND ALCAP.IID_PLAZA IN (".$in_plaza.")
					AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
					AND ALCAP.ANIO = ".$anio."
					AND ALCAP.MES = ".$mes2."), 0) AS CAPACIDAD_TOTAL,
		NVL((SELECT SUM(ALCAP.USO_VARIADOS)
					FROM ALMACEN_CAPACIDAD ALCAP
					WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
														FROM ALMACEN_CAPACIDAD ALCAP2
														WHERE ALCAP2.ANIO = ".$anio."
															AND ALCAP.MES = ALCAP2.MES
															AND ALCAP2.MES = ".$mes2."
															AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
					AND ALCAP.IID_PLAZA IN (".$in_plaza.")
					AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
					AND ALCAP.ANIO = ".$anio."
					AND ALCAP.MES = ".$mes2."), 0) AS USO_VARIADO,
		NVL((SELECT SUM(ALCAP.AREA_RACKS)
					FROM ALMACEN_CAPACIDAD ALCAP
					WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
														FROM ALMACEN_CAPACIDAD ALCAP2
														WHERE ALCAP2.ANIO = ".$anio."
															AND ALCAP.MES = ALCAP2.MES
															AND ALCAP2.MES = ".$mes2."
															AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
					AND ALCAP.IID_PLAZA IN (".$in_plaza.")
					AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
					AND ALCAP.ANIO = ".$anio."
					AND ALCAP.MES = ".$mes2."), 0) AS AREA_RACKS,
		NVL((SELECT SUM(ALCAP.TAMANIO_BODEGA)
					FROM ALMACEN_CAPACIDAD ALCAP
					WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
														FROM ALMACEN_CAPACIDAD ALCAP2
														WHERE ALCAP2.ANIO = ".$anio."
															AND ALCAP.MES = ALCAP2.MES
															AND ALCAP2.MES = ".$mes2."
															AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
					AND ALCAP.IID_PLAZA IN (".$in_plaza.")
					AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
					AND ALCAP.ANIO = ".$anio."
					AND ALCAP.MES = ".$mes2."), 0) AS TAMANIO_BODEGA
			FROM ALMACEN PLA
			INNER JOIN ALMACEN_CAPACIDAD ALC ON PLA.IID_ALMACEN = ALC.IID_ALMACEN AND ALC.IID_PLAZA = PLA.IID_PLAZA AND PLA.IID_PLAZA IN (".$in_plaza.")
			GROUP BY PLA.IID_ALMACEN, PLA.V_NOMBRE ORDER BY PLA.IID_ALMACEN";
			#echo $sql;
			if($proyecto == 'BMW'){
				#echo $sql;
			}
	$stid = oci_parse($conn,$sql);
	oci_execute($stid);
	while (($row = oci_fetch_assoc($stid))!= false) {
	$res_array[] = $row;
	}
	oci_free_statement($stid);
	oci_close($conn);
	return $res_array;
}


public function graficaAlmacenProyectoCuarentena($plaza,$fecha,$fil_check,$proyecto)
{
	if ($proyecto == '0') {
		$in_1 =  " AND PRU2.OCUPACION IN (2)";
		$in_2 =  " AND PRU2.OCUPACION  IN (2)";
		$in_3 =  " AND PRU.OCUPACION  IN (2)";
		$in_4 =  " AND PRU.OCUPACION  IN (2)";
		$and_pro = " ";
		$and_pro2 = " ";
	}else {
		$in_1 =  " AND PRU2.OCUPACION IN (1)";
		$in_2 =  " AND PRU2.OCUPACION IN (0)";
		$in_3 =  " AND PRU.OCUPACION IN (1)";
		$in_4 =  " AND PRU.OCUPACION IN (0)";
		$and_pro = " AND PRU.T_PROYECTO = '$proyecto'";
		$and_pro2 = " AND PRU2.T_PROYECTO = '$proyecto'";
	}

	$no_semana = date("W");
	$no_semana_inf = date("W")-4;


	$mes = date("m")-1;
	$mes2 = date("m");

	$anio = date("Y");
	$anio2 = date("Y");

	#	echo $mes." ".$mes2;

	if ($fil_check == 'on'){
			#echo substr($fecha,0,10)."    ".substr($fecha,11,10);
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
	$sql ="SELECT PLA.IID_ALMACEN ,
			PLA.T_PROYECTO,
			NVL(( SELECT SUM(PRU.MTS_UTILIZADOS)
					 FROM PRUEBA_SUBIDA PRU
					 WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
																			FROM PRUEBA_SUBIDA PRU2
																			WHERE PRU2.ANIO = ".$anio."
																						AND PRU.MES = PRU2.MES
																						AND PRU2.MES = ".$mes2."
																						AND PRU2.IID_PLAZA = PRU.IID_PLAZA
																						$in_1
																							AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
																							AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN
																							$and_pro2)
							 AND PRU.IID_PLAZA IN(".$in_plaza.")
							 AND PRU.ANIO = ".$anio."
							 AND PRU.MES = ".$mes2."
							 $in_3
							 AND PRU.ID_CLIENTE = 2905
							 AND PRU.ID_CLIENTE= PLA.ID_CLIENTE
               AND PRU.T_PROYECTO = PLA.T_PROYECTO
               AND PRU.IID_ALMACEN = PLA.IID_ALMACEN
							 $and_pro
						 	 ), 0) AS MTS_UTILIZADOS,
		NVL(( SELECT SUM(PRU.MTS_UTILIZADOS_PASILLOS)
				FROM PRUEBA_SUBIDA PRU
				WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
											FROM PRUEBA_SUBIDA PRU2
											WHERE PRU2.ANIO = ".$anio."
											AND PRU.MES = PRU2.MES
											AND PRU2.MES = ".$mes2."
											AND PRU2.IID_PLAZA = PRU.IID_PLAZA
											$in_2
											AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
											AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN
											$and_pro2)
				AND PRU.IID_PLAZA IN(".$in_plaza.")
				AND PRU.ANIO = ".$anio."
				AND PRU.MES = ".$mes2."
				$in_4
				AND PRU.ID_CLIENTE = 2905
				AND PRU.ID_CLIENTE= PLA.ID_CLIENTE
        AND PRU.T_PROYECTO = PLA.T_PROYECTO
        AND PRU.IID_ALMACEN = PLA.IID_ALMACEN
				$and_pro), 0) AS MTS_PASILLOS_ESPACIO,
		NVL((SELECT SUM(ALCAP.MTS_RACKS)
					FROM ALMACEN_CAPACIDAD ALCAP
					WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
														FROM ALMACEN_CAPACIDAD ALCAP2
														WHERE ALCAP2.ANIO = ".$anio."
															AND ALCAP.MES = ALCAP2.MES
															AND ALCAP2.MES = ".$mes2."
															AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA
															)
					AND ALCAP.IID_PLAZA IN (".$in_plaza.")
					AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
					AND ALCAP.ANIO = ".$anio."
					AND ALCAP.MES = ".$mes2." ), 0) AS MTS_RACKS,
		NVL((SELECT SUM(ALCAP.CAPACIDAD_TOTAL)
					FROM ALMACEN_CAPACIDAD ALCAP
					WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
														FROM ALMACEN_CAPACIDAD ALCAP2
														WHERE ALCAP2.ANIO = ".$anio."
															AND ALCAP.MES = ALCAP2.MES
															AND ALCAP2.MES = ".$mes2."
															AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
					AND ALCAP.IID_PLAZA IN (".$in_plaza.")
					AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
					AND ALCAP.ANIO = ".$anio."
					AND ALCAP.MES = ".$mes2."), 0) AS CAPACIDAD_TOTAL,
		NVL((SELECT SUM(ALCAP.USO_VARIADOS)
					FROM ALMACEN_CAPACIDAD ALCAP
					WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
														FROM ALMACEN_CAPACIDAD ALCAP2
														WHERE ALCAP2.ANIO = ".$anio."
															AND ALCAP.MES = ALCAP2.MES
															AND ALCAP2.MES = ".$mes2."
															AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
					AND ALCAP.IID_PLAZA IN (".$in_plaza.")
					AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
					AND ALCAP.ANIO = ".$anio."
					AND ALCAP.MES = ".$mes2."), 0) AS USO_VARIADO,
		NVL((SELECT SUM(ALCAP.AREA_RACKS)
					FROM ALMACEN_CAPACIDAD ALCAP
					WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
														FROM ALMACEN_CAPACIDAD ALCAP2
														WHERE ALCAP2.ANIO = ".$anio."
															AND ALCAP.MES = ALCAP2.MES
															AND ALCAP2.MES = ".$mes2."
															AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
					AND ALCAP.IID_PLAZA IN (".$in_plaza.")
					AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
					AND ALCAP.ANIO = ".$anio."
					AND ALCAP.MES = ".$mes2."), 0) AS AREA_RACKS,
		NVL((SELECT SUM(ALCAP.TAMANIO_BODEGA)
					FROM ALMACEN_CAPACIDAD ALCAP
					WHERE ALCAP.SEMANA = (SELECT MAX(ALCAP2.SEMANA)
														FROM ALMACEN_CAPACIDAD ALCAP2
														WHERE ALCAP2.ANIO = ".$anio."
															AND ALCAP.MES = ALCAP2.MES
															AND ALCAP2.MES = ".$mes2."
															AND ALCAP2.IID_PLAZA = ALCAP.IID_PLAZA)
					AND ALCAP.IID_PLAZA IN (".$in_plaza.")
					AND ALCAP.IID_ALMACEN = PLA.IID_ALMACEN
					AND ALCAP.ANIO = ".$anio."
					AND ALCAP.MES = ".$mes2."), 0) AS TAMANIO_BODEGA
		 FROM PRUEBA_SUBIDA PLA
		 WHERE PLA.OCUPACION = 2
		 GROUP BY PLA.IID_ALMACEN, PLA.ID_CLIENTE ,PLA.T_PROYECTO
		 ORDER BY PLA.IID_ALMACEN";
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
			$and_alm2 = "AND PRUEBA_SUB.IID_ALMACEN = ".$almacen;
		}

		if ($fil_check == 'on'){

			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

				$dia2 = substr($fecha, 11,2);
				$mes2 = substr($fecha, 14,2);
				$anio2 = substr($fecha,17,4);
				$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
			}
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

		$sql = "SELECT PLA.IID_NUM_CLIENTE,
       PRUEBA_SUB.IID_ALMACEN,
       REPLACE(PLA.V_RAZON_SOCIAL, ' (ARGO)') AS PLAZA,
       NVL(( SELECT SUM(PRU.MTS_UTILIZADOS)
                    FROM PRUEBA_SUBIDA PRU
                    WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
                                               FROM PRUEBA_SUBIDA PRU2
                                               WHERE PRU2.ANIO = ".$anio."
                                               AND PRU.MES = PRU2.MES
                                               AND PRU2.MES = ".$mes2."
                                               AND PRU2.IID_PLAZA = PRU.IID_PLAZA
																							 AND PRU2.OCUPACION IN (1, 2)
												                      AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
												                      AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN)
                    AND PRU.IID_PLAZA IN(".$in_plaza.")
										AND PRU.MES = ".$mes2."
                     AND PRU.ANIO =  ".$anio."
                    AND PRU.ID_CLIENTE = PLA.IID_NUM_CLIENTE
										".$and_alm."
                    AND PRU.OCUPACION IN (1, 2)
										AND PRU.ID_CLIENTE = 2905), 0) AS MTS_UTILIZADOS,
        NVL(( SELECT SUM(PRU.MTS_UTILIZADOS_PASILLOS)
                     FROM PRUEBA_SUBIDA PRU
                     WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
                                                FROM PRUEBA_SUBIDA PRU2
                                                WHERE PRU2.ANIO = ".$anio."
                                                AND PRU.MES = PRU2.MES
                                                AND PRU2.MES = ".$mes2."
                                                AND PRU2.IID_PLAZA = PRU.IID_PLAZA
																								AND PRU2.OCUPACION IN (0, 2)
													                      AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
													                      AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN)
                     AND PRU.IID_PLAZA IN(".$in_plaza.")
										 AND PRU.MES = ".$mes2."
                      AND PRU.ANIO =  ".$anio."
                     AND PRU.ID_CLIENTE = PLA.IID_NUM_CLIENTE
										 ".$and_alm."
                     AND PRU.OCUPACION IN (0, 2)
									 	 AND PRU.ID_CLIENTE = 2905), 0) AS MTS_PASILLOS_ESPACIO
					       ,ALC.MTS_RACKS AS MTS_RACKS,
					       ALC.CAPACIDAD_TOTAL AS CAPACIDAD_TOTAL,
					       ALC.USO_VARIADOS AS USO_VARIADO,
					       ALC.AREA_RACKS AS AREA_RACKS,
					       ALC.TAMANIO_BODEGA AS TAMANIO_BODEGA
					FROM CLIENTE PLA
					INNER JOIN PRUEBA_SUBIDA PRUEBA_SUB ON PRUEBA_SUB.ID_CLIENTE = PLA.IID_NUM_CLIENTE
					INNER JOIN ALMACEN_CAPACIDAD ALC ON PRUEBA_SUB.IID_PLAZA = ALC.IID_PLAZA
										AND PRUEBA_SUB.IID_ALMACEN = ALC.IID_ALMACEN AND PRUEBA_SUB.SEMANA = ALC.SEMANA AND PRUEBA_SUB.MES = ALC.MES AND PRUEBA_SUB.ANIO = ALC.ANIO
										AND PRUEBA_SUB.ANIO = ".$anio."
										AND PRUEBA_SUB.MES =  ".$mes2."
										AND PRUEBA_SUB.ID_CLIENTE = 2905
					WHERE PRUEBA_SUB.SEMANA = (SELECT MAX(PRU2.SEMANA)
					                                               FROM PRUEBA_SUBIDA PRU2
					                                               WHERE PRU2.ANIO = ".$anio."
					                                               AND PRUEBA_SUB.MES = PRU2.MES
					                                               AND PRU2.MES = ".$mes2."
					                                               AND PRU2.IID_PLAZA = PRUEBA_SUB.IID_PLAZA
																												 AND PRU2.ID_CLIENTE = 2905
																												 AND PRU2.OCUPACION IN (0, 1, 2)
																							           AND PRUEBA_SUB.ID_CLIENTE = PRU2.ID_CLIENTE
																							           AND PRUEBA_SUB.IID_ALMACEN = PRU2.IID_ALMACEN)
																												 ".$and_alm2."
					GROUP BY PLA.IID_NUM_CLIENTE,
					         PLA.V_RAZON_SOCIAL,
					         ALC.MTS_RACKS,
					         ALC.CAPACIDAD_TOTAL,
					         ALC.USO_VARIADOS,
					         ALC.AREA_RACKS,
					         ALC.TAMANIO_BODEGA,
					         PRUEBA_SUB.IID_ALMACEN
					ORDER BY PLA.V_RAZON_SOCIAL ";
					#echo $sql;
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


	public function graficaMensual($plaza,$fecha,$almacen,$fil_check){

		$no_semana = date("W");
		$no_semana_inf = date("W")-4;


		$mes = date("m")-1;
		$mes2 = date("m");

		$anio = date("Y");
		$anio2 = date("Y");



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
		                                               AND PRU2.MES = ".$mes2."
		                                               AND PRU2.IID_PLAZA = PRU.IID_PLAZA
																									 AND PRU2.OCUPACION IN (1)
															                      AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
															                      AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN)
		                    AND PRU.IID_PLAZA IN(".$in_plaza.")
												".$prueba_almacen."
		                    AND PRU.IID_PLAZA = PLA.IID_PLAZA
		                    AND PRU.OCUPACION IN (1)
												AND PRU.ANIO = ".$anio."
												AND PRU.MES = ".$mes2."
												AND PRU.ID_CLIENTE = 2905), 0) AS MTS_UTILIZADOS,
		       NVL(( SELECT SUM(PRU.MTS_UTILIZADOS_PASILLOS)
		                    FROM PRUEBA_SUBIDA PRU
		                    WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
		                                               FROM PRUEBA_SUBIDA PRU2
		                                               WHERE PRU2.ANIO = ".$anio."
		                                               AND PRU.MES = PRU2.MES
		                                               AND PRU2.MES = ".$mes2."
		                                               AND PRU2.IID_PLAZA = PRU.IID_PLAZA
																									 AND PRU2.OCUPACION IN (0,2)
															                      AND PRU.ID_CLIENTE = PRU2.ID_CLIENTE
															                      AND PRU.IID_ALMACEN = PRU2.IID_ALMACEN)
		                    AND PRU.IID_PLAZA IN(".$in_plaza.")
												".$prueba_almacen."
		                    AND PRU.IID_PLAZA = PLA.IID_PLAZA
		                    AND PRU.OCUPACION IN(0, 2)
												AND PRU.ANIO = ".$anio."
												AND PRU.MES = ".$mes2."
												AND PRU.ID_CLIENTE = 2905), 0) AS MTS_PASILLOS_ESPACIO,
		       NVL((SELECT SUM (CAP.MTS_RACKS)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
																									AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes2."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
												".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
												AND CAP.ANIO = ".$anio."
												AND CAP.MES = ".$mes2."), 0) AS MTS_RACKS,
		       NVL((SELECT SUM (CAP.CAPACIDAD_TOTAL)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
																									AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes2."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
												".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
												AND CAP.ANIO = ".$anio."
												AND CAP.MES = ".$mes2."), 0) AS CAPACIDAD_TOTAL,
		       NVL((SELECT SUM (CAP.USO_VARIADOS)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
																									AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes2."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
												".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
												AND CAP.ANIO = ".$anio."
												AND CAP.MES = ".$mes2."), 0) AS USO_VARIADO,
		        NVL((SELECT SUM (CAP.AREA_RACKS)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
																									AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes2."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
												".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
												AND CAP.ANIO = ".$anio."
												AND CAP.MES = ".$mes2."), 0) AS AREA_RACKS,
		        NVL((SELECT SUM (CAP.TAMANIO_BODEGA)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
																									AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes2."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
												".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
												AND CAP.ANIO = ".$anio."
												AND CAP.MES = ".$mes2."), 0) AS TAMANIO_BODEGA
						FROM PLAZA PLA
						WHERE PLA.IID_PLAZA IN (2,3,4,5,6,7,8,17,18)
						GROUP BY PLA.IID_PLAZA, PLA.V_RAZON_SOCIAL ORDER BY PLA.IID_PLAZA";

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
            INNER JOIN ALMACEN_CAPACIDAD ALC ON AL.IID_PLAZA = ALC.IID_PLAZA AND AL.IID_ALMACEN = ALC.IID_ALMACEN
						INNER JOIN PRUEBA_SUBIDA PRU ON ALC.IID_ALMACEN = PRU.IID_ALMACEN AND PRU.ID_CLIENTE = 2905
						WHERE AL.IID_PLAZA = ".$in_plaza." AND AL.IID_ALMACEN NOT IN (9998, 9999) ORDER BY AL.IID_ALMACEN";
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
}
