<?php

namespace addons\webscan\controller;

use think\addons\Controller;

class Index extends Controller
{

    public function index()
    {
       return $this->view->fetch('',['from'=>$this->request->param('from'),'captcha'=>$this->request->param('captcha','','')]);
    }

}
