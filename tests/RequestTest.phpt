<?php

namespace Trejjam\MailChimp\Tests;

use Nette;
use Tester;
use Tester\Assert;
use Trejjam\MailChimp;

$container = require __DIR__ . '/bootstrap.php';

class RequestTest extends Tester\TestCase
{
	private $container;

	function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	public function testConfig()
	{
		/** @var MailChimp\Request $api */
		$api = $this->container->getByType(MailChimp\Request::class);

		Assert::type(MailChimp\Request::class, $api);

		/** @var \Trejjam\MailChimp\Entity\Root $rootResponse */
		$rootResponse = $api->get('/', MailChimp\Entity\Root::class);

		Assert::type(MailChimp\Entity\Root::class, $rootResponse);
		Assert::notSame('', $rootResponse->last_login);

		/** @var MailChimp\Entity\Link $firstLink */
		$firstLink = $rootResponse->getLinks()->current();

		Assert::type(MailChimp\Entity\Link::class, $firstLink);
		Assert::same($firstLink->rel, 'self');
		Assert::same($firstLink->method, 'GET');

	}
}

$test = new RequestTest($container);
$test->run();
