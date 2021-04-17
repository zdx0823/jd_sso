<?php

namespace App\Http\Controllers;

use Hash;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\User;
use App\Models\RegiestToken;
use App\Models\PasswordReset;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
    const S_ACCOUNT_ERR = '账号或密码错误，请重新输入';
    const S_SIGNUP_SUCC = '登录成功';
    const S_SIGNIN_SUCC = '注册成功';
    const S_SIGNOUT_SUCC = '登出成功';


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
            $isTimeout = (time() - $tokenIns->ctime) < env('REGIEST_TOKEN_TIMEOUT');
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

            $isTimeout = (time() - $resetIns->ctime) < env('PASSWORD_RESET_TIMEOUT');
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
        $isTimeout = (time() - $tokenIns->ctime) > env('REGIEST_TOKEN_TIMEOUT');
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
        $isTimeout = (time() - $tokenIns->ctime) > env('PASSWORD_RESET_TIMEOUT');
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


    // 登录逻辑
    public function singIn (Request $request) {

        $email = $request->email;
        $password = $request->password;
        $captcha = strtolower($request->captcha);
        $remember = boolval($request->remember);

        // 验证码是否正确
        $captchaKey = $this->captchaKeyList['login'];
        $sessionCaptcha = strtolower(session($captchaKey));
        if ($captcha !== $sessionCaptcha) {
            return CustomCommon::makeErrRes(self::S_CAPTCHA_ERR);
        }

        // 尝试登录
        $res = Auth::attempt([
            'email' => $email,
            'password' => $password,
            'actived' => 1
        ]);

        // 用户不存在
        if (!$res) {
            return CustomCommon::makeErrRes(self::S_ACCOUNT_ERR);
        }

        // 设置登录超时时间
        $user = Auth::user();
        $timeout = $remember
            ? time() + env('LOGIN_REMEMBER_TIMEOUT')
            : time() + env('LOGIN_TIMEOUT');

        $user->rememberToken = $timeout;
        $user->save();

        return CustomCommon::makeSuccRes([
            'after' => route('indexPage')
        ], self::S_SIGNUP_SUCC);
    }


    /**
     * 登出
     */
    public function logout () {

        Auth::logout();
        return CustomCommon::makeSuccRes([
            'after' => route('indexPage')
        ], self::S_SIGNOUT_SUCC);
    }

    
    public function test () {
        // self::captcha();
    }

}
