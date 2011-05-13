<?php

namespace app\control;

use \app\model\Task;
use \app\model\TaskModel;

use \fw\KeyValueStorage;

use \fw\control\Context;
use \fw\control\Controller;
use \fw\control\RouteInfo;
use \fw\control\UrlRouter;

use \fw\input\DateValidator;
use \fw\input\EnumValidator;
use \fw\input\EmailValidator;
use \fw\input\StringValidator;
use \fw\input\ValidatorGroup;

class TaskController extends Controller
{
    private $_taskModel;
    
    public function __construct(Context $context)
    {
        parent::__construct($context);
        
        $this->_taskModel = new TaskModel($this->configuration);
    }
    
    public function indexAction()
    {
        $this->frontController->forwardExternal(new RouteInfo('task', 'list'));
    }
    
    public function listAction()
    {
        $user      = $_SESSION['user'];
        $taskModel = $this->_taskModel;
        $taskModel->connect();
        
        $template = $this->view->getActionTemplate();
        
        $template->user         = $user;
        $template->taskModel    = $taskModel;
        $template->tasksPerPage = TaskModel::TASKS_PER_PAGE;
        
        $ownListMaxPage    = ceil($taskModel->countTasksByUserId($user->id) / TaskModel::TASKS_PER_PAGE);
        $publicListMaxPage = ceil($taskModel->countOtherUsersPublicTasks($user->id) / TaskModel::TASKS_PER_PAGE);
        
        $ownListPage    = isset($_SESSION['ownListPage'])    ? $_SESSION['ownListPage']    : 1;
        $publicListPage = isset($_SESSION['publicListPage']) ? $_SESSION['publicListPage'] : 1;
        
        $ownListPage    = max(1, min((int)$this->routeParameters->get('ownListPage',    $ownListPage),    $ownListMaxPage));
        $publicListPage = max(1, min((int)$this->routeParameters->get('publicListPage', $publicListPage), $publicListMaxPage));
        
        $_SESSION['ownListPage']    = $ownListPage;
        $_SESSION['publicListPage'] = $publicListPage;
        
        $template->ownListPage      = $ownListPage;
        $template->ownTotalPages    = max($ownListMaxPage, 1);
        
        $template->publicListPage   = $publicListPage;
        $template->publicTotalPages = max($publicListMaxPage, 1);
    }
    
    public function viewAction()
    {
        $this->_taskModel->connect();
        
        $task = $this->_forwardIfTaskNotPublicOrOwn();
        
        $this->view->getActionTemplate()->showUser = $task->user_id !== $_SESSION['user']->id;
        $this->view->getActionTemplate()->task     = $task;
    }
    
    public function newAction()
    {
        $template = $this->view->getActionTemplate();
        $template->validationErrors = new KeyValueStorage();
        $template->parameters       = new KeyValueStorage();
        
        $parameters = $this->postParameters;
        
        if ($parameters->has('add'))
        {
            $this->_taskModel->connect();
            
            $validator = $this->_getTaskFormValidator($parameters->start);
            
            if (!$validator->validate($parameters))
            {
                $template->validationErrors = $validator->getLastErrors();
                $template->parameters       = $parameters;
            }
            else
            {
                $newTask = Task::create(
                    null,
                    $_SESSION['user']->id,
                    $parameters->get('title'),
                    $parameters->get('description'),
                    $parameters->get('start'),
                    $parameters->get('finish'),
                    $parameters->get('priority'),
                    'on' === $parameters->get('is_public') ? 1 : 0
                );
                
                $this->_taskModel->addTask($newTask);
                
                $successTemplate         = $this->view->createTemplate('task/add-successful');
                $successTemplate->taskId = $newTask->id;
                
                $this->view->setActionTemplate($successTemplate);
            }
        }
    }
    
    public function editAction()
    {
        $this->_taskModel->connect();
        
        $task = $this->_forwardIfTaskNotOwn();
        
        $template = $this->view->getActionTemplate();
        $template->validationErrors = new KeyValueStorage();
        $template->parameters       = new KeyValueStorage();
        
        $parameters = $this->postParameters;
        
        $this->view->getActionTemplate()->task = $task;
        
        if ($parameters->has('update'))
        {
            $validator = $this->_getTaskFormValidator($parameters->start);
            
            if (!$validator->validate($parameters))
            {
                $template->validationErrors = $validator->getLastErrors();
                $template->parameters       = $parameters;
            }
            else
            {
                $task->title       = $parameters->get('title');
                $task->description = $parameters->get('description');
                $task->start       = $parameters->get('start');
                $task->finish      = $parameters->get('finish');
                $task->priority    = $parameters->get('priority');
                $task->is_public   = 'on' === $parameters->get('is_public') ? 1 : 0;
                
                $this->_taskModel->updateTask($task);
                
                $successTemplate         = $this->view->createTemplate('task/update-successful');
                $successTemplate->taskId = $task->id;
                
                $this->view->setActionTemplate($successTemplate);
            }
        }
    }
    
    public function confirmDeleteAction()
    {
        $this->view->getActionTemplate()->taskId = (int)$this->routeParameters->get('taskId', 0);
    }
    
    public function deleteAction()
    {
        $this->_taskModel->connect();
        
        $task = $this->_forwardIfTaskNotOwn();
        
        $this->_taskModel->deleteTaskById($task->id);
    }
    
    private function _forwardIfTaskNotExists()
    {
        $taskId = (int)$this->routeParameters->get('taskId', 0);
        $task   = $this->_taskModel->getTaskById($taskId);
        
        if (null == $task)
        {
            $this->frontController->forward(new RouteInfo('task', 'not-exists'));
        }
        else
        {
            return $task;
        }
    }
    
    private function _forwardIfTaskNotPublicOrOwn()
    {
        $task = $this->_forwardIfTaskNotExists();
        
        if (!$task->is_public && $task->user_id !== $_SESSION['user']->id)
        {
            $this->frontController->forward(new RouteInfo('task', 'not-public'));
        }
        else
        {
            return $task;
        }
    }
    
    private function _forwardIfTaskNotOwn()
    {
        $task = $this->_forwardIfTaskNotExists();
        
        if ($task->user_id !== $_SESSION['user']->id)
        {
            $this->frontController->forward(new RouteInfo('task', 'not-own'));
        }
        else
        {
            return $task;
        }
    }
    
    private function _getTaskFormValidator($startValue)
    {
        $validator = ValidatorGroup::build();
        $validator->addValidator(StringValidator::build()->maxLength(255)->required(), 'title', '')
                  ->addValidator(StringValidator::build()->maxLength(4096), 'description', '')
                  ->addValidator(
                        DateValidator::build()->outputFormat('Y-m-d')->required(),
                        'start'
                  )
                  ->addValidator(
                        DateValidator::build()->outputFormat('Y-m-d')->notBefore($startValue)->required(),
                        'finish'
                    )
                  ->addValidator(
                        EnumValidator::build()->acceptValue(Task::PRIORITY_LOW)
                                              ->acceptValue(Task::PRIORITY_NORMAL)
                                              ->acceptValue(Task::PRIORITY_HIGH)
                                              ->required(),
                        'priority',
                        Task::PRIORITY_NORMAL
                  )
                  ->addValidator(
                        EnumValidator::build()->acceptValue('on')
                                              ->acceptValue('off'),
                        'is_public',
                        'off'
                  );
        
        return $validator;
    }
    
    public function notExistsAction()
    {
    }
    
    public function notPublicAction()
    {
    }
    
    public function notOwnAction()
    {
    }
}