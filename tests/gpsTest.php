<?php

/**
 *
 * File:  gpsTest.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/21
 */

declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase;

use ChinaGnss\Gps;

class gpsTest extends TestCase {
    public function testAnalytical() {
        require 'vendor/autoload.php';

        $str = '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

        $gps = new Gps();
        $gps->analytical($str);
    }
}