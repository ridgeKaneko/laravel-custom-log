<?php


namespace CustomLog\Processors;


use CustomLog\ProcessorRecordBag;
use Illuminate\Support\Facades\Request;

/**
 * ルート情報関連ログプロセッサクラス
 *
 * @package CustomLog\Processors
 */
class RouteProcessor extends MonologProcessor
{
    const LENGTH_ACTION = 45;
    const LENGTH_URL = 18;

    /**
     * 各情報設定
     *
     * @param ProcessorRecordBag $bag
     * @return mixed|void
     */
    public function process(ProcessorRecordBag $bag)
    {
        $bag->set("route-method",Request::route()->getActionMethod());
        $bag->set("route-action",$this->getAction(),self::LENGTH_ACTION);
        $bag->set("route-url",Request::route()->uri(),self::LENGTH_URL);
    }

    /**
     * アクション名取得
     *
     * @return string|string[]
     */
    private function getAction()
    {
        return str_replace("App\\Http\\Controllers\\","",Request::route()->getActionName());
    }
}
