<?php


namespace CustomLog;


use http\Exception\InvalidArgumentException;
use Monolog\Logger;

/**
 * monologログレベル　パース処理クラス
 *
 * @package CustomLog
 */
class LevelParser
{
    const LEVEL_MAP = [
        'debug'     => Logger::DEBUG,
        'info'      => Logger::INFO,
        'notice'    => Logger::NOTICE,
        'warning'   => Logger::WARNING,
        'error'     => Logger::ERROR,
        'critical'  => Logger::CRITICAL,
        'alert'     => Logger::ALERT,
        'emergency' => Logger::EMERGENCY,
    ];

    /**
     * ログレベルパース処理
     *
     * @param string $str
     * @return mixed
     */
    public static function parse(string $str)
    {
        if(!array_key_exists($str,self::LEVEL_MAP)){
            throw new InvalidArgumentException("Invalid log level.");
        }

        return self::LEVEL_MAP[$str];
    }

}
