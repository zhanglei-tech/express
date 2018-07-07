<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 2018/4/27
 * Time: 15:04
 */

namespace app\api\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class UserController extends HomeBaseController
{
    public function openid($code)
    {
        $params = array(
            'appid' => config('wx_appid'),
            'secret' => config('wx_secret'),
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        );
        $url = "https://api.weixin.qq.com/sns/jscode2session?" . http_build_query($params);
        $data = cmf_curl_get($url);

        $this->result(json_decode($data));
    }

    public function query($code)
    {
        $deliverer = Db::name('deliverer')->where('openid', $code)->find();
        if (!empty($deliverer)) {
            $this->result($deliverer, 1, '已绑定用户');
        } else {
            $this->result('未绑定用户');
        }
    }

    public function bind($code, $mobile, $nickname)
    {
        $deliverer = Db::name('deliverer')->where('mobile', $mobile)->find();
        if ($deliverer) {
            if (!empty($deliverer['openid'])) {
                $this->result('该手机号已绑定');
            }
            $deliverer['openid'] = $code;
            $deliverer['mobile'] = $mobile;
            $deliverer['nickname'] = $nickname;
            $result = Db::name('deliverer')->update($deliverer);
            if ($result !== false) {
                $this->result($deliverer, 1, '绑定成功');
            } else {
                $this->result('绑定失败');
            }
        } else {
            $deliverer['openid'] = $code;
            $deliverer['mobile'] = $mobile;
            $deliverer['nickname'] = $nickname;
            if (Db::name('deliverer')->insert($deliverer)) {
                $deliverer['id'] = Db::name('deliverer')->getLastInsID();
                $this->result($deliverer, 1, '绑定成功');
            } else {
                $this->result('绑定成功');
            }
        }
    }

    public function modify($id, $mobile, $nickname)
    {
        $deliverer = Db::name('deliverer')->find($id);
        if ($deliverer) {
            $delivererByMobile = Db::name('deliverer')->where('mobile', $mobile)->find();
            if ($delivererByMobile && $delivererByMobile['id'] != $deliverer['id']) {
                $this->result('该手机号已绑定');
            } else {
                $deliverer['mobile'] = $mobile;
                $deliverer['nickname'] = $nickname;
                $result = Db::name('deliverer')->update($deliverer);
                if ($result !== false) {
                    $this->result($deliverer, 1, '修改成功');
                } else {
                    $this->result('修改失败');
                }
            }
        } else {
            $this->result('用户不存在');
        }
    }
}