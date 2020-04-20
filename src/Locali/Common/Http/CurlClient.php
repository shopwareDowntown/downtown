<?php
declare(strict_types=1);

namespace Shopware\Production\Locali\Common\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CurlClient
 * @package Shopware\Production\Locali\Common\Http
 */
class CurlClient
{
    /**
     * @param string $url
     * @param array $headers
     * @return Response
     */
    public function get(string $url, array $headers = [])
    {
        return $this->call($url, Request::METHOD_GET, $headers);
    }

    /**
     * @param string $url
     * @param null $data
     * @param array $headers
     * @return Response
     */
    public function post(string $url, $data = null, array $headers = [])
    {
        return $this->call($url, Request::METHOD_POST, $headers, $data);
    }

    /**
     * @param string $url
     * @param null $data
     * @param array $headers
     * @return Response
     */
    public function patch(string $url, $data = null, array $headers = [])
    {
        return $this->call($url, Request::METHOD_PATCH, $headers, $data);
    }

    /**
     * @param string $url
     * @param null $data
     * @param array $headers
     * @return Response
     */
    public function put(string $url, $data = null, array $headers = [])
    {
        return $this->call($url, Request::METHOD_PUT, $headers, $data);
    }

    /**
     * @param string $url
     * @param null $data
     * @param array $headers
     * @return Response
     */
    public function delete(string $url, $data = null, array $headers = [])
    {
        return $this->call($url, Request::METHOD_DELETE, $headers, $data);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param null $data
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function call(string $url, string $method = Request::METHOD_GET, array $headers = [], $data = null)
    {
        $client = new Client();

        return $client->request($method, $url, [
            'headers' => $headers,
            'json'    => $data,
        ]);
    }
}
