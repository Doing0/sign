<?php
/**
 * Created by PhpStorm
 * PROJECT:签名认证sign composer包
 * User: Doing <vip.dulin@gmail.com>
 * Desc:签名认证类-生成公钥和私钥工具
 */


namespace Sign;
//header('Content-Type: text/html; charset=UTF-8');


class MakeKey {
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


    /**生成私钥和配套的公钥
     * @return mixed
     *
     */
    public function makeKey()
    {
        #私钥
        $this->privateKey = $this->makePriveKey();
        #公钥
        $publicKey = $this->makePublicKey();
        #拼接数据返回
        $data['privateKey'] = $this->privateKey;
        $data['publicKey'] = $publicKey;
        return $data;

    }


    /** 简述:生成私钥
     * @return string
     */
    private function makePriveKey()
    {
        //生成唯一uniqid 前缀时间戳,返回值末尾的更多的熵到23位
        //在srt_shuffle随机打乱字符串;
        return str_shuffle(uniqid(time(), true));
    }//pf

    /** 简述:根据私钥和appId生成配套的公钥
     * @return string
     */
    private function makePublicKey()
    {
        $privateKey = $this->privateKey;
        $appId = $this->appId;
        $privateKey = $privateKey . $appId;
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
        $nh = rand(0, 64);
        $ch = $chars[$nh];
        $mdKey = md5($appId . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $privateKey = base64_encode($privateKey);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($privateKey); $i++)
        {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh + strpos($chars, $privateKey[$i]) + ord($mdKey[$k++])) %
                64;
            $tmp .= $chars[$j];
        }
        return urlencode(base64_encode($ch . $tmp));
    }//pf

}//class




