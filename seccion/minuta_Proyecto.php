<?php
require('PDF_MD_TABLE.php');
include_once '../class/Minuta_Proyecto.php';
$numero_proyecto = $_GET['proyecto'];
//echo $numero_proyecto;
$obj_class = new Minuta_Proyecto();
$minuta_Gral = $obj_class->info_Gral($numero_proyecto);
function GenerateWord()
{
    //Get a random word
    $nb=rand(3,10);
    $w='';
    for($i=1;$i<=$nb;$i++)
        $w.=chr(rand(ord('a'),ord('z')));
    return $w;
}

function GenerateSentence()
{
    //Get a random sentence
    $nb=rand(1,10);
    $s='';
    for($i=1;$i<=$nb;$i++)
        $s.=GenerateWord().' ';
    return substr($s,0,-1);
}


$pdf=new PDF_MC_Table();
$pdf->AddPage();
$pdf -> SetFont('Arial','', 10);


$mizq = 10;
$ancho1 = 130;
$ancho2 = 50;
$alto = 5;
$pdf -> SetX($mizq,+$ancho1);
$pdf -> Cell($ancho2,$alto, 'PROYECTO/ASUNTO: ' ,0,0);
for ($i=0; $i <count($minuta_Gral) ; $i++) {
  $pdf -> MultiCell($ancho1, $alto, utf8_decode($minuta_Gral[$i]["DESCRIPCION_ACTIVIDAD"]), 0);
}

$pdf -> Ln(10);
$pdf->SetFont('Arial','',8);
//Table with 20 rows and 4 columns
$pdf->SetWidths(array(60,90,40));
srand(microtime()*1000000);
//tabla con valores reales
$pdf -> SetFillColor(232,232,232);
$pdf->Cell(60,6,'INVOLUCRADO',1,0,'C',1);
$pdf->Cell(90,6,'ACTIVIDAD',1,0,'C',1);
$pdf->Cell(40,6,'FECHA ENTREGA',1,0,'C',1);
$pdf -> Ln(6);
$minuta_jefe = $obj_class->info_Designados($numero_proyecto);
for($i=0;$i<count($minuta_jefe);$i++)
    $pdf->Row(array( $minuta_jefe[$i]["NOMBRE"], $minuta_jefe[$i]["ACTIVIDAD"], $minuta_jefe[$i]["FECHA"]));

$pdf->Ln(10);
$obtengoxlinea = $pdf->GetX();
$obtengoylinea =$pdf->GetY()+3;
$pdf->SetY($obtengoylinea);
$pdf->Cell(90,6,'FECHA PARA ENTREGAR ACTIVIDADES',0,0,'C',0);
for ($i=0; $i <count($minuta_Gral) ; $i++) {
  $fecha_fin = $minuta_Gral[$i]["FECHA_FIN"];
}
$pdf->Cell(30,6,$fecha_fin,0,0,'C',0);
$pdf->Ln(10);

$obtengoxlinea = $pdf->GetX()+60;
$obtengoylinea =$pdf->GetY()+20;
$pdf->Line($obtengoxlinea, $obtengoylinea,$obtengoxlinea+80, $obtengoylinea);
$val_firmasy =$pdf->GetY()+20;
$pdf->SetY($val_firmasy) ;
$pdf->SetX($obtengoxlinea+5) ;
for ($i=0; $i <count($minuta_Gral) ; $i++) {
  $pdf->Cell(70,6,utf8_decode($minuta_Gral[$i]["NOMBRE"]),0,0,'C');
}
$pdf->Output();
?>
