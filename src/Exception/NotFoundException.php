<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Exception;

use Exception;

abstract class NotFoundException extends \LogicException
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if ($code instanceof Exception && $previous === null) {
            $previous = $code;
            $code = 0;
        }

        parent::__construct($message, $code, $previous);
    }
}
