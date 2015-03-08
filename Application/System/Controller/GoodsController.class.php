<?php

/**
 *  商品基本属性
 */

namespace System\Controller;

use Common\Controller\AuthController;

class GoodsController extends AuthController {
    /*
     * 显示所有商品
     */

    public function index() {
        $goods = D("Goods");
        $res = $goods->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);

            parent::showPage("Goods", "10");
        }
        $total = $goods->count("id");
        $this->assign("total", $total);


        $this->display();
    }

    /*
     * 添加商品     */

    public function add() {
        if (!IS_POST) {
            $this->type();
            $this->com();
            $this->display();
        } else {
            $goods = D("Goods");
            $data['product'] = $_POST['product'];
            $data['letter'] = $_POST['letter'];
            $data['type'] = $_POST['type'];
            $data['com_id'] = $_POST['com_id'];
            $data['barcode'] = $_POST['barcode'];
            $data['price'] = $_POST['price'];
            $data['standard'] = $_POST['standard'];
            $data['unit'] = $_POST['unit'];
            $data['info'] = $_POST['info'];

            $res = $goods->add($data);
            if ($res) {
                $this->success("添加商品成功 ！！", U('Goods/index'));
            } else {
                $this->error("添加商品失败 ？？");
            }
        }
    }

    /*
     * 更新商品
     */

    public function update() {
        $goods = D("Goods");

        if (!IS_POST) {
            $goid = $_GET['goid'];
            if (empty($goid)) {
                $this->error("非法操作！", U('Goods/index'));
            } else {
                $where['id'] = $goid;
                $res = $goods->where($where)->select();
                $this->assign("res", $res);
                $this->type();
                $this->com();
                $this->display();
            }
        } else {
            $where['id'] = $_POST['goid'];
            $data['product'] = $_POST['product'];
            $data['letter'] = $_POST['letter'];
            $data['type'] = $_POST['type'];
            $data['com_id'] = $_POST['com_id'];
            $data['barcode'] = $_POST['barcode'];
            $data['standard'] = $_POST['standard'];
            $data['price'] = $_POST['price'];
            $data['unit'] = $_POST['unit'];
            $data['info'] = $_POST['info'];
            $res = $goods->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Goods/index'));
            }
        }
    }

    /*
     * 获取商品类型
     */

    private function type() {
        $type = D('Type');
        $ty = $type->select();
        $this->assign("go", $ty);
    }

    /*
     * 获取生产商
     */

    private function com() {
        $type = D('Company');
        $ty = $type->select();
        $this->assign("com", $ty);
    }

    /*
     * 删除商品
     */

    public function delete() {
        $goid = $_GET['goid'];
        if (empty($goid)) {
            $this->error("非法操作！", U('Goods/index'));
        } else {
            $goods = D("Goods");
            $where['id'] = $goid;
            $res = $goods->where($where)->delete();
            if ($res) {
                $this->success("删除成功", U('Goods/index'));
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
            $bar = trim($_GET['bar']);
            if ($bar != "") {

                $where['barcode'] = array('like', "%$bar%");
                $this->assign("bar", $bar);
            }

            $pro = trim($_GET['pro']);
            if ($pro != "") {

                $where['product'] = array('like', "%$pro%");
                $this->assign("pro", $pro);
            }

            $let = trim($_GET['let']);
            if ($let != "") {
                $let = strtoupper($let);
                $where['letter'] = array('like', "%$let%");
                $this->assign("let", $let);
            }

            $type = trim($_GET['type']);
            if ($type != "") {

                $where['type'] = array('like', "%$type%");
                $this->assign("type", $type);
            }

            $stand = trim($_GET['stand']);
            if ($stand != "") {

                $where['standard'] = array('like', "%$stand%");
                $this->assign("stand", $stand);
            }

            $unit = trim($_GET['unit']);
            if ($unit != "") {

                $where['unit'] = array('like', "%$unit%");
                $this->assign("unit", $unit);
            }

            $price = trim($_GET['price']);
            if ($price != "") {

                $where['price'] = array('like', "%$price%");
                $this->assign("price", $price);
            }

            $info = trim($_GET['info']);
            if ($info != "") {

                $where['info'] = array('like', "%$info%");
                $this->assign("info", $info);
            }


            parent::showPage("Goods", "10", $where);

            /*
             * 统计商品数量
             */
            $total = D('Goods')->where($where)->count("id");
            $this->assign("total", $total);


            $this->display();
        }
    }

}
