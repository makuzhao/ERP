<?php

/**
 * 用户表 、 用户组表 、 规则表 关联
 */

namespace Boss\Model;

use Think\Model\RelationModel;

class UserModel extends RelationModel {

    protected $_link = array(
        'Group' => array(
            'mapping_type' => self::MANY_TO_MANY,
            'foreign_key' => 'uid',
            'relation_foreign_key' => 'group_id',
            'relation_table' => 'tb_group_user', //此处应显式定义中间表名称，且不能使用C函数读取表前缀   
        )
    );

    /*
      protected $connection = array(
      'db_type' => 'mysql',
      'db_user' => 'root',
      'db_pwd' => 'root',
      'db_host' => 'localhost',
      'db_port' => '3308',
      'db_name' => "db_boss",
      'db_charset' => 'utf8',
      );
     */
}
