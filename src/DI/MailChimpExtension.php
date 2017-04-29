<?php

namespace Trejjam\MailChimp\DI;

use GuzzleHttp;
use Composer;
use Nette;
use Trejjam;

/**
 * Class MailChimpExtension
 *
 * Inspired by
 * - https://github.com/zbycz/mailchimp-v3-php/blob/master/MailchimpService.php
 * - https://github.com/Kdyby/CsobPaymentGateway
 *
 * @package Trejjam\Email\DI
 */
class MailChimpExtension extends Trejjam\BaseExtension\DI\BaseExtension
{
	protected $default = [
		'findDataCenter' => TRUE,
		'apiUrl'         => 'https://%s.api.mailchimp.com/%s/',
		'apiKey'         => NULL,
		'lists'          => [],  // self named ids from https://<dc>.api.mailchimp.com/playground/
		'segments'       => [],
		'http'           => [
			'client' => [
				'verify' => NULL, //NULL will be filled by Composer CA
			],
		],
	];

	protected $classesDefinition = [
		'http.client' => GuzzleHttp\Client::class,
		'request'     => Trejjam\MailChimp\Request::class,
		'context'     => Trejjam\MailChimp\Context::class,
		'group.root'  => Trejjam\MailChimp\Group\Root::class,
		'group.lists' => Trejjam\MailChimp\Group\Lists::class,
		'lists'       => Trejjam\MailChimp\Lists::class,
		'segments'    => Trejjam\MailChimp\Segments::class,
	];

	protected $factoriesDefinition = [

	];

	public function __construct()
	{
		$this->default['http']['client']['verify'] = Composer\CaBundle\CaBundle::getSystemCaRootBundlePath();
	}

	/**
	 * Extract dc from apikey
	 *
	 * http://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3#resources
	 *
	 * @inheritdoc
	 */
	protected function createConfig()
	{
		$config = parent::createConfig();

		Nette\Utils\Validators::assert($config['apiUrl'], 'string', 'apiUrl');
		Nette\Utils\Validators::assert($config['apiKey'], 'string', 'apiKey');
		Nette\Utils\Validators::assert($config['lists'], 'array', 'list');
		Nette\Utils\Validators::assert($config['segments'], 'array', 'segments');

		foreach ($config['lists'] as $listName => $listId) {
			Nette\Utils\Validators::assert($listId, 'string', 'lists-' . $listName);
		}
		foreach ($config['segments'] as $listName => $segments) {
			Nette\Utils\Validators::assertField($config['lists'], $listName);

			foreach ($segments as $segmentId) {
				Nette\Utils\Validators::assert($segmentId, 'string|integer', $listName);
			}
		}

		if ($config['findDataCenter']) {
			$accountDataCenter = Nette\Utils\Strings::match($config['apiKey'], '~-(us(?:\d+))$~');
			$config['apiUrl'] = sprintf($config['apiUrl'], $accountDataCenter[1], Trejjam\MailChimp\Request::VERSION);
		}

		Nette\Utils\Validators::assert($config['apiUrl'], 'string', 'apiUrl');

		return $config;
	}

	public function beforeCompile()
	{
		parent::beforeCompile();

		$config = $this->createConfig();

		$classes = $this->getClasses();

		$classes['http.client']->setArguments(
			[
				'config' => $config['http']['client'],
			]
		)->setAutowired(FALSE);

		$classes['request']->setArguments(
			[
				'httpClient' => $this->prefix('@http.client'),
				'apiUrl'     => $config['apiUrl'],
				'apiKey'     => $config['apiKey'],
			]
		);

		$classes['lists']->setArguments(
			[
				'lists' => $config['lists'],
			]
		);
		$classes['segments']->setArguments(
			[
				'segments' => $config['segments'],
			]
		);
	}
}
