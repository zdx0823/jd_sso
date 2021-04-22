<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;

use Illuminate\Http\Request;

use App\Custom\SingIn\SingIn;
use App\Custom\Common\CustomCommon;

use App\Models\UserTgt;

class IsLogged
{

    private static function isServeExist ($tgt, $serve) {

        $ins = UserTgt::where('tgt', $tgt)
            ->where('serve', $serve)
            ->first();

        return !($ins == null);
    }


    /**
     * 判断是继续下一步，还是生成ST返回重定向链接
     * 
     * 只有SSO未登录，或数据库已存在相同记录才会下一步，否则ST生成重定向链接
     */
    public function handle(Request $request, Closure $next) {

        $tgt = Cookie::get('tgt');
        $serve = $request->serve ? 
            Customcommon::getUrlWithPort($request->serve)
            : null;

        // 无serve参数，下一步
        if ($serve == null) return $next($request);

        // 未登录，下一步
        if ($tgt == null) return $next($request);

        // 数据库有相同项，即已颁发过ST，并验证成功，重定向回它来的页面
        if (self::isServeExist($tgt, $serve)) return $next($request);

        // 已登录，数据库查无此项，表示要申请ST的

        // 过期时间
        $timeout = intval(config('custom.timeout.token.st') / 60);

        // 生成ST，生成重定向url
        $redirectUrl = SingIn::getRedirectUrl($tgt, $timeout, $request->serve);

        return redirect()->away($redirectUrl);
    }
}
