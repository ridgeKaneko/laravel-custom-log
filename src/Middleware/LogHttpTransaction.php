<?php

namespace CustomLog\Middleware;

use CustomLog\Facades\Log;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * ログチャンネルの固定を行い、リクエスト、レスポンスをログに残す
 *
 * Class LogHttpTransaction
 * @package App\Http\Middleware
 */
class LogHttpTransaction
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $channel | middlewareParam
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $channel = null)
    {
        //ログチャンネル切り替え
        Log::pinChannel($channel);

        //リクエストデータ出力
        Log::info("##request  >",["params" => $request->all()]);

        return $next($request);
    }

    public function terminate($req,$res)
    {
        //json
        if($res instanceof JsonResponse)
        {
            Log::info("##response >",["type" => "json","params" => $res->getData(true),"status" => $res->status()]);
        }
        //view
        elseif ($res instanceof Response && $res->original instanceof View)
        {
            $view = $res->original;
            Log::info("##response >",["type" => "view","path" => $view->getPath()]);
        }
        //redirect
        elseif ($res instanceof RedirectResponse)
        {
            Log::info("##response >",["type" => "redirect","url" => $res->getTargetUrl()]);
        }
    }
}
