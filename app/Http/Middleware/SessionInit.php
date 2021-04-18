<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// 初始化一些自定义session
class SessionInit
{

    /**
     * 用户，
     * id，当前登录的用户id
     * timeout，超时时间，是个未来的时间 time() + xxx 的结果
     */
    private function user () {

        $key = env('USER_SESSION_KEY');

        // 没有这个数组，初始化
        if (!session()->has($key)) {
            session([
                $key => [
                    'id' => null,
                    'timeout' => 0,
                    'prevServe' => null,
                ]
            ]);
        }

        // 数组中没有这两个键，初始化
        $session = session()->get($key);
        if (!array_key_exists('id', $session) || !array_key_exists('timeout', $session)) {
            session([
                $key => [
                    'id' => null,
                    'timeout' => 0
                ]
            ]);
        }

    }

    public function handle(Request $request, Closure $next)
    {

        $this->user();

        return $next($request);
    }
}
