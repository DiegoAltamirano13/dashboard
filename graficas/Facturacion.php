<?php

include_once '../libs/conOra.php';
include_once 'ObjFacturacion.php';
include_once 'ArrayFacturacion.php';

class Facturacion {
	
	/**
	 * funcion que retorna una cadena de anio definida por un rango de anio inicial y final
	 * @param unknown $anioIni
	 * @param unknown $anioFin
	 * @return string
	 */
	public function getMes($mesN){
		$mes = "";
		
		switch ($mesN){
			case '01':
				$mes = "Enero";
				break;
			case '02':
				$mes = "Febrero";
				break;
			case '03':
				$mes = "Marzo";
				break;
			case '04':
				$mes = "Abril";
				break;
			case '05':
				$mes = "Mayo";
				break;
			case '06':
				$mes = "Junio";
				break;
			case '07':
				$mes = "Julio";
				break;
			case '08':
				$mes = "Agosto";
				break;
			case '09':
				$mes = "Septiembre";
				break;
			case '10':
				$mes = "Octubre";
				break;
			case '11':
				$mes = "Noviembre";
				break;
			case '12':
				$mes = "Diciembre";
				break;
		}
		

		return $mes;
	}
	
	/**
	 * funcion que retorna una cadena de anio definida por un rango de anio inicial y final
	 * @param unknown $anioIni
	 * @param unknown $anioFin
	 * @return string
	 */	
	public function getAnios($anioIni, $anioFin, $mesActual){
		
		$anios="";
		
		if ($anioIni >= $anioFin)
			return "'".$anioIni."'";
		
		for($i=$anioIni; $i<$anioFin; $i++){
			$anios = $anios."'".$i."', ";
		}
		
		if ($mesActual == 0)
			$anios = $anios." '2087'";
		else 
			$anios = $anios." '".$anioFin."'";
		
		return $anios;
	}
	
	/**
	 * funcion que retorna una cadena de numeros entre 1 a 12 separados por comas delimitado por el mes actual
	 * @param unknown $mesActual
	 * @param unknown $tipo
	 * @return string
	 */
	public function getMeses($mesActual, $tipo, $grafica){
		
		$meses = " ";
		
		if ($grafica == 1){
		
			if($mesActual == 0 and $tipo == 0){
				return $meses = " '13' ";
			}
			
			if($mesActual == 0 and $tipo == 1){
				return $meses = " '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ";
			}
			
			if($tipo == 0){
				for($i=1; $i<=$mesActual; $i++){
					$meses = $meses."'".str_pad($i, 2, "0", STR_PAD_LEFT)."', ";
				}
			}else{
				for($i=$mesActual; $i<=12; $i++){
					$meses = $meses."'".str_pad($i, 2, "0", STR_PAD_LEFT)."', ";
				}			
			}
			
			return $meses = $meses." '13' ";
		
		}
		
		if ($grafica == 2){
		
			if($mesActual == 0 and ($tipo == 0 or $tipo == 1)){
				return $meses = " '12' ";
			}
				
			return $meses = "'".str_pad($mesActual, 2, "0", STR_PAD_LEFT)."'";
		}
		
		if ($grafica == 3){
		
			if($mesActual == 0 and ($tipo == 0 or $tipo == 1)){
				return $meses = " '12' ";
			}
			
			return $meses = "'".str_pad($mesActual, 2, "0", STR_PAD_LEFT)."'";
		}
		
		return $meses;
		
	}

