<?php

/**
 * 测试终端接收的应答
 * File:  TerminalTest.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/25
 */

declare(strict_types=1);

namespace ChinaGnss;

use PHPUnit\Framework\TestCase;

class TerminalTest extends TestCase {

    /**
     * 注册应答
     * @return bool
     */
    public function testRegister() {
        $str = '7e8100000a0580323437580000009300f276707e';
        $gps = new Gps();
        $gps->analytical($str);

//        $info = $gps->getInfo();
//        var_dump($info);

        $this->assertIsObject($gps);
        return true;
    }

    /**
     * 注册应答 - 自定义BODY
     * @return bool
     */
    public function testRegister2() {
        $str = '7e8100000a0580323437580000009300f276707e';
        $gps = new Gps();
        $gps->setAuto(false);
        $gps->analytical($str);

        $this->assertIsObject($gps);
        return true;
    }

    /**
     * 注册失败
     * @return bool
     */
    public function testRegisterError() {
        $str = '7e810000060580323437580000009303fb7e';
        $gps = new Gps();
        $gps->analytical($str);

        $gps->setReplyId(MessageId::CANCELLATION);
        $gps->reply(-1);
        var_dump($gps);

        $this->assertIsObject($gps);
        return true;
    }
}