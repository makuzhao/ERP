<?php

/*
 * 公共函数库
 * 
 */

/*
 * msubustr()
 * $str:要截取的字符串
 * $start=0：开始位置，默认从0开始
 * $length：截取长度
 * $charset=”utf-8″：字符编码，默认UTF－8
 * $suffix=true：是否在截取后的字符后面显示省略号，默认true显示，false为不显示
 */
namespace Common;

function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
    if (function_exists("mb_substr")) {
        if ($suffix && strlen($str) > $length)
            return mb_substr($str, $start, $length, $charset) . "...";
        else
            return mb_substr($str, $start, $length, $charset);
    }
    elseif (function_exists('iconv_substr')) {
        if ($suffix && strlen($str) > $length)
            return iconv_substr($str, $start, $length, $charset) . "...";
        else
            return iconv_substr($str, $start, $length, $charset);
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    if ($suffix)
        return $slice . "…";
    return $slice;
}
/*
 * 下限值红色提醒
 */

function red($k) {

    if ($k <= 0) {
        echo "<font style='font-size:20px;color:red'>$str</font>&nbsp;进货";
    } else {
        echo $k;
    }
}
