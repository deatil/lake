<?php

namespace Lake;

/**
 * 创建软链接
 *
 * @create 2020-4-26
 * @author deatil
 */
class Symlink
{
    /**
     * 创建软链接
     *
     * @param string $target 原路径
     * @param string $link 软连接路径
     *
     * @create 2020-4-26
     * @author deatil
     */
    public static function make($target, $link)
    {
        // 原文件存在且软连接不存在时，创建软连接
        if (!file_exists($target) 
            || file_exists($link)
        ) { 
            return false;
        }
        
        // 创建软连接
        $symlinkStatus = symlink($target, $link);
        if ($symlinkStatus === false) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 删除软链接
     *
     * @create 2020-4-27
     * @author deatil
     */
    public static function remove($link)
    {
        if (!file_exists($link)) { 
            return false;
        }
        
        if (is_dir($link)) {
            $rmdirStatus = rmdir($link);
            if ($rmdirStatus === true) {
                return true;
            }
        } elseif (is_file($link)) {
            $unlinkStatus = unlink($link);
            if ($unlinkStatus === true) {
                return true;
            }
        }
        
        return false;
    }
}
