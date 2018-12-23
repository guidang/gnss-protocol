<?php

/**
 *
 * File:  Message.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/21
 */

declare(strict_types=1);

namespace ChinaGnss;

use ChinaGnss\Structure\Property;
use ChinaGnss\Structure\Head;

class Message {
    public $head; //消息头
    public $body; //消息体
    public $checkcode; //检验码

    public $data; //源内容

    public $property; //消息属性
    public $receive_property; //已解析的属性
    public $receive_head; //已解析的消息头
    public $receive_body = []; //已解析的消息内容
    public $receive_check; //是否校验通过(与校验码匹配)

    public $head_length = 12; //消息头长度,默认为12字节

    public $head_msg_id = ''; //消息ID HEX
    public $head_msg_number = ''; //消息流水号HEX
    public $head_msg_mobile = ''; //设备手机号

    public function __construct($data) {
        $this->data = $data;

        $this->analytical();
    }

    /**
     * 解析
     */
    public function analytical() {
        $this->receive_property = $this->analyticalProperty();

        $this->receive_head = $this->analyticalHead();

        $this->receive_check = $this->analyticalCode();

        $this->receive_head['body_prop'] = $this->receive_property;

//        $this->receive_body = $this->analyticalBody();

//        var_dump($this);
    }

    /**
     * 解析属性
     * @return array
     */
    public function analyticalProperty() : array {
        $this->property =  Format::subByte($this->data, 2, 2);
        $property = new Property($this->property);
        $prop = $property->analyze();
//        var_dump($prop);
        return $prop;
    }

    /**
     * 切割消息头
     * @return string
     */
    public function splitHead() : string {
        //分包
        if ($this->receive_property['subcontracting']) {
            $this->head_length += 4;
        }
        $head = Format::subByte($this->data, 0, $this->head_length);
        return $head;
    }

    /**
     * 解析消息头
     */
    public function analyticalHead() : array {
        $this->head = $this->splitHead();
        $msg_head = new Head($this->head);
        $head = $msg_head->analyze();
//        var_dump($head);
//        var_dump($msg_head);
        $this->head_msg_id = $msg_head->msg_id;
        $this->head_msg_number = $msg_head->msg_number;
        $this->head_msg_mobile = $msg_head->device_mobile;
        return $head;
    }

    /**
     * 校验数据是否与校验码一致
     * @return bool
     */
    public function analyticalCode() : bool {
        $length = Format::byteLen($this->data) - 1; //[消息头.消息体,校验码]
        $head_body = Format::subByte($this->data, 0, $length); //消息头与消息体
        $this->checkcode = Format::subByte($this->data, $length);

        //直接在分析校验码处截取消息体
        $this->body = Format::subByte($head_body, $this->head_length);

        $hex_code = Format::generateCode($head_body);
//        echo "head body: {$head_body} \nverify code: {$code}\nhex code: {$hex_code}\n";

        if ($hex_code === strtolower($this->checkcode)) {
            return true;
        }
        return false;
    }

    /**
     * 截取消息体
     * @return string
     */
    public function splitBody() : string {
        $body = Format::subByte($this->data, $this->head_length, $this->receive_property['body_len']);
        return $body;
    }

    /**
     * 分析消息体
     * @return array
     */
    public function analyticalBody() {
//        $this->body = $this->splitBody();
        try {
            $ProtocolRid = sprintf('R%s', $this->receive_head['msg_id']);
            $ProtocolFullName = sprintf('ChinaGnss\Protocol\%s', Router::$$ProtocolRid);
//            var_dump($ProtocolFullName);
            $protocol = new $ProtocolFullName($this->body);
            $result = $protocol->analyze();
            $this->receive_body = $result;
//            var_dump($result);
        } catch (\Exception $e) {
            $this->receive_body = [];

            echo sprintf("\nError: %s \mFile: %s \nLine: %s\n", $e->getMessage(), __FILE__, __LINE__);
        }

    }
}
