<?php

namespace Tests\Unit;

use Monolog\Formatter\JsonFormatter;
use Tests\TestCase;

class StructuredLoggingConfigTest extends TestCase
{
    public function test_production_file_logs_use_json_by_default(): void
    {
        $config = require config_path('logging.php');

        $this->assertSame(JsonFormatter::class, $config['channels']['single']['formatter']);
        $this->assertSame(JsonFormatter::class, $config['channels']['daily']['formatter']);
    }
}
