<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Gregwar\Captcha\PhraseBuilder;
use Gregwar\Captcha\CaptchaBuilder;
use Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $captchaKeyList = [
        'login' => 'captcha_login',
        'passwordReset' => 'captcha_passwordRest',
    ];


    // 生成验证码
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


    // 验证码路由
    public function captcha (Request $request) {

        $w = $request->w;
        $h = $request->h;
        $captchaType = $request->captchaType;

        self::buildCaptcha($w, $h, function ($code) use ($captchaType) {
            $key = $this->captchaKeyList[$captchaType];
            session([
                $key => $code
            ]);
        });

    }


    // 发送邮件
    public static function sendEmailConfirmtionTo ($params) {

        [
            'email' => $email,
            'token' => $token,
            'confirmRoute' => $confirmRoute,
            'msg' => $msg,
            'subject' => $subject,
        ] = $params;

        $view = 'email.token';
        $name = 'JD';
        $to = $email;
        $data = compact('token', 'confirmRoute', 'msg');

        Mail::send($view, $data, function ($message) use ($name, $to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

}
