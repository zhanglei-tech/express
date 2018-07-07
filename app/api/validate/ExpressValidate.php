<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\api\validate;

use think\Validate;

class ExpressValidate extends Validate
{
    protected $rule = [
        'user_id' => 'require',
        'delivery_id' => 'require',
        'serial' => 'require',
    ];
    protected $message = [
        'user_id.require' => '用户不能为空',
        'delivery_id.require' => '请选择快递点',
        'serial.require' => '请填写快递编号',
    ];

    protected $scene = [
        'add'  => ['user_id', 'delivery_id', 'serial'],
        'edit' => ['user_id', 'delivery_id', 'serial'],
    ];
}