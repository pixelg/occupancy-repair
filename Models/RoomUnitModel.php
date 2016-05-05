<?php

namespace OTE\Models;

class RoomUnitModel extends BaseModel
{
    public $roomUnitIds;
    public $roomUnitCount;
    
    public function fetchRoomUnitIds($roomId)
    {
        $sql = sprintf("SELECT id FROM room_units WHERE room_id = %s", $roomId);
        $roomUnitsResult = $this->dbHelper->runQuery($sql); 
        $this->roomUnitIds = $this->dbHelper->populateArray($roomUnitsResult, 'id');
        $this->roomUnitCount = count($this->roomUnitIds);
        
        return $this->roomUnitIds;
    }
}