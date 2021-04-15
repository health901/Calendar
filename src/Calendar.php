<?php

namespace VRobin\Calendar;

use VRobin\Calendar\Exceptions\InvalidateDateException;
use function Sodium\add;

class Calendar
{
    /**
     * 1900-2100 各年的 农历数据速查表.
     * @var string[]
     */
    private $dates = [
        'AB500D2', '4BD0883',
        '4AE00DB', 'A5700D0', '54D0581', 'D2600D8', 'D9500CC', '655147D', '56A00D5', '9AD00CA', '55D027A', '4AE00D2',
        'A5B0682', 'A4D00DA', 'D2500CE', 'D25157E', 'B5500D6', '56A00CC', 'ADA027B', '95B00D3', '49717C9', '49B00DC',
        'A4B00D0', 'B4B0580', '6A500D8', '6D400CD', 'AB5147C', '2B600D5', '95700CA', '52F027B', '49700D2', '6560682',
        'D4A00D9', 'EA500CE', '6A9157E', '5AD00D6', '2B600CC', '86E137C', '92E00D3', 'C8D1783', 'C9500DB', 'D4A00D0',
        'D8A167F', 'B5500D7', '56A00CD', 'A5B147D', '25D00D5', '92D00CA', 'D2B027A', 'A9500D2', 'B550781', '6CA00D9',
        'B5500CE', '535157F', '4DA00D6', 'A5B00CB', '457037C', '52B00D4', 'A9A0883', 'E9500DA', '6AA00D0', 'AEA0680',
        'AB500D7', '4B600CD', 'AAE047D', 'A5700D5', '52600CA', 'F260379', 'D9500D1', '5B50782', '56A00D9', '96D00CE',
        '4DD057F', '4AD00D7', 'A4D00CB', 'D4D047B', 'D2500D3', 'D550883', 'B5400DA', 'B6A00CF', '95A1680', '95B00D8',
        '49B00CD', 'A97047D', 'A4B00D5', 'B270ACA', '6A500DC', '6D400D1', 'AF40681', 'AB600D9', '93700CE', '4AF057F',
        '49700D7', '64B00CC', '74A037B', 'EA500D2', '6B50883', '5AC00DB', 'AB600CF', '96D0580', '92E00D8', 'C9600CD',
        'D95047C', 'D4A00D4', 'DA500C9', '755027A', '56A00D1', 'ABB0781', '25D00DA', '92D00CF', 'CAB057E', 'A9500D6',
        'B4A00CB', 'BAA047B', 'AD500D2', '55D0983', '4BA00DB', 'A5B00D0', '5171680', '52B00D8', 'A9300CD', '795047D',
        '6AA00D4', 'AD500C9', '5B5027A', '4B600D2', '96E0681', 'A4E00D9', 'D2600CE', 'EA6057E', 'D5300D5', '5AA00CB',
        '76A037B', '96D00D3', '4AB0B83', '4AD00DB', 'A4D00D0', 'D0B1680', 'D2500D7', 'D5200CC', 'DD4057C', 'B5A00D4',
        '56D00C9', '55B027A', '49B00D2', 'A570782', 'A4B00D9', 'AA500CE', 'B25157E', '6D200D6', 'ADA00CA', '4B6137B',
        '93700D3', '49F08C9', '49700DB', '64B00D0', '68A1680', 'EA500D7', '6AA00CC', 'A6C147C', 'AAE00D4', '92E00CA',
        'D2E0379', 'C9600D1', 'D550781', 'D4A00D9', 'DA400CD', '5D5057E', '56A00D6', 'A6C00CB', '55D047B', '52D00D3',
        'A9B0883', 'A9500DB', 'B4A00CF', 'B6A067F', 'AD500D7', '55A00CD', 'ABA047C', 'A5A00D4', '52B00CA', 'B27037A',
        '69300D1', '7330781', '6AA00D9', 'AD500CE', '4B5157E', '4B600D6', 'A5700CB', '54E047C', 'D1600D2', 'E960882',
        'D5200DA', 'DAA00CF', '6AA167F', '56D00D7', '4AE00CD', 'A9D047D', 'A2D00D4', 'D1500C9', 'F250279', 'D5200D1'
    ];
    /**
     * 1900-2100 各年的 24 节气日期速查表.
     *
     * @var array
     */
    protected $solarTerms = [
        '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bcf97c3598082c95f8c965cc920f',
        '97bd0b06bdb0722c965ce1cfcc920f', 'b027097bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
        '97bcf97c359801ec95f8c965cc920f', '97bd0b06bdb0722c965ce1cfcc920f', 'b027097bd097c36b0b6fc9274c91aa',
        '97b6b97bd19801ec9210c965cc920e', '97bcf97c359801ec95f8c965cc920f', '97bd0b06bdb0722c965ce1cfcc920f',
        'b027097bd097c36b0b6fc9274c91aa', '9778397bd19801ec9210c965cc920e', '97b6b97bd19801ec95f8c965cc920f',
        '97bd09801d98082c95f8e1cfcc920f', '97bd097bd097c36b0b6fc9210c8dc2', '9778397bd197c36c9210c9274c91aa',
        '97b6b97bd19801ec95f8c965cc920e', '97bd09801d98082c95f8e1cfcc920f', '97bd097bd097c36b0b6fc9210c8dc2',
        '9778397bd097c36c9210c9274c91aa', '97b6b97bd19801ec95f8c965cc920e', '97bcf97c3598082c95f8e1cfcc920f',
        '97bd097bd097c36b0b6fc9210c8dc2', '9778397bd097c36c9210c9274c91aa', '97b6b97bd19801ec9210c965cc920e',
        '97bcf97c3598082c95f8c965cc920f', '97bd097bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b97bd19801ec9210c965cc920e', '97bcf97c3598082c95f8c965cc920f', '97bd097bd097c35b0b6fc920fb0722',
        '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bcf97c359801ec95f8c965cc920f',
        '97bd097bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
        '97bcf97c359801ec95f8c965cc920f', '97bd097bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b97bd19801ec9210c965cc920e', '97bcf97c359801ec95f8c965cc920f', '97bd097bd07f595b0b6fc920fb0722',
        '9778397bd097c36b0b6fc9210c8dc2', '9778397bd19801ec9210c9274c920e', '97b6b97bd19801ec95f8c965cc920f',
        '97bd07f5307f595b0b0bc920fb0722', '7f0e397bd097c36b0b6fc9210c8dc2', '9778397bd097c36c9210c9274c920e',
        '97b6b97bd19801ec95f8c965cc920f', '97bd07f5307f595b0b0bc920fb0722', '7f0e397bd097c36b0b6fc9210c8dc2',
        '9778397bd097c36c9210c9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bd07f1487f595b0b0bc920fb0722',
        '7f0e397bd097c36b0b6fc9210c8dc2', '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
        '97bcf7f1487f595b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b97bd19801ec9210c965cc920e', '97bcf7f1487f595b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722',
        '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bcf7f1487f531b0b0bb0b6fb0722',
        '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
        '97bcf7f1487f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b97bd19801ec9210c9274c920e', '97bcf7f0e47f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722',
        '9778397bd097c36b0b6fc9210c91aa', '97b6b97bd197c36c9210c9274c920e', '97bcf7f0e47f531b0b0bb0b6fb0722',
        '7f0e397bd07f595b0b0bc920fb0722', '9778397bd097c36b0b6fc9210c8dc2', '9778397bd097c36c9210c9274c920e',
        '97b6b7f0e47f531b0723b0b6fb0722', '7f0e37f5307f595b0b0bc920fb0722', '7f0e397bd097c36b0b6fc9210c8dc2',
        '9778397bd097c36b0b70c9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721', '7f0e37f1487f595b0b0bb0b6fb0722',
        '7f0e397bd097c35b0b6fc9210c8dc2', '9778397bd097c36b0b6fc9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721',
        '7f0e27f1487f595b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722',
        '9778397bd097c36b0b6fc9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
        '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721',
        '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b7f0e47f531b0723b0787b0721', '7f0e27f0e47f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722',
        '9778397bd097c36b0b6fc9210c91aa', '97b6b7f0e47f149b0723b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722',
        '7f0e397bd07f595b0b0bc920fb0722', '9778397bd097c36b0b6fc9210c8dc2', '977837f0e37f149b0723b0787b0721',
        '7f07e7f0e47f531b0723b0b6fb0722', '7f0e37f5307f595b0b0bc920fb0722', '7f0e397bd097c35b0b6fc9210c8dc2',
        '977837f0e37f14998082b0787b0721', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e37f1487f595b0b0bb0b6fb0722',
        '7f0e397bd097c35b0b6fc9210c8dc2', '977837f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
        '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722', '977837f0e37f14998082b0787b06bd',
        '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722',
        '977837f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
        '7f0e397bd07f595b0b0bc920fb0722', '977837f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
        '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722', '977837f0e37f14998082b0787b06bd',
        '7f07e7f0e47f149b0723b0787b0721', '7f0e27f0e47f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722',
        '977837f0e37f14998082b0723b06bd', '7f07e7f0e37f149b0723b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722',
        '7f0e397bd07f595b0b0bc920fb0722', '977837f0e37f14898082b0723b02d5', '7ec967f0e37f14998082b0787b0721',
        '7f07e7f0e47f531b0723b0b6fb0722', '7f0e37f1487f595b0b0bb0b6fb0722', '7f0e37f0e37f14898082b0723b02d5',
        '7ec967f0e37f14998082b0787b0721', '7f07e7f0e47f531b0723b0b6fb0722', '7f0e37f1487f531b0b0bb0b6fb0722',
        '7f0e37f0e37f14898082b0723b02d5', '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
        '7f0e37f1487f531b0b0bb0b6fb0722', '7f0e37f0e37f14898082b072297c35', '7ec967f0e37f14998082b0787b06bd',
        '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e37f0e37f14898082b072297c35',
        '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
        '7f0e37f0e366aa89801eb072297c35', '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f149b0723b0787b0721',
        '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e37f0e366aa89801eb072297c35', '7ec967f0e37f14998082b0723b06bd',
        '7f07e7f0e47f149b0723b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722', '7f0e37f0e366aa89801eb072297c35',
        '7ec967f0e37f14998082b0723b06bd', '7f07e7f0e37f14998083b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722',
        '7f0e37f0e366aa89801eb072297c35', '7ec967f0e37f14898082b0723b02d5', '7f07e7f0e37f14998082b0787b0721',
        '7f07e7f0e47f531b0723b0b6fb0722', '7f0e36665b66aa89801e9808297c35', '665f67f0e37f14898082b0723b02d5',
        '7ec967f0e37f14998082b0787b0721', '7f07e7f0e47f531b0723b0b6fb0722', '7f0e36665b66a449801e9808297c35',
        '665f67f0e37f14898082b0723b02d5', '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
        '7f0e36665b66a449801e9808297c35', '665f67f0e37f14898082b072297c35', '7ec967f0e37f14998082b0787b06bd',
        '7f07e7f0e47f531b0723b0b6fb0721', '7f0e26665b66a449801e9808297c35', '665f67f0e37f1489801eb072297c35',
        '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
    ];
    /**
     * 天干地支之天干速查表.
     *
     * @var array
     */
    protected $gan = ['甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'];

