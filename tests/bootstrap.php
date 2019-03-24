<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
    echo "Install Nette Tester using `composer update --dev`\n";
    exit(1);
}

Tester\Environment::setup();

$tempDir = implode(DIRECTORY_SEPARATOR, [__DIR__, 'temp', Nette\Utils\Random::generate()]);

if (!file_exists($tempDir)) {
    mkdir($tempDir, 0777, true);
}

$configurator = new Nette\Configurator;
$configurator->setDebugMode(true);
Tracy\Debugger::$logDirectory = __DIR__ . '/log';
//$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory($tempDir);

$configurator->createRobotLoader()
    ->addDirectory(dirname(__DIR__) . '/src')
    ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

return $configurator->createContainer();
