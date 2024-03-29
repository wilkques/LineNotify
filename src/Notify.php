<?php

namespace Wilkques\LineNotify;

use Wilkques\Http\Client;
use Wilkques\LineNotify\Enum\UrlEnum;

/**
 * @method static static clientId() set client id
 * @method static static clientSecret() set client secret
 * @method static static token() set access token
 * @method \Wilkques\Http\Client asForm()
 * @method \Wilkques\Http\Client withToken(string $token, string $type = 'Bearer')
 * @method \Wilkques\Http\Client post(string $url, array $data, array $query = null)
 * @method \Wilkques\Http\Response throw(callable $callable = null)
 */
class Notify
{
    /** @var string */
    protected $clientId;
    /** @var string */
    protected $clientSecret;
    /** @var array */
    protected $methods = [
        'clientId', 'clientSecret', 'token'
    ];
    /** @var string */
    protected $token;
    /** @var Client */
    protected $client;

    /**
     * @param string|null $clientId
     * @param string|null $clientSecret
     */
    public function __construct(string $clientId = null, string $clientSecret = null)
    {
        $this->setClientId($clientId)->setClientSecret($clientSecret);
    }

    /**
     * @param string|null $clientId
     * 
     * @return static
     */
    public function setClientId(string $clientId = null)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string|null $clientSecret
     * 
     * @return static
     */
    public function setClientSecret(string $clientSecret = null)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @throws \UnexpectedValueException
     * 
     * @return static
     */
    private function checkClientId()
    {
        if (!$this->getClientId()) {
            throw new \UnexpectedValueException('ClientId is required');
        }

        return $this;
    }

    /**
     * @throws \UnexpectedValueException
     * 
     * @return static
     */
    private function checkClientSecret()
    {
        if (!$this->getClientSecret()) {
            throw new \UnexpectedValueException('ClientSecret is required');
        }

        return $this;
    }

    /**
     * @param array $args
     * 
     * @return string
     */
    public function generateSubscribeUrl(array $args = ['state' => 'default'])
    {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->checkClientId()->getClientId(),
            'redirect_uri' => $this->getCurrentUrl(),
            'scope' => 'notify'
        ];

        $params = array_merge($params, $args);

        return UrlEnum::AUTH_URL . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @param string $code
     * @param string $redirectUri
     * 
     * @return Response
     */
    public function requestToken(string $code, string $redirectUri = null)
    {
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->checkClientId()->getClientId(),
            'client_secret' => $this->checkClientSecret()->getClientSecret(),
            'redirect_uri' => $redirectUri ? $redirectUri : $this->getCurrentUrl(),
            'code' => $code,
        ];

        return new Response(
            $this,
            $this->asForm()->post(UrlEnum::TOKEN_URL, $params)
                ->throw(
                    function (
                        \Wilkques\Http\Response $response,
                        \Wilkques\Http\Exceptions\RequestException $exception
                    ) {
                        if ($response->failed()) {
                            throw $exception;
                        }
                    }
                )
        );
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * 
     * @return static
     */
    public function setToken(string $token = null)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    private function getCurrentUrl()
    {
        $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

        return "$http://{$_SERVER['HTTP_HOST']}$uri";
    }

    /**
     * @param Message $message
     * 
     * @return Response
     */
    public function sendMessage(Message $message)
    {
        if (!$token = $this->getToken()) throw new \UnexpectedValueException('Token is required');

        return new Response(
            $this,
            $this->withToken($token)->asForm()
                ->post(UrlEnum::NOTIFY_URL, $message->getOptions())
        );
    }

    /**
     * @param Client $client
     * 
     * @return static
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return Client
     */
    public function newClient()
    {
        return $this->getClient() ?? $this->setClient(new Client);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return static|Client
     */
    public function __call(string $method, array $arguments)
    {
        $method = ltrim(trim($method));

        if (in_array($method, $this->methods)) {
            $method = 'set' . ucfirst($method);

            return $this->{$method}(...$arguments);
        }

        return $this->newClient()->{$method}(...$arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return static
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return (new static)->{$method}(...$arguments);
    }
}
