<?php

namespace Api;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\ClientErrorResponseException;

class Client implements ClientInterface
{
    protected array $resources = [];
    protected array $headers = [];
    protected string $baseUrl;

    protected GuzzleClient $httpClient;
    protected ClientInterface $root;

    public function __construct(string $token, string $baseUrl)
    {
        $this->httpClient = new GuzzleClient();
        $this->root = new ClientItem($this, '');

        $this->setBaseUrl($baseUrl);
        $this->setToken($token);
    }

    public function __get($name): ClientItem
    {
        $this->resources[$name] = $this->root->__get($name);
        return $this->resources[$name];
    }

    public function __call($name, $args): void
    {
        $this->{$name}->__call('', $args);
    }

    public function get(string $path, array $params = []): mixed
    {
        return $this->request('get', $path, ['headers' => $this->basicHeaders(), 'query' => $params]);
    }

    public function post(string $path, array $params = []): mixed
    {
        return $this->request(
            'post',
            $path,
            ['headers' => $this->basicHeaders(), 'json' => $params]
        );
    }

    public function execute(string $uri, array $params = [], string $method = 'get'): mixed
    {
        return $this->$method($uri, $params);
    }

    protected function basicHeaders(array $additionalHeaders = []): array
    {
        return array_merge($this->headers, $additionalHeaders);
    }

    protected function request(string $method, string $uri, array $params): mixed
    {
        try {
            $response = $this->httpClient->$method($this->baseUrl . $uri, $params);
            return $response->getBody();
        } catch (ClientErrorResponseException $e) {
            //$this->log('Something went wrong on fetching data:')
            //$this->log($e->getResponse())
        }

        return [];
    }

    private function setBaseUrl(string $baseUrl): void
    {
        $baseUrl = trim($baseUrl);
        $baseUrl = trim($baseUrl, '/');

        if (!str_starts_with($baseUrl, 'http') || !str_starts_with($baseUrl, 'https')) {
            $baseUrl = 'https://' . $baseUrl;
        }
        $this->baseUrl = $baseUrl;
    }

    private function setToken(string $token): void
    {
        $this->headers['token'] = $token;
    }
}