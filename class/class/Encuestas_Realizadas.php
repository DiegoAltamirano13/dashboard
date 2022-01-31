
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

	/*++++++++++++++++++++++++ GRAFICA PERSONAL ACTIVO ++++++++++++++++++++++++*/
	public function grafica($fecha, $mes,  $cliente)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha_act = " ";
		$and_fecha_act2 = " ";
		$and_fecha_can = " AND can.fecha_cancelacion >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND can.fecha_cancelacion < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),-1) ) ";
		$fecha_cancel =" and RCAN.FECHA_CANCELACION <= TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),0) ) ";


		if ($cliente == "ALL") {
			$where_cliente = " ";
			$where_cliente2 = " ";
		}else {
			$where_cliente = " AND A.ID_CLIENTE = $cliente";
			$where_cliente2 = " AND ENC.ID_CLIENTE = $cliente";
		}

		if ($mes == 1 ) {
			$fecha_ini = "01/".$fecha;
			$fecha_fin = "06/".$fecha;
		}elseif ($mes == 2) {
			$fecha_ini = "07/".$fecha;
			$fecha_fin = "12/".$fecha;
		}


		$where_fechas = "AND TO_DATE(TO_CHAR(A.FECHA, 'MM/YYYY'), 'MM/YYYY') >= TO_DATE('$fecha_ini', 'MM/YYYY') AND TO_DATE(TO_CHAR(A.FECHA, 'MM/YYYY'), 'MM/YYYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')";
		$where_fechas2 = " TO_DATE(ENC.FECHA_PROG, 'MM/YYYY') >= TO_DATE('$fecha_ini', 'MM/YYYY') AND TO_DATE(ENC.FECHA_PROG, 'MM/YYYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')";


				$sql = "SELECT (SUM(Z.TOTAL_NEGATIVAS)/SUM(Z.TOTAL_PREG))*100 AS PROMEDIO_NEGATIVO,(SUM(Z.TOTAL_POSITIVAS)/SUM(Z.TOTAL_PREG))*100 AS PROMEDIO_POSITIVA, Z.CONSECUTIVO_ENC FROM(
  SELECT X.PLAZA , X.CONSECUTIVO_ENC, SUM(X.TOTAL) AS TOTAL_NEGATIVAS, 0 AS TOTAL_PREG, 0 TOTAL_POSITIVAS FROM (
							    SELECT   DISTINCT(PLA.IID_PLAZA) AS PLAZA, COUNT(*) AS TOTAL, 'SUBPREGUNTAS', A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
							             INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
							             INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND C.ID_CLIENTE = A.ID_CLIENTE
							             INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC
							             INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA AND E.IID_CONS = D.ID_SUBPREGUNTA
							             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
							             INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
							             INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
							             INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
							             INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
										             INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
										    WHERE (F.RESPUESTA = 'REGULAR' OR F.RESPUESTA = 'MALO')
										          AND E.IID_PREGUNTA IN (1, 6)
										          AND B.N_TIPO IN (1, 2)
															$where_fechas
															$where_cliente
										    GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
										    UNION ALL
										    SELECT DISTINCT(PLA.IID_PLAZA) AS PLAZA , COUNT(*) AS TOTAL, 'PREGUNTAS DEPENDIENTES NO POSITIVAS', A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
										             INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
										             INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND C.ID_CLIENTE = A.ID_CLIENTE
										             INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC AND D.ID_SUBPREGUNTA IS NULL
										             INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA
										             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
										             INNER JOIN AD_SGC_ENCUESTA_PREGUNTA F ON F.ID_PREGUNTA = D.ID_PREGUNTA AND F.TIPO_ENCUESTA = A.ENCUESTA_TIPO
										             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA G ON G.ID_RESPUESTA = D.ID_RESPUESTA
										             INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
										             INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
										             INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
										             INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
										             INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
										    WHERE F.RESPUESTA IN('NO')
										          AND B.N_TIPO IN( 1, 2)
										          AND E.IID_PREGUNTA IN (3)
										          AND F.DEPENDIENTE IS NULL
										          AND E.TIPO_PREGUNTA <> 1
															AND F.PREGUNTA <> '¿HAN VISITADO NUESTRA PÁGINA WEB?'
															$where_fechas
															$where_cliente
										    GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
										    UNION ALL
										    SELECT DISTINCT(PLA.IID_PLAZA) AS PLAZA, COUNT(*) AS TOTAL, 'PREGUNTAS DEPENDIENTES NO POSITIVAS', A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
										             INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
										             INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND C.ID_CLIENTE = A.ID_CLIENTE
										             INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC AND D.ID_SUBPREGUNTA IS NULL
										             INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA
										             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
										             INNER JOIN AD_SGC_ENCUESTA_PREGUNTA F ON F.ID_PREGUNTA = D.ID_PREGUNTA AND F.TIPO_ENCUESTA = A.ENCUESTA_TIPO
										             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA G ON G.ID_RESPUESTA = D.ID_RESPUESTA
										             INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
										             INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
										             INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
										             INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
										             INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
										    WHERE F.RESPUESTA IN('SI')
										          AND B.N_TIPO IN( 1, 2)
										          AND E.IID_PREGUNTA IN (2)
										          AND F.DEPENDIENTE IS NULL
										          AND E.TIPO_PREGUNTA <> 1
															AND F.PREGUNTA <> '¿HAN VISITADO NUESTRA PÁGINA WEB?'
															$where_fechas
															$where_cliente
										    GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA) X
			GROUP BY X.PLAZA, X.CONSECUTIVO_ENC
			UNION ALL
										  SELECT Y.PLAZA, Y.CONSECUTIVO_ENC, 0 AS TOTAL_NEGATIVAS, SUM(Y.TOTAL) AS TOTAL_PREG, 0 TOTAL_POSITIVAS  FROM (
										  SELECT   DISTINCT(PLA.IID_PLAZA) AS PLAZA , COUNT(*) AS TOTAL,  A.CONSECUTIVO_ENC   FROM AD_SGC_ENCUESTA_ENC A
										           INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
										           INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND C.ID_CLIENTE = A.ID_CLIENTE
										           INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC
										           INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA
										           INNER JOIN AD_SGC_ENCUESTA_PREGUNTA F ON F.ID_PREGUNTA = D.ID_PREGUNTA AND F.TIPO_ENCUESTA = A.ENCUESTA_TIPO
										           INNER JOIN AD_SGC_ENCUESTA_RESPUESTA G ON G.ID_RESPUESTA = D.ID_RESPUESTA
										           INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
										           INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
										           INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
										           INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
										           INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
										  WHERE E.IID_PREGUNTA IN (2, 3)
										        AND B.N_TIPO IN( 1, 2)
										        AND F.DEPENDIENTE IS NULL
										        AND (D.ID_SUBPREGUNTA IS NULL OR F.RESP_SIONO IS NOT NULL)
										        AND E.TIPO_PREGUNTA <> 1
										        AND G.RESPUESTA IN ('SI', 'NO')
														AND F.PREGUNTA <> '¿HAN VISITADO NUESTRA PÁGINA WEB?'
														$where_fechas
														$where_cliente
										  GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
										  UNION ALL
										  SELECT   DISTINCT(PLA.IID_PLAZA) AS PLAZA ,  COUNT(*)AS TOTAL , A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
										           INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
										           INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND C.ID_CLIENTE = A.ID_CLIENTE
										           INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC
										           INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA AND E.IID_CONS = D.ID_SUBPREGUNTA
										           INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
										           INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
										           INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
										           INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
										           INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
										           INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
										  WHERE E.IID_PREGUNTA IN (1, 6)
										        AND B.N_TIPO IN( 1, 2)
														$where_fechas
														$where_cliente
										  GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
										  ) Y
										  GROUP BY Y.CONSECUTIVO_ENC, Y.PLAZA
			UNION ALL
			SELECT R.PLAZA , R.CONSECUTIVO_ENC, 0 AS TOTAL_NEGATIVAS, 0 AS TOTAL_PREG,  SUM(R.TOTAL) AS TOTAL_POSITIVAS FROM (
										    SELECT   DISTINCT(PLA.IID_PLAZA) AS PLAZA, COUNT(*) AS TOTAL, 'SUBPREGUNTAS', A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
										             INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
										             INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND C.ID_CLIENTE = A.ID_CLIENTE
										             INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC
										             INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA AND E.IID_CONS = D.ID_SUBPREGUNTA
										             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
										             INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
										             INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
										             INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
										             INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
										             INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
										    WHERE (F.RESPUESTA = 'EXCELENTE' OR F.RESPUESTA = 'BUENO')
										          AND E.IID_PREGUNTA IN (1, 6)
										          AND B.N_TIPO IN( 1, 2)
															$where_fechas
															$where_cliente
										    GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
										    UNION ALL
										    SELECT DISTINCT(PLA.IID_PLAZA) AS PLAZA , COUNT(*) AS TOTAL, 'PREGUNTAS DEPENDIENTES NO POSITIVAS', A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
										             INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
										             INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND C.ID_CLIENTE = A.ID_CLIENTE
										             INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC AND D.ID_SUBPREGUNTA IS NULL
										             INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA
										             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
										             INNER JOIN AD_SGC_ENCUESTA_PREGUNTA F ON F.ID_PREGUNTA = D.ID_PREGUNTA AND F.TIPO_ENCUESTA = A.ENCUESTA_TIPO
										             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA G ON G.ID_RESPUESTA = D.ID_RESPUESTA
										             INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
										             INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
										             INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
										             INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
										             INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
										    WHERE F.RESPUESTA IN('SI')
										          AND B.N_TIPO IN( 1, 2)
										          AND E.IID_PREGUNTA IN (2)
										          AND F.DEPENDIENTE IS NULL
										          AND E.TIPO_PREGUNTA <> 1
															AND F.PREGUNTA <> '¿HAN VISITADO NUESTRA PÁGINA WEB?'
															$where_fechas
															$where_cliente
										    GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
										    UNION ALL
										    SELECT DISTINCT(PLA.IID_PLAZA) AS PLAZA, COUNT(*) AS TOTAL, 'PREGUNTAS DEPENDIENTES NO POSITIVAS', A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
										             INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
										             INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND C.ID_CLIENTE = A.ID_CLIENTE
										             INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC AND D.ID_SUBPREGUNTA IS NULL
										             INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA
										             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
										             INNER JOIN AD_SGC_ENCUESTA_PREGUNTA F ON F.ID_PREGUNTA = D.ID_PREGUNTA AND F.TIPO_ENCUESTA = A.ENCUESTA_TIPO
										             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA G ON G.ID_RESPUESTA = D.ID_RESPUESTA
										             INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
										             INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
										             INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
										             INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
										             INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
										    WHERE F.RESPUESTA IN('NO')
										          AND B.N_TIPO IN( 1, 2)
										          AND E.IID_PREGUNTA IN (3)
										          AND F.DEPENDIENTE IS NULL
										          AND E.TIPO_PREGUNTA <> 1
															AND F.PREGUNTA <> '¿HAN VISITADO NUESTRA PÁGINA WEB?'
															$where_fechas
															$where_cliente
										    GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA) R
			GROUP BY R.PLAZA, R.CONSECUTIVO_ENC
			)Z
			GROUP BY Z.CONSECUTIVO_ENC";
			#echo $sql;
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);

				while (($row = oci_fetch_assoc($stid)) != false)
				{
					$res_array[]= $row;
				}
