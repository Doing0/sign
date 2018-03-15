# 说明
## 描述一:仅用于签名
  通过公钥、私钥和appId进行加密、解密和签名的一种加密类:主要可以运用到接口调用授权中
  
**安装命令**
~~~
composer require doing/sign dev-master
~~~
## 使用步骤
> 前提:在服务端调用makeKey和doSign时php文件头部已引用命名空间use Sign\Sign;

### 1.管理人员在SignConfig.php配置类常量APPID($appId)最好是英文单词
### 2.服务端调用以下生成器 生成公钥和私钥
~~~
$keys= MakeKey::instance()->makeKey();
print_r($keys);die;
/* array(2) {
["privateKey"] => string(3) "abc"
["publicKey"] => string(3) "cde"
}*/
//把生成的privateKey私钥放在SignConfig.php类的常量PRIVATEKEY($privateKey)中
//把生成的publicKey公钥和把配置好的appId给前段开发人员(使用者)
~~~
### 3.签名和验证
#### 3.1使用者
~~~
//使用者配置好$publicKey和$appId 调用服务器写的认证签名接口,把参数pulicKey和appId通过header头的方式传递去服务器(根据需求自行封装认证接口方法)
$publicKey = 'cde';
$appId = 'appid';
~~~
#### 3.2服务器端
~~~
//通过读取3.1中header中的参数publicKey和appId调用以下方法验证
$res = Sign::instance()->doSign($publicKey,$appId);
print_r($res);die;
~~~
### 4.验证结果
$res验证通过返回字符串True,验证失败返回字符串False 并返给3.1
### 5.使用者在3.1中收到返回结果根据项目需求写后续操作

## 描述二:签名+授权(仅能在thinkphp5.0上使用)

## 使用步骤
> 前提:在服务端调用makeKey和doSign时php文件头部已引用命名空间use Sign\OAuth;
### 1.在使用之前须操作描述一中的步骤1和2配置号appId以及生成并配置好对应的公钥和私钥
### 2.调用接口者获取一次性code码
#### 2.1调用接口者通过在header上携带参数公钥和appId去掉服务器对应接口去获取一次性code码
#### 2.2 服务器通过header获取对应参数生成一次性code码并返回
~~~
 $code = OAuth::instance()->getCode($publicKey, $appId);
~~~

###3.调用接口者获取access_token
####3.1 调用接口者通过携带参数code请求相应接口获取access_token
####3.2 服务器获取到code参数并去生成access_token 并返回
~~~
 $accessToken = OAuth::instance()->getAccessToken($code);
~~~

#### 4.调用API的权限验证
####4.1调用接口者每次在调用项目API时header上都要携带access_token参数
####4.2服务器每次相应请求时都要去判断access_toen是否有权限
~~~
//$res 验证通过为true其他情况全部已抛出异常的形式处理(自行封装)
$res = OAuth::instance()->check();
当返回为true时,服务器去调客户端请求的对应API接口
~~~


