<?php

namespace app\api\controller\equipment;

use app\common\controller\Api;

class Base extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }
}