<?php

namespace Wilkques\LineNotify;

use Wilkques\HttpClient\Response as HttpClientResponse;

/**
 * @method static int status()
 * @method static string body()
 * @method static array json()
 * @method static array headers()
 * @method static string|null header()
 * @method static boolean ok()
 * @method static boolean redirect()
 * @method static boolean successful()
 * @method static boolean failed()
 * @method static boolean clientError()
 * @method static boolean serverError()
 * @method static throws throw(callable $callback = null)
 */
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
     * @param string $method
     * @param array $arguments
     * 
     * @return HttpClientResponse
     */
    public function __call(string $method, array $arguments)
    {
        return $this->getResponse()->{$method}(...$arguments);
    }
}
