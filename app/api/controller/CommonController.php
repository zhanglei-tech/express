<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 2018/5/5
 * Time: 08:54
 */

namespace app\api\controller;


use cmf\controller\HomeBaseController;
use think\Db;

class CommonController extends HomeBaseController
{
    public function getDate()
    {
        $this->result(date('Y-m-d'));
    }
}