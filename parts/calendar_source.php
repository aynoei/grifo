<?php  
//error_reporting(0); 
include($_SERVER['DOCUMENT_ROOT'] . '/grifo/parts/includes.php'); 


echo $eventos->lerEventos('promotoriadasaude@gmail.com',$_GET['start'],$_GET['end']);

/*
echo '<pre>';
var_dump($eventos->lerEventos('promotoriadasaude@gmail.com','array'));
echo '</pre>';
*/