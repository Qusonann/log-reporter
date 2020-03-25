<?php
declare(strict_types=1);

namespace LogReporter\Impl;

use Gelf\Message;

/**
 * Adds additional fields to log message
 */
class MyMessage extends Message
{
    /** @var Message */
    private $message;
    /** @var array */
    private $additionalFields;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(Message $message, array $additionalFields = [])
    {
        $this->message = $message;
        $this->additionalFields = $additionalFields;
    }

    public function toArray() {
        $arr = $this->message->toArray();
        return array_merge($arr, $this->additionalFields);
    }

    public function __call($name, $arguments)
    {
        return call_user_func([$this->message, $name], ...$arguments);
    }

    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return $this->message->getVersion();
    }

    /**
     * @inheritDoc
     */
    public function getHost()
    {
        return $this->message->getHost();
    }

    /**
     * @inheritDoc
     */
    public function getShortMessage()
    {
        return $this->message->getShortMessage();
    }

    /**
     * @inheritDoc
     */
    public function getFullMessage()
    {
        return $this->message->getFullMessage();
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp()
    {
        return $this->message->getTimestamp();
    }

    /**
     * @inheritDoc
     */
    public function getLevel()
    {
        return $this->message->getLevel();
    }

    /**
     * @inheritDoc
     */
    public function getSyslogLevel()
    {
        return $this->message->getSyslogLevel();
    }

    /**
     * @inheritDoc
     */
    public function getFacility()
    {
        return $this->message->getFacility();
    }

    /**
     * @inheritDoc
     */
    public function getFile()
    {
        return $this->message->getFile();
    }

    /**
     * @inheritDoc
     */
    public function getLine()
    {
        return $this->message->getLine();
    }

    /**
     * @inheritDoc
     */
    public function getAdditional($key)
    {
        return $this->message->getAdditional($key);
    }

    /**
     * @inheritDoc
     */
    public function hasAdditional($key)
    {
        return $this->message->hasAdditional($key);
    }

    /**
     * @inheritDoc
     */
    public function getAllAdditionals()
    {
        return $this->message->getAllAdditionals();
    }
}