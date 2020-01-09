<?php

namespace Wilkques\LineNotify\Curl\HTTPClient;

use Wilkques\LineNotify\Exception\CurlExecutionException;
use Wilkques\LineNotify\Curl\HTTPClient;
use Wilkques\LineNotify\Curl\Response;

/**
 * Class CurlHTTPClient.
 *
 * A HTTPClient that uses cURL.
 */
class CurlHTTPClient implements HTTPClient
{
    /** @var array */
    private $authHeaders = [];

    /**
     * CurlHTTPClient constructor.
     */
    public function __construct()
    {

    }

    public function setToken($channelToken)
    {
        $this->authHeaders = [
            "Authorization: Bearer $channelToken",
        ];
    }

    /**
     * Sends GET request to API.
     *
     * @param string $url Request URL.
     * @param array $data Request body
     * @param array $headers Request headers.
     * @return Response Response of API request.
     * @throws CurlExecutionException
     */
    public function get($url, array $data = [], array $headers = [])
    {
        if ($data) {
            $url .= '?' . http_build_query($data);
        }
        return $this->sendRequest('GET', $url, $headers);
    }

    /**
     * Sends POST request to API.
     *
     * @param string $url Request URL.
     * @param array $data Request body or resource path.
     * @param array|null $headers Request headers.
     * @return Response Response of API request.
     * @throws CurlExecutionException
     */
    public function post($url, array $data, array $headers = null)
    {
        $headers = is_null($headers) ? ['Content-Type: application/json; charset=utf-8'] : $headers;
        return $this->sendRequest('POST', $url, $headers, $data);
    }

    /**
     * Sends DELETE request to API.
     *
     * @param string $url Request URL.
     * @return Response Response of API request.
     * @throws CurlExecutionException
     */
    public function delete($url)
    {
        return $this->sendRequest('DELETE', $url, [], []);
    }

    /**
     * @param string $method
     * @param array $headers
     * @param string|null $reqBody
     * @return array cUrl options
     */
    private function getOptions($method, $headers, $reqBody)
    {
        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_HEADER => true,
        ];
        if ($method === 'POST') {
            if (is_null($reqBody)) {
                // Rel: https://github.com/line/line-bot-sdk-php/issues/35
                $options[CURLOPT_HTTPHEADER][] = 'Content-Length: 0';
            } else {
                if (isset($reqBody['__file']) && isset($reqBody['__type'])) {
                    $options[CURLOPT_PUT] = true;
                    $options[CURLOPT_INFILE] = fopen($reqBody['__file'], 'r');
                    $options[CURLOPT_INFILESIZE] = filesize($reqBody['__file']);
                } elseif (in_array('Content-Type: application/x-www-form-urlencoded', $headers)) {
                    $options[CURLOPT_POST] = true;
                    $options[CURLOPT_POSTFIELDS] = http_build_query($reqBody);
                } elseif (!empty($reqBody)) {
                    $options[CURLOPT_POST] = true;
                    $options[CURLOPT_POSTFIELDS] = json_encode($reqBody);
                } else {
                    $options[CURLOPT_POST] = true;
                    $options[CURLOPT_POSTFIELDS] = $reqBody;
                }
            }
        }
        return $options;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $additionalHeader
     * @param string|null $reqBody
     * @return Response
     * @throws CurlExecutionException
     */
    private function sendRequest($method, $url, array $additionalHeader, $reqBody = null)
    {
        $curl = new Curl($url);

        $headers = array_merge($this->authHeaders, $additionalHeader);

        $options = $this->getOptions($method, $headers, $reqBody);
        $curl->setoptArray($options);

        $result = $curl->exec();

        if ($curl->errno()) {
            throw new CurlExecutionException($curl->error());
        }

        $info = $curl->getinfo();
        $httpStatus = $info['http_code'];

        $responseHeaderSize = $info['header_size'];

        $responseHeaderStr = substr($result, 0, $responseHeaderSize);
        $responseHeaders = [];
        foreach (explode("\r\n", $responseHeaderStr) as $responseHeader) {
            $kv = explode(':', $responseHeader, 2);
            if (count($kv) === 2) {
                $responseHeaders[$kv[0]] = trim($kv[1]);
            }
        }

        $body = substr($result, $responseHeaderSize);

        if (isset($options[CURLOPT_INFILE])) {
            fclose($options[CURLOPT_INFILE]);
        }

        return new Response($httpStatus, $body, $responseHeaders);
    }
}
