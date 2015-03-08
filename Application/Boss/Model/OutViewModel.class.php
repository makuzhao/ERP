<?php

/*
 * 入库查询 tb_in  tb_goods
 * 
 */

namespace Boss\Model;

use Think\Model\ViewModel;

class OutViewModel extends ViewModel {

    protected $viewFields = array(
        'Goods' => array('letter', 'id' => 'goid', 'product' => 'pro', 'barcode' => 'code',),
        'Storage' => array('id', 'encode', 'outtime', 'outsum', 'outprice', 'outamount', 'people', 'barcode', 'product', '_as' => 'x', '_on' => 'x.barcode=Goods.barcode'),
    );

}
