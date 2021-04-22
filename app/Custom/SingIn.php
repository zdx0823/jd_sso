<?php
namespace App\Custom\SingIn;

use Auth;
use Cookie;

use App\Models\LoginSt;

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
   * 尝试登录，调用Auth::attempt方法登录
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
   * 生成Tgt并存入cookie
   */
  private static function generateTgt ($request) {

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
   * 生成ST
   * 生成重定向的url
   */
  public static function getRedirectUrl ($tgt, $timeout, $tPrevServe = null) {

      $skey = config('custom.session.user');
      $userSession = session()->get($skey);
      $prevServe = $tPrevServe
        ? $tPrevServe
        : (
            isset($userSession['prevServe']) ? 
                $userSession['prevServe']
                : null
        );

      if ($prevServe) {

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
              'serve' => CustomCommon::getUrlWithPort($prevServe)
          ]);
  
          // 拼接url
          $afterUrl = CustomCommon::appendQuery($prevServe, compact('st'));

      } else {

          $afterUrl = route('indexPage');
      }

      return $afterUrl;
  }


  /**
   * 尝试登录，生成tgt，生成重定向的url
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
      ] = self::generateTgt($request);

      // 取得重定向链接
      $after = self::getRedirectUrl($tgt, $timeout);

      return CustomCommon::makeSuccRes(compact('after'), self::S_SIGNIN_SUCC);
  }
}
