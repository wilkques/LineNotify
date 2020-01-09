<?php

namespace Wilkques\LineNotify;

use Wilkques\LineNotify\Curl\HTTPClient\CurlHTTPClient;
use Wilkques\LineNotify\Enum\UrlEnum;

class Notify
{
    protected $clientId;
    protected $clientSecret;
    protected $curl;

    public function __construct($clientId, $clientSecret = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->curl = new CurlHTTPClient();
    }

    public function generateSubscribeUrl($args = ['state' => 'default'])
    {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->getCurrentUrl(),
            'scope' => 'notify'
        ];
        $params = array_merge($params, $args);
        return UrlEnum::AUTH_URL . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    public function requestToken($code, $redirect_uri = null)
    {
        if (!$this->clientSecret) {
            throw new \UnexpectedValueException('ClientSecret is required');
        }
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $redirect_uri ? $redirect_uri : $this->getCurrentUrl(),
            'code' => $code,
        ];

        $response = $this->curl->post(UrlEnum::TOKEN_URL, $params, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $params = $response->getJSONDecodedBody();

        if ($params['status'] != 200) {
            throw new \Exception('Request token error::' . $params['message']);
        }
        return $params['access_token'];
    }

    private function getCurrentUrl()
    {
        $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        return $http . "://$_SERVER[HTTP_HOST]$uri";
    }

    public static function sendMessage($token, Message $message)
    {
        $curl = new CurlHTTPClient();
        $curl->setToken($token);
        return $curl->post(UrlEnum::NOTIFY_URL, $message->build(), [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
    }
}
