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
    public $auth_code = ''; //鉴权码(注册成功时返回, 以后每次鉴权时需要)

    protected $message; //消息类
    protected $auto = true; //是否分析消息体
    protected $reply; //应答类

    public function __construct() {
    }

    /**
     * 是否解析为默认协议消息体
     * @param bool $switch
     */
    public function setAuto(bool $switch) : void {
        $this->auto  = $switch;
    }

    /**
     * 接收消息
     * @param string $data
     */
    protected function receive(string $data) : void {
        $this->data = $data;

        $hex_msg = Format::filterData($data);

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
            'head' => $this->message->receive_head, //消息头
            'body' => $this->message->receive_body, //消息体
            'check_code' => $this->message->checkcode, //校验码
            'is_check' => $this->message->receive_check, //校验是否通过
            'msg_id' => $this->message->head_msg_id, //消息ID
            'msg_number' => $this->message->head_msg_number, //消息流水号
            'msg_mobile' => $this->message->head_msg_mobile, //手机号
        ];
        return $resp;
    }

    /**
     * 服务器 - 应答消息内容
     * @param int $code 结果 (-1. 空数据)
     * @param int $number 应答流水号
     * @param int $type 应答类型 (0.应答消息(十六进制字符串), 1.应答消息体, 其它.应答消息(已封装的十六进制数据流))
     * @param array $options 扩展选项 [is_pack,encrypt_type,keep] 是否分包,加密方式,保留位
     * @return string
     */
    public function reply(int $code = 4, int $number = 0, int $type = 2, array $options = []) : string {
        $this->reply = new Reply($this->message->head_msg_id, $this->message->head_msg_number, $this->message->head_msg_mobile);

        if ($number > 0) {
            $this->reply->setNumber($number);
        }

        if (! empty($options)) {
            $is_pack = isset($options['is_pack']) ? (bool)$options['is_pack'] : false;
            $encrypt_type = (! empty($options['encrypt_type']) && (mb_strlen($options['encrypt_type']) == 3)) ? $options['encrypt_type'] : '000';
            $keep = (! empty($options['keep']) && (mb_strlen($options['keep']) == 2)) ? $options['keep'] : '00';

            $this->reply->setParams($is_pack, $encrypt_type, $keep);
        }

        $reply = $this->reply->reply($code, $type);

        //注册协议
        if ($this->message->head_msg_id == MessageId::REGISTER) {
            $this->auth_code = $this->reply->auth_code;
        }

        return $reply;
    }

    /**
     * 获取应答对象
     * @return Reply
     */
    public function getReply() : Reply {
        return $this->reply;
    }

    /**
     * 客户端 - 应答消息内容
     * @param string $reply_id 消息ID
     * @param string $message 消息内容
     * @param int $number 应答流水号
     * @param int $type 应答类型 (0.应答消息(十六进制字符串), 1.应答消息体, 其它.应答消息(已封装的十六进制数据流))
     * @param array $options 扩展选项 [is_pack,encrypt_type,keep] 是否分包,加密方式,保留位
     * @return string
     */
    public function create(string $reply_id, string $message = '', int $number = 0, int $type = 2, array $options = []) : string {
        $this->reply = new Reply($this->message->head_msg_id, $this->message->head_msg_number, $this->message->head_msg_mobile);

        $this->reply->reply_id = $reply_id;
        $this->reply->reply_body = $message;

        if ($number > 0) {
            $this->reply->setNumber($number);
        }

        if (! empty($options)) {
            $is_pack = isset($options['is_pack']) ? (bool)$options['is_pack'] : false;
            $encrypt_type = (! empty($options['encrypt_type']) && (mb_strlen($options['encrypt_type']) == 3)) ? $options['encrypt_type'] : '000';
            $keep = (! empty($options['keep']) && (mb_strlen($options['keep']) == 2)) ? $options['keep'] : '00';

            $this->reply->setParams($is_pack, $encrypt_type, $keep);
        }

        $reply = $this->reply->reply(-1, $type);

        return $reply;
    }
}