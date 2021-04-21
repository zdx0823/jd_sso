import $ from 'jquery'
import util from './util'
require('./common')

$(() => {

  // 是否有附带过来的消息
  if (window.sessionMsg) {
    util.toast(sessionMsg, 'danger')
  }
  

  $('[jshook=logout]').on('click', () => {
    
    $.post('/login/logout', {}).then(res => {

      const { realMsg, data, status } = util.deJson(res)
      if (status == -1) {
        util.toast(realMsg, 'danger')
        return
      }

      window.location.replace(data.after)
    })

  })

})