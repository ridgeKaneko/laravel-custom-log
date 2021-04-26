<?php


namespace CustomLog;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class CustomStreamLogger extends Logger
{
    protected $streamHandler;

    public function __construct(string $name, StreamHandler $handler,$processors)
    {
        $this->streamHandler = $handler;
        parent::__construct($name, [$handler], $processors, null);
    }

    /**
     * ログの書き込みを行うファイルのパス取得
     *
     * @return string|null
     */
    public function filePath()
    {
        return $this->streamHandler->getUrl();
    }
}
