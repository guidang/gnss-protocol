<?php

/**
 *
 * File:  Gps.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/21
 */

declare(strict_types=1);

namespace ChinaGnss;

class Gps {
    protected $data = '';

    public $hex_msg = ''; //接收数据 - 不包含包头包尾
    public $hex_reply = ''; //回复数据 - 不包含包头包尾

    protected $message;
    protected $auto = true; //是否分析消息体

    public function __construct() {
    }

    /**
     * 是否
     * @param bool $switch
     */
    public function setAuto(bool $switch) {
        $this->auto  = $switch;
    }

    protected function receive(string $data) {
        $this->data = $data;

        $data = strtolower($data);
        $prefix = Format::subByte($data, 0, 1);
        $suffix = Format::subByte($data, Format::byteLen($data) - 1, 1);

//        var_dump($data, $prefix, $suffix);
        $hex_msg = (($prefix == '7e') && ($suffix == '7e')) ? mb_substr($data, 2, mb_strlen($data) - 4) : $data;

        $this->hex_msg = Format::strUnescape($hex_msg);
    }

    /**
     * 分析与拆分数据
     * @param string $data
     */
    public function analytical(string $data) {
        $this->receive($data);

        $this->message = new Message($this->hex_msg);
        if ($this->auto) {
            try {
                $this->message->analyticalBody();
            } catch (\Exception $e) {
                echo sprintf("\nError: %s \nFile: %s \nLine: %s\n\n", $e->getMessage(), __FILE__, __LINE__);
            }
        }
    }

    /**
     * 获取消息对象
     * @return Message
     */
    public function getMessage() : Message {
        return $this->message;
    }

    /**
     * 获取消息内容
     * @return array
     */
    public function getInfo() : array {
        $resp = [
            'head' => $this->message->receive_head,
            'body' => $this->message->receive_body,
            'check_code' => $this->message->checkcode,
            'is_check' => $this->message->receive_check,
            'msg_id' => $this->message->head_msg_id,
            'msg_number' => $this->message->head_msg_number,
        ];
        return $resp;
    }

    public function reply(int $code = 4) : string {
        //通用应答
        $reply_msg_id = '8001';

        $reply_msg_body_arr = [
            $this->message->head_msg_number,
        ];

        //注册应答
        if ($this->message->head_msg_id == '0100') {
            $reply_msg_id = '8100';

            if ($code === 0) {
                $reply_msg_body_arr[] = Format::fillDec2Hex(0, 2); //应答结果
                $reply_msg_body_arr[] = Format::randomString('hex', 4); //鉴权码
            } else {
                $reply_msg_body_arr[] = Format::fillDec2Hex($code, 2); //应答结果
            }
        } else { //通用应答
            $reply_msg_body_arr[] = $this->message->head_msg_id; //消息ID
            $reply_msg_body_arr[] = Format::fillDec2Hex($code, 2); //应答结果
        }

        $reply_msg_body = implode('', $reply_msg_body_arr);
        var_dump($reply_msg_body);

        return '';
    }
}