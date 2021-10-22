<?php


namespace CustomLog\Middleware;


use CustomLog\Facades\DBLog;
use Closure;
use Illuminate\Http\Request;

class LogDBQuery
{
    public function handle(Request $request, Closure $next, string $channel = null)
    {
        //ミドルウェアでチャンネル指定があった場合
        if (!is_null($channel)) {
            DBLog::setChannel($channel);
        }

        DBLog::listenQuery();

        return $next($request);
    }
}
