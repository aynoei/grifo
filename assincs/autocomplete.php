<?php

include($_SERVER['DOCUMENT_ROOT'] . 'grifo/parts/includes.php'); 

use Medoo\Medoo;

$biblioteca = new Biblioteca\Custom();

   $database = new Medoo([
         'database_type' => 'mysql',
         'database_name' => 'grifo',
         'server' => 'localhost',
         'username' => 'root',
         'password' => ''
     ]);

$get = $_GET;

echo '<pre>';
var_dump($get);
echo '</pre>';

//$this->database->query("SELECT *  FROM procedimentos WHERE ultima_atualizacao = '0' AND promotoria = '$pj'")->fetchAll(); 