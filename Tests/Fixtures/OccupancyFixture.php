<?php

class OccupancyFixture
{
    protected static $id = 2000;
    
    public static function purge($dbHelper)
    {
        $dbHelper->runQuery("DELETE FROM occupancies WHERE created_by = 1000");
    }
    
    public static function generateMultiDates($dbHelper)
    {
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                (1, 1, NULL, '2016-04-25', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                (1, 1, NULL, '2016-04-25', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        "); 
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                (1, 1, NULL, '2016-04-27', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        "); 
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                (1, 1, NULL, '2016-04-26', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                (1, 1, NULL, '2016-04-26', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        "); 
    }
   
    /**
        This function should create a scenario of allocations that are more than the total units that can be allocated.
        Date used: 2016-04-25
        
        4 Allocated
        2 Over Allocation
        2 De-allocated
        3 Over Allocation
        1 Over with reservation 
        
        The total result should be 8 allocated (4 over allocated as this room only has 4 units).
    **/
    public static function generateOverAllocation($dbHelper, $date = '2016-04-25')
    {
        $id = self::$id++;
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 1, NULL, '$date', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 2, NULL, '$date', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 5, NULL, '$date', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 155, NULL, '$date', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate Over
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 1, NULL, '$date', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate Over
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 2, NULL, '$date', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // De-allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 1, NULL, '$date', -1, 0, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // De-allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 2, NULL, '$date', -1, 0, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate Over
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 1, NULL, '$date', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate Over
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 2, NULL, '$date', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate Over
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 5, NULL, '$date', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate Over with reservation id
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 5, 3, '$date', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
    }
    
    /**
        This function should create a scenario of allocations that are less than zero.
        Date used: 2016-04-27
        
        3 De-Allocated
   
        The result should be -3 allocated.
    **/
    public static function generateAllocationsUnderZero($dbHelper)
    {
        $id = self::$id++;
        
        // De-Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 1, NULL, '2016-04-27', -1, 0, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // De-Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 2, NULL, '2016-04-27', -1, 0, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // De-Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 5, NULL, '2016-04-27', -1, 0, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
    }
    
    /**
        This function should create a scenario of allocations that have the same room unit id assigned multiple times.
        Date used: 2016-04-29
    **/
    public static function generateMultiOccupiedRoomUnits($dbHelper)
    {
        $id = self::$id++;
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 1, NULL, '2016-04-29', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 2, NULL, '2016-04-29', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 5, NULL, '2016-04-29', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // De-Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 1, NULL, '2016-04-29', -1, 0, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // De-Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 2, NULL, '2016-04-29', -1, 0, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // De-Allocate
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 5, NULL, '2016-04-29', -1, 0, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate Room Unit Id 1
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 1, NULL, '2016-04-29', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate Room Unit Id 1 Again
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 1, NULL, '2016-04-29', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $id = self::$id++;
        
        // Allocate Room Unit Id 2
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (id, room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                ($id, 1, 2, NULL, '2016-04-29', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
    }
    
    /**
        This function should create normal allocations.
        Date used: 2016-04-28
    **/
        
    public static function generateAllocated($dbHelper)
    {
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                (1, 1, NULL, '2016-04-28', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                (1, 2, NULL, '2016-04-28', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                (1, 5, NULL, '2016-04-28', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
        
        $dbHelper->runQuery("
            INSERT INTO occupancies 
                (room_id, room_unit_id, reservation_id, date, quantity, status, created, created_by, modified, modified_by)
            VALUES
                (1, 155, NULL, '2016-04-28', 1, 1, '2016-04-25 00:00:00', 1000, '2016-04-25 00:00:00', 1000)
        ");
    }
}