    /**
     * 天干地支之地支速查表.
     *
     * @var array
     */
    protected $zhi = ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'];

    /**
     * 生肖.
     *
     * @var array
     */
    protected $animals = ['鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪'];


    /**
     * 日期转农历称呼速查表.
     *
     * @var array
     */
    protected $dateAlias = ['初一', '初二', '初三', '初四', '初五', '初六', '初七', '初八', '初九', '初十', '十一', '十二', '十三', '十四',
        '十五', '十六', '十七', '十八', '十九', '二十', '廿一', '廿二', '廿三', '廿四', '廿五', '廿六', '廿七', '廿八', '廿九', '三十'];

    /**
     * 月份转农历称呼速查表.
     *
     * @var array
     */
    protected $monthAlias = ['正', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '腊'];
    /**
     * 24节气速查表.
     *
     * @var array
     */
    protected $solarTerm = [
        '小寒', '大寒', '立春', '雨水', '惊蛰', '春分',
        '清明', '谷雨', '立夏', '小满', '芒种', '夏至',
        '小暑', '大暑', '立秋', '处暑', '白露', '秋分',
        '寒露', '霜降', '立冬', '小雪', '大雪', '冬至',
    ];
    /**
     * @var \DateTime
     */
    protected $date;            //日期

    protected $lunarYear;       //农历年
    protected $lunarMonth;      //农历月
    protected $lunarDay;        //农历日
    protected $isLeapMonth;     //是否是闰月
    protected $solarTermIndex;  //节气序号

