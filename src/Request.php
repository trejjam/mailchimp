<?php

namespace Trejjam\MailChimp;

use Nette;
use Schematic\Entry;
use Trejjam;
use GuzzleHttp;

final class Request
{
    public const VERSION = '3.0';
    public const API_USER = 'apikey';

    /**
     * @var GuzzleHttp\Client
     */
    private $httpClient;
    /**
     * @var string
     */
    private $apiUrl;
    /**
     * @var string
     */
    private $apiKey;

    public function __construct(
        GuzzleHttp\Client $httpClient,
        string $apiUrl,
        string $apiKey
    ) {
        $this->httpClient = $httpClient;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    /**
     * @return array|Entry|mixed
     * @throws Nette\Utils\JsonException
     */
    protected function makeRequest(string $method, string $endpointPath, ?string $endpointClass = null, array $requestOptions = [])
    {
        $mergedRequestOptions = array_merge_recursive(
            [
                GuzzleHttp\RequestOptions::AUTH => [self::API_USER, $this->apiKey],
            ], $requestOptions
        );

        $response = $this->httpClient->request(
            $method, $this->apiUrl . $endpointPath, $mergedRequestOptions
        );

        if ($response->getStatusCode() !== 200) {
            throw (new Trejjam\MailChimp\Exception\RequestException(
                $response->getReasonPhrase(),
                $response->getStatusCode()
            ))->setResponse($response);
        }

        $returnArray = Nette\Utils\Json::decode($response->getBody()->getContents(), Nette\Utils\Json::FORCE_ARRAY);

        if (empty($endpointClass)) {
            return $returnArray;
        }

        return new $endpointClass($returnArray);
    }

    /**
     * @return array|Entry|mixed
     * @throws Nette\Utils\JsonException
     */
    public function get(string $endpointPath, ?string $endpointClass = null)
    {
        return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass);
    }

    /**
     * @return array|mixed|Entry
     * @throws Nette\Utils\JsonException
     */
    public function put(string $endpointPath, array $body, ?string $endpointClass = null)
    {
        return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass, [
            GuzzleHttp\RequestOptions::BODY => Nette\Utils\Json::encode($body),
        ]);
    }

    /**
     * @return array|mixed|Entry
     * @throws Nette\Utils\JsonException
     */
    public function patch(string $endpointPath, array $body, ?string $endpointClass = null)
    {
        return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass, [
            GuzzleHttp\RequestOptions::BODY => Nette\Utils\Json::encode($body),
        ]);
    }

    /**
     * @return array|mixed|Entry
     * @throws Nette\Utils\JsonException
     */
    public function post(string $endpointPath, array $body, ?string $endpointClass = null)
    {
        return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass, [
            GuzzleHttp\RequestOptions::BODY => Nette\Utils\Json::encode($body),
        ]);
    }

    /**
     * @return array|Entry|mixed
     * @throws Nette\Utils\JsonException
     */
    public function delete(string $endpointPath, ?string $endpointClass = null)
    {
        return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass);
    }
}
