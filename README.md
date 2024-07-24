### URL base: [https://wilsrpg.x10.mx/pokedle-api/v1](https://wilsrpg.x10.mx/pokedle-api/v1)
Todas as requisições podem ser feitas em formato JSON ou formulário HTML.

### POST /jogo

Inicia um novo jogo. Recebe os números das gerações (de 1 a 9), como array ou string (separados por vírgula), opcionalmente o número da geração do contexto do jogo como número (pra determinar tipos e evoluções; se não for especificado, o número da maior geração será usado), e retorna o valor da seed usada para gerar o pokémon secreto, ou uma string contendo a descrição do erro, caso haja.

**Parâmetros:** body (object)

	{
		"geracoes": [int] ou string,
		"geracao_contexto": int (opcional)
	}

**Retorno:**
200: operação bem sucedida

	{
		"seed": int,
		"jogo": string,
		"geracoes": [int],
		"geracao_contexto": int
	}
400: problema no parâmetro

	{
		"erro": string
	}



### POST /palpites

Registra um palpite. Recebe uma string com o nome do pokémon e retorna um objeto com suas informações e a classificação de cada categoria em relação ao pokémon secreto, ou uma string contendo a descrição do erro, caso haja.
0 = errou ou (em altura e peso) valor menor que o do pokémon secreto;
1 = acertou;
2 (nos tipos) = acertou, mas está na posição errada;
2 (em altura e peso) = valor maior que o do pokémon secreto

**Parâmetros:** body (object)

	{
		"palpite": string
	}

**Retorno:**
200: operação bem sucedida

	{
		"id": int,
		"id_r": int,
		"nome": string,
		"nome_r": int,
		"tipo1": string,
		"tipo1_r": int,
		"tipo2": string,
		"tipo2_r": int,
		"cor": string,
		"cor_r": int,
		"evoluido": string,
		"evoluido_r": int,
		"altura": int,
		"altura_r": int,
		"peso": int,
		"peso_r": int,
		"url_do_sprite": string
	}
400: problema no parâmetro
403: sem partida iniciada
409: pokémon já palpitado
422: pokémon de geração não selecionada

	{
		"erro": string
	}



### GET /jogo

Retorna dados sobre a partida atual, ou uma string contendo a descrição do erro, caso haja.

**Parâmetros:** nenhum

**Retorno:**
200: operação bem sucedida

	{
		"seed": int,
		"geracoes": [int],
		"geracao_contexto": int,
		"total_de_pokemons_das_geracoes_selecionadas": int,
		"total_de_palpites": int,
		"descobriu": boolean
	}
403: sem partida iniciada

	{
		"erro": string
	}



### GET /pokemons

Retorna dados sobre os pokémons da partida atual, ou uma string contendo a descrição do erro, caso haja.

**Parâmetros:** nenhum

**Retorno:**
200: operação bem sucedida

	{
		"ids_dos_pokemons_das_geracoes_selecionadas": [int],
		"nomes_dos_pokemons_das_geracoes_selecionadas": [string],
		"urls_dos_sprites_dos_pokemons_das_geracoes_selecionadas": [string]
	}
403: sem partida iniciada

	{
		"erro": string
	}



### GET /palpites

Retorna um array com todos os palpites já dados na partida atual, ou uma string contendo a descrição do erro, caso haja.

**Parâmetros:** nenhum

**Retorno:**
200: operação bem sucedida

	{
		"palpites": [
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
	}
403: sem partida iniciada

	{
		"erro": string
	}