<?php

/*
 * 销售员的收益列表
 */

namespace Boss\Controller;

use Common\Controller\AuthController;
use Boss\Model\RetailerViewModel;
use Think\Auth;
use Think\Page;
use Think\Model;

class RetailerController extends AuthController {
    /*
     * 当前Boss 名下的销售员 
     */

    public function index() {

        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $shop_id = trim($_GET['shopid']);
        if ($shop_id == "") {
            $shop_id = $_SESSION["shop_id"];
            $shopName = $_SESSION["shop_name"];
        }

        if ($shop_id == "") {
            $this->error("请先点击具体的店铺，再回来点此");
        }

        $this->assign("shopid", $shop_id);
        $this->assign("shopName", $shopName);

        $today = date("Y-m-d");
        $where ['saledate'] = array('like', "%$today%");
        $where['belong'] = $_SESSION['auth'];

        //$where = "";
        $everypage = "10";

        //$res = $out->field("id,outtime,outprice,outamount")->relation(true)->order("outtime desc")->select();
        $tbName = D("RetailerView"); // 实例化Data数据对象
        $tbName = new RetailerViewModel("RetailerViewModel", $tablePrefix = $shop_id);
        /*
         *  使用视图子查询
         */
        $subQuery = $tbName->field('*')->group('name')->where($where)->order('sum(amount) desc')->select(false);
        $count = $tbName->table($subQuery . 'xc')->count('name');


        //$count = $tbName->count('barcode'); // 查询满足要求的总记录数


        $Page = new Page($count, $everypage); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $tbName->field("sum(amount) as total,name,realName")->where($where)->order("sum(amount) desc")->limit($Page->firstRow . ',' . $Page->listRows)->group("name")->select();

        // var_dump($list);
        //print_r($list);
        //var_dump($list);
        //  echo "SQL:" . $tbName->getLastSql();

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        //$tbName = D('RetailerView');
        $sumSQL = $tbName->field('sum(amount) as total,barcode,product,realName')->where($where)->order("sum(amount) desc")->group("barcode")->select(FALSE);

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

        $shop_id = trim($_GET['shopid']);

        if ($shop_id == "") {
            $shop_id = $_SESSION["shop_id"];
        }


        if ($shop_id == "") {
            $this->error("请先点击具体的店铺，再回来点此");
        }
            
        $this->assign("shopid", $shop_id);
        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);



        $this->assign("shop", U('Retailer/index', array('shopid' => $shop_id)));

        if (!IS_GET) {
            $this->display();
        } else {
            $man = trim($_GET['man']);
            $date = trim($_GET['date']);

            if ($man != "") {

                $where['realName'] = array('like', "%$man%");
                $this->assign("man", $man);
            }
            $start = trim($_GET['start']);

            $end = trim($_GET['end']);
            if ($end != "") {

                $where['saledate'] = array('between', array($start . " 00:00:00", $end . " 59:59:59"));
                $this->assign("date", $people);
            }
            $where['belong'] = $_SESSION['auth'];

            $everypage = "10";

            //$res = $out->field("id,outtime,outprice,outamount")->relation(true)->order("outtime desc")->select();
            // $tbName = D("RetailerView"); // 实例化Data数据对象
            $tbName = new RetailerViewModel("RetailerViewModel", $tablePrefix = $shop_id);

            /*
             *  使用视图子查询
             */
            $subQuery = $tbName->group('name')->where($where)->order('sum(amount) desc')->select(false);
            $count = $tbName->table($subQuery . 'xc')->count('name');

            //$count = $tbName->count('barcode'); // 查询满足要求的总记录数


            $Page = new Page($count, $everypage); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show = $Page->show(); // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $tbName->field("sum(amount) as total,name,realName")->where($where)->order("sum(amount) desc")->limit($Page->firstRow . ',' . $Page->listRows)->group("name")->select();



            $this->assign('list', $list); // 赋值数据集
            $this->assign('page', $show); // 赋值分页输出
            // $tbName = D('RetailerView');
            $sumSQL = $tbName->field('sum(amount) as total')->where($where)->order("sum(amount) desc")->group("name")->select(FALSE);

            $outsum = $tbName->table($sumSQL . 'cc')->sum('total');

            $this->assign("outsum", $outsum);



            $this->display();
        }
    }

}
