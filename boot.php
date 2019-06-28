<?php
define('DS', DIRECTORY_SEPARATOR);
define('PHP_EXT', '.php');
define('ROOT_PATH', __DIR__ . DS);
define('CORE_PATH', ROOT_PATH . 'Core' . DS);
define('DATA_PATH', ROOT_PATH . 'Data' . DS);
define('CACHE_PATH', DATA_PATH . 'Cache' . DS);
define('APP_PATH', ROOT_PATH . 'App' . DS);
define('ACTION_PATH', APP_PATH . 'Action' . DS);


// 加载函数
require CORE_PATH . "function" . PHP_EXT;

// 设置错误
 error_reporting(E_ALL);

// 注册自动加载
spl_autoload_register(function($class_name) {
    if(DIRECTORY_SEPARATOR == '/') {
        $class_name = str_replace('\\','/', $class_name);
    }
    __include(ROOT_PATH . $class_name . PHP_EXT );
});

// 定义业务常量
define('TIMESTAMP', time());
define('DATATIME', date('Y-m-d H:i:s'));
define('CLIENT_IP', request()->ip());