<?php

namespace OTE\Business;

class AllocationWriter
{
    private $now;
    
    public function __construct($now)
    {
        $this->now = $now;
    }
    
    public function writeOverAllocation($allocation)
    {
        $fileName = dirname(__DIR__) . "/results/over-allocation-{$allocation->roomId}{$allocation->date}.json";
        file_put_contents($fileName, json_encode($allocation, JSON_PRETTY_PRINT));
    }
    
    public function writeNegativeAllocation($allocation)
    {
        $fileName = dirname(__DIR__) . "/results/negative-allocation-{$allocation->roomId}{$allocation->date}.json";
        file_put_contents($fileName, json_encode($allocation, JSON_PRETTY_PRINT));
    }
}