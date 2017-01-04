<?php

namespace Trejjam\MailChimp\Tests\Mock;

use Nette;
use Trejjam;

class MailChimpExtension extends Trejjam\MailChimp\DI\MailChimpExtension
{

	public function createConfig()
	{
		return parent::createConfig();
	}
}
