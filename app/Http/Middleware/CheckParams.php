<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Custom\Common\CustomCommon;

class CheckParams
{

    private function decrypt ($encrypted) {
        $key = $iv = substr(csrf_token(), 0, 16);
        return openssl_decrypt($encrypted, 'aes-128-cbc', $key, 0 , $iv);
    }

    /**
     * 构建错误返回信息
     * $res Validator::make返回的实例
     * $result  返回值，数组。msg为简单的信息，realMsg真实信息，msgArr关联数组
     */
    public function makeErrRes ($res) {
        $str = '';
        $arr = [];
        $msgArr = $res->errors()->toArray();
        foreach ($msgArr as $key => $val) {
            $arr[$key] = $val[0];
            $str .= ($val[0] . '\n');
        }
        $str = rtrim($str, '\n');

        $result = CustomCommon::makeErrRes($str, $msgArr);
        return $result;
    }


    /**
     * 注册发送邮件参数检查
     * 检查项：email, password,
     * 密码为长度2-16的字符，不能全部重复，不能数字开头
     */
    private function sendEmailRegiest ($request) {

        // query字段
        $validateData = $request->input();

        // 邮箱和密码是rsa加密字段，解密后再判断值
        $validateData['email'] = isset($validateData['email'])
            ? $this->decrypt($validateData['email'])
            : null;

        $validateData['password'] = isset($validateData['password'])
            ? $this->decrypt($validateData['password'])
            : null;

        
        $res = Validator::make($validateData, [
            'username' => 'bail|alpha_num|between:2,16',
            'email' => 'bail|required|email',
            'password' => '$password',
        ], [
            '$password' => '密码由字母数字符号组成，且由字母开头不重复的2-16位字符'
        ]);


        if ($res->fails() !== false) return $this->makeErrRes($res);
        
        $request->email = $validateData['email'];
        $request->password = $validateData['password'];

        return true;
    }


    /**
     * 修改密码发送邮件参数检查
     * 检查项：email, captcha
     */
    private function sendEmailResetPwd ($request) {

        // query字段
        $validateData = $request->input();

        // 邮箱和密码是rsa加密字段，解密后再判断值
        $validateData['email'] = isset($validateData['email'])
            ? $this->decrypt($validateData['email'])
            : null;

        $res = Validator::make($validateData, [
            'email' => 'bail|required|email',
            'captcha' => 'required'
        ]);

        if ($res->fails() !== false) return $this->makeErrRes($res);

        $request->email = $validateData['email'];

        return true;
    }


    /**
     * 新密码参数检查
     * 检查项：两次密码必须一致
     */
    private function changePwd ($request) {

        // query字段
        $validateData = $request->input();

        // 邮箱和密码是rsa加密字段，解密后再判断值
        $validateData['pass1'] = isset($validateData['pass1'])
            ? $this->decrypt($validateData['pass1'])
            : null;

        $validateData['pass2'] = isset($validateData['pass2'])
            ? $this->decrypt($validateData['pass2'])
            : null;

        $res = Validator::make($validateData, [
            'pass1' => 'bail|required|$password',
            'pass2' => 'bail|required|same:pass1'
        ]);

        if ($res->fails() !== false) return $this->makeErrRes($res);

        $request->password = $validateData['pass1'];

        return true;
    }


    private function captcha ($request) {
        
        // query字段
        $validateData = $request->input();
        
        $res = Validator::make($validateData, [
            'w' => 'numeric|min:1',
            'h' => 'numeric|min:1',
            'captchaType' => 'required|in:login,passwordReset',
        ]);

        if ($res->fails() !== false) return $this->makeErrRes($res);

        $request->w = isset($request->w) ? $request->w : 100;
        $request->h = isset($request->h) ? $request->h : 40;

        return true;
    }


    private function singIn ($request) {

        // query字段
        $validateData = $request->input();

        // 邮箱和密码是rsa加密字段，解密后再判断值
        $validateData['email'] = isset($validateData['email'])
            ? $this->decrypt($validateData['email'])
            : null;

        $validateData['password'] = isset($validateData['password'])
            ? $this->decrypt($validateData['password'])
            : null;

        $res = Validator::make($validateData, [
            'email' => 'bail|required|email',
            'captcha' => 'required'
        ]);

        if ($res->fails() !== false) return $this->makeErrRes($res);

        $request->email = $validateData['email'];
        $request->password = $validateData['password'];

        return true;
    }


    private function checkTgc ($request) {

        $validateData = $request->input();

        $res = Validator::make($validateData, [
            'tgc' => 'bail|required',
            'session_id' => 'bail|required'
        ]);

        if ($res->fails() !== false) return $this->makeErrRes($res);

        return true;

    }

    
    /**
     * 检索出路由名，路由名即此类的方法名，如果返回非true值就是参数错误
     * 路由名对应的方法，接收一个$request，返回true或一个数组
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();
        $res = $this->$routeName($request);
        if ($res !== true) {
            return response()->json($res);
        }
        
        return $next($request);
    }
}
