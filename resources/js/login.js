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

    let $form = $('form')
    let email = $form.find('[jshook=email]').val()
    let password = $form.find('[jshook=password]').val()
    
    $.post('/login', {
      email: util.encrypt(email),
      password: util.encrypt(password),
    }).then((res) => {
      
      const {status, msg, realMsg} = util.deJson(res)
      
      console.log(res);

    })

  })


})