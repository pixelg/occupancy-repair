<?php

require __DIR__ . '/vendor/autoload.php';
require 'settings.php';

use OTE\Utils;
use OTE\Models;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$now = new DateTime();
echo "Start {$now->format('Y-m-d H:i:s')}!\n\n";

$log = new Logger('results');
$log->pushHandler(new StreamHandler(__DIR__ . "/tmp/results-{$now->format('YmdHis')}.log", Logger::INFO));

$propertiesLogger = new Logger('properties');
$propertiesLogger->pushHandler(new StreamHandler(__DIR__ . "/tmp/properties-{$now->format('YmdHis')}.log", Logger::INFO));
        
$settings = new Settings();
$dbHelper = new Utils\MySqlHelper($settings->db['default']);
$dbHelper->connect();

$roomModel = new Models\RoomModel($dbHelper);
$roomIds = $roomModel->fetchRoomIds(1623);

$roomUnitModel = new Models\RoomUnitModel($dbHelper);
$occupancyModel = new Models\OccupancyModel($dbHelper);
$rsyncProperties = [];

foreach($roomIds as $roomId){
    $dates = $occupancyModel->fetchDistinctDatesByRoomId($roomId, $now->format('Y-m-d'));
    
    if (empty($dates)){
        // $log->info("Room: $roomId has 0 occupancies.");
        continue;
    }
    
    $allocations = $occupancyModel->buildAllocations($roomId, $dates, true);
    $isValid = $occupancyModel->analyzeAllocations($allocations, true);
    
    if (!$isValid){
        $rsyncProperty = $roomModel->fetchProperty($roomId);
        
        if (empty($rsyncProperties[$rsyncProperty[0]['id']])){
            $propertiesLogger->info(sprintf("%s | %s | %s | %s", $rsyncProperty[0]['id'], $rsyncProperty[0]['name'], $rsyncProperty[0]['city'], $rsyncProperty[0]['cc_iso']));
            $rsyncProperties[$rsyncProperty[0]['id']] = $rsyncProperty;
        }
    }
}


$dbHelper->close();
$now = new DateTime();
die("Done {$now->format('Y-m-d H:i:s')}!\n\n");
