<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Custom\Common\CustomCommon;
use Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Support\Facades\Auth;

class RegiestController extends Controller
{

    public function create () {
        return view('regiest');
    }


    // 发送邮件
    protected function sendEmailConfirmtionTo ($email, $token) {
        $view = 'email.token';
        $data = compact('token');
        $name = 'JD';
        $to = $email;
        $subject = "感谢注册 JD 应用！请确认你的邮箱。";
        Mail::send($view, $data, function ($message) use ($name, $to, $subject) {
            $message->to($to)->subject($subject);
        });
    }


    // 生成token，使用aes，把数据转成json字符串后加密
    protected function buildEmailToken ($id) {

        $token = Crypt::encryptString($id);
        return $token;   

    }


    // 解密，把token转成数据，返回id
    protected function deEmailToken ($token) {
        
        $id = null;
        try {
            $id = Crypt::decryptString($token);
        } catch (\Throwable $th) {}

        return $id;     
    }


    // 验证邮箱是否注册，发送注册邮件
    public function store (Request $request) {

        $username = $request->username;
        $email = $request->email;
        $password = bcrypt($request->password);

        // 是否有该记录
        $user = User::where('email', $email)->first();

        // 记录已存在，且已验证通过，邮箱不可用
        if (isset($user) && $user->email_verified_at != null) {
            return CustomCommon::makeErrRes('邮箱已被注册，请直接登录');
        }

        // 记录存在且验证时效未超时，提示去邮箱验证链接
        if (isset($user) && time() - $user->mtime < env('VERIFIED_EMAIL_TOKEN_TIMEOUT')) {
            return CustomCommon::makeErrRes('您已提交注册申请，请到邮箱验证申请链接完成注册');
        }
        

        // 记录不存在，或记录存在但验证已超时

        // 记录不存在，写入
        if ($user == null) {

            $id = User::create(compact(
                'username',
                'email',
                'password',
            ))->id;

        } else {

            // 记录存在但验证超时，更新
            DB::table('users')
                ->where('email', $email)
                ->update([
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'mtime' => time()
                ]);

            $id = $user->id;
        }

        // 生成token
        $token = $this->buildEmailToken($id);

        // 发送邮件
        $this->sendEmailConfirmtionTo($email, $token);
        return CustomCommon::makeSuccRes([], '邮件发送成功，请到邮箱查看');
    }


    // 验证注册邮件token是否有效，有效则完成注册
    public function confirm (Request $request, $token) {
        
        $id = $this->deEmailToken($token);

        // token解析失败，返回token无效
        if ($id == null) {
            $msg = '链接已失效，请重新提交信息';
            return view('email.tokenErr', compact('msg'));
        }

        // 记录是否存在
        $user = User::find($id);

        // 记录不存在或已激活，返回token无效
        if ($user == null || $user->actived > 0) {
            // 记录不存在，token无效
            $msg = '链接已失效，请重新提交信息';
            return view('email.tokenErr', compact('msg'));
        }


        // 未激活，更新数据
        $user->actived = 1;
        $user->save();

        // 重定向到之前页面或首页
    }


    public static function buildCaptcha ($width = 100, $height = 40, $fn = null) {

        $key = env('APP_KEY');
        $phrase = new PhraseBuilder;

        // 设置验证码位数
        $code = $phrase->build(4);

        // 生成验证码图片的Builder对象,配置相应属性
        $builder = new CaptchaBuilder($code, $phrase);

        // 设置背景颜色
        $builder->setBackgroundColor(96, 165, 250);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(10);
        $builder->setMaxFrontLines(10);

        // 可以设置图片宽高及字体
        $builder->build($width, $height, $font = null);

        // 获取验证码的内容
        $phrase = $builder->getPhrase();

        // 执行回调
        if (isset($fn)) {
            $fn($phrase);
        }

        // 生成图片
        header('Cache-Control: no-cache, must-revalidate');
        header('content-type: image/jpeg');
        $builder->output();
    }


    public function test () {
        // self::captcha();
    }


    public function captcha (Request $request) {

        $w = $request->w;
        $h = $request->h;
        $captchaType = $request->captchaType;

        self::buildCaptcha($w, $h, function ($code) use ($captchaType) {
            session([
                "captcha_$captchaType" => $code
            ]);
        });
    }


    // 登录页面
    public function loginPage (Request $request) {
        return view('login', [
            'type' => 'login'
        ]);
    }


    // 登录逻辑
    public function login (Request $request) {

        $email = $request->email;
        $password = $request->password;
        $captcha = strtolower($request->captcha);
        $remember = boolval($request->remember);

        // 验证码是否正确
        $sessionCaptcha = strtolower(session("captcha_login"));
        if ($captcha !== $sessionCaptcha) {
            return CustomCommon::makeErrRes('验证码不正确，请重新输入');
        }

        // 尝试登录
        $res = Auth::attempt([
            'email' => $email,
            'password' => $password,
            'actived' => 1
        ]);

        // 用户不存在
        if (!$res) {
            return CustomCommon::makeErrRes('账号或密码错误，请重新输入');
        }

        // 设置登录超时时间
        $user = Auth::user();
        $timeout = $remember
            ? time() + env('LOGIN_REMEMBER_TIMEOUT')
            : time() + env('LOGIN_TIMEOUT');

        $user->rememberToken = $timeout;
        $user->save();

        return CustomCommon::makeSuccRes([], '登录成功');
    }
}
