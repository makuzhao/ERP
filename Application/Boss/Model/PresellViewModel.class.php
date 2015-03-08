<?php

/*
 *  下限提示
 */

namespace Boss\Model;

use Think\Model\ViewModel;

class PresellViewModel extends ViewModel {
    /*
     * 需要的数据：条形码 商品名 首字母  总库存量 下限值 presell表的 id
     * 
     */

    protected $viewFields = array(
        'Presell' => array('id', 'barcode', 'letter', 'confine', '_type' => 'left'),
        'Storage' => array('product', 'outamount', 'SUM(outamount)' => 'total', '_as' => 'x', '_on' => 'Presell.barcode=x.barcode'),
    );

}
