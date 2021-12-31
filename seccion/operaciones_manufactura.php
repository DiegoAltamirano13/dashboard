<?php
ini_set('display_errors', false);
// *********************************** OPERACIONES PARA CARGAS *********************************** //
/* ============ CARGAS EN PROCESO ============ */
//PLAZA CORDOBA
$total_cargas_proceso_cor = count($cargas_proceso_cor);
//PLAZA MEXICO
$total_cargas_proceso_mex = count($cargas_proceso_mex);
//PLAZA GOLFO
$total_cargas_proceso_gol = count($cargas_proceso_gol);
//PLAZA PENINSULA
$total_cargas_proceso_pen = count($cargas_proceso_pen);
//PLAZA PUEBLA
$total_cargas_proceso_pue = count($cargas_proceso_pue);
//PLAZA BAJIO
$total_cargas_proceso_baj = count($cargas_proceso_baj);
//PLAZA OCCIDENTE
$total_cargas_proceso_occ = count($cargas_proceso_occ);
//PLAZA NORESTE
$total_cargas_proceso_nor = count($cargas_proceso_nor);
//PLAZA LEON
$total_cargas_proceso_leo = count($cargas_proceso_leo);
/*TOTAL CARGAS EN PROCESO*/
$total_cargas_proceso =  $total_cargas_proceso_cor + $total_cargas_proceso_mex + $total_cargas_proceso_gol + $total_cargas_proceso_pen + $total_cargas_proceso_pue + $total_cargas_proceso_baj + $total_cargas_proceso_occ + $total_cargas_proceso_nor + $total_cargas_proceso_leo ;

/* ============ CARGAS FINALIZADAS ============ */
//PLAZA CORDOBA
$total_cargas_finalizadas_cor = count($cargas_finalizadas_cor);
//PLAZA MEXICO
$total_cargas_finalizadas_mex = count($cargas_finalizadas_mex);
//PLAZA GOLFO
$total_cargas_finalizadas_gol = count($cargas_finalizadas_gol);
//PLAZA PENINSULA
$total_cargas_finalizadas_pen = count($cargas_finalizadas_pen);
//PLAZA PUEBLA
$total_cargas_finalizadas_pue = count($cargas_finalizadas_pue);
//PLAZA BAJIO
$total_cargas_finalizadas_baj = count($cargas_finalizadas_baj);
//PLAZA OCCIDENTE
$total_cargas_finalizadas_occ = count($cargas_finalizadas_occ);
//PLAZA NORESTE
$total_cargas_finalizadas_nor = count($cargas_finalizadas_nor);
//PLAZA LEON
$total_cargas_finalizadas_leo = count($cargas_finalizadas_leo);
/*TOTAL CARGAS FINALIZADAS*/
$total_cargas_finalizadas =  $total_cargas_finalizadas_cor + $total_cargas_finalizadas_mex + $total_cargas_finalizadas_gol + $total_cargas_finalizadas_pen + $total_cargas_finalizadas_pue + $total_cargas_finalizadas_baj + $total_cargas_finalizadas_occ + $total_cargas_finalizadas_nor + $total_cargas_finalizadas_leo ;

/* ============ CARGAS FINALIZADAS DESFASADAS ============ */
//PLAZA CORDOBA
$total_cargas_finalizadas_des_cor = count($cargas_finalizadas_des_cor);
//PLAZA MEXICO
$total_cargas_finalizadas_des_mex = count($cargas_finalizadas_des_mex);
//PLAZA GOLFO
$total_cargas_finalizadas_des_gol = count($cargas_finalizadas_des_gol);
//PLAZA PENINSULA
$total_cargas_finalizadas_des_pen = count($cargas_finalizadas_des_pen);
//PLAZA PUEBLA
$total_cargas_finalizadas_des_pue = count($cargas_finalizadas_des_pue);
//PLAZA BAJIO
$total_cargas_finalizadas_des_baj = count($cargas_finalizadas_des_baj);
//PLAZA OCCIDENTE
$total_cargas_finalizadas_des_occ = count($cargas_finalizadas_des_occ);
//PLAZA NORESTE
$total_cargas_finalizadas_des_nor = count($cargas_finalizadas_des_nor);
//PLAZA LEON
$total_cargas_finalizadas_des_leo = count($cargas_finalizadas_des_leo);

