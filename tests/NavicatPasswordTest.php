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
    }
}