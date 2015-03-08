<?php

/*
 * 
 * 零售商管理
 * 
 */

namespace Home\Controller;

use Common\Controller\AuthController;

class ShopController extends AuthController {
    /*
     * 显示所有商家
     */

    public function index() {
        $company = D("Shop");
        $res = $company->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);

            parent::showPage("Shop");
        }


        $this->display();
    }

    /*
     * 添加商家     */

    public function add() {
        if (!IS_POST) {

            $this->display();
        } else {
            $company = D("Shop");
            $data['company'] = $_POST['company'];
            $data['address'] = $_POST['address'];
            $data['boss'] = $_POST['boss'];
            $data['tel'] = $_POST['tel'];


            $res = $company->add($data);
            if ($res) {
                $this->success("添加商家成功 ！！", U('Shop/index'));
            } else {
                $this->error("添加商家失败 ？？");
            }
        }
    }

    /*
     * 更新商家
     */

    public function update() {
        $company = D("Shop");

        if (!IS_POST) {
            $comid = $_GET['comid'];
            if (empty($comid)) {
                $this->error("非法操作！", U('Shop/index'));
            } else {
                $where['id'] = $comid;
                $res = $company->where($where)->select();
                $this->assign("res", $res);
                $this->display();
            }
        } else {
            $where['id'] = $_POST['comid'];
            $data['company'] = $_POST['company'];
            $data['address'] = $_POST['address'];
            $data['boss'] = $_POST['boss'];
            $data['tel'] = $_POST['tel'];

            $res = $company->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Shop/index'));
            }
        }
    }

    /*
     * 删除商家
     */

    public function delete() {
        $comid = $_GET['comid'];
        if (empty($comid)) {
            $this->error("非法操作！", U('Shop/index'));
        } else {
            $company = D("Shop");
            $where['id'] = $comid;
            $res = $company->where($where)->delete();
            if ($res) {
                $this->success("删除成功", U('Shop/index'));
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
            $str = $_GET['search'];
            $where['company'] = array('like', "%$str%");
            $where['address'] = array('like', "%$str%");
            $where['boss'] = array('like', "%$str%");
            $where['tel'] = array('like', "%$str%");
            $where['_logic'] = 'or';


            $this->assign("replace", "<font color='red'>$str</font>");
            $this->assign("str", $str);

            parent::showPage("Shop", "10", $where);

            $this->display();
        }
    }

}
