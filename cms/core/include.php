<?php
ini_set('memory_limit','536870912');
error_reporting(-1);
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include(__DIR__ ."/lib/include.php");
include(__DIR__ ."/softlib/include.php");
include(__DIR__ ."/exceptions/filesystemexception.php");
include(__DIR__ ."/pool.php");
include(__DIR__ ."/path.php");
include(__DIR__ ."/io.php");
include(__DIR__ ."/code.php");
include(__DIR__ ."/config.php");
include(__DIR__ ."/ini.php");
include(__DIR__ ."/cms.php");
LCMS\Core\CMS::initialize();

