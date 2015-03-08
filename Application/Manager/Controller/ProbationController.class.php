<?php

/*
 * 审核收费管理
 * 
 */

namespace Manager\Controller;

use Common\Controller\AuthController;
use Think\Page;

class ProbationController extends AuthController {
    /*
     *  审核试用 
     *  1、先判断用户是否到期，如果到期，直接自动禁用用户
     * 
     *  2、显示不过期的用户信息
     */

    public function freeIndex() {
        /*
         * 判断到期的用户
         * 
         */
        $today = date("Y-m-d");
        $end = D("Probation")->field("id,name,isfree,freeend")->where("freedate > $today")->select();
        $num = D("Probation")->field("id")->where("freedate > $today")->count();

        for ($i = 0; $i < $num; $i++) {
            if ($end[$i]['isfree'] == "1") {
                $result = strtotime($end[$i]['freeend']) - strtotime($today);
                if ($result <= 0) {
                    $data['isfree'] = "0";
                    $data['freedate'] = "";
                    $data['freemonth'] = "";
                    $data['freeend'] = "";
                    $wh['id'] = $end[$i]['id'];
                    D("Probation")->where($wh)->save($data);
                    /*
                     * 禁用Boss时，同时禁用Boss下的Salesman  name -> belong ->id ->status (for update)
                     * 
                     */
                    $namewh['belong'] = $end[$i]['name'];
                    $name = D("User")->field("id,status")->where($namewh)->select();
                    $namecount = D("User")->field("id")->where($namewh)->count();
                    for ($j = 0; $j < $namecount; $j++) {
                        $da['status'] = "0";
                        $whus['id'] = $namewh[$j]['id'];
                        D("User")->where($whus)->save($da);
                    }
                }
            }
        }


        /*
         * 显示用户试用信息
         */
        $where = "";
        $everypage = "10";
        $order = "isfree desc,freeend asc";
        $tbName = D("Probation"); // 实例化Data数据对象
        $count = $tbName->where($where)->count(); // 查询满足要求的总记录数

        $Page = new Page($count, $everypage); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        /*
          foreach ($where as $key => $val) {

          $p->parameter = "";
          }
         */
        $list = $tbName->where($where)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();



        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        /*
         * 总计
         * 
         */
        $total = D("Probation")->count();
        $this->assign("total", $total);

        $this->display();
    }

