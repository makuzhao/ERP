<?php

/**
 *  商品基本属性
 */

namespace Sale\Controller;

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


        $this->display();
    }

    /*
     * 添加商品   
     * 
     * 
     */

    public function add() {
        if (!IS_POST) {
            $this->type();
            $this->com();
            $this->display();
        } else {
            $goods = D("Goods");
            $data['product'] = $_POST['product'];
            $data['letter'] = $_POST['letter'];
            // $data['type'] = $_POST['type'];
            // $data['com_id'] = $_POST['com_id'];
            $data['barcode'] = $_POST['barcode'];
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
            // $data['type'] = $_POST['type'];
            // $data['com_id'] = $_POST['com_id'];
            $data['barcode'] = $_POST['barcode'];
            $data['standard'] = $_POST['standard'];
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
            $str = $_GET['search'];
            $where['product'] = array('like', "%$str%");
            $where['letter'] = array('like', "%$str%");
            // $where['type'] = array('like', "%$str%");
            $where['barcode'] = array('like', "%$str%");
            $where['standard'] = array('like', "%$str%");
            $where['unit'] = array('like', "%$str%");
            $where['info'] = array('like', "%$str%");
            $where['_logic'] = 'or';

            $this->assign("replace", "<font color='red'>$str</font>");
            $this->assign("str", $str);

            parent::showPage("Goods", "10", $where);

            $this->display();
        }
    }

}
