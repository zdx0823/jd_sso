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
    private const S_LOGOUT_ERR = '部分网站登出失败，请您关闭浏览器清除登录信息';
    private const S_CHECK_TGC_FAIL = '该用户已登出，请重新登录';


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
        $logout_api = $request->logout_api;

        UserTgt::create(compact('tgt', 'tgc', 'session_id', 'logout_api'));

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


    /**
     * 遍历发起请求，登出api
     * 
     * 返回失败的数组
     */
    private static function doLogoutApi ($tgcList) {

        // 遍历发起请求
        $failItem = [];
        foreach ($tgcList as $item) {

            $logoutApi = $item['logout_api'];
            $session_id = $item['session_id'];

            $res = CustomCommon::client('POST', $logoutApi, [
                'form_params' => compact('session_id')
            ]);

            if ($res['status'] == -1) {
                array_push($failItem, $item);
            }
        }

        return $failItem;
    }

    
    /**
     * 登出
     * 1. 软删除所有关联tgc
     * 2. 取出所有tgc
     * 3. 硬删除所有关联tgc
     * 4. 遍历tgc对应api，发起请求
     * 5. 重定向到首页
     */
    public function ssoLogout (Request $request) {

        $tgt = UserTgt::where('tgc', $request->tgc)
            ->first()
            ->toArray()['tgt'];

        // 软删除所有tgt
        UserTgt::where('tgt', $tgt)->update([
            'dtime' => time()
        ]);

        // 登出Auth
        Auth::logout();

        // 取出所有tgt关联数据
        $tgcList = UserTgt::where('tgt', $tgt)->get()->toArray();

        // 硬删除所有关联tgt
        UserTgt::where('tgt', $tgt)->delete();

        // 删除cookie的tgt
        Cookie::queue(Cookie::forget('tgt'));

        // 遍历发起请求
        $failList = self::doLogoutApi($tgcList);

        // 对失败的再次请求一次
        $failList = self::doLogoutApi($failList);

        // 还有登不出的，返回提示语，让用户离开时关闭浏览器
        $msg = count($failList) > 0 ? self::S_LOGOUT_ERR : null;

        // return CustomCommon::makeSuccRes(compact('msg'));
        return redirect()->route('indexPage')->with('msg', $msg);
    }

    
    /**
     * 检查tgc是否有效，供子系统调用
     * 1. 需求tgc, session_id
     * 2. 是否存在数据库，dtime是否为0，是，更新session_id，否，不更新
     */
    public function checkTgc (Request $request) {

        $tgc = $request->tgc;
        $session_id = $request->session_id;

        $num = UserTgt::where('tgc', $tgc)
            ->where('dtime', 0)
            ->update(compact('session_id'));

        // 更新失败，返回
        if ($num == 0) return Customcommon::makeErrRes();

        // 更新成功
        return CustomCommon::makeSuccRes(self::S_CHECK_TGC_FAIL);
    }
}
