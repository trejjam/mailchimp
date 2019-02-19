<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Tests;

use Nette\DI\Compiler;
use Tester\TestCase;
use Tester\Assert;
use Trejjam\MailChimp;
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
                    'client' => ['verify' => CaBundle::getSystemCaRootBundlePath()],
                ],
            ], $mailChimpConfig
        );
    }
}

$test = new DITest($container);
$test->run();
