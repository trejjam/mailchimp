<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Exception;

use Psr;

final class RequestException extends \LogicException
{
    private Psr\Http\Message\ResponseInterface $httpResponse;

    /**
     * @internal
     */
    public function setResponse(Psr\Http\Message\ResponseInterface $httpResponse) : self
    {
        $this->httpResponse = $httpResponse;

        return $this;
    }

    public function getResponse() : Psr\Http\Message\ResponseInterface
    {
        return $this->httpResponse;
    }
}
