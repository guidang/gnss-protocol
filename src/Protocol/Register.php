<?php

/**
 *
 * File:  Register.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/21
 */

declare(strict_types=1);

namespace ChinaGnss\Protocol;

use ChinaGnss\Format;
use ChinaGnss\Structure\Data;

class Register implements Data {
    public $data = ''; //源内容

    public $province_id; //省域ID， GB/T 2260 前二
    public $city_id; //城市ID，GB/T 2260 后四
    public $manufacturer_id; //制造商 ID
    public $terminal_model; //终端型号
    public $terminal_id; //终端 ID
    public $plate_color; //车牌颜色
    public $plate_number; //车辆标识(车牌号)

    /**
     * Register constructor.
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
    public function split() : void {
        $this->province_id = Format::subByte($this->data, 0, 2);
        $this->city_id = Format::subByte($this->data, 2, 2);
        $this->manufacturer_id = Format::subByte($this->data, 4, 5);
        $this->terminal_model = Format::subByte($this->data, 9, 20);
        $this->terminal_id = Format::subByte($this->data, 29, 7);
        $this->plate_color = Format::subByte($this->data, 36, 1);
        $this->plate_number = Format::subByte($this->data, 37);
    }

    /**
     * 解析
     * @return mixed
     */
    public function analyze() : array {
        // TODO: Implement analyze() method.

        $plate_number = '';
        if (! empty($this->plate_number)) {
            $plate_number = Format::hex2Str($this->plate_number);
        }

        $msg = [
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'manufacturer_id' => $this->manufacturer_id,
            'terminal_model' => $this->terminal_model,
            'terminal_id' => $this->terminal_id,
            'plate_color' => $this->plate_color,
            'plate_number' => $plate_number,
        ];
//        var_dump($msg);
        return $msg;
    }
}