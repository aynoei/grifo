<?php

include($_SERVER['DOCUMENT_ROOT'] . 'grifo/parts/includes.php'); 

use Medoo\Medoo;

$biblioteca = new Biblioteca\Custom();

global $database;

$get = $_GET;

echo '<pre>';
var_dump($get);
echo '</pre>';

//$this->database->query("SELECT *  FROM procedimentos WHERE ultima_atualizacao = '0' AND promotoria = '$pj'")->fetchAll(); 