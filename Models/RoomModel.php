<?php

namespace OTE\Models;

class RoomModel extends BaseModel
{
    public $roomIds;
    
    public function fetchRoomIds($propertyId = null)
    {
        $sql = "SELECT id FROM rooms";
        
        if ($propertyId){
            $sql .= ' WHERE property_id = ' . $propertyId;
        }
        
        $roomResult = $this->dbHelper->runQuery($sql);
        $this->roomIds = $this->dbHelper->populateArray($roomResult, 'id');
        // var_dump($roomIds);
        
        return $this->roomIds;
    }
    
    public function fetchRoomObj()
    {
        $sql = "SELECT id FROM rooms";
        $roomResult = $this->dbHelper->runQuery($sql);
        $results = [];
        while($obj = $roomResult->fetch_object('OTE\Models\RoomModel', [$this->dbHelper])){
            $results[] = $obj;
        }
        
        $roomResult->free();
        return $results;
    }
    
    public function fetchProperty($roomId)
    {
        $sql = sprintf("
                    SELECT p.id, p.name, p.city, p.cc_iso 
                    FROM rooms r JOIN properties p ON r.property_id = p.id 
                    WHERE r.id = %s
                    ", $roomId);
        $result = $this->dbHelper->runQuery($sql);
        return $this->dbHelper->populateArray($result);
    }
    
}