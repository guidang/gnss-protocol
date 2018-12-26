<?php

/**
 * 定位 - 扩展信息解析
 * File:  Extend.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/26
 */

declare(strict_types=1);

namespace ChinaGnss\Protocol\LocationReporting;

use ChinaGnss\Format;
use ChinaGnss\Structure\Data;

class Extend implements Data {
    public $data = ''; //源数据

    public $info = []; //数据
    public $list = []; //已解析的数据

    //消息协议 [进制=>消息ID]
    protected $params = [
        'dec' => ['01', '02', '03', '04','30', '31'],
        'bin' => ['25', '2a', '2b'],
    ];

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
        $this->recursionExtInfo($this->data);
    }

    /**
     * 解析
     * @return mixed
     */
    public function analyze(): array {
        if (empty($this->info)) {
            return [];
        }

        $msg = [];

        foreach ($this->info as $key => $value) {
            $msg[] = $this->analyzeInfo($value);
        }

        return $msg;
    }

    /**
     * 设置参数
     * @param array $params
     */
    public function setParams(array $params = []) : void {
        $diy_params = [];

        empty($params['dec']) || $diy_params['dec'] = $params['dec'];
        empty($params['bin']) || $diy_params['bin'] = $params['bin'];

        if (! empty($diy_params)) {
            $this->params = array_merge($this->params, $diy_params);
        }
    }

    /**
     * 递归获取扩展信息
     * @param string $str
     */
    public function recursionExtInfo(string $str = '') : void {
        if ($str === '') {
            return;
        }

        $id = Format::subByte($str, 0, 1);
        $length = (int)Format::hex2Dec(Format::subByte($str, 1, 1));
        $msg = Format::subByte($str, 2, $length);

        $this->info[] = [
            'id' => $id,
            'length' => $length,
            'message' => $msg,
        ];

        $str = Format::subByte($str, 2 + $length);
        $this->recursionExtInfo($str);
//        var_dump($str, $start, $info, $id, $length, $msg);
    }

    /**
     * 扩展信息解析
     * @param array $info
     * @return array
     */
    public function analyzeInfo(array $info) : array {
        $msg = $info;

        //转十进制
        if (in_array($info['id'], $this->params['dec'])) {
            $info = Format::hex2Dec($info['message']);
        } else if (in_array($info['id'], $this->params['bin'])) {
            $bin_data = Format::fillHex2Bin($info['message']);
            $info = Format::strRevArr($bin_data);
        } else {
            $info = '';
        }
        $msg['info'] = $info;

        return $msg;
    }
}