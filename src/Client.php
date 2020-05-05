<?php

namespace Fbclit\DayforceApi;

use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /**
     * The base Dayforce API url.
     *
     * @var string
     */
    protected $url;

    /**
     * The unique Dayforce client / company name.
     *
     * @var string
     */
    protected $client;

    /**
     * The Dayforce API version.
     *
     * @var string
     */
    protected $version;

    /**
     * The Guzzle HTTP client configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Constructor.
     *
     * @param string $url
     * @param string $client
     * @param string $version
     */
    public function __construct($url, $client, $version = 'v1')
    {
        $this->url = $url;
        $this->client = $client;
        $this->version = $version;
    }

    /**
     * Create a new Dayforce client API instance.
     *
     * @param string $username
     * @param string $password
     *
     * @return Api
     */
    public function api($username, $password)
    {
        $config = array_merge_recursive($this->config, [
            'auth' => [$username, $password],
            'base_uri' => $this->getBaseUrl(),
        ]);

        return $this->getNewApiClient(
            $this->getNewHttpClient($config)
        );
    }

    /**
     * Set the Guzzle HTTP client config.
     *
     * @param array $config
     *
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the base URL of the content server API.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $url = rtrim($this->url, '/');

        return "{$url}/Api/{$this->client}/{$this->version}/";
    }

    /**
     * Get a new API client.
     *
     * @param ClientInterface $client The Guzzle HTTP client.
     *
     * @return Api
     */
    protected function getNewApiClient(ClientInterface $client)
    {
        return new Api($client);
    }

    /**
     * Get a new Guzzle HTTP client.
     *
     * @param array $config
     *
     * @return HttpClient
     */
    protected function getNewHttpClient($config = [])
    {
        return new HttpClient($config);
    }
}