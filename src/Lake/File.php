<?php

namespace Lake;

/**
 * 文件类
 *
 * @create 2019-10-15
 * @author deatil
 */
class File
{

    /**
     * 计算文件大小
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function byteFormat($bytes)
    {
        $sizeText = [" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB"];
        return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $sizeText[$i];
    }

    /**
     * 读取文件内容
     * @param $filename  文件名
     * @return string 文件内容
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function readFile($filename)
    {
        $content = '';
        if (function_exists('file_get_contents')) {
            @$content = file_get_contents($filename);
        } else {
            if (@$fp = fopen($filename, 'r')) {
                @$content = fread($fp, filesize($filename));
                @fclose($fp);
            }
        }
        return $content;
    }

    /**
     * 写入文件
     * @param $filename
     * @param $writetext
     * @param string $openmod
     * @return bool
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function writeFile($filename, $writetext, $openmod = 'w')
    {
        if (@$fp = fopen($filename, $openmod)) {
            flock($fp, 2);
            fwrite($fp, $writetext);
            fclose($fp);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除全部
     *
     * @param $path
     * @param bool $delDir
     * @return bool
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function delAll($path, $delDir = false)
    {
        $handle = opendir($path);
        if ($handle) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    is_dir("$path/$item") ? self::delAll("$path/$item", $delDir) : unlink("$path/$item");
                }
            }
            closedir($handle);
            if ($delDir) {
                return rmdir($path);
            }
        } else {
            if (file_exists($path)) {
                return unlink($path);
            } else {
                return false;
            }
        }
    }

    /**
     * 删除
     * @param $dirName
     * @return bool
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function delDir($dirName)
    {
        if (!file_exists($dirName)) {
            return false;
        }

        $dir = opendir($dirName);
        while ($fileName = readdir($dir)) {
            $file = $dirName . '/' . $fileName;
            if ($fileName != '.' && $fileName != '..') {
                if (is_dir($file)) {
                    self::delDir($file);
                } else {
                    unlink($file);
                }
            }
        }
        closedir($dir);
        return rmdir($dirName);
    }

    /**
     * 复制
     * @param $surDir
     * @param $toDir
     * @return bool
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function copyDir($surDir, $toDir)
    {
        $surDir = rtrim($surDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (!file_exists($surDir)) {
            return false;
        }

        if (!file_exists($toDir)) {
            self::mkDir($toDir);
        }
        $file = opendir($surDir);
        while ($fileName = readdir($file)) {
            $file1 = $surDir . '/' . $fileName;
            $file2 = $toDir . '/' . $fileName;
            if ($fileName != '.' && $fileName != '..') {
                if (is_dir($file1)) {
                    self::copyDir($file1, $file2);
                } else {
                    copy($file1, $file2);
                }
            }
        }
        closedir($file);
        return true;
    }

    /**
     * 创建文件夹
     * @param $dir
     * @return bool
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function mkDir($dir)
    {
        $dir = rtrim($dir, '/') . '/';
        if (!is_dir($dir)) {
            if (mkdir($dir, 0700) == false) {
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path 路径
     * @param array $files
     *  文件类型数组
     *
     * @param string $preg
     * @return array 所有文件路径
     */
    public static function getFiles($path, &$files = array(), $preg = "/\.(gif|jpeg|jpg|png|bmp)$/i")
    {
        if (!is_dir($path)) {
            return null;
        }

        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path .'/' . $file; //'/' .

                if (is_dir($path2)) {
                    self::getFiles($path2, $files);
                } else {
                    if (preg_match($preg, $file)) {
                        $files [] = $path2;
                    }
                }
            }
        }
        return $files;
    }

    /**
     * @param $dir
     * @param bool $doc
     * @return array
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function getDirs($dir, $doc = false)
    {
        $dir = rtrim($dir, '/') . '/';
        $dirArray = [];
        if (false != ($handle = opendir($dir))) {
            $i = 0;
            $j = 0;
            while (false !== ($file = readdir($handle))) {
                if (is_dir($dir . $file)) { //判断是否文件夹
                    if ($file[0] != '.') {
                        $dirArray ['dir'] [$i] = $file;
                        $i++;
                    }

                } else {
                    if ($file[0] != '.') {
                        $dirArray ['file'] [$j] = $file;
                        $j++;
                    }

                }
            }
            closedir($handle);
        }
        return $dirArray;
    }

    /**
     * @param $dir
     * @return int|string
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function dirSize($dir)
    {
        if (!self::readable($dir)) {
            return 0;
        }
        
        $dir_list = opendir($dir);
        $dir_size = 0;
        while (false !== ($folder_or_file = readdir($dir_list))) {
            if ($folder_or_file != "." && $folder_or_file != "..") {
                if (is_dir("$dir/$folder_or_file")) {
                    $dir_size += self::dirSize("$dir/$folder_or_file");
                } else {
                    $dir_size += filesize("$dir/$folder_or_file");
                }
            }
        }
        closedir($dir_list);
        return $dir_size;
    }
    
    /**
     * @param null $dir
     * @return string
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function realSize($dir = null)
    {
        if (self::readable($dir)) {
            if (is_file($dir)) { // 对文件的判断
                return self::byteFormat(filesize($dir));
            } else {
                return self::byteFormat(self::dirSize($dir));
            }
        } else
            return "文件不存在";

    }

    /**
     * @param null $dir
     * @return bool
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function readable($dir = null)
    {
        if (($frst = file_get_contents($dir)) && is_file($dir)) {
            return true; // 是文件，并且可读
        } else { // 是目录
            if (is_dir($dir) && self::iscandir($dir)) {
                return true; // 目录可读
            } else {
                return false;
            }
        }
    }

    /**
     * @param null $dir
     * @return bool
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function writeable($dir = null)
    {
        if (is_file($dir)) { // 对文件的判断
            return is_writeable($dir);
        } elseif (is_dir($dir)) {
            // 开始写入测试;
            $file = '_______' . time() . rand() . '_______';
            $file = $dir . '/' . $file;
            if (file_put_contents($file, '//')) {
                unlink($file); // 删除测试文件
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        };
    }
    
    /**
     * 检测是否为空文件夹
     * @param $dir  目录名
     * @return boolean true 空， fasle 不为空
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function emptyDir($dir)
    {
        return (($files = @scandir($dir)) && count($files) <= 2);
    }

    /**
     * @param $path
     * @param int $property
     * @return bool
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function makeDir($path, $property = 0777)
    {
        return is_dir($path) or (self::makeDir(dirname($path), $property) and @mkdir($path, $property));
    }
    
    /**
     * 删除
     * @param $dirName
     * @return bool
     *
     * @create 2019-10-15
     * @author deatil
     */
    public function iDelFile($dir,$file_type='') 
    {
        if(is_dir($dir)){
            $files = self::iscandir($dir);
            //打开目录 //列出目录中的所有文件并去掉 . 和 ..
            foreach($files as $filename){
                if($filename!='.' && $filename!='..'){
                    if(!is_dir($dir.'/'.$filename)){
                        if(empty($file_type)){
                            unlink($dir.'/'.$filename);
                        }else{
                            if(is_array($file_type)){
                                //正则匹配指定文件
                                if(preg_match($file_type[0],$filename)){
                                    unlink($dir.'/'.$filename);
                                }
                            }else{
                                //指定包含某些字符串的文件
                                if(false!=stristr($filename,$file_type)){
                                    unlink($dir.'/'.$filename);
                                }
                            }
                        }
                    }else{
                        self::iDelFile($dir.'/'.$filename);
                        rmdir($dir.'/'.$filename);
                    }
                }
            }
        }else{
            if(file_exists($dir)) unlink($dir);
        }
    }
    
    /**
     * @param $dir
     * @return bool
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function iemptyDir($dir)
    {
        if (($files = self::iscandir($dir)) && count($files) <= 2)
            return true;
        return false;

    }

    /**
     * @param $dir
     * @param bool $file
     * @return array
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function scanDir($dir, $file = false)
    {
        if ($file == true) {
            $res = self::iscandir($dir);
            foreach ($res as $key => $value) {
                if (($res[$key][0]) == '.') {
                    unset($res[$key]);
                }
            }

            return $res;
        } else {
            $path = self::getDirs($dir);
            $dir = $path['dir'];
            foreach ($dir as $key => $value) {
                if (($dir[$key][0]) == '.') {
                    unset($dir[$key]);
                }
            }

            return $dir;
        }
    }

    /**
     * 创建时间
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function filemtime($file)
    {
        return filemtime($file);
    }

    /**
     * 创建时间
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function filectime($file)
    {
        return filectime($file);
    }

    /**
     * 更新时间
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function fileatime($file)
    {
        return fileatime($file);
    }
    
    /**
     * 部分空间为了安全起见，禁用scandir函数
     *
     * @param string $dir 路径
     * @return array
     *
     * @create 2019-10-15
     * @author deatil
     */
    public static function iscandir($dir, $type = 'all')
    {
        if(function_exists('scandir')){
            $files = scandir($dir);
        } else {
            $files = [];
            $mydir = dir($dir);
            while($file = $mydir->read())
            {
                $files[] = "$file";
            }
            $mydir->close();
        }
        $arr_file = [];
        foreach ($files as $key => $val) {
            if(($val != ".") AND ($val != "..")){
                if ('all' == $type) {
                    $arr_file[] = "$val";
                } else if ('file' == $type && is_file($val)) {
                    $arr_file[] = "$val";
                } else if ('dir' == $type && is_dir($val)) {
                    $arr_file[] = "$val";
                }
            }
        }

        return $arr_file;
    }
    
    /**
     * 写入数据到php.ini文件
     *
     * @param array $array 数据
     * @param string $file 文件
     * @return void
     *
     * @create 2020-7-28
     * @author deatil
     */
    public static function writePhpIni($array, $file)
    {
        if (empty($array)) {
            return false;
        }
        
        if (empty($file) || !file_exists($file)) {
            return false;
        }
        
        $res = [];
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $res[] = "[$key]";
                foreach ($val as $skey => $sval) {
                    $res[] = "$skey = $sval";
                }
            } else {
                $res[] = "$key = $val";
            }
        }
        
        $status = file_put_contents($file, implode("\r\n", $res));
        if ($status === false) {
            return false;
        }
        
        return true;
    }
    
}