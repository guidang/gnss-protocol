中国交通部GPS车载终端通讯协议解析
------

## 安装
```
composer require skiy/gnss-protocol:dev-master
```

## 使用
```php
require 'vendor/autoload.php';

//位置
$str = '07040033058032343758004d000101002e00000000000c0001000000000000000000000000000018121308573725040000000001040000000030011f310100f9';
//注册
$str = '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

$gps = new \ChinaGnss\Gps();

//是否使用默认协议分析消息体(默认为true)
$gps->setAuto(false);

//拆分、分析消息
$gps->analytical($str);

//获取消息类
$msg = $gps->getMessage();
var_dump($msg);

//获取消息体内容
$info = $gps->getInfo();
var_dump($info);

//获取应答消息
$reply_info = $gps->reply(4, 100, 0);
var_dump($reply_info);

//获取应答消息类
$reply = $gps->getReply();
var_dump($reply);

```

## TODO

### 解析
-

### 封装
-

