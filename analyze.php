<?php

require __DIR__ . '/vendor/autoload.php';
require 'settings.php';

use OTE\Utils;
use OTE\Models;
use OTE\Business;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$now = new DateTime();
echo "Start Analyzer {$now->format('Y-m-d H:i:s')}\n\n";

$log = new Logger('analyzer');
$log->pushHandler(new StreamHandler(__DIR__ . "/tmp/analyzer-{$now->format('YmdHis')}.log", Logger::INFO));

$settings = new Settings();
$dbHelper = new Utils\MySqlHelper($settings->db['default']);
$dbHelper->connect();

$roomModel = new Models\RoomModel($dbHelper);
$roomIds = $roomModel->fetchRoomIds(1623);

$roomUnitModel = new Models\RoomUnitModel($dbHelper);
$occupancyModel = new Models\OccupancyModel($dbHelper, $now);
$analyzer = new Business\Analyzer($now);
$propertyLogger = new Business\PropertyLogger($roomModel, $now);
$allocationWriter = new Business\AllocationWriter($now);

foreach($roomIds as $roomId){
    $dates = $occupancyModel->fetchDistinctDatesByRoomId($roomId, $now->format('Y-m-d'));
    
    if (empty($dates)){
        // $log->info("Room: $roomId has 0 occupancies.");
        continue;
    }
    
    $allocations = $occupancyModel->buildAllocations($roomId, $dates, true);
    $analyzer->analyzeAllocations($allocations, true, $propertyLogger, $allocationWriter);
}

$propertyLogger->write();
$dbHelper->close();
$now = new DateTime();
die("Done Analyzer {$now->format('Y-m-d H:i:s')}\n\n");
