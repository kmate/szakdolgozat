<?php

namespace fw\tests\log;

use \fw\log\Log;
use \fw\log\LogTarget;
use \PHPUnit_Framework_TestCase;

class LogTest extends PHPUnit_Framework_TestCase
{
    const TEST_MESSAGE = 'test message';
    
    private $_log;
    private $_mockTarget;
    
    public function setUp()
    {
        $this->_log        = new Log();
        $this->_mockTarget = $this->getMockForAbstractClass('\fw\log\LogTarget');
    }
    
    public function tearDown()
    {
        unset($this->_log);
        unset($this->_mockTarget);
    }
    
    public function testNewLogHasNoLogTarget()
    {
        $this->assertFalse($this->_log->hasTarget());
    }
    
    public function testLogHasTargetAfterAddingOne()
    {
        $this->_log->addTarget($this->_mockTarget);
        
        $this->assertTrue($this->_log->hasTarget());
    }
    
    public function testAddingSameLogTargetTwiceResultsInAddedOnce()
    {
        $this->_log->addTarget($this->_mockTarget);
        $this->_log->addTarget($this->_mockTarget);
        
        $this->assertEquals(1, $this->_log->getTargetCount());
    }
    
    public function testLogHaveZeroTargetsAfterAllRemoved()
    {
        $this->_log->addTarget($this->_mockTarget);
        $this->_log->removeAllTargets();
        
        $this->assertEquals(0, $this->_log->getTargetCount());
    }
    
    public function testWritingErrorInvokesWriteOnTargets()
    {
        $this->_log->addTarget($this->_getWriteMockForLevel(Log::LEVEL_ERROR));
        $this->_log->error(self::TEST_MESSAGE);
    }
    
    public function testWritingWarningInvokesWriteOnTargets()
    {
        $this->_log->addTarget($this->_getWriteMockForLevel(Log::LEVEL_WARNING));
        $this->_log->warning(self::TEST_MESSAGE);
    }
    
    public function testWritingInfoInvokesWriteOnTargets()
    {
        $this->_log->addTarget($this->_getWriteMockForLevel(Log::LEVEL_INFO));
        $this->_log->info(self::TEST_MESSAGE);
    }
    
    public function testDebugDisabledByDefault()
    {
        $this->assertFalse($this->_log->getDebugEnabled());
    }
    
    public function testWritingDebugDoesNotInvokeWriteOnTargetsWhenDisabled()
    {
        $this->_log->setDebugEnabled(false);
        $this->_log->addTarget($this->_getNeverInvokedWriteMockForLevel(Log::LEVEL_DEBUG));
        $this->_log->debug(self::TEST_MESSAGE);
    }
    
    public function testWritingDebugInvokesWriteOnTargetsWhenEnabled()
    {
        $this->_log->setDebugEnabled(true);
        $this->_log->addTarget($this->_getWriteMockForLevel(Log::LEVEL_DEBUG));
        $this->_log->debug(self::TEST_MESSAGE);
    }
    
    public function testAddingTargetWithNotMatchingLevelWillNotLog()
    {
        $this->_log->addTarget($this->_getNeverInvokedWriteMockForLevel(Log::LEVEL_INFO), array(Log::LEVEL_ERROR));
        $this->_log->info(self::TEST_MESSAGE);
    }
    
    /**
     * @expectedException     \fw\log\Exception
     * @expectedExceptionCode 1 (Exception::INVALID_LOG_LEVEL)
     */
    public function testAddingTargetWithInvalidLevelThrowsException()
    {
        $this->_log->addTarget($this->_getNeverInvokedWriteMockForLevel(Log::LEVEL_INFO), array('invalid log level'));
        $this->_log->info(self::TEST_MESSAGE);
    }
    
    private function _getWriteMockForLevel($level)
    {
        $this->_mockTarget
            ->expects($this->once())
            ->method('write')
            ->with($level, __CLASS__, self::TEST_MESSAGE);
        
        return $this->_mockTarget;
    }
    
    private function _getNeverInvokedWriteMockForLevel($level)
    {
        $this->_mockTarget
            ->expects($this->never())
            ->method('write');
        
        return $this->_mockTarget;
    }
}
