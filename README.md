# Calendar
中国农历转换,生肖星座

---
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/vrobin/calendar)
![Packagist License](https://img.shields.io/packagist/l/vrobin/calendar)
[![Build Status](https://travis-ci.com/health901/Calendar.svg?branch=master)](https://travis-ci.com/health901/Calendar)
[![Version](https://poser.pugx.org/vkrobin/calendar/version)](//packagist.org/packages/vkrobin/calendar)
---
## 安装
`composer require vrobin/calendar`

## 使用

### 创建对象

使用公历日期创建对象 日期格式参考`DateTime`类
```
Calendar::createFromSolar('1989-02-06 00:32')
```
使用农历日期创建对象  第四个参数为是否为闰月
```
Calendar::createFromLunar(1989, 3, 8, false)
```
### 获取属性

获取公历数据

`solarDate` 方法返回一个`DateTime`对象,可使用`format`方法输出内容

例:
```
$calendar->solarDate()->format('Y-m-d');
```

获取农历数据

`lunarFormat` 通过格式字符串输出相应内容

输出生辰八字:
```
Calendar::createFromSolar('1989-02-06 00:32')->lunarFormat('TD年 rs月 ea日 wl时'); //己巳年 丙寅月 丁酉日 庚子时
```

输出农历日期:
```
Calendar::createFromSolar('2028-06-26')->lunarFormat('TD年 S Y月R'); //戊申年 猴 闰五月初四
```

符号格式表

| 符号 | 描述 | 示例 |
|:---:|:---:|:---:|
|  y  | 数字年 | 1999 |
|  m  | 数字月 |  4   |
|  d  | 数字日 |  13  |
|  T  | 年天干 |  甲  |
|  D  | 年地支 |  子  |
|  Y  | 中文月 | 正月 |
|  R  | 中文日 | 初八 |
|  S  | 生肖   |  牛  |
|  r | 月天干  |  甲  |
|  s | 月地支  |  子  |
|  e | 日天干  |  甲  |
|  a | 日地支  |  子  |
|  X | 星座    | 白羊 |
|  j | 节气    | 白露 |
|  w | 时天干  |  甲  |
|  l | 时地支  |  子  |