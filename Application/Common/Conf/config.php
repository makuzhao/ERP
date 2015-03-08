<?php

return array(
//'配置项'=>'配置值'

    'URL_MODEL' => 2, // URL模式

    /*
     * 数据库设置 
     */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'db_erp', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'root', // 密码
    'DB_PORT' => '3306', // 端口    注意：服务器的端口是 3306
    'DB_PREFIX' => 'tb_', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8


    /*
     * Auth 认证配置  
     * 
     * 特别要注意四表的表名，注意更新
     * 
     */
    'AUTH_ON' => true, // 认证开关
    'AUTH_TYPE' => 1, // 认证方式，1 实时认证；2 登录认证。
    'AUTH_GROUP' => 'group', // 用户组数据表名
    'AUTH_GROUP_ACCESS' => 'group_user', // 用户-用户组关系表
    'AUTH_RULE' => 'rule', // 权限规则表
    'AUTH_USER' => 'user', // 用户信息表,不要带前缀

    /*
     *  系统首页设置
     */
    "HOME_PAGE" => "ERP 后台管理系统",
    "ALL_COPY" => "Copyright © www.yestop.com.cn All Rights Reserved. ", 
    "DELU" => 'ERP 系统，欢迎你试用', //  

    /*
     * 系统默认设置
     * 
     */
    'DEFAULT_MODULE' => 'Sale', // 默认模块
    'DEFAULT_CONTROLLER' => 'Login', // 默认控制器名称
    'DEFAULT_ACTION' => 'index', // 默认操作名称

    /*
     * 模板提示配置
     */
    /* 错误配置 */
    'ERROR_MESSAGE' => '->_-> ERROR <-_<-', //错误显示信息,非调试模式有效
    'ERROR_PAGE' => __ROOT__ . '/Public/Alert/messager.html', // 错误定向页面  根目录下 
    'SHOW_ERROR_MSG' => true, // 显示错误信息
    //'SHOW_PAGE_TRACE' => True, // 显示页面Trace信息
    // 错误页面
    'URL_404_REDIRECT' => __ROOT__ . '/Public/Alert/messager.html',
    'TMPL_EXCEPTION_FILE' => __ROOT__ . '/Public/Alert/messager.html', // 异常页面的模板文件
    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => 'Alert:error',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Alert:success',
    /*
     * 域名配置
     */
    'PATH_HTTP' => "http",
    // 域名或IP
    'PATH_URL' => "127.0.0.1",
    // web端口 
    'PATH_PORPT' => "80",
    //项目名称
    'PATH_PROJECT' => "ERP",
);
