<?php

require __DIR__ . '/../vendor/autoload.php';

if ( !class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();

$tempDir = __DIR__ . '/temp';

if ( !file_exists($tempDir)) {
	mkdir($tempDir);
}

$configurator = new Nette\Configurator;
$configurator->setDebugMode(!FALSE);
Tracy\Debugger::$logDirectory = __DIR__ . '/log';
//$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory($tempDir);
$configurator->createRobotLoader()
			 ->addDirectory(__DIR__ . '/../src')
			 ->addDirectory(__DIR__ . '/mock')
			 ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

return $configurator->createContainer();
