rotas:

POST /jogo
Inicia um novo jogo. Recebe os números das gerações, como array ou string (separados por vírgula).

Parâmetros: body (object)
{
  "geracoes": [int] ou string
}

Retorno: nenhum
{
  "seed": int
  "sucesso": 
}

POST /palpites
Registra um palpite. Recebe uma string com o nome do pokémon e retorna um objeto com suas informações e a classificação de cada categoria em relação ao pokémon secreto.
0 = errou ou (em altura e peso) valor menor que o do pokémon secreto;
1 = acertou;
2 (nos tipos) = acertou na posição errada;
2 (em altura e peso) = valor maior que o do pokémon secreto

Parâmetros: body (object)
{
  "pokemon": string
}

Retorno:
{
  "id": int,
  "id_c": int,
  "nome": string,
  "nome_c": int,
  "tipo1": string,
  "tipo1_c": int,
  "tipo2": string,
  "tipo2_c": int,
  "cor": string,
  "cor_c": int,
  "evoluido": string,
  "evoluido_c": int,
  "altura": int,
  "altura_c": int,
  "peso": int,
  "peso_c": int,
  "url_do_sprite": string
}

GET /jogo
Retorna dados sobre a partida atual.

Parâmetros: nenhum

Retorno:
{
  "seed": int,
  "geracoes": [int],
  "total_de_pokemons_das_geracoes": int,
  "numero_de_palpites": int,
  "descobriu": boolean
}

GET /pokemons
Retorna dados sobre os pokémons da partida atual.

Parâmetros: nenhum

Retorno:
{
  "ids_dos_pokemons_das_geracoes_selecionadas": [int],
  "pokemons_das_geracoes_selecionadas": [string],
  "url_do_sprite_dos_pokemons_das_geracoes_selecionadas": [string]
}

GET /palpites
Retorna um array com todos os palpites já dados na partida atual.

Parâmetros: nenhum

Retorno:
[
  {
    "id": int,
    "id_c": int,
    "nome": string,
    "nome_c": int,
    "tipo1": string,
    "tipo1_c": int,
    "tipo2": string,
    "tipo2_c": int,
    "cor": string,
    "cor_c": int,
    "evoluido": string,
    "evoluido_c": int,
    "altura": double,
    "altura_c": int,
    "peso": double,
    "peso_c": int,
    "url_do_sprite": string
  }
]

<!--/nomes-dos-pokemons (GET)
parâmetros: nenhum
retorno:
{
  "pokemons_das_geracoes_selecionadas": [string]
}

/ids-dos-pokemons (GET)
parâmetros: nenhum
retorno:
{
  "ids_dos_pokemons_das_geracoes_selecionadas": [int]
}

/urls-dos-sprites-dos-pokemons (GET)
parâmetros: nenhum
retorno:
{
  "url_do_sprite_dos_pokemons_das_geracoes_selecionadas": [string]
}-->