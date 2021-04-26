<?php


namespace CustomLog\Processors;


use Carbon\Carbon;
use CustomLog\ProcessorRecordBag;

/**
 * 日付・日時関連ログプロセッサクラス
 *
 * @package App\Logging\Processors
 */
class DateTimeProcessor extends MonologProcessor
{
    protected $format = "Y-m-d H:i:s";

    /**
     * 各情報設定
     *
     * @param ProcessorRecordBag $bag
     * @return mixed|void
     */
    public function process(ProcessorRecordBag $bag)
    {
        $bag->set([
            'UTC' => Carbon::now("UTC")->format($this->format),
            'JST' => Carbon::now("Asia/Tokyo")->format($this->format)
        ]);
    }

    /**
     * 日付format設定
     *
     * @param string $format
     */
    public function format($format)
    {
        $this->format = $format;
    }
}
