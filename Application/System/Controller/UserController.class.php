<?php

/*
 *  用户管理
 */

namespace System\Controller;

use Common\Controller\AuthController;
use Think\Model;
use Think\Controller;
use Think\Page;
use Think\Db;
use Think\Storage;
use System\Model\UserViewModel;
use Think\Model\ViewModel;

class UserController extends AuthController {
    /*
     * 显示用户的所有信息
     */

    public function index() {
        $user = D("UserView");
        $res = $user->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);

            $where['redirect'] = "Boss";
            parent::showPage("UserView", "10", $where);


            /*
             * 试用、收费状态的显示 id = user_id
             */
        }

        $us = D('UserView');
        $count = $us->where($where)->count("id");
        $this->assign("uscount", $count);

        $this->display();
    }

    /*
     * 添加用户  创建用户数据库 $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
     * 
     * 
     */

    public function add() {
        if (!IS_POST) {
            $this->usGroup();
            $this->display();
        } else {
            $user = D("User");
            $data['name'] = trim($_POST['name']);
            $data['pwd'] = C('AUTHBEFORE') . trim($_POST['pwd']) . C('AUTHEND');
            $data['realName'] = trim($_POST['real']);
            $data['IDcard'] = trim($_POST['IDcard']);
            $data['tel'] = trim($_POST['tel']);
            $data['email'] = trim($_POST['email']);
            $data['phone'] = trim($_POST['phone']);
            $data['status'] = "1";
            $data['fax'] = trim($_POST['fax']);
            $data['address'] = trim($_POST['address']);
            $data['area'] = trim($_POST['province']) . " - " . trim($_POST['city']);

            $data['info'] = trim($_POST['info']);

            $data['belong'] = trim($_SESSION['auth']); // 创建用户的人
            $data['redirect'] = "Boss"; // 登陆跳转的模块

            $data['regtime'] = date("Y-m-d H:i:s");
            $data['Group'] = array(
                'id' => $_POST['group_id']
            );
            $res = $user->relation(true)->add($data);
            /*
             * 添加用户时，同时添加审计收费管理，自动开启试用期三个月
             */

            //   $res = $user->relation(true)->add($data);

            $where['name'] = trim($_POST['name']);
            $result = $user->field("id,name")->where($where)->find();

            $add['user_id'] = $result['id'];
            $add['name'] = $result['name'];
            $add['isfree'] = "1";
            $add['freedate'] = date("Y-m-d");
            $add['freemonth'] = "3";
            $add['freeend'] = date("Y-m-d", 3 * 30 * 24 * 60 * 60 + strtotime(date("Y-m-d")));
            D('Probation')->add($add);


            /*
             * 自动创建数据库
             */
            $bossDB = "db_" . $result['id'];
            $data['dbname'] = $bossDB;

            $DB = array();
            $DB['DB_TYPE'] = C('DB_TYPE');
            $DB['DB_PORT'] = C('DB_PORT');
            $DB['DB_HOST'] = C('DB_HOST');
            $DB['DB_NAME'] = $bossDB;
            $DB['DB_USER'] = C('DB_USER');
            $DB['DB_PWD'] = C('DB_PWD');
            $DB['DB_PREFIX'] = C('DB_PREFIX');
            $prefix = "";
            // $dsn = "$dbType://$dbUser:$dbPwd@$host:$dbPort/$dbName";


            $dbname = $DB['DB_NAME'];


            unset($DB['DB_NAME']);
            $db = Db::getInstance($DB);
            //var_dump($DB);



            $sqld = "CREATE DATABASE IF NOT EXISTS `" . $dbname . "` DEFAULT CHARACTER SET utf8";
            //开始安装
            // $this->show("<br>... 开始安装数据库: ( " . $dbname . " ) ...<br>");
            $result = $db->execute($sqld);


            if ($result) {
                // $this->show("<br>...  " . $dbname . "  ...安装成功<br>");

                $DB['DB_NAME'] = $bossDB;
                $db = Db::getInstance($DB);
                $this->create_tables($db, $prefix);

                $user->where($where)->relation(true)->save($data);
            }

            if ($res) {
                $this->success("添加用户成功 ！！", U('User/index'));
            } else {
                $this->error("添加用户失败 ？？");
            }
        }
    }

    /*
     * ajax()  检验用户名的唯一性
     * 
     */

    public function ajax() {
        $name = trim($_GET['name']);
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

    public function usGroup() {

        $ug = D("Group");
        $us = $ug->field("id,title,hint")->where("`title` = 'Boss'")->select();
        $this->assign("ug", $us);
    }

    /*
     *  addDB() 创建该用户的数据库
     */

    public function addDB() {
        if (!IS_POST) {
            $id = trim($_GET['id']);
            $red = D('User')->field("redirect")->where("id = $id")->find();
            //var_dump($red);
            if ($red['redirect'] == "Salesman" || $red['redirect'] == "") {
                $this->error("没有权限创建数据库");
            }
            $dbName = "db_$id";
            $this->assign("id", $id);
            $this->assign("dbname", $dbName);

            $this->display();
        } else {

            /*
             * 获取数据
             */
            $user = D("User");
            $where['id'] = trim($_POST['id']);
            $data['dbname'] = trim($_POST['dbname']);






            /*
              if ($res) {
              $this->success("添加用户成功 ！！", U('User/index'));
              } else {
              $this->error("添加用户失败 ？？");
              }
             */
            /*
             * 创建数据库
             */
            // exit();
            $DB = array();
            $DB['DB_TYPE'] = C('DB_TYPE');
            $DB['DB_PORT'] = C('DB_PORT');
            $DB['DB_HOST'] = C('DB_HOST');
            $DB['DB_NAME'] = trim($_POST['dbname']);
            $DB['DB_USER'] = C('DB_USER');
            $DB['DB_PWD'] = C('DB_PWD');
            $DB['DB_PREFIX'] = C('DB_PREFIX');
            $prefix = "";
            // $dsn = "$dbType://$dbUser:$dbPwd@$host:$dbPort/$dbName";


            $dbname = $DB['DB_NAME'];


            unset($DB['DB_NAME']);
            $db = Db::getInstance($DB);
            //var_dump($DB);

            $sqld = "CREATE DATABASE IF NOT EXISTS `" . $dbname . "` DEFAULT CHARACTER SET utf8";
            //开始安装
            //  $this->show("<br>... 开始安装数据库: ( " . $dbname . " ) ...<br>");
            $result = $db->execute($sqld);


            if ($result) {
                // $this->show("<br>...  " . $dbname . "  ...安装成功<br>");

                $DB['DB_NAME'] = trim($_POST['dbname']);
                $db = Db::getInstance($DB);
                //print_r($db);
                $this->create_tables($db, $prefix);
                $res = $user->where($where)->relation(true)->save($data);
            } else {
                //  $this->show("创建表失败");
            }
        }
    }

    /**
     * 创建数据表
     * @param  resource $db 数据库连接资源
     */
    public function create_tables($db, $prefix = '') {
        //读取SQL文件
        $sql = file_get_contents(dirname(dirname(MODULE_PATH)) . '/Public/Install/boss.sql');
        $sql = str_replace("\r", "\n", $sql);
        $sql = explode(";\n", $sql); //  \n 后面不可有任何空格
        //替换表前缀
        $orginal = C('ORIGINAL_TABLE_PREFIX');
        $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
        //开始安装
        // $this->show('<br>... 开始导入 SQL 文件到数据库 ...<br>');
        //$sql = trim($sql);
        //print_r($sql);
        foreach ($sql as $value) {
            $value = trim($value);
            if (empty($value))
                continue;

            if (substr($value, 0, 12) == 'CREATE TABLE') {
                $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);

                if (false !== $db->execute($value)) {
                    //  $this->show("<br>... 创建数据表: （ {$name} ） ... 成功<br>");
                } else {
                    // $this->show("<br><font sytle='color:red'>... 创建数据表: （ {$name} ） ... 失败</font><br>", 'error');
                    session('error', true);
                }
            } else {
                $r = $db->execute($value);
            }
        }

        // echo "<a href='" . U('User/index') . "'><button style='padding:10px;margin:20px  100px;font-size:20px;'>跳 转</button></a>";
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

                $this->assign("us", $res);
                $this->usGroup();

                $this->display();
            }
        } else {
            $id = trim($_POST['uid']);
            $where['id'] = $id;
            $data['name'] = $_POST['name'];
            $data['pwd'] = C('AUTHBEFORE') . trim($_POST['pwd']) . C('AUTHEND');
            $data['Group'] = array(
                'id' => $_POST['group_id']
            );
            $data['tel'] = trim($_POST['tel']);
            $data['email'] = trim($_POST['email']);
            $data['phone'] = trim($_POST['phone']);
            $data['fax'] = trim($_POST['fax']);
            $data['address'] = trim($_POST['address']);
            //$data['area'] = trim($_POST['area']);
            $data['IDcard'] = $_POST['IDcard'];
            $data['realName'] = trim($_POST['real']);

            if (trim($_POST['province']) != "") {
                $data['area'] = trim($_POST['province']) . " - " . trim($_POST['city']);
            }



            //$data['status'] = trim($_POST['status']);
            $data['info'] = trim($_POST['info']);
            // $data['belong'] = trim($_SESSION['auth']);


            $res = $user->relation(true)->where($where)->save($data);

            /*
             * 禁用Boss时，同时禁用Boss下的Salesman  name -> belong ->id ->status (for update)
             * 

              $name['belong'] = $_POST['name'];
              $count = $user->field("id")->where($name)->count();
              $r = $user->field("id")->where($name)->select();
              $status['status'] = trim($_POST['status']);
              for ($i = 0; $i < $count; $i++) {
              $wd['id'] = $r[$i]['id'];
              var_dump($r[$i]['id']);
              $user->relation(true)->where($wd)->save($status);
              }
             */
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success
                        ("数据更新成功 ！！", U('User/index'));
            }
        }
    }

    /*
     * delete()删除用户 删除对应的销售员信息 删除对应的数据库 删除对应的probation数据 
     * 
     * 1、删除对应收费的用户
     * 
     * 2、删除用户拥有的数据库
     * 
     * 3、删除用户名下的所有销售员信息
     * 
     */

    public function delete() {
        $uid = trim($_GET['uid']);
        if (empty($uid)) {
            $this->error("非法操作！", U('User/index'));
        } else {
            $user = D("User");
            $where['id'] = $uid;
            /*
             * 删除Boss的审计收费信息
             */
            $pro['user_id'] = $uid;
            D('Probation')->where($pro)->delete();

            /*
             * 删除Boss的数据库信息
             */
            $db = "db_" . $uid;
            $sql = "DROP DATABASE " . $db;
            $mo = new Model;
            $mo->query($sql);
            $mo->getLastSql();


            /*
             * 删除当前ID 的Boss 下的所有销售员Salesman信息  id -> name ->belong -> id (for Del)
             */
            $name = $user->field("id,name ")->where($where)->find();
            $be['belong'] = $name['name'];

            $belong = $user->field("id")->where($be)->select();
            $count = $user->field("id")->where($be)->count();

            for ($i = 0; $i < $count; $i++) {
                $wh["id"] = $belong[$i]['id'];
                D('User')->where($wh)->delete();
            }



            /*
             * 删除 当前 Boss 个人信息
             */

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
            $name = trim($_GET['name']);
            if ($name != "") {

                $where['name'] = array('like', "%$name%");
                $this->assign("name", $name);
            }
            $real = trim($_GET['real']);
            if ($real != "") {

                $where['realName'] = array('like', "%$real%");
                $this->assign("real", $real);
            }
            $IDcard = trim($_GET ['IDcard']);
            if ($IDcard != "") {

                $where['IDcard'] = array('like', "% $IDcard%");
                $this->assign("IDcard", $IDcard);
            }


            $tel = trim($_GET['tel']);
            if ($tel != "") {

                $where['tel'] = array('like', "%$tel %");
                $this->assign("tel", $tel);
            }

            $free = trim($_GET ['free']);
            if ($free != "") {

                $where['Probation.isfree'] = array('like', "%$free%");
                $this->assign("free", $free);
            }

            $toll = trim($_GET ['toll']);
            if ($toll != "") {

                $where['istoll'] = array('like', "%$toll%");
                $this->assign("toll", $toll);
            }



            $email = trim($_GET ['email']);
            if ($email != "") {

                $where['tel'] = array('like', "%$email%");
                $this->assign("email", $email);
            }

            $phone = trim($_GET['phone']);
            if ($phone != "") {

                $where['phone'] = array('like', "% $phone%");
                $this->assign("phone", $phone);
            }

            $fax = trim($_GET['fax']);
            if ($fax != "") {

                $where['fax'] = array('like', "% $fax%");
                $this->assign("fax", $fax);
            }

            $info = trim($_GET ['info']);
            if ($info != "") {

                $where['info'] = array('like', "%$info%");
                $this->assign("info", $info);
            }

            $address = trim($_GET['address']);
            if ($address != "") {

                $where['address'] = array('like', "%$address%");
                $this->assign("address", $address);
            }

            $province = trim($_GET['province']);
            $city = trim($_GET['city']);

            if ($province != "" || $city != "") {
                $area = $province . " - " . $city;
                $where['area'] = array('like', "%$area%");
                $this->assign("area", $area);
            }

            $start = trim($_GET['start']);
            $end = trim($_GET['end']);
            if ($start != "" && $end != "") {

                $where['latetime'] = array('between', array($start . " 00:00:00", $end . " 59:59:59"));
                $this->assign("date", $date);
            }
            //$where['redirect'] = "Boss";
            //   parent::showPage("User", "10", $where);

            $where['redirect'] = "Boss";
            parent::showPage("UserView", "10", $where);


            // 满足条件的记录数
            $us = D('UserView');
            $count = $us->where($where)->count("id");
            $this->assign("uscount", $count);

            $this->display();
        }
    }

}
