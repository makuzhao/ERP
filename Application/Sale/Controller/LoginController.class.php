<?php

namespace Sale\Controller;

use Think\Controller;
use Think\Verify;

/*
 *  分配登陆控制器
 * 
 *  1、姓名密码验证
 * 
 *  2、登陆权限验证
 */

class LoginController extends Controller {

    public function index() {
        if (!IS_POST) {
            $this->assign("path", C('PUBLIC'));
            $this->display();
        } else {
            $code = trim($_POST['verify']);
            $verify = new Verify();
            $verify->reset = TRUE;  // 开启重置，单一验证
            $r = $verify->check($code);
            if (!$r) {
                $this->error("验证码错误");
            }

            $name = trim($_POST['name']);
            $pwd = trim($_POST['pwd']);
            $user = D('User');
            $probation = D("Probation");

            $where['pwd'] = C('AUTHBEFORE') . $pwd . C('AUTHEND');

            $where['name'] = $name;

            $res = $user->field('id,belong,redirect,realName,shop_id')->where($where)->find();
            session("nameReal", $res['realName']);
            session("storeId", $res['shop_id']); // 用户的店铺ID 


            if ($res == "") {
                $this->error("用户名或登陆密码错误！", U('Login/index'));
            } else {
                /*
                 * 系统登录 无附加条件验证
                 */
                if ($res['redirect'] == "System") {
                    session("uid", $res['id']);
                    session("auth", $name);

                    $this->redirect("Manager/Index/index");
                }
                /*
                 * 老板登录 ( isfree = 1 || istoll = 0 ) && name = ?
                 */

                if ($res['redirect'] == "Boss") {
                    $whbos['id'] = $res['id'];
                    $data['latetime'] = date("Y-m-d H:i:s");
                    $user->where($whbos)->save($data);
                    // $wh['name'] = $res['belong'];
                    $prowh['isfree'] = "1";
                    $prowh['istoll'] = "0";
                    $prowh['_logic'] = "or";
                    $promap['_complex'] = $prowh;
                    $promap['name'] = $name;
                    $pro = $probation->where($promap)->find();

                    if (!$pro) {
                        $this->error("。。。试用的期限已过，或者没有付费。。。");
                    }

                    $db = $user->field("id,dbname")->where($whbos)->find();
                    session("uid", $res['id']);
                    session("auth", $name);
                    session("dbName", $db['dbname']);

                    $this->redirect("Boss/Index/index");
                }
                if ($res['redirect'] == "Sale") {

                    /*
                     * 销售员登录
                     */

                    $whsale['id'] = $res['id'];
                    $data['latetime'] = date("Y-m-d H:i:s");
                    $user->where($whsale)->save($data);  // 这次登陆时间

                    session("uid", $res['id']); // 用户ID
                    session("auth", $name);  // 用户名


                    $wh['name'] = $res['belong'];
                    //$wh['status'] = "1";
                    $db = $user->field("id,name,dbname,redirect,realName")->where($wh)->order("id asc")->find();

                    $whsale['status'] = "1"; // 账号状态
                    $xc = $user->where($whsale)->find();

                    if (!$xc) {
                        $this->error("。。。账号正在被禁用 。。。");
                    }

                    session("dbName", $db['dbname']); // 隶属老板的数据库 


                    $this->redirect("Sale/Index/index");
                }
            }
        }
    }

    /*
     * verify() 显示、刷新验证码
     */

    public function verify() {
        ob_end_clean();
        $Verify = new Verify();
        $Verify->fontSize = 30;
        $Verify->useCurve = FALSE;
        // $Verify->imageW = 200;
        // $Verify->imageH=30;
        $Verify->entry();
    }

    /*
     * check() ajax验证验证码的正确性
     *  
     */

    public function checkVer() {

        $code = trim($_GET['code']);
        if ($code != "") {

            $path = __ROOT__ . "/Public/Image"; // 文件路径
            $verify = new Verify();
            $verify->reset = false; // 禁用重置，方便多次验证
            $r = $verify->check($code);

            if ($r) {
                echo "<img src='" . $path . "/gou.png' style='width: 50px;height: 32px;'>";
            } else {

                echo "<img src='" . $path . "/cha.png' style='width: 50px;height: 32px;'>";
            }
        }
    }

}
