<?php

/**
 *
 * File:  RegisterReply.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/25
 */

declare(strict_types=1);

namespace ChinaGnss\Protocol;

use ChinaGnss\Format;
use ChinaGnss\Structure\Data;

class RegisterReply implements Data {
    public $data = ''; //源内容

    public $msg_number; //消息流水号
    public $msg_result; //消息结果 0:成功;1:车辆已被注册;2:数据库中无该车辆; 3:终端已被注册;4:数据库中无该终端
    public $auth_code = '';  //鉴权码

    /**
     * 初始化
     * Data constructor.
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
        $this->msg_number = Format::subByte($this->data, 0, 2);
        $this->msg_result = Format::subByte($this->data, 2, 1);
        $this->auth_code = Format::subByte($this->data, 3);
    }

    /**
     * 解析
     * @return mixed
     */
    public function analyze(): array {
        $msg = [
            'msg_number' => Format::hex2Dec($this->msg_number),
            'msg_result' => Format::hex2Dec($this->msg_result),
            'auth_code' => $this->auth_code,
        ];

        return $msg;
    }
}