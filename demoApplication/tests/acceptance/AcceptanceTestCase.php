<?php

namespace app\tests\acceptance;

use \fw\config\XmlConfiguration;
use \app\model\DatabaseModel;
use \app\tests\DatabaseSeleniumTestCase;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'DatabaseSeleniumTestCase.php';

if (!defined('MODEL_TEST_ASSETS_PATH'))
{
    define('MODEL_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'model');
}

if (!defined('MODEL_TEST_CONFIG_PATH'))
{
    define('MODEL_TEST_CONFIG_PATH', MODEL_TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'configuration.xml');
}

if (!defined('MODEL_DATASET_PATH'))
{
    define('MODEL_DATASET_PATH', MODEL_TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'data.xml');
}

class AcceptanceTestCase extends DatabaseSeleniumTestCase
{
    protected $coverageScriptUrl = 'http://localhost/phpunit_coverage.php';
    
    public function setUp()
    {
        parent::setUp();
        
        $this->setBrowserUrl(TEST_BASE_URL);
    }
    
    public function getConnection()
    {
        $configuration = new XmlConfiguration(MODEL_TEST_CONFIG_PATH);
        
        $model = new DatabaseModel($configuration);
        $model->connect();
        
        return $this->createDefaultDBConnection(
            $model->getConnection(),
            $configuration->model->datasources->test_data_source->dbname
        );
    }
    
    public function getDataSet()
    {
        return $this->createFlatXmlDataSet(MODEL_DATASET_PATH);
    }
}