    protected $dataCache = [];


    /**
     * @var DataParse
     */
    protected $lunarData;

    /**
     * @param $date
     * @return Calendar
     * @throws InvalidateDateException
     */
    public static function createFromSolar($date)
    {
        $calendar = new self();
        return $calendar->_createFromSolar($date);;
    }

    protected function _createFromSolar($date): Calendar
    {
        try {
            $this->date = new \DateTime($date);
        } catch (\Exception $e) {
            throw new InvalidateDateException();
        }

        $this->calcLunarDate();
        return $this;
    }

    public static function createFromLunar($year, $month, $day, $isLeap = false)
    {
        $calendar = new self();
        return $calendar->_createFromLunar($year, $month, $day, $isLeap);
    }

    protected function _createFromLunar($year, $month, $day, $isLeap = false): Calendar
    {
        $this->lunarYear = $year;
        $this->lunarMonth = $month;
        $this->lunarDay = $day;
        $this->isLeapMonth = $isLeap;
        $this->calcSolarDate();
        return $this;
    }

    /**
     * alias for createFromSolar
     * @param $date
     * @return Calendar
     * @throws InvalidateDateException
     */
    public function create($date): Calendar
    {
        return $this->createFromSolar($date);
    }

    public static function isLeapYear($year): bool
    {
        return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
    }


