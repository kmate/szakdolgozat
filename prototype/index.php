<?php

// setting up the application
require_once 'bootstrap.php';

use \fw\config\Configuration;
use \fw\config\IniConfiguration;
use \fw\control\FrontController;

// loading configuration
$configurationPath = join(DIRECTORY_SEPARATOR, array(__DIR__, 'config', 'application.ini'));
$configuration     = new IniConfiguration($configurationPath);

// selecting active section
Configuration::setActiveSection(ACTIVE_CONFIGURATION);

// starting the application
$frontController = new FrontController($configuration);
$frontController->dispatch();