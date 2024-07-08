<?php
$pokeapi = new PokePHP\PokeApi;

function obter_dados($poke, $geracao) {
  global $pokeapi;
  $pokemon_secreto = json_decode($pokeapi->pokemon($poke));
  if (empty($pokemon_secreto->id))
    return ['erro' => 'Pokémon não encontrado.'];
  $pokespecie_secreto = json_decode($pokeapi->pokemonSpecies($poke));
  $numero_de_pokemons_por_geracao = [
    [0,151],
    [151,100],
    [251,135],
    [386,107],
    [493,156],
    [649,72],
    [721,88],
    [809,96],
    [905,120] 
  ];
  
  $nome = $pokemon_secreto->name;
  $tipo1 = $pokemon_secreto->types[0]->type->name;
  $tipo2 = 'none';
  if (!empty($pokemon_secreto->types[1]))
    $tipo2 = $pokemon_secreto->types[1]->type->name;
  if (!empty($pokemon_secreto->past_types)) {
    $url = $pokemon_secreto->past_types[0]->generation->url;
    $geracao_do_tipo_anterior = str_replace('/','',substr($url,strrpos(substr($url,0,strlen($url)-1),'/')));
    if ($geracao_do_tipo_anterior >= $geracao) {
      $tipo1 = $pokemon_secreto->past_types[0]->types[0]->type->name;
      $tipo2 = 'none';
      if (!empty($pokemon_secreto->past_types[0]->types[1]))
        $tipo2 = $pokemon_secreto->past_types[0]->types[1]->type->name;
    }
  }
  //$habitat = $pokespecie_secreto->habitat->name;
  $cor = $pokespecie_secreto->color->name;
  //$estagio_de_evolucao = $evolucoes->chain->species->name;
  $evoluido = 'não';
  if (!empty($pokespecie_secreto->evolves_from_species)) {
    $url = $pokespecie_secreto->evolves_from_species->url;
    //$id_da_preevolucao = str_replace('/','',substr($url,strrpos(substr($url,0,strlen($url)-1),'/')));
    $url = substr($url, 0, strlen($url)-1);
    $id_da_preevolucao = substr($url, strrpos($url,'/')+1);
    $id_do_ultimo = $numero_de_pokemons_por_geracao[$geracao-1][0] + $numero_de_pokemons_por_geracao[$geracao-1][1];
    if ($id_da_preevolucao <= $id_do_ultimo)
      $evoluido = 'sim';
  }
  $altura = $pokemon_secreto->height;
  $peso = $pokemon_secreto->weight;
  $url_do_sprite = $pokemon_secreto->sprites->front_default;
  
  return (object) [
    'id'=>$pokemon_secreto->id,
    'nome'=>$nome,
    'tipo1'=>$tipo1,
    'tipo2'=>$tipo2,
    //'habitat'=>$habitat,
    'cor'=>$cor,
    //'estagio_de_evolucao'=>$estagio_de_evolucao,
    'evoluido'=>$evoluido,
    'altura'=>$altura,
    'peso'=>$peso,
    'url_do_sprite'=>$url_do_sprite
  ];
}

