<?php

/**
 *
 * File:  PlatformReply.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/26
 */

declare(strict_types=1);

namespace ChinaGnss\Protocol;

use ChinaGnss\Format;
use ChinaGnss\Structure\Data;

class PlatformReply implements Data {
    public $data = ''; //源内容

    public $msg_number = ''; //消息流水号
    public $msg_id = ''; //消息ID
    public $msg_result = ''; //结果

    /**
     * 初始化
     * Data constructor.
     * @param string $data
     */
    public function __construct(string $data) {
        $this->data = $data;

        var_dump($this->data);

        $this->split();
    }

    /**
     * 分割
     * @return mixed
     */
    public function split(): void {
        $this->msg_number = Format::subByte($this->data, 0, 2);
        $this->msg_id = Format::subByte($this->data, 2, 2);
        $this->msg_result = Format::subByte($this->data, 4);
    }

    /**
     * 解析
     * @return mixed
     */
    public function analyze(): array {
        $msg = [
            'msg_number' => $this->msg_number,
            'msg_id' => $this->msg_id,
            'msg_result' => Format::hex2Dec($this->msg_result),
        ];
        return $msg;
    }
}
