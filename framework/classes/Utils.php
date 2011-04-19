<?php

namespace fw;

class Utils
{
    public static function getClientIp()
    {
        $ip = false;
        
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip)
            {
                array_unshift($ips, $ip);
                $ip = false;
            }
            
            for ($i = 0; $i < count($ips); ++$i)
            {
                if (!preg_match('/^(?:10|172\.(?:1[6-9]|2\d|3[01])|192\.168)\./', $ips[$i]))
                {
                    if (ip2long($ips[$i]) != false)
                    {
                        $ip = $ips[$i];
                        break;
                    }
                }
            }
        }
        
        if (!$ip && !empty($_SERVER['REMOTE_ADDR']))
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip ? $ip : 'unknown';
    }
}