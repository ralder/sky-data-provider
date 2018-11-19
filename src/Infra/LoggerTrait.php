<?php
namespace sky\Infra;

use Psr\Log\LoggerInterface;
use RuntimeException;

trait LoggerTrait
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @return LoggerInterface $logger
     */
    protected function getLogger(): LoggerInterface
    {
        if (null === $this->logger) {
            throw new RuntimeException('Logger not defined');
        }

        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    protected function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
