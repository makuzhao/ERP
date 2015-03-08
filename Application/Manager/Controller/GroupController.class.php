<?php

/*
 *  用户组管理
 */

namespace Manager\Controller;

use Common\Controller\AuthController;

class GroupController extends AuthController {
    /*
     * 显示所有组信息
     */

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

    private function rule() {
        $rule = D("Rule");
        /*
         * 系统
         */
        $sys = $rule->field("id,title")->where("title like  '系统%'")->select();
        $this->assign("sys", $sys);
        /*
         * 老板
         */
        $boss = $rule->field("id,title")->where("title like  '老板%'")->select();
        $this->assign("boss", $boss);
        /*
         * 销售员
         */
        $salesman = $rule->field("id,title")->where("title like  '销售员%'")->select();
        $this->assign("salesman", $salesman);
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

                $res = $group->field("id,title")->where($where)->select();
                // print_r($res);
                $this->assign("group", $res);

                $this->papa($res[0]['title']);

                /*
                 * 拆分规则字符串成数组
                 */


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
     * 已分配、未分配的权限进行分离
     */

    public function papa($title) {
        $title = trim($title);
        $rule = D("Rule");
        $group = D('Group');
        /*
         * 系统 查询值tb_group 、tb_rule
         */
        /*
         * 查询分组拥有的权限
         */
        $sysg = array();
        $sg = $group->field("rules")->where("title ='$title'")->find();
        $sysg = explode(",", $sg['rules']);
        sort($sysg);

        /*
         * 系统 权限
         */
        $sys = $rule->field("id,title")->where("title like  '系统%'")->select();
        $syscount = $rule->where("title like  '系统%'")->count("id");
        for ($s = 0; $s < $syscount; $s++) {


            if (in_array($sys[$s]['id'], $sysg)) {
                /*
                 * 已分配
                 */
                $sysHave[$s]['id'] = $sys[$s]['id'];
                $sysHave[$s]['title'] = $sys[$s]['title'];
                // $this->assign("sysHave", $sysHave);
            } else {
                /*
                 * 未分配
                 */
                $sysNo[$s]['id'] = $sys[$s]['id'];
                $sysNo[$s]['title'] = $sys[$s]['title'];
            }
        }
        $this->assign("sysHave", $sysHave);
        $this->assign("sysNo", $sysNo);


        /*
         * 老板
         */
        $boss = $rule->field("id,title")->where("title like  '老板%'")->select();
        $boscount = $rule->where("title like  '老板%'")->count("id");

        for ($b = 0; $b < $boscount; $b++) {

            if (in_array($boss[$b]['id'], $sysg)) {
                /*
                 * 已分配
                 */
                $bosHave[$b]['id'] = $boss[$b]['id'];
                $bosHave[$b]['title'] = $boss[$b]['title'];
            } else {
                /*
                 * 未分配
                 */
                $bosNo[$b]['id'] = $boss[$b]['id'];
                $bosNo[$b]['title'] = $boss[$b]['title'];
            }
        }
        $this->assign("bosHave", $bosHave);
        $this->assign("bosNo", $bosNo);


        /*
         * 销售员
         */
        $salesman = $rule->field("id,title")->where("title like  '销售员%'")->select();
        $salecount = $rule->where("title like  '销售员%'")->count("id");

        for ($sale = 0; $sale < $salecount; $sale++) {

            if (in_array($salesman[$sale]['id'], $sysg)) {
                /*
                 * 已分配
                 */
                $saleHave[$sale]['id'] = $salesman[$sale]['id'];
                $saleHave[$sale]['title'] = $salesman[$sale]['title'];
                //  $this->assign("saleHave", $saleHave);
            } else {
                /*
                 * 未分配
                 */
                $saleNo[$sale]['id'] = $salesman[$sale]['id'];
                $saleNo[$sale]['title'] = $salesman[$sale]['title'];
                //   $this->assign("bosNo", $saleNo);
            }
        }
        $this->assign("saleHave", $saleHave);
        $this->assign("saleNo", $saleNo);
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
