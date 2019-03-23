<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\DI;

use GuzzleHttp;
use Composer\CaBundle\CaBundle;
use Nette\DI\Compiler;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Trejjam\MailChimp;
use Trejjam\BaseExtension\DI\BaseExtension;
use function Safe\sprintf;

/**
 * Inspired by
 * - https://github.com/zbycz/mailchimp-v3-php/blob/master/MailchimpService.php
 * - https://github.com/Kdyby/CsobPaymentGateway
 */
final class MailChimpExtension extends BaseExtension
{
	protected $default = [
		'findDataCenter' => true,
		'apiUrl'         => 'https://%s.api.mailchimp.com/%s/',
		'apiKey'         => null,
		'lists'          => [],  // self named ids from https://<dc>.api.mailchimp.com/playground/
		'segments'       => [],
		'http'           => [
			'clientFactory' => null,
			'client'        => [
				'verify' => null, //NULL will be filled by Composer CA
			],
		],
	];

	protected $classesDefinition = [
		'http.client' => GuzzleHttp\Client::class,

		'request' => MailChimp\Request::class,

		'context'     => MailChimp\Context::class,
		'group.root'  => MailChimp\Group\Root::class,
		'group.lists' => MailChimp\Group\Lists::class,

		'lists'    => MailChimp\Lists::class,
		'segments' => MailChimp\Segments::class,
	];

	protected $factoriesDefinition = [

	];

	public function __construct()
	{
		$this->default['http']['client']['verify'] = CaBundle::getSystemCaRootBundlePath();
	}

	/**
	 * Extract dc from apikey
	 *
	 * http://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3#resources
	 *
	 * @inheritdoc
	 */
	public function loadConfiguration(bool $validateConfig = true) : void
	{
		parent::loadConfiguration();

		Validators::assert($this->config['apiUrl'], 'string', 'apiUrl');
		Validators::assert($this->config['apiKey'], 'string', 'apiKey');
		Validators::assert($this->config['lists'], 'array', 'list');
		Validators::assert($this->config['segments'], 'array', 'segments');
		Validators::assert($this->config['http']['clientFactory'], 'null|string|array|Nette\DI\Statement', 'http.client');

		foreach ($this->config['lists'] as $listName => $listId) {
			Validators::assert($listId, 'string', 'lists-' . $listName);
		}
		foreach ($this->config['segments'] as $listName => $segments) {
			Validators::assertField($this->config['lists'], $listName);

			foreach ($segments as $segmentId) {
				Validators::assert($segmentId, 'string|integer', $listName);
			}
		}

		if ($this->config['findDataCenter'] === true) {
			$accountDataCenter = Strings::match($this->config['apiKey'], '~-(us(?:\d+))$~');
			$this->config['apiUrl'] = sprintf($this->config['apiUrl'], $accountDataCenter[1], MailChimp\Request::VERSION);
		}

		Validators::assert($this->config['apiUrl'], 'string', 'apiUrl');
	}

	public function beforeCompile() : void
	{
		parent::beforeCompile();

		$types = $this->getTypes();

		if ($this->config['http']['clientFactory'] !== null) {
			if (is_string($this->config['http']['clientFactory']) && Strings::startsWith($this->config['http']['clientFactory'], '@')) {
				$types['http.client']->setFactory($this->config['http']['clientFactory']);
			}
			else {
				Compiler::loadDefinition($types['http.client'], $this->config['http']['clientFactory']);
			}
		}

		$types['http.client']->setArguments(
			[
				'config' => $this->config['http']['client'],
			]
		)->setAutowired(false);

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
