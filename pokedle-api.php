<?php
$pokeapi = new PokePHP\PokeApi;

function obter_dados($poke, $geracao) {
  global $pokeapi;
  $pokespecie_secreto = json_decode($pokeapi->pokemonSpecies($poke));
  if (empty($pokespecie_secreto->id))
    return ['erro' => 'Pokémon não encontrado: '.$poke.'"'];
  $pokemon_secreto = json_decode($pokeapi->pokemon($pokespecie_secreto->id));
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

  $tipos = [
  	"normal" => "normal",
    "fighting" => "lutador",
    "flying" => "voador",
    "poison" => "venenoso",
    "ground" => "terra",
    "rock" => "pedra",
    "bug" => "inseto",
    "ghost" => "fantasma",
    "steel" => "metálico",
    "fire" => "fogo",
    "water" => "água",
    "grass" => "planta",
    "electric" => "elétrico",
    "psychic" => "psíquico",
    "ice" => "gelo",
    "dragon" => "dragão",
    "dark" => "noturno",
    "fairy" => "fada",
    "stellar" => "estelar",
    "unknown" => "desconhecido",
    "nenhum" => "nenhum"
  ];
  $cores = [
    "black" => "preto",
    "blue" => "azul",
    "brown" => "marrom",
    "gray" => "cinza",
    "green" => "verde",
    "pink" => "rosa",
    "purple" => "roxo",
    "red" => "vermelho",
    "white" => "branco",
    "yellow" => "amarelo"
  ];
  
  $nome = $pokespecie_secreto->name;
  $tipo1 = $pokemon_secreto->types[0]->type->name;
  $tipo2 = 'nenhum';
  if (!empty($pokemon_secreto->types[1]))
    $tipo2 = $pokemon_secreto->types[1]->type->name;
  if (!empty($pokemon_secreto->past_types)) {
    $url = $pokemon_secreto->past_types[0]->generation->url;
    $geracao_do_tipo_anterior = str_replace('/','',substr($url,strrpos(substr($url,0,strlen($url)-1),'/')));
    if ($geracao_do_tipo_anterior >= $geracao) {
      $tipo1 = $pokemon_secreto->past_types[0]->types[0]->type->name;
      $tipo2 = 'nenhum';
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
    'id'=>$pokemon_secreto->id*1,
    'nome'=>$nome,
    'tipo1'=>$tipos[$tipo1],
    'tipo2'=>$tipos[$tipo2],
    //'habitat'=>$habitat,
    'cor'=>$cores[$cor],
    //'estagio_de_evolucao'=>$estagio_de_evolucao,
    'evoluido'=>$evoluido,
    'altura'=>$altura/10,
    'peso'=>$peso/10,
    'url_do_sprite'=>$url_do_sprite
  ];
}

  if ($api == 'pokedle-api') {
  if ($versao == 'v1') {
    //if (empty($acao)) {
    //  http_response_code(400);
    //  echo json_encode(['erro' => 'rota incompleta']);
    //  exit;
    //}

      //if ($acao == 'jogo') {
        if ($metodo == 'POST' && $acao == 'jogo') {
        //if (isset($_SESSION['geracoes'])) {
        //  //http_response_code(400);
        //  echo json_encode(['erro' => 'Já existe um jogo em andamento. termine-o ou exclua a sessão']);
        //  exit;
        //}

        $qp_geracoes = '';
        if(array_key_exists('geracoes', $query_params))
          $qp_geracoes = $query_params['geracoes'];

        if (empty($qp_geracoes)) {
          http_response_code(400);
          echo json_encode(['erro' => 'É preciso informar pelo menos uma geração (use o parâmetro "geracoes". Por exemplo, para selecionar as gerações 2 e 3: ?geracoes=2,3).']);
          exit;
        }
        $geracoes;
        if (is_array($qp_geracoes))
          $geracoes = $qp_geracoes;
        else
          $geracoes = explode(',', $qp_geracoes);
        foreach ($geracoes as $g) {
          if (!is_numeric($g)) {
            http_response_code(400);
            echo json_encode(['erro' => 'As gerações devem conter apenas números inteiros.']);
            exit;
          }
          $g = $g * 1;
          if (!is_int($g)) {
            http_response_code(400);
            echo json_encode(['erro' => 'as gerações devem conter apenas números inteiros.']);
            exit;
          }
          if ($g > 9 || $g < 1) {
            http_response_code(400);
            echo json_encode(['erro' => 'as gerações devem ser números entre 1 e 9']);
            exit;
          }
        }
        $geracoes = array_map(function ($i) {return $i*1;}, $geracoes);

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
        $pokemons_da_geracao_ = [];

        foreach ($geracoes as $g) {
          $G_offset = $numero_de_pokemons_por_geracao[$g-1][0];
          $G_limit = $numero_de_pokemons_por_geracao[$g-1][1];
          $G_url = 'https://pokeapi.co/api/v2/pokemon-species/?offset='.$G_offset.'&limit='.$G_limit;
          $pks = json_decode($pokeapi->sendRequest($G_url))->results;
          $pokemons_da_geracao = array_merge($pokemons_da_geracao,$pks);
        }

        $nomes_dos_pokemons_das_geracoes = [];
        $ids_dos_pokemons_das_geracoes = [];
        $urls_dos_sprites = [];
        foreach ($pokemons_da_geracao as $pg) {
          $nomes_dos_pokemons_das_geracoes[] = $pg->name;
          $id = (int) str_replace('/','',substr($pg->url,strrpos(substr($pg->url,0,strlen($pg->url)-1),'/')));
          $ids_dos_pokemons_das_geracoes[] = $id;
          $urls_dos_sprites[] = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/'.$id.'.png';
        }

        $seed = (int) date("Ymd");
        srand($seed);
        $total_de_pokemons_das_geracoes = count($pokemons_da_geracao);
        $indice_do_pokemon_secreto = (rand() % $total_de_pokemons_das_geracoes);
        $id_do_pokemon_secreto = $ids_dos_pokemons_das_geracoes[$indice_do_pokemon_secreto];

        $pkscrt = obter_dados($id_do_pokemon_secreto, $geracao);
        //$uuid = uuid_create(UUID_TYPE_TIME);
        //$_SESSION['id'] = $uuid;
        $_SESSION['seed'] = $seed;
        $_SESSION['geracoes'] = $geracoes;
        $_SESSION['total_de_pokemons_das_geracoes_selecionadas'] = $total_de_pokemons_das_geracoes;
        $_SESSION['ids_dos_pokemons_das_geracoes_selecionadas'] = $ids_dos_pokemons_das_geracoes;
        $_SESSION['nomes_dos_pokemons_das_geracoes_selecionadas'] = $nomes_dos_pokemons_das_geracoes;
        $_SESSION['urls_dos_sprites_dos_pokemons_das_geracoes_selecionadas'] = $urls_dos_sprites;
        $_SESSION['pokemon_secreto'] = $pkscrt;
        $_SESSION['descobriu'] = false;
        $_SESSION['palpites'] = [];

        echo json_encode(['seed' => $seed]);
        exit;
      }
      //else

      if ($metodo == 'GET' && $acao == 'jogo') {
      //if ($acao == 'jogo') {
        if (empty($_SESSION['geracoes'])) {
          http_response_code(403);
          echo json_encode(['erro' => 'Não há jogos em andamento em sua sessão.']);
          exit;
        }
        //$g = array_map(function ($i) {return $i*1;}, $_SESSION['geracoes']);
        $jogo = [
          'seed' => $_SESSION['seed'],
          'geracoes' => $_SESSION['geracoes'],
          'total_de_pokemons_das_geracoes_selecionadas' => $_SESSION['total_de_pokemons_das_geracoes_selecionadas'],
          'total_de_palpites' => count($_SESSION['palpites']),
          'descobriu' => $_SESSION['descobriu']
        ];
        //header('X-PHP-Response-Code: ?', true, ?);
        //http_response_code(?);
        echo json_encode($jogo);
        exit;
      }
      //}
      //else
      
      if ($metodo == 'GET' && $acao == 'pokemons') {
        if (empty($_SESSION['geracoes'])) {
          http_response_code(403);
          echo json_encode(['erro' => 'inicie uma sessão para poder jogar']);
          exit;
        }
        echo json_encode([
          "ids_dos_pokemons_das_geracoes_selecionadas" => $_SESSION['ids_dos_pokemons_das_geracoes_selecionadas'],
          "nomes_dos_pokemons_das_geracoes_selecionadas" => $_SESSION['nomes_dos_pokemons_das_geracoes_selecionadas'],
          "urls_dos_sprites_dos_pokemons_das_geracoes_selecionadas" => $_SESSION['urls_dos_sprites_dos_pokemons_das_geracoes_selecionadas']
        ]);
        exit;
      }
      //else
      if ($metodo == 'GET' && $acao == 'palpites') {
        if (empty($_SESSION['geracoes'])) {
          http_response_code(403);
          echo json_encode(['erro' => 'inicie uma sessão para poder jogar']);
          exit;
        }
        //if ($metodo == 'GET') {
          //if (empty($param)) {
          //echo json_encode(['palpites' => $_SESSION['palpites']]);
          echo json_encode($_SESSION['palpites']);
          exit;
        //} else {
        //  http_response_code(404);
        //  echo json_encode(['erro' => 'Rota não encontrada.']);
        //  exit;
        //}
      }

      if ($metodo == 'POST' && $acao == 'palpites') {
        if (empty($_SESSION['geracoes'])) {
          http_response_code(403);
          echo json_encode(['erro' => 'inicie uma sessão para poder jogar']);
          exit;
        }
        if (empty($_POST['pokemon'])) {
          http_response_code(400);
          echo json_encode(['erro' => 'Propriedade "pokemon" ausente do corpo da requisição.']);
          exit;
        }
        $pk = $_POST['pokemon'];
        //$pokemon = obter_dados($param, max($_SESSION['geracoes']));
        $pokemon = obter_dados($pk, max($_SESSION['geracoes']));
        if (empty($pokemon->id)) {
          http_response_code(400);
          echo json_encode(['erro' => 'Pokémon não encontrado']);
          exit;
        }
        if (array_search($pokemon->nome, $_SESSION['nomes_dos_pokemons_das_geracoes_selecionadas']) === false) {
          http_response_code(422);
          echo json_encode(['erro' => 'São válidos apenas pokémons das gerações selecionadas: '.implode(',', $_SESSION['geracoes'])]);
          exit;
        }
        foreach ($_SESSION['palpites'] as $p)
          if ($pokemon->nome == $p['nome']) {
          http_response_code(409);
          echo json_encode(['erro' => 'Este pokémon já foi palpitado']);
          exit;
        }

        $pkscrt = (object) $_SESSION['pokemon_secreto'];
        $resultado = 
        [
          'id'=>$pokemon->id,
          'id_c'=>$pokemon->id === $pkscrt->id ? 1 : 0,
          'nome'=>$pokemon->nome,
          'nome_c'=>$pokemon->nome === $pkscrt->nome ? 1 : 0,
          'tipo1'=>$pokemon->tipo1,
          'tipo1_c'=>($pokemon->tipo1 === $pkscrt->tipo1 ? 1 : ($pokemon->tipo1 === $pkscrt->tipo2 ? 2 : 0)),
          'tipo2'=>$pokemon->tipo2,
          'tipo2_c'=>$pokemon->tipo2 === $pkscrt->tipo2 ? 1 : ($pokemon->tipo2 === $pkscrt->tipo1 ? 2 : 0),
          //'habitat'=>$pokemon->habitat,
          //'habitat_c'=>$pokemon->habitat === $pkscrt->habitat ? 1 : 0,
          'cor'=>$pokemon->cor,
          'cor_c'=>$pokemon->cor === $pkscrt->cor ? 1 : 0,
          'evoluido'=>$pokemon->evoluido,
          'evoluido_c'=>$pokemon->evoluido === $pkscrt->evoluido ? 1 : 0,
          'altura'=>$pokemon->altura,
          'altura_c'=>$pokemon->altura === $pkscrt->altura ? 1 : ($pokemon->altura > $pkscrt->altura ? 2 : 0),
          'peso'=>$pokemon->peso,
          'peso_c'=>$pokemon->peso === $pkscrt->peso ? 1 : ($pokemon->peso > $pkscrt->peso ? 2 : 0),
          'url_do_sprite'=>$pokemon->url_do_sprite
        ];
        $_SESSION['palpites'][] = $resultado;

        //echo json_encode(['resultado' => $resultado]);
        echo json_encode($resultado);
        exit;
      }
      //}
      //else
      //if ($acao == 'deletar') {
      //  session_unset();
      //  echo json_encode(['sucesso' => 'sessão excluída']);
      //  exit;
      //}
      //else
        http_response_code(404);
        echo json_encode(['erro' => 'Rota não encontrada: "' . $acao.'"']);
        exit;
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
  //else
    http_response_code(404);
    echo json_encode(['erro' => 'Versão não encontrada: "'.$versao.'"']);
    exit;

  }
  //else
    http_response_code(404);
    echo json_encode(['erro' => 'API não encontrada: "'.$api.'"']);
    exit;

//} else
//  echo json_encode(['erro' => 'API não encontrada.']);