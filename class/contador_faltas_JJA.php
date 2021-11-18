
<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: DIEGO ALTAMIRANO.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class FaltasJefes
{
	public function empleados_Faltantes(){
		$conn = conexion::conectar();
		$res_array = array();
		//echo $fecha;

			$sql = "SELECT N.V_NOMBRE || ' ' || N.V_APE_PAT || ' ' || N.V_APE_MAT AS NOMBRE, K.S_DESCRIPCION,  TO_CHAR(L.D_FEC_INICIO, 'DD/MM/YYYY') AS D_FEC_INICIO, TO_CHAR(L.D_FEC_FIN, 'DD/MM/YYYY') AS D_FEC_FIN, L.C_DIAS_FALTA FROM RH_FALTAS L
                       INNER JOIN NO_PERSONAL N ON L.IID_EMPLEADO = N.IID_EMPLEADO
                       INNER JOIN RH_FALTAS_CAT K ON L.ID_TIPO_FALTA = K.ID_TIPO_FALTA
             WHERE (L.d_Fec_Inicio >= TO_DATE(TO_CHAR(SYSDATE, 'DD/MM/YYYY'), 'DD/MM/YYYY') AND L.D_FEC_FIN <= TO_DATE(TO_CHAR(SYSDATE, 'DD/MM/YYYY'), 'DD/MM/YYYY'))
						 AND L.IID_EMPLEADO IN (1144,1959,1441,1193,1300,1570,2208,2390,2423,1173,7,1898)";
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
}
