<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 13.2.15
 * Time: 16:54
 */

namespace Trejjam\MailChimp;


use Nette,
	Trejjam;

interface Exception
{

}

interface LogicalException extends Exception
{

}

class LogicException extends \LogicException implements LogicalException
{

}
