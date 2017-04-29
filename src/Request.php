<?php

namespace Trejjam\MailChimp;

use Nette;
use Schematic\Entry;
use Trejjam;
use GuzzleHttp;

class Request
{
	const VERSION  = '3.0';
	const API_USER = 'apikey';

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

	public function __construct(GuzzleHttp\Client $httpClient, $apiUrl, $apiKey)
	{
		$this->httpClient = $httpClient;
		$this->apiUrl = $apiUrl;
		$this->apiKey = $apiKey;
	}

	/**
	 * @param string $method
	 * @param string $endpointPath
	 * @param string $endpointClass
	 * @param array  $requestOptions
	 *
	 * @return array|Entry|mixed
	 * @throws Nette\Utils\JsonException
	 */
	protected function makeRequest($method, $endpointPath, $endpointClass = NULL, array $requestOptions = [])
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
	 * @param string $endpointPath
	 * @param string $endpointClass
	 *
	 * @return array|Entry|mixed
	 * @throws Nette\Utils\JsonException
	 */
	public function get($endpointPath, $endpointClass = NULL)
	{
		return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass);
	}

	/**
	 * @param string $endpointPath
	 * @param array  $body
	 * @param string $endpointClass
	 *
	 * @return array|mixed|Entry
	 * @throws Nette\Utils\JsonException
	 */
	public function put($endpointPath, array $body, $endpointClass = NULL)
	{
		return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass, [
			GuzzleHttp\RequestOptions::BODY => Nette\Utils\Json::encode($body),
		]);
	}

	/**
	 * @param string $endpointPath
	 * @param array  $body
	 * @param string $endpointClass
	 *
	 * @return array|mixed|Entry
	 * @throws Nette\Utils\JsonException
	 */
	public function patch($endpointPath, array $body, $endpointClass = NULL)
	{
		return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass, [
			GuzzleHttp\RequestOptions::BODY => Nette\Utils\Json::encode($body),
		]);
	}

	/**
	 * @param string $endpointPath
	 * @param array  $body
	 * @param string $endpointClass
	 *
	 * @return array|mixed|Entry
	 * @throws Nette\Utils\JsonException
	 */
	public function post($endpointPath, array $body, $endpointClass = NULL)
	{
		return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass, [
			GuzzleHttp\RequestOptions::BODY => Nette\Utils\Json::encode($body),
		]);
	}

	/**
	 * @param string $endpointPath
	 * @param string $endpointClass
	 *
	 * @return array|Entry|mixed
	 * @throws Nette\Utils\JsonException
	 */
	public function delete($endpointPath, $endpointClass = NULL)
	{
		return $this->makeRequest(__FUNCTION__, $endpointPath, $endpointClass);
	}
}
