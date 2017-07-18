<?php
/**
 * Created by PhpStorm.
 * User: wangzhiyuan
 * Date: 2016/11/8
 * Time: 下午2:20
 */

namespace App\Models;
use anlutro\cURL\Request;
use CUrl;
use Log;

class RsaCrypt {

    const PRIVATE_KEY_FILE_PATH = 'Certificate/rsa_private_key.pem';
    const PUBLIC_KEY_FILE_PATH = 'Certificate/rsa_public_key.pem';


    public static function private_key_path(){
        return app_path().'/'.self::PRIVATE_KEY_FILE_PATH;
    }

    public static function public_key_path(){
        return app_path().'/'.self::PUBLIC_KEY_FILE_PATH;
    }


    public static function public_encode($orignData){
        //公钥文件的路径
        $publicKeyFilePath = self::public_key_path();//self::PUBLIC_KEY_FILE_PATH;

        extension_loaded('openssl') or die('php需要openssl扩展支持');

        (file_exists($publicKeyFilePath)) or die('公钥的文件路径不正确');

        //生成Resource类型的公钥，如果公钥文件内容被破坏，openssl_pkey_get_public函数返回false
        $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyFilePath));

        ($publicKey) or die('公钥不可用');

        //加密以后的数据，用于在网路上传输
        $encryptData = '';

        ///////////////////////////////用公钥加密////////////////////////
        if (openssl_public_encrypt($orignData, $encryptData, $publicKey)) {
            return $encryptData;
        } else {
            die('加密失败');
        }

    }


    public static function private_decode($encryptData){
        //密钥文件的路径
        $privateKeyFilePath = self::private_key_path();//self::PRIVATE_KEY_FILE_PATH;

        extension_loaded('openssl') or die('php需要openssl扩展支持');

        (file_exists($privateKeyFilePath)) or die('密钥的文件路径不正确');

        //生成Resource类型的密钥，如果密钥文件内容被破坏，openssl_pkey_get_private函数返回false
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyFilePath));

        ($privateKey) or die('密钥不可用');

        //解密以后的数据
        $decryptData = '';

        ///////////////////////////////用私钥解密////////////////////////
        if (openssl_private_decrypt($encryptData, $decryptData, $privateKey)) {
            return $decryptData;
        } else {
            die('解密失败');
        }
    }


    /**
     * Rsa加密
     * @param string $orignData
     * @return string
     */
    public static function encode($orignData) {
        //密钥文件的路径
        $privateKeyFilePath = self::private_key_path();//self::PRIVATE_KEY_FILE_PATH;

        extension_loaded('openssl') or die('php需要openssl扩展支持');

        (file_exists($privateKeyFilePath)) or die('密钥的文件路径不正确');

        //生成Resource类型的密钥，如果密钥文件内容被破坏，openssl_pkey_get_private函数返回false
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyFilePath));

        ($privateKey) or die('密钥不可用');

        //加密以后的数据，用于在网路上传输
        $encryptData = '';

        ///////////////////////////////用私钥加密////////////////////////
        if (openssl_private_encrypt($orignData, $encryptData, $privateKey)) {
            return $encryptData;
        } else {
            die('加密失败');
        }
    }

    /**
     * Rsa解密
     * @param string $encryptData
     * @return string
     */
    public static function decode($encryptData) {
        //公钥文件的路径
        $publicKeyFilePath = self::public_key_path();//self::PUBLIC_KEY_FILE_PATH;

        extension_loaded('openssl') or die('php需要openssl扩展支持');

        (file_exists($publicKeyFilePath)) or die('公钥的文件路径不正确');

        //生成Resource类型的公钥，如果公钥文件内容被破坏，openssl_pkey_get_public函数返回false
        $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyFilePath));

        ($publicKey) or die('公钥不可用');

        //解密以后的数据
        $decryptData = '';

        ///////////////////////////////用公钥解密////////////////////////
        if (openssl_public_decrypt($encryptData, $decryptData, $publicKey)) {
            return $decryptData;
        } else {
            die('解密失败');
        }
    }

}