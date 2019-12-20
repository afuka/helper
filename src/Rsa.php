<?php
namespace Afuka\EasyHelper;

/**
 * Rsa 加解密
 */
class Rsa
{
    protected $private     = '';
    protected $public      = '';
    protected $safe        = false;
    protected $base64Twice = true;


    public function __construct(array $config = [])
    {
        $this->safe         = boolval(Arr::get($config, 'safe', $this->safe));
        $this->private      = Arr::get($config, 'private_key', '');
        $this->public       = Arr::get($config, 'public_key', '');
        $this->base64Twice  = boolval(Arr::get($config, 'base64_twice', $this->base64Twice));

        if(empty($this->private) || empty($this->public)) {
            throw new InvalidArgumentException('undefined encrypt key!');
        }

        // 验证key，文件还是字符串
        if(substr(strrchr($this->private, '.'), 1) == 'pem' && is_file($this->private)) {
            $this->private = file_get_contents($this->private);
        }
        if(substr(strrchr($this->public, '.'), 1) == 'pem' && is_file($this->public)) {
            $this->public = file_get_contents($this->public);
        }

    }    
    /**
     * 长字符串 私钥解密
     * @param $string
     * @return string
     */
    public function decrypt(string $string)
    {
        $crypto = '';
        //对每117个字符使用 rsa 加密，每个加密结果均为172位长度，此处需要拆分解密
        foreach (str_split($string, 172) as $chunk) {
            $chunk = $this->safe ? SafeBase64::decode($chunk) : base64_decode($chunk);
            openssl_private_decrypt($chunk, $decryptData, $this->private);
            $crypto .= $decryptData;
        }
        // 在加密前还会进行一次字符串转换，转换为base64格式，此处要进行反转
        if($this->base64Twice) {
            return $this->safe ? SafeBase64::decode($crypto) : base64_decode($crypto);
        } else {
            return $crypto;
        }
    }

    /**
     * 长字符串 公钥加密
     * @param $string
     * @return string
     */
    public function encrypt(string $string)
    {
        if($this->base64Twice) {
            $string = $this->safe ? SafeBase64::encode($string) : base64_encode($string);
        }

        $crypto = '';
        foreach (str_split($string, 117) as $chunk) {
            $section = '';
            openssl_public_encrypt($chunk, $section, $this->public);//公钥加密
            $crypto .= $this->safe ? SafeBase64::encode($section) : base64_encode($section);
        }
        return $crypto;
    }

}
