<?php

namespace Wilkques\LineNotify;

use Wilkques\HttpClient\Response as HttpClientResponse;
use Wilkques\LineNotify\Exceptions\RequestException;

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
     * @param string|null $token
     * 
     * @return Notify
     */
    public function setToken(string $token = null)
    {
        return $this->getNotify()->setToken($token ?? $this->accessToken());
    }

    /**
     * @return RequestException
     */
    public function getThrow()
    {
        return new RequestException($this);
    }

    /**
     * @param callable|null $callback
     * 
     * @throws \Wilkques\HttpClient\Exception\RequestException|RequestException
     * 
     * @return static
     */
    public function throw(callable $callback = null)
    {
        $response = $this->getResponse();

        if ($response->failed()) {
            if ($callback) {
                throw $this->callableReturnCheck($callback($this, $this->getThrow()));
            }

            throw $this->getThrow();
        }

        return $this;
    }

    /**
     * @param mixed $callable
     * 
     * @throws UnexpectedValueException
     * 
     * @return mixed
     */
    protected function callableReturnCheck($callable = null)
    {
        if (is_null($callable)) return $this->getThrow();
        else if (!is_object($callable)) throw new \UnexpectedValueException("throw return must be Exception Object");

        return $callable;
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
