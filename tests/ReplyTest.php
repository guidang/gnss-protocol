<?php

/**
 * 消息应答
 * File:  ReplyTest.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/22
 */

declare(strict_types=1);

namespace ChinaGnss;

use PHPUnit\Framework\TestCase;

class ReplyTest extends TestCase {

    /**
     * 注册成功应答
     */
    public function testRegister() {
        $str = '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

        $gps = new Gps();
        $gps->analytical($str);

        $code = 0;
        $reply = $gps->reply($code);
//        var_dump($reply);
        var_dump($gps);

        $this->assertIsObject($gps);
    }

    /**
     * 注册失败应答
     */
    public function testRegisterError() {
        $str = '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

        $gps = new Gps();
        $gps->analytical($str);

        $code = 4;
        $reply = $gps->reply($code);
//        var_dump($reply);

        $this->assertIsObject($gps);
    }

    /**
     * 通用应答
     */
    public function testPlatMessage() {
        $str = '07040033058032343758004d000101002e00000000000c0001000000000000000000000000000018121308573725040000000001040000000030011f310100f9';

        $gps = new Gps();
        $gps->analytical($str);

        $msg = $gps->reply(4, 100, 2);
//        var_dump($msg);

        $this->assertIsObject($gps);
    }

    /**
     * 应答消息类
     */
    public function testReply() {
        $str = '07040033058032343758004d000101002e00000000000c0001000000000000000000000000000018121308573725040000000001040000000030011f310100f9';

        $gps = new Gps();
        $gps->analytical($str);

        $reply = $gps->reply(4, 100, 0);
//        var_dump($reply);

        $msg = $gps->getReply();
//        var_dump($msg);

        $this->assertIsObject($gps);
    }
}