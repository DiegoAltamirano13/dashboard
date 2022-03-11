<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: DIEGO ALTAMIRANO
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class Minuta_Proyecto
{

 /* funcion tabla */
	/*====================== /*GRAFICA DE NOMINA PAGADA ======================*/

public function info_Gral($proyecto){
		#echo $fecha;

		$conn = conexion::conectar();

		$res_array = array();

		$sql = "SELECT T.DESCRIPCION_ACTIVIDAD, TO_CHAR(T.FECHA_REALIZA_SOLICITUD, 'dd/mm/yyyy') AS FECHA_REALIZA_SOLICITUD , TO_CHAR(T.FECHA_TIEMPO_FIN, 'DD/MM/YYYY') AS FECHA_FIN FROM AD_DIR_PROYECTOS T WHERE T.ID_PROYECTO = $proyecto";


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


	public function info_Designados($proyecto, $tipo, $empleado){
			#echo $fecha;

			$conn = conexion::conectar();

			$res_array = array();

			if ($tipo == 1 ) {
				$where = "WHERE F.IID_USUARIO_AUT = $empleado";
			}else {
				$where = " WHERE E.ID_EMPLEADO_RESP = $empleado";
			}
			//$sql = "SELECT TO_CHAR(E.FECHA_COMENTARIO, 'DD/MM/YYYY') AS FECHA, E.COMENTARIO FROM AD_DIR_COMENTARIOS_SOL E WHERE E.ID_PROYECTO = $proyecto";
			$sql ="SELECT TO_CHAR(E.FECHA_COMENTARIO, 'DD/MM/YYYY') AS FECHA, E.COMENTARIO FROM RH_CAT_AREAS F
         INNER JOIN NO_CONTRATO N ON F.IID_AREA = N.IID_AREA AND F.IID_DEPTO = N.IID_DEPTO
         INNER JOIN NO_PERSONAL NP ON N.IID_CONTRATO = NP.IID_CONTRATO AND N.IID_EMPLEADO = NP.IID_EMPLEADO
         INNER JOIN AD_DIR_COMENTARIOS_SOL E ON E.ID_EMPLEADO_RESP = NP.IID_EMPLEADO
				 $where
				 AND E.ID_PROYECTO = $proyecto
				 ORDER BY E.FECHA_COMENTARIO";


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
}
	/*====================== /.VALIDA SI ES FECHA  ======================*/
?>
