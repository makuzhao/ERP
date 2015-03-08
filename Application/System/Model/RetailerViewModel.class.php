<?php

/*
 *  生产商收益
 *  出货 销售表关联
 *  tb_out  tb_sales
 * s
 */

namespace System\Model;

use Think\Model\ViewModel;
use \Think\View;

class RetailerViewModel extends ViewModel {

    protected $viewFields = array(
        'Sales' => array('out_id', 'barcode', 'num', 'presell', 'amount', 'salesman', 'saledate'),
        'Out' => array('id', 'product', 'outprice', '_as' => 'x', '_on' => 'x.barcode=sales.barcode'),
    );

}
