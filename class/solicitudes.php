<?php

  $charset 	= 'UTF8';
	#$con = oci_connect("exodo", "exodo2019", "10.10.1.205/ORACBA", $charset);
  include_once '../conOra.php';
  #$con = oci_connect("exodo", "exodo2019", "10.10.1.205:1526/ORACBA", $charset);
    $con = conexion::conectar();

$nombre_plaza = $_GET["nombreplaza"];

//$fecha_ini = $_GET["fecha_ini"];
//$fecha_fin = $_GET["fecha_fin"];

  $consulta_plaza = oci_parse($con, "SELECT IID_PLAZA FROM PLAZA WHERE PLAZA.V_RAZON_SOCIAL LIKE '%".$nombre_plaza."%' AND PLAZA.I_EMPRESA_PADRE = 1");
        //oci_bind_by_name($consulta_plaza, ":nombre_plaza", $nombre_plaza );
        oci_execute($consulta_plaza);
while ($row = oci_fetch_array($consulta_plaza, OCI_ASSOC)) {
   $iid_plaza =  $row["IID_PLAZA"];
   #echo $iid_plaza;
}

$dl = "";
switch($iid_plaza){
            case 3: break;
            case 4: $dl = "@DLMEX"; break;
            case 5: break;
            case 6: break;
            case 7: $dl = "@DLPUE"; break;
            case 8: $dl = " "; break;
            case 17: $dl = "@DLR06"; break;
            case 18: break;
            default:break;
        }

$consulta = "SELECT OP.ID_SOLICITUD AS SOLICITUD, OP.ID_PLAZA, OP.S_TIPO AS TIPO, OP.V_PEDIMENTO_FACTURA AS FACTURA, OP.V_CONTENEDOR AS CONTENEDOR, OP.IID_REGIMEN AS REGIMEN
                        FROM OP_IN_SOLICITUD_CARGA_DESCARGA OP
                        WHERE (OP.N_VIRTUAL IS NULL OR OP.N_VIRTUAL = 0)
                              AND OP.ID_TIPO  IN (1, 2)
                              AND OP.IID_REGIMEN = 2
                              AND OP.ID_PLAZA = $iid_plaza
                              AND TO_DATE(OP.D_FEC_LLEGADA_REAL, 'DD/MM/YYYY') = TO_DATE(SYSDATE, 'DD/MM/YYYY')
                        UNION
                        SELECT OP.ID_SOLICITUD AS SOLICITUD, OP.ID_PLAZA, OP.S_TIPO AS TIPO, OP.V_PEDIMENTO_FACTURA AS FACTURA, OP.V_CONTENEDOR AS CONTENEDOR, OP.IID_REGIMEN AS REGIMEN
                        FROM OP_IN_SOLICITUD_CARGA_DESCARGA".$dl." OP
                        WHERE (OP.N_VIRTUAL IS NULL OR OP.N_VIRTUAL = 0)
                              AND OP.ID_TIPO IN (1, 2)
                              AND OP.IID_REGIMEN = 1
                              AND OP.ID_PLAZA = $iid_plaza
                              AND TO_DATE(OP.D_FEC_LLEGADA_REAL, 'DD/MM/YYYY') = TO_DATE(SYSDATE, 'DD/MM/YYYY')";

       $sql = oci_parse($con, $consulta);
       //oci_bind_by_name($sql, ":iid_plaza", $iid_plaza );
       //oci_bind_by_name($sql, ":dl", $dl );

        oci_execute($sql);
#echo $consulta;

        //echo $iid_plaza. "</br>". $iid_cliente;

//  $n_rows = oci_fetch_all($sql, $res);
//  echo "$n_rows rows fetched </br>";

   while (($row = oci_fetch_object($sql))) {
     //echo $row->NID_FOLIO."</BR>";
     $test[] = $row;
     //json_encode($response);

   }
   //var_dump($test);
   $response = array("usuario"=>$test);
   echo json_encode($response);

 ?>
