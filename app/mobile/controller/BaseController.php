<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 2018/4/12
 * Time: 20:35
 */

namespace app\mobile\controller;

use cmf\controller\HomeBaseController;

// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:*');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

class BaseController extends HomeBaseController
{

}