//Conseguirte
				#echo $sql;
				oci_free_statement($stid);
				oci_close($conn);


				return $res_array;

	}

	public function tipo_respuestas($fecha, $mes, $cliente){
		$conn = conexion::conectar();
		$res_array = array();

		$fecha_ini = substr($fecha, 0, 10);
		$fecha_fin = substr($fecha, 11, 10);

		if ($mes == 1 ) {
			$mes1 = "01/".$fecha;
			$mes2 = "06/".$fecha;
		}elseif ($mes == 2) {
			$mes1 = "07/".$fecha;
			$mes2 = "12/".$fecha;
		}

		$sql ="SELECT COUNT(TIPO_HAB) AS HABILITADO,
		       COUNT(TIPO_DEP_DIRECTO) AS DIRECTO
							FROM(
							SELECT CASE WHEN C.N_TIPO_CLIENTE = 1 THEN
							            'HABILITADO'
							            WHEN C.N_TIPO_CLIENTE = 3 THEN
							            'HABILITADO'
							       END AS TIPO_HAB,
							       '' AS TIPO_DEP_DIRECTO
							       FROM AD_SGC_ENCUESTA_ENC Y
							       INNER JOIN CLIENTE C ON Y.ID_CLIENTE = C.IID_NUM_CLIENTE
										 WHERE TO_DATE(TO_CHAR(Y.FECHA, 'MM/YYY'), 'MM/YYY') >= TO_DATE('$mes1', 'mm/yyyy') AND TO_DATE(TO_CHAR(Y.FECHA, 'MM/YYY'), 'MM/YYYY') <= TO_DATE('$mes2', 'mm/yyyy')
							UNION ALL
							SELECT '' AS TIPO_HAB,
							       CASE WHEN C.N_TIPO_CLIENTE = 2 THEN
							            'DEPOSITANTE DIRECTA'
							       END AS TIPO_DEP_DIRECTO
							       FROM AD_SGC_ENCUESTA_ENC Y
							       INNER JOIN CLIENTE C ON Y.ID_CLIENTE = C.IID_NUM_CLIENTE
									 	 WHERE TO_DATE(TO_CHAR(Y.FECHA, 'MM/YYYY'), 'MM/YYYY') >= TO_DATE('$mes1', 'mm/yyyy') AND TO_DATE(TO_CHAR(Y.FECHA, 'MM/YYYY'), 'MM/YYYY') <= TO_DATE('$mes2', 'mm/yyyy'))";
										#echo $sql;
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);

					while (($row = oci_fetch_assoc($stid)) != false){
					 						$res_array[]= $row;
					}
					oci_free_statement($stid);
					oci_close($conn);
					 return $res_array;
	}


	public function widgets($fecha, $mes, $cliente)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($cliente == "ALL") {
			$where_cliente = " ";
		}else {
			$where_cliente = " AND R.ID_CLIENTE = $cliente";
		}

		if ($mes == 1 ) {
			$fecha_ini = "01/".$fecha;
			$fecha_fin = "06/".$fecha;
		}elseif ($mes == 2) {
			$fecha_ini = "07/".$fecha;
			$fecha_fin = "12/".$fecha;
		}

		$where_fechas = "AND TO_DATE(TO_CHAR(R.D_FECHA_ENVIO, 'MM/YYYY'), 'MM/YYYY') >= TO_DATE('$fecha_ini', 'MM/YYYY') AND TO_DATE(TO_CHAR(R.D_FECHA_ENVIO, 'MM/YYYY'), 'MM/YYYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')";
		$where_fechas2 = "TO_DATE(TO_CHAR(R.FECHA, 'MM/YYYY'), 'MM/YYYY') >= TO_DATE('$fecha_ini', 'MM/YYYY') AND TO_DATE(TO_CHAR(R.FECHA, 'MM/YYYY'), 'MM/YYYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')";

		$sql ="SELECT SUM(NO_RESPONDIDAS) AS N_RESPONDIDAS, SUM(RESPONDIDAS)AS RESPONDIDAS FROM(
									SELECT COUNT(R.N_STATUS) AS NO_RESPONDIDAS, 0 AS RESPONDIDAS FROM AD_SGC_ENCUESTA_TIPO_CLIENTE R
												 WHERE R.N_STATUS = 0
												 $where_fechas
												 $where_cliente
									UNION ALL
									SELECT 0 AS NO_RESPONDIDAS, COUNT(R.CONSECUTIVO_ENC) AS RESPONDIDAS FROM AD_SGC_ENCUESTA_ENC R
												 WHERE R.CONSECUTIVO_ENC <> 0 AND
												 $where_fechas2
												 $where_cliente
									)";
				#echo $sql;
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);

				while (($row = oci_fetch_assoc($stid)) != false)
				{
					$res_array[]= $row;
				}
