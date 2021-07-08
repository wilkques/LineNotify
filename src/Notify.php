<?php

namespace Wilkques\LineNotify;

use Wilkques\HttpClient\Exception\RequestException;
use Wilkques\HttpClient\Http;
use Wilkques\HttpClient\Response;
use Wilkques\LineNotify\Enum\UrlEnum;

/**
 * @method static clientId() set client id
 * @method static clientSecret() set client secret
 * @method static token() set access token
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
    /** @var Response */
    protected $response;
    /** @var string */
    protected $token;

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
     * @param Response $response
     * 
     * @return static
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return Response
     */
    public function response()
    {
        return $this->response;
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

        $this->setResponse(
            Http::asForm()->post(UrlEnum::TOKEN_URL, $params)
                ->throw(function (Response $response, RequestException $exception) {
                    if ($response->failed()) {
                        throw new \Exception('Request token error::' . $exception->getMessage());
                    }
                })
        );

        return $this;
    }

    /**
     * @return array
     */
    public function json()
    {
        return $this->response()->json();
    }

    /**
     * @return mixed
     */
    public function getResponseByKey(string $key)
    {
        return $this->json()[$key];
    }

    /**
     * @return string|null
     */
    public function accessToken()
    {
        return $this->getResponseByKey('access_token') ?? null;
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
        $this->token = $token ?? $this->token();

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
     * @return \Wilkques\LineNotify\Curl\Response
     */
    public function sendMessage(Message $message)
    {
        if (!$token = $this->getToken()) throw new \UnexpectedValueException('Token is required');

        return Http::withToken($token)->asForm()->post(UrlEnum::NOTIFY_URL, $message->build());
    }

    public function __call($method, $arguments)
    {
        if (in_array($method, $this->methods)) {
            $method = 'set' . ucfirst($method);
        }

        return $this->{$method}(...$arguments);
    }

    public static function __callStatic($method, $arguments)
    {
        return (new static)->{$method}(...$arguments);
    }
}
