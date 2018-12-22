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

    public function testRegisterBody() {
        $str = '7e0100002d058032343758009300000000594300000059432d31310000000000000000000000000000000005803234375802d4c1423838383838ff7e';

        $gps = new Gps();
//        $gps->setAuto(false);

        $gps->analytical($str);

//        $msg = $gps->getMessage();
//        var_dump($msg);
        var_dump($gps->getInfo());

        $this->assertIsObject($gps);
    }
}