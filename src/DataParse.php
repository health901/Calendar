<?php


namespace VRobin\Calendar;


class DataParse
{
    protected $originData;

    protected $parseData;
    protected $year;

    public function __construct($originData, $year)
    {
        $this->originData = $originData;
        $this->year = $year;
        $this->parse();
    }

    protected function parse()
    {
        $hex_array = str_split($this->originData);
        array_splice($hex_array, 4, 0, '0');
        $data = implode("", $hex_array);
        $bin = pack('H*', $data);
        $tempData = unpack('nBin/CLeapMonth/CNewYear', $bin);
        $tempData['LeapDays'] = $tempData['Bin'] & 1 ? 30 : 29;
        $monthInfo = $tempData['Bin'] >> 4;
        for ($i = 1; $i <= 12; $i++) {
            $tempData['MonthDays'][] = $monthInfo & pow(2, 12 - $i) ? 30 : 29;
        }
        $this->parseData = $tempData;
    }

    /**
     * 返回农历闰月
     * @return mixed
     */
    public function getLeapMonth()
    {
        return $this->parseData['LeapMonth'];
    }

    public function getLeapDays(): int
    {
        return $this->getLeapMonth() ? $this->parseData['LeapDays'] : 0;
    }

    public function getNewYear(): string
    {
        $newYear = $this->year . str_pad($this->parseData['NewYear'], 4, '0', STR_PAD_LEFT);
        return (new \DateTime($newYear))->format('Y-m-d');
    }

    public function getMonthDays()
    {
        return $this->parseData['MonthDays'];
    }

}