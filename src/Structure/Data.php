<?php

/**
 * 数据接口
 * File:  Data.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/21
 */

declare(strict_types=1);

namespace  ChinaGnss\Structure;

interface Data {
    /**
     * 分割
     * @return mixed
     */
    public function split();

    /**
     * 解析
     * @return mixed
     */
    public function analyze();
}