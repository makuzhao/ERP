<?php

// 测 PHP 版本
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    echo "PHP 版本太低，至少是 5.3.0";
}

// 开启调试模式
define('APP_DEBUG', true);

// 应用名
define('APP_NAME', 'Application');

// 应用目录
define('APP_PATH', './Application/');



// 导入ThinkPHP 核心文件
require './ThinkPHP/ThinkPHP.php';


