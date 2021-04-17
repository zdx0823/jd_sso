import $ from 'jquery'
import util from './util'
require('./common')

$(() => {

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