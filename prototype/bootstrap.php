<?php

// setting up the framework
require_once '../framework/bootstrap.php';

// application constants
define('APPLICATION_CLASSPATH',     __DIR__ . DIRECTORY_SEPARATOR . 'classes');
define('VIEW_TEMPLATES_DIRECTORY',  __DIR__ . DIRECTORY_SEPARATOR . 'view');
define('CONTROLLER_CLASSPATH',      APPLICATION_CLASSPATH . DIRECTORY_SEPARATOR . 'control');
define('ACTIVE_CONFIGURATION',      'development');

// setting up classloader for application classes
new fw\ClassLoader(APPLICATION_CLASSPATH, 'app', true);
