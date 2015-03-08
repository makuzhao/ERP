<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sale\Controller;

use Think\Controller;

class AlertController extends Controller {

//put your code here

    public function error($message, $jumpUrl = '', $ajax = false) {
        parent::error($message, $jumpUrl, $ajax);
    }

    public function success($message, $jumpUrl = '', $ajax = false) {
        parent::success($message, $jumpUrl, $ajax);
    }

    public function aler() {
        echo C(ERROR_PAGE);
        $this->display();
    }

}