$total_cargas_finalizadas_des = $total_cargas_finalizadas_des_cor + $total_cargas_finalizadas_des_mex + $total_cargas_finalizadas_des_gol + $total_cargas_finalizadas_des_pen + $total_cargas_finalizadas_des_pue + $total_cargas_finalizadas_des_baj + $total_cargas_finalizadas_des_occ + $total_cargas_finalizadas_des_nor + $total_cargas_finalizadas_des_leo ;

//PLAZA CORDOBA
$total_cargas_finalizadas_des_cor_ritm = count($cargas_finalizadas_des_cor_ritm);
//PLAZA MEXICO
$total_cargas_finalizadas_des_mex_ritm = count($cargas_finalizadas_des_mex_ritm);
//PLAZA GOLFO
$total_cargas_finalizadas_des_gol_ritm = count($cargas_finalizadas_des_gol_ritm);
//PLAZA PENINSULA
$total_cargas_finalizadas_des_pen_ritm = count($cargas_finalizadas_des_pen_ritm);
//PLAZA PUEBLA
$total_cargas_finalizadas_des_pue_ritm = count($cargas_finalizadas_des_pue_ritm);
//PLAZA BAJIO
$total_cargas_finalizadas_des_baj_ritm = count($cargas_finalizadas_des_baj_ritm);
//PLAZA OCCIDENTE
$total_cargas_finalizadas_des_occ_ritm = count($cargas_finalizadas_des_occ_ritm);
//PLAZA NORESTE
$total_cargas_finalizadas_des_nor_ritm = count($cargas_finalizadas_des_nor_ritm);
//PLAZA LEON
$total_cargas_finalizadas_des_leo_ritm = count($cargas_finalizadas_des_leo_ritm);

/*TOTAL CARGAS FINALIZADAS DESFASADAS*/
$total_cargas_finalizadas_des_ritm = $total_cargas_finalizadas_des_cor_ritm + $total_cargas_finalizadas_des_mex_ritm + $total_cargas_finalizadas_des_gol_ritm + $total_cargas_finalizadas_des_pen_ritm + $total_cargas_finalizadas_des_pue_ritm + $total_cargas_finalizadas_des_baj_ritm + $total_cargas_finalizadas_des_occ_ritm + $total_cargas_finalizadas_des_nor_ritm + $total_cargas_finalizadas_des_leo_ritm;

/* ============ TOTAL CARGAS POR PLAZA ============ */
//PLAZA CORDOBA
$total_cargas_cor = $total_cargas_proceso_cor + $total_cargas_finalizadas_cor + $total_cargas_finalizadas_des_cor ;
//PLAZA MEXICO
$total_cargas_mex = $total_cargas_proceso_mex + $total_cargas_finalizadas_mex + $total_cargas_finalizadas_des_mex ;
//PLAZA GOLFO
$total_cargas_gol = $total_cargas_proceso_gol + $total_cargas_finalizadas_gol + $total_cargas_finalizadas_des_gol ;
//PLAZA PENINSULA
$total_cargas_pen = $total_cargas_proceso_pen + $total_cargas_finalizadas_pen + $total_cargas_finalizadas_des_pen ;
//PLAZA PUEBLA
$total_cargas_pue = $total_cargas_proceso_pue + $total_cargas_finalizadas_pue + $total_cargas_finalizadas_des_pue ;
//PLAZA BAJIO
$total_cargas_baj = $total_cargas_proceso_baj + $total_cargas_finalizadas_baj + $total_cargas_finalizadas_des_baj ;
//PLAZA OCCIDENTE
$total_cargas_occ = $total_cargas_proceso_occ + $total_cargas_finalizadas_occ + $total_cargas_finalizadas_des_occ ;
//PLAZA NORESTE
$total_cargas_nor = $total_cargas_proceso_nor + $total_cargas_finalizadas_nor + $total_cargas_finalizadas_des_nor ;
//PLAZA LEON
$total_cargas_leo = $total_cargas_proceso_leo + $total_cargas_finalizadas_leo + $total_cargas_finalizadas_des_leo ;


