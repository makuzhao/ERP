<?php

/*
 *  商品类型
 */

namespace Manager\Controller;

use Think\Controller;
use Common\Controller\AuthController;

class TypeController extends AuthController {
  
    /*
     * 显示商品类型
     */

    public function index() {
        $type = D("Type");
        $res = $type->select();
        if ($res == "") {
            $res = "没有任何数据";
            $this->assign("res", $res);
        } else {
            $this->assign("res", $res);

            parent::showPage("Type");
        }
        $total = $type->count("id");
        $this->assign("total", $total);

        $this->display();
    }

    /*
     * 添加商品类型
     */

    public function add() {
        if (!IS_POST) {
            $this->display();
        } else {
            $type = D("Type");
            $data['type'] = $_POST['type'];

            $res = $type->add($data);
            if ($res) {
                $this->success("添加商品类型成功 ！！", U('Type/index'));
            } else {
                $this->error("添加商品类型失败 ？？");
            }
        }
    }

    /*
     * 更新商品类型
     */

    public function update() {
        $type = D("Type");

        if (!IS_POST) {
            $tyid = $_GET['tyid'];
            if (empty($tyid)) {
                $this->error("非法操作！", U('Type/index'));
            } else {
                $where['id'] = $tyid;

                $res = $type->where($where)->select();
                // var_dump($res);
                $this->assign("res", $res);


                $this->display();
            }
        } else {
            $where['id'] = $_POST['tyid'];
            $data['type'] = $_POST['type'];




            $res = $type->where($where)->save($data);
            if (!$res) {
                $this->error("数据更新失败 ？？");
            } else {
                $this->success("数据更新成功 ！！", U('Type/index'));
            }
        }
    }

    /*
     * 删除商品类型 
     */

    public function delete() {
        $tyid = $_GET['tyid'];
        if (empty($tyid)) {
            $this->error("非法操作！", U('Type/index'));
        } else {
            $type = D("Type");
            $where['id'] = $tyid;
            $res = $type->where($where)->delete();
            if ($res) {
                $this->success("删除成功", U('Type/index'));
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

            $where['type'] = array('like', "%$str%");
            $this->assign("str", $str);

            parent::showPage("Type", "10", $where);

            $total = D('Type')->where($where)->count("id");
            $this->assign("total", $total);

            $this->display();
        }
    }

}
