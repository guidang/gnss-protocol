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
use ChinaGnss\Protocol\LocationReporting\Extend;
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
    public $extend = ''; //扩展信息

    public $extend_arr = []; //扩展信息数组(已拆分)
    protected $ext; //扩展类

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
        $this->extend = Format::subByte($this->data, 28);

        //切割扩展数据
        $this->splitExtend();
    }

    /**
     * 解析扩展数据
     */
    public function splitExtend() : void {
        $this->ext = new Extend($this->extend);
        $this->extend_arr = $this->ext->info;
    }

    /**
     * 解析
     * @return mixed
     */
    public function analyze(): array {
        $alarm_signs = $this->analyzeAlarm(); //报警
        $status = $this->analyzeStatus(); //状态
        $extend = $this->ext->analyze(); //扩展信息

        $msg = [
            'alarm_signs' => $alarm_signs,
            'status' => $status,
            'latitude' => Format::hex2Dec($this->latitude) / 1e6,
            'longitude' => Format::hex2Dec($this->longitude) / 1e6,
            'elevation' => Format::hex2Dec($this->elevation),
            'speed' => Format::hex2Dec($this->speed),
            'direction' => Format::hex2Dec($this->direction),
            'time' => $this->time,
            'extend' => $extend,
        ];

//        var_dump($this);
        return $msg;
    }

    /**
     * 解析报警数据
     * @return array
     */
    public function analyzeAlarm() : array {
        $signs_bin = Format::fillHex2Bin($this->alarm_signs);
//        $signs_bin = '234567890abcdefghijklnmopqrstuvw';
        $signs_arr = Format::strRevArr($signs_bin);

        /*
        $gov_key = [
            'emergency_alarm',
            'overspeed_alarm',
            'fatigue_driving',
            'danger_warning',
            'gnss_module_failure',
            'gnss_antenna_failure',
            'gnss_antenna_shortcircuit',
            'power_undervoltage',
            'power_failure',
            'display_failure',
            'tts_failure',
            'camera_failure',
            'iccard_failure',
            'overspeed_warning',
            'fatigue_driving_warning',
            'keep15',
            'keep16',
            'keep17',
            'driving_timeout',
            'overtime_parking',
            'access_area',
            'access_routes',
            'road_travel_timeerr',
            'route_deviation_alarm',
            'vss_failure',
            'abnormal_oil',
            'vehicle_theft',
            'unauthorized_start',
            'unauthorized_movement',
            'collision_warning',
            'rollover_warning',
            'unauthorized_opendoor_alarm',
        ];
        */

        /*
        //默认直接赋值方式
        $msg = [
            'emergency_alarm' => $signs_arr[0],
            'overspeed_alarm' => $signs_arr[1],
            'fatigue_driving' => $signs_arr[2],
            'danger_warning' => $signs_arr[3],
            'gnss_module_failure' => $signs_arr[4],
            'gnss_antenna_failure' => $signs_arr[5],
            'gnss_antenna_shortcircuit' => $signs_arr[6],
            'power_undervoltage' => $signs_arr[7],
            'power_failure' => $signs_arr[8],
            'display_failure' => $signs_arr[9],
            'tts_failure' => $signs_arr[10],
            'camera_failure' => $signs_arr[11],
            'iccard_failure' => $signs_arr[12],
            'overspeed_warning' => $signs_arr[13],
            'fatigue_driving_warning' => $signs_arr[14],
            'keep15' => $signs_arr[15],
            'keep16' => $signs_arr[16],
            'keep17' => $signs_arr[17],
            'driving_timeout' => $signs_arr[18],
            'overtime_parking' => $signs_arr[19],
            'access_area' => $signs_arr[20],
            'access_routes' => $signs_arr[21],
            'road_travel_timeerr' => $signs_arr[22],
            'route_deviation_alarm' => $signs_arr[23],
            'vss_failure' => $signs_arr[24],
            'abnormal_oil' => $signs_arr[25],
            'vehicle_theft' => $signs_arr[26],
            'unauthorized_start' => $signs_arr[27],
            'unauthorized_movement' => $signs_arr[28],
            'collision_warning' => $signs_arr[29],
            'rollover_warning' => $signs_arr[30],
            'unauthorized_opendoor_alarm' => $signs_arr[31],
        ];
        */

//        $msg = array_combine($gov_key, $signs_arr);
        $msg = $signs_arr;
        $msg = [];

        return $msg;
    }

    /**
     * 解析状态
     * @return array
     */
    public function analyzeStatus() : array {
        $status_bin = Format::fillHex2Bin($this->status);
        $status_arr = Format::strRevArr($status_bin);

        //装载状态 0:空车;1:半载;2:保留;3:满载
//        $loading = Format::bin2Dec($status_arr[8].$status_arr[9]);

        /*
        $gov_key = [
            'acc',
            'located',
            'latitude',
            'longitude',
            'operation',
            'encrypted',
            'keep6',
            'keep7',
            'loading1',
            'loading2',
            'oil_disconnected',
            'circuit_disconnected',
            'door_locked',
            'front_door_opened',
            'middle_door_opened',
            'back_door_opened',
            'driver_door_opened',
            'other_door_opened',
            'gps',
            'beidou',
            'glonass',
            'galileo',
            'keep22',
            'keep23',
            'keep24',
            'keep25',
            'keep26',
            'keep27',
            'keep28',
            'keep29',
            'keep30',
            'keep31',
        ];
        */
//        $msg = array_combine($gov_key, $status_arr);
        $msg = $status_arr;
        $msg = [];

        return $msg;
    }
}