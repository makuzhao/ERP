<?php

/*
 *  下限提示
 */

namespace Manager\Model;

use Think\Model\ViewModel;

class PresellViewModel extends ViewModel {
    /*
     * 需要的数据：条形码 商品名  供货商 总库存量 下限值 presell表的 id
     * 
     */

    protected $viewFields = array(
        'Presell' => array('id', 'barcode', 'letter', 'confine'),
        'Goods' => array('com_id', '_on' => 'Presell.barcode=Goods.barcode'),
        'Company' => array("company", '_on' => 'Goods.com_id=Company.id'),
        'Out' => array('product', 'outamount', '_as' => 'x', '_on' => 'Presell.barcode=x.barcode'),
    );

}
