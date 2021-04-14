import $ from 'jquery'
import _ from 'lodash'
import v from './validate'
import CryptoJS from "crypto-js"


// aes-128-cbc 加密
function encrypt (text) {

  let csrfToken = $('meta[name="csrf-token"]').attr('content')
  let tKey = csrfToken.slice(0, 16)
  let tIv = tKey
  
  var key = CryptoJS.enc.Utf8.parse(tKey); //为了避免补位，直接用16位的秘钥
  var iv = CryptoJS.enc.Utf8.parse(tIv); //16位初始向量
  var encrypted = CryptoJS.AES.encrypt(text, key, {
          iv: iv,
          mode:CryptoJS.mode.CBC,
          padding:CryptoJS.pad.Pkcs7
      });
  return encrypted.toString();

}



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
$('form input[type=submit]').on('click', function (e) {
  
  e.preventDefault()

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
    email: encrypt(email),
    password: encrypt(password),
  }).then(() => {
    console.log(233);
  })

  
})

