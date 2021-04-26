<?php


namespace CustomLog\Handler;


use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CsvHandler extends RotatingFileHandler
{
    /**
     * @var array [ヘッダ名 -> レコードキー]
     */
    private $record_map;

    public function __construct(string $filename,array $record_map, int $maxFiles = 0, $level = Logger::DEBUG, bool $bubble = true, int $filePermission = null, bool $useLocking = false)
    {
        parent::__construct($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking);

        $this->record_map = $record_map;
    }

    public function handle(array $record): bool
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        if ($this->processors) {
            $record = $this->processRecord($record);
        }

        $this->write($record);

        return false === $this->bubble;
    }

    protected function streamWrite($stream, array $record): void
    {
        //初回書き込み時
        if(!fgetcsv($stream)){
            fputcsv($stream,array_keys($this->record_map)); //ヘッダ書き込み
        }

        //ログ本文部分作成
        $row = [];
        foreach ($this->record_map as $recordKey) {
            $row[] = $record[$recordKey];
        }

        //本文書き込み
        fputcsv($stream,$row);
    }
}