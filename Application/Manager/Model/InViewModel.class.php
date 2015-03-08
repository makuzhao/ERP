<?php

/*
 * 入库查询 tb_in  tb_goods
 * 
 */

namespace Manager\Model;

use Think\Model\ViewModel;

class InViewModel extends ViewModel {

    protected $viewFields = array(
        'Goods' => array('letter', 'id' => 'goid'),
        'In' => array('id', 'encode', 'intime', 'insum', 'inprice', 'inamount', 'people', 'barcode' => 'incode', 'product' => 'pro', '_as' => 'x', '_on' => 'x.barcode=Goods.barcode'),
    );

}