/* ============ TOTAL CARGAS GLOBAL ============ */
$total_cargas = $total_cargas_proceso+$total_cargas_finalizadas+$total_cargas_finalizadas_des;


// *********************************** OPERACIONES PARA DESCARGAS *********************************** //
/* ============ DESCARGAS EN PROCESO ============ */
//PLAZA CORDOBA
$total_descargas_proceso_cor = count($descargas_proceso_cor);
//PLAZA MEXICO
$total_descargas_proceso_mex = count($descargas_proceso_mex);
//PLAZA GOLFO
$total_descargas_proceso_gol = count($descargas_proceso_gol);
//PLAZA PENINSULA
$total_descargas_proceso_pen = count($descargas_proceso_pen);
//PLAZA PUEBLA
$total_descargas_proceso_pue = count($descargas_proceso_pue);
//PLAZA BAJIO
$total_descargas_proceso_baj = count($descargas_proceso_baj);
//PLAZA OCCIDENTE
$total_descargas_proceso_occ = count($descargas_proceso_occ);
//PLAZA NORESTE
$total_descargas_proceso_nor = count($descargas_proceso_nor);
//PLAZA LEON
$total_descargas_proceso_leo = count($descargas_proceso_leo);

/*TOTAL DESCARGAS EN PROCESO*/
$total_descargas_proceso = $total_descargas_proceso_cor + $total_descargas_proceso_mex + $total_descargas_proceso_gol + $total_descargas_proceso_pen + $total_descargas_proceso_pue + $total_descargas_proceso_baj + $total_descargas_proceso_occ + $total_descargas_proceso_nor + $total_descargas_proceso_leo ;

/* ============ DESCARGAS FINALIZADAS ============ */
//PLAZA CORDOBA
$total_descargas_finalizadas_cor = count($descargas_finalizadas_cor);
//PLAZA MEXICO
$total_descargas_finalizadas_mex = count($descargas_finalizadas_mex);
//PLAZA GOLFO
$total_descargas_finalizadas_gol = count($descargas_finalizadas_gol);
//PLAZA PENINSULA
$total_descargas_finalizadas_pen = count($descargas_finalizadas_pen);
//PLAZA PUEBLA
$total_descargas_finalizadas_pue = count($descargas_finalizadas_pue);
//PLAZA BAJIO
$total_descargas_finalizadas_baj = count($descargas_finalizadas_baj);
//PLAZA OCCIDENTE
$total_descargas_finalizadas_occ = count($descargas_finalizadas_occ);
//PLAZA NORESTE
$total_descargas_finalizadas_nor = count($descargas_finalizadas_nor);
//PLAZA LEON
$total_descargas_finalizadas_leo = count($descargas_finalizadas_leo);

/*TOTAL DESCARGAS FINALIZADAS*/
$total_descargas_finalizadas = $total_descargas_finalizadas_cor + $total_descargas_finalizadas_mex + $total_descargas_finalizadas_gol + $total_descargas_finalizadas_pen + $total_descargas_finalizadas_pue + $total_descargas_finalizadas_baj + $total_descargas_finalizadas_occ + $total_descargas_finalizadas_nor + $total_descargas_finalizadas_leo ;

/* ============ DESCARGAS FINALIZADAS DESFASADAS ============ */
//PLAZA CORDOBA
$total_descargas_finalizadas_des_cor = count($descargas_finalizadas_des_cor);
//PLAZA MEXICO
$total_descargas_finalizadas_des_mex = count($descargas_finalizadas_des_mex);
//PLAZA GOLFO
$total_descargas_finalizadas_des_gol = count($descargas_finalizadas_des_gol);
//PLAZA PENINSULA
$total_descargas_finalizadas_des_pen = count($descargas_finalizadas_des_pen);
//PLAZA PUEBLA
$total_descargas_finalizadas_des_pue = count($descargas_finalizadas_des_pue);
//PLAZA BAJIO
$total_descargas_finalizadas_des_baj = count($descargas_finalizadas_des_baj);
//PLAZA OCCIDENTE
$total_descargas_finalizadas_des_occ = count($descargas_finalizadas_des_occ);
//PLAZA NORESTE
$total_descargas_finalizadas_des_nor = count($descargas_finalizadas_des_nor);
//PLAZA LEON
$total_descargas_finalizadas_des_leo = count($descargas_finalizadas_des_leo);

