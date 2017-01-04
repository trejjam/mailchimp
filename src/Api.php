<?php

namespace Trejjam\MailChimp;

use Trejjam;
use GuzzleHttp;

class Api
{
	/**
	 * @var GuzzleHttp\Client
	 */
	private $httpClient;

	public function __construct(GuzzleHttp\Client $httpClient)
	{
		$this->httpClient = $httpClient;
	}
}
