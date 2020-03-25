<?php
declare(strict_types=1);

namespace LogReporter\Impl;

use LogReporter\Reporter;
use Gelf\Publisher;
use Gelf\Transport\UdpTransport;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Throwable;

class LogServerReporter implements Reporter
{
    /**
     * @var Logger
     */
    private $monolog;
    /**
     * @var string[]
     */
    private $notices;
    /**
     * @var string[]
     */
    private $alerts;
    /**
     * @var array
     */
    private $extras = [];

    public function __construct(string $logServerIp = '', int $logServerPort = 0, array $additionalFields = [], bool $toStdOut = true)
    {
        $this->monolog = new Logger('default');
        if ($toStdOut) {
            $this->monolog->pushHandler(new StreamHandler(STDOUT));
        }
        if ($logServerIp !== '') {
            $transport = new UdpTransport($logServerIp, $logServerPort);
            $handler = new ApplicationGelfHandler(
                $additionalFields,
                new Publisher($transport)
            );
            $this->monolog->pushHandler($handler);
        }
    }

    /** @noinspection SpellCheckingInspection */
    public static function setHandlers(Reporter $reporter): void
    {
        $handler = function (int $errno, string $errstr, $errfile, $errline) use($reporter) {
            if (error_reporting()) {
                $reporter->reportNotice("ERROR: $errno; $errstr; $errfile; $errline");
            }
        };
        set_error_handler($handler);
        set_exception_handler(function (Throwable $e) use($reporter) {
            $reporter->reportNotice("EXCEPTION: " . $e->getMessage());
        });
    }

    public function reportNotice(string $message, array $extra = []): void
    {
        if (!empty($extra)) {
            $this->extras[$message] = $extra;
        }
        if(!isset($this->notices[$message]))
            $this->notices[$message] = 1;
        else
            $this->notices[$message]++;
    }

    public function reportAlert(string $message, array $extra = []): void
    {
        if (!empty($extra)) {
            $this->extras[$message] = $extra;
        }
        if(!isset($this->alerts[$message]))
            $this->alerts[$message] = 1;
        else
            $this->alerts[$message]++;
    }


    public function reportDebug(string $message, array $extra = []): void
    {
        $this->monolog->debug($message, $extra);
    }


    public function sendOut(): void
    {
        if(!empty($this->alerts)) {
            try {
                $this->monolog->alert(
                    $this->combineTypedMessagesIntoSingle($this->alerts),
                    $this->getExtra($this->alerts)
                );
                $this->alerts = [];
            } catch (Throwable $e){
                $this->alerts = [];
            }
        }
        if(!empty($this->notices)) {
            try {
                $this->monolog->notice(
                    $this->combineTypedMessagesIntoSingle($this->notices),
                    $this->getExtra($this->notices)
                );
                $this->notices = [];
            } catch (Throwable $e){
                $this->notices = [];
            }
        }
        $this->extras = [];
    }

    private function getExtra(array $messageGroup): array
    {
        foreach ($messageGroup as $key => $value) {
            if (isset($this->extras[$key])) {
                return $this->extras[$key];
            }
        }

        return [];
    }

    /**
     * @param array $messageGroup
     * @return string
     */
    private function combineTypedMessagesIntoSingle(array $messageGroup): string
    {
        $singleMessage = "";
        foreach ($messageGroup as $messageOfGroup => $count) {
            $singleMessage .=  PHP_EOL . $count . ") " . $messageOfGroup;
        }

        return $singleMessage;
    }
}