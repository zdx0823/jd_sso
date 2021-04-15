import $ from 'jquery'
import _ from 'lodash'
import util from './util'
import { bindRule } from './common'


$(() => {


  // 绑定验证规则
  let ruleFnList = bindRule()


  // 侦听提交按钮，提交前检查参数
  let isSendEmail = false
  $('form input[type=submit]').on('click', function (e) {
    
    e.preventDefault()

    // 如果已提交过表单且邮件发送成功，不允许重复提交
    if (isSendEmail) return

    let isFormOk = true
    ruleFnList.forEach(fn => {
      isFormOk = fn()
    })

    // 参数有问题
    if (!isFormOk) return

    let $form = $('form')
    let email = $form.find('[jshook=email]').val()
    let password = $form.find('[jshook=password]').val()
    let captcha = $form.find('[jshook=captcha]').val()

    isSendEmail = true
    $.post('/login', {
      email: util.encrypt(email),
      password: util.encrypt(password),
      captcha
    }).then((res) => {
      
      const {status, msg, realMsg} = util.deJson(res)
      if (status === -1) {
        util.toast(realMsg, 'danger')
        isSendEmail = false
        return
      }

      // 登录成功，锁定表单
      $form.find('[jshook=formShade]').show()
      util.toast(msg, 'success')
      isSendEmail = true

    })

  })


  // 验证码点击事件
  $('[jshook=captchaImg]').on('click', function () {
    let $el = $(this)

    let time = Date.now()
    let src = "/captcha?captchaType=login&w=128&h=48#" + time
    $el.find('img').attr('src', src)

  })

})