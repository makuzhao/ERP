<?php

namespace Boss\Controller;

use Common\Controller\AuthController;
use Think\Model;
use Think\Db;

/*
 * 
 * 店铺管理
 * 
 */

class ShopController extends AuthController {
    /*
     * 显示所有店铺
     */

    public function index() {

        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $company = D("Shop");
        $res = $company->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);

            parent::showPage("Shop");
        }

        $id = $company->where($where)->count("id");
        $this->assign("realName", $_SESSION["nameReal"]);
        $this->assign("id", $id);

        $this->display();
    }

    /*
     * add () 添加店铺   
     * 
     * 1、填店铺的基本信息
     * 
     * 2、信息优先入库
     * 
     * 3、根据店铺的 ID 创建三表 sales storage goods
     *  
     */

    public function add() {
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        if (!IS_POST) {

            $this->display();
        } else {
            $company = D("Shop");
            /*
             *  获取信息
             */
            $data['shop'] = $_POST['shop'];  // 唯一性，不可更改
            $data['address'] = $_POST['address'];
            $data['people'] = $_POST['people'];
            $data['tel'] = $_POST['tel'];

            /*
             * 信息入库
             */

            $res = $company->add($data);

            /*
              if ($res) {
              $this->success("添加店铺成功 ！！", U('Shop/index'));
              } else {
              $this->error("添加店铺失败 ？？");
              }

              /*
             * 查询 ID  shop =》 ID
             */

            $where['shop'] = $_POST['shop'];
            $id = $company->field("id")->where($where)->find();
            $tb_id = $id['id'];  // 店铺 ID 建表用

            /*
             * 创建三表 tb_ID_sales tb_ID_storage tb_ID_goods
             */

            $bossDB = $_SESSION['dbName'];


            $DB = array();
            $DB['DB_TYPE'] = C('DB_TYPE');
            $DB['DB_PORT'] = C('DB_PORT');
            $DB['DB_HOST'] = C('DB_HOST');
            $DB['DB_NAME'] = $bossDB;
            $DB['DB_USER'] = C('DB_USER');
            $DB['DB_PWD'] = C('DB_PWD');
            $DB['DB_PREFIX'] = C('DB_PREFIX');
            $prefix = "tb_";
            // $dsn = "$dbType://$dbUser:$dbPwd@$host:$dbPort/$dbName";


            $dbname = $DB['DB_NAME'];


            //unset($DB['DB_NAME']);
            $db = Db::getInstance($DB);
            //var_dump($DB);


            $DB['DB_NAME'] = $bossDB;
            $db = Db::getInstance($DB);
            $this->create_tables($db, $prefix, $tb_id);


            if ($res) {
                //echo "刷新<script language=JavaScript> self.opener.location.reload();</script>";
                $this->success("添加店铺成功 ！！");
                $this->success("<script language=JavaScript> parent.location.reload();</script>", U('Shop/index'), 2);
            } else {
                $this->error("添加店铺失败 ？？");
            }
        }
    }

    /**
     * 创建数据表
     * @param  resource $db 数据库连接资源
     */
    private function create_tables($db, $prefix = '', $tb_id = "") {
        //读取SQL文件
        $sql = file_get_contents(dirname(dirname(MODULE_PATH)) . '/Public/Install/tb_three.sql');
        $sql = str_replace("\r", "\n", $sql);
        $sql = explode(";\n", $sql); //  \n 后面不可有任何空格
        //替换表前缀
        $orginal = C('ORIGINAL_TABLE_PREFIX');

        $tb = $prefix . $tb_id . "_";

        $sql = str_replace(" `{$prefix}", " `{$tb}", $sql);

        //$sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
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
                // $name = $name . $tb_id;

                if (false !== $db->execute($value)) {
                    //  $this->show("<br>... 创建数据表: （ {$name} ） ... 成功<br>");
                } else {
                    //  $this->show("<br><font sytle='color:red'>... 创建数据表: （ {$name} ） ... 失败</font><br>", 'error');
                    session('error', true);
                }
            } else {
                $r = $db->execute($value);
            }
        }
    }

    /*
     *  ajaxCheck（） 验证店铺名的唯一性
     */

    public function ajaxCheck() {
        // header('Content-Type:text/html; charset=utf-8');
        $shop = trim($_GET['shop']);
        $shop = iconv("gbk", "UTF-8", $shop);

        if ($shop != "") {
            $model = new Model();
            $sql = "use  " . $_SESSION['dbName'];
            $model->query($sql);

            $where['shop'] = array("like", "%$shop%");
            $s = D("Shop");
            $res = $s->where($where)->find();
            if ($res != "") {
                echo "<font style='color:red'> $shop </font>不可用";
            } else {
                echo "<font > $shop </font>可用";
            }
        }
    }

    /*
     * 更新商家
     */

    public function update() {
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);


        $company = D("Shop");

        if (!IS_POST) {
            $comid = $_GET['comid'];
            if (empty($comid)) {
                $this->error("非法操作！", U('Shop/index'));
            } else {
                $where['id'] = $comid;
                $res = $company->where($where)->select();
                $this->assign("res", $res);
                $this->display();
            }
        } else {
            $where['id'] = $_POST['comid'];
            $data['shop'] = $_POST['shop'];
            $data['address'] = $_POST['address'];
            $data['people'] = $_POST['people'];
            $data['tel'] = $_POST['tel'];

            $res = $company->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Shop/index'));
            }
        }
    }

    /*
     * 删除店铺  delete（）
     * 
     * 1、删除与店铺ID 相关的所有表 tb_ID_goods tb_ID_sales（ user 也要删除） tb_ID_storage 
     * 
     * 2、删除tb_user 表中 shop_id 相关的销售员
     * 
     */

    public function delete() {

        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $comid = $_GET['comid'];
        if (empty($comid)) {
            $this->error("非法操作！", U('Shop/index'));
        } else {
            $company = D("Shop");
            echo $where['id'] = $comid;

            $res = $company->where($where)->delete();

            /*
             * 删除数据表
             */

            $model->query("DROP TABLE tb_" . $comid . "_goods");
            $model->query("DROP TABLE tb_" . $comid . "_sales");
            $model->query("DROP TABLE tb_" . $comid . "_storage");

            /*
             * 删除该店铺的销售员
             */
            $comid = $_GET['comid'];
            $model = new Model();
            $sql = "use " . C(DB_NAME);
            $model->query($sql);
            $user = D("User");

            $user->where(" `shop_id`= $comid ")->delete();

            if ($res) {
                $this->success("删除店铺成功！！");
                $this->success("<script language=JavaScript> parent.location.reload();</script>", U('Shop/index'));
            } else {
                $this->error("删除店铺失败！");
            }
        }
    }

    /*
     * 用户搜索 searching（）
     * 
     * $str  关键字
     * 
     */

    public function searching() {
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        if (!IS_POST) {
            $this->display();
        } else {
            $shop = trim($_POST['shop']);

            if ($shop != "") {

                $where['shop'] = array('like', "%$shop%");

                $this->assign("shop", $shop);
            }

            $tel = trim($_POST['tel']);
            if ($tel != "") {

                $where['tel'] = array('like', "%$tel%");
                $this->assign("tel", $tel);
            }
            $address = trim($_POST['address']);
            if ($address != "") {

                $where['address'] = array('like', "%$address%");
                $this->assign("address", $address);
            }


            $people = trim($_POST['people']);
            if ($people != "") {

                $where['people'] = array('like', "%$people%");
                $this->assign("people", $people);
            }



            parent::showPage("Shop", "10", $where);

            $user = D('Shop');
            $id = $user->where($where)->count("id");
            $this->assign("id", $id);

            $this->display();
        }
    }

}
