<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
    echo "Install Nette Tester using `composer update --dev`\n";
    exit(1);
}

Tester\Environment::setup();

$tempDir = implode(DIRECTORY_SEPARATOR, [__DIR__, 'temp', Nette\Utils\Random::generate()]);
$logDir = implode(DIRECTORY_SEPARATOR, [__DIR__, 'log']);

if (!file_exists($tempDir)) {
    mkdir($tempDir, 0777, true);
}

if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

$configurator = new Nette\Configurator;
$configurator->setDebugMode(true);
Tracy\Debugger::$logDirectory = $logDir;
//$configurator->enableDebugger($logDir);
$configurator->setTempDirectory($tempDir);

$configurator->createRobotLoader()
    ->addDirectory(dirname(__DIR__) . '/src')
    ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');

if (array_key_exists('MAILCHIMP_API_KEY', $_ENV) && array_key_exists('MAILCHIMP_TEST_LIST', $_ENV)) {
    $configurator->addConfig(
        [
            'parameters' => [
                'mailchimpApiKey'   => $_ENV['MAILCHIMP_API_KEY'],
                'mailchimpTestList' => $_ENV['MAILCHIMP_TEST_LIST'],
            ],
        ]
    );
}
else {
    $configurator->addConfig(__DIR__ . '/config/config.local.neon');
}

return $configurator->createContainer();
