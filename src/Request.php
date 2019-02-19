<?php
declare(strict_types=1);

namespace Trejjam\MailChimp;

use Schematic\Entry;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Trejjam\MailChimp\Exception\RequestException;

final class Request
{
    public const VERSION = '3.0';
    public const API_USER = 'apikey';

    /**
     * @var Client
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
        Client $httpClient,
        string $apiUrl,
        string $apiKey
    ) {
        $this->httpClient = $httpClient;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    /**
     * @return array|Entry|mixed
     * @throws JsonException
     */
    protected function makeRequest(
        string $method,
        string $endpointPath,
        ?string $endpointClass = null,
        array $requestOptions = [],
        ?PaginationOption $paginationOption = null
    ) {
        $mergedRequestOptions = array_merge_recursive(
            [
                RequestOptions::AUTH => [self::API_USER, $this->apiKey],
            ], $requestOptions
        );

        if ($paginationOption !== null) {
            $endpointPath .= "?offset={$paginationOption->getOffset()}&count={$paginationOption->getCount()}";
        }

        $response = $this->httpClient->request(
            $method, $this->apiUrl . $endpointPath, $mergedRequestOptions
        );

        if ($response->getStatusCode() !== 200) {
            throw (new RequestException(
                $response->getReasonPhrase(),
                $response->getStatusCode()
            ))->setResponse($response);
        }

        $returnArray = Json::decode($response->getBody()->getContents(), Json::FORCE_ARRAY);

        if (empty($endpointClass)) {
            return $returnArray;
        }

        return new $endpointClass($returnArray);
    }

    /**
     * @return array|Entry|mixed
     * @throws JsonException
     */
    public function get(string $endpointPath, ?string $endpointClass = null, ?PaginationOption $paginationOption = null)
    {
        return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass, [], $paginationOption);
    }

    /**
     * @return array|mixed|Entry
     * @throws JsonException
     */
    public function put(string $endpointPath, array $body, ?string $endpointClass = null)
    {
        return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass, [
            RequestOptions::BODY => Json::encode($body),
        ]);
    }

    /**
     * @return array|mixed|Entry
     * @throws JsonException
     */
    public function patch(string $endpointPath, array $body, ?string $endpointClass = null)
    {
        return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass, [
            RequestOptions::BODY => Json::encode($body),
        ]);
    }

    /**
     * @return array|mixed|Entry
     * @throws JsonException
     */
    public function post(string $endpointPath, array $body, ?string $endpointClass = null)
    {
        return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass, [
            RequestOptions::BODY => Json::encode($body),
        ]);
    }

    /**
     * @return array|Entry|mixed
     * @throws JsonException
     */
    public function delete(string $endpointPath, ?string $endpointClass = null)
    {
        return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass);
    }
}
