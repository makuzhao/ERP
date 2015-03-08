<?php

/*
 * 后台 控制器
 */

namespace System\Controller;

use Common\Controller\AuthController;

class IndexController extends AuthController {

    public function index() {


        $sess = $_SESSION['auth'];
        $this->assign("sess", $sess);


        $this->display();
    }

    public function main() {

        /*
         *  显示当前多少个 boss 使用用本系统，其中，试用 ？个 ，收费 ？个
         * 
         */
        $probation = D('Probation');

        $prototal = $probation->count();
        $this->assign("prototal", $prototal);

        $free['isfree'] = "1";
        $freetotal = $probation->where($free)->count();
        $this->assign("freetotal", $freetotal);

        $toll['istoll'] = "0";
        $tolltotal = $probation->where($toll)->count();
        $this->assign("tolltotal", $tolltotal);




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
    }

    public function unsetSess() {
        $sess = $_SESSION['auth'];
        unset($sess);
        session("auth", NULL);
        /*
         *  测试结束后要注释下面的话
         */
        if ($sess == "") {
            $this->success("退出登录成功 ！！", U('Sale/Login/index'));
        }
    }

}
