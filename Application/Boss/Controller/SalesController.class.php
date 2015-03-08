<?php

namespace Boss\Controller;

use Common\Controller\AuthController;
use Think\Model;

/*
 * 
 * 商品销售 ： 查询、查看、 添加、 删除 
 * 
 * 
 */

class SalesController extends AuthController {
    /*
     * 显示所有销出的商品
     */

    public function index() {
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $shop_id = $_GET['shopid'];

        if ($shop_id == "") {
            $shop_id = $_SESSION["shop_id"];
        }
        if ($shop_id == "") {
            $this->error("请先点击具体的店铺，再回来点此");
        }
        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);

        $out = D($shop_id . "Sales");


        $res = $out->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("shopid", $shop_id);
            $this->assign("res", $res);

            parent::showPage($shop_id . "Sales", "10");
        }
        /*
         * 金额
         */
        $total = $out->where($where)->sum("amount");
        $this->assign("total", $total);
        /*
         * 数量
         */
        $num = $out->where($where)->sum("num");
        $this->assign("num", $num);
        /*
         * 单子数
         */
        $id = $out->where($where)->count("id");
        $this->assign("id", $id);

        $this->display();
    }

    /*
     *  look()  查看商品的具体信息
     * 
     *  1、 根据get传来的 barcode 查库
     *  
     *  2、 信息显示在弹出层上
     */

    public function look() {
        $id = $_GET['id'];
        $where['barcode'] = $id;
        $goods = D('Goods');
        $res = $goods->where($where)->select();
        if (!$res) {
            $this->error("不存在这个条形码 ？？");
        } else {
            $this->assign("look", $res);
        }
        $this->display();
    }

    /*
     * add() 添加销售的商品    同时修改 tb_out 表中的总的数量相减   注意 POST GET 前后判断顺序 的干扰
     * 
     * 1、接收 ajax get方式传来的条形码 barcode
     * 
     * 2、根据 barcode 提取 预售价表 tb_presell  表中的 所有数据   
     * 
     *          
     *    如果条形码不存在，说明找不到该商品，提示去上架该商品
     *    
     *    如果找到商品，在特定的位置输出对应的值
     * 
     * 3、提交数据
     * 
     * 4、获取 barcode  num 这两个值，
     * 
     * 5、根据 barcode outamount>0 这两个条件去查询 tb_out ，ID asc 排列 ,获得一条数据（多条数据最早的一条）中的 id outamount
     * 
     * 6、（outamount - num = ?）  update 字段 outamount 的值 where id = （5中的结果集 id）
     * 
     * 7、如果6、更新成功，则添加所有提交的数据到 tb_sales 表中,附加条件：tb_out 中的id 也要入库 到 out_id 字段
     * 
     * 8、购物车功能，只有按了返回按钮才能返回 index 页面，否则就是 add 成功也是跳转到 add 页面，只有按了结算按钮才能数据循环插入表
     * 
     */

    public function add() {
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $shop_id = $_GET['shopid'];
        if ($shop_id == "") {
            $shop_id = $_SESSION["shop_id"];
        }
        if ($shop_id == "") {
            $this->error("请先点击具体的店铺，再回来点此！");
        }
        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);

        $this->assign("shopid", $shop_id);

        $out = D($shop_id . "Sales");
        /*
         * 本店今日营业额
         */
        $s = D($shop_id . "Sales");
        $day = date("Y-m-d");
        $wh['saledate'] = array("like", "%$day%");
        $total = $s->where($wh)->sum("amount");
        $this->assign("total", $total);


        $this->display();
    }

    public function shows() {


        /*
         * 1、根据 barcode outamount>0 查 tb_out 表的 id outamount 
         * 
         * 2、相减
         * 
         * 3、插入 tb_sales 数据
         * 
         * 4、更新 tb_out 数据
         * 
         * 5、弹出一个页面 显示 商品的单笔信息，今日收益
         */

        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $shop_id = $_GET['shopid'];
        if ($shop_id == "") {
            $shop_id = $_SESSION["shop_id"];
        }
        if ($shop_id == "") {
            $this->error("请先点击具体的店铺，再回来点此！");
        }
        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);
        $this->assign("shopid", $shop_id);

        $out = D($shop_id . "Storage");

        $barcode = $_POST['barcode'];
        if ($barcode == "") {
            $this->error("操作失误");
        } else {
            $salenum = $_POST['num'];

            /*
             *  获取提交的数据
             */
            $goods = D($shop_id . 'Goods');
            $letbar = strtoupper($barcode);
            $gg['barcode'] = $barcode;

            $pro = $goods->field("id,barcode,product,letter")->where($gg)->find();
            $ou = $out->field('outpresell')->where($gg)->find();
            $barcode = $pro['barcode'];
            $data['product'] = $pro['product'];
            $data['barcode'] = $pro['barcode'];
            $data['letter'] = $pro['letter'];
            $data['num'] = $salenum;
            $data['presell'] = $ou['outpresell'];
            $data['amount'] = $salenum * $ou['outpresell'];
            $data['salesman'] = $_SESSION['nameReal'];
            $data['saledate'] = date("Y-m-d H:i:s");


            $this->assign("pro", $pro['product']);
            $this->assign("num", $salenum);
            $this->assign("pre", $ou['outpresell']);
            $this->assign("sum", $ou['outpresell'] * $salenum);



            $barout['barcode'] = $barcode;
            $barout['outamount'] = array('gt', 0);
            $resout = $out->field("id,outamount")->where($barout)->order('id asc')->find();
            if ($resout == "") {
                $this->error("库存不足");
            } else {


                /*
                 *  根据 销售数量 循环满足条件查询 更新 outamount
                 */
                for ($i = 0; $i < $salenum; $i++) {
                    $resout = $out->field("id,outamount")->where($barout)->order('id asc')->find();

                    $where['id'] = $resout['id'];
                    $outdata['outamount'] = $resout['outamount'] - 1;
// exit();
                    $res = $out->where($where)->save($outdata);
                    if (!$res) {
                        $this->error("更新失败");
                    }
                }

                /*
                 *   插入 tb_sales 的每条数据要知道从那张单子减出来的
                 */

                $resid = $out->field("id")->where($barout)->order('id asc')->find();
                $data['out_id'] = $resid['id'];

                $sales = D($shop_id . 'Sales');
                $ressale = $sales->add($data);
                if (!$ressale) {
                    $this->error("该商品销售失败");
                    return FALSE;
                }

                /*
                 * 特定销售员的今日销售额 模糊查询
                 * 
                 * $today
                 * $salesman
                 * $saletotal
                 * 
                 */
                $today = date("Y-m-d");
                $sawhere['saledate'] = array("like", "%$today%");
                $sawhere['salesman'] = $_SESSION['nameReal'];
                $pretotal = $sales->where($sawhere)->sum("amount");
                $this->assign("pretotal", $pretotal);
                $this->assign("real", $_SESSION['nameReal']);

                $this->display();
            }
        }
    }

    /*
     * ajaxId ()  查询库存量
     *  
     * 1、获取 get 传来的id 
     * 
     * 2、根据 id 查询 inamount
     * 
     * 3、指定位置输出
     * 
     */

    public function ajaxId() {
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $shop_id = $_GET['shopid'];
        if ($shop_id == "") {
            $shop_id = $_SESSION["shop_id"];
        }
        if ($shop_id == "") {
            $this->error("请先点击具体的店铺，再回来点此！");
        }
        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);

        /*
         *  关键字：条形码、商品大写首字母
         */
        if ($_POST['code'] != "") {
//$_SESSION['barsess'] = time();
            $barcode = $_POST['code'];

            $presell = D($shop_id . "Storage");
            $pre['barcode'] = array("like", "%$barcode%");
            $s = strtoupper($barcode);
            $pre['letter'] = array("like", "%$s%");
            $pre['_logic'] = "OR";
            $bar = $presell->where($pre)->order('id desc')->find();

            $barcode = $bar['barcode'];
            $barpro = $bar['product'];

            $baramount = $presell->where($pre)->sum("outamount");
            $barid = $bar['id'];
            $barnum = 1;   // 预售数量
            $barpre = $bar['outpresell'];  // 预售价

            $barsale = $_SESSION['n'];

            if ($bar) {
                $this->assign("code", $barcode);
                $this->assign("product", $barpro);
                $this->assign("sum", $baramount);
                $this->assign("pre", $barpre);
                $this->assign("num", $barnum);

                $s = D($shop_id . "Sales");
                $day = date("Y-m-d");
                $wh['saledate'] = array("like", "%$day%");
                $total = $s->where($wh)->sum("amount");
                $this->assign("total", $total);

                $this->assign("shopid", $shop_id);
                $this->display();
            } else {
                $this->error("没有该商品");
            }
        }
    }

    /*
     * 更新
     *

      public function update() {
      $company = D("Sales");

      if (!IS_POST) {
      $comid = $_GET['outid'];
      if (empty($comid)) {
      $this->error("非法操作！", U('Sales/index'));
      } else {
      $where['id'] = $comid;
      $res = $company->where($where)->select();
      $this->assign("res", $res);
      $this->display();
      }
      } else {
      $where['id'] = $_POST['comid'];
      $data['barcode'] = $_POST['barcode'];
      $data['num'] = $_POST['num'];
      $data['presell'] = $_POST['presell'];

      $res = $company->where($where)->save($data);
      if (!$res) {
      $this->error("数据更新失败 ？？");
      } else {
      $this->success("数据更新成功 ！！", U('Sales/index'));
      }
      }
      }

     * 
     * 
     * 删除出库商品
     *
     * 


      public function delete() {
      $outid = $_GET['outid'];
      if (empty($outid)) {
      $this->error("非法操作！", U('Sales /index'));
      } else {
      $out = D("Sales");
      $where['id'] = $outid;
      $res = $out->where($where)->delete();
      if ($res) {
      $this->success("删除成功", U('Sales/index'));
      } else {
      $this->error("删除失败！");
      }
      }
      }
     */
    /*
     * 用户搜索 searching（）
     * 
     * $str  关键字
     * 
     */

    public function searching() {

        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);



        if (!IS_POST) {
            $this->display();
        } else {
            $bar = trim($_POST['barcode']);
            if ($bar != "") {

                $where['barcode'] = array('like', "%$bar%");
                $this->assign("bar", $bar);
            }

            $pro = trim($_POST['product']);
            if ($pro != "") {

                $where['product'] = array('like', "%$pro%");
                $this->assign("pro", $pro);
            }

            $let = trim($_POST['letter']);
            if ($let != "") {
                $let = strtoupper($let);
                $where['letter'] = array('like', "%$let%");
                $this->assign("let", $let);
            }

            $amount = trim($_POST['outamount']);
            if ($amount != "") {

                $where['num'] = array('like', "%$amount%");
                $this->assign("num", $amount);
            }

            $price = trim($_POST['outprice']);
            if ($price != "") {

                $where['presell'] = array('like', "%$price%");
                $this->assign("pre", $price);
            }

            $sum = trim($_POST['outsum']);
            if ($sum != "") {

                $where['amount'] = array('like', "%$sum%");
                $this->assign("amount", $sum);
            }

            $people = trim($_POST['people']);
            if ($people != "") {

                $where['salesman'] = array('like', "%$people%");
                $this->assign("sale", $people);
            }

            $start = trim($_POST['start']);

            $end = trim($_POST['end']);
            if ($end != "") {

                $where['saledate'] = array('between', array($start . " 00:00:00", $end . " 59:59:59"));
                $this->assign("date", $people);
            }
            $shop_id = $_POST['shopid'];

            if ($shop_id == "") {
                $shop_id = $_SESSION["shop_id"];
            }
            if ($shop_id == "") {
                $this->error("非法操作！");
            }
            $shopName = $_SESSION["shop_name"];
            $this->assign("shopName", $shopName);

            $this->assign("shopid", U('Sales/index', array('shopid' => $shop_id)));


            parent::showPage($shop_id . "Sales", "10", $where);
            $out = D($shop_id . 'Sales');

            /*
             * 金额
             */
            $total = $out->where($where)->sum("amount");
            $this->assign("total", $total);
            /*
             * 数量
             */
            $num = $out->where($where)->sum("num");
            $this->assign("sanum", $num);
            /*
             * 单子数
             */
            $id = $out->where($where)->count("id");
            $this->assign("id", $id);

            $this->display();
        }
    }

}
