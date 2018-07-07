<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 2018/4/8
 * Time: 01:31
 */

namespace app\mobile\controller;

use think\Validate;
use app\user\model\UserModel;

class LoginController extends BaseController
{
    public function login()
    {
        $validate = new Validate([
            'username' => 'require',
            'password' => 'require',
        ]);
        $validate->message([
            'username.require' => '用户名不能为空',
            'password.require' => '密码不能为空',
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $userModel = new UserModel();
        $user['user_pass'] = $data['password'];
        if (Validate::is($data['username'], 'email')) {
            $user['user_email'] = $data['username'];
            $log = $userModel->doEmail($user);
        } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
            $user['mobile'] = $data['username'];
            $log = $userModel->doMobile($user);
        } else {
            $user['user_login'] = $data['username'];
            $log = $userModel->doName($user);
        }
        $session_login_http_referer = session('login_http_referer');
        $redirect = empty($session_login_http_referer) ? $this->request->root() : $session_login_http_referer;
        switch ($log) {
            case 0:
                cmf_user_action('login');
                $this->success('登录成功', $redirect);
                break;
            case 1:
                $this->error('密码错误');
                break;
            case 2:
                $this->error('账户不存在');
                break;
            case 3:
                $this->error('账号被禁止访问系统');
                break;
            default :
                $this->error('未受理的请求');
        }
    }
}