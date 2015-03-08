<?php

return array(
    //'配置项'=>'配置值'
    /* 数据库设置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'db_erp', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'root', // 密码
    'DB_PORT' => '3308', // 端口
    'DB_PREFIX' => 'tb_', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8

    /* 错误配置 */
    'SHOW_ERROR_MSG' => true, // 显示错误信息
    'SHOW_PAGE_TRACE' => true,
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
    'AUTH_USER' => 'user', // 用户信息表
    /*
     * 安全秘钥
     * 
     */
    'AUTHBEFORE' => '',
    'AUTHEND' => '',
    /*
     *  系统首页设置
     */
    "HOME_PAGE" => "ERP 后台管理系统",
    /*
     * 系统默认设置
     * 
     */
    'DEFAULT_MODULE' => 'Sale', // 默认模块
    'DEFAULT_CONTROLLER' => 'Login', // 默认控制器名称
    'DEFAULT_ACTION' => 'index', // 默认操作名称
);
