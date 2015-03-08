<?php

/*
 *  用户组管理
 */

namespace Home\Controller;

use Common\Controller\AuthController;

class GroupController extends AuthController {
    /*
     * 显示所有组信息
     */

    private $group;

    public function index() {
        $group = D("Group");
        $res = $group->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);
            parent::showPage("Group");
        }

        $this->display();
    }

    /*
     * 添加用户组
     * 用户名、单选的状态、多选的权限
     */

    public function add() {

        if (!IS_POST) {
            $this->rule();
            $this->display();
        } else {
            $group = D("Group");
            $data['title'] = $_POST['title'];
            $data['status'] = $_POST['status'];

            $data['rules'] = implode(",", $rules = $_POST['rules']);
            $res = $group->data($data)->add();
            if ($res == "") {
                $this->error("用户组添加失败");
            } else {
                $this->success("数据更新成功 ！！", U('Group/index'));
            }
        }
    }

    /*
     * 规则表的规则信息
     */

    public function rule() {
        $rule = D("Rule");
        $res = $rule->getField("id,name,title");
        // $res = $group->select();
        //print_r($res);
        $this->assign("res", $res);
    }

    /*
     * 更新用户组信息
     */

    public function update() {
        $group = D("Group");
        if (!IS_POST) {
            $gid = $_GET['gid'];
            if (empty($gid)) {
                $this->error("非法操作！", U('Group/index'));
            } else {
                $where['id'] = $gid;

                $res = $group->where($where)->select();
                // print_r($res);
                $this->assign("group", $res);
                $this->rule();

                $this->display();
            }
        } else {
            $where['id'] = $_POST['gid'];
            $data['title'] = $_POST['title'];
            $data['status'] = $_POST['status'];

            $data['rules'] = implode(",", $rules = $_POST['rules']);
            $res = $group->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Group/index'));
            }
        }
    }

    /*
     * 删除用户组
     */

    public function delete() {
        $gid = $_GET['gid'];
        if (empty($gid)) {
            $this->error("非法操作！", U('Group/index'));
        } else {
            $group = D("Group");
            $where['id'] = $gid;
            $res = $group->where($where)->delete();
            if ($res) {
                $this->success("删除成功");
            } else {
                $this->error("删除失败！");
            }
        }
    }

}
