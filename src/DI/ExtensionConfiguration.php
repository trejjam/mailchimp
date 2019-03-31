<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\DI;

final class ExtensionConfiguration
{
	/** @var bool */
	public $findDataCenter = true;
	/** @var string */
	public $apiUrlTemplate = 'https://%s.api.mailchimp.com/%s/';
	/** @var string */
	public $apiUrl;
	/** @var string */
	public $apiKey;

	/**
	 * key => value; mame => mailchimp_list_id from https://<dc>.api.mailchimp.com/playground
	 *
	 * @var string[]
	 */
	public $lists = [];
	/** @var string[][]|int[][] */
	public $segments = [];
	/** @var HttpClientConfiguration */
	public $http;

	public function __construct()
	{
		$this->http = new HttpClientConfiguration;
	}
}
