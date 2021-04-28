<?php


namespace CustomLog\Drivers;


use CustomLog\Formatter\CsvFormatter;
use CustomLog\Handler\CsvHandler;
use CustomLog\LevelParser;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

abstract class CsvDriver extends BaseStreamDriver
{
    protected $record_map;

    protected $charEncoding;
    protected $dateFormat;

    protected function handler(): StreamHandler
    {
        $maxFiles = $this->config->days ?? 0;
        $level = $this->config->level ? LevelParser::parse($this->config->level) : Logger::DEBUG;

        return new CsvHandler(parent::logsPath(),$this->record_map,$maxFiles,$level);
    }

    protected function formatter(): FormatterInterface
    {
        return new CsvFormatter($this->record_map,$this->dateFormat,$this->charEncoding);
    }
}

