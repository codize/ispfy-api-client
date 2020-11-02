# Esta biblioteca auxilia na implementação da Api do Ispfy

## Documentação completa em
https://www.ispfy.com.br/api/postman

## Portas de acesso
* Porta 8020 para HTTP e 8043 para HTTPs (Usar preferencialmente HTTPs pois o conteúdo poderá ser interceptado, inclusive o TOKEN)
* Liberar no firewall em Sistema > Network > Firewall

## Token de autenticação
Deve ser enviado no Header com a tag 'token'

## Estrutura
* As rotas OBJECT retornam uma lista de objetos conforme os filtros (query) da sua rota
* As rotas TOOL tem query própria
* As rotas OBJECT e TOOL não compartilham os mesmos filtros
* O token é individual de cada usuário e herda suas permissões

## Endpoints disponíveis
* GET  => /object/geofiber/cto
* GET  => /object/geofiber/spliter
* GET  => /object/geofiber/spliter/via
* GET  => /object/carteira
* GET  => /object/cidade
* GET  => /object/cliente
* GET  => /object/cliente/contrato
* GET  => /object/cliente/contrato/ponto
* GET  => /object/cliente/contato
* GET  => /object/suporte/ticket
* GET  => /object/suporte/topico
* GET  => /object/cliente/contrato/cobranca

* GET  => /tool/cobranca/imprimir/{id}
* GET  => /tool/assinante/boleto
* GET  => /tool/assinante/info
* POST => /tool/assinante/login
* POST => /tool/assinante/liberar
* POST => /tool/assinante/ticket


## Query para rotas do tipo TOOL
* GET /tool/cobranca/imprimir/{codigo-do-boleto} (Impressão de um boleto pelo código)

* GET /tool/assinante/boleto (Segunda via do boleto, customizado)
  - doc: CPF/CNPJ
  - status: [vencido, vincendo, todos]
  - tipo: [carne, expandido, fatura, linha]
  
* GET /tool/assinante/info (Retonar dados, contratos, pontos, cobranças e chamados de um cliente)
  - id: Código do cliente
  - doc: CPF/CNPJ

* POST /tool/assinante/login (Tenta logar na central do assinante e retorna um objeto cliente caso sucesso)
  - username: CPF/CNPJ do cliente na central do assinante
  - password: Senha do cliente na central do assinante em MD5

* POST /tool/assinante/liberar (Libera os pontos de internet de um contrato por 3 dias)
  - id_contrato: Código do contrato a ser liberado
 
* POST /tool/assinante/ticket (Abre um ticket)
  - id_cliente: Código do cliente no sistema (Obrigatório)
  - id_ponto: Código do ponto de internet no sistema (Opcional)
  - id_topico:  Tópico de abertura do chamado (Obrigatório)
  - setor: Tag do setor que o chamado sera encaminhado (Obrigatório: TECNICO, COMERCIAL ou FINANCEIRO)
  - requisicao: Texto de requisição do assinante (Obrigatório)


## Query para rotas do tipo OBJECT
* limit: Limite de registros a serem exibidos 
	* -1 = Sem limite
	* default: 10

* offset: Número do registro para iniciar a contagem até o limite 
	* default: 0

* sort: Ordena do menor para o maior ou maior para menor
	* nome_do_acmpo:ASC Menor para maior (default)
	* nome_do_acmpo:DESC Maior para menor

* paginaton: Caso TRUE retorna no body o conteúdo, o offset o limit e a contagem de linhas.
	* default: False
	* Exexemplo: 
```
	[
	    "data" => [{nome: joao}],
	    "count" => 40,
	    "offset" => 5,
	    "limit" => 10
	]
```

* filter: Filtra o conteudo pelo campo, operador e comparador
	* Ex: data_vencimento:GT:2019-01-01 [AND] data_vencimento:LT:2019-12-31
	* nome_do_campo:operador:valor
	* operadores: [OR], [XOR], [AND] (Com colchetes)
	* comparadores: 
		* EQ: Igual
		* NOT: Diferente
		* LT: Menor que
		* LTE: Menor ou igual a
		* GT: Maior que
		* GTE: Maior ou igual a 
		* START: Inicia com
		* END: Termina com
		* CONTAINS: Contém
		* IN: Existe na lista [x,y,z] (Itens seprados por vírgulas)
		* NOTIN: Não exista na lista [x,y,z] (Itens seprados por vírgulas)
		* BTW: Entre o valor A e valor B (Dois itens separados por virgula)

