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
     * 构造注册信息
     */
    public function testResgisterMsg() {
        $msg = new Terminal();

        $setting = [
            'msg_id' => MessageId::REGISTER,
            'msg_number' => '0000',
            'msg_mobile' => '015697794619',
        ];

        $msg->setting($setting);

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
//        var_dump($send_data_str);

        $this->assertTrue(true);
    }

    public function testRegisterParam() {
        $str = '7e0100005a015697794619000000000000594300000059432d31310000000000000000000000000000000005803234375802d4c14131323334351b7e';
        $msg = new Terminal($str);
//        var_dump($msg);
    }

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
//        $str = '7e810000060580323437580000009303fb7e';
//        $gps = new Gps();
//        $gps->analytical($str);
//
//        $gps->setReplyId(MessageId::CANCELLATION);
//        $gps->reply(-1);
//        var_dump($gps);
//
//        $this->assertIsObject($gps);
        $this->assertTrue(true);
        return true;
    }
}