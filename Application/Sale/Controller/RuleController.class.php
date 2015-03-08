<?php

/*
 *  规则管理
 */

namespace Home\Controller;

use Common\Controller\AuthController;

class RuleController extends AuthController {
    /*
     * 显示规则信息
     */

    public function index() {
        $rule = D("Rule");
        $res = $rule->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);
            parent::showPage("Rule", "10");
        }

        $this->display();
    }

    /*
     * 添加规则
     * 
     */

    public function add() {

        if (!IS_POST) {

            $this->display();
        } else {
            $rule = D("Rule");
            $data['name'] = $_POST['name'];
            $data['title'] = $_POST['title'];
            $data['type'] = $_POST['type'];
            $data['status'] = $_POST['status'];
            $data['condition'] = $_POST['condition'];

            $res = $rule->data($data)->add();

            if ($res) {
                $this->success("数据更新成功 ！！", U('Rule/index'));
                $this->error("规则添加失败");
            } else {
                $this->error("规则添加失败");
            }
        }
    }

    /*
     * 更新规则信息
     */

    public function update() {
        $rule = D("Rule");
        if (!IS_POST) {
            $rid = $_GET['rid'];
            if (empty($rid)) {
                $this->error("非法操作！", U('Rule/index'));
            } else {
                $where['id'] = $rid;

                $res = $rule->where($where)->select();
                // print_r($res);
                $this->assign("rule", $res);


                $this->display();
            }
        } else {
            $where['id'] = $_POST['rid'];
            $data['name'] = $_POST['name'];
            $data['title'] = $_POST['title'];
            $data['type'] = $_POST['type'];
            $data['status'] = $_POST['status'];
            $data['condition'] = $_POST['condition'];
            $res = $rule->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Rule/index'));
            }
        }
    }

    /*
     * 删除规则
     */

    public function delete() {
        $rid = $_GET['rid'];
        if (empty($rid)) {
            $this->error("非法操作！", U('Rule/index'));
        } else {
            $rule = D("Rule");
            $where['id'] = $rid;
            $res = $rule->where($where)->delete();
            if ($res) {
                $this->success("删除成功");
            } else {
                $this->error("删除失败！");
            }
        }
    }

}
