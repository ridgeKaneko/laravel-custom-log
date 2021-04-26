<?php


namespace CustomLog\Processors;

use CustomLog\ProcessorRecordBag;
use Monolog\Processor\ProcessorInterface;

/**
 * ログプロセッサ共通処理クラス
 *
 * @package CustomLog\Processors
 */
abstract class MonologProcessor implements ProcessorInterface
{
    /**
     * メイン処理
     *
     * @param array $records
     * @return array
     */
    public function __invoke(array $records)
    {
        return tap(new ProcessorRecordBag($records),function ($bag) {
            $this->process($bag);
        })->all();
    }

    /**
     * レコード生成追加等
     *
     * @param ProcessorRecordBag $bag
     * @return void
     */
    public abstract function process(ProcessorRecordBag $bag);
}
