<?php
include_once '../class/Perfil.php';
$insObj = new Perfil();
?>
<form method="post" >
  <textarea name="data"></textarea>
  <button type="submit"></button>
</form>

<?php
if( isset( $_POST["data"] ) ){
  $resClass =  $insObj->test($_POST["data"]);

  print_r($resClass);
}
?>