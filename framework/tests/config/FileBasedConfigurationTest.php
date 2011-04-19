<?php

namespace fw\tests\config;

use \fw\config\FileBasedConfiguration;
use \PHPUnit_Framework_TestCase;

if (!defined('CONFIG_TEST_ASSETS_PATH'))
{
    define('CONFIG_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'config');
}

define(
    'MISSING_CONFIG_PATH',
    join(DIRECTORY_SEPARATOR, array(CONFIG_TEST_ASSETS_PATH, 'Missing.config'))
);

class FileBasedConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException     \fw\config\Exception
     * @expectedExceptionCode 2 (Exception::UNABLE_TO_READ_FILE)
     */
    public function testConstructionWithMissingFileThrowsException()
    {
        $configurationStub = $this->getMockForAbstractClass('\fw\config\FileBasedConfiguration', array(MISSING_CONFIG_PATH));
    }
}
