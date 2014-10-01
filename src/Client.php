<?php
namespace ANClient;

use ANClient\Auth\MemoryTokenCache;
use ANClient\Auth\TokenCacheInterface;
use GuzzleHttp\Message\ResponseInterface;

class Client
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
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var Auth\TokenCacheInterface
     */
    protected $tokenCache;

    /**
     * @param array $config
     * @param \GuzzleHttp\Client $httpClient
     * @param TokenCacheInterface $tokenCache
     * @throws \RuntimeException
     */
    public function __construct(array $config, \GuzzleHttp\Client $httpClient, TokenCacheInterface $tokenCache = null)
    {
        if (empty($config['endpoint']) || empty($config['auth'])) {
            throw new \RuntimeException('endpoint and auth elements must be specified in the config');
        }

        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->tokenCache = ($tokenCache) ? $tokenCache : new MemoryTokenCache();
    }

    /**
     * Get the underlying HTTP client
     *
     * @return Client|\GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param string $method
     * @param mixed $uri
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
    public function getAuthToken()
    {
        if ($token = $this->tokenCache->getToken()) {
            return $token;
        }

        $request = $this->httpClient->createRequest(
            'POST',
            $this->getUri('auth'),
            ['json' => ['auth'=>$this->getConfig('auth', [])]]
        );

        $result = $this->responseToResult($this->httpClient->send($request));
        $this->tokenCache->cacheToken($result['token']);

        return $result['token'];
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws \RuntimeException
     */
    public function responseToResult(ResponseInterface $response)
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