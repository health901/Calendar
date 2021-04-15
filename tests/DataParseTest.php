<?php


use VRobin\Calendar\DataParse;
use PHPUnit\Framework\TestCase;

class DataParseTest extends TestCase
{
    public function testAll()
    {
        $parser = new DataParse('4BD0883', 1900);
        $this->assertEquals('1900-01-31', $parser->getNewYear());
        $this->assertEquals('8', $parser->getLeapMonth());

    }
}
