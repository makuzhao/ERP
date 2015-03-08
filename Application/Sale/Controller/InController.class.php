<?php

/**
 * 入库管理
 */

namespace Home\Controller;

use Think\Page;
use Common\Controller\AuthController;

class InController extends AuthController {
    /*
     * 显示所有入库商品
     */

    public function index() {
        $in = D("In");
        $res = $in->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);
            
            $total = $in->sum("inamount");
            $this->assign("total", $total);

            parent::showPage("In", "10");
        }


        $this->display();
    }

    /*
     * 添加入库商品   关联商品表    */

    public function add() {
        if (!IS_POST) {
            $this->goods();
            $this->display();
        } else {
            $in = D("In");
            $data['encode'] = "IN" . time();
            $data['barcode'] = $_POST['barcode'];
            $data['letter'] = $_POST['letter'];
            $data['product'] = $_POST['product'];
            $data['intime'] = date("Y-m-d H:i:s");
            $data['inamount'] = $_POST['inamount'];
            $data['inprice'] = $_POST['inprice'];
            $data['insum'] = $_POST['inamount'] * $_POST['inprice'];
            $data['people'] = $_POST['people'];



            $res = $in->add($data);
            if ($res) {
                $this->success("添加入库商品成功 ！！", U('In/index'));
            } else {
                $this->error("添加入库商品失败 ？？");
            }
        }
    }

    /*
     * 商品表
     */

    public function goods() {
        $gods = D('Goods');
        $result = $gods->select();
        $this->assign("gods", $result);
    }

    /*
     * 更新入库商品
     */

    public function update() {
        $in = D("In");

        if (!IS_POST) {
            $inid = $_GET['inid'];
            if (empty($inid)) {
                $this->error("非法操作！", U('In/index'));
            } else {
                $where['id'] = $inid;
                $res = $in->where($where)->select();
                $this->assign("res", $res);
                $this->display();
            }
        } else {
            $where['id'] = $_POST['inid'];
// $data['encode'] = $_POST['encode'];
// $data['product'] = $_POST['product'];
// $data['intime'] = date("Y-m-d H:i:s");
            $data['inamount'] = $_POST['inamount'];
            $data['inprice'] = $_POST['inprice'];
            $data['insum'] = $_POST['inamount'] * $_POST['inprice'];
            $data['people'] = $_POST['people'];
            $res = $in->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('In/index'));
            }
        }
    }

    /*
     * 删除入库商品
     */

    public function delete() {
        $inid = $_GET['inid'];
        if (empty($inid)) {
            $this->error("非法操作！", U('In/index'));
        } else {
            $in = D("In");
            $where['id'] = $inid;
            $res = $in->where($where)->delete();
            if ($res) {
                $this->success("删除成功", U('In/index'));
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
            $str = $_GET['search'];
            $where['encode'] = array('like', "%$str%");
            $up = strtoupper($str);
            $where['letter'] = array('like', "%$up%");
            $where['incode'] = array('like', "%$str%");
            $where['pro'] = array('like', "%$str%");
            $where['intime'] = array('like', "%$str%");
            $where['inprice'] = array('like', "%$str%");
            $where['inamount'] = array('like', "%$str%");
            $where['insum'] = array('like', "%$str%");
            $where['people'] = array('like', "%$str%");
            $where['_logic'] = 'or';

            $this->assign("replace", "<font color='red'>$str</font>");
            $this->assign("str", $str);

            parent::showPage("InView", "10", $where, "intime desc");

            $tb = D("InView");
            $total = $tb->where($where)->sum("insum");
            $this->assign("total", $total);


            $this->display();
        }
    }

    /*
     * ajax 异步提交条形码 根据条形码 查出商品名  然后把名称返回到前端
     */

    public function ajax() {
        if ($_GET['code'] != "") {
            $barcode = $_GET['code'];
//exit();
            $goods = D('Goods');
            $where['barcode'] = $barcode;
            $where['letter'] = strtoupper($barcode);
            $where['_logic'] = 'or';
            $res = $goods->field("id,product")->where($where)->order("id asc")->find();



            if (!$res) {
                echo " <div class='form-group'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>商品</label>
                        <div class='col-sm-5'>
                            <div class='radio' style='margin-top:-5px;'><font style='color: red;font-size:20px;'>暂时还没有该商品，请先去<a href=" . U('Goods/add') . ">  添加商品 </a></font> </div>
                        </div>
                        <input type='text' style='display:none'/>
                        
                    </div>";
            } else {
                echo "<div class='form-group'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>商品</label>
                        <div class='col-sm-5'>
                            <div class='radio' ><input type='text' class='form-control' id='inputEmail3' placeholder='商品名' name='product' value='" . $res['product'] . "' readonly></div>
                        </div>

                    </div>
                    
                    <div class='form-group' style='display: none;'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>首字母</label>
                        <div class='col-sm-5'>
                            <div class='radio' ><input type='text' class='form-control' id='inputEmail3' placeholder='首字母大写' name='letter' value='" . $res['letter'] . "' readonly></div>
                        </div>

                    </div>

                    <div class='form-group'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>数量</label>
                        <div class='col-sm-5'>
                            <input type='text' class='form-control' id='inputEmail3' placeholder='数量' name='inamount' required>
                        </div>

                    </div>

                    <div class='form-group'>
                        <label for='inputPassword3' class='col-sm-2 control-label'>单价</label>
                        <div class='col-sm-5'>
                            <input type='text' class='form-control' id='inputEmail3' placeholder='单价' name='inprice' required>
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
                            <button type='submit' class='btn btn-primary btn-lg'> 入 库 </button>
                        </div>
                    </div>";
            }
        } else {
            $this->display();
        }
    }

}
