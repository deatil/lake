<?php

namespace Lake;

/**
 * 字符
 *
 * @create 2020-7-23
 * @author deatil
 */
class Str
{
    /**
     * 字符串转换为数组，主要用于把分隔符调整到第二个参数
     * @param  string $str  要分割的字符串
     * @param  string $glue 分割符
     * @return array
     *
     * @create 2020-7-24
     * @author deatil
     */
    public static function str2arr($str, $glue = ',')
    {
        return explode($glue, $str);
    }
    
    /**
     * 数组转换为字符串，主要用于把分隔符调整到第二个参数
     * @param  array  $arr  要连接的数组
     * @param  string $glue 分割符
     * @return string
     *
     * @create 2020-7-24
     * @author deatil
     */
    public static function arr2str($arr, $glue = ',')
    {
        if (is_string($arr)) {
            return $arr;
        }
        
        return implode($glue, $arr);
    }
    
    /**
     * 根据PHP各种类型变量生成唯一标识号
     * @param mixed $mix 变量
     * @return string
     *
     * @create 2020-7-24
     * @author deatil
     */
    public static function toGuidString($mix)
    {
        if (is_object($mix)) {
            return spl_object_hash($mix);
        } elseif (is_resource($mix)) {
            $mix = get_resource_type($mix) . strval($mix);
        } else {
            $mix = serialize($mix);
        }
        return md5($mix);
    }
    
    /**
     * 判断是否为序列化
     *
     * @create 2019-7-2
     * @author deatil
     */
    public static function isSerialized($data) 
    {
        $data = trim( $data );
        if ('N;' == $data) {
            return true;
        }
        if (!preg_match( '/^([adObis]):/', $data, $badions )) {
            return false;
        }
        
        switch ($badions[1]) {
            case 'a':
            case 'O':
            case 's':
                if (preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) {
                    return true;
                }
                break;
            case 'b':
            case 'i':
            case 'd':
                if (preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data )) {
                    return true;
                }
            break;
        }
        return false;
    }
    
    /**
     * 检查字符串是否是UTF8编码
     * @param string $string 字符串
     * @return Boolean
     */
    public static function isUtf8($string) {
        $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($string[$i]);
            if ($c > 128) {
                if (($c >= 254)) return false;
                elseif ($c >= 252) $bits = 6;
                elseif ($c >= 248) $bits = 5;
                elseif ($c >= 240) $bits = 4;
                elseif ($c >= 224) $bits = 3;
                elseif ($c >= 192) $bits = 2;
                else return false;
                if (($i + $bits) > $len) return false;
                while ($bits > 1) {
                    $i++;
                    $b = ord($string[$i]);
                    if ($b < 128 || $b > 191) return false;
                    $bits--;
                }
            }
        }

        return true;
    }
    
    /**
     * 字符截取
     * @param $string 需要截取的字符串
     * @param $length 长度
     * @param $dot 追加字符
     * @return string
     *
     * @create 2020-7-24
     * @author deatil
     */
    public static function wordCut($sourcestr, $length, $dot = '...')
    {
        $returnstr = '';
        $i = 0;
        $n = 0;
        $str_length = strlen($sourcestr); // 字符串的字节数
        while (($n < $length) && ($i <= $str_length)) {
            $temp_str = substr($sourcestr, $i, 1);
            $ascnum = Ord($temp_str); // 得到字符串中第$i位字符的ascii码
            if ($ascnum >= 224) { // 如果ASCII位高与224，
                $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
                $i = $i + 3; // 实际Byte计为3
                $n++; // 字串长度计1
            } elseif ($ascnum >= 192) { // 如果ASCII位高与192，
                $returnstr = $returnstr . substr($sourcestr, $i, 2); // 根据UTF-8编码规范，将2个连续的字符计为单个字符
                $i = $i + 2; // 实际Byte计为2
                $n++; // 字串长度计1
            } elseif ($ascnum >= 65 && $ascnum <= 90) {
                // 如果是大写字母，
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1; // 实际的Byte数仍计1个
                $n++; // 但考虑整体美观，大写字母计成一个高位字符
            } else {
                // 其他情况下，包括小写字母和半角标点符号，
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1; // 实际的Byte数计1个
                $n = $n + 0.5; // 小写字母和半角标点等与半个高位字符宽...
            }
        }
        if ($str_length > strlen($returnstr)) {
            $returnstr = $returnstr . $dot; // 超过长度时在尾处加上省略号
        }
        return $returnstr;
    }
    
    /**
     * 安全过滤函数
     * @param $string
     * @return string
     *
     * @create 2020-7-24
     * @author deatil
     */
    public static function safeReplace($string)
    {
        $string = str_replace('%20', '', $string);
        $string = str_replace('%27', '', $string);
        $string = str_replace('%2527', '', $string);
        $string = str_replace('*', '', $string);
        $string = str_replace('"', '&quot;', $string);
        $string = str_replace("'", '', $string);
        $string = str_replace('"', '', $string);
        $string = str_replace(';', '', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('>', '&gt;', $string);
        $string = str_replace("{", '', $string);
        $string = str_replace('}', '', $string);
        $string = str_replace('\\', '', $string);
        return $string;
    }

}
