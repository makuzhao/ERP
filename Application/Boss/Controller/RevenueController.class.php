<?php

/*
 * 收益报表 销售额 - 入库额  今天
 */

namespace Boss\Controller;

use Common\Controller\AuthController;
use Boss\Model\RevenueViewModel;
use Think\Auth;
use Think\Page;
use Think\Model;

class RevenueController extends AuthController {
    /*
     * 查阅商品收益   两表关联  所有商品 某种商品今天的销售额
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
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $shop_id = trim($_GET['shopid']);

        /*
         * 全局的shopid 从这里创建 
         */
        if ($shop_id == "") {
            $shop_id = $_SESSION['shop_id'];
        }
        if ($shop_id == "") {
            $this->error("请先点击具体的店铺，再回来点此");
        }
        $_SESSION["shop_id"] = $shop_id;
        $shopName = D("Shop")->field("shop")->where(" id = $shop_id")->find();
        $_SESSION["shop_name"] = $shopName["shop"];

        $this->assign("shopid", $shop_id);
        $this->assign("shopName", $shopName['shop']);

        $today = date("Y-m-d");
        $where ['saledate'] = array('like', "%$today%");
        $everypage = "10";

        //$res = $out->field("id,outtime,outprice,outamount")->relation(true)->order("outtime desc")->select();
        // $tbName = D("RevenueView"); // 实例化Data数据对象

        $tbName = new RevenueViewModel("RevenueViewModel", $tablePrefix = $shop_id);



        /*
         *  使用视图子查询
         */
        $subQuery = $tbName->group('barcode')->where($where)->order('sum(amount) desc')->select(false);
        $count = $tbName->table($subQuery . 'xc')->count('barcode');


        //$count = $tbName->count('barcode'); // 查询满足要求的总记录数


        $Page = new Page($count, $everypage); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $tbName->field("sum(amount) as total,sum(num) as num,barcode,product,letter")->where($where)->order("sum(amount) desc")->limit($Page->firstRow . ',' . $Page->listRows)->group("barcode")->select();

        // var_dump($list);
        //print_r($list);
        //var_dump($list);
        //  echo "SQL:" . $tbName->getLastSql();

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        // $tbName = D('RevenueView');
        $sumSQL = $tbName->field('sum(amount) as total,barcode,product')->where($where)->order("sum(amount) desc")->group("barcode")->select(FALSE);

        $outsum = $tbName->table($sumSQL . 'cc')->sum('total');


        $this->assign("outsum", $outsum);



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

        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        if (!IS_GET) {
            $this->display();
        } else {
            $shop_id = trim($_GET['shopid']);
            if ($shop_id == "") {
                $shop_id = $_SESSION["shop_id"];
            }
            $shopName = $_SESSION["shop_name"];
            $this->assign("shopName", $shopName);

            if ($shop_id == "") {
                $this->error("请先点击具体的店铺，再回来点此");
            }
            $this->assign("shop", U('Revenue/index', array('shopid' => $shop_id)));

            $pro = trim($_GET['product']);
            if ($pro != "") {
                $pro = strtoupper($pro);
                $where[$shop_id . "Goods.product|" . $shop_id . "Goods.letter"] = array('like', "%$pro%");
                $this->assign("pro", $pro);
            }

            $bar = trim($_GET['barcode']);
            if ($bar != "") {

                $where[$shop_id . 'Goods.barcode'] = array('like', "%$bar%");
                $this->assign("bar", $bar);
            }

            $start = trim($_GET['start']);

            $end = trim($_GET['end']);
            if ($end != "") {

                $where['saledate'] = array('between', array($start . " 00:00:00", $end . " 59:59:59"));
                $this->assign("date", $people);
            }




            $everypage = "10";

            //$res = $out->field("id,outtime,outprice,outamount")->relation(true)->order("outtime desc")->select();
            //  $tbName = D('RevenueView'); // 实例化Data数据对象
            $tbName = new RevenueViewModel("RevenueViewModel", $tablePrefix = $shop_id);
            /*
             *  使用视图子查询
             */
            $subQuery = $tbName->group('barcode')->where($where)->order('sum(amount) desc')->select(false);
            $count = $tbName->table($subQuery . 'xc')->count('barcode');

            //$count = $tbName->count('barcode'); // 查询满足要求的总记录数


            $Page = new Page($count, $everypage); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show = $Page->show(); // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $tbName->field("sum(amount) as total,num,barcode,product,letter")->where($where)->order("sum(amount) desc")->limit($Page->firstRow . ',' . $Page->listRows)->group("barcode")->select();


            $this->assign('list', $list); // 赋值数据集
            $this->assign('page', $show); // 赋值分页输出
            //$tbName = D('RevenueView');
            $sumSQL = $tbName->field('sum(amount) as total,Goods.letter')->where($where)->order("sum(amount) desc")->group("barcode")->select(FALSE);

            $outsum = $tbName->table($sumSQL . 'cc')->sum('total');

            $this->assign("outsum", $outsum);

            $this->display();
        }
    }

}
