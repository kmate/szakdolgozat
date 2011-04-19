<?php

namespace fw\tests\log;

use \fw\log\Log;
use \fw\log\FileTarget;
use \PHPUnit_Framework_TestCase;

require_once 'vfsStream/vfsStream.php';

use \vfsStream;
use \vfsStreamWrapper;
use \vfsStreamDirectory;

define('LOG_FILETARGET_VFS_DIRECTORY', 'logs');
define('LOG_FILETARGET_VFS_FILENAME',  'test.log');
define('LOG_FILETARGET_VFS_PATH',      LOG_FILETARGET_VFS_DIRECTORY . '/' . LOG_FILETARGET_VFS_FILENAME);

class FileTargetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException     \fw\log\FileException
     * @expectedExceptionCode 1 (FileException::UNABLE_TO_OPEN)
     */
    public function testConstructorThrowsExceptionWhenPermissionDenied()
    {
        vfsStream::setup(LOG_FILETARGET_VFS_DIRECTORY, 0400);
        
        $target = new FileTarget(vfsStream::url(LOG_FILETARGET_VFS_PATH));
    }
    
    public function testWritesFormattedLineWithLineFeedToFile()
    {
        $expectedLine = date('d/M/Y:H:i:s O') . ' - [debug] source: message (unknown)' . PHP_EOL;
        
        vfsStream::setup(LOG_FILETARGET_VFS_DIRECTORY);
        
        $target = new FileTarget(vfsStream::url(LOG_FILETARGET_VFS_PATH));
        $target->write(Log::LEVEL_DEBUG, 'source', 'message');
        
        $this->assertStringEqualsFile(vfsStream::url(LOG_FILETARGET_VFS_PATH), $expectedLine);
    }
}
