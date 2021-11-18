<?php
ini_set('display_errors', false);

if( $_SERVER['REQUEST_METHOD'] <> 'POST')
{ 
  header('Location: ../seccion/index.php');
}
$numero = $_POST['numero'] ;
$mensaje = $_POST['mensaje']; 

if ($_POST['numero'] == true && ctype_digit($numero) && strlen($numero) == 10 )
{
	$envio =  shell_exec ('sudo yowsup-cli demos -l 5212881186646:waa1jpnHfenLIXH632o31kE68B8= -s 521'.$numero.' "'.$mensaje.'"');
	//echo "<pre>$envio</pre>";	 
	$info_envio = $envio;
	$mensaje_info = strpos($info_envio, "Yowsdown");

	if ($mensaje_info !== false) {
	     echo '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="ion-happy-outline"></i> Error al enviar mensaje!</h4>Por el momento esta opción solo funciona en el servidor de pruebas</div>';
	} else {
	     echo '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="ion-sad-outline"></i> Error al enviar mensaje!</h4>Por el momento esta opción solo funciona en el servidor de pruebas</b></div>';
	}
}else
{
	echo '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="ion-sad-outline"></i> Error al enviar mensaje!</h4>Por el momento esta opción solo funciona en el servidor de pruebas</div>';
}

 
//is_null($numero)
?> 