//if ($api == 'api') {
  //if (empty($versao)) {
  //  echo json_encode(['erro' => 'Versão não encontrada.']);
  //}
  //else
  if ($versao == 'v1') {
    if (empty($acao)) {
      echo json_encode(['erro' => 'rota incompleta']);
      exit;
    }

    else

    //if ($metodo == 'GET') {
      //http_response_code(420);
      if ($acao == 'novo-jogo') {
        if (isset($_SESSION['geracoes'])) {
          echo json_encode(['erro' => 'já existe um jogo em andamento. termine-o ou exclua a sessão']);
          exit;
        }
        if (empty($param)) {
          echo json_encode(['erro' => 'é preciso informar pelo menos uma geração']);
          exit;
        }
        $geracoes = explode(',', $param);
        foreach ($geracoes as $g) {
          if (!is_numeric($g)) {
            echo json_encode(['erro' => 'as gerações devem conter apenas números inteiros separados por vírgula']);
            exit;
          }
          $g = $g * 1;
          if (!is_int($g)) {
            echo json_encode(['erro' => 'as gerações devem conter apenas números inteiros separados por vírgula']);
            exit;
          }
          if ($g > 9 || $g < 1) {
            echo json_encode(['erro' => 'as gerações devem ser números entre 1 e 9']);
            exit;
          }
        }

        sort($geracoes);
        $geracao = max($geracoes);
        $numero_de_pokemons_por_geracao = [
          [0,151],
          [151,100],
          [251,135],
          [386,107],
          [493,156],
          [649,72],
          [721,88],
          [809,96],
          [905,120] 
        ];
        $G_offset = 0;
        $G_limit = 0;
        $pks = [];
        $pksps = [];
        $pokemons_da_geracao = [];
        $nomes_dos_pokemons_das_geracoes = [];

        foreach ($geracoes as $g) {
          $G_offset = $numero_de_pokemons_por_geracao[$g-1][0];
          $G_limit = $numero_de_pokemons_por_geracao[$g-1][1];
          $G_url = 'https://pokeapi.co/api/v2/pokemon-species/?offset='.$G_offset.'&limit='.$G_limit;
          $pks = json_decode($pokeapi->sendRequest($G_url))->results;
          $pokemons_da_geracao = array_merge($pokemons_da_geracao,$pks);
        }

        $ids_da_geracao = [];
        foreach ($pokemons_da_geracao as $pg) {
          $ids_da_geracao[] = str_replace('/','',substr($pg->url,strrpos(substr($pg->url,0,strlen($pg->url)-1),'/')));
          $nomes_dos_pokemons_das_geracoes[] = $pg->name;
        }

        $seed = (int) date("Ymd");
        srand($seed);
        $indice_do_pokemon_secreto = (rand() % count($pokemons_da_geracao));
        $id_do_pokemon_secreto = $ids_da_geracao[$indice_do_pokemon_secreto];

        $pkscrt = obter_dados($id_do_pokemon_secreto, $geracao);
        //$uuid = uuid_create(UUID_TYPE_TIME);
        //$_SESSION['id'] = $uuid;
        $_SESSION['seed'] = $seed;
        $_SESSION['geracoes'] = $geracoes;
        //$_SESSION['ids_da_geracao'] = $ids_da_geracao;
        $_SESSION['nomes_dos_pokemons_das_geracoes'] = $nomes_dos_pokemons_das_geracoes;
        $_SESSION['pokemon_secreto'] = $pkscrt;
        $_SESSION['descobriu'] = false;
        $_SESSION['palpites'] = [];
        echo json_encode(['sucesso' => 'sessão iniciada', 'seed' => $seed]);
      }
      else
      if ($acao == 'geracoes') {
        if (empty($_SESSION['geracoes'])) {
          echo json_encode(['erro' => 'inicie uma sessão para poder jogar']);
          exit;
        }
        $g = array_map(function ($i) {return $i*1;}, $_SESSION['geracoes']);
        echo json_encode(['geracoes' => $g]);
      }
      else
      if ($acao == 'pokemons') {
        if (empty($_SESSION['geracoes'])) {
          echo json_encode(['erro' => 'inicie uma sessão para poder jogar']);
          exit;
        }
        echo json_encode(['pokemons_das_geracoes_selecionadas' => $_SESSION['nomes_dos_pokemons_das_geracoes']]);
      }
      else
      if ($acao == 'palpites') {
        if (empty($_SESSION['geracoes'])) {
          echo json_encode(['erro' => 'inicie uma sessão para poder jogar']);
          exit;
        }
        if (empty($param)) {
          echo json_encode(['palpites' => $_SESSION['palpites']]);
          exit;
        }
        $pokemon = obter_dados($param, max($_SESSION['geracoes']));
        if (empty($pokemon->id)) {
          echo json_encode(['erro' => 'Pokémon não encontrado']);
          exit;
        }
        if (array_search($pokemon->nome, $_SESSION['nomes_dos_pokemons_das_geracoes']) === false) {
          echo json_encode(['erro' => 'São válidos apenas pokémons das gerações selecionadas: '.implode(',', $_SESSION['geracoes'])]);
          exit;
        }
        foreach ($_SESSION['palpites'] as $p)
          if ($pokemon->nome == $p['nome']) {
          echo json_encode(['erro' => 'Este pokémon já foi palpitado']);
          exit;
        }

        $pkscrt = (object) $_SESSION['pokemon_secreto'];
        $resultado = 
        [
          'id'=>$pokemon->id,
          'id_r'=>$pokemon->id === $pkscrt->id ? 1 : 0,
          'nome'=>$pokemon->nome,
          'nome_r'=>$pokemon->nome === $pkscrt->nome ? 1 : 0,
          'tipo1'=>$pokemon->tipo1,
          'tipo1_r'=>($pokemon->tipo1 === $pkscrt->tipo1 ? 1 : ($pokemon->tipo1 === $pkscrt->tipo2 ? 0.5 : 0)),
          'tipo2'=>$pokemon->tipo2,
          'tipo2_r'=>$pokemon->tipo2 === $pkscrt->tipo2 ? 1 : ($pokemon->tipo2 === $pkscrt->tipo1 ? 0.5 : 0),
          //'habitat'=>$pokemon->habitat,
          //'habitat_r'=>$pokemon->habitat === $pkscrt->habitat ? 1 : 0,
          'cor'=>$pokemon->cor,
          'cor_r'=>$pokemon->cor === $pkscrt->cor ? 1 : 0,
          'evoluido'=>$pokemon->evoluido,
          'evoluido_r'=>$pokemon->evoluido === $pkscrt->evoluido ? 1 : 0,
          'altura'=>$pokemon->altura,
          'altura_r'=>$pokemon->altura === $pkscrt->altura ? 1 : ($pokemon->altura > $pkscrt->altura ? 1.5 : 0.5),
          'peso'=>$pokemon->peso,
          'peso_r'=>$pokemon->peso === $pkscrt->peso ? 1 : ($pokemon->peso > $pkscrt->peso ? 1.5 : 0.5),
          'url_do_sprite'=>$pokemon->url_do_sprite
        ];
        $_SESSION['palpites'][] = $resultado;

        echo json_encode(['resultado' => $resultado]);
      }
      else if ($acao == 'deletar') {
        session_unset();
        echo json_encode(['sucesso' => 'sessão excluída']);
      }
      else
        echo json_encode(['erro' => 'Rota não encontrada: ' . $acao]);
    //}

    //else
    //if ($metodo == 'POST') {
    //  echo json_encode(['erro' => 'Rota não encontrada. (POST)']);
    //}
        
    //else
    //if ($metodo == 'PUT') {
    //  echo json_encode(['erro' => 'Rota não encontrada. (PUT)']);
    //}

    //else
    //if ($metodo == 'DELETE') {
    //  if (isset($acao)) {
    //    if ($acao == 'jogo') {
    //      session_unset();
    //      echo json_encode(['sucesso' => 'sessão excluída']);
    //    }
    //    else
    //      echo json_encode(['erro' => 'Rota não encontrada. (DELETE)']);
    //  }
    //  else
    //    echo json_encode(['erro' => 'Rota incompleta.']);
    //}
    
    //else
    //  echo json_encode(['erro' => 'Método não reconhecido ou não utilizado.']);

  }
  else
    echo json_encode(['erro' => 'Versão não encontrada.']);

//} else
//  echo json_encode(['erro' => 'API não encontrada.']);