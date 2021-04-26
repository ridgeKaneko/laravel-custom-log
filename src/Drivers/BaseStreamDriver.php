<?php


namespace CustomLog\Drivers;


use CustomLog\ChannelConfig;
use CustomLog\CustomStreamLogger;
use CustomLog\ProcessorRecordBag;
use Carbon\Carbon;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\ProcessorInterface;

abstract class BaseStreamDriver
{
    /**
     * @var ChannelConfig $config
     */
    protected $config;

    protected $processors;

    /**
     * Monolog Logger作成
     * メイン処理（logger作成時に実際に呼ばれるメソッド）
     *
     * @param array $config
     * @return Logger
     */
    public function __invoke(array $config) : Logger
    {
        $this->config = ChannelConfig::fromArray($config);

        //ハンドラ生成、設定
        $handler = $this->handler();
        $handler->setFormatter($this->formatter());

        //プロセッサ生成
        $processors = $this->processors();
        $processors[] = function ($records)
        {
            return tap(new ProcessorRecordBag($records),function ($bag) {
                $this->process($bag);
            })->all();
        };

        return new CustomStreamLogger($config["name"],$handler,$processors);
    }

    /**
     * ログ格納用ファイルパス生成（内部利用パス）
     *
     * @return string
     */
    protected function logsPath() : string
    {
        return $this->config->path ?? storage_path('/logs/laravel.log');
    }

    /**
     * handler作成
     * 内部処理
     * @return StreamHandler
     */
    abstract protected function handler() : StreamHandler;

    /**
     * formatter作成
     * 内部処理
     * @return FormatterInterface
     */
    abstract protected function formatter() : FormatterInterface;

    /**
     * processor作成処理　
     * 内部処理
     * @return ProcessorInterface[]
     */
    private function processors()
    {
        $processors = [];
        foreach ($this->processors as $class)
        {
            $processors[] = new $class;
        }

        return $processors;
    }

    /**
     * ドライバ固有プロセッサ処理
     *
     * @param ProcessorRecordBag $recordBag
     */
    protected function process(ProcessorRecordBag $recordBag){}
}
