<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'BaseTestCase.php';

use OTE\Models;

class RoomModelTest extends BaseTestCase
{
    public function testFetchRoomIds()
    {
        $this->dbHelper->connect();
        $roomModel = new Models\RoomModel($this->dbHelper);
        $roomIds = $roomModel->fetchRoomIds();
        // var_dump($roomIds);
        $this->dbHelper->close();
    }
}