<?php

/**
 *
 * File:  Terminal.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/27
 */

declare(strict_types=1);

namespace ChinaGnss;

use ChinaGnss\Structure\Property;

class Terminal {
    protected $previous_data = ''; //上一条源数据

    protected $msg_id = ''; //消息ID
    protected $msg_number = 0; //消息流水号
    protected $msg_mobile = ''; //设备手机号
    protected $reply_id = ''; //应答ID

    protected $keep = '00'; //保留位
    protected $is_pack = 0; //是否分包
    protected $encrypt_type = '000'; //加密类型

    public $body = ''; //消息内容
    public $data = ''; //应答消息内容
    public $hex = ''; //应答消息内容 (十六进制)

    public function __construct(string $data = '') {
        $this->previous_data = $data;

        empty($this->previous_data) || $this->initParams();
    }

    /**
     * 根据一条消息数据提取消息ID、流水号、手机号等信息
     */
    public function initParams() : void {
        $this->previous_data = Format::filterData($this->previous_data);
        $message = new Message($this->previous_data);

        $encrypt = $message->receive_property;
        $this->setEncrypt($encrypt['subcontracting'], $encrypt['encrypt_type'], $encrypt['keep']);

        $msg_number = (int)Format::hex2Dec($message->head_msg_number);
        $setting = [
            'msg_mobile' => $message->head_msg_mobile,
            'reply_id' => $message->head_msg_id,
            'msg_number' => $msg_number + 1,
        ];
        $this->setting($setting);
    }

    /**
     * 设置加密
     * @param bool $is_pack 是否分包
     * @param string $encrypt_type 加密方式
     * @param string $keep 保留位
     */
    public function setEncrypt(bool $is_pack = false, string $encrypt_type = '000', string $keep = '00') {
        $this->is_pack = ($is_pack) ? 1 : 0;
        $this->keep = $keep;

        //长度为3
        (strlen($encrypt_type) === 3) && $this->encrypt_type = $encrypt_type;
    }

    /**
     * 配置消息信息
     * @param array $config
     */
    public function setting(array $config = []): void {
        isset($config['msg_id']) && $this->msg_id = $config['msg_id'];
        isset($config['msg_mobile']) && $this->msg_mobile = $config['msg_mobile'];
        isset($config['reply_id']) && $this->reply_id = $config['reply_id'];

        //设置消息流水号
        if (isset($config['msg_number'])) {
            $this->msg_number = is_int($config['msg_number']) ? $config['msg_number'] : (int)Format::hex2Dec($config['msg_number']);
        }
    }

    /**
     * 构造注册消息体
     * @param array $params
     */
    public function register(array $params): void {
        //消息ID不为终端注册
//        if ($this->msg_id != MessageId::REGISTER) {
//            return;
//        }
        $this->msg_id = MessageId::REGISTER;

        $register_params = [
            'province_id' => '0000',
            'city_id' => '0000',
            'manufacturer_id' => '0000000000',
            'terminal_model' => '0000000000000000000000000000000000000000',
            'terminal_id' => '00000000000000',
            'plate_color' => '00',
            'plate_number' => '0',
        ];

        if (!empty($params) && is_array($params)) {
            $register_params = array_merge($register_params, $params);
        }

        //组成消息体
        $this->body = sprintf('%s%s%s%s%s%s%s',
            $register_params['province_id'], //省域ID， GB/T 2260 前二
            $register_params['city_id'], //城市ID，GB/T 2260 后四
            $register_params['manufacturer_id'], //制造商 ID
            $register_params['terminal_model'], //终端型号
            $register_params['terminal_id'], //终端 ID
            $register_params['plate_color'], //车牌颜色
            $register_params['plate_number']); //车辆标识(车牌号)
    }

    /**
     * 自定义消息体
     * @param string $msg_id
     * @param string $body
     */
    public function setBody(string $msg_id = '', string $body = '') {
        if (! empty($msg_id) && strlen($msg_id) == 4) {
            $this->msg_id = $msg_id;
        }
        $this->body = $body;
    }

    /**
     * 封装消息体字符串, 并且返回数据
     * @param int $type 应答类型 (0.应答消息(十六进制字符串), 1.应答消息体, 其它.应答消息(已封装的十六进制数据流))
     * @return string
     */
    public function compile(int $type = 2): string {
        if ($type == 1) {
            return $this->body;
        }

        //消息体长度(二进制)
        $length_bin = Format::dec2Bin(Format::byteLen($this->body));
        //消息属性(二进制字符串)
        $property_bin = sprintf('%s%d%s%s', $this->keep, $this->is_pack, $this->encrypt_type, Format::fillLeft($length_bin, 10));
        $property = Format::fillBin2Hex($property_bin);

        $msg_number = Format::fillDec2Hex($this->msg_number, 4);

        $msg_head = sprintf('%s%s%s%s', $this->msg_id, $property, $this->msg_mobile, $msg_number);

        $head_body = $msg_head . $this->body;
        $check_code = Format::generateCode($head_body);

        //未转义的消息内容
        $message = sprintf('%s%s%s', $msg_head, $this->body, $check_code);

        //转义且封装的消息内容
        $this->data = sprintf('7e%s7e', Format::strEscape($message));

        if ($type == 0) {
            return $this->data;
        }

        //十六进制数据
        $this->hex = Format::packData($this->data);

        return $this->hex;
    }
}