<?php


namespace CustomLog\Drivers;


use CustomLog\LevelParser;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

abstract class DayRotateDriver extends BaseStreamDriver
{
    protected $dateFormat = "Y-m-d H:i:s.v";
    protected $logFormat;

    protected function handler(): StreamHandler
    {
        $maxFiles = $this->config->days ?? 0;
        $level = $this->config->level ? LevelParser::parse($this->config->level) : Logger::DEBUG;

        return new RotatingFileHandler(parent::logsPath(),$maxFiles,$level);
    }

    protected function formatter(): FormatterInterface
    {
        return tap(new LineFormatter($this->logFormat.PHP_EOL, $this->dateFormat, true, true), function ($formatter) {
            $formatter->includeStacktraces();
        });
    }
}
