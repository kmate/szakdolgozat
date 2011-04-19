<?php

namespace fw\tests\log;

use \fw\log\Log;
use \fw\log\OutputTarget;
use \PHPUnit_Extensions_OutputTestCase;

class OutputTargetTest extends PHPUnit_Extensions_OutputTestCase
{
    private $_target;
    
    public function setUp()
    {
        $this->_target = new OutputTarget();
    }
    
    public function tearDown()
    {
        unset($this->_target);
    }
    
    public function testWritesFormattedLineWithLineFeedToOutput()
    {
        $expectedLine = date('d/M/Y:H:i:s O') . ' - [debug] source: message (unknown)' . PHP_EOL;
        
        $this->_target->write(Log::LEVEL_DEBUG, 'source', 'message');
        
        $this->expectOutputString($expectedLine);
    }
}
