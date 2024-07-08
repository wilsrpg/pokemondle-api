<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
date_default_timezone_set('America/Sao_Paulo');
if(session_status() === 1)
  session_start();

require 'vendor/autoload.php';

if(isset($_GET['url'])) {
  $url = explode('/', $_GET['url']);
  if (isset($url[0]))
    $api = $url[0];
  if (isset($url[1]))
    $versao = $url[1];
  if (isset($url[2]))
    $acao = $url[2];
  if (isset($url[3]))
    $param = $url[3];
  $metodo = $_SERVER['REQUEST_METHOD'];
  
  if ($api != 'api' || empty($versao) || empty($acao))
    echo json_encode(['erro' => 'Rota não encontrada. Verifique se o endereço corresponde ao padrão domínio/api/versão/rota.']);
  else if ($metodo != 'GET')
    echo json_encode(['erro' => 'O único método utilizado nesta API é o GET.']);
  else
    include_once 'api/poke.php';
}