//Conseguirte
		#echo $sql;
				oci_free_statement($stid);
				oci_close($conn);


				return $res_array;

	}

	//*detalle de tabla RESPONDIDAS
	public function detalleRespondidas($fecha, $mes, $cliente)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($cliente == "ALL") {
			$where_cliente = " ";
		}else {
			$where_cliente = " AND R.ID_CLIENTE = $cliente";
		}


		if ($mes == 1 ) {
			$fecha_ini = "01/".$fecha;
			$fecha_fin = "06/".$fecha;
		}elseif ($mes == 2) {
			$fecha_ini = "07/".$fecha;
			$fecha_fin = "12/".$fecha;
		}


		$where_fechas = "AND TO_DATE(TO_CHAR(R.D_FECHA_ENVIO, 'MM/YYYY'), 'MM/YYYY') >= TO_DATE('$fecha_ini', 'MM/YYYY') AND TO_DATE(TO_CHAR(R.D_FECHA_ENVIO, 'MM/YYYY'), 'MM/YYYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')";
		$where_fechas2 = "TO_DATE(TO_CHAR(R.FECHA, 'MM/YYY'), 'MM/YYY') >= TO_DATE('$fecha_ini', 'MM/YYYY') AND TO_DATE(TO_CHAR(R.FECHA, 'MM/YYY'), 'MM/YYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')";

		$sql ="SELECT R.ID_CLIENTE, CL.V_RAZON_SOCIAL,  'SIN CONTESTAR' AS ESTATUS
		          FROM AD_SGC_ENCUESTA_TIPO_CLIENTE R
		          INNER JOIN CLIENTE CL ON R.ID_CLIENTE = CL.IID_NUM_CLIENTE
		         WHERE R.N_STATUS = 0
												 $where_fechas
												 $where_cliente
									UNION ALL
									SELECT R.ID_CLIENTE, CL.V_RAZON_SOCIAL,  'CONTESTADA' AS ESTATUS
          FROM AD_SGC_ENCUESTA_ENC R
          INNER JOIN CLIENTE CL ON R.ID_CLIENTE = CL.IID_NUM_CLIENTE
         WHERE R.CONSECUTIVO_ENC <> 0 AND
												 $where_fechas2
												 $where_cliente";
				#echo $sql;
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);

				while (($row = oci_fetch_assoc($stid)) != false)
				{
					$res_array[]= $row;
				}
