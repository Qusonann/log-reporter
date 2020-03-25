<?php
declare(strict_types=1);

namespace LogReporter\Impl;

use Gelf\PublisherInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\GelfHandler;
use Monolog\Logger;

class ApplicationGelfHandler extends GelfHandler
{
    /** @var array */
    private $additionalFields;

    public function __construct(array $additionalFields, PublisherInterface $publisher, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($publisher, $level, $bubble);
        $this->additionalFields = $additionalFields;
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new ApplicationGelfFormatter($this->additionalFields);
    }
}