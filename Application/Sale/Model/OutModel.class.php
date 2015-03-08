<?php

/*
 *  出库商品
 */

namespace Home\Model;

use Think\Model\RelationModel;

class OutModel extends RelationModel {

    protected $_link = array(
        'In' => array(
            'mapping_type' => self::HAS_ONE,
            'foreign_key' => 'id',
            'mapping_fields ' => 'id',
        ),
    );

}
