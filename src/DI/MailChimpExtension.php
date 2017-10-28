<?php
declare(strict_types=1);

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

	}

	/**
	 * Extract dc from apikey
	 *
	 * http://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3#resources
	 *
	 * @inheritdoc
	 */
	public function loadConfiguration(bool $validateConfig = TRUE) : void
	{
		$this->default['http']['client']['verify'] = Composer\CaBundle\CaBundle::getSystemCaRootBundlePath();

		parent::loadConfiguration();

		Nette\Utils\Validators::assert($this->config['apiUrl'], 'string', 'apiUrl');
		Nette\Utils\Validators::assert($this->config['apiKey'], 'string', 'apiKey');
		Nette\Utils\Validators::assert($this->config['lists'], 'array', 'list');
		Nette\Utils\Validators::assert($this->config['segments'], 'array', 'segments');

		foreach ($this->config['lists'] as $listName => $listId) {
			Nette\Utils\Validators::assert($listId, 'string', 'lists-' . $listName);
		}
		foreach ($this->config['segments'] as $listName => $segments) {
			Nette\Utils\Validators::assertField($this->config['lists'], $listName);

			foreach ($segments as $segmentId) {
				Nette\Utils\Validators::assert($segmentId, 'string|integer', $listName);
			}
		}

		if ($this->config['findDataCenter']) {
			$accountDataCenter = Nette\Utils\Strings::match($this->config['apiKey'], '~-(us(?:\d+))$~');
			$this->config['apiUrl'] = sprintf($this->config['apiUrl'], $accountDataCenter[1], Trejjam\MailChimp\Request::VERSION);
		}

		Nette\Utils\Validators::assert($this->config['apiUrl'], 'string', 'apiUrl');
	}

	public function beforeCompile()
	{
		parent::beforeCompile();

		$types = $this->getTypes();

		$types['http.client']->setArguments(
			[
				'config' => $this->config['http']['client'],
			]
		)->setAutowired(FALSE);

		$types['request']->setArguments(
			[
				'httpClient' => $this->prefix('@http.client'),
				'apiUrl'     => $this->config['apiUrl'],
				'apiKey'     => $this->config['apiKey'],
			]
		);

		$types['lists']->setArguments(
			[
				'lists' => $this->config['lists'],
			]
		);
		$types['segments']->setArguments(
			[
				'segments' => $this->config['segments'],
			]
		);
	}
}
