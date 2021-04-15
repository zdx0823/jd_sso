<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Custom\Common\CustomCommon;
use Mail;

class RegiestController extends Controller
{

    public function createPage () {
        return view('regiest');
    }


    // 发送邮件
    protected function sendEmailConfirmtionTo ($user) {
        $view = 'test';
        $data = compact('user');
        $name = 'JD';
        $to = $user->email;
        $subject = "感谢注册 JD 应用！请确认你的邮箱。";
        Mail::send($view, $data, function ($message) use ($name, $to, $subject) {
            $message->to($to)->subject($subject);
        });
    }


    public function store (Request $request) {

        [
            'username' => $username,
        ] = $request->input();

        $username = $request->username;
        $email = $request->email;
        $password = bcrypt($request->password);

        // 邮箱是否已注册
        $user = User::where('email', $email)
            ->where('email_verified_at', '<>', null)
            ->first();
        if ($user != null) {

            // 已提交信息，但未认证邮箱，且认证未超时
            if (!$user->isEmailTimeout) {
                CustomCommon::makeErrRes('邮箱已被用于注册，但未认证，请到邮箱查看邮件并点击链接认证');
            }
            
        }

        // 邮箱不存在，可用
        // 写入数据库
        $user = User::create(compact(
            'username',
            'email',
            'password',
        ));

        $this->sendEmailConfirmtionTo($user);

        return $email;
    }

}
