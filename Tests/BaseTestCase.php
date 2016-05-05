<?php

require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(dirname(__FILE__)) . '/settings.php';

use OTE\Utils;

class BaseTestCase extends PHPUnit_Framework_TestCase
{
    protected static $dbHelper;
    
    public function __construct(){}
    
    public static function setUpBeforeClass()
    {
        $settings = new Settings();
        self::$dbHelper = new Utils\MySqlHelper($settings->db['test']);
        self::$dbHelper->connect();
    }
    
    public static function tearDownAfterClass()
    {
        self::$dbHelper->close();
    }
    
    
}