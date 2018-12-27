<?php

/**
 * 定位数据批量上传
 * File:  LocatingData.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/21
 */

declare(strict_types=1);

namespace ChinaGnss\Protocol;

use ChinaGnss\Format;
use ChinaGnss\Structure\Data;

class LocatingData implements Data {
    public $data = ''; //源内容

    public $count; //数据项个数
    public $type; //位置数据类型, 0:正常位置批量汇报，1:盲区补报
    public $items; //位置汇报数据项

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
        $this->count = Format::subByte($this->data, 0, 2);
        $this->type = Format::subByte($this->data, 2, 1);
        $this->items = Format::subByte($this->data, 3);
    }

    /**
     * 解析
     * @return mixed
     */
    public function analyze(): array {
        $msg_len = Format::subByte($this->items, 0, 2); //位置汇报数据体长度
        $msg_info = Format::subByte($this->items, 2); //位置汇报数据体

        $reporting = new LocationReporting($msg_info);
        $location = $reporting->analyze();

        $msg = [
            'count' => Format::hex2Dec($this->count),
            'type' => Format::hex2Dec($this->type),
            'items' => [
                'length' => Format::hex2Dec($msg_len),
                'location' => $location,
            ]
        ];
//        var_dump($this);
        return $msg;
    }
}