<?php


namespace CustomLog\Middleware;


use CustomLog\Facades\DBLog;
use Closure;
use Illuminate\Http\Request;

class LogDBQuery
{
    public function handle(Request $request, Closure $next, string $channel)
    {
        DBLog::listenQuery();
    }
}
