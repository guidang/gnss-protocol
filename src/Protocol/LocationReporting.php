<?php

/**
 * 位置信息汇报
 * File:  LocationReporting.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/23
 */

declare(strict_types=1);

namespace ChinaGnss\Protocol;

use ChinaGnss\Format;
use ChinaGnss\Structure\Data;

class LocationReporting implements Data {
    public $data = '';

    public $alarm_signs; //报警标志
    public $status; //状态
    public $latitude; //纬度
    public $longitude; //经度
    public $elevation; //高度
    public $speed; //速度
    public $direction; //方向
    public $time; //时间

    public $ext_msg_id; //附加信息ID
    public $ext_msg_length; //附加信息长度
    public $ext_msg_data; //附加信息内容

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
        // TODO: Implement split() method.
        $this->alarm_signs = Format::subByte($this->data, 0, 4);
        $this->status = Format::subByte($this->data, 4, 4);
        $this->latitude = Format::subByte($this->data, 8, 4);
        $this->longitude = Format::subByte($this->data, 12, 4);
        $this->elevation = Format::subByte($this->data, 16, 2);
        $this->speed = Format::subByte($this->data, 18, 2);
        $this->direction = Format::subByte($this->data, 20, 2);
        $this->time = Format::subByte($this->data, 22, 6);

        $this->ext_msg_id = Format::subByte($this->data, 28, 1);
        $this->ext_msg_length = Format::subByte($this->data, 29, 1);
        $this->ext_msg_data = Format::subByte($this->data, 30);

        for ($i = 31; $i >= 0; $i--) {
            $key = 31 - $i;
            $value = $this->ext_msg_data[$i];
//            echo ("\ni: {$i}, key: {$key}, value: {$value}");
        }
    }

    /**
     * 解析
     * @return mixed
     */
    public function analyze(): array {
        // TODO: Implement analyze() method.
        $msg = [
            'longitude' => $this->longitude, //113926326 06ca60b6
            'elevation' => base_convert($this->elevation, 16, 10),
            'speed' => base_convert($this->speed, 16, 10),
            'direction' => base_convert($this->direction, 16, 10),
        ];

//        var_dump($this);
        return $msg;
    }
}