	/**
	 * funcion que retorna un array de objetos facturacion para ser utilizado en la grafica de mes - mes
	 * @param unknown $anio
	 * @param unknown $meses
	 * @param unknown $plazas
	 */
	public function getGraficaMesAnio($anio, $meses, $plazas){
		$conn 	= conexion::conectar();
		$sql = 	" select "
				." f.iid_plaza, "
				." to_char(t.d_fecha_movto,'mm') as mes, "
				." to_char(t.d_fecha_movto,'yyyy') as anio, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as cargos, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as abonos, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) - decode(sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))), null, 0, sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2)))) as total "
				." from "
				." ad_cxc_movtos t, "
				." ad_fa_factura f "
				." where "
				." t.iid_plaza                               = f.iid_plaza                                                                       and "
				." t.iid_folio                               = f.iid_folio                                                                       and "
				." f.status                                  = 7                                                                                 and "
				." t.n_status                                = 2                                                                                 and "
				." t.n_tipo_movto                            in (1,3,4)                                                                          and "
				." to_char(t.d_fecha_movto,'yyyy')           in (".$anio.")                                                                 	 and "
				." f.iid_plaza                               in (".$plazas.")                                                                    and "
				." to_char(t.d_fecha_movto, 'mm')            in (".$meses.")                             										 and "
				." to_number(to_char(t.d_fecha_movto, 'dd')) <= 31 "
				." group by f.iid_plaza, to_char(t.d_fecha_movto,'mm'), to_char(t.d_fecha_movto,'yyyy') "
				." order by f.iid_plaza, to_char(t.d_fecha_movto,'mm'), to_char(t.d_fecha_movto,'yyyy') ";

				
		$stid_GMA = oci_parse($conn, $sql);
		oci_execute($stid_GMA);
		
		 
		$contCordoba = 0;
		$arrayCordoba = null;
		while(($row = oci_fetch_array($stid_GMA, OCI_BOTH)) != false ){
			if($row[0]==3){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[3]);				
				$arrayCordoba[$contCordoba++] = $objFac;
			}
		}
		
		$arrayFacturacion = new ArrayFacturacion();
		$arrayFacturacion->setArrayCordoba($arrayCordoba);
		
