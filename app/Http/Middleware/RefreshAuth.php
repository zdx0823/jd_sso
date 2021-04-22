<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use Session;
use Auth;

use App\Models\UserTgt;
use App\Models\User;

use Illuminate\Http\Request;

/**
 * 更新session
 * cookie和session的tgt是否不等，不等则进行判断，调用Auth::login重新登入
 */
class RefreshAuth
{

    public function handle(Request $request, Closure $next) {
        
        $ctgt = Cookie::get('tgt');
        $stgt = Session::get('tgt');

        // 都不存在，登出
        if ($ctgt == null && $stgt == null) {
            
            Auth::logout();
        } else if ($ctgt == null) {
            // cookie不存在，登出

            Auth::logout();
            Session::forget('tgt');
        } else if ($stgt == null) {
            // session不存在，看情况登入

            $ins = UserTgt::where('tgt', $ctgt)->where('dtime', 0)->first();
            if ($ins) {
                Auth::login(User::find($ins->uid));
                session(['tgt' => $ctgt]);
            }
        } else {
            // 都存在，登入

            $uid = UserTgt::where('tgt', $ctgt)
                ->where('dtime', 0)
                ->first()
                ->toArray()['uid'];
            
            Auth::login(User::find($uid));
        }

        return $next($request);
    }
}
