<?php

// define framework classpath
define('FRAMEWORK_CLASSPATH', __DIR__ . DIRECTORY_SEPARATOR . 'classes');

// including classloader
require_once FRAMEWORK_CLASSPATH . DIRECTORY_SEPARATOR . 'ClassLoader.php';

// setting up classloader for other framework classes
new fw\ClassLoader(FRAMEWORK_CLASSPATH, 'fw', true);
