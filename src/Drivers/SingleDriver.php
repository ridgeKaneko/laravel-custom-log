<?php


namespace CustomLog\Drivers;


use CustomLog\LevelParser;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

abstract class SingleDriver extends BaseStreamDriver
{
    protected $dateFormat = "Y-m-d H:i:s.v";
    protected $logFormat;

    protected function handler(): StreamHandler
    {
        $level = $this->config->level ? LevelParser::parse($this->config->level) : Logger::DEBUG;

        return new StreamHandler(self::logsPath(), $level, true,null,false);
    }

    protected function formatter(): FormatterInterface
    {
        return tap(new LineFormatter($this->logFormat.PHP_EOL, $this->dateFormat, true, true), function ($formatter) {
            $formatter->includeStacktraces();
        });
    }
}
