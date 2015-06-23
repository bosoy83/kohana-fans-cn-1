# Logger 库 #

| author | icyleaf|
|:-------|:-------|
| version | 0.1    |

Logger 库提供获取 Kohana 系统指定 log 目录的日志文件以及查看某个 YYYY-MM-DD 的日志详情。

## 概述（Overview） ##

首先是实例化 Logger 类，

```
// 默认初始化，日志路径使用 APPPATH.'logs'。
$logger = Logger::instance();

// 自定义日志路径，比如在 APPPATH.'app1/logs'
logger = Logger::instance(APPPATH.'app1/logs');
```

## 属性（Property） ##

### path ###

动态设置日志路径。

```
// 实例化 Logger 后，可以使用下面方法修改日志路径： APPPATH.'app1/logs'
$logger->path = APPPATH.'app1/logs';
```

## 方法（Method） ##

### get\_logs() ###

get\_logs($year=NULL, $month=NULL, $day=NULL) 以数组的形式返回指定日志目录下的所有日志文件，其中包含三个参数：

  * (int) $year  - 指定查询的年份
  * (int) $month - 指定查询的月份
  * (int) $day   - 指定查询的天数
  * (array) 返回数组形式的结果，如果没有日志文件返回 NULL

实例：
```
// 返回日志目录所有的日志文件
$log_files = $logger->get_logs();
// 调试输出结果
echo Kohana::debug($log_files);

// 自定义日期查询，比如查询包含 2009 年 10 月的日志文件
$log_2009_10_files = $logger->get_logs(2009, 10);
// 调试输出结果
echo Kohana::debug($log_files);
```

返回结果：
```
// 返回 $logger->get_logs() 的结果
array(2) (
    2009 => array(1) (
        10 => array(3) (
            10 => array(3) (
                "file" => string(45) "logs/2009/10/10.php"
                "created_date" => integer 1255146704
                "modify_date" => integer 1255164921
            )
            12 => array(3) (
                "file" => string(45) "logs/2009/10/12.php"
                "created_date" => integer 1255323052
                "modify_date" => integer 1255344031
            )
            13 => array(3) (
                "file" => string(45) "logs/2009/10/13.php"
                "created_date" => integer 1255397764
                "modify_date" => integer 1255397764
            )
        )
    )
)
```

### get\_log() ###

类似于上面的 get\_logs() 方法，只不过是返回单个日志文件的详情。同样有上面的三个参数。

实例：
```
// 显示 2009 年 10 月 12 日的日志信息
$log_content = $logger->get_log(2009, 10, 12);
// 调试输出结果
echo Kohana::debug($log_content);
```

返回结果：
```
// 返回的日志内容类似下面的形式
array(4) (
    0 => array(3) (
        "date" => integer 1255323052
        "type" => string(5) "ERROR"
        "desc" => string(129) "Kohana_Request_Exception [ 0 ]: Unable to find a route to match the URI: favicon.ico ~ SYSPATH/classes\kohana\request.php [ 571 ]"
    )
    1 => array(3) (
        "date" => integer 1255323055
        "type" => string(5) "ERROR"
        "desc" => string(129) "Kohana_Request_Exception [ 0 ]: Unable to find a route to match the URI: favicon.ico ~ SYSPATH/classes\kohana\request.php [ 571 ]"
    )
    2 => array(3) (
        "date" => integer 1255343991
        "type" => string(5) "ERROR"
        "desc" => string(181) "ErrorException [ 2048 ]: Non-static method Logger::instance() should not be called statically, assuming $this from incompatible context ~ APPPATH/classes\controller\logger.php [ 7 ]"
    )
    3 => array(3) (
        "date" => integer 1255344031
        "type" => string(5) "ERROR"
        "desc" => string(188) "ErrorException [ 2 ]: Missing argument 1 for Logger::get_logs(), called in E:\PHP\cactus\application\classes\controller\logger.php on line 9 and defined ~ APPPATH/classes\logger.php [ 35 ]"
    )
)
```


## 源码(Source) ##

Kohana v3.0

  * libraries/[logger.php](http://code.google.com/p/kohana-fans-cn/source/browse/trunk/3.0/libraries/logger.php)