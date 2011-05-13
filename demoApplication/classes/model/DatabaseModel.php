<?php

namespace app\model;

use \fw\config\Configuration;
use \PDO;

class DatabaseModel
{
    private static $_connectionPool = array();
    
    protected $_config;
    protected $_connection;
    
    public function __construct(Configuration $config)
    {
        $this->_config = $config;
    }
    
    public function connect($dataSourceId = null)
    {
        if (null === $dataSourceId)
        {
            $dataSourceId = $this->_config->model->datasources->get('default', '');
        }
        
        if (!$this->_config->model->datasources->has($dataSourceId))
        {
            throw new DatabaseModelException(
                'Data source not found: \'' . $dataSourceId . '\'',
                DatabaseModelException::DATASOURCE_NOT_FOUND
            );
        }
        
        $this->_connection = $this->_getPooledConnection($dataSourceId);
    }
    
    private function _getPooledConnection($dataSourceId)
    {
        if (isset(self::$_connectionPool[$dataSourceId]))
        {
            return self::$_connectionPool[$dataSourceId];
        }
        else
        {
            $dsn      = $this->_config->model->datasources->get($dataSourceId)->get('dsn', '');
            $userName = $this->_config->model->datasources->get($dataSourceId)->get('username', '');
            $password = $this->_config->model->datasources->get($dataSourceId)->get('password', '');
            
            $connection = new PDO($dsn, $userName, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->query('SET NAMES UTF8');
            
            self::$_connectionPool[$dataSourceId] = $connection;
            
            return $connection;
        }
    }
    
    public function getConnection()
    {
        return $this->_connection;
    }
}