<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 2018/7/7
 * Time: 11:12
 */

namespace app\api\model;

use think\Model;

class DebugModel extends Model
{
    public function log($info)
    {
        $this->insert(array('info' => $info));
    }
}