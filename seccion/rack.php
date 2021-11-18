<?php
include_once '../class/Dispositivo.php';

if ($tablet_browser > 0) {
// Si es tablet has lo que necesites
   include_once 'rackT.php';
}
else if ($mobile_browser > 0) {
// Si es dispositivo mobil has lo que necesites
   include_once 'rackM.php';
}
else {
// Si es ordenador de escritorio has lo que necesites
   //include_once 'rackD.php';
	include_once 'rackD.php';
}