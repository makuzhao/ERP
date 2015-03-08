<?php

/*
 * 
 * 商品预售参数设定： 售价  数量  库存下限通知(权限只有看，搜) 
 * 
 */

namespace Boss\Controller;

use Common\Controller\AuthController;
use Think\Page;
use Think\Model;

class PresellController extends AuthController {
    /*
     * 显示所有
     */

    public function index() {
        $db_name = session("dbName") . ".Presell";
        $company = M($db_name);
        $res = $company->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);

            parent::showPage($db_name);
        }

        $id = $company->count("id");
        $this->assign("id", $id);


        $this->display();
    }

    /*
     * add()  唯一性的添加  
     * 1、根据 code 查询 presell -》 没有数据 -》 往下
     * 
     * 2、根据 code 查询 goods -》没有数据，提醒本地没有改商品 
     * 
     *                           有数据  ，可写
     * 
     */

    public function add() {
        $barcode = trim($_GET['code']);
        if (!IS_POST) {
            if ($_GET['code'] != "") {

                $model = new Model();
                $sql = "use  " . $_SESSION['dbName'];
                $model->query($sql);
                $company = D("Presell");
                $where['barcode'] = array('like', "%$barcode%");
                $s = strtoupper($barcode);
                $where['letter'] = array('like', "%$s%");
                $where['_logic'] = "or";
                $bar = $company->field("id")->where($where)->find();
                $br = $bar['id'];
                $str = U('Boss/Presell/update/comid/' . "$br");

                if ($bar) {

                    echo "<div class='form-group'>
                    <label for='inputPassword3' class='col-sm-2 control-label'>提示</label>
                    <div class='col-sm-5'><div style='color:red;margin-top:5px;font-size:18px;'>该商品已经上架，请去 <a href='$str'>修改</a> 数据即可</div><input type='text' style='display:none'/></div>

                </div>";
                } else {
                    $go = D("Goods");
                    $whgo['barcode'] = array('like', "%$barcode%");
                    $s = strtoupper($barcode);
                    $whgo['letter'] = array('like', "%$s%");
                    $whgo['_logic'] = "or";
                    $bgo = $go->field("id")->where($whgo)->order("id desc")->find();
                    if ($bgo == "") {
                        echo "<div class='form-group'>
                    <label for='inputPassword3' class='col-sm-2 control-label'>提示</label>
                    <div class='col-sm-5'><div style='color:red;margin-top:5px;font-size:18px;'>本店不经营该商品</div><input type='text' style='display:none'/></div>

                </div>";
                    } else {

                        echo " <div class='form-group' style='display: none'>
                    <label for='inputPassword3' class='col-sm-2 control-label'>首字母</label>
                    <div class='col-sm-5'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='首字母大写' name='letter' value='" . $bgo['letter'] . "' required>
                    </div>

                </div>";

                        echo "<div class='form-group'>
                    <label for='inputPassword3' class='col-sm-2 control-label'>数量</label>
                    <div class='col-sm-5'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='数量' name='num'  required>
                    </div>

                </div>

                <div class='form-group'>
                    <label for='inputEmail3' class='col-sm-2 control-label'>预售价</label>
                    <div class='col-sm-5'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='预售价' name='presell' required>
                    </div>
                </div>
                
                <div class='form-group'>
                    <label for='inputEmail3' class='col-sm-2 control-label'>下限值</label>
                    <div class='col-sm-5'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='库存超过这个值时有提示信息' name='confine' required>
                    </div>
                </div>

                
                <div class='form-group'>
                    <div class='col-sm-offset-3 col-sm-10'>
                        <button type='submit'  class='btn btn-primary btn-lg'> 添 加 </button>
                    </div>
                </div>";
                    }
                }
            } else {
                $this->display();
            }
        } else {
            $model = new Model();
            $sql = "use  " . $_SESSION['dbName'];
            $model->query($sql);

            $company = D("Presell");
            $data['barcode'] = trim($_POST['barcode']);
            $data['num'] = trim($_POST['num']);
            $data['presell'] = trim($_POST['presell']);

            $res = $company->add($data);
            if (!$res) {
                $this->error("添加失败 ？？");
            } else {
                $this->redirect('Presell/index');
            }
        }
    }

    /*
     * 更新
     */

    public function update() {
        $db_name = session("dbName") . ".Presell";
        $company = M($db_name);

        if (!IS_POST) {
            $comid = $_GET['comid'];
            if (empty($comid)) {
                $this->error("非法操作！", U('Presell/index'));
            } else {
                $where['id'] = $comid;
                $res = $company->where($where)->select();
                $this->assign("res", $res);
                $this->display();
            }
        } else {
            $where['id'] = $_POST['comid'];
            $data['barcode'] = $_POST['barcode'];
            $data['num'] = $_POST['num'];
            $data['presell'] = $_POST['presell'];
            $data['confine'] = $_POST['confine'];

            $res = $company->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->redirect('Presell/index');
            }
        }
    }

    /*
     * 删除
     */

    public function delete() {
        $comid = $_GET['comid'];
        if (empty($comid)) {
            $this->error("非法操作！", U('Presell/index'));
        } else {
            $db_name = session("dbName") . ".Presell";
            $company = M($db_name);

            $where['id'] = $comid;
            $res = $company->where($where)->delete();
            if ($res) {
                $this->success("删除成功", U('Presell/index'));
            } else {
                $this->error("删除失败！");
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
        if (!IS_GET) {
            $this->display();
        } else {
            $bar = trim($_GET['barcode']);
            if ($bar != "") {

                $where['barcode'] = array('like', "%$bar%");
                $this->assign("bar", $bar);
            }



            $let = trim($_GET['letter']);
            if ($let != "") {
                $let = strtoupper($let);
                $where['letter'] = array('like', "%$let%");
                $this->assign("let", $let);
            }

            $amount = trim($_GET['outamount']);
            if ($amount != "") {

                $where['num'] = array('like', "%$amount%");
                $this->assign("num", $amount);
            }

            $price = trim($_GET['outprice']);
            if ($price != "") {

                $where['presell'] = array('like', "%$price%");
                $this->assign("pre", $price);
            }

            $sum = trim($_GET['outsum']);
            if ($sum != "") {

                $where['amount'] = array('like', "%$sum%");
                $this->assign("amount", $sum);
            }

            $people = trim($_GET['confine']);
            if ($people != "") {

                $where['confine'] = array('like', "%$people%");
                $this->assign("confine", $people);
            }

            $db_name = session("dbName") . ".Presell";

            parent::showPage($db_name, "10", $where);

            $id = M($db_name)->where($where)->count("id");
            $this->assign("id", $id);

            $this->display();
        }
    }

    /*
     * confine() 商品下限提示：tb_storage 的总库存量 - 下限值 < 0 红色提醒  红色结果集 asc 排序
     * 
     * 1、根据barcode查询总库存量
     * 2、获取下限值
     * 3、数据相减
     * 4、结果集排序显示  条形码 商品名 供货商 总库存 下限值 提示信息（红色）
     * 
     */

    public function confine() {
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $tbName = D('PresellView');

        $everypage = "10";
        $where = "";

        // echo $count = $tbName->where($where)->count(); // 查询满足要求的总记录数

        $subQuery = $tbName->field('*')->group('barcode')->where($where)->order('sum(outamount) desc')->select(false);
        $count = $tbName->table($subQuery . 'xc')->count('barcode');

        $Page = new Page($count, $everypage); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        /*
          foreach ($where as $key => $val) {

          $p->parameter = "";
          }
         */
        $order = "k asc";

        $list = $tbName->field("SUM(outamount)-SUM(confine) as k,id,barcode,product,confine,total")->where($where)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->group("barcode")->select();



        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        $this->assign("id", $count);
        $this->display();
    }

    public function conSearch() {
        if (!IS_GET) {
            $this->display();
        } else {

            $model = new Model();
            $sql = "use  " . $_SESSION['dbName'];
            $model->query($sql);


            $bar = trim($_GET['bar']);
            if ($bar != "") {
                $where['barcode'] = array('like', "%$bar%");
                $this->assign("bar", $bar);
            }

            $pro = trim($_GET['product']);
            if ($pro != "") {
                $where['product'] = array('like', "%$pro%");
                $this->assign("pro", $pro);
            }

            $let = trim($_GET['letter']);
            if ($let != "") {
                $let = strtoupper($let);
                $where['letter'] = array('like', "%$let%");
                $this->assign("let", $let);
            }



            $people = trim($_GET['confine']);
            if ($people != "") {

                $where['confine'] = array('like', "%$people%");
                $this->assign("confine", $people);
            }






            $everypage = "10";

            //$res = $out->field("id,outtime,outprice,outamount")->relation(true)->order("outtime desc")->select();
            $tbName = D("PresellView"); // 实例化Data数据对象

            /*
             *  使用视图子查询
             */
            $subQuery = $tbName->field('*')->group('barcode')->where($where)->order('sum(outamount) desc')->select(false);
            $count = $tbName->table($subQuery . 'xc')->count('barcode');

            //$count = $tbName->count('barcode'); // 查询满足要求的总记录数


            $Page = new Page($count, $everypage); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show = $Page->show();
            // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

            $order = "k asc";

            $list = $tbName->field("SUM(outamount)-SUM(confine) as k,id,barcode,product,confine,total")->where($where)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->group("barcode")->select();


            $this->assign('list', $list); // 赋值数据集
            $this->assign('page', $show); // 赋值分页输出


            $this->assign("id", $count);

            $this->display();
        }
    }

}
