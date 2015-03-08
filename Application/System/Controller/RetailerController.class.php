<?php

/*
 * 收益报表 出库 - 入库
 */

namespace System\Controller;

use Common\Controller\AuthController;
use System\Model\RetailerViewModel;
use Think\Auth;
use Think\Page;

class RetailerController extends AuthController {
    /*
     * 查阅商品收益   两表关联
     * 
     * 时间倒序
     * 商品名
     * 销售量  主表
     * 销售额 
     * 进货量
     * 进货额
     * 零售商收益 =  销售额 - 出货额
     * 
     * 
     */

    public function index() {

        $where['_string'] = "out_id = x.id";
        parent::showPage("RetailerView", "10", $where, "saledate desc");

        $tbName = D('RetailerView');
        $outsum = $tbName->where($where)->sum("amount");
        $this->assign("outsum", $outsum);

        $insum = $tbName->sum("outsum");
        $this->assign("insum", $insum);

        $total = $outsum - $insum;
        $this->assign("total", $total);

        $this->display();
    }

    /*
     *  searching() 搜索
     * 
     *  $str 关键字
     * 
     */

    public function searching() {
        if (!IS_GET) {
            $this->display();
        } else {
            $str = $_GET['search'];


            $where['saledate'] = array('like', "%$str%");
            $where['barcode'] = array('like', "%$str%");
            $where['product'] = array('like', "%$str%");
            $where['num'] = array('like', "%$str%");
            $where['amount'] = array('like', "%$str%");
            $where['outamount'] = array('like', "%$str%");
            $where['outsum'] = array('like', "%$str%");
            $where['_logic'] = 'or';
            $order = "saledate desc";

            $map['_complex'] = $where;
            $map['_string'] = "out_id = x.id";

            $this->assign("replace", "<font color='red'>$str</font>");
            $this->assign("str", $str);

            parent::showPage("RetailerView", "10", $where, $order);

            $tbName = D('RetailerView');
            $outsum = $tbName->where($map)->sum("amount");
            $this->assign("outsum", $outsum);

            $insum = $tbName->where($where)->sum("outsum");
            $this->assign("insum", $insum);

            $total = $outsum - $insum;
            $this->assign("total", $total);

            $this->display();
        }
    }

}
