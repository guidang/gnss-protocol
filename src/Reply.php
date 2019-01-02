<?php

/**
 * 应答
 * File:  Reply.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/23
 */

declare(strict_types=1);

namespace ChinaGnss;

class Reply {

    public $msg_id = ''; //消息ID
    public $msg_number = ''; //消息流水号
    public $msg_mobile = ''; //设备手机号
    public $reply_id = ''; //应答ID
    public $auth_code = ''; //鉴权码

    protected $reply_number = 0; //应答流水号

    public $reply_body = ''; //应答消息体
    public $reply_data = ''; //应答消息内容
    public $reply_hex = ''; //应答消息内容 (十六进制)

    protected $code = 4; //应答结果 (十进制)

    protected $keep = '00'; //保留位
    protected $is_pack = 0; //是否分包
    protected $encrypt_type = '000'; //加密类型

    public function __construct(string $head_msg_id, string $head_msg_number, string $head_msg_mobile) {
        $this->msg_id = $head_msg_id;
        $this->msg_number = $head_msg_number;
        $this->msg_mobile = $head_msg_mobile;

        //确定应答ID
        $this->reply_id = ($this->msg_id == MessageId::REGISTER) ? MessageId::REGISTER_REPLY : MessageId::PLATFORM_REPLY;
    }

    /**
     * 设置应答流水号
     * @param int $number
     */
    public function setNumber(int $number) {
        $this->reply_number = $number;
    }

    /**
     * @param bool $is_pack 是否分包
     * @param string $encrypt_type 加密方式
     * @param string $keep 保留位
     */
    public function setParams(bool $is_pack = false, string $encrypt_type = '000', string $keep = '00') {
        $this->is_pack = ($is_pack) ? 1 : 0;
        $this->encrypt_type = $encrypt_type;
        $this->keep = $keep;
    }

    /**
     * 回复内容
     * @param int $code 结果 (-1.空数据)
     * @param int $type 应答类型 (0.应答消息(十六进制字符串), 1.应答消息体, 其它.应答消息(已封装的十六进制数据流))
     * @return string
     */
    public function reply(int $code = 4, int $type = 2) : string {
        $this->code = $code;

        if ($code != -1) {
            $this->reply_body = ($this->reply_id == MessageId::REGISTER_REPLY) ? $this->registerReply() : $this->platformReply();
        }

        if ($type == 1) {
            return $this->reply_body;
        }

        //编译消息内容
        $this->compileBody();

        //封装成十六进制字符串
        if ($type === 0) {
            return $this->reply_data;
        }

        //封装成十六进制数据流
        $this->reply_hex = Format::packData($this->reply_data);
        return $this->reply_hex;
    }

    /**
     * 注册应答
     * @return string
     */
    protected function registerReply() : string {
        $reply_msg_body_arr = [
            $this->msg_number,
        ];

        if ($this->code === 0) {
            $reply_msg_body_arr[] = Format::fillDec2Hex(0, 2); //应答结果
            $this->auth_code = Format::randomString('hex', 4);
            $reply_msg_body_arr[] = $this->auth_code; //鉴权码
        } else {
            $reply_msg_body_arr[] = Format::fillDec2Hex($this->code, 2); //应答结果
        }

        return implode('', $reply_msg_body_arr);
    }

    /**
     * 通用应答
     * @return string
     */
    protected function platformReply() : string {
        $reply_msg_body_arr = [
            $this->msg_number,
            $this->msg_id, //消息ID
            Format::fillDec2Hex($this->code, 2), //应答结果
        ];

        return implode('', $reply_msg_body_arr);
    }

    /**
     * 封装消息体字符串
     */
    protected function compileBody() : void {
        //消息体长度(二进制)
        $length_bin = Format::dec2Bin(Format::byteLen($this->reply_body));
        //消息属性(二进制字符串)
        $property_bin = sprintf('%s%d%s%s', $this->keep, $this->is_pack, $this->encrypt_type, Format::fillLeft($length_bin, 10));
        $property = Format::fillBin2Hex($property_bin);

        $reply_number = Format::fillDec2Hex($this->reply_number, 4);

        $reply_head = sprintf('%s%s%s%s', $this->reply_id, $property, $this->msg_mobile, $reply_number);

        $head_body = $reply_head . $this->reply_body;
        $reply_code = Format::generateCode($head_body);

        //未转义的消息内容
        $reply = sprintf('%s%s%s', $reply_head, $this->reply_body, $reply_code);
        //转义且封装的消息内容
        $this->reply_data = sprintf('7e%s7e', Format::strEscape($reply));
    }
}
