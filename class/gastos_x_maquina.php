<?php
/**
* © Argo Almacenadora ®
* Fecha: 2020
* Developer: Diego Altamirano Suarez
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class NominaPagada
{
	/*====================== GRAFICA ======================*/
public function graficaNomina($fecha,$plaza,$tipo,$fil_habilitado) //GRAFICA NOMINA INICIAL.
{
    $mesIni = substr($fecha, 3, 2);
    $anioIni = substr($fecha, 6,4);
    $mesFin = substr($fecha, 14, 2);
    $anioFin = substr($fecha,17, 5);
    //echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;

		$andPlaza = " ";
		switch ($plaza) {
		  	//case 'CORPORATIVO': $andPlaza = "2"; break;
		    case 'CÓRDOBA': $andPlaza = "3"; break;
		    case 'MÉXICO': $andPlaza = "4"; break;
		    case 'GOLFO': $andPlaza = "5"; break;
		    case 'PENINSULA': $andPlaza = "6"; break;
		    case 'PUEBLA': $andPlaza = "7"; break;
		    case 'BAJIO': $andPlaza = "8"; break;
		    case 'OCCIDENTE': $andPlaza = "17"; break;
		    case 'NORESTE': $andPlaza = "18"; break;
		    default: $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 "; break;
		}
		$andTipo = " ";
		if ($tipo == "ALL") {
				$andTipo = "00017,00050,00056,00057,00059,00060,00065,00073,00074,00077,00078,00083,00084,00085,00086,00087,00088,00089,00091,00068, 00009";
		}
		else {
			$andTipo = $tipo;
		}

		if ($fil_habilitado == 'on') {
			$and_habilitado = " AND a.s_tipo_almacen in(5, 15)";
		}
		else {
			$and_habilitado = " AND a.s_tipo_almacen in(2, 3, 6)";
		}


		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT X.PAGADO,
						       Y.PRESUPUESTO,
						       X.PLAZA,
						       X.IID_PLAZA
						FROM (SELECT sum(t.d_cargo) as PAGADO,
                       REPLACE(p.v_razon_social, ' (ARGO)') AS plaza,
                       P.IID_PLAZA
                  from ct_cg_polizas_detalle t, almacen a, plaza p
                  where t.iid_almacen = a.iid_almacen
                   and t.v_cuenta = 5105
                   and t.v_scuenta in ($andTipo)
                   and (to_date(t.i_periodo_mes || '/' ||
                                                   t.i_periodo_anio,
                                                   'mm/yyyy') >=
                                          to_date('".$mesIni."/".$anioIni."', 'mm/yyyy') and
                                          to_date(t.i_periodo_mes || '/' ||
                                                   t.i_periodo_anio,
                                                   'mm/yyyy') <=
                                          to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
                   and p.iid_plaza = t.iid_plaza
                   and t.iid_plaza in (3, 4, 5, 6, 7, 8, 17, 18)
                   and a.iid_almacen not in (9998, 9999)
                   AND a.s_tipo_almacen in (2, 3, 6)
                   group by p.v_razon_social, p.iid_plaza
                   order by p.iid_plaza) X
                   LEFT OUTER JOIN (SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
                           P.IID_PLAZA,
                           P.V_RAZON_SOCIAL
                    FROM OP_MF_PRES_ALMACEN Y,
                                ALMACEN A,
                                PLAZA P
                    WHERE Y.IID_ALMACEN = A.IID_ALMACEN
                                 AND A.IID_PLAZA = P.IID_PLAZA
                                 and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') >=
                                   to_date('".$mesIni."/".$anioFin."', 'mm/yyyy') and
                                   to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') <=
                                   to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
                    GROUP BY P.IID_PLAZA, P.V_RAZON_SOCIAL) Y  ON Y.IID_PLAZA = X.IID_PLAZA
                    INNER JOIN PLAZA PLA ON PLA.IID_PLAZA = X.IID_PLAZA
				WHERE PLA.IID_PLAZA IN (3, 4, 5, 6, 7, 8, 17, 18) ";
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

public function widgetsNomina($fecha,$plaza,$tipo,$fil_habilitado,$almacen) //GRAFICA NOMINA INICIAL.
  {
      $mesIni = substr($fecha, 3, 2);
      $anioIni = substr($fecha, 6,4);
      $mesFin = substr($fecha, 14, 2);
      $anioFin = substr($fecha,17, 5);
      //echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;

  		$andPlaza = " ";
  		switch ($plaza) {
  		  	//case 'CORPORATIVO': $andPlaza = "2"; break;
  		    case 'CÓRDOBA': $andPlaza = "3"; break;
  		    case 'MÉXICO': $andPlaza = "4"; break;
  		    case 'GOLFO': $andPlaza = "5"; break;
  		    case 'PENINSULA': $andPlaza = "6"; break;
  		    case 'PUEBLA': $andPlaza = "7"; break;
  		    case 'BAJIO': $andPlaza = "8"; break;
  		    case 'OCCIDENTE': $andPlaza = "17"; break;
  		    case 'NORESTE': $andPlaza = "18"; break;
  		    default: $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 "; break;
  		}
  		$andTipo = " ";
  		if ($tipo == "ALL") {
  				$andTipo = "00017,00050,00056,00057,00059,00060,00065,00073,00074,00077,00078,00083,00084,00085,00086,00087,00088,00089,00091,00068, 00009";
  		}
  		else {
  			$andTipo = $tipo;
  		}

  		if ($fil_habilitado == 'on') {
  			$and_habilitado = " AND a.s_tipo_almacen in(5, 15)";
  		}
  		else {
  			$and_habilitado = " AND a.s_tipo_almacen in(2, 3, 6)";
  		}

      if ($almacen <> "ALL") {
        $and_almacen = " AND A.IID_ALMACEN =".$almacen. " ";

      }else {

        $and_almacen = "";
      }


  		$conn = conexion::conectar();
  		$res_array = array();
      if ($almacen == 1 || $almacen == 2 || $almacen == 3) {
        $sql = "SELECT X.PAGADO, Y.PRESUPUESTO FROM (
                        (select SUM(t.d_cargo) as PAGADO, 'PEÑUELA' AS ALMACEN, 'PEÑU' AS IID
                           from ct_cg_polizas_detalle t, almacen a, plaza p
                          where t.iid_almacen = a.iid_almacen
                            and t.v_cuenta = 5105
                            and t.v_scuenta in
                                ($andTipo)
                            and (TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') >= to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')
         						        and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') <= to_date('".$mesFin."/".$anioFin."', 'mm/yyyy')
         						        )
                            and p.iid_plaza = t.iid_plaza
                            and t.iid_plaza in ($andPlaza)
                            and a.iid_almacen not in (9998, 9999)
                            $and_habilitado
                            and (a.v_nombre LIKE '%PEÑUELA%' OR a.v_Direccion LIKE '%02040%' OR
                                A.V_DIRECCION LIKE '%VICTORIA II%'
                                OR A.V_NOMBRE LIKE '%PANTACO%')
                          GROUP BY 'PANTACO'))X
                RIGHT OUTER JOIN (
                SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
                                          'PEÑUELA' AS ALMACEN,
                                          'PEÑU' AS IID
                                     FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
                                    WHERE Y.IID_ALMACEN = A.IID_ALMACEN
                                      AND A.IID_PLAZA = P.IID_PLAZA
                                      and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') >=
    						                          to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')  and
    						                          to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') <=
    						                          to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
                                      and P.iid_plaza in ($andPlaza)
                                      and a.iid_almacen not in (9998, 9999)
                                      $and_habilitado
                                      and (a.v_nombre LIKE '%PEÑUELA%' OR
                                          a.v_Direccion LIKE '%02040%' OR
                                          a.V_DIRECCION LIKE '%VICTORIA II%'
                                          OR A.V_NOMBRE LIKE '%PANTACO%')) Y ON X.ALMACEN = Y.ALMACEN";
                                          #echo $sql;
      }else {
  		      $sql = "SELECT SUM(X.PAGADO) AS PAGADO,
  						       SUM(Y.PRESUPUESTO) AS PRESUPUESTO
  						FROM (SELECT sum(t.d_cargo) as PAGADO,
                         REPLACE(p.v_razon_social, ' (ARGO)') AS plaza,
                         P.IID_PLAZA
                    from ct_cg_polizas_detalle t, almacen a, plaza p
                    where t.iid_almacen = a.iid_almacen
                     and t.v_cuenta = 5105
                     and t.v_scuenta in ($andTipo)
                     and (to_date(t.i_periodo_mes || '/' ||
                                                     t.i_periodo_anio,
                                                     'mm/yyyy') >=
                                            to_date('".$mesIni."/".$anioIni."', 'mm/yyyy') and
                                            to_date(t.i_periodo_mes || '/' ||
                                                     t.i_periodo_anio,
                                                     'mm/yyyy') <=
                                            to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
                     and p.iid_plaza = t.iid_plaza
                     and t.iid_plaza in ($andPlaza)
                     and a.iid_almacen not in (9998, 9999)
                     $and_habilitado
                     $and_almacen
                     group by p.v_razon_social, p.iid_plaza
                     order by p.iid_plaza) X
                     LEFT OUTER JOIN (SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
                             P.IID_PLAZA,
                             P.V_RAZON_SOCIAL
                      FROM OP_MF_PRES_ALMACEN Y,
                                  ALMACEN A,
                                  PLAZA P
                      WHERE Y.IID_ALMACEN = A.IID_ALMACEN
                                   AND A.IID_PLAZA = P.IID_PLAZA
                                   AND P.iid_plaza IN ($andPlaza)
                                   $and_almacen
                                   and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') >=
                                     to_date('".$mesIni."/".$anioFin."', 'mm/yyyy') and
                                     to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') <=
                                     to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
                      GROUP BY P.IID_PLAZA, P.V_RAZON_SOCIAL) Y  ON Y.IID_PLAZA = X.IID_PLAZA";

        }
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


public function graficaPorMes($fecha,$plaza,$tipo,$almacen,$fil_habilitado)// GRAFICA POR MES
{
		 $andFecha = substr($fecha, 6,4);
		 $andFecha2 = $andFecha-1;
		 $andFecha3 = $andFecha-2;
		 $in_plaza = "2,3,4,5,6,7,8,17,18";
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
		 $andTipo = " ";
		 if ($tipo == "ALL") {
				 $andTipo = "00017,00050,00056,00057,00059,00060,00065,00073,00074,00077,00078,00083,00084,00085,00086,00087,00088,00089,00091,00068, 00009";
		 }
		 else {
			 $andTipo = $tipo;
		 }
		 if ($almacen=="ALL") {
		 		$in_almacen = "";
		 }elseif ($almacen == 1) {
		  $in_almacen = " AND a.v_nombre like '%PEÑUELA%'";
		}elseif ($almacen == 2) {
			$in_almacen = " AND (a.V_DIRECCION LIKE '%02040%' OR A.V_NOMBRE LIKE '%(PANTACO %' )";
		}elseif ($almacen == 3) {
			$in_almacen = " AND a.V_DIRECCION LIKE '%VICTORIA II%'";
		}else {
		 		$in_almacen = "AND A.IID_ALMACEN = $almacen";
		 }

		 if ($fil_habilitado == 'on') {
 			$and_habilitado = " AND a.s_tipo_almacen in(5, 15) ";
 		}
 		else {
 			$and_habilitado = " AND a.s_tipo_almacen in(2, 3, 6) ";
 		}

		 $conn = conexion::conectar();
		 $res_array = array();

		 $sql = "SELECT pla.n_mes,
       			pla.mes ,
			       ( SELECT NVL( SUM(T.D_CARGO),0 )
               FROM CT_CG_POLIZAS_DETALLE T, ALMACEN A, PLAZA P
               WHERE T.IID_ALMACEN = A.IID_ALMACEN AND T.V_CUENTA = 5105 AND T.V_SCUENTA in($andTipo)
               AND T.I_PERIODO_ANIO = $andFecha AND T.I_PERIODO_MES = PLA.N_MES
							 $in_almacen
               AND P.IID_PLAZA = T.IID_PLAZA
							 AND A.IID_ALMACEN NOT IN (9998, 9999)
							 $and_habilitado
               AND T.IID_PLAZA IN ($in_plaza)) AS PAGADO,
							 ( SELECT NVL( SUM(T.D_CARGO),0 )
	               FROM CT_CG_POLIZAS_DETALLE T, ALMACEN A, PLAZA P
	               WHERE T.IID_ALMACEN = A.IID_ALMACEN AND T.V_CUENTA = 5105 AND T.V_SCUENTA in($andTipo)
	               AND T.I_PERIODO_ANIO = $andFecha2 AND T.I_PERIODO_MES = PLA.N_MES
								 $in_almacen
	               AND P.IID_PLAZA = T.IID_PLAZA
								 AND A.IID_ALMACEN NOT IN (9998, 9999)
								 $and_habilitado
	               AND T.IID_PLAZA IN ($in_plaza)) AS PAGADO2,
								 ( SELECT NVL( SUM(T.D_CARGO),0 )
		               FROM CT_CG_POLIZAS_DETALLE T, ALMACEN A, PLAZA P
		               WHERE T.IID_ALMACEN = A.IID_ALMACEN AND T.V_CUENTA = 5105 AND T.V_SCUENTA in($andTipo)
		               AND T.I_PERIODO_ANIO = $andFecha3 AND T.I_PERIODO_MES = PLA.N_MES
									 $in_almacen
		               AND P.IID_PLAZA = T.IID_PLAZA
									 AND A.IID_ALMACEN NOT IN (9998, 9999)
									 $and_habilitado
		               AND T.IID_PLAZA IN ($in_plaza)) AS PAGADO3,
									 (SELECT NVL(SUM(Y.PRESUPUESTO), 0)
                   FROM OP_MF_PRES_ALMACEN Y,
                        ALMACEN A,
                        PLAZA P
                   WHERE Y.IID_ALMACEN = A.IID_ALMACEN
                         AND Y.ANIO = $andFecha
                         AND Y.MES = PLA.N_MES
												 $in_almacen
                         AND P.IID_PLAZA = A.IID_PLAZA
                         AND A.IID_ALMACEN NOT IN (9998, 9999)
												 $and_habilitado
                         AND A.S_TIPO_ALMACEN IN(2, 3, 6)
                         AND P.IID_PLAZA IN ($in_plaza)) AS PRESUPUESTO1,
										(SELECT NVL(SUM(Y.PRESUPUESTO), 0)
		                     FROM OP_MF_PRES_ALMACEN Y,
		                          ALMACEN A,
		                          PLAZA P
		                     WHERE Y.IID_ALMACEN = A.IID_ALMACEN
		                           AND Y.ANIO = $andFecha2
		                           AND Y.MES = PLA.N_MES
		  												 $in_almacen
		                           AND P.IID_PLAZA = A.IID_PLAZA
		                           AND A.IID_ALMACEN NOT IN (9998, 9999)
		  												 $and_habilitado
		                           AND A.S_TIPO_ALMACEN IN(2, 3, 6)
		                           AND P.IID_PLAZA IN ($in_plaza)) AS PRESUPUESTO2,
										(SELECT NVL(SUM(Y.PRESUPUESTO), 0)
					 		                     FROM OP_MF_PRES_ALMACEN Y,
					 		                          ALMACEN A,
					 		                          PLAZA P
					 		                     WHERE Y.IID_ALMACEN = A.IID_ALMACEN
					 		                           AND Y.ANIO = $andFecha3
					 		                           AND Y.MES = PLA.N_MES
					 		  												 $in_almacen
					 		                           AND P.IID_PLAZA = A.IID_PLAZA
					 		                           AND A.IID_ALMACEN NOT IN (9998, 9999)
					 		  												 $and_habilitado
					 		                           AND A.S_TIPO_ALMACEN IN(2, 3, 6)
					 		                           AND P.IID_PLAZA IN ($in_plaza)) AS PRESUPUESTO3
										 FROM RH_MESES_GRAFICAS pla
										 ORDER BY pla.n_mes";
						$stid = oci_parse($conn, $sql);
						oci_execute($stid);

						while (($row = oci_fetch_assoc($stid)) != false)
						{
							 				$res_array[]= $row;
						}

						oci_free_statement($stid);
						oci_close($conn);
						//echo $sql;
						return $res_array;
	}
	/*====================== /*GRAFICA DE NOMINA PAGADA ======================*/

public function graficaPlazaAlmacen($fecha,$plaza,$tipo,$fil_habilitado) //grafica por almacen
{
		$mesIni = substr($fecha, 3, 2);
		$anioIni = substr($fecha, 6,4);
		$mesFin = substr($fecha, 14, 2);
		$anioFin = substr($fecha,17, 5);
		//echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;

	 $andPlaza = " ";
	 switch ($plaza) {
			 //case 'CORPORATIVO': $andPlaza = "2"; break;
			 case 'CÓRDOBA': $andPlaza = "3"; break;
			 case 'MÉXICO': $andPlaza = "4"; break;
			 case 'GOLFO': $andPlaza = "5"; break;
			 case 'PENINSULA': $andPlaza = "6"; break;
			 case 'PUEBLA': $andPlaza = "7"; break;
			 case 'BAJIO': $andPlaza = "8"; break;
			 case 'OCCIDENTE': $andPlaza = "17"; break;
			 case 'NORESTE': $andPlaza = "18"; break;
			 default: $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 "; break;
	 }
	 $andTipo = " ";
	 if ($tipo == "ALL") {
			 $andTipo = "00017,00050,00056,00057,00059,00060,00065,00073,00074,00077,00078,00083,00084,00085,00086,00087,00088,00089,00091,00068, 00009";
	 }
	 else {
		 $andTipo = $tipo;
	 }

	 if ($fil_habilitado == 'on') {
	 	$and_habilitado = " AND a.s_tipo_almacen in(5, 15)";
	 }
	 else {
	 	$and_habilitado = " AND a.s_tipo_almacen in(2, 3, 6)";
	 }

	 $conn = conexion::conectar();
	 $res_array = array();
	 if ($plaza == 'MÉXICO') {
		 $sql = "SELECT SUM(X.PAGADO) AS PAGADO, X.ALMACEN, y.presupuesto
						  FROM (select t.d_cargo as PAGADO,
						               a.v_nombre AS ALMACEN
						          from ct_cg_polizas_detalle t, almacen a, plaza p
						         where t.iid_almacen = a.iid_almacen
						           and t.v_cuenta = 5105
						           and t.v_scuenta in
						               ($andTipo)
						           and (TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') >= to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')
						               and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') <= to_date('".$mesFin."/".$anioFin."', 'mm/yyyy')
						               )
						           and p.iid_plaza = t.iid_plaza
						           and t.iid_plaza in ($andPlaza)
						           and a.iid_almacen not in (9998, 9999)
						           $and_habilitado
						           and (a.v_nombre NOT LIKE '%PEÑUELA%' AND
						               a.v_Direccion NOT LIKE '%02040%'
                           OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
						        UNION ALL
						        select t.d_cargo as PAGADO,
						               'PANTACO' AS ALMACEN
						          from ct_cg_polizas_detalle t, almacen a, plaza p
						         where t.iid_almacen = a.iid_almacen
						           and t.v_cuenta = 5105
						           and t.v_scuenta in
						               ($andTipo)
						           and (TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') >= to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')
						               and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') <= to_date('".$mesFin."/".$anioFin."', 'mm/yyyy')
						               )
						           and p.iid_plaza = t.iid_plaza
						           and t.iid_plaza in ($andPlaza)
						           and a.iid_almacen not in (9998, 9999)
						           $and_habilitado
						           and (a.v_nombre LIKE '%PEÑUELA%' OR a.v_Direccion LIKE '%02040%' OR A.V_NOMBRE LIKE '%(PANTACO %')) X
						           LEFT OUTER JOIN (SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
						                          'PEÑUELA' AS ALMACEN
						                     FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
						                    WHERE Y.IID_ALMACEN = A.IID_ALMACEN
						                      AND A.IID_PLAZA = P.IID_PLAZA
						                      and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') >=
						                          to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')  and
						                          to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') <=
						                          to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
						                          and P.iid_plaza in ($andPlaza)
						                          and a.iid_almacen not in (9998, 9999)
						                          $and_habilitado
						                          and (a.v_nombre LIKE '%PEÑUELA%' OR a.v_Direccion LIKE '%02040%' OR A.V_NOMBRE LIKE '%(PANTACO %')
															  UNION ALL
																SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
			 						                          'PEÑUELA' AS ALMACEN
			 						                     FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
			 						                    WHERE Y.IID_ALMACEN = A.IID_ALMACEN
			 						                      AND A.IID_PLAZA = P.IID_PLAZA
			 						                      and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') >=
			 						                          to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')  and
			 						                          to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') <=
			 						                          to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
			 						                          and P.iid_plaza in ($andPlaza)
			 						                          and a.iid_almacen not in (9998, 9999)
			 						                          $and_habilitado
			 						                          and (a.v_nombre NOT LIKE '%PEÑUELA%' OR a.v_Direccion NOT LIKE '%02040%' OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
						                    GROUP BY A.IID_ALMACEN, A.V_NOMBRE) Y ON Y.ALMACEN = X.ALMACEN
						 GROUP BY X.ALMACEN, Y.PRESUPUESTO";
	 }elseif ($plaza == 'CÓRDOBA') {
      $sql = "SELECT SUM(X.PAGADO) AS PAGADO, X.ALMACEN, y.presupuesto
 						  FROM (select t.d_cargo as PAGADO,
 						               a.v_nombre AS ALMACEN
 						          from ct_cg_polizas_detalle t, almacen a, plaza p
 						         where t.iid_almacen = a.iid_almacen
 						           and t.v_cuenta = 5105
 						           and t.v_scuenta in
 						               ($andTipo)
 						           and (TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') >= to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')
 						               and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') <= to_date('".$mesFin."/".$anioFin."', 'mm/yyyy')
 						               )
 						           and p.iid_plaza = t.iid_plaza
 						           and t.iid_plaza in ($andPlaza)
 						           and a.iid_almacen not in (9998, 9999)
 						           $and_habilitado
 						           and (a.v_nombre NOT LIKE '%PEÑUELA%' AND
 						               a.v_Direccion NOT LIKE '%02040%'
                          OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
 						        UNION ALL
 						        select t.d_cargo as PAGADO,
 						               'PENUELA' AS ALMACEN
 						          from ct_cg_polizas_detalle t, almacen a, plaza p
 						         where t.iid_almacen = a.iid_almacen
 						           and t.v_cuenta = 5105
 						           and t.v_scuenta in
 						               ($andTipo)
 						           and (TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') >= to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')
 						               and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') <= to_date('".$mesFin."/".$anioFin."', 'mm/yyyy')
 						               )
 						           and p.iid_plaza = t.iid_plaza
 						           and t.iid_plaza in ($andPlaza)
 						           and a.iid_almacen not in (9998, 9999)
 						           $and_habilitado
 						           and (a.v_nombre LIKE '%PEÑUELA%' OR a.v_Direccion LIKE '%02040%' OR A.V_NOMBRE LIKE '%(PANTACO %')) X
 						           LEFT OUTER JOIN ( SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
 						                          'PEÑUELA' AS ALMACEN
 						                     FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
 						                    WHERE Y.IID_ALMACEN = A.IID_ALMACEN
 						                      AND A.IID_PLAZA = P.IID_PLAZA
 						                      and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') >=
 						                          to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')  and
 						                          to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') <=
 						                          to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
 						                          and P.iid_plaza in ($andPlaza)
 						                          and a.iid_almacen not in (9998, 9999)
 						                          $and_habilitado
 						                          and (a.v_nombre LIKE '%PEÑUELA%' OR a.v_Direccion LIKE '%02040%' OR A.V_NOMBRE LIKE '%((PANTACO %')
																UNION ALL
																SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
			  						                          'PEÑUELA' AS ALMACEN
			  						                     FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
			  						                    WHERE Y.IID_ALMACEN = A.IID_ALMACEN
			  						                      AND A.IID_PLAZA = P.IID_PLAZA
			  						                      and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') >=
			  						                          to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')  and
			  						                          to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') <=
			  						                          to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
			  						                          and P.iid_plaza in ($andPlaza)
			  						                          and a.iid_almacen not in (9998, 9999)
			  						                          $and_habilitado
			  						                          and (a.v_nombre NOT LIKE '%PEÑUELA%' OR a.v_Direccion NOT LIKE '%02040%' OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
 						                    GROUP BY A.IID_ALMACEN, A.V_NOMBRE) Y ON Y.ALMACEN = X.ALMACEN
 						 GROUP BY X.ALMACEN, Y.PRESUPUESTO";
	 }elseif ($plaza == 'BAJIO') {
		 $sql = "SELECT SUM(X.PAGADO) AS PAGADO, X.ALMACEN, y.presupuesto
							FROM (select t.d_cargo as PAGADO,
													 a.v_nombre AS ALMACEN
											from ct_cg_polizas_detalle t, almacen a, plaza p
										 where t.iid_almacen = a.iid_almacen
											 and t.v_cuenta = 5105
											 and t.v_scuenta in
													 ($andTipo)
											 and (TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') >= to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')
													 and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') <= to_date('".$mesFin."/".$anioFin."', 'mm/yyyy')
													 )
											 and p.iid_plaza = t.iid_plaza
											 and t.iid_plaza in ($andPlaza)
											 and a.iid_almacen not in (9998, 9999)
											 $and_habilitado
											 and (a.v_nombre NOT LIKE '%PEÑUELA%' AND
													 a.v_Direccion NOT LIKE '%02040%'  OR A.V_DIRECCION NOT LIKE '%VICTORIA II%' OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
										UNION ALL
										select t.d_cargo as PAGADO,
													 'ARGO VICTORIA' AS ALMACEN
											from ct_cg_polizas_detalle t, almacen a, plaza p
										 where t.iid_almacen = a.iid_almacen
											 and t.v_cuenta = 5105
											 and t.v_scuenta in
													 ($andTipo)
											 and (TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') >= to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')
													 and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') <= to_date('".$mesFin."/".$anioFin."', 'mm/yyyy')
													 )
											 and p.iid_plaza = t.iid_plaza
											 and t.iid_plaza in ($andPlaza)
											 and a.iid_almacen not in (9998, 9999)
											 $and_habilitado
											 and (a.v_nombre LIKE '%PEÑUELA%' OR a.v_Direccion LIKE '%02040%' OR A.V_DIRECCION LIKE '%VICTORIA II%' OR A.V_NOMBRE LIKE '%(PANTACO %')) X
											 LEFT OUTER JOIN (SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
																			'PEÑUELA' AS ALMACEN
																 FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
																WHERE Y.IID_ALMACEN = A.IID_ALMACEN
																	AND A.IID_PLAZA = P.IID_PLAZA
																	and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') >=
																			to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')  and
																			to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') <=
																			to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
																			and P.iid_plaza in ($andPlaza)
																			and a.iid_almacen not in (9998, 9999)
																			$and_habilitado
																			and (a.v_nombre LIKE '%PEÑUELA%' OR a.v_Direccion LIKE '%02040%' OR A.V_DIRECCION LIKE '%VICTORIA II%' OR A.V_NOMBRE LIKE '%(PANTACO %')
																UNION ALL
																SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
			 																			'PEÑUELA' AS ALMACEN
			 																 FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
			 																WHERE Y.IID_ALMACEN = A.IID_ALMACEN
			 																	AND A.IID_PLAZA = P.IID_PLAZA
			 																	and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') >=
			 																			to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')  and
			 																			to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') <=
			 																			to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
			 																			and P.iid_plaza in ($andPlaza)
			 																			and a.iid_almacen not in (9998, 9999)
			 																			$and_habilitado
			 																			and (a.v_nombre NOT LIKE '%PEÑUELA%' OR a.v_Direccion NOT LIKE '%02040%' OR A.V_DIRECCION NOT LIKE '%VICTORIA II%' OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
																GROUP BY A.IID_ALMACEN, A.V_NOMBRE) Y ON Y.ALMACEN = X.ALMACEN
						 GROUP BY X.ALMACEN, Y.PRESUPUESTO";
	 }else {
		 $sql = "SELECT SUM(X.PAGADO) AS PAGADO, X.ALMACEN, y.presupuesto
					  FROM (select t.d_cargo as PAGADO,
					               a.v_nombre AS ALMACEN
					          from ct_cg_polizas_detalle t, almacen a, plaza p
					         where t.iid_almacen = a.iid_almacen
					           and t.v_cuenta = 5105
					           and t.v_scuenta in
					               ($andTipo)
					           and (TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') >= to_date('".$mesIni."/".$anioIni."', 'mm/yyyy')
					               and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'mm/yyyy') <= to_date('".$mesFin."/".$anioFin."', 'mm/yyyy')
					               )
					           and p.iid_plaza = t.iid_plaza
					           and t.iid_plaza in ($andPlaza)
					           and a.iid_almacen not in (9998, 9999)
					           $and_habilitado) X
					           LEFT OUTER JOIN (SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
					                          A.V_NOMBRE AS ALMACEN
					                     FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
					                    WHERE Y.IID_ALMACEN = A.IID_ALMACEN
					                      AND A.IID_PLAZA = P.IID_PLAZA
					                      and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') >=
					                          to_date('".$mesIni."/".$anioIni."', 'mm/yyyy') and
					                          to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') <=
					                          to_date('".$mesFin."/".$anioFin."', 'mm/yyyy'))
					                          and P.iid_plaza in ($andPlaza)
					                          and a.iid_almacen not in (9998, 9999)
					                          $and_habilitado
					                    GROUP BY A.IID_ALMACEN, A.V_NOMBRE) Y ON Y.ALMACEN = X.ALMACEN
					 GROUP BY X.ALMACEN, y.presupuesto ";
	 }
						//5 HABILITADO 15 FISCAL HABILITADO
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


public function graficaPlazaAlmacenxmes($fecha,$plaza,$almacen,$mes, $tipo,$fil_habilitado) //grafica por almacen
  {
  		$mesIni = substr($fecha, 3, 2);
  		$anioIni = substr($fecha, 6,4);
  		$mesFin = substr($fecha, 14, 2);
  		$anioFin = substr($fecha,17, 5);
  		//echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;

  	 $andPlaza = " ";
  	 switch ($plaza) {
  			 //case 'CORPORATIVO': $andPlaza = "2"; break;
  			 case 'CÓRDOBA':
            $andPlaza = "3";
            $namer = "'PEÑUELA' AS ALMACEN, 'PEÑU' AS IID ";
            break;
  			 case 'MÉXICO':
            $andPlaza = "4";
            $namer = "'PANTACO' AS ALMACEN, 'PANT' AS IID ";
            break;
  			 case 'GOLFO': $andPlaza = "5"; break;
  			 case 'PENINSULA': $andPlaza = "6"; break;
  			 case 'PUEBLA': $andPlaza = "7"; break;
  			 case 'BAJIO':
            $andPlaza = "8";
            $namer = "'VICTORIA' AS ALMACEN, 'VICT' AS IID ";
            break;
  			 case 'OCCIDENTE': $andPlaza = "17"; break;
  			 case 'NORESTE': $andPlaza = "18"; break;
  			 default: $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 "; break;
  	 }
  	 $andTipo = " ";
  	 if ($tipo == "ALL") {
  			 $andTipo = "00017,00050,00056,00057,00059,00060,00065,00073,00074,00077,00078,00083,00084,00085,00086,00087,00088,00089,00091,00068, 00009";
  	 }
  	 else {
  		 $andTipo = $tipo;
  	 }

  	 if ($fil_habilitado == 'on') {
  	 	$and_habilitado = " AND a.s_tipo_almacen in(5, 15)";
  	 }
  	 else {
  	 	$and_habilitado = " AND a.s_tipo_almacen in(2, 3, 6)";
  	 }

  	 $conn = conexion::conectar();
  	 $res_array = array();
     if ($plaza == 'CÓRDOBA' || $plaza == 'MÉXICO' || $plaza == 'BAJIO') {
       if ($almacen == 'TODOS') {
         $sql = "SELECT GASTO.IID, GASTO.PAGADO AS PAGADO, GASTO.ALMACEN, Y.PRESUPUESTO FROM ((SELECT SUM(t.d_cargo) as PAGADO, a.v_nombre AS ALMACEN, to_char(a.iid_almacen) as iid
                                                     from ct_cg_polizas_detalle t, almacen a, plaza p
                                                    where t.iid_almacen = a.iid_almacen
                                                      and t.v_cuenta = 5105
                                                      and t.v_scuenta in
                                                          ($andTipo)
                                                      and (TO_DATE(t.i_periodo_anio,
                                                                   'yyyy') = to_date('".$anioFin."', 'yyyy'))
                                                      and p.iid_plaza = t.iid_plaza
                                                      and t.iid_plaza in ($andPlaza)
                                                      and a.iid_almacen not in (9998, 9999)
                                                      $and_habilitado
                                                      and (a.v_nombre NOT LIKE '%PEÑUELA%' AND
                                                          a.v_Direccion NOT LIKE '%02040%' AND
                                                          A.V_DIRECCION NOT LIKE '%VICTORIA II%'
                                                         AND A.V_NOMBRE NOT LIKE '%(PANTACO %')
                                                    GROUP BY a.v_nombre, A.iid_almacen) UNION ALL
                                                  (select SUM(t.d_cargo) as PAGADO, $namer
                                                     from ct_cg_polizas_detalle t, almacen a, plaza p
                                                    where t.iid_almacen = a.iid_almacen
                                                      and t.v_cuenta = 5105
                                                      and t.v_scuenta in
                                                          ($andTipo)
                                                      and (TO_DATE(t.i_periodo_anio,
                                                                   'yyyy') = to_date('".$anioFin."', 'yyyy'))
                                                      and p.iid_plaza = t.iid_plaza
                                                      and t.iid_plaza in ($andPlaza)
                                                      and a.iid_almacen not in (9998, 9999)
                                                      $and_habilitado
                                                      and (a.v_nombre LIKE '%PEÑUELA%' OR a.v_Direccion LIKE '%02040%' OR
                                                          A.V_DIRECCION LIKE '%VICTORIA II%'
                                                         and a.V_DIRECCION LIKE '%VICTORIA II%'
                                                         OR A.V_NOMBRE LIKE '%(PANTACO %')
                                                    GROUP BY 'PANTACO')) GASTO
                    LEFT OUTER JOIN (SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
                                              $namer
                                         FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
                                        WHERE Y.IID_ALMACEN = A.IID_ALMACEN
                                          AND A.IID_PLAZA = P.IID_PLAZA
                                          and (to_date(Y.anio, 'yyyy') = to_date('".$anioFin."', 'yyyy'))
                                          and P.iid_plaza in ($andPlaza)
                                          and a.iid_almacen not in (9998, 9999)
                                          $and_habilitado
                                          and (a.v_nombre LIKE '%PEÑUELA%' OR
                                              a.v_Direccion LIKE '%02040%'
                                             OR a.V_DIRECCION  LIKE '%VICTORIA II%'
                                             OR A.V_NOMBRE LIKE '%(PANTACO %')
                                       UNION ALL
                                       SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
                                              A.V_NOMBRE AS ALMACEN,
                                              TO_CHAR(A.IID_ALMACEN) AS IID
                                         FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
                                        WHERE Y.IID_ALMACEN = A.IID_ALMACEN
                                          AND A.IID_PLAZA = P.IID_PLAZA
                                          and (to_date( Y.anio, 'yyyy') = to_date('".$anioFin."', 'yyyy'))
                                          and P.iid_plaza in ($andPlaza)
                                          and a.iid_almacen not in (9998, 9999)
                                          $and_habilitado
                                          and (a.v_nombre NOT LIKE '%PEÑUELA%' OR
                                              a.v_Direccion NOT LIKE '%02040%'
                                             and a.V_DIRECCION NOT LIKE '%VICTORIA II%'
                                             AND A.V_NOMBRE NOT LIKE '%(PANTACO %')
                                        GROUP BY A.IID_ALMACEN, A.V_NOMBRE) Y ON Y.ALMACEN = GASTO.ALMACEN";
                                        #echo $sql;
       }
       if ($almacen == 'PEÑU' || $almacen == 'PANT' || $almacen == 'VICT') {
              $sql = "SELECT X.PAGADO, Y.PRESUPUESTO FROM (
                              (select SUM(t.d_cargo) as PAGADO, $namer
                                 from ct_cg_polizas_detalle t, almacen a, plaza p
                                where t.iid_almacen = a.iid_almacen
                                  and t.v_cuenta = 5105
                                  and t.v_scuenta in
                                      ($andTipo)
                                  and (TO_DATE(t.i_periodo_mes || '/' || t.i_periodo_anio, 'mm/yyyy') = to_date('".$mes."/".$anioFin."', 'mm/yyyy'))
                                  and p.iid_plaza = t.iid_plaza
                                  and t.iid_plaza in ($andPlaza)
                                  and a.iid_almacen not in (9998, 9999)
                                  $and_habilitado
                                  and (a.v_nombre LIKE '%PEÑUELA%' OR a.v_Direccion LIKE '%02040%' OR
                                      A.V_DIRECCION LIKE '%VICTORIA II%'
                                    OR A.V_NOMBRE LIKE '%(PANTACO %')
                                GROUP BY 'PANTACO'))X
                      LEFT OUTER JOIN (
                      SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
                                                $namer
                                           FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
                                          WHERE Y.IID_ALMACEN = A.IID_ALMACEN
                                            AND A.IID_PLAZA = P.IID_PLAZA
                                            and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') =
                                                to_date('".$mes."/".$anioFin."', 'mm/yyyy'))
                                            and P.iid_plaza in ($andPlaza)
                                            and a.iid_almacen not in (9998, 9999)
                                            $and_habilitado
                                            and (a.v_nombre LIKE '%PEÑUELA%' OR
                                                a.v_Direccion LIKE '%02040%' OR
                                                a.V_DIRECCION LIKE '%VICTORIA II%'
                                              OR A.V_NOMBRE LIKE '%(PANTACO %')) Y ON X.ALMACEN = Y.ALMACEN";
                                              #echo $sql;
       }elseif ($almacen != 'PEÑU' && $almacen != 'PANT' && $almacen != 'VICT' && $almacen != 'TODOS'){
         $sql = "SELECT GASTO.IID, GASTO.PAGADO AS PAGADO, GASTO.ALMACEN, Y.PRESUPUESTO
                  FROM ((SELECT SUM(t.d_cargo) as PAGADO,
                                a.v_nombre AS ALMACEN,
                                to_char(a.iid_almacen) as iid
                           from ct_cg_polizas_detalle t, almacen a, plaza p
                          where t.iid_almacen = a.iid_almacen
                            and t.v_cuenta = 5105
                            and t.v_scuenta in
                                ($andTipo)
                            and (TO_DATE(t.i_periodo_mes || '/' || t.i_periodo_anio,
                                         'mm/yyyy') = to_date('".$mes."/".$anioFin."', 'mm/yyyy'))
                            and p.iid_plaza = t.iid_plaza
                            and t.iid_plaza in ($andPlaza)
                            AND A.IID_ALMACEN = $almacen
                            $and_habilitado
                            AND a.s_tipo_almacen in (2, 3, 6)
                            and (a.v_nombre NOT LIKE '%PEÑUELA%' AND
                                a.v_Direccion NOT LIKE '%02040%' AND
                                A.V_DIRECCION NOT LIKE '%VICTORIA II%'
                              OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
                          GROUP BY a.v_nombre, A.iid_almacen)) GASTO
                  LEFT OUTER JOIN (SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
                                          A.V_NOMBRE AS ALMACEN,
                                          TO_CHAR(A.IID_ALMACEN) AS IID
                                     FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
                                    WHERE Y.IID_ALMACEN = A.IID_ALMACEN
                                      AND A.IID_PLAZA = P.IID_PLAZA
                                      and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') =
                                          to_date('".$mes."/".$anioFin."', 'mm/yyyy'))
                                      and P.iid_plaza in ($andPlaza)
                                      AND A.IID_ALMACEN = $almacen
                                      and a.iid_almacen not in (9998, 9999)
                                      $and_habilitado
                                      and (a.v_nombre NOT LIKE '%PEÑUELA%' OR
                                          a.v_Direccion NOT LIKE '%02040%' and
                                          a.V_DIRECCION NOT LIKE '%VICTORIA II%'
                                        OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
                                    GROUP BY A.IID_ALMACEN, A.V_NOMBRE) Y ON Y.ALMACEN = GASTO.ALMACEN";

       }
    }else {
      if ($almacen == "TODOS") {
        $sql = "SELECT GASTO.IID, GASTO.PAGADO AS PAGADO, GASTO.ALMACEN, Y.PRESUPUESTO
                 FROM ((SELECT SUM(t.d_cargo) as PAGADO,
                               a.v_nombre AS ALMACEN,
                               to_char(a.iid_almacen) as iid
                          from ct_cg_polizas_detalle t, almacen a, plaza p
                         where t.iid_almacen = a.iid_almacen
                           and t.v_cuenta = 5105
                           and t.v_scuenta in
                               ($andTipo)
                           and (TO_DATE(t.i_periodo_anio,
                                        'yyyy') = to_date('".$anioFin."', 'yyyy'))
                           and p.iid_plaza = t.iid_plaza
                           and t.iid_plaza in ($andPlaza)
                           $and_habilitado
                           AND a.s_tipo_almacen in (2, 3, 6)
                           and a.iid_almacen not in (9998, 9999)
                           and (a.v_nombre NOT LIKE '%PEÑUELA%' AND
                               a.v_Direccion NOT LIKE '%02040%' AND
                               A.V_DIRECCION NOT LIKE '%VICTORIA II%'
                             OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
                         GROUP BY a.v_nombre, A.iid_almacen)) GASTO
                 LEFT OUTER JOIN (SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
                                         A.V_NOMBRE AS ALMACEN,
                                         TO_CHAR(A.IID_ALMACEN) AS IID
                                    FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
                                   WHERE Y.IID_ALMACEN = A.IID_ALMACEN
                                     AND A.IID_PLAZA = P.IID_PLAZA
                                     and (to_date(Y.anio, 'yyyy') =
                                         to_date('".$anioFin."', 'yyyy'))
                                     and P.iid_plaza in ($andPlaza)
                                     and a.iid_almacen not in (9998, 9999)
                                     $and_habilitado
                                     and (a.v_nombre NOT LIKE '%PEÑUELA%' OR
                                         a.v_Direccion NOT LIKE '%02040%' and
                                         a.V_DIRECCION NOT LIKE '%VICTORIA II%'
                                       OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
                                   GROUP BY A.IID_ALMACEN, A.V_NOMBRE) Y ON Y.ALMACEN = GASTO.ALMACEN";
                                   #echo $sql;
      }
      else {
        $sql = "SELECT GASTO.IID, GASTO.PAGADO AS PAGADO, GASTO.ALMACEN, Y.PRESUPUESTO
                 FROM ((SELECT SUM(t.d_cargo) as PAGADO,
                               a.v_nombre AS ALMACEN,
                               to_char(a.iid_almacen) as iid
                          from ct_cg_polizas_detalle t, almacen a, plaza p
                         where t.iid_almacen = a.iid_almacen
                           and t.v_cuenta = 5105
                           and t.v_scuenta in
                               ($andTipo)
                           and (TO_DATE(t.i_periodo_mes || '/' || t.i_periodo_anio,
                                        'mm/yyyy') = to_date('".$mes."/".$anioFin."', 'mm/yyyy'))
                           and p.iid_plaza = t.iid_plaza
                           and t.iid_plaza in ($andPlaza)
                           AND A.IID_ALMACEN = $almacen
                           $and_habilitado
                           AND a.s_tipo_almacen in (2, 3, 6)
                           and a.iid_almacen not in (9998, 9999)
                           and (a.v_nombre NOT LIKE '%PEÑUELA%' AND
                               a.v_Direccion NOT LIKE '%02040%' AND
                               A.V_DIRECCION NOT LIKE '%VICTORIA II%'
                              OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
                         GROUP BY a.v_nombre, A.iid_almacen)) GASTO
                 LEFT OUTER JOIN (SELECT SUM(Y.PRESUPUESTO) AS PRESUPUESTO,
                                         A.V_NOMBRE AS ALMACEN,
                                         TO_CHAR(A.IID_ALMACEN) AS IID
                                    FROM OP_MF_PRES_ALMACEN Y, ALMACEN A, PLAZA P
                                   WHERE Y.IID_ALMACEN = A.IID_ALMACEN
                                     AND A.IID_PLAZA = P.IID_PLAZA
                                     and (to_date(Y.mes || '/' || Y.anio, 'mm/yyyy') =
                                         to_date('".$mes."/".$anioFin."', 'mm/yyyy'))
                                     and P.iid_plaza in ($andPlaza)
                                     AND A.IID_ALMACEN = $almacen
                                     and a.iid_almacen not in (9998, 9999)
                                     $and_habilitado
                                     and (a.v_nombre NOT LIKE '%PEÑUELA%' OR
                                         a.v_Direccion NOT LIKE '%02040%' and
                                         a.V_DIRECCION NOT LIKE '%VICTORIA II%'
                                         OR A.V_NOMBRE NOT LIKE '%(PANTACO %')
                                   GROUP BY A.IID_ALMACEN, A.V_NOMBRE) Y ON Y.ALMACEN = GASTO.ALMACEN";
      }
    }
                                    //  echo $sql;

     if ($almacen == 'PANT') {
       #echo $sql;
     }
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

	/*====================== TABLA DE NOMINA PAGADA ======================*/

public function detalleGastos($fecha,$plaza,$tipo,$almacen,$fil_habilitado)
{

		$mesIni = substr($fecha, 3, 2);
		$anioIni = substr($fecha, 6,4);
		$mesFin = substr($fecha, 14, 2);
		$anioFin = substr($fecha,17, 5);
		//echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;
		$anioIni2 = $anioIni -1;
		$anioIni3 = $anioIni -2;
	 $andPlaza = " ";
	 switch ($plaza) {
			 //case 'CORPORATIVO': $andPlaza = "2"; break;
			 case 'CÓRDOBA': $andPlaza = "3"; break;
			 case 'MÉXICO': $andPlaza = "4"; break;
			 case 'GOLFO': $andPlaza = "5"; break;
			 case 'PENINSULA': $andPlaza = "6"; break;
			 case 'PUEBLA': $andPlaza = "7"; break;
			 case 'BAJIO': $andPlaza = "8"; break;
			 case 'OCCIDENTE': $andPlaza = "17"; break;
			 case 'NORESTE': $andPlaza = "18"; break;
			 default: $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 "; break;
	 }
	 $andTipo = " ";
	 if ($tipo == "ALL") {
			 $andTipo = "00017,00050,00056,00057,00059,00060,00065,00073,00074,00077,00078,00083,00084,00085,00086,00087,00088,00089,00091,00068, 00009";  // SE AGREGARON 068 Y 9
	 }
	 else {
		 $andTipo = $tipo;
	 }

	 if ($fil_habilitado == 'on') {
	 	$and_habilitado = " AND a.s_tipo_almacen in(5, 15)";
	 }
	 else {
	 	$and_habilitado = " AND a.s_tipo_almacen in(2, 3, 6)";
	 }
	 $andaid_almacen = '';
	 if ($almacen == 1) {
	 		$andaid_almacen = " AND a.v_nombre like '%PEÑUELA%'";

	 }elseif ($almacen == 2) {
	 		$andaid_almacen = " AND (a.V_DIRECCION LIKE '%02040%' OR A.V_NOMBRE LIKE '%(PANTACO %' )";

	 }elseif ($almacen == 3) {
		 	$andaid_almacen = " AND a.V_DIRECCION LIKE '%VICTORIA II%'";

	 } elseif($almacen != 'ALL' AND $almacen != 2 AND $almacen != 1 AND $almacen != 3) {
	 		$andaid_almacen = ' AND a.iid_almacen = '.$almacen;

	 }else {

	 }

   $GROUP = "GROUP BY DESCRIPCION";

	 $conn = conexion::conectar();
	 $res_array = array();
	 $sql = "SELECT SUM(PAGADO1)AS PAGO1, SUM(PAGADO2) AS PAGO2, SUM(PAGADO3) AS PAGO3, SUM(PAGADO4) AS PAGO4, DESCRIPCION FROM (select t.d_cargo as PAGADO1,
	        0.00 AS PAGADO2,
	        0.00 AS PAGADO3,
          0.00 AS PAGADO4,
	        UPPER(ct.v_descripcion) AS DESCRIPCION,
	        a.iid_almacen
	 from ct_cg_polizas_detalle t,
	      almacen a,
	      plaza p ,
	      CT_CG_CAT_CUENTAS CT
	 where t.iid_almacen = a.iid_almacen
	       and t.v_cuenta = ct.v_cuenta
	       and t.v_scuenta = ct.v_scuenta
	       and t.v_sscuenta = ct.v_sscuenta
	       and t.v_cuenta = 5105
	       and t.v_scuenta in($andTipo)
	       and (TO_DATE(t.i_periodo_mes  || '/' || t.i_periodo_anio, 'MM/YYYY') >= TO_DATE('".$mesIni."/".$anioIni."', 'MM/YYYY' )
				 			and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'MM/YYYY') <= TO_DATE('".$mesFin."/".$anioIni."', 'MM/YYYY'))
	       and p.iid_plaza = t.iid_plaza
	       and t.iid_plaza in($andPlaza)
	       and a.iid_almacen not in (9998, 9999)
	       $and_habilitado $andaid_almacen
	 union all
	       select
	       0.00 as PAGADO1,
	        t.d_cargo as PAGADO2,
	        0.00 AS PAGADO3,
          0.00 AS PAGADO4,
	        UPPER(ct.v_descripcion) AS DESCRIPCION,
	        a.iid_almacen
	 from ct_cg_polizas_detalle t,
	      almacen a,
	      plaza p ,
	      CT_CG_CAT_CUENTAS CT
	 where t.iid_almacen = a.iid_almacen
	       and t.v_cuenta = ct.v_cuenta
	       and t.v_scuenta = ct.v_scuenta
	       and t.v_sscuenta = ct.v_sscuenta
	       and t.v_cuenta = 5105
	       and t.v_scuenta in($andTipo)
				 and (TO_DATE(t.i_periodo_mes  || '/' || t.i_periodo_anio, 'MM/YYYY') >= TO_DATE('".$mesIni."/".$anioIni2."', 'MM/YYYY' )
				 			and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'MM/YYYY') <= TO_DATE('".$mesFin."/".$anioIni2."', 'MM/YYYY'))
	       and p.iid_plaza = t.iid_plaza
	       and t.iid_plaza in($andPlaza)
	       and a.iid_almacen not in (9998, 9999)
	       $and_habilitado $andaid_almacen
	 union all
	       select
	       0.00 as PAGADO1,
	       0.00 AS PAGADO2,
	        t.d_cargo as PAGADO3,
          0.00 AS PAGADO4,
	        UPPER(ct.v_descripcion) AS DESCRIPCION,
	        a.iid_almacen
	 from ct_cg_polizas_detalle t,
	      almacen a,
	      plaza p ,
	      CT_CG_CAT_CUENTAS CT
	 where t.iid_almacen = a.iid_almacen
	       and t.v_cuenta = ct.v_cuenta
	       and t.v_scuenta = ct.v_scuenta
	       and t.v_sscuenta = ct.v_sscuenta
	       and t.v_cuenta = 5105
	       and t.v_scuenta in($andTipo)
				 and (TO_DATE(t.i_periodo_mes  || '/' || t.i_periodo_anio, 'MM/YYYY') >= TO_DATE('".$mesIni."/".$anioIni3."', 'MM/YYYY' )
				 			and TO_DATE(t.i_periodo_mes|| '/' || t.i_periodo_anio, 'MM/YYYY') <= TO_DATE('".$mesFin."/".$anioIni3."', 'MM/YYYY'))
	       and p.iid_plaza = t.iid_plaza
	       and t.iid_plaza in($andPlaza)
	       and a.iid_almacen not in (9998, 9999)
	       $and_habilitado $andaid_almacen
    union all
      	       select
      	       0.00 as PAGADO1,
      	       0.00 AS PAGADO2,
      	        0.00 as PAGADO3,
                t.d_cargo as PAGADO4,
      	        UPPER(ct.v_descripcion) AS DESCRIPCION,
      	        a.iid_almacen
      	 from ct_cg_polizas_detalle t,
      	      almacen a,
      	      plaza p ,
      	      CT_CG_CAT_CUENTAS CT
      	 where t.iid_almacen = a.iid_almacen
      	       and t.v_cuenta = ct.v_cuenta
      	       and t.v_scuenta = ct.v_scuenta
      	       and t.v_sscuenta = ct.v_sscuenta
      	       and t.v_cuenta = 5105
      	       and t.v_scuenta in($andTipo)
               AND T.I_PERIODO_ANIO = $anioIni
               AND T.I_PERIODO_MES <= $mesFin
      	       and p.iid_plaza = t.iid_plaza
      	       and t.iid_plaza in($andPlaza)
      	       and a.iid_almacen not in (9998, 9999)
      	       $and_habilitado $andaid_almacen
       )
	       $GROUP";
						//5 HABILITADO 15 FISCAL HABILITADO
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
	/*====================== /*TABLA DE NOMINA PAGADA ======================*/
	/*====================== SQL DINAMICO ======================*/
public function sql($option,$depto,$plaza)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT * FROM DUAL";
		switch ($option) {
			case '1':
				$sql = "SELECT TO_CHAR(TRUNC(SYSDATE, 'MM'), 'DD/MM/YYYY') mes1, TO_CHAR(LAST_DAY( TO_DATE(SYSDATE) ), 'DD/MM/YYYY') mes2 FROM DUAL";
				break;
			case '2':
				$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18) ";
				break;
			case '3':
				$sql = " SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep ";
				break;
			case '4':
				$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
				break;
			case '5':
				$sql = "select v_scuenta as cuenta,UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(17, 50, 56, 57, 59, 60, 65, 73, 74, 77, 78, 83, 84, 85, 86, 88, 89, 91 , 68, 9) and v_sscuenta = 0 and v_cuenta_activa = 1
									union all
									select v_scuenta as cuenta, UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(87) and v_sscuenta = 1";
				break;
			//case '6':
				//break;
			default:
				$sql = "SELECT * FROM DUAL";
				break;
		}

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
	 if ($plaza == 'CÓRDOBA') {
	 		$sql= "SELECT IID_ALMACEN, V_NOMBRE FROM ALMACEN WHERE IID_PLAZA = 3 AND
                  (V_NOMBRE NOT LIKE '%PEÑUELA%' AND S_STATUS = 1 AND IID_ALMACEN NOT IN (9998, 9999))
							UNION
							SELECT 1, 'PEÑUELA' FROM ALMACEN WHERE IID_PLAZA = 3 AND
                  (V_NOMBRE LIKE '%PEÑUELA%' AND S_STATUS = 1 AND IID_ALMACEN NOT IN (9998, 9999))
							GROUP BY IID_ALMACEN
							ORDER BY IID_ALMACEN";
	 }elseif ($plaza == 'MÉXICO'){
		 	$sql = "SELECT IID_ALMACEN, V_NOMBRE FROM ALMACEN WHERE IID_PLAZA = 4 AND
                    (V_DIRECCION NOT LIKE '%02040%' AND S_STATUS = 1 AND IID_ALMACEN NOT IN (9998, 9999) OR ALMACEN.V_NOMBRE NOT LIKE '%(PANTACO %')
							UNION
							SELECT 2, 'PANTACO' FROM ALMACEN WHERE IID_PLAZA = 4 AND
                    (V_DIRECCION LIKE '%02040%' AND S_STATUS = 1 AND IID_ALMACEN NOT IN (9998, 9999) OR ALMACEN.V_NOMBRE LIKE '%(PANTACO %')
							GROUP BY IID_ALMACEN
							ORDER BY IID_ALMACEN";
	 }elseif ($plaza == 'BAJIO') {
		 $sql = "SELECT IID_ALMACEN, V_NOMBRE FROM ALMACEN WHERE IID_PLAZA = 8 AND
                  (V_DIRECCION NOT LIKE '%VICTORIA II%' AND S_STATUS = 1 AND IID_ALMACEN NOT IN (9998, 9999))
						 UNION
						 SELECT 3, 'VICTORIA' FROM ALMACEN WHERE IID_PLAZA = 8 AND
                  (V_DIRECCION LIKE '%VICTORIA II%' AND S_STATUS = 1 AND IID_ALMACEN NOT IN (9998, 9999))
						 GROUP BY IID_ALMACEN
						 ORDER BY IID_ALMACEN";
	 }
	 else {
	 		$sql = "SELECT IID_ALMACEN, V_NOMBRE FROM ALMACEN WHERE IID_PLAZA IN ($in_plaza) AND IID_ALMACEN NOT IN (9998, 9999) and s_status = 1 ORDER BY IID_ALMACEN";
	 }
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
	/*====================== /*SQL DINAMICO ======================*/

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
	/*====================== VALIDA SI ES FECHA  ======================*/
function nombreTipo($tipo){
		$conn = conexion::conectar();
		$res_array = array();
		if ($tipo == 87 ) {
			$sql = "select v_scuenta as cuenta, UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(87) and v_sscuenta = 1";
		}
		else {
			$sql = "select v_scuenta as cuenta,UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in($tipo) and v_sscuenta = 0";
		}
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

function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}
	/*====================== /.VALIDA SI ES FECHA  ======================*/


}
