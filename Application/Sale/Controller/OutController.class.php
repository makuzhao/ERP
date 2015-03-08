<?php

/*
 *  出库商品
 */

namespace Sale\Controller;

use Common\Controller\AuthController;
use Think\Model;

class OutController extends AuthController {
    /*
     * 显示所有出库商品
     * 
     */

    public function index() {

        $model = new Model();
        $sql = "use " . $_SESSION['dbName'];
        $model->query($sql);

        $shop_id = $_SESSION['storeId'];

        $out = D($shop_id . "Storage");

        $res = $out->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);


            parent::showPage($shop_id . "Storage", "10");
        }
        /*
         * 金额
         */
        $total = $out->sum("outsum");
        $this->assign("total", $total);
        /*
         * 数量
         */
        $num = $out->sum("outamount");
        $this->assign("num", $num);
        /*
         * 单子数
         */
        $id = $out->count("id");
        $this->assign("id", $id);

        $this->display();
    }

    /*
     * showShop() 通过零售商的 ID 显示 商家名 点击显示具体的信息
     */

    public function showShop() {
        $model = new Model();
        $sql = "use " . $_SESSION['dbName'];
        $model->query($sql);

        $sh_id = trim($_GET['shid']);
        if ($sh_id == "") {
            $this->error("操作错误");
        } else {
            $shop = D("Shop");
            $where['id'] = $sh_id;
            $res = $shop->where($where)->select();
            $this->assign("shop", $res);
            $this->display();
        }
    }

    /*
     *  look()  查看商品的具体信息
     * 
     *  1、 根据get传来的 barcode 查库
     *  
     *  2、 信息显示在弹出层上
     */

    public function look() {
        $model = new Model();
        $sql = "use " . $_SESSION['dbName'];
        $model->query($sql);

        $id = $_GET['id'];
        $where['barcode'] = $id;

        $shop_id = $_SESSION['storeId'];
        $goods = D($shop_id . 'Goods');

        $res = $goods->where($where)->select();


        $this->assign("look", $res);


        if (!$res) {
            $this->error("不存在这个商品条形码 ？？");
        } else {
            $this->assign("look", $res);
        }
        $this->display();
    }

    /*
     *  add() 商品出库 主要先添加 tb_out  后更新 tb_in
     * 
     *  1、根据 ajax 通过 get 传递的 barcode 值 查询 tb_in 的 总inamount ，然后显示到前端指定的位置
     * 
     *  2、获取前端所有的数据，并写入 tb_out
     * 
     *  3、条件 barcode inamount>0 查询 tb_in 中的 id  inamount 排序 id ASC  的一条数据，根据 所得的id 条件更新 inamount 的值
     * 
     *  4、根据 outamount 数值循环执行 3 步骤
     * 
     */

    public function add() {

        $model = new Model();
        $sql = "use " . $_SESSION['dbName'];
        $model->query($sql);


        $shop_id = $_SESSION['storeId'];

        if (!IS_POST) {
            $this->display();
        } else {

            $out = D($shop_id . "Storage");
            $code = $_POST['code'];

            $shop = D($shop_id . "Goods");

            $sh = $shop->field("id,product,letter,price")->where("barcode = $code")->find();



            $outamount = $_POST['outamount'];
            /*
             * 获取所有数据
             */
            $data['encode'] = "OUT" . time();
            $data['barcode'] = $code;
            $data['product'] = $sh['product'];
            $data['letter'] = $sh['letter'];
            //$data['sh_id'] = $_POST['sh_id'];
            $data['outprice'] = $_POST['outprice'];
            $data['outamount'] = $outamount;
            $data['outsum'] = $outamount * $_POST['outprice'];
            $data['outpresell'] = $_POST['outpresell'];
            $data['people'] = $_SESSION['nameReal'];
            $data['outtime'] = date("Y-m-d H:i:s");



            $res = $out->add($data);
            if (!$res) {
                $this->error("商品进货失败");
                return false;
            } else {
                $this->success("进货成功", U('Out/add'), 1);
            }
        }
    }

    /*
     * shopID() 获取零售商的ID  货要送到哪
     * 
     */



    /*
     * 
     * ajax()   异步提交条形码 根据条形码 查出库存的商品名、总数量  然后把名称返回到前端
     *
     * 1、接收get传来的条形码 barcode
     * 
     * 2、根据 barcode 提取  tb_in 表中的    product   总的inamount
     * 
     *          
     *    如果条形码找不到该商品，提示去采购该商品
     *    
     *    如果找到商品，请选择订单的商品 ， 把选择订单的 id 传回 提取 tb_in 表的 inamount 
     * 
     * 3、在特定的位置输出对应的值
     * 
     */

    public function ajax() {
        $model = new Model();
        $sql = "use " . $_SESSION['dbName'];
        $model->query($sql);

        $shop_id = $_SESSION['storeId'];

        $shop = D($shop_id . "Goods");
        $co = $shop->count("id");

        $sh = $shop->field("id,company")->select();



        $code = $_POST['code'];
        $out = D($shop_id . "Goods");
        $where['barcode'] = array("like", "%$code%");
        $s = strtoupper($code);
        $where['letter'] = array("like", "%$s%");
        $where['_logic'] = 'OR';

        //$count = $out->where($where)->sum("inamount"); // 总的库存量
        $res = $out->field("id,product,barcode,price,letter")->where($where)->order('id desc')->limit(1)->select(); // 商品名
        // var_dump($res);

        if (!$res) {
            $this->error("本店没有该商品", U('Out /add'));
        } else {
            $this->assign("res", $res);

            $this->display();
        }
    }

    /*
     * 更新出库商品
     */

    public function update() {
        $model = new Model();
        $sql = "use " . $_SESSION['dbName'];
        $model->query($sql);
        $shop_id = $_SESSION['storeId'];

        $out = D("Storage");

        if (!IS_POST) {
            $outid = $_GET['outid'];
            if (empty($outid)) {
                $this->error("非法操作！", U('Out /index'));
            } else {
                $where['id'] = $outid;
                $res = $out->where($where)->select();
                $this->assign("res", $res);
                $this->display();
            }
        } else {
            $where['id'] = $_POST['outid'];

            $data['people'] = $_POST['people'];
            $res = $out->where($where)->save($data);

//  echo $out->getLastSql();

            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Out/index'));
            }
        }
    }

    /*
     * 删除出库商品
     */

    public function delete() {
        $model = new Model();
        $sql = "use " . $_SESSION['dbName'];
        $model->query($sql);

        $outid = $_GET['outid'];
        if (empty($outid)) {
            $this->error("非法操作！", U('Out /index'));
        } else {
            $out = D("Storage");
            $where['id'] = $outid;
            $res = $out->where($where)->delete();
            if ($res) {
                $this->success("删除成功", U('Out/index'));
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /*
     * 用户搜索 searching（）
     * 
     * $str  关键字
     * 
     */

    public function searching() {
        $model = new Model();
        $sql = "use " . $_SESSION['dbName'];
        $model->query($sql);
        $shop_id = $_SESSION['storeId'];

        if (!IS_GET) {
            $this->display();
        } else {
            $str = trim($_GET['search']);

            $bar = trim($_GET['barcode']);
            if ($bar != "") {

                $where['barcode'] = array('like', "%$bar%");
                $this->assign("bar", $bar);
            }

            $pro = trim($_GET['product']);
            if ($pro != "") {

                $where['product'] = array('like', "%$pro%");
                $this->assign("pro", $pro);
            }

            $let = trim($_GET['letter']);
            if ($let != "") {
                $let = strtoupper($let);
                $where['letter'] = array('like', "%$let%");
                $this->assign("let", $let);
            }

            $amount = trim($_GET['outamount']);
            if ($amount != "") {

                $where['outamount'] = array('like', "%$amount%");
                $this->assign("amount", $amount);
            }

            $presell = trim($_GET['outpresell']);
            if ($presell != "") {

                $where['outpresell'] = array('like', "%$presell%");
                $this->assign("presell", $presell);
            }

            $price = trim($_GET['outprice']);
            if ($price != "") {

                $where['outprice'] = array('like', "%$price%");
                $this->assign("price", $price);
            }

            $sum = trim($_GET['outsum']);
            if ($sum != "") {

                $where['outsum'] = array('like', "%$sum%");
                $this->assign("sum", $sum);
            }

            $people = trim($_GET['people']);
            if ($people != "") {

                $where['people'] = array('like', "%$people%");
                $this->assign("people", $people);
            }

            $start = trim($_GET['start']);

            $end = trim($_GET['end']);
            if ($end != "") {

                $where['outtime'] = array('between', array($start . " 00:00:00", $end . " 59:59:59"));
                $this->assign("date", $people);
            }


            parent::showPage($shop_id . "Storage", "10", $where);

            $tb = D($shop_id . "Storage");

            /*
             * 金额
             */
            $total = $tb->where($where)->sum("outsum");
            $this->assign("total", $total);
            /*
             * 数量
             */
            $num = $tb->where($where)->sum("outamount");
            $this->assign("num", $num);
            /*
             * 单子数
             */
            $id = $tb->where($where)->count("id");
            $this->assign("id", $id);


            $this->display();
        }
    }

}
