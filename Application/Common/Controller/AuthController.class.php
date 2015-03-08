<?php

namespace Common\Controller;

use Think\Controller;
use Think\Auth;
use Think\Page;

/*
 *  Auth 认证 控制器
 */

class AuthController extends Controller {
/*
    protected function _initialize() {

        $sess_auth = session("auth");
        $uid = session("uid");

        //还未登陆的用户
        if (!$sess_auth) {
            $this->error('对不起，你还未登陆！', U('Sale/Login/index'));
        } else {
            $auth = new Auth();
            $yn = $auth->check(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME, $uid);
            //检查用户权限
            if (!$yn) {
                $this->error("你没有权限");
            }
        }
    }

    /*
     * 普通分页方法
     */

    public function showPage($tbName, $everypage = "10", $where = "", $order = "id desc") {
        $tbName = D($tbName); // 实例化Data数据对象
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
    }

    /*
     * 关联模型 分页
     */

    public function relationPage($tbName, $everypage = "10", $where = "", $order = "id desc") {
        $tbName = D($tbName); // 实例化Data数据对象
        $count = $tbName->where($where)->relation(TRUE)->count(); // 查询满足要求的总记录数

        $Page = new Page($count, $everypage); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $tbName->where($where)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->relation(true)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        //var_dump($list);
    }

}
