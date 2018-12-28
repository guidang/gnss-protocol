<?php

/**
 *
 * File:  BodyTest.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/22
 */

declare(strict_types=1);

namespace ChinaGnss;

use PHPUnit\Framework\TestCase;

class BodyTest extends TestCase {

    /**
     * 注册
     */
    public function testRegister() {
        $str = '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

        $gps = new Gps();
//        $gps->setAuto(false);

        $gps->analytical($str);

//        $msg = $gps->getMessage();
//        var_dump($msg);
        $info = $gps->getInfo();
//        var_dump($info);

        $this->assertIsObject($gps);
    }

    /**
     * 心跳
     */
    public function testHeartbeat() {
        $str = '7e000200000580323437580079977e';

        $gps = new Gps();
        //心跳无Body
        $gps->setAuto(false);

        $gps->analytical($str);

        $msg = $gps->getMessage();
//        var_dump($msg);

        $this->assertIsObject($gps);
    }

    /**
     * 鉴权
     */
    public function testAuthentication() {
        $str = '7e0102000205803234375800007b7d02e87e';

        $gps = new Gps();

        $gps->analytical($str);

//        $msg = $gps->getMessage();
//        var_dump($msg);
        $info = $gps->getInfo();
//        var_dump($info);

        $this->assertIsObject($gps);
    }

    /**
     * 位置信息汇报
     */
    public function testLocationReporting() {
        $str = '0200002e05803234375832b900000000000c000301581e5206ca60b6004c0000008f18122303545925040000000001040000037530011f310105e6';

        $gps = new Gps();

        $gps->analytical($str);

//        $msg = $gps->getMessage();
//        var_dump($msg);
        $info = $gps->getInfo();
//        var_dump($info);

        $this->assertIsObject($gps);
    }

    /**
     * 定位数据批量上传
     */
    public function testLocatingData() {
        $str = '070400330580323437583389000101002e00000000000c000301581db206ca5fbb004b002d016418122304323925040000000001040000037930011f310109f5';

        $gps = new Gps();

//        var_dump($str);
        $gps->analytical($str);

        $msg = $gps->getMessage();
//        var_dump($msg);
        $info = $gps->getInfo();
//        var_dump($info);

        $this->assertIsObject($gps);
    }

    public function testSplitMessage() {
        $msg = [
            'register' => '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e',
            'authentication' => '7e0102000205803234375800007b7d02e87e',
            'locationreporting' => '7e0200002e058032343758004e00000000000c0001000000000000000000000000000018121310340125040000000001040000000030011b310100817e',
            'locatingdata' => '7e07040033058032343758004d000101002e00000000000c0001000000000000000000000000000018121308573725040000000001040000000030011f310100f97e',
            'heartbeat' => '7e000200000580323437580079977e',
        ];

        $linkpackage = implode('', $msg);
        $arr = Format::splitMessage($linkpackage, '7e', '7e');
//        var_dump($arr);
        $this->assertIsArray($arr);
    }
}