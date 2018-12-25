<?php

/**
 *
 * File:  Router.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/21
 */

declare(strict_types=1);

namespace ChinaGnss;

class Router {
    //平台应答消息ID
    public static $R0001 = 'TerminalReply'; //终端通用应答
    public static $R8001 = 'PlatformReply'; //平台通用应答
    public static $R8100 = 'RegisterReply'; //终端注册应答

    public static $R0002 = 'Heartbeat'; //终端心跳

    //操作消息ID
    public static $R0100 = 'Register'; //终端注册
    public static $R0102 = 'Authentication'; //鉴权
    public static $R0704 = 'LocatingData'; //定位数据批量上传
    public static $R0003 = 'Cancellation'; //终端注销
    public static $R0200 = 'LocationReporting'; //位置信息汇报
}