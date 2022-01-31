<?php

include_once '../libs/conOra.php'; 

/**
* 
*/ 

// ################################### INICIA CLASE PARA LEER-INSERTAR-ACTUALIZAR-BORRAR EXCEL INFO FINANCIERA ################################### 
class Informacion_financiera
{

//METODO PARA LEER REGISTROS DE ARCHIVOS EN LA DB
	public function leer_excel_info_finan()
	{
		$conn = conexion::conectar();

		$sql = "SELECT excel.iid_excel_info_finan AS id_excel, excel.v_nombre_excel AS nombre_excel, excel.v_extension_excel AS extension_excel, excel.v_titulo_excel AS titulo_excel, excel.n_size_excel AS size_excel, to_char(excel.d_fecha_info_finan, 'yyyy-mm-dd') AS fecha_excel
				FROM ct_dashboard_excel_info_finan excel WHERE excel.n_status IN (1,2)";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$this->res_leer_excel_info_finan[]=$row;
		}

		oci_free_statement($stid);
		oci_close($conn);
		return $this->res_leer_excel_info_finan; 
	}

//METODO PARA INSERTAR INFORMACION DEL ARCHIVO EN LA DB
	public function insertar_excel_info_finan($nombre_excel,$titulo_excel,$size_excel,$fecha_info_finan,$usuario_insert,$fecha_insert,$status,$extension)
	{
		$conn = conexion::conectar(); 
		
		$sql = "INSERT INTO ct_dashboard_excel_info_finan( V_NOMBRE_EXCEL,V_TITULO_EXCEL,N_SIZE_EXCEL,D_FECHA_INFO_FINAN,N_USUARIO_INSERT,D_FECHA_INSERT,N_STATUS,V_EXTENSION_EXCEL) 
				VALUES ( '".$nombre_excel."', '".$titulo_excel."', ".$size_excel.", to_date('".$fecha_info_finan."', 'yyyy/mm/dd'), ".$usuario_insert.", to_date('".$fecha_insert."', 'yyyy/mm/dd hh24:mi:ss'), ".$status.", '".$extension."' )";

		$stmt = oci_parse($conn, $sql);

			oci_execute($stmt);//EJECUTAMOS CONEXION
			oci_commit($conn);
			oci_free_statement($stmt);
			oci_close($conn); //CERRAMOS CONEXION  

	}

