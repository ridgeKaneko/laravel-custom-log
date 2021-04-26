<?php


namespace CustomLog\Drivers;


use CustomLog\Handler\CsvHandler;
use CustomLog\LevelParser;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

abstract class CsvDriver extends BaseStreamDriver
{
    protected $record_map;

    protected function handler(): StreamHandler
    {
        $maxFiles = $this->config->days ?? 0;
        $level = $this->config->level ? LevelParser::parse($this->config->level) : Logger::DEBUG;

        return new CsvHandler(parent::logsPath(),$this->record_map,$maxFiles,$level);
    }
}

