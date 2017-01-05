<?php

namespace Trejjam\MailChimp\Tests;

use Nette;
use Tester;
use Tester\Assert;
use Trejjam\MailChimp;
use Composer;

$container = require __DIR__ . '/bootstrap.php';

class DITest extends Tester\TestCase
{
	public function testConfig()
	{
		$mailChimpExtension = new MailChimp\Tests\Mock\MailChimpExtension;

		$mailChimpExtension->setConfig(
			[
				'apiKey' => 'someApiKey123-us11',
				'lists'  => [
					'newsletter' => 'foo123',
					'user'       => 123,
				],
			]
		);

		$mailChimpExtension->setCompiler(new Nette\DI\Compiler, 'container_' . __FUNCTION__);
		$mailChimpConfig = $mailChimpExtension->createConfig();

		Assert::same(
			[
				'findDataCenter' => TRUE,
				'apiUrl'         => 'https://us11.api.mailchimp.com/3.0/',
				'apiKey'         => 'someApiKey123-us11',
				'lists'          => [
					'newsletter' => 'foo123',
					'user'       => 123,
				],
				'http'           => [
					'client' => ['verify' => Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()],
				],
			], $mailChimpConfig
		);
	}
}

$test = new DITest($container);
$test->run();
