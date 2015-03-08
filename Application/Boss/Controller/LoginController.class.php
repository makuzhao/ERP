<?php

/*
 *  登陆控制器
 */

namespace Boss\Controller;

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
            $res = $user->field("id,dbname")->where($where)->find();

            $map['_string'] = "isfree ='1' or istoll ='0'";
            $map['name'] = $name;

            $re = D("Probation")->where($map)->find();



            if ($res != "" && $re != "") {

                if ($res['dbname'] == "") {
                    $this->error("您还没创建数据库", U('Login/index'));
                } else {

                    $where['id'] = $res['id'];

                    $data['latetime'] = date("Y-m-d H:i:s");
                    $user->where($where)->save($data);

                    session("uid", $res['id']);
                    session("auth", $name);

                    session("dbName", $res['dbname']);

                    $this->redirect('Index/index');
                }
            } else {
                $this->error("<font style='color:red'>错误提示</font><br>1、用户名或密码错误；<br>2、当前用户被禁用", U('Login/index'));
            }
        }
    }

    /*
     * 
     * 
     * 
     */

    public function test() {

        $conn = mysql_connect("127.0.0.1:3308", "root", "root") or die("00俩姐姐");

        mysql_select_db("db_erp", $conn) or die("XXCC靓妹妹");

        $query = "select * from tb_user";

        $result = mysql_query($query);



        for ($i = 1; $i < mysql_num_fields($result); $i++) {

            /*
             *                  字段模糊查询 
             *  第一次
             *         $i=1 ->   name       ->  %$str%  红色替换
             *         $i=2 ->   pwd        ->  %$str%  红色替换
             *         $i=3 ->   regtmie    ->  %$str%  红色替换
             *         $i=4 ->   latetime   ->  %$str%  红色替换
             *         
             */
            $name = mysql_field_name($result, $i);

            echo $name . "<BR>";
        }
    }

}
