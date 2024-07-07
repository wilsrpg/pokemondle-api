<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
date_default_timezone_set('America/Sao_Paulo');

require 'vendor/autoload.php';

if(isset($_GET['url']))
  $url = explode('/', $_GET['url']);
else {
  echo 'O caminho especificado não foi encontrado.';
  exit; 
}
if (isset($url[0])) {
  $api = $url[0];
  //echo $url[0];
}
if (isset($url[1])) {
  $versao = $url[1];
  //echo $url[1];
}
if (isset($url[2])) {
  $acao = $url[2];
  //echo $url[2];
}
if (isset($url[3])) {
  $param = $url[3];
  //echo $url[3];
}
$metodo = $_SERVER['REQUEST_METHOD'];

include_once 'api/poke.php';