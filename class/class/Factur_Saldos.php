<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class Factur_Saldos
{

 /* funcion tabla */
	/*====================== /*GRAFICA DE NOMINA PAGADA ======================*/

public function tabla_porcentaje($fecha, $plaza, $almacen){
		#echo $fecha;

		$conn = conexion::conectar();

		$res_array = array();

		$in_plaza = "0";
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
		    default: $in_plaza = 0; break;
		}

		if ($plaza == "ALL") {
			$plaza_val = "3,4,5,6,7,8,17,18";
		}else {
			$plaza_val = $in_plaza;
		}

		if ($almacen == "ALL") {
			$almacen = 0;
			$almacen_ultimo = " ";
		}else {
			$almacen = $almacen;
			$almacen_ultimo = "ADF.IID_ALMACEN = $almacen";
		}

		//$error_mg = "";
		#echo $almacen."  ".$plaza;
		$formatted = vsprintf('%3$04d/%2$02d/%1$02d', sscanf($fecha,'%02d/%02d/%04d'));
    $fechats = strtotime($formatted);
    //echo $fechats;
    switch (date('w', $fechats)){
      case 0: $dia = "Domingo"; break;
      case 1: $dia = "Lunes"; break;
      case 2: $dia = "Martes"; break;
      case 3: $dia = "Miercoles"; break;
      case 4: $dia = "Jueves"; break;
      case 5: $dia = "Viernes"; break;
      case 6: $dia = "Sabado"; break;
    }

    if ($dia == "Sabado") {
      #echo "HERE";
      $fecha = $fecha;
    }else {
      #echo "HERE2";
      $fecha = date("d/m/Y");
      $first = strtotime('last saturday');
      $fecha =  date('d/m/Y', $first);
    }
		#$fecha_n = '03/10/2020';
		//echo $pre.' '.$fecha.' '.$promotor.' '.$plaza;
		$stid = oci_parse($conn, "BEGIN PCK_SALDOS_CLIENTE_X_SEMANA(:fecha, :plaza, :almacen, :error_msg); END;");
		oci_bind_by_name($stid, ':fecha', $fecha); //3
		oci_bind_by_name($stid, ':plaza', $in_plaza); //2019
		oci_bind_by_name($stid, ':almacen', $almacen); // ALL
		oci_bind_by_name($stid, ':error_msg',$error_mg);


		oci_execute($stid);
		#oci_free_statement($stid);

		#echo "El error es". $error_mg;
		//
		#echo $fecha. "   ". $in_plaza. "   ". $almacen;
		#while (($row = oci_fetch_array($curs, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		#		$res_array[]=$row;
		#}

		//oci_free_statement($stid);
		#oci_close($conn);
		$sql = "SELECT Y.IID_NUM_CLIENTE,
										Y.V_RAZON_SOCIAL,
										Y.SALDO_ADEUDADO_SEMANA_1,
										Y.SALDO_ADEUDADO_SEMANA_2 ,
										Y.SALDO_ADEUDADO_SEMANA_3 ,
										Y.SALDO_ADEUDADO_SEMANA_4 ,
										Y.SALDO_ADEUDADO_SEMANA_5 ,
										Y.SALDO_ADEUDADO_SEMANA_6 ,
										Y.SALDO_ADEUDADO_SEMANA_7 ,
										Y.SALDO_ADEUDADO_SEMANA_8 ,
										Y.SALDO_ADEUDADO_SEMANA_9 ,
										Y.SALDO_ADEUDADO_SEMANA_10 ,
										Y.SALDO_ADEUDADO_SEMANA_11 ,
										Y.SALDO_ADEUDADO_SEMANA_12 ,
										Y.SALDO_ADEUDADO_SEMANA_13 ,
										Y.SALDO_ADEUDADO_SEMANA_14 ,
										Y.SALDO_ADEUDADO_SEMANA_15 ,
										Y.SALDO_ADEUDADO_SEMANA_16 ,
										Y.SALDO_ADEUDADO_SEMANA_17 ,
										Y.SALDO_ADEUDADO_SEMANA_18 ,
										Y.SALDO_ADEUDADO_SEMANA_19 ,
										Y.SALDO_ADEUDADO_SEMANA_20 ,
										Y.SALDO_ADEUDADO_SEMANA_21 ,
										Y.SALDO_ADEUDADO_SEMANA_22 ,
										Y.SALDO_ADEUDADO_SEMANA_23 ,
										Y.SALDO_ADEUDADO_SEMANA_24 ,
										Y.SALDO_ADEUDADO_SEMANA_25 ,
										Y.SALDO_ADEUDADO_SEMANA_26 ,
										Y.SALDO_ADEUDADO_SEMANA_27 ,
										Y.SALDO_ADEUDADO_SEMANA_28 ,
										Y.SALDO_ADEUDADO_SEMANA_29 ,
										Y.SALDO_ADEUDADO_SEMANA_30 ,
										Y.SALDO_ADEUDADO_SEMANA_31 ,
										Y.SALDO_ADEUDADO_SEMANA_32 ,
										Y.SALDO_ADEUDADO_SEMANA_33 ,
										Y.SALDO_ADEUDADO_SEMANA_34 ,
										Y.SALDO_ADEUDADO_SEMANA_35 ,
										Y.SALDO_ADEUDADO_SEMANA_36 ,
										Y.SALDO_ADEUDADO_SEMANA_37 ,
										Y.SALDO_ADEUDADO_SEMANA_38 ,
										Y.SALDO_ADEUDADO_SEMANA_39 ,
										Y.SALDO_ADEUDADO_SEMANA_40 ,
										Y.SALDO_ADEUDADO_SEMANA_41 ,
										Y.SALDO_ADEUDADO_SEMANA_42 ,
										Y.SALDO_ADEUDADO_SEMANA_43 ,
										Y.SALDO_ADEUDADO_SEMANA_44 ,
										Y.SALDO_ADEUDADO_SEMANA_45 ,
										Y.SALDO_ADEUDADO_SEMANA_46 ,
										Y.SALDO_ADEUDADO_SEMANA_47 ,
										Y.SALDO_ADEUDADO_SEMANA_48 ,
										Y.SALDO_ADEUDADO_SEMANA_49 ,
										Y.SALDO_ADEUDADO_SEMANA_50 ,
										Y.SALDO_ADEUDADO_SEMANA_51 ,
										Y.SALDO_ADEUDADO_SEMANA_52 ,
										Y.SALDO_ADEUDADO_SEMANA_53 ,
										Y.MENOS_90 ,
              			Y.MAS_90 ,
										Z.FECHA_MOVTO AS FECHA_ULTIMO_PAGO,
							      Z.CANTIDAD_ABONO AS IMPORTE_PAGADO,
							      Z.DIAS_AL_PAGO AS DIAS_ULTIMO_PAGO,
										(SUM(X.ENTRADA) - SUM(SALIDA)) AS VALOR_MERCA
                  FROM (SELECT sum((vie.c_cantidad_ume) * (vie.valor)) AS SALIDA,
                               0 AS ENTRADA,
                               vie.iid_num_cliente
                          FROM VISTA_REP_MOVTOS_VALOR VIE
                         INNER JOIN OP_IN_RECIBO_DEPOSITO SDH ON SDH.VID_CERTIFICADO =
                                                                 VIE.vid_certificado
                         WHERE SDH.I_SAL_CERO = 1
                           AND VIE.v_tipo_movto = 'SALIDA'
                           AND VIE.d_fecha_mvto <= to_date('$fecha', 'dd/mm/yyyy')
                           GROUP BY vie.iid_num_cliente
                        UNION
                        SELECT 0 AS SALIDA,
                               sum((vie.c_cantidad_ume) * (vie.valor)) AS ENTRADA,
                               vie.iid_num_cliente
                          FROM VISTA_REP_MOVTOS_VALOR VIE
                         INNER JOIN OP_IN_RECIBO_DEPOSITO SDH ON SDH.VID_CERTIFICADO =
                                                                 VIE.vid_certificado
                         WHERE SDH.I_SAL_CERO = 1
                           AND VIE.v_tipo_movto = 'ENTRADA'
                           AND VIE.d_fecha_mvto <= to_date('$fecha', 'dd/mm/yyyy')
                           GROUP BY vie.iid_num_cliente) X
                           RIGHT OUTER JOIN AD_CXC_REPORTE_DASHBOAR_SALDO_CLIENTE Y ON X.IID_NUM_CLIENTE = Y.IID_NUM_CLIENTE
												 	 LEFT JOIN (SELECT MAX(X.FECHA_MOVTO) AS FECHA_MOVTO,
													       SUM(CANTIDAD_ABONO) AS CANTIDAD_ABONO,
													       (to_date('$fecha', 'dd/mm/yyyy') - (FECHA_MOVTO)) AS DIAS_AL_PAGO,
																 IID_NUM_CLIENTE
													  FROM (SELECT (ADC.N_MONTO_ABONO) AS CANTIDAD_ABONO,
													               (ADC.D_FECHA_MOVTO) AS FECHA_MOVTO,
													               (to_date('$fecha', 'dd/mm/yyyy') - (ADC.D_FECHA_MOVTO)) AS DIAS_AL_PAGO,
																				 IID_NUM_CLIENTE
													          FROM AD_FA_FACTURA ADF
													         INNER JOIN AD_CXC_MOVTOS ADC ON ADC.IID_PLAZA = ADF.IID_PLAZA
													                                     AND ADC.IID_FOLIO = ADF.IID_FOLIO
													         WHERE ADC.N_MONTO_ABONO IS NOT NULL
																	 		AND ADF.IID_PLAZA IN ($plaza_val)
																		$almacen_ultimo
													           AND ADC.D_FECHA_MOVTO =
													               (SELECT MAX(S.D_FECHA_MOVTO)
													                  FROM AD_CXC_MOVTOS S
													                 WHERE S.IID_PLAZA = ADF.IID_PLAZA
													                   AND ADF.IID_FOLIO = S.IID_FOLIO
													                   AND ADC.D_FECHA_MOVTO <= to_date('$fecha', 'dd/mm/yyyy'))) X
													 WHERE X.FECHA_MOVTO =
													       (SELECT MAX(ADC.D_FECHA_MOVTO) AS FECHA_MOVTO
													          FROM AD_FA_FACTURA ADF
													         INNER JOIN AD_CXC_MOVTOS ADC ON ADC.IID_PLAZA = ADF.IID_PLAZA
													                                     AND ADC.IID_FOLIO = ADF.IID_FOLIO
													         WHERE IID_NUM_CLIENTE = X.IID_NUM_CLIENTE
																	 	 AND ADC.IID_PLAZA IN ($plaza_val)
																		 	$almacen_ultimo
													           AND ADC.N_MONTO_ABONO IS NOT NULL
																		 AND ADC.N_TIPO_MOVTO IN (1, 2)
													           AND ADC.D_FECHA_MOVTO =
													               (SELECT MAX(S.D_FECHA_MOVTO)
													                  FROM AD_CXC_MOVTOS S
													                 WHERE S.IID_PLAZA = ADF.IID_PLAZA
													                   AND ADF.IID_FOLIO = S.IID_FOLIO
																						 AND S.N_TIPO_MOVTO IN (1, 2)
													                   AND ADC.D_FECHA_MOVTO <= (to_date('$fecha', 'dd/mm/yyyy'))))
													 GROUP BY X.FECHA_MOVTO,
												 						X.IID_NUM_CLIENTE) Z ON  Z.IID_NUM_CLIENTE =  Y.IID_NUM_CLIENTE
GROUP BY
     Y.IID_NUM_CLIENTE,
										Y.V_RAZON_SOCIAL,
										Y.SALDO_ADEUDADO_SEMANA_1,
										Y.SALDO_ADEUDADO_SEMANA_2 ,
										Y.SALDO_ADEUDADO_SEMANA_3 ,
										Y.SALDO_ADEUDADO_SEMANA_4 ,
										Y.SALDO_ADEUDADO_SEMANA_5 ,
										Y.SALDO_ADEUDADO_SEMANA_6 ,
										Y.SALDO_ADEUDADO_SEMANA_7 ,
										Y.SALDO_ADEUDADO_SEMANA_8 ,
										Y.SALDO_ADEUDADO_SEMANA_9 ,
										Y.SALDO_ADEUDADO_SEMANA_10 ,
										Y.SALDO_ADEUDADO_SEMANA_11 ,
										Y.SALDO_ADEUDADO_SEMANA_12 ,
										Y.SALDO_ADEUDADO_SEMANA_13 ,
										Y.SALDO_ADEUDADO_SEMANA_14 ,
										Y.SALDO_ADEUDADO_SEMANA_15 ,
										Y.SALDO_ADEUDADO_SEMANA_16 ,
										Y.SALDO_ADEUDADO_SEMANA_17 ,
										Y.SALDO_ADEUDADO_SEMANA_18 ,
										Y.SALDO_ADEUDADO_SEMANA_19 ,
										Y.SALDO_ADEUDADO_SEMANA_20 ,
										Y.SALDO_ADEUDADO_SEMANA_21 ,
										Y.SALDO_ADEUDADO_SEMANA_22 ,
										Y.SALDO_ADEUDADO_SEMANA_23 ,
										Y.SALDO_ADEUDADO_SEMANA_24 ,
										Y.SALDO_ADEUDADO_SEMANA_25 ,
										Y.SALDO_ADEUDADO_SEMANA_26 ,
										Y.SALDO_ADEUDADO_SEMANA_27 ,
										Y.SALDO_ADEUDADO_SEMANA_28 ,
										Y.SALDO_ADEUDADO_SEMANA_29 ,
										Y.SALDO_ADEUDADO_SEMANA_30 ,
										Y.SALDO_ADEUDADO_SEMANA_31 ,
										Y.SALDO_ADEUDADO_SEMANA_32 ,
										Y.SALDO_ADEUDADO_SEMANA_33 ,
										Y.SALDO_ADEUDADO_SEMANA_34 ,
										Y.SALDO_ADEUDADO_SEMANA_35 ,
										Y.SALDO_ADEUDADO_SEMANA_36 ,
										Y.SALDO_ADEUDADO_SEMANA_37 ,
										Y.SALDO_ADEUDADO_SEMANA_38 ,
										Y.SALDO_ADEUDADO_SEMANA_39 ,
										Y.SALDO_ADEUDADO_SEMANA_40 ,
										Y.SALDO_ADEUDADO_SEMANA_41 ,
										Y.SALDO_ADEUDADO_SEMANA_42 ,
										Y.SALDO_ADEUDADO_SEMANA_43 ,
										Y.SALDO_ADEUDADO_SEMANA_44 ,
										Y.SALDO_ADEUDADO_SEMANA_45 ,
										Y.SALDO_ADEUDADO_SEMANA_46 ,
										Y.SALDO_ADEUDADO_SEMANA_47 ,
										Y.SALDO_ADEUDADO_SEMANA_48 ,
										Y.SALDO_ADEUDADO_SEMANA_49 ,
										Y.SALDO_ADEUDADO_SEMANA_50 ,
										Y.SALDO_ADEUDADO_SEMANA_51 ,
										Y.SALDO_ADEUDADO_SEMANA_52 ,
										Y.SALDO_ADEUDADO_SEMANA_53 ,
										Y.MENOS_90 ,
              			Y.MAS_90 ,
										Z.FECHA_MOVTO,
					          Z.CANTIDAD_ABONO,
					          Z.DIAS_AL_PAGO";


		$stid2 = oci_parse($conn, $sql);
		oci_execute($stid2);
		#echo $sql;
		while (($row = oci_fetch_assoc($stid2)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid2);
		//oci_close($conn);

		return $res_array;


	}

	public function tabla_porcentaje2($fecha, $plaza, $almacen){
			#echo $fecha;

			$conn = conexion::conectar();

			$res_array = array();

			$in_plaza = "0";
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
			    default: $in_plaza = 0; break;
			}

			if ($plaza == "ALL") {
				$plaza_val = "3,4,5,6,7,8,17,18";
			}else {
				$plaza_val = $in_plaza;
			}

			if ($almacen == "ALL") {
				$almacen = 0;
				$almacen_ultimo = " ";
			}else {
				$almacen = $almacen;
				$almacen_ultimo = "ADF.IID_ALMACEN = $almacen";
			}

			//$error_mg = "";
			#echo $almacen."  ".$plaza;
			$formatted = vsprintf('%3$04d/%2$02d/%1$02d', sscanf($fecha,'%02d/%02d/%04d'));
	    $fechats = strtotime($formatted);
	    //echo $fechats;
	    switch (date('w', $fechats)){
	      case 0: $dia = "Domingo"; break;
	      case 1: $dia = "Lunes"; break;
	      case 2: $dia = "Martes"; break;
	      case 3: $dia = "Miercoles"; break;
	      case 4: $dia = "Jueves"; break;
	      case 5: $dia = "Viernes"; break;
	      case 6: $dia = "Sabado"; break;
	    }

	    if ($dia == "Sabado") {
	      #echo "HERE";
	      $fecha = $fecha;
	    }else {
	      #echo "HERE2";
	      $fecha = date("d/m/Y");
	      $first = strtotime('last saturday');
	      $fecha =  date('d/m/Y', $first);
	    }
			#$fecha_n = '03/10/2020';
			//echo $pre.' '.$fecha.' '.$promotor.' '.$plaza;
			$stid = oci_parse($conn, "BEGIN PCK_SALDOS_CLIENTE_X_SEMANA_1_MES(:fecha, :plaza, :almacen, :error_msg); END;");
			oci_bind_by_name($stid, ':fecha', $fecha); //3
			oci_bind_by_name($stid, ':plaza', $in_plaza); //2019
			oci_bind_by_name($stid, ':almacen', $almacen); // ALL
			oci_bind_by_name($stid, ':error_msg',$error_mg);


			oci_execute($stid);
			#oci_free_statement($stid);

			#echo "El error es". $error_mg;
			//
			#echo $fecha. "   ". $in_plaza. "   ". $almacen;
			#while (($row = oci_fetch_array($curs, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
			#		$res_array[]=$row;
			#}

			//oci_free_statement($stid);
			#oci_close($conn);
			$sql = "SELECT Y.IID_NUM_CLIENTE,
											Y.V_RAZON_SOCIAL,
											Y.SALDO_ADEUDADO_SEMANA_50 ,
											Y.SALDO_ADEUDADO_SEMANA_51 ,
											Y.SALDO_ADEUDADO_SEMANA_52 ,
											Y.SALDO_ADEUDADO_SEMANA_53 ,
											Y.MENOS_90 ,
	              			Y.MAS_90 ,
											Z.FECHA_MOVTO AS FECHA_ULTIMO_PAGO,
								      Z.CANTIDAD_ABONO AS IMPORTE_PAGADO,
								      Z.DIAS_AL_PAGO AS DIAS_ULTIMO_PAGO,
											(SUM(X.ENTRADA) - SUM(SALIDA)) AS VALOR_MERCA
	                  FROM (SELECT sum((vie.c_cantidad_ume) * (vie.valor)) AS SALIDA,
	                               0 AS ENTRADA,
	                               vie.iid_num_cliente
	                          FROM VISTA_REP_MOVTOS_VALOR VIE
	                         INNER JOIN OP_IN_RECIBO_DEPOSITO SDH ON SDH.VID_CERTIFICADO =
	                                                                 VIE.vid_certificado
	                         WHERE SDH.I_SAL_CERO = 1
	                           AND VIE.v_tipo_movto = 'SALIDA'
	                           AND VIE.d_fecha_mvto <= to_date('$fecha', 'dd/mm/yyyy')
	                           GROUP BY vie.iid_num_cliente
	                        UNION
	                        SELECT 0 AS SALIDA,
	                               sum((vie.c_cantidad_ume) * (vie.valor)) AS ENTRADA,
	                               vie.iid_num_cliente
	                          FROM VISTA_REP_MOVTOS_VALOR VIE
	                         INNER JOIN OP_IN_RECIBO_DEPOSITO SDH ON SDH.VID_CERTIFICADO =
	                                                                 VIE.vid_certificado
	                         WHERE SDH.I_SAL_CERO = 1
	                           AND VIE.v_tipo_movto = 'ENTRADA'
	                           AND VIE.d_fecha_mvto <= to_date('$fecha', 'dd/mm/yyyy')
	                           GROUP BY vie.iid_num_cliente) X
	                           RIGHT OUTER JOIN AD_CXC_REPORTE_DASHBOAR_SALDO_CLIENTE Y ON X.IID_NUM_CLIENTE = Y.IID_NUM_CLIENTE
													 	 LEFT JOIN (SELECT MAX(X.FECHA_MOVTO) AS FECHA_MOVTO,
														       SUM(CANTIDAD_ABONO) AS CANTIDAD_ABONO,
														       (to_date('$fecha', 'dd/mm/yyyy') - (FECHA_MOVTO)) AS DIAS_AL_PAGO,
																	 IID_NUM_CLIENTE
														  FROM (SELECT (ADC.N_MONTO_ABONO) AS CANTIDAD_ABONO,
														               (ADC.D_FECHA_MOVTO) AS FECHA_MOVTO,
														               (to_date('$fecha', 'dd/mm/yyyy') - (ADC.D_FECHA_MOVTO)) AS DIAS_AL_PAGO,
																					 IID_NUM_CLIENTE
														          FROM AD_FA_FACTURA ADF
														         INNER JOIN AD_CXC_MOVTOS ADC ON ADC.IID_PLAZA = ADF.IID_PLAZA
														                                     AND ADC.IID_FOLIO = ADF.IID_FOLIO
														         WHERE ADC.N_MONTO_ABONO IS NOT NULL
																		 		AND ADF.IID_PLAZA IN ($plaza_val)
																			$almacen_ultimo
														           AND ADC.D_FECHA_MOVTO =
														               (SELECT MAX(S.D_FECHA_MOVTO)
														                  FROM AD_CXC_MOVTOS S
														                 WHERE S.IID_PLAZA = ADF.IID_PLAZA
														                   AND ADF.IID_FOLIO = S.IID_FOLIO
														                   AND ADC.D_FECHA_MOVTO <= to_date('$fecha', 'dd/mm/yyyy'))) X
														 WHERE X.FECHA_MOVTO =
														       (SELECT MAX(ADC.D_FECHA_MOVTO) AS FECHA_MOVTO
														          FROM AD_FA_FACTURA ADF
														         INNER JOIN AD_CXC_MOVTOS ADC ON ADC.IID_PLAZA = ADF.IID_PLAZA
														                                     AND ADC.IID_FOLIO = ADF.IID_FOLIO
														         WHERE IID_NUM_CLIENTE = X.IID_NUM_CLIENTE
																		 	 AND ADC.IID_PLAZA IN ($plaza_val)
																			 	$almacen_ultimo
														           AND ADC.N_MONTO_ABONO IS NOT NULL
																			 AND ADC.N_TIPO_MOVTO IN (1, 2)
														           AND ADC.D_FECHA_MOVTO =
														               (SELECT MAX(S.D_FECHA_MOVTO)
														                  FROM AD_CXC_MOVTOS S
														                 WHERE S.IID_PLAZA = ADF.IID_PLAZA
														                   AND ADF.IID_FOLIO = S.IID_FOLIO
																							 AND S.N_TIPO_MOVTO IN (1, 2)
														                   AND ADC.D_FECHA_MOVTO <= (to_date('$fecha', 'dd/mm/yyyy'))))
														 GROUP BY X.FECHA_MOVTO,
													 						X.IID_NUM_CLIENTE) Z ON  Z.IID_NUM_CLIENTE =  Y.IID_NUM_CLIENTE
	GROUP BY
	     Y.IID_NUM_CLIENTE,
											Y.V_RAZON_SOCIAL,
											Y.SALDO_ADEUDADO_SEMANA_50 ,
											Y.SALDO_ADEUDADO_SEMANA_51 ,
											Y.SALDO_ADEUDADO_SEMANA_52 ,
											Y.SALDO_ADEUDADO_SEMANA_53 ,
											Y.MENOS_90 ,
	              			Y.MAS_90 ,
											Z.FECHA_MOVTO,
						          Z.CANTIDAD_ABONO,
						          Z.DIAS_AL_PAGO";


			$stid2 = oci_parse($conn, $sql);
			oci_execute($stid2);
			#echo $sql;
			while (($row = oci_fetch_assoc($stid2)) != false)
			{
				$res_array[]= $row;
			}

			oci_free_statement($stid2);
			//oci_close($conn);

			return $res_array;


		}

	public function nombre_Mercancia($cliente, $plaza){

		$conn = conexion::conectar();
		$res_array = array();

		$in_plaza = "3,4,5,6,7,8,17,18";
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
				default: $in_plaza =  "3,4,5,6,7,8,17,18" ; break;
		}

		$sql = "SELECT  PA.V_DESCRIPCION AS mercancia
						FROM

						 (SELECT RD.VID_RECIBO, RD.VID_NUM_PARTE, SUM(RD.C_CANTIDAD)C_CANTIDAD_UME
						    FROM OP_IN_RECIBO_DEPOSITO_DET RD
						   GROUP BY RD.VID_RECIBO, RD.VID_NUM_PARTE)E,

						 (SELECT SS.VID_RECIBO, SD.VID_NUM_PARTE, SUM(SD.C_CANTIDAD)C_CANTIDAD_UME
						    FROM OP_IN_ORD_SALIDA SS,
						         OP_IN_ORD_SALIDA_DET SD
						   WHERE SS.VID_ORD_SAL = SD.VID_ORD_SAL
						   GROUP BY SS.VID_RECIBO, SD.VID_NUM_PARTE)S,

						  OP_IN_RECIBO_DEPOSITO R,
						  op_in_partes PA
						WHERE E.VID_RECIBO = S.VID_RECIBO(+)
						AND E.VID_NUM_PARTE = S.VID_NUM_PARTE(+)
						AND PA.VID_NUM_PARTE = E.VID_NUM_PARTE
						AND R.VID_RECIBO = E.VID_RECIBO
						AND R.IID_NUM_CLIENTE = $cliente
						AND R.I_SAL_CERO = 1 ";

				$stid2 = oci_parse($conn, $sql);
				oci_execute($stid2);
				##echo $sql;
				while (($row = oci_fetch_assoc($stid2)) != false){
								$res_array[]= $row;
				}
				oci_free_statement($stid2);
				oci_close($conn);
				return $res_array;
	}


	public function promedio_dias($cliente, $plaza, $fecha){
			#echo $fecha;

			$conn = conexion::conectar();

			$res_array = array();

			$curs = oci_new_cursor($conn);
			$promotor = "ALL";

			$in_plaza = "3,4,5,6,7,8,17,18";

			if ($plaza == "ALL") {
				$plaza_inin = 3;
				$plaza_finn = 18;
			}else {
				switch ($plaza) {
						case 'CORPORATIVO': $plaza_inin = 2;
																$plaza_finn = 2;
						 	break;
						case 'CÓRDOBA': $plaza_inin = 3;
														$plaza_finn = 3;
							break;
						case 'MÉXICO': $plaza_inin = 4;
						 							$plaza_finn = 4;
							break;
						case 'GOLFO': $plaza_inin = 5;
													$plaza_finn = 5;
							break;
						case 'PENINSULA': $plaza_inin = 6;
															$plaza_finn = 6;
							break;
						case 'PUEBLA': $plaza_inin = 7;
													$plaza_finn = 7;
							break;
						case 'BAJIO': $plaza_inin = 8;
													$plaza_finn = 8;
							break;
						case 'OCCIDENTE': $plaza_inin = 17;
															$plaza_finn = 17;
							break;
						case 'NORESTE': $plaza_inin = 18;
														$plaza_finn = 18;
							break;
						default: $plaza_inin = 3; break;
				}
			}

			#	echo $cliente."   ".$plaza."   ".$fecha;
			$fecha1 = str_replace('/', '-' , $fecha);
			//echo "gas".$fecha;
			$fecha1 =  date("d-m-Y",strtotime($fecha1."- 28 days"));

			$fecha32 = date('d/m/Y', strtotime($fecha1));
			date_default_timezone_set('UTC');

			date_default_timezone_set("America/Mexico_City");
			$formatted = vsprintf('%3$04d/%2$02d/%1$02d', sscanf($fecha32,'%02d/%02d/%04d'));
			$fechats = strtotime($formatted);
			//echo $fechats;
			switch (date('w', $fechats)){
				case 0: $dia = "Domingo"; break;
				case 1: $dia = "Lunes"; break;
				case 2: $dia = "Martes"; break;
				case 3: $dia = "Miercoles"; break;
				case 4: $dia = "Jueves"; break;
				case 5: $dia = "Viernes"; break;
				case 6: $dia = "Sabado"; break;
			}

			#echo $dia;

			if ($dia == "Sabado") {
				$fecha_ini = $fecha32;
			}else {
				$format = "d/m/Y";
				$date = DateTime::createFromFormat($format, $fecha32);
				$date->modify('next saturday');
				$fecha32= $date->format("d/m/Y");
				$fecha_ini = $fecha32;
			}
			//echo $fecha;
			//echo $fecha1;
			$fecha32 = date('d/m/Y', strtotime($fecha1));

			$stid = oci_parse($conn, "BEGIN PCK_AD_CXC.PROC_RPT_CART_PROM_CTES (:p_cliente_ini, :p_cliente_fin, :p_plaza_ini, :p_plaza_fin, :p_fecha_ini, :p_fecha_fin,  :msg, :p_code); END;");
			oci_bind_by_name($stid, ':p_cliente_ini', $cliente); //3
			oci_bind_by_name($stid, ':p_cliente_fin', $cliente); //2019
			oci_bind_by_name($stid, ':p_plaza_ini', $plaza_inin); // ALL
			oci_bind_by_name($stid, ':p_plaza_fin', $plaza_finn); // ALL
			oci_bind_by_name($stid, ':p_fecha_ini', $fecha32); // ALL
			oci_bind_by_name($stid, ':p_fecha_fin', $fecha); // ALL
			oci_bind_by_name($stid, ':msg', $msg, 300); // ALL
			oci_bind_by_name($stid, ':p_code',$error_mg, 300);

			#echo $cliente."   ".$plaza_inin. "    ".$plaza_finn. "  ".$fecha32."   ".$fecha;


			oci_execute($stid);

			#echo $msg. "  ".$error_mg;

			$sql = " SELECT (SUM(C.D_DIAS_CARTERA)/( COUNT(DISTINCT(C.IID_FOLIO)))) AS DIAS_PROM FROM AD_CXC_RPT_CRT_PROM_CTE2 C WHERE C.IID_NUM_CLIENTE = $cliente";
			#$sql = " SELECT C.D_DIAS_CARTERA AS DIAS_PROM FROM AD_CXC_RPT_CRT_PROM_CTE2 C WHERE C.IID_NUM_CLIENTE = $cliente";
			#$sql = "SELECT * FROM CLIENTE C WHERE C.IID_NUM_CLIENTE = $cliente";


			$stid2 = oci_parse($conn, $sql);
			oci_execute($stid2);
				#echo $sql;
			while (($row = oci_fetch_assoc($stid2)) != false)
			{
				$res_array[]= $row;
				##print_r($res_array);
			}
			;

			oci_free_statement($stid2);
			//oci_close($conn);

			return $res_array;
		}
