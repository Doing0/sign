<?php
/**
 * Created by PhpStorm
 * PROJECT:签名认证sign composer包
 * User: Doing <vip.dulin@gmail.com>
 * Desc:签名认证类-授权
 */


namespace Sign;

//header('Content-Type: text/html; charset=UTF-8');


use think\Cache;

class OAuth {
    //appId
    protected $appId;
    //私钥
    protected $privateKey;
    #实例化对象
    private static $instance;

    /** 简述:初始化获取参数
     *
     * @params
     *
     */
    public function __construct()
    {
        $this->appId = SignConfig::APPID;
        $this->privateKey = SignConfig::PRIVATEKEY;
    }//pf


    /**入口实例化对象
     *
     * @param array $options
     *
     * @return array|static
     */

    public static function instance()
    {
        if (is_null(self::$instance))
        {

            self::$instance = new static();
        }

        return self::$instance;
    }//pf


    /** 简述:生成一次性code码为了获取access_token用
     *必须是当Sign.php的doSign方法成功时来自动调用此方法
     */
    private function makeCode()
    {
        #生成code
        $code = md5(md5(rand(1, 99999)));
        #写缓存 缓存时间默认8秒(可在SignConfig.php的CODE_EXPIRE配置) 并返回
        Cache::set($code, $code, SignConfig::CODE_EXPIRE);
        return $code;
    }//pf

    /** 简述:验证code码
     *
     * @params
     *
     */
    private function checkCode($code)
    {
        #读取缓存
        return Cache::get($code) ? true : false;

        #对比
    }//pf

    /** 简述:清除code
     *保证code是一次性码,用过就失效
     */
    public function clearCode($code)
    {
        return Cache::rm($code);
    }//pf

    /** 简述:获取code
     * 只有通过了签名才能请求
     */
    public function getCode($priveKey, $appId)
    {
        //假设前提是在doSign里面只能返回true 其余的情况走的是异常
        #签名验证
        Sign::instance()->doSign($priveKey, $appId);
        return $this->makeCode();
    }//pf

    /** 简述:获取access_token
     *
     */
    public function getAccessToken($code)
    {
        #code验证 过期返回false 正常返回true
        //TODO 返回false时的逻辑根据自己的项目需求书写,建议抛出异常,而不是返回false
        if ($this->checkCode($code) === false) return false;
        #清除一次性code
        $this->clearCode($code);
        # 获取accessToken并返回
        return $this->makeAccessToken();


    }//pf

    /** 简述:生成accessToken
     *
     * @params
     *
     */
    private function makeAccessToken()
    {
        #生成
        $accessToken = md5(md5(rand(1, 99999) . time()));
        #写缓存 缓存时间默认7200秒(可在SignConfig.php的ACCESS_TOKEN_EXPIRE配置) 并返回
        Cache::set($accessToken, $accessToken, SignConfig::ACCESS_TOKEN_EXPIRE);
        return $accessToken;
    }//pf

    /** 简述:授权验证accessToken
     *建议在调接口初始化的时候调用,保证每个接口在调用时有权限调用接口
     * access_token建议通过header传递
     *
     * @params access_token建议通过header传递
     *
     */
    public function check()
    {
        #获取
        $access_token = request()->header('access_token');
        #验证是后存在
        $res = $this->checkAccessToken($access_token);
        if ($res === true) return true;
        //TODO 返回false的逻辑根据自己的项目需求书写,建议抛出异常


    }//pf

    /** 简述:验证accesstoken
     *
     * @params
     *
     */
    private function checkAccessToken($access_token)
    {
        #读取缓存
        return Cache::get($access_token) ? true : false;

    }//pf


}//class




