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
    protected $appId = '';
    //私钥
    protected $privateKey = '';
    #实例化对象
    private static $instance;


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

    /**签名认证
     *
     * @param $priveKey [私钥]
     * @param $appId [appId]
     * @return string true|false
     */
    public function doSign($priveKey, $appId)
    {
        #解密签名过程 获取签名字符串
        $signStr = $this->getSignStr($priveKey, $appId);
        #对比验证
        return $this->checkSign($signStr);
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

    /** 简述:根据私钥生成配套的公钥
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

    /** 简述:验证
     * @params $signStr签名字符串
     * @return string
     * @return string True|False
     */
    private function checkSign($signStr)
    {
        if ($signStr === $this->privateKey)
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
        return trim(base64_decode($tmp), $appId);
    }//pf


}//class