//Conseguirte
		#echo $sql;
				oci_free_statement($stid);
				oci_close($conn);


				return $res_array;

	}

// total pagos
	public function graficaMensual($cliente, $fecha, $mes){

		if ($cliente == "ALL") {
			$where_cliente = " ";
			$where_cliente2 = " ";
		}else {
			$where_cliente = " AND EE.ID_CLIENTE = $cliente";
			$where_cliente2 = " AND ENC.ID_CLIENTE = $cliente";
		}


		if ($mes == 1 ) {
			$fecha_ini = "01/".$fecha;
			$fecha_fin = "06/".$fecha;
		}elseif ($mes == 2) {
			$fecha_ini = "07/".$fecha;
			$fecha_fin = "12/".$fecha;
		}
		$where_fechas = "AND TO_CHAR(EE.FECHA, 'YYYY') = '$fecha'";
		$where_fechas2 = "TO_CHAR(ENC.FECHA, 'YYYY') = '$fecha'";

		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT COUNT(X.TIPO_RESP_NEG) AS NEGATIVAS, COUNT(X.TIPO_RESP_POS) AS POSITIVAS, COUNT(X.TIPO_RESP_MEJOR) AS MEJORAS, RH.N_MES, RH.MES FROM (
						SELECT CASE WHEN EP.RESP_SIONO = ER.RESPUESTA THEN
              					'NEGATIVA'
        					 END AS TIPO_RESP_NEG,
       		 				 CASE WHEN EP.RESP_SIONO <> ER.RESPUESTA THEN
             						'POSITIVA'
        					 END AS TIPO_RESP_POS,
        	 				 '' AS TIPO_RESP_MEJOR,
       		 				 TO_CHAR(EE.FECHA_PROG, 'MM') AS MES
          	FROM 	AD_SGC_ENCUESTA_ENC         EE,
               		AD_SGC_ENCUESTA_DET         ED,
               		AD_SGC_ENCUESTA_PREGUNTA    EP,
               		AD_SGC_ENCUESTA_SUBPREGUNTA ES,
               		AD_SGC_ENCUESTA_RESPUESTA   ER
         		WHERE EE.CONSECUTIVO_ENC = ED.CONSECUTIVO_ENC
           				AND ED.ID_PREGUNTA = EP.ID_PREGUNTA
           				AND ED.ID_SUBPREGUNTA = ES.ID_SUBPREGUNTA(+)
           				AND EP.ID_PREGUNTA = ES.ID_PREGUNTA(+)
           				AND ED.ID_RESPUESTA = ER.ID_RESPUESTA
           				$where_fechas
					 				$where_cliente
				    UNION ALL
 						SELECT CASE WHEN K.RESPUESTA = 'MALO' THEN
            						'NEGATIVA'
            			 END AS TIPO_RESP_NEG,
        	 				 CASE WHEN K.RESPUESTA = 'EXCELENTE' THEN
            						'POSITIVA'
            						WHEN K.RESPUESTA = 'BUENO' THEN
            						'POSITIVA'
           				 END AS TIPO_RESP_POS,
        	 				 CASE  WHEN K.RESPUESTA = 'REGULAR' THEN
            						'MEJORA'
        					 END AS TIPO_RESP_MEJOR,
        	 				 TO_CHAR(ENC.FECHA_PROG, 'MM') AS MES
					   FROM AD_SGC_ENCUESTA_DET L
         		 INNER JOIN AD_SGC_ENCUESTA_ENC ENC ON ENC.CONSECUTIVO_ENC = L.CONSECUTIVO_ENC
         		 INNER JOIN AD_SGC_ENCUESTA_RESPUESTA K ON L.ID_RESPUESTA= K.ID_RESPUESTA
         	 	 INNER JOIN AD_SGC_ENCUESTA_SUBPREGUNTA I ON L.ID_SUBPREGUNTA = I.ID_SUBPREGUNTA
						 WHERE $where_fechas2
						 			 $where_cliente2 ) X
				     RIGHT OUTER JOIN RH_MESES_GRAFICAS RH ON RH.N_MES = X.MES
						 GROUP BY RH.MES, RH.N_MES
						 ORDER BY RH.N_MES";

	#	echo $sql;
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

	/*++++++++++++++++++++++++ SQL TABLA DETALLE BAJA ++++++++++++++++++++++++*/
	public function tablaBaja($cliente, $fecha, $mes)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($cliente == "ALL") {
			$where_cliente = " ";
		}else {
			$where_cliente = " AND ENCE.ID_CLIENTE = $cliente";
		}
		if ($mes == 1 ) {
			$fecha_ini = "01/".$fecha;
			$fecha_fin = "06/".$fecha;
		}elseif ($mes == 2) {
			$fecha_ini = "07/".$fecha;
			$fecha_fin = "12/".$fecha;
		}

		$where_fechas = " WHERE TO_DATE(TO_CHAR(ENCE.FECHA, 'MM/YYY'),'MM/YYY') >= TO_DATE('$fecha_ini', 'MM/YYYY') AND TO_DATE(TO_CHAR(ENCE.FECHA, 'MM/YYY'), 'MM/YYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')";



		$sql = "SELECT ENCE.CONSECUTIVO_ENC, CL.V_RAZON_SOCIAL, ENCE.USUARIO, ENCE.PUESTO, TO_CHAR(ENCE.FECHA, 'DD/MM/YYYY') AS FECHA_PROG, TO_CHAR(ENCE.FECHA, 'DD/MM/YYYY') AS FECHA
       			FROM AD_SGC_ENCUESTA_ENC ENCE
       			INNER JOIN CLIENTE CL ON ENCE.ID_CLIENTE = CL.IID_NUM_CLIENTE
						$where_fechas
						$where_cliente
			 			ORDER BY ENCE.CONSECUTIVO_ENC ";

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

	public function grafica_Pregunta($tipo, $cliente, $fecha, $mes)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($cliente == "ALL") {
			$where_cliente = " ";
		}else {
			$where_cliente = " AND B.ID_CLIENTE = $cliente";
		}


		if ($mes == 1 ) {
			$fecha_ini = "01/".$fecha;
			$fecha_fin = "06/".$fecha;
		}elseif ($mes == 2) {
			$fecha_ini = "07/".$fecha;
			$fecha_fin = "12/".$fecha;
		}

		switch ($tipo) {
			case '1':
				$subpreg = 'ATENCIÓN COMERCIAL';
				break;
			case '2':
				$subpreg = 'PAGO DE IMPUESTOS';
				break;
			case '3':
				$subpreg = 'ATENCIÓN OPERATIVA A LA ENTRADA DE SU MERCANCÍA';
				break;
			case '4':
				$subpreg = 'ATENCIÓN OPERATIVA A LA SALIDA DE SU MERCANCÍA';
				break;
			case '5':
				$subpreg = 'CONTROL, ORGANIZACIÓN Y REPORTE DE INVENTARIOS';
				break;
			case '6':
				$subpreg = 'FACTURACIÓN, COBRANZA Y ACLARACIÓN DE FACTURAS';
				break;
			case '7':
					$subpreg = 'ATENCIÓN OPERATIVA';
					break;
			case '8':
					$subpreg = 'EMISIÓN Y LIBERACIÓN DE CERTIFICADOS';
					break;
			default:
				// code...
				break;
		}

		$sql = "SELECT COUNT(F.RESPUESTA) AS CANTIDAD_RESPUESTA, F.RESPUESTA  FROM  AD_SGC_ENCUESTA_ENC B
		         INNER JOIN AD_SGC_ENCUESTA_DET A ON A.CONSECUTIVO_ENC = B.CONSECUTIVO_ENC
		         INNER JOIN AD_SGC_ENCUESTA_SUBPREGUNTA D ON A.ID_PREGUNTA = D.ID_PREGUNTA AND D.ID_SUBPREGUNTA = A.ID_SUBPREGUNTA AND B.ENCUESTA_TIPO = D.TIPO_ENCUESTA
		         INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON A.ID_RESPUESTA = F.ID_RESPUESTA
		         WHERE D.SUBPREGUNTA = '$subpreg'
						 			$where_cliente
		               AND TO_DATE(TO_CHAR(B.FECHA, 'MM/YYYY'), 'MM/YYYY') >= TO_DATE('$fecha_ini', 'mm/yyyy') AND TO_DATE(TO_CHAR(B.FECHA, 'MM/YYYY'), 'mm/YYYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')
		         GROUP BY F.RESPUESTA";

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


	public function grafica_PreguntaTabla_Det($tipo, $cliente, $fecha, $mes)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($cliente == "ALL") {
			$where_cliente = " ";
		}else {
			$where_cliente = " AND B.ID_CLIENTE = $cliente";
		}

		if ($mes == 1 ) {
			$fecha_ini = "01/".$fecha;
			$fecha_fin = "06/".$fecha;
		}elseif ($mes == 2) {
			$fecha_ini = "07/".$fecha;
			$fecha_fin = "12/".$fecha;
		}

		switch ($tipo) {
			case '1':
				$subpreg = 'ATENCIÓN COMERCIAL';
				break;
			case '2':
				$subpreg = 'PAGO DE IMPUESTOS';
				break;
			case '3':
				$subpreg = 'ATENCIÓN OPERATIVA A LA ENTRADA DE SU MERCANCÍA';
				break;
			case '4':
				$subpreg = 'ATENCIÓN OPERATIVA A LA SALIDA DE SU MERCANCÍA';
				break;
			case '5':
				$subpreg = 'CONTROL, ORGANIZACIÓN Y REPORTE DE INVENTARIOS';
				break;
			case '6':
				$subpreg = 'FACTURACIÓN, COBRANZA Y ACLARACIÓN DE FACTURAS';
				break;
			case '7':
					$subpreg = 'ATENCIÓN OPERATIVA';
					break;
			case '8':
					$subpreg = 'EMISIÓN Y LIBERACIÓN DE CERTIFICADOS';
					break;
			default:
				// code...
				break;
		}

		$sql = "SELECT B.ID_CLIENTE, CL.V_RAZON_SOCIAL, F.RESPUESTA,  F.RESPUESTA2  FROM  AD_SGC_ENCUESTA_ENC B
		         INNER JOIN AD_SGC_ENCUESTA_DET A ON A.CONSECUTIVO_ENC = B.CONSECUTIVO_ENC
		         INNER JOIN AD_SGC_ENCUESTA_SUBPREGUNTA D ON A.ID_PREGUNTA = D.ID_PREGUNTA AND D.ID_SUBPREGUNTA = A.ID_SUBPREGUNTA AND B.ENCUESTA_TIPO = D.TIPO_ENCUESTA
		         INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON A.ID_RESPUESTA = F.ID_RESPUESTA
						 INNER JOIN CLIENTE CL ON B.ID_CLIENTE = CL.IID_NUM_CLIENTE
		         WHERE D.SUBPREGUNTA = '$subpreg'
						 			$where_cliente
		               AND TO_DATE(TO_CHAR(B.FECHA, 'MM/YYYY'), 'MM/YYYY') >= TO_DATE('$fecha_ini', 'mm/yyyy') AND  TO_DATE(TO_CHAR(B.FECHA, 'MM/YYYY'), 'MM/YYYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')";

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

	public function grafica_Pregunta2($tipo, $cliente, $fecha, $mes)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($cliente == "ALL") {
			$where_cliente = " ";
		}else {
			$where_cliente = " AND B.ID_CLIENTE = $cliente";
		}


		if ($mes == 1 ) {
			$fecha_ini = "01/".$fecha;
			$fecha_fin = "06/".$fecha;
		}elseif ($mes == 2) {
			$fecha_ini = "07/".$fecha;
			$fecha_fin = "12/".$fecha;
		}

		switch ($tipo) {
			case '1':
				$subpreg = '¿ACTUALMENTE TIENE ALGUNA INCONFORMIDAD O QUEJA QUE NO HA SIDO CUBIERTA POR ARGO?';
				break;
			case '2':
				$subpreg = '¿RECOMENDARÍA LOS SERVICIOS DE ARGO?';
				break;
			case '3':
				$subpreg = '¿HAN VISITADO NUESTRA PÁGINA WEB?';
				break;
			default:
				// code...
				break;
		}
	#	$subpreg ='¿ESTA CONFORME CON EL SETVICIO QUE SE LE BRINDO POR PARTE DE ARGO ALMACENADORA?';

		$sql = "SELECT COUNT(F.RESPUESTA) AS CANTIDAD_RESPUESTA, F.RESPUESTA  FROM  AD_SGC_ENCUESTA_ENC B
		         INNER JOIN AD_SGC_ENCUESTA_DET A ON A.CONSECUTIVO_ENC = B.CONSECUTIVO_ENC
		         INNER JOIN AD_SGC_ENCUESTA_PREGUNTA D ON A.ID_PREGUNTA = D.ID_PREGUNTA AND B.ENCUESTA_TIPO = D.TIPO_ENCUESTA AND A.ID_CONSECUTIVO = D.IID_CONSECUTIVO
		         INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON A.ID_RESPUESTA = F.ID_RESPUESTA
		         WHERE D.PREGUNTA = '$subpreg'
		               AND TO_DATE(TO_CHAR(B.FECHA, 'MM/YYYY'), 'MM/YYYY') >= TO_DATE('$fecha_ini', 'mm/yyyy') AND TO_DATE(TO_CHAR(B.FECHA, 'MM/YYYY'), 'MM/YYYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')
									 $where_cliente
		         GROUP BY F.RESPUESTA";

#echo $SQL;

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

	public function grafica_Pregunta2Tabla_Det($tipo, $cliente, $fecha, $mes)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($cliente == "ALL") {
			$where_cliente = " ";
		}else {
			$where_cliente = " AND B.ID_CLIENTE = $cliente";
		}

		if ($mes == 1 ) {
			$fecha_ini = "01/".$fecha;
			$fecha_fin = "06/".$fecha;
		}elseif ($mes == 2) {
			$fecha_ini = "07/".$fecha;
			$fecha_fin = "12/".$fecha;
		}

		switch ($tipo) {
			case '1':
				$subpreg = '¿ACTUALMENTE TIENE ALGUNA INCONFORMIDAD O QUEJA QUE NO HA SIDO CUBIERTA POR ARGO?';
				break;
			case '2':
				$subpreg = '¿RECOMENDARÍA LOS SERVICIOS DE ARGO?';
				break;
			case '3':
				$subpreg = '¿HAN VISITADO NUESTRA PÁGINA WEB?';
				break;
			default:
				// code...
				break;
		}
	#	$subpreg ='¿ESTA CONFORME CON EL SETVICIO QUE SE LE BRINDO POR PARTE DE ARGO ALMACENADORA?';

		$sql = "SELECT b.id_cliente, CL.V_RAZON_SOCIAL , F.RESPUESTA, F.RESPUESTA2  FROM  AD_SGC_ENCUESTA_ENC B
		         INNER JOIN AD_SGC_ENCUESTA_DET A ON A.CONSECUTIVO_ENC = B.CONSECUTIVO_ENC
		         INNER JOIN AD_SGC_ENCUESTA_PREGUNTA D ON A.ID_PREGUNTA = D.ID_PREGUNTA AND B.ENCUESTA_TIPO = D.TIPO_ENCUESTA AND A.ID_CONSECUTIVO = D.IID_CONSECUTIVO
		         INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON A.ID_RESPUESTA = F.ID_RESPUESTA
						 INNER JOIN CLIENTE CL ON B.ID_CLIENTE = CL.IID_NUM_CLIENTE
		         WHERE D.PREGUNTA = '$subpreg'
		               AND TO_DATE(TO_CHAR(B.FECHA, 'MM/YYYY'), 'MM/YYYY') >= TO_DATE('$fecha_ini', 'mm/yyyy') AND TO_DATE(TO_CHAR(B.FECHA, 'MM/YYYY'), 'MM/YYYY') <= TO_DATE('$fecha_fin', 'MM/YYYY')
									 $where_cliente";

#echo $SQL;

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

	public function tablaBajaDet($encuesta)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($cliente == "ALL") {
			$where_cliente = " ";
		}else {
			$where_cliente = " AND EE.ID_CLIENTE = $cliente";
		}


		$fecha_ini =  substr($fecha,0,10);
		$fecha_fin = substr($fecha,11,10);
		$where_fechas = "AND ENCE.FECHA >= TO_DATE('$fecha_ini', 'DD/MM/YYYY') AND ENCE.FECHA <= TO_DATE('$fecha_fin', 'DD/MM/YYYY')";


		$sql = "SELECT ENCP.PREGUNTA,
						       ENCP.RESP_SIONO,
						       ENCR.RESPUESTA,
						       ENCR.RESPUESTA2,
						       CASE
						            WHEN ENCP.RESP_SIONO = ENCR.RESPUESTA THEN 'NEGATIVA'
						            WHEN ENCP.RESP_SIONO <> ENCR.RESPUESTA THEN 'POSITIVA'
						       END AS TIPO_RES
						FROM AD_SGC_ENCUESTA_ENC ENCE
						         INNER JOIN AD_SGC_ENCUESTA_DET ENCD ON ENCE.CONSECUTIVO_ENC = ENCD.CONSECUTIVO_ENC
						         INNER JOIN AD_SGC_ENCUESTA_PREGUNTA ENCP ON ENCP.ID_PREGUNTA = ENCD.ID_PREGUNTA
						         LEFT OUTER JOIN AD_SGC_ENCUESTA_SUBPREGUNTA ENDS ON ENDS.ID_SUBPREGUNTA = ENCD.ID_SUBPREGUNTA AND ENDS.ID_PREGUNTA = ENCP.ID_PREGUNTA
						         INNER JOIN AD_SGC_ENCUESTA_RESPUESTA ENCR ON ENCR.ID_RESPUESTA = ENCD.ID_RESPUESTA
						         WHERE ENCE.CONSECUTIVO_ENC =  $encuesta";

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
					echo $sql;
					break;
				case '2':
					$sql = "SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep";
					break;
				case '3':
					$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
					break;
					case '4':
						if ($depto <> "ALL"){
							$where_plaza = " AND REPLACE(P.v_razon_social, ' (ARGO)') LIKE '$depto'" ;
							$wheredef = "	= (SELECT P.IID_PLAZA FROM PLAZA P WHERE P.IID_PLAZA IN (2,3,4,5,6,7,8,17,18) $where_plaza)  AND ALM.S_STATUS = 1 ";
						}
						else {
							$wheredef = "  IN (2,3,4,5,6,7,8,17,18)  AND ALM.S_STATUS = 1 ";
						}
						$sql = "SELECT ALM.V_NOMBRE, ALM.IID_ALMACEN, ALM.IID_PLAZA FROM ALMACEN ALM WHERE ALM.IID_PLAZA $wheredef";
										echo $sql;
						break;

						case '5':
						if ($depto == "ALL") {
							$where = " ";
						}else {
							$where = " AND c.iid_almacen = $depto";
						}
							$sql = "SELECT DISTINCT a.iid_num_cliente, d.v_razon_social, d.v_nombre_corto
											  FROM co_convenio_contrato a
											 INNER JOIN co_convenio_contrato_anexos b ON b.nid_folio = a.nid_folio
											                                         AND b.s_status = 1
											 INNER JOIN co_convenio_contrato_almacen c ON c.nid_folio = a.nid_folio
											                                          AND c.s_tipo = a.s_tipo
											 INNER JOIN cliente d ON d.iid_num_cliente = a.iid_num_cliente
											 WHERE a.s_status IN (2, 3)
											   			$where";
															#echo $sql;
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

	function consultaReal($plaza, $tipo, $fecha, $mes, $cliente){
			$conn = conexion::conectar();
			$res_array = array();
			if ($plaza == 0) {
				$where_plaza = " ";
			}else {
				$where_plaza = " WHERE Z.PLAZA = $plaza";
			}

			if ($mes == 1 ) {
				$fecha_ini = "01/".$fecha;
				$fecha_fin = "06/".$fecha;
			}elseif ($mes == 2) {
				$fecha_ini = "07/".$fecha;
				$fecha_fin = "12/".$fecha;
			}

			$fechaF = "AND TO_DATE(TO_CHAR(A.FECHA, 'MM/YYY'), 'MM/YYY') >= TO_DATE('$fecha_ini', 'mm/yyyy')  AND TO_DATE(TO_CHAR(A.FECHA, 'MM/YYY'), 'MM/YYY') <= TO_DATE('$fecha_fin', 'mm/yyyy') ";

			if ($cliente == "ALL") {
				$were_cl = " ";
			}else {
					$were_cl = " AND A.ID_CLIENTE = $cliente";
			}

			$sql = "SELECT SUM(Z.TOTAL_RESPONDIDAS) AS TR, SUM(Z.TOTAL_PREG) AS TP, (SUM(Z.TOTAL_RESPONDIDAS)/SUM(Z.TOTAL_PREG))* 100 AS PORCENTAJE, Z.CONSECUTIVO_ENC, Z.PLAZA
							FROM (
							  SELECT X.PLAZA , SUM(X.TOTAL) AS TOTAL_RESPONDIDAS, 0 AS TOTAL_PREG, X.CONSECUTIVO_ENC FROM (
							    SELECT   DISTINCT(PLA.IID_PLAZA) AS PLAZA, COUNT(*) AS TOTAL, 'SUBPREGUNTAS', A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
							             INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
							             INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND A.ID_CLIENTE = C.ID_CLIENTE
							             INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC
							             INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA AND E.IID_CONS = D.ID_SUBPREGUNTA
							             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
							             INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
							             INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
							             INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
							             INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
							             INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
							    WHERE (F.RESPUESTA = 'EXCELENTE' OR F.RESPUESTA = 'BUENO')
							          AND E.IID_PREGUNTA IN(1, 6)
							          AND B.N_TIPO = $tipo
												$fechaF
												$were_cl
							    GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
							    UNION ALL
							    SELECT DISTINCT(PLA.IID_PLAZA) AS PLAZA , COUNT(*) AS TOTAL, 'PREGUNTAS DEPENDIENTES NO POSITIVAS', A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
							             INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
							             INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND A.ID_CLIENTE = C.ID_CLIENTE
							             INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC AND D.ID_SUBPREGUNTA IS NULL
							             INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA
							             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
							             INNER JOIN AD_SGC_ENCUESTA_PREGUNTA F ON F.ID_PREGUNTA = D.ID_PREGUNTA AND F.TIPO_ENCUESTA = A.ENCUESTA_TIPO
							             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA G ON G.ID_RESPUESTA = D.ID_RESPUESTA
							             INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
							             INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
							             INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
							             INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
							             INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
							    WHERE F.RESPUESTA IN('NO')
							          AND B.N_TIPO = $tipo
							          AND E.IID_PREGUNTA IN (2)
							          AND F.DEPENDIENTE IS NULL
							          AND E.TIPO_PREGUNTA <> 1
												$fechaF
												$were_cl
							    GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
							    UNION ALL
							    SELECT DISTINCT(PLA.IID_PLAZA) AS PLAZA, COUNT(*) AS TOTAL, 'PREGUNTAS DEPENDIENTES NO POSITIVAS', A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
							             INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
							             INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND A.ID_CLIENTE = C.ID_CLIENTE
							             INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC AND D.ID_SUBPREGUNTA IS NULL
							             INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA
							             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
							             INNER JOIN AD_SGC_ENCUESTA_PREGUNTA F ON F.ID_PREGUNTA = D.ID_PREGUNTA AND F.TIPO_ENCUESTA = A.ENCUESTA_TIPO
							             INNER JOIN AD_SGC_ENCUESTA_RESPUESTA G ON G.ID_RESPUESTA = D.ID_RESPUESTA
							             INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
							             INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
							             INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
							             INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
							             INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
							    WHERE F.RESPUESTA IN('SI')
							          AND B.N_TIPO = $tipo
							          AND E.IID_PREGUNTA IN (3)
							          AND F.DEPENDIENTE IS NULL
							          AND E.TIPO_PREGUNTA <> 1
												$fechaF
												$were_cl
							    GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
							  ) X
							  GROUP BY X.CONSECUTIVO_ENC, X.PLAZA
							  UNION ALL
							  SELECT Y.PLAZA, 0 AS TOTAL_RESPONDIDAS, SUM(Y.TOTAL) AS TOTAL_PREG , Y.CONSECUTIVO_ENC FROM (
							  SELECT   DISTINCT(PLA.IID_PLAZA) AS PLAZA , COUNT(*) AS TOTAL,  A.CONSECUTIVO_ENC   FROM AD_SGC_ENCUESTA_ENC A
							           INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
							           INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND A.ID_CLIENTE = C.ID_CLIENTE
							           INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC
							           INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA
							           INNER JOIN AD_SGC_ENCUESTA_PREGUNTA F ON F.ID_PREGUNTA = D.ID_PREGUNTA AND F.TIPO_ENCUESTA = A.ENCUESTA_TIPO
							           INNER JOIN AD_SGC_ENCUESTA_RESPUESTA G ON G.ID_RESPUESTA = D.ID_RESPUESTA
							           INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
							           INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
							           INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
							           INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
							           INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
							  WHERE E.IID_PREGUNTA IN (2,3)
							        AND B.N_TIPO = $tipo
							        AND F.DEPENDIENTE IS NULL
							        AND (D.ID_SUBPREGUNTA IS NULL OR F.RESP_SIONO IS NOT NULL)
							        AND E.TIPO_PREGUNTA <> 1
							        AND G.RESPUESTA IN ('SI', 'NO')
											$fechaF
											$were_cl
							  GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
							  UNION ALL
							  SELECT   DISTINCT(PLA.IID_PLAZA) AS PLAZA ,  COUNT(*)AS TOTAL , A.CONSECUTIVO_ENC FROM AD_SGC_ENCUESTA_ENC A
							           INNER JOIN AD_SGC_ENCUESTA_TIPO B ON A.ENCUESTA_TIPO = B.ID_TIPO
							           INNER JOIN AD_SGC_ENCUESTA_TIPO_CLIENTE C ON C.ID_TIPO = A.ENCUESTA_TIPO AND A.ID_CLIENTE = C.ID_CLIENTE
							           INNER JOIN AD_SGC_ENCUESTA_DET D ON A.CONSECUTIVO_ENC = D.CONSECUTIVO_ENC
							           INNER JOIN AD_SGC_CAT_PREGUNTAS E ON D.ID_PREGUNTA = E.IID_PREGUNTA AND E.IID_CONS = D.ID_SUBPREGUNTA
							           INNER JOIN AD_SGC_ENCUESTA_RESPUESTA F ON F.ID_RESPUESTA = D.ID_RESPUESTA
							           INNER JOIN CO_CONVENIO_CONTRATO H ON C.ID_CLIENTE = H.IID_NUM_CLIENTE AND H.S_STATUS = 2
							           INNER JOIN CO_CONVENIO_CONTRATO_ANEXOS I ON H.NID_FOLIO = I.NID_FOLIO AND H.S_TIPO = I.S_TIPO AND I.N_COMPLETO = 1 AND I.S_STATUS = 1
							           INNER JOIN CO_CONVENIO_CONTRATO_ALMACEN J ON J.NID_FOLIO = I.NID_FOLIO AND I.S_TIPO = J.S_TIPO AND I.I_ANEXO = J.I_ANEXO
							           INNER JOIN ALMACEN  ALM ON J.IID_ALMACEN = ALM.IID_ALMACEN
							           INNER JOIN PLAZA PLA ON ALM.IID_PLAZA = PLA.IID_PLAZA
							  WHERE E.IID_PREGUNTA IN(1, 6)
							        AND B.N_TIPO = $tipo
											$fechaF
											$were_cl
							  GROUP BY A.CONSECUTIVO_ENC, PLA.IID_PLAZA
							  ) Y
							  GROUP BY Y.CONSECUTIVO_ENC, Y.PLAZA
							)Z
							$where_plaza
							GROUP BY Z.CONSECUTIVO_ENC, Z.PLAZA
							ORDER BY Z.PLAZA";
							#
							if ($plaza == 4) {
								// code...
										#echo $sql;
							}

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false){
								$res_array[]= $row;
    }

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;

	}


}
