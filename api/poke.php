<?php
$pokeapi = new PokePHP\PokeApi;

function obter_dados($poke, $geracao) {
  //var_dump($poke);
  global $pokeapi;
  $pokemon_secreto = json_decode($pokeapi->pokemon($poke));
  if (empty($pokemon_secreto->id))
    return ['erro' => 'Pokémon não encontrado.'];
  $pokespecie_secreto = json_decode($pokeapi->pokemonSpecies($poke));
  //$pkscrt = [];
  //var_dump($pokespecie_secreto);
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
  $tipo2 = 'monotipo';
  if (!empty($pokemon_secreto->types[1]))
    $tipo2 = $pokemon_secreto->types[1]->type->name;
  if (!empty($pokemon_secreto->past_types)) {
    $url = $pokemon_secreto->past_types[0]->generation->url;
    $geracao_do_tipo_anterior = str_replace('/','',substr($url,strrpos(substr($url,0,strlen($url)-1),'/')));
    if ($geracao_do_tipo_anterior >= $geracao) {
      $tipo1 = $pokemon_secreto->past_types[0]->types[0]->type->name;
      $tipo2 = 'monotipo';
      if (!empty($pokemon_secreto->past_types[0]->types[1]))
        $tipo2 = $pokemon_secreto->past_types[0]->types[1]->type->name;
    }
  }
  //$habitat = $pokespecie_secreto->habitat->name;
  $cor = $pokespecie_secreto->color->name;
  //$estagio = $evolucoes->chain->species->name;
  $evoluido = 'não';
  if (!empty($pokespecie_secreto->evolves_from_species)) {
    $url = $pokespecie_secreto->evolves_from_species->url;
    $id_da_preevolucao = str_replace('/','',substr($url,strrpos(substr($url,0,strlen($url)-1),'/')));
    $id_do_ultimo = $numero_de_pokemons_por_geracao[$geracao-1][0] + $numero_de_pokemons_por_geracao[$geracao-1][1];
    if ($id_da_preevolucao <= $id_do_ultimo)
      $evoluido = 'sim';
  }
  $altura = $pokemon_secreto->height;
  $peso = $pokemon_secreto->weight;
  $url_do_sprite = $pokemon_secreto->sprites->front_default;
  
  //return $pkscrt;
  return (object) [
    'id'=>$pokemon_secreto->id,
    'nome'=>$nome,
    'tipo1'=>$tipo1,
    'tipo2'=>$tipo2,
    //'habitat'=>$habitat,
    'cor'=>$cor,
    'evoluido'=>$evoluido,
    'altura'=>$altura,
    'peso'=>$peso,
    'url_do_sprite'=>$url_do_sprite
  ];
}

