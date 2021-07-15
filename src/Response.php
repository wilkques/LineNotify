<?php

namespace Wilkques\LineNotify;

use Wilkques\HttpClient\Response as HttpClientResponse;

class Response
{
    /** @var Notify */
    protected $notify;
    /** @var HttpClientResponse */
    protected $response;

    /**
     * @param Notify $notify
     * @param HttpClientResponse $response
     */
    public function __construct(Notify $notify, HttpClientResponse $response)
    {
        $this->setNotify($notify)->setResponse($response);
    }

    /**
     * @param Notify $notify
     * 
     * @return static
     */
    public function setNotify(Notify $notify)
    {
        $this->notify = $notify;

        return $this;
    }

    /**
     * @return Notify
     */
    public function getNotify()
    {
        return $this->notify;
    }

    /**
     * @param HttpClientResponse $response
     * 
     * @return static
     */
    public function setResponse(HttpClientResponse $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return HttpClientResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function json()
    {
        return $this->getResponse()->json();
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
     * @param string|null $token
     * 
     * @return Notify
     */
    public function setToken(string $token = null)
    {
        return $this->getNotify()->setToken($token ?? $this->accessToken());
    }
}