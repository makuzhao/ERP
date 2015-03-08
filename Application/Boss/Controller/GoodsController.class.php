<?php

/**
 *  商品基本属性 条形码唯一性存在
 */

namespace Boss\Controller;

use Common\Controller\AuthController;
use Think\Model;

class GoodsController extends AuthController {
    /*
     * 显示主库的商品信息，boss 只能拥有 查看，添加，搜索的权限
     */

    public function erpIndex() {

        parent::showPage("Goods");


        /*
         * 单子数
         */
        $id = D('Goods')->where($where)->count("id");
        $this->assign("id", $id);

        $shop_id = $_GET['shopid'];

        if ($shop_id == "") {
            $shop_id = $_SESSION["shop_id"];
        }
        if ($shop_id == "") {
            $this->error("请先点击具体的店铺，再回来点此！");
        }
        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);

        $this->assign("shopid", $shop_id);

        $this->display();
    }

    public function erpSearch() {

        if (!IS_GET) {

            $shopName = $_SESSION["shop_name"];
            $this->assign("shopName", $shopName);

            $this->display();
        } else {
            $bar = trim($_GET['barcode']);
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

            $type = trim($_POST['type']);
            if ($type != "") {

                $where['type'] = array('like', "%$type%");
                $this->assign("type", $type);
            }

            $stand = trim($_POST['standard']);
            if ($stand != "") {

                $where['standard'] = array('like', "%$stand%");
                $this->assign("stand", $stand);
            }

            $unit = trim($_POST['unit']);
            if ($unit != "") {

                $where['unit'] = array('like', "%$unit%");
                $this->assign("unit", $unit);
            }


            $price = trim($_POST['price']);
            if ($price != "") {

                $where['price'] = array('like', "%$price%");
                $this->assign("price", $price);
            }


            $info = trim($_POST['info']);
            if ($info != "") {

                $where['info'] = array('like', "%$info%");
                $this->assign("info", $info);
            }


            //$where['_logic'] = 'or';

            parent::showPage("Goods", "10", $where);

            $id = D('Goods')->where($where)->count("id");
            $this->assign("id", $id);

            $this->display();
        }
    }

    /*
     * erpAdd()  直接从主库添加商品
     * 
     * 1、添加到本地 tb_goods
     * 
     * 2、添加到本地 tb_storage
     * 
     */

    public function erpAdd() {




        $goid = $_GET['goid'];
        $gods = D("Goods");
        if (IS_GET) {
            /*
             * 检查本地库是否已有该商品
             */
            $whgo['id'] = $goid;
            $resgo = $gods->where($whgo)->select();
            $resgo[0]['barcode'];

            $shop_id = $_GET['shopid'];
            if ($shop_id == "") {
                $shop_id = $_SESSION["shop_id"];
            }
            if ($shop_id == "") {
                $this->error("请先点击具体的店铺，再回来点此！");
            }
            $shopName = $_SESSION["shop_name"];
            $this->assign("shopName", $shopName);

            $this->assign("shopid", $shop_id);

            $dbgo = session("dbName") . "." . $shop_id . "Goods";
            $gos = M($dbgo);
            $w['barcode'] = $resgo[0]['barcode'];
            $r = $gos->field("id")->where($w)->find();


            if ($r) {
                $this->error("本店商品信息库已有该商品的信息", U('Goods/index', array('shopid' => $shop_id)));
            } else {


                $this->assign("resgo", $resgo);
                $this->display();
            }
        }
        if (IS_POST) {
            /*
             *  数据入本地 tb_goods
             */
            $shop_id = $_POST['shopid'];
            $this->assign("shopid", $shop_id);

            $wh['id'] = trim($_POST['outid']);
            $re = $gods->where($wh)->find();
            $da['barcode'] = $re['barcode'];
            $da['product'] = $re['product'];
            $da['letter'] = $re['letter'];
            $da['type'] = $re['type'];
            $da['standard'] = $re['standard'];
            $da['unit'] = $re['unit'];
            $da['price'] = $re['price'];
            $da['info'] = $re['info'];



            $db = session("dbName") . "." . $shop_id . "Goods";
            $go = M($db);
            $result = $go->add($da);


            /*
             *  数据入本地 tb_storage
             */
            $data['encode'] = "OUT" . time();
            $data['barcode'] = trim($_POST['barcode']);
            $data['product'] = trim($_POST['product']);
            $data['letter'] = trim($_POST['letter']);
            $outamount = trim($_POST['outamount']);

            $data['outprice'] = trim($_POST['outprice']);
            $data['outamount'] = $outamount;
            $data['outsum'] = $outamount * $_POST['outprice'];
            $data['outpresell'] = trim($_POST['outpresell']);
            $data['people'] = $_SESSION['nameReal'];
            $data['outtime'] = date("Y-m-d H:i:s");
            $db_name = session("dbName") . "." . $shop_id . "Storage";
            $out = M($db_name);
            $res = $out->add($data);
            if ($res) {
                $this->success("入库成功", U('Out/index', array("shopid" => $shop_id)));
            } else {
                $this->success("入库失败");
            }
        }
    }

    /*
     * 查看主库商品信息
     */

    public function erpLook() {
// $db_name = session("dbName") . ".Goods";
        $goods = D("Goods");
        $id = $_GET['id'];
        $where['barcode'] = $id;
        $res = $goods->where($where)->order("id asc")->limit(0, 1)->select();
        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);
        $this->assign("look", $res);

        if (!$res) {
            $this->error("不存在这个条形码对应的商品 ？？");
        } else {
            $this->assign("look", $res);
        }
        $this->display();
    }

    /*
     * 查看主库商家信息
     */

    public function erpCom() {

        $goods = D("Company");

        $id = $_GET['id'];
        $where['id'] = $id;

        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);


        $res = $goods->where($where)->select();

        if (!$res) {
            $this->error("不存在这个这个商家 ？？");
        } else {

            $this->assign("company", $res);
            $this->display();
        }
    }

    /*
     * 添加主库商品信息
     */

    public function erpGoods() {

        $type = D('Type');
        $ty = $type->select();
        $this->assign("go", $ty);
        $com = D('Company');
        $company = $com->select();
        $this->assign("com", $company);


        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);


        $this->display();
        if (IS_POST) {
            $goods = D("Goods");
            $data['product'] = $_POST['product'];
            $data['letter'] = $_POST['letter'];
            $data['type'] = $_POST['type'];
            $data['com_id'] = $_POST['com_id'];
            $data['barcode'] = $_POST['barcode'];
            $data['standard'] = $_POST['standard'];
            $data['unit'] = $_POST['unit'];
            $data['info'] = $_POST['info'];
            $data['price'] = $_POST['price'];

            $res = $goods->add($data);
            if ($res) {
                $this->success("添加商品成功 ！！", U('Goods/erpIndex'));
            } else {
                $this->error("添加商品失败 ？？");
            }
        }
    }

    /*
     * erpBar（）检测条形码是否已经存在主库中 
     */

    public function erpBar() {

        $bar = trim($_GET['code']);
        if ($bar != "") {
            /*
             * 主地商品表检测
             */
            $go = D('Goods');
            $where['barcode'] = $bar;
            $gores = $go->field("id")->where($where)->select();

            if ($gores != "") {
                echo "<font style='color:red;font-size:20px;margin-top:6px'>主库中已经存在这个条形码 : $bar</font>";
            }
        }
    }

    /*
     * 显示某个店铺的自有商品的所有商品
     * 统计数量
     */

    public function index() {
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $shop_id = $_GET['shopid'];

        if ($shop_id == "") {
            $shop_id = $_SESSION["shop_id"];
        }
        if ($shop_id == "") {
            $this->error("请先点击具体的店铺，再回来点此！");
        }
        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);

        $this->assign("shopid", $shop_id);

        $goods = D($shop_id . "Goods");
        $res = $goods->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);

            parent::showPage($shop_id . "Goods", "10");
        }

        $id = D($shop_id . 'Goods')->where($where)->count("id");
        $this->assign("id", $id);

        $this->display();
    }

    /*
     * 添加本地商品  
     *    
     */

    public function add() {
        if (!IS_POST) {
            $shop_id = $_GET['shopid'];
            if ($shop_id == "") {
                $shop_id = $_SESSION["shop_id"];
            }
            if ($shop_id == "") {
                $this->error("请先点击具体的店铺，再回来点此！");
            }
            $shopName = $_SESSION["shop_name"];
            $this->assign("shopName", $shopName);
            $this->assign("shopid", $shop_id);
            $this->checkBar();

            $this->display();
        } else {
            $model = new Model();
            $sql = "use  " . $_SESSION['dbName'];
            $model->query($sql);
            $shop_id = $_POST['shopid'];

            $goods = D($shop_id . "Goods");
            $data['product'] = $_POST['product'];
            $data['letter'] = $_POST['letter'];
            $data['type'] = $_POST['type'];
            //$data['com_id'] = $_POST['com_id'];
            $data['barcode'] = $_POST['barcode'];
            $data['standard'] = $_POST['standard'];
            $data['unit'] = $_POST['unit'];
            $data['price'] = $_POST['price'];
            $data['info'] = $_POST['info'];

            $res = $goods->add($data);
            if ($res) {
                $this->success("添加商品成功 ！！", U('Goods/index', array('shopid' => $shop_id)));
            } else {
                $this->error("添加商品失败 ？？");
            }
        }
    }

    /*
     * 更新商品
     */

    public function update() {
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);


        if (!IS_POST) {
            $goid = $_GET['goid'];
            $shop_id = $_GET['shopid'];

            if ($shop_id == "") {
                $shop_id = $_SESSION["shop_id"];
            }
            if ($shop_id == "") {
                $this->error("请先点击具体的店铺，再回来点此！");
            }
            $shopName = $_SESSION["shop_name"];
            $this->assign("shopName", $shopName);

            if (empty($goid)) {
                $this->error("非法操作！", U('Goods/index', array('shopid' => $shop_id)));
            } else {

                $shop_id = $_GET['shopid'];

                if ($shop_id == "") {
                    $shop_id = $_SESSION["shop_id"];
                }
                if ($shop_id == "") {
                    $this->error("非法操作！");
                }
                $shopName = $_SESSION["shop_name"];
                $this->assign("shopName", $shopName);

                $this->assign("shopid", $shop_id);
                $goods = D($shop_id . "Goods");
                $where['id'] = $goid;
                $res = $goods->where($where)->select();
                $this->assign("res", $res);
//$this->type();
//$this->com();
                $this->display();
            }
        } else {
            $shop_id = $_POST['shopid'];

            if ($shop_id == "") {
                $shop_id = $_SESSION["shop_id"];
            }
            if ($shop_id == "") {
                $this->error("请先点击具体的店铺，再回来点此！");
            }
            $shopName = $_SESSION["shop_name"];
            $this->assign("shopName", $shopName);

            $goods = D($shop_id . "Goods");

            $where['id'] = $_POST['goid'];
            $data['product'] = $_POST['product'];
            $data['letter'] = $_POST['letter'];
            $data['type'] = $_POST['type'];
            //$data['com_id'] = $_POST['com_id'];
            $data['barcode'] = $_POST['barcode'];
            $data['standard'] = $_POST['standard'];
            $data['unit'] = $_POST['unit'];
            $data['info'] = $_POST['info'];
            $data['price'] = trim($_POST['price']);
            $res = $goods->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Goods/index', array('shopid' => $shop_id)));
            }
        }
    }

    /*
     * 校验商品是否已经在店铺的自有商品表中
     */

    public function checkBar() {


        $bar = trim($_GET['code']);

        if ($bar != "") {
            /*
             * 本地商品表检测
             */
            $model = new Model();
            $sql = "use  " . $_SESSION['dbName'];
            $model->query($sql);
            $shop_id = $_GET['shopid'];

            if ($shop_id == "") {
                $shop_id = $_SESSION["shop_id"];
            }
            if ($shop_id == "") {
                $this->error("请先点击具体的店铺，再回来点此！");
            }
            $shopName = $_SESSION["shop_name"];
            $this->assign("shopName", $shopName);

            $go = D($shop_id . 'Goods');
            $where['barcode'] = $bar;
            $gores = $go->field("id")->where($where)->find();
            if ($gores != "") {
                echo "<div class='form-group'>
                    <label for='inputEmail3' class='col-sm-3 control-label'>商品名</label>
                    <div class='col-sm-5'>";
                echo "<font style='color:red;font-size:20px;margin-top:6px'>本商店已有这个商品</font>";
                echo " </div>
                </div>";
            } else {
                /*
                 * 主库商品表检测
                 * 
                 * A、没有数据 -》去主库添加
                 * 
                 * B、有数据 -》显示信息 -》添加到本地库
                 * 
                 */
                $model1 = new Model();
                $sql1 = "use  db_erp";
                $model1->query($sql1);


                $go1 = D('Goods');
                $where1['barcode'] = $bar;
                $gores1 = $go1->field("*")->where($where1)->find();

                if ($gores1 == "") {

                    echo "<div class='form-group'>
                    <label for='inputEmail3' class='col-sm-3 control-label'>商品名</label>
                    <div class='col-sm-5'>";
                    echo "<font style='color:red;font-size:20px;margin-top:6px'>主库商品信息中没有这个商品，请去&nbsp;&nbsp;";
                    echo "<a href='" . U('Goods/erpGoods') . "'>添加</a></font>";
                    echo " </div>
                </div>";
                } else {



                    echo "<div class='form-group'>
                    <label for='inputEmail3' class='col-sm-3 control-label'>商品名</label>
                    <div class='col-sm-3'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='商品名' name='product' value='" . $gores1['product'] . "' readonly>
                    </div>
                </div>";

                    echo "<div class='form-group'>
                    <label for='inputEmail3' class='col-sm-3 control-label'>首字母</label>
                    <div class='col-sm-3'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='首字母大写，例：三星 SX' name='letter' value='" . $gores1['letter'] . "' readonly>
                    </div>
                </div>";

                    echo "<div class='form-group'>
                    <label for='inputPassword3' class='col-sm-3 control-label'>类型</label>
                    <div class='col-sm-3'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='类型' name='type' value='" . $gores1['type'] . "' readonly>

                    </div>

                </div>";


                    echo "<div class='form-group'>
                    <label for='inputEmail3' class='col-sm-3 control-label'>规格</label>
                    <div class='col-sm-3'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='规格' name='standard' value='" . $gores1['standard'] . "' readonly>
                    </div>
                </div>";

                    echo "<div class='form-group'>
                    <label for='inputEmail3' class='col-sm-3 control-label' >计量单位</label>
                    <div class='col-sm-3'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='计量单位，例：台' name='unit' value='" . $gores1['unit'] . "' readonly>
                    </div>
                </div>";
                    echo "<div class='form-group'>
                    <label for='inputEmail3' class='col-sm-3 control-label' >建议售价</label>
                    <div class='col-sm-3'>
                        <input type='text' class='form-control' id='inputEmail3' placeholder='建议售价' name='price' value='" . $gores1['price'] . "' readonly>
                    </div>
                </div>";
                    echo " <div class='form-group'>
                    <label for='inputEmail3' class='col-sm-3 control-label'>商品描述</label>
                    <div class='col-sm-3'>
                        <textarea class='form-control' name='info' rows='5' readonly>" . strip_tags($gores1['info']) . "</textarea>
                    </div>
                </div> ";

                    echo "<div class='form-group'>
                    <div class='col-sm-offset-4 col-sm-3'>
                        <button type='submit' class='btn btn-primary btn-lg'> 添 加 </button>
                    </div>
                </div>";
                }
            }
        } else {
            /*
              echo "<div class='form-group'>
              <label for='inputEmail3' class='col-sm-3 control-label'></label>
              <div class='col-sm-3'>
              请输入完整的条形码或首字母
              </div>
              </div>
              ";
             * */
            echo "";
        }
    }

    /*
     * 删除本地商品
     */

    public function delete() {
        $model = new Model();
        $sql = "use  " . $_SESSION['dbName'];
        $model->query($sql);

        $goid = $_GET['goid'];
        $shop_id = $_GET['shopid'];

        if ($shop_id == "") {
            $shop_id = $_SESSION["shop_id"];
        }
        if ($shop_id == "") {
            $this->error("请先点击具体的店铺，再回来点此！");
        }
        $shopName = $_SESSION["shop_name"];
        $this->assign("shopName", $shopName);


        if (empty($goid)) {
            $this->error("非法操作！", U('Goods/index', array('shopid' => $shop_id)));
        } else {
            $goods = D($shop_id . "Goods");
            $where['id'] = $goid;
            $res = $goods->where($where)->delete();
            if ($res) {
                $this->success("删除成功", U('Goods/index', array('shopid' => $shop_id)));
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /*
     * 用户搜索 searching（）  店铺的自有商品
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
            $shop_id = $_POST['shopid'];

            if ($shop_id == "") {
                $shop_id = $_SESSION["shop_id"];
            }
            if ($shop_id == "") {
                $this->error("请先点击具体的店铺，再回来点此！");
            }
            $shopName = $_SESSION["shop_name"];
            $this->assign("shopName", $shopName);

            $bar = trim($_POST['barcode']);
            if ($bar != "") {

                $where['barcode'] = array('like', "%$bar%");
                $this->assign("bar", $bar);
            }

            $pro = trim($_POST['product']);
            if ($pro != "") {

                $where['product'] = array('like', "%$pro%");
                $this->assign("pro", $pro);
            }

            $let = trim($_POST['letter']);
            if ($let != "") {
                $let = strtoupper($let);
                $where['letter'] = array('like', "%$let%");
                $this->assign("let", $let);
            }

            $type = trim($_POST['type']);
            if ($type != "") {

                $where['type'] = array('like', "%$type%");
                $this->assign("type", $type);
            }

            $stand = trim($_POST['standard']);
            if ($stand != "") {

                $where['standard'] = array('like', "%$stand%");
                $this->assign("stand", $stand);
            }

            $unit = trim($_POST['unit']);
            if ($unit != "") {

                $where['unit'] = array('like', "%$unit%");
                $this->assign("unit", $unit);
            }


            $price = trim($_POST['price']);
            if ($price != "") {

                $where['price'] = array('like', "%$price%");
                $this->assign("price", $price);
            }


            $info = trim($_POST['info']);
            if ($info != "") {

                $where['info'] = array('like', "%$info%");
                $this->assign("info", $info);
            }


            // $where['_logic'] = 'or';

            parent::showPage($shop_id . "Goods", "10", $where);
            $this->assign("shop", U('Goods/index', array('shopid' => $shop_id)));

            $id = D($shop_id . 'Goods')->where($where)->count("id");
            $this->assign("id", $id);

            $this->display();
        }
    }

}
