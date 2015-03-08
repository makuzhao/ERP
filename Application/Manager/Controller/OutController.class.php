<?php

/*
 *  出库商品
 */

namespace Manager\Controller;

use Common\Controller\AuthController;
use Think\Model;

class OutController extends AuthController {
    /*
     * 显示所有出库商品
     * 
     */

    public function index() {
        $out = D("Out");
        $res = $out->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);

            $total = $out->sum("outsum");
            $this->assign("total", $total);

            parent::showPage("Out", "10");
        }


        $this->display();
    }

    /*
     * showShop() 通过零售商的 ID 显示 商家名 点击显示具体的信息
     */

    public function showShop() {
        $sh_id = trim($_GET['shid']);
        if ($sh_id == "") {
            $this->error("操作错误");
        } else {
            $shop = D("Shop");
            $where['id'] = $sh_id;
            $res = $shop->where($where)->select();
            $this->assign("shop", $res);
            $this->display();
        }
    }

    /*
     *  look()  查看商品的具体信息
     * 
     *  1、 根据get传来的 barcode 查库
     *  
     *  2、 信息显示在弹出层上
     */

    public function look() {

        $id = $_GET['id'];
        $where['barcode'] = $id;
        $goods = D('Goods');

        $res = $goods->where($where)->select();


        $this->assign("look", $res);

        $this->display();
        exit();
        if (!$res) {
            $this->error("不存在这个条形码 ？？");
        } else {
            $this->assign("look", $res);
        }
        $this->display();
    }

    /*
     *  add() 商品出库 主要先添加 tb_out  后更新 tb_in
     * 
     *  1、根据 ajax 通过 get 传递的 barcode 值 查询 tb_in 的 总inamount ，然后显示到前端指定的位置
     * 
     *  2、获取前端所有的数据，并写入 tb_out
     * 
     *  3、条件 barcode inamount>0 查询 tb_in 中的 id  inamount 排序 id ASC  的一条数据，根据 所得的id 条件更新 inamount 的值
     * 
     *  4、根据 outamount 数值循环执行 3 步骤
     * 
     */

    public function add() {
        if (!IS_POST) {
            $this->display();
        } else {
            $out = D("Out");
            $code = $_POST['barcode'];
            $outamount = $_POST['outamount'];
            /*
             * 获取所有数据
             */
            $data['encode'] = "OUT" . time();
            $data['barcode'] = $code;
            $data['product'] = $_POST['product'];
            $data['letter'] = $_POST['letter'];
            $data['sh_id'] = $_POST['sh_id'];
            $data['outprice'] = $_POST['outprice'];
            $data['outamount'] = $outamount;
            $data['outsum'] = $outamount * $_POST['outprice'];
            $data['people'] = $_POST['people'];
            $data['outtime'] = date("Y-m-d H:i:s");



            /*
             *  for 更新入库表的 inamount 数据
             */
            $in = D('In');
            $where['barcode'] = $code;
            $where['inamount'] = ['gt', 0];

            for ($i = 0; $i < $outamount; $i++) {
                $result = $in->field("id,inamount")->where($where)->order("id asc")->find();

                $id['id'] = $result['id'];
                $dat['inamount'] = $result['inamount'] - 1;


                $re = $in->where($id)->save($dat);
                if (!$re) {
                    $this->error("更新失败");
                    return false;
                }
            }

            /*
             * 商品出库
             * 
             */

            $resid = $in->field("id")->where($where)->order("id asc")->find();
            $data['int_id'] = $resid['id'];

            $res = $out->add($data);
            if (!$res) {
                $this->error("商品出库失败");
                return false;
            } else {
                $this->redirect("Out/index");
            }
        }
    }

    /*
     * shopID() 获取零售商的ID  货要送到哪
     * 
     */



    /*
     * 
     * ajax()   异步提交条形码 根据条形码 查出库存的商品名、总数量  然后把名称返回到前端
     *
     * 1、接收get传来的条形码 barcode
     * 
     * 2、根据 barcode 提取  tb_in 表中的    product   总的inamount
     * 
     *          
     *    如果条形码找不到该商品，提示去采购该商品
     *    
     *    如果找到商品，请选择订单的商品 ， 把选择订单的 id 传回 提取 tb_in 表的 inamount 
     * 
     * 3、在特定的位置输出对应的值
     * 
     */

    public function ajax() {

        $shop = D("Shop");
        $co = $shop->count("id");

        $sh = $shop->field("id,company")->select();



        $code = $_GET['code'];
        $out = D("In");
        $where['barcode'] = $code;
        $where['letter'] = strtoupper($code);
        $where['_logic'] = 'OR';

        $count = $out->where($where)->sum("inamount"); // 总的库存量
        $res = $out->field("id,product")->where($where)->order('id desc')->find(); // 商品名

        if (!$count) {

            echo "<div class='form-group'>
                    <label for='inputPassword3' class='col-sm-2 control-label'></label>
                    <div class='col-sm-7' ><font style='color:red;font-size:20px;margin-top:6px'>库存中没有这个商品，请先去<a href='" . U('In/add') . "'> 入库 </a>该商品</font><input type='text' style='display:none'/></div>
                </div>";
            return FALSE;
        } else {

            echo "<div class='form-group'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>商品</label>
                        <div class='col-sm-5'>
                            <div class='radio' ><input type='text' class='form-control' id='inputEmail3' placeholder='商品名' name='product' value='" . $res['product'] . "' readonly></div>
                        </div>

                    </div>
                    <div class='form-group'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>库存</label>
                        <div class='col-sm-5'>
                            <div style='font-size:18px;margin-top:6px;margin-left:6px;'> $count </div>
                        </div>

                    </div>";


            echo "<div class='form-group'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>商家</label>
                        <div class='col-sm-5'>
                        <select class='form-control' name='sh_id'>
                        
                        ";

            for ($i = 0; $i < $co; $i++) {
                echo "<option value='" . $sh[$i]['id'] . "'>" . $sh[$i]['company'] . "</option>";
            }
            echo "</select>
                </div>

                    </div>
                    
                    
                    
                    <div class='form-group' style='display: none;'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>首字母</label>
                        <div class='col-sm-5'>
                            <div class='radio' ><input type='text' class='form-control' id='inputEmail3' placeholder='首字母大写' name='letter'  readonly></div>
                        </div>

                    </div>

                    <div class='form-group'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>数量</label>
                        <div class='col-sm-5'>
                            <input type='text' class='form-control' id='inputEmail3' placeholder='数量' name='outamount' required>
                        </div>

                    </div>

                    <div class='form-group'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>单价</label>
                        <div class='col-sm-5'>
                            <input type='text' class='form-control' id='inputEmail3' placeholder='单价' name='outprice' required>
                        </div>

                    </div>
                    <div class='form-group'>
                        <label for='inputEmail3' class='col-sm-2 control-label'>负责人</label>
                        <div class='col-sm-5'>
                            <input type='text' class='form-control' id='inputEmail3' placeholder='负责人' name='people' required>
                        </div>
                    </div>
                    <div class='form-group'>
                        <div class='col-sm-offset-3 col-sm-10'>
                            <button type='submit' class='btn btn-primary btn-lg'> 出 库 </button>
                        </div>
                    </div>";
        }
    }

    /*
     * 更新出库商品
     */

    public function update() {
        $out = D("Out");

        if (!IS_POST) {
            $outid = $_GET['outid'];
            if (empty($outid)) {
                $this->error("非法操作！", U('Out /index'));
            } else {
                $where['id'] = $outid;
                $res = $out->where($where)->select();
                $this->assign("res", $res);
                $this->display();
            }
        } else {
            $where['id'] = $_POST['outid'];

            $data['people'] = $_POST['people'];
            $res = $out->where($where)->save($data);

//  echo $out->getLastSql();

            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Out/index'));
            }
        }
    }

    /*
     * 删除出库商品
     */

    public function delete() {
        $outid = $_GET['outid'];
        if (empty($outid)) {
            $this->error("非法操作！", U('Out /index'));
        } else {
            $out = D("Out");
            $where['id'] = $outid;
            $res = $out->where($where)->delete();
            if ($res) {
                $this->success("删除成功", U('Out/index'));
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
            $where['product'] = array('like', "%$str%");
            $where['encode'] = array('like', "%$str%");
            $where['barcode'] = array('like', "%$str%");
            $up = strtoupper($str);
            $where['letter'] = array('like', "%$up%");
            $where['outtime'] = array('like', "%$str%");
            $where['outamount'] = array('like', "%$str%");
            $where['outprice'] = array('like', "%$str%");
            $where['outsum'] = array('like', "%$str%");
            $where['people'] = array('like', "%$str%");
            $where['_logic'] = 'or';

            $this->assign("replace", "<font color='red'>$str</font>");
            $this->assign("str", $str);

            parent::showPage("Out", "10", $where);

            $tb = D("Out");
            $total = $tb->where($where)->sum("outsum");
            $this->assign("total", $total);

            $this->display();
        }
    }

    /*
     * test()
     */

    public function test() {
        echo "46466";
        $this->display();
    }

}
