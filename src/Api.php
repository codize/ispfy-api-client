<?php

namespace Ispfy;

class Api
{
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';


    /** DEPRECIADO, UTLIZE A STRING DIRETAMENTE *********************************************/
    const ENDPOINT_GEOFIBER_PROJETO = '/object/geofiber/projeto';
    const ENDPOINT_GEOFIBER_CTO = '/object/geofiber/cto';
    const ENDPOINT_GEOFIBER_SPLITER = '/object/geofiber/spliter';
    const ENDPOINT_GEOFIBER_SPLITER_VIA = '/object/geofiber/spliter/via';
    const ENDPOINT_GEOFIBER_CIDADE = '/object/cidade';
    const ENDPOINT_CLIENTE = '/object/cliente';
    const ENDPOINT_TICKET = '/object/suporte/ticket';
    const ENDPOINT_CLIENTE_CONTRATO = '/object/cliente/contrato';
    const ENDPOINT_CLIENTE_PONTO = '/object/cliente/contrato/ponto';
    const ENDPOINT_CLIENTE_CONTATOO = '/object/cliente/contato';
    const ENDPOINT_CLIENTE_COBRANCA = '/object/cliente/contrato/cobranca';
    const ENDPOINT_CLIENTE_HISTORICO = '/object/cliente/historico';
    const ENDPOINT_ASSINANTE_LOGIN = '/tool/assinante/login';
    const ENDPOINT_CLIENTE_BOLETO_ABERTO = '/tool/cliente/cobranca/abertas/boleto';
    /****************************************************************************************/


    /**
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $token;

    /**
     * @var mixed
     */
    private $curl;

    /**
     * @var mixed
     */
    private $resultBody;

    /**
     * @var mixed
     */
    private $resultHeader;

    /**
     * @var mixed
     */
    private $resultInfo;

    /**
     * @param string $target URL/IP + PORTA do servidor 8020 para HTTP e 8043 para HTTPS
     * @param string $token Codigo de autenticacao
     * @param int $timeout Tempo de resposa ate desistir
     */
    public function __construct($target, $token, $timeout = 10)
    {
        $this->target = $target;
        $this->token = $token;

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
    }

    /**
     * @param bool $verify Ativa ou desativa a verificao de certificado,
     * false caso nao tenha certificado proprio
     * @return self
     */
    public function setVerifySSL($verify)
    {
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, $verify);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $verify);

        return $this;
    }

    /**
     * @param string $endpoint End point da requisicao. Ex: /object/cliente
     * @param string $method Metodo http [GET, POST, PUT, DELETE]
     * @param array $query Query utilizada para filtro, ordenacao, paginacao, etc...
     * @param array $body Dados a serem enviados para o servidor, utilizado apenas em requisicos POST e PUT
     * @return 
     */
    public function request($endpoint, $method, $query = [], $body = [])
    {
        //Make query
        $query = $query ? "?" . http_build_query($query) : "";

        //Make header
        $header = array(
            "Token: {$this->token}",
        );

        //Set curl parameters
        curl_setopt($this->curl, CURLOPT_URL, "{$this->target}/api{$endpoint}{$query}");
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);

        //Assign content
        $response = curl_exec($this->curl);
        $info = curl_getinfo($this->curl);

        //Load erros if exists
        $error = curl_error($this->curl);
        $errorno = curl_errno($this->curl);

        //Validate response
        if (in_array($errorno, array(CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED))) {
            die("Erro: Falha ao tentar conectar com gateway");
        } else {
            if ($error != "") {
                die("Erro: {$error}");
            }
        }

        //Decode header string
        $headerData = explode(PHP_EOL, substr($response, 0, $info['header_size']));
        foreach ($headerData as $key => $row) {
            if (!trim($row)) {
                continue;
            }
            if ($key == 0) {
                $header["code"] = explode(" ", $row)[1];
            } else {
                $row = explode(": ", $row);
                $header[$row[0]] = $row[1];
            }
        }

        //Decode body JSON data
        $body = substr($response, $info['header_size'], strlen($response));

        //Check return code
        if (!in_array(substr($header["code"], 0, 1), ['2', '3'])) {
            die("Recusa: {$body}");
        };

        //Assign result values
        $this->resultBody = $body;
        $this->resultHeader = $header;
        $this->resultInfo = $info;
    }

    /**
     * @param string $endpoint End point da requisicao. Ex: /object/cliente
     * @param mixed $query Query utilizada para filtro, ordenacao, paginacao, etc...
     * @return mixed Retorna apenas o body da requisicao
     */
    public function get($endpoint, $query = [])
    {
        $this->request($endpoint, self::HTTP_METHOD_GET, $query, []);
        return $this->getBodyDecoded();
    }

    /**
     * @param string $endpoint End point da requisicao. Ex: /object/cliente
     * @param mixed $body Dados a serem enviados para o servidor, utilizado apenas em requisicos POST e PUT
     * @return mixed Retorna apenas o body da requisicao
     */
    public function post($endpoint, $body = [])
    {
        $this->request($endpoint, self::HTTP_METHOD_POST, [], $body);
        return $this->getBodyDecoded();
    }

    /**
     * @param string $endpoint End point da requisicao. Ex: /object/cliente
     * @param mixed $body Dados a serem enviados para o servidor, utilizado apenas em requisicos POST e PUT
     * @return mixed Retorna apenas o body da requisicao
     */
    public function put($endpoint, $body = [])
    {
        $this->request($endpoint, self::HTTP_METHOD_PUT, [], $body);
        return $this->getBodyDecoded();
    }

    /**
     * @param string $endpoint End point da requisicao. Ex: /object/cliente
     * @return mixed Retorna apenas o body da requisicao
     */
    public function delete($endpoint)
    {
        $this->request($endpoint, self::HTTP_METHOD_DELETE);
        return $this->getBodyDecoded();
    }

    /**
     * @return mixed Retorna apenas o header da requisicao
     */
    public function getHeader()
    {
        return $this->resultHeader;
    }

    /**
     * @return mixed Retona apenas o body da requisicao
     */
    public function getBodyDecoded()
    {
        return @json_decode($this->resultBody, true);
    }

    /**
     * @return mixed Retona apenas o body da requisicao
     */
    public function getBodyContent()
    {
        return $this->resultBody;
    }

    /**
     * @return mixed Retorna apenas os detalhes de processamento da requisicao
     */
    public function getInfo()
    {
        return $this->resultInfo;
    }
}
