<?php

namespace OTE\Business;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class PropertyLogger
{
    private $properties = [];
    private $propertiesLogger;
    private $roomModel;
    
    public function __construct($roomModel, $now)
    {
        $this->roomModel = $roomModel;
        $this->propertiesLogger = new Logger('properties');
        $this->propertiesLogger->pushHandler(new StreamHandler(dirname(__DIR__) . "/tmp/properties-{$now->format('YmdHis')}.log", Logger::INFO));
    }
    
    public function write()
    {
        foreach($this->properties as $property){
            $this->propertiesLogger->info(sprintf("%s | %s | %s | %s", $property['id'], $property['name'], $property['city'], $property['cc_iso']));
        }
    }
    
    public function addProperty($roomId)
    {
        $property = $this->roomModel->fetchProperty($roomId);
        $property = $property[0];
        
        if (empty($this->properties[$property['id']])){
            $this->properties[$property['id']] = $property;
        }
    }
}