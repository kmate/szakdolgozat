<?php

namespace app\tests\model;

use \fw\config\Configuration;
use \fw\config\XmlConfiguration;
use \app\model\DatabaseModel;
use \PHPUnit_Framework_TestCase;

if (!defined('MODEL_TEST_ASSETS_PATH'))
{
    define('MODEL_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'model');
}

if (!defined('MODEL_TEST_CONFIG_PATH'))
{
    define('MODEL_TEST_CONFIG_PATH', MODEL_TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'configuration.xml');
}

class DatabaseModelTest extends PHPUnit_Framework_TestCase
{
    private $_configuration;
    private $_model;
    
    public function setUp()
    {
        $this->_configuration = new XmlConfiguration(MODEL_TEST_CONFIG_PATH);
        $this->_model = new DatabaseModel($this->_configuration);
    }
    
    public function tearDown()
    {
        unset($this->_model);
    }
    
    /**
     * @expectedException     \app\model\DatabaseModelException
     * @expectedExceptionCode 1 (DatabaseModelException::DATASOURCE_NOT_FOUND)
     */
    public function testThrowsEceptionWhenDefaultDataSourceNotFound()
    {
        $this->_model = new DatabaseModel(new Configuration());
        $this->_model->connect();
    }
    
    /**
     * @expectedException     \app\model\DatabaseModelException
     * @expectedExceptionCode 1 (DatabaseModelException::DATASOURCE_NOT_FOUND)
     */
    public function testThrowsEceptionWhenNamedDataSourceNotFound()
    {
        $this->_model = new DatabaseModel(new Configuration());
        $this->_model->connect('missing_data_source');
    }
    
    public function testConnectToDefaultSource()
    {
        $this->_model->connect();
        
        $this->assertInstanceOf('\\PDO', $this->_model->getConnection());
    }
    
    public function testConnectToNamedDataSource()
    {
        $this->_model->connect('test_data_source');
        
        $this->assertInstanceOf('\\PDO', $this->_model->getConnection());
    }
    
    public function testConnectionsArePooled()
    {
        $this->_model->connect();
        $connection1 = $this->_model->getConnection();
        
        $model2 = new DatabaseModel($this->_configuration);
        $model2->connect();
        $connection2 = $model2->getConnection();
        
        $this->assertTrue($connection1 === $connection2);
    }
}