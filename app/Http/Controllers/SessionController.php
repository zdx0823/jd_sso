<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Cookie;

use App\Models\LoginSt;
use App\Models\UserTgt;

use App\Custom\Common\CustomCommon;

/**
 * 处理登录逻辑
 * 设计验证验证码，生成tgc，生成st等动作
 */
class SingIn {

    private const S_CAPTCHA_ERR = "验证码错误";
    private const S_ACCOUNT_ERR = "账号或密码错误";
    private const S_SIGNIN_SUCC = "登录成功";

    /**
     * 尝试登录
     * 验证码错误，或登录失败返回提示语，成功返回true
     */
    private static function attemptLogin ($request) {

        $email = $request->email;
        $password = $request->password;
        $captcha = strtolower($request->captcha);
        $remember = boolval($request->remember);

        // 验证码是否正确
        $captchaKey = config('custom.session.captcha.login');
        $sessionCaptcha = strtolower(session($captchaKey));
        if ($captcha !== $sessionCaptcha) {
            return self::S_CAPTCHA_ERR;
        }

        // 尝试登录
        $res = Auth::attempt([
            'email' => $email,
            'password' => $password,
            'actived' => 1
        ]);

        // 用户不存在
        if (!$res) {
            return self::S_ACCOUNT_ERR;
        }

        return true;
    }


    /**
     * 生成Tgc并存入cookie
     */
    private static function generateTgc ($request) {

        // 生成tgt
        $tgt = CustomCommon::build_tgt();

        // cookie过期时间
        $cookieTimeout = $request->remember
            ? intval(config('custom.timeout.login.remember'))
            : intval(config('custom.timeout.login.default'));

        // 分钟为单位
        $cookieTimeout = intval($cookieTimeout / 60);

        // tgt存入cookie
        Cookie::queue('tgt', $tgt, $cookieTimeout);

        return [
            'tgt' => $tgt,
            'timeout' => $cookieTimeout,
        ];
    }


    /**
     * 生成重定向的url，生成ST
     */
    private static function getRedirectUrl ($tgt, $timeout) {

        $skey = config('custom.session.user');
        $userSession = session()->get($skey);

        if (isset($userSession['prevServe'])) {

            $prevServe = $userSession['prevServe'];

            // 取出id，生成st
            $uid = Auth::user()->id;
            $st = Customcommon::build_st();

            // 存入数据库
            LoginSt::create([
                'uid' => $uid,
                'st' => $st,
                'tgt' => $tgt,
                'ctime' => time(),
                'timeout' => $timeout,
            ]);
    
            // 拼接url
            $afterUrl = CustomCommon::appendQuery($prevServe, compact('st'));

        } else {

            $afterUrl = route('indexPage');
        }

        return $afterUrl;
    }


    /**
     * 尝试登录，生成tgc，生成重定向的url
     * 返回Customcommon::makeSuccRes或Customcommon::makeErrRes的执行结果
     */
    public static function handle ($request) {

        // 尝试登录，失败返回
        $attemptRes = self::attemptLogin($request);
        if ($attemptRes !== true) return CustomCommon::makeErrRes($attemptRes);;
        
        // 生成tgc
        [
            'tgt' => $tgt,
            'timeout' => $timeout,
        ] = self::generateTgc($request);

        // 取得重定向链接
        $after = self::getRedirectUrl($tgt, $timeout);

        return CustomCommon::makeSuccRes(compact('after'), self::S_SIGNIN_SUCC);
    }
}


class SessionController extends Controller {
    
    private const ST_LEN = 96;


    // 登录
    public function singIn (Request $request) {

        return SingIn::handle($request);

    }

    
    /**
     * 验证st是否有效
     * 有效将删除数据库对应记录
     * 返回json，正确返回的json包含该用户的id
     */
    public function checkSt (Request $request) {

        // 获取前96位视为st
        $st = substr($request->st, 0, self::ST_LEN);

        $ins = LoginSt::where('st', $st)->first();

        // 不存在
        if ($ins == null) return CustomCommon::makeErrRes(self::S_ST_ERR);

        // 超时
        $isTimeout = (time() - $ins->ctime) > config('custom.timeout.token.st');
        if ($isTimeout) return CustomCommon::makeErrRes(self::S_ST_ERR);

        // 可用

        // 生成TGC，和session_id一起存入数据库
        $tgc = Customcommon::build_tgc();
        $tgt = $ins->tgt;
        $tgtTimeout = $ins->timeout;
        $session_id = $request->session_id;

        UserTgt::create(compact('tgt', 'tgc', 'session_id'));

        // 删除st
        LoginSt::where('st', $st)->delete();

        // 返回tgc
        return CustomCommon::makeSuccRes([
            'tgc' => $tgc,
            'timeout' => $tgtTimeout,
        ]);
    }


    public function userInfo (Request $request) {

        return CustomCommon::makeSuccRes([
            'id' => 1
        ]);

    }
}