//METODO PARA ELIMINAR REGISTROS DE ARCHIVOS EN LA DB (SOLO ACTUALIZAMOS EL STATUS DEL ARCHIVO PARA NO BORRAR LOS REG)
	public function delete_excel_info_finan($iid_excel_info_finan,$id_usuario,$fecha_delete)
	{
		$conn = conexion::conectar();
		$n_status = 3;  

        // $stid = oci_parse($conn, "UPDATE ct_dashboard_excel_info_finan 
        //   SET n_status = :n_status, n_usuario_delete = :n_usuario_delete, d_fecha_delete = to_date(:d_fecha_delete, 'yyyy/mm/dd HH24:MI:SS')
        //   WHERE iid_excel_info_finan= :iid_excel_info_finan");

        $stid = oci_parse($conn, "UPDATE ct_dashboard_excel_info_finan 
          SET n_status = ".$n_status.", n_usuario_delete = ".$id_usuario.", d_fecha_delete = to_date('".$fecha_delete."', 'yyyy/mm/dd HH24:MI:SS')
          WHERE iid_excel_info_finan= ".$iid_excel_info_finan." ");


        // oci_bind_by_name($stid, ":iid_excel_info_finan", $iid_excel_info_finan);
        // oci_bind_by_name($stid, ":n_status", $n_status);
        // oci_bind_by_name($stid, ":n_usuario_delete", $id_usuario);
        // oci_bind_by_name($stid, ":d_fecha_delete", $fecha_delete);
        oci_execute($stid, OCI_NO_AUTO_COMMIT); 
        oci_commit($conn);
        oci_free_statement($stid);
		oci_close($conn); //CERRAMOS CONEXION  


	}

//METODO PARA ACTUALIZAR REGISTROS DE ARCHIVOS EN LA DB  
	public function update_excel_info_finan($id_excel,$nombre_excel,$update_titulo_excel,$size_excel,$update_fecha_excel,$usuario_update,$fecha_update,$status,$extension)
	{
		$conn = conexion::conectar(); 

		if($nombre_excel == true && $size_excel == true && $extension == true)
		{
			$update = "UPDATE ct_dashboard_excel_info_finan 
          			   SET v_nombre_excel='".$nombre_excel."', v_extension_excel ='".$extension."', v_titulo_excel='".$update_titulo_excel."', n_size_excel=".$size_excel.", d_fecha_info_finan=to_date('".$update_fecha_excel."', 'yyyy/mm/dd'), n_status=".$status.", n_usuario_update=".$usuario_update.", d_fecha_update=to_date('".$fecha_update."', 'yyyy/mm/dd HH24:MI:SS')
          			   WHERE iid_excel_info_finan= ".$id_excel." ";
		}else{
			$update = "UPDATE ct_dashboard_excel_info_finan 
          			   SET v_titulo_excel='".$update_titulo_excel."', d_fecha_info_finan=to_date('".$update_fecha_excel."', 'yyyy/mm/dd'), n_status=".$status.", n_usuario_update=".$usuario_update.", d_fecha_update=to_date('".$fecha_update."', 'yyyy/mm/dd HH24:MI:SS')
          			   WHERE iid_excel_info_finan= ".$id_excel." ";
		} 

		$stid = oci_parse($conn, $update);

        oci_execute($stid, OCI_NO_AUTO_COMMIT); 
        oci_commit($conn);
        oci_free_statement($stid);
		oci_close($conn);

	}

//METODO PARA HISTORIAL DE INFORMACION FINANCIERA
	public function historial_info_finan()
	{  
		$conn = conexion::conectar(); 

		$sql = "SELECT excel.iid_excel_info_finan AS id_excel, excel.v_nombre_excel AS nombre_excel, excel.v_titulo_excel AS titulo_excel, TO_CHAR(excel.d_fecha_info_finan, 'dd-mm-yyyy') AS fecha
				FROM ct_dashboard_excel_info_finan excel
				WHERE excel.n_status <> 3
				ORDER by excel.d_fecha_info_finan DESC, excel.iid_excel_info_finan DESC";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid ); 

		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$this->res_historial_info_finan[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_historial_info_finan; 
	}

//METODO PARA LEER EL ULTIMO REGISTRO DE INFORMACION FINANCIERA
	public function info_finan_ultimo()
	{  
		$conn = conexion::conectar(); 

		$sql = "SELECT * FROM (
				SELECT excel.iid_excel_info_finan AS id_excel, excel.v_nombre_excel AS nombre_excel, excel.v_titulo_excel AS titulo_excel, TO_CHAR(excel.d_fecha_info_finan, 'dd-mm-yyyy') AS fecha
				FROM ct_dashboard_excel_info_finan excel
				WHERE excel.n_status <> 3
				ORDER by excel.d_fecha_info_finan DESC, excel.iid_excel_info_finan DESC
				)
				WHERE ROWNUM <= 1";

		$stid = oci_parse($conn, $sql);

		oci_execute($stid);
		$res_info_finan_ultimo = oci_fetch_array($stid, OCI_BOTH);
		oci_free_statement($stid);
		oci_close($conn);
		return $res_info_finan_ultimo;
	}

//METODO PARA LEER ARCHIVO EXCEL CON PHPEXCEL
	public function leer_excel_phpexcel($historial_info_finan_id)
	{  
		$conn = conexion::conectar(); 

				$sql = "SELECT excel.iid_excel_info_finan AS id_excel, excel.v_nombre_excel AS nombre_excel, excel.v_titulo_excel AS titulo_excel, TO_CHAR(excel.d_fecha_info_finan, 'dd-mm-yyyy') AS fecha
						FROM ct_dashboard_excel_info_finan excel
        				WHERE excel.iid_excel_info_finan = ".$historial_info_finan_id." AND excel.n_status <> 3 ";
				$stid = oci_parse($conn, $sql);
				oci_execute($stid ); 

				while (($row = oci_fetch_assoc($stid)) != false) 
				{
					$this->res_leer_excel_phpexcel[]=$row;
				}
					oci_free_statement($stid);
					oci_close($conn);
					return $this->res_leer_excel_phpexcel; 
	}



//METODO PARA LOS WIDGETS SUBIR EXCEL
	public function widgets_reg_excel_info_finan()
	{  
		$conn = conexion::conectar(); 

				$sql = "SELECT * FROM
						(SELECT COUNT(*) AS total_reg FROM ct_dashboard_excel_info_finan excel WHERE excel.n_status IN (1,2) ),
						(SELECT COUNT(*) AS total_reg_update FROM ct_dashboard_excel_info_finan excel WHERE excel.n_status = 2 ),
						(SELECT COUNT(*) AS total_reg_delete FROM ct_dashboard_excel_info_finan excel WHERE excel.n_status = 3 )";
				$stid = oci_parse($conn, $sql);
				oci_execute($stid ); 

				while (($row = oci_fetch_assoc($stid)) != false) 
				{
					$this->res_widgets_reg_excel[]=$row;
				}
					oci_free_statement($stid);
					oci_close($conn);
					return $this->res_widgets_reg_excel; 
	} 


}
// ################################### TERMINA CLASE PARA LEER-INSERTAR-ACTUALIZAR-BORRAR EXCEL INFO FINANCIERA ###################################