<?php
ini_set('display_errors', false);

if( $_SERVER['REQUEST_METHOD'] <> 'POST')
{ 
  header('Location: ../seccion/index.php');
}
$numero = $_POST["numero"]; 
$almacen = $_POST['almacen'] ;

include_once '../class/Cartas_cupo.php';
$obj_ce_cartas_cupo = new Cartascupo();
$bit_cc= $obj_ce_cartas_cupo->bit_cc($numero,$almacen,true);

if ( $numero == true && $almacen == true){
  echo '<table class="table compact table-striped table-bordered">
         <thead>
           <tr>
            <th>EVENTO</th>
            <th>FEC. EVENTO</th>
            <th>ENV.</th>
            <th>FIRMA ACUSE</th>
            <th>COMENTARIO</th>
            <th>TIPO</th>
           </tr>
         </thead>
         <tbody>';
    for ($i=0; $i <count($bit_cc) ; $i++) {
    echo '<tr>
            <td>'.$bit_cc[$i]["EVENTO"].'</td>
            <td>'.$bit_cc[$i]["FECHA_EVENTO"].'</td>
            <td>'.$bit_cc[$i]["STATUS_ENVIADO"].'</td>
            <td>'.$bit_cc[$i]["FIRMA_ACUSE"].'</td>
            <td>'.$bit_cc[$i]["COMENTARIO"].'</td>
            <td>'.$bit_cc[$i]["TIPO"].'</td>
          </tr>';
    }
    echo '</tbody>';
  echo '</table>';
}
	
?> 

 

 