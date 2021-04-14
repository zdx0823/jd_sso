import validator from 'validator'

let message = {
  email: '邮箱格式不正确',
  username: '用户名不正确，用户名只能由字母数字组成，长度为2-16的字符'
}

function email (value) {

  let result = validator.isEmail(value)

  return {
    result,
    msg: result 
      ? ''
      : message.email
  }

}


// 中英文数字，2-16字符
function username (value) {
  
  let result = /^[0-9a-zA-Z]{2,16}$/.test(value)

  return {
    result,
    msg: result 
      ? ''
      : message.username
  }

}


// 密码，长度8-32，字母数字符号组成
function password (value) {

  let result = (value.length < 8 || value.length > 32)
  if (result) return {
    result: false,
    msg: '密码长度为8-32位'
  }

  result = /^[a-zA-Z]/.test(value)
  if (!result) return {
    result: false,
    msg: '密码必须以字母开头'
  }

  result = (Array.from(new Set(value.split(''))).length === 1)
  if (result) return {
    result: false,
    msg: '密码不能为同一字符'
  }

  return {
    result: true,
    msg: '',
  }
}


export default {
  email,
  username,
  password
}