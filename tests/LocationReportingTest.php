<?php

/**
 *
 * File:  LocationReportingTest.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/24
 */


declare(strict_types=1);

namespace ChinaGnss\Protocol;

use ChinaGnss\Gps;
use PHPUnit\Framework\TestCase;

class LocationReportingTest extends TestCase {
    public $str = '0200002e05803234375832b900000000000c000301581e5206ca60b6004c0000008f18122303545925040000000001040000037530011f310105e6';

    public function testLocationReporting() {
        $gps = new Gps();

        $gps->analytical($this->str);

        $msg = $gps->getMessage();
//        var_dump($msg);

        $info = $gps->getInfo();
//        var_dump($info);

        $reply = $gps->reply();
//        var_dump($reply);

        $this->assertTrue(true);
    }

    public function testBody() {
        $body = '00000000000c000301581e5206ca60b6004c0000008f18122303545925040000000001040000037530011f310105';
        $location = new LocationReporting($body);
        var_dump($location);

        $result = $location->analyze();

        var_dump($result);
    }

}
