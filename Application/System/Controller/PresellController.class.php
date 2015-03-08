<?php

/*
 * 
 * 商品预售参数设定： 售价  数量  库存下限通知(权限只有看，搜) 
 * 
 */

namespace System\Controller;

use Common\Controller\AuthController;
use Think\Page;

class PresellController extends AuthController {
    /*
     * 显示所有
     */

    public function index() {
        $company = D("Presell");
        $res = $company->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);

            parent::showPage("Presell");
        }


        $this->display();
    }

    /*
     * add()  唯一性的添加
     * 
     */

    public function add() {
        $company = D("Presell");
        if (!IS_POST) {
            if ($_GET['code'] != "") {
                $barcode = $_GET['code'];
                $where['barcode'] = $barcode;
                $where['letter'] = strtoupper($barcode);
                $where['_logic'] = "or";
                $bar = $company->field("id")->where($where)->find();
                $br = $bar['id'];
                $str = U('System/Presell/update/comid/' . "$br");

                if ($bar) {

                    echo "<div class='form-group'>
                    <label for='inputPassword3' class='col-sm-2 control-label'>提示</label>
                    <div class='col-sm-5'><div style='color:red;margin-top:5px;font-size:18px;'>该商品已经上架，请去 <a href='$str'>修改</a> 数据即可</div><input type='text' style='display:none'/></div>

                </div>";
                } else {
                    $out = D("Out");
                    $owhere['barcode'] = $barcode;
                    $owhere['letter'] = strtoupper($barcode);
                    $owhere['_logic'] = "or";

                    $ob = $out->field("id,letter")->where($owhere)->order("id desc")->find();
                    echo " <div class='form-group' style='display: none'>
                    <label for='inputPassword3' class='col-sm-2 control-label'>首字母</label>
                    <div class='col-sm-5'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='首字母大写' name='letter' value='" . $ob['letter'] . "' required>
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
            } else {
                $this->display();
            }
        } else {
            $data['barcode'] = $_POST['barcode'];
            $data['num'] = $_POST['num'];
            $data['presell'] = $_POST['presell'];

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
        $company = D("Presell");

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
            $company = D("Presell");
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
            $str = $_GET['search'];
            $where['barcode'] = array('like', "%$str%");
            $up = strtoupper($str);
            $where['letter'] = array('like', "%$up%");
            $where['num'] = array('like', "%$str%");
            $where['presell'] = array('like', "%$str%");

            $where['_logic'] = 'or';


            $this->assign("replace", "<font color='red'>$str</font>");
            $this->assign("str", $str);

            parent::showPage("Presell", "10", $where);

            $this->display();
        }
    }

    /*
     * confine() 商品下限提示：tb_out 的总库存量 - 下限值 < 0 红色提醒  红色结果集 asc 排序
     * 
     * 1、根据barcode查询总库存量
     * 2、获取下限值
     * 3、数据相减
     * 4、结果集排序显示  条形码 商品名 供货商 总库存 下限值 提示信息（红色）
     * 
     */

    public function confine() {
        $tbName = D('PresellView');

        $res = $tbName->field('id,barcode,product,company,sum(outamount),confine')->group('id')->select();

        echo $tbName->field("id")->group('id')->count();


        $this->assign("res", $res);
        var_dump($res);


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

}
