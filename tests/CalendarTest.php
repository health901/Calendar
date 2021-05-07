<?php


use VRobin\Calendar\Calendar;
use PHPUnit\Framework\TestCase;

class CalendarTest extends TestCase
{
    public function testIsLeapYear()
    {
        $this->assertTrue(Calendar::isLeapYear('2020'));
        $this->assertFalse(Calendar::isLeapYear('1900'));
        $this->assertTrue(Calendar::isLeapYear('1904'));
    }

    public function testLunar()
    {
        //普通日期
        $this->assertEquals('1989-3-8', Calendar::createFromSolar('1989-04-13')->lunarFormat('y-m-d'));
        $this->assertEquals('1989-04-13', Calendar::createFromLunar(1989, 3, 8)->solarDate()->format('Y-m-d'));
        //闰年
        $this->assertEquals('戊申年 猴 五月初四', Calendar::createFromSolar('2028-05-27')->lunarFormat('TD年 S Y月R'));
        $this->assertEquals('戊申年 猴 闰五月初四', Calendar::createFromSolar('2028-06-26')->lunarFormat('TD年 S Y月R'));
        $this->assertEquals('2028-05-27', Calendar::createFromLunar(2028, 5, 4)->solarDate()->format('Y-m-d'));
        $this->assertEquals('2028-06-26', Calendar::createFromLunar(2028, 5, 4, true)->solarDate()->format('Y-m-d'));
        //节气
        $this->assertEquals('清明', Calendar::createFromSolar('2021-04-04')->lunarFormat('j'));
        $this->assertEquals('谷雨', Calendar::createFromSolar('2021-04-21')->lunarFormat('j'));

        $cal = Calendar::createFromSolar('1989-02-06');
        $this->assertEquals('丙寅', $cal->lunarFormat('rs'), '月干支');
        $this->assertEquals('丙寅', $cal->lunarFormat('rs'), '月干支缓存');
        $this->assertEquals('丁酉', $cal->lunarFormat('ea'), '日干支');

        $this->assertEquals('壬辰', Calendar::createFromSolar('2021-04-08')->lunarFormat('rs'), '月干支');
        $this->assertEquals('丙戌', Calendar::createFromSolar('2021-04-08')->lunarFormat('ea'), '日干支');

        $this->assertEquals('壬午', Calendar::createFromSolar('2045-06-10')->lunarFormat('rs'), '月干支');
        $this->assertEquals('乙未', Calendar::createFromSolar('2045-06-10')->lunarFormat('ea'), '日干支');

        //八字 测试数据 https://www.buyiju.com/bazi/#csshow
        $this->assertEquals('己巳年 丙寅月 丁酉日 庚子时', Calendar::createFromSolar('1989-02-06 00:32')->lunarFormat('TD年 rs月 ea日 wl时'));
        $this->assertEquals('甲午年 己巳月 壬午日 乙巳时', Calendar::createFromSolar('2014-05-11 09:32')->lunarFormat('TD年 rs月 ea日 wl时'));
    }
}
