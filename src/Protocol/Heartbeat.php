<?php

/**
 * 心跳
 * File:  Heartbeat.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/27
 */

declare(strict_types=1);

namespace ChinaGnss\Protocol;

use ChinaGnss\Structure\Data;

class Heartbeat implements Data {
    public $data = ''; //源内容

    /**
     * 初始化
     * Data constructor.
     * @param string $data
     */
    public function __construct(string $data) {
    }

    /**
     * 分割
     * @return mixed
     */
    public function split(): void {
    }

    /**
     * 解析
     * @return mixed
     */
    public function analyze(): array {
        return [];
    }
}