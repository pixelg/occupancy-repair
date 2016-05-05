<?php

namespace OTE\Utils;

interface IDBHelper
{
    public function connect(); 
    public function runQuery($sql);
    public function populateArray(&$queryResult, $field = null);
    public function close();
}