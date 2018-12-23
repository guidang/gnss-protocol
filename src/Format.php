<?php

/**
 *
 * File:  Format.php
 * Author: Skiychan <dev@skiy.net>
 * Created: 2018/12/21
 */

declare(strict_types=1);

namespace ChinaGnss;

class Format {

    /**
     * 消息转义
     * @param $str
     * @return mixed
     */
    public static function strEscape(string $str) : string {
        $str = str_replace('7d', '7d01', $str);
        $str = str_replace('7e', '7d02', $str);
        return $str;
    }

    /**
     * 消息还原
     * @param $str
     * @return mixed
     */
    public static function strUnescape(string $str) : string {
        $str = str_replace('7d02', '7e', $str);
        $str = str_replace('7d01', '7d', $str);
        return $str;
    }

    /**
     * 字符串按字节截取
     * @param string $str 被截取的字符串
     * @param int $start 开始位置
     * @param int $len 选取长度
     * @return string
     */
    public static function subByte(string $str, int $start = 0, int $len = -1) : string {
        if ($len == -1) return mb_substr($str, $start * 2);

        return mb_substr($str, $start * 2, $len * 2);
    }

    /**
     * 字符串按字节算长度
     * @param string $str
     * @param bool $force
     * @return int
     */
    public static function byteLen(string $str, bool $force = false) : int {
        $strlen = mb_strlen($str);
        $bytelen = $strlen / 2;

        if (! $force) {
            return (int)$bytelen;
        }

        if (is_int($bytelen)) {
            return $bytelen;
        }

        return -1;
    }

    /**
     * 十六进制转二进制并且填充
     * @param string $str
     * @return string
     */
    public static function fillHex2Bin(string $str) : string {
        $fill_len = self::byteLen($str) * 8;
        $str_bin = self::hex2Bin($str);
        return self::fillLeft($str_bin, $fill_len);
    }

    /**
     * 十进制转十六进制并且填充
     * @param int $number 十进制数字
     * @param int $fill_len 填充长度
     * @return string
     */
    public static function fillDec2Hex(int $number, int $fill_len = 2) : string {
        $number_hex = dechex($number);
        return self::fillLeft($number_hex, $fill_len);
    }

    /**
     * 十六进制转二进制
     * @param string $str
     * @return string
     */
    public static function hex2Bin(string $str) : string {
        return base_convert($str, 16, 2);
    }

    /**
     * 十六进制转十进制
     * @param string $str
     * @return string
     */
    public static function hex2Dec(string $str) : string {
        return base_convert($str, 16, 10);
    }

    /**
     * 向左侧填充字符串
     * @param string $str 原字符串
     * @param int $len 填充长度
     * @param string $pad_string 填充字符串
     * @return string
     */
    public static function fillLeft(string $str, int $len = 8, string $pad_string = '0') : string {
        return str_pad($str, $len, $pad_string, STR_PAD_LEFT);
    }

    /**
     * 反向截取字符串
     * @param string $str
     * @param int $start 倒数第几位(从0开始)
     * @param int $length 取长度
     * @return string|null
     */
    public static function substrReverse(string $str, int $start = 0, int $length = 1) : ?string {
        $len = mb_strlen($str);

        if (($length <= 0) || ($start < 0)) {
            return null;
        }

        if ($start > ($len - 1)) {
            return null;
        }

        $real_end = $len - $start;
        $real_start = $real_end - $length;

        ($real_start < 0) && $real_start = 0;

        return mb_substr($str, $real_start, $length);
    }

    /**
     * @param  string $str 字符串
     * @param int $filter_len 按长度切割,
     * @param string $fill_str 前面补位字符
     * @param int $mix_len 最小字符串长度 (不足则前面补位)
     * @return array
     */
    public static function strCut2Arr(string $str, int $filter_len = 1, $fill_str = '', $mix_len = 0) : array {
        $len = mb_strlen($str);
        $pre_len = $len % $filter_len;

        //如果存在填充字符,先填充再切割
        if (mb_strlen($fill_str) > 0) {
            $new_len = 0;
            //无最大长度,则按分割长度进行补位
            if ($mix_len == 0) {
                if ($pre_len != 0) {
                    $new_len = $len + $filter_len - $pre_len;
                }
            } else if ($mix_len > 0) {
                ($mix_len >= $len) && $new_len = $mix_len;
            }
            //新的长度
            if ($new_len > 0) {
                $str = str_pad($str, $new_len, $fill_str, STR_PAD_LEFT);
                $len = strlen($str);
                $pre_len = $len % $filter_len;
            }
        }

        $arr = [];
        if ($pre_len > 0) {
            $arr[] = mb_substr($str, 0, $pre_len);
        }
        $lastlen = $len - $pre_len;

        if ($lastlen > 0) {
            $last = substr($str, $pre_len, $lastlen);

            $new_arr = str_split($last, $filter_len);
            if (!empty($new_arr)) {
                $arr = array_merge($arr, $new_arr);
            }
        }
        return $arr;
    }

    /**
     * 生成校验码
     * @param string $str 十六进制字符串
     * @return string
     */
    public static function generateCode(string $str) : string {
        $code_arr = self::strCut2Arr($str, 2);

        $last_code = '';
        foreach ($code_arr as $value) {
            $hex_val = hexdec($value);
            if ($last_code === '') {
                $last_code = $hex_val;
                continue;
            }

            $temp = $last_code;
            $last_code = $temp ^ $hex_val;
        }

        $hex_code = self::fillDec2Hex($last_code, 2);
        return $hex_code;
    }

    /**
     * 十六进制转中文
     * @param string $hex
     * @param string $to_encoding 编码(默认GBK)
     * @return string
     */
    public static function hex2Str(string $hex, string $to_encoding = 'gbk') : string {
        $string = '';

        if (strtolower($to_encoding) == 'gbk') {
            for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
                $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
            }

            $string = mb_convert_encoding($string, 'UTF-8', 'GBK');

        } else {

        }

        return $string;
    }

    /**
     * 创建随机字符串
     * @param string $type 生成字符串类型
     * @param int $len 长度
     * @return string
     */
    public static function randomString(string $type = 'alnum', int $len = 8) : string {
        switch ($type) {
            case 'basic':
                return (string)mt_rand();
            case 'alnum':
            case 'numeric':
            case 'nozero':
            case 'alpha':
            case 'hex':
                switch ($type) {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric':
                        $pool = '0123456789';
                        break;
                    case 'nozero':
                        $pool = '123456789';
                        break;
                    case 'hex':
                        $pool = '0123456789abcdefABCDEF';
                        break;
                }

                $times = (int)ceil($len / strlen($pool));
                return substr(str_shuffle(str_repeat($pool, $times)), 0, $len);
            case 'unique': // todo: remove in 3.1+
            case 'md5':
                return md5(uniqid(mt_rand()));
            case 'encrypt': // todo: remove in 3.1+
            case 'sha1':
                return sha1(uniqid(mt_rand(), true));
        }
    }
}