<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

use Illuminate\Http\Request;

use App\Custom\Common\CustomCommon;

class CheckAuth
{

    private $needJsonRoute = [];

    /**
     * 是否登录超时
     * 根据 env('USER_SESSION_KEY')的session中的 id 和 timeout判断
     * id跟当前登录id对应不上，表示登录超时
     * 当前时间大于timeout表示超时
     * 
     * 返回布尔值
     */
    private function isAuthTimeout () {

        [
            'id' => $id,
            'timeout' => $timeout
        ] = session()->get(env('USER_SESSION_KEY'));

        $user = Auth::user();

        if ($user->id !== $id) return false;
        if (time() > $timeout) return false;

        return true;
    }


    public function handle(Request $request, Closure $next)
    {
        // 判断需要返回视图还是json数据
        $routeName = $request->route()->getName();
        $jsonRes = CustomCommon::makeErrRes('未登录，请登录后操作');
        $viewRes = redirect()->route('indexPage');
        $res = \in_array($routeName, $this->needJsonRoute)
            ? $jsonRes
            : $viewRes;

        // 没登录，返回
        if (!Auth::check()) return $res;

        // 已登录，但超时，返回
        if (!$this->isAuthTimeout()) return $res;

        // 已登录，且未超时，正常登录
        return $next($request);
    }
}