    public function freeUpdate() {
        /*
         * 更新状态， 注意boss名下的salesman
         * 
         * 1 试用 ：传入试用期限
         * 
         * 0 禁用：所有数据清空
         * 
         */
        $probation = D("Probation");
        $user = D("User");

        if (!IS_POST) {
            $uid = $_GET['uid'];
            if (empty($uid)) {
                $this->error("非法操作！", U('Probation/freeIndex'));
            } else {
                $where['id'] = $uid;

                $res = $probation->where($where)->select();

                $this->assign("us", $res);


                $this->display();
            }
        } else {
            /*
             *  更新状态，每次都要更新 user 表 boss 名下的salesman 的 status 状态 1 1 0 0
             */
            $id = trim($_POST['uid']);
            $where['id'] = $id;
            $data['isfree'] = trim($_POST['isfree']);

            if (trim($_POST['isfree']) == '1') {
                $data['freedate'] = date("Y-m-d");
                $data['freemonth'] = trim($_POST['freemonth']);
                $data['freeend'] = date("Y-m-d", trim($_POST['freemonth']) * 30 * 24 * 60 * 60 + strtotime(date("Y-m-d")));
                $res = $probation->where($where)->save($data);
                /*
                 *   id  -> name  -> belong ->status  (for update)
                 */
                $us = $probation->field("name")->where($where)->find();
                $wherebe['belong'] = $us['name'];
                $name = $user->field("id,status")->where($wherebe)->select();
                $count = $user->field("id")->where($wherebe)->count();
                for ($i = 0; $i < $count; $i++) {
                    $whereid['id'] = $name[$i]['id'];
                    $dat['status'] = "1";
                    $user->where($whereid)->save($dat);
                }
            } else {
                $data['freedate'] = "";
                $data['freemonth'] = "";
                $data['freeend'] = "";
                $res = $probation->where($where)->save($data);
                /*
                 *   id  -> name  -> belong ->status  (for update)
                 */
                $us = $probation->field("name")->where($where)->find();
                $wherebe['belong'] = $us['name'];
                $name = $user->field("id,status")->where($wherebe)->select();
                $count = $user->field("id")->where($wherebe)->count();
                for ($i = 0; $i < $count; $i++) {
                    $whereid['id'] = $name[$i]['id'];
                    $dat['status'] = "0";
                    $user->where($whereid)->save($dat);
                }
            }


            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Probation/freeIndex'));
            }
        }
    }

    public function freeSearch() {
        if (!IS_GET) {
            $this->display();
        } else {
            $name = trim($_GET['name']);
            if ($name != "") {

                $where['name'] = array('like', "%$name%");
                $this->assign("name", $name);
            }

            $IDcard = trim($_GET['status']);
            if ($IDcard != "") {

                $where['isfree'] = array('like', "%$IDcard%");
                $this->assign("isfree", $IDcard);
            }



            $area = trim($_GET['freemonth']);
            if ($area != "") {

                $where['freemonth'] = array('like', "%$area%");
                $this->assign("freemonth", $area);
            }

            $start = trim($_GET['start']);
            if ($start != "") {

                $where['freedate'] = array('egt', $start . " 00:00:00");
                $this->assign("date", $start);
            }
            $end = trim($_GET['end']);
            if ($end != "") {

                $where['freeend'] = array('elt', $end . " 59:59:59");
                $this->assign("end", $end);
            }


            parent ::showPage("Probation", "10", $where);

            /*
             *  总计
             */
            $total = D("Probation")->where($where)->count();
            $this->assign("total", $total);

            $this->display();
        }
    }

    /*
     * 审核收费
     */

    public function tollIndex() {
        /*
         * 判断到期的用户
         * 
         */
        $today = date("Y-m-d");
        $end = D("Probation")->field("id,istoll,tollend")->where("tolldate > $today")->select();
        $num = D("Probation")->field("id")->where("tolldate > $today")->count();
        for ($i = 0; $i < $num; $i++) {
            $result = strtotime($end[$i]['tollend']) - strtotime($today);
            if ($end[$i]['istoll'] == "0") {

                if ($result <= 0) {
                    $data['istoll'] = "1";
                    $data['tolldate'] = "";
                    $data['tollmonth'] = "0";
                    $data['tollend'] = "";
                    $data['tollsum'] = "";
                    $wh['id'] = $end[$i]['id'];
                    D("Probation")->where($wh)->save($data);

                    /*
                     * 禁用Boss时，同时禁用Boss下的Salesman  name -> belong ->id ->status (for update)
                     * 
                     */
                    $namewh['belong'] = $end[$i]['name'];
                    $name = D("User")->field("id,status")->where($namewh)->select();
                    $namecount = D("User")->field("id")->where($namewh)->count();
                    for ($j = 0; $j < $namecount; $j++) {
                        $da['status'] = "0";
                        $whus['id'] = $namewh[$j]['id'];
                        D("User")->where($whus)->save($da);
                    }
                }
            }
        }


        /*
         * 显示用户试用信息
         */
        $where = "";
        $everypage = "10";
        $order = "istoll desc,tollend asc";
        $tbName = D("Probation"); // 实例化Data数据对象
        $count = $tbName->where($where)->count(); // 查询满足要求的总记录数

        $Page = new Page($count, $everypage); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        /*
          foreach ($where as $key => $val) {

          $p->parameter = "";
          }
         */
        $list = $tbName->where($where)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();



        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        /*
         *  总计金额
         */
        $sum = D("Probation")->where($where)->sum("tollsum");
        $this->assign("sum", $sum);

        /*
         *  总计用户
         */

        $us = D("Probation")->where($where)->count("name");
        $this->assign("us", $us);
        $this->display();
    }

    public function tollUpdate() {
        /*
         * 更新状态 
         * 
         * 1 试用 ：传入试用期限
         * 
         * 0 禁用：所有数据清空
         * 
         */
        $probation = D("Probation");
        $user = D("User");

        if (!IS_POST) {
            $uid = $_GET['uid'];
            if (empty($uid)) {
                $this->error("非法操作！", U('Probation/tollIndex'));
            } else {
                $where['id'] = $uid;

                $res = $probation->where($where)->select();

                $this->assign("us", $res);

                $this->display();
            }
        } else {
            $id = trim($_POST['uid']);
            $where['id'] = $id;
            $data['istoll'] = trim($_POST['istoll']);

            if (trim($_POST['istoll']) == '0') {
                $data['tolldate'] = date("Y-m-d");
                $data['tollmonth'] = trim($_POST['tollmonth']);
                $data['tollend'] = date("Y-m-d", trim($_POST['tollmonth']) * 30 * 24 * 60 * 60 + strtotime(date("Y-m-d")));
                $data['tollevery'] = trim($_POST['tollevery']);
                $data['tollsum'] = trim($_POST['tollmonth']) * trim($_POST['tollevery']);
                $res = $probation->where($where)->save($data);
                //var_dump($data);
                /*
                 *   id  -> name  -> belong ->status  (for update)
                 */
                $us = $probation->field("name")->where($where)->find();
                $wherebe['belong'] = $us['name'];
                $name = $user->field("id")->where($wherebe)->select();
                $count = $user->field("id")->where($wherebe)->count();
                for ($i = 0; $i < $count; $i++) {
                    $whereid['id'] = $name[$i]['id'];
                    $dat['status'] = "1";
                    $user->where($whereid)->save($dat);
                }
            } else {
                $data['tolldate'] = "";
                $data['tollmonth'] = "0";
                $data['tollend'] = "";
                $data['tollsum'] = "";
                $res = $probation->where($where)->save($data);
                /*
                 *   id  -> name  -> belong ->status  (for update)
                 */
                $us = $probation->field("name")->where($where)->find();
                $wherebe['belong'] = $us['name'];
                $name = $user->field("id")->where($wherebe)->select();
                $count = $user->field("id")->where($wherebe)->count();
                for ($i = 0; $i < $count; $i++) {
                    $whereid['id'] = $name[$i]['id'];
                    $dat['status'] = "0";
                    $user->where($whereid)->save($dat);
                }
            }

            //exit();



            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Probation/tollIndex'));
            }
        }
    }

    public function tollSearch() {
        if (!IS_GET) {
            $this->display();
        } else {
            $name = trim($_GET['name']);
            if ($name != "") {

                $where['name'] = array('like', "%$name%");
                $this->assign("name", $name);
            }

            $IDcard = trim($_GET['status']);
            if ($IDcard != "") {

                $where['istoll'] = array('like', "%$IDcard%");
                $this->assign("istoll", $IDcard);
            }



            $area = trim($_GET['freemonth']);
            if ($area != "") {

                $where['tollmonth'] = array('like', "%$area%");
                $this->assign("tollmonth", $area);
            }

            $every = trim($_GET['every']);
            if ($every != "") {

                $where['tollevery'] = array('like', "%$every%");
                $this->assign("every", $every);
            }

            $sum = trim($_GET['sum']);
            if ($sum != "") {

                $where['tollsum'] = array('like', "%$sum%");
                $this->assign("sum", $sum);
            }

            $start = trim($_GET['start']);
            if ($start != "") {

                $where['tolldate'] = array('egt', $start . " 00:00:00");
                $this->assign("date", $start);
            }
            $end = trim($_GET['end']);
            if ($end != "") {

                $where['tollend'] = array('elt', $end . " 59:59:59");
                $this->assign("end", $end);
            }


            parent ::showPage("Probation", "10", $where);
            /*
             *  总计金额
             */
            $sumtotal = D("Probation")->where($where)->sum("tollsum");
            $this->assign("sumtotal", $sumtotal);

            /*
             *  总计用户
             */

            $us = D("Probation")->where($where)->count("name");
            $this->assign("us", $us);

            $this->display();
        }
    }

}
