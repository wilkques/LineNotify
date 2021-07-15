<?php

namespace Wilkques\LineNotify;

use Wilkques\HttpClient\Http;
use Wilkques\LineNotify\Enum\UrlEnum;

/**
 * @method static static clientId() set client id
 * @method static static clientSecret() set client secret
 * @method static static token() set access token
 * @method static \Wilkques\HttpClient\Http asForm()
 * @method static \Wilkques\HttpClient\Http withToken(string $token, string $type = 'Bearer')
 * @method static \Wilkques\HttpClient\Response post(string $url, array $data, array $query = null)
 * @method static \Wilkques\HttpClient\Response throw(callable $callable = null)
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
    /** @var Http */
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
     * @return static
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
                        \Wilkques\HttpClient\Response $response,
                        \Wilkques\HttpClient\Exception\RequestException $exception
                    ) {
                        if ($response->failed()) {
                            throw new \Exception('Request error::' . $exception->getMessage());
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
     * @return static
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
     * @return Http
     */
    public function getClient()
    {
        return $this->client = $this->client ?? new Http;
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return static|Http
     */
    public function __call(string $method, array $arguments)
    {
        $method = ltrim(trim($method));

        if (in_array($method, $this->methods)) {
            $method = 'set' . ucfirst($method);

            return $this->{$method}(...$arguments);
        }

        return $this->getClient()->{$method}(...$arguments);
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
