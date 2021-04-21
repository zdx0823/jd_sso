<?php

namespace App\Http\Controllers;

use Hash;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\User;
use App\Models\RegiestToken;
use App\Models\PasswordReset;
use App\Models\LoginSt;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

use App\Custom\Common\CustomCommon;

class UserController extends Controller
{

    const S_EMAIL_USED = '邮箱已被注册，请直接登录';
    const S_REGIEST_EMAIL_SENDED = '您已提交注册申请，请到邮箱验证申请链接完成注册';
    const S_CAPTCHA_ERR = '验证码错误，请重新输入';
    const S_EMAIL_ERR = '邮箱错误，请重新输入';
    const S_RESETPWD_EMAIL_SENDED = '您已提交过申请，请到邮箱点击链接进行重置，或3分钟后再次提交申请';
    const S_TOKEN_TIMEOUT = '链接已失效，请重新提交信息';
    const S_ROUTE_REGIEST_CONFIRM = 'regiestConfirm';
    const S_ROUTE_RESETPWD_CONFIRM = 'resetPwdConfirm';
    const S_RESET_PASS_ERR_SAME = '修改失败，新密码与原密码相同';
    const S_SIGNIN_SUCC = '注册成功';
    const S_SIGNOUT_SUCC = '登出成功';
    const S_ST_ERR = 'st不存在或已失效';
    const ST_LEN = 96; // 见Customcommon::build_st
    const S_LOGOUT_ERR = '部分网站登出失败，请您离开时关闭浏览器，确保登录信息被清除';


