<?php

/*
 *  用户管理
 */

namespace Boss\Controller;

use Common\Controller\AuthController;
use Think\Model;
use Think\Controller;
use Think\Page;
use Boss\Model\UserModel;

class UserController extends AuthController {
    /*
     * 显示当前boss的所有销售员的信息
     */

    public function index() {

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

        $where['belong'] = $_SESSION['auth'];

        $where['shop_id'] = $shop_id;

        parent::relationPage("User", "10", $where);

        $user = D('User');
        $id = $user->where($where)->count("id");
        $this->assign("id", $id);

        $this->display();
    }

    /*
     * 添加用户 初始使用的权限
     */

    public function add() {


        if (!IS_POST) {
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
            $this->usGroup();
            $this->display();
        } else {

            $shop_id = trim($_POST['shopid']);

            $user = D("User");
            $data['name'] = trim($_POST['name']);
            $data['pwd'] = C('AUTHBEFORE') . trim($_POST['xcpwd']) . C('AUTHEND');
            $data['IDcard'] = trim($_POST['card']);
            $data['realName'] = trim($_POST['real']);
            $data['tel'] = trim($_POST['tel']);
            $data['email'] = trim($_POST['email']);
            $data['info'] = trim($_POST['info']);
            $data['status'] = "1";
            $data['belong'] = $_SESSION['auth'];
            $data['redirect'] = "Sale";
            $data['regtime'] = date("Y-m-d H:i:s");
            $data['Group'] = array(
                'id' => $_POST['group_id']
            );

            $data['shop_id'] = $shop_id;

            $res = $user->relation(true)->add($data);
            if ($res) {
                $this->success("添加销售员成功 ！！", U('User/index', array('shopid' => $shop_id)));
            } else {
                $this->error("添加销售员失败 ？？");
            }

// print_r($res);
        }
    }

    /*
     * ajax() 检验用户唯一性
     */

    public function ajax() {
        $name = trim($_POST['name']);
        // $name = iconv("gbk", "UTF-8", $name);


        if (!empty($name)) {
            $where['name'] = $name;
            $us = D('User');
            $re = $us->field("id,name")->where($where)->select();
            if ($re) {
                echo "<font style='color:red;padding-left: 20px;'>$name 不可用</font>";
            } else {
                echo "<font style='padding-left: 20px;'>$name 可用</font>";
            }
        }
    }

    /*
     * 用户组
     */

    private function usGroup() {
        $ug = D("Group");
        $us = $ug->field("id,hint")->where("`title` = 'Salesman'")->select();
        $this->assign("ug", $us);
// var_dump($ug);
    }

    /*
     * 更新用户信息
     */

    public function update() {
        $user = D("User");

        if (!IS_POST) {
            $uid = $_GET['uid'];

            if (empty($uid)) {
                $this->error("非法操作！", U('User/index'));
            } else {
                $where['id'] = $uid;


                $shopName = $_SESSION["shop_name"];
                $this->assign("shopName", $shopName);

                $res = $user->relation(true)->where($where)->select();
// var_dump($res);
                $this->assign("us", $res);

                $this->usGroup();

                $this->display();
            }
        } else {
            $shop_id = $_POST['shopid'];


            $where['id'] = $_POST['uid'];
            $data['name'] = trim($_POST['name']);
            $data['pwd'] = C('AUTHBEFORE') . trim($_POST['xcpwd']) . C('AUTHEND');

            $data['IDcard'] = trim($_POST['card']);
            $data['realName'] = trim($_POST['real']);

            $data['tel'] = trim($_POST['tel']);
            $data['email'] = trim($_POST['email']);
            $data['info'] = trim($_POST['info']);
            $data['status'] = trim($_POST['status']);

            $res = $user->relation(true)->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('User/index', array('shopid' => $shop_id)));
            }
        }
    }

    /*
     * 删除用户
     */

    public function delete() {
        $uid = $_GET['uid'];
        if (empty($uid)) {
            $this->error("非法操作！", U('User/index'));
        } else {
            $user = D("User");
            $where['id'] = $uid;
            $res = $user->where($where)->relation(true)->delete();
            if ($res) {
                $this->success("删除成功", U('User/index'));
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /*
     * 搜索 searching（）
     * 
     * $str  关键字
     * 
     */

    public function searching() {
        if (!IS_POST) {



            $this->display();
        } else {
            $name = trim($_POST['name']);
            if ($name != "") {

                $where['name'] = array('like', "%$name%");
                $this->assign("name", $name);
            }

            $tel = trim($_POST['tel']);
            if ($tel != "") {

                $where['tel'] = array('like', "%$tel%");
                $this->assign("tel", $tel);
            }
            $real = trim($_POST['real']);
            if ($real != "") {

                $where['realName'] = array('like', "%$real%");
                $this->assign("real", $real);
            }

            $card = trim($_POST['card']);
            if ($card != "") {

                $where['IDcard'] = array('like', "%$card%");
                $this->assign("card", $card);
            }

            $email = trim($_POST['email']);
            if ($email != "") {

                $where['tel'] = array('like', "%$email%");
                $this->assign("email", $email);
            }

            $date = trim($_POST['date']);
            if ($date != "") {

                $where['latetime'] = array('like', "%$date%");
                $this->assign("date", $date);
            }

            $status = trim($_POST['status']);
            if ($status != "") {

                $where['status'] = array('like', "%$status%");
                $this->assign("status", $status);
            }




            $where['belong'] = $_SESSION['auth'];
            $shop_id = trim($_POST['shopid']);
            $where['shop_id'] = $shop_id;
            $this->assign("shopid", U('User/index', array('shopid' => $shop_id)));


            parent::showPage("User", "10", $where);

            $user = D('User');
            $id = $user->where($where)->count("id");
            $this->assign("id", $id);


            $shopName = $_SESSION["shop_name"];
            $this->assign("shopName", $shopName);

            $this->display();
        }
    }

}
