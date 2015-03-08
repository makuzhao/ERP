<?php

/*
 * 
 * 生产商管理
 * 
 */

namespace Manager\Controller;

use Common\Controller\AuthController;

class CompanyController extends AuthController {
    /*
     * 显示所有商家
     */

    public function index() {
        $company = D("Company");
        $res = $company->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);

            parent::showPage("Company");
        }

        $total = $company->count("id");
        $this->assign("total", $total);


        $this->display();
    }

    /*
     * 添加商家     */

    public function add() {
        if (!IS_POST) {

            $this->display();
        } else {
            $company = D("Company");
            $data['company'] = trim($_POST['company']);
            $data['boss'] = trim($_POST['boss']);
            $data['bostel'] = trim($_POST['bostel']);

            $data['people'] = trim($_POST['people']);
            $data['peotel'] = trim($_POST['peotel']);

            $data['address'] = trim($_POST['address']);
            $data['tel'] = trim($_POST['tel']);

            $data['website'] = trim($_POST['website']);
            $data['info'] = trim($_POST['info']);


            $res = $company->add($data);
            if ($res) {
                $this->success("添加商家成功 ！！", U('Company/index'));
            } else {
                $this->error("添加商家失败 ？？");
            }
        }
    }

    /*
     * 更新商家
     */

    public function update() {
        $company = D("Company");

        if (!IS_POST) {
            $comid = $_GET['comid'];
            if (empty($comid)) {
                $this->error("非法操作！", U('Company/index'));
            } else {
                $where['id'] = $comid;
                $res = $company->where($where)->select();
                $this->assign("res", $res);
                $this->display();
            }
        } else {
            $where['id'] = $_POST['comid'];
            $data['company'] = trim($_POST['company']);
            $data['boss'] = trim($_POST['boss']);
            $data['bostel'] = trim($_POST['bostel']);

            $data['people'] = trim($_POST['people']);
            $data['peotel'] = trim($_POST['peotel']);

            $data['address'] = trim($_POST['address']);
            $data['tel'] = trim($_POST['tel']);

            $data['website'] = trim($_POST['website']);
            $data['info'] = trim($_POST['info']);

            $res = $company->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Company/index'));
            }
        }
    }

    /*
     * 删除商家
     */

    public function delete() {
        $comid = $_GET['comid'];
        if (empty($comid)) {
            $this->error("非法操作！", U('Company/index'));
        } else {
            $company = D("Company");
            $where['id'] = $comid;
            $res = $company->where($where)->delete();
            if ($res) {
                $this->success("删除成功", U('Company/index'));
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
        if (!IS_GET) {
            $this->display();
        } else {

            $com = trim($_GET['com']);
            if ($com != "") {

                $where['company'] = array('like', "%$com%");
                $this->assign("com", $com);
            }

            $bos = trim($_GET['bos']);
            if ($bos != "") {

                $where['boss'] = array('like', "%$bos%");
                $this->assign("bos", $bos);
            }

            $bostel = trim($_GET['bostel']);
            if ($bostel != "") {

                $where['bostel'] = array('like', "%$bostel%");
                $this->assign("bostel", $bostel);
            }

            $people = trim($_GET['people']);
            if ($people != "") {

                $where['people'] = array('like', "%$people%");
                $this->assign("peo", $people);
            }

            $peotel = trim($_GET['peotel']);
            if ($peotel != "") {

                $where['peotel'] = array('like', "%$peotel%");
                $this->assign("peotel", $peotel);
            }

            $addr = trim($_GET['addr']);
            if ($addr != "") {

                $where['address'] = array('like', "%$addr%");
                $this->assign("addr", $addr);
            }

            $tel = trim($_GET['tel']);
            if ($tel != "") {

                $where['tel'] = array('like', "%$tel%");
                $this->assign("tel", $tel);
            }

            $web = trim($_GET['web']);
            if ($web != "") {

                $where['website'] = array('like', "%$web%");
                $this->assign("web", $web);
            }

            $info = trim($_GET['info']);
            if ($info != "") {

                $where['info'] = array('like', "%$info%");
                $this->assign("info", $info);
            }



            parent::showPage("Company", "10", $where);

            /*
             * 统计商家数量
             */

            $total = D('Company')->where($where)->count("id");
            $this->assign("total", $total);

            $this->display();
        }
    }

}
