<?php

include $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
use Medoo\Medoo;
$biblioteca = new Biblioteca\Custom();
$tabbelas = new Biblioteca\Tabelas();

$database = new Medoo([
         'database_type' => 'mysql',
         'database_name' => 'grifo',
         'server' => 'localhost',
         'username' => 'root',
         'password' => ''
     ]);

$input = filter_input_array(INPUT_POST);


echo json_encode($input);
