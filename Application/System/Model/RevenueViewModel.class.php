<?php

/*
 *  零售商收益
 *  出货 进货表关联
 *  tb_out  tb_in
 * 
 */

namespace System\Model;

use Think\Model\ViewModel;
use \Think\View;

class RevenueViewModel extends ViewModel {

    protected $viewFields = array(
        'Out' => array('int_id', 'barcode', 'outtime', 'outamount', 'outprice', 'outsum', 'product', '_as' => 'x'),
        'In' => array('id', 'inprice', 'product' => 'pro', '_as' => 'c', '_on' => 'x.barcode=c.barcode'),
    );

}
