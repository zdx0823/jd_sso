import $ from 'jquery'
import _ from 'lodash'
import v from './validate'
import util from './util'

$(() => {

  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });


  let ruleFnList = []

  // 筛选出有rule的input
  let $input = $('input[rule]').filter((i, el) => {

    let ruleName = $(el).attr('rule')
    if (ruleName === '') return false
    if (v[ruleName] == null) return false

    return true

  })


  // 给input绑定时间，并记录到ruleFnList里
  $input.each((i, el) => {

    let $el = $(el)

    // 节流
    let fn = _.throttle(function () {
    
      let rule = $el.attr('rule')
      let value = $el.val()
      let {result, msg} = v[rule](value)

      let $p = $el.parent().next()
      let $span = $p.find('span')

      // 验证正确隐藏提示语，错误显示
      if (result) {
        $span.html('')
        $p.hide()
      } else {
        $span.html(msg)
        $p.show()
      }
    
      return result

    }, 500)
    

    $el.on('keyup', fn)
    ruleFnList.push(fn)
    fn = null
    
  })


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
    let username = $form.find('[jshook=username]').val()
    let email = $form.find('[jshook=email]').val()
    let password = $form.find('[jshook=password]').val()
    
    $.post('/store', {
      username,
      email: util.encrypt(email),
      password: util.encrypt(password),
    }).then((res) => {
      
      const {status, msg, realMsg} = util.deJson(res)
      
      return;
      if (status == -1) {
        util.toast(msg, 'danger')
        return
      }

      util.toast(realMsg, 'success')

      // 申请成功，锁定表单，提示用户去看邮件
      $form.find('[jshook=formShade]').show()

      isSendEmail = true
    })

  })

})