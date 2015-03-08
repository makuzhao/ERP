<?php

/*
 *  用户管理
 */

namespace Home\Controller;

use Common\Controller\AuthController;
use Think\Model;
use Think\Controller;
use Think\Page;

class UserController extends AuthController {
    /*
     * 显示用户的所有信息
     */

    public function index() {
        $user = D("User");
        $res = $user->relation(true)->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);
//var_dump($res);
            parent::relationPage("User");
        }


        $this->display();
    }

    /*
     * 添加用户
     */

    public function add() {
        if (!IS_POST) {
            $this->usGroup();
            $this->display();
        } else {
            $user = D("User");
            $data['name'] = $_POST['name'];
            $data['pwd'] = C('AUTHBEFORE') . trim($_POST['pwd']) . C('AUTHEND');
            $data['regtime'] = date("Y-m-d H:i:s");
            $data['Group'] = array(
                'id' => $_POST['group_id']
            );
            $res = $user->relation(true)->add($data);
            if ($res) {
                $this->success("添加用户成功 ！！", U('User/index'));
            } else {
                $this->error("添加用户失败 ？？");
            }

// print_r($res);
        }
    }

    /*
     * 用户组
     */

    public function usGroup() {
        $ug = D("Group");
        $ug = $ug->getField("id,title,rules");
        $this->assign("ug", $ug);
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

                $res = $user->relation(true)->where($where)->select();
// var_dump($res);
                $this->assign("us", $res);
                $this->usGroup();

                $this->display();
            }
        } else {
            $where['id'] = $_POST['uid'];
            $data['name'] = $_POST['name'];
            $data['pwd'] = C('AUTHBEFORE') . trim($_POST['pwd']) . C('AUTHEND');
            $data['Group'] = array(
                'id' => $_POST['group_id']
            );



            $res = $user->relation(true)->where($where)->save($data);
            if ($res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('User/index'));
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
        if (!IS_GET) {
            $this->display();
        } else {
            $str = $_GET['search'];
            $where['name'] = array('like', "%$str%");
            $where['pwd'] = array('like', "%$str%");
            //$where['regtime'] = array('like', "%$str%");
            //$where['latetime'] = array('like', "%$str%");
            $where['_logic'] = 'or';

            $this->assign("replace", "<font color='red'>$str</font>");
            $this->assign("str", $str);

            parent::showPage("User", "10", $where);

            $this->display();
        }
    }

}
