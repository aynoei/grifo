<?php
error_reporting(0); ini_set('display_errors', 0);


//error_reporting(E_ALL); ini_set('display_errors', 1);
// If you installed via composer, just use this code to require autoloader on the top of your projects.
include $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
include $_SERVER['DOCUMENT_ROOT'] .  'grifo/functions.php'; $grifo = new Grifo();
include $_SERVER['DOCUMENT_ROOT'] . 'grifo/calendar.php'; $eventos = new EventosGoogle();
include_once($_SERVER['DOCUMENT_ROOT'] . 'grifo/parts/database.php'); $database = database();
//include $_SERVER['DOCUMENT_ROOT'] . '/grifo/classes/formulario.php'; $formulario = new Formulario();



$authGrifo = array('promotoria'=>9,'user'=>1);
$calendario_id = 'promotoriadasaude@gmail.com';