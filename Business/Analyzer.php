<?php

namespace OTE\Business;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Analyzer
{
    public $now;
    
    public function __construct(\DateTime $now)
    {
        $this->now = $now;
    }
    
    public function analyzeAllocations($allocations, $logMessages = false, $propertyLogger = null, $allocationWriter = null)
    {
        $streamHandlerNegativeAllocations = new StreamHandler(dirname(__DIR__) . "/tmp/negative-allocations-{$this->now->format('YmdHis')}.log", Logger::INFO);
        $negativeLogger = new Logger('negative-allocation');
        $negativeLogger->pushHandler($streamHandlerNegativeAllocations);
        
        $streamHandlerPositiveAllocations = new StreamHandler(dirname(__DIR__) . "/tmp/positive-allocations-{$this->now->format('YmdHis')}.log", Logger::INFO);
        $positiveLogger = new Logger('positive-allocation');
        $positiveLogger->pushHandler($streamHandlerPositiveAllocations);
        
        $streamHandlerMultiRoomUnits = new StreamHandler(dirname(__DIR__) . "/tmp/multi-allocations-{$this->now->format('YmdHis')}.log", Logger::INFO);
        $multiAllocationLogger = new Logger('multi-allocation');
        $multiAllocationLogger->pushHandler($streamHandlerMultiRoomUnits);
        
        $streamHandlerUnder = new StreamHandler(dirname(__DIR__) . "/tmp/room-units-under-allocations-{$this->now->format('YmdHis')}.log", Logger::INFO);
        $unitsUnderLogger = new Logger('units-under-allocation');
        $unitsUnderLogger->pushHandler($streamHandlerUnder);
        
        $streamHandlerOver = new StreamHandler(dirname(__DIR__) . "/tmp/room-units-over-allocations-{$this->now->format('YmdHis')}.log", Logger::INFO);
        $unitsOverLogger = new Logger('units-over-allocation');
        $unitsOverLogger->pushHandler($streamHandlerOver);
        
        foreach($allocations as $allocation){
            if ($allocation->isNegativeAllocated()){
                $allocation->markNegativeAllocations();
                $message = sprintf($message = sprintf("Room: %s | Date: %s | TotalUnits: %s | Allocated: %s",
                $allocation->roomId, $allocation->date, $allocation->totalUnits, $allocation->allocated));
                $logMessages && $negativeLogger->addWarning($message);
                $propertyLogger && $propertyLogger->addProperty($allocation->roomId);
                $allocationWriter && $allocationWriter->writeNegativeAllocation($allocation);
            }
            
            if ($allocation->isOverAllocated()){
                $message = sprintf($message = sprintf("Room: %s | Date: %s | TotalUnits: %s | Allocated: %s",
                $allocation->roomId, $allocation->date, $allocation->totalUnits, $allocation->allocated));
                $logMessages && $positiveLogger->addWarning($message);
                $propertyLogger && $propertyLogger->addProperty($allocation->roomId);
                $allocationWriter && $allocationWriter->writeOverAllocation($allocation);
            }
            
            if ($allocation->hasRoomUnitsUnderZero() && $allocation->hasRoomUnitsOverZero()){
                $message = sprintf($message = sprintf("Room: %s | Date: %s | TotalUnits: %s | Allocated: %s",
                $allocation->roomId, $allocation->date, $allocation->totalUnits, $allocation->allocated));
                $logMessages && $multiAllocationLogger->addWarning($message);
                continue;
            }
            
            if ($allocation->hasRoomUnitsUnderZero()){
                $message = sprintf($message = sprintf("Room: %s | Date: %s | TotalUnits: %s | Allocated: %s",
                $allocation->roomId, $allocation->date, $allocation->totalUnits, $allocation->allocated));
                $logMessages && $unitsUnderLogger->addWarning($message);
                continue;
            }
            
            if ($allocation->hasRoomUnitsOverZero()){
                $message = sprintf($message = sprintf("Room: %s | Date: %s | TotalUnits: %s | Allocated: %s",
                $allocation->roomId, $allocation->date, $allocation->totalUnits, $allocation->allocated));
                $logMessages && $unitsOverLogger->addWarning($message);
                continue;
            }
        }
    }
}