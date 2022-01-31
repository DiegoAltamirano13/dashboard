<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2020
* Developer: Diego Altamirano Suarez
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
$iidconsecutivo = $_GET['iidconsecutivo'];
$iidcliente = $_GET['cliente'];
$plaza = $_GET['plaza'];
$conn = conexion::conectar();
$res_array = array();
$sql = "SELECT URL_IMAGEN FROM OP_IN_IMAGENES WHERE ID_SOLICITUD = $iidconsecutivo AND IID_NUM_CLIENTE = $iidcliente AND IID_PLAZA = $plaza";
#echo $sql;
 $stid = oci_parse($conn, $sql);
 oci_execute($stid);
 echo "<div id ='myCarousel' class='carousel slide' data-ride='carousel'>";
 echo "<div class='carousel-inner'>";

 $cont_slide = 0;
 while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
   $imagen_ur = "https://argoevidencias.dnsalias.org/".substr($row['URL_IMAGEN'], 21); //http://187.141.70.71/
   $active = "";
   if ($cont_slide == 0) {
     $active = "active";
   }
     echo "<div class='item $active' align='center'>";
     //echo "<img class='d-block w-100'height='450' width='400' alt='' src='".$row['URL_IMAGEN']."'/>";
     echo "<img class='d-block w-100'height='450' width='400' alt='' src='".$imagen_ur."'/>";
     echo "</div/>";
    $cont_slide++;

 }
 echo "</div>";
 echo "<a class='left carousel-control' href='#myCarousel' data-slide='prev'>";
    echo "<span class='glyphicon glyphicon-chevron-left'></span>";
    echo "<span class='sr-only'>Previous</span>";
  echo "</a>";
  echo "<a class='right carousel-control' href='#myCarousel' data-slide='next'>";
    echo "<span class='glyphicon glyphicon-chevron-right'></span>";
    echo "<span class='sr-only'>Next</span>";
  echo "</a>";
 echo "</div>";

?>