    /**
     * 验证邮箱是否注册，发送注册邮件
     * 如果已注册或已发送邮件返回对应的错误提示
     */
    public function sendEmailRegiest (Request $request) {

        $username = $request->username;
        $email = $request->email;
        $password = bcrypt($request->password);

        // 是否有该记录
        $user = User::where('email', $email)->first();

        // 记录已存在，且已验证通过，邮箱不可用
        if (isset($user) && $user->actived == 1) {
            return CustomCommon::makeErrRes(self::S_EMAIL_USED);
        }

        // 记录存在，且存在验证token，token未失效，提示用户去点击链接
        if (isset($user)) {
            
            $tokenIns = RegiestToken::where('email', $email)->first();
            $isTimeout = (time() - $tokenIns->ctime) < config('custom.timeout.token.regiest');
            if ($isTimeout) {
                return CustomCommon::makeErrRes(self::S_REGIEST_EMAIL_SENDED);
            }

        }
        
        /*** 记录不存在，或记录存在但token已超时 ***/

        // 生成token
        $token = md5(\bcrypt(\random_bytes(32)));

        // 记录不存在，写入
        if ($user == null) {

            User::create(compact(
                'username',
                'email',
                'password',
            ));

        }

        // 记录token
        RegiestToken::updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'ctime' => time()]
        );

        // 发送邮件
        self::sendEmailConfirmtionTo([
            'email' => $email,
            'token' => $token,
            'confirmRoute' => self::S_ROUTE_REGIEST_CONFIRM,
            'msg' => '感谢您注册JD，请点击链接完成注册',
            'subject' => 'JD注册确认邮件',
        ]);

        return CustomCommon::makeSuccRes([
            'after' => route('indexPage')
        ], '邮件发送成功，请到邮箱查看');
    }


    /**
     * 修改密码发送邮件
     * 验证码不正确，邮箱不正确，token存在但未超时均不会发送邮件
     */
    public function sendEmailResetPwd (Request $request) {

        $email = $request->email;
        $captcha = strtolower($request->captcha);
        
        /***验证码是否正确，邮箱是否正确，是否已经提交申请，申请是否未超时***/

        // 验证码是否正确
        $captchaKey = $this->captchaKeyList['passwordReset'];
        $sessionCaptcha = strtolower(session($captchaKey));
        if ($captcha !== $sessionCaptcha) return CustomCommon::makeErrRes(self::S_CAPTCHA_ERR);
        

        // 邮箱是否正确
        // $user = Auth::user();
        $user = User::where('email', $email)->first();
        if ($email !== $user->email) return CustomCommon::makeErrRes(self::S_EMAIL_ERR);


        // 是否已提交申请，申请是否未超时
        $resetIns = PasswordReset::where('email', $email)->first();
        if ($resetIns != null) {

            $isTimeout = (time() - $resetIns->ctime) < config('custom.timeout.token.password_reset');
            if ($isTimeout) return CustomCommon::makeErrRes(self::S_RESETPWD_EMAIL_SENDED);

        }


        /***生成token插入数据库，发送邮件***/

        // 生成token，插入数据库
        $token = md5(bcrypt(random_bytes(20)));
        PasswordReset::updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'ctime' => time()]
        );

        // 发送邮件
        self::sendEmailConfirmtionTo([
            'email' => $email,
            'token' => $token,
            'msg' => '您正在重置密码，请点击链接进行重置',
            'subject' => '重置密码确认邮件',
            'confirmRoute' => self::S_ROUTE_RESETPWD_CONFIRM
        ]);

        return CustomCommon::makeSuccRes([
            'after' => route('indexPage')
        ], '邮件发送成功，请到邮箱查看');
    }


    /**
     * 验证注册邮件token是否有效，有效则完成注册
     * 成功重定向到之前的页面，失败返回失败界面
     */
    public function regiestConfirm (Request $request, $token) {

        // 找出对应记录
        $tokenIns = RegiestToken::where('token', $token)->first();

        /***token不存在或已失效***/
        
        // 不存在
        if ($tokenIns == null) {
            return view('email.tokenErr', ['msg' => self::S_TOKEN_TIMEOUT]);
        }

        // 失效
        $isTimeout = (time() - $tokenIns->ctime) > config('custom.timeout.token.regiest');
        if ($isTimeout) {
            return view('email.tokenErr', ['msg' => self::S_TOKEN_TIMEOUT]);
        }


        /***token可用***/

        // 找出对应数据
        $user = User::where('email', $tokenIns->email)->first();

        // 未激活，更新数据
        $user->actived = 1;
        $user->save();

        // 删除注册token
        RegiestToken::where('token', $token)->delete();

        // 帮用户登录重定向到首页
        Auth::login($user);

        // 设置session
        self::setUserSession();

        return redirect()->route('indexPage');
    }


    /**
     * 验证重置密码的token是否有效
     * 有效返回修改页面，无效返回无效界面
     */
    public function resetPwdConfirm (Request $request, $token) {

        // 找出对应记录
        $tokenIns = PasswordReset::where('token', $token)->first();

        /***token不存在或已失效***/
        
        // 不存在
        if ($tokenIns == null) {
            return view('email.tokenErr', ['msg' => self::S_TOKEN_TIMEOUT]);
        }

        // 失效
        $isTimeout = (time() - $tokenIns->ctime) > config('custom.timeout.token.password_reset');
        if ($isTimeout) {
            return view('email.tokenErr', ['msg' => self::S_TOKEN_TIMEOUT]);
        }


        /***token可用***/

        // 返回修改界面
        return view('password.form', [
            'resetPwdToken' => $token
        ]);
    }


    /**
     * 修改密码逻辑
     */
    public function changePwd (Request $request) {

        $password = $request->password;
        $token = $request->resetPwdToken;

        $tokenIns = PasswordReset::where('token', $token)->first();

        // token不存在，返回链接失效
        if ($tokenIns == null) return CustomCommon::makeErrRes(self::S_TOKEN_TIMEOUT);

        // 取出邮箱，找到记录
        $user = User::where('email', $tokenIns->email)->first();

        // 新密码与原密码相同，不允许修改
        if (Hash::check($password, $user->password)) return CustomCommon::makeErrRes(self::S_RESET_PASS_ERR_SAME);

        // 更新
        $user->password = bcrypt($password);
        $user->save();

        // 删除修改密码的token
        PasswordReset::where('token', $token)->delete();

        return CustomCommon::makeSuccRes([
            'after' => route('loginPage')
        ], '修改成功');
    }


    /**
     * 设置7天免登录的session
     * isRemember 是否记住我
     * 默认为24时时效
     */
    private static function setUserSession ($isRemember = false) {

        $key = config('custom.session.user');
        $userSession = session()->get($key);

        $userSession['id'] = Auth::user()->id;
        $userSession['timeout'] = $isRemember
            ? time() + config('custom.timeout.login.remember')
            : time() + config('custom.timeout.login.default');

        session([ $key => $userSession ]);

    }



    // 获取登录用户的一些信息，如id，头像链接等
    private function getUserInfo ($uid) {

        $user = User::find($uid);

        $id = $user->id;
        return compact('id');
    
    }


    /**
     * 验证tgc是否还可用
     */
    public function checkTgc (Request $request) {

        $session_id = $request->session_id;
        $tgc = $request->tgc;

        $ins = UserTgt::where('tgc', $tgc)->first();

        if ($ins == null) return CustomCommon::makeErrRes();

        // 更新session_id
        $ins->session_id = $session_id;
        $ins->save();

        return CustomCommon::makeSuccRes();
    }


    private function sendLogoutRequest ($logout_api, $session_id) {

        $data = CustomCommon::deJson('');
        $client = new Client;
        try {
            
            $clientRes = $client->request('POST', $logout_api, [
                'form_params' => compact('session_id')
            ]);
        
            $data = CustomCommon::deJson($clientRes->getBody());
            
        } catch (\Throwable $th) {}

        
        return ($data['status'] == 1);
    }


    private function eachRequest ($tgcList) {

        $failRequest = [];
        foreach ($tgcList as $item) {
            
            $logout_api = $item['logout_api'];
            $session_id = $item['session_id'];

            if ($this->sendLogoutRequest($logout_api, $session_id) === false) {
                array_push($failRequest, $item);
            }

        }

        return $failRequest;
    }


    /**
     * 登出
     */
    public function logout (Request $request) {

        $tgc = $request->tgc;

        // 找出该用户其他tgc
        $tgt = UserTgt::where('tgc', $tgc)
            ->first()
            ->toArray()['tgt'];

        $tgcList = UserTgt::where('tgt', $tgt)
            ->get()
            ->toArray();

        // 删除这些tgc
        UserTgt::where('tgt', $tgt)->delete();

        // 遍历每一项，发起请求
        $failRequest = $this->eachRequest($tgcList);

        // 再试一遍失败的项
        $tgcList = $failRequest;
        $failRequest = $this->eachRequest($tgcList);

        // 还有失败项，返回信息告知用户离开是关闭浏览器
        $msg = (count($failRequest) > 0)
            ? self::S_LOGOUT_ERR
            : null;

        // Auth登出
        Auth::logout();

        return CustomCommon::makeSuccRes([
            'after' => route('indexPage', compact('msg'))
        ], self::S_SIGNOUT_SUCC);
    }

    
    public function test () {
        return session()->get(config('custom.session.user'));
    }

}
