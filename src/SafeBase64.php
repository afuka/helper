<?php
namespace Afuka\EasyHelper;

/**
 *  url 安全 base64
 */
class SafeBase64
{
    /**
     * 编码
     *
     * @param string $string
     * @return string
     */
    public static function encode($string) 
    {
        $data = base64_encode($string);
        $data = str_replace(['+', '/', '='], ['-', '_', ''], $data);
        return $data;
    } 
     
    /**
     * 解码
     *
     * @param string $string
     * @return string
     */
    public static function decode($string) 
    {
        $data = str_replace(['-', '_'], ['+', '/'], $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
     }
}