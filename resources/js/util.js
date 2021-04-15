import CryptoJS from "crypto-js"
import $ from 'jquery'
window.jQuery = $
require('jquery-toast-plugin')
require('jquery-toast-plugin/dist/jquery.toast.min.css')

// aes-128-cbc加密，使用laravel的csrf_token做密钥和初始向量
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


// function decrypt (text) {
//   var key = CryptoJS.enc.Utf8.parse('1234567890654321'); //为了避免补位，直接用16位的秘钥
//   var iv = CryptoJS.enc.Utf8.parse('1234567890123456'); //16位初始向量
//   var encrypted = CryptoJS.AES.decrypt(text, key, {
//       iv: iv,
//       mode:CryptoJS.mode.CBC,
//       padding:CryptoJS.pad.Pkcs7

// 　　}); 

// 　　return encrypted.toString(CryptoJS.enc.Utf8); 
// }


// 包装toast插件
function toast (msg, type = 'info') {

  let colorList = {
    success: '#059669',
    danger: '#DC2626',
    info: '#4B5563',
    warning: '#D97706'
  }

  let loaderBgList = {
    success: '#10B981',
    danger: '#EF4444',
    info: '#6B7280',
    warning: '#F59E0B'
  }

  let bgColor = colorList[type] ? colorList[type] : colorList['info']
  let loaderBg = loaderBgList[type] ? loaderBgList[type] : loaderBgList['info']

  $.toast({

    text: msg,
    showHideTransition: 'fade',
    allowToastClose: true,
    hideAfter: 3000,
    stack: false,
    position: 'top-center',
    bgColor,
    textColor: '#ffffff',
    textAlign: 'center',
    loader: true,
    loaderBg,
  
  });

}


// 尝试解析json，解析失败status置为-1
function deJson (res) {

  if (typeof res === 'object') return res

  let data = {}
  
  try {
    data = JSON.parse(res)
  } catch (error) {
    data = {
      status: -1,
      msg: '服务错误，请重试'
    }
  }

  return data
}


export default {
  encrypt,
  toast,
  deJson,
}