import $ from 'jquery'
import v from './validate'

// laravel的csrf_token
const csrfToken = $('meta[name="csrf-token"]').attr('content')

$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': csrfToken
  }
})

$.csrfToken = csrfToken

/**
 * 绑定规则到input上
 *  查找具有rule属性并且不为空的input，给keyup绑定事件，根据rule值执行对应验证方法。
 *  验证失败显示提示语，验证成功关闭提示语
 * @returns 规则方法数组
 */
function bindRule () {

  let ruleFnList = []

  // 筛选出有rule的input
  let $input = $('input[rule]').filter((i, el) => {

    let ruleName = $(el).attr('rule')
    if (ruleName === '') return false
    if (v[ruleName] == null) return false

    return true

  })


  // 给input绑定事件，并记录到ruleFnList里
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

  return ruleFnList
}


export {
  bindRule
}