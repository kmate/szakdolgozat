<?php

namespace fw\tests\log;

use \fw\Utils;
use \fw\log\Log;
use \fw\log\LogTarget;
use \PHPUnit_Framework_TestCase;

class LogTargetTest extends PHPUnit_Framework_TestCase
{
    private $_mockTarget;
    
    public function setUp()
    {
        $this->_mockTarget = $this->getMockForAbstractClass('\fw\log\LogTarget');
    }
    
    public function tearDown()
    {
        unset($this->_mockTarget);
    }
    
    public function testFormattingWorksWithDefaultFormatString()
    {
        $expectedLine  = date('d/M/Y:H:i:s O') . ' - [debug] source: message (unknown)';
        $formattedLine = $this->_mockTarget->format(Log::LEVEL_DEBUG, 'source', 'message');
        
        $this->assertEquals($expectedLine, $formattedLine);
    }
    
    /**
     * @dataProvider formatStringsProvider
     */
    public function testFormattingWorksWithCustomFormatStrings($formatString, $level, $source, $message, $expectedLine)
    {
        $expectedLine  = date('d/M/Y:H:i:s O') . ' - [debug] source: message (' . Utils::getClientIp() . ')';
        $formattedLine = $this->_mockTarget->format(Log::LEVEL_DEBUG, 'source', 'message');
        
        $this->assertEquals($expectedLine, $formattedLine);
    }
    
    public function formatStringsProvider()
    {
        return array(
            array('%l',       Log::LEVEL_ERROR,   '',   '',   'error'),
            array('%s - %m',  Log::LEVEL_WARNING, '',   '%l', Log::UNKNOWN_SOURCE . ' - %l'),
            array('%l%%%s%m', Log::LEVEL_INFO,    '%m', '%s', 'info%%%m%s'),
            array('%%t-%%h',  Log::LEVEL_DEBUG,   '',   '',   '%' . date('d/M/Y:H:i:s O') . '-%' . Utils::getClientIp())
        );
    }
}
