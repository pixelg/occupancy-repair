<?php

namespace OTE\Models;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use OTE\Business;

class OccupancyModel extends BaseModel
{   
    public static $lowDate = null;
    public static $highDate = null;
    
    public $now;
    
    public function __construct($dbHelper, \DateTime $now)
    {
        parent::__construct($dbHelper);
        $this->now = $now;
    }
    
    public function fetchDistinctDatesByRoomId($roomId, $minDate = '2016-01-01')
    {
        $sql = sprintf("
            SELECT DISTINCT(`date`) 
            FROM occupancies 
            WHERE room_id = %s 
            AND reservation_id IS NULL
            AND `date` >= '$minDate'", 
            $roomId);
        
        $result = $this->dbHelper->runQuery($sql);
        $datesForRoom = $this->dbHelper->populateArray($result);
        
        return $datesForRoom;
    }
    
    public function findUpperLowerDates($roomId, $dates, $totalUnitCount)
    {
        foreach($dates as $date){
            $date = $date['date'];
            
            $sql = sprintf("
                SELECT SUM(quantity) as allocated 
                FROM occupancies 
                WHERE room_id = %s
                AND `date` = '%s'
                AND status in (0, 1)
            ", $roomId, $date);
            
            $result = $this->dbHelper->runQuery($sql);
            $allocated = $this->dbHelper->populateArray($result, 'allocated');
            $allocated = (int)$allocated[0];
            
            // If allocated is negative. Only has cancellations.
            if ($allocated < 0){
                $negativeLogger = new Logger('negative-allocation');
                $negativeLogger->pushHandler(new StreamHandler(dirname(__DIR__) . "/tmp/bug-timeframe-{$this->now->format('YmdHis')}.log", Logger::INFO));
                // $negativeLogger->addWarning("Room: $roomId | Date: $date | TotalUnits: $totalUnitCount | Allocated: $allocated");
                
                $sql = sprintf("
                    SELECT id, room_id, `date`, created, created_by
                    FROM occupancies 
                    WHERE room_id = %s
                    AND `date` = '%s'
                    AND status = 0
                    AND reservation_id IS NULL
                ", $roomId, $date);
            
                $result = $this->dbHelper->runQuery($sql);
                $occupancies = $this->dbHelper->populateArray($result);
                foreach($occupancies as $occupancy){
                    self::$lowDate = is_null(self::$lowDate) ? $occupancy['created'] : self::$lowDate;
                    self::$highDate = is_null(self::$highDate) ? $occupancy['created'] : self::$highDate;
                    
                    $message = "id: {$occupancy['id']} | room: {$occupancy['room_id']} | created: {$occupancy['created']} | created_by: {$occupancy['created_by']}";
                    $negativeLogger->addWarning($message);
                    
                    if ($occupancy['created'] < self::$lowDate){
                        self::$lowDate = $occupancy['created'];
                    }
                    
                    if ($occupancy['created'] > self::$highDate){
                        self::$highDate = $occupancy['created'];
                    }
                }
                
            }
        }
    }
    
    public function buildAllocations($roomId, $dates, $logMessages = false)
    {
        $allocationsLogger = new Logger('allocations');
        $allocationsLogger->pushHandler(new StreamHandler(dirname(__DIR__) . "/tmp/allocations-{$this->now->format('YmdHis')}.log", Logger::INFO));
        
        $allocations = [];
        
        $roomUnitModel = new RoomUnitModel($this->dbHelper);
        $roomUnitModel->fetchRoomUnitIds($roomId);
        
        foreach($dates as $date){
            $allocated = $this->fetchAllocated($roomId, $date['date']);
            
            if (empty($allocated)){
                continue;
            }
            
            $allocation = new Business\Allocation($roomId, $date['date'], $allocated, $roomUnitModel->roomUnitCount);
            // Add individual room unit allocations
            $allocation->allocatedRoomUnits  = $this->fetchAllocatedRoomUnits($roomId, $date['date']);
            //Get the summary of how many units are occupied for a given room unit id
            $allocation->allocatedRoomUnitSummaries = $this->fetchAllocatedRoomUnitSummaries($roomId, $date['date']);
            // Add the room units that have never been assigned
            foreach($roomUnitModel->roomUnitIds as $roomUnitId){
                $allocatedRoomUnitIds = array_filter($allocation->allocatedRoomUnitSummaries, 
                    function($allocatedRoomUnitSummary) use ($roomUnitId){
                        return $allocatedRoomUnitSummary->roomUnitId == $roomUnitId;
                    });
                    
                if (empty($allocatedRoomUnitIds)){
                    $allocatedRoomUnitSummary = new Business\AllocatedRoomUnitSummary();
                    $allocatedRoomUnitSummary->roomUnitId = $roomUnitId;
                    $allocatedRoomUnitSummary->quantity = 0;
                    $allocation->allocatedRoomUnitSummaries[] = $allocatedRoomUnitSummary;
                }
            }
            
            $allocations[] = $allocation;
            
            if ($logMessages){
                $message = sprintf("Room: %s | Date: %s | TotalUnits: %s | Allocated: %s",
                $allocation->roomId, $allocation->date, $allocation->totalUnits, $allocation->allocated);
                $allocationsLogger->addInfo($message);
            }
        }
        
        return $allocations;
    }
    
    public function fetchAllocated($roomId, $date)
    {
        $sql = sprintf("
            SELECT SUM(quantity) as allocated 
            FROM occupancies 
            WHERE room_id = %s
            AND `date` = '%s'
            AND status IN (0, 1)
        ", $roomId, $date);
            
        $result = $this->dbHelper->runQuery($sql);
        $allocated = $this->dbHelper->populateArray($result, 'allocated');
        $allocated = (int)$allocated[0];
        
        return $allocated;
    }
    
    public function fetchAllocatedRoomUnits($roomId, $date)
    {
        $occupiedRoomUnits = [];
        
        $sql = sprintf("
                SELECT id, room_unit_id as roomUnitId, quantity
                FROM occupancies
                WHERE room_id = %s
                AND `date` = '%s'
                AND status IN (0, 1)
                ",
                $roomId, $date);
      
        $result = $this->dbHelper->runQuery($sql);        
        $occupiedRoomUnits = $this->dbHelper->populateObject($result, 'OTE\Business\AllocatedRoomUnit');
        return $occupiedRoomUnits;
    }
    
    public function fetchAllocatedRoomUnitSummaries($roomId, $date)
    {
        $sql = sprintf("
                SELECT room_unit_id as roomUnitId, SUM(quantity) as quantity
                FROM occupancies
                WHERE room_id = %s
                AND `date` = '%s'
                AND status IN (0, 1)
                GROUP BY room_unit_id",
                $roomId, $date);
      
        $result = $this->dbHelper->runQuery($sql);        
        $occupiedRoomUnitSummaries = $this->dbHelper->populateObject($result, 'OTE\Business\AllocatedRoomUnitSummary');
        return $occupiedRoomUnitSummaries;
    }
    
    public function fixNegativeAllocations($allocationArray)
    {
        $now = new \DateTime();
        
        $streamHandler = new StreamHandler(dirname(__DIR__) . "/tmp/fixed-negative-allocations-{$this->now->format('YmdHis')}.log", Logger::INFO);
        $negativeLogger = new Logger('fixed-negative-allocation');
        $negativeLogger->pushHandler($streamHandler);
        
        foreach($allocationArray['allocatedRoomUnits'] as $allocatedRoomUnit){
            if (!$allocatedRoomUnit['isValid']){
                $sql = sprintf("DELETE FROM occupancies WHERE id = %s AND reservation_id IS NULL", $allocatedRoomUnit['id']);
                var_export($sql);
                // $result = $this->dbHelper->runQuery($sql);
                $message = sprintf("Room: %s | Date: %s",
                $allocationArray['roomId'], $allocationArray['date']);
                $negativeLogger->addInfo($message);
            }
        }
    }
    
    // public function findMultiAllocatedRoomUnits($allocations)
    // {
    //     foreach($allocations as $allocation){
    //         foreach($allocation->allocatedRoomUnitSummaries as $allocatedRoomUnitSummary){
    //             if ($allocatedRoomUnitSummary->quantity > 1){
    //                 $allocation->getFirstAvailable()
    //             }
                
    //             if ($quantity > 1){
    //                 $allocatedRoomUnit->isValid = false;
    //             }
    //         }
    //     }
    // }
    
    public function findOccupancyIdsForChange($allocations)
    {
        $idsToChange = [];
        $removeIndex = 0;
        $switchIndex = 0;
        
        foreach($allocations as $allocation){
            if ($allocation['diff'] > 0){
                $sql = sprintf("
                    SELECT id 
                    FROM occupancies 
                    WHERE room_id = %s 
                    AND `date` = '%s'
                    AND `status` = 1
                    AND reservation_id IS NULL
                    ORDER BY id DESC",
                    $allocation['roomId'],
                    $allocation['date']);
                
                $result = $this->dbHelper->runQuery($sql);
                $ids = $this->dbHelper->populateArray($result);
                
                for($i = 0; $i < $allocation['diff']; $i++){
                    $idsToChange['Removal'][$removeIndex++] = $ids[$i];
                }
            }
            
            if ($allocation['diff'] < 0){
                $sql = sprintf("
                    SELECT id 
                    FROM occupancies 
                    WHERE room_id = %s 
                    AND `date` = '%s'
                    AND `status` = 0
                    AND reservation_id IS NULL",
                    $allocation['roomId'],
                    $allocation['date']);
                
                $result = $this->dbHelper->runQuery($sql);
                $ids = $this->dbHelper->populateArray($result);
                
                for($i = 0; $i < abs($allocation['diff']); $i++){
                    $idsToChange['Switch'][$switchIndex++] = $ids[$i];
                }
            }
        }
        
        return $idsToChange;
    }
    
    public function deleteRows($idsToRemove)
    {
        array_walk($idsToRemove, function(&$v, $k){ $v = $v['id']; });
        $idsToRemove = implode(',', $idsToRemove);
        
        $sql = sprintf("DELETE FROM occupancies WHERE id IN (%s)", $idsToRemove);
        $result = $this->dbHelper->runQuery($sql);
        
        return $result;
    }
}