<?php

namespace app\tests;

abstract class DatabaseSeleniumTestCase extends \PHPUnit_Extensions_SeleniumTestCase
{
    protected $databaseTester;
    
    protected function closeConnection(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        $this->getDatabaseTester()->closeConnection($connection);
    }
    
    protected abstract function getConnection();
    
    protected function getDatabaseTester()
    {
        if (empty($this->databaseTester)) {
            $this->databaseTester = $this->newDatabaseTester();
        }

        return $this->databaseTester;
    }
    
    protected abstract function getDataSet();
    
    protected function getSetUpOperation()
    {
        return \PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
    }
    
    protected function getTearDownOperation()
    {
        return \PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }
    
    protected function newDatabaseTester()
    {
        return new \PHPUnit_Extensions_Database_DefaultTester($this->getConnection());
    }
    
    protected function createDefaultDBConnection(\PDO $connection, $schema = '')
    {
        return new \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($connection, $schema);
    }
    
    protected function createFlatXMLDataSet($xmlFile)
    {
        return new \PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet($xmlFile);
    }
    
    protected function createXMLDataSet($xmlFile)
    {
        return new \PHPUnit_Extensions_Database_DataSet_XmlDataSet($xmlFile);
    }
    
    protected function createMySQLXMLDataSet($xmlFile)
    {
        return new \PHPUnit_Extensions_Database_DataSet_MysqlXmlDataSet($xmlFile);
    }
    
    protected function getOperations()
    {
        return new \PHPUnit_Extensions_Database_Operation_Factory();
    }
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->databaseTester = NULL;

        $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onSetUp();
    }
    
    protected function tearDown()
    {
        $this->getDatabaseTester()->setTearDownOperation($this->getTearDownOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onTearDown();
        
        $this->databaseTester = NULL;
    }
    
    public static function assertTablesEqual(\PHPUnit_Extensions_Database_DataSet_ITable $expected, \PHPUnit_Extensions_Database_DataSet_ITable $actual, $message = '')
    {
        $constraint = new \PHPUnit_Extensions_Database_Constraint_TableIsEqual($expected);
        
        self::assertThat($actual, $constraint, $message);
    }
    
    public static function assertDataSetsEqual(\PHPUnit_Extensions_Database_DataSet_IDataSet $expected, \PHPUnit_Extensions_Database_DataSet_IDataSet $actual, $message = '')
    {
        $constraint = new \PHPUnit_Extensions_Database_Constraint_DataSetIsEqual($expected);
        
        self::assertThat($actual, $constraint, $message);
    }
}