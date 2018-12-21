<?php

/**
 *
 * File:  Property.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/21
 */

declare(strict_types=1);

namespace ChinaGnss\Structure;

use ChinaGnss\Format;

class Property implements Data {
    public $data; //源内容
    public $data_bin; //源内容的二进制

    public $body_len; //消息长度
    public $encrypt_type; //加密方式
    public $subcontracting; //分包
    public $keep; //保留

//    public $response; //数据

    public function __construct($data) {
        $this->data = $data;
        $this->data_bin = Format::fillHex2Bin($data);

        $this->split();
    }

    /**
     * 切割
     */
    public function split() : void {
        $this->body_len = mb_substr($this->data_bin, 16 - 10, 10); //消息体长度
        $this->encrypt_type = mb_substr($this->data_bin, 16 - 13, 3); //消息加密方式 000不加密, XX1 RSA加密
        $this->subcontracting = mb_substr($this->data_bin, 16 - 14, 1); //是否分包, 1时表示消息体为长消息,进行分包发送处理
        $this->keep = mb_substr($this->data_bin, 0, 2); //保留位
    }

    /**
     * 解析属性
     * @return array
     */
    public function analyze() : array {
        //加密方式
        $encrypt_type = '';
        if ($this->encrypt_type != '000') {
            $encrypt_flag = mb_substr($this->encrypt_type, -1, 1);
            if ($encrypt_flag == 1) {
                $encrypt_type = 'rsa';
            }
        }

        $msg = [
            'body_len' => base_convert($this->body_len, 2, 10), //消息体长度(十进制)
            'encrypt_type' => $encrypt_type,
            'subcontracting' => ($this->subcontracting == 1) ? true : false, //是否为长消息
            'keep' => $this->keep,
        ];

        return $msg;
    }
}