		return $arrayFacturacion;
		
				
	}				
	
	/**
	 * funcion que retorna un array de objetos facturacion para ser utilizado en la grafica de area mes - anio
	 * @param unknown $anio
	 * @param unknown $meses
	 * @param unknown $plazas
	 */
	public function getGraficaMesMes($anio, $anioOld, $meses, $mesesOld, $plazas){
		$conn 	= conexion::conectar();
		$sql = 	" select " 
				." f.iid_plaza, "
				." to_char(t.d_fecha_movto,'mm') as mes, "
				." to_char(t.d_fecha_movto,'yyyy') as anio, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as cargos, " 
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as abonos, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) - decode(sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))), null, 0, sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2)))) as total "
				." from "
				." ad_cxc_movtos t, " 
				." ad_fa_factura f "
				." where "
				." t.iid_plaza                               = f.iid_plaza                                                                       and "
				." t.iid_folio                               = f.iid_folio                                                                       and "
				." f.status                                  = 7                                                                                 and "
				." t.n_status                                = 2                                                                                 and "
				." t.n_tipo_movto                            in (1,3,4)                                                                          and "
				." to_char(t.d_fecha_movto,'yyyy')           in (".$anioOld.")                                                                 	 and "
				." f.iid_plaza                               in (".$plazas.")                                                                    and "
				." to_char(t.d_fecha_movto, 'mm')            in (".$mesesOld.")                             										 and "
				." to_number(to_char(t.d_fecha_movto, 'dd')) <= 31 " 
				." group by f.iid_plaza, to_char(t.d_fecha_movto,'mm'), to_char(t.d_fecha_movto,'yyyy') "
				." order by f.iid_plaza, to_char(t.d_fecha_movto,'mm'), to_char(t.d_fecha_movto,'yyyy') ";

		
		$stid_GMA = oci_parse($conn, $sql);
		oci_execute($stid_GMA);
		
		 
		$contCordoba 		= 0;
		$arrayCordoba 		= null;
		$contMexico 		= 0;
		$arrayMexico 		= null;
		$contGolgo 			= 0;
		$arrayGolfo 		= null;
		$contPeninsula 		= 0;
		$arrayPeninsula  	= null;
		$contPuebla 		= 0;
		$arrayPuebla  		= null;
		$contBajio 			= 0;
		$arrayBajio  		= null;
		$contOccidente 		= 0;
		$arrayOccidente  	= null;
		$contNoreste 		= 0;
		$arrayNoreste	  	= null;
		$contLeon 			= 0;
		$arrayLeon	  		= null;
		
		while(($row = oci_fetch_array($stid_GMA, OCI_BOTH)) != false ){
			if($row[0] == 3){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);				
				$arrayCordoba[$contCordoba++] = $objFac;
			}
			if($row[0] == 4){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayMexico[$contMexico++] = $objFac;
			}
			if($row[0] == 5){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayGolfo[$contGolgo++] = $objFac;
			}
			if($row[0] == 6){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayPeninsula[$contPeninsula++] = $objFac;
			}
			if($row[0] == 7){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayPuebla[$contPuebla++] = $objFac;
			}
			if($row[0] == 8){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayBajio[$contBajio++] = $objFac;
			}
			if($row[0] == 17){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayOccidente[$contOccidente++] = $objFac;
			}
			if($row[0] == 18){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayNoreste[$contNoreste++] = $objFac;
			}
			if($row[0] == 23){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayLeon[$contLeon++] = $objFac;
			}
		}
		
		$sql = 	" select "
				." f.iid_plaza, "
				." to_char(t.d_fecha_movto,'mm') as mes, "
				." to_char(t.d_fecha_movto,'yyyy') as anio, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as cargos, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as abonos, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) - decode(sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))), null, 0, sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2)))) as total "
				." from "
				." ad_cxc_movtos t, "
				." ad_fa_factura f "
				." where "
				." t.iid_plaza                               = f.iid_plaza                                                                       and "
				." t.iid_folio                               = f.iid_folio                                                                       and "
				." f.status                                  = 7                                                                                 and "
				." t.n_status                                = 2                                                                                 and "
				." t.n_tipo_movto                            in (1,3,4)                                                                          and "
				." to_char(t.d_fecha_movto,'yyyy')           in (".$anio.")                                                                 	 and "
				." f.iid_plaza                               in (".$plazas.")                                                                    and "
				." to_char(t.d_fecha_movto, 'mm')            in (".$meses.")                             										 and "
				." to_number(to_char(t.d_fecha_movto, 'dd')) <= 31 "
				." group by f.iid_plaza, to_char(t.d_fecha_movto,'mm'), to_char(t.d_fecha_movto,'yyyy') "
				." order by f.iid_plaza, to_char(t.d_fecha_movto,'mm'), to_char(t.d_fecha_movto,'yyyy') ";

		$stid_GMA = oci_parse($conn, $sql);
		oci_execute($stid_GMA);
		
		while(($row = oci_fetch_array($stid_GMA, OCI_BOTH)) != false ){
			if($row[0] == 3){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayCordoba[$contCordoba++] = $objFac;
			}
			if($row[0] == 4){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayMexico[$contMexico++] = $objFac;
			}
			if($row[0] == 5){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayGolfo[$contGolgo++] = $objFac;
			}
			if($row[0] == 6){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayPeninsula[$contPeninsula++] = $objFac;
			}
			if($row[0] == 7){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayPuebla[$contPuebla++] = $objFac;
			}
			if($row[0] == 8){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayBajio[$contBajio++] = $objFac;
			}
			if($row[0] == 17){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayOccidente[$contOccidente++] = $objFac;
			}
			if($row[0] == 18){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayNoreste[$contNoreste++] = $objFac;
			}
			if($row[0] == 23){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setMes($row[1]);
				$objFac->setAnio($row[2]);
				$objFac->setTotal($row[5]);
				$arrayLeon[$contLeon++] = $objFac;
			}
		}
		
		$arrayFacturacion = new ArrayFacturacion();
		$arrayFacturacion->setArrayCordoba($arrayCordoba);
		$arrayFacturacion->setArrayMexico($arrayMexico);
		$arrayFacturacion->setArrayGolfo($arrayGolfo);
		$arrayFacturacion->setArrayPeninsula($arrayPeninsula);
		$arrayFacturacion->setArrayPuebla($arrayPuebla);
		$arrayFacturacion->setArrayBajio($arrayBajio);
		$arrayFacturacion->setArrayOccidente($arrayOccidente);
		$arrayFacturacion->setArrayNoreste($arrayNoreste);
		$arrayFacturacion->setArrayLeon($arrayLeon);
		
		return $arrayFacturacion;
		
				
	}
	

	/**
	 * funcion que retorna un array de objetos facturacion para ser utilizado en la grafica de area acumulada - anio
	 * @param unknown $anio
	 * @param unknown $meses
	 * @param unknown $plazas
	 */
	public function getGraficaAcumulado($anios, $mes, $plazas){
		$conn 	= conexion::conectar();
		$sql = 	" select "
				." f.iid_plaza, "
				." to_char(t.d_fecha_movto,'yyyy') as anio, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as cargos, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as abonos, "
			    ." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) - decode(sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))), null, 0, sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2)))) as total "
				." from "
				." ad_cxc_movtos t, "
				." ad_fa_factura f "
				." where "
				." t.iid_plaza                               = f.iid_plaza                                                                       and "
				." t.iid_folio                               = f.iid_folio                                                                       and "
				." f.status                                  = 7                                                                                 and "
				." t.n_status                                = 2                                                                                 and "
				." t.n_tipo_movto                            in (1,3,4)                                                                          and "
				." to_char(t.d_fecha_movto,'yyyy')           in (".$anios.")                                                                 	 and "
				." f.iid_plaza                               in (".$plazas.")                                                                    and "
				." to_number(to_char(t.d_fecha_movto, 'mm')) <= (".$mes.")                             									 		 and "
				." to_number(to_char(t.d_fecha_movto, 'dd')) <= 31 "
				." group by f.iid_plaza, to_char(t.d_fecha_movto,'yyyy') "
				." order by f.iid_plaza, to_char(t.d_fecha_movto,'yyyy') ";
				
		$stid_GMA = oci_parse($conn, $sql);
		oci_execute($stid_GMA);
		
		$contCordoba 		= 0;
		$arrayCordoba 		= null;
		$contMexico 		= 0;
		$arrayMexico 		= null;
		$contGolgo 			= 0;
		$arrayGolfo 		= null;
		$contPeninsula 		= 0;
		$arrayPeninsula  	= null;
		$contPuebla 		= 0;
		$arrayPuebla  		= null;
		$contBajio 			= 0;
		$arrayBajio  		= null;
		$contOccidente 		= 0;
		$arrayOccidente  	= null;
		$contNoreste 		= 0;
		$arrayNoreste	  	= null;
		$contLeon 			= 0;
		$arrayLeon	  		= null;
		
		while(($row = oci_fetch_array($stid_GMA, OCI_BOTH)) != false ){
			if($row[0] == 3){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayCordoba[$contCordoba++] = $objFac;
			}
			if($row[0] == 4){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayMexico[$contMexico++] = $objFac;
			}
			if($row[0] == 5){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayGolfo[$contGolgo++] = $objFac;
			}
			if($row[0] == 6){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayPeninsula[$contPeninsula++] = $objFac;
			}
			if($row[0] == 7){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayPuebla[$contPuebla++] = $objFac;
			}
			if($row[0] == 8){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayBajio[$contBajio++] = $objFac;
			}
			if($row[0] == 17){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayOccidente[$contOccidente++] = $objFac;
			}
			if($row[0] == 18){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayNoreste[$contNoreste++] = $objFac;
			}
			if($row[0] == 23){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayLeon[$contLeon++] = $objFac;
			}
		}
	

		$arrayFacturacion = new ArrayFacturacion();
		$arrayFacturacion->setArrayCordoba($arrayCordoba);
		$arrayFacturacion->setArrayMexico($arrayMexico);
		$arrayFacturacion->setArrayGolfo($arrayGolfo);
		$arrayFacturacion->setArrayPeninsula($arrayPeninsula);
		$arrayFacturacion->setArrayPuebla($arrayPuebla);
		$arrayFacturacion->setArrayBajio($arrayBajio);
		$arrayFacturacion->setArrayOccidente($arrayOccidente);
		$arrayFacturacion->setArrayNoreste($arrayNoreste);
		$arrayFacturacion->setArrayLeon($arrayLeon);
		
		return $arrayFacturacion;
				
				
	}
	
	/**
	 * funcion que retorna un array de objetos facturacion para ser utilizado en la grafica de area por mes - anio
	 * @param unknown $anio
	 * @param unknown $meses
	 * @param unknown $plazas
	 */
	public function getGraficaPorMes($anios, $mes, $plazas){
		$conn 	= conexion::conectar();
		$sql = 	" select "
				." f.iid_plaza, "
				." to_char(t.d_fecha_movto,'yyyy') as anio, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as cargos, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) as abonos, "
				." sum(decode(f.iid_moneda, 1, round(t.n_monto_cargo/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ),2), 2, round(t.c_tipo_cambio * t.n_monto_cargo / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))) - decode(sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2))), null, 0, sum(decode(f.iid_moneda, 1, round(t.n_monto_abono/(1+ decode(f.iva_tasa, .01, 0, f.iva_tasa) ) ,2), 2, round(t.c_tipo_cambio * t.n_monto_abono / (1+ decode(f.iva_tasa, .01, 0, f.iva_tasa)), 2)))) as total "
				." from "
				." ad_cxc_movtos t, "
				." ad_fa_factura f "
				." where "
				." t.iid_plaza                               = f.iid_plaza                                                                       and "
				." t.iid_folio                               = f.iid_folio                                                                       and "
				." f.status                                  = 7                                                                                 and "
				." t.n_status                                = 2                                                                                 and "
				." t.n_tipo_movto                            in (1,3,4)                                                                          and "
				." to_char(t.d_fecha_movto,'yyyy')           in (".$anios.")                                                                 	 and "
				." f.iid_plaza                               in (".$plazas.")                                                                    and "
				." to_number(to_char(t.d_fecha_movto, 'mm')) = (".$mes.")                             									 		 and "
				." to_number(to_char(t.d_fecha_movto, 'dd')) <= 31 "
				." group by f.iid_plaza, to_char(t.d_fecha_movto,'yyyy') "
				." order by f.iid_plaza, to_char(t.d_fecha_movto,'yyyy') ";
		
		$stid_GMA = oci_parse($conn, $sql);
		oci_execute($stid_GMA);
		
		$contCordoba 		= 0;
		$arrayCordoba 		= null;
		$contMexico 		= 0;
		$arrayMexico 		= null;
		$contGolgo 			= 0;
		$arrayGolfo 		= null;
		$contPeninsula 		= 0;
		$arrayPeninsula  	= null;
		$contPuebla 		= 0;
		$arrayPuebla  		= null;
		$contBajio 			= 0;
		$arrayBajio  		= null;
		$contOccidente 		= 0;
		$arrayOccidente  	= null;
		$contNoreste 		= 0;
		$arrayNoreste	  	= null;
		$contLeon 			= 0;
		$arrayLeon	  		= null;
		
		while(($row = oci_fetch_array($stid_GMA, OCI_BOTH)) != false ){
			if($row[0] == 3){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayCordoba[$contCordoba++] = $objFac;
			}
			if($row[0] == 4){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayMexico[$contMexico++] = $objFac;
			}
			if($row[0] == 5){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayGolfo[$contGolgo++] = $objFac;
			}
			if($row[0] == 6){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayPeninsula[$contPeninsula++] = $objFac;
			}
			if($row[0] == 7){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayPuebla[$contPuebla++] = $objFac;
			}
			if($row[0] == 8){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayBajio[$contBajio++] = $objFac;
			}
			if($row[0] == 17){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayOccidente[$contOccidente++] = $objFac;
			}
			if($row[0] == 18){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayNoreste[$contNoreste++] = $objFac;
			}
			if($row[0] == 23){
				$objFac=new OjbFacturacion();
				$objFac->setPlaza($row[0]);
				$objFac->setAnio($row[1]);
				$objFac->setTotal($row[4]);
				$arrayLeon[$contLeon++] = $objFac;
			}
		}
		
		
		$arrayFacturacion = new ArrayFacturacion();
		$arrayFacturacion->setArrayCordoba($arrayCordoba);
		$arrayFacturacion->setArrayMexico($arrayMexico);
		$arrayFacturacion->setArrayGolfo($arrayGolfo);
		$arrayFacturacion->setArrayPeninsula($arrayPeninsula);
		$arrayFacturacion->setArrayPuebla($arrayPuebla);
		$arrayFacturacion->setArrayBajio($arrayBajio);
		$arrayFacturacion->setArrayOccidente($arrayOccidente);
		$arrayFacturacion->setArrayNoreste($arrayNoreste);
		$arrayFacturacion->setArrayLeon($arrayLeon);
		
		return $arrayFacturacion;
		
		
		}
}

?>