    protected function year()
    {
        return intval($this->date->format("Y"));
    }

    public function month()
    {
        return intval($this->date->format('n'));
    }

    public function day()
    {
        return intval($this->date->format('j'));
    }


    protected function getLunarDataFromSolar()
    {
        $this->lunarYear = intval($this->year());
        $index = $this->lunarYear - 1900 + 1;
        $DataOfThisYear = $this->dates[$index];
        $this->lunarData = new DataParse($DataOfThisYear, $this->lunarYear);
        $newYear = $this->lunarData->getNewYear();
        if ($newYear > $this->date->format('Y-m-d')) {
            $this->lunarYear = $this->lunarYear - 1;
            $DataOfPreYear = $this->dates[$index - 1];
            $this->lunarData = new DataParse($DataOfPreYear, $this->lunarYear);
        }
    }

    protected function calcLunarDate()
    {
        $this->getLunarDataFromSolar();
        $newYear = new \DateTime($this->lunarData->getNewYear());
        $days = $this->date->diff($newYear)->days + 1;
        $go = 0;
        $months = $this->lunarData->getMonthDays();
        $leapMonth = $this->lunarData->getLeapMonth();
        if ($leapMonth) {
            array_splice($months, $leapMonth, 0, $this->lunarData->getLeapDays());
        }
        foreach ($months as $k => $md) {
            if ($md + $go > $days) {
                $this->lunarMonth = $leapMonth ? ($k + 1 <= $leapMonth ? $k + 1 : $k) : $k + 1;
                $this->isLeapMonth = ($this->lunarMonth == $leapMonth) && ($k == $leapMonth);
                $this->lunarDay = $days - $go;
                break;
            }
            $go += $md;
        }
    }

    protected function getLunarDataFromLunar()
    {
        $index = $this->lunarYear - 1900 + 1;
        $DataOfThisYear = $this->dates[$index];
        $this->lunarData = new DataParse($DataOfThisYear, $this->lunarYear);

    }

    protected function calcSolarDate()
    {
        $this->getLunarDataFromLunar();
        $months = $this->lunarData->getMonthDays();
        $leapMonth = $this->lunarData->getLeapMonth();

        $go = 0;
        foreach ($months as $k => $v) {
            if ($k + 1 < $this->lunarMonth) {
                $go += $v;
            } else {
                break;
            }
        }
        if ($leapMonth) {   //有闰月
            if ($this->lunarMonth > $leapMonth) {   //已过闰月
                $go += $this->lunarData->getLeapDays();
            } elseif ($this->lunarMonth == $leapMonth && $this->isLeapMonth) { //当前月是闰月
                $go += $months[$leapMonth - 1]; //加上平月填上
            }
        }
        $go += $this->lunarDay;
        $go--;
        $newYear = $this->lunarData->getNewYear();
        $this->date = (new \DateTime($newYear))->add(new \DateInterval('P' . $go . 'D'));
    }

