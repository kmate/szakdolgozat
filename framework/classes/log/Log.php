<?php

namespace fw\log;

class Log
{
    const LEVEL_ERROR   = 'error';
    const LEVEL_WARNING = 'warning';
    const LEVEL_INFO    = 'info';
    const LEVEL_DEBUG   = 'debug';
    
    const UNKNOWN_SOURCE = 'UNKNOWN';
    
    private $_targets;
    private $_targetLevels;
    private $_debugEnabled;
    
    public function __construct($debugEnabled = false)
    {
        $this->setDebugEnabled($debugEnabled);
        $this->removeAllTargets();
    }
    
    public function getDebugEnabled()
    {
        return $this->_debugEnabled;
    }
    
    public function setDebugEnabled($value)
    {
        $this->_debugEnabled = $value;
    }
    
    public function addTarget(LogTarget $target, array $levels = array())
    {
        if (0 < count($levels))
        {
            $levels = array_unique($levels);
            
            $this->_checkLevelsAreAllowed($levels);
        }
        else
        {
            $levels = $this->_getAllowedLevels();
        }
        
        if (!in_array($target, $this->_targets))
        {
            $this->_targets[]      = $target;
            $this->_targetLevels[] = $levels;
        }
    }
    
    private function _checkLevelsAreAllowed(array $levels)
    {
        foreach ($levels as $level)
        {
            if (!in_array($level, $this->_getAllowedLevels()))
            {
                throw new Exception(
                    'Invalid log level: \'' . $level . '\'',
                    Exception::INVALID_LOG_LEVEL
                );
            }
        }
    }
    
    private function _getAllowedLevels()
    {
        return array(
            self::LEVEL_ERROR,
            self::LEVEL_WARNING,
            self::LEVEL_INFO,
            self::LEVEL_DEBUG
        );
    }
    
    public function hasTarget()
    {
        return 0 < $this->getTargetCount();
    }
    
    public function getTargetCount()
    {
        return count($this->_targets);
    }
    
    public function removeAllTargets()
    {
        $this->_targets      = array();
        $this->_targetLevels = array();
    }
    
    public function error($message = '')
    {
        $this->_invokeWriteOnTargets(Log::LEVEL_ERROR, $message);
    }
    
    public function warning($message = '')
    {
        $this->_invokeWriteOnTargets(Log::LEVEL_WARNING, $message);
    }
    
    public function info($message = '')
    {
        $this->_invokeWriteOnTargets(Log::LEVEL_INFO, $message);
    }
    
    public function debug($message = '')
    {
        if ($this->_debugEnabled)
        {
            $this->_invokeWriteOnTargets(Log::LEVEL_DEBUG, $message);
        }
    }
    
    private function _invokeWriteOnTargets($level, $message)
    {
        list(, , $callerInfo) = debug_backtrace(false);
        
        $source = !empty($callerInfo['class']) ? $callerInfo['class'] : self::UNKNOWN_SOURCE;
        
        for ($i = 0; $i < count($this->_targets); ++$i)
        {
            if (in_array($level, $this->_targetLevels[$i]))
            {
                $this->_targets[$i]->write($level, $source, $message);
            }
        }
    }
}