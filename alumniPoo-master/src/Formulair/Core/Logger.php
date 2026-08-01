<?php

namespace Formulair\Core;

class Logger
{
    private string $logFile;
    private string $logLevel;

    public function __construct(string $logFile = null)
    {
        if ($logFile === null) {
            $logDir = dirname(__DIR__) . '/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . '/app.log';
        }

        $this->logFile = $logFile;
        $this->logLevel = Environment::get('APP_DEBUG', 'false') === 'true' ? 'DEBUG' : 'ERROR';
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[$timestamp] $level: $message$contextStr" . PHP_EOL;

        // Write to stderr so logs show up in the hosting platform's log stream
        // (the log file is not persisted/visible on Railway's ephemeral filesystem).
        // php://stderr is used instead of the STDERR constant, which isn't
        // defined under all SAPIs (e.g. the PHP built-in server).
        $stderr = @fopen('php://stderr', 'a');
        if ($stderr) {
            fwrite($stderr, $logMessage);
            fclose($stderr);
        }

        // Best-effort local file write for local development; must not fail the request.
        @error_log($logMessage, 3, $this->logFile);
    }
}
