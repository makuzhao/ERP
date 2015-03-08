<?php

/*
 *  登陆控制器
 */

namespace System\Controller;

use Think\Controller;

class LoginController extends Controller {

    public function index() {
        if (!IS_POST) {
            $this->display();
        } else {
            $name = trim($_POST['name']);
            $pwd = trim($_POST['pwd']);
            $user = D('User');

            $where['pwd'] = C('AUTHBEFORE') . $pwd . C('AUTHEND');

            $where['name'] = $name;
            $res = $user->where($where)->getField("id");


            if ($res == "") {
                $this->error("用户名或登陆密码错误！", U('Login/index'));
            } else {

                $where['id'] = $res;
                $data['latetime'] = date("Y-m-d H:i:s");
                $user->where($where)->save($data);

                session("uid", $res);
                session("auth", $name);
                $this->redirect('Index/index');
            }
        }
    }

    /*
     * 
     * 
     * 
     */
}
