<?php

require __DIR__ . '/vendor/autoload.php';
require 'settings.php';

use OTE\Utils;
use OTE\Models;
use OTE\Business;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$now = new DateTime();
echo "Start Repair {$now->format('Y-m-d H:i:s')}\n\n";

// $log = new Logger('results');
// $log->pushHandler(new StreamHandler(__DIR__ . "/tmp/results-{$now->format('YmdHis')}.log", Logger::INFO));

$settings = new Settings();
$dbHelper = new Utils\MySqlHelper($settings->db['default']);
$dbHelper->connect();

$occupancyModel = new Models\OccupancyModel($dbHelper, $now);

foreach(glob(__DIR__ . "/results/negative-allocation*.json") as $fileName){
    $fileContents = file_get_contents($fileName);
    // var_export($fileContents);
    $occupancyModel->fixNegativeAllocations(json_decode($fileContents, true));
}

$dbHelper->close();
$now = new DateTime();
die("Done Repair {$now->format('Y-m-d H:i:s')}\n\n");
