<?php

/*
 * 收益报表 出库 - 入库
 */

namespace System\Controller;

use Common\Controller\AuthController;
use System\Model\RevenueViewModel;
use Think\Auth;
use Think\Page;

class RevenueController extends AuthController {
    /*
     * 查阅商品收益   两表关联
     * 
     * 时间倒序
     * 条形码
     * 商品名
     * 销售量  主表
     * 销售价
     * 销售额
     * 进货价
     * 生产商收益 = 销售额 - 进货额
     * 
     */

    public function index() {
        $where = "int_id = c.id";
        $everypage = "10";

        //$res = $out->field("id,outtime,outprice,outamount")->relation(true)->order("outtime desc")->select();
        $tbName = D("RevenueView"); // 实例化Data数据对象
        $count = $tbName->where($where)->count(); // 查询满足要求的总记录数

        $Page = new Page($count, $everypage); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $tbName->where($where)->order("outtime desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        //print_r($list);
        //var_dump($list);
        //  echo "SQL:" . $tbName->getLastSql();

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出


        $outsum = $tbName->where($where)->sum("outsum");
        $this->assign("outsum", $outsum);

        $insum = $tbName->sum("insum");
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
     *  outtime desc 
     * 
     */

    public function searching() {
        if (!IS_GET) {
            $this->display();
        } else {
            $str = $_GET['search'];


            $where['outtime'] = array('like', "%$str%");
            $where['x.product'] = array('like', "%$str%");
            $where['outamount'] = array('like', "%$str%");
            $where['outsum'] = array('like', "%$str%");
            $where['inamount'] = array('like', "%$str%");
            $where['insum'] = array('like', "%$str%");
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
            $map['_string'] = "int_id = c.id";

            $order = "outtime desc";

            $this->assign("replace", "<font color='red'>$str</font>");
            $this->assign("str", $str);

            parent::showPage("RevenueView", "10", $map, "outtime desc");

            $tbName = D('RevenueView');
            $outsum = $tbName->where($map)->sum("outsum");
            $this->assign("outsum", $outsum);

            $insum = $tbName->where($where)->sum("insum");
            $this->assign("insum", $insum);

            $total = $outsum - $insum;
            $this->assign("total", $total);




            $this->display();
        }
    }

}