/*TOTAL DESCARGAS FINALIZADAS DESFASADAS*/
$total_descargas_finalizadas_des = $total_descargas_finalizadas_des_cor + $total_descargas_finalizadas_des_mex + $total_descargas_finalizadas_des_gol + $total_descargas_finalizadas_des_pen + $total_descargas_finalizadas_des_pue + $total_descargas_finalizadas_des_baj + $total_descargas_finalizadas_des_occ + $total_descargas_finalizadas_des_nor + $total_descargas_finalizadas_des_leo ;


/* ============ TOTAL DESCARGAS POR PLAZA ============ */
//PLAZA CORDOBA
$total_descargas_cor = $total_descargas_proceso_cor + $total_descargas_finalizadas_cor + $total_descargas_finalizadas_des_cor ;
//PLAZA MEXICO
$total_descargas_mex = $total_descargas_proceso_mex + $total_descargas_finalizadas_mex + $total_descargas_finalizadas_des_mex ;
//PLAZA GOLFO
$total_descargas_gol = $total_descargas_proceso_gol + $total_descargas_finalizadas_gol + $total_descargas_finalizadas_des_gol ;
//PLAZA PENINSULA
$total_descargas_pen = $total_descargas_proceso_pen + $total_descargas_finalizadas_pen + $total_descargas_finalizadas_des_pen ;
//PLAZA PUEBLA
$total_descargas_pue = $total_descargas_proceso_pue + $total_descargas_finalizadas_pue + $total_descargas_finalizadas_des_pue ;
//PLAZA BAJIO
$total_descargas_baj = $total_descargas_proceso_baj + $total_descargas_finalizadas_baj + $total_descargas_finalizadas_des_baj ;
//PLAZA OCCIDENTE
$total_descargas_occ = $total_descargas_proceso_occ + $total_descargas_finalizadas_occ + $total_descargas_finalizadas_des_occ ;
//PLAZA NORESTE
$total_descargas_nor = $total_descargas_proceso_nor + $total_descargas_finalizadas_nor + $total_descargas_finalizadas_des_nor ;
//PLAZA LEON
$total_descargas_leo = $total_descargas_proceso_leo + $total_descargas_finalizadas_leo + $total_descargas_finalizadas_des_leo ;


/* ============ TOTAL DESCARGAS GLOBAL ============ */
$total_descargas = $total_descargas_proceso+$total_descargas_finalizadas+$total_descargas_finalizadas_des;


// *********************************** OPERACIONES PARA OTROS *********************************** //
/* ============ TOTAL OP OTROS ============ */
//PLAZA CORDOBA
$total_otros_cor = count($otros_cor);
//PLAZA MEXICO
$roral_otros_mex = count($otros_mex);
//PLAZA GOLFO
$roral_otros_gol = count($otros_gol);
//PLAZA PENINSULA
$roral_otros_pen = count($otros_pen);
//PLAZA PUEBLA
$roral_otros_pue = count($otros_pue);
//PLAZA BAJIO
$roral_otros_baj = count($otros_baj);
//PLAZA OCCIDENTE
$roral_otros_occ = count($otros_occ);
//PLAZA NORESTE
$roral_otros_nor = count($otros_nor);
//PLAZA LEON
$roral_otros_leo = count($otros_leo);

/*TOTAL DESCARGAS EN PROCESO*/
$total_otros = $total_otros_cor + $roral_otros_mex + $roral_otros_gol + $roral_otros_pen + $roral_otros_pue + $roral_otros_baj + $roral_otros_occ + $roral_otros_nor + $roral_otros_leo ;

