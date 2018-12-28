中国交通部GPS车载终端通讯协议解析 
------
部标GPS数据流解析 (字符串处理版)。

## 安装
```
composer require skiy/gnss-protocol:dev-master
```

## 使用
### 解析
-
```php
<?php

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

### 封装
客户端消息发送   
- 初始化终端
```php
    //方式一
    $msg = new Terminal();

    $setting = [
        'msg_id' => MessageId::REGISTER,
        'msg_number' => '0000',
        'msg_mobile' => '015697794619',
    ];

    $msg->setting($setting);
    
    //方式二, 通过上条消息获取基本信息
    $msg = new \ChinaGnss\Terminal($reply);
```
- 注册
```php
    $plate_number = '粤A12345';
    
    $register_params = [
        'province_id' => '0000',
        'city_id' => '0000',
        'manufacturer_id' => '5943000000',
        'terminal_model' => '59432d3131000000000000000000000000000000',
        'terminal_id' => '00058032343758',
        'plate_color' => '02',
        'plate_number' => Format::str2Hex($plate_number, 'gbk'),
    ];
    
    $msg->register($register_params);
    
    $send_data_str = $msg->compile(0);
```
- 自定义内容
```php
    $local_body = '00000000000c00010000000000000000000000000000%s25040000000001040000000030011b310100';
    $body = sprintf($local_body, date('ymdHis')); //自定义内容
    $term = new \ChinaGnss\Terminal($reply);
    $term->setBody(\ChinaGnss\MessageId::LOCATION_REPORTING, $body); //$body为缺省时，发送空消息
    $location = $term->compile();

```
