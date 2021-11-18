<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
$iidconsecutivo = $_GET['iidconsecutivo'];
$conn = conexion::conectar();
$res_array = array();
$sql = "SELECT v_foto FROM ad_cxc_remates_mercancias_img WHERE iid_remate = $iidconsecutivo";;
 $stid = oci_parse($conn, $sql);
 oci_execute($stid);
 echo "<div id ='myCarousel' class='carousel slide' data-ride='carousel'>";
 echo "<div class='carousel-inner'>";
 $cont_slide = 0;
 while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
   $active = "";
   if ($cont_slide == 0) {
     $active = "active";
   }
   $valor = substr($row['V_FOTO'], -3, -1);
   $con_url = substr($row['V_FOTO'], 0, 21);
   #echo $con_url;
   if (strpos($con_url, '10.10.1.13')) {
     echo "<div class='item $active'>";
     if ($valor == 'JP') {
       echo "<img class='d-block w-100' src='../imagenes_remates/".substr($row['V_FOTO'], 21, -3)."JPG'/>";
     }
     else {
       echo "<img class='d-block w-100' src='../imagenes_remates/".substr($row['V_FOTO'], 21, -3)."jpg'/>";
     }
     echo "</div>";
   }else {
     echo "<div class='item $active'>";
     if ($valor == 'JP') {
       echo "<img class='d-block w-100' src='../imagenes_remates/".substr($row['V_FOTO'], 0, -3)."JPG'/>";
     }
     else {
       echo "<img class='d-block w-100' src='../imagenes_remates/".substr($row['V_FOTO'], 0, -3)."jpg'/>";
     }
     echo "</div>";
   }


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
