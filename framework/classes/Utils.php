<?php

namespace fw;

class Utils
{
    public static function getClientIp()
    {
        $keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($keys as $key)
        {
            if (isset($_SERVER[$key]))
            {
                foreach (explode(',', $_SERVER[$key]) as $ip)
                {
                    if (false !== filter_var($ip, FILTER_VALIDATE_IP))
                    {
                        return $ip;
                    }
                }
            }
        }
        
        return 'unknown';
    }
    
    public static function getUserAgent()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            return $_SERVER['HTTP_USER_AGENT'];
        }
        else
        {
            return 'unknown';
        }
    }
    
    public static function setLocation($location)
    {
        header('Location: ' . $location);
        
        exit();
    }
}