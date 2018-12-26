<?php

/**
 * 注册应答
 * File:  RegisterTest.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/26
 */

declare(strict_types=1);

namespace ChinaGnss;

use PHPUnit\Framework\TestCase;

class ReplyTest extends TestCase {

    /**
     * 测试注册成功
     */
    public function testRegisterSuccess() {
        $str = '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

        $gps = new Gps();
        $gps->analytical($str);

        $reply = $gps->reply();
        $data = $gps->getReply();

        echo "\nreply_body: {$data->reply_body}";

        $this->assertNotNull($reply);
    }

    /**
     * 注册失败
     */
    public function testRegisterFailure() {
        //设备不存在
        $str = '7e0100002d158032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

        $gps = new Gps();
        $gps->analytical($str);

        $reply = $gps->reply();
        $data = $gps->getReply();

        echo "\nreply_body: {$data->reply_body}";

        $this->assertNotNull($reply);
    }
}