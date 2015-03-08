<?php

namespace System\Model;

use Think\Model\ViewModel;

class UserViewModel extends ViewModel {

    protected $viewFields = array(
        'User' => array('*', '_type' => 'LEFT'),
        'Probation' => array('id' => 'pro_id', 'isfree', 'istoll', '_on' => 'User.id=Probation.user_id'),
    );

}
