<?php
namespace Afuka\Helper;

use InvalidArgumentException;

/**
 *  加密处理
 */
class Cipher
{
    protected $option = OPENSSL_RAW_DATA;
    protected $method = '';
    protected $key    = '';
    protected $iv     = '';
    protected $safe   = false;

    /**
     * 实力话
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->option = intval(Arr::get($config, 'option', $this->option));
        $this->method = Arr::get($config, 'method', $this->method);
        $this->safe   = boolval(Arr::get($config, 'safe', $this->safe));
        $this->key    = Arr::get($config, 'key', '');
        $this->iv     = Arr::get($config, 'iv', $this->iv);

        if(empty($this->key)) {
            throw new InvalidArgumentException('undefined encrypt key!');
        }
        $this->method = strtolower($this->method);
        if(
            !in_array($this->method, openssl_get_cipher_methods()) 
            && !in_array($this->method, openssl_get_cipher_methods(true))
        ) {
            throw new InvalidArgumentException('invalid encrypt method!');
        }
    }

    /**
     * 加密
     * @return string 处理过的数据
     */
    public function encrypt(string $data)
    {
        try {
            $str = openssl_encrypt(
                $data, 
                $this->method, 
                $this->key,
                $this->option,
                $this->iv
            );
            $str = $this->safe ? SafeBase64::encode($str) : base64_encode($str);
            return $str;
        } catch (\Exception $th) {
            return '';
        }
    }
 
    /**
     * 解密
     * @return string 加密的字符串不是完整的会返回空字符串值
     */
    public function decrypt(string $data)
    {
        try{
            $data = $this->safe ? SafeBase64::decode($data) : base64_decode($data);

            return openssl_decrypt(
                $data,
                $this->method, 
                $this->key,
                $this->option,
                $this->iv
            );
        } catch (\Exception $e) {
            return '';
        }
    }

}