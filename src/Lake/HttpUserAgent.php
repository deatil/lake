<?php

namespace Lake;

/**
 * 请求头管理
 *
 * @create 2020-8-14
 * @author deatil
 */
class HttpUserAgent 
{

    /**
     * 获取操作系统 
     * $userAgent = $_SERVER['HTTP_USER_AGENT'];
     * @return string
     *
     * @create 2020-8-14
     * @author deatil
     */
    public static function getOs($userAgent = '')
    {
        if (empty($userAgent)) {
            return 'Other';
        }
        
        $agent = strtolower($userAgent);
        if (strpos($agent, 'windows nt')) {
            $platform = 'Windows';
        } elseif (strpos($agent, 'macintosh')) {
            $platform = 'MacOS';
        } elseif (strpos($agent, 'ipod')) {
            $platform = 'iPod';
        } elseif (strpos($agent, 'ipad')) {
            $platform = 'iPad';
        } elseif (strpos($agent, 'iphone')) {
            $platform = 'iPhone';
        } elseif (strpos($agent, 'android')) {
            $platform = 'Android';
        } elseif (strpos($agent, 'unix')) {
            $platform = 'Unix';
        } elseif (strpos($agent, 'linux')) {
            $platform = 'Linux';
        } else {
            $platform = 'Other';
        }
        return $platform;
    }
    
    /**
     * 获取浏览器
     * @return string
     *
     * @create 2020-8-14
     * @author deatil
     */
    public static function getBrowser($userAgent = '')
    {
        if (empty($userAgent)) {
            return 'Other';
        }
        
        $agent = $userAgent;
        if (strpos($agent, 'MSIE') !== false 
            || strpos($agent, 'rv:11.0') //ie11判断
        ) {
            return "IE";
        } elseif (strpos($agent, 'Firefox') !== false) {
            return "Firefox";
        } elseif (strpos($agent, 'Chrome') !== false) {
            return "Chrome";
        } elseif (strpos($agent, 'Opera') !== false) {
            return 'Opera';
        } elseif ((strpos($agent, 'Chrome') == false) 
            && strpos($agent, 'Safari') !== false
        ) {
            return 'Safari';
        } else {
            return 'Unknown';
        }
    }
}
