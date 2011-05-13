<?php

namespace app\services;

use \app\model\TaskModel;

use \fw\config\Configuration;
use \fw\rpc\Service;

class TaskService implements Service
{
    private $_config;
    
    public function getConfiguration()
    {
        return $this->_config;
    }
    
    public function setConfiguration(Configuration $config)
    {
        $this->_config = $config;
    }
    
    public function getOwnTasksByPage($page)
    {
        $taskModel = new TaskModel($this->getConfiguration());
        $taskModel->connect();
        
        $user = $_SESSION['user'];
        
        $ownListMaxPage = ceil($taskModel->countTasksByUserId($user->id) / TaskModel::TASKS_PER_PAGE);
        $ownListPage    = max(1, min((int)$page, $ownListMaxPage));
        
        $_SESSION['ownListPage'] = $ownListPage;
        
        $offset = ((int)$ownListPage - 1) * TaskModel::TASKS_PER_PAGE;
        
        $result              = new \stdClass();
        $result->tasks       = $taskModel->getTasksByUserId($user->id, $offset, TaskModel::TASKS_PER_PAGE);
        $result->currentPage = $ownListPage;
        $result->totalPages  = max($ownListMaxPage, 1);
        
        return $result;
    }
    
    public function getPublicTasksByPage($page)
    {
        $taskModel = new TaskModel($this->getConfiguration());
        $taskModel->connect();
        
        $user = $_SESSION['user'];
        
        $publicListMaxPage = ceil($taskModel->countOtherUsersPublicTasks($user->id) / TaskModel::TASKS_PER_PAGE);
        $publicListPage    = max(1, min((int)$page, $publicListMaxPage));
        
        $_SESSION['publicListPage'] = $publicListPage;
        
        $offset = ((int)$publicListPage - 1) * TaskModel::TASKS_PER_PAGE;
        
        $result              = new \stdClass();
        $result->tasks       = $taskModel->getOtherUsersPublicTasks($user->id, $offset, TaskModel::TASKS_PER_PAGE);
        $result->currentPage = $publicListPage;
        $result->totalPages  = max($publicListMaxPage, 1);
        
        return $result;
    }
}