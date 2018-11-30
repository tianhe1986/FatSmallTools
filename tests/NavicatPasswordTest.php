<?php

namespace FatSmallTools\Tests;

use PHPUnit\Framework\TestCase;
use FatSmallTools\NavicatPassword;

class NavicatPasswordTest extends TestCase
{
    public function testHandleTwelve()
    {
        $navicatPassword = new NavicatPassword(12);
        $string = '123456';
        $encrypted = $navicatPassword->encrypt($string);
        $this->assertEquals('833E4ABBC56C89041A9070F043641E3B', $encrypted);
        $decrypted = $navicatPassword->decrypt($encrypted);
        $this->assertEquals($string, $decrypted);
        
        $string = 'This is a test';
        $encrypted = $navicatPassword->encrypt($string);
        $this->assertEquals('B75D320B6211468D63EB3B67C9E85933', $encrypted);
        $decrypted = $navicatPassword->decrypt($encrypted);
        $this->assertEquals($string, $decrypted);
    }
    
    public function testHandleEleven()
    {
        $navicatPassword = new NavicatPassword(11);
        $string = '123456';
        $encrypted = $navicatPassword->encrypt($string);
        $this->assertEquals('15057D7BA390', $encrypted);
        $decrypted = $navicatPassword->decrypt($encrypted);
        $this->assertEquals($string, $decrypted);
        
        $string = 'This is a test';
        $encrypted = $navicatPassword->encrypt($string);
        $this->assertEquals('0EA71F51DD37BFB60CCBA219BE3A', $encrypted);
        $decrypted = $navicatPassword->decrypt($encrypted);
        $this->assertEquals($string, $decrypted);
    }
}