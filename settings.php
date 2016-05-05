<?php

date_default_timezone_set('UTC');

class Settings
{
    public $db = [
        'default' => [
            'host' => 'localhost',
            'user' => 'root',
            'password' => 'root',
            'db_name' => 'ote_prod'
        ],
        'test' => [
            'host' => 'localhost',
            'user' => 'root',
            'password' => 'root',
            'db_name' => 'occupancy-test'
        ]
    ];
}