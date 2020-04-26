<?php

namespace lake;

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
     * @create 2020-4-26
     * @author deatil
     */
    public static function make($target, $link)
    {
        $manual = $target; // 原路径
        $manualLink = $link; // 软连接路径
        $isExistFile = true; // 原文件是否存在的标识
        
        // 原文件存在且软连接不存在时，创建软连接
        if (!file_exists($manual)) { 
            return false;
        }
        
        if (file_exists($manualLink)) { 
            return false;
        }
        
        // 创建软连接
        $symlinkStatus = symlink($manual, $manualLink);
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
        
        $rmdirStatus = rmdir($link);
        if ($rmdirStatus === false) {
            return false;
        }
        
        return true;
        
    }
}
