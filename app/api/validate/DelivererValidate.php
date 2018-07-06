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

class DelivererValidate extends Validate
{
    protected $rule = [
        'nickname' => 'require',
        'mobile' => 'require',
        'openid' => 'require',
    ];
    protected $message = [
        'nickname.require' => '请填写昵称',
        'mobile.require' => '请填写手机号',
        'openid.require' => '微信openid不能为空',
    ];

    protected $scene = [
        'add'  => ['nickname', 'mobile', 'openid'],
        'edit' => ['nickname', 'mobile', 'openid'],
    ];
}