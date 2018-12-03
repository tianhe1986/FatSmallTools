# FatSmallTools
一系列小工具，用来做些奇怪的事情。

# 安装
确保PHP版本在7.0以上。

推荐通过[Composer](https://getcomposer.org/)进行安装

Composer的安装请参考官方配置。

安装好Composer后，在你的项目中创建一个 composer.json 文件：
```json
{
    "require": {
        "tianhe1986/fatsmalltools": "*"
    }
}
```

然后在项目文件夹下执行:
```bash
$ composer install
```

之后添加autoloader:
```php
<?php
require "vendor/autoload.php";
```

# navicat密码保存加解密
原理参考 [how-does-navicat-encrypt-password](https://github.com/DoubleLabyrinth/how-does-navicat-encrypt-password)，有兴趣的可以自己研究下^^  

使用方法如下：
```php
use FatSmallTools\NavicatPassword;

//需要指定版本，11或12
$navicatPassword = new NavicatPassword(11);
//$navicatPassword = new NavicatPassword(12);

//加密
$encode = $navicatPassword->encrypt('123456'); // verstion 11 15057D7BA390, version 12 833E4ABBC56C89041A9070F043641E3B

//解密
$decode = $navicatPassword->encrypt('15057D7BA390');
//$decode = $navicatPassword->encrypt('15057D7BA390');
```
