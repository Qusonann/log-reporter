<?php
declare(strict_types=1);

namespace Tests\Impl;

use LogReporter\Impl\LogServerReporter;
use LogReporter\Reporter;
use Monolog\Test\TestCase;

class DefaultReporterTest extends TestCase
{
    public function test_reporter_create_and_log(): void
    {
        $reporter = new LogServerReporter();
        $reporter->reportDebug("test");
        $reporter->reportNotice("test");
        $reporter->sendOut();
        $this->assertTrue($reporter instanceof Reporter);
    }
}