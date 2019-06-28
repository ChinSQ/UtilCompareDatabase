# 请先阅读我

> 在开发过程中，或许总会遇到两个数据库之间的差异对比，很多时候，我们会因为缺少工具，觉得对比麻烦就全新覆盖上去了。

> 但是如果数据库已经部署到线上客户正在使用的情况下，那么我们只能用某些工具去对比差异，就我这个消息不通的技术渣来说，很经常，百度谷歌都找不到工具，所以我就写了这个，如果喜欢请支持我！！！

## 查看演示

![image](https://github.com/ChinSQ/UtilCompareDatabase/blob/master/1.png?raw=true)

## 如何部署

> 直接将该文件解压到您的服务器上面去就好了，说白了就是一个PHP脚本

> 目录结构说明
```
/CompareDatabase/                # 工具目录
/CompareDatabase/Core/           # 工具核心目录
/CompareDatabase/convention.php  # 数据库配置文件
/index.php                       # 对比脚本-直接Web目录访问到就可以查看差异了
```
> `convention.php` 说明
```
// 数据库配置
"database"=>[
    // 微惠云数据库配置
    "yun"=>[
        "drive"=>"mysql",
        "host"=>"127.0.0.1",
        "port"=>"3306",
        "username"=>"yun",
        "password"=>"EyLYLe7yxtnxMJ",
        "database"=>"yun",
        "charset"=>"utf8"
    ],
    // 微擎数据库配置
    "w7"=>[
        "drive"=>"mysql",
        "host"=>"127.0.0.1",
        "port"=>"3306",
        "username"=>"w7",
        "password"=>"aeRHX8wFa8xix",
        "database"=>"w7",
        "charset"=>"utf8"
    ],
],
```
> `index.php` 说明
```
const SHOW_A   = 1; // 只看A的差异
const SHOW_B   = 2; // 只看B的差异
const SHOW_ALL = 3; // 查看所有差异

// 基础配置
$dbNameA  = 'w7';          // 数据库A
$dbNameB  = 'yun';         // 数据库B
$prefixA  = 'ims_ybsc_';   // 数据库A 过滤表前缀
$prefixB  = 'ims_ybsc_';   // 数据库B 过滤表前缀
$showMode = SHOW_ALL;      // 显示模式
```

## 如果有疑问，欢迎加入群和大家一起交流

<img src="https://github.com/ChinSQ/UtilCompareDatabase/blob/master/qrcode.png?raw=true" width="300px" />

#### 如果我您你有帮助，可以考虑捐赠一下小弟呗，老穷了!

> 捐赠记得附带 捐赠备注，我会在文档中更新

<img src="https://github.com/ChinSQ/UtilCompareDatabase/blob/master/wxpay.jpg?raw=true" width="300px" />
<img src="https://github.com/ChinSQ/UtilCompareDatabase/blob/master/alipay.jpg?raw=true" width="300px" />

> @微惠云小程序 https://m.weihuiyun.net/







