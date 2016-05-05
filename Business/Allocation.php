<?php 

namespace OTE\Business;

class Allocation
{
    public $roomId;
    public $date;
    public $allocated;
    public $totalUnits;
    public $difference = 0;
    public $isValid;
    public $allocatedRoomUnits = [];
    public $allocatedRoomUnitSummaries = [];
    
    public function __construct($roomId, $date, $allocated, $totalUnits) 
    {
        $this->roomId = $roomId;
        $this->date = $date;
        $this->allocated = $allocated;
        $this->totalUnits = $totalUnits;
        $this->setDifference($allocated, $totalUnits);
    }
    
    public function setDifference($allocated, $totalUnits)
    {
        if ($allocated < 0){
            $this->difference = $allocated;
            return $this->isValid = false;
        } 
        
        if ($allocated > 0 && $allocated > $totalUnits){
           $this->difference = $allocated - $totalUnits;
           return $this->isValid = false;
        }
        
        return $this->isValid = true;
    }
    
    public function isOverAllocated()
    {
        if ($this->difference > 0){
            return true;
        }
        
        return false;
    }
    
    public function isUnderAllocated()
    {
        if ($this->difference < 0){
            return true;
        }
        
        return false;
    }
    
    public function hasRoomUnitsUnderZero()
    {
        foreach($this->allocatedRoomUnitSummaries as $allocatedRoomUnitSummary){
            if ($allocatedRoomUnitSummary->quantity > 1){
                return true;
            }
        }
        
        return false;
    }
    
    public function hasRoomUnitsOverZero()
    {
        foreach($this->allocatedRoomUnitSummaries as $allocatedRoomUnitSummary){
            if ($allocatedRoomUnitSummary->quantity < 0){
                return true;
            }
        }
        
        return false;
    }
    
}