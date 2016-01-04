<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 26. 10. 2014
 * Time: 17:38
 */

namespace Trejjam\MailChimp\DI;

use Nette,
	Trejjam;

/**
 * Class MailChimpExtension
 *
 * Inspired by
 * - https://github.com/zbycz/mailchimp-v3-php/blob/master/MailchimpService.php
 * - https://github.com/Kdyby/CsobPaymentGateway
 *
 * @package Trejjam\Email\DI
 */
class MailChimpExtension extends Trejjam\BaseExtension\DI\BaseExtension
{
	protected $default = [
		'findDc' => TRUE,
		'apiUrl' => 'https://%s.api.mailchimp.com/3.0/',
		'apiKey' => NULL,
		'list'   => NULL,  // list id from https://<dc>.api.mailchimp.com/playground/
	];

	protected $classesDefinition = [
		//TODO define used class
	];

	protected $factoriesDefinition = [
		//TODO define used factory (if necessary)
	];

	/**
	 * Extract dc from apikey
	 *
	 * http://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3#resources
	 *
	 * @inheritdoc
	 */
	protected function createConfig()
	{
		$config = parent::createConfig();

		Nette\Utils\Validators::assert($config['apiUrl'], 'string', 'apiUrl');
		Nette\Utils\Validators::assert($config['apiKey'], 'string', 'apiKey');
		Nette\Utils\Validators::assert($config['list'], 'string|integer|none', 'list');

		if ($config['findDc']) {
			$dc = Nette\Utils\Strings::match($config['apiKey'], '~-(us(?:\d+))$~');
			$config['apiUrl'] = sprintf($config['apiUrl'], $dc[1]);
		}

		Nette\Utils\Validators::assert($config['apiUrl'], 'string', 'apiUrl');

		return $config;
	}

	public function beforeCompile()
	{
		parent::beforeCompile();

		$config = $this->createConfig();

		$classes = $this->getClasses();

		//TODO add config values to service
	}
}
