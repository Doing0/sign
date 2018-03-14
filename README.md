# 说明
## 描述
  通过公钥、私钥和appId进行加密、解密和签名的一种加密类:主要可以运用到接口调用授权中
  
**安装命令**
~~~
composer require doing/sign dev-master
~~~
## 使用步骤
> 前提在服务端调用makeKey和doSign时php文件头部已应用命名空间use Sign\Sign;

### 1.管理人员在Sign.php配置类的属性$appId最好是英文单词
### 2.服务端调用以下生成器 生成公钥和私钥
~~~
$keys= Sign::instance()->makeKey();
print_r($keys);die;
/* array(2) {
["privateKey"] => string(3) "abc"
["publicKey"] => string(3) "cde"
}*/
//把生成的privateKey私钥放在Sign.php类是属性$privateKey中
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
print_r($keys);die;
~~~
### 4.验证结果
$res验证通过返回字符串True,验证失败返回字符串False 并返给3.1
### 5.使用者在3.1中收到返回结果根据项目需求写后续操作