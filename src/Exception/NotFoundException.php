<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Exception;

use Exception;
use Throwable;

abstract class NotFoundException extends \LogicException
{
    /**
     * @param string $message
     * @param int|Throwable $code
     */
    public function __construct($message = '', $code = 0, ?Throwable $previous = null)
    {
        if ($code instanceof Exception && $previous === null) {
            $previous = $code;
            $code = 0;
        }
        assert(is_int($code));

        parent::__construct($message, $code, $previous);
    }
}
