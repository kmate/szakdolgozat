<?php

namespace app\model;

class Task
{
    const FIELD_ID          = 'id';
    const FIELD_USER_ID     = 'user_id';
    const FIELD_TITLE       = 'title';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_START       = 'start';
    const FIELD_FINISH      = 'finish';
    const FIELD_PRIORITY    = 'priority';
    const FIELD_IS_PUBLIC   = 'is_public';
    
    const PRIORITY_LOW    = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH   = 'high';
    
    public $id;
    public $user_id;
    public $title;
    public $description;
    public $start;
    public $finish;
    public $priority = self::PRIORITY_NORMAL;
    public $is_public;
    
    public $user_full_name;
    
    public static function create($id, $user_id, $title, $description, $start, $finish, $priority, $is_public)
    {
        $task              = new Task();
        $task->id          = $id;
        $task->user_id     = $user_id;
        $task->title       = $title;
        $task->description = $description;
        $task->start       = $start;
        $task->finish      = $finish;
        $task->priority    = $priority;
        $task->is_public   = $is_public;
        
        return $task;
    }
}