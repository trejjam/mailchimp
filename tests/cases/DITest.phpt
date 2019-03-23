<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Tests;

use GuzzleHttp;
use Nette\DI\Compiler;
use Tester\TestCase;
use Tester\Assert;
use Trejjam\MailChimp\DI\MailChimpExtension;
use Composer\CaBundle\CaBundle;

$container = require __DIR__ . '/../bootstrap.php';

final class DITest extends TestCase
{
    public function testConfig() : void
    {
        $mailChimpExtension = new MailChimpExtension;
        $mailChimpExtension->setCompiler(new Compiler, 'container_' . __FUNCTION__);

        $mailChimpExtension->setConfig(
            [
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
            ]
        );

        $mailChimpExtension->loadConfiguration();

        $mailChimpConfig = $mailChimpExtension->getConfig();

        Assert::same(
            [
                'findDataCenter' => true,
                'apiUrl'         => 'https://us11.api.mailchimp.com/3.0/',
                'apiKey'         => 'someApiKey123-us11',
                'lists'          => [
                    'newsletter' => 'foo123',
                    'user'       => '123',
                ],
                'segments'       => [
                    'newsletter' => [
                        'segmentA' => 2,
                    ],
                ],
                'http'           => [
                    'clientFactory' => null,
                    'client'        => ['verify' => CaBundle::getSystemCaRootBundlePath()],
                ],
            ], $mailChimpConfig
        );
    }

    public function testGuzzleFactory() : void
    {
        $compiler = new Compiler;
        $containerBuilder = $compiler->getContainerBuilder();

        $guzzleClassFactory = $containerBuilder->addDefinition('guzzleClassFactory');
        $guzzleClassFactory->setType(GuzzleHttp\Client::class);

        $mailChimpExtension = new MailChimpExtension;
        $extensionName = 'container_' . __FUNCTION__;
        $mailChimpExtension->setCompiler($compiler, $extensionName);

        $mailChimpExtension->setConfig(
            [
                'apiKey' => 'someApiKey123-us11',
                'http'   => [
                    'clientFactory' => '@guzzleClassFactory',
                ],
            ]
        );

        $mailChimpExtension->loadConfiguration();
        $mailChimpExtension->beforeCompile();

        $mailChimpConfig = $mailChimpExtension->getConfig();

        Assert::same(
            [
                'findDataCenter' => true,
                'apiUrl'         => 'https://us11.api.mailchimp.com/3.0/',
                'apiKey'         => 'someApiKey123-us11',
                'lists'          => [],
                'segments'       => [],
                'http'           => [
                    'clientFactory' => '@guzzleClassFactory',
                    'client'        => ['verify' => CaBundle::getSystemCaRootBundlePath()],
                ],
            ], $mailChimpConfig
        );

        $httpClient = $containerBuilder->getDefinition($extensionName . '.http.client');
        Assert::same('@guzzleClassFactory', $httpClient->getFactory()->getEntity());
    }
}

$test = new DITest($container);
$test->run();
