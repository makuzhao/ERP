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
         * 今日收益 = 出库金额 - 入库金额
         */

        $time = "2014-10-14";

        $out = D('Out');
        $outwhere['outtime'] = array("like", "$time%");
        $outsum = $out->where($outwhere)->sum("outsum");
        $this->assign("outsum", $outsum);

        $in = D('In');
        $inwhere['intime'] = array("like", "$time%");
        $insum = $in->where($inwhere)->sum("insum");
        $this->assign("insum", $insum);

        $daysum = $outsum - $insum;
        if ($daysum > 0) {
            $this->assign("daysum", $daysum);
        } else {
            $str = "<font style='color: red'>$daysum </font>";
            $this->assign("daysum", $str);
        }

        /*
         * 近三天的收益
         */

        $three = date("Y-m-d", strtotime("-1 day"));

        $out = D('Out');
        $thoutwhere['outtime'] = array("like", "$three%");
        $thoutsum = $out->where($thoutwhere)->sum("outsum");
        $this->assign("thoutsum", $thoutsum);

        $in = D('In');
        $thinwhere['intime'] = array("like", "$three%");
        $thinsum = $in->where($thinwhere)->sum("insum");
        $this->assign("thinsum", $thinsum);

        $threesum = $thoutsum - $thinsum;
        if ($threesum > 0) {
            $this->assign("threesum", $threesum);
        } else {
            $str2 = "<font style='color: red'>$threesum  </font>";
            $this->assign("threesum", $str2);
        }


        $this->display();
    }

    public function unsetSess() {
        $sess = $_SESSION['auth'];
        unset($sess);
        /*
         *  测试结束后要注释下面的话
         */
        if ($sess == "") {
            $this->success("退出登录", U('Login/index'));
        }
    }

}
