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
namespace app\admin\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'user_login' => 'require|unique:user,user_login',
        'user_pass' => 'require',
        'user_email' => 'require|email|unique:user,user_email',
        'mobile' => 'require|unique:user,mobile',
        'user_nickname' => 'require',
    ];
    protected $message = [
        'user_login.require' => '用户不能为空',
        'user_login.unique' => '用户名已存在',
        'user_pass.require' => '密码不能为空',
        'user_email.require' => '邮箱不能为空',
        'user_email.email' => '邮箱不正确',
        'user_email.unique' => '邮箱已经存在',
        'mobile.require' => '手机不能为空',
        'mobile.unique' => '手机已经存在',
        'user_nickname.require' => '昵称不能为空',
    ];

    protected $scene = [
        'add' => ['user_login', 'user_pass', 'mobile', 'user_nickname'],
        'edit' => ['user_login', 'mobile', 'user_nickname'],
    ];
}