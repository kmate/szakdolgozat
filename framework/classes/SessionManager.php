<?php

namespace fw;

/**
 * Munkamenet-kezelő
 * 
 * @author Karácsony Máté
 */
class SessionManager
{
    const VAR_LOGGED_IN  = '__loggedIn';
    const VAR_CLIENT_IP  = '__clientIp';
    const VAR_USER_AGENT = '__userAgent';
    
    /**
     * Elindítja vagy folytatja az adott nevű munkamenetet
     * 
     * @param  string  a munkamenet neve
     * @return void
     */
    public static function start($sessionName)
    {
        session_name($sessionName);
        @session_start();
    }
    
    /**
     * Bejelentkezettnek jelöli meg az aktuális munkamenetet,
     * és kliens információkkal tölti fel a későbbi ellenőrzéshez
     *
     * @return void
     */
    public static function login()
    {
        session_regenerate_id();
        
        $_SESSION[self::VAR_LOGGED_IN]  = true;
        $_SESSION[self::VAR_CLIENT_IP]  = Utils::getClientIp();
        $_SESSION[self::VAR_USER_AGENT] = Utils::getUserAgent();
    }
    
    /**
     * Ellenőrzi, hogy érvényes-e az aktuális bejelentkezett munkamenet
     * 
     * @return bool
     */
    public static function isValid()
    {
        $loggedIn = isset($_SESSION[self::VAR_LOGGED_IN])
                 && true === $_SESSION[self::VAR_LOGGED_IN];
        
        $sameClientIp = isset($_SESSION[self::VAR_CLIENT_IP])
                     && 0 === strcmp(Utils::getClientIp(), $_SESSION[self::VAR_CLIENT_IP]);
        
        $sameUserAgent = isset($_SESSION[self::VAR_USER_AGENT])
                      && 0 === strcmp(Utils::getUserAgent(), $_SESSION[self::VAR_USER_AGENT]);
        
        return $loggedIn && $sameClientIp && $sameUserAgent;
    }
    
    /**
     * Megszűnteti és törli az aktuális munkamenetet
     * 
     * @return void
     */
    public static function destroy()
    {
        $_SESSION = array();
        
        if (ini_get('session.use_cookies'))
        {
            $params = session_get_cookie_params();
            
            setcookie(
                session_name(),
                '',
                time() - 86400,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        @session_destroy();
    }
}
