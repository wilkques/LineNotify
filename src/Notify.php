<?php

namespace Wilkques\LineNotify;

use Wilkques\HttpClient\Http;
use Wilkques\LineNotify\Enum\UrlEnum;

class Notify
{
    /** @var string */
    protected $clientId;
    /** @var string */
    protected $clientSecret;
    
    public function __construct(string $clientId, string $clientSecret = null)
    {
        $this->setClientId($clientId)->setClientSecret($clientSecret);
    }

    /**
     * @param string $clientId
     * 
     * @return static
     */
    public function setClientId(string $clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientSecret
     * 
     * @return static
     */
    public function setClientSecret(string $clientSecret)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
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
            'client_id' => $this->getClientId(),
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
     * @return string
     */
    public function requestToken(string $code, string $redirectUri = null)
    {
        if (!$this->clientSecret) {
            throw new \UnexpectedValueException('ClientSecret is required');
        }

        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'redirect_uri' => $redirectUri ? $redirectUri : $this->getCurrentUrl(),
            'code' => $code,
        ];

        $response = Http::asForm()->post(UrlEnum::TOKEN_URL, $params);

        $params = $response->getJSONDecodedBody();

        if ($params['status'] != 200) {
            throw new \Exception('Request token error::' . $params['message']);
        }

        return $params['access_token'];
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
     * @param string $token
     * @param Message $message
     * 
     * @return \Wilkques\LineNotify\Curl\Response
     */
    public static function sendMessage(string $token, Message $message)
    {
        return Http::withToken($token)->asForm()->post(UrlEnum::NOTIFY_URL, $message->build());
    }
}
