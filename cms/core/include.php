<?php
ini_set('memory_limit','536870912');
error_reporting(-1);
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include(__DIR__ ."/lib/include.php");
include(__DIR__ ."/exceptions/filesystemexception.php");
include(__DIR__ ."/main/include.php");
include(__DIR__ ."/cms.php");
LCMS\Core\CMS::initialize();
