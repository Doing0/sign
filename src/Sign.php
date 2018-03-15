<?php
/**
 * Created by PhpStorm
 * PROJECT:签名认证sign composer包
 * User: Doing <vip.dulin@gmail.com>
 * Desc:签名认证类
 */


namespace Sign;
//header('Content-Type: text/html; charset=UTF-8');


class Sign {
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
    }


    /**签名认证
     *
     * @param $publicKey [公钥]
     * @param $appId [appId]
     *
     * @return string true|false
     */
    public function doSign($publicKey, $appId)
    {
        #解密签名过程 获取签名字符串 有拼接appId
        $signStr = $this->getSignStr($publicKey, $appId);
        #对比验证
        $res = $this->checkSign($signStr);
        if ($res == true) return true;
        //TODO 验证通过和未通过的逻辑可自己根据项目写

    }


    /** 简述:验证
     * @params $signStr签名字符串
     * @return string
     * @return string True|False
     */
    private function checkSign($signStr)
    {
        if ($signStr === $this->privateKey . $this->appId)
        {
            return 'True';
        }else
        {
            return 'False';
        }
    }//pf

    /** 简述:获取签名字符串
     * @return string
     */
    private function getSignStr($priveKey, $appId)
    {
        $priveKey = base64_decode(urldecode($priveKey));
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
        $ch = $priveKey[0];
        $nh = strpos($chars, $ch);
        $mdKey = md5($appId . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $priveKey = substr($priveKey, 1);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($priveKey); $i++)
        {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars, $priveKey[$i]) - $nh - ord($mdKey[$k++]);
            while ($j < 0) $j += 64;
            $tmp .= $chars[$j];
        }
        return trim(base64_decode($tmp), $appId) . $appId;
    }//pf


}//class




