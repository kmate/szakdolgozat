<?php

namespace app\tests\model;

use \fw\config\XmlConfiguration;
use \app\model\Task;
use \app\model\TaskModel;
use \PHPUnit_Extensions_Database_TestCase;

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

class TaskModelTest extends PHPUnit_Extensions_Database_TestCase
{
    private $_model;
    
    public function getConnection()
    {
        $configuration = new XmlConfiguration(MODEL_TEST_CONFIG_PATH);
        
        $this->_model = new TaskModel($configuration);
        $this->_model->connect();
        
        return $this->createDefaultDBConnection(
            $this->_model->getConnection(),
            $configuration->model->datasources->test_data_source->dbname
        );
    }
    
    public function getDataSet()
    {
        return $this->createFlatXmlDataSet(MODEL_DATASET_PATH);
    }
    
    public function testGetUserByIdReturnsNullWhenNotFound()
    {
        $task = $this->_model->getTaskById(1000);
        
        $this->assertNull($task);
    }
    
    public function testGetTaskByIdReturnsCorrectTask()
    {
        $expectedTask = Task::create(
            1,
            1,
            'Feladat 1',
            'Leírás 1',
            '2011-02-11',
            '2011-03-12',
            Task::PRIORITY_LOW,
            1
        );
        
        $expectedTask->user_full_name = 'Teszt Felhasználó';
        
        $task = $this->_model->getTaskById($expectedTask->id);
        
        $this->assertEquals($expectedTask, $task);
    }
    
    public function testGetTasksByUserIdReturnsCorrectTasks()
    {
        $expectedTaskIds = array(1, 7, 8, 10, 11, 12);
        
        $tasks   = $this->_model->getTasksByUserId(1);
        $taskIds = array_map(function($task) { return $task->id; }, $tasks);
        
        sort($taskIds);
        
        $this->assertEquals($expectedTaskIds, $taskIds);
    }
    
    public function testCountTasksByUserIdReturnsCorrectNumber()
    {
        $expectedTaskCount = 6;
        
        $taskCount = $this->_model->countTasksByUserId(1);
        
        $this->assertEquals($expectedTaskCount, $taskCount);
    }
    
    public function testGetTasksByUserIdWorksWithLimits()
    {
        $expectedTaskIds = array(10);
        
        $tasks   = $this->_model->getTasksByUserId(1, 1, 1);
        $taskIds = array_map(function($task) { return $task->id; }, $tasks);
        
        $this->assertEquals($expectedTaskIds, $taskIds);
    }
    
    public function testGetOtherUsersPublicTasksReturnsCorrectTasks()
    {
        $expectedTaskIds = array(4, 5, 6);
        
        $tasks   = $this->_model->getOtherUsersPublicTasks(1);
        $taskIds = array_map(function($task) { return $task->id; }, $tasks);
        
        sort($taskIds);
        
        $this->assertEquals($expectedTaskIds, $taskIds);
    }
    
    public function testGetOtherUsersPublicTasksFillsUserFullName()
    {
        $expectedTaskIds = array(4, 5, 6);
        
        $tasks         = $this->_model->getOtherUsersPublicTasks(1);
        $userFullNames = array_map(function($task) { return $task->user_full_name; }, $tasks);
        
        foreach ($userFullNames as $userFullName)
        {
            $this->assertNotEmpty($userFullName);
        }
    }
    
    
    public function testCountOtherUsersPublicTasksReturnsCorrectNumber()
    {
        $expectedTaskCount = 3;
        
        $taskCount = $this->_model->countOtherUsersPublicTasks(1);
        
        $this->assertEquals($expectedTaskCount, $taskCount);
    }
    
    public function testGetOtherUsersPublicTasksWorksWithLimits()
    {
        $expectedTaskIds = array(5);
        
        $tasks   = $this->_model->getOtherUsersPublicTasks(1, 2, 1);
        $taskIds = array_map(function($task) { return $task->id; }, $tasks);
        
        $this->assertEquals($expectedTaskIds, $taskIds);
    }
    
    public function testDeleteTaskById()
    {
        $this->_model->deleteTaskById(1);
        
        $this->assertNull($this->_model->getTaskById(1));
    }
    
    public function testAddTask()
    {
        $newTask = Task::create(
            null,
            1,
            'Feladat 13',
            null,
            '2011-05-05',
            '2011-05-07',
            Task::PRIORITY_LOW,
            0
        );
        
        $newTask->user_full_name = 'Teszt Felhasználó';
        
        $this->_model->addTask($newTask);
        
        $task = $this->_model->getTaskById($newTask->id);
        
        $this->assertEquals($newTask, $task);
    }
    
    public function testUpdateTask()
    {
        $modifiedTask = Task::create(
            1,
            2,
            'Feladat 1 - megváltoztatva',
            'Leírás 1 - megváltoztatva',
            '2010-05-08',
            '2011-05-03',
            Task::PRIORITY_HIGH,
            0
        );
        
        $modifiedTask->user_full_name = 'Második Teszt Felhasználó';
        
        $this->_model->updateTask($modifiedTask);
        
        $task = $this->_model->getTaskById($modifiedTask->id);
        
        $this->assertEquals($modifiedTask, $task);
    }
}