<?php

/**
 *
 * File:  Authentication.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/23
 */

declare(strict_types=1);

namespace ChinaGnss\Protocol;

use ChinaGnss\Structure\Data;

class Authentication implements Data {
    public $data = ''; //源内容

    public $code = ''; //鉴权码

    /**
     * Authentication constructor.
     * @param string $data
     */
    public function __construct(string $data) {
        $this->data = $data;

        $this->split();
    }

    /**
     * 分割
     * @return mixed
     */
    public function split(): void {
        // TODO: Implement split() method.
        $this->code = $this->data;
    }

    /**
     * 解析
     * @return mixed
     */
    public function analyze(): array {
        // TODO: Implement analyze() method.
        $msg = [
            'code' => $this->code,
        ];
        return $msg;
    }
}