// *********************************** TOTAL CARGAS-DESCARGAS DESFASADOS *********************************** //
$total_car_des_desfasados = $total_cargas_finalizadas_des + $total_descargas_finalizadas_des ;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($plaza_manufac== false)
{

/////////////////////////////CARGAS//////////////////////////////
$total_cargas_proceso = count($cargas_proceso);
$total_cargas_finalizadas = count($cargas_fin_tiempo);
$total_cargas_finalizadas_des = count($cargas_fin_des);
$total_cargas_finalizadas_des_ritm = count($cargas_fin_des_ritmo);
$total_cargas_canceladas = count($cargas_canceladas);
//--EFECTIVIDAD DE CARGAS --//
$total_cargas = $total_cargas_proceso + $total_cargas_finalizadas + $total_cargas_finalizadas_des;
$total_cargas_entregasdas = $total_cargas_finalizadas + $total_cargas_finalizadas_des ;


/////////////////////////////DESCARGAS//////////////////////////////
$total_descargas_proceso = count($descargas_proceso);
$total_descargas_finalizadas = count($descargas_fin_tiempo);
$total_descargas_finalizadas_des = count($descargas_fin_des);
$total_descargas_finalizadas_des_ritmo = count($descargas_fin_des_ritmo);
$total_descargas_canceladas = count($descargas_canceladas);
//--EFECTIVIDAD DE DESCARGAS --//
$total_descargas = $total_descargas_proceso + $total_descargas_finalizadas + $total_descargas_finalizadas_des;
$total_descargas_entregasdas = $total_descargas_finalizadas + $total_descargas_finalizadas_des ;

//TOTAL DESFASADOS CARGAS DESCARGAS
$total_car_des_desfasados = $total_cargas_finalizadas_des + $total_descargas_finalizadas_des;

/////////////////////OPERACIONES OTROS///////////////////////
$total_otros_pendientes = count($otros_pendientes);
$total_otros_proceso = count($otros_proceso);
$total_otros_concluidos = count($otros_concluidos) ;
$total_otros_cancelados = count($otros_cancelados);

$total_otros = $total_otros_pendientes + $total_otros_proceso + $total_otros_concluidos ;

}else {
  $total_cargas_proceso = count($cargas_proceso);
  $total_cargas_finalizadas = count($cargas_fin_tiempo);
  $total_cargas_finalizadas_des = count($cargas_fin_des);
  $total_cargas_canceladas = count($cargas_canceladas);
  //--EFECTIVIDAD DE CARGAS --//
  $total_cargas = $total_cargas_proceso + $total_cargas_finalizadas + $total_cargas_finalizadas_des;
  $total_cargas_entregasdas = $total_cargas_finalizadas + $total_cargas_finalizadas_des ;


  /////////////////////////////DESCARGAS//////////////////////////////
  $total_descargas_proceso = count($descargas_proceso);
  $total_descargas_finalizadas = count($descargas_fin_tiempo);
  $total_descargas_finalizadas_des = count($descargas_fin_des);
  $total_descargas_canceladas = count($descargas_canceladas);
  //--EFECTIVIDAD DE DESCARGAS --//
  $total_descargas = $total_descargas_proceso + $total_descargas_finalizadas + $total_descargas_finalizadas_des;
  $total_descargas_entregasdas = $total_descargas_finalizadas + $total_descargas_finalizadas_des ;

  //TOTAL DESFASADOS CARGAS DESCARGAS
  $total_car_des_desfasados = $total_cargas_finalizadas_des + $total_descargas_finalizadas_des;

  /////////////////////OPERACIONES OTROS///////////////////////
  $total_otros_pendientes = count($otros_pendientes);
  $total_otros_proceso = count($otros_proceso);
  $total_otros_concluidos = count($otros_concluidos) ;
  $total_otros_cancelados = count($otros_cancelados);

  $total_otros = $total_otros_pendientes + $total_otros_proceso + $total_otros_concluidos ;
}

$otros_cross_programados = count($otros_cross_programados);



?>