    /**
     * *********************************
     * 符号 * 描述 * 示例 *
     *  y  * 数字年 * 1999 *
     *  m  * 数字月 *  4   *
     *  d  * 数字日 *  13  *
     *  T  * 年天干 *  甲  *
     *  D  * 年地支 *  子  *
     *  Y  * 中文月 * 正月 *
     *  R  * 中文日 * 初八 *
     *  S  * 生肖   *  牛  *
     *  r * 月天干  *  甲  *
     *  s * 月地支  *  子  *
     *  e * 日天干  *  甲  *
     *  a * 日地支  *  子  *
     *  X * 星座    * 白羊 *
     *  j * 节气    * 白露 *
     *  w * 时天干  *  甲  *
     *  l * 时地支  *  子  *
     */
    protected function expression(): array
    {
        return [
            'y' => $this->lunarYear,
            'm' => $this->lunarMonth,
            'd' => $this->lunarDay,
            'T' => [$this, 'getYearTiangan'],
            'D' => [$this, 'getYearDizhi'],
            'S' => [$this, 'getShengxiao'],
            'Y' => [$this, 'getLunarMonth'],
            'R' => $this->dateAlias[$this->lunarDay - 1],
            'r' => [$this, 'getMonthTiangan'],
            's' => [$this, 'getMonethDizhi'],
            'X' => [$this, 'getConstellation'],
            'e' => [$this, 'getDayTiangan'],
            'a' => [$this, 'getDayDizhi'],
            'j' => [$this, 'getSolarTerm'],
            'w' => [$this, 'getHourTiangan'],
            'l' => [$this, 'getHourDizhi']
        ];
    }

    /**
     * @return \DateTime
     */
    public function solarDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * *********************************
     * 符号 * 描述 * 示例 *
     *  y  * 数字年 * 1999 *
     *  m  * 数字月 *  4   *
     *  d  * 数字日 *  13  *
     *  T  * 年天干 *  甲  *
     *  D  * 年地支 *  子  *
     *  Y  * 中文月 * 正月 *
     *  R  * 中文日 * 初八 *
     *  S  * 生肖   *  牛  *
     *  r * 月天干  *  甲  *
     *  s * 月地支  *  子  *
     *  e * 日天干  *  甲  *
     *  a * 日地支  *  子  *
     *  X * 星座    * 白羊 *
     * @param $format
     * @return array|string|string[]|null
     */
    public function lunarFormat($format)
    {

        return preg_replace_callback('/\w/', function ($matches) {
            $expressions = $this->expression();
            if (isset($expressions[$matches[0]])) {
                $key = $matches[0];
                //缓存
                if (isset($this->dataCache[$key])) {
                    return $this->dataCache[$key];
                }
                $result = $expressions[$matches[0]];
                if (is_callable($result)) {
                    $result = call_user_func($result);
                }
                $this->dataCache[$key] = $result;
                return $result;
            }
        }, $format);
    }

    protected function getLunarMonth()
    {
        return ($this->isLeapMonth ? '闰' : '') . $this->monthAlias[$this->lunarMonth - 1];
    }

    /**
     * 传入公历年获得该年第n个节气的公历日期
     *
     * @param int $no 二十四节气中的第几个节气(1~24)；从n=1(小寒)算起
     *
     * @return int
     *
     * @example
     * <pre>
     *  $_24 = $this->getTerm(1987,3) ;// _24 = 4; 意即 1987 年 2 月 4 日立春
     * </pre>
     */
    protected function getTermByNo($no): int
    {
        if ($this->year() < 1900 || $this->year() > 2100) {
            return -1;
        }
        if ($no < 1 || $no > 24) {
            return -1;
        }
        $solarTermsOfYear = array_map('hexdec', str_split($this->solarTerms[$this->year() - 1900], 5));
        $positions = [
            0 => [0, 1],
            1 => [1, 2],
            2 => [3, 1],
            3 => [4, 2],
        ];
        $group = intval(($no - 1) / 4);
        list($offset, $length) = $positions[($no - 1) % 4];

        return substr($solarTermsOfYear[$group], $offset, $length);
    }

    /**
     * 农历年 天干
     * @return mixed|string
     */
    protected function getYearTiangan()
    {
        $index = ($this->lunarYear - 4) % 10;
        return $this->gan[$index];
    }

