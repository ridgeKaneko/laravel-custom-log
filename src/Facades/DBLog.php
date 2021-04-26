<?php


namespace CustomLog\Facades;


use CustomLog\DBLogService;
use Illuminate\Support\Facades\Facade;

/**
 * Class DBLog
 * @method static void listenQuery()
 * @method static void logException(\Throwable $exception)
 * @method static void setChannel(string $channel)
 *
 * @see DBLogService
 * @package App\Facades
 */
class DBLog extends Facade
{
    /**
     * アクセス先であるインスタンスをコンテナから取得する際のキー定義
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return DBLogService::class;
    }
}
