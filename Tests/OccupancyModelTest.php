<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'BaseTestCase.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'OccupancyFixture.php';

use OTE\Models;

class OccupancyModelTest extends BaseTestCase
{
    protected $occupancyModel;
    
    public function setUp()
    {
        $this->occupancyModel = new Models\OccupancyModel(parent::$dbHelper);
    }
    
    public function testFetchDistinctDatesByRoomId()
    {
        $roomId = 1;
        
        OccupancyFixture::purge(parent::$dbHelper);
        OccupancyFixture::generateMultiDates(parent::$dbHelper);
        
        $datesForRoom = $this->occupancyModel->fetchDistinctDatesByRoomId($roomId);
        // var_export($datesForRoom);
    }
    
    public function testFetchAllocated()
    {
        $roomId = 1;
        
        OccupancyFixture::purge(parent::$dbHelper);
        OccupancyFixture::generateOverAllocation(parent::$dbHelper);
        $date = '2016-04-25';
        $expected = 8;
        $allocated = $this->occupancyModel->fetchAllocated($roomId, $date);
        // var_export($allocated);
        $this->assertEquals($expected, $allocated);
        
        OccupancyFixture::generateAllocationsUnderZero(parent::$dbHelper);
        $date = '2016-04-27';
        $expected = -3;
        $allocated = $this->occupancyModel->fetchAllocated($roomId, $date);
        $this->assertEquals($expected, $allocated);
        
        OccupancyFixture::generateAllocated(parent::$dbHelper);
        $date = '2016-04-28';
        $expected = 4;
        $allocated = $this->occupancyModel->fetchAllocated($roomId, $date);
        $this->assertEquals($expected, $allocated);
    }
    
    public function testFetchAllocatedRoomUnits()
    {
        $roomId = 1;
        
        OccupancyFixture::purge(parent::$dbHelper);
        OccupancyFixture::generateMultiOccupiedRoomUnits(parent::$dbHelper);
        $date = '2016-04-29';
        $expected = array (
                    0 =>
                    array (
                        'id' => '2024',
                        'room_unit_id' => '1',
                        'quantity' => '1',
                    ),
                    1 =>
                    array (
                        'id' => '2025',
                        'room_unit_id' => '2',
                        'quantity' => '1',
                    ),
                    2 =>
                    array (
                        'id' => '2026',
                        'room_unit_id' => '5',
                        'quantity' => '1',
                    ),
                    3 =>
                    array (
                        'id' => '2027',
                        'room_unit_id' => '1',
                        'quantity' => '-1',
                    ),
                    4 =>
                    array (
                        'id' => '2028',
                        'room_unit_id' => '2',
                        'quantity' => '-1',
                    ),
                    5 =>
                    array (
                        'id' => '2029',
                        'room_unit_id' => '5',
                        'quantity' => '-1',
                    ),
                    6 =>
                    array (
                        'id' => '2030',
                        'room_unit_id' => '1',
                        'quantity' => '1',
                    ),
                    7 =>
                    array (
                        'id' => '2031',
                        'room_unit_id' => '1',
                        'quantity' => '1',
                    ),
                    8 =>
                    array (
                        'id' => '2032',
                        'room_unit_id' => '2',
                        'quantity' => '1',
                    ),
                ); 


        $actual = $this->occupancyModel->fetchAllocatedRoomUnits($roomId, $date);
        // var_export($actual);
        // $this->assertEquals($expected, $actual);
    }
    
    public function testFetchAllocatedRoomUnitSummaries()
    {
        $roomId = 1;
        
        OccupancyFixture::purge(parent::$dbHelper);
        OccupancyFixture::generateMultiOccupiedRoomUnits(parent::$dbHelper);
        $date = '2016-04-29';
        $expected = array (
                        0 =>
                        array (
                            'room_unit_id' => '1',
                            'quantity' => '2',
                        ),
                        1 =>
                        array (
                            'room_unit_id' => '2',
                            'quantity' => '1',
                        ),
                        2 =>
                        array (
                            'room_unit_id' => '5',
                            'quantity' => '0',
                        ),
                    );

        $actual = $this->occupancyModel->fetchAllocatedRoomUnitSummaries($roomId, $date);
        // var_export($actual);
        // $this->assertEquals($expected, $actual);
    }
    
    public function testBuildAllocations()
    {
        $roomId = 1;
        
        OccupancyFixture::purge(parent::$dbHelper);
        OccupancyFixture::generateMultiOccupiedRoomUnits(parent::$dbHelper);
        
        $datesForRoom = $this->occupancyModel->fetchDistinctDatesByRoomId($roomId);
        $allocations = $this->occupancyModel->buildAllocations($roomId, $datesForRoom);
        
        // var_export($roomUnitModel->roomUnitIds);
        
        // $this->occupancyModel->findMultiAllocatedRoomUnits($roomUnitModel->roomUnitIds, $allocations);
        
        var_export($allocations);
    }
    
    
    
}