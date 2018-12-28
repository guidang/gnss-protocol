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
        $count = Format::hex2Dec($this->count);

        $items = [];

        $start = 0;
        for ($i = 0; $i < $count; $i++) {
            $length_hex = Format::subByte($this->items, $start, 2); //位置汇报数据体长度
            $length = (int)Format::hex2Dec($length_hex);
            $msg_info = Format::subByte($this->items, $start + 2, $length); //位置汇报数据体

            $start = $start + 2 + $length;

            $reporting = new LocationReporting($msg_info);
            $location = $reporting->analyze();

            $items[] = [
                'length' => $length,
                'info' => $location,
            ];
        }

        $msg = [
            'count' => $count,
            'type' => Format::hex2Dec($this->type),
            'items' => $items,
        ];
        return $msg;
    }
}