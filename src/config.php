<?php

/**
 *
 * File:  config.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/21
 */

return [
    //入口
    'router' => [
        '0100' => 'Register', //终端注册
        '0102' => 'Authentication', //鉴权
        '0704' => 'LocatingData', //定位数据批量上传
        '0003' => 'Cancellation', //终端注销
        '0200' => 'LocationReporting', //位置信息汇报
        '0002' => 'Heartbeat', //终端心跳
    ],
];