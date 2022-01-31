<?php 
/**
* © Argo Almacenadora ®
* Fecha: 26/12/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Comercial Comparativos ingresos de clientes
* Version --
*/
include_once '../libs/conOra.php'; 

class Cursor
{ 

// ************************** FUNCION PARA CONSULTAR LA FECHA EN LA BASE **************************  //
	function graficaVenta()
	{
		$conn = conexion::conectar();

		$res_consulta_fecha = array();
		$n_anio = 2018;
		$v_id_promotor = 65; 
		$cur_posicion ;

		$curs = oci_new_cursor($conn);/**/
		

		$curs = oci_new_cursor($conn);
		$stid = oci_parse($conn, "begin PCK_DASHBOARD.CUR_VENTA_VS_PRESUPUESTO(:n_anio,:v_id_promotor,:cur_posicion); end;");
		oci_bind_by_name($stid, ':n_anio', $n_anio);
		oci_bind_by_name($stid, ':v_id_promotor', $v_id_promotor);
		oci_bind_by_name($stid, ":cur_posicion", $curs, -1, OCI_B_CURSOR);
		oci_execute($stid);

		oci_execute($curs);  // Ejecutar el REF CURSOR como un ide de sentencia normal
		while (($row = oci_fetch_array($curs, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		    //echo $row['FIRST_NAME'] . "<br />\n";
		    $res_consulta_fecha[]=$row;
		}

		oci_free_statement($stid);
		oci_free_statement($curs);
		oci_close($conn);
		return $res_consulta_fecha;	

		 $sql = "Select f.iid_num_cliente, f.iid_plaza, to_char(t.d_fecha_movto,'yyyy') AS anio ,to_char(t.d_fecha_movto,'mm') as mes
,NVL (
( NVL (sum ( decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2)) ) ,0) )-
( NVL (sum ( decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2)) ) ,0) )
,0) AS facturado_total
from ad_cxc_movtos t, ad_fa_factura f, co_clientes_tipo_reporte r
where t.iid_plaza = f.iid_plaza and t.iid_folio = f.iid_folio and f.iid_num_cliente = r.iid_num_cliente and
f.status = 7 and t.n_status = 2 and t.n_tipo_movto in (1,3,4)
AND to_char(t.d_fecha_movto,'yyyy') = 2018 and f.iid_num_cliente = 2630
group by  f.iid_num_cliente, to_char(t.d_fecha_movto,'yyyy') ,f.iid_plaza ,to_char(t.d_fecha_movto,'mm') 
ORDER BY mes";

	}

 


}

?>