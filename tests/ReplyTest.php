<?php

/**
 *
 * File:  ReplyTest.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/22
 */

declare(strict_types=1);

namespace ChinaGnss;

use PHPUnit\Framework\TestCase;

class ReplyTest extends TestCase {

    public function testRegister() {
        $str = '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

        $gps = new Gps();
        $gps->analytical($str);

        $code = 0;
        $gps->reply($code);
    }

    public function testPlatMessage() {
        $str = '07040033058032343758004d000101002e00000000000c0001000000000000000000000000000018121308573725040000000001040000000030011f310100f9';

        $gps = new Gps();
        $gps->analytical($str);

        $code = 4;
        $gps->reply($code);
    }
}