    /**
     * 农历年 地支
     * @return mixed|string
     */
    protected function getYearDizhi()
    {
        $index = ($this->lunarYear - 4) % 12;
        return $this->zhi[$index];
    }

    /**
     * 农历年 生肖
     * @return mixed|string
     */
    protected function getShengxiao()
    {
        $index = ($this->lunarYear - 4) % 12;
        return $this->animals[$index];
    }

    /**
     * 农历月 天干
     * @return mixed|string
     */
    public function getMonthTiangan()
    {
        //甲或己年，正月建丙寅
        //乙或庚年，正月建戊寅
        //丙或辛年，正月建庚寅
        //丁或壬年，正月建壬寅
        //戊或癸年，正月建甲寅
        $yearIndex = ($this->lunarYear - 4) % 10;
        $index = (($yearIndex % 5) * 2 + 2 + $this->getSolarTermMonth() - 1) % 10;
        return $this->gan[$index];
    }

    /**
     * 农历月 地支
     * @return mixed|string
     */
    protected function getMonethDizhi()
    {
        $index = ($this->getSolarTermMonth() + 1) % 12;
        return $this->zhi[$index];
    }

    protected function getSolarTerm()
    {
        if ($this->solarTermIndex === null) {
            $this->getSolarTermMonth();
        }
        return $this->solarTerm[$this->solarTermIndex];
    }

    protected function getSolarTermMonth()
    {
        //一个月两个节气
        //取当前月的两个节气
        $month = $this->month();
        $start = ($month - 1) * 2 + 1;
        if ($this->solarTermIndex === null) {
            $firstTermDay = (int)$this->getTermByNo($start % 24);
            $secondTermDay = (int)$this->getTermByNo(($start + 1) % 24);
            if ($this->day() < $firstTermDay) {
                $this->solarTermIndex = $start - 1;
            } else if ($this->day() < $secondTermDay) {
                $this->solarTermIndex = $start;
            } else {
                $this->solarTermIndex = $start + 1;
            }
            if ($this->solarTermIndex < 0) {
                $this->solarTermIndex += 24;
            }
        }
        return $this->solarTermIndex ? ceil(($this->solarTermIndex - 2) / 2) : $this->lunarMonth;
    }

    /**
     * 公历月、日判断所属星座.
     *
     * @return string
     */
    protected function getConstellation(): string
    {
        $constellations = '魔羯水瓶双鱼白羊金牛双子巨蟹狮子处女天秤天蝎射手魔羯';
        $arr = [20, 19, 21, 21, 21, 22, 23, 23, 23, 23, 22, 22];

        return mb_substr(
            $constellations,
            $this->month() * 2 - ($this->day() < $arr[$this->month() - 1] ? 2 : 0),
            2,
            'UTF-8'
        );
    }


    protected function getDayTiangan()
    {

        return $this->gan[$this->getDayTianganIndex()];
    }

    protected function getDayTianganIndex()
    {
        $days = $this->getDiffFrom2000();
        $index = ($days + 4) % 10;
        if ($index < 0) {
            $index += 10;
        }

        return $index;
    }

    protected function getDayDizhi()
    {
        $days = $this->getDiffFrom2000();
        $index = ($days + 6) % 12;
        if ($index < 0) {
            $index += 12;
        }
        return $this->zhi[$index];
    }

    protected function getHourTiangan()
    {
        //甲己日起甲子
        //乙庚日起丙子
        //丙辛日起戊子
        //丁壬日起庚子
        //戊癸日起壬子
        $hour = (int)$this->date->format('G');
        $dayIndex = $this->getDayTianganIndex();
        $startIndex = ($dayIndex % 5) * 2;
        $hourIndex = ceil($hour % 24 / 2) % 12;
        $index = ($startIndex + $hourIndex) % 10;
        return $this->gan[$index];
    }

    protected function getHourDizhi()
    {
        $hour = $this->date->format('G');
        $index = ceil($hour % 24 / 2) % 12;
        return $this->zhi[$index];
    }

    protected function getDiffFrom2000()
    {
        $d2000 = new \DateTime('2000-01-01');
        $date = new \DateTime($this->date->format('Y-m-d'));
        $diff = $date->diff($d2000, false);
        return $diff->invert ? $diff->days : $diff->days * -1;
    }
}