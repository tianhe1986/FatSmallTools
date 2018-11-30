<?php

namespace FatSmallTools;

class NavicatPassword
{
    protected $version = 0;
    protected $aesKey = 'libcckeylibcckey';
    protected $aesIv = 'libcciv libcciv ';
    
    public function __construct($version = 12)
    {
        $this->version = $version;
    }
    
    public function encrypt($string)
    {
        $result = FALSE;
        switch ($this->version) {
            case 11:
                $result = $this->encryptEleven($string);
                break;
            case 12:
                $result = $this->encryptTwelve($string);
                break;
            default:
                break;
        }
        
        return $result;
    }
    
    protected function encryptEleven($string)
    {
        
    }
    protected function encryptTwelve($string)
    {
        $result = openssl_encrypt($string, 'AES-128-CBC', $this->aesKey, OPENSSL_RAW_DATA, $this->aesIv);
        return strtoupper(bin2hex($result));
    }

    public function decrypt($string)
    {
        $result = FALSE;
        switch ($this->version) {
            case 11:
                $result = $this->decryptEleven($string);
                break;
            case 12:
                $result = $this->decryptTwelve($string);
                break;
            default:
                break;
        }
        
        return $result;
    }
    
    protected function decryptEleven($string)
    {
        
    }
    protected function decryptTwelve($string)
    {
        $data = hex2bin(strtolower($string));
        return openssl_decrypt($data, 'AES-128-CBC', $this->aesKey, OPENSSL_RAW_DATA, $this->aesIv);
    }
}