<?php

namespace App\Service\Traits;

use Psr\Log\LoggerInterface;

trait LoggerAwareTrait
{
    protected LoggerInterface $logger;

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
