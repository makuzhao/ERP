<?php

/*
 * 后台 控制器
 */

namespace Boss\Controller;

use Common\Controller\AuthController;
use Think\Model;

class IndexController extends AuthController {

    public function index() {


        $sess = $_SESSION['auth'];
        $this->assign("sess", $sess);

        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);
        $shop = D("Shop");
        $res = $shop->field("id,shop")->select();
        $this->assign("shop", $res);
        $this->assign("emp","<div style='width: 600px;height: 500px;background-color: red;'>还没有店铺</div>");


        $this->display();
    }

    public function main() {

        // 每个店铺  店名 收益 销售额 入库额

        /*
         * 今日收益 = 销售金额 sales - 出库金额 out  
         */

        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $shop = D("Shop")->field("id,shop")->select();
        $count = D("Shop")->count("id");

        $time = date("Y-m-d");
        $today = array();

        for ($i = 0; $i < $count; $i++) {

            $in = D($shop[$i]['id'] . "Sales");
            $inwhere['saledate'] = array("like", "$time%");
            $salesum = $in->where($inwhere)->sum("amount");
            //$this->assign($shop[$id]['id'] . "sales", $insum);

            $today[$i]['id'] = $shop[$i]['id'];
            $today[$i]['shop'] = $shop[$i]['shop'];
            $today[$i]['salesum'] = $salesum;

            $out = D($shop[$i]['id'] . "Storage");
            $outwhere['outtime'] = array("like", "$time%");
            $outsum = $out->where($outwhere)->sum("outsum");
            //$this->assign($shop[$id]['id'] . "out", $outsum);
            $today[$i]['outsum'] = $outsum;


            $daysum = $salesum - $outsum;
            if ($daysum > 0) {
                $this->assign($shop[$i]['id'] . "daysum", $daysum);
                $today[$i]['daysum'] = $daysum;
            } else {
                $str = "<font style='color: red'>$daysum </font>";
                $this->assign("daysum", $str);
                $today[$i]['daysum'] = $str;
            }
            $dayTotal = $dayTotal + $daysum;
        }
        $this->assign("dayTotal", $dayTotal);
        $this->assign("today", $today);

        /*
         * 近三天的收益
         */

        $three = date("Y-m-d", strtotime("-3 day")) . " 00:00:00";
        $now = date("Y-m-d H:i:s");
        $threeDay = array();

        for ($i = 0; $i < $count; $i++) {
            $in = D($shop[$i]["id"] . "Sales");
            $thinwhere['saledate'] = array(
                array('gt', $three),
                array('elt', $now)
            );
            $thsalesum = $in->where($thinwhere)->sum("amount");
            // $this->assign("thinsum", $thsalesum);
            $threeDay[$i]["thsalesum"] = $thsalesum;
            $threeDay[$i]["shop"] = $shop[$i]["shop"];

            $out = D($shop[$i]["id"] . "Storage");
            $thoutwhere['outtime'] = array(
                array('gt', $three),
                array('elt', $now),
            );
            $thoutsum = $out->where($thoutwhere)->sum("outsum");
            // $this->assign("thoutsum", $thoutsum);
            $threeDay[$i]["thoutsum"] = $thoutsum;

            $threesum = $thsalesum - $thoutsum;
            if ($threesum > 0) {
                //  $this->assign("threesum", $threesum);
                $threeDay[$i]["threesum"] = $threesum;
            } else {
                $str2 = "<font style='color: red'>$threesum  </font>";
                //$this->assign("threesum", $str2);
                $threeDay[$i]["threesum"] = $str2;
            }
            $threeTotal = $threeTotal + $threesum;
        }
        $this->assign("threeTotal", $threeTotal);
        $this->assign("threeDay", $threeDay);

        /*
         * 当前本月的收益
         */
        $moth = date("Y-m");
        $nowMoth = array();

        for ($i = 0; $i < $count; $i++) {
            $in = D($shop[$i]["id"] . "Sales");
            $minwhere['saledate'] = array("like", "$moth%");
            $msalesum = $in->where($minwhere)->sum("amount");
            // $this->assign("minsum", $minsum);
            $nowMoth[$i]["msalesum"] = $msalesum;
            $nowMoth[$i]["shop"] = $shop[$i]["shop"];

            $out = D($shop[$i]["id"] . "Storage");
            $moutwhere['outtime'] = array("like", "$moth%");
            $moutsum = $out->where($moutwhere)->sum("outsum");
            //  $this->assign("moutsum", $moutsum);
            $nowMoth[$i]["moutsum"] = $moutsum;


            $mothsum = $msalesum - $moutsum;
            if ($mothsum > 0) {
                $this->assign("mothsum", $mothsum);
                $nowMoth[$i]["mothsum"] = $mothsum;
            } else {
                $str = "<font style='color: red'>$mothsum </font>";
                $nowMoth[$i]["mothsum"] = $str;
            }
            $mothTotal = $mothTotal + $mothsum;
        }
        $this->assign("mothTotal", $mothTotal);

        $this->assign("nowMoth", $nowMoth);
        $this->display();
    }

    public function unsetSess() {
        $sess = $_SESSION['auth'];
        unset($sess);
        session("auth", NULL);
        session("dbName", NULL);
        session("uid", NULL);
        session("nameReal", NULL);
        session("storeId", NULL);
        session_destroy();
        /*
         *  测试结束后要注释下面的话
         */
        if ($sess == "") {
            $this->success("退出登录成功 ！！", U('Sale/Login/index'));
        }
    }

}
