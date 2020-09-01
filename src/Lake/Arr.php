<?php

namespace Lake;

/**
 * 数组
 *
 * @create 2020-7-23
 * @author deatil
 */
class Arr
{

    /**
     * 数组深度合并
     *
     * @param array $arr1 数组
     * @param array $arr2 数组
     * @return array
     *
     * @create 2020-7-23
     * @author deatil
     */
    public static function merge($arr1, $arr2 = [])
    {
        $merged = $arr1;
        
        if (empty($arr1)) {
            return $arr2;
        }
        
        if (empty($arr2)) {
            return $arr1;
        }
        
        foreach ($arr2 as $key => $value) {
            if (is_array($value) 
                && isset($merged[$key]) 
                && is_array($merged[$key])
            ) {
                $merged[$key] = self::merge($merged[$key], $value);
            } elseif (is_numeric($key)) {
                if (!in_array($value, $merged)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }
        
        return $merged;
    }
    
    /**
     * 返回数组结构
     * @param array $arr 输出的数据
     * @param string $blankspace 空格
     * @return string
     *
     * @create 2020-7-23
     * @author deatil
     */
    public static function varExport($arr = [], $blankspace = '')
    {
        $blank = '    ';
        $ret = "[\n";
        if (!empty($arr)) {
            foreach ($arr as $k => $v) {
                $ret .= $blankspace . $blank;
                $ret .= (is_numeric($k) ? '' : "'".$k."' => ");
                $_type = strtolower(gettype($v));
                switch($_type){
                    case 'integer':
                        $ret .= $v.",";
                        break;
                    case 'array':
                        $ret .= self::varExport($v, $blankspace . $blank).",";
                        break;
                    case 'null':
                        $ret .= "NULL,";
                        break;
                    default:
                        $ret  .= "'".$v."',";
                        break;
                }
                $ret .= "\n";
            }
        }
        
        $ret .= $blankspace . "]";
        return $ret;
    }
    
    /**
     * 打印输出数据到文件
     * @param mixed $data 输出的数据
     * @param boolean $force 强制替换
     * @param string|null $file 文件名称
     *
     * @create 2020-7-24
     * @author deatil
     */
    public static function printr($data, $force = false, $file = null)
    {
        if (is_null($file)) {
            return false;
        }
        
        if (is_string($data)) {
            $str = $data;
        } else {
            if (is_array($data) || is_object($data)) {
                $str = print_r($data, true);
            } else {
                $str = var_export($data, true);
            }
        }
        
        $str = $str . PHP_EOL;
        
        if ($force) {
            file_put_contents($file, $str);
        } else {
            file_put_contents($file, $str, FILE_APPEND);
        }
        
        return true;
    }
    
    /**
     * 对查询结果集进行排序
     * @access public
     * @param array $list 查询结果
     * @param string $field 排序的字段名
     * @param array $sortby 排序类型
     * asc正向排序 desc逆向排序 nat自然排序
     * @return array
     *
     * @create 2020-7-23
     * @author deatil
     */
    public static function sort($list, $field, $sortby = 'asc')
    {
        if (!is_array($list)) {
            return false;
        }
        
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }
        
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }
        
        return $resultSet;
    }
    
    /**
     * 把返回的数据集转换成Tree
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     *
     * @create 2020-7-23
     * @author deatil
     */
    public static function listToTree(
        $list, 
        $pk = 'id', 
        $pid = 'parentid', 
        $child = '_child', 
        $root = 0
    ) {
        // 创建Tree
        $tree = [];
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }
    
    /**
     * select返回的数组进行整数映射转换
     *
     * @param array $map  映射关系二维数组  
     *  [
     *      '字段名1' => array(映射关系数组),
     *      '字段名2' => array(映射关系数组),
     *      ......
     *  ]
     * @return array
     *
     *  [
     *      [
     *          'id' => 1,
     *          'title' => '标题',
     *          'status' => '1',
     *          'status_text' => '正常',
     *       ]
     *      ....
     *  ]
     *
     * @create 2020-7-23
     * @author deatil
     */
    public static function intToString(
        $data, 
        $map = [
            'status' => [
                1 => '正常', 
                -1 => '删除', 
                0 => '禁用', 
                2 => '未审核', 
                3 => '草稿'
            ]
        ]
    ) {
        if ($data === false || $data === null) {
            return $data;
        }
        
        $data = (array) $data;
        foreach ($data as $key => $row) {
            foreach ($map as $col => $pair) {
                if (isset($row[$col]) && isset($pair[$row[$col]])) {
                    $data[$key][$col . '_text'] = $pair[$row[$col]];
                }
            }
        }
        
        return $data;
    }
    
    /**
     * 数据签名认证
     * @param  array  $data 被认证的数据
     * @return string 签名
     *
     * @create 2020-7-23
     * @author deatil
     */
    public static function dataAuthSign($data)
    {
        // 数据类型检测
        if (!is_array($data)) {
            $data = (array) $data;
        }
        
        ksort($data); // 排序
        $code = http_build_query($data); // url编码并生成query字符串
        $sign = sha1($code); // 生成签名
        return $sign;
    }
    
    /**
     * 解析配置
     * @param string $data 配置值
     * @return array|string
     *
     * @create 2020-7-24
     * @author deatil
     */
    public static function parseAttr($data = '')
    {
        $array = preg_split('/[,;\r\n ]+/', trim($data, ",;\r\n"));
        if (strpos($data, ':')) {
            $value = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }
        return $value;
    }
    
    /**
     * 解析配置信息
     *
     * @create 2020-7-24
     * @author deatil
     */
    public static function parseFieldList($data = '')
    {
        if (empty($data)) {
            return [];
        }
        
        $json = json_decode($data, true);
        if (empty($json)) {
            return [];
        }
        
        $res = [];
        foreach ($json as $v) {
            $res[$v['key']] = $v['value'];
        }
        
        return $res;
    }

}
