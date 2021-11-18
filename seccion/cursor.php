<?php
include_once '../class/Cursor.php';

$insObj = new Cursor();

$res = $insObj->graficaVenta();


for ($i=0; $i <count($res) ; $i++) { 
echo 'tu res es '.$res[$i]["MES"]."<br>" ;
}
