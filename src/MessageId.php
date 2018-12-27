<?php

/**
 * 消息ID 常量
 * File:  MessageId.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/25
 */

declare(strict_types=1);

namespace ChinaGnss;

class MessageId {
    const TERMINAL_REPLY = '0001 '; //终端通用应答
    const PLATFORM_REPLY = '8001'; //平台通用应答
    const REGISTER_REPLY = '8100'; //终端注册应答

    const HEARTBEAT = '0002'; //终端心跳

    const REGISTER = '0100'; //终端注册
    const CANCELLATION = '0003'; //终端注销
    const AUTHENTICATION = '0102'; //鉴权

    CONST LOCATION_REPORTING = '0200'; //位置信息汇报
    CONST LOCATING_DATA = '0704'; //定位数据批量上传
}