<?php
namespace ANClient;

use ANClient\Http\MemoryAuthCache;
use ANClient\Http\AuthCacheInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;

class Http
{
    /**
     * @var array
     */
    protected $config = array(
        'endpoint' => 'http://sand.api.appnexus.com',
        'auth' => array(
            'username' => null,
            'password' => null
        )
    );

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var Http\AuthCacheInterface
     */
    protected $authCache;

    /**
     * @param array $config
     * @param Client $httpClient
     * @param AuthCacheInterface $authCache
     */
    public function __construct(array $config, Client $httpClient, AuthCacheInterface $authCache = null)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->authCache = ($authCache) ? $authCache : new MemoryAuthCache();
    }

    /**
     * @param string $method
     * @param null $uri
     * @param array $options
     * @return mixed
     */
    public function dispatch($method = 'GET', $uri = null, array $options = [])
    {
        $request = $this->httpClient->createRequest($method, $this->getUri($uri), $options);
        $request->setHeader('Authorization', $this->getAuthToken());

        $response = $this->httpClient->send($request);

        //check for success and return only the body
        return $this->responseToResult($response);
    }

    /**
     * @return mixed
     */
    protected function getAuthToken()
    {
        if ($token = $this->authCache->getToken()) {
            return $token;
        }

        $request = $this->httpClient->createRequest(
            'POST',
            $this->getUri('auth'),
            ['json' => ['auth'=>$this->getConfig('auth', [])]]
        );

        $result = $this->responseToResult($this->httpClient->send($request));
        $this->authCache->cacheToken($result['token']);

        return $result['token'];
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws \RuntimeException
     */
    protected function responseToResult(ResponseInterface $response)
    {
        $body = $response->json();
        $status = $response->getStatusCode();
        $error = isset($body['response']['error']) ? $body['response']['error'] : 'No error message provided';

        if ((int)$status !== 200) {
            throw new \RuntimeException("Non 200 Status returned ($status): $error");
        }

        if (!isset($body['response'])) {
            throw new \RuntimeException("Body did not contain a response ($status): $error");
        }

        if (isset($body['response']['error'])) {
            throw new \RuntimeException("Response contained an error ($status): $error");
        }

        return $body['response'];
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    protected function getConfig($key, $default = null)
    {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

    /**
     * Cat the base uri and given resource into a clean uri
     *
     * @param $resource
     * @return string
     */
    protected function getUri($resource)
    {
        return rtrim($this->getConfig('endpoint'), '\\/ ').'/'.trim($resource, '\\/ ');
    }
} 