if ($api == 'api') {
  if(isset($versao) && $versao == 'v1') {
    if(empty($acao)) {
      echo json_encode(['erro' => 'rota incompleta']);
      exit;
    }
    if ($metodo == 'POST') {
        echo json_encode(['erro' => 'Rota não encontrada. (POST)']);
    }
    
    else

    if ($metodo == 'GET') {
      //http_response_code(420);
      //if(isset($acao) && $acao == '') {
      //  $db = DB::conectar();
      //  $rs = $db->prepare('SELECT * FROM usuario ORDER BY id');
      //  $rs->execute();
      //  $obj = $rs->fetchAll(PDO::FETCH_ASSOC);
      //  if ($obj) {
      //    echo json_encode(['dados' => $obj]);
      //  } else {
      //    echo json_encode(['dados' => 'Nenhum resultado encontrado.']);
      //  }
      //}
      if(isset($acao) && $acao == 'novo-jogo') {
        if (isset($_SESSION['geracoes'])) {
          echo json_encode(['erro' => 'já existe um jogo em andamento. termine-o ou exclua a sessão']);
          exit;
        }
        //if(session_status() === 1)
        //  session_start();
        //if(empty($_GET['geracoes'])) {
        if(empty($param)) {
          echo json_encode(['erro' => 'é preciso informar pelo menos uma geração']);
          exit;
        }
        //$geracoes = explode(',', $_GET['geracoes']);
        $geracoes = explode(',', $param);
        //if (empty($geracoes)) {
        //  echo json_encode(['erro' => 'é preciso selecionar pelo menos uma geração']);
        //  exit;
        //}
        //var_dump($geracoes);
        foreach ($geracoes as $g) {
          if (!is_numeric($g)) {
            echo json_encode(['erro' => 'as gerações devem conter apenas números']);
            exit;
          }
          $g = $g * 1;
          if (!is_int($g)) {
            echo json_encode(['erro' => 'as gerações devem conter apenas números inteiros']);
            exit;
          }
          if ($g > 9 || $g < 1) {
            echo json_encode(['erro' => 'as gerações devem ser números entre 1 e 9']);
            exit;
          }
          //else
        }
        //$geracoes = sort($geracoes);
        //$descobriu = false;
        //$palpites = [];

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

        /*
        foreach ($geracoes as $g) {
          $G_offset = $numero_de_pokemons_por_geracao[$g-1][0];
          $G_limit = $numero_de_pokemons_por_geracao[$g-1][1];
          $G_url = 'https://pokeapi.co/api/v2/pokemon/?offset='.$G_offset.'&limit='.$G_limit;
          //$G_url_sp = 'https://pokeapi.co/api/v2/pokemon-species/?offset='.$G_offset.'&limit='.$G_limit;
          $pks = json_decode($pokeapi->sendRequest($G_url))->results;
          //var_dump($pks);
          //$pksps = json_decode($pokeapi->sendRequest($G_url_sp))->results;

          $dados_dos_pokemons = [];
          $nome;
          $tipo1;
          $tipo2;
          //$habitat;
          $cor;
          $evoluido;
          $altura;
          $peso;
          $url_do_sprite;

          for ($i=0; $i < count($pks); $i++) { 
            $pk = $pks[$i];
            $pksp = $pksps[$i];

            $nome = $pk->name;
            $tipo1 = $pk->types[0]->type->name;
            $tipo2 = 'monotipo';
            if (!empty($pk->types[1]))
              $tipo2 = $pk->types[1]->type->name;
            if (!empty($pk->past_types)) {
              $url = $pk->past_types[0]->generation->url;
              $geracao_do_tipo_anterior = str_replace('/','',substr($url,strrpos(substr($url,0,strlen($url)-1),'/')));
              if ($geracao_do_tipo_anterior >= $geracao) {
                $tipo1 = $pk->past_types[0]->types[0]->type->name;
                $tipo2 = 'monotipo';
                if (!empty($pk->past_types[0]->types[1]))
                  $tipo2 = $pk->past_types[0]->types[1]->type->name;
              }
            }
            $altura = $pk->height;
            $peso = $pk->weight;
            $url_do_sprite = $pk->sprites->front_default;
            //$habitat = $pksp->habitat->name;
            $cor = $pksp->color->name;
            //$estagio = $evolucoes->chain->species->name;
            $evoluido = 'não';
            if (!empty($pksp->evolves_from_species)) {
              $url = $pksp->evolves_from_species->url;
              $id_da_preevolucao = str_replace('/','',substr($url,strrpos(substr($url,0,strlen($url)-1),'/')));
              $id_do_ultimo = $numero_de_pokemons_por_geracao[$geracao-1][0] + $numero_de_pokemons_por_geracao[$geracao-1][1];
              if ($id_da_preevolucao <= $id_do_ultimo)
                $evoluido = 'sim';
            }

            $dados_dos_pokemons[] = [
              'id'=>$id,
              'nome'=>$nome,
              'tipo1'=>$tipo1,
              'tipo2'=>$tipo2,
              //'habitat'=>$habitat,
              'cor'=>$cor,
              'evoluido'=>$evoluido,
              'altura'=>$altura,
              'peso'=>$peso,
              'url_do_sprite'=>$url_do_sprite
            ];
          }
          $pokemons_da_geracao = array_merge($pokemons_da_geracao, $dados_dos_pokemons);
        }

        //$ids_da_geracao = [];
        //foreach ($pokemons_da_geracao as $pg) {
        //  $ids_da_geracao[] = str_replace('/','',substr($pg->url,strrpos(substr($pg->url,0,strlen($pg->url)-1),'/')));
        //}
        $ids_da_geracao = array_filter($pokemons_da_geracao, fn($i)=>$i->id);
        //var_dump($ids_da_geracao);
        */
        //echo 'aew';
        //exit;
        $seed = (int) date("Ymd");
        srand($seed);
        $indice_do_pokemon_secreto = (rand() % count($pokemons_da_geracao));
        $id_do_pokemon_secreto = $ids_da_geracao[$indice_do_pokemon_secreto];
        /*
        $pkscrt = [];
        $pokemon_secreto = json_decode($pokeapi->pokemon($id_do_pokemon_secreto));
        $pokespecie_secreto = json_decode($pokeapi->pokemonSpecies($id_do_pokemon_secreto));

        $nome = $pokemon_secreto->name;
        $tipo1 = $pokemon_secreto->types[0]->type->name;
        $tipo2 = 'monotipo';
        if (!empty($pokemon_secreto->types[1]))
          $tipo2 = $pokemon_secreto->types[1]->type->name;
        if (!empty($pokemon_secreto->past_types)) {
          $url = $pokemon_secreto->past_types[0]->generation->url;
          $geracao_do_tipo_anterior = str_replace('/','',substr($url,strrpos(substr($url,0,strlen($url)-1),'/')));
          if ($geracao_do_tipo_anterior >= $geracao) {
            $tipo1 = $pokemon_secreto->past_types[0]->types[0]->type->name;
            $tipo2 = 'monotipo';
            if (!empty($pokemon_secreto->past_types[0]->types[1]))
              $tipo2 = $pokemon_secreto->past_types[0]->types[1]->type->name;
          }
        }
        //$habitat = $pokespecie_secreto->habitat->name;
        $cor = $pokespecie_secreto->color->name;
        //$estagio = $evolucoes->chain->species->name;
        $evoluido = 'não';
        if (!empty($pokespecie_secreto->evolves_from_species)) {
          $url = $pokespecie_secreto->evolves_from_species->url;
          $id_da_preevolucao = str_replace('/','',substr($url,strrpos(substr($url,0,strlen($url)-1),'/')));
          $id_do_ultimo = $numero_de_pokemons_por_geracao[$geracao-1][0] + $numero_de_pokemons_por_geracao[$geracao-1][1];
          if ($id_da_preevolucao <= $id_do_ultimo)
            $evoluido = 'sim';
        }
        $altura = $pokemon_secreto->height;
        $peso = $pokemon_secreto->weight;
        $url_do_sprite = $pokemon_secreto->sprites->front_default;
        
        $pkscrt = [
          'id'=>$id_do_pokemon_secreto,
          'nome'=>$nome,
          'tipo1'=>$tipo1,
          'tipo2'=>$tipo2,
          //'habitat'=>$habitat,
          'cor'=>$cor,
          'evoluido'=>$evoluido,
          'altura'=>$altura,
          'peso'=>$peso,
          'url_do_sprite'=>$url_do_sprite
        ];
        */

        $pkscrt = obter_dados($id_do_pokemon_secreto, $geracao);
        //$uuid = uuid_create(UUID_TYPE_TIME);
        //$_SESSION['id'] = $uuid;
        $_SESSION['seed'] = $seed;
        $_SESSION['geracoes'] = $geracoes;
        //$_SESSION['pokemons_da_geracao'] = $pokemons_da_geracao;
        //$_SESSION['ids_da_geracao'] = $ids_da_geracao;
        $_SESSION['nomes_dos_pokemons_das_geracoes'] = $nomes_dos_pokemons_das_geracoes;
        $_SESSION['pokemon_secreto'] = $pkscrt;
        $_SESSION['descobriu'] = false;
        $_SESSION['palpites'] = [];
        //var_dump($pkscrt);
        //var_dump($_SESSION);
        //$_SESSION['geracoes'] = sort($geracoes);
        //$_SESSION['descobriu'] = false;
        //$_SESSION['palpites'] = [];
        //$_COOKIE['sessao'] = $uuid;
        echo json_encode(['sucesso' => 'sessão iniciada', 'seed' => $seed]);
      }
      else
      if(isset($acao) && $acao == 'geracoes') {
        if (empty($_SESSION['geracoes'])) {
          echo json_encode(['erro' => 'inicie uma sessão para poder jogar']);
          exit;
        }
        $g = array_map(function ($i) {return $i*1;}, $_SESSION['geracoes']);
        //var_dump($g);
        echo json_encode(['geracoes' => $g]);
      }
      else
      if(isset($acao) && $acao == 'pokemons') {
        if (empty($_SESSION['geracoes'])) {
          echo json_encode(['erro' => 'inicie uma sessão para poder jogar']);
          exit;
        }
        echo json_encode(['geracoes' => $_SESSION['nomes_dos_pokemons_das_geracoes']]);
      }
      else
      if(isset($acao) && $acao == 'palpites') {
        //if (empty($_COOKIE['sessao']))
        //  echo json_encode(['erro' => 'inicie uma sessão para poder jogar']);
        if (empty($_SESSION['geracoes'])) {
          echo json_encode(['erro' => 'inicie uma sessão para poder jogar']);
          exit;
        }
        //else
        if (empty($param)) {
          echo json_encode(['palpites' => $_SESSION['palpites']]);
          exit;
        }
        //else {
          //$achou = false;
          //for ($i=0; $i < count($_SESSION['sessoes']) && !$achou; $i++) { 
          //  if ($_COOKIE['sessao'] == $_SESSION['sessoes'][i]->id)
          //    $achou = true;
          //}
          //if (!$achou)
          //  echo json_encode(['erro' => 'sessão não encontrada']);
          //if (array_search($_COOKIE['sessao'], array_values($_SESSION['sessoes']))) {
          //}
          //$achou = false;
          //for ($i=0; $i < count($_SESSION['sessoes']) && !$achou; $i++) { 
          //  if ($_COOKIE['sessao'] == $_SESSION['sessoes'][i]->id)
          //    $achou = true;
          //}
          //if (!$achou)
          //  echo json_encode(['erro' => 'sessão não encontrada']);
          
          //$pokemon = json_decode($pokeapi->pokemon($palpite));
          //$pokemon = obter_dados($_GET['palpite']);
          $pokemon = obter_dados($param, max($_SESSION['geracoes']));
          //var_dump('Pokemon');
          //var_dump($pokemon);
          //var_dump('Keys');
          //var_dump(array_keys($pokemon));
          //var_dump('Pokemon[id]');
          //var_dump($pokemon['id']);
          if (empty($pokemon->id)) {
            echo json_encode(['erro' => 'Pokémon não encontrado']);
            exit;
          }
          if (array_search($pokemon->nome, $_SESSION['nomes_dos_pokemons_das_geracoes']) === false) {
            echo json_encode(['erro' => 'São válidos apenas pokémons das gerações selecionadas']);
            exit;
          //} else if ($palpites->findOne(['nome' => $pokemon->name])) {
          }
          foreach ($_SESSION['palpites'] as $p)
            if ($pokemon->nome == $p['nome']) {
            echo json_encode(['erro' => 'Este pokémon já foi palpitado']);
            exit;
          }

          //var_dump($pkscrt);
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
            //'habitat'=>$pokemon->habitat,$pokemon->habitat
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
        //}
      }
      else if ($acao == 'deletar') {
        session_unset();
        echo json_encode(['sucesso' => 'sessão excluída']);
      }
      else
        echo json_encode(['erro' => 'Rota não encontrada. (GET)']);
    }

    //else
    
    //if ($metodo == 'PUT') {
    //}

    else
    
    if ($metodo == 'DELETE') {
      if(isset($acao)) {
        if ($acao == 'jogo') {
          //if (empty($_COOKIE['sessao']))
          //  echo json_encode(['erro' => 'cookie de sessão vazio']);
          //$achou = false;
          //for ($i=0; $i < count($_SESSION['sessoes']) && !$achou; $i++) { 
          //  if ($_COOKIE['sessao'] == $_SESSION['sessoes'][i]->id) {
          //    $achou = true;
          //    array_splice($_SESSION['sessoes'], i, 1);
          //  }
          //}
          //if (!$achou)
          //  echo json_encode(['erro' => 'sessão não encontrada']);
          //unset($_SESSION);
          session_unset();
          echo json_encode(['sucesso' => 'sessão excluída']);
        }
        //else
        //if ($acao == 'cookie') {
        //  unset($_COOKIE['sessao']);
        //  echo json_encode(['sucesso' => 'cookie excluído']);
        //}
        else
          echo json_encode(['erro' => 'Rota não encontrada. (DELETE)']);
      }
      else
        echo json_encode(['erro' => 'Rota incompleta.']);
    }
    
    else
      echo json_encode(['erro' => 'Método não reconhecido ou não utilizado.']);

  } else
    echo json_encode(['erro' => 'Versão não encontrada.']);

} else
  echo json_encode(['erro' => 'API não encontrada.']);