/*CONSULTA COMENTARIOS */
public function datos_comentarios($cliente, $plaza, $fecha){
		#echo $fecha;

		$conn = conexion::conectar();

		$res_array = array();

		$curs = oci_new_cursor($conn);
		$promotor = "ALL";

		$in_plaza = "3,4,5,6,7,8,17,18";
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
		    default: $in_plaza =  "3,4,5,6,7,8,17,18" ; break;
		}

		#	echo $cliente."   ".$plaza."   ".$fecha;
		$fecha1 = str_replace('/', '-' , $fecha);
		//echo "gas".$fecha;
		$fecha1 =  date("d-m-Y",strtotime($fecha1."- 12 month"));
		//echo $fecha;
		//echo $fecha1;
		$fecha32 = date('d/m/Y', strtotime($fecha1));

		$sql = " SELECT c.v_razon_social, s.v_nombre, p.v_razon_social, jh.fecha_registro,  jh.comentario
						 FROM AD_CXC_REP_DASHBOAR_SALDO_VENCIDO_COMENTARIOS jh
						 inner join cliente c on jh.iid_cliente = c.iid_num_cliente
						 inner join se_usuarios s on jh.iid_empleado_registro = s.iid_empleado
						 inner join plaza p on jh.iid_plaza = p.iid_plaza
						 where p.iid_plaza in ($in_plaza)
						 			and c.iid_num_cliente = $cliente
									and (jh.fecha_registro >= to_date('$fecha32', 'dd/mm/yyyy') AND jh.fecha_registro <= to_date('$fecha', 'dd/mm/yyyy'))
						 order by fecha_registro";


		$stid2 = oci_parse($conn, $sql);
		oci_execute($stid2);
		##echo $sql;
		while (($row = oci_fetch_assoc($stid2)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid2);
		oci_close($conn);

		return $res_array;
	}

function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}

	public function sql($option,$depto,$plaza){
			$conn = conexion::conectar();
			$res_array = array();

			$sql = "SELECT * FROM DUAL";
		 	switch ($option) {
				case '1':
					$sql = "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0), 'DD/MM/YYYY') mes1, TO_CHAR(SYSDATE, 'DD/MM/YYYY') mes2 FROM DUAL";
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
					$sql = "select v_scuenta as cuenta,UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(17, 50, 56, 57, 59, 60, 65, 73, 74, 77, 78, 83, 84, 85, 86, 88, 89, 91) and v_sscuenta = 0
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
}
	/*====================== /.VALIDA SI ES FECHA  ======================*/
