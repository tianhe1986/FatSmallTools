<?php

namespace FatSmallTools;

class NavicatPassword
{
    protected $version = 11;
    
    public function __construct($version = 11)
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
        
    }
}