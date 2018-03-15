<?php
/**
 * Created by PhpStorm
 * PROJECT:签名认证sign composer包
 * User: Doing <vip.dulin@gmail.com>
 * Desc:签名认证配置文件
 */

namespace Sign;
class SignConfig {
    //自定义AppId
    CONST APPID = 'appid';
//生成的私钥
    CONST PRIVATEKEY = '7099602418.18211b0a1d150611a80456';
    //code过期时间 单位秒
    CONST CODE_EXPIRE = 8;
    //accessToken过期时间 单位秒
    CONST ACCESS_TOKEN_EXPIRE = 7200;
}


