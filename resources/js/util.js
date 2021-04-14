import CryptoJS from "crypto-js"


function encrypt(text){
  var key = CryptoJS.enc.Utf8.parse('1234567890654321'); //为了避免补位，直接用16位的秘钥
  var iv = CryptoJS.enc.Utf8.parse('1234567890123456'); //16位初始向量
  var encrypted = CryptoJS.AES.encrypt(text, key, {
          iv: iv,
          mode:CryptoJS.mode.CBC,
          padding:CryptoJS.pad.Pkcs7
      });
  return encrypted.toString();
}


function decrypt (text) {
  var key = CryptoJS.enc.Utf8.parse('1234567890654321'); //为了避免补位，直接用16位的秘钥
  var iv = CryptoJS.enc.Utf8.parse('1234567890123456'); //16位初始向量
  var encrypted = CryptoJS.AES.decrypt(text, key, {
      iv: iv,
      mode:CryptoJS.mode.CBC,
      padding:CryptoJS.pad.Pkcs7

　　}); 

　　return encrypted.toString(CryptoJS.enc.Utf8); 
}


export default {
  encrypt,
  decrypt,
}