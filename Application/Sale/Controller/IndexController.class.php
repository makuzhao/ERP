<?php

/*
 * 后台 控制器
 */

namespace Sale\Controller;

use Common\Controller\AuthController;
use Think\Model;

class IndexController extends AuthController {

    public function index() {
        $model = new Model();
        $sql = "use " . $_SESSION['dbName'];
        $model->query($sql);


        $shop_id = $_SESSION['storeId'];
        $sess = $_SESSION['auth'];
        $this->assign("sess", $sess);


        $this->display();
    }

    public function main() {

        /*
         * 今日收益 = 销售金额 sales - 出库金额 out  
         */

        $model = new Model();
        $sql = "use " . $_SESSION['dbName'];
        $model->query($sql);


        $time = date("Y-m-d");

        $out = D('Out');

        $outwhere['outtime'] = array("like", "$time%");
        $outsum = $out->where($outwhere)->sum("outsum");
        $this->assign("outsum", $outsum);

        $in = D('Sales');
        $inwhere['saledate'] = array("like", "$time%");
        $insum = $in->where($inwhere)->sum("amount");
        $this->assign("insum", $insum);

        $daysum = $insum - $outsum;
        if ($daysum > 0) {
            $this->assign("daysum", $daysum);
        } else {
            $str = "<font style='color: red'>$daysum </font>";
            $this->assign("daysum", $str);
        }

        /*
         * 近三天的收益
         */

        $three = date("Y-m-d", strtotime("-3 day")) . " 00:00:00";
        $now = date("Y-m-d H:i:s");

        $out = D('Out');
        $thoutwhere['outtime'] = array(
            array('gt', $three),
            array('elt', $now),
        );
        $thoutsum = $out->where($thoutwhere)->sum("outsum");
        $this->assign("thoutsum", $thoutsum);

        $in = D('Sales');
        $thinwhere['saledate'] = array(
            array('gt', $three),
            array('elt', $now)
        );
        $thinsum = $in->where($thinwhere)->sum("amount");
        $this->assign("thinsum", $thinsum);

        $threesum = $thinsum - $thoutsum;
        if ($threesum > 0) {
            $this->assign("threesum", $threesum);
        } else {
            $str2 = "<font style='color: red'>$threesum  </font>";
            $this->assign("threesum", $str2);
        }

        /*
         * 当前本月的收益
         */
        $moth = date("Y-m");

        $out = D('Out');
        $moutwhere['outtime'] = array("like", "$moth%");
        $moutsum = $out->where($moutwhere)->sum("outsum");
        $this->assign("moutsum", $moutsum);

        $in = D('Sale');
        $minwhere['saledate'] = array("like", "$moth%");
        $minsum = $in->where($minwhere)->sum("amount");
        $this->assign("minsum", $minsum);

        $mothsum = $minsum - $moutsum;
        if ($mothsum > 0) {
            $this->assign("mothsum", $mothsum);
        } else {
            $str = "<font style='color: red'>$mothsum </font>";
            $this->assign("mothsum", $str);
        }

        $this->display();

        /*
         * 
         * 


          $year = date("Y");
          $month = date("m");
          $day = date('w');
          $nowMonthDay = date("t");
          $firstday = date('d') - $day;
          if (substr($firstday, 0, 1) == "-") {
          $firstMonth = $month - 1;
          $lastMonthDay = date("t", $firstMonth);
          $firstday = $lastMonthDay - substr($firstday, 1);
          $time_1 = strtotime($year . "-" . $firstMonth . "-" . $firstday);
          } else {
          $time_1 = strtotime($year . "-" . $month . "-" . $firstday);
          }

          $lastday = date('d') + (7 - $day);
          if ($lastday > $nowMonthDay) {
          $lastday = $lastday - $nowMonthDay;
          $lastMonth = $month + 1;
          $time_2 = strtotime($year . "-" . $lastMonth . "-" . $lastday);
          } else {
          $time_2 = strtotime($year . "-" . $month . "-" . $lastday);
          }

          echo date('Y:m:d', $time_1); //一周开始的日期
          echo '<br/>';
          echo date('Y:m:d', $time_2); //一周结束的日期



         * 
         * 
         */
    }

    public function unsetSess() {
        $sess = $_SESSION['auth'];
        unset($sess);
        session("auth", NULL);
        session("uid", NULL);
        session("dbName", NULL);
        session('nameReal', null);

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
