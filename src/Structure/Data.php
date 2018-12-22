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
     * 初始化
     * Data constructor.
     * @param string $data
     */
    public function __construct(string $data);

    /**
     * 分割
     * @return mixed
     */
    public function split() : void;

    /**
     * 解析
     * @return mixed
     */
    public function analyze() : array;
}