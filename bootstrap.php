<?php

require_once "vendor/autoload.php";
require_once "config.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths     = array(__DIR__."/config/doctrine");
$isDevMode = true;

$config = Setup::createYAMLMetadataConfiguration($paths, $isDevMode);
$em = EntityManager::create($dbParams, $config);