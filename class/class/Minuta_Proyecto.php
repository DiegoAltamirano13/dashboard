<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
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

		$sql = "SELECT T.DESCRIPCION_ACTIVIDAD,
									 TO_CHAR(T.FECHA_REALIZA_SOLICITUD, 'dd/mm/yyyy') AS FECHA_REALIZA_SOLICITUD ,
									 TO_CHAR(T.FECHA_TIEMPO_FIN, 'DD/MM/YYYY') AS FECHA_FIN,
									 S.V_NOMBRE AS NOMBRE
						FROM AD_DIR_PROYECTOS T
 						INNER JOIN SE_USUARIOS S ON T.ID_EMPLEADO_SOLICITA = S.IID_EMPLEADO
						WHERE T.ID_PROYECTO = $proyecto";


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


	public function info_Designados($proyecto){
			#echo $fecha;

			$conn = conexion::conectar();

			$res_array = array();

			$sql = "SELECT N.V_NOMBRE || ' ' || N.V_APE_PAT || ' ' || N.V_APE_MAT AS NOMBRE, S.ACTIVIDAD, TO_CHAR(s.fecha_estimada, 'DD/MM/YYYY') AS FECHA FROM AD_DIR_JEFE_AREA_RESP S
        					INNER JOIN NO_PERSONAL N ON N.IID_EMPLEADO = S.ID_JEFE_AREA
        					WHERE S.ID_PROYECTO = $proyecto";


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
