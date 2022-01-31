<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class descargaImagenes
{
	public function descargaImagen(){
  $local_file = '/var/www/html/dashboard/imagenes_remates/';//Ruta local donde guardara archivos
  $server_file = '/home/soldet/remates/';//Ruta de servidor ftp donde se encuentran archivos a copiar

  //datos de conexion
  $ftp_server='10.10.1.13';
  $ftp_user_name='soldet';
  $ftp_user_pass='soldet';

  $conn_id = ftp_connect($ftp_server);
  $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

  $content = ftp_nlist($conn_id, $server_file); // lista de contenido de la ruta especificada
//  var_dump($content);

  for($i=0; $i<count($content);$i++){//recorre areglo de rutas
                  list($cero, $uno, $dos, $tres, $cuatro) = preg_split('_[\\\\/]_', $content[$i]); // obtener nombre despues de ultima diagonal

                  if (ftp_get($conn_id, $local_file.$cuatro, $content[$i], FTP_BINARY)) { //Descargar archivos
                                 //echo "Se descargo el archivo!!\n revisa ". $local_file.$cuatro."\n";
                  } else {
                                 #echo "Ha ocurrido un error\n";
                  }
  }
  ftp_close($conn_id);
	}

	public function descargaImagenDestruccion(){
		$local_file = '/var/www/html/dashboard/imagenes_destruccion/';//Ruta local donde guardara archivos
	  $server_file = '/home/soldet/destruccion/';//Ruta de servidor ftp donde se encuentran archivos a copiar

	  //datos de conexion
	  $ftp_server='10.10.1.13';
	  $ftp_user_name='soldet';
	  $ftp_user_pass='soldet';

	  $conn_id = ftp_connect($ftp_server);
	  $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

	  $content = ftp_nlist($conn_id, $server_file); // lista de contenido de la ruta especificada
	//  var_dump($content);

	  for($i=0; $i<count($content);$i++){//recorre areglo de rutas
	                  list($cero, $uno, $dos, $tres, $cuatro) = preg_split('_[\\\\/]_', $content[$i]); // obtener nombre despues de ultima diagonal

	                  if (ftp_get($conn_id, $local_file.$cuatro, $content[$i], FTP_BINARY)) { //Descargar archivos
	                                 //echo "Se descargo el archivo!!\n revisa ". $local_file.$cuatro."\n";
	                  } else {
	                                 #echo "Ha ocurrido un error\n";
	                  }
	  }
	  ftp_close($conn_id);
	}


//TABLA Remates
	public function tablaImagenes(){
			$conn = conexion::conectar();
			$res_array = array();
			$sql = "Select t.iid_consecutivo, t.v_tipo_mercancia, t.n_valor_mercancia
							from ad_cxc_remates_mercancias t";
			 $stid = oci_parse($conn, $sql);
			 oci_execute($stid);
			 while(($row= oci_fetch_assoc($stid))!=false ){
				 $res_array[]=$row;
			 }
			 #echo $sql;
			 oci_free_statement($stid);
			 oci_close($conn);
			 return $res_array;
	}

	public function modalImagenes($iid_consecutivo){
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT v_foto FROM ad_cxc_remates_mercancias_img WHERE iid_remate = $iid_consecutivo";
		 $stid = oci_parse($conn, $sql);
		 oci_execute($stid);

		 while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
			 		$ruta_imagen = $row["V_FOTO"];
		 			echo "<img src='".$ruta_imagen."' />";
		 }

	}

}
?>
