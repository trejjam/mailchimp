<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Tests;

use Composer\CaBundle\CaBundle;
use GuzzleHttp;
use Nette\DI\Compiler;
use Nette\DI\Definitions\Reference;
use Tester\Assert;
use Tester\TestCase;
use Trejjam\MailChimp\DI\MailChimpExtension;

$container = require __DIR__ . '/../bootstrap.php';

final class DITest extends TestCase
{
    private const NAME = 'trejjam.mailchimp';

    public function testConfig() : void
    {
        $mailChimpExtension = new MailChimpExtension();

        $compiler = new Compiler();
        $compiler->addExtension(self::NAME, $mailChimpExtension);
        $compiler->addConfig(
            [
                self::NAME => [
                    'apiKey'   => 'someApiKey123-us11',
                    'lists'    => [
                        'newsletter' => 'foo123',
                        'user'       => '123',
                    ],
                    'segments' => [
                        'newsletter' => [
                            'segmentA' => 2,
                        ],
                    ],
                ],
            ]
        );
        $compiler->processExtensions();

        $mailChimpConfig = $mailChimpExtension->getConfig();

        Assert::same(true, $mailChimpConfig->findDataCenter);
        Assert::same('https://us11.api.mailchimp.com/3.0/', $mailChimpConfig->apiUrl);
        Assert::same('someApiKey123-us11', $mailChimpConfig->apiKey);
        Assert::same(
            [
                'newsletter' => 'foo123',
                'user'       => '123',
            ],
            $mailChimpConfig->lists
        );
        Assert::same(
            [
                'newsletter' => [
                    'segmentA' => 2,
                ],
            ],
            $mailChimpConfig->segments
        );
        Assert::null($mailChimpConfig->http->clientFactory);
        Assert::same([
            'verify' => CaBundle::getSystemCaRootBundlePath(),
        ], $mailChimpConfig->http->client);
    }

    public function testGuzzleFactory() : void
    {
        $mailChimpExtension = new MailChimpExtension();

        $compiler = new Compiler();
        $compiler->addExtension(self::NAME, $mailChimpExtension);
        $compiler->addConfig(
            [
                self::NAME => [
                    'apiKey' => 'someApiKey123-us11',
                    'http'   => [
                        'clientFactory' => '@guzzleClassFactory',
                    ],
                ],
            ]
        );
        $containerBuilder = $compiler->getContainerBuilder();

        $guzzleClassFactory = $containerBuilder->addDefinition('guzzleClassFactory');
        $guzzleClassFactory->setType(GuzzleHttp\Client::class);

        $compiler->processExtensions();

        $mailChimpExtension->beforeCompile();

        $mailChimpConfig = $mailChimpExtension->getConfig();

        Assert::same(true, $mailChimpConfig->findDataCenter);
        Assert::same('https://us11.api.mailchimp.com/3.0/', $mailChimpConfig->apiUrl);
        Assert::same('someApiKey123-us11', $mailChimpConfig->apiKey);
        Assert::same([], $mailChimpConfig->lists);
        Assert::same([], $mailChimpConfig->segments);
        Assert::same('@guzzleClassFactory', $mailChimpConfig->http->clientFactory);
        Assert::same([
            'verify' => CaBundle::getSystemCaRootBundlePath(),
        ], $mailChimpConfig->http->client);

        $httpClient = $containerBuilder->getDefinition(self::NAME . '.http.client');

        $httpClientServiceDefinition = $httpClient->getFactory()->getEntity();
        if ($httpClientServiceDefinition instanceof Reference) {
            Assert::same('guzzleClassFactory', $httpClientServiceDefinition->getValue());
        }
        else {
            // pre Nette 3.0 compatibility
            Assert::same('@guzzleClassFactory', $httpClientServiceDefinition);
        }
    }

    public function testGuzzleFactory2() : void
    {
        $mailChimpExtension = new MailChimpExtension();

        $compiler = new Compiler();
        $compiler->addExtension(self::NAME, $mailChimpExtension);
        $compiler->addConfig(
            [
                self::NAME => [
                    'apiKey' => 'someApiKey123-us11',
                    'http'   => [
                        'clientFactory' => 'GuzzleHttp\Client([])',
                    ],
                ],
            ]
        );

        $compiler->processExtensions();

        $mailChimpExtension->beforeCompile();

        $mailChimpConfig = $mailChimpExtension->getConfig();

        Assert::same(true, $mailChimpConfig->findDataCenter);
        Assert::same('https://us11.api.mailchimp.com/3.0/', $mailChimpConfig->apiUrl);
        Assert::same('someApiKey123-us11', $mailChimpConfig->apiKey);
        Assert::same([], $mailChimpConfig->lists);
        Assert::same([], $mailChimpConfig->segments);
        Assert::same('GuzzleHttp\Client([])', $mailChimpConfig->http->clientFactory);
        Assert::same([
            'verify' => CaBundle::getSystemCaRootBundlePath(),
        ], $mailChimpConfig->http->client);

        $containerBuilder = $compiler->getContainerBuilder();
        $httpClient = $containerBuilder->getDefinition(self::NAME . '.http.client');

        $httpClientServiceDefinition = $httpClient->getFactory()->getEntity();
        Assert::same('GuzzleHttp\Client([])', $httpClientServiceDefinition);
    }
}

$test = new DITest($container);
$test->run();
