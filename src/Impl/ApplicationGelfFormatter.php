<?php
declare(strict_types=1);

namespace LogReporter\Impl;

use Gelf\Message;
use Monolog\Formatter\GelfMessageFormatter;

class ApplicationGelfFormatter extends GelfMessageFormatter
{
    /** @var array */
    private $additionalFields;

    public function __construct(array $additionalFields, ?string $systemName = null, ?string $extraPrefix = null, string $contextPrefix = 'ctxt_', ?int $maxLength = null)
    {
        parent::__construct($systemName, $extraPrefix, $contextPrefix, $maxLength);
        $this->additionalFields = $additionalFields;
    }

    public function format(array $record): Message
    {
        return new MyMessage(parent::format($record), $this->additionalFields);
    }
}