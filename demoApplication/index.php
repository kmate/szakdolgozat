<?php

// setting up the application
require_once 'bootstrap.php';

use \app\view\CustomTemplateView;

use \fw\config\Configuration;
use \fw\config\IniConfiguration;
use \fw\control\FrontController;
use \fw\control\HttpContext;

// loading configuration
$configurationPath = join(DIRECTORY_SEPARATOR, array(__DIR__, 'config', 'application.ini'));
$configuration     = new IniConfiguration($configurationPath);

// selecting active section
Configuration::setActiveSection(ACTIVE_CONFIGURATION);

// injecting custom view
$context = new HttpContext();
$context->setView(new CustomTemplateView($configuration));

// starting the application
$frontController = new FrontController($configuration);
$frontController->setContext($context);
$frontController->dispatch();