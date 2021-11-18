<?php

class OjbFacturacion {
	
	private $plaza;
	private $mes;
	private $anio; 
	private $cargo;
	private $abono;
	private $total;
	
	public function getPlaza(){
		return $this->plaza;
	}
	
	public function getMes(){
		return $this->mes;
	}	
	
	public function getAnio(){
		return $this->anio;
	}
	
	public function getCargo(){
		return $this->cargo;
	}	
	
	public function getAbono(){
		return $this->abono;
	}	
	
	public function getTotal(){
		return $this->total;
	}	

	public function setPlaza($plaza){
		$this->plaza = $plaza;
	}
	
	public function setMes($mes){
		$this->mes = $mes;
	}
	
	public function setAnio($anio){
		$this->anio = $anio;
	}	
	
	public function setCargo($cargo){
		$this->cargo = $cargo;
	}
	
	public function setAbono($abono){
		$this->abono = $abono;
	}
	
	public function setTotal($total){
		$this->total = $total;
	}		
	
}

?>