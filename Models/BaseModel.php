<?php

namespace OTE\Models;

use OTE\Utils;

class BaseModel
{
    protected $dbHelper;
    
    public function __construct(Utils\IDBHelper $dbHelper)
    {
        $this->dbHelper = $dbHelper;
    }
}