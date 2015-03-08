<?php

/*
 *  生产商收益
 *  出货 销售表关联
 *  tb_out  tb_sales
 * s
 */

namespace Boss\Model;

use Think\Model\ViewModel;
use \Think\View;

class RetailerViewModel extends ViewModel {

    protected $viewFields = array(
            //  'db_erp.user' => array('name', 'realName', '_as' => 'us', '_type' => 'left'),
            //  '14Sales' => array('salesman', 'amount', '_on' => 'us.realName=14Sales.salesman'),
    );

    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        parent::__construct($name, $tablePrefix, $connection);
        $this->viewFields = array(
            'db_erp.user' => array('name', 'realName', '_as' => 'us', '_type' => 'left'),
            $tablePrefix . 'Sales' => array('salesman', 'amount', '_on' => 'us.realName=' . $tablePrefix . 'Sales.salesman'),
        );
    }

}
