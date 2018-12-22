<?php

/**
 *
 * File:  GpsTest.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/22
 */

declare(strict_types=1);

namespace ChinaGnss;

use PHPUnit\Framework\TestCase;

class GpsTest extends TestCase {

    public function testAnalytical() {
        $str = '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

        $gps = new Gps();
        $gps->analytical($str);
        $this->assertIsObject($gps);
        return true;

    }

    public function testSetAuto() {
        $this->assertTrue(true);
        return true;
    }

    public function testGetMessage() {
        $str = '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

        $gps = new Gps();
        $gps->analytical($str);

        $msg = $gps->getMessage();
        $this->assertIsObject($msg);

        return $msg;
    }
}
