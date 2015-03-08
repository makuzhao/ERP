<?php

/*
 *  本店商品收益
 * 
 */

namespace Boss\Model;

use Think\Model\ViewModel;
use \Think\View;

class RevenueViewModel extends ViewModel {

    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        parent::__construct($name, $tablePrefix, $connection);
        $this->viewFields = array(
            $tablePrefix . 'Goods' => array('barcode', 'product', 'letter', '_type' => 'LEFT'),
            $tablePrefix . 'Sales' => array('amount', 'SUM(num)' => 'num', 'saledate', '_on' => $tablePrefix . "Goods.barcode = " . $tablePrefix . "Sales.barcode"),
        );
    }

    protected $viewFields = array(
            //'Goods' => array('barcode', 'product', 'letter', '_type' => 'LEFT'),
            //  'Sales' => array('amount', 'SUM(num)' => 'num', 'saledate', '_on' => 'Goods.barcode = Sales.barcode'),
    );

}
