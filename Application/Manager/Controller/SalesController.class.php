<?php

/*
 * 
 * 商品销售 ： 查询、查看、 添加、 删除 
 * 
 * 
 */

namespace Manager\Controller;

use Common\Controller\AuthController;

class SalesController extends AuthController {
    /*
     * 显示所有销出的商品
     */

    public function index() {
        $out = D("Sales");
        $res = $out->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);
            parent::showPage("Sales", "10");
        }
        /*
         * 金额
         */
        $total = $out->sum("amount");
        $this->assign("total", $total);
        /*
         * 数量
         */
        $num = $out->sum("num");
        $this->assign("num", $num);
        /*
         * 单子数
         */
        $id = $out->count("id");
        $this->assign("id", $id);

        $this->display();
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
        if (!$res) {
            $this->error("不存在这个条形码 ？？");
        } else {
            $this->assign("look", $res);
        }
        $this->display();
    }

    /*
     * add() 添加销售的商品    同时修改 tb_out 表中的总的数量相减   注意 POST GET 前后判断顺序 的干扰
     * 
     * 1、接收 ajax get方式传来的条形码 barcode
     * 
     * 2、根据 barcode 提取 预售价表 tb_presell  表中的 所有数据   
     * 
     *          
     *    如果条形码不存在，说明找不到该商品，提示去上架该商品
     *    
     *    如果找到商品，在特定的位置输出对应的值
     * 
     * 3、提交数据
     * 
     * 4、获取 barcode  num 这两个值，
     * 
     * 5、根据 barcode outamount>0 这两个条件去查询 tb_out ，ID asc 排列 ,获得一条数据（多条数据最早的一条）中的 id outamount
     * 
     * 6、（outamount - num = ?）  update 字段 outamount 的值 where id = （5中的结果集 id）
     * 
     * 7、如果6、更新成功，则添加所有提交的数据到 tb_sales 表中,附加条件：tb_out 中的id 也要入库 到 out_id 字段
     * 
     * 8、购物车功能，只有按了返回按钮才能返回 index 页面，否则就是 add 成功也是跳转到 add 页面，只有按了结算按钮才能数据循环插入表
     * 
     */

    public function add() {
        $out = D("Sales");
        /*
         * 本店今日营业额
         */
        $s = D("Sales");
        $day = date("Y-m-d");
        $wh['saledate'] = array("like", "%$day%");
        $total = $s->where($wh)->count("amount");
        $this->assign("total", $total);

        /*
         *  关键字：条形码、商品大写首字母
         */
        if (@$_GET['code'] != "") {
            $_SESSION['barsess'] = time();
            $barcode = $_GET['code'];

            $presell = D("Presell");
            $pre['barcode'] = $barcode;
            $pre['letter'] = $barcode;
            $pre['_logic'] = "OR";
            $bar = $presell->where($pre)->find();
            $barid = $bar['id'];
            $barnum = $bar['num'];
            $barpre = $bar['presell'];

            $barsale = $_SESSION['auth'];

            if ($bar) {
                echo "<div class='form-group'> <label for='inputPassword3' class='col-sm-2 control-label'>销售数量</label>  <div class='col-sm-5'> <input type='text' id='num'  class='form-control'  placeholder='数量' name='num' value='" . $barnum . "'  required> </div> </div>";

                echo "<div class='form-group'> <label for='inputPassword3' class='col-sm-2 control-label'>销售价格</label>  <div class='col-sm-5'> <input type='text' class='form-control'  placeholder='单价' name='presell' value='" . $barpre . "'   required> </div> </div>";
                echo "<div class='form-group'> <label for='inputPassword3' class='col-sm-2 control-label'>销售人员</label>  <div class='col-sm-5'> <input type='text' class='form-control'  placeholder='默认是右上角的当前用户' name='salesman' value='" . $barsale . "'  </div> </div>";
                echo "<div class='form-group'>
                        <div class='col-sm-offset-3 col-sm-10' style='margin-top: 20px;'>
                          <button type='button' class='btn btn-primary btn-lg' id='submit' onclick='showdiv()' > 销售 </button>
                        </div>
                      </div>";
            } else {
                echo "<div class='form-group'> <label for='inputPassword3' class='col-sm-2 control-label'>错误提示</label>  <div class='col-sm-5' style='margin-top: 3px;' ><font style='color:red;font-size:20px;'>该商品已经下架，请去 <a href='" . U("Manager/Presell/add") . "'> 添加 </a> 数据即可销售</font></div></div>";
            }
        } else {

            $this->display();
        }
    }

    public function shows() {


        if (IS_POST) {
            /*
             * 1、根据 barcode outamount>0 查 tb_out 表的 id outamount 
             * 
             * 2、相减
             * 
             * 3、插入 tb_sales 数据
             * 
             * 4、更新 tb_out 数据
             * 
             * 5、弹出一个页面 显示 商品的单笔信息，今日收益
             */


            $out = D("Out");
            $barcode = $_POST['barcode'];
            if ($barcode == "") {
                $this->error("操作失误");
            } else {
                $salenum = $_POST['num'];

                /*
                 *  获取提交的数据
                 */
                $goods = D('Goods');
                $letbar = strtoupper($barcode);
                $gg['barcode'] = $barcode;
                $gg['letter'] = $letbar;
                $gg['_logic'] = "OR";
                $pro = $goods->field("id,barcode,product")->where($gg)->find();
                $barcode = $pro['barcode'];
                $data['product'] = $pro['product'];
                $data['barcode'] = $pro['barcode'];
                $data['num'] = $salenum;
                $data['presell'] = $_POST['presell'];
                $data['amount'] = $salenum * $_POST['presell'];
                $data['salesman'] = $_POST['salesman'];
                $data['saledate'] = date("Y-m-d H:i:s");




                $barout['barcode'] = $barcode;
                $barout['outamount'] = ['gt', 0];



                /*
                 *  根据 销售数量 循环满足条件查询 更新 outamount
                 */
                for ($i = 0; $i < $salenum; $i++) {
                    $resout = $out->field("id,outamount")->where($barout)->order('id asc')->find();

                    $where['id'] = $resout['id'];
                    $outdata['outamount'] = $resout['outamount'] - 1;
// exit();
                    $res = $out->where($where)->save($outdata);
                    if (!$res) {
                        $this->error("更新失败");
                    }
                }
            }
            /*
             *   插入 tb_sales 的每条数据要知道从那张单子减出来的
             */

            $resid = $out->field("id")->where($barout)->order('id asc')->find();
            $data['out_id'] = $resid['id'];

            $sales = D('Sales');
            $ressale = $sales->add($data);
            if (!$ressale) {
                $this->error("该商品销售失败");
                return FALSE;
            }
            // 插入数据成功之后，要把信息显示到遮罩层：商品 数量 价格 销售额 今日该商品的销售额

            echo "<div class='form-group'> <label for='inputPassword3' class='col-sm-2 control-label'>商品</label>  <div class='col-sm-5'> <input type='text'   class='form-control'    value='" . $pro['product'] . "'  readonly> </div> </div>";
            echo "<div class='form-group'> <label for='inputPassword3' class='col-sm-2 control-label'>数量</label>  <div class='col-sm-5'> <input type='text'   class='form-control'   value='" . $data['num'] . "'  readonly> </div> </div>";
            echo "<div class='form-group'> <label for='inputPassword3' class='col-sm-2 control-label'>价格</label>  <div class='col-sm-5'> <input type='text'   class='form-control'    value='" . $data['presell'] . "'  readonly> </div> </div>";
            echo "<div class='form-group'> <label for='inputPassword3' class='col-sm-2 control-label'>销售额</label>  <div class='col-sm-5'> <input type='text'   class='form-control'   value='" . $salenum * $_POST['presell'] . "'  readonly> </div> </div>";

            /*
             * 特定销售员的今日销售额 模糊查询
             * 
             * $today
             * $salesman
             * $saletotal
             * 
             */
            $today = date("Y-m-d");
            $sawhere['saledate'] = array("like", "%$today%");
            $sawhere['salesman'] = $_POST['salesman'];
            $pretotal = $sales->where($sawhere)->count("amount");
            echo "<div style='float: right;height: 300px;width: 280px;font-size: 50px;color: red; text-align: center;margin-top:-208px;'>                     
                        " . $_POST['salesman'] . "<br>
                       营业额(￥) <br> $pretotal
                    </div>";
        }
    }

    /*
     * ajaxId ()  查询库存量
     *  
     * 1、获取 get 传来的id 
     * 
     * 2、根据 id 查询 inamount
     * 
     * 3、指定位置输出
     * 
     */

    public function ajaxId() {
        $id = $_GET['id'];
        $where['id'] = $id;
        $in = D('In');
        $res = $in->field("inamount")->where($where)->find();
        $str = "<input type='hidden' name='id' value='$id'><input type='text' class='form-control'  placeholder='库存量' value='" . $res['inamount'] . "'  readonly >";
        echo $str;
    }

    /*
     * 更新
     *

      public function update() {
      $company = D("Sales");

      if (!IS_POST) {
      $comid = $_GET['outid'];
      if (empty($comid)) {
      $this->error("非法操作！", U('Sales/index'));
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
      $this->success("数据更新成功 ！！", U('Sales/index'));
      }
      }
      }

     * 
     * 
     * 删除出库商品
     *
     * 


      public function delete() {
      $outid = $_GET['outid'];
      if (empty($outid)) {
      $this->error("非法操作！", U('Sales /index'));
      } else {
      $out = D("Sales");
      $where['id'] = $outid;
      $res = $out->where($where)->delete();
      if ($res) {
      $this->success("删除成功", U('Sales/index'));
      } else {
      $this->error("删除失败！");
      }
      }
      }
     */
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
            $where['product'] = array('like', "%$str%");
            $where['num'] = array('like', "%$str%");
            $where['amount'] = array('like', "%$str%");
            $where['salesman'] = array('like', "%$str%");
            $where['presell'] = array('like', "%$str%");
            $where['saledate'] = array('like', "%$str%");
            $where['_logic'] = 'or';

            $this->assign("replace", "<font color='red'>$str</font>");
            $this->assign("str", $str);

            parent::showPage("Sales", "10", $where);
            $tb = D("Sales");
            $total = $tb->where($where)->sum("amount");
            $this->assign("total", $total);

            $this->display();
        }
    }

    /*
     * test()
     */

    public function test() {
        echo "ID:" . $id = $_GET['id'];

        $this->display();
    }

}
