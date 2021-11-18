<?php
ini_set('display_errors', false);

if( $_SERVER['REQUEST_METHOD'] <> 'POST')
{ 
  //header('Location: ../seccion/index.php');
}
$saldo_cliente_plaza = $_POST['plaza'] ;
$and_saldo_dias = $_POST['saldo_dias'] ;

$cliente = $_POST['cliente'] ;

include_once '../class/Saldo_cliente.php';
$ins_obj_saldos_clientes = new Saldos_clientes($and_saldo_dias,$saldo_cliente_plaza);
$tabla_saldos_clientes_resumen = $ins_obj_saldos_clientes->tabla_saldos_clientes(false,true,true,$cliente);
//tabla_saldos_clientes($saldo_cliente_plaza,$importe_filtro_resumen,$group,$cliente)

switch ($saldo_dias) {
        case 1:
          $titulo_dias = "DE 1 A 30 DÍAS";
          break;
        case 2:
          $titulo_dias = "DE 31 A 60 DÍAS";
          break;
        case 3:
          $titulo_dias = "DE 61 A 90 DÍAS";
          break;
        default:
          $titulo_dias = "MAS DE 90 DÍAS";
          break;
      }


if ( $cliente == true){
	
	
	echo '<table class="table compact table-striped table-bordered">
			<thead>
			<tr>
				<th>PLAZA</th>
				<th>SALDO</th>
			</tr>
			</thead>
			<tbody>';
		for ($i=0; $i < count($tabla_saldos_clientes_resumen) ; $i++) { 	
		echo '<tr>';
			echo '<td>('.$tabla_saldos_clientes_resumen[$i]["ID_PLAZA"].')'.$tabla_saldos_clientes_resumen[$i]["PLAZA"].'</td>';
			if (is_null($tabla_saldos_clientes_resumen[$i]["MONTO"])){
              echo '<td>$'.number_format($tabla_saldos_clientes_resumen[$i]["SALDO"],2).'
              <a class="fancybox fancybox.iframe" href="saldo_cliente_det.php?plaza='.$tabla_saldos_clientes_resumen[$i]["PLAZA"].'&cliente='.$tabla_saldos_clientes_resumen[$i]["ID_CLIENTE"].'&filtro='.$and_saldo_dias.'"><sup><span class="label bg-green">Ver<i class="fa fa-fw fa-plus"></i></span></sup></a>
              </td>'; 
              $saldo_total[$i] = $tabla_saldos_clientes_resumen[$i]["SALDO"];
            }else{
              echo '<td>$'.number_format($tabla_saldos_clientes_resumen[$i]["MONTO"],2).'
              <a class="fancybox fancybox.iframe" href="saldo_cliente_det.php?plaza='.$tabla_saldos_clientes_resumen[$i]["PLAZA"].'&cliente='.$tabla_saldos_clientes_resumen[$i]["ID_CLIENTE"].'&filtro='.$and_saldo_dias.'"><sup><span class="label bg-green">Ver<i class="fa fa-fw fa-plus"></i></span></sup></a>
              </td>'; 
              $saldo_total[$i] = $tabla_saldos_clientes_resumen[$i]["MONTO"];
            }
		echo '</tr>';
		}
		echo '<tr>
				<th>SALDO TOTAL</th>
				<th>$'.number_format(array_sum($saldo_total),2).'</th>
			  </tr>';
	echo '</tbody>';
echo '</table>';

}else
{
	echo "error";
}
 
?> 

 

 