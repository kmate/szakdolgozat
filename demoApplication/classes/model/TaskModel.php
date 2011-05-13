<?php

namespace app\model;

use \PDO;
use \PDOStatement;

class TaskModel extends DatabaseModel
{
    const TASKS_PER_PAGE = 5;
    
    public function getTaskById($id)
    {
        $statement = $this->_connection->prepare(
            'SELECT *, (SELECT `user`.`full_name` FROM `user` WHERE `user`.`id` = `user_id`) as `user_full_name` ' .
            'FROM `task` WHERE `id` = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        $result = $statement->fetchObject('\\app\\model\\Task');
        
        return false !== $result ? $result : null;
    }
    
    public function getTasksByUserId($userId, $offset = 0, $rowCount = 0)
    {
        $statement = $this->_prepareWithLimit(
            'SELECT * FROM `task` WHERE `user_id` = :userId ORDER BY `start` ASC',
            $offset,
            $rowCount
        );
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();
        
        return $this->_fetchAllIntoTasks($statement);
    }
    
    public function countTasksByUserId($userId)
    {
        $statement = $this->_connection->prepare('SELECT COUNT(*) FROM `task` WHERE `user_id` = :userId');
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();
        
        return (int)$statement->fetchColumn();
    }
    
    public function getOtherUsersPublicTasks($excludedUserId, $offset = 0, $rowCount = 0)
    {
        $statement = $this->_prepareWithLimit(
            'SELECT *, (SELECT `user`.`full_name` FROM `user` WHERE `user`.`id` = `user_id`) as `user_full_name` ' .
            'FROM `task` WHERE `user_id` != :excludedUserId AND `is_public` = 1 ORDER BY `start` ASC',
            $offset,
            $rowCount
        );
        $statement->bindValue(':excludedUserId', $excludedUserId, PDO::PARAM_INT);
        $statement->execute();

        return $this->_fetchAllIntoTasks($statement);
    }
    
    public function countOtherUsersPublicTasks($excludedUserId)
    {
        $statement = $this->_connection->prepare(
            'SELECT COUNT(*) FROM `task` WHERE `user_id` != :excludedUserId AND `is_public` = 1'
        );
        $statement->bindValue(':excludedUserId', $excludedUserId, PDO::PARAM_INT);
        $statement->execute();
        
        return (int)$statement->fetchColumn();
    }
    
    private function _prepareWithLimit($query, $offset = 0, $rowCount = 0)
    {
        if ($rowCount > 0)
        {
            $statement = $this->_connection->prepare($query . ' LIMIT :offset, :rowCount');
            $statement->bindValue(':offset',   $offset,   PDO::PARAM_INT);
            $statement->bindValue(':rowCount', $rowCount, PDO::PARAM_INT);
        }
        else
        {
            $statement = $this->_connection->prepare($query);
        }
        
        return $statement;
    }
    
    private function _fetchAllIntoTasks(PDOStatement $statement)
    {
        return $statement->fetchAll(PDO::FETCH_CLASS, '\\app\\model\\Task');
    }
    
    public function deleteTaskById($id)
    {
        $statement = $this->_connection->prepare('DELETE FROM `task` WHERE `id` = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }
    
    public function addTask(Task $newTask)
    {
        $statement = $this->_connection->prepare(
            'INSERT INTO `task`' .
            '(`user_id`, `title`, `description`, `start`, `finish`, `priority`, `is_public`)' .
            'VALUES' .
            '(:user_id,  :title,  :description,  :start,  :finish,  :priority,  :is_public)'
        );
        $statement->bindValue(':user_id',     $newTask->user_id, PDO::PARAM_INT);
        $statement->bindValue(':title',       $newTask->title);
        $statement->bindValue(':description', $newTask->description);
        $statement->bindValue(':start',       $newTask->start);
        $statement->bindValue(':finish',      $newTask->finish);
        $statement->bindValue(':priority',    $newTask->priority);
        $statement->bindValue(':is_public',   $newTask->is_public, PDO::PARAM_INT);
        $statement->execute();
        
        $newTask->id = $this->_connection->lastInsertId();
    }
    
    public function updateTask(Task $modifiedTask)
    {
        $statement = $this->_connection->prepare(
            'UPDATE `task` SET ' .
            '`user_id`     = :user_id,' .
            '`title`       = :title,' .
            '`description` = :description,' .
            '`start`       = :start,' .
            '`finish`      = :finish,' .
            '`priority`    = :priority,' .
            '`is_public`   = :is_public ' .
            'WHERE `id`    = :id'
        );
        $statement->bindValue(':id',          $modifiedTask->id,      PDO::PARAM_INT);
        $statement->bindValue(':user_id',     $modifiedTask->user_id, PDO::PARAM_INT);
        $statement->bindValue(':title',       $modifiedTask->title);
        $statement->bindValue(':description', $modifiedTask->description);
        $statement->bindValue(':start',       $modifiedTask->start);
        $statement->bindValue(':finish',      $modifiedTask->finish);
        $statement->bindValue(':priority',    $modifiedTask->priority);
        $statement->bindValue(':is_public',   $modifiedTask->is_public, PDO::PARAM_INT);
        $statement->execute();
    }
}