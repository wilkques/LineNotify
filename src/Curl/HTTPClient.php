<?php

namespace Wilkques\LineNotify\Curl;

/**
 * The interface that represents HTTP client API.
 *
 * If you want to switch using HTTP client, please implement this.
 */
interface HTTPClient
{
    /**
     * Sends GET request to API.
     *
     * @param string $url Request URL.
     * @param array $data URL parameters.
     * @param array $headers
     * @return Response Response of API request.
     */
    public function get($url, array $data = [], array $headers = []);

    /**
     * Sends POST request to API.
     *
     * @param string $url Request URL.
     * @param array $data Request body.
     * @param array|null $headers Request headers.
     * @return Response Response of API request.
     */
    public function post($url, array $data, array $headers = null);

    /**
     * Sends DELETE request to API.
     *
     * @param string $url Request URL.
     * @return Response Response of API request.
     */
    public function delete($url);
}
