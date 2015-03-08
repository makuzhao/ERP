<?php

return array(
    //'配置项'=>'配置值'

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
     *  系统首页设置
     */
    "HOME_PAGE" => "ERP Manager